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
                   Itemwise Report 
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
<!--                    <div class="col-md-6">
                         Horizontal Form 
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Itemwise Report </h3>
                            </div>
                             /.box-header 
                             form start 
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/get_itemwise_report_by_date">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" name="to_date" required="" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                </div>
                                 /.box-body 
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                 /.box-footer 
                            </form>
                        </div>
                         /.box 

                    </div>-->


                    <div class="col-md-12">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Item Wise Report</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_itemwise_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date1"  class="form-control backdate" name="from_date" required="" value="<?php echo $from_date; ?>" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date1" class="form-control" name="to_date" required="" value="<?php echo $to_date; ?>" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Company</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm company_search_name"  name="company_name" id="company_name">
                                                <option value="">Select Company</option>
                                                <?php foreach ($company_name as $key) { ?>
                                                
                                                  <option value="<?php echo $key->company_name ?>"  
                                                            <?php   
                                                            if ($company_name_str == $key->company_name) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?> ><?php echo $key->company_name . " - " . $key->c_code; ?></option>
                                                
                                                <?php } ?>  
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Item</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm company_search_name"  name="product_name" id="product_name">
                                                <option value="">Select Item</option>
                                                <?php foreach ($item_name as $key) { ?>
                                                
                                                   <option value="<?php echo $key->code ?>"  
                                                            <?php   
                                                            if ($product_name == $key->code) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?> ><?php echo $key->code; ?></option>
                                                
                                                
<!--                                                    <option value="<?php// echo $key->code; ?>"><?php// echo $key->code; ?></option> -->
                                                <?php } ?>  
                                            </select>
                                        </div>
                                    </div>

                                    <center><button type="reset" class="btn btn-default">Cancel</button>
                                        <button type="submit" class="btn btn-success">Submit</button></center>


                                </div>
                                <!-- /.box-body -->
                                
                                <!-- /.box-footer -->
                            </form>
                       <a href="<?php echo base_url(); ?>ReportController/get_itemwise_report_by_date"><button class="btn btn-success pull-right">Export to Excel</button></a>

                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Invoice No</th>
                                        <th>Invoice Date</th>
                                        <th>Invoice Amount</th>
                                        <th>Invoice Balance</th>
                                        <th>Payment Due Date</th>
                                        <th>Company Name</th>
                                        <th>Customer Name</th>
                                        <th>Pan Card</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        <th>Item Name</th>

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
                                         
                                            <td><?php echo $key->invoice_number; ?> </td>
                                            <td><?php echo $key->invoice_date; ?> </td>
                                          <td><?php echo $key->total; ?> </td>
                                          <td><?php echo $key->balance; ?> </td>
                                          <td><?php echo $key->payment_due_date; ?> </td>
                                          <td><?php echo $key->company_name; ?> </td>
                                          <td><?php echo $key->fullname; ?> </td>
                                          <td><?php echo $key->pancard; ?> </td>
                                          <td><?php echo $key->email; ?> </td>
                                          <td><?php echo $key->mobile; ?> </td>
                                          <td><?php echo $key->address; ?> </td>
                                           <td><?php echo $key->product_name; ?> </td>
                                          
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

