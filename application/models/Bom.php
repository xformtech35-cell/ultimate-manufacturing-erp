<?php

Class Bom extends CI_Model {

    public function add_customer($data_customer) {
        return $this->db->insert('customer', $data_customer);
    }

    public function get_last_bom_number($uid) {
        
        $financial_year = '';
        if (date('m') <= 3) {//Upto March
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {//After March
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        // Get all BOM numbers for this financial year (base numbers only, no -Rx revisions)
        // Extract the numeric part and find the MAX to avoid duplicates with revisions
        $query = $this->db->query(
            "SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(number_fk, '/', 2), '/', -1) AS UNSIGNED)) as max_seq
             FROM {$this->db->dbprefix}bom_total
             WHERE number_fk LIKE ? AND number_fk NOT LIKE '%-R%'",
            ["%/$financial_year"]
        );
        $result = $query->row();

        return $result->max_seq ? intval($result->max_seq) : 0;
    }

    public function get_customer($uid) {
        $this->db->select('*');
        $this->db->from('customer');
        $query = $this->db->get();
        return $query->result();
    }

    public function customer_check($company_name, $uid) {
        $this->db->select('company_name');
        $this->db->from('customer');
        $this->db->where('company_name', $company_name);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_company_name($uid) {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->order_by("company_name", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_customer_by_id($id) {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->where('customer_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_customer_email($number, $uid) {
        $this->db->select('customer.customer_id, customer.email');
        $this->db->from('bom_total');
        $this->db->join('customer', 'customer.customer_id = bom_total.customer_id_fk', 'left');
        $this->db->where('bom_total.number_fk', $number);
        // Removed uid filter — any user can look up customer email for any BOM
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

    public function edit_customer($data_customer, $customer_id) {
        $this->db->where('customer_id', $customer_id);
        $this->db->update('customer', $data_customer);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function get_combined_boms_query($status = null)
    {
        $prefix = $this->db->dbprefix;
        
        $fy_where_ba = "";
        $fy_where_bt = "";
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $fy_where_ba = " AND ba.action_date >= {$this->db->escape($fy_from)} AND ba.action_date <= {$this->db->escape($fy_to)}";
            $fy_where_bt = " AND bt.date >= {$this->db->escape($fy_from)} AND bt.date <= {$this->db->escape($fy_to)}";
        }

        $status_where = "";
        if ($status !== null && $status !== '') {
            $status_val = (int) $status;
            $status_where = " WHERE sub.status = {$status_val}";
        }

        return "SELECT * FROM (
                    SELECT 
                        bt.id                      AS bom_total_id,
                        bt.number_fk               AS number,
                        ba.action_date             AS date,
                        CASE 
                            WHEN ba.status = 'approved' THEN 4
                            WHEN ba.status = 'rejected' THEN 5
                            ELSE bt.status
                        END                        AS status,
                        ba.remarks                 AS note,
                        bt.project_code,
                        bt.customer_code,
                        bt.system,
                        bt.location,
                        bt.capacity,
                        bt.project_qty,
                        bt.oc_number,
                        bt.send_to_mrp,
                        c.company_name,
                        c.fullname,
                        u.username                 AS prepare_by,
                        ba.action_by               AS approved_by_name,
                        ba.action_date             AS sort_date
                    FROM {$prefix}bom_approvals ba
                    JOIN {$prefix}bom_total bt ON bt.number_fk = ba.bom_number
                    LEFT JOIN {$prefix}customer c ON c.customer_id = bt.customer_id_fk
                    LEFT JOIN {$prefix}user u ON u.user_id = bt.uid
                    WHERE ba.status IN ('approved', 'rejected') {$fy_where_ba}

                    UNION ALL

                    SELECT 
                        bt.id                      AS bom_total_id,
                        bt.number_fk               AS number,
                        bt.date                    AS date,
                        bt.status                  AS status,
                        bt.note                    AS note,
                        bt.project_code,
                        bt.customer_code,
                        bt.system,
                        bt.location,
                        bt.capacity,
                        bt.project_qty,
                        bt.oc_number,
                        bt.send_to_mrp,
                        c.company_name,
                        c.fullname,
                        u.username                 AS prepare_by,
                        u2.username                AS approved_by_name,
                        bt.date                    AS sort_date
                    FROM {$prefix}bom_total bt
                    LEFT JOIN {$prefix}customer c ON c.customer_id = bt.customer_id_fk
                    LEFT JOIN {$prefix}user u ON u.user_id = bt.uid
                    LEFT JOIN {$prefix}user u2 ON u2.user_id = bt.approved_by
                    WHERE bt.number_fk NOT IN (
                        SELECT bom_number FROM {$prefix}bom_approvals WHERE status IN ('approved', 'rejected')
                    ) {$fy_where_bt}
                ) sub
                {$status_where}
                ORDER BY sub.sort_date DESC, sub.bom_total_id DESC";
    }

    public function get_boms($uid) {
        $sql = $this->get_combined_boms_query(null);
        return $this->db->query($sql)->result();
    }

    public function get_bom_data_by_status($status, $uid) {
        $sql = $this->get_combined_boms_query($status);
        return $this->db->query($sql)->result();
    }

    public function get_bom_data($number, $uid) {
        $this->db->select('bom.* , inventory.item_name');
        $this->db->from('bom');
        $this->db->where('bom.number', $number);
        // $this->db->where('bom.uid', $uid);
        $this->db->join('inventory', 'inventory.code=bom.product_name', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_bom_data_group_by($number, $uid) {
        
        $this->db->select('bom_total.id, bom_total.number_fk as number, bom_total.date, bom_total.status, 
                          bom_total.note, bom_total.project_code, bom_total.customer_code, bom_total.system, 
                          bom_total.location, bom_total.capacity, bom_total.project_qty, bom_total.oc_number, bom_total.approved_by,
                          bom_total.send_to_mrp,
                          customer.company_name, customer.fullname, u.username as prepare_by, u2.username as approved_by_name, bom_total.customer_id_fk');
        $this->db->from('bom_total');
        $this->db->where('bom_total.number_fk', $number);
        // $this->db->where('bom_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id = bom_total.customer_id_fk', 'left');
        $this->db->join('user u', 'bom_total.uid = u.user_id', 'left');
        $this->db->join('user u2', 'bom_total.approved_by = u2.user_id', 'left');
        $query = $this->db->get();
        
        return $query->row_array();
    }

    public function delete_bom_by_bom_number($bom_number, $uid) {
        $this->db->where('number', $bom_number);
        // Removed uid filter — allow delete by BOM number regardless of creator
        $this->db->delete('bom');
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_bom_total_by_bom_number($bom_number, $uid) {
        $this->db->where('number_fk', $bom_number);
        // Removed uid filter — allow delete by BOM number regardless of creator
        $this->db->delete('bom_total');
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_settings($uid) {
        $this->db->select('*');
        $this->db->from('settings');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_total_amount($data_total_amount) {
        return $this->db->insert('bom_total', $data_total_amount);
    }

    public function edit_total_amount($data_total_amount, $number, $uid) {
        $this->db->where('number_fk', $number);
        // Removed uid filter — allow editing BOM header regardless of creator
        $this->db->update('bom_total', $data_total_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_item($bom_id) {
        $this->db->where('bom_id', $bom_id);
        $this->db->delete('bom');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_bom_count($uid) {
        $sql = $this->get_combined_boms_query(null);
        return $this->db->query($sql)->num_rows();
    }
   
    public function get_status($number, $uid) {
        $this->db->select('status');
        $this->db->from('bom_total');
        $this->db->where('number_fk', $number);
        // Removed uid filter — fetch status for any owner
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_gst_bom_status($data_status, $quote_number, $uid) {
        $this->db->where('number_fk', $quote_number);
        // Removed uid filter — allow status updates regardless of creator
        $this->db->update('bom_total', $data_status);
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_bom_number_from_bom_total($id, $uid) {
        $this->db->select('number_fk');
        $this->db->from('bom_total');
        $this->db->where('id', $id);
        // Removed uid filter — fetch BOM number regardless of creator
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function get_bom_draft_count($status, $uid) {
        $sql = $this->get_combined_boms_query($status);
        return $this->db->query($sql)->num_rows();
    }

    public function get_datewise_record($from_date, $to_date, $uid) {
        $f_date = date('Y-m-d', strtotime($from_date));
        $t_date = date('Y-m-d', strtotime($to_date));

        $this->db->select('bom_total.id as bom_total_id, bom_total.number_fk as number, bom_total.date, bom_total.status, 
                          bom_total.note, bom_total.project_code, bom_total.customer_code, bom_total.system, 
                          bom_total.location, bom_total.capacity, bom_total.project_qty, bom_total.oc_number,
                          bom_total.send_to_mrp,
                          customer.company_name, customer.fullname, u.username as prepare_by, u2.username as approved_by_name');
        $this->db->from('bom_total');
        $this->db->where('bom_total.date >=', $f_date);
        $this->db->where('bom_total.date <=', $t_date);
        $this->db->join('customer', 'customer.customer_id = bom_total.customer_id_fk', 'left');
        $this->db->join('user u', 'bom_total.uid = u.user_id', 'left');
        $this->db->join('user u2', 'bom_total.approved_by = u2.user_id', 'left');
        $this->db->order_by("bom_total.id", "desc");
        $query = $this->db->get();
        
        return $query->result();
    }

    public function get_monthyearwise_record($month_year, $uid) {
        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        
        $this->db->select('bom_total.id as bom_total_id, bom_total.number_fk as number, bom_total.date, bom_total.status, 
                          bom_total.note, bom_total.project_code, bom_total.customer_code, bom_total.system, 
                          bom_total.location, bom_total.capacity, bom_total.project_qty, bom_total.oc_number,
                          bom_total.send_to_mrp,
                          customer.company_name, customer.fullname, u.username as prepare_by, u2.username as approved_by_name');
        $this->db->from('bom_total');
        $this->db->like('bom_total.date', $newmonthyear_str, 'both');
        $this->db->join('customer', 'customer.customer_id = bom_total.customer_id_fk', 'left');
        $this->db->join('user u', 'bom_total.uid = u.user_id', 'left');
        $this->db->join('user u2', 'bom_total.approved_by = u2.user_id', 'left');
        // Removed uid filter — show all BOMs across all users
        $this->db->order_by("bom_total.id", "desc");
        $query = $this->db->get();
        
        return $query->result();
    }

    public function get_units() {
        $this->db->select('*');
        $this->db->from('units');
        $query = $this->db->get();
        return $query->result();
    }

    // =========================================================
    // BOM APPROVAL WORKFLOW METHODS
    // =========================================================

    /**
     * Submit a BOM for approval — creates rows in bom_approvals from the matrix
     */
    public function submit_bom_for_approval($bom_number, $bom_id, $uid, $employee_user_id = 0)
    {
        // Get approval matrix rules for BOM document type
        $rules = $this->db->select('*')
                          ->from('approval_matrix')
                          ->where('document_type', 'BOM')
                          ->where('status', 'active')
                          ->order_by('level', 'ASC')
                          ->get()
                          ->result_array();

        if (empty($rules)) {
            // No matrix configured for BOM — create default Admin approval rule so it enters Under Review & BOM Approvals workflow
            $default_rule = array(
                'document_type' => 'BOM',
                'level' => 1,
                'approver_role' => 'Admin',
                'min_amount' => 0.00,
                'max_amount' => 0.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            $this->db->insert('approval_matrix', $default_rule);
            
            $rules = array(array(
                'level' => 1,
                'approver_role' => 'Admin'
            ));
        }

        // Auto-check and migrate bom_approvals table / missing columns for live DB compatibility
        if (!$this->db->table_exists('bom_approvals')) {
            $tbl = $this->db->dbprefix . 'bom_approvals';
            $sql = "CREATE TABLE IF NOT EXISTS `{$tbl}` (
              `approval_id` int(11) NOT NULL AUTO_INCREMENT,
              `bom_number` varchar(100) NOT NULL,
              `bom_id_fk` int(11) NOT NULL DEFAULT 0,
              `approval_level` varchar(50) NOT NULL,
              `approver_role` varchar(50) DEFAULT NULL,
              `required` tinyint(1) DEFAULT 1,
              `level` int(11) DEFAULT NULL,
              `iteration` int(11) NOT NULL DEFAULT 1,
              `approver_email` varchar(100) NOT NULL DEFAULT '',
              `status` varchar(50) DEFAULT 'pending',
              `remarks` text DEFAULT NULL,
              `created_at` datetime DEFAULT current_timestamp(),
              `action_date` datetime DEFAULT NULL,
              `action_by` varchar(100) DEFAULT NULL,
              `uid` int(11) NOT NULL,
              PRIMARY KEY (`approval_id`),
              KEY `idx_bom_number` (`bom_number`),
              KEY `idx_status` (`status`),
              KEY `idx_uid` (`uid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql);
        } else {
            if (!$this->db->field_exists('iteration', 'bom_approvals')) {
                $this->db->query("ALTER TABLE `{$this->db->dbprefix}bom_approvals` ADD `iteration` INT(11) NOT NULL DEFAULT 1 AFTER `level` ");
            }
            if (!$this->db->field_exists('bom_id_fk', 'bom_approvals')) {
                $this->db->query("ALTER TABLE `{$this->db->dbprefix}bom_approvals` ADD `bom_id_fk` INT(11) NOT NULL DEFAULT 0 AFTER `bom_number` ");
            }
            if (!$this->db->field_exists('approver_email', 'bom_approvals')) {
                $this->db->query("ALTER TABLE `{$this->db->dbprefix}bom_approvals` ADD `approver_email` VARCHAR(100) NOT NULL DEFAULT '' AFTER `iteration` ");
            }
        }

        // Get the latest iteration
        $max_iteration_row = $this->db->select_max('iteration')
                                      ->where('bom_number', $bom_number)
                                      ->get('bom_approvals')
                                      ->row_array();
        $iteration = isset($max_iteration_row['iteration']) ? intval($max_iteration_row['iteration']) + 1 : 1;

        // Insert fresh approval rows from matrix
        $rows = array();
        foreach ($rules as $rule) {
            $rows[] = array(
                'bom_number'     => $bom_number,
                'bom_id_fk'      => $bom_id,
                'approval_level' => 'L' . $rule['level'],
                'approver_role'  => $rule['approver_role'],
                'required'       => 1,
                'level'          => $rule['level'],
                'iteration'      => $iteration,
                'approver_email' => '',
                'status'         => $rule['level'] == 1 ? 'pending' : 'waiting',
                'uid'            => $uid,
                'created_at'     => date('Y-m-d H:i:s'),
            );
        }
        $this->db->insert_batch('bom_approvals', $rows);

        // Update BOM status to Under Review (7)
        $this->db->where('number_fk', $bom_number)
                 ->update('bom_total', array('status' => 7));

        return 'submitted';
    }

    /**
     * Get all approval rows for a BOM number
     */
    public function get_bom_approvals($bom_number, $uid)
    {
        return $this->db->select('*')
                        ->from('bom_approvals')
                        ->where('bom_number', $bom_number)
                        ->order_by('level', 'ASC')
                        ->get()
                        ->result_array();
    }

    /**
     * Process an approve/reject action on a BOM approval row
     * Returns: 'approved' | 'next_level' | 'rejected'
     */
    public function process_bom_approval_action($approval_id, $action, $remarks, $actor_name, $uid, $approver_user_id = 0)
    {
        $row = $this->db->where('approval_id', $approval_id)
                        ->get('bom_approvals')
                        ->row_array();

        if (!$row) return false;

        $bom_number = $row['bom_number'];
        $current_level = (int) $row['level'];
        $now = date('Y-m-d H:i:s');

        if ($action === 'rejected') {
            // Mark this row rejected
            $this->db->where('approval_id', $approval_id)
                     ->update('bom_approvals', array(
                         'status'      => 'rejected',
                         'remarks'     => $remarks,
                         'action_date' => $now,
                         'action_by'   => $actor_name,
                     ));
            // Mark BOM as Rejected (5)
            $this->db->where('number_fk', $bom_number)
                     ->update('bom_total', array('status' => 5));
            return 'rejected';
        }

        // Approved — mark this level done
        $this->db->where('approval_id', $approval_id)
                 ->update('bom_approvals', array(
                     'status'      => 'approved',
                     'remarks'     => $remarks,
                     'action_date' => $now,
                     'action_by'   => $actor_name,
                 ));

        // Find next level
        $next = $this->db->where('bom_number', $bom_number)
                         ->where('iteration', $row['iteration'])
                         ->where('level >', $current_level)
                         ->where('status', 'waiting')
                         ->order_by('level', 'ASC')
                         ->limit(1)
                         ->get('bom_approvals')
                         ->row_array();

        if ($next) {
            // Activate next level
            $this->db->where('approval_id', $next['approval_id'])
                     ->update('bom_approvals', array('status' => 'pending'));
            return 'next_level';
        }

        // All levels approved — mark BOM as Approved (4) and sent to MRP (1)
        $this->db->where('number_fk', $bom_number)
                 ->update('bom_total', array(
                     'status' => 4,
                     'send_to_mrp' => 1,
                     'approved_by' => $approver_user_id
                 ));
        return 'approved';
    }

    /**
     * Get all BOMs pending approval for a given role or Admin
     */
    public function get_pending_bom_approvals_for_role($approver_role, $uid)
    {
        $prefix = $this->db->dbprefix;

        $session_data = $this->session->userdata('session_data_head');
        $session_role = $session_data['result']['role_name'] ?? '';
        $is_admin = (strtolower($session_role) === 'admin' || strtolower($approver_role) === 'admin');

        $where_clause = "WHERE ba.status = 'pending'";
        if (!$is_admin && !empty($approver_role)) {
            $approver_role_esc = $this->db->escape($approver_role);
            $where_clause .= " AND ba.approver_role = {$approver_role_esc}";
        }

        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $where_clause .= " AND bt.date >= {$this->db->escape($fy_from)} AND bt.date <= {$this->db->escape($fy_to)}";
        }

        $sql = "SELECT ba.*,
                       bt.date        AS bom_date,
                       bt.oc_number,
                       bt.project_code,
                       bt.system      AS bom_system,
                       c.company_name,
                       c.fullname,
                       u.username     AS prepare_by
                FROM {$prefix}bom_approvals ba
                LEFT JOIN {$prefix}bom_total bt
                    ON bt.number_fk = ba.bom_number
                LEFT JOIN {$prefix}customer c
                    ON c.customer_id = bt.customer_id_fk
                LEFT JOIN {$prefix}user u
                    ON u.user_id = bt.uid
                {$where_clause}
                ORDER BY ba.created_at DESC";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Check if a user's role can approve a given bom_approvals row
     */
    public function can_user_approve_bom($approval_id, $user_roles_list, $uid)
    {
        $row = $this->db->where('approval_id', $approval_id)
                        ->where('status', 'pending')
                        ->get('bom_approvals')
                        ->row_array();

        if (!$row) return false;
        
        // Admin override
        if (in_array('Admin', $user_roles_list) || in_array('admin', $user_roles_list)) {
            return true;
        }

        return in_array($row['approver_role'], $user_roles_list);
    }

    /**
     * Get BOM approval history (all approved or rejected decisions)
     */
    public function get_bom_approval_history($uid)
    {
        $prefix = $this->db->dbprefix;

        $fy_where = "";
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $fy_where = " AND bt.date >= {$this->db->escape($fy_from)} AND bt.date <= {$this->db->escape($fy_to)}";
        }

        $sql = "SELECT ba.*,
                       bt.date        AS bom_date,
                       bt.oc_number,
                       bt.project_code,
                       bt.system      AS bom_system,
                       c.company_name,
                       c.fullname,
                       u.username     AS prepare_by
                FROM {$prefix}bom_approvals ba
                LEFT JOIN {$prefix}bom_total bt
                    ON bt.number_fk = ba.bom_number
                LEFT JOIN {$prefix}customer c
                    ON c.customer_id = bt.customer_id_fk
                LEFT JOIN {$prefix}user u
                    ON u.user_id = bt.uid
                WHERE ba.status IN ('approved', 'rejected') {$fy_where}
                ORDER BY ba.action_date DESC";

        return $this->db->query($sql)->result_array();
    }
}
