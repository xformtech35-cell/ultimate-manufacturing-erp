<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AssetbalancesheetController extends MY_Controller {

    protected $user_id;
    function __construct() {
        parent::__construct();

        
        $this->load->library('session');
        $this->load->model('assetbalancesheet', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? null;
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

 
    public function asset_index() {
           $data['asset_name'] = $this->assetbalancesheet->get_asset_name($this->user_id);
        $data['asset_result'] = $this->assetbalancesheet->get_asset($this->user_id);
         $data['subasset_result'] = $this->assetbalancesheet->get_subasset($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('assetbalancesheet/add_asset',$data);
    }
    
    public function add_asset() {
        $asset = $this->input->post('asset');
        $data_asset = array('asset' => $asset, 'uid' => $this->user_id);
        $result = $this->assetbalancesheet->asset_check($asset, $this->user_id);  
        if ($result == FALSE) {
            $this->assetbalancesheet->add_asset($data_asset);
            $this->session->set_flashdata('SUCCESSMSGAsset', "Asset added successfully!!");
            redirect('AssetbalancesheetController/asset_index');
        } else {
            $this->session->set_flashdata('INFOMSGAsset', "Asset already exist!!");
            redirect('AssetbalancesheetController/asset_index');
        }
    }
    
    
     public function add_subasset() {
        
        $asset = $this->input->post('asset');
         $subasset = $this->input->post('subasset');
    
        $data_asset = array('asset_id' => $asset, 'subasset_name' => $subasset);
      //  print_r($data_asset);die();
        $result = $this->assetbalancesheet->asset_check($asset, $this->user_id);  
        if ($result == FALSE) {
            $this->assetbalancesheet->add_subasset($data_asset);
            $this->session->set_flashdata('SUCCESSMSGSubasset', "SubAsset added successfully!!");
            redirect('AssetbalancesheetController/asset_index');
        } else {
            $this->session->set_flashdata('INFOMSGSubasset', "SubAsset already exist!!");
            redirect('AssetbalancesheetController/asset_index');
        }
    }
    
      public function delete_asset_by_id() {
       
        $id = $this->uri->segment(3);
        $result = $this->assetbalancesheet->delete_asset_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Asset deleted successfully!!");
            redirect('AssetbalancesheetController/asset_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Asset not deleted successfully!!");
            redirect('AssetbalancesheetController/asset_index');
        }
    }
    
     public function delete_subasset_by_id() {
     
        $id = $this->uri->segment(3);
      
        $result = $this->assetbalancesheet->delete_subasset_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "SubAsset deleted successfully!!");
            redirect('AssetbalancesheetController/asset_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "SubAsset not deleted successfully!!");
            redirect('AssetbalancesheetController/asset_index');
        }
    }
    
    
    
    
    public function get_asset() {
         $asset_result = $this->assetbalancesheet->get_asset($this->user_id);
       echo json_encode($asset_result);
    }
    
    
        public function get_subasset_id() {
           
          
        $asset_id = $this->input->post('asset_id');
        $result = $this->assetbalancesheet->get_subasset_id($asset_id, $this->user_id);
        echo json_encode($result);
    }


}
