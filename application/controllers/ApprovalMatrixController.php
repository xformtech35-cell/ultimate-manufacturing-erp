<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ApprovalMatrixController extends MY_Controller
{
    protected $user_id;

    public function __construct()
    {
        parent::__construct();

        // Load libraries, helpers, and models
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->model('role', '', TRUE);

        $this->load->model('ApprovalMatrixModel');

        // Session check
        $session_data_head = $this->session->userdata('session_data_head');
        if (!isset($session_data_head['result']['user_id'])) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }

        $this->user_id = $session_data_head['result']['user_id'];
    }

    // ---------------------------------------------------------
    // Display all approval rules
    // ---------------------------------------------------------
    public function index()
    {
        $this->load->model('department');
        $data['approvals'] = $this->ApprovalMatrixModel->getAll();
        $data['role'] = $this->role->get_role();
        $data['department_result'] = $this->department->get_departments();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('approval_matrix/index', $data);
    }

    // ---------------------------------------------------------
    // Add new approval rule
    // ---------------------------------------------------------
    public function add()
    {
        if ($this->input->post()) {
            $this->form_validation->set_rules('document_type', 'Document Type', 'required');
            $this->form_validation->set_rules('level', 'Level', 'required|integer');
            $this->form_validation->set_rules('approver_role', 'Approver Role', 'required');

            if ($this->form_validation->run() === TRUE) {
                $data = [
                    'document_type' => $this->input->post('document_type'),
                    'level' => $this->input->post('level'),
                    'department_id' => $this->input->post('department_id') ?: null,
                    'approver_role' => $this->input->post('approver_role'),
                    'min_amount' => $this->input->post('min_amount') ?: 0,
                    'max_amount' => $this->input->post('max_amount') ?: 0,
                    'status' => $this->input->post('status') ?: 'active',
                    'created_by' => $this->user_id,
                    'created_at' => date("Y-m-d H:i:s")
                ];

                $this->ApprovalMatrixModel->insert($data);
                $this->session->set_flashdata('SUCCESSMSG', "Approval Rule added successfully!");
                redirect('ApprovalMatrixController');
            }
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('approval_matrix/add');
    }

    // ---------------------------------------------------------
    // Edit existing approval rule
    // ---------------------------------------------------------
    public function edit($id)
    {
        $this->load->model('department');
        $data['rule'] = $this->ApprovalMatrixModel->getById($id);
        $data['role'] = $this->role->get_role();
        $data['department_result'] = $this->department->get_departments();

        if ($this->input->post()) {
            $this->form_validation->set_rules('document_type', 'Document Type', 'required');
            $this->form_validation->set_rules('level', 'Level', 'required|integer');
            $this->form_validation->set_rules('approver_role', 'Approver Role', 'required');

            if ($this->form_validation->run() === TRUE) {
                $update_data = [
                    'document_type' => $this->input->post('document_type'),
                    'level' => $this->input->post('level'),
                    'department_id' => $this->input->post('department_id') ?: null,
                    'approver_role' => $this->input->post('approver_role'),
                    'min_amount' => $this->input->post('min_amount') ?: 0,
                    'max_amount' => $this->input->post('max_amount') ?: 0,
                    'status' => $this->input->post('status') ?: 'active',
                    'updated_by' => $this->user_id,
                    'updated_at' => date("Y-m-d H:i:s")
                ];

                $this->ApprovalMatrixModel->update($id, $update_data);
                $this->session->set_flashdata('SUCCESSMSG', "Approval Rule updated successfully!");
                redirect('ApprovalMatrixController');
            }
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('approval_matrix/edit', $data);
    }

    // ---------------------------------------------------------
    // Delete approval rule
    // ---------------------------------------------------------
    public function delete($id)
    {
        $this->ApprovalMatrixModel->delete($id);
        $this->session->set_flashdata('SUCCESSMSG', "Approval Rule deleted successfully!");
        redirect('ApprovalMatrixController');
    }
}
