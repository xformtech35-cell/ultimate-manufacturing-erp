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

                <div class="row">
                    <div class="col-md-12">
                        <!-- Search & Filter Box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Filter PRs</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <form method="get" action="<?php echo base_url('RequisitionController/list_approval_history'); ?>">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="">All Status</option>
                                                    <option value="Pending" <?php echo (isset($filters['status']) && $filters['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Approved" <?php echo (isset($filters['status']) && $filters['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="Rejected" <?php echo (isset($filters['status']) && $filters['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <select name="department" class="form-control">
                                                    <option value="">All Departments</option>
                                                    <?php if (isset($departments)): ?>
                                                        <?php foreach ($departments as $dept): ?>
                                                            <option value="<?php echo $dept->department_id; ?>"
                                                                <?php echo (isset($filters['department']) && $filters['department'] == $dept->department_id) ? 'selected' : ''; ?>>
                                                                <?php echo $dept->department_name; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>From Date</label>
                                                <input type="date" name="start_date" class="form-control"
                                                    value="<?php echo isset($filters['start_date']) ? $filters['start_date'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>To Date</label>
                                                <input type="date" name="end_date" class="form-control"
                                                    value="<?php echo isset($filters['end_date']) ? $filters['end_date'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fa fa-filter"></i> Filter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- PRs List Box -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Purchase Requisitions</h3>
                                <div class="box-tools pull-right">
                                    <span class="badge bg-blue"><?php echo count($requisitions ?? []); ?> PRs</span>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($requisitions)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>PR No</th>
                                                    <th>Location</th>
                                                    <th>Department</th>
                                                    <th>Requested By</th>
                                                    <th>PR Date</th>
                                                    <th>Required Date</th>
                                                    <th>Status</th>
                                                    <th>Workflow</th>
                                                    <th>Total Value</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($requisitions as $requisition): ?>
                                                    <?php
                                                    // Get PR number - handle cases where pr_no might be null
                                                    $pr_no = !empty($requisition->pr_no) ? $requisition->pr_no : '' . $requisition->pr_id;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $pr_no; ?></strong>
                                                        </td>
                                                        <td><?php echo $requisition->location_name ?? 'N/A'; ?></td>
                                                        <td><?php echo $requisition->department_name ?? 'N/A'; ?></td>
                                                        <td><?php echo $requisition->requester_name ?? 'N/A'; ?></td>
                                                        <td><?php echo date('d-m-Y', strtotime($requisition->pr_date)); ?></td>
                                                        <td><?php echo date('d-m-Y', strtotime($requisition->required_date)); ?></td>
                                                        <td>
                                                            <span class="label label-<?php
                                                                                        switch ($requisition->approval_status) {
                                                                                            case 'Approved':
                                                                                                echo 'success';
                                                                                                break;
                                                                                            case 'Rejected':
                                                                                                echo 'danger';
                                                                                                break;
                                                                                            case 'Pending':
                                                                                                echo 'warning';
                                                                                                break;
                                                                                            default:
                                                                                                echo 'default';
                                                                                        }
                                                                                        ?>">
                                                                <?php echo $requisition->approval_status; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="label label-info">
                                                                <?php echo $requisition->workflow_status; ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-right">
                                                            ₹<?php echo number_format($requisition->total_value, 2); ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="<?php echo base_url('RequisitionController/view_approval_history/' . $requisition->pr_id); ?>"
                                                                    class="btn btn-info btn-sm" title="View Approval History">
                                                                    <i class="fa fa-history"></i> History
                                                                </a>
                                                                <a href="<?php echo base_url('RequisitionController/show_requisition/' . $requisition->pr_id); ?>"
                                                                    class="btn btn-default btn-sm" title="View PR Details">
                                                                    <i class="fa fa-eye"></i> View
                                                                </a>
                                                                <?php if ($requisition->approval_status == 'Pending'): ?>
                                                                    <a href="<?php echo base_url('RequisitionController/view_for_approval/' . $requisition->pr_id); ?>"
                                                                        class="btn btn-warning btn-sm" title="Take Approval Action">
                                                                        <i class="fa fa-check-circle"></i> Approve
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center" style="padding: 40px;">
                                        <i class="fa fa-history fa-4x text-muted"></i>
                                        <h3>No Purchase Requisitions Found</h3>
                                        <p class="text-muted">You don't have access to any purchase requisition history.</p>
                                        <p>This could be because:</p>
                                        <ul class="list-unstyled">
                                            <li>• You haven't created any PRs</li>
                                            <li>• You haven't been assigned as an approver</li>
                                            <li>• No PRs have been submitted for your approval</li>
                                        </ul>
                                        <a href="<?php echo base_url('RequisitionController/create_purchase_requisition'); ?>"
                                            class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Create New PR
                                        </a>
                                        <a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>"
                                            class="btn btn-default">
                                            <i class="fa fa-dashboard"></i> Go to Approval Dashboard
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($requisitions)): ?>
                                <div class="box-footer clearfix">
                                    <div class="pull-left">
                                        <p class="text-muted">
                                            Showing <strong><?php echo count($requisitions); ?></strong> purchase requisitions
                                        </p>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>"
                                            class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to Dashboard
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>

    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        .btn-group .btn-sm {
            margin-right: 5px;
        }

        .btn-group .btn-sm:last-child {
            margin-right: 0;
        }

        .label {
            font-size: 85%;
        }
    </style>
</body>