<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PoamendmentController extends MY_Controller
{

    private $user_id;
    private $user_email;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Poamendment_model', 'amendment');
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Get session data
        $session_data_head = $this->session->userdata('session_data_head');

        // Set user ID
        $this->user_id = isset($session_data_head['result']['user_id']) ? $session_data_head['result']['user_id'] : null;

        // Set user email
        $this->user_email = isset($session_data_head['result']['email']) ? $session_data_head['result']['email'] : 'admin@xform.in';

        // Check if user is logged in
        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    /**
     * List all PO Amendments
     */
    public function index()
    {
        $data = array();
        $data['title'] = 'PO Amendments';
        $data['page_title'] = 'PO Amendments';

        // Get filters from query string
        $filters = array();
        if ($this->input->get('status')) {
            $filters['status'] = $this->input->get('status');
        }
        if ($this->input->get('amendment_no')) {
            $filters['amendment_no'] = $this->input->get('amendment_no');
        }
        if ($this->input->get('po_number')) {
            $filters['po_number'] = $this->input->get('po_number');
        }
        if ($this->input->get('amendment_type')) {
            $filters['amendment_type'] = $this->input->get('amendment_type');
        }

        // Get all amendments
        $data['amendments'] = $this->amendment->get_all_amendments($filters);

        // Get stats counts
        $this->load->database();

        // Draft count (only for current user)
        $this->db->where('status', 'draft');
        $this->db->where('initiated_by', $this->user_id);
        $data['draft_count'] = $this->db->count_all_results('po_amendments');

        // Pending approval count
        $this->db->where('status', 'pending_approval');
        $data['pending_approval_count'] = $this->db->count_all_results('po_amendments');

        // Approved count
        $this->db->where('status', 'approved');
        $data['approved_count'] = $this->db->count_all_results('po_amendments');

        // Vendor acknowledged count
        $this->db->where('status', 'vendor_acknowledged');
        $data['vendor_ack_count'] = $this->db->count_all_results('po_amendments');

        // Revised PO issued count
        $this->db->where('status', 'revised_po_issued');
        $data['revised_po_count'] = $this->db->count_all_results('po_amendments');

        // Completed count
        $this->db->where('status', 'completed');
        $data['completed_count'] = $this->db->count_all_results('po_amendments');



        $this->load->view('admin/header_side_bar');
        $this->load->view('po_amendment/list_po_amendment', $data);
    }

    /**
     * Create new PO Amendment
     */
    public function create()
    {
        $data = array();
        $data['title'] = 'Create PO Amendment';
        $data['page_title'] = 'Create PO Amendment';

        // Get PO ID from query string
        $po_id = $this->input->get('po_id');
        if ($po_id) {
            $data['po'] = $this->amendment->get_po_for_amendment($po_id);
            if ($data['po']) {
                $data['po_items'] = $this->amendment->get_po_items($data['po']['number_fk']);
            }
        }




        if ($this->input->post()) {
            $this->form_validation->set_rules('po_id', 'PO ID', 'required');
            $this->form_validation->set_rules('po_number', 'PO Number', 'required');
            $this->form_validation->set_rules('amendment_type', 'Amendment Type', 'required');
            $this->form_validation->set_rules('description', 'Description', 'required');
            $this->form_validation->set_rules('reason', 'Reason', 'required');




            // echo "ssss111";

            // die();

            //if ($this->form_validation->run() == TRUE) {
            // Get amendment value from POST
            $amendment_value = $this->input->post('amendment_value') ? floatval($this->input->post('amendment_value')) : 0;

            $amendment_data = array(
                'po_id' => $this->input->post('po_id'), // This will be mapped to po_id_fk in model
                'po_number' => $this->input->post('po_number'),
                'amendment_type' => $this->input->post('amendment_type'),
                'description' => $this->input->post('description'),
                'reason' => $this->input->post('reason'),
                'amendment_value' => $amendment_value,
                'user_id' => $this->user_id
            );




            // var_dump($amendment_data);
            // die();

            // Collect amendment items if any
            $amendment_items = array();
            if ($this->input->post('change_type')) {
                $change_types = $this->input->post('change_type');
                $po_item_ids = $this->input->post('po_item_id');
                $old_values = $this->input->post('old_value');
                $new_values = $this->input->post('new_value');
                $change_amounts = $this->input->post('change_amount');
                $change_descriptions = $this->input->post('change_description');

                for ($i = 0; $i < count($change_types); $i++) {
                    $amendment_items[] = array(
                        'po_item_id' => $po_item_ids[$i] ?? 0,
                        'change_type' => $change_types[$i],
                        'old_value' => $old_values[$i] ?? '',
                        'new_value' => $new_values[$i] ?? '',
                        'change_amount' => $change_amounts[$i] ?? 0,
                        'change_description' => $change_descriptions[$i] ?? '',
                        'reason' => $this->input->post('reason') // Use main reason or item-specific
                    );
                }
                $amendment_data['amendment_items'] = $amendment_items;
            }

            // Handle file upload
            if (!empty($_FILES['attachment']['name'])) {
                $config['upload_path'] = './uploads/amendments/';
                $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png';
                $config['max_size'] = 5120; // 5MB
                $config['file_name'] = 'amendment_' . time();

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('attachment')) {
                    $amendment_data['attachment'] = $this->upload->data('file_name');
                }
            }

            // Create amendment
            $amendment_id = $this->amendment->create_amendment($amendment_data);

            if ($amendment_id) {
                // Submit for approval if requested
                if ($this->input->post('submit_for_approval')) {
                    $this->amendment->submit_for_approval($amendment_id, $amendment_value);
                    $this->session->set_flashdata('SUCCESSMSG', 'Amendment created and submitted for approval!');
                } else {
                    $this->session->set_flashdata('SUCCESSMSG', 'Amendment created successfully as draft!');
                }

                redirect('PoamendmentController/view/' . $amendment_id);
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to create amendment.');
            }
            //  }
        }

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('po_amendment/create_po_amendment', $data);
    }
    /**
     * View amendment details
     */
    public function view($amendment_id)
    {
        $data = array();
        $data['title'] = 'Amendment Details';
        $data['page_title'] = 'Amendment Details';

        $data['amendment'] = $this->amendment->get_amendment($amendment_id);

        if (!$data['amendment']) {
            show_404();
        }

        // Check if current user can approve this amendment
        $user_email = $this->amendment->get_user_email();
        $data['can_approve'] = $this->amendment->can_user_approve($amendment_id, $user_email);

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('po_amendment/view_po_amendment', $data);
    }

    /**
     * Edit amendment
     */
    public function edit($amendment_id)
    {
        $data = array();
        $data['title'] = 'Edit Amendment';
        $data['page_title'] = 'Edit Amendment';

        $data['amendment'] = $this->amendment->get_amendment($amendment_id);

        if (!$data['amendment']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = array(
                'amendment_type' => $this->input->post('amendment_type'),
                'description' => $this->input->post('description'),
                'reason' => $this->input->post('reason')
            );

            if ($this->amendment->update_amendment($amendment_id, $update_data)) {
                $this->session->set_flashdata('success', 'Amendment updated successfully!');
                redirect('PoamendmentController/view/' . $amendment_id);
            } else {
                $this->session->set_flashdata('error', 'Failed to update amendment.');
            }
        }

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('po_amendment/edit_po_amendment', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Delete amendment
     */
    public function delete($amendment_id)
    {
        if ($this->amendment->delete_amendment($amendment_id)) {
            $this->session->set_flashdata('success', 'Amendment deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete amendment.');
        }

        redirect('PoamendmentController/index');
    }

    /**
     * Submit for approval
     */
    public function submit_approval($amendment_id)
    {
        if ($this->input->post()) {
            $amendment_value = $this->input->post('amendment_value') ?: 0;

            if ($this->amendment->submit_for_approval($amendment_id, $amendment_value)) {
                $this->session->set_flashdata('success', 'Amendment submitted for approval!');
            } else {
                $this->session->set_flashdata('error', 'Failed to submit for approval.');
            }

            redirect('PoamendmentController/view/' . $amendment_id);
        }
    }

    /**
     * Update vendor acknowledgment
     */
    public function vendor_acknowledge($amendment_id)
    {
        if ($this->input->post()) {
            $vendor_data = array(
                'ack_by' => $this->input->post('ack_by'),
                'ack_notes' => $this->input->post('ack_notes')
            );

            if ($this->amendment->update_vendor_ack($amendment_id, $vendor_data)) {
                $this->session->set_flashdata('success', 'Vendor acknowledgment recorded.');
            } else {
                $this->session->set_flashdata('error', 'Failed to update vendor acknowledgment.');
            }

            redirect('PoamendmentController/view/' . $amendment_id);
        }
    }

    /**
     * Update revised PO
     */
    public function update_revised_po($amendment_id)
    {
        if ($this->input->post()) {
            $revised_data = array(
                'revised_po_number' => $this->input->post('revised_po_number'),
                'create_new_po' => $this->input->post('create_new_po')
            );

            if ($this->amendment->update_revised_po($amendment_id, $revised_data)) {
                $this->session->set_flashdata('success', 'Revised PO details updated.');
            } else {
                $this->session->set_flashdata('error', 'Failed to update revised PO.');
            }

            redirect('PoamendmentController/view/' . $amendment_id);
        }
    }

    /**
     * List pending approvals
     */
    public function approvals()
    {
        $data = array();
        $data['title'] = 'Amendment Approvals';
        $data['page_title'] = 'Pending Approvals';

        $user_email = $this->amendment->get_user_email();


        // echo  $user_email;


        // die();
        $data['pending_approvals'] = $this->amendment->get_pending_approvals($user_email);
        $data['approval_history'] = $this->amendment->get_approval_history($user_email);

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('po_amendment/approvals_list_amendment', $data);
    }

    /**
     * Process approval action
     */
    public function process_approval($approval_id)
    {
        if ($this->input->post()) {
            $action = $this->input->post('action');
            $remarks = $this->input->post('remarks');

            if ($this->amendment->update_approval($approval_id, $action, $remarks)) {
                $this->session->set_flashdata('success', 'Approval processed successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to process approval.');
            }

            redirect('PoamendmentController/approvals');
        }
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $data = array();
        $data['title'] = 'Amendment Dashboard';
        $data['page_title'] = 'Amendment Dashboard';

        $data['counts'] = $this->amendment->get_dashboard_counts();
        $data['recent_amendments'] = $this->amendment->get_all_amendments(array(), 10, 0);

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('po_amendment/dashboard', $data);
    }

    /**
     * Get POs for dropdown (AJAX)
     */
    public function get_pos_for_dropdown()
    {
        $this->load->database();

        $this->db->select('pt.id, pt.number_fk, s.company_name, pt.total');
        $this->db->from('po_total pt');
        $this->db->join('supplier s', 's.supplier_id = pt.supplier_id_fk');
        $this->db->where('pt.approval_status', 'approved');
        $this->db->where('pt.status', '1');
        $pos = $this->db->get()->result_array();

        echo json_encode($pos);
    }


    // Add this method to your PoamendmentController.php

    /**
     * Get PO items via AJAX
     */
    public function get_po_items_ajax()
    {
        $po_id = $this->input->post('po_id');

        if ($po_id) {
            // Get PO details
            $this->db->select('pt.id, pt.number_fk, pt.date, pt.total, s.company_name');
            $this->db->from('po_total pt');
            $this->db->join('supplier s', 's.supplier_id = pt.supplier_id_fk');
            $this->db->where('pt.id', $po_id);
            $this->db->where('pt.approval_status', 'approved');
            $po = $this->db->get()->row_array();

            if ($po) {
                // Get PO items
                $this->db->where('number', $po['number_fk']);
                $items = $this->db->get('purchase_order')->result_array();

                // Enhance items with inventory details
                $enhanced_items = [];
                foreach ($items as $item) {
                    if (!empty($item['product_name'])) {
                        $this->db->where('code', $item['product_name']);
                        $inventory = $this->db->get('inventory')->row_array();
                        if ($inventory) {
                            $item['inventory_details'] = $inventory;
                        }
                    }
                    $enhanced_items[] = $item;
                }

                echo json_encode([
                    'success' => true,
                    'po' => $po,
                    'items' => $enhanced_items
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'PO not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'PO ID required']);
        }
    }


    /**
     * View revision history for a PO
     */
    public function revision_history($po_number)
    {
        $data = array();
        $data['title'] = 'PO Revision History';
        $data['page_title'] = 'Revision History: ' . $po_number;

        // Get all revisions for this PO
        $data['revisions'] = $this->amendment->get_po_revisions($po_number);

        // Get PO details
        $this->load->model('supplier');
        $data['po_data'] = $this->supplier->get_po_data_group_by($po_number, $this->user_id);

        // Get settings
        $this->load->model('login');
        $data['settings'] = $this->login->get_settings($this->user_id);

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('po_amendment/revision_history', $data);
    }

    // In PoamendmentController.php

    /**
     * Get PO items via AJAX
     */
    public function get_po_items($po_id)
    {
        header('Content-Type: application/json');

        if (!$po_id) {
            echo json_encode([
                'success' => false,
                'message' => 'PO ID is required'
            ]);
            return;
        }

        // Get PO details
        $po = $this->amendment->get_po_for_amendment($po_id);

        if (!$po) {
            echo json_encode([
                'success' => false,
                'message' => 'PO not found'
            ]);
            return;
        }

        // Get PO items
        $items = $this->amendment->get_po_items($po['number_fk']);

        echo json_encode([
            'success' => true,
            'data' => $items,
            'po' => [
                'po_number' => $po['number_fk'],
                'supplier_name' => $po['company_name'],
                'po_date' => date('d-m-Y', strtotime($po['date'])),
                'total_amount' => $po['total'],
                'status' => $po['approval_status']
            ]
        ]);
    }


    /**
     * View approval details with action buttons
     */
    public function approval_action($amendment_id)
    {
        $data = array();
        $data['title'] = 'Approval Action';
        $data['page_title'] = 'Take Approval Action';

        $user_email = $this->amendment->get_user_email();

        // Get amendment details
        $data['amendment'] = $this->amendment->get_amendment($amendment_id);

        if (!$data['amendment']) {
            show_404();
        }

        // Check if user can approve this amendment
        $data['can_approve'] = $this->amendment->can_user_approve($amendment_id, $user_email);

        if (!$data['can_approve']) {
            $this->session->set_flashdata('error', 'You are not authorized to approve this amendment.');
            redirect('PoamendmentController/view/' . $amendment_id);
        }

        // Get current user's approval record
        $this->db->where('amendment_id', $amendment_id);
        $this->db->where('approver_email', $user_email);
        $this->db->where('status', 'pending');
        $data['approval_record'] = $this->db->get('amendment_approvals')->row_array();

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('po_amendment/approval_action', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process approval action for a specific amendment
     */
    public function process_amendment_approval($amendment_id)
    {
        $user_email = $this->amendment->get_user_email();

        // Check if user can approve this amendment
        if (!$this->amendment->can_user_approve($amendment_id, $user_email)) {
            $this->session->set_flashdata('error', 'You are not authorized to approve this amendment.');
            redirect('PoamendmentController/view/' . $amendment_id);
        }

        if ($this->input->post()) {
            $action = $this->input->post('action');
            $remarks = $this->input->post('remarks');

            // Get approval_id for this user and amendment
            $this->db->where('amendment_id', $amendment_id);
            $this->db->where('approver_email', $user_email);
            $this->db->where('status', 'pending');
            $approval = $this->db->get('amendment_approvals')->row_array();

            if (!$approval) {
                $this->session->set_flashdata('error', 'No pending approval found.');
                redirect('PoamendmentController/view/' . $amendment_id);
            }

            if ($action == 'rejected' && empty($remarks)) {
                $this->session->set_flashdata('error', 'Remarks are required for rejection.');
                redirect('PoamendmentController/approval_action/' . $amendment_id);
            }

            if ($this->amendment->update_approval($approval['approval_id'], $action, $remarks)) {
                $this->session->set_flashdata('success', 'Amendment ' . $action . ' successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to process approval.');
            }

            redirect('PoamendmentController/view/' . $amendment_id);
        }
    }

    /**
     * View approval workflow
     */
    public function approval_workflow($amendment_id)
    {
        $data = array();
        $data['title'] = 'Approval Workflow';
        $data['page_title'] = 'Approval Workflow';

        $data['amendment'] = $this->amendment->get_amendment($amendment_id);
        $data['workflow'] = $this->amendment->get_approval_workflow_status($amendment_id);

        if (!$data['amendment']) {
            show_404();
        }

        $this->load->view('admin/header_side_bar', $data);
        $this->load->view('po_amendment/approval_workflow', $data);
    }
}
