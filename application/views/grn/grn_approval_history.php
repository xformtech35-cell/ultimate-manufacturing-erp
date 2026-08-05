<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="content-wrapper">
            <section class="content-header">
                <h1>GRN Approval History</h1>
                <ol class="breadcrumb">
                    <li><a href="<?= base_url('Home/index/') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?= base_url('GrnController/grn_approvals') ?>"><i class="fa fa-check-circle"></i> GRN Approvals</a></li>
                    <li class="active">Approval History</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">GRN Approval History</h3>
                                <div class="box-tools pull-right">
                                    <a href="<?= base_url('GrnController/export_grn_approval_report') ?>" class="btn btn-sm btn-success">
                                        <i class="fa fa-download"></i> Export to Excel
                                    </a>
                                    <button class="btn btn-sm btn-primary" onclick="window.print()">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <!-- Filter Form -->
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-md-12">
                                        <form method="get" action="<?= base_url('GrnController/grn_approval_history') ?>" class="form-inline">
                                            <div class="form-group">
                                                <label for="status_filter">Status: </label>
                                                <select name="status" id="status_filter" class="form-control input-sm" style="margin-left: 10px;">
                                                    <option value="">All Status</option>
                                                    <option value="approved" <?= (isset($_GET['status']) && $_GET['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                                                    <option value="rejected" <?= (isset($_GET['status']) && $_GET['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                            </div>
                                            <div class="form-group" style="margin-left: 15px;">
                                                <label for="date_from">From: </label>
                                                <input type="date" name="date_from" id="date_from" class="form-control input-sm" style="margin-left: 10px;"
                                                    value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : '' ?>">
                                            </div>
                                            <div class="form-group" style="margin-left: 15px;">
                                                <label for="date_to">To: </label>
                                                <input type="date" name="date_to" id="date_to" class="form-control input-sm" style="margin-left: 10px;"
                                                    value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : '' ?>">
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary" style="margin-left: 15px;">
                                                <i class="fa fa-filter"></i> Filter
                                            </button>
                                            <a href="<?= base_url('GrnController/grn_approval_history') ?>" class="btn btn-sm btn-default" style="margin-left: 10px;">
                                                <i class="fa fa-refresh"></i> Clear
                                            </a>
                                        </form>
                                    </div>
                                </div>

                                <?php if (empty($approval_history)): ?>
                                    <div class="alert alert-info text-center">
                                        <i class="fa fa-info-circle"></i> No GRN approval history found.
                                        <?php if (isset($_GET['status']) || isset($_GET['date_from'])): ?>
                                            <br><small>Try changing your filter criteria.</small>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr class="bg-primary">
                                                    <th>#</th>
                                                    <th>GRN Number</th>
                                                    <th>Supplier</th>
                                                    <th>PO Number</th>
                                                    <th>Date</th>
                                                    <th>Amount (₹)</th>
                                                    <th>Approval Level</th>
                                                    <th>Approver Role</th>
                                                    <th>Status</th>
                                                    <th>Action Date</th>
                                                    <th>Remarks</th>
                                                    <th>Action By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                $total_amount = 0;
                                                $approved_count = 0;
                                                $rejected_count = 0;
                                                foreach ($approval_history as $history):
                                                    $amount = $history->total ?? 0;
                                                    $total_amount += $amount;

                                                    if (isset($history->status)) {
                                                        if ($history->status == 'approved') $approved_count++;
                                                        if ($history->status == 'rejected') $rejected_count++;
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= $i++ ?></td>
                                                        <td>
                                                            <a href="<?= base_url('GrnController/show_grn_approval_details/' . str_replace('/', '-', $history->grn_number)) ?>"
                                                                class="text-primary" title="View GRN Details">
                                                                <strong><?= html_escape($history->grn_number ?? 'N/A') ?></strong>
                                                            </a>
                                                        </td>
                                                        <td><?= html_escape($history->supplier_name ?? 'N/A') ?></td>
                                                        <td><?= html_escape($history->po_number_fk ?? 'N/A') ?></td>
                                                        <td>
                                                            <?php
                                                            if (!empty($history->date) && $history->date !== '0000-00-00' && $history->date !== null) {
                                                                echo date('d-m-Y', strtotime($history->date));
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-right" style="font-weight: bold;">
                                                            ₹<?= number_format((float)$amount, 2) ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $level = $history->approval_level ?? '';
                                                            $level_text = str_replace('_', ' ', $level);
                                                            echo '<span class="label label-default">' . ucfirst($level_text) . '</span>';
                                                            ?>
                                                        </td>
                                                        <td><?= html_escape($history->approver_role ?? 'Approver') ?></td>
                                                        <td>
                                                            <span class="label label-<?= ($history->status ?? '') == 'approved' ? 'success' : 'danger' ?>">
                                                                <?= ucfirst($history->status ?? 'pending') ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $action_date = $history->action_date ?? $history->created_at ?? '';
                                                            if (!empty($action_date) && $action_date !== '0000-00-00 00:00:00' && $action_date !== null) {
                                                                echo '<small>' . date('d-m-Y', strtotime($action_date)) . '<br>';
                                                                echo date('H:i:s', strtotime($action_date)) . '</small>';
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted"><?= html_escape(!empty($history->remarks) ? $history->remarks : 'No remarks') ?></small>
                                                        </td>
                                                        <td>
                                                             <small>
                                                             <?php
                                                             if (!empty($history->action_by_name)) {
                                                                 echo html_escape($history->action_by_name) . (!empty($history->action_by_role) ? ' (' . html_escape($history->action_by_role) . ')' : '');
                                                             } else {
                                                                 echo html_escape(!empty($history->action_by) ? $history->action_by : 'System');
                                                             }
                                                             ?>
                                                             </small>
                                                        </td>
                                                        <td>
                                                            <a href="<?= base_url('GrnController/show_grn_approval_details/' . str_replace('/', '-', $history->grn_number)) ?>"
                                                                class="btn btn-xs btn-info" title="View Details">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light-blue">
                                                    <td colspan="5" class="text-right"><strong>Summary:</strong></td>
                                                    <td class="text-right"><strong>₹<?= number_format($total_amount, 2) ?></strong></td>
                                                    <td colspan="3">
                                                        <strong>Total: <?= count($approval_history) ?></strong> |
                                                        <span class="text-success">Approved: <?= $approved_count ?></span> |
                                                        <span class="text-danger">Rejected: <?= $rejected_count ?></span>
                                                    </td>
                                                    <td colspan="4"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <?php if (isset($pagination)): ?>
                                        <div class="text-center">
                                            <?= $pagination ?>
                                        </div>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> Showing <?= count($approval_history) ?> approval record(s)
                                        </small>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="text-muted">
                                            Generated on: <?= date('d-m-Y H:i:s') ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Card -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Approved</span>
                                        <span class="info-box-number"><?= isset($approved_count) ? $approved_count : 0 ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= (isset($approved_count) && isset($approval_history) && count($approval_history) > 0) ? ($approved_count / count($approval_history)) * 100 : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= (isset($approved_count) && isset($approval_history) && count($approval_history) > 0) ? round(($approved_count / count($approval_history)) * 100, 2) : 0 ?>% of total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Rejected</span>
                                        <span class="info-box-number"><?= isset($rejected_count) ? $rejected_count : 0 ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= (isset($rejected_count) && isset($approval_history) && count($approval_history) > 0) ? ($rejected_count / count($approval_history)) * 100 : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= (isset($rejected_count) && isset($approval_history) && count($approval_history) > 0) ? round(($rejected_count / count($approval_history)) * 100, 2) : 0 ?>% of total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-blue">
                                    <span class="info-box-icon"><i class="fa fa-inr"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Amount</span>
                                        <span class="info-box-number">₹<?= isset($total_amount) ? number_format((float)$total_amount, 2) : '0.00' ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Average: ₹<?= (isset($total_amount) && isset($approval_history) && count($approval_history) > 0) ? number_format($total_amount / count($approval_history), 2) : '0.00' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <script>
            $(document).ready(function() {
                // Initialize date pickers
                $('#date_from, #date_to').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });

                // DataTable initialization (if DataTables is available)
                if ($.fn.dataTable) {
                    $('table').DataTable({
                        "pageLength": 25,
                        "order": [
                            [9, "desc"]
                        ], // Sort by action date descending
                        "dom": '<"top"fl>rt<"bottom"ip><"clear">',
                        "language": {
                            "search": "Search GRNs:",
                            "lengthMenu": "Show _MENU_ entries"
                        }
                    });
                }

                // Print styles
                window.printStyles = `
                    @media print {
                        .box-header, .box-tools, .nav-tabs, .breadcrumb, 
                        .info-box, .form-inline, .btn, .footer {
                            display: none !important;
                        }
                        .box {
                            border: none !important;
                            box-shadow: none !important;
                        }
                        table {
                            font-size: 11px !important;
                        }
                        h1 {
                            font-size: 18px !important;
                        }
                        .text-right {
                            text-align: right !important;
                        }
                    }
                `;

                // Add print styles when printing
                window.onafterprint = function() {
                    $('#print-styles').remove();
                };
            });
        </script>

        <style>
            .table th {
                background-color: #f4f4f4;
                font-weight: bold;
            }

            .table td {
                vertical-align: middle !important;
            }

            .label-warning {
                background-color: #f39c12;
            }

            .label-success {
                background-color: #00a65a;
            }

            .label-danger {
                background-color: #dd4b39;
            }

            .label-default {
                background-color: #777;
            }

            .info-box {
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }

            .nav-tabs-custom {
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            }

            .bg-light-blue {
                background-color: #3c8dbc !important;
                color: white !important;
            }
        </style>

<?php $this->load->view('admin/footer'); ?>