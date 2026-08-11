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

        // 2. Ensure sidebar menu item exists in DB table sidebar_menu
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

        // 3. Auto-grant Inventory_Approval permission to Admin role (Role ID 1) if not set
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

        $this->session->set_flashdata('INFOMSG', "Inventory request for item '{$req['item_name']}' has been rejected.");
        redirect('InventoryApprovalController/index');
    }
}
