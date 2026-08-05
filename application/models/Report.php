<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Report extends CI_Model
{

    public function get_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select('*,invoice.invoice_number, invoice.gst, customer.gst as customer_gst');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);


        // $this->db->where('customer.fullname', $company_name);

        //  $this->db->where('invoice.uid', );
        //  $this->db->where('invoice_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number');
        //        if ($company_name) {
        //            $this->db->where('customer.company_name', $company_name);
        //        }
        $this->db->group_by('invoice.invoice_number');
        $query = $this->db->get();
        //   var_dump($query->result());die();
        return $query->result();
    }

  public function get_sales_report_by_hsn($from_date, $to_date, $uid)
{
    $invoice_table = $this->db->dbprefix('invoice');

    // HSN expression: replace empty string with 'NA'
    $hsn_expr = "COALESCE(NULLIF(i.hsn_code, ''), 'NA')";

    $this->db->select("
        {$hsn_expr} AS hsn_code,
        SUM(i.amount) AS taxable_value,
        SUM(i.amount + i.sgst + i.cgst + i.igst) AS total_value,
        SUM(i.sgst) AS sgst,
        SUM(i.cgst) AS cgst,
        SUM(i.igst) AS igst
    ", FALSE);

    $this->db->from($invoice_table . ' i');
    $this->db->where('i.invoice_date >=', $from_date);
    $this->db->where('i.invoice_date <=', $to_date);
    $this->db->where('i.uid', $uid);

    $this->db->group_by('hsn_code');      // Use alias
    $this->db->order_by('hsn_code', 'asc'); // Use alias

    $query = $this->db->get();
    return $query->result();
}
    public function get_report_by_date1($from_date, $to_date, $company_name, $transaction_type, $uid)
    {
        //   print_r($company_name);die();
        $this->db->select('*,invoice.gst,sum(sgst + cgst + igst) as total_gst_amount, customer.gst as customer_gst, sum(amount) as total_before_tax');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->where('customer.company_name', $company_name);

        //  $this->db->where('invoice.uid', );
        //  $this->db->where('invoice_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number');

        if ($company_name) {
            $this->db->where('customer.company_name', $company_name);
        }
        $this->db->group_by('invoice.amount');
        $query = $this->db->get();
        return $query->result();
    }




    public function get_non_gst_invoice_report_by_date($from_date, $to_date, $company_name, $uid)
    {
        $this->db->select('*');
        $this->db->from('non_gst_invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        //  $this->db->where('non_gst_invoice.uid', $uid);
        //  $this->db->where('non_gst_invoice_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=non_gst_invoice.customer_id');
        $this->db->join('non_gst_invoice_total', 'non_gst_invoice_total.number_fk=non_gst_invoice.invoice_number');
        if ($company_name) {
            $this->db->where('customer.company_name', $company_name);
        }
        $this->db->group_by('non_gst_invoice.invoice_number');
        $query = $this->db->get();
        return $query->result();
    }



    public function get_po_report_by_date1($from_date, $to_date, $uid)
    {
        //   print_r($to_date);die();
        $this->db->select('*,po.gst,sum(sgst + cgst + igst) as total_gst_amount, supplier.gst as customer_gst, sum(amount)as total_before_tax');
        $this->db->from('purchase_order po');
        $this->db->where('purchase_date >=', $from_date);
        $this->db->where('delivery_date <=', $to_date);
        // $this->db->where('po.uid', $uid);
        // $this->db->where('po_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=po.supplier_id');
        $this->db->join('po_total', 'po_total.number_fk=po.number');

        $this->db->group_by('po.number');
        $query = $this->db->get();
        //  var_dump($query->result());die();
        return $query->result();
    }



    public function get_po_report_by_date($month_year, $month, $uid)
    {
        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;

        $this->db->select('sum(price)as taxable, sum(igst) as integrated_tax, sum(cgst) as central_tax,sum(sgst) as state_tax');
        $this->db->from('invoice');
        $this->db->like('invoice_date', $newmonthyear_str, 'both');
        $this->db->where('gst', '0%');


        // $this->db->group_by('invoice_number');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_joborder_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select('joborder_total.*, customer.company_name, SUM(jo.quantity * i.cost_price) as total_cost');
        $this->db->from('joborder_total');
        $this->db->where('joborder_total.date >=', $from_date);
        $this->db->where('joborder_total.date <=', $to_date);
        $this->db->join('customer', 'customer.customer_id = joborder_total.customer_id_fk', 'left');
        $this->db->join('joborder jo', 'jo.number = joborder_total.number_fk', 'left');
        $this->db->join('inventory i', 'i.code = jo.product_name', 'left');
        $this->db->group_by('joborder_total.number_fk');
        $this->db->order_by('joborder_total.date', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_joborder_item_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select('jo.number, joborder_total.date, joborder_total.status, customer.company_name, SUM(jo.quantity * i.cost_price) as item_cost');
        $this->db->from('joborder jo');
        $this->db->where('joborder_total.date >=', $from_date);
        $this->db->where('joborder_total.date <=', $to_date);
        $this->db->join('joborder_total', 'joborder_total.number_fk = jo.number', 'left');
        $this->db->join('customer', 'customer.customer_id = jo.customer_id', 'left');
        $this->db->join('inventory i', 'i.code = jo.product_name', 'left');
        $this->db->group_by('jo.number');
        $this->db->order_by('joborder_total.date', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_joborder_items_by_number($joborder_number, $uid)
    {
        $this->db->select('jo.*, joborder_total.date, joborder_total.status, customer.company_name, inventory.item_name as product_description');
        $this->db->from('joborder_total');
        $this->db->where('joborder_total.number_fk', $joborder_number);
        $this->db->join('joborder jo', 'jo.number = joborder_total.number_fk', 'left');
        $this->db->join('customer', 'customer.customer_id = joborder_total.customer_id_fk', 'left');
        $this->db->join('inventory', 'inventory.code = jo.product_name', 'left');
        $this->db->order_by('jo.joborder_id', 'asc');

        $query = $this->db->get();
        // Debug: uncomment to see the query
        // echo $this->db->last_query(); die();
        return $query->result();
    }

    public function get_joborder_items_by_date($from_date, $to_date, $uid)
    {
        $this->db->select('joborder.*, joborder_total.date, joborder_total.status, customer.company_name, inventory.item_name as product_description');
        $this->db->from('joborder');
        $this->db->where('joborder_total.date >=', $from_date);
        $this->db->where('joborder_total.date <=', $to_date);
        $this->db->join('joborder_total', 'joborder_total.number_fk = joborder.number', 'left');
        $this->db->join('customer', 'customer.customer_id = joborder_total.customer_id_fk', 'left');
        $this->db->join('inventory', 'inventory.code = joborder.product_name', 'left');
        $this->db->order_by('joborder_total.date', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_create_sale_by_customer($from_date, $to_date, $uid)
    {

        $this->db->select('customer.customer_id,customer.fullname,customer.email,customer.mobile,customer.address,customer.gst,customer.company_name,
                invoice_total.number_fk,invoice_total.date,invoice_total.total,invoice_total.balance,invoice_total.payment_due_date,invoice_total.customer_po,invoice_total.po_date');
        $this->db->from('customer');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        //  $this->db->where('po.uid', $uid);
        // $this->db->where('po_total.uid', $uid);
        $this->db->join('invoice_total', 'customer.customer_id = invoice_total.customer_id_fk');

        //   $this->db->join('po_total', 'po_total.number_fk=po.number');
        $this->db->join('po_total', 'po_total.number_fk = invoice_total.number');

        //  $this->db->group_by('po.number');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_inventory_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select('*');
        $this->db->from('inventory');
        $this->db->where('date_added >=', $from_date);
        $this->db->where('date_added <=', $to_date);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_total()
    {
        $this->db->select("*, sum(total) as total, sum(expense) as expense");
        $this->db->from('location');

        $this->db->join('quotation', 'location.quotation_no = quotation.quotation_no');

        // $this->db->where('po_date >=', $from_date);
        // $this->db->where('po_date <=', $to_date);

        $this->db->join('provider', 'location.provider_id = provider.provider_id');

        $this->db->join('city', 'location.city_id = city.city_id');
        $this->db->group_by('quotation.q_id');

        $query = $this->db->get();

        $query->result();

        $in_process = 0;
        $total_in_process1 = 0;
        $pending = 0;
        $total_pending1 = 0;
        $completed = 0;
        $total_completed1 = 0;

        // print_r($query->result());
        //die();

        foreach ($query->result() as $value) {

            if ($value->work_status == 'In Process') {
                $in_process++;
                $total_in_process1 = $total_in_process1 + $value->total;
            }
            if ($value->work_status == 'Pending') {
                $pending++;
                $total_pending1 = $total_pending1 + $value->total;
            }
            if ($value->work_status == 'Completed') {
                $completed++;
                $total_completed1 = $total_completed1 + $value->total;
            }
        }

        $my_array = array(
            'in_process' => $in_process,
            'pending' => $pending,
            'completed' => $completed,
            'total_in_process1' => $total_in_process1,
            'total_pending1' => $total_pending1,
            'total_completed1' => $total_completed1
        );


        return $my_array;
    }
public function get_purchase_bill_report_by_date($from_date, $to_date, $user_id) {
    $this->db->select('pb.*, pbt.total, pbt.paid, pbt.balance, pbt.payment_due_date, pbt.delivery_date, s.company_name as supplier_name,s.s_code, s.fullname as supplier_fullname, s.pancard, s.email, s.mobile, s.address');
    $this->db->from('purchase_bill pb');
    $this->db->join('purchase_bill_total pbt', 'pb.number = pbt.number_fk', 'left');
    $this->db->join('supplier s', 'pb.supplier_id_fk = s.supplier_id', 'left');
    $this->db->where('pb.uid', $user_id);
    $this->db->where('pb.date >=', $from_date);
    $this->db->where('pb.date <=', $to_date);
    $this->db->order_by('pb.date', 'ASC');
    $this->db->order_by('pb.po_bill_id', 'ASC');
    
    $query = $this->db->get();
    return $query->result();
}

public function get_purchase_bill_report_by_hsn($from_date, $to_date, $user_id) {
    $this->db->select('pb.number,
        pb.date,
        s.company_name as supplier_name,
        pb.hsn_code,
        pb.gst_type,
        SUM(pb.amount) AS total_before_tax,
        SUM(pb.sgst) AS sgst,
        SUM(pb.cgst) AS cgst,
        SUM(pb.igst) AS igst,
        SUM(pb.sgst + pb.cgst + pb.igst) AS total_gst_amount,
        SUM(pb.amount + pb.sgst + pb.cgst + pb.igst) AS total,
        MAX(pbt.balance) AS balance', false);
    $this->db->from('purchase_bill pb');
    $this->db->join('purchase_bill_total pbt', 'pb.number = pbt.number_fk', 'left');
    $this->db->join('supplier s', 'pb.supplier_id_fk = s.supplier_id', 'left');
    $this->db->where('pb.uid', $user_id);
    $this->db->where('pb.date >=', $from_date);
    $this->db->where('pb.date <=', $to_date);
    $this->db->group_by(array('pb.number', 'pb.hsn_code'));
    $this->db->order_by('pb.date', 'ASC');
    $this->db->order_by('pb.number', 'ASC');
    
    $query = $this->db->get();
    return $query->result();
}

    public function get_total_by_date($result)
    {

        /* foreach ($result as $value) {
          $my_arra[] =  $value->quotation_no;
          }

          $this->db->select('*');
          $this->db->from('quotation');
          $this->db->like('work_status', 'In Process');
          // $this->db->where_in('quotation_no', $my_arra);
          $in_process = $this->db->count_all_results();

          $this->db->select('*');
          $this->db->from('quotation');
          $this->db->like('work_status', 'Pending');
          // $this->db->where_in('quotation_no', $my_arra);
          $pending = $this->db->count_all_results();

          $this->db->select('*');
          $this->db->from('quotation');
          $this->db->like('work_status', 'Completed');
          // $this->db->where_in('quotation_no', $my_arra);
          $completed = $this->db->count_all_results();

          $this->db->select_sum('total');
          $this->db->like('work_status', 'In Process');
          // $this->db->where_in('quotation_no', $my_arra);
          $query_in_process = $this->db->get('quotation'); // Produces: SELECT SUM(age) as age FROM members
          $total_in_process2 = $query_in_process->result();
          $total_in_process1 = $total_in_process2[0]->total;

          $this->db->select_sum('total');
          $this->db->like('work_status', 'Pending');
          //$this->db->where_in('quotation_no', $my_arra);
          $query_pending = $this->db->get('quotation'); // Produces: SELECT SUM(age) as age FROM members
          $total_pending2 = $query_pending->result();
          $total_pending1 = $total_pending2[0]->total;


          $this->db->select_sum('total');
          $this->db->like('work_status', 'Completed');
          //$this->db->where_in('quotation_no', $my_arra);
          $query_completed = $this->db->get('quotation'); // Produces: SELECT SUM(age) as age FROM members
          $total_completed2 = $query_completed->result();
          $total_completed1 = $total_completed2[0]->total; */

        $this->db->select("*");
        $this->db->from('location');

        $this->db->join('quotation', 'location.quotation_no = quotation.quotation_no');

        // $this->db->where('po_date >=', $from_date);
        // $this->db->where('po_date <=', $to_date);

        $this->db->join('provider', 'location.provider_id = provider.provider_id');

        $this->db->join('city', 'location.city_id = city.city_id');
        //$this->db->group_by('quotation.quotation_no');

        $query = $this->db->get();

        $query->result();

        $in_process = 0;
        $total_in_process1 = 0;
        $pending = 0;
        $total_pending1 = 0;
        $completed = 0;
        $total_completed1 = 0;

        // print_r($query->result());
        //die();

        foreach ($query->result() as $value) {

            if ($value->work_status == 'In Process') {
                $in_process++;
                $total_in_process1 = $total_in_process1 + $value->total;
            }
            if ($value->work_status == 'Pending') {
                $pending++;
                $total_pending1 = $total_pending1 + $value->total;
            }
            if ($value->work_status == 'Completed') {
                $completed++;
                $total_completed1 = $total_completed1 + $value->total;
            }
        }

        $total_Open1 = 1;
        $open = 1;

        $my_array = array(
            'in_process' => $in_process,
            'pending' => $pending,
            'completed' => $completed,
            'total_in_process1' => $total_in_process1,
            'total_pending1' => $total_pending1,
            'total_completed1' => $total_completed1
        );
        return $my_array;
    }

    public function get_total_by_date_quo($result)
    {

        foreach ($result as $value) {
            $my_arra[] = $value->number_fk;
        }

        $this->db->select('*');
        $this->db->from('invoice_total');
        $this->db->like('1', 'Draft');
        // $this->db->where_in('quotation_no', $my_arra);
        $draft = $this->db->count_all_results();

        $this->db->select('*');
        $this->db->from('invoice_total');
        $this->db->like('2', 'Sent');
        // $this->db->where_in('quotation_no', $my_arra);
        $sent = $this->db->count_all_results();

        $this->db->select('*');
        $this->db->from('invoice_total');
        $this->db->like('3', 'Viewed');
        // $this->db->where_in('quotation_no', $my_arra);
        $viewed = $this->db->count_all_results();

        $this->db->select('*');
        $this->db->from('invoice_total');
        $this->db->like('4', 'Approved');
        // $this->db->where_in('quotation_no', $my_arra);
        $approved = $this->db->count_all_results();

        $this->db->select('*');
        $this->db->from('invoice_total');
        $this->db->like('5', 'Rejected');
        // $this->db->where_in('quotation_no', $my_arra);
        $rejected = $this->db->count_all_results();

        $this->db->select('*');
        $this->db->from('invoice_total');
        $this->db->like('6', 'Canceled');
        // $this->db->where_in('quotation_no', $my_arra);
        $canceled = $this->db->count_all_results();
        $my_array = array(
            'Draft' => $draft,
            'Sent' => $sent,
            'Viewed' => $viewed,
            'Approved' => $approved,
            'Rejected' => $rejected,
            'Canceled' => $canceled
        );

        return $my_array;
    }

    public function get_itemwise_report_by_date($from_date, $to_date, $uid, $company_name, $product_name)
    {

        $this->db->select('*, SUM(amount) as total, SUM(sgst) as sgst,  SUM(cgst) as cgst, SUM(quantity) as quantity');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        // $this->db->where('invoice.uid', $uid);
        $this->db->where('invoice.product_name', $product_name);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number');
        if ($company_name) {
            $this->db->where('customer.company_name', $company_name);
        }
        $this->db->group_by('invoice.product_name');
        $query = $this->db->get();

        // var_dump($query->result());die();
        return $query->result();
    }

    public function get_customer_report_by_date($po_date1, $po_date2, $uid)
    {
        //print_r($po_date2);die();
        $this->db->select('* , SUM(balance) as balance');
        $this->db->from('customer');
        $this->db->where('invoice_total.date >=', $po_date1);
        $this->db->where('invoice_total.date <=', $po_date2);


        $this->db->join('invoice_total', 'customer.customer_id=invoice_total.customer_id_fk');
        //   $this->db->where('uid', $uid);
        $this->db->group_by('invoice_total.customer_id_fk');
        $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_supplier_report_by_date($po_date1, $po_date2)
    {
        $this->db->select('*, SUM(balance) as balance');
        $this->db->from('supplier');

        //   $this->db->where('uid', $uid);
        $this->db->where('po_total.date >=', $po_date1);
        $this->db->where('po_total.date <=', $po_date2);
        $this->db->join('po_total', 'supplier.supplier_id=po_total.supplier_id_fk');
        $this->db->group_by('supplier.supplier_id');
        $query = $this->db->get();
        //  var_dump($query->result());die();
        return $query->result();
    }
    //     public function get_supplier_report_by_date($uid) {
    //        //$this->db->select('*, SUM(balance) as balance');
    //        $this->db->from('vtechaccounting_supplier');
    //        //$this->db->join('po_total', 'supplier.supplier_id=po_total.supplier_id_fk');
    //     //   $this->db->where('uid', $uid);
    //       //$this->db->group_by('supplier.supplier_id');
    //
    //        $this->db->order_by("supplier_id", "desc");
    //        $query = $this->db->get();
    //        return $query->result();
    //    }

    

    public function get_customer_statement_report($from_date, $to_date, $company_name)
    {
        //print_r($from_date);die();
        $this->db->select('invoice.invoice_date,invoice.invoice_number, invoice_total.total,customer.company_name, invoice_total.balance');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number');
        $this->db->where('customer.customer_id', $company_name);

        $this->db->group_by('invoice.invoice_number');
        $this->db->order_by('invoice_date', 'asc');
        $query = $this->db->get();
        //var_dump($query);die();
        return $query->result();
    }

    public function get_gstr1_report($uid)
    {
        $this->db->select('* , SUM(invocie_pay_amount) as total_balance_amount');
        $this->db->from('invoice');
        //  $this->db->where('invoice.uid', $uid);
        //  $this->db->where('invoice_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'Left Join');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number', 'Right Join');
        $this->db->join('invocie_payment_gst', 'invocie_payment_gst.invoice_number_fk=invoice_total.number_fk', 'Left');
        $this->db->group_by('invoice.invoice_number');
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_item_report_by_customer($po_date1, $po_date2, $item_name)
    {
        // print_r($item_name);die();
        $this->db->select('product_name , SUM(quantity) as quantity , SUM(amount) as amount');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $po_date1);
        $this->db->where('invoice_date <=', $po_date2);
        $this->db->where('product_name', $item_name);
        // $this->db->where('customer_id', $customer_id);
        $this->db->group_by('product_name');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_sale_report_by_customer($po_date1, $po_date2, $uid)
    {
        //print_r($po_date2);die();
        $this->db->select('*, company_name , SUM(total) as total');
        $this->db->from('invoice_total');
        $this->db->where('invoice_total.date >=', $po_date1);
        $this->db->where('invoice_total.date <=', $po_date2);
        $this->db->join('customer', 'customer.customer_id=invoice_total.customer_id_fk');
        //$this->db->where('uid', $uid);
        $this->db->group_by('invoice_total.customer_id_fk');
        //        $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_purchase_report_by_supplier($po_date1, $po_date2)
    {


        $this->db->select('supplier.company_name, po_total.total');
        $this->db->from('po_total');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);
        $this->db->join('supplier', 'supplier.supplier_id=po_total.supplier_id_fk');
        // $this->db->where('uid', $uid);
        $this->db->group_by('po_total.supplier_id_fk');
        //      $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_discount_report($po_date1, $po_date2)
    {
        $this->db->select('company_name, SUM(discount) as discount');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $po_date1);
        $this->db->where('invoice_date <=', $po_date2);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id');
        //   $this->db->where('uid', $uid);
        $this->db->group_by('invoice.customer_id');
        //        $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_sale_tax_report($po_date1, $po_date2)
    {
        $this->db->select('company_name, SUM(sgst) as sgst, SUM(igst) as igst');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $po_date1);
        $this->db->where('invoice_date <=', $po_date2);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id');
        //   $this->db->where('uid', $uid);
        $this->db->group_by('invoice.customer_id');
        //        $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_purchase_tax_report($po_date1, $po_date2)
    {
        $this->db->select('company_name, SUM(sgst) as sgst, SUM(igst) as igst');
        $this->db->from('purchase_order');
        $this->db->where('purchase_date >=', $po_date1);
        $this->db->where('purchase_date <=', $po_date2);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id');
        //   $this->db->where('uid', $uid);
        $this->db->group_by('purchase_order.supplier_id');
        //        $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_gstr_report($po_date1, $po_date2)
    {
        $this->db->select('invoice.*, customer.company_name, customer.gst AS customer_gst_no, invoice_total.total_before_tax, invoice_total.total_gst_amount, invoice_total.total AS grand_total');
        $this->db->from('invoice');
        $this->db->where('invoice.invoice_date >=', $po_date1);
        $this->db->where('invoice.invoice_date <=', $po_date2);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'Left');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number', 'Left');
        $this->db->group_by('invoice.invoice_id');
        $this->db->order_by('invoice.invoice_id', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_gstr2_report($po_date1, $po_date2)
    {
        $this->db->select('
            pb.number AS invoice_number,
            pb.date   AS invoice_date,
            supplier.company_name,
            supplier.gst         AS customer_gst_no,
            SUM(pb.amount)      AS total_before_tax,
            SUM(pb.sgst)        AS sgst,
            SUM(pb.cgst)        AS cgst,
            SUM(pb.igst)        AS igst,
            SUM(pb.sgst + pb.cgst + pb.igst) AS total_gst_amount,
            purchase_bill_total.total      AS grand_total
        ', FALSE);
        $this->db->from('purchase_bill pb');
        $this->db->where('pb.date >=', $po_date1);
        $this->db->where('pb.date <=', $po_date2);
        $this->db->join('supplier', 'supplier.supplier_id=pb.supplier_id_fk', 'Left');
        $this->db->join('purchase_bill_total', 'purchase_bill_total.number_fk=pb.number', 'Left');
        $this->db->group_by('pb.number');
        $this->db->order_by('pb.date', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_loan_statement_report($po_date1, $po_date2)
    {
        $this->db->select('*');
        $this->db->from('loan_account');
        $this->db->where('loan_date >=', $po_date1);
        $this->db->where('loan_date <=', $po_date2);
        //$this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'Left Join');
        //  $this->db->group_by('loan_account.loan_id');
        $this->db->order_by("loan_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_sale_order_report($po_date1, $po_date2, $fullname)
    {
        //print_r($po_date2);die();
        $this->db->select('*');
        $this->db->from('salesorder');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);
        $this->db->join('customer', 'customer.customer_id=salesorder.customer_id');
        $this->db->join('salesorder_total', 'salesorder_total.number_fk=salesorder.number');
        $this->db->where('customer.fullname', $fullname);
        $this->db->group_by('salesorder.number');
        //        $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_purchase_order_report($po_date1, $po_date2, $supplier_name)
    {
        // print_r($fullname);die();
        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->where('purchase_date >=', $po_date1);
        $this->db->where('purchase_date <=', $po_date2);
        $this->db->where('supplier.fullname=', $supplier_name);

        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id');
        $this->db->join('po_total', 'po_total.number_fk=purchase_order.number');
        //   $this->db->where('uid', $uid);
        $this->db->group_by('purchase_order.number');
        //        $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_sale_order_item_report($po_date1, $po_date2, $fullname, $uid)
    {
        $this->db->select('*');
        $this->db->from('salesorder');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);
        $this->db->join('customer', 'customer.customer_id=salesorder.customer_id');
        $this->db->join('salesorder_total', 'salesorder_total.number_fk=salesorder.number');
        //   $this->db->group_by('salesorder.customer_id');
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_purchase_order_item_report($po_date1, $po_date2, $fullname)
    {
        //print_r($fullname);die();

        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->where('purchase_date >=', $po_date1);
        $this->db->where('purchase_date <=', $po_date2);
        $this->db->where('supplier.fullname =', $fullname);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id');
        $this->db->join('po_total', 'po_total.number_fk=purchase_order.number');
        //   $this->db->where('uid', $uid);
        $this->db->group_by('purchase_order.supplier_id');
        //        $this->db->order_by("customer_id", "desc");
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_profit_loss_report($po_date1, $po_date2)
    {

        $this->db->select('SUM(total) as po_total');
        $this->db->from('po_total');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);
        $query_po_total = $this->db->get();
        $query_po_total1 = $query_po_total->row_array();

        $this->db->select('SUM(total) as invoice_total');
        $this->db->from('invoice_total');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);
        $query_invoice_total = $this->db->get();
        $query_invoice_total1 = $query_invoice_total->row_array();

        $this->db->select('expense_category, SUM(expense_amount) as expense_total');
        $this->db->from('expense');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);
        $this->db->group_by('expense.expense_category');
        $query_expense_total = $this->db->get();
        $query_expense_total1 = $query_expense_total->result();
        $data1 = array(
            'po_total' => $query_po_total1['po_total'],
            'invoice_total' => $query_invoice_total1['invoice_total'],
            'expense_total_data' => $query_expense_total1
        );
        return $data1;
    }

    public function get_balance_sheet_report($po_date1, $po_date2)
    {


        //  print_r($po_date2);die();


        $this->db->select('SUM(total) as sundry_creditors');
        $this->db->from('purchase_bill_total');
        $this->db->where('po_date >=', $po_date1);
        $this->db->where('po_date <=', $po_date2);
        $query_purchase_bill_total = $this->db->get();
        //$query_invoice_total1 = $query_invoice_total->result();

        $query_purchase_bill_total1 = $query_purchase_bill_total->result();
        //  print_r($query_purchase_bill_total1);die();

        $this->db->select('SUM(total) as sundry_debtors');
        $this->db->from('invoice_total');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);
        $query_invoice_total = $this->db->get();
        $query_invoice_total1 = $query_invoice_total->row_array();

        $this->db->select('liabilities, sub_liabilities, SUM(current_balance) as sub_liabilities_total');
        $this->db->from('loan_account');
        $this->db->where('loan_date >=', $po_date1);
        $this->db->where('loan_date <=', $po_date2);
        $this->db->group_by('loan_account.sub_liabilities');
        $query_sub_liabilities_total = $this->db->get();
        $query_sub_liabilities_total1 = $query_sub_liabilities_total->result();
        $data1 = array(
            'sundry_creditors' => $query_purchase_bill_total1,
            'sundry_debtors' => $query_invoice_total1['sundry_debtors'],
            'expense_liabilities_data' => $query_sub_liabilities_total1
        );
        // print_r($data1);die();
        return $data1;
    }





    public function get_quotation_report_by_date($from_date, $to_date, $company_name, $uid)
    {
        $this->db->select('*,quotation.gst,sum(sgst + cgst + igst) as total_gst_amount,quotation_total.status,gst_type,customer.gst as customer_gst, sum(amount)as total_before_tax');
        $this->db->from('quotation');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        //  $this->db->where('invoice.uid', );
        //  $this->db->where('invoice_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=quotation.customer_id');
        $this->db->join('quotation_total', 'quotation_total.number_fk=quotation.number');
        if ($company_name) {
            $this->db->where('customer.company_name', $company_name);
        }
        $this->db->group_by('quotation.number');
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }
    public function purchase_order_item_report($po_date1, $po_date2, $uid)
    {
        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->where('purchase_date >=', $po_date1);
        $this->db->where('purchase_date <=', $po_date2);

        //   $this->db->group_by('salesorder.customer_id');
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }
     public function get_expenditure_report_by_date($po_date1, $po_date2, $exp_cat, $uid, $expense_mode = '', $filters = array())
    {
        $this->db->select('*');
        $this->db->from('expense');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);

        // Filter by specific category if selected
        if (!empty($exp_cat)) {
            $this->db->where('expense_category', $exp_cat);
        } elseif (!empty($expense_mode)) {
            // No category selected — filter all records belonging to this mode by prefix
            $prefix = '';
            if ($expense_mode === 'direct') {
                $prefix = 'Direct - ';
            } elseif ($expense_mode === 'indirect') {
                $prefix = 'Indirect - ';
            }
            if ($prefix !== '') {
                $this->db->like('expense_category', $prefix, 'after');
            }
        }

        if (!empty($filters['employee_name'])) {
            $this->db->like('employee_name', $filters['employee_name']);
        }

        if (!empty($filters['expense_month'])) {
            $this->db->where('expense_month', $filters['expense_month']);
        }

        if (!empty($filters['gst_class'])) {
            $this->db->where('gst_class', $filters['gst_class']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('status', $filters['status']);
        }

        $this->db->order_by('date', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_expenditure_item_report_by_date($po_date1, $po_date2, $exp_cat, $uid)
    {
        $this->db->select('*');
        $this->db->from('expense');
        $this->db->where('date >=', $po_date1);
        $this->db->where('date <=', $po_date2);
        $this->db->where('expense_category', $exp_cat);
        // $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }



    public function get_grn_report_by_date($date1, $date2, $uid) {
        $this->db->select('g.grn_number, g.date, g.po_number_fk, g.product_name, g.quantity, 
                          g.received_quantity, g.pending_quantity, g.price, g.received_quantity * g.price as amount, g.hsn_code, g.gst, 
                          s.company_name, s.fullname, 
                          g.received_quantity * g.price as total', FALSE);
        $this->db->from('grn g');
        $this->db->join('supplier s', 's.supplier_id = g.supplier_id', 'left');
        $this->db->where('g.uid', $uid);
        if (!empty($date1)) $this->db->where('STR_TO_DATE(g.date, "%d-%m-%Y") >=', $date1);
        if (!empty($date2)) $this->db->where('STR_TO_DATE(g.date, "%d-%m-%Y") <=', $date2);
        $this->db->order_by('g.date', 'ASC');
        $this->db->order_by('g.grn_number', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    public function get_pb_report_by_date1($from_date, $to_date, $uid)
    {
        // print_r($to_date);die();
        $this->db->select('*,po.gst, sum(sgst + cgst + igst) as total_gst_amount, supplier.gst as customer_gst, sum(amount)as total_before_tax');
        $this->db->from('purchase_bill po');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        // $this->db->where('po.uid', $uid);
        // $this->db->where('po_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=po.supplier_id_fk');
        $this->db->join('purchase_bill_total', 'purchase_bill_total.number_fk=po.number');

        $this->db->group_by('po.number');
        $query = $this->db->get();





        //         $sql = "SELECT * FROM vtechaccounting_purchase_bill pb  LEFT JOIN (
        //           SELECT SUM(sgst) as sgst_sum, pbs.number as pbnum FROM vtechaccounting_purchase_bill pbs 
        //           GROUP BY pbs.number
        //         ) as ams_sum on ams_sum.number = pb.number";

        // $result = $this->db->query($sql)->result_array();



        // var_dump($result);

        // die();





        return $query->result();
    }
}
