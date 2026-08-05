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
                    PR Report - <?php echo $report_title ?? 'Report'; ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('RequisitionController/reports_analytics'); ?>">Reports & Analytics</a></li>
                    <li class="active">Report View</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <!-- Filter Summary -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Report Filters</h3>
                                <div class="box-tools pull-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm" onclick="exportToCSV()">
                                            <i class="fa fa-file-excel-o"></i> Export to CSV
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="exportToPDF()">
                                            <i class="fa fa-file-pdf-o"></i> Export to PDF
                                        </button>
                                        <a href="<?php echo base_url('RequisitionController/reports_analytics'); ?>"
                                            class="btn btn-default btn-sm">
                                            <i class="fa fa-arrow-left"></i> Back to Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Report Type:</strong><br>
                                        <?php
                                        $report_types = [
                                            'department_wise' => 'Department-wise Analysis',
                                            'monthly_summary' => 'Monthly Summary',
                                            'approval_timeline' => 'Approval Timeline',
                                            'default' => 'PR Summary'
                                        ];
                                        echo $report_types[$report_type] ?? $report_types['default'];
                                        ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Date Range:</strong><br>
                                        <?php
                                        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                                            echo date('d-m-Y', strtotime($filters['start_date'])) . ' to ' .
                                                date('d-m-Y', strtotime($filters['end_date']));
                                        } else {
                                            echo 'All Dates';
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Generated On:</strong><br>
                                        <?php echo date('d-m-Y H:i:s'); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total Records:</strong><br>
                                        <?php echo count($report_data ?? []); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Data -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title"><?php echo $report_title ?? 'Report Data'; ?></h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($report_data)): ?>
                                    <?php if ($report_type == 'department_wise'): ?>
                                        <!-- Department-wise Report -->
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Department</th>
                                                    <th>Total PRs</th>
                                                    <th>Approved</th>
                                                    <th>Pending</th>
                                                    <th>Rejected</th>
                                                    <th>Total Value (₹)</th>
                                                    <th>Avg. Approval Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($report_data as $row): ?>
                                                    <tr>
                                                        <td><?php echo $row->department_name ?? 'N/A'; ?></td>
                                                        <td><?php echo $row->total_prs ?? 0; ?></td>
                                                        <td>
                                                            <span class="label label-success">
                                                                <?php echo $row->approved_count ?? 0; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="label label-warning">
                                                                <?php echo $row->pending_count ?? 0; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="label label-danger">
                                                                <?php echo $row->rejected_count ?? 0; ?>
                                                            </span>
                                                        </td>
                                                        <td>₹<?php echo number_format($row->total_value ?? 0, 2); ?></td>
                                                        <td>
                                                            <?php
                                                            if (isset($row->avg_approval_time) && $row->avg_approval_time > 0) {
                                                                echo $row->avg_approval_time . ' days';
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>

                                    <?php elseif ($report_type == 'monthly_summary'): ?>
                                        <!-- Monthly Summary Report -->
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th>Total PRs</th>
                                                    <th>Approved</th>
                                                    <th>Pending</th>
                                                    <th>Total Value (₹)</th>
                                                    <th>Avg. Value (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($report_data as $row): ?>
                                                    <tr>
                                                        <td><?php echo $row->month ?? 'N/A'; ?></td>
                                                        <td><?php echo $row->total_prs ?? 0; ?></td>
                                                        <td><?php echo $row->approved_count ?? 0; ?></td>
                                                        <td><?php echo $row->pending_count ?? 0; ?></td>
                                                        <td>₹<?php echo number_format($row->total_value ?? 0, 2); ?></td>
                                                        <td>₹<?php echo number_format($row->avg_value ?? 0, 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>

                                    <?php elseif ($report_type == 'approval_timeline'): ?>
                                        <!-- Approval Timeline Report -->
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>PR No</th>
                                                    <th>Department</th>
                                                    <th>Created Date</th>
                                                    <th>Final Status Date</th>
                                                    <th>Total Days</th>
                                                    <th>Status</th>
                                                    <th>Approval Path</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($report_data as $row): ?>
                                                    <tr>
                                                        <td><?php echo $row->pr_no ?? 'N/A'; ?></td>
                                                        <td><?php echo $row->department_name ?? 'N/A'; ?></td>
                                                        <td><?php echo date('d-m-Y', strtotime($row->created_date ?? date('Y-m-d'))); ?></td>
                                                        <td><?php echo date('d-m-Y', strtotime($row->final_status_date ?? date('Y-m-d'))); ?></td>
                                                        <td><?php echo $row->total_days ?? 0; ?> days</td>
                                                        <td>
                                                            <span class="label label-<?php
                                                                                        echo ($row->final_status == 'Approved') ? 'success' : (($row->final_status == 'Pending') ? 'warning' : 'danger');
                                                                                        ?>">
                                                                <?php echo $row->final_status ?? 'Pending'; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $row->approval_path ?? 'N/A'; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>

                                    <?php else: ?>
                                        <!-- Default PR Summary Report -->
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>PR No</th>
                                                    <th>Date</th>
                                                    <th>Project Code</th>
                                                    <th>Sales Order</th>
                                                    <th>OC Number</th>
                                                    <th>Department</th>
                                                    <th>Requested By</th>
                                                    <th>Total Value (₹)</th>
                                                    <th>Urgency</th>
                                                    <th>Status</th>
                                                    <th>Workflow Status</th>
                                                    <th>Last Updated</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Handle both array and object data
                                                if (is_array($report_data) && !empty($report_data)) {
                                                    foreach ($report_data as $row):
                                                        $row = (object)$row; // Convert to object if array
                                                ?>
                                                        <tr>
                                                            <td><?php echo $row->pr_no ?? $row->{'PR No'} ?? 'N/A'; ?></td>
                                                            <td><?php echo date('d-m-Y', strtotime($row->pr_date ?? $row->{'PR Date'} ?? date('Y-m-d'))); ?></td>
                                                            <td><?php echo $row->project_code ?? $row->{'Project Code'} ?? 'N/A'; ?></td>
                                                            <td><?php echo $row->so_no ?? $row->{'SO No'} ?? 'N/A'; ?></td>
                                                            <td><?php echo $row->oc_no ?? $row->{'OC No'} ?? 'N/A'; ?></td>
                                                            <td><?php echo $row->department_name ?? $row->Department ?? 'N/A'; ?></td>
                                                            <td><?php echo $row->requester_name ?? $row->{'Requested By'} ?? 'N/A'; ?></td>
                                                            <td>₹<?php
                                                                    $value = $row->total_value ?? $row->{'Total Value'} ?? 0;
                                                                    echo number_format((float)$value, 2);
                                                                    ?></td>
                                                            <td>
                                                                <span class="label label-<?php
                                                                                            $urgency = $row->urgency_level ?? $row->{'Urgency Level'} ?? 'Medium';
                                                                                            echo ($urgency == 'High') ? 'danger' : (($urgency == 'Medium') ? 'warning' : 'info');
                                                                                            ?>">
                                                                    <?php echo $urgency; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="label label-<?php
                                                                                            $status = $row->approval_status ?? $row->{'Approval Status'} ?? 'Pending';
                                                                                            echo ($status == 'Approved') ? 'success' : (($status == 'Pending') ? 'warning' : 'danger');
                                                                                            ?>">
                                                                    <?php echo $status; ?>
                                                                </span>
                                                            </td>
                                                            <td><?php echo $row->workflow_status ?? $row->{'Workflow Status'} ?? 'N/A'; ?></td>
                                                            <td><?php echo date('d-m-Y', strtotime($row->updated_at ?? date('Y-m-d'))); ?></td>
                                                        </tr>
                                                <?php endforeach;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i> No data found for the selected criteria.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <?php if (!empty($report_data)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Summary Statistics</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-aqua"><i class="fa fa-file-text-o"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Records</span>
                                                    <span class="info-box-number"><?php echo count($report_data); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-green"><i class="fa fa-inr"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Value</span>
                                                    <span class="info-box-number">
                                                        ₹<?php
                                                            $total = 0;
                                                            foreach ($report_data as $row) {
                                                                if (is_object($row)) {
                                                                    $total += (float)($row->total_value ?? $row->{'Total Value'} ?? 0);
                                                                } else {
                                                                    $total += (float)($row['total_value'] ?? $row['Total Value'] ?? 0);
                                                                }
                                                            }
                                                            echo number_format($total, 2);
                                                            ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Avg. Value</span>
                                                    <span class="info-box-number">
                                                        ₹<?php
                                                            $count = count($report_data);
                                                            $avg = $count > 0 ? $total / $count : 0;
                                                            echo number_format($avg, 2);
                                                            ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-blue"><i class="fa fa-calendar"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Report Period</span>
                                                    <span class="info-box-number">
                                                        <?php
                                                        if (!empty($filters['start_date'])) {
                                                            echo date('M Y', strtotime($filters['start_date']));
                                                        } else {
                                                            echo 'All Time';
                                                        }
                                                        ?>
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
        function exportToCSV() {
            // Redirect to generate report with CSV format
            window.location.href = '<?php echo base_url("RequisitionController/generate_report"); ?>' +
                '?output_format=csv&report_type=<?php echo $report_type; ?>' +
                '&start_date=<?php echo $filters["start_date"] ?? ""; ?>' +
                '&end_date=<?php echo $filters["end_date"] ?? ""; ?>' +
                '&department=<?php echo $filters["department"] ?? ""; ?>';
        }

        function exportToPDF() {
            // Redirect to generate report with PDF format
            window.location.href = '<?php echo base_url("RequisitionController/generate_report"); ?>' +
                '?output_format=pdf&report_type=<?php echo $report_type; ?>' +
                '&start_date=<?php echo $filters["start_date"] ?? ""; ?>' +
                '&end_date=<?php echo $filters["end_date"] ?? ""; ?>' +
                '&department=<?php echo $filters["department"] ?? ""; ?>';
        }

        $(document).ready(function() {
            // Initialize DataTables if tables exist
            $('table').each(function() {
                if (!$(this).hasClass('dataTable')) {
                    $(this).DataTable({
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "pageLength": 25,
                        "dom": '<"top"lf>rt<"bottom"ip><"clear">',
                        "buttons": [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        "language": {
                            "search": "Search Reports:"
                        }
                    });
                }
            });
        });
    </script>
</body>