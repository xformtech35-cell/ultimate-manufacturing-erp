<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Initialize variables to avoid undefined errors
$inspection_data = isset($inspection_data) ? $inspection_data : array();
$grn_summary = isset($grn_summary) ? $grn_summary : array();
$grn_details = isset($grn_details) ? $grn_details : array();
$session_data_head = isset($session_data_head) ? $session_data_head : array();
$users = isset($users) && is_array($users) ? $users : array();
?>
<style>
    /* Premium aesthetics for Conduct GRN Inspection Page */
    .conduct-inspection-wrapper {
        padding: 15px;
        font-family: 'Inter', 'Source Sans Pro', sans-serif;
    }
    
    .conduct-inspection-wrapper label {
        color: #334155 !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        display: inline-block !important;
        margin-bottom: 6px !important;
    }
    
    .conduct-inspection-wrapper .checkbox label {
        color: #334155 !important;
        font-weight: 500 !important;
        cursor: pointer;
        padding-left: 24px !important;
        position: relative;
    }

    .conduct-inspection-wrapper .checkbox input[type="checkbox"] {
        position: absolute;
        margin-left: -24px !important;
        margin-top: 2px !important;
    }
    
    .conduct-inspection-wrapper h1,
    .conduct-inspection-wrapper h3,
    .conduct-inspection-wrapper h4 {
        color: #0f172a !important;
        font-weight: 700 !important;
    }
    
    .conduct-inspection-wrapper .box.box-primary {
        border-top: 3px solid #3b82f6 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04), 0 2px 4px rgba(15, 23, 42, 0.02) !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        margin-bottom: 24px;
    }
    
    .conduct-inspection-wrapper .box-header {
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 18px 24px !important;
    }
    
    .conduct-inspection-wrapper .box-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
    }
    
    .conduct-inspection-wrapper .box-body {
        padding: 24px !important;
    }
    
    .conduct-inspection-wrapper .box-footer {
        background: #f8fafc !important;
        border-top: 1px solid #e2e8f0 !important;
        padding: 18px 24px !important;
        border-radius: 0 0 12px 12px !important;
    }
    
    .conduct-inspection-wrapper .form-control {
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 6px 12px !important;
        height: 38px !important;
        background-color: #fff !important;
        color: #0f172a !important;
        font-size: 13.5px !important;
        box-shadow: none !important;
        transition: border-color 0.2s, box-shadow 0.2s !important;
    }
    
    .conduct-inspection-wrapper .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
    }
    
    .conduct-inspection-wrapper .form-control[readonly] {
        background-color: #f8fafc !important;
        color: #64748b !important;
        border-color: #e2e8f0 !important;
        cursor: not-allowed;
    }
    
    /* ── Table: matches global ERP palette ── */
    .conduct-inspection-wrapper .table {
        border-radius: 8px !important;
        overflow: hidden !important;
        border: 1px solid #e2e8f0 !important;
        margin-top: 15px;
        width: 100% !important;
    }

    .conduct-inspection-wrapper .table thead th {
        background: linear-gradient(135deg, #1e6fa8 0%, #3c8dbc 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 11.5px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        padding: 13px 14px !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        border: none !important;
        border-right: 1px solid rgba(255,255,255,0.15) !important;
        text-align: center;
    }
    .conduct-inspection-wrapper .table thead th:first-child {
        text-align: left;
    }
    .conduct-inspection-wrapper .table thead th:last-child {
        border-right: none !important;
    }

    .conduct-inspection-wrapper .table tbody tr:nth-of-type(odd) > td {
        background-color: #f4f8fb !important;
    }
    .conduct-inspection-wrapper .table tbody tr:nth-of-type(even) > td {
        background-color: #ffffff !important;
    }
    .conduct-inspection-wrapper .table tbody tr:hover > td {
        background-color: #dbeeff !important;
        transition: background-color 0.12s ease !important;
    }
    .conduct-inspection-wrapper .table tbody td {
        padding: 11px 14px !important;
        vertical-align: middle !important;
        color: #2d3748 !important;
        font-size: 13px !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 1px solid #e8eef4 !important;
        line-height: 1.5 !important;
    }

    /* Table inputs / selects inside cells — compact, clean */
    .conduct-inspection-wrapper .table .form-control {
        height: 34px !important;
        font-size: 12.5px !important;
        padding: 4px 8px !important;
        border-radius: 5px !important;
        border: 1px solid #cbd5e1 !important;
        background: #fff !important;
    }
    .conduct-inspection-wrapper .table .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59,130,246,0.12) !important;
    }

    .conduct-inspection-wrapper hr {
        border-top: 1px solid #e2e8f0 !important;
        margin: 24px 0 !important;
    }
    
    .conduct-inspection-wrapper .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        border: none !important;
        font-weight: 600 !important;
        padding: 9px 20px !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.1) !important;
        transition: all 0.2s !important;
    }
    .conduct-inspection-wrapper .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
    }
    
    .conduct-inspection-wrapper .btn-default {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600 !important;
        padding: 9px 20px !important;
        border-radius: 6px !important;
        margin-left: 8px !important;
        transition: all 0.2s !important;
    }
    .conduct-inspection-wrapper .btn-default:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }
    
    .conduct-inspection-wrapper .btn-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;
        border: none !important;
        font-weight: 600 !important;
        padding: 9px 20px !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 4px rgba(2, 132, 199, 0.1) !important;
        transition: all 0.2s !important;
    }
    .conduct-inspection-wrapper .btn-info:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2) !important;
    }
