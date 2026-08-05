<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];
defined('BASEPATH') or exit('No direct script access allowed');

// Get user ID from session
$user_id = $session_data_head['result']['user_id'] ?? null;
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Approval Dashboard
                    <small>Manage purchase requisition approvals</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Purchase Requisition</a></li>
                    <li class="active">Approval Dashboard</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <!-- Flash Messages -->
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

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pending Approvals </span>
                                <span class="info-box-number"><?php echo $stats['pending_approvals'] ?? 0; ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 70%"></div>
                                </div>
                                <span class="progress-description">
                                    Waiting for your review
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">My Approved PRs</span>
                                <span class="info-box-number"><?php echo $stats['my_approved_prs'] ?? 0; ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 50%"></div>
                                </div>
                                <span class="progress-description">
                                    Approved by you
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-yellow">
                            <span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">My Pending PRs</span>
                                <span class="info-box-number"><?php echo $stats['my_pending_prs'] ?? 0; ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 40%"></div>
                                </div>
                                <span class="progress-description">
                                    Waiting for approval
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">My Rejected PRs</span>
                                <span class="info-box-number"><?php echo $stats['my_rejected_prs'] ?? 0; ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 20%"></div>
                                </div>
                                <span class="progress-description">
                                    Rejected PRs
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Main Content Row: Table on Left (col-md-8), Widgets on Right (col-md-4) -->
                <div class="row">
                    <!-- Left Column: Pending Approvals Table (col-md-8) -->
                    <div class="col-md-8">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-clock-o"></i> Pending Approvals
                                    <span class="badge bg-blue" style="margin-left: 5px;"><?php echo count($pending_approvals); ?></span>
                                </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body" style="padding: 10px; overflow-x: auto !important; max-width: 100% !important;">
                                <?php if (!empty($pending_approvals)): ?>
                                    <div class="table-responsive" style="overflow-x: auto !important; width: 100% !important; max-width: 100% !important; display: block;">
                                     <table id="pending_approvals_table" class="table table-bordered table-striped table-hover" style="width:100% !important; font-size: 11px;">
                                        <thead>
                                            <tr style="background-color: #3c8dbc; color: #fff;">
                                                <th style="white-space: nowrap; width: 14%;">PR No</th>
                                                <th style="width: 9%;">Location</th>
                                                <th style="width: 10%;">Dept</th>
                                                <th style="width: 13%;">Requester</th>
                                                <th style="white-space: nowrap; width: 11%;">Total Value</th>
                                                <th style="white-space: nowrap; width: 12%;">Submitted</th>
                                                <th style="white-space: nowrap; width: 8%;">Level</th>
                                                <th style="white-space: nowrap; width: 13%;">Approver Role</th>
                                                <th style="white-space: nowrap; width: 10%; text-align: center;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pending_approvals as $approval): ?>
                                                <?php
                                                // Get applicable approval matrix for this PR
                                                $applicable_levels = $this->requisition->get_applicable_approval_levels($approval->total_value);
                                                $current_approver = null;
                                                foreach ($applicable_levels as $level) {
                                                    if ($level->level == $approval->approval_level) {
                                                        $current_approver = $level;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?php echo base_url('RequisitionController/show_requisition/') . $approval->pr_id; ?>">
                                                            <strong><?php echo $approval->pr_no ?? 'N/A'; ?></strong>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $approval->location_name ?? 'N/A'; ?></td>
                                                    <td><?php echo $approval->department_name ?? 'N/A'; ?></td>
                                                    <td><small><?php echo $approval->requester_name ?? 'N/A'; ?></small></td>
                                                    <td style="white-space: nowrap;">
                                                        <strong>₹<?php echo number_format($approval->total_value ?? 0, 2); ?></strong>
                                                    </td>
                                                    <td style="white-space: nowrap;" data-order="<?php echo strtotime($approval->submitted_for_approval); ?>">
                                                         <?php echo date('d-m-Y', strtotime($approval->submitted_for_approval)); ?>
                                                         <br>
                                                         <small class="text-muted"><?php echo date('H:i', strtotime($approval->submitted_for_approval)); ?></small>
                                                     </td>
                                                    <td>
                                                        <span class="label label-<?php
                                                                                    echo ($approval->approval_level == 1) ? 'warning' : (($approval->approval_level == 2) ? 'primary' : (($approval->approval_level == 3) ? 'info' : 'default'));
                                                                                    ?>">
                                                            L<?php echo $approval->approval_level ?? '1'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($current_approver): ?>
                                                            <span class="label label-success" style="font-size:10px;"><?php echo $current_approver->approver_role; ?></span>
                                                            <?php
                                                            $role_name = $session_data_head['result']['role_name'] ?? '';
                                                            $is_admin = (strtolower($role_name) === 'admin');
                                                            $user_roles = $this->requisition->get_user_roles($user_id);
                                                            if ($is_admin || in_array($current_approver->approver_role, $user_roles)): ?>
                                                                <br><span class="label label-warning" style="margin-top:2px; font-size:9px; display:inline-block;"><i class="fa fa-user"></i> <?php echo $is_admin ? 'Admin' : 'You'; ?></span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td style="text-align: center; white-space: nowrap;">
                                                        <div class="btn-group">
                                                            <a href="<?php echo base_url('RequisitionController/view_for_approval/') . $approval->pr_id; ?>"
                                                                class="btn btn-xs btn-primary" title="Review PR">
                                                                <i class="fa fa-eye"></i> Review
                                                            </a>
                                                            <a href="<?php echo base_url('RequisitionController/view_approval_history/') . $approval->pr_id; ?>"
                                                                class="btn btn-xs btn-info" title="View History">
                                                                <i class="fa fa-history"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                     </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No pending approvals at the moment.
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($pending_approvals)): ?>
                                <div class="box-footer clearfix">
                                    <div class="pull-left">
                                        <p class="text-muted" style="margin: 5px 0 0;">
                                            Showing <?php echo count($pending_approvals); ?> pending approvals
                                        </p>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo base_url('RequisitionController/all_requisitions?workflow_status=L1_Pending&workflow_status=L2_Pending&workflow_status=L3_Pending'); ?>"
                                            class="btn btn-sm btn-default">
                                            <i class="fa fa-search"></i> View All
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Column: Recent Activity, Workflow Status, Approval Matrix (col-md-4) -->
                    <div class="col-md-4">
                        <!-- Recent Activity -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-history"></i> Recent Activity</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($recent_prs)): ?>
                                    <ul class="products-list product-list-in-box">
                                        <?php foreach ($recent_prs as $pr): ?>
                                            <li class="item">
                                                <div class="product-img">
                                                    <?php
                                                    $icon_class = '';
                                                    $icon_color = '';
                                                    switch ($pr->approval_status) {
                                                        case 'Approved':
                                                            $icon_class = 'fa-check';
                                                            $icon_color = 'text-green';
                                                            break;
                                                        case 'Rejected':
                                                            $icon_class = 'fa-times';
                                                            $icon_color = 'text-red';
                                                            break;
                                                        default:
                                                            $icon_class = 'fa-file-text-o';
                                                            $icon_color = 'text-blue';
                                                            break;
                                                    }
                                                    ?>
                                                    <i class="fa <?php echo $icon_class; ?> fa-2x <?php echo $icon_color; ?>"></i>
                                                </div>
                                                <div class="product-info">
                                                    <a href="<?php echo base_url('RequisitionController/show_requisition/') . $pr->pr_id; ?>"
                                                        class="product-title">
                                                        <?php echo $pr->pr_no ?? 'N/A'; ?>
                                                        <span class="label label-<?php
                                                                                    echo ($pr->approval_status == 'Approved') ? 'success' : (($pr->approval_status == 'Pending') ? 'warning' : 'danger');
                                                                                    ?> pull-right">
                                                            <?php echo $pr->approval_status; ?>
                                                        </span>
                                                    </a>
                                                    <span class="product-description">
                                                        <i class="fa fa-building"></i> <?php echo $pr->department_name ?? 'N/A'; ?><br>
                                                        <i class="fa fa-money"></i> ₹<?php echo number_format($pr->total_value ?? 0, 2); ?><br>
                                                        <i class="fa fa-calendar"></i> <?php echo date('d-m-Y', strtotime($pr->pr_date)); ?>
                                                    </span>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No recent activity.
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($recent_prs)): ?>
                                <div class="box-footer text-center">
                                    <a href="<?php echo base_url('RequisitionController/all_requisitions'); ?>"
                                        class="btn btn-sm btn-default">
                                        View All Activity
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Workflow Status -->
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-pie-chart"></i> Workflow Status</h3>
                                <div class="box-tools pull-right">
                                    <span class="badge bg-green"><?php echo array_sum($stats['workflow_counts'] ?? []); ?> Total</span>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="progress-group">
                                    <span class="progress-text">Draft</span>
                                    <span class="progress-number"><b><?php echo $stats['workflow_counts']['Draft'] ?? 0; ?></b></span>
                                    <div class="progress sm">
                                        <div class="progress-bar progress-bar-gray"
                                            style="width: <?php echo min(($stats['workflow_counts']['Draft'] ?? 0) * 10, 100); ?>%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Submitted</span>
                                    <span class="progress-number"><b><?php echo $stats['workflow_counts']['Submitted'] ?? 0; ?></b></span>
                                    <div class="progress sm">
                                        <div class="progress-bar progress-bar-aqua"
                                            style="width: <?php echo min(($stats['workflow_counts']['Submitted'] ?? 0) * 10, 100); ?>%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Level 1 Pending</span>
                                    <span class="progress-number"><b><?php echo $stats['workflow_counts']['L1_Pending'] ?? 0; ?></b></span>
                                    <div class="progress sm">
                                        <div class="progress-bar progress-bar-yellow"
                                            style="width: <?php echo min(($stats['workflow_counts']['L1_Pending'] ?? 0) * 10, 100); ?>%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Level 2 Pending</span>
                                    <span class="progress-number"><b><?php echo $stats['workflow_counts']['L2_Pending'] ?? 0; ?></b></span>
                                    <div class="progress sm">
                                        <div class="progress-bar progress-bar-orange"
                                            style="width: <?php echo min(($stats['workflow_counts']['L2_Pending'] ?? 0) * 10, 100); ?>%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Level 3 Pending</span>
                                    <span class="progress-number"><b><?php echo $stats['workflow_counts']['L3_Pending'] ?? 0; ?></b></span>
                                    <div class="progress sm">
                                        <div class="progress-bar progress-bar-purple"
                                            style="width: <?php echo min(($stats['workflow_counts']['L3_Pending'] ?? 0) * 10, 100); ?>%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Approved</span>
                                    <span class="progress-number"><b><?php echo $stats['workflow_counts']['Approved'] ?? 0; ?></b></span>
                                    <div class="progress sm">
                                        <div class="progress-bar progress-bar-green"
                                            style="width: <?php echo min(($stats['workflow_counts']['Approved'] ?? 0) * 10, 100); ?>%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Rejected</span>
                                    <span class="progress-number"><b><?php echo $stats['workflow_counts']['Rejected'] ?? 0; ?></b></span>
                                    <div class="progress sm">
                                        <div class="progress-bar progress-bar-red"
                                            style="width: <?php echo min(($stats['workflow_counts']['Rejected'] ?? 0) * 10, 100); ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Matrix Info -->
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-sitemap"></i> Approval Matrix</h3>
                            </div>
                            <div class="box-body">
                                <?php
                                // Get approval matrix for display
                                $approval_matrix = $this->requisition->get_pr_approval_matrix();
                                if (!empty($approval_matrix)):
                                ?>
                                    <table class="table table-condensed table-striped" style="font-size: 11px;">
                                        <thead>
                                            <tr>
                                                <th>Level</th>
                                                <th>Role</th>
                                                <th>Amount Range</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($approval_matrix as $matrix): ?>
                                                <tr>
                                                    <td><span class="label label-default">L<?php echo $matrix->level; ?></span></td>
                                                    <td><strong><?php echo $matrix->approver_role; ?></strong></td>
                                                    <td>
                                                        ₹<?php echo number_format($matrix->min_amount, 0); ?>
                                                        -
                                                        <?php echo $matrix->max_amount == 0 ? '∞' : '₹' . number_format($matrix->max_amount, 0); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <p class="text-muted" style="font-size: 11px; margin-top: 5px;">
                                        <i class="fa fa-info-circle"></i> PRs are routed dynamically by total value.
                                    </p>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-warning"></i> No approval matrix defined.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>        </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Quick Actions</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <a href="<?php echo base_url('RequisitionController/create_purchase_requisition'); ?>"
                                            class="btn btn-app bg-green">
                                            <i class="fa fa-plus"></i> Create PR
                                        </a>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <a href="<?php echo base_url('RequisitionController/my_requisitions'); ?>"
                                            class="btn btn-app bg-yellow">
                                            <i class="fa fa-user"></i> My PRs
                                        </a>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <a href="<?php echo base_url('RequisitionController/all_requisitions'); ?>"
                                            class="btn btn-app bg-purple">
                                            <i class="fa fa-search"></i> All PRs
                                        </a>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <a href="<?php echo base_url('RequisitionController/view_requisition_order'); ?>"
                                            class="btn btn-app bg-aqua">
                                            <i class="fa fa-list"></i> PR List
                                        </a>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>"
                                            class="btn btn-app bg-blue active">
                                            <i class="fa fa-tasks"></i> Dashboard
                                        </a>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <button type="button" class="btn btn-app bg-red" onclick="refreshDashboard()">
                                            <i class="fa fa-refresh"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- JavaScript -->
    <script>
        $(document).ready(function() {
            // Explicitly initialize pending approvals DataTable in descending order of submission
            if ($.fn.DataTable) {
                $('#pending_approvals_table').DataTable({
                    "order": [[ 5, "desc" ]],
                    "pageLength": 25,
                    "autoWidth": false,
                    "columnDefs": [
                        { "orderable": false, "targets": -1 }
                    ],
                    "language": {
                        "search": "Search Pending Approvals:",
                        "lengthMenu": "_MENU_ entries per page"
                    }
                });
            }



            // Auto-refresh notifications every 60 seconds
            setInterval(function() {
                loadNotifications();
            }, 60000);

            // Load initial notifications
            loadNotifications();

            // Initialize tooltips
            $('[title]').tooltip();
        });

        function loadNotifications() {
            $.ajax({
                url: '<?php echo base_url("RequisitionController/get_notifications"); ?>',
                type: 'GET',
                success: function(response) {
                    try {
                        var data = JSON.parse(response);
                        if (data.length > 0) {
                            // Update notification badge
                            var badge = $('#notification-count');
                            if (badge.length === 0) {
                                // Create badge if doesn't exist
                                $('.sidebar-menu a[href*="approval_dashboard"]').append(
                                    '<span class="pull-right-container">' +
                                    '<span id="notification-badge" class="label label-danger">' + data.length + '</span>' +
                                    '</span>'
                                );
                            } else {
                                badge.text(data.length).show();
                            }

                            // Show notification toast if new items
                            if (data.length > 0) {
                                showNotificationToast('New PRs pending approval', 'You have ' + data.length + ' new PR(s) waiting for your review.');
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing notifications:', e);
                    }
                },
                error: function() {
                    console.error('Failed to load notifications');
                }
            });
        }

        function refreshDashboard() {
            // Show loading
            $('#loader').show();

            // Reload the page after 1 second
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        }

        function showNotificationToast(title, message) {
            // Create toast notification
            var toast = $('<div class="alert alert-info alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 300px;">' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '<h4><i class="icon fa fa-bell"></i> ' + title + '</h4>' +
                '<p>' + message + '</p>' +
                '</div>');

            // Add to body
            $('body').append(toast);

            // Auto remove after 5 seconds
            setTimeout(function() {
                toast.alert('close');
            }, 5000);
        }

        // Update dashboard stats via AJAX
        function updateDashboardStats() {
            $.ajax({
                url: '<?php echo base_url("RequisitionController/get_approval_stats"); ?>',
                type: 'GET',
                success: function(stats) {
                    // Update stats if needed
                    console.log('Stats updated:', stats);

                    // You can update specific elements here if needed
                    // Example: $('#pending-approvals-count').text(stats.pending_approvals);
                },
                error: function() {
                    console.error('Failed to update stats');
                }
            });
        }
    </script>

    <style>
        .progress-bar-gray {
            background-color: #6c757d;
        }

        .progress-bar-aqua {
            background-color: #17a2b8;
        }

        .progress-bar-yellow {
            background-color: #ffc107;
            color: #000;
        }

        .progress-bar-orange {
            background-color: #fd7e14;
        }

        .progress-bar-purple {
            background-color: #6f42c1;
        }

        .progress-bar-green {
            background-color: #28a745;
        }

        .progress-bar-red {
            background-color: #dc3545;
        }

        .products-list .product-img {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .products-list>.item {
            border-bottom: 1px solid #f4f4f4;
            padding: 10px 0;
        }

        .products-list>.item:last-child {
            border-bottom: none;
        }

        .product-description {
            display: block;
            font-size: 12px;
            color: #666;
            line-height: 1.6;
        }

        .table-condensed th,
        .table-condensed td {
            padding: 5px;
            font-size: 12px;
        }

        .btn-app.active {
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
        }

        /* Strict Table Container Isolation for col-md-8 */
        .col-md-8 .box {
            overflow: hidden !important;
        }

        #pending_approvals_table_wrapper {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            clear: both;
        }

        #pending_approvals_table {
            width: 100% !important;
            margin: 0 !important;
        }

        #pending_approvals_table_wrapper .dataTables_filter,
        #pending_approvals_table_wrapper .dataTables_length,
        #pending_approvals_table_wrapper .dataTables_info,
        #pending_approvals_table_wrapper .dataTables_paginate {
            max-width: 100% !important;
        }
    </style>
</body>