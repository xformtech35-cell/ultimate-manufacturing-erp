<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Customer extends CI_Model {
    
    function __construct(){
            parent::__construct();
            //load our second db and put in $db2
           // $this->crm = $this->load->database('crm', TRUE);
        }

    public function add_customer($data_customer) {
        
        $dataOpp = array(
            'opp_name' => $data_customer['company_name'],
        );
        
        //$this->crm->insert('opportunity', $dataOpp);
        
        return $this->db->insert('customer', $data_customer);
    }

    public function get_customer($uid = NULL, $limit = 0) {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->order_by("customer_id", "desc");
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function customer_check($company_name, $uid) {
        $this->db->select('company_name');
        $this->db->from('customer');
        //$this->db->where('uid', $uid);
        $this->db->where('company_name', $company_name);
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

    public function get_customer_by_mobile($mobile) {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->join('user', 'customer.customer_mobile=user.user_id');
        $this->db->where('customer_mobile', $mobile);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_customer_by_id($id) {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->where('customer_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete_customer_by_id($id) {
        $this->db->where('customer_id', $id);
        $this->db->delete('customer');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_customer_count($uid) {
        return $this->db->count_all('customer');
    }

    public function edit_customer($data_customer, $customer_id, $uid) {
        //$this->db->where('uid', $uid);
        $this->db->where('customer_id', $customer_id);
        $this->db->update('customer', $data_customer);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
      public function get_last_customer_code($uid) {
        $this->db->select('COUNT(c_code)');
        $this->db->from('customer');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['COUNT(c_code)'];
    }

}
