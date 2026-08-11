<?php

defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceController extends MY_Controller
{

    protected $user_id;

    private function getPostedArray($key)
    {
        $value = $this->input->post($key);

        return is_array($value) ? $value : array();
    }

    private function getFirstPostedArray(array $keys)
    {
        foreach ($keys as $key) {
            $value = $this->getPostedArray($key);
            if (!empty($value)) {
                return $value;
            }
        }

        return array();
    }

    private function getMergedPostedArray(array $keys)
    {
        $merged = array();

        foreach ($keys as $key) {
            $values = $this->getPostedArray($key);
            foreach ($values as $index => $value) {
                if (!array_key_exists($index, $merged) || $merged[$index] === '' || $merged[$index] === null) {
                    $merged[$index] = $value;
                }
            }
        }

        return $merged;
    }

    private function getInvoiceNumberFromUri($startSegment = 3, $endSegment = 6)
    {
        $segments = array();

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
        $this->load->model('customer', '', TRUE);
        $this->load->model('estimate', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('advance', '', TRUE);
        $this->load->library('form_validation');
        $this->load->model('salesorder', '', TRUE);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function index()
    {
        $str = $this->input->get('str');
        if ($str == "All" || $str === null) {
            $data['invoices'] = $this->invoice->get_invoices($this->user_id);
            //print_r($data['invoices']);die();
        } else {
            $month_year = date('M-Y');

            $data['invoices'] = $this->invoice->get_monthyearwise_record($month_year, $this->user_id);
        }
        $data['status_result'] = $this->invoice->get_status($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['next_invoice_name'] = $this->invoice->get_next_invoice_name($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);



        $invoice_number = $this->session->userdata('s_invoice_number');
        //  print_r($invoice_number);die(); 
        $invoice_data_group = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);

        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['invoice_sent_count'] = $this->invoice->get_invoice_draft_count($sent_status, $this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        // print_r($session_data_head);die();
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/view_invoice', $data);
    }



    public function create_invoice()
    {
        $data['result'] = $this->invoice->get_customer($this->user_id);

        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['next_invoice_name'] = $this->invoice->get_next_invoice_name($this->user_id);

        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['barcode'] = $this->inventory->get_product_barcode();
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/create_invoice', $data);
    }


    public function create_central_gst_invoice()
    {
        $data['result'] = $this->invoice->get_customer($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['next_invoice_name'] = $this->invoice->get_next_invoice_name($this->user_id);
        // $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);
        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/create_central_gst_invoice', $data);
    }

    public function add_customer()
    {
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = trim((string) $this->input->post('email'));
        $mobile = trim((string) $this->input->post('mobile'));
        $email = ($email === '') ? null : $email;
        $mobile = ($mobile === '') ? null : $mobile;
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
            } elseif ($igst_check_customer) {
                redirect('InvoiceController/create_central_gst_invoice');
            } else {
                redirect('InvoiceController/create_invoice');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Company already exist!!");
            if ($gst_check_customer) {
                redirect('InvoiceController/create_invoice');
            } elseif ($igst_check_customer) {
                redirect('InvoiceController/create_central_gst_invoice');
            } else {
                redirect('InvoiceController/create_invoice');
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

    public function my_profile()
    {
        $session_data_head = $this->session->userdata('session_data_head');

        $mobile = $session_data_head['result']['user_id'];
        $data['result'] = $this->customers->get_customer_by_mobile($mobile);

        $this->load->view('header_side_bar', $session_data_head);
        $this->load->view('my_profile', $data);
    }

    public function get_customer()
    {
        $mobile = $this->input->post('customer_mobile');
        $data['result'] = $this->customers->get_customer_by_mobile($mobile);
        $this->load->view('view_booked_services', $data);
    }

    public function get_product_name()
    {
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

    public function get_estimate()
    {
        $product_name = $this->input->post('item_name');

        $result = $this->estimate->get_estimate($product_name, $this->user_id);
        echo json_encode($result);
    }

    public function add_new_estimate_customer()
    {
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

    public function get_customer_name_to_append_dropdown()
    {
        $query = $this->db->query("select customer_firstname from customer");
        $row_vendor_name = $query->row_array();
        $data = array("customer_firstname" => $row_vendor_name['customer_firstname']);
        echo json_encode($data);
    }

    public function get_customer_shipping_address() {
        $customer_id = $this->input->post('customer_id');
        if ($customer_id) {
            $this->db->select('address, company_name');
            $this->db->from('customer');
            $this->db->where('customer_id', $customer_id);
            $this->db->where('uid', $this->user_id);
            $query = $this->db->get();
            $customer = $query->row();
            
            if ($customer) {
                // Decode address if it's JSON encoded
                $address_raw = $customer->address ?? '';
                $addresses_array = array();
                $default_address = '';
                
                // Try to decode as JSON
                $decoded = json_decode($address_raw, true);
                
                if (is_array($decoded) && count($decoded) > 0) {
                    // It's a JSON array - store all addresses
                    $addresses_array = $decoded;
                    // Use first address as default
                    $default_address = $decoded[0];
                } else if (!empty($address_raw)) {
                    // Single address (not JSON)
                    $default_address = $address_raw;
                    $addresses_array = array($address_raw);
                }
                
                echo json_encode([
                    'success' => true,
                    'address' => $default_address,
                    'addresses' => $addresses_array,
                    'company_name' => $customer->company_name ?? ''
                ]);
            } else {
                echo json_encode(['success' => false, 'address' => '', 'addresses' => array()]);
            }
        } else {
            echo json_encode(['success' => false, 'address' => '', 'addresses' => array()]);
        }
    }

    public function add_invoice()
    {
        $flag = 0;
        $data = array();

        $customer_id = $this->input->post('customer_id');
        $invoice_number = strtoupper(trim($this->input->post('invoice_number')));
        $invoice_number_id = $this->input->post('invoice_number_id');
        $invoice_date = date("Y-m-d", strtotime($this->input->post('invoice_date')));
        $invoice_customer_po = $this->input->post('invoice_customer_po');
        $invoice_po_date = $this->input->post('invoice_po_date');
        $amount_due = $this->input->post('amount_due');
        $invoice_subheading = $this->input->post('invoice_subheading');
        $invoice_footer = $this->input->post('invoice_footer');
        $invoice_memo = $this->input->post('invoice_memo');
        $invoice_terms_and_conditions = $this->input->post('invoice_terms_and_conditions');
        $invoice_payment_terms = $this->input->post('invoice_payment_terms');
        $invoice_exclusions = $this->input->post('invoice_exclusions');

        // Initialize arrays with empty values if null
        $item = $this->getMergedPostedArray(array('product_name', 'term'));
        $quantity = $this->getPostedArray('quantity');
        $unit = $this->getPostedArray('unit');
        $hsn = $this->getPostedArray('hsn');
        $gst_per = $this->getPostedArray('gst_per');
        $price = $this->getPostedArray('price');
        $amount_quantity = $this->getPostedArray('amount');
        $discount = $this->getPostedArray('discount');
        $description = $this->getPostedArray('description');
        $item_stock = $this->getPostedArray('total_stock');




        // var_dump( $unit );
        // die();



        $igst_check = $this->input->post('igst_check');





        if ($invoice_number === '') {
            $this->session->set_flashdata('INFOMSG', "Invoice name is required!");
            redirect($igst_check ? 'InvoiceController/create_central_gst_invoice' : 'InvoiceController/create_invoice');
            return;
        }

        if ($this->invoice->invoice_name_exists($invoice_number, $this->user_id)) {
            $this->session->set_flashdata('INFOMSG', "Same name invoice present, can't create!");
            redirect($igst_check ? 'InvoiceController/create_central_gst_invoice' : 'InvoiceController/create_invoice');
            return;
        }

        $sgst = '0';
        $igst = '0';

        if (!empty($igst_check)) {
            $igst = $this->input->post('igst');
            $sgst = '1';
            $gst_type = "I";
        } else {
            $igst = '1';
            $sgst = $this->input->post('sgst');
            $cgst = $this->input->post('cgst');
            $gst_type = 'S';
        }



        // echo  $cgst;

        // echo $gst_type;

        // die();

        $due_date = $this->input->post('due_date');
        $pay_amount = $this->input->post('pay_amount');
        $balance = $this->input->post('balance');
        $amount = $this->input->post('total_quotation_amount');
        $status = $this->input->post('status');
        $sez = $this->input->post('sez');
        $payment_method = $this->input->post('payment_method');
        $note = $this->input->post('note');
        $despatch_through = $this->input->post('despatch_through');
        $vehicle_no = $this->input->post('vehicle_no');
        $delivery_date = $this->input->post('delivery_date');
        $delivery_note_no = $this->input->post('delivery_note_no');
        $sales_person = $this->input->post('sales_person');
        $shipping_address = $this->input->post('shipping_address');
        $total_before_tax = $this->input->post('total_before_tax');
         $total_gst_amount = $this->input->post('total_gst_amount');
       
     



        // Round off
        $amount = round($amount);



        $data_invoice_total = array(
            'number_fk' => $invoice_number,
            'date' => $invoice_date,
            'total' => $amount,
            'balance' => $amount,
            'customer_id_fk' => $customer_id,
            'status' => $status,
            'sez' => $sez,
            'payment_method' => $payment_method,
            'note' => $note,
            'despatch_through' => $despatch_through,
            'vehicle_no' => $vehicle_no,
            'delivery_date' => $delivery_date,
            'delivery_note_no' => $delivery_note_no,
            'sales_person' => $sales_person,
            'payment_due_date' => $due_date,
            'invoice_subheading' => $invoice_subheading,
            'invoice_footer' => $invoice_footer,
            'invoice_memo' => $invoice_memo,
            'invoice_terms_and_conditions' => $invoice_terms_and_conditions,
            'invoice_payment_terms' => $invoice_payment_terms,
           
            'invoice_exclusions' => $invoice_exclusions,
            'customer_po' => $invoice_customer_po,
            'po_date' => $invoice_po_date,
            'shipping_address' => $shipping_address,
            'total_before_tax' => $total_before_tax,
            'total_gst_amount' => $total_gst_amount,
            // TDS/TCS fields will be added after database migration
             //'tds_tcs_type' => $tds_tcs_type,
            // 'tds_tcs_rate' => $tds_tcs_rate,
            // 'tds_tcs_amount' => $tds_tcs_amount,
            'uid' => $session_data_head['result']['user_id'] ?? $this->user_id
        );

        foreach ($item as $index => $productName) {
            $productName = trim((string) $productName);
            $rowQuantity = isset($quantity[$index]) ? trim((string) $quantity[$index]) : '';
            $rowHsn = isset($hsn[$index]) ? trim((string) $hsn[$index]) : '';
            $rowPrice = isset($price[$index]) ? trim((string) $price[$index]) : '';

            // Ignore placeholder/empty rows so PHP-rendered extra rows do not block valid ones.
            if ($productName === '' && $rowQuantity === '' && $rowHsn === '' && $rowPrice === '') {
                continue;
            }

            if ($productName === '' || $rowQuantity === '' || $rowHsn === '' || $rowPrice === '') {
                $flag = 1;
                continue;
            }

            $sgst1 = '0';
            $cgst1 = '0';
            $igst1 = '0';

        if ($gst_type == 'I') {

    // IGST invoice
    $igst1 = isset($igst[$index]) ? $igst[$index] : '0';

} else {

    // SGST + CGST invoice
    $sgst1 = isset($sgst[$index]) ? $sgst[$index] : '0';
    $cgst1 = isset($cgst[$index]) ? $cgst[$index] : '0';
}

            $data[] = array(
                'invoice_number' => $invoice_number,
                'invoice_date' => $invoice_date,
                'customer_id' => $customer_id,
                'product_name' => $productName,
                'quantity' => $rowQuantity,
                'unit' => isset($unit[$index]) ? $unit[$index] : '',
                'hsn_code' => $rowHsn,
                'gst' => isset($gst_per[$index]) ? $gst_per[$index] : '0',
                'discount' => isset($discount[$index]) ? $discount[$index] : '0',
                'sgst' => $sgst1,
                'cgst' => $cgst1,
                'igst' => $igst1,
                'gst_type' => $gst_type,
                'price' => $rowPrice,
                'amount' => isset($amount_quantity[$index]) ? $amount_quantity[$index] : '0',
                'description' => isset($description[$index]) ? $description[$index] : '',
                'uid' => $this->user_id,
            );
        }



        // var_dump($data);

        //  die();
        if (!empty($data)) {
            $this->db->insert_batch('invoice', $data);
            $result = $this->invoice->add_invoice_total($data_invoice_total);

            // // Update stock
            // for ($i = 0; $i < $item_count; $i++) {
            //     if (isset($item[$i]) && $item[$i] != '') {
            //         $stock = $this->invoice->get_inventory_stock_count($item[$i], $this->user_id);
            //         if ($stock && isset($stock['stock'])) {
            //             $this->db->where('code', $item[$i]);
            //             $this->db->update('inventory', array('stock' => $stock['stock'] - $quantity[$i]));
            //         }
            //     }
            // }

            if ($result == TRUE) {
                $successMessage = $flag === 1
                    ? "Invoice submitted successfully. Incomplete item rows were skipped."
                    : "Invoice submitted successfully!!";

                $this->session->set_flashdata('SUCCESSMSG', $successMessage);
                redirect('InvoiceController/index');
            } else {
                $this->session->set_flashdata('INFOMSG', "Invoice not submitted successfully!!");
                redirect('InvoiceController/index');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Please fill all required fields for invoice items!");
            redirect('InvoiceController/index');
        }
    }

    public function delete_invoice_by_invoice_number()
    {
        $invoice_number = $this->getInvoiceNumberFromUri();
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

    public function show_invoice()
    {
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
        $data['next_invoice_name'] = $this->invoice->get_next_invoice_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        // print_r($data);        die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/show_invoice', $data);
        // var_dump($data);
        // die();

        // Debug the contents of $data['show_invoice']
        // echo '<pre>';
        // print_r($data['show_invoice']);
        // echo '</pre>';
        //  die();


    }

    public function sgst_to_igst()
    {
        $invoice_number = $this->input->get('invoice_number');

        $this->invoice->update_invoice_data($invoice_number, $this->user_id);

        $this->index();
    }






    public function print_invoice()
    {
        $invoice_number = $this->getInvoiceNumberFromUri();
        $data['show_invoice'] = $this->invoice->get_invoice_data($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $this->session->userdata('session_data_head');

        $this->load->view('admin/invoice_preview', $data);
    }

    public function get_invoice_payment_details()
    {
        $id = $this->input->post('id');
        $arr = $this->invoice->get_invoice_payment_details($id);
        echo json_encode($arr);
    }

    public function get_proforma_invoice_payment_details()
    {
        $id = $this->input->post('id');
        $arr = $this->invoice->get_proforma_invoice_payment_details($id);
        echo json_encode($arr);
    }



    public function edit_invoice_payment()
    {
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

        $data_payment_in = array("pay_balance" => $pay_balance, "pay_paid" => $paid, "status" => "used");



        if ($payment_id != "") {
            $this->db->where('payment_id', $payment_id);
            $this->db->update('payment_in', $data_payment_in);
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
        $invoice_payment_gst = array(
            'payment_type' => $payment_type,
            'invocie_pay_amount' => $paid,
            'invocie_pay_method' => $payment_method,
            'invoice_pay_date' => $date,
            'invoice_pay_remark' => $note,
            'invoice_number_fk' => $invoice_number,
            'uid' => $this->user_id,
            'rem_balance' => $invoice_balance,
            'bank_name' => $bank_name,
            'customer_id_fk' => $customer_id_fk
        );
        //   print_r($invoice_payment_gst);die();
        $this->invoice->pay_gst_invoice_amount($invoice_payment_gst);

        $data_payment = array(
            'paid' => $total_paid,
            'balance' => $invoice_balance,
            'payment_method' => $payment_method,
            'note' => $note,
            'uid' => $this->user_id
        );
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



    public function edit_invoice_details()
    {
        $invoice_number = $this->getInvoiceNumberFromUri();


        $data['show_invoice'] = $this->invoice->get_invoice_data($invoice_number, $this->user_id);
        //  print_r($data['show_invoice']);die();
        $data['invoice_data_group'] = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);

        if (empty($data['invoice_data_group'])) {
            $this->session->set_flashdata('INFOMSG', 'Invoice not found: ' . htmlspecialchars($invoice_number));
            redirect('InvoiceController/index');
            return;
        }

        $data['customer_result'] = $this->invoice->get_company_name($this->user_id);
        $data['status_result'] = $this->invoice->get_status_by_invoiceid($invoice_number, $this->user_id);
        if (empty($data['status_result'])) {
            $obj = new stdClass();
            $obj->status = 1;
            $data['status_result'] = [$obj];
        }
        //  $data['product_code_list'] = $this->inventory->get_product_part_name_edit($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        // print_r( $this->user_id); die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/edit_invoice', $data);
    }



    public function edit_invoice()
    {

        $customer_id = $this->input->post('customer_id');
        $invoice_id = $this->getPostedArray('invoice_id');
        $invoice_number = $this->input->post('invoice_number');
        $date = $this->input->post('invoice_date');

        $date = date("Y-m-d", strtotime($date));

        $invoice_customer_po = $this->input->post('invoice_customer_po');
        $invoice_po_date = $this->input->post('invoice_po_date');
        $item = $this->getMergedPostedArray(array('product_name', 'term'));
        $quantity = $this->getPostedArray('quantity');
        $unit = $this->getPostedArray('unit');
        $hsn = $this->getPostedArray('hsn');
        $gst_per = $this->getPostedArray('gst_per');
        //Check Invoice gst type for edit invoice
        $gst_check = $this->input->post('gst');

        $total_before_tax = $this->input->post('total_before_tax');

        $total_gst_amount = $this->input->post('total_gst_amount');

        //   print_r($total_gst_amount);die();

        $sgst = '0';
        $igst = '0';
        if ($gst_check) {
            $igst = '1';
            $sgst = $this->getPostedArray('sgst');
            $cgst = $this->getPostedArray('cgst');
            $gst_type = "S";
        } else {
            $sgst = '1';
            $cgst = '1';
            $igst = $this->getPostedArray('igst');
            $gst_type = "I";
        }

        $price = $this->getPostedArray('price');
        $amount = $this->input->post('total_quotation_amount');
        $amount_quantity = $this->getPostedArray('amount');
        $discount = $this->getPostedArray('discount');
        $payment_due_date = $this->input->post('payment_due_date');
        $invoice_subheading = $this->input->post('invoice_subheading');
        $invoice_footer = $this->input->post('invoice_footer');
        $invoice_memo = $this->input->post('invoice_memo');
        $invoice_terms_and_conditions = $this->input->post('invoice_terms_and_conditions');
        $invoice_payment_terms = $this->input->post('invoice_payment_terms');
        
        $invoice_exclusions = $this->input->post('invoice_exclusions');
        $note = $this->input->post('note');
        $description = $this->getPostedArray('description');
        $payment_method = $this->input->post('payment_method');
        $status = $this->input->post('status');
        $total_invoice_amount = $this->input->post('total_quotation_amount');
        $despatch_through = $this->input->post('despatch_through');
        $vehicle_no = $this->input->post('vehicle_no');
        $delivery_date = $this->input->post('delivery_date');
        $delivery_note_no = $this->input->post('delivery_note_no');
        $shipping_address = $this->input->post('shipping_address');
        $sales_person = $this->input->post('sales_person');
        $data = array();
        //round of
        $total_invoice_amount = round($total_invoice_amount);

        foreach ($item as $index => $productName) {
            $productName = trim((string) $productName);
            $rowQuantity = isset($quantity[$index]) ? trim((string) $quantity[$index]) : '';
            $rowHsn = isset($hsn[$index]) ? trim((string) $hsn[$index]) : '';
            $rowPrice = isset($price[$index]) ? trim((string) $price[$index]) : '';

            if ($productName === '' || $rowQuantity === '' || $rowHsn === '' || $rowPrice === '') {
                continue;
            }

            $rowInvoiceId = isset($invoice_id[$index]) ? $invoice_id[$index] : '';

            if ($rowInvoiceId) {

                // //for update stock in inventory table when invoice created
                // $stock = $this->invoice->get_inventory_stock_count($item[$i], $this->user_id);
                // $edit_quantity = $this->invoice->get_invoice_quantity_count($item[$i], $invoice_number, $this->user_id);
                // $datas1[] = array(
                //     'stock' => (abs($edit_quantity['quantity'] + $stock['stock']) - ($quantity[$i])),
                // );
                // $this->db->where('code', $item[$i]);
                // $this->db->update('inventory', $datas1[$i]);
                // //for end update stock in inventory table when invoice created

                if ($sgst == '1') {
                    $igst1 = isset($igst[$index]) ? $igst[$index] : '0';
                    $sgst1 = '0';
                    $cgst1 = '0';
                }
                if ($igst == '1') {
                    $igst1 = '0';
                    $sgst1 = isset($sgst[$index]) ? $sgst[$index] : '0';
                    $cgst1 = isset($cgst[$index]) ? $cgst[$index] : '0';
                }

                $data = array(
                    'invoice_number' => $invoice_number,
                    'invoice_date' => date("Y-m-d", strtotime($this->input->post('invoice_date'))),
                    'customer_id' => $customer_id,
                    'product_name' => $productName,
                    'quantity' => $rowQuantity,
                    'unit' => isset($unit[$index]) ? $unit[$index] : '',
                    'hsn_code' => $rowHsn,
                    'gst' => isset($gst_per[$index]) ? $gst_per[$index] : '0',
                    'discount' => isset($discount[$index]) ? $discount[$index] : '0',
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $gst_type,
                    'price' => $rowPrice,
                    'amount' => isset($amount_quantity[$index]) ? $amount_quantity[$index] : '0',
                    'description' => isset($description[$index]) ? $description[$index] : '',
                );

                $this->db->where('invoice_number', $invoice_number);
                $this->db->where('invoice_id', $rowInvoiceId);
                $this->db->update('invoice', $data);
            } else {

                if ($sgst == '1') {
                    $igst1 = isset($igst[$index]) ? $igst[$index] : '0';
                    $sgst1 = '0';
                    $cgst1 = '0';
                }
                if ($igst == '1') {
                    $igst1 = '0';
                    $sgst1 = isset($sgst[$index]) ? $sgst[$index] : '0';
                    $cgst1 = isset($cgst[$index]) ? $cgst[$index] : '0';
                }

                $data_insert = array(
                    'invoice_number' => $invoice_number,
                    'invoice_date' => date("Y-m-d", strtotime($this->input->post('invoice_date'))),
                    'customer_id' => $customer_id,
                    'product_name' => $productName,
                    'quantity' => $rowQuantity,
                    'unit' => isset($unit[$index]) ? $unit[$index] : '',
                    'hsn_code' => $rowHsn,
                    'gst' => isset($gst_per[$index]) ? $gst_per[$index] : '0',
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $gst_type,
                    'price' => $rowPrice,
                    'discount' => isset($discount[$index]) ? $discount[$index] : '0',
                    'amount' => isset($amount_quantity[$index]) ? $amount_quantity[$index] : '0',
                    'description' => isset($description[$index]) ? $description[$index] : '',
                    'uid' => $this->user_id,
                );
                $this->db->insert('invoice', $data_insert);
            }
        }
        //for update stock in inventory table when invoice created
        foreach ($item as $index => $productName) {
            $productName = trim((string) $productName);
            if ($productName !== '') {
                $rowQuantity = isset($quantity[$index]) ? $quantity[$index] : 0;

                $stock = $this->invoice->get_inventory_stock_count($productName, $this->user_id);
                $datas[] = array(
                    'stock' => $stock['stock'] - $rowQuantity,
                );
                $this->db->where('code', $productName);
                $this->db->update('inventory', end($datas));
            }
        }
        //for end update stock in inventory table when invoice created

        $previous_balance = $this->invoice->get_previous_balance_invoice($invoice_number, $this->user_id);
        $current_balance = floatval($total_invoice_amount - $previous_balance['total_balance_amount']);


        //for advance balance
        //  $bal =$total_invoice_amount -$previous_balance['balance'];
        //        $advance = $this->invoice->get_advance_amount_by_customer_id($customer_id, $this->user_id);
        //        $rem_balance = abs($current_balance - $advance['advance_pay']);
        //        
        //         $updated_at = date("Y-m-d");
        //        if($advance['advance_pay'] > $amount){
        //            $data_advance_amt = array('advance_pay'=>$rem_balance, 'advance_pay'=>$advance['advance_pay'], 'updated_at'=>$updated_at);
        //        }  else {
        //            $data_advance_amt = array('advance_pay'=>0, 'advance_pay_now'=>$advance['advance_pay'], 'updated_at'=>$updated_at);
        //        }
        //        if($advance['advance_pay'] > $amount){
        //            $rem_balance = 0;
        //        }
        //        $this->db->where('customer_id_fk', $customer_id);
        //        $this->db->update('advance_amount', $data_advance_amt);


        $data_toatl_amount = array(
            'date' => $date,
            'total' => $total_invoice_amount,
            'balance' => $current_balance,
            'customer_id_fk' => $customer_id,
            'payment_method' => $payment_method,
            'status' => $status,
            'despatch_through' => $despatch_through,
            'vehicle_no' => $vehicle_no,
            'delivery_date' => $delivery_date,
            'delivery_note_no' => $delivery_note_no,
            'note' => $note,
            'payment_due_date' => $payment_due_date,
            'invoice_subheading' => $invoice_subheading,
            'invoice_footer' => $invoice_footer,
            'invoice_memo' => $invoice_memo,
            'invoice_terms_and_conditions' => $invoice_terms_and_conditions,
            'invoice_payment_terms' => $invoice_payment_terms,
            'invoice_exclusions' => $invoice_exclusions,
            'customer_po' => $invoice_customer_po,
            'po_date' => $invoice_po_date,
            'shipping_address' => $shipping_address,
            'sales_person' => $sales_person,
            'total_before_tax' => $total_before_tax,
            'total_gst_amount' => $total_gst_amount,
            'uid' => $this->user_id
        );

        // print_r($data_toatl_amount);die();

        $result = $this->invoice->update_invoice_total($data_toatl_amount, $invoice_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Invoice updated successfully!!");
            redirect('InvoiceController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Invoice not updated successfully!!");
            redirect('InvoiceController/index');
        }
    }

    public function get_invoice_data_by_status()
    {
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


    public function send_invoice_whatsapp()
    {
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
    public function send_invoice_email()
    {
        //get data using session to set mail properties
        $session_data_head2 = $this->session->userdata('session_data_head2');

        // print_r($session_data_head2);die();
        $set_company_name = $session_data_head2['company_name'];

        $set_company_logo = base_url() . $session_data_head2['company_logo'];


        $set_from_email = $session_data_head2['from_email'];
        //        print_r($set_from_email);die();

        $set_cc_email = $session_data_head2['cc_email'];

        $data['settings'] = $this->login->get_settings($this->user_id);

        //enddata using session to set mail properties

        $invoice_number = $this->input->post('invoice_number');

        $invoice_data_group = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);
        //  print_r($invoice_data_group);die();
        $customer_name = $invoice_data_group['fullname'];
        $issue_date = !empty($invoice_data_group['invoice_date']) ? date('d-m-Y', strtotime($invoice_data_group['invoice_date'])) : '';
        $expires_date = !empty($invoice_data_group['payment_due_date']) ? date('d-m-Y', strtotime($invoice_data_group['payment_due_date'])) : '';
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
       
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;line-height:32px"><center>Invoice</center></span><br>
            <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:32px;line-height:32px">' . $invoice_number . '</span><br>
                    
                <span style="text-decoration:none;color:#a0a0a5;">for <b>' . $customer_name . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
                <span style="text-decoration:none;color:#a0a0a5;">from <b>' . $data['settings']['company_name'] . '</b></span>
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
            <span style="color:#2f2f36; font-size:12px;"><b>Note:</b> The invoice PDF is attached to this email for your convenience.</span>
            <hr>
            <span style="text-decoration:none;color:#2f2f36;">"Thanks for your business. If this invoice was sent in error, please contact" <a href="mailto:' . $data['settings']['from_email'] . '" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">' . $data['settings']['from_email'] . '</a></span>
         </div>
          <center><a href="https://xform.in/"><span style="text-decoration:none;color:#2f2f36; ">Powered by 
      <img alt="' . $data['settings']['company_name'] . '" src="' . $data['settings']['company_logo'] . '" width="8%" height="8%" style="margin-top:3%;">
       Xform</span></a></center>
     </div>
     
    </body>
    </html>';

        // Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $data['settings']['company_name'] . ' <' . $data['settings']['from_email'] . '>' . "\r\n";

        // Generate PDF for attachment
        $pdf_filename = 'Invoice_' . str_replace('/', '_', $invoice_number) . '_' . time() . '.pdf';
        $pdf_filepath = FCPATH . 'uploads/' . $pdf_filename;
        
        try {
            // Get invoice data for PDF generation
            $show_invoice = $this->invoice->get_invoice_data($invoice_number, $this->user_id);
            
            // Create PDF using mPDF
            $mpdf = new \Mpdf\Mpdf();
            $invoice_data = array(
                'show_invoice' => $show_invoice,
                'invoice_data_group' => $invoice_data_group,
                'settings' => $data['settings'],
                'stamp' => 'no'
            );
            
            $html = $this->load->view('admin/invoice_print', $invoice_data, true);
            
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
            log_message('error', 'PDF generation failed for invoice email: ' . $e->getMessage());
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
            redirect('InvoiceController/index');
        } else {
            // Clean up temporary PDF file if sending failed
            if ($pdf_filepath && file_exists($pdf_filepath)) {
                unlink($pdf_filepath);
            }
            
            $this->session->set_flashdata('INFOMSG', "Email not Sent Successfully!!");
            redirect('InvoiceController/index');
        }
    }

    public function get_customer_email()
    {
        $invoice_number = $this->input->post('invoice_number');
        $result = $this->invoice->get_customer_email($invoice_number, $this->user_id);
        echo json_encode($result);
    }

    private function ensure_invoice_waybill_table()
    {
        if ($this->db->table_exists('invoice_waybill')) {
            return;
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `invoice_waybill` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `uid` int(11) NOT NULL,
            `invoice_number` varchar(100) NOT NULL,
            `waybill_no` varchar(12) NOT NULL,
            `generated_date` varchar(20) NOT NULL,
            `valid_upto` varchar(20) DEFAULT NULL,
            `transport_mode` varchar(30) NOT NULL,
            `transporter_name` varchar(150) DEFAULT NULL,
            `transporter_gstin` varchar(15) DEFAULT NULL,
            `vehicle_no` varchar(30) NOT NULL,
            `distance` varchar(20) DEFAULT NULL,
            `mobile` varchar(20) DEFAULT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uid_invoice_number` (`uid`, `invoice_number`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function get_invoice_waybill()
    {
        $invoice_number = trim((string) $this->input->post('invoice_number'));
        $this->ensure_invoice_waybill_table();

        $result = array();
        if ($invoice_number !== '') {
            $result = $this->db
                ->where('uid', $this->user_id)
                ->where('invoice_number', $invoice_number)
                ->get('invoice_waybill')
                ->row_array();
        }

        echo json_encode($result ? $result : array());
    }

    public function save_invoice_waybill()
    {
        $invoice_number = trim((string) $this->input->post('invoice_number'));
        $waybill_no = preg_replace('/[^0-9]/', '', (string) $this->input->post('waybill_no'));
        $generated_date = trim((string) $this->input->post('generated_date'));
        $transport_mode = trim((string) $this->input->post('transport_mode'));
        $vehicle_no = strtoupper(trim((string) $this->input->post('vehicle_no')));

        if ($invoice_number === '' || strlen($waybill_no) !== 12 || $generated_date === '' || $transport_mode === '' || $vehicle_no === '') {
            echo json_encode(array('success' => false, 'message' => 'Please fill valid E-Way Bill required fields.'));
            return;
        }

        $this->ensure_invoice_waybill_table();

        $data = array(
            'uid' => $this->user_id,
            'invoice_number' => $invoice_number,
            'waybill_no' => $waybill_no,
            'generated_date' => $generated_date,
            'valid_upto' => trim((string) $this->input->post('valid_upto')),
            'transport_mode' => $transport_mode,
            'transporter_name' => trim((string) $this->input->post('transporter_name')),
            'transporter_gstin' => strtoupper(trim((string) $this->input->post('transporter_gstin'))),
            'vehicle_no' => $vehicle_no,
            'distance' => trim((string) $this->input->post('distance')),
            'mobile' => preg_replace('/[^0-9]/', '', (string) $this->input->post('mobile')),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $existing = $this->db
            ->select('id')
            ->where('uid', $this->user_id)
            ->where('invoice_number', $invoice_number)
            ->get('invoice_waybill')
            ->row_array();

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('invoice_waybill', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('invoice_waybill', $data);
        }

        echo json_encode(array('success' => true));
    }

    public function send_invoice_reminder_email()
    {
        $session_data_head2 = $this->session->userdata('session_data_head2');

        $set_company_name = $session_data_head2['company_name'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];

        $data['settings'] = $this->login->get_settings($this->user_id);

        $invoice_number = $this->input->post('invoice_number');
        $invoice_data_group = $this->invoice->get_invoice_data_group_by($invoice_number, $this->user_id);

        $customer_name = isset($invoice_data_group['fullname']) ? $invoice_data_group['fullname'] : '';
        $issue_date = isset($invoice_data_group['invoice_date']) ? $invoice_data_group['invoice_date'] : '';
        $expires_date = isset($invoice_data_group['payment_due_date']) ? $invoice_data_group['payment_due_date'] : '';
        $issue_date_display = !empty($issue_date) ? date('d-m-Y', strtotime($issue_date)) : '';
        $expires_date_display = !empty($expires_date) ? date('d-m-Y', strtotime($expires_date)) : '';
        $grand_total = isset($invoice_data_group['total']) ? $invoice_data_group['total'] : 0;
        $balance_amount = isset($invoice_data_group['balance']) ? $invoice_data_group['balance'] : 0;

        $to_email = $this->input->post('to_email');
        $subject = trim($this->input->post('subject'));
        $message = trim($this->input->post('message'));
        $copy_email = $this->input->post('copy_email');

        if (empty($subject)) {
            $subject = 'Reminder: Invoice ' . $invoice_number . ' payment follow-up';
        }

        $due_status_text = '';
        $month_wise_text = '';
        if (!empty($expires_date)) {
            $month_wise_text = date('M-Y', strtotime($expires_date));

            $start = new DateTime(date('Y-m-d', strtotime($expires_date)));
            $end = new DateTime(date('Y-m-d'));
            $diff = $start->diff($end);

            if ($diff->invert > 0) {
                $due_after_days = $diff->days + 1;
                $due_status_text = 'Due after ' . $due_after_days . ' day(s)';
            } else {
                $due_status_text = 'Due ' . $diff->days . ' day(s) ago';
            }
        }

        if (empty($message)) {
            $message = 'This is a payment reminder for Invoice ' . $invoice_number . '.';
        }

        $user_id_send = $this->user_id;

        // Generate PDF for attachment
        $pdf_filename = 'Invoice_' . str_replace('/', '_', $invoice_number) . '_' . time() . '.pdf';
        $pdf_filepath = FCPATH . 'uploads/' . $pdf_filename;
        
        try {
            // Get invoice data for PDF generation
            $show_invoice = $this->invoice->get_invoice_data($invoice_number, $this->user_id);
            
            // Create PDF using mPDF
            $mpdf = new \Mpdf\Mpdf();
            $invoice_data = array(
                'show_invoice' => $show_invoice,
                'invoice_data_group' => $invoice_data_group,
                'settings' => $data['settings'],
                'stamp' => 'no'
            );
            
            $html = $this->load->view('admin/invoice_print', $invoice_data, true);
            
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
            log_message('error', 'PDF generation failed for reminder email: ' . $e->getMessage());
            // Continue anyway and send email with link only
            $pdf_filepath = false;
        }

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

        // Attach PDF if generated successfully
        if ($pdf_filepath && file_exists($pdf_filepath)) {
            $this->email->attach($pdf_filepath);
        }

        $htmlContent = '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title>Invoice Reminder</title>
    </head>
    <body style="background:#f8f8f8; font-family: Arial, sans-serif;">
        <div style="padding:2% 8%; margin:0 auto; max-width:720px;">
            <div style="padding:20px; border:1px solid #ddd; background:#fff; border-radius:4px;">
                <h2 style="margin-top:0; color:#2f2f36; text-align:center;">Payment Reminder</h2>
                <p style="color:#2f2f36;"><b>Invoice:</b> ' . $invoice_number . '</p>
                <p style="color:#2f2f36;"><b>Customer:</b> ' . $customer_name . '</p>
                <p style="color:#2f2f36;"><b>Invoice Date:</b> ' . $issue_date_display . '</p>
                <p style="color:#2f2f36;"><b>Due Date:</b> ' . $expires_date_display . '</p>
                <p style="color:#2f2f36;"><b>Day-wise Status:</b> ' . $due_status_text . '</p>
                <p style="color:#2f2f36;"><b>Month-wise:</b> ' . $month_wise_text . '</p>
                <p style="color:#2f2f36;"><b>Total:</b> ' . $grand_total . ' INR</p>
                <p style="color:#2f2f36;"><b>Balance:</b> ' . $balance_amount . ' INR</p>
                <hr>
                <p style="color:#2f2f36;">' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</p>
                <hr>
                <p style="text-align:center;">
                    <a href="' . base_url() . 'Pdf/download_invoice/' . $invoice_number . '/' . $user_id_send . '" style="background-color:#00929f;border-radius:4px;color:#ffffff;display:inline-block;font-size:15px;font-weight:bold;line-height:38px;text-align:center;text-decoration:none;width:220px" target="_blank">View Invoice Online</a>
                </p>
                <p style="color:#2f2f36; font-size:12px;"><b>Note:</b> The invoice PDF is attached to this email for your convenience.</p>
                <p style="color:#2f2f36; font-size:12px;">If already paid, please ignore this reminder.</p>
            </div>
        </div>
    </body>
</html>';

        $this->email->message($htmlContent);

        if ($this->email->send()) {
            // Clean up temporary PDF file after successful sending
            if ($pdf_filepath && file_exists($pdf_filepath)) {
                unlink($pdf_filepath);
            }
            
            $this->session->set_flashdata('SUCCESSMSG', "Reminder sent successfully with PDF attachment!!");
            redirect('InvoiceController/index');
        } else {
            // Clean up temporary PDF file if sending failed
            if ($pdf_filepath && file_exists($pdf_filepath)) {
                unlink($pdf_filepath);
            }
            
            $this->session->set_flashdata('INFOMSG', "Reminder not sent successfully!!");
            redirect('InvoiceController/index');
        }
    }







    public function delete_item()
    {
        $invoice_id = $this->input->post('invoice_id');
        $result = $this->invoice->delete_item($invoice_id);
        echo json_encode($result);
    }



    public function update_stock_by_item_name()
    {
        $item_name = $this->input->post('item_name');
        $quantity = $this->input->post('quantity');
        $data = array(
            'stock' => $quantity
        );
        $this->db->where('code', $item_name);
        $this->db->update('inventory', $data);
        echo 'True';
    }

    public function approve_invoice_status()
    {
        $invoice_no = $this->input->post('number_fk');
        $status = 4;
        $data = array(
            'status' => $status
        );
        $this->db->where('number_fk', $invoice_no);
        $this->db->update('invoice_total', $data);
        echo 'True';
    }






    public function duplicate_invoice()
    {

        $source_invoice_number = $this->input->post('source_invoice_number');
        $invoice_number = strtoupper(trim($this->input->post('invoice_number')));

        if ($invoice_number === '') {
            $this->session->set_flashdata('INFOMSG', "Invoice name is required!");
            redirect('InvoiceController/index');
            return;
        }

        if ($this->invoice->invoice_name_exists($invoice_number, $this->user_id)) {
            redirect('InvoiceController/show_invoice/' . $this->input->post('invoice_id') . '?duplicate_invoice=1');
            return;
        }

        $data1['show_invoice'] = $this->invoice->get_invoice_data($source_invoice_number, $this->user_id);
        //  $data['status_result'] = $this->invoice->get_status_by_invoiceid($invoice_number, $this->user_id);
        $data['invoice_data_group'] = $this->invoice->get_duplicate_invoice_data_group_by($source_invoice_number, $this->user_id);
        $data['invoice_data_group']['paid'] = 0;

        $data['invoice_data_group']['number_fk'] = $invoice_number;

        $data['invoice_data_group']['uid'] = $this->user_id;

        $this->db->trans_begin();

        try {
            foreach ($data1['show_invoice'] as $key) {

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
                $data1 = array(
                    'invoice_number' => $invoice_number,
                    'invoice_date' => date("Y-m-d"),
                    'customer_id' => $key->customer_id,
                    'product_name' => $key->product_name,
                    'quantity' => $key->quantity,
                    'unit' => $key->unit,
                    'hsn_code' => $key->hsn_code,
                    'gst' => $key->gst,
                    'discount' => $key->discount,
                    'sgst' => $sgst1,
                    'cgst' => $cgst1,
                    'igst' => $igst1,
                    'gst_type' => $key->gst_type,
                    'price' => $key->price,
                    'amount' => $key->amount,
                    'description' => $key->description,
                    'uid' => $this->user_id,
                );

                $this->db->insert('invoice', $data1);
            }
            unset($data['invoice_data_group']['id']);

            $result = $this->invoice->add_invoice_total($data['invoice_data_group']);

            if ($this->db->trans_status() === FALSE || $result != TRUE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('INFOMSG', "Invoice not submitted successfully!!");
                redirect('InvoiceController/index');
                return;
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('SUCCESSMSG', "Invoice submitted successfully!!");
            redirect('InvoiceController/index');
            return;
        } catch (\Throwable $e) {
            $this->db->trans_rollback();

            if (stripos($e->getMessage(), 'Duplicate entry') !== false) {
                redirect('InvoiceController/show_invoice/' . $this->input->post('invoice_id') . '?duplicate_invoice=1');
                return;
            }

            throw $e;
        }
    }
    public function get_datewise_record()
    {
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
        $this->load->view('invoice/view_invoice', $data);
    }

    public function get_monthyearwise_record()
    {
        $month_year = $this->input->post('month_year');
        $data['invoice_count'] = $this->invoice->get_invoice_count($this->user_id);

        $draft_status = 1;
        $data['invoice_draft_count'] = $this->invoice->get_invoice_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['invoice_sent_count'] = $this->invoice->get_invoice_draft_count($sent_status, $this->user_id);
        $data['invoices'] = $this->invoice->get_monthyearwise_record($month_year, $this->user_id);
        // print_r($data['invoices']);die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/view_invoice', $data);
    }


    function payment_in()
    {
        $data['company_name'] = $this->invoice->get_company_name_with_bal($this->user_id);

        $data['result'] = $this->invoice->get_payment_in($this->user_id);
        $data['result_by_id'] = null;
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/payment_in', $data);
    }


    function save_payment()
    {

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

        $payment_customer_id = $this->input->post('customer_id');
        $payment_bank_voucher_type = $this->input->post('bank_voucher_type');

        $data1 = array(
            'payment_customer_id' => $payment_customer_id,
            'payment' => $payment,
            'pay_balance' => $payment,
            'payment_date' => $pay_date1,
            'payment_type' => $payment_type,
            'payment_bank' => $payment_bank,
            'payment_method' => $payment_method,
            'payment_note' => $payment_note,
            'bank_voucher_type' => $payment_bank_voucher_type
        );

        if ($payment_id != "") {
            $this->db->where('payment_id', $payment_id);
            $this->db->update('payment_in', $data1);

            $this->session->set_flashdata('SUCCESSMSG', "Payment in updated successfully!!");
            redirect('InvoiceController/payment_in');
        } else {
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


    function get_pending_invoice_payment()
    {
        $customer_id_fk = $this->input->post('customer_id_fk');
        $result = $this->invoice->get_pending_invoice_payment($customer_id_fk, $this->user_id);
        echo json_encode($result);
    }


    public function getPaymentById()
    {
        $id = $this->input->get('id');
        $data['result_by_id'] = $this->invoice->getPaymentById($id);
        //var_dump($data); die();
        $data['company_name'] = $this->invoice->get_company_name_with_bal($this->user_id);
        $data['result'] = $this->invoice->get_payment_in($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('invoice/payment_in', $data);
    }

    
    function get_sales_order_po_number()
    {
        $customer_id_fk = $this->input->post('customer_id');
        $result = $this->salesorder->get_sales_order_po_number($customer_id_fk, $this->user_id);
        
        // Format response for AJAX
        $response = array('success' => false, 'data' => array());
        
        if (!empty($result)) {
            // Get all PO records for this customer
            $response['success'] = true;
            $poDataArray = array();
            
            foreach ($result as $poData) {
                $poDataArray[] = array(
                    'po_number' => isset($poData->po_number) ? $poData->po_number : '',
                    'po_date' => isset($poData->po_date) ? $poData->po_date : '',
                    'po_status' => isset($poData->po_status) ? $poData->po_status : ''
                );
            }
            
            $response['data'] = $poDataArray;
        }
        
        echo json_encode($response);
    }

    function get_po_items()
    {
        $po_number = $this->input->post('po_number');
        
        // Log input for debugging
        error_log('get_po_items called with PO: ' . $po_number . ' UID: ' . $this->user_id);
        
        $result = $this->salesorder->get_po_items($po_number, $this->user_id);
        
        // Format response for AJAX
        $response = array('success' => false, 'data' => array());
        
        // Log result for debugging
        error_log('get_po_items result: ' . print_r($result, true));
        
        if (!empty($result)) {
            $response['success'] = true;
            $itemsDataArray = array();
            
            foreach ($result as $itemData) {
                $itemsDataArray[] = array(
                    'product_name' => isset($itemData->product_name) ? $itemData->product_name : '',
                    'description' => isset($itemData->description) ? $itemData->description : '',
                    'hsn' => isset($itemData->hsn_code) ? $itemData->hsn_code : '',
                    'quantity' => isset($itemData->quantity) ? $itemData->quantity : '1',
                    'unit' => '', // Unit not directly stored in salesorder table
                    'gst_per' => isset($itemData->gst_per) ? $itemData->gst_per : '0',
                    'sgst' => isset($itemData->sgst) ? $itemData->sgst : '0',
                    'cgst' => isset($itemData->cgst) ? $itemData->cgst : '0',
                    'igst' => isset($itemData->igst) ? $itemData->igst : '0',
                    'price' => isset($itemData->price) ? $itemData->price : '0.00',
                    'discount' => isset($itemData->discount) ? $itemData->discount : '0'
                );
            }
            
            $response['data'] = $itemsDataArray;
        }
        
        echo json_encode($response);
    }

}
