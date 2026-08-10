<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Engineering_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get active sales orders for selection
     */
    public function get_sales_orders() {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('so.date >=', $fy_from);
            $this->db->where('so.date <=', $fy_to);
        }
        $this->db->select('so.id, so.number_fk as so_number, so.project_code, c.company_name');
        $this->db->from('salesorder_total so');
        $this->db->join('customer c', 'c.customer_id = so.customer_id_fk', 'left');
        $this->db->order_by('so.id', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get single sales order by ID
     */
    public function get_sales_order_by_id($so_id) {
        $this->db->select('so.id, so.number_fk as so_number, so.project_code, c.company_name');
        $this->db->from('salesorder_total so');
        $this->db->join('customer c', 'c.customer_id = so.customer_id_fk', 'left');
        $this->db->where('so.id', $so_id);
        return $this->db->get()->row();
    }

    /**
     * Get BOM items associated with a sales order
     */
    public function get_bom_items_by_so($so_number) {
        if (empty($so_number)) return array();
        
        $this->db->select('bt.id as bom_item_id, bt.number_fk as bom_code, bt.project_code');
        $this->db->from('bom_total bt');
        $this->db->where('bt.project_code', $so_number);
        $this->db->or_where('bt.number_fk', $so_number);
        $this->db->order_by('bt.id', 'ASC');
        return $this->db->get()->result();
    }

    // ================= DATASHEETS METHODS =================

    public function get_all_datasheets() {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('d.created_at >=', $fy_from);
            $this->db->where('d.created_at <=', $fy_to);
        }
        $this->db->select('d.*, bt.number_fk as bom_code');
        $this->db->from('engineering_datasheets d');
        $this->db->join('bom_total bt', 'bt.id = d.bom_item_id_fk', 'left');
        $this->db->order_by('d.id', 'DESC');
        return $this->db->get()->result();
    }

    public function insert_datasheet($data) {
        return $this->db->insert('engineering_datasheets', $data);
    }

    public function get_datasheet_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('engineering_datasheets')->row();
    }

    public function delete_datasheet($id) {
        $row = $this->get_datasheet_by_id($id);
        if ($row && !empty($row->file_path) && file_exists($row->file_path)) {
            @unlink($row->file_path);
        }
        $this->db->where('id', $id);
        return $this->db->delete('engineering_datasheets');
    }

    // ================= BUDGET SHEETS METHODS =================

    public function get_all_budgets() {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('created_at >=', $fy_from);
            $this->db->where('created_at <=', $fy_to);
        }
        $this->db->select('*');
        $this->db->from('engineering_budgets');
        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result();
    }

    public function insert_budget($data) {
        return $this->db->insert('engineering_budgets', $data);
    }

    public function get_budget_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('engineering_budgets')->row();
    }

    public function delete_budget($id) {
        $row = $this->get_budget_by_id($id);
        if ($row && !empty($row->file_path) && file_exists($row->file_path)) {
            @unlink($row->file_path);
        }
        $this->db->where('id', $id);
        return $this->db->delete('engineering_budgets');
    }
}
