<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class BankdetailController extends MY_Controller {

    protected $user_id;
    function __construct() {
        parent::__construct();

        
        $this->load->library('session');
        $this->load->model('bankdetail', '', TRUE);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'] ?? null;
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

 
    public function bankdetail_index() {
       
        $data['bankdetail_result'] = $this->bankdetail->get_bankdetail($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bank_detail/add_bankdetail', $data);
    }
    
    public function add_bank_detail() {
        
        $bank_name = $this->input->post('bank_name');
        $account_number = $this->input->post('account_number');
        $account_type = $this->input->post('account_type');
        $bank_address = $this->input->post('bank_address');
        $ifsc_code = $this->input->post('ifsc_code');
        $state = $this->input->post('state');
        $status = $this->input->post('status');
        $comment = $this->input->post('comment');
        $initial_balance = $this->input->post('initial_balance');
        $minimum_allowed_balance = $this->input->post('minimum_allowed_balance');
        $minimum_desired_balance = $this->input->post('minimum_desired_balance');
        $account_owner_name = $this->input->post('account_owner_name');
        $account_owner_address = $this->input->post('account_owner_address');
        $data_bankdetail =
                array(
                    'bank_name' => $bank_name, 
                    'account_number' => $account_number,
                    'account_type' => $account_type,
                    'bank_address' => $bank_address,
                    'ifsc_code' => $ifsc_code,
                    'state' => $state,
                    'status' => $status,
                    'comment' => $comment,
                    'initial_balance' => $initial_balance,
                    'minimum_allowed_balance' => $minimum_allowed_balance,
                    'minimum_desired_balance' => $minimum_desired_balance,
                    'account_owner_name' => $account_owner_name,
                    'account_owner_address' => $account_owner_address,
                    'uid' => $this->user_id
                );
        $result = $this->bankdetail->bankdetail_check($bank_name, $this->user_id); 
       // print_r($result);die();
        if ($result == FALSE) {
            $this->bankdetail->add_bank_detail($data_bankdetail);
            $this->session->set_flashdata('SUCCESSMSG', "Bank Details added successfully!!");
            redirect('BankdetailController/bankdetail_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Bank Details already exist!!");
            redirect('BankdetailController/bankdetail_index');
        }
    }
    
    
      public function delete_bank_detail_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->bankdetail->delete_bank_detail_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Bank Detail deleted successfully!!");
            redirect('BankdetailController/bankdetail_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Bank Detail not deleted successfully!!");
            redirect('BankdetailController/bankdetail_index');
        }
    }
    
    
     public function get_bank_detail() {
       $bankdetail_result = $this->bankdetail->get_bankdetail($this->user_id);
       echo json_encode($bankdetail_result);
    }

      public function get_bank_detail_id() {
        $id = $this->uri->segment(3);
        $data['bank_detail_by_id'] = $this->bankdetail->get_bank_detail_id($id);
        //print_r( $data['bank_detail_by_id'] );die();
        $data['account_type_catgory'] = $this->bankdetail->get_account_type_catgory($this->user_id);
      // print_r( $data['account_type_catgory'] );die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bank_detail/edit_bankdetail', $data);
    }
    
  
    
      public function edit_bank_detail() {
        $bank_id = $this->input->post('bank_id');
       $bank_name = $this->input->post('bank_name');
        $account_number = $this->input->post('account_number');
        $account_type = $this->input->post('account_type');
        $bank_address = $this->input->post('bank_address');
        $ifsc_code = $this->input->post('ifsc_code');
        $state = $this->input->post('state');
        $status = $this->input->post('status');
        $comment = $this->input->post('comment');
        $initial_balance = $this->input->post('initial_balance');
        $minimum_allowed_balance = $this->input->post('minimum_allowed_balance');
        $minimum_desired_balance = $this->input->post('minimum_desired_balance');
        $account_owner_name = $this->input->post('account_owner_name');
        $account_owner_address = $this->input->post('account_owner_address');
        $data_bank = array('bank_name' => $bank_name, 
                    'account_number' => $account_number,
                    'account_type' => $account_type,
                    'bank_address' => $bank_address,
                    'ifsc_code' => $ifsc_code,
                    'state' => $state,
                    'status' => $status,
                    'comment' => $comment,
                    'initial_balance' => $initial_balance,
                    'minimum_allowed_balance' => $minimum_allowed_balance,
                    'minimum_desired_balance' => $minimum_desired_balance,
                    'account_owner_name' => $account_owner_name,
                    'account_owner_address' => $account_owner_address
                    );

        $result = $this->bankdetail->edit_bank_detail($data_bank, $bank_id, $this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Bank Detail updated successfully!!");
            redirect('BankdetailController/bankdetail_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Bank Detail not updated successfully!!");
            redirect('BankdetailController/bankdetail_index');
        }
    }
    public function bank_transcation_index(){

        $data['bankdetail_result'] = $this->bankdetail->get_bankdetail($this->user_id);
      
       $data['banktransaction_result'] = $this->bankdetail->get_banktransaction($this->user_id);
   




        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bank_detail/add_banktransaction' ,$data);
       
    }
    public function add_banktransaction_detail(){
        
        
        $transaction_detail = $this->input->post('transaction_detail');
        $withdrawal_amount = $this->input->post('withdrawal_amount');
        $deposite_amount = $this->input->post('deposite_amount');
        $balance_amount = $this->input->post('balance_amount');
        $description = $this->input->post('description');
        $bank_transaction_name = $this->input->post('bank_transaction_name');
        $transaction_date = date("Y-m-d", strtotime($this->input->post('transaction_date')));
        
        $data_banktransaction =
                array(
                    'transaction_detail' => $transaction_detail, 
                    'withdrawal_amount' => $withdrawal_amount,
                    'deposite_amount' => $deposite_amount,
                    'balance_amount' => $balance_amount,
                    'description' => $description,
                    'bank_transaction_name' => $bank_transaction_name,
                    'transaction_date' => $transaction_date,
                    'uid' => $this->user_id
                );
        $result = $this->bankdetail->banktransaction_check($transaction_detail, $this->user_id); 
        //print_r($result);die();
        if ($result == FALSE) {
            $this->bankdetail->add_bank_transaction($data_banktransaction);
            $this->session->set_flashdata('SUCCESSMSG', "Bank Details added successfully!!");
            redirect('BankdetailController/bank_transcation_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Bank Details already exist!!");
            redirect('BankdetailController/bank_transcation_index');
        }  
    }
    
      public function get_bank_transaction_id() {
        $id = $this->uri->segment(3);
        $data['bankdetail_result'] = $this->bankdetail->get_bankdetail($this->user_id);
        $data['bank_transaction_by_id'] = $this->bankdetail->get_bank_transaction_id($id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bank_detail/edit_banktransaction', $data);
    }
    
     public function delete_bank_transaction_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->bankdetail->delete_bank_transaction_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Bank Detail deleted successfully!!");
            redirect('BankdetailController/bank_transcation_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Bank Detail not deleted successfully!!");
            redirect('BankdetailController/bank_transcation_index');
        }
    }
    
     public function edit_bank_transaction() {
        $bank_transaction_id = $this->input->post('bank_transaction_id');
        $transaction_detail = $this->input->post('transaction_detail');
        $withdrawal_amount = $this->input->post('withdrawal_amount');
        $deposite_amount = $this->input->post('deposite_amount');
        $balance_amount = $this->input->post('balance_amount');
        $description = $this->input->post('description');
        $bank_transaction_name = $this->input->post('bank_transaction_name');
        $transaction_date = date("Y-m-d", strtotime($this->input->post('transaction_date')));
        $data_banktransaction = array('transaction_detail' => $transaction_detail, 
                    'withdrawal_amount' => $withdrawal_amount,
                    'deposite_amount' => $deposite_amount,
                    'balance_amount' => $balance_amount,
                    'description' => $description,
                    'bank_transaction_name' => $bank_transaction_name,
                    'transaction_date' => $transaction_date
        );

        $result = $this->bankdetail->edit_bank_transaction($data_banktransaction, $bank_transaction_id, $this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Bank Detail updated successfully!!");
            redirect('BankdetailController/bank_transcation_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Bank Detail not updated successfully!!");
            redirect('BankdetailController/bank_transcation_index');
        }
    }
    
    
    
    
    
    
    
    public function loan_account_index(){
        $data['loan_result'] = $this->bankdetail->get_loan($this->user_id);
//        $data['banktransaction_result'] = $this->bankdetail->get_banktransaction($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bank_detail/add_loan_account', $data);
       
    }
    public function add_loan(){
        
        
        $acc_name = $this->input->post('acc_name');
        $acc_number = $this->input->post('acc_number');
        $bank = $this->input->post('bank');
        $loan_description = $this->input->post('loan_description');
        $loan_recevied = $this->input->post('loan_recevied');
        $current_balance = $this->input->post('current_balance');
        $interest_rate = $this->input->post('interest_rate');
        $duration = $this->input->post('duration');
        $processing_fee = $this->input->post('processing_fee');
        $liabilities = $this->input->post('liabilities');
        $sub_liabilities = $this->input->post('sub_liabilities');
        $processing_fee_paid_from = $this->input->post('processing_fee_paid_from');
        $loan_date = date("Y-m-d", strtotime($this->input->post('loan_date')));
        
        $data_loan =
                array(
                    'acc_name' => $acc_name, 
                    'acc_number' => $acc_number,
                    'bank' => $bank,
                    'loan_description' => $loan_description,
                    'loan_recevied' => $loan_recevied,
                    'current_balance' => $current_balance,
                    'interest_rate' => $interest_rate,
                    'duration' => $duration,
                    'processing_fee' => $processing_fee,
                    'liabilities' => $liabilities,
                    'sub_liabilities' => $sub_liabilities,
                    'processing_fee_paid_from' => $processing_fee_paid_from,
                    'loan_date' => $loan_date,
                    'uid' => $this->user_id
                );
        $result = $this->bankdetail->loan_check($acc_name, $this->user_id); 
        //print_r($result);die();
        if ($result == FALSE) {
            $this->bankdetail->add_loan($data_loan);
            $this->session->set_flashdata('SUCCESSMSG', "Loan Details added successfully!!");
            redirect('BankdetailController/loan_account_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Loan Details already exist!!");
            redirect('BankdetailController/loan_account_index');
        }  
    }
    
//      public function get_loan_id() {
//        $id = $this->uri->segment(3);
//       // $data['loan_result'] = $this->bankdetail->get_loan($this->user_id);
//        $data['bank_loan_by_id'] = $this->bankdetail->get_loan_id($id);
//        $session_data_head = $this->session->userdata('session_data_head');
//        $this->load->view('admin/header_side_bar', $session_data_head);
//        $this->load->view('bank_detail/edit_loan_account', $data);
//    }
    
    
    public function get_loan_id() {
        $id = $this->uri->segment(3);
        //$data['bankdetail_result'] = $this->bankdetail->get_bankdetail($this->user_id);
        $data['bank_loan_by_id'] = $this->bankdetail->get_loan_id($id);
        //print_r($data['bank_loan_by_id']);die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('bank_detail/edit_loan_account', $data);
    }
    
     public function delete_loan_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->bankdetail->delete_loan_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Loan Detail deleted successfully!!");
            redirect('BankdetailController/loan_account_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Loan Detail not deleted successfully!!");
            redirect('BankdetailController/loan_account_index');
        }
    }
    
     public function edit_loan() {
         $loan_id = $this->input->post('loan_id');
         $acc_name = $this->input->post('acc_name');
        $acc_number = $this->input->post('acc_number');
        $bank = $this->input->post('bank');
        $loan_description = $this->input->post('loan_description');
        $loan_recevied = $this->input->post('loan_recevied');
        $current_balance = $this->input->post('current_balance');
        $interest_rate = $this->input->post('interest_rate');
        $duration = $this->input->post('duration');
        $processing_fee = $this->input->post('processing_fee');
        $liabilities = $this->input->post('liabilities');
        $sub_liabilities = $this->input->post('sub_liabilities');
        $processing_fee_paid_from = $this->input->post('processing_fee_paid_from');
        $loan_date = date("Y-m-d", strtotime($this->input->post('loan_date')));
         
        $data_loan =
                array(
                    'acc_name' => $acc_name, 
                    'acc_number' => $acc_number,
                    'bank' => $bank,
                    'loan_description' => $loan_description,
                    'loan_recevied' => $loan_recevied,
                    'current_balance' => $current_balance,
                    'interest_rate' => $interest_rate,
                    'duration' => $duration,
                    'processing_fee' => $processing_fee,
                     'liabilities' => $liabilities,
                    'sub_liabilities' => $sub_liabilities,
                    'processing_fee_paid_from' => $processing_fee_paid_from,
                    'loan_date' => $loan_date
                    
                ); 
                //print_r($data_loan);die();
        $result = $this->bankdetail->edit_loan($data_loan, $loan_id, $this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Bank Detail updated successfully!!");
            redirect('BankdetailController/loan_account_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Bank Detail not updated successfully!!");
            redirect('BankdetailController/loan_account_index');
        }
    }
    
    
    
    
 
}
