<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class RollController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('login', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('roll', '', TRUE);
        $this->load->library('form_validation');
        $this->load->library('image_lib');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function add_roll_stock() {
        $data['finish_stock'] = $this->roll->get_finish_goods_stock($this->user_id);
        $data['code_name'] = $this->roll->get_code_list($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('roll_stock/add_roll_stock', $data);
    }

    public function add_finish_goods() {
        $code = $this->input->post('code');
        $roll_weight = $this->input->post('roll_weight');
        $roll_size = $this->input->post('roll_size');
        $bags_created = $this->input->post('bags_created');
        $roll_color = $this->input->post('roll_color');
        $gsm = $this->input->post('gsm');
        $bag_type = $this->input->post('bag_type');
        $bag_size = $this->input->post('bag_size');
        $date_added = date("Y-m-d");
        $data_finish_goods = array('code' => $code, 'roll_weight' => $roll_weight, 'roll_size' => $roll_size, 'bags_created' => $bags_created,
            'roll_color' => $roll_color, 'gsm' => $gsm, 'bag_type' => $bag_type, 'bag_size' => $bag_size,
            'created_date' => $date_added, 'uid' => $this->user_id);

        $stock = $this->roll->get_inventory_stock_count($code, $this->user_id);
        $finish_stock = array(
            'stock' => $stock['stock'] + $bags_created,
        );
        
        $this->db->where('uid', $this->user_id);
        $this->db->where('code', $code);
        $this->db->update('inventory', $finish_stock);

        $result = $this->roll->add_finish_goods($data_finish_goods);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Finish Goods added successfully!!");
            redirect('RollController/add_roll_stock');
        } else {
            $this->session->set_flashdata('INFOMSG', "Finish Goods not added successfully!!");
            redirect('RollController/add_roll_stock');
        }
    }

    public function edit_finish_goods() {
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $roll_weight = $this->input->post('roll_weight');
        $roll_size = $this->input->post('roll_size');
        $bags_created = $this->input->post('bags_created');
        $roll_color = $this->input->post('roll_color');
        $gsm = $this->input->post('gsm');
        $bag_type = $this->input->post('bag_type');
        $bag_size = $this->input->post('bag_size');
        $date_added = date("Y-m-d");
        
        $data_finish_goods = array('code' => $code, 'roll_weight' => $roll_weight, 'roll_size' => $roll_size, 'bags_created' => $bags_created,
            'roll_color' => $roll_color, 'gsm' => $gsm, 'bag_type' => $bag_type, 'bag_size' => $bag_size,
            'created_date' => $date_added, 'uid' => $this->user_id);

        $stock = $this->roll->get_inventory_stock_count($code, $this->user_id);
        
        $get_bags_created = $this->roll->get_finish_stock_count($code, $id, $this->user_id);
       
        $finish_stock = array(
            'stock' => ($stock['stock'] + $get_bags_created['bags_created']) - $bags_created,
        );
        
        $this->db->where('uid', $this->user_id);
        $this->db->where('code', $code);
        $this->db->update('inventory', $finish_stock);
        
        $result = $this->roll->edit_finish_goods($data_finish_goods, $id, $this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Finish goods updated successfully!!");
            redirect('RollController/add_roll_stock');
        } else {
            $this->session->set_flashdata('INFOMSG', "Finish goods not updated successfully!!");
           redirect('RollController/add_roll_stock');
        }
    }

    public function get_finish_goods_by_id() {
        $id = $this->uri->segment(3);
        $data['finish'] = $this->roll->get_finish_goods_by_id($id);
        $data['code_name'] = $this->roll->get_code_list($this->user_id);
        $data['codes1'] = $this->roll->get_codes($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('roll_stock/edit_finish_stock', $data);
    }

    public function delete_finish_stock_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->roll->delete_finish_stock_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Finish Stock deleted successfully!!");
            redirect('RollController/add_roll_stock');
        } else {
            $this->session->set_flashdata('INFOMSG', "Finish Stock not deleted successfully!!");
            redirect('RollController/add_roll_stock');
        }
    }

    public function get_all_products_code() {
        $result = $this->inventory->get_product_part_name($this->user_id);
        echo json_encode($result);
    }

}
