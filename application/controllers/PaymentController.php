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
        $suppliers = $this->supplier->get_supplier($this->user_id);
        if (is_array($suppliers)) {
            usort($suppliers, function($a, $b) {
                $na = is_object($a) ? ($a->company_name ?? '') : ($a['company_name'] ?? '');
                $nb = is_object($b) ? ($b->company_name ?? '') : ($b['company_name'] ?? '');
                return strcasecmp($na, $nb);
            });
        }
        $data['result'] = $suppliers;
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('payment_history/ledger_report', $data);
    }

    public function get_gst_ledger() {
        $from_date1 = $this->input->post('from_date');
        $to_date1 = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');

        if (empty($company_name)) {
            $this->session->set_flashdata('INFOMSG', 'Please select a customer first.');
            redirect('PaymentController/ledger_report');
            return;
        }

        $from_date = !empty($from_date1) ? date('Y-m-d', strtotime($from_date1)) : date('Y-04-01');
        $to_date   = !empty($to_date1)   ? date('Y-m-d', strtotime($to_date1))   : date('Y-m-d');

        $data['from_date'] = !empty($from_date1) ? $from_date1 : date('01-04-Y');
        $data['to_date']   = !empty($to_date1)   ? $to_date1   : date('d-m-Y');
        $data['company_id'] = $company_name;

        // Fetch customer profile directly so header info is always complete
        $this->load->model('Customer', 'customer');
        $customer_details = $this->customer->get_customer_by_id($company_name);
        $data['company_name'] = isset($customer_details['company_name']) ? $customer_details['company_name'] : '';
        $data['address']      = isset($customer_details['address'])      ? $customer_details['address']      : '';
        $data['gst']          = isset($customer_details['gst'])          ? $customer_details['gst']          : '';

        $invoice         = $this->payment->get_gst_ledger($from_date, $to_date, $company_name);
        $payments        = $this->payment->get_payment_ledger($from_date, $to_date, $company_name);
        $payment_in      = $this->payment->get_purchase_gst_ledger_payment_in($from_date, $to_date, $company_name);
        $opening_balance = $this->payment->get_customer_opening_balance($company_name, $from_date, $this->user_id);

        $ledger_array = array();

        // 1. Opening Balance (Dr Opening Balance for Customer)
        if ($opening_balance && (float)$opening_balance->opening_balance_amount != 0) {
            $ob_amount = (float)$opening_balance->opening_balance_amount;
            if ($ob_amount < 0) {
                log_message('error', "Negative opening balance detected for customer $company_name: $ob_amount. Rejected.");
            } else {
                $ledger_array[] = array(
                    "invoice_date"       => $from_date,
                    "display_date"       => date('d-m-Y', strtotime($from_date)),
                    "total"              => $ob_amount, // Debit
                    "invocie_pay_amount" => '',
                    "invoice_number"     => $opening_balance->balance_id,
                    "invoice_no"         => '',
                    'type'               => 'Opening Balance',
                    'particulars'        => 'Dr Opening Balance',
                    'is_opening_balance' => true,
                    'sort_date'          => $from_date
                );
            }
        }

        // 2. Sales Invoices (Debit)
        if (!empty($invoice) && is_array($invoice)) {
            foreach ($invoice as $key) {
                $inv_total = (float)$key->total;
                if ($inv_total < 0) {
                    log_message('error', "Negative invoice amount detected for invoice {$key->invoice_number}: $inv_total. Skipped.");
                    continue;
                }
                $inv_date = $key->invoice_date;
                $ledger_array[] = array(
                    "invoice_date"       => $inv_date,
                    "display_date"       => date('d-m-Y', strtotime($inv_date)),
                    "invoice_number"     => $key->invoice_number,
                    "invoice_no"         => '',
                    "total"              => $inv_total,
                    "invocie_pay_amount" => '',
                    "company_name"       => $key->company_name,
                    "address"            => $key->address,
                    "balance"            => (float)$key->balance,
                    'type'               => 'Sales',
                    'particulars'        => 'Sales Invoice',
                    'is_opening_balance' => false,
                    'sort_date'          => $inv_date
                );
            }
        }

        // 3. Invoice Payments (Credit)
        if (!empty($payments) && is_array($payments)) {
            foreach ($payments as $key1) {
                $pay_amount = (float)$key1->invocie_pay_amount;
                if ($pay_amount < 0) {
                    log_message('error', "Negative invoice payment amount detected: $pay_amount. Skipped.");
                    continue;
                }
                if ($pay_amount == 0) {
                    continue;
                }
                $p_date = date('Y-m-d', strtotime($key1->invoice_pay_date));
                $bank_info = !empty(trim($key1->bank_name)) ? ' (' . trim($key1->bank_name) . ')' : '';
                $inv_fk = !empty($key1->invoice_number_fk) ? $key1->invoice_number_fk : '';
                $ledger_array[] = array(
                    "invoice_date"       => $p_date,
                    "display_date"       => date('d-m-Y', strtotime($key1->invoice_pay_date)),
                    "total"              => '',
                    "invocie_pay_amount" => $pay_amount,
                    "invoice_number"     => $inv_fk,
                    "invoice_no"         => '',
                    'type'               => 'Receipt',
                    'particulars'        => 'Payment Received' . $bank_info,
                    'is_opening_balance' => false,
                    'sort_date'          => $p_date
                );
            }
        }

        // 4. Bank Receipts / Payment In
        if (!empty($payment_in) && is_array($payment_in)) {
            foreach ($payment_in as $key1) {
                // If payment_in was used to pay an invoice, only include remaining unallocated balance to avoid double-counting
                $pay_amount = ($key1->status === 'used') ? (float)$key1->pay_balance : (float)$key1->payment;
                if ($pay_amount < 0) {
                    log_message('error', "Negative payment_in amount detected: $pay_amount. Skipped.");
                    continue;
                }
                if ($pay_amount == 0) {
                    continue;
                }
                $p_date = $key1->payment_date;
                $v_type = !empty($key1->bank_voucher_type) ? $key1->bank_voucher_type : 'Receipt';
                $bank_name = !empty(trim($key1->payment_bank)) ? ' (' . trim($key1->payment_bank) . ')' : '';
                $is_refund = (strtolower($v_type) === 'payment');

                $ledger_array[] = array(
                    "invoice_date"       => $p_date,
                    "display_date"       => date('d-m-Y', strtotime($p_date)),
                    "total"              => $is_refund ? $pay_amount : '',
                    "invocie_pay_amount" => $is_refund ? '' : $pay_amount,
                    "invoice_number"     => $key1->payment_id,
                    "invoice_no"         => '',
                    'type'               => $v_type,
                    'particulars'        => ($is_refund ? 'Dr Refund' : 'Cr Receipt') . $bank_name,
                    'is_opening_balance' => false,
                    'sort_date'          => $p_date
                );
            }
        }

        // 5. Chronological Sort (Opening Balance first, then by date, then Sales before Receipts)
        usort($ledger_array, function($a, $b) {
            if (!empty($a['is_opening_balance']) && empty($b['is_opening_balance'])) return -1;
            if (empty($a['is_opening_balance']) && !empty($b['is_opening_balance'])) return 1;

            $da = strtotime($a['sort_date']);
            $db = strtotime($b['sort_date']);
            if ($da === $db) {
                $priority = ['Opening Balance' => 0, 'Sales' => 1, 'Payment' => 2, 'Receipt' => 3];
                $pa = $priority[$a['type']] ?? 4;
                $pb = $priority[$b['type']] ?? 4;
                return $pa - $pb;
            }
            return $da - $db;
        });

        $data['ledger'] = $ledger_array;

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

        if (empty($supplier_name)) {
            $this->session->set_flashdata('INFOMSG', 'Please select a supplier first.');
            redirect('PaymentController/ledger_report');
            return;
        }

        $from_date = !empty($from_date1) ? date('Y-m-d', strtotime($from_date1)) : date('Y-04-01');
        $to_date   = !empty($to_date1)   ? date('Y-m-d', strtotime($to_date1))   : date('Y-m-d');

        $data['from_date'] = !empty($from_date1) ? $from_date1 : date('01-04-Y');
        $data['to_date']   = !empty($to_date1)   ? $to_date1   : date('d-m-Y');

        $data['company_name'] = '';
        $data['address'] = '';
        $data['gst'] = '';

        $supplier_details = $this->supplier->get_supplier_by_id($supplier_name);
        if (!empty($supplier_details)) {
            $data['company_name'] = isset($supplier_details['company_name']) ? $supplier_details['company_name'] : '';
            $data['address']      = isset($supplier_details['address'])      ? $supplier_details['address']      : '';
            $data['gst']          = isset($supplier_details['gst'])          ? $supplier_details['gst']          : '';
        }

        $invoice         = $this->payment->get_purchse_bill_ledger($from_date, $to_date, $supplier_name);
        $payments        = $this->payment->get_purchse_bill_payment_history($from_date, $to_date, $supplier_name);
        $payment_out     = $this->payment->get_purchase_gst_ledger_payment_out($from_date, $to_date, $supplier_name);
        $opening_balance = $this->payment->get_supplier_opening_balance($supplier_name, $from_date, $this->user_id);

        $ledger_array = array();

        // 1. Supplier Opening Balance (Credit in Vendor Ledger)
        if ($opening_balance && (float)$opening_balance->opening_balance_amount != 0) {
            $ob_amount = (float)$opening_balance->opening_balance_amount;
            if ($ob_amount < 0) {
                log_message('error', "Negative opening balance detected for supplier $supplier_name: $ob_amount. Rejected.");
            } else {
                $ledger_array[] = array(
                    "invoice_date"       => $from_date,
                    "display_date"       => date('d-m-Y', strtotime($from_date)),
                    "total"              => $ob_amount, // Shows in Credit
                    "invocie_pay_amount" => '',
                    "invoice_number"     => $opening_balance->balance_id,
                    "invoice_no"         => '',
                    'type'               => 'Opening Balance',
                    'particulars'        => 'Cr Opening Balance',
                    'is_opening_balance' => true,
                    'sort_date'          => $from_date
                );
            }
        }

        // 2. Purchase Bills (Credit)
        if (!empty($invoice) && is_array($invoice)) {
            foreach ($invoice as $key) {
                $bill_total = (float)$key->total;
                if ($bill_total < 0) {
                    log_message('error', "Negative purchase bill amount detected for bill {$key->number}: $bill_total. Skipped.");
                    continue;
                }
                $inv_date = $key->date;
                $ledger_array[] = array(
                    "invoice_date"       => $inv_date,
                    "display_date"       => date('d-m-Y', strtotime($inv_date)),
                    "invoice_number"     => $key->number,
                    "invoice_no"         => $key->invoice_no,
                    "total"              => $bill_total,
                    "invocie_pay_amount" => '',
                    'type'               => 'Prch',
                    'particulars'        => 'Purchase Bill',
                    'is_opening_balance' => false,
                    'sort_date'          => $inv_date
                );
            }
        }

        // 3. Purchase Bill Payments (Debit)
        if (!empty($payments) && is_array($payments)) {
            foreach ($payments as $key1) {
                $pay_amount = (float)$key1->purchase_pay_amount;
                if ($pay_amount < 0) {
                    log_message('error', "Negative purchase payment amount detected: $pay_amount. Skipped.");
                    continue;
                }
                if ($pay_amount == 0) {
                    continue;
                }
                $p_date = date('Y-m-d', strtotime($key1->purchase_pay_date));
                $ledger_array[] = array(
                    "invoice_date"       => $p_date,
                    "display_date"       => date('d-m-Y', strtotime($key1->purchase_pay_date)),
                    "total"              => '',
                    "invocie_pay_amount" => $pay_amount,
                    "invoice_number"     => !empty($key1->purchase_number_fk) ? $key1->purchase_number_fk : '',
                    "invoice_no"         => '',
                    'type'               => 'Payment',
                    'particulars'        => 'Payment Made',
                    'is_opening_balance' => false,
                    'sort_date'          => $p_date
                );
            }
        }

        // 4. Bank Payments Out (Debit)
        if (!empty($payment_out) && is_array($payment_out)) {
            foreach ($payment_out as $key1) {
                // If payment_out was used to pay a purchase bill, only include remaining unallocated balance to avoid double-counting
                $pay_amount = ($key1->status === 'used') ? (float)$key1->pay_balance : (float)$key1->payment;
                if ($pay_amount < 0) {
                    log_message('error', "Negative payment_out amount detected: $pay_amount. Skipped.");
                    continue;
                }
                if ($pay_amount == 0) {
                    continue;
                }
                $p_date = $key1->payment_date;
                $v_type = !empty($key1->bank_voucher_type) ? $key1->bank_voucher_type : 'Payment';
                $bank_name = !empty(trim($key1->payment_bank)) ? ' (' . trim($key1->payment_bank) . ')' : '';
                $is_refund = (strtolower($v_type) === 'receipt');

                $ledger_array[] = array(
                    "invoice_date"       => $p_date,
                    "display_date"       => date('d-m-Y', strtotime($p_date)),
                    "total"              => $is_refund ? $pay_amount : '',
                    "invocie_pay_amount" => $is_refund ? '' : $pay_amount,
                    "invoice_number"     => $key1->payment_id,
                    "invoice_no"         => '',
                    'type'               => $v_type,
                    'particulars'        => ($is_refund ? 'Cr Refund' : 'Dr Payment') . $bank_name,
                    'is_opening_balance' => false,
                    'sort_date'          => $p_date
                );
            }
        }

        // 5. Chronological Sort (Opening Balance first, then by date)
        usort($ledger_array, function($a, $b) {
            if (!empty($a['is_opening_balance']) && empty($b['is_opening_balance'])) return -1;
            if (empty($a['is_opening_balance']) && !empty($b['is_opening_balance'])) return 1;

            $date_a = strtotime($a['sort_date']);
            $date_b = strtotime($b['sort_date']);

            if ($date_a == $date_b) {
                $priority = [
                    'Opening Balance' => 0,
                    'Prch'            => 1,
                    'Payment'         => 2,
                    'Receipt'         => 3
                ];
                $priority_a = $priority[$a['type']] ?? 4;
                $priority_b = $priority[$b['type']] ?? 4;
                return $priority_a - $priority_b;
            }

            return $date_a - $date_b;
        });

        $data['ledger'] = $ledger_array;

        $this->load->view('payment_history/purchase_ledger_report', $data);
    }
}
