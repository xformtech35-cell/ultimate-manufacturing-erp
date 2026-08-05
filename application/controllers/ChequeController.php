<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ChequeController extends MY_Controller {

    protected $user_id;
    function __construct() {
        parent::__construct();

        
        $this->load->library('session');
        $this->load->model('cheque', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? null;
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

 
    public function cheque_index() {
       
        $data['chequedetail_result'] = $this->cheque->get_cheque_detail($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('cheque/add_cheque', $data);
    }
    
    public function add_cheque_detail() {
        
        $cheque_no = $this->input->post('cheque_no');
        $bank_account_name = $this->input->post('bank_account_name');
        $no_of_cheque = $this->input->post('no_of_cheque');
        $status = $this->input->post('status');
        //$creation_date = date("Y-m-d");
        $creation_date = date("Y-m-d", strtotime($this->input->post('creation_date')));
        $data_chequedetail = array('cheque_no' => $cheque_no, 'bank_account_name' => $bank_account_name,'no_of_cheque' => $no_of_cheque,'status' => $status, 'creation_date' => $creation_date,'uid' => $this->user_id
                );
        $result = $this->cheque->chequedetail_check($cheque_no, $this->user_id);  
        if ($result == FALSE) {
            $this->cheque->add_cheque_detail($data_chequedetail);
            $this->session->set_flashdata('SUCCESSMSG', "Cheque Details added successfully!!");
            redirect('ChequeController/cheque_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Cheque Details already exist!!");
            redirect('ChequeController/cheque_index');
        }
    }
    
    
      public function delete_cheque_detail_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->cheque->delete_cheque_detail_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Cheque Detail deleted successfully!!");
            redirect('ChequeController/cheque_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Cheque Detail not deleted successfully!!");
            redirect('ChequeController/cheque_index');
        }
    }
    
    
     public function get_cheque_detail() {
       $chequedetail_result = $this->cheque->get_bankdetail($this->user_id);
       echo json_encode($chequedetail_result);
    }

      public function get_cheque_detail_id() {
        $id = $this->uri->segment(3);
        $data['cheque_detail_by_id'] = $this->cheque->get_cheque_detail_id($id);
        $data['status_catgory'] = $this->cheque->get_status_catgory($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('cheque/edit_cheque', $data);
    }
    
  
    
      public function edit_cheque_detail() {
        $cheque_id = $this->input->post('cheque_id');
       $cheque_no = $this->input->post('cheque_no');
        $bank_account_name = $this->input->post('bank_account_name');
        $no_of_cheque = $this->input->post('no_of_cheque');
        $status = $this->input->post('status');
        $creation_date = date("Y-m-d", strtotime($this->input->post('creation_date')));
        $data_cheque = array('cheque_no' => $cheque_no, 'bank_account_name' => $bank_account_name,'no_of_cheque' => $no_of_cheque,'status' => $status, 'creation_date' => $creation_date
                    );

        $result = $this->cheque->edit_cheque_detail($data_cheque, $cheque_id, $this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Cheque Detail updated successfully!!");
            redirect('ChequeController/cheque_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Cheque Detail not updated successfully!!");
            redirect('ChequeController/cheque_index');
        }
    }
    
    
}
