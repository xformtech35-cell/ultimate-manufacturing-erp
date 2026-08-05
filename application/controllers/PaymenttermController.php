<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PaymenttermController extends MY_Controller {

    protected $user_id;
    function __construct() {
        parent::__construct();

        
        $this->load->library('session');
        $this->load->model('paymentterm', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

 
    public function paymentterm_index() {
       
        $data['paymentterm_result'] = $this->paymentterm->get_paymentterm($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('payment_terms/add_paymentterm',$data);
    }
    
    public function add_payment_term() {
        $payment_term = $this->input->post('payment_term');
        $data_paymentterm = array('payment_term' => $payment_term, 'uid' => $this->user_id);
        $result = $this->paymentterm->paymentterm_check($payment_term, $this->user_id);  
        if ($result == FALSE) {
            $this->paymentterm->add_paymentterm($data_paymentterm);
            $this->session->set_flashdata('SUCCESSMSG', "Payment Term added successfully!!");
            redirect('PaymenttermController/paymentterm_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "paymentterm already exist!!");
            redirect('PaymenttermController/paymentterm_index');
        }
    }
    
    
      public function delete_payment_term_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->paymentterm->delete_payment_term_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment Term deleted successfully!!");
            redirect('PaymenttermController/paymentterm_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment Term not deleted successfully!!");
            redirect('PaymenttermController/paymentterm_index');
        }
    }
    
    
     public function get_payment_term() {
       $paymentterm_result = $this->moc->get_paymentterm($this->user_id);
       echo json_encode($paymentterm_result);
    }


}
