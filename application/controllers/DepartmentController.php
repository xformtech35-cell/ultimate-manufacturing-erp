<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DepartmentController extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('department');

        $session_data_head = $this->session->userdata('session_data_head');
        if (empty($session_data_head['result']['user_id'])) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function index()
    {
        $data['department_result'] = $this->department->get_departments();


        $this->load->view('admin/header_side_bar');
        $this->load->view('department/add_department', $data);
        $this->load->view('admin/footer');
    }

    public function add_department()
    {
        $department_name = $this->input->post('department_name');
        $description = $this->input->post('description');

        $data = array(
            'department_name' => $department_name,
            'description' => $description
        );

        if (!$this->department->department_exists($department_name)) {
            $this->department->add_department($data);
            $this->session->set_flashdata('SUCCESSMSG', "Department added successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Department already exists!");
        }
        redirect('DepartmentController/index');
    }

    public function delete_department_by_id()
    {

         $id = $this->input->get('department_id');

        if ($this->department->delete_department_by_id($id)) {
            $this->session->set_flashdata('SUCCESSMSG', "Department deleted successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Department deletion failed!");
        }
        redirect('DepartmentController/index');
    }
}
