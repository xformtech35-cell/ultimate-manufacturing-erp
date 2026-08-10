<?php

class Estimate extends CI_Model
{

    public function add_customer($data_customer)
    {
        return $this->db->insert('customer', $data_customer);
    }

    public function get_last_quotation_number($uid)
    {

        $financial_year = '';
        if (date('m') <= 3) { //Upto June 2014-2015
            // echo "hrov";die();
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else { //After June 2015-2016
            // echo "hrov123";die();
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        $this->db->select('count(number_fk) as id');
        $this->db->from('quotation_total');
        $this->db->like('number_fk', $financial_year, "before");
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $result = $query->row();

        return $result->id;
    }

    public function get_last_non_gst_quotation_number($uid)
    {

        $this->db->select('COUNT(uid)');
        $this->db->from('non_gst_quotation_total');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function get_customer($uid)
    {
        $this->db->select('*');
        $this->db->from('customer');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function customer_check($company_name, $uid)
    {
        $this->db->select('company_name');
        $this->db->from('customer');
        //$this->db->where('uid', $uid);
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

    public function get_customer_email($number, $uid)
    {
        $this->db->select('email');
        $this->db->from('quotation');
        $this->db->where('number', $number);
        // $this->db->where('quotation.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=quotation.customer_id', 'Left Join');
        $this->db->group_by('quotation.number');
        $query = $this->db->get();
        return $query->row_array();
    }


    public function get_customer_mobile($number, $uid)
    {
        $this->db->select('mobile');
        $this->db->from('quotation');
        $this->db->where('number', $number);
        // $this->db->where('quotation.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=quotation.customer_id', 'Left Join');
        $this->db->group_by('quotation.number');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_company_name($uid)
    {
        $this->db->select('*');
        $this->db->from('customer');
        //$this->db->where('uid', $uid);
        $this->db->order_by("company_name", "asc");
        $query = $this->db->get();
        return $query->result();
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

    public function get_estimate($item_name, $uid)
    {
        $this->db->select('*');
        $this->db->from('inventory');
        $this->db->join('barcode_master', 'inventory.code=barcode_master.item', 'left');
        $this->db->where('code', $item_name);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_inventory_id_by_code($product_name)
    {
        $this->db->select('inventory_id');
        $this->db->from('inventory');
        $this->db->where('code', $product_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_customer_wise_rate($item_name, $company_id)
    {

        $this->db->select('customer_rate');
        $this->db->from('customer_wise_rate');
        $this->db->where('inventory_id_fk', $item_name);
        $this->db->where('customer_id_fk', $company_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_estimates($uid, $limit = 1000)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31';
            $this->db->where('quotation_total.date >=', $fy_from);
            $this->db->where('quotation_total.date <=', $fy_to);
        }

        $this->db->select('quotation_total.id,quotation_id, gst_type,number, date, fullname, company_name, basic_total, total,status, customer.customer_id');
        $this->db->from('quotation');
        $this->db->join('customer', 'customer.customer_id=quotation.customer_id', 'Left Join');
        $this->db->join('quotation_total', 'quotation_total.number_fk=quotation.number', 'Left Join');
        $this->db->group_by('quotation.number');
        $this->db->order_by("quotation_total.id", "desc");
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();

        return $query->result();
    }

    //    public function get_non_gst_estimates($uid) {
    //        $this->db->select('quotation_id,number, product_name, date, fullname, basic_total, total,status, customer.customer_id');
    //        $this->db->from('non_gst_quotation');
    //        $this->db->where('non_gst_quotation.uid', $uid);
    //        $this->db->where('non_gst_quotation_total.uid', $uid);
    //        $this->db->join('customer', 'customer.customer_id=non_gst_quotation.customer_id', 'Left Join');
    //        $this->db->join('non_gst_quotation_total', 'non_gst_quotation_total.number_fk=non_gst_quotation.number', 'Left Join');
    //        $this->db->group_by('non_gst_quotation.number');
    //        $this->db->order_by("non_gst_quotation.quotation_id", "desc");
    //        $query = $this->db->get();
    //        return $query->result();
    //    }

    public function get_quotation_data_by_status($status, $uid)
    {

        $this->db->select('id,number, date, fullname,company_name, gst_type, basic_total, total,status');
        $this->db->from('quotation q');
        // $this->db->where('q.uid', $uid);
        // $this->db->where('qt.uid', $uid);
        $this->db->join('customer c', 'c.customer_id=q.customer_id', 'Left Join');
        $this->db->join('quotation_total qt', 'qt.number_fk=q.number', 'Left Join');
        $this->db->where('status', $status);
        $this->db->group_by('q.number');
        $this->db->order_by("qt.id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_estimates_data($number, $uid)
    {
        $this->db->select('q.*, inventory.item_name');
        $this->db->from('quotation q');
        $this->db->where('number', $number);
        $this->db->join('inventory', 'inventory.code=q.product_name', 'Left Join');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();



        // print_r($query->result());
        // die();
        return $query->result();
    }

    public function get_non_gst_estimates_data($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('non_gst_quotation');
        $this->db->where('number', $number);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_estimates_data_group_by($number, $uid)
    {
        $this->db->select('id,quotation_id,number, qt.number_fk, product_name, date, company_name, fullname, pancard, c.mobile, c.email, c.gst, c.state_code, c.c_code, discount, basic_total, total, enquiry, address, company_name, exp_date, quotation_subheading,quotation_memo, quotation_footer, terms_and_conditions, payment_terms, process_schedule, taxes, exclusions, status, qt.project_code,'
            . ' u.username as prepare_by');
        $this->db->from('quotation q');
        $this->db->where('number', $number);
        //   $this->db->where('q.uid', $uid);
        //  $this->db->where('qt.uid', $uid);
        $this->db->join('customer c', 'c.customer_id=q.customer_id', 'Left Join');
        $this->db->join('quotation_total qt', 'qt.number_fk=q.number', 'Left Join');
        $this->db->join('user u', 'qt.uid=u.user_id', 'Left Join');
        //  $this->db->join('user u1', 'qt.approved_by=u1.user_id', 'Left Join');
        $this->db->group_by('q.number');
        $query = $this->db->get();



        $this->db->select('u1.username as approved_by');
        $this->db->from('quotation_total qt');
        $this->db->where('number_fk', $number);
        $this->db->join('user u1', 'qt.approved_by=u1.user_id', 'Left Join');
        $query1 = $this->db->get();

        $app_by = $query1->row_array();


        $arr = $query->row_array();

        if ($app_by && isset($app_by['approved_by'])) {
            $arr['approved_by'] = $app_by['approved_by'];
        } else {
            $arr['approved_by'] = '';
        }

        // Set default values for missing fields
        if (!isset($arr['project_code'])) {
            $arr['project_code'] = '';
        }
        if (!isset($arr['po_number'])) {
            $arr['po_number'] = '';
        }

        return $arr;
    }

    public function get_non_gst_estimates_data_group_by($number, $uid)
    {
        $this->db->select('quotation_id,number, product_name, date, company_name, fullname, pancard, c.gst,discount, basic_total, total, enquiry, address, company_name, exp_date, quotation_subheading,quotation_memo, quotation_footer');
        $this->db->from('non_gst_quotation q');
        $this->db->where('number', $number);
        //  $this->db->where('q.uid', $uid);
        // $this->db->where('qt.uid', $uid);
        $this->db->join('customer c', 'c.customer_id=q.customer_id', 'Left Join');
        $this->db->join('non_gst_quotation_total qt', 'qt.number_fk=q.number', 'Left Join');
        $this->db->group_by('q.number');
        $query = $this->db->get();
        $arr = $query->row_array();

        // Set default values for potentially missing fields
        if (!isset($arr['approved_by'])) {
            $arr['approved_by'] = '';
        }
        if (!isset($arr['project_code'])) {
            $arr['project_code'] = '';
        }
        if (!isset($arr['po_number'])) {
            $arr['po_number'] = '';
        }

        return $arr;
    }

    public function get_convert_invoice_data($number, $uid)
    {
        $this->db->select('
            quotation.quotation_id,
            quotation.customer_id,
            quotation.number,
            quotation.date,
            quotation.exp_date,
            quotation.product_name,
            quotation.quantity,
            quotation.unit,
            quotation.hsn_code,
            quotation.gst,
            quotation.sgst,
            quotation.cgst,
            quotation.igst,
            quotation.gst_type,
            quotation.price,
            quotation.amount,
            quotation.discount,
            quotation.description,
            quotation.uid,
            quotation_total.id,
            quotation_total.number_fk,
            quotation_total.basic_total,
            quotation_total.total,
            quotation_total.customer_id_fk,
            quotation_total.enquiry,
            quotation_total.status,
            quotation_total.payment_method,
            quotation_total.quotation_subheading,
            quotation_total.quotation_footer,
            quotation_total.quotation_memo,
            quotation_total.terms_and_conditions,
            quotation_total.payment_terms,
            quotation_total.process_schedule,
            quotation_total.taxes,
            quotation_total.exclusions,
            quotation_total.sez,
            quotation_total.approved_by,
            quotation_total.project_code
        ');
        $this->db->from('quotation_total');
        $this->db->where('quotation_total.number_fk', $number);
        $this->db->where('quotation_total.uid', $uid);
        $this->db->join('quotation', 'quotation.number=quotation_total.number_fk', 'Left Join');
        $query = $this->db->get();

        return $query->result();
    }
    public function get_convert_invoice_total($number)
    {
        $this->db->select('* ');
        $this->db->from('quotation_total');
        $this->db->where('number_fk', $number);

        $query = $this->db->get();
        return $query->result();
    }

    public function add_invoice($data_invoice)
    {
        return $this->db->insert('invoice', $data_invoice);
    }
    public function add_salesorder($data_salesorder)
    {
        // Define allowed columns for salesorder table based on your structure
        $allowed_columns = [
            'customer_id',
            'number',
            'product_name',
            'quantity',
            'unit',
            'hsn_code',
            'gst',
            'sgst',
            'cgst',
            'igst',
            'gst_type',
            'price',
            'amount',
            'discount',
            'description',
            'uid'
        ];

        // Filter data to only include allowed columns
        $filtered_data = array();
        foreach ($allowed_columns as $column) {
            if (isset($data_salesorder[$column])) {
                $filtered_data[$column] = $data_salesorder[$column];
            }
        }

        // If no data to insert, return false
        if (empty($filtered_data)) {
            log_message('error', 'No valid data to insert into salesorder table');
            return false;
        }

        return $this->db->insert('salesorder', $filtered_data);
    }

    public function add_non_gst_invoice($data_invoice)
    {
        return $this->db->insert('non_gst_invoice', $data_invoice);
    }

    public function delete_quotation_by_quote_number($qoute_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number', $qoute_number);
        $this->db->delete('quotation');
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_quotation_total_by_quote_number($qoute_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $qoute_number);
        $this->db->delete('quotation_total');
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_settings($uid)
    {
        $this->db->select('*');
        $this->db->from('settings');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_total_amount($data_toatl_amount)
    {
        return $this->db->insert('quotation_total', $data_toatl_amount);
    }

    public function add_total_amount_non_gst($data_toatl_amount)
    {
        return $this->db->insert('non_gst_quotation_total', $data_toatl_amount);
    }

    public function get_quote_id_data($number)
    {
        $this->db->select('quotation_id');
        $this->db->from('quotation');
        $this->db->where('number', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_total_amount($data_toatl_amount, $number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $number);
        $this->db->update('quotation_total', $data_toatl_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit_total_non_gst_amount($data_toatl_amount, $number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $number);
        $this->db->update('non_gst_quotation_total', $data_toatl_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_item($quotation_id)
    {
        $this->db->where('quotation_id', $quotation_id);
        $this->db->delete('quotation');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_quotation_count($uid)
    {
        return $this->db->count_all('quotation_total');
    }

    //    public function get_non_gst_quotation_count($uid) {
    //        $this->db->select('*');
    //        $this->db->from('non_gst_quotation_total');
    //        //$this->db->where('uid', $uid);
    //        $query = $this->db->get();
    //        return $query->num_rows();
    //    }
    //    public function delete_non_gst_quotation_by_quote_number($qoute_number, $uid) {
    //        $this->db->where('number', $qoute_number);
    //        //$this->db->where('uid', $uid);
    //        $this->db->delete('non_gst_quotation');
    //        if ($this->db->affected_rows() >= '1') {
    //            $this->db->where('number_fk', $qoute_number);
    //            $this->db->delete('non_gst_quotation_total');
    //            if ($this->db->affected_rows() >= '1') {
    //                return TRUE;
    //            } else {
    //                return FALSE;
    //            }
    //            return TRUE;
    //        } else {
    //            return FALSE;
    //        }
    //    }
    //    public function get_convert_invoice_non_gst_data($number, $uid) {
    //        $this->db->select('* ');
    //        $this->db->from('non_gst_quotation');
    //        $this->db->where('non_gst_quotation.uid', $uid);
    //        $this->db->where('non_gst_quotation_total.uid', $uid);
    //        $this->db->where('number', $number);
    //        $this->db->join('non_gst_quotation_total', 'non_gst_quotation_total.number_fk=non_gst_quotation.number', 'Left Join');
    //        $query = $this->db->get();
    //        return $query->result();
    //    }
    //    public function get_ng_quotation_data_by_status($status, $uid) {
    //
    //        $this->db->select('number, date, fullname, basic_total, total,status');
    //        $this->db->from('non_gst_quotation q');
    //        $this->db->where('q.uid', $uid);
    //        $this->db->where('qt.uid', $uid);
    //        $this->db->join('customer c', 'c.customer_id=q.customer_id', 'Left Join');
    //        $this->db->join('non_gst_quotation_total qt', 'qt.number_fk=q.number', 'Left Join');
    //        $this->db->where('status', $status);
    //        $this->db->group_by('q.number');
    //        $query = $this->db->get();
    //        return $query->result();
    //    }

    public function get_status($number, $uid)
    {
        $this->db->select('status');
        $this->db->from('quotation_total');
        $this->db->where('number_fk', $number);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_status_ng($number, $uid)
    {
        $this->db->select('status');
        $this->db->from('non_gst_quotation_total');
        $this->db->where('number_fk', $number);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_non_gst_quotation_status($data_status, $quote_number, $uid)
    {
        $this->db->where('number_fk', $quote_number);
        //$this->db->where('uid', $uid);
        $this->db->update('non_gst_quotation_total', $data_status);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit_gst_quotation_status($data_status, $quote_number, $uid)
    {
        $this->db->where('number_fk', $quote_number);
        //$this->db->where('uid', $uid);
        $this->db->update('quotation_total', $data_status);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_enquiry_status($number, $uid)
    {
        $this->db->select('enquiry');
        $this->db->from('quotation_total');
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_non_gst_enquiry_status($number, $uid)
    {
        $this->db->select('enquiry');
        $this->db->from('non_gst_quotation_total');
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_quotation_number_from_quotation_total($id, $uid)
    {
        $this->db->select('number_fk');
        $this->db->from('quotation_total');
        $this->db->where('id', $id);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_item_name($item_name)
    {

        $this->db->select('*');
        $this->db->from('barcode_master');
        $this->db->where('barcode', $item_name);
        $this->db->where('status', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_quo_count($uid)
    {
        $this->db->select('*');
        $this->db->from('quotation_total');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_quo_draft_count($status, $uid)
    {
        $this->db->select('*');
        $this->db->from('quotation_total');
        $this->db->where('quotation_total.status', $status);
        //$this->db->where('quotation_total.uid', $uid);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_monthyearwise_record($month_year, $uid)
    {

        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        //print_r($newmonthyear_str);die();
        $this->db->select('quotation_total.id,quotation_id, gst_type,number, date, fullname, company_name, basic_total, total,status, customer.customer_id');
        $this->db->from('quotation');
        $this->db->like('date', $newmonthyear_str, 'both');
        $this->db->join('customer', 'customer.customer_id=quotation.customer_id', 'Left Join');
        $this->db->join('quotation_total', 'quotation_total.number_fk=quotation.number', 'Left Join');
        $this->db->group_by('quotation.number');
        $this->db->order_by("quotation_total.id", "desc");
        $query = $this->db->get();
        return $query->result();
    }
}
