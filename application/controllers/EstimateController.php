<?php

defined('BASEPATH') or exit('No direct script access allowed');

class EstimateController extends MY_Controller
{

    protected $user_id;

    function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('estimate', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('salesorder', '', TRUE);
        $this->load->library('form_validation');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);

        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }




    public function get_quotation_data_by_status()
    {
        $status = $this->uri->segment(3);
        $data['estimates'] = $this->estimate->get_quotation_data_by_status($status, $this->user_id);
        $data['quo_count'] = $this->estimate->get_quo_count($this->user_id);

        $draft_status = 1;
        $data['quo_draft_count'] = $this->estimate->get_quo_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['quo_sent_count'] = $this->estimate->get_quo_draft_count($sent_status, $this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/view_estimate', $data);
    }

    public function index()
    {
        $str = $this->input->get('str');
        //print_r($str);die();
        if ($str == "All" || $str === null) {
            $data['estimates'] = $this->estimate->get_estimates($this->user_id);
            // print_r($data['estimates']);die();
        } else {
            $month_year = date('M-Y');
            $data['estimates'] = $this->estimate->get_monthyearwise_record($month_year, $this->user_id);
        }
        $data['quo_count'] = $this->estimate->get_quo_count($this->user_id);
        $draft_status = 1;
        $data['quo_draft_count'] = $this->estimate->get_quo_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['quo_sent_count'] = $this->estimate->get_quo_draft_count($sent_status, $this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['quotation_id'] = $this->estimate->get_last_quotation_number($this->user_id);
        // $data['non_gst_quotation_id'] = $this->estimate->get_last_non_gst_quotation_number($this->user_id);
        // $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        //$data['invoice_id'] = $this->invoice->get_last_non_gst_invoice_number($this->user_id);
        $data['company_name'] = $this->estimate->get_company_name($this->user_id);
        // print_r($data['company_name']);die();
        $data['result'] = $this->estimate->get_customer($this->user_id);
        // print_r($data['result']);die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/view_estimate', $data);
    }

    public function non_gst_index()
    {

        $data['non_gst_estimates'] = $this->estimate->get_non_gst_estimates($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['non_gst_quotation_id'] = $this->estimate->get_last_non_gst_quotation_number($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['company_name'] = $this->estimate->get_company_name($this->user_id);
        $data['result'] = $this->estimate->get_customer($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/view_non_gst_estimate', $data);
    }

    public function create_estimate()
    {
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['quotation_id'] = $this->estimate->get_last_quotation_number($this->user_id);
        $data['non_gst_quotation_id'] = $this->estimate->get_last_non_gst_quotation_number($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['result'] = $this->estimate->get_customer($this->user_id);
        $data['company_name'] = $this->estimate->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/create_estimate', $data);
    }

    public function create_igst_estimate()
    {
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);


        $data['quotation_id'] = $this->estimate->get_last_quotation_number($this->user_id);
        // $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $data['result'] = $this->estimate->get_customer($this->user_id);
        $data['company_name'] = $this->estimate->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/create_igst_estimate', $data);
    }

    public function create_gst_estimate()
    {

        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        // print_r($data['item_name']);die();
        $data['quotation_id'] = $this->estimate->get_last_quotation_number($this->user_id);
        // $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);

        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);

        //print_r($data['quotation_id']);die();



        $data['result'] = $this->estimate->get_customer($this->user_id);
        $data['company_name'] = $this->estimate->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/create_gst_estimate', $data);
    }

    public function print_quotation()
    {

        $id = $this->uri->segment(3);
        $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];
        $data['show_quotation'] = $this->estimate->get_estimates_data($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, $this->user_id);

        $this->session->userdata('session_data_head');
        $this->load->view('admin/print_igst_quote', $data);
    }

    public function print_igst_quotation()
    {

        $id = $this->uri->segment(3);
        $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];
        $data['show_quotation'] = $this->estimate->get_estimates_data($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, $this->user_id);

        $this->session->userdata('session_data_head');
        $this->load->view('admin/print_igst_quote', $data);
    }

    public function print_non_gst_quotation()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['show_non_gst_quotation'] = $this->estimate->get_non_gst_estimates_data($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['non_gst_estimates_data_group'] = $this->estimate->get_non_gst_estimates_data_group_by($number, $this->user_id);
        $this->session->userdata('session_data_head');
        $this->load->view('admin/non_gst_quotation_preview', $data);
    }

    public function show_quotation()
    {
        $id = $this->uri->segment(3);
        $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];

// print_r($number);
//         die();

        //        $id1 = $this->uri->segment(4);
        //        $id2 = $this->uri->segment(5);
        //        $id3 = $this->uri->segment(6);
        //        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;

        $data['show_quotation'] = $this->estimate->get_estimates_data($number, $this->user_id);

        
        $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, $this->user_id);

        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['next_invoice_name'] = $this->invoice->get_next_invoice_name($this->user_id);
        $data['salesorder_id'] = $this->salesorder->get_last_salesorder_number($this->user_id);


        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/show_quotation', $data);
    }

    public function show_non_gst_quotation()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['show_non_gst_quotation'] = $this->estimate->get_non_gst_estimates_data($number, $this->user_id);
        $data['non_gst_estimates_data_group'] = $this->estimate->get_non_gst_estimates_data_group_by($number, $this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_non_gst_invoice_number($this->user_id);

        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/show_non_gst_quotation', $data);
    }

    public function add_customer()
    {
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        if ($fullname == '') {
            $fullname = $company_name;
        }
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $address = $this->input->post('address');
        $data_customer = array(
            'company_name' => $company_name,
            'fullname' => $fullname,
            'pancard' => $pancard,
            'gst' => $gst,
            'email' => $email,
            'mobile' => $mobile,
            'address' => $address,
            'uid' => $this->user_id
        );
        $result = $this->estimate->customer_check($company_name, $this->user_id);

        //sgst and cgst quotation_gst_check create_gst_estimate
        $quotation_gst_check = $this->input->post('redirect_quotation');

        //non gst quotation_non_gst_check create_estimate
        $quotation_non_gst_check = $this->input->post('redirect_ng_quotation');
        //igst quotation_igst_check  create_igst_estimate
        $quotation_igst_check = $this->input->post('redirect_igst_quotation');

        if ($result == FALSE) {
            $this->customer->add_customer($data_customer);
            $this->session->set_flashdata('SUCCESSMSG', "Company added successfully!!");

            if ($quotation_gst_check) {
                redirect('EstimateController/create_gst_estimate');
            } else if ($quotation_igst_check) {
                redirect('EstimateController/create_igst_estimate');
            } else {
                redirect('EstimateController/create_estimate');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Company already exist!!");
            if ($quotation_gst_check) {
                redirect('EstimateController/create_gst_estimate');
            } else if ($quotation_igst_check) {
                redirect('EstimateController/create_igst_estimate');
            } else {
                redirect('EstimateController/create_estimate');
            }
        }
    }

    public function edit_customer()
    {
        $customer_id = $this->input->post('customer_id');
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $address = $this->input->post('address');
        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'pancard' => $pancard, 'gst' => $gst, 'email' => $email, 'mobile' => $mobile, 'address' => $address);
        $result = $this->customer->edit_customer($data_customer, $customer_id, $this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Company updated successfully!!");
            redirect('CustomerController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Company not updated successfully!!");
            redirect('CustomerController/index');
        }
    }

    public function get_customer_by_id()
    {
        $id = $this->uri->segment(3);
        $data['customer'] = $this->customer->get_customer_by_id($id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('customer/edit_customer', $data);
    }

    public function delete_customer_by_id()
    {
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

    public function get_product_name()
    {
        $keyword = $this->input->get('term', TRUE);
        $product_name = $this->inventory->get_product_name($keyword);
        $dname_list1 = array();
        if (count($product_name) > 0) {
            foreach ($product_name as $value) {
                $dname_list1[] = $value->code;
            }
            echo json_encode($dname_list1);
        }
    }

    public function get_estimate()
    {
        $product_name = $this->input->post('item_name');
        $company_id = $this->input->post('company_id');
        $result1 = $this->estimate->get_inventory_id_by_code($product_name);
        $result_rate = $this->estimate->get_customer_wise_rate($result1['inventory_id'], $company_id);
        $result = $this->estimate->get_estimate($product_name, $this->user_id);

        if ($result_rate) {
            unset($result['sell_price']);
            $result['sell_price'] = $result_rate['customer_rate'];
        }

        echo json_encode($result);
    }

    public function add_new_estimate_customer()
    {

        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $customer_pancard_no = $this->input->post('customer_pancard_no');
        $customer_gst_no = $this->input->post('customer_gst_no');
        $customer_email = $this->input->post('customer_email');
        $customer_mobile = $this->input->post('customer_mobile');
        $customer_address = $this->input->post('customer_address');





        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'customer_pancard_no' => $customer_pancard_no, 'customer_gst_no' => $customer_gst_no, 'customer_email' => $customer_email, 'customer_mobile' => $customer_mobile, 'customer_address' => $customer_address);
        $result = $this->estimate->customer_check($customer_mobile);

        if ($result == FALSE) {
            $this->estimate->add_customer($data_customer);
            echo json_encode($result);
        }
    }

    public function get_customer_name_to_append_dropdown()
    {

        $query = $this->db->query("select customer_firstname from customer");
        $row_vendor_name = $query->row_array();
        $data = array("customer_firstname" => $row_vendor_name['customer_firstname']);
        echo json_encode($data);
    }

    public function add_estimate_quotation()
    {
        // Get and format dates first
        $raw_date = $this->input->post('date');
        $raw_expires = $this->input->post('expires_date');

        // Format dates using helper function
        $formatted_date = $this->format_date_for_db($raw_date);
        $formatted_expires = $this->format_date_for_db($raw_expires);

        // Optional: Debug - remove in production
        error_log("Add Estimate - Raw date: " . $raw_date . " -> Formatted: " . $formatted_date);
        error_log("Add Estimate - Raw expires: " . $raw_expires . " -> Formatted: " . $formatted_expires);

        $customer_id = $this->input->post('customer_id');
        $number = $this->input->post('number');
        $date = $this->input->post('date');
        $expires_date = $this->input->post('expires_date');
        $po = $this->input->post('po');
        $sez = $this->input->post('sez');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');

        //Check Quotation gst type
        $quotation_non_gst_check = $this->input->post('quotation_non_gst_check');
        $quotation_gst_check = $this->input->post('quotation_gst_check');
        $quotation_igst_check = $this->input->post('quotation_igst_check');

        $sgst = '0';
        $igst = '0';
        $gst_type = '';

        if ($quotation_non_gst_check) {
            $sgst = '0';
            $cgst = '0';
            $igst = '0';
        } else if ($quotation_gst_check) {
            $igst = '1';
            $sgst = $this->input->post('sgst');
            $cgst = $this->input->post('cgst');
            $gst_type = "S";
        } else if ($quotation_igst_check) {
            $igst = $this->input->post('igst');
            $sgst = '1';
            $gst_type = "I";
        }

        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $discount = $this->input->post('discount');
        $unit = $this->input->post('unit');

        //total amount without gst
        $basic_total = $this->input->post('basic_total');

        $quotation_subheading = $this->input->post('quotation_subheading');
        $quotation_footer = $this->input->post('quotation_footer');
        $quotation_memo = $this->input->post('quotation_memo');
        $terms_and_conditions = $this->input->post('terms_and_conditions');
        $payment_terms = $this->input->post('payment_terms');
        $process_schedule = $this->input->post('process_schedule');
        $taxes = $this->input->post('taxes');
        $exclusions = $this->input->post('exclusions');
        $description = $this->input->post('description');

        $total_quotation_amount = $this->input->post('total_quotation_amount');
        $total_quotation_amount = round($total_quotation_amount);
        $status = $this->input->post('status');
        $enquiry = $this->input->post('enquiry');

        $item_count = count($item);
        $flag = 1; // Initialize flag as 1 (error)

        //For Non Gst insert into non_gst_quotation table..
        if ($quotation_non_gst_check) {
            $data = array(); // Initialize data array
            for ($i = 0; $i < $item_count; $i++) {
                if (!empty($item[$i]) && !empty($quantity[$i]) && !empty($hsn[$i]) && !empty($price[$i]) && !empty($amount[$i])) {

                    $data[] = array(
                        'customer_id' => $customer_id,
                        'number' => $number,
                        'date' => $formatted_date, // Use formatted date
                        'exp_date' => $formatted_expires, // Use formatted expires date
                        'product_name' => $item[$i],
                        'quantity' => $quantity[$i],
                        'hsn_code' => $hsn[$i],
                        'price' => $price[$i],
                        'amount' => $amount[$i],
                        'discount' => !empty($discount[$i]) ? $discount[$i] : 0,
                        'unit' => !empty($unit[$i]) ? $unit[$i] : '',
                        'description' => !empty($description[$i]) ? $description[$i] : '',
                        'uid' => $this->user_id,
                    );

                    $flag = 0; // Set flag to 0 (success)
                }
            }

            if ($flag == 0 && !empty($data)) {
                $this->db->insert_batch('non_gst_quotation', $data);

                $data_total_amount = array(
                    'basic_total' => $basic_total,
                    'total' => $total_quotation_amount,
                    'customer_id_fk' => $customer_id,
                    'number_fk' => $number,
                    'status' => $status,
                    'enquiry' => $enquiry,
                    'uid' => $this->user_id,
                );

                $result = $this->estimate->add_total_amount_non_gst($data_total_amount);

                if ($result == TRUE) {
                    $this->session->set_flashdata('SUCCESSMSG', "Quotation added successfully!!");
                    redirect('EstimateController/non_gst_index');
                } else {
                    $this->session->set_flashdata('INFOMSG', "Quotation not added successfully!!");
                    redirect('EstimateController/non_gst_index');
                }
            }
        }
        // For GST quotations
        else {
            $data = array(); // Initialize data array
            for ($i = 0; $i < $item_count; $i++) {
                if (!empty($item[$i]) && !empty($quantity[$i]) && !empty($hsn[$i]) && !empty($price[$i]) && !empty($amount[$i])) {

                    // Set GST values based on type
                    if ($quotation_igst_check) {
                        // IGST case
                        $igst1 = !empty($igst[$i]) ? $igst[$i] : 0;
                        $sgst1 = '0';
                        $cgst1 = '0';
                    } else {
                        // CGST/SGST case
                        $igst1 = '0';
                        $sgst1 = !empty($sgst[$i]) ? $sgst[$i] : 0;
                        $cgst1 = !empty($cgst[$i]) ? $cgst[$i] : 0;
                    }

                    $data[] = array(
                        'customer_id' => $customer_id,
                        'number' => $number,
                        'date' => $formatted_date, // Use formatted date
                        'exp_date' => $formatted_expires, // Use formatted expires date
                        'product_name' => $item[$i],
                        'quantity' => $quantity[$i],
                        'hsn_code' => $hsn[$i],
                        'gst' => !empty($gst_per[$i]) ? $gst_per[$i] : 0,
                        'sgst' => $sgst1,
                        'cgst' => $cgst1,
                        'igst' => $igst1,
                        'gst_type' => $gst_type,
                        'price' => $price[$i],
                        'amount' => $amount[$i],
                        'discount' => !empty($discount[$i]) ? $discount[$i] : 0,
                        'unit' => !empty($unit[$i]) ? $unit[$i] : '',
                        'description' => !empty($description[$i]) ? $description[$i] : '',
                        'uid' => $this->user_id,
                    );

                    $flag = 0; // Set flag to 0 (success)
                }
            }

            if ($flag == 0 && !empty($data)) {
                $this->db->insert_batch('quotation', $data);

                $project_code = $this->input->post('project_code') ?? '';

                $logged_in_uid = $session_data_head['result']['user_id'] ?? $this->user_id;

                $data_total_amount = array(
                    'basic_total' => $basic_total,
                    'total' => $total_quotation_amount,
                    'customer_id_fk' => $customer_id,
                    'number_fk' => $number,
                    'status' => $status,
                    'enquiry' => $enquiry,
                    'uid' => $logged_in_uid,
                    'sez' => $sez,
                    'quotation_subheading' => $quotation_subheading,
                    'quotation_footer' => $quotation_footer,
                    'quotation_memo' => $quotation_memo,
                    'terms_and_conditions' => $terms_and_conditions,
                    'payment_terms' => $payment_terms,
                    'process_schedule' => $process_schedule,
                    'taxes' => $taxes,
                    'exclusions' => $exclusions,
                    'project_code' => $project_code,
                    'system' => $this->input->post('system') ?? '',
                    'location' => $this->input->post('location') ?? '',
                    'capacity' => $this->input->post('capacity') ?? '',
                );

                $result = $this->estimate->add_total_amount($data_total_amount);

                if ($result == TRUE) {
                    $this->session->set_flashdata('SUCCESSMSG', "Quotation added successfully!!");
                    redirect('EstimateController/index');
                } else {
                    $this->session->set_flashdata('INFOMSG', "Quotation not added successfully!!");
                    redirect('EstimateController/index');
                }
            }
        }

        // If flag is 1 (error) or no data inserted
        if ($flag == 1) {
            $this->session->set_flashdata('INFOMSG', "Please fill all required fields!!");
            if ($quotation_non_gst_check) {
                redirect('EstimateController/create_estimate');
            } else if ($quotation_gst_check) {
                redirect('EstimateController/create_gst_estimate');
            } else if ($quotation_igst_check) {
                redirect('EstimateController/create_igst_estimate');
            } else {
                redirect('EstimateController/create_estimate');
            }
        }
    }

    /**
     * Format any date string to Y-m-d format for database
     * @param string $date_string
     * @return string|null
     */
    private function format_date_for_db($date_string)
    {
        if (empty($date_string)) {
            return null;
        }

        // Remove any extra whitespace
        $date_string = trim($date_string);

        // If it's already in Y-m-d format and valid
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_string)) {
            $parts = explode('-', $date_string);
            if (checkdate($parts[1], $parts[2], $parts[0])) {
                return $date_string;
            }
        }

        // Try to parse with strtotime first (handles many formats)
        $timestamp = strtotime($date_string);
        if ($timestamp !== false && $timestamp > 0) {
            return date('Y-m-d', $timestamp);
        }

        // Try common formats with DateTime
        $formats = [
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'm/d/Y',
            'm-d-Y',
            'm.d.Y',
            'Y/m/d',
            'Y-m-d',
            'Y.m.d',
            'd M Y',
            'd M, Y',
            'M d, Y',
            'd F Y',
            'd F, Y',
            'F d, Y'
        ];

        foreach ($formats as $format) {
            $date_obj = DateTime::createFromFormat($format, $date_string);
            if ($date_obj !== false) {
                // Validate the date
                $year = $date_obj->format('Y');
                $month = $date_obj->format('m');
                $day = $date_obj->format('d');
                if (checkdate($month, $day, $year)) {
                    return $date_obj->format('Y-m-d');
                }
            }
        }

        // If all parsing fails, log error and return current date as fallback
        error_log("Failed to parse date: " . $date_string . " - using current date instead");
        return date('Y-m-d');
    }

    public function convert_to_invoice()
    {
        //        $id = $this->uri->segment(3);
        //        $id1 = $this->uri->segment(4);
        //        $id2 = $this->uri->segment(5);
        //        $id3 = $this->uri->segment(6);
        //        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        //        echo $number;die();

        $id = $this->uri->segment(3);

        $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);




        $number = $quote_number_id['number_fk'];

        $invoice_number = strtoupper(trim($this->input->post('invoice_number')));

        if ($invoice_number === '') {
            $this->session->set_flashdata('INFOMSG', "Invoice name is required!");
            redirect('EstimateController/show_quotation/' . $id);
            return;
        }

        if ($this->invoice->invoice_name_exists($invoice_number, $this->user_id)) {
            $this->session->set_flashdata('invoice_duplicate_error', "Same name invoice present, can't create!");
            redirect('EstimateController/show_quotation/' . $id);
            return;
        }

        $total = 0;
        $payment_method = 0;
        $status = 0;
        $invoice_date = 0;
        $exp_date = 0;
        $invoice_subheading = '';
        $invoice_footer = '';
        $invoice_memo = '';
        $data = $this->estimate->get_convert_invoice_data($number, $this->user_id);
        $basic_total = 0;
        $total_gst_amount = 0;
        if (!empty($data)) {
            $basic_total = isset($data[0]->basic_total) ? $data[0]->basic_total : 0;
            $total_gst_amount = isset($data[0]->total) ? ($data[0]->total - $basic_total) : 0;
        }

        foreach ($data as $key) {

            unset($key->enquiry);
            if (!isset($key->discount) || $key->discount === null) {
                $key->discount = 0;
            }
            unset($key->basic_total);
            unset($key->number);
            //unset($key->date);
            $key->invoice_number = $invoice_number;

            $invoice_date = $key->date;
            $exp_date = $key->exp_date;
            $key->invoice_date = $this->format_date_for_db($invoice_date);
            // Format exp_date immediately to ensure it's valid
            $exp_date = $this->format_date_for_db($exp_date);

            $total = $key->total;
            $payment_method = $key->payment_method;
            $invoice_subheading = $key->quotation_subheading;
            $invoice_footer = $key->quotation_footer;
            $invoice_memo = $key->quotation_memo;
            $status = $key->status;

            unset($key->id);
            unset($key->quotation_id);
            unset($key->date);
            unset($key->exp_date);
            unset($key->quotation_subheading);
            unset($key->quotation_footer);
            unset($key->quotation_memo);
            unset($key->terms_and_conditions);
            unset($key->payment_terms);
            unset($key->process_schedule);
            unset($key->taxes);
            unset($key->exclusions);
            unset($key->number_fk);
            unset($key->total);
            unset($key->customer_id_fk);
            unset($key->payment_method);
            unset($key->status);
            unset($key->approved_by);
            unset($key->sez);


            //print_r($key);die();


            $result1 = $this->estimate->add_invoice($key);


            //Update stock when quotation converted to invoice
            //            $stock = $this->invoice->get_inventory_stock_count($key->product_name, $key->uid);
            //            $datas = array(
            //                'stock' => $stock['stock'] - $key->quantity,
            //            );
            //            $this->db->where('uid', $key->uid);
            //            $this->db->where('code', $key->product_name);
            //            $this->db->update('inventory', $datas);
        }

        foreach ($data as $key) {

            unset($key->description);
            unset($key->enquiry);
            unset($key->discount);
            unset($key->basic_total);
            unset($key->invoice_number);
            // unset($key->payment_due_date);

            $key->number_fk = $invoice_number;

            $invoice_memo = $key->customer_id;
            $key->customer_id_fk = $invoice_memo;

            //            $date = $key->date;
            //            $key->date = $date;

            $key->date = $exp_date;
            $key->payment_due_date = $exp_date;
            $key->total = $total;
            $key->payment_method = $payment_method;
            //            $key->status = $status;
            $key->status = 1;

            $key->balance = $total;
            $key->total_before_tax = $basic_total;
            $key->total_gst_amount = $total_gst_amount;

            //            $payment_due_date = $key->date;
            //            $key->payment_due_date = $payment_due_date;
            //            $invoice_subheading = $key->quotation_subheading;
            //            $key->invoice_subheading = $invoice_subheading;
            //
            //            $invoice_footer = $key->quotation_footer;
            //            $key->invoice_footer = $invoice_footer;
            //
            //            $invoice_memo1 = $key->quotation_memo;
            //            $key->invoice_memo = $invoice_memo1;


            unset($key->id);
            unset($key->quotation_id);
            unset($key->number);
            unset($key->customer_id);
            unset($key->invoice_date);
            unset($key->exp_date);
            unset($key->product_name);
            unset($key->quantity);
            unset($key->unit);
            unset($key->hsn_code);
            unset($key->gst);
            unset($key->sgst);
            unset($key->cgst);
            unset($key->igst);
            unset($key->gst_type);
            unset($key->price);
            unset($key->amount);
            unset($key->description);
            unset($key->gst_type);
        }

        $result = $this->invoice->add_invoice_total($key);
        if (($result == TRUE) && ($result1 == TRUE)) {
            $this->session->set_flashdata('SUCCESSMSG', "Quotation converted to invoice successfully!!");
            redirect('InvoiceController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Quotation not converted to invoice successfully!!");
            redirect('InvoiceController/index');
        }
    }

    public function delete_quotation_by_quote_number()
    {
        $quote_number = $this->uri->segment(3);
        $result = $this->estimate->delete_quotation_by_quote_number($quote_number, $this->user_id);
        $result1 = $this->estimate->delete_quotation_total_by_quote_number($quote_number, $this->user_id);
        if (($result == TRUE) && ($result1 == TRUE)) {
            $this->session->set_flashdata('SUCCESSMSG', "Quotation deleted successfully!!");
            redirect('EstimateController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Quotation not deleted successfully!!");
            redirect('EstimateController/index');
        }
    }

    public function get_settings()
    {
        $result = $this->estimate->get_settings($this->user_id);
        echo json_encode($result);
    }

    public function edit_estimate_details()
    {
        //dddddddddddddd
        $id = $this->uri->segment(3);
        //print_r($id);die();
        $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);

        $number = $quote_number_id['number_fk'];

        //        $id = $this->uri->segment(3);
        //        $id1 = $this->uri->segment(4);
        //        $id2 = $this->uri->segment(5);
        //        $id3 = $this->uri->segment(6);
        //        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['show_quotation'] = $this->estimate->get_estimates_data($number, $this->user_id);
        $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, $this->user_id);
        $data['status_result'] = $this->estimate->get_status($number, $this->user_id);
        $data['enquiry_status'] = $this->estimate->get_enquiry_status($number, $this->user_id);
        $data['customer_result'] = $this->estimate->get_company_name($this->user_id);
        $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/edit_estimate', $data);
    }

    //    public function edit_non_gst_estimate_details() {
    //        $id = $this->uri->segment(3);
    //        $id1 = $this->uri->segment(4);
    //        $id2 = $this->uri->segment(5);
    //        $id3 = $this->uri->segment(6);
    //        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
    //        $data['show_non_gst_quotation'] = $this->estimate->get_non_gst_estimates_data($number, $this->user_id);
    //        $data['status_result'] = $this->estimate->get_status_ng($number, $this->user_id);
    //
    //        $data['non_gst_enquiry_status'] = $this->estimate->get_non_gst_enquiry_status($number, $this->user_id);
    //
    //        $data['non_gst_estimates_data_group'] = $this->estimate->get_non_gst_estimates_data_group_by($number, $this->user_id);
    //        $data['customer_result'] = $this->customer->get_customer($this->user_id);
    //        $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);
    //        $session_data_head = $this->session->userdata('session_data_head');
    //        $this->load->view('admin/header_side_bar', $session_data_head);
    //        $this->load->view('estimate/edit_non_gst_estimate', $data);
    //    }

    public function get_stock_by_item_name()
    {

        $item_name = $this->input->post('item_name');

        $total_quantity = $this->inventory->get_stock_by_item_name($item_name, $this->user_id);
        echo json_encode($total_quantity);
    }

    public function edit_estimate_quotation()
    {

        $customer_id = $this->input->post('customer_id');
        $quotation_id = $this->input->post('quotation_id');

        $number = $this->input->post('number');
        $revision = $this->input->post('revision');
        if ($revision == 'Y') {
            if (preg_match('/\/R(\d+)$/', $number, $matches)) {
                $revNum = intval($matches[1]) + 1;
                $numberRev = preg_replace('/\/R\d+$/', '/R' . $revNum, $number);
            } else {
                $numberRev = $number . "/R1";
            }
        } else {
            $numberRev = $number;
        }

        // Format dates using helper function
        $raw_date = $this->input->post('date');
        $raw_expires = $this->input->post('expires_date');
        $date = $this->format_date_for_db($raw_date);
        $expires_date = $this->format_date_for_db($raw_expires);
        $po = $this->input->post('po');
        $quotation_subheading = $this->input->post('quotation_subheading');
        $quotation_footer = $this->input->post('quotation_footer');
        $quotation_memo = $this->input->post('quotation_memo');
        $terms_and_conditions = $this->input->post('terms_and_conditions');
        $payment_terms = $this->input->post('payment_terms');
        $process_schedule = $this->input->post('process_schedule');
        $taxes = $this->input->post('taxes');
        $exclusions = $this->input->post('exclusions');

        $description = $this->input->post('description');


        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');


// print_r($unit);
//         die();

        $quotation_non_gst_check = $this->input->post('non_gst');
        $quotation_gst_check = $this->input->post('gst');
        $quotation_igst_check = $this->input->post('igst_edit_hide_show');

        $sgst = '0';
        $igst = '0';
        if ($quotation_non_gst_check) {
            $sgst = '0';
            $cgst = '0';
            $igst = '0';
        } else if ($quotation_gst_check) {
            $igst = '1';
            $sgst = $this->input->post('sgst');
            $cgst = $this->input->post('cgst');
            $gst_type = "S";
        } else if ($quotation_igst_check) {
            $igst = $this->input->post('igst');
            $sgst = '1';
            $gst_type = "I";
        }

        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $discount = $this->input->post('discount');

        //total amount without gst
        $basic_total = $this->input->post('basic_total');

        $total_quotation_amount = $this->input->post('total_quotation_amount');
        $total_quotation_amount = round($total_quotation_amount);

        $status = $this->input->post('status');
        $enquiry = $this->input->post('enquiry');




        $item_count = count($item);
        $data = array();
        //Check for non gst to edit 
        if ($quotation_non_gst_check) {

            for ($i = 0; $i < $item_count; $i++) {
                if ($item[$i] != '' && $quantity[$i] != '' && $hsn[$i] != '' && $price[$i] != '') {

                    if ($quotation_id[$i]) {
                        $data = array(
                            'customer_id' => $customer_id,
                            'number' => $number,
                            'date' => $date,
                            'exp_date' => $expires_date,
                            'product_name' => $item[$i],
                            'quantity' => $quantity[$i],
                            'unit' => $unit[$i],
                            'hsn_code' => $hsn[$i],
                            'price' => $price[$i],
                            'amount' => $amount[$i],
                            'discount' => $discount[$i],
                            'description' => $description[$i],
                        );


                        $this->db->where('uid', $this->user_id);
                        $this->db->where('number', $number);
                        $this->db->where('quotation_id', $quotation_id[$i]);
                        $this->db->update('non_gst_quotation', $data);
                    } else {
                        $data_insert = array(
                            'customer_id' => $customer_id,
                            'number' => $number,
                            'date' => $date,
                            'exp_date' => $expires_date,
                            'product_name' => $item[$i],
                            'quantity' => $quantity[$i],
                            'unit' => $unit[$i],
                            'hsn_code' => $hsn[$i],
                            'price' => $price[$i],
                            'amount' => $amount[$i],
                            'discount' => $discount[$i],
                            'description' => $description[$i],
                            'uid' => $this->user_id,
                        );

                        $this->db->insert('non_gst_quotation', $data_insert);
                    }
                }
            }
        } else {

            for ($i = 0; $i < $item_count; $i++) {
                if ($item[$i] != '' && $quantity[$i] != '' && $hsn[$i] != '' && $price[$i] != '' && $amount[$i] != '') {
                    if ($sgst == '1') {
                        $igst1 = $igst[$i];
                        $sgst1 = '0';
                        $cgst1 = '0';
                    }
                    if ($igst == '1') {
                        $igst1 = '0';
                        $sgst1 = $sgst[$i];
                        $cgst1 = $cgst[$i];
                    }
                    if ($quotation_id[$i]) {

                        if ($revision == 'Y') {
                            // Insert new revision record, keep original untouched
                            $data_insert_rev = array(
                                'customer_id' => $customer_id,
                                'number' => $numberRev,
                                'date' => $date,
                                'exp_date' => $expires_date,
                                'product_name' => $item[$i],
                                'quantity' => $quantity[$i],
                                'unit' => $unit[$i],
                                'hsn_code' => $hsn[$i],
                                'gst' => $gst_per[$i],
                                'sgst' => $sgst1,
                                'cgst' => $cgst1,
                                'igst' => $igst1,
                                'gst_type' => $gst_type,
                                'price' => $price[$i],
                                'amount' => $amount[$i],
                                'discount' => $discount[$i],
                                'description' => $description[$i],
                                'uid' => $this->user_id,
                            );
                            $this->db->insert('quotation', $data_insert_rev);
                        } else {
                            $data = array(
                                'customer_id' => $customer_id,
                                'number' => $numberRev,
                                'date' => $date,
                                'exp_date' => $expires_date,
                                'product_name' => $item[$i],
                                'quantity' => $quantity[$i],
                                'unit' => $unit[$i],
                                'hsn_code' => $hsn[$i],
                                'gst' => $gst_per[$i],
                                'sgst' => $sgst1,
                                'cgst' => $cgst1,
                                'igst' => $igst1,
                                'gst_type' => $gst_type,
                                'price' => $price[$i],
                                'amount' => $amount[$i],
                                'discount' => $discount[$i],
                                'description' => $description[$i],
                            );

                            //commented for quotation  $this->db->where('uid', $this->user_id);
                            $this->db->where('number', $number);
                            $this->db->where('quotation_id', $quotation_id[$i]);
                            $this->db->update('quotation', $data);
                        }
                    } else {
                        if ($sgst == '1') {
                            $igst1 = $igst[$i];
                            $sgst1 = '0';
                            $cgst1 = '0';
                        }
                        if ($igst == '1') {
                            $igst1 = '0';
                            $sgst1 = $sgst[$i];
                            $cgst1 = $cgst[$i];
                        }
                        $data_insert = array(
                            'customer_id' => $customer_id,
                            'number' => $numberRev,
                            'date' => $date,
                            'exp_date' => $expires_date,
                            'product_name' => $item[$i],
                            'quantity' => $quantity[$i],
                            'unit' => $unit[$i],
                            'hsn_code' => $hsn[$i],
                            'gst' => $gst_per[$i],
                            'sgst' => $sgst1,
                            'cgst' => $cgst1,
                            'igst' => $igst1,
                            'gst_type' => $gst_type,
                            'price' => $price[$i],
                            'amount' => $amount[$i],
                            'discount' => $discount[$i],
                            'description' => $description[$i],
                            'uid' => $this->user_id,
                        );
                        // print_r($data_insert);die();
                        //    echo $description[$i] . "<br><br><br><br><br>";

                        $this->db->insert('quotation', $data_insert);
                    }
                }
            }
        }


        // die();
        $app_uid = '';
        if ($status == '4') {
            $app_uid = $this->user_id;
        }

        $project_code = $this->input->post('project_code') ?? '';

        $data_toatl_amount = array(
            'basic_total' => $basic_total,
            'total' => $total_quotation_amount,
            'number_fk' => $numberRev,
            'customer_id_fk' => $customer_id,
            'status' => $status,
            'enquiry' => $enquiry,
            'approved_by' => $app_uid,
            'quotation_subheading' => $quotation_subheading,
            'quotation_footer' => $quotation_footer,
            'quotation_memo' => $quotation_memo,
            'terms_and_conditions' => $terms_and_conditions,
            'payment_terms' => $payment_terms,
            'process_schedule' => $process_schedule,
            'taxes' => $taxes,
            'exclusions' => $exclusions,
            'project_code' => $project_code,
            'system' => $this->input->post('system') ?? '',
            'location' => $this->input->post('location') ?? '',
            'capacity' => $this->input->post('capacity') ?? '',
        );
        if ($quotation_non_gst_check) {
            $result = $this->estimate->edit_total_non_gst_amount($data_toatl_amount, $number, $this->user_id);
        } else {
            if ($revision == 'Y') {
                // Fetch existing sez value from original quotation_total (live DB may have this column)
                $existing_qt = $this->db->select('*')->from('quotation_total')->where('number_fk', $number)->get()->row_array();
                $data_toatl_amount['uid'] = $this->user_id;
                $data_toatl_amount['payment_method'] = 0;
                if (isset($existing_qt['sez'])) {
                    $data_toatl_amount['sez'] = $existing_qt['sez'];
                }
                $result = $this->estimate->add_total_amount($data_toatl_amount);
            } else {
                $result = $this->estimate->edit_total_amount($data_toatl_amount, $number, $this->user_id);
            }
        }

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Quotation edited successfully!!");
            if ($quotation_non_gst_check) {
                redirect('EstimateController/non_gst_index');
            }
            redirect('EstimateController/index');
        }
        redirect('EstimateController/index');
    }

    public function delete_item()
    {
        $quotation_id = $this->input->post('quotation_id');
        $result = $this->estimate->delete_item($quotation_id);
        echo json_encode($result);
    }

    public function print_invoice()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;

        $data['show_quotation'] = $this->estimate->get_estimates_data($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, $this->user_id);
        $this->session->userdata('session_data_head');
        $this->load->view('admin/print', $data);
    }



    public function get_ng_quotation_data_by_status()
    {
        $status = $this->uri->segment(3);
        $data['non_gst_estimates'] = $this->estimate->get_ng_quotation_data_by_status($status, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/view_non_gst_estimate', $data);
    }

    public function send_quotation_email()
    {

        $quote_number = $this->input->post('number');
        $check_non_gst_email = $this->input->post('check_non_gst_email');

        //get data using session to set mail properties
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'];
        $set_company_logo = base_url() . '/' . $session_data_head2['company_logo'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];
        //enddata using session to set mail properties


        $data['settings'] = $this->login->get_settings($this->user_id);


        // var_dump($data['settings']['company_name']);

        // $data['settings']['email']);


        // die();


        if ($check_non_gst_email) {

            $estimates_data_group = $this->estimate->get_non_gst_estimates_data_group_by($quote_number, $this->user_id);
        } else {
            $estimates_data_group = $this->estimate->get_estimates_data_group_by($quote_number, $this->user_id);
        }

        $customer_name = $estimates_data_group['fullname'];
        $issue_date = date('d-m-Y', strtotime($estimates_data_group['date']));
        $expires_date = date('d-m-Y', strtotime($estimates_data_group['exp_date']));
        $grand_total = $estimates_data_group['total'];

        $to_email = $this->input->post('to_email');

        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');
        $user_id_send = $this->user_id;
        if ($check_non_gst_email) {
            $url = base_url() . 'Download/download_non_gst_quote/' . $quote_number . '/' . $user_id_send;
        } else {
            $url = base_url() . 'Download/index/' . $quote_number . '/' . $user_id_send;
        }

        // Generate PDF to temporary file
        $pdf_file_path = null;
        try {
            // Get quotation data for PDF generation
            if ($check_non_gst_email) {
                $pdf_data['show_quotation'] = $this->estimate->get_non_gst_estimates_data($quote_number, $user_id_send);
                $pdf_data['estimates_data_group'] = $this->estimate->get_non_gst_estimates_data_group_by($quote_number, $user_id_send);
                $view_name = 'admin/print_non_gst_quote';
            } else {
                $pdf_data['show_quotation'] = $this->estimate->get_estimates_data($quote_number, $user_id_send);
                $pdf_data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($quote_number, $user_id_send);
                $view_name = 'admin/print_igst_quote';
            }
            
            $pdf_data['settings'] = $data['settings'];
            $pdf_data['stamp'] = 'yes';
            
            // Load the view as HTML
            $html = $this->load->view($view_name, $pdf_data, true);
            
            // Generate PDF file
            require_once APPPATH . '../vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
            $mpdf->SetHTMLHeader('<div>' . date("d-m-Y") . " - " . $quote_number . '</div>');
            $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
            $mpdf->SetWatermarkText($data['settings']['company_name']);
            $mpdf->showWatermarkText = true;
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->WriteHTML($html);
            
            // Create temp directory if it doesn't exist
            $upload_path = realpath(APPPATH . '../uploads');
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            // Save PDF to temporary file
            $pdf_filename = str_replace("/", "-", $quote_number) . "_" . time() . ".pdf";
            $pdf_file_path = $upload_path . DIRECTORY_SEPARATOR . $pdf_filename;
            $mpdf->Output($pdf_file_path, "F");
            
        } catch (Exception $e) {
            log_message('error', 'PDF Generation Error: ' . $e->getMessage());
        }

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
            $this->email->cc($set_cc_email);
        }

        $htmlContent11 = '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Quotation</title>
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
     <div class="boxs">
       <div class="shadows1">  
          <center> <img alt="' . $data['settings']['company_name'] . '" src="' . $data['settings']['company_logo'] . '" width="30%"></center>
       
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;"><center>Quotation</center></span><br>
            
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;">' . $quote_number . '</span><br>
                    
                <span style="text-decoration:none;color:#a0a0a5;">for <b>' . $customer_name . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">from <b>' . $data['settings']['company_name'] . '</b></span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;">Please check our quotation. </span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;"><b>Message :</b>' . $message . '</span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;font-size:18px">Grand Total : <b>' . $grand_total . ' INR</b></span>
       <hr>
           <center> <a href="' . $url . '" style="background-color:#00929f;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px" target="_blank">
            Download in browser</a><br>
            </center>
            <span style="text-decoration:none;color:#2f2f36;"> Expires on : <b>' . $expires_date . '</b></span>
            <hr>
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If this quotation was sent in error, please contact" <a href="mailto:' . $data['settings']['email'] . '" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">' . $data['settings']['email'] . '</a></span>
         </div>
          <center><span style="text-decoration:none;color:#2f2f36; ">Powered by 
      <img alt="' . $data['settings']['company_name'] . '" src="' . $data['settings']['company_logo'] . '" width="8%" height="8%" style="margin-top:3%;">
       ' . $data['settings']['company_name'] . ' </span></center>
   </div>
     
    </body>
    </html>';

        // Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $data['settings']['company_name'] . ' <' . $data['settings']['email'] . '>' . "\r\n";

        $this->email->message($htmlContent11);
        
        // Attach PDF if it was generated successfully
        if ($pdf_file_path && file_exists($pdf_file_path)) {
            $this->email->attach($pdf_file_path);
        }
        
        //        $this->email->send($to_email, $message, $headers);
        //         echo $this->email->print_debugger();
        //        die();

        if ($this->email->send($to_email, $message, $headers)) {
            $this->session->set_flashdata('SUCCESSMSG', "Email Sent Successfully!!");

            // Clean up temporary PDF file
            if ($pdf_file_path && file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }

            $status = 2;
            $data_status = array('status' => $status);

            if ($check_non_gst_email) {
                $this->estimate->edit_non_gst_quotation_status($data_status, $quote_number, $this->user_id);
                redirect('EstimateController/non_gst_index');
            } else {
                $this->estimate->edit_gst_quotation_status($data_status, $quote_number, $this->user_id);
                redirect('EstimateController/index');
            }
        } else {

            $this->session->set_flashdata('INFOMSG', "Email not Sent Successfully!!");

            // Clean up temporary PDF file on failure
            if ($pdf_file_path && file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }

            if ($check_non_gst_email) {
                redirect('EstimateController/non_gst_index');
            } else {
                redirect('EstimateController/index');
            }
        }
    }

    public function get_customer_email()
    {
        $number = $this->input->post('number');
        // print_r($number);die();
        $result = $this->estimate->get_customer_email($number, $this->user_id);
        echo json_encode($result);
    }


    public function get_customer_mobile()
    {
        $number = $this->input->post('number');
        //  print_r($number);die();
        $result = $this->estimate->get_customer_mobile($number, $this->user_id);
        //  print_r($result);die();
        echo json_encode($result);
    }

    public function delete_non_gst_quotation_by_quote_number()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $quote_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $result = $this->estimate->delete_non_gst_quotation_by_quote_number($quote_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Quotation deleted successfully!!");
            redirect('EstimateController/non_gst_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Quotation not deleted successfully!!");
            redirect('EstimateController/non_gst_index');
        }
    }

    public function convert_to_invoice_non_gst_data()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $invoice_number = $this->input->post('invoice_number');
        $total = 0;
        $payment_method = 0;
        $status = 0;
        $data = $this->estimate->get_convert_invoice_non_gst_data($number, $this->user_id);
        foreach ($data as $key) {
            unset($key->description);
            unset($key->enquiry);
            unset($key->discount);
            unset($key->basic_total);
            unset($key->number);
            //unset($key->date);
            $key->invoice_number = $invoice_number;

            $invoice_date = $key->date;
            $key->invoice_date = $this->format_date_for_db($invoice_date);

            $payment_due_date = $key->date;
            $key->payment_due_date = $this->format_date_for_db($payment_due_date);

            $invoice_subheading = $key->quotation_subheading;
            $key->invoice_subheading = $invoice_subheading;

            $invoice_footer = $key->quotation_footer;
            $key->invoice_footer = $invoice_footer;

            $invoice_memo = $key->quotation_memo;
            $key->invoice_memo = $invoice_memo;

            $total = $key->total;
            $payment_method = $key->payment_method;
            $status = $key->status;

            unset($key->id);
            unset($key->quotation_id);
            unset($key->date);
            unset($key->exp_date);
            unset($key->quotation_subheading);
            unset($key->quotation_footer);
            unset($key->quotation_memo);
            unset($key->number_fk);
            unset($key->total);
            unset($key->customer_id_fk);
            unset($key->payment_method);
            unset($key->status);

            $result1 = $this->estimate->add_non_gst_invoice($key);
            //redirect('EstimateController/index');
            //Update stock when quotation converted to invoice
            $stock = $this->invoice->get_inventory_stock_count($key->product_name, $key->uid);
            $stock_update = array(
                'stock' => $stock['stock'] - $key->quantity,
            );
            $this->db->where('uid', $key->uid);
            $this->db->where('code', $key->product_name);
            $this->db->update('inventory', $stock_update);
        }

        foreach ($data as $key) {

            unset($key->description);
            unset($key->enquiry);
            unset($key->discount);
            unset($key->gst);
            unset($key->basic_total);
            unset($key->invoice_number);
            // unset($key->payment_due_date);

            $key->number_fk = $invoice_number;

            $invoice_memo = $key->customer_id;
            $key->customer_id_fk = $invoice_memo;

            $date = $key->payment_due_date;
            $key->date = $this->format_date_for_db($date);

            $key->total = $total;
            $key->payment_method = $payment_method;
            //$key->status = $status;
            $key->status = 1;

            $key->balance = $total;
            $key->total_before_tax = $total;
            $key->total_gst_amount = 0;

            unset($key->id);
            unset($key->quotation_id);
            unset($key->payment_due_date);
            unset($key->number);
            unset($key->customer_id);
            unset($key->invoice_date);
            unset($key->exp_date);
            unset($key->product_name);
            unset($key->quantity);
            unset($key->hsn_code);
            unset($key->price);
            unset($key->amount);
            unset($key->invoice_subheading);
            unset($key->invoice_footer);
            unset($key->invoice_memo);
        }

        $result = $this->invoice->add_non_gst_invoice_total($key);

        if (($result == TRUE) && ($result1 == TRUE)) {
            $this->session->set_flashdata('SUCCESSMSG', "Quotation converted to invoice successfully!!");
            redirect('InvoiceController/index_non_gst');
        } else {
            $this->session->set_flashdata('INFOMSG', "Quotation not converted to invoice successfully!!");
            redirect('InvoiceController/index_non_gst');
        }
    }

    public function get_item_name()
    {
        $item_name = $this->input->post('item_name');
        $result = $this->estimate->get_item_name($item_name);
        echo json_encode($result);
    }

    public function duplicate_quote()
    {

        $id = $this->input->post('id');


        $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];

        $data['show_quotation'] = $this->estimate->get_estimates_data($number, $this->user_id);
        $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, $this->user_id);

        $data['quotation_id'] = $this->estimate->get_last_quotation_number($this->user_id);


        if (date('m') <= 3) { //Upto March - previous FY
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else { //April onwards - current FY
            $financial_year = date('y') . '-' . (date('y') + 1);
        }
        $str = sprintf("%04d", $data['quotation_id'] + 1);

        $number =  'QUOTE/' . $str . '/' . $financial_year;


        $sgst = '0';

        $igst = '0';

        $sgst1 = '0';
        $cgst1 = '0';
        $igst1 = '0';

        $customer_id = '';

        foreach ($data['show_quotation'] as $key) {



            if ($key->gst_type == 'I') {
                $igst1 = $key->igst;
                $sgst1 = '0';
                $cgst1 = '0';
            }
            if ($key->gst_type == 'S') {
                $igst1 = '0';
                $sgst1 = $key->sgst;
                $cgst1 = $key->cgst;
            }

            $data_quotation = array(
                'customer_id' => $key->customer_id,
                'number' => $number,
                'date' => date("Y-m-d"),
                'exp_date' => $key->exp_date,
                'product_name' => $key->product_name,
                'quantity' => $key->quantity,
                'unit' => $key->unit,
                'hsn_code' => $key->hsn_code,
                'gst' => $key->gst,
                'sgst' => $sgst1,
                'cgst' => $cgst1,
                'igst' => $igst1,
                'gst_type' => $key->gst_type,
                'price' => $key->price,
                'amount' => $key->amount,
                'discount' => $key->discount,
                'description' => $key->description,
                'uid' => $this->user_id,
            );


            $this->db->insert('quotation', $data_quotation);
        }




        $basic_total = $data['estimates_data_group']['basic_total'];
        $total_quotation_amount = $data['estimates_data_group']['total'];
        //$customer_id = $data['estimates_data_group']['customer_id_fk'];
        $status = $data['estimates_data_group']['status'];
        $enquiry = $data['estimates_data_group']['enquiry'];
        $quotation_subheading = $data['estimates_data_group']['quotation_subheading'];
        $quotation_footer = $data['estimates_data_group']['quotation_footer'];
        $quotation_memo = $data['estimates_data_group']['quotation_memo'];
        $terms_and_conditions = $data['estimates_data_group']['terms_and_conditions'];
        $payment_terms = $data['estimates_data_group']['payment_terms'];
        $process_schedule = $data['estimates_data_group']['process_schedule'];
        $taxes = $data['estimates_data_group']['taxes'];
        $exclusions = $data['estimates_data_group']['exclusions'];






        $data_toatl_amount = array(
            'basic_total' => $basic_total,
            'total' => $total_quotation_amount,
            'customer_id_fk' => $customer_id,
            'number_fk' => $number,
            'status' => $status,
            'enquiry' => $enquiry,
            'uid' => $this->user_id,
            'quotation_subheading' => $quotation_subheading,
            'quotation_footer' => $quotation_footer,
            'quotation_memo' => $quotation_memo,
            'terms_and_conditions' => $terms_and_conditions,
            'payment_terms' => $payment_terms,
            'process_schedule' => $process_schedule,
            'taxes' => $taxes,
            'exclusions' => $exclusions
        );
        $result = $this->estimate->add_total_amount($data_toatl_amount);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Quotation added successfully!!");
            redirect('EstimateController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Quotation not added successfully!!");
            redirect('EstimateController/index');
        }
    }


    public function convert_to_sales_order()
    {
        $id = $this->uri->segment(3);
        $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];
        $salesorder_number = $this->input->post('salesorder_number');

        $total = 0;
        $data = $this->estimate->get_convert_invoice_data($number, $this->user_id);

        $project_code = '';
        $client_code = '';
        $project_system = '';

        if (!empty($data)) {
            $proj_code_val = isset($data[0]->project_code) ? trim((string)$data[0]->project_code) : '';
            $proj_code_val = trim($proj_code_val, " \t\n\r\0\x0B");
            $project = null;
            if ($proj_code_val !== '') {
                $project = $this->db->where('project_code', $proj_code_val)->get('project')->row_array();
            }

            if ($project) {
                $project_code = $proj_code_val;
                $project_system = isset($project['system']) ? $project['system'] : '';
            }

            $customer_id = isset($data[0]->customer_id) ? $data[0]->customer_id : 0;
            $customer = $this->db->where('customer_id', $customer_id)->get('customer')->row_array();

            // Settings: company prefix + financial year
            $settings       = $this->login->get_settings($this->user_id);
            $company_name   = $settings['company_name'] ?? 'UWS';
            $words          = preg_split('/[\s\-]+/', trim($company_name));
            $company_prefix = strtoupper(substr($words[0] ?? 'UWS', 0, 5));

            $month      = (int)date('m');
            $fy_start   = ($month <= 3) ? ((int)date('y') - 1) : (int)date('y');
            $fy_end     = $fy_start + 1;
            $financial_year = sprintf('%02d%02d', $fy_start, $fy_end); // e.g. "2526"

            // Get salesorder count in the current financial year to replicate $salesorder_id logic
            $salesorder_id = $this->salesorder->get_last_salesorder_number($this->user_id);

            // Find max sequential number from existing numbers in salesorder_total
            $this->db->select('number_fk, oc_number');
            $this->db->from('salesorder_total');
            $query_so = $this->db->get();
            $rows_so  = $query_so->result();

            $max_seq = 0;
            foreach ($rows_so as $row) {
                $val = !empty($row->oc_number) ? $row->oc_number : $row->number_fk;
                if (!empty($val) && preg_match('/-OC-(\d+)$/i', $val, $m)) {
                    $seq = (int)$m[1];
                    if ($seq > $max_seq) $max_seq = $seq;
                }
            }
            $next_seq = max($salesorder_id + 1, $max_seq + 1);

            // Helper: extract initials
            $getInitials = function($clean_str) {
                if (empty($clean_str)) return '';
                $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', $clean_str);
                $words = preg_split('/\s+/', trim($clean));
                $initials = '';
                foreach ($words as $w) {
                    if (!empty($w)) {
                        $initials .= substr($w, 0, 1);
                    }
                }
                return strtoupper($initials);
            };

            // Calculate client code
            $client_code = '';
            if ($customer && !empty($customer['c_code'])) {
                $client_code = strtoupper(trim($customer['c_code']));
            }
            if (empty($client_code) && $customer) {
                $client_code = $getInitials($customer['company_name']);
            }
            if (empty($client_code) && $project && !empty($project['organisation_name'])) {
                $client_code = $getInitials($project['organisation_name']);
            }
            if (empty($client_code)) {
                $client_code = 'XXX';
            }

            // Calculate system code
            $system_name = $project['system'] ?? '';
            $system_code = !empty($system_name) ? $getInitials($system_name) : 'XX';

            // Build project-based Sales Order number
            $salesorder_number = $company_prefix . '-' . $financial_year
                               . '-' . ($system_code ?: 'XX')
                               . '-' . ($client_code ?: 'XXX')
                               . '-OC-' . $next_seq;
        }

        // Based on your SalesOrderController, the salesorder table has these fields:
        // customer_id, number, product_name, quantity, hsn_code, gst, sgst, cgst, 
        // igst, gst_type, price, amount, discount, description, uid

        foreach ($data as $key) {
            // Prepare data for salesorder table (line items)
            $salesorder_data = array(
                'customer_id' => isset($key->customer_id) ? $key->customer_id : 0,
                'number' => $salesorder_number,
                'product_name' => isset($key->product_name) ? $key->product_name : '',
                'quantity' => isset($key->quantity) ? $key->quantity : 0,
                'unit' => isset($key->unit) ? $key->unit : '',
                'hsn_code' => isset($key->hsn_code) ? $key->hsn_code : '',
                'gst' => isset($key->gst) ? $key->gst : 0,
                'sgst' => isset($key->sgst) ? $key->sgst : 0,
                'cgst' => isset($key->cgst) ? $key->cgst : 0,
                'igst' => isset($key->igst) ? $key->igst : 0,
                'gst_type' => isset($key->gst_type) ? $key->gst_type : '',
                'price' => isset($key->price) ? $key->price : 0,
                'amount' => isset($key->amount) ? $key->amount : 0,
                'discount' => isset($key->discount) ? $key->discount : 0,
                'description' => isset($key->description) ? $key->description : '',
                'uid' => $this->user_id
            );

            $result1 = $this->estimate->add_salesorder($salesorder_data);
            $total = isset($key->total) ? $key->total : 0;
        }

        // Prepare data for salesorder_total table
        // Based on your SalesOrderController, salesorder_total has these fields:
        // basic_total, total, customer_id_fk, number_fk, status, enquiry, uid,
        // salesorder_subheading, salesorder_footer, salesorder_memo, date, exp_date,
        // terms_and_conditions, payment_terms, transportation, pay_terms, installation,
        // process_schedule, taxes, exclusions, project_code, po_number, customer_code

        $salesorder_total_data = array(
            'basic_total' => isset($data[0]->basic_total) ? $data[0]->basic_total : 0,
            'total' => isset($data[0]->total) ? $data[0]->total : 0,
            'customer_id_fk' => isset($data[0]->customer_id) ? $data[0]->customer_id : 0,
            'number_fk' => $salesorder_number,
            'status' => 1, // Default status (Draft)
            'enquiry' => isset($data[0]->enquiry) ? $data[0]->enquiry : '',
            'uid' => $this->user_id,
            'salesorder_subheading' => isset($data[0]->quotation_subheading) ? $data[0]->quotation_subheading : '',
            'salesorder_footer' => isset($data[0]->quotation_footer) ? $data[0]->quotation_footer : '',
            'salesorder_memo' => isset($data[0]->quotation_memo) ? $data[0]->quotation_memo : '',
            'date' => date('Y-m-d'), // Current date
            'exp_date' => isset($data[0]->exp_date) ? $data[0]->exp_date : date('Y-m-d', strtotime('+30 days')),
            'terms_and_conditions' => isset($data[0]->terms_and_conditions) ? $data[0]->terms_and_conditions : '',
            'payment_terms' => isset($data[0]->payment_terms) ? $data[0]->payment_terms : '',
            'transportation' => '',
            'pay_terms' => '',
            'installation' => '',
            'process_schedule' => isset($data[0]->process_schedule) ? $data[0]->process_schedule : '',
            'taxes' => isset($data[0]->taxes) ? $data[0]->taxes : '',
            'exclusions' => isset($data[0]->exclusions) ? $data[0]->exclusions : '',
            'project_code' => $project_code,
            'po_number' => '',
            'customer_code' => $client_code,
            'system' => isset($data[0]->system) && !empty($data[0]->system) ? $data[0]->system : $project_system,
            'location' => isset($data[0]->location) && !empty($data[0]->location) ? $data[0]->location : (isset($project['location']) ? $project['location'] : ''),
            'capacity' => isset($data[0]->capacity) && !empty($data[0]->capacity) ? $data[0]->capacity : (isset($project['capacity']) ? $project['capacity'] : ''),
            'oc_number' => $salesorder_number
        );

        $result = $this->salesorder->add_total_amount($salesorder_total_data);

        if (($result == TRUE) && isset($result1) && $result1 == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Quotation converted to Sales Order successfully!!");
            redirect('SalesOrderController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Quotation not converted to Sales Order successfully!!");
            redirect('SalesController/index');
        }
    }
    public function sgst_to_igst()
{
    $number = $this->input->post('number_fk');

    $this->db->where('number', $number);
    $result = $this->db->get('quotation')->result();

    foreach ($result as $key) {

        if ($key->gst_type == 'S') {
            $igst = $key->sgst * 2;

            $data = [
                'gst_type' => 'I',
                'igst' => $igst,
                'sgst' => 0,
                'cgst' => 0
            ];
        } else {
            $gst = $key->igst / 2;

            $data = [
                'gst_type' => 'S',
                'igst' => 0,
                'sgst' => $gst,
                'cgst' => $gst
            ];
        }

        $this->db->where('quotation_id', $key->quotation_id);
        $this->db->update('quotation', $data);
    }

    $this->session->set_flashdata('SUCCESSMSG', "GST type converted successfully!");
    redirect('EstimateController/index');
}
    public function get_monthyearwise_record()
    {
        $month_year = $this->input->post('month_year');
        $data['quo_count'] = $this->estimate->get_quo_count($this->user_id);
        $draft_status = 1;
        $data['quo_draft_count'] = $this->estimate->get_quo_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['quo_sent_count'] = $this->estimate->get_quo_draft_count($sent_status, $this->user_id);
        //$data['estimates'] = $this->estimate->get_estimates($this->user_id);
        $data['estimates'] = $this->estimate->get_monthyearwise_record($month_year, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/view_estimate', $data);
    }

    public function export_all_quotations() {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("System")
                ->setLastModifiedBy("System")
                ->setTitle("Quotation List")
                ->setSubject("Quotation Details")
                ->setDescription("Export of all Quotation details");

            // Heading
            $sheet->setCellValue('A1', 'QUOTATION LIST REPORT');
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Generated on: ' . date('d-m-Y'));
            $sheet->mergeCells('A2:G2');
            $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $headers = ['Sr.No.', 'Status', 'Date', 'Number', 'Company Name', 'Type', 'Amount'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '3', $header);
                
                // Style table headers: background color #3c8dbc, bold white text, centered
                $style = $sheet->getStyle($column . '3');
                $style->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF3C8DBC');
                $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                
                $column++;
            }

            $estimates = $this->estimate->get_estimates($this->user_id);
            $statusArr = [
                1 => 'Draft',
                2 => 'Sent',
                3 => 'Viewed',
                4 => 'Approved',
                5 => 'Rejected',
                6 => 'Canceled'
            ];

            $rowNum = 4;
            $i = 1;
            foreach ($estimates as $key) {
                $status = isset($key->status) ? $key->status : 0;
                $statusStr = isset($statusArr[$status]) ? $statusArr[$status] : '';
                $dateStr = (!empty($key->date) && $key->date !== '0000-00-00') ? date('d-m-Y', strtotime($key->date)) : '';
                $qNumber = isset($key->number) ? $key->number : '';
                $companyName = isset($key->company_name) ? $key->company_name : '';
                $typeStr = (isset($key->gst_type) && $key->gst_type == 'I') ? 'IGST' : 'CGST/SGST';
                $amountVal = isset($key->total) ? round($key->total) : 0;

                $sheet->setCellValue('A' . $rowNum, $i);
                $sheet->setCellValue('B' . $rowNum, $statusStr);
                $sheet->setCellValue('C' . $rowNum, $dateStr);
                $sheet->setCellValue('D' . $rowNum, $qNumber);
                $sheet->setCellValue('E' . $rowNum, $companyName);
                $sheet->setCellValue('F' . $rowNum, $typeStr);
                $sheet->setCellValue('G' . $rowNum, $amountVal);

                $rowNum++;
                $i++;
            }

            // Auto size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'Quotation_List_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            log_message('error', 'Quotation list Excel export error: ' . $e->getMessage());
            $this->session->set_flashdata('ERRORMSG', "Excel list export failed: " . $e->getMessage());
            redirect('EstimateController/index');
        }
    }
}
