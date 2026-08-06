<?php

class OrderConfirmation extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->_auto_migrate_oa_columns();
    }

    private function _auto_migrate_oa_columns() {
        $prefix = $this->db->dbprefix;

        // 1. Create orderconfirmation_total table if missing
        $sql_total = "CREATE TABLE IF NOT EXISTS `{$prefix}orderconfirmation_total` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `number_fk` varchar(100) DEFAULT NULL,
          `supplier_id` int(11) DEFAULT NULL,
          `customer_id` int(11) DEFAULT NULL,
          `po_reference` varchar(100) DEFAULT NULL,
          `po_date` varchar(100) DEFAULT NULL,
          `subject` text DEFAULT NULL,
          `date` date DEFAULT NULL,
          `delivery_date` date DEFAULT NULL,
          `payment_terms` text DEFAULT NULL,
          `price_basis` text DEFAULT NULL,
          `transportation_charges` text DEFAULT NULL,
          `service_charges` text DEFAULT NULL,
          `warranty` text DEFAULT NULL,
          `salesorder_id` varchar(100) DEFAULT NULL,
          `project_code` varchar(100) DEFAULT NULL,
          `remarks` longtext,
          `sub_total` decimal(15,2) DEFAULT 0.00,
          `tax_amount` decimal(15,2) DEFAULT 0.00,
          `total` decimal(15,2) DEFAULT 0.00,
          `status` int(11) DEFAULT 1,
          `uid` int(11) NOT NULL,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `number_fk` (`number_fk`),
          KEY `supplier_id` (`supplier_id`),
          KEY `po_reference` (`po_reference`),
          KEY `uid` (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($sql_total);

        // 2. Create orderconfirmation table if missing
        $sql_detail = "CREATE TABLE IF NOT EXISTS `{$prefix}orderconfirmation` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `number` varchar(100) DEFAULT NULL,
          `description` longtext,
          `hsn_code` varchar(50) DEFAULT NULL,
          `quantity` decimal(10,2) DEFAULT 0.00,
          `unit` varchar(20) DEFAULT NULL,
          `unit_price` decimal(15,2) DEFAULT 0.00,
          `tax_rate` decimal(5,2) DEFAULT 0.00,
          `tax_amount` decimal(15,2) DEFAULT 0.00,
          `amount` decimal(15,2) DEFAULT 0.00,
          `uid` int(11) NOT NULL,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `number` (`number`),
          KEY `uid` (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($sql_detail);

        // 3. Ensure any missing columns are added if table existed previously with fewer columns
        if ($this->db->table_exists('orderconfirmation_total')) {
            $fields = $this->db->list_fields('orderconfirmation_total');
            $columns_to_add = [
                'customer_id'            => 'INT NULL',
                'po_date'                => 'VARCHAR(100) NULL',
                'subject'                => 'TEXT NULL',
                'price_basis'            => 'TEXT NULL',
                'transportation_charges' => 'TEXT NULL',
                'service_charges'        => 'TEXT NULL',
                'warranty'               => 'TEXT NULL',
                'salesorder_id'          => 'VARCHAR(100) NULL'
            ];
            foreach ($columns_to_add as $col => $type) {
                if (!in_array($col, $fields)) {
                    $this->db->query("ALTER TABLE `{$prefix}orderconfirmation_total` ADD COLUMN `{$col}` {$type}");
                }
            }
        }
    }

    public function get_last_oc_number($uid) {
        $financial_year = '';
        if (date('m') <= 3) {
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        $this->db->select('count(number_fk) as id');
        $this->db->from('orderconfirmation_total');
        $this->db->like('number_fk', $financial_year, "before");
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $result = $query->row();

        return $result ? $result->id : 0;
    }

    public function get_supplier($uid) {
        $this->db->select('*');
        $this->db->from('supplier');
        $this->db->where('uid', $uid);
        $this->db->order_by("company_name", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    public function supplier_check($company_name, $uid) {
        $this->db->select('company_name');
        $this->db->from('supplier');
        $this->db->where('company_name', $company_name);
        $this->db->where('uid', $uid);
        $this->db->limit(1);
        $query = $this->db->get();
        return ($query->num_rows() == 1);
    }

    public function get_supplier_by_id($supplier_id, $uid) {
        $this->db->select('*');
        $this->db->from('supplier');
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_customers($uid) {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->where('uid', $uid);
        $this->db->order_by('company_name', 'asc');
        return $this->db->get()->result();
    }

    public function get_orderconfirmation_by_number($number, $uid) {
        $this->db->select('oct.*, s.company_name as supplier_company_name, c.company_name as customer_company_name, c.client_name, c.address as customer_address, c.gstin as customer_gstin, c.mobile_number as customer_mobile');
        $this->db->from('orderconfirmation_total as oct');
        $this->db->join('supplier as s', 'oct.supplier_id = s.supplier_id', 'left');
        $this->db->join('customer as c', 'oct.customer_id = c.customer_id', 'left');
        $this->db->where('oct.number_fk', $number);
        $this->db->where('oct.uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_orderconfirmation_detail($number, $uid) {
        $this->db->select('*');
        $this->db->from('orderconfirmation');
        $this->db->where('number', $number);
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_orderconfirmations($uid) {
        $this->db->select('*');
        $this->db->from('orderconfirmation_total');
        $this->db->where('uid', $uid);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_monthyearwise_record($month_year, $uid) {
        $this->db->select('*');
        $this->db->from('orderconfirmation_total');
        $this->db->like('number_fk', $month_year);
        $this->db->where('uid', $uid);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_orderconfirmation_count($uid) {
        $this->db->select('count(id) as total');
        $this->db->from('orderconfirmation_total');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        $result = $query->row();
        return $result->total;
    }

    public function get_orderconfirmation_status_count($status, $uid) {
        $this->db->select('count(id) as total');
        $this->db->from('orderconfirmation_total');
        $this->db->where('status', $status);
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        $result = $query->row();
        return $result->total;
    }

    public function get_project_code($uid) {
        // Check if project table exists
        $tables = $this->db->list_tables();
        if (!in_array('project', $tables)) {
            return array(); // Return empty array if table doesn't exist
        }
        
        $this->db->select('*');
        $this->db->from($this->db->dbprefix . 'project');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function add_orderconfirmation($data) {
        return $this->db->insert('orderconfirmation_total', $data);
    }

    public function add_orderconfirmation_detail($data) {
        return $this->db->insert('orderconfirmation', $data);
    }

    public function delete_orderconfirmation_detail($number, $uid) {
        $this->db->where('number', $number);
        $this->db->where('uid', $uid);
        return $this->db->delete('orderconfirmation');
    }

    public function delete_orderconfirmation_by_number($number, $uid) {
        // First delete the details
        $this->db->where('number', $number);
        $this->db->where('uid', $uid);
        $this->db->delete('orderconfirmation');
        
        // Then delete the header
        $this->db->where('number_fk', $number);
        $this->db->where('uid', $uid);
        return $this->db->delete('orderconfirmation_total');
    }

    public function update_orderconfirmation($number, $data, $uid) {
        $this->db->where('number_fk', $number);
        $this->db->where('uid', $uid);
        return $this->db->update('orderconfirmation_total', $data);
    }

    public function update_status($number, $status, $uid) {
        $data = array('status' => $status);
        $this->db->where('number_fk', $number);
        $this->db->where('uid', $uid);
        return $this->db->update('orderconfirmation_total', $data);
    }

    public function get_settings($uid) {
        $this->db->select('*');
        $this->db->from('settings');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_paymentterm($uid) {
        $this->db->select('*');
        $this->db->from('paymentterm');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_items($uid) {
        $this->db->select('*');
        $this->db->from('inventory');
        $this->db->where('uid', $uid);
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
}
?>

