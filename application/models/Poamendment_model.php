<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Poamendment_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('login');
    }

    /**
     * Generate amendment number
     */
    public function generate_amendment_no()
    {
        $prefix = 'AMEND';
        $year = date('Y');
        $month = date('m');

        $this->db->select('COUNT(*) as total');
        $this->db->from('po_amendments');
        $this->db->like('amendment_no', $prefix . '-' . $year . $month, 'after');
        $query = $this->db->get();
        $count = $query->row()->total + 1;

        return $prefix . '-' . $year . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get PO details for amendment
     */
    public function get_po_for_amendment($po_id)
    {
        $this->db->select('pt.*, s.company_name, s.email as vendor_email, s.fullname as vendor_name, s.supplier_id');
        $this->db->from('po_total pt');
        $this->db->join('supplier s', 's.supplier_id = pt.supplier_id_fk', 'left');
        $this->db->where('pt.id', $po_id);

        return $this->db->get()->row_array();
    }

    /**
     * Get PO items
     */
    public function get_po_items($po_number)
    {
        $this->db->where('number', $po_number);
        return $this->db->get('purchase_order')->result_array();
    }

    /**
     * Submit amendment for approval using dynamic matrix
     */
    public function submit_for_approval($amendment_id, $amendment_value)
    {
        // Get user ID from session
        $session_data_head = $this->session->userdata('session_data_head');
        $user_id = isset($session_data_head['result']['user_id']) ? $session_data_head['result']['user_id'] : 1;
        $user_email = isset($session_data_head['result']['email']) ? $session_data_head['result']['email'] : null;

        // Update amendment status
        $this->db->where('amendment_id', $amendment_id);
        $this->db->update('po_amendments', array(
            'status' => 'pending_approval',
            'amendment_value' => $amendment_value,
            'submitted_date' => date('Y-m-d H:i:s')
        ));

        // Get approvers based on dynamic matrix from approval_matrix
        $approvers = $this->get_approvers_from_matrix($amendment_value, 'PA');

        // Get user info for initiator
        $user_info = $this->login->get_user_info_by_email($user_email);
        $initiator_name = $user_info ? $user_info->username : 'System';

        // Insert approval records
        foreach ($approvers as $approver) {
            $approver_email = $this->get_approver_email_by_role($approver['approver_role']);
            $approval_data = array(
                'amendment_id' => $amendment_id,
                'approval_level' => $approver['level'],
                'approver_role' => $approver['approver_role'],
                'approver_email' => $approver_email,
                'status' => 'pending',
                'remarks' => '',
                'initiated_by' => $initiator_name,
                'initiated_email' => $user_email,
                'created_at' => date('Y-m-d H:i:s'),
                'uid' => $user_id
            );
            $this->db->insert('amendment_approvals', $approval_data);
        }

        // Send email notifications to approvers
        $this->send_approval_notifications($amendment_id);

        return true;
    }

    /**
     * Get approvers from approval matrix
     */
    public function get_approvers_from_matrix($amount, $document_type = 'PA')
    {
        $this->db->select('*');
        $this->db->from('approval_matrix');
        $this->db->where('document_type', $document_type);
        $this->db->where('status', 'active');
        $this->db->where('min_amount <=', $amount);
        $this->db->group_start();
        $this->db->where('max_amount >=', $amount);
        $this->db->or_where('max_amount', 0);
        $this->db->or_where('max_amount IS NULL', null, false);
        $this->db->group_end();
        $this->db->order_by('level', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Get approver email by role
     */
    private function get_approver_email_by_role($role)
    {
        // Try to get email from users table based on role
        $this->db->select('user_email');
        $this->db->from('user');
        $this->db->like('role', $role);
        $this->db->limit(1);
        $user = $this->db->get()->row();




        // var_dump($user);

        // die();

        if ($user && !empty($user->user_email)) {
            return $user->user_email;
        }

        // Fallback to default emails
        // $role_emails = [
        //     'Buyer' => 'buyer@xform.in',
        //     'Purchase Manager' => 'purchase.manager@xform.in',
        //     'Director' => 'director@xform.in',
        //     'Site Incharge' => 'site.incharge@xform.in',
        //     'Store Incharge' => 'store.incharge@xform.in',
        //     'QA / Project Head' => 'qa@xform.in',
        //     'Accounts' => 'accounts@xform.in'
        // ];

        // return isset($role_emails[$role]) ? $role_emails[$role] : 'admin@xform.in';
    }

    /**
     * Get amendment by ID
     */
    public function get_amendment($amendment_id)
    {
        $this->db->select('pa.*, u.username as initiated_by_name, u.user_email as initiated_email');
        $this->db->from('po_amendments pa');
        $this->db->join('user u', 'u.user_id = pa.initiated_by', 'left');
        $this->db->where('pa.amendment_id', $amendment_id);

        $amendment = $this->db->get()->row_array();

        if ($amendment) {
            // Get amendment items
            $this->db->where('amendment_id', $amendment_id);
            $amendment['items'] = $this->db->get('amendment_items')->result_array();

            // Get approval status with approver details
            $this->db->select('aa.*, u2.username as approver_name');
            $this->db->from('amendment_approvals aa');
            $this->db->join('user u2', 'u2.user_email = aa.approver_email', 'left');
            $this->db->where('aa.amendment_id', $amendment_id);
            $this->db->order_by('aa.approval_level', 'ASC');
            $amendment['approvals'] = $this->db->get()->result_array();

            // Get PO details
            $amendment['po_details'] = $this->get_po_for_amendment($amendment['po_id_fk']);
        }

        return $amendment;
    }

    /**
     * Get all amendments
     */
    public function get_all_amendments($filters = array(), $limit = null, $offset = null)
    {
        $this->db->select('pa.*, s.company_name as vendor_name, u.username as initiated_by_name');
        $this->db->from('po_amendments pa');
        $this->db->join('po_total pt', 'pt.id = pa.po_id_fk', 'left');
        $this->db->join('supplier s', 's.supplier_id = pt.supplier_id_fk', 'left');
        $this->db->join('user u', 'u.user_id = pa.initiated_by', 'left');

        // Apply filters
        if (!empty($filters['po_number'])) {
            $this->db->like('pa.po_number', $filters['po_number']);
        }
        if (!empty($filters['amendment_no'])) {
            $this->db->like('pa.amendment_no', $filters['amendment_no']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('pa.status', $filters['status']);
        }
        if (!empty($filters['amendment_type'])) {
            $this->db->where('pa.amendment_type', $filters['amendment_type']);
        }

        $this->db->order_by('pa.initiated_date', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Update amendment
     */
    public function update_amendment($amendment_id, $data)
    {
        $this->db->where('amendment_id', $amendment_id);
        return $this->db->update('po_amendments', $data);
    }

    /**
     * Delete amendment
     */
    public function delete_amendment($amendment_id)
    {
        $this->db->where('amendment_id', $amendment_id);
        return $this->db->update('po_amendments', array('status' => 'cancelled'));
    }

    /**
     * Update vendor acknowledgment
     */
    public function update_vendor_ack($amendment_id, $vendor_data)
    {
        $data = array(
            'vendor_acknowledged' => 1,
            'vendor_ack_date' => date('Y-m-d H:i:s'),
            'vendor_ack_by' => $vendor_data['ack_by'],
            'vendor_notes' => $vendor_data['ack_notes'],
            'status' => 'vendor_acknowledged'
        );

        $this->db->where('amendment_id', $amendment_id);
        return $this->db->update('po_amendments', $data);
    }

    /**
     * Update revised PO details
     */
    public function update_revised_po($amendment_id, $revised_data)
    {
        $data = array(
            'revised_po_issued' => 1,
            'revised_po_date' => date('Y-m-d H:i:s'),
            'revised_po_number' => $revised_data['revised_po_number'],
            'status' => 'revised_po_issued'
        );

        $this->db->where('amendment_id', $amendment_id);
        return $this->db->update('po_amendments', $data);
    }

    /**
     * Update approval status (Approve/Reject)
     */
    public function update_approval($approval_id, $status, $remarks = '')
    {
        // Get user info from session
        $session_data_head = $this->session->userdata('session_data_head');
        $user_email = isset($session_data_head['result']['email']) ? $session_data_head['result']['email'] : 'admin@xform.in';
        $user_id = isset($session_data_head['result']['user_id']) ? $session_data_head['result']['user_id'] : 1;
        $user_name = isset($session_data_head['result']['username']) ? $session_data_head['result']['username'] : 'Admin';

        // Start transaction
        $this->db->trans_start();

        // Get approval details
        $this->db->where('approval_id', $approval_id);
        $approval = $this->db->get('amendment_approvals')->row_array();

        if (!$approval) {
            $this->db->trans_rollback();
            return false;
        }

        $amendment_id = $approval['amendment_id'];

        // Update approval record
        $approval_data = array(
            'status' => $status,
            'remarks' => $remarks,
            'action_date' => date('Y-m-d H:i:s'),
            'action_by' => $user_email,
            'action_by_name' => $user_name,
            'action_by_id' => $user_id
        );

        $this->db->where('approval_id', $approval_id);
        $this->db->update('amendment_approvals', $approval_data);

        // Handle rejection
        if ($status == 'rejected') {
            // Update amendment status to rejected
            $amendment_data = array(
                'status' => 'rejected',
                'rejection_reason' => $remarks,
                'rejected_by' => $user_email,
                'rejected_by_name' => $user_name,
                'rejected_date' => date('Y-m-d H:i:s')
            );
            $this->db->where('amendment_id', $amendment_id);
            $this->db->update('po_amendments', $amendment_data);
        }
        // Handle approval
        elseif ($status == 'approved') {
            // Check if there are more pending approvals
            $this->db->where('amendment_id', $amendment_id);
            $this->db->where('status', 'pending');
            $pending_approvals = $this->db->count_all_results('amendment_approvals');

            if ($pending_approvals == 0) {
                // All approvals are done, update amendment status to approved
                $amendment_data = array(
                    'status' => 'approved',
                    'approved_date' => date('Y-m-d H:i:s'),
                    'approved_by' => $user_email,
                    'approved_by_name' => $user_name
                );
                $this->db->where('amendment_id', $amendment_id);
                $this->db->update('po_amendments', $amendment_data);
            }
            // If there are still pending approvals, amendment remains in pending_approval status
        }

        // Commit transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        // Send notifications
        $this->send_approval_action_notifications($amendment_id, $status, $remarks, $user_name);

        return true;
    }

    /**
     * Get pending approvals for user
     */
    public function get_pending_approvals($user_email)
    {


        //  $user_email = "rajan@xform.in";


        // // die();
        $this->db->select('aa.*, pa.amendment_no, pa.description, pa.po_number, pa.initiated_date, 
                          pa.amendment_value, pa.amendment_type, pa.status as amendment_status,
                          s.company_name as vendor_name, u.username as initiated_by');
        $this->db->from('amendment_approvals aa');
        $this->db->join('po_amendments pa', 'pa.amendment_id = aa.amendment_id');
        $this->db->join('po_total pt', 'pt.id = pa.po_id_fk', 'left');
        $this->db->join('supplier s', 's.supplier_id = pt.supplier_id_fk', 'left');
        $this->db->join('user u', 'u.user_id = pa.initiated_by', 'left');
        $this->db->where('aa.approver_email', $user_email);
        $this->db->where('aa.status', 'pending');
        $this->db->where('pa.status', 'pending_approval');
        $this->db->order_by('aa.created_at', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Get amendment approval history for user
     */
    public function get_approval_history($user_email)
    {
        $this->db->select('aa.*, pa.amendment_no, pa.po_number, pa.amendment_value, 
                          pa.amendment_type, s.company_name as vendor_name');
        $this->db->from('amendment_approvals aa');
        $this->db->join('po_amendments pa', 'pa.amendment_id = aa.amendment_id');
        $this->db->join('po_total pt', 'pt.id = pa.po_id_fk', 'left');
        $this->db->join('supplier s', 's.supplier_id = pt.supplier_id_fk', 'left');
        $this->db->where('aa.approver_email', $user_email);
        $this->db->where('aa.status !=', 'pending');
        $this->db->order_by('aa.action_date', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Get user's email from session
     */
    public function get_user_email()
    {
        $session_data_head = $this->session->userdata('session_data_head');


        // var_dump($session_data_head);

        // die();
        if (isset($session_data_head['result']['user_email']) && !empty($session_data_head['result']['user_email'])) {
            return $session_data_head['result']['user_email'];
        }
        return $this->session->userdata('user_email') ?: 'admin@xform.in';
    }

    /**
     * Create amendment
     */
    public function create_amendment($amendment_data)
    {
        $this->db->trans_start();

        // Generate amendment number
        $amendment_no = $this->generate_amendment_no();


        $this->db->select('number_fk');
        $this->db->from('po_total');
        $this->db->where('id', $amendment_data['po_id']);
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        $var = $query->row_array();



        // var_dump($var);


        // die();

        // Prepare amendment data - map po_id to po_id_fk
        $amendment_main = array(
            'amendment_no' => $amendment_no,
            'po_id_fk' => $amendment_data['po_id'], // Changed from po_id to po_id_fk
            'po_number' => $var['number_fk'],
            'amendment_type' => $amendment_data['amendment_type'],
            'initiated_by' => $amendment_data['user_id'] ?? 1,
            'initiated_date' => date('Y-m-d H:i:s'),
            'description' => $amendment_data['description'],
            'reason' => $amendment_data['reason'],
            'amendment_value' => $amendment_data['amendment_value'] ?? 0,
            'attachment' => $amendment_data['attachment'] ?? null,
            'status' => isset($amendment_data['submit_for_approval']) ? 'pending_approval' : 'draft',
            'uid' => $amendment_data['user_id'] ?? 1
        );

        // Insert amendment
        $this->db->insert('po_amendments', $amendment_main);
        $amendment_id = $this->db->insert_id();

        // Save amendment items if exists
        if (isset($amendment_data['amendment_items']) && !empty($amendment_data['amendment_items'])) {
            foreach ($amendment_data['amendment_items'] as $item) {
                $item_data = array(
                    'amendment_id' => $amendment_id,
                    'po_item_id' => $item['po_item_id'] ?? 0,
                    'field_type' => $item['change_type'] ?? 'general',
                    'field_name' => $item['change_type'] ?? 'Item Amendment',
                    'old_value' => $item['old_value'] ?? '',
                    'new_value' => $item['new_value'] ?? '',
                    'change_amount' => $item['change_amount'] ?? 0,
                    'change_description' => $item['change_description'] ?? '',
                    'created_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert('amendment_items', $item_data);
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $amendment_id : false;
    }
    private function get_old_value_for_item($item)
    {
        $parts = [];
        if (isset($item['current_quantity'])) {
            $parts[] = 'Qty: ' . $item['current_quantity'];
        }
        if (isset($item['current_price'])) {
            $parts[] = 'Price: ₹' . $item['current_price'];
        }
        return implode(' | ', $parts);
    }

    private function get_new_value_for_item($item)
    {
        $parts = [];
        if (isset($item['new_quantity']) && !empty($item['new_quantity'])) {
            $parts[] = 'Qty: ' . $item['new_quantity'];
        }
        if (isset($item['new_price']) && !empty($item['new_price'])) {
            $parts[] = 'Price: ₹' . $item['new_price'];
        }
        return implode(' | ', $parts);
    }

    /**
     * Get PO revisions
     */
    public function get_po_revisions($po_number)
    {
        $this->db->where('original_po_number', $po_number)
            ->or_where('po_number', $po_number);
        $this->db->order_by('revision_number', 'ASC');
        return $this->db->get('po_amendments')->result_array();
    }

    /**
     * Get PO amendments (same as get_po_revisions but different name for clarity)
     */
    public function get_po_amendments($po_number, $user_id = null)
    {
        // Open parentheses for OR conditions
        $this->db->group_start();

        if (is_array($po_number)) {
            // If $po_number is an array, use where_in / or_where_in
            $this->db->where_in('po_number', $po_number);
            $this->db->or_where_in('original_po_number', $po_number);
        } else {
            // If $po_number is a single value, use simple where / or_where
            $this->db->where('po_number', $po_number);
            $this->db->or_where('original_po_number', $po_number);
        }

        $this->db->group_end(); // close parentheses

        // Optional user filter
        if ($user_id) {
            $this->db->where('uid', $user_id);
        }

        $this->db->order_by('initiated_date', 'DESC');

        // Execute query and return result
        return $this->db->get('po_amendments')->result_array();
    }

    /**
     * Get dashboard counts
     */
    public function get_dashboard_counts()
    {
        $data = array();

        // Draft amendments
        $this->db->where('status', 'draft');
        $data['draft'] = $this->db->count_all_results('po_amendments');

        // Pending approval
        $this->db->where('status', 'pending_approval');
        $data['pending_approval'] = $this->db->count_all_results('po_amendments');

        // Approved, awaiting vendor ack
        $this->db->where('status', 'approved');
        $this->db->where('vendor_acknowledged', 0);
        $data['awaiting_vendor'] = $this->db->count_all_results('po_amendments');

        // Vendor acknowledged, awaiting revised PO
        $this->db->where('vendor_acknowledged', 1);
        $this->db->where('revised_po_issued', 0);
        $data['awaiting_revised_po'] = $this->db->count_all_results('po_amendments');

        // Completed
        $this->db->where('status', 'completed');
        $data['completed'] = $this->db->count_all_results('po_amendments');

        // Rejected
        $this->db->where('status', 'rejected');
        $data['rejected'] = $this->db->count_all_results('po_amendments');

        return $data;
    }

    /**
     * Send approval notifications
     */
    private function send_approval_notifications($amendment_id)
    {
        // Get amendment details
        $amendment = $this->get_amendment($amendment_id);

        if (!$amendment) return;

        // Get pending approvers
        $this->db->where('amendment_id', $amendment_id);
        $this->db->where('status', 'pending');
        $this->db->order_by('approval_level', 'ASC');
        $approvers = $this->db->get('amendment_approvals')->result_array();

        foreach ($approvers as $approver) {
            // Send email notification
            $subject = "PO Amendment Approval Required: " . $amendment['amendment_no'];
            $message = "Dear " . $approver['approver_role'] . ",\n\n";
            $message .= "A PO Amendment requires your approval.\n";
            $message .= "Amendment No: " . $amendment['amendment_no'] . "\n";
            $message .= "PO Number: " . $amendment['po_number'] . "\n";
            $message .= "Amendment Value: ₹" . number_format($amendment['amendment_value'], 2) . "\n";
            $message .= "Initiated By: " . $amendment['initiated_by_name'] . "\n";
            $message .= "Description: " . $amendment['description'] . "\n\n";
            $message .= "Please review and take appropriate action.\n\n";
            $message .= "Click here to review: " . base_url('PoamendmentController/approvals') . "\n\n";
            $message .= "Thank you,\nSystem Admin";

            // You can enable email sending when ready
            // $this->send_email($approver['approver_email'], $subject, $message);
        }
    }

    /**
     * Send approval action notifications
     */
    private function send_approval_action_notifications($amendment_id, $action, $remarks, $action_by)
    {
        $amendment = $this->get_amendment($amendment_id);
        if (!$amendment) return;

        // Get initiator email
        $initiator_email = $amendment['initiated_email'];

        if ($action == 'approved') {
            // Check if fully approved
            $this->db->where('amendment_id', $amendment_id);
            $this->db->where('status', 'pending');
            $pending_approvals = $this->db->count_all_results('amendment_approvals');

            if ($pending_approvals == 0) {
                // Fully approved
                $subject = "PO Amendment Fully Approved: " . $amendment['amendment_no'];
                $message = "Dear " . $amendment['initiated_by_name'] . ",\n\n";
                $message .= "Your PO Amendment has been fully approved.\n";
                $message .= "Amendment No: " . $amendment['amendment_no'] . "\n";
                $message .= "PO Number: " . $amendment['po_number'] . "\n";
                $message .= "Status: Approved\n\n";
                $message .= "You can now proceed with vendor acknowledgment.\n\n";
                $message .= "Click here to view: " . base_url('PoamendmentController/view/' . $amendment_id) . "\n\n";
                $message .= "Thank you,\nSystem Admin";

                // $this->send_email($initiator_email, $subject, $message);
            } else {
                // Partially approved, notify next approver
                $this->db->where('amendment_id', $amendment_id);
                $this->db->where('status', 'pending');
                $this->db->order_by('approval_level', 'ASC');
                $this->db->limit(1);
                $next_approver = $this->db->get('amendment_approvals')->row_array();

                if ($next_approver) {
                    $subject = "PO Amendment Approval Required (Level " . $next_approver['approval_level'] . "): " . $amendment['amendment_no'];
                    $message = "Dear " . $next_approver['approver_role'] . ",\n\n";
                    $message .= "A PO Amendment requires your approval.\n";
                    $message .= "Amendment No: " . $amendment['amendment_no'] . "\n";
                    $message .= "PO Number: " . $amendment['po_number'] . "\n";
                    $message .= "Previous level approved by: " . $action_by . "\n\n";
                    $message .= "Please review and take appropriate action.\n\n";
                    $message .= "Click here to review: " . base_url('PoamendmentController/approvals') . "\n\n";
                    $message .= "Thank you,\nSystem Admin";

                    // $this->send_email($next_approver['approver_email'], $subject, $message);
                }
            }
        } elseif ($action == 'rejected') {
            // Rejection notification
            $subject = "PO Amendment Rejected: " . $amendment['amendment_no'];
            $message = "Dear " . $amendment['initiated_by_name'] . ",\n\n";
            $message .= "Your PO Amendment has been rejected.\n";
            $message .= "Amendment No: " . $amendment['amendment_no'] . "\n";
            $message .= "PO Number: " . $amendment['po_number'] . "\n";
            $message .= "Rejected By: " . $action_by . "\n";
            $message .= "Rejection Reason: " . $remarks . "\n\n";
            $message .= "Please review and resubmit if necessary.\n\n";
            $message .= "Click here to view: " . base_url('PoamendmentController/view/' . $amendment_id) . "\n\n";
            $message .= "Thank you,\nSystem Admin";

            // $this->send_email($initiator_email, $subject, $message);
        }
    }

    /**
     * Check if user can approve/reject this amendment
     */
    public function can_user_approve($amendment_id, $user_email)
    {
        $this->db->where('amendment_id', $amendment_id);
        $this->db->where('approver_email', $user_email);
        $this->db->where('status', 'pending');
        $query = $this->db->get('amendment_approvals');

        return $query->num_rows() > 0;
    }

    /**
     * Check if user can approve by approval_id
     */
    public function can_user_approve_by_approval_id($approval_id, $user_email)
    {
        $this->db->where('approval_id', $approval_id);
        $this->db->where('approver_email', $user_email);
        $this->db->where('status', 'pending');
        $query = $this->db->get('amendment_approvals');

        return $query->num_rows() > 0;
    }

    /**
     * Get approval workflow status
     */
    public function get_approval_workflow_status($amendment_id)
    {
        $this->db->where('amendment_id', $amendment_id);
        $this->db->order_by('approval_level', 'ASC');
        return $this->db->get('amendment_approvals')->result_array();
    }

    /**
     * Get approval details by ID
     */
    public function get_approval_by_id($approval_id)
    {
        $this->db->select('aa.*, pa.*, u.username as initiated_by_name');
        $this->db->from('amendment_approvals aa');
        $this->db->join('po_amendments pa', 'pa.amendment_id = aa.amendment_id');
        $this->db->join('user u', 'u.user_id = pa.initiated_by');
        $this->db->where('aa.approval_id', $approval_id);
        return $this->db->get()->row_array();
    }

    /**
     * Get approval matrix configuration
     */
    public function get_approval_matrix_config($document_type = 'PA')
    {
        $this->db->where('document_type', $document_type);
        $this->db->where('status', 'active');
        $this->db->order_by('level', 'ASC');
        return $this->db->get('approval_matrix')->result_array();
    }

    /**
     * Get amendment statistics
     */
    public function get_amendment_statistics()
    {
        $data = array();

        // Total amendments
        $data['total'] = $this->db->count_all('po_amendments');

        // Amendments by status
        $this->db->select('status, COUNT(*) as count');
        $this->db->group_by('status');
        $status_counts = $this->db->get('po_amendments')->result_array();

        foreach ($status_counts as $status) {
            $data[$status['status']] = $status['count'];
        }

        // Amendments by month (last 6 months)
        $six_months_ago = date('Y-m-d', strtotime('-6 months'));
        $this->db->select("DATE_FORMAT(initiated_date, '%Y-%m') as month, COUNT(*) as count");
        $this->db->where('initiated_date >=', $six_months_ago);
        $this->db->group_by("DATE_FORMAT(initiated_date, '%Y-%m')");
        $this->db->order_by('month', 'DESC');
        $data['monthly_stats'] = $this->db->get('po_amendments')->result_array();

        return $data;
    }

    /**
     * Helper function to send email
     */
    private function send_email($to, $subject, $message)
    {
        // Configure email library
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'your_smtp_host',
            'smtp_port' => 587,
            'smtp_user' => 'your_email@domain.com',
            'smtp_pass' => 'your_password',
            'mailtype' => 'text',
            'charset' => 'utf-8'
        );

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");

        $this->email->from('noreply@yourcompany.com', 'PO Amendment System');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);

        return $this->email->send();
    }
}
