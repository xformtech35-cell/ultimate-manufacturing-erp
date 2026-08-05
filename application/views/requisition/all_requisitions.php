<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];
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
                    All Purchase Requisitions
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>">Approval Dashboard</a></li>
                    <li class="active">All Requisitions</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <!-- Filter Box -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Filters</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <form method="get" action="<?php echo base_url('RequisitionController/all_requisitions'); ?>">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Status:</label>
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
                                                <label>Workflow Status:</label>
                                                <select name="workflow_status" class="form-control">
                                                    <option value="">All Workflow Status</option>
                                                    <option value="Draft" <?php echo (isset($filters['workflow_status']) && $filters['workflow_status'] == 'Draft') ? 'selected' : ''; ?>>Draft</option>
                                                    <option value="Submitted" <?php echo (isset($filters['workflow_status']) && $filters['workflow_status'] == 'Submitted') ? 'selected' : ''; ?>>Submitted</option>
                                                    <option value="L1_Pending" <?php echo (isset($filters['workflow_status']) && $filters['workflow_status'] == 'L1_Pending') ? 'selected' : ''; ?>>Level 1 Pending</option>
                                                    <option value="L2_Pending" <?php echo (isset($filters['workflow_status']) && $filters['workflow_status'] == 'L2_Pending') ? 'selected' : ''; ?>>Level 2 Pending</option>
                                                    <option value="L3_Pending" <?php echo (isset($filters['workflow_status']) && $filters['workflow_status'] == 'L3_Pending') ? 'selected' : ''; ?>>Level 3 Pending</option>
                                                    <option value="Approved" <?php echo (isset($filters['workflow_status']) && $filters['workflow_status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="Rejected" <?php echo (isset($filters['workflow_status']) && $filters['workflow_status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Department:</label>
                                                <select name="department" class="form-control">
                                                    <option value="">All Departments</option>
                                                    <?php foreach ($departments as $dept): ?>
                                                        <option value="<?php echo $dept->department_id; ?>"
                                                            <?php echo (isset($filters['department']) && $filters['department'] == $dept->department_id) ? 'selected' : ''; ?>>
                                                            <?php echo $dept->department_name; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Urgency:</label>
                                                <select name="urgency" class="form-control">
                                                    <option value="">All Urgency</option>
                                                    <option value="Low" <?php echo (isset($filters['urgency']) && $filters['urgency'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                                                    <option value="Medium" <?php echo (isset($filters['urgency']) && $filters['urgency'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                                    <option value="High" <?php echo (isset($filters['urgency']) && $filters['urgency'] == 'High') ? 'selected' : ''; ?>>High</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>From Date:</label>
                                                <input type="text" name="start_date" class="form-control datepicker"
                                                    value="<?php echo isset($filters['start_date']) ? $filters['start_date'] : ''; ?>"
                                                    placeholder="DD-MM-YYYY">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>To Date:</label>
                                                <input type="text" name="end_date" class="form-control datepicker"
                                                    value="<?php echo isset($filters['end_date']) ? $filters['end_date'] : ''; ?>"
                                                    placeholder="DD-MM-YYYY">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Search:</label>
                                                <input type="text" name="search" class="form-control"
                                                    value="<?php echo isset($filters['search']) ? $filters['search'] : ''; ?>"
                                                    placeholder="Search PR No, Department, Requester...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top: 25px;">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-filter"></i> Apply Filters
                                                </button>
                                                <a href="<?php echo base_url('RequisitionController/all_requisitions'); ?>"
                                                    class="btn btn-default">
                                                    Clear Filters
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Table -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header with-border" style="padding: 12px 15px; display: block !important; clear: both !important;">
                                <div style="float: left !important; display: inline-block;">
                                    <h3 class="box-title" style="float: left !important; font-weight: 600; margin: 0; font-size: 18px; color: #1e293b; display: inline-flex; align-items: center; gap: 8px; line-height: 30px;">
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> Purchase Requisitions List
                                        <?php if ($total_count > 0): ?>
                                            <span class="badge bg-blue" style="margin-left: 5px;"><?php echo $total_count; ?> records</span>
                                        <?php endif; ?>
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <a href="<?php echo base_url('RequisitionController/export_requisitions'); ?>?<?php echo http_build_query($filters); ?>" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #2e7d32 !important;">
                                        <i class="fa fa-download"></i> Export
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <?php if (!empty($requisitions)): ?>
                                    <table id="all_requisition" class="table table-bordered table-striped" style="margin-bottom: 0;">
                                        <thead>
                                            <tr>
                                                <th>PR No</th>
                                                <th>Date</th>
                                                <th>Location</th>
                                                <th>Department</th>
                                                <th>Requested By</th>
                                                <th>Total Value</th>
                                                <th>Urgency</th>
                                                <th>Approval Status</th>
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
                                                    <td><?php echo $req->location_name ?? 'N/A'; ?></td>
                                                    <td><?php echo $req->department_name ?? 'N/A'; ?></td>
                                                    <td><?php echo $req->requester_name ?? 'N/A'; ?></td>
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
                                                        <?php else: ?>
                                                            <span class="label label-default"><?php echo $req->workflow_status; ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $req->current_approver_role ?? 'N/A'; ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="<?php echo base_url('RequisitionController/show_requisition/') . $req->pr_id; ?>"
                                                                class="btn btn-xs btn-info" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="<?php echo base_url('RequisitionController/view_approval_history/') . $req->pr_id; ?>"
                                                                class="btn btn-xs btn-primary" title="Approval History">
                                                                <i class="fa fa-history"></i>
                                                            </a>
                                                            <?php
                                                            // Fix: Check if user can approve
                                                            $can_approve = false;
                                                            if (isset($this->requisition) && method_exists($this->requisition, 'can_user_approve')) {
                                                                $can_approve = $this->requisition->can_user_approve($req->pr_id, $user_id ?? 0);
                                                            }
                                                            if ($can_approve): ?>
                                                                <a href="<?php echo base_url('RequisitionController/view_for_approval/') . $req->pr_id; ?>"
                                                                    class="btn btn-xs btn-success" title="Approve">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <!-- Pagination -->
                                    <?php if (isset($pagination) && !empty($pagination)): ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php echo $pagination; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No requisitions found matching your criteria.
                                    </div>
                                <?php endif; ?>
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

        <!-- Control Sidebar -->
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#all_requisition').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 25,
                "order": [
                    [1, 'desc']
                ],
                "language": {
                    "search": "Search Requisitions:"
                }
            });

            // Initialize datepicker
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true
            });
        });
    </script>
</body>