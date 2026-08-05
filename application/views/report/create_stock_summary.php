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
                    Stock Summary Report 
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
                                <h3 class="box-title">Stock Summary Report </h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_stock_summary">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" name="from_date" value="<?php echo $from_date ?>" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" name="to_date" value="<?php echo $to_date?>" required="" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                </div>
                                <center><button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success">Submit</button></center>
                                <!-- /.box-body -->

                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url();?>ReportController/get_stock_summary_report_xlsx"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Item Name</th>
                                        <th>Stock Quantity</th>
                                        <th>Purchase Price</th>
                                        <th>Sell Price</th>
                                        <th>Stock Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                    $i = 1;
                                    foreach ($result as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                            <td> <?php echo $key->code; ?> </td>
                                            
                                            <td><?php echo $key->stock; ?> </td>
                                            <td><?php echo $key->cost_price; ?> </td>
                                            <td><?php echo $key->sell_price; ?> </td>
                                            <td><?php echo $key->cost_price * $key->stock; ?></td>
                                           
                                                      
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

