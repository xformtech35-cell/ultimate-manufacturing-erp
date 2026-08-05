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
                   GSTR3B Report 
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
                  
                    <!--/.col (left ) -->

                    <!-- right column -->
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">GSTR3B Report</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->                                                                                           
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/get_po_report_by_date">
                                <div class="box-body">
                                

                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-2 control-label">Date<span style="color: red;">*</span></label>

                                          <div class="col-sm-9">
                                                    <input type="text" class="form-control onlymonth input-sm pull-right" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off" required="">
                                                </div>
                                    </div>

                             
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>


                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->

            <!-- Main content -->
            <section class="content ">
                <div class="row ">
                    <!-- left column -->
                    <div class="col-md-6 hide">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Purchase Order</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/get_po_report_by_date">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="from_date2" class="form-control backdate" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="to_date2" class="form-control" name="to_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>


                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                    <div class="col-md-6">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Job Order Report</h3>
                            </div>
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_joborder_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="joborder_from_date" class="form-control backdate" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="joborder_to_date" class="form-control" name="to_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- left column -->
<!--                    <div class="col-md-6">
                         Horizontal Form 
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Non GST Invoice</h3>
                            </div>
                             /.box-header 
                             form start 
                            <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>ReportController/get_non_gst_invoice_report_by_date">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="from_date4" class="form-control backdate" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="to_date4" class="form-control" name="to_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Company</label>
                                        <div class="col-sm-9">
                                            <select class="form-control input-sm company_search_name"  name="company_name" id="company_name">
                                                <option value="">Select Customer</option>
                                                <?php foreach ($company_name as $key) { ?>
                                                    <option value="<?php echo $key->company_name; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                                <?php } ?>  
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Item</label>
                                        <div class="col-sm-9">
                                            <select class="form-control input-sm company_search_name"  name="product_name" id="product_name">
                                                <option value="">Select Item</option>
                                                <?php foreach ($item_name as $key) { ?>
                                                    <option value="<?php echo $key->code; ?>"><?php echo $key->code; ?></option> 
                                                <?php } ?>  
                                            </select>
                                        </div>
                                    </div>


                                </div>
                                 /.box-body 
                                <div class="box-footer">
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                 /.box-footer 
                            </form>
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


