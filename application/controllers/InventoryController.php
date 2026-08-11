<?php

defined('BASEPATH') or exit('No direct script access allowed');

class InventoryController extends MY_Controller
{

    protected $user_id;

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('login', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('gst', '', TRUE);
        $this->load->library('form_validation');
        $this->load->library('image_lib');
        $this->load->model('expense', '', TRUE);
        $this->load->model('units', '', TRUE);
        $this->load->model('ItemCategory', '', TRUE);
        $this->load->model('ItemGroup', '', TRUE);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
 
        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    private function normalize_expense_mode($mode)
    {
        $mode = strtolower(trim((string) $mode));
        if ($mode === 'direct') {
            return 'direct';
        }
        if ($mode === 'indirect') {
            return 'indirect';
        }
        return '';
    }

    private function ensure_expense_columns()
    {
        $columns = array(
            'expense_type'  => "ALTER TABLE `{$this->db->dbprefix}expense` ADD COLUMN `expense_type` VARCHAR(50) NOT NULL DEFAULT '' AFTER `expense_category`",
            'expense_month' => "ALTER TABLE `{$this->db->dbprefix}expense` ADD COLUMN `expense_month` VARCHAR(20) NOT NULL DEFAULT '' AFTER `expense_type`",
            'basic_amount'  => "ALTER TABLE `{$this->db->dbprefix}expense` ADD COLUMN `basic_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `expense_amount`",
            // Needed for Direct Expense form
            'bank_voucher_type' => "ALTER TABLE `{$this->db->dbprefix}expense` ADD COLUMN `bank_voucher_type` VARCHAR(50) NOT NULL DEFAULT '' AFTER `expense_category`",
        );
        $existing = $this->db->list_fields('expense');
        foreach ($columns as $col => $sql) {
            if (!in_array($col, $existing)) {
                $this->db->query($sql);
            }
        }
    }

    private function get_expense_mode_prefix($mode)
    {
        if ($mode === 'direct') {
            return 'Direct - ';
        }
        if ($mode === 'indirect') {
            return 'Indirect - ';
        }
        return '';
    }

    private function normalize_expense_type($type)
    {
        $type = strtolower(trim((string) $type));
        if ($type === 'individual') {
            return 'individual';
        }
        if ($type === 'corporate') {
            return 'corporate';
        }
        return '';
    }

    private function build_master_expense_category($category, $mode, $expense_type = '')
    {
        $category = trim((string) $category);
        if ($category === '') {
            return '';
        }

        $direct_prefix = 'Direct - ';
        $indirect_prefix = 'Indirect - ';
        if (stripos($category, $direct_prefix) === 0) {
            $category = trim(substr($category, strlen($direct_prefix)));
        }
        if (stripos($category, $indirect_prefix) === 0) {
            $category = trim(substr($category, strlen($indirect_prefix)));
            if (preg_match('/^(Individual|Corporate)\s*-\s*(.*)$/i', $category, $matches)) {
                $category = trim($matches[2]);
            }
        }

        if ($mode === 'direct') {
            return $direct_prefix . $category;
        }

        if ($mode === 'indirect') {
            $expense_type = $this->normalize_expense_type($expense_type);
            if ($expense_type === '') {
                return '';
            }
            $type_label = ucfirst($expense_type);
            return $indirect_prefix . $type_label . ' - ' . $category;
        }

        return $category;
    }

    private function parse_master_expense_category($stored_category, $mode)
    {
        $stored_category = trim((string) $stored_category);
        $name = $stored_category;
        $expense_type = '';

        if ($mode === 'direct') {
            $prefix = 'Direct - ';
            if (stripos($stored_category, $prefix) === 0) {
                $name = trim(substr($stored_category, strlen($prefix)));
            }
        }

        if ($mode === 'indirect') {
            $prefix = 'Indirect - ';
            if (stripos($stored_category, $prefix) === 0) {
                $name = trim(substr($stored_category, strlen($prefix)));
            }

            if (preg_match('/^(Individual|Corporate)\s*-\s*(.*)$/i', $name, $matches)) {
                $expense_type = strtolower($matches[1]);
                $name = trim($matches[2]);
            }
        }

        return array(
            'name' => $name,
            'expense_type' => $expense_type,
        );
    }

    private function get_expense_type_label_from_record($record)
    {
        $expense_type = '';
        $expense_category = '';

        if (is_array($record)) {
            $expense_type = isset($record['expense_type']) ? trim((string) $record['expense_type']) : '';
            $expense_category = isset($record['expense_category']) ? trim((string) $record['expense_category']) : '';
        } else {
            $expense_type = isset($record->expense_type) ? trim((string) $record->expense_type) : '';
            $expense_category = isset($record->expense_category) ? trim((string) $record->expense_category) : '';
        }

        $normalized_type = $this->normalize_expense_type($expense_type);
        if ($normalized_type !== '') {
            return ucfirst($normalized_type);
        }

        $parsed = $this->parse_master_expense_category($expense_category, 'indirect');
        return $parsed['expense_type'] !== '' ? ucfirst($parsed['expense_type']) : '';
    }

    private function apply_expense_mode_prefix($category, $mode)
    {
        $category = trim((string) $category);
        if ($category === '') {
            return '';
        }
        $prefix = $this->get_expense_mode_prefix($mode);

        if ($prefix === '') {
            return $category;
        }

        if (stripos($category, $prefix) === 0) {
            return $category;
        }

        return $prefix . $category;
    }

    private function filter_expense_categories_by_mode($categories, $mode)
    {
        $prefix = $this->get_expense_mode_prefix($mode);
        if ($prefix === '') {
            return $categories;
        }

        $filtered = array();
        foreach ((array) $categories as $row) {
            if (isset($row->exp_cat) && stripos($row->exp_cat, $prefix) === 0) {
                $filtered[] = $row;
            }
        }

        return $filtered;
    }

    private function filter_expense_entries_by_mode($entries, $mode)
    {
        $prefix = $this->get_expense_mode_prefix($mode);
        if ($prefix === '') {
            return $entries;
        }

        $filtered = array();
        foreach ((array) $entries as $row) {
            if (isset($row->expense_category) && stripos($row->expense_category, $prefix) === 0) {
                $filtered[] = $row;
            }
        }

        return $filtered;
    }

    public function index()
    {
        $data['result'] = $this->inventory->get_inventory($this->user_id);
        $data['gst_result'] = $this->inventory->get_gst($this->user_id);
        $data['gst_class'] = $this->inventory->get_gst_class($this->user_id);
        $data['unit_result']  = $this->units->get_unit_name($this->user_id);
        $data['category_result'] = $this->ItemCategory->get_categories($this->user_id);
        $data['group_result'] = $this->ItemGroup->get_groups($this->user_id);
        $data['inventory_id'] = $this->inventory->get_last_inventory_number($this->user_id);

        $this->load->model('supplier', '', TRUE);
        $data['supplier_result'] = $this->supplier->get_supplier_name();

        // Fetch deletion requests for tabs
        $session_data_head = $this->session->userdata('session_data_head');
        $res = $session_data_head['result'] ?? [];
        $role_name = strtolower($res['role_name'] ?? '');
        $role_id   = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id   = (int)($res['user_id'] ?? 0);
        $is_admin  = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        if ($is_admin) {
            $data['approved_deletions'] = $this->db->where('status', 'approved')->where('module', 'inventory')->order_by('updated_at', 'DESC')->get('item_delete_requests')->result_array();
            $data['deletion_history']   = $this->db->where_in('status', ['pending', 'rejected', 'deleted'])->where('module', 'inventory')->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
        } else {
            $data['approved_deletions'] = $this->db->where('status', 'approved')->where('module', 'inventory')->where('requested_by', $user_id)->order_by('updated_at', 'DESC')->get('item_delete_requests')->result_array();
            $data['deletion_history']   = $this->db->where_in('status', ['pending', 'approved', 'rejected', 'deleted'])->where('module', 'inventory')->where('requested_by', $user_id)->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
        }

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('inventory/add_inventory', $data);
    }

public function add_expense_data_direct()
{
    $expense_mode = 'direct';
    $str = $this->input->get('str');
    
    if ($str == "All") {
        $data['expense_result'] = $this->inventory->get_expense_data($this->user_id);
    } else {
        // Get current month in the format that matches your database (e.g., "Mar-25" or "March-2025")
        $current_month = date('M-y'); // This gives format like "Mar-25"
        $data['expense_result'] = $this->inventory->get_monthyearwise_record($current_month, $this->user_id);
        
        // Debug - remove after testing
        // echo "Current month: " . $current_month;
        // print_r($data['expense_result']);
    }
    
    $data['gst_class_result'] = $this->gst->get_gst_classes($this->user_id);
    $data['expense_catgory'] = $this->filter_expense_categories_by_mode($this->expense->get_expense_catgory($this->user_id), $expense_mode);
    $data['expense_result'] = $this->filter_expense_entries_by_mode($data['expense_result'], $expense_mode);
    $data['expense_mode'] = $expense_mode;
    
    // Get direct individuals for dropdown
    $data['direct_individuals'] = $this->expense->get_direct_individuals($this->user_id);
    
    $session_data_head = $this->session->userdata('session_data_head');
    $this->load->view('admin/header_side_bar', $session_data_head);
    $this->load->view('expense/add_expense_direct', $data);
}

public function add_expense_data_indirect()
{
    $expense_mode = 'indirect';
    $str = $this->input->get('str');
    
    if ($str == "All") {
        $data['expense_result'] = $this->inventory->get_expense_data($this->user_id);
    } else {
        // Show current month data by default
        $current_month = date('M-y');
        $data['expense_result'] = $this->inventory->get_monthyearwise_record($current_month, $this->user_id);
    }
    
    $data['gst_class_result'] = $this->gst->get_gst_classes($this->user_id);
    $data['expense_catgory'] = $this->filter_expense_categories_by_mode($this->expense->get_expense_catgory($this->user_id), $expense_mode);
    $data['expense_result'] = $this->filter_expense_entries_by_mode($data['expense_result'], $expense_mode);
    $data['expense_mode'] = $expense_mode;
    
    // Get individuals for dropdown
    $data['individuals'] = $this->expense->get_indirect_individuals($this->user_id);
    
    $session_data_head = $this->session->userdata('session_data_head');
    $this->load->view('admin/header_side_bar', $session_data_head);
    $this->load->view('expense/add_expense_indirect', $data);
}

    public function add_inventory()
    {
        // Get the item_name from the form
        $item_name = $this->input->post('item_name');
        
        // If item_name is empty, use the code as item_name (fallback)
        if (empty($item_name) || $item_name == '') {
            $item_name = $this->input->post('code');
        }
        
        $prod_description = $this->input->post('prod_description');
        $code             = trim($this->input->post('code'));
        $hsn              = $this->input->post('hsn');
        $unit             = $this->input->post('unit');
        $item_type        = $this->input->post('item_type');
        $gst_per          = $this->input->post('gst_per');
        $stock            = $this->input->post('stock') ?: 0;
        $cost_price       = $this->input->post('cost_price') ?: 0;
        $sell_price       = $this->input->post('sell_price') ?: 0;
        $packing          = $this->input->post('packing') ?: '';
        $category_id      = $this->input->post('category_id');
        $group_id         = $this->input->post('group_id') ?: null;
        $company_name     = trim((string) $this->input->post('company_name'));
        
        // VALIDATION: Check if required fields are provided
        $errors = array();
        
        if (empty($code)) {
            $errors[] = "Item code is required";
        }
        
        if (empty($hsn)) {
            $errors[] = "HSN code is required";
        }
        
        if (empty($unit)) {
            $errors[] = "Unit is required";
        }
        
        if (empty($gst_per)) {
            $errors[] = "GST percentage is required";
        }
        
        // If there are validation errors, show them and redirect back
        if (!empty($errors)) {
            $error_message = implode(", ", $errors);
            $this->session->set_flashdata('INFOMSG', $error_message);
            redirect('InventoryController/index');
            return;
        }
        
        // Handle category_id - convert empty to NULL
        if (empty($category_id) || $category_id == '' || $category_id == 0) {
            $category_id = null;
        } else {
            // VALIDATE CATEGORY EXISTS
            $category_exists = $this->db
                ->where('category_id', $category_id)
                ->get('item_category_master')
                ->num_rows();
                
            if ($category_exists == 0) {
                $this->session->set_flashdata('INFOMSG', "Invalid category selected! Category does not exist. Please select a valid category or leave it empty.");
                redirect('InventoryController/index');
                return;
            }
        }
        
        // Handle group_id - convert empty to NULL
        if (empty($group_id) || $group_id == '' || $group_id == 0) {
            $group_id = null;
        } else {
            // VALIDATE GROUP EXISTS
            $group_exists = $this->db
                ->where('group_id', $group_id)
                ->get('item_group_master')
                ->num_rows();
                
            if ($group_exists == 0) {
                $this->session->set_flashdata('INFOMSG', "Invalid group selected! Group does not exist. Please select a valid group or leave it empty.");
                redirect('InventoryController/index');
                return;
            }
        }
        
        $date_added    = date("Y-m-d");
        $date_updated  = date("d-m-Y"); 
        
        $data_inventory = array(
            'item_name'       => $item_name,
            'prod_description' => $prod_description,
            'code'            => $code,
            'hsn'             => $hsn,
            'unit'            => $unit,
            'item_type'       => $item_type,
            'gst_per'         => $gst_per,
            'stock'           => $stock,
            'available_stock' => $stock,
            'allocated_stock' => 0,
            'cost_price'      => $cost_price,
            'sell_price'      => $sell_price,
            'packing'         => $packing,
            'category_id'     => $category_id,
            'group_id'        => $group_id,
            'company_name'    => $company_name,
            'date_added'      => $date_added,
            'date_modified'   => $date_updated,
            'uid'             => $this->user_id
        );
        
        // Check if inventory code already exists
        $result = $this->inventory->inventory_code_check($code, $this->user_id);
        
        if ($result == FALSE) {
            $insert_result = $this->inventory->add_inventory($data_inventory);
            
            if ($insert_result) {
                $this->session->set_flashdata('SUCCESSMSG', "Inventory added successfully!!");
            } else {
                $this->session->set_flashdata('INFOMSG', "Failed to add inventory. Database error!");
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Inventory already exist!!");
        }
        
        redirect('InventoryController/index');
    }

    public function edit_inventory()
    {
        $inventory_id     = $this->input->post('inventory_id');
        $item_name        = $this->input->post('item_name');
        $prod_description = $this->input->post('prod_description');
        $code             = trim($this->input->post('code'));

        // Check if code is already used by another inventory item (excluding current)
        $existing_code_item = $this->db
            ->where('code', $code)
            ->where('inventory_id !=', $inventory_id)
            ->get('inventory')
            ->row();

        if ($existing_code_item) {
            $this->session->set_flashdata('INFOMSG', "Inventory code already exists for another item: " . $existing_code_item->item_name);
            redirect('InventoryController/index');
            return;
        }
        $hsn              = $this->input->post('hsn');
        $unit             = $this->input->post('unit');
        $item_type        = $this->input->post('item_type');
        $gst_per          = $this->input->post('gst_per');
        $stock            = (float) $this->input->post('stock');
        $cost_price       = $this->input->post('cost_price');
        $sell_price       = $this->input->post('sell_price');
        $category_id      = $this->input->post('category_id');
        $group_id         = $this->input->post('group_id') ?: null;

        // ── Read old stock BEFORE update (for informative success message) ───
        $old_item = $this->db
            ->select('stock, item_name')
            ->where('inventory_id', $inventory_id)
            ->get('inventory')
            ->row();
        $old_stock = $old_item ? (float) $old_item->stock : 0;

        // Handle category_id - convert empty to NULL
        if (empty($category_id) || $category_id == '' || $category_id == 0) {
            $category_id = null;
        } else {
            // Validate category exists
            $category_exists = $this->db
                ->where('category_id', $category_id)
                ->get('item_category_master')
                ->num_rows();
                
            if ($category_exists == 0) {
                $this->session->set_flashdata('INFOMSG', "Invalid category selected!");
                redirect('InventoryController/index');
                return;
            }
        }
        
        // Handle group_id - convert empty to NULL
        if (empty($group_id) || $group_id == '' || $group_id == 0) {
            $group_id = null;
        } else {
            // Validate group exists
            $group_exists = $this->db
                ->where('group_id', $group_id)
                ->get('item_group_master')
                ->num_rows();
                
            if ($group_exists == 0) {
                $this->session->set_flashdata('INFOMSG', "Invalid group selected!");
                redirect('InventoryController/index');
                return;
            }
        }
        
        $date_updated = date("d-m-Y");
        
        $data_inventory = array(
            'item_name'        => $item_name,
            'prod_description' => $prod_description,
            'item_type'        => $item_type,
            'code'             => $code,
            'hsn'              => $hsn,
            'unit'             => $unit,
            'gst_per'          => $gst_per,
            'stock'            => $stock,
            'cost_price'       => $cost_price,
            'sell_price'       => $sell_price,
            'category_id'      => $category_id,
            'group_id'         => $group_id,
            'date_modified'    => $date_updated
        );
        
        $session_data_head = $this->session->userdata('session_data_head');
        $res          = $session_data_head['result'] ?? [];
        $role_name    = strtolower($res['role_name'] ?? '');
        $role_id      = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id      = (int)($res['user_id'] ?? 0);
        $user_name    = $res['username'] ?? 'User #' . $user_id;

        $is_admin = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        if (!$is_admin) {
            // Non-admin user: Submit Inventory Update Request for Admin Approval
            $full_old_item = $this->inventory->get_inventory_by_id($inventory_id);

            // Ensure table exists
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

            // Check if pending update request already exists for this item
            $existing_req = $this->db
                ->where('inventory_id', $inventory_id)
                ->where('request_type', 'update')
                ->where('status', 'pending')
                ->get('inventory_approval_requests')
                ->row();

            if ($existing_req) {
                $this->db->where('id', $existing_req->id)->update('inventory_approval_requests', [
                    'new_data'          => json_encode($data_inventory),
                    'reason'            => $this->input->post('reason') ?: 'Inventory update request',
                    'requested_by'      => $user_id,
                    'requested_by_name' => $user_name,
                    'created_at'        => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->db->insert('inventory_approval_requests', [
                    'inventory_id'      => $inventory_id,
                    'item_code'         => $code,
                    'item_name'         => $item_name,
                    'request_type'      => 'update',
                    'requested_by'      => $user_id,
                    'requested_by_name' => $user_name,
                    'reason'            => $this->input->post('reason') ?: 'Inventory update request',
                    'old_data'          => json_encode($full_old_item),
                    'new_data'          => json_encode($data_inventory),
                    'status'            => 'pending'
                ]);
            }

            $this->session->set_flashdata('INFOMSG', "Inventory update request submitted for '{$item_name}' ({$code})! Pending Admin Approval.");
            redirect('InventoryController/index');
            return;
        }

        $result = $this->inventory->edit_inventory($data_inventory, $inventory_id, $this->user_id);
        
        if ($result) {
            if ($stock != $old_stock) {
                $diff    = $stock - $old_stock;
                $arrow   = $diff > 0 ? '+' . $diff : $diff;
                $this->session->set_flashdata(
                    'SUCCESSMSG',
                    "Item updated successfully! Stock adjusted: {$old_stock} → {$stock} ({$arrow} units). " .
                    "All modules (Inventory History, Stock Summary, Stock Verification) are now updated."
                );
            } else {
                $this->session->set_flashdata('SUCCESSMSG', "Inventory updated successfully!");
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Inventory could not be updated. Please try again.");
        }
        
        redirect('InventoryController/index');
    }

    public function get_expense_by_id()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->get('expense_mode'));
        $id = $this->uri->segment(3);
        $data['exp_result_by_id'] = $this->inventory->get_expense_by_id($id);
        $data['expense_result'] = $this->filter_expense_entries_by_mode($this->inventory->get_expense_data($this->user_id), $expense_mode);
        $data['expense_catgory'] = $this->filter_expense_categories_by_mode($this->expense->get_expense_catgory($this->user_id), $expense_mode);
        $data['gst_class_result'] = $this->gst->get_gst_classes($this->user_id);
        $data['expense_mode'] = $expense_mode;
        
        // Get individuals based on expense mode
        if ($expense_mode == 'direct') {
            $data['direct_individuals'] = $this->expense->get_direct_individuals($this->user_id);
        } else {
            $data['individuals'] = $this->expense->get_indirect_individuals($this->user_id);
        }
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('expense/edit_expense', $data);
    }

    public function get_inventory_by_id()
    {
        $id = $this->uri->segment(3);
        $data['inventory'] = $this->inventory->get_inventory_by_id($id);
        $data['gst_class'] = $this->inventory->get_gst_class($this->user_id);
        $data['unit_result']  = $this->units->get_unit_name($this->user_id);
        $data['category_result'] = $this->ItemCategory->get_categories($this->user_id);
        $data['group_result'] = $this->ItemGroup->get_groups($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('inventory/edit_inventory', $data);
    }

    public function delete_inventory_by_id()
    {
        $id = $this->uri->segment(3);
        $session_data_head = $this->session->userdata('session_data_head');
        $res = $session_data_head['result'] ?? [];
        $role_name = strtolower($res['role_name'] ?? '');
        $role_id   = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id   = (int)($res['user_id'] ?? 0);

        if ($role_name !== 'admin' && $role_id !== 1 && $user_id !== 1) {
            $reason = $this->input->get('reason') ?: 'Item deletion request';
            $item   = $this->inventory->get_inventory_by_id($id);
            $user_name = $res['username'] ?? 'User #' . $user_id;

            // Ensure table exists
            if ($this->db->table_exists('inventory_approval_requests')) {
                $existing_del = $this->db
                    ->where('inventory_id', $id)
                    ->where('request_type', 'delete')
                    ->where('status', 'pending')
                    ->get('inventory_approval_requests')
                    ->row();

                if (!$existing_del) {
                    $this->db->insert('inventory_approval_requests', [
                        'inventory_id'      => $id,
                        'item_code'         => $item['code'] ?? '',
                        'item_name'         => $item['item_name'] ?? '',
                        'request_type'      => 'delete',
                        'requested_by'      => $user_id,
                        'requested_by_name' => $user_name,
                        'reason'            => $reason,
                        'status'            => 'pending'
                    ]);
                }
            }
            
            // Dynamically set redirect URL based on referrer
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            $redirect_url = 'InventoryController/index';
            if (strpos($referer, 'inventory_history') !== false) {
                $redirect_url = 'InventoryController/inventory_history';
            }
            
            redirect('DeleteApprovalController/request_delete?item_id=' . urlencode($id) . '&module=inventory&reason=' . urlencode($reason) . '&redirect_url=' . urlencode($redirect_url));
            return;
        }

        try {
            $result = $this->inventory->delete_inventory_by_id($id);
            if ($result === 'CONSTRAIN_ERROR') {
                $this->session->set_flashdata('ERRORMSG', "Cannot delete this inventory item because it is referenced in material issues or other modules.");
                redirect('InventoryController/index');
            } elseif ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Inventory deleted successfully!!");
                redirect('InventoryController/index');
            } else {
                $this->session->set_flashdata('INFOMSG', "Inventory not deleted successfully!!");
                redirect('InventoryController/index');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('ERRORMSG', "Cannot delete this inventory item because it is referenced in material issues or other modules.");
            redirect('InventoryController/index');
        } catch (Throwable $t) {
            $this->session->set_flashdata('ERRORMSG', "Cannot delete this inventory item because it is referenced in material issues or other modules.");
            redirect('InventoryController/index');
        }
    }

    public function delete_expense_by_id()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->get('expense_mode'));
        $id = $this->uri->segment(3);
        $result = $this->inventory->delete_expense_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Expense amount deleted successfully!!");
            redirect('InventoryController/add_expense_data?expense_mode=' . $expense_mode);
        } else {
            $this->session->set_flashdata('INFOMSG', "Expense amount not deleted successfully!!");
            redirect('InventoryController/add_expense_data?expense_mode=' . $expense_mode);
        }
    }

    public function add_expense()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->post('expense_mode'));
        $expense_id = $this->input->post('expense_id');
        $basic_amount = $this->input->post('basic_amount');
        $expense_amount = $this->input->post('expense_amount');
        $date = date("Y-m-d", strtotime($this->input->post('date')));
        $expense_note = $this->input->post('expense_note');
        $expense_category = $this->input->post('expense_category');
        $expense_type = trim((string) $this->input->post('expense_type'));
        $expense_month = trim((string) $this->input->post('expense_month'));
        $gst_class = $this->input->post('gst_class');
        $employee_name = $this->input->post('employee_name');
        $status = $this->input->post('status');

