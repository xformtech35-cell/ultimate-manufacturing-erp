<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PlanningController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('master', '', TRUE);
        $this->load->model('planning', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->library('form_validation');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function index() {
        $data['delivered_items'] = $this->planning->get_delivered_items();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/view_delivered_item', $data);
    }
    
    public function get_row_item_batchwise() {
        $batch_no = $this->uri->segment(3);
        $data['delivered_items'] = $this->planning->get_row_item_batchwise($batch_no);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/view_delivered_item_batchwise', $data);
    }

    
    public function add_delivered_item_form() {
        $data['batch'] = $this->planning->get_batch_no();
        $data['raw_items'] = $this->master->get_raw_items();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/add_delivered_item_form', $data);
    }

    public function add_delivered_item() {
        $edit = 0;
        $msg = 'Added';
        if ($this->input->post('raw_item_delivery_id')) {
            $raw_item_delivery_id = strtoupper($this->input->post('raw_item_delivery_id'));
            $edit = 1;
            $msg = 'Updated';
        }
        $raw_item_name = $this->input->post('raw_item_name');
        $batch = $this->input->post('batch');
        $batch_description = $this->input->post('batch_description');
        
        $raw_item_qty = $this->input->post('raw_item_qty');
        $raw_item_unit = $this->input->post('raw_item_unit');
        $raw_item_qty_hide = $this->input->post('raw_item_qty_hide');
        $today = date('Y-m-d',  strtotime($this->input->post('batch_date')));
        
        if ($edit == 0) {
                
            for ($i = 0; $i < count($raw_item_name); $i++) {
                $data_delivered_item = array('raw_item_name' => $raw_item_name[$i], 
                    'raw_item_qty' => $raw_item_qty[$i], 
                    'raw_item_unit' => $raw_item_unit[$i], 'raw_item_deliver_date' => $today,
                    'batch' => $batch, 'batch_description' => $batch_description);
                    $result = $this->planning->add_delivered_item($data_delivered_item);
                    
                    //Update row stock quantiy
                    $raw_item_id = $this->planning->get_row_stock_item_id($raw_item_name[$i]);
                    $raw_item_stock = $this->planning->get_row_stock_item_count($raw_item_id['raw_item_master_id']);
                    $datas = array(
                        'raw_item_stock' => max(0, $raw_item_stock['raw_item_stock'] - $raw_item_qty[$i]),
                    );
                   $this->planning->update_row_item_stock($raw_item_id['raw_item_master_id'], $datas);
            }
           
        } else {
//            for($i=0;$i<count($raw_item_name);$i++){
            $data_delivered_item = array('raw_item_name' => $raw_item_name, 'raw_item_qty' => $raw_item_qty, 
                'raw_item_unit' => $raw_item_unit,);
            $result = $this->planning->update_delivered_item($raw_item_delivery_id, $data_delivered_item);
            
                    //Update row stock quantiy
                    $raw_item_id = $this->planning->get_row_stock_item_id($raw_item_name);
                    $raw_item_stock = $this->planning->get_row_stock_item_count($raw_item_id['raw_item_master_id']);
                    $qty = max(0, intval(($raw_item_stock['raw_item_stock'] +$raw_item_qty_hide) - $raw_item_qty));
                    $datas = array(
                        'raw_item_stock' => $qty
                    );
                   $this->planning->update_row_item_stock($raw_item_id['raw_item_master_id'], $datas);
            
//            }
        }
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Delivered Item " . $msg . " Successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Delivered Item Not " . $msg . " Successfully!!");
        }
        redirect('PlanningController/index');
    }

    public function get_raw_item_by_id() {
        $dept_id = $this->uri->segment(3);
        $data['raw_items'] = $this->master->get_raw_items();
        $data['delivered_item'] = $this->planning->get_raw_item_by_id($dept_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/edit_delivered_item_form', $data);
    }

    public function delete_raw_item_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->planning->delete_raw_item_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Delivered Item Deleted Successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Delivered Item Not Deleted Successfully!!");
        }
        redirect('PlanningController/index');
    }

    public function finished_products() {
        $data['finished_product'] = $this->planning->get_finished_products();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/view_finished_products', $data);
    }

    public function get_product_by_batch_wise() {
        $batch_fk = $this->uri->segment(3);
        $data['finished_product'] = $this->planning->get_product_by_batch_wise($batch_fk);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/view_finished_products_batch_wise', $data);
    }
    
    
    public function add_finished_product_form() {
        $data['products'] = $this->master->get_products();
        $data['batch'] = $this->planning->get_all_batch_numbers();
        
        // Load Job Orders for selection
        $this->db->select('*');
        $this->db->from('joborder_total');
        $this->db->where('uid', $this->user_id);
        $data['joborders'] = $this->db->get()->result_array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/add_finished_product_form', $data);
    }

    public function ajax_get_jo_details() {
        header('Content-Type: application/json');
        $jo_number = $this->input->post('jo_number');
        if (empty($jo_number)) {
            echo json_encode(array('success' => false, 'message' => 'No JO number provided'));
            return;
        }

        $jo_total = $this->db->where('number_fk', $jo_number)->get('joborder_total')->row_array();
        $jo_items = $this->db->where('number', $jo_number)->get('joborder')->result_array();

        if (empty($jo_items)) {
            echo json_encode(array('success' => false, 'message' => 'Job Order details not found'));
            return;
        }

        echo json_encode(array(
            'success' => true,
            'jo' => $jo_total,
            'items' => $jo_items
        ));
    }

    public function get_products_for_json() {
        $data = $this->master->get_raw_items();
        echo json_encode($data);
    }

    public function add_finished_product() {
        $edit = 0;
        $msg = 'Added';

        if ($this->input->post('product_id')) {
            $product_id = $this->input->post('product_id');
            $edit = 1;
            $msg = 'Updated';
        }
        $product_name = $this->input->post('product_name');
        $jo_number = $this->input->post('jo_number') ?: null;
        
        $batch_fk = $this->input->post('batch_fk');

        $product_qty = $this->input->post('product_qty');
        $hidden_product_qty = $this->input->post('hidden_product_qty');
        
        $product_unit = $this->input->post('product_unit');
        $today = date('Y-m-d');

        //get inventory id by product name in inventory
        $inventory_data = $this->planning->get_inventory_id_by_code($product_name);
        
        $inventory_id = $inventory_data['inventory_id'];
        $stock = $inventory_data['stock'];
        

        if ($edit == 0) {
            
            $data_inventory = array('prod_description' => $product_name, 'code' => $product_name,
                'stock' => $product_qty, 'date_added' => $today, 'date_modified' => $today, 'uid' => $this->user_id);

            $inventory_check = $this->inventory->inventory_code_check($product_name, $this->user_id);
            
            if ($inventory_check == FALSE) {
                $this->inventory->add_inventory($data_inventory);
            } else {
                
                $product_qty1 = intval($product_qty + $stock); 
                $data_inventory = array('code' => $product_name,
                    'stock' => $product_qty1, 'date_modified' => $today, 'uid' => $this->user_id);

                $result = $this->inventory->edit_inventory($data_inventory, $inventory_id, $this->user_id);
            }
            
            
            $data_finished_product = array('product_name' => $product_name, 'product_qty' => $product_qty, 'product_unit' => $product_unit, 'product_finished_date' => $today, 'batch_fk' => $batch_fk, 'jo_number' => $jo_number);
            $result = $this->planning->add_finished_product($data_finished_product);
            $batch_status = $this->input->post('batch_status');
            if ($batch_status == 1) {
                $data_batch = array('batch_status' => 1);
                $this->planning->update_batch_status($batch_fk, $data_batch);
            }
            
        } else {
            
            $edit_product_qty  = max(0, intval(($product_qty + $stock ) - ($hidden_product_qty))) ; 
            $data_inventory = array('code' => $product_name,
                    'stock' => $edit_product_qty, 'date_modified' => $today, 'uid' => $this->user_id);

                $this->inventory->edit_inventory($data_inventory, $inventory_id, $this->user_id);
                
            $data_finished_product = array('product_name' => $product_name, 'product_qty' => $product_qty, 'product_unit' => $product_unit, 'batch_fk' => $batch_fk, 'jo_number' => $jo_number);
            
            $result = $this->planning->update_finished_product($product_id, $data_finished_product);
            
        }
        if ($result == TRUE) {

            $this->session->set_flashdata('SUCCESSMSG', "Finished Product " . $msg . " Successfully!!");
        } else {

            $this->session->set_flashdata('INFOMSG', "Finished Product Not " . $msg . " Successfully!!");
        }
        redirect('PlanningController/finished_products');
    }

    public function get_product_by_id() {
        $id = $this->uri->segment(3);
        $data['products'] = $this->master->get_products();
        $data['batch'] = $this->planning->get_all_batch_numbers_edit();
        $data['finished_product'] = $this->planning->get_product_by_id($id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/edit_finished_product_form', $data);
    }

    public function delete_product_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->planning->delete_product_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Finished Product Deleted Successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Finished Product Not Deleted Successfully!!");
        }
        redirect('PlanningController/finished_products');
    }
    
    public function raw_deliverd() {
        $data['row_item_name'] = $this->planning->get_row_item_name();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('planning/raw_deliverd', $data);
    }
    
    public function get_total_row_item_delivered() {
        
        $from_date1 = $this->input->post('from_date');
        $to_date1 = $this->input->post('to_date');
        $raw_item_name = $this->input->post('raw_item_name');
        $from_date = date('Y-m-d', strtotime($from_date1));
        $to_date = date('Y-m-d', strtotime($to_date1));

        $data['from_date'] = $from_date1;
        $data['to_date'] = $to_date1;
        //$data['raw_item_name'] = $raw_item_name;
        $data['row_item_qty'] = $this->planning->get_total_row_item_delivered($from_date, $to_date, $raw_item_name);
        //print_r($data);die();
        $this->load->view('planning/view_raw_deliverd', $data);
    }

}
