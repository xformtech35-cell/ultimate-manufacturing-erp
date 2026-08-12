<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InventoryApprovalController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Inventory', 'inventory');
        $this->load->model('Master', 'master');
        $this->_ensure_table_and_permissions();
    }

    /**
     * Self-healing DB table and sidebar permission setup
     */
    private function _ensure_table_and_permissions()
    {
        // 1. Create table if not exists
        if (!$this->db->table_exists('inventory_approval_requests')) {
            $table_name = $this->db->dbprefix('inventory_approval_requests');
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `$table_name` (
                    `id`                INT(11)      NOT NULL AUTO_INCREMENT,
                    `inventory_id`      INT(11)      NOT NULL,
                    `item_code`         VARCHAR(100) DEFAULT NULL,
                    `item_name`         VARCHAR(255) DEFAULT NULL,
                    `request_type`      ENUM('update','delete') NOT NULL DEFAULT 'update',
                    `requested_by`      INT(11)      NOT NULL,
                    `requested_by_name` VARCHAR(100) DEFAULT NULL,
                    `reason`            TEXT         DEFAULT NULL,
                    `old_data`          LONGTEXT     DEFAULT NULL,
                    `new_data`          LONGTEXT     DEFAULT NULL,
                    `status`            ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                    `reviewed_by`       INT(11)      DEFAULT NULL,
                    `reviewed_by_name`  VARCHAR(100) DEFAULT NULL,
                    `review_remarks`    VARCHAR(500) DEFAULT NULL,
                    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at`        DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // 2. Ensure user_notifications table exists
        if (!$this->db->table_exists('user_notifications')) {
            $table_notif = $this->db->dbprefix('user_notifications');
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `$table_notif` (
                    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
                    `user_id`    INT(11)      NOT NULL,
                    `title`      VARCHAR(255) NOT NULL,
                    `message`    TEXT         NOT NULL,
                    `type`       ENUM('success','info','warning','error') DEFAULT 'info',
                    `module`     VARCHAR(100) DEFAULT NULL,
                    `ref_id`     INT(11)      DEFAULT NULL,
                    `is_read`    TINYINT(1)   DEFAULT 0,
                    `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_user_read` (`user_id`, `is_read`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // 3. Ensure user_notified column exists in item_delete_requests if table present
        if ($this->db->table_exists('item_delete_requests')) {
            if (!$this->db->field_exists('user_notified', 'item_delete_requests')) {
                $table_del = $this->db->dbprefix('item_delete_requests');
                $this->db->query("ALTER TABLE `$table_del` ADD COLUMN `user_notified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `review_remarks`");
            }
        }

        // 4. Ensure sidebar menu item exists in DB table sidebar_menu
        $has_menu = $this->db->where('url', 'InventoryApprovalController/index')->get('sidebar_menu')->row();
        if (!$has_menu) {
            $this->db->insert('sidebar_menu', [
                'parent_id'   => 19, // Store / Inventory parent ID
                'title'       => 'Inventory Approval',
                'icon'        => 'fa fa-check-square-o',
                'url'         => 'InventoryApprovalController/index',
                'permission'  => 'Inventory_Approval',
                'sort_order'  => 25,
                'active_cond' => json_encode([
                    'controllers' => ['InventoryApprovalController'],
                    'pages'       => ['index']
                ])
            ]);
        }

        // 5. Auto-grant Inventory_Approval permission to Admin role (Role ID 1) if not set
        if ($this->db->table_exists('permission')) {
            $admin_perm = $this->db->where('role_id_fk', 1)->where('grp_perm', 'Inventory_Approval')->get('permission')->row();
            if (!$admin_perm) {
                $this->db->insert('permission', [
                    'role_id_fk' => 1,
                    'grp_perm'   => 'Inventory_Approval'
                ]);
            }
        }
    }

    /**
     * Check if user has permission to access Inventory Approvals
     */
    private function _check_access()
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id      = (int)($res['user_id'] ?? 0);

        if ($role_name === 'admin' || $role_id === 1 || $user_id === 1) {
            return true;
        }

        $permissions = $session_data['permission'] ?? [];
        if (in_array('Inventory_Approval', $permissions) || in_array('Store_Inventory', $permissions)) {
            return true;
        }

        $this->session->set_flashdata('ERRORMSG', 'You do not have permission to access Inventory Approvals.');
        redirect('Home/index');
        exit;
    }

    /**
     * Inventory Approvals Dashboard Panel
     */
    public function index()
    {
        $this->_check_access();

        // Sync legacy delete requests into inventory_approval_requests if any exist
        $this->_sync_delete_requests();

        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['role'] ?? 0);
        $user_id      = (int)($res['user_id'] ?? 0);
        $is_admin     = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        $data['is_admin'] = $is_admin;

        if ($is_admin) {
            $data['pending_requests']  = $this->db->order_by('id', 'DESC')->get_where('inventory_approval_requests', ['status' => 'pending'])->result_array();
            $data['approved_requests'] = $this->db->order_by('id', 'DESC')->get_where('inventory_approval_requests', ['status' => 'approved'])->result_array();
            $data['rejected_requests'] = $this->db->order_by('id', 'DESC')->get_where('inventory_approval_requests', ['status' => 'rejected'])->result_array();
        } else {
            $data['pending_requests']  = $this->db->order_by('id', 'DESC')->get_where('inventory_approval_requests', ['requested_by' => $user_id, 'status' => 'pending'])->result_array();
            $data['approved_requests'] = $this->db->order_by('id', 'DESC')->get_where('inventory_approval_requests', ['requested_by' => $user_id, 'status' => 'approved'])->result_array();
            $data['rejected_requests'] = $this->db->order_by('id', 'DESC')->get_where('inventory_approval_requests', ['requested_by' => $user_id, 'status' => 'rejected'])->result_array();
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('inventory_approval/index', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Sync legacy delete requests from item_delete_requests to inventory_approval_requests
    */
    private function _sync_delete_requests()
    {
        if ($this->db->table_exists('item_delete_requests')) {
            $del_reqs = $this->db->get_where('item_delete_requests', ['module' => 'inventory'])->result_array();
            foreach ($del_reqs as $req) {
                $exists = $this->db->get_where('inventory_approval_requests', [
                    'inventory_id' => $req['item_id'],
                    'request_type' => 'delete',
                    'created_at'   => $req['created_at']
                ])->row();

                if (!$exists) {
                    $this->db->insert('inventory_approval_requests', [
                        'inventory_id'      => $req['item_id'],
                        'item_code'         => $req['item_code'],
                        'item_name'         => $req['item_name'],
                        'request_type'      => 'delete',
                        'requested_by'      => $req['requested_by'],
                        'requested_by_name' => $req['requested_by_name'],
                        'reason'            => $req['reason'],
                        'status'            => $req['status'] === 'deleted' ? 'approved' : ($req['status'] === 'rejected' ? 'rejected' : 'pending'),
                        'reviewed_by'       => $req['reviewed_by'],
                        'review_remarks'    => $req['review_remarks'],
                        'created_at'        => $req['created_at']
                    ]);
                }
            }
        }
    }

    /**
     * Approve Inventory Request (Update or Delete)
     */
    public function approve($id)
    {
        $this->_check_access();

        $req = $this->db->get_where('inventory_approval_requests', ['id' => $id])->row_array();
        if (!$req) {
            $this->session->set_flashdata('ERRORMSG', 'Approval request not found.');
            redirect('InventoryApprovalController/index');
            return;
        }

        if ($req['status'] !== 'pending') {
            $this->session->set_flashdata('INFOMSG', 'This request has already been processed.');
            redirect('InventoryApprovalController/index');
            return;
        }

        $session_data = $this->session->userdata('session_data_head');
        $approver_id   = $session_data['result']['user_id']  ?? 0;
        $approver_name = $session_data['result']['username'] ?? 'Admin';

        if ($req['request_type'] === 'update') {
            // Process Inventory Update
            $new_data = json_decode($req['new_data'], true);
            if (empty($new_data) || !is_array($new_data)) {
                $this->session->set_flashdata('ERRORMSG', 'Invalid request data.');
                redirect('InventoryApprovalController/index');
                return;
            }

            // Perform inventory update
            $inventory_id = $req['inventory_id'];
            $result = $this->inventory->edit_inventory($new_data, $inventory_id, $approver_id);

            if ($result) {
                // Update request status
                $this->db->where('id', $id)->update('inventory_approval_requests', [
                    'status'           => 'approved',
                    'reviewed_by'      => $approver_id,
                    'reviewed_by_name' => $approver_name,
                    'review_remarks'   => $this->input->post('remarks') ?: 'Approved by ' . $approver_name,
                    'updated_at'       => date('Y-m-d H:i:s')
                ]);

                // Create user notification for requester
                if (!empty($req['requested_by'])) {
                    $this->db->insert('user_notifications', [
                        'user_id'    => $req['requested_by'],
                        'title'      => 'Inventory Update Approved',
                        'message'    => "Your update request for item '{$req['item_name']}' ({$req['item_code']}) has been approved by {$approver_name}.",
                        'type'       => 'success',
                        'module'     => 'inventory',
                        'ref_id'     => $req['inventory_id'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $this->session->set_flashdata('SUCCESSMSG', "Inventory update request for item '{$req['item_name']}' approved and applied successfully!");
            } else {
                $this->session->set_flashdata('ERRORMSG', "Failed to apply inventory updates.");
            }
        } elseif ($req['request_type'] === 'delete') {
            // Process Inventory Delete
            $inventory_id = $req['inventory_id'];
            $del_result   = $this->inventory->delete_inventory_by_id($inventory_id);

            if ($del_result === 'CONSTRAIN_ERROR') {
                $this->session->set_flashdata('ERRORMSG', "Cannot delete item '{$req['item_name']}' because it is referenced in active transaction records.");
            } else {
                $this->db->where('id', $id)->update('inventory_approval_requests', [
                    'status'           => 'approved',
                    'reviewed_by'      => $approver_id,
                    'reviewed_by_name' => $approver_name,
                    'review_remarks'   => $this->input->post('remarks') ?: 'Approved by ' . $approver_name,
                    'updated_at'       => date('Y-m-d H:i:s')
                ]);

                // Also update item_delete_requests table if present
                $this->db->where('item_id', $inventory_id)->where('module', 'inventory')->update('item_delete_requests', [
                    'status'      => 'deleted',
                    'reviewed_by' => $approver_id
                ]);

                // Create user notification for requester
                if (!empty($req['requested_by'])) {
                    $this->db->insert('user_notifications', [
                        'user_id'    => $req['requested_by'],
                        'title'      => 'Inventory Deletion Approved',
                        'message'    => "Your deletion request for item '{$req['item_name']}' ({$req['item_code']}) has been approved by {$approver_name}.",
                        'type'       => 'success',
                        'module'     => 'inventory',
                        'ref_id'     => $req['inventory_id'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $this->session->set_flashdata('SUCCESSMSG', "Item '{$req['item_name']}' deletion approved and completed successfully!");
            }
        }

        redirect('InventoryApprovalController/index');
    }

    /**
     * Reject Inventory Request
     */
    public function reject($id)
    {
        $this->_check_access();

        $req = $this->db->get_where('inventory_approval_requests', ['id' => $id])->row_array();
        if (!$req) {
            $this->session->set_flashdata('ERRORMSG', 'Approval request not found.');
            redirect('InventoryApprovalController/index');
            return;
        }

        $session_data = $this->session->userdata('session_data_head');
        $approver_id   = $session_data['result']['user_id']  ?? 0;
        $approver_name = $session_data['result']['username'] ?? 'Admin';
        $remarks       = $this->input->post('remarks') ?: $this->input->get('remarks') ?: 'Rejected by ' . $approver_name;

        $this->db->where('id', $id)->update('inventory_approval_requests', [
            'status'           => 'rejected',
            'reviewed_by'      => $approver_id,
            'reviewed_by_name' => $approver_name,
            'review_remarks'   => $remarks,
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        // Also update item_delete_requests if applicable
        if ($req['request_type'] === 'delete') {
            $this->db->where('item_id', $req['inventory_id'])->where('module', 'inventory')->update('item_delete_requests', [
                'status'         => 'rejected',
                'reviewed_by'    => $approver_id,
                'review_remarks' => $remarks
            ]);
        }

        // Create user notification for requester
        if (!empty($req['requested_by'])) {
            $this->db->insert('user_notifications', [
                'user_id'    => $req['requested_by'],
                'title'      => 'Inventory Request Rejected',
                'message'    => "Your {$req['request_type']} request for item '{$req['item_name']}' ({$req['item_code']}) was rejected. Remarks: {$remarks}",
                'type'       => 'error',
                'module'     => 'inventory',
                'ref_id'     => $req['inventory_id'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->session->set_flashdata('INFOMSG', "Inventory request for item '{$req['item_name']}' has been rejected.");
        redirect('InventoryApprovalController/index');
    }

    /**
     * AJAX: Combined pending count (inventory updates + item deletions) for the bell badge
     */
    public function get_pending_count_ajax()
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['role'] ?? 0);
        $user_id      = (int)($res['user_id'] ?? 0);
        $is_admin     = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        $count = 0;
        if ($is_admin) {
            // Pending inventory update/delete requests
            if ($this->db->table_exists('inventory_approval_requests')) {
                $count += $this->db->where('status', 'pending')->count_all_results('inventory_approval_requests');
            }
            // Pending item deletion requests (legacy)
            if ($this->db->table_exists('item_delete_requests')) {
                $count += $this->db->where('status', 'pending')->count_all_results('item_delete_requests');
            }
        } else {
            // Non-admin: count their own unreviewed requests
            if ($this->db->table_exists('inventory_approval_requests')) {
                $count += $this->db->where('requested_by', $user_id)->where('status', 'pending')->count_all_results('inventory_approval_requests');
            }
            if ($this->db->table_exists('item_delete_requests')) {
                $this->db->where('requested_by', $user_id);
                if ($this->db->field_exists('user_notified', 'item_delete_requests')) {
                    $this->db->where('user_notified', 0);
                } else {
                    $this->db->where('status', 'pending');
                }
                $count += $this->db->count_all_results('item_delete_requests');
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['count' => (int)$count]);
    }

    /**
     * AJAX: Combined notification HTML for the bell dropdown
     */
    public function get_pending_html_ajax()
    {
        $session_data = $this->session->userdata('session_data_head');
        $res          = $session_data['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['role'] ?? 0);
        $user_id      = (int)($res['user_id'] ?? 0);
        $is_admin     = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        $html = '';

        if ($is_admin) {
            // Pending inventory update requests
            $inv_reqs = [];
            if ($this->db->table_exists('inventory_approval_requests')) {
                $inv_reqs = $this->db->where('status', 'pending')->order_by('created_at', 'DESC')->limit(8)->get('inventory_approval_requests')->result_array();
            }
            foreach ($inv_reqs as $req) {
                $type_icon  = $req['request_type'] === 'delete' ? 'fa-trash' : 'fa-pencil-square';
                $type_label = $req['request_type'] === 'delete' ? 'Delete Request' : 'Edit Request';
                $type_color = $req['request_type'] === 'delete' ? '#d9534f' : '#3c8dbc';
                $approve_url = base_url('InventoryApprovalController/approve/' . $req['id']);
                $reject_url  = base_url('InventoryApprovalController/reject/'  . $req['id']);
                $time_ago    = $this->_time_ago_inv($req['created_at']);
                $html .= "
                <li style=\"border-bottom:1px solid #f0f0f0;\">
                    <div style=\"white-space:normal;padding:8px 12px;\">
                        <div style=\"display:flex;align-items:flex-start;gap:8px;\">
                            <span style=\"background:{$type_color};color:#fff;border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;flex-shrink:0;\">
                                <i class=\"fa {$type_icon}\"></i>
                            </span>
                            <div style=\"flex:1;\">
                                <strong style=\"color:{$type_color};font-size:12px;\">{$type_label} (Pending)</strong>
                                <div style=\"font-size:12px;color:#333;\"><strong>" . htmlspecialchars($req['item_code']) . "</strong> — " . htmlspecialchars($req['item_name']) . "</div>
                                <div style=\"font-size:11px;color:#777;\">By: " . htmlspecialchars($req['requested_by_name']) . " &bull; {$time_ago}</div>
                                " . (!empty($req['reason']) ? '<div style="font-size:11px;color:#888;font-style:italic;">Reason: ' . htmlspecialchars(substr($req['reason'], 0, 55)) . '</div>' : '') . "
                                <div style=\"margin-top:5px;display:flex;gap:5px;\">
                                    <a href=\"{$approve_url}\" class=\"btn btn-xs btn-success\" onclick=\"return confirm('Approve this request?');\"><i class=\"fa fa-check\"></i> Approve</a>
                                    <a href=\"javascript:void(0);\" class=\"btn btn-xs btn-danger\" onclick=\"var r=prompt('Rejection remarks:');if(r!==null)window.location.href='{$reject_url}?remarks='+encodeURIComponent(r);\"><i class=\"fa fa-times\"></i> Reject</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>";
            }

            // Pending item delete requests (legacy)
            $del_reqs = [];
            if ($this->db->table_exists('item_delete_requests')) {
                $del_reqs = $this->db->where('status', 'pending')->order_by('created_at', 'DESC')->limit(5)->get('item_delete_requests')->result_array();
            }
            foreach ($del_reqs as $req) {
                $approve_url = base_url('DeleteApprovalController/approve/' . $req['id']);
                $reject_url  = base_url('DeleteApprovalController/reject/'  . $req['id']);
                $time_ago    = $this->_time_ago_inv($req['created_at']);
                $html .= "
                <li style=\"border-bottom:1px solid #f0f0f0;\">
                    <div style=\"white-space:normal;padding:8px 12px;\">
                        <div style=\"display:flex;align-items:flex-start;gap:8px;\">
                            <span style=\"background:#f39c12;color:#fff;border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;flex-shrink:0;\">
                                <i class=\"fa fa-trash\"></i>
                            </span>
                            <div style=\"flex:1;\">
                                <strong style=\"color:#f39c12;font-size:12px;\">Delete Request (Pending)</strong>
                                <div style=\"font-size:12px;color:#333;\"><strong>" . htmlspecialchars($req['item_code']) . "</strong></div>
                                <div style=\"font-size:11px;color:#777;\">By: " . htmlspecialchars($req['requested_by_name']) . " &bull; {$time_ago}</div>
                                <div style=\"margin-top:5px;display:flex;gap:5px;\">
                                    <a href=\"{$approve_url}\" class=\"btn btn-xs btn-success\" onclick=\"return confirm('Approve deletion?');\"><i class=\"fa fa-check\"></i> Approve</a>
                                    <a href=\"javascript:void(0);\" class=\"btn btn-xs btn-danger\" onclick=\"var r=prompt('Rejection remarks:');if(r!==null)window.location.href='{$reject_url}?remarks='+encodeURIComponent(r);\"><i class=\"fa fa-times\"></i> Reject</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>";
            }

            if (empty($inv_reqs) && empty($del_reqs)) {
                $html = '<li><a href="#" style="text-align:center;color:#999;padding:15px 0;display:block;">No pending approval requests</a></li>';
            }
        } else {
            // Non-admin: show their own pending/reviewed requests
            $own_reqs = $this->db->table_exists('inventory_approval_requests')
                ? $this->db->where('requested_by', $user_id)->order_by('created_at', 'DESC')->limit(8)->get('inventory_approval_requests')->result_array()
                : [];

            foreach ($own_reqs as $req) {
                $time_ago = $this->_time_ago_inv($req['updated_at'] ?: $req['created_at']);
                if ($req['status'] === 'pending') {
                    $html .= "
                    <li style=\"border-bottom:1px solid #f0f0f0;\">
                        <div style=\"white-space:normal;padding:8px 12px;\">
                            <span style=\"background:#f39c12;color:#fff;border-radius:50%;width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;margin-right:8px;vertical-align:middle;\"><i class=\"fa fa-clock-o\"></i></span>
                            <strong style=\"color:#f39c12;font-size:12px;\">" . ($req['request_type'] === 'delete' ? 'Delete' : 'Edit') . " Request Pending</strong>
                            <div style=\"font-size:11px;color:#333;margin-left:36px;\"><strong>" . htmlspecialchars($req['item_code']) . "</strong> — Awaiting Admin Approval</div>
                            <div style=\"font-size:10px;color:#aaa;margin-left:36px;\">{$time_ago}</div>
                        </div>
                    </li>";
                } elseif ($req['status'] === 'approved') {
                    $html .= "
                    <li style=\"border-bottom:1px solid #f0f0f0;\">
                        <div style=\"white-space:normal;padding:8px 12px;\">
                            <span style=\"background:#27ae60;color:#fff;border-radius:50%;width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;margin-right:8px;vertical-align:middle;\"><i class=\"fa fa-check\"></i></span>
                            <strong style=\"color:#27ae60;font-size:12px;\">" . ($req['request_type'] === 'delete' ? 'Delete' : 'Edit') . " Request Approved</strong>
                            <div style=\"font-size:11px;color:#333;margin-left:36px;\"><strong>" . htmlspecialchars($req['item_code']) . "</strong> — Applied by Admin</div>
                            <div style=\"font-size:10px;color:#aaa;margin-left:36px;\">{$time_ago}</div>
                        </div>
                    </li>";
                } else {
                    $html .= "
                    <li style=\"border-bottom:1px solid #f0f0f0;\">
                        <div style=\"white-space:normal;padding:8px 12px;\">
                            <span style=\"background:#d9534f;color:#fff;border-radius:50%;width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;margin-right:8px;vertical-align:middle;\"><i class=\"fa fa-times\"></i></span>
                            <strong style=\"color:#d9534f;font-size:12px;\">Request Rejected</strong>
                            <div style=\"font-size:11px;color:#333;margin-left:36px;\"><strong>" . htmlspecialchars($req['item_code']) . "</strong>" . (!empty($req['review_remarks']) ? ' — ' . htmlspecialchars($req['review_remarks']) : '') . "</div>
                            <div style=\"font-size:10px;color:#aaa;margin-left:36px;\">{$time_ago}</div>
                        </div>
                    </li>";
                }
            }
            if (empty($own_reqs)) {
                $html = '<li><a href="#" style="text-align:center;color:#999;padding:15px 0;display:block;">No approval requests</a></li>';
            }
        }

        echo $html;
    }

    public function mark_notification_read($id)
    {
        $session_data = $this->session->userdata('session_data_head');
        $user_id      = (int)($session_data['result']['user_id'] ?? 0);

        if ($user_id > 0 && $this->db->table_exists('user_notifications')) {
            $this->db->where('id', (int)$id)->where('user_id', $user_id)->update('user_notifications', ['is_read' => 1]);
        }
        echo json_encode(['status' => 'success']);
    }

    private function _time_ago_inv($datetime)
    {
        $now  = new DateTime();
        $ago  = new DateTime($datetime);
        $diff = $now->diff($ago);
        if ($diff->d > 0)  return $diff->d . 'd ago';
        if ($diff->h > 0)  return $diff->h . 'h ago';
        if ($diff->i > 0)  return $diff->i . 'm ago';
        return 'Just now';
    }
}
