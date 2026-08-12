<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OrderConfirmationController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('orderconfirmation', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('paymentterm', '', TRUE);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? NULL;

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }

        $permissions = $session_data_head['permission'] ?? array();
        $role_name = strtolower($session_data_head['result']['role_name'] ?? '');
        $is_admin = ($role_name === 'admin' || ($session_data_head['result']['role'] ?? 0) == 1);

        if (!$is_admin && !in_array('OrderConfirmation', $permissions)) {
            $this->session->set_flashdata('ERRORMSG', 'You do not have permission to access Order Acceptance.');
            redirect('Home/index');
        }
    }

    public function index() {
        $str = $this->input->get('str');
        
        if($str == "All") {  
            $data['orderconfirmations'] = $this->orderconfirmation->get_orderconfirmations($this->user_id);
        } else {
            $month_year = date('M-y');
            $data['orderconfirmations'] = $this->orderconfirmation->get_monthyearwise_record($month_year, $this->user_id);
        }
        
        $data['oc_count'] = $this->orderconfirmation->get_orderconfirmation_count($this->user_id);
        
        // Status counts
        $draft_status = 1;
        $data['oc_draft_count'] = $this->orderconfirmation->get_orderconfirmation_status_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['oc_sent_count'] = $this->orderconfirmation->get_orderconfirmation_status_count($sent_status, $this->user_id);
        $accepted_status = 3;
        $data['oc_accepted_count'] = $this->orderconfirmation->get_orderconfirmation_status_count($accepted_status, $this->user_id);
        $rejected_status = 4;
        $data['oc_rejected_count'] = $this->orderconfirmation->get_orderconfirmation_status_count($rejected_status, $this->user_id);
        
        $data['supplier_result'] = $this->orderconfirmation->get_supplier($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['oc_id'] = $this->orderconfirmation->get_last_oc_number($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('orderconfirmation/view_order_confirmation', $data);
    }

    public function create_order_confirmation() {
        $data['supplier_result'] = $this->orderconfirmation->get_supplier($this->user_id);
        $data['customer_result'] = $this->orderconfirmation->get_customers($this->user_id);
        $data['oc_id'] = $this->orderconfirmation->get_last_oc_number($this->user_id);
        $data['paymentterm_result'] = $this->paymentterm->get_paymentterm($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['project_code_result'] = $this->orderconfirmation->get_project_code($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('orderconfirmation/create_order_confirmation', $data);
    }

    public function show_order_confirmation() {
        $segments = $this->uri->segment_array();
        $number = implode('/', array_slice($segments, 2));
        
        $data['oc'] = $this->orderconfirmation->get_orderconfirmation_by_number($number, $this->user_id);
        if (empty($data['oc'])) {
            $this->session->set_flashdata('INFOMSG', 'Order Acceptance record not found.');
            redirect('OrderConfirmationController/index');
            return;
        }
        $data['oc_detail'] = $this->orderconfirmation->get_orderconfirmation_detail($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('orderconfirmation/show_order_confirmation', $data);
    }

    public function edit_order_confirmation_details() {
        $segments = $this->uri->segment_array();
        $number = implode('/', array_slice($segments, 2));
        
        $data['oc'] = $this->orderconfirmation->get_orderconfirmation_by_number($number, $this->user_id);
        if (empty($data['oc'])) {
            $this->session->set_flashdata('INFOMSG', 'Order Acceptance record not found.');
            redirect('OrderConfirmationController/index');
            return;
        }
        $data['oc_detail'] = $this->orderconfirmation->get_orderconfirmation_detail($number, $this->user_id);
        $data['supplier_result'] = $this->orderconfirmation->get_supplier($this->user_id);
        $data['customer_result'] = $this->orderconfirmation->get_customers($this->user_id);
        $data['paymentterm_result'] = $this->paymentterm->get_paymentterm($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['project_code_result'] = $this->orderconfirmation->get_project_code($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('orderconfirmation/edit_order_confirmation', $data);
    }

    public function save_order_confirmation() {
        $number = $this->input->post('number');
        $supplier_id = $this->input->post('supplier_id');
        $customer_id = $this->input->post('customer_id');
        $po_reference = $this->input->post('po_reference');
        $po_date = $this->input->post('po_date');
        $subject = $this->input->post('subject');
        $date = $this->input->post('date');
        $delivery_date = $this->input->post('delivery_date');
        $payment_terms = $this->input->post('payment_terms');
        $price_basis = $this->input->post('price_basis');
        $transportation_charges = $this->input->post('transportation_charges');
        $service_charges = $this->input->post('service_charges');
        $warranty = $this->input->post('warranty');
        $salesorder_id = $this->input->post('salesorder_id');
        $project_code = $this->input->post('project_code') ?? '';
        $remarks = $this->input->post('remarks');
        $sub_total = $this->input->post('sub_total');
        $tax_amount = $this->input->post('tax_amount');
        $total_amount = $this->input->post('total_amount');
        
        $oc_data = array(
            'number_fk'              => $number,
            'supplier_id'            => $supplier_id,
            'customer_id'            => $customer_id,
            'po_reference'           => $po_reference,
            'po_date'                => $po_date,
            'subject'                => $subject,
            'date'                   => $date,
            'delivery_date'          => $delivery_date,
            'payment_terms'          => $payment_terms,
            'price_basis'            => $price_basis,
            'transportation_charges' => $transportation_charges,
            'service_charges'        => $service_charges,
            'warranty'               => $warranty,
            'salesorder_id'          => $salesorder_id,
            'project_code'           => $project_code,
            'remarks'                => $remarks,
            'sub_total'              => $sub_total,
            'tax_amount'             => $tax_amount,
            'total'                  => $total_amount,
            'uid'                    => $this->user_id,
            'status'                 => 1
        );

        if($this->orderconfirmation->add_orderconfirmation($oc_data)) {
            // Save OC details (line items)
            $descriptions = $this->input->post('description');
            $hsn_codes = $this->input->post('hsn_code');
            $quantities = $this->input->post('quantity');
            $units = $this->input->post('unit');
            $unit_prices = $this->input->post('unit_price');
            $tax_rates = $this->input->post('tax_rate');
            $tax_amounts = $this->input->post('tax_amount_item');
            $amounts = $this->input->post('amount');
            
            if(!empty($descriptions)) {
                foreach($descriptions as $key => $description) {
                    if(!empty($description) || !empty($quantities[$key])) {
                        $oc_detail = array(
                            'number' => $number,
                            'description' => $description,
                            'hsn_code' => isset($hsn_codes[$key]) ? $hsn_codes[$key] : '',
                            'quantity' => isset($quantities[$key]) ? $quantities[$key] : 0,
                            'unit' => isset($units[$key]) ? $units[$key] : '',
                            'unit_price' => isset($unit_prices[$key]) ? $unit_prices[$key] : 0,
                            'tax_rate' => isset($tax_rates[$key]) ? $tax_rates[$key] : 0,
                            'tax_amount' => isset($tax_amounts[$key]) ? $tax_amounts[$key] : 0,
                            'amount' => isset($amounts[$key]) ? $amounts[$key] : 0,
                            'uid' => $this->user_id
                        );
                        $this->orderconfirmation->add_orderconfirmation_detail($oc_detail);
                    }
                }
            }
            
            $this->session->set_flashdata('SUCCESSMSG', 'Order Acceptance Saved Successfully');
            redirect('OrderConfirmationController/show_order_confirmation/' . $number);
        } else {
            $this->session->set_flashdata('INFOMSG', 'Error Saving Order Acceptance');
            redirect('OrderConfirmationController/create_order_confirmation');
        }
    }

    public function update_order_confirmation() {
        $number = $this->input->post('number');
        $supplier_id = $this->input->post('supplier_id');
        $customer_id = $this->input->post('customer_id');
        $po_reference = $this->input->post('po_reference');
        $po_date = $this->input->post('po_date');
        $subject = $this->input->post('subject');
        $date = $this->input->post('date');
        $delivery_date = $this->input->post('delivery_date');
        $payment_terms = $this->input->post('payment_terms');
        $price_basis = $this->input->post('price_basis');
        $transportation_charges = $this->input->post('transportation_charges');
        $service_charges = $this->input->post('service_charges');
        $warranty = $this->input->post('warranty');
        $salesorder_id = $this->input->post('salesorder_id');
        $project_code = $this->input->post('project_code') ?? '';
        $remarks = $this->input->post('remarks');
        $sub_total = $this->input->post('sub_total');
        $tax_amount = $this->input->post('tax_amount');
        $total_amount = $this->input->post('total_amount');
        
        $oc_data = array(
            'supplier_id'            => $supplier_id,
            'customer_id'            => $customer_id,
            'po_reference'           => $po_reference,
            'po_date'                => $po_date,
            'subject'                => $subject,
            'date'                   => $date,
            'delivery_date'          => $delivery_date,
            'payment_terms'          => $payment_terms,
            'price_basis'            => $price_basis,
            'transportation_charges' => $transportation_charges,
            'service_charges'        => $service_charges,
            'warranty'               => $warranty,
            'salesorder_id'          => $salesorder_id,
            'project_code'           => $project_code,
            'remarks'                => $remarks,
            'sub_total'              => $sub_total,
            'tax_amount'             => $tax_amount,
            'total'                  => $total_amount
        );

        if($this->orderconfirmation->update_orderconfirmation($number, $oc_data, $this->user_id)) {
            // Delete existing details and re-insert
            $this->orderconfirmation->delete_orderconfirmation_detail($number, $this->user_id);
            
            // Save OC details (line items)
            $descriptions = $this->input->post('description');
            $hsn_codes = $this->input->post('hsn_code');
            $quantities = $this->input->post('quantity');
            $units = $this->input->post('unit');
            $unit_prices = $this->input->post('unit_price');
            $tax_rates = $this->input->post('tax_rate');
            $tax_amounts = $this->input->post('tax_amount_item');
            $amounts = $this->input->post('amount');
            
            if(!empty($descriptions)) {
                foreach($descriptions as $key => $description) {
                    if(!empty($description) || !empty($quantities[$key])) {
                        $oc_detail = array(
                            'number' => $number,
                            'description' => $description,
                            'hsn_code' => isset($hsn_codes[$key]) ? $hsn_codes[$key] : '',
                            'quantity' => isset($quantities[$key]) ? $quantities[$key] : 0,
                            'unit' => isset($units[$key]) ? $units[$key] : '',
                            'unit_price' => isset($unit_prices[$key]) ? $unit_prices[$key] : 0,
                            'tax_rate' => isset($tax_rates[$key]) ? $tax_rates[$key] : 0,
                            'tax_amount' => isset($tax_amounts[$key]) ? $tax_amounts[$key] : 0,
                            'amount' => isset($amounts[$key]) ? $amounts[$key] : 0,
                            'uid' => $this->user_id
                        );
                        $this->orderconfirmation->add_orderconfirmation_detail($oc_detail);
                    }
                }
            }
            
            $this->session->set_flashdata('SUCCESSMSG', 'Order Acceptance Updated Successfully');
            redirect('OrderConfirmationController/show_order_confirmation/' . $number);
        } else {
            $this->session->set_flashdata('INFOMSG', 'Error Updating Order Acceptance');
            redirect('OrderConfirmationController/edit_order_confirmation_details/' . $number);
        }
    }

    public function delete_order_confirmation() {
        $segments = $this->uri->segment_array();
        $number = implode('/', array_slice($segments, 2));
        
        if($this->orderconfirmation->delete_orderconfirmation_by_number($number, $this->user_id)) {
            $this->session->set_flashdata('SUCCESSMSG', 'Order Acceptance Deleted Successfully');
        } else {
            $this->session->set_flashdata('INFOMSG', 'Error Deleting Order Acceptance');
        }
        redirect('OrderConfirmationController/index');
    }

    public function update_status() {
        $segments = array_values($this->uri->segment_array());
        $status = array_pop($segments);
        $number = implode('/', array_slice($segments, 2));
        
        if($this->orderconfirmation->update_status($number, $status, $this->user_id)) {
            $this->session->set_flashdata('SUCCESSMSG', 'Status Updated Successfully');
        } else {
            $this->session->set_flashdata('INFOMSG', 'Error Updating Status');
        }
        redirect('OrderConfirmationController/show_order_confirmation/' . $number);
    }

    public function print_order_confirmation() {
        $segments = $this->uri->segment_array();
        $number = implode('/', array_slice($segments, 2));
        
        $data['oc'] = $this->orderconfirmation->get_orderconfirmation_by_number($number, $this->user_id);
        $data['oc_detail'] = $this->orderconfirmation->get_orderconfirmation_detail($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        $this->load->view('orderconfirmation/print_order_confirmation', $data);
    }

    public function print_oa_letter() {
        $segments = $this->uri->segment_array();
        $number = implode('/', array_slice($segments, 2));
        
        $data['oc'] = $this->orderconfirmation->get_orderconfirmation_by_number($number, $this->user_id);
        $data['oc_detail'] = $this->orderconfirmation->get_orderconfirmation_detail($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        $this->load->view('orderconfirmation/print_oa_letter', $data);
    }

    public function create_from_so() {
        $segments = $this->uri->segment_array();
        $so_number = implode('/', array_slice($segments, 2));
        $this->load->model('salesorder');
        
        $so_header = $this->salesorder->get_salesorder_by_number($so_number, $this->user_id);
        $so_details = $this->salesorder->get_salesorder_detail($so_number, $this->user_id);
        
        $data['so_header'] = $so_header;
        $data['so_details'] = $so_details;
        $data['supplier_result'] = $this->orderconfirmation->get_supplier($this->user_id);
        $data['customer_result'] = $this->orderconfirmation->get_customers($this->user_id);
        $data['oc_id'] = $this->orderconfirmation->get_last_oc_number($this->user_id);
        $data['paymentterm_result'] = $this->paymentterm->get_paymentterm($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['project_code_result'] = $this->orderconfirmation->get_project_code($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('orderconfirmation/create_order_confirmation', $data);
    }
}
?>

