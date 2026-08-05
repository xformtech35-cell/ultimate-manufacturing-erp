<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LogHistoryController extends MY_Controller
{
    protected $user_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? null;
        $user_role_id = $session_data_head['result']['role_id_fk'] ?? $session_data_head['result']['role_id'] ?? null;

        if (empty($this->user_id)) {
            $this->session->sess_destroy();
            redirect('LoginController/logout');
            exit;
        }

        // Auto-register the Log History module menu in sidebar_menu table if not exists
        if ($this->db->table_exists('sidebar_menu')) {
            $has_menu = $this->db->where('permission', 'Log_History')->get('sidebar_menu')->row();
            if (!$has_menu) {
                $this->db->insert('sidebar_menu', array(
                    'title' => 'Log History',
                    'icon' => 'fa fa-history',
                    'url' => 'LogHistoryController/index',
                    'permission' => 'Log_History',
                    'parent_id' => NULL,
                    'sort_order' => 90,
                    'active_cond' => json_encode(array('currentPage' => 'LogHistoryController'))
                ));
            }

            // Auto-grant Log_History permission to Admin role (Role ID 1) if not exists
            if ($this->db->table_exists('permission')) {
                $admin_perm = $this->db->where('role_id_fk', 1)->where('grp_perm', 'Log_History')->get('permission')->row();
                if (!$admin_perm) {
                    $this->db->insert('permission', array(
                        'role_id_fk' => 1,
                        'grp_perm' => 'Log_History'
                    ));
                }
            }
        }

        // Check if the current user has the Log_History permission
        $has_permission = false;
        if ($user_role_id == 1) {
            $has_permission = true; // Admin has full access
        } else {
            $perms = $this->db->select('grp_perm')->where('role_id_fk', $user_role_id)->get('permission')->result_array();
            foreach ($perms as $p) {
                if ($p['grp_perm'] === 'Log_History') {
                    $has_permission = true;
                    break;
                }
            }
        }

        if (!$has_permission) {
            show_error('You do not have permission to access this page.', 403);
            exit;
        }
    }

    public function index()
    {
        // 1. AI Governance Logs
        $data['ai_logs'] = $this->db->select('g.*, u.username, b.number_fk as bom_number')
                                    ->from('ai_governance_log g')
                                    ->join('user u', 'u.user_id = g.user_id', 'left')
                                    ->join('bom_total b', 'b.id = g.record_id', 'left')
                                    ->order_by('g.id', 'DESC')
                                    ->get()
                                    ->result_array();

        // 2. Requisition (PR) Approval History
        $data['pr_logs'] = $this->db->select('h.*, pr.created_by')
                                    ->from('pr_approval_history h')
                                    ->join('purchase_requisition pr', 'pr.pr_id = h.pr_id', 'left')
                                    ->order_by('h.history_id', 'DESC')
                                    ->get()
                                    ->result_array();

        // 3. GRN Inspection Logs
        $data['grn_logs'] = $this->db->select('l.*, u.username as inspector_name')
                                     ->from('grn_inspection_log l')
                                     ->join('user u', 'u.user_id = l.inspected_by', 'left')
                                     ->order_by('l.inspection_id', 'DESC')
                                     ->get()
                                     ->result_array();

        // 4. PO Email Logs
        $data['po_email_logs'] = $this->db->select('l.*, u.username as sender_name')
                                          ->from('po_email_logs l')
                                          ->join('user u', 'u.user_id = l.sent_by', 'left')
                                          ->order_by('l.log_id', 'DESC')
                                          ->get()
                                          ->result_array();

        // 5. General Activity Logs (audit_trail)
        // --- Filter parameters ---
        $filter_user_id     = $this->input->get('filter_user_id');
        $filter_module      = $this->input->get('filter_module');
        $filter_action_type = $this->input->get('filter_action_type');
        $filter_date_from   = $this->input->get('filter_date_from');
        $filter_date_to     = $this->input->get('filter_date_to');
        $filter_keyword     = $this->input->get('filter_keyword');

        $this->db->select('a.*, u.username as operator_name')
                 ->from('audit_trail a')
                 ->join('user u', 'u.user_id = a.user_id', 'left');

        if (!empty($filter_user_id)) {
            $this->db->where('a.user_id', (int)$filter_user_id);
        }
        if (!empty($filter_module)) {
            // filter_module is stored as the controller class name (e.g. SalesOrderController)
            $this->db->where('a.table_name', $filter_module);
        }
        if (!empty($filter_action_type)) {
            $action_map = [
                'create'     => 'Created',
                'update'     => 'Updated',
                'delete'     => 'Deleted',
                'login'      => 'User logged',
                'approval'   => 'Approved',
                'grn'        => 'GRN',
                'inspection' => 'Inspection',
            ];
            $keyword = $action_map[$filter_action_type] ?? $filter_action_type;
            $this->db->like('a.action', $keyword, 'after');
        }
        if (!empty($filter_date_from)) {
            $this->db->where('DATE(a.created_at) >=', $filter_date_from);
        }
        if (!empty($filter_date_to)) {
            $this->db->where('DATE(a.created_at) <=', $filter_date_to);
        }
        if (!empty($filter_keyword)) {
            $this->db->like('a.action', $filter_keyword);
        }

        $this->db->order_by('a.audit_id', 'DESC');
        $data['activity_logs'] = $this->db->get()->result_array();

        // ---------------------------------------------------------------
        // All Users — from the full user table (ALL logins, not just those who have activity)
        // ---------------------------------------------------------------
        $data['all_users'] = $this->db
            ->select('user_id, username')
            ->from($orig_prefix . 'user')
            ->order_by('username', 'ASC')
            ->get()->result_array();

        // ---------------------------------------------------------------
        // All Modules — from sidebar_menu (top-level/parent menu entries that
        // have a URL pointing to a real controller).  We extract the controller
        // class name from the URL (first segment before '/') and pair it with
        // the human-readable title so the dropdown shows "Sales Order" but
        // filters on "SalesOrderController" (which is what MY_Controller stores
        // in the table_name column of audit_trail).
        // ---------------------------------------------------------------
        $sidebar_rows = $this->db
            ->select('id, title, url, permission')
            ->from($orig_prefix . 'sidebar_menu')
            ->where('url IS NOT NULL', NULL, FALSE)
            ->where("url != ''", NULL, FALSE)
            ->order_by('title', 'ASC')
            ->get()->result_array();

        $sidebar_modules = [];
        $seen_controllers = [];
        foreach ($sidebar_rows as $row) {
            $url = trim($row['url']);
            if (empty($url)) continue;
            // Extract controller name: first segment of the URL
            $parts = explode('/', $url);
            $controller_class = $parts[0]; // e.g. SalesOrderController
            if (empty($controller_class)) continue;
            if (isset($seen_controllers[$controller_class])) continue; // deduplicate
            $seen_controllers[$controller_class] = true;
            $sidebar_modules[] = [
                'controller' => $controller_class,
                'title'      => $row['title'],
                'permission' => $row['permission'],
            ];
        }
        // Sort by title
        usort($sidebar_modules, function($a, $b) {
            return strcmp($a['title'], $b['title']);
        });
        $data['all_modules'] = $sidebar_modules;

        // Pass back the current filter values so the view can pre-populate them
        $data['filter_user_id']     = $filter_user_id;
        $data['filter_module']      = $filter_module;
        $data['filter_action_type'] = $filter_action_type;
        $data['filter_date_from']   = $filter_date_from;
        $data['filter_date_to']     = $filter_date_to;
        $data['filter_keyword']     = $filter_keyword;

        $this->load->view('admin/header_side_bar');
        $this->load->view('log_history/index', $data);
        $this->load->view('admin/footer');
    }
}
