<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GrnController extends MY_Controller
{
    protected $user_id;
    protected $user_email;

    private function getGrnNumberFromUri($startSegment = 3, $endSegment = 8)
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
        $this->load->model('grn', '', TRUE);
        $this->load->model('user', '', TRUE);
        $this->load->model('Email_model');

        $session_data_head = $this->session->userdata('session_data_head');

        if (!$session_data_head || !isset($session_data_head['result']['user_id'])) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('ERRORMSG', "Session expired. Please login again.");
            redirect('LoginController/logout');
            exit();
        }

        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
        $this->user_email = $session_data_head['result']['user_email'] ?? '';
        if (empty($this->user_email) && !empty($this->user_id)) {
            $u = $this->db->select('user_email, username')->where('user_id', $this->user_id)->get('user')->row_array();
            if ($u) {
                $this->user_email = !empty($u['user_email']) ? $u['user_email'] : $u['username'];
            }
        }
    }

    public function index()
    {
        $this->grn_approvals();
    }

    public function grn_index()
    {
        $data['grn'] = $this->grn->get_grn($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['grn_id'] = $this->grn->get_last_grn_number($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['po_number'] = $this->grn->get_po_number($this->user_id);
        $data['result'] = $this->estimate->get_customer($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/view_grn', $data);
    }

    public function create_grn()
    {
        $data['grn_id'] = $this->grn->get_last_grn_number($this->user_id);
        $data['invoice_id'] = $this->invoice->get_last_invoice_number($this->user_id);
        $data['result'] = $this->estimate->get_customer($this->user_id);
        $data['po_number'] = $this->grn->get_po_number_with_pending($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/create_grn', $data);
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

    public function get_customer_name_to_append_dropdown()
    {
        $this->db->select('customer_firstname');
        $this->db->from('customer');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $data = array("customer_firstname" => $row['customer_firstname']);
            echo json_encode($data);
        } else {
            echo json_encode(array("customer_firstname" => ""));
        }
    }

    public function add_grn()
    {
        $supplier_id = $this->input->post('supplier_id');
        $grn_number = $this->input->post('grn_number');
        $po_number_fk = $this->input->post('po_number_fk');

        // Auto-format grn_number with OC/SO suffix if not already present
        if (!empty($grn_number) && !preg_match('/\/\([0-9]+\/[0-9]+\)$/', $grn_number) && !empty($po_number_fk)) {
            if (preg_match('/\/\(([0-9]+\/[0-9]+)\)$/', $po_number_fk, $m)) {
                $grn_number = $grn_number . '/(' . $m[1] . ')';
            } else {
                $po_rec = $this->db->where('number_fk', $po_number_fk)->get('po_total')->row_array();
                if ($po_rec) {
                    $so = !empty($po_rec['so_no']) ? $po_rec['so_no'] : ($po_rec['oc_no'] ?? '');
                    if (!empty($so) && preg_match('/(?:([0-9]{4})|([0-9]{2}-[0-9]{2})).*?([0-9]+)$/', trim($so), $m)) {
                        $s_fy = !empty($m[1]) ? $m[1] : str_replace('-', '', $m[2]);
                        $s_seq = $m[3];
                        $grn_number = $grn_number . '/(' . $s_fy . '/' . $s_seq . ')';
                    }
                }
            }
        }
        $date = $this->input->post('date');
        $note = $this->input->post('note');
        $product_name = $this->input->post('term');
        $quantity = $this->input->post('quantity');
        $hsn = $this->input->post('hsn');

        $gst_per = $this->input->post('gst_per');
        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');

        $received_quantity = $this->input->post('received_quantity');
        $pending_quantity = $this->input->post('pending_quantity');
        $price = $this->input->post('price');
        $description = $this->input->post('description');
        $invoice_number = $this->input->post('invoice_number');
        $invoice_date = $this->input->post('invoice_date');
        $total_grn_amount1 = $this->input->post('total_quotation_amount');

        $item_count = is_array($product_name) ? count($product_name) : 0;
        $data = array();

        for ($i = 0; $i < $item_count; $i++) {
            if (!empty($product_name[$i]) && $quantity[$i] != '' && $price[$i] != '') {
                $data[] = array(
                    'supplier_id' => $supplier_id,
                    'grn_number' => $grn_number,
                    'po_number_fk' => $po_number_fk,
                    'date' => $this->input->post('date'),
                    'note' => $note,
                    'product_name' => $product_name[$i],
                    'quantity' => $quantity[$i],
                    'hsn_code' => $hsn[$i],
                    'gst' => $gst_per[$i],
                    'sgst' => $sgst[$i],
                    'cgst' => $cgst[$i],
                    'received_quantity' => $received_quantity[$i],
                    'pending_quantity' => $pending_quantity[$i],
                    'price' => $price[$i],
                    'description' => $description[$i],
                    'invoice_number' => $invoice_number,
                    'invoice_date' => $invoice_date,
                    'uid' => $this->user_id,
                );

                $pending_qty = array(
                    'po_pending_quantity' => $pending_quantity[$i],
                    'uid' => $this->user_id,
                );

                $this->grn->add_pending_qty_to_po_table($pending_qty, $po_number_fk, $product_name[$i], $this->user_id);
            }
        }

        // Guard against empty item list
        if (empty($data)) {
            $this->session->set_flashdata('INFOMSG', "Please add at least one product item to create a GRN.");
            redirect('GrnController/create_grn');
            return;
        }

        $this->db->insert_batch('grn', $data);

        // Add GRN total with approval status - Round off the final amount
        $data_toatl_amount = array(
            'number_fk' => $grn_number,
            'total' => round($total_grn_amount1, 0),
            'uid' => $this->user_id,
            'approval_status' => 'pending_approval'
        );

        $result = $this->grn->add_total_grn_amount($data_toatl_amount);

        // Handle approval workflow — wrapped in try/catch so GRN always saves
        // even if approval configuration is incomplete
        $approval_message = '';
        try {
            // Fix: use $po_number_fk (was incorrectly $po_number — undefined variable)
            $location_id = $this->grn->get_po_location($po_number_fk);

            // Get GRN approval workflow
            $approval_workflow = $this->grn->get_grn_approval_workflow($total_grn_amount1, 'GRN', $location_id);

            // Create approval requests
            if (!empty($approval_workflow['workflow'])) {
                foreach ($approval_workflow['workflow'] as $level => $approval) {
                    $this->db->insert('grn_approvals', [
                        'grn_number' => $grn_number,
                        'approval_level' => $approval['level_name'],
                        'approver_role' => $approval['role'],
                        'approver_email' => !empty($approval['email']) ? $approval['email'] : 'admin@system.local',
                        'status' => $approval['status'],
                        'level' => $level,
                        'created_at' => date('Y-m-d H:i:s'),
                        'uid' => $this->user_id
                    ]);
                }

                // Update grn_total with approval status
                $this->db->where('number_fk', $grn_number);
                $this->db->update('grn_total', [
                    'approval_status' => 'pending_approval',
                    'approval_level' => $approval_workflow['current_level'] ?? 'quality',
                    'current_approver' => $approval_workflow['current_approver'] ?? ''
                ]);

                // Send approval notification email
                $grn_data = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
                if ($grn_data && !empty($approval_workflow['current_approver'])) {
                    $this->Email_model->send_grn_approval_notification(
                        $grn_number,
                        $approval_workflow['current_approver'],
                        $total_grn_amount1,
                        ucfirst(str_replace('_', ' ', $approval_workflow['current_level'] ?? 'quality')),
                        $grn_data
                    );
                }

                $current_level_label = ucfirst(str_replace('_', ' ', $approval_workflow['current_level'] ?? 'Quality'));
                $approval_message = " Waiting for approval from " . $current_level_label . ".";
            }
        } catch (Exception $e) {
            // Log the error but don't block GRN creation
            error_log("GRN approval workflow error: " . $e->getMessage());
        }

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "GRN added successfully!" . $approval_message);
            redirect('GrnController/grn_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "GRN not added successfully!!");
            redirect('GrnController/grn_index');
        }
    }

    public function get_settings()
    {
        $result = $this->estimate->get_settings($this->user_id);
        echo json_encode($result);
    }

    public function get_customer_email()
    {
        $number = $this->input->post('number');
        $result = $this->estimate->get_customer_email($number, $this->user_id);
        echo json_encode($result);
    }

    public function get_po_details_details()
    {
        $po_number = $this->input->post('po_number');
        $arr = $this->grn->get_po_details_details($po_number, $this->user_id);
        echo json_encode($arr);
    }

    public function show_grn()
    {
        $grn_number = $this->getGrnNumberFromUri();

        if (empty($grn_number)) {
            show_404();
        }

        $data['show_grn'] = $this->grn->get_grn_data($grn_number, $this->user_id);
        $data['grn_data_group'] = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
        $data['approval_history'] = $this->grn->get_grn_approval_details($grn_number);
        $data['settings'] = $this->login->get_settings($this->user_id);

        if (empty($data['grn_data_group'])) {
            $this->session->set_flashdata('ERRORMSG', 'GRN not found or you do not have access to this GRN.');
            redirect('GrnController/grn_index');
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/show_grn_with_approvals', $data);
    }

    public function delete_grn_by_grn_number()
    {
        $grn_number = $this->getGrnNumberFromUri();
        $result = $this->grn->delete_grn_by_grn_number($grn_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "GRN deleted successfully!!");
            redirect('GrnController/grn_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "GRN not deleted successfully!!");
            redirect('GrnController/grn_index');
        }
    }

    public function edit_grn($grn_id = '')
    {
        if (empty($grn_id)) {
            show_404();
        }

        $data['grn_data_group'] = $this->grn->get_grn_by_id($grn_id, $this->user_id);
        $data['show_grn'] = $this->grn->get_grn_items_by_grn_id($grn_id, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        if (empty($data['grn_data_group'])) {
            $this->session->set_flashdata('ERRORMSG', 'GRN not found or you do not have access to this GRN.');
            redirect('GrnController/grn_index');
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/edit_grn', $data);
    }

    public function update_grn_data()
    {
        $grn_id = $this->input->post('grn_id');

        if (empty($grn_id)) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid GRN ID');
            redirect('GrnController/grn_index');
        }

        // Get GRN data
        $grn_data = $this->grn->get_grn_by_id($grn_id, $this->user_id);
        if (empty($grn_data)) {
            $this->session->set_flashdata('ERRORMSG', 'GRN not found');
            redirect('GrnController/grn_index');
        }

        // Update GRN main table (only basic GRN fields)
        $grn_update_data = array(
            'invoice_number' => $this->input->post('invoice_number'),
            'date' => $this->input->post('date'),
            'invoice_date' => $this->input->post('invoice_date')
        );

        // Update GRN table
        $this->db->where('grn_id', $grn_id);
        $grn_update_result = $this->db->update('grn', $grn_update_data);

        if ($grn_update_result !== FALSE) {
            // Update line item data
            $quantities = $this->input->post('quantity');
            $received_quantities = $this->input->post('received_quantity');
            $descriptions = $this->input->post('description');

            if (!empty($quantities) && is_array($quantities)) {
                $grn_items = $this->grn->get_grn_items_by_grn_id($grn_id, $this->user_id);
                $i = 0;
                foreach ($grn_items as $item) {
                    $item_update = array();
                    
                    if (isset($quantities[$i])) {
                        $item_update['quantity'] = floatval($quantities[$i]);
                    }
                    if (isset($received_quantities[$i])) {
                        $item_update['received_quantity'] = floatval($received_quantities[$i]);
                        // Calculate pending = quantity - received
                        $qty = (isset($quantities[$i]) ? floatval($quantities[$i]) : $item->quantity);
                        $item_update['pending_quantity'] = max(0, ($qty - floatval($received_quantities[$i])));
                    }
                    if (isset($descriptions[$i])) {
                        $item_update['description'] = $descriptions[$i];
                    }

                    if (!empty($item_update)) {
                        $this->db->where('grn_id', $item->grn_id);
                        $this->db->update('grn', $item_update);
                    }
                    $i++;
                }
            }

            $this->session->set_flashdata('SUCCESSMSG', 'GRN Updated Successfully!');
            redirect('GrnController/grn_index');
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Failed to update GRN');
            redirect('GrnController/edit_grn/' . $grn_id);
        }
    }

    public function get_all_po_data()
    {
        $po_number = $this->input->post('po_number');
        $arr = $this->grn->get_all_po_data($po_number, $this->user_id);
        echo json_encode($arr);
    }

    public function inspection_report()
    {
        $grn_number = $this->getGrnNumberFromUri();

        $data['grn_summary'] = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);

        if (empty($data['grn_summary'])) {
            $this->session->set_flashdata('ERRORMSG', 'GRN not found or you do not have access to this GRN.');
            redirect('GrnController/grn_index');
        }
        

        if (method_exists($this->grn, 'get_grn_data_with_inspection')) {
            $data['inspection_details'] = $this->grn->get_grn_data_with_inspection($grn_number, $this->user_id);
        } else {
            $data['inspection_details'] = $this->grn->get_grn_data($grn_number, $this->user_id);
        }

        $data['settings'] = $this->login->get_settings($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/inspection_report', $data);
    }

    public function save_inspection()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('grn_number', 'GRN Number', 'required');
        $this->form_validation->set_rules('inspection_date', 'Inspection Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('ERRORMSG', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        $grn_number = $this->input->post('grn_number');
        $inspector_name = $this->input->post('inspector_name');
        $inspection_date = $this->input->post('inspection_date');
        $overall_status = $this->input->post('overall_status');
        $overall_quality = $this->input->post('overall_quality');
        $overall_notes = $this->input->post('overall_notes');
        $update_stock = $this->input->post('update_stock');
        $notify_accounts = $this->input->post('notify_accounts');

        $session_data_head = $this->session->userdata('session_data_head');
        $inspected_by = $session_data_head['result']['user_id'] ?? $this->user_id;

        $product_names = $this->input->post('product_name');
        $quantities = $this->input->post('quantity');
        $accepted_qty = $this->input->post('accepted_quantity');
        $rejected_qty = $this->input->post('rejected_quantity');
        $rejection_reasons = $this->input->post('rejection_reason');
        $quality_ratings = $this->input->post('quality_rating');
        $packaging_conditions = $this->input->post('packaging_condition');
        $batch_numbers = $this->input->post('batch_number');
        $expiry_dates = $this->input->post('expiry_date');
        $storage_locations = $this->input->post('storage_location');
        $item_notes = $this->input->post('inspection_notes');

        $this->db->trans_start();

        $success_count = 0;
        $item_count = count($product_names);
        $total_accepted = 0;
        $total_rejected = 0;

        for ($i = 0; $i < $item_count; $i++) {
            if (!empty($product_names[$i])) {
                $accepted = $accepted_qty[$i] ?? 0;
                $rejected = $rejected_qty[$i] ?? 0;

                $inspection_data = array(
                    'grn_number' => $grn_number,
                    'item_code' => $product_names[$i],
                    'inspected_quantity' => $quantities[$i] ?? 0,
                    'accepted_quantity' => $accepted,
                    'rejected_quantity' => $rejected,
                    'quality_rating' => $quality_ratings[$i] ?? 'GOOD',
                    'packaging_condition' => $packaging_conditions[$i] ?? 'INTACT',
                    'inspection_notes' => $item_notes[$i] ?? NULL,
                    'inspected_by' => $inspected_by,
                    'inspection_date' => $inspection_date,
                    'uid' => $this->user_id
                );

                if ($this->grn->save_inspection_data($inspection_data)) {
                    $success_count++;

                    if ($update_stock && $accepted > 0) {
                        $this->grn->update_stock_after_inspection($product_names[$i], $accepted, $this->user_id);
                        
                        // Add entry to stock ledger
                        $stock = $this->grn->get_inventory_stock_count($product_names[$i], $this->user_id);
                        $ledger_data = array(
                            'transaction_type' => 'GRN',
                            'reference_no' => $grn_number,
                            'item_code' => $product_names[$i],
                            'quantity' => floatval($accepted),
                            'balance_quantity' => floatval($stock['stock'] ?? 0),
                            'transaction_date' => date('Y-m-d H:i:s'),
                            'remarks' => 'Received via GRN Inspection for ' . $grn_number,
                            'uid' => $this->user_id
                        );
                        $this->db->insert('stock_ledger', $ledger_data);
                    }

                    $total_accepted += $accepted;
                    $total_rejected += $rejected;

                    $log_data = array(
                        'grn_number' => $grn_number,
                        'item_code' => $product_names[$i],
                        'inspected_quantity' => $quantities[$i] ?? 0,
                        'accepted_quantity' => $accepted,
                        'rejected_quantity' => $rejected,
                        'quality_rating' => $quality_ratings[$i] ?? 'GOOD',
                        'packaging_condition' => $packaging_conditions[$i] ?? 'INTACT',
                        'inspection_notes' => $item_notes[$i] ?? NULL,
                        'inspected_by' => $inspected_by,
                        'inspection_date' => $inspection_date . ' ' . date('H:i:s'),
                        'uid' => $this->user_id
                    );
                    $this->grn->create_inspection_log($log_data);
                }
            }
        }

        $update_data = array(
            'inspection_status' => $overall_status,
            'quality_rating' => $overall_quality,
            'inspected_by' => $inspected_by,
            'inspection_date' => $inspection_date,
            'rejected_quantity' => $total_rejected,
            'rejection_reason' => $overall_notes
        );

        $this->grn->update_grn_inspection_status($grn_number, $update_data, $this->user_id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('ERRORMSG', 'Failed to save inspection data. Please try again.');
        } else {
            if ($notify_accounts) {
                $this->_notify_accounts_department($grn_number, $total_accepted, $total_rejected, $overall_status);
            }

            // Notify Purchase Manager if any items were rejected
            if ($total_rejected > 0) {
                $this->_notify_purchase_manager_rejection(
                    $grn_number,
                    $total_accepted,
                    $total_rejected,
                    $overall_notes,
                    'inspection'
                );
            }

            $this->session->set_flashdata('SUCCESSMSG', 'Inspection completed successfully! ' . $success_count . ' items processed.');
        }

        redirect('GrnController/inspection_report/' . $grn_number);
    }

    private function _notify_accounts_department($grn_number, $total_accepted, $total_rejected, $status)
    {
        $grn_details = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);

        // Fetch accounts department users
        $accounts_users = $this->db->select('u.username, u.user_email')
                                   ->from('user u')
                                   ->join('role r', 'u.role = r.role_id', 'left')
                                   ->where('r.role_name', 'Accounts')
                                   ->where('u.user_email IS NOT NULL')
                                   ->where('u.user_email !=', '')
                                   ->get()
                                   ->result_array();

        if (!empty($accounts_users)) {
            $this->load->model('Email_model');
            foreach ($accounts_users as $acc_user) {
                $this->Email_model->send_accounts_notification(
                    $grn_number,
                    $acc_user['user_email'],
                    $grn_details['total'] ?? 0,
                    $grn_details
                );
            }
        }
        log_message('info', 'Accounts notified for GRN: ' . $grn_number . ' - Status: ' . $status);
    }

    /**
     * Notify all Purchase Manager role users when a GRN has items rejected.
     * Sends per-item accepted/rejected quantity details.
     */
    private function _notify_purchase_manager_rejection($grn_number, $total_accepted, $total_rejected, $remarks = '', $rejection_type = 'inspection')
    {
        $this->load->model('Email_model');

        // Get GRN summary data
        $grn_data = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
        if (empty($grn_data)) {
            // Try fetching without uid filter
            $grn_total = $this->db->where('number_fk', $grn_number)->get('grn_total')->row_array();
            $grn_data  = $grn_total ?? [];
        }

        // For approval-type rejections, fetch item quantities from inspection log or grn table
        if ($rejection_type === 'approval') {
            $inspection_items = $this->db
                ->select('item_code, SUM(accepted_quantity) as accepted_quantity, SUM(rejected_quantity) as rejected_quantity, SUM(inspected_quantity) as inspected_quantity, inspection_notes')
                ->from('grn_inspection_log')
                ->where('grn_number', $grn_number)
                ->group_by('item_code')
                ->get()->result_array();

            if (empty($inspection_items)) {
                // Fall back to grn table with quantities
                $inspection_items = $this->db
                    ->select('product_name as item_code, received_quantity as inspected_quantity, received_quantity as accepted_quantity, 0 as rejected_quantity')
                    ->from('grn')
                    ->where('grn_number', $grn_number)
                    ->get()->result_array();
            }

            // Recalculate totals from items
            $total_accepted = 0;
            $total_rejected = 0;
            foreach ($inspection_items as $it) {
                $total_accepted += floatval($it['accepted_quantity'] ?? 0);
                $total_rejected += floatval($it['rejected_quantity'] ?? 0);
            }
        } else {
            // Inspection type: read latest inspection log
            $inspection_items = $this->db
                ->select('item_code, accepted_quantity, rejected_quantity, inspected_quantity, inspection_notes, rejection_reason')
                ->from('grn_inspection_log')
                ->where('grn_number', $grn_number)
                ->order_by('id', 'desc')
                ->get()->result_array();
        }

        // Fetch all Purchase Manager emails (role name "Purchase Manager" or "Purchase")
        $pm_users = $this->db->select('u.user_email, u.username')
            ->from('user u')
            ->join('role r', 'u.role = r.role_id', 'left')
            ->group_start()
                ->like('r.role_name', 'Purchase', 'after')
                ->or_like('r.role_name', 'purchase', 'after')
            ->group_end()
            ->where('u.user_email IS NOT NULL')
            ->where('u.user_email !=', '')
            ->get()->result_array();

        if (empty($pm_users)) {
            // Fallback: try Admin role
            $pm_users = $this->db->select('u.user_email, u.username')
                ->from('user u')
                ->join('role r', 'u.role = r.role_id', 'left')
                ->where('r.role_name', 'Admin')
                ->where('u.user_email IS NOT NULL')
                ->where('u.user_email !=', '')
                ->limit(1)
                ->get()->result_array();
        }

        if (!empty($pm_users)) {
            foreach ($pm_users as $pm) {
                $this->Email_model->send_grn_rejection_to_purchase_manager(
                    $grn_number,
                    $pm['user_email'],
                    $grn_data,
                    $inspection_items,
                    $total_accepted,
                    $total_rejected,
                    $remarks,
                    $rejection_type
                );
            }
            log_message('info', 'Purchase Manager notified for GRN rejection: ' . $grn_number . ' (' . count($pm_users) . ' recipients)');
        } else {
            log_message('error', 'No Purchase Manager user found to notify for GRN: ' . $grn_number);
        }
    }

    public function conduct_inspection()
    {
        $grn_number = $this->input->get('grn');

        if (empty($grn_number)) {
            $grn_number = $this->getGrnNumberFromUri();

            if (empty($grn_number)) {
                $this->session->set_flashdata('ERRORMSG', 'GRN number is required.');
                redirect('GrnController/grn_index');
            }
        } else {
            $grn_number = urldecode($grn_number);
        }

        $data['grn_details'] = $this->grn->get_grn_data($grn_number, $this->user_id);
        $data['grn_summary'] = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
        $data['users'] = $this->user->get_user($role = null);

        // Fetch Accounts department recipients for display
        $data['accounts_users'] = $this->db->select('u.username, u.user_email')
                                           ->from('user u')
                                           ->join('role r', 'u.role = r.role_id', 'left')
                                           ->where('r.role_name', 'Accounts')
                                           ->where('u.user_email IS NOT NULL')
                                           ->where('u.user_email !=', '')
                                           ->get()
                                           ->result_array();

        if (empty($data['grn_details'])) {
            $this->session->set_flashdata('ERRORMSG', 'GRN not found: ' . $grn_number);
            redirect('GrnController/grn_index');
        }

        $data['inspection_data'] = array();
        $data['session_data_head'] = $this->session->userdata('session_data_head');
        $data['settings'] = $this->login->get_settings($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/conduct_inspection', $data);
        $this->load->view('admin/footer');
    }

    // ==================== GRN APPROVAL METHODS ====================

    public function grn_approvals()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $role_name = $session_data_head['result']['role_name'] ?? '';
        $is_admin = (strtolower($role_name) === 'admin');

        if (empty($this->user_email) && !$is_admin) {
            $this->session->set_flashdata('ERRORMSG', 'User email not found!');
            redirect('Dashboard');
        }

        $data['pending_approvals'] = $this->grn->get_pending_grn_approvals($this->user_email);
        $data['approval_history'] = $this->grn->get_grn_approval_history($this->user_email);
        $data['total_pending'] = count($data['pending_approvals']);
        $data['total_history'] = count($data['approval_history']);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/grn_approvals', $data);
    }

    public function process_grn_approval()
    {
        $approval_id = $this->input->post('approval_id');
        $action = $this->input->post('action');
        $remarks = $this->input->post('remarks');

        $session_data_head = $this->session->userdata('session_data_head');
        $role_name = $session_data_head['result']['role_name'] ?? '';
        $is_admin = (strtolower($role_name) === 'admin');
        
        $actor_email = !empty($this->user_email) ? $this->user_email : 'admin@system';

        $result = $this->grn->process_grn_approval($approval_id, $action, $remarks, $actor_email, $this->user_id);

        if ($result) {
            // Get approval details
            $approval = $this->db->where('approval_id', $approval_id)->get('grn_approvals')->row_array();

            if ($action == 'approved') {
                $grn_details = $this->db->where('number_fk', $approval['grn_number'])->get('grn_total')->row_array();

                if ($grn_details['approval_status'] == 'approved') {
                    // GRN fully approved - notify accounts and update stock
                    if (intval($grn_details['stock_updated'] ?? 0) === 0) {
                        $grn_items = $this->db->where('grn_number', $approval['grn_number'])->get('grn')->result_array();
                        foreach ($grn_items as $item) {
                            if (!empty($item['product_name'])) {
                                $stock = $this->grn->get_inventory_stock_count($item['product_name'], $grn_details['uid']);
                                $new_stock = floatval($item['received_quantity']) + ($stock['stock'] ?? 0);
                                $allocated = floatval($stock['allocated_stock'] ?? 0);
                                $new_available = max(0, $new_stock - $allocated);
                                
                                $target_uid = !empty($stock['uid']) ? $stock['uid'] : $grn_details['uid'];
                                
                                $update_data = array(
                                    'stock' => $new_stock,
                                    'available_stock' => $new_available
                                );
                                $this->db->where('uid', $target_uid);
                                $this->db->where('code', $item['product_name']);
                                $this->db->update('inventory', $update_data);

                                // Add entry to stock ledger
                                $ledger_data = array(
                                    'transaction_type' => 'GRN',
                                    'reference_no' => $approval['grn_number'],
                                    'item_code' => $item['product_name'],
                                    'quantity' => floatval($item['received_quantity']),
                                    'balance_quantity' => $new_stock,
                                    'transaction_date' => date('Y-m-d H:i:s'),
                                    'remarks' => 'Received via GRN ' . $approval['grn_number'],
                                    'uid' => $target_uid
                                );
                                $this->db->insert('stock_ledger', $ledger_data);
                            }
                        }
                        $this->db->where('number_fk', $approval['grn_number'])->update('grn_total', array('stock_updated' => 1));
                    }
                    $this->_notify_grn_fully_approved($approval['grn_number']);
                    $this->session->set_flashdata('SUCCESSMSG', 'GRN fully approved! Stock updated in inventory.');
                } else {
                    // Move to next approver
                    $this->_notify_next_approver($approval['grn_number']);
                    $this->session->set_flashdata('SUCCESSMSG', 'GRN approved! Waiting for next approver.');
                }
            } else {
                // Notify Purchase Manager when a GRN is rejected in the approval workflow
                $this->_notify_purchase_manager_rejection(
                    $approval['grn_number'],
                    0,
                    0,
                    $remarks,
                    'approval'
                );
                $this->session->set_flashdata('SUCCESSMSG', 'GRN rejected successfully!');
            }
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Failed to process approval!');
        }

        redirect('GrnController/grn_approvals');
    }

    private function _notify_next_approver($grn_number)
    {
        // Get next pending approval
        $next_approval = $this->db->where('grn_number', $grn_number)
            ->where('status', 'pending')
            ->where('approval_level !=', 'not_required')
            ->order_by('level', 'asc')
            ->limit(1)
            ->get('grn_approvals')
            ->row_array();

        if ($next_approval) {
            $grn_data = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
            if ($grn_data) {
                $this->Email_model->send_grn_approval_notification(
                    $grn_number,
                    $next_approval['approver_email'],
                    $grn_data['total'] ?? 0,
                    ucfirst(str_replace('_', ' ', $next_approval['approval_level'])),
                    $grn_data
                );
            }
        }
    }

    private function _notify_grn_fully_approved($grn_number)
    {
        // Notify GRN creator that GRN is fully approved
        $grn_data = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
        if ($grn_data) {
            // Get GRN creator email
            $creator_email = $this->db->select('user_email')
                ->from('user')
                ->where('user_id', $this->user_id)
                ->get()
                ->row()
                ->user_email ?? '';

            if ($creator_email) {
                $this->Email_model->send_grn_fully_approved_notification(
                    $grn_number,
                    $creator_email,
                    $grn_data['total'] ?? 0,
                    $grn_data
                );
            }

            // Notify accounts department
            $accounts_email = $this->grn->get_approver_email_by_role('Accounts');
            if ($accounts_email) {
                $this->Email_model->send_accounts_notification(
                    $grn_number,
                    $accounts_email,
                    $grn_data['total'] ?? 0,
                    $grn_data
                );
            }
        }
    }

    public function show_grn_approval_details($grn_number_encoded)
    {
        $grn_number = str_replace('-', '/', $grn_number_encoded);

        $data['show_grn'] = $this->grn->get_grn_data($grn_number, $this->user_id);
        $data['grn_data_group'] = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
        $data['approval_history'] = $this->grn->get_grn_approval_details($grn_number);
        $data['settings'] = $this->login->get_settings($this->user_id);

        if (empty($data['grn_data_group'])) {
            $this->session->set_flashdata('ERRORMSG', 'GRN not found or you do not have access to this GRN.');
            redirect('GrnController/grn_approvals');
            return;
        }

        // Get user's approval for this GRN
        $session_data_head = $this->session->userdata('session_data_head');
        $role_name = $session_data_head['result']['role_name'] ?? '';
        $is_admin = (strtolower($role_name) === 'admin');

        if ($is_admin) {
            $data['user_approval'] = $this->db->where('grn_number', $grn_number)
                ->where('status', 'pending')
                ->order_by('level', 'asc')
                ->get('grn_approvals')
                ->row_array();
        } else {
            $data['user_approval'] = $this->db->where('grn_number', $grn_number)
                ->where('approver_email', $this->user_email)
                ->where('status', 'pending')
                ->get('grn_approvals')
                ->row_array();
        }

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/view_grn_approval', $data);
    }

    public function get_grn_approval_status()
    {
        $grn_number = $this->input->post('grn_number');

        $grn_total = $this->db->where('number_fk', $grn_number)
            ->get('grn_total')
            ->row_array();

        $approvals = $this->db->where('grn_number', $grn_number)
            ->order_by('level', 'asc')
            ->get('grn_approvals')
            ->result_array();

        echo json_encode([
            'grn_total' => $grn_total,
            'approvals' => $approvals
        ]);
    }

    public function approve_all_grn_items()
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $grn_number = $this->input->post('grn_number');
            $remarks = $this->input->post('remarks');

            $session_data_head = $this->session->userdata('session_data_head');
            $role_name = $session_data_head['result']['role_name'] ?? '';
            $is_admin = (strtolower($role_name) === 'admin');

            // Get all pending approvals for this GRN
            $pending_query = $this->db->where('grn_number', $grn_number)
                ->where('status', 'pending')
                ->where('approval_level !=', 'not_required');

            // Non-admin only sees their own approvals
            if (!$is_admin && !empty($this->user_email)) {
                $pending_query->where('approver_email', $this->user_email);
            }

            $pending_approvals = $pending_query->get('grn_approvals')->result_array();

            $actor_email = !empty($this->user_email) ? $this->user_email : 'admin@system';
            $approved_count = 0;

            foreach ($pending_approvals as $approval) {
                $result = $this->grn->process_grn_approval(
                    $approval['approval_id'],
                    'approved',
                    $remarks . ' (Bulk approved)',
                    $actor_email,
                    $this->user_id
                );

                if ($result) {
                    $approved_count++;
                }
            }

            if ($approved_count > 0) {
                $this->session->set_flashdata('SUCCESSMSG', "Successfully approved {$approved_count} GRN items.");
            } else {
                $this->session->set_flashdata('INFOMSG', "No pending approvals found to approve.");
            }

            redirect('GrnController/grn_approvals');
        }
    }

    public function grn_approval_history()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $role_name = $session_data_head['result']['role_name'] ?? '';
        $is_admin  = (strtolower($role_name) === 'admin');

        if (empty($this->user_email) && !$is_admin) {
            $this->session->set_flashdata('ERRORMSG', 'User email not found!');
            redirect('Dashboard');
        }

        // Get completed approvals (approved/rejected)
        $query = $this->db
            ->select('ga.*, gt.total, s.company_name as supplier_name, g.date, u.username as action_by_name, r.role_name as action_by_role')
            ->from('grn_approvals ga')
            ->join('grn_total gt', 'ga.grn_number = gt.number_fk', 'left')
            ->join('grn g', 'ga.grn_number = g.grn_number', 'left')
            ->join('supplier s', 'g.supplier_id = s.supplier_id', 'left')
            ->join('user u', 'ga.action_by = u.user_email', 'left')
            ->join('role r', 'u.role = r.role_id', 'left');

        if (!$is_admin && !empty($this->user_email)) {
            $query->where('ga.approver_email', $this->user_email);
        }

        $data['approval_history'] = $query
            ->where_in('ga.status', ['approved', 'rejected'])
            ->group_by('ga.approval_id')
            ->order_by('ga.action_date', 'DESC')
            ->get()
            ->result();

        $data['total_history'] = count($data['approval_history']);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/grn_approval_history', $data);
    }

    // Method to view GRN details (for approvers)
    public function view_grn_details($grn_number_encoded)
    {
        $grn_number = str_replace('-', '/', $grn_number_encoded);

        $data['show_grn'] = $this->grn->get_grn_data($grn_number, $this->user_id);
        $data['grn_data_group'] = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
        $data['approval_history'] = $this->grn->get_grn_approval_details($grn_number);
        $data['settings'] = $this->login->get_settings($this->user_id);

        if (empty($data['grn_data_group'])) {
            $this->session->set_flashdata('ERRORMSG', 'GRN not found or you do not have access to this GRN.');
            redirect('GrnController/grn_approvals');
        }

        // Get user's pending approval for this GRN
        $session_data_head = $this->session->userdata('session_data_head');
        $role_name = $session_data_head['result']['role_name'] ?? '';
        $is_admin = (strtolower($role_name) === 'admin');

        if ($is_admin) {
            $data['user_approval'] = $this->db->where('grn_number', $grn_number)
                ->where('status', 'pending')
                ->order_by('level', 'asc')
                ->get('grn_approvals')
                ->row_array();
        } else {
            $data['user_approval'] = $this->db->where('grn_number', $grn_number)
                ->where('approver_email', $this->user_email)
                ->where('status', 'pending')
                ->get('grn_approvals')
                ->row_array();
        }

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('grn/view_grn_approval', $data);
    }

    // Export GRN approval report
    public function export_grn_approval_report()
    {
        if (empty($this->user_email)) {
            $this->session->set_flashdata('ERRORMSG', 'User email not found!');
            redirect('Dashboard');
        }

        // Get approval history for export
        $approval_history = $this->db
            ->select('ga.*, gt.total, s.company_name as supplier_name, g.date, g.po_number_fk')
            ->from('grn_approvals ga')
            ->join('grn_total gt', 'ga.grn_number = gt.number_fk', 'left')
            ->join('grn g', 'ga.grn_number = g.grn_number', 'left')
            ->join('supplier s', 'g.supplier_id = s.supplier_id', 'left')
            ->where('ga.approver_email', $this->user_email)
            ->where_in('ga.status', ['approved', 'rejected'])
            ->group_by('ga.approval_id')
            ->order_by('ga.action_date', 'DESC')
            ->get()
            ->result();

        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $headers = [
            'GRN Number',
            'Supplier',
            'PO Number',
            'GRN Date',
            'Amount (₹)',
            'Approval Level',
            'Approver Role',
            'Status',
            'Action Date',
            'Remarks',
            'Action By'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Add data rows
        $row = 2;
        foreach ($approval_history as $history) {
            $sheet->setCellValue('A' . $row, $history->grn_number ?? 'N/A');
            $sheet->setCellValue('B' . $row, $history->supplier_name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $history->po_number_fk ?? 'N/A');

            // Format date
            $date = 'N/A';
            if (!empty($history->date) && $history->date !== '0000-00-00' && $history->date !== null) {
                $date = date('d-m-Y', strtotime($history->date));
            }
            $sheet->setCellValue('D' . $row, $date);

            // Format amount
            $amount = $history->total ?? 0;
            $sheet->setCellValue('E' . $row, $amount);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // Format approval level
            $level = $history->approval_level ?? '';
            $level_text = ucfirst(str_replace('_', ' ', $level));
            $sheet->setCellValue('F' . $row, $level_text);

            $sheet->setCellValue('G' . $row, $history->approver_role ?? 'Approver');
            $sheet->setCellValue('H' . $row, ucfirst($history->status ?? 'pending'));

            // Format action date
            $action_date = 'N/A';
            if (!empty($history->action_date) && $history->action_date !== '0000-00-00 00:00:00' && $history->action_date !== null) {
                $action_date = date('d-m-Y H:i:s', strtotime($history->action_date));
            } elseif (!empty($history->created_at) && $history->created_at !== '0000-00-00 00:00:00' && $history->created_at !== null) {
                $action_date = date('d-m-Y H:i:s', strtotime($history->created_at));
            }
            $sheet->setCellValue('I' . $row, $action_date);

            $sheet->setCellValue('J' . $row, $history->remarks ?? 'No remarks');
            $sheet->setCellValue('K' . $row, $history->action_by ?? 'System');

            $row++;
        }

        // Style headers
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCCCCC');

        // Auto-size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set filename and headers
        $filename = 'grn_approval_report_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    // Get GRN approval statistics
    public function get_grn_approval_stats()
    {
        if (empty($this->user_email)) {
            echo json_encode(['error' => 'User not authenticated']);
            return;
        }

        $model_stats = $this->grn->get_approval_statistics($this->user_email);
        $current_month = date('Y-m');

        $stats = [
            'total_pending' => $model_stats['pending'],
            'total_approved' => $model_stats['approved'],
            'total_rejected' => $model_stats['rejected'],
            'monthly_pending' => $this->grn->get_monthly_grn_approval_count($this->user_email, 'pending', $current_month),
            'monthly_approved' => $this->grn->get_monthly_grn_approval_count($this->user_email, 'approved', $current_month)
        ];

        echo json_encode($stats);
    }

    public function get_grn_supplier_contact()
    {
        $grn_number = $this->input->post('number');
        $result = $this->grn->get_grn_supplier_contact($grn_number, $this->user_id);
        echo json_encode($result);
    }

    public function send_grn_email()
    {
        $session_data_head2 = $this->session->userdata('session_data_head2');
        if (!is_array($session_data_head2)) {
            $session_data_head2 = array();
        }

        $set_company_name = isset($session_data_head2['company_name']) ? $session_data_head2['company_name'] : 'Company';
        $set_company_logo = isset($session_data_head2['company_logo']) ? base_url() . '/' . $session_data_head2['company_logo'] : '';
        $set_from_email = isset($session_data_head2['from_email']) ? $session_data_head2['from_email'] : '';
        $set_cc_email = isset($session_data_head2['cc_email']) ? $session_data_head2['cc_email'] : '';

        $grn_number = $this->input->post('number');
        $grn_data_group = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);

        if (empty($grn_data_group)) {
            $this->session->set_flashdata('INFOMSG', 'GRN not found.');
            redirect('GrnController/grn_index');
            return;
        }

        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');
        $download_url = base_url() . 'Pdf/grn_pdf/' . $grn_number . '/' . $this->user_id;

        $pdf_file_path = $this->generate_grn_pdf($grn_number);
        if (!$pdf_file_path || !file_exists($pdf_file_path)) {
            $this->session->set_flashdata('INFOMSG', 'Failed to generate GRN PDF.');
            redirect('GrnController/grn_index');
            return;
        }

        $this->load->library('email');
        $this->email->set_mailtype('html');
        if (strpos(base_url(), 'localhost') !== false) {

    // Local server
    $this->email->from($set_from_email, $set_company_name);

} else {

    // Live server
    $this->email->from("noreply@uwsenvirotech.com", $set_company_name);

}
        $this->email->to($to_email);
        $this->email->subject($subject);

        if ($copy_email && !empty($set_cc_email)) {
            $this->email->cc($set_cc_email);
        }

        $this->email->attach($pdf_file_path);
        $this->email->message($this->create_grn_email_html($grn_number, $grn_data_group, $message, $set_company_name, $set_company_logo, $download_url));

        if ($this->email->send()) {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('SUCCESSMSG', 'GRN email sent successfully!!');
        } else {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('INFOMSG', 'GRN email not sent successfully!!');
        }

        redirect('GrnController/grn_index');
    }

    private function generate_grn_pdf($grn_number)
    {
        $data['show_grn'] = $this->grn->get_grn_data($grn_number, $this->user_id);
        $data['grn_data_group'] = $this->grn->get_grn_data_group_by($grn_number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        $html = $this->load->view('admin/grn_print', $data, true);

        $uploads_dir = FCPATH . 'uploads/grn/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $file_name = 'GRN_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $grn_number) . '.pdf';
        $pdf_file_path = $uploads_dir . $file_name;

        if (!class_exists('\\Mpdf\\Mpdf')) {
            require_once APPPATH . '../vendor/autoload.php';
        }

        if (class_exists('\\Mpdf\\Mpdf')) {
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

    private function create_grn_email_html($grn_number, $grn_data_group, $custom_message, $company_name, $company_logo, $download_url = '')
    {
        $vendor_name = isset($grn_data_group['fullname']) ? $grn_data_group['fullname'] : (isset($grn_data_group['company_name']) ? $grn_data_group['company_name'] : 'Vendor');
        $grn_date = !empty($grn_data_group['date']) ? date('d-m-Y', strtotime($grn_data_group['date'])) : date('d-m-Y');
        $po_number = !empty($grn_data_group['po_number_fk']) ? $grn_data_group['po_number_fk'] : 'N/A';
        $grand_total = isset($grn_data_group['total']) ? (float) $grn_data_group['total'] : 0;

        return '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>GRN</title>
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
                <span style="color:#2f2f36;font-weight:bold;font-size:32px;"><center>GRN</center></span><br>
                <span style="color:#2f2f36;font-weight:bold;font-size:32px;">' . $grn_number . '</span><br>
                <span style="color:#a0a0a5;">for <b>' . $vendor_name . '</b></span><br>
                <span style="color:#a0a0a5;">GRN date : <b>' . $grn_date . '</b></span><br>
                <span style="color:#a0a0a5;">PO number : <b>' . $po_number . '</b></span><br>
                <span style="color:#a0a0a5;">from <b>' . $company_name . '</b></span>
                <hr>
                <span style="color:#2f2f36;">Please find attached our GRN PDF.</span>
                <hr>
                <span style="color:#2f2f36;"><b>Message :</b> ' . nl2br(htmlspecialchars($custom_message)) . '</span>
                <hr>
                <span style="color:#2f2f36;font-size:18px">Grand Total : <b>' . number_format(round($grand_total, 0), 0) . ' INR</b></span>
                <hr>
                ' . (!empty($download_url) ? '<a href="' . $download_url . '" style="background-color:#00929f;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px" target="_blank">Download in browser</a><br><hr>' : '') . '
                <span style="color:#2f2f36; font-size:12px;"><b>Note:</b> The GRN PDF is attached to this email for your convenience.</span>
            </div>
        </div>
    </body>
    </html>';
    }
}
