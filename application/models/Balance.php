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
        if ($query === FALSE) {
            log_message('error', 'Opening balance query failed: ' . $this->db->last_query());
            return array();
        }
        
        $balances = $query->result();
        foreach ($balances as $balance) {
            $balance->company_name = $balance->account_name . ' (Account)';
        }
        return $balances;
    }

    public function get_all_customers($user_id) {
        $this->load->model('Customer');
        $customers = $this->Customer->get_customer($user_id);
        foreach ($customers as $customer) {
            $customer->company_name = $customer->fullname ?: $customer->company_name ?: 'Demo Customer';
        }
        return $customers;
    }

    public function get_all_accounts($user_id) {
        $accounts = array();

        $this->load->model('Customer');
        $customers = $this->Customer->get_customer($user_id);
        foreach ($customers as $customer) {
            $company_name = !empty($customer->company_name) ? $customer->company_name : (!empty($customer->fullname) ? $customer->fullname : 'Customer Account');
            $accounts[] = (object) array(
                'account_id' => 'customer:' . $customer->customer_id,
                'account_type' => 'Customer',
                'company_name' => $company_name,
                'account_code' => !empty($customer->c_code) ? $customer->c_code : ''
            );
        }

        $this->load->model('Supplier');
        $suppliers = $this->Supplier->get_supplier_name();
        foreach ($suppliers as $supplier) {
            $company_name = !empty($supplier->company_name) ? $supplier->company_name : (!empty($supplier->fullname) ? $supplier->fullname : 'Vendor Account');
            $accounts[] = (object) array(
                'account_id' => 'supplier:' . $supplier->supplier_id,
                'account_type' => 'Vendor',
                'company_name' => $company_name,
                'account_code' => !empty($supplier->s_code) ? $supplier->s_code : ''
            );
        }

        usort($accounts, function($a, $b) {
            return strcmp($a->company_name, $b->company_name);
        });

        return $accounts;
    }

    public function get_customer_company_name($customer_id) {
        $this->load->model('Customer');
        $customer = $this->Customer->get_customer_by_id($customer_id);
        return $customer ? ($customer['company_name'] ?: $customer['fullname'] ?: 'Customer Account') : null;
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
        if (!isset($data['account_name']) || empty($data['account_name'])) return false;
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
            $balance->company_name = $balance->account_name . ' (Account)';
        }
        return $balance;
    }

    public function update_opening_balance($id, $user_id, $data) {
        if (empty($user_id) || empty($id)) return false;
        if (!isset($data['account_name']) || empty($data['account_name'])) return false;
        
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
    
}
?>

