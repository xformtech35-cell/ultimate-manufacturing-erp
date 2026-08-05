<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
    exit();
}
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
                    GRN Inspection Report
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'GrnController/grn_index' ?>">GRN</a></li>
                    <li class="active">Inspection Report</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Inspection Report: <?php echo isset($grn_summary['grn_number']) ? $grn_summary['grn_number'] : 'N/A'; ?></h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if (empty($inspection_details)): ?>
                                    <div class="alert alert-warning">
                                        <h4><i class="icon fa fa-warning"></i> No Inspection Data</h4>
                                        No inspection details found for this GRN. Inspection features may not be enabled.
                                    </div>

                                    <div class="text-center">
                                        <a href="<?php echo base_url() . 'GrnController/show_grn/' . $grn_summary['grn_number'] ?? ''; ?>"
                                            class="btn btn-primary">
                                            <i class="fa fa-eye"></i> View Regular GRN Details
                                        </a>
                                        <a href="<?php echo base_url() . 'GrnController/grn_index'; ?>"
                                            class="btn btn-default">
                                            <i class="fa fa-list"></i> Back to GRN List
                                        </a>
                                    </div>
                                <?php else: ?>

                                    <!-- Inspection Summary -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="callout callout-info">
                                                <h4><i class="fa fa-info-circle"></i> Inspection Summary</h4>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>GRN Number:</strong><br>
                                                        <span class="text-primary"><?php echo $grn_summary['grn_number'] ?? 'N/A'; ?></span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>PO Reference:</strong><br>
                                                        <span class="text-primary"><?php echo $grn_summary['po_number_fk'] ?? 'N/A'; ?></span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Supplier:</strong><br>
                                                        <span class="text-primary"><?php echo $grn_summary['company_name'] ?? 'N/A'; ?></span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Total Amount:</strong><br>
                                                        <span class="text-primary">₹<?php echo number_format($grn_summary['total'] ?? 0, 2); ?></span>
                                                    </div>
                                                </div>

                                                <?php if (isset($grn_summary['inspection_status']) || isset($grn_summary['inspected_by'])): ?>
                                                    <div class="row" style="margin-top: 10px;">
                                                        <div class="col-md-3">
                                                            <strong>Inspection Status:</strong><br>
                                                            <?php
                                                            $status = $grn_summary['inspection_status'] ?? 'PENDING';
                                                            $badge_class = '';
                                                            switch ($status) {
                                                                case 'PASSED':
                                                                    $badge_class = 'badge-success';
                                                                    break;
                                                                case 'PARTIAL':
                                                                    $badge_class = 'badge-warning';
                                                                    break;
                                                                case 'FAILED':
                                                                    $badge_class = 'badge-danger';
                                                                    break;
                                                                default:
                                                                    $badge_class = 'badge-info';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>Quality Rating:</strong><br>
                                                            <?php
                                                            $quality = $grn_summary['quality_rating'] ?? 'GOOD';
                                                            $quality_class = '';
                                                            switch ($quality) {
                                                                case 'EXCELLENT':
                                                                    $quality_class = 'badge-success';
                                                                    break;
                                                                case 'GOOD':
                                                                    $quality_class = 'badge-info';
                                                                    break;
                                                                case 'FAIR':
                                                                    $quality_class = 'badge-warning';
                                                                    break;
                                                                case 'POOR':
                                                                    $quality_class = 'badge-danger';
                                                                    break;
                                                                default:
                                                                    $quality_class = 'badge-secondary';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $quality_class; ?>"><?php echo $quality; ?></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>Inspected By:</strong><br>
                                                            <span class="text-primary"><?php echo $grn_summary['inspector_name'] ?? 'N/A'; ?></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>Inspection Date:</strong><br>
                                                            <span class="text-primary"><?php echo !empty($grn_summary['inspection_date']) ? date('d-m-Y', strtotime($grn_summary['inspection_date'])) : 'N/A'; ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Inspection Details Table -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Item-wise Inspection Details</h3>
                                                </div>
                                                <div class="box-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr class="bg-primary">
                                                                    <th>#</th>
                                                                    <th>Item Code</th>
                                                                    <th>Item Name</th>
                                                                    <th>Ordered Qty</th>
                                                                    <th>Delivered Qty</th>
                                                                    <th>Accepted Qty</th>
                                                                    <th>Rejected Qty</th>
                                                                    <th>Quality</th>
                                                                    <th>Packaging</th>
                                                                    <th>Status</th>
                                                                    <th>Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $i = 1;
                                                                $total_ordered = 0;
                                                                $total_delivered = 0;
                                                                $total_accepted = 0;
                                                                $total_rejected = 0;

                                                                foreach ($inspection_details as $item):
                                                                    $ordered_qty = $item->quantity ?? 0;
                                                                    $delivered_qty = $ordered_qty; // Assuming all ordered were delivered
                                                                    $accepted_qty = $item->received_quantity ?? 0;
                                                                    $rejected_qty = $item->rejected_quantity ?? 0;

                                                                    $total_ordered += $ordered_qty;
                                                                    $total_delivered += $delivered_qty;
                                                                    $total_accepted += $accepted_qty;
                                                                    $total_rejected += $rejected_qty;

                                                                    // Determine row class
                                                                    $row_class = '';
                                                                    if ($rejected_qty > 0 && $accepted_qty > 0) {
                                                                        $row_class = 'warning';
                                                                    } elseif ($rejected_qty == $ordered_qty) {
                                                                        $row_class = 'danger';
                                                                    } elseif ($accepted_qty == $ordered_qty) {
                                                                        $row_class = 'success';
                                                                    }

                                                                    // Quality badge
                                                                    $quality = $item->quality_rating ?? 'GOOD';
                                                                    $quality_class = '';
                                                                    switch ($quality) {
                                                                        case 'EXCELLENT':
                                                                            $quality_class = 'success';
                                                                            break;
                                                                        case 'GOOD':
                                                                            $quality_class = 'info';
                                                                            break;
                                                                        case 'FAIR':
                                                                            $quality_class = 'warning';
                                                                            break;
                                                                        case 'POOR':
                                                                            $quality_class = 'danger';
                                                                            break;
                                                                    }

                                                                    // Packaging badge
                                                                    $packaging = $item->packaging_condition ?? 'INTACT';
                                                                    $packaging_class = '';
                                                                    switch ($packaging) {
                                                                        case 'INTACT':
                                                                            $packaging_class = 'success';
                                                                            break;
                                                                        case 'MINOR_DAMAGE':
                                                                            $packaging_class = 'warning';
                                                                            break;
                                                                        case 'MAJOR_DAMAGE':
                                                                            $packaging_class = 'danger';
                                                                            break;
                                                                    }

                                                                    // Status badge
                                                                    $status = '';
                                                                    $status_class = '';
                                                                    if ($accepted_qty == $ordered_qty && $rejected_qty == 0) {
                                                                        $status = 'FULLY ACCEPTED';
                                                                        $status_class = 'success';
                                                                    } elseif ($accepted_qty > 0 && $rejected_qty > 0) {
                                                                        $status = 'PARTIALLY ACCEPTED';
                                                                        $status_class = 'warning';
                                                                    } elseif ($accepted_qty == 0 && $rejected_qty > 0) {
                                                                        $status = 'REJECTED';
                                                                        $status_class = 'danger';
                                                                    } else {
                                                                        $status = 'PENDING';
                                                                        $status_class = 'info';
                                                                    }
                                                                ?>
                                                                    <tr class="<?php echo $row_class; ?>">
                                                                        <td><?php echo $i++; ?></td>
                                                                        <td><strong><?php echo $item->product_name ?? 'N/A'; ?></strong></td>
                                                                        <td><?php echo $item->item_name ?? 'N/A'; ?></td>
                                                                        <td class="text-center"><?php echo $ordered_qty; ?></td>
                                                                        <td class="text-center"><?php echo $delivered_qty; ?></td>
                                                                        <td class="text-center text-success"><strong><?php echo $accepted_qty; ?></strong></td>
                                                                        <td class="text-center text-danger"><strong><?php echo $rejected_qty; ?></strong></td>
                                                                        <td class="text-center">
                                                                            <span class="label label-<?php echo $quality_class; ?>">
                                                                                <?php echo $quality; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="label label-<?php echo $packaging_class; ?>">
                                                                                <?php echo $packaging; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="label label-<?php echo $status_class; ?>">
                                                                                <?php echo $status; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <?php if (!empty($item->rejection_reason)): ?>
                                                                                <small class="text-danger">
                                                                                    <i class="fa fa-exclamation-triangle"></i>
                                                                                    <?php echo $item->rejection_reason; ?>
                                                                                </small>
                                                                            <?php endif; ?>

                                                                            <?php if (!empty($item->inspection_notes)): ?>
                                                                                <br><small class="text-info">
                                                                                    <i class="fa fa-info-circle"></i>
                                                                                    <?php echo $item->inspection_notes; ?>
                                                                                </small>
                                                                            <?php endif; ?>

                                                                            <?php if (!empty($item->batch_number)): ?>
                                                                                <br><small class="text-muted">
                                                                                    <i class="fa fa-barcode"></i> Batch: <?php echo $item->batch_number; ?>
                                                                                </small>
                                                                            <?php endif; ?>

                                                                            <?php if (!empty($item->expiry_date) && $item->expiry_date != '0000-00-00'): ?>
                                                                                <br><small class="text-muted">
                                                                                    <i class="fa fa-calendar"></i> Expiry: <?php echo date('d-m-Y', strtotime($item->expiry_date)); ?>
                                                                                </small>
                                                                            <?php endif; ?>

                                                                            <?php if (!empty($item->storage_location)): ?>
                                                                                <br><small class="text-muted">
                                                                                    <i class="fa fa-archive"></i> Storage: <?php echo $item->storage_location; ?>
                                                                                </small>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                            <tfoot class="bg-gray">
                                                                <tr>
                                                                    <th colspan="3" class="text-right">TOTALS:</th>
                                                                    <th class="text-center"><?php echo $total_ordered; ?></th>
                                                                    <th class="text-center"><?php echo $total_delivered; ?></th>
                                                                    <th class="text-center text-success"><?php echo $total_accepted; ?></th>
                                                                    <th class="text-center text-danger"><?php echo $total_rejected; ?></th>
                                                                    <th colspan="4"></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Statistics -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box bg-green">
                                                <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Accepted Items</span>
                                                    <span class="info-box-number"><?php echo $total_accepted; ?></span>
                                                    <div class="progress">
                                                        <?php
                                                        $acceptance_rate = ($total_delivered > 0) ? ($total_accepted / $total_delivered) * 100 : 0;
                                                        ?>
                                                        <div class="progress-bar" style="width: <?php echo $acceptance_rate; ?>%"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                        <?php echo number_format($acceptance_rate, 1); ?>% of delivered quantity
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box bg-red">
                                                <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Rejected Items</span>
                                                    <span class="info-box-number"><?php echo $total_rejected; ?></span>
                                                    <div class="progress">
                                                        <?php
                                                        $rejection_rate = ($total_delivered > 0) ? ($total_rejected / $total_delivered) * 100 : 0;
                                                        ?>
                                                        <div class="progress-bar" style="width: <?php echo $rejection_rate; ?>%"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                        <?php echo number_format($rejection_rate, 1); ?>% of delivered quantity
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box-footer">
                                                <div class="btn-group">
                                                    <a href="<?php echo base_url(); ?>GrnController/show_grn/<?php echo $grn_summary['grn_number'] ?? ''; ?>"
                                                        class="btn btn-primary">
                                                        <i class="fa fa-eye"></i> View Full GRN
                                                    </a>
                                                    <a href="<?php echo base_url(); ?>GrnController/grn_index"
                                                        class="btn btn-default">
                                                        <i class="fa fa-list"></i> Back to GRN List
                                                    </a>
                                                    <button type="button" class="btn btn-success" onclick="window.print()">
                                                        <i class="fa fa-print"></i> Print Report
                                                    </button>
                                                </div>

                                                <div class="pull-right">
                                                    <small class="text-muted">
                                                        <i class="fa fa-clock-o"></i> Report Generated: <?php echo date('d-m-Y H:i:s'); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endif; // End of if inspection_details exists 
                                ?>

                            </div>
                            <!-- /.box-body -->
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

    <!-- Add CSS for better appearance -->
    <style>
        .progress-group {
            margin-bottom: 20px;
        }

        .info-box {
            min-height: 90px;
            margin-bottom: 10px;
        }

        .badge {
            padding: 5px 10px;
            font-size: 12px;
        }

        .callout {
            border-left: 5px solid #00c0ef;
        }

        .label {
            padding: 5px 10px;
            font-size: 12px;
        }

        @media print {

            .box-header,
            .box-tools,
            .btn,
            .breadcrumb,
            .content-header {
                display: none !important;
            }

            .box {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</body>

</html>