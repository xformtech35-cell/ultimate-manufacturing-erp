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
                    Sales Report (HSN Wise)
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
                            
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_sales_hsn_report">
                                <div class="box-body">
                                    <div class="form-group">
                                     <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date"  value="<?php echo $from_date; ?>" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" value="<?php echo $to_date; ?>" name="to_date" required="" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                </div>
                                <center><button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success">Submit</button></center>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    
                                </div>
                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url(); ?>ReportController/get_sales_hsn_report_by_date_xlsx"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                        <table id="example3" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Sr.No.</th>
            <th>HSN Code</th>
            <th>Total Value (incl. tax)</th>
            <th>Taxable Value (excl. tax)</th>
            <th>IGST</th>
            <th>CGST</th>
            <th>SGST</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($result as $row) {
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo !empty($row->hsn_code) ? $row->hsn_code : 'NA'; ?></td>
                <td><?php echo number_format($row->total_value, 2); ?></td>
                <td><?php echo number_format($row->taxable_value, 2); ?></td>
                <td><?php echo number_format($row->igst, 2); ?></td>
                <td><?php echo number_format($row->cgst, 2); ?></td>
                <td><?php echo number_format($row->sgst, 2); ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
                        </div>
                        <!-- /.box -->
                        
                    </div>
                    <!--/.col (left ) -->
                </div>
                <!--/.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

    </div>
    <!-- ./wrapper -->

    <?php $this->load->view('admin/footer'); ?>
    <div class="control-sidebar-bg"></div>

    <script>
        $(function() {
            // Initialize From Date datepicker
            $("#from_date").datepicker({
                maxDate: "0",
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true,
                onClose: function(selectedDate) {
                    // Update To Date minimum date when From Date is selected
                    if (selectedDate) {
                        $("#to_date").datepicker("option", "minDate", selectedDate);
                    }
                }
            });

            // Initialize To Date datepicker
            $("#to_date").datepicker({
                maxDate: "0",
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true,
                minDate: $("#from_date").val() ? $("#from_date").val() : "-30d"
            });

            // When From Date changes, update To Date constraints
            $("#from_date").on("change", function() {
                var fromDate = $(this).val();
                if (fromDate) {
                    $("#to_date").datepicker("option", "minDate", fromDate);
                } else {
                    $("#to_date").datepicker("option", "minDate", "-30d");
                }
                $("#to_date").val("");
            });
        });
    </script>
</body>

</html>
