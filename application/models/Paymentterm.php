<?php
Class Paymentterm extends CI_Model {

     public function add_paymentterm($data_paymentterm) {
        return $this->db->insert('payment_terms', $data_paymentterm);
    }

    public function get_paymentterm() {
        $this->db->select('*');
        $this->db->from('payment_terms');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function paymentterm_check($payment_term, $uid) {
        $this->db->select('*');
        $this->db->from('payment_terms');
        $this->db->where('payment_term', $payment_term);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_payment_term_by_id($id) {
        $this->db->where('payment_term_id ', $id);
        $this->db->delete('payment_terms');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
