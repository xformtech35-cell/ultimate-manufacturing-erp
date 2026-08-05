<?php
class Requisition extends CI_Model
{
    // ===========================================
    // BASIC CRUD METHODS
    // ===========================================

    /**
     * Insert requisition master record
     */
    public function insert_requisition($data)
    {
        $this->db->insert('purchase_requisition', $data);
        return $this->db->insert_id();
    }

    /**
     * Insert requisition items (batch insert)
     */
    public function insert_requisition_items($data)
    {
        return $this->db->insert_batch('purchase_requisition_items', $data);
    }

    /**
     * Get last PR number for sequence generation
     */
    public function get_last_pr_number($uid)
    {
        // Determine financial year
        if (date('m') <= 3) {
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        // Get the last PR number from purchase_requisition_items
        $this->db->select('pr_no');
        $this->db->from('purchase_requisition_items');
        $this->db->like('pr_no', $financial_year, 'before');
        $this->db->order_by('pr_no', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        $last_pr_no = $query->row();

        if ($last_pr_no) {
            preg_match('/PR\/(\d+)/', $last_pr_no->pr_no, $matches);
            $last_number = isset($matches[1]) ? (int)$matches[1] : 0;
            return $last_number;
        } else {
            return 1; // start from 1 if no PR exists
        }
    }

    /**
     * Get all purchase requisitions for a user
     */
    public function get_purchase_requisition($user_id)
    {
        $this->db->select('pr.*, x.*, d.department_name, u.username AS requested_by_name');
        $this->db->from('purchase_requisition_items pr');
        $this->db->join('purchase_requisition x', 'x.pr_id = pr.pr_id', 'left');
        $this->db->join('department_master d', 'x.department_id_fk = d.department_id', 'left');
        $this->db->join('user u', 'x.requested_by = u.user_id', 'left');

        $this->db->where('pr.created_by', $user_id);
        $this->db->order_by('pr.item_id ', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * Get month-year wise records
     */
    public function get_monthyearwise_record($month_year, $user_id)
    {
        $this->db->select('pr.*, x.*, d.department_name, u.username AS requested_by_name');
        $this->db->from('purchase_requisition_items pr');
        $this->db->join('purchase_requisition x', 'x.pr_id = pr.pr_id', 'left');
        $this->db->join('department_master d', 'x.department_id_fk = d.department_id', 'left');
        $this->db->join('user u', 'x.requested_by = u.user_id', 'left');

        $this->db->where('pr.created_by', $user_id);
        $this->db->like("DATE_FORMAT(x.pr_date,'%b-%Y')", $month_year);
        $this->db->order_by('pr.item_id ', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * Get one requisition master record
     */
    public function get_requisition_by_id($pr_id)
    {
        $this->db->select('*');
        $this->db->from('purchase_requisition');
        $this->db->where('pr_id', $pr_id);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * Get requisition items by item IDs
     */
    public function get_requisition_items_by_item_ids($item_ids)
    {
        $this->db->select('*');
        $this->db->from('purchase_requisition_items');
        $this->db->where_in('item_id', $item_ids);
        $this->db->order_by('item_id', 'ASC');
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * Get single requisition item by ID
     */
    public function get_requisition_item_by_id($item_id)
    {
        $this->db->select('*');
        $this->db->from('purchase_requisition_items');
        $this->db->where('item_id', $item_id);
        return $this->db->get()->row();
    }

    /**
     * Update requisition master record
     */
    public function update_requisition($pr_id, $data)
    {
        $this->db->where('pr_id', $pr_id);
        return $this->db->update('purchase_requisition', $data);
    }

    /**
     * Delete all items from requisition
     */
    public function delete_requisition_items($pr_id)
    {
        $this->db->where('pr_id', $pr_id);
        return $this->db->delete('purchase_requisition_items');
    }

    /**
     * Delete full requisition (master)
     */
    public function delete_requisition($pr_id)
    {
        $this->db->where('pr_id', $pr_id);
        return $this->db->delete('purchase_requisition');
    }

    // ===========================================
    // APPROVAL FLOW METHODS
    // ===========================================

    /**
     * Calculate total value of requisition
     */
    public function calculate_requisition_total($pr_id)
    {
        $this->db->select_sum('estimated_cost * quantity', 'total_value');
        $this->db->from('purchase_requisition_items');
        $this->db->where('pr_id', $pr_id);
        $result = $this->db->get()->row();
        return $result->total_value ?: 0;
    }

    /**
     * Get approval matrix for PR
     */
    public function get_pr_approval_matrix()
    {
        $this->db->where('document_type', 'PR');
        $this->db->where('status', 'active');
        $this->db->order_by('level', 'ASC');
        return $this->db->get('approval_matrix')->result();
    }

    /**
     * Get next approver based on amount
     */
    public function get_next_approver($total_value)
    {
        $this->db->where('document_type', 'PR');
        $this->db->where('status', 'active');
        $this->db->where('(min_amount <= ' . $total_value . ' OR min_amount = 0)');
        $this->db->where('(max_amount >= ' . $total_value . ' OR max_amount = 0)');
        $this->db->order_by('level', 'ASC');
        $this->db->limit(1);
        
        return $this->db->get('approval_matrix')->row();
    }

    /**
     * Get current approver for PR
     */
    public function get_current_approver($pr_id)
    {
        $this->db->select('current_approver_role, approval_level, workflow_status');
        $this->db->from('purchase_requisition');
        $this->db->where('pr_id', $pr_id);
        return $this->db->get()->row();
    }

    /**
     * Submit PR for approval
     */
    public function submit_for_approval($pr_id)
    {
        // Calculate total value
        $total_value = $this->calculate_requisition_total($pr_id);
        
        // Get first approver (Site Incharge)
        $this->db->where('document_type', 'PR');
        $this->db->where('level', 1);
        $this->db->where('status', 'active');
        $first_approver = $this->db->get('approval_matrix')->row();
        
        if (!$first_approver) {
            return false;
        }

        $data = [
            'total_value' => $total_value,
            'submitted_for_approval' => date('Y-m-d H:i:s'),
            'approval_status' => 'Pending',
            'workflow_status' => 'L1_Pending',
            'approval_level' => $first_approver->level,
            'current_approver_role' => $first_approver->approver_role
        ];
        
        $this->db->where('pr_id', $pr_id);
        $result = $this->db->update('purchase_requisition', $data);
        
        if ($result) {
            // Add to approval history
            $this->add_approval_history($pr_id, $first_approver->level, $first_approver->approver_role, null, 'Submitted', 'PR submitted for approval');
        }
        
        return $result;
    }

    /**
     * Process approval action
     */
    public function process_approval($pr_id, $approver_id, $approver_role, $action, $comments = '')
    {
        $requisition = $this->get_requisition_by_id($pr_id);
        
        // Add to approval history
        $this->add_approval_history($pr_id, $requisition->approval_level, $approver_role, $approver_id, $action, $comments);
        
        if ($action == 'Approved') {
            return $this->process_approval_action($pr_id, $approver_id, $approver_role, $comments);
        } elseif ($action == 'Rejected') {
            return $this->process_rejection($pr_id, $approver_id, $comments);
        }
        
        return false;
    }

    /**
     * Process approval based on current level
     */
    private function process_approval_action($pr_id, $approver_id, $approver_role, $comments)
    {
        $requisition = $this->get_requisition_by_id($pr_id);
        $update_data = [];
        
        // Update based on current approval level
        switch($requisition->approval_level) {
            case 1: // Site Incharge approval
                $update_data['approved_by_l1'] = $approver_id;
                $update_data['approved_date_l1'] = date('Y-m-d H:i:s');
                $update_data['approval_level'] = 2;
                $update_data['workflow_status'] = 'L2_Pending';
                $update_data['current_approver_role'] = 'Manager';
                break;
                
            case 2: // Manager approval
                $update_data['approved_by_l2'] = $approver_id;
                $update_data['approved_date_l2'] = date('Y-m-d H:i:s');
                $update_data['approval_level'] = 3;
                $update_data['workflow_status'] = 'L3_Pending';
                $update_data['current_approver_role'] = 'Procurement Head';
                break;
                
            case 3: // Procurement Head approval (Final)
                $update_data['approved_by_l3'] = $approver_id;
                $update_data['approved_date_l3'] = date('Y-m-d H:i:s');
                $update_data['approval_status'] = 'Approved';
                $update_data['workflow_status'] = 'Approved';
                $update_data['current_approver_role'] = null;
                break;
        }
        
        $this->db->where('pr_id', $pr_id);
        $result = $this->db->update('purchase_requisition', $update_data);
        
        return $result;
    }

    /**
     * Process rejection
     */
    private function process_rejection($pr_id, $rejector_id, $reason)
    {
        $data = [
            'approval_status' => 'Rejected',
            'workflow_status' => 'Rejected',
            'rejected_by' => $rejector_id,
            'rejected_date' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason,
            'current_approver_role' => null
        ];
        
        $this->db->where('pr_id', $pr_id);
        return $this->db->update('purchase_requisition', $data);
    }

    /**
     * Add to approval history
     */
    public function add_approval_history($pr_id, $level, $approver_role, $approver_id, $action, $comments = '')
    {
        // Get PR number
        $this->db->select('pr_no');
        $this->db->from('purchase_requisition_items');
        $this->db->where('pr_id', $pr_id);
        $this->db->limit(1);
        $pr_item = $this->db->get()->row();
        
        $history_data = [
            'pr_id' => $pr_id,
            'pr_no' => $pr_item ? $pr_item->pr_no : '' . $pr_id,
            'approval_level' => $level,
            'approver_role' => $approver_role,
            'approver_id' => $approver_id,
            'action' => $action,
            'comments' => $comments,
            'action_date' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('pr_approval_history', $history_data);
    }

    /**
     * Get approval history for PR
     */
    public function get_approval_history($pr_id)
    {
        $this->db->select('h.*, u.username as approver_name');
        $this->db->from('pr_approval_history h');
        $this->db->join('user u', 'h.approver_id = u.user_id', 'left');
        $this->db->where('h.pr_id', $pr_id);
        $this->db->order_by('h.action_date', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get PRs pending user's approval
     */
    public function get_pending_approvals($user_id)
    {
        // Get user's roles
        $this->db->select('role_name');
        $this->db->from('user_roles');
        $this->db->where('user_id', $user_id);
        $this->db->where('is_active', 1);
        $roles_result = $this->db->get()->result();
        
        $user_roles = [];
        foreach ($roles_result as $role) {
            $user_roles[] = $role->role_name;
        }
        
        if (empty($user_roles)) {
            return [];
        }
        
        // Get PRs where current approver role matches user's role
        $this->db->select('pr.*, d.department_name, u.username as requester_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        $this->db->where_in('pr.current_approver_role', $user_roles);
        $this->db->where('pr.approval_status', 'Pending');
        $this->db->order_by('pr.submitted_for_approval', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Get PRs by user (created by)
     */
    public function get_user_requisitions($user_id, $status = null)
    {
        $this->db->select('pr.*, d.department_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');
        $this->db->where('pr.created_by', $user_id);
        
        if ($status) {
            $this->db->where('pr.approval_status', $status);
        }
        
        $this->db->order_by('pr.pr_date', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Check if user can approve this PR
     */
    public function can_user_approve($pr_id, $user_id)
    {
        // Get user's roles
        $this->db->select('role_name');
        $this->db->from('user_roles');
        $this->db->where('user_id', $user_id);
        $this->db->where('is_active', 1);
        $roles_result = $this->db->get()->result();
        
        $user_roles = [];
        foreach ($roles_result as $role) {
            $user_roles[] = $role->role_name;
        }
        
        if (empty($user_roles)) {
            return false;
        }
        
        // Check if PR needs approval from user's role
        $this->db->select('current_approver_role, approval_status');
        $this->db->from('purchase_requisition');
        $this->db->where('pr_id', $pr_id);
        $this->db->where('approval_status', 'Pending');
        $pr = $this->db->get()->row();
        
        return $pr && in_array($pr->current_approver_role, $user_roles);
    }

    /**
     * Get PR dashboard statistics
     */
    public function get_dashboard_stats($user_id)
    {
        $stats = [];
        
        // Get user's roles
        $this->db->select('role_name');
        $this->db->from('user_roles');
        $this->db->where('user_id', $user_id);
        $this->db->where('is_active', 1);
        $roles_result = $this->db->get()->result();
        
        $user_roles = [];
        foreach ($roles_result as $role) {
            $user_roles[] = $role->role_name;
        }
        
        // Pending PRs for approval
        if (!empty($user_roles)) {
            $this->db->where_in('current_approver_role', $user_roles);
            $this->db->where('approval_status', 'Pending');
            $stats['pending_approvals'] = $this->db->count_all_results('purchase_requisition');
        } else {
            $stats['pending_approvals'] = 0;
        }
        
        // User's pending PRs
        $this->db->where('created_by', $user_id);
        $this->db->where('approval_status', 'Pending');
        $stats['my_pending_prs'] = $this->db->count_all_results('purchase_requisition');
        
        // User's approved PRs
        $this->db->where('created_by', $user_id);
        $this->db->where('approval_status', 'Approved');
        $stats['my_approved_prs'] = $this->db->count_all_results('purchase_requisition');
        
        // User's rejected PRs
        $this->db->where('created_by', $user_id);
        $this->db->where('approval_status', 'Rejected');
        $stats['my_rejected_prs'] = $this->db->count_all_results('purchase_requisition');
        
        // All pending PRs
        $this->db->where('approval_status', 'Pending');
        $stats['total_pending_prs'] = $this->db->count_all_results('purchase_requisition');
        
        return $stats;
    }

    /**
     * Get all requisition items for a PR
     */
    public function get_requisition_items($pr_id)
    {
        $this->db->select('*');
        $this->db->from('purchase_requisition_items');
        $this->db->where('pr_id', $pr_id);
        $this->db->order_by('item_id', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get users by role
     */
    public function get_users_by_role($role_name)
    {
        $this->db->select('u.*');
        $this->db->from('user u');
        $this->db->join('user_roles ur', 'u.user_id = ur.user_id');
        $this->db->where('ur.role_name', $role_name);
        $this->db->where('ur.is_active', 1);
        return $this->db->get()->result();
    }

    /**
     * Get all PRs with filters
     */
    public function get_all_requisitions($filters = [])
    {
        $this->db->select('pr.*, d.department_name, u.username as requester_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        
        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('pr.approval_status', $filters['status']);
        }
        
        if (!empty($filters['department'])) {
            $this->db->where('pr.department_id_fk', $filters['department']);
        }
        
        if (!empty($filters['start_date'])) {
            $this->db->where('pr.pr_date >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('pr.pr_date <=', $filters['end_date']);
        }
        
        if (!empty($filters['urgency'])) {
            $this->db->where('pr.urgency_level', $filters['urgency']);
        }
        
        $this->db->order_by('pr.pr_date', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Update PR status
     */
    public function update_pr_status($pr_id, $status, $reason = null)
    {
        $data = ['approval_status' => $status];
        
        if ($status == 'Rejected' && $reason) {
            $data['rejection_reason'] = $reason;
            $data['rejected_date'] = date('Y-m-d H:i:s');
        }
        
        $this->db->where('pr_id', $pr_id);
        return $this->db->update('purchase_requisition', $data);
    }

    /**
     * Get PR workflow status
     */
    public function get_pr_workflow_status($pr_id)
    {
        $this->db->select('workflow_status, approval_level, current_approver_role');
        $this->db->from('purchase_requisition');
        $this->db->where('pr_id', $pr_id);
        return $this->db->get()->row();
    }

    /**
     * Get approval statistics by user
     */
    public function get_user_approval_stats($user_id)
    {
        $stats = [];
        
        // Approved count
        $this->db->from('pr_approval_history');
        $this->db->where('approver_id', $user_id);
        $this->db->where('action', 'Approved');
        $stats['approved_count'] = $this->db->count_all_results();
        
        // Rejected count
        $this->db->from('pr_approval_history');
        $this->db->where('approver_id', $user_id);
        $this->db->where('action', 'Rejected');
        $stats['rejected_count'] = $this->db->count_all_results();
        
        // Pending approvals
        $this->db->select('pr.*');
        $this->db->from('purchase_requisition pr');
        $this->db->join('user_roles ur', 'ur.user_id = ' . $user_id);
        $this->db->where('pr.current_approver_role = ur.role_name');
        $this->db->where('pr.approval_status', 'Pending');
        $stats['pending_count'] = $this->db->count_all_results();
        
        return $stats;
    }

    /**
     * Get detailed requisition by ID with approver names
     */
    public function get_requisition_with_details($pr_id)
    {
        $this->db->select('pr.*, 
                          d.department_name,
                          u.username as requester_name,
                          u1.username as site_incharge_name,
                          u2.username as manager_name,
                          u3.username as procurement_head_name,
                          ur.username as rejected_by_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        $this->db->join('user u1', 'pr.approved_by_l1 = u1.user_id', 'left');
        $this->db->join('user u2', 'pr.approved_by_l2 = u2.user_id', 'left');
        $this->db->join('user u3', 'pr.approved_by_l3 = u3.user_id', 'left');
        $this->db->join('user ur', 'pr.rejected_by = ur.user_id', 'left');
        $this->db->where('pr.pr_id', $pr_id);
        
        return $this->db->get()->row();
    }

    /**
     * Get requisitions with advanced filters
     */
    public function get_filtered_requisitions($params = [])
    {
        $this->db->select('pr.*, d.department_name, u.username as requester_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        
        // Apply filters
        if (!empty($params['user_id'])) {
            $this->db->where('pr.created_by', $params['user_id']);
        }
        
        if (!empty($params['status'])) {
            $this->db->where('pr.approval_status', $params['status']);
        }
        
        if (!empty($params['workflow_status'])) {
            $this->db->where('pr.workflow_status', $params['workflow_status']);
        }
        
        if (!empty($params['approval_level'])) {
            $this->db->where('pr.approval_level', $params['approval_level']);
        }
        
        if (!empty($params['department_id'])) {
            $this->db->where('pr.department_id_fk', $params['department_id']);
        }
        
        if (!empty($params['start_date'])) {
            $this->db->where('pr.pr_date >=', $params['start_date']);
        }
        
        if (!empty($params['end_date'])) {
            $this->db->where('pr.pr_date <=', $params['end_date']);
        }
        
        if (!empty($params['urgency'])) {
            $this->db->where('pr.urgency_level', $params['urgency']);
        }
        
        if (!empty($params['min_amount'])) {
            $this->db->where('pr.total_value >=', $params['min_amount']);
        }
        
        if (!empty($params['max_amount'])) {
            $this->db->where('pr.total_value <=', $params['max_amount']);
        }
        
        // Ordering
        $order_by = !empty($params['order_by']) ? $params['order_by'] : 'pr.pr_date';
        $order_dir = !empty($params['order_dir']) ? $params['order_dir'] : 'DESC';
        $this->db->order_by($order_by, $order_dir);
        
        // Limit/Offset for pagination
        if (!empty($params['limit'])) {
            $this->db->limit($params['limit'], !empty($params['offset']) ? $params['offset'] : 0);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Count filtered requisitions
     */
    public function count_filtered_requisitions($params = [])
    {
        $this->db->from('purchase_requisition pr');
        
        // Apply filters
        if (!empty($params['user_id'])) {
            $this->db->where('pr.created_by', $params['user_id']);
        }
        
        if (!empty($params['status'])) {
            $this->db->where('pr.approval_status', $params['status']);
        }
        
        if (!empty($params['workflow_status'])) {
            $this->db->where('pr.workflow_status', $params['workflow_status']);
        }
        
        if (!empty($params['approval_level'])) {
            $this->db->where('pr.approval_level', $params['approval_level']);
        }
        
        if (!empty($params['department_id'])) {
            $this->db->where('pr.department_id_fk', $params['department_id']);
        }
        
        if (!empty($params['start_date'])) {
            $this->db->where('pr.pr_date >=', $params['start_date']);
        }
        
        if (!empty($params['end_date'])) {
            $this->db->where('pr.pr_date <=', $params['end_date']);
        }
        
        if (!empty($params['urgency'])) {
            $this->db->where('pr.urgency_level', $params['urgency']);
        }
        
        if (!empty($params['min_amount'])) {
            $this->db->where('pr.total_value >=', $params['min_amount']);
        }
        
        if (!empty($params['max_amount'])) {
            $this->db->where('pr.total_value <=', $params['max_amount']);
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Get requisition summary statistics
     */
    public function get_requisition_summary()
    {
        $summary = [];
        
        // Total requisitions
        $summary['total'] = $this->db->count_all('purchase_requisition');
        
        // By status
        $this->db->select('approval_status, COUNT(*) as count');
        $this->db->from('purchase_requisition');
        $this->db->group_by('approval_status');
        $status_counts = $this->db->get()->result();
        
        foreach ($status_counts as $status) {
            $summary['by_status'][$status->approval_status] = $status->count;
        }
        
        // By urgency
        $this->db->select('urgency_level, COUNT(*) as count');
        $this->db->from('purchase_requisition');
        $this->db->group_by('urgency_level');
        $urgency_counts = $this->db->get()->result();
        
        foreach ($urgency_counts as $urgency) {
            $summary['by_urgency'][$urgency->urgency_level] = $urgency->count;
        }
        
        // By department
        $this->db->select('d.department_name, COUNT(pr.pr_id) as count');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');
        $this->db->group_by('pr.department_id_fk');
        $dept_counts = $this->db->get()->result();
        
        foreach ($dept_counts as $dept) {
            $summary['by_department'][$dept->department_name] = $dept->count;
        }
        
        // Total value
        $this->db->select_sum('total_value', 'total');
        $this->db->from('purchase_requisition');
        $total_value = $this->db->get()->row();
        $summary['total_value'] = $total_value->total ?: 0;
        
        return $summary;
    }

    /**
     * Get recent requisitions
     */
    public function get_recent_requisitions($limit = 10)
    {
        $this->db->select('pr.*, d.department_name, u.username as requester_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        $this->db->order_by('pr.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Get requisitions expiring soon (based on required_date)
     */
    public function get_expiring_requisitions($days = 7)
    {
        $today = date('Y-m-d');
        $expiry_date = date('Y-m-d', strtotime("+$days days"));
        
        $this->db->select('pr.*, d.department_name, u.username as requester_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        $this->db->where('pr.required_date >=', $today);
        $this->db->where('pr.required_date <=', $expiry_date);
        $this->db->where('pr.approval_status', 'Approved');
        $this->db->order_by('pr.required_date', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Check if requisition can be edited
     */
    public function can_edit_requisition($pr_id, $user_id)
    {
        $this->db->select('approval_status, workflow_status, created_by');
        $this->db->from('purchase_requisition');
        $this->db->where('pr_id', $pr_id);
        $requisition = $this->db->get()->row();
        
        if (!$requisition) {
            return false;
        }
        
        // Check if user is the creator
        if ($requisition->created_by != $user_id) {
            return false;
        }
        
        // Check if requisition is in draft state
        if ($requisition->workflow_status != 'Draft' && $requisition->approval_status != 'Pending') {
            return false;
        }
        
        return true;
    }

    /**
     * Get requisition timeline
     */
    public function get_requisition_timeline($pr_id)
    {
        $timeline = [];
        
        // Get requisition creation
        $requisition = $this->get_requisition_by_id($pr_id);
        if ($requisition) {
            $timeline[] = [
                'date' => $requisition->created_at,
                'action' => 'Created',
                'description' => 'Purchase Requisition created',
                'user' => $this->get_user_name($requisition->created_by)
            ];
        }
        
        // Get submission date
        if ($requisition->submitted_for_approval) {
            $timeline[] = [
                'date' => $requisition->submitted_for_approval,
                'action' => 'Submitted',
                'description' => 'Submitted for approval',
                'user' => $this->get_user_name($requisition->created_by)
            ];
        }
        
        // Get approval