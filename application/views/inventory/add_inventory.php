<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') or exit('No direct script access allowed');

$role_name = strtolower($session_data_head1['result']['role_name'] ?? '');
$role_id   = (int)($session_data_head1['result']['role_id'] ?? $session_data_head1['result']['user_role_id'] ?? 0);
$user_id   = (int)($session_data_head1['result']['user_id'] ?? 0);
$is_admin  = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);
?>

<style>
    /* Enhanced UI Styles */
    :root {
        --primary-blue: #3498db;
        --primary-dark-blue: #2980b9;
        --success-green: #27ae60;
        --warning-orange: #f39c12;
        --danger-red: #e74c3c;
        --info-teal: #17a2b8;
    }

    .enhanced-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .enhanced-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .table-enhance {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .table-enhance thead {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark-blue) 100%);
    }

    .table-enhance thead th {
        color: white;
        font-weight: 600;
        padding: 16px 12px;
        border: none;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .table-enhance tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .table-enhance tbody tr:hover {
        background-color: #f8fafc;
    }

    .table-enhance tbody td {
        padding: 14px 12px;
        vertical-align: middle;
        border: none;
        color: #495057;
        font-size: 14px;
    }

    /* Badge Styles */
    .badge-type {
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-boughtout {
        background-color: #e3f2fd;
        color: #1565c0;
        border: 1px solid #bbdefb;
    }

    .badge-manufacturing {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    /* Action Buttons */
    .btn-action-group {
        display: flex;
        gap: 6px;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-action-edit:hover {
        background-color: var(--primary-blue);
        color: white;
        border-color: var(--primary-blue);
    }

    .btn-action-delete:hover {
        background-color: var(--danger-red);
        color: white;
        border-color: var(--danger-red);
    }

    /* Modal Enhancement - keeping for reference but modal now in footer */
    .enhanced-card {
        border-radius: 15px;
        overflow: hidden;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s;
        padding: 10px 15px;
        height: auto;
    }

    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    /* Flash Messages */
    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        border-left: 4px solid var(--success-green);
    }

    .alert-info {
        border-left: 4px solid var(--info-teal);
    }

    /* Price Styling */
    .price-cost {
        color: #e74c3c;
        font-weight: 600;
    }

    .price-sell {
        color: #27ae60;
        font-weight: 600;
    }

    /* Label Styling */
    .control-label {
        font-weight: 600;
        color: #555;
    }

    /* Required field indicator */
    .required-star {
        color: #e74c3c;
        margin-left: 2px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-action-group {
            flex-direction: column;
            gap: 4px;
        }

        .btn-action {
            width: 100%;
        }
    }

    /* Stock warning */
    .stock-warning {
        color: #e74c3c;
        font-weight: bold;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }

    /* Button styles */
    .btn-add {
        background: linear-gradient(135deg, var(--success-green) 0%, #229954 100%);
        border: none;
        border-radius: 6px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
    }

    /* Modal dialog size */
    .modal-dialog {
        max-width: 800px;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <i class="fa fa-cubes"></i> Inventory Management
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#"><i class="fa fa-shopping-bag"></i> Inventory</a></li>
                    <li class="active">Inventory Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info enhanced-card">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-list-alt"></i> Inventory Details
                                </h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-info" style="margin-left: 5px;"
                                        onclick="window.location.href='<?php echo base_url('InventoryController/import_inventory_view'); ?>'">
                                        <i class="fa fa-upload"></i> Import Inventory
                                    </button>

                                    <button class="btn btn-success" style="margin-left: 5px;"
                                        onclick="window.location.href='<?php echo base_url('ReportController/get_inventory_report'); ?>'">
                                        <i class="fa fa-download"></i> Export Inventory
                                    </button>

                                    <button class="btn btn-danger" style="margin-left: 5px;"
                                        onclick="window.location.href='<?php echo base_url('InventoryController/export_inventory_pdf'); ?>'">
                                        <i class="fa fa-file-pdf-o"></i> Export PDF
                                    </button>

                                    <!-- Updated button to call modal from footer -->
                                    <button class="btn btn-primary" style="margin-left: 5px;" onclick="openProductModal()">
                                        <i class="fa fa-plus"></i> Add New Item
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_inventory" data-toggle="tab"><i class="fa fa-cubes"></i> Inventory List</a></li>
                                        <li><a href="#tab_approved_deletions" data-toggle="tab"><i class="fa fa-check-circle text-success"></i> Approved Deletions <span class="label label-success"><?= count($approved_deletions); ?></span></a></li>
                                        <li><a href="#tab_deletion_history" data-toggle="tab"><i class="fa fa-history"></i> Deletion History <span class="label label-info"><?= count($deletion_history); ?></span></a></li>
                                    </ul>
                                    <div class="tab-content" style="padding: 15px 0 0 0;">
                                        <!-- Tab 1: Inventory List -->
                                        <div class="tab-pane active" id="tab_inventory">
                                            <div class="table-responsive">
                                                <table id="add_inventory" class="table table-enhance table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Item Code</th>
                                                            <th>Item Name</th>
                                                            <th>Description</th>
                                                            <th>Category</th>
                                                            <th>Group</th>
                                                            <th>Company Name</th>
                                                            <th>HSN/SAC</th>
                                                            <th>Unit</th>
                                                            <th>Stock</th>
                                                            <th>GST%</th>
                                                            <th>Type</th>
                                                            <th>Packing</th>
                                                            <th>Cost Price</th>
                                                            <th>Sell Price</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php $i = 1; ?>
                                                        <?php foreach ($result as $key): ?>
                                                            <?php
                                                            $type_class = $key->item_type == 'B' ? 'badge-boughtout' : 'badge-manufacturing';
                                                            $type_text = $key->item_type == 'B' ? 'Boughtout' : 'Manufacturing';
                                                            ?>
                                                            <tr>
                                                                <td><?= $i; ?></td>

                                                                <td>
                                                                    <strong><?= $key->code; ?></strong>
                                                                    <?php if ($key->stock <= 5): ?>
                                                                        <br><small class="stock-warning"><i class="fa fa-exclamation-triangle"></i> Low Stock</small>
                                                                    <?php endif; ?>
                                                                </td>

                                                                <td><?= $key->item_name; ?></td>
                                                                <td><?= $key->prod_description; ?></td>
                                                                <td><?= !empty($key->category_name) ? $key->category_name : 'N/A'; ?></td>
                                                                <td><?= !empty($key->group_name) ? $key->group_name : 'N/A'; ?></td>
                                                                <td><?= !empty($key->supplier_name) ? $key->supplier_name : 'N/A'; ?></td>
                                                                <td><?= !empty($key->hsn) ? $key->hsn : 'N/A'; ?></td>

                                                                <td>
                                                                    <span class="label label-default"><?= $key->unit; ?></span>
                                                                </td>
                                                                <td><?= $key->stock; ?></td>

                                                                <td>
                                                                    <span class="label label-primary"><?= $key->gst_per; ?></span>
                                                                </td>

                                                                <td>
                                                                    <span class="badge-type <?= $type_class; ?>">
                                                                        <?= $type_text; ?>
                                                                    </span>
                                                                </td>
                                                                <td><?= isset($key->packing) ? $key->packing : 'N/A'; ?></td>

                                                                <td class="price-cost">
                                                                    ₹<?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->cost_price), 0); ?>
                                                                </td>

                                                                <td class="price-sell">
                                                                    ₹<?= indian_number_format(round($key->sell_price), 0); ?>
                                                                </td>

                                                                <td class="text-center">
                                                                     <div class="btn-group">
                                                                         <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 10px; font-weight: 600; font-size: 12px; border-radius: 4px; border: 1px solid #ccc; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                                                                             Action <span class="caret" style="margin-left: 3px;"></span>
                                                                         </button>
                                                                         <ul class="dropdown-menu dropdown-menu-right text-left" style="min-width: 170px; box-shadow: 0 6px 18px rgba(0,0,0,0.15); border-radius: 6px; padding: 6px 0; margin-top: 4px;">
                                                                             <li>
                                                                                 <a href="<?= base_url('InventoryController/get_inventory_by_id/' . $key->inventory_id); ?>" style="padding: 6px 15px; font-size: 13px;">
                                                                                     <i class="fa fa-pencil-square text-primary" style="margin-right: 8px; width: 16px;"></i> Edit Item
                                                                                 </a>
                                                                             </li>
                                                                             <li>
                                                                                 <a href="<?= base_url('MaterialIssueController/stock_ledger/' . $key->inventory_id); ?>" style="padding: 6px 15px; font-size: 13px;">
                                                                                     <i class="fa fa-history text-info" style="margin-right: 8px; width: 16px;"></i> Stock History
                                                                                 </a>
                                                                             </li>
                                                                             <li role="separator" class="divider" style="margin: 4px 0;"></li>
                                                                             <li>
                                                                                 <?php if ($is_admin): ?>
                                                                                     <a href="<?= base_url('InventoryController/delete_inventory_by_id/' . $key->inventory_id); ?>"
                                                                                        onclick="return confirm('Are you sure you want to delete this item?')"
                                                                                        style="padding: 6px 15px; font-size: 13px; color: #d9534f;">
                                                                                         <i class="fa fa-trash text-danger" style="margin-right: 8px; width: 16px;"></i> Delete Item
                                                                                     </a>
                                                                                 <?php else: ?>
                                                                                     <a href="javascript:void(0);"
                                                                                        onclick="openDeleteRequestModal('<?= $key->inventory_id; ?>', '<?= htmlspecialchars($key->code, ENT_QUOTES); ?>', 'inventory')"
                                                                                        style="padding: 6px 15px; font-size: 13px; color: #d9534f;">
                                                                                         <i class="fa fa-trash text-danger" style="margin-right: 8px; width: 16px;"></i> Delete Item
                                                                                     </a>
                                                                                 <?php endif; ?>
                                                                             </li>
                                                                         </ul>
                                                                     </div>
                                                                </td>
                                                            </tr>
                                                            <?php $i++; ?>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab 2: Approved Deletions -->
                                        <div class="tab-pane" id="tab_approved_deletions">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" style="margin-bottom:0;">
                                                    <thead>
                                                        <tr style="background:#f4f4f4;">
                                                            <th style="width:40px;">#</th>
                                                            <th>Item Code</th>
                                                            <th>Item Name</th>
                                                            <th>Reason for Deletion</th>
                                                            <th>Approved At</th>
                                                            <th style="width:150px;text-align:center;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (empty($approved_deletions)): ?>
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted" style="padding:20px;">
                                                                    <i class="fa fa-check-circle text-success" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                                                                    No approved deletion requests pending execution.
                                                                </td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach ($approved_deletions as $idx => $row): ?>
                                                                <tr>
                                                                    <td><?= $idx + 1; ?></td>
                                                                    <td><strong><?= htmlspecialchars($row['item_code']); ?></strong></td>
                                                                    <td><?= htmlspecialchars($row['item_name']); ?></td>
                                                                    <td><?= htmlspecialchars($row['reason']); ?></td>
                                                                    <td><?= date('d M Y, h:i A', strtotime($row['updated_at'])); ?></td>
                                                                    <td class="text-center">
                                                                        <a href="<?= base_url('DeleteApprovalController/execute_delete/' . $row['id']); ?>" 
                                                                           class="btn btn-xs btn-danger"
                                                                           onclick="return confirm('Delete [<?= htmlspecialchars($row['item_code']); ?>] permanently? This CANNOT be undone.');"
                                                                           style="font-weight:600;padding:4px 10px;">
                                                                            <i class="fa fa-trash"></i> Delete Permanently
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab 3: Deletion History -->
                                        <div class="tab-pane" id="tab_deletion_history">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" style="margin-bottom:0;">
                                                    <thead>
                                                        <tr style="background:#f4f4f4;">
                                                            <th style="width:40px;">#</th>
                                                            <th>Item Code</th>
                                                            <th>Item Name</th>
                                                            <th>Status</th>
                                                            <th>Remarks</th>
                                                            <th>Requested At</th>
                                                            <th>Reviewed At</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (empty($deletion_history)): ?>
                                                            <tr>
                                                                <td colspan="7" class="text-center text-muted" style="padding:20px;">No deletion requests found.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach ($deletion_history as $idx => $row): ?>
                                                                <tr>
                                                                    <td><?= $idx + 1; ?></td>
                                                                    <td><strong><?= htmlspecialchars($row['item_code']); ?></strong></td>
                                                                    <td><?= htmlspecialchars($row['item_name']); ?></td>
                                                                    <td>
                                                                        <?php if ($row['status'] === 'pending'): ?>
                                                                            <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                                                        <?php elseif ($row['status'] === 'approved'): ?>
                                                                            <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                                                                        <?php elseif ($row['status'] === 'deleted'): ?>
                                                                            <span class="label label-default"><i class="fa fa-trash"></i> Deleted</span>
                                                                        <?php else: ?>
                                                                            <span class="label label-danger"><i class="fa fa-times"></i> Rejected</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td><?= !empty($row['review_remarks']) ? htmlspecialchars($row['review_remarks']) : '<span class="text-muted">—</span>'; ?></td>
                                                                    <td><?= date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                                                    <td><?= !empty($row['updated_at']) && $row['status'] !== 'pending' ? date('d M Y, h:i A', strtotime($row['updated_at'])) : '—'; ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.box-body -->

                            <?php if (!empty($result)): ?>
                                <div class="box-footer clearfix">
                                    <div class="pull-left">
                                        <p class="text-muted">
                                            Showing <strong><?php echo count($result); ?></strong> inventory items
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('#add_inventory')) {
                $('#add_inventory').DataTable().destroy();
            }
            $('#add_inventory').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 25,
                "language": {
                    "search": "Search Items:",
                    "lengthMenu": "Show _MENU_ items",
                    "info": "Showing _START_ to _END_ of _TOTAL_ items",
                    "infoEmpty": "No items to show",
                    "paginate": {
                        "previous": "<i class='fa fa-chevron-left'></i>",
                        "next": "<i class='fa fa-chevron-right'></i>"
                    }
                }
            });

            // Auto-dismiss alerts
            setTimeout(function() {
                $('.alert').fadeOut(500, function() {
                    $(this).remove();
                });
            }, 2000);
            
            // Manual alert close
            $('.alert').on('click', '.close', function() {
                $(this).closest('.alert').fadeOut(500);
            });
        });

        // Function to open the product modal from footer
        function openProductModal() {
            // Check if the modal exists in the footer
            if ($('#productModal').length) {
                // Show the modal
                $('#productModal').modal('show');
                
                // Set a flag to indicate this is from the main inventory page
                window.productModalSource = 'main_inventory';
                
                // Reset form fields if needed (the modal's own reset will handle this)
                console.log('Opening product modal from inventory page');
            } else {
                console.error('Product modal not found in footer');
                alert('Error: Product modal not loaded. Please refresh the page.');
            }
        }

        // Function to refresh the inventory table after adding new item
        function refreshInventoryTable() {
            // Reload the page to show new item (simplest approach)
            // You can enhance this to use AJAX reload if needed
            location.reload();
        }
    </script>
</body>