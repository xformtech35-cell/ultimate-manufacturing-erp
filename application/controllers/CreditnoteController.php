<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CreditnoteController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('estimate', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('creditnote', '', TRUE);
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
         $str = $this->input->get('str');
         if($str=="All"){  
            $data['notes'] = $this->creditnote->get_credit_notes($this->user_id);
        }else{
            $month_year = date('M-Y');
            
            $data['notes'] = $this->creditnote->get_monthwise_credit_notes($month_year, $this->user_id);
        }

        // print_r($data['notes']);
        // die();


        // $data['status_result'] = $this->invoice->get_status($this->user_id);
        // $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        // $data['settings'] = $this->login->get_settings($this->user_id);
        // $data['credit_id'] = $this->creditnote->get_last_credit_id($this->user_id);
        // $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        // $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);
        
        
        
           $invoice_number = $this->session->userdata('s_invoice_number');
        //  print_r($invoice_number);die(); 
        //  $invoice_data_group = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);

        // $draft_status = 1;
        // $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);
        // $sent_status = 2;
        // $data['invoice_sent_count'] = $this->invoice->get_invoice_draft_count($sent_status, $this->user_id);
            
        $session_data_head = $this->session->userdata('session_data_head');
       // print_r($session_data_head);die();
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/view_credit_note', $data);
    }

    public function index_non_gst() {
        $data['non_gst_invoices'] = $this->invoice->get_non_gst_invoices($this->user_id);

        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_non_gst_invoice_number($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['invoice_count'] = $this->invoice->get_non_gst_invoice_count($this->user_id);



        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_non_gst_invoice_status($draft_status, $this->user_id);
        $sent_status = 2;
        $data['invoice_sent_count'] = $this->invoice->get_non_gst_invoice_status($sent_status, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/view_non_gst_invoice', $data);
    }

    public function create_note() {
      
        $data['result'] = $this->invoice->get_customer($this->user_id);
       
       
        $data['credit_id'] = $this->creditnote->get_last_credit_id($this->user_id);
       
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['barcode'] = $this->inventory->get_product_barcode();
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);
 
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/credit_note', $data);
    }

   

    
    public function add_customer() {
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $address = $this->input->post('address');

        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'pancard' => $pancard, 'gst' => $gst,
            'email' => $email, 'mobile' => $mobile, 'address' => $address, 'uid' => $this->user_id);

        $result = $this->invoice->customer_check($company_name);
        //non_gst_check_customer   igst_check_customer  gst_check_customer
        $non_gst_check_customer = $this->input->post('non_gst_check_customer');
        $igst_check_customer = $this->input->post('igst_check_customer');
        $gst_check_customer = $this->input->post('gst_check_customer');

        if ($result == FALSE) {
            $this->invoice->add_customer($data_customer);
            $this->session->set_flashdata('SUCCESSMSG', "Company added successfully!!");

            if ($gst_check_customer) {
                redirect('InvoiceController/create_invoice');
            } else if ($igst_check_customer) {
                redirect('InvoiceController/create_central_gst_invoice');
            } else {
                redirect('InvoiceController/create_non_gst_invoice');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Company already exist!!");
         
         
         
            if ($gst_check_customer) {
                redirect('InvoiceController/create_invoice');
            } else if ($igst_check_customer) {
                redirect('InvoiceController/create_central_gst_invoice');
            } else {
                redirect('InvoiceController/create_non_gst_invoice');
            }
        }
    }

    public function edit_customer() {
        $customer_id = $this->input->post('customer_id');

        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
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

    public function add_invoice() {

        $flag = 0;
        
        $data = array();
        
        $invoice_number = $this->input->post('invoice_number');
        $credit_date = date("Y-m-d", strtotime($this->input->post('credit_date')));
        $doc_date = date("Y-m-d", strtotime($this->input->post('doc_date')));
        // $doc_date = $this->input->post('doc_date');
        $company_name = $this->input->post('company_name');
        $doc_no = $this->input->post('doc_no');
        $acc_type = $this->input->post('acc_type');
        $type = $this->input->post('type');
        $credit_amt = $this->input->post('credit_amt');
        $debit_amt = $this->input->post('debit_amt');
        $credit_no = $this->input->post('credit_no');
        // $debit_amt = $this->input->post('debit_amt');





      
        
       
        
      //  print_r($data_invoice_total);die();

       
            if ($acc_type != '') {

                $data = array(
                    

                    'invoice_number' => $invoice_number,
                    'credit_date' => $credit_date,
                    'doc_date' => $doc_date,
                    'doc_no' => $doc_no,
                    'company_name' => $company_name,
                    'acc_type' => $acc_type,
                    'type' => $type,
                    'debit_amt' => $debit_amt,
                    'credit_amt' => $credit_amt,
                    'credit_no' => $credit_no,

                    
                 
                );
               
                $flag = 0;
               

            } else {
                $flag = 1;
            }
        


        if ($flag == 0) {
            
            // var_dump($data);
            
            // die();      


            $result = $this->creditnote->add_invoice($data);

// print_r($result);
// die();
              
           
            if ($result == TRUE) {

                $this->session->set_flashdata('SUCCESSMSG', "Credit Note submitted successfully!!");
                redirect('CreditnoteController/index');
            } else {
                $this->session->set_flashdata('INFOMSG', "Credit Note not submitted!!");
                redirect('CreditnoteController/index');
            }
        }
    }

    public function delete_invoice_by_invoice_number() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);    
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        //print_r($invoice_number);die();
        $result = $this->invoice->delete_invoice_by_invoice_number($invoice_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Invoice deleted successfully!!");
            redirect('InvoiceController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Invoice not deleted successfully!!");
            redirect('InvoiceController/index');
        }
    }

    public function show_invoice() {
        $id = $this->uri->segment(3);
     //   print_r($id);die();
        $invoice_number_id = $this->invoice->get_invoice_number_from_invoice_total($id, $this->user_id);
        $invoice_number = $invoice_number_id['number_fk'];

        $data['show_invoice'] = $this->invoice->get_invoice_data($invoice_number, $this->user_id);
       // print_r($data['show_invoice']);die();
        $data['status_result'] = $this->invoice->get_status_by_invoiceid($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);
        $data['payment_history'] = $this->invoice->get_invoice_payment_history_data($invoice_number, $this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
       // print_r($data);        die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/show_invoice', $data);
    }
    
    public function sgst_to_igst() {
      $invoice_number = $this->input->get('invoice_number');
      
        $this->invoice->update_invoice_data($invoice_number, $this->user_id);
        
        $this->index();
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
        $this->load->view('invoice/show_non_gst_invoice', $data);
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

    public function print_invoice() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['show_invoice'] = $this->invoice->get_invoice_data($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $this->session->userdata('session_data_head');
        
        $this->load->view('admin/invoice_preview', $data);
    }

    public function get_invoice_payment_details() {
        $id = $this->input->post('id');
        $arr = $this->invoice->get_invoice_payment_details($id);
        echo json_encode($arr);
    }
    
       public function get_proforma_invoice_payment_details() {
        $id = $this->input->post('id');
        $arr = $this->invoice->get_proforma_invoice_payment_details($id);
        echo json_encode($arr);
    }

    public function get_non_gst_invoice_payment_details() {
        $id = $this->input->post('id');
        $arr = $this->invoice->get_non_gst_invoice_payment_details($id);
        echo json_encode($arr);
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
        $bank_name = $this->input->post('bank_name');
        $note = $this->input->post('note');

        $pay_amt = $this->input->post('pay_amt');  // payment_in
        $payment_id = $this->input->post('pay_id');  // payment_in




        $pay_balance = $pay_amt - $paid;

        // pay_balance

        // pay_paid

        $data_payment_in = array("pay_balance"=> $pay_balance, "pay_paid"=> $paid, "status" => "used");

        

        if($payment_id != ""){
            $this->db->where('payment_id', $payment_id);
            $this->db->update('payment_in', $data_payment_in);
        }  
        



        if($payment_type == ""){
            $payment_type = " ";
        }

        if($payment_method == ""){
            $payment_method = " ";
        }

        if($note == ""){
            $note = "Payment In";
        }

        if($bank_name == ""){
            $bank_name = " ";
        }
        
     //  print_r($paid);die();
        $paid_amount = $this->invoice->get_previous_balance_invoice($invoice_number, $this->user_id);

        $total_paid = $paid + $paid_amount['paid'];

      
        $balance1 = abs($balance - $total_paid);

     
       // die();

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
            'rem_balance' => $invoice_balance,'bank_name' =>$bank_name , 'customer_id_fk' => $customer_id_fk);
     //   print_r($invoice_payment_gst);die();
        $this->invoice->pay_gst_invoice_amount($invoice_payment_gst);

        $data_payment = array('paid' => $total_paid, 'balance' => $invoice_balance, 'payment_method' => $payment_method,
             'note' => $note,'uid' => $this->user_id);
       // print_r($data_payment);die();




       
        $result = $this->invoice->edit_invoice_payment($data_payment, $id);
       // print_r($result);die();
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment updated successfully!!");
            redirect('InvoiceController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not updated successfully!!");
            redirect('InvoiceController/index');
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

    public function edit_note_details() {
        $invoice_number = $this->input->get('invoice_number');
        $data['show_invoice'] = $this->creditnote->get_note_data($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->creditnote->get_credit_note_data($invoice_number, $this->user_id);
// var_dump( $data['invoice_data_group']);
// die();
        $data['customer_result'] = $this->customer->get_customer($this->user_id);
        $data['status_result'] = $this->invoice->get_status_by_invoiceid($invoice_number, $this->user_id);
   //  $data['product_code_list'] = $this->inventory->get_product_part_name_edit($this->user_id);
    //    $data['item_name'] = $this->inventory->get_item_name($this->user_id);
    // print_r( $this->user_id); die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/edit_note', $data);
    }

    public function edit_non_gst_invoice_details() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['show_ng_invoice'] = $this->invoice->get_ng_invoice_data($invoice_number, $this->user_id);
        $data['product_code_list'] = $this->inventory->get_product_part_name_edit($this->user_id);
        $data['ng_invoice_data_group'] = $this->invoice->get_ng_invoice_data_group_by($invoice_number, $this->user_id);
        $data['customer_result'] = $this->customer->get_customer($this->user_id);
        $data['status_result'] = $this->invoice->get_status_by_non_gst_invoice($invoice_number, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/edit_non_gst_invoice', $data);
    }

    public function edit_invoice() {

        $flag = 0;
        
        $data = array();
        
        $invoice_number = $this->input->post('invoice_number');
        $credit_date = date("Y-m-d", strtotime($this->input->post('credit_date')));
        $doc_date = date("Y-m-d", strtotime($this->input->post('doc_date')));
        // $doc_date = $this->input->post('doc_date');
        $company_name = $this->input->post('company_name');
        $doc_no = $this->input->post('doc_no');
        $acc_type = $this->input->post('acc_type');
        $type = $this->input->post('type');
        $credit_amt = $this->input->post('credit_amt');
        $debit_amt = $this->input->post('debit_amt');
        $credit_no = $this->input->post('credit_no');
       
       
                     if ($acc_type != '') {

                        $data = array(
                            
        
                            'invoice_number' => $invoice_number,
                            'credit_date' => $credit_date,
                            'doc_date' => $doc_date,
                            'doc_no' => $doc_no,
                            'company_name' => $company_name,
                            'acc_type' => $acc_type,
                            'type' => $type,
                            'debit_amt' => $debit_amt,
                            'credit_amt' => $credit_amt,
                            'credit_no' => $credit_no,
        
                            
                         
                        );
                       
                        $flag = 0;
                       
        
                    } else {
                        $flag = 1;
                    }

                    if ($flag == 0) {
        
      
        $this->db->where('invoice_number', $invoice_number);
        $result =$this->db->update('credit_note', $data);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Invoice updated successfully!!");
            redirect('CreditnoteController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Invoice not updated successfully!!");
            redirect('CreditnoteController/index');
        }
    }
}

    public function get_invoice_data_by_status() {
        $status = $this->uri->segment(3);
        $data['invoices'] = $this->invoice->get_invoice_data_by_status($status, $this->user_id);
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);

        $sent_status = 2;
        $data['invoice_sent_count'] = $this->invoice->get_invoice_draft_count($sent_status, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/view_invoice', $data);
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
        $this->load->view('invoice/view_non_gst_invoice', $data);
    }
 public function send_invoice_whatsapp(){
    $this->load->library('session');

      $session_data_head2 = $this->session->userdata('session_data_head2');
        
    //print_r($session_data_head2);die();
        $set_company_name = $session_data_head2['company_name'];
        
        $set_company_logo = base_url() . $session_data_head2['company_logo'];
        
        
        $set_from_email = $session_data_head2['from_email'];
//        print_r($set_from_email);die();
        
        $set_cc_email = $session_data_head2['cc_email'];
      

        //enddata using session to set mail properties

        $invoice_number = $this->input->post('invoice_number');
      //  print_r($invoice_number);die();
        $invoice_data_group = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);
     
        
// print_r($invoice_data_group);die();
        $customer_name = $invoice_data_group['fullname'];
        $issue_date = $invoice_data_group['invoice_date'];
        $expires_date = $invoice_data_group['payment_due_date'];
        $grand_total = $invoice_data_group['total'];
        $mobile = $invoice_data_group['mobile'];
        $_SESSION['mobile'] = $mobile;
    //  print_r($_SESSION['mobile']);die();
        
       
 
        $to_email = $this->input->post('to_email');
       $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');
       $mobile = $this->input->post('mobile');
        
        //print_r($_SESSION['mobile']);die();

        $user_id_send = $this->user_id;
   //print_r($user_id_send);die();
        $url = base_url() . 'Pdf/download_invoice/' . $invoice_number . '/' . $user_id_send;
       
     // print_r($url);die();
     
     
        
//        
//       
//          $mobile = $_POST['mobile'];
//          $message = $_POST['message'];
//        $chatApiToken = ""; // Get it from https://www.phphive.info/255/get-whatsapp-password/
//$number = $mobile; // Number
//$message = $message; // Message
//$curl = curl_init();
//curl_setopt_array($curl, array(
//CURLOPT_URL => 'https://wa.me/?text=urlencodedtext',
//CURLOPT_RETURNTRANSFER => true,
//CURLOPT_ENCODING => '',
//CURLOPT_MAXREDIRS => 10,
//CURLOPT_TIMEOUT => 0,
//CURLOPT_FOLLOWLOCATION => true,
//CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//CURLOPT_CUSTOMREQUEST => 'POST',
//CURLOPT_POSTFIELDS =>json_encode(array( "message" => $message)),
//CURLOPT_HTTPHEADER => array(
//'Authorization: Bearer '.$chatApiToken,
//'Content-Type: application/json'
//),
//));
//$response = curl_exec($curl);
//curl_close($curl);
//echo $response;
        
//redirect('InvoiceController/index');
             
        
 }
    public function send_invoice_email() {
        //get data using session to set mail properties
        $session_data_head2 = $this->session->userdata('session_data_head2');
        
    // print_r($session_data_head2);die();
        $set_company_name = $session_data_head2['company_name'];
        
        $set_company_logo = base_url() . $session_data_head2['company_logo'];
        
        
        $set_from_email = $session_data_head2['from_email'];
//        print_r($set_from_email);die();
        
        $set_cc_email = $session_data_head2['cc_email'];
      

        //enddata using session to set mail properties

        $invoice_number = $this->input->post('invoice_number');
       
        $invoice_data_group = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);
      //  print_r($invoice_data_group);die();
        $customer_name = $invoice_data_group['fullname'];
        $issue_date = $invoice_data_group['invoice_date'];
        $expires_date = $invoice_data_group['payment_due_date'];
        $grand_total = $invoice_data_group['total'];
        $mobile = $invoice_data_group['mobile'];
          $mobile = $this->input->post('mobile');
       //print_r($mobile);die();
        
       
 
        $to_email = $this->input->post('to_email');
       $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');
     

        $user_id_send = $this->user_id;
   
        $url = base_url() . 'Pdf/download_invoice/' . $invoice_number . '/' . $user_id_send;
                      //  print_r($url);die();

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
        <title>Invoice</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <head>
        <title>Welcome to ' . $set_company_name . '</title>
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
           <img alt="' . $set_company_name . '" src="' . $set_company_logo . '" width="30%" style="font-size:16px;color:#b8b9c1;font-weight:normal;">
       
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
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If this invoice was sent in error, please contact" <a href="mailto:'. $set_from_email .'" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">'. $set_from_email .'</a></span>
         </div>
          <center><a href="https://xform.in/"><span style="text-decoration:none;color:#2f2f36; ">Powered by 
      <img alt="XForm Technologies" src="https://xform.in/images/logo.jpg" width="8%" height="8%" style="margin-top:3%;">
       Xform</span></a></center>
     </div>
     
    </body>
    </html>';

        // Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Xform <contact@xform.in>' . "\r\n";

        $this->email->message($htmlContent11);

        if ($this->email->send()) {

            //change invoice status
            $status = 2;
            $data_customer = array('status' => $status);
            $this->invoice->edit_invoice_status($data_customer, $invoice_number, $this->user_id);

            $this->session->set_flashdata('SUCCESSMSG', "Email Sent Successfully!!");
            redirect('InvoiceController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Email not Sent Successfully!!");
            redirect('InvoiceController/index');
        }
    }

    public function get_customer_email() {
        $invoice_number = $this->input->post('invoice_number');
        $result = $this->invoice->get_customer_email($invoice_number, $this->user_id);
        echo json_encode($result);
    }

    public function edit_ng_invoice() {
        $customer_id = $this->input->post('customer_id');
        $invoice_id = $this->input->post('invoice_id');

        $invoice_number = $this->input->post('invoice_number');
        $date = $this->input->post('date');

        $invoice_customer_po = $this->input->post('invoice_customer_po');
        $invoice_po_date = $this->input->post('invoice_po_date');

        $item = $this->input->post('term');
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
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If this invoice was sent in error, please contact" <a href="mailto:'. $set_from_email .'" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">'. $set_from_email .'</a></span>
         </div>
          <center><a href="https://xform.in/"><span style="text-decoration:none;color:#2f2f36; ">Powered by 
      <img alt="XForm Technologies" src="https://xform.in/images/logo.jpg" width="8%" height="8%" style="margin-top:3%;">
       Xform</span></a></center>
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

        $item = $this->input->post('term');
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
            'payment_method' => $payment_method, 'note' => $note, 'uid' => $session_data_head['result']['user_id'] ?? $this->user_id, 'shipping_address' => $shipping_address);
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
        $result = $this->invoice->delete_item($invoice_id);
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

    public function approve_invoice_status() {
        $invoice_no = $this->input->post('number_fk');
        $status = 4;
        $data = array(
            'status' => $status
        );
        $this->db->where('number_fk', $invoice_no);
        $this->db->update('invoice_total', $data);
        echo 'True';
    }
    
    
    
    
    
    
      public function duplicate_invoice() {
         
        $invoice_number = $this->input->post('invoice_number');
        $data1['show_invoice'] = $this->invoice->get_invoice_data($invoice_number, $this->user_id);
     //  $data['status_result'] = $this->invoice->get_status_by_invoiceid($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->invoice->get_duplicate_invoice_data_group_by($invoice_number, $this->user_id);
         $data['invoice_data_group']['paid']= 0; 
        $data2['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
       
      if (date('m') <= 3) {//Upto June 2014-2015
                $financial_year = (date('y') - 1) . '-' . date('y');
    } else {//After June 2015-2016
                $financial_year =  date('y') . '-' . (date('y') + 1);
    }
                $str = sprintf("%04d", $data2['invoice_id'] + 1);
               
                $invoice_number = 'INV/' . $str . '/' . $financial_year;

             $data['invoice_data_group']['number_fk']= $invoice_number; 
 
             $data['invoice_data_group']['uid']= $this->user_id; 


foreach ($data1['show_invoice'] as $key) {
              
                 if ($key->gst_type == 'I') {
                        $igst1 = $key->igst;
                        $sgst1 = '0';
                        $cgst1 = '0';
                    }if ($key->gst_type == 'S') {
                        $igst1 = '0';
                        $sgst1 = $key->sgst;
                        $cgst1 = $key->cgst;
                    }
                $data1 = array(
                    'invoice_number' => $invoice_number,
                    'invoice_date' =>  date("Y-m-d"),
                    'customer_id' => $key->customer_id,
                    'product_name' => $key->product_name,
                    'quantity' => $key->quantity,
                    'hsn_code' => $key->hsn_code,
                    'gst' => $key->gst,
                    'discount' => $key->discount,
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $key->gst_type,
                    'price' => $key->price,
                    'amount' => $key->amount,
                    'description' =>$key->description,
                    'uid' => $this->user_id,
                );
              

              $this->db->insert('invoice', $data1);
        }
     unset($data['invoice_data_group']['id']);
        
       
            $result = $this->invoice->add_invoice_total($data['invoice_data_group']);

            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Invoice submitted successfully!!");
                redirect('InvoiceController/index');
            } else {
                $this->session->set_flashdata('INFOMSG', "Invoice not submitted successfully!!");
                redirect('InvoiceController/index');
            }
        }
    public function get_datewise_record(){
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count(1, $this->user_id);
        $data['invoice_sent_count'] = $this->invoice->get_invoice_draft_count(2, $this->user_id);
        $data['invoices'] = $this->invoice->get_datewise_record($from_date, $to_date, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/view_credit_note', $data);
    }

    public function get_monthyearwise_record(){
        $month_year = $this->input->post('month_year');
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);

        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['invoice_sent_count'] = $this->invoice->get_invoice_draft_count($sent_status, $this->user_id);
        $data['invoices'] = $this->invoice->get_monthyearwise_record($month_year, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/view_credit_note', $data);
    }  


    function payment_in(){
        $data['company_name'] = $this->invoice->get_company_name_with_bal($this->user_id);

        $data['result'] = $this->invoice->get_payment_in($this->user_id);
        $data['result_by_id'] = null;
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/payment_in', $data);
    }


    function save_payment(){

        $payment_id = $this->input->post('payment_id');
        $payment_customer_id = $this->input->post('customer_id');
        $payment = $this->input->post('payment');
        $payment_date = $this->input->post('payment_date');
        $payment_type = $this->input->post('payment_type');
        $payment_bank = $this->input->post('payment_bank');
        $payment_method = $this->input->post('payment_method');
        $payment_note = $this->input->post('payment_note');
        $pay_date = strtotime($payment_date);
        $pay_date1 = date('Y-m-d', $pay_date);

        $data1 = array('payment_customer_id' => $payment_customer_id, 'payment' => $payment, 'pay_balance' => $payment,
        'payment_date' => $pay_date1, 'payment_type' => $payment_type, 
        'payment_bank' => $payment_bank, 'payment_method' => $payment_method,
        'payment_note' => $payment_note);

        if($payment_id != ""){
            $this->db->where('payment_id', $payment_id);
            $this->db->update('payment_in', $data1);
           
            $this->session->set_flashdata('SUCCESSMSG', "Payment in updated successfully!!");
            redirect('InvoiceController/payment_in');
        }else{
            $this->db->insert('payment_in', $data1);
        }

     

        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['result'] = $this->invoice->get_payment_in($this->user_id);

        

        if ($data['result']) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment in added submitted successfully!!");
            redirect('InvoiceController/payment_in');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not  submitted successfully!!");
            redirect('InvoiceController/payment_in');
        }
    }


    function get_pending_invoice_payment(){
        $customer_id_fk = $this->input->post('customer_id_fk');
        $result = $this->invoice->get_pending_invoice_payment($customer_id_fk, $this->user_id);
        echo json_encode($result);

    }


    public function getPaymentById() {
        $id = $this->input->get('id');
        $data['result_by_id'] = $this->invoice->getPaymentById($id);
//var_dump($data); die();
        $data['company_name'] = $this->invoice->get_company_name_with_bal($this->user_id);
        $data['result'] = $this->invoice->get_payment_in($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/payment_in', $data);
    }


  
     
    
            
}    
    
    
    
    
    
    