</style>

<div class="content-wrapper">
    <div class="conduct-inspection-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Conduct GRN Inspection
                    <small><?php echo isset($grn_summary['grn_number']) ? $grn_summary['grn_number'] : ''; ?></small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-check"></i> Success!</h4>
                                <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('ERRORMSG')): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                <?php echo $this->session->flashdata('ERRORMSG'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Inspection Form: <?php echo isset($grn_summary['grn_number']) ? $grn_summary['grn_number'] : ''; ?></h3>
                            </div>

                            <form action="<?php echo base_url('GrnController/save_inspection'); ?>" method="post" id="inspectionForm">
                                <div class="box-body">
                                    <!-- GRN Information -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>GRN Number</label>
                                                <input type="text" class="form-control" value="<?php echo isset($grn_summary['grn_number']) ? $grn_summary['grn_number'] : ''; ?>" readonly>
                                                <input type="hidden" name="grn_number" value="<?php echo isset($grn_summary['grn_number']) ? $grn_summary['grn_number'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>PO Reference</label>
                                                <input type="text" class="form-control" value="<?php echo isset($grn_summary['po_number_fk']) ? $grn_summary['po_number_fk'] : ''; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Supplier</label>
                                                <input type="text" class="form-control" value="<?php echo isset($grn_summary['company_name']) ? $grn_summary['company_name'] : ''; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Inspected By *</label>
                                                <select class="form-control" name="inspected_by" required>
                                                    <?php
                                                    $current_user_id = $session_data_head['result']['user_id'] ?? 0;
                                                    $current_username = $session_data_head['result']['username'] ?? 'Current User';
                                                    ?>

                                                    <option value="<?php echo $current_user_id; ?>" selected>
                                                        <?php echo $current_username; ?>
                                                    </option>

                                                    <?php foreach ($users as $user): ?>
                                                        <?php if ($user->user_id != $current_user_id): ?>
                                                            <option value="<?php echo $user->user_id; ?>">
                                                                <?php echo $user->username; ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Inspection Date *</label>
                                                <input type="date" class="form-control" name="inspection_date" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Overall Status *</label>
                                                <select class="form-control" name="overall_status" required>
                                                    <option value="PENDING" selected>Pending</option>
                                                    <option value="PASSED">Passed</option>
                                                    <option value="FAILED">Failed</option>
                                                    <option value="PARTIAL">Partial</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <hr style="border-top:1px solid #e2e8f0;margin:20px 0;">

                                    <!-- Item Inspection Details sub-header matching global box-header style -->
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                        <h4 style="margin:0;font-size:15px;font-weight:700;color:#0f172a;"><i class="fa fa-list" style="color:#3c8dbc;margin-right:6px;"></i>Item Inspection Details</h4>
                                    </div>

                                    <div class="table-responsive" id="inspectionTableWrapper">
                                        <table class="table table-bordered no-datatable" id="inspectionItemsTable">
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Ordered Qty</th>
                                                    <th>Delivered Qty</th>
                                                    <th>Accepted Qty</th>
                                                    <th>Rejected Qty</th>
                                                    <th>Rejection Reason</th>
                                                    <th>Quality Rating</th>
                                                    <th>Packaging</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($grn_details)): ?>
                                                    <?php foreach ($grn_details as $item): ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $item->product_name; ?>
                                                                <input type="hidden" name="product_name[]" value="<?php echo $item->product_name; ?>">
                                                                <input type="hidden" name="quantity[]" value="<?php echo $item->received_quantity; ?>">
                                                            </td>
                                                            <td class="text-center"><?php echo $item->quantity; ?></td>
                                                            <td class="text-center"><?php echo $item->received_quantity; ?></td>
                                                            <td>
                                                                <input type="number" class="form-control"
                                                                    name="accepted_quantity[]"
                                                                    min="0"
                                                                    max="<?php echo $item->received_quantity; ?>"
                                                                    value="<?php echo $item->received_quantity; ?>"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control"
                                                                    name="rejected_quantity[]"
                                                                    min="0"
                                                                    max="<?php echo $item->received_quantity; ?>"
                                                                    value="0">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="rejection_reason[]">
                                                                    <option value="">Select Reason</option>
                                                                    <option value="DAMAGED">Damaged</option>
                                                                    <option value="WRONG_ITEM">Wrong Item</option>
                                                                    <option value="EXPIRED">Expired</option>
                                                                    <option value="POOR_QUALITY">Poor Quality</option>
                                                                    <option value="QUANTITY_MISMATCH">Quantity Mismatch</option>
                                                                    <option value="OTHER">Other</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="quality_rating[]" required>
                                                                    <option value="GOOD" selected>Good</option>
                                                                    <option value="EXCELLENT">Excellent</option>
                                                                    <option value="FAIR">Fair</option>
                                                                    <option value="POOR">Poor</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="packaging_condition[]" required>
                                                                    <option value="INTACT" selected>Intact</option>
                                                                    <option value="MINOR_DAMAGE">Minor Damage</option>
                                                                    <option value="MAJOR_DAMAGE">Major Damage</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="inspection_notes[]" placeholder="Remarks">
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center">No items found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <label>Overall Inspection Notes</label>
                                        <textarea class="form-control" name="overall_notes" rows="3" placeholder="Enter overall inspection notes..."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="update_stock" value="1" checked>
                                                Update Stock After Inspection
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label style="font-weight: 600;">
                                                <input type="checkbox" name="notify_accounts" value="1" checked>
                                                Notify Accounts Department
                                            </label>
                                            <?php if (!empty($accounts_users)): ?>
                                                <div style="margin-left: 20px; margin-top: 4px;">
                                                    <span class="label label-info" style="font-size: 11px; font-weight: normal; padding: 4px 8px; display: inline-block;">
                                                        <i class="fa fa-envelope"></i> <strong>Recipients:</strong> 
                                                        <?php 
                                                            $acc_list = array();
                                                            foreach ($accounts_users as $u) {
                                                                $acc_list[] = htmlspecialchars($u['username']) . ' (' . htmlspecialchars($u['user_email']) . ')';
                                                            }
                                                            echo implode(', ', $acc_list);
                                                        ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <div style="margin-left: 20px; margin-top: 4px;">
                                                    <span class="label label-warning" style="font-size: 11px; font-weight: normal; padding: 4px 8px; display: inline-block;">
                                                        <i class="fa fa-info-circle"></i> <strong>Recipients:</strong> Accounts Department (accounts_uws@yopmail.com, xformtech46@gmail.com)
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check"></i> Submit Inspection
                                    </button>
                                    <a href="<?php echo base_url('GrnController/grn_index'); ?>" class="btn btn-default">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                    <a href="<?php echo base_url('GrnController/inspection_report/' . (isset($grn_summary['grn_number']) ? $grn_summary['grn_number'] : '')); ?>"
                                        class="btn btn-info pull-right">
                                        <i class="fa fa-eye"></i> View Inspection Report
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div> <!-- Closes conduct-inspection-wrapper -->
</div> <!-- Closes content-wrapper -->

    <script>
        $(document).ready(function() {

            /* ── Inspection items DataTable — matches GRN index global UI ── */
            if ($.fn.DataTable && $('#inspectionItemsTable').length) {
                $('#inspectionItemsTable').DataTable({
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                    ordering: true,
                    searching: true,
                    info: true,
                    language: {
                        search:      'Search:',
                        lengthMenu:  '_MENU_ entries per page',
                        info:        'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty:   'Showing 0 to 0 of 0 entries',
                        paginate: {
                            first:    '«',
                            last:     '»',
                            next:     '›',
                            previous: '‹'
                        }
                    },
                    dom: '<"pull-left"l><"pull-right"f><"table-responsive-container"t><"pull-left"i><"pull-right"p>',
                    columnDefs: [
                        { orderable: false, targets: [3, 4, 5, 6, 7, 8] }  // input columns not orderable
                    ],
                    drawCallback: function() {
                        // Re-apply alternating row colours after every draw
                        $('#inspectionItemsTable tbody tr:odd  td').css('background-color', '#f4f8fb');
                        $('#inspectionItemsTable tbody tr:even td').css('background-color', '#ffffff');
                    }
                });
            }

            /* ── Qty validation: accepted + rejected ≤ delivered ── */
            $('input[name="accepted_quantity[]"], input[name="rejected_quantity[]"]').on('input', function() {
                var row         = $(this).closest('tr');
                var deliveredQty = parseInt(row.find('td:nth-child(3)').text()) || 0;
                var acceptedQty  = parseInt(row.find('input[name="accepted_quantity[]"]').val()) || 0;
                var rejectedQty  = parseInt(row.find('input[name="rejected_quantity[]"]').val()) || 0;

                if (acceptedQty + rejectedQty > deliveredQty) {
                    alert('Accepted + Rejected quantity cannot exceed delivered quantity!');
                    $(this).val('');
                }
            });
        });
    </script>

<?php $this->load->view('admin/footer'); ?>