        // Handle file upload safely
        $uploaded_file_path = '';
        if (isset($_FILES['expense_upload']) && !empty($_FILES['expense_upload']['tmp_name']) && is_uploaded_file($_FILES['expense_upload']['tmp_name'])) {
            $file = $_FILES['expense_upload'];
            $upload_dir = './uploads/';
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0755, true);
            }
            $safe_name = time() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $file['name']);
            $target_path = $upload_dir . $safe_name;
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $uploaded_file_path = $target_path;
            }
        }

        // Ensure numeric values
        $basic_amount_numeric = is_numeric($basic_amount) ? (float) $basic_amount : 0.0;
        
        // Get GST percentage value (remove % if present)
        $gst_value = $gst_class;
        if (is_string($gst_value)) {
            $gst_value = str_replace('%', '', $gst_value);
        }
        $gst_class_numeric = is_numeric($gst_value) ? (float) $gst_value : 0.0;

        // Calculate total = basic + GST amount
        $gst_amount = $basic_amount_numeric * ($gst_class_numeric / 100);
        $expense_amount_calculated = $basic_amount_numeric + $gst_amount;

        $expense_category = $this->apply_expense_mode_prefix($expense_category, $expense_mode);

        if ($expense_mode === 'indirect' && $this->normalize_expense_type($expense_type) === '') {
            $parsed_expense_category = $this->parse_master_expense_category($expense_category, 'indirect');
            $expense_type = $parsed_expense_category['expense_type'] !== '' ? ucfirst($parsed_expense_category['expense_type']) : '';
        }

        $this->ensure_expense_columns();

        $bank_voucher_type = trim((string) $this->input->post('bank_voucher_type'));
        
        $data_expense = array(
            'basic_amount'   => $basic_amount_numeric,
            'expense_amount' => $expense_amount_calculated,
            'expense_note' => $expense_note,
            'date' => $date,
            'uid' => $this->user_id,
            'expense_category' => $expense_category,
            'expense_type' => $expense_type,
            'expense_month' => $expense_month,
            'gst_class' => $gst_class,
            'employee_name' => $employee_name,
            'status' => $status,
            'bank_voucher_type' => $bank_voucher_type,
        );

        // For new records, always send a non-null upload value.
        // For edit, keep existing DB file path when no new file is uploaded.
        if ($expense_id) {
            if (!empty($uploaded_file_path)) {
                $data_expense['expense_upload'] = $uploaded_file_path;
            }
        } else {
            $data_expense['expense_upload'] = $uploaded_file_path ?: '';
        }

        if ($expense_id) {
            $result = $this->inventory->edit_expense($data_expense, $expense_id, $this->user_id);
        } else {
            $result = $this->inventory->add_expense($data_expense);
        }
        
        if ($result == True) {
            $this->session->set_flashdata('SUCCESSMSG', "Expense added successfully!!");
            if ($expense_mode == 'direct') {
                redirect('InventoryController/add_expense_data_direct');
            } else {
                redirect('InventoryController/add_expense_data_indirect');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Expense not added successfully!!");
            if ($expense_mode == 'direct') {
                redirect('InventoryController/add_expense_data_direct');
            } else {
                redirect('InventoryController/add_expense_data_indirect');
            }
        }
    }

    public function profit_loss_report()
    {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $expense_category = $this->input->post('expense_category');

        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);

        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);

        $from_date1 = date('Y-m-d', strtotime($this->input->post('from_date1')));
        $to_date1 = date('Y-m-d', strtotime($this->input->post('to_date1')));

        $data['cost_price'] = $this->inventory->get_cost_price_report_by_date($po_date1, $po_date2, $this->user_id);
        $data['sell_price'] = $this->inventory->get_sell_price_report_by_date($po_date1, $po_date2, $this->user_id);
        $data['expense_amount'] = $this->inventory->get_expense_amount_report_by_date($from_date1, $to_date1, $expense_category, $this->user_id);
        $data['categories'] = $this->inventory->get_expense_categories($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('admin/profit_loss_report', $data);
    }

    public function get_all_products_code()
    {
        $result = $this->inventory->get_product_part_name($this->user_id);
        echo json_encode($result);
    }

    public function get_all_product()
    {
        $result = $this->inventory->get_item_name($this->user_id);
        echo json_encode($result);
    }

    public function customer_wise_rate()
    {
        $data['customer_wise_rate'] = $this->inventory->get_customer_wise_rate_data();
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('inventory/customer_wise_rate', $data);
    }

    public function add_customer_wise_rate()
    {
        $customer_id_fk = $this->input->post('customer_id_fk');
        $inventory_id_fk = $this->input->post('inventory_id_fk');
        $customer_rate = $this->input->post('customer_rate');
        $date_added = date("Y-m-d");
        $date_updated = date("Y-m-d");

        $data_rate_cust_wise = array(
            'customer_id_fk' => $customer_id_fk,
            'inventory_id_fk' => $inventory_id_fk,
            'customer_rate' => $customer_rate,
            'rate_added_date' => $date_added,
            'rate_modified_date' => $date_updated
        );

        $result = $this->inventory->customer_rate_wise_check($customer_id_fk, $inventory_id_fk);

        if ($result == FALSE) {
            $this->inventory->add_customer_wise_rate($data_rate_cust_wise);
            $this->session->set_flashdata('SUCCESSMSG', "Party wise rate added successfully!!");
            redirect('InventoryController/customer_wise_rate');
        } else {
            $this->session->set_flashdata('INFOMSG', "Party wise rate already exist!!");
            redirect('InventoryController/customer_wise_rate');
        }
    }

    public function edit_customer_wise_rate()
    {
        $customer_wise_rate_id = $this->input->post('customer_wise_rate_id');
        $customer_id_fk = $this->input->post('customer_id_fk');
        $inventory_id_fk = $this->input->post('inventory_id_fk');
        $customer_rate = $this->input->post('customer_rate');
        $date_updated = date("Y-m-d");

        $data_rate_cust_wise = array(
            'customer_id_fk' => $customer_id_fk,
            'inventory_id_fk' => $inventory_id_fk,
            'customer_rate' => $customer_rate,
            'rate_modified_date' => $date_updated
        );

        $result = $this->inventory->edit_customer_wise_rate($data_rate_cust_wise, $customer_wise_rate_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Rate updated successfully!!");
            redirect('InventoryController/customer_wise_rate');
        } else {
            $this->session->set_flashdata('INFOMSG', "Rate not updated successfully!!");
            redirect('InventoryController/customer_wise_rate');
        }
    }

    public function get_customer_rate_by_id()
    {
        $id = $this->uri->segment(3);
        $data['customer_wise_rate'] = $this->inventory->get_customer_rate_by_id($id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('inventory/edit_customer_wise_rate', $data);
    }

    public function delete_customer_wise_rate_by_id()
    {
        $id = $this->uri->segment(3);
        $result = $this->inventory->delete_customer_wise_rate_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "customer rate deleted successfully!!");
            redirect('InventoryController/customer_wise_rate');
        } else {
            $this->session->set_flashdata('INFOMSG', "customer rate not deleted successfully!!");
            redirect('InventoryController/customer_wise_rate');
        }
    }

    public function scanner()
    {
        $this->load->view('scanner/test');
    }

    public function generate_code()
    {
        if (isset($_POST['generate_text'])) {
            require_once APPPATH . 'third_party/phpqrcode/qrlib.php';
            $text = $_POST['qr_text'];
            $folder = "uploads/";
            $file_name = "qr.png";
            $file_name = $folder . $file_name;
            QRcode::png($text, $file_name);
            QRcode::png($text);
            redirect('InventoryController/index');
        }
    }

    public function get_inventory_by_id_to_generate_bar_code()
    {
        $id = $this->uri->segment(3);

        $this->db->select('code');
        $this->db->from('inventory');
        $this->db->where('inventory_id', $id);
        $this->db->limit('1');
        $query = $this->db->get();
        $result_item_name = $query->row_array();

        $data['inventory'] = $this->inventory->get_inventory_by_id($id);
        $data['gst_class'] = $this->inventory->get_gst_class($this->user_id);
        $data['barcode_data']  = $this->inventory->get_barcode_barcode_master($result_item_name['code']);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('inventory/bar_code', $data);
    }

    public function get_barcode_id_barcode_master()
    {
        $item_name = $this->input->post('item_name');
        $barcode = $this->input->post('barcode');

        $data = array('item' => $item_name, 'barcode' => $barcode);
        $this->inventory->add_barcode_master($data);

        $result = $this->inventory->get_barcode_id_barcode_master($item_name);
        echo json_encode($result);
    }

    public function inventory_history()
    {
        $search_item = $this->input->get('search_item');
        $item_type = $this->input->get('item_type');
        $stock_status = $this->input->get('stock_status');
        $sort_by = $this->input->get('sort_by') ?: 'date_added';
        $barcode_no = $this->input->post('barcode_no');

        $session_data_head = $this->session->userdata('session_data_head');

        if (!empty($barcode_no)) {
            $data['result'] = array();
            $data['data'] = $this->inventory->get_history_product_barcode_id($barcode_no);
            $data['barcode'] = $this->inventory->get_all_barcode_for_autocomplete();

            if (empty($data['data'])) {
                $this->session->set_flashdata('SUCCESSMSG', "Invalid Barcode Number!!");
            }
        } else {
            $data['result'] = $this->inventory->get_filtered_inventory(
                $this->user_id,
                $search_item,
                $item_type,
                $stock_status,
                $sort_by
            );
            $data['barcode'] = $this->inventory->get_all_barcode_for_autocomplete();
            $data['data'] = array();
        }

        $data['filters'] = array(
            'search_item' => $search_item,
            'item_type' => $item_type,
            'stock_status' => $stock_status,
            'sort_by' => $sort_by
        );

        // Fetch active stock allocations to show who it is allocated to
        $stock_allocations_table = $this->db->dbprefix('stock_allocations');
        $joborder_total_table = $this->db->dbprefix('joborder_total');
        $alloc_query = $this->db->query("
            SELECT sa.inventory_id, sa.allocated_quantity, sa.issued_quantity, sa.notes,
                   jt.number_fk as joborder_number, 
                   COALESCE(NULLIF(jt.salesorder_number, ''), NULLIF(jt.oc_number, ''), jt.so_reference, '') as salesorder_number
            FROM {$stock_allocations_table} sa
            LEFT JOIN {$joborder_total_table} jt ON jt.id = sa.joborder_id
            WHERE sa.status != 'cancelled' AND sa.allocated_quantity > sa.issued_quantity
        ");
        $allocations = array();
        if ($alloc_query) {
            foreach ($alloc_query->result_array() as $row) {
                $inv_id = $row['inventory_id'];
                if (!isset($allocations[$inv_id])) {
                    $allocations[$inv_id] = array();
                }
                $allocations[$inv_id][] = $row;
            }
        }
        $data['allocations'] = $allocations;

        // Fetch issued quantities from approved material issue slips (with JO ids for links)
        $mis_items_table  = $this->db->dbprefix('material_issue_items');
        $mis_slips_table  = $this->db->dbprefix('material_issue_slips');
        $jo_table         = $this->db->dbprefix('joborder_total');
        $issued_query = $this->db->query("
            SELECT
                mii.inventory_id_fk          AS inventory_id,
                SUM(mii.quantity)            AS total_issued_qty,
                GROUP_CONCAT(
                    DISTINCT CONCAT_WS('|', mis.joborder_number, COALESCE(jt.id,''), mis.issue_id, mis.issue_no)
                    ORDER BY mis.joborder_number
                    SEPARATOR ';;'
                ) AS jo_details
            FROM {$mis_items_table} mii
            INNER JOIN {$mis_slips_table} mis ON mis.issue_id = mii.issue_id
            LEFT  JOIN {$jo_table} jt ON jt.number_fk = mis.joborder_number
            WHERE mis.status IN ('issued', 'draft')
            GROUP BY mii.inventory_id_fk
        ");
        $issued_data = array();
        if ($issued_query) {
            foreach ($issued_query->result_array() as $row) {
                // Parse the concatenated string into structured array
                $jo_entries  = array();
                $slip_entries = array();
                if (!empty($row['jo_details'])) {
                    foreach (explode(';;', $row['jo_details']) as $entry) {
                        $parts = explode('|', $entry);
                        $jo_num   = isset($parts[0]) ? trim($parts[0]) : '';
                        $jo_id    = isset($parts[1]) ? trim($parts[1]) : '';
                        $slip_id  = isset($parts[2]) ? trim($parts[2]) : '';
                        $slip_no  = isset($parts[3]) ? trim($parts[3]) : '';
                        if ($jo_num && !isset($jo_entries[$jo_num])) {
                            $jo_entries[$jo_num] = array('jo_number' => $jo_num, 'jo_id' => $jo_id);
                        }
                        if ($slip_no && !isset($slip_entries[$slip_no])) {
                            $slip_entries[$slip_no] = array('slip_no' => $slip_no, 'slip_id' => $slip_id);
                        }
                    }
                }
                $issued_data[$row['inventory_id']] = array(
                    'total_issued_qty' => $row['total_issued_qty'],
                    'jo_entries'       => array_values($jo_entries),
                    'slip_entries'     => array_values($slip_entries),
                );
            }
        }
        $data['issued_data'] = $issued_data;

        // Fetch deletion requests for tabs
        $res = $session_data_head['result'] ?? [];
        $role_name = strtolower($res['role_name'] ?? '');
        $role_id   = (int)($res['role_id'] ?? $res['user_role_id'] ?? 0);
        $user_id   = (int)($res['user_id'] ?? 0);
        $is_admin  = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);

        if ($is_admin) {
            $data['approved_deletions'] = $this->db->where('status', 'approved')->where('module', 'inventory')->order_by('updated_at', 'DESC')->get('item_delete_requests')->result_array();
            $data['deletion_history']   = $this->db->where_in('status', ['pending', 'rejected', 'deleted'])->where('module', 'inventory')->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
        } else {
            $data['approved_deletions'] = $this->db->where('status', 'approved')->where('module', 'inventory')->where('requested_by', $user_id)->order_by('updated_at', 'DESC')->get('item_delete_requests')->result_array();
            $data['deletion_history']   = $this->db->where_in('status', ['pending', 'approved', 'rejected', 'deleted'])->where('module', 'inventory')->where('requested_by', $user_id)->order_by('created_at', 'DESC')->get('item_delete_requests')->result_array();
        }

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('inventory/inventory_history', $data);
    }
    
    public function increase_inventory_stock()
    {
        $item = $this->input->post('item');
        $result = $this->inventory->increase_inventory_stock($item);
        echo json_encode($result);
    }

    public function add_expense_cateogry()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->post('expense_mode'));
        $expense_type = $this->normalize_expense_type($this->input->post('expense_type'));
        $expense_cateogry = $this->input->post('exp_cat');
        $expense_cateogry = $this->build_master_expense_category($expense_cateogry, $expense_mode, $expense_type);

        if ($expense_cateogry === '') {
            $this->session->set_flashdata('INFOMSG', "Please enter valid Expense Category and Expenditure Type!!");
            $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
            redirect($redirect_url);
            return;
        }

        $data_expense_cateogry = array('exp_cat' => $expense_cateogry, 'uid' => $this->user_id);
        $result = $this->expense->exp_cat_check($expense_cateogry, $this->user_id);
        if ($result == FALSE) {
            $this->expense->add_expense_cateogry($data_expense_cateogry);
            $this->session->set_flashdata('SUCCESSMSG', "Expense Category added successfully!!");
            $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
            redirect($redirect_url);
        } else {
            $this->session->set_flashdata('INFOMSG', "Expense Category already exist!!");
            $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
            redirect($redirect_url);
        }
    }
    
    public function delete_exp_cat_by_id()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->get('expense_mode'));
        $id = $this->uri->segment(3);
        $result = $this->expense->delete_exp_cat_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Expense Category deleted successfully!!");
            $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
            redirect($redirect_url);
        } else {
            $this->session->set_flashdata('INFOMSG', "Expense Category not deleted successfully!!");
            $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
            redirect($redirect_url);
        }
    }

    public function expense_catgory()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->get('expense_mode'));
        if ($expense_mode === 'direct') {
            redirect('InventoryController/direct_expense_master');
        } elseif ($expense_mode === 'indirect') {
            redirect('InventoryController/indirect_expense_master');
        } else {
            // Default to direct if no mode specified
            redirect('InventoryController/direct_expense_master');
        }
    }

    public function direct_expense_master()
    {
        $data['expense_catgory'] = $this->filter_expense_categories_by_mode($this->expense->get_expense_catgory($this->user_id), 'direct');
        $data['expense_mode'] = 'direct';
        $edit_id = (int) $this->input->get('edit_id');
        if ($edit_id > 0) {
            $data['edit_expense_cat'] = $this->expense->get_exp_cat_by_id($edit_id);
            if (!empty($data['edit_expense_cat'])) {
                $parsed = $this->parse_master_expense_category($data['edit_expense_cat']['exp_cat'], 'direct');
                $data['edit_expense_name'] = $parsed['name'];
                $data['edit_expense_type'] = $parsed['expense_type'];
            }
        }
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('expense/expense_catgory', $data);
    }

    public function indirect_expense_master()
    {
        $data['expense_catgory'] = $this->filter_expense_categories_by_mode($this->expense->get_expense_catgory($this->user_id), 'indirect');
        $data['expense_mode'] = 'indirect';
        $edit_id = (int) $this->input->get('edit_id');
        if ($edit_id > 0) {
            $data['edit_expense_cat'] = $this->expense->get_exp_cat_by_id($edit_id);
            if (!empty($data['edit_expense_cat'])) {
                $parsed = $this->parse_master_expense_category($data['edit_expense_cat']['exp_cat'], 'indirect');
                $data['edit_expense_name'] = $parsed['name'];
                $data['edit_expense_type'] = $parsed['expense_type'];
            }
        }
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('expense/expense_catgory', $data);
    }

    public function edit_expense_cateogry()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->post('expense_mode'));
        $expense_type = $this->normalize_expense_type($this->input->post('expense_type'));
        $exp_cat_id = (int) $this->input->post('exp_cat_id');
        $expense_cateogry = $this->input->post('exp_cat');

        if ($exp_cat_id <= 0 || trim((string) $expense_cateogry) === '') {
            $this->session->set_flashdata('INFOMSG', "Invalid Expense Category data!!");
            $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
            redirect($redirect_url);
            return;
        }

        $expense_cateogry = $this->build_master_expense_category($expense_cateogry, $expense_mode, $expense_type);
        if ($expense_cateogry === '') {
            $this->session->set_flashdata('INFOMSG', "Please select valid Expenditure Type!!");
            $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
            redirect($redirect_url . '?edit_id=' . $exp_cat_id);
            return;
        }

        $exists = $this->expense->exp_cat_check_except_id($expense_cateogry, $exp_cat_id);
        if ($exists) {
            $this->session->set_flashdata('INFOMSG', "Expense Category already exist!!");
            $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
            redirect($redirect_url . '?edit_id=' . $exp_cat_id);
            return;
        }

        $result = $this->expense->edit_exp_cat_by_id($exp_cat_id, array('exp_cat' => $expense_cateogry));
        if ($result) {
            $this->session->set_flashdata('SUCCESSMSG', "Expense Category updated successfully!!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Expense Category not updated successfully!!");
        }

        $redirect_url = ($expense_mode === 'direct') ? 'InventoryController/direct_expense_master' : 'InventoryController/indirect_expense_master';
        redirect($redirect_url);
    }
    
    public function get_monthyearwise_record()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->post('expense_mode'));
        $month_year = $this->input->post('month_year');
        $data['expense_result'] = $this->inventory->get_monthyearwise_record($month_year, $this->user_id);
        $data['expense_result'] = $this->filter_expense_entries_by_mode($data['expense_result'], $expense_mode);
        $data['gst_class_result'] = $this->gst->get_gst_classes($this->user_id);
        $data['expense_catgory'] = $this->filter_expense_categories_by_mode($this->expense->get_expense_catgory($this->user_id), $expense_mode);
        $data['expense_mode'] = $expense_mode;
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('expense/add_expense', $data);
    }

    public function export_inventory()
    {
        if ($this->user_id === NULL) {
            $this->session->set_flashdata('ERRORMSG', 'Please login to export data');
            redirect('LoginController/logout');
        }

        $search_item = $this->input->get('search_item');
        $item_type = $this->input->get('item_type');
        $stock_status = $this->input->get('stock_status');
        $sort_by = $this->input->get('sort_by') ?: 'date_added';

        $result = $this->inventory->get_filtered_inventory(
            $this->user_id,
            $search_item,
            $item_type,
            $stock_status,
            $sort_by,
            0  // no limit — export all records
        );


        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $this->export_inventory_excel($result);
        } else {
            $this->export_inventory_csv($result);
        }
    }

    private function export_inventory_excel($result)
    {
        require_once APPPATH . '../vendor/autoload.php';

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getProperties()
                ->setCreator("ERP System")
                ->setLastModifiedBy("ERP System")
                ->setTitle("Inventory Report")
                ->setSubject("Inventory Details")
                ->setDescription("Export of inventory items with filters");

            $headers = [
                'Sr.No.', 'Item Code', 'Item Name', 'Description', 'HSN/SAC',
                'GST %', 'Type', 'Stock', 'Allocated', 'Available', 'Unit', 'Cost Price', 'Sell Price',
                'Category', 'Group', 'Date Added', 'Date Modified'
            ];

            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->getFont()->setBold(true);
                $sheet->getStyle($column . '1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFCCCCCC');
                $column++;
            }

            $row = 2;
            $sr_no = 1;
            $total_stock_value = 0;

            foreach ($result as $item) {
                $type_text = ($item->item_type == 'B') ? 'Boughtout' : 'Manufacturing';
                $item_stock_value = $item->stock * $item->cost_price;
                $total_stock_value += $item_stock_value;

                $sheet->setCellValue('A' . $row, $sr_no);
                $sheet->setCellValue('B' . $row, $item->code ?? '');
                $sheet->setCellValue('C' . $row, $item->item_name ?? '');
                $sheet->setCellValue('D' . $row, $item->prod_description ?? '');
                $sheet->setCellValue('E' . $row, $item->hsn ?? '');
                $sheet->setCellValue('F' . $row, $item->gst_per . '%');
                $sheet->setCellValue('G' . $row, $type_text);
                
                $stockCell = $sheet->getCell('H' . $row);
                $stockCell->setValue($item->stock);
                $reorder_lvl = isset($item->reorder_level) && $item->reorder_level !== '' ? intval($item->reorder_level) : 10;
                if ($item->stock <= $reorder_lvl) {
                    $sheet->getStyle('H' . $row)->getFont()->getColor()
                        ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                }

                $sheet->setCellValue('I' . $row, $item->allocated_stock ?? 0);
                $sheet->setCellValue('J' . $row, $item->available_stock ?? 0);

                $sheet->setCellValue('K' . $row, $item->unit ?? '');
                $sheet->setCellValue('L' . $row, $item->cost_price);
                $sheet->setCellValue('M' . $row, $item->sell_price);
                $sheet->setCellValue('N' . $row, $item->category_name ?? 'N/A');
                $sheet->setCellValue('O' . $row, $item->group_name ?? 'N/A');
                $sheet->setCellValue('P' . $row, date('d-m-Y', strtotime($item->date_added)));
                $sheet->setCellValue('Q' . $row, $item->date_modified ?? '');

                $sheet->getStyle('L' . $row . ':M' . $row)
                    ->getNumberFormat()
                    ->setFormatCode('₹#,##0.00');

                $row++;
                $sr_no++;
            }

            $summaryRow = $row + 1;
            $sheet->setCellValue('I' . $summaryRow, 'SUMMARY:');
            $sheet->getStyle('I' . $summaryRow)->getFont()->setBold(true);
            $sheet->setCellValue('J' . $summaryRow, 'Total Items:');
            $sheet->setCellValue('K' . $summaryRow, count($result));
            $sheet->setCellValue('L' . $summaryRow, 'Total Stock Value:');
            $sheet->setCellValue('M' . $summaryRow, $total_stock_value);
            $sheet->getStyle('M' . $summaryRow)->getNumberFormat()->setFormatCode('₹#,##0.00');

            foreach (range('A', 'Q') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'inventory_report_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            $this->export_inventory_csv($result);
        }
    }

    private function export_inventory_csv($result)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=inventory_history_' . date('Y-m-d_H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Item Code', 'Item Name', 'Description', 'HSN/SAC', 'GST%', 'Type',
            'Stock', 'Allocated', 'Available', 'Unit', 'Cost Price', 'Sell Price', 'Category', 'Group',
            'Date Added', 'Date Modified'
        ]);

        foreach ($result as $row) {
            $type_text = ($row->item_type == 'B') ? 'Boughtout' : 'Manufacturing';

            fputcsv($output, [
                $row->code ?? '',
                $row->item_name ?? '',
                $row->prod_description ?? '',
                $row->hsn ?? '',
                $row->gst_per . '%',
                $type_text,
                $row->stock,
                $row->allocated_stock ?? 0,
                $row->available_stock ?? 0,
                $row->unit ?? '',
                '₹' . number_format($row->cost_price, 2),
                '₹' . number_format($row->sell_price, 2),
                $row->category_name ?? 'N/A',
                $row->group_name ?? 'N/A',
                date('d-m-Y', strtotime($row->date_added)),
                $row->date_modified ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    public function import_inventory_view()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('inventory/import_inventory');
    }

    public function download_inventory_template()
    {
        require_once APPPATH . '../vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()
            ->setCreator("ERP System")
            ->setLastModifiedBy("ERP System")
            ->setTitle("Inventory Import Template")
            ->setSubject("Inventory Import")
            ->setDescription("Template for importing inventory items");

        $gst_classes = $this->inventory->get_gst_class($this->user_id);
        $units = $this->units->get_unit_name($this->user_id);
        $categories = $this->ItemCategory->get_categories($this->user_id);
        $groups = $this->ItemGroup->get_groups($this->user_id);

        $gst_values = array_map(function ($item) {
            return str_replace('%', '', $item['gst_class']);
        }, $gst_classes);

        $unit_values = array_map(function ($item) {
            return $item->unit;
        }, $units);

        $headers = [
            'Code*', 'Item Name', 'Description', 'HSN*', 'GST %*', 'Unit*',
            'Item Type* (B/M)', 'Stock', 'Cost Price', 'Sell Price',
            'Category Name', 'Group Name', 'Reorder Level'
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getStyle($column . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFCCCCCC');
            $column++;
        }

        $sampleData = [
            'TEST001', 'Test Product', 'Product description here', 123456,
            '18', 'PCS', 'B', 10, 100.50, 150.75, 'Electronics (ID: 1)',
            'Components (ID: 2)', 5
        ];

        $column = 'A';
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->setCellValue($column . $row, $data);
            $sheet->getStyle($column . $row)->getFont()->setItalic(true)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN));
            $column++;
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'inventory_import_template_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function process_inventory_import()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $config['upload_path'] = './uploads/imports/inventory/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = 5120;
            $config['encrypt_name'] = true;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('inventory_file')) {
                $this->session->set_flashdata('INFOMSG', 'Upload Error: ' . $this->upload->display_errors());
                redirect('InventoryController/import_inventory_view');
            }

            $upload_data = $this->upload->data();
            $file_path = $config['upload_path'] . $upload_data['file_name'];

            require_once APPPATH . '../vendor/autoload.php';

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                array_shift($rows);
                array_shift($rows);

                $imported = 0;
                $updated = 0;
                $skipped = 0;
                $errors = [];
                $error_rows = [];

                foreach ($rows as $index => $row) {
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $row_number = $index + 3;

                    if (empty($row[0])) {
                        $errors[] = "Row {$row_number}: Code is required";
                        $skipped++;
                        continue;
                    }

                    if (empty($row[3]) || !is_numeric($row[3])) {
                        $errors[] = "Row {$row_number}: Valid HSN code is required";
                        $skipped++;
                        continue;
                    }

                    if (empty($row[4]) || !is_numeric($row[4])) {
                        $errors[] = "Row {$row_number}: Valid GST percentage is required";
                        $skipped++;
                        continue;
                    }

                    $code = trim($row[0]);
                    $item_name = !empty($row[1]) ? trim($row[1]) : $code;
                    $description = !empty($row[2]) ? trim($row[2]) : '';
                    $hsn = (int)$row[3];
                    $gst_per = $row[4] . '%';
                    $unit = trim($row[5]);
                    $item_type = trim($row[6]);
                    $stock = !empty($row[7]) ? (int)$row[7] : 0;
                    $cost_price = !empty($row[8]) ? (float)$row[8] : 0;
                    $sell_price = !empty($row[9]) ? (float)$row[9] : 0;
                    $reorder_level = !empty($row[12]) ? (int)$row[12] : 10;

                    $category_id = null;
                    if (!empty($row[10])) {
                        $category_name = trim($row[10]);
                        if (preg_match('/\(ID:\s*(\d+)\)/', $category_name, $matches)) {
                            $category_id = $matches[1];
                        } else {
                            $category = $this->db
                                ->where('category_name', $category_name)
                                ->get('item_category_master')
                                ->row();
                            if ($category) {
                                $category_id = $category->category_id;
                            }
                        }
                    }

                    $group_id = null;
                    if (!empty($row[11])) {
                        $group_name = trim($row[11]);
                        if (preg_match('/\(ID:\s*(\d+)\)/', $group_name, $matches)) {
                            $group_id = $matches[1];
                        } else {
                            $group = $this->db
                                ->where('group_name', $group_name)
                                ->get('item_group_master')
                                ->row();
                            if ($group) {
                                $group_id = $group->group_id;
                            }
                        }
                    }

                    $existing = $this->db
                        ->where('code', $code)
                        ->get('inventory')
                        ->row();

                    $date_added = date("Y-m-d");
                    $date_updated = date("d-m-Y");

                    $inventory_data = array(
                        'item_name' => $item_name,
                        'prod_description' => $description,
                        'code' => $code,
                        'hsn' => $hsn,
                        'unit' => $unit,
                        'item_type' => $item_type,
                        'gst_per' => $gst_per,
                        'stock' => $stock,
                        'reorder_level' => $reorder_level,
                        'cost_price' => $cost_price,
                        'sell_price' => $sell_price,
                        'date_added' => $date_added,
                        'date_modified' => $date_updated,
                        'uid' => $this->user_id,
                        'category_id' => $category_id,
                        'group_id' => $group_id
                    );

                    if ($existing) {
                        $this->db->where('inventory_id', $existing->inventory_id);
                        if ($this->db->update('inventory', $inventory_data)) {
                            $updated++;
                        } else {
                            $errors[] = "Row {$row_number}: Failed to update item";
                            $error_rows[] = $row_number;
                            $skipped++;
                        }
                    } else {
                        if ($this->db->insert('inventory', $inventory_data)) {
                            $imported++;
                        } else {
                            $errors[] = "Row {$row_number}: Failed to insert item";
                            $error_rows[] = $row_number;
                            $skipped++;
                        }
                    }
                }

                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                $message = "Import completed: {$imported} items imported, {$updated} items updated successfully.";
                if ($skipped > 0) {
                    $message .= " {$skipped} items skipped.";
                }

                if (!empty($errors)) {
                    $this->session->set_flashdata('IMPORT_ERRORS', $errors);
                    $this->session->set_flashdata('ERROR_ROWS', $error_rows);
                }

                $this->session->set_flashdata('SUCCESSMSG', $message);
            } catch (Exception $e) {
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                $this->session->set_flashdata('INFOMSG', 'Error processing file: ' . $e->getMessage());
            }

            redirect('InventoryController/import_inventory_view');
        }
    }

    public function import_inventory_summary()
    {
        if ($this->session->flashdata('IMPORT_ERRORS')) {
            $data['errors'] = $this->session->flashdata('IMPORT_ERRORS');
            $data['error_rows'] = $this->session->flashdata('ERROR_ROWS');

            $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('inventory/import_summary', $data);
        } else {
            redirect('InventoryController/index');
        }
    }

    private function get_category_group_mappings()
    {
        $mappings = [
            'categories' => [],
            'groups' => []
        ];

        $categories = $this->ItemCategory->get_categories($this->user_id);
        foreach ($categories as $category) {
            $mappings['categories'][$category->category_id] = $category->category_name;
            $mappings['categories'][$category->category_name] = $category->category_id;
        }

        $groups = $this->ItemGroup->get_groups($this->user_id);
        foreach ($groups as $group) {
            $mappings['groups'][$group->group_id] = $group->group_name;
            $mappings['groups'][$group->group_name] = $group->group_id;
        }

        return $mappings;
    }

    public function export_inventory_pdf()
    {
        if ($this->user_id === NULL) {
            $this->session->set_flashdata('ERRORMSG', 'Please login to export data');
            redirect('LoginController/logout');
        }

        $search_item = $this->input->get('search_item');
        $item_type = $this->input->get('item_type');
        $stock_status = $this->input->get('stock_status');
        $sort_by = $this->input->get('sort_by') ?: 'date_added';

        $result = $this->inventory->get_filtered_inventory(
            $this->user_id,
            $search_item,
            $item_type,
            $stock_status,
            $sort_by,
            0  // no limit for PDF export
        );

        $this->generate_inventory_pdf($result, $search_item, $item_type, $stock_status, $sort_by);
    }

    private function generate_inventory_pdf($result, $search_item, $item_type, $stock_status, $sort_by)
    {
        $html = $this->load->view('inventory/pdf_template', [
            'result' => $result,
            'filters' => [
                'search_item' => $search_item,
                'item_type' => $item_type,
                'stock_status' => $stock_status,
                'sort_by' => $sort_by
            ],
            'user_id' => $this->user_id,
            'total_items' => count($result),
            'total_stock_value' => $this->calculate_total_stock_value($result),
            'low_stock_count' => $this->count_low_stock_items($result)
        ], true);

        if (class_exists('\Mpdf\Mpdf')) {
            require_once APPPATH . '../vendor/autoload.php';

            try {
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'margin_left' => 15,
                    'margin_right' => 15,
                    'margin_top' => 20,
                    'margin_bottom' => 20,
                    'margin_header' => 10,
                    'margin_footer' => 10,
                    'default_font' => 'dejavusans'
                ]);

                $mpdf->SetWatermarkText('INVENTORY REPORT');
                $mpdf->showWatermarkText = true;
                $mpdf->watermark_font = 'DejaVuSansCondensed';
                $mpdf->watermarkTextAlpha = 0.1;

                $mpdf->SetCreator("ERP System");
                $mpdf->SetAuthor("ERP System");
                $mpdf->SetTitle("Inventory Report");
                $mpdf->SetSubject("Inventory Details");
                $mpdf->SetKeywords("Inventory, Report, PDF");

                $header = '<div style="text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                        <h2 style="color: #3498db; margin: 0;">Inventory Report</h2>
                        <p style="color: #666; font-size: 12px; margin: 5px 0;">Generated on: ' . date('d-m-Y') . '</p>
                      </div>';
                $mpdf->SetHTMLHeader($header);

                $footer = '<table width="100%" style="border-top: 1px solid #ddd; padding-top: 5px;">
                        <tr>
                            <td width="33%" style="text-align: left; font-size: 10px; color: #666;">ERP System</td>
                            <td width="34%" style="text-align: center; font-size: 10px; color: #666;">Page {PAGENO} of {nbpg}</td>
                            <td width="33%" style="text-align: right; font-size: 10px; color: #666;">' . date('d-m-Y') . '</td>
                        </tr>
                      </table>';
                $mpdf->SetHTMLFooter($footer);

                $mpdf->WriteHTML($html);

                $filename = 'inventory_report_' . date('Ymd_His') . '.pdf';
                $mpdf->Output($filename, 'D');
                exit;
            } catch (Exception $e) {
                $this->fallback_pdf_generation($html);
            }
        } else {
            $this->fallback_pdf_generation($html);
        }
    }

    private function fallback_pdf_generation($html)
    {
        if (file_exists(APPPATH . 'libraries/m_pdf.php')) {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $filename = 'inventory_report_' . date('Ymd_His') . '.pdf';
            $this->m_pdf->pdf->Output($filename, 'D');
            exit;
        } else {
            header('Content-Type: text/html');
            header('Content-Disposition: attachment; filename="inventory_report_' . date('Ymd_His') . '.html"');
            echo $html;
            exit;
        }
    }

    private function calculate_total_stock_value($result)
    {
        $total = 0;
        foreach ($result as $item) {
            $total += ($item->stock * $item->cost_price);
        }
        return $total;
    }

    private function count_low_stock_items($result)
    {
        $count = 0;
        foreach ($result as $item) {
            $reorder_lvl = isset($item->reorder_level) && $item->reorder_level !== '' ? intval($item->reorder_level) : 10;
            if ($item->stock <= $reorder_lvl) {
                $count++;
            }
        }
        return $count;
    }

    public function ajax_add_inventory()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
        
        $item_name = $this->input->post('item_name');
        $item_name_display = $this->input->post('item_name_display');
        
        if (empty($item_name) && !empty($item_name_display)) {
            $item_name = $item_name_display;
        } elseif (empty($item_name)) {
            $item_name = $this->input->post('code');
        }
        
        $prod_description = $this->input->post('prod_description');
        $code             = $this->input->post('code');
        $hsn              = $this->input->post('hsn');
        $unit             = $this->input->post('unit');
        $item_type        = $this->input->post('item_type');
        $gst_per          = $this->input->post('gst_per');
        $stock            = $this->input->post('stock') ?: 0;
        $cost_price       = $this->input->post('cost_price') ?: 0;
        $sell_price       = $this->input->post('sell_price') ?: 0;
        $category_id      = $this->input->post('category_id');
        $group_id         = $this->input->post('group_id') ?: null;
        
        $errors = [];
        if (empty($code)) $errors[] = 'Item code is required';
        if (empty($hsn)) $errors[] = 'HSN code is required';
        if (empty($unit)) $errors[] = 'Unit is required';
        if (empty($gst_per)) $errors[] = 'GST percentage is required';
       
        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors
            ]);
            return;
        }
        
        // Handle category_id validation
        if (empty($category_id) || $category_id == '' || $category_id == 0) {
            $category_id = null;
        } else {
            $category_exists = $this->db
                ->where('category_id', $category_id)
                ->get('item_category_master')
                ->num_rows();
                
            if ($category_exists == 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid category selected!'
                ]);
                return;
            }
        }
        
        // Handle group_id validation
        if (empty($group_id) || $group_id == '' || $group_id == 0) {
            $group_id = null;
        } else {
            $group_exists = $this->db
                ->where('group_id', $group_id)
                ->get('item_group_master')
                ->num_rows();
                
            if ($group_exists == 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid group selected!'
                ]);
                return;
            }
        }
        
        $date_added = date("Y-m-d");
        $date_updated = date("d-m-Y");
        
        $data_inventory = array(
            'item_name'       => $item_name,
            'prod_description' => $prod_description,
            'code'            => $code,
            'hsn'             => $hsn,
            'unit'            => $unit,
            'item_type'       => $item_type,
            'gst_per'         => $gst_per,
            'stock'           => $stock,
            'cost_price'      => $cost_price,
            'sell_price'      => $sell_price,
            'category_id'     => $category_id,
            'group_id'        => $group_id,
            'date_added'      => $date_added,
            'date_modified'   => $date_updated,
            'uid'             => $this->user_id
        );
        
        $existing = $this->inventory->inventory_code_check($code, $this->user_id);
        
        if ($existing == FALSE) {
            $insert_id = $this->inventory->add_inventory($data_inventory);
            
            if ($insert_id) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Inventory item added successfully!',
                    'id' => $insert_id,
                    'data' => [
                        'code' => $code,
                        'item_name' => $item_name,
                        'hsn' => $hsn,
                        'unit' => $unit,
                        'gst_per' => $gst_per,
                        'sell_price' => $sell_price
                    ]
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to add inventory item. Database error.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'exist',
                'message' => 'Item code already exists!'
            ]);
        }
    }

    // ---- Indirect Individual Master ----

    public function indirect_individual_master()
    {
        $edit_id = (int) $this->input->get('edit_id');
        $data['records'] = $this->expense->get_indirect_individuals($this->user_id);
        $data['edit_record'] = null;
        if ($edit_id > 0) {
            $rec = $this->expense->get_indirect_individual_by_id($edit_id);
            if (!empty($rec) && (int)$rec['uid'] === (int)$this->user_id) {
                $data['edit_record'] = $rec;
            }
        }
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('expense/indirect_individual_master', $data);
    }

    public function add_indirect_individual()
    {
        $code         = trim($this->input->post('code'));
        $employee_name = trim($this->input->post('employee_name'));
        $type         = trim($this->input->post('type'));

        if ($code === '' || $employee_name === '' || $type === '') {
            $this->session->set_flashdata('INFOMSG', 'All fields are required.');
            redirect('InventoryController/indirect_individual_master');
            return;
        }

        if ($this->expense->indirect_individual_code_exists($code, $this->user_id)) {
            $this->session->set_flashdata('INFOMSG', 'Code already exists.');
            redirect('InventoryController/indirect_individual_master');
            return;
        }

        $this->expense->add_indirect_individual([
            'code'          => $code,
            'employee_name' => $employee_name,
            'type'          => $type,
            'uid'           => $this->user_id,
        ]);
        $this->session->set_flashdata('SUCCESSMSG', 'Record added successfully.');
        redirect('InventoryController/indirect_individual_master');
    }

    public function edit_indirect_individual()
    {
        $id           = (int) $this->input->post('id');
        $code         = trim($this->input->post('code'));
        $employee_name = trim($this->input->post('employee_name'));
        $type         = trim($this->input->post('type'));

        if ($id <= 0 || $code === '' || $employee_name === '' || $type === '') {
            $this->session->set_flashdata('INFOMSG', 'All fields are required.');
            redirect('InventoryController/indirect_individual_master');
            return;
        }

        $rec = $this->expense->get_indirect_individual_by_id($id);
        if (empty($rec) || (int)$rec['uid'] !== (int)$this->user_id) {
            $this->session->set_flashdata('INFOMSG', 'Record not found.');
            redirect('InventoryController/indirect_individual_master');
            return;
        }

        if ($this->expense->indirect_individual_code_exists($code, $this->user_id, $id)) {
            $this->session->set_flashdata('INFOMSG', 'Code already exists.');
            redirect('InventoryController/indirect_individual_master?edit_id=' . $id);
            return;
        }

        $this->expense->edit_indirect_individual($id, [
            'code'          => $code,
            'employee_name' => $employee_name,
            'type'          => $type,
        ]);
        $this->session->set_flashdata('SUCCESSMSG', 'Record updated successfully.');
        redirect('InventoryController/indirect_individual_master');
    }

    public function delete_indirect_individual($id)
    {
        $id = (int) $id;
        $rec = $this->expense->get_indirect_individual_by_id($id);
        if (!empty($rec) && (int)$rec['uid'] === (int)$this->user_id) {
            $this->expense->delete_indirect_individual($id);
            $this->session->set_flashdata('SUCCESSMSG', 'Record deleted successfully.');
        } else {
            $this->session->set_flashdata('INFOMSG', 'Record not found.');
        }
        redirect('InventoryController/indirect_individual_master');
    }

    // ---- Direct Individual Master ----

    public function direct_individual_master()
    {
        $edit_id = (int) $this->input->get('edit_id');
        $data['records'] = $this->expense->get_direct_individuals($this->user_id);
        $data['edit_record'] = null;
        if ($edit_id > 0) {
            $rec = $this->expense->get_direct_individual_by_id($edit_id);
            if (!empty($rec) && (int)$rec['uid'] === (int)$this->user_id) {
                $data['edit_record'] = $rec;
            }
        }
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('expense/direct_individual_master', $data);
    }

    public function add_direct_individual()
    {
        $code         = trim($this->input->post('code'));
        $employee_name = trim($this->input->post('employee_name'));
        $type         = trim($this->input->post('type'));

        if ($code === '' || $employee_name === '' || $type === '') {
            $this->session->set_flashdata('INFOMSG', 'All fields are required.');
            redirect('InventoryController/direct_individual_master');
            return;
        }

        if ($this->expense->direct_individual_code_exists($code, $this->user_id)) {
            $this->session->set_flashdata('INFOMSG', 'Code already exists.');
            redirect('InventoryController/direct_individual_master');
            return;
        }

        $this->expense->add_direct_individual([
            'code'          => $code,
            'employee_name' => $employee_name,
            'type'          => $type,
            'uid'           => $this->user_id,
        ]);
        $this->session->set_flashdata('SUCCESSMSG', 'Record added successfully.');
        redirect('InventoryController/direct_individual_master');
    }

    public function edit_direct_individual()
    {
        $id           = (int) $this->input->post('id');
        $code         = trim($this->input->post('code'));
        $employee_name = trim($this->input->post('employee_name'));
        $type         = trim($this->input->post('type'));

        if ($id <= 0 || $code === '' || $employee_name === '' || $type === '') {
            $this->session->set_flashdata('INFOMSG', 'All fields are required.');
            redirect('InventoryController/direct_individual_master');
            return;
        }

        $rec = $this->expense->get_direct_individual_by_id($id);
        if (empty($rec) || (int)$rec['uid'] !== (int)$this->user_id) {
            $this->session->set_flashdata('INFOMSG', 'Record not found.');
            redirect('InventoryController/direct_individual_master');
            return;
        }

        if ($this->expense->direct_individual_code_exists($code, $this->user_id, $id)) {
            $this->session->set_flashdata('INFOMSG', 'Code already exists.');
            redirect('InventoryController/direct_individual_master?edit_id=' . $id);
            return;
        }

        $this->expense->edit_direct_individual($id, [
            'code'          => $code,
            'employee_name' => $employee_name,
            'type'          => $type,
        ]);
        $this->session->set_flashdata('SUCCESSMSG', 'Record updated successfully.');
        redirect('InventoryController/direct_individual_master');
    }

    public function delete_direct_individual($id)
    {
        $id = (int) $id;
        $rec = $this->expense->get_direct_individual_by_id($id);
        if (!empty($rec) && (int)$rec['uid'] === (int)$this->user_id) {
            $this->expense->delete_direct_individual($id);
            $this->session->set_flashdata('SUCCESSMSG', 'Record deleted successfully.');
        } else {
            $this->session->set_flashdata('INFOMSG', 'Record not found.');
        }
        redirect('InventoryController/direct_individual_master');
    }

    // ---- Expense Export ----

   public function export_expense_excel()
{
    $expense_mode  = $this->normalize_expense_mode($this->input->get('expense_mode'));
    $expense_id    = (int) $this->input->get('expense_id');

    if ($expense_id > 0) {
        $row = $this->inventory->get_expense_by_id($expense_id);
        $records = $row ? array((object) $row) : array();
    } else {
        $all = $this->inventory->get_all_expense($this->user_id);
        $records = $this->filter_expense_entries_by_mode($all, $expense_mode);
    }

    $mode_label = ($expense_mode == 'direct') ? 'Direct' : (($expense_mode == 'indirect') ? 'Indirect' : 'Expense');
    $filename   = strtolower($mode_label) . '_expense_' . date('Ymd_His') . '.xlsx';

    require_once APPPATH . '../vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle($mode_label . ' Expense');

    // ========== ADD HEADING ROWS ==========
    // Determine the last column letter for merging
    $lastColumn = ($expense_mode == 'indirect') ? 'L' : 'K';
    $lastColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);

    // Heading: Report Title
    $sheet->setCellValue('A1', 'EXPENSE REPORT - ' . strtoupper($mode_label));
    $sheet->mergeCells('A1:' . $lastColumn . '1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Subheading: Generation Date
    $sheet->setCellValue('A2', 'Generated on: ' . date('d-m-Y'));
    $sheet->mergeCells('A2:' . $lastColumn . '2');
    $sheet->getStyle('A2')->getFont()->setItalic(true);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Optional: Filter info (e.g., if expense_id was used)
    if ($expense_id > 0) {
        $sheet->setCellValue('A3', 'Filter: Single Expense ID = ' . $expense_id);
        $sheet->mergeCells('A3:' . $lastColumn . '3');
        $sheet->getStyle('A3')->getFont()->setItalic(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerRow = 4; // Data headers start at row 4
    } else {
        $headerRow = 3; // Data headers start at row 3
    }

    // ========== COLUMN HEADERS (shifted down) ==========
    $headers = array(
        'Sr.No.',
        'Expenditure Category',
        'Bank Voucher Type',
        'Expenditure Type',
        'Employee Name',
        'Paid Date',
        'Month',
        'GST (%)',
        'Basic Amount',
        'Total Amount',
        'Remark',
        'Payment Status'
    );

    // Remove "Expenditure Type" for Direct mode
    if ($expense_mode != 'indirect') {
        unset($headers[3]); // remove Expenditure Type
        $headers = array_values($headers);
    }

    // Apply headers at $headerRow
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col . $headerRow, $h);
        $sheet->getStyle($col . $headerRow)->getFont()->setBold(true);
        $sheet->getStyle($col . $headerRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF3c8dbc');
        $sheet->getStyle($col . $headerRow)->getFont()->getColor()->setARGB('FFFFFFFF');
        $col++;
    }

    // ========== DATA ROWS ==========
    $status_map = array('1' => 'Done', '2' => 'Pending on Date', '3' => 'Advance', '4' => 'Pending Amount');
    $row_num = $headerRow + 1;
    $sr = 1;
    $grand_total = 0;

    foreach ($records as $r) {
        $cat = $r->expense_category ?? '';
        if ($cat && stripos($cat, 'Direct - ') === 0)   $cat = trim(substr($cat, 9));
        if ($cat && stripos($cat, 'Indirect - ') === 0) $cat = trim(substr($cat, 11));
        if ($expense_mode == 'indirect' && preg_match('/^(Individual|Corporate)\s*-\s*(.*)$/i', $cat, $matches)) {
            $cat = trim($matches[2]);
        }
        $status_text = $status_map[$r->status ?? ''] ?? '';
        $grand_total += (float)($r->expense_amount ?? 0);

        $bank_voucher_type = $r->bank_voucher_type ?? '';

        if ($expense_mode == 'indirect') {
            $expense_type_label = $this->get_expense_type_label_from_record($r);
            $sheet->setCellValue('A'.$row_num, $sr);
            $sheet->setCellValue('B'.$row_num, $cat);
            $sheet->setCellValue('C'.$row_num, $bank_voucher_type);
            $sheet->setCellValue('D'.$row_num, $expense_type_label);
            $sheet->setCellValue('E'.$row_num, $r->employee_name ?? '');
            $sheet->setCellValue('F'.$row_num, !empty($r->date) ? date('d-m-Y', strtotime($r->date)) : '');
            $sheet->setCellValue('G'.$row_num, $r->expense_month ?? '');
            $sheet->setCellValue('H'.$row_num, $r->gst_class ?? '');
            $sheet->setCellValue('I'.$row_num, $r->basic_amount ?? 0);
            $sheet->setCellValue('J'.$row_num, $r->expense_amount ?? '');
            $sheet->setCellValue('K'.$row_num, $r->expense_note ?? '');
            $sheet->setCellValue('L'.$row_num, $status_text);
        } else {
            // Direct
            $sheet->setCellValue('A'.$row_num, $sr);
            $sheet->setCellValue('B'.$row_num, $cat);
            $sheet->setCellValue('C'.$row_num, $bank_voucher_type);
            $sheet->setCellValue('D'.$row_num, $r->employee_name ?? '');
            $sheet->setCellValue('E'.$row_num, !empty($r->date) ? date('d-m-Y', strtotime($r->date)) : '');
            $sheet->setCellValue('F'.$row_num, $r->expense_month ?? '');
            $sheet->setCellValue('G'.$row_num, $r->gst_class ?? '');
            $sheet->setCellValue('H'.$row_num, $r->basic_amount ?? 0);
            $sheet->setCellValue('I'.$row_num, $r->expense_amount ?? '');
            $sheet->setCellValue('J'.$row_num, $r->expense_note ?? '');
            $sheet->setCellValue('K'.$row_num, $status_text);
        }

        $row_num++;
        $sr++;
    }

    // Total row
    $total_col = ($expense_mode == 'indirect') ? 'J' : 'I';
    $sheet->setCellValue('A'.$row_num, '');
    $sheet->setCellValue($total_col.$row_num, $grand_total);
    $sheet->getStyle('A'.$row_num.':'.$total_col.$row_num)->getFont()->setBold(true);
    $sheet->getStyle($total_col.$row_num)->getNumberFormat()->setFormatCode('#,##0.00');

    // Auto-size columns (now including heading rows)
    $lastColLetter = ($expense_mode == 'indirect') ? 'L' : 'K';
    foreach (range('A', $lastColLetter) as $c) {
        $sheet->getColumnDimension($c)->setAutoSize(true);
    }

    // Output headers and save
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}

    public function export_expense_pdf()
    {
        $expense_mode = $this->normalize_expense_mode($this->input->get('expense_mode'));
        $expense_id   = (int) $this->input->get('expense_id');
        $settings = $this->login->get_settings($this->user_id);

        if ($expense_id > 0) {
            $row = $this->inventory->get_expense_by_id($expense_id);
            $records = $row ? array((object) $row) : array();
        } else {
            $all = $this->inventory->get_all_expense($this->user_id);
            $records = $this->filter_expense_entries_by_mode($all, $expense_mode);
        }

        $mode_label = ($expense_mode == 'direct') ? 'Direct' : (($expense_mode == 'indirect') ? 'Indirect' : 'Expense');
        $status_map = array('1' => 'Done', '2' => 'Pending on Date', '3' => 'Advance', '4' => 'Pending Amount');
        $is_indirect = ($expense_mode == 'indirect');

        $grand_total = 0;
        $basic_total = 0;
        foreach ($records as $r) {
            $grand_total += (float)($r->expense_amount ?? 0);
            $basic_total += (float)($r->basic_amount ?? 0);
        }

        $escape = function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };

        $format_amount = function ($value) {
            return number_format((float) $value, 2);
        };

        $clean_category = function ($category) use ($is_indirect) {
            $category = trim((string) $category);
            if (stripos($category, 'Direct - ') === 0) {
                $category = trim(substr($category, 9));
            }
            if (stripos($category, 'Indirect - ') === 0) {
                $category = trim(substr($category, 11));
            }
            if ($is_indirect && preg_match('/^(Individual|Corporate)\s*-\s*(.*)$/i', $category, $matches)) {
                $category = trim($matches[2]);
            }
            return $category;
        };

        $company_name = isset($settings['company_name']) ? $settings['company_name'] : '';
        $company_address = isset($settings['address']) ? $settings['address'] : '';
        $company_mobile = isset($settings['mobile']) ? $settings['mobile'] : '';
        $company_email = isset($settings['email']) ? $settings['email'] : '';
        $company_gst = isset($settings['company_gst']) ? $settings['company_gst'] : '';

        $html  = '<style>';
        $html .= 'body{font-family:DejaVuSans,sans-serif;font-size:9px;color:#222;}';
        $html .= '.report-wrap{border:1px solid #222;}';
        $html .= '.company-table{width:100%;border-collapse:collapse;}';
        $html .= '.company-table td{border-bottom:1px solid #222;padding:7px 8px;vertical-align:top;}';
        $html .= '.company-name{font-size:17px;font-weight:bold;color:#003366;text-transform:uppercase;margin-bottom:3px;}';
        $html .= '.title{font-size:15px;font-weight:bold;text-align:center;color:#003366;letter-spacing:.5px;padding:6px;border-bottom:1px solid #222;background:#f0f0f0;text-transform:uppercase;}';
        $html .= '.section-heading{font-weight:bold;color:#003366;font-size:10px;margin-bottom:3px;text-transform:uppercase;}';
        $html .= '.label{font-weight:bold;color:#444;display:inline-block;min-width:90px;}';
        $html .= '.summary-table{width:100%;border-collapse:collapse;}';
        $html .= '.summary-table td{border-bottom:1px solid #222;border-right:1px solid #222;padding:5px 7px;}';
        $html .= '.summary-table td:last-child{border-right:none;}';
        $html .= '.summary-value{font-size:12px;font-weight:bold;color:#003366;}';
        $html .= '.expense-table{width:100%;border-collapse:collapse;}';
        $html .= '.expense-table th{background:#3c8dbc;color:#fff;border:1px solid #222;padding:5px 4px;text-align:center;font-size:8.5px;}';
        $html .= '.expense-table td{border:1px solid #222;padding:4px 4px;vertical-align:top;font-size:8.5px;}';
        $html .= '.expense-table tr:nth-child(even) td{background:#f7f9fb;}';
        $html .= '.num{text-align:right;white-space:nowrap;}';
        $html .= '.center{text-align:center;}';
        $html .= '.remark{word-wrap:break-word;}';
        $html .= '.total-row td{font-weight:bold;background:#eaf4fb;border-top:2px solid #3c8dbc;}';
        $html .= '.status{font-weight:bold;}';
        $html .= '.status-done{color:#1e8449;} .status-pending{color:#d35400;} .status-advance{color:#21618c;} .status-pending-amount{color:#b03a2e;}';
        $html .= '.no-data{text-align:center;color:#777;padding:15px;}';
        $html .= '</style>';

        $html .= '<div class="report-wrap">';
        $html .= '<table class="company-table"><tr>';
        $html .= '<td width="62%"><div class="company-name">' . $escape($company_name) . '</div>';
        if ($company_address !== '') $html .= '<div><b>Address:</b> ' . $escape($company_address) . '</div>';
        if ($company_mobile !== '') $html .= '<div><b>Mobile:</b> ' . $escape($company_mobile) . '</div>';
        if ($company_email !== '') $html .= '<div><b>Email:</b> ' . $escape($company_email) . '</div>';
        if ($company_gst !== '') $html .= '<div><b>GST:</b> ' . $escape($company_gst) . '</div>';
        $html .= '</td>';
        $html .= '<td width="38%" style="text-align:right;"><div class="section-heading">Report Details</div>';
        $html .= '<div><span class="label">Report:</span> ' . $escape($mode_label) . ' Expenditure</div>';
        $html .= '<div><span class="label">Generated:</span> ' . date('d-m-Y h:i A') . '</div>';
        $html .= '<div><span class="label">Records:</span> ' . count($records) . '</div>';
        if ($expense_id > 0) $html .= '<div><span class="label">Expense ID:</span> ' . (int) $expense_id . '</div>';
        $html .= '</td></tr></table>';

        $html .= '<div class="title">' . $escape($mode_label) . ' Expenditure Report</div>';
        $html .= '<table class="summary-table"><tr>';
        $html .= '<td width="33%"><div>Total Records</div><div class="summary-value">' . count($records) . '</div></td>';
        $html .= '<td width="33%"><div>Total Basic Amount</div><div class="summary-value">' . $format_amount($basic_total) . '</div></td>';
        $html .= '<td width="34%"><div>Total Amount</div><div class="summary-value">' . $format_amount($grand_total) . '</div></td>';
        $html .= '</tr></table>';

        $html .= '<table class="expense-table"><thead><tr>';
        $html .= '<th width="4%">Sr.</th><th width="15%">Expenditure Category</th><th width="9%">Bank Voucher Type</th>';
        if ($is_indirect) $html .= '<th width="8%">Expenditure Type</th>';
        $html .= '<th width="11%">Employee Name</th><th width="8%">Paid Date</th><th width="8%">Month</th><th width="6%">GST (%)</th><th width="9%">Basic Amount</th><th width="9%">Total Amount</th><th>Remark</th><th width="8%">Status</th>';
        $html .= '</tr></thead><tbody>';

        if (empty($records)) {
            $html .= '<tr><td class="no-data" colspan="' . ($is_indirect ? '12' : '11') . '">No expense records found.</td></tr>';
        } else {
            $sr = 1;
            foreach ($records as $r) {
                $cat = $clean_category($r->expense_category ?? '');
                $status_key = (string)($r->status ?? '');
                $status_text = $status_map[$status_key] ?? '';
                $status_class = 'status-pending-amount';
                if ($status_key === '1') {
                    $status_class = 'status-done';
                } elseif ($status_key === '2') {
                    $status_class = 'status-pending';
                } elseif ($status_key === '3') {
                    $status_class = 'status-advance';
                }

                $html .= '<tr>';
                $html .= '<td class="center">' . $sr . '</td>';
                $html .= '<td>' . $escape($cat) . '</td>';
                $html .= '<td>' . $escape($r->bank_voucher_type ?? '') . '</td>';
                if ($is_indirect) $html .= '<td>' . $escape($this->get_expense_type_label_from_record($r)) . '</td>';
                $html .= '<td>' . $escape($r->employee_name ?? '') . '</td>';
                $html .= '<td class="center">' . (!empty($r->date) ? date('d-m-Y', strtotime($r->date)) : '') . '</td>';
                $html .= '<td class="center">' . $escape($r->expense_month ?? '') . '</td>';
                $html .= '<td class="center">' . $escape($r->gst_class ?? '') . '</td>';
                $html .= '<td class="num">' . $format_amount($r->basic_amount ?? 0) . '</td>';
                $html .= '<td class="num">' . $format_amount($r->expense_amount ?? 0) . '</td>';
                $html .= '<td class="remark">' . nl2br($escape($r->expense_note ?? '')) . '</td>';
                $html .= '<td class="status ' . $status_class . '">' . $escape($status_text) . '</td>';
                $html .= '</tr>';
                $sr++;
            }
        }

        $html .= '<tr class="total-row">';
        $html .= '<td colspan="' . ($is_indirect ? '8' : '7') . '" class="num">Grand Total</td>';
        $html .= '<td class="num">' . $format_amount($basic_total) . '</td>';
        $html .= '<td class="num">' . $format_amount($grand_total) . '</td>';
        $html .= '<td colspan="2"></td>';
        $html .= '</tr>';
        $html .= '</tbody></table></div>';

        require_once APPPATH . '../vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4-L',
            'margin_left'   => 8,
            'margin_right'  => 8,
            'margin_top'    => 12,
            'margin_bottom' => 12,
            'default_font'  => 'dejavusans',
        ]);
        $mpdf->SetHTMLHeader('<div style="text-align:center;font-size:9px;color:#888;border-bottom:1px solid #ccc;padding-bottom:3px;">' . htmlspecialchars($mode_label) . ' Expenditure Report — ' . date('d-m-Y') . '</div>');
        $mpdf->SetHTMLHeader('');
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:8px;color:#777;border-top:1px solid #ccc;padding-top:3px;">This is a system generated expenditure report | Page {PAGENO} of {nbpg}</div>');
        $mpdf->WriteHTML($html);
        $mpdf->Output(strtolower($mode_label) . '_expense_' . date('Ymd_His') . '.pdf', 'D');
        exit;
    }
}
