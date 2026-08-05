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
                    Purchase Voucher Report (HSN Wise)
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
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_purchase_bill_hsn_report">
                                <div class="box-body">
                                    <div class="form-group">
                                     <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date"  value="<?php echo isset($from_date) ? $from_date : ''; ?>" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control payment-due-date-check" value="<?php echo isset($to_date) ? $to_date : ''; ?>" name="to_date" required="" onkeydown="return false;"> 
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
                            <a href="<?php echo base_url(); ?>ReportController/get_purchase_bill_hsn_report_by_date_xlsx"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Voucher Number</th>
                                        <th>Date</th>
                                        <th>Supplier Name</th>
                                        <th>HSN Code</th>
                                        <th>Type</th>
                                        <th>Total Before Tax</th>
                                        <th>SGST</th>
                                        <th>CGST</th>
                                        <th>IGST</th>
                                        <th>Total GST</th>
                                        <th>Grand Total</th>
                                        <th>Balance</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(isset($result) && !empty($result)):
                                        $i = 1;
                                        foreach ($result as $row) {
                                            $gst_type = '';
                                            $sgst = (float) $row->sgst;
                                            $cgst = (float) $row->cgst;
                                            $igst = (float) $row->igst;

                                            if ($row->gst_type != 'I') {
                                                $gst_type = 'SGST';
                                            } else {
                                                $gst_type = 'IGST';
                                                $sgst = 0;
                                                $cgst = 0;
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                   <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $row->number; ?> </td>
                                                
                                                <td><?php echo date("d-m-Y",strtotime($row->date)); ?> </td>
                                                <td><?php echo $row->supplier_name; ?></td>
                                                <td><?php echo !empty($row->hsn_code) ? $row->hsn_code : ''; ?></td>
                                                <td><?php echo $gst_type; ?></td>
                                                <td><?php echo $row->total_before_tax; ?></td>
                                                <td><?php echo $sgst; ?></td>
                                                <td><?php echo $cgst; ?></td>
                                                <td><?php echo $igst; ?></td>
                                                <td><?php echo $row->total_gst_amount; ?></td>
                                                <td><?php echo $row->total; ?></td>
                                                <td><?php echo $row->balance; ?></td>
                                                          
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    endif;
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
