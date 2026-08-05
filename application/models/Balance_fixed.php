<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balance extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_opening_balances($user_id) {
        if (empty($user_id)) return array();
        
        if (!$this->db->table_exists('opening_balance')) {
            return array();
        }
        
        $this->db->select('balance_id, account_name, opening_balance_amount, balance_date, description, created_at');
        $this->db->from('opening_balance');
        $this->db->where('uid', $user_id);
        $this->db->order_by('balance_date DESC, created_at DESC');
        $query = $this->db->get();
        
        $balances = $query->result();
        foreach ($balances as $balance) {
            $balance->company_name = $balance->account_name;
        }
        return $balances;
    }

    public function get_all_customers($user_id) {
        if (empty($user_id)) return array();
        
        $tables = ['customer', $this->db->dbprefix . 'customer'];
        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                $this->db->select('customer_id, company_name, c_code');
                $this->db->from($table);
                $this->db->where('uid', $user_id);
                $this->db->order_by('company_name');
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    return $query->result();
                }
            }
        }
        // Fallback
        return array();
    }

    public function check_existing_opening_balance($account_name, $user_id) {
        if (empty($user_id) || empty($account_name)) return false;
        
        if (!$this->db->table_exists('opening_balance')) {
            return false;
        }
        
        $this->db->where('account_name', $account_name);
        $this->db->where('uid', $user_id);
        $query = $this->db->get('opening_balance');
        return $query->num_rows() > 0;
    }

    public function create_opening_balance($data) {
        if (!$this->db->table_exists('opening_balance')) {
            return false;
        }
        return $this->db->insert('opening_balance', $data);
    }

    public function get_opening_balance_by_id($id, $user_id) {
        if (empty($user_id) || empty($id)) return null;
        
        if (!$this->db->table_exists('opening_balance')) {
            return null;
        }
        
        $this->db->where('balance_id', $id);
        $this->db->where('uid', $user_id);
        $query = $this->db->get('opening_balance');
        $balance = $query->row();
        if ($balance) {
            $balance->company_name = $balance->account_name;
        }
        return $balance;
    }

    public function update_opening_balance($id, $user_id, $data) {
        if (empty($user_id) || empty($id)) return false;
        
        if (!$this->db->table_exists('opening_balance')) {
            return false;
        }
        
        $this->db->where('balance_id', $id);
        $this->db->where('uid', $user_id);
        return $this->db->update('opening_balance', $data);
    }

    public function delete_opening_balance($id, $user_id) {
        if (empty($user_id) || empty($id)) return false;
        
        if (!$this->db->table_exists('opening_balance')) {
            return false;
        }
        
        $this->db->where('balance_id', $id);
        $this->db->where('uid', $user_id);
        return $this->db->delete('opening_balance');
    }

    public function get_opening_balance_by_account($account_name, $user_id) {
        if (empty($user_id) || empty($account_name)) return null;
        
        if (!$this->db->table_exists('opening_balance')) {
            return null;
        }
        
        $this->db->where('account_name', $account_name);
        $this->db->where('uid', $user_id);
        $query = $this->db->get('opening_balance');
        return $query->row();
    }

    // Get customer by ID for mapping to account_name
    public function get_customer_company_name($customer_id) {
        $tables = ['customer', $this->db->dbprefix . 'customer'];
        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                $this->db->select('company_name');
                $this->db->from($table);
                $this->db->where('customer_id', $customer_id);
                $query = $this->db->get();
                $result = $query->row();
                if ($result) return $result->company_name;
            }
        }
        return null;
    }
}
?>

