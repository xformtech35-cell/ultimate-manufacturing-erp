<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SalesOrderController extends MY_Controller
{

    protected $user_id;
    protected $has_salesorder_unit_column = null;

    function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('salesorder', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('paymentterm', '', TRUE);


        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
 
        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    private function salesorder_supports_unit_column()
    {
        if ($this->has_salesorder_unit_column === null) {
            $this->has_salesorder_unit_column = $this->db->field_exists('unit', 'salesorder');
        }

        return $this->has_salesorder_unit_column;
    }

    private function sanitize_salesorder_row($row)
    {
        if (!$this->salesorder_supports_unit_column()) {
            unset($row['unit']);
        }

        return $row;
    }
    public function sgst_to_igst()
{
    $salesorder_number = $this->input->post('number_fk');

    $this->db->select('*');
    $this->db->from('salesorder');
    $this->db->where('number', $salesorder_number);
    $query = $this->db->get();
    $result = $query->result();

    foreach ($result as $key) {

        $data = array();
        if ($key->gst_type == 'S') {
            $igst = $key->sgst * 2;
            $data = array('gst_type' => 'I', 'igst' => $igst, 'sgst' => 0, 'cgst' => 0);
            $this->db->where('salesorder_id', $key->salesorder_id);
            $this->db->update('salesorder', $data);
        } else {
            $gst = $key->igst / 2;
            $data = array('gst_type' => 'S', 'igst' => 0, 'sgst' => $gst, 'cgst' => $gst);

            $this->db->where('salesorder_id', $key->salesorder_id);
            $this->db->update('salesorder', $data);
        }
    }

    $this->session->set_flashdata('SUCCESSMSG', "GST type converted successfully!");
    redirect('SalesOrderController/index');
}

private function recalculate_invoice_total($invoice_number)
{
    $this->db->where('invoice_number', $invoice_number);
    $this->db->where('uid', $this->user_id);
    $items = $this->db->get('invoice')->result();
    
    $total_before_tax = 0;
    $total_gst_amount = 0;
    $grand_total = 0;
    
    foreach ($items as $item) {
        $total_before_tax += $item->amount;
        
        if ($item->gst_type == 'I') {
            $total_gst_amount += $item->igst;
            $grand_total += $item->amount + $item->igst;
        } else {
            $total_gst_amount += ($item->sgst + $item->cgst);
            $grand_total += $item->amount + $item->sgst + $item->cgst;
        }
    }
    
    // Round off grand total
    $grand_total = round($grand_total);
    
    $this->db->where('number_fk', $invoice_number);
    $this->db->where('uid', $this->user_id);
    $this->db->update('invoice_total', array(
        'total_before_tax' => $total_before_tax,
        'total_gst_amount' => $total_gst_amount,
        'total' => $grand_total,
        'balance' => $grand_total
    ));
}

private function get_invoice_total_id($invoice_number)
{
    $this->db->select('id');
    $this->db->where('number_fk', $invoice_number);
    $this->db->where('uid', $this->user_id);
    $result = $this->db->get('invoice_total')->row();
    return $result ? $result->id : 0;
}

    public function get_salesorder_data_by_status()
    {
        $status = $this->uri->segment(3);
        $data['salesorders'] = $this->salesorder->get_salesorder_data_by_status($status, $this->user_id);
        //
        $data['salesorder_count'] = $this->salesorder->get_salesorder_count($this->user_id);
        $draft_status = 1;
        $data['salesorder_draft_count'] = $this->salesorder->get_salesorder_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['salesorder_sent_count'] = $this->salesorder->get_salesorder_draft_count($sent_status, $this->user_id);
        // Load system team users for CC selection
        $team_users = $this->db->select('username, user_email')
                                ->from('user')
                                ->where('user_email IS NOT NULL')
                                ->where('user_email !=', '')
                                ->group_by('user_email')
                                ->get()
                                ->result_array();
        $data['team_users'] = $team_users;

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/view_salesorder', $data);
    }

    public function index()
    {
        $str = $this->input->get('str');
        //print_r($str);die();
        if ($str == "All" || $str === null) {
            $data['salesorders'] = $this->salesorder->get_salesorders($this->user_id);
        } else {
            $month_year = date('M-y');
            $data['salesorders'] = $this->salesorder->get_monthyearwise_record($month_year, $this->user_id);
        }
        $data['salesorder_count'] = $this->salesorder->get_salesorder_count($this->user_id);
        $draft_status = 1;
        $data['salesorder_draft_count'] = $this->salesorder->get_salesorder_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['salesorder_sent_count'] = $this->salesorder->get_salesorder_draft_count($sent_status, $this->user_id);

        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['salesorder_id'] = $this->salesorder->get_last_salesorder_number($this->user_id);
        $data['company_name'] = $this->salesorder->get_company_name($this->user_id);
        $data['result'] = $this->salesorder->get_customer($this->user_id);
        // Load system team users for CC selection
        $data['team_users'] = $this->db->select('username, user_email')
                                       ->from('user')
                                       ->where('user_email IS NOT NULL')
                                       ->where('user_email !=', '')
                                       ->group_by('user_email')
                                       ->get()
                                       ->result_array();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/view_salesorder', $data);
    }

    public function create_salesorder()
    {

        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['salesorder_id'] = $this->salesorder->get_last_salesorder_number($this->user_id);
        $data['non_gst_salesorder_id'] = $this->salesorder->get_last_non_gst_salesorder_number($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['result'] = $this->salesorder->get_customer($this->user_id);
        $data['company_name'] = $this->salesorder->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/create_salesorder', $data);
    }

    public function create_igst_salesorder()
    {
        $data['paymentterm_result'] = $this->paymentterm->get_paymentterm($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);


        $data['salesorder_id'] = $this->salesorder->get_last_salesorder_number($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['result'] = $this->salesorder->get_customer($this->user_id);
        $data['company_name'] = $this->salesorder->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/create_igst_salesorder', $data);
    }

    public function create_gst_salesorder()
    {
        $data['paymentterm_result'] = $this->paymentterm->get_paymentterm($this->user_id);
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['salesorder_id'] = $this->salesorder->get_last_salesorder_number($this->user_id);

        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['result'] = $this->salesorder->get_customer($this->user_id);
        $data['company_name'] = $this->salesorder->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/create_gst_salesorder', $data);
    }
public function get_salesorders_for_joborder($uid) {
        $this->db->select('st.id, st.number_fk, st.date, st.project_code, st.customer_code,
                          st.system, st.location, st.capacity, st.oc_number, st.project_qty,
                          c.company_name');
        $this->db->from('salesorder_total st');
        $this->db->join('customer c', 'c.customer_id = st.customer_id_fk', 'left');
        $this->db->where('st.uid', $uid);
        $this->db->order_by('st.id', 'DESC');
        
        // Only include approved sales orders (status 4 = Approved)
        $this->db->where('st.status', 4);
        
        // Optional: exclude sales orders that have already been converted to job orders
        // $this->db->where('st.converted_to_joborder', 0);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get sales order items with additional details
     */
    public function get_salesorders_data_with_details($number, $uid) {
        $this->db->select('so.*, inv.item_name, inv.code as product_code');
        $this->db->from('salesorder so');
        $this->db->join('inventory inv', 'inv.code = so.product_name', 'left');
        $this->db->where('so.number', $number);
        $this->db->where('so.uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get sales order group data by number
     * (This might already exist in your model)
     */
    public function get_salesorders_data_group_by($number, $uid) {
        $this->db->select('st.*, c.company_name, c.fullname, c.email, c.mobile, c.gst, c.address');
        $this->db->from('salesorder_total st');
        $this->db->join('customer c', 'c.customer_id = st.customer_id_fk', 'left');
        $this->db->where('st.number_fk', $number);
        $this->db->where('st.uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    /**
     * Get sales orders data (items) by number
     */
    public function get_salesorders_data($number, $uid) {
        $this->db->select('*');
        $this->db->from('salesorder');
        $this->db->where('number', $number);
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }
    public function print_salesorder()
    {

        $id = $this->uri->segment(3);
        $quote_number_id = $this->salesorder->get_salesorder_number_from_salesorder_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];
        $data['show_quotation'] = $this->salesorder->get_salesorders_data($number, $this->user_id);
        //print_r($data['show_salesorder']);die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['estimates_data_group'] = $this->salesorder->get_salesorders_data_group_by($number, $this->user_id);
        //print_r($data['estimates_data_group']);die();
        $this->session->userdata('session_data_head');
        $this->load->view('admin/print', $data);
    }

    public function print_igst_salesorder()
    {

        $id = $this->uri->segment(3);
        $quote_number_id = $this->salesorder->get_salesorder_number_from_salesorder_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];
        $data['show_quotation'] = $this->salesorder->get_salesorders_data($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['estimates_data_group'] = $this->salesorder->get_salesorders_data_group_by($number, $this->user_id);
        //print_r($data['salesorders_data_group']);die();
        $this->session->userdata('session_data_head');
        $this->load->view('admin/print_igst_quote', $data);
    }

    public function show_salesorder()
    {
        $id = $this->uri->segment(3);
        $quote_number_id = $this->salesorder->get_salesorder_number_from_salesorder_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];
        $data['show_salesorder'] = $this->salesorder->get_salesorders_data($number, $this->user_id);
        $data['salesorders_data_group'] = $this->salesorder->get_salesorders_data_group_by($number, $this->user_id);

        // var_dump($data['salesorders_data_group']);
        //      die();

        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/show_salesorder', $data);
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
        $result = $this->salesorder->customer_check($company_name, $this->user_id);

        //sgst and cgst salesorder_gst_check create_gst_salesorder
        $salesorder_gst_check = $this->input->post('redirect_salesorder');

        //non gst salesorder_non_gst_check create_salesorder
        $salesorder_non_gst_check = $this->input->post('redirect_ng_salesorder');
        //igst salesorder_igst_check  create_igst_salesorder
        $salesorder_igst_check = $this->input->post('redirect_igst_salesorder');

        if ($result == FALSE) {
            $this->customer->add_customer($data_customer);
            $this->session->set_flashdata('SUCCESSMSG', "Company added successfully!!");

            if ($salesorder_gst_check) {
                redirect('SalesOrderController/create_gst_salesorder');
            } else if ($salesorder_igst_check) {
                redirect('SalesOrderController/create_igst_salesorder');
            } else {
                redirect('SalesOrderController/create_salesorder');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Company already exist!!");
            if ($salesorder_gst_check) {
                redirect('SalesOrderController/create_gst_salesorder');
            } else if ($salesorder_igst_check) {
                redirect('SalesOrderController/create_igst_salesorder');
            } else {
                redirect('SalesOrderController/create_salesorder');
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

    public function get_salesorder()
    {
        $product_name = $this->input->post('item_name');
        $company_id = $this->input->post('company_id');
        $result1 = $this->salesorder->get_inventory_id_by_code($product_name);
        $result_rate = $this->salesorder->get_customer_wise_rate($result1['inventory_id'], $company_id);
        $result = $this->salesorder->get_salesorder($product_name, $this->user_id);

        if ($result_rate) {
            unset($result['sell_price']);
            $result['sell_price'] = $result_rate['customer_rate'];
        }

        echo json_encode($result);
    }

    public function add_new_salesorder_customer()
    {

        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $customer_pancard_no = $this->input->post('customer_pancard_no');
        $customer_gst_no = $this->input->post('customer_gst_no');
        $customer_email = $this->input->post('customer_email');
        $customer_mobile = $this->input->post('customer_mobile');
        $customer_address = $this->input->post('customer_address');

        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'customer_pancard_no' => $customer_pancard_no, 'customer_gst_no' => $customer_gst_no, 'customer_email' => $customer_email, 'customer_mobile' => $customer_mobile, 'customer_address' => $customer_address);
        $result = $this->salesorder->customer_check($customer_mobile);

        if ($result == FALSE) {
            $this->salesorder->add_customer($data_customer);
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

public function add_salesorder_salesorder()
{
    $session_data_head = $this->session->userdata('session_data_head');
    $customer_id = $this->input->post('customer_id');
    $number = $this->input->post('number');
    $date = $this->input->post('date');
    $expires_date = $this->input->post('expires_date');
    $po = $this->input->post('po');

    $item = $this->input->post('product_name');
    $quantity = $this->input->post('quantity');
    $unit = $this->input->post('unit');
    $hsn = $this->input->post('hsn');
    $gst_per = $this->input->post('gst_per');
    $tag_no = $this->input->post('tag_no');
    $tag_no = is_array($tag_no) ? $tag_no : [];

    // FIX: Check if arrays exist and are not null
    $item_count = (is_array($item) || is_object($item)) ? count((array)$item) : 0;
    
    if ($item_count == 0) {
        $this->session->set_flashdata('INFOMSG', "Please add at least one item to the sales order!");
        redirect('SalesOrderController/index');
    }

    //Chech Sales Order gst type
    $salesorder_non_gst_check = $this->input->post('salesorder_non_gst_check');
    $salesorder_gst_check = $this->input->post('salesorder_gst_check');
    $salesorder_igst_check = $this->input->post('salesorder_igst_check');

    $sgst = '0';
    $igst = '0';
    if ($salesorder_non_gst_check) {
        $sgst = '0';
        $cgst = '0';
        $igst = '0';
    } else if ($salesorder_gst_check) {
        $igst = '1';
        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
        $gst_type = "S";
    } else if ($salesorder_igst_check) {
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

    $salesorder_subheading = $this->input->post('salesorder_subheading') ?? '';
    $salesorder_footer = $this->input->post('salesorder_footer') ?? '';
    $salesorder_memo = $this->input->post('salesorder_memo') ?? '';
    $terms_and_conditions = $this->input->post('terms_and_conditions') ?? '';
    $payment_terms = $this->input->post('payment_terms') ?? '';
    $transportation = $this->input->post('transportation') ?? '';
    $installation = $this->input->post('installation') ?? '';
    $pay_terms = $this->input->post('pay_terms') ?? '';

    $taxes = $this->input->post('taxes') ?? '';
    $exclusions = $this->input->post('exclusions') ?? '';
    $description = $this->input->post('description') ?? '';

    $project_code = $this->input->post('project_code') ?? '';

    $po_number = $this->input->post('po_number') ?? '';
    $po_status = $this->input->post('po_status') ?? 'open';
    $customer_code = $this->input->post('customer_code') ?? '';
    //        $part_image = $this->input->post('upload_part_img');

    // Handle file attachment
    $attachment_filename = '';
    if (!empty($_FILES['attachment']['name'])) {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png|txt';
        $config['max_size'] = 5120; // 5MB
        $config['file_name'] = uniqid() . '_' . $_FILES['attachment']['name'];
        
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload('attachment')) {
            $upload_data = $this->upload->data();
            $attachment_filename = $upload_data['file_name'];
        }
    }

    $total_salesorder_amount = $this->input->post('total_salesorder_amount');
    $total_salesorder_amount = round($total_salesorder_amount);
    $status = $this->input->post('status');
    $enquiry = $this->input->post('enquiry');

    // Auto-resolve race conditions / duplicate SO serial numbers for concurrent sessions (checking serial number suffix globally via fast SQL)
    $original_number = $number;
    if (preg_match('/-OC-(\d+)$/i', $number, $m)) {
        $seq_target = (int)$m[1];
        
        // High-speed SQL check if target serial number is already taken anywhere in salesorder_total
        $is_taken = $this->db->query("SELECT 1 FROM {$this->db->dbprefix}salesorder_total WHERE number_fk LIKE " . $this->db->escape('%-OC-' . $seq_target) . " LIMIT 1")->num_rows() > 0;
        
        if ($is_taken) {
            // High-speed SQL query to fetch current max serial number
            $row_max = $this->db->query("SELECT MAX(CAST(SUBSTRING_INDEX(number_fk, '-OC-', -1) AS UNSIGNED)) AS max_seq FROM {$this->db->dbprefix}salesorder_total WHERE number_fk LIKE '%-OC-%'")->row();
            $max_seq = (int)($row_max->max_seq ?? 0);
            $new_seq = max($seq_target, $max_seq) + 1;
            $number = preg_replace('/-OC-\d+$/i', '-OC-' . $new_seq, $number);
        }
    } else {
        $check_exists = $this->db->where('number_fk', $number)->count_all_results('salesorder_total');
        if ($check_exists > 0) {
            $seq = 1;
            do {
                $try_number = $number . '-' . $seq;
                $exists = $this->db->where('number_fk', $try_number)->count_all_results('salesorder_total');
                $seq++;
            } while ($exists > 0);
            $number = $try_number;
        }
    }

    // Use $item_count instead of count($item) in the loops
    $data = array();
    $flag = 0;

    //For Non Gst insert into non_gst_salesorder table..
    if ($salesorder_non_gst_check) {
        for ($i = 0; $i < $item_count; $i++) {
            $is_heading_row = (isset($item[$i]) && $item[$i] === '__HEADING__');
            if ($is_heading_row || (isset($item[$i]) && $item[$i] != '' && isset($quantity[$i]) && $quantity[$i] != '' && isset($hsn[$i]) && $hsn[$i] != '' && isset($price[$i]) && $price[$i] != '' && isset($amount[$i]) && $amount[$i] != '')) {
                $data[] = $this->sanitize_salesorder_row(array(
                    'customer_id' => $customer_id,
                    'number' => $number,
                    'product_name' => $item[$i],
                    'quantity' => $is_heading_row ? 0 : $quantity[$i],
                    'unit' => isset($unit[$i]) ? $unit[$i] : '',
                    'hsn_code' => $is_heading_row ? '' : $hsn[$i],
                    'price' => $is_heading_row ? 0 : $price[$i],
                    'amount' => $is_heading_row ? 0 : $amount[$i],
                    'discount' => isset($discount[$i]) ? $discount[$i] : 0,
                    'description' => isset($description[$i]) ? $description[$i] : '',
                    'tag_no' => isset($tag_no[$i]) ? $tag_no[$i] : '',
                    'uid' => $this->user_id,
                ));
                $flag = 0;
            } else {
                $flag = 1;
            }
        }
    } else {
        for ($i = 0; $i < $item_count; $i++) {
            $is_heading_row = (isset($item[$i]) && $item[$i] === '__HEADING__');
            if ($is_heading_row || (isset($item[$i]) && $item[$i] != '' && isset($quantity[$i]) && $quantity[$i] != '' && isset($hsn[$i]) && $hsn[$i] != '' && isset($price[$i]) && $price[$i] != '' && isset($amount[$i]) && $amount[$i] != '')) {
                if (!$is_heading_row) {
                    if ($sgst == '1') {
                        $igst1 = isset($igst[$i]) ? $igst[$i] : '0';
                        $sgst1 = '0';
                        $cgst1 = '0';
                    }
                    if ($igst == '1') {
                        $igst1 = '0';
                        $sgst1 = isset($sgst[$i]) ? $sgst[$i] : '0';
                        $cgst1 = isset($cgst[$i]) ? $cgst[$i] : '0';
                    }
                } else {
                    $igst1 = 0; $sgst1 = 0; $cgst1 = 0;
                }

                $data[] = $this->sanitize_salesorder_row(array(
                    'customer_id' => $customer_id,
                    'number' => $number,
                    'product_name' => $item[$i],
                    'quantity' => $is_heading_row ? 0 : $quantity[$i],
                    'unit' => isset($unit[$i]) ? $unit[$i] : '',
                    'hsn_code' => $is_heading_row ? '' : $hsn[$i],
                    'gst' => $is_heading_row ? 0 : (isset($gst_per[$i]) ? $gst_per[$i] : 0),
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $gst_type,
                    'price' => $is_heading_row ? 0 : $price[$i],
                    'amount' => $is_heading_row ? 0 : $amount[$i],
                    'discount' => isset($discount[$i]) ? $discount[$i] : 0,
                    'description' => isset($description[$i]) ? $description[$i] : '',
                    'tag_no' => isset($tag_no[$i]) ? $tag_no[$i] : '',
                    'uid' => $this->user_id,
                ));
                $flag = 0;
            } else {
                $flag = 1;
            }
        }
    }
    
    if ($flag == 0 && !empty($data)) {
        $this->db->insert_batch('salesorder', $data);
        $logged_in_uid = $session_data_head['result']['user_id'] ?? $this->user_id;
        $data_toatl_amount = array(
            'basic_total' => $basic_total,
            'total' => $total_salesorder_amount,
            'customer_id_fk' => $customer_id,
            'number_fk' => $number,
            'status' => $status,
            'enquiry' => $enquiry,
            'uid' => $logged_in_uid,
            'salesorder_subheading' => $salesorder_subheading,
            'salesorder_footer' => $salesorder_footer,
            'salesorder_memo' => $salesorder_memo,
            'date' => date("Y-m-d", strtotime($this->input->post('date') ?? '')),
            'exp_date' => date("Y-m-d", strtotime($this->input->post('expires_date') ?? '')),
            'terms_and_conditions' => $terms_and_conditions,
            'payment_terms' => $payment_terms,
            'transportation' => $transportation,
            'pay_terms' => $pay_terms,
            'installation' => $installation,
            'taxes' => $taxes,
            'exclusions' => $exclusions,
            'po_number' => $po_number,
            'po_status' => $po_status,
            'attachment' => $attachment_filename,
            'customer_code' => $customer_code,
            'po_date' => date("Y-m-d", strtotime($this->input->post('po_date') ?? '')),
            'project_code' => $project_code,
            'system' => $this->input->post('system') ?? '',
            'location' => $this->input->post('location') ?? '',
            'capacity' => $this->input->post('capacity') ?? '',
            'project_qty' => $this->input->post('project_qty') ?? '',
            'oc_number' => $number,
        );

        $result = $this->salesorder->add_total_amount($data_toatl_amount);

        if ($result == TRUE) {
            $msg = ($number !== $original_number) 
                ? "Sales Order added successfully as {$number} (Auto-assigned to prevent duplicate with concurrent session)!!" 
                : "Sales Order added successfully!!";
            $this->session->set_flashdata('SUCCESSMSG', $msg);
            if ($salesorder_non_gst_check) {
                redirect('SalesOrderController/non_gst_index');
            }
            redirect('SalesOrderController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Sales Order not added successfully!!");
            if ($salesorder_non_gst_check) {
                redirect('SalesOrderController/non_gst_index');
            }
            redirect('SalesOrderController/index');
        }
    } else {
        $this->session->set_flashdata('INFOMSG', "Please fill all item details correctly!");
        redirect('SalesOrderController/index');
    }
}

   public function convert_to_invoice()
{
    $salesorder_id = $this->uri->segment(3);
    
    // Get sales order details
    $quote_number_id = $this->salesorder->get_salesorder_number_from_salesorder_total($salesorder_id, $this->user_id);
    $number = $quote_number_id['number_fk'];
    $invoice_number = $this->input->post('invoice_number');
    
    // Fetch all line items of this sales order
    // echo "Sales Order Number: " . $number . "<br>";


   // die();
    $items = $this->salesorder->get_convert_invoice_data($number, $this->user_id);
    if (empty($items)) {
        $this->session->set_flashdata('INFOMSG', "No items found to convert.");
        redirect('InvoiceController/index');
    }
    
    // Prepare header data from first item (common for all)

// echo $invoice_number;

//     die();
    $first = $items[0];
    $header_data = [
        'number_fk'                    => $invoice_number,
        'customer_id_fk'               => $first->customer_id_fk ?? $first->customer_id,
        'date'                         => date('Y-m-d', strtotime($first->date)),
        'payment_due_date'             => date('Y-m-d', strtotime($first->exp_date)),
        'total'                        => $first->total,
        'balance'                      => $first->total,
        'paid'                         => 0,
        'payment_method'               => $first->payment_method,
        'status'                       => 1,
        'invoice_subheading'           => $first->salesorder_subheading,
        'invoice_footer'               => $first->salesorder_footer,
        'invoice_memo'                 => $first->salesorder_memo,
        'invoice_terms_and_conditions' => $first->terms_and_conditions,
        'invoice_payment_terms'        => $first->payment_terms,
        'invoice_taxes'                => $first->taxes,
        'invoice_exclusions'           => $first->exclusions,
        'customer_po'                  => $first->po_number,
        'po_date'                      => !empty($first->po_date) && $first->po_date !== '0000-00-00' ? date('Y-m-d', strtotime($first->po_date)) : null,
        'uid'                          => $this->user_id,
        'total_before_tax'             => $first->basic_total ?? 0,
        'total_gst_amount'             => ($first->total ?? 0) - ($first->basic_total ?? 0)
    ];
    
    // Insert invoice header
    $invoice_id = $this->invoice->add_invoice_total($header_data);
    if (!$invoice_id) {
        $this->session->set_flashdata('INFOMSG', "Failed to create invoice header.");
        redirect('InvoiceController/index');
    }
    
    // Insert each line item
    foreach ($items as $item) {
    // var_dump($item);
    // die();
        $line_data = [
            'invoice_number'   => $invoice_number,
            'invoice_date'     => date('Y-m-d', strtotime($first->date)),
            'customer_id'   => $item->customer_id_fk ?? $item->customer_id,
            'product_name'     => $item->product_name,
            'description'      => $item->description,
            'quantity'         => $item->quantity,
            'unit'             => $item->unit,
            'hsn_code'         => $item->hsn_code,
            'gst'              => $item->gst,
            'gst_type'         => $item->gst_type,
            'sgst'             => $item->sgst,
            'cgst'             => $item->cgst,
            'igst'             => $item->igst,
            'price'            => $item->price,
            'amount'           => $item->amount,
            'uid'              => $this->user_id
        ];


        // var_dump($line_data);
        // die();
        $result = $this->invoice->add_invoice($line_data);
        if (!$result) {
            // Rollback? For simplicity, just log error
            log_message('error', 'Failed to insert invoice line item for sales order ' . $salesorder_id);
        }
    }
    
    $this->session->set_flashdata('SUCCESSMSG', "Sales Order converted to invoice successfully!!");
    redirect('InvoiceController/index');
}

    public function delete_salesorder_by_quote_number()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $quote_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        //print_r($quote_number);die();
        $result = $this->salesorder->delete_salesorder_by_quote_number($quote_number, $this->user_id);
        $result1 = $this->salesorder->delete_salesorder_total_by_quote_number($quote_number, $this->user_id);
        if (($result == TRUE) && ($result1 == TRUE)) {
            $this->session->set_flashdata('SUCCESSMSG', "Sales Order deleted successfully!!");
            redirect('SalesOrderController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Sales Order not deleted successfully!!");
            redirect('SalesOrderController/index');
        }
    }

    public function get_settings()
    {
        $result = $this->salesorder->get_settings($this->user_id);
        echo json_encode($result);
    }

    public function edit_salesorder_details()
    {
        $id = $this->uri->segment(3);
        $quote_number_id = $this->salesorder->get_salesorder_number_from_salesorder_total($id, $this->user_id);

        $number = $quote_number_id['number_fk'];

        //        $id = $this->uri->segment(3);
        //        $id1 = $this->uri->segment(4);
        //        $id2 = $this->uri->segment(5);
        //        $id3 = $this->uri->segment(6);
        //        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['paymentterm_result'] = $this->paymentterm->get_paymentterm($this->user_id);
        $data['show_salesorder'] = $this->salesorder->get_salesorders_data($number, $this->user_id);
        $data['salesorders_data_group'] = $this->salesorder->get_salesorders_data_group_by($number, $this->user_id);
        // print_r($data['salesorders_data_group']);die();
        $data['status_result'] = $this->salesorder->get_status($number, $this->user_id);
        $data['enquiry_status'] = $this->salesorder->get_enquiry_status($number, $this->user_id);
        $data['customer_result'] = $this->salesorder->get_company_name($this->user_id);
        $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/edit_salesorder', $data);
    }

    public function get_stock_by_item_name()
    {

        $item_name = $this->input->post('item_name');

        $total_quantity = $this->inventory->get_stock_by_item_name($item_name, $this->user_id);
        echo json_encode($total_quantity);
    }

public function edit_salesorder_salesorder()
{
    $customer_id = $this->input->post('customer_id');
    $salesorder_id = $this->input->post('salesorder_id');
    $number = $this->input->post('number');
    $date = $this->input->post('date');
    $expires_date = $this->input->post('expires_date');
    $po = $this->input->post('po');
    $salesorder_subheading = $this->input->post('salesorder_subheading') ?? '';
    $salesorder_footer = $this->input->post('salesorder_footer') ?? '';
    $salesorder_memo = $this->input->post('salesorder_memo') ?? '';
    $terms_and_conditions = $this->input->post('terms_and_conditions') ?? '';
    $payment_terms = $this->input->post('payment_terms') ?? '';
    $installation = $this->input->post('installation') ?? '';
    $transportation = $this->input->post('transportation') ?? '';
    $pay_terms = $this->input->post('pay_terms') ?? '';
    $taxes = $this->input->post('taxes') ?? '';
    $exclusions = $this->input->post('exclusions') ?? '';
    $description = $this->input->post('description') ?? '';
    $project_code = $this->input->post('project_code') ?? '';
    $po_number = $this->input->post('po_number') ?? '';
    
    // NEW: Get po_status from POST, default to 'open' if not provided
    $po_status = $this->input->post('po_status') ?? 'open';
    $customer_code = $this->input->post('customer_code') ?? '';
    $po_date = $this->input->post('po_date') ?? '';


    // Handle file attachment upload (NEW)
    $attachment_filename = '';
    if (!empty($_FILES['attachment']['name'])) {
        $config['upload_path'] = './uploads/salesorder_attachments/';
        $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png|txt';
        $config['max_size'] = 5120; // 5MB
        $config['file_name'] = uniqid() . '_' . $_FILES['attachment']['name'];
        
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload('attachment')) {
            $upload_data = $this->upload->data();
            $attachment_filename = $upload_data['file_name'];
        }
    } else {
        // Keep existing attachment if no new file uploaded
        $existing = $this->salesorder->get_salesorders_data_group_by($number, $this->user_id);
        $attachment_filename = $existing['attachment'] ?? '';
    }

    $item = $this->input->post('product_name');
    $quantity = $this->input->post('quantity');
    $unit = $this->input->post('unit');
    $hsn = $this->input->post('hsn');
    $gst_per = $this->input->post('gst_per');
    $tag_no = $this->input->post('tag_no');
    $tag_no = is_array($tag_no) ? $tag_no : [];

    $salesorder_non_gst_check = $this->input->post('non_gst');
    $salesorder_gst_check = $this->input->post('gst');
    $salesorder_igst_check = $this->input->post('igst_edit_hide_show');

    $sgst = '0';
    $igst = '0';
    if ($salesorder_non_gst_check) {
        $sgst = '0';
        $cgst = '0';
        $igst = '0';
    } else if ($salesorder_gst_check) {
        $igst = '1';
        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
        $gst_type = "S";
    } else if ($salesorder_igst_check) {
        $igst = $this->input->post('igst');
        $sgst = '1';
        $gst_type = "I";
    }

    $price = $this->input->post('price');
    $amount = $this->input->post('amount');
    $gst_amount = $this->input->post('gst_amount');
    $discount = $this->input->post('discount');

    $basic_total = $this->input->post('basic_total');
    $total_salesorder_amount = $this->input->post('total_salesorder_amount');
    $total_salesorder_amount = round($total_salesorder_amount);
    $status = $this->input->post('status');
    $enquiry = $this->input->post('enquiry');

    $item_count = count($item);
    $data = array();

    for ($i = 0; $i < $item_count; $i++) {
        $is_heading_row = (isset($item[$i]) && $item[$i] === '__HEADING__');
        if ($is_heading_row || ($item[$i] != '' && $quantity[$i] != '' && $hsn[$i] != '' && $price[$i] != '' && $amount[$i] != '')) {
            if (!$is_heading_row) {
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
            } else {
                $igst1 = 0; $sgst1 = 0; $cgst1 = 0;
            }
            if ($salesorder_id[$i]) {
                $data = $this->sanitize_salesorder_row(array(
                    'customer_id' => $customer_id,
                    'number' => $number,
                    'product_name' => $item[$i],
                    'quantity' => $is_heading_row ? 0 : $quantity[$i],
                    'unit' => $is_heading_row ? '' : $unit[$i],
                    'hsn_code' => $is_heading_row ? '' : $hsn[$i],
                    'gst' => $is_heading_row ? 0 : $gst_per[$i],
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $gst_type ?? '',
                    'price' => $is_heading_row ? 0 : $price[$i],
                    'amount' => $is_heading_row ? 0 : $amount[$i],
                    'discount' => $is_heading_row ? 0 : $discount[$i],
                    'description' => $description[$i],
                    'tag_no' => isset($tag_no[$i]) ? $tag_no[$i] : '',
                ));
                $this->db->where('uid', $this->user_id);
                $this->db->where('number', $number);
                $this->db->where('salesorder_id', $salesorder_id[$i]);
                $this->db->update('salesorder', $data);
            } else {
                if (!$is_heading_row) {
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
                } else {
                    $igst1 = 0; $sgst1 = 0; $cgst1 = 0;
                }
                $data_insert = $this->sanitize_salesorder_row(array(
                    'customer_id' => $customer_id,
                    'number' => $number,
                    'product_name' => $item[$i],
                    'quantity' => $is_heading_row ? 0 : $quantity[$i],
                    'unit' => $is_heading_row ? '' : $unit[$i],
                    'hsn_code' => $is_heading_row ? '' : $hsn[$i],
                    'gst' => $is_heading_row ? 0 : $gst_per[$i],
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $gst_type ?? '',
                    'price' => $is_heading_row ? 0 : $price[$i],
                    'amount' => $is_heading_row ? 0 : $amount[$i],
                    'discount' => $is_heading_row ? 0 : $discount[$i],
                    'description' => $description[$i],
                    'tag_no' => isset($tag_no[$i]) ? $tag_no[$i] : '',
                    'uid' => $this->user_id,
                ));
                $this->db->insert('salesorder', $data_insert);
            }
        }
    }

    $app_uid = '';
    if (in_array($status, [2, 3, 4, 5, 6])) {
        $app_uid = $this->user_id;
    }

    // Updated data array including the new fields
    $data_toatl_amount = array(
        'basic_total' => $basic_total,
        'total' => $total_salesorder_amount,
        'number_fk' => $number,
        'customer_id_fk' => $customer_id,
        'status' => $status,
        'enquiry' => $enquiry,
        'approved_by' => $app_uid,
        'salesorder_subheading' => $salesorder_subheading,
        'salesorder_footer' => $salesorder_footer,
        'date' => date("Y-m-d", strtotime($this->input->post('date') ?? '')),
        'exp_date' => date("Y-m-d", strtotime($this->input->post('expires_date') ?? '')),
        'salesorder_memo' => $salesorder_memo,
        'terms_and_conditions' => $terms_and_conditions,
        'payment_terms' => $payment_terms,
        'installation' => $installation,
        'transportation' => $transportation,
        'pay_terms' => $pay_terms,
        'taxes' => $taxes,
        'exclusions' => $exclusions,
        'po_number' => $po_number,
        'po_date' => date("Y-m-d", strtotime($this->input->post('po_date') ?? '')),
        'po_status' => $po_status,
        'attachment' => $attachment_filename,
        'customer_code' => $customer_code,
        'project_code' => $project_code,
        'system' => $this->input->post('system') ?? '',
        'location' => $this->input->post('location') ?? '',
        'capacity' => $this->input->post('capacity') ?? '',
        'project_qty' => $this->input->post('project_qty') ?? '',
        'oc_number' => $number,
    );

    $result = $this->salesorder->edit_total_amount($data_toatl_amount, $number, $this->user_id);

    if ($result == TRUE) {
        $this->session->set_flashdata('SUCCESSMSG', "Sales Order edited successfully!!");
        if ($salesorder_non_gst_check) {
            redirect('SalesOrderController/non_gst_index');
        }
        redirect('SalesOrderController/index');
    }
    redirect('SalesOrderController/index');
}

    public function delete_item()
    {
        $salesorder_id = $this->input->post('salesorder_id');
        $result = $this->salesorder->delete_item($salesorder_id);
        echo json_encode($result);
    }

    public function print_invoice()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;

        $data['show_salesorder'] = $this->salesorder->get_salesorders_data($number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['salesorders_data_group'] = $this->salesorder->get_salesorders_data_group_by($number, $this->user_id);
        $this->session->userdata('session_data_head');
        $this->load->view('admin/print', $data);
    }

    public function get_ng_salesorder_data_by_status()
    {
        $status = $this->uri->segment(3);
        $data['non_gst_salesorders'] = $this->salesorder->get_ng_salesorder_data_by_status($status, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/view_non_gst_salesorder', $data);
    }

    public function send_salesorder_email()
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

        if ($check_non_gst_email) {

            $salesorders_data_group = $this->salesorder->get_non_gst_salesorders_data_group_by($quote_number, $this->user_id);
        } else {
            $salesorders_data_group = $this->salesorder->get_salesorders_data_group_by($quote_number, $this->user_id);
        }

        $customer_name = $salesorders_data_group['fullname'];
        $issue_date = $salesorders_data_group['date'];
        $expires_date = $salesorders_data_group['exp_date'];
        $grand_total = $salesorders_data_group['total'];

        $to_email = $this->input->post('to_email');

        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');
        $user_id_send = $this->user_id;
        if ($check_non_gst_email) {
            $url = base_url() . 'Download/download_non_gst_quote/' . $quote_number . '/' . $user_id_send;
        } else {
            $url = base_url() . 'Pdf/print_igst_salesorder/' . $quote_number . '/' . $user_id_send;
        }

        // Generate PDF to temporary file
        $pdf_file_path = null;
        try {
            // Get salesorder data for PDF generation
            if ($check_non_gst_email) {
                $pdf_data['show_salesorder'] = $this->salesorder->get_non_gst_salesorders_data($quote_number, $user_id_send);
                $pdf_data['salesorders_data_group'] = $this->salesorder->get_non_gst_salesorders_data_group_by($quote_number, $user_id_send);
                $view_name = 'admin/print_non_gst_salesorder';
            } else {
                $pdf_data['show_salesorder'] = $this->salesorder->get_salesorders_data($quote_number, $user_id_send);
                $pdf_data['salesorders_data_group'] = $this->salesorder->get_salesorders_data_group_by($quote_number, $user_id_send);
                $view_name = 'admin/print_igst_salesorder';
            }
            
            $pdf_data['settings'] = $data['settings'];
            $pdf_data['stamp'] = 'yes';
            
            // Load the view as HTML
            $html = $this->load->view($view_name, $pdf_data, true);
            
            // Generate PDF file
            require_once APPPATH . '../vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
            $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $quote_number . '</div>');
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

        // Collect CC emails (array of checkboxes and custom entries)
        $cc_list = array();
        $cc_emails_post = $this->input->post('cc_emails');
        if (!empty($cc_emails_post) && is_array($cc_emails_post)) {
            foreach ($cc_emails_post as $cc_item) {
                $cc_item = trim($cc_item);
                if (!empty($cc_item) && filter_var($cc_item, FILTER_VALIDATE_EMAIL)) {
                    $cc_list[] = $cc_item;
                }
            }
        }
        // Fallback for single checkbox option
        if ($copy_email && !empty($set_cc_email) && !in_array($set_cc_email, $cc_list)) {
            $cc_list[] = $set_cc_email;
        }

        if (!empty($cc_list)) {
            $this->email->cc(array_unique($cc_list));
        }

        $htmlContent11 = '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Sales Order</title>
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
     <div class="boxs">
       <div class="shadows1">  
          <center> <img alt="' . $set_company_name . '" src="' . $set_company_logo . '" width="30%"></center>
       
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;"><center>Sales Order</center></span><br>
            
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;">' . $quote_number . '</span><br>
                    
                <span style="text-decoration:none;color:#a0a0a5;">for <b>' . $customer_name . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">from <b>' . $set_company_name . '</b></span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;">Please check our salesorder. </span>
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
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If this salesorder was sent in error, please contact" <a href="mailto:contact@xform.in" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">contact@xform.in</a></span>
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
                $this->salesorder->edit_non_gst_salesorder_status($data_status, $quote_number, $this->user_id);
                redirect('SalesOrderController/non_gst_index');
            } else {
                $this->salesorder->edit_gst_salesorder_status($data_status, $quote_number, $this->user_id);
                redirect('SalesOrderController/index');
            }
        } else {

            $this->session->set_flashdata('INFOMSG', "Email not Sent Successfully!!");

            // Clean up temporary PDF file on failure
            if ($pdf_file_path && file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }

            if ($check_non_gst_email) {
                redirect('SalesOrderController/non_gst_index');
            } else {
                redirect('SalesOrderController/index');
            }
        }
    }

    public function update_salesorder_status()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $permissions = $session_data_head['permission'] ?? array();
        $user_role = $session_data_head['result']['role_name'] ?? '';
        $is_admin = (isset($session_data_head['result']['role_name']) && strtolower($session_data_head['result']['role_name']) === 'admin');

        $this->load->model('ApprovalMatrixModel');
        $so_matrix_rules = $this->ApprovalMatrixModel->getApprovers('SO');

        $is_matrix_approver = false;
        if (!empty($so_matrix_rules)) {
            foreach ($so_matrix_rules as $rule) {
                if (strtolower($rule->approver_role) === strtolower($user_role)) {
                    $is_matrix_approver = true;
                    break;
                }
            }
        }

        if (!$is_admin && !in_array('SO_Approval', $permissions) && !$is_matrix_approver) {
            $this->session->set_flashdata('ERRORMSG', 'You do not have permission to change status / approve Sales Orders.');
            redirect('SalesOrderController/index');
        }

        $so_number = $this->input->post('so_number');
        $status = (int)$this->input->post('status');
        $remarks = $this->input->post('remarks');
        $redirect_to = $this->input->post('redirect_to') ?: 'index';
        $data_status = array('status' => $status, 'remarks' => $remarks);

        if (in_array($status, [2, 3, 4, 5, 6])) {
            $data_status['approved_by'] = $this->user_id;
        }

        $status_label = 'updated';
        switch ($status) {
            case 4: $status_label = 'Approved'; break;
            case 5: $status_label = 'Placed on Hold'; break;
            case 6: $status_label = 'Canceled'; break;
            case 2: $status_label = 'Submitted for Approval'; break;
            case 1: $status_label = 'Set to Draft'; break;
        }

        $result = $this->salesorder->edit_gst_salesorder_status($data_status, $so_number, $this->user_id);
        if ($result) {
            $this->session->set_flashdata('SUCCESSMSG', "Sales Order {$so_number} status {$status_label} successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Failed to update status!");
        }
        redirect('SalesOrderController/' . $redirect_to);
    }

    public function so_approval_dashboard()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $permissions = $session_data_head['permission'] ?? array();
        $user_role = $session_data_head['result']['role_name'] ?? '';
        $is_admin = (isset($session_data_head['result']['role_name']) && strtolower($session_data_head['result']['role_name']) === 'admin');

        $this->load->model('ApprovalMatrixModel');
        $so_matrix_rules = $this->ApprovalMatrixModel->getApprovers('SO');

        $is_matrix_approver = false;
        if (!empty($so_matrix_rules)) {
            foreach ($so_matrix_rules as $rule) {
                if (strtolower($rule->approver_role) === strtolower($user_role)) {
                    $is_matrix_approver = true;
                    break;
                }
            }
        }

        if (!$is_admin && !in_array('SO_Approval', $permissions) && !$is_matrix_approver) {
            $this->session->set_flashdata('ERRORMSG', 'You do not have permission to access SO Approval Dashboard.');
            redirect('Home/index');
        }

        $status_filter = $this->input->get('status') ?: 'pending';

        $data['status_filter'] = $status_filter;
        $data['salesorders'] = $this->salesorder->get_pending_salesorders($this->user_id, $status_filter);
        
        $data['count_pending'] = count($this->salesorder->get_pending_salesorders($this->user_id, 'pending'));
        $data['count_approved'] = count($this->salesorder->get_pending_salesorders($this->user_id, 'approved'));
        $data['count_hold_canceled'] = count($this->salesorder->get_pending_salesorders($this->user_id, 'hold_canceled'));
        $data['count_all'] = count($this->salesorder->get_pending_salesorders($this->user_id, 'all'));

        $data['so_matrix_rules'] = $so_matrix_rules;
        $data['user_role'] = $user_role;
        $data['is_admin'] = $is_admin;
        $data['is_matrix_approver'] = $is_matrix_approver;

        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['company_name'] = $this->salesorder->get_company_name($this->user_id);
        $data['result'] = $this->salesorder->get_customer($this->user_id);
        $data['team_users'] = $this->db->select('username, user_email')
                                       ->from('user')
                                       ->where('user_email IS NOT NULL')
                                       ->where('user_email !=', '')
                                       ->group_by('user_email')
                                       ->get()
                                       ->result_array();

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/so_approval_dashboard', $data);
    }

    public function get_customer_email()
    {
        $number = $this->input->post('number');
        $result = $this->salesorder->get_customer_email($number, $this->user_id);
        echo json_encode($result);
    }

    public function delete_non_gst_salesorder_by_quote_number()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $quote_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $result = $this->salesorder->delete_non_gst_salesorder_by_quote_number($quote_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Sales Order deleted successfully!!");
            redirect('SalesOrderController/non_gst_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Sales Order not deleted successfully!!");
            redirect('SalesOrderController/non_gst_index');
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

    // Get sales order items (non-GST)
    $data = $this->salesorder->get_convert_invoice_non_gst_data($number, $this->user_id);
    if (empty($data)) {
        $this->session->set_flashdata('INFOMSG', "No items found to convert.");
        redirect('InvoiceController/index_non_gst');
    }

    // First loop: insert each line item into 'invoice' table
    foreach ($data as $item) {
        // Prepare line item data
        $line_data = [
            'invoice_number'      => $invoice_number,
            'product_name'        => $item->product_name,
            'quantity'            => $item->quantity,
            'hsn_code'            => $item->hsn_code,
            'price'               => $item->price,
            'amount'              => $item->amount,
            'uid'                 => $this->user_id,
            'description'         => $item->description ?? '',
            'date'                => date("Y-m-d", strtotime($item->date)),
            'exp_date'            => date("Y-m-d", strtotime($item->exp_date)),
            'invoice_subheading'  => $item->salesorder_subheading,
            'invoice_footer'      => $item->salesorder_footer,
            'invoice_memo'        => $item->salesorder_memo,
            'payment_due_date'    => date("Y-m-d", strtotime($item->date)),
        ];

        $result1 = $this->salesorder->add_non_gst_invoice((object)$line_data); // adjust if method expects object

        // Update stock
        $stock = $this->invoice->get_inventory_stock_count($item->product_name, $this->user_id);
        $stock_update = ['stock' => $stock['stock'] - $item->quantity];
        $this->db->where('uid', $this->user_id);
        $this->db->where('code', $item->product_name);
        $this->db->update('inventory', $stock_update);

        // Capture header values from the first item (or any, they should be same for all)
        $total = $item->total;
        $payment_method = $item->payment_method;
        $status = $item->status;
    }

    // Prepare invoice total header data
    $header_data = [
        'number_fk'                    => $invoice_number,
        'customer_id_fk'               => $data[0]->customer_id,
        'date'                         => date("Y-m-d", strtotime($data[0]->date)),
        'payment_due_date'             => date("Y-m-d", strtotime($data[0]->exp_date)),
        'total'                        => $total,
        'balance'                      => $total,
        'paid'                         => 0,
        'payment_method'               => $payment_method,
        'status'                       => 1, // Active
        'invoice_subheading'           => $data[0]->salesorder_subheading,
        'invoice_footer'               => $data[0]->salesorder_footer,
        'invoice_memo'                 => $data[0]->salesorder_memo,
        'invoice_terms_and_conditions' => $data[0]->terms_and_conditions ?? '',
        'invoice_payment_terms'        => $data[0]->payment_terms ?? '',
        'invoice_taxes'                => $data[0]->taxes ?? '',
        'invoice_exclusions'           => $data[0]->exclusions ?? '',
        'customer_po'                  => $data[0]->po_number ?? '',
        'po_date'                      => !empty($data[0]->po_date) && $data[0]->po_date !== '0000-00-00' ? date('Y-m-d', strtotime($data[0]->po_date)) : null,
        'uid'                          => $this->user_id,
        'total_before_tax'             => $total,
        'total_gst_amount'             => 0
    ];

    $result = $this->invoice->add_invoice_total($header_data);

    if ($result && $result1) {
        $this->session->set_flashdata('SUCCESSMSG', "Sales Order converted to invoice successfully!!");
        redirect('InvoiceController/index_non_gst');
    } else {
        $this->session->set_flashdata('INFOMSG', "Sales Order not converted to invoice successfully!!");
        redirect('InvoiceController/index_non_gst');
    }
}

    public function get_item_name()
    {
        $item_name = $this->input->post('item_name');
        $result = $this->salesorder->get_item_name($item_name);
        echo json_encode($result);
    }

    public function duplicate_quote()
    {

        $id = $this->input->post('id');


        $quote_number_id = $this->salesorder->get_salesorder_number_from_salesorder_total($id, $this->user_id);
        $number = $quote_number_id['number_fk'];

        $data['show_salesorder'] = $this->salesorder->get_salesorders_data($number, $this->user_id);
        $data['salesorders_data_group'] = $this->salesorder->get_salesorders_data_group_by($number, $this->user_id);

        $data['salesorder_id'] = $this->salesorder->get_last_salesorder_number($this->user_id);


        if (date('m') <= 3) { //Upto June 2014-2015
            $financial_year =  (date('y') - 1) . '-' . date('y');
        } else { //After June 2015-2016
            $financial_year =  date('y') . '-' . (date('y') + 1);
        }
        $str = sprintf("%04d", $data['salesorder_id']['COUNT(uid)'] + 1);
        $number = 'QUOTE/' . $str . '/' . $financial_year;


        $sgst = '0';

        $igst = '0';

        $sgst1 = '0';
        $cgst1 = '0';
        $igst1 = '0';

        $customer_id = '';

        foreach ($data['show_salesorder'] as $key) {



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

            $data_salesorder = array(
                'customer_id' => $key->customer_id,
                'number' => $number,
                'date' => date("Y-m-d"),
                'exp_date' => $key->exp_date,
                'product_name' => $key->product_name,
                'quantity' => $key->quantity,
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

            $this->db->insert('salesorder', $data_salesorder);
        }




        $basic_total = $data['salesorders_data_group']['basic_total'];
        $total_salesorder_amount = $data['salesorders_data_group']['total'];
        //$customer_id = $data['salesorders_data_group']['customer_id_fk'];
        $status = $data['salesorders_data_group']['status'];
        $enquiry = $data['salesorders_data_group']['enquiry'];
        $salesorder_subheading = $data['salesorders_data_group']['salesorder_subheading'];
        $salesorder_footer = $data['salesorders_data_group']['salesorder_footer'];
        $salesorder_memo = $data['salesorders_data_group']['salesorder_memo'];
        $terms_and_conditions = $data['salesorders_data_group']['terms_and_conditions'];
        $payment_terms = $data['salesorders_data_group']['payment_terms'];
        $taxes = $data['salesorders_data_group']['taxes'];
        $exclusions = $data['salesorders_data_group']['exclusions'];






        $data_toatl_amount = array(
            'basic_total' => $basic_total,
            'total' => $total_salesorder_amount,
            'customer_id_fk' => $customer_id,
            'number_fk' => $number,
            'status' => $status,
            'enquiry' => $enquiry,
            'uid' => $this->user_id,
            'salesorder_subheading' => $salesorder_subheading,
            'salesorder_footer' => $salesorder_footer,
            'salesorder_memo' => $salesorder_memo,
            'terms_and_conditions' => $terms_and_conditions,
            'payment_terms' => $payment_terms,
            'taxes' => $taxes,
            'exclusions' => $exclusions,
            'system' => $data['salesorders_data_group']['system'] ?? null,
            'location' => $data['salesorders_data_group']['location'] ?? null,
            'capacity' => $data['salesorders_data_group']['capacity'] ?? null,
            'project_qty' => $data['salesorders_data_group']['project_qty'] ?? null,
            'oc_number' => $data['salesorders_data_group']['oc_number'] ?? null,
        );
        $result = $this->salesorder->add_total_amount($data_toatl_amount);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Sales Order added successfully!!");
            redirect('SalesOrderController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Sales Order not added successfully!!");
            redirect('SalesOrderController/index');
        }
    }
    public function get_monthyearwise_record()
    {
        $month_year = $this->input->post('month_year');
        $data['salesorder_count'] = $this->salesorder->get_salesorder_count($this->user_id);
        $draft_status = 1;
        $data['salesorder_draft_count'] = $this->salesorder->get_salesorder_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['salesorder_sent_count'] = $this->salesorder->get_salesorder_draft_count($sent_status, $this->user_id);
        $data['salesorders'] = $this->salesorder->get_monthyearwise_record($month_year, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('salesorder/view_salesorder', $data);
    }

    /**
     * Export single sales order to Excel
     */
    /**
     * Export BOM (Bill of Materials) to Excel in the same format as the provided file
     */
    public function export_bom_excel()
    {
        // Get BOM ID from URL (assuming you have a BOM module)
        $id = $this->uri->segment(3);

        // Validate ID
        if (empty($id)) {
            $this->session->set_flashdata('ERRORMSG', "Invalid BOM ID!!");
            redirect('SalesOrderController/index');
        }

        // Load PhpSpreadsheet
        require_once FCPATH . 'vendor/autoload.php';

        try {
            // Get BOM data - you'll need to create these methods in your model
            // This should fetch all the data shown in your Excel file
            $bom_data = $this->salesorder->get_bom_data($id, $this->user_id);

            if (empty($bom_data)) {
                $this->session->set_flashdata('ERRORMSG', "BOM not found!!");
                redirect('SalesOrderController/index');
            }

            // Create new Spreadsheet object
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // ========== SHEET 1: DOSING SYSTEM ==========
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Dosing System');

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator($this->session->userdata('session_data_head')['result']['username'] ?? 'System User')
                ->setLastModifiedBy('System')
                ->setTitle("Bill Of Material - Dosing System")
                ->setSubject("BOM Details")
                ->setDescription("Bill of Materials generated on " . date('Y-m-d H:i:s'))
                ->setKeywords("bom export excel")
                ->setCategory("BOM Export");

            // Set default font
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

            $row = 1;

            // ========== HEADER SECTION ==========
            $sheet->setCellValue('A' . $row, 'SYSTEM');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $row, 'Dosing System');
            $row++;

            $sheet->setCellValue('A' . $row, 'Location');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $row, 'Hindalco');
            $row++;

            $sheet->setCellValue('A' . $row, 'CLIENT');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $row, 'Praj Industries Ltd (Project Code: W 26004.)');
            $row++;

            $sheet->setCellValue('A' . $row, 'CAPACITY');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $row, '');
            $row++;

            $sheet->setCellValue('A' . $row, 'QTY');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $row, '1 LOT');

            $sheet->setCellValue('D' . $row, 'Revision');
            $sheet->getStyle('D' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('E' . $row, '0');
            $row++;

            $sheet->setCellValue('A' . $row, 'OC No.');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $row, 'UWS-2526-DS-PIL-OC-242');

            $sheet->setCellValue('D' . $row, 'Date');
            $sheet->getStyle('D' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('E' . $row, date('Y-m-d H:i:s', strtotime('2026-01-19')));
            $row += 2;

            // ========== MAIN TABLE HEADER ==========
            $headers = ['SR.NO', 'EQUIPMENT', '', 'QTY', 'UNIT', 'TAG NO.', 'SCOPE', 'STORES REMARK IF MATERIAL IS STOCK Y/N', 'REMARK', ''];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $row, $header);
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $col++;
            }
            $row++;

            // ========== EQUIPMENTS SECTION ==========
            $sheet->setCellValue('A' . $row, 'EQUIPMENTS');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            // ALUM DOSING SYSTEM FOR FLASH MIXER--(U001)
            $sheet->setCellValue('A' . $row, 'ALUM DOSING SYSTEM FOR FLASH MIXER--(U001)');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            // Equipment items for U001
            $equipment_u001 = [
                [
                    'sr_no' => '1',
                    'equipment' => 'Dosing Pump',
                    'description' => "Prominnent make Dosing pump\nRequired Capacity : 45 LPH @ 1 bar\nDosing Pump Model : PSMA 05050 PP\nSelected Capacity : 50 LPH @ 5 Bar,\nMOC Dosing Head : PP,\nPower :415V, 0.37kW, IE3, 3Ph, 50Hz, CGL/ Siemens make Motor",
                    'qty' => '2',
                    'unit' => 'Nos',
                    'tag_no' => 'PU002 A/B',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ],
                [
                    'sr_no' => '',
                    'equipment' => 'Dosing valve / Antisyphone Valve',
                    'description' => 'Dosing valve assembly for Model: PSMA 05050 PP',
                    'qty' => '1',
                    'unit' => 'Nos',
                    'tag_no' => '',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ],
                [
                    'sr_no' => '2',
                    'equipment' => 'Agitator System',
                    'description' => "Agitator System, SS316,\n0.37kW/ 0.5HP / 3 PH/ IE3/ 50Hz/ Non FLP CGL/ Siemens make Motor & Bonfiglioli make Inline Helical type Gearbox.\n(WITHOUT BERRING HOUSING)\nMake : Fluidyeme",
                    'qty' => '2',
                    'unit' => 'Nos',
                    'tag_no' => 'AG002 A/B',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => 'Refer GAD'
                ],
                [
                    'sr_no' => '3',
                    'equipment' => 'Dosing Tank',
                    'description' => "Capacity : 550 Ltr, Cylindrical Vertical-Flat top & Bottom Tank with nozzles as per TDS & GAD, MOC : FRP, Make : Coroseal",
                    'qty' => '2',
                    'unit' => 'Nos',
                    'tag_no' => 'TK004 A/B',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => 'Refer GAD'
                ],
                [
                    'sr_no' => '4',
                    'equipment' => 'PRV',
                    'description' => "Range : 0 to 10 Kg/Cm2, MOC :PP\nEnd Connection : 1/2\" BSP(F)",
                    'qty' => '2',
                    'unit' => 'Nos',
                    'tag_no' => 'PSV-002 A/B',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ],
                [
                    'sr_no' => '5',
                    'equipment' => 'Pulsation Dampner',
                    'description' => "Pulsation Dampner, Capacity : 650 ml, MOC:PP",
                    'qty' => '1',
                    'unit' => 'Nos',
                    'tag_no' => '',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => 'Refer GAD'
                ],
                [
                    'sr_no' => '6',
                    'equipment' => 'FRP Canopy',
                    'description' => "FRP Canopy for pump & agitator motor.\nMake: Arham Composite/ Arun Plast",
                    'qty' => '4',
                    'unit' => 'Nos',
                    'tag_no' => '',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => 'Refer GAD'
                ],
                [
                    'sr_no' => '7',
                    'equipment' => 'Calibration Pot',
                    'description' => "Acrylic tube type Calibration Pot, Capacity : 500 ml,\nEnd connection: 1/2\" BSP(F), Make: UWS",
                    'qty' => '1',
                    'unit' => 'Nos',
                    'tag_no' => '',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ],
                [
                    'sr_no' => '8',
                    'equipment' => 'Cable Tray',
                    'description' => "EPP make FRP cable tray",
                    'qty' => '1',
                    'unit' => 'Set',
                    'tag_no' => '',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ],
                [
                    'sr_no' => '9',
                    'equipment' => 'MSEP Skid',
                    'description' => "MSEP Skid for dosing system mounting arrangement",
                    'qty' => '1',
                    'unit' => 'Nos',
                    'tag_no' => '',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => 'Refere GAD'
                ],
                [
                    'sr_no' => '10',
                    'equipment' => 'Y Strainer',
                    'description' => "Y Strainer, Size : 15NB BSP(F), MOC: PP",
                    'qty' => '2',
                    'unit' => 'Nos',
                    'tag_no' => '',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ]
            ];

            foreach ($equipment_u001 as $item) {
                $col = 'A';
                $sheet->setCellValue($col++ . $row, $item['sr_no']);
                $sheet->setCellValue($col++ . $row, $item['equipment']);
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $item['qty']);
                $sheet->setCellValue($col++ . $row, $item['unit']);
                $sheet->setCellValue($col++ . $row, $item['tag_no']);
                $sheet->setCellValue($col++ . $row, $item['scope']);
                $sheet->setCellValue($col++ . $row, $item['stock']);
                $sheet->setCellValue($col++ . $row, $item['remark']);

                // Add description in column B with wrap text
                $sheet->setCellValue('B' . $row, $item['description']);
                $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);

                // Add SUM formula in column J
                $sheet->setCellValue('J' . $row, '=SUM(A' . $row . ':I' . $row . ')');

                $row++;
            }

            // Add empty row
            $row++;

            // ========== INSTRUMENTS SECTION ==========
            $sheet->setCellValue('A' . $row, 'Instruments');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $instruments_u001 = [
                [
                    'sr_no' => '1',
                    'instrument' => 'Level Gauge',
                    'description' => "Borosilicate tube type Level gauge, Tank Height: 1150mm\nCC Distance : 980 mm,\nModel: TTG1PA2P31121W\nProcess Conncetion : 25NB ANSI 150#,\nMOC of process connection : PP",
                    'qty' => '2',
                    'unit' => 'Nos',
                    'tag_no' => "LG-1803\nLG-1804",
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ],
                [
                    'sr_no' => '2',
                    'instrument' => 'Level Switch',
                    'description' => "Magnetic Float Operated Pivoted Level Switch,\nFloat MOC: PP,\nModel : FGSO-J13EPD1WW\nMake: Pune Techtro, End connection: 50NB ANSI 150#,\nLow level (L1): 1100mm, Mid Level (L2): 990mm High level (L3): 210mm form level switch mounting.",
                    'qty' => '2',
                    'unit' => 'Nos',
                    'tag_no' => "LS-1803\nLS-1804",
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ],
                [
                    'sr_no' => '3',
                    'instrument' => 'Pressure Gauges',
                    'description' => "Diaphragm type Pressure gauge Range : 0 to 10 Bar, 1-1/2\" Flange Bottom connection, 100mm dial, MOC : SS, with Glycerine filled, Wika/Baumer/GIC",
                    'qty' => '2',
                    'unit' => 'Nos',
                    'tag_no' => '',
                    'scope' => 'UWS',
                    'stock' => '',
                    'remark' => ''
                ]
            ];

            foreach ($instruments_u001 as $item) {
                $col = 'A';
                $sheet->setCellValue($col++ . $row, $item['sr_no']);
                $sheet->setCellValue($col++ . $row, $item['instrument']);
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $item['qty']);
                $sheet->setCellValue($col++ . $row, $item['unit']);
                $sheet->setCellValue($col++ . $row, $item['tag_no']);
                $sheet->setCellValue($col++ . $row, $item['scope']);
                $sheet->setCellValue($col++ . $row, $item['stock']);
                $sheet->setCellValue($col++ . $row, $item['remark']);

                // Add description in column B
                $sheet->setCellValue('B' . $row, $item['description']);
                $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);

                // Add SUM formula
                $sheet->setCellValue('J' . $row, '=SUM(A' . $row . ':I' . $row . ')');

                $row++;
            }

            // Add empty row
            $row++;

            // ========== PIPING AND FITTINGS SECTION ==========
            $sheet->setCellValue('A' . $row, 'Astral make Grey CPVC PIPING, FITTINGS');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $piping_u001 = [
                ['sr_no' => '1', 'item' => 'Flange', 'description' => "Astral make Grey CPVC 15NB Vanstone Flange, CPVC,\nASTM D1784 (CELL CLASS 24448-B), ANSI 150#, SCH80,\nASME B16.5,", 'qty' => '4', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '2', 'item' => 'Flange', 'description' => "Astral make Grey CPVC 25NB Vanstone Flange, CPVC,\nASTM D1784 (CELL CLASS 24448-B), ANSI 150#, SCH80,\nASME B16.5,", 'qty' => '8', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '3', 'item' => 'Elbow', 'description' => "Astral make Grey CPVC 15NB Elbow, ASTM D1784\n(CELL CLASS 24448-B), SCH80, Dimensional Standard : ASTM F439", 'qty' => '6', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '4', 'item' => 'Elbow', 'description' => "Astral make Grey CPVC 15NB Elbow, ASTM D1784\n(CELL CLASS 24448-B), SCH80, Dimensional Standard : ASTM F439", 'qty' => '4', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '5', 'item' => 'Elbow', 'description' => "Astral make Grey CPVC 15NB 45Deg Elbow, ASTM D1784\n(CELL CLASS 24448-B), SCH80, Dimensional Standard : ASTM F439", 'qty' => '2', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '6', 'item' => 'Elbow', 'description' => "Astral make Grey CPVC 25NB Elbow, ASTM D1784\n(CELL CLASS 24448-B), SCH80, Dimensional Standard : ASTM F439", 'qty' => '2', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '7', 'item' => 'TEE', 'description' => "Astral make Grey CPVC 15NB Equal TEE, ASTM D1784\n(CELL CLASS 24448-B), SCH80, Dimensional Standard : ASTM F439", 'qty' => '7', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '8', 'item' => 'TEE', 'description' => "Astral make Grey CPVC 15NB Equal TEE, ASTM D1784\n(CELL CLASS 24448-B), SCH80, Dimensional Standard : ASTM F439", 'qty' => '3', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '9', 'item' => 'TEE', 'description' => "Astral make Grey CPVC 25NB Equal TEE, ASTM D1784\n(CELL CLASS 24448-B), SCH80, Dimensional Standard : ASTM F439", 'qty' => '2', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '10', 'item' => 'Union', 'description' => "Astral make Grey CPVC 15NB Union, SCH80", 'qty' => '3', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '11', 'item' => 'FTA', 'description' => "Astral make Grey CPVC 15 NB FTA as per Pump Suction & Discharge Valve, SCH80.", 'qty' => '4', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '12', 'item' => 'FTA', 'description' => "Astral make Grey CPVC 25 NB FTA as per Pump Suction & Discharge Valve, SCH80.", 'qty' => '2', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '13', 'item' => 'MTA', 'description' => "Astral make Grey CPVC 15NB MTA, SCH80.", 'qty' => '4', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '14', 'item' => 'Pipe', 'description' => "Astral make Grey CPVC 15NB Pipe, ASTM D1784\n(CELL CLASS 24448-B), Dimensional Standard : ASTM F441.", 'qty' => '5', 'unit' => 'Mtr', 'remark' => ''],
                ['sr_no' => '15', 'item' => 'Pipe', 'description' => "Astral make Grey CPVC 25NB Pipe, ASTM D1784\n(CELL CLASS 24448-B), Dimensional Standard : ASTM F441.", 'qty' => '2', 'unit' => 'Mtr', 'remark' => ''],
                ['sr_no' => '16', 'item' => 'Ball Valve', 'description' => "Astral make Grey CPVC 25NB Solvented type Ball Valve, SCH80.", 'qty' => '6', 'unit' => 'Nos', 'remark' => 'For LG Isolation & Drain'],
                ['sr_no' => '17', 'item' => 'Ball Valve', 'description' => "Astral make Grey CPVC 15NB Solvented type Ball Valve, SCH80.", 'qty' => '9', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '18', 'item' => 'Ball Valve', 'description' => "Astral make Grey CPVC 15NB Solvented type Ball Valve, SCH80.", 'qty' => '4', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '19', 'item' => 'NRV', 'description' => "Gray CPVC NRV, Size: 1/2\" union type", 'qty' => '2', 'unit' => 'Nos', 'remark' => '']
            ];

            foreach ($piping_u001 as $item) {
                $col = 'A';
                $sheet->setCellValue($col++ . $row, $item['sr_no']);
                $sheet->setCellValue($col++ . $row, $item['item']);
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $item['qty']);
                $sheet->setCellValue($col++ . $row, $item['unit']);
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, 'UWS');
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $item['remark']);

                // Add description in column B
                $sheet->setCellValue('B' . $row, $item['description']);
                $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);

                // Add SUM formula
                $sheet->setCellValue('J' . $row, '=SUM(A' . $row . ':I' . $row . ')');

                $row++;
            }

            // Add empty row
            $row++;

            // ========== MISCELLANEOUS SECTION ==========
            $sheet->setCellValue('A' . $row, 'MESCELLANEOUS');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $misc_u001 = [
                ['sr_no' => '1', 'item' => 'Anchor Fastener', 'description' => "M 10 X 75, MOC- Green Passivated.", 'qty' => '', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '2', 'item' => 'N+B+2W', 'description' => "Hardware for mating flanges, MOC : FULL THREADED, GREEN\nPASSIVATED (GP), Material Grade: IS 1363 PART 1.", 'qty' => '', 'unit' => 'Nos', 'remark' => ''],
                ['sr_no' => '3', 'item' => 'Gasket', 'description' => "NACF Gasket for mating flanges", 'qty' => '', 'unit' => 'Nos', 'remark' => '']
            ];

            foreach ($misc_u001 as $item) {
                $col = 'A';
                $sheet->setCellValue($col++ . $row, $item['sr_no']);
                $sheet->setCellValue($col++ . $row, $item['item']);
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $item['qty']);
                $sheet->setCellValue($col++ . $row, $item['unit']);
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, 'UWS');
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $item['remark']);

                // Add description in column B
                $sheet->setCellValue('B' . $row, $item['description']);
                $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);

                // Add SUM formula
                $sheet->setCellValue('J' . $row, '=SUM(A' . $row . ':I' . $row . ')');

                $row++;
            }

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(8);   // SR.NO
            $sheet->getColumnDimension('B')->setWidth(50);  // EQUIPMENT/Description
            $sheet->getColumnDimension('C')->setWidth(5);   // Empty column
            $sheet->getColumnDimension('D')->setWidth(8);   // QTY
            $sheet->getColumnDimension('E')->setWidth(8);   // UNIT
            $sheet->getColumnDimension('F')->setWidth(15);  // TAG NO.
            $sheet->getColumnDimension('G')->setWidth(10);  // SCOPE
            $sheet->getColumnDimension('H')->setWidth(20);  // STORES REMARK
            $sheet->getColumnDimension('I')->setWidth(20);  // REMARK
            $sheet->getColumnDimension('J')->setWidth(12);  // SUM column

            // ========== SHEET 2: INSTRUMENT SCHEDULE ==========
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Instrument Schedule');

            $row2 = 1;

            // Header
            $sheet2->mergeCells('D' . $row2 . ':O' . $row2);
            $sheet2->setCellValue('D' . $row2, 'INSTRUMENT SCHEDULE');
            $sheet2->getStyle('D' . $row2)->getFont()->setBold(true)->setSize(14);
            $row2++;

            $sheet2->mergeCells('J' . $row2 . ':O' . $row2);
            $sheet2->setCellValue('J' . $row2, 'Marcuras Water Treatment (I) Pvt.LTD');
            $sheet2->getStyle('J' . $row2)->getFont()->setBold(true);
            $row2 += 2;

            // Revision table
            $sheet2->setCellValue('J' . $row2, 'Rev.');
            $sheet2->setCellValue('K' . $row2, 'Date');
            $sheet2->setCellValue('L' . $row2, 'Prep. By');
            $sheet2->setCellValue('M' . $row2, 'Chkd. By');
            $sheet2->setCellValue('N' . $row2, 'Appd. By');
            $sheet2->setCellValue('O' . $row2, 'Remark');

            // Style revision header
            $sheet2->getStyle('J' . $row2 . ':O' . $row2)->getFont()->setBold(true);
            $sheet2->getStyle('J' . $row2 . ':O' . $row2)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD3D3D3');
            $row2 += 2;

            // Instrumentation Data header
            $sheet2->setCellValue('A' . $row2, 'INSTRUMENTATION DATA');
            $sheet2->getStyle('A' . $row2)->getFont()->setBold(true);
            $row2++;

            $sheet2->setCellValue('A' . $row2, 'DETAILS REQUIRED ON PIPING');
            $sheet2->getStyle('A' . $row2)->getFont()->setBold(true);
            $row2++;

            // Instrument table headers
            $inst_headers = [
                'SR. NO.',
                'TAG NO.',
                'TYPE OF INSTRUMENT',
                'P&ID NO.',
                'LOCATION',
                'CONNECTION TYPE',
                'SIZE IN NB',
                'RANGE/ CTC',
                'DIAL SIZE',
                'STANDARD',
                'MOC',
                '',
                'MODEL',
                '',
                'MAKE'
            ];
            $sub_headers = ['', '', '', '', '', '', '', '', '', '', 'BODY', 'INTERNAL', '', '', ''];

            $col = 'A';
            foreach ($inst_headers as $idx => $header) {
                $sheet2->setCellValue($col . $row2, $header);
                $sheet2->getStyle($col . $row2)->getFont()->setBold(true);
                $sheet2->getStyle($col . $row2)->getAlignment()->setHorizontal('center');
                $sheet2->getStyle($col . $row2)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD3D3D3');
                $col++;
            }
            $row2++;

            $col = 'A';
            foreach ($sub_headers as $sub) {
                $sheet2->setCellValue($col . $row2, $sub);
                if (!empty($sub)) {
                    $sheet2->getStyle($col . $row2)->getFont()->setBold(true);
                    $sheet2->getStyle($col . $row2)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFD3D3D3');
                }
                $col++;
            }
            $row2++;

            // Add sample instrument data (you would fetch this from database)
            $sample_instruments = [
                ['1', 'LG-1803', 'Level Gauge', '', '', 'Flange', '25NB', '0-1150mm', '', '', 'PP', '', 'TTG1PA2P31121W', '', 'Pune Techtro'],
                ['2', 'LS-1803', 'Level Switch', '', '', 'Flange', '50NB', '', '', '', 'PP', '', 'FGSO-J13EPD1WW', '', 'Pune Techtro']
            ];

            foreach ($sample_instruments as $inst) {
                $col = 'A';
                foreach ($inst as $value) {
                    $sheet2->setCellValue($col . $row2, $value);
                    $col++;
                }
                $row2++;
            }

            // Set column widths for Instrument Schedule
            $sheet2->getColumnDimension('A')->setWidth(8);
            $sheet2->getColumnDimension('B')->setWidth(15);
            $sheet2->getColumnDimension('C')->setWidth(20);
            $sheet2->getColumnDimension('D')->setWidth(15);
            $sheet2->getColumnDimension('E')->setWidth(15);
            $sheet2->getColumnDimension('F')->setWidth(15);
            $sheet2->getColumnDimension('G')->setWidth(10);
            $sheet2->getColumnDimension('H')->setWidth(15);
            $sheet2->getColumnDimension('I')->setWidth(10);
            $sheet2->getColumnDimension('J')->setWidth(12);
            $sheet2->getColumnDimension('K')->setWidth(10);
            $sheet2->getColumnDimension('L')->setWidth(10);
            $sheet2->getColumnDimension('M')->setWidth(20);
            $sheet2->getColumnDimension('N')->setWidth(10);
            $sheet2->getColumnDimension('O')->setWidth(15);

            // ========== SHEET 3: VALVE SCHEDULE ==========
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Valve Schedule');

            $row3 = 1;

            // Header
            $sheet3->mergeCells('A' . $row3 . ':P' . $row3);
            $sheet3->setCellValue('A' . $row3, 'VALVE SCHEDULED');
            $sheet3->getStyle('A' . $row3)->getFont()->setBold(true)->setSize(14);
            $row3++;

            $sheet3->mergeCells('A' . $row3 . ':P' . $row3);
            $sheet3->setCellValue('A' . $row3, 'V A L V E    D E T A I L S');
            $sheet3->getStyle('A' . $row3)->getFont()->setBold(true);
            $row3 += 2;

            // Valve table headers
            $valve_headers = [
                'SR. NO.',
                'TAG NO.',
                'SIZE NB',
                'RATING',
                'TYPE',
                'VALVE CONFIGURATION',
                'Make',
                'SERVICE',
                'BODY',
                'DISC / BALL / DIAPH.',
                'SEAT / SEAL',
                'END CONNECTION',
                'P& ID NO.',
                'FLUID',
                'TEMP. 0C Design',
                'REMARK'
            ];

            $col = 'A';
            foreach ($valve_headers as $header) {
                $sheet3->setCellValue($col . $row3, $header);
                $sheet3->getStyle($col . $row3)->getFont()->setBold(true);
                $sheet3->getStyle($col . $row3)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD3D3D3');
                $sheet3->getStyle($col . $row3)->getAlignment()->setHorizontal('center');
                $col++;
            }
            $row3++;

            // Add sample valve data
            $sheet3->setCellValue('A' . $row3, 'D-14002-2-PID4401(MGF SCETION)');

            // Set column widths for Valve Schedule
            $sheet3->getColumnDimension('A')->setWidth(25);
            $sheet3->getColumnDimension('B')->setWidth(15);
            $sheet3->getColumnDimension('C')->setWidth(10);
            $sheet3->getColumnDimension('D')->setWidth(10);
            $sheet3->getColumnDimension('E')->setWidth(15);
            $sheet3->getColumnDimension('F')->setWidth(20);
            $sheet3->getColumnDimension('G')->setWidth(15);
            $sheet3->getColumnDimension('H')->setWidth(20);
            $sheet3->getColumnDimension('I')->setWidth(15);
            $sheet3->getColumnDimension('J')->setWidth(20);
            $sheet3->getColumnDimension('K')->setWidth(15);
            $sheet3->getColumnDimension('L')->setWidth(15);
            $sheet3->getColumnDimension('M')->setWidth(15);
            $sheet3->getColumnDimension('N')->setWidth(15);
            $sheet3->getColumnDimension('O')->setWidth(15);
            $sheet3->getColumnDimension('P')->setWidth(20);

            // Set active sheet to first sheet
            $spreadsheet->setActiveSheetIndex(0);

            // Set filename
            $filename = 'OC_242_BOM_Praj_Industries_Dosing_System_Hindalco_' . date('Ymd_His') . '.xlsx';

            // Clear output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            // Create writer and save to output
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            log_message('error', 'PhpSpreadsheet error in export_bom_excel: ' . $e->getMessage());
            $this->session->set_flashdata('ERRORMSG', "BOM export failed: " . $e->getMessage());
            redirect('SalesOrderController/index');
        } catch (Exception $e) {
            log_message('error', 'General error in export_bom_excel: ' . $e->getMessage());
            $this->session->set_flashdata('ERRORMSG', "BOM export failed: " . $e->getMessage());
            redirect('SalesOrderController/index');
        }
    }

