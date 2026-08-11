<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DeliverychallanController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('deliverychallan', '', TRUE);
        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('estimate', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('advance', '', TRUE);
        $this->load->library('form_validation');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);


        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function index() {
      
      //  echo "djbc";die();
        $str = $this->input->get('str');
        if($str=="All" || $str === null){  
            $data['invoices'] = $this->deliverychallan->get_delivery_challans($this->user_id);
           
        }else{
           $month_year = date('M-Y');
           
            $data['invoices'] = $this->deliverychallan->get_monthyearwise_record($month_year, $this->user_id);
        }
        // print_r($data['invoices']);die();
        
        $data['status_result'] = $this->deliverychallan->get_status($this->user_id);
       
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
       
        $data['settings'] = $this->login->get_settings($this->user_id);
       
        $data['invoice_id'] = $this->deliverychallan->get_last_delivery_challan_number($this->user_id);
       
        $data['company_name'] = $this->deliverychallan->get_company_name($this->user_id);
       
        $data['invoice_count'] = $this->deliverychallan->get_delivery_challan_count($this->user_id);
       
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->deliverychallan->get_delivery_challan_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['invoice_sent_count'] = $this->deliverychallan->get_delivery_challan_draft_count($sent_status, $this->user_id);

        
        $session_data_head = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data_head);
         
        $this->load->view('delivery_challan/view_delivery_challan', $data);
    }
    
     
    
    
    public function create_delivery_challan() {
       //echo "dd";die();
        $data['result'] = $this->deliverychallan->get_customer($this->user_id);
        //print_r($data['result']);die();
        $data['invoice_id'] = $this->deliverychallan->get_last_delivery_challan_number($this->user_id);
        
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
      //print_r($data['product_name']);die();
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
      //print_r($data['item_name']);die();
        $data['company_name'] = $this->deliverychallan->get_company_name($this->user_id);
       // print_r($data['company_name']);die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_count'] = $this->deliverychallan->get_delivery_challan_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->deliverychallan->get_delivery_challan_draft_count($draft_status, $this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/create_delivery_challan', $data);
    }
    

    public function create_non_gst_invoice() {
        $data['result'] = $this->invoice->get_customer($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_non_gst_invoice_number($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/create_non_gst_invoice', $data);
    }

    public function create_central_gst_invoice() {
        $data['result'] = $this->deliverychallan->get_customer($this->user_id);
        $data['invoice_id'] = $this->deliverychallan->get_last_delivery_challan_number($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['company_name'] = $this->deliverychallan->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_count'] = $this->deliverychallan->get_delivery_challan_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->deliverychallan->get_delivery_challan_draft_count($draft_status, $this->user_id);


        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/create_central_gst_invoice', $data);
    }

    public function create_central_gst_delivery_challan() {
        $data['result'] = $this->deliverychallan->get_customer($this->user_id);
        $data['invoice_id'] = $this->deliverychallan->get_last_delivery_challan_number($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['company_name'] = $this->deliverychallan->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_count'] = $this->deliverychallan->get_delivery_challan_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->deliverychallan->get_delivery_challan_draft_count($draft_status, $this->user_id);


        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/create_central_gst_delivery_challan', $data);
    }

    public function add_customer() {
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = trim((string) $this->input->post('email'));
        $mobile = trim((string) $this->input->post('mobile'));
        $email = ($email === '') ? null : $email;
        $mobile = ($mobile === '') ? null : $mobile;
        $address = $this->input->post('address');

        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'pancard' => $pancard, 'gst' => $gst,
            'email' => $email, 'mobile' => $mobile, 'address' => $address, 'uid' => $this->user_id);

        $result = $this->deliverychallan->customer_check($company_name);
        //non_gst_check_customer   igst_check_customer  gst_check_customer
        $non_gst_check_customer = $this->input->post('non_gst_check_customer');
        $igst_check_customer = $this->input->post('igst_check_customer');
        $gst_check_customer = $this->input->post('gst_check_customer');

        if ($result == FALSE) {
            $this->deliverychallan->add_customer($data_customer);
            $this->session->set_flashdata('SUCCESSMSG', "Company added successfully!!");

            if ($gst_check_customer) {
                redirect('DeliveryChallanController/create_delivery_challan');
            } else if ($igst_check_customer) {
                redirect('DeliveryChallanController/create_central_gst_invoice');
            } else {
                redirect('DeliveryChallanController/create_non_gst_invoice');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Company already exist!!");
            if ($gst_check_customer) {
                redirect('DeliveryChallanController/create_delivery_challan');
            } else if ($igst_check_customer) {
                redirect('DeliveryChallanController/create_central_gst_invoice');
            } else {
                redirect('DeliveryChallanController/create_non_gst_invoice');
            }
        }
    }

    public function edit_customer() {
        $customer_id = $this->input->post('customer_id');

        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = trim((string) $this->input->post('email'));
        $mobile = trim((string) $this->input->post('mobile'));
        $email = ($email === '') ? null : $email;
        $mobile = ($mobile === '') ? null : $mobile;
        $address = $this->input->post('address');

        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'pancard' => $pancard, 'gst' => $gst, 'email' => $email, 'mobile' => $mobile, 'address' => $address);
        $result = $this->customer->edit_customer($data_customer, $customer_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Company updated successfully!!");
            redirect('CustomerController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Company not updated successfully!!");
            redirect('CustomerController/index');
        }
    }

    public function get_customer_by_id() {
        $id = $this->uri->segment(3);
        $data['customer'] = $this->customer->get_customer_by_id($id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('customer/edit_customer', $data);
    }

    public function delete_customer_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->customer->delete_customer_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Company deleted successfully!!");
            redirect('CustomerController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Company not deleted successfully!!");
            redirect('CustomerController/index');
        }
    }

    public function my_profile() {
        $session_data_head = $this->session->userdata('session_data_head');

        $mobile = $session_data_head['result']['user_id'];
        $data['result'] = $this->customers->get_customer_by_mobile($mobile);

        $this->load->view('header_side_bar', $session_data_head);
        $this->load->view('my_profile', $data);
    }

    public function get_customer() {
        $mobile = $this->input->post('customer_mobile');
        $data['result'] = $this->customers->get_customer_by_mobile($mobile);
        $this->load->view('view_booked_services', $data);
    }

    public function get_product_name() {
        $keyword = $this->input->get('term', TRUE);
        $product_name = $this->inventory->get_product_name($keyword);
        $dname_list1 = array();
        if (count($product_name) > 0) {
            foreach ($product_name as $value) {
                $dname_list1[] = $value->item_name;
            }
            echo json_encode($dname_list1);
        }
    }

    public function get_estimate() {
        $product_name = $this->input->post('item_name');

        $result = $this->estimate->get_estimate($product_name, $this->user_id);
        echo json_encode($result);
    }

    public function add_new_estimate_customer() {
        $company_name = $this->input->post('company_name');
        $customer_firstname = $this->input->post('customer_firstname');
        $customer_lastname = $this->input->post('customer_lastname');
        $customer_pancard_no = $this->input->post('customer_pancard_no');
        $customer_gst_no = $this->input->post('customer_gst_no');
        $customer_email = $this->input->post('customer_email');
        $customer_mobile = $this->input->post('customer_mobile');
        $customer_address = $this->input->post('customer_address');

        $data_customer = array('company_name' => $company_name, 'customer_firstname' => $customer_firstname, 'customer_lastname' => $customer_lastname, 'customer_pancard_no' => $customer_pancard_no, 'customer_gst_no' => $customer_gst_no, 'customer_email' => $customer_email, 'customer_mobile' => $customer_mobile, 'customer_address' => $customer_address);

        $result = $this->estimate->customer_check($customer_mobile);

        if ($result == FALSE) {
            $this->estimate->add_customer($data_customer);
            echo json_encode($result);
        }
    }

    public function get_customer_name_to_append_dropdown() {
        $query = $this->db->query("select customer_firstname from customer");
        $row_vendor_name = $query->row_array();
        $data = array("customer_firstname" => $row_vendor_name['customer_firstname']);
        echo json_encode($data);
    }

    public function add_delivery_challan() {
        $customer_id = $this->input->post('customer_id');
        $invoice_number = $this->input->post('invoice_number');
        $invoice_number_id = $this->input->post('invoice_number_id');
        $invoice_date = $this->input->post('invoice_date');
        $invoice_customer_po = $this->input->post('invoice_customer_po');
        $invoice_po_date = $this->input->post('invoice_po_date');
        $amount_due = $this->input->post('amount_due');
        $invoice_subheading = $this->input->post('dc_subheading')?? '';
        $invoice_footer = $this->input->post('dc_footer');
        $invoice_memo = $this->input->post('dc_memo');
        $dc_terms_and_conditions = $this->input->post('dc_terms_and_conditions');
        $dc_payment_terms = $this->input->post('dc_payment_terms');
        $dc_process_schedule = $this->input->post('dc_process_schedule');
        $dc_taxes = $this->input->post('dc_taxes');
        $dc_exclusions = $this->input->post('dc_exclusions');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $supplier_code = $this->input->post('supplier_code') ?? '';
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        $igst_check = $this->input->post('igst_check');
        $sgst = '0';
        $igst = '0';

        if ($igst_check) {
            $igst = $this->input->post('igst');
            $sgst = '1';
            $gst_type = "I";
        } else {
            $igst = '1';
            $sgst = $this->input->post('sgst');
            $cgst = $this->input->post('cgst');
            $gst_type = 'S';
        }

        $price = $this->input->post('price');
        $due_date = $this->input->post('due_date');
        $pay_amount = $this->input->post('pay_amount');
        $discount = $this->input->post('discount');
        $balance = $this->input->post('balance');
        $amount_quantity = $this->input->post('amount');
        $amount = $this->input->post('total_quotation_amount');


        //round of
        $amount = round($amount);

        $status = $this->input->post('status');
        $payment_method = $this->input->post('payment_method');
        $note = $this->input->post('note');
        $description = $this->input->post('description');

        /* Changes made for proforma invoice */

         $despatch_through = $this->input->post('despatch_through');
         $vehicle_no = $this->input->post('vehicle_no');
         $delivery_date = $this->input->post('delivery_date');
          $delivery_note_no = $this->input->post('delivery_note_no');
          $sales_person = $this->input->post('sales_person');
          $shipping_address = $this->input->post('shipping_address');
//        $data_invoice_total = array('number_fk' => $invoice_number, 'date' => $invoice_date, 'total' => $amount,
//            'balance' => $amount, 'customer_id_fk' => $customer_id, 'status' => $status,
//            'payment_method' => $payment_method, 'note' => $note,
//            'despatch_through' => $despatch_through, 'vehicle_no' => $vehicle_no, 'delivery_date' => $delivery_date,
//            'delivery_note_no' => $delivery_note_no,
//            'sales_person' => $sales_person,
//            'payment_due_date' => $due_date,
//            'invoice_subheading' => $invoice_subheading,
//            'invoice_footer' => $invoice_footer,
//            'invoice_memo' => $invoice_memo,
//            'customer_po' => $invoice_customer_po,
//            'po_date' => $invoice_po_date,
//            'shipping_address' => $shipping_address,
//            'uid' => $this->user_id);



        $data_invoice_total = array('number_fk' => $invoice_number, 'date' => $invoice_date, 'total' => $amount,
            'balance' => $amount, 'customer_id_fk' => $customer_id, 'status' => $status,
            'payment_method' => $payment_method, 'note' => $note,
            'payment_due_date' => $due_date,
            'dc_subheading' => $invoice_subheading,
            'dc_footer' => $invoice_footer,
            'dc_memo' => $invoice_memo,
            'dc_terms_and_conditions' => $dc_terms_and_conditions,
            'dc_payment_terms' => $dc_payment_terms,
            'dc_process_schedule' => $dc_process_schedule,
            'dc_taxes' => $dc_taxes,
            'dc_exclusions' => $dc_exclusions,
            'po_date' => $invoice_po_date,
            'despatch_through' => $despatch_through, 'vehicle_no' => $vehicle_no,  'shipping_address' => $shipping_address,
             'delivery_date' => $delivery_date,
            'delivery_note_no' => $delivery_note_no,
             'customer_po' => $invoice_customer_po,
            'supplier_code' => $supplier_code,
            'uid' => $session_data_head['result']['user_id'] ?? $this->user_id);



        $item_count = count($item);

        for ($i = 0; $i < $item_count; $i++) {
            if ($item[$i] != '' && $quantity[$i] != '' && $hsn[$i] != '' && $price[$i] != '') {

                if ($sgst == '1') {
                    $igst1 = $igst[$i];
                    $sgst1 = '0';
                    $cgst1 = '0';
                }if ($igst == '1') {
                    $igst1 = '0';
                    $sgst1 = $sgst[$i];
                    $cgst1 = $cgst[$i];
                }

                $data[] = array(
                    'invoice_number' => $invoice_number,
                    'invoice_date' => date("Y-m-d", strtotime($this->input->post('invoice_date'))),
                    'customer_id' => $customer_id,
                    'product_name' => $item[$i],
                    'quantity' => $quantity[$i],
                    'unit' => $unit[$i],
                    'hsn_code' => $hsn[$i],
                    'gst' => $gst_per[$i],
                    'discount' => isset($discount[$i]) ? $discount[$i] : 0,
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $gst_type,
                    'price' => $price[$i],
                    'amount' => $amount_quantity[$i],
                    'description' => $description[$i],
                    'uid' => $this->user_id,
                );

                $flag = 0;
            } else {
                $flag = 1;
            }
        }


        if ($flag == 0) {
            //  var_dump($data);die();
            $this->db->insert_batch('delivery_challan', $data);
            $result = $this->deliverychallan->add_delivery_challan_total($data_invoice_total);



            //for update stock in inventory table when invoice created
//            for ($i = 0; $i < $item_count; $i++) {
//                if ($item[$i] != '') {
//
//                    $stock = $this->invoice->get_inventory_stock_count($item[$i], $this->user_id);
//                    $datas[] = array(
//                        'stock' => $stock['stock'] - $quantity[$i],
//                    );
//                    $this->db->where('code', $item[$i]);
//                    $this->db->update('inventory', $datas[$i]);
//                }
//            }
            if ($result == TRUE) {
                // $this->invoice->change_flag_barcode();

                $this->session->set_flashdata('SUCCESSMSG', "Delivery Challan submitted successfully!!");
                redirect('DeliveryChallanController/index');
            } else {
                $this->session->set_flashdata('INFOMSG', "Delivery Challan not submitted successfully!!");
                redirect('DeliveryChallanController/index');
            }
        }
        //redirect('InvoiceController/index/');
    }
    
    
    
    
 


    public function show_delivery_challan() {
      // echo "dddd11";die();
        $id = $this->uri->segment(3);

// echo $id;

// die();

        $invoice_number_id = $this->deliverychallan->get_invoice_number_from_invoice_total($id, $this->user_id);
        $invoice_number = $invoice_number_id['number_fk'];


//         echo $invoice_number;

// die();
       
        $data['show_invoice'] = $this->deliverychallan->get_delivery_challan_data($invoice_number, $this->user_id);
    // print_r($data['show_invoice']);die();
        $data['status_result'] = $this->deliverychallan->get_status_by_invoiceid($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->deliverychallan->get_delivery_challan_data_group_by($invoice_number, $this->user_id);
 //print_r($data['invoice_data_group']);die();
        $data['payment_history'] = $this->deliverychallan->get_delivery_challan_payment_history_data($invoice_number, $this->user_id);
        $data['invoice_id'] = $this->deliverychallan->get_last_delivery_challan_number($this->user_id);
        $data['inv_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['next_invoice_name'] = $this->invoice->get_next_invoice_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['total_id'] = $id;
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        
        
        $this->load->view('delivery_challan/show_delivery_challan', $data);
    }

    public function show_non_gst_invoice() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['show_ng_invoice'] = $this->invoice->get_ng_invoice_data($invoice_number, $this->user_id);
        $data['ng_invoice_data_group'] = $this->invoice->get_ng_invoice_data_group_by($invoice_number, $this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_non_gst_invoice_number($this->user_id);
        $data['status_result'] = $this->invoice->get_status_by_non_gst_invoice($invoice_number, $this->user_id);

        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/show_non_gst_invoice', $data);
    }

    public function print_non_gst_invoice() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['show_ng_invoice'] = $this->invoice->get_ng_invoice_data($invoice_number, $this->user_id);
        $data['ng_invoice_data_group'] = $this->invoice->get_ng_invoice_data_group_by($invoice_number, $this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_non_gst_invoice_number($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $this->session->userdata('session_data_head');
        $this->load->view('admin/non_gst_invoice_preview', $data);
    }

    public function print_delivery_challan() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        
        $data['show_invoice'] = $this->deliverychallan->get_delivery_challan_data($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->deliverychallan->get_delivery_challan_data_group_by($invoice_number, $this->user_id);
        $data['invoice_id'] = $this->deliverychallan->get_last_delivery_challan_number($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $this->session->userdata('session_data_head');
        $this->load->view('admin/delivery_challan_preview', $data);
    }

    public function get_proforma_payment_details() {
        $id = $this->input->post('id');
        $arr = $this->deliverychallan->get_proforma_payment_details($id);
        echo json_encode($arr);
    }

    public function get_non_gst_invoice_payment_details() {
        $id = $this->input->post('id');
        $arr = $this->invoice->get_non_gst_invoice_payment_details($id);
        echo json_encode($arr);
    }

    public function edit_proforma_payment() {
        
        $id = $this->input->post('id');
        $invoice_number = $this->input->post('invoice_number');
        $customer_id_fk = $this->input->post('customer_id_fk');
        $payment_type = $this->input->post('payment_type');
        $paid = $this->input->post('paid');
        $balance = $this->input->post('balance');
        $date = $this->input->post('date');
        $payment_method = $this->input->post('payment_method');
        $bank_name = $this->input->post('bank_name');
        
        $paid_amount = $this->deliverychallan->get_previous_balance_proforma($invoice_number, $this->user_id);
       
        $total_paid = $paid + $paid_amount['paid'];

        $note = $this->input->post('note');
        $balance1 = abs($balance - $total_paid);
//print_r($balance1);die();
        //pay amount of invoice
        $invoice_pay_amount = $this->deliverychallan->get_pay_gst_invoice_amount($invoice_number, $this->user_id);
        if (count($invoice_pay_amount) == 0) {
            $total_invoice_paid = $paid;
        } else {
            for ($i = 0; $i < count($invoice_pay_amount); $i++) {
                $paid_amounts[] = $invoice_pay_amount[$i]->invocie_pay_amount;
                $total_invoice_paid1 = array_sum($paid_amounts);
            }
            $total_invoice_paid = $total_invoice_paid1 + $paid;
        }

        $total_amount_invoice = $paid_amount['total'];
        $invoice_balance = $total_amount_invoice - $total_invoice_paid;

        //Invoice History Details
        $invoice_payment_gst = array('payment_type' => $payment_type, 'invocie_pay_amount' => $paid, 'invocie_pay_method' => $payment_method,
            'invoice_pay_date' => $date, 'invoice_pay_remark' => $note, 'invoice_number_fk' => $invoice_number, 'uid' => $this->user_id,'bank_name' => $bank_name, 'customer_id_fk' => $customer_id_fk);
        $this->deliverychallan->pay_gst_proforma_amount($invoice_payment_gst);
        

        $data_payment = array('paid' => $total_paid, 'balance' => $invoice_balance, 'payment_method' => $payment_method,
            'date' => $date, 'note' => $note, 'uid' => $this->user_id);
      //  print_r($data_payment);die();
        $result = $this->deliverychallan->edit_invoice_payment($data_payment, $id);
       // print_r($result);die();
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment updated successfully!!");
            redirect('DeliveryChallanController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not updated successfully!!");
            redirect('DeliveryChallanController/index');
        }
    }

    public function edit_non_gst_invoice_payment() {
        $id = $this->input->post('id');
        $invoice_number = $this->input->post('invoice_numbers');
        $customer_id_fk = $this->input->post('customer_id_fk');

        $paid = $this->input->post('paid');
        $balance = $this->input->post('balance');
        $date = $this->input->post('date');
        $payment_method = $this->input->post('payment_method');
        $paid_amount = $this->invoice->get_previous_balance_non_gst_invoice($invoice_number, $this->user_id);
        $total_paid = $paid + $paid_amount['paid'];
        $note = $this->input->post('note');
        $balance1 = abs($balance - $total_paid);

        //pay amount of non gst invoice
        $invoice_pay_amount = $this->invoice->get_pay_non_gst_invoice_amount($invoice_number, $this->user_id);
        //echo count($invoice_pay_amount); 
        if (count($invoice_pay_amount) == 0) {
            $total_invoice_paid = $paid;
        } else {
            for ($i = 0; $i < count($invoice_pay_amount); $i++) {
                $paid_amounts[] = $invoice_pay_amount[$i]->ng_invocie_pay_amount;
                $total_invoice_paid1 = array_sum($paid_amounts);
            }
            $total_invoice_paid = $total_invoice_paid1 + $paid;
        }
        $total_amount_invoice = round($paid_amount['total']);


        $invoice_balance = $total_amount_invoice - $total_invoice_paid;

        $invoice_payment_non_gst = array('ng_invocie_pay_amount' => $paid, 'ng_invocie_pay_method' => $payment_method,
            'ng_invoice_pay_date' => $date, 'ng_invoice_pay_remark' => $note, 'ng_invoice_number_fk' => $invoice_number, 'uid' => $this->user_id, 'customer_id_fk' => $customer_id_fk);
        $this->invoice->pay_non_gst_invoice_amount($invoice_payment_non_gst);

        $data_payment = array('paid' => $total_invoice_paid, 'balance' => $invoice_balance,
            'payment_method' => $payment_method, 'date' => $date, 'note' => $note, 'uid' => $this->user_id);

        $result = $this->invoice->edit_non_gst_invoice_payment($data_payment, $id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment updated successfully!!");
            redirect('InvoiceController/index_non_gst');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not updated successfully!!");
            redirect('InvoiceController/index_non_gst');
        }
    }

    public function edit_delivery_challan_details() {

        $id = $this->uri->segment(3);

        $invoice_number_id = $this->deliverychallan->get_invoice_number_from_invoice_total($id, $this->user_id);

        if (empty($invoice_number_id)) {
            $this->session->set_flashdata('ERRORMSG', 'Delivery challan not found.');
            redirect('DeliveryChallanController/index');
            return;
        }

        $invoice_number = $invoice_number_id['number_fk'];

        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['show_invoice'] = $this->deliverychallan->get_delivery_challan_data($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->deliverychallan->get_delivery_challan_data_group_by($invoice_number, $this->user_id);

        if (empty($data['invoice_data_group'])) {
            $this->session->set_flashdata('ERRORMSG', 'Delivery challan data not found.');
            redirect('DeliveryChallanController/index');
            return;
        }

        $data['customer_result'] = $this->deliverychallan->get_company_name($this->user_id);
        $data['status_result'] = $this->deliverychallan->get_status_by_invoiceid($invoice_number, $this->user_id);
        $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/edit_delivery_challan', $data);
    }

    public function edit_non_gst_invoice_details() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['show_ng_invoice'] = $this->invoice->get_ng_invoice_data($invoice_number, $this->user_id);
        $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);
        $data['ng_invoice_data_group'] = $this->invoice->get_ng_invoice_data_group_by($invoice_number, $this->user_id);
        $data['customer_result'] = $this->invoice->get_company_name($this->user_id);
        $data['status_result'] = $this->invoice->get_status_by_non_gst_invoice($invoice_number, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/edit_non_gst_invoice', $data);
    }

    public function edit_delivery_challan() {

        $customer_id = $this->input->post('customer_id');
        $invoice_id = $this->input->post('invoice_id');

//   echo $customer_id;
//         die();
       
        $invoice_number = $this->input->post('invoice_number');
        $date = $this->input->post('date');
        $invoice_customer_po = $this->input->post('invoice_customer_po');
         $invoice_po_date = $this->input->post('invoice_po_date');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        //Check Invoice gst type for edit invoice
        $gst_check = $this->input->post('gst');

        $sgst = '0';
        $igst = '0';
        if ($gst_check) {
            $igst = '1';
            $sgst = $this->input->post('sgst');
            $cgst = $this->input->post('cgst');
            $gst_type = "S";
        } else {
            $sgst = '1';
            $cgst = '1';
            $igst = $this->input->post('igst');
            $gst_type = "I";
        }

        $price = $this->input->post('price');
        $amount = $this->input->post('total_quotation_amount');
        $amount_quantity = $this->input->post('amount');
        $discount = $this->input->post('discount');
        $payment_due_date = $this->input->post('payment_due_date');
        $invoice_subheading = $this->input->post('dc_subheading')?? '';
        $invoice_footer = $this->input->post('dc_footer');
        $invoice_memo = $this->input->post('dc_memo');
        $dc_terms_and_conditions = $this->input->post('dc_terms_and_conditions');
        $dc_payment_terms = $this->input->post('dc_payment_terms');
        $dc_process_schedule = $this->input->post('dc_process_schedule');
        $dc_taxes = $this->input->post('dc_taxes');
        $dc_exclusions = $this->input->post('dc_exclusions');
        $note = $this->input->post('note');
        $description = $this->input->post('description');
        $payment_method = $this->input->post('payment_method');
        $status = $this->input->post('status');
        $total_invoice_amount = $this->input->post('total_quotation_amount');
         $despatch_through = $this->input->post('despatch_through');
         $vehicle_no = $this->input->post('vehicle_no');
         $delivery_date = $this->input->post('delivery_date');
          $delivery_note_no = $this->input->post('delivery_note_no');
           $shipping_address = $this->input->post('shipping_address');
              $supplier_code = $this->input->post('supplier_code') ?? '';
        $item_count = count($item);
        $data = array();

        //round of
        $total_invoice_amount = round($total_invoice_amount);

        // Delete rows that were removed in the UI (their invoice_id was removed from the form)
        // Must run BEFORE the insert loop so newly inserted rows are not accidentally deleted
        $submitted_ids = array_filter(is_array($invoice_id) ? $invoice_id : array());
        if (!empty($submitted_ids)) {
            $this->db->where('invoice_number', $invoice_number);
            $this->db->where('uid', $this->user_id);
            $this->db->where_not_in('invoice_id', $submitted_ids);
            $this->db->delete('delivery_challan');
        }

        for ($i = 0; $i < $item_count; $i++) {
            if ($item[$i] != '' && $quantity[$i] != '' && $hsn[$i] != '' && $price[$i] != '') {
                if ($invoice_id[$i]) {


                    $stock = $this->deliverychallan->get_inventory_stock_count($item[$i], $this->user_id);
                    $edit_quantity = $this->deliverychallan->get_invoice_quantity_count($item[$i], $invoice_number, $this->user_id);
                    $datas1[] = array(
                        'stock' => (abs($edit_quantity['quantity'] + $stock['stock']) - ($quantity[$i])),
                    );
                    $this->db->where('code', $item[$i]);
                    $this->db->update('inventory', $datas1[$i]);


                    if ($sgst == '1') {
                        $igst1 = $igst[$i];
                        $sgst1 = '0';
                        $cgst1 = '0';
                    }if ($igst == '1') {
                        $igst1 = '0';
                        $sgst1 = $sgst[$i];
                        $cgst1 = $cgst[$i];
                    }

                    $data = array(
                        'invoice_number' => $invoice_number,
                        'invoice_date' => date("Y-m-d", strtotime($this->input->post('date'))),
                        'customer_id' => $customer_id,
                        'product_name' => $item[$i],
                        'quantity' => $quantity[$i],
                        'unit' => $unit[$i],
                        'hsn_code' => $hsn[$i],
                        'gst' => $gst_per[$i],
                        'discount' => $discount[$i],
                        'sgst' => $sgst1,
                        'cgst' => $cgst1,
                        'igst' => $igst1,
                        'gst_type' => $gst_type,
                        'price' => $price[$i],
                        'amount' => $amount_quantity[$i],
                        'description' => $description[$i],
                    );

                    $this->db->where('invoice_number', $invoice_number);
                    $this->db->where('invoice_id', $invoice_id[$i]);
                    $this->db->update('delivery_challan', $data);
                } else {

                    if ($sgst == '1') {
                        $igst1 = $igst[$i];
                        $sgst1 = '0';
                        $cgst1 = '0';
                    }if ($igst == '1') {
                        $igst1 = '0';
                        $sgst1 = $sgst[$i];
                        $cgst1 = $cgst[$i];
                    }

                    $data_insert = array(
                        'invoice_number' => $invoice_number,
                        'invoice_date' => date("Y-m-d", strtotime($this->input->post('date'))),
                        'customer_id' => $customer_id,
                        'product_name' => $item[$i],
                        'quantity' => $quantity[$i],
                        'unit' => $unit[$i],
                        'hsn_code' => $hsn[$i],
                        'gst' => $gst_per[$i],
                        'discount' => $discount[$i],
                        'sgst' => $sgst1,
                        'cgst' => $cgst1,
                        'igst' => $igst1,
                        'gst_type' => $gst_type,
                        'price' => $price[$i],
                        'amount' => $amount_quantity[$i],
                        'description' => $description[$i],
                        'uid' => $this->user_id,
                    );
                    $this->db->insert('delivery_challan', $data_insert);
                }
            }
        }

        for ($i = 0; $i < $item_count; $i++) {
            if ($item[$i] != '') {

                $stock = $this->invoice->get_inventory_stock_count($item[$i], $this->user_id);
                $datas[] = array(
                    'stock' => $stock['stock'] - $quantity[$i],
                );
                $this->db->where('code', $item[$i]);
                $this->db->update('inventory', $datas[$i]);
            }
        }


        $previous_balance = $this->invoice->get_previous_balance_invoice($invoice_number, $this->user_id);
        $current_balance = floatval($total_invoice_amount - $previous_balance['total_balance_amount']);


        $data_toatl_amount = array('number_fk' => $invoice_number, 'date' => $date, 'total' => $amount,
            'balance' => $amount, 'customer_id_fk' => $customer_id, 'status' => $status,
            'payment_method' => $payment_method, 'note' => $note,
            'payment_due_date' => $payment_due_date,
            'dc_subheading' => $invoice_subheading,
            'dc_footer' => $invoice_footer,
            'dc_memo' => $invoice_memo,
            'dc_terms_and_conditions' => $dc_terms_and_conditions,
            'dc_payment_terms' => $dc_payment_terms,
            'dc_process_schedule' => $dc_process_schedule,
            'dc_taxes' => $dc_taxes,
            'dc_exclusions' => $dc_exclusions,
            'po_date' => $invoice_po_date,
            'despatch_through' => $despatch_through, 'vehicle_no' => $vehicle_no,  'shipping_address' => $shipping_address,
             'delivery_date' => $delivery_date,
            'delivery_note_no' => $delivery_note_no,
             'customer_po' => $invoice_customer_po,
            'supplier_code' => $supplier_code,
            'uid' => $this->user_id);


           

        $result = $this->deliverychallan->update_delivery_challan_total($data_toatl_amount, $invoice_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Delivery challan updated successfully!!");
            redirect('DeliveryChallanController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Delivery challan not updated successfully!!");
            redirect('DeliveryChallanController/index');
        }
    }

    public function get_delivery_challan_data_by_status() {

        $status = $this->uri->segment(3);
        $data['invoices'] = $this->deliverychallan->get_delivery_challan_data_by_status($status, $this->user_id);
        

        $data['invoice_count'] = $this->deliverychallan->get_delivery_challan_count($this->user_id);


        $draft_status = 1;
        $data['invoice_draft_count'] = $this->deliverychallan->get_delivery_challan_draft_count($draft_status, $this->user_id);
       
        $sent_status = 2;
        
        $data['invoice_sent_count'] = $this->deliverychallan->get_delivery_challan_send_count($sent_status, $this->user_id);
       


        $session_data_head = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/view_delivery_challan', $data);
    }

    public function get_non_gst_invoice_status_count() {
        $status = $this->uri->segment(3);

        $data['non_gst_invoices'] = $this->invoice->get_ng_invoice_data_by_status($status, $this->user_id);
        $data['invoice_count'] = $this->invoice->get_non_gst_invoice_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_non_gst_invoice_status($draft_status, $this->user_id);
        $sent_status = 2;
        $data['invoice_sent_count'] = $this->invoice->get_non_gst_invoice_status($sent_status, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/view_non_gst_invoice', $data);
    }

    public function send_delivery_challan_email() {
        //get data using session to set mail properties
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'];
        $set_company_logo = base_url() . '/' . $session_data_head2['company_logo'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];
        //enddata using session to set mail properties

        $invoice_number = $this->input->post('invoice_number');
        $invoice_data_group = $this->deliverychallan->get_delivery_challan_data_group_by($invoice_number, $this->user_id);

        $customer_name = $invoice_data_group['fullname'];
        $issue_date = !empty($invoice_data_group['invoice_date']) ? date('d-m-Y', strtotime($invoice_data_group['invoice_date'])) : '';
        $expires_date = !empty($invoice_data_group['payment_due_date']) ? date('d-m-Y', strtotime($invoice_data_group['payment_due_date'])) : '';
        $grand_total = $invoice_data_group['total'];

        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');


       $data['settings'] = $this->login->get_settings($this->user_id);

// echo $invoice_number;
//        die();

        $user_id_send = $this->user_id;
        $url = base_url() . 'Pdf/download_delivery_challan?invoice_number=' . $invoice_number;
        // Email sending

        $this->load->library('email');
        $this->email->set_mailtype("html");
        if (strpos(base_url(), 'localhost') !== false) {

    // Local server
    $this->email->from($set_from_email, $set_company_name);

} else {

    // Live server
    $this->email->from("noreply@uwsenvirotech.com", $set_company_name);

}
        $this->email->to($to_email);
        $this->email->subject($subject);
        if ($copy_email) {
            //echo $copy_email;
            $this->email->cc($set_cc_email);
        }
        $htmlContent11 = '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Delivery Challan</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <head>
        <title>Welcome to ' . $data['settings']['company_name'] . '</title>
        <style> 

            @media (min-width: 1281px) {

             .boxs{
             padding:2% 10% 2% 10%; 
             margin:0% 20% 0% 20%;
             }
            }

            /* 
              ##Device = Laptops, Desktops
              ##Screen = B/w 1025px to 1280px
            */

            @media (min-width: 1025px) and (max-width: 1280px) {

              .boxs{
             padding:2% 10% 2% 10%; 
             margin:0% 10% 0% 10%;
             }
            }

            /* 
              ##Device = Tablets, Ipads (portrait)
              ##Screen = B/w 768px to 1024px
            */

            @media (min-width: 768px) and (max-width: 1024px) {

             .boxs{
             padding:2% 10% 2% 10%; 
             margin:0% 10% 0% 10%;
             }
            }

            /* 
              ##Device = Tablets, Ipads (landscape)
              ##Screen = B/w 768px to 1024px
            */

            @media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {

             .boxs{
             padding:2% 10% 2% 10%; 
            margin:0% 20% 0% 20%;
             }

            }

            /* 
              ##Device = Low Resolution Tablets, Mobiles (Landscape)
              ##Screen = B/w 481px to 767px
            */

            @media (min-width: 481px) and (max-width: 767px) {

             .boxs{
             padding:2% 10% 2% 10%; 
             margin:0% 0% 0% 0%;
              text-align: center;
             }

            }

            @media (min-width: 320px) and (max-width: 480px) {

            .boxs{
             padding:2% 10% 2% 10%; 
             margin:0% 0% 0% 0%;
              text-align: center;
             }

            }
        .shadows1{    
                padding:2% 4% 2% 4%;
                border-radius: 2px;
                line-height: 2;
               text-align: center;
                 border: 1px solid grey;
              -webkit-box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.58);
              -moz-box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.58);
              box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.58);
                 background: #fff;
}
</style>
    </head>
    <body style=" background: #f8f8f8;">
      <div style=" padding:2% 10% 2% 10%; margin:0% 20% 0% 20%;">
       <div class="shadows1">  
           <img alt="' . $data['settings']['company_name'] . '" src="' . $data['settings']['company_logo'] . '" width="30%" style="font-size:16px;color:#b8b9c1;font-weight:normal;">
       
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;line-height:32px"><center>Delivery Challan</center></span><br>
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;line-height:32px">' . $invoice_number . '</span><br>
                    
                <span style="text-decoration:none;color:#a0a0a5;">for <b>' . $customer_name . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">from <b>' . $data['settings']['company_name'] . '</b></span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;">Please check our delivery challan. </span>
       <hr>

       <span style="text-decoration:none;color:#2f2f36;"><b>Message :</b>' . $message . '</span>
       <hr>

       <span style="text-decoration:none;color:#2f2f36;font-size:18px">Grand Total : <b>' . $grand_total . ' INR</b></span>
       <hr>
            <a href="' . $url . '" style="background-color:#00929f;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px" target="_blank">
            Download in browser</a><br>
            
            <span style="text-decoration:none;color:#2f2f36;"> Payment due date : <b>' . $expires_date . '</b></span>
            <hr>
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If this delivery challan was sent in error, please contact" <a href="mailto:' . $data['settings']['company_email'] . '" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">' . $data['settings']['company_email'] . '</a></span>
         </div>
          <center><span style="text-decoration:none;color:#2f2f36; ">Powered by 
      <img alt="' . $data['settings']['company_name'] . '" src="http://xformtechnologies.com/wp-content/uploads/2017/05/logo.png" width="8%" height="8%" style="margin-top:3%;">
      ' . $data['settings']['company_name'] . '</span></center>
     </div>
     
    </body>
    </html>';

        // Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Xform <contact@xform.in>' . "\r\n";

        // Generate PDF for attachment
        $pdf_filename = 'DeliveryChallan_' . str_replace('/', '_', $invoice_number) . '_' . time() . '.pdf';
        $pdf_filepath = FCPATH . 'uploads/' . $pdf_filename;
        
        try {
            // Get delivery challan data for PDF generation
            $show_invoice = $this->deliverychallan->get_delivery_challan_data($invoice_number, $this->user_id);
            
            // Create PDF using mPDF
            $mpdf = new \Mpdf\Mpdf();
            $invoice_data = array(
                'show_invoice' => $show_invoice,
                'invoice_data_group' => $invoice_data_group,
                'invoice_id' => $this->deliverychallan->get_last_delivery_challan_number($this->user_id),
                'settings' => $data['settings'],
                'stamp' => 'no',
                'qty' => ''
            );
            
            $html = $this->load->view('admin/delivery_challan_print', $invoice_data, true);
            
            $invoice_header_date = 'N/A';
            if (!empty($invoice_data_group['invoice_date'])) {
                $invoice_header_timestamp = strtotime($invoice_data_group['invoice_date']);
                if ($invoice_header_timestamp !== false) {
                    $invoice_header_date = date('d-M-Y', $invoice_header_timestamp);
                }
            }
            
            $mpdf->SetHTMLHeader('<div>' . $invoice_header_date . " - " . $invoice_number . '</div>');
            $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right;">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
            $mpdf->SetWatermarkText($data['settings']['company_name']);
            $mpdf->showWatermarkText = true;
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->WriteHTML($html);
            $mpdf->Output($pdf_filepath, 'F'); // Save to file
            
        } catch (Exception $e) {
            log_message('error', 'PDF generation failed for delivery challan email: ' . $e->getMessage());
            // Continue anyway and send email with link only
            $pdf_filepath = false;
        }

        // Attach PDF if generated successfully
        if ($pdf_filepath && file_exists($pdf_filepath)) {
            $this->email->attach($pdf_filepath);
        }

        $this->email->message($htmlContent11);

        if ($this->email->send()) {

            // Clean up temporary PDF file after successful sending
            if ($pdf_filepath && file_exists($pdf_filepath)) {
                unlink($pdf_filepath);
            }

            //change invoice status
            $status = 2;
            $data_customer = array('status' => $status);
            $this->invoice->edit_invoice_status($data_customer, $invoice_number, $this->user_id);

            $this->session->set_flashdata('SUCCESSMSG', "Email Sent Successfully with PDF!!");
            redirect('DeliveryChallanController/index');
        } else {
            // Clean up temporary PDF file if sending failed
            if ($pdf_filepath && file_exists($pdf_filepath)) {
                unlink($pdf_filepath);
            }
            
            $this->session->set_flashdata('INFOMSG', "Email not Sent Successfully!!");
            redirect('DeliveryChallanController/index');
        }
    }

    public function get_customer_email() {
        $invoice_number = $this->input->post('invoice_number');
        $result = $this->deliverychallan->get_customer_email($invoice_number, $this->user_id);
        echo json_encode($result);
    }

    public function edit_ng_invoice() {
        $customer_id = $this->input->post('customer_id');
        $invoice_id = $this->input->post('invoice_id');

        $invoice_number = $this->input->post('invoice_number');
        $date = $this->input->post('date');

        $invoice_customer_po = $this->input->post('invoice_customer_po');
        $invoice_po_date = $this->input->post('invoice_po_date');

        $item = $this->input->post('product_name');
         $invoice_subheading = $this->input->post('invoice_subheading');
        $quantity = $this->input->post('quantity');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');

        $price = $this->input->post('price');
        $amount = $this->input->post('total_quotation_amount');
        $amount_quantity = $this->input->post('amount');
        $payment_due_date = $this->input->post('payment_due_date');
        $invoice_subheading = $this->input->post('invoice_subheading');
        $invoice_footer = $this->input->post('invoice_footer');
        $invoice_memo = $this->input->post('invoice_memo');
        $note = $this->input->post('note');
        $description = $this->input->post('description');

        $payment_method = $this->input->post('payment_method');
        $status = $this->input->post('status');
        $total_invoice_amount = $this->input->post('total_quotation_amount');
        $shipping_address = $this->input->post('shipping_address');

        $item_count = count($item);

        //ROund of 
        $total_invoice_amount = round($total_invoice_amount);

        $data = array();

        for ($i = 0; $i < $item_count; $i++) {
            if ($item[$i] != '' && $quantity[$i] != '' && $hsn[$i] != '' && $price[$i] != '') {

                if ($invoice_id[$i]) {

                    //for update stock in inventory table when invoice created
                    $stock = $this->invoice->get_inventory_stock_count($item[$i], $this->user_id);
                    $edit_quantity = $this->invoice->get_non_gst_invoice_quantity_count($item[$i], $invoice_number, $this->user_id);
                    $datas1[] = array(
                        'stock' => (abs($edit_quantity['quantity'] + $stock['stock']) - ($quantity[$i])),
                    );
                    $this->db->where('code', $item[$i]);
                    $this->db->where('uid', $this->user_id);
                    $this->db->update('inventory', $datas1[$i]);
                    //for end update stock in inventory table when invoice created


                    $data = array(
                        'invoice_number' => $invoice_number,
                        'customer_id' => $customer_id,
                        'invoice_date' => date("Y-m-d", strtotime($this->input->post('date'))),
                        'product_name' => $item[$i],
                        'quantity' => $quantity[$i],
                        'hsn_code' => $hsn[$i],
                        'gst' => $gst_per[$i],
                        'price' => $price[$i],
                        'amount' => $amount_quantity[$i],
                        'payment_due_date' => $payment_due_date,
                        'invoice_subheading' => $invoice_subheading,
                        'invoice_footer' => $invoice_footer,
                        'invoice_memo' => $invoice_memo,
                        'description' => $description[$i],
                    );
                    $this->db->where('uid', $this->user_id);
                    $this->db->where('invoice_number', $invoice_number);
                    $this->db->where('invoice_id', $invoice_id[$i]);
                    $this->db->update('non_gst_invoice', $data);
                } else {

                    $data_insert = array(
                        'invoice_number' => $invoice_number,
                        'customer_id' => $customer_id,
                        'invoice_date' => date("Y-m-d", strtotime($this->input->post('date'))),
                        'product_name' => $item[$i],
                        'quantity' => $quantity[$i],
                        'hsn_code' => $hsn[$i],
                        'gst' => $gst_per[$i],
                        'price' => $price[$i],
                        'amount' => $amount_quantity[$i],
                        'payment_due_date' => $payment_due_date,
                        'invoice_subheading' => $invoice_subheading,
                        'invoice_footer' => $invoice_footer,
                        'invoice_memo' => $invoice_memo,
                        'description' => $description[$i],
                        'uid' => $this->user_id,
                    );

                    $this->db->insert('non_gst_invoice', $data_insert);
                }
            }
        }

        //for update stock in inventory table when invoice created
        for ($i = 0; $i < $item_count; $i++) {
            if ($item[$i] != '') {

                $stock = $this->invoice->get_inventory_stock_count($item[$i], $this->user_id);
                $datas[] = array(
                    'stock' => $stock['stock'] - $quantity[$i],
                );
                $this->db->where('uid', $this->user_id);
                $this->db->where('code', $item[$i]);
                $this->db->update('inventory', $datas[$i]);
            }
        }
        //for end update stock in inventory table when invoice created

        $data_toatl_amount = array('date' => $date, 'total' => $total_invoice_amount, 'balance' => $total_invoice_amount,
            'customer_id_fk' => $customer_id, 'payment_method' => $payment_method, 'status' => $status, 'note' => $note,
            'uid' => $this->user_id, 'shipping_address' => $shipping_address);
        $result = $this->invoice->update_non_gst_invoice_total($data_toatl_amount, $invoice_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Invoice updated successfully!!");
            redirect('InvoiceController/index_non_gst');
        } else {
            $this->session->set_flashdata('INFOMSG', "Invoice not updated successfully!!");
            redirect('InvoiceController/index_non_gst');
        }
    }

    public function delete_non_gst_invoice_by_invoice_number() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;


        $result = $this->invoice->delete_non_gst_invoice_by_invoice_number($invoice_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Invoice deleted successfully!!");
            redirect('InvoiceController/index_non_gst');
        } else {
            $this->session->set_flashdata('INFOMSG', "Invoice not deleted successfully!!");
            redirect('InvoiceController/index_non_gst');
        }
    }

    public function send_non_gst_invoice_email() {

        //get data using session to set mail properties
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'];
        $set_company_logo = base_url() . '/' . $session_data_head2['company_logo'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];
        //enddata using session to set mail properties

        $invoice_number = $this->input->post('invoice_number');

        $invoice_data_group = $this->invoice->get_ng_invoice_data_group_by($invoice_number, $this->user_id);

        $customer_name = $invoice_data_group['fullname'];
        $issue_date = $invoice_data_group['invoice_date'];
        $expires_date = $invoice_data_group['payment_due_date'];
        $grand_total = $invoice_data_group['total'];

        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');

        $user_id_send = $this->user_id;
        $url = base_url() . 'Download/download_non_gst_invoice/' . $invoice_number . '/' . $user_id_send;
        // Email sending
        //$cc_email_constant = $this->config->item('cc_email');
        $this->load->library('email');
        $this->email->set_mailtype("html");
        if (strpos(base_url(), 'localhost') !== false) {

    // Local server
    $this->email->from($set_from_email, $set_company_name);

} else {

    // Live server
    $this->email->from("noreply@uwsenvirotech.com", $set_company_name);

}
        $this->email->to($to_email);

        $this->email->subject($subject);
        if ($copy_email) {
            $this->email->cc($set_cc_email);
        }

        $htmlContent11 = '
    <html>
    <head>
        <title>Welcome to ' . $set_company_name . '</title>
        <style> 
        .shadows1{    
                padding:2% 10% 2% 10%;
                border-radius: 2px;
                line-height: 2;
               text-align: center;
                 border: 1px solid grey;
              -webkit-box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.58);
              -moz-box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.58);
              box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.58);
                 background: #fff;
}
</style>
    </head>
    <body style=" background: #f8f8f8;">
      <div style=" padding:2% 10% 2% 10%; margin:0% 20% 0% 20%;">
       <div class="shadows1">  
            <center> <img alt="' . $set_company_name . '" src="' . $set_company_logo . '" width="30%"></center>
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;line-height:32px"><center>Invoice</center></span><br>
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;line-height:32px">' . $invoice_number . '</span><br>
                    
                <span style="text-decoration:none;color:#a0a0a5;">for <b>' . $customer_name . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">from <b>' . $set_company_name . '</b></span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;">Please check our invoice. </span>
       <hr>

       <span style="text-decoration:none;color:#2f2f36;"><b>Message :</b>' . $message . '</span>
       <hr>

       <span style="text-decoration:none;color:#2f2f36;font-size:18px">Grand Total : <b>' . $grand_total . ' INR</b></span>
       <hr>
            <a href="' . $url . '" style="background-color:#00929f;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px" target="_blank">
            Download in browser</a><br>
            
            <span style="text-decoration:none;color:#2f2f36;"> Payment due date : <b>' . $expires_date . '</b></span>
            <hr>
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If this invoice was sent in error, please contact" <a href="mailto:contact@xform.in" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">contact@xform.in</a></span>
         </div>
          <center><span style="text-decoration:none;color:#2f2f36; ">Powered by 
      <img alt="XForm Technologies" src="http://xformtechnologies.com/wp-content/uploads/2017/05/logo.png" width="8%" height="8%" style="margin-top:3%;">
       Xform Technologies </span></center>
     </div>
     
    </body>
    </html>';

        // Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Xform <contact@xform.in>' . "\r\n";

        $this->email->message($htmlContent11);

        if ($this->email->send()) {
            $status = 2;
            $data_customer = array('status' => $status);
            $this->invoice->edit_non_gst_invoice_status($data_customer, $invoice_number, $this->user_id);

            $this->session->set_flashdata('SUCCESSMSG', "Email Sent Successfully!!");
            redirect('InvoiceController/index_non_gst');
        } else {
            $this->session->set_flashdata('INFOMSG', "Email not Sent Successfully!!");
            redirect('InvoiceController/index_non_gst');
        }
    }

    public function get_customer_email_non_gst() {
        $invoice_number = $this->input->post('invoice_number');
        $result = $this->invoice->get_customer_email_non_gst($invoice_number, $this->user_id);
        echo json_encode($result);
    }

    public function add_non_gst_invoice() {
        $customer_id = $this->input->post('customer_id');
        $invoice_number = $this->input->post('invoice_number');
        $invoice_date = $this->input->post('invoice_date');

        $invoice_customer_po = $this->input->post('invoice_customer_po');
        $invoice_po_date = $this->input->post('invoice_po_date');

        $amount_due = $this->input->post('amount_due');
        $invoice_subheading = $this->input->post('invoice_subheading');
        $invoice_footer = $this->input->post('invoice_footer');
        $invoice_memo = $this->input->post('invoice_memo');

        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');

        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');

        $price = $this->input->post('price');
        $due_date = $this->input->post('due_date');

        $pay_amount = $this->input->post('pay_amount');

        $balance = $this->input->post('balance');
        $amount_quantity = $this->input->post('amount');
        $amount = $this->input->post('total_quotation_amount');
        $status = $this->input->post('status');
        $payment_method = $this->input->post('payment_method');
        $note = $this->input->post('note');
        $description = $this->input->post('description');
        $shipping_address = $this->input->post('shipping_address');

        //Round of
        $amount = round($amount);

        $data_invoice_total = array('number_fk' => $invoice_number, 'date' => $invoice_date,
            'total' => $amount, 'balance' => $amount, 'customer_id_fk' => $customer_id, 'status' => $status,
            'payment_method' => $payment_method, 'note' => $note, 'uid' => $this->user_id, 'shipping_address' => $shipping_address);
        $item_count = count($item);

        for ($i = 0; $i < $item_count; $i++) {
            if ($item[$i] != '' && $quantity[$i] != '' && $hsn[$i] != '' && $price[$i] != '') {
                $data[] = array(
                    'invoice_number' => $invoice_number,
                    'customer_id' => $customer_id,
                    'invoice_date' => date("Y-m-d", strtotime($this->input->post('invoice_date'))),
                    'product_name' => $item[$i],
                    'quantity' => $quantity[$i],
                    'hsn_code' => $hsn[$i],
                    'gst' => $gst_per[$i],
                    'price' => $price[$i],
                    'amount' => $amount_quantity[$i],
                    'payment_due_date' => $due_date,
                    'invoice_subheading' => $invoice_subheading,
                    'invoice_footer' => $invoice_footer,
                    'invoice_memo' => $invoice_memo,
                    'customer_po' => $invoice_customer_po,
                    'po_date' => $invoice_po_date,
                    'description' => $description[$i],
                    'uid' => $this->user_id,
                );

                $flag = 0;
            } else {
                $flag = 1;
            }
        }

        if ($flag == 0) {

            //for update stock in inventory table when invoice created
            for ($i = 0; $i < $item_count; $i++) {
                if ($item[$i] != '') {
                    $stock = $this->invoice->get_inventory_stock_count($item[$i], $this->user_id);
                    $datas[] = array(
                        'stock' => $stock['stock'] - $quantity[$i],
                    );
                    $this->db->where('uid', $this->user_id);
                    $this->db->where('code', $item[$i]);
                    $this->db->update('inventory', $datas[$i]);
                }
            }


            $this->db->insert_batch('non_gst_invoice', $data);
            $result = $this->invoice->add_non_gst_invoice_total($data_invoice_total);

            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Invoice submitted successfully!!");
                redirect('InvoiceController/index_non_gst');
            } else {
                $this->session->set_flashdata('INFOMSG', "Invoice not submitted successfully!!");
                redirect('InvoiceController/index_non_gst');
            }
        }
    }

    public function delete_item() {
        $invoice_id = $this->input->post('invoice_id');
        $result = $this->deliverychallan->delete_item($invoice_id);
        echo json_encode($result);
    }

    public function delete_non_gst_item() {
        $invoice_id = $this->input->post('invoice_id');
        $result = $this->invoice->delete_non_gst_item($invoice_id);
        echo json_encode($result);
    }

    public function update_stock_by_item_name() {
        $item_name = $this->input->post('item_name');
        $quantity = $this->input->post('quantity');
        $data = array(
            'stock' => $quantity
        );
        $this->db->where('code', $item_name);
        $this->db->update('inventory', $data);
        echo 'True';
    }

    public function approve_delivery_challan_status() {
        $invoice_no = $this->input->post('number_fk');
        $status = 4;
        $data = array(
            'status' => $status
        );
        $this->db->where('number_fk', $invoice_no);
        $this->db->update('delivery_challan_total', $data);
        echo 'True';
    }

    public function add_customer_ajax() {
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = trim((string) $this->input->post('email'));
        $mobile = trim((string) $this->input->post('mobile'));
        $email = ($email === '') ? null : $email;
        $mobile = ($mobile === '') ? null : $mobile;
        $state_code = $this->input->post('state_code');
        $address = $this->input->post('address');
        $gst_check_customer = $this->input->post('gst_check_customer');

        if($gst_check_customer == ''){
            $gst_check_customer = '';
        }

        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'pancard' => $pancard, 'gst' => $gst,
            'email' => $email, 'mobile' => $mobile, 'state_code' => $state_code, 'address' => $address, 'uid' => $this->user_id);

        $result1 = $this->deliverychallan->customer_check($company_name, $gst_check_customer);
        
        if ($result1 == false) {
            
            
            $result['save_customer'] = $this->deliverychallan->add_customer($data_customer, $gst_check_customer);
            $result['get_customer'] = $this->deliverychallan->get_customer($gst_check_customer);
            echo json_encode($result);
        } else {
            
            $result['get_customer'] = $this->deliverychallan->get_customer($gst_check_customer);
            echo json_encode($result);
        }
    }

    public function approve_invoice_status() {
        $invoice_no = $this->input->post('number_fk');
        $status = 4;
        $data = array(
            'status' => $status
        );
        $this->db->where('number_fk', $invoice_no);
        $this->db->update('delivery_challan_total', $data);
        echo 'True';
    }

    
    public function get_datewise_record(){
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['invoice_count'] = $this->deliverychallan->get_delivery_challan_count($this->user_id);
        $data['invoice_draft_count'] = $this->deliverychallan->get_delivery_challan_draft_count(1, $this->user_id);
        $data['invoice_sent_count'] = $this->deliverychallan->get_delivery_challan_draft_count(2, $this->user_id);
        $data['invoices'] = $this->deliverychallan->get_datewise_record($from_date, $to_date, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/view_delivery_challan', $data);
    }

    public function get_monthyearwise_record(){
        $month_year = $this->input->post('month_year');
        $data['invoice_count'] = $this->deliverychallan->get_delivery_challan_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->deliverychallan->get_delivery_challan_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['invoice_sent_count'] = $this->deliverychallan->get_delivery_challan_draft_count($sent_status, $this->user_id);
        $data['invoices'] = $this->deliverychallan->get_monthyearwise_record($month_year, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('delivery_challan/view_delivery_challan', $data);
    }


    public function approve_delivery_status() {
        $invoice_no = $this->input->post('number_fk');
       
        $status = 4;
        $data = array(
            'status' => $status
        );
        $this->db->where('number_fk', $invoice_no);
        $this->db->update('delivery_challan_total', $data);
        echo 'True';
    }
    

    public function edit_invoice_payment() {
        $id = $this->input->post('id');
        $invoice_number = $this->input->post('invoice_number');
        $customer_id_fk = $this->input->post('customer_id_fk');
        $payment_type = $this->input->post('payment_type');
        $paid = $this->input->post('paid');
        $balance = $this->input->post('balance');
        $date = $this->input->post('date');
        $payment_method = $this->input->post('payment_method');

        $paid_amount = $this->invoice->get_previous_balance_invoice($invoice_number, $this->user_id);

        $total_paid = $paid + $paid_amount['paid'];

        $note = $this->input->post('note');
        $balance1 = abs($balance - $total_paid);

        //pay amount of invoice
        $invoice_pay_amount = $this->invoice->get_pay_gst_invoice_amount($invoice_number, $this->user_id);
        if (count($invoice_pay_amount) == 0) {
            $total_invoice_paid = $paid;
        } else {
            for ($i = 0; $i < count($invoice_pay_amount); $i++) {
                $paid_amounts[] = $invoice_pay_amount[$i]->invocie_pay_amount;
                $total_invoice_paid1 = array_sum($paid_amounts);
            }
            $total_invoice_paid = $total_invoice_paid1 + $paid;
        }

        $total_amount_invoice = $paid_amount['total'];
        $invoice_balance = $total_amount_invoice - $total_invoice_paid;

        //Invoice History Details
        $invoice_payment_gst = array('payment_type' => $payment_type, 'invocie_pay_amount' => $paid, 'invocie_pay_method' => $payment_method,
            'invoice_pay_date' => $date, 'invoice_pay_remark' => $note, 'invoice_number_fk' => $invoice_number, 'uid' => $this->user_id, 
            'rem_balance' => $invoice_balance, 'customer_id_fk' => $customer_id_fk);
        $this->invoice->pay_gst_invoice_amount($invoice_payment_gst);

        $data_payment = array('paid' => $total_paid, 'balance' => $invoice_balance, 'payment_method' => $payment_method,
            'date' => $date, 'note' => $note, 'uid' => $this->user_id);

        $result = $this->invoice->edit_invoice_payment($data_payment, $id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment updated successfully!!");
            redirect('InvoiceController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not updated successfully!!");
            redirect('InvoiceController/index');
        }
    }

    
    
       public function get_delivery_challan_payment_details() {
        $id = $this->input->post('id');
        $arr = $this->deliverychallan->get_delivery_challan_payment_details($id);
        echo json_encode($arr);
    }
  

    public function delete_delivery_challan_by_invoice_number() {
       //echo "dhhhhhhh";die();
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 .  "/" . $id3;

        //print_r($invoice_number);die();

        $result = $this->deliverychallan->delete_delivery_challan_by_invoice_number($invoice_number, $this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Delivery Challan deleted successfully!!");
            redirect('DeliveryChallanController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Delivery Challan not deleted..!!");
            redirect('DeliveryChallanController/index');
        }
    }




    public function convert_to_invoice() {
        $id = $this->input->post('id');

   // print_r($id);die();
      
      $quote_number_id = $this->deliverychallan->get_dc_number_from_dc_total($id, $this->user_id);
    // print_r($quote_number_id);die();
        $number = $quote_number_id['number_fk'];
     //   print_r($number);die();
        $invoice_number = strtoupper(trim($this->input->post('invoice_number')));

        if ($invoice_number === '') {
            $this->session->set_flashdata('INFOMSG', "Invoice name is required!");
            redirect('DeliveryChallanController/show_delivery_challan/' . $id);
            return;
        }

        if ($this->invoice->invoice_name_exists($invoice_number, $this->user_id)) {
            redirect('DeliveryChallanController/show_delivery_challan/' . $id . '?duplicate_invoice=1');
            return;
        }
        
       $total = 0;
        $payment_method = 0;
        $status = 0;
        $po_date = '';
        $exp_date = 0;
        $subheading = '';
        $footer = '';
        $memo = '';
        $supplier_id = '';
        
        $data_purchase_bill = $this->deliverychallan->get_convert_dc_data($number, $this->user_id);
        
       // var_dump($data_purchase_bill) ;die();
                    
        foreach ($data_purchase_bill as $key) {
           //echo $key->date; die();
           //echo $key->purchase_date; die();
$data[] = array(
                    'customer_id' => $key->customer_id,
                    'invoice_number' => $invoice_number,
                    'invoice_date' => $key->invoice_date,
                  
                    'product_name' => $key->product_name,
                    'quantity' => $key->quantity,
                    'hsn_code' => $key->hsn_code,
                    'unit' => $key->unit,
                    'gst' => $key->gst,
                      'sgst' => $key->sgst,
                        'cgst' => $key->cgst,
                        'igst' => $key->igst,
                        'gst_type' => $key->gst_type,
                    'price' => $key->price,
                    'amount' => $key->amount,
                    'discount' => $key->discount,
                  //  'subheading' => $subheading,
                    //'reasons' => $reasons,
                  'description' =>  $key->description,
                  //  'po_pending_quantity' => 'Y',
                    'uid' => $this->user_id,
                );
         }
         foreach ($data_purchase_bill as $key) {
              
              $total = $key->total;
               $status = $key->status;
                $delivery_date = $key->delivery_date;
                $payment_due_date = isset($key->payment_due_date) ? $key->payment_due_date : $key->date;
                
                $customer_id = $key->customer_id;
                 $date = $key->date;
          }

        //   var_dump( $data);
        //   die();
            $this->db->trans_begin();

            try {
                $this->db->insert_batch('invoice', $data);
                
                $data_toatl_amount = array('total' => $total, 'number_fk' => $invoice_number, 'status' => $status, 'delivery_date' => $delivery_date, 'customer_id_fk' => $customer_id, 'date' => $date,
                    'payment_method' => $payment_method, 'paid' => 0, 'balance' => $total, 'payment_due_date' => $payment_due_date, 'uid' => $this->user_id);
                $result = $this->invoice->add_invoice_total($data_toatl_amount);

                if ($this->db->trans_status() === FALSE || $result != TRUE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('INFOMSG', "Delivery Challan not converted to Invoice successfully!!");
                    redirect('InvoiceController/index');
                    return;
                }

                $this->db->trans_commit();
                $this->session->set_flashdata('SUCCESSMSG', "Delivery Challan converted to Invoice successfully!!");
                redirect('InvoiceController/index');
                return;
            } catch (\Throwable $e) {
                $this->db->trans_rollback();

                if (stripos($e->getMessage(), 'Duplicate entry') !== false) {
                    redirect('DeliveryChallanController/show_delivery_challan/' . $id . '?duplicate_invoice=1');
                    return;
                }

                throw $e;
            }
        
    }



public function bulk_convert_delivery_challan_new()
{
    $dc_ids = $this->input->post('dc_ids');

    if (empty($dc_ids)) {
        $this->session->set_flashdata('INFOMSG', 'No delivery challans selected.');
        redirect('DeliveryChallanController/index?str=All');
        return;
    }

    // Auto-generate a single invoice number
    $invoice_number = $this->invoice->get_next_invoice_name($this->user_id);

    $all_items = [];
    $grand_total = 0;
    $customer_id = null;
    $invoice_date = null;
    $payment_due_date = null;
    $date = null;
    $status = null;

    // Start ONE transaction for the whole operation
    $this->db->trans_begin();

    try {
        foreach ($dc_ids as $dc_id) {
            // Get the delivery challan number (number_fk) from delivery_challan_total
            $dc_data = $this->deliverychallan->get_dc_number_from_dc_total($dc_id, $this->user_id);
            if (!$dc_data || empty($dc_data['number_fk'])) {
                throw new Exception("Invalid delivery challan ID: {$dc_id}");
            }

            $delivery_challan_number = $dc_data['number_fk'];

            // Fetch all line items of this delivery challan
            $items = $this->deliverychallan->get_convert_dc_data($delivery_challan_number, $this->user_id);
            if (empty($items)) {
                throw new Exception("No items found for DC: {$delivery_challan_number}");
            }

            // Sum the total of this DC (take total from first item)
            $dc_total = $items[0]->total;
            $grand_total += $dc_total;

            // Collect each item for the new invoice
            foreach ($items as $item) {
                $all_items[] = [
                    'customer_id'   => $item->customer_id,
                    'invoice_number'=> $invoice_number,
                    'invoice_date'  => $item->invoice_date,
                    'product_name'  => $item->product_name,
                    'quantity'      => $item->quantity,
                    'hsn_code'      => $item->hsn_code,
                    'unit'          => $item->unit,
                    'gst'           => $item->gst,
                    'sgst'          => $item->sgst,
                    'cgst'          => $item->cgst,
                    'igst'          => $item->igst,
                    'gst_type'      => $item->gst_type,
                    'price'         => $item->price,
                    'amount'        => $item->amount,
                    'discount'      => $item->discount,
                    'description'   => $item->description,
                    'uid'           => $this->user_id,
                ];

                // Capture common invoice header data from the first item of the first DC
                if ($customer_id === null) {
                    $customer_id      = $item->customer_id;
                    $invoice_date     = $item->invoice_date;
                    $status           = $item->status;
                    $payment_due_date = isset($item->payment_due_date) ? $item->payment_due_date : $item->date;
                    $date             = $item->date;
                }
            }
        }

        if (empty($all_items)) {
            throw new Exception('No items to convert.');
        }

        // Perform the batch insert (no second transaction start)
        if (!$this->db->insert_batch('invoice', $all_items)) {
            $error = $this->db->error();
            throw new Exception("Batch insert failed: " . print_r($error, true));
        }

        // Insert the total record into invoice_total
        $invoice_total_data = [
            'total'            => $grand_total,
            'number_fk'        => $invoice_number,
            'status'           => $status,
            'delivery_date'    => date('Y-m-d', strtotime($invoice_date)),
            'customer_id_fk'   => $customer_id,
            'date'             => $date,
            'payment_method'   => 0,
            'paid'             => 0,
            'balance'          => $grand_total,
            'payment_due_date' => $payment_due_date,
            'uid'              => $this->user_id,
        ];

        if (!$this->db->insert('invoice_total', $invoice_total_data)) {
            $error = $this->db->error();
            throw new Exception("Insert total failed: " . print_r($error, true));
        }

        // Commit the transaction
        $this->db->trans_commit();

        $this->session->set_flashdata('SUCCESSMSG', "All selected delivery challans converted to single Invoice {$invoice_number}.");
        redirect('InvoiceController/index');
    } catch (Exception $e) {




        echo "DC Conversion failed: " . $e->getMessage();
        die();
        $this->db->trans_rollback();
        $this->session->set_flashdata('selected_dc_ids', $dc_ids);
        $this->session->set_flashdata('INFOMSG', "Conversion failed: " . $e->getMessage());
        redirect('DeliveryChallanController/index?str=All');
    }
}

}

