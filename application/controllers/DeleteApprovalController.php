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
            $table_name = $this->db->dbprefix('item_delete_requests');
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `$table_name` (
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
    // AJAX: pending count badge (Admin vs Non-Admin)
    // ─────────────────────────────────────────────────────────────
    public function get_pending_count()
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id      = (int)($res['user_id'] ?? 0);
        $is_admin     = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        if ($is_admin) {
            $count = $this->db->where('status', 'pending')->count_all_results('item_delete_requests');
        } else {
            $count = $this->db
                ->where_in('status', ['approved', 'rejected'])
                ->where('requested_by', $user_id)
                ->where('user_notified', 0)
                ->count_all_results('item_delete_requests');
        }
        echo json_encode(['count' => (int)$count]);
    }

    // ─────────────────────────────────────────────────────────────
    // AJAX: dropdown HTML list (Admin vs Non-Admin notifications)
    // ─────────────────────────────────────────────────────────────
    public function get_pending_requests_html()
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id      = (int)($res['user_id'] ?? 0);
        $is_admin     = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        if ($is_admin) {
            $requests = $this->db
                ->where('status', 'pending')
                ->order_by('created_at', 'DESC')
                ->limit(15)
                ->get('item_delete_requests')
                ->result_array();

            if (empty($requests)) {
                echo '<li><a href="#" style="text-align:center;color:#999;padding:15px 0;">No pending deletion requests</a></li>';
                return;
            }

            foreach ($requests as $req) {
                $module_label = $req['module'] === 'inventory' ? 'Inventory' : 'Item Code Master';
                $time_ago     = $this->_time_ago($req['created_at']);
                $approve_url  = base_url('DeleteApprovalController/approve/' . $req['id']);
                $reject_url   = base_url('DeleteApprovalController/reject/'  . $req['id']);
                echo "
                <li style=\"border-bottom:1px solid #f0f0f0;\">
                    <div style=\"white-space:normal;padding:8px 12px;display:block;\">
                        <div style=\"display:flex;align-items:flex-start;gap:8px;\">
                            <span style=\"background:#f39c12;color:#fff;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;flex-shrink:0;\">
                                <i class=\"fa fa-clock-o\"></i>
                            </span>
                            <div style=\"flex:1;\">
                                <strong style=\"color:#f39c12;\">Delete Request (Pending)</strong>
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
                                    <a href=\"{$approve_url}\" class=\"btn btn-xs btn-success\"
                                        onclick=\"return confirm('Approve deletion request of [{$req['item_code']}]? The requester will be notified to perform actual deletion.');\">
                                        <i class=\"fa fa-check\"></i> Approve
                                    </a>
                                    <a href=\"javascript:void(0);\" class=\"btn btn-xs btn-danger\"
                                        onclick=\"var rem = prompt('Enter rejection remarks:'); if(rem !== null) { window.location.href = '{$reject_url}?remarks=' + encodeURIComponent(rem); }\">
                                        <i class=\"fa fa-times\"></i> Reject
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>";
            }
        } else {
            // Non-Admin: show approved or rejected requests not yet dismissed
            $requests = $this->db
                ->where_in('status', ['approved', 'rejected'])
                ->where('requested_by', $user_id)
                ->where('user_notified', 0)
                ->order_by('updated_at', 'DESC')
                ->limit(15)
                ->get('item_delete_requests')
                ->result_array();

            if (empty($requests)) {
                echo '<li><a href="#" style="text-align:center;color:#999;padding:15px 0;">No new deletion notifications</a></li>';
                return;
            }

            foreach ($requests as $req) {
                $time_ago     = $this->_time_ago($req['updated_at'] ?: $req['created_at']);
                $dismiss_url  = base_url('DeleteApprovalController/dismiss_notification/' . $req['id']);
                $delete_url   = base_url('DeleteApprovalController/execute_delete/' . $req['id']);
                
                if ($req['status'] === 'approved') {
                    echo "
                    <li style=\"border-bottom:1px solid #f0f0f0;\">
                        <div style=\"white-space:normal;padding:8px 12px;display:block;\">
                            <div style=\"display:flex;align-items:flex-start;gap:8px;\">
                                <span style=\"background:#27ae60;color:#fff;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;flex-shrink:0;\">
                                    <i class=\"fa fa-check\"></i>
                                </span>
                                <div style=\"flex:1;\">
                                    <strong style=\"color:#27ae60;\">Delete Request Approved</strong>
                                    <div style=\"font-size:12px;color:#333;margin:2px 0;\">
                                        Admin approved deletion of <strong>" . htmlspecialchars($req['item_code']) . "</strong>.
                                    </div>
                                    <div style=\"font-size:11px;color:#aaa;\">{$time_ago}</div>
                                    <div style=\"margin-top:6px;display:flex;gap:6px;\">
                                        <a href=\"{$delete_url}\" class=\"btn btn-xs btn-danger\"
                                            onclick=\"return confirm('Delete [{$req['item_code']}] permanently from system? This CANNOT be undone.');\">
                                            <i class=\"fa fa-trash\"></i> Delete Now
                                        </a>
                                        <a href=\"{$dismiss_url}\" class=\"btn btn-xs btn-default\">
                                            Dismiss
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>";
                } else {
                    echo "
                    <li style=\"border-bottom:1px solid #f0f0f0;\">
                        <div style=\"white-space:normal;padding:8px 12px;display:block;\">
                            <div style=\"display:flex;align-items:flex-start;gap:8px;\">
                                <span style=\"background:#d9534f;color:#fff;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;flex-shrink:0;\">
                                    <i class=\"fa fa-times\"></i>
                                </span>
                                <div style=\"flex:1;\">
                                    <strong style=\"color:#d9534f;\">Delete Request Rejected</strong>
                                    <div style=\"font-size:12px;color:#333;margin:2px 0;\">
                                        Admin rejected deletion of <strong>" . htmlspecialchars($req['item_code']) . "</strong>.
                                        " . (!empty($req['review_remarks']) ? '<div style="margin-top:2px;font-style:italic;color:#e74c3c;">Remarks: ' . htmlspecialchars($req['review_remarks']) . '</div>' : '') . "
                                    </div>
                                    <div style=\"font-size:11px;color:#aaa;\">{$time_ago}</div>
                                    <div style=\"margin-top:6px;display:flex;gap:6px;\">
                                        <a href=\"{$dismiss_url}\" class=\"btn btn-xs btn-default\">
                                            Dismiss
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>";
                }
            }
        }
    }

    // ─────────────────────────────────────────────────────────────
    // APPROVE  (Admin only - set approved but do not delete yet)
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

        // Set status to approved, reset user_notified so requester sees it
        $this->db->where('id', $id)->update('item_delete_requests', [
            'status'        => 'approved',
            'reviewed_by'   => $reviewer_id,
            'user_notified' => 0
        ]);

        $this->session->set_flashdata('SUCCESSMSG', "Deletion request for item <strong>{$req['item_code']}</strong> has been approved. The user will be notified to perform actual deletion.");
        redirect('DeleteApprovalController/panel');
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

        $remarks = $this->input->get_post('remarks') ?: '';

        $this->db->where('id', $id)->update('item_delete_requests', [
            'status'         => 'rejected',
            'reviewed_by'    => $reviewer_id,
            'review_remarks' => $remarks,
            'user_notified'  => 0
        ]);

        $this->session->set_flashdata('INFOMSG', "Deletion request for item <strong>{$req['item_code']}</strong> was rejected.");
        redirect('DeleteApprovalController/panel');
    }

    // ─────────────────────────────────────────────────────────────
    // EXECUTE ACTUAL DELETION (Called by requester or Admin after approval)
    // ─────────────────────────────────────────────────────────────
    public function execute_delete($id)
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $user_id      = (int)($res['user_id'] ?? 0);
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $is_admin     = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        $req = $this->db->where('id', $id)->where('status', 'approved')->get('item_delete_requests')->row_array();
        if (empty($req)) {
            $this->session->set_flashdata('ERRORMSG', 'Approved request not found.');
            redirect('InventoryController/index');
            return;
        }

        // Must be original requester or Admin
        if ($req['requested_by'] !== $user_id && !$is_admin) {
            $this->session->set_flashdata('ERRORMSG', 'You are not authorized to perform deletion of this item.');
            redirect('InventoryController/index');
            return;
        }

        $deleted = false;
        if ($req['module'] === 'inventory') {
            try {
                $result = $this->inventory->delete_inventory_by_id($req['item_id']);
                $deleted = ($result === true);
                if ($result === 'CONSTRAIN_ERROR') {
                    $this->session->set_flashdata('ERRORMSG', "Cannot delete item <strong>{$req['item_code']}</strong>: it is referenced in other records.");
                    redirect($req['redirect_url'] ?: 'InventoryController/index');
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
                'status'        => 'deleted',
                'user_notified' => 1
            ]);
            $this->session->set_flashdata('SUCCESSMSG', "Item <strong>{$req['item_code']}</strong> has been permanently deleted.");
        } else {
            $this->session->set_flashdata('ERRORMSG', "Failed to delete item <strong>{$req['item_code']}</strong>.");
        }

        redirect($req['redirect_url'] ?: 'InventoryController/index');
    }

    // ─────────────────────────────────────────────────────────────
    // DISMISS NOTIFICATION (Acknowledge Admin's action)
    // ─────────────────────────────────────────────────────────────
    public function dismiss_notification($id)
    {
        $session_data = $this->session->userdata('session_data_head');
        $user_id      = (int)($session_data['result']['user_id'] ?? 0);

        $this->db
            ->where('id', $id)
            ->where('requested_by', $user_id)
            ->update('item_delete_requests', ['user_notified' => 1]);

        $req = $this->db->where('id', $id)->get('item_delete_requests')->row_array();
        $this->session->set_flashdata('INFOMSG', 'Notification dismissed.');
        redirect($req['redirect_url'] ?: 'InventoryController/index');
    }

    // ─────────────────────────────────────────────────────────────
    // FULL PANEL PAGE  (Admin & Non-Admin request management)
    // ─────────────────────────────────────────────────────────────
    public function panel()
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id      = (int)($res['user_id'] ?? 0);
        $is_admin     = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        if ($is_admin) {
            $data['pending']  = $this->db->where('status', 'pending')->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
            $data['history']  = $this->db->where_in('status', ['approved','rejected','deleted'])->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
        } else {
            $data['pending']  = $this->db->where('status', 'pending')->where('requested_by', $user_id)->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
            $data['history']  = $this->db->where_in('status', ['approved','rejected','deleted'])->where('requested_by', $user_id)->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
        }

        $data['is_admin'] = $is_admin;
        $data['user_id']  = $user_id;

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
