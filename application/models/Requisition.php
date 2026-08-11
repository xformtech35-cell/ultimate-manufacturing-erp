<?php
class Requisition extends CI_Model
{
    // ===========================================
    // BASIC CRUD METHODS
    // ===========================================


    public function __construct()
    {
        parent::__construct();

        $role_name = $GLOBALS['role_name'] ?? null;
    }

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
        $this->db->like('pr_no', $financial_year, 'both');
        $this->db->order_by('pr_no', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        $last_pr_no = $query->row();

        if ($last_pr_no) {
            preg_match('/PR\/\d{2}-\d{2}\/(\d+)/i', $last_pr_no->pr_no, $matches);
            if (!isset($matches[1])) {
                preg_match('/PR\/(\d+)/i', $last_pr_no->pr_no, $matches);
            }
            $last_number = isset($matches[1]) ? (int)$matches[1] : 0;
            return $last_number;
        } else {
            return 1; // start from 1 if no PR exists
        }
    }

    /**
     * Get one requisition master record
     */
    public function get_requisition_by_id($pr_id)
    {
        $this->db->select('pr.*, 
                  d.department_name,
                  l.location_name,
                  u.username as requester_name,
                  (SELECT pri.pr_no 
                   FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
                   WHERE pri.pr_id = pr.pr_id 
                   LIMIT 1) as pr_no');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left'); // Add this join
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        $this->db->where('pr.pr_id', $pr_id);

        return $this->db->get()->row();
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
     * Update requisition master record
     */
    public function update_requisition($pr_id, $data)
    {
        $this->db->where('pr_id', $pr_id);



        // var_dump($data);

        // die();
        return $this->db->update('purchase_requisition', $data);
    }

    // ===========================================
    // APPROVAL WORKFLOW CORE METHODS
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
        return $result->total_value ? (float)$result->total_value : 0;
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
     * Submit PR for approval
     */
    public function submit_for_approval($pr_id, $user_id)
    {
        // Calculate total value
        $total_value = $this->calculate_requisition_total($pr_id);

        // Get first approver based on amount
        $first_approver = $this->get_next_approver($total_value);

        if (!$first_approver) {
            return false;
        }

        $data = [
            'total_value' => $total_value,
            'submitted_for_approval' => date('Y-m-d H:i:s'),
            'approval_status' => 'Pending',
            'workflow_status' => 'Submitted',
            'approval_level' => $first_approver->level,
            'current_approver_role' => $first_approver->approver_role
        ];

        $this->db->where('pr_id', $pr_id);
        $result = $this->db->update('purchase_requisition', $data);

        if ($result) {
            // Add to approval history
            $this->add_approval_history($pr_id, $first_approver->level, $first_approver->approver_role, $user_id, 'Submitted', 'PR submitted for approval');
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
        } elseif ($action == 'Returned') {
            return $this->process_return($pr_id, $approver_id, $comments);
        }

        return false;
    }

    /**
     * Process approval based on current level
     */
    private function process_approval_action($pr_id, $approver_id, $approver_role, $comments)
    {
        $requisition = $this->get_requisition_by_id($pr_id);
        $total_value = $requisition->total_value;

        $update_data = [];

        // Get current approval level
        $current_level = $requisition->approval_level;

        // Update based on current approval level
        switch ($current_level) {
            case 1: // Site Incharge approval
                $update_data['approved_by_site_incharge'] = $approver_id;
                $update_data['approved_date_site_incharge'] = date('Y-m-d H:i:s');
                break;

            case 2: // Manager approval
                $update_data['approved_by_manager'] = $approver_id;
                $update_data['approved_date_manager'] = date('Y-m-d H:i:s');
                break;

            case 3: // Procurement Head approval
                $update_data['approved_by_procurement_head'] = $approver_id;
                $update_data['approved_date_procurement_head'] = date('Y-m-d H:i:s');
                break;
        }

        // Get next approver based on amount

        // echo $current_level; echo "<br>"; echo  $total_value;



        $next_approver = $this->get_next_approver_by_level($current_level + 1, $total_value);

        //    echo "<br>";

        //    var_dump($next_approver);

        //   echo "EEEEEEEEEEEEEEEE<br>";

        //  die();

        // Check if there's a next approver
        if ($next_approver) {


            //echo "ooooooooooooo";
            $update_data['approval_level'] = $current_level + 1;
            $update_data['workflow_status'] = 'L' . ($current_level + 1) . '_Pending';
            $update_data['current_approver_role'] = $next_approver->approver_role;
        } else {
            // Final approval
            $update_data['approval_status'] = 'Approved';
            $update_data['workflow_status'] = 'Approved';
            $update_data['current_approver_role'] = null;
        }


        // echo "<br>";

        //  echo  $next_approver;

        // echo "EEEEEEEEEEEEEEEE";

        // var_dump($update_data);

        // die();

        $this->db->where('pr_id', $pr_id);
        $result = $this->db->update('purchase_requisition', $update_data);

        return $result;
    }

    /**
     * Get next approver by level
     */
    private function get_next_approver_by_level($level, $total_value)
    {

        // echo "<br> level :::::::::: " . $level . " total value :::::::::: " . $total_value ;

        // die();
        // Cast total_value to float to ensure it's a number
        $total_value_float = (float)$total_value;

        $this->db->where('document_type', 'PR');
        $this->db->where('level', $level);
        $this->db->where('status', 'active');

        // Use proper SQL formatting
        // $this->db->where("(min_amount <= $total_value_float OR min_amount = 0)");
        // $this->db->where("(max_amount >= $total_value_float OR max_amount = 0)");

        $this->db->order_by('level', 'ASC');

        return $this->db->get('approval_matrix')->row();
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
     * Process return for revision
     */
    private function process_return($pr_id, $returned_by, $comments)
    {
        $data = [
            'workflow_status' => 'Draft',
            'approval_status' => 'Pending',
            'approval_level' => 1,
            'current_approver_role' => null,
            'submitted_for_approval' => null
        ];

        $this->db->where('pr_id', $pr_id);
        return $this->db->update('purchase_requisition', $data);
    }

    /**
     * Add to approval history
     */
    public function add_approval_history($pr_id, $level, $approver_role, $approver_id, $action, $comments = '')
    {
        // Get PR number from items table
        $this->db->select('pr_no');
        $this->db->from('purchase_requisition_items');
        $this->db->where('pr_id', $pr_id);
        $this->db->limit(1);
        $pr_item = $this->db->get()->row();

        // Get approver name
        $approver_name = 'System';
        if ($approver_id) {
            $this->db->select('username');
            $this->db->from('user');
            $this->db->where('user_id', $approver_id);
            $user = $this->db->get()->row();
            if ($user) {
                $approver_name = $user->username;
            }
        }

        // echo $approver_name;

        // die();

        $history_data = [
            'pr_id' => $pr_id,
            'pr_no' => $pr_item ? $pr_item->pr_no : '' . $pr_id,
            'approval_level' => $level,
            'approver_role' => $approver_role,
            'approver_id' => $approver_id,
            'approver_name' => $approver_name,
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
        $this->db->select('h.*, u.username as approver_name, r.role_name as approver_actual_role');
        $this->db->from('pr_approval_history h');
        $this->db->join('user u', 'h.approver_id = u.user_id', 'left');
        $this->db->join('role r', 'u.role = r.role_id', 'left');
        $this->db->where('h.pr_id', $pr_id);
        return $this->db->get()->result();
    }

    /**
     * Get PRs pending user's approval
     */
    public function get_pending_approvals($user_id = null)
    {
        $session_data_head = $this->session->userdata('session_data_head');
        if (!$user_id) {
            $user_id = $session_data_head['result']['user_id'] ?? ($GLOBALS['user_id'] ?? null);
        }

        $role_name_session = $session_data_head['result']['role_name'] ?? ($GLOBALS['role_name'] ?? '');
        $is_admin = (strtolower($role_name_session) === 'admin');

        if (!$is_admin && $user_id) {
            $user_role_row = $this->db->select('r.role_name')
                ->from('user u')
                ->join('role r', 'u.role = r.role_id', 'left')
                ->where('u.user_id', $user_id)
                ->get()->row_array();
            if (!empty($user_role_row['role_name']) && strtolower($user_role_row['role_name']) === 'admin') {
                $is_admin = true;
            }
        }

        $user_roles = [];
        if (!$is_admin && $user_id) {
            $user_roles = $this->get_user_roles($user_id);
            if (empty($user_roles)) {
                return [];
            }
        }

        // Get PRs where current approver role matches any of user's roles or user is Admin
        $this->db->select('pr.*, 
                     d.department_name, 
                     u.username as requester_name,
                     l.location_name,
                     (SELECT pri.pr_no 
                      FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
                      WHERE pri.pr_id = pr.pr_id 
                      LIMIT 1) as pr_no');

        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');

        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('pr.created_at >=', $fy_from);
            $this->db->where('pr.created_at <=', $fy_to);
        }

        // ONLY FOR NON-ADMIN USERS: Filter by assigned approver roles
        if (!$is_admin && !empty($user_roles)) {
            $this->db->where_in('pr.current_approver_role', $user_roles);
        }

        $this->db->where('pr.approval_status', 'Pending');
        $this->db->where('pr.workflow_status !=', 'Draft');
        $this->db->order_by('pr.submitted_for_approval', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get user role from user_roles table
     */
    public function get_user_role($user_id)
    {
        $this->db->select('role_name');
        $this->db->from('user_roles');
        $this->db->where('user_id', $user_id);
        $this->db->where('is_active', 1);
        $this->db->limit(1);
        $role = $this->db->get()->row();

        return $role ? $role->role_name : null;
    }

    /**
     * Get all roles for a user
     */
    public function get_user_roles($user_id)
    {
        $this->db->select('role_name');
        $this->db->from('user_roles');
        $this->db->where('user_roles.user_id', $user_id);  // Specify the table
        $this->db->where('is_active', 1);
        $result = $this->db->get()->result();

        $roles = [];
        foreach ($result as $role) {
            $roles[] = $role->role_name;
        }

        return $roles;
    }

    /**
     * Check if user can approve this PR
     */
    public function can_user_approve($pr_id, $user_id)
    {
        $pr = $this->get_requisition_by_id($pr_id);
        if (!$pr || $pr->approval_status != 'Pending') {
            return false;
        }

        $session_data = $this->session->userdata('session_data_head');
        $session_role = $session_data['result']['role_name'] ?? '';
        if (strtolower($session_role) === 'admin') {
            return true;
        }

        $user_role_row = $this->db->select('r.role_name')
            ->from('user u')
            ->join('role r', 'u.role = r.role_id', 'left')
            ->where('u.user_id', $user_id)
            ->get()->row_array();

        if (!empty($user_role_row['role_name']) && strtolower($user_role_row['role_name']) === 'admin') {
            return true;
        }

        // Get all user roles
        $user_roles = $this->get_user_roles($user_id);

        return in_array($pr->current_approver_role, $user_roles);
    }

    /**
     * Get detailed requisition by ID with approver names
     */
    public function get_requisition_with_details($pr_id)
    {
        $this->db->select('pr.*, 
                  d.department_name,
                  l.location_name,
                  l.city as location_city,
                  l.address as location_address,
                  u.username as requester_name,
                  u1.username as site_incharge_name,
                  u2.username as manager_name,
                  u3.username as procurement_head_name,
                  ur.username as rejected_by_name,
                  pri.pr_no');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left'); // Add this join
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        $this->db->join('user u1', 'pr.approved_by_site_incharge = u1.user_id', 'left');
        $this->db->join('user u2', 'pr.approved_by_manager = u2.user_id', 'left');
        $this->db->join('user u3', 'pr.approved_by_procurement_head = u3.user_id', 'left');
        $this->db->join('user ur', 'pr.rejected_by = ur.user_id', 'left');
        $this->db->join('purchase_requisition_items pri', 'pr.pr_id = pri.pr_id', 'left');
        $this->db->where('pr.pr_id', $pr_id);
        $this->db->group_by('pr.pr_id');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Get PR approval progress
     */
    public function get_approval_progress($pr_id)
    {
        $pr = $this->get_requisition_by_id($pr_id);
        $matrix = $this->get_pr_approval_matrix();

        $progress = [
            'total_levels' => count($matrix),
            'completed_levels' => 0,
            'current_level' => $pr ? $pr->approval_level : 0,
            'percentage' => 0,
            'levels' => []
        ];

        foreach ($matrix as $level) {
            $level_info = [
                'level' => $level->level,
                'role' => $level->approver_role,
                'min_amount' => $level->min_amount,
                'max_amount' => $level->max_amount,
                'status' => 'pending',
                'approved_by' => null,
                'approved_date' => null
            ];

            // Check if this level is completed
            switch ($level->level) {
                case 1:
                    if ($pr && $pr->approved_date_site_incharge) {
                        $level_info['status'] = 'approved';
                        $level_info['approved_by'] = $pr->approved_by_site_incharge;
                        $level_info['approved_date'] = $pr->approved_date_site_incharge;
                        $progress['completed_levels']++;
                    }
                    break;
                case 2:
                    if ($pr && $pr->approved_date_manager) {
                        $level_info['status'] = 'approved';
                        $level_info['approved_by'] = $pr->approved_by_manager;
                        $level_info['approved_date'] = $pr->approved_date_manager;
                        $progress['completed_levels']++;
                    }
                    break;
                case 3:
                    if ($pr && $pr->approved_date_procurement_head) {
                        $level_info['status'] = 'approved';
                        $level_info['approved_by'] = $pr->approved_by_procurement_head;
                        $level_info['approved_date'] = $pr->approved_date_procurement_head;
                        $progress['completed_levels']++;
                    }
                    break;
            }

            // Check if this is current level
            if ($pr && $pr->approval_level == $level->level && $pr->approval_status == 'Pending') {
                $level_info['status'] = 'current';
            }

            // Check if PR value falls in this level's range
            if ($pr && $pr->total_value) {
                $min = (float)$level->min_amount;
                $max = (float)$level->max_amount;

                if ($max == 0) {
                    $max = PHP_FLOAT_MAX;
                }

                if ($pr->total_value >= $min && $pr->total_value <= $max) {
                    $level_info['applicable'] = true;
                } else {
                    $level_info['applicable'] = false;
                }
            } else {
                $level_info['applicable'] = true;
            }

            $progress['levels'][] = $level_info;
        }

        // Calculate percentage
        if ($progress['total_levels'] > 0) {
            $applicable_levels = array_filter($progress['levels'], function ($level) {
                return $level['applicable'];
            });

            $applicable_count = count($applicable_levels);
            $progress['percentage'] = $applicable_count > 0 ? ($progress['completed_levels'] / $applicable_count) * 100 : 0;
        }

        return $progress;
    }

    /**
     * Get PR timeline
     */
    public function get_pr_timeline($pr_id)
    {
        $timeline = [];
        $pr = $this->get_requisition_by_id($pr_id);

        if ($pr) {
            // Creation
            $timeline[] = [
                'date' => $pr->created_at,
                'event' => 'Created',
                'description' => 'Purchase Requisition created',
                'user' => $this->get_user_name($pr->created_by),
                'status' => 'Draft'
            ];

            // Submission
            if ($pr->submitted_for_approval) {
                $timeline[] = [
                    'date' => $pr->submitted_for_approval,
                    'event' => 'Submitted',
                    'description' => 'Submitted for approval',
                    'user' => $this->get_user_name($pr->created_by),
                    'status' => 'Submitted'
                ];
            }

            // Site Incharge Approval
            if ($pr->approved_date_site_incharge) {
                $timeline[] = [
                    'date' => $pr->approved_date_site_incharge,
                    'event' => 'Site Incharge Approved',
                    'description' => 'Approved by Site Incharge',
                    'user' => $this->get_user_name($pr->approved_by_site_incharge),
                    'status' => 'L1_Approved'
                ];
            }

            // Manager Approval
            if ($pr->approved_date_manager) {
                $timeline[] = [
                    'date' => $pr->approved_date_manager,
                    'event' => 'Manager Approved',
                    'description' => 'Approved by Manager',
                    'user' => $this->get_user_name($pr->approved_by_manager),
                    'status' => 'L2_Approved'
                ];
            }

            // Procurement Head Approval
            if ($pr->approved_date_procurement_head) {
                $timeline[] = [
                    'date' => $pr->approved_date_procurement_head,
                    'event' => 'Procurement Head Approved',
                    'description' => 'Approved by Procurement Head',
                    'user' => $this->get_user_name($pr->approved_by_procurement_head),
                    'status' => 'Approved'
                ];
            }

            // Rejection
            if ($pr->rejected_date) {
                $timeline[] = [
                    'date' => $pr->rejected_date,
                    'event' => 'Rejected',
                    'description' => $pr->rejection_reason ?: 'Rejected without reason',
                    'user' => $this->get_user_name($pr->rejected_by),
                    'status' => 'Rejected'
                ];
            }
        }

        // Add approval history
        $history = $this->get_approval_history($pr_id);
        foreach ($history as $record) {
            $timeline[] = [
                'date' => $record->action_date,
                'event' => $record->action,
                'description' => $record->comments ?: $record->action . ' at level ' . $record->approval_level,
                'user' => $record->approver_name ?: $record->approver_role,
                'status' => $record->action
            ];
        }

        // Sort by date
        usort($timeline, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $timeline;
    }

    /**
     * Get user name by ID
     */
    public function get_user_name($user_id)
    {
        if (!$user_id) return 'System';

        $this->db->select('username');
        $this->db->from('user');
        $this->db->where('user_id', $user_id);
        $result = $this->db->get()->row();

        return $result ? $result->username : 'Unknown User';
    }

    /**
     * Get PR dashboard statistics
     */
    public function get_dashboard_stats($user_id)
    {
        $stats = [];

        // Get user roles
        $user_roles = $this->get_user_roles($user_id);

        // Pending PRs for approval (where user has one of the current approver roles)
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

        // All PRs by workflow status
        $this->db->select('workflow_status, COUNT(*) as count');
        $this->db->from('purchase_requisition');
        $this->db->group_by('workflow_status');
        $workflow_stats = $this->db->get()->result();

        $status_counts = [
            'Draft' => 0,
            'Submitted' => 0,
            'L1_Pending' => 0,
            'L2_Pending' => 0,
            'L3_Pending' => 0,
            'Approved' => 0,
            'Rejected' => 0
        ];

        foreach ($workflow_stats as $stat) {
            $status_counts[$stat->workflow_status] = $stat->count;
        }

        $stats['workflow_counts'] = $status_counts;

        return $stats;
    }

    /**
     * Get users by role
     */
    public function get_users_by_role($role_name)
    {
        $this->db->select('u.userid, u.username, u.email');
        $this->db->from('user u');
        $this->db->join('user_roles ur', 'u.userid = ur.user_id');
        $this->db->where('ur.role_name', $role_name);
        $this->db->where('ur.is_active', 1);
        return $this->db->get()->result();
    }

    /**
     * Get PR workflow status
     */
    public function get_pr_workflow_status($pr_id)
    {
        $this->db->select('workflow_status, approval_level, current_approver_role, approval_status');
        $this->db->from('purchase_requisition');
        $this->db->where('pr_id', $pr_id);
        return $this->db->get()->row();
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
        if ($requisition->workflow_status != 'Draft') {
            return false;
        }

        return true;
    }

    /**
     * Get all PRs with filters
     */
    /**
     * Get all PRs with filters
     */
    public function get_all_requisitions($filters = [], $limit = null, $offset = null)
    {

        $location_id = $GLOBALS['location_id'] ?? null;
        $user_id = $GLOBALS['user_id'] ?? null;
        $role_name = $GLOBALS['role_name'] ?? null;


        // die();

        // Get user's role from user_roles table
        if ($role_name != "Admin") {
            $this->db->select('role_name');
            $this->db->from('user_roles');
            $this->db->where('user_id', $user_id);
            $this->db->where('location_id', $location_id);

            $this->db->where('is_active', 1);
            $user_roles_result = $this->db->get()->result();

            $user_roles = [];
            foreach ($user_roles_result as $role) {
                $user_roles[] = $role->role_name;
            }


            // var_dump($user_roles);


            // die();


            if (empty($user_roles)) {
                return [];
            }
        }


        $this->db->select(
            'pr.*, 
         d.department_name, 
         l.location_name, 
         u.username as requester_name,
         (SELECT pri.pr_no 
          FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
          WHERE pri.pr_id = pr.pr_id 
          LIMIT 1) as pr_no'
        );

        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');



        // ONLY FOR NON-ADMIN USERS: Filter by location
        if ($role_name != "Admin") {
            if ($location_id != null) {
                // Non-admin: Show only their location
                $this->db->where('pr.location_id_fk', $location_id);
            }
            $this->db->where_in('pr.current_approver_role', $user_roles);
        }
        // ADMIN USERS: No location filter - they see all data!

        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && empty($filters['date_from'])) {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('pr.created_at >=', $fy_from);
            $this->db->where('pr.created_at <=', $fy_to);
        }

        // Apply filters if provided
        if (!empty($filters)) {
            if (isset($filters['status'])) {
                $this->db->where('pr.status', $filters['status']);
            }

            if (isset($filters['department_id'])) {
                $this->db->where('pr.department_id_fk', $filters['department_id']);
            }

            if (isset($filters['location_id'])) {
                $this->db->where('pr.location_id_fk', $filters['location_id']);
            }

            if (isset($filters['date_from'])) {
                $this->db->where('pr.pr_date >=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $this->db->where('pr.pr_date <=', $filters['date_to']);
            }

            if (isset($filters['created_by'])) {
                $this->db->where('pr.created_by', $filters['created_by']);
            }

            if (isset($filters['search'])) {
                $this->db->group_start();
                $this->db->like('(SELECT pri.pr_no FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri WHERE pri.pr_id = pr.pr_id LIMIT 1)', $filters['search']);
                $this->db->or_like('pr.description', $filters['search']);
                $this->db->or_like('d.department_name', $filters['search']);
                $this->db->or_like('l.location_name', $filters['search']);
                $this->db->or_like('u.username', $filters['search']);
                $this->db->group_end();
            }
        }

        $this->db->order_by('pr.pr_date', 'DESC');
        $this->db->order_by('pr.pr_id', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        // For debugging, you can uncomment the line below to see the generated SQL
        // echo $this->db->last_query(); die();



        // var_dump($query->result());


        // die();

        return $query->result();
    }
    /**
     * Count all PRs with filters
     */
    public function count_all_requisitions($filters = [])
    {
        $this->db->from('purchase_requisition pr');

        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('pr.approval_status', $filters['status']);
        }

        if (!empty($filters['workflow_status'])) {
            $this->db->where('pr.workflow_status', $filters['workflow_status']);
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

        if (!empty($filters['created_by'])) {
            $this->db->where('pr.created_by', $filters['created_by']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get PRs by user (created by)
     */
    public function get_user_requisitions($user_id, $status = null, $limit = null, $offset = null)
    {
        // Use a subquery to get the pr_no from items table
        $this->db->select('pr.*, 
                     d.department_name,
                     (SELECT pri.pr_no 
                      FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
                      WHERE pri.pr_id = pr.pr_id 
                      LIMIT 1) as pr_no');

        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->where('pr.created_by', $user_id);

        if ($status) {
            $this->db->where('pr.approval_status', $status);
        }

        $this->db->order_by('pr.pr_date', 'DESC');
        $this->db->order_by('pr.pr_id', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Count user's PRs
     */
    public function count_user_requisitions($user_id, $status = null)
    {
        $this->db->where('created_by', $user_id);

        if ($status) {
            $this->db->where('approval_status', $status);
        }

        return $this->db->count_all_results('purchase_requisition');
    }

    /**
     * Get PR summary for dashboard
     */
    public function get_pr_summary($user_id = null)
    {
        $summary = [];

        // Total PRs
        $summary['total'] = $this->db->count_all('purchase_requisition');

        // By status
        $this->db->select('approval_status, COUNT(*) as count');
        $this->db->from('purchase_requisition');
        if ($user_id) {
            $this->db->where('created_by', $user_id);
        }
        $this->db->group_by('approval_status');
        $status_counts = $this->db->get()->result();

        foreach ($status_counts as $status) {
            $summary[strtolower($status->approval_status)] = $status->count;
        }

        // By workflow status
        $this->db->select('workflow_status, COUNT(*) as count');
        $this->db->from('purchase_requisition');
        if ($user_id) {
            $this->db->where('created_by', $user_id);
        }
        $this->db->group_by('workflow_status');
        $workflow_counts = $this->db->get()->result();

        foreach ($workflow_counts as $workflow) {
            $summary['wf_' . strtolower($workflow->workflow_status)] = $workflow->count;
        }

        return $summary;
    }


    /**
     * Get PR statistics by department
     */
    public function get_pr_stats_by_department($start_date = null, $end_date = null)
    {
        $this->db->select('d.department_name, 
                          COUNT(pr.pr_id) as total_prs,
                          SUM(CASE WHEN pr.approval_status = "Approved" THEN 1 ELSE 0 END) as approved,
                          SUM(CASE WHEN pr.approval_status = "Pending" THEN 1 ELSE 0 END) as pending,
                          SUM(CASE WHEN pr.approval_status = "Rejected" THEN 1 ELSE 0 END) as rejected,
                          SUM(pr.total_value) as total_value');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id');

        if ($start_date) {
            $this->db->where('pr.pr_date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('pr.pr_date <=', $end_date);
        }

        $this->db->group_by('d.department_id');
        $this->db->order_by('total_value', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Check if PR is in approval process
     */
    public function is_in_approval_process($pr_id)
    {
        $this->db->select('approval_status, workflow_status');
        $this->db->from('purchase_requisition');
        $this->db->where('pr_id', $pr_id);
        $pr = $this->db->get()->row();

        if (!$pr) {
            return false;
        }

        return $pr->approval_status == 'Pending' && $pr->workflow_status != 'Draft';
    }

    /**
     * Get PR notifications for user
     */
    public function get_pr_notifications($user_id)
    {
        // Get user roles
        $user_roles = $this->get_user_roles($user_id);

        if (empty($user_roles)) {
            return [];
        }

        // PRs pending user's approval
        $this->db->select('pr.pr_id, (SELECT pri.pr_no FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri WHERE pri.pr_id = pr.pr_id LIMIT 1) as pr_no, pr.total_value, pr.submitted_for_approval, u.username as requester_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('user u', 'pr.created_by = u.user_id');
        $this->db->where_in('pr.current_approver_role', $user_roles);
        $this->db->where('pr.approval_status', 'Pending');
        $this->db->where('pr.workflow_status !=', 'Draft');
        $this->db->order_by('pr.submitted_for_approval', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    /**
     * Update PR number in items table
     */
    public function update_pr_number($pr_id, $pr_no)
    {
        $this->db->where('pr_id', $pr_id);
        return $this->db->update('purchase_requisition_items', ['pr_no' => $pr_no]);
    }

    /**
     * Get PR by PR number
     */
    public function get_requisition_by_pr_no($pr_no)
    {
        $this->db->select('pr.*, d.department_name, u.username as requester_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        $this->db->join('purchase_requisition_items i', 'pr.pr_id = i.pr_id');
        $this->db->where('i.pr_no', $pr_no);
        $this->db->group_by('pr.pr_id');

        return $this->db->get()->row();
    }

    /**
     * Bulk update PR status
     */
    public function bulk_update_status($pr_ids, $status, $user_id = null)
    {
        $data = ['approval_status' => $status];

        if ($status == 'Rejected' && $user_id) {
            $data['rejected_by'] = $user_id;
            $data['rejected_date'] = date('Y-m-d H:i:s');
        }

        $this->db->where_in('pr_id', $pr_ids);
        return $this->db->update('purchase_requisition', $data);
    }

    /**
     * Get PR export data
     */
    public function get_export_data($filters = [], $user_id = null)
    {
        $this->db->select('pr.pr_id, pr.pr_date, d.department_name, pr.requested_by, 
                          pr.required_date, pr.urgency_level, pr.approval_status,
                          pr.workflow_status, pr.total_value, pr.remarks,
                          pr.project_code, pr.so_no, pr.oc_no,
                          u.username as created_by_name, pr.created_at,
                          pr.approved_by_site_incharge, pr.approved_date_site_incharge,
                          pr.approved_by_manager, pr.approved_date_manager,
                          pr.approved_by_procurement_head, pr.approved_date_procurement_head,
                          pr.rejected_by, pr.rejected_date, pr.rejection_reason');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $this->db->where('pr.pr_date >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $this->db->where('pr.pr_date <=', $filters['end_date']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('pr.approval_status', $filters['status']);
        }
        if (!empty($filters['department'])) {
            $this->db->where('pr.department_id_fk', $filters['department']);
        }

        // Removed user_id filter — all users see global company data
 


        $this->db->order_by('pr.pr_date', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Get month-year wise requisition records
     */
    /**
     * Get month-year wise requisition records
     */
    public function get_datewise_record($from_date, $to_date, $user_id = null)
    {
        $f_date = date('Y-m-d', strtotime($from_date));
        $t_date = date('Y-m-d', strtotime($to_date));

        $this->db->select('pri.*, pr.*, d.department_name, l.location_name, u.username as requested_by_name');
        $this->db->from('purchase_requisition_items pri');
        $this->db->join('purchase_requisition pr', 'pri.pr_id = pr.pr_id', 'left');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left');
        $this->db->join('user u', 'pr.requested_by = u.user_id', 'left');
        $this->db->where('pr.pr_date >=', $f_date);
        $this->db->where('pr.pr_date <=', $t_date);
        $this->db->order_by('pr.pr_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_monthyearwise_record($month_year, $user_id = null)
    {


        $this->db->select('pri.*, pr.*, d.department_name, l.location_name, u.username as requested_by_name');
        $this->db->from('purchase_requisition_items pri');
        $this->db->join('purchase_requisition pr', 'pri.pr_id = pr.pr_id', 'left');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left');
        $this->db->join('user u', 'pr.requested_by = u.user_id', 'left');

        // Handle different month formats
        if (strlen($month_year) > 8) {
            // Format: "January-2026" (full month name)
            $month_str = substr($month_year, 0, 3); // Take first 3 letters
            $year_str = substr($month_year, -4); // Take last 4 digits

            // Convert to abbreviated month
            $month_num = date('m', strtotime($month_str));
        } else {
            // Format: "Jan-2026" (already abbreviated)
            $month_str = substr($month_year, 0, 3);
            $year_str = substr($month_year, 4, 4);
            $month_num = date('m', strtotime($month_str . " 1 2023"));
        }

        // Validate the parsed values
        if (!is_numeric($year_str) || $year_str < 2000 || $year_str > 2100) {
            // Invalid year, return empty result or current month
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        } else {
            // Create date range for the month
            $start_date = $year_str . '-' . $month_num . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
        }

        // Filter by date range
        $this->db->where('pr.pr_date >=', $start_date);
        $this->db->where('pr.pr_date <=', $end_date);




        // Removed user_id filter — all users see global company data
 

        $this->db->order_by('pr.pr_date', 'DESC');
        $this->db->order_by('pri.pr_no', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get all purchase requisitions for a user
     */
    public function get_purchase_requisition($user_id = null)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('pr.created_at >=', $fy_from);
            $this->db->where('pr.created_at <=', $fy_to);
        }

        $this->db->select('pri.*, pr.*, d.department_name, u.username as requested_by_name');
        $this->db->from('purchase_requisition_items pri');
        $this->db->join('purchase_requisition pr', 'pri.pr_id = pr.pr_id', 'left');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('user u', 'pr.requested_by = u.user_id', 'left');

        $this->db->order_by('pr.pr_date', 'DESC');
        $this->db->order_by('pri.pr_no', 'DESC');

        return $this->db->get()->result();
    }

    public function delete_requisition_items($pr_id)
    {
        $this->db->where('pr_id', $pr_id);
        return $this->db->delete('purchase_requisition_items');
    }


    // Add this method to your Requisition model (application/models/Requisition.php)

    /**
     * Get requisition items by item IDs
     * This method is used by RFQController to convert selected items to RFQ
     */
    public function get_requisition_items_by_item_ids($item_ids)
    {
        if (empty($item_ids)) {
            return [];
        }

        // First, let's check if the column exists by running a simpler query
        $this->db->select('
        pri.item_id,
        pri.pr_id,
        pri.item_code,
        pri.description,
        pri.specification,
        pri.quantity,
        pri.unit,
        pri.estimated_cost,
        pri.hsn,
        pri.pr_no,
        pr.department_id_fk,
        pr.requested_by,
        pr.urgency_level,
        pr.pr_date,
        pr.required_date,
        pr.remarks,
        pr.created_by as pr_created_by,
        pr.approval_status,
        pr.workflow_status as pr_workflow_status,  
        d.department_name,
        u.username as requested_by_name
    ');
        $this->db->from('purchase_requisition_items pri');
        $this->db->join('purchase_requisition pr', 'pri.pr_id = pr.pr_id', 'left');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('user u', 'pr.requested_by = u.user_id', 'left');  // Changed from 'user' to 'user'
        $this->db->where_in('pri.item_id', $item_ids);

        // Debug: Show the SQL query
        // echo $this->db->get_compiled_select(); die();

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get requisition items by PR ID for conversion to RFQ
     */
    public function get_items_for_rfq_conversion($pr_id)
    {
        $this->db->select('
        pri.*,
        pr.department_id_fk,
        pr.requested_by,
        pr.urgency_level,
        pr.pr_date,
        pr.required_date,
        pr.remarks,
        pr.created_by as pr_created_by,
        pr.approval_status,
        pr.workflow_status,
        d.department_name,
        u.username as requested_by_name
    ');
        $this->db->from('purchase_requisition_items pri');
        $this->db->join('purchase_requisition pr', 'pri.pr_id = pr.pr_id', 'left');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('user u', 'pr.requested_by = u.user_id', 'left');
        $this->db->where('pri.pr_id', $pr_id);

        // Only include approved PR items
        $this->db->where('pr.approval_status', 'Approved');
        $this->db->where('pr.workflow_status', 'Approved');

        $this->db->order_by('pri.item_id', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Check if items are approved for RFQ conversion
     */
    public function are_items_approved_for_rfq($item_ids)
    {
        if (empty($item_ids)) {
            return false;
        }

        $this->db->select('COUNT(*) as approved_count');
        $this->db->from('purchase_requisition_items pri');
        $this->db->join('purchase_requisition pr', 'pri.pr_id = pr.pr_id', 'left');
        $this->db->where_in('pri.item_id', $item_ids);
        $this->db->where('pr.approval_status', 'Approved');
        $this->db->where('pr.workflow_status', 'Approved');

        $query = $this->db->get();
        $result = $query->row();

        return ($result && $result->approved_count == count($item_ids));
    }


    /**
     * Get applicable approval levels based on total value
     * This replaces the need for get_applicable_approval_levels()
     */
    public function get_applicable_approval_levels($total_value = null)
    {
        // If you want to filter by total_value, use this:
        if ($total_value !== null) {
            return $this->get_approval_chain($total_value);
        }

        // Otherwise return all PR approval matrix entries
        return $this->get_pr_approval_matrix();
    }

    /**
     * Alternative: Get applicable levels for display
     */
    public function get_applicable_levels_for_pr($pr_id)
    {
        $pr = $this->get_requisition_by_id($pr_id);
        $total_value = $pr ? $pr->total_value : 0;

        return $this->get_approval_chain($total_value);
    }


    /**
     * Get PR approval chain
     */
    public function get_approval_chain($total_value)
    {
        // Cast to float to ensure it's a number
        $total_value_float = (float)$total_value;

        $this->db->where('document_type', 'PR');
        $this->db->where('status', 'active');

        // Use proper SQL parameter binding
        $this->db->group_start();
        $this->db->where("min_amount <= $total_value_float");
        $this->db->or_where('min_amount', 0);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where("max_amount >= $total_value_float");
        $this->db->or_where('max_amount', 0);
        $this->db->group_end();

        $this->db->order_by('level', 'ASC');

        return $this->db->get('approval_matrix')->result();
    }

    public function get_recent_prs($limit = 10)
    {
        $this->db->select('pr.*, 
                     d.department_name, 
                     u.username as requester_name,
                     (SELECT pri.pr_no 
                      FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
                      WHERE pri.pr_id = pr.pr_id 
                      LIMIT 1) as pr_no');  // Add this subquery

        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');
        $this->db->order_by('pr.created_at', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }



    /**
     * Get PRs for approval history selection with optional filters
     */
    /**
     * Get PRs for approval history selection with optional filters
     */
    public function get_prs_for_history($user_id, $filters = [])
    {


        $user_roles = $this->get_user_roles($user_id);

        // Check if user is Admin
        $is_admin = false;
        $role_name = $GLOBALS['role_name'] ?? null;

        $location_id = $GLOBALS['location_id'] ?? null;


        // die();


        if ($role_name === 'Admin') {
            $is_admin = true;
        } elseif (isset($filters['role_name']) && $filters['role_name'] === 'Admin') {
            $is_admin = true;
        }



        // echo $is_admin;

        // die();

        $this->db->select('pr.*, 
        d.department_name, 
        u.username as requester_name, l.location_name, 
        (SELECT pri.pr_no 
         FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
         WHERE pri.pr_id = pr.pr_id 
         LIMIT 1) as pr_no');

        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left');

        $this->db->join('user u', 'pr.created_by = u.user_id', 'left');

        // Removed location/created_by restriction — all users see global company data
 

        // Apply optional filters
        if (!empty($filters['status'])) {
            $this->db->where('pr.approval_status', $filters['status']);
        }

        if (!empty($filters['workflow_status'])) {
            $this->db->where('pr.workflow_status', $filters['workflow_status']);
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

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('pr.pr_title', $filters['search']);
            $this->db->or_like('u.username', $filters['search']);
            $this->db->or_like('d.department_name', $filters['search']);
            $this->db->or_like('(SELECT pri.pr_no 
                              FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
                              WHERE pri.pr_id = pr.pr_id 
                              LIMIT 1)', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('pr.created_at', 'DESC');

        // Execute ONCE
        $query = $this->db->get();



        // var_dump($query->result());


        // die();
        return $query->result();
    }

    /**
     * Get monthly PR statistics
     */
    public function get_monthly_stats($start_date = null, $end_date = null)
    {
        $this->db->select("
        DATE_FORMAT(pr_date, '%Y-%m') as month_year,
        COUNT(*) as total_prs,
        SUM(CASE WHEN approval_status = 'Approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN approval_status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN approval_status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
        SUM(total_value) as total_value,
        AVG(total_value) as avg_value
    ");

        $this->db->from('purchase_requisition');

        if ($start_date) {
            $this->db->where('pr_date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('pr_date <=', $end_date);
        }

        $this->db->group_by("DATE_FORMAT(pr_date, '%Y-%m')");
        $this->db->order_by('month_year', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get approval metrics
     */
    public function get_approval_metrics($start_date = null, $end_date = null)
    {
        // Average approval time
        $this->db->select("
        AVG(TIMESTAMPDIFF(HOUR, submitted_for_approval, COALESCE(approved_date_procurement_head, rejected_date, NOW()))) as avg_approval_time_hours,
        AVG(TIMESTAMPDIFF(DAY, submitted_for_approval, COALESCE(approved_date_procurement_head, rejected_date, NOW()))) as avg_approval_time_days,
        COUNT(*) as total_prs,
        SUM(CASE WHEN approval_status = 'Approved' THEN 1 ELSE 0 END) as approved_count,
        SUM(CASE WHEN approval_status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN approval_status = 'Rejected' THEN 1 ELSE 0 END) as rejected_count,
        (SUM(CASE WHEN approval_status = 'Approved' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as approval_rate
    ");

        $this->db->from('purchase_requisition');
        $this->db->where('workflow_status !=', 'Draft');

        if ($start_date) {
            $this->db->where('pr_date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('pr_date <=', $end_date);
        }

        return $this->db->get()->row();
    }

    /**
     * Get approval timeline analysis
     */
    public function get_approval_timeline_analysis($start_date = null, $end_date = null)
    {
        $this->db->select("
        pr.pr_id,
        pr.pr_no,
        pr.total_value,
        pr.submitted_for_approval,
        pr.approved_date_site_incharge,
        pr.approved_date_manager,
        pr.approved_date_procurement_head,
        pr.rejected_date,
        TIMESTAMPDIFF(HOUR, pr.submitted_for_approval, pr.approved_date_site_incharge) as level1_time,
        TIMESTAMPDIFF(HOUR, pr.approved_date_site_incharge, pr.approved_date_manager) as level2_time,
        TIMESTAMPDIFF(HOUR, pr.approved_date_manager, pr.approved_date_procurement_head) as level3_time,
        TIMESTAMPDIFF(HOUR, pr.submitted_for_approval, COALESCE(pr.approved_date_procurement_head, pr.rejected_date, NOW())) as total_time,
        pr.approval_status
    ");

        $this->db->from('purchase_requisition pr');
        $this->db->where('pr.workflow_status !=', 'Draft');

        if ($start_date) {
            $this->db->where('pr.pr_date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('pr.pr_date <=', $end_date);
        }

        $this->db->order_by('pr.submitted_for_approval', 'DESC');
        $this->db->limit(100);

        return $this->db->get()->result();
    }
}
