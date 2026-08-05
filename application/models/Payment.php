<?php

Class Payment extends CI_Model {

    public function get_payment_history_details($uid) {
        $this->db->select('*');
        $this->db->from('invocie_payment_gst');
        //$this->db->where('uid', $uid);
        $this->db->order_by("invocie_pay_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_non_gst_payment_history_details($uid) {
        $this->db->select('*');
        $this->db->from('invocie_payment_non_gst');
        //$this->db->where('uid', $uid);
        $this->db->order_by("ng_invocie_pay_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_payment_by_id($id) {
        $this->db->select('*');
        $this->db->from('invocie_payment_gst');
        $this->db->where('invocie_pay_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit_payment_history($data_payment, $invocie_pay_id) {
        $this->db->where('invocie_pay_id', $invocie_pay_id);
        $this->db->update('invocie_payment_gst', $data_payment);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_paid_amount_sum($invoice_number_fk, $uid) {
        $this->db->select('SUM(invocie_pay_amount) as total_balance_amount');
        $this->db->from('invocie_payment_gst');
        //$this->db->where('uid', $uid);
        $this->db->where('invoice_number_fk', $invoice_number_fk);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function edit_invoice_balance_amount($data_invoice_balance, $invoice_number_fk, $uid) {
        $this->db->where('number_fk', $invoice_number_fk);
        //$this->db->where('uid', $uid);
        $this->db->update('invoice_total', $data_invoice_balance);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_current_balance_details($invoice_number_fk, $uid) {
        $this->db->select('invocie_pay_amount, SUM(invocie_pay_amount) as total_paid_amount ,total, balance');
        $this->db->from('invocie_payment_gst');
        $this->db->where('invoice_number_fk', $invoice_number_fk);
        $this->db->where('invocie_payment_gst.uid', $uid);
        $this->db->join('invoice_total', 'invoice_total.number_fk=invocie_payment_gst.invoice_number_fk');
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function get_gst_ledger($from_date, $to_date, $company_name) {
      //  echo $company_name;
   //  print_r($to_date);die();
        $this->db->select('*,invoice.invoice_date,invoice.invoice_number, invoice_total.total,customer.company_name,customer.fullname, invoice_total.balance');
        $this->db->from('invoice');
        //echo "$invoice.invoice_date"; die();
        $this->db->where('invoice.invoice_date >=', $from_date);
        $this->db->where('invoice.invoice_date <=', $to_date);
        $this->db->where('customer.customer_id', $company_name);
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number');
        

        $this->db->group_by('invoice.invoice_number');
        $this->db->order_by('invoice_date', 'asc');
        $query = $this->db->get();
        
  //var_dump($query->result());die();
        return $query->result();
              

    }

    public function get_payment_ledger($from_date, $to_date, $company_name) {

  // print_r($company_name);die();
        //$this->db->select('invocie_payment_gst.invoice_pay_date,invocie_payment_gst.payment_type, invocie_payment_gst.invocie_pay_amount, invoice.invoice_number, customer.company_name');
        $this->db->select('*');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->where('customer.customer_id', $company_name);
        
        $this->db->join('invocie_payment_gst', 'invocie_payment_gst.customer_id_fk=invoice.customer_id');
        $this->db->join('customer', 'customer.customer_id=invocie_payment_gst.customer_id_fk');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number');

    //  $this->db->where('customer.company_name', $company_name);
        $this->db->group_by('invocie_pay_id');
        $this->db->order_by('invocie_payment_gst.invoice_pay_date', 'desc');
       $this->db->order_by('invoice_total.balance', 'desc'); 
        $query = $this->db->get();
    //    var_dump($query->result());die();
        return $query->result();
    }

    public function get_non_gst_ledger($from_date, $to_date, $company_name) {
        $this->db->select('*');
        $this->db->from('non_gst_invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->join('customer', 'customer.customer_id=non_gst_invoice.customer_id');
        $this->db->join('non_gst_invoice_total', 'non_gst_invoice_total.number_fk=non_gst_invoice.invoice_number');
        $this->db->where('customer.customer_id', $company_name);
        $this->db->group_by('non_gst_invoice.invoice_number');
        $this->db->order_by('invoice_date', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_non_gst_payment_ledger($from_date, $to_date, $company_name) {
        $this->db->select('*');
        $this->db->from('non_gst_invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->join('invocie_payment_non_gst', 'invocie_payment_non_gst.customer_id_fk=non_gst_invoice.customer_id');
        $this->db->where('invocie_payment_non_gst.customer_id_fk', $company_name);
        $this->db->group_by('ng_invocie_pay_id');
        $this->db->order_by('invoice_date', 'asc');
        $this->db->order_by('invocie_payment_non_gst.ng_invoice_pay_date', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    //get purchase ledger
    public function get_purchse_ledger_sum_by_vendor($uid) {
        $current_date = date('Y-m-d');
        $date = date('Y-m-d',  strtotime($current_date));
        $this->db->select('*, SUM(paid_amount) as total_purchase_amount');
        //$this->db->select_sum('paid_amount');
        $this->db->from('purchase_stock');
        $this->db->where('purchase_stock.uid', $uid);
        $this->db->where('purchase_stock.purchase_date', $date);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_stock.supplier_id_fk');
        $this->db->order_by("purchase_stock.purchase_date", "desc");
        ///$this->db->group_by(array("purchase_date", "supplier_id_fk"));
        //$this->db->group_by('purchase_date','&&','supplier_id_fk');
        $this->db->group_by('supplier_id_fk');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_purchse_ledger($from_date, $to_date, $company_name) {
        $this->db->select('*');
        $this->db->from('purchase_stock');
        $this->db->where('purchase_date >=', $from_date);
        $this->db->where('purchase_date <=', $to_date);
        $this->db->join('supplier', 'supplier.supplier_id=purchase_stock.supplier_id_fk');
//        $this->db->join('purchase_payment_history', 'purchase_payment_history.supplier_id_fk=purchase_stock.supplier_id_fk','Left');
        $this->db->where('purchase_stock.supplier_id_fk', $company_name);
        $this->db->order_by('purchase_stock.purchase_date', 'desc');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_purchse_payment_history($from_date, $to_date, $company_name) {
        $this->db->select('*');
        $this->db->from('purchase_payment_history');
        $this->db->where('payment_date >=', $from_date);
        $this->db->where('payment_date <=', $to_date);
        //$this->db->join('supplier', 'supplier.supplier_id=purchase_stock.supplier_id_fk');
//        $this->db->join('purchase_payment_history', 'purchase_payment_history.supplier_id_fk=purchase_stock.supplier_id_fk','Left');
        $this->db->where('purchase_payment_history.supplier_id_fk', $company_name);
        $this->db->order_by('purchase_payment_history.payment_date', 'desc');
        $query = $this->db->get();
        return $query->result();
    }



    public function get_purchse_bill_ledger($from_date, $to_date, $company_name) {
        //  echo $company_name;
     //  print_r($to_date);die();
          $this->db->select('*,purchase_bill.date,purchase_bill.number, purchase_bill_total.total,supplier.company_name,supplier.company_name, supplier.address, purchase_bill_total.balance');
          $this->db->from('purchase_bill');
          //echo "$purchase_bill.date"; die();
          $this->db->where('purchase_bill.date >=', $from_date);
          $this->db->where('purchase_bill.date <=', $to_date);
          $this->db->where('supplier.supplier_id', $company_name);
          $this->db->join('supplier', 'supplier.supplier_id=purchase_bill.supplier_id_fk');
          $this->db->join('purchase_bill_total', 'purchase_bill_total.number_fk=purchase_bill.number');
          
  
          $this->db->group_by('purchase_bill.number');
          $this->db->order_by('date', 'asc');
          $query = $this->db->get();
          
          //var_dump($query->result());die();
          return $query->result();
                
  
      }
  
      public function get_purchse_bill_payment_history($from_date, $to_date, $company_name) {
  
    // print_r($company_name);die();
          //$this->db->select('purchase_bill_payment_gst.purchase_pay_date,purchase_bill_payment_gst.payment_type, purchase_bill_payment_gst.purchase_pay_amount, purchase_bill.number, supplier.company_name');
          $this->db->select('*');
          $this->db->from('purchase_bill');
          $this->db->where('date >=', $from_date);
          $this->db->where('date <=', $to_date);
          $this->db->where('supplier.supplier_id', $company_name);
          
          $this->db->join('purchase_bill_payment_gst', 'purchase_bill_payment_gst.supplier_id_fk=purchase_bill.supplier_id_fk');
          $this->db->join('supplier', 'supplier.supplier_id=purchase_bill_payment_gst.supplier_id_fk');
          $this->db->join('purchase_bill_total', 'purchase_bill_total.number_fk=purchase_bill.number');
  
      //  $this->db->where('supplier.company_name', $company_name);
          $this->db->group_by('purchase_pay_id');
          $this->db->order_by('purchase_bill_payment_gst.purchase_pay_date', 'desc');
         $this->db->order_by('purchase_bill_total.balance', 'desc'); 
          $query = $this->db->get();
         // var_dump($query->result());die();
          return $query->result();
      }


      public function get_purchase_gst_ledger_payment_out($from_date, $to_date, $supplier_name) {
          $this->db->select('*');
          $this->db->from('payment_out');
          $this->db->where('payment_date >=', $from_date);
          $this->db->where('payment_date <=', $to_date);
          $this->db->where('payment_supplier_id', $supplier_name);
          $this->db->order_by('payment_id', 'asc');
          $query = $this->db->get();
          return $query->result();
                
  
      }


      public function get_purchase_gst_ledger_payment_in($from_date, $to_date, $supplier_name) {

          $this->db->select('*');
          $this->db->from('payment_in');
          $this->db->where('payment_date >=', $from_date);
          $this->db->where('payment_date <=', $to_date);
          $this->db->where('payment_customer_id', $supplier_name);
          $this->db->order_by('payment_id', 'asc');
          $query = $this->db->get();
          return $query->result();
                
  
      }

      public function get_customer_opening_balance($customer_id, $opening_date, $uid) {
          if (!$this->db->table_exists('opening_balance')) {
              return null;
          }

          $this->db->select('company_name, c_code');
          $this->db->from('customer');
          $this->db->where('customer_id', $customer_id);
          $customer = $this->db->get()->row();
          if (!$customer) {
              return null;
          }

          $account_names = array($customer->company_name);
          if (!empty($customer->c_code)) {
              $account_names[] = $customer->company_name . ' - ' . $customer->c_code;
          }

          $this->db->select('balance_id, account_name, opening_balance_amount, balance_date, description');
          $this->db->from('opening_balance');
          $this->db->where('uid', $uid);
          $this->db->where('balance_date <=', $opening_date);
          $this->db->group_start();
          foreach ($account_names as $index => $account_name) {
              if ($index === 0) {
                  $this->db->like('account_name', $account_name, 'after');
              } else {
                  $this->db->or_like('account_name', $account_name, 'after');
              }
          }
          $this->db->group_end();
          $this->db->order_by('balance_date', 'desc');
          $this->db->order_by('balance_id', 'desc');
          $this->db->limit(1);
          $query = $this->db->get();
          return $query->row();
      }

      public function get_supplier_opening_balance($supplier_id, $opening_date, $uid) {
          if (!$this->db->table_exists('opening_balance')) {
              return null;
          }

          $this->db->select('company_name, s_code');
          $this->db->from('supplier');
          $this->db->where('supplier_id', $supplier_id);
          $supplier = $this->db->get()->row();
          if (!$supplier) {
              return null;
          }

          $account_names = array($supplier->company_name);
          if (!empty($supplier->s_code)) {
              $account_names[] = $supplier->company_name . ' - ' . $supplier->s_code;
          }

          $this->db->select('balance_id, account_name, opening_balance_amount, balance_date, description');
          $this->db->from('opening_balance');
          $this->db->where('uid', $uid);
          $this->db->where('balance_date <=', $opening_date);
          $this->db->group_start();
          foreach ($account_names as $index => $account_name) {
              if ($index === 0) {
                  $this->db->like('account_name', $account_name, 'after');
              } else {
                  $this->db->or_like('account_name', $account_name, 'after');
              }
          }
          $this->db->group_end();
          $this->db->order_by('balance_date', 'desc');
          $this->db->order_by('balance_id', 'desc');
          $this->db->limit(1);
          $query = $this->db->get();
          return $query->row();
      }
    
    
}
