<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BalanceController extends MY_Controller {

    protected $user_id;
    
    function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->model('Balance', '', TRUE);
        $this->load->model('Customer', '', TRUE);
        $this->load->model('Supplier', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');

        if (!$session_data_head || !isset($session_data_head['result']['user_id'])) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }

        $this->user_id = $session_data_head['result']['user_id'];
    }

    /**
     * View all opening balances
     */
    public function opening_balance_index() {
        $session_data_head = $this->session->userdata('session_data_head');
        if (!$session_data_head) {
            redirect('LoginController/logout');
            return;
        }
        
        $opening_balances = $this->Balance->get_all_opening_balances($this->user_id);
        $data['opening_balances'] = $opening_balances;
        $data['total_balance'] = 0;
        
        foreach ($opening_balances as $balance) {
            $data['total_balance'] += floatval($balance->opening_balance_amount);
        }
        
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('balance/view_opening_balance', $data);
    }

    /**
     * Show create form for opening balance
     */
    public function create_opening_balance() {
        $session_data_head = $this->session->userdata('session_data_head');
        
        if (!$session_data_head) {
            redirect('LoginController/logout');
            return;
        }
        
        $data['accounts'] = $this->Balance->get_all_accounts($this->user_id);
        
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('balance/create_opening_balance', $data);
    }

    /**
     * Store new opening balance
     */
    public function store_opening_balance() {
        $this->form_validation->set_rules('customer_id', 'Account/Customer', 'required');
        $this->form_validation->set_rules('opening_balance_amount', 'Opening Balance Amount', 'required|numeric');
        $this->form_validation->set_rules('balance_date', 'Balance Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('ERRORMSG', validation_errors());
            redirect('BalanceController/create_opening_balance');
            return;
        }

        $account = $this->resolve_opening_balance_account($this->input->post('customer_id'));
        if (!$account) {
            $this->session->set_flashdata('ERRORMSG', "Invalid account selected!");
            redirect('BalanceController/create_opening_balance');
            return;
        }

        $account_name = $this->input->post('account_name') ?: $account['account_name'];

        // Check if opening balance already exists for this account
        $existing = $this->Balance->check_existing_opening_balance($account_name, $this->user_id);
        if ($existing) {
            $this->session->set_flashdata('ERRORMSG', "Opening balance already exists for this account! Please edit the existing one.");
            redirect('BalanceController/create_opening_balance');
            return;
        }

        $data = array(
            'account_name' => $account_name,
            'opening_balance_amount' => floatval($this->input->post('opening_balance_amount')),
            'balance_date' => date('Y-m-d', strtotime($this->input->post('balance_date'))),
            'description' => $this->input->post('description'),
            'uid' => $this->user_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($this->Balance->create_opening_balance($data)) {
            $this->session->set_flashdata('SUCCESSMSG', "Opening Balance created successfully!!");
            redirect('BalanceController/opening_balance_index');
        } else {
            $this->session->set_flashdata('ERRORMSG', "Failed to create Opening Balance. Please try again!!");
            redirect('BalanceController/create_opening_balance');
        }
    }

    /**
     * Show edit form for opening balance
     */
    public function edit_opening_balance($id = null) {
        if ($id === null) {
            redirect('BalanceController/opening_balance_index');
            return;
        }

        $session_data_head = $this->session->userdata('session_data_head');
        
        if (!$session_data_head) {
            redirect('LoginController/logout');
            return;
        }

        $data['opening_balance'] = $this->Balance->get_opening_balance_by_id($id, $this->user_id);
        
        if (!$data['opening_balance']) {
            $this->session->set_flashdata('ERRORMSG', "Opening Balance not found!");
            redirect('BalanceController/opening_balance_index');
            return;
        }

        $data['accounts'] = $this->Balance->get_all_accounts($this->user_id);

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('balance/edit_opening_balance', $data);
    }

    /**
     * Update opening balance
     */
    public function update_opening_balance($id = null) {
        if ($id === null) {
            redirect('BalanceController/opening_balance_index');
            return;
        }

        $this->form_validation->set_rules('customer_id', 'Account/Customer', 'required');
        $this->form_validation->set_rules('opening_balance_amount', 'Opening Balance Amount', 'required|numeric');
        $this->form_validation->set_rules('balance_date', 'Balance Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('ERRORMSG', validation_errors());
            redirect('BalanceController/edit_opening_balance/' . $id);
            return;
        }

        $account = $this->resolve_opening_balance_account($this->input->post('customer_id'));
        if (!$account) {
            $this->session->set_flashdata('ERRORMSG', "Invalid account selected!");
            redirect('BalanceController/edit_opening_balance/' . $id);
            return;
        }

        $account_name = $this->input->post('account_name') ?: $account['account_name'];
        
        $data = array(
            'account_name' => $account_name,
            'opening_balance_amount' => floatval($this->input->post('opening_balance_amount')),
            'balance_date' => date('Y-m-d', strtotime($this->input->post('balance_date'))),
            'description' => $this->input->post('description'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($this->Balance->update_opening_balance($id, $this->user_id, $data)) {
            $this->session->set_flashdata('SUCCESSMSG', "Opening Balance updated successfully!!");
            redirect('BalanceController/opening_balance_index');
        } else {
            $this->session->set_flashdata('ERRORMSG', "Failed to update Opening Balance!!");
            redirect('BalanceController/edit_opening_balance/' . $id);
        }
    }

    /**
     * Delete opening balance
     */
    public function delete_opening_balance($id = null) {
        if ($id === null) {
            redirect('BalanceController/opening_balance_index');
            return;
        }

        if ($this->Balance->delete_opening_balance($id, $this->user_id)) {
            $this->session->set_flashdata('SUCCESSMSG', "Opening Balance deleted successfully!!");
        } else {
            $this->session->set_flashdata('ERRORMSG', "Failed to delete Opening Balance!!");
        }
        
        redirect('BalanceController/opening_balance_index');
    }

    /**
     * Get opening balance by customer ID (AJAX)
     */
    public function get_balance_by_customer_ajax($customer_id = null) {
        if ($customer_id === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid customer ID']);
            return;
        }

        $balance = $this->Balance->get_opening_balance_by_customer($customer_id, $this->user_id);
        
        if ($balance) {
            echo json_encode(['success' => true, 'data' => $balance]);
        } else {
            echo json_encode(['success' => false, 'data' => null, 'message' => 'No opening balance found for this customer']);
        }
    }

    /**
     * Export opening balances to CSV
     */
    public function export_opening_balances() {
        $balances = $this->Balance->get_all_opening_balances($this->user_id);
        
        $filename = 'opening_balances_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add CSV headers
        fputcsv($output, array('Customer Name', 'Account Name', 'Opening Balance Amount', 'Balance Date', 'Description', 'Created At'));
        
        // Add data rows
        foreach ($balances as $balance) {
            fputcsv($output, array(
                $balance->company_name,
                $balance->account_name,
                $balance->opening_balance_amount,
                date('d-m-Y', strtotime($balance->balance_date)),
                $balance->description,
                date('d-m-Y H:i:s', strtotime($balance->created_at))
            ));
        }
        
        fclose($output);
    }

    private function resolve_opening_balance_account($account_key) {
        $parts = explode(':', (string)$account_key, 2);
        if (count($parts) !== 2) {
            return null;
        }

        $account_type = $parts[0];
        $account_id = (int)$parts[1];

        if ($account_type === 'customer') {
            $customer = $this->Customer->get_customer_by_id($account_id);
            if (!$customer) {
                return null;
            }

            return array(
                'account_name' => !empty($customer['company_name']) ? $customer['company_name'] : (!empty($customer['fullname']) ? $customer['fullname'] : 'Customer Account')
            );
        }

        if ($account_type === 'supplier') {
            $supplier = $this->Supplier->get_supplier_by_id($account_id);
            if (!$supplier) {
                return null;
            }

            return array(
                'account_name' => !empty($supplier['company_name']) ? $supplier['company_name'] : (!empty($supplier['fullname']) ? $supplier['fullname'] : 'Vendor Account')
            );
        }

        return null;
    }
}
?>
