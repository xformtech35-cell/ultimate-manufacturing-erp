<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Invoice extends CI_Model
{

    public function add_customer($data_customer)
    {
        return $this->db->insert('customer', $data_customer);
    }

    public function get_customer($uid)
    {
        $this->db->select('*');
        $this->db->from('customer');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_company_name($uid)
    {
        $this->db->select('*');
        $this->db->from('customer');
        //  $this->db->where('uid', $uid);
        $this->db->order_by("company_name", "asc");
        $query = $this->db->get();
        return $query->result();
    }


    public function get_company_name1($uid)
    {
        $this->db->select('*');
        $this->db->from('invoice');
        //  $this->db->where('uid', $uid);
        $this->db->order_by("product_name", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_status()
    {
        $this->db->select('status');
        $this->db->from('invoice_total');
        $this->db->distinct();
        $query = $this->db->get();
        return $query->result();
    }

    public function customer_check($company_name)
    {
        $this->db->select('company_name');
        $this->db->from('customer');
        $this->db->where('company_name', $company_name);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function add_user($data)
    {
        return $this->db->insert('user', $data);
    }

    public function get_customer_by_mobile($mobile)
    {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->join('user', 'customer.customer_mobile=user.user_id');
        $this->db->where('customer_mobile', $mobile);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_customer_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->where('customer_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete_customer_by_id($id)
    {
        $this->db->where('customer_id', $id);
        $this->db->delete('customer');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit_customer($data_customer, $customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->update('customer', $data_customer);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_estimate($item_name)
    {
        $this->db->select('*');
        $this->db->from('inventory');
        $this->db->where('item_name', $item_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_invoices($uid)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31';
            $this->db->where('invoice_total.date >=', $fy_from);
            $this->db->where('invoice_total.date <=', $fy_to);
        }

        $this->db->select('*, invoice_total.number_fk as invoice_number, invoice_total.id as id, invoice.invoice_date as invoice_date, customer.mobile, SUM(invocie_pay_amount) as total_balance_amount');
        $this->db->from('invoice');
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'left');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number', 'left');
        $this->db->join('invocie_payment_gst', 'invocie_payment_gst.invoice_number_fk=invoice_total.number_fk', 'left');
        $this->db->group_by('invoice_total.number_fk');
        $this->db->order_by("invoice_total.id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_performa_invoices($uid)
    {
        $this->db->select('* , SUM(invocie_pay_amount) as total_balance_amount');
        $this->db->from('performa_invoice');
        // $this->db->where('performa_invoice.uid', $uid);
        //  $this->db->where('performa_invoice_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=performa_invoice.customer_id', 'Left Join');
        $this->db->join('performa_invoice_total', 'performa_invoice_total.number_fk=performa_invoice.invoice_number', 'Right Join');
        $this->db->join('performa_invoice_payment_gst', 'performa_invoice_payment_gst.invoice_number_fk=performa_invoice_total.number_fk', 'Left');
        $this->db->group_by('performa_invoice.invoice_number');
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_non_gst_invoices($uid)
    {
        $this->db->select('*');
        $this->db->from('non_gst_invoice');
        // $this->db->where('non_gst_invoice.uid', $uid);
        //$this->db->where('non_gst_invoice_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=non_gst_invoice.customer_id', 'Left Join');
        $this->db->join('non_gst_invoice_total', 'non_gst_invoice_total.number_fk=non_gst_invoice.invoice_number', 'Right Join');
        $this->db->group_by('non_gst_invoice.invoice_number');
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_estimates_data($qoute_number)
    {
        $this->db->select('*');
        $this->db->from('quotation');
        $this->db->where('number', $qoute_number);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function quote_check($quote_number)
    {
        $this->db->select('number_fk');
        $this->db->from('invoice_total');
        $this->db->where('number_fk', $quote_number);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function invoice_name_exists($invoiceName, $uid = null)
    {
        $this->db->select('id');
        $this->db->from('invoice_total');
        $this->db->where('number_fk', $invoiceName);
        // Removed uid filter — check uniqueness globally across all users
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->num_rows() > 0;
    }

    public function add_invoice($data_invoice)
    {


    // var_dump($data_invoice);

    // die();
        return $this->db->insert('invoice', $data_invoice);
    }

    public function add_invoice_total($data_invoice_total)
    {
        //   print_r($data_invoice_total);die();
        return $this->db->insert('invoice_total', $data_invoice_total);
    }

    public function add_non_gst_invoice_total($data_invoice_total)
    {
        return $this->db->insert('non_gst_invoice_total', $data_invoice_total);
    }

    public function delete_invoice_by_invoice_number($invoice_number, $uid)
    {
        // print_r($invoice_number);die();
        // $this->db->where('uid', $uid);
        $this->db->where('invoice_number', $invoice_number);
        $this->db->delete('invoice');
        if ($this->db->affected_rows() >= '1') {
            $this->db->where('number_fk', $invoice_number);
            $this->db->delete('invoice_total');
            $this->db->where('invoice_number_fk', $invoice_number);
            $this->db->delete('invocie_payment_gst');
            if ($this->db->affected_rows() >= '0') {
                return TRUE;
            } else {
                return FALSE;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    // public function get_last_invoice_number($uid) {
    //     $financial_year = '';
    //     if (date('m') <= 3) {//Upto June 2014-2015
    //         // echo "hrov";die();
    //         $financial_year = (date('y') - 1) . '-' . date('y');
    //     } else {//After June 2015-2016
    //         // echo "hrov123";die();
    //         $financial_year = date('y') . '-' . (date('y') + 1);
    //     }

    //     $this->db->select('number_fk');
    //     $this->db->from('invoice_total');
    //     $this->db->like('number_fk', $financial_year, "before");
    //     $this->db->order_by('id', 'DESC');
    //     $query = $this->db->get();
    //     $result = $query->row();

    //     $pieces = explode("/", $result->number_fk);


    //     var_dump( $pieces);


    //     die();

    //     return $pieces[1];
    // }
    public function get_last_invoice_number($uid)
    {
        $this->db->select('number_fk');
        $this->db->from('invoice_total');

        // if (!empty($uid)) {
        //     $this->db->where('uid', $uid);
        // }

        $this->db->where('number_fk IS NOT NULL', null, false);
        $this->db->where('number_fk !=', '');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        $result = $query->row();

        if (is_object($result) && !empty($result->number_fk)) {
            if (preg_match('/^([^\/]+)\/(\d+)/', trim($result->number_fk), $matches)) {
                return (int)$matches[2];
            }
        }

        return 0;
    }

    private function increment_invoice_number($invoice)
    {
        $invoice = trim($invoice);
        if ($invoice === '') {
            return null;
        }

        $currentMonth = strtoupper(date('M'));
        $currentYear = (int) date('Y');
        $currentMonthNumber = (int) date('n');
        $fyStartYear = ($currentMonthNumber >= 4) ? $currentYear : $currentYear - 1;
        $fyEndYear = $fyStartYear + 1;
        $currentYearRange = substr((string) $fyStartYear, -2) . '-' . substr((string) $fyEndYear, -2);
        $currentSuffix = '/' . $currentMonth . '/' . $currentYearRange;

        // If the invoice ends in a month/year suffix like /MAY/26-27,
        // preserve the suffix format and update it to the current period.
        if (preg_match('/\/[A-Za-z]{3}\/\d{2,4}-\d{2,4}$/', $invoice, $suffixMatch, PREG_OFFSET_CAPTURE)) {
            $suffixStart = $suffixMatch[0][1];
            $prefixPart = substr($invoice, 0, $suffixStart);
            if (preg_match_all('/\d+/', $prefixPart, $digitMatches, PREG_OFFSET_CAPTURE)) {
                $lastMatch = end($digitMatches[0]);
                $number = $lastMatch[0];
                $offset = $lastMatch[1];
                $nextNumber = (int) $number + 1;
                $padded = str_pad($nextNumber, strlen($number), '0', STR_PAD_LEFT);
                return substr($prefixPart, 0, $offset) . $padded . substr($prefixPart, $offset + strlen($number)) . $currentSuffix;
            }

            return $prefixPart . $currentSuffix;
        }

        // If the invoice ends in a year-range like 26-27 or 2025-2026,
        // increment the previous numeric sequence instead of the year.
        if (preg_match('/(\d{2,4}-\d{2,4})$/', $invoice, $yearMatch, PREG_OFFSET_CAPTURE)) {
            $suffixStart = $yearMatch[0][1];
            $prefixPart = substr($invoice, 0, $suffixStart);
            if (preg_match_all('/\d+/', $prefixPart, $digitMatches, PREG_OFFSET_CAPTURE)) {
                $lastMatch = end($digitMatches[0]);
                $number = $lastMatch[0];
                $offset = $lastMatch[1];
                $nextNumber = (int) $number + 1;
                $padded = str_pad($nextNumber, strlen($number), '0', STR_PAD_LEFT);
                return substr($invoice, 0, $offset) . $padded . substr($invoice, $offset + strlen($number));
            }
        }

        if (preg_match_all('/\d+/', $invoice, $digitMatches, PREG_OFFSET_CAPTURE)) {
            $lastMatch = end($digitMatches[0]);
            $number = $lastMatch[0];
            $offset = $lastMatch[1];
            $nextNumber = (int) $number + 1;
            $padded = str_pad($nextNumber, strlen($number), '0', STR_PAD_LEFT);
            return substr($invoice, 0, $offset) . $padded . substr($invoice, $offset + strlen($number));
        }

        return null;
    }


    public function get_next_invoice_name($uid)
    {

        $this->db->select('number_fk');
        $this->db->from('invoice_total');

        // if (!empty($uid)) {
        //     $this->db->where('uid', $uid);
        // }

        $this->db->where('number_fk IS NOT NULL', null, false);
        $this->db->where('number_fk !=', '');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        $result = $query->row();

        if (is_object($result) && !empty($result->number_fk)) {
            $last_invoice = trim($result->number_fk);
            $next_invoice = $this->increment_invoice_number($last_invoice);
            if (!empty($next_invoice)) {
                return $next_invoice;
            }

            return 'INV/000001';
        }

        // Default if no record exists
        return 'INV/000001';
    }

    public function get_last_non_gst_invoice_number($uid)
    {
        $this->db->select('number_fk');
        $this->db->from('non_gst_invoice_total');

        // if (!empty($uid)) {
        //     $this->db->where('uid', $uid);
        // }

        $this->db->where('number_fk IS NOT NULL', null, false);
        $this->db->where('number_fk !=', '');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        $result = $query->row();

        if (is_object($result) && !empty($result->number_fk)) {
            if (preg_match('/^([^\/]+)\/(\d+)/', trim($result->number_fk), $matches)) {
                return array('COUNT(uid)' => (int)$matches[2]);
            }
        }

        return array('COUNT(uid)' => 0);
    }

    public function get_next_non_gst_invoice_name($uid)
    {

        $this->db->select('number_fk');
        $this->db->from('non_gst_invoice_total');

        // if (!empty($uid)) {
        //     $this->db->where('uid', $uid);
        // }

        $this->db->where('number_fk IS NOT NULL', null, false);
        $this->db->where('number_fk !=', '');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        $result = $query->row();

        if (is_object($result) && !empty($result->number_fk)) {
            $last_invoice = trim($result->number_fk);
            $next_invoice = $this->increment_invoice_number($last_invoice);
            if (!empty($next_invoice)) {
                return $next_invoice;
            }

            return 'UWS/000001';
        }

        // Default if no record exists
        return 'UWS/000001';
    }

    public function get_item_id($item)
    {
        $this->db->select('stock');
        $this->db->from('inventory');
        $this->db->where('item_name', $item);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_convert_invoice_total($number)
    {
        $this->db->select('* ');
        $this->db->from('quotation_total');
        $this->db->where('number_fk', $number);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update_invoice_total($invoice_number, $quote_number, $uid)
    {
        $this->db->where('number_fk', $quote_number);
        //$this->db->where('uid', $uid);
        $this->db->update('invoice_total', $invoice_number);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function update_non_gst_invoice_total($invoice_number, $quote_number, $uid)
    {
        $this->db->where('number_fk', $quote_number);
        //$this->db->where('uid', $uid);
        $this->db->update('non_gst_invoice_total', $invoice_number);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_invoice_data($invoice_number, $uid)
    {
        $this->db->select('i.*, inventory.item_name');
        $this->db->from('invoice i');
        $this->db->join('inventory', 'inventory.code=i.product_name', 'Left Join');
        $this->db->where('i.invoice_number', $invoice_number);
        // $this->db->where('i.uid', $uid);
        $query = $this->db->get();

        return $query->result();
    }

    public function update_invoice_data($invoice_number, $uid)
    {
        $this->db->select('*');
        $this->db->from('invoice');
        $this->db->where('invoice_number', $invoice_number);
        $query = $this->db->get();
        $result = $query->result();

        foreach ($result as $key) {

            $data = array();
            if ($key->gst_type == 'S') {
                $igst = $key->sgst * 2;
                $data = array('gst_type' => 'I', 'igst' => $igst, 'sgst' => 0, 'cgst' => 0);
                $this->db->where('invoice_id', $key->invoice_id);
                $this->db->update('invoice', $data);
            } else {
                $gst = $key->igst / 2;
                $data = array('gst_type' => 'S', 'igst' => 0, 'sgst' => $gst, 'cgst' => $gst);

                $this->db->where('invoice_id', $key->invoice_id);
                $this->db->update('invoice', $data);
            }
        }
    }

    public function get_ng_invoice_data($invoice_number, $uid)
    {
        $this->db->select('*');
        $this->db->from('non_gst_invoice');
        $this->db->where('invoice_number', $invoice_number);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ng_invoice_data_group_by($invoice_number, $uid)
    {
        $this->db->select('*');
        $this->db->from('non_gst_invoice');
        // $this->db->where('non_gst_invoice.uid', $uid);
        // $this->db->where('non_gst_invoice_total.uid', $uid);
        $this->db->where('invoice_number', $invoice_number);
        $this->db->join('customer', 'customer.customer_id=non_gst_invoice.customer_id', 'Left Join');
        $this->db->join('non_gst_invoice_total', 'non_gst_invoice_total.number_fk=non_gst_invoice.invoice_number', 'Right Join');
        $this->db->group_by('non_gst_invoice.invoice_number');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_invoice_data_group_by($invoice_number, $uid)
    {
        $this->db->select('*');
        $this->db->from('invoice_total');
        //  $this->db->where('invoice.uid', $uid);
        //  $this->db->where('invoice_total.uid', $uid);
        $this->db->where('invoice_total.number_fk', $invoice_number);
        $this->db->join('invoice', 'invoice.invoice_number=invoice_total.number_fk', 'left');
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'left');
        $this->db->group_by('invoice_total.number_fk');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_duplicate_invoice_data_group_by($invoice_number, $uid)
    {
        $this->db->select('*');
        $this->db->from('invoice_total');
        // $this->db->where('invoice.uid', $uid);
        //  $this->db->where('invoice_total.uid', $uid);
        $this->db->where('number_fk', $invoice_number);
        // $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'Left Join');
        // $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number', 'Right Join');
        //  $this->db->group_by('invoice.invoice_number');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit_invoice_payment($data_payment, $id)
    {

        $this->db->where('id', $id);
        $this->db->update('invoice_total', $data_payment);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit_non_gst_invoice_payment($data_payment, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('non_gst_invoice_total', $data_payment);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_invoice_payment_details($id)
    {
        $this->db->select('*');
        $this->db->from('invoice_total');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_proforma_invoice_payment_details($id)
    {
        $this->db->select('*');
        $this->db->from('proforma_invoice_total');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_non_gst_invoice_payment_details($id)
    {
        $this->db->select('*');
        $this->db->from('non_gst_invoice_total');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_invoice_data_by_status($status, $uid)
    {
        $this->db->select('*, invoice_total.number_fk as invoice_number, invoice_total.id as id, invoice.invoice_date as invoice_date, SUM(invocie_pay_amount) as total_balance_amount');
        $this->db->from('invoice');
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'left');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number', 'left');
        $this->db->join('invocie_payment_gst', 'invocie_payment_gst.invoice_number_fk=invoice_total.number_fk', 'left');
        $this->db->where('invoice_total.status', $status);
        //  $this->db->where('invoice.uid', $uid);
        // $this->db->where('invoice_total.uid', $uid);
        $this->db->group_by('invoice_total.number_fk');
        $this->db->order_by("invoice_total.id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ng_invoice_data_by_status($status, $uid)
    {
        $this->db->select('*');
        $this->db->from('non_gst_invoice');
        $this->db->join('customer', 'customer.customer_id=non_gst_invoice.customer_id', 'Left Join');
        $this->db->join('non_gst_invoice_total', 'non_gst_invoice_total.number_fk=non_gst_invoice.invoice_number', 'Right Join');
        $this->db->where('status', $status);
        //  $this->db->where('non_gst_invoice.uid', $uid);
        //   $this->db->where('non_gst_invoice_total.uid', $uid);
        $this->db->group_by('non_gst_invoice.invoice_number');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_invoice_count($uid)
    {
        return $this->db->count_all('invoice_total');
    }

    public function get_proforma_count($uid)
    {
        if ($this->db->table_exists('proforma_invoice_total')) {
            return $this->db->count_all('proforma_invoice_total');
        }
        return 0;
    }

    public function get_non_gst_invoice_count($uid)
    {
        if ($this->db->table_exists('non_gst_invoice_total')) {
            return $this->db->count_all('non_gst_invoice_total');
        }
        return 0;
    }

    public function get_invoice_draft_count($status, $uid)
    {
        $this->db->select('COUNT(DISTINCT number_fk) as cnt', FALSE);
        $this->db->from('invoice_total');
        $this->db->where('status', $status);
        $query = $this->db->get();
        $row = $query->row();
        return $row ? (int)$row->cnt : 0;
    }

    public function get_non_gst_invoice_status($status, $uid)
    {
        $this->db->select('*');
        $this->db->from('non_gst_invoice_total');
        $this->db->where('status', $status);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_customer_email($invoice_number, $uid)
    {
        $this->db->select('email, mobile');
        $this->db->from('invoice');
        $this->db->where('invoice_number', $invoice_number);
        //  $this->db->where('invoice.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'Left Join');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_invoice_unpaid_data($status)
    {
        $this->db->select('*');
        $this->db->from('invoice_total');
        $this->db->where('status', $status);
        $query = $this->db->get();
        return $query->result();
    }

    public function delete_non_gst_invoice_by_invoice_number($invoice_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('invoice_number', $invoice_number);
        $this->db->delete('non_gst_invoice');
        if ($this->db->affected_rows() >= '1') {
            $this->db->where('number_fk', $invoice_number);
            $this->db->delete('non_gst_invoice_total');
            if ($this->db->affected_rows() >= '1') {
                return TRUE;
            } else {
                return FALSE;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_customer_email_non_gst($invoice_number, $uid)
    {
        $this->db->select('email');
        $this->db->from('non_gst_invoice');
        $this->db->where('invoice_number', $invoice_number);
        //   $this->db->where('non_gst_invoice.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=non_gst_invoice.customer_id', 'Left Join');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete_item($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $this->db->delete('invoice');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_non_gst_item($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $this->db->delete('non_gst_invoice');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_inventory_stock_count($product_name, $uid)
    {
        $this->db->select('stock');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $this->db->where('code', $product_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_invoice_quantity_count($product_name, $invoice_number, $uid)
    {
        $this->db->select('quantity');
        $this->db->from('invoice');
        //$this->db->where('uid', $uid);
        $this->db->where('invoice_number', $invoice_number);
        $this->db->where('product_name', $product_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_non_gst_invoice_quantity_count($product_name, $invoice_number, $uid)
    {
        $this->db->select('quantity');
        $this->db->from('non_gst_invoice');
        //$this->db->where('uid', $uid);
        $this->db->where('invoice_number', $invoice_number);
        $this->db->where('product_name', $product_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_previous_balance_invoice($invoice_number, $uid)
    {
        $this->db->select('paid,total,balance , SUM(invocie_pay_amount) as total_balance_amount');
        $this->db->from('invoice_total');
        //  $this->db->where('invoice_total.uid', $uid);
        $this->db->where('number_fk', $invoice_number);
        $this->db->join('invocie_payment_gst', 'invocie_payment_gst.invoice_number_fk=invoice_total.number_fk', 'Left');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_status_by_invoiceid($invoice_number, $uid)
    {
        $this->db->select('status');
        $this->db->from('invoice_total');
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $invoice_number);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_status_by_non_gst_invoice($invoice_number, $uid)
    {
        $this->db->select('status');
        $this->db->from('non_gst_invoice_total');
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $invoice_number);
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_invoice_status($data_customer, $invoice_number, $uid)
    {
        $this->db->where('number_fk', $invoice_number);
        //$this->db->where('uid', $uid);
        $this->db->update('invoice_total', $data_customer);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit_non_gst_invoice_status($data_customer, $invoice_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $invoice_number);
        $this->db->update('non_gst_invoice_total', $data_customer);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function pay_gst_invoice_amount($invoice_payment_gst)
    {

        return $this->db->insert('invocie_payment_gst', $invoice_payment_gst);
    }

    public function get_pay_gst_invoice_amount($invoice_number, $uid)
    {
        $this->db->select('invocie_pay_amount');
        $this->db->from('invocie_payment_gst');
        //$this->db->where('uid', $uid);
        $this->db->where('invoice_number_fk', $invoice_number);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_pay_non_gst_invoice_amount($invoice_number, $uid)
    {
        $this->db->select('ng_invocie_pay_amount');
        $this->db->from('invocie_payment_non_gst');
        //$this->db->where('uid', $uid);
        $this->db->where('ng_invoice_number_fk', $invoice_number);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_previous_balance_non_gst_invoice($invoice_number, $uid)
    {
        $this->db->select('paid,total');
        $this->db->from('non_gst_invoice_total');
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $invoice_number);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function pay_non_gst_invoice_amount($invoice_payment_non_gst)
    {
        return $this->db->insert('invocie_payment_non_gst', $invoice_payment_non_gst);
    }

    public function get_advance_amount_by_customer_id($customer_id, $uid)
    {
        $this->db->select('advance_pay');
        $this->db->from('advance_amount');
        $this->db->where('customer_id_fk', $customer_id);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_invoice_payment_history_data($invoice_number, $uid)
    {
        $this->db->select('*');
        $this->db->from('invocie_payment_gst');
        $this->db->where('invoice_number_fk', $invoice_number);
        //$this->db->where('uid', $uid);
        $this->db->order_by("invocie_pay_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_invoice_number_from_invoice_total($id, $uid)
    {
        $this->db->select('number_fk');
        $this->db->from('invoice_total');
        $this->db->where('id', $id);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_item_name($uid)
    {
        $this->db->select('inventory_id, code');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function change_flag_barcode($item)
    {
        $status = 1;
        $this->db->set('status', $status);
        $this->db->where('item', $item);
        $this->db->update('barcode_master');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_barcode_status($item_barcode)
    {

        $this->db->set('status', '1');
        $this->db->where('barcode', $item_barcode);
        $this->db->update('barcode_master');
    }

    public function get_datewise_record($from_date, $to_date, $uid)
    {
        $f_date = date('Y-m-d', strtotime($from_date));
        $t_date = date('Y-m-d', strtotime($to_date));

        $this->db->select('*, invoice_total.number_fk as invoice_number, invoice_total.id as id, invoice.invoice_date as invoice_date, SUM(invocie_pay_amount) as total_balance_amount');
        $this->db->from('invoice');
        $this->db->where('invoice.invoice_date >=', $f_date);
        $this->db->where('invoice.invoice_date <=', $t_date);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'Left Join');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number', 'Right Join');
        $this->db->join('invocie_payment_gst', 'invocie_payment_gst.invoice_number_fk=invoice_total.number_fk', 'Left');
        $this->db->group_by('invoice_total.number_fk');
        $this->db->order_by("invoice_total.id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_monthyearwise_record($month_year, $uid)
    {
        // print_r($month_year);die();
        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        $this->db->select('*, invoice_total.number_fk as invoice_number, invoice_total.id as id, invoice.invoice_date as invoice_date, SUM(invocie_pay_amount) as total_balance_amount');
        $this->db->from('invoice');
        $this->db->like('invoice_date', $newmonthyear_str, 'both');
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'Left Join');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number', 'Right Join');
        $this->db->join('invocie_payment_gst', 'invocie_payment_gst.invoice_number_fk=invoice_total.number_fk', 'Left');
        $this->db->group_by('invoice_total.number_fk');
        $this->db->order_by("invoice_total.id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function print_voucher($invocie_pay_id, $uid)
    {
        // print_r($invocie_pay_id);die();
        $this->db->select('*');
        $this->db->from('invocie_payment_gst');
        $this->db->where('invocie_pay_id', $invocie_pay_id);

        $this->db->join('customer', 'customer.customer_id=invocie_payment_gst.customer_id_fk', 'Left Join');

        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_payment_in($uid)
    {
        $this->db->select('*');
        $this->db->from('payment_in');
        $this->db->join('customer', 'customer.customer_id=payment_in.payment_customer_id', 'Left Join');
        $this->db->order_by("payment_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_total_balance_payment_in($payment_customer_id)
    {
        $this->db->select_sum('payment');
        $this->db->from('payment_in');
        $this->db->where('payment_customer_id', $payment_customer_id);
        $query = $this->db->get();
        return $query->row()->payment;
    }


    public function print_voucher_in($invocie_pay_id, $uid)
    {
        $this->db->select('*');
        $this->db->from('payment_in');
        $this->db->where('payment_id', $invocie_pay_id);

        $this->db->join('customer', 'customer.customer_id=payment_in.payment_customer_id', 'Left Join');

        $query = $this->db->get();
        return $query->row_array();
    }


    public function get_company_name_with_bal($uid)
    {
        $this->db->select('customer_id, company_name');
        $this->db->from('customer');
        $this->db->order_by("company_name", "asc");
        $query = $this->db->get();

        $arr = $query->result();

        $arr1 = array();
        foreach ($arr as $key) {
            $this->db->select_sum('payment');
            $this->db->from('payment_in');
            $this->db->where('payment_customer_id', $key->customer_id);
            $query1 = $this->db->get();
            if ($query1->row()->payment == '' || $query1->row()->payment == 0 ||  $query1->row()->payment == NULL) {
                $arr1[] = array("company_name" => $key->company_name, "payment" => "0", "customer_id" => $key->customer_id);
            } else {
                $arr1[] = array("company_name" => $key->company_name, "payment" => $query1->row()->payment, "customer_id" => $key->customer_id);
            }
        }
        return $arr1;
    }


    public function get_pending_invoice_payment($id, $uid)
    {
        $this->db->select('number_fk, date, total, paid, balance, status, id');
        $this->db->from('invoice_total');
        $this->db->where('customer_id_fk', $id);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function getPaymentById($id)
    {
        $this->db->select('*');
        $this->db->from('payment_in');
        $this->db->where('payment_id', $id);
        $query = $this->db->get();

        return $query->row_array();
    }
}
