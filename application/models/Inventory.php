<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Inventory extends CI_Model
{

    public function add_inventory($data_inventory)
    {
        return $this->db->insert('inventory', $data_inventory);
    }

    public function add_expense($data_expense)
    {
        return $this->db->insert('expense', $data_expense);
    }

    public function inventory_code_check($code, $uid)
    {
        $this->db->select('code');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $this->db->where('code', $code);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_expense_categories($uid)
    {
        //$this->db->distinct();
        $this->db->select('exp_cat');
        $this->db->from('expense_category');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
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

    public function get_inventory_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('inventory');
        $this->db->where('inventory_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete_inventory_by_id($id)
    {
        try {
            $db_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;

            $this->db->where('inventory_id', $id);
            $result = $this->db->delete('inventory');

            $error = $this->db->error();
            $this->db->db_debug = $db_debug;

            if ($error['code'] !== 0) {
                return 'CONSTRAIN_ERROR';
            }

            if ($result && $this->db->affected_rows() == '1') {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $e) {
            return 'CONSTRAIN_ERROR';
        } catch (Throwable $t) {
            return 'CONSTRAIN_ERROR';
        }
    }

    public function delete_expense_by_id($id)
    {
        $this->db->where('expense_id', $id);
        $this->db->delete('expense');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_expense_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('expense');
        $this->db->where('expense_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_all_expense($uid)
    {
        $this->db->select('*');
        $this->db->from('expense');
        $this->db->order_by('date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_expense_by_date_range($from_date, $to_date, $expense_mode, $user_id)
{
    $prefix = ($expense_mode == 'direct') ? 'Direct - ' : 'Indirect - ';
    
    $this->db->select('*');
    $this->db->from('expense');
    $this->db->where('uid', $user_id);
    $this->db->where('date >=', $from_date);
    $this->db->where('date <=', $to_date);
    $this->db->like('expense_category', $prefix, 'after');
    $this->db->order_by('date', 'DESC');
    
    $query = $this->db->get();
    return $query->result();
}


    public function edit_expense($data_expense, $expense_id, $uid)
    {
        $this->db->where('expense_id', $expense_id);
        $this->db->where('uid', $uid);
        $this->db->update('expense', $data_expense);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

        public function get_expense_by_date_range_with_category($from_date, $to_date, $category, $expense_mode, $user_id)
        {
            $prefix = ($expense_mode == 'direct') ? 'Direct - ' : 'Indirect - ';
            
            $this->db->select('*');
            $this->db->from('expense');
            $this->db->where('uid', $user_id);
            $this->db->like('expense_category', $prefix, 'after');
            
            // Apply date filter only if dates are provided
            if (!empty($from_date) && !empty($to_date)) {
                $this->db->where('date >=', $from_date);
                $this->db->where('date <=', $to_date);
            }
            
            // Apply category filter if provided
            if (!empty($category)) {
                $this->db->where('expense_category', $category);
            }
            
            $this->db->order_by('date', 'DESC');
            $query = $this->db->get();
            return $query->result();
        }


    public function edit_inventory($data_inventory, $inventory_id, $uid)
    {
        // ── 1. Read current stock values BEFORE the update ──────────────────
        $before = $this->db
            ->select('stock, available_stock, allocated_stock, code, item_name')
            ->where('inventory_id', $inventory_id)
            ->get('inventory')
            ->row();

        $old_stock = $before ? (float) $before->stock : 0;
        $new_stock = isset($data_inventory['stock']) ? (float) $data_inventory['stock'] : $old_stock;

        // ── 2. If stock is being changed, recalculate available_stock ────────
        //   available_stock = new_stock - allocated_stock
        //   (allocated_stock stays unchanged; only physical stock changes)
        if ($new_stock !== $old_stock) {
            $allocated = $before ? (float) $before->allocated_stock : 0;
            $data_inventory['available_stock'] = max(0, $new_stock - $allocated);
        }

        // ── 3. Update the inventory row ──────────────────────────────────────
        $this->db->where('inventory_id', $inventory_id);
        $this->db->update('inventory', $data_inventory);
        $affected = $this->db->affected_rows();

        // ── 4. Log stock adjustment to stock_ledger (for full traceability) ──
        if ($new_stock !== $old_stock) {
            $diff = $new_stock - $old_stock; // positive = added, negative = removed
            $item_code = $before ? $before->code : '';

            $ledger_data = array(
                'transaction_type' => 'ADJUSTMENT',
                'reference_no'     => 'MANUAL-ADJ-' . $inventory_id,
                'item_code'        => $item_code,
                'quantity'         => $diff,          // signed: + for increase, - for decrease
                'balance_quantity'  => $new_stock,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'remarks'          => 'Manual stock adjustment via Item Code master (old: ' . $old_stock . ', new: ' . $new_stock . ') by uid:' . $uid,
                'uid'              => $uid,
            );
            $this->db->insert('stock_ledger', $ledger_data);
        }

        // affected_rows is 1 when updated, 0 when nothing changed (same data)
        return ($affected >= 0); // always true; treat 0 affected as success (no change needed)
    }


    public function get_inventory($uid, $limit = 0)
    {
        $this->db->select(
            'inventory.*, 
         item_category_master.category_name, 
         item_group_master.group_name'
        );

        $this->db->from('inventory as inventory');

        $this->db->join(
            'item_category_master',
            'item_category_master.category_id = inventory.category_id',
            'left'
        );

        $this->db->join(
            'item_group_master',
            'item_group_master.group_id = inventory.group_id',
            'left'
        );

        $this->db->order_by("inventory.inventory_id", "desc");
        if ($limit > 0) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    public function get_inventory_report($uid, $filters = array())
    {
        $this->db->select(
            'inventory.*, 
         item_category_master.category_name, 
         item_group_master.group_name'
        );

        $this->db->from('inventory as inventory');
        $this->db->join(
            'item_category_master',
            'item_category_master.category_id = inventory.category_id',
            'left'
        );
        $this->db->join(
            'item_group_master',
            'item_group_master.group_id = inventory.group_id',
            'left'
        );
        // Inventory is a company-wide shared table — all users see all items
        // (same as get_inventory() which has no uid filter)

        if (!empty($filters['item_name'])) {
            $this->db->where('inventory.item_name', $filters['item_name']);
        }

        if (!empty($filters['unit'])) {
            $this->db->where('inventory.unit', $filters['unit']);
        }

        if (!empty($filters['item_type'])) {
            $this->db->where('inventory.item_type', $filters['item_type']);
        }

        $this->db->order_by('inventory.inventory_id', 'desc');

        return $this->db->get()->result();
    }

    public function get_inventory_filter_options($uid)
    {
        $this->db->select('item_name, unit, item_type');
        $this->db->from('inventory');
        // Inventory is company-wide — no uid filter needed
        $this->db->order_by('item_name', 'asc');
        $rows = $this->db->get()->result();

        $options = array(
            'item_names' => array(),
            'units' => array(),
            'item_types' => array()
        );

        foreach ($rows as $row) {
            if (isset($row->item_name) && trim($row->item_name) !== '') {
                $options['item_names'][trim($row->item_name)] = trim($row->item_name);
            }
            if (isset($row->unit) && trim($row->unit) !== '') {
                $options['units'][trim($row->unit)] = trim($row->unit);
            }
            if (isset($row->item_type) && trim($row->item_type) !== '') {
                $options['item_types'][trim($row->item_type)] = trim($row->item_type);
            }
        }

        asort($options['item_names']);
        asort($options['units']);
        asort($options['item_types']);

        return $options;
    }



    public function get_customer()
    {
        $this->db->select('*');
        $this->db->from('customer');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_gst($uid)
    {
        $this->db->select('gst_per');
        $this->db->distinct();
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_gst_class($uid)
    {
        $this->db->select('gst_class');
        $this->db->from('gst_classes');
        //$this->db->where('uid', $uid);
        $this->db->order_by("gst_class", "asc");
        $query = $this->db->get();
        $numbers = array();
        //$sort_gst=array();
        foreach ($query->result() as $value) {
            $sort = floatval($value->gst_class);
            $numbers[] = $sort;
        }

        sort($numbers);
        $sort_gst = array();
        $arrlength = count($numbers);
        for ($x = 0; $x < $arrlength; $x++) {
            $sort_gst[] = array('gst_class' => $numbers[$x] . '%');
        }
        return $sort_gst;
    }

    public function get_expense_data($uid)
    {
        $this->db->select('*');
        $this->db->from('expense');
        //$this->db->where('uid', $uid);
        $this->db->order_by("expense_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_product_name($keyword)
    {
        $this->db->distinct();
        $this->db->select("code");
        $this->db->like('code', $keyword, 'after');
        $query = $this->db->get('inventory');
        return $query->result();
    }

    public function get_product_part_name($uid)
    {
        //        $this->db->distinct();
        //        $this->db->select("code,inventory_id");
        //        $this->db->where('uid', $uid);
        ////        $this->db->like('code', $keyword, 'after');
        //        $query = $this->db->get('inventory');
        //        return $query->result();

        $this->db->select('*');
        $this->db->from('barcode_master');
        $this->db->where('status', 0);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_product_part_name_edit($uid)
    {
        $this->db->select('*');
        $this->db->from('barcode_master');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_item_name($uid)
    {
        $this->db->select('code, item_name');
        $this->db->from('inventory');
        $this->db->order_by('code', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_stock_by_item_name($item_name, $uid)
    {
        $this->db->select('stock');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $this->db->where('code', $item_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_last_inventory_number($uid)
    {
        $this->db->select('COUNT(uid)');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        //        $this->db->order_by("inventory_id", "DESC");
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function get_cost_price_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select_sum('cost_price');
        $this->db->from('inventory');
        $this->db->where('date_added >=', $from_date);
        $this->db->where('date_added <=', $to_date);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_sell_price_report_by_date($from_date, $to_date, $uid)
    {
        $this->db->select_sum('sell_price');
        $this->db->from('inventory');
        $this->db->where('date_added >=', $from_date);
        $this->db->where('date_added <=', $to_date);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_expense_amount_report_by_date($from_date, $to_date, $expense_category, $uid)
    {
        //echo $expense_category;die();
        $this->db->select_sum('expense_amount');
        $this->db->from('expense');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        if ($expense_category) {
            $this->db->where('expense_category', $expense_category);
        }
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function add_customer_wise_rate($data_rate_cust_wise)
    {
        return $this->db->insert('customer_wise_rate', $data_rate_cust_wise);
    }

    public function customer_rate_wise_check($customer_id_fk, $inventory_id_fk)
    {
        $this->db->select('customer_wise_rate_id');
        $this->db->from('customer_wise_rate');
        $this->db->where('customer_id_fk', $customer_id_fk);
        $this->db->where('inventory_id_fk', $inventory_id_fk);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_customer_wise_rate_data()
    {
        $this->db->select('*');
        $this->db->from('customer_wise_rate');
        $this->db->join('inventory', 'inventory.inventory_id=customer_wise_rate.inventory_id_fk');
        $this->db->join('customer', 'customer.customer_id=customer_wise_rate.customer_id_fk');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_customer_rate_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('customer_wise_rate');
        $this->db->where('customer_wise_rate_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit_customer_wise_rate($data_rate_cust_wise, $customer_wise_rate_id)
    {
        $this->db->where('customer_wise_rate_id', $customer_wise_rate_id);
        $this->db->update('customer_wise_rate', $data_rate_cust_wise);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_customer_wise_rate_by_id($id)
    {
        $this->db->where('customer_wise_rate_id', $id);
        $this->db->delete('customer_wise_rate');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_barcode_id_barcode_master($item)
    {

        $this->db->select('*');
        $this->db->from('barcode_master');
        $this->db->order_by('barcode_master_id', 'desc');
        $this->db->where('item', $item);
        $this->db->limit('1');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_barcode_master($data)
    {
        return $this->db->insert('barcode_master', $data);
    }

    public function get_history_product_barcode_id($barcode_no)
    {
        $this->db->select('barcode_master.*,customer.fullname,invoice.*,invoice_total.id');
        $this->db->from('barcode_master');
        $this->db->where('barcode', $barcode_no);
        $this->db->join('invoice', 'invoice.product_name=barcode_master.barcode', 'left');
        $this->db->join('customer', 'customer.customer_id=invoice.customer_id', 'left');
        $this->db->join('invoice_total', 'invoice_total.number_fk=invoice.invoice_number', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_product_barcode()
    {
        $this->db->select('*');
        $this->db->from('barcode_master');
        $this->db->where('status', 0);
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_inventory_stock_count($item_name)
    {
        $this->db->set('stock', 'GREATEST(0, stock-1)', FALSE);
        $this->db->where('code', $item_name);
        $this->db->update('inventory');
    }

    public function increase_inventory_stock($item)
    {
        $this->db->set('stock', 'stock+1', FALSE);
        $this->db->where('code', $item);
        $this->db->update('inventory');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_barcode_barcode_master($item)
    {
        $this->db->select('*');
        $this->db->from('barcode_master');

        $this->db->where('item', $item);
        $this->db->order_by('barcode_master_id', 'desc');
        $this->db->limit('1');
        $query = $this->db->get();

        return $query->row_array();
    }

    public function get_all_barcode_for_autocomplete()
    {
        $this->db->select('barcode');
        $this->db->from('barcode_master');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_barcode_for_invalid_entry()
    {
        $this->db->select('barcode');
        $this->db->from('barcode_master');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_monthyearwise_record($month_year, $uid)
    {

        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        //print_r($newmonthyear_str);die();
        $this->db->select('*');
        $this->db->from('expense');
        $this->db->like('date', $newmonthyear_str, 'both');
        //$this->db->where('uid', $uid);
        $this->db->order_by("expense_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    // Add this method to your Inventory model (inventory.php)
    public function get_filtered_inventory($uid = null, $search_item = null, $item_type = null, $stock_status = null, $sort_by = 'date_added', $limit = 0)
    {
        $this->db->select('inventory.*, item_category_master.category_name, item_group_master.group_name');
        $this->db->from('inventory as inventory');

        // Join with other tables
        $this->db->join('item_category_master', 'item_category_master.category_id = inventory.category_id', 'left');
        $this->db->join('item_group_master', 'item_group_master.group_id = inventory.group_id', 'left');

        // Apply search filter
        if (!empty($search_item)) {
            $this->db->group_start();
            $this->db->like('inventory.code', $search_item);
            $this->db->or_like('inventory.prod_description', $search_item);
            $this->db->or_like('inventory.item_name', $search_item);
            $this->db->group_end();
        }

        // Apply item type filter
        if (!empty($item_type)) {
            $this->db->where('inventory.item_type', $item_type);
        }

        // Apply stock status filter
        if (!empty($stock_status)) {
            if ($stock_status == 'low') {
                $this->db->where('inventory.stock <=', 5);
            } elseif ($stock_status == 'ok') {
                $this->db->where('inventory.stock >', 5);
            }
        }

        // Apply sorting
        switch ($sort_by) {
            case 'stock':
                $this->db->order_by('inventory.stock', 'ASC');
                break;
            case 'cost_price':
                $this->db->order_by('inventory.cost_price', 'DESC');
                break;
            case 'sell_price':
                $this->db->order_by('inventory.sell_price', 'DESC');
                break;
            case 'date_added':
            default:
                $this->db->order_by('inventory.date_added', 'DESC');
                break;
        }

        if ($limit > 0) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    public function get_filtered_inventory_for_export($uid = null, $search_item = null, $item_type = null, $stock_status = null, $sort_by = 'date_added')
    {
        $this->db->select('inventory.*, item_category_master.category_name, item_group_master.group_name');
        $this->db->from('inventory as inventory');

        $this->db->join('item_category_master', 'item_category_master.category_id = inventory.category_id', 'left');
        $this->db->join('item_group_master', 'item_group_master.group_id = inventory.group_id', 'left');

        // Apply filters
        if (!empty($search_item)) {
            $this->db->group_start();
            $this->db->like('inventory.code', $search_item);
            $this->db->or_like('inventory.prod_description', $search_item);
            $this->db->or_like('inventory.item_name', $search_item);
            $this->db->group_end();
        }

        if (!empty($item_type)) {
            $this->db->where('inventory.item_type', $item_type);
        }

        if (!empty($stock_status)) {
            if ($stock_status == 'low') {
                $this->db->where('inventory.stock <=', 5);
            } elseif ($stock_status == 'ok') {
                $this->db->where('inventory.stock >', 5);
            }
        }

        return $this->db->get()->result_array();
    }

    public function get_total_inventory_count($uid)
    {
        $this->db->where('uid', $uid);
        $this->db->from('inventory');
        return $this->db->count_all_results();
    }
}
