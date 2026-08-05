<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Expense extends CI_Model {

  

    public function add_expense_cateogry($data_gst) {
        return $this->db->insert('expense_category', $data_gst);
    }

    public function get_expense_catgory($uid) {
        $this->db->select('*');
        $this->db->from('expense_category');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function exp_cat_check($exp_cat, $uid) {
        $this->db->select('*');
        $this->db->from('expense_category');
        $this->db->where('exp_cat', $exp_cat);
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_exp_cat_by_id($id) {
        $this->db->where('exp_cat_id', $id);
        $this->db->delete('expense_category');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_exp_cat_by_id($id) {
        $this->db->select('*');
        $this->db->from('expense_category');
        $this->db->where('exp_cat_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function exp_cat_check_except_id($exp_cat, $exclude_id) {
        $this->db->select('exp_cat_id');
        $this->db->from('expense_category');
        $this->db->where('exp_cat', $exp_cat);
        $this->db->where('exp_cat_id !=', $exclude_id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function edit_exp_cat_by_id($id, $data) {
        $this->db->where('exp_cat_id', $id);
        $this->db->update('expense_category', $data);
        return TRUE;
    }

    // --- Indirect Individual Master ---

    public function add_indirect_individual($data) {
       
        return $this->db->insert('indirect_individual', $data);
    }

    public function get_indirect_individuals($uid) {
       
        $this->db->select('*');
        $this->db->from('indirect_individual');
       // $this->db->where('uid', $uid);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();



        return $query->result();
    }

    public function get_indirect_individual_by_id($id) {
       
        $this->db->select('*');
        $this->db->from('indirect_individual');
        // $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit_indirect_individual($id, $data) {
       
        // $this->db->where('id', $id);
        $this->db->update('indirect_individual', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete_indirect_individual($id) {
       
        // $this->db->where('id', $id);
        $this->db->delete('indirect_individual');
        return $this->db->affected_rows() == 1;
    }

    public function indirect_individual_code_exists($code, $uid, $exclude_id = 0) {
       
        $this->db->select('id');
        $this->db->from('indirect_individual');
        $this->db->where('code', $code);
        $this->db->where('uid', $uid);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    // ---- Direct Individual Master ----

    public function add_direct_individual($data) {
       
        return $this->db->insert('direct_individual', $data);
    }

    public function get_direct_individuals($uid) {
       
        $this->db->select('*');
        $this->db->from('direct_individual');
        $this->db->where('uid', $uid);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_direct_individual_by_id($id) {
       
        $this->db->select('*');
        $this->db->from('direct_individual');
        // $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit_direct_individual($id, $data) {
       
        // $this->db->where('id', $id);
        $this->db->update('direct_individual', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete_direct_individual($id) {
       
        // $this->db->where('id', $id);
        $this->db->delete('direct_individual');
        return $this->db->affected_rows() == 1;
    }

    public function direct_individual_code_exists($code, $uid, $exclude_id = 0) {
       
        $this->db->select('id');
        $this->db->from('direct_individual');
        $this->db->where('code', $code);
        $this->db->where('uid', $uid);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
}
?>