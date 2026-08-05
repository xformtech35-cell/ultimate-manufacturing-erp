<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdvanceController extends MY_Controller  {

    protected $user_id;
    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('login', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('advance', '', TRUE);
        $this->load->model('invoice', '', TRUE);
         $this->load->model('customer', '', TRUE);
        $this->load->library('form_validation');
        $this->load->library('image_lib');
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? null;
        
        if($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function index() {
		
        $data['result'] = $this->advance->get_advance_amount($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('advance/add_advance', $data);
    }
    public function add_advance() {
        $customer_id = $this->input->post('customer_id');
        $advance_pay = $this->input->post('advance_pay');
        $created_at = date("Y-m-d");
        $updated_at = date("Y-m-d");
        
        $data_advance = array('customer_id_fk' => $customer_id, 'advance_pay' => $advance_pay,
            'created_at' => $created_at, 'updated_at' => $updated_at, 'uid' => $this->user_id);
          $result = $this->advance->advance_amount_check($customer_id);
       
        if ($result == FALSE) {
             $this->advance->add_advance($data_advance);
            $this->session->set_flashdata('SUCCESSMSG', "Advance added successfully!!");
            redirect('AdvanceController/index');
        } else {
             $this->session->set_flashdata('INFOMSG', "Please Edit Advance Amount to add!!");
             redirect('AdvanceController/index');
        }
    }

    public function edit_advance() {
        $advance_id = $this->input->post('advance_id');
        $customer_id = $this->input->post('customer_id');
        $advance_pay = $this->input->post('advance_pay');
        $updated_at = date("Y-m-d");
        $data_advance = array('customer_id_fk' => $customer_id, 'advance_pay' => $advance_pay, 'updated_at' => $updated_at, 'uid' => $this->user_id);
        
        $result = $this->advance->edit_advance($data_advance, $advance_id, $this->user_id);
       
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Advance updated successfully!!");
             redirect('AdvanceController/index');
        } else {
             $this->session->set_flashdata('INFOMSG', "Advance not updated successfully!!");
              redirect('AdvanceController/index');
        }
    }

    public function get_advance_by_id() {
        $id = $this->uri->segment(3);
        $data['advance'] = $this->advance->get_advance_by_id($id);
        $data['customer_result'] = $this->customer->get_customer($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('advance/edit_advance', $data);
    }

    public function delete_advance_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->advance->delete_advance_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Advance deleted successfully!!");
            redirect('AdvanceController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Advance not deleted successfully!!");
            redirect('AdvanceController/index');
        }
    }
   
}
