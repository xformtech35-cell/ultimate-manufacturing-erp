<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BankentryController extends MY_Controller {

    protected $user_id;

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('invoice', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('expense', '', TRUE);
        $this->load->model('gst', '', TRUE);
        $this->load->model('login', '', TRUE);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? null;

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    private function filter_expense_categories_by_mode($categories, $mode) {
        $filtered = [];
        foreach ($categories as $cat) {
            if (stripos($cat['expense_category'], ucfirst($mode)) === 0) {
                $filtered[] = $cat;
            }
        }
        return $filtered;
    }

    private function filter_expense_entries_by_mode($entries, $mode) {
        $filtered = [];
        if (is_array($entries)) {
            foreach ($entries as $entry) {
                if (stripos($entry->expense_category, ucfirst($mode)) === 0) {
                    $filtered[] = $entry;
                }
            }
        }
        return $filtered;
    }

    public function index() {
        // Load Customer Payment Data
        $data['company_name'] = $this->invoice->get_company_name_with_bal($this->user_id);
        $data['result'] = $this->invoice->get_payment_in($this->user_id);
        $data['result_by_id'] = null;

        // Load Vendor Payment Data
        $data['vendor_company_name'] = $this->supplier->get_company_name_with_bal($this->user_id);
        $data['vendor_result'] = $this->supplier->get_payment_out($this->user_id);
        $data['vendor_result_by_id'] = null;

        // Load Direct Expense Data
        $current_month = date('M-y');
        $direct_expenses = $this->inventory->get_monthyearwise_record($current_month, $this->user_id);
        $data['direct_expense_result'] = $this->filter_expense_entries_by_mode($direct_expenses, 'direct');

        // Load Indirect Expense Data
        $indirect_expenses = $this->inventory->get_monthyearwise_record($current_month, $this->user_id);
        $data['indirect_expense_result'] = $this->filter_expense_entries_by_mode($indirect_expenses, 'indirect');

        // Load GST and Expense Categories
        $data['gst_class_result'] = $this->gst->get_gst_classes($this->user_id);
        $data['expense_catgory'] = $this->expense->get_expense_catgory($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bankentry/index', $data);
    }
}


