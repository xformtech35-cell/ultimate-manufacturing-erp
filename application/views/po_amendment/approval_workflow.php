<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Approval Workflow</title>
    <!-- Add your CSS includes here -->
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Approval Workflow</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('PoamendmentController/index'); ?>">PO Amendments</a></li>
                    <li><a href="<?php echo base_url('PoamendmentController/view/' . $amendment['amendment_id']); ?>">Amendment Details</a></li>
                    <li class="active">Approval Workflow</li>
                </ol>
            </section>

            <section class="content">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i> Success!</h4>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Amendment Summary -->
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Amendment Summary</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Amendment No:</strong> <?php echo $amendment['amendment_no']; ?></p>
                                        <p><strong>PO Number:</strong> <?php echo $amendment['po_number']; ?></p>
                                        <p><strong>Vendor:</strong>
                                            <?php echo !empty($amendment['po_details']['company_name']) ? $amendment['po_details']['company_name'] : 'N/A'; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Amendment Type:</strong>
                                            <?php echo ucfirst(str_replace('_', ' ', $amendment['amendment_type'])); ?>
                                        </p>
                                        <p><strong>Amendment Value:</strong>
                                            ₹<?php echo !empty($amendment['amendment_value']) ? number_format($amendment['amendment_value'], 2) : '0.00'; ?>
                                        </p>
                                        <p><strong>Initiated By:</strong>
                                            <?php echo !empty($amendment['initiated_by_name']) ? $amendment['initiated_by_name'] : 'User ID: ' . $amendment['initiated_by']; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Status:</strong>
                                            <span class="label label-<?php
                                                                        $status_labels = array(
                                                                            'draft' => 'default',
                                                                            'pending_approval' => 'warning',
                                                                            'approved' => 'info',
                                                                            'vendor_acknowledged' => 'primary',
                                                                            'revised_po_issued' => 'success',
                                                                            'completed' => 'success',
                                                                            'cancelled' => 'danger'
                                                                        );
                                                                        echo isset($status_labels[$amendment['status']]) ? $status_labels[$amendment['status']] : 'default';
                                                                        ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $amendment['status'])); ?>
                                            </span>
                                        </p>
                                        <p><strong>Initiated Date:</strong>
                                            <?php echo date('d-m-Y H:i', strtotime($amendment['initiated_date'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Workflow Visualization -->
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Approval Workflow</h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($workflow)): ?>
                                    <div class="timeline">
                                        <?php foreach ($workflow as $step): ?>
                                            <!-- timeline item -->
                                            <div class="timeline-item">
                                                <div class="timeline-point timeline-point-<?php echo $step['status_class']; ?>">
                                                    <i class="fa fa-<?php echo $step['icon']; ?>"></i>
                                                </div>
                                                <div class="timeline-event">
                                                    <div class="timeline-heading">
                                                        <h4>
                                                            <?php echo ucfirst(str_replace('_', ' ', $step['approval_level'])); ?>
                                                            <?php if ($step['status'] == 'approved'): ?>
                                                                <span class="label label-success pull-right">Approved</span>
                                                            <?php elseif ($step['status'] == 'rejected'): ?>
                                                                <span class="label label-danger pull-right">Rejected</span>
                                                            <?php else: ?>
                                                                <span class="label label-warning pull-right">Pending</span>
                                                            <?php endif; ?>
                                                        </h4>
                                                    </div>
                                                    <div class="timeline-body">
                                                        <p><strong>Approver:</strong> <?php echo $step['approver_email']; ?></p>
                                                        <?php if (!empty($step['approver_name'])): ?>
                                                            <p><strong>Name:</strong> <?php echo $step['approver_name']; ?></p>
                                                        <?php endif; ?>
                                                        <?php if (!empty($step['approver_role'])): ?>
                                                            <p><strong>Role:</strong> <?php echo $step['approver_role']; ?></p>
                                                        <?php endif; ?>

                                                        <?php if (!empty($step['action_date'])): ?>
                                                            <p><strong>Action Date:</strong> <?php echo date('d-m-Y H:i', strtotime($step['action_date'])); ?></p>
                                                            <p><strong>Action By:</strong> <?php echo $step['action_by']; ?></p>
                                                            <p><strong>Remarks:</strong> <?php echo $step['remarks']; ?></p>
                                                        <?php else: ?>
                                                            <p><strong>Created:</strong> <?php echo date('d-m-Y H:i', strtotime($step['created_at'])); ?></p>
                                                            <p><em>Waiting for approval...</em></p>
                                                            <?php if ($step['can_approve']): ?>
                                                                <a href="<?php echo site_url('PoamendmentController/approval_action/' . $amendment['amendment_id']); ?>"
                                                                    class="btn btn-sm btn-success">
                                                                    <i class="fa fa-check"></i> Take Action
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END timeline item -->
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <p>No approval workflow defined for this amendment.</p>
                                        <?php if ($amendment['status'] == 'draft'): ?>
                                            <p>Submit this amendment for approval to start the workflow.</p>
                                            <a href="<?php echo site_url('PoamendmentController/view/' . $amendment['amendment_id']); ?>#submitApprovalModal"
                                                class="btn btn-success">
                                                <i class="fa fa-paper-plane"></i> Submit for Approval
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Workflow Summary -->
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Workflow Summary</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Pending Steps</span>
                                                <span class="info-box-number">
                                                    <?php
                                                    $pending_count = 0;
                                                    if (!empty($workflow)) {
                                                        foreach ($workflow as $step) {
                                                            if ($step['status'] == 'pending') {
                                                                $pending_count++;
                                                            }
                                                        }
                                                    }
                                                    echo $pending_count;
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Completed Steps</span>
                                                <span class="info-box-number">
                                                    <?php
                                                    $completed_count = 0;
                                                    if (!empty($workflow)) {
                                                        foreach ($workflow as $step) {
                                                            if ($step['status'] == 'approved' || $step['status'] == 'rejected') {
                                                                $completed_count++;
                                                            }
                                                        }
                                                    }
                                                    echo $completed_count;
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-blue"><i class="fa fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Steps</span>
                                                <span class="info-box-number">
                                                    <?php echo !empty($workflow) ? count($workflow) : 0; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($workflow)): ?>
                                    <div class="progress" style="height: 30px;">
                                        <?php
                                        $total_steps = count($workflow);
                                        $completed_steps = 0;
                                        foreach ($workflow as $step) {
                                            if ($step['status'] == 'approved' || $step['status'] == 'rejected') {
                                                $completed_steps++;
                                            }
                                        }
                                        $percentage = $total_steps > 0 ? ($completed_steps / $total_steps) * 100 : 0;
                                        ?>
                                        <div class="progress-bar progress-bar-success progress-bar-striped"
                                            role="progressbar"
                                            style="width: <?php echo $percentage; ?>%"
                                            aria-valuenow="<?php echo $percentage; ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                            <?php echo round($percentage); ?>% Complete
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="box-footer">
                                <a href="<?php echo site_url('PoamendmentController/view/' . $amendment['amendment_id']); ?>"
                                    class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Amendment
                                </a>
                                <a href="<?php echo site_url('PoamendmentController/approvals'); ?>"
                                    class="btn btn-info">
                                    <i class="fa fa-list"></i> View All Approvals
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->

    <!-- Add CSS for timeline -->
    <style>
        .timeline {
            position: relative;
            margin: 0 0 30px 0;
            padding: 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #ddd;
            left: 31px;
            margin: 0;
            border-radius: 2px;
        }

        .timeline-item {
            position: relative;
            margin-right: 10px;
            margin-bottom: 15px;
        }

        .timeline-point {
            position: absolute;
            left: 27px;
            top: 0;
            z-index: 10;
            font-size: 16px;
            line-height: 30px;
            width: 30px;
            height: 30px;
            text-align: center;
            border-radius: 50%;
            color: #fff;
        }

        .timeline-point-success {
            background-color: #00a65a;
        }

        .timeline-point-danger {
            background-color: #dd4b39;
        }

        .timeline-point-warning {
            background-color: #f39c12;
        }

        .timeline-point-info {
            background-color: #00c0ef;
        }

        .timeline-event {
            position: relative;
            padding-left: 70px;
        }

        .timeline-heading {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .timeline-body {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 3px;
            border: 1px solid #f0f0f0;
        }

        .timeline-body p {
            margin-bottom: 5px;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Auto-refresh the page every 30 seconds if there are pending approvals
            var hasPending = <?php echo (!empty($workflow) && $pending_count > 0) ? 'true' : 'false'; ?>;
            if (hasPending) {
                setTimeout(function() {
                    location.reload();
                }, 30000); // 30 seconds
            }
        });
    </script>
</body>

</html>