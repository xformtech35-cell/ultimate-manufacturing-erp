<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DeleteApprovalController
 * Handles item deletion requests that require Admin approval.
 * Non-admin users submit a deletion request; Admin approves/rejects from the notification dropdown.
 */
class DeleteApprovalController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Inventory', 'inventory');
        $this->load->model('Master', 'master');
        $this->_ensure_table();
    }

    // ─────────────────────────────────────────────────────────────
    // TABLE SETUP  (auto-creates on first use)
    // ─────────────────────────────────────────────────────────────
    private function _ensure_table()
    {
        if (!$this->db->table_exists('item_delete_requests')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `item_delete_requests` (
                    `id`                int(11)      NOT NULL AUTO_INCREMENT,
                    `item_id`           varchar(50)  NOT NULL,
                    `item_code`         varchar(100) DEFAULT NULL,
                    `item_name`         varchar(255) DEFAULT NULL,
                    `module`            varchar(100) NOT NULL COMMENT 'inventory / item_code_master',
                    `redirect_url`      varchar(500) DEFAULT NULL,
                    `requested_by`      int(11)      NOT NULL,
                    `requested_by_name` varchar(100) DEFAULT NULL,
                    `reason`            text         DEFAULT NULL,
                    `status`            enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                    `reviewed_by`       int(11)      DEFAULT NULL,
                    `review_remarks`    varchar(500) DEFAULT NULL,
                    `created_at`        datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at`        datetime     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
    }

    // ─────────────────────────────────────────────────────────────
    // SUBMIT DELETE REQUEST  (called by non-admin users)
    // ─────────────────────────────────────────────────────────────
    public function request_delete()
    {
        $item_id      = $this->input->get_post('item_id');
        $module       = $this->input->get_post('module');       // 'inventory' | 'item_code_master'
        $redirect_url = $this->input->get_post('redirect_url');
        $reason       = $this->input->get_post('reason');

        $session_data = $this->session->userdata('session_data_head');
        $requested_by = $session_data['result']['user_id']   ?? 0;
        $req_name     = $session_data['result']['username']  ?? 'Unknown';

        // Fetch item details for the request
        $item_code = '';
        $item_name = '';
        if ($module === 'inventory') {
            $item = $this->inventory->get_inventory_by_id($item_id);
            $item_code = $item['code']      ?? $item_id;
            $item_name = $item['item_name'] ?? '';
        } elseif ($module === 'item_code_master') {
            $item = $this->db->where('product_master_id', $item_id)->get('product_master')->row_array();
            $item_code = $item['product_master_name'] ?? $item_id;
            $item_name = $item['product_master_name'] ?? '';
        }

        // Check for existing pending request for same item
        $existing = $this->db
            ->where('item_id', $item_id)
            ->where('module', $module)
            ->where('status', 'pending')
            ->get('item_delete_requests')
            ->row_array();

        if ($existing) {
            $this->session->set_flashdata('INFOMSG', 'A deletion request for this item is already pending Admin approval.');
            redirect($redirect_url ?: 'InventoryController/index');
            return;
        }

        $data = [
            'item_id'           => $item_id,
            'item_code'         => $item_code,
            'item_name'         => $item_name,
            'module'            => $module,
            'redirect_url'      => $redirect_url,
            'requested_by'      => $requested_by,
            'requested_by_name' => $req_name,
            'reason'            => $reason,
            'status'            => 'pending',
        ];

        if ($this->db->insert('item_delete_requests', $data)) {
            $this->session->set_flashdata('INFOMSG', "Delete request submitted for item <strong>{$item_code}</strong>. Awaiting Admin approval.");
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Failed to submit deletion request. Please try again.');
        }

        redirect($redirect_url ?: 'InventoryController/index');
    }

    // ─────────────────────────────────────────────────────────────
    // AJAX: pending count badge
    // ─────────────────────────────────────────────────────────────
    public function get_pending_count()
    {
        $count = $this->db->where('status', 'pending')->count_all_results('item_delete_requests');
        echo json_encode(['count' => (int)$count]);
    }

    // ─────────────────────────────────────────────────────────────
    // AJAX: dropdown HTML list
    // ─────────────────────────────────────────────────────────────
    public function get_pending_requests_html()
    {
        $requests = $this->db
            ->where('status', 'pending')
            ->order_by('created_at', 'DESC')
            ->limit(15)
            ->get('item_delete_requests')
            ->result_array();

        if (empty($requests)) {
            echo '<li><a href="#" style="text-align:center;color:#999;">No pending deletion requests</a></li>';
            return;
        }

        foreach ($requests as $req) {
            $module_label = $req['module'] === 'inventory' ? 'Inventory' : 'Item Code Master';
            $time_ago     = $this->_time_ago($req['created_at']);
            $approve_url  = base_url('DeleteApprovalController/approve/' . $req['id']);
            $reject_url   = base_url('DeleteApprovalController/reject/'  . $req['id']);
            echo "
            <li style=\"border-bottom:1px solid #f0f0f0;\">
                <a href=\"#\" style=\"white-space:normal;padding:8px 12px;display:block;\">
                    <div style=\"display:flex;align-items:flex-start;gap:8px;\">
                        <span style=\"background:#d9534f;color:#fff;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;flex-shrink:0;\">
                            <i class=\"fa fa-trash\"></i>
                        </span>
                        <div style=\"flex:1;\">
                            <strong style=\"color:#d9534f;\">Delete Request</strong>
                            <div style=\"font-size:12px;color:#333;margin:2px 0;\">
                                <strong>" . htmlspecialchars($req['item_code']) . "</strong>
                                " . (!empty($req['item_name']) && $req['item_name'] !== $req['item_code'] ? '<span style=\"color:#888;\">— ' . htmlspecialchars($req['item_name']) . '</span>' : '') . "
                            </div>
                            <div style=\"font-size:11px;color:#777;\">
                                {$module_label} &bull; By: " . htmlspecialchars($req['requested_by_name']) . "
                            </div>
                            " . (!empty($req['reason']) ? '<div style="font-size:11px;color:#888;font-style:italic;">Reason: ' . htmlspecialchars(substr($req['reason'], 0, 60)) . '</div>' : '') . "
                            <div style=\"font-size:11px;color:#aaa;\">{$time_ago}</div>
                            <div style=\"margin-top:6px;display:flex;gap:6px;\">
                                <a href=\"{$approve_url}\" class=\"btn btn-xs btn-success del-approve-btn\" data-id=\"{$req['id']}\"
                                    onclick=\"return confirm('Approve deletion of [{$req['item_code']}]? This CANNOT be undone.');\">
                                    <i class=\"fa fa-check\"></i> Approve
                                </a>
                                <a href=\"{$reject_url}\" class=\"btn btn-xs btn-danger del-reject-btn\" data-id=\"{$req['id']}\">
                                    <i class=\"fa fa-times\"></i> Reject
                                </a>
                            </div>
                        </div>
                    </div>
                </a>
            </li>";
        }
    }

    // ─────────────────────────────────────────────────────────────
    // APPROVE  (Admin only)
    // ─────────────────────────────────────────────────────────────
    public function approve($id)
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $reviewer_id  = (int)($res['user_id'] ?? 0);

        if ($role_name !== 'admin' && $role_id !== 1 && $reviewer_id !== 1) {
            $this->session->set_flashdata('ERRORMSG', 'Only Admin can approve deletion requests.');
            redirect('InventoryController/index');
            return;
        }

        $req = $this->db->where('id', $id)->where('status', 'pending')->get('item_delete_requests')->row_array();
        if (empty($req)) {
            $this->session->set_flashdata('ERRORMSG', 'Request not found or already processed.');
            redirect('DeleteApprovalController/panel');
            return;
        }

        // Perform the actual deletion
        $deleted = false;
        if ($req['module'] === 'inventory') {
            try {
                $result = $this->inventory->delete_inventory_by_id($req['item_id']);
                $deleted = ($result === true);
                if ($result === 'CONSTRAIN_ERROR') {
                    $this->session->set_flashdata('ERRORMSG', "Cannot delete item <strong>{$req['item_code']}</strong>: it is referenced in other records.");
                    $this->db->where('id', $id)->update('item_delete_requests', [
                        'status'         => 'rejected',
                        'reviewed_by'    => $reviewer_id,
                        'review_remarks' => 'Auto-rejected: Foreign key constraint error',
                    ]);
                    redirect($req['redirect_url'] ?: 'DeleteApprovalController/panel');
                    return;
                }
            } catch (Exception $e) {
                $deleted = false;
            }
        } elseif ($req['module'] === 'item_code_master') {
            $result = $this->master->delete_product_by_id($req['item_id']);
            $deleted = ($result == true);
        }

        if ($deleted) {
            $this->db->where('id', $id)->update('item_delete_requests', [
                'status'      => 'approved',
                'reviewed_by' => $reviewer_id,
            ]);
            $this->session->set_flashdata('SUCCESSMSG', "Item <strong>{$req['item_code']}</strong> deleted successfully (request approved).");
        } else {
            $this->session->set_flashdata('ERRORMSG', "Failed to delete item <strong>{$req['item_code']}</strong>.");
        }

        redirect($req['redirect_url'] ?: 'DeleteApprovalController/panel');
    }

    // ─────────────────────────────────────────────────────────────
    // REJECT  (Admin only)
    // ─────────────────────────────────────────────────────────────
    public function reject($id)
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $reviewer_id  = (int)($res['user_id'] ?? 0);

        if ($role_name !== 'admin' && $role_id !== 1 && $reviewer_id !== 1) {
            $this->session->set_flashdata('ERRORMSG', 'Only Admin can reject deletion requests.');
            redirect('InventoryController/index');
            return;
        }

        $req = $this->db->where('id', $id)->where('status', 'pending')->get('item_delete_requests')->row_array();
        if (empty($req)) {
            $this->session->set_flashdata('ERRORMSG', 'Request not found or already processed.');
            redirect('DeleteApprovalController/panel');
            return;
        }

        $this->db->where('id', $id)->update('item_delete_requests', [
            'status'      => 'rejected',
            'reviewed_by' => $reviewer_id,
        ]);

        $this->session->set_flashdata('INFOMSG', "Deletion of item <strong>{$req['item_code']}</strong> was rejected.");
        redirect($req['redirect_url'] ?: 'DeleteApprovalController/panel');
    }

    // ─────────────────────────────────────────────────────────────
    // FULL PANEL PAGE  (Admin view all requests)
    // ─────────────────────────────────────────────────────────────
    public function panel()
    {
        $session_data = $this->session->userdata('session_data_head');
        $role_name    = $session_data['result']['role_name'] ?? '';

        if (strtolower($role_name) !== 'admin') {
            $this->session->set_flashdata('ERRORMSG', 'Access denied.');
            redirect('Dashboard');
            return;
        }

        $data['pending']  = $this->db->where('status', 'pending') ->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
        $data['history']  = $this->db->where_in('status', ['approved','rejected'])->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('admin/delete_approval_panel', $data);
        $this->load->view('admin/footer');
    }

    // ─────────────────────────────────────────────────────────────
    // HELPER: human-readable time ago
    // ─────────────────────────────────────────────────────────────
    private function _time_ago($datetime)
    {
        $time  = strtotime($datetime);
        $diff  = time() - $time;
        if ($diff < 60)     return 'just now';
        if ($diff < 3600)   return floor($diff/60) . ' min ago';
        if ($diff < 86400)  return floor($diff/3600) . ' hr ago';
        return floor($diff/86400) . ' days ago';
    }
}
