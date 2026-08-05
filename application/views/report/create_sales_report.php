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
                    Sales Report 
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
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_sales_report">
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
                            <a href="<?php echo base_url(); ?>ReportController/create_sales_hsn_report"><button class="btn-sm btn btn-info pull-right" style="margin-right: 8px;">Sales HSN Report</button></a>
                            <a href="<?php echo base_url(); ?>ReportController/get_sales_report_by_date_xlsx"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Invoice Number</th>
                                        <th>Customer Code</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Balance</th>
                                        <th>Payment Due Date</th>
                                        <th>Customer Po</th>
                                        <th>PO Date</th>
                                        <th>Company Name</th>
                                        <th>Customer Name</th>
                                        <th>GST Number</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        
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
                                            <td> <?php echo $key->invoice_number; ?> </td>
                                            <td> <?php echo $key->c_code; ?> </td>
                                            <td><?php echo date("d-m-Y",strtotime($key->invoice_date)); ?> </td>
                                            <td><?php echo $key->total; ?> </td>
                                            <td><?php echo $key->balance; ?> </td>
                                            <td><?php echo $key->payment_due_date; ?></td>
                                            <td><?php echo $key->customer_po; ?></td>
                                            <td><?php echo $key->po_date; ?></td>
                                            <td><?php echo $key->company_name; ?></td>
                                            <td><?php echo $key->fullname; ?></td>
                                            <td><?php echo $key->customer_gst; ?></td>
                                            <td><?php echo $key->email; ?></td>
                                            <td><?php echo $key->mobile; ?></td>
                                            <td><?php echo $key->address; ?></td>
                                                      
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
                                                            <span class="draft">₹<?php // echo number_format($sgst[0]->sgst, 2); ?></span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            CGST                               
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php //echo number_format($cgst[0]->cgst, 2); ?></span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            IGST                               
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php //echo number_format($igst[0]->igst, 2); ?></span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            Total CGST & SGST :  
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php //echo number_format($sgst[0]->sgst + $cgst[0]->cgst, 2); ?></span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            Total IGST :  
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php // echo number_format($igst[0]->igst, 2); ?></span>
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

