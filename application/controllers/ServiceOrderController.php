<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceOrderController extends MY_Controller {

    protected $user_id;

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('serviceorder');
        $this->load->model('login');
        $this->load->model('customer');
        $this->load->model('inventory');
        $this->load->library('form_validation');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? NULL;

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    private function getDocNumberFromUri($startSegment = 3, $endSegment = 6) {
        $segments = array();
        for ($segmentIndex = $startSegment; $segmentIndex <= $endSegment; $segmentIndex++) {
            $segmentValue = $this->uri->segment($segmentIndex);
            if ($segmentValue !== NULL && $segmentValue !== '') {
                $segments[] = $segmentValue;
            }
        }
        return implode('/', $segments);
    }

    private function get_type_config($type) {
        $configs = [
            'order' => [
                'type' => 'order',
                'title' => 'Service Order',
                'prefix' => 'SO',
                'url_prefix' => 'index'
            ],
            'amc' => [
                'type' => 'amc',
                'title' => 'AMC',
                'prefix' => 'AMC',
                'url_prefix' => 'amc_index'
            ],
            'onetime' => [
                'type' => 'onetime',
                'title' => 'One Time Service',
                'prefix' => 'OTS',
                'url_prefix' => 'one_time_index'
            ],
            'foc' => [
                'type' => 'foc',
                'title' => 'FOC',
                'prefix' => 'FOC',
                'url_prefix' => 'foc_index'
            ],
            'ec' => [
                'type' => 'ec',
                'title' => 'E&C Project',
                'prefix' => 'EC',
                'url_prefix' => 'ec_project_index'
            ],
            'proforma' => [
                'type' => 'proforma',
                'title' => 'Service Proforma Invoice',
                'prefix' => 'SPI',
                'url_prefix' => 'proforma_index'
            ],
            'quotation' => [
                'type' => 'quotation',
                'title' => 'Service Quotation',
                'prefix' => 'SQ',
                'url_prefix' => 'quotation_index'
            ]
        ];
        return $configs[$type] ?? $configs['order'];
    }

    private function render_list($type) {
        $config = $this->get_type_config($type);
        $str = $this->input->get('str');
        
        if ($str == "All" || $str === null) {
            $data['service_orders'] = $this->serviceorder->get_service_orders($type, $this->user_id);
        } else {
            $month_year = date('M-Y');
            $data['service_orders'] = $this->serviceorder->get_monthyearwise_record($type, $month_year, $this->user_id);
        }

        $data['config'] = $config;
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['company_name'] = $this->serviceorder->get_company_name($this->user_id);
        $data['total_count'] = $this->serviceorder->get_service_order_count($type, $this->user_id);
        
        $data['draft_count'] = $this->serviceorder->get_service_order_status_count($type, 1, $this->user_id);
        $data['sent_count'] = $this->serviceorder->get_service_order_status_count($type, 2, $this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('service_order/view_service_orders', $data);
    }

    public function index() {
        $this->render_list('order');
    }

    public function amc_index() {
        $this->render_list('amc');
    }

    public function one_time_index() {
        $this->render_list('onetime');
    }

    public function foc_index() {
        $this->render_list('foc');
    }

    public function ec_project_index() {
        $this->render_list('ec');
    }

    public function proforma_index() {
        $this->render_list('proforma');
    }

    public function quotation_index() {
        $this->render_list('quotation');
    }

    public function get_service_order_data_by_status() {
        $type = $this->uri->segment(3);
        $status = $this->uri->segment(4);
        $config = $this->get_type_config($type);

        $data['service_orders'] = $this->serviceorder->get_service_order_data_by_status($type, $status, $this->user_id);
        $data['config'] = $config;
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['company_name'] = $this->serviceorder->get_company_name($this->user_id);
        $data['total_count'] = $this->serviceorder->get_service_order_count($type, $this->user_id);
        
        $data['draft_count'] = $this->serviceorder->get_service_order_status_count($type, 1, $this->user_id);
        $data['sent_count'] = $this->serviceorder->get_service_order_status_count($type, 2, $this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('service_order/view_service_orders', $data);
    }

    public function create_service_order() {
        $type = $this->uri->segment(3) ?? 'order';
        $config = $this->get_type_config($type);

        $data['config'] = $config;
        $data['company_name'] = $this->serviceorder->get_company_name($this->user_id);
        $data['next_seq'] = $this->serviceorder->get_last_service_order_number($type, $this->user_id) + 1;
        $data['settings'] = $this->login->get_settings($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('service_order/create_service_order', $data);
    }

    public function add_service_order() {
        $type = $this->input->post('service_type');
        $config = $this->get_type_config($type);

        $customer_id = $this->input->post('customer_id');
        $doc_number = $this->input->post('doc_number');
        $doc_date = $this->input->post('doc_date');
        $po_number = $this->input->post('po_number') ?? '';
        $customer_code = $this->input->post('customer_code') ?? '';
        $status = $this->input->post('status') ?? 1;

        $basic_total = $this->input->post('basic_total') ?? 0;
        $total = $this->input->post('total') ?? 0;

        $subheading = $this->input->post('subheading') ?? '';
        $footer = $this->input->post('footer') ?? '';
        $memo = $this->input->post('memo') ?? '';
        $terms = $this->input->post('terms') ?? '';
        $payment_terms = $this->input->post('payment_terms') ?? '';
        $transportation = $this->input->post('transportation') ?? '';
        $installation = $this->input->post('installation') ?? '';
        $process_schedule = $this->input->post('process_schedule') ?? '';
        $taxes = $this->input->post('taxes') ?? '';
        $exclusions = $this->input->post('exclusions') ?? '';

        $data_total = [
            'number_fk' => $doc_number,
            'date' => $doc_date,
            'basic_total' => $basic_total,
            'total' => $total,
            'customer_id_fk' => $customer_id,
            'status' => $status,
            'uid' => $this->user_id,
            'service_type' => $type,
            'service_order_subheading' => $subheading,
            'service_order_footer' => $footer,
            'service_order_memo' => $memo,
            'terms_and_conditions' => $terms,
            'payment_terms' => $payment_terms,
            'transportation' => $transportation,
            'installation' => $installation,
            'process_schedule' => $process_schedule,
            'taxes' => $taxes,
            'exclusions' => $exclusions,
            'po_number' => $po_number,
            'customer_code' => $customer_code
        ];

        // Save Header
        $this->serviceorder->add_service_order_total($data_total);

        // Save Line Items
        $service_name = $this->input->post('service_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $sac_code = $this->input->post('sac_code');
        $gst = $this->input->post('gst');
        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
        $igst = $this->input->post('igst');
        $gst_type = $this->input->post('gst_type'); // 'S' or 'I'
        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $discount = $this->input->post('discount');
        $description = $this->input->post('description');

        if (is_array($service_name)) {
            for ($i = 0; $i < count($service_name); $i++) {
                if (empty($service_name[$i])) continue;

                $data_item = [
                    'number' => $doc_number,
                    'customer_id' => $customer_id,
                    'service_name' => $service_name[$i],
                    'quantity' => $quantity[$i] ?? 1,
                    'unit' => $unit[$i] ?? '',
                    'sac_code' => $sac_code[$i] ?? '',
                    'gst' => $gst[$i] ?? 0,
                    'sgst' => $sgst[$i] ?? 0,
                    'cgst' => $cgst[$i] ?? 0,
                    'igst' => $igst[$i] ?? 0,
                    'gst_type' => $gst_type[$i] ?? 'S',
                    'price' => $price[$i] ?? 0,
                    'amount' => $amount[$i] ?? 0,
                    'discount' => $discount[$i] ?? 0,
                    'description' => $description[$i] ?? '',
                    'uid' => $this->user_id,
                    'service_type' => $type
                ];
                $this->serviceorder->add_service_order($data_item);
            }
        }

        $this->session->set_flashdata('SUCCESSMSG', "{$config['title']} created successfully!");
        redirect("ServiceOrderController/{$config['url_prefix']}");
    }

    public function edit_service_order_details() {
        $doc_number = $this->getDocNumberFromUri();
        $type = $this->uri->segment(3); // Wait, if the segment structure is: edit_service_order_details/SO/0001/26-27
        // Then getDocNumberFromUri() returns the full doc number "SO/0001/26-27"
        // Let's see: we should make sure we can find the type from the header record
        $data['invoice_data_group'] = $this->serviceorder->get_service_order_data_group_by($doc_number, $this->user_id);

        if (empty($data['invoice_data_group'])) {
            $this->session->set_flashdata('INFOMSG', 'Service document not found: ' . htmlspecialchars($doc_number));
            redirect('ServiceOrderController/index');
            return;
        }

        $type = $data['invoice_data_group']['service_type'];
        $config = $this->get_type_config($type);

        $data['config'] = $config;
        $data['show_invoice'] = $this->serviceorder->get_service_order_data($doc_number, $this->user_id);
        $data['customer_result'] = $this->serviceorder->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('service_order/edit_service_order', $data);
    }

    public function edit_service_order() {
        $doc_number = $this->input->post('doc_number');
        $type = $this->input->post('service_type');
        $config = $this->get_type_config($type);

        $customer_id = $this->input->post('customer_id');
        $doc_date = $this->input->post('doc_date');
        $po_number = $this->input->post('po_number') ?? '';
        $customer_code = $this->input->post('customer_code') ?? '';
        $status = $this->input->post('status') ?? 1;

        $basic_total = $this->input->post('basic_total') ?? 0;
        $total = $this->input->post('total') ?? 0;

        $subheading = $this->input->post('subheading') ?? '';
        $footer = $this->input->post('footer') ?? '';
        $memo = $this->input->post('memo') ?? '';
        $terms = $this->input->post('terms') ?? '';
        $payment_terms = $this->input->post('payment_terms') ?? '';
        $transportation = $this->input->post('transportation') ?? '';
        $installation = $this->input->post('installation') ?? '';
        $process_schedule = $this->input->post('process_schedule') ?? '';
        $taxes = $this->input->post('taxes') ?? '';
        $exclusions = $this->input->post('exclusions') ?? '';

        $data_total = [
            'date' => $doc_date,
            'basic_total' => $basic_total,
            'total' => $total,
            'customer_id_fk' => $customer_id,
            'status' => $status,
            'service_order_subheading' => $subheading,
            'service_order_footer' => $footer,
            'service_order_memo' => $memo,
            'terms_and_conditions' => $terms,
            'payment_terms' => $payment_terms,
            'transportation' => $transportation,
            'installation' => $installation,
            'process_schedule' => $process_schedule,
            'taxes' => $taxes,
            'exclusions' => $exclusions,
            'po_number' => $po_number,
            'customer_code' => $customer_code
        ];

        // Update Header
        $this->serviceorder->update_service_order_total($doc_number, $data_total);

        // Delete existing items and insert new ones
        $this->serviceorder->delete_service_order_items($doc_number);

        // Save Line Items
        $service_name = $this->input->post('service_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $sac_code = $this->input->post('sac_code');
        $gst = $this->input->post('gst');
        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
        $igst = $this->input->post('igst');
        $gst_type = $this->input->post('gst_type');
        $price = $this->input->post('price');
        $amount = $this->input->post('amount');
        $discount = $this->input->post('discount');
        $description = $this->input->post('description');

        if (is_array($service_name)) {
            for ($i = 0; $i < count($service_name); $i++) {
                if (empty($service_name[$i])) continue;

                $data_item = [
                    'number' => $doc_number,
                    'customer_id' => $customer_id,
                    'service_name' => $service_name[$i],
                    'quantity' => $quantity[$i] ?? 1,
                    'unit' => $unit[$i] ?? '',
                    'sac_code' => $sac_code[$i] ?? '',
                    'gst' => $gst[$i] ?? 0,
                    'sgst' => $sgst[$i] ?? 0,
                    'cgst' => $cgst[$i] ?? 0,
                    'igst' => $igst[$i] ?? 0,
                    'gst_type' => $gst_type[$i] ?? 'S',
                    'price' => $price[$i] ?? 0,
                    'amount' => $amount[$i] ?? 0,
                    'discount' => $discount[$i] ?? 0,
                    'description' => $description[$i] ?? '',
                    'uid' => $this->user_id,
                    'service_type' => $type
                ];
                $this->serviceorder->add_service_order($data_item);
            }
        }

        $this->session->set_flashdata('SUCCESSMSG', "{$config['title']} updated successfully!");
        redirect("ServiceOrderController/{$config['url_prefix']}");
    }

    public function show_service_order() {
        $doc_number = $this->getDocNumberFromUri();
        $data['invoice_data_group'] = $this->serviceorder->get_service_order_data_group_by($doc_number, $this->user_id);

        if (empty($data['invoice_data_group'])) {
            $this->session->set_flashdata('INFOMSG', 'Service document not found: ' . htmlspecialchars($doc_number));
            redirect('ServiceOrderController/index');
            return;
        }

        $type = $data['invoice_data_group']['service_type'];
        $config = $this->get_type_config($type);

        $data['config'] = $config;
        $data['show_invoice'] = $this->serviceorder->get_service_order_data($doc_number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('service_order/show_service_order', $data);
    }

    public function print_service_order() {
        $doc_number = $this->getDocNumberFromUri();
        $data['invoice_data_group'] = $this->serviceorder->get_service_order_data_group_by($doc_number, $this->user_id);

        if (empty($data['invoice_data_group'])) {
            $this->session->set_flashdata('INFOMSG', 'Service document not found: ' . htmlspecialchars($doc_number));
            redirect('ServiceOrderController/index');
            return;
        }

        $type = $data['invoice_data_group']['service_type'];
        $config = $this->get_type_config($type);

        $data['config'] = $config;
        $data['show_invoice'] = $this->serviceorder->get_service_order_data($doc_number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        $this->load->view('service_order/print_service_order', $data);
    }

    public function delete_service_order() {
        $doc_number = $this->getDocNumberFromUri();
        // Get service type before delete to redirect correctly
        $data = $this->serviceorder->get_service_order_data_group_by($doc_number, $this->user_id);
        if ($data) {
            $type = $data['service_type'];
            $config = $this->get_type_config($type);
            $this->serviceorder->delete_service_order($doc_number);
            $this->serviceorder->delete_service_order_items($doc_number);
            $this->session->set_flashdata('SUCCESSMSG', "{$config['title']} deleted successfully!");
            redirect("ServiceOrderController/{$config['url_prefix']}");
        } else {
            $this->session->set_flashdata('INFOMSG', "Document not found!");
            redirect("ServiceOrderController/index");
        }
    }
}
