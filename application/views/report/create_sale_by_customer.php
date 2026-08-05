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
                    Sale By Customer 
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Sale By Customer </li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <!-- left column -->
                    
                    <!--/.col (left ) -->

                    <!-- right column -->
                    <div class="col-md-12">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Report</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_sale_by_customer">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date1"  class="form-control backdate" value="<?php echo $from_date; ?>" name="from_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date1" class="form-control" value="<?php echo $to_date; ?>" name="to_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row hide">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Company</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm company_search_name"  name="customer_id" id="company_name">
                                                <option value="">Select Customer</option>
                                                <?php foreach ($company_name as $key) { ?>
                                                    <option value="<?php echo $key->customer_id . '$' . $key->company_name; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                                <?php } ?>  
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <center><button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success">Submit</button></center>
                                
                                <!-- /.box-body -->
                                
                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url(); ?>ReportController/get_sale_report_by_customer"><button class="btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Customer Name</th>
                                        <th>Sale Amount</th>
                                       
                                        
                                    </tr>
                                </thead>
<!--                              
-->                                  <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($result as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                         
                                            <td><?php echo $key->company_name; ?> </td>
                                            <td><?php echo $key->total; ?> </td>
                                          
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


