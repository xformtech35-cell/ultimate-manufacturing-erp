<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MocController extends MY_Controller {


    function __construct() {
        parent::__construct();

        
        $this->load->library('session');
        $this->load->model('moc', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

 
    public function moc_index() {
       
        $data['moc_result'] = $this->moc->get_moc($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('moc/add_moc',$data);
    }
    
    public function add_moc() {
        $moc = $this->input->post('moc');
        $data_moc = array('moc' => $moc, 'uid' => $this->user_id);
        $result = $this->moc->moc_check($moc, $this->user_id);  
        if ($result == FALSE) {
            $this->moc->add_moc($data_moc);
            $this->session->set_flashdata('SUCCESSMSG', "Moc added successfully!!");
            redirect('MocController/moc_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Moc already exist!!");
            redirect('MocController/moc_index');
        }
    }
    
    
      public function delete_moc_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->moc->delete_moc_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Moc deleted successfully!!");
            redirect('MocController/moc_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Moc not deleted successfully!!");
            redirect('MocController/moc_index');
        }
    }
    
    
     public function get_moc() {
       $moc_result = $this->moc->get_moc($this->user_id);
       // Ensure we always return a valid JSON array
       if (!is_array($moc_result) || empty($moc_result)) {
           $moc_result = array();
       }
       header('Content-Type: application/json');
       echo json_encode($moc_result);
    }


}
