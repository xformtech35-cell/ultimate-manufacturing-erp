<?php

class Salesorder extends CI_Model
{


    private $crm;
    function __construct()
    {
        parent::__construct();
        //load our second db and put in $db2
        // $this->crm = $this->load->database('crm', TRUE);
    }


    public function add_customer($data_customer)
    {
        return $this->db->insert('customer', $data_customer);
    }

    public function get_last_salesorder_number($uid)
    {
        $financial_year = '';
        if (date('m') <= 3) { //Upto June 2014-2015
            // echo "hrov";die();
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else { //After June 2015-2016
            // echo "hrov123";die();
            $financial_year = date('y') . '-' . (date('y') + 1);
        }
        //   echo $financial_year;die();

        $this->db->select('count(number_fk) as id');
        $this->db->from('salesorder_total');
        $this->db->like('number_fk', $financial_year, "before");
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();
        $result = $query->row();
        if($result->id == 0){
            $result->id = 1;
        }

        return $result->id;


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
        $this->db->select("customer.email, COALESCE(customer.customer_mobile, customer.mobile, '') as mobile", FALSE);
        $this->db->from('salesorder_total');
        $this->db->where('salesorder_total.number_fk', $number);
        // $this->db->where('salesorder_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=salesorder_total.customer_id_fk', 'Left Join');
        $this->db->order_by('salesorder_total.id', 'DESC');
        $this->db->limit(1);
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
    public function get_company_name1($po_date1, $po_date2, $fullname, $uid)
    {
        $this->db->distinct();
        $this->db->select('salesorder.product_name, salesorder.customer_id, customer.company_name');
        $this->db->from('salesorder');
        $this->db->join('salesorder_total', 'salesorder_total.number_fk = salesorder.number', 'LEFT');
        $this->db->join('customer', 'customer.customer_id = salesorder.customer_id', 'LEFT');
        $this->db->where('salesorder_total.date >=', $po_date1);
        $this->db->where('salesorder_total.date <=', $po_date2);
        $this->db->where('salesorder.product_name', $fullname);
        $this->db->order_by('customer.company_name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_item_name1($po_date1, $po_date2, $fullname, $uid)
    {
        $this->db->distinct();
        $this->db->select('salesorder.product_name, salesorder.quantity');
        $this->db->from('salesorder');
        $this->db->join('salesorder_total', 'salesorder_total.number_fk = salesorder.number', 'LEFT');
        $this->db->where('salesorder_total.date >=', $po_date1);
        $this->db->where('salesorder_total.date <=', $po_date2);
        $this->db->where('salesorder.product_name', $fullname);
        $this->db->order_by('salesorder.product_name', 'asc');
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

    public function get_salesorders($uid, $limit = 1000)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year)) {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31';
            $this->db->where('salesorder_total.date >=', $fy_from);
            $this->db->where('salesorder_total.date <=', $fy_to);
        }

        $this->db->select('salesorder_total.id, salesorder_total.project_code, customer.company_name as customer_name, customer.fullname, salesorder_total.number_fk as number, COALESCE(q.gst_type, "S") as gst_type, salesorder_total.date, salesorder_total.basic_total, salesorder_total.total, salesorder_total.status, u1.username as created_by_name, u2.username as approved_by_name');
        $this->db->join('user u1', 'u1.user_id = salesorder_total.uid', 'left');
        $this->db->join('user u2', 'u2.user_id = salesorder_total.approved_by', 'left');
        $this->db->from('salesorder_total');
        $this->db->join('customer', 'customer.customer_id=salesorder_total.customer_id_fk', 'left');
        $this->db->join('salesorder q', 'q.number=salesorder_total.number_fk', 'left');
        // $this->db->where('salesorder_total.uid', $uid);
        $this->db->group_by('salesorder_total.id');
        $this->db->order_by('salesorder_total.id', 'DESC');
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();

        return $query->result();
    }

    public function get_salesorders_for_joborder($uid, $limit = 1000)
    {
        $this->db->select('salesorder_total.id, salesorder_total.project_code, customer.company_name as customer_name, customer.fullname, salesorder_total.number_fk as number, COALESCE(q.gst_type, "S") as gst_type, salesorder_total.date, salesorder_total.basic_total, salesorder_total.total, salesorder_total.status, u1.username as created_by_name, u2.username as approved_by_name');
        $this->db->join('user u1', 'u1.user_id = salesorder_total.uid', 'left');
        $this->db->join('user u2', 'u2.user_id = salesorder_total.approved_by', 'left');
        $this->db->from('salesorder_total');
        $this->db->join('customer', 'customer.customer_id=salesorder_total.customer_id_fk', 'left');
        $this->db->join('salesorder q', 'q.number=salesorder_total.number_fk', 'left');
        // $this->db->where('salesorder_total.uid', $uid);
        $this->db->group_by('salesorder_total.id');
        $this->db->order_by('salesorder_total.id', 'DESC');
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_salesorder_data_by_status($status, $uid)
    {

        $this->db->select('qt.id, q.number, qt.date, c.company_name as customer_name, c.fullname, COALESCE(q.gst_type, "S") as gst_type, qt.project_code, qt.basic_total, qt.total, qt.status');
        $this->db->from('salesorder_total qt');
        $this->db->join('customer c', 'c.customer_id=qt.customer_id_fk', 'left');
        $this->db->join('salesorder q', 'q.number=qt.number_fk', 'left');
        $this->db->where('qt.status', $status);
        // $this->db->where('qt.uid', $uid);
        $this->db->group_by('qt.id');
        $this->db->order_by('qt.id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_salesorders_data($number, $uid)
    {
        $this->db->select('s.*, inventory.item_name');
        $this->db->from('salesorder s');
        $this->db->where('s.number', $number);
        // $this->db->where('s.uid', $uid);
        $this->db->join('inventory', 'inventory.code=s.product_name', 'Left Join');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_salesorders_data_group_by($number, $uid)
    {

        $this->db->select('qt.id, q.salesorder_id, qt.number_fk, q.product_name, qt.date, q.discount, qt.basic_total, qt.total, qt.enquiry, qt.exp_date, qt.transportation, qt.installation, qt.pay_terms, qt.salesorder_subheading, qt.salesorder_memo, qt.salesorder_footer, qt.terms_and_conditions, qt.payment_terms, qt.process_schedule, qt.taxes, qt.exclusions, qt.status,'
            . ' qt.project_code, qt.po_number, qt.po_date, qt.po_status, qt.attachment, qt.customer_code, qt.system, qt.location, qt.capacity, qt.oc_number, qt.project_qty, c.*, c.company_name, u1.username as created_by_name, u2.username as approved_by_name');
        $this->db->from('salesorder_total qt');
        $this->db->where('qt.number_fk', $number);
        // $this->db->where('qt.uid', $uid);
        $this->db->join('salesorder q', 'q.number=qt.number_fk', 'Left Join');
        $this->db->join('customer c', 'c.customer_id=qt.customer_id_fk', 'Left Join');
        $this->db->join('user u1', 'u1.user_id = qt.uid', 'left');
        $this->db->join('user u2', 'u2.user_id = qt.approved_by', 'left');
        $this->db->group_by('qt.number_fk');
        $query = $this->db->get();

        return $query->row_array();
    }



    public function get_convert_invoice_data($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('salesorder_total');
        $this->db->where('number', $number);
        // $this->db->where('salesorder_total.uid', $uid);
        $this->db->join('salesorder', 'salesorder.number=salesorder_total.number_fk', 'Left Join');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_convert_invoice_total($number)
    {
        $this->db->select('* ');
        $this->db->from('salesorder_total');
        $this->db->where('number_fk', $number);

        $query = $this->db->get();
        return $query->result();
    }



    public function delete_salesorder_by_quote_number($qoute_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number', $qoute_number);
        $this->db->delete('salesorder');
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_salesorder_total_by_quote_number($qoute_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $qoute_number);
        $this->db->delete('salesorder_total');
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
        return $this->db->insert('salesorder_total', $data_toatl_amount);
    }


    public function get_quote_id_data($number)
    {
        $this->db->select('salesorder_id');
        $this->db->from('salesorder');
        $this->db->where('number', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_total_amount($data_toatl_amount, $number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $number);
        $this->db->update('salesorder_total', $data_toatl_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function delete_salesorder_item($salesorder_id)
    {
        $this->db->where('salesorder_id', $salesorder_id);
        $this->db->delete('salesorder');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_salesorder_count($uid)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year)) {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31';
            $this->db->where('date >=', $fy_from);
            $this->db->where('date <=', $fy_to);
        }
        $this->db->select('*');
        $this->db->from('salesorder_total');
        $query = $this->db->get();
        return $query->num_rows();
    }


    public function get_status($number, $uid)
    {
        $this->db->select('status');
        $this->db->from('salesorder_total');
        $this->db->where('number_fk', $number);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }


    public function edit_gst_salesorder_status($data_status, $quote_number, $uid)
    {
        $this->db->where('number_fk', $quote_number);
        //$this->db->where('uid', $uid);
        $this->db->update('salesorder_total', $data_status);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_enquiry_status($number, $uid)
    {
        $this->db->select('enquiry');
        $this->db->from('salesorder_total');
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_salesorder_number_from_salesorder_total($id, $uid)
    {
        $this->db->select('number_fk');
        $this->db->from('salesorder_total');
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



    public function get_salesorder_draft_count($status, $uid)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year)) {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31';
            $this->db->where('salesorder_total.date >=', $fy_from);
            $this->db->where('salesorder_total.date <=', $fy_to);
        }
        $this->db->select('*');
        $this->db->from('salesorder_total');
        $this->db->where('salesorder_total.status', $status);
        $query = $this->db->get();
        return $query->num_rows();
    }


public function get_project_code($uid)
{
    $this->db->select('project_code');
    $this->db->from($this->db->dbprefix . 'project');
    $query = $this->db->get();
    return $query->result();
}


    public function add_sales_total($data_sales_total)
    {
        return $this->db->insert('salesorder', $data_sales_total);
    }


    public function get_monthyearwise_record($month_year, $uid)
    {

        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        //print_r($newmonthyear_str);die();
        $this->db->select('salesorder_total.id, salesorder_total.project_code, customer.company_name as customer_name, customer.fullname, salesorder_id, gst_type, number, date, basic_total, total, status');
        $this->db->from('salesorder');
        $this->db->like('date', $newmonthyear_str, 'both');
        $this->db->join('salesorder_total', 'salesorder_total.number_fk=salesorder.number', 'Left Join');
        $this->db->join('customer', 'customer.customer_id=salesorder.customer_id', 'Left Join');
        $this->db->group_by('salesorder.number');
        $this->db->order_by("salesorder.salesorder_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }


        public function get_sales_order_po_number($id, $uid)
    {
        $this->db->select('po_number, po_status, po_date');
        $this->db->from('salesorder_total');
        $this->db->where('customer_id_fk', $id);
                $this->db->where('po_status', "Open");

         $query = $this->db->get();
        return $query->result();
    }

    public function get_po_items($po_number, $uid)
    {
        // First, get the salesorder number (number_fk) from po_number
        // Note: uid filter not used here, similar to get_sales_order_po_number method
        $this->db->select('number_fk');
        $this->db->from('salesorder_total');
        $this->db->where('po_number', $po_number);
        $this->db->limit(1);
        $po_query = $this->db->get();
        $po_result = $po_query->row();

        if (!$po_result) {
            return array();
        }

        $number_fk = $po_result->number_fk;

        // Now fetch all items for this salesorder number
        // Using correct field names from the database schema
        $this->db->select('s.product_name, s.description, s.hsn_code, s.quantity, s.gst as gst_per, s.sgst, s.cgst, s.igst, s.price, s.discount, inv.item_name');
        $this->db->from('salesorder s');
        $this->db->where('s.number', $number_fk);
        $this->db->join('inventory inv', 'inv.code=s.product_name', 'Left Join');
        $query = $this->db->get();
        return $query->result();
    }
/**
 * Fetch all Sales Order numbers with customer names for BOM / Job Order SO selector.
 */
public function get_so_list_for_bom($uid)
{
    $this->db->select('salesorder_total.number_fk as so_number, customer.company_name as customer_name, customer.c_code');
    $this->db->from('salesorder_total');
    $this->db->join('customer', 'customer.customer_id = salesorder_total.customer_id_fk', 'left');
    $this->db->where('salesorder_total.number_fk !=', '');
    $this->db->group_by('salesorder_total.number_fk');
    $this->db->order_by('salesorder_total.id', 'DESC');
    $query = $this->db->get();
    return $query->result();
}

public function add_non_gst_invoice($data_invoice)
{
    return $this->db->insert('non_gst_invoice', $data_invoice);
}

}