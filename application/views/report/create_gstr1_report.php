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
                  GSTR 1 Report 
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
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">GSTR 1 Report   </h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/get_gstr1_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" name="from_date" required="" value="<?php echo $from_date ?>" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control backdate" name="to_date" required="" value="<?php echo $to_date ?>" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                    <div class="form-group row hidden">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Company</label>
                                        <div class="col-sm-9">
                                            
                                            <select class="form-control input-sm company_search_name"  name="company_name" id="company_name">
                                                <option value="">Select Company</option>
                                                <?php foreach ($company_name as $key) { ?>
                                                    <option value="<?php echo $key->customer_id; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                                <?php } ?>  
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                    <!-- left column -->
<!--                    <div class="col-md-6">
                         Horizontal Form 
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">GST Report</h3>
                            </div>
                             /.box-header 
                             form start 
                            <div class="box-body">
                                <div class="col-md-9 col-sm-8">
                                    <div class="pad">

                                        <div id="panel-invoice-overview" class="panel panel-default overview">

                                            <table class="table table-bordered table-condensed no-margin">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            SGST 
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($sgst[0]->sgst, 2); ?></span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            CGST                               
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($cgst[0]->cgst, 2); ?></span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            IGST                               
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($igst[0]->igst, 2); ?></span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            Total CGST & SGST :  
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($sgst[0]->sgst + $cgst[0]->cgst, 2); ?></span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            Total IGST :  
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($igst[0]->igst, 2); ?></span>
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                         /.box 

                    </div>-->
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
            });
        });
    </script>
