<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AssetController extends MY_Controller {

    protected $user_id;
    function __construct() {
        parent::__construct();

        
        $this->load->library('session');
        $this->load->model('asset', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? null;
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

 
    public function asset_index() {
       
        $data['asset_result'] = $this->asset->get_asset($this->user_id);
       
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        
        
        //  var_dump($data);
        // die();
        $this->load->view('asset/add_asset',$data);
    }
    
    public function add_asset() {
        $asset = $this->input->post('asset');
        $data_asset = array('asset' => $asset, 'uid' => $this->user_id);
        $result = $this->asset->asset_check($asset, $this->user_id);  
        if ($result == FALSE) {
            $this->asset->add_asset($data_asset);
            $this->session->set_flashdata('SUCCESSMSG', "Assets added successfully!!");
            redirect('AssetController/asset_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Assets already exist!!");
            redirect('AssetController/asset_index');
        }
    }
    
    
      public function delete_asset_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->asset->delete_asset_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Moc deleted successfully!!");
            redirect('AssetController/asset_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Moc not deleted successfully!!");
            redirect('AssetController/asset_index');
        }
    }
    
    
     public function get_asset() {
       $asset_result = $this->asset->get_asset($this->user_id);
       echo json_encode($asset_result);
    }

    
    
    public function asset_sub_category_index() {
        $data['asset_result'] = $this->asset->get_asset($this->user_id);
        $data['asset_sub_category'] = $this->asset->get_asset_sub_category($this->user_id);
        echo "test";
        die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('asset/add_asset_sub_category',$data);
    }
    
    public function add_asset_sub_category() {
          $asset = $this->input->post('asset');
        $asset_sub_category = $this->input->post('asset_sub_category');
        $data_asset_sub_category = array('asset' => $asset, 'asset_sub_category' => $asset_sub_category, 'uid' => $this->user_id);
       // print_r($data_asset_sub_category); die();
        $result = $this->asset->asset_sub_category_check($asset_sub_category, $this->user_id);  
        if ($result == FALSE) {
            $this->asset->add_asset_sub_category($data_asset_sub_category);
            $this->session->set_flashdata('SUCCESSMSG', "Assets added successfully!!");
            redirect('AssetController/asset_sub_category_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Assets already exist!!");
            redirect('AssetController/asset_sub_category_index');
        }
    }
    
    
      public function delete_asset_sub_category_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->asset->delete_asset_sub_category_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Asset deleted successfully!!");
            redirect('AssetController/asset_sub_category_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Asset not deleted successfully!!");
            redirect('AssetController/asset_sub_category_index');
        }
    }
    
    
     public function get_asset_sub_category() {
       $asset_result = $this->asset->get_asset_sub_category($this->user_id);
       echo json_encode($asset_result);
    }
}
