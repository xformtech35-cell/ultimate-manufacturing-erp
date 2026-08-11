<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class BomController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('bom', '', TRUE);
        $this->load->model('units', '', TRUE);
        $this->load->model('salesorder', '', TRUE);
        $this->load->model('inventory', '', TRUE);

        $session_data_head = $this->session->userdata('session_data_head');
        // Use the actual logged-in user's ID (not hardcoded 1)
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
 
        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function create_gst_bom() {
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $data['salesorder_list'] = $this->salesorder->get_so_list_for_bom($this->user_id);
        $data['bom_id'] = $this->bom->get_last_bom_number($this->user_id);
        $data['company_name'] = $this->bom->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/create_gst_bom', $data);
    }

    public function create_igst_bom() {
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $data['salesorder_list'] = $this->salesorder->get_so_list_for_bom($this->user_id);
        $data['bom_id'] = $this->bom->get_last_bom_number($this->user_id);
        $data['company_name'] = $this->bom->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/create_igst_bom', $data);
    }

    public function add_customer() {
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
        $state_code = $this->input->post('state_code');
        $gst_check_customer = $this->input->post('gst_check_customer');
        
        $data_customer = array(
            'company_name' => $company_name, 
            'fullname' => $fullname,
            'pancard' => $pancard, 
            'gst' => $gst, 
            'email' => $email,
            'mobile' => $mobile, 
            'address' => $address, 
            'state_code' => $state_code,
            'uid' => $this->user_id
        );
        
        $result = $this->bom->customer_check($company_name, $this->user_id);

        if ($result == FALSE) {
            $this->customer->add_customer($data_customer);
            $this->session->set_flashdata('SUCCESSMSG', "Company added successfully!!");
            redirect('BomController/create_gst_bom');
        } else {
            $this->session->set_flashdata('INFOMSG', "Company already exist!!");
            redirect('BomController/create_gst_bom');
        }
    }

    public function add_product()
    {
        header('Content-Type: application/json');
        
        $item_code = $this->input->post('item_code');
        $item_name = $this->input->post('item_name');
        $unit      = $this->input->post('unit');
        $hsn       = $this->input->post('hsn');
        $hsn       = !empty($hsn) ? intval($hsn) : 0;
        $gst_per   = $this->input->post('gst_per');
        $desc      = $this->input->post('description');
        
        if (empty($item_code) || empty($item_name)) {
            echo json_encode(array('success' => false, 'error' => 'Item Code and Item Name are required'));
            return;
        }
        
        // Check if item code already exists
        $existing = $this->db->where('code', $item_code)->get('inventory')->num_rows();
        if ($existing > 0) {
            echo json_encode(array('success' => false, 'error' => 'Item Code already exists'));
            return;
        }
        
        // Format GST if not having '%'
        if (!empty($gst_per) && strpos($gst_per, '%') === false) {
            $gst_per .= '%';
        }
        
        $date_added = date("Y-m-d");
        $date_updated = date("d-m-Y");
        
        $data_inventory = array(
            'item_name'        => $item_name,
            'prod_description' => $desc,
            'code'             => $item_code,
            'hsn'              => $hsn,
            'unit'             => $unit,
            'gst_per'          => $gst_per,
            'stock'            => 0,
            'cost_price'       => 0,
            'sell_price'       => 0,
            'date_added'       => $date_added,
            'date_modified'    => $date_updated,
            'uid'              => $this->user_id
        );
        
        $insert = $this->db->insert('inventory', $data_inventory);
        if ($insert) {
            echo json_encode(array('success' => true, 'message' => 'Product added successfully'));
        } else {
            echo json_encode(array('success' => false, 'error' => 'Failed to save product in database'));
        }
    }

    public function get_product_details_by_code() {
        $product_code = $this->input->post('product_code');
        
        if (!$product_code) {
            echo json_encode(['error' => 'Product code is required']);
            return;
        }
        
        // Fetch product details from inventory model
        $this->db->select('*');
        $this->db->from('inventory');
        $this->db->where('code', $product_code);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $product = $query->row_array();
            echo json_encode([
                'success' => true,
                'product_code' => $product['code'],
                'product_name' => $product['item_name'],
                'unit' => isset($product['unit']) ? $product['unit'] : '',
                'description' => isset($product['description']) ? $product['description'] : '',
                'stock' => isset($product['stock']) ? $product['stock'] : 0
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Product not found']);
        }
    }

    /**
     * NEW: Fetch SO details by SO Number (number_fk) for the BOM SO selector.
     * Called by the new #bom_so_number_select dropdown on BOM create/edit pages.
     */
    public function ajax_get_so_details_by_number() {
        header('Content-Type: application/json');
        $so_number = $this->input->post('so_number');
        if (!$so_number) {
            echo json_encode(array('success' => false, 'message' => 'SO Number is required'));
            return;
        }

        // Fetch salesorder_total record by number_fk
        $this->db->select('st.number_fk, st.customer_id_fk, st.system, st.location, st.capacity, st.oc_number, st.project_qty, c.c_code, c.company_name as cname, c.customer_id');
        $this->db->from('salesorder_total st');
        $this->db->join('customer c', 'c.customer_id = st.customer_id_fk', 'left');
        $this->db->where('st.number_fk', $so_number);
        $this->db->order_by('st.id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() === 0) {
            echo json_encode(array('success' => false, 'message' => 'Sales Order not found: ' . $so_number));
            return;
        }

        $row         = $query->row_array();
        $customer_id = $row['customer_id'];

        // Helper: extract initials
        $getInitials = function($str) {
            if (empty($str)) return '';
            $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', $str);
            $words = preg_split('/\s+/', trim($clean));
            $ini = '';
            foreach ($words as $w) { if (!empty($w)) $ini .= substr($w, 0, 1); }
            return strtoupper($ini);
        };

        $system_name = $row['system'] ?? '';
        $location    = $row['location'] ?? '';
        $capacity    = $row['capacity'] ?? '';
        $project_qty = $row['project_qty'] ?? '';
        // If no dedicated oc_number stored, use the SO number itself as the OC reference
        $oc_number   = !empty($row['oc_number']) ? $row['oc_number'] : $so_number;
        $client_code = !empty($row['cname']) ? $getInitials($row['cname']) : strtoupper(trim($row['c_code'] ?? ''));

        // Get SO line items
        $so_items   = $this->salesorder->get_salesorders_data($so_number, $this->user_id);
        $items_list = array();
        if (!empty($so_items)) {
            foreach ($so_items as $item) {
                $items_list[] = array(
                    'product_name' => !empty($item->item_name) ? $item->item_name : $item->product_name,
                    'product_code' => $item->product_name,
                    'quantity'     => $item->quantity,
                    'unit'         => !empty($item->unit) ? $item->unit : ''
                );
            }
        }

        // Calculate matching BOM number matching SO sequence (e.g. SO 162 -> BOM/00162/26-27)
        $so_seq = null;
        if (preg_match('/(\d+)$/', $so_number, $matches)) {
            $so_seq = intval($matches[1]);
        }
        $financial_year = (date('m') <= 3) ? (date('y') - 1) . '-' . date('y') : date('y') . '-' . (date('y') + 1);
        $suggested_bom_number = $so_seq ? ('BOM/' . sprintf('%05d', $so_seq) . '/' . $financial_year) : '';

        echo json_encode(array(
            'success'              => true,
            'customer_id'          => $customer_id,
            'customer_code'        => $client_code,
            'company_name'         => $row['cname'] ?? '',
            'system'               => $system_name,
            'location'             => $location,
            'capacity'             => $capacity,
            'project_qty'          => $project_qty,
            'oc_number'            => $oc_number,
            'so_number'            => $so_number,
            'suggested_bom_number' => $suggested_bom_number,
            'items'                => $items_list
        ));
    }

    public function ajax_get_sales_order_details() {
        header('Content-Type: application/json');
        $project_code = $this->input->post('project_code');
        if (!$project_code) {
            echo json_encode(array('success' => false, 'message' => 'Project code is required'));
            return;
        }

        // ── Settings: company prefix + financial year ────────────────
        $settings       = $this->login->get_settings($this->user_id);
        $company_name   = $settings['company_name'] ?? 'UWS';
        $words          = preg_split('/[\s\-]+/', trim($company_name));
        $company_prefix = strtoupper(substr($words[0] ?? 'UWS', 0, 5));

        $month      = (int)date('m');
        $fy_start   = ($month <= 3) ? ((int)date('y') - 1) : (int)date('y');
        $fy_end     = $fy_start + 1;
        $financial_year = sprintf('%02d%02d', $fy_start, $fy_end); // e.g. "2526"

        // ── Helper: next sequential OC number from bom_total (global — all users) ──
        $all_oc  = $this->db->select('oc_number')->from('bom_total')
                             ->get()->result();
        $max_seq = 0;
        foreach ($all_oc as $r) {
            if (!empty($r->oc_number) && preg_match('/-OC-(\d+)$/i', $r->oc_number, $m)) {
                $seq = (int)$m[1];
                if ($seq > $max_seq) $max_seq = $seq;
            }
        }
        $next_seq = $max_seq + 1;

        // ── Helper: extract initials ──────────────────────────────────
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

        // ── Query salesorder_total — correct columns: number_fk, customer_id_fk ─
        $this->db->select('st.number_fk, st.customer_id_fk, st.system, st.location, st.capacity, st.oc_number, st.project_qty, c.c_code, c.company_name as cname, p.system as p_system');
        $this->db->from('salesorder_total st');
        $this->db->join('customer c', 'c.customer_id = st.customer_id_fk', 'left');
        $this->db->join('project p', 'p.project_code = st.project_code', 'left');
        $this->db->where('st.project_code', $project_code);
        $this->db->order_by('st.id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        $customer_id = '';
        $client_code = '';
        $system_name = '';
        $system_code = '';

        if ($query->num_rows() > 0) {
            $row         = $query->row_array();
            $customer_id = $row['customer_id_fk'];
            $client_code = !empty($row['cname']) ? $getInitials($row['cname']) : (strtoupper(trim($row['c_code'] ?? '')));
            $so_number   = $row['number_fk'] ?? '';
            $system_name = !empty($row['system']) ? $row['system'] : ($row['p_system'] ?? '');
            $system_code = $getInitials($system_name);
            $location    = $row['location'] ?? '';
            $capacity    = $row['capacity'] ?? '';
            $project_qty = $row['project_qty'] ?? '';
            $so_oc_number = $row['oc_number'] ?? '';

            // Get SO items using the SO number (number_fk)
            $so_items   = $this->salesorder->get_salesorders_data($so_number, $this->user_id);
            $items_list = array();
            if (!empty($so_items)) {
                foreach ($so_items as $item) {
                    $items_list[] = array(
                        'product_name' => !empty($item->item_name) ? $item->item_name : $item->product_name,
                        'product_code' => $item->product_name,
                        'quantity'     => $item->quantity,
                        'unit'         => !empty($item->unit) ? $item->unit : ''
                    );
                }
            }

            $display_oc = !empty($so_oc_number) ? $so_oc_number : $so_number;

            // ── Build partial OC number: UWS-2526-[SYS]-PIL-OC-1 ─────
            if (!empty($so_oc_number)) {
                $auto_oc = $so_oc_number;
            } else {
                $auto_oc = $company_prefix . '-' . $financial_year
                         . '-' . ($system_code ?: 'XX')
                         . '-' . ($client_code ?: 'XXX')
                         . '-OC-' . $next_seq;
            }

            echo json_encode(array(
                'success'        => true,
                'customer_id'    => $customer_id,
                'customer_code'  => $client_code,
                'oc_number'      => $display_oc,
                'auto_oc_number' => $auto_oc,
                'company_prefix' => $company_prefix,
                'financial_year' => $financial_year,
                'system_name'    => $system_name,
                'system_code'    => $system_code,
                'location'       => $location,
                'capacity'       => $capacity,
                'project_qty'    => $project_qty,
                'client_code'    => $client_code,
                'next_seq'       => $next_seq,
                'items'          => $items_list
            ));

        } else {
            // ── Fallback: look in project table ─────────────────────────
            $project = $this->db->where('project_code', $project_code)->get('project')->row_array();
            if ($project && !empty($project['organisation_name'])) {
                $customer = $this->db->where('company_name', $project['organisation_name'])
                                     ->get('customer')->row_array();
                if (!$customer) {
                    $customer = $this->db->like('company_name', $project['organisation_name'])
                                         ->get('customer')->row_array();
                }
                if ($customer) {
                    $client_code = strtoupper(trim($customer['c_code'] ?? ''));
                    $auto_oc = $company_prefix . '-' . $financial_year
                             . '-XX-' . ($client_code ?: 'XXX')
                             . '-OC-' . $next_seq;

                    echo json_encode(array(
                        'success'        => true,
                        'customer_id'    => $customer['customer_id'],
                        'customer_code'  => $client_code,
                        'oc_number'      => '',
                        'auto_oc_number' => $auto_oc,
                        'company_prefix' => $company_prefix,
                        'financial_year' => $financial_year,
                        'system_name'    => '',
                        'system_code'    => '',
                        'location'       => '',
                        'capacity'       => '',
                        'project_qty'    => '',
                        'client_code'    => $client_code,
                        'next_seq'       => $next_seq,
                        'items'          => array()
                    ));
                    return;
                }
            }
            echo json_encode(array('success' => false, 'message' => 'No linked Sales Order or Project found for: ' . $project_code));
        }
    }



    public function ajax_get_projects_by_customer() {
        header('Content-Type: application/json');
        $customer_id = $this->input->post('customer_id');
        if (!$customer_id) {
            echo json_encode(array('success' => false, 'message' => 'Customer ID is required'));
            return;
        }

        // Get customer's company name
        $customer = $this->db->select('company_name')
                             ->where('customer_id', $customer_id)
                             ->get('customer')
                             ->row_array();

        $company_name = $customer ? $customer['company_name'] : '';

        $projects = array();

        // 1. From salesorder_total
        $so_projects = $this->db->select('DISTINCT(project_code)')
                                ->where('customer_id_fk', $customer_id)
                                ->where('project_code !=', '')
                                ->get('salesorder_total')
                                ->result_array();
        foreach ($so_projects as $p) {
            $projects[$p['project_code']] = $p['project_code'];
        }

        // 2. From project table matching organisation_name
        if (!empty($company_name)) {
            $proj_list = $this->db->select('project_code')
                                  ->where('organisation_name', $company_name)
                                  ->where('project_code !=', '')
                                  ->get('project')
                                  ->result_array();
            foreach ($proj_list as $p) {
                $projects[$p['project_code']] = $p['project_code'];
            }
        }

        $result_list = array();
        foreach ($projects as $code) {
            $result_list[] = array('project_code' => $code);
        }

        echo json_encode(array(
            'success' => true,
            'projects' => $result_list
        ));
    }

    /**
     * AJAX: Returns data needed to auto-generate the OC number
     * Format: PREFIX-YYRR-SYS-CLI-OC-NNN  e.g. UWS-2526-DS-PIL-OC-242
     * Returns: company_prefix, financial_year, next_seq
     */
    public function get_next_oc_number() {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false]);
            return;
        }

        $settings = $this->login->get_settings($this->user_id);

        // Company prefix: first word of company name, max 5 chars uppercase
        $company_name = $settings['company_name'] ?? 'UWS';
        $words = preg_split('/[\s\-]+/', trim($company_name));
        $company_prefix = strtoupper(substr($words[0] ?? 'UWS', 0, 5));

        // Financial year: if month Jan-Mar, still in previous FY
        $month = (int)date('m');
        if ($month <= 3) {
            $fy_start = (int)date('y') - 1;
        } else {
            $fy_start = (int)date('y');
        }
        $fy_end = $fy_start + 1;
        $financial_year = sprintf('%02d%02d', $fy_start, $fy_end); // e.g. "2526"

        // Find max sequential number from existing OC numbers in bom_total (global — all users)
        $this->db->select('oc_number');
        $this->db->from('bom_total');
        $this->db->where('oc_number !=', '');
        $this->db->where('oc_number IS NOT NULL', null, false);
        $query = $this->db->get();
        $rows  = $query->result();

        $max_seq = 0;
        foreach ($rows as $row) {
            if (!empty($row->oc_number) && preg_match('/-OC-(\d+)$/i', $row->oc_number, $m)) {
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

    /**
     * AJAX Endpoint: Fetch Sales Order details when an SO is selected in Create BOM form
     */
    public function get_salesorder_details()
    {
        $so_number = trim($this->input->post('so_number') ?? $this->input->get('so_number') ?? '');

        if (empty($so_number)) {
            echo json_encode(['success' => false, 'message' => 'No SO number provided']);
            return;
        }

        // Fetch salesorder_total header
        $so_total = $this->db
            ->select('salesorder_total.*, customer.company_name, customer.c_code')
            ->from('salesorder_total')
            ->join('customer', 'customer.customer_id = salesorder_total.customer_id_fk', 'left')
            ->where('salesorder_total.number_fk', $so_number)
            ->get()
            ->row_array();

        if (!$so_total) {
            echo json_encode(['success' => false, 'message' => 'Sales Order not found']);
            return;
        }

        // Fetch item details for system/location/capacity if available
        $so_item = $this->db
            ->get_where('salesorder', ['number' => $so_number])
            ->row_array();

        // Check if an existing BOM exists for this SO number
        $existing_bom = $this->db
            ->select('number_fk, send_to_mrp')
            ->from('bom_total')
            ->where('oc_number', $so_number)
            ->order_by('id', 'DESC')
            ->get()
            ->row_array();

        $suggested_bom_number = '';
        $is_revision = false;
        if ($existing_bom) {
            if ($existing_bom['send_to_mrp'] == 2) {
                // MRP has run -> suggest next revision /R1, /R2, etc.
                $is_revision = true;
                $base_bom = preg_replace('/(?:\/R|-R)\d+$/i', '', $existing_bom['number_fk']);
                $revs = $this->db->select('number_fk')->like('number_fk', $base_bom)->get('bom_total')->result_array();
                $max_r = 0;
                foreach ($revs as $rv) {
                    if (preg_match('/(?:\/R|-R)(\d+)$/i', $rv['number_fk'], $m)) {
                        if (intval($m[1]) > $max_r) $max_r = intval($m[1]);
                    }
                }
                $suggested_bom_number = $base_bom . '/R' . ($max_r + 1);
            } else {
                $suggested_bom_number = $existing_bom['number_fk'];
            }
        } else {
            // Suggest new BOM number matching SO sequence if applicable
            $seq = 0;
            if (preg_match('/-OC-(\d+)$/i', $so_number, $m)) {
                $seq = (int)$m[1];
            } elseif (preg_match('/(\d+)$/', $so_number, $m)) {
                $seq = (int)$m[1];
            }

            // FY string
            $m_curr = (int)date('m');
            $fy_s = ($m_curr <= 3) ? ((int)date('y') - 1) : (int)date('y');
            $fy_str = sprintf('%02d-%02d', $fy_s, $fy_s + 1);

            if ($seq > 0) {
                $suggested_bom_number = sprintf('BOM/%05d/%s', $seq, $fy_str);
            } else {
                $suggested_bom_number = $this->bom->get_last_bom_number($this->user_id);
            }
        }

        echo json_encode([
            'success'              => true,
            'so_number'            => $so_total['number_fk'],
            'customer_id'          => $so_total['customer_id_fk'],
            'customer_name'        => $so_total['company_name'] ?? '',
            'customer_code'        => $so_total['c_code'] ?? '',
            'project_code'         => $so_total['project_code'] ?? '',
            'oc_number'            => $so_total['number_fk'],
            'system'               => $so_item['product_name'] ?? '',
            'location'             => $so_item['description'] ?? '',
            'capacity'             => $so_item['unit'] ?? '',
            'project_qty'          => $so_item['quantity'] ?? 1,
            'suggested_bom_number' => $suggested_bom_number,
            'is_revision'          => $is_revision
        ]);
    }

public function add_bom_bom() {
    $session_data_head = $this->session->userdata('session_data_head');
    // Check if this is an edit operation
    $edit_bom = $this->input->post('edit_bom');
    
    // Header fields (stored in bom_total)
    $customer_id = $this->input->post('customer_id');
    $number = $this->input->post('number');
    $project_code = $this->input->post('project_code') ?? '';
    $customer_code = $this->input->post('customer_code') ?? '';
    $project_qty = $this->input->post('project_qty') ?? '';
    $date = $this->input->post('date');
    $status_main = $this->input->post('status_main');
    $system = $this->input->post('system') ?? '';
    $location = $this->input->post('location') ?? '';
    $capacity = $this->input->post('capacity') ?? '';
    $oc_number = strtoupper(trim($this->input->post('oc_number') ?? ''));

    $note = $this->input->post('note');

    // Item fields - MATCHING YOUR EXACT DATABASE COLUMN NAMES
    $product_name = $this->input->post('product_name'); // This maps to 'product_name' column in DB
    $quantity = $this->input->post('quantity');
    $description = $this->input->post('description'); // This maps to 'description' column in DB
    $unit = $this->input->post('unit');
    $tag_no = $this->input->post('tag_no');
    $scope = $this->input->post('scope');
    $stores_remark = $this->input->post('stores_remark');
    $remark = $this->input->post('remark'); // This maps to 'remark' column in DB
    $status = $this->input->post('status_i'); // Individual item status

    // Ensure arrays
    $product_name = is_array($product_name) ? $product_name : [];
    $quantity = is_array($quantity) ? $quantity : [];
    $description = is_array($description) ? $description : [];
    $unit = is_array($unit) ? $unit : [];
    $tag_no = is_array($tag_no) ? $tag_no : [];
    $scope = is_array($scope) ? $scope : [];
    $stores_remark = is_array($stores_remark) ? $stores_remark : [];
    $remark = is_array($remark) ? $remark : [];
    $status = is_array($status) ? $status : [];

    $item_count = count($product_name);
    
    // Check if bom_total record exists or if this SO already has an existing MRP-run BOM
    $bom_total_exists = $this->db
        ->where('number_fk', $number)
        ->get('bom_total')
        ->row();

    // Check if an existing BOM for this OC/SO number has already had MRP RUN executed
    $so_mrp_run_bom = null;
    if (!empty($oc_number) && $edit_bom != 'edit_bom') {
        $so_mrp_run_bom = $this->db
            ->where('oc_number', $oc_number)
            ->where('send_to_mrp', 2)
            ->order_by('id', 'DESC')
            ->get('bom_total')
            ->row();
    }

    $mrp_has_run = false;
    if (($edit_bom == 'edit_bom' && $bom_total_exists && $bom_total_exists->send_to_mrp == 2) || $so_mrp_run_bom) {
        $target_bom = $so_mrp_run_bom ? $so_mrp_run_bom : $bom_total_exists;
        $mrp_has_run = true;
        
        // Extract base BOM number (e.g. "BOM/00163/26-27" from "BOM/00163/26-27/R1" or "BOM/00163/26-27")
        $base_bom_number = preg_replace('/(?:\/R|-R)\d+$/i', '', $target_bom->number_fk);
        
        // Query DB for highest revision number for this base BOM
        $existing_revs = $this->db->select('number_fk')
                                  ->like('number_fk', $base_bom_number)
                                  ->get('bom_total')
                                  ->result_array();
        
        $max_rev = 0;
        foreach ($existing_revs as $er) {
            if (preg_match('/(?:\/R|-R)(\d+)$/i', $er['number_fk'], $matches)) {
                $r = intval($matches[1]);
                if ($r > $max_rev) {
                    $max_rev = $r;
                }
            }
        }
        
        $next_rev = $max_rev + 1;
        $new_number = $base_bom_number . '/R' . $next_rev;
        
        $number = $new_number;
        $bom_total_exists = null; // Force insert instead of update/delete
        $status_main = 1; // Reset new revision to Draft status for re-approval
    }

    // Parse date from dd-mm-yy format to Y-m-d
    // Parse date from dd-mm-yyyy format to Y-m-d
$date_parts = explode('-', $date);
if (count($date_parts) == 3) {
    // Check if year has 4 digits
    if (strlen($date_parts[2]) == 4) {
        $date_formatted = $date_parts[2] . "-" . $date_parts[1] . "-" . $date_parts[0];
    } else {
        // Handle 2-digit year format
        $date_formatted = "20" . $date_parts[2] . "-" . $date_parts[1] . "-" . $date_parts[0];
    }
} else {
    $date_formatted = date("Y-m-d");
}
    
    $logged_in_uid = $session_data_head['result']['user_id'] ?? $this->user_id;
    
    // Prepare header data for bom_total
    $data_total = array(
        'customer_id_fk' => $customer_id, 
        'number_fk' => $number, 
        'date' => $date_formatted,
        'status' => $status_main, 
        'uid' => $logged_in_uid,
        'note' => $note,
        'project_code' => $project_code,
        'customer_code' => $customer_code,
        'system' => $system,
        'location' => $location,
        'capacity' => $capacity,
        'project_qty' => $project_qty,
        'oc_number' => $oc_number,
        // Set default values for required fields
        'basic_total' => 0,
        'total' => 0,
        'payment_method' => 0,
        'enquiry' => 0,
        'terms_and_conditions' => '',
        'payment_terms' => '',
        'process_schedule' => '',
        'taxes' => '',
        'exclusions' => '',
        'bom_subheading' => '',
        'bom_footer' => '',
        'approved_by' => 0,
        'po_number' => ''
    );

    // Insert or Update bom_total
    if($bom_total_exists) {
        if ($edit_bom == 'edit_bom') {
            // Update existing bom_total (edit mode)
            $this->db->where('number_fk', $number);
            $this->db->update('bom_total', $data_total);
            
            // For edit, delete existing items first
            $this->bom->delete_bom_by_bom_number($number, $this->user_id);
        } else {
            // NEW BOM but number already exists - auto-find next unique number (safety guard)
            $financial_year = '';
            if (date('m') <= 3) {
                $financial_year = (date('y') - 1) . '-' . date('y');
            } else {
                $financial_year = date('y') . '-' . (date('y') + 1);
            }
            $seq = 1;
            do {
                $try_number = 'BOM/' . sprintf('%04d', $seq) . '/' . $financial_year;
                $check = $this->db->where('number_fk', $try_number)->count_all_results('bom_total');
                $seq++;
            } while ($check > 0 && $seq < 9999);
            $number = $try_number;
            $data_total['number_fk'] = $number;
            $data_total['send_to_mrp'] = 0;
            $this->db->insert('bom_total', $data_total);
        }
    } else {
        // Insert new bom_total
        $data_total['send_to_mrp'] = 0;
        $this->db->insert('bom_total', $data_total);
    }
    
    // Insert BOM items - MATCHING YOUR EXACT DATABASE COLUMNS
    for ($i = 0; $i < $item_count; $i++) {
        if (!empty($product_name[$i]) && (!empty($quantity[$i]) || $product_name[$i] === '__HEADING__')) {
            $data = array(
                'customer_id' => $customer_id,
                'number' => $number,
                'product_name' => $product_name[$i], // From product_name field
                'quantity' => isset($quantity[$i]) ? $quantity[$i] : 0,
                'description' => isset($description[$i]) ? $description[$i] : '', // From description field
                'unit' => isset($unit[$i]) ? $unit[$i] : '',
                'tag_no' => isset($tag_no[$i]) ? $tag_no[$i] : '',
                'scope' => isset($scope[$i]) ? $scope[$i] : '',
                'stores_remark' => isset($stores_remark[$i]) ? $stores_remark[$i] : '',
                'remark' => isset($remark[$i]) ? $remark[$i] : '', // This maps to remark column
                'status_i' => isset($status[$i]) ? $status[$i] : $status_main,
                'uid' => $this->user_id,
                // Set default values for required fields
                'item_no' => '',
                'drawing_no' => '',
                'size' => '',
                'moc' => 0
            );
            
            $this->db->insert('bom', $data);
        }
    }
    
    if($edit_bom == 'edit_bom') {
        if (isset($mrp_has_run) && $mrp_has_run) {
            $this->session->set_flashdata('SUCCESSMSG', "BOM updated as a new revision: " . $number . " (MRP has already been run for the previous version)!!");
        } else {
            $this->session->set_flashdata('SUCCESSMSG', "BOM updated successfully!!");
        }
    } else {
        $this->session->set_flashdata('SUCCESSMSG', "BOM added successfully!!");
    }
    
    redirect('BomController/index');
}
    public function index() {
        $data['bom_count'] = $this->bom->get_bom_count($this->user_id);
        $draft_status = 1;
        $data['bom_draft_count'] = $this->bom->get_bom_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['bom_sent_count'] = $this->bom->get_bom_draft_count($sent_status, $this->user_id);
        $data['boms'] = $this->bom->get_boms($this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['bom_id'] = $this->bom->get_last_bom_number($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/view_bom', $data);
    }

    public function get_datewise_record() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['bom_count'] = $this->bom->get_bom_count($this->user_id);
        $data['bom_draft_count'] = $this->bom->get_bom_draft_count(1, $this->user_id);
        $data['bom_sent_count'] = $this->bom->get_bom_draft_count(2, $this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['bom_id'] = $this->bom->get_last_bom_number($this->user_id);
        $data['boms'] = $this->bom->get_datewise_record($from_date, $to_date, $this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/view_bom', $data);
    }

    public function get_monthyearwise_record() {
        $month_year = $this->input->post('month_year');
        $data['bom_count'] = $this->bom->get_bom_count($this->user_id);
        $draft_status = 1;
        $data['bom_draft_count'] = $this->bom->get_bom_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['bom_sent_count'] = $this->bom->get_bom_draft_count($sent_status, $this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['bom_id'] = $this->bom->get_last_bom_number($this->user_id);
        $data['boms'] = $this->bom->get_monthyearwise_record($month_year, $this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/view_bom', $data);
    }

    public function get_bom_data_by_status() {
        $status = $this->uri->segment(3);
        $data['boms'] = $this->bom->get_bom_data_by_status($status, $this->user_id);
        $data['bom_count'] = $this->bom->get_bom_count($this->user_id);
        $draft_status = 1;
        $data['bom_draft_count'] = $this->bom->get_bom_draft_count($draft_status, $this->user_id);
        $sent_status = 2;
        $data['bom_sent_count'] = $this->bom->get_bom_draft_count($sent_status, $this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/view_bom', $data);
    }

    public function show_bom() {
        $id = $this->uri->segment(3) ? $this->uri->segment(3) : $this->input->get('id');
        $bom_number_id = $this->bom->get_bom_number_from_bom_total($id, $this->user_id);

        if (empty($bom_number_id)) {
            redirect('BomController/index');
        }

        $number = $bom_number_id['number_fk'];


        // var_dump($number);
        // die();
        
        $data['show_bom'] = $this->bom->get_bom_data($number, $this->user_id);
        $data['bom_data_group'] = $this->bom->get_bom_data_group_by($number, $this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);


// var_dump($data['bom_data_group']);
//         die("test");
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/show_bom', $data);
    }

    public function get_customer_email() {
        $number = trim((string) $this->input->post('number'));

        if ($number === '') {
            echo json_encode(array('success' => false, 'email' => '', 'customer_id' => ''));
            return;
        }

        $result = $this->bom->get_customer_email($number, $this->user_id);

        if (!empty($result)) {
            echo json_encode(array(
                'success' => true,
                'email' => isset($result['email']) ? $result['email'] : '',
                'customer_id' => isset($result['customer_id']) ? $result['customer_id'] : ''
            ));
            return;
        }

        echo json_encode(array('success' => false, 'email' => '', 'customer_id' => ''));
    }

    public function send_bom_email() {
        $bom_number = trim((string) $this->input->post('number'));
        $to_email = trim((string) $this->input->post('to_email'));
        $subject = trim((string) $this->input->post('subject'));
        $message = trim((string) $this->input->post('message'));
        $copy_email = $this->input->post('copy_email');

        if ($bom_number === '' || $to_email === '' || $subject === '') {
            $this->session->set_flashdata('INFOMSG', 'Required email details are missing.');
            redirect('BomController/index');
            return;
        }

        $bom_data_group = $this->bom->get_bom_data_group_by($bom_number, $this->user_id);
        if (empty($bom_data_group)) {
            $this->session->set_flashdata('INFOMSG', 'BOM details not found.');
            redirect('BomController/index');
            return;
        }

        $settings = $this->login->get_settings($this->user_id);
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = !empty($session_data_head2['company_name']) ? $session_data_head2['company_name'] : (isset($settings['company_name']) ? $settings['company_name'] : 'Company');
        $set_company_logo = !empty($session_data_head2['company_logo']) ? base_url() . '/' . ltrim($session_data_head2['company_logo'], '/') : base_url() . ltrim(isset($settings['company_logo']) ? $settings['company_logo'] : '', '/');
        $set_from_email = !empty($session_data_head2['from_email']) ? $session_data_head2['from_email'] : (isset($settings['email']) ? $settings['email'] : '');
        $set_cc_email = !empty($session_data_head2['cc_email']) ? $session_data_head2['cc_email'] : '';

        $bom_id = isset($bom_data_group['id']) ? $bom_data_group['id'] : 0;
        $customer_name = isset($bom_data_group['fullname']) && $bom_data_group['fullname'] !== '' ? $bom_data_group['fullname'] : (isset($bom_data_group['company_name']) ? $bom_data_group['company_name'] : 'Customer');
        $issue_date = !empty($bom_data_group['date']) && $bom_data_group['date'] !== '0000-00-00' ? date('d-m-Y', strtotime($bom_data_group['date'])) : date('d-m-Y');
        $download_url = base_url() . 'Download/download_bom/' . $bom_id . '/' . $this->user_id;

        $this->load->library('email');
        $this->email->set_mailtype('html');
        if ($set_from_email !== '') {
            if (strpos(base_url(), 'localhost') !== false) {

    // Local server
    $this->email->from($set_from_email, $set_company_name);

} else {

    // Live server
    $this->email->from("noreply@uwsenvirotech.com", $set_company_name);

}
        }
        $this->email->to($to_email);
        $this->email->subject($subject);

        if ($copy_email && $set_cc_email !== '') {
            $this->email->cc($set_cc_email);
        }

        $htmlContent = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BOM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <style>
        body { background: #f8f8f8; font-family: Arial, Helvetica, sans-serif; }
        .boxs { padding: 2% 10%; }
        .shadows1 {
            padding: 2% 4%;
            border-radius: 2px;
            line-height: 2;
            text-align: center;
            border: 1px solid grey;
            box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.58);
            background: #fff;
            max-width: 760px;
            margin: 0 auto;
        }
        .download-btn {
            background-color: #00929f;
            border-radius: 4px;
            color: #ffffff !important;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            line-height: 40px;
            text-align: center;
            text-decoration: none;
            width: 220px;
        }
    </style>
</head>
<body>
    <div class="boxs">
        <div class="shadows1">
            ' . ($set_company_logo !== base_url() ? '<center><img alt="' . htmlspecialchars($set_company_name, ENT_QUOTES, 'UTF-8') . '" src="' . $set_company_logo . '" width="30%"></center>' : '') . '
            <span style="color:#2f2f36;font-weight:bold;font-size:32px;"><center>BOM</center></span><br>
            <span style="color:#2f2f36;font-weight:bold;font-size:28px;">' . htmlspecialchars($bom_number, ENT_QUOTES, 'UTF-8') . '</span><br>
            <span style="color:#a0a0a5;">for <b>' . htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8') . '</b></span><br>
            <span style="color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
            <span style="color:#a0a0a5;">from <b>' . htmlspecialchars($set_company_name, ENT_QUOTES, 'UTF-8') . '</b></span>
            <hr>
            <span style="color:#2f2f36;">Please find the BOM attached via the download link below.</span>
            <hr>
            <span style="color:#2f2f36;"><b>Message :</b> ' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</span>
            <hr>
            <center>
                <a href="' . $download_url . '" class="download-btn" target="_blank">Download in browser</a>
            </center>
        </div>
    </div>
</body>
</html>';

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        if ($set_from_email !== '') {
            $headers .= 'From: ' . $set_company_name . ' <' . $set_from_email . ">\r\n";
        }

        $this->email->message($htmlContent);

        if ($this->email->send($to_email, $message, $headers)) {
            $data_status = array('status' => 2);
            $this->bom->edit_gst_bom_status($data_status, $bom_number, $this->user_id);
            $this->session->set_flashdata('SUCCESSMSG', 'Email Sent Successfully!!');
        } else {
            $this->session->set_flashdata('INFOMSG', 'Email not Sent Successfully!!');
        }

        redirect('BomController/index');
    }

    public function edit_bom_details() {
        $id = $this->uri->segment(3);
        $bom_number_id = $this->bom->get_bom_number_from_bom_total($id, $this->user_id);
        $number = $bom_number_id['number_fk'];
        
        $data['show_bom'] = $this->bom->get_bom_data($number, $this->user_id);
        $bom_group = $this->bom->get_bom_data_group_by($number, $this->user_id);

        // If MRP has already been run for this BOM (send_to_mrp == 2), predict the upcoming revision number to show in heading
        $bom_total_row = $this->db->where('number_fk', $number)->get('bom_total')->row();
        if ($bom_total_row && $bom_total_row->send_to_mrp == 2) {
            $base_bom_number = preg_replace('/(?:\/R|-R)\d+$/i', '', $number);
            $existing_revs = $this->db->select('number_fk')
                                      ->like('number_fk', $base_bom_number)
                                      ->get('bom_total')
                                      ->result_array();
            $max_rev = 0;
            foreach ($existing_revs as $er) {
                if (preg_match('/(?:\/R|-R)(\d+)$/i', $er['number_fk'], $matches)) {
                    $r = intval($matches[1]);
                    if ($r > $max_rev) {
                        $max_rev = $r;
                    }
                }
            }
            $display_number = $base_bom_number . '/R' . ($max_rev + 1);
            if (is_array($bom_group)) {
                $bom_group['display_number'] = $display_number;
            }
        }
        $data['bom_data_group'] = $bom_group;

        $data['status_result'] = $this->bom->get_status($number, $this->user_id);
        $data['customer_result'] = $this->customer->get_customer($this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['company_name'] = $this->bom->get_company_name($this->user_id);
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['salesorder_list'] = $this->salesorder->get_so_list_for_bom($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/edit_bom', $data);
    }

    public function delete_bom_by_bom_number() {
        $segments = $this->uri->segment_array();
        // Index 2 in array_slice corresponds to index 3 in 1-indexed URI segments
        $bom_segments = array_slice($segments, 2);
        $bom_number = implode('/', $bom_segments);
        
        $result = $this->bom->delete_bom_by_bom_number($bom_number, $this->user_id);
        $result1 = $this->bom->delete_bom_total_by_bom_number($bom_number, $this->user_id);
        
        if ($result || $result1) {
            $this->session->set_flashdata('SUCCESSMSG', "BOM deleted successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "BOM not deleted successfully!!");
        }
        redirect('BomController/index');
    }

    public function send_to_mrp() {
        $id = $this->uri->segment(3);
        if (empty($id)) {
            $this->session->set_flashdata('ERRORMSG', "Invalid BOM ID!!");
            redirect('BomController/index');
        }

        // Get BOM details
        $bom_total = $this->db
            ->where('id', $id)
            ->get('bom_total')
            ->row();

        if (!$bom_total) {
            $this->session->set_flashdata('ERRORMSG', "BOM not found!!");
            redirect('BomController/index');
        }

        if ($bom_total->send_to_mrp == 2) {
            $this->session->set_flashdata('ERRORMSG', "MRP has already run for this BOM. It cannot be modified.");
            redirect('BomController/index');
        }

        // Gate: only Approved BOMs (status=4) can be sent to MRP
        if ($bom_total->status != 4) {
            $this->session->set_flashdata('ERRORMSG', "BOM must be Approved before it can be sent to MRP.");
            redirect('BomController/index');
        }

        $this->db->where('id', $id)->update('bom_total', array('send_to_mrp' => 1));

        $this->session->set_flashdata('SUCCESSMSG', "BOM sent to MRP successfully!!");
        redirect('BomController/index');
    }

    public function unsend_from_mrp() {
        $id = $this->uri->segment(3);
        if (empty($id)) {
            $this->session->set_flashdata('ERRORMSG', "Invalid BOM ID!!");
            redirect('BomController/index');
        }

        // Get BOM details
        $bom_total = $this->db
            ->where('id', $id)
            ->get('bom_total')
            ->row();

        if (!$bom_total) {
            $this->session->set_flashdata('ERRORMSG', "BOM not found!!");
            redirect('BomController/index');
        }

        if ($bom_total->send_to_mrp == 2) {
            $this->session->set_flashdata('ERRORMSG', "MRP has already run for this BOM. It cannot be unsent.");
            redirect('BomController/index');
        }

        $this->db->where('id', $id)->update('bom_total', array('send_to_mrp' => 0, 'status' => 1));

        $this->session->set_flashdata('SUCCESSMSG', "BOM unsent from MRP successfully and reverted to Draft!!");
        redirect('BomController/index');
    }

    /**
 * Export BOM to Excel — format exactly matches reference image
 * OC 242 BOM Praj Industries (Dosing System -- Hindalco-26004)
 * URL: BomController/export_bom_excel/{id}
 *
 * Column layout (A-I, 9 columns):
 *   A=SR.NO  B=EQUIPMENT(name)  C=DESCRIPTION  D=QTY  E=UNIT  F=TAG NO.  G=SCOPE  H=STORES REMARK  I=REMARK
 *   Header row: B:C merged as "EQUIPMENT"
 */

    /**
 * Export BOM to Excel — format exactly matches reference image
 * OC 242 BOM Praj Industries (Dosing System -- Hindalco-26004)
 * URL: BomController/export_bom_excel/{id}
 *
 * Column layout (A-I, 9 columns):
 *   A=SR.NO  B=EQUIPMENT(name)  C=DESCRIPTION  D=QTY  E=UNIT  F=TAG NO.  G=SCOPE  H=STORES REMARK  I=REMARK
 *   Header row: B:C merged as "EQUIPMENT"
 */
public function export_bom_excel() {
    $id = $this->uri->segment(3);
    if (empty($id)) {
        $this->session->set_flashdata('ERRORMSG', "Invalid BOM ID!!");
        redirect('BomController/index');
    }

    $bom_number_data = $this->bom->get_bom_number_from_bom_total($id, $this->user_id);
    if (empty($bom_number_data)) {
        $this->session->set_flashdata('ERRORMSG', "BOM not found!!");
        redirect('BomController/index');
    }
    $number = $bom_number_data['number_fk'];

    $show_bom       = $this->bom->get_bom_data($number, $this->user_id);
    $bom_data_group = $this->bom->get_bom_data_group_by($number, $this->user_id);
    $settings       = $this->login->get_settings($this->user_id);
    $unit_result    = $this->units->get_units($this->user_id);

    if (empty($bom_data_group)) {
        $this->session->set_flashdata('ERRORMSG', "BOM data not found!!");
        redirect('BomController/index');
    }

    // Helper: strip HTML
    $stripHtml = function($text) {
        if (empty($text)) return '';
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = preg_replace('/<\/p>/i', "\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim($text);
    };

    // Helper: get unit label
    $getUnit = function($unit_code) use ($unit_result) {
        if (!empty($unit_code) && !empty($unit_result)) {
            foreach ($unit_result as $u) {
                if ($u->unit == $unit_code) return $u->unit;
            }
        }
        return $unit_code ?? '';
    };

    require_once FCPATH . 'vendor/autoload.php';

    try {
        // ================================================================
        // SPREADSHEET SETUP
        // ================================================================
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($settings['company_name'] ?? 'System')
            ->setLastModifiedBy('System')
            ->setTitle("BOM - " . ($bom_data_group['number'] ?? 'Unknown'))
            ->setSubject("Bill of Material");

        // ================================================================
        // SHEET 1: MAIN BOM
        // ================================================================
        $sheet = $spreadsheet->getActiveSheet();
        $systemName = !empty($bom_data_group['system']) ? $bom_data_group['system'] : 'BOM';
        $sheet->setTitle(mb_substr($systemName, 0, 31));

        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        // ---- Column widths (matching reference image) ----
        // A=SR.NO, B=ITEM CODE, C=PRODUCT NAME, D=DESCRIPTION, E=QTY, F=UNIT, G=TAG NO, H=SCOPE, I=STORES REMARK, J=REMARK
        $colWidths = ['A'=>6, 'B'=>15, 'C'=>22, 'D'=>38, 'E'=>6, 'F'=>8, 'G'=>14, 'H'=>8, 'I'=>22, 'J'=>16];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ---- Styles ----
        $boldStyle = [
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 10]
        ];
        $metaLabelStyle = [
            'font'      => ['bold' => true, 'name' => 'Calibri', 'size' => 10],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $metaValueStyle = [
            'font'      => ['name' => 'Calibri', 'size' => 10],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['wrapText' => true]
        ];
        // Peach header (FCD5B6) matching reference row
        $peachHeaderStyle = [
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => '000000']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCD5B6']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                              'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                              'wrapText'   => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        // Light green EQUIPMENTS heading (C3D69B)
        $sectionStyle = [
            'font'      => ['bold' => true, 'size' => 13, 'name' => 'Calibri', 'color' => ['rgb' => '000000']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C3D69B']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                              'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        // Lavender sub-group (E6E0ED) with RED bold Cambria (matching reference ALUM DOSING SYSTEM row)
        $subGroupStyle = [
            'font'      => ['bold' => true, 'size' => 12, 'name' => 'Cambria', 'color' => ['rgb' => 'FF0000']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6E0ED']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                              'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        // Peach sub-group (FDEADA) with Calibri 12 Bold (piping/material levels)
        $peachSectionStyle = [
            'font'      => ['bold' => true, 'size' => 12, 'name' => 'Calibri', 'color' => ['rgb' => '000000']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEADA']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                              'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        // Light Blue/Teal sub-group (DBEFF4) with Calibri 12 Bold (component types)
        $blueSectionStyle = [
            'font'      => ['bold' => true, 'size' => 12, 'name' => 'Calibri', 'color' => ['rgb' => '000000']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEFF4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                              'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $dataBorderStyle = [
            'font'    => ['name' => 'Calibri', 'size' => 10],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $dataBoldBorderStyle = [
            'font'    => ['bold' => true, 'name' => 'Calibri', 'size' => 10],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        // ================================================================
        // ROW 1-2: LOGO (centered: merged A1:J2) + company name (text fallback)
        // ================================================================
        $row = 1;

        // Merge columns A to J for the logo row
        $sheet->mergeCells('A1:J2');
        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getRowDimension(2)->setRowHeight(40);

        if (!empty($settings['company_logo'])) {
            $logo_path = FCPATH . ltrim($settings['company_logo'], './');
            if (!file_exists($logo_path)) {
                $logo_path = FCPATH . $settings['company_logo'];
            }
            if (file_exists($logo_path)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Company Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logo_path);
                $drawing->setHeight(75);
                
                // Calculate column widths in pixels dynamically to perfectly center the logo
                $colWidths = ['A'=>6, 'B'=>15, 'C'=>22, 'D'=>38, 'E'=>6, 'F'=>8, 'G'=>14, 'H'=>8, 'I'=>22, 'J'=>16];
                $totalWidth = 0;
                $colPixelOffsets = [];
                foreach ($colWidths as $col => $w) {
                    $colPixelOffsets[$col] = $totalWidth;
                    // Excel column width to pixel conversion: width * 7 + 5 (approx)
                    $totalWidth += ($w * 7) + 5;
                }
                
                // Proportionally scale logo width (default fallback 441px)
                $logoWidth = 441;
                list($origWidth, $origHeight) = getimagesize($logo_path);
                if ($origHeight > 0) {
                    $logoWidth = round(75 * ($origWidth / $origHeight));
                }
                
                // Center the logo across all columns A to J
                $targetLeft = ($totalWidth - $logoWidth) / 2;
                if ($targetLeft < 0) {
                    $targetLeft = 0;
                }
                
                // Find target column and within-column pixel offset
                $targetCol = 'A';
                $targetOffset = 0;
                foreach ($colPixelOffsets as $col => $offset) {
                    $colWidthPx = ($colWidths[$col] * 7) + 5;
                    if ($targetLeft >= $offset && $targetLeft < ($offset + $colWidthPx)) {
                        $targetCol = $col;
                        $targetOffset = round($targetLeft - $offset);
                        break;
                    }
                }
                
                $drawing->setCoordinates($targetCol . '1');
                $drawing->setOffsetX($targetOffset);
                $drawing->setOffsetY(3);
                $drawing->setWorksheet($sheet);
            } else {
                // Logo file not found: write company name as text
                $sheet->setCellValue('A1', $settings['company_name'] ?? '');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'name' => 'Calibri', 'color' => ['rgb' => '1F4E79']],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                      'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
                ]);
            }
        } else {
            $sheet->setCellValue('A1', $settings['company_name'] ?? '');
            $sheet->getStyle('A1')->applyFromArray([
                'font'      => ['bold' => true, 'size' => 16, 'name' => 'Calibri', 'color' => ['rgb' => '1F4E79']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                  'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
            ]);
        }

        // ================================================================
        // ROW 3: Blank separator
        // ================================================================
        $row = 3;
        $sheet->getRowDimension($row)->setRowHeight(4);
        $row++;

        // ================================================================
        // ROWS 4-9: METADATA TABLE (left A:F) + "Bill Of Material" (right G:J)
        // ================================================================
        $metaStartRow = $row; // row 4

        // "Bill Of Material" spans G4:J9 (right block, vertically merged, large font)
        $sheet->mergeCells('G' . $metaStartRow . ':J' . ($metaStartRow + 5));
        $sheet->setCellValue('G' . $metaStartRow, 'Bill Of Material');
        $sheet->getStyle('G' . $metaStartRow)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 28, 'name' => 'Calibri', 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                              'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                              'wrapText'   => false]
        ]);

        // Row 4: SYSTEM
        $sheet->setCellValue('A' . $row, 'SYSTEM');
        $sheet->getStyle('A' . $row)->applyFromArray($metaLabelStyle);
        $sheet->mergeCells('B' . $row . ':F' . $row);
        $sheet->setCellValue('B' . $row, $bom_data_group['system'] ?? '');
        $sheet->getStyle('B' . $row . ':F' . $row)->applyFromArray($metaValueStyle);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // Row 5: Location
        $sheet->setCellValue('A' . $row, 'Location');
        $sheet->getStyle('A' . $row)->applyFromArray($metaLabelStyle);
        $sheet->mergeCells('B' . $row . ':F' . $row);
        $sheet->setCellValue('B' . $row, $bom_data_group['location'] ?? '');
        $sheet->getStyle('B' . $row . ':F' . $row)->applyFromArray($metaValueStyle);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // Row 6: CLIENT
        $sheet->setCellValue('A' . $row, 'CLIENT');
        $sheet->getStyle('A' . $row)->applyFromArray($metaLabelStyle);
        $sheet->mergeCells('B' . $row . ':F' . $row);
        // CLIENT = Company name + (Project Code: xxxx) if available
        $clientVal = $bom_data_group['company_name'] ?? '';
        if (!empty($bom_data_group['project_code'])) {
            $clientVal .= ' (Project Code: ' . $bom_data_group['project_code'] . ')';
        }
        $sheet->setCellValue('B' . $row, $clientVal);
        $sheet->getStyle('B' . $row . ':F' . $row)->applyFromArray($metaValueStyle);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // Row 7: CAPACITY
        $sheet->setCellValue('A' . $row, 'CAPACITY');
        $sheet->getStyle('A' . $row)->applyFromArray($metaLabelStyle);
        $sheet->mergeCells('B' . $row . ':F' . $row);
        $sheet->setCellValue('B' . $row, $bom_data_group['capacity'] ?? '');
        $sheet->getStyle('B' . $row . ':F' . $row)->applyFromArray($metaValueStyle);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // Row 8: QTY | 1 LOT | (C:F empty) | Revision | 0
        $sheet->setCellValue('A' . $row, 'QTY');
        $sheet->getStyle('A' . $row)->applyFromArray($metaLabelStyle);
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->setCellValue('B' . $row, !empty($bom_data_group['project_qty']) ? $bom_data_group['project_qty'] : '1 LOT');
        $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray($metaValueStyle);
        $sheet->setCellValue('E' . $row, 'Revision');
        $sheet->getStyle('E' . $row)->applyFromArray($metaLabelStyle);
        $sheet->setCellValue('F' . $row, '0');
        $sheet->getStyle('F' . $row)->applyFromArray($metaValueStyle);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // Row 9: OC No. | UWS-... | (C:F empty) | Date | value
        $sheet->setCellValue('A' . $row, 'OC No.');
        $sheet->getStyle('A' . $row)->applyFromArray($metaLabelStyle);
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->setCellValue('B' . $row, $bom_data_group['oc_number'] ?? '');
        $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray($metaValueStyle);
        $sheet->setCellValue('E' . $row, 'Date');
        $sheet->getStyle('E' . $row)->applyFromArray($metaLabelStyle);
        $bom_date = !empty($bom_data_group['date']) ? date('n/j/Y', strtotime($bom_data_group['date'])) : date('n/j/Y');
        $sheet->setCellValue('F' . $row, $bom_date);
        $sheet->getStyle('F' . $row)->applyFromArray($metaValueStyle);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // Row 10: Prepared By | value | (C:D empty) | Approved By | value
        $sheet->setCellValue('A' . $row, 'Prepared By');
        $sheet->getStyle('A' . $row)->applyFromArray($metaLabelStyle);
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->setCellValue('B' . $row, $bom_data_group['prepare_by'] ?? '');
        $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray($metaValueStyle);
        $sheet->setCellValue('E' . $row, 'Approved By');
        $sheet->getStyle('E' . $row)->applyFromArray($metaLabelStyle);
        $sheet->setCellValue('F' . $row, $bom_data_group['approved_by_name'] ?? '');
        $sheet->getStyle('F' . $row)->applyFromArray($metaValueStyle);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // ================================================================
        // TABLE HEADER ROW (peach FCD5B6)
        // Columns: A=SR.NO | B=ITEM CODE | C=PRODUCT NAME | D=DESCRIPTION | E=QTY | F=UNIT | G=TAG NO. | H=SCOPE | I=STORES REMARK | J=REMARK
        // ================================================================
        $headerRow = $row;
        $sheet->getRowDimension($headerRow)->setRowHeight(30);

        // A: SR.NO
        $sheet->setCellValue('A' . $headerRow, 'SR.NO');
        $sheet->getStyle('A' . $headerRow)->applyFromArray($peachHeaderStyle);

        // B: ITEM CODE
        $sheet->setCellValue('B' . $headerRow, 'ITEM CODE');
        $sheet->getStyle('B' . $headerRow)->applyFromArray($peachHeaderStyle);

        // C: PRODUCT NAME
        $sheet->setCellValue('C' . $headerRow, 'PRODUCT NAME');
        $sheet->getStyle('C' . $headerRow)->applyFromArray($peachHeaderStyle);

        // D: DESCRIPTION
        $sheet->setCellValue('D' . $headerRow, 'DESCRIPTION');
        $sheet->getStyle('D' . $headerRow)->applyFromArray($peachHeaderStyle);

        // E: QTY
        $sheet->setCellValue('E' . $headerRow, 'QTY');
        $sheet->getStyle('E' . $headerRow)->applyFromArray($peachHeaderStyle);

        // F: UNIT
        $sheet->setCellValue('F' . $headerRow, 'UNIT');
        $sheet->getStyle('F' . $headerRow)->applyFromArray($peachHeaderStyle);

        // G: TAG NO.
        $sheet->setCellValue('G' . $headerRow, 'TAG NO.');
        $sheet->getStyle('G' . $headerRow)->applyFromArray($peachHeaderStyle);

        // H: SCOPE
        $sheet->setCellValue('H' . $headerRow, 'SCOPE');
        $sheet->getStyle('H' . $headerRow)->applyFromArray($peachHeaderStyle);

        // I: STORES REMARK
        $sheet->setCellValue('I' . $headerRow, 'STORES REMARK IF MATERIAL IS STOCK Y/N');
        $sheet->getStyle('I' . $headerRow)->applyFromArray($peachHeaderStyle);

        // J: REMARK
        $sheet->setCellValue('J' . $headerRow, 'REMARK');
        $sheet->getStyle('J' . $headerRow)->applyFromArray($peachHeaderStyle);

        $row++;

        // Check if database records already contain headings
        $hasHeadingsInDb = false;
        if (!empty($show_bom)) {
            foreach ($show_bom as $item) {
                if (isset($item->product_name) && $item->product_name === '__HEADING__') {
                    $hasHeadingsInDb = true;
                    break;
                }
            }
        }

        if (!$hasHeadingsInDb) {
            // ================================================================
            // SECTION HEADING: EQUIPMENTS (green C3D69B, merged A:J)
            // ================================================================
            $sheet->mergeCells('A' . $row . ':J' . $row);
            $sheet->setCellValue('A' . $row, 'EQUIPMENTS');
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($sectionStyle);
            $sheet->getRowDimension($row)->setRowHeight(18);
            $row++;

            // ================================================================
            // SUB-GROUP HEADING (lavender E6E0ED, red bold Cambria, merged A:J)
            // e.g. "ALUM DOSING SYSTEM FOR FLASH MIXER--(U001)"
            // ================================================================
            $subheading = !empty($bom_data_group['bom_subheading']) ? $bom_data_group['bom_subheading'] : $bom_data_group['system'] ?? '';
            if (!empty($subheading)) {
                $sheet->mergeCells('A' . $row . ':J' . $row);
                $sheet->setCellValue('A' . $row, strtoupper($subheading));
                $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($subGroupStyle);
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
        }

        // ================================================================
        // BOM ITEM ROWS
        // Layout per reference image:
        //   Row A: [SR.NO] [ITEM CODE] [PRODUCT NAME] [DESCRIPTION] [QTY] [UNIT] [TAG] [SCOPE] [STORES] [REMARK]
        // ================================================================
        $sr_no = 1;
        $current_section_is_peach = false;
        
        if (!empty($show_bom)) {
            foreach ($show_bom as $item) {
                // If it is a Heading row
                if (isset($item->product_name) && $item->product_name === '__HEADING__') {
                    $desc = $stripHtml($item->description ?? '');
                    
                    // Spacer row
                    if (trim($desc) === '') {
                        $sheet->mergeCells('A' . $row . ':J' . $row);
                        $sheet->setCellValue('A' . $row, '');
                        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE]]
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(15);
                        $row++;
                        continue;
                    }
                    
                    $isMain = null;
                    if (isset($item->tag_no) && ($item->tag_no === 'MAIN' || $item->tag_no === 'SUB')) {
                        $isMain = ($item->tag_no === 'MAIN');
                    }

                    if ($isMain === true || ($isMain === null && preg_match('/SYSTEM|SPARES|COMMISSIONING|TANK FOR|EQUIPMENTS/i', $desc))) {
                        $current_section_is_peach = false;
                        $sheet->mergeCells('A' . $row . ':J' . $row);
                        $sheet->setCellValue('A' . $row, strtoupper($desc));
                        if (preg_match('/EQUIPMENTS/i', $desc)) {
                            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($sectionStyle);
                        } else {
                            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($subGroupStyle);
                        }
                        $sheet->getRowDimension($row)->setRowHeight(20);
                    } elseif ($isMain === false || ($isMain === null && preg_match('/PIPING|FITTINGS|VALVES|FLANGE|ELBOW|TEE|PIPE|CPVC|UPVC/i', $desc))) {
                        $current_section_is_peach = true;
                        $sheet->mergeCells('A' . $row . ':J' . $row);
                        $sheet->setCellValue('A' . $row, $desc);
                        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($peachSectionStyle);
                        $sheet->getRowDimension($row)->setRowHeight(20);
                    } else {
                        $current_section_is_peach = false;
                        $sheet->mergeCells('A' . $row . ':J' . $row);
                        $sheet->setCellValue('A' . $row, $desc);
                        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($blueSectionStyle);
                        $sheet->getRowDimension($row)->setRowHeight(20);
                    }
                    
                    $row++;
                    continue;
                }

                // Normal Item row
                $itemCode   = $stripHtml($item->product_name ?? '');
                $productName= $stripHtml($item->item_name ?? '');
                $desc       = $stripHtml($item->description ?? '');
                $qty        = $item->quantity ?? '';
                $unitVal    = $getUnit($item->unit ?? '');
                $tagNo      = $stripHtml($item->tag_no ?? '');
                $scope      = $stripHtml($item->scope ?? '');
                $stores     = isset($item->stores_remark)
                                ? ($item->stores_remark == 'Y' ? 'Y' : ($item->stores_remark == 'N' ? 'N' : ''))
                                : '';
                $remark     = $stripHtml($item->remark ?? '');

                // Set row height dynamically based on description length
                $descLines  = max(1, ceil(strlen($desc) / 50));
                $rowHeight  = max(18, $descLines * 14);
                $sheet->getRowDimension($row)->setRowHeight($rowHeight);

                $itemFillColor = 'FFFFFF'; // Product items always have white/no background fill color
                $cellStyleNormal = array_merge($dataBorderStyle, [
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $itemFillColor]]
                ]);
                $cellStyleBold = array_merge($dataBoldBorderStyle, [
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $itemFillColor]]
                ]);

                // A: SR.NO (center, border)
                $sheet->setCellValue('A' . $row, $sr_no);
                $sheet->getStyle('A' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                      'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                // B: ITEM CODE (center for short codes, left-aligned and wrapped for long ones)
                $itemCodeAlign = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                if (strlen($itemCode) > 12) {
                    $itemCodeAlign = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                }
                $sheet->setCellValue('B' . $row, $itemCode);
                $sheet->getStyle('B' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => [
                        'horizontal' => $itemCodeAlign,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                        'wrapText'   => true
                    ]
                ]));

                // C: PRODUCT NAME
                $sheet->setCellValue('C' . $row, $productName);
                $sheet->getStyle('C' . $row)->applyFromArray(array_merge($cellStyleBold, [
                    'alignment' => ['wrapText' => true, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                // D: DESCRIPTION
                $sheet->setCellValue('D' . $row, $desc);
                $sheet->getStyle('D' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => ['wrapText' => true, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                // E: QTY (center)
                $sheet->setCellValue('E' . $row, $qty);
                $sheet->getStyle('E' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                      'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                // F: UNIT (center)
                $sheet->setCellValue('F' . $row, $unitVal);
                $sheet->getStyle('F' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                      'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                // G: TAG NO. (center)
                $sheet->setCellValue('G' . $row, $tagNo);
                $sheet->getStyle('G' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                      'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                // H: SCOPE (center)
                $sheet->setCellValue('H' . $row, $scope);
                $sheet->getStyle('H' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                      'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                // I: STORES REMARK (center)
                $sheet->setCellValue('I' . $row, $stores);
                $sheet->getStyle('I' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                      'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                // J: REMARK (left, wrap)
                $sheet->setCellValue('J' . $row, $remark);
                $sheet->getStyle('J' . $row)->applyFromArray(array_merge($cellStyleNormal, [
                    'alignment' => ['wrapText' => true, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP]
                ]));

                $row++;
                $sr_no++;
            }
        } else {
            $sheet->mergeCells('A' . $row . ':J' . $row);
            $sheet->setCellValue('A' . $row, 'No BOM items found');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        // Auto-fit column widths based on cell content (A to J)
        $minColWidths = ['A'=>6, 'B'=>15, 'C'=>22, 'D'=>38, 'E'=>6, 'F'=>8, 'G'=>14, 'H'=>8, 'I'=>22, 'J'=>16];
        $maxColWidths = ['A'=>10, 'B'=>45, 'C'=>45, 'D'=>65, 'E'=>10, 'F'=>12, 'G'=>25, 'H'=>15, 'I'=>30, 'J'=>35];
        $mergedRanges = $sheet->getMergeCells();

        for ($colChar = 'A'; $colChar <= 'J'; $colChar++) {
            $maxWidth = $minColWidths[$colChar];
            
            // Loop from $headerRow + 1 to $row - 1 (only actual BOM item rows)
            for ($r = $headerRow + 1; $r < $row; $r++) {
                // Skip rows where the entire A to J columns are merged (e.g. heading rows)
                $isMerged = false;
                foreach ($mergedRanges as $range) {
                    if (preg_match('/^A' . $r . ':J' . $r . '$/i', $range)) {
                        $isMerged = true;
                        break;
                    }
                }
                if ($isMerged) {
                    continue;
                }
                
                $cellVal = $sheet->getCell($colChar . $r)->getValue();
                if ($cellVal !== null && $cellVal !== '') {
                    $lines = explode("\n", (string)$cellVal);
                    foreach ($lines as $line) {
                        $len = mb_strlen($line);
                        // Add padding for cell visual spacing
                        $cellWidth = $len + 4;
                        if ($cellWidth > $maxWidth) {
                            $maxWidth = $cellWidth;
                        }
                    }
                }
            }
            
            if ($maxWidth > $maxColWidths[$colChar]) {
                $maxWidth = $maxColWidths[$colChar];
            }
            $sheet->getColumnDimension($colChar)->setWidth($maxWidth);
        }

        // ================================================================
        // SHEET 2: INSTRUMENT SCHEDULE
        // ================================================================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Instrument Schedule');

        $is_cols = ['A'=>8,'B'=>14,'C'=>22,'D'=>14,'E'=>14,'F'=>16,'G'=>12,'H'=>14,'I'=>10,'J'=>14,'K'=>10,'L'=>10,'M'=>20,'N'=>10,'O'=>14];
        foreach ($is_cols as $c => $w) { $sheet2->getColumnDimension($c)->setWidth($w); }

        $r2 = 1;
        $sheet2->mergeCells('D' . $r2 . ':I' . $r2);
        $sheet2->setCellValue('D' . $r2, 'INSTRUMENT SCHEDULE');
        $sheet2->getStyle('D' . $r2)->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'name' => 'Calibri']]);
        $sheet2->mergeCells('J' . $r2 . ':O' . $r2);
        $sheet2->setCellValue('J' . $r2, $settings['company_name'] ?? '');
        $sheet2->getStyle('J' . $r2)->applyFromArray(['font' => ['bold' => true, 'size' => 10, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]]);
        $r2++;

        $sheet2->setCellValue('J' . $r2, 'PROJECT CODE:');
        $sheet2->getStyle('J' . $r2)->applyFromArray(['font' => ['bold' => true, 'name' => 'Calibri']]);
        $sheet2->setCellValue('K' . $r2, $bom_data_group['project_code'] ?? '');
        $r2++;
        $sheet2->setCellValue('J' . $r2, 'DOCUMENT NO.:');
        $sheet2->getStyle('J' . $r2)->applyFromArray(['font' => ['bold' => true, 'name' => 'Calibri']]);
        $r2++;
        $sheet2->setCellValue('J' . $r2, 'OC NO.:');
        $sheet2->getStyle('J' . $r2)->applyFromArray(['font' => ['bold' => true, 'name' => 'Calibri']]);
        $sheet2->setCellValue('K' . $r2, $bom_data_group['oc_number'] ?? '');
        $r2 += 3;

        $revHeaders = ['Rev.', 'Date', 'Prep. By', 'Chkd. By', 'Appd. By', 'Remark'];
        $revCols    = ['J','K','L','M','N','O'];
        foreach ($revHeaders as $ri => $rh) {
            $sheet2->setCellValue($revCols[$ri] . $r2, $rh);
            $sheet2->getStyle($revCols[$ri] . $r2)->applyFromArray([
                'font' => ['bold' => true, 'name' => 'Calibri'],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D3D3D3']]
            ]);
        }
        $r2++;

        $sheet2->setCellValue('A' . $r2, 'INSTRUMENTATION DATA');
        $sheet2->getStyle('A' . $r2)->applyFromArray(['font' => ['bold' => true, 'name' => 'Calibri']]);
        $r2++;
        $sheet2->setCellValue('A' . $r2, 'DETAILS REQUIRED ON PIPING');
        $sheet2->getStyle('A' . $r2)->applyFromArray(['font' => ['bold' => true, 'name' => 'Calibri']]);
        $r2++;

        $instHeaders = ['SR. NO.','TAG NO.','TYPE OF INSTRUMENT','P&ID NO.','LOCATION','CONNECTION TYPE','SIZE IN NB','RANGE/ CTC','DIAL SIZE','STANDARD','MOC','','MODEL','','MAKE'];
        $instCols    = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O'];
        foreach ($instHeaders as $ii => $ih) {
            $sheet2->setCellValue($instCols[$ii] . $r2, $ih);
            $sheet2->getStyle($instCols[$ii] . $r2)->applyFromArray([
                'font'      => ['bold' => true, 'name' => 'Calibri', 'size' => 10],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCD5B6']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ]);
        }
        $r2++;
        $subH = ['','','','','','','','','','','BODY','INTERNAL','','',''];
        foreach ($subH as $si => $sh) {
            if (!empty($sh)) {
                $sheet2->setCellValue($instCols[$si] . $r2, $sh);
                $sheet2->getStyle($instCols[$si] . $r2)->applyFromArray([
                    'font'      => ['bold' => true, 'name' => 'Calibri'],
                    'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCD5B6']],
                    'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
                ]);
            }
        }
        $r2++;
        for ($ti = 1; $ti <= 60; $ti++) {
            $sheet2->setCellValue('A' . $r2, $ti);
            $sheet2->getStyle('A' . $r2 . ':O' . $r2)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
            ]);
            $r2++;
        }

        // ================================================================
        // SHEET 3: VALVE SCHEDULE
        // ================================================================
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Valve Schedule');

        $vs_cols = ['A'=>8,'B'=>12,'C'=>10,'D'=>10,'E'=>14,'F'=>20,'G'=>14,'H'=>16,'I'=>12,'J'=>18,'K'=>14,'L'=>16,'M'=>14,'N'=>12,'O'=>14,'P'=>16];
        foreach ($vs_cols as $c => $w) { $sheet3->getColumnDimension($c)->setWidth($w); }

        $r3 = 1;
        $sheet3->mergeCells('A' . $r3 . ':P' . $r3);
        $sheet3->setCellValue('A' . $r3, 'VALVE SCHEDULED');
        $sheet3->getStyle('A' . $r3)->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'name' => 'Calibri']]);
        $r3++;
        $sheet3->mergeCells('A' . $r3 . ':P' . $r3);
        $sheet3->setCellValue('A' . $r3, 'V A L V E    D E T A I L S');
        $sheet3->getStyle('A' . $r3)->applyFromArray(['font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri']]);
        $r3++;

        $valveH = ['SR. NO.','TAG NO.','SIZE    NB','RATING','TYPE','VALVE CONFIGURATION','Make','SERVICE','BODY','DISC / BALL  /  DIAPH.','SEAT / SEAL','END CONNECTION','P& ID NO.','FLUID','TEMP. 0C   Design','REMARK'];
        $valveC = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P'];
        foreach ($valveH as $vi => $vh) {
            $sheet3->setCellValue($valveC[$vi] . $r3, $vh);
            $sheet3->getStyle($valveC[$vi] . $r3)->applyFromArray([
                'font'      => ['bold' => true, 'name' => 'Calibri', 'size' => 10],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D3D3D3']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ]);
        }
        $r3++;
        $sheet3->mergeCells('A' . $r3 . ':P' . $r3);
        $subLabel = !empty($bom_data_group['bom_subheading']) ? strtoupper($bom_data_group['bom_subheading']) : '';
        $sheet3->setCellValue('A' . $r3, $subLabel);
        $sheet3->getStyle('A' . $r3)->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri'],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6E0ED']]
        ]);
        $r3++;
        for ($ti = 1; $ti <= 80; $ti++) {
            $sheet3->setCellValue('A' . $r3, $ti);
            $sheet3->getStyle('A' . $r3 . ':P' . $r3)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
            ]);
            $r3++;
        }

        // ================================================================
        // OUTPUT FILE
        // ================================================================
        $spreadsheet->setActiveSheetIndex(0);

        $safeName = preg_replace('/[\/\\:*?"<>|]/', '-', ($bom_data_group['number'] ?? 'BOM'));
        $filename = $safeName . '_BOM_' . date('Ymd_His') . '.xlsx';

        while (ob_get_level()) { ob_end_clean(); }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    } catch (Exception $e) {
        log_message('error', 'BOM Excel export error: ' . $e->getMessage());
        $this->session->set_flashdata('ERRORMSG', "Excel export failed: " . $e->getMessage());
        redirect('BomController/index');
    }
}

public function export_all_boms() {
    try {
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("System")
            ->setLastModifiedBy("System")
            ->setTitle("BOM List")
            ->setSubject("BOM Details")
            ->setDescription("Export of all BOM details");

        // Heading
        $sheet->setCellValue('A1', 'BOM LIST REPORT');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Generated on: ' . date('d-m-Y'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headers = ['Sr.No.', 'Status', 'Date', 'BOM Number', 'Company Name', 'SO Number', 'Created By', 'Approved By'];
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

        $boms = $this->bom->get_boms($this->user_id);
        $statusArr = [
            0 => 'Pending',
            1 => 'Draft',
            2 => 'Sent',
            3 => 'Viewed',
            4 => 'Approved',
            5 => 'Rejected',
            6 => 'Canceled',
            7 => 'Under Review'
        ];

        $rowNum = 4;
        $i = 1;
        foreach ($boms as $key) {
            $status = isset($key->status) ? $key->status : 0;
            $statusStr = isset($statusArr[$status]) ? $statusArr[$status] : '';
            $dateStr = (!empty($key->date) && $key->date !== '0000-00-00') ? date('d-m-Y', strtotime($key->date)) : '';
            $bomNumber = isset($key->number) ? $key->number : '';
            $companyName = isset($key->company_name) ? $key->company_name : '';
            $soNumber = isset($key->oc_number) ? $key->oc_number : '';
            $createdBy = isset($key->prepare_by) ? $key->prepare_by : '';
            $approvedBy = isset($key->approved_by_name) ? $key->approved_by_name : '';

            $sheet->setCellValue('A' . $rowNum, $i);
            $sheet->setCellValue('B' . $rowNum, $statusStr);
            $sheet->setCellValue('C' . $rowNum, $dateStr);
            $sheet->setCellValue('D' . $rowNum, $bomNumber);
            $sheet->setCellValue('E' . $rowNum, $companyName);
            $sheet->setCellValue('F' . $rowNum, $soNumber);
            $sheet->setCellValue('G' . $rowNum, $createdBy);
            $sheet->setCellValue('H' . $rowNum, $approvedBy);

            $rowNum++;
            $i++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'BOM_List_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        log_message('error', 'BOM list Excel export error: ' . $e->getMessage());
        $this->session->set_flashdata('ERRORMSG', "Excel list export failed: " . $e->getMessage());
        redirect('BomController/index');
    }
}

    private function get_cell_value($sheet, $col, $row) {
        $cell = $sheet->getCell($col . $row);
        if (!$cell) return '';
        try {
            $val = $cell->getCalculatedValue();
            if ($val instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                return trim($val->getPlainText());
            }
            return trim($val ?? '');
        } catch (Exception $e) {
            $val = $cell->getValue();
            if ($val instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                return trim($val->getPlainText());
            }
            return trim($val ?? '');
        }
    }

    public function ajax_import_bom_excel() {
        header('Content-Type: application/json');
        
        if (empty($_FILES['file']['name'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
            return;
        }

        require_once FCPATH . 'vendor/autoload.php';

        try {
            $inputFileName = $_FILES['file']['tmp_name'];
            
            $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if (!in_array($file_ext, ['xlsx', 'xls'])) {
                echo json_encode(['success' => false, 'message' => 'Please upload a valid Excel file (.xlsx or .xls)']);
                return;
            }

            // Copy file to uploads for diagnostic check
            if (!is_dir(FCPATH . 'uploads')) {
                mkdir(FCPATH . 'uploads', 0777, true);
            }
            copy($inputFileName, FCPATH . 'uploads/temp_import_bom.xlsx');

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getActiveSheet();
            
            $items = [];
            $highestRow = $sheet->getHighestRow();
            
            $header_found = false;
            $last_item_name = '';

            $last_item_code = '';
            $last_item_name = '';
            
            // Default column mapping for the new 10-column format
            $col_map = [
                'item_code'     => 'B',
                'product_name'  => 'C',
                'description'   => 'D',
                'quantity'      => 'E',
                'unit'          => 'F',
                'tag_no'        => 'G',
                'scope'         => 'H',
                'stores_remark' => 'I',
                'remark'        => 'J'
            ];

            for ($row = 1; $row <= $highestRow; $row++) {
                $cellA = $this->get_cell_value($sheet, 'A', $row);
                $cellB = $this->get_cell_value($sheet, 'B', $row);
                $cellC = $this->get_cell_value($sheet, 'C', $row);
                $cellD = $this->get_cell_value($sheet, 'D', $row);
                
                if (!$header_found) {
                    if (strcasecmp(trim($cellA), 'SR.NO') === 0 || strcasecmp(trim($cellB), 'ITEM CODE') === 0 || strcasecmp(trim($cellB), 'EQUIPMENT') === 0 || strcasecmp(trim($cellB), 'PRODUCT NAME') === 0) {
                        $header_found = true;
                        
                        // Check if it is the old 9-column format
                        $b_val = strtoupper(trim($cellB));
                        $d_val = strtoupper(trim($cellD));
                        
                        if ($b_val === 'EQUIPMENT' || $b_val === 'PRODUCT NAME' || $d_val === 'QTY') {
                            $col_map = [
                                'item_code'     => '', // No item code in old format
                                'product_name'  => 'B',
                                'description'   => 'C',
                                'quantity'      => 'D',
                                'unit'          => 'E',
                                'tag_no'        => 'F',
                                'scope'         => 'G',
                                'stores_remark' => 'H',
                                'remark'        => 'I'
                            ];
                        }
                    }
                    continue;
                }
                
                // Read values based on the mapped columns
                $itemCode = !empty($col_map['item_code']) ? $this->get_cell_value($sheet, $col_map['item_code'], $row) : '';
                $productName = $this->get_cell_value($sheet, $col_map['product_name'], $row);
                $desc = $this->get_cell_value($sheet, $col_map['description'], $row);
                $quantity = $this->get_cell_value($sheet, $col_map['quantity'], $row);
                $unit = $this->get_cell_value($sheet, $col_map['unit'], $row);
                
                $cellA_trimmed = trim($cellA);
                $itemCode_trimmed = trim($itemCode);
                $productName_trimmed = trim($productName);
                $desc_trimmed = trim($desc);
                $colA_is_numeric = is_numeric($cellA_trimmed) || preg_match('/^\d+\.?$/', $cellA_trimmed);
                
                $all_empty = empty($cellA_trimmed) && empty($itemCode_trimmed) && empty($productName_trimmed) && empty($desc_trimmed);
                
                if (!$all_empty && !$colA_is_numeric && empty($quantity) && empty($unit)) {
                    $heading_text = '';
                    if (!empty($productName_trimmed)) {
                        $heading_text = $productName_trimmed;
                    } elseif (!empty($itemCode_trimmed)) {
                        $heading_text = $itemCode_trimmed;
                    } elseif (!empty($desc_trimmed)) {
                        $heading_text = $desc_trimmed;
                    } else {
                        $heading_text = $cellA_trimmed;
                    }
                    
                    $items[] = [
                        'excel_row'    => $row,
                        'type'         => 'subheading',
                        'heading_text' => $heading_text,
                        'raw_name'     => $heading_text,
                        'description'  => $heading_text,
                        'quantity'     => '0',
                        'unit'         => '',
                        'tag_no'       => '',
                        'scope'        => '',
                        'stores_remark'=> '',
                        'remark'       => '',
                        'match_status' => 'subheading',
                        'matches'      => [],
                        'details_match'=> false,
                        'product_name' => '__HEADING__'
                    ];
                    $last_item_code = '';
                    $last_item_name = '';
                    continue;
                }
                
                // Aggregate sub-row descriptions if it's a single item with multiple description rows
                if (empty($cellA_trimmed) && empty($itemCode_trimmed) && empty($productName_trimmed) && !empty($desc_trimmed)) {
                    if (!empty($items)) {
                        $last_idx = count($items) - 1;
                        if ($items[$last_idx]['type'] === 'item') {
                            $tag_no = !empty($col_map['tag_no']) ? $this->get_cell_value($sheet, $col_map['tag_no'], $row) : '';
                            $scope = !empty($col_map['scope']) ? $this->get_cell_value($sheet, $col_map['scope'], $row) : '';
                            $remark = !empty($col_map['remark']) ? $this->get_cell_value($sheet, $col_map['remark'], $row) : '';
                            
                            $extra_info = [];
                            if (!empty($quantity) && $quantity != 0) {
                                $extra_info[] = "Qty: " . $quantity . (!empty($unit) ? " " . $unit : "");
                            }
                            if (!empty($tag_no)) {
                                $extra_info[] = "Tag: " . $tag_no;
                            }
                            if (!empty($scope)) {
                                $extra_info[] = "Scope: " . $scope;
                            }
                            if (!empty($remark)) {
                                $extra_info[] = "Remark: " . $remark;
                            }
                            
                            $append_str = $desc_trimmed;
                            if (!empty($extra_info)) {
                                $append_str .= " (" . implode(", ", $extra_info) . ")";
                            }
                            
                            $items[$last_idx]['description'] .= "\n" . $append_str;
                            continue;
                        }
                    }
                }
                
                // Code & Name Inheritance logic
                if (empty($itemCode_trimmed) && empty($productName_trimmed) && (!empty($quantity) || !empty($unit))) {
                    if (!empty($last_item_code) || !empty($last_item_name)) {
                        $itemCode = $last_item_code;
                        $productName = $last_item_name;
                        $itemCode_trimmed = $last_item_code;
                        $productName_trimmed = $last_item_name;
                    }
                }
                
                if ((!empty($itemCode_trimmed) || !empty($productName_trimmed)) && (!empty($quantity) || !empty($unit))) {
                    $last_item_code = $itemCode_trimmed;
                    $last_item_name = $productName_trimmed;
                    
                    $tag_no = !empty($col_map['tag_no']) ? $this->get_cell_value($sheet, $col_map['tag_no'], $row) : '';
                    $scope = !empty($col_map['scope']) ? $this->get_cell_value($sheet, $col_map['scope'], $row) : '';
                    $stores_remark = !empty($col_map['stores_remark']) ? strtoupper($this->get_cell_value($sheet, $col_map['stores_remark'], $row)) : '';
                    $remark = !empty($col_map['remark']) ? $this->get_cell_value($sheet, $col_map['remark'], $row) : '';
                    
                    if (empty($quantity)) {
                        $quantity = '1';
                    }
                    
                    $match_status = 'none';
                    $matches = [];
                    $details_match = false;
                    $pre_selected_code = '';

                    // Look for exact match in global inventory
                    $db_item = null;
                    if (!empty($col_map['item_code']) && !empty($itemCode_trimmed)) {
                        // For the new format with separate Item Code, matching must be strictly by Item Code
                        $db_item = $this->db->where('code', $itemCode_trimmed)
                                            ->get('inventory')
                                            ->row();
                    } else {
                        // For legacy format, match by Product Name
                        $db_item = $this->db->where('item_name', $productName_trimmed)
                                            ->get('inventory')
                                            ->row();
                    }
                    
                    if ($db_item && !empty(trim($db_item->code))) {
                        $match_status = 'exact';
                        $pre_selected_code = $db_item->code;
                        $matches[] = [
                            'code' => $db_item->code,
                            'item_name' => $db_item->item_name,
                            'unit' => $db_item->unit,
                            'prod_description' => $db_item->prod_description
                        ];
                        
                        // If the item is already available, we update it automatically and treat it as resolved (details_match = true)
                        $details_match = true;
                    } else {
                        // Look for partial matches in global inventory
                        $query = $this->db->group_start();
                        if (!empty($col_map['item_code']) && !empty($itemCode_trimmed)) {
                            $query->like('code', $itemCode_trimmed)
                                  ->or_like('item_name', $productName_trimmed);
                        } else {
                            $query->like('item_name', $productName_trimmed);
                        }
                        $db_matches = $query->group_end()
                                             ->limit(5)
                                             ->get('inventory')
                                             ->result();
                        if (!empty($db_matches)) {
                            $match_status = 'partial';
                            $pre_selected_code = $db_matches[0]->code;
                            foreach ($db_matches as $db_m) {
                                $matches[] = [
                                    'code' => $db_m->code,
                                    'item_name' => $db_m->item_name,
                                    'unit' => $db_m->unit,
                                    'prod_description' => $db_m->prod_description
                                ];
                            }
                        }
                    }

                    $items[] = [
                        'excel_row'     => $row,
                        'type'          => 'item',
                        'item_code'     => $itemCode,
                        'raw_name'      => $productName,
                        'description'   => $desc,
                        'quantity'      => $quantity,
                        'unit'          => $unit,
                        'tag_no'        => $tag_no,
                        'scope'         => $scope,
                        'stores_remark' => $stores_remark,
                        'remark'        => $remark,
                        'match_status'  => $match_status,
                        'matches'       => $matches,
                        'details_match' => $details_match,
                        'product_name'  => $pre_selected_code
                    ];
                }
            }
            
            if (empty($items)) {
                echo json_encode(['success' => false, 'message' => 'No valid item rows found. Please make sure your sheet has a header row and valid items.']);
                return;
            }

            echo json_encode(['success' => true, 'items' => $items]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error reading Excel file: ' . $e->getMessage()]);
        }
    }

    public function ajax_confirm_import_bom() {
        header('Content-Type: application/json');
        
        $mappings_json = $this->input->post('mappings');
        if (empty($mappings_json)) {
            echo json_encode(['success' => false, 'message' => 'No mappings provided']);
            return;
        }
        
        $mappings = null;
        try {
            $mappings = json_decode($mappings_json, true);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Invalid mappings JSON']);
            return;
        }
        
        if (!is_array($mappings)) {
            echo json_encode(['success' => false, 'message' => 'Invalid mappings format']);
            return;
        }
        
        $new_products = [];
        $final_items = [];
        
        $date_added = date("Y-m-d");
        $date_updated = date("d-m-Y");
        
        foreach ($mappings as $map) {
            $action = $map['action'] ?? 'CREATE'; // CREATE, USE_DB, UPDATE_DB, SUBHEADING, SKIP
            $excel_item = $map['excel_item'] ?? null;
            $db_code = $map['db_code'] ?? '';
            
            if (!$excel_item) continue;

            if ($action === 'SKIP') {
                continue;
            }
            
            // Pass through sub-heading rows without inventory lookup
            if (($excel_item['type'] ?? '') === 'subheading' || ($excel_item['product_name'] ?? '') === '__HEADING__' || $action === 'SUBHEADING' || $db_code === '__HEADING__') {
                $final_items[] = [
                    'product_name'  => '__HEADING__',
                    'raw_name'      => $excel_item['heading_text'] ?? $excel_item['raw_name'] ?? '',
                    'description'   => $excel_item['heading_text'] ?? $excel_item['description'] ?? '',
                    'quantity'      => '0',
                    'unit'          => '',
                    'tag_no'        => '',
                    'scope'         => '',
                    'stores_remark'=> '',
                    'remark'       => ''
                ];
                continue;
            }
            
            $item_code = trim($excel_item['item_code'] ?? '');
            $raw_name = trim($excel_item['raw_name'] ?? '');
            $desc = trim($excel_item['description'] ?? '');
            $unit = trim($excel_item['unit'] ?? '');
            
            $product_code = '';
            
            if ($action === 'CREATE' || empty($db_code)) {
                $exist_item = null;
                if (!empty($item_code)) {
                    // Search strictly by Code globally across all inventory
                    $exist_item = $this->db->where('code', $item_code)
                                           ->get('inventory')
                                           ->row();
                    
                    if (!$exist_item) {
                        // If not found by Code, check if same Name has an empty Code
                        $name_match = $this->db->where('item_name', $raw_name)
                                               ->get('inventory')
                                               ->row();
                        if ($name_match && empty(trim($name_match->code))) {
                            $this->db->where('id', $name_match->id)
                                     ->update('inventory', ['code' => $item_code]);
                            $exist_item = $this->db->where('id', $name_match->id)->get('inventory')->row();
                        }
                    }
                } else {
                    $exist_item = $this->db->where('item_name', $raw_name)
                                           ->get('inventory')
                                           ->row();
                }
                
                if ($exist_item && !empty(trim($exist_item->code))) {
                    $product_code = $exist_item->code;
                    // Keep existing DB details
                    $desc = !empty($exist_item->prod_description) ? $exist_item->prod_description : $desc;
                    $unit = !empty($exist_item->unit) ? $exist_item->unit : $unit;
                } else {
                    $product_code = !empty($item_code) ? $item_code : $raw_name;
                    
                    // Extra global check by code to avoid duplicate key crash
                    $global_check = $this->db->where('code', $product_code)->get('inventory')->row();
                    if ($global_check) {
                        $product_code = $global_check->code;
                        $desc = !empty($global_check->prod_description) ? $global_check->prod_description : $desc;
                        $unit = !empty($global_check->unit) ? $global_check->unit : $unit;
                    } else {
                        // Check if already created in this request batch
                        $already_created = false;
                        foreach ($new_products as $np) {
                            if ($np['code'] === $product_code) {
                                $already_created = true;
                                break;
                            }
                        }
                        
                        if (!$already_created) {
                            // Create new product safely
                            $data_inventory = array(
                                'item_name'        => $raw_name,
                                'prod_description' => $desc,
                                'code'             => $product_code,
                                'hsn'              => '',
                                'unit'             => $unit,
                                'gst_per'          => '',
                                'stock'            => 0,
                                'cost_price'       => 0,
                                'sell_price'       => 0,
                                'date_added'       => $date_added,
                                'date_modified'    => $date_updated,
                                'uid'              => $this->user_id
                            );
                            
                            try {
                                @$this->db->insert('inventory', $data_inventory);
                                $new_products[] = [
                                    'code' => $product_code,
                                    'item_name' => $raw_name
                                ];
                            } catch (\Throwable $e) {
                                log_message('error', 'Inventory insert duplicate absorbed: ' . $e->getMessage());
                            }
                        }
                    }
                }
            } else {
                // Using existing database item
                $product_code = $db_code;
                
                // Update the details of the database item automatically with Excel's details
                $this->db->where('code', $db_code)
                         ->update('inventory', [
                             'prod_description' => $desc,
                             'unit'             => $unit,
                             'date_modified'    => $date_updated
                         ]);
            }
            
            $final_items[] = [
                'product_name' => $product_code,
                'raw_name'     => $raw_name,
                'description'  => $desc,
                'quantity'     => $excel_item['quantity'] ?? '1',
                'unit'         => $unit,
                'tag_no'       => $excel_item['tag_no'] ?? '',
                'scope'        => $excel_item['scope'] ?? '',
                'stores_remark'=> $excel_item['stores_remark'] ?? '',
                'remark'       => $excel_item['remark'] ?? ''
            ];
        }
        
        echo json_encode([
            'success' => true,
            'items' => $final_items,
            'new_products' => $new_products
        ]);
    }

    // =========================================================
    // BOM APPROVAL WORKFLOW CONTROLLER METHODS
    // =========================================================

    /**
     * Submit a BOM for Sales approval
     * URL: BomController/submit_bom_for_approval/{bom_total_id}
     */
    public function submit_bom_for_approval()
    {
        $id = $this->uri->segment(3);
        if (empty($id)) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid BOM ID.');
            redirect('BomController/index');
        }

        $bom_total = $this->db->where('id', $id)->get('bom_total')->row();
        if (!$bom_total) {
            $this->session->set_flashdata('ERRORMSG', 'BOM not found.');
            redirect('BomController/index');
        }

        // Only Pending (0), Draft (1), or Rejected (5) BOMs can be submitted
        if (!in_array($bom_total->status, [0, 1, 5])) {
            $this->session->set_flashdata('ERRORMSG', 'This BOM has already been submitted or approved.');
            redirect('BomController/index');
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $employee_user_id = $session_data_head['result']['user_id'] ?? 0;
        $result = $this->bom->submit_bom_for_approval($bom_total->number_fk, $id, $this->user_id, $employee_user_id);

        if ($result === 'auto_approved') {
            $this->session->set_flashdata('SUCCESSMSG', 'No approval rules configured for BOM — auto-approved!');
        } else {
            $this->session->set_flashdata('SUCCESSMSG', 'BOM submitted for approval successfully!');
        }
        redirect('BomController/index');
    }

    /**
     * Approve or Reject a BOM approval row
     * URL: BomController/process_bom_approval (POST)
     */
    public function process_bom_approval()
    {
        $approval_id = (int) $this->input->post('approval_id');
        $action      = $this->input->post('action'); // 'approved' or 'rejected'
        $remarks     = trim($this->input->post('remarks') ?? '');
        $session_data_head = $this->session->userdata('session_data_head');
        $actor_name = $session_data_head['result']['username'] ?? 'Unknown';
        $employee_user_id = $session_data_head['result']['user_id'] ?? 0;

        if (!in_array($action, ['approved', 'rejected'])) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid action.');
            redirect('BomController/bom_approval_dashboard');
        }

        // Get user roles
        $user_role_id = $session_data_head['result']['role'] ?? null;
        $user_role_name = $session_data_head['result']['role_name'] ?? '';
        $user_roles_list = array($user_role_name);

        // Also fetch multi-roles from user_roles table (uses user_id and role_name directly)
        $multi_roles = $this->db->select('role_name')
                                ->from('user_roles')
                                ->where('user_id', $session_data_head['result']['user_id'])
                                ->where('is_active', 1)
                                ->get()->result_array();
        foreach ($multi_roles as $mr) {
            if (!in_array($mr['role_name'], $user_roles_list)) {
                $user_roles_list[] = $mr['role_name'];
            }
        }

        // Check permission
        if (!$this->bom->can_user_approve_bom($approval_id, $user_roles_list, $this->user_id)) {
            $this->session->set_flashdata('ERRORMSG', 'You do not have permission to approve this BOM.');
            redirect('BomController/bom_approval_dashboard');
        }

        $result = $this->bom->process_bom_approval_action($approval_id, $action, $remarks, $actor_name, $this->user_id, $employee_user_id);

        if ($result === 'approved') {
            $this->session->set_flashdata('SUCCESSMSG', 'BOM fully approved and automatically sent to MRP!');
        } elseif ($result === 'next_level') {
            $this->session->set_flashdata('SUCCESSMSG', 'Level approved. Waiting for next approver.');
        } elseif ($result === 'rejected') {
            $this->session->set_flashdata('ERRORMSG', 'BOM has been rejected.');
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Action could not be processed.');
        }
        redirect('BomController/bom_approval_dashboard');
    }

    /**
     * BOM Approvals Dashboard — shows all pending BOMs for the logged-in approver role
     * URL: BomController/bom_approval_dashboard
     */
    public function bom_approval_dashboard()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $user_role_name    = $session_data_head['result']['role_name'] ?? '';

        // Fetch all pending rows for this role
        $data['pending_approvals'] = $this->bom->get_pending_bom_approvals_for_role($user_role_name, $this->user_id);
        $data['approval_history']  = $this->bom->get_bom_approval_history($this->user_id);
        $data['settings']          = $this->login->get_settings($this->user_id);

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bom/bom_approval_dashboard', $data);
    }

    /**
     * AI Draft-Stall Triage Dashboard
     * URL: BomController/draft_triage
     */
    public function draft_triage()
    {
        redirect('AiController/bom_triage');
    }

}