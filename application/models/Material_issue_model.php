<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Material_issue_model extends CI_Model
{

    private $inventory_table = 'inventory';
    private $issue_slip_table = 'material_issue_slips';
    private $issue_items_table = 'material_issue_items';
    private $stock_ledger_table = 'stock_ledger';
    private $verification_table = 'stock_verifications';
    private $verification_items_table = 'stock_verification_items';
    private $purchase_stock_table = 'purchase_stock';
    private $users_table = 'user';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate next Material Issue Slip number
     */
    public function generate_issue_no()
    {
        $prefix = 'MIS-' . date('Y') . '-';
        $this->db->select_max('issue_no');
        $this->db->like('issue_no', $prefix, 'after');
        $query = $this->db->get($this->issue_slip_table);
        $result = $query->row();

        if ($result && $result->issue_no) {
            $last_number = intval(substr($result->issue_no, -3));
            $next_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next_number = '001';
        }

        return $prefix . $next_number;
    }

    /**
     * Generate next Stock Verification number
     */
    public function generate_verification_no()
    {
        $prefix = 'SV-' . date('Y') . '-';
        $this->db->select_max('verification_no');
        $this->db->like('verification_no', $prefix, 'after');
        $query = $this->db->get($this->verification_table);
        $result = $query->row();

        if ($result && $result->verification_no) {
            $last_number = intval(substr($result->verification_no, -3));
            $next_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next_number = '001';
        }

        return $prefix . $next_number;
    }

    /**
     * Generate next Material Return Note (MRN) number
     */
    public function generate_mrn_no()
    {
        $prefix = 'MRN-' . date('Y') . '-';
        $this->db->select_max('issue_no');
        $this->db->like('issue_no', $prefix, 'after');
        $query = $this->db->get($this->issue_slip_table);
        $result = $query->row();

        if ($result && $result->issue_no) {
            $last_number = intval(substr($result->issue_no, -3));
            $next_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next_number = '001';
        }

        return $prefix . $next_number;
    }


    /**
     * Create new Material Issue Slip
     */
    public function create_issue_slip($issue_data, $items_data)
    {
        // Start transaction
        $this->db->trans_start();

        // Generate issue number
        $issue_data['issue_no'] = $this->generate_issue_no();

        // Calculate totals
        $total_qty = 0;
        $total_items = count($items_data);

        foreach ($items_data as $item) {
            $total_qty += $item['quantity'];
        }

        $issue_data['total_items'] = $total_items;
        $issue_data['total_qty'] = $total_qty;
        // uid is already set by the controller via $issue_data['uid']

        // Insert issue slip
        $this->db->insert($this->issue_slip_table, $issue_data);
        $issue_id = $this->db->insert_id();

        // Insert items and update stock
        foreach ($items_data as $item) {
            $item['issue_id'] = $issue_id;
            $item['uid'] = $issue_data['uid'];
            $item['total_amount'] = $item['quantity'] * $item['unit_price'];
            $this->db->insert($this->issue_items_table, $item);

            // Update inventory stock and log to ledger (with BOM explosion if applicable)
            $this->process_stock_update_for_item(
                $item['inventory_id_fk'],
                $item['quantity'],
                'issue',
                $issue_data['joborder_number'] ?? null,
                $issue_data['issue_no'],
                $issue_data['issued_to'],
                $issue_data['uid']
            );
        }

        // Complete transaction
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Process stock updates and ledger entries for an issued item (with BOM explosion if available)
     */
    public function process_stock_update_for_item($inventory_id, $quantity, $type, $joborder_number, $issue_no, $issued_to, $uid)
    {
        // 1. Get finished good item details (code, name)
        $inventory = $this->get_item_details($inventory_id);
        if (!$inventory) {
            return;
        }
        $product_code = $inventory['code'];
        $product_name = $inventory['item_name'];

        // 2. Fetch Job Order reference and project code to find matching BOM
        $project_code = '';
        $oc_number = '';
        if (!empty($joborder_number)) {
            $jo_total = $this->db->select('*')
                                 ->from('joborder_total')
                                 ->where('number_fk', $joborder_number)
                                 ->get()
                                 ->row_array();
            if ($jo_total) {
                $project_code = $jo_total['project_code'];
                $oc_number = !empty($jo_total['oc_number']) ? $jo_total['oc_number'] : $jo_total['so_reference'];
            }
        }

        // 3. Find candidate BOMs matching this finished good code/name and project/SO
        $fg_codes = array_filter(array($product_code, $product_name));
        $bom = null;
        if (!empty($fg_codes)) {
            $this->db->select('*');
            $this->db->from('bom_total');
            $this->db->where('uid', $uid);
            $this->db->where('send_to_mrp >=', 1);
            $this->db->where_in('system', $fg_codes);
            
            $this->db->group_start();
                $this->db->where('oc_number', $oc_number);
                if (!empty($project_code)) {
                    $this->db->or_where('project_code', $project_code);
                }
                $this->db->or_group_start();
                    $this->db->group_start();
                        $this->db->where('oc_number', '');
                        $this->db->or_where('oc_number IS NULL', null, false);
                    $this->db->group_end();
                    $this->db->group_start();
                        $this->db->where('project_code', '');
                        $this->db->or_where('project_code IS NULL', null, false);
                    $this->db->group_end();
                $this->db->group_end();
            $this->db->group_end();
            
            $this->db->order_by('id', 'DESC');
            $this->db->limit(1);
            $bom = $this->db->get()->row_array();
        }

        if ($bom) {
            // Explode BOM and process each component
            $bom_no = $bom['number_fk'];
            $bom_items = $this->db->select('b.*, i.inventory_id')
                                  ->from('bom b')
                                  ->join('inventory i', 'i.code = b.product_name AND i.uid = b.uid', 'left')
                                  ->where('b.number', $bom_no)
                                  ->where('b.uid', $uid)
                                  ->get()
                                  ->result_array();

            foreach ($bom_items as $b_item) {
                if (empty($b_item['inventory_id'])) {
                    continue;
                }
                // BOM quantity is per 1 unit of finished good
                $comp_qty = floatval($b_item['quantity']) * floatval($quantity);
                
                if ($type == 'return') {
                    $this->update_inventory_stock($b_item['inventory_id'], $comp_qty, 'issue', $joborder_number);
                    
                    $this->add_to_stock_ledger(
                        $issue_no,
                        $b_item['inventory_id'],
                        abs($comp_qty),
                        'RETURN',
                        'Material Return (BOM Component of ' . $product_code . ') from ' . $issued_to,
                        $uid
                    );
                } else {
                    $this->update_inventory_stock($b_item['inventory_id'], $comp_qty, $type, $joborder_number);

                    // Add entry to stock ledger
                    $transaction_type = ($type == 'issue') ? 'ISSUE' : 'RECEIPT';
                    $ledger_ref = ($type == 'issue') ? $issue_no : $issue_no . '-CNL';
                    $remarks = ($type == 'issue') 
                        ? 'Material Issue (BOM Component of ' . $product_code . ') to ' . $issued_to
                        : 'Reversal: Deletion/Cancellation of Issue Slip ' . $issue_no . ' (BOM Component of ' . $product_code . ')';

                    $this->add_to_stock_ledger(
                        $ledger_ref,
                        $b_item['inventory_id'],
                        $comp_qty,
                        $transaction_type,
                        $remarks,
                        $uid
                    );
                }
            }
        } else {
            // No BOM -> process the item itself
            if ($type == 'return') {
                $this->update_inventory_stock($inventory_id, $quantity, 'issue', $joborder_number);
                
                $this->add_to_stock_ledger(
                    $issue_no,
                    $inventory_id,
                    abs($quantity),
                    'RETURN',
                    'Material Return from ' . $issued_to,
                    $uid
                );
            } else {
                $this->update_inventory_stock($inventory_id, $quantity, $type, $joborder_number);

                $transaction_type = ($type == 'issue') ? 'ISSUE' : 'RECEIPT';
                $ledger_ref = ($type == 'issue') ? $issue_no : $issue_no . '-CNL';
                $remarks = ($type == 'issue') 
                    ? 'Material Issue to ' . $issued_to
                    : 'Reversal: Deletion/Cancellation of Issue Slip ' . $issue_no;

                $this->add_to_stock_ledger(
                    $ledger_ref,
                    $inventory_id,
                    $quantity,
                    $transaction_type,
                    $remarks,
                    $uid
                );
            }
        }
    }

    /**
     * Update inventory stock and allocations
     */
    private function update_inventory_stock($inventory_id, $quantity, $type = 'issue', $joborder_number = null)
    {
        if ($type == 'issue') {
            if ($quantity < 0) {
                // Scenario B: Material Return Note (MRN)
                // Simply increase physical stock and increase available stock
                $this->db->query(
                    "UPDATE {$this->db->dbprefix}inventory 
                     SET stock = GREATEST(0, stock - ?),
                         available_stock = GREATEST(0, available_stock - ?)
                     WHERE inventory_id = ?",
                    array($quantity, $quantity, $inventory_id)
                );
                return TRUE;
            }

            // Check if there is an active allocation for this Job Order (or Sales Order fallback) and inventory item
            $jo_id = 0;
            $salesorder_number = null;
            if (!empty($joborder_number)) {
                $jo_total = $this->db->select('id, salesorder_number, oc_number, so_reference')
                                     ->where('number_fk', $joborder_number)
                                     ->get('joborder_total', 1)
                                     ->row_array();
                $jo_id = $jo_total ? (int)$jo_total['id'] : 0;
                $salesorder_number = $jo_total ? $jo_total['salesorder_number'] : null;
                
                if (empty($salesorder_number) && $jo_total) {
                    $salesorder_number = !empty($jo_total['oc_number']) ? $jo_total['oc_number'] : $jo_total['so_reference'];
                }
            }

            $allocation = null;
            if ($jo_id > 0) {
                $allocation = $this->db->where('joborder_id', $jo_id)
                                       ->where('inventory_id', $inventory_id)
                                       ->where('status !=', 'cancelled')
                                       ->get('stock_allocations', 1)
                                       ->row_array();
            }

            if (!$allocation && !empty($salesorder_number)) {
                // Fallback to Sales Order level allocation if no Job Order level allocation exists yet
                $allocation = $this->db->where('joborder_id', 0)
                                       ->where('inventory_id', $inventory_id)
                                       ->like('notes', 'Sales Order Allocation: ' . $salesorder_number, 'both')
                                       ->where('status !=', 'cancelled')
                                       ->get('stock_allocations', 1)
                                       ->row_array();
            }

            if ($allocation) {
                // Update allocation record: convert allocated stock to issued stock
                $issued_qty = floatval($allocation['issued_quantity']) + $quantity;
                $pending_qty = max(0, floatval($allocation['allocated_quantity']) - $issued_qty);
                $status = ($pending_qty <= 0) ? 'completed' : 'partially_issued';

                $update_alloc_data = array(
                    'issued_quantity' => $issued_qty,
                    'pending_quantity' => $pending_qty,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                );

                // If it was a Sales Order level allocation, link it to the Job Order now
                if ($allocation['joborder_id'] == 0 && $jo_id > 0) {
                    $update_alloc_data['joborder_id'] = $jo_id;
                }

                $this->db->where('id', $allocation['id'])
                         ->update('stock_allocations', $update_alloc_data);


                $this->db->query(
                    "UPDATE {$this->db->dbprefix}inventory 
                     SET stock = GREATEST(0, stock - ?),
                         allocated_stock = GREATEST(0, CASE WHEN (stock - ?) >= 0 THEN LEAST(stock - ?, allocated_stock - ?) ELSE 0 END)
                     WHERE inventory_id = ?",
                    array($quantity, $quantity, $quantity, $quantity, $inventory_id)
                );
                $this->db->query(
                    "UPDATE {$this->db->dbprefix}inventory 
                     SET available_stock = GREATEST(0, stock - IFNULL(allocated_stock, 0))
                     WHERE inventory_id = ?",
                    array($inventory_id)
                );
            } else {
                // No active allocation: just deduct physical stock and update available stock
                $this->db->query(
                    "UPDATE {$this->db->dbprefix}inventory 
                     SET stock = GREATEST(0, stock - ?)
                     WHERE inventory_id = ?",
                    array($quantity, $inventory_id)
                );
                $this->db->query(
                    "UPDATE {$this->db->dbprefix}inventory 
                     SET available_stock = GREATEST(0, stock - IFNULL(allocated_stock, 0))
                     WHERE inventory_id = ?",
                    array($inventory_id)
                );
            }
        } elseif ($type == 'receipt') {
            // Restore stock and check if there is a matching allocation to restore
            $jo_id = 0;
            $salesorder_number = null;
            if (!empty($joborder_number)) {
                $jo_total = $this->db->select('id, salesorder_number, oc_number, so_reference')
                                     ->where('number_fk', $joborder_number)
                                     ->get('joborder_total', 1)
                                     ->row_array();
                $jo_id = $jo_total ? (int)$jo_total['id'] : 0;
                $salesorder_number = $jo_total ? $jo_total['salesorder_number'] : null;
                
                if (empty($salesorder_number) && $jo_total) {
                    $salesorder_number = !empty($jo_total['oc_number']) ? $jo_total['oc_number'] : $jo_total['so_reference'];
                }
            }

            $allocation = null;
            if ($jo_id > 0) {
                $allocation = $this->db->where('joborder_id', $jo_id)
                                       ->where('inventory_id', $inventory_id)
                                       ->where('status !=', 'cancelled')
                                       ->get('stock_allocations', 1)
                                       ->row_array();
            }

            if (!$allocation && !empty($salesorder_number)) {
                $allocation = $this->db->where('joborder_id', 0)
                                       ->where('inventory_id', $inventory_id)
                                       ->like('notes', 'Sales Order Allocation: ' . $salesorder_number, 'both')
                                       ->where('status !=', 'cancelled')
                                       ->get('stock_allocations', 1)
                                       ->row_array();
            }

            if ($allocation) {
                // We are returning issued stock back to allocated stock
                $new_issued_qty = max(0, floatval($allocation['issued_quantity']) - $quantity);
                $new_pending_qty = max(0, floatval($allocation['allocated_quantity']) - $new_issued_qty);
                $status = ($new_issued_qty <= 0) ? 'allocated' : 'partially_issued';

                $update_alloc_data = array(
                    'issued_quantity' => $new_issued_qty,
                    'pending_quantity' => $new_pending_qty,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                );

                $this->db->where('id', $allocation['id'])
                         ->update('stock_allocations', $update_alloc_data);

                // Update inventory: increase physical stock and increase allocated stock
                $this->db->query(
                    "UPDATE {$this->db->dbprefix}inventory 
                     SET stock = stock + ?,
                         allocated_stock = allocated_stock + ?,
                         available_stock = GREATEST(0, stock - allocated_stock)
                     WHERE inventory_id = ?",
                    array($quantity, $quantity, $inventory_id)
                );
            } else {
                // No active allocation: just increase physical stock
                $this->db->query(
                    "UPDATE {$this->db->dbprefix}inventory 
                     SET stock = stock + ?,
                         available_stock = GREATEST(0, stock - IFNULL(allocated_stock, 0))
                     WHERE inventory_id = ?",
                    array($quantity, $inventory_id)
                );
            }
        } elseif ($type == 'adjustment') {
            $this->db->query(
                "UPDATE {$this->db->dbprefix}inventory 
                 SET stock = GREATEST(0, ?),
                     available_stock = GREATEST(0, GREATEST(0, ?) - IFNULL(allocated_stock, 0))
                 WHERE inventory_id = ?",
                array($quantity, $quantity, $inventory_id)
            );
        }

        return TRUE;
    }

    /**
     * Add entry to stock ledger
     */
    private function add_to_stock_ledger($reference_no, $inventory_id, $quantity, $transaction_type, $remarks = '', $uid = 1)
    {
        // Get current stock balance
        $this->db->select('stock, sell_price as unit_price, code');
        $this->db->where('inventory_id', $inventory_id);
        $item = $this->db->get($this->inventory_table)->row();

        if (!$item) return false;

        // Calculate balance after transaction
        $balance_qty = $item->stock;

        $ledger_data = array(
            'transaction_type' => $transaction_type,
            'reference_no' => $reference_no,
            'item_code' => $item->code,
            'quantity' => ($transaction_type == 'ISSUE' || $transaction_type == 'SALES') ? -$quantity : $quantity,
            'balance_quantity' => $balance_qty,
            'transaction_date' => date('Y-m-d H:i:s'),
            'remarks' => $remarks,
            'uid' => $uid
        );

        return $this->db->insert($this->stock_ledger_table, $ledger_data);
    }

    /**
     * Get all material issue slips
     */
    public function get_issue_slips($filters = array())
    {
        $this->db->select('mis.*, u.username as issued_by_name');
        $this->db->from($this->issue_slip_table . ' mis');
        $this->db->join($this->users_table . ' u', 'u.user_id = mis.uid', 'left');

        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && empty($filters['date_from'])) {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('mis.issue_date >=', $fy_from);
            $this->db->where('mis.issue_date <=', $fy_to);
        }

        // Apply filters
        if (!empty($filters['date_from'])) {
            $this->db->where('mis.issue_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('mis.issue_date <=', $filters['date_to']);
        }
        if (!empty($filters['issued_to'])) {
            $this->db->like('mis.issued_to', $filters['issued_to']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('mis.status', $filters['status']);
        }
        if (!empty($filters['project_code'])) {
            $this->db->where('mis.project_code', $filters['project_code']);
        }
        if (!empty($filters['department'])) {
            $this->db->where('mis.department', $filters['department']);
        }

        $this->db->order_by('mis.issue_date', 'DESC');
        $this->db->order_by('mis.issue_id', 'DESC');

        return $this->db->get()->result_array();
    }

    public function get_material_issue_report($from_date = '', $to_date = '', $uid = null)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && empty($from_date)) {
            $from_date = $fy_year . '-04-01';
            $to_date   = ($fy_year + 1) . '-03-31 23:59:59';
        }
        $has_joborder_number = $this->db->field_exists('joborder_number', $this->issue_slip_table);

        $select = array(
            'mis.issue_id',
            'mis.issue_no',
            'mis.issue_date',
            'mis.status',
            'i.code as item_code',
            'i.item_name',
            'i.unit',
            'mii.quantity as issued_qty',
            'COALESCE(NULLIF(mii.unit_price, 0), i.cost_price, 0) as cost_price',
            '(mii.quantity * COALESCE(NULLIF(mii.unit_price, 0), i.cost_price, 0)) as total_cost',
            'COALESCE(NULLIF(mis.project_code, \' \'), NULLIF(jt.project_code, \' \'), NULLIF(sot.project_code, \' \'), \'\') as project_code',
            'COALESCE(
                (
                    SELECT p.project_name
                    FROM ' . $this->db->dbprefix('project') . ' p
                    WHERE p.project_code = COALESCE(NULLIF(mis.project_code, \' \'), NULLIF(jt.project_code, \' \'), NULLIF(sot.project_code, \' \'), \'___none___\')
                    LIMIT 1
                ),
                \'\'
            ) as project_name',
            'COALESCE(NULLIF(jt.so_reference, \' \'), NULLIF(jt.oc_number, \' \'), NULLIF(sot.number_fk, \' \'), \'\') as salesorder_number',
            'mis.uid as userid',
            'u.username as username',
            '(
                SELECT GROUP_CONCAT(DISTINCT bt.number_fk SEPARATOR \', \')
                FROM ' . $this->db->dbprefix('bom_total') . ' bt
                WHERE (bt.project_code = COALESCE(NULLIF(mis.project_code, \' \'), NULLIF(jt.project_code, \' \'), NULLIF(sot.project_code, \' \'), \'___none___\'))
                   OR (bt.po_number = COALESCE(NULLIF(jt.so_reference, \' \'), NULLIF(jt.oc_number, \' \'), NULLIF(sot.number_fk, \' \'), \'___none___\'))
                   OR (bt.oc_number = COALESCE(NULLIF(jt.so_reference, \' \'), NULLIF(jt.oc_number, \' \'), NULLIF(sot.number_fk, \' \'), \'___none___\'))
            ) as bom_numbers'
        );

        if ($has_joborder_number) {
            $select[] = 'mis.joborder_number';
            $select[] = 'COALESCE(jo.quantity, 0) as joborder_qty';
        } else {
            $select[] = '"" as joborder_number';
            $select[] = '0 as joborder_qty';
        }

        $this->db->select(implode(', ', $select), false);
        $this->db->from($this->issue_items_table . ' mii');
        $this->db->join($this->issue_slip_table . ' mis', 'mis.issue_id = mii.issue_id', 'inner');
        $this->db->join($this->inventory_table . ' i', 'i.inventory_id = mii.inventory_id_fk', 'left');

        if ($has_joborder_number) {
            $this->db->join('joborder jo', 'jo.number = mis.joborder_number AND jo.product_name = i.code', 'left');
            $this->db->join('joborder_total jt', 'jt.number_fk = mis.joborder_number', 'left');
        } else {
            $this->db->join('joborder_total jt', '1=0', 'left'); // dummy join
        }

        $this->db->join('salesorder_total sot', '(sot.number_fk = COALESCE(NULLIF(jt.so_reference, \'\'), NULLIF(jt.oc_number, \'\'), NULLIF(jt.salesorder_number, \'\'), \'___none___\')) OR (sot.project_code = mis.project_code AND mis.project_code != \'\')', 'left');
        $this->db->join('user u', 'u.user_id = mis.uid', 'left');

        if ($uid !== null) {
            $this->db->where('mis.uid', (int) $uid);
        }
        if ($from_date !== '') {
            $this->db->where('mis.issue_date >=', $from_date);
        }
        if ($to_date !== '') {
            $this->db->where('mis.issue_date <=', $to_date);
        }

        // Exclude return/MRN notes from regular issue report (only show regular issues)
        $this->db->where('mis.issue_no NOT LIKE', 'MRN-%');
        $this->db->where('mis.status !=', 'cancelled');

        $this->db->group_by('mii.issue_item_id'); // Prevent duplicate entries due to sot join
        $this->db->order_by('mis.issue_date', 'DESC');
        $this->db->order_by('mis.issue_id', 'DESC');

        return $this->db->get()->result();
    }

    public function get_material_allocation_report($from_date = '', $to_date = '', $uid = null)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && empty($from_date)) {
            $from_date = $fy_year . '-04-01';
            $to_date   = ($fy_year + 1) . '-03-31 23:59:59';
        }

        $this->db->select("
            sa.id as allocation_id,
            sa.product_code as item_code,
            i.item_name,
            sa.allocated_quantity,
            sa.issued_quantity,
            sa.pending_quantity,
            COALESCE(i.cost_price, 0) as cost_price,
            (sa.allocated_quantity * COALESCE(i.cost_price, 0)) as total_cost,
            sa.allocated_date,
            sa.status,
            sa.notes,
            sa.uid as userid,
            u.username as username,
            COALESCE(jt.number_fk, 'N/A') as joborder_number,
            COALESCE(
                NULLIF(jt.so_reference, ''),
                NULLIF(jt.oc_number, ''),
                NULLIF(sot.number_fk, ''),
                CASE 
                    WHEN sa.notes LIKE '%SO: %' THEN TRIM(SUBSTRING_INDEX(sa.notes, 'SO: ', -1))
                    WHEN sa.notes LIKE '%Sales Order Allocation: %' THEN TRIM(SUBSTRING_INDEX(sa.notes, 'Sales Order Allocation: ', -1))
                    ELSE ''
                END,
                ''
            ) as salesorder_number,
            COALESCE(NULLIF(jt.project_code, ''), NULLIF(sot.project_code, ''), '') as project_code,
            COALESCE(p.project_name, p2.project_name, '') as project_name,
            (
                SELECT GROUP_CONCAT(DISTINCT bt.number_fk SEPARATOR ', ')
                FROM " . $this->db->dbprefix('bom_total') . " bt
                WHERE (bt.project_code = COALESCE(NULLIF(jt.project_code, ''), NULLIF(sot.project_code, ''), '___none___'))
                   OR (bt.po_number = COALESCE(NULLIF(jt.so_reference, ''), NULLIF(jt.oc_number, ''), NULLIF(sot.number_fk, ''), '___none___'))
                   OR (bt.oc_number = COALESCE(NULLIF(jt.so_reference, ''), NULLIF(jt.oc_number, ''), NULLIF(sot.number_fk, ''), '___none___'))
            ) as bom_numbers
        ", false);
        $this->db->from('stock_allocations sa');
        $this->db->join('joborder_total jt', 'jt.id = sa.joborder_id', 'left');
        $this->db->join('project p', 'p.project_code = jt.project_code', 'left');
        $this->db->join($this->db->dbprefix('salesorder_total') . ' sot', "sot.number_fk = CASE 
            WHEN sa.notes LIKE '%SO: %' THEN TRIM(SUBSTRING_INDEX(sa.notes, 'SO: ', -1))
            WHEN sa.notes LIKE '%Sales Order Allocation: %' THEN TRIM(SUBSTRING_INDEX(sa.notes, 'Sales Order Allocation: ', -1))
            ELSE ''
        END", 'left', false);
        $this->db->join('project p2', 'p2.project_code = sot.project_code', 'left');
        $this->db->join('user u', 'u.user_id = sa.uid', 'left');
        $this->db->join('inventory i', 'i.code = sa.product_code', 'left');

        if ($uid !== null) {
            $this->db->where('sa.uid', (int) $uid);
        }
        if ($from_date !== '') {
            $this->db->where('sa.allocated_date >=', $from_date);
        }
        if ($to_date !== '') {
            $this->db->where('sa.allocated_date <=', $to_date);
        }

        $this->db->where_not_in('sa.status', array('completed', 'cancelled'));

        $this->db->order_by('sa.allocated_date', 'DESC');
        $this->db->order_by('sa.id', 'DESC');

        return $this->db->get()->result();
    }

    public function get_material_reversal_report($from_date = '', $to_date = '', $uid = null)
    {
        $has_joborder_number = $this->db->field_exists('joborder_number', $this->issue_slip_table);

        $select = array(
            'mis.issue_id',
            'mis.issue_no',
            'mis.issue_date',
            'mis.status',
            'i.code as item_code',
            'i.item_name',
            'i.unit',
            'mii.quantity as issued_qty',
            'COALESCE(NULLIF(mii.unit_price, 0), i.cost_price, 0) as cost_price',
            '(mii.quantity * COALESCE(NULLIF(mii.unit_price, 0), i.cost_price, 0)) as total_cost',
            'COALESCE(NULLIF(mis.project_code, \' \'), NULLIF(jt.project_code, \' \'), NULLIF(sot.project_code, \' \'), \'\') as project_code',
            'COALESCE(
                (
                    SELECT p.project_name
                    FROM ' . $this->db->dbprefix('project') . ' p
                    WHERE p.project_code = COALESCE(NULLIF(mis.project_code, \' \'), NULLIF(jt.project_code, \' \'), NULLIF(sot.project_code, \' \'), \'___none___\')
                    LIMIT 1
                ),
                \'\'
            ) as project_name',
            'COALESCE(NULLIF(jt.so_reference, \' \'), NULLIF(jt.oc_number, \' \'), NULLIF(sot.number_fk, \' \'), \'\') as salesorder_number',
            'mis.uid as userid',
            'u.username as username',
            '(
                SELECT GROUP_CONCAT(DISTINCT bt.number_fk SEPARATOR \', \')
                FROM ' . $this->db->dbprefix('bom_total') . ' bt
                WHERE (bt.project_code = COALESCE(NULLIF(mis.project_code, \' \'), NULLIF(jt.project_code, \' \'), NULLIF(sot.project_code, \' \'), \'___none___\'))
                   OR (bt.po_number = COALESCE(NULLIF(jt.so_reference, \' \'), NULLIF(jt.oc_number, \' \'), NULLIF(sot.number_fk, \' \'), \'___none___\'))
                   OR (bt.oc_number = COALESCE(NULLIF(jt.so_reference, \' \'), NULLIF(jt.oc_number, \' \'), NULLIF(sot.number_fk, \' \'), \'___none___\'))
            ) as bom_numbers'
        );

        if ($has_joborder_number) {
            $select[] = 'mis.joborder_number';
            $select[] = 'COALESCE(jo.quantity, 0) as joborder_qty';
        } else {
            $select[] = '"" as joborder_number';
            $select[] = '0 as joborder_qty';
        }

        $this->db->select(implode(', ', $select), false);
        $this->db->from($this->issue_items_table . ' mii');
        $this->db->join($this->issue_slip_table . ' mis', 'mis.issue_id = mii.issue_id', 'inner');
        $this->db->join($this->inventory_table . ' i', 'i.inventory_id = mii.inventory_id_fk', 'left');

        if ($has_joborder_number) {
            $this->db->join('joborder jo', 'jo.number = mis.joborder_number AND jo.product_name = i.code', 'left');
            $this->db->join('joborder_total jt', 'jt.number_fk = mis.joborder_number', 'left');
        } else {
            $this->db->join('joborder_total jt', '1=0', 'left'); // dummy join
        }

        $this->db->join('salesorder_total sot', '(sot.number_fk = COALESCE(NULLIF(jt.so_reference, \'\'), NULLIF(jt.oc_number, \'\'), NULLIF(jt.salesorder_number, \'\'), \'___none___\')) OR (sot.project_code = mis.project_code AND mis.project_code != \'\')', 'left');
        $this->db->join('user u', 'u.user_id = mis.uid', 'left');

        if ($uid !== null) {
            $this->db->where('mis.uid', (int) $uid);
        }
        if ($from_date !== '') {
            $this->db->where('mis.issue_date >=', $from_date);
        }
        if ($to_date !== '') {
            $this->db->where('mis.issue_date <=', $to_date);
        }

        // Only show return/MRN notes OR cancelled slips (reversals)
        $this->db->group_start();
            $this->db->where('mis.status', 'cancelled');
            $this->db->or_like('mis.issue_no', 'MRN-', 'after');
        $this->db->group_end();

        $this->db->group_by('mii.issue_item_id'); // Prevent duplicate entries due to sot join
        $this->db->order_by('mis.issue_date', 'DESC');
        $this->db->order_by('mis.issue_id', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get inventory item by item code
     */
    public function get_inventory_item_by_code($item_code)
    {
        $this->db->select('*');
        $this->db->from($this->inventory_table);
        $this->db->where('code', $item_code);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get total issued quantity for a given inventory item (all material issue slips)
     */
    public function get_issued_quantity_for_inventory($inventory_id, $joborder_number = null)
    {
        $this->db->select('IFNULL(SUM(mi.quantity),0) as total_issued');
        $this->db->from($this->issue_items_table . ' mi');
        $this->db->where('mi.inventory_id_fk', $inventory_id);

        if (!empty($joborder_number) && $this->db->field_exists('joborder_number', $this->issue_slip_table)) {
            $this->db->join($this->issue_slip_table . ' mis', 'mis.issue_id = mi.issue_id', 'inner');
            $this->db->where('mis.joborder_number', $joborder_number);
            $this->db->where('mis.status !=', 'cancelled');
        }

        $query = $this->db->get();
        $row = $query->row_array();
        return isset($row['total_issued']) ? floatval($row['total_issued']) : 0;
    }

    /**
     * Get issue slip by ID
     */
    public function get_issue_slip($issue_id)
    {
        // Get issue slip header
        $this->db->select('mis.*, u.username as issued_by_name, u2.username	 as approved_by_name');
        $this->db->from($this->issue_slip_table . ' mis');
        $this->db->join($this->users_table . ' u', 'u.user_id  = mis.uid', 'left');
        $this->db->join($this->users_table . ' u2', 'u2.user_id = mis.approved_by', 'left');
        $this->db->where('mis.issue_id', $issue_id);
        $issue_slip = $this->db->get()->row_array();

        if ($issue_slip) {
            // Get issue items
            $this->db->select('mii.*, i.item_name, i.code, i.unit, i.stock as current_stock');
            $this->db->from($this->issue_items_table . ' mii');
            $this->db->join($this->inventory_table . ' i', 'i.inventory_id = mii.inventory_id_fk');
            $this->db->where('mii.issue_id', $issue_id);
            $issue_slip['items'] = $this->db->get()->result_array();
        }

        return $issue_slip;
    }

    /**
     * Approve issue slip
     */
    public function approve_issue_slip($issue_id, $approved_by)
    {
        $data = array(
            'status' => 'issued',
            'approved_by' => $approved_by,
            'approved_date' => date('Y-m-d H:i:s')
        );

        $this->db->where('issue_id', $issue_id);
        $this->db->where('status', 'draft');
        return $this->db->update($this->issue_slip_table, $data);
    }

    /**
     * Cancel issue slip
     */
    public function cancel_issue_slip($issue_id, $remarks = '')
    {
        // Start transaction
        $this->db->trans_start();

        // Get issue slip
        $this->db->select('issue_no, joborder_number, issued_to, uid');
        $this->db->where('issue_id', $issue_id);
        $issue_slip = $this->db->get($this->issue_slip_table)->row_array();

        // Get items to restore stock
        $this->db->select('inventory_id_fk, quantity');
        $this->db->where('issue_id', $issue_id);
        $items = $this->db->get($this->issue_items_table)->result_array();

        // Restore stock for each item
        foreach ($items as $item) {
            $this->process_stock_update_for_item(
                $item['inventory_id_fk'],
                $item['quantity'],
                'receipt',
                $issue_slip['joborder_number'] ?? null,
                $issue_slip['issue_no'],
                $issue_slip['issued_to'] ?? '',
                $issue_slip['uid'] ?? 1
            );
        }

        // Update issue slip status
        $update_data = array(
            'status' => 'cancelled',
            'remarks' => $remarks
        );

        $this->db->where('issue_id', $issue_id);
        $this->db->update($this->issue_slip_table, $update_data);

        // Complete transaction
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Get stock summary
     */
    public function get_stock_summary($filters = array())
    {
        $this->db->select('i.*, cat.category_name, grp.group_name');
        $this->db->from($this->inventory_table . ' i');
        $this->db->join('item_category_master cat', 'cat.category_id = i.category_id', 'left');
        $this->db->join('item_group_master grp', 'grp.group_id = i.group_id', 'left');

        // Apply filters
        if (!empty($filters['category_id'])) {
            $this->db->where('i.category_id', $filters['category_id']);
        }
        if (!empty($filters['group_id'])) {
            $this->db->where('i.group_id', $filters['group_id']);
        }
        if (!empty($filters['item_type'])) {
            $this->db->where('i.item_type', $filters['item_type']);
        }
        if (isset($filters['low_stock']) && $filters['low_stock']) {
            $this->db->where('i.stock <=', 5);
            $this->db->where('i.stock >', 0);
        }
        if (isset($filters['out_of_stock']) && $filters['out_of_stock']) {
            $this->db->where('i.stock <= 0');
        }

        $this->db->order_by('i.item_name');

        return $this->db->get()->result_array();
    }

    /**
     * Get inventory items with stock > 0
     */
    public function get_inventory_items_with_stock()
    {
        $this->db->select('inventory_id, item_name, code, unit, stock, cost_price, sell_price');
        $this->db->from($this->inventory_table);
        $this->db->where('stock >', 0);
        $this->db->order_by('item_name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get all inventory items
     */
    public function get_all_inventory_items()
    {
        $this->db->select('inventory_id, item_name, code, unit, stock, cost_price, sell_price');
        $this->db->from($this->inventory_table);
        $this->db->order_by('item_name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get item details by ID
     */
    public function get_item_details($inventory_id)
    {
        $this->db->select('*');
        $this->db->where('inventory_id', $inventory_id);
        return $this->db->get($this->inventory_table)->row_array();
    }

    /**
     * Get stock verification history (list of all verifications)
     */
    public function get_verification_history($uid = null)
    {
        $this->db->select('sv.*, u.username as verified_by_name');
        $this->db->from($this->verification_table . ' sv');
        $this->db->join('user u', 'u.user_id = sv.uid', 'left');
        if ($uid) {
            $this->db->where('sv.uid', $uid);
        }
        $this->db->order_by('sv.verification_date', 'DESC');
        $this->db->order_by('sv.verification_id', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Get items for a specific verification
     */
    public function get_verification_items($verification_id)
    {
        $this->db->select('svi.*, i.item_name, i.code');
        $this->db->from($this->verification_items_table . ' svi');
        $this->db->join($this->inventory_table . ' i', 'i.inventory_id = svi.inventory_id_fk', 'left');
        $this->db->where('svi.verification_id', $verification_id);
        return $this->db->get()->result_array();
    }

    /**
     * Create stock verification
     */
    public function create_stock_verification($verification_data, $items_data)
    {
        // Start transaction
        $this->db->trans_start();

        // Generate verification number
        $verification_data['verification_no'] = $this->generate_verification_no();
        $verification_data['uid'] = $this->session->userdata('uid') ?: 1;

        // Calculate totals
        $total_items = count($items_data);
        $total_variance = 0;

        foreach ($items_data as $item) {
            $variance_value = abs($item['variance'] * $item['unit_price']);
            $total_variance += $variance_value;
        }

        $verification_data['total_items'] = $total_items;
        $verification_data['total_variance'] = $total_variance;

        // Insert verification header
        $this->db->insert($this->verification_table, $verification_data);
        $verification_id = $this->db->insert_id();

        // Insert verification items
        foreach ($items_data as $item) {
            $item['verification_id'] = $verification_id;
            $item['uid'] = $this->session->userdata('uid') ?: 1;
            $item['variance_value'] = $item['variance'] * $item['unit_price'];
            $this->db->insert($this->verification_items_table, $item);
        }

        // Complete transaction
        $this->db->trans_complete();

        return $this->db->trans_status() ? $verification_id : false;
    }

    /**
     * Adjust stock based on verification
     */
    public function adjust_stock_from_verification($verification_id)
    {
        // Start transaction
        $this->db->trans_start();

        // Get verification items
        $this->db->select('svi.*, i.code');
        $this->db->from($this->verification_items_table . ' svi');
        $this->db->join($this->inventory_table . ' i', 'i.inventory_id = svi.inventory_id_fk');
        $this->db->where('svi.verification_id', $verification_id);
        $this->db->where('svi.variance !=', 0);
        $items = $this->db->get()->result_array();

        // Get verification details for reference
        $this->db->select('verification_no, remarks');
        $this->db->where('verification_id', $verification_id);
        $verification = $this->db->get($this->verification_table)->row_array();

        // Process each item
        foreach ($items as $item) {
            if ($item['variance'] != 0) {
                // Update inventory stock to physical count
                $this->db->set('stock', $item['physical_stock']);
                $this->db->where('inventory_id', $item['inventory_id_fk']);
                $this->db->update($this->inventory_table);

                // Add adjustment entry to ledger
                $ledger_data = array(
                    'transaction_type' => 'ADJUSTMENT',
                    'reference_no' => $verification['verification_no'],
                    'item_code' => $item['code'],
                    'quantity' => $item['variance'],
                    'balance_quantity' => $item['physical_stock'],
                    'transaction_date' => date('Y-m-d H:i:s'),
                    'remarks' => 'Stock Adjustment - ' . $verification['remarks'],
                    'uid' => $this->session->userdata('uid') ?: 1
                );

                $this->db->insert($this->stock_ledger_table, $ledger_data);
            }
        }

        // Update verification status
        $this->db->set('status', 'completed');
        $this->db->where('verification_id', $verification_id);
        $this->db->update($this->verification_table);

        // Complete transaction
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Get stock ledger for an item
     */
    public function get_stock_ledger($inventory_id, $date_from = null, $date_to = null)
    {
        // Get item code and prices
        $this->db->select('code, cost_price, sell_price');
        $this->db->where('inventory_id', $inventory_id);
        $item = $this->db->get($this->inventory_table)->row();

        if (!$item) return array();

        $this->db->select('sl.*');
        $this->db->from($this->stock_ledger_table . ' sl');
        $this->db->where('sl.item_code', $item->code);

        if ($date_from) {
            $this->db->where('DATE(sl.transaction_date) >=', $date_from);
        }
        if ($date_to) {
            $this->db->where('DATE(sl.transaction_date) <=', $date_to);
        }

        $this->db->order_by('sl.transaction_date', 'ASC');
        $this->db->order_by('sl.ledger_id', 'ASC');

        $entries = $this->db->get()->result_array();

        foreach ($entries as &$entry) {
            $entry['purchase_price'] = (float)$item->cost_price;
            $entry['selling_price'] = (float)$item->sell_price;
        }

        return $entries;
    }

    /**
     * Get low stock items (stock <= 10)
     */
    public function get_low_stock_items()
    {
        $this->db->select('i.*, cat.category_name, grp.group_name');
        $this->db->from($this->inventory_table . ' i');
        $this->db->join('item_category_master cat', 'cat.category_id = i.category_id', 'left');
        $this->db->join('item_group_master grp', 'grp.group_id = i.group_id', 'left');
        $this->db->where('i.stock <=', 5);
        $this->db->order_by('i.stock', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Get stock valuation report
     */
    public function get_stock_valuation_report()
    {
        $this->db->select('i.*, 
            (i.stock * i.cost_price) as total_cost_value,
            (i.stock * i.sell_price) as total_selling_value,
            cat.category_name, 
            grp.group_name');
        $this->db->from($this->inventory_table . ' i');
        $this->db->join('item_category_master cat', 'cat.category_id = i.category_id', 'left');
        $this->db->join('item_group_master grp', 'grp.group_id = i.group_id', 'left');
        $this->db->where('i.stock >', 0);
        $this->db->order_by('total_cost_value', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Get categories for filter
     */
    public function get_categories()
    {
        $this->db->select('category_id, category_name');
        $this->db->from('item_category_master');
        $this->db->order_by('category_name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get groups for filter
     */
    public function get_groups()
    {
        $this->db->select('group_id, group_name');
        $this->db->from('item_group_master');
        $this->db->order_by('group_name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get item by code
     */
    public function get_item_by_code($code)
    {
        $this->db->select('*');
        $this->db->where('code', $code);
        return $this->db->get($this->inventory_table)->row_array();
    }

    /**
     * Get recent stock movements
     */
    public function get_recent_stock_movements($limit = 10)
    {
        $this->db->select('sl.*, i.item_name');
        $this->db->from($this->stock_ledger_table . ' sl');
        $this->db->join($this->inventory_table . ' i', 'i.code = sl.item_code', 'left');
        $this->db->order_by('sl.transaction_date', 'DESC');
        $this->db->order_by('sl.ledger_id', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get dashboard summary
     */
    public function get_dashboard_summary()
    {
        $summary = array();

        // Total items
        $this->db->select('COUNT(*) as total_items');
        $result = $this->db->get($this->inventory_table)->row();
        $summary['total_items'] = $result->total_items;

        // Items in stock
        $this->db->select('COUNT(*) as in_stock_items');
        $this->db->where('stock >', 0);
        $result = $this->db->get($this->inventory_table)->row();
        $summary['in_stock_items'] = $result->in_stock_items;

        // Low stock items
        $this->db->select('COUNT(*) as low_stock_items');
        $this->db->where('stock >', 0);
        $this->db->where('stock <=', 5);
        $result = $this->db->get($this->inventory_table)->row();
        $summary['low_stock_items'] = $result->low_stock_items;

        // Out of stock items
        $this->db->select('COUNT(*) as out_of_stock_items');
        $this->db->where('stock <= 0');
        $result = $this->db->get($this->inventory_table)->row();
        $summary['out_of_stock_items'] = $result->out_of_stock_items;

        // Today's issues
        $this->db->select('COUNT(*) as today_issues');
        $this->db->where('DATE(issue_date)', date('Y-m-d'));
        $result = $this->db->get($this->issue_slip_table)->row();
        $summary['today_issues'] = $result->today_issues;

        // Pending approvals
        $this->db->select('COUNT(*) as pending_approvals');
        $this->db->where('status', 'draft');
        $result = $this->db->get($this->issue_slip_table)->row();
        $summary['pending_approvals'] = $result->pending_approvals;

        return $summary;
    }

    /**
     * Get MRP data: items across all active job orders with stock and pending details
     */
    public function get_mrp_data($uid)
    {
        $p  = $this->db->dbprefix;
        $uid = (int) $uid;

        $fy_year = $this->session->userdata('fy_year');
        $fy_where = "";
        if (!empty($fy_year)) {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $fy_where = " AND jt.date >= {$this->db->escape($fy_from)} AND jt.date <= {$this->db->escape($fy_to)} ";
        }

        // One summary row per item across all active job orders
        $sql = "
            SELECT
                j.product_name                               AS item_code,
                IFNULL(i.item_name, j.product_name)          AS item_name,
                IFNULL(i.unit, '')                           AS unit,
                IFNULL(i.available_stock, IFNULL(i.stock, 0)) AS available_stock,
                IFNULL(i.allocated_stock, 0)                 AS allocated_stock,
                i.inventory_id,
                SUM(j.quantity)                              AS total_required_qty,
                MAX(IF(jt.bom_id > 0, 1, 0))                 AS has_bom,
                IFNULL((
                    SELECT SUM(mii.quantity)
                    FROM {$p}material_issue_items mii
                    INNER JOIN {$p}material_issue_slips mis ON mis.issue_id = mii.issue_id
                    WHERE mis.status != 'cancelled'
                      AND mii.inventory_id_fk = i.inventory_id
                ), 0)                                        AS total_issued_qty
            FROM {$p}joborder j
            LEFT JOIN {$p}inventory i ON i.code = j.product_name
            LEFT JOIN {$p}joborder_total jt ON jt.number_fk = j.number
            WHERE j.product_name IS NOT NULL
              AND j.product_name != ''
              AND j.product_name != '__HEADING__'
              AND jt.uid = {$uid}
              {$fy_where}
            GROUP BY j.product_name, i.item_name, i.unit, i.available_stock, i.allocated_stock, i.stock, i.inventory_id
            ORDER BY i.item_name, j.product_name
        ";

        $items = $this->db->query($sql)->result_array();

        // Check once whether the joborder_number column exists in material_issue_slips
        $has_jo_number_col = $this->db->field_exists('joborder_number', $this->issue_slip_table);

        // Build the jo_issued_qty subquery conditionally
        if ($has_jo_number_col) {
            $jo_issued_subquery = "
                IFNULL((
                    SELECT SUM(mii.quantity)
                    FROM {$p}material_issue_items mii
                    INNER JOIN {$p}material_issue_slips mis ON mis.issue_id = mii.issue_id
                    WHERE mis.status != 'cancelled'
                      AND mis.joborder_number = j.number
                      AND mii.inventory_id_fk = (
                          SELECT inv.inventory_id FROM {$p}inventory inv
                          WHERE inv.code = j.product_name LIMIT 1
                      )
                ), 0)";
        } else {
            $jo_issued_subquery = "0";
        }

        // JO-level breakdown per item for the expandable detail rows
        $jo_sql = "
            SELECT
                j.product_name                               AS item_code,
                j.number                                     AS jo_number,
                jt.date                                      AS jo_date,
                j.quantity                                   AS jo_qty,
                IFNULL(j.unit, '')                           AS unit,
                IFNULL(jt.customer_code, '')                 AS customer_code,
                IFNULL(jt.system, '')                        AS system_name,
                IFNULL(jt.project_code, '')                  AS project_code,
                COALESCE(NULLIF(jt.salesorder_number, ''), NULLIF(jt.oc_number, ''), jt.so_reference, '') AS salesorder_number,
                IFNULL(c.company_name, '')                   AS company_name,
                {$jo_issued_subquery}                        AS jo_issued_qty
            FROM {$p}joborder j
            LEFT JOIN {$p}joborder_total jt ON jt.number_fk = j.number
            LEFT JOIN {$p}customer c ON c.customer_id = jt.customer_id_fk
            WHERE j.product_name IS NOT NULL
              AND j.product_name != ''
              AND j.product_name != '__HEADING__'
              AND jt.uid = {$uid}
            ORDER BY j.product_name, jt.date DESC
        ";

        $jo_rows = $this->db->query($jo_sql)->result_array();

        // Build a map of item_code => [ jo rows ]
        $jo_map = array();
        foreach ($jo_rows as $row) {
            $jo_map[$row['item_code']][] = $row;
        }

        // Fetch allocations across active Job Orders for this user to calculate status
        $allocations_map = array();
        $alloc_rows = $this->db->select('sa.product_code, SUM(sa.allocated_quantity) as total_allocated, SUM(sa.issued_quantity) as total_issued_allocated')
                               ->from('stock_allocations sa')
                               ->join('joborder_total jt', 'jt.id = sa.joborder_id')
                               ->where('sa.status !=', 'cancelled')
                               ->where('jt.uid', $uid)
                               ->group_by('sa.product_code')
                               ->get()
                               ->result_array();
        foreach ($alloc_rows as $row) {
            $allocations_map[$row['product_code']] = array(
                'allocated' => floatval($row['total_allocated']),
                'issued' => floatval($row['total_issued_allocated'])
            );
        }

        // Fetch generated PR items for this user
        $pr_map = array();
        $pr_rows = $this->db->select('pri.item_code, pri.pr_no, pr.pr_id, pr.approval_status')
                            ->from('purchase_requisition_items pri')
                            ->join('purchase_requisition pr', 'pr.pr_id = pri.pr_id', 'inner')
                            ->where('pr.approval_status !=', 'Rejected')
                            ->where('pr.created_by', $uid)
                            ->get()->result_array();
        foreach ($pr_rows as $pr_row) {
            $pr_map[$pr_row['item_code']] = $pr_row;
        }

        // Attach calculated fields and JO detail to each summary row
        foreach ($items as &$item) {
            $item['total_required_qty'] = floatval($item['total_required_qty']);
            $item['total_issued_qty']   = floatval($item['total_issued_qty']);
            $item['available_stock']    = floatval($item['available_stock']);
            $item['allocated_stock']    = floatval($item['allocated_stock']);
            
            $item_alloc = isset($allocations_map[$item['item_code']]) ? $allocations_map[$item['item_code']] : array('allocated' => 0, 'issued' => 0);
            $item['allocated_qty'] = max(0, $item_alloc['allocated'] - $item_alloc['issued']);

            $item['pending_qty']        = max(0, $item['total_required_qty'] - $item['total_issued_qty']);
            $item['shortage']           = max(0, $item['pending_qty'] - $item['available_stock'] - $item['allocated_qty']);
            $item['jo_details']         = isset($jo_map[$item['item_code']]) ? $jo_map[$item['item_code']] : array();
            
            // Map keys expected by the material_issue/mrp.php view
            $item['raw_material_code']  = $item['item_code'];
            $item['raw_material_name']  = $item['item_name'];
            $item['gross_requirement']  = floatval($item['total_required_qty']);
            $item['current_stock']      = floatval($item['available_stock']);
            $item['net_requirement']    = floatval($item['pending_qty']);
            $item['has_bom']            = (intval($item['has_bom']) > 0);

            if (isset($pr_map[$item['item_code']])) {
                $item['has_pr']  = true;
                $item['pr_info'] = $pr_map[$item['item_code']];
            } else {
                $item['has_pr']  = false;
                $item['pr_info'] = null;
            }
            
            if ($item['pending_qty'] <= 0) {
                $item['status'] = 'fulfilled';
            } else if ($item['allocated_qty'] >= $item['pending_qty']) {
                $item['status'] = 'allocated';
            } else if ($item['shortage'] > 0) {
                $item['status'] = 'shortage';
            } else {
                $item['status'] = 'ok';
            }
        }
        unset($item);

        return $items;
    }

    /**
     * Get unique departments from material issue slips
     */
    public function get_unique_departments()
    {
        $this->db->distinct();
        $this->db->select('department');
        $this->db->from($this->issue_slip_table);
        $this->db->where('department IS NOT NULL', null, false);
        $this->db->where('department !=', '');
        $this->db->order_by('department', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        
        $departments = array();
        foreach ($result as $row) {
            if (!empty($row['department'])) {
                $departments[] = $row['department'];
            }
        }
        return $departments;
    }

    /**
     * Get unique issued_to from material issue slips
     */
    public function get_unique_issued_to()
    {
        $this->db->distinct();
        $this->db->select('issued_to');
        $this->db->from($this->issue_slip_table);
        $this->db->where('issued_to IS NOT NULL', null, false);
        $this->db->where('issued_to !=', '');
        $this->db->order_by('issued_to', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        
        $users = array();
        foreach ($result as $row) {
            if (!empty($row['issued_to'])) {
                $users[] = $row['issued_to'];
            }
        }
        return $users;
    }

    /**
     * Get material issue slips by month and year
     */
    public function get_monthyearwise_record($month_year, $uid = null)
    {
        // Parse month_year string (format: "MonthName-YYYY" or "MM-YYYY")
        $monthyear_arr = explode('-', $month_year);
        if (count($monthyear_arr) == 2) {
            $month_part = trim($monthyear_arr[0]);
            $year_part = trim($monthyear_arr[1]);
            
            // Convert month name to number (e.g., "April" -> "04")
            $nmonth = date('m', strtotime($month_part));
            $newmonthyear_str = $year_part . '-' . $nmonth;
        } else {
            // Fallback to current month if format is invalid
            $newmonthyear_str = date('Y-m');
        }

        $this->db->select('mis.*, u.username as issued_by_name');
        $this->db->from($this->issue_slip_table . ' mis');
        $this->db->join($this->users_table . ' u', 'u.user_id = mis.uid', 'left');
        
        // Filter by month and year
        $this->db->like('mis.issue_date', $newmonthyear_str, 'after');
        
        // Filter by user if specified
        if ($uid !== null) {
            $this->db->where('mis.uid', $uid);
        }

        $this->db->order_by('mis.issue_date', 'DESC');
        $this->db->order_by('mis.issue_id', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Run MRP for a specific Sales Order by exploding its BOM(s)
     */
    public function get_sales_order_mrp_data($so_number, $uid)
    {
        $p = $this->db->dbprefix;
        $uid = (int) $uid;

        // Check if Projects module is enabled/visible for the current user
        $session_data_head = $this->session->userdata('session_data_head');
        $user_role_id = $session_data_head['result']['role'] ?? null;
        
        $has_projects_permission = false;
        if ($user_role_id) {
            $perms = $this->db->select('grp_perm')
                              ->from('permission')
                              ->where('role_id_fk', $user_role_id)
                              ->where('grp_perm', 'projects')
                              ->get()
                              ->row_array();
            if ($perms) {
                $has_projects_permission = true;
            }
        }
        
        if (!$has_projects_permission) {
            $session_permissions = $session_data_head['permission'] ?? array();
            if (in_array('projects', $session_permissions)) {
                $has_projects_permission = true;
            }
        }

        // 1. Get Sales Order Header info
        $so_total = $this->db->select('st.*, c.company_name, c.fullname')
                             ->from('salesorder_total st')
                             ->join('customer c', 'c.customer_id = st.customer_id_fk', 'left')
                             ->where('st.number_fk', $so_number)
                             ->get()
                             ->row_array();
        if (!$so_total) {
            return null;
        }

        // If projects module is not enabled/visible, ignore project_code and work with Sales Order Number
        $project_code_val = '';
        if ($has_projects_permission && !empty($so_total['project_code'])) {
            $project_code_val = $so_total['project_code'];
        }

        // 2. Get items in Sales Order
        $so_items = $this->db->select('s.*, i.item_name as finished_good_name, i.unit as finished_good_unit, i.stock as finished_good_stock')
                             ->from('salesorder s')
                             ->join('inventory i', 'i.code = s.product_name', 'left')
                             ->where('s.number', $so_number)
                             ->where('s.product_name !=', '__HEADING__')
                             ->get()
                             ->result_array();

        // 3. Find associated BOMs and explode them
        $exploded_items = array();
        $boms_list = array();

        // Gather finished goods from Sales Order (mapping codes and names to support text-based matching)
        $so_fg_qtys = array();
        $fg_codes = array();
        foreach ($so_items as $so_item) {
            $qty = floatval($so_item['quantity']);
            $so_fg_qtys[$so_item['product_name']] = $qty;
            $fg_codes[] = $so_item['product_name'];
            
            if (!empty($so_item['finished_good_name'])) {
                $clean_name = trim($so_item['finished_good_name']);
                $so_fg_qtys[$clean_name] = $qty;
                $fg_codes[] = $clean_name;
            }
        }

        // Fetch candidate BOMs matching the project code, OC number, or generic finished good codes/names
        // (global — all users, not filtered by uid so any team member's BOM is included)
        $this->db->select('bt.*');
        $this->db->from('bom_total bt');
        // Removed uid filter — explode BOMs from all users
        $this->db->where('bt.send_to_mrp >=', 1);
        $this->db->group_start();
            // Direct matches by Sales Order or Project Code
            $this->db->where('bt.oc_number', $so_number);
            if (!empty($project_code_val)) {
                $this->db->or_where('bt.project_code', $project_code_val);
            }
            // Generic matches (system matches, but no project/SO is specified on the BOM)
            if (!empty($fg_codes)) {
                $this->db->or_group_start();
                    $this->db->where_in('bt.system', $fg_codes);
                    $this->db->group_start();
                        $this->db->where('bt.oc_number', '');
                        $this->db->or_where('bt.oc_number IS NULL', null, false);
                    $this->db->group_end();
                    $this->db->group_start();
                        $this->db->where('bt.project_code', '');
                        $this->db->or_where('bt.project_code IS NULL', null, false);
                    $this->db->group_end();
                $this->db->group_end();
            }
        $this->db->group_end();
        $boms = $this->db->get()->result_array();

        // Filter BOMs to only explode the latest revision of each base BOM
        $latest_boms = array();
        foreach ($boms as $bom) {
            $bom_no = $bom['number_fk'];
            if (preg_match('/^(.*?)-R(\d+)$/', $bom_no, $matches)) {
                $base = $matches[1];
                $rev = intval($matches[2]);
            } else {
                $base = $bom_no;
                $rev = 0;
            }
            if (!isset($latest_boms[$base]) || $rev > $latest_boms[$base]['rev']) {
                $latest_boms[$base] = array(
                    'bom' => $bom,
                    'rev' => $rev
                );
            }
        }
        $boms = array();
        foreach ($latest_boms as $base => $data) {
            $boms[] = $data['bom'];
        }

        $processed_bom_numbers = array();

        foreach ($boms as $bom) {
            $bom_no = $bom['number_fk'];
            $system = $bom['system'];

            // Determine if this BOM is specific to a finished good item
            if (!empty($system) && in_array($system, $fg_codes)) {
                $fg_qty = $so_fg_qtys[$system] ?? 1;
                $multiplier = $fg_qty;
                $finished_good_label = '';
                foreach ($so_items as $so_item) {
                    if ($so_item['product_name'] == $system || (isset($so_item['finished_good_name']) && trim($so_item['finished_good_name']) == $system)) {
                        $finished_good_label = $so_item['finished_good_name'] ?: $system;
                        break;
                    }
                }
            } else {
                // General project/order level BOM: explode exactly once
                if (isset($processed_bom_numbers[$bom_no])) {
                    continue;
                }
                $multiplier = 1;
                $finished_good_label = 'Project BOM';
            }

            $processed_bom_numbers[$bom_no] = true;
            $boms_list[$bom_no] = $bom_no;

            // Fetch BOM items (global across users, match by code OR item_name)
            $bom_items = $this->db->select('b.*, i.item_name as component_name, i.available_stock as available_stock, i.allocated_stock as allocated_stock, i.unit as component_unit, i.inventory_id')
                                  ->from('bom b')
                                  ->join('inventory i', 'i.code = b.product_name OR i.item_name = b.product_name', 'left')
                                  ->where('b.number', $bom_no)
                                  ->get()
                                  ->result_array();

            foreach ($bom_items as $b_item) {
                if (isset($b_item['product_name']) && $b_item['product_name'] === '__HEADING__') {
                    continue;
                }
                $comp_code = $b_item['product_name'];
                $req_qty = floatval($b_item['quantity']) * $multiplier;
                $stock = isset($b_item['available_stock']) ? floatval($b_item['available_stock']) : 0;
                $allocated_stock = isset($b_item['allocated_stock']) ? floatval($b_item['allocated_stock']) : 0;

                if (isset($exploded_items[$comp_code])) {
                    $exploded_items[$comp_code]['total_required_qty'] += $req_qty;
                } else {
                    $exploded_items[$comp_code] = array(
                        'item_code' => $comp_code,
                        'item_name' => $b_item['component_name'] ?: $b_item['description'] ?: $comp_code,
                        'unit' => $b_item['component_unit'] ?: $b_item['unit'] ?: '',
                        'total_required_qty' => $req_qty,
                        'available_stock' => $stock,
                        'allocated_stock' => $allocated_stock,
                        'inventory_id' => $b_item['inventory_id'],
                        'bom_source' => $bom_no,
                        'finished_good' => $finished_good_label
                    );
                }
            }
        }

        // Get job order numbers associated with this Sales Order to filter material issues
        $joborders = $this->db->select('number_fk')
                              ->from('joborder_total')
                              ->group_start()
                                  ->where('salesorder_number', $so_number)
                                  ->or_where('so_reference', $so_number)
                                  ->or_where('oc_number', $so_number)
                              ->group_end()
                              ->get()
                              ->result_array();
        $joborder_numbers = array_column($joborders, 'number_fk');

        // 4. Attach calculations (issued quantities & shortage calculations)
        $allocations_map = array();
        $alloc_rows = $this->db->select('sa.product_code, SUM(sa.allocated_quantity) as total_allocated, SUM(sa.issued_quantity) as total_issued_allocated')
                               ->from('stock_allocations sa')
                               ->join('joborder_total jt', 'jt.id = sa.joborder_id', 'left')
                               ->group_start()
                                   ->where('jt.salesorder_number', $so_number)
                                   ->or_where('jt.so_reference', $so_number)
                                   ->or_where('jt.oc_number', $so_number)
                                   ->or_group_start()
                                       ->where('sa.joborder_id', 0)
                                       ->like('sa.notes', 'Sales Order Allocation: ' . $so_number, 'both')
                                   ->group_end()
                               ->group_end()
                               ->where('sa.status !=', 'cancelled')
                               ->group_by('sa.product_code')
                               ->get()
                               ->result_array();

        foreach ($alloc_rows as $row) {
            $allocations_map[$row['product_code']] = array(
                'allocated' => floatval($row['total_allocated']),
                'issued' => floatval($row['total_issued_allocated'])
            );
        }

        // Fetch generated PR items for this Sales Order / Project
        $pr_map = array();
        $this->db->select('pri.item_code, pri.pr_no, pr.pr_id, pr.approval_status')
                 ->from('purchase_requisition_items pri')
                 ->join('purchase_requisition pr', 'pr.pr_id = pri.pr_id', 'inner')
                 ->where('pr.approval_status !=', 'Rejected');
        $this->db->group_start();
            $this->db->where('pr.so_no', $so_number);
            if (!empty($project_code_val)) {
                $this->db->or_where('pr.project_code', $project_code_val);
            }
        $this->db->group_end();
        $pr_rows = $this->db->get()->result_array();

        foreach ($pr_rows as $pr_row) {
            $pr_map[$pr_row['item_code']] = $pr_row;
        }

        $mrp_list = array();
        foreach ($exploded_items as $code => $item) {
            $issued_qty = 0;
            if ($item['inventory_id']) {
                $has_filter = false;
                $filter_project_code = '';
                if (!empty($project_code_val)) {
                    $filter_project_code = $project_code_val;
                    $has_filter = true;
                } else if (!empty($so_number)) {
                    $filter_project_code = $so_number;
                    $has_filter = true;
                }
                
                if (!empty($joborder_numbers)) {
                    $has_filter = true;
                }

                if ($has_filter) {
                    $this->db->select('IFNULL(SUM(mii.quantity), 0) as total_issued');
                    $this->db->from('material_issue_items mii');
                    $this->db->join('material_issue_slips mis', 'mis.issue_id = mii.issue_id', 'inner');
                    $this->db->where('mii.inventory_id_fk', $item['inventory_id']);
                    $this->db->where('mis.status !=', 'cancelled');
                    
                    $this->db->group_start();
                    $inner_filter = false;
                    if (!empty($filter_project_code)) {
                        $this->db->where('mis.project_code', $filter_project_code);
                        $inner_filter = true;
                    }
                    if (!empty($joborder_numbers)) {
                        if ($inner_filter) {
                            $this->db->or_where_in('mis.joborder_number', $joborder_numbers);
                        } else {
                            $this->db->where_in('mis.joborder_number', $joborder_numbers);
                        }
                    }
                    $this->db->group_end();

                    $issued_row = $this->db->get()->row_array();
                    $issued_qty = isset($issued_row['total_issued']) ? floatval($issued_row['total_issued']) : 0;
                }
            }

            $item['total_issued_qty'] = $issued_qty;
            
            $item_alloc = isset($allocations_map[$code]) ? $allocations_map[$code] : array('allocated' => 0, 'issued' => 0);
            $item['allocated_qty'] = max(0, $item_alloc['allocated'] - $item_alloc['issued']);

            $item['pending_qty'] = max(0, $item['total_required_qty'] - $item['total_issued_qty']);
            $item['shortage'] = max(0, $item['pending_qty'] - $item['available_stock'] - $item['allocated_qty']);

            if (isset($pr_map[$code])) {
                $item['has_pr']  = true;
                $item['pr_info'] = $pr_map[$code];
            } else {
                $item['has_pr']  = false;
                $item['pr_info'] = null;
            }

            // Set status
            if ($item['pending_qty'] <= 0) {
                $item['status'] = 'fulfilled';
            } elseif ($item['allocated_qty'] >= $item['pending_qty']) {
                $item['status'] = 'allocated';
            } elseif ($item['shortage'] > 0) {
                $item['status'] = 'shortage';
            } else {
                $item['status'] = 'ok';
            }

            $mrp_list[] = $item;
        }

        return array(
            'so_info' => $so_total,
            'so_items' => $so_items,
            'mrp_items' => $mrp_list,
            'associated_boms' => array_values($boms_list)
        );
    }

    /**
     * Delete issue slip and restore stock
     */
    public function delete_issue_slip($issue_id)
    {
        $this->db->trans_start();

        // Get issue slip details
        $this->db->select('status, issue_no, joborder_number, issued_to, uid');
        $this->db->where('issue_id', $issue_id);
        $issue_slip = $this->db->get($this->issue_slip_table)->row_array();

        if ($issue_slip) {
            // Get items
            $this->db->select('inventory_id_fk, quantity');
            $this->db->where('issue_id', $issue_id);
            $items = $this->db->get($this->issue_items_table)->result_array();

            // If the status is not cancelled, restore the stock
            if ($issue_slip['status'] !== 'cancelled') {
                foreach ($items as $item) {
                    $this->process_stock_update_for_item(
                        $item['inventory_id_fk'],
                        $item['quantity'],
                        'receipt',
                        $issue_slip['joborder_number'],
                        $issue_slip['issue_no'],
                        $issue_slip['issued_to'],
                        $issue_slip['uid']
                    );
                }
            }

            // If there's a joborder, we might need to reset joborder_total.material_issue_status if all issues for it are gone
            if (!empty($issue_slip['joborder_number'])) {
                // Check if any other non-cancelled issue slips exist for this job order
                $this->db->from($this->issue_slip_table);
                $this->db->where('joborder_number', $issue_slip['joborder_number']);
                $this->db->where('issue_id !=', $issue_id);
                $this->db->where('status !=', 'cancelled');
                $count = $this->db->count_all_results();
                
                if ($count == 0) {
                    $this->db->set('material_issue_status', 0);
                    $this->db->where('number_fk', $issue_slip['joborder_number']);
                    $this->db->update('joborder_total');
                }
            }

            // Delete items
            $this->db->where('issue_id', $issue_id);
            $this->db->delete($this->issue_items_table);

            // Delete slip
            $this->db->where('issue_id', $issue_id);
            $this->db->delete($this->issue_slip_table);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_detailed_issued_quantities($inventory_id, $joborder_number)
    {
        $has_joborder_number = $this->db->field_exists('joborder_number', $this->issue_slip_table);
        if (!$has_joborder_number) {
            return array(
                'gross_issued' => 0,
                'returned' => 0,
                'net_issued' => 0
            );
        }

        // Gross issued quantity (only positive quantities in non-cancelled slips)
        $this->db->select('IFNULL(SUM(mii.quantity), 0) as gross_issued');
        $this->db->from($this->issue_items_table . ' mii');
        $this->db->join($this->issue_slip_table . ' mis', 'mis.issue_id = mii.issue_id', 'inner');
        $this->db->where('mii.inventory_id_fk', $inventory_id);
        $this->db->where('mis.joborder_number', $joborder_number);
        $this->db->where('mis.status !=', 'cancelled');
        $this->db->where('mii.quantity >', 0);
        $gross_row = $this->db->get()->row_array();
        $gross_issued = isset($gross_row['gross_issued']) ? floatval($gross_row['gross_issued']) : 0;

        // Returned quantity (absolute sum of negative quantities in non-cancelled slips)
        $this->db->select('IFNULL(SUM(mii.quantity), 0) as returned');
        $this->db->from($this->issue_items_table . ' mii');
        $this->db->join($this->issue_slip_table . ' mis', 'mis.issue_id = mii.issue_id', 'inner');
        $this->db->where('mii.inventory_id_fk', $inventory_id);
        $this->db->where('mis.joborder_number', $joborder_number);
        $this->db->where('mis.status !=', 'cancelled');
        $this->db->where('mii.quantity <', 0);
        $ret_row = $this->db->get()->row_array();
        $returned = isset($ret_row['returned']) ? abs(floatval($ret_row['returned'])) : 0;

        return array(
            'gross_issued' => $gross_issued,
            'returned' => $returned,
            'net_issued' => max(0, $gross_issued - $returned)
        );
    }
}
