<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
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
                    Approval History
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>">Approval Dashboard</a></li>
                    <li class="active">Approval History</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <!-- PR Summary -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">PR: <?php echo $requisition->pr_no ?? 'N/A'; ?></h3>
                                <div class="box-tools pull-right">
                                    <span class="label label-<?php
                                                                echo ($requisition->approval_status == 'Approved') ? 'success' : (($requisition->approval_status == 'Pending') ? 'warning' : 'danger');
                                                                ?>">
                                        <?php echo $requisition->approval_status; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Department:</strong> <?php echo $requisition->department_name; ?></p>
                                        <p><strong>Requested By:</strong> <?php echo $requisition->requester_name; ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>PR Date:</strong> <?php echo date('d-m-Y', strtotime($requisition->pr_date)); ?></p>
                                        <p><strong>Total Value:</strong> ₹<?php echo number_format($requisition->total_value, 2); ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Urgency:</strong>
                                            <span class="label label-<?php
                                                                        echo ($requisition->urgency_level == 'High') ? 'danger' : (($requisition->urgency_level == 'Medium') ? 'warning' : 'info');
                                                                        ?>">
                                                <?php echo $requisition->urgency_level; ?>
                                            </span>
                                        </p>
                                        <p><strong>Remarks:</strong> <?php echo $requisition->remarks; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Progress -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Approval Progress</h3>
                            </div>
                            <div class="box-body">
                                <div class="progress-group">
                                    <?php foreach ($approval_progress['levels'] as $level): ?>
                                        <?php if ($level['applicable']): ?>
                                            <div class="progress-group">
                                                <span class="progress-text">
                                                    Level <?php echo $level['level']; ?> - <?php echo $level['role']; ?>
                                                    <?php if ($level['approved_date']): ?>
                                                        <span class="text-success">
                                                            <i class="fa fa-check"></i> Approved on <?php echo date('d-m-Y', strtotime($level['approved_date'])); ?>
                                                        </span>
                                                    <?php elseif ($level['status'] == 'current'): ?>
                                                        <span class="text-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                                    <?php else: ?>
                                                        <span class="text-muted"><i class="fa fa-clock-o"></i> Waiting</span>
                                                    <?php endif; ?>
                                                </span>
                                                <div class="progress sm">
                                                    <div class="progress-bar progress-bar-<?php
                                                                                            echo ($level['status'] == 'approved') ? 'success' : (($level['status'] == 'current') ? 'warning' : 'default');
                                                                                            ?>" style="width: <?php
                                                                                                                echo ($level['status'] == 'approved') ? '100' : (($level['status'] == 'current') ? '50' : '0');
                                                                                                                ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <p class="text-center">
                                    <strong>Overall Progress: <?php echo round($approval_progress['percentage'], 2); ?>%</strong>
                                    (<?php echo $approval_progress['completed_levels']; ?> of <?php echo $approval_progress['total_levels']; ?> levels completed)
                                </p>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Approval Timeline</h3>
                            </div>
                            <div class="box-body">
                                <ul class="timeline">
                                    <?php foreach ($timeline as $event): ?>
                                        <li class="time-label">
                                            <span class="bg-<?php
                                                            echo ($event['status'] == 'Approved' || strpos($event['status'], 'Approved') !== false) ? 'green' : (($event['status'] == 'Rejected') ? 'red' : 'blue');
                                                            ?>">
                                                <?php echo date('d M Y', strtotime($event['date'])); ?>
                                            </span>
                                        </li>
                                        <li>
                                            <i class="fa fa-<?php
                                                            echo (strpos($event['event'], 'Approved') !== false) ? 'check' : (($event['event'] == 'Rejected') ? 'times' : (($event['event'] == 'Submitted') ? 'paper-plane' : 'file'));
                                                            ?> bg-<?php
                                                                    echo ($event['status'] == 'Approved' || strpos($event['status'], 'Approved') !== false) ? 'green' : (($event['status'] == 'Rejected') ? 'red' : 'blue');
                                                                    ?>"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="fa fa-clock-o"></i> <?php echo date('H:i', strtotime($event['date'])); ?></span>
                                                <h3 class="timeline-header">
                                                    <strong><?php echo $event['event']; ?></strong>
                                                    <?php if ($event['user'] != 'System'): ?>
                                                        by <?php echo $event['user']; ?>
                                                    <?php endif; ?>
                                                </h3>
                                                <div class="timeline-body">
                                                    <?php echo $event['description']; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                    <li>
                                        <i class="fa fa-clock-o bg-gray"></i>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Detailed History Table -->
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Detailed Approval History</h3>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Level</th>
                                            <th>Approver Role</th>
                                            <th>Approver Name</th>
                                            <th>Action</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($approval_history as $history): ?>
                                            <tr>
                                                <td><?php echo date('d-m-Y H:i:s', strtotime($history->action_date)); ?></td>
                                                <td>Level <?php echo $history->approval_level; ?></td>
                                                <td><?php echo $history->approver_role; ?></td>
                                                <td><?php echo html_escape($history->approver_name) . (!empty($history->approver_actual_role) ? ' (' . html_escape($history->approver_actual_role) . ')' : ''); ?></td>
                                                <td>
                                                    <span class="label label-<?php
                                                                                echo ($history->action == 'Approved') ? 'success' : (($history->action == 'Rejected') ? 'danger' : 'warning');
                                                                                ?>">
                                                        <?php echo $history->action; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $history->comments; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer">
                                <a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>"
                                    class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                                </a>
                                <a href="<?php echo base_url('RequisitionController/show_requisition/') . $requisition->pr_id; ?>"
                                    class="btn btn-info pull-right">
                                    <i class="fa fa-eye"></i> View Full PR Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
</body>