<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use LDAP\Result;

class Dashboard extends CI_Model
{

    public function get_quotation_count($uid)
    {
        $this->db->select('id, number_fk, total, status');
        $this->db->from('quotation_total');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1000);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_invoice_count($uid)
    {
        $this->db->select('id, date, total, status');
        $this->db->from('invoice_total');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1000);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_sgst_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select_sum('sgst');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_cgst_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select_sum('cgst');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_igst_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select_sum('igst');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    //    public function get_ng_quotation_count($uid) {
    //        $this->db->select('*');
    //        $this->db->from('non_gst_quotation_total');
    //        //$this->db->where('uid', $uid);
    //        $query = $this->db->get();
    //        return $query->result();
    //    }

    public function get_ng_invoice_count($uid)
    {
        $this->db->select('*');
        $this->db->from('non_gst_invoice_total');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    // ===========================================================================
    //  ROLE-BASED METHODS (safely checks if created_by / uid column exists)
    // ===========================================================================

    private function _filter_by_user($table, $uid, $alias = '')
    {
        $prefix = $alias ? $alias . '.' : '';
        if ($this->db->field_exists('created_by', $table)) {
            $this->db->where($prefix . 'created_by', $uid);
        } else if ($this->db->field_exists('uid', $table)) {
            $this->db->where($prefix . 'uid', $uid);
        } else if ($this->db->field_exists('user_id', $table)) {
            $this->db->where($prefix . 'user_id', $uid);
        }
    }

    // --- SALES ---

    public function get_fy_invoice_count_by_user($from_date, $to_date, $uid)
    {
        $this->db->from('invoice_total');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->_filter_by_user('invoice_total', $uid);
        return $this->db->count_all_results();
    }

    public function get_fy_invoice_amount_by_user($from_date, $to_date, $uid)
    {
        $this->db->select_sum('total_before_tax');
        $this->db->from('invoice_total');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->_filter_by_user('invoice_total', $uid);
        $result = $this->db->get()->row();
        return $result->total_before_tax ?? 0;
    }

    public function get_fy_quotation_count_by_user($from_date, $to_date, $uid)
    {
        $this->db->from('quotation');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->_filter_by_user('quotation', $uid);
        $this->db->group_by('number');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_fy_proforma_count_by_user($from_date, $to_date, $uid)
    {
        $this->db->from('proforma_invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->_filter_by_user('proforma_invoice', $uid);
        $this->db->group_by('invoice_number');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_salesorder_count_by_user($uid)
    {
        $this->db->from('salesorder_total');
        $this->_filter_by_user('salesorder_total', $uid);
        return $this->db->count_all_results();
    }

    // --- PURCHASE ---

    public function get_fy_po_count_by_user($from_date, $to_date, $uid)
    {
        $this->db->from('po_total');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->_filter_by_user('po_total', $uid);
        return $this->db->count_all_results();
    }

    public function get_fy_po_amount_by_user($from_date, $to_date, $uid)
    {
        $this->db->select_sum('total');
        $this->db->from('po_total');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->_filter_by_user('po_total', $uid);
        $result = $this->db->get()->row();
        return $result->total ?? 0;
    }

    public function get_fy_purchase_bill_count_by_user($from_date, $to_date, $uid)
    {
        $user_where = '';
        if ($this->db->field_exists('created_by', 'purchase_bill')) {
            $user_where = ' AND pb.created_by = ' . (int)$uid;
        } else if ($this->db->field_exists('uid', 'purchase_bill')) {
            $user_where = ' AND pb.uid = ' . (int)$uid;
        }

        $query = $this->db->query(
            'SELECT COUNT(pbt.id) as cnt
               FROM ' . $this->db->dbprefix('purchase_bill_total') . ' pbt
               INNER JOIN ' . $this->db->dbprefix('purchase_bill') . ' pb ON pb.number = pbt.number_fk
               WHERE pb.date >= ? AND pb.date <= ?' . $user_where,
            [$from_date, $to_date]
        );
        $row = $query->row();
        return (int)($row->cnt ?? 0);
    }

    public function get_fy_purchase_bill_amount_by_user($from_date, $to_date, $uid)
    {
        $user_where = '';
        if ($this->db->field_exists('created_by', 'purchase_bill')) {
            $user_where = ' AND pb.created_by = ' . (int)$uid;
        } else if ($this->db->field_exists('uid', 'purchase_bill')) {
            $user_where = ' AND pb.uid = ' . (int)$uid;
        }

        $query = $this->db->query(
            'SELECT SUM(pbt.total_before_tax) as total
               FROM ' . $this->db->dbprefix('purchase_bill_total') . ' pbt
               INNER JOIN ' . $this->db->dbprefix('purchase_bill') . ' pb ON pb.number = pbt.number_fk
               WHERE pb.date >= ? AND pb.date <= ?' . $user_where,
            [$from_date, $to_date]
        );
        $row = $query->row();
        return $row->total ?? 0;
    }

    public function get_grn_count_by_user($uid)
    {
        $this->db->from('grn_total');
        $this->_filter_by_user('grn_total', $uid);
        return $this->db->count_all_results();
    }

    public function get_pending_grn_count_by_user($uid)
    {
        $this->db->from('grn_total');
        $this->db->where('approval_status', 'pending');
        $this->_filter_by_user('grn_total', $uid);
        return $this->db->count_all_results();
    }

    public function get_pr_count_by_user($uid)
    {
        $this->db->from('purchase_requisition');
        $this->_filter_by_user('purchase_requisition', $uid);
        return $this->db->count_all_results();
    }

    public function get_rfq_count_by_user($uid)
    {
        $this->db->from('rfq');
        $this->_filter_by_user('rfq', $uid);
        return $this->db->count_all_results();
    }

    public function get_pr_status_data_by_user($uid)
    {
        $this->db->select('workflow_status as status, COUNT(*) as count, SUM(total_value) as total_value');
        $this->db->from('purchase_requisition');
        $this->_filter_by_user('purchase_requisition', $uid);
        $this->db->group_by('workflow_status');
        return $this->db->get()->result();
    }

    public function get_po_status_data_by_user($uid)
    {
        $this->db->select('approval_status as status, COUNT(*) as count, SUM(total) as total_value');
        $this->db->from('po_total');
        $this->_filter_by_user('po_total', $uid);
        $this->db->group_by('approval_status');
        return $this->db->get()->result();
    }

    public function get_recent_pr_by_user($uid, $limit = 5)
    {
        $this->db->select('pr.*, d.department_name, l.location_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left');
        $this->_filter_by_user('purchase_requisition', $uid, 'pr');
        $this->db->order_by('pr.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_recent_po_by_user($uid, $limit = 5)
    {
        $this->db->select('po.*, s.company_name');
        $this->db->from('po_total po');
        $this->db->join('supplier s', 'po.supplier_id_fk = s.supplier_id', 'left');
        $this->_filter_by_user('po_total', $uid, 'po');
        $this->db->order_by('po.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    // --- PRODUCTION ---

    public function get_job_orders_count_by_user($uid)
    {
        $this->db->from('joborder_total');
        $this->_filter_by_user('joborder_total', $uid);
        return $this->db->count_all_results();
    }

    public function get_pending_job_orders_by_user($uid)
    {
        $this->db->from('joborder_total');
        $this->db->where('status', 0);
        $this->_filter_by_user('joborder_total', $uid);
        return $this->db->count_all_results();
    }

    public function get_completed_job_orders_by_user($uid)
    {
        $this->db->from('joborder_total');
        $this->db->where('status', 2);
        $this->_filter_by_user('joborder_total', $uid);
        return $this->db->count_all_results();
    }

    public function get_material_issues_by_user($uid)
    {
        $this->db->from('material_issue_slips');
        $this->_filter_by_user('material_issue_slips', $uid);
        return $this->db->count_all_results();
    }

    // --- original methods continue below ---

    public function get_monthly_monthly_quotations()
    {


        // $query = $this->db->query('select month(date) as month, sum(amount + sgst + cgst + igst) as amount from vtechAccounting_quotation group by month(date)');

        $this->db->select('month(date) as month, sum(amount + sgst + cgst + igst) as amount');
        $this->db->from('quotation');
        //$this->db->where('uid', $uid);
        $this->db->group_by('month(date)');
        $query = $this->db->get();

        //        print_r($query->result());
        //        
        //        die();
        return $query->result();
    }

    public function get_monthly_sales()
    {

        // $query = $this->db->query('select month(invoice_date) as month, sum(amount + sgst + cgst + igst) as amount from vtechAccounting_invoice group by month(invoice_date)');

        $this->db->select('month(invoice_date) as month, sum(amount + sgst + cgst + igst) as amount');
        $this->db->from('invoice');
        //$this->db->where('uid', $uid);
        $this->db->group_by('month(invoice_date)');
        $query = $this->db->get();


        return $query->result();
    }

    public function get_monthly_purchase()
    {

        // $query = $this->db->query('select month(purchase_date) as month, sum(amount + sgst + cgst + igst) as amount from vtechAccounting_purchase_order group by month(purchase_date)');


        $this->db->select('month(purchase_date) as month, sum(amount + sgst + cgst + igst) as amount');
        $this->db->from('purchase_order');
        //$this->db->where('uid', $uid);
        $this->db->group_by('month(purchase_date)');
        $query = $this->db->get();


        return $query->result();
    }

    public function get_monthly_expenses()
    {
        // $query = $this->db->query('select month(date) as month, sum(expense_amount) as amount from vtechAccounting_expense group by month(date)');


        $this->db->select('month(date) as month, sum(expense_amount) as amount');
        $this->db->from('expense');
        //$this->db->where('uid', $uid);
        $this->db->group_by('month(date)');
        $query = $this->db->get();
        return $query->result();
    }

    // Purchase Requisition Methods
    public function get_pr_count($uid)
    {
        $this->db->from('purchase_requisition');
        //$this->db->where('uid', $uid);
        return $this->db->count_all_results();
    }

    public function get_pr_status_data($uid)
    {
        $this->db->select('workflow_status as status, COUNT(*) as count, SUM(total_value) as total_value');
        $this->db->from('purchase_requisition');
        //$this->db->where('uid', $uid);
        $this->db->group_by('workflow_status');
        return $this->db->get()->result();
    }

    public function get_monthly_pr_data($uid)
    {
        $current_year = date('Y');
        $months = array_fill(0, 12, 0);

        $this->db->select('MONTH(pr_date) as month, COUNT(*) as count');
        $this->db->from('purchase_requisition');
        $this->db->where('YEAR(pr_date)', $current_year);
        //$this->db->where('uid', $uid);
        $this->db->group_by('MONTH(pr_date)');
        $result = $this->db->get()->result();

        foreach ($result as $row) {
            $months[$row->month - 1] = (int)$row->count;
        }

        return $months;
    }

    // RFQ Methods
    public function get_rfq_count($uid)
    {
        $this->db->from('rfq');
        //$this->db->where('uid', $uid);
        return $this->db->count_all_results();
    }

    public function get_rfq_status_data($uid)
    {
        $this->db->select('status, COUNT(*) as count');
        $this->db->from('rfq');
        //$this->db->where('uid', $uid);
        $this->db->group_by('status');
        return $this->db->get()->result();
    }

    public function get_monthly_rfq_data($uid)
    {
        $current_year = date('Y');
        $months = array_fill(0, 12, 0);

        $this->db->select('MONTH(rfq_date) as month, COUNT(*) as count');
        $this->db->from('rfq');
        $this->db->where('YEAR(rfq_date)', $current_year);
        //$this->db->where('uid', $uid);
        $this->db->group_by('MONTH(rfq_date)');
        $result = $this->db->get()->result();

        foreach ($result as $row) {
            $months[$row->month - 1] = (int)$row->count;
        }

        return $months;
    }

    // Recent Activity Methods
    public function get_recent_pr($uid, $limit = 5)
    {
        $this->db->select('pr.*, d.department_name, l.location_name');
        $this->db->from('purchase_requisition pr');
        $this->db->join('department_master d', 'pr.department_id_fk = d.department_id', 'left');
        $this->db->join('location_master l', 'pr.location_id_fk = l.location_id', 'left');
        //$this->db->where('pr.uid', $uid);
        $this->db->order_by('pr.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_recent_rfq($uid, $limit = 5)
    {
        $this->db->select('r.*, pr.pr_date, COUNT(rs.supplier_id) as vendor_count');
        $this->db->from('rfq r');
        $this->db->join('purchase_requisition pr', 'r.pr_id = pr.pr_id', 'left');
        $this->db->join('rfq_suppliers rs', 'r.rfq_id = rs.rfq_id', 'left');
        //$this->db->where('r.uid', $uid);
        $this->db->group_by('r.rfq_id');
        $this->db->order_by('r.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_recent_po($uid, $limit = 5)
    {
        $this->db->select('po.*, s.company_name');
        $this->db->from('po_total po');
        $this->db->join('supplier s', 'po.supplier_id_fk = s.supplier_id', 'left');
        //$this->db->where('po.uid', $uid);
        $this->db->order_by('po.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    // Approval Pending Counts
    public function get_pending_pr_approvals($uid)
    {
        $this->db->from('purchase_requisition');
        $this->db->where('workflow_status !=', 'Approved');
        $this->db->where('workflow_status !=', 'Rejected');
        //$this->db->where('uid', $uid);
        return $this->db->count_all_results();
    }

    public function get_pending_po_approvals($uid)
    {
        $this->db->from('po_total');
        $this->db->where('approval_status', 'pending_approval');
        //$this->db->where('uid', $uid);
        return $this->db->count_all_results();
    }

    // Add this PO Count method (it was missing)
    public function get_po_count($uid)
    {
        $this->db->from('po_total');
        //$this->db->where('uid', $uid); // Uncomment if you need user filtering
        return $this->db->count_all_results();
    }

    public function get_purchase_bill_count($uid)
    {
        $this->db->from('purchase_bill_total');
        //$this->db->where('uid', $uid); // Uncomment if you need user filtering
        return $this->db->count_all_results();
    }

    // Also add these other PO-related methods that were mentioned:
    public function get_po_status_data($uid)
    {
        $this->db->select('approval_status as status, COUNT(*) as count, SUM(total) as total_value');
        $this->db->from('po_total');
        //$this->db->where('uid', $uid);
        $this->db->group_by('approval_status');
        return $this->db->get()->result();
    }

    public function get_monthly_po_data($uid)
    {
        $current_year = date('Y');
        $months = array_fill(0, 12, 0);

        $this->db->select('MONTH(date) as month, COUNT(*) as count');
        $this->db->from('po_total');
        $this->db->where('YEAR(date)', $current_year);
        //$this->db->where('uid', $uid);
        $this->db->group_by('MONTH(date)');
        $result = $this->db->get()->result();

        foreach ($result as $row) {
            if ($row->month >= 1 && $row->month <= 12) {
                $months[$row->month - 1] = (int)$row->count;
            }
        }

        return $months;
    }

    public function get_total_invoice_amount($uid = null)
{
    $this->db->select_sum('total');
    $this->db->from('invoice_total');
    
    if ($uid !== null) {
        $this->db->where('uid', $uid);
    }
    
    $query = $this->db->get();
    $result = $query->row();
    
    return $result->total ?? 0;
}

    public function get_total_po_amount($uid = null)
{
    $this->db->select_sum('total');
    $this->db->from('po_total');
    
    if ($uid !== null) {
        $this->db->where('uid', $uid);
    }
    
    $query = $this->db->get();
    $result = $query->row();
    
    return $result->total ?? 0;
}

    public function get_total_inventory_amount($uid = null)
    {
        $this->db->select('SUM(COALESCE(stock, 0) * COALESCE(cost_price, 0)) as total_val', FALSE);
        $this->db->from('inventory');
        
        if ($uid !== null) {
            $this->db->where('uid', $uid);
        }
        
        $query = $this->db->get();
        $result = $query->row();
        
        return (float) ($result->total_val ?? 0);
    }

    public function get_total_sale_value($uid = null)
    {
        $this->db->select('SUM(COALESCE(stock, 0) * COALESCE(sell_price, 0)) as total_val', FALSE);
        $this->db->from('inventory');
        
        if ($uid !== null) {
            $this->db->where('uid', $uid);
        }
        
        $query = $this->db->get();
        $result = $query->row();
        
        return (float) ($result->total_val ?? 0);
    }

    public function get_inventory_count($uid = null)
    {
        $this->db->from('inventory');
        
        if ($uid !== null) {
            $this->db->where('uid', $uid);
        }
        
        return $this->db->count_all_results();
    }

    // ─── Financial Year (April–March) filtered methods ───────────────────────

    public function get_fy_monthly_quotations($from_date, $to_date)
    {
        $this->db->select('MONTH(date) as month, SUM(amount + sgst + cgst + igst) as amount');
        $this->db->from('quotation');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->group_by('MONTH(date)');
        return $this->db->get()->result();
    }

    public function get_fy_monthly_sales($from_date, $to_date)
    {
        $this->db->select('MONTH(invoice_date) as month, SUM(amount + sgst + cgst + igst) as amount');
        $this->db->from('invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->group_by('MONTH(invoice_date)');
        return $this->db->get()->result();
    }

    public function get_fy_monthly_purchase($from_date, $to_date)
    {
        // Use purchase_bill (Purchase Vouchers) to match the summary strip total
        $this->db->select('MONTH(date) as month, SUM(amount + sgst + cgst + igst) as amount');
        $this->db->from('purchase_bill');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->group_by('MONTH(date)');
        return $this->db->get()->result();
    }

    public function get_fy_monthly_expenses($from_date, $to_date)
    {
        $this->db->select('MONTH(date) as month, SUM(expense_amount) as amount');
        $this->db->from('expense');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->group_by('MONTH(date)');
        return $this->db->get()->result();
    }

    public function get_fy_invoice_count($from_date, $to_date)
    {
        // invoice_total has its own `date` column — no join needed
        $this->db->from('invoice_total');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        return $this->db->count_all_results();
    }

    public function get_fy_invoice_total_amount($from_date, $to_date)
    {
        $this->db->select_sum('total_before_tax');
        $this->db->from('invoice_total');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $result = $this->db->get()->row();


        // var_dump($result);

        // die();
        return $result->total_before_tax ?? 0;
    }

    public function get_fy_po_count($from_date, $to_date)
    {
        // po_total has its own `date` column — no join needed
        $this->db->from('po_total');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        return $this->db->count_all_results();
    }

    public function get_fy_po_total_amount($from_date, $to_date)
    {
        $this->db->select_sum('total');
        $this->db->from('po_total');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $result = $this->db->get()->row();
        return $result->total ?? 0;
    }

    public function get_fy_purchase_bill_count($from_date, $to_date)
    {
        // Use raw query to avoid CI dbprefix mishandling aliases
        $query = $this->db->query(
            'SELECT COUNT(pbt.id) as cnt
               FROM ' . $this->db->dbprefix('purchase_bill_total') . ' pbt
               INNER JOIN ' . $this->db->dbprefix('purchase_bill') . ' pb ON pb.number = pbt.number_fk
               WHERE pb.date >= ? AND pb.date <= ?',
            [$from_date, $to_date]
        );
        $row = $query->row();
        return (int)($row->cnt ?? 0);
    }

    public function get_fy_purchase_bill_total_amount($from_date, $to_date)
    {
        $query = $this->db->query(
            'SELECT SUM(pbt.total_before_tax) as total
               FROM ' . $this->db->dbprefix('purchase_bill_total') . ' pbt
               INNER JOIN ' . $this->db->dbprefix('purchase_bill') . ' pb ON pb.number = pbt.number_fk
               WHERE pb.date >= ? AND pb.date <= ?',
            [$from_date, $to_date]
        );
        $row = $query->row();
        return $row->total ?? 0;
    }

    public function get_fy_quotation_count($from_date, $to_date)
    {
        $this->db->from('quotation');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->group_by('number');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_fy_proforma_count($from_date, $to_date)
    {
        $this->db->from('proforma_invoice');
        $this->db->where('invoice_date >=', $from_date);
        $this->db->where('invoice_date <=', $to_date);
        $this->db->group_by('invoice_number');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_fy_total_expense($from_date, $to_date)
    {
        $this->db->select_sum('expense_amount');
        $this->db->from('expense');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $result = $this->db->get()->row();
        return $result->expense_amount ?? 0;
    }

    public function get_fy_direct_expense($from_date, $to_date)
    {
        $this->db->select_sum('expense_amount');
        $this->db->from('expense');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->like('expense_category', 'Direct - ', 'after');
        $result = $this->db->get()->row();
        return $result->expense_amount ?? 0;
    }

    public function get_fy_indirect_expense($from_date, $to_date)
    {
        $this->db->select_sum('expense_amount');
        $this->db->from('expense');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->like('expense_category', 'Indirect - ', 'after');
        $result = $this->db->get()->row();
        return $result->expense_amount ?? 0;
    }

}

