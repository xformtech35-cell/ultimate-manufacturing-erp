<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Generate PO number with format: PO/{PO_FY}/{PO_SEQUENCE}/({SO_FY}/{SO_SEQUENCE})
    // Example: PO/26-27/0003/(2627/305)
    public function generate_po_number($so_no = null)
    {
        if (date('m') <= 3) {
            $financial_year = (date('y') - 1) . '-' . date('y');
            $so_fy = (date('y') - 1) . date('y');
        } else {
            $financial_year = date('y') . '-' . (date('y') + 1);
            $so_fy = date('y') . (date('y') + 1);
        }

        $this->db->select('number_fk');
        $this->db->from('po_total');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        $last_po_no = $query->row();

        $next_number = 1;
        if ($last_po_no && !empty($last_po_no->number_fk)) {
            if (preg_match('/PO\/(?:[0-9]{2}-[0-9]{2}\/)?([0-9]{4})/i', $last_po_no->number_fk, $matches)) {
                $next_number = (int)$matches[1] + 1;
            } elseif (preg_match('/PO\/([0-9]+)/i', $last_po_no->number_fk, $matches)) {
                $next_number = (int)$matches[1] + 1;
            }
        }

        $formatted_number = str_pad($next_number, 4, '0', STR_PAD_LEFT);

        // Format SO suffix (e.g., (2627/305))
        $so_suffix = "";
        if (!empty($so_no)) {
            $clean_so = trim($so_no);
            if (preg_match('/(?:([0-9]{4})|([0-9]{2}-[0-9]{2})).*?([0-9]+)$/', $clean_so, $so_matches)) {
                $fy_code = !empty($so_matches[1]) ? $so_matches[1] : str_replace('-', '', $so_matches[2]);
                $so_seq = $so_matches[3];
                $so_suffix = "/(" . $fy_code . "/" . $so_seq . ")";
            } elseif (preg_match('/([0-9]+)$/', $clean_so, $so_matches)) {
                $so_suffix = "/(" . $so_fy . "/" . $so_matches[1] . ")";
            }
        }

        return 'PO/' . $financial_year . '/' . $formatted_number . $so_suffix;
    }

    // Convert RFQ to PO with 3-level approval
    public function convert_rfq_to_po($rfq_id, $quotation_id, $vendor_id, $user_id)
    {



        $data['settings'] = $this->login->get_settings($user_id);

        $data['supplier'] = $this->supplier->get_supplier_state_code($vendor_id);


        // echo  $data['settings']['state_code'];
        // echo  $data['supplier']['state_code'];

        $gst_type = "S";

        if ($data['settings']['state_code']  ==  $data['supplier']['state_code']) {
            $gst_type = "S";
        } else {
            $gst_type = "I";
        }


        // die();


        try {
            $this->db->trans_begin();

            // Get RFQ details
            $rfq = $this->db->where('rfq_id', $rfq_id)->get('rfq')->row_array();
            if (!$rfq) throw new Exception('RFQ not found!');

            // Get quotation details
            $this->db->select('vq.*, s.supplier_id, s.company_name as supplier_name, s.fullname, s.email, s.mobile, s.address, s.gst, s.pancard');
            $this->db->from('vendor_quotations vq');
            $this->db->join('supplier s', 'vq.supplier_id = s.supplier_id');
            $this->db->where('vq.quotation_id', $quotation_id);
            $quotation = $this->db->get()->row_array();

            if (!$quotation) throw new Exception('Quotation not found!');

            // Check if PO already exists
            $existing_po = $this->db->where('quotation_id', $quotation_id)
                ->where_in('approval_status', ['pending_approval', 'approved', 'sent'])
                ->get('po_total')
                ->row_array();

            if ($existing_po) {
                return [
                    'success' => true,
                    'already_exists' => true,
                    'po_number' => $existing_po['number_fk'],
                    'po_total_id' => $existing_po['id'],
                    'total_amount' => $existing_po['total'],
                    'current_approver' => $existing_po['current_approver'],
                    'approval_workflow' => [],
                    'quotation_data' => $quotation
                ];
            }

            // Get linked PR details to inherit Project/SO/OC details
            $pr_record = $this->db->where('pr_id', $rfq['pr_id'])->get('purchase_requisition')->row_array();
            $location_id = $pr_record ? ($pr_record['location_id_fk'] ?? null) : null;
            $pr_proj = $pr_record ? ($pr_record['project_code'] ?? null) : null;
            $pr_so = $pr_record ? ($pr_record['so_no'] ?? null) : null;
            $pr_oc = $pr_record ? ($pr_record['oc_no'] ?? null) : null;

            // Mark any previous pending PO for older revisions of this RFQ & Vendor as superseded
            $this->db->where('rfq_id', $rfq_id)
                ->where('supplier_id_fk', $vendor_id)
                ->where('quotation_id !=', $quotation_id)
                ->where('approval_status', 'pending_approval')
                ->update('po_total', ['approval_status' => 'superseded', 'note' => 'Superseded by new quotation revision']);

            // Generate PO number formatted with SO suffix
            $po_number = $this->generate_po_number($pr_so);
            $total_amount = $quotation['final_amount'] ?? 0;
            $approval_workflow = $this->get_3level_approval_workflow($total_amount, $location_id);

            // Insert PO header
            $po_total_data = [
                'number_fk' => $po_number,
                'total' => $total_amount,
                'payment_method' => 0,
                'uid' => $user_id,
                'po_payment_terms' => 'Net 30 Days',
                'po_taxes' => 'GST as applicable',
                'status' => '1',
                'balance' => $total_amount,
                'paid' => 0,
                'payment_due_date' => date('d-m-Y', strtotime('+15 days')),
                'supplier_id_fk' => $vendor_id,
                'date' => date('Y-m-d'),
                'note' => 'Converted from RFQ #' . $rfq['rfq_no'],
                'approval_status' => 'pending_approval',
                'approval_level' => $approval_workflow['current_level'],
                'current_approver' => $approval_workflow['current_approver'],
                'rfq_id' => $rfq_id,
                'quotation_id' => $quotation_id,
                'pr_id' => $rfq['pr_id'],
                'project_code' => $pr_proj,
                'so_no' => $pr_so,
                'oc_no' => $pr_oc
            ];

            $this->db->insert('po_total', $po_total_data);
            $po_total_id = $this->db->insert_id();

            // Get quotation items
            $this->db->select('vqi.*, ri.description, ri.unit, ri.hsn');
            $this->db->from('vendor_quotation_items vqi');
            $this->db->join('rfq_items ri', 'vqi.rfq_item_id = ri.rfq_item_id');
            $this->db->where('vqi.quotation_id', $quotation_id);
            $quotation_items = $this->db->get()->result_array();

            if (empty($quotation_items)) throw new Exception('No items found!');

            // Insert PO items
            foreach ($quotation_items as $item) {
                $po_item_data = [
                    'number' => $po_number,
                    'supplier_id' => $vendor_id,
                    'purchase_date' => date('Y-m-d'),
                    'delivery_date' => date('d-m-Y', strtotime('+15 days')),
                    'product_name' => $item['item_code'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'PCS',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'gst' => ($item['gst_percentage'] ?? 18) . '%',
                    'sgst' => 9,
                    'cgst' => 9,
                    'igst' => 18,
                    'gst_type' => $gst_type,
                    'price' => $item['unit_price'],
                    'amount' => $item['total_amount'],
                    'amount_due' => $item['total_amount'],
                    'reasons' => 'Converted from RFQ',
                    'description' => $item['description'],
                    'po_pending_quantity' => 'Y',
                    'uid' => $user_id
                ];
                $this->db->insert('purchase_order', $po_item_data);
            }

            // Create approval requests
            foreach ($approval_workflow['workflow'] as $level => $approval) {
                $approval_data = [
                    'po_id_fk' => $po_total_id,
                    'approval_level' => $approval['level_name'],
                    'approver_role' => $approval['role'],
                    'approver_email' => $approval['email'],
                    'status' => $approval['status'],
                    'level' => $level,
                    'created_at' => date('Y-m-d H:i:s'),
                    'uid' => $user_id
                ];
                if ($this->db->field_exists('po_number', 'po_approvals')) {
                    $approval_data['po_number'] = $po_number;
                }
                $this->db->insert('po_approvals', $approval_data);
            }

            // Update RFQ status
            $this->db->where('rfq_id', $rfq_id)
                ->update('rfq', ['status' => 'converted_to_po']);

            $this->db->trans_commit();

            return [
                'success' => true,
                'po_number' => $po_number,
                'po_total_id' => $po_total_id,
                'total_amount' => $total_amount,
                'current_approver' => $approval_workflow['current_approver'],
                'approval_workflow' => $approval_workflow,
                'quotation_data' => $quotation
            ];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            error_log("Error converting RFQ to PO: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get dynamic approval workflow with CUMULATIVE logic
    public function get_3level_approval_workflow($amount, $location_id = null)
    {
        try {
            // Get approval matrix from database for PO document type
            $this->db->select('am.*, r.role_name');
            $this->db->from('approval_matrix am');
            $this->db->join('role r', 'am.approver_role = r.role_name', 'left');
            $this->db->where('am.document_type', 'PO');
            $this->db->where('am.status', 'active');
            $this->db->order_by('am.level', 'asc');
            $matrix = $this->db->get()->result_array();

            if (empty($matrix)) {
                return $this->get_default_approval_workflow($amount, $location_id);
            }

            $workflow = [];
            $current_level = null;
            $current_approver = null;

            foreach ($matrix as $level) {
                $min_amount = floatval($level['min_amount']);
                $max_amount = floatval($level['max_amount']);

                // CUMULATIVE LOGIC: Each level is required if amount >= its min_amount
                // AND all lower levels are also required
                $is_required = false;

                if ($amount >= $min_amount) {
                    // For Buyer (0-5000): always required if amount > 0
                    if ($min_amount == 0 && $amount > 0) {
                        $is_required = true;
                    }
                    // For Purchase Manager (5001-200000): required if amount >= 5001
                    elseif ($min_amount == 5001 && $amount >= 5001) {
                        $is_required = true;
                    }
                    // For Director (200001+): required if amount >= 200001
                    elseif ($min_amount == 200001 && $amount >= 200001) {
                        $is_required = true;
                    }
                }

                $approver_email = $this->get_approver_email_by_role($level['role_name'], $location_id);

                $workflow[$level['level']] = [
                    'level_name' => strtolower(str_replace(' ', '_', $level['role_name'])),
                    'role' => $level['role_name'],
                    'email' => $approver_email,
                    'status' => $is_required ? 'pending' : 'not_required',
                    'is_required' => $is_required
                ];

                if ($is_required && !$current_level) {
                    $current_level = $workflow[$level['level']]['level_name'];
                    $current_approver = $workflow[$level['level']]['email'];
                }
            }

            if (!$current_level && count($workflow) > 0) {
                $first_level = reset($workflow);
                $current_level = $first_level['level_name'];
                $current_approver = $first_level['email'];
            }

            return [
                'workflow' => $workflow,
                'current_level' => $current_level,
                'current_approver' => $current_approver
            ];
        } catch (Exception $e) {
            error_log("Error in get_3level_approval_workflow: " . $e->getMessage());
            return $this->get_default_approval_workflow($amount, $location_id);
        }
    }

    // Updated default approval workflow
    private function get_default_approval_workflow($amount, $location_id = null)
    {
        // Get PO matrix from database
        $this->db->select('am.*, r.role_name');
        $this->db->from('approval_matrix am');
        $this->db->join('role r', 'am.approver_role = r.role_name', 'left');
        $this->db->where('am.document_type', 'PO');
        $this->db->where('am.status', 'active');
        $this->db->order_by('am.level', 'asc');
        $matrix = $this->db->get()->result_array();

        $workflow = [];
        $current_level = null;
        $current_approver = null;

        foreach ($matrix as $level) {
            $min_amount = floatval($level['min_amount']);
            $max_amount = floatval($level['max_amount']);

            // Determine if this level is required based on amount
            $is_required = false;

            // For Buyer (0-5000)
            if ($min_amount == 0 && $max_amount > 0 && $amount <= $max_amount) {
                $is_required = true;
            }
            // For Purchase Manager (5001-499999.99)
            elseif ($min_amount == 5001 && $amount >= $min_amount && $amount <= $max_amount) {
                $is_required = true;
            }
            // For Director (200000-9999999.00)
            elseif ($min_amount == 200000 && $amount >= $min_amount && $amount <= $max_amount) {
                $is_required = true;
            }

            $approver_email = $this->get_approver_email_by_role($level['role_name'], $location_id);

            $workflow[$level['level']] = [
                'level_name' => strtolower(str_replace(' ', '_', $level['role_name'])),
                'role' => $level['role_name'],
                'email' => $approver_email,
                'status' => $is_required ? 'pending' : 'not_required',
                'is_required' => $is_required
            ];

            // Set first required level as current
            if ($is_required && !$current_level) {
                $current_level = $workflow[$level['level']]['level_name'];
                $current_approver = $workflow[$level['level']]['email'];
            }
        }

        // If no level is required (for amounts below 0 or above all ranges)
        if (!$current_level && count($workflow) > 0) {
            // At least Buyer should be required for any PO
            $workflow[1]['status'] = 'pending';
            $workflow[1]['is_required'] = true;
            $current_level = $workflow[1]['level_name'];
            $current_approver = $workflow[1]['email'];
        }

        return [
            'workflow' => $workflow,
            'current_level' => $current_level,
            'current_approver' => $current_approver
        ];
    }

    // Get approver email by role - reads ALL from database
    private function get_approver_email_by_role($role, $location_id = null)
    {
        try {
            // First, try to get active user with this role at this specific location from user_roles
            if ($location_id) {
                $this->db->select('u.user_email');
                $this->db->from('user u');
                $this->db->join('user_roles ur', 'u.user_id = ur.user_id');
                $this->db->where('ur.role_name', $role);
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
            $this->db->where('r.role_name', $role);
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
            $this->db->where('r.role_name', $role);
            $this->db->where('u.user_email IS NOT NULL');
            $this->db->where('u.user_email !=', '');
            $this->db->order_by('u.user_id', 'asc');
            $this->db->limit(1);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $user = $query->row_array();
                return $user['user_email'];
            }

            // Fallback to defaults
            $role_emails = $this->get_all_role_emails_from_db();
            return $role_emails[$role] ?? 'nayan@xform.in';
        } catch (Exception $e) {
            error_log("Error in get_approver_email_by_role: " . $e->getMessage());

            // Ultimate fallback - get from database or use default
            $role_emails = $this->get_all_role_emails_from_db();
            return $role_emails[$role] ?? 'nayan@xform.in';
        }
    }

    // Get all role-email mappings from database
    private function get_all_role_emails_from_db()
    {
        try {
            // Get all users with their roles and emails
            $this->db->select('r.role_name, u.user_email, u.username');
            $this->db->from('user u');
            $this->db->join('role r', 'u.role = r.role_id');
            $this->db->where('u.user_email IS NOT NULL');
            $this->db->where('u.user_email !=', '');
            $this->db->order_by('r.role_name', 'asc');
            $this->db->order_by('u.user_id', 'asc');
            $query = $this->db->get();

            $role_emails = [];

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    // Only set if not already set (first user for each role)
                    if (!isset($role_emails[$row['role_name']])) {
                        $role_emails[$row['role_name']] = $row['user_email'];
                    }
                }
            }

            // Add fallback for common roles if not found in database
            $fallback_emails = [
                'Buyer' => 'xformtech50@gmail.com',
                'Purchase Manager' => 'xformtech51@xform.in',
                'Director' => 'xformtech52@xform.in',
                'Admin' => 'xformtech53@xform.in'
            ];

            // Merge with fallback (fallback only used if not in database)
            foreach ($fallback_emails as $role => $email) {
                if (!isset($role_emails[$role])) {
                    $role_emails[$role] = $email;
                }
            }

            return $role_emails;
        } catch (Exception $e) {
            error_log("Error in get_all_role_emails_from_db: " . $e->getMessage());

            // Return hardcoded fallback if database fails
            return [
                'Buyer' => 'xformtech50@gmail.com',
                'Purchase Manager' => 'xformtech51@xform.in',
                'Director' => 'xformtech52@xform.in',
                'Site Incharge' => 'xformtech54@gmail.com',
                'Store Incharge' => 'xformtech55@gmail.com',
                'QA / Project Head' => 'xformtech56@gmail.com',
                'Accounts' => 'xformtech57@gmail.com',
                'Procurement Head' => 'xformtech58@xform.in',
                'Manager' => 'xformtech59@xform.in',
                'Admin' => 'xformtech53@xform.in'
            ];
        }
    }

    // Process PO approval
    public function process_po_approval($approval_id, $action, $remarks, $user_email, $user_id)
    {
        try {
            $this->db->trans_begin();

            // Get approval details
            $approval = $this->db->where('approval_id', $approval_id)->get('po_approvals')->row_array();
            if (!$approval) throw new Exception('Approval not found!');

            // Update current approval
            $this->db->where('approval_id', $approval_id);
            $this->db->update('po_approvals', [
                'status' => $action,
                'remarks' => $remarks,
                'action_by' => $user_email,
                'action_date' => date('Y-m-d H:i:s')
            ]);

            // Get all approvals for this PO
            $this->db->where('po_id_fk', $approval['po_id_fk']);
            $this->db->order_by('level', 'asc');
            $all_approvals = $this->db->get('po_approvals')->result_array();

            if ($action == 'approved') {
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
                    $this->db->where('id', $approval['po_id_fk']);
                    $this->db->update('po_total', [
                        'approval_level' => $next_approval['approval_level'],
                        'current_approver' => $next_approval['approver_email'],
                        'approval_status' => 'pending_approval'
                    ]);
                } else {
                    // All approvals done
                    $this->db->where('id', $approval['po_id_fk']);
                    $this->db->update('po_total', [
                        'approval_status' => 'approved',
                        'approved_at' => date('Y-m-d H:i:s'),
                        'approved_by' => $user_id
                    ]);
                    // TODO: Send PO to vendor
                }
            } else if ($action == 'rejected') {
                // Reject PO
                $this->db->where('id', $approval['po_id_fk']);
                $this->db->update('po_total', [
                    'approval_status' => 'rejected',
                    'rejection_reason' => $remarks,
                    'rejected_at' => date('Y-m-d H:i:s'),
                    'rejected_by' => $user_id
                ]);

                // Reject all pending approvals
                $this->db->where('po_id_fk', $approval['po_id_fk']);
                $this->db->where('status', 'pending');
                $this->db->update('po_approvals', [
                    'status' => 'rejected',
                    'action_date' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            error_log("Error processing approval: " . $e->getMessage());
            return false;
        }
    }

    // Get PO details (robust lookup handling slashes/hyphens)
    public function get_po_details($po_number)
    {
        // 1. Exact match
        $this->db->select('po.*, s.company_name as supplier_name, s.fullname, s.email, s.mobile, s.address, s.gst, s.pancard');
        $this->db->from('po_total po');
        $this->db->join('supplier s', 'po.supplier_id_fk = s.supplier_id', 'left');
        $this->db->where('po.number_fk', $po_number);
        $res = $this->db->get()->row_array();

        if ($res) {
            return $res;
        }

        // 2. Match converting slashes to hyphens
        $this->db->select('po.*, s.company_name as supplier_name, s.fullname, s.email, s.mobile, s.address, s.gst, s.pancard');
        $this->db->from('po_total po');
        $this->db->join('supplier s', 'po.supplier_id_fk = s.supplier_id', 'left');
        $this->db->where("REPLACE(po.number_fk, '/', '-') =", $po_number);
        $res = $this->db->get()->row_array();

        if ($res) {
            return $res;
        }

        // 3. Match alphanumeric clean string
        $clean_param = preg_replace('/[^A-Za-z0-9]/', '', $po_number);
        $this->db->select('po.*, s.company_name as supplier_name, s.fullname, s.email, s.mobile, s.address, s.gst, s.pancard');
        $this->db->from('po_total po');
        $this->db->join('supplier s', 'po.supplier_id_fk = s.supplier_id', 'left');
        $this->db->where("REPLACE(REPLACE(REPLACE(po.number_fk, '/', ''), '-', ''), '(', '') =", $clean_param);
        return $this->db->get()->row_array();
    }

    // Get PO items
    public function get_po_items($po_number)
    {
        $this->db->group_start();
        $this->db->where('number', $po_number);
        $this->db->or_where("REPLACE(number, '/', '-') =", $po_number);
        $this->db->group_end();
        $this->db->order_by('po_id', 'ASC');
        return $this->db->get('purchase_order')->result_array();
    }

    // Get approval details
    public function get_approval_details($po_number)
    {
        $po = $this->get_po_details($po_number);
        $po_id = $po['id'] ?? 0;

        if (!$po_id) {
            return [];
        }

        $this->db->select('pa.*, user.username as action_by_name, role.role_name as action_by_role');
        $this->db->from('po_approvals pa');
        $this->db->join('user', 'pa.action_by = user.user_email', 'left');
        $this->db->join('role', 'user.role = role.role_id', 'left');
        $this->db->where('pa.po_id_fk', $po_id);
        $this->db->order_by('pa.created_at', 'ASC');
        return $this->db->get()->result_array();
    }

    // Get pending approvals for a user
    public function get_pending_approvals($user_email)
    {
        $is_admin = $this->is_admin_user($user_email);
        $user_roles = [];
        $locations = [];
        if (!$is_admin) {
            $user_info = $this->get_user_roles_and_locations($user_email);
            $user_roles = $user_info['roles'];
            $locations = $user_info['locations'];
        }

        $this->db->select('pa.*, pt.date, pt.number_fk as po_number, pt.total, pt.so_no, pt.oc_no, supplier.company_name as supplier_name');
        $this->db->from('po_approvals pa');
        $this->db->join('po_total pt', 'pa.po_id_fk = pt.id', 'left');
        $this->db->join('supplier', 'pt.supplier_id_fk = supplier.supplier_id', 'left');
        $this->db->join('purchase_requisition', 'pt.pr_id = purchase_requisition.pr_id', 'left');
        
        if (!$is_admin) {
            $this->db->group_start();
            $this->db->where('pa.approver_email', $user_email);
            if (!empty($user_roles)) {
                $this->db->or_group_start();
                $this->db->where_in('pa.approver_role', $user_roles);
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

        $this->db->where('pa.status', 'pending');
        $this->db->order_by('pa.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    // Get approval history
    public function get_approval_history($user_email)
    {
        $is_admin = $this->is_admin_user($user_email);
        $user_roles = [];
        $locations = [];
        if (!$is_admin) {
            $user_info = $this->get_user_roles_and_locations($user_email);
            $user_roles = $user_info['roles'];
            $locations = $user_info['locations'];
        }

        $this->db->select('pa.*, pt.date, pt.number_fk as po_number, pt.total, pt.so_no, pt.oc_no, supplier.company_name as supplier_name, user.username as action_by_name, role.role_name as action_by_role');
        $this->db->from('po_approvals pa');
        $this->db->join('po_total pt', 'pa.po_id_fk = pt.id', 'left');
        $this->db->join('supplier', 'pt.supplier_id_fk = supplier.supplier_id', 'left');
        $this->db->join('purchase_requisition', 'pt.pr_id = purchase_requisition.pr_id', 'left');
        $this->db->join('user', 'pa.action_by = user.user_email', 'left');
        $this->db->join('role', 'user.role = role.role_id', 'left');
        
        if (!$is_admin) {
            $this->db->group_start();
            $this->db->where('pa.approver_email', $user_email);
            if (!empty($user_roles)) {
                $this->db->or_group_start();
                $this->db->where_in('pa.approver_role', $user_roles);
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

        $this->db->where_in('pa.status', ['approved', 'rejected']);
        $this->db->order_by('pa.action_date', 'DESC');
        return $this->db->get()->result_array();
    }

    // Get pending count
    public function get_pending_count($user_email)
    {
        $is_admin = $this->is_admin_user($user_email);
        $user_roles = [];
        $locations = [];
        if (!$is_admin) {
            $user_info = $this->get_user_roles_and_locations($user_email);
            $user_roles = $user_info['roles'];
            $locations = $user_info['locations'];
        }

        $this->db->from('po_approvals pa');
        $this->db->join('po_total pt', 'pa.po_id_fk = pt.id', 'left');
        $this->db->join('purchase_requisition', 'pt.pr_id = purchase_requisition.pr_id', 'left');
        
        if (!$is_admin) {
            $this->db->group_start();
            $this->db->where('pa.approver_email', $user_email);
            if (!empty($user_roles)) {
                $this->db->or_group_start();
                $this->db->where_in('pa.approver_role', $user_roles);
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

        $this->db->where('pa.status', 'pending');
        try {
            return $this->db->count_all_results();
        } catch (Exception $e) {
            log_message('error', 'PENDING COUNT QUERY FAILED: ' . $this->db->last_query());
            throw $e;
        }
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


    // Helper to get roles and locations
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

    // Fix wrong PO numbers
    public function fix_wrong_po_numbers()
    {
        $this->db->select('id, number_fk');
        $this->db->from('po_total');
        $this->db->where("number_fk NOT REGEXP '^PO/[0-9]{4}/[A-Z]{3}/[0-9]{2}-[0-9]{2}$'");
        $this->db->or_where("number_fk LIKE 'PO/2026/%'");
        $wrong_pos = $this->db->get()->result_array();

        $fixes = [];
        foreach ($wrong_pos as $po) {
            $old_number = $po['number_fk'];

            if (preg_match('/^PO\/\d{4}\/[A-Z]{3}\/\d{4}-\d{2}$/', $old_number)) {
                $parts = explode('/', $old_number);
                $year_part = $parts[3];
                $year_parts = explode('-', $year_part);
                if (count($year_parts) == 2 && strlen($year_parts[0]) == 4) {
                    $new_year = substr($year_parts[0], -2) . '-' . $year_parts[1];
                    $new_number = 'PO/' . $parts[1] . '/' . $parts[2] . '/' . $new_year;
                    $this->update_po_number($po['id'], $old_number, $new_number);
                    $fixes[] = ['id' => $po['id'], 'old' => $old_number, 'new' => $new_number];
                }
            } elseif (preg_match('/^PO\/2026\/[A-Z]{3}\/[0-9]{2}-[0-9]{2}$/', $old_number)) {
                $parts = explode('/', $old_number);
                $month = $parts[2];
                $financial_year = $parts[3];
                $next_number = $this->get_next_po_for_month_year($month, $financial_year);
                $new_number = 'PO/' . str_pad($next_number, 4, '0', STR_PAD_LEFT) . '/' . $month . '/' . $financial_year;
                $this->update_po_number($po['id'], $old_number, $new_number);
                $fixes[] = ['id' => $po['id'], 'old' => $old_number, 'new' => $new_number];
            }
        }
        return $fixes;
    }

    private function update_po_number($po_id, $old_number, $new_number)
    {
        $this->db->where('id', $po_id);
        $this->db->update('po_total', ['number_fk' => $new_number]);

        $this->db->where('number', $old_number);
        $this->db->update('purchase_order', ['number' => $new_number]);

        $this->db->where('po_number', $old_number);
        $this->db->update('po_approvals', ['po_number' => $new_number]);
    }

    private function get_next_po_for_month_year($month, $financial_year)
    {
        $this->db->select('number_fk');
        $this->db->from('po_total');
        $this->db->like('number_fk', '/' . $month . '/' . $financial_year, 'before');
        $this->db->order_by('number_fk', 'DESC');
        $query = $this->db->get();

        $max_number = 0;
        foreach ($query->result_array() as $row) {
            if (preg_match('/PO\/(\d+)\/' . $month . '\/' . $financial_year . '/', $row['number_fk'], $matches)) {
                $num = (int)$matches[1];
                if ($num > $max_number) $max_number = $num;
            }
        }
        return $max_number + 1;
    }

    // Add these methods to Purchase_model.php

    public function send_draft_to_vendor($draft_id, $vendor_email, $user_id)
    {
        try {
            $this->db->trans_begin();

            // Get draft details
            $draft = $this->db->where('draft_id', $draft_id)
                ->where('created_by', $user_id)
                ->get('po_drafts')
                ->row_array();

            if (!$draft) {
                throw new Exception('Draft not found!');
            }

            // Generate a draft PO number (temporary)
            $draft_po_number = 'DRAFT-' . date('Ymd') . '-' . rand(1000, 9999);

            // Parse draft data
            $po_data = json_decode($draft['po_data'], true);

            // Update draft with vendor email and status
            $this->db->where('draft_id', $draft_id)
                ->update('po_drafts', [
                    'vendor_email' => $vendor_email,
                    'draft_po_number' => $draft_po_number,
                    'sent_to_vendor_at' => date('Y-m-d H:i:s'),
                    'status' => 'sent_to_vendor'
                ]);

            // Log the email send
            $this->db->insert('po_email_logs', [
                'po_draft_id' => $draft_id,
                'email_type' => 'draft_to_vendor',
                'recipient_email' => $vendor_email,
                'sent_by' => $user_id,
                'sent_at' => date('Y-m-d H:i:s'),
                'status' => 'sent'
            ]);

            $this->db->trans_commit();

            return [
                'success' => true,
                'draft_po_number' => $draft_po_number,
                'vendor_email' => $vendor_email,
                'po_data' => $po_data
            ];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function get_draft_for_email($draft_id)
    {
        $draft = $this->db->where('draft_id', $draft_id)->get('po_drafts')->row_array();
        if ($draft) {
            $draft['po_data'] = json_decode($draft['po_data'], true);
        }
        return $draft;
    }
}
