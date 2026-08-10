<?php
class Grn extends CI_Model
{
    // Get last GRN number
    public function get_last_grn_number($uid)
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('grn_total');
        // Removed uid filter — count all GRNs
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    // Get PO details
    public function get_po_details_details($po_number, $uid)
    {
        $this->db->select('supplier_id, number');
        $this->db->from('purchase_order');
        $this->db->where('number', $po_number);
        $this->db->group_by('purchase_order.number');
        $query = $this->db->get();
        return $query->row_array();
    }

    // Get GRN data
    public function get_grn_data($grn_number, $uid)
    {
        $this->db->select('g.*, inventory.item_name, inventory.unit');
        $this->db->from('grn g');
        $this->db->join('inventory', 'g.product_name = inventory.code AND (inventory.uid = g.uid OR inventory.uid = 1)', 'left');
        $this->db->group_start();
        $this->db->where('g.grn_number', $grn_number);
        $this->db->or_like('g.grn_number', $grn_number, 'after');
        $this->db->group_end();
        $query = $this->db->get();
        return $query->result();
    }

    // Get GRN data grouped
    public function get_grn_data_group_by($grn_number, $uid)
    {
        $this->db->select('g.*, s.company_name, s.fullname, s.address, s.gst, s.pancard, gt.total');
        $this->db->from('grn g');
        $this->db->join('supplier s', 's.supplier_id = g.supplier_id', 'left');
        $this->db->join('grn_total gt', 'gt.number_fk = g.grn_number', 'left');
        $this->db->group_start();
        $this->db->where('g.grn_number', $grn_number);
        $this->db->or_like('g.grn_number', $grn_number, 'after');
        $this->db->group_end();
        $this->db->group_by('g.grn_number');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_grn_supplier_contact($grn_number, $uid)
    {
        $this->db->select('s.email, s.mobile as mobile');
        $this->db->from('grn g');
        $this->db->join('supplier s', 's.supplier_id = g.supplier_id', 'left');
        $this->db->where('g.grn_number', $grn_number);
        // $this->db->where('g.uid', $uid);
        $this->db->order_by('g.grn_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Delete GRN by number
    public function delete_grn_by_grn_number($grn_number, $uid)
    {
        // $this->db->where('uid', $uid);
        $this->db->where('grn_number', $grn_number);
        $this->db->delete('grn');
        if ($this->db->affected_rows() >= '1') {
            $this->db->where('number_fk', $grn_number);
            // $this->db->where('uid', $uid);
            $this->db->delete('grn_total');
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
    // Add total GRN amount
    public function add_total_grn_amount($data_toatl_amount)
    {
        return $this->db->insert('grn_total', $data_toatl_amount);
    }

    // Get all PO data
    public function get_all_po_data($po_number, $uid)
    {
        // Get all PO items
        $this->db->select('*, quantity as ordered_quantity');
        $this->db->from('purchase_order');
        $this->db->where('number', $po_number);
        $query = $this->db->get();
        $po_items = $query->result();

        foreach ($po_items as $item) {
            // Extract first part of product name
            $product_parts = explode(' - ', $item->product_name);
            $base_product_name = trim($product_parts[0]);

            // Use LIKE to match the base product name
            $this->db->select('pending_quantity, received_quantity, quantity as grn_quantity');
            $this->db->from('grn');
            $this->db->where('po_number_fk', $po_number);
            $this->db->like('product_name', $base_product_name, 'after');
            $this->db->order_by('grn_id', 'DESC');
            $this->db->limit(1);
            $grn_query = $this->db->get();
            $grn_data = $grn_query->row_array();

            if ($grn_data) {
                $item->pending_quantity = $grn_data['pending_quantity'];
                $item->last_received_qty = $grn_data['received_quantity'];
                $item->grn_quantity = $grn_data['grn_quantity'];

                // Get total received
                $this->db->select('SUM(received_quantity) as total_received');
                $this->db->from('grn');
                $this->db->where('po_number_fk', $po_number);
                $this->db->like('product_name', $base_product_name, 'after');
                $received_query = $this->db->get();
                $received_data = $received_query->row_array();
                $item->total_received = $received_data['total_received'] ?? 0;
            } else {
                $item->pending_quantity = $item->ordered_quantity;
                $item->last_received_qty = 0;
                $item->grn_quantity = 0;
                $item->total_received = 0;
            }
        }

        return $po_items;
    }

    // Get inventory stock count
    public function get_inventory_stock_count($product_name, $uid)
    {
        $this->db->select('stock, allocated_stock, available_stock, uid');
        $this->db->from('inventory');
        // Search across all uid entries for this product
        $this->db->where('code', $product_name);
        $query = $this->db->get();
        $res = $query->row_array();
        return $res;
    }

    // Get GRN count
    public function get_grn_count($uid)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('created_at >=', $fy_from);
            $this->db->where('created_at <=', $fy_to);
        }
        $this->db->select('COUNT(*) as count');
        $this->db->from('grn_total');
        // Removed uid filter — count all GRNs across all users
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['count'] ?? 0;
    }

    // Add pending quantity to PO table
    public function add_pending_qty_to_po_table($pending_qty, $po_number_fk, $product_name, $uid)
    {
        $this->db->where('number', $po_number_fk);
        $this->db->where('uid', $uid);
        $this->db->where('product_name', $product_name);
        $this->db->update('purchase_order', $pending_qty);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    // Get all GRNs
    public function get_grn($uid)
    {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('gt.created_at >=', $fy_from);
            $this->db->where('gt.created_at <=', $fy_to);
        }
        $this->db->select('grn_id, grn_number, po_number_fk, date, company_name, fullname, total');
        $this->db->from('grn g');
        $this->db->join('supplier s', 's.supplier_id = g.supplier_id');
        $this->db->join('grn_total gt', 'gt.number_fk = g.grn_number');
        // $this->db->where('g.uid', $uid);
        // $this->db->where('gt.uid', $uid);
        $this->db->group_by('g.grn_number');
        $this->db->order_by("g.grn_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    // Get customer
    public function get_customer($uid)
    {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    // Get PO numbers
    public function get_po_number($uid)
    {
        $this->db->distinct();
        $this->db->select('po.number');
        $this->db->from('purchase_order po');
        $this->db->join('po_total pot', 'pot.number_fk = po.number', 'inner');
        $this->db->join('grn grn', 'po.number = grn.po_number_fk', 'left');
        $this->db->where('grn.grn_id IS NULL');
        $this->db->where('pot.approval_status', 'approved');
        $this->db->group_start();
        $this->db->where('po.po_pending_quantity', 'Y');
        $this->db->or_where('CAST(po.po_pending_quantity AS SIGNED) >', 0);
        $this->db->group_end();
        $this->db->group_by('po.number');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get PO numbers that still have pending items (received qty < ordered qty for at least one item).
     * Excludes POs where every line item's total received qty >= ordered qty.
     */
    public function get_po_number_with_pending($uid)
    {
        $p = $this->db->dbprefix;
        $uid = (int) $uid;

        $sql = "
            SELECT DISTINCT po.number
            FROM {$p}purchase_order po
            JOIN {$p}po_total pot ON pot.number_fk = po.number
            WHERE pot.approval_status = 'approved'
              AND IFNULL(po.is_archived, 0) != 1
            GROUP BY po.number
            HAVING SUM(
                CASE
                    WHEN po.po_pending_quantity = 'Y' THEN po.quantity
                    ELSE COALESCE(CAST(NULLIF(po.po_pending_quantity, '') AS DECIMAL(15, 4)), 0)
                END
            ) > 0
            ORDER BY po.number DESC
        ";

        return $this->db->query($sql)->result();
    }

    // Add customer
    public function add_customer($data_customer)
    {
        return $this->db->insert('customer', $data_customer);
    }

    // Customer check
    public function customer_check($mobile)
    {
        $this->db->select('mobile');
        $this->db->from('customer');
        $this->db->where('mobile', $mobile);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    // Add user
    public function add_user($data)
    {
        return $this->db->insert('user', $data);
    }

    // Get customer by mobile
    public function get_customer_by_mobile($mobile)
    {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->join('user', 'customer.customer_mobile=user.user_id');
        $this->db->where('customer_mobile', $mobile);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Get customer email
    public function get_customer_email($number)
    {
        $this->db->select('email');
        $this->db->from('quotation');
        $this->db->where('number', $number);
        $this->db->join('customer', 'customer.customer_id=quotation.customer_id', 'Left Join');
        $this->db->group_by('quotation.number');
        $query = $this->db->get();
        return $query->row_array();
    }

    // Get customer by ID
    public function get_customer_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->where('customer_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Delete customer by ID
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

    // Edit customer
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

    // Get GRN data with inspection
    public function get_grn_data_with_inspection($grn_number, $uid)
    {
        $this->db->select('g.*, 
                      s.company_name,
                      gi.quality_rating,
                      gi.packaging_condition,
                      gi.inspection_notes,
                      gi.batch_number,
                      gi.expiry_date,
                      gi.storage_location,
                      gi.accepted_quantity,
                      gi.rejected_quantity,
                      gi.inspected_by,
                      gi.inspection_date,
                      u.username as inspector_name');
        $this->db->from('grn g');
        $this->db->join('supplier s', 'g.supplier_id = s.supplier_id', 'left');
        $this->db->join('grn_inspection gi', 'g.grn_number = gi.grn_number AND g.product_name = gi.item_code', 'left');
        $this->db->join('user u', 'gi.inspected_by = u.user_id', 'left');
        $this->db->where('g.grn_number', $grn_number);
        // $this->db->where('g.uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    // Save inspection data
    public function save_inspection_data($inspection_data)
    {
        // Check if inspection already exists
        $this->db->where('grn_number', $inspection_data['grn_number']);
        $this->db->where('item_code', $inspection_data['item_code']);
        $this->db->where('uid', $inspection_data['uid']);
        $query = $this->db->get('grn_inspection');

        if ($query->num_rows() > 0) {
            // Update existing inspection
            $this->db->where('grn_number', $inspection_data['grn_number']);
            $this->db->where('item_code', $inspection_data['item_code']);
            $this->db->where('uid', $inspection_data['uid']);
            return $this->db->update('grn_inspection', $inspection_data);
        } else {
            // Insert new inspection
            return $this->db->insert('grn_inspection', $inspection_data);
        }
    }

    // Update GRN inspection status
    public function update_grn_inspection_status($grn_number, $update_data, $uid)
    {
        $this->db->where('grn_number', $grn_number);
        $this->db->where('uid', $uid);
        return $this->db->update('grn', $update_data);
    }

    // Update stock after inspection
    public function update_stock_after_inspection($product_name, $accepted_quantity, $uid)
    {
        // Check if item exists under this uid
        $this->db->where('code', $product_name);
        $this->db->where('uid', $uid);
        $count = $this->db->count_all_results('inventory');
        
        $target_uid = ($count > 0) ? $uid : 1;

        // Update inventory stock
        $this->db->set('stock', 'stock + ' . $accepted_quantity, FALSE);
        $this->db->set('available_stock', 'available_stock + ' . $accepted_quantity, FALSE);
        $this->db->group_start()->where('item_name', $product_name)->or_where('code', $product_name)->group_end();
        $this->db->where('uid', $target_uid);
        return $this->db->update('inventory');
    }

    // Get inspection summary
    public function get_inspection_summary($grn_number, $uid)
    {
        $this->db->select('
        SUM(gi.accepted_quantity) as total_accepted,
        SUM(gi.rejected_quantity) as total_rejected,
        COUNT(CASE WHEN gi.quality_rating = "EXCELLENT" THEN 1 END) as excellent_count,
        COUNT(CASE WHEN gi.quality_rating = "GOOD" THEN 1 END) as good_count,
        COUNT(CASE WHEN gi.quality_rating = "FAIR" THEN 1 END) as fair_count,
        COUNT(CASE WHEN gi.quality_rating = "POOR" THEN 1 END) as poor_count,
        COUNT(CASE WHEN gi.packaging_condition = "INTACT" THEN 1 END) as intact_count,
        COUNT(CASE WHEN gi.packaging_condition = "MINOR_DAMAGE" THEN 1 END) as minor_damage_count,
        COUNT(CASE WHEN gi.packaging_condition = "MAJOR_DAMAGE" THEN 1 END) as major_damage_count
    ');
        $this->db->from('grn_inspection gi');
        $this->db->where('gi.grn_number', $grn_number);
        $this->db->where('gi.uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Create inspection log
    public function create_inspection_log($log_data)
    {
        return $this->db->insert('grn_inspection_log', $log_data);
    }

    // Get PO items for inspection
    public function get_po_items_for_inspection($po_number, $uid)
    {
        $this->db->select('product_name, quantity, received_quantity, pending_quantity');
        $this->db->from('grn');
        $this->db->where('po_number_fk', $po_number);
        $this->db->where('uid', $uid);
        $this->db->group_by('product_name');
        $query = $this->db->get();
        return $query->result();
    }

    // Get GRN by PO number
    public function get_grn_by_po_number($po_number, $uid)
    {
        $this->db->select('grn_number');
        $this->db->from('grn');
        $this->db->where('po_number_fk', $po_number);
        $this->db->where('uid', $uid);
        $this->db->group_by('grn_number');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Get GRN approval workflow
    public function get_grn_approval_workflow($amount, $document_type = 'GRN', $location_id = null)
    {
        try {
            // Get approval matrix from database for GRN document type
            $this->db->select('am.*, r.role_name');
            $this->db->from('approval_matrix am');
            $this->db->join('role r', 'am.approver_role = r.role_name', 'left');
            $this->db->where('am.document_type', $document_type);
            $this->db->where('am.status', 'active');
            $this->db->order_by('am.level', 'asc');
            $matrix = $this->db->get()->result_array();

            if (empty($matrix)) {
                // Fallback to default workflow
                return $this->get_default_grn_workflow($amount, $location_id);
            }

            $workflow = [];
            $current_level = null;
            $current_approver = null;

            foreach ($matrix as $level) {
                $min_amount = floatval($level['min_amount']);
                $max_amount = floatval($level['max_amount']);
                $is_required = false;

                // Check if amount falls within the range
                if ($min_amount == 0 && $max_amount == 0) {
                    // Always required (like Quality department)
                    $is_required = true;
                } elseif ($amount >= $min_amount && $amount <= $max_amount) {
                    $is_required = true;
                }

                // Get approver email for this role
                $approver_email = $this->get_approver_email_by_role($level['role_name'], $location_id);

                $workflow[$level['level']] = [
                    'level_name' => strtolower(str_replace(' ', '_', $level['role_name'])),
                    'role' => $level['role_name'],
                    'email' => $approver_email,
                    'status' => $is_required ? 'pending' : 'not_required',
                    'min_amount' => $min_amount,
                    'max_amount' => $max_amount,
                    'is_required' => $is_required
                ];

                // Set current level to the first required approver
                if ($is_required && !$current_level) {
                    $current_level = $workflow[$level['level']]['level_name'];
                    $current_approver = $workflow[$level['level']]['email'];
                }
            }

            return [
                'workflow' => $workflow,
                'current_level' => $current_level,
                'current_approver' => $current_approver
            ];
        } catch (Exception $e) {
            error_log("Error in get_grn_approval_workflow: " . $e->getMessage());
            return $this->get_default_grn_workflow($amount, $location_id);
        }
    }

    // Default GRN workflow fallback
    private function get_default_grn_workflow($amount, $location_id = null)
    {
        $workflow = [];

        // Level 1: Quality (always required)
        $workflow[1] = [
            'level_name' => 'quality',
            'role' => 'Quality',
            'email' => $this->get_approver_email_by_role('Quality', $location_id),
            'status' => 'pending'
        ];

        // Determine current approver
        $current_level = 'quality';
        $current_approver = $workflow[1]['email'];

        return [
            'workflow' => $workflow,
            'current_level' => $current_level,
            'current_approver' => $current_approver
        ];
    }

    // Get approver email by role
    public function get_approver_email_by_role($role, $location_id = null)
    {
        try {
            // Normalize role name
            $normalized_role = $role;
            if (strtolower($role) === 'store' || strtolower($role) === 'store incharge') {
                $normalized_role = 'Store Incharge';
            }

            // First, try to get active user with this role at this specific location from user_roles
            if ($location_id) {
                $this->db->select('u.user_email');
                $this->db->from('user u');
                $this->db->join('user_roles ur', 'u.user_id = ur.user_id');
                $this->db->where('ur.role_name', $normalized_role);
                $this->db->where('ur.location_id', $location_id);
                $this->db->where('ur.is_active', 1);
                $this->db->where('u.user_email IS NOT NULL');
                $this->db->where('u.user_email !=', '');
                $this->db->order_by('u.user_id', 'asc');
                $this->db->limit(1);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $user = $query->row_array();
                    return $user['user_email'];
                }
            }

            // Fallback to query user table by main role and location
            $this->db->select('u.user_email');
            $this->db->from('user u');
            $this->db->join('role r', 'u.role = r.role_id');
            $this->db->where('r.role_name', $normalized_role);
            if ($location_id) {
                $this->db->where('u.location_id', $location_id);
            }
            $this->db->where('u.user_email IS NOT NULL');
            $this->db->where('u.user_email !=', '');
            $this->db->order_by('u.user_id', 'asc');
            $this->db->limit(1);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $user = $query->row_array();
                return $user['user_email'];
            }

            // Fallback without location filter
            $this->db->select('u.user_email');
            $this->db->from('user u');
            $this->db->join('role r', 'u.role = r.role_id');
            $this->db->where('r.role_name', $normalized_role);
            $this->db->where('u.user_email IS NOT NULL');
            $this->db->where('u.user_email !=', '');
            $this->db->order_by('u.user_id', 'asc');
            $this->db->limit(1);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $user = $query->row_array();
                return $user['user_email'];
            }

            // Fallback emails
            $role_emails = [
                'Store Incharge' => 'xformtech39@gmail.com',
                'Quality' => 'xformtech32@gmail.com',
                'Store' => 'xformtech39@gmail.com',
                'Site Incharge' => 'xformtech32@gmail.com',
                'Accounts' => 'xformtech35@gmail.com'
            ];

            return $role_emails[$role] ?? 'nayan@xform.in';
        } catch (Exception $e) {
            error_log("Error in get_approver_email_by_role: " . $e->getMessage());
            return 'nayan@xform.in';
        }
    }

    // Process GRN approval
    public function process_grn_approval($approval_id, $action, $remarks, $user_email, $user_id)
    {
        try {
            $this->db->trans_begin();

            // Get approval details
            $approval = $this->db->where('approval_id', $approval_id)->get('grn_approvals')->row_array();
            if (!$approval) throw new Exception('Approval not found!');

            // Update current approval
            $this->db->where('approval_id', $approval_id);
            $this->db->update('grn_approvals', [
                'status' => $action,
                'remarks' => $remarks,
                'action_by' => $user_email,
                'action_date' => date('Y-m-d H:i:s')
            ]);

            if ($action == 'approved') {
                // Get all approvals for this GRN
                $all_approvals = $this->db->where('grn_number', $approval['grn_number'])
                    ->order_by('level', 'asc')
                    ->get('grn_approvals')
                    ->result_array();

                // Find next required approval
                $next_approval = null;
                foreach ($all_approvals as $app) {
                    if ($app['status'] == 'pending' && $app['approval_level'] != 'not_required') {
                        $next_approval = $app;
                        break;
                    }
                }

                if ($next_approval) {
                    // Move to next approver
                    $this->db->where('number_fk', $approval['grn_number']);
                    $this->db->update('grn_total', [
                        'approval_level' => $next_approval['approval_level'],
                        'current_approver' => $next_approval['approver_email'],
                        'approval_status' => 'pending_approval'
                    ]);
                } else {
                    // All approvals done
                    $this->db->where('number_fk', $approval['grn_number']);
                    $this->db->update('grn_total', [
                        'approval_status' => 'approved',
                        'approved_at' => date('Y-m-d H:i:s'),
                        'approved_by' => $user_id
                    ]);
                }
            } else if ($action == 'rejected') {
                // Reject GRN
                $this->db->where('number_fk', $approval['grn_number']);
                $this->db->update('grn_total', [
                    'approval_status' => 'rejected',
                    'rejection_reason' => $remarks,
                    'rejected_at' => date('Y-m-d H:i:s'),
                    'rejected_by' => $user_id
                ]);

                // Reject all pending approvals
                $this->db->where('grn_number', $approval['grn_number']);
                $this->db->where('status', 'pending');
                $this->db->update('grn_approvals', [
                    'status' => 'rejected',
                    'action_date' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            error_log("Error processing GRN approval: " . $e->getMessage());
            return false;
        }
    }

    // Get pending approvals for a user
    public function get_pending_grn_approvals($user_email)
    {
        $is_admin = $this->is_admin_user($user_email);
        $user_roles = array();
        $locations = array();
        if (!$is_admin) {
            $user_info = $this->get_user_roles_and_locations($user_email);
            $user_roles = $user_info['roles'];
            $locations = $user_info['locations'];
        }

        $this->db->select('grn_approvals.*, grn_total.total, supplier.company_name as supplier_name, grn.date, grn.po_number_fk');
        $this->db->from('grn_approvals');
        $this->db->join('grn_total', 'grn_approvals.grn_number = grn_total.number_fk', 'left');
        $this->db->join('grn', 'grn_approvals.grn_number = grn.grn_number', 'left');
        $this->db->join('supplier', 'grn.supplier_id = supplier.supplier_id', 'left');
        $this->db->join('po_total', 'grn.po_number_fk = po_total.number_fk', 'left');
        $this->db->join('purchase_requisition', 'po_total.pr_id = purchase_requisition.pr_id', 'left');
        
        if (!$is_admin) {
            // Normalize roles list
            $all_roles = $user_roles;
            if (in_array('Store Incharge', $user_roles) || in_array('Store', $user_roles)) {
                $all_roles[] = 'Store';
                $all_roles[] = 'store Incharge';
                $all_roles[] = 'store';
                $all_roles[] = 'Store Incharge';
            }
            $all_roles = array_unique($all_roles);

            $this->db->group_start();
            $this->db->where('grn_approvals.approver_email', $user_email);
            if (!empty($all_roles)) {
                $this->db->or_group_start();
                $this->db->where_in('grn_approvals.approver_role', $all_roles);
                if (!empty($locations)) {
                    $this->db->group_start();
                    $this->db->where_in('purchase_requisition.location_id_fk', $locations);
                    $this->db->or_where('purchase_requisition.location_id_fk IS NULL');
                    $this->db->or_where('purchase_requisition.location_id_fk', 0);
                    $this->db->or_where('purchase_requisition.location_id_fk', '');
                    $this->db->group_end();
                }
                $this->db->group_end();
            }
            $this->db->group_end();
        }

        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('grn.date >=', $fy_from);
            $this->db->where('grn.date <=', $fy_to);
        }

        $this->db->where('grn_approvals.status', 'pending');
        $this->db->group_by('grn_approvals.grn_number');
        $this->db->order_by('grn_approvals.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    // Get GRN approval history
    public function get_grn_approval_history($user_email)
    {
        $is_admin = $this->is_admin_user($user_email);
        $user_roles = array();
        $locations = array();
        if (!$is_admin) {
            $user_info = $this->get_user_roles_and_locations($user_email);
            $user_roles = $user_info['roles'];
            $locations = $user_info['locations'];
        }

        $this->db->select('grn_approvals.*, grn_total.total, supplier.company_name as supplier_name, grn.date, grn.po_number_fk, user.username as action_by_name, role.role_name as action_by_role');
        $this->db->from('grn_approvals');
        $this->db->join('grn_total', 'grn_approvals.grn_number = grn_total.number_fk', 'left');
        $this->db->join('grn', 'grn_approvals.grn_number = grn.grn_number', 'left');
        $this->db->join('supplier', 'grn.supplier_id = supplier.supplier_id', 'left');
        $this->db->join('po_total', 'grn.po_number_fk = po_total.number_fk', 'left');
        $this->db->join('purchase_requisition', 'po_total.pr_id = purchase_requisition.pr_id', 'left');
        $user_table = $this->db->dbprefix('user');
        $role_table = $this->db->dbprefix('role');
        $this->db->join('user', "grn_approvals.action_by = {$user_table}.user_email OR grn_approvals.action_by = CAST({$user_table}.user_id AS CHAR) OR grn_approvals.action_by = {$user_table}.username", 'left');
        $this->db->join('role', "{$user_table}.role = {$role_table}.role_id", 'left');

        if (!$is_admin) {
            // Normalize roles list
            $all_roles = $user_roles;
            if (in_array('Store Incharge', $user_roles) || in_array('Store', $user_roles)) {
                $all_roles[] = 'Store';
                $all_roles[] = 'store Incharge';
                $all_roles[] = 'store';
                $all_roles[] = 'Store Incharge';
            }
            $all_roles = array_unique($all_roles);

            $this->db->group_start();
            $this->db->where('grn_approvals.approver_email', $user_email);
            if (!empty($all_roles)) {
                $this->db->or_group_start();
                $this->db->where_in('grn_approvals.approver_role', $all_roles);
                if (!empty($locations)) {
                    $this->db->group_start();
                    $this->db->where_in('purchase_requisition.location_id_fk', $locations);
                    $this->db->or_where('purchase_requisition.location_id_fk IS NULL');
                    $this->db->or_where('purchase_requisition.location_id_fk', 0);
                    $this->db->or_where('purchase_requisition.location_id_fk', '');
                    $this->db->group_end();
                }
                $this->db->group_end();
            }
            $this->db->group_end();
        }

        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('grn.date >=', $fy_from);
            $this->db->where('grn.date <=', $fy_to);
        }

        $this->db->where_in('grn_approvals.status', ['approved', 'rejected']);
        $this->db->group_by('grn_approvals.approval_id');
        $this->db->order_by('grn_approvals.action_date', 'DESC');
        return $this->db->get()->result_array();
    }

    // Get GRN approval details
    public function get_grn_approval_details($grn_number)
    {
        $this->db->select('grn_approvals.*, user.username as action_by_name, role.role_name as action_by_role');
        $this->db->from('grn_approvals');
        $this->db->join('user', 'grn_approvals.action_by = user.user_email', 'left');
        $this->db->join('role', 'user.role = role.role_id', 'left');
        $this->db->where('grn_approvals.grn_number', $grn_number);
        $this->db->order_by('grn_approvals.created_at', 'ASC');
        return $this->db->get()->result_array();
    }

    // Get GRN approval statistics
    public function get_approval_statistics($user_email)
    {
        $is_admin = $this->is_admin_user($user_email);
        $user_roles = [];
        $locations = [];
        $all_roles = [];
        if (!$is_admin) {
            $user_info = $this->get_user_roles_and_locations($user_email);
            $user_roles = $user_info['roles'];
            $locations = $user_info['locations'];

            // Normalize roles list
            $all_roles = $user_roles;
            if (in_array('Store Incharge', $user_roles) || in_array('Store', $user_roles)) {
                $all_roles[] = 'Store';
                $all_roles[] = 'store Incharge';
                $all_roles[] = 'store';
                $all_roles[] = 'Store Incharge';
            }
            $all_roles = array_unique($all_roles);
        }

        $stats = [];

        // Pending
        $this->db->from('grn_approvals');
        $this->db->join('grn', 'grn_approvals.grn_number = grn.grn_number', 'left');
        $this->db->join('po_total', 'grn.po_number_fk = po_total.number_fk', 'left');
        $this->db->join('purchase_requisition', 'po_total.pr_id = purchase_requisition.pr_id', 'left');
        
        if (!$is_admin) {
            $this->db->group_start();
            $this->db->where('grn_approvals.approver_email', $user_email);
            if (!empty($all_roles)) {
                $this->db->or_group_start();
                $this->db->where_in('grn_approvals.approver_role', $all_roles);
                if (!empty($locations)) {
                    $this->db->group_start();
                    $this->db->where_in('purchase_requisition.location_id_fk', $locations);
                    $this->db->or_where('purchase_requisition.location_id_fk IS NULL');
                    $this->db->or_where('purchase_requisition.location_id_fk', 0);
                    $this->db->or_where('purchase_requisition.location_id_fk', '');
                    $this->db->group_end();
                }
                $this->db->group_end();
            }
            $this->db->group_end();
        }
        $this->db->where('grn_approvals.status', 'pending');
        $stats['pending'] = $this->db->count_all_results();

        // Approved
        $this->db->from('grn_approvals');
        $this->db->join('grn', 'grn_approvals.grn_number = grn.grn_number', 'left');
        $this->db->join('po_total', 'grn.po_number_fk = po_total.number_fk', 'left');
        $this->db->join('purchase_requisition', 'po_total.pr_id = purchase_requisition.pr_id', 'left');
        
        if (!$is_admin) {
            $this->db->group_start();
            $this->db->where('grn_approvals.approver_email', $user_email);
            if (!empty($all_roles)) {
                $this->db->or_group_start();
                $this->db->where_in('grn_approvals.approver_role', $all_roles);
                if (!empty($locations)) {
                    $this->db->group_start();
                    $this->db->where_in('purchase_requisition.location_id_fk', $locations);
                    $this->db->or_where('purchase_requisition.location_id_fk IS NULL');
                    $this->db->or_where('purchase_requisition.location_id_fk', 0);
                    $this->db->or_where('purchase_requisition.location_id_fk', '');
                    $this->db->group_end();
                }
                $this->db->group_end();
            }
            $this->db->group_end();
        }
        $this->db->where('grn_approvals.status', 'approved');
        $stats['approved'] = $this->db->count_all_results();

        // Rejected
        $this->db->from('grn_approvals');
        $this->db->join('grn', 'grn_approvals.grn_number = grn.grn_number', 'left');
        $this->db->join('po_total', 'grn.po_number_fk = po_total.number_fk', 'left');
        $this->db->join('purchase_requisition', 'po_total.pr_id = purchase_requisition.pr_id', 'left');
        
        if (!$is_admin) {
            $this->db->group_start();
            $this->db->where('grn_approvals.approver_email', $user_email);
            if (!empty($all_roles)) {
                $this->db->or_group_start();
                $this->db->where_in('grn_approvals.approver_role', $all_roles);
                if (!empty($locations)) {
                    $this->db->group_start();
                    $this->db->where_in('purchase_requisition.location_id_fk', $locations);
                    $this->db->or_where('purchase_requisition.location_id_fk IS NULL');
                    $this->db->or_where('purchase_requisition.location_id_fk', 0);
                    $this->db->or_where('purchase_requisition.location_id_fk', '');
                    $this->db->group_end();
                }
                $this->db->group_end();
            }
            $this->db->group_end();
        }
        $this->db->where('grn_approvals.status', 'rejected');
        $stats['rejected'] = $this->db->count_all_results();

        return $stats;
    }

    public function get_monthly_grn_approval_count($user_email, $status, $month)
    {
        $user_info = $this->get_user_roles_and_locations($user_email);
        $user_roles = $user_info['roles'];
        $locations = $user_info['locations'];

        // Normalize roles list
        $all_roles = $user_roles;
        if (in_array('Store Incharge', $user_roles) || in_array('Store', $user_roles)) {
            $all_roles[] = 'Store';
            $all_roles[] = 'store Incharge';
            $all_roles[] = 'store';
            $all_roles[] = 'Store Incharge';
        }
        $all_roles = array_unique($all_roles);

        $this->db->from('grn_approvals');
        $this->db->join('grn', 'grn_approvals.grn_number = grn.grn_number', 'left');
        $this->db->join('po_total', 'grn.po_number_fk = po_total.number_fk', 'left');
        $this->db->join('purchase_requisition', 'po_total.pr_id = purchase_requisition.pr_id', 'left');
        
        $this->db->group_start();
        $this->db->where('grn_approvals.approver_email', $user_email);
        if (!empty($all_roles)) {
            $this->db->or_group_start();
            $this->db->where_in('grn_approvals.approver_role', $all_roles);
            if (!empty($locations)) {
                $this->db->group_start();
                $this->db->where_in('purchase_requisition.location_id_fk', $locations);
                $this->db->or_where('purchase_requisition.location_id_fk IS NULL');
                $this->db->or_where('purchase_requisition.location_id_fk', 0);
                $this->db->or_where('purchase_requisition.location_id_fk', '');
                $this->db->group_end();
            }
            $this->db->group_end();
        }
        $this->db->group_end();
        $this->db->where('grn_approvals.status', $status);
        
        if ($status == 'pending') {
            $this->db->where("DATE_FORMAT(grn_approvals.created_at, '%Y-%m')", $month);
        } else {
            $this->db->where("DATE_FORMAT(grn_approvals.action_date, '%Y-%m')", $month);
        }
        
        return $this->db->count_all_results();
    }

    public function get_po_location($po_number)
    {
        $this->db->select('purchase_requisition.location_id_fk');
        $this->db->from('po_total');
        $this->db->join('purchase_requisition', 'po_total.pr_id = purchase_requisition.pr_id', 'left');
        $this->db->where('po_total.number_fk', $po_number);
        $this->db->limit(1);
        $res = $this->db->get()->row_array();
        return $res ? ($res['location_id_fk'] ?? null) : null;
    }

    private function get_user_roles_and_locations($user_email)
    {
        $roles = [];
        $locations = [];

        // 1. Get from user_roles
        $this->db->select('ur.role_name, ur.location_id');
        $this->db->from('user u');
        $this->db->join('user_roles ur', 'u.user_id = ur.user_id');
        $this->db->where('u.user_email', $user_email);
        $this->db->where('ur.is_active', 1);
        $roles_result = $this->db->get()->result_array();

        foreach ($roles_result as $row) {
            $roles[] = $row['role_name'];
            $locations[] = $row['location_id'];
        }

        // 2. Get main role
        $this->db->select('r.role_name, u.location_id');
        $this->db->from('user u');
        $this->db->join('role r', 'u.role = r.role_id');
        $this->db->where('u.user_email', $user_email);
        $main_role_result = $this->db->get()->row_array();
        if ($main_role_result) {
            $roles[] = $main_role_result['role_name'];
            if ($main_role_result['location_id']) {
                $locations[] = $main_role_result['location_id'];
            }
        }

        return [
            'roles' => array_unique($roles),
            'locations' => array_unique($locations)
        ];
    }

    // Helper to check if a user (by email or session) is Admin role
    public function is_admin_user($user_email = '')
    {
        $session_data = $this->session->userdata('session_data_head');
        $session_role = $session_data['result']['role_name'] ?? '';
        if (strtolower($session_role) === 'admin') {
            return true;
        }
        
        $user_id = $session_data['result']['user_id'] ?? null;
        if ($user_id) {
            $user_role_row = $this->db->select('r.role_name')
                ->from('user u')
                ->join('role r', 'u.role = r.role_id', 'left')
                ->where('u.user_id', $user_id)
                ->get()->row_array();
            if (!empty($user_role_row['role_name']) && strtolower($user_role_row['role_name']) === 'admin') {
                return true;
            }
        }

        if (empty($user_email)) {
            return false;
        }

        $this->db->select('r.role_name');
        $this->db->from('user u');
        $this->db->join('role r', 'u.role = r.role_id', 'left');
        $this->db->group_start();
        $this->db->where('u.user_email', $user_email);
        $this->db->or_where('u.username', $user_email);
        $this->db->group_end();
        $result = $this->db->get()->row_array();
        return (!empty($result['role_name']) && strtolower($result['role_name']) === 'admin');
    }


    // Get GRN by ID
    public function get_grn_by_id($grn_id, $uid)
    {
        $this->db->select('*');
        $this->db->from('grn');
        $this->db->where('grn_id', $grn_id);
        // $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Update GRN
    public function update_grn($grn_id, $data, $uid)
    {
        $this->db->where('grn_id', $grn_id);
        // $this->db->where('uid', $uid);
        return $this->db->update('grn', $data);
    }

    // Search GRNs
    public function search_grns($search_term, $uid)
    {
        $this->db->select('g.grn_id, g.grn_number, g.po_number_fk, g.date, s.company_name, s.fullname, gt.total');
        $this->db->from('grn g');
        $this->db->join('supplier s', 's.supplier_id = g.supplier_id');
        $this->db->join('grn_total gt', 'gt.number_fk = g.grn_number');
        // $this->db->where('g.uid', $uid);
        // $this->db->where('gt.uid', $uid);
        $this->db->group_start();
        $this->db->like('g.grn_number', $search_term);
        $this->db->or_like('g.po_number_fk', $search_term);
        $this->db->or_like('s.company_name', $search_term);
        $this->db->or_like('s.fullname', $search_term);
        $this->db->group_end();
        $this->db->group_by('g.grn_number');
        $this->db->order_by("g.grn_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    // Get monthly GRN summary
    public function get_monthly_summary($uid, $month, $year)
    {
        $this->db->select('COUNT(*) as total_grns, SUM(gt.total) as total_amount');
        $this->db->from('grn g');
        $this->db->join('grn_total gt', 'gt.number_fk = g.grn_number');
        // $this->db->where('g.uid', $uid);
        // $this->db->where('gt.uid', $uid);
        $this->db->where('MONTH(g.date)', $month);
        $this->db->where('YEAR(g.date)', $year);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Get GRN items by GRN ID
    public function get_grn_items_by_grn_id($grn_id, $uid)
    {
        $this->db->select('*');
        $this->db->from('grn');
        $this->db->where('grn_id', $grn_id);
        // $this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }
}
