<?php

class OrderConfirmation extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_last_oc_number($uid) {
        $financial_year = '';
        if (date('m') <= 3) {
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        $this->db->select('count(number_fk) as id');
        $this->db->from('orderconfirmation_total');
        $this->db->like('number_fk', $financial_year, "before");
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $result = $query->row();

        return $result->id;
    }

    public function get_supplier($uid) {
        $this->db->select('*');
        $this->db->from('supplier');
        $this->db->where('uid', $uid);
        $this->db->order_by("company_name", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    public function supplier_check($company_name, $uid) {
        $this->db->select('company_name');
        $this->db->from('supplier');
        $this->db->where('company_name', $company_name);
        $this->db->where('uid', $uid);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_supplier_by_id($supplier_id, $uid) {
        $this->db->select('*');
        $this->db->from('supplier');
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_orderconfirmation_by_number($number, $uid) {
        $this->db->select('oct.*, s.*');
        $this->db->from('orderconfirmation_total as oct');
        $this->db->join('supplier as s', 'oct.supplier_id = s.supplier_id', 'left');
        $this->db->where('oct.number_fk', $number);
        $this->db->where('oct.uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_orderconfirmation_detail($number, $uid) {
        $this->db->select('*');
        $this->db->from('orderconfirmation');
        $this->db->where('number', $number);
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_orderconfirmations($uid) {
        $this->db->select('*');
        $this->db->from('orderconfirmation_total');
        $this->db->where('uid', $uid);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_monthyearwise_record($month_year, $uid) {
        $this->db->select('*');
        $this->db->from('orderconfirmation_total');
        $this->db->like('number_fk', $month_year);
        $this->db->where('uid', $uid);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_orderconfirmation_count($uid) {
        $this->db->select('count(id) as total');
        $this->db->from('orderconfirmation_total');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        $result = $query->row();
        return $result->total;
    }

    public function get_orderconfirmation_status_count($status, $uid) {
        $this->db->select('count(id) as total');
        $this->db->from('orderconfirmation_total');
        $this->db->where('status', $status);
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        $result = $query->row();
        return $result->total;
    }

    public function get_project_code($uid) {
        // Check if project table exists
        $tables = $this->db->list_tables();
        if (!in_array('project', $tables)) {
            return array(); // Return empty array if table doesn't exist
        }
        
        $this->db->select('*');
        $this->db->from($this->db->dbprefix . 'project');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function add_orderconfirmation($data) {
        return $this->db->insert('orderconfirmation_total', $data);
    }

    public function add_orderconfirmation_detail($data) {
        return $this->db->insert('orderconfirmation', $data);
    }

    public function delete_orderconfirmation_detail($number, $uid) {
        $this->db->where('number', $number);
        $this->db->where('uid', $uid);
        return $this->db->delete('orderconfirmation');
    }

    public function delete_orderconfirmation_by_number($number, $uid) {
        // First delete the details
        $this->db->where('number', $number);
        $this->db->where('uid', $uid);
        $this->db->delete('orderconfirmation');
        
        // Then delete the header
        $this->db->where('number_fk', $number);
        $this->db->where('uid', $uid);
        return $this->db->delete('orderconfirmation_total');
    }

    public function update_orderconfirmation($number, $data, $uid) {
        $this->db->where('number_fk', $number);
        $this->db->where('uid', $uid);
        return $this->db->update('orderconfirmation_total', $data);
    }

    public function update_status($number, $status, $uid) {
        $data = array('status' => $status);
        $this->db->where('number_fk', $number);
        $this->db->where('uid', $uid);
        return $this->db->update('orderconfirmation_total', $data);
    }

    public function get_settings($uid) {
        $this->db->select('*');
        $this->db->from('settings');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_paymentterm($uid) {
        $this->db->select('*');
        $this->db->from('paymentterm');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_items($uid) {
        $this->db->select('*');
        $this->db->from('inventory');
        $this->db->where('uid', $uid);
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
}
?>

