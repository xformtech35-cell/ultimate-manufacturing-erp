<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Mpdf\Mpdf;


class RequisitionController extends MY_Controller
{
    protected $user_id;

    function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('requisition');
        $this->load->model('department');
        $this->load->model('inventory', '', TRUE);
        $this->load->model('user', '', TRUE);
        $this->load->model('login', '', TRUE);
        $this->load->model('LocationModel');
        $this->load->library('form_validation');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
        $this->location_id = $session_data_head['result']['location_id'] ?? NULL;
        $this->role_name = $session_data_head['result']['role_name'] ?? NULL;
        $GLOBALS['user_id'] = $session_data_head['result']['user_id'] ?? NULL; // Keep real user ID for matrix

        $GLOBALS['location_id'] = $this->location_id;
        $GLOBALS['role_name'] = $this->role_name;


        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    // ===========================================
    // APPROVAL WORKFLOW METHODS
    // ===========================================

    /**
     * Submit PR for approval
     */
    public function submit_for_approval($pr_id)
    {
        $real_user_id = $this->session->userdata('session_data_head')['result']['user_id'] ?? $this->user_id;
        if (!$real_user_id) {
            $this->session->set_flashdata('ERRORMSG', 'User not authenticated');
            redirect('RequisitionController/view_requisition_order');
        }

        // Check if user can submit this PR
        if (!$this->requisition->can_edit_requisition($pr_id, $real_user_id)) {
            $this->session->set_flashdata('ERRORMSG', 'You cannot submit this requisition for approval');
            redirect('RequisitionController/view_requisition_order');
        }

        $result = $this->requisition->submit_for_approval($pr_id, $real_user_id);

        if ($result) {
            $this->session->set_flashdata('SUCCESSMSG', 'Requisition submitted for approval successfully!');
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Failed to submit requisition for approval');
        }

        redirect('RequisitionController/view_requisition_order');
    }

    /**
     * Show approval dashboard
     */
    public function approval_dashboard()
    {
        $real_user_id = $this->session->userdata('session_data_head')['result']['user_id'] ?? $this->user_id;
        $data['pending_approvals'] = $this->requisition->get_pending_approvals();
        $data['stats'] = $this->requisition->get_dashboard_stats($real_user_id);
        $data['recent_prs'] = $this->requisition->get_recent_prs(10);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/approval_dashboard', $data);
    }

    /**
     * View requisition for approval
     */
    public function view_for_approval($pr_id = NULL)
    {
        if ($pr_id === NULL) {
            $this->session->set_flashdata('ERRORMSG', 'Requisition ID is missing.');
            redirect('RequisitionController/approval_dashboard');
            return;
        }

        $real_user_id = $this->session->userdata('session_data_head')['result']['user_id'] ?? $this->user_id;
        // Check if user can approve this PR
        if (!$this->requisition->can_user_approve($pr_id, $real_user_id)) {
            $this->session->set_flashdata('ERRORMSG', 'You are not authorized to approve this requisition');
            redirect('RequisitionController/approval_dashboard');
        }

        $data['requisition'] = $this->requisition->get_requisition_with_details($pr_id);
        $data['requisition_items'] = $this->requisition->get_requisition_items($pr_id);
        $data['approval_history'] = $this->requisition->get_approval_history($pr_id);
        $data['approval_progress'] = $this->requisition->get_approval_progress($pr_id);
        $data['timeline'] = $this->requisition->get_pr_timeline($pr_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/view_for_approval', $data);
    }

    /**
     * Process approval action
     */
    public function process_approval()
    {
        $pr_id = $this->input->post('pr_id');
        $action = $this->input->post('action');
        $comments = $this->input->post('comments');
        $real_user_id = $this->session->userdata('session_data_head')['result']['user_id'] ?? $this->user_id;

        // Validate action
        if (!in_array($action, ['Approved', 'Rejected', 'Returned'])) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid action');
            redirect('RequisitionController/approval_dashboard');
        }

        // Check if user can approve this PR
        if (!$this->requisition->can_user_approve($pr_id, $real_user_id)) {
            $this->session->set_flashdata('ERRORMSG', 'You are not authorized to perform this action');
            redirect('RequisitionController/approval_dashboard');
        }

        // Get user's role for this PR
        $user_roles = $this->requisition->get_user_roles($real_user_id);
        $pr = $this->requisition->get_requisition_by_id($pr_id);
        $user_role = $pr->current_approver_role;

        // Process approval
        $result = $this->requisition->process_approval($pr_id, $real_user_id, $user_role, $action, $comments);

        if ($result) {
            $action_message = ($action == 'Approved') ? 'approved' : (($action == 'Rejected') ? 'rejected' : 'returned for revision');
            $this->session->set_flashdata('SUCCESSMSG', "Requisition $action_message successfully!");

            // Send notification/email here if needed
        } else {
            $this->session->set_flashdata('ERRORMSG', "Failed to process $action");
        }

        redirect('RequisitionController/approval_dashboard');
    }

