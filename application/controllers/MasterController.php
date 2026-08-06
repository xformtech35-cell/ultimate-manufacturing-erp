<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MasterController extends MY_Controller {

    function __construct() {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('master', '', TRUE);
         
        $this->load->library('form_validation');
        if (!$this->session->userdata('session_data_head')) {
            redirect('LoginController/logout');
        }
    }

    public function index() {
        $data['result'] = $this->guest->get_guest();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('guest/add_guest', $data);
    }
 
    public function view_category() {
//        $data['location'] = $this->guest->get_location();
        $data['category'] = $this->master->get_categories();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/view_category', $data);
    }
 
    public function add_category_form() {
         
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/add_category_form');
    }
    
    public function add_category() {
        $category_name = strtoupper($this->input->post('category_name'));
        $data_category = array('category_name' => $category_name);
        $result = $this->master->add_category($data_category);

        if ($result != FALSE) {
            $this->session->set_flashdata('SUCCESSMSG', "Category Added Successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Category Not Added Successfully!!");
        }
        redirect('MasterController/view_category');
    }

    public function get_category_by_id() {
        $id = $this->uri->segment(3);
        //$data['problem_categories'] = $this->supervision->get_problem_categories();
        $data['category'] = $this->master->get_category_by_id($id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/edit_category_form', $data);
    }

    public function edit_category() {
        $category_id = $this->input->post('category_id');
        $category_name = strtoupper($this->input->post('category_name'));

        $data_category = array('category_name' => $category_name);

        $result = $this->master->edit_category($category_id, $data_category);

        if ($result != FALSE) {
            $this->session->set_flashdata('SUCCESSMSG', "Category Updated successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Category Not Updated!!");
        }
        redirect('MasterController/view_category');
    }

    public function delete_category_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->master->delete_category_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Category Deleted Successfully!!");
            redirect('MasterController/view_category');
        } else {
            $this->session->set_flashdata('INFOMSG', "Category Not Deleted Successfully!!");
            redirect('MasterController/view_category');
        }
    }
    
    public function view_product(){
        $data['products'] = $this->master->get_products();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/view_product', $data);
    }

    public function add_product_form() {
        $data['category'] = $this->master->get_categories();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/add_product_form',$data);
    }
    
    public function add_product() {
        $category_id_fk = $this->input->post('category_id_fk');
        $product_master_name = strtoupper($this->input->post('product_master_name'));

        $data_product = array('product_master_name' => $product_master_name, 'category_id_fk' => $category_id_fk);

        $result = $this->master->add_product($data_product);

        if ($result != FALSE) {
            $this->session->set_flashdata('SUCCESSMSG', "Product added successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Product already exist!!");
        }
        redirect('MasterController/view_product');
//        $this->index();
    }

    public function get_product_by_id() {
        $id = $this->uri->segment(3);
        $data['product'] = $this->master->get_product_by_id($id);
        $data['category'] = $this->master->get_categories();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/edit_product_form', $data);
    }
    
    public function edit_product() {
        $product_master_id = $this->input->post('product_master_id');
        $category_id_fk = $this->input->post('category_id_fk');
        $product_master_name = $this->input->post('product_master_name');

        $data_product = array('category_id_fk' => $category_id_fk, 'product_master_name' => $product_master_name);
        $result = $this->master->edit_product($product_master_id, $data_product);
        if ($result != FALSE) {
            $this->session->set_flashdata('SUCCESSMSG', "Product Updated Successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Product Not Updated Successfully!!");
        }
        redirect('MasterController/view_product');
    }
    
    public function delete_product_by_id() {
        $id = $this->uri->segment(3);
        $session_data_head = $this->session->userdata('session_data_head');
        $res = $session_data_head['result'] ?? [];
        $role_name = strtolower($res['role_name'] ?? '');
        $role_id   = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id   = (int)($res['user_id'] ?? 0);

        if ($role_name !== 'admin' && $role_id !== 1 && $user_id !== 1) {
            redirect('DeleteApprovalController/request_delete?item_id=' . urlencode($id) . '&module=item_code_master&redirect_url=' . urlencode('MasterController/view_product'));
            return;
        }

        $result = $this->master->delete_product_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Product Deleted Successfully!!");
            redirect('MasterController/view_product');
        } else {
            $this->session->set_flashdata('INFOMSG', "Product Not Deleted Successfully!!");
            redirect('MasterController/view_product');
        }
    }
    
    public function view_raw_items(){
        $data['raw_items'] = $this->master->get_raw_items();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/view_raw_items', $data);
    }
    
    public function add_raw_itms_form() {
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/add_raw_item_form');
    }
    
    public function add_raw_item() {
        $raw_item_master_name = strtoupper($this->input->post('raw_item_master_name'));
        $data_raw_item = array('raw_item_master_name' => $raw_item_master_name);
        $result = $this->master->add_raw_item($data_raw_item);

        if ($result != FALSE) {
            $this->session->set_flashdata('SUCCESSMSG', "Raw Item Added Successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Raw Item Not Added Successfully!!");
        }
        redirect('MasterController/view_raw_items');
    }
    
    public function get_raw_item_by_id() {
        $id = $this->uri->segment(3);
        //$data['problem_categories'] = $this->supervision->get_problem_categories();
        $data['raw_item'] = $this->master->get_raw_item_by_id($id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('master/edit_raw_item_form', $data);
    }

    public function edit_raw_item() {
        $raw_item_master_id = $this->input->post('raw_item_master_id');
        $raw_item_master_name = strtoupper($this->input->post('raw_item_master_name'));

        $data_raw_item = array('raw_item_master_name' => $raw_item_master_name);

        $result = $this->master->edit_raw_item($raw_item_master_id, $data_raw_item);

        if ($result != FALSE) {
            $this->session->set_flashdata('SUCCESSMSG', "Raw Item Updated successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Raw Item Not Updated!!");
        }
        redirect('MasterController/view_raw_items');
    }

    public function delete_raw_item_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->master->delete_raw_item_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Raw Item Deleted Successfully!!");
            redirect('MasterController/view_raw_items');
        } else {
            $this->session->set_flashdata('INFOMSG', "Raw Item Not Deleted Successfully!!");
            redirect('MasterController/view_raw_items');
        }
    }
    
    
    
}
