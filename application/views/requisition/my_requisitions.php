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
                    My Purchase Requisitions
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>">Approval Dashboard</a></li>
                    <li class="active">My Requisitions</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Stats Row -->
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-files-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total PRs</span>
                                <span class="info-box-number"><?php echo $stats['total'] ?? 0; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Approved</span>
                                <span class="info-box-number"><?php echo $stats['approved'] ?? 0; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pending</span>
                                <span class="info-box-number"><?php echo $stats['pending'] ?? 0; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Rejected</span>
                                <span class="info-box-number"><?php echo $stats['rejected'] ?? 0; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter and Create Button -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="box-title">My Requisitions</h3>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <!-- Status Filter -->
                                        <div class="btn-group">
                                            <a href="<?php echo base_url('RequisitionController/my_requisitions'); ?>"
                                                class="btn btn-sm btn-<?php echo !isset($_GET['status']) ? 'primary' : 'default'; ?>">
                                                All
                                            </a>
                                            <a href="<?php echo base_url('RequisitionController/my_requisitions?status=Pending'); ?>"
                                                class="btn btn-sm btn-<?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'primary' : 'default'; ?>">
                                                Pending
                                            </a>
                                            <a href="<?php echo base_url('RequisitionController/my_requisitions?status=Approved'); ?>"
                                                class="btn btn-sm btn-<?php echo (isset($_GET['status']) && $_GET['status'] == 'Approved') ? 'primary' : 'default'; ?>">
                                                Approved
                                            </a>
                                            <a href="<?php echo base_url('RequisitionController/my_requisitions?status=Rejected'); ?>"
                                                class="btn btn-sm btn-<?php echo (isset($_GET['status']) && $_GET['status'] == 'Rejected') ? 'primary' : 'default'; ?>">
                                                Rejected
                                            </a>
                                        </div>
                                        <a href="<?php echo base_url('RequisitionController/create_purchase_requisition'); ?>"
                                            class="btn btn-sm btn-success">
                                            <i class="fa fa-plus"></i> Create New PR
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($requisitions)): ?>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>PR No</th>
                                                <th>Date</th>
                                                <th>Department</th>
                                                <th>Total Value</th>
                                                <th>Urgency</th>
                                                <th>Status</th>
                                                <th>Workflow Status</th>
                                                <th>Current Approver</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($requisitions as $req): ?>
                                                <tr>
                                                    <td><?php echo $req->pr_no ?? 'N/A'; ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($req->pr_date)); ?></td>
                                                    <td><?php echo $req->department_name ?? 'N/A'; ?></td>
                                                    <td>₹<?php echo number_format($req->total_value ?? 0, 2); ?></td>
                                                    <td>
                                                        <span class="label label-<?php
                                                                                    echo ($req->urgency_level == 'High') ? 'danger' : (($req->urgency_level == 'Medium') ? 'warning' : 'info');
                                                                                    ?>">
                                                            <?php echo $req->urgency_level; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-<?php
                                                                                    echo ($req->approval_status == 'Approved') ? 'success' : (($req->approval_status == 'Pending') ? 'warning' : 'danger');
                                                                                    ?>">
                                                            <?php echo $req->approval_status; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($req->workflow_status == 'Draft'): ?>
                                                            <span class="label label-default">Draft</span>
                                                        <?php elseif ($req->workflow_status == 'Submitted'): ?>
                                                            <span class="label label-primary">Submitted</span>
                                                        <?php elseif (strpos($req->workflow_status, 'Pending') !== false): ?>
                                                            <span class="label label-warning"><?php echo $req->workflow_status; ?></span>
                                                        <?php elseif ($req->workflow_status == 'Approved'): ?>
                                                            <span class="label label-success">Approved</span>
                                                        <?php elseif ($req->workflow_status == 'Rejected'): ?>
                                                            <span class="label label-danger">Rejected</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $req->current_approver_role ?? 'N/A'; ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="<?php echo base_url('RequisitionController/show_requisition/') . $req->pr_id; ?>"
                                                                class="btn btn-xs btn-info" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <?php if ($req->workflow_status == 'Draft'): ?>
                                                                <a href="<?php echo base_url('RequisitionController/edit_requisition/') . $req->pr_id; ?>"
                                                                    class="btn btn-xs btn-warning" title="Edit">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="<?php echo base_url('RequisitionController/submit_for_approval/') . $req->pr_id; ?>"
                                                                    class="btn btn-xs btn-success" title="Submit for Approval"
                                                                    onclick="return confirm('Submit this PR for approval?')">
                                                                    <i class="fa fa-paper-plane"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            <a href="<?php echo base_url('RequisitionController/view_approval_history/') . $req->pr_id; ?>"
                                                                class="btn btn-xs btn-primary" title="Approval History">
                                                                <i class="fa fa-history"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No requisitions found.
                                        <a href="<?php echo base_url('RequisitionController/create_purchase_requisition'); ?>"
                                            class="btn btn-sm btn-success pull-right">
                                            Create Your First PR
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="box-footer">
                                <a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>"
                                    class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Dashboard
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