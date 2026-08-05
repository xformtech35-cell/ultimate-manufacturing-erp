<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class UnitController extends MY_Controller {

    protected $user_id;
    function __construct() {
        parent::__construct();

        
        $this->load->library('session');
        $this->load->model('units', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

 
    public function unit_index() {
       
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('unit/add_unit',$data);
    }
    
    public function add_unit() {
        $unit = $this->input->post('unit');
        $data_units = array('unit' => $unit, 'uid' => $this->user_id);
        $result = $this->units->unit_check($unit, $this->user_id);  
        if ($result == FALSE) {
            $this->units->add_unit($data_units);
            $this->session->set_flashdata('SUCCESSMSG', "Unit added successfully!!");
            redirect('UnitController/unit_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Unit already exist!!");
            redirect('UnitController/unit_index');
        }
    }
    
    
      public function delete_unit_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->units->delete_unit_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Unit deleted successfully!!");
            redirect('UnitController/unit_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Unit not deleted successfully!!");
            redirect('UnitController/unit_index');
        }
    }
    
    
       public function get_units() {
         $unit_result = $this->units->get_units($this->user_id);
       echo json_encode($unit_result);
    }



    public function get_unit_name() {
        $unit_result = $this->units->get_unit_name($this->user_id);
      echo json_encode($unit_result);
   }


}
