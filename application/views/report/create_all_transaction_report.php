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
                    Report 
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
                                <h3 class="box-title">All Transaction Report</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_all_transaction_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date"  name="from_date" required="" value="<?php echo $from_date ?>" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check"  name="to_date" required="" value="<?php echo $to_date ?>" onkeydown="return false;"> 
                                        </div>
                                    </div>
<!--                                    <div class="form-group">
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
                                                            ?> ><?php echo $key->company_name; ?></option>
                                                
                                                <?php } ?>  
                                            </select>
                                        </div>
                                    </div>-->
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Transcation</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm company_search_name"  name="transaction_type" id="transaction_type">
                                              
             <option value="all_transcation" <?php if ($transaction_type == "all_transcation") { echo 'selected="selected"'; } ?> >All Transaction</option>    
             <option value="sales" <?php if ($transaction_type == "sales") { echo 'selected="selected"'; } ?> >Sales</option>    
             <option value="purchase" <?php if ($transaction_type == "purchase") { echo 'selected="selected"'; } ?> >Purchase</option>    
<!--              <option value="payment_in" <?php if ($transaction_type == "payment_in") { echo 'selected="selected"'; } ?> >Payment In</option>    
               <option value="payment_out" <?php if ($transaction_type == "payment_out") { echo 'selected="selected"'; } ?> >Payment Out</option>    
               <option value="credit_note" <?php if ($transaction_type == "credit_note") { echo 'selected="selected"'; } ?> >Credit Note</option>    
               <option value="debit_note" <?php if ($transaction_type == "credit_note") { echo 'selected="selected"'; } ?> >Debit Note</option>    
               <option value="sales_order" <?php if ($transaction_type == "sales_order") { echo 'selected="selected"'; } ?> >Sale Order</option>    
               <option value="purchase_order" <?php if ($transaction_type == "purchase_order") { echo 'selected="selected"'; } ?> >Purchase Order</option>    -->
               <option value="quotation" <?php if ($transaction_type == "quotation") { echo 'selected="selected"'; } ?> >Quotation</option>    
<!--               <option value="delivery_challan" <?php if ($transaction_type == "delivery_challan") { echo 'selected="selected"'; } ?> >Delivery Challan</option>    -->
                           
                                            </select>
                                        </div>
                                    </div>
                                    <center><button type="submit" class="btn btn-default">Cancel</button>
                                        <button type="submit" class="btn btn-success">Submit</button></center>
                                </div>
                                <!-- /.box-body -->
                                
                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url(); ?>ReportController/get_all_transaction_report"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                      
                                        <th>Date</th>
                                        <th>Ref No.</th>
                                        <th>Company Name</th>
                                        <th>Type</th>
                                        <th>Total</th>
                                        <th>Recevied / Paid</th>
                                          <th>Sales / Purchase</th>
                                        <th>Balance</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                    $i = 1;
                                   
                                        
                                 if( isset($result_sales) ) {
                                         foreach ($result_sales as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                         
                                            <td><?php echo $key->date; ?> </td>
                                            <td><?php echo $key->number_fk; ?> </td>
                                           <td><?php echo $key->company_name; ?> </td>
                                           
                                           <td> <?php  $gst_type = '';
                                           if ($key->gst_type != 'I') { 
                                                         $gst_type = 'SGST';
                                                     } else { 
                                                         $gst_type = 'IGST';
                                                         }
                                                         echo $gst_type; ?></td>
                                         <td><?php echo $key->total; ?> </td>
                                          <?php if ($key->status == 1) { ?>
                                                    <td>Draft</td>
                                                <?php } ?>
                                                <?php if ($key->status == 2) { ?>
                                                    <td>Sent</td>
                                                <?php } ?>
                                                <?php if ($key->status == 3) { ?>
                                                    <td>Viewed</td>
                                                <?php } ?>
                                                <?php if ($key->status == 4) { ?>
                                                    <td>Approved</td>
                                                <?php } ?>
                                                <?php if ($key->status == 5) { ?>
                                                    <td>Rejected</td>
                                                <?php } ?>
                                                <?php if ($key->status == 6) { ?>
                                                    <td>Canceled</td>
                                                <?php } if ($key->status == 0) { ?>
                                                    <td></td><?php } ?>

                                          <td><?php echo "Sales" ?> </td>
                                          <td><?php echo $key->total; ?> </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    
                                 }
                                    
                                     if( isset($result_quotation) ){
                                     foreach ($result_quotation as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                         
                                            <td><?php echo $key->date; ?> </td>
                                            <td><?php echo $key->number_fk; ?> </td>
                                           <td><?php echo $key->company_name; ?> </td>
                                           
                                           <td> <?php  $gst_type = '';
                                           if ($key->gst_type != 'I') { 
                                                         $gst_type = 'SGST';
                                                     } else { 
                                                         $gst_type = 'IGST';
                                                         }
                                                         echo $gst_type; ?></td>
                                         <td><?php echo $key->total; ?> </td>
                                          <?php if ($key->status == 1) { ?>
                                                    <td>Draft</td>
                                                <?php } ?>
                                                <?php if ($key->status == 2) { ?>
                                                    <td>Sent</td>
                                                <?php } ?>
                                                <?php if ($key->status == 3) { ?>
                                                    <td>Viewed</td>
                                                <?php } ?>
                                                <?php if ($key->status == 4) { ?>
                                                    <td>Approved</td>
                                                <?php } ?>
                                                <?php if ($key->status == 5) { ?>
                                                    <td>Rejected</td>
                                                <?php } ?>
                                                <?php if ($key->status == 6) { ?>
                                                    <td>Canceled</td>
                                                <?php } if ($key->status == 0) { ?>
                                                    <td></td><?php } ?>

                                          <td><?php echo "Quotation" ?> </td>
                                          <td><?php echo $key->total; ?> </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                     }
                                   if( isset($result_purchase) ){
                                     foreach ($result_purchase as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                         
                                            <td><?php echo $key->purchase_date; ?> </td>
                                            <td><?php echo $key->number_fk; ?> </td>
                                           <td><?php echo $key->company_name; ?> </td>
                                           
                                           <td> <?php  $gst_type = '';
                                           if ($key->gst_type != 'I') { 
                                                         $gst_type = 'SGST';
                                                     } else { 
                                                         $gst_type = 'IGST';
                                                         }
                                                         echo $gst_type; ?></td>
                                         <td><?php echo $key->total; ?> </td>
                                          <?php if ($key->status == 1) { ?>
                                                    <td>Draft</td>
                                                <?php } ?>
                                                <?php if ($key->status == 2) { ?>
                                                    <td>Sent</td>
                                                <?php } ?>
                                                <?php if ($key->status == 3) { ?>
                                                    <td>Viewed</td>
                                                <?php } ?>
                                                <?php if ($key->status == 4) { ?>
                                                    <td>Approved</td>
                                                <?php } ?>
                                                <?php if ($key->status == 5) { ?>
                                                    <td>Rejected</td>
                                                <?php } ?>
                                                <?php if ($key->status == 6) { ?>
                                                    <td>Canceled</td>
                                                <?php } if ($key->status == 0) { ?>
                                                    <td></td><?php } ?>

                                          <td><?php echo "Purchase" ?> </td>
                                          <td><?php echo $key->total; ?> </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    
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

