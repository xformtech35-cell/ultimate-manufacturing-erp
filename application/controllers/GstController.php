<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class GstController extends MY_Controller {

    protected $user_id;
    function __construct() {
        parent::__construct();

        
        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('estimate', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('gst', '', TRUE);
        $this->load->library('form_validation');
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function gst_index() {
       
        $data['gst_class_result'] = $this->gst->get_gst_classes($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('gst/add_gst_classes',$data);
    }
    
    public function add_gst_class() {
        $gst = $this->input->post('gst_class');
        
        $append_per_to_gst_class = $gst."%";
        $data_gst = array('gst_class' => $append_per_to_gst_class, 'uid' => $this->user_id);
        $result = $this->gst->gst_check($append_per_to_gst_class, $this->user_id);  
        if ($result == FALSE) {
            $this->gst->add_gst_class($data_gst);
            $this->session->set_flashdata('SUCCESSMSG', "GST Class added successfully!!");
            redirect('GstController/gst_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "GST Class already exist!!");
            redirect('GstController/gst_index');
        }
    }
    public function delete_gst_class_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->gst->delete_gst_class_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "GST Class deleted successfully!!");
            redirect('GstController/gst_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "GST Class not deleted successfully!!");
            redirect('GstController/gst_index');
        }
    }
    
    
   

}
