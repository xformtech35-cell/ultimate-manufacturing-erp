<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="content-wrapper">
            <section class="content-header">
                <h1>GRN Approval Details</h1>
                <ol class="breadcrumb">
                    <li><a href="<?= base_url('Home/index/') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?= base_url('GrnController/grn_approvals') ?>"><i class="fa fa-check-circle"></i> GRN Approvals</a></li>
                    <li class="active">GRN Approval Details</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">

                        <!-- GRN Summary Card -->
                        <?php if (!empty($grn_data_group)): ?>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">GRN Summary</h3>
                                    <div class="box-tools pull-right">
                                        <?php if (!empty($grn_data_group['approval_status'])): ?>
                                            <span class="label label-<?= $grn_data_group['approval_status'] == 'approved' ? 'success' : ($grn_data_group['approval_status'] == 'rejected' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($grn_data_group['approval_status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-light-blue">
                                                <span class="info-box-icon"><i class="fa fa-hashtag"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">GRN Number</span>
                                                    <span class="info-box-number"><?= html_escape($grn_data_group['grn_number'] ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-green">
                                                <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">PO Number</span>
                                                    <span class="info-box-number"><?= html_escape($grn_data_group['po_number_fk'] ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-yellow">
                                                <span class="info-box-icon"><i class="fa fa-building"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Supplier</span>
                                                    <span class="info-box-number"><?= html_escape($grn_data_group['company_name'] ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-red">
                                                <span class="info-box-icon"><i class="fa fa-inr"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Amount</span>
                                                    <span class="info-box-number">₹<?= number_format((float)($grn_data_group['total'] ?? 0), 2) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="40%">GRN Date</th>
                                                    <td>
                                                        <?php
                                                        if (!empty($grn_data_group['date']) && $grn_data_group['date'] !== '0000-00-00' && $grn_data_group['date'] !== null) {
                                                            echo date('d-m-Y', strtotime($grn_data_group['date']));
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Invoice Number</th>
                                                    <td><?= html_escape($grn_data_group['invoice_number'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Invoice Date</th>
                                                    <td>
                                                        <?php
                                                        if (!empty($grn_data_group['invoice_date']) && $grn_data_group['invoice_date'] !== '0000-00-00' && $grn_data_group['invoice_date'] !== null) {
                                                            echo date('d-m-Y', strtotime($grn_data_group['invoice_date']));
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Notes</th>
                                                    <td><?= !empty($grn_data_group['note']) ? html_escape($grn_data_group['note']) : 'No notes' ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="40%">Supplier GST</th>
                                                    <td><?= html_escape($grn_data_group['gst'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Supplier PAN</th>
                                                    <td><?= html_escape($grn_data_group['pancard'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Supplier Address</th>
                                                    <td><?= html_escape($grn_data_group['address'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Current Approver</th>
                                                    <td>
                                                        <?php if (!empty($grn_data_group['current_approver'])): ?>
                                                            <span class="label label-info"><?= html_escape($grn_data_group['current_approver']) ?></span>
                                                        <?php else: ?>
                                                            <span class="label label-default">Not Assigned</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- GRN Items Table -->
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">GRN Items</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="bg-success">
                                                <th>#</th>
                                                <th>Product Name</th>
                                                <th>HSN Code</th>
                                                <th>Quantity</th>
                                                <th>Received Qty</th>
                                                <th>Pending Qty</th>
                                                <th>Price (₹)</th>
                                                <th>GST %</th>
                                                <th>SGST (₹)</th>
                                                <th>CGST (₹)</th>
                                                <th>Total (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($show_grn)): ?>
                                                <?php
                                                $i = 1;
                                                $grand_total = 0;
                                                foreach ($show_grn as $item):
                                                    $item_total = ($item->price * $item->received_quantity) + ($item->sgst ?? 0) + ($item->cgst ?? 0);
                                                    $grand_total += $item_total;
                                                ?>
                                                    <tr>
                                                        <td><?= $i++ ?></td>
                                                        <td>
                                                            <?php
                                                            // Extract product name without description
                                                            $product_name = $item->product_name;
                                                            if (strpos($product_name, ' - ') !== false) {
                                                                $parts = explode(' - ', $product_name, 2);
                                                                echo '<strong>' . html_escape($parts[0]) . '</strong>';
                                                                if (isset($parts[1])) {
                                                                    echo '<br><small class="text-muted">' . substr(html_escape($parts[1]), 0, 100) . '...</small>';
                                                                }
                                                            } else {
                                                                echo html_escape($product_name);
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?= html_escape($item->hsn_code ?? 'N/A') ?></td>
                                                        <td class="text-center"><?= $item->quantity ?></td>
                                                        <td class="text-center"><?= $item->received_quantity ?></td>
                                                        <td class="text-center"><?= $item->pending_quantity ?></td>
                                                        <td class="text-right"><?= number_format((float)$item->price, 2) ?></td>
                                                        <td class="text-center"><?= html_escape($item->gst ?? '0%') ?></td>
                                                        <td class="text-right"><?= number_format((float)$item->sgst, 2) ?></td>
                                                        <td class="text-right"><?= number_format((float)$item->cgst, 2) ?></td>
                                                        <td class="text-right" style="font-weight: bold;">
                                                            ₹<?= number_format($item_total, 2) ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="11" class="text-center text-muted">No items found in this GRN</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <?php if (!empty($show_grn)): ?>
                                                <tr class="bg-gray">
                                                    <td colspan="10" class="text-right"><strong>Grand Total:</strong></td>
                                                    <td class="text-right"><strong>₹<?= number_format($grand_total, 2) ?></strong></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Approval History -->
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Approval History</h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($approval_history)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr class="bg-primary">
                                                    <th>Level</th>
                                                    <th>Approver Role</th>
                                                    <th>Approver Email</th>
                                                    <th>Status</th>
                                                    <th>Action Date</th>
                                                    <th>Remarks</th>
                                                    <th>Action By</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($approval_history as $history): ?>
                                                    <tr>
                                                        <td>
                                                            <?php
                                                            $level = $history['approval_level'] ?? '';
                                                            $level_text = str_replace('_', ' ', $level);
                                                            echo ucfirst($level_text);
                                                            ?>
                                                        </td>
                                                        <td><?= html_escape($history['approver_role'] ?? 'N/A') ?></td>
                                                        <td><?= html_escape($history['approver_email'] ?? 'N/A') ?></td>
                                                        <td>
                                                            <span class="label label-<?= ($history['status'] ?? '') == 'approved' ? 'success' : (($history['status'] ?? '') == 'rejected' ? 'danger' : 'warning') ?>">
                                                                <?= ucfirst($history['status'] ?? 'pending') ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $action_date = $history['action_date'] ?? $history['created_at'] ?? '';
                                                            if (!empty($action_date) && $action_date !== '0000-00-00 00:00:00' && $action_date !== null) {
                                                                echo date('d-m-Y H:i', strtotime($action_date));
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <small><?= html_escape(!empty($history['remarks']) ? $history['remarks'] : 'No remarks') ?></small>
                                                        </td>
                                                         <td>
                                                             <?php
                                                             if (!empty($history['action_by_name'])) {
                                                                 echo html_escape($history['action_by_name']) . (!empty($history['action_by_role']) ? ' (' . html_escape($history['action_by_role']) . ')' : '');
                                                             } else {
                                                                 echo html_escape(!empty($history['action_by']) ? $history['action_by'] : 'System');
                                                             }
                                                             ?>
                                                         </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info text-center">
                                        <i class="fa fa-info-circle"></i> No approval history found for this GRN.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Approval Action Box (Only show if user has pending approval) -->
                        <?php if (!empty($user_approval)): ?>
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Take Action</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-8 col-md-offset-2">
                                            <div class="alert alert-info">
                                                <h4><i class="icon fa fa-info"></i> Your Approval Required</h4>
                                                You have a pending approval for this GRN. Please review and take appropriate action.
                                            </div>

                                            <form id="approvalForm" method="post" action="<?= base_url('GrnController/process_grn_approval') ?>">
                                                <input type="hidden" name="approval_id" value="<?= $user_approval['approval_id'] ?>">
                                                <input type="hidden" name="user_email" value="<?= $this->session->userdata('session_data_head')['result']['user_email'] ?? '' ?>">

                                                <div class="form-group">
                                                    <label>Your Role</label>
                                                    <input type="text" class="form-control" value="<?= html_escape($user_approval['approver_role'] ?? 'Approver') ?>" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label>Approval Level</label>
                                                    <input type="text" class="form-control" value="<?= ucfirst(str_replace('_', ' ', $user_approval['approval_level'] ?? '')) ?>" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="remarks">Remarks <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="remarks" id="remarks" rows="3"
                                                        placeholder="Please provide remarks for your decision..." required></textarea>
                                                    <small class="text-muted">Remarks are required for audit trail.</small>
                                                </div>

                                                <div class="form-group text-center">
                                                    <button type="submit" name="action" value="approved" class="btn btn-success btn-lg">
                                                        <i class="fa fa-check"></i> Approve GRN
                                                    </button>
                                                    <button type="submit" name="action" value="rejected" class="btn btn-danger btn-lg">
                                                        <i class="fa fa-times"></i> Reject GRN
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="box box-default">
                            <div class="box-body text-center">
                                <div class="btn-group">
                                    <a href="<?= base_url('GrnController/grn_approvals') ?>" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to Approvals
                                    </a>
                                    <a href="<?= base_url('GrnController/show_grn/' . str_replace('/', '-', $grn_data_group['grn_number'] ?? '')) ?>"
                                        class="btn btn-info" target="_blank">
                                        <i class="fa fa-eye"></i> View Full GRN
                                    </a>
                                    <a href="<?= base_url('GrnController/conduct_inspection?grn=' . urlencode($grn_data_group['grn_number'] ?? '')) ?>"
                                        class="btn btn-success" target="_blank">
                                        <i class="fa fa-clipboard"></i> Conduct Inspection
                                    </a>
                                    <?php if (!empty($grn_data_group['po_number_fk'])): ?>
                                        <a href="<?= base_url('SupplierController/show_po_details/' . str_replace('/', '-', $grn_data_group['po_number_fk'])) ?>"
                                            class="btn btn-warning" target="_blank">
                                            <i class="fa fa-file-text"></i> View PO
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-primary" onclick="window.print()">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>

        <script>
            $(document).ready(function() {
                // Form validation
                $('#approvalForm').submit(function(e) {
                    var remarks = $('#remarks').val().trim();
                    if (remarks === '') {
                        e.preventDefault();
                        alert('Please enter remarks before submitting.');
                        $('#remarks').focus();
                        return false;
                    }

                    // Confirm action
                    var action = $(document.activeElement).val();
                    var confirmMessage = action === 'approved' ?
                        'Are you sure you want to APPROVE this GRN?' :
                        'Are you sure you want to REJECT this GRN?';

                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                        return false;
                    }

                    // Show loading
                    $('button[type="submit"]').html('<i class="fa fa-spinner fa-spin"></i> Processing...')
                        .prop('disabled', true);
                });

                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Auto-focus remarks field if approval box is visible
                <?php if (!empty($user_approval)): ?>
                    $('#remarks').focus();
                <?php endif; ?>
            });
        </script>

        <style>
            .info-box {
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
                margin-bottom: 10px;
            }

            .info-box-icon {
                border-radius: 0;
            }

            .bg-light-blue {
                background-color: #3c8dbc !important;
                color: white !important;
            }

            .bg-yellow {
                background-color: #f39c12 !important;
                color: white !important;
            }

            .table th {
                background-color: #f4f4f4;
                font-weight: bold;
            }

            .btn-group .btn {
                margin: 0 5px;
            }

            @media print {

                .box-header,
                .box-tools,
                .btn,
                .breadcrumb,
                #approvalForm,
                .no-print {
                    display: none !important;
                }

                .box {
                    border: none !important;
                    box-shadow: none !important;
                }

                .info-box {
                    page-break-inside: avoid;
                }
            }
        </style>

<?php $this->load->view('admin/footer'); ?>