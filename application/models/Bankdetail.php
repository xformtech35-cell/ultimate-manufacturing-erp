<?php
Class Bankdetail extends CI_Model {

     public function add_bank_detail($data_bankdetail) {
        return $this->db->insert('bank_details', $data_bankdetail);
    }

    public function get_bankdetail($uid) {
        $this->db->select('*');
        $this->db->from('bank_details');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function bankdetail_check($bank_name, $uid) {
        $this->db->select('*');
        $this->db->from('bank_details');
        $this->db->where('bank_name', $bank_name);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_bank_detail_by_id($id) {
        $this->db->where('bank_id ', $id);
        $this->db->delete('bank_details');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
     public function get_bank_detail_id($id) {
        $this->db->select('*');
        $this->db->from('bank_details');
        $this->db->where('bank_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
   
    public function edit_bank_detail($data_bank, $bank_id, $uid) {
        $this->db->where('bank_id', $bank_id);
        //$this->db->where('uid', $uid);
        $this->db->update('bank_details', $data_bank);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
     public function get_account_type_catgory($uid) {
        $this->db->select('*');
        $this->db->from('bank_details');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }
    
     public function add_bank_transaction($data_banktransaction) {
        return $this->db->insert('bank_transaction', $data_banktransaction);
    }
    public function banktransaction_check($transaction_detail, $uid) {
        $this->db->select('*');
        $this->db->from('bank_transaction');
        $this->db->where('transaction_detail', $transaction_detail);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_banktransaction($uid) {


        $this->db->select('*');
        $this->db->from('bank_transaction');
        //$this->db->where('uid', $uid);
      //  $this->db->where('transaction_date >=', $po_date1);
       //  $this->db->where('transaction_date <=', $po_date2);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_bank_transaction_id($id) {
        $this->db->select('*');
        $this->db->from('bank_transaction');
        $this->db->where('bank_transaction_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    
    public function delete_bank_transaction_by_id($id){
         $this->db->where('bank_transaction_id ', $id);
        $this->db->delete('bank_transaction');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
     public function edit_bank_transaction($data_banktransaction, $bank_transaction_id, $uid) {
        $this->db->where('bank_transaction_id', $bank_transaction_id);
        //$this->db->where('uid', $uid);
        $this->db->update('bank_transaction', $data_banktransaction);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    
    
    
    
    public function add_loan($data_loan) {
        return $this->db->insert('loan_account', $data_loan);
    }
    public function loan_check($acc_name, $uid) {
        $this->db->select('*');
        $this->db->from('loan_account');
        $this->db->where('acc_name', $acc_name);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_loan() {
        $this->db->select('*');
        $this->db->from('loan_account');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_loan_id($id) {
        $this->db->select('*');
        $this->db->from('loan_account');
        $this->db->where('loan_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    
    public function delete_loan_by_id($id){
         $this->db->where('loan_id ', $id);
        $this->db->delete('loan_account');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
     public function edit_loan($data_loan, $loan_id, $uid) {
        $this->db->where('loan_id', $loan_id);
        //$this->db->where('uid', $uid);
        $this->db->update('loan_account', $data_loan);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
}
