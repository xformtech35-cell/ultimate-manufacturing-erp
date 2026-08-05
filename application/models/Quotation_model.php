<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quotation_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Save main quotation
    public function save_quotation($data)
    {
        $this->db->trans_start();

        // Insert main quotation
        $this->db->insert('vendor_quotations', $data);
        $quotation_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $quotation_id;
    }

    // Save quotation items with GST calculation
    public function save_quotation_items($quotation_id, $rfq_id, $items)
    {
        $total_amount = 0;
        $gst_amount = 0;

        foreach ($items as $rfq_item_id => $item_data) {
            $unit_price = $item_data['unit_price'];
            $gst_percentage = $item_data['gst_percentage'] ?? 0;

            // Get item quantity from RFQ items
            $this->db->select('quantity');
            $this->db->where('rfq_item_id', $rfq_item_id);
            $this->db->where('rfq_id', $rfq_id);
            $item_details = $this->db->get('rfq_items')->row_array();

            if ($item_details) {
                $quantity = $item_details['quantity'];
                $item_total = $unit_price * $quantity;
                $item_gst = ($item_total * $gst_percentage) / 100;
                $item_final = $item_total + $item_gst;

                $item_data = [
                    'quotation_id' => $quotation_id,
                    'rfq_item_id' => $rfq_item_id,
                    'item_code' => $this->get_item_code($rfq_item_id),
                    'unit_price' => $unit_price,
                    'quantity' => $quantity,
                    'gst_percentage' => $gst_percentage,
                    'gst_amount' => $item_gst,
                    'total_amount' => $item_final
                ];

                $this->db->insert('vendor_quotation_items', $item_data);

                $total_amount += $item_total;
                $gst_amount += $item_gst;
            }
        }

        // Update totals in main quotation
        $final_amount = $total_amount + $gst_amount;
        $this->update_quotation_totals($quotation_id, $total_amount, $gst_amount, $final_amount);

        return true;
    }

    // Get item code from RFQ items
    private function get_item_code($rfq_item_id)
    {
        $this->db->select('item_code');
        $this->db->where('rfq_item_id', $rfq_item_id);
        $item = $this->db->get('rfq_items')->row_array();
        return $item ? $item['item_code'] : '';
    }

    // Update quotation totals
    public function update_quotation_totals($quotation_id, $total_amount, $gst_amount, $final_amount)
    {
        $this->db->where('quotation_id', $quotation_id);
        $this->db->update('vendor_quotations', [
            'total_amount' => $total_amount,
            'gst_amount' => $gst_amount,
            'final_amount' => $final_amount
        ]);

        return true;
    }

    // Get quotations for specific RFQ
    public function get_quotations_by_rfq($rfq_id)
    {
        $this->db->select('vq.*, s.company_name as supplier_name, s.fullname as contact_person, s.email, s.mobile as phone');
        $this->db->from('vendor_quotations vq');
        $this->db->join('supplier s', 's.supplier_id = vq.supplier_id', 'left');
        $this->db->where('vq.rfq_id', $rfq_id);
        $this->db->where('vq.is_latest', 1);
        $this->db->order_by('vq.final_amount', 'ASC');
        $quotations = $this->db->get()->result_array();

        // Get items for each quotation
        foreach ($quotations as &$quotation) {
            $quotation['items'] = $this->get_quotation_items($quotation['quotation_id']);
        }

        return $quotations;
    }

    // Get quotation revision history for a specific vendor on an RFQ
    public function get_quotation_history($rfq_id, $supplier_id)
    {
        $this->db->select('vq.*, s.company_name as supplier_name, s.fullname as contact_person, s.email, s.mobile as phone');
        $this->db->from('vendor_quotations vq');
        $this->db->join('supplier s', 's.supplier_id = vq.supplier_id', 'left');
        $this->db->where('vq.rfq_id', $rfq_id);
        $this->db->where('vq.supplier_id', $supplier_id);
        $this->db->order_by('vq.revision_no', 'DESC');
        $quotations = $this->db->get()->result_array();

        foreach ($quotations as &$quotation) {
            $quotation['items'] = $this->get_quotation_items($quotation['quotation_id']);
        }

        return $quotations;
    }

    // Get the highest revision number for a supplier and RFQ
    public function get_highest_revision($rfq_id, $supplier_id)
    {
        $this->db->select_max('revision_no');
        $this->db->where('rfq_id', $rfq_id);
        $this->db->where('supplier_id', $supplier_id);
        $row = $this->db->get('vendor_quotations')->row_array();
        return $row ? ($row['revision_no'] !== null ? (int)$row['revision_no'] : -1) : -1;
    }

    // Get quotation items
    public function get_quotation_items($quotation_id)
    {
        $this->db->select('vqi.*, ri.description, ri.unit, ri.hsn');
        $this->db->from('vendor_quotation_items vqi');
        $this->db->join('rfq_items ri', 'ri.rfq_item_id = vqi.rfq_item_id', 'left');
        $this->db->where('vqi.quotation_id', $quotation_id);
        return $this->db->get()->result_array();
    }

    // Get specific quotations for comparison
    public function get_quotations_for_comparison($quotation_ids)
    {
        $this->db->select('vq.*, s.company_name as supplier_name');
        $this->db->from('vendor_quotations vq');
        $this->db->join('supplier s', 's.supplier_id = vq.supplier_id', 'left');
        $this->db->where_in('vq.quotation_id', $quotation_ids);
        $this->db->order_by('vq.final_amount', 'ASC');
        $quotations = $this->db->get()->result_array();

        foreach ($quotations as &$quotation) {
            $quotation['items'] = $this->get_quotation_items($quotation['quotation_id']);
        }

        return $quotations;
    }

    // Check if vendor already quoted for this RFQ
    public function check_existing_quotation($rfq_id, $supplier_id)
    {
        $this->db->where('rfq_id', $rfq_id);
        $this->db->where('supplier_id', $supplier_id);
        return $this->db->get('vendor_quotations')->row_array();
    }

    // Delete quotation
    public function delete_quotation($quotation_id)
    {
        $this->db->trans_start();

        // Delete items first
        $this->db->where('quotation_id', $quotation_id);
        $this->db->delete('vendor_quotation_items');

        // Delete main quotation
        $this->db->where('quotation_id', $quotation_id);
        $this->db->delete('vendor_quotations');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    // Get RFQ details
    // public function get_rfq_details($rfq_id)
    // {
    //     $this->db->select('r.*, 
    //         (SELECT pri.pr_no 
    //          FROM ' . $this->db->dbprefix . 'purchase_requisition_items pri 
    //          WHERE pri.pr_id = r.pr_id 
    //          LIMIT 1) as pr_no');
    //     $this->db->where('r.rfq_id', $rfq_id);
    //     return $this->db->get('rfq r')->row_array();
    // }
    public function get_rfq_details($rfq_id)
    {
        $dbprefix = $this->db->dbprefix;
        $this->db->select("
            r.*, 
            u.username AS created_by_name,
            (
                SELECT pri.pr_no
                FROM {$dbprefix}purchase_requisition_items pri
                WHERE pri.pr_id = r.pr_id
                LIMIT 1
            ) AS pr_no
        ");

        $this->db->from('rfq r');
        $this->db->join('user u', 'u.user_id = r.created_by', 'left');
        $this->db->where('r.rfq_id', $rfq_id);

        return $this->db->get()->row_array();
    }

    // Get RFQ items
    public function get_rfq_items($rfq_id)
    {
        $this->db->where('rfq_id', $rfq_id);
        return $this->db->get('rfq_items')->result_array();
    }

    // Get suppliers for specific RFQ from rfq_suppliers
    public function get_suppliers($rfq_id = null)
    {
        if ($rfq_id) {
            // Get suppliers assigned to specific RFQ
            $this->db->select('s.supplier_id, s.company_name as supplier_name, s.fullname, s.email, s.mobile, s.address');
            $this->db->from('rfq_suppliers rs');
            $this->db->join('supplier s', 's.supplier_id = rs.supplier_id', 'inner');
            $this->db->where('rs.rfq_id', $rfq_id);
            $this->db->order_by('s.company_name', 'ASC');
            return $this->db->get()->result_array();
        } else {
            // Get all suppliers (for create RFQ form)
            $this->db->select('supplier_id, company_name as supplier_name, fullname, email, mobile, address');
            $this->db->from('supplier');
            $this->db->order_by('company_name', 'ASC');
            return $this->db->get()->result_array();
        }
    }
}