// Add these methods to your Salesorder_model.php

    /**
     * Get BOM data by ID
     */
    public function get_bom_data($id, $user_id)
    {
        // This method should fetch all BOM data including:
        // - System details
        // - Equipment lists for each system
        // - Instruments
        // - Piping and fittings
        // - Miscellaneous items
        // - Commissioning spares

        // You'll need to implement this based on your database structure
        // Return an array with all BOM data

        $data = [
            'header' => $this->get_bom_header($id, $user_id),
            'equipments' => $this->get_bom_equipments($id, $user_id),
            'instruments' => $this->get_bom_instruments($id, $user_id),
            'piping' => $this->get_bom_piping($id, $user_id),
            'miscellaneous' => $this->get_bom_miscellaneous($id, $user_id)
        ];

        return $data;
    }

    /**
     * Get BOM header
     */
    public function get_bom_header($id, $user_id)
    {
        $this->db->select('*')
            ->from('bom_header')  // Adjust table name as per your database
            ->where('id', $id)
            ->where('uid', $user_id);

        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get BOM equipments
     */
    public function get_bom_equipments($id, $user_id)
    {
        $this->db->select('*')
            ->from('bom_equipments')  // Adjust table name
            ->where('bom_id', $id)
            ->where('uid', $user_id)
            ->order_by('sr_no', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get BOM instruments
     */
    public function get_bom_instruments($id, $user_id)
    {
        $this->db->select('*')
            ->from('bom_instruments')  // Adjust table name
            ->where('bom_id', $id)
            ->where('uid', $user_id)
            ->order_by('sr_no', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get BOM piping and fittings
     */
    public function get_bom_piping($id, $user_id)
    {
        $this->db->select('*')
            ->from('bom_piping')  // Adjust table name
            ->where('bom_id', $id)
            ->where('uid', $user_id)
            ->order_by('sr_no', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get BOM miscellaneous items
     */
    public function get_bom_miscellaneous($id, $user_id)
    {
        $this->db->select('*')
            ->from('bom_miscellaneous')  // Adjust table name
            ->where('bom_id', $id)
            ->where('uid', $user_id)
            ->order_by('sr_no', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Export single sales order to Excel (matches print template layout exactly)
     */
    public function export_salesorder_excel()
    {
        try {
            $id = $this->uri->segment(3);
            if (empty($id)) {
                $this->session->set_flashdata('ERRORMSG', "Invalid Sales Order ID!!");
                redirect('SalesOrderController/index');
                return;
            }

            $salesorder_number_data = $this->salesorder->get_salesorder_number_from_salesorder_total($id, $this->user_id);
            if (empty($salesorder_number_data)) {
                $this->session->set_flashdata('ERRORMSG', "Sales Order not found!!");
                redirect('SalesOrderController/index');
                return;
            }

            $number            = $salesorder_number_data['number_fk'];
            $salesorder_data   = $this->salesorder->get_salesorders_data($number, $this->user_id);
            $salesorder_header = $this->salesorder->get_salesorders_data_group_by($number, $this->user_id);
            $settings          = $this->login->get_settings($this->user_id);

            if (empty($salesorder_data) || empty($salesorder_header)) {
                $this->session->set_flashdata('ERRORMSG', "Sales Order not found!!");
                redirect('SalesOrderController/index');
                return;
            }

            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (!file_exists($autoload_path)) {
                throw new Exception("PhpSpreadsheet not found: " . $autoload_path);
            }
            require_once $autoload_path;
            require_once APPPATH . '/third_party/amount_convert.php';

            // ── PhpSpreadsheet setup ─────────────────────────────────────────────
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Sales Order');
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);
            $spreadsheet->getProperties()->setTitle('Sales Order - ' . ($salesorder_header['number_fk'] ?? $number));

            // ── Shortcuts ────────────────────────────────────────────────────────
            $B   = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN;
            $FS  = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;
            $HC  = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
            $HL  = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
            $HR  = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
            $VC  = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
            $VT  = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP;
            $VB  = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM;

            $blackBorder = ['borders' => ['allBorders' => ['borderStyle' => $B, 'color' => ['argb' => 'FF000000']]]];
            $outerBorder = ['borders' => ['outline'    => ['borderStyle' => $B, 'color' => ['argb' => 'FF000000']]]];

            // ── Detect GST type ─────────────────────────────────────────────────
            $gst_type = 'S';
            foreach ($salesorder_data as $item) {
                if (isset($item->product_name) && $item->product_name !== '__HEADING__') {
                    $gst_type = $item->gst_type ?? 'S';
                    break;
                }
            }
            $is_igst = ($gst_type === 'I');

            // ── HTML → plain text helper ─────────────────────────────────────────
            $htmlToLines = function($raw) {
                if (empty($raw)) return [];
                $t = str_replace(['<br>', '<br/>', '<br />', '</p>', '</li>', '</div>', '&nbsp;'],
                                 ["\n","\n","\n","\n","\n","\n",' '], $raw);
                $t = strip_tags($t);
                $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $out = [];
                foreach (explode("\n", $t) as $l) {
                    $l = trim($l, " \t\n\r\0\x0B\xC2\xA0");
                    if ($l !== '') $out[] = $l;
                }
                return $out;
            };
            $htmlToText = function($raw) use ($htmlToLines) {
                return implode("\n", $htmlToLines($raw));
            };

            // ── Column layout ────────────────────────────────────────────────────
            // Columns: A=Sr | B=Description | C=HSN | D=Qty | E=Unit | F=Tax%
            //   SGST: G=SGST | H=CGST | I=Price | J=Amount  => lastCol=J
            //   IGST: G=IGST | H=Price | I=Amount            => lastCol=I
            $sheet->getColumnDimension('A')->setWidth(5);   // Sr.No.
            $sheet->getColumnDimension('B')->setWidth(44);  // Description
            $sheet->getColumnDimension('C')->setWidth(10);  // HSN
            $sheet->getColumnDimension('D')->setWidth(7);   // Qty
            $sheet->getColumnDimension('E')->setWidth(8);   // Unit
            $sheet->getColumnDimension('F')->setWidth(7);   // Tax%
            if ($is_igst) {
                $sheet->getColumnDimension('G')->setWidth(14); // IGST
                $sheet->getColumnDimension('H')->setWidth(14); // Price
                $sheet->getColumnDimension('I')->setWidth(16); // Amount
                $LC = 'I'; $numCols = 9;
            } else {
                $sheet->getColumnDimension('G')->setWidth(13); // SGST
                $sheet->getColumnDimension('H')->setWidth(13); // CGST
                $sheet->getColumnDimension('I')->setWidth(14); // Price
                $sheet->getColumnDimension('J')->setWidth(16); // Amount
                $LC = 'J'; $numCols = 10;
            }

            $row = 1;

            // ════════════════════════════════════════════════════════════════════
            // SECTION 1 — "SALES ORDER" title (mimics <caption>)
            // ════════════════════════════════════════════════════════════════════
            $sheet->mergeCells("A{$row}:{$LC}{$row}");
            $sheet->setCellValue("A{$row}", 'SALES ORDER');
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF003366']],
                'alignment' => ['horizontal' => $HC, 'vertical' => $VC],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;

            // ════════════════════════════════════════════════════════════════════
            // SECTION 2 — Company Header (logo+info left, blank right)
            // Matches <table class="company-header">
            // ════════════════════════════════════════════════════════════════════
            $hdr_start = $row;

            // Left: Company info labels
            $co_fields = [
                ['Company Name:', $settings['company_name'] ?? ''],
                ['GST Number:',   strtoupper($settings['company_gst'] ?? '')],
                ['PAN Number:',   strtoupper($settings['company_pan'] ?? '')],
                ['Mobile:',       $settings['mobile'] ?? ''],
                ['Email:',        $settings['email'] ?? ''],
                ['Address:',      $settings['address'] ?? ''],
            ];

            // Right: blank (logo area placeholder)
            $half = $is_igst ? 'D' : 'E'; // approx halfway
            $rhs  = $is_igst ? 'E' : 'F';

            foreach ($co_fields as $r) {
                $sheet->getRowDimension($row)->setRowHeight(14);
                $sheet->setCellValue("A{$row}", $r[0]);
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $sheet->mergeCells("B{$row}:{$half}{$row}");
                $sheet->setCellValue("B{$row}", $r[1]);
                // Right side label placeholder (company name = large, others blank)
                if ($row === $hdr_start) {
                    $sheet->mergeCells("{$rhs}{$row}:{$LC}{$row}");
                    $sheet->setCellValue("{$rhs}{$row}", $settings['company_name'] ?? '');
                    $sheet->getStyle("{$rhs}{$row}")->getFont()->setBold(true)->setSize(13)->getColor()->setARGB('FF003366');
                    $sheet->getStyle("{$rhs}{$row}")->getAlignment()->setHorizontal($HC)->setVertical($VC);
                } else {
                    $sheet->mergeCells("{$rhs}{$row}:{$LC}{$row}");
                }
                $row++;
            }
            $hdr_end = $row - 1;
            // Style header block
            $sheet->getStyle("A{$hdr_start}:{$LC}{$hdr_end}")->applyFromArray([
                'fill' => ['fillType' => $FS, 'startColor' => ['argb' => 'FFF5F8FF']],
            ]);
            $sheet->getStyle("A{$hdr_start}:{$LC}{$hdr_end}")->applyFromArray($blackBorder);

            // ════════════════════════════════════════════════════════════════════
            // SECTION 3 — Customer Details (left) + Sales Order Details (right)
            // Matches <table class="details-section"> two-column layout
            // ════════════════════════════════════════════════════════════════════
            $det_start = $row;

            // Build left (customer) and right (SO details) field arrays
            $enquiry_map = [1 => 'By Mail', 2 => 'By Verbal', 3 => 'Just Dial', 4 => 'India Mart'];
            $enq = $salesorder_header['enquiry'] ?? '';

            $cust_left = [
                ['Company Name:',  $salesorder_header['company_name'] ?? 'N/A'],
                ['Customer Name:', $salesorder_header['fullname'] ?? 'N/A'],
                ['Customer Code:', $salesorder_header['c_code'] ?: 'None Provided'],
                ['GST Number:',    $salesorder_header['gst'] ?: 'None Provided'],
                ['PAN Number:',    $salesorder_header['pancard'] ?: 'None Provided'],
                ['State Code:',    $salesorder_header['state_code'] ?: 'None Provided'],
                ['Address:',       $salesorder_header['address'] ?: 'None Provided'],
                ['PO Number:',     $salesorder_header['po_number'] ?? 'N/A'],
                ['PO Date:',       (!empty($salesorder_header['po_date']) && $salesorder_header['po_date'] !== '0000-00-00') ? date('d-m-Y', strtotime($salesorder_header['po_date'])) : 'N/A'],
                ['PO Status:',     ucfirst($salesorder_header['po_status'] ?? 'N/A')],
                ['Enquiry:',       $enquiry_map[$enq] ?? 'N/A'],
            ];

            $so_date  = (!empty($salesorder_header['date']) && $salesorder_header['date'] !== '0000-00-00')     ? date('d-m-Y', strtotime($salesorder_header['date'])) : '';
            $exp_date = (!empty($salesorder_header['exp_date']) && $salesorder_header['exp_date'] !== '0000-00-00') ? date('d-m-Y', strtotime($salesorder_header['exp_date'])) : '';
            $so_number = !empty($salesorder_header['number']) ? $salesorder_header['number'] : ($salesorder_header['number_fk'] ?? $number);

            $cust_right = [
                ['SO Number:',      $so_number],
                ['SO Date:',        $so_date],
                ['Delivery On:',    $exp_date],
                ['Project Code:',   $salesorder_header['project_code'] ?? ''],
                ['Created By:',     $salesorder_header['created_by_name'] ?? ''],
                ['Approved By:',    $salesorder_header['approved_by_name'] ?? 'N/A'],
            ];
            if (!empty($salesorder_header['system']))     $cust_right[] = ['System:',    $salesorder_header['system']];
            if (!empty($salesorder_header['location']))   $cust_right[] = ['Location:',  $salesorder_header['location']];
            if (!empty($salesorder_header['capacity']))   $cust_right[] = ['Capacity:',  $salesorder_header['capacity']];
            if (!empty($salesorder_header['project_qty'])) $cust_right[] = ['Project Qty:', $salesorder_header['project_qty']];

            // Section heading row
            $leftMid  = $is_igst ? 'D' : 'E';
            $rightMid = $is_igst ? 'E' : 'F';

            $sheet->mergeCells("A{$row}:{$leftMid}{$row}");
            $sheet->setCellValue("A{$row}", 'CUSTOMER DETAILS');
            $sheet->mergeCells("{$rightMid}{$row}:{$LC}{$row}");
            $sheet->setCellValue("{$rightMid}{$row}", 'SALES ORDER DETAILS');
            $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray([
                'font'  => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF003366']],
                'fill'  => ['fillType' => $FS, 'startColor' => ['argb' => 'FFE0E8F5']],
                'alignment' => ['vertical' => $VC],
            ]);
            $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($blackBorder);
            $sheet->getRowDimension($row)->setRowHeight(14);
            $row++;

            // Two-column detail rows
            $maxDet = max(count($cust_left), count($cust_right));
            for ($di = 0; $di < $maxDet; $di++) {
                $sheet->getRowDimension($row)->setRowHeight(13);
                // Left
                if (isset($cust_left[$di])) {
                    $sheet->setCellValue("A{$row}", $cust_left[$di][0]);
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $sheet->mergeCells("B{$row}:{$leftMid}{$row}");
                    $sheet->setCellValue("B{$row}", $cust_left[$di][1]);
                    $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                }
                // Right
                if (isset($cust_right[$di])) {
                    $sheet->setCellValue("{$rightMid}{$row}", $cust_right[$di][0]);
                    $sheet->getStyle("{$rightMid}{$row}")->getFont()->setBold(true);
                    $nextR = chr(ord($rightMid) + 1);
                    $sheet->mergeCells("{$nextR}{$row}:{$LC}{$row}");
                    $sheet->setCellValue("{$nextR}{$row}", $cust_right[$di][1]);
                }
                $row++;
            }
            $det_end = $row - 1;
            $sheet->getStyle("A{$det_start}:{$LC}{$det_end}")->applyFromArray($blackBorder);
            $sheet->getStyle("A{$det_start}:{$LC}{$det_end}")->getFill()->setFillType($FS)->getStartColor()->setARGB('FFFAFCFF');

            // ════════════════════════════════════════════════════════════════════
            // SECTION 4 — Items Table Header
            // Matches <tr class="items-header"> with grey background
            // ════════════════════════════════════════════════════════════════════
            $tbl_hdr_row = $row;
            if ($is_igst) {
                $colHdrs = ['Sr.No.', 'Description', 'HSN', 'Qty', 'Unit', 'TAX', 'IGST', 'Price', 'Amount'];
            } else {
                $colHdrs = ['Sr.No.', 'Description', 'HSN', 'Qty', 'Unit', 'TAX', 'SGST', 'CGST', 'Price', 'Amount'];
            }
            $alphas = ['A','B','C','D','E','F','G','H','I','J'];
            foreach ($colHdrs as $ci => $ch) {
                $sheet->setCellValue($alphas[$ci] . $row, $ch);
            }
            $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'fill'      => ['fillType' => $FS, 'startColor' => ['argb' => 'FFE0E0E0']], // light grey like print
                'alignment' => ['horizontal' => $HC, 'vertical' => $VC, 'wrapText' => true],
            ]);
            $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($blackBorder);
            $sheet->getRowDimension($row)->setRowHeight(16);
            $tbl_data_row = $row + 1;
            $row++;

            // ════════════════════════════════════════════════════════════════════
            // SECTION 5 — Item Rows
            // ════════════════════════════════════════════════════════════════════
            $sr = 1;
            $total_qty = $total_sgst = $total_cgst = $total_igst = $total_before_tax = 0;

            $rowStyle = [
                'borders'   => ['allBorders' => ['borderStyle' => $B, 'color' => ['argb' => 'FF000000']]],
                'alignment' => ['vertical'   => $VT],
            ];

            foreach ($salesorder_data as $item) {
                // Section heading row (purple/orange like print)
                if (isset($item->product_name) && $item->product_name === '__HEADING__') {
                    $desc    = trim($item->description ?? '');
                    if ($desc === '') { $row++; continue; } // spacer
                    $isMain  = (!isset($item->tag_no) || $item->tag_no === 'MAIN');
                    $bg      = $isMain ? 'FFE6E0ED' : 'FFFDEADA';
                    $fg      = $isMain ? 'FF5A3D8A' : 'FF000000';
                    $display = $isMain ? strtoupper($desc) : $desc;

                    $sheet->mergeCells("A{$row}:{$LC}{$row}");
                    $sheet->setCellValue("A{$row}", $display);
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => $fg]],
                        'fill'      => ['fillType' => $FS, 'startColor' => ['argb' => $bg]],
                        'alignment' => ['horizontal' => $HL, 'vertical' => $VC],
                    ]);
                    $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($blackBorder);
                    $sheet->getRowDimension($row)->setRowHeight(14);
                    $row++;
                    continue;
                }

                // Normal item row
                $qty    = floatval($item->quantity ?? 0);
                $sgst   = floatval($item->sgst ?? 0);
                $cgst   = floatval($item->cgst ?? 0);
                $igst   = floatval($item->igst ?? 0);
                $price  = floatval($item->price ?? 0);
                $amount = floatval($item->amount ?? 0);

                $total_qty         += $qty;
                $total_sgst        += $sgst;
                $total_cgst        += $cgst;
                $total_igst        += $igst;
                $total_before_tax  += $amount;

                // Build description: "ProductCode - ItemName\nDescription"
                $prod = trim($item->product_name ?? '');
                $iname = trim($item->item_name ?? '');
                $descStr = ($prod && $iname && $iname !== $prod) ? "$prod - $iname" : ($prod ?: $iname);
                if (!empty($item->description)) {
                    $cleanDesc = strip_tags(str_replace(
                        ['<br>', '<br/>', '<br />', '</p>', '</li>', '&nbsp;'],
                        ["\n", "\n", "\n", "\n", "\n", ' '],
                        html_entity_decode($item->description, ENT_QUOTES, 'UTF-8')
                    ));
                    $cleanDesc = trim($cleanDesc);
                    if ($cleanDesc) $descStr .= "\n" . $cleanDesc;
                }

                $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($rowStyle);
                $sheet->getRowDimension($row)->setRowHeight(-1); // auto-height

                $sheet->setCellValue("A{$row}", $sr);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HC)->setVertical($VT);

                $sheet->setCellValue("B{$row}", $descStr);
                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true)->setVertical($VT);

                $sheet->setCellValue("C{$row}", $item->hsn_code ?? '-');
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal($HC)->setVertical($VT);

                $sheet->setCellValue("D{$row}", $qty);
                $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal($HC)->setVertical($VT);

                $sheet->setCellValue("E{$row}", $item->unit ?? '-');
                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal($HC)->setVertical($VT);

                $sheet->setCellValue("F{$row}", ($item->gst ?? '0') . '%');
                $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal($HC)->setVertical($VT);

                if ($is_igst) {
                    $sheet->setCellValue("G{$row}", $igst);
                    $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal($HR)->setVertical($VT);
                    $sheet->setCellValue("H{$row}", $price);
                    $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal($HR)->setVertical($VT);
                    $sheet->setCellValue("I{$row}", $amount);
                    $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal($HR)->setVertical($VT);
                } else {
                    $sheet->setCellValue("G{$row}", $sgst);
                    $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal($HR)->setVertical($VT);
                    $sheet->setCellValue("H{$row}", $cgst);
                    $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal($HR)->setVertical($VT);
                    $sheet->setCellValue("I{$row}", $price);
                    $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal($HR)->setVertical($VT);
                    $sheet->setCellValue("J{$row}", $amount);
                    $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal($HR)->setVertical($VT);
                }

                $row++;
                $sr++;
            }

            // ════════════════════════════════════════════════════════════════════
            // SECTION 6 — Totals (matches print template totals)
            // ════════════════════════════════════════════════════════════════════
            $grand_total = $salesorder_header['total'] ?? ($total_before_tax + $total_sgst + $total_cgst + $total_igst);

            $numFmt  = '#,##0.00';
            $totStyle = [
                'font'    => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => $B, 'color' => ['argb' => 'FF000000']]],
            ];
            $grandStyle = [
                'font'  => ['bold' => true, 'size' => 10],
                'fill'  => ['fillType' => $FS, 'startColor' => ['argb' => 'FFF0F0F0']],
                'borders' => ['allBorders' => ['borderStyle' => $B, 'color' => ['argb' => 'FF000000']]],
            ];

            // Total Qty + Total Before Tax
            $lastMinusOne = chr(ord($LC) - 1);
            if ($is_igst) {
                $sheet->mergeCells("A{$row}:C{$row}");
                $sheet->setCellValue("A{$row}", 'Total Qty:');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->setCellValue("D{$row}", $total_qty);
                $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal($HC);
                $sheet->mergeCells("E{$row}:{$lastMinusOne}{$row}");
                $sheet->setCellValue("E{$row}", 'Total Before Tax:');
                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->setCellValue("{$LC}{$row}", $total_before_tax);
                $sheet->getStyle("{$LC}{$row}")->getNumberFormat()->setFormatCode($numFmt);
                $sheet->getStyle("{$LC}{$row}")->getAlignment()->setHorizontal($HR);
            } else {
                $sheet->mergeCells("A{$row}:C{$row}");
                $sheet->setCellValue("A{$row}", 'Total Qty:');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->setCellValue("D{$row}", $total_qty);
                $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal($HC);
                $sheet->mergeCells("E{$row}:{$lastMinusOne}{$row}");
                $sheet->setCellValue("E{$row}", 'Total Before Tax:');
                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->setCellValue("{$LC}{$row}", $total_before_tax);
                $sheet->getStyle("{$LC}{$row}")->getNumberFormat()->setFormatCode($numFmt);
                $sheet->getStyle("{$LC}{$row}")->getAlignment()->setHorizontal($HR);
            }
            $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($totStyle);
            $sheet->getRowDimension($row)->setRowHeight(14);
            $row++;

            // CGST / SGST or IGST row
            if (!$is_igst) {
                $sheet->mergeCells("A{$row}:{$lastMinusOne}{$row}");
                $sheet->setCellValue("A{$row}", 'CGST Amount:');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->setCellValue("{$LC}{$row}", $total_cgst);
                $sheet->getStyle("{$LC}{$row}")->getNumberFormat()->setFormatCode($numFmt);
                $sheet->getStyle("{$LC}{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($totStyle);
                $sheet->getRowDimension($row)->setRowHeight(13);
                $row++;

                $sheet->mergeCells("A{$row}:{$lastMinusOne}{$row}");
                $sheet->setCellValue("A{$row}", 'SGST Amount:');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->setCellValue("{$LC}{$row}", $total_sgst);
                $sheet->getStyle("{$LC}{$row}")->getNumberFormat()->setFormatCode($numFmt);
                $sheet->getStyle("{$LC}{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($totStyle);
                $sheet->getRowDimension($row)->setRowHeight(13);
                $row++;
            } else {
                $sheet->mergeCells("A{$row}:{$lastMinusOne}{$row}");
                $sheet->setCellValue("A{$row}", 'IGST Amount:');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->setCellValue("{$LC}{$row}", $total_igst);
                $sheet->getStyle("{$LC}{$row}")->getNumberFormat()->setFormatCode($numFmt);
                $sheet->getStyle("{$LC}{$row}")->getAlignment()->setHorizontal($HR);
                $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($totStyle);
                $sheet->getRowDimension($row)->setRowHeight(13);
                $row++;
            }

            // Grand Total row (grey bg)
            $sheet->mergeCells("A{$row}:{$lastMinusOne}{$row}");
            $sheet->setCellValue("A{$row}", 'Grand Total (INR):');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HR);
            $sheet->setCellValue("{$LC}{$row}", $grand_total);
            $sheet->getStyle("{$LC}{$row}")->getNumberFormat()->setFormatCode($numFmt);
            $sheet->getStyle("{$LC}{$row}")->getAlignment()->setHorizontal($HR);
            $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($grandStyle);
            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;

            // Amount in Words
            $in_words = function_exists('number_to_word') ? number_to_word($grand_total) . ' Only' : '';
            if ($in_words) {
                $sheet->mergeCells("A{$row}:{$LC}{$row}");
                $sheet->setCellValue("A{$row}", 'Grand Total in Words: ' . strtoupper($in_words));
                $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(8);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HR)->setWrapText(true);
                $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($blackBorder);
                $sheet->getRowDimension($row)->setRowHeight(14);
                $row++;
            }

            // ════════════════════════════════════════════════════════════════════
            // SECTION 7 — Bank Details (left) + Authorised Signatory (right)
            // Matches the bank+signature row in the print template
            // ════════════════════════════════════════════════════════════════════
            $sig_start = $row;
            $bank_text = $settings['invoice_notes'] ?? '';
            $bank_lines = [];
            if (!empty($bank_text)) {
                $bank_text_clean = strip_tags(html_entity_decode($bank_text, ENT_QUOTES, 'UTF-8'));
                foreach (explode("\n", $bank_text_clean) as $bl) {
                    $bl = trim($bl);
                    if ($bl) $bank_lines[] = $bl;
                }
            }
            $bank_content = !empty($bank_lines) ? implode("\n", $bank_lines) : 'No bank details available';

            $bankCol = $is_igst ? 'D' : 'E';
            $sigCol  = $is_igst ? 'E' : 'F';

            $sheet->getRowDimension($row)->setRowHeight(60);

            // Left: Bank Details
            $sheet->mergeCells("A{$row}:{$bankCol}{$row}");
            $sheet->setCellValue("A{$row}", "Bank Details:\n" . $bank_content);
            $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true)->setVertical($VT)->setHorizontal($HL);
            $sheet->getStyle("A{$row}")->getFont()->setSize(8);

            // Right: Authorised Signatory
            $sheet->mergeCells("{$sigCol}{$row}:{$LC}{$row}");
            $sheet->setCellValue("{$sigCol}{$row}", "Authorised Signatory");
            $sheet->getStyle("{$sigCol}{$row}")->getFont()->setBold(true);
            $sheet->getStyle("{$sigCol}{$row}")->getAlignment()->setHorizontal($HC)->setVertical($VB);

            $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($blackBorder);
            $row++;

            // ════════════════════════════════════════════════════════════════════
            // SECTION 8 — Terms, Payment Terms, Process Schedule, Taxes,
            //             Exclusions, Notes (each as its own merged row)
            // ════════════════════════════════════════════════════════════════════
            $sections = [
                'Terms & Conditions:'  => !empty($salesorder_header['terms_and_conditions'])  ? $salesorder_header['terms_and_conditions']  : ($settings['so_terms_and_conditions'] ?? $settings['terms_and_conditions'] ?? ''),
                'Payment Terms:'       => !empty($salesorder_header['payment_terms'])         ? $salesorder_header['payment_terms']         : ($settings['payment_terms'] ?? ''),
                'Process Schedule:'    => $salesorder_header['process_schedule'] ?? '',
                'Taxes:'               => $salesorder_header['taxes'] ?? '',
                'Exclusions:'          => $salesorder_header['exclusions'] ?? '',
                'Note:'                => $salesorder_header['salesorder_memo'] ?? '',
            ];

            foreach ($sections as $label => $content) {
                if (empty(trim(strip_tags($content)))) continue;
                $lines = $htmlToLines($content);
                if (empty($lines)) continue;

                // Label heading row
                $sheet->mergeCells("A{$row}:{$LC}{$row}");
                $sheet->setCellValue("A{$row}", $label);
                $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(9);
                $sheet->getStyle("A{$row}")->getFill()->setFillType($FS)->getStartColor()->setARGB('FFE8EFF8');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HL)->setVertical($VC);
                $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($blackBorder);
                $sheet->getRowDimension($row)->setRowHeight(13);
                $row++;

                // Content lines
                foreach ($lines as $line) {
                    $sheet->mergeCells("A{$row}:{$LC}{$row}");
                    $sheet->setCellValue("A{$row}", '    ' . $line);
                    $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true)->setVertical($VT)->setHorizontal($HL);
                    $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($blackBorder);
                    $sheet->getStyle("A{$row}")->getFont()->setSize(9);
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                    $row++;
                }
            }

            // ════════════════════════════════════════════════════════════════════
            // SECTION 9 — Footer
            // ════════════════════════════════════════════════════════════════════
            $sheet->mergeCells("A{$row}:{$LC}{$row}");
            $footer_text = (!empty($salesorder_header['salesorder_footer']) ? $salesorder_header['salesorder_footer'] . "\n" : '')
                         . 'This is a Computer Generated Sales Order'
                         . "\n" . ($settings['company_name'] ?? '') . ' | ' . ($settings['address'] ?? '');
            $sheet->setCellValue("A{$row}", $footer_text);
            $sheet->getStyle("A{$row}")->getFont()->setSize(8)->getColor()->setARGB('FF444444');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($HC)->setWrapText(true);
            $sheet->getStyle("A{$row}:{$LC}{$row}")->applyFromArray($blackBorder);
            $sheet->getStyle("A{$row}:{$LC}{$row}")->getFill()->setFillType($FS)->getStartColor()->setARGB('FFF5F5F5');
            $sheet->getRowDimension($row)->setRowHeight(-1);

            // ── Freeze the table header row ──────────────────────────────────────
            $sheet->freezePane("A{$tbl_data_row}");

            // ── Page setup — A4 Landscape ────────────────────────────────────────
            $sheet->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
            $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.4)->setRight(0.4);
            $sheet->getHeaderFooter()->setOddHeader('&L&B' . ($settings['company_name'] ?? '') . '&RSales Order');
            $sheet->getHeaderFooter()->setOddFooter('&LPage &P of &N&RThis is a Computer Generated Sales Order');

            // ── File name & download ─────────────────────────────────────────────
            $so_num_clean = str_replace(['/', '\\', ' '], '-', $so_number ?: $number);
            $filename = 'SalesOrder_' . $so_num_clean . '_' . date('Ymd_His') . '.xlsx';

            while (ob_get_level()) { ob_end_clean(); }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Pragma: public');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            log_message('error', 'Excel export error: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            $this->session->set_flashdata('ERRORMSG', "Excel export failed: " . $e->getMessage());
            redirect('SalesOrderController/index');
        }
    }


    public function export_all_salesorders() {
        try {
            // Create new Spreadsheet object
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("System")
                ->setLastModifiedBy("System")
                ->setTitle("Sales Order List")
                ->setSubject("Sales Order Details")
                ->setDescription("Export of all Sales Order details");

            // Heading
            $sheet->setCellValue('A1', 'SALES ORDER LIST REPORT');
            $sheet->mergeCells('A1:J1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Generated on: ' . date('d-m-Y'));
            $sheet->mergeCells('A2:J2');
            $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $headers = ['Sr.No.', 'Status', 'Date', 'Number', 'Customer Name', 'Company Name', 'Type', 'Amount', 'Created By', 'Approved By'];
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

            $salesorders = $this->salesorder->get_salesorders($this->user_id);
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
            foreach ($salesorders as $key) {
                $status = isset($key->status) ? $key->status : 0;
                $statusStr = isset($statusArr[$status]) ? $statusArr[$status] : 'Pending';
                $dateStr = (!empty($key->date) && $key->date !== '0000-00-00') ? date('d-m-Y', strtotime($key->date)) : '';
                $soNumber = isset($key->number) ? $key->number : '';
                $customerName = isset($key->fullname) ? $key->fullname : '';
                $companyName = isset($key->customer_name) ? $key->customer_name : '';
                $gstType = (isset($key->gst_type) && $key->gst_type == 'I') ? 'IGST' : 'GST';
                $amount = isset($key->total) ? $key->total : 0;
                $createdBy = isset($key->created_by_name) && !empty($key->created_by_name) ? $key->created_by_name : 'Admin';
                $approvedBy = isset($key->approved_by_name) && !empty($key->approved_by_name) ? $key->approved_by_name : 'N/A';

                $sheet->setCellValue('A' . $rowNum, $i);
                $sheet->setCellValue('B' . $rowNum, $statusStr);
                $sheet->setCellValue('C' . $rowNum, $dateStr);
                $sheet->setCellValue('D' . $rowNum, $soNumber);
                $sheet->setCellValue('E' . $rowNum, $customerName);
                $sheet->setCellValue('F' . $rowNum, $companyName);
                $sheet->setCellValue('G' . $rowNum, $gstType);
                $sheet->setCellValue('H' . $rowNum, $amount);
                $sheet->setCellValue('I' . $rowNum, $createdBy);
                $sheet->setCellValue('J' . $rowNum, $approvedBy);

                $rowNum++;
                $i++;
            }

            // Auto size columns
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'Sales_Order_List_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            log_message('error', 'Excel list export error: ' . $e->getMessage());
            $this->session->set_flashdata('ERRORMSG', "Excel list export failed: " . $e->getMessage());
            redirect('SalesOrderController/index');
        }
    }

      public function delete_salesorder_item()
    {
        $salesorder_id = $this->input->post('salesorder_id');
        $result = $this->salesorder->delete_salesorder_item($salesorder_id);
        echo json_encode($result);
    }

    public function ajax_get_project_details()
    {
        header('Content-Type: application/json');
        $project_code = $this->input->post('project_code');
        if (empty($project_code)) {
            echo json_encode(array('success' => false, 'message' => 'No project code provided'));
            return;
        }

        $project = $this->db->where('project_code', $project_code)->get('project')->row_array();
        if (!$project) {
            echo json_encode(array('success' => false, 'message' => 'Project not found'));
            return;
        }

        // Find customer whose company_name matches project's organisation_name (exact first, then like)
        $customer = null;
        if (!empty($project['organisation_name'])) {
            $customer = $this->db->where('company_name', $project['organisation_name'])
                                 ->get('customer')
                                 ->row_array();
            if (!$customer) {
                $customer = $this->db->like('company_name', $project['organisation_name'])
                                     ->get('customer')
                                     ->row_array();
            }
        }

        // Settings: company prefix + financial year
        $settings       = $this->login->get_settings($this->user_id);
        $company_name   = $settings['company_name'] ?? 'UWS';
        $words          = preg_split('/[\s\-]+/', trim($company_name));
        $company_prefix = strtoupper(substr($words[0] ?? 'UWS', 0, 5));

        $month      = (int)date('m');
        $fy_start   = ($month <= 3) ? ((int)date('y') - 1) : (int)date('y');
        $fy_end     = $fy_start + 1;
        $financial_year = sprintf('%02d%02d', $fy_start, $fy_end); // e.g. "2526"

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
        $next_seq = $max_seq + 1;

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

        $client_code = $customer ? $getInitials($customer['company_name']) : (!empty($project['organisation_name']) ? $getInitials($project['organisation_name']) : '');
        $system_name = $project['system'] ?? '';
        $system_code = !empty($system_name) ? $getInitials($system_name) : 'XX';

        // Build partial OC number: UWS-2526-[SYS]-PIL-OC-1
        $auto_oc = $company_prefix . '-' . $financial_year
                 . '-' . ($system_code ?: 'XX')
                 . '-' . ($client_code ?: 'XXX')
                 . '-OC-' . $next_seq;

        echo json_encode(array(
            'success'        => true,
            'project'        => $project,
            'customer'       => $customer,
            'company_prefix' => $company_prefix,
            'financial_year' => $financial_year,
            'client_code'    => $client_code,
            'next_seq'       => $next_seq,
            'auto_oc_number' => $auto_oc
        ));
    }

    public function get_next_oc_number() {
        header('Content-Type: application/json');
        
        $settings = $this->login->get_settings($this->user_id);

        $company_name = $settings['company_name'] ?? 'UWS';
        $words = preg_split('/[\s\-]+/', trim($company_name));
        $company_prefix = strtoupper(substr($words[0] ?? 'UWS', 0, 5));

        $month = (int)date('m');
        $fy_start = ($month <= 3) ? ((int)date('y') - 1) : (int)date('y');
        $fy_end = $fy_start + 1;
        $financial_year = sprintf('%02d%02d', $fy_start, $fy_end); // e.g. "2526"

        // Find max sequential number from existing numbers in salesorder_total
        $this->db->select('number_fk, oc_number');
        $this->db->from('salesorder_total');
        $query = $this->db->get();
        $rows  = $query->result();

        $max_seq = 0;
        foreach ($rows as $row) {
            $val = !empty($row->oc_number) ? $row->oc_number : $row->number_fk;
            if (!empty($val) && preg_match('/-OC-(\d+)$/i', $val, $m)) {
                $seq = (int)$m[1];
                if ($seq > $max_seq) $max_seq = $seq;
            }
        }
        $next_seq = $max_seq + 1;

        echo json_encode([
            'success'        => true,
            'company_prefix' => $company_prefix,
            'financial_year' => $financial_year,
            'next_seq'       => $next_seq
        ]);
    }

}
