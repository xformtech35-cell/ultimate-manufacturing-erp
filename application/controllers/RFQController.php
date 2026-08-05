<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RFQController extends MY_Controller
{
    protected $user_id;

    function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('requisition');
        $this->load->model('RFQ_model', 'rfq');
        $this->load->model('Quotation_model'); // Load Quotation Model

        $session_data = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data['result']['user_id'] ?? 1);
 
        if (($session_data['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            redirect("LoginController/logout");
        }
    }

    // -------------------------------------------------------------
    // VIEW ALL RFQs
    // -------------------------------------------------------------
    public function index()
    {
        $data['rfqs'] = $this->rfq->get_rfqs();
        $session_data = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data);
        $this->load->view('rfq/view_rfqs', $data);
    }

    // -------------------------------------------------------------
    // Convert PR → RFQ (GET METHOD)
    // -------------------------------------------------------------
    public function convert_to_rfq($pr_id = null)
    {
        if (!$pr_id) {
            $pr_id = $this->input->get('pr_id');
            if (!$pr_id) {
                $pr_id = $this->uri->segment(3);
            }
        }

        $data = array();

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $item_ids = $this->input->post('item_id');
            if (!empty($item_ids)) {
                $data['pr_items']  = $this->requisition->get_requisition_items_by_item_ids($item_ids);
            }
        } else {
            $get_item_id = $this->input->get('item_id');
            if ($get_item_id) {
                $data['pr_items'] = $this->requisition->get_requisition_items_by_item_ids(array($get_item_id));
            }
        }

        if (empty($data['pr_items']) && $pr_id) {
            $data['pr_items'] = $this->requisition->get_requisition_items($pr_id);
        }

        if (empty($data['pr_items'])) {
            $this->session->set_flashdata('ERRORMSG', 'No items found to convert to RFQ');
            redirect('RequisitionController/view_requisition_order?str=All');
            return;
        }

        $data['suppliers'] = $this->rfq->get_suppliers();

        // Auto-generate RFQ Number
        $data['rfq_no'] = $this->rfq->generate_rfq_no();

        $data['user_emails'] = $this->db->select('user_email, username')->get('user')->result_array();

        $session_data = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data);
        $this->load->view('rfq/create_rfq', $data);
    }

    // -------------------------------------------------------------
    // SAVE RFQ
    // -------------------------------------------------------------
   
    public function save_rfq()
    {
        $pr_id    = $this->input->post('pr_id');
        $rfq_no   = $this->input->post('rfq_no');
        $rfq_date = $this->input->post('rfq_date');
        $suppliers = $this->input->post('suppliers');
        $items     = $this->input->post('item_id');
        $send_email = $this->input->post('send_email'); // Checkbox for email option
        $additional_cc = $this->input->post('additional_cc');
        if (is_array($additional_cc)) {
            $additional_cc = implode(',', $additional_cc);
        } else {
            $additional_cc = $additional_cc ?: '';
        }

        // Self-healing database check to add additional_cc column if it doesn't exist
        if (!$this->db->field_exists('additional_cc', 'rfq')) {
            $this->db->query("ALTER TABLE `{$this->db->dbprefix}rfq` ADD COLUMN `additional_cc` TEXT DEFAULT NULL AFTER `email_sent_date`");
        }

        $mysql_rfq_date = date('Y-m-d', strtotime($rfq_date));

        // Insert RFQ master
        $rfq_data = [
            "pr_id"         => $pr_id,
            "rfq_no"        => $rfq_no,
            "rfq_date"      => $mysql_rfq_date,
            "created_by"    => $this->user_id,
            "email_sent"    => ($send_email == '1') ? 0 : 0, // Default to 0, will update if emails sent
            "additional_cc" => !empty($additional_cc) ? $additional_cc : null
        ];

        $rfq_id = $this->rfq->insert_rfq($rfq_data);

        // Get all items first
        $all_items = $this->requisition->get_requisition_items_by_item_ids($items);

        // Create a lookup array for quick access
        $item_lookup = [];
        foreach ($all_items as $item) {
            $item_lookup[$item->item_id] = $item;
        }

        // Insert RFQ Items
        $rfq_items = [];
        foreach ($items as $item_id) {
            if (isset($item_lookup[$item_id])) {
                $item = $item_lookup[$item_id];

                $rfq_items[] = [
                    "rfq_id"        => $rfq_id,
                    "item_code"     => $item->item_code,
                    "description"   => $item->description,
                    "quantity"      => $item->quantity,
                    "unit"          => $item->unit,
                    "quoted_price"  => null,
                    "gst_percentage" => null,
                    "delivery_terms" => null,
                    "payment_terms" => null,
                    "validity_days" => null
                ];
            }
        }

        $this->rfq->insert_rfq_items($rfq_items);

        // Save RFQ Vendors
        if (!empty($suppliers)) {
            // Self-healing database check to add emails_to and emails_cc columns if they don't exist
            if (!$this->db->field_exists('emails_to', 'rfq_suppliers')) {
                $this->db->query("ALTER TABLE `{$this->db->dbprefix}rfq_suppliers` ADD COLUMN `emails_to` TEXT DEFAULT NULL AFTER `supplier_id`");
            }
            if (!$this->db->field_exists('emails_cc', 'rfq_suppliers')) {
                $this->db->query("ALTER TABLE `{$this->db->dbprefix}rfq_suppliers` ADD COLUMN `emails_cc` TEXT DEFAULT NULL AFTER `emails_to`");
            }

            $supplier_data = [];
            $supplier_emails = $this->input->post('supplier_emails') ?: [];
            $supplier_email_types = $this->input->post('supplier_email_type') ?: [];
            $new_supplier_emails = $this->input->post('new_supplier_emails') ?: [];

            foreach ($suppliers as $sid) {
                // Persist new emails to supplier table if provided
                if (!empty($new_supplier_emails[$sid])) {
                    $supplier_row = $this->db->select('email')->where('supplier_id', $sid)->get('supplier')->row_array();
                    if ($supplier_row) {
                        $curr_emails_str = $supplier_row['email'] ?? '';
                        $curr_emails_str = str_replace([';', ' ', '/'], ',', $curr_emails_str);
                        $curr_emails_arr = array_filter(array_map('trim', explode(',', $curr_emails_str)));

                        $updated = false;
                        foreach ($new_supplier_emails[$sid] as $new_email) {
                            $new_email = trim($new_email);
                            if (!empty($new_email) && !in_array($new_email, $curr_emails_arr)) {
                                $curr_emails_arr[] = $new_email;
                                $updated = true;
                            }
                        }

                        if ($updated) {
                            $new_emails_str = implode(', ', $curr_emails_arr);
                            $this->db->where('supplier_id', $sid)->update('supplier', ['email' => $new_emails_str]);
                        }
                    }
                }

                $to_arr = [];
                $cc_arr = [];

                if (isset($supplier_emails[$sid])) {
                    foreach ($supplier_emails[$sid] as $email) {
                        $type = $supplier_email_types[$sid][$email] ?? 'main';
                        if ($type === 'cc') {
                            $cc_arr[] = $email;
                        } else {
                            $to_arr[] = $email;
                        }
                    }
                }

                $supplier_data[] = [
                    "rfq_id"      => $rfq_id,
                    "supplier_id" => $sid,
                    "emails_to"   => !empty($to_arr) ? implode(',', $to_arr) : null,
                    "emails_cc"   => !empty($cc_arr) ? implode(',', $cc_arr) : null,
                    "email_sent"  => 0
                ];
            }
            $this->rfq->save_rfq_suppliers($supplier_data);
        }

        // Send emails if requested
        $email_result = null;
        if ($send_email == '1' && !empty($suppliers)) {
            // Load email helper
            $this->load->helper('email');

            // Send emails
            $email_result = send_rfq_to_vendors($rfq_id, $suppliers);

            if ($email_result['sent_count'] > 0) {
                $this->session->set_flashdata('SUCCESSMSG', "RFQ Created Successfully and emails sent to " . $email_result['sent_count'] . " vendor(s)");

                // Update RFQ email status
                $this->rfq->update_rfq_email_status($rfq_id, 1);
            } else {
                $this->session->set_flashdata('WARNINGMSG', "RFQ Created Successfully but failed to send emails to vendors");
            }
        } else {
            $this->session->set_flashdata('SUCCESSMSG', "RFQ Created Successfully");
        }

        redirect("RFQController/index");
    }
    // -------------------------------------------------------------
    // SHOW RFQ DETAILS WITH QUOTATIONS
    // -------------------------------------------------------------
    // public function show_rfq($rfq_id)
    // {
    //     $data['rfq']        = $this->Quotation_model->get_rfq_details($rfq_id);
    //     $data['items']      = $this->Quotation_model->get_rfq_items($rfq_id);
    //     $data['quotations'] = $this->Quotation_model->get_quotations_by_rfq($rfq_id);
    //     $data['suppliers']  = $this->Quotation_model->get_suppliers($rfq_id);

    //     $session_data = $this->session->userdata('session_data_head');

    //     $this->load->view('admin/header_side_bar', $session_data);
    //     $this->load->view('rfq/show_rfq', $data);
    // }
    public function show_rfq($rfq_id)
{
    $data['rfq']        = $this->Quotation_model->get_rfq_details($rfq_id);
    $data['items']      = $this->Quotation_model->get_rfq_items($rfq_id);
    $data['quotations'] = $this->Quotation_model->get_quotations_by_rfq($rfq_id);

    // Get all vendors assigned to this RFQ (keep all selectable to allow revised quotes/revisions)
    $data['suppliers']  = $this->Quotation_model->get_suppliers($rfq_id);

    $session_data = $this->session->userdata('session_data_head');

    $this->load->view('admin/header_side_bar', $session_data);
    $this->load->view('rfq/show_rfq', $data);
}

    // -------------------------------------------------------------
    // SAVE VENDOR QUOTATION (Item-wise pricing)
    // -------------------------------------------------------------
    public function saveQuotation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('supplier_id', 'Vendor', 'required');
        $this->form_validation->set_rules('quote_date', 'Quote Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Please fill all required fields.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $rfq_id = $this->input->post('rfq_id');
        $supplier_id = $this->input->post('supplier_id');

        // Self-healing database check to add revision_no and is_latest columns if they don't exist
        if (!$this->db->field_exists('revision_no', 'vendor_quotations')) {
            $this->db->query("ALTER TABLE `{$this->db->dbprefix}vendor_quotations` ADD COLUMN `revision_no` INT DEFAULT 0 AFTER `supplier_id`");
        }
        if (!$this->db->field_exists('is_latest', 'vendor_quotations')) {
            $this->db->query("ALTER TABLE `{$this->db->dbprefix}vendor_quotations` ADD COLUMN `is_latest` TINYINT DEFAULT 1 AFTER `status`");
        }

        // Get the highest revision number and set the new revision number
        $highest_revision = $this->Quotation_model->get_highest_revision($rfq_id, $supplier_id);
        $new_revision = $highest_revision + 1;

        // If this is a revision, set is_latest = 0 on all previous quotations for this vendor+RFQ
        if ($new_revision > 0) {
            $this->db->where('rfq_id', $rfq_id)
                     ->where('supplier_id', $supplier_id)
                     ->update('vendor_quotations', ['is_latest' => 0]);
        }

        // Save main quotation
        $quotation_data = [
            'rfq_id' => $rfq_id,
            'supplier_id' => $supplier_id,
            'revision_no' => $new_revision,
            'quote_date' => date('Y-m-d', strtotime($this->input->post('quote_date'))),
            'remarks' => $this->input->post('remarks'),
            'status' => 'pending',
            'is_latest' => 1,
            'created_by' => $this->user_id
        ];

        $quotation_id = $this->Quotation_model->save_quotation($quotation_data);

        if ($quotation_id) {
            // Save quotation items with GST
            $item_prices = $this->input->post('item_prices');
            $item_gst = $this->input->post('item_gst');

            $items_data = [];
            foreach ($item_prices as $rfq_item_id => $unit_price) {
                $items_data[$rfq_item_id] = [
                    'unit_price' => $unit_price,
                    'gst_percentage' => $item_gst[$rfq_item_id] ?? 0
                ];
            }

            if (!empty($items_data)) {
                $this->Quotation_model->save_quotation_items($quotation_id, $rfq_id, $items_data);
            }

            $this->session->set_flashdata('success', 'Quotation submitted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to submit quotation. Please try again.');
        }

        redirect('RFQController/show_rfq/' . $rfq_id);
    }

    // -------------------------------------------------------------
    // GET QUOTATION COMPARISON (AJAX)
    // -------------------------------------------------------------
    public function getQuotationComparison()
    {
        $rfq_id = $this->input->post('rfq_id');
        $quotation_ids = $this->input->post('quotation_ids');

        $data['rfq'] = $this->Quotation_model->get_rfq_details($rfq_id);
        $data['items'] = $this->Quotation_model->get_rfq_items($rfq_id);
        $data['quotations'] = $this->Quotation_model->get_quotations_for_comparison($quotation_ids);
        $data['ranks'] = ['L1', 'L2', 'L3', 'L4', 'L5'];

        $this->load->view('rfq/comparison_table', $data);
    }

    // -------------------------------------------------------------
    // GET ALL QUOTATIONS COMPARISON (AJAX)
    // -------------------------------------------------------------
    public function getAllQuotationComparison()
    {
        $rfq_id = $this->input->post('rfq_id');

        $data['rfq'] = $this->Quotation_model->get_rfq_details($rfq_id);
        $data['items'] = $this->Quotation_model->get_rfq_items($rfq_id);
        $data['quotations'] = $this->Quotation_model->get_quotations_by_rfq($rfq_id);
        $data['ranks'] = ['L1', 'L2', 'L3', 'L4', 'L5'];

        $this->load->view('rfq/comparison_table', $data);
    }

    // -------------------------------------------------------------
    // GET REVISION HISTORY (AJAX)
    // -------------------------------------------------------------
    public function getRevisionHistory()
    {
        $rfq_id = $this->input->post('rfq_id');
        $supplier_id = $this->input->post('supplier_id');

        $data['rfq'] = $this->Quotation_model->get_rfq_details($rfq_id);
        $data['items'] = $this->Quotation_model->get_rfq_items($rfq_id);
        $data['history'] = $this->Quotation_model->get_quotation_history($rfq_id, $supplier_id);

        $this->load->view('rfq/revision_history_modal', $data);
    }

    // -------------------------------------------------------------
    // DELETE QUOTATION
    // -------------------------------------------------------------
    public function deleteQuotation($quotation_id, $rfq_id)
    {
        if ($this->Quotation_model->delete_quotation($quotation_id)) {
            $this->session->set_flashdata('success', 'Quotation deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete quotation.');
        }

        redirect('RFQController/show_rfq/' . $rfq_id);
    }

    // -------------------------------------------------------------
    // VIEW QUOTATION DETAILS
    // -------------------------------------------------------------
    public function viewQuotation($quotation_id)
    {
        $this->db->select('vq.*, s.supplier_name, r.rfq_no');
        $this->db->from('vendor_quotations vq');
        $this->db->join('supplier s', 's.supplier_id = vq.supplier_id', 'left');
        $this->db->join('rfq r', 'r.rfq_id = vq.rfq_id', 'left');
        $this->db->where('vq.quotation_id', $quotation_id);
        $data['quotation'] = $this->db->get()->row_array();

        $data['quotation_items'] = $this->Quotation_model->get_quotation_items($quotation_id);

        $session_data = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data);
        $this->load->view('rfq/quotation_details', $data);
    }

    // -------------------------------------------------------------
    // VIEW RFQ DETAILS (with error handling for failed PO conversion)
    // -------------------------------------------------------------
    public function view_rfq_details($rfq_id)
    {
        // Load the same data as show_rfq method
        $data['rfq']        = $this->Quotation_model->get_rfq_details($rfq_id);
        $data['items']      = $this->Quotation_model->get_rfq_items($rfq_id);
        $data['quotations'] = $this->Quotation_model->get_quotations_by_rfq($rfq_id);
        $data['suppliers']  = $this->Quotation_model->get_suppliers($rfq_id);

        $session_data = $this->session->userdata('session_data_head');

        $this->load->view('admin/header_side_bar', $session_data);
        $this->load->view('rfq/show_rfq', $data);
    }

    // -------------------------------------------------------------
    // SEND RFQ EMAILS TO VENDORS
    // -------------------------------------------------------------
    public function send_rfq_emails($rfq_id)
    {
        // Check if RFQ exists
        $rfq = $this->Quotation_model->get_rfq_details($rfq_id);
        if (!$rfq) {
            $this->session->set_flashdata('error', 'RFQ not found');
            redirect('RFQController/index');
        }

        // Get suppliers for this RFQ
        $suppliers = $this->Quotation_model->get_suppliers($rfq_id);
        if (empty($suppliers)) {
            $suppliers = $this->rfq->get_rfq_suppliers($rfq_id); // Try alternative method
        }

        if (empty($suppliers)) {
            $this->session->set_flashdata('error', 'No suppliers found for this RFQ');
            redirect('RFQController/show_rfq/' . $rfq_id);
        }

        // Extract supplier IDs
        $supplier_ids = [];
        foreach ($suppliers as $supplier) {
            $supplier_ids[] = is_array($supplier) ? $supplier['supplier_id'] : $supplier->supplier_id;
        }

        // Load email helper
        $this->load->helper('email');

        // Send emails
        $email_result = send_rfq_to_vendors($rfq_id, $supplier_ids);

        if ($email_result['sent_count'] > 0) {
            // Update RFQ email status
            $this->rfq->update_rfq_email_status($rfq_id, 1);

            $this->session->set_flashdata('success', "Emails sent successfully to " . $email_result['sent_count'] . " vendor(s)");

            // If some failed
            if ($email_result['failed_count'] > 0) {
                $this->session->set_flashdata('warning', $email_result['failed_count'] . " vendor(s) could not be reached");
            }
        } else {
            $this->session->set_flashdata('error', "Failed to send emails to vendors. Please check email configuration.");
        }

        redirect('RFQController/show_rfq/' . $rfq_id);
    }

}
