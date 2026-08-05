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
                    Profit Loss
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Expenditure</a></li>
                    <li class="active">Profit Loss Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Profit Loss Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/get_profit_loss_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" name="to_date" required="" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Stock in Hand<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text"  autocomplete="off" class="form-control" name="stock_in_hand" required=""> 
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Scrap<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text"  autocomplete="off" class="form-control " name="scrap" required=""> 
                                        </div>
                                        
                                    </div>
                                    
                                    <center>
                                        <button type="submit" class="btn btn-default">Cancel</button>
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </center>
                                    
                                    
                                </div>
                                <!-- /.box-body -->
                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url(); ?>ReportController/get_profit_loss_report"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="box-title"> Profit Loss Details</h3> 
                                </div>


                            </div>

                            <!-- /.box-body -->
                            <table id="manageTable" class="table table-bordered table-striped">

                                <thead>
                                    <tr>
                                        <th>
                                            Profit Loss Report</th>
                                    </tr>
                                    <tr>
                                        <th>VTech Solutions</th>
                                    </tr>
                                    <tr>
                                        <th>CIN:U74999PN2017PTC173511</th>
                                    </tr>
                                    
                                        
                                        <tr>
                                        <th>Status</th>
                                        <th>Unit</th>
                                        <th>Stock Type</th>
                                        <th>Qty In Stock</th>
                                        <th>Category</th>
                                        <th>Created Time</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>

                            </table>

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
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

