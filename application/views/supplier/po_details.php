<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    PURCHASE ORDER DETAILS
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Purchase Order Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Flash Messages -->
                        <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-check"></i> Success!</h4>
                                <?= $this->session->flashdata('SUCCESSMSG') ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('ERRMSG')): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                <?= $this->session->flashdata('ERRMSG') ?>
                            </div>
                        <?php endif; ?>

                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Purchase Order - <?= $po['number_fk'] ?? 'N/A' ?></h3>
                                <div class="pull-right">
                                    <a href="<?= base_url('SupplierController/po_approvals') ?>" class="btn btn-success">
                                        <i class="fa fa-list"></i> View PO Approvals
                                    </a>
                                    <a href="<?= base_url('RFQController/index') ?>" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> Back to RFQ List
                                    </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- PO INFO -->
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <strong><i class="fa fa-calendar"></i> PO Date:</strong><br>
                                        <?= !empty($po['date']) ? date('d-m-Y', strtotime($po['date'])) : 'N/A' ?>
                                    </div>
                                    <div class="col-md-2">
                                        <strong><i class="fa fa-info-circle"></i> Status:</strong><br>
                                        <span class="label label-<?=
                                                                    isset($po['approval_status']) ?
                                                                        ($po['approval_status'] == 'pending_approval' ? 'warning' : ($po['approval_status'] == 'approved' ? 'success' : ($po['approval_status'] == 'rejected' ? 'danger' : 'info')))
                                                                        : 'info' ?>">
                                            <?= isset($po['approval_status']) ? ucfirst(str_replace('_', ' ', $po['approval_status'])) : 'Pending' ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fa fa-user"></i> Current Approver:</strong><br>
                                        <?= $po['current_approver'] ?? 'N/A' ?>
                                    </div>
                                    <div class="col-md-2">
                                        <strong><i class="fa fa-hashtag"></i> PR ID:</strong><br>
                                        <?= $po['pr_id'] ?? 'N/A' ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fa fa-file-text-o"></i> SO No:</strong><br>
                                        <?= !empty($po['so_no']) ? htmlspecialchars($po['so_no']) : (!empty($po['oc_no']) ? htmlspecialchars($po['oc_no']) : 'N/A') ?>
                                    </div>
                                </div>

                                <!-- Supplier Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5><i class="fa fa-building"></i> Vendor Information</h5>
                                        <div class="well">
                                            <strong>Company:</strong> <?= $po['supplier_name'] ?? 'N/A' ?><br>
                                            <strong>Contact Person:</strong> <?= $po['fullname'] ?? 'N/A' ?><br>
                                            <strong>Email:</strong> <?= $po['email'] ?? 'N/A' ?><br>
                                            <strong>Phone:</strong> <?= $po['mobile'] ?? 'N/A' ?><br>
                                            <strong>Address:</strong> <?= $po['address'] ?? 'N/A' ?><br>
                                            <strong>GST:</strong> <?= $po['gst'] ?? 'N/A' ?><br>
                                            <strong>PAN:</strong> <?= $po['pancard'] ?? 'N/A' ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fa fa-file-invoice-dollar"></i> Payment Information</h5>
                                        <div class="well">
                                            <strong>Total Amount:</strong> ₹<?= number_format($po['total'] ?? 0, 2) ?><br>
                                            <strong>Payment Due Date:</strong> <?= $po['payment_due_date'] ?? 'N/A' ?><br>
                                            <strong>Balance:</strong> ₹<?= number_format($po['balance'] ?? 0, 2) ?><br>
                                            <strong>Paid:</strong> ₹<?= number_format($po['paid'] ?? 0, 2) ?><br>
                                            <strong>Payment Terms:</strong> <?= $po['po_payment_terms'] ?? 'Net 30 Days' ?><br>
                                            <strong>Taxes:</strong> <?= $po['po_taxes'] ?? 'GST as applicable' ?>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- PO ITEMS -->
                                <h4><i class="fa fa-list"></i> Purchase Order Items</h4>

                                <?php
                                // Initialize total_amount variable
                                $total_amount = 0;
                                ?>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product Name</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Unit Price</th>
                                                <th>GST</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($po_items)): ?>
                                                <?php
                                                $total_amount = 0;
                                                foreach ($po_items as $index => $item):
                                                    $item_amount = $item['amount'] ?? 0;
                                                    $total_amount += $item_amount;
                                                ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td><strong><?= $item['product_name'] ?? 'N/A' ?></strong></td>
                                                        <td><?= $item['description'] ?? 'N/A' ?></td>
                                                        <td><?= $item['quantity'] ?? 0 ?></td>
                                                        <td><?= $item['unit'] ?? 'N/A' ?></td>
                                                        <td class="text-right">₹<?= number_format($item['price'] ?? 0, 2) ?></td>
                                                        <td class="text-right"><?= $item['gst'] ?? '0%' ?></td>
                                                        <td class="text-right"><strong>₹<?= number_format($item_amount, 2) ?></strong></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        <i class="fa fa-exclamation-circle fa-2x mb-2"></i><br>
                                                        No items found.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="7" class="text-right"><strong>Grand Total:</strong></td>
                                                <!-- FIXED: Use $total_amount which is always initialized -->
                                                <td class="text-right"><strong>₹<?= number_format($po['total'] ?? $total_amount, 2) ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <hr>

                                <!-- Approval History -->
                                <h4><i class="fa fa-history"></i> Approval History</h4>

                                <?php if (!empty($approval_history)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Approver</th>
                                                    <th>Level</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Action Date</th>
                                                    <th>Action By</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($approval_history as $index => $approval): ?>
                                                    <tr class="<?=
                                                                ($approval['status'] ?? '') == 'approved' ? 'success' : (($approval['status'] ?? '') == 'rejected' ? 'danger' : 'warning')
                                                                ?>">
                                                        <td><?= $index + 1 ?></td>
                                                        <td><?= $approval['approver_email'] ?? 'N/A' ?></td>
                                                        <td>
                                                            <span class="label label-info">
                                                                <?= ucfirst(str_replace('_', ' ', $approval['approval_level'] ?? 'N/A')) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="label label-<?=
                                                                                        ($approval['status'] ?? '') == 'approved' ? 'success' : (($approval['status'] ?? '') == 'rejected' ? 'danger' : 'warning')
                                                                                        ?>">
                                                                <?= ucfirst($approval['status'] ?? 'N/A') ?>
                                                            </span>
                                                        </td>
                                                        <td><?= $approval['remarks'] ?? 'N/A' ?></td>
                                                        <td>
                                                            <?php
                                                            if (!empty($approval['action_date'])) {
                                                                echo date('d-m-Y H:i:s', strtotime($approval['action_date']));
                                                            } elseif (!empty($approval['created_at'])) {
                                                                echo date('d-m-Y H:i:s', strtotime($approval['created_at']));
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if (!empty($approval['action_by_name'])) {
                                                                echo html_escape($approval['action_by_name']) . (!empty($approval['action_by_role']) ? ' (' . html_escape($approval['action_by_role']) . ')' : '');
                                                            } else {
                                                                echo html_escape($approval['action_by'] ?? 'N/A');
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No approval history found.
                                    </div>
                                <?php endif; ?>

                                <!-- PO Notes -->
                                <?php if (!empty($po['note']) || !empty($po['po_terms_and_conditions'])): ?>
                                    <hr>
                                    <h4><i class="fa fa-sticky-note"></i> Notes & Terms</h4>
                                    <div class="well">
                                        <?php if (!empty($po['note'])): ?>
                                            <p><strong>Note:</strong> <?= $po['note'] ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($po['po_terms_and_conditions'])): ?>
                                            <p><strong>Terms & Conditions:</strong> <?= $po['po_terms_and_conditions'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <div class="pull-right">
                                    <!-- FIXED: Added null check for approval_status -->
                                    <?php if (($po['approval_status'] ?? '') == 'approved'): ?>
                                        <button class="btn btn-success print-po">
                                            <i class="fa fa-print"></i> Print PO
                                        </button>
                                        <a href="mailto:<?= $po['email'] ?? '#' ?>?subject=Purchase Order <?= $po['number_fk'] ?? '' ?>&body=Dear <?= $po['supplier_name'] ?? '' ?>,%0D%0A%0D%0APlease find attached the Purchase Order <?= $po['number_fk'] ?? '' ?>." class="btn btn-primary">
                                            <i class="fa fa-envelope"></i> Email to Vendor
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('RFQController/index') ?>" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
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

    <!-- Print Styles -->
    <style>
        @media print {

            .no-print,
            .box-footer,
            .content-header,
            .breadcrumb,
            .main-header {
                display: none !important;
            }

            .content-wrapper,
            .box,
            .box-body {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
            }

            .box {
                border: none !important;
                box-shadow: none !important;
            }

            body {
                background: white !important;
                font-size: 12px;
            }

            .table {
                font-size: 10px;
            }

            .print-po,
            .btn {
                display: none !important;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Print functionality
            $('.print-po').on('click', function() {
                window.print();
            });
        });
    </script>
</body>

</html>