    /**
     * Get approval statistics
     */
    public function get_approval_stats()
    {
        $stats = $this->requisition->get_dashboard_stats($this->user_id);
        echo json_encode($stats);
    }

    /**
     * View approval history
     */
    public function view_approval_history($pr_id = NULL)
    {
        if ($pr_id === NULL) {
            $this->session->set_flashdata('ERRORMSG', 'Requisition ID is missing.');
            redirect('RequisitionController/list_approval_history');
            return;
        }

        $data['requisition'] = $this->requisition->get_requisition_with_details($pr_id);
        $data['approval_history'] = $this->requisition->get_approval_history($pr_id);
        $data['timeline'] = $this->requisition->get_pr_timeline($pr_id);
        $data['approval_progress'] = $this->requisition->get_approval_progress($pr_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/approval_history', $data);
    }

    /**
     * View my requisitions
     */
    public function my_requisitions()
    {
        $status = $this->input->get('status');
        $real_user_id = $this->session->userdata('session_data_head')['result']['user_id'] ?? $this->user_id;
        $data['requisitions'] = $this->requisition->get_user_requisitions($real_user_id, $status);
        $data['stats'] = $this->requisition->get_pr_summary($real_user_id);


        // echo "test";
        //         die();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/my_requisitions', $data);
    }

    /**
     * View all requisitions with filters
     */
    public function all_requisitions()
    {
        $filters = [
            'status' => $this->input->get('status'),
            'workflow_status' => $this->input->get('workflow_status'),
            'department' => $this->input->get('department'),
            'start_date' => $this->input->get('start_date'),
            'end_date' => $this->input->get('end_date'),
            'urgency' => $this->input->get('urgency'),
            'search' => $this->input->get('search')
        ];

        // Clean filters - remove empty values
        foreach ($filters as $key => $value) {
            if ($value === '' || $value === null) {
                unset($filters[$key]);
            }
        }

        // Pagination
        $limit = 20;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $data['requisitions'] = $this->requisition->get_all_requisitions($filters, $limit, $offset);
        $data['total_count'] = $this->requisition->count_all_requisitions($filters);
        $data['departments'] = $this->department->get_departments();
        $data['filters'] = $filters;

        // Fix: Ensure total_count is a valid integer
        $total_count = (int)$data['total_count'];
        if ($total_count < 0) {
            $total_count = 0;
        }
        $data['total_count'] = $total_count;

        // Calculate pagination data
        $data['current_page'] = $page;
        $data['total_pages'] = $total_count > 0 ? round($total_count / $limit) : 1;
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        $data['start_item'] = $offset + 1;
        $data['end_item'] = min($offset + $limit, $total_count);

        // Build custom pagination
        $data['pagination'] = $this->build_custom_pagination($filters, $page, $data['total_pages'], $total_count, $limit);

        // Add user_id to session_data_head
        if (isset($this->session_data_head)) {
            $this->session_data_head['user_id'] = $this->user_id;
            $data['session_data_head'] = $this->session_data_head;
        }


// echo "ssssssssssss";



//         var_dump($data);


//         die();

     //   die();

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('requisition/all_requisitions', $data);
    }

    /**
     * Build custom pagination HTML
     */
    private function build_custom_pagination($filters, $current_page, $total_pages, $total_count, $limit = 20)
    {
        if ($total_pages <= 1 || $total_count == 0) {
            return '<div class="text-center" style="margin-top: 20px;">
                    Showing 0 to 0 of 0 entries
                </div>';
        }

        $base_url = base_url('RequisitionController/all_requisitions');

        // Build query string from filters
        $query_params = $filters;
        $query_string = '';
        if (!empty($query_params)) {
            $query_string = '?' . http_build_query($query_params);
        }

        $html = '<div class="row">';
        $html .= '<div class="col-sm-6">';
        $html .= '<div class="dataTables_info" style="padding-top: 8px;">';
        $start_item = (($current_page - 1) * $limit) + 1;
        $end_item = min($current_page * $limit, $total_count);
        $html .= 'Showing ' . $start_item . ' to ' . $end_item . ' of ' . $total_count . ' entries';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-sm-6">';
        $html .= '<div class="dataTables_paginate paging_simple_numbers">';
        $html .= '<ul class="pagination pagination-sm no-margin pull-right">';

        // Previous link
        if ($current_page > 1) {
            $prev_url = $base_url . ($query_string ? $query_string . '&' : '?') . 'page=' . ($current_page - 1);
            $html .= '<li class="paginate_button previous"><a href="' . $prev_url . '">Previous</a></li>';
        } else {
            $html .= '<li class="paginate_button previous disabled"><span>Previous</span></li>';
        }

        // Calculate page range to show
        $start_page = max(1, $current_page - 2);
        $end_page = min($total_pages, $current_page + 2);

        // Show first page if not in range
        if ($start_page > 1) {
            $first_url = $base_url . ($query_string ? $query_string . '&' : '?') . 'page=1';
            $html .= '<li class="paginate_button"><a href="' . $first_url . '">1</a></li>';
            if ($start_page > 2) {
                $html .= '<li class="paginate_button disabled"><span>...</span></li>';
            }
        }

        // Page numbers
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                $html .= '<li class="paginate_button active"><span>' . $i . '</span></li>';
            } else {
                $page_url = $base_url . ($query_string ? $query_string . '&' : '?') . 'page=' . $i;
                $html .= '<li class="paginate_button"><a href="' . $page_url . '">' . $i . '</a></li>';
            }
        }

