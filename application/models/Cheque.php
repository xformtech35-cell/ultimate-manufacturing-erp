<?php
Class Cheque extends CI_Model {

     public function add_cheque_detail($data_chequedetail) {
        return $this->db->insert('cheque_details', $data_chequedetail);
    }

    public function get_cheque_detail() {
        $this->db->select('*');
        $this->db->from('cheque_details');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function chequedetail_check($cheque_no, $uid) {
        $this->db->select('*');
        $this->db->from('cheque_details');
        $this->db->where('cheque_no', $cheque_no);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_cheque_detail_by_id($id) {
        $this->db->where('cheque_id ', $id);
        $this->db->delete('cheque_details');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
     public function get_cheque_detail_id($id) {
        $this->db->select('*');
        $this->db->from('cheque_details');
        $this->db->where('cheque_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
   
    public function edit_cheque_detail($data_cheque, $cheque_id, $uid) {
        $this->db->where('cheque_id', $cheque_id);
        //$this->db->where('uid', $uid);
        $this->db->update('cheque_details', $data_cheque);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
     public function get_status_catgory($uid) {
        $this->db->select('*');
        $this->db->from('cheque_details');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }
  }
