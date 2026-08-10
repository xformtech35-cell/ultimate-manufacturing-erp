<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RFQ_model extends CI_Model
{
    // -------------------------
    // Get Last RFQ Number
    // -------------------------
    public function get_last_rfq_number()
    {
        // Determine financial year (e.g., 26-27 for 2026-2027)
        if (date('m') <= 3) {
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        // Get the last RFQ for this financial year — new format: RFQ/26-27/0001
        $this->db->select('rfq_no');
        $this->db->from('rfq');
        $this->db->like('rfq_no', 'RFQ/' . $financial_year . '/', 'after');
        $this->db->order_by('rfq_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        $last_rfq_no = $query->row();

        if ($last_rfq_no) {
            // New format: RFQ/26-27/0001 — extract last numeric segment
            preg_match('/RFQ\/\d{2}-\d{2}\/(\d+)/i', $last_rfq_no->rfq_no, $matches);
            $last_number = isset($matches[1]) ? (int)$matches[1] : 0;
            return $last_number + 1;
        } else {
            return 1;
        }
    }

    // -------------------------
    // Generate New RFQ Number
    // -------------------------
    public function generate_rfq_no()
    {
        $last_number = $this->get_last_rfq_number();

        if (date('m') <= 3) {
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        // Format: RFQ/26-27/0001  (consistent with PO/PR format)
        $rfq_no = "RFQ/" . $financial_year . "/" . sprintf("%04d", $last_number);

        return $rfq_no;
    }

    // -------------------------
    // Get All RFQs
    // -------------------------
    public function get_rfqs()
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('r.created_at >=', $fy_from);
            $this->db->where('r.created_at <=', $fy_to);
        }

        $this->db->select('r.*, u.username as created_by_name,
            (SELECT pri.pr_no 
             FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
             WHERE pri.pr_id = r.pr_id 
             LIMIT 1) as pr_no');
        $this->db->from('rfq r');
        $this->db->join('user u', 'u.user_id = r.created_by', 'left');
        $this->db->order_by('r.rfq_id', 'DESC'); // Most recent first
        $query = $this->db->get();
        return $query->result();
    }

    // -------------------------
    // Insert RFQ Master
    // -------------------------
    public function insert_rfq($data)
    {
        $this->db->insert("rfq", $data);
        return $this->db->insert_id();
    }

    // -------------------------
    // Insert RFQ Items
    // -------------------------
    public function insert_rfq_items($items)
    {
        return $this->db->insert_batch("rfq_items", $items);
    }

    // -------------------------
    // Save RFQ Suppliers
    // -------------------------
    public function save_rfq_suppliers($data)
    {
        return $this->db->insert_batch("rfq_suppliers", $data);
    }

    // -------------------------
    // Get RFQ by ID
    // -------------------------
    public function get_rfq_by_id($rfq_id)
    {
        return $this->db->select("r.*")
            ->from("rfq r")
            ->where("r.rfq_id", $rfq_id)
            ->get()
            ->row_array();
    }

    // -------------------------
    // Get RFQ Items
    // -------------------------
    public function get_rfq_items($rfq_id)
    {
        return $this->db->where("rfq_id", $rfq_id)
            ->get("rfq_items")
            ->result_array();
    }

    // -------------------------
    // Get Supplier List
    // -------------------------
    public function get_suppliers()
    {
        return $this->db->get("supplier")->result_array();
    }

    public function get_rfq_quotations($rfq_id)
    {
        $this->db->select('s.company_name AS supplier_name, r.amount, r.quote_date');
        $this->db->from('rfq_suppliers r');
        $this->db->join('supplier s', 's.supplier_id = r.supplier_id', 'left');
        $this->db->where('r.rfq_id', $rfq_id);
        $query = $this->db->get();
        return $query->result_array();
    }


    // In requisition_model.php
    public function get_requisition_item_by_id($item_id)
    {
        $this->db->where('item_id', $item_id);
        $query = $this->db->get('requisition_items');
        return $query->row();
    }

    // In RFQ_model.php
    public function update_rfq_email_status($rfq_id, $status = 1)
    {
        $this->db->where('rfq_id', $rfq_id);
        return $this->db->update('rfq', array(
            'email_sent' => $status,
            'email_sent_date' => ($status == 1) ? date('Y-m-d H:i:s') : NULL
        ));
    }

    // Also add these helper methods if needed:
    public function get_rfq_suppliers($rfq_id)
    {
        $this->db->select('rs.*, s.*');
        $this->db->from('rfq_suppliers rs');
        $this->db->join('supplier s', 's.supplier_id = rs.supplier_id', 'left');
        $this->db->where('rs.rfq_id', $rfq_id);
        return $this->db->get()->result_array();
    }

    public function update_supplier_email_status($rfq_id, $supplier_id, $status = 1)
    {
        $this->db->where('rfq_id', $rfq_id);
        $this->db->where('supplier_id', $supplier_id);
        return $this->db->update('rfq_suppliers', array(
            'email_sent' => $status,
            'email_sent_date' => ($status == 1) ? date('Y-m-d H:i:s') : NULL
        ));
    }
}
