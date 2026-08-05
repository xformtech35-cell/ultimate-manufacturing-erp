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
                    Sales / Purchase Report 
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
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_sales_purchase_report">
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
                            <a href="<?php echo base_url(); ?>ReportController/get_sales_purchase_report_by_date_xlsx"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Type</th>
                                         <th>INV/PO No.</th>
                                        <th>Ref. No.</th>
                                        <th>Date</th>
                                        <th>GST Type</th>
                                        <th>Total Before Tax</th>
                                        <th>Total GST</th>
                                        <th>Grand Total</th>
                                        <th>Company Name</th>
                                        <th>GST Number</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($result_sales as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                            <td><?php echo 'Sales'; ?> </td>
                                            <td> <?php echo $key->invoice_number; ?> </td>
                                            <td> <?php echo $key->customer_po; ?> </td>
                                            <td><?php echo date("d-m-Y",strtotime($key->invoice_date)); ?> </td>
                                            <td><?php if($key->gst_type == "S") { echo "SGST"; } else { echo "IGST"; } ?></td>
                                            <td><?php echo $key->total_before_tax; ?> </td>
                                            <td><?php echo $key->total_gst_amount; ?> </td>
                                            <td><?php echo $key->total; ?> </td>
                                            <td><?php echo $key->company_name; ?></td>
                                            <td><?php echo $key->customer_gst; ?></td>
                                                      
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>


<?php
                                    $i = 1;
                                    foreach ($result_purchase_bill as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                            <td><?php echo 'Purchase'; ?> </td>
                                            <td> <?php echo $key->number; ?> </td>
                                             <td> <?php echo $key->invoice_no; ?> </td>
                                            <td><?php echo date("d-m-Y",strtotime($key->date)); ?> </td>
                                            <td><?php if($key->gst_type == "S") { echo "SGST"; } else { echo "IGST"; } ?></td>
                                            <td><?php echo $key->total_before_tax; ?> </td>
                                            <td><?php echo $key->total_gst_amount; ?> </td>
                                            <td><?php echo $key->total; ?> </td>
                                            <td><?php echo $key->company_name; ?></td>
                                            <td><?php echo $key->customer_gst; ?></td>
                                                      
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