        // Show last page if not in range
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                $html .= '<li class="paginate_button disabled"><span>...</span></li>';
            }
            $last_url = $base_url . ($query_string ? $query_string . '&' : '?') . 'page=' . $total_pages;
            $html .= '<li class="paginate_button"><a href="' . $last_url . '">' . $total_pages . '</a></li>';
        }

        // Next link
        if ($current_page < $total_pages) {
            $next_url = $base_url . ($query_string ? $query_string . '&' : '?') . 'page=' . ($current_page + 1);
            $html .= '<li class="paginate_button next"><a href="' . $next_url . '">Next</a></li>';
        } else {
            $html .= '<li class="paginate_button next disabled"><span>Next</span></li>';
        }

        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Get notifications (AJAX)
     */
    public function get_notifications()
    {
        $notifications = $this->requisition->get_pr_notifications($this->user_id);
        echo json_encode($notifications);
    }

    /**
     * Export requisitions
     */
    public function export_requisitions()
    {
        $filters = [
            'start_date' => $this->input->get('start_date'),
            'end_date' => $this->input->get('end_date'),
            'status' => $this->input->get('status'),
            'department' => $this->input->get('department')
        ];

        $data = $this->requisition->get_export_data($filters, $this->user_id);

        // Export as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="requisitions_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        // Headers
        fputcsv($output, [
            'PR ID',
            'PR Date',
            'Department',
            'Requested By',
            'Required Date',
            'Urgency Level',
            'Approval Status',
            'Workflow Status',
            'Total Value',
            'Remarks',
            'Created By',
            'Created At',
            'Site Incharge Approved By',
            'Site Incharge Approved Date',
            'Manager Approved By',
            'Manager Approved Date',
            'Procurement Head Approved By',
            'Procurement Head Approved Date',
            'Rejected By',
            'Rejected Date',
            'Rejection Reason'
        ]);

        // Data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    }

    // ===========================================
    // EXISTING METHODS (Keep as is with minor fixes)
    // ===========================================

    public function view_requisition_order()
    {
        // Check if month filter is submitted via POST
        if ($this->input->post('month_year')) {
            $month_year = $this->input->post('month_year');
            $data['purchase_requisition'] = $this->requisition->get_monthyearwise_record($month_year, $this->user_id);
            $data['selected_month'] = $month_year; // To show selected month in view
        }
        // Check if "All" is requested via GET
        elseif ($this->input->get('str') == "All") {
            $data['purchase_requisition'] = $this->requisition->get_purchase_requisition($this->user_id);
            $data['selected_month'] = 'All';
        }
        // Default: show all records on load
        else {
            $data['purchase_requisition'] = $this->requisition->get_purchase_requisition($this->user_id);
            $data['selected_month'] = 'All';
        }



        // echo "sss";

        // die();

        $data['department_result'] = $this->department->get_departments();
        $role = "user";
        $data['result'] = $this->user->get_user($role);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/view_requisition_order', $data);
    }
    public function create_purchase_requisition()
    {
        $data['department_result'] = $this->department->get_departments();
        $data['location_result'] = $this->LocationModel->get_locations();
        $data['pr_id'] = $this->requisition->get_last_pr_number($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['users'] = $this->user->get_user_without_role();
        $data['projects'] = $this->db->get('project')->result_array();
        $data['sales_orders'] = $this->db->select('number_fk, oc_number, project_code')
                                         ->from('salesorder_total')
                                         ->get()
                                         ->result_array();

        $selected_item_code = '';
        if ($this->input->get('item')) {
            $item_id = $this->input->get('item');
            $selected_item = $this->db->select('code')->where('inventory_id', $item_id)->get('inventory')->row();
            if ($selected_item) {
                $selected_item_code = $selected_item->code;
            }
        }
        $data['selected_item_code'] = $selected_item_code;

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/create_requisition_order', $data);
    }

    public function add_requisition()
    {
        // Basic info
        $department_id = $this->input->post('department_id_fk');
        $requested_by = $this->input->post('requested_by');
        $urgency_level = $this->input->post('urgency_level');
        $pr_date = date("Y-m-d", strtotime($this->input->post('pr_date')));
        $required_date = date("Y-m-d", strtotime($this->input->post('required_date')));
        $approval_status = 'Pending'; // Default to Pending
        $remarks = $this->input->post('remarks');
        $project_code = $this->input->post('project_code') ?? '';
        $so_no = $this->input->post('so_no');
        $oc_no = $this->input->post('oc_no');

        // Table data
        $items = $this->input->post('item_code');
        $descriptions = $this->input->post('description');
        $hsn_codes = $this->input->post('hsn');
        $quantities = $this->input->post('quantity');
        $units = $this->input->post('unit');
        $estimated_costs = $this->input->post('estimated_cost');
        $specifications = $this->input->post('specification');
        $location_id = $this->input->post('location_id_fk'); // Add this line

        // If oc_no was not submitted but so_no was, look it up
        if (empty($oc_no) && !empty($so_no)) {
            $so_row = $this->db->select('oc_number')
                               ->where('number_fk', $so_no)
                               ->get('salesorder_total')
                               ->row_array();
            if ($so_row) {
                $oc_no = $so_row['oc_number'];
            }
        }

        // var_dump($estimated_costs);
        // die();

        // Insert master requisition with workflow status
        $master_data = [
            'department_id_fk' => $department_id,
            'requested_by' => $requested_by,
            'urgency_level' => $urgency_level,
            'pr_date' => $pr_date,
            'required_date' => $required_date,
            'approval_status' => $approval_status,
            // 'workflow_status' => 'Draft',
            'submitted_for_approval' => date("Y-m-d H:i:s"),
            'current_approver_role' => 'Purchase Manager',
            'workflow_status' => 'L1_Pending',
            'remarks' => $remarks,
            'project_code' => $project_code,
            'so_no' => $so_no,
            'oc_no' => $oc_no,
            'location_id_fk' => $location_id, // Add this line
            'created_by' => $this->user_id,
            'created_at' => date("Y-m-d H:i:s"),

        ];






        // die();

        $pr_id = $this->requisition->insert_requisition($master_data);

        if ($pr_id) {
            // Get last PR number and determine financial year
            $last_pr_no = $this->requisition->get_last_pr_number($this->user_id);

            if (date('m') <= 3) {
                $financial_year = (date('y') - 1) . '-' . date('y');
            } else {
                $financial_year = date('y') . '-' . (date('y') + 1);
            }

            // Prepare item data for batch insert
            $item_data = [];
            for ($i = 0; $i < count($items); $i++) {
                if (!empty($items[$i])) {
                    $last_pr_no++;
                    $pr_no = "PR/" . $financial_year . "/" . sprintf("%04d", $last_pr_no);

                    $item_data[] = [
                        'pr_id' => $pr_id,
                        'item_code' => $items[$i],
                        'description' => $descriptions[$i],
                        'hsn' => $hsn_codes[$i],
                        'quantity' => $quantities[$i],
                        'unit' => $units[$i],
                        'estimated_cost' => $estimated_costs[$i],
                        'specification' => $specifications[$i],
                        'pr_no' => $pr_no,
                        'created_by' => $this->user_id
                    ];

                    // echo $specifications[$i];
                    $data_purchase_requisition = array('total_value' => $estimated_costs[$i]);

                    // var_dump($data_purchase_requisition);
                    //                     die();

                    $this->db->where('pr_id', $pr_id);
                    $this->db->update('purchase_requisition',  $data_purchase_requisition);
                }
            }

            if (!empty($item_data)) {
                $this->requisition->insert_requisition_items($item_data);
            }

            $this->session->set_flashdata('SUCCESSMSG', "Purchase Requisition added successfully!");

            // Auto-submit for approval if needed
            if ($this->input->post('submit_for_approval') == 'yes') {
                $this->submit_for_approval($pr_id);
            } else {
                redirect('RequisitionController/view_requisition_order');
            }
        } else {
            $this->session->set_flashdata('ERRORMSG', "Requisition not added successfully!");
            redirect('RequisitionController/create_purchase_requisition');
        }
    }

    public function edit_requisition($pr_id)
    {
        // Check if requisition can be edited
        // if (!$this->requisition->can_edit_requisition($pr_id, $this->user_id)) {
        //     $this->session->set_flashdata('ERRORMSG', 'You cannot edit this requisition');
        //     redirect('RequisitionController/view_requisition_order');
        // }

        $data['requisition'] = $this->requisition->get_requisition_by_id($pr_id);
        $data['requisition_items'] = $this->requisition->get_requisition_items($pr_id);
        $data['location_result'] = $this->LocationModel->get_locations();
        $data['department_result'] = $this->department->get_departments();
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['users'] = $this->user->get_user_without_role();
        $data['projects'] = $this->db->get('project')->result_array();
        $data['sales_orders'] = $this->db->select('number_fk, oc_number, project_code')
                                         ->from('salesorder_total')
                                         ->get()
                                         ->result_array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/edit_requisition_order', $data);
    }

    public function update_requisition()
    {
        $pr_id = $this->input->post('pr_id');


        // echo $this->input->post('pr_no');
        // die();

        // Check if requisition can be edited
        // if (!$this->requisition->can_edit_requisition($pr_id, $this->user_id)) {
        //     $this->session->set_flashdata('ERRORMSG', 'You cannot edit this requisition');
        //     redirect('RequisitionController/view_requisition_order');
        // }

        $project_code = $this->input->post('project_code') ?? '';
        $so_no = $this->input->post('so_no');
        $oc_no = $this->input->post('oc_no');

        if (empty($oc_no) && !empty($so_no)) {
            $so_row = $this->db->select('oc_number')
                               ->where('number_fk', $so_no)
                               ->get('salesorder_total')
                               ->row_array();
            if ($so_row) {
                $oc_no = $so_row['oc_number'];
            }
        }

        // Master data
        $master_data = [
            'department_id_fk' => $this->input->post('department_id_fk'),
            'location_id_fk' => $this->input->post('location_id_fk'),
            'requested_by' => $this->input->post('requested_by'),
            'urgency_level' => $this->input->post('urgency_level'),
            'pr_date' => date("Y-m-d", strtotime($this->input->post('pr_date'))),
            'required_date' => date("Y-m-d", strtotime($this->input->post('required_date'))),
            'remarks' => $this->input->post('remarks'),
            'project_code' => $project_code,
            'so_no' => $so_no,
            'oc_no' => $oc_no,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        // Update master
        $this->requisition->update_requisition($pr_id, $master_data);

        // Delete existing items
        $this->requisition->delete_requisition_items($pr_id);

        // Insert updated items
        $items = $this->input->post('item_code');
        $descriptions = $this->input->post('description');
        $hsn_codes = $this->input->post('hsn');
        $quantities = $this->input->post('quantity');
        $units = $this->input->post('unit');
        $estimated_costs = $this->input->post('estimated_cost');
        $specifications = $this->input->post('specification');
        $pr_no  = $this->input->post('pr_no');


        $item_data = [];
        for ($i = 0; $i < count($items); $i++) {
            if (!empty($items[$i])) {
                $item_data[] = [
                    'pr_id' => $pr_id,
                    'item_code' => $items[$i],
                    'description' => $descriptions[$i],
                    'hsn' => $hsn_codes[$i],
                    'quantity' => $quantities[$i],
                    'unit' => $units[$i],
                    'pr_no' => $pr_no,
                    'estimated_cost' => $estimated_costs[$i],
                    'specification' => $specifications[$i],
                    'created_by' => $this->user_id
                ];
            }
        }

        if (!empty($item_data)) {
            $this->requisition->insert_requisition_items($item_data);
        }

        $this->session->set_flashdata('SUCCESSMSG', "Purchase Requisition updated successfully!");

        // Auto-submit for approval if needed
        if ($this->input->post('submit_for_approval') == 'yes') {
            $this->submit_for_approval($pr_id);
        } else {
            redirect('RequisitionController/view_requisition_order');
        }
    }

    public function show_requisition($pr_id)
    {
        $data['requisition'] = $this->requisition->get_requisition_with_details($pr_id);
        $data['users'] = $this->user->get_user_without_role();
        $data['requisition_items'] = $this->requisition->get_requisition_items($pr_id);
        $data['department_result'] = $this->department->get_departments();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['approval_history'] = $this->requisition->get_approval_history($pr_id);
        $data['approval_progress'] = $this->requisition->get_approval_progress($pr_id);




        // echo "hii";


        // var_dump($data['requisition']);

        // die();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/show_requisition_order', $data);
    }

    public function delete_requisition($pr_id)
    {
        if (!is_numeric($pr_id)) {
            show_error("Invalid request");
        }

        $real_user_id = $this->session->userdata('session_data_head')['result']['user_id'] ?? $this->user_id;
        // Check if requisition can be deleted
        $pr = $this->requisition->get_requisition_by_id($pr_id);
        if ($pr && $pr->workflow_status != 'Draft' && $pr->created_by != $real_user_id) {
            $this->session->set_flashdata('ERRORMSG', 'You cannot delete this requisition');
            redirect('RequisitionController/view_requisition_order');
        }

        // Delete items first
        $this->requisition->delete_requisition_items($pr_id);

        // Delete master requisition
        $this->requisition->delete_requisition($pr_id);

        $this->session->set_flashdata('SUCCESSMSG', "Purchase Requisition deleted successfully!");
        redirect('RequisitionController/view_requisition_order');
    }

    public function test_approval()
    {
        $data['pending_approvals'] = $this->requisition->get_pending_approvals($this->user_id);



        //  echo "sss";

        // var_dump($data['pending_approvals']);

        // die();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/test_approval', $data);
    }

    /**
     * List PRs for approval history selection
     */
    public function list_approval_history()
    {
        // Get all PRs that the user has access to (their own or ones they can approve)
        $data['requisitions'] = $this->requisition->get_prs_for_history($this->user_id);


        // var_dump($data);


        // die();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/list_approval_history', $data);
    }

    /**
     * Reports & Analytics Dashboard
     */
    public function reports_analytics()
    {
        $data['reports'] = [];

        // Get report data
        $data['department_stats'] = $this->requisition->get_pr_stats_by_department();
        $data['monthly_stats'] = $this->requisition->get_monthly_stats();
        $data['approval_metrics'] = $this->requisition->get_approval_metrics();

        // Get date filters
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if ($start_date && $end_date) {
            $data['department_stats'] = $this->requisition->get_pr_stats_by_department($start_date, $end_date);
        }

        $data['departments'] = $this->department->get_departments();
        $data['filters'] = [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('requisition/reports_analytics', $data);
    }

    /**
     * Generate custom report
     */
    public function generate_report()
    {
        $report_type = $this->input->post('report_type');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $department = $this->input->post('department');

        $data = [];

        switch ($report_type) {
            case 'department_wise':
                $data['report_title'] = 'Department-wise PR Analysis';
                $data['report_data'] = $this->requisition->get_pr_stats_by_department($start_date, $end_date);
                break;

            case 'monthly_summary':
                $data['report_title'] = 'Monthly PR Summary';
                $data['report_data'] = $this->requisition->get_monthly_stats($start_date, $end_date);
                break;

            case 'approval_timeline':
                $data['report_title'] = 'Approval Timeline Analysis';
                $data['report_data'] = $this->requisition->get_approval_timeline_analysis($start_date, $end_date);
                break;

            default:
                $data['report_title'] = 'PR Summary Report';
                $data['report_data'] = $this->requisition->get_export_data([
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'department' => $department
                ]);
                break;
        }

        $output_format = $this->input->post('output_format');

        if ($output_format == 'csv') {
            $this->export_report_csv($data);
        } elseif ($output_format == 'pdf') {
            $this->export_report_pdf($data);
        } else {
            // Display in view
            $data['report_type'] = $report_type;
            $data['filters'] = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'department' => $department
            ];

            $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('requisition/report_view', $data);
        }
    }

    /**
     * Export report as CSV
     */
    private function export_report_csv($data)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="pr_report_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        if (isset($data['report_data'][0]) && is_array($data['report_data'][0])) {
            // Array data
            fputcsv($output, array_keys($data['report_data'][0]));
            foreach ($data['report_data'] as $row) {
                fputcsv($output, $row);
            }
        } elseif (isset($data['report_data'][0]) && is_object($data['report_data'][0])) {
            // Object data
            fputcsv($output, array_keys((array)$data['report_data'][0]));
            foreach ($data['report_data'] as $row) {
                fputcsv($output, (array)$row);
            }
        }

        fclose($output);
    }

    /**
     * Get approval metrics
     */
    public function get_approval_metrics_ajax()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $metrics = $this->requisition->get_approval_metrics($start_date, $end_date);
        echo json_encode($metrics);
    }

    /**
     * Export report as PDF using mPDF
     */
    private function export_report_pdf($data)
    {
        // Check if $data['report_data'] is set and not empty
        if (!isset($data['report_data']) || empty($data['report_data'])) {
            $this->session->set_flashdata('ERRORMSG', 'No data available for export');
            redirect('RequisitionController/reports_analytics');
        }

        try {
            // Create new PDF document in landscape mode
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_header' => 10,
                'margin_footer' => 10,
                'default_font' => 'dejavusans' // Supports Unicode
            ]);

            // Set document information
            $mpdf->SetCreator('Your Company');
            $mpdf->SetAuthor('Your Company');
            $mpdf->SetTitle($data['report_title']);
            $mpdf->SetSubject('PR Report');

            // Create HTML content
            $html = '<html>
        <head>
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; }
                h1 { color: #2c3e50; text-align: center; margin-bottom: 15px; font-size: 16pt; }
                .report-info { margin-bottom: 15px; text-align: center; color: #7f8c8d; font-size: 9pt; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th { background-color: #3498db; color: white; padding: 6px; text-align: left; font-weight: bold; border: 1px solid #2980b9; }
                td { padding: 5px; border: 1px solid #ddd; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .footer { margin-top: 20px; text-align: center; color: #95a5a6; font-size: 8pt; }
                .page-count { float: right; font-size: 8pt; color: #7f8c8d; }
            </style>
        </head>
        <body>';

            // Add report title
            $html .= '<h1>' . htmlspecialchars($data['report_title']) . '</h1>';
            $html .= '<div class="report-info">';
            $html .= 'Generated on: ' . date('d-m-Y') . ' | ';
            $html .= 'Total Records: ' . count($data['report_data']);
            $html .= '</div>';

            // Create table HTML
            $html .= '<table>';

            // Add headers
            if (isset($data['report_data'][0])) {
                if (is_object($data['report_data'][0])) {
                    // Object data
                    $first_row = (array)$data['report_data'][0];
                    $html .= '<thead><tr>';
                    foreach (array_keys($first_row) as $header) {
                        $html .= '<th>' . htmlspecialchars($header) . '</th>';
                    }
                    $html .= '</tr></thead><tbody>';

                    // Add data rows
                    foreach ($data['report_data'] as $row) {
                        $html .= '<tr>';
                        foreach ((array)$row as $cell) {
                            // Convert to string and escape
                            $cell_value = is_string($cell) ? $cell : (string)$cell;
                            $html .= '<td>' . htmlspecialchars($cell_value) . '</td>';
                        }
                        $html .= '</tr>';
                    }
                } elseif (is_array($data['report_data'][0])) {
                    // Array data
                    $html .= '<thead><tr>';
                    foreach (array_keys($data['report_data'][0]) as $header) {
                        $html .= '<th>' . htmlspecialchars($header) . '</th>';
                    }
                    $html .= '</tr></thead><tbody>';

                    // Add data rows
                    foreach ($data['report_data'] as $row) {
                        $html .= '<tr>';
                        foreach ($row as $cell) {
                            // Convert to string and escape
                            $cell_value = is_string($cell) ? $cell : (string)$cell;
                            $html .= '<td>' . htmlspecialchars($cell_value) . '</td>';
                        }
                        $html .= '</tr>';
                    }
                }
            }

            $html .= '</tbody></table>';

            // Add footer with page numbers
            $html .= '<div class="footer">';
            $html .= '<div class="page-count">Page {PAGENO} of {nb}</div>';
            $html .= 'Generated by Your Company ERP System';
            $html .= '</div>';

            $html .= '</body></html>';

            // Write HTML content
            $mpdf->WriteHTML($html);

            // Output PDF
            $filename = 'pr_report_' . date('Ymd_His') . '.pdf';
            $mpdf->Output($filename, 'D'); // 'D' for download
            exit;
        } catch (\Exception $e) {
            // If mPDF fails, fall back to CSV
            log_message('error', 'PDF Generation Error: ' . $e->getMessage());
            $this->session->set_flashdata('INFO', 'PDF generation failed. Downloading CSV instead.');
            $this->export_report_csv($data);
        }
    }

    public function convert_pr_to_po($pr_id)
    {
        if (empty($pr_id) || !is_numeric($pr_id)) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid Purchase Requisition ID');
            redirect('RequisitionController/view_requisition_order');
            return;
        }

        $pr = $this->requisition->get_requisition_with_details($pr_id);
        if (!$pr) {
            $this->session->set_flashdata('ERRORMSG', 'Purchase Requisition not found');
            redirect('RequisitionController/view_requisition_order');
            return;
        }

        if (strtolower($pr->approval_status) !== 'approved') {
            $this->session->set_flashdata('ERRORMSG', 'Only Approved Purchase Requisitions can be converted to Purchase Orders');
            redirect('RequisitionController/show_requisition/' . $pr_id);
            return;
        }

        redirect('SupplierController/create_purchase_order?pr_id=' . $pr_id);
    }
}
