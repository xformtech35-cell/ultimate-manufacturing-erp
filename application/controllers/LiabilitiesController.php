<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LiabilitiesController extends MY_Controller {

    protected $user_id;
    function __construct() {
        parent::__construct();

        
        $this->load->library('session');
        $this->load->model('liabilities', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

 
    public function liabilities_index() {
        
          $data['liabilities_name'] = $this->liabilities->get_liabilities_name($this->user_id);
       
        $data['liabilities_result'] = $this->liabilities->get_liabilities($this->user_id);
         $data['subliabilities_result'] = $this->liabilities->get_subLiabilities($this->user_id);
         
        //  var_dump($data);
        //  die();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('liabilities/add_liabilities',$data);
    }
    
    public function add_liabilities() {
        $Liabilities = $this->input->post('Liabilities');
        $data_liabilities = array('Liabilities' => $Liabilities, 'uid' => $this->user_id);
        $result = $this->liabilities->liabilities_check($Liabilities, $this->user_id);  
        if ($result == FALSE) {
            $this->liabilities->add_liabilities($data_liabilities);
            $this->session->set_flashdata('SUCCESSMSGLiabilities', "Liabilities added successfully!!");
            redirect('LiabilitiesController/liabilities_index');
        } else {
            $this->session->set_flashdata('INFOMSGLiabilities', "Liabilities already exist!!");
            redirect('LiabilitiesController/liabilities_index');
        }
    }
    
     public function add_subLiabilities() {
        $Liabilities = $this->input->post('Liabilities');
        $subLiabilities = $this->input->post('subLiabilities');
        
      
        $data_liabilities = array('liabilities_id' => $Liabilities, 'subliabilities_name' => $subLiabilities);
        $result = $this->liabilities->liabilities_check($Liabilities, $this->user_id);  
        if ($result == FALSE) {
            $this->liabilities->add_subLiabilities($data_liabilities);
            $this->session->set_flashdata('SUCCESSMSGsubLiabilities', "Sub Liabilities added successfully!!");
            redirect('LiabilitiesController/liabilities_index');
        } else {
            $this->session->set_flashdata('INFOMSGsubLiabilities', "Sub Liabilities already exist!!");
            redirect('LiabilitiesController/liabilities_index');
        }
    }
    
      public function delete_liabilities_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->liabilities->delete_liabilities_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Liabilities deleted successfully!!");
            redirect('LiabilitiesController/liabilities_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Liabilities not deleted successfully!!");
            redirect('LiabilitiesController/liabilities_index');
        }
    }
    
     public function delete_subliabilities_by_id() {
       
        $id = $this->uri->segment(3);
        $result = $this->liabilities->delete_subliabilities_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Liabilities deleted successfully!!");
            redirect('LiabilitiesController/liabilities_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Liabilities not deleted successfully!!");
            redirect('LiabilitiesController/liabilities_index');
        }
    }
    
       public function get_liabilities() {
        
         $liabilities_result = $this->liabilities->get_liabilities($this->user_id);
         
       echo json_encode($liabilities_result);
    }
    
    
        public function get_liabilities_id() {
        
        $liabilities_id = $this->input->post('Liabilities_id');
     
        $result = $this->liabilities->get_liabilities_id($liabilities_id, $this->user_id);
        echo json_encode($result);
    }

}
