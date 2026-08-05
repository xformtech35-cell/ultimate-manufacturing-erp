<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        
        $class = $this->router->fetch_class();
        $method = $this->router->fetch_method();
        
        // Global Financial Year handling
        $fy_get = $this->input->get('fy');
        if ($fy_get !== null && $fy_get !== '') {
            $this->session->set_userdata('fy_year', (int)$fy_get);
        }

        // Classes that are exempt from login check
        $exempt_classes = array('LoginController', 'Welcome');
        
        if (!in_array($class, $exempt_classes)) {
            $session_data_head = $this->session->userdata('session_data_head');
            $user_id = $session_data_head['result']['user_id'] ?? null;
            
            // Allow public quote download in Download controller
            if ($class === 'Download' && $method === 'public_quote') {
                return;
            }
            
            if (empty($user_id)) {
                // Ensure session is destroyed and redirect to logout
                $this->session->sess_destroy();
                redirect('LoginController/logout');
                exit;
            }
        }

        // Auto-log POST activity and Deletion events
        $route = $class . '/' . $method;
        $is_post = ($this->input->method(TRUE) === 'POST');
        $is_delete_method = (stripos($method, 'delete') !== false);

        if ($is_post || $is_delete_method) {
            $log_map = array(
                // Sales Orders
                'SalesOrderController/add_salesorder_salesorder' => 'Created Sales Order',
                'SalesOrderController/edit_salesorder_salesorder' => 'Updated Sales Order',
                'SalesOrderController/delete_salesorder_by_quote_number' => 'Deleted Sales Order',
                'SalesOrderController/delete_non_gst_salesorder_by_quote_number' => 'Deleted Non-GST Sales Order',
                
                // Quotations & Estimates
                'EstimateController/add_estimate_quotation' => 'Created Quotation',
                'EstimateController/edit_estimate_quotation' => 'Updated Quotation',
                'EstimateController/delete_quotation_by_quote_number' => 'Deleted Quotation',
                'EstimateController/delete_non_gst_quotation_by_quote_number' => 'Deleted Non-GST Quotation',
                'EstimateController/convert_to_invoice' => 'Converted Quotation to Invoice',
                'EstimateController/convert_to_invoice_non_gst_data' => 'Converted Non-GST Quotation to Invoice',
                'EstimateController/convert_to_sales_order' => 'Converted Quotation to Sales Order',
                'EstimateController/duplicate_quote' => 'Duplicated Quotation',
                'EstimateController/send_quotation_email' => 'Sent Quotation Email',

                // Suppliers & PO
                'SupplierController/add_supplier' => 'Created Supplier Master',
                'SupplierController/edit_supplier' => 'Updated Supplier Master',
                'SupplierController/delete_supplier_by_id' => 'Deleted Supplier Master',
                'SupplierController/add_purchase_order' => 'Created Purchase Order',
                'SupplierController/edit_purchase_order' => 'Updated Purchase Order',
                'SupplierController/delete_po_by_po_number' => 'Deleted Purchase Order',
                'SupplierController/process_po_approval' => 'PO Approval Process',
                'SupplierController/send_po_email' => 'Sent PO Email',
                
                // Purchase Requisitions
                'RequisitionController/add_requisition_order' => 'Created Purchase Requisition',
                'RequisitionController/edit_requisition_order' => 'Updated Purchase Requisition',
                'RequisitionController/delete_requisition_order' => 'Deleted Purchase Requisition',
                'RequisitionController/process_approval' => 'PR Approval Process',
                
                // BOM
                'BomController/add_bom' => 'Created BOM (Bill of Materials)',
                'BomController/edit_bom' => 'Updated BOM (Bill of Materials)',
                'BomController/delete_bom_by_id' => 'Deleted BOM',
                'BomController/submit_for_approval' => 'Submitted BOM for Approval',
                'BomController/process_approval' => 'BOM Approval Process',
                
                // Job Order
                'JobOrderController/add_joborder' => 'Created Job Order',
                'JobOrderController/edit_joborder' => 'Updated Job Order',
                'JobOrderController/delete_joborder_by_quote_number' => 'Deleted Job Order',
                
                // Inventory
                'InventoryController/add_inventory' => 'Created Inventory Item',
                'InventoryController/edit_inventory' => 'Updated Inventory Item',
                'InventoryController/delete_inventory_by_id' => 'Deleted Inventory Item',
                'InventoryController/increase_inventory_stock' => 'Increased Stock Level',
                'InventoryController/ajax_add_inventory' => 'Created Inventory (AJAX)',
                
                // RFQ
                'RFQController/save_rfq' => 'Created RFQ',
                'RFQController/saveQuotation' => 'Saved Supplier Quotation',
                'RFQController/deleteQuotation' => 'Deleted Supplier Quotation',
                'RFQController/send_rfq_emails' => 'Sent RFQ Emails',
                
                // Customers
                'CustomerController/add_customer' => 'Created Customer Master',
                'CustomerController/edit_customer' => 'Updated Customer Master',
                'CustomerController/delete_customer_by_id' => 'Deleted Customer Master',
                
                // Drawing
                'DrawingController/add_drawing' => 'Created Drawing',
                'DrawingController/update_drawing' => 'Updated Drawing',
                'DrawingController/delete_drawing' => 'Deleted Drawing',
                'DrawingController/save_revision' => 'Saved Drawing Revision',
                'DrawingController/delete_revision' => 'Deleted Drawing Revision',
                
                // Invoices
                'InvoiceController/add_invoice' => 'Created Tax Invoice',
                'InvoiceController/edit_invoice' => 'Updated Tax Invoice',
                'InvoiceController/delete_invoice_by_quote_number' => 'Deleted Tax Invoice',
                
                // Proforma Invoice
                'ProformaInvoiceController/add_proforma_invoice' => 'Created Proforma Invoice',
                'ProformaInvoiceController/edit_proforma_invoice' => 'Updated Proforma Invoice',
                'ProformaInvoiceController/delete_proformainvoice_by_quote_number' => 'Deleted Proforma Invoice',
                
                // Delivery Challan
                'DeliveryChallanController/add_delivery_challan' => 'Created Delivery Challan',
                'DeliveryChallanController/edit_delivery_challan' => 'Updated Delivery Challan',
                'DeliveryChallanController/delete_delivery_challan_by_quote_number' => 'Deleted Delivery Challan',
                
                // Credit Notes
                'CreditnoteController/add_credit_note' => 'Created Credit Note',
                'CreditnoteController/delete_creditnote_by_quote_number' => 'Deleted Credit Note',
                
                // Material Issues / Stock Issues / Stock Allocations
                'MaterialIssueController/add_material_issue' => 'Issued Stock',
                'MaterialIssueController/add_material_allocation' => 'Allocated Stock',
                'MaterialIssueController/add_material_reversal' => 'Reversed Stock Allocation',
                'MaterialIssueController/save_verification' => 'Saved Stock Verification',
                
                // Users & Roles
                'UserController/add_user' => 'Created User Profile',
                'UserController/edit_user' => 'Updated User Profile',
                'UserController/delete_user_by_id' => 'Deleted User Profile',
                'RoleController/add_group' => 'Created Access Role Group',
                'RoleController/edit_group' => 'Updated Access Role Group',
                'RoleController/delete_group_by_id' => 'Deleted Access Role Group',
                'RoleController/permission_save' => 'Updated Role Permissions',
                'RollController/add_group' => 'Created Access Role Group',
                'RollController/edit_group' => 'Updated Access Role Group',
                'RollController/delete_group_by_id' => 'Deleted Access Role Group',

                // GRN
                'GrnController/add_grn' => 'Created GRN',
                'GrnController/update_grn_data' => 'Updated GRN',
                'GrnController/delete_grn_by_grn_number' => 'Deleted GRN',
                'GrnController/save_inspection' => 'Conducted GRN Inspection',
                'GrnController/process_grn_approval' => 'GRN Approval Process',
                'GrnController/approve_all_grn_items' => 'Approved All GRN Items',
                'GrnController/send_grn_email' => 'Sent GRN Email',

                // Projects
                'ProjectController/add_project' => 'Created Project',
                'ProjectController/update_project' => 'Updated Project',
                'ProjectController/delete_project' => 'Deleted Project',

                // Approval Matrix
                'ApprovalMatrixController/add' => 'Created Approval Matrix Entry',
                'ApprovalMatrixController/edit' => 'Updated Approval Matrix Entry',
                'ApprovalMatrixController/delete' => 'Deleted Approval Matrix Entry',

                // Planning
                'PlanningController/add_delivered_item' => 'Recorded Raw Material Delivery',
                'PlanningController/delete_raw_item_by_id' => 'Deleted Raw Material Delivery',
                'PlanningController/add_finished_product' => 'Recorded Finished Product Production',
                'PlanningController/delete_product_by_id' => 'Deleted Finished Product Production',

                // Order Confirmation
                'OrderConfirmationController/save_order_confirmation' => 'Created Order Confirmation',
                'OrderConfirmationController/update_order_confirmation' => 'Updated Order Confirmation',
                'OrderConfirmationController/delete_order_confirmation' => 'Deleted Order Confirmation',
                'OrderConfirmationController/update_status' => 'Updated Order Confirmation Status',

                // PO Amendment
                'PoamendmentController/create' => 'Created PO Amendment',
                'PoamendmentController/edit' => 'Updated PO Amendment',
                'PoamendmentController/delete' => 'Deleted PO Amendment',
                'PoamendmentController/submit_approval' => 'Submitted PO Amendment for Approval',
                'PoamendmentController/vendor_acknowledge' => 'Vendor Acknowledged PO Amendment',
                'PoamendmentController/update_revised_po' => 'Updated Revised PO',
                'PoamendmentController/process_amendment_approval' => 'PO Amendment Approval Process',

                // Advance
                'AdvanceController/add_advance' => 'Recorded Advance Payment',
                'AdvanceController/edit_advance' => 'Updated Advance Payment',
                'AdvanceController/delete_advance_by_id' => 'Deleted Advance Payment',

                // Asset
                'AssetController/add_asset' => 'Created Asset',
                'AssetController/delete_asset_by_id' => 'Deleted Asset',
                'AssetController/add_asset_sub_category' => 'Created Asset Sub-Category',
                'AssetController/delete_asset_sub_category_by_id' => 'Deleted Asset Sub-Category',

                // Bank Details
                'BankdetailController/add_bank_detail' => 'Created Bank Detail',
                'BankdetailController/edit_bank_detail' => 'Updated Bank Detail',
                'BankdetailController/delete_bank_detail_by_id' => 'Deleted Bank Detail',
                'BankdetailController/add_banktransaction_detail' => 'Recorded Bank Transaction',
                'BankdetailController/edit_bank_transaction' => 'Updated Bank Transaction',
                'BankdetailController/delete_bank_transaction_by_id' => 'Deleted Bank Transaction',
                'BankdetailController/add_loan' => 'Recorded Loan',
                'BankdetailController/edit_loan' => 'Updated Loan',
                'BankdetailController/delete_loan_by_id' => 'Deleted Loan',

                // Location
                'LocationController/add_location' => 'Created Location',
                'LocationController/update_location' => 'Updated Location',
                'LocationController/delete_location_by_id' => 'Deleted Location',

                // Department
                'DepartmentController/add_department' => 'Created Department',
                'DepartmentController/delete_department_by_id' => 'Deleted Department',

                // Cheque
                'ChequeController/add_cheque_detail' => 'Created Cheque Detail',
                'ChequeController/edit_cheque_detail' => 'Updated Cheque Detail',
                'ChequeController/delete_cheque_detail_by_id' => 'Deleted Cheque Detail',

                // Liabilities
                'LiabilitiesController/add_liabilities' => 'Created Liabilities',
                'LiabilitiesController/add_subLiabilities' => 'Created Sub-Liabilities',
                'LiabilitiesController/delete_liabilities_by_id' => 'Deleted Liabilities',
                'LiabilitiesController/delete_subliabilities_by_id' => 'Deleted Sub-Liabilities',

                // MOC
                'MocController/add_moc' => 'Created MOC',
                'MocController/delete_moc_by_id' => 'Deleted MOC',

                // Sales Return
                'SalesReturnController/add_sales_return' => 'Created Sales Return',
                'SalesReturnController/edit_sales_return' => 'Updated Sales Return',
                'SalesReturnController/delete_sales_return_by_po_return_number' => 'Deleted Sales Return',
                'SalesReturnController/send_sales_return_email' => 'Sent Sales Return Email',
                'SalesReturnController/delete_sales_return_item' => 'Deleted Sales Return Item',

                // Item Category & Groups
                'ItemCategoryController/add_item_category' => 'Created Item Category',
                'ItemCategoryController/delete_category' => 'Deleted Item Category',
                'ItemGroupController/add_item_group' => 'Created Item Group',
                'ItemGroupController/delete_group' => 'Deleted Item Group',
                'UnitController/add_unit' => 'Created Unit',
                'UnitController/delete_unit_by_id' => 'Deleted Unit',
                'GstController/add_gst_class' => 'Created GST Class',
                'GstController/delete_gst_class_by_id' => 'Deleted GST Class'
            );

            if (isset($log_map[$route])) {
                $action_desc = $log_map[$route];
                
                // Specific dynamic additions based on post action types
                if ($route === 'RequisitionController/process_approval') {
                    $act_val = $_POST['action'] ?? 'Processed';
                    $action_desc = "PR " . $act_val;
                } elseif ($route === 'SupplierController/process_po_approval') {
                    $act_val = $_POST['action'] ?? 'Processed';
                    $action_desc = "PO " . ucfirst($act_val);
                } elseif ($route === 'BomController/process_approval') {
                    $act_val = $_POST['action'] ?? 'Processed';
                    $action_desc = "BOM " . ucfirst($act_val);
                } elseif ($route === 'GrnController/process_grn_approval') {
                    $act_val = $_POST['action'] ?? 'Processed';
                    $action_desc = "GRN " . ucfirst($act_val);
                } elseif ($route === 'PoamendmentController/process_amendment_approval') {
                    $act_val = $_POST['action'] ?? 'Processed';
                    $action_desc = "PO Amendment " . ucfirst($act_val);
                }
                
                $identifier = '';
                // Check POST data first for document numbers/names
                $possible_keys = array('number_fk', 'number', 'invoice_number', 'pr_no', 'grn_number', 'company_name', 'customer_name', 'username', 'fullname', 'po_number', 'drawing_no', 'drawing_name', 'rfq_id', 'group_name', 'project_name', 'project_code', 'asset_name', 'bank_name', 'location_name', 'item_code', 'item_name');
                foreach ($possible_keys as $key) {
                    if (!empty($_POST[$key])) {
                        if (is_array($_POST[$key])) {
                            continue;
                        }
                        $identifier = ' - ' . htmlspecialchars($_POST[$key]);
                        break;
                    }
                }

                // Get record_id from segment 3 or POST
                $record_id = NULL;
                $seg3 = $this->uri->segment(3);
                if (is_numeric($seg3)) {
                    $record_id = intval($seg3);
                    if (empty($identifier)) {
                        $identifier = ' [ID: ' . $record_id . ']';
                    }
                }
                
                if (empty($record_id) && $is_post) {
                    $id_keys = array('id', 'project_id', 'po_id', 'pr_id', 'bom_id', 'user_id', 'customer_id', 'supplier_id', 'drawing_id', 'invoice_id', 'rfq_id', 'grn_id', 'joborder_id', 'role_id', 'group_id');
                    foreach ($id_keys as $k) {
                        if (!empty($_POST[$k]) && is_numeric($_POST[$k])) {
                            $record_id = intval($_POST[$k]);
                            break;
                        }
                    }
                }

                // If identifier is still empty, let's look at the URI segment
                if (empty($identifier) && !empty($seg3)) {
                    $identifier = ' - ' . htmlspecialchars($seg3);
                }

                // Prepare new values JSON if POST
                $new_values = NULL;
                if ($is_post && !empty($_POST)) {
                    $post_data = $_POST;
                    $sensitive_keys = array('password', 'new_password', 'confirm_password', 'old_password', 'pwd');
                    foreach ($sensitive_keys as $skey) {
                        if (isset($post_data[$skey])) {
                            $post_data[$skey] = '********';
                        }
                    }
                    $new_values = json_encode($post_data);
                }

                $this->log_activity($action_desc . $identifier, $class, $record_id, NULL, $new_values);
            }
        }
    }

    public function log_activity($action, $table_name = NULL, $record_id = NULL, $old_values = NULL, $new_values = NULL) {
        $session_data_head = $this->session->userdata('session_data_head');
        $user_id = $session_data_head['result']['user_id'] ?? null;
        
        $data = array(
            'user_id' => $user_id ?: 0,
            'action' => $action !== null ? $action : '',
            'table_name' => $table_name !== null ? $table_name : 'General',
            'record_id' => $record_id !== null ? $record_id : 0,
            'old_values' => !empty($old_values) ? (is_array($old_values) ? json_encode($old_values) : $old_values) : NULL,
            'new_values' => !empty($new_values) ? (is_array($new_values) ? json_encode($new_values) : $new_values) : NULL,
            'ip_address' => (function() {
                $ip = $this->input->ip_address() ?: '127.0.0.1';
                // Normalize IPv6 loopback variants to human-readable 127.0.0.1
                if ($ip === '::1' || $ip === '::ffff:127.0.0.1' || $ip === '0:0:0:0:0:0:0:1') {
                    $ip = '127.0.0.1 (localhost)';
                }
                return $ip;
            })(),
            'user_agent' => $this->input->user_agent() ?: 'System',
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $orig_prefix = $this->db->dbprefix;
        $this->db->dbprefix = '';
        $this->db->insert('sameeppayroll_audit_trail', $data);
        $this->db->dbprefix = $orig_prefix;
    }
}
