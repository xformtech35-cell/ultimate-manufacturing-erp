<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Supplier extends CI_Model
{

    public function add_supplier($data_supplier)
    {
        return $this->db->insert('supplier', $data_supplier);
    }

    public function add_stock($data_purchase_stock, $inventor_stock)
    {
        $stock = $inventor_stock + $data_purchase_stock['instock'];
        $date_modified = date('d-m-Y');

        $data_stock = array('stock' => $stock, 'date_modified' => $date_modified);

        $inventory_id = $data_purchase_stock['inventory_id_fk'];
        $this->db->where('inventory_id', $inventory_id);
        $this->db->update('inventory', $data_stock);

        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }

        //        if ($this->db->affected_rows() == '1') {
        //            return $this->db->insert('purchase_stock', $data_purchase_stock);
        //        } else {
        //            return FALSE;
        //        }
    }

    public function supplier_check($mobile, $uid)
    {
        $this->db->select('company_name');
        $this->db->from('supplier');
        //$this->db->where('uid', $uid);
        $this->db->where('company_name', $mobile);
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

    public function get_item_id($item_name, $uid)
    {
        $this->db->select('inventory_id, stock, inventory_qty');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $this->db->where('code', $item_name);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_supplier_id_by_name($supplier_name, $uid)
    {
        $this->db->select('supplier_id');
        $this->db->from('supplier');
        //$this->db->where('uid', $uid);
        $this->db->where('fullname', $supplier_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_supplier_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('supplier');
        $this->db->where('supplier_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function delete_supplier_by_id($id)
    {
        $this->db->where('supplier_id', $id);
        $this->db->delete('supplier');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_supplier_count($uid)
    {
        $this->db->select('*');
        $this->db->from('supplier');
        // $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_po_count($uid)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('po_total.date >=', $fy_from);
            $this->db->where('po_total.date <=', $fy_to);
        }
        $this->db->select('*');
        $this->db->from('po_total');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_po_total_amount($uid)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('po_total.date >=', $fy_from);
            $this->db->where('po_total.date <=', $fy_to);
        }
        $this->db->select_sum('total');
        $this->db->from('po_total');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        $result = $query->row();
        return $result->total ?? 0;
    }

    public function edit_supplier($data_supplier, $supplier_id, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('supplier_id', $supplier_id);
        $this->db->update('supplier', $data_supplier);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_supplier($uid, $limit = 1000)
    {
        $this->db->select('*');
        $this->db->from('supplier');
        //   $this->db->where('uid', $uid);
        $this->db->order_by("supplier_id", "desc");
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query->result();
    }
    public function get_supplier_names($keyword)
    {
        $this->db->distinct();
        $this->db->select("fullname");
        $this->db->like('fullname', $keyword, 'after');
        $query = $this->db->get('supplier');
        return $query->result();
    }
    public function get_purchase_stock($uid)
    {
        $this->db->select('*');
        //stock,instock,oldstock,purchase_date,item_name,company_name,fullname
        $this->db->from('purchase_stock');
        $this->db->where('purchase_stock.uid', $uid);
        $this->db->join('inventory', 'inventory.inventory_id=purchase_stock.inventory_id_fk');
        $this->db->join('supplier', 'supplier.supplier_id=purchase_stock.supplier_id_fk');
        $this->db->order_by("purchase_stock.purchase_date", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function update_sold_stock($item_name, $minus_total_quantity, $uid)
    {
        $this->db->where('item_name', $item_name);
        //$this->db->where('uid', $uid);
        $this->db->update('inventory', $minus_total_quantity);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_purchase_order($uid)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31';
            $this->db->where('po_total.date >=', $fy_from);
            $this->db->where('po_total.date <=', $fy_to);
        }

        $this->db->select('* ,number, SUM(purchase_pay_amount) as total_balance_amount');
        $this->db->from('purchase_order');
        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id', 'Left Join');
        $this->db->join('po_total', 'po_total.number_fk=purchase_order.number', 'Right Join');
        $this->db->join('purchase_payment_gst', 'purchase_payment_gst.purchase_number_fk=po_total.number_fk', 'Left');
        $this->db->group_by('purchase_order.number');
        $this->db->order_by("purchase_order.po_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_supplier_name()
    {
        $this->db->select('*');
        $this->db->from('supplier');
        $query = $this->db->get();

        return $query->result();
    }


    public function get_supplier_state_code($vendor_id)
    {
        $this->db->select('state_code');
        $this->db->from('supplier');
        $this->db->where('supplier_id', $vendor_id);

        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_total_amount($data_toatl_amount)
    {
        return $this->db->insert('po_total', $data_toatl_amount);
    }

    public function add_purchase_bill_total_amount($data_toatl_amount)
    {
        //  print_r($data_toatl_amount);die();
        return $this->db->insert('purchase_bill_total', $data_toatl_amount);
    }
    public function add_purchase_return_total_amount($data_toatl_amount)
    {
        return $this->db->insert('purchase_return_total', $data_toatl_amount);
    }
    public function get_last_po_number($uid)
    {
        if (date('m') <= 3) {
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        // Fetch all DISTINCT PO numbers for this financial year and user
        // Handles both old format (PO/0048/26-27) and new format (PO/26-27/0005/...)
        $query = $this->db
            ->select('DISTINCT number_fk', FALSE)
            ->from('po_total')
            ->like('number_fk', $financial_year)   // matches both formats
            ->where('uid', $uid)
            ->get();

        $rows = $query->result_array();

        $max_seq = 0;
        foreach ($rows as $row) {
            $nfk = $row['number_fk'];
            // New format: PO/26-27/0005/(...)  — extract 3rd segment
            if (preg_match('/^PO\/\d{2}-\d{2}\/(\d+)/i', $nfk, $m)) {
                $seq = (int)$m[1];
            }
            // Old format: PO/0048/26-27 or PO/0048/MON/26-27 — extract 2nd segment
            elseif (preg_match('/^PO\/(\d+)\//i', $nfk, $m)) {
                $seq = (int)$m[1];
            } else {
                continue;
            }
            if ($seq > $max_seq) {
                $max_seq = $seq;
            }
        }

        return $max_seq; // caller does +1 and sprintf("%04d", ...)
    }

    public function get_po_data($number, $uid)
    {
        $this->db->select('po.*, inventory.item_name');
        $this->db->from('purchase_order po');
        $this->db->join('inventory', 'inventory.code=po.product_name', 'Left Join');
        //$this->db->where('uid', $uid);
        $this->db->where('po.number', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_po_data_group_by($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->where('number', $number);
        // $this->db->where('purchase_order.uid', $uid);
        // $this->db->where('po_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id', 'Left Join');
        $this->db->join('po_total', 'po_total.number_fk=purchase_order.number', 'Right Join');
        $this->db->group_by('purchase_order.number');
        $query = $this->db->get();

        return $query->row_array();
    }
    public function edit_total_amount($data_toatl_amount, $number)
    {

        $this->db->where('number_fk', $number);
        $this->db->update('po_total', $data_toatl_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_po_by_po_number($po_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number', $po_number);
        $this->db->delete('purchase_order');
        if ($this->db->affected_rows() >= '1') {
            $this->db->where('number_fk', $po_number);
            $this->db->delete('po_total');
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

    public function get_supplier_email($po_number, $uid)
    {
        $this->db->select('supplier.email, supplier.mobile as mobile');
        $this->db->from('purchase_order');
        $this->db->where('number', $po_number);
        $this->db->where('purchase_order.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id', 'Left Join');
        $this->db->order_by('purchase_order.po_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_purchase_bill_supplier_email($bill_number, $uid)
    {
        $this->db->select('supplier.email, supplier.mobile as mobile');
        $this->db->from('purchase_bill');
        $this->db->where('number', $bill_number);
        $this->db->where('purchase_bill.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_bill.supplier_id_fk', 'Left Join');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_purchase_return_supplier_email($return_number, $uid)
    {
        $this->db->select('supplier.email, supplier.mobile as mobile');
        $this->db->from('purchase_return');
        $this->db->where('number', $return_number);
        $this->db->where('purchase_return.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_return.supplier_id_fk', 'Left Join');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete_item($invoice_id, $uid)
    {
        $this->db->where('po_id', $invoice_id);
        //$this->db->where('uid', $uid);
        $this->db->delete('purchase_order');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_item_purchase_bill($po_bill_id, $uid)
    {
        $this->db->where('po_bill_id', $po_bill_id);
        //$this->db->where('uid', $uid);
        $this->db->delete('purchase_bill');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_item_purchase_return($po_return_id, $uid)
    {
        $this->db->where('po_return_id', $po_return_id);
        //$this->db->where('uid', $uid);
        $this->db->delete('purchase_return');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function inventory_id_fk_check($inventory_id, $uid)
    {
        $this->db->select('inventory_id_fk');
        $this->db->from('purchase_stock');
        //$this->db->where('uid', $uid);
        $this->db->where('inventory_id_fk', $inventory_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_purchase_stock_by_id($id)
    {
        $this->db->where('purchase_stock_id', $id);
        $this->db->delete('purchase_stock');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_row_item_stock_by_id($id)
    {
        $this->db->where('raw_item_stock_id', $id);
        $this->db->delete('raw_items_stock');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_purchase_payment_histroy()
    {
        $this->db->select('*');
        $this->db->from('purchase_payment_history');
        $this->db->join('supplier', 'supplier.supplier_id=purchase_payment_history.supplier_id_fk');
        $this->db->order_by("payment_date", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function add_purchase_payment_histroy($data_purchase_payment_histroy)
    {
        return $this->db->insert('purchase_payment_history', $data_purchase_payment_histroy);
    }

    public function delete_purchase_payment_histroy($id)
    {
        $this->db->where('purchase_payment_id', $id);
        $this->db->delete('purchase_payment_history');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function get_last_supplier_code($uid)
    {
        $this->db->select('MAX(CAST(s_code AS UNSIGNED)) as max_code');
        $this->db->from('supplier');
        $this->db->where('uid', $uid);
        $this->db->where('s_code !=', '');  // Exclude empty codes
        $query = $this->db->get();
        $result = $query->row_array();
        $max_code = $result['max_code'];
        
        // If no suppliers exist for this user, start from 0
        if (empty($max_code) || $max_code == null) {
            return 0;
        }
        
        // Return the max code as integer so controller can add 5000 to get next code
        return intval($max_code);
    }

    public function get_monthyearwise_record($month_year, $uid)
    {
        // FIX: The original code was trying to split "M-Y" format incorrectly
        // We're now passing "Y-m" format directly

        $this->db->select('* ,number, SUM(purchase_pay_amount) as total_balance_amount');
        $this->db->from('purchase_order');

        // Use LIKE with YYYY-MM format directly
        $this->db->like('purchase_date', $month_year, 'after');

        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id', 'Left Join');
        $this->db->join('po_total', 'po_total.number_fk=purchase_order.number', 'Right Join');
        $this->db->join('purchase_payment_gst', 'purchase_payment_gst.purchase_number_fk=po_total.number_fk', 'Left');
        $this->db->group_by('purchase_order.number');
        $this->db->order_by("purchase_order.po_id", "desc");

        $query = $this->db->get();
        return $query->result();
    }

    public function get_po_number_from_po_total($id, $uid)
    {
        $this->db->select('number_fk');
        $this->db->from('po_total');
        $this->db->where('id', $id);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }


    public function get_po_number_from_po_total12($uid)
    {
        $this->db->select('*');
        $this->db->from('purchase_bill');
        //  $this->db->where('id', $id);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_purchase_bill($uid)
    {

        $this->db->select('* , SUM(purchase_pay_amount) as total_balance_amount');
        $this->db->from('purchase_bill');
        //  $this->db->where('invoice.uid', $uid);
        //  $this->db->where('invoice_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_bill.supplier_id_fk', 'Left Join');
        $this->db->join('purchase_bill_total', 'purchase_bill_total.number_fk=purchase_bill.number', 'Left Join');
        $this->db->join('purchase_payment_gst', 'purchase_payment_gst.purchase_number_fk=purchase_bill_total.number_fk', 'Left');
        $this->db->group_by('purchase_bill.number');
        $this->db->order_by("purchase_bill.po_bill_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_purchase_bill_purmonthyearwise_record($month_year, $uid)
    {

        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        $this->db->select('* , SUM(purchase_pay_amount) as total_balance_amount');
        $this->db->from('purchase_bill');
        $this->db->like('date', $newmonthyear_str, 'both');
        $this->db->join('supplier', 'supplier.supplier_id=purchase_bill.supplier_id_fk', 'Left Join');
        $this->db->join('purchase_bill_total', 'purchase_bill_total.number_fk=purchase_bill.number', 'Left Join');
        $this->db->join('purchase_payment_gst', 'purchase_payment_gst.purchase_number_fk=purchase_bill_total.number_fk', 'Left');
        $this->db->group_by('purchase_bill.number');
        $this->db->order_by("purchase_bill.po_bill_id", "desc");
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }

    public function get_last_purchase_bill_number($uid)
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
        $this->db->from('purchase_bill_total');
        $this->db->like('number_fk', $financial_year, "before");
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();

        $result = $query->row();

        return $result->id;
    }

    public function delete_purchase_bill_by_po_bill_number($po_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number', $po_number);
        $this->db->delete('purchase_bill');
        if ($this->db->affected_rows() >= '1') {
            $this->db->where('number_fk', $po_number);
            $this->db->delete('purchase_bill_total');
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

    public function get_purchase_bill_data_group_by($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('purchase_bill');
        $this->db->where('number', $number);
        // $this->db->where('purchase_order.uid', $uid);
        // $this->db->where('po_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_bill.supplier_id_fk', 'Left Join');
        $this->db->join('purchase_bill_total', 'purchase_bill_total.number_fk=purchase_bill.number', 'Left Join');
        $this->db->group_by('purchase_bill.number');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_purchase_bill_data($number, $uid)
    {
        $this->db->select('pb.*, inventory.item_name');
        $this->db->from('purchase_bill pb');
        //$this->db->where('uid', $uid);
        $this->db->where('pb.number', $number);
        $this->db->join('inventory', 'inventory.code=pb.product_name', 'Left Join');
        $query = $this->db->get();
        return $query->result();
    }


    public function edit_purchase_bill_total_amount($data_toatl_amount, $number)
    {

        $this->db->where('number_fk', $number);
        $this->db->update('purchase_bill_total', $data_toatl_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_convert_purchase_bill_data($number, $uid)
    {
        $this->db->select('
            purchase_order.supplier_id,
            purchase_order.number,
            purchase_order.purchase_date,
            purchase_order.delivery_date,
            purchase_order.product_name,
            purchase_order.quantity,
            purchase_order.discount,
            purchase_order.hsn_code,
            purchase_order.unit,
            purchase_order.gst,
            purchase_order.sgst,
            purchase_order.cgst,
            purchase_order.igst,
            purchase_order.gst_type,
            purchase_order.price,
            purchase_order.amount,
            purchase_order.description,
            supplier.fullname,
            supplier.s_code,
            po_total.total,
            po_total.status,
            po_total.date
        ');
        $this->db->from('purchase_order');
        $this->db->where('number', $number);
        $this->db->where('purchase_order.uid', $uid);
        $this->db->where('po_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id', 'Left Join');
        $this->db->join('po_total', 'po_total.number_fk=purchase_order.number', 'Left Join');
        $query = $this->db->get();
        //var_dump($query->result());die();
        return $query->result();
    }













    /* start purchase return*/

    public function get_purchase_return($uid)
    {
        $this->db->select('po_return_id, number, date, fullname, supplier.s_code, gst_type, status, total');
        $this->db->from('purchase_return');
        // $this->db->where('purchase_order.uid', $uid);
        // $this->db->where('po_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_return.supplier_id_fk', 'Left Join');
        $this->db->join('purchase_return_total', 'purchase_return_total.number_fk=purchase_return.number', 'Left Join');
        $this->db->group_by('purchase_return.number');
        $this->db->order_by("purchase_return.po_return_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_purchase_return_purmonthyearwise_record($month_year, $uid)
    {

        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        $this->db->select('po_return_id, number, date, fullname, supplier.s_code, gst_type, status, total');
        $this->db->from('purchase_return');
        $this->db->like('date', $newmonthyear_str, 'both');
        $this->db->join('supplier', 'supplier.supplier_id=purchase_return.supplier_id_fk', 'Left Join');
        $this->db->join('purchase_return_total', 'purchase_return_total.number_fk=purchase_return.number', 'Left Join');
        $this->db->group_by('purchase_return.number');
        $this->db->order_by("purchase_return.po_return_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_last_purchase_return_number($uid)
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
        $this->db->from('purchase_return_total');
        $this->db->like('number_fk', $financial_year, "before");
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();

        $result = $query->row();

        return $result->id;

        //         
        //        $this->db->select('COUNT(uid)');
        //        $this->db->from('purchase_return_total');
        //        //$this->db->where('uid', $uid);
        //        $query = $this->db->get();
        //        $result = $query->row_array();
        //        return $result;

    }

    public function delete_purchase_return_by_po_return_number($po_number, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('number', $po_number);
        $this->db->delete('purchase_return');
        if ($this->db->affected_rows() >= '1') {
            $this->db->where('number_fk', $po_number);
            $this->db->delete('purchase_return_total');
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

    public function get_purchase_return_data_group_by($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('purchase_return');
        $this->db->where('number', $number);
        // $this->db->where('purchase_order.uid', $uid);
        // $this->db->where('po_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_return.supplier_id_fk', 'Left Join');
        $this->db->join('purchase_return_total', 'purchase_return_total.number_fk=purchase_return.number', 'Right Join');
        $this->db->group_by('purchase_return.number');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_purchase_return_data($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('purchase_return');
        //$this->db->where('uid', $uid);
        $this->db->where('number', $number);
        $query = $this->db->get();
        return $query->result();
    }


    public function edit_purchase_return_total_amount($data_toatl_amount, $number)
    {

        $this->db->where('number_fk', $number);
        $this->db->update('purchase_return_total', $data_toatl_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_convert_purchase_return_data($number, $uid)
    {
        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->where('number', $number);
        $this->db->where('purchase_order.uid', $uid);
        $this->db->where('po_total.uid', $uid);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_order.supplier_id', 'Left Join');
        $this->db->join('po_total', 'po_total.number_fk=purchase_order.number', 'Right Join');
        $query = $this->db->get();
        return $query->result();
    }
    public function edit_purchase_payment($data_payment, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('po_total', $data_payment);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_purchase_payment_history_data($purchase_number, $uid)
    {
        $this->db->select('*');
        $this->db->from('purchase_payment_gst');
        $this->db->where('purchase_number_fk', $purchase_number);
        //$this->db->where('uid', $uid);
        $this->db->order_by("purchase_pay_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }
    public function get_status_by_purchaseid($purchase_number, $uid)
    {
        $this->db->select('status');
        $this->db->from('po_total');
        //$this->db->where('uid', $uid);
        $this->db->where('number_fk', $purchase_number);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_purchase_number_from_purchase_total($id, $uid)
    {
        $this->db->select('number_fk');
        $this->db->from('po_total');
        $this->db->where('id', $id);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_previous_balance_purchase($number, $uid)
    {
        $this->db->select('paid,total,balance , SUM(purchase_pay_amount) as total_balance_amount');
        $this->db->from('po_total');
        // $this->db->where('po_total.uid', $uid);
        $this->db->where('number_fk', $number);
        $this->db->join('purchase_payment_gst', 'purchase_payment_gst.purchase_number_fk=po_total.number_fk', 'Left');
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_pay_gst_purchase_amount($number, $uid)
    {
        $this->db->select('purchase_pay_amount');
        $this->db->from('purchase_payment_gst');
        //$this->db->where('uid', $uid);
        $this->db->where('purchase_number_fk', $number);
        $query = $this->db->get();
        return $query->result();
    }
    public function pay_gst_purchase_amount($purchase_payment_gst)
    {

        return $this->db->insert('purchase_payment_gst', $purchase_payment_gst);
    }
    public function get_purchase_payment_details($id)
    {
        $this->db->select('balance, number_fk, total, supplier_id_fk');
        $this->db->from('po_total');
        $this->db->where('id', $id);
        $query = $this->db->get();
        // var_dump($query->row_array());die();
        return $query->row_array();
    }

    public function get_purchase_bill_payment_details($id)
    {
        $this->db->select('*');
        $this->db->from('purchase_bill_total');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /*function for enter payment of purchase bill*/
    public function edit_purchase_bill_payment($data_payment, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('purchase_bill_total', $data_payment);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_previous_balance_purchase_bill($number, $uid)
    {
        $this->db->select('paid,total,balance , SUM(purchase_pay_amount) as total_balance_amount');
        $this->db->from('purchase_bill_total');
        // $this->db->where('po_total.uid', $uid);
        $this->db->where('number_fk', $number);
        $this->db->join('purchase_bill_payment_gst', 'purchase_bill_payment_gst.purchase_number_fk=purchase_bill_total.number_fk', 'Left');
        $query = $this->db->get();
        return $query->row_array();
    }



    public function get_pay_gst_purchase_amount_bill($number, $uid)
    {
        $this->db->select('purchase_pay_amount');
        $this->db->from('purchase_bill_payment_gst');
        //$this->db->where('uid', $uid);
        $this->db->where('purchase_number_fk', $number);
        $query = $this->db->get();
        return $query->result();
    }


    public function pay_gst_purchase_amount_bill($purchase_payment_gst)
    {

        return $this->db->insert('purchase_bill_payment_gst', $purchase_payment_gst);
    }

    /*end of purchase bill*/

    public function delete_purchase_return_item($po_return_id)
    {
        $this->db->where('po_return_id', $po_return_id);
        $this->db->delete('purchase_return');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function get_payment_out($uid)
    {
        $this->db->select('*');
        $this->db->from('payment_out');
        $this->db->join('supplier', 'supplier.supplier_id=payment_out.payment_supplier_id', 'Left Join');
        $this->db->order_by("payment_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_total_balance_payment_in($payment_supplier_id)
    {
        $this->db->select_sum('payment');
        $this->db->from('payment_out');
        $this->db->where('payment_supplier_id', $payment_supplier_id);
        $query = $this->db->get();
        return $query->row()->payment;
    }

    public function print_voucher_out($invocie_pay_id, $uid)
    {
        $this->db->select('*');
        $this->db->from('payment_out');
        $this->db->where('payment_id', $invocie_pay_id);

        $this->db->join('supplier', 'supplier.supplier_id=payment_out.payment_supplier_id', 'Left Join');

        $query = $this->db->get();
        return $query->row_array();
    }


    public function get_company_name_with_bal($uid)
    {
        $this->db->select('supplier_id, company_name, s_code');
        $this->db->from('supplier');
        $this->db->order_by("company_name", "asc");
        $query = $this->db->get();

        $arr = $query->result();

        $arr1 = array();
        foreach ($arr as $key) {
            $this->db->select_sum('payment');
            $this->db->from('payment_out');
            $this->db->where('payment_supplier_id', $key->supplier_id);
            $query1 = $this->db->get();
            if ($query1->row()->payment == '' || $query1->row()->payment == 0 ||  $query1->row()->payment == NULL) {
                $arr1[] = array("company_name" => $key->company_name, "payment" => "0", "supplier_id" => $key->supplier_id, "s_code" => $key->s_code ?? '');
            } else {
                $arr1[] = array("company_name" => $key->company_name, "payment" => $query1->row()->payment, "supplier_id" => $key->supplier_id, "s_code" => $key->s_code ?? '');
            }
        }
        return $arr1;
    }

    public function get_pending_purchase_payment($id, $uid)
    {
        $this->db->select('number_fk, po_date, total, paid, balance, status, id');
        $this->db->from('purchase_bill_total');
        $this->db->where('supplier_id_fk', $id);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }


    public function getPaymentById($id)
    {
        $this->db->select('*');
        $this->db->from('payment_out');
        $this->db->where('payment_id', $id);
        $query = $this->db->get();

        return $query->row_array();
    }


    public function checkPoNumber($id)
    {
        //  echo $id;
        $this->db->select('purchase_order_number');
        $this->db->from('purchase_booked');
        $this->db->where('purchase_order_number', $id);
        $query = $this->db->get();



        return $query->row_array();
    }

    public function add_po_number($po_number)
    {
        $data = array('purchase_order_number' => $po_number);
        return $this->db->insert('purchase_booked', $data);
    }

    public function get_po_status($po_number, $user_id)
    {




        // echo $po_number; 

        // die();
        $this->db->select('status');
        $this->db->from('po_total');
        $this->db->where('number_fk', $po_number);
        // $this->db->where('uid', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result['status'];
        }

        return 0; // Default to 0 if not found
    }

    /**
     * Get PO amendments
     */
    public function get_po_amendments($po_number, $user_id)
    {
        $this->db->where('po_number', $po_number)
            ->or_where('original_po_number', $po_number);
        $this->db->order_by('revision_number', 'ASC');
        return $this->db->get('po_amendments')->result_array();
    }

    /**
     * Check if PO has pending amendments
     */
    public function has_pending_amendments($po_number, $user_id)
    {
        $this->db->where('(po_number = ? OR original_po_number = ?)', array($po_number, $po_number))
            ->where('status', 'pending_approval');
        $count = $this->db->count_all_results('po_amendments');
        return $count > 0;
    }

    /**
     * Get latest revision number
     */
    public function get_next_revision_number($po_number)
    {
        $this->db->select_max('revision_number');
        $this->db->where('original_po_number', $po_number);
        $result = $this->db->get('po_total')->row_array();

        return ($result['revision_number'] ?: 0) + 1;
    }



    public function get_po_revisions($original_po_number)
    {
        $this->db->select('*');
        $this->db->from('po_total');
        $this->db->where('original_po_number', $original_po_number);
        $this->db->or_where('number_fk', $original_po_number);
        $this->db->order_by('revision_number', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function bulk_insert_vendors($vendors)
    {
        return $this->db->insert_batch('supplier', $vendors);
    }
}
