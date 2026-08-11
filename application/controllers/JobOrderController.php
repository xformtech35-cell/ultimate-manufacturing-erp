<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JobOrderController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('joborder', '', TRUE);
        $this->load->model('units', '', TRUE);
        $this->load->model('salesorder', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('bom', '', TRUE);
        $this->load->model('Drawing_model', '', TRUE);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
 
        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function create_joborder() {
        // Fetch all sales orders
        $all_salesorders = $this->salesorder->get_salesorders_for_joborder($this->user_id);
        
        // Find already linked Sales Orders in joborder_total
        $linked_sos = $this->db->select('so_reference, oc_number')
                               ->from('joborder_total')
                               ->get()
                               ->result_array();
        
        $linked_numbers = [];
        foreach ($linked_sos as $ls) {
            if (!empty($ls['so_reference'])) {
                $linked_numbers[trim($ls['so_reference'])] = true;
            }
            if (!empty($ls['oc_number'])) {
                $linked_numbers[trim($ls['oc_number'])] = true;
            }
        }
        
        // Filter out already linked Sales Orders
        $filtered_salesorders = [];
        if (!empty($all_salesorders)) {
            foreach ($all_salesorders as $so) {
                $so_num = trim($so->number);
                if (!isset($linked_numbers[$so_num])) {
                    $filtered_salesorders[] = $so;
                }
            }
        }
        
        $data['salesorder_list'] = $filtered_salesorders;
        $data['so_list_for_selector'] = $this->salesorder->get_so_list_for_bom($this->user_id);
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $data['joborder_id'] = $this->joborder->get_last_joborder_number($this->user_id);
        $data['company_name'] = $this->joborder->get_company_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('joborder/create_joborder', $data);
    }

    /**
     * AJAX endpoint to get Sales Order items by SO number
     */
    public function get_salesorder_items() {
        $so_number = $this->input->post('so_number');
        
        if (empty($so_number)) {
            echo json_encode(['success' => false, 'message' => 'No Sales Order number provided']);
            return;
        }
        
        // Get Sales Order header/group data
        $so_header = $this->salesorder->get_salesorders_data_group_by($so_number, $this->user_id);
        
        // Debug: Log the header data
        log_message('debug', 'SO Header: ' . json_encode($so_header));
        
        // Get Sales Order items
        $so_items = $this->salesorder->get_salesorders_data($so_number, $this->user_id);
        
        if (empty($so_items)) {
            echo json_encode(['success' => false, 'message' => 'No items found for this Sales Order']);
            return;
        }
        
        // Get GST type from first item (all items in an SO should have the same GST type)
        $gst_type = isset($so_items[0]->gst_type) ? $so_items[0]->gst_type : 'S';
        
        // Format items for the job order table with all fields from database
        $formatted_items = [];
        foreach ($so_items as $item) {
            $product_name = isset($item->product_name) ? trim((string) $item->product_name) : '';
            // Some rows may have empty product_name but still have inventory.item_name populated.
            $fallback_product_name = isset($item->item_name) ? trim((string) $item->item_name) : '';

            // Use product_name as code (from salesorder table). Display name prefers inventory.item_name.
            $product_code = $product_name;
            $product_name_display = $fallback_product_name !== '' ? $fallback_product_name : $product_name;

            // Calculate amount if not already calculated
            $amount = (isset($item->amount)) ? $item->amount : ((isset($item->price) && isset($item->quantity)) ? $item->price * $item->quantity : 0);

            // Don’t skip rows; let the frontend decide what to display.
            $formatted_items[] = [
                'product_name' => $product_name_display,
                'product_code' => $product_code,
                'description' => $item->description ?? '',
                'hsn_code' => $item->hsn_code ?? '',
                'quantity' => $item->quantity ?? 1,
                'unit' => isset($item->unit) ? $item->unit : '',
                'tag_no' => $item->tag_no ?? '',
                'scope' => $item->scope ?? '',
                'stores_remark' => $item->stores_remark ?? '',
                'remark' => $item->remark ?? '',
                'gst' => $item->gst ?? 0,
                'sgst' => $item->sgst ?? 0,
                'cgst' => $item->cgst ?? 0,
                'igst' => $item->igst ?? 0,
                'gst_type' => $gst_type,
                'price' => $item->price ?? 0,
                'amount' => $amount
            ];
        }

        if (empty($formatted_items)) {
            echo json_encode(['success' => false, 'message' => 'No valid items found for this Sales Order']);
            return;
        }

        // Fetch matching BOM items
        $bom_items = [];
        $so_number_clean = trim($so_number);
        
        $this->db->select('number_fk');
        $this->db->from('bom_total');
        $this->db->group_start();
            $this->db->where('oc_number', $so_number_clean);
            $this->db->or_where('number_fk', $so_number_clean);
            if (!empty($so_header['number_fk'])) {
                $this->db->or_where('oc_number', trim($so_header['number_fk']));
            }
            if (!empty($so_header['project_code'])) {
                $this->db->or_where('project_code', trim($so_header['project_code']));
            }
        $this->db->group_end();
        $boms = $this->db->get()->result_array();
        
        if (!empty($boms)) {
            $bom_numbers = array_unique(array_column($boms, 'number_fk'));
            
            // Query BOM line items with clean join to inventory
            $this->db->select('bom.*, inventory.item_name as inv_item_name, inventory.code as inv_code');
            $this->db->from('bom');
            $this->db->join('inventory', 'inventory.code = bom.product_name', 'left');
            $this->db->where_in('bom.number', $bom_numbers);
            $this->db->order_by('bom.bom_id', 'asc');
            $bom_items_raw = $this->db->get()->result_array();
            
            foreach ($bom_items_raw as $item) {
                $raw_pcode = trim($item['product_name'] ?? '');
                $inv_name  = trim($item['inv_item_name'] ?? $item['item_name'] ?? '');
                
                // Fallback direct query if leading/trailing tabs prevented join match
                if (empty($inv_name) && !empty($raw_pcode)) {
                    $inv_row = $this->db->select('item_name')->from('inventory')->where('code', $raw_pcode)->get()->row_array();
                    if (!empty($inv_row['item_name'])) {
                        $inv_name = trim($inv_row['item_name']);
                    }
                }

                // Formulate clear display name
                $display_name = !empty($inv_name) ? ($raw_pcode !== '' ? ($raw_pcode . ' - ' . $inv_name) : $inv_name) : $raw_pcode;
                
                $bom_items[] = [
                    'bom_number' => $item['number'],
                    'product_name' => $display_name,
                    'product_display_name' => $display_name,
                    'product_code' => $raw_pcode,
                    'code' => $raw_pcode,
                    'quantity' => floatval($item['quantity'] ?? 1),
                    'unit' => trim($item['unit'] ?? ''),
                    'description' => $item['description'] ?? '',
                    'tag_no' => $item['tag_no'] ?? '',
                    'scope' => $item['scope'] ?? '',
                    'stores_remark' => $item['stores_remark'] ?? '',
                    'price' => floatval($item['price'] ?? 0),
                    'remark' => $item['remark'] ?? '',
                ];
            }
        }

        // Fetch Drawings
        $drawings = [];
        $project_id = null;
        if (!empty($so_header['project_code'])) {
            $project = $this->db->where('project_code', $so_header['project_code'])->get('project')->row();
            if ($project) {
                $project_id = $project->project_id;
            }
        }
        
        if (!empty($project_id)) {
            $drawings_raw = $this->Drawing_model->get_drawings_by_project($project_id);
            foreach ($drawings_raw as $d) {
                $latest_rev = $this->Drawing_model->get_latest_revision($d->drawing_id);
                $files = [];
                if ($latest_rev) {
                    $files_raw = $this->Drawing_model->get_files_by_revision($latest_rev->revision_id);
                    foreach ($files_raw as $f) {
                        $files[] = [
                            'file_id' => $f->file_id,
                            'file_name' => $f->file_name,
                            'file_path' => $f->file_path,
                            'file_type' => $f->file_type,
                            'file_size' => $f->file_size,
                            'description' => $f->description
                        ];
                    }
                }
                $drawings[] = [
                    'drawing_id' => $d->drawing_id,
                    'drawing_no' => $d->drawing_no,
                    'drawing_name' => $d->drawing_name,
                    'current_revision' => $d->current_revision,
                    'status' => $d->status,
                    'latest_revision' => $latest_rev ? $latest_rev->revision_no : '',
                    'latest_revision_date' => $latest_rev ? $latest_rev->revision_date : '',
                    'files' => $files
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'gst_type' => $gst_type,
            'so_number' => $so_number,
            'customer_id' => $so_header['customer_id_fk'] ?? '',
            'company_name' => $so_header['company_name'] ?? $so_header['fullname'] ?? '',
            'customer_code' => $so_header['customer_code'] ?? '',
            'date' => $so_header['date'] ?? date('Y-m-d'),
            'status' => $so_header['status'] ?? 1,
            'project_code' => $so_header['project_code'] ?? '',
            'project_qty' => $so_header['project_qty'] ?? '',
            'system' => $so_header['system'] ?? '',
            'location' => $so_header['location'] ?? '',
            'capacity' => $so_header['capacity'] ?? '',
            'oc_number' => !empty($so_header['oc_number']) ? $so_header['oc_number'] : $so_number,
            'note' => $so_header['note'] ?? '',
            'items' => $formatted_items,
            'bom_items' => $bom_items,
            'drawings' => $drawings
        ]);
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
        
        $result = $this->joborder->customer_check($company_name, $this->user_id);

        if ($result == FALSE) {
            $this->customer->add_customer($data_customer);
            $this->session->set_flashdata('SUCCESSMSG', "Company added successfully!!");
            redirect('JobOrderController/create_joborder');
        } else {
            $this->session->set_flashdata('INFOMSG', "Company already exist!!");
            redirect('JobOrderController/create_joborder');
        }
    }

    public function add_joborder() {
        $session_data_head = $this->session->userdata('session_data_head');
        // Check if this is an edit operation
        $edit_joborder = $this->input->post('edit_joborder');
        
        // Header fields (stored in joborder_total)
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
        $so_number = $this->input->post('so_number'); // Store reference to original SO

        // Prevent duplicate Job Orders for the same Sales Order
        if ($edit_joborder !== 'edit_joborder') {
            $check_so = !empty($so_number) ? trim($so_number) : (!empty($oc_number) ? trim($oc_number) : '');
            if (!empty($check_so)) {
                $exists = $this->db->select('number_fk')
                                   ->from('joborder_total')
                                   ->group_start()
                                       ->where('so_reference', $check_so)
                                       ->or_where('oc_number', $check_so)
                                   ->group_end()
                                   ->get()
                                   ->row();
                if ($exists) {
                    $this->session->set_flashdata('error', "A Job Order ({$exists->number_fk}) already exists for Sales Order/OC {$check_so}!");
                    redirect('JobOrderController/create_joborder');
                    return;
                }
            }
        }

        // Item fields
        $product_name = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        $tag_no = $this->input->post('tag_no');
        $scope = $this->input->post('scope');
        $stores_remark = $this->input->post('stores_remark');
        $remark = $this->input->post('remark');
        $price = $this->input->post('price');
        $status = $this->input->post('status_i');

        // Ensure arrays
        $product_name = is_array($product_name) ? $product_name : [];
        $quantity = is_array($quantity) ? $quantity : [];
        $description = is_array($description) ? $description : [];
        $unit = is_array($unit) ? $unit : [];
        $tag_no = is_array($tag_no) ? $tag_no : [];
        $scope = is_array($scope) ? $scope : [];
        $stores_remark = is_array($stores_remark) ? $stores_remark : [];
        $remark = is_array($remark) ? $remark : [];
        $price = is_array($price) ? $price : [];
        $status = is_array($status) ? $status : [];


        $item_count = count($product_name);
        
        // Check if joborder_total record exists
        $joborder_total_exists = $this->db
            ->where('number_fk', $number)
            ->get('joborder_total')
            ->row();

        // Parse date from dd-mm-yyyy format to Y-m-d for MySQL
        $date_formatted = date('Y-m-d');
        if (!empty($date)) {
            $date_parts = explode('-', $date);
            if (count($date_parts) == 3) {
                $year = $date_parts[2];
                if (strlen($year) == 2) {
                    $year = '20' . $year;
                }
                $date_formatted = $year . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        $session_data_head = $this->session->userdata('session_data_head');
        $logged_in_uid = $session_data_head['result']['user_id'] ?? $this->user_id;

        // Prepare header data for joborder_total
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
            'so_reference' => $so_number, // Store reference to source SO
            // Default values for required fields
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
            'bom_memo' => '',
            'approved_by' => 0,
            'po_number' => ''
        );

        // Insert or Update joborder_total
        if($joborder_total_exists) {
            $this->db->where('number_fk', $number);
            $this->db->update('joborder_total', $data_total);
            
            if($edit_joborder == 'edit_joborder') {
                $this->joborder->delete_joborder_by_joborder_number($number, $this->user_id);
            }
        } else {
            $this->db->insert('joborder_total', $data_total);
        }

        // Insert JOBORDER items into joborder table
        $batch_data = [];
        for ($i = 0; $i < $item_count; $i++) {
            $is_heading = (isset($product_name[$i]) && $product_name[$i] === '__HEADING__');
            if (!empty($product_name[$i]) && (!empty($quantity[$i]) || $is_heading)) {
                $batch_data[] = array(
                    'customer_id' => $customer_id,
                    'number' => $number,
                    'product_name' => $product_name[$i],
                    'quantity' => $quantity[$i],
                    'description' => isset($description[$i]) ? $description[$i] : '',
                    'unit' => isset($unit[$i]) ? $unit[$i] : '',
                    'tag_no' => isset($tag_no[$i]) ? $tag_no[$i] : '',
                    'scope' => isset($scope[$i]) ? $scope[$i] : '',
                    'stores_remark' => isset($stores_remark[$i]) ? $stores_remark[$i] : '',
                    'remark' => isset($remark[$i]) ? $remark[$i] : '',
                    'price' => isset($price[$i]) ? $price[$i] : 0,
                    'status_i' => isset($status[$i]) ? $status[$i] : $status_main,
                    'uid' => $this->user_id,
                    'item_no' => '',

                    'drawing_no' => '',
                    'size' => '',
                    'moc' => 0
                );
            }
        }
        
        if (!empty($batch_data)) {
            $this->db->insert_batch('joborder', $batch_data);
        }
        
        if($edit_joborder == 'edit_joborder') {
            $this->session->set_flashdata('SUCCESSMSG', "JOBORDER updated successfully!!");
        } else {
            $this->session->set_flashdata('SUCCESSMSG', "JOBORDER added successfully!!");
        }
        
        redirect('JobOrderController/index');
    }

    /**
     * Change Job Order Status (Draft = 1, Sent = 2, Approved = 4, Closed = 6)
     */
    public function update_joborder_status()
    {
        $joborder_id = $this->input->post('jo_id');
        $joborder_number = $this->input->post('jo_number');
        $status = (int)$this->input->post('status');
        $remarks = $this->input->post('remarks');

        $session_data_head = $this->session->userdata('session_data_head');
        $logged_in_uid = $session_data_head['result']['user_id'] ?? $this->user_id;

        $data_status = array(
            'status' => $status,
            'note' => $remarks
        );

        if (in_array($status, [2, 4, 6])) {
            $data_status['approved_by'] = $logged_in_uid;
        }

        if (!empty($joborder_number)) {
            $result = $this->joborder->edit_gst_joborder_status($data_status, $joborder_number, $this->user_id);
        } else {
            $this->db->where('id', $joborder_id)->update('joborder_total', $data_status);
            $result = true;
        }

        $status_label = 'updated';
        switch ($status) {
            case 1: $status_label = 'Set to Draft'; break;
            case 2: $status_label = 'Sent'; break;
            case 4: $status_label = 'Approved'; break;
            case 6: $status_label = 'Closed'; break;
        }

        if ($result) {
            $this->session->set_flashdata('SUCCESSMSG', "Job Order status {$status_label} successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Failed to update Job Order status.");
        }

        redirect('JobOrderController/index');
    }

    /**
     * Forcefully Close Job Order (Manager / Admin Action)
     */
    public function force_close_joborder($id = null)
    {
        if (empty($id)) {
            $id = $this->input->post('jo_id');
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $logged_in_uid = $session_data_head['result']['user_id'] ?? $this->user_id;

        $data_status = array(
            'status' => 6, // Closed
            'approved_by' => $logged_in_uid,
            'note' => 'Force closed by Manager/Admin'
        );

        $this->db->where('id', $id)->update('joborder_total', $data_status);

        $this->session->set_flashdata('SUCCESSMSG', 'Job Order has been Forcefully Closed successfully!');
        redirect('JobOrderController/index');
    }
    
    public function index() {
        $data['joborder_count'] = $this->joborder->get_joborder_count($this->user_id);
        $data['joborder_draft_count'] = $this->joborder->get_joborder_draft_count(1, $this->user_id);
        $data['joborder_sent_count'] = $this->joborder->get_joborder_draft_count(2, $this->user_id);
        $data['joborder_approved_count'] = $this->joborder->get_joborder_draft_count(4, $this->user_id);
        $data['joborder_closed_count'] = $this->joborder->get_joborder_draft_count(6, $this->user_id);
        $data['joborders'] = $this->joborder->get_joborders($this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['joborder_id'] = $this->joborder->get_last_joborder_number($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('joborder/view_joborder', $data);
    }

    public function get_datewise_record() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['joborder_count'] = $this->joborder->get_joborder_count($this->user_id);
        $data['joborder_draft_count'] = $this->joborder->get_joborder_draft_count(1, $this->user_id);
        $data['joborder_sent_count'] = $this->joborder->get_joborder_draft_count(2, $this->user_id);
        $data['joborder_approved_count'] = $this->joborder->get_joborder_draft_count(4, $this->user_id);
        $data['joborder_closed_count'] = $this->joborder->get_joborder_draft_count(6, $this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['joborder_id'] = $this->joborder->get_last_joborder_number($this->user_id);
        $data['joborders'] = $this->joborder->get_datewise_record($from_date, $to_date, $this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('joborder/view_joborder', $data);
    }

    public function get_monthyearwise_record() {
        $month_year = $this->input->post('month_year');
        $data['joborder_count'] = $this->joborder->get_joborder_count($this->user_id);
        $data['joborder_draft_count'] = $this->joborder->get_joborder_draft_count(1, $this->user_id);
        $data['joborder_sent_count'] = $this->joborder->get_joborder_draft_count(2, $this->user_id);
        $data['joborder_approved_count'] = $this->joborder->get_joborder_draft_count(4, $this->user_id);
        $data['joborder_closed_count'] = $this->joborder->get_joborder_draft_count(6, $this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['joborder_id'] = $this->joborder->get_last_joborder_number($this->user_id);
        $data['joborders'] = $this->joborder->get_monthyearwise_record($month_year, $this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('joborder/view_joborder', $data);
    }

    public function get_joborder_data_by_status() {
        $status = $this->uri->segment(3);
        $data['joborders'] = $this->joborder->get_joborder_data_by_status($status, $this->user_id);
        $data['joborder_count'] = $this->joborder->get_joborder_count($this->user_id);
        $data['joborder_draft_count'] = $this->joborder->get_joborder_draft_count(1, $this->user_id);
        $data['joborder_sent_count'] = $this->joborder->get_joborder_draft_count(2, $this->user_id);
        $data['joborder_approved_count'] = $this->joborder->get_joborder_draft_count(4, $this->user_id);
        $data['joborder_closed_count'] = $this->joborder->get_joborder_draft_count(6, $this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('joborder/view_joborder', $data);
    }

    public function show_joborder() {
        $id = $this->uri->segment(3) ? $this->uri->segment(3) : $this->input->get('id');
        $joborder_number_id = $this->joborder->get_joborder_number_from_joborder_total($id, $this->user_id);

        if (!$joborder_number_id) {
            $this->session->set_flashdata('error', 'Job Order not found');
            redirect('JobOrderController/index');
        }

        $number = $joborder_number_id['number_fk'];
        
        $data['show_joborder'] = $this->joborder->get_joborder_data($number, $this->user_id);
        $data['joborder_data_group'] = $this->joborder->get_joborder_data_group_by($number, $this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        
        // Fetch reference Sales Order items
        $so_reference_items = [];
        $joborder_group = $data['joborder_data_group'];
        $so_number = $joborder_group['so_reference'] ?? $joborder_group['oc_number'] ?? '';
        
        if (!empty($so_number)) {
            $so_reference_items = $this->salesorder->get_salesorders_data($so_number, $this->user_id);
        }
        $data['so_reference_items'] = $so_reference_items;
        
        // Fetch Drawings
        $drawings = [];
        $project_id = null;
        $project_code = $joborder_group['project_code'] ?? '';
        
        if (!empty($project_code)) {
            $project = $this->db->where('project_code', $project_code)->get('project')->row();
            if ($project) {
                $project_id = $project->project_id;
            }
        }
        
        if (!empty($project_id)) {
            $drawings_raw = $this->Drawing_model->get_drawings_by_project($project_id);
            foreach ($drawings_raw as $d) {
                $latest_rev = $this->Drawing_model->get_latest_revision($d->drawing_id);
                $files = [];
                if ($latest_rev) {
                    $files_raw = $this->Drawing_model->get_files_by_revision($latest_rev->revision_id);
                    foreach ($files_raw as $f) {
                        $files[] = [
                            'file_id' => $f->file_id,
                            'file_name' => $f->file_name,
                            'file_path' => $f->file_path,
                            'file_type' => $f->file_type,
                            'file_size' => $f->file_size,
                            'description' => $f->description
                        ];
                    }
                }
                $drawings[] = [
                    'drawing_id' => $d->drawing_id,
                    'drawing_no' => $d->drawing_no,
                    'drawing_name' => $d->drawing_name,
                    'current_revision' => $d->current_revision,
                    'status' => $d->status,
                    'latest_revision' => $latest_rev ? $latest_rev->revision_no : '',
                    'latest_revision_date' => $latest_rev ? $latest_rev->revision_date : '',
                    'files' => $files
                ];
            }
        }
        $data['drawings'] = $drawings;
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('joborder/show_joborder', $data);
    }

    public function get_customer_email() {
        $number = trim((string) $this->input->post('number'));

        if ($number === '') {
            echo json_encode(array('success' => false, 'email' => '', 'customer_id' => ''));
            return;
        }

        $result = $this->joborder->get_customer_email($number, $this->user_id);

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

    public function get_customer_mobile() {
        $number = trim((string) $this->input->post('number'));

        if ($number === '') {
            echo json_encode(array('mobile' => ''));
            return;
        }

        $result = $this->joborder->get_customer_mobile($number, $this->user_id);
        echo json_encode($result);
    }

    public function send_joborder_email() {
        $joborder_number = trim((string) $this->input->post('number'));
        $to_email = trim((string) $this->input->post('to_email'));
        $subject = trim((string) $this->input->post('subject'));
        $message = trim((string) $this->input->post('message'));
        $copy_email = $this->input->post('copy_email');

        if ($joborder_number === '' || $to_email === '' || $subject === '') {
            $this->session->set_flashdata('INFOMSG', 'Required email details are missing.');
            redirect('JobOrderController/index');
            return;
        }

        $joborder_data_group = $this->joborder->get_joborder_data_group_by($joborder_number, $this->user_id);
        if (empty($joborder_data_group)) {
            $this->session->set_flashdata('INFOMSG', 'Job Order details not found.');
            redirect('JobOrderController/index');
            return;
        }

        $settings = $this->login->get_settings($this->user_id);
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = !empty($session_data_head2['company_name']) ? $session_data_head2['company_name'] : (isset($settings['company_name']) ? $settings['company_name'] : 'Company');
        $set_company_logo = !empty($session_data_head2['company_logo']) ? base_url() . '/' . ltrim($session_data_head2['company_logo'], '/') : base_url() . ltrim(isset($settings['company_logo']) ? $settings['company_logo'] : '', '/');
        $set_from_email = !empty($session_data_head2['from_email']) ? $session_data_head2['from_email'] : (isset($settings['email']) ? $settings['email'] : '');
        $set_cc_email = !empty($session_data_head2['cc_email']) ? $session_data_head2['cc_email'] : '';

        $joborder_id = isset($joborder_data_group['id']) ? $joborder_data_group['id'] : 0;
        $customer_name = isset($joborder_data_group['fullname']) && $joborder_data_group['fullname'] !== '' ? $joborder_data_group['fullname'] : (isset($joborder_data_group['company_name']) ? $joborder_data_group['company_name'] : 'Customer');
        $issue_date = !empty($joborder_data_group['date']) && $joborder_data_group['date'] !== '0000-00-00' ? date('d-m-Y', strtotime($joborder_data_group['date'])) : date('d-m-Y');
        $download_url = base_url() . 'Download/download_joborder/' . $joborder_id . '/' . $this->user_id;

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
    <title>Job Order</title>
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
            <span style="color:#2f2f36;font-weight:bold;font-size:32px;"><center>Job Order</center></span><br>
            <span style="color:#2f2f36;font-weight:bold;font-size:28px;">' . htmlspecialchars($joborder_number, ENT_QUOTES, 'UTF-8') . '</span><br>
            <span style="color:#a0a0a5;">for <b>' . htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8') . '</b></span><br>
            <span style="color:#a0a0a5;">issued on : <b>' . $issue_date . '</b></span><br>
            <span style="color:#a0a0a5;">from <b>' . htmlspecialchars($set_company_name, ENT_QUOTES, 'UTF-8') . '</b></span>
            <hr>
            <span style="color:#2f2f36;">Please find the Job Order attached via the download link below.</span>
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

        $pdf_file_path = $this->generate_joborder_pdf($joborder_number);
        if (!$pdf_file_path || !file_exists($pdf_file_path)) {
            $this->session->set_flashdata('INFOMSG', 'Failed to generate Job Order PDF.');
            redirect('JobOrderController/index');
            return;
        }

        $this->email->attach($pdf_file_path);
        $this->email->message($htmlContent);

        if ($this->email->send()) {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $data_status = array('status' => 2);
            $this->joborder->edit_gst_joborder_status($data_status, $joborder_number, $this->user_id);
            $this->session->set_flashdata('SUCCESSMSG', 'Email Sent Successfully!!');
        } else {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('INFOMSG', 'Email not Sent Successfully!!');
        }

        redirect('JobOrderController/index');
    }

    private function generate_joborder_pdf($joborder_number)
    {
        $data['show_joborder'] = $this->joborder->get_joborder_data($joborder_number, $this->user_id);
        $data['joborder_data_group'] = $this->joborder->get_joborder_data_group_by($joborder_number, $this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        if (empty($data['show_joborder']) || empty($data['joborder_data_group'])) {
            return false;
        }

        $html = $this->load->view('joborder/print_joborder', $data, true);

        $uploads_dir = FCPATH . 'uploads/joborder/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $file_name = 'Job_Order_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $joborder_number) . '.pdf';
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

        if (!empty($data['settings']['company_name'])) {
            $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $joborder_number . '</div>');
            $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
            $mpdf->SetWatermarkText($data['settings']['company_name']);
            $mpdf->showWatermarkText = true;
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
        }

        $mpdf->WriteHTML($html);
        $mpdf->Output($pdf_file_path, 'F');

        return $pdf_file_path;
    }

    public function edit_joborder_details() {
        $id = $this->uri->segment(3);
        $joborder_number_id = $this->joborder->get_joborder_number_from_joborder_total($id, $this->user_id);
        
        if (!$joborder_number_id) {
            $this->session->set_flashdata('error', 'Job Order not found');
            redirect('JobOrderController/index');
        }
        
        $number = $joborder_number_id['number_fk'];
        
        $data['show_joborder'] = $this->joborder->get_joborder_data($number, $this->user_id);
        $data['joborder_data_group'] = $this->joborder->get_joborder_data_group_by($number, $this->user_id);
        $data['status_result'] = $this->joborder->get_status($number, $this->user_id);
        $data['customer_result'] = $this->customer->get_customer($this->user_id);
        $data['unit_result'] = $this->units->get_units($this->user_id);
        $data['company_name'] = $this->joborder->get_company_name($this->user_id);
        $data['project_code_result'] = $this->salesorder->get_project_code($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['salesorder_list'] = $this->salesorder->get_salesorders_for_joborder($this->user_id);
        $data['so_list_for_selector'] = $this->salesorder->get_so_list_for_bom($this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('joborder/edit_joborder', $data);
    }

    public function delete_joborder_by_joborder_number() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $joborder_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        
        $result = $this->joborder->delete_joborder_by_joborder_number($joborder_number, $this->user_id);
        $result1 = $this->joborder->delete_joborder_total_by_joborder_number($joborder_number, $this->user_id);
        
        if (($result == TRUE) && ($result1 == TRUE)) {
            $this->session->set_flashdata('SUCCESSMSG', "JOBORDER deleted successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "JOBORDER not deleted successfully!!");
        }
        redirect('JobOrderController/index');
    }

    /**
     * Export JOBORDER to Excel with proper formatting and styling including company logo and headers
     */
    public function export_joborder_excel() {
        // Get JOBORDER ID from URL
        $id = $this->uri->segment(3);
       
        // Validate ID
        if (empty($id)) {
            $this->session->set_flashdata('ERRORMSG', "Invalid JOBORDER ID!!");
            redirect('JobOrderController/index');
        }
       
        // Get JOBORDER number from JOBORDER total
        $joborder_number_data = $this->joborder->get_joborder_number_from_joborder_total($id, $this->user_id);
        if (empty($joborder_number_data)) {
            $this->session->set_flashdata('ERRORMSG', "JOBORDER not found!!");
            redirect('JobOrderController/index');
        }
       
        $number = $joborder_number_data['number_fk'];
       
        // Get JOBORDER data
        $show_joborder = $this->joborder->get_joborder_data($number, $this->user_id);
        $joborder_data_group = $this->joborder->get_joborder_data_group_by($number, $this->user_id);
        $settings = $this->login->get_settings($this->user_id);
        $unit_result = $this->units->get_units($this->user_id);
       
        // Check if data exists
        if (empty($show_joborder) || empty($joborder_data_group)) {
            $this->session->set_flashdata('ERRORMSG', "JOBORDER data not found!!");
            redirect('JobOrderController/index');
        }
       
        // Helper function to strip HTML tags and decode entities
        $stripHtml = function($text) {
            if (empty($text)) return '';
            $text = strip_tags($text);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = trim(preg_replace('/\s+/', ' ', $text));
            return $text;
        };
       
        // Load PhpSpreadsheet
        require_once FCPATH . 'vendor/autoload.php';
       
        try {
            // Create new Spreadsheet object
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('JOBORDER');
 
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator($settings['company_name'] ?? 'System')
                ->setLastModifiedBy('System')
                ->setTitle("JOBORDER - " . ($joborder_data_group['number'] ?? 'Unknown'))
                ->setSubject("Job Order Details");
 
            // Set default font
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
 
            // Define styles
            $boldStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']]
            ];
           
            // Peach header style for Job Order title
            $peachTitleStyle = [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '000000']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FCD5B4']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ];
           
            // Green header style for table headers
            $greenHeaderStyle = [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E7D32']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
           
            $subTitleStyle = [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E9']
                ]
            ];
 
            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(8);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(40);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getColumnDimension('E')->setWidth(10);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(25);
            $sheet->getColumnDimension('I')->setWidth(25);
           
            // Starting row
            $row = 1;
 
            // Company logo section
            $sheet->mergeCells('A' . $row . ':I' . $row);
           
            if (!empty($settings['company_logo'])) {
                $logo_path = FCPATH . $settings['company_logo'];
               
                if (file_exists($logo_path)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Company Logo');
                    $drawing->setDescription('Company Logo');
                    $drawing->setPath($logo_path);
                    $drawing->setHeight(160);
                    $drawing->setWidth(400);
                    $drawing->setCoordinates('C' . $row);
                    $drawing->setOffsetX(200);
                    $drawing->setOffsetY(10);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row)->setRowHeight(80);
                } else {
                    $sheet->setCellValue('A' . $row, $settings['company_name'] ?? 'Company Name');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
                    $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('000000');
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
                }
            } else {
                $sheet->setCellValue('A' . $row, $settings['company_name'] ?? 'Company Name');
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('000000');
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
            }
            $row += 2;
           
            // Tagline
            $sheet->mergeCells('A' . $row . ':I' . $row);
            $sheet->setCellValue('A' . $row, '*Ultimate Technologies for Fluid Automation*');
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
            $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('000000');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
            $row += 2;
 
            // Job Order Title
            $sheet->mergeCells('A' . $row . ':I' . $row);
            $sheet->setCellValue('A' . $row, 'Job Order');
            $sheet->getStyle('A' . $row)->applyFromArray($peachTitleStyle);
            $row += 2;
 
            // Job Order Header Information
            $sheet->setCellValue('A' . $row, 'SYSTEM');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['system'] ?? 'Dosing System'));
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
 
            $sheet->setCellValue('A' . $row, 'Location');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['location'] ?? 'Hindalco'));
           
            $sheet->setCellValue('F' . $row, 'Customer Code:');
            $sheet->getStyle('F' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('G' . $row, $stripHtml($joborder_data_group['customer_code'] ?? ''));
            $row++;
 
            $sheet->setCellValue('A' . $row, 'CLIENT');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['company_name'] ?? ''));
           
            $sheet->setCellValue('F' . $row, 'Date:');
            $sheet->getStyle('F' . $row)->applyFromArray($boldStyle);
            $joborder_date = !empty($joborder_data_group['date']) ? date('d-m-Y', strtotime($joborder_data_group['date'])) : '';
            $sheet->setCellValue('G' . $row, $joborder_date);
            $row++;
 
            $sheet->setCellValue('A' . $row, 'CAPACITY');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['capacity'] ?? ''));
           
            $sheet->setCellValue('F' . $row, 'Revision:');
            $sheet->getStyle('F' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('G' . $row, '0');
            $row++;
 
            $sheet->setCellValue('A' . $row, 'OC No.');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['oc_number'] ?? ''));
           
            $sheet->setCellValue('F' . $row, 'Project Quantity:');
            $sheet->getStyle('F' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('G' . $row, $stripHtml($joborder_data_group['project_qty'] ?? ''));
            $row += 2;
 
            // Main Table Header
            $headers = ['SR.NO', 'PRODUCT NAME', 'DESCRIPTION', 'QTY', 'UNIT', 'TAG NO.', 'SCOPE', 'STORES REMARK IF MATERIAL IS STOCK Y/N', 'REMARK'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $row, $header);
                $sheet->getStyle($col . $row)->applyFromArray($greenHeaderStyle);
                $col++;
            }
            $row++;
 
            // Job Order Items
            if (!empty($show_joborder)) {
                $sr_no = 1;
                $last_row_data = $row;
               
                foreach ($show_joborder as $item) {
                    $col = 'A';
                   
                    $sheet->setCellValue($col++ . $row, $sr_no);
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $productName = $stripHtml($item->product_name . " - " . ($item->item_name ?? ''));
                    $sheet->setCellValue($col++ . $row, $productName);
                    $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('B' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $description = $stripHtml($item->description ?? '');
                    $sheet->setCellValue($col++ . $row, $description);
                    $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $sheet->setCellValue($col++ . $row, $item->quantity ?? '');
                    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('D' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $unit_value = '';
                    if (!empty($item->unit) && !empty($unit_result)) {
                        foreach ($unit_result as $unit) {
                            if ($unit->unit == $item->unit) {
                                $unit_value = $unit->unit;
                                break;
                            }
                        }
                    }
                    $sheet->setCellValue($col++ . $row, $unit_value);
                    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('E' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $tagNo = $stripHtml($item->tag_no ?? '');
                    $sheet->setCellValue($col++ . $row, $tagNo);
                    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $scope = $stripHtml($item->scope ?? '');
                    $sheet->setCellValue($col++ . $row, $scope);
                    $sheet->getStyle('G' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $stores_remark = '';
                    if (isset($item->stores_remark)) {
                        if ($item->stores_remark == 'Y') {
                            $stores_remark = 'Yes';
                        } elseif ($item->stores_remark == 'N') {
                            $stores_remark = 'No';
                        }
                    }
                    $sheet->setCellValue($col++ . $row, $stores_remark);
                    $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('H' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $remark = $stripHtml($item->remark ?? '');
                    $sheet->setCellValue($col++ . $row, $remark);
                    $sheet->getStyle('I' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('000000');
                   
                    $row++;
                    $sr_no++;
                }
               
                $sheet->getStyle('A' . $last_row_data . ':I' . ($row-1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
            } else {
                $sheet->mergeCells('A' . $row . ':I' . $row);
                $sheet->setCellValue('A' . $row, 'No items found');
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('000000');
                $row++;
            }
 
            $row += 2;
 
            // Job Order Details Section
            $sheet->setCellValue('A' . $row, 'JOBORDER Details :');
            $sheet->getStyle('A' . $row)->applyFromArray($subTitleStyle);
            $row++;
 
            $sheet->setCellValue('A' . $row, 'JOBORDER Number:');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['number'] ?? ''));
            $row++;
 
            $sheet->setCellValue('A' . $row, 'JOBORDER Date:');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $joborder_date);
            $row++;
 
            $sheet->setCellValue('A' . $row, 'JOBORDER Status:');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $statusArr = array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled');
            $status_main = $statusArr[$joborder_data_group['status']] ?? 'Draft';
            $sheet->setCellValue('B' . $row, $status_main);
            $row++;
 
            $sheet->setCellValue('A' . $row, 'Company Name:');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['company_name'] ?? ''));
            $row++;
 
            $sheet->setCellValue('A' . $row, 'Prepared By:');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['prepare_by'] ?? ''));
            $row++;
 
            $sheet->setCellValue('A' . $row, 'Approved By:');
            $sheet->getStyle('A' . $row)->applyFromArray($boldStyle);
            $sheet->setCellValue('B' . $row, $stripHtml($joborder_data_group['approved_by'] ?? 'N/A'));
            $row += 2;
 
            // Notes Section
            if (!empty($joborder_data_group['note'])) {
                $sheet->setCellValue('A' . $row, 'Notes');
                $sheet->getStyle('A' . $row)->applyFromArray($subTitleStyle);
                $row++;
               
                $note = $stripHtml($joborder_data_group['note']);
                $sheet->mergeCells('A' . $row . ':I' . $row);
                $sheet->setCellValue('A' . $row, $note);
                $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
                $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('000000');
                $row += 2;
            }
 
            // Footer Section
            if (!empty($settings['joborder_footer'])) {
                $sheet->mergeCells('A' . $row . ':I' . $row);
                $footerText = $stripHtml($settings['joborder_footer']);
                $sheet->setCellValue('A' . $row, $footerText);
                $sheet->getStyle('A' . $row)->getFont()->setSize(9);
                $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('000000');
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
                $row++;
            }
 
            // Computer Generated Message
            $sheet->mergeCells('A' . $row . ':I' . $row);
            $sheet->setCellValue('A' . $row, 'This is Computer Generated JOBORDER');
            $sheet->getStyle('A' . $row)->getFont()->setSize(9)->setItalic(true);
            $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('000000');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
 
            // Set filename
            $filename = 'JOBORDER_' . str_replace('/', '-', ($joborder_data_group['number'] ?? 'Unknown')) . '_' . date('Ymd_His') . '.xlsx';
 
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
 
            // Create writer and save
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
           
        } catch (Exception $e) {
            log_message('error', 'Excel export error: ' . $e->getMessage());
            $this->session->set_flashdata('ERRORMSG', "Excel export failed: " . $e->getMessage());
            redirect('JobOrderController/index');
        }
    }

    public function export_all_joborders() {
        try {
            // Create new Spreadsheet object
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("System")
                ->setLastModifiedBy("System")
                ->setTitle("Job Order List")
                ->setSubject("Job Order Details")
                ->setDescription("Export of all Job Order details");

            // Heading
            $sheet->setCellValue('A1', 'JOB ORDER LIST REPORT');
            $sheet->mergeCells('A1:J1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Generated on: ' . date('d-m-Y'));
            $sheet->mergeCells('A2:J2');
            $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $headers = ['Sr.No.', 'Status', 'Date', 'Number', 'Company Name', 'SO Number', 'OC Number', 'Project Code'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '3', $header);
                
                // Style table headers
                $style = $sheet->getStyle($column . '3');
                $style->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF3C8DBC');
                $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                
                $column++;
            }

            $joborders = $this->joborder->get_joborders($this->user_id);
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
            foreach ($joborders as $key) {
                $status = isset($key->status) ? $key->status : 0;
                $statusStr = isset($statusArr[$status]) ? $statusArr[$status] : 'Pending';
                $dateStr = (!empty($key->date) && $key->date !== '0000-00-00') ? date('d-m-Y', strtotime($key->date)) : '';
                $joNumber = isset($key->number_fk) ? $key->number_fk : '';
                
                $soNumber = '';
                if (!empty($key->so_reference)) {
                    $soNumber = $key->so_reference;
                } elseif (!empty($key->oc_number)) {
                    $soNumber = $key->oc_number;
                }

                $sheet->setCellValue('A' . $rowNum, $i);
                $sheet->setCellValue('B' . $rowNum, $statusStr);
                $sheet->setCellValue('C' . $rowNum, $dateStr);
                $sheet->setCellValue('D' . $rowNum, $joNumber);
                $sheet->setCellValue('E' . $rowNum, isset($key->company_name) ? $key->company_name : '');
                $sheet->setCellValue('F' . $rowNum, $soNumber);
                $sheet->setCellValue('G' . $rowNum, isset($key->oc_number) ? $key->oc_number : '');
                $sheet->setCellValue('H' . $rowNum, isset($key->project_code) ? $key->project_code : '');

                // Align center for specific columns
                $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $rowNum++;
                $i++;
            }

            // Auto-size columns
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Add border to all data cells
            if ($rowNum > 4) {
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                $sheet->getStyle('A3:H' . ($rowNum - 1))->applyFromArray($styleArray);
            }

            // Output file
            $filename = 'JobOrder_List_Export_' . date('Ymd_His') . '.xlsx';
            
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
            log_message('error', 'Excel export error in export_all_joborders: ' . $e->getMessage());
            $this->session->set_flashdata('ERRORMSG', "Excel export failed: " . $e->getMessage());
            redirect('JobOrderController/index');
        }
    }
}
