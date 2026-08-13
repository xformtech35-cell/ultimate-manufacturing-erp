<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MaterialIssueController extends MY_Controller
{

    private $model;
    private $uid;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Material_issue_model');
        $this->model = $this->Material_issue_model;

        $this->load->model('Joborder');
        $this->load->model('Project_model');

        // Check login - redirect to logout if not logged in
        $session_data_head = $this->session->userdata('session_data_head');
        $this->uid = isset($session_data_head['result']['user_id']) ? (int)$session_data_head['result']['user_id'] : null;

        if ($this->uid === null) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }

        // Load helpers
        $this->load->helper('url', 'form');
        $this->load->library('form_validation');
    }

    /**
     * Index page - List all material issue slips
     */
    public function index()
    {
        // Load required models
        $this->load->model('login');

        $data = array();

        // Get filters from GET parameters
        $filters = array();
        if ($this->input->get('date_from')) {
            $d = DateTime::createFromFormat('d-m-Y', $this->input->get('date_from'));
            $filters['date_from'] = $d ? $d->format('Y-m-d') : $this->input->get('date_from');
        }
        if ($this->input->get('date_to')) {
            $d = DateTime::createFromFormat('d-m-Y', $this->input->get('date_to'));
            $filters['date_to'] = $d ? $d->format('Y-m-d') : $this->input->get('date_to');
        }
        if ($this->input->get('status')) {
            $filters['status'] = $this->input->get('status');
        }
        if ($this->input->get('issued_to')) {
            $filters['issued_to'] = $this->input->get('issued_to');
        }
        if ($this->input->get('department')) {
            $filters['department'] = $this->input->get('department');
        }
        if ($this->input->get('project_code')) {
            $filters['project_code'] = $this->input->get('project_code');
        }

        // Get settings
        $data['settings'] = $this->login->get_settings($this->uid);
        
        // Get dropdown data for filters
        $data['departments'] = $this->model->get_unique_departments();
        $data['projects'] = $this->Project_model->get_all_projects();
        $data['users'] = $this->model->get_unique_issued_to();

        $data['issue_slips'] = $this->model->get_issue_slips($filters);
        $data['filters'] = $filters;

        // Load header
        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/list', $data);
    }

    /**
     * Get material issue slips by month and year
     */
    public function get_datewise_record()
    {
        $this->load->model('login');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        $data = array();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['settings'] = $this->login->get_settings($this->uid);
        $data['departments'] = $this->model->get_unique_departments();
        $data['projects'] = $this->Project_model->get_all_projects();
        $data['users'] = $this->model->get_unique_issued_to();
        $data['issue_slips'] = $this->model->get_datewise_record($from_date, $to_date, $this->uid);
        $data['filters'] = array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', is_array($session_data_head) ? $session_data_head : array());
        $this->load->view('material_issue/list', $data);
    }

    public function get_monthyearwise_record()
    {
        // Load required models
        $this->load->model('login');

        $month_year = $this->input->post('month_year');
        
        $data = array();
        $data['settings'] = $this->login->get_settings($this->uid);
        
        // Get dropdown data for filters
        $data['departments'] = $this->model->get_unique_departments();
        $data['projects'] = $this->Project_model->get_all_projects();
        $data['users'] = $this->model->get_unique_issued_to();
        
        // Get data for the selected month
        $data['issue_slips'] = $this->model->get_monthyearwise_record($month_year, $this->uid);
        $data['filters'] = array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', is_array($session_data_head) ? $session_data_head : array());
        $this->load->view('material_issue/list', $data);
    }

    /**
     * Create new material issue slip
     */
    public function create()
    {

        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Form validation
            $this->form_validation->set_rules('issue_date', 'Issue Date', 'required');
            $this->form_validation->set_rules('issued_to', 'Issued To', 'required|trim');
            $this->form_validation->set_rules('project_code', 'Project Code');
            $this->form_validation->set_rules('inventory_id[]', 'Items', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantity', 'required');

            if ($this->form_validation->run() == FALSE) {
                // Validation failed
                $this->session->set_flashdata('ERRORMSG', validation_errors());
                redirect('MaterialIssueController/create');
            } else {
                // Get form data
                $raw_date = $this->input->post('issue_date');
                $issue_date = DateTime::createFromFormat('d-m-Y', $raw_date);
                $issue_data = array(
                    'issue_date' => $issue_date ? $issue_date->format('Y-m-d') : date('Y-m-d'),
                    'issued_to' => $this->input->post('issued_to'),
                    'department' => $this->input->post('department'),
                    'project_code' => $this->input->post('project_code') ?? '',
                    'purpose' => $this->input->post('purpose'),
                    'remarks' => $this->input->post('remarks'),
                    'status' => 'draft',
                    'uid' => $this->uid
                );





                // Add job order number if column exists
                if ($this->db->field_exists('joborder_number', 'material_issue_slips')) {
                    $issue_data['joborder_number'] = $this->input->post('joborder_number');
                }

                // Debug: Log the issue data
                log_message('debug', 'Material Issue Data: ' . json_encode($issue_data));

                // Get items data
                $items_data = array();
                $inventory_ids = $this->input->post('inventory_id');
                $quantities = $this->input->post('quantity');
                $item_remarks = $this->input->post('item_remarks');
                $pending_bases = $this->input->post('pending_base');

                // Read joborder_number
                $joborder_number = $this->input->post('joborder_number');

                // Read overrun POST fields sent from the view
                $bom_qtys             = $this->input->post('bom_qty');
                $allowed_overrun_pcts = $this->input->post('allowed_overrun_pct_item');
                $allowed_overrun_qtys = $this->input->post('allowed_overrun_qty_item');
                $max_allowed_qtys     = $this->input->post('max_allowed_qty_item');
                $item_rates           = $this->input->post('item_price');

                // Track whether any item needs manager approval
                $has_approval_required = false;
                $items_data = array();

                for ($i = 0; $i < count($inventory_ids); $i++) {
                    if (!empty($inventory_ids[$i]) && !empty($quantities[$i]) && $quantities[$i] > 0) {
                        $qty         = floatval($quantities[$i]);
                        $bom_qty     = isset($bom_qtys[$i]) ? floatval($bom_qtys[$i]) : 0;
                        $rate        = isset($item_rates[$i]) ? floatval($item_rates[$i]) : 0;

                        // ── 1. STOCK VALIDATION (Requirement 10) ──
                        $inv_row = $this->db->select('stock, code, item_name, allowed_overrun_pct')
                                            ->where('inventory_id', $inventory_ids[$i])
                                            ->get('inventory')
                                            ->row_array();

                        $available_stock = $inv_row ? floatval($inv_row['stock']) : 0;
                        if ($qty > $available_stock) {
                            $item_name_str = $inv_row ? $inv_row['item_name'] : ('Item #' . ($i + 1));
                            $this->session->set_flashdata('ERRORMSG', "Cannot issue '{$item_name_str}'. Requested quantity ({$qty}) exceeds available stock ({$available_stock}).");
                            redirect('MaterialIssueController/create');
                            return;
                        }

                        // ── 2. CUMULATIVE MI QUANTITY (Requirement 5 & 6) ──
                        // Excludes cancelled slips automatically via model method
                        $prev_issued_qty = $this->model->get_issued_quantity_for_inventory($inventory_ids[$i], $joborder_number);
                        $total_mi_qty    = $prev_issued_qty + $qty;

                        // ── 3. OVERRUN PERCENTAGE TOLERANCE ──
                        // Check BOM override first, fallback to inventory setting
                        $bom_override = null;
                        if (!empty($inv_row['code'])) {
                            $bom_override = $this->db->select('allowed_overrun_pct')
                                                     ->where('product_name', $inv_row['code'])
                                                     ->where('allowed_overrun_pct IS NOT NULL', null, false)
                                                     ->order_by('bom_id', 'DESC')
                                                     ->limit(1)
                                                     ->get('bom')
                                                     ->row_array();
                        }
                        if ($bom_override && $bom_override['allowed_overrun_pct'] !== null) {
                            $allowed_overrun_pct = floatval($bom_override['allowed_overrun_pct']);
                        } else {
                            $allowed_overrun_pct = $inv_row ? floatval($inv_row['allowed_overrun_pct']) : 2.00;
                        }

                        $allowed_overrun_qty = round($bom_qty * $allowed_overrun_pct / 100, 4);
                        $max_allowed_qty     = round($bom_qty + $allowed_overrun_qty, 4);

                        // ── 4. OVERRUN CALCULATIONS BASED ON CUMULATIVE MI ──
                        $overrun_qty        = max(0, round($total_mi_qty - $bom_qty, 4));
                        $overrun_pct_actual = ($bom_qty > 0 && $total_mi_qty > $bom_qty) ? round(($total_mi_qty - $bom_qty) / $bom_qty * 100, 4) : 0;
                        $overrun_value      = round($overrun_qty * $rate, 4);

                        // ── 5. OVERRUN STATUS DECISION ──
                        if ($bom_qty <= 0 || $total_mi_qty <= $bom_qty) {
                            // No overrun
                            $overrun_status = 'none';
                        } elseif ($total_mi_qty <= $max_allowed_qty) {
                            // Overrun exists but within allowed tolerance
                            $overrun_status = 'within_limit';
                        } else {
                            // Overrun exceeds allowed tolerance → requires approval
                            $overrun_status = 'approval_required';
                            $has_approval_required = true;
                        }

                        $items_data[] = array(
                            'inventory_id_fk'     => $inventory_ids[$i],
                            'quantity'            => $qty,
                            'unit_price'          => $rate,
                            'pending_qty'         => max(0, (isset($pending_bases[$i]) ? floatval($pending_bases[$i]) : 0) - $qty),
                            'remarks'             => isset($item_remarks[$i]) ? $item_remarks[$i] : '',
                            // Overrun & Cumulative fields stored on item record
                            'bom_qty'             => $bom_qty,
                            'previous_issued_qty' => $prev_issued_qty,
                            'total_mi_qty'        => $total_mi_qty,
                            'allowed_overrun_pct' => $allowed_overrun_pct,
                            'allowed_overrun_qty' => $allowed_overrun_qty,
                            'max_allowed_qty'     => $max_allowed_qty,
                            'overrun_qty'         => $overrun_qty,
                            'overrun_pct_actual'  => $overrun_pct_actual,
                            'overrun_value'       => $overrun_value,
                            'overrun_status'      => $overrun_status
                        );
                    }
                }

                log_message('debug', 'Processed items data: ' . json_encode($items_data));

                // Validate items
                if (empty($items_data)) {
                    $this->session->set_flashdata('ERRORMSG', 'Please add at least one item');
                    redirect('MaterialIssueController/create');
                }
                



                // Create issue slip
                $success = $this->model->create_issue_slip($issue_data, $items_data);


                $this->db->set('material_issue_status', 1);
                $this->db->where('number_fk', 
                $this->input->post('joborder_number')
                );
                $this->db->update('joborder_total');

                if ($success) {
                    // Debug: Success message
                    log_message('debug', 'Material Issue Slip created successfully with ID: ' . $this->db->insert_id());
                    $this->session->set_flashdata('SUCCESSMSG', 'Material Issue Slip created successfully');
                    redirect('MaterialIssueController/index');
                } else {
                    // Debug: Error message
                    log_message('error', 'Failed to create Material Issue Slip. DB Error: ' . $this->db->error()['message']);
                    $this->session->set_flashdata('ERRORMSG', 'Failed to create Material Issue Slip');
                    redirect('MaterialIssueController/create');
                }
            }
        }

        // Get inventory items for dropdown
        $data['inventory_items'] = $this->model->get_inventory_items_with_stock();
        // Get job order list for JO dropdown — exclude fully completed job orders
        $data['joborders'] = $this->Joborder->get_joborders_with_pending($this->uid);
        // Get project list for Project Code dropdown
        $data['projects'] = $this->Project_model->get_all_projects();

        // Load view
        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/create', $data);
    }

    /**
     * Get job order items for a selected JO number (AJAX)
     */
    public function get_joborder_items($joborder_number = null)
    {
        header('Content-Type: application/json');
        
        try {
            if (!$joborder_number) {
                $joborder_number = $this->input->get('number');
            }

            if (!$joborder_number) {
                echo json_encode(array('status' => 'error', 'message' => 'Job order number is required'));
                return;
            }

            // Fetch Job Order items (finished goods)
            $items = $this->Joborder->get_joborder_data($joborder_number, $this->uid);
            
            if (!$items) {
                echo json_encode(array('status' => 'error', 'message' => 'No job order found or no items in this job order', 'data' => array()));
                return;
            }

            $response = array();

            foreach ($items as $item) {
                if (isset($item->product_name) && $item->product_name === '__HEADING__') {
                    continue;
                }
                // resolve inventory reference by product_name (which contains the item code)
                $inventory = $this->model->get_inventory_item_by_code($item->product_name);
                
                $gross_issued_qty = 0;
                $returned_qty = 0;
                $net_issued_qty = 0;
                $out_qty = 0;
                $pending = floatval($item->quantity);

                if ($inventory) {
                    $detailed_qtys = $this->model->get_detailed_issued_quantities($inventory['inventory_id'], $joborder_number);
                    $gross_issued_qty = $detailed_qtys['gross_issued'];
                    $returned_qty = $detailed_qtys['returned'];
                    $net_issued_qty = $detailed_qtys['net_issued'];
                    $out_qty = $net_issued_qty;
                    $pending = max(0, floatval($item->quantity) - floatval($out_qty));
                }

                $available_stock = $inventory ? floatval($inventory['stock'] - $out_qty) : 0;
                $joborder_pending = max(0, floatval($item->quantity) - floatval($out_qty));
                
                // Use sell_price, fallback to cost_price if not available
                $unit_price = 0;
                if ($inventory) {
                    $unit_price = !empty($inventory['sell_price']) ? floatval($inventory['sell_price']) : floatval($inventory['cost_price']);
                }

                // ── OVERRUN CONTROL: compute allowed overrun fields for this item ──
                $bom_qty_val = floatval($item->quantity); // BOM required qty
                
                // 1. Check if BOM item has specific overrun_pct override
                $bom_override = null;
                if (!empty($inventory['code'])) {
                    $bom_override = $this->db->select('allowed_overrun_pct')
                                             ->where('product_name', $inventory['code'])
                                             ->where('allowed_overrun_pct IS NOT NULL', null, false)
                                             ->order_by('bom_id', 'DESC')
                                             ->limit(1)
                                             ->get('bom')
                                             ->row_array();
                }
                if ($bom_override && $bom_override['allowed_overrun_pct'] !== null) {
                    $allowed_overrun_pct = floatval($bom_override['allowed_overrun_pct']);
                } else {
                    $allowed_overrun_pct = $inventory ? floatval($inventory['allowed_overrun_pct'] ?? 2.00) : 2.00;
                }

                $allowed_overrun_qty  = round($bom_qty_val * $allowed_overrun_pct / 100, 4);
                $max_allowed_qty      = round($bom_qty_val + $allowed_overrun_qty, 4);

                $response[] = array(
                    'item_code'           => $item->product_name,
                    'item_name'           => isset($inventory['item_name']) ? $inventory['item_name'] : $item->product_name,
                    'inventory_id'        => $inventory ? $inventory['inventory_id'] : '',
                    'required_qty'        => $bom_qty_val,
                    'out_qty'             => floatval($out_qty), // previous_issued_qty
                    'gross_issued_qty'    => $gross_issued_qty,
                    'returned_qty'        => $returned_qty,
                    'net_issued_qty'      => $net_issued_qty,
                    'pending_qty'         => $joborder_pending,
                    'stock'               => $available_stock,
                    'joborder_pending'    => $joborder_pending,
                    'price'               => $unit_price,
                    'unit'                => $inventory ? $inventory['unit'] : '',
                    // Overrun fields
                    'allowed_overrun_pct' => $allowed_overrun_pct,
                    'allowed_overrun_qty' => $allowed_overrun_qty,
                    'max_allowed_qty'     => $max_allowed_qty
                );
            }

            echo json_encode(array('status' => 'success', 'data' => $response));
        } catch (Exception $e) {
            echo json_encode(array('status' => 'error', 'message' => 'Error: ' . $e->getMessage()));
        }
    }

    /**
     * Get item details including price (AJAX)
     */
    public function get_item_price()
    {
        header('Content-Type: application/json');
        
        try {
            $inventory_id = $this->input->get('inventory_id');
        
        if (!$inventory_id) {
            echo json_encode(array('status' => 'error', 'message' => 'Inventory ID is required'));
            return;
        }

            $item = $this->model->get_item_details($inventory_id);
            
            if ($item) {
                $unit_price = !empty($item['sell_price']) ? floatval($item['sell_price']) : floatval($item['cost_price']);
                echo json_encode(array(
                    'status' => 'success',
                    'price' => $unit_price,
                    'stock' => floatval($item['stock']),
                    'item_name' => $item['item_name']
                ));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Item not found'));
            }
        } catch (Exception $e) {
            echo json_encode(array('status' => 'error', 'message' => 'Error: ' . $e->getMessage()));
        }
    }

    private function prepare_slip_items($issue_slip)
    {
        $items = $issue_slip['items'] ?? [];
        $joborder_number = $issue_slip['joborder_number'] ?? null;

        if (!empty($joborder_number)) {
            // Fetch all items from the Job Order
            $joborder_items = $this->db
                ->select('j.*, MIN(i.inventory_id) as inventory_id, MIN(i.stock) as current_stock, MIN(i.unit) as unit, MIN(i.item_name) as item_name, MIN(i.code) as code')
                ->from('joborder j')
                ->join('inventory i', 'i.code = j.product_name OR i.item_name = j.product_name', 'left')
                ->where('j.number', $joborder_number)
                ->group_by('j.joborder_id')
                ->get()
                ->result_array();

            $issued_items_map = [];
            foreach ($items as $item) {
                $issued_items_map[$item['inventory_id_fk']] = $item;
            }

            $merged_items = [];
            foreach ($joborder_items as $jo_item) {
                $inv_id = $jo_item['inventory_id'] ?? null;
                if (!$inv_id) {
                    continue;
                }

                // Get total issued quantity for this item across all slips
                $issued_qty = floatval($this->model->get_issued_quantity_for_inventory($inv_id, $joborder_number));
                $required_qty = floatval($jo_item['quantity']);
                $pending_qty = max(0, $required_qty - $issued_qty);

                if (isset($issued_items_map[$inv_id])) {
                    $item_data = $issued_items_map[$inv_id];
                } else {
                    $item_data = [
                        'issue_item_id' => 0,
                        'issue_id' => $issue_slip['issue_id'],
                        'inventory_id_fk' => $inv_id,
                        'quantity' => 0.00,
                        'unit_price' => 0.00,
                        'total_amount' => 0.00,
                        'remarks' => '',
                        'code' => $jo_item['code'] ?? $jo_item['product_name'],
                        'item_name' => $jo_item['item_name'] ?? $jo_item['product_name'],
                        'unit' => $jo_item['unit'] ?? 'QTY',
                        'current_stock' => $jo_item['current_stock'] ?? 0.00
                    ];
                }

                $item_data['required_qty'] = $required_qty;
                $item_data['fulfilled_qty'] = $issued_qty;
                $item_data['pending_qty'] = $pending_qty;
                $item_data['current_stock'] = $jo_item['current_stock'] ?? 0.00;

                $merged_items[] = $item_data;
            }

            return $merged_items;
        } else {
            // No job order, keep standard behavior and calculate stock/pending
            foreach ($items as $index => $item) {
                $current_stock = isset($item['current_stock']) ? floatval($item['current_stock']) : 0;
                $issued_qty = floatval($item['quantity']);
                $pending_qty = max(0, $current_stock - $issued_qty);

                $items[$index]['required_qty'] = 0;
                $items[$index]['fulfilled_qty'] = $issued_qty;
                $items[$index]['pending_qty'] = $pending_qty;
            }
            return $items;
        }
    }

    /**
     * View material issue slip details
     */
    public function view($issue_id)
    {
        $data['issue_slip'] = $this->model->get_issue_slip($issue_id);

        if (!$data['issue_slip']) {
            show_404();
        }

        // Populate required, fulfilled and pending quantities for each item
        $data['issue_slip']['items'] = $this->prepare_slip_items($data['issue_slip']);

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/view', $data);
    }

    /**
     * Print material issue slip
     */
    public function print_slip($issue_id)
    {
        $this->load->model('login');

        $data['issue_slip'] = $this->model->get_issue_slip($issue_id);
        $data['settings'] = $this->login->get_settings($this->uid);

        if (!$data['issue_slip']) {
            show_404();
        }

        // Populate required, fulfilled and pending quantities for print as well
        $data['issue_slip']['items'] = $this->prepare_slip_items($data['issue_slip']);
        $data['items'] = $data['issue_slip']['items'];
        $data['total_qty'] = $data['issue_slip']['total_qty'];
        $data['items_count'] = count($data['items']);

        $this->load->view('material_issue/print', $data);
    }

    /**
     * Approve material issue slip
     */
    public function approve($issue_id)
    {
        $approved_by = $this->uid ?: 1;
        $success = $this->model->approve_issue_slip($issue_id, $approved_by);

        if ($success) {
            $this->session->set_flashdata('SUCCESSMSG', 'Material Issue Slip approved and marked as Issued successfully');
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Failed to approve Material Issue Slip. It may already be approved.');
        }

        redirect('MaterialIssueController/index');
    }

    /**
     * Cancel material issue slip
     */
    public function cancel($issue_id)
    {
        $remarks = $this->input->post('cancel_remarks');

        if (empty($remarks)) {
            $this->session->set_flashdata('ERRORMSG', 'Please provide reason for cancellation');
            redirect('MaterialIssueController/view/' . $issue_id);
        }

        $success = $this->model->cancel_issue_slip($issue_id, $remarks);

        if ($success) {
            $this->session->set_flashdata('SUCCESSMSG', 'Material Issue Slip cancelled successfully');
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Failed to cancel Material Issue Slip');
        }

        redirect('MaterialIssueController/view/' . $issue_id);
    }

    /**
     * Approve overrun on a material issue slip (Manager action)
     * Sets overrun_status = 'approved' on all approval_required items,
     * then changes slip status to 'draft' so normal stock deduction can proceed.
     */
    public function approve_overrun($issue_id)
    {
        $remarks = $this->input->post('overrun_remarks') ?: 'Approved by manager';

        // Update all approval_required items on this slip to approved
        $this->db->set('overrun_status', 'approved')
                 ->set('overrun_remarks', $remarks)
                 ->set('overrun_approved_by', $this->uid)
                 ->where('issue_id', $issue_id)
                 ->where('overrun_status', 'approval_required')
                 ->update('material_issue_items');

        // Check if any items still pending approval
        $still_pending = $this->db->where('issue_id', $issue_id)
                                  ->where('overrun_status', 'approval_required')
                                  ->count_all_results('material_issue_items');

        if ($still_pending == 0) {
            // All overruns approved — unlock slip back to draft for normal processing
            $this->db->set('status', 'draft')
                     ->where('issue_id', $issue_id)
                     ->update('material_issue_slips');
        }

        $this->session->set_flashdata('SUCCESSMSG', 'Overrun approved. Material Issue Slip is now active.');
        redirect('MaterialIssueController/view/' . $issue_id);
    }

    /**
     * Reject overrun on a material issue slip (Manager action)
     * Sets overrun_status = 'rejected' on all approval_required items,
     * then cancels the slip — no stock is deducted.
     */
    public function reject_overrun($issue_id)
    {
        $remarks = $this->input->post('overrun_remarks') ?: 'Rejected by manager';

        // Update all approval_required items to rejected
        $this->db->set('overrun_status', 'rejected')
                 ->set('overrun_remarks', $remarks)
                 ->set('overrun_approved_by', $this->uid)
                 ->where('issue_id', $issue_id)
                 ->where('overrun_status', 'approval_required')
                 ->update('material_issue_items');

        // Cancel the slip — no stock deduction
        $this->db->set('status', 'cancelled')
                 ->where('issue_id', $issue_id)
                 ->update('material_issue_slips');

        $this->session->set_flashdata('ERRORMSG', 'Overrun rejected. Material Issue Slip has been cancelled.');
        redirect('MaterialIssueController/view/' . $issue_id);
    }

    /**
     * Delete material issue slip
     */
    public function delete($issue_id)
    {
        $success = $this->model->delete_issue_slip($issue_id);

        if ($success) {
            $this->session->set_flashdata('SUCCESSMSG', 'Material Issue Slip deleted successfully');
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Failed to delete Material Issue Slip');
        }

        redirect('MaterialIssueController/index');
    }

    /**
     * Stock summary report
     */
    public function stock_summary()
    {
        $filters = array();
        if ($this->input->get('category_id')) {
            $filters['category_id'] = $this->input->get('category_id');
        }
        if ($this->input->get('group_id')) {
            $filters['group_id'] = $this->input->get('group_id');
        }
        if ($this->input->get('item_type')) {
            $filters['item_type'] = $this->input->get('item_type');
        }
        if ($this->input->get('low_stock')) {
            $filters['low_stock'] = true;
        }
        if ($this->input->get('out_of_stock')) {
            $filters['out_of_stock'] = true;
        }

        $data['stock_items'] = $this->model->get_stock_summary($filters);
        $data['filters'] = $filters;

        // Get categories and groups for filter dropdowns
        $data['categories'] = $this->model->get_categories();
        $data['groups'] = $this->model->get_groups();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', is_array($session_data_head) ? $session_data_head : array());
        $this->load->view('material_issue/stock_summary', $data);
    }

    /**
     * Stock verification
     */
    public function stock_verification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Form validation
            $this->form_validation->set_rules('verification_date', 'Verification Date', 'required');
            $this->form_validation->set_rules('physical_stock[]', 'Physical Stock', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('ERRORMSG', validation_errors());
                redirect('MaterialIssueController/stock_verification');
            } else {
                $verification_data = array(
                    'verification_date' => $this->input->post('verification_date'),
                    'remarks' => $this->input->post('remarks'),
                    'status' => 'draft'
                );

                $items_data = array();
                $inventory_ids = $this->input->post('inventory_id');
                $system_stocks = $this->input->post('system_stock');
                $physical_stocks = $this->input->post('physical_stock');
                $unit_prices = $this->input->post('unit_price');

                for ($i = 0; $i < count($inventory_ids); $i++) {
                    if (!empty($inventory_ids[$i])) {
                        $variance = $physical_stocks[$i] - $system_stocks[$i];
                        $items_data[] = array(
                            'inventory_id_fk' => $inventory_ids[$i],
                            'system_stock' => $system_stocks[$i],
                            'physical_stock' => $physical_stocks[$i],
                            'variance' => $variance,
                            'unit_price' => $unit_prices[$i]
                        );
                    }
                }

                $verification_id = $this->model->create_stock_verification($verification_data, $items_data);

                if ($verification_id) {
                    $this->session->set_flashdata('SUCCESSMSG', 'Stock Verification created successfully');
                    redirect('MaterialIssueController/stock_summary');
                } else {
                    $this->session->set_flashdata('ERRORMSG', 'Failed to create Stock Verification');
                    redirect('MaterialIssueController/stock_verification');
                }
            }
        }

        // Get all inventory items with stock for verification
        $data['inventory_items'] = $this->model->get_inventory_items_with_stock();

        // Get verification history for history tab
        $data['verification_history'] = $this->model->get_verification_history();

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/stock_verification', $data);
       
    }

    /**
     * Export stock verification to CSV
     */
    public function export_stock_verification($verification_id = null)
    {
        $filename = 'stock_verification_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');

        if ($verification_id) {
            // Export a specific verification with its items
            $verification = $this->db->where('verification_id', $verification_id)->get('stock_verifications')->row_array();
            $items        = $this->model->get_verification_items($verification_id);

            fputcsv($output, ['Stock Verification Report']);
            fputcsv($output, ['Verification No', $verification['verification_no'] ?? '']);
            fputcsv($output, ['Date',            $verification['verification_date'] ?? '']);
            fputcsv($output, ['Status',          strtoupper($verification['status'] ?? '')]);
            fputcsv($output, ['Remarks',         $verification['remarks'] ?? '']);
            fputcsv($output, ['Total Items',     $verification['total_items'] ?? 0]);
            fputcsv($output, ['Total Variance Value', number_format($verification['total_variance'] ?? 0, 2)]);
            fputcsv($output, []);
            fputcsv($output, ['#', 'Item Code', 'Item Name', 'System Stock', 'Physical Stock', 'Variance', 'Unit Price', 'Variance Value']);
            $i = 1;
            foreach ($items as $item) {
                fputcsv($output, [
                    $i++,
                    $item['code']          ?? '',
                    $item['item_name']     ?? '',
                    $item['system_stock']  ?? 0,
                    $item['physical_stock'] ?? 0,
                    $item['variance']      ?? 0,
                    $item['unit_price']    ?? 0,
                    $item['variance_value'] ?? 0,
                ]);
            }
        } else {
            // Export all verification history
            $history = $this->model->get_verification_history();
            fputcsv($output, ['#', 'Verification No', 'Date', 'Total Items', 'Total Variance Value', 'Status', 'Remarks']);
            $i = 1;
            foreach ($history as $row) {
                fputcsv($output, [
                    $i++,
                    $row['verification_no']   ?? '',
                    $row['verification_date'] ?? '',
                    $row['total_items']       ?? 0,
                    number_format($row['total_variance'] ?? 0, 2),
                    strtoupper($row['status'] ?? ''),
                    $row['remarks']           ?? '',
                ]);
            }
        }

        fclose($output);
        exit;
    }


    /**
     * AJAX: Return item detail table for a verification (used in history modal)
     */
    public function get_verification_items_ajax($verification_id = null)
    {
        if (!$verification_id) {
            echo '<p class="text-danger">No verification ID provided.</p>';
            return;
        }
        $items = $this->model->get_verification_items($verification_id);
        if (empty($items)) {
            echo '<p class="text-muted text-center">No items found for this verification.</p>';
            return;
        }
        echo '<table class="table table-bordered table-condensed" style="margin:0;">';
        echo '<thead style="background:#f5f5f5;"><tr>
            <th>#</th><th>Item Code</th><th>Item Name</th>
            <th>System Stock</th><th>Physical Stock</th>
            <th>Variance</th><th>Unit Price</th><th>Variance Value</th>
        </tr></thead><tbody>';
        $n = 1;
        foreach ($items as $it) {
            $v    = floatval($it['variance'] ?? 0);
            $vval = floatval($it['variance_value'] ?? 0);
            $rc   = $v > 0 ? 'success' : ($v < 0 ? 'danger' : '');
            echo "<tr class=\"{$rc}\">
                <td>{$n}</td>
                <td><strong>" . htmlspecialchars($it['code'] ?? '') . "</strong></td>
                <td>" . htmlspecialchars($it['item_name'] ?? '') . "</td>
                <td class='text-right'>" . number_format($it['system_stock'] ?? 0, 2) . "</td>
                <td class='text-right'>" . number_format($it['physical_stock'] ?? 0, 2) . "</td>
                <td class='text-right' style='color:" . ($v > 0 ? '#5cb85c' : ($v < 0 ? '#d9534f' : '#888')) . ";font-weight:bold;'>" . number_format($v, 2) . "</td>
                <td class='text-right'>" . number_format($it['unit_price'] ?? 0, 2) . "</td>
                <td class='text-right' style='font-weight:bold;'>" . number_format($vval, 2) . "</td>
            </tr>";
            $n++;
        }
        echo '</tbody></table>';
    }

    /**
     * Apply stock adjustment from verification
     */
    public function apply_adjustment($verification_id)
    {
        // Check authorization
        if (!$this->session->userdata('is_manager') && !$this->session->userdata('is_admin')) {
            $this->session->set_flashdata('ERRORMSG', 'You are not authorized to apply stock adjustments');
            redirect('MaterialIssueController/stock_summary');
        }

        $success = $this->model->adjust_stock_from_verification($verification_id);

        if ($success) {
            $this->session->set_flashdata('SUCCESSMSG', 'Stock adjusted successfully');
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Failed to adjust stock');
        }

        redirect('MaterialIssueController/stock_summary');
    }

    /**
     * Stock ledger for specific item
     */
    public function stock_ledger($inventory_id = null)
    {
        if ($this->input->post('inventory_id')) {
            $inventory_id = $this->input->post('inventory_id');
        } elseif ($this->input->get('inventory_id')) {
            $inventory_id = $this->input->get('inventory_id');
        } elseif ($this->input->get('item')) {
            $inventory_id = $this->input->get('item');
        }

        $data = array();

        if ($inventory_id) {
            $date_from = $this->input->get('date_from');
            $date_to = $this->input->get('date_to');

            $data['ledger_entries'] = $this->model->get_stock_ledger($inventory_id, $date_from, $date_to);
            $data['selected_item'] = $inventory_id;
            $data['date_from'] = $date_from;
            $data['date_to'] = $date_to;

            // Get item details
            $item = $this->model->get_item_details($inventory_id);
            $data['item_details'] = is_array($item) ? $item : array();
            $data['item_name'] = !empty($item) ? $item['item_name'] . ' (' . $item['code'] . ')' : '';
        }

        // Get inventory items for dropdown
        $data['inventory_items'] = $this->model->get_all_inventory_items();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', is_array($session_data_head) ? $session_data_head : array());
        $this->load->view('material_issue/stock_ledger', $data);
    }

    /**
     * Stock valuation report
     */
    public function stock_valuation()
    {
        $data['valuation_report'] = $this->model->get_stock_valuation_report();

        // Calculate totals
        $total_cost = 0;
        $total_selling = 0;

        foreach ($data['valuation_report'] as $item) {
            $total_cost += $item['total_cost_value'];
            $total_selling += $item['total_selling_value'];
        }

        $data['total_cost_value'] = $total_cost;
        $data['total_selling_value'] = $total_selling;

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/stock_valuation', $data);
       
    }

    /**
     * Low stock alert
     */
    public function low_stock()
    {
        $data['low_stock_items'] = $this->model->get_low_stock_items();

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/low_stock', $data);
       
    }

    /**
     * MRP - Material Requirements Planning
     * Lists all items across active job orders with stock & pending details
     */
    public function mrp()
    {
        $data['mrp_items'] = $this->model->get_mrp_data($this->uid);

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/mrp', $data);
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $data['summary'] = $this->model->get_dashboard_summary();
        $data['recent_movements'] = $this->model->get_recent_stock_movements(10);

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/dashboard', $data);
       
    }

    /**
     * AJAX: Get item details
     */
    public function ajax_get_item_details()
    {
        $inventory_id = $this->input->post('inventory_id');

        if (!$inventory_id) {
            echo json_encode(array('success' => false, 'message' => 'Item ID is required'));
            return;
        }

        $item = $this->model->get_item_details($inventory_id);

        if ($item) {
            echo json_encode(array(
                'success' => true,
                'item' => array(
                    'code' => $item['code'],
                    'item_name' => $item['item_name'],
                    'unit' => $item['unit'],
                    'stock' => $item['stock'],
                    'cost_price' => $item['cost_price'],
                    'sell_price' => $item['sell_price']
                )
            ));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Item not found'));
        }
    }

    /**
     * AJAX: Check stock availability
     */
    public function ajax_check_stock()
    {
        $inventory_id = $this->input->post('inventory_id');
        $quantity = $this->input->post('quantity');

        if (!$inventory_id || !$quantity) {
            echo json_encode(array('success' => false, 'message' => 'Invalid parameters'));
            return;
        }

        $item = $this->model->get_item_details($inventory_id);

        if ($item) {
            if ($quantity > $item['stock']) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $item['stock']
                ));
            } else {
                echo json_encode(array('success' => true));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => 'Item not found'));
        }
    }

    /**
     * Export stock summary to Excel
     */
    public function export_stock_summary()
    {
        // Get data
        $filters = array();
        if ($this->input->get('category_id')) {
            $filters['category_id'] = $this->input->get('category_id');
        }
        if ($this->input->get('group_id')) {
            $filters['group_id'] = $this->input->get('group_id');
        }
        if ($this->input->get('item_type')) {
            $filters['item_type'] = $this->input->get('item_type');
        }
        if ($this->input->get('low_stock')) {
            $filters['low_stock'] = true;
        }
        if ($this->input->get('out_of_stock')) {
            $filters['out_of_stock'] = true;
        }

        $stock_items = $this->model->get_stock_summary($filters);

        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet') || file_exists(APPPATH . '../vendor/autoload.php')) {
            $this->export_stock_summary_excel($stock_items);
        } else {
            $this->export_stock_summary_csv($stock_items);
        }
    }

    private function export_stock_summary_excel($stock_items)
    {
        require_once APPPATH . '../vendor/autoload.php';

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getProperties()
                ->setCreator("ERP System")
                ->setLastModifiedBy("ERP System")
                ->setTitle("Stock Summary Report")
                ->setSubject("Stock Summary Details")
                ->setDescription("Export of stock items with current valuation");

            $headers = [
                'Sr.No.', 'Item Code', 'Item Name', 'Category', 'Group',
                'Unit', 'Current Stock', 'Cost Price', 'Sell Price',
                'Cost Value', 'Sell Value', 'Status'
            ];

            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
                $sheet->getStyle($column . '1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF3498DB');
                $column++;
            }

            $row = 2;
            $sr_no = 1;
            $totalCostVal = 0;
            $totalSellVal = 0;

            foreach ($stock_items as $item) {
                $costPrice = (floatval($item['stock']) > 0) ? floatval($item['cost_price']) : 0;
                $sellPrice = (floatval($item['stock']) > 0) ? floatval($item['sell_price']) : 0;
                $costVal = floatval($item['stock']) * $costPrice;
                $sellVal = floatval($item['stock']) * $sellPrice;
                $totalCostVal += $costVal;
                $totalSellVal += $sellVal;

                $statusText = 'In Stock';
                if ($item['stock'] <= 0) {
                    $statusText = 'Out of Stock';
                } elseif ($item['stock'] <= 10) {
                    $statusText = 'Low Stock';
                }

                $sheet->setCellValue('A' . $row, $sr_no);
                $sheet->setCellValue('B' . $row, $item['code'] ?? '');
                $sheet->setCellValue('C' . $row, $item['item_name'] ?? '');
                $sheet->setCellValue('D' . $row, $item['category_name'] ?? 'N/A');
                $sheet->setCellValue('E' . $row, $item['group_name'] ?? 'N/A');
                $sheet->setCellValue('F' . $row, $item['unit'] ?? '');
                
                $stockCell = $sheet->getCell('G' . $row);
                $stockCell->setValue(floatval($item['stock']));
                if ($item['stock'] <= 0) {
                    $sheet->getStyle('G' . $row)->getFont()->getColor()
                        ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                } elseif ($item['stock'] <= 10) {
                    $sheet->getStyle('G' . $row)->getFont()->getColor()
                        ->setARGB('FFD35400');
                }

                $sheet->setCellValue('H' . $row, $costPrice);
                $sheet->setCellValue('I' . $row, $sellPrice);
                $sheet->setCellValue('J' . $row, $costVal);
                $sheet->setCellValue('K' . $row, $sellVal);
                $sheet->setCellValue('L' . $row, $statusText);

                $sheet->getStyle('H' . $row . ':K' . $row)
                    ->getNumberFormat()
                    ->setFormatCode('₹#,##0.00');

                $row++;
                $sr_no++;
            }

            $summaryRow = $row + 1;
            $sheet->setCellValue('H' . $summaryRow, 'Total Valuation:');
            $sheet->getStyle('H' . $summaryRow)->getFont()->setBold(true);
            $sheet->setCellValue('J' . $summaryRow, $totalCostVal);
            $sheet->setCellValue('K' . $summaryRow, $totalSellVal);
            $sheet->getStyle('J' . $summaryRow . ':K' . $summaryRow)->getFont()->setBold(true);
            $sheet->getStyle('J' . $summaryRow . ':K' . $summaryRow)->getNumberFormat()->setFormatCode('₹#,##0.00');

            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'stock_summary_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            $this->export_stock_summary_csv($stock_items);
        }
    }

    private function export_stock_summary_csv($stock_items)
    {
        $filename = 'stock_summary_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Sr.No.', 'Item Code', 'Item Name', 'Category', 'Group',
            'Unit', 'Current Stock', 'Cost Price', 'Sell Price',
            'Cost Value', 'Sell Value', 'Status'
        ));

        $sr = 1;
        foreach ($stock_items as $item) {
            $costPrice = (floatval($item['stock']) > 0) ? floatval($item['cost_price']) : 0;
            $sellPrice = (floatval($item['stock']) > 0) ? floatval($item['sell_price']) : 0;
            $cost_value = floatval($item['stock']) * $costPrice;
            $sell_value = floatval($item['stock']) * $sellPrice;
            $statusText = $item['stock'] <= 0 ? 'Out of Stock' : ($item['stock'] <= 10 ? 'Low Stock' : 'In Stock');

            fputcsv($output, array(
                $sr++,
                $item['code'],
                $item['item_name'],
                $item['category_name'] ?: 'N/A',
                $item['group_name'] ?: 'N/A',
                $item['unit'],
                $item['stock'],
                $costPrice,
                $sellPrice,
                $cost_value,
                $sell_value,
                $statusText
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Export issue slips to Excel
     */
    public function export_issue_slips()
    {
        // Get filters
        $filters = array();
        if ($this->input->get('date_from')) {
            $filters['date_from'] = $this->input->get('date_from');
        }
        if ($this->input->get('date_to')) {
            $filters['date_to'] = $this->input->get('date_to');
        }

        $issue_slips = $this->model->get_issue_slips($filters);

        $filename = 'issue_slips_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, array(
            'Issue No',
            'Date',
            'Issued To',
            'Department',
            'Project Code',
            'Items',
            'Total Qty',
            'Status',
            'Purpose'
        ));

        // Data
        foreach ($issue_slips as $slip) {
            fputcsv($output, array(
                $slip['issue_no'],
                $slip['issue_date'],
                $slip['issued_to'],
                $slip['department'] ?: 'N/A',
                $slip['project_code'] ?: 'N/A',
                $slip['total_items'],
                $slip['total_qty'],
                $slip['status'],
                $slip['purpose'] ?: ''
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Display list of Sales Orders to run MRP against
     */
    public function sales_order_mrp()
    {
        $this->load->model('salesorder');
        $this->load->model('login');

        $data = array();
        $data['settings'] = $this->login->get_settings($this->uid);
        
        $sales_orders = $this->salesorder->get_salesorders_for_joborder($this->uid);
        $filtered_sales_orders = array();
        if (!empty($sales_orders)) {
            foreach ($sales_orders as &$so) {
                // Find BOMs linked by oc_number or project_code with send_to_mrp >= 1 (all users)
                $this->db->select('number_fk, send_to_mrp');
                $this->db->from('bom_total');
                // Removed uid filter — show BOMs from all users
                $this->db->where('send_to_mrp >=', 1);
                $this->db->group_start();
                    $this->db->where('oc_number', $so->number);
                    if (!empty($so->project_code)) {
                        $this->db->or_where('project_code', $so->project_code);
                    }
                $this->db->group_end();
                $boms1 = $this->db->get()->result_array();
                $boms2 = array();

                // Also find BOMs matching the product names in this sales order with send_to_mrp >= 1
                $this->db->select('product_name');
                $this->db->from('salesorder');
                $this->db->where('number', $so->number);
                $so_items = $this->db->get()->result_array();
                if (!empty($so_items)) {
                    $fg_codes = array_column($so_items, 'product_name');
                    $this->db->select('number_fk, send_to_mrp');
                    $this->db->from('bom_total');
                    // Removed uid filter — show BOMs from all users
                    $this->db->where('send_to_mrp >=', 1);
                    $this->db->where_in('system', $fg_codes);
                    // Match generic only!
                    $this->db->group_start();
                        $this->db->where('oc_number', '');
                        $this->db->or_where('oc_number IS NULL', null, false);
                    $this->db->group_end();
                    $this->db->group_start();
                        $this->db->where('project_code', '');
                        $this->db->or_where('project_code IS NULL', null, false);
                    $this->db->group_end();
                    $boms2 = $this->db->get()->result_array();
                }

                $all_boms = array_merge($boms1, $boms2);
                $latest_boms = array();
                foreach ($all_boms as $b) {
                    $bom_no = $b['number_fk'];
                    if (preg_match('/^(.*?)(?:\/R|-R)(\d+)$/i', $bom_no, $matches)) {
                        $base = $matches[1];
                        $rev = intval($matches[2]);
                    } else {
                        $base = $bom_no;
                        $rev = 0;
                    }
                    if (!isset($latest_boms[$base]) || $rev > $latest_boms[$base]['rev']) {
                        $latest_boms[$base] = array(
                            'bom' => $b,
                            'rev' => $rev
                        );
                    }
                }

                $bom_nos = array();
                $mrp_run_fully = true;
                $has_boms = false;

                foreach ($latest_boms as $base => $bd) {
                    $b = $bd['bom'];
                    $bom_nos[] = $b['number_fk'];
                    $has_boms = true;
                    if ($b['send_to_mrp'] == 1) {
                        $mrp_run_fully = false;
                    }
                }

                $associated_boms = array_values(array_unique($bom_nos));
                if (!empty($associated_boms)) {
                    $so->associated_boms = $associated_boms;
                    $so->mrp_status = ($has_boms && $mrp_run_fully) ? 'already_run' : 'not_run';
                    $filtered_sales_orders[] = $so;
                }
            }
        }
        $data['sales_orders'] = $filtered_sales_orders;

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/sales_order_mrp', $data);
    }

    /**
     * Run MRP for a specific Sales Order and show exploded BOM requirements
     */
    public function run_sales_order_mrp()
    {
        $this->load->model('salesorder');
        $this->load->model('login');

        $id = $this->uri->segment(3);
        if (!$id) {
            $id = $this->input->get('id');
        }

        if (!$id) {
            $this->session->set_flashdata('ERRORMSG', 'Sales Order ID is required');
            redirect('MaterialIssueController/sales_order_mrp');
            return;
        }

        $so_number_data = $this->salesorder->get_salesorder_number_from_salesorder_total($id, $this->uid);
        if (!$so_number_data) {
            $this->session->set_flashdata('ERRORMSG', 'Sales Order not found');
            redirect('MaterialIssueController/sales_order_mrp');
            return;
        }

        $so_number = $so_number_data['number_fk'];
        $mrp_data = $this->model->get_sales_order_mrp_data($so_number, $this->uid);

        if (!$mrp_data) {
            $this->session->set_flashdata('ERRORMSG', 'Failed to retrieve MRP data for this Sales Order');
            redirect('MaterialIssueController/sales_order_mrp');
            return;
        }

        // Promote all associated BOMs from state 1 to state 2 (MRP run executed) and set status to Approved (4)
        $associated_boms = $mrp_data['associated_boms'] ?? array();
        if (!empty($associated_boms)) {
            $this->db->where_in('number_fk', $associated_boms)
                     // Removed uid filter — promote BOMs regardless of creator
                     ->where('send_to_mrp', 1)
                     ->update('bom_total', array('send_to_mrp' => 2, 'status' => 4));
        }

        $data = array();
        $data['settings'] = $this->login->get_settings($this->uid);
        $data['so_info'] = $mrp_data['so_info'];
        $data['so_items'] = $mrp_data['so_items'];
        $data['mrp_items'] = $mrp_data['mrp_items'];
        $data['associated_boms'] = $mrp_data['associated_boms'] ?? array();

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/run_so_mrp', $data);
    }

    /**
     * AJAX: Generate Purchase Requisition for selected shortage items from MRP Run
     */
    public function ajax_generate_pr_from_mrp()
    {
        header('Content-Type: application/json');

        $items_json = $this->input->post('items');
        if (!$items_json) {
            echo json_encode(array('success' => false, 'message' => 'No items provided'));
            return;
        }

        $items = json_decode($items_json, true);
        if (empty($items)) {
            echo json_encode(array('success' => false, 'message' => 'Items list is empty'));
            return;
        }

        // 1. Get and validate department_id_fk (department_master is NOT NULL)
        $dept_id = $this->session->userdata('session_data_head')['result']['department_id_fk'] ?? null;
        if (!empty($dept_id)) {
            $dept_exists = $this->db->where('department_id', $dept_id)->count_all_results('department_master');
            if ($dept_exists == 0) {
                $dept_id = null;
            }
        }
        if (empty($dept_id)) {
            $dept = $this->db->select('department_id')
                             ->order_by('department_id', 'ASC')
                             ->get('department_master', 1)
                             ->row_array();
            $dept_id = $dept ? $dept['department_id'] : 2; // fallback to 2 (first department ID)
        }

        // 2. Get and validate location_id_fk (location_master is NULL allowed)
        $location_id = $this->session->userdata('session_data_head')['result']['location_id'] ?? null;
        if (!empty($location_id)) {
            $loc_exists = $this->db->where('location_id', $location_id)->count_all_results('location_master');
            if ($loc_exists == 0) {
                $location_id = null;
            }
        } else {
            $location_id = null;
        }

        // Get current user's name for requested_by (NOT NULL)
        $logged_in_uid = $this->session->userdata('session_data_head')['result']['user_id'] ?? $this->uid;
        $user_row = $this->db->select('username')
                             ->where('user_id', $logged_in_uid)
                             ->get('user', 1)
                             ->row_array();
        $requested_by = $user_row ? $user_row['username'] : 'MRP System';

        $pr_date       = date('Y-m-d');
        $required_date = date('Y-m-d', strtotime('+7 days'));

        $so_no = $this->input->post('so_no');
        $project_code = $this->input->post('project_code') ?? '';

        if (!empty($so_no)) {
            // Validate if at least one Job Order has been created for this Sales Order
            $jo_count = $this->db->from('joborder_total')
                                 ->group_start()
                                     ->where('salesorder_number', $so_no)
                                     ->or_where('so_reference', $so_no)
                                     ->or_where('oc_number', $so_no)
                                 ->group_end()
                                 ->count_all_results();

            if ($jo_count == 0) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Job Order not created for this Sales Order'
                ));
                return;
            }
        }

        $oc_no = null;
        if (!empty($so_no)) {
            $so_row = $this->db->select('oc_number')
                               ->where('number_fk', $so_no)
                               ->get('salesorder_total')
                               ->row_array();
            if ($so_row) {
                $oc_no = $so_row['oc_number'];
            }
        }

        $remarks_str = 'Auto-generated from MRP Run';
        if (!empty($so_no)) {
            $remarks_str .= ' for SO: ' . $so_no;
        }
        if (!empty($project_code)) {
            $remarks_str .= ' | Project: ' . $project_code;
        }
        $remarks_str .= ' on ' . date('d-M-Y H:i');

        $master_data = array(
            'pr_date'                => $pr_date,
            'required_date'          => $required_date,
            'department_id_fk'       => $dept_id,
            'location_id_fk'         => $location_id,
            'requested_by'           => $requested_by,
            'approval_status'        => 'Pending',
            'workflow_status'        => 'L1_Pending',
            'current_approver_role'  => 'Purchase Manager',
            'submitted_for_approval' => date('Y-m-d H:i:s'),
            'urgency_level'          => 'Normal',
            'remarks'                => $remarks_str,
            'project_code'           => $project_code,
            'so_no'                  => $so_no,
            'oc_no'                  => $oc_no,
            'created_by'             => $logged_in_uid,
        );

        $this->db->insert('purchase_requisition', $master_data);
        $pr_id = $this->db->insert_id();

        if (!$pr_id) {
            echo json_encode(array('success' => false, 'message' => 'Failed to create Purchase Requisition'));
            return;
        }

        // Financial year prefix for PR number
        if (date('m') <= 3) {
            $fy = (date('y') - 1) . '-' . date('y');
        } else {
            $fy = date('y') . '-' . (date('y') + 1);
        }

        $this->load->model('Requisition');
        $last_pr_no = $this->Requisition->get_last_pr_number($logged_in_uid);
        
        $last_pr_no++;
        $pr_no = 'PR/' . $fy . '/' . sprintf('%04d', $last_pr_no);

        $item_rows = array();
        foreach ($items as $item) {
            $shortage_qty = isset($item['shortage']) ? floatval($item['shortage']) : 0;
            $item_rows[] = array(
                'pr_id'          => $pr_id,
                'item_code'      => isset($item['code']) ? $item['code'] : '',
                'description'    => isset($item['name']) ? 'MRP: ' . $item['name'] : 'MRP Item',
                'quantity'       => $shortage_qty,
                'unit'           => isset($item['unit']) ? $item['unit'] : '',
                'estimated_cost' => 0,
                'hsn'            => '',
                'specification'  => 'MRP Auto-PR | Gross Req: ' . (isset($item['gross']) ? $item['gross'] : '0')
                                  . ' | Stock Available: ' . (isset($item['stock']) ? $item['stock'] : '0')
                                  . ' | Shortage: ' . $shortage_qty,
                'pr_no'          => $pr_no,
                'created_by'     => $logged_in_uid,
            );
        }

        if (!empty($item_rows)) {
            $this->db->insert_batch('purchase_requisition_items', $item_rows);
        }

        // Update total_value on the master
        $total = array_sum(array_column($item_rows, 'estimated_cost'));
        $this->db->where('pr_id', $pr_id)->update('purchase_requisition', array('total_value' => $total));

        echo json_encode(array(
            'success' => true,
            'pr_id'   => $pr_id,
            'pr_url'  => base_url() . 'RequisitionController/show_requisition/' . $pr_id,
            'message' => 'Purchase Requisition generated successfully! (PR Number: ' . $pr_no . ')'
        ));
    }

    /**
     * AJAX: Allocate stock to Job Order (Step 1 - Reservation only)
     */
    public function ajax_allocate_stock()
    {
        $inventory_id = (int) $this->input->post('inventory_id');
        $jo_number    = $this->input->post('jo_number');
        $qty          = floatval($this->input->post('quantity'));
        $item_code    = $this->input->post('item_code');
        $item_name    = $this->input->post('item_name');
        $unit         = $this->input->post('unit');

        if (!$inventory_id || !$jo_number || $qty <= 0) {
            echo json_encode(array('success' => false, 'message' => 'Invalid parameters: inventory_id, jo_number and quantity are required.'));
            return;
        }

        // Check current stock and available stock
        $inventory_table = $this->db->dbprefix . 'inventory';

        $inv = $this->db->query(
            "SELECT stock, allocated_stock, available_stock, item_name, unit FROM {$inventory_table} WHERE inventory_id = {$inventory_id} LIMIT 1"
        )->row_array();

        if (!$inv) {
            echo json_encode(array('success' => false, 'message' => 'Item not found in inventory.'));
            return;
        }

        $available_stock = isset($inv['available_stock']) ? floatval($inv['available_stock']) : floatval($inv['stock']);

        if ($available_stock <= 0) {
            echo json_encode(array(
                'success' => false,
                'message' => 'No stock available for allocation. Available (Open): 0.00 ' . $inv['unit'],
            ));
            return;
        }

        $qty_to_allocate = min($qty, $available_stock);

        // Get job order total ID
        $jo_total = $this->db->select('id')
                             ->where('number_fk', $jo_number)
                             ->get('joborder_total', 1)
                             ->row_array();
        $jo_id = $jo_total ? (int)$jo_total['id'] : 0;

        // Check if allocation already exists
        $existing = $this->db->where('joborder_id', $jo_id)
                             ->where('inventory_id', $inventory_id)
                             ->where('product_code', $item_code)
                             ->where('status !=', 'cancelled')
                             ->get('stock_allocations')
                             ->row_array();

        if ($existing) {
            if ($existing['allocated_quantity'] >= $qty) {
                echo json_encode(array('success' => false, 'message' => 'Stock is already allocated/reserved for this item and Job Order.'));
                return;
            }
            $qty_needed_more = $qty - floatval($existing['allocated_quantity']);
            $qty_to_allocate = min($qty_needed_more, $available_stock);
            if ($qty_to_allocate <= 0) {
                echo json_encode(array('success' => false, 'message' => 'No additional stock available to allocate.'));
                return;
            }

            // Update existing reservation record
            $new_allocated = floatval($existing['allocated_quantity']) + $qty_to_allocate;
            $new_pending = floatval($existing['pending_quantity']) + $qty_to_allocate;
            $this->db->where('id', $existing['id'])
                     ->update('stock_allocations', array(
                         'allocated_quantity' => $new_allocated,
                         'pending_quantity'   => $new_pending,
                         'status'             => 'allocated',
                         'updated_at'         => date('Y-m-d H:i:s')
                     ));

            // Update inventory allocated and available stock
            $this->db->query(
                "UPDATE {$inventory_table} 
                 SET allocated_stock = IFNULL(allocated_stock, 0) + ?
                 WHERE inventory_id = ?",
                array($qty_to_allocate, $inventory_id)
            );
            $this->db->query(
                "UPDATE {$inventory_table} 
                 SET available_stock = GREATEST(0, stock - IFNULL(allocated_stock, 0))
                 WHERE inventory_id = ?",
                array($inventory_id)
            );

            if ($qty_to_allocate < $qty_needed_more) {
                $msg = 'Partially allocated additional ' . number_format($qty_to_allocate, 2) . ' ' . $inv['unit'] . ' to JO: ' . $jo_number . ' (Total Allocated: ' . number_format($new_allocated, 2) . '). Remaining ' . number_format($qty_needed_more - $qty_to_allocate, 2) . ' can be generated for PR.';
            } else {
                $msg = 'Additional stock reserved successfully! Total Allocated: ' . number_format($new_allocated, 2) . ' ' . $inv['unit'] . ' to JO: ' . $jo_number;
            }
            echo json_encode(array('success' => true, 'message' => $msg));
            return;
        }

        // Insert reservation record in stock_allocations
        $alloc_data = array(
            'joborder_id'        => $jo_id,
            'inventory_id'       => $inventory_id,
            'product_code'       => $item_code,
            'allocated_quantity' => $qty_to_allocate,
            'issued_quantity'    => 0,
            'pending_quantity'   => $qty_to_allocate,
            'status'             => 'allocated',
            'uid'                => $this->uid,
            'allocated_date'     => date('Y-m-d'),
            'notes'              => 'Allocated via MRP run on ' . date('d-M-Y H:i'),
        );
        $this->db->insert('stock_allocations', $alloc_data);
        $alloc_id = $this->db->insert_id();

        if (!$alloc_id) {
            echo json_encode(array('success' => false, 'message' => 'Failed to register stock allocation.'));
            return;
        }

        // Update inventory allocated and available stock
        $this->db->query(
            "UPDATE {$inventory_table} 
             SET allocated_stock = IFNULL(allocated_stock, 0) + ?
             WHERE inventory_id = ?",
            array($qty_to_allocate, $inventory_id)
        );
        $this->db->query(
            "UPDATE {$inventory_table} 
             SET available_stock = GREATEST(0, stock - IFNULL(allocated_stock, 0))
             WHERE inventory_id = ?",
            array($inventory_id)
        );

        if ($qty_to_allocate < $qty) {
            $msg = 'Partially allocated ' . number_format($qty_to_allocate, 2) . ' ' . $inv['unit'] . ' to JO: ' . $jo_number . ' (Requested: ' . number_format($qty, 2) . '). Remaining ' . number_format($qty - $qty_to_allocate, 2) . ' can be generated for PR.';
        } else {
            $msg = 'Stock reserved successfully! Allocated ' . number_format($qty_to_allocate, 2) . ' ' . $inv['unit'] . ' to JO: ' . $jo_number;
        }

        echo json_encode(array(
            'success' => true,
            'message' => $msg,
        ));
    }

    /**
     * Private helper to allocate a single item to all Job Orders of a Sales Order
     */
    private function _perform_item_allocation($so_number, $item_code, $inventory_id, $gross_qty)
    {
        $inventory_table = $this->db->dbprefix . 'inventory';

        // Fetch Job Orders associated with this Sales Order (all users — global data)
        $job_orders = $this->db->select('j.*, jt.id as jt_id')
                               ->from('joborder j')
                               ->join('joborder_total jt', 'jt.number_fk = j.number')
                               ->group_start()
                                   ->where('jt.salesorder_number', $so_number)
                                   ->or_where('jt.so_reference', $so_number)
                                   ->or_where('jt.oc_number', $so_number)
                               ->group_end()
                               ->where('j.product_name !=', '__HEADING__')
                               ->get()
                               ->result_array();

        if (empty($job_orders)) {
            return array('success' => false, 'message' => 'No Job Orders found for this Sales Order. Please create a Job Order first to allocate stock.');
        }

        // Fetch candidate BOMs matching the finished good product codes or the Sales Order details
        $fg_codes = array();
        foreach ($job_orders as $jo) {
            if (!empty($jo['product_name'])) {
                $fg_codes[] = $jo['product_name'];
            }
        }

        $so_total = $this->db->select('project_code')
                             ->from('salesorder_total')
                             ->where('number_fk', $so_number)
                             ->get()
                             ->row_array();
        $so_project_code = $so_total ? $so_total['project_code'] : '';

        $this->db->select('bt.*')
                 ->from('bom_total bt')
                 ->where('bt.send_to_mrp >=', 1)
                 ->group_start()
                     ->where('bt.oc_number', $so_number);
        if (!empty($so_project_code)) {
            $this->db->or_where('bt.project_code', $so_project_code);
        }
        if (!empty($fg_codes)) {
            $this->db->or_where_in('bt.system', $fg_codes);
        }
        $this->db->group_end();
        $boms = $this->db->get()->result_array();

        // Filter BOMs to only explode the latest revision of each base BOM
        $latest_boms = array();
        foreach ($boms as $bom) {
            $bom_no = $bom['number_fk'];
            if (preg_match('/^(.*?)-R(\d+)$/', $bom_no, $matches)) {
                $base = $matches[1];
                $rev = intval($matches[2]);
            } else {
                $base = $bom_no;
                $rev = 0;
            }
            if (!isset($latest_boms[$base]) || $rev > $latest_boms[$base]['rev']) {
                $latest_boms[$base] = array('bom' => $bom, 'rev' => $rev);
            }
        }
        $boms = array();
        foreach ($latest_boms as $base => $data) {
            $boms[] = $data['bom'];
        }

        $project_bom_found = null;
        $item_boms_found = array();

        foreach ($boms as $bom) {
            $bom_item = $this->db->select('quantity')
                                 ->from('bom')
                                 ->where('number', $bom['number_fk'])
                                 ->where('product_name', $item_code)
                                 ->get()
                                 ->row_array();
            if (!$bom_item) {
                continue;
            }

            $system = $bom['system'];
            $is_project_bom = ($bom['oc_number'] == $so_number) || (!empty($so_project_code) && $bom['project_code'] == $so_project_code);

            if ($is_project_bom) {
                $project_bom_found = array(
                    'bom_no' => $bom['number_fk'],
                    'quantity' => floatval($bom_item['quantity'])
                );
            } elseif (!empty($system) && in_array($system, $fg_codes)) {
                $item_boms_found[$system] = array(
                    'bom_no' => $bom['number_fk'],
                    'quantity' => floatval($bom_item['quantity'])
                );
            }
        }

        $allocations_made = 0;
        $total_allocated_qty = 0;
        $failed_jobs = array();

        $allocate_fn = function($jo_id, $jo_no, $qty_needed) use ($inventory_id, $item_code, $inventory_table, $so_number, &$allocations_made, &$total_allocated_qty, &$failed_jobs) {
            // Check if allocation already exists
            $existing = $this->db->where('joborder_id', $jo_id)
                                 ->where('inventory_id', $inventory_id)
                                 ->where('product_code', $item_code)
                                 ->where('status !=', 'cancelled')
                                 ->get('stock_allocations')
                                 ->row_array();

            // Verify stock available
            $inv = $this->db->query(
                "SELECT stock, allocated_stock, available_stock, unit FROM {$inventory_table} WHERE inventory_id = {$inventory_id} LIMIT 1"
            )->row_array();

            if (!$inv) {
                return false;
            }

            if ($existing) {
                $allocated_quantity = floatval($existing['allocated_quantity']);
                if ($allocated_quantity >= $qty_needed) {
                    $allocations_made++;
                    return true;
                }
            }

            $available_stock = isset($inv['available_stock']) ? floatval($inv['available_stock']) : floatval($inv['stock']);
            if ($available_stock <= 0) {
                $failed_jobs[] = $jo_no . ' (0 stock available)';
                return false;
            }

            if ($existing) {
                $allocated_quantity = floatval($existing['allocated_quantity']);
                $qty_needed_more = $qty_needed - $allocated_quantity;
                $qty_to_allocate = min($qty_needed_more, $available_stock);
                if ($qty_to_allocate <= 0) {
                    return false;
                }

                $new_allocated = $allocated_quantity + $qty_to_allocate;
                $new_pending = floatval($existing['pending_quantity']) + $qty_to_allocate;
                $this->db->where('id', $existing['id'])
                          ->update('stock_allocations', array(
                              'allocated_quantity' => $new_allocated,
                              'pending_quantity'   => $new_pending,
                              'status'             => 'allocated',
                              'updated_at'         => date('Y-m-d H:i:s')
                          ));
            } else {
                $qty_to_allocate = min($qty_needed, $available_stock);
                if ($qty_to_allocate <= 0) {
                    return false;
                }

                // Insert allocation record
                $alloc_data = array(
                    'joborder_id'        => $jo_id,
                    'inventory_id'       => $inventory_id,
                    'product_code'       => $item_code,
                    'allocated_quantity' => $qty_to_allocate,
                    'issued_quantity'    => 0,
                    'pending_quantity'   => $qty_to_allocate,
                    'status'             => 'allocated',
                    'uid'                => $this->uid,
                    'allocated_date'     => date('Y-m-d'),
                    'notes'              => 'Allocated via Sales Order MRP run for SO: ' . $so_number,
                );
                $this->db->insert('stock_allocations', $alloc_data);
            }

            // Update inventory
            $this->db->query(
                "UPDATE {$inventory_table} 
                 SET allocated_stock = IFNULL(allocated_stock, 0) + ?
                 WHERE inventory_id = ?",
                array($qty_to_allocate, $inventory_id)
            );
            $this->db->query(
                "UPDATE {$inventory_table} 
                 SET available_stock = GREATEST(0, stock - IFNULL(allocated_stock, 0))
                 WHERE inventory_id = ?",
                array($inventory_id)
            );

            $allocations_made++;
            $total_allocated_qty += $qty_to_allocate;
            return true;
        };

        // 1. First check item-specific BOM allocations
        if (!empty($item_boms_found)) {
            foreach ($job_orders as $jo) {
                $fg = $jo['product_name'];
                if (!isset($item_boms_found[$fg])) {
                    continue;
                }
                $bom_qty = $item_boms_found[$fg]['quantity'];
                $qty_needed = $bom_qty * floatval($jo['quantity']);
                if ($qty_needed > 0) {
                    $allocate_fn($jo['jt_id'], $jo['number'], $qty_needed);
                }
            }
        }

        // 2. If no item-specific allocations were made, fall back to Project BOM allocation
        if ($allocations_made === 0 && $project_bom_found) {
            $first_jo = $job_orders[0];
            $qty_needed = ($gross_qty > 0) ? $gross_qty : $project_bom_found['quantity'];
            if ($qty_needed > 0) {
                $allocate_fn($first_jo['jt_id'], $first_jo['number'], $qty_needed);
            }
        }

        if ($allocations_made > 0) {
            $msg = 'Successfully allocated ' . number_format($total_allocated_qty, 2) . ' of component ' . $item_code . ' to Job Order(s).';
            if (!empty($failed_jobs)) {
                $msg .= ' Failed for: ' . implode(', ', $failed_jobs);
            }
            return array('success' => true, 'message' => $msg);
        } else {
            if (!empty($failed_jobs)) {
                return array('success' => false, 'message' => 'Allocation failed due to: ' . implode(', ', $failed_jobs));
            } else {
                return array('success' => false, 'message' => 'No allocations were made. The item might already be allocated, or no active Job Orders require this item.');
            }
        }
    }

    /**
     * AJAX: Allocate stock to all Job Orders of a Sales Order for a given raw material
     */
    public function ajax_allocate_so_mrp_item()
    {
        header('Content-Type: application/json');

        $so_number    = $this->input->post('so_number');
        $item_code    = $this->input->post('item_code');
        $inventory_id = (int)$this->input->post('inventory_id');
        $gross_qty    = floatval($this->input->post('quantity'));

        if (!$so_number || !$item_code || !$inventory_id) {
            echo json_encode(array('success' => false, 'message' => 'Invalid parameters: so_number, item_code, and inventory_id are required.'));
            return;
        }

        $res = $this->_perform_item_allocation($so_number, $item_code, $inventory_id, $gross_qty);
        echo json_encode($res);
    }

    /**
     * AJAX: Bulk allocate stock to Job Orders of a Sales Order for multiple raw materials
     */
    public function ajax_bulk_allocate_so_mrp_items()
    {
        header('Content-Type: application/json');

        $so_number = $this->input->post('so_number');
        $items     = $this->input->post('items');

        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (!$so_number || empty($items) || !is_array($items)) {
            echo json_encode(array('success' => false, 'message' => 'Invalid parameters: so_number and items array are required.'));
            return;
        }

        $success_count = 0;
        $failure_count = 0;
        $messages = array();

        foreach ($items as $item) {
            $item_code    = $item['item_code'];
            $inventory_id = (int)$item['inventory_id'];
            $gross_qty    = floatval($item['quantity']);

            if (!$item_code || !$inventory_id) {
                $failure_count++;
                continue;
            }

            $res = $this->_perform_item_allocation($so_number, $item_code, $inventory_id, $gross_qty);
            if ($res['success']) {
                $success_count++;
            } else {
                $failure_count++;
                $messages[] = $item_code . ': ' . $res['message'];
            }
        }

        if ($success_count > 0) {
            $msg = "Successfully allocated {$success_count} item(s).";
            if ($failure_count > 0) {
                $msg .= " Failed for {$failure_count} item(s): " . implode('; ', $messages);
            }
            echo json_encode(array('success' => true, 'message' => $msg));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Failed to allocate any items: ' . implode('; ', $messages)
            ));
        }
    }
    /**
     * AJAX: Deallocate / Return stock to inventory (Step 1 cancellation)
     */
    public function ajax_deallocate_stock()
    {
        header('Content-Type: application/json');

        $inventory_id = (int)$this->input->post('inventory_id');
        $item_code    = $this->input->post('item_code');
        $jo_number    = $this->input->post('jo_number'); // optional
        $so_number    = $this->input->post('so_number'); // optional

        if (!$inventory_id || !$item_code) {
            echo json_encode(array('success' => false, 'message' => 'Invalid parameters: inventory_id and item_code are required.'));
            return;
        }

        // 1. Gather all potential targets (Job Orders / Sales Orders) first to avoid query builder pollution
        $jo_id = 0;
        $so_from_jo = null;
        if (!empty($jo_number)) {
            // Lookup Job Order total ID and Sales Order number (using fallbacks if necessary)
            $jo_total = $this->db->select('id, salesorder_number, oc_number, so_reference')->where('number_fk', $jo_number)->get('joborder_total', 1)->row_array();
            $jo_id = $jo_total ? (int)$jo_total['id'] : 0;
            $so_from_jo = $jo_total ? $jo_total['salesorder_number'] : null;
            if (empty($so_from_jo) && $jo_total) {
                $so_from_jo = !empty($jo_total['oc_number']) ? $jo_total['oc_number'] : $jo_total['so_reference'];
            }
        }

        $jo_ids = array();
        $target_so = null;

        if ($jo_id > 0) {
            if (!empty($so_from_jo)) {
                $target_so = $so_from_jo;
                $job_orders = $this->db->select('jt.id')->from('joborder_total jt')
                                       ->group_start()
                                           ->where('jt.salesorder_number', $so_from_jo)
                                           ->or_where('jt.so_reference', $so_from_jo)
                                           ->or_where('jt.oc_number', $so_from_jo)
                                       ->group_end()
                                       ->get()->result_array();
                $jo_ids = array_column($job_orders, 'id');
            }
        } else {
            if (!empty($so_number)) {
                $target_so = $so_number;
                // Find all Job Orders linked to this Sales Order
                $job_orders = $this->db->select('jt.id')->from('joborder_total jt')
                                       ->group_start()
                                           ->where('jt.salesorder_number', $so_number)
                                           ->or_where('jt.so_reference', $so_number)
                                           ->or_where('jt.oc_number', $so_number)
                                       ->group_end()
                                       ->get()->result_array();
                $jo_ids = array_column($job_orders, 'id');
            }
        }

        // 2. Now perform the find query for the active allocation
        $this->db->select('*')
                 ->from('stock_allocations')
                 ->where('inventory_id', $inventory_id)
                 ->where('product_code', $item_code)
                 ->where('status !=', 'cancelled');

        if ($jo_id > 0) {
            $this->db->group_start();
                $this->db->where('joborder_id', $jo_id);
                if (!empty($target_so)) {
                    $this->db->or_group_start()
                                 ->where('joborder_id', 0)
                                 ->like('notes', 'Sales Order Allocation: ' . $target_so, 'both')
                             ->group_end();
                }
                if (!empty($jo_ids)) {
                    $this->db->or_where_in('joborder_id', $jo_ids);
                }
            $this->db->group_end();
        } else {
            if (!empty($target_so)) {
                $this->db->group_start()
                             ->group_start()
                                 ->where('joborder_id', 0)
                                 ->group_start()
                                     ->like('notes', 'Sales Order Allocation: ' . $target_so, 'both')
                                     ->or_like('notes', 'Allocated via Sales Order MRP run for SO: ' . $target_so, 'both')
                                 ->group_end()
                             ->group_end();
                if (!empty($jo_ids)) {
                    $this->db->or_where_in('joborder_id', $jo_ids);
                }
                $this->db->group_end();
            }
        }

        $allocation = $this->db->get()->row_array();

        if (!$allocation) {
            echo json_encode(array('success' => false, 'message' => 'No active allocation found to deallocate.'));
            return;
        }

        $pending_qty = floatval($allocation['pending_quantity']);
        $req_qty = $this->input->post('quantity');
        $return_qty = ($req_qty !== null && $req_qty !== '') ? floatval($req_qty) : $pending_qty;

        if ($return_qty <= 0) {
            echo json_encode(array('success' => false, 'message' => 'Invalid return quantity.'));
            return;
        }

        if ($return_qty < $pending_qty) {
            // Partial return: subtract from allocation pending_quantity and quantity
            $new_pending_qty = $pending_qty - $return_qty;
            $new_alloc_qty = floatval($allocation['allocated_quantity']) - $return_qty;

            $this->db->where('id', $allocation['id'])
                     ->update('stock_allocations', array(
                         'allocated_quantity' => $new_alloc_qty,
                         'pending_quantity' => $new_pending_qty,
                         'updated_at' => date('Y-m-d H:i:s')
                     ));
            $actual_returned = $return_qty;
            $msg = 'Allocation updated. ' . number_format($actual_returned, 2) . ' returned to available inventory stock.';
        } else {
            // Full return: cancel the allocation
            $this->db->where('id', $allocation['id'])
                     ->update('stock_allocations', array(
                         'status' => 'cancelled',
                         'updated_at' => date('Y-m-d H:i:s')
                     ));
            $actual_returned = $pending_qty;
            $msg = 'Allocation cancelled. ' . number_format($actual_returned, 2) . ' returned to available inventory stock.';
        }

        // Update inventory: reduce allocated_stock, increase available_stock
        $inventory_table = $this->db->dbprefix . 'inventory';
        $this->db->query(
            "UPDATE {$inventory_table} 
             SET allocated_stock = GREATEST(0, IFNULL(allocated_stock, 0) - ?)
             WHERE inventory_id = ?",
            array($actual_returned, $inventory_id)
        );
        $this->db->query(
            "UPDATE {$inventory_table} 
             SET available_stock = GREATEST(0, stock - IFNULL(allocated_stock, 0))
             WHERE inventory_id = ?",
            array($inventory_id)
        );

        echo json_encode(array('success' => true, 'message' => $msg));
    }

    /**
     * Create Material Return Note (MRN) - Scenario B
     */
    public function create_mrn()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->form_validation->set_rules('return_date', 'Return Date', 'required');
            $this->form_validation->set_rules('returned_by', 'Returned By', 'required|trim');
            $this->form_validation->set_rules('inventory_id[]', 'Items', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantity', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('ERRORMSG', validation_errors());
                redirect('MaterialIssueController/create_mrn');
            } else {
                $raw_date = $this->input->post('return_date');
                $return_date = DateTime::createFromFormat('d-m-Y', $raw_date);
                
                // Construct issue_data representing MRN (negative quantities)
                $issue_data = array(
                    'issue_date' => $return_date ? $return_date->format('Y-m-d') : date('Y-m-d'),
                    'issued_to' => $this->input->post('returned_by'), // store returnee in issued_to
                    'department' => $this->input->post('department'),
                    'project_code' => $this->input->post('project_code') ?? '',
                    'purpose' => 'Production Return (MRN)',
                    'remarks' => $this->input->post('remarks'),
                    'status' => 'issued', // auto-issued/completed on save
                    'uid' => $this->uid
                );

                if ($this->db->field_exists('joborder_number', 'material_issue_slips')) {
                    $issue_data['joborder_number'] = $this->input->post('joborder_number');
                }

                $items_data = array();
                $inventory_ids = $this->input->post('inventory_id');
                $quantities = $this->input->post('quantity');
                $item_remarks = $this->input->post('item_remarks');

                for ($i = 0; $i < count($inventory_ids); $i++) {
                    if (!empty($inventory_ids[$i]) && !empty($quantities[$i]) && $quantities[$i] > 0) {
                        // Store quantity as negative for return
                        $qty = -floatval($quantities[$i]);

                        $items_data[] = array(
                            'inventory_id_fk' => $inventory_ids[$i],
                            'quantity' => $qty,
                            'unit_price' => 0,
                            'pending_qty' => 0,
                            'remarks' => isset($item_remarks[$i]) ? $item_remarks[$i] : ''
                        );
                    }
                }

                if (empty($items_data)) {
                    $this->session->set_flashdata('ERRORMSG', 'Please return at least one item');
                    redirect('MaterialIssueController/create_mrn');
                    return;
                }

                // Start transaction
                $this->db->trans_start();

                // Generate MRN number
                $issue_data['issue_no'] = $this->model->generate_mrn_no();
                $issue_data['total_items'] = count($items_data);
                $issue_data['total_qty'] = array_sum(array_column($items_data, 'quantity'));

                // Insert into slips
                $this->db->insert('material_issue_slips', $issue_data);
                $mrn_id = $this->db->insert_id();

                foreach ($items_data as $item) {
                    $item['issue_id'] = $mrn_id;
                    $item['uid'] = $this->uid;
                    $item['total_amount'] = 0;
                    $this->db->insert('material_issue_items', $item);

                    // Update inventory and stock ledger (with BOM explosion if applicable)
                    $this->model->process_stock_update_for_item(
                        $item['inventory_id_fk'],
                        $item['quantity'],
                        'return',
                        $issue_data['joborder_number'] ?? null,
                        $issue_data['issue_no'],
                        $issue_data['issued_to'],
                        $this->uid
                    );
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $this->session->set_flashdata('SUCCESSMSG', 'Material Return Note ' . $issue_data['issue_no'] . ' created successfully');
                    redirect('MaterialIssueController/index');
                } else {
                    $this->session->set_flashdata('ERRORMSG', 'Failed to create Material Return Note');
                    redirect('MaterialIssueController/create_mrn');
                }
            }
        }

        // Get inventory items and joborders
        $data['inventory_items'] = $this->model->get_all_inventory_items();
        $data['joborders'] = $this->Joborder->get_joborders_with_issued_material($this->uid);
        $data['projects'] = $this->Project_model->get_all_projects();
        $this->load->model('department');
        $data['departments'] = $this->department->get_departments();

        $this->load->view('admin/header_side_bar');
        $this->load->view('material_issue/create_mrn', $data);
    }

    /**
     * Send Low Stock Alert Email to Purchase & Store Departments
     */
    public function send_low_stock_alert()
    {
        header('Content-Type: application/json');

        // Fetch low stock items (stock <= 10)
        $low_stock_items = $this->model->get_low_stock_items();

        if (empty($low_stock_items)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'No low stock items found to send alert.'
            ));
            exit;
        }

        // Get recipients (users from database)
        $this->db->select('user_email, username');
        $users = $this->db->get('user')->result_array();
        $recipient_emails = array();
        if (!empty($users)) {
            foreach ($users as $u) {
                if (!empty($u['user_email']) && filter_var($u['user_email'], FILTER_VALIDATE_EMAIL)) {
                    $recipient_emails[] = $u['user_email'];
                }
            }
        }
        
        if (empty($recipient_emails)) {
            $recipient_emails[] = 'purchase@uwsenvirotech.com';
        }

        $recipient_emails = array_values(array_unique($recipient_emails));

        // Build HTML Email Content
        $htmlContent = '<h2>Low Stock Alert Notification</h2>';
        $htmlContent .= '<p>The following <strong>' . count($low_stock_items) . ' items</strong> are currently low in stock or out of stock and require immediate purchase re-ordering:</p>';
        $htmlContent .= '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 13px;">';
        $htmlContent .= '<tr style="background-color: #2980b9; color: #ffffff;">
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                            <th>Cost Price (₹)</th>
                            <th>Sell Price (₹)</th>
                         </tr>';

        $i = 1;
        foreach ($low_stock_items as $item) {
            $status = ($item['stock'] <= 0) ? '<span style="color:red;font-weight:bold;">OUT OF STOCK</span>' : '<span style="color:orange;font-weight:bold;">LOW STOCK</span>';
            $htmlContent .= '<tr>';
            $htmlContent .= '<td>' . $i++ . '</td>';
            $htmlContent .= '<td><strong>' . htmlspecialchars($item['code']) . '</strong></td>';
            $htmlContent .= '<td>' . htmlspecialchars($item['item_name']) . '</td>';
            $htmlContent .= '<td>' . htmlspecialchars($item['category_name'] ?? 'N/A') . '</td>';
            $htmlContent .= '<td style="text-align:center;font-weight:bold;">' . number_format($item['stock'], 2) . '</td>';
            $htmlContent .= '<td>' . $status . '</td>';
            $htmlContent .= '<td style="text-align:right;">₹' . number_format($item['cost_price'], 2) . '</td>';
            $htmlContent .= '<td style="text-align:right;">₹' . number_format($item['sell_price'], 2) . '</td>';
            $htmlContent .= '</tr>';
        }
        $htmlContent .= '</table>';
        $htmlContent .= '<p style="margin-top:20px; font-size: 12px; color: #777;">Generated automatically by Ultimate Manufacturing ERP System on ' . date('d-m-Y') . '</p>';

        // Attempt sending via CodeIgniter Email library
        $this->load->library('email');
        $config = array(
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'newline'  => "\r\n"
        );
        $this->email->initialize($config);
        $this->email->from('noreply@uwsenvirotech.com', 'Manufacturing ERP System');
        $this->email->to(implode(',', $recipient_emails));
        $this->email->subject('ALERT: Low Stock Items Report (' . count($low_stock_items) . ' Items Need Purchase Reorder)');
        $this->email->message($htmlContent);

        @$this->email->send();

        echo json_encode(array(
            'success' => true,
            'message' => 'Low stock alert email sent successfully to ' . count($recipient_emails) . ' recipient(s) across departments!'
        ));
        exit;
    }
}

