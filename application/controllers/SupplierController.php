<?php

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SupplierController extends MY_Controller
{

    protected $user_id;
    protected $user_email;

    private function normalize_optional_value($value)
    {
        $value = is_string($value) ? trim($value) : $value;

        return $value === '' ? null : $value;
    }

    private function normalize_supplier_optional_fields(array $data_supplier)
    {
        $optionalFields = ['fullname', 'pancard', 'gst', 'email', 'mobile', 'state_code', 'address'];

        foreach ($optionalFields as $field) {
            if (array_key_exists($field, $data_supplier)) {
                $data_supplier[$field] = $this->normalize_optional_value($data_supplier[$field]);
            }
        }

        return $data_supplier;
    }

    private function getPurchaseNumberFromUri($startSegment = 3, $endSegment = 8)
    {
        $segments = [];

        for ($segmentIndex = $startSegment; $segmentIndex <= $endSegment; $segmentIndex++) {
            $segmentValue = $this->uri->segment($segmentIndex);
            if ($segmentValue !== NULL && $segmentValue !== '') {
                $segments[] = $segmentValue;
            }
        }

        return implode('/', $segments);
    }


    function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('estimate', '', TRUE);
        $this->load->model('deliverychallan', '', TRUE);
        $this->load->model('Email_model', '', TRUE);

        $this->load->model('invoice', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('payment', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        //added for planning
        $this->load->model('master', '', TRUE);
        $this->load->library('form_validation');
        $this->load->model('Purchase_model');
        $this->load->model('Email_model');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);

        if ($session_data_head === NULL || ($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
            exit();
        }

        $this->user_email = $session_data_head['result']['user_email'];
    }

    public function index()
    {
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/add_supplier', $data);
    }

    public function view_purchase_order()
    {
        $str = $this->input->get('str');
        $month_year_post = $this->input->post('month_year');

        // Debug: Check what's being received
        // echo "POST month_year: " . $month_year_post . "<br>";
        // echo "GET str: " . $str . "<br>";
        // die();

        // Check if form was submitted with month_year
        if (!empty($month_year_post)) {
            // Format from MM-YYYY to YYYY-MM for database
            // Input: 06-2026 (MM-YYYY)
            // Output: 2026-06 (YYYY-MM)
            $month_year_formatted = date('Y-m', strtotime('01-' . $month_year_post));

            // Debug: Check the conversion
            // echo "Converted month_year: " . $month_year_formatted . "<br>";
            // die();

            $data['purchase_order'] = $this->supplier->get_monthyearwise_record($month_year_formatted, $this->user_id);
            $data['selected_month_year'] = $month_year_post; // For display in form
        }
        // Check if "Show All" was clicked
        elseif ($str == "All") {
            $data['purchase_order'] = $this->supplier->get_purchase_order($this->user_id);
            $data['selected_month_year'] = '';
        }
        // Default: show all records on load
        else {
            $data['purchase_order'] = $this->supplier->get_purchase_order($this->user_id);
            $data['selected_month_year'] = '';
        }

        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['po_id'] = $this->supplier->get_last_po_number($this->user_id);
        $data['result'] = $this->supplier->get_supplier($this->user_id);

        // Load system team users with valid email addresses for CC selection
        $data['team_users'] = $this->db->select('username, user_email')
                                       ->from('user')
                                       ->where('user_email IS NOT NULL')
                                       ->where('user_email !=', '')
                                       ->group_by('user_email')
                                       ->get()
                                       ->result_array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_purchase_order', $data);
    }

    public function create_purchase_order()
    {
        $data['quotation_id'] = $this->estimate->get_last_quotation_number($this->user_id);
        $data['po_id'] = (int)$this->supplier->get_last_po_number($this->user_id); // ensure integer

        $pr_id = $this->input->get('pr_id');
        if ($pr_id) {
            $pr_record = $this->db->where('pr_id', $pr_id)->get('purchase_requisition')->row_array();
            if ($pr_record) {
                $data['pr_info'] = $pr_record;
                $data['pr_no'] = $pr_record['pr_no'];
                $data['so_no'] = $pr_record['so_no'] ?? '';
            }
        }

        $data['result'] = $this->supplier->get_supplier($this->user_id);

        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        $data['projects'] = $this->db->get('project')->result_array();
        $data['sales_orders'] = $this->db->select('number_fk, oc_number, project_code')
                                         ->from('salesorder_total')
                                         ->get()
                                         ->result_array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/create_purchase_order', $data);
    }

    public function create_central_gst_purchase()
    {
        $data['quotation_id'] = $this->estimate->get_last_quotation_number($this->user_id);
        $data['po_id'] = $this->supplier->get_last_po_number($this->user_id);

        $pr_id = $this->input->get('pr_id');
        if ($pr_id) {
            $pr_record = $this->db->where('pr_id', $pr_id)->get('purchase_requisition')->row_array();
            if ($pr_record) {
                $data['pr_info'] = $pr_record;
                $data['pr_no'] = $pr_record['pr_no'];
                $data['so_no'] = $pr_record['so_no'] ?? '';
            }
        }

        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['projects'] = $this->db->get('project')->result_array();
        $data['sales_orders'] = $this->db->select('number_fk, oc_number, project_code')
                                         ->from('salesorder_total')
                                         ->get()
                                         ->result_array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/create_central_gst_purchase', $data);
    }



    public function purchase_stock()
    {
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_code'] = $this->master->get_raw_items();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/purchase_stock', $data);
    }

    //Get Purchase Date
    public function view_purchase_stock()
    {
        $from_input = $this->input->post('from_date');
        $to_input = $this->input->post('to_date');

        // avoid passing null to strtotime (PHP 8.1+ deprecation)
        $from_date = '';
        if (!empty($from_input)) {
            $from_date = date("d-m-Y", strtotime($from_input));
        }

        $to_date = '';
        if (!empty($to_input)) {
            $to_date = date("d-m-Y", strtotime($to_input));
        }

        $reload = $this->input->post('reload');
        if ($reload) {
            $from_date = '';
        }
        $data['stock'] = $this->master->get_purchase_stock($this->user_id, $from_date, $to_date);
        $data['purchase_ledger'] = $this->payment->get_purchse_ledger_sum_by_vendor($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_purchase_stock', $data);
    }

    public function get_next_vendor_code()
    {
        try {
            // Check if user_id is set
            if (empty($this->user_id)) {
                echo "5000";  // Default for new user
                return;
            }
            
            // Load supplier model if not already loaded
            if (!isset($this->supplier)) {
                $this->load->model('supplier');
            }
            
            // Get the last supplier code for this user
            $last_code = $this->supplier->get_last_supplier_code($this->user_id);
            
            // If no existing vendors, start from 0; otherwise use the last code
            if ($last_code == 0) {
                $next_code = 5000;  // First vendor code
            } else {
                $next_code = $last_code + 1;  // Next sequential code
            }
            
            // Ensure it's numeric and output
            echo intval($next_code);
        } catch (Exception $e) {
            echo "5000";  // Default to 5000 on error
        }
    }

    public function add_vendor_ajax()
    {
        try {
            $company_name = $this->input->post('company_name');
            $fullname     = $this->input->post('fullname');
            $pancard      = $this->input->post('pancard');
            $gst          = $this->input->post('gst');
            $emails       = $this->input->post('emails');
            if (is_array($emails)) {
                $email = implode(', ', array_filter(array_map('trim', $emails)));
            } else {
                $email = $this->input->post('email');
            }
            $mobile       = $this->input->post('mobile');
            $state_code   = $this->input->post('state_code');
            $address      = $this->input->post('address');

            // Validate company name
            if (empty($company_name)) {
                $result['success'] = false;
                $result['message'] = 'Company name is required';
                echo json_encode($result);
                return;
            }

            // Generate vendor code using same logic as get_next_vendor_code
            $last_code = $this->supplier->get_last_supplier_code($this->user_id);
            if ($last_code == 0) {
                $s_code = 5000;  // First vendor code
            } else {
                $s_code = $last_code + 1;  // Next sequential code
            }

            $already_exists = $this->supplier->supplier_check($company_name, $this->user_id);

            if ($already_exists == false) {
                $data_supplier = $this->normalize_supplier_optional_fields(array(
                    'company_name' => $company_name,
                    'fullname'     => $fullname,
                    'pancard'      => $pancard,
                    'gst'          => $gst,
                    'email'        => $email,
                    'mobile'       => $mobile,
                    'state_code'   => $state_code,
                    'address'      => $address,
                    'uid'          => $this->user_id,
                    's_code'       => $s_code
                ));
                
                $insert_result = $this->supplier->add_supplier($data_supplier);
                
                if ($insert_result) {
                    $result['save_vendor'] = true;
                    $result['success'] = true;
                    $result['message'] = 'Vendor added successfully';
                } else {
                    $result['save_vendor'] = false;
                    $result['success'] = false;
                    $result['message'] = 'Error adding vendor to database';
                }
            } else {
                $result['save_vendor'] = false;
                $result['success'] = false;
                $result['message'] = 'Vendor already exists!';
            }

            $vendors = $this->supplier->get_supplier_name();
            $result['vendors'] = $vendors;
            echo json_encode($result);
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = 'An error occurred: ' . $e->getMessage();
            echo json_encode($result);
        }
    }

    public function add_supplier()
    {
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $s_code = trim($this->input->post('s_code'));

        $emails = $this->input->post('emails');
        if (is_array($emails)) {
            $email = implode(', ', array_filter(array_map('trim', $emails)));
        } else {
            $email = $this->input->post('email');
        }
        $mobile = $this->input->post('mobile');
        $state_code = $this->input->post('state_code');
        $address = $this->input->post('address');
        $redirect_supplier = $this->input->post('redirect_supplier');

        if (empty($s_code)) {
            $s_code = $this->supplier->get_last_supplier_code($this->user_id);
            $s_code = $s_code + 5000;
        }


        $data_supplier = $this->normalize_supplier_optional_fields(array(
            'company_name' => $company_name,
            'fullname' => $fullname,
            'pancard' => $pancard,
            'gst' => $gst,
            'email' => $email,
            'mobile' => $mobile,
            'state_code' => $state_code,
            'address' => $address,
            'uid' => $this->user_id,
            's_code' => $s_code
        ));
        $result = $this->supplier->supplier_check($company_name, $this->user_id);

        if ($result == FALSE) {
            $this->supplier->add_supplier($data_supplier);
            $this->session->set_flashdata('SUCCESSMSG', "Vendor added successfully!!");
            if ($redirect_supplier) {
                redirect('SupplierController/create_purchase_order');
            }
            redirect('SupplierController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Vendor already exist!!");
            if ($redirect_supplier) {
                redirect('SupplierController/create_purchase_order');
            }
            redirect('SupplierController/index');
        }
    }

    public function edit_supplier()
    {
        $supplier_id = $this->input->post('supplier_id');
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $emails = $this->input->post('emails');
        if (is_array($emails)) {
            $email = implode(', ', array_filter(array_map('trim', $emails)));
        } else {
            $email = $this->input->post('email');
        }
        $mobile = $this->input->post('mobile');
        $state_code = $this->input->post('state_code');
        $address = $this->input->post('address');
        $s_code = trim($this->input->post('s_code'));

        $data_supplier = $this->normalize_supplier_optional_fields(array(
            'company_name' => $company_name,
            'fullname' => $fullname,
            'pancard' => $pancard,
            'gst' => $gst,
            'email' => $email,
            'mobile' => $mobile,
            'state_code' => $state_code,
            'address' => $address,
            's_code' => $s_code,
        ));

        $result = $this->supplier->edit_supplier($data_supplier, $supplier_id, $this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Vendor updated successfully!!");
            redirect('SupplierController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Vendor not updated successfully!!");
            redirect('SupplierController/index');
        }
    }

    public function get_supplier_by_id()
    {
        $id = $this->uri->segment(3);
        $data['supplier'] = $this->supplier->get_supplier_by_id($id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/edit_supplier', $data);
    }

    public function delete_supplier_by_id()
    {
        $id = $this->uri->segment(3);
        $result = $this->supplier->delete_supplier_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Vendor deleted successfully!!");
            redirect('SupplierController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Vendor not deleted successfully!!");
            redirect('SupplierController/index');
        }
    }

    public function add_stock()
    {
        $raw_item_id = $this->input->post('product_name');
        $instock = $this->input->post('instock');
        $supplier_name = $this->input->post('supplier_name');
        $paid_amount = $this->input->post('paid_amount');
        $rate_on_item = $this->input->post('rate_on_item');
        $purchase_unit = $this->input->post('purchase_unit');

        $purchase_date = date("Y-m-d", strtotime($this->input->post('purchase_date')));

        $stock_result = $this->master->get_existing_stock($raw_item_id);
        if ($stock_result) {
            $old_stock = $stock_result['raw_item_stock'];
            $stock = $old_stock + $instock;
        } else {
            $old_stock = 0;
            $stock = $instock;
        }

        $supplier_id = $this->supplier->get_supplier_id_by_name($supplier_name, $this->user_id);

        $data_purchase_stock = array(
            'inventory_id_fk' => $raw_item_id,
            'instock' => $instock,
            'oldstock' => $old_stock,
            'supplier_id_fk' => $supplier_id['supplier_id'],
            'purchase_date' => $purchase_date,
            'uid' => $this->user_id,
            'paid_amount' => $paid_amount,
            'rate_on_item' => $rate_on_item,
            'purchase_unit' => $purchase_unit
        );
        //Below comment Due to stock added direct from inventory
        //$this->supplier->add_stock($data_purchase_stock, $inventory_id['stock']);
        $this->db->insert('purchase_stock', $data_purchase_stock);
        $result = $this->master->raw_item_id_check($raw_item_id);
        if ($result == FALSE) {
            $data_raw_item = array('raw_item_id_fk' => $raw_item_id, 'raw_item_stock' => $instock);
            $this->db->insert('raw_items_stock', $data_raw_item);
            redirect('SupplierController/view_purchase_stock/');
        } else {
            $data_raw_item1 = array('raw_item_stock' => $stock);
            //            $this->db->where('uid', $this->user_id);
            $this->db->where('raw_item_id_fk', $raw_item_id);
            $this->db->update('raw_items_stock', $data_raw_item1);
            redirect('SupplierController/view_purchase_stock/');
        }

        redirect('SupplierController/view_purchase_stock/');
    }

    public function get_supplier_names()
    {
        $keyword = $this->input->get('term', TRUE);
        $product_name = $this->supplier->get_supplier_names($keyword);
        $dname_list1 = array();
        if (count($product_name) > 0) {
            foreach ($product_name as $value) {
                $dname_list1[] = $value->fullname;
            }
            echo json_encode($dname_list1);
        }
    }

    public function update_sold_stock()
    {
        $item_name = $this->input->post('item_name');
        $minus_total_quantity = $this->input->post('minus_total_quantity');
        $update_sold_stock = array('stock' => $minus_total_quantity);
        $result = $this->supplier->update_sold_stock($item_name, $update_sold_stock, $this->user_id);
        echo json_encode($result);
    }

    public function print_po()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $number = implode('/', array_filter([$id, $id1, $id2, $id3], function($v) { return $v !== null && $v !== ''; }));
        $data['show_po'] = $this->supplier->get_po_data($number, $this->user_id);
        $data['po_data_group'] = $this->supplier->get_po_data_group_by($number, $this->user_id);
        $data['po_id'] = $this->supplier->get_last_po_number($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $this->session->userdata('session_data_head');
        $this->load->view('admin/po_preview', $data);
    }

    public function add_po()
    {


        $supplier_id = $this->input->post('supplier_id');
        $number = $this->input->post('number');
        $so_no_post = $this->input->post('so_no');
        if (empty($so_no_post)) {
            $so_no_post = $this->input->post('oc_no');
        }

        if (!empty($so_no_post) && !empty($number)) {
            if (!preg_match('/\/\([0-9]+\/[0-9]+\)$/', $number)) {
                $clean_so = trim($so_no_post);
                if (date('m') <= 3) {
                    $default_so_fy = (date('y') - 1) . date('y');
                } else {
                    $default_so_fy = date('y') . (date('y') + 1);
                }
                if (preg_match('/(?:([0-9]{4})|([0-9]{2}-[0-9]{2})).*?([0-9]+)$/', $clean_so, $so_matches)) {
                    $fy_code = !empty($so_matches[1]) ? $so_matches[1] : str_replace('-', '', $so_matches[2]);
                    $so_seq = $so_matches[3];
                    $number = $number . '/(' . $fy_code . '/' . $so_seq . ')';
                } elseif (preg_match('/([0-9]+)$/', $clean_so, $so_matches)) {
                    $number = $number . '/(' . $default_so_fy . '/' . $so_matches[1] . ')';
                }
            }
        }

        $date = date("Y-m-d", strtotime($this->input->post('purchase_date')));

        $delivery_date = $this->input->post('delivery_date');
        $po = $this->input->post('po');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $discount = $this->input->post('discount');
        $unit = $this->input->post('unit');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $gst_check = $this->input->post('gst_check');
        $status = $this->input->post('status');

        $data = array();
        $sgst = '0';
        $igst = '0';
        if ($gst_check == 'gst_check') {
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


        //File upload start
        $data1 = array();
        $config['upload_path'] = './' . $this->config->item('upload_data') . '/';
        $config['allowed_types'] = '*';
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('po_upload')) {
            $data1["file_name"] = "";
        } else {

            $data1 = $this->upload->data();
        }

        //End file upload
        $payment_method = $this->input->post('payment_method');
        $total_quotation_amount = $this->input->post('total_quotation_amount');
        //round of
        $total_quotation_amount = round($total_quotation_amount);
        $subheading = $this->input->post('subheading') ?: '';
        $footer = $this->input->post('footer') ?: '';
        $memo = $this->input->post('memo') ?: '';
        $reasons = $this->input->post('reasons') ?: '';
        $description = $this->input->post('description');


        $po_terms_and_conditions = $this->input->post('po_terms_and_conditions') ?: '';
        $po_payment_terms = $this->input->post('po_payment_terms') ?: '';
        $po_process_schedule = $this->input->post('po_process_schedule') ?: '';
        $po_taxes = $this->input->post('po_taxes') ?: '';
        $po_exclusions = $this->input->post('po_exclusions') ?: '';
        $po_note = $this->input->post('po_note') ?: '';


        $item_count = count($item);

        // var_dump($item_count);
        // die();

        for ($i = 0; $i < $item_count; $i++) {




            //echo $item[$i] . " " . $quantity[$i] . " " .  $hsn[$i] . " " .  $gst_per[$i] . " " .  $sgst[$i] . " " .  $cgst[$i] . " " .  $price[$i] . " " .  $amount[$i];

            //echo $amount[$i];die();

            if ($item[$i] != '' && $quantity[$i] != '' && $hsn[$i] != '' && $gst_per[$i] != ''  && $price[$i] != '' && $amount[$i] != '') {
                $flag = 0;
                //echo 'test';die();
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

                $data[] = array(

                    'supplier_id' => $supplier_id,
                    'number' => $number,
                    'purchase_date' => $date,
                    'delivery_date' => $this->input->post('delivery_date'),
                    'product_name' => $item[$i],
                    'quantity' => $quantity[$i],
                    'discount' => $discount[$i],
                    'unit' => $unit[$i],
                    'hsn_code' => $hsn[$i],
                    'gst' => $gst_per[$i],
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $gst_type,
                    'price' => $price[$i],
                    'amount' => $amount[$i],
                    'subheading' => $subheading,

                    'footer' => $footer,
                    'memo' => $memo,
                    'po_upload' => $data1["file_name"],
                    'reasons' => $reasons,
                    'description' => $description[$i],
                    'po_pending_quantity' => 'Y',
                    'uid' => $this->user_id,
                );
                //   print_r($data);die();
            } else {
                $flag = 1;
            }
        }

        // print_r($data);die();

        if ($flag == 0) {

            $this->db->insert_batch('purchase_order', $data);
            $project_code = $this->input->post('project_code');
            $so_no = $this->input->post('so_no');
            $oc_no = $this->input->post('oc_no');

            if (empty($oc_no) && !empty($so_no)) {
                $so_row = $this->db->select('oc_number')
                                   ->where('number_fk', $so_no)
                                   ->get('salesorder_total')
                                   ->row_array();
                if ($so_row) {
                    $oc_no = $so_row['oc_number'];
                }
            }

            $data_toatl_amount = array(
                'total' => $total_quotation_amount,
                'number_fk' => $number,
                'supplier_id_fk' => $supplier_id,
                'payment_due_date' => $delivery_date,
                'date' => $date,
                'balance' => $total_quotation_amount,
                'payment_method' => $payment_method,
                'uid' => $this->user_id,
                'po_terms_and_conditions' => $po_terms_and_conditions,
                'po_payment_terms' => $po_payment_terms,
                'po_process_schedule' => $po_process_schedule,
                'status' => $status,
                'approval_status' => 'pending_approval',
                'po_taxes' => $po_taxes,
                'po_exclusions' => $po_exclusions,
                'po_note' => $po_note,
                'project_code' => $project_code,
                'so_no' => $so_no,
                'oc_no' => $oc_no
            );
            $result = $this->supplier->add_total_amount($data_toatl_amount);
            $po_total_id = $this->db->insert_id();

            if ($result == TRUE) {
                if ($po_total_id) {
                    $approval_workflow = $this->Purchase_model->get_3level_approval_workflow($total_quotation_amount);
                    $this->db->where('id', $po_total_id)->update('po_total', [
                        'approval_status'  => 'pending_approval',
                        'approval_level'   => $approval_workflow['current_level'],
                        'current_approver' => $approval_workflow['current_approver']
                    ]);

                    foreach ($approval_workflow['workflow'] as $level => $approval) {
                        $approval_data = [
                            'po_id_fk'       => $po_total_id,
                            'approval_level' => $approval['level_name'],
                            'approver_role'  => $approval['role'],
                            'approver_email' => $approval['email'],
                            'status'         => $approval['status'],
                            'level'          => $level,
                            'created_at'     => date('Y-m-d H:i:s'),
                            'uid'            => $this->user_id
                        ];
                        if ($this->db->field_exists('po_number', 'po_approvals')) {
                            $approval_data['po_number'] = $number;
                        }
                        $this->db->insert('po_approvals', $approval_data);
                    }
                }
                $this->session->set_flashdata('SUCCESSMSG', "Purchase Order added successfully!!");
                redirect('SupplierController/view_purchase_order');
            } else {
                $this->session->set_flashdata('INFOMSG', "Purchase Order not added successfully!!");
                redirect('SupplierController/view_purchase_order');
            }
        }
        //        redirect('SupplierController/view_purchase_order/');
    }
    public function show_po()
    {
        $po_number = $this->getPurchaseNumberFromUri();

        $purchase_number = $po_number;

        $data['show_po'] = $this->supplier->get_po_data($po_number, $this->user_id);
        $data['po_data_group'] = $this->supplier->get_po_data_group_by($po_number, $this->user_id);
        if (empty($data['po_data_group'])) {
            $this->session->set_flashdata('FAILMSG', 'Purchase order not found.');
            redirect('SupplierController/view_purchase_order');
            return;
        }

        $data['payment_history'] = $this->supplier->get_purchase_payment_history_data($purchase_number, $this->user_id);
        $data['po_id'] = $this->supplier->get_last_po_number($this->user_id);
        $data['purcahse_bill_id'] = $this->supplier->get_last_purchase_bill_number($this->user_id);
        $data['invoice_id'] = $this->deliverychallan->get_last_delivery_challan_number($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['status_result'] = $this->supplier->get_status_by_purchaseid($purchase_number, $this->user_id);
        $po_status = isset($data['status_result'][0]) && isset($data['status_result'][0]->status)
            ? (int) $data['status_result'][0]->status
            : (int) ($data['po_data_group']['status'] ?? 0);
        $data['is_draft'] = ($po_status === 1);

        // Load amendment model
        $this->load->model('Poamendment_model', 'amendment');
        $data['amendments'] = $this->amendment->get_po_amendments($po_number, $this->user_id);

        // Check if this is a revised PO
        $data['is_revised_po'] = (strpos($po_number, '-R') !== false);
        if ($data['is_revised_po']) {
            $parts = explode('-R', $po_number);
            $data['original_po_number'] = $parts[0];
            $data['revision_number'] = $parts[1];
        } else {
            $data['original_po_number'] = $po_number;
            $data['revision_number'] = 0;
        }

        // Get PO revisions from po_total table
        $data['po_revisions'] = $this->supplier->get_po_revisions($data['original_po_number']);

        // IMPORTANT: Get amendments with revision data
        // If you have an amendments table, get amendments for this PO
        // $data['amendments'] = $this->amendment->get_amendments_by_po($po_number, $this->user_id);

        // Since you don't have amendment data, let's format the po_revisions to match what the view expects
        $data['formatted_revisions'] = [];
        foreach ($data['po_revisions'] as $revision) {
            $formatted_revision = [
                'revision_number' => $revision['revision_number'] ?? 0,
                'amendment_type' => 'price_change', // Default or get from another table
                'amendment_no' => 'REV-' . ($revision['revision_number'] ?? 0),
                'description' => $revision['revision_reason'] ?? 'PO Revision',
                'reason' => $revision['revision_reason'] ?? 'PO Revision',
                'amendment_value' => isset($revision['total']) ? $revision['total'] : 0,
                'new_revised_po_number' => $revision['number_fk'] ?? '',
                'initiated_date' => $revision['date'] ?? '',
                'attachment' => '',
                'status' => $revision['approval_status'] ?? 'pending_approval'
            ];
            $data['formatted_revisions'][] = $formatted_revision;
        }

        // For backward compatibility, set revisions to formatted_revisions
        $data['revisions'] = $data['formatted_revisions'];

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/show_po', $data);
    }
    public function edit_po_details()
    {
        $number = $this->getPurchaseNumberFromUri();
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['show_po'] = $this->supplier->get_po_data($number, $this->user_id);
        $data['po_data_group'] = $this->supplier->get_po_data_group_by($number, $this->user_id);
        if (empty($data['po_data_group'])) {
            $this->session->set_flashdata('FAILMSG', 'Purchase order not found.');
            redirect('SupplierController/view_purchase_order');
            return;
        }
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);
        
        $data['projects'] = $this->db->get('project')->result_array();
        $data['sales_orders'] = $this->db->select('number_fk, oc_number, project_code')
                                         ->from('salesorder_total')
                                         ->get()
                                         ->result_array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/edit_purchase_order', $data);
    }

    public function edit_purchase_order()
    {
        $supplier_id = $this->input->post('supplier_id');
        $po_id = $this->input->post('po_id');
        $current_number = $this->input->post('number'); // Current PO number being edited

        // Debug
        error_log("Editing PO: " . $current_number);

        // Determine if this is already a revised PO
        $is_revised = (strpos($current_number, '-R') !== false);

        if ($is_revised) {
            // This is already a revised PO (e.g., PO/0005/JAN/25-26-R1)
            // Extract base PO and current revision
            $pattern = '/(.*)-R(\d+)$/';
            if (preg_match($pattern, $current_number, $matches)) {
                $base_po = $matches[1]; // PO/0005/JAN/25-26
                $current_rev = (int)$matches[2]; // 1

                // Next revision number
                $next_rev = $current_rev + 1; // 2
                $revised_number = $base_po . '-R' . $next_rev; // PO/0005/JAN/25-26-R2
                $original_po_number = $base_po;

                error_log("Revised PO detected. Base: $base_po, Current Rev: $current_rev, Next Rev: $next_rev");
            } else {
                // Fallback if regex fails
                $base_po = $current_number;
                $next_rev = 1;
                $revised_number = $base_po . '-R' . $next_rev;
                $original_po_number = $base_po;
            }
        } else {
            // This is an original PO (e.g., PO/0005/JAN/25-26)
            $base_po = $current_number;

            // Check if there are any existing revisions
            $this->db->select('MAX(revision_number) as max_rev');
            $this->db->from('po_total');
            $this->db->where('original_po_number', $base_po);
            $this->db->or_where('number_fk', $base_po);
            $query = $this->db->get();
            $result = $query->row_array();

            $next_rev = ($result['max_rev'] ?: 0) + 1;
            $revised_number = $base_po . '-R' . $next_rev;
            $original_po_number = $base_po;

            error_log("Original PO. Base: $base_po, Next Rev: $next_rev");
        }

        error_log("New PO Number: " . $revised_number);

        // echo  $revised_number;
        //         die();

        // Get form data
        $date = date("Y-m-d", strtotime($this->input->post('purchase_date')));
        $delivery_date = $this->input->post('delivery_date');
        $subheading = $this->input->post('subheading') ?: '';
        $footer = $this->input->post('footer') ?: '';
        $memo = $this->input->post('memo') ?: '';
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $discount = $this->input->post('discount');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');

        $po_terms_and_conditions = $this->input->post('po_terms_and_conditions');
        $po_payment_terms = $this->input->post('po_payment_terms');
        $po_process_schedule = $this->input->post('po_process_schedule');
        $po_taxes = $this->input->post('po_taxes');
        $po_exclusions = $this->input->post('po_exclusions');
        $po_note = $this->input->post('po_note');
        $status = $this->input->post('status');
        $revision_reason = $this->input->post('revision_reason') ?: 'PO Revision';

        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
        $igst = $this->input->post('igst');

        $gst_check = $this->input->post('gst');
        $igst_edit_hide_show = $this->input->post('igst_edit_hide_show');
        $quotation_igst_check = $this->input->post('quotation_igst_check');

        if ($gst_check) {
            $igst = '0';
            $gst_type = "S";
        } else if ($igst_edit_hide_show || $quotation_igst_check) {
            $sgst = '0';
            $cgst = '0';
            $gst_type = "I";
        } else if ($this->input->post('gst_check')) {
            $igst = '0';
            $gst_type = "S";
        } else {
            $sgst = '0';
            $cgst = '0';
            $gst_type = "I";
        }

        // File upload
        $data1 = array();
        $config['upload_path'] = './' . $this->config->item('upload_data') . '/';
        $config['allowed_types'] = '*';
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('po_upload')) {
            $data1["file_name"] = "";
        } else {
            $data1 = $this->upload->data();
        }

        $reasons = $this->input->post('reasons');
        $description = $this->input->post('description');

        $payment_method = $this->input->post('payment_method');
        $total_po_amount = round ($this->input->post('total_quotation_amount'));

        $item_count = count($item);

        // Start transaction
        $this->db->trans_begin();

        try {
            // Archive current PO
            $this->db->where('number', $current_number)
                ->where('uid', $this->user_id)
                ->update('purchase_order', array(
                    'is_archived' => 1,
                    'archived_by_revision' => $revised_number
                ));

            $this->db->where('number_fk', $current_number)
                ->where('uid', $this->user_id)
                ->update('po_total', array(
                    'is_archived' => 1,
                    'archived_by_revision' => $revised_number,
                    'is_revised' => 1
                ));

            // Insert new revised items
            for ($i = 0; $i < $item_count; $i++) {
                if (!empty($item[$i])) {
                    if ($sgst == '1') {
                        $igst1 = isset($igst[$i]) ? $igst[$i] : '0';
                        $sgst1 = '0';
                        $cgst1 = '0';
                    } else {
                        $igst1 = '0';
                        $sgst1 = isset($sgst[$i]) ? $sgst[$i] : '0';
                        $cgst1 = isset($cgst[$i]) ? $cgst[$i] : '0';
                    }

                    $data = array(
                        'supplier_id' => $supplier_id,
                        'number' => $revised_number,
                        'purchase_date' => $date,
                        'delivery_date' => $delivery_date,
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
                        'amount' => $amount[$i],
                        'subheading' => $subheading,
                        'footer' => $footer,
                        'memo' => $memo,
                        'po_upload' => $data1["file_name"],
                        'reasons' => $reasons,
                        'description' => $description[$i],
                        'original_po_number' => $original_po_number,
                        'revision_number' => $next_rev,
                        'uid' => $this->user_id,
                        'po_pending_quantity' => 'Y',
                    );

                    $this->db->insert('purchase_order', $data);
                }
            }

            $project_code = $this->input->post('project_code');
            $so_no = $this->input->post('so_no');
            $oc_no = $this->input->post('oc_no');

            if (empty($oc_no) && !empty($so_no)) {
                $so_row = $this->db->select('oc_number')
                                   ->where('number_fk', $so_no)
                                   ->get('salesorder_total')
                                   ->row_array();
                if ($so_row) {
                    $oc_no = $so_row['oc_number'];
                }
            }

            // Insert new PO total
            $data_toatl_amount = array(
                'total' => $total_po_amount,
                'balance' => $total_po_amount,
                'number_fk' => $revised_number,
                'supplier_id_fk' => $supplier_id,
                'payment_due_date' => $delivery_date,
                'date' => $date,
                'payment_method' => $payment_method,
                'uid' => $this->user_id,
                'po_terms_and_conditions' => $po_terms_and_conditions,
                'po_payment_terms' => $po_payment_terms,
                'po_process_schedule' => $po_process_schedule,
                'status' => $status,
                'approval_status' => 'pending_approval',
                'po_taxes' => $po_taxes,
                'po_exclusions' => $po_exclusions,
                'po_note' => $po_note,
                'original_po_number' => $original_po_number,
                'revision_number' => $next_rev,
                'revision_reason' => $revision_reason,
                'project_code' => $project_code,
                'so_no' => $so_no,
                'oc_no' => $oc_no
            );
            
            $this->db->insert('po_total', $data_toatl_amount);


            $po_total_id = $this->db->insert_id(); // This will be 90 for the next insert


            // Get 3-level approval workflow
            $approval_workflow = $this->Purchase_model->get_3level_approval_workflow($total_po_amount);


            // Create approval requests
            foreach ($approval_workflow['workflow'] as $level => $approval) {
                $this->db->insert('po_approvals', [
                    'po_number' => $revised_number,
                    'po_id_fk' => $po_total_id,
                    'approval_level' => $approval['level_name'],
                    'approver_role' => $approval['role'],
                    'approver_email' => $approval['email'],
                    'status' => $approval['status'],
                    'level' => $level,
                    'created_at' => date('Y-m-d H:i:s'),
                    'uid' => $this->user_id
                ]);
            }


            // Record amendment
            $amendment_data = array(
                'po_number' => $current_number,
                'revised_po_number' => $revised_number,
                'revision_number' => $next_rev,
                'revision_reason' => $revision_reason,
                'total_amount' => $total_po_amount,
                'created_by' => $this->user_id,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'draft',
                'original_po_number' => $original_po_number
            );

            $this->db->insert('po_amendments', $amendment_data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('INFOMSG', "Failed to create revised PO!");
                redirect('SupplierController/view_purchase_order');
            } else {
                $this->db->trans_commit();

                // For redirect, we need to handle the URL format
                // Convert PO/0005/JAN/25-26-R2 to PO-0005-JAN-25-26-R2 for URL
                // $redirect_url = str_replace('/', '-', $revised_number);
                error_log("Redirecting to: SupplierController/show_po/" . $revised_number);

                $this->session->set_flashdata('SUCCESSMSG', "Revised PO created successfully! Revised PO #: " . $revised_number);
                redirect('SupplierController/show_po/' . $revised_number);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('INFOMSG', "Error: " . $e->getMessage());
            redirect('SupplierController/view_purchase_order');
        }
    }

    private function archive_original_po($original_po_number, $revised_po_number, $revision_reason, $revision_number)
    {
        // Copy original PO items to archive table or mark them as archived
        $this->db->where('number', $original_po_number)
            ->where('uid', $this->user_id)
            ->update('purchase_order', array(
                'is_archived' => 1,
                'archived_by_revision' => $revised_po_number
            ));

        // Archive the PO total as well
        $this->db->where('number_fk', $original_po_number)
            ->where('uid', $this->user_id)
            ->update('po_total', array(
                'is_archived' => 1,
                'archived_by_revision' => $revised_po_number
            ));
    }

    public function delete_po_by_po_number()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $po_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $result = $this->supplier->delete_po_by_po_number($po_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Order deleted successfully!!");
            redirect('SupplierController/view_purchase_order');
        }
    }

    public function update_po_status()
    {
        $po_number = $this->input->post('po_number');
        $status = $this->input->post('status');
        $remarks = $this->input->post('remarks');
        $data_status = array('status' => $status, 'remarks' => $remarks);

        if ($status == 4) { // Approved
            $data_status['approved_by'] = $this->user_id;
        }

        $this->db->where('number_fk', $po_number);
        $result = $this->db->update('po_total', $data_status);

        if ($result) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Order status updated successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Failed to update status!");
        }
        redirect('SupplierController/view_purchase_order');
    }

    public function send_po_email()
    {

        //get data using session to set mail properties
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'];
        $set_company_logo = base_url() . '/' . $session_data_head2['company_logo'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];
        //enddata using session to set mail properties

        $po_number = $this->input->post('number');
        $po_data_group = $this->supplier->get_po_data_group_by($po_number, $this->user_id);

        if (empty($po_data_group)) {
            $this->session->set_flashdata('INFOMSG', "Purchase Order not found.");
            redirect('SupplierController/view_purchase_order');
            return;
        }

        $name = $po_data_group['fullname'];
        $issue_date = !empty($po_data_group['date']) ? date('d-m-Y', strtotime($po_data_group['date'])) : '';
        $expires_date = !empty($po_data_group['delivery_date']) ? date('d-m-Y', strtotime($po_data_group['delivery_date'])) : '';
        $grand_total = $po_data_group['total'];

        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');



        // echo $po_number;

        $user_id_send = $this->user_id;
        $url = base_url() . 'Download/download_po/' . $po_number . '/' . $user_id_send;


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

        // Handle CC emails (array of selected emails + default cc email)
        $cc_emails_input = $this->input->post('cc_emails');
        if (!empty($cc_emails_input) && is_array($cc_emails_input)) {
            $valid_cc = array_filter(array_map('trim', $cc_emails_input));
            if (!empty($valid_cc)) {
                $this->email->cc(implode(',', array_unique($valid_cc)));
            }
        } elseif ($copy_email && !empty($set_cc_email)) {
            $this->email->cc($set_cc_email);
        }

        $htmlContent11 = '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Purchase Order</title>
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
       
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;"><center>Purchase Order</center></span><br>
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;">' . $po_number . '</span><br>
                    
                <span style="text-decoration:none;color:#a0a0a5;">for <b>' . $name . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">from <b>' . $set_company_name . '</b></span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;">Please check our Purchase Order. </span>
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
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If this purchase order was sent in error, please contact" <a href="mailto:contact@xform.in" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">contact@xform.in</a></span>
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

        $pdf_file_path = $this->generate_purchase_order_pdf($po_number);
        if (!$pdf_file_path || !file_exists($pdf_file_path)) {
            $this->session->set_flashdata('INFOMSG', "Failed to generate Purchase Order PDF.");
            redirect('SupplierController/view_purchase_order');
            return;
        }

        $this->email->attach($pdf_file_path);
        $this->email->message($htmlContent11);

        if ($this->email->send($to_email, $message, $headers)) {
            $po_status_data = array('status' => '2');
            $this->db->where('number_fk', $po_number);
            $this->db->update('po_total', $po_status_data);

            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('SUCCESSMSG', "Email Sent Successfully!!");
            redirect('SupplierController/view_purchase_order');
        } else {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('INFOMSG', "Email not Sent Successfully!!");
            redirect('SupplierController/view_purchase_order');
        }
    }

    public function send_purchase_bill_email()
    {
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'];
        $set_company_logo = base_url() . '/' . $session_data_head2['company_logo'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];

        $bill_number = $this->input->post('number');
        $bill_data_group = $this->supplier->get_purchase_bill_data_group_by($bill_number, $this->user_id);

        if (empty($bill_data_group)) {
            $this->session->set_flashdata('INFOMSG', "Purchase Voucher not found.");
            redirect('SupplierController/view_purchase_bill');
            return;
        }

        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');
        $download_url = base_url() . 'Pdf/download_purchase_bill/' . $bill_number . '/' . $this->user_id;

        $pdf_file_path = $this->generate_purchase_bill_pdf($bill_number);
        if (!$pdf_file_path || !file_exists($pdf_file_path)) {
            $this->session->set_flashdata('INFOMSG', "Failed to generate Purchase Voucher PDF.");
            redirect('SupplierController/view_purchase_bill');
            return;
        }

        $this->db->where('number_fk', $bill_number);
        $this->db->where('uid', $this->user_id);
        $this->db->update('purchase_bill_total', array('status' => '2'));

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

        $this->email->attach($pdf_file_path);
        $this->email->message($this->create_purchase_bill_email_html($bill_number, $bill_data_group, $message, $set_company_name, $set_company_logo, $download_url));

        if ($this->email->send()) {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Voucher email sent successfully!!");
        } else {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('INFOMSG', "Purchase Voucher email not sent successfully!!");
        }

        redirect('SupplierController/view_purchase_bill');
    }

    public function send_purchase_return_email()
    {
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'];
        $set_company_logo = base_url() . '/' . $session_data_head2['company_logo'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];

        $return_number = $this->input->post('number');
        $return_data_group = $this->supplier->get_purchase_return_data_group_by($return_number, $this->user_id);

        if (empty($return_data_group)) {
            $this->session->set_flashdata('INFOMSG', "Returnable Challan not found.");
            redirect('SupplierController/view_purchase_return');
            return;
        }

        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');
        $download_url = base_url() . 'Pdf/download_purchase_return/' . $return_number . '/' . $this->user_id;

        $pdf_file_path = $this->generate_purchase_return_pdf($return_number);
        if (!$pdf_file_path || !file_exists($pdf_file_path)) {
            $this->session->set_flashdata('INFOMSG', "Failed to generate Returnable Challan PDF.");
            redirect('SupplierController/view_purchase_return');
            return;
        }

        $this->db->where('number_fk', $return_number);
        $this->db->where('uid', $this->user_id);
        $this->db->update('purchase_return_total', array('status' => '2'));

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

        $this->email->attach($pdf_file_path);
        $this->email->message($this->create_purchase_return_email_html($return_number, $return_data_group, $message, $set_company_name, $set_company_logo, $download_url));

        if ($this->email->send()) {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('SUCCESSMSG', "Returnable Challan email sent successfully!!");
        } else {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('INFOMSG', "Returnable Challan email not sent successfully!!");
        }

        redirect('SupplierController/view_purchase_return');
    }

    public function get_supplier_email()
    {
        $po_number = $this->input->post('number');
        $result = $this->supplier->get_supplier_email($po_number, $this->user_id);
        echo json_encode($result);
    }

    public function get_purchase_bill_supplier_email()
    {
        $bill_number = $this->input->post('number');
        $result = $this->supplier->get_purchase_bill_supplier_email($bill_number, $this->user_id);
        echo json_encode($result);
    }

    public function get_purchase_return_supplier_email()
    {
        $return_number = $this->input->post('number');
        $result = $this->supplier->get_purchase_return_supplier_email($return_number, $this->user_id);
        echo json_encode($result);
    }

    public function delete_item()
    {
        $po_id = $this->input->post('po_id');
        $result = $this->supplier->delete_item($po_id, $this->user_id);
        echo json_encode($result);
    }

    public function delete_item_purchase_bill()
    {
        $po_bill_id = $this->input->post('po_bill_id');
        $result = $this->supplier->delete_item_purchase_bill($po_bill_id, $this->user_id);
        echo json_encode($result);
    }

    public function delete_item_purchase_return()
    {
        $po_return_id = $this->input->post('po_return_id');
        $result = $this->supplier->delete_item_purchase_return($po_return_id, $this->user_id);
        echo json_encode($result);
    }

    public function view_raw_item_stock()
    {
        $data['stock'] = $this->master->get_raw_item_stock($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_raw_item_stock', $data);
    }

    public function delete_purchase_stock_by_id()
    {
        $id = $this->uri->segment(3);
        $result = $this->supplier->delete_purchase_stock_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Stock deleted successfully!!");
            redirect('SupplierController/view_purchase_stock');
        } else {
            $this->session->set_flashdata('INFOMSG', "Purchase Stock not deleted successfully!!");
            redirect('SupplierController/view_purchase_stock');
        }
    }

    public function delete_row_item_stock_by_id()
    {
        $id = $this->uri->segment(3);
        $result = $this->supplier->delete_row_item_stock_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Row Item Stock deleted successfully!!");
            redirect('SupplierController/view_raw_item_stock');
        } else {
            $this->session->set_flashdata('INFOMSG', "Row Item Stock not deleted successfully!!");
            redirect('SupplierController/view_raw_item_stock');
        }
    }

    public function get_purchase_stock()
    {

        $from_date = '';
        $to_date = '';
        $data['stock'] = $this->master->get_purchase_stock($this->user_id, $from_date, $to_date);
        $data['purchase_ledger'] = $this->payment->get_purchse_ledger_sum_by_vendor($this->user_id);
        echo json_encode($data);
    }

    public function view_purchase_payment()
    {
        $data['supplier'] = $this->supplier->get_supplier($this->user_id);
        $data['result'] = $this->supplier->get_purchase_payment_histroy();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/purchase_payment_history', $data);
    }

    public function add_purchase_payment_histroy()
    {
        $supplier_id_fk = $this->input->post('supplier_id_fk');
        $amount = $this->input->post('amount');
        $payment_type = $this->input->post('payment_type');
        $note = $this->input->post('note');
        $payment_date = date('Y-m-d', strtotime($this->input->post('payment_date')));

        $data_purchase_payment_histroy = array(
            'supplier_id_fk' => $supplier_id_fk,
            'amount' => $amount,
            'payment_type' => $payment_type,
            'note' => $note,
            'payment_date' => $payment_date
        );

        $result = $this->supplier->add_purchase_payment_histroy($data_purchase_payment_histroy);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase payment added successfully!!");
            redirect('SupplierController/view_purchase_payment');
        } else {
            $this->session->set_flashdata('INFOMSG', "Purchase payment already exist!!");
            redirect('SupplierController/view_purchase_payment');
        }
    }

    public function delete_purchase_payment_histroy()
    {
        $id = $this->uri->segment(3);
        $result = $this->supplier->delete_purchase_payment_histroy($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase payment deleted successfully!!");
            redirect('SupplierController/view_purchase_payment');
        } else {
            $this->session->set_flashdata('INFOMSG', "Purchase payment  not deleted successfully!!");
            redirect('SupplierController/view_purchase_payment');
        }
    }
    public function get_monthyearwise_record()
    {
        $month_year = $this->input->post('month_year');
        $data['purchase_order'] = $this->supplier->get_monthyearwise_record($month_year, $this->user_id);
        //   print_r($data['purchase_order']);die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('estimate/view_purchase_order', $data);
    }
    public function purchase_bill()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_purchase_bill');
    }
    public function convert_to_purchase_bill()
    {


        $po_number = $this->input->post('po_number');

        $number_id = $this->supplier->checkPoNumber($po_number);

        if (isset($number_id)) {
            $this->session->set_flashdata('INFOMSG', "Purchase already booked");
            redirect('SupplierController/view_purchase_order');
        } else {

            $result = $this->supplier->add_po_number($po_number);
        }




        $id = $this->uri->segment(3);
        // print_r($id);die();

        $quote_number_id = $this->supplier->get_po_number_from_po_total($id, $this->user_id);
        //print_r($quote_number_id);die();
        $number = $quote_number_id['number_fk'];
        //   print_r($number);die();
        $purchases_bill_number = $this->input->post('purchases_bill_number');

        $total = 0;
        $payment_method = 0;
        $status = 0;
        $po_date = '';
        $exp_date = 0;
        $subheading = '';
        $footer = '';
        $memo = '';
        $supplier_id = '';

        $data_purchase_bill = $this->supplier->get_convert_purchase_bill_data($number, $this->user_id);

        //var_dump($data_purchase_bill) ;die();

        foreach ($data_purchase_bill as $key) {
            //echo $key->date; die();
            //echo $key->purchase_date; die();
            $data[] = array(
                'supplier_id_fk' => $key->supplier_id,
                'number' => $purchases_bill_number,
                'date' => $key->purchase_date,

                'product_name' => $key->product_name,
                'quantity' => $key->quantity,
                'discount' => isset($key->discount) ? $key->discount : '',
                'hsn_code' => $key->hsn_code,
                'unit' => $key->unit,
                'gst' => $key->gst,
                'sgst' => $key->sgst,
                'cgst' => $key->cgst,
                'igst' => $key->igst,
                'gst_type' => $key->gst_type,
                'price' => $key->price,
                'amount' => $key->amount,
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

            $supplier_id = $key->supplier_id;
            $po_date = $key->date;
        }
        $this->db->insert_batch('purchase_bill', $data);

        $data_toatl_amount = array(
            'total' => $total,
            'balance' => $total,
            'number_fk' => $purchases_bill_number,
            'status' => $status,
            'delivery_date' => $delivery_date,
            'payment_due_date' => $delivery_date,
            'footer' => $footer,
            'memo' => $memo,
            'supplier_id_fk' => $supplier_id,
            'po_date' => $po_date,
            'payment_method' => $payment_method,
            'uid' => $this->user_id
        );



        $result = $this->supplier->add_purchase_bill_total_amount($data_toatl_amount);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Order converted to Purchase Bill successfully!!");
            redirect('SupplierController/view_purchase_bill');
        } else {
            $this->session->set_flashdata('INFOMSG', "Purchase Order not converted to Purchase Bill successfully!!");
            redirect('SupplierController/view_purchase_bill');
        }
    }


    public function create_purchase_bill()
    {
        $data['purcahse_bill_id'] = $this->supplier->get_last_purchase_bill_number($this->user_id);


        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        // print_r($data['item_name']);        die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/create_purchase_bill', $data);
    }

    public function create_central_gst_purchase_bill()
    {
        $data['purcahse_bill_id'] = $this->supplier->get_last_purchase_bill_number($this->user_id);
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        // print_r($data['item_name']);        die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/create_central_gst_purchase_bill', $data);
    }





    public function add_purchase_bill()
    {

        $supplier_id = $this->input->post('supplier_id');
        $number = $this->input->post('number');

        $date = date("Y-m-d", strtotime($this->input->post('date')));

        $delivery_date = $this->input->post('delivery_date');
        $po = $this->input->post('po');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $discount = $this->input->post('discount');
        $unit = $this->input->post('unit');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $gst_check = $this->input->post('gst_check');
        $invoice_no = $this->input->post('invoice_no');
        $expenditure_type = $this->input->post('expenditure_type');
        $status = $this->input->post('status');

        // Handle invoice file upload
        $invoice_file = '';
        if (!empty($_FILES['invoice_file']['name'])) {
            $config['upload_path'] ='./uploads/invoice/';
            $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png';
            $config['max_size'] = 5120; // 50MB
            $config['file_name'] = $_FILES['invoice_file']['name'];
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('invoice_file')) {
                $upload_data = $this->upload->data();
                $invoice_file = $upload_data['file_name'];
            } else {
                // Handle upload error if needed
                $this->session->set_flashdata('INFOMSG', 'Invoice file upload failed: ' . $this->upload->display_errors());
                redirect('SupplierController/create_purchase_bill');
            }
        }



        $sgst = '0';
        $igst = '0';
        if ($gst_check == 'gst_check') {
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


        $payment_method = $this->input->post('payment_method');
        $total_quotation_amount = $this->input->post('total_quotation_amount');
        //round of
        $total_quotation_amount = round($total_quotation_amount);


         $total_before_tax = $this->input->post('total_before_tax');
        //round of
        $total_before_tax = ($total_before_tax);

        $subheading = $this->input->post('pv_subheading') ?: $this->input->post('subheading');
        $footer = $this->input->post('pv_footer') ?: '';
        $memo = $this->input->post('pv_memo') ?: '';
        $reasons = $this->input->post('reasons');
        $description = $this->input->post('description');


        $po_terms_and_conditions = $this->input->post('pv_terms_and_conditions') ?: $this->input->post('po_terms_and_conditions');
        $po_payment_terms = $this->input->post('pv_payment_terms') ?: $this->input->post('po_payment_terms');
        $po_process_schedule = $this->input->post('pv_process_schedule') ?: $this->input->post('po_process_schedule');
        $po_taxes = $this->input->post('pv_taxes') ?: $this->input->post('po_taxes');
        $po_exclusions = $this->input->post('pv_exclusions') ?: $this->input->post('po_exclusions');
        $po_note = $this->input->post('pv_note') ?: $this->input->post('po_note');


        $item_count = count($item);

        for ($i = 0; $i < $item_count; $i++) {

            //  echo $item[$i] . " " . $quantity[$i] . " " .  $hsn[$i] . " " .  $gst_per[$i] . " " .  $sgst[$i] . " " .  $cgst[$i] . " " .  $price[$i] . " " .  $amount[$i];

            if ($item[$i] != '') {
                $flag = 0;

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


                $data[] = array(
                    'supplier_id_fk' => $supplier_id,
                    'number' => $number,
                    'date' => $date,
                    'product_name' => $item[$i],
                    'quantity' => $quantity[$i],
                    'discount' => $discount[$i],
                    'unit' => $unit[$i],
                    'hsn_code' => $hsn[$i],
                    'gst' => $gst_per[$i],
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $gst_type,
                    'price' => $price[$i],
                    'amount' => $amount[$i],
                    //  'subheading' => $subheading,
                    //'reasons' => $reasons,
                    'description' => $description[$i],
                    //  'po_pending_quantity' => 'Y',
                    'uid' => $this->user_id,
                );
            } else {
                $flag = 1;
            }
        }



        if ($flag == 0) {

            $this->db->insert_batch('purchase_bill', $data);

            $data_toatl_amount = array(
                'total' => $total_quotation_amount,
                 'total_before_tax' => $total_before_tax,
                'number_fk' => $number,
                'status' => $status,
                'invoice_no' => $invoice_no,
                'expenditure_type' => $expenditure_type,
                'invoice_file' => $invoice_file,
                'delivery_date' => $delivery_date,
                'pv_subheading' => $subheading,
                'pv_footer' => $footer,
                'pv_memo' => $memo,
                'footer' => $footer,
                'memo' => $memo,
                'supplier_id_fk' => $supplier_id,
                'po_date' => $this->input->post('date'),
                'payment_due_date' => $delivery_date,
                'balance' => $total_quotation_amount,
                'payment_method' => $payment_method,
                'pv_terms_and_conditions' => $po_terms_and_conditions,
                'pv_payment_terms' => $po_payment_terms,
                'pv_process_schedule' => $po_process_schedule,
                'pv_taxes' => $po_taxes,
                'pv_exclusions' => $po_exclusions,
                'pv_note' => $po_note,
                'uid' => $this->user_id
            );

            //print_r($data_toatl_amount);die(); 
            $result = $this->supplier->add_purchase_bill_total_amount($data_toatl_amount);

            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Purchase Bill added successfully!!");
                redirect('SupplierController/view_purchase_bill');
            } else {
                $this->session->set_flashdata('INFOMSG', "Purchase Bill not added successfully!!");
                redirect('SupplierController/view_purchase_bill');
            }
        }
        //        redirect('SupplierController/view_purchase_order/');
    }

    public function view_purchase_bill()
    {
        $str = $this->input->get('str');
        //print_r($str);die();
        if ($str == "All") {
            $data['purchase_bill'] = $this->supplier->get_purchase_bill($this->user_id);
            //print_r($data['purchase_bill']); die();
        } else {
            $month_year = date('M-Y');
            $data['purchase_bill'] = $this->supplier->get_purchase_bill_purmonthyearwise_record($month_year, $this->user_id);

            //print_r($data['purchase_bill'] );            die();
        }


        //print_r($data['purchase_bill']);die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['purcahse_bill_id'] = $this->supplier->get_last_purchase_bill_number($this->user_id);
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_purchase_bill', $data);
    }

    public function delete_purchase_bill_by_po_bill_number()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $po_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $result = $this->supplier->delete_purchase_bill_by_po_bill_number($po_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Bill deleted successfully!!");
            redirect('SupplierController/view_purchase_bill');
        } else {
            $this->session->set_flashdata('INFOMSG', "Purchase Bill not deleted successfully!!");
            redirect('SupplierController/view_purchase_bill');
        }
    }


    public function edit_purchase_bill_details()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $number = implode('/', array_filter([$id, $id1, $id2, $id3], function($v) { return $v !== null && $v !== ''; }));
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['show_purchase_bill'] = $this->supplier->get_purchase_bill_data($number, $this->user_id);
        $data['purchase_bill_data_group'] = $this->supplier->get_purchase_bill_data_group_by($number, $this->user_id);
        //print_r($data['purchase_bill_data_group']);die();
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/edit_purchase_bill', $data);
    }



    public function edit_purchase_bill()
    {

        $supplier_id = $this->input->post('supplier_id');
        $number = $this->input->post('number');

        $date = date("Y-m-d", strtotime($this->input->post('date')));

        $delivery_date = $this->input->post('delivery_date');
        $po = $this->input->post('po');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $discount = $this->input->post('discount');
        $unit = $this->input->post('unit');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $gst_check = $this->input->post('gst_check');
        $status = $this->input->post('status');
        $invoice_no = $this->input->post('invoice_no');
        $expenditure_type = $this->input->post('expenditure_type');
        $payment_due_date = $this->input->post('payment_due_date');
        if (empty($delivery_date)) {
            $delivery_date = $payment_due_date;
        }

        // Handle invoice file upload
        $invoice_file = '';
        if (!empty($_FILES['invoice_file']['name'])) {
            $config['upload_path'] = FCPATH . 'uploads/invoice/';
            $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png';
            $config['max_size'] = 51200; // 50MB
            $config['file_name'] = $_FILES['invoice_file']['name'];
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('invoice_file')) {
                $upload_data = $this->upload->data();
                $invoice_file = $upload_data['file_name'];
            } else {
                // Handle upload error if needed
                $this->session->set_flashdata('INFOMSG', 'Invoice file upload failed: ' . $this->upload->display_errors());
                redirect('SupplierController/edit_purchase_bill_details/' . str_replace('/', '-', $number));
            }
        }

        $sgst = '0';
        $igst = '0';
        if ($gst_check == 'gst_check') {
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




        $payment_method = $this->input->post('payment_method');
        $total_quotation_amount = $this->input->post('total_quotation_amount');
        //round of
        $total_quotation_amount = round($total_quotation_amount);


         $total_before_tax = $this->input->post('total_before_tax');
        //round of
        $total_before_tax = ($total_before_tax);
        $total_before_tax = ($total_before_tax);

        $subheading = $this->input->post('pv_subheading') ?: '';
        $footer = $this->input->post('pv_footer') ?: '';
        $memo = $this->input->post('pv_memo') ?: '';
        $reasons = $this->input->post('reasons');
        $description = $this->input->post('description');


        $po_terms_and_conditions = $this->input->post('pv_terms_and_conditions');
        $po_payment_terms = $this->input->post('pv_payment_terms');
        $po_process_schedule = $this->input->post('pv_process_schedule');
        $po_taxes = $this->input->post('pv_taxes');
        $po_exclusions = $this->input->post('pv_exclusions');
        $po_note = $this->input->post('pv_note');

        $po_bill_id = $this->input->post('po_bill_id');

        //print_r($po_bill_id);die();
        $item_count = count($item);

        for ($i = 0; $i < $item_count; $i++) {

            // echo $item[$i] . " " . $quantity[$i] . " " .  $hsn[$i] . " " .  $gst_per[$i] . " " .  $sgst[$i] . " " .  $cgst[$i] . " " .  $price[$i] . " " .  $amount[$i];

            if ($item[$i] != '') {
                $flag = 0;
                if ($po_bill_id[$i]) {
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


                    $data = array(
                        'supplier_id_fk' => $supplier_id,
                        'number' => $number,
                        'date' => $date,

                        'product_name' => $item[$i],
                        'quantity' => $quantity[$i],
                        'discount' => $discount[$i],
                        'unit' => $unit[$i],
                        'hsn_code' => $hsn[$i],
                        'gst' => $gst_per[$i],
                        'sgst' => $sgst1,
                        'cgst' => $cgst1,
                        'igst' => $igst1,
                        'gst_type' => $gst_type,
                        'price' => $price[$i],
                        'amount' => $amount[$i],
                        //  'subheading' => $subheading,



                        //'reasons' => $reasons,
                        'description' => $description[$i],
                        //  'po_pending_quantity' => 'Y',
                        'uid' => $this->user_id,
                    );



                    //$this->db->where('uid', $this->user_id);
                    $this->db->where('number', $number);
                    $this->db->where('po_bill_id', $po_bill_id[$i]);
                    $this->db->update('purchase_bill', $data);
                } else {
                    $data_insert = array(
                        'supplier_id_fk' => $supplier_id,
                        'number' => $number,
                        'date' => $date,

                        'product_name' => $item[$i],
                        'quantity' => $quantity[$i],
                        'discount' => $discount[$i],
                        'unit' => $unit[$i],
                        'hsn_code' => $hsn[$i],

                        'gst' => $gst_per[$i],
                        'sgst' => $sgst1,
                        'cgst' => $cgst1,
                        'igst' => $igst1,
                        'gst_type' => $gst_type,
                        'price' => $price[$i],
                        'amount' => $amount[$i],
                        //  'subheading' => $subheading,



                        //'reasons' => $reasons,
                        'description' => $description[$i],
                        //  'po_pending_quantity' => 'Y',
                        'uid' => $this->user_id,
                    );
                    $this->db->insert('purchase_bill', $data_insert);
                }
            }
        }
        // die(); 


        if ($flag == 0) {


            $data_toatl_amount = array(
                'total' => $total_quotation_amount,
                'total_before_tax' => $total_before_tax,
                'payment_due_date' => $payment_due_date,
                'balance' => $total_quotation_amount,
                'number_fk' => $number,
                'status' => $status,
                'invoice_no' => $invoice_no,
                'expenditure_type' => $expenditure_type,
                'delivery_date' => $delivery_date,
                'pv_subheading' => $subheading,
                'pv_footer' => $footer,
                'pv_memo' => $memo,
                'footer' => $footer,
                'memo' => $memo,
                'supplier_id_fk' => $supplier_id,
                'po_date' => $this->input->post('date'),
                'payment_method' => $payment_method,
                'pv_terms_and_conditions' => $po_terms_and_conditions,
                'pv_payment_terms' => $po_payment_terms,
                'pv_process_schedule' => $po_process_schedule,
                'pv_taxes' => $po_taxes,
                'pv_exclusions' => $po_exclusions,
                'pv_note' => $po_note,
                'uid' => $this->user_id
            );

            // Only update invoice_file if a new file was uploaded
            if (!empty($invoice_file)) {
                $data_toatl_amount['invoice_file'] = $invoice_file;
            }
            // print_r($data_toatl_amount);



            // die();
            $result = $this->supplier->edit_purchase_bill_total_amount($data_toatl_amount, $number);


            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Purchase Bill updated successfully!!");
                redirect('SupplierController/view_purchase_bill');
            } else {
                $this->session->set_flashdata('INFOMSG', "Purchase Bill not updated successfully!!");
                redirect('SupplierController/view_purchase_bill');
            }
        }
        //        redirect('SupplierController/view_purchase_order/');
    }


    public function show_purchase_bill()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $number = implode('/', array_filter([$id, $id1, $id2, $id3], function($v) { return $v !== null && $v !== ''; }));
        $data['show_purchase_bill'] = $this->supplier->get_purchase_bill_data($number, $this->user_id);
        $data['purchase_bill_data_group'] = $this->supplier->get_purchase_bill_data_group_by($number, $this->user_id);
        if (empty($data['purchase_bill_data_group'])) {
            $this->session->set_flashdata('error', 'Purchase voucher not found.');
            redirect('SupplierController/view_purchase_bill');
            return;
        }
        //  $data['po_id'] = $this->supplier->get_last_po_number($this->user_id);
        // $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/show_purchase_bill', $data);
    }


    public function get_purchase_bill_purmonthyearwise_record()
    {
        $month_year = $this->input->post('month_year');
        $data['purchase_bill'] = $this->supplier->get_purchase_bill_purmonthyearwise_record($month_year, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_purchase_bill', $data);
    }
    /* Start of purchase return */

    public function purchase_return()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_purchase_return');
    }
    public function create_purchase_return()
    {
        $data['purcahse_return_id'] = $this->supplier->get_last_purchase_return_number($this->user_id);

        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        // print_r($data['item_name']);        die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/create_purchase_return', $data);
    }

    public function create_central_gst_purchase_return()
    {
        $data['purcahse_return_id'] = $this->supplier->get_last_purchase_return_number($this->user_id);
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        // print_r($data['item_name']);        die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/create_central_gst_purchase_return', $data);
    }





    public function add_purchase_return()
    {

        $supplier_id = $this->input->post('supplier_id');
        $number = $this->input->post('number');

        $date = date("Y-m-d", strtotime($this->input->post('date')));

        $delivery_date = $this->input->post('delivery_date');
        $po = $this->input->post('po');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');



        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        $discount = $this->input->post('discount');

        //          var_dump($gst_per);
        //        die();
        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $gst_check = $this->input->post('gst_check');
        $ref_no = $this->input->post('ref_no');
        $status = $this->input->post('status');


        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
        $igst = $this->input->post('igst');

        $sgst = '0';
        $igst = '0';
        if ($gst_check == 'gst_check') {
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





        $payment_method = $this->input->post('payment_method');
        $total_quotation_amount = round($this->input->post('total_quotation_amount'));

        $subheading = $this->input->post('subheading') ?: '';
        $footer = $this->input->post('footer') ?: '';
        $memo = $this->input->post('memo') ?: '';
        $reasons = $this->input->post('reasons');
        $description = $this->input->post('description');


        $po_terms_and_conditions = $this->input->post('po_terms_and_conditions');
        $po_payment_terms = $this->input->post('po_payment_terms');
        $po_process_schedule = $this->input->post('po_process_schedule');
        $po_taxes = $this->input->post('po_taxes');
        $po_exclusions = $this->input->post('po_exclusions');
        $po_note = $this->input->post('po_note');


        $item_count = count($item);

        for ($i = 0; $i < $item_count; $i++) {

            //      echo $item[$i]; 
            //              echo $quantity[$i];  
            //              echo $unit[$i]; 
            //              echo $hsn[$i]; 
            //              echo $gst_per[$i]; 
            //    echo $sgst[$i]; 
            //  echo $cgst[$i]; 
            //              echo $price[$i];
            //              echo $amount[$i];


            if ($gst_check == 'central_gst_check') {

                // var_dump($igst);
                $igst1 = $igst[$i];
                $sgst1 = '0';
                $cgst1 = '0';
            } else {
                $igst1 = '0';
                $sgst1 = $sgst[$i];
                $cgst1 = $cgst[$i];
                //   echo "sdsd";
            }


            if ($item[$i] != '') {

                $data[] = array(
                    'supplier_id_fk' => $supplier_id,
                    'number' => $number,
                    'date' => $date,

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
                    'discount' => $discount[$i],
                    'amount' => $amount[$i],
                    //  'subheading' => $subheading,



                    //'reasons' => $reasons,
                    'description' => $description[$i],
                    //  'po_pending_quantity' => 'Y',
                    'uid' => $this->user_id,
                );
            } else {
                $flag = 1;
            }
        }

        if ($flag == 0) {

            $this->db->insert_batch('purchase_return', $data);

            $data_toatl_amount = array(
                'total' => $total_quotation_amount,
                'number_fk' => $number,
                'status' => $status,
                'delivery_date' => $this->input->post('delivery_date'),
                'footer' => $footer,
                'memo' => $memo,
                'supplier_id_fk' => $supplier_id,
                'po_date' => $this->input->post('date'),
                'ref_no' => $ref_no,
                'payment_method' => $payment_method,
                'uid' => $this->user_id
            );

            $result = $this->supplier->add_purchase_return_total_amount($data_toatl_amount);

            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Purchase Return added successfully!!");
                redirect('SupplierController/view_purchase_return');
            } else {
                $this->session->set_flashdata('INFOMSG', "Purchase Return not added successfully!!");
                redirect('SupplierController/view_purchase_return');
            }
        }
    }

    public function view_purchase_return()
    {
        $str = $this->input->get('str');
        //print_r($str);die();
        if ($str == "All" || $str === null) {
            $data['purchase_return'] = $this->supplier->get_purchase_return($this->user_id);
        } else {
            $month_year = $str;
            $data['purchase_return'] = $this->supplier->get_purchase_return_purmonthyearwise_record($month_year, $this->user_id);
        }



        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['purcahse_return_id'] = $this->supplier->get_last_purchase_return_number($this->user_id);
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_purchase_return', $data);
    }

    public function delete_purchase_return_by_po_return_number()
    {
        $po_number = $this->getPurchaseNumberFromUri();
        $result = $this->supplier->delete_purchase_return_by_po_return_number($po_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Order deleted successfully!!");
            redirect('SupplierController/view_purchase_return');
        } else {
            $this->session->set_flashdata('INFOMSG', "Purchase Order not deleted successfully!!");
            redirect('SupplierController/view_purchase_return');
        }
    }


    public function edit_purchase_return_details()
    {

        // support both query-param and URI-segment styles for links
        $number = $this->input->get('number');
        if (empty($number)) {
            $number = $this->uri->segment(3);
        }

        $gst_type = $this->input->get('gst_type');
        if (empty($gst_type)) {
            $gst_type = $this->uri->segment(4);
        }



        if ($gst_type == 'I') {
            $data['gst_check'] = "central_gst_check";
        } else {
            $data['gst_check'] = "gst_check";
        }

        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['show_purchase_return'] = $this->supplier->get_purchase_return_data($number, $this->user_id);
        $data['purchase_return_data_group'] = $this->supplier->get_purchase_return_data_group_by($number, $this->user_id);

        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/edit_purchase_return', $data);
    }



    public function edit_purchase_return()
    {

        $supplier_id = $this->input->post('supplier_id');
        $number = $this->input->post('number');

        $date = date("Y-m-d", strtotime($this->input->post('date')));

        $delivery_date = $this->input->post('delivery_date');
        $po = $this->input->post('po');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        $discount = $this->input->post('discount');
        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $gst_check = $this->input->post('gst_check');
        $ref_no = $this->input->post('ref_no');
        $status = $this->input->post('status');

        $sgst = '0';
        $igst = '0';
        //        if ($gst_check == 'gst_check') {
        //           
        //        } else {
        //           
        //        }

        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
        $igst = $this->input->post('igst');
        
        $igst_edit_hide_show = $this->input->post('igst_edit_hide_show');
        $gst_check = $this->input->post('gst');
        $non_gst = $this->input->post('non_gst');
        
        if ($non_gst) {
            $sgst = '0';
            $cgst = '0';
            $igst = '0';
            $gst_type = "N";
        } else if ($gst_check) {
            $igst = '0';
            $gst_type = "S";
        } else if ($igst_edit_hide_show) {
            $gst_type = "I";
        } else {
            $gst_type = "S";
        }

        $payment_method = $this->input->post('payment_method');
        $total_quotation_amount = round($this->input->post('total_quotation_amount'));

        $subheading = $this->input->post('subheading') ?: '';
        $footer = $this->input->post('footer') ?: '';
        $memo = $this->input->post('memo') ?: '';
        $reasons = $this->input->post('reasons');
        $description = $this->input->post('description');


        $po_terms_and_conditions = $this->input->post('po_terms_and_conditions');
        $po_payment_terms = $this->input->post('po_payment_terms');
        $po_process_schedule = $this->input->post('po_process_schedule');
        $po_taxes = $this->input->post('po_taxes');
        $po_exclusions = $this->input->post('po_exclusions');
        $po_note = $this->input->post('po_note');

        $po_return_id = $this->input->post('po_return_id');

        //print_r($po_return_id);die();
        $item_count = is_array($item) ? count($item) : 0;

        // Delete rows that were removed in the UI
        $submitted_ids = array_filter(is_array($po_return_id) ? $po_return_id : array());
        if (!empty($submitted_ids)) {
            $this->db->where('number', $number);
            $this->db->where('uid', $this->user_id);
            $this->db->where_not_in('po_return_id', $submitted_ids);
            $this->db->delete('purchase_return');
        }

        for ($i = 0; $i < $item_count; $i++) {

            if ($gst_type == "I") {
                $igst1 = isset($igst[$i]) ? $igst[$i] : '0';
                $sgst1 = '0';
                $cgst1 = '0';
            } else {
                $igst1 = '0';
                $sgst1 = isset($sgst[$i]) ? $sgst[$i] : '0';
                $cgst1 = isset($cgst[$i]) ? $cgst[$i] : '0';
            }

            if ($po_return_id[$i] != '') {
                $flag = 0;


                $data = array(
                    'supplier_id_fk' => $supplier_id,
                    'number' => $number,
                    'date' => $date,

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
                    'discount' => $discount[$i],
                    'amount' => $amount[$i],
                    //  'subheading' => $subheading,



                    //'reasons' => $reasons,
                    'description' => $description[$i],
                    //  'po_pending_quantity' => 'Y',
                    'uid' => $this->user_id,
                );

                // print_r($data); //die();
                //$this->db->where('uid', $this->user_id);
                $this->db->where('number', $number);
                $this->db->where('po_return_id', $po_return_id[$i]);
                $this->db->update('purchase_return', $data);
            } else {

                $data_insert = array(
                    'supplier_id_fk' => $supplier_id,
                    'number' => $number,
                    'date' => $date,
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
                    'discount' => $discount[$i],
                    'amount' => $amount[$i],
                    //  'subheading' => $subheading,

                    //'reasons' => $reasons,
                    'description' => $description[$i],
                    //  'po_pending_quantity' => 'Y',
                    'uid' => $this->user_id,
                );
                $this->db->insert('purchase_return', $data_insert);
            }
        }



        if ($flag == 0) {


            $data_toatl_amount = array(
                'total' => $total_quotation_amount,
                'number_fk' => $number,
                'status' => $status,
                'delivery_date' => $this->input->post('delivery_date'),
                'footer' => $footer,
                'memo' => $memo,
                'supplier_id_fk' => $supplier_id,
                'po_date' => $this->input->post('date'),
                'ref_no' => $ref_no,
                'payment_method' => $payment_method,
                'uid' => $this->user_id
            );

            $result = $this->supplier->edit_purchase_return_total_amount($data_toatl_amount, $number);


            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Purchase Return updated successfully!!");
                redirect('SupplierController/view_purchase_return');
            } else {
                $this->session->set_flashdata('INFOMSG', "Purchase Return not updated successfully!!");
                redirect('SupplierController/view_purchase_return');
            }
        }
        //        redirect('SupplierController/view_purchase_order/');

    }

    public function show_purchase_return()
    {

        $number = $this->input->get('number');
        $gst_type = $this->input->get('gst_type');
        $data['gst_type'] = $gst_type;
        // Validate input
        if (empty($number)) {
            $this->session->set_flashdata('INFOMSG', 'No Purchase Return number provided.');
            redirect('SupplierController/view_purchase_return');
            return;
        }

        $data['show_purchase_return'] = $this->supplier->get_purchase_return_data($number, $this->user_id);
        $data['purchase_return_data_group'] = $this->supplier->get_purchase_return_data_group_by($number, $this->user_id);

        // If no data was found for the requested number, redirect back with an info message
        if (empty($data['show_purchase_return']) || empty($data['purchase_return_data_group'])) {
            $this->session->set_flashdata('INFOMSG', 'Purchase Return not found.');
            redirect('SupplierController/view_purchase_return');
            return;
        }
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/show_purchase_return', $data);
    }


    public function get_purchase_return_purmonthyearwise_record()
    {
        $month_year = $this->input->post('month_year');
        $data['purchase_return'] = $this->supplier->get_purchase_return_purmonthyearwise_record($month_year, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/view_purchase_return', $data);
    }


    public function edit_purchase_payment()
    {
        $id = $this->input->post('id');
        $number = $this->input->post('number');
        //  print_r($number);die();
        $supplier_id = $this->input->post('supplier_id_fk');
        $payment_type = $this->input->post('payment_type');
        $paid = $this->input->post('paid');
        $balance = $this->input->post('balance');
        $date = $this->input->post('date');
        $payment_method = $this->input->post('payment_method');
        $paid_amount = $this->supplier->get_previous_balance_purchase($number, $this->user_id);
        $total_paid = $paid + $paid_amount['paid'];
        $note = $this->input->post('note');
        $balance1 = abs($balance - $total_paid);
        $purchase_pay_amount = $this->supplier->get_pay_gst_purchase_amount($number, $this->user_id);
        if (count($purchase_pay_amount) == 0) {
            $total_invoice_paid = $paid;
        } else {
            for ($i = 0; $i < count($purchase_pay_amount); $i++) {

                $paid_amounts[] = $purchase_pay_amount[$i]->purchase_pay_amount;
                $total_invoice_paid1 = array_sum($paid_amounts);
            }
            $total_invoice_paid = $total_invoice_paid1 + $paid;
        }

        $total_amount_invoice = $paid_amount['total'];
        $invoice_balance = $total_amount_invoice - $total_invoice_paid;
        $purchase_payment_gst = array(
            'payment_type' => $payment_type,
            'purchase_pay_amount' => $paid,
            'purchase_pay_method' => $payment_method,
            'purchase_pay_date' => $date,
            'purchase_pay_remark' => $note,
            'purchase_number_fk' => $number,
            'uid' => $this->user_id,
            'supplier_id_fk' => $supplier_id
        );
        $this->supplier->pay_gst_purchase_amount($purchase_payment_gst);

        $data_payment = array(
            'paid' => $total_paid,
            'balance' => $invoice_balance,
            'payment_method' => $payment_method,
            'date' => $date,
            'note' => $note,
            'uid' => $this->user_id
        );

        $result = $this->supplier->edit_purchase_payment($data_payment, $id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment updated successfully!!");
            redirect('SupplierController/view_purchase_order');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not updated successfully!!");
            redirect('SupplierController/view_purchase_order');
        }
    }

    public function approve_purchase_status()
    {
        $invoice_no = $this->input->post('number_fk');
        $status = 4;
        $data = array(
            'status' => $status
        );
        $this->db->where('number_fk', $invoice_no);
        $this->db->update('po_total', $data);
        echo 'True';
    }

    public function get_purchase_payment_details()
    {
        $id = $this->input->post('id');
        $arr = $this->supplier->get_purchase_payment_details($id);
        //print_r($arr);die();
        echo json_encode($arr);
    }
    




    public function edit_purchase_bill_payment()
    {
        $id = $this->input->post('id');
        echo $id;
        $number = $this->input->post('number');
        //print_r($number);die();
        $supplier_id = $this->input->post('supplier_id_fk');
        $payment_type = $this->input->post('payment_type');
        $paid = $this->input->post('paid');
        $balance = $this->input->post('balance');
        $date = $this->input->post('date');
        $payment_method = $this->input->post('payment_method');
        $bank_name = $this->input->post('bank_name');
        $paid_amount = $this->supplier->get_previous_balance_purchase_bill($number, $this->user_id);
        $total_paid = $paid + $paid_amount['paid'];
        $note = $this->input->post('note');
        $balance1 = abs($balance - $total_paid);



        $pay_amt = $this->input->post('pay_amt');  // payment_in
        $payment_id = $this->input->post('pay_id');  // payment_in




        $pay_balance = $pay_amt - $paid;

        // pay_balance

        // pay_paid

        $data_payment_in = array("pay_balance" => $pay_balance, "pay_paid" => $paid, "status" => "used");



        if ($payment_id != "") {
            $this->db->where('payment_id', $payment_id);
            $this->db->update('payment_out', $data_payment_in);
        }


        if ($payment_type == "") {
            $payment_type = " ";
        }

        if ($payment_method == "") {
            $payment_method = " ";
        }

        if ($note == "") {
            $note = "Payment In";
        }

        if ($bank_name == "") {
            $bank_name = " ";
        }



        //  echo $balance1;
        $purchase_pay_amount = $this->supplier->get_pay_gst_purchase_amount_bill($number, $this->user_id);
        // var_dump($purchase_pay_amount);
        //         die();
        if (count($purchase_pay_amount) == 0) {
            $total_invoice_paid = $paid;
        } else {
            for ($i = 0; $i < count($purchase_pay_amount); $i++) {

                $paid_amounts[] = $purchase_pay_amount[$i]->purchase_pay_amount;
                $total_invoice_paid1 = array_sum($paid_amounts);
            }
            $total_invoice_paid = $total_invoice_paid1 + $paid;
        }


        // echo $total_invoice_paid;


        // die();

        $total_amount_invoice = $paid_amount['total'];
        $invoice_balance = $total_amount_invoice - $total_invoice_paid;
        $purchase_payment_gst = array(
            'payment_type' => $payment_type,
            'purchase_pay_amount' => $paid,
            'purchase_pay_method' => $payment_method,
            'purchase_pay_date' => $date,
            'purchase_pay_remark' => $note,
            'purchase_number_fk' => $number,
            'uid' => $this->user_id,
            'supplier_id_fk' => $supplier_id
        );

        //  print_r($purchase_payment_gst);die();
        $this->supplier->pay_gst_purchase_amount_bill($purchase_payment_gst);

        $data_payment = array(
            'paid' => $total_paid,
            'balance' => $invoice_balance,
            'payment_method' => $payment_method,
            'po_date' => $date,
            'note' => $note,
            'uid' => $this->user_id
        );

        $result = $this->supplier->edit_purchase_bill_payment($data_payment, $id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment updated successfully!!");
            redirect('SupplierController/view_purchase_bill');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not updated successfully!!");
            redirect('SupplierController/view_purchase_bill');
        }
    }

    public function approve_purchase_bill_status()
    {
        $invoice_no = $this->input->post('number_fk');
        $status = 4;
        $data = array(
            'status' => $status
        );
        $this->db->where('number_fk', $invoice_no);
        $this->db->update('purchase_bill_total', $data);
        echo 'True';
    }


    public function get_purchase_bill_payment_details()
    {
        $id = $this->input->post('id');
        $arr = $this->supplier->get_purchase_bill_payment_details($id);
        echo json_encode($arr);
    }



    public function delete_purchase_return_item()
    {
        $po_return_id = $this->input->post('po_return_id');
        $result = $this->supplier->delete_purchase_return_item($po_return_id);
        echo json_encode($result);
    }



    /**
     * Display the "payment out" screen where users can add or edit
     * supplier payments.  The form is driven by the `payment_out` view and
     * the underlying model returns a list of suppliers with their current
     * balances.  This route is used by the sidebar link labelled "Payment
     * Out".
     */
    public function payment_out()
    {
        $data['company_name'] = $this->supplier->get_company_name_with_bal($this->user_id);
        $data['result'] = $this->supplier->get_payment_out($this->user_id);
        $data['result_by_id'] = null;
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/payment_out', $data);
    }

    /**
     * Placeholder for the future "purchase" functionality.
     * At the moment it simply renders an empty page informing the user that
     * the feature is under construction; the view can be extended later with
     * additional form fields for selecting outstanding purchase invoices.
     */
    public function payment_purchase_against()
    {
        $data['company_name'] = $this->supplier->get_company_name_with_bal($this->user_id);
        // you might later pull a list of purchases that have outstanding
        // balances and pass them to the view here.
        $data['result'] = $this->supplier->get_payment_out($this->user_id);
        $data['result_by_id'] = null;
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/payment_purchase_against', $data);
    }


    function save_payment()
    {
        $payment_id = $this->input->post('payment_id');
        $payment_supplier_id = $this->input->post('supplier_id');
        $payment = $this->input->post('payment');
        $payment_date = $this->input->post('payment_date');
        $payment_type = $this->input->post('payment_type');
        $payment_bank = $this->input->post('payment_bank');
        $payment_method = $this->input->post('payment_method');
        $payment_note = $this->input->post('payment_note');
        $bank_voucher_type = $this->input->post('bank_voucher_type');
        $pay_date = strtotime($payment_date);
        $pay_date1 = date('Y-m-d', $pay_date);

        $data1 = array(
            'payment_supplier_id' => $payment_supplier_id,
            'payment' => $payment,
            'pay_balance' => $payment,
            'payment_date' => $pay_date1,
            'payment_type' => $payment_type,
            'payment_bank' => $payment_bank,
            'payment_method' => $payment_method,
            'payment_note' => $payment_note,
            'bank_voucher_type' => $bank_voucher_type
        );


        if ($payment_id != "") {
            $this->db->where('payment_id', $payment_id);
            $this->db->update('payment_out', $data1);

            $this->session->set_flashdata('SUCCESSMSG', "Payment out updated successfully!!");
            redirect('SupplierController/payment_out');
        } else {
            $this->db->insert('payment_out', $data1);
        }



        $data['company_name'] = $this->supplier->get_supplier_name($this->user_id);
        $data['result'] = $this->supplier->get_payment_out($this->user_id);

        if ($data['result']) {
            $this->session->set_flashdata('SUCCESSMSG', "Payment out added submitted successfully!!");
            redirect('SupplierController/payment_out');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not  submitted successfully!!");
            redirect('SupplierController/payment_out');
        }
    }


    function get_pending_purchase_payment()
    {
        $supplier_id_fk = $this->input->post('supplier_id_fk');
        $result = $this->supplier->get_pending_purchase_payment($supplier_id_fk, $this->user_id);
        echo json_encode($result);
    }

    public function getPaymentById()
    {
        $id = $this->input->get('id');
        $data['result_by_id'] = $this->supplier->getPaymentById($id);
        $data['company_name'] = $this->supplier->get_company_name_with_bal($this->user_id);
        $data['result'] = $this->supplier->get_payment_out($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/payment_out', $data);
    }



    public function convert_to_delivery_challan()
    {
        $id = $this->uri->segment(3);
        // print_r($id);die();

        $quote_number_id = $this->supplier->get_po_number_from_po_total($id, $this->user_id);
        //  print_r($quote_number_id);die();
        $number = $quote_number_id['number_fk'];
        //print_r($number);die();
        $invoice_number = $this->input->post('invoice_number');

        // echo $invoice_id;
        //         die();

        $total = 0;
        $payment_method = 0;
        $status = 0;
        $po_date = '';
        $exp_date = 0;
        $subheading = '';
        $footer = '';
        $memo = '';
        $supplier_id = '';

        $data_purchase_bill = $this->supplier->get_convert_purchase_bill_data($number, $this->user_id);

        //var_dump($data_purchase_bill) ;die();

        foreach ($data_purchase_bill as $key) {
            //echo $key->date; die();
            //echo $key->purchase_date; die();
            $data[] = array(
                'customer_id' => "",
                'invoice_number' => $invoice_number,
                'invoice_date' => $key->purchase_date,

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

            $supplier_id = $key->supplier_id;
            $po_date = $key->date;
            $customer_po = $key->number;
        }
        // var_dump($data);
        // die();
        $this->db->insert_batch('delivery_challan', $data);

        $data_toatl_amount = array(
            'total' => $total,
            'number_fk' => $invoice_number,
            'status' => $status,
            'delivery_date' => $delivery_date,
            'footer' => $footer,
            'memo' => $memo,
            'customer_id_fk' => "",
            'po_date' => $po_date,
            'payment_method' => $payment_method,
            'customer_po' =>  $customer_po,
            'uid' => $this->user_id
        );



        // var_dump($data_toatl_amount);
        // die();

        $result = $this->deliverychallan->add_delivery_challan_total($data_toatl_amount);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Order converted to Delivery Challan successfully!!");
            redirect('DeliveryChallanController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Purchase Order not converted to Delivery Challan successfully!!");
            redirect('DeliveryChallanController/index');
        }
    }


    // Convert RFQ to PO
    public function convert_rfq_to_po()
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $rfq_id = $this->input->post('rfq_id');
            $quotation_id = $this->input->post('quotation_id');
            $vendor_id = $this->input->post('vendor_id');

            $result = $this->Purchase_model->convert_rfq_to_po($rfq_id, $quotation_id, $vendor_id, $this->user_id);



            // var_dump($result);

            // die();


            if ($result['success']) {
                $this->session->unset_userdata('ERRMSG');
                $this->session->unset_userdata('ERRORMSG');
                $this->session->unset_userdata('error');

                if (!empty($result['already_exists'])) {
                    $this->session->set_flashdata('INFOMSG', 'Purchase Order already exists for this quotation! Showing details for ' . $result['po_number']);
                } else {
                    // Send approval notification
                    $po_data = [
                        'pr_id' => $result['quotation_data']['pr_id'] ?? '',
                        'date' => date('Y-m-d'),
                        'payment_due_date' => date('d-m-Y', strtotime('+15 days'))
                    ];

                    // Get supplier data from quotation result
                    $supplier_data = [
                        'company_name' => $result['quotation_data']['supplier_name'] ?? '',
                        'fullname' => $result['quotation_data']['fullname'] ?? ''
                    ];

                    $approver_email = $result['current_approver'] ?? '';
                    $approver_level = $result['approval_workflow']['current_level'] ?? '';
                    $approver_title = ucwords(str_replace('_', ' ', $approver_level));

                    $this->Email_model->send_approval_notification(
                        $result['po_number'],
                        $approver_email,
                        $result['total_amount'],
                        $approver_level,
                        $po_data,
                        $supplier_data
                    );

                    $this->session->set_flashdata('SUCCESSMSG', 'Purchase Order created successfully! Waiting for approval from ' . $approver_title);
                }

                // Redirect to PO details page
                redirect('SupplierController/show_po_details/' . str_replace('/', '-', $result['po_number']));
            } else {
                $this->session->set_flashdata('ERRMSG', $result['message'] ?? 'Failed to convert RFQ to PO');
                redirect('RFQController/show_rfq/' . $rfq_id);
            }
        } else {
            redirect('RFQController/index');
        }
    }
    // PO Approvals Page
    public function po_approvals()
    {


        // echo $this->user_email;

        $data['pending_approvals'] = $this->Purchase_model->get_pending_approvals($this->user_email);
        $data['approval_history'] = $this->Purchase_model->get_approval_history($this->user_email);



        // var_dump($data['pending_approvals']);
        // die();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/po_approvals', $data);
    }

    // Process PO Approval
    // Process PO Approval
    public function process_po_approval()
    {
        $approval_id = $this->input->post('approval_id');
        $action = $this->input->post('action');
        $remarks = $this->input->post('remarks');
        $user_email = $this->user_email;

        $result = $this->Purchase_model->process_po_approval($approval_id, $action, $remarks, $user_email, $this->user_id);

        if ($result) {
            if ($action == 'approved') {
                // Get approval details to check if PO is fully approved
                $approval = $this->db->where('approval_id', $approval_id)->get('po_approvals')->row_array();
                $po_details = $this->Purchase_model->get_po_details($approval['po_number']);

                if ($po_details['approval_status'] == 'approved') {


                    $po_status_data = array('status' => '4');

                    $this->db->where('number_fk', $approval['po_number']);
                    $this->db->update('po_total', $po_status_data);


                    $this->session->set_flashdata('SUCCESSMSG', 'PO approved and sent to vendor!');
                } else {
                    $this->session->set_flashdata('SUCCESSMSG', 'PO approved! Waiting for next approver.');
                }
            } else {
                $this->session->set_flashdata('SUCCESSMSG', 'PO rejected successfully!');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', 'Failed to process approval!');
        }

        redirect('SupplierController/po_approvals');
    }
    // Show PO Details
    public function show_po_details($po_number = '')
    {
        if ($this->session->flashdata('SUCCESSMSG') || $this->session->flashdata('INFOMSG')) {
            $this->session->unset_userdata('ERRMSG');
            $this->session->unset_userdata('ERRORMSG');
            $this->session->unset_userdata('error');
        }

        if (empty($po_number)) {
            $this->session->set_flashdata('INFOMSG', 'Invalid PO number!');
            redirect('SupplierController/view_purchase_order');
            return;
        }

        $data['po'] = $this->Purchase_model->get_po_details($po_number);

        if (empty($data['po'])) {
            $this->session->set_flashdata('INFOMSG', 'Purchase Order not found!');
            redirect('SupplierController/view_purchase_order');
            return;
        }

        $actual_po_number = $data['po']['number_fk'];
        $data['po_items'] = $this->Purchase_model->get_po_items($actual_po_number);
        $data['approval_history'] = $this->Purchase_model->get_approval_details($actual_po_number);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/po_details', $data);
    }

    // Get creator email
    private function get_creator_email($user_id)
    {
        $user = $this->db->where('user_id', $user_id)->get('user')->row_array();
        return $user['email'] ?? $this->session->userdata('user_email');
    }

    // Get pending count for sidebar badge
    private function get_pending_count()
    {
        $user_email = $this->session->userdata('user_email');
        return $this->Purchase_model->get_pending_count($user_email);
    }


    // Add these methods to SupplierController.php

    public function send_draft_to_vendor($draft_id)
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $vendor_email = $this->input->post('vendor_email');
            $subject = $this->input->post('subject');
            $message = $this->input->post('message');

            // Get draft details
            $draft = $this->Purchase_model->get_draft_for_email($draft_id);

            if (!$draft) {
                $this->session->set_flashdata('INFOMSG', 'Draft not found!');
                redirect('SupplierController/view_po_drafts');
            }

            // Mark draft as sent to vendor
            $result = $this->Purchase_model->send_draft_to_vendor(
                $draft_id,
                $vendor_email,
                $this->user_id
            );

            if ($result['success']) {
                // Generate draft PDF
                $pdf_file = $this->generate_draft_pdf($draft, $result['draft_po_number']);

                // Send email
                $email_result = $this->Email_model->send_draft_po_to_vendor(
                    $vendor_email,
                    $result['draft_po_number'],
                    $pdf_file,
                    $draft,
                    $subject,
                    $message
                );

                if ($email_result) {
                    $this->session->set_flashdata(
                        'SUCCESSMSG',
                        'Draft PO sent to vendor for review! Draft #: ' . $result['draft_po_number']
                    );
                } else {
                    $this->session->set_flashdata(
                        'INFOMSG',
                        'Draft saved but email failed. Draft #: ' . $result['draft_po_number']
                    );
                }
            } else {
                $this->session->set_flashdata(
                    'INFOMSG',
                    'Failed to send draft: ' . $result['message']
                );
            }

            redirect('SupplierController/view_po_drafts');
        } else {
            // Show email form
            $data['draft'] = $this->Purchase_model->get_draft_details($draft_id, $this->user_id);

            $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('supplier/send_draft_to_vendor', $data);
        }
    }


    private function format_draft_items($po_data)
    {
        $items = [];
        if (isset($po_data['items']) && is_array($po_data['items'])) {
            foreach ($po_data['items'] as $index => $item) {
                $items[] = (object)[
                    'product_name' => $item['product_name'] ?? '',
                    'quantity' => $item['quantity'] ?? 0,
                    'unit' => $item['unit'] ?? 'PCS',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'gst' => $item['gst'] ?? '18%',
                    'sgst' => $item['sgst'] ?? 0,
                    'cgst' => $item['cgst'] ?? 0,
                    'igst' => $item['igst'] ?? 0,
                    'gst_type' => $item['gst_type'] ?? 'S',
                    'price' => $item['price'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'description' => $item['description'] ?? ''
                ];
            }
        }
        return $items;
    }

    private function get_supplier_name($supplier_id)
    {
        $supplier = $this->db->where('supplier_id', $supplier_id)
            ->get('supplier')
            ->row_array();
        return $supplier['company_name'] ?? 'Unknown Supplier';
    }

    // New method to handle vendor response
    public function vendor_draft_response($draft_token)
    {
        // This would be called when vendor clicks links in the email
        $response = $this->input->post('response');
        $comments = $this->input->post('comments');

        // Validate token and update draft status
        $this->db->where('draft_token', $draft_token)
            ->update('po_drafts', [
                'vendor_response' => $response,
                'vendor_comments' => $comments,
                'vendor_responded_at' => date('Y-m-d H:i:s'),
                'status' => ($response == 'accepted') ? 'vendor_accepted' : 'vendor_rejected'
            ]);

        // Send notification to PO creator
        $this->send_vendor_response_notification($draft_token, $response, $comments);

        // Show thank you page to vendor
        $this->load->view('supplier/vendor_thank_you');
    }

    private function send_vendor_response_notification($draft_token, $response, $comments)
    {
        $draft = $this->db->select('draft_id')
            ->from('po_drafts')
            ->where('draft_token', $draft_token)
            ->get()
            ->row_array();

        if (empty($draft['draft_id'])) {
            return false;
        }

        return $this->Email_model->send_vendor_response_notification(
            $draft['draft_id'],
            $response,
            $comments
        );
    }
public function sgst_to_igst()
{
    $number = $this->input->post('number');

    $this->db->where('number', $number);
    $result = $this->db->get('purchase_bill')->result();

    if (!empty($result)) {

        $gst_type = $result[0]->gst_type;

        foreach ($result as $key) {

            if ($gst_type == 'S') {
                $igst = $key->sgst * 2;

                $this->db->where('number', $number);
                $this->db->where('product_name', $key->product_name);

                $this->db->update('purchase_bill', [
                    'gst_type' => 'I',
                    'igst' => $igst,
                    'sgst' => 0,
                    'cgst' => 0
                ]);
            } else {
                $gst = $key->igst / 2;

                $this->db->where('number', $number);
                $this->db->where('product_name', $key->product_name);

                $this->db->update('purchase_bill', [
                    'gst_type' => 'S',
                    'igst' => 0,
                    'sgst' => $gst,
                    'cgst' => $gst
                ]);
            }
        }
    }

    $this->session->set_flashdata('SUCCESSMSG', "GST type converted successfully!");
    redirect($_SERVER['HTTP_REFERER']);
}
    public function get_vendor_email()
    {
        $po_number = $this->input->post('po_number');

        if (!$po_number) {
            echo json_encode(['success' => false, 'message' => 'PO number required']);
            return;
        }

        // Get PO data to find supplier
        $po_data = $this->supplier->get_po_data_group_by($po_number, $this->user_id);

        if ($po_data) {
            // Get supplier email
            $this->db->select('email');
            $this->db->from('supplier');
            $this->db->where('supplier_id', $po_data['supplier_id']);
            $this->db->where('uid', $this->user_id);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $supplier = $query->row_array();
                echo json_encode(['success' => true, 'email' => $supplier['email']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Supplier not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'PO not found']);
        }
    }


    public function send_draft_po()
    {
        // Get data using session to set mail properties
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'];
        $set_company_logo = base_url() . '/' . $session_data_head2['company_logo'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];

        // Get form data
        $po_number = $this->input->post('number');
        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');

        // Get PO data
        $po_data_group = $this->supplier->get_po_data_group_by($po_number, $this->user_id);

        if (!$po_data_group) {
            $this->session->set_flashdata('INFOMSG', "PO not found!");
            redirect('SupplierController/view_purchase_order');
        }

        $name = $po_data_group['fullname'];
        $issue_date = $po_data_group['date'];
        $expires_date = $po_data_group['delivery_date'];
        $grand_total = $po_data_group['total'];
        $user_id_send = $this->user_id;

        // Generate draft PDF using existing download system
        $pdf_file_path = $this->generate_draft_pdf($po_number);

        if (!$pdf_file_path || !file_exists($pdf_file_path)) {
            $this->session->set_flashdata('INFOMSG', "Failed to generate draft PDF!");
            redirect('SupplierController/view_purchase_order');
        }

        // Update PO status to "Sent" (status 2)
        $this->db->where('number_fk', $po_number);
        $this->db->where('uid', $this->user_id);
        $this->db->update('po_total', array('status' => 2));

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
        $this->email->subject($subject . ' - DRAFT Purchase Order');

        // Handle CC emails (array of selected emails + default cc email)
        $cc_emails_input = $this->input->post('cc_emails');
        if (!empty($cc_emails_input) && is_array($cc_emails_input)) {
            $valid_cc = array_filter(array_map('trim', $cc_emails_input));
            if (!empty($valid_cc)) {
                $this->email->cc(implode(',', array_unique($valid_cc)));
            }
        } elseif ($copy_email && !empty($set_cc_email)) {
            $this->email->cc($set_cc_email);
        }

        // Attach PDF file
        $this->email->attach($pdf_file_path);

        // Create HTML content for draft email (using your existing template structure)
        $htmlContent = $this->create_draft_email_html($po_number, $po_data_group, $message, $set_company_name, $set_company_logo, $this->user_id);

        $this->email->message($htmlContent);

        if ($this->email->send()) {
            // Log the email sending
            $email_log = array(
                'po_number' => $po_number,
                'vendor_email' => $to_email,
                'sent_date' => date('Y-m-d H:i:s'),
                'sent_by' => $this->user_id,
                'email_type' => 'draft'
            );
            $this->db->insert('po_email_logs', $email_log);

            // Clean up PDF file
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }

            $this->session->set_flashdata('SUCCESSMSG', "Draft PO sent to vendor successfully!");
        } else {
            // Revert status if email fails
            $this->db->where('number_fk', $po_number);
            $this->db->where('uid', $this->user_id);
            $this->db->update('po_total', array('status' => 1));

            // Clean up PDF file
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }

            $this->session->set_flashdata('INFOMSG', "Failed to send draft PO!");
        }

        redirect('SupplierController/view_purchase_order');
    }

    private function generate_draft_pdf($po_number)
    {
        // Get PO data
        $data['show_po'] = $this->supplier->get_po_data($po_number, $this->user_id);
        $data['po_data_group'] = $this->supplier->get_po_data_group_by($po_number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        // Add draft flag to data
        $data['is_draft'] = true;

        // Generate HTML content
        $html = $this->load->view('admin/po_draft_print', $data, true);

        // Create uploads directory if it doesn't exist
        $uploads_dir = FCPATH . 'uploads/draft_po/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        // Generate PDF file name
        $file_name = "DRAFT_PO_" . str_replace("/", "_", $po_number) . ".pdf";
        $pdf_file_path = $uploads_dir . $file_name;

        // Check which mPDF method to use
        if (class_exists('\Mpdf\Mpdf')) {
            // Use Composer mPDF
            require_once APPPATH . '../vendor/autoload.php';

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            // Write HTML content
            $mpdf->WriteHTML($html);

            // Save PDF to file
            $mpdf->Output($pdf_file_path, 'F');
        } else {
            // Fallback to CI mPDF library
            $this->load->library('m_pdf');

            // Generate PDF
            $this->m_pdf->pdf->WriteHTML($html);

            // Save PDF to file
            $this->m_pdf->pdf->Output($pdf_file_path, 'F');
        }

        return $pdf_file_path;
    }

    private function create_draft_email_html($po_number, $po_data_group, $custom_message, $company_name, $company_logo, $user_id)
    {
        $name = $po_data_group['fullname'] ?? 'Vendor';
        $issue_date = $po_data_group['date'] ?? date('d-m-Y');
        $expires_date = $po_data_group['delivery_date'] ?? '';
        $grand_total = $po_data_group['total'] ?? 0;

        // Use the same structure as your existing send_po_email method
        return '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>DRAFT Purchase Order</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <head>
        <title>Welcome to ' . $company_name . '</title>
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
        .draft-banner {
            background-color: #fff3cd;
            border: 2px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            margin: 15px 0;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
</style>
    </head>
    <body style=" background: #f8f8f8;">
     <div class="boxs">
       <div class="shadows1">
          <center> <img alt="' . $company_name . '" src="' . $company_logo . '" width="30%"></center>
       
        <div class="draft-banner">
         DRAFT - FOR REVIEW AND CONFIRMATION ONLY 
        </div>
       
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;"><center>Purchase Order</center></span><br>
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;">' . $po_number . '</span><br>
                    
                <span style="text-decoration:none;color:#a0a0a5;">for <b>' . $name . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">from <b>' . $company_name . '</b></span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;">Please review the attached DRAFT Purchase Order. This is for your confirmation before we proceed with the formal order.</span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;"><b>Message :</b>' . nl2br(htmlspecialchars($custom_message)) . '</span>
       <hr>
       <span style="text-decoration:none;color:#2f2f36;font-size:18px">Proposed Total : <b>Rs. ' . number_format($grand_total, 2) . '</b></span>
       <hr>
           <span style="text-decoration:none;color:#2f2f36;">
               Please review the attached PDF draft and respond with your confirmation or any required changes within 3 business days.
           </span>
       <hr>
            <span style="text-decoration:none;color:#2f2f36;">"This is a draft purchase order for review purposes only. Please confirm all details before we proceed with the formal order."</span>
            <hr>
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If you have any questions, please contact us immediately."</span>
         </div>
          <center><span style="text-decoration:none;color:#2f2f36; ">Powered by 
      <img alt="XForm Technologies" src="http://xformtechnologies.com/wp-content/uploads/2017/05/logo.png" width="8%" height="8%" style="margin-top:3%;">
       Xform Technologies </span></center>
   </div>
     
    </body>
    </html>';
    }

    private function generate_purchase_bill_pdf($bill_number)
    {
        $data['show_purchase_bill'] = $this->supplier->get_purchase_bill_data($bill_number, $this->user_id);
        $data['purchase_bill_data_group'] = $this->supplier->get_purchase_bill_data_group_by($bill_number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['stamp'] = 'yes';

        $html = $this->load->view('admin/purchase_bill_print', $data, true);

        $uploads_dir = FCPATH . 'uploads/purchase_bill/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $file_name = 'Purchase_Voucher_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $bill_number) . '.pdf';
        $pdf_file_path = $uploads_dir . $file_name;

        if (class_exists('\Mpdf\Mpdf')) {
            require_once APPPATH . '../vendor/autoload.php';

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            $mpdf->WriteHTML($html);
            $mpdf->Output($pdf_file_path, 'F');
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output($pdf_file_path, 'F');
        }

        return $pdf_file_path;
    }

    private function create_purchase_bill_email_html($bill_number, $bill_data_group, $custom_message, $company_name, $company_logo, $download_url = '')
    {
        $vendor_name = $bill_data_group['fullname'] ?? ($bill_data_group['company_name'] ?? 'Vendor');
        $voucher_date = !empty($bill_data_group['date']) ? date('d-m-Y', strtotime($bill_data_group['date'])) : date('d-m-Y');
        $delivery_date = !empty($bill_data_group['delivery_date']) ? $bill_data_group['delivery_date'] : 'N/A';
        $grand_total = isset($bill_data_group['total']) ? (float) $bill_data_group['total'] : 0;
        $invoice_no = !empty($bill_data_group['invoice_no']) ? $bill_data_group['invoice_no'] : 'N/A';

        return '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Purchase Voucher</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <style>
            .boxs { padding:2% 10%; margin:0 auto; max-width:760px; }
            .shadows1 {
                padding:2% 4%;
                border-radius: 2px;
                line-height: 2;
                text-align: center;
                border: 1px solid grey;
                box-shadow: 0 0 19px rgba(0,0,0,0.18);
                background: #fff;
            }
        </style>
    </head>
    <body style="background:#f8f8f8;">
        <div class="boxs">
            <div class="shadows1">
                <center><img alt="' . $company_name . '" src="' . $company_logo . '" width="30%"></center>
                <span style="color:#2f2f36;font-weight:bold;font-size:32px;"><center>Purchase Voucher</center></span><br>
                <span style="color:#2f2f36;font-weight:bold;font-size:32px;">' . $bill_number . '</span><br>
                <span style="color:#a0a0a5;">for <b>' . $vendor_name . '</b></span><br>
                <span style="color:#a0a0a5;">voucher date : <b>' . $voucher_date . '</b></span><br>
                <span style="color:#a0a0a5;">invoice no : <b>' . $invoice_no . '</b></span><br>
                <span style="color:#a0a0a5;">from <b>' . $company_name . '</b></span>
                <hr>
                <span style="color:#2f2f36;">Please find attached our Purchase Voucher PDF.</span>
                <hr>
                <span style="color:#2f2f36;"><b>Message :</b> ' . nl2br(htmlspecialchars($custom_message)) . '</span>
                <hr>
                <span style="color:#2f2f36;font-size:18px">Grand Total : <b>' . number_format($grand_total, 2) . ' INR</b></span>
                <hr>
                ' . (!empty($download_url) ? '<a href="' . $download_url . '" style="background-color:#00929f;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px" target="_blank">Download in browser</a><br><hr>' : '') . '
                <span style="color:#2f2f36; font-size:12px;"><b>Note:</b> The Purchase Voucher PDF is attached to this email for your convenience.</span>
                <hr>
                <span style="color:#2f2f36;">Delivery Date : <b>' . $delivery_date . '</b></span>
            </div>
        </div>
    </body>
    </html>';
    }

    private function generate_purchase_return_pdf($return_number)
    {
        $data['show_purchase_return'] = $this->supplier->get_purchase_return_data($return_number, $this->user_id);
        $data['purchase_return_data_group'] = $this->supplier->get_purchase_return_data_group_by($return_number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        $html = $this->load->view('admin/purchase_return_print', $data, true);

        $uploads_dir = FCPATH . 'uploads/purchase_return/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $file_name = 'Returnable_Challan_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $return_number) . '.pdf';
        $pdf_file_path = $uploads_dir . $file_name;

        if (class_exists('\Mpdf\Mpdf')) {
            require_once APPPATH . '../vendor/autoload.php';

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            $mpdf->WriteHTML($html);
            $mpdf->Output($pdf_file_path, 'F');
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output($pdf_file_path, 'F');
        }

        return $pdf_file_path;
    }

    private function generate_purchase_order_pdf($po_number)
    {
        $data['show_po'] = $this->supplier->get_po_data($po_number, $this->user_id);
        $data['po_data_group'] = $this->supplier->get_po_data_group_by($po_number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['stamp'] = 'yes';

        $html = $this->load->view('admin/po_print', $data, true);

        $uploads_dir = FCPATH . 'uploads/purchase_order/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $file_name = 'Purchase_Order_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $po_number) . '.pdf';
        $pdf_file_path = $uploads_dir . $file_name;

        if (!class_exists('\Mpdf\Mpdf')) {
            require_once APPPATH . '../vendor/autoload.php';
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output($pdf_file_path, 'F');

        return $pdf_file_path;
    }

    private function create_purchase_return_email_html($return_number, $return_data_group, $custom_message, $company_name, $company_logo, $download_url = '')
    {
        $vendor_name = $return_data_group['fullname'] ?? ($return_data_group['company_name'] ?? 'Vendor');
        $challan_date = !empty($return_data_group['date']) ? date('d-m-Y', strtotime($return_data_group['date'])) : date('d-m-Y');
        $delivery_date = !empty($return_data_group['delivery_date']) ? $return_data_group['delivery_date'] : 'N/A';
        $grand_total = isset($return_data_group['total']) ? (float) $return_data_group['total'] : 0;

        return '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Returnable Challan</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <style>
            .boxs { padding:2% 10%; margin:0 auto; max-width:760px; }
            .shadows1 {
                padding:2% 4%;
                border-radius: 2px;
                line-height: 2;
                text-align: center;
                border: 1px solid grey;
                box-shadow: 0 0 19px rgba(0,0,0,0.18);
                background: #fff;
            }
        </style>
    </head>
    <body style="background:#f8f8f8;">
        <div class="boxs">
            <div class="shadows1">
                <center><img alt="' . $company_name . '" src="' . $company_logo . '" width="30%"></center>
                <span style="color:#2f2f36;font-weight:bold;font-size:32px;"><center>Returnable Challan</center></span><br>
                <span style="color:#2f2f36;font-weight:bold;font-size:32px;">' . $return_number . '</span><br>
                <span style="color:#a0a0a5;">for <b>' . $vendor_name . '</b></span><br>
                <span style="color:#a0a0a5;">challan date : <b>' . $challan_date . '</b></span><br>
                <span style="color:#a0a0a5;">from <b>' . $company_name . '</b></span>
                <hr>
                <span style="color:#2f2f36;">Please find attached our Returnable Challan PDF.</span>
                <hr>
                <span style="color:#2f2f36;"><b>Message :</b> ' . nl2br(htmlspecialchars($custom_message)) . '</span>
                <hr>
                <span style="color:#2f2f36;font-size:18px">Grand Total : <b>' . number_format($grand_total, 2) . ' INR</b></span>
                <hr>
                ' . (!empty($download_url) ? '<a href="' . $download_url . '" style="background-color:#00929f;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px" target="_blank">Download in browser</a><br><hr>' : '') . '
                <span style="color:#2f2f36; font-size:12px;"><b>Note:</b> The Returnable Challan PDF is attached to this email for your convenience.</span>
                <hr>
                <span style="color:#2f2f36;">Delivery Date : <b>' . $delivery_date . '</b></span>
            </div>
        </div>
    </body>
    </html>';
    }




    // Add these methods to SupplierController.php

    public function export_vendors()
    {

        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("System")
            ->setLastModifiedBy("System")
            ->setTitle("Vendor List")
            ->setSubject("Vendor Details")
            ->setDescription("Export of all vendor details");

        // Add report heading (row 1)
        $sheet->setCellValue('A1', 'Vendor Details Report');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getAlignment()->setVertical('center');

        // Add column headers (row 3)
        $headers = [
            'Sr.No.',
            'Vendor Code',
            'Company Name',
            'Contact Person',
            'PAN No',
            'TAX No',
            'Email',
            'Mobile',
            'State Code',
            'Address'
        ];

        $column = 'A';
        $headerRow = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($column . $headerRow, $header);
            $sheet->getStyle($column . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle($column . $headerRow)->getAlignment()->setHorizontal('center');
            $column++;
        }

        // Get vendor data
        $vendors = $this->supplier->get_supplier($this->user_id);

        // Add data rows starting from row 4
        $row = 4;
        $sr_no = 1;

        foreach ($vendors as $vendor) {
            $sheet->setCellValue('A' . $row, $sr_no);
            $sheet->setCellValue('B' . $row, $vendor->s_code ?? '');
            $sheet->setCellValue('C' . $row, $vendor->company_name ?? '');
            $sheet->setCellValue('D' . $row, $vendor->fullname ?? '');
            $sheet->setCellValue('E' . $row, $vendor->pancard ?? '');
            $sheet->setCellValue('F' . $row, $vendor->gst ?? '');
            $sheet->setCellValue('G' . $row, $vendor->email ?? '');
            $sheet->setCellValue('H' . $row, $vendor->mobile ?? '');
            $sheet->setCellValue('I' . $row, $vendor->state_code ?? '');
            $sheet->setCellValue('J' . $row, $vendor->address ?? '');

            $row++;
            $sr_no++;
        }

        // Auto size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set headers for download
        $filename = 'vendors_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }


    public function import_vendors_view()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('supplier/import_vendors');
    }

    public function download_vendor_template()
    {
        require_once APPPATH . '../vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("System")
            ->setLastModifiedBy("System")
            ->setTitle("Vendor Import Template")
            ->setSubject("Vendor Import")
            ->setDescription("Template for importing vendor details");

        // Add headers with instructions
        $headers = [
            'Company Name*',
            'Contact Person',
            'PAN No',
            'GST No',
            'Email',
            'Mobile*',
            'State Code',
            'Address'
        ];

        $instructions = [
            'Company Name*' => 'Required field',
            'Contact Person' => 'Optional',
            'PAN No' => '10 characters max',
            'GST No' => '15 characters max',
            'Email' => 'Valid email format',
            'Mobile*' => 'Required, 10 digits',
            'State Code' => 'Numeric state code',
            'Address' => 'Full address'
        ];

        $column = 'A';
        $row = 1;

        // Add headers
        foreach ($headers as $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $column++;
        }

        // Add instructions in row 2
        $column = 'A';
        $row = 2;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue($column . $row, $instruction);
            $sheet->getStyle($column . $row)->getFont()->setColor(
                new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED)
            );
            $column++;
        }

        // Add sample data in row 3
        $sampleData = [
            'ABC Corporation',
            'John Doe',
            'ABCDE1234F',
            '27ABCDE1234F1Z5',
            'vendor@example.com',
            '9876543210',
            '27',
            '123 Street, City, State'
        ];

        $column = 'A';
        $row = 3;
        foreach ($sampleData as $data) {
            $sheet->setCellValue($column . $row, $data);
            $sheet->getStyle($column . $row)->getFont()->setItalic(true);
            $sheet->getStyle($column . $row)->getFont()->setColor(
                new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN)
            );
            $column++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set headers for download
        $filename = 'vendor_import_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function process_vendor_import()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $config['upload_path'] = './uploads/imports/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = 5120; // 5MB
            $config['encrypt_name'] = TRUE;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('vendor_file')) {
                $this->session->set_flashdata('INFOMSG', $this->upload->display_errors());
                redirect('SupplierController/import_vendors_view');
            }

            $upload_data = $this->upload->data();
            $file_path = $config['upload_path'] . $upload_data['file_name'];

            // Load PhpSpreadsheet
            require_once APPPATH . '../vendor/autoload.php';

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                // Remove header row (row 1)
                array_shift($rows);

                $imported = 0;
                $skipped = 0;
                $errors = [];

                foreach ($rows as $index => $row) {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $row_number = $index + 2; // +2 because we removed header and Excel is 1-indexed

                    // Validate required fields
                    if (empty($row[0]) || empty($row[5])) { // Company Name and Mobile
                        $errors[] = "Row {$row_number}: Missing required fields (Company Name or Mobile)";
                        $skipped++;
                        continue;
                    }

                    // Check if vendor already exists
                    $existing = $this->db
                        ->where('company_name', $row[0])
                        ->where('uid', $this->user_id)
                        ->get('supplier')
                        ->row();

                    if ($existing) {
                        $errors[] = "Row {$row_number}: Vendor '{$row[0]}' already exists";
                        $skipped++;
                        continue;
                    }

                    // Get next supplier code
                    $s_code = $this->supplier->get_last_supplier_code($this->user_id);
                    $s_code = $s_code + 5000;

                    // Prepare data
                    $vendor_data = $this->normalize_supplier_optional_fields([
                        'company_name' => $row[0] ?? '',
                        'fullname' => $row[1] ?? '',
                        'pancard' => strtoupper($row[2] ?? ''),
                        'gst' => strtoupper($row[3] ?? ''),
                        'email' => $row[4] ?? null,
                        'mobile' => $row[5] ?? '',
                        'state_code' => $row[6] ?? '',
                        'address' => $row[7] ?? '',
                        'uid' => $this->user_id,
                        's_code' => $s_code
                    ]);

                    // Insert vendor
                    if ($this->db->insert('supplier', $vendor_data)) {
                        $imported++;
                    } else {
                        $errors[] = "Row {$row_number}: Failed to insert vendor";
                        $skipped++;
                    }
                }

                // Clean up uploaded file
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                // Prepare result message
                $message = "Import completed: {$imported} vendors imported successfully.";
                if ($skipped > 0) {
                    $message .= " {$skipped} vendors skipped.";
                }

                if (!empty($errors)) {
                    $this->session->set_flashdata('IMPORT_ERRORS', $errors);
                }

                $this->session->set_flashdata('SUCCESSMSG', $message);
            } catch (Exception $e) {
                $this->session->set_flashdata('INFOMSG', 'Error processing file: ' . $e->getMessage());
            }

            redirect('SupplierController/import_vendors_view');
        }
    }

    public function export_vendors_pdf()
    {
        require_once APPPATH . '../vendor/autoload.php';

        // Get vendor data
        $vendors = $this->supplier->get_supplier($this->user_id);

        // Create HTML content
        $html = '<!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10pt; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: left; }
            td { border: 1px solid #ddd; padding: 8px; }
            .header { text-align: center; margin-bottom: 20px; }
            .footer { margin-top: 30px; text-align: center; font-size: 9pt; color: #666; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>Vendor List</h2>
            <p>Generated on: ' . date('d-m-Y') . '</p>
            <p>Total Vendors: ' . count($vendors) . '</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Sr.No.</th>
                    <th>Code</th>
                    <th>Company Name</th>
                    <th>Contact Person</th>
                    <th>PAN No</th>
                    <th>TAX No</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>State Code</th>
                </tr>
            </thead>
            <tbody>';

        $sr_no = 1;
        foreach ($vendors as $vendor) {
            $html .= '<tr>
            <td>' . $sr_no . '</td>
            <td>' . ($vendor->s_code ?? '') . '</td>
            <td>' . ($vendor->company_name ?? '') . '</td>
            <td>' . ($vendor->fullname ?? '') . '</td>
            <td>' . ($vendor->pancard ?? '') . '</td>
            <td>' . ($vendor->gst ?? '') . '</td>
            <td>' . ($vendor->email ?? '') . '</td>
            <td>' . ($vendor->mobile ?? '') . '</td>
            <td>' . ($vendor->state_code ?? '') . '</td>
        </tr>';
            $sr_no++;
        }

        $html .= '</tbody>
        </table>
        
        <div class="footer">
            <p>© ' . date('Y') . ' - Generated by ERP System</p>
        </div>
    </body>
    </html>';

        // Create PDF using mPDF
        if (class_exists('\Mpdf\Mpdf')) {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            $mpdf->WriteHTML($html);

            $filename = 'vendors_' . date('Ymd_His') . '.pdf';

            $mpdf->Output($filename, 'D'); // Download
            exit;
        } else {
            // Fallback to CI mPDF library
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);

            $filename = 'vendors_' . date('Ymd_His') . '.pdf';
            $this->m_pdf->pdf->Output($filename, 'D');
            exit;
        }
    }
}
