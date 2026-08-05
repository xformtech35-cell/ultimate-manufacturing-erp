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
                    Reports & Analytics
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>">Approval Dashboard</a></li>
                    <li class="active">Reports & Analytics</li>
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

                <!-- Filter Box -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Report Filters</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <form method="get" action="<?php echo base_url('RequisitionController/reports_analytics'); ?>">
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
                                            <div class="form-group" style="margin-top: 25px;">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-filter"></i> Apply Filters
                                                </button>
                                                <a href="<?php echo base_url('RequisitionController/reports_analytics'); ?>"
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

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3><?php echo isset($approval_metrics->total_prs) ? $approval_metrics->total_prs : 0; ?></h3>
                                <p>Total PRs</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-file-text-o"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3><?php echo isset($approval_metrics->approved_count) ? $approval_metrics->approved_count : 0; ?></h3>
                                <p>Approved</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3><?php echo isset($approval_metrics->pending_count) ? $approval_metrics->pending_count : 0; ?></h3>
                                <p>Pending</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3><?php echo isset($approval_metrics->rejected_count) ? $approval_metrics->rejected_count : 0; ?></h3>
                                <p>Rejected</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-times"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department-wise Report -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Department-wise Analysis</h3>
                                <div class="box-tools pull-right">
                                    <?php if (!empty($department_stats)): ?>
                                        <span class="badge bg-blue"><?php echo count($department_stats); ?> Departments</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($department_stats)): ?>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Department</th>
                                                <th>Total PRs</th>
                                                <th>Approved</th>
                                                <th>Pending</th>
                                                <th>Rejected</th>
                                                <th>Total Value</th>
                                                <th>Avg. Value</th>
                                                <th>Approval Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $counter = 1; ?>
                                            <?php foreach ($department_stats as $stat): ?>
                                                <?php
                                                $approval_rate = ($stat->total_prs > 0) ? ($stat->approved / $stat->total_prs) * 100 : 0;
                                                ?>
                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td><?php echo $stat->department_name; ?></td>
                                                    <td><?php echo $stat->total_prs; ?></td>
                                                    <td>
                                                        <span class="badge bg-green"><?php echo $stat->approved; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-yellow"><?php echo $stat->pending; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-red"><?php echo $stat->rejected; ?></span>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong>₹<?php echo number_format($stat->total_value, 2); ?></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        ₹<?php echo number_format(($stat->total_prs > 0) ? $stat->total_value / $stat->total_prs : 0, 2); ?>
                                                    </td>
                                                    <td>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar progress-bar-success" style="width: <?php echo $approval_rate; ?>%"></div>
                                                        </div>
                                                        <span class="badge bg-light-blue"><?php echo number_format($approval_rate, 1); ?>%</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No department data available for the selected period.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Summary -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Monthly Summary</h3>
                                <div class="box-tools pull-right">
                                    <?php if (!empty($monthly_stats)): ?>
                                        <span class="badge bg-blue"><?php echo count($monthly_stats); ?> Months</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($monthly_stats)): ?>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Month-Year</th>
                                                <th>Total PRs</th>
                                                <th>Approved</th>
                                                <th>Pending</th>
                                                <th>Rejected</th>
                                                <th>Total Value</th>
                                                <th>Avg. Value</th>
                                                <th>Trend</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($monthly_stats as $stat): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo date('M Y', strtotime($stat->month_year . '-01')); ?></strong>
                                                    </td>
                                                    <td><?php echo $stat->total_prs; ?></td>
                                                    <td>
                                                        <span class="badge bg-green"><?php echo $stat->approved; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-yellow"><?php echo $stat->pending; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-red"><?php echo $stat->rejected; ?></span>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong>₹<?php echo number_format($stat->total_value, 2); ?></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        ₹<?php echo number_format($stat->avg_value, 2); ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($stat->total_prs > 5): ?>
                                                            <span class="label label-success"><i class="fa fa-line-chart"></i> High Activity</span>
                                                        <?php elseif ($stat->total_prs > 2): ?>
                                                            <span class="label label-warning"><i class="fa fa-line-chart"></i> Medium Activity</span>
                                                        <?php else: ?>
                                                            <span class="label label-default"><i class="fa fa-line-chart"></i> Low Activity</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No monthly data available for the selected period.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Report Generator -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Generate Custom Report</h3>
                            </div>
                            <div class="box-body">
                                <form method="post" action="<?php echo base_url('RequisitionController/generate_report'); ?>">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Report Type:</label>
                                                <select name="report_type" class="form-control" required>
                                                    <option value="summary">PR Summary Report</option>
                                                    <option value="department_wise">Department-wise Analysis</option>
                                                    <option value="monthly_summary">Monthly Summary</option>
                                                    <option value="approval_timeline">Approval Timeline Analysis</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>From Date:</label>
                                                <input type="text" name="start_date" class="form-control datepicker"
                                                    placeholder="DD-MM-YYYY">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>To Date:</label>
                                                <input type="text" name="end_date" class="form-control datepicker"
                                                    placeholder="DD-MM-YYYY">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Department:</label>
                                                <select name="department" class="form-control">
                                                    <option value="">All Departments</option>
                                                    <?php foreach ($departments as $dept): ?>
                                                        <option value="<?php echo $dept->department_id; ?>">
                                                            <?php echo $dept->department_name; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Output Format:</label>
                                                <select name="output_format" class="form-control">
                                                    <option value="html">HTML (View in Browser)</option>
                                                    <option value="csv">CSV (Download)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top: 25px;">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-download"></i> Generate Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="callout callout-info">
                                            <h4><i class="icon fa fa-info"></i> Report Types:</h4>
                                            <ul>
                                                <li><strong>PR Summary Report:</strong> Detailed list of all PRs with filters</li>
                                                <li><strong>Department-wise Analysis:</strong> Performance metrics by department</li>
                                                <li><strong>Monthly Summary:</strong> Monthly trends and statistics</li>
                                                <li><strong>Approval Timeline Analysis:</strong> Time taken for approval at each level</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <?php if (isset($approval_metrics) && $approval_metrics): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Performance Metrics</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="info-box bg-light-blue">
                                                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Avg. Approval Time</span>
                                                    <span class="info-box-number"><?php echo isset($approval_metrics->avg_approval_time_days) ? number_format($approval_metrics->avg_approval_time_days, 1) : '0.0'; ?> days</span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: 70%"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                        <?php echo isset($approval_metrics->avg_approval_time_hours) ? number_format($approval_metrics->avg_approval_time_hours, 0) : '0'; ?> hours
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-green">
                                                <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Approval Rate</span>
                                                    <span class="info-box-number"><?php echo isset($approval_metrics->approval_rate) ? number_format($approval_metrics->approval_rate, 1) : '0.0'; ?>%</span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: <?php echo isset($approval_metrics->approval_rate) ? $approval_metrics->approval_rate : '0'; ?>%"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                        <?php echo isset($approval_metrics->approved_count) ? $approval_metrics->approved_count : '0'; ?> of <?php echo isset($approval_metrics->total_prs) ? $approval_metrics->total_prs : '0'; ?> PRs
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-yellow">
                                                <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Pending Rate</span>
                                                    <span class="info-box-number">
                                                        <?php
                                                        if (isset($approval_metrics->total_prs) && $approval_metrics->total_prs > 0) {
                                                            $pending_rate = ($approval_metrics->pending_count / $approval_metrics->total_prs) * 100;
                                                            echo number_format($pending_rate, 1) . '%';
                                                        } else {
                                                            echo '0.0%';
                                                        }
                                                        ?>
                                                    </span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: 
                                                        <?php
                                                        if (isset($approval_metrics->total_prs) && $approval_metrics->total_prs > 0) {
                                                            echo ($approval_metrics->pending_count / $approval_metrics->total_prs) * 100;
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?>%">
                                                        </div>
                                                    </div>
                                                    <span class="progress-description">
                                                        <?php echo isset($approval_metrics->pending_count) ? $approval_metrics->pending_count : '0'; ?> PRs awaiting approval
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-red">
                                                <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Rejection Rate</span>
                                                    <span class="info-box-number">
                                                        <?php
                                                        if (isset($approval_metrics->total_prs) && $approval_metrics->total_prs > 0) {
                                                            $rejection_rate = ($approval_metrics->rejected_count / $approval_metrics->total_prs) * 100;
                                                            echo number_format($rejection_rate, 1) . '%';
                                                        } else {
                                                            echo '0.0%';
                                                        }
                                                        ?>
                                                    </span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: 
                                                        <?php
                                                        if (isset($approval_metrics->total_prs) && $approval_metrics->total_prs > 0) {
                                                            echo ($approval_metrics->rejected_count / $approval_metrics->total_prs) * 100;
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?>%">
                                                        </div>
                                                    </div>
                                                    <span class="progress-description">
                                                        <?php echo isset($approval_metrics->rejected_count) ? $approval_metrics->rejected_count : '0'; ?> PRs rejected
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
            // Initialize datepicker
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true
            });

            // Initialize DataTables for all tables
            $('.table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 25,
                "language": {
                    "search": "Search Analytics:",
                    "paginate": {
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        });
    </script>
</body>