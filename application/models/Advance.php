<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Advance extends CI_Model {

    public function add_advance($data_advance) {
        return $this->db->insert('advance_amount', $data_advance);
    }
  
    public function advance_amount_check($customer_id) {
        $this->db->select('customer_id_fk');
        $this->db->from('advance_amount');
        $this->db->where('customer_id_fk', $customer_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function add_user($data) {
        return $this->db->insert('user', $data);
    }

    public function get_advance_by_id($id) {
        $this->db->select('*');
        $this->db->from('advance_amount');
        $this->db->where('advance_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete_advance_by_id($id) {
        $this->db->where('advance_id', $id);
        $this->db->delete('advance_amount');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit_advance($data_advance, $advance_id, $uid) {
        $this->db->where('advance_id', $advance_id);
        //$this->db->where('uid', $uid);
        $this->db->update('advance_amount', $data_advance);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_advance_amount($uid) {
        $this->db->select('*');
        $this->db->from('advance_amount');
        $this->db->where('advance_amount.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=advance_amount.customer_id_fk');
        $this->db->order_by("advance_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_customer() {
        $this->db->select('*');
        $this->db->from('customer');
        $query = $this->db->get();
        return $query->result();
    }

}
