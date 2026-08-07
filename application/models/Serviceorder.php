<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Serviceorder extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_customer($uid) {
        $this->db->select('*');
        $this->db->from('customer');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_company_name($uid) {
        $this->db->select('customer_id, company_name, state_code, c_code');
        $this->db->from('customer');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_service_orders($type, $uid) {
        $this->db->select('t.*, c.company_name, c.fullname');
        $this->db->from('service_order_total t');
        $this->db->join('customer c', 'c.customer_id = t.customer_id_fk', 'left');
        $this->db->where('t.service_type', $type);
        $this->db->order_by('t.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_service_order_count($type, $uid) {
        $this->db->where('service_type', $type);
        return $this->db->count_all_results('service_order_total');
    }

    public function get_service_order_status_count($type, $status, $uid) {
        $this->db->where('service_type', $type);
        $this->db->where('status', $status);
        return $this->db->count_all_results('service_order_total');
    }

    public function get_monthyearwise_record($type, $month_year, $uid) {
        // month_year is formatted like "Aug-2026"
        // date format in DB is YYYY-MM-DD
        $parts = explode('-', $month_year);
        if (count($parts) === 2) {
            $month = date('m', strtotime($parts[0]));
            $year = $parts[1];
            $this->db->like('date', "$year-$month", 'after');
        }
        $this->db->select('t.*, c.company_name, c.fullname');
        $this->db->from('service_order_total t');
        $this->db->join('customer c', 'c.customer_id = t.customer_id_fk', 'left');
        $this->db->where('t.service_type', $type);
        $this->db->order_by('t.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_service_order_data_by_status($type, $status, $uid) {
        $this->db->select('t.*, c.company_name, c.fullname');
        $this->db->from('service_order_total t');
        $this->db->join('customer c', 'c.customer_id = t.customer_id_fk', 'left');
        $this->db->where('t.service_type', $type);
        $this->db->where('t.status', $status);
        $this->db->order_by('t.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_last_service_order_number($type, $uid) {
        $financial_year = '';
        if (date('m') <= 3) {
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        $this->db->select('count(number_fk) as id');
        $this->db->from('service_order_total');
        $this->db->where('service_type', $type);
        $this->db->like('number_fk', $financial_year, 'before');
        $query = $this->db->get();
        $result = $query->row();
        return $result->id;
    }

    public function get_service_order_data_group_by($number, $uid) {
        $this->db->select('t.*, c.company_name, c.fullname, c.address as customer_address, c.customer_gst_no, c.customer_pancard_no');
        $this->db->from('service_order_total t');
        $this->db->join('customer c', 'c.customer_id = t.customer_id_fk', 'left');
        $this->db->where('t.number_fk', $number);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_service_order_data($number, $uid) {
        $this->db->select('s.*');
        $this->db->from('service_order s');
        $this->db->where('s.number', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function add_service_order_total($data) {
        return $this->db->insert('service_order_total', $data);
    }

    public function add_service_order($data) {
        return $this->db->insert('service_order', $data);
    }

    public function update_service_order_total($number, $data) {
        $this->db->where('number_fk', $number);
        return $this->db->update('service_order_total', $data);
    }

    public function delete_service_order($number) {
        $this->db->where('number_fk', $number);
        return $this->db->delete('service_order_total');
    }

    public function delete_service_order_items($number) {
        $this->db->where('number', $number);
        return $this->db->delete('service_order');
    }
}
