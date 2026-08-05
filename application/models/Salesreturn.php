<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Salesreturn extends CI_Model
{


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
    public function get_customer_count($uid)
    {
        $this->db->select('*');
        $this->db->from('customer');
        // $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->num_rows();
    }



    public function add_sales_return_total_amount($data_toatl_amount)
    {

        return $this->db->insert('sales_return_total', $data_toatl_amount);
    }





    public function delete_item_sales_return($sr_return_id, $uid)
    {
        $this->db->where('sr_return_id', $sr_return_id);
        //$this->db->where('uid', $uid);
        $this->db->delete('sales_return');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    /* start sales return*/

    public function get_sales_return($uid)
    {
        $this->db->select('sr_return_id, number, date, fullname, company_name, gst_type, status, total');
        $this->db->from('sales_return');
        // $this->db->where('purchase_order.uid', $uid);
        // $this->db->where('po_total.uid', $uid);
        $this->db->join('customer', 'customer.customer_id=sales_return.customer_id_fk', 'Left Join');
        $this->db->join('sales_return_total', 'sales_return_total.number_fk=sales_return.number', 'Left Join');
        $this->db->group_by('sales_return.number');
        $this->db->order_by("sales_return.sr_return_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_sales_return_purmonthyearwise_record($month_year, $uid)
    {

        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        $this->db->select('sr_return_id, number, date, fullname, company_name, gst_type, status, total');
        $this->db->from('sales_return');
        $this->db->like('date', $newmonthyear_str, 'both');
        $this->db->join('customer', 'customer.customer_id=sales_return.customer_id_fk', 'Left Join');
        $this->db->join('sales_return_total', 'sales_return_total.number_fk=sales_return.number', 'Left Join');
        $this->db->group_by('sales_return.number');
        $this->db->order_by("sales_return.sr_return_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_last_sales_return_number($uid)
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
        $this->db->from('sales_return_total');
        $this->db->like('number_fk', $financial_year, "before");
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();

        $result = $query->row();

        return $result->id;
        //      
        //        $this->db->select('COUNT(uid)');
        //        $this->db->from('sales_return_total');
        //        //$this->db->where('uid', $uid);
        //        $query = $this->db->get();
        //        $result = $query->row_array();
        //        return $result; 3s 

    }

    public function delete_sales_return_by_po_return_number($po_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number', $po_number);
        $this->db->delete('sales_return');
        if ($this->db->affected_rows() >= '1') {
            $this->db->where('number_fk', $po_number);
            $this->db->delete('sales_return_total');
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

    public function get_sales_return_data_group_by($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('sales_return');
        $this->db->where('sales_return.uid', $uid);
        $this->db->where('number', $number);
        $this->db->join('customer', 'customer.customer_id=sales_return.customer_id_fk', 'Left Join');
        $this->db->join('sales_return_total', 'sales_return_total.number_fk=sales_return.number', 'Right Join');
        $this->db->group_by('sales_return.number');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_sales_return_data($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('sales_return');
        $this->db->where('uid', $uid);
        $this->db->where('number', $number);
        $query = $this->db->get();
        return $query->result();
    }


    public function edit_sales_return_total_amount($data_toatl_amount, $number)
    {

        $this->db->where('number_fk', $number);
        $this->db->update('sales_return_total', $data_toatl_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }




    /*end of sales bill*/

    public function delete_sales_return_item($sr_return_id)
    {
        $this->db->where('sr_return_id', $sr_return_id);
        $this->db->delete('sales_return');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
