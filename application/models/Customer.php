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
            'opp_name' => isset($data_customer['company_name']) ? $data_customer['company_name'] : '',
        );
        
        //$this->crm->insert('opportunity', $dataOpp);

        if (empty($data_customer['c_code'])) {
            $data_customer['c_code'] = $this->get_next_customer_code();
        }
        
        if (empty($data_customer['created_date'])) {
            $data_customer['created_date'] = date('Y-m-d H:i:s');
        }

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

    public function get_customer_filtered($filter = 'all', $fy_year = null, $uid = null, $limit = 0) {
        $this->db->select('*');
        $this->db->from('customer');

        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01 00:00:00';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $fy_from_date = $fy_year . '-04-01';
            $fy_to_date   = ($fy_year + 1) . '-03-31';

            if ($filter === 'registered') {
                $this->db->where('created_date >=', $fy_from);
                $this->db->where('created_date <=', $fy_to);
            } elseif ($filter === 'active') {
                $prefix = $this->db->dbprefix;
                $this->db->where("customer_id IN (
                    SELECT customer_id_fk FROM {$prefix}salesorder_total WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id_fk IS NOT NULL
                    UNION
                    SELECT customer_id FROM {$prefix}quotation WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id IS NOT NULL
                    UNION
                    SELECT customer_id_fk FROM {$prefix}invoice_total WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id_fk IS NOT NULL
                    UNION
                    SELECT customer_id_fk FROM {$prefix}joborder_total WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id_fk IS NOT NULL
                    UNION
                    SELECT customer_id_fk FROM {$prefix}delivery_challan_total WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id_fk IS NOT NULL
                )", NULL, FALSE);
            }
        }

        $this->db->order_by("customer_id", "desc");
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_customer_filter_counts($fy_year = null) {
        $counts = [
            'all'        => $this->db->count_all('customer'),
            'registered' => 0,
            'active'     => 0,
        ];

        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01 00:00:00';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $fy_from_date = $fy_year . '-04-01';
            $fy_to_date   = ($fy_year + 1) . '-03-31';

            // Registered count
            $this->db->from('customer');
            $this->db->where('created_date >=', $fy_from);
            $this->db->where('created_date <=', $fy_to);
            $counts['registered'] = $this->db->count_all_results();

            // Active count
            $prefix = $this->db->dbprefix;
            $active_query = $this->db->query("SELECT COUNT(DISTINCT cust_id) as cnt FROM (
                SELECT customer_id_fk as cust_id FROM {$prefix}salesorder_total WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id_fk IS NOT NULL
                UNION
                SELECT customer_id as cust_id FROM {$prefix}quotation WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id IS NOT NULL
                UNION
                SELECT customer_id_fk as cust_id FROM {$prefix}invoice_total WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id_fk IS NOT NULL
                UNION
                SELECT customer_id_fk as cust_id FROM {$prefix}joborder_total WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id_fk IS NOT NULL
                UNION
                SELECT customer_id_fk as cust_id FROM {$prefix}delivery_challan_total WHERE date >= '{$fy_from_date}' AND date <= '{$fy_to_date}' AND customer_id_fk IS NOT NULL
            ) t");
            $r = $active_query->row();
            $counts['active'] = (int)($r->cnt ?? 0);
        }

        return $counts;
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
    
    
    public function get_last_customer_code($uid = null) {
        $this->db->select('MAX(CAST(c_code AS UNSIGNED)) as max_code');
        $this->db->from('customer');
        $this->db->where('c_code !=', '');
        $this->db->where('c_code IS NOT NULL', null, false);
        $query = $this->db->get();
        $result = $query->row_array();
        $max_code = isset($result['max_code']) ? $result['max_code'] : null;
        if (empty($max_code) || $max_code == null) {
            return 0;
        }
        return intval($max_code);
    }

    public function get_next_customer_code() {
        $last_code = $this->get_last_customer_code();
        if ($last_code == 0) {
            return 3001;
        }
        return $last_code + 1;
    }

}
