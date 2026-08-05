<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();


        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('payment', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->library('form_validation');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function view_payment() {

        $data['gst_invoice_payment_history'] = $this->payment->get_payment_history_details($this->user_id);
        //  $data['non_gst_invoice_payment_history'] = $this->payment->get_non_gst_payment_history_details($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('payment_history/view_payment_history', $data);
    }

    public function delete_gst_class_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->gst->delete_gst_class_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "GST Class deleted successfully!!");
            redirect('GstController/gst_index');
        } else {
            $this->session->set_flashdata('INFOMSG', "GST Class not deleted successfully!!");
            redirect('GstController/gst_index');
        }
    }

    public function get_payment_by_id() {
        $id = $this->uri->segment(3);
        $data['payment'] = $this->payment->get_payment_by_id($id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('payment_history/edit_payment', $data);
    }

    //edit_payment_history

    public function edit_payment_history() {

        $invocie_pay_id = $this->input->post('invocie_pay_id');
        $invoice_number_fk = $this->input->post('invoice_number_fk');
        $invocie_pay_amount = $this->input->post('invocie_pay_amount');
        $payment_type = $this->input->post('payment_type');
        $invoice_pay_remark = $this->input->post('invoice_pay_remark');
        $invoice_pay_date = $this->input->post('invoice_pay_date');

        $data_payment = array('invocie_pay_amount' => $invocie_pay_amount,
            'payment_type' => $payment_type,
            'invoice_pay_remark' => $invoice_pay_remark,
            'invoice_pay_date' => $invoice_pay_date);
        $result = $this->payment->edit_payment_history($data_payment, $invocie_pay_id);

        $total = $this->invoice->get_previous_balance_invoice($invoice_number_fk, $this->user_id);
        $paid_amount = $this->payment->get_paid_amount_sum($invoice_number_fk, $this->user_id);

        $total_invoice_amount = $total['total'] - $paid_amount['total_balance_amount'];
        $data_invoice_balance = array('balance' => $total_invoice_amount);

        if ($result == TRUE) {
            $this->payment->edit_invoice_balance_amount($data_invoice_balance, $invoice_number_fk, $this->user_id);
            $this->session->set_flashdata('SUCCESSMSG', "Payment updated successfully!!");
            redirect('PaymentController/view_payment');
        } else {
            $this->session->set_flashdata('INFOMSG', "Payment not updated successfully!!");
            redirect('PaymentController/view_payment');
        }
    }

    public function get_current_balance_details() {
        $invoice_number_fk = $this->input->post('invoice_number_fk');
        $result = $this->payment->get_current_balance_details($invoice_number_fk, $this->user_id);
        echo json_encode($result);
    }

    public function ledger_report() {
        $data['result'] = $this->supplier->get_supplier($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['item_name'] = $this->invoice->get_item_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('payment_history/ledger_report', $data);
    }

    public function get_gst_ledger() {
        $from_date1 = $this->input->post('from_date');
        $to_date1 = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');
        $from_date = date('Y-m-d', strtotime($from_date1));
        $to_date = date('Y-m-d', strtotime($to_date1));

        $data['from_date'] = $from_date1;
        $data['to_date'] = $to_date1;
        $data['company_id'] = $company_name;

        $data['company_name'] = '';
        $data['address'] = '';
        $data['gst'] = '';

        $invoice = $this->payment->get_gst_ledger($from_date, $to_date, $company_name);


        $payments = $this->payment->get_payment_ledger($from_date, $to_date, $company_name);

        $payment_in = $this->payment->get_purchase_gst_ledger_payment_in($from_date, $to_date, $company_name);
        $opening_balance = $this->payment->get_customer_opening_balance($company_name, $from_date, $this->user_id);


        $ledger_array1 = array();
        $ledger_array2 = array();
        $ledger_array3 = array();
        $ledger_array4 = array();

        if ($opening_balance && (double)$opening_balance->opening_balance_amount != 0) {
            $ledger_array4[] = array(
                "invoice_date" => $from_date,
                "total" => $opening_balance->opening_balance_amount,
                "invocie_pay_amount" => '',
                "invoice_number" => $opening_balance->balance_id,
                "invoice_no" => '',
                'type' => 'Opening Balance',
                'particulars' => 'Dr Opening Balance',
                'is_opening_balance' => true
            );
        }

        //     Declare two dates 
        $Date1 = $from_date1;
        $Date2 = $to_date1;

// Declare an empty array 
        $array = array();

// Use strtotime function 
        $Variable1 = strtotime($Date1);
        $Variable2 = strtotime($Date2);

// Use for loop to store dates into array 
// 86400 sec = 24 hrs = 60*60*24 = 1 day 
        for ($currentDate = $Variable1; $currentDate <= $Variable2; $currentDate += (86400)) {

            $Store = date('Y-m-d', $currentDate);
            $array[] = $Store;
        }

        foreach ($array as $date) {

            // $current_date1 = $date->format("d-m-Y");
            $timestamp = strtotime($date);

            // Creating new date format from that timestamp
            $current_date1 = date("d-m-Y", $timestamp);
            $i = 1;

            foreach ($invoice as $key) {

               
                if (date('d-m-Y', strtotime($key->invoice_date)) == $current_date1) {
              
                    $address = $key->address;
                    $data['company_name'] = $key->company_name;
                    $data['address'] = $key->address;
                    $data['gst'] = $key->gst;

                    // echo $key->company_name;
                    // die();

                    $ledger_array1[] = array("invoice_date" => date('d-m-Y', strtotime($key->invoice_date)), "invoice_number" => $key->invoice_number, "total" => $key->total, "invocie_pay_amount" => '', "company_name" => $key->company_name, "address" =>$key->address, "balance" => $key->balance,
                    'type' => 'Sales', 'particulars' => 'Cr Sales'
                );
                }

                $i++;
            }
            foreach ($payments as $key1) {

                if ($key1->invoice_pay_date == $current_date1) {

                    $ledger_array2[] = array("invoice_date" => $key1->invoice_pay_date, "total" => '', "invocie_pay_amount" => $key1->invocie_pay_amount, "invoice_number" => '', "company_name" => '', "address" => '');
                }
            }

            foreach ($payment_in as $key1) {

                if (date('d-m-Y', strtotime($key1->payment_date)) == $current_date1) {
                    $voucher_type = !empty($key1->bank_voucher_type) ? $key1->bank_voucher_type : 'Receipt';
                    $particular_prefix = (strtolower($voucher_type) == 'payment') ? 'Dr ' : 'Cr ';
                    $ledger_array3[] = array("invoice_date" => $key1->payment_date, "total" => '', "invocie_pay_amount" => $key1->payment, "invoice_number" => $key1->payment_id, "invoice_no" => '', 'type' => $voucher_type, 'particulars' => $particular_prefix . $key1->payment_bank );
                }
            }
        }





        $data['ledger'] = array_merge($ledger_array4, $ledger_array1, $ledger_array2, $ledger_array3);

        // var_dump( $data['ledger']);

        // die();
        $session_data_head = $this->session->userdata('session_data_head');

        if ($this->input->post('download_pdf')) {
            $data['is_pdf'] = true;
            $html = $this->load->view('payment_history/ledger_report_view', $data, true);

            require_once APPPATH . '../vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf(array(
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'L'
            ));
            $mpdf->WriteHTML($html);
            $mpdf->Output('Sales_Ledger_' . date('Ymd_His') . '.pdf', 'D');
            return;
        }

        $this->load->view('payment_history/ledger_report_view', $data);
    }

    public function get_non_gst_ledger() {
        $from_date1 = $this->input->post('from_date');
        $to_date1 = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');
        $from_date = date('Y-m-d', strtotime($from_date1));
        $to_date = date('Y-m-d', strtotime($to_date1));
        $data['from_date'] = $from_date1;
        $data['to_date'] = $to_date1;

        $invoice = $this->payment->get_non_gst_ledger($from_date, $to_date, $company_name);
        $payments = $this->payment->get_non_gst_payment_ledger($from_date, $to_date, $company_name);
        

        $ledger_array1 = array();
        $ledger_array2 = array();

        $period = new DatePeriod(new DateTime($from_date), new DateInterval('P1D'), new DateTime($to_date + '1'));
        foreach ($period as $date) {

            $current_date1 = $date->format("d-m-Y");
            $i = 1;
            foreach ($invoice as $key) {

               

                if (date('d-m-Y', strtotime($key->invoice_date)) == $current_date1) {
                    $ledger_array1[] = array("invoice_date" => date('d-m-Y', strtotime($key->invoice_date)), "invoice_number" => $key->invoice_number, "total" => $key->total, "invocie_pay_amount" => '', "company_name" => $key->company_name, "balance" => $key->balance);
                }
                $i++;
            }
            foreach ($payments as $key1) {

                if ($key1->ng_invoice_pay_date == $current_date1) {
                    $ledger_array2[] = array("invoice_date" => $key1->ng_invoice_pay_date, "total" => '', "invocie_pay_amount" => $key1->ng_invocie_pay_amount, "invoice_number" => '', "company_name" => '');
                }
            }
        }
        $data['ledger'] = array_merge($ledger_array1, $ledger_array2);

        $this->load->view('payment_history/non_gst_ledger_report_view', $data);
    }

      //Purchase Ledger
    public function get_purchse_ledger() {

    $from_date1 = $this->input->post('from_date');
    $to_date1 = $this->input->post('to_date');
    $supplier_name = $this->input->post('supplier_name');

    $from_date = date('Y-m-d', strtotime($from_date1));
    $to_date = date('Y-m-d', strtotime($to_date1));
    $data['from_date'] = $from_date1;
    $data['to_date'] = $to_date1;

    $data['company_name'] = '';
    $data['address'] = '';
    $data['gst'] = '';

    $supplier_details = $this->supplier->get_supplier_by_id($supplier_name);
    if (!empty($supplier_details)) {
        $data['company_name'] = isset($supplier_details['company_name']) ? $supplier_details['company_name'] : '';
        $data['address'] = isset($supplier_details['address']) ? $supplier_details['address'] : '';
        $data['gst'] = isset($supplier_details['gst']) ? $supplier_details['gst'] : '';
    }

    $invoice = $this->payment->get_purchse_bill_ledger($from_date, $to_date, $supplier_name);
    $payments = $this->payment->get_purchse_bill_payment_history($from_date, $to_date, $supplier_name);
    $payment_out = $this->payment->get_purchase_gst_ledger_payment_out($from_date, $to_date, $supplier_name);
    $opening_balance = $this->payment->get_supplier_opening_balance($supplier_name, $from_date, $this->user_id);

    $ledger_array = array(); // Single array for all entries

    // Add opening balance if exists
    if ($opening_balance && (double)$opening_balance->opening_balance_amount != 0) {
        $ledger_array[] = array(
            "invoice_date" => $from_date, // Keep as Y-m-d for sorting
            "display_date" => date('d-m-Y', strtotime($from_date)), // For display
            "total" => $opening_balance->opening_balance_amount,
            "invocie_pay_amount" => '',
            "invoice_number" => $opening_balance->balance_id,
            "invoice_no" => '',
            'type' => 'Opening Balance',
            'particulars' => 'Dr Opening Balance',
            'is_opening_balance' => true,
            'sort_date' => $from_date // For sorting
        );
    }

    // Add invoice entries (Purchase bills)
    foreach ($invoice as $key) {
        $data['company_name'] = $key->company_name;
        $data['address'] = $key->address;
        $data['gst'] = $key->gst;

        $invoice_date = $key->date;
        $ledger_array[] = array(
            "invoice_date" => $invoice_date,
            "display_date" => date('d-m-Y', strtotime($invoice_date)),
            "invoice_number" => $key->number,
            "invoice_no" => $key->invoice_no,
            "total" => $key->total,
            "invocie_pay_amount" => '',
            'type' => 'Prch',
            'particulars' => 'Dr Purchase',
            'sort_date' => $invoice_date
        );
    }

    // Add payment entries (from purchase bill payment history)
    foreach ($payments as $key1) {
        $payment_date = $key1->purchase_pay_date;
        $ledger_array[] = array(
            "invoice_date" => $payment_date,
            "display_date" => date('d-m-Y', strtotime($payment_date)),
            "total" => '',
            "invocie_pay_amount" => $key1->purchase_pay_amount,
            "invoice_number" => '',
            "invoice_no" => '',
            'type' => 'Payment',
            'particulars' => 'Cr Payment',
            'sort_date' => $payment_date
        );
    }

    // Add GST/payment out entries
    foreach ($payment_out as $key1) {
        $payment_date = $key1->payment_date;
        $voucher_type = !empty($key1->bank_voucher_type) ? $key1->bank_voucher_type : 'Payment';
        $particular_prefix = (strtolower($voucher_type) == 'payment') ? 'Dr ' : 'Cr ';
        
        $ledger_array[] = array(
            "invoice_date" => $payment_date,
            "display_date" => date('d-m-Y', strtotime($payment_date)),
            "total" => '',
            "invocie_pay_amount" => $key1->payment,
            "invoice_number" => $key1->payment_id,
            "invoice_no" => '',
            'type' => $voucher_type,
            'particulars' => $particular_prefix . $key1->payment_bank,
            'sort_date' => $payment_date
        );
    }

    // Sort the ledger array by date (invoice_date) in ascending order
    usort($ledger_array, function($a, $b) {
        $date_a = strtotime($a['sort_date']);
        $date_b = strtotime($b['sort_date']);
        
        if ($date_a == $date_b) {
            // If same date, maintain order: Opening Balance first, then Purchases, then Payments
            $priority = [
                'Opening Balance' => 0,
                'Prch' => 1,
                'Payment' => 2,
                'Receipt' => 2
            ];
            
            $priority_a = $priority[$a['type']] ?? 3;
            $priority_b = $priority[$b['type']] ?? 3;
            
            return $priority_a - $priority_b;
        }
        
        return $date_a - $date_b;
    });

    $data['ledger'] = $ledger_array;

    $this->load->view('payment_history/purchase_ledger_report', $data);
}

}
