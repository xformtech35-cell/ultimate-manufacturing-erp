<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                   Job Order Report 
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Report</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Job Order Report </h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_joborder_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" value="<?php echo $from_date; ?>" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" value="<?php echo $to_date; ?>" name="to_date" required="" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <center> <button type="submit" class="btn btn-default">Cancel</button>
                                        <button type="submit" class="btn btn-success">Submit</button></center>   
                                </div>
                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url(); ?>ReportController/get_joborder_report_by_date_xlsx"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Job Order Number</th>
                                        <th>Date</th>
                                        <th>Company Name</th>
                                        <th>Total Cost</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($result as $key) {
                                        $status_label = 'Unknown';
                                        if ($key->status == 1) {
                                            $status_label = 'Draft';
                                        } elseif ($key->status == 2) {
                                            $status_label = 'Sent';
                                        } elseif ($key->status == 3) {
                                            $status_label = 'Viewed';
                                        } elseif ($key->status == 4) {
                                            $status_label = 'Approved';
                                        } elseif ($key->status == 5) {
                                            $status_label = 'Rejected';
                                        } elseif ($key->status == 6) {
                                            $status_label = 'Canceled';
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $key->number_fk; ?></td>
                                            <td><?php echo !empty($key->date) && $key->date != '0000-00-00' ? date("d-m-Y", strtotime($key->date)) : ''; ?></td>
                                            <td><?php echo isset($key->company_name) ? $key->company_name : ''; ?></td>
                                            <td><?php echo isset($key->total_cost) && $key->total_cost !== null ? number_format($key->total_cost, 2) : '0.00'; ?></td>
                                            <td><?php echo $status_label; ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->

        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#example3')) {
        $('#example3').DataTable().destroy();
    }
    $('#example3').DataTable({
        autoWidth: false,
        scrollX: true,
        pageLength: 25,
        language: {
            search: "Search Job Orders:"
        },
        columns: [
            { width: '50px' },   // Sr.No.
            { width: '160px' },  // Job Order Number
            { width: '100px' },  // Date
            { width: '300px' },  // Company Name
            { width: '100px' },  // Total Cost
            { width: '90px' }    // Status
        ],
        columnDefs: [
            { targets: [0], className: 'dt-center' },
            { targets: [4], className: 'dt-right' },
            { targets: [2, 5], className: 'dt-center' }
        ]
    });
});
</script>
