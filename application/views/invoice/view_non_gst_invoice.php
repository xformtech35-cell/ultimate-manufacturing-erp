<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];
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
                   Non GST Invoice
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Non GST Invoice</a></li>
                    <li class="active">Non GST Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">

                            <div class="box-header">

                                <h3 class="box-title">Non GST Invoice Details</h3>

                                <a href="<?php echo base_url(); ?>InvoiceController/create_non_gst_invoice"><button class="btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i>Create Non GST Invoice</button></a>
<!--                                 <a href="<?php //echo base_url(); ?>InvoiceController/index"><button class="btn btn-danger btn-sm pull-right"><i class="fa fa-eye" aria-hidden="true"></i>
View Invoice</button></a>-->
                            </div>
                            
                                <ul class="nav nav-tabs">
                                  <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_non_gst_invoice_status_count' && $this->uri->segment(3) == '2') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>InvoiceController/get_non_gst_invoice_status_count/2">Sent <span class="badge badge-light"> <?php echo $invoice_sent_count; ?></span></a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_non_gst_invoice_status_count' && $this->uri->segment(3) == '1') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>InvoiceController/get_non_gst_invoice_status_count/1">Draft <span class="badge badge-light"> <?php echo $invoice_draft_count; ?></span></a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'index_non_gst' || $this->uri->segment(2) == '' || $this->uri->segment(2) == 'index') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>InvoiceController/index_non_gst">All Invoices <span class="badge badge-light"> <?php echo $invoice_count; ?></span></a>
                                  </li>
                                </ul>
                                                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <table id="example2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Due</th>
                                            <th>Due Status</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Balance</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $unpaid=0; ?>
                                        <?php $i=1; foreach ($non_gst_invoices as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                
                                                <?php   if($key->status==1){ ?>
                                                <td>Draft</td>
                                                <?php } ?>
                                                  <?php   if($key->status==2){ ?>
                                                <td>Sent</td>
                                                <?php } ?>
                                                  <?php   if($key->status==3){ ?>
                                                <td>Viewed</td>
                                                <?php } ?>
                                                  <?php   if($key->status==4){ ?>
                                                <td>Approved</td>
                                                <?php } ?>
                                                  <?php   if($key->status==5){ ?>
                                                <td>Rejected</td>
                                                <?php } ?>
                                                  <?php   if($key->status==6){ ?>
                                                <td>Canceled</td>
                                                <?php } if($key->status==0){ ?>
                                                <td></td><?php } ?>
                                                
                                                <td>
                                                    <?php
                                                    
                                                    $today1 = date('d-m-Y');
                                                    $payment_due_date = date('Y-m-d', strtotime($key->payment_due_date));
                                                   
                                                    $start = new DateTime($payment_due_date);
                                                    $end = new DateTime(); //Current date time
                                                    $diff = $start->diff($end);
                                                    $due_string='';
                                                   if($diff->invert > 0 && $diff->d >= 0 ){
                                                       $due_after_days = $diff->d +1;
                                                        $due_string = "Due after ".$due_after_days." day(s)";
                                                        if($key->balance != 0.00){?>
                                                       <span style="color: gray;"><b><?php echo $due_string;  ?></b></span>
                                                   <?php }
                                                   }
                                                   else if($diff->invert == 0 && $diff->d >= 0){
                                                       $due_string = "Due ". $diff->d. " day(s) ago";
                                                       if($key->balance != 0.00){?>
                                                       <span style="color: red;"><b><?php echo $due_string;  ?></b></span>
                                                   <?php }
                                                   }?>
                                                    
                                                </td>
                                                <td> <?php
                                                    $today = date('Y-m-d');
                                                    $date = new DateTime($key->payment_due_date);
                                                    $currentdate = new DateTime($today);
                                                    if (($date < $currentdate) && ($key->balance != 0.00)) {
                                                        ?>
                                                         <a href="<?php echo base_url() . 'InvoiceController/show_non_gst_invoice/' . $key->invoice_number; ?>"><button type="submit" class="btn btn-danger">Over Due <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?></button></a>
                                                        <input type="hidden" class="form-control setunpaid" name="setunpaid" id="setunpaid" value="<?php $unpaid; ?>">
                                                        <?php
                                                        $unpaid++;
                                                    } else if (($date >= $currentdate) && ($key->balance != 0.00)) {
                                                        ?>
                                                        
                                                        <a href="<?php echo base_url() . 'InvoiceController/show_non_gst_invoice/' . $key->invoice_number; ?>"><button type="submit" class="btn btn-info">Due on <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?></button></a>
                                                        
                                                        <input type="hidden" class="form-control setunpaid" name="setunpaid" id="setunpaid" value="<?php $unpaid; ?>">
                                                        <?php
                                                        $unpaid++;
                                                    } else if ($key->balance == 0.00) {
                                                        ?>
                                                        <a href="<?php echo base_url() . 'InvoiceController/show_non_gst_invoice/' . $key->invoice_number; ?>"><button type="submit" class="btn btn-success">Paid <?php } ?></button></a>
                                                </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->invoice_date)); ?> </td>
                                                <td><a href="<?php echo base_url() . 'InvoiceController/show_non_gst_invoice/' . $key->invoice_number ?>"><?php echo $key->invoice_number; ?> </a></td>

                                                <td> <?php echo $key->fullname; ?> </td>
                                                <td> <?php echo number_format($key->total,2); ?> </td>
                                                <td> <?php echo number_format($key->balance,2); ?> </td>

                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                             <li><a class="invoice-email-send-non-gst" data-toggle="modal" data-href="#"  data-id="<?php echo $key->invoice_number; ?>" data-target="#emailModal"> Send </a></li> 
                                                            <li role="presentation" class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'Pdf/download_non_gst_invoice/' . $key->invoice_number ?>" name="btn_submit" id="download1">Export As PDF</a></li>
                                                            <?php if($key->status == 4){ ?>
                                                            <li><a class="modal-non_gst_invoice-payment" data-toggle="modal" data-href="#"  data-id="<?php echo $key->id; ?>" data-target="#modal"> Enter Payment </a></li>
                                                            <?php } ?>
                                                            <li><a href="<?php echo base_url() . 'InvoiceController/show_non_gst_invoice/' . $key->invoice_number; ?>">View</a></li>
                                                            <li><a href="<?php echo base_url() . 'InvoiceController/delete_non_gst_invoice_by_invoice_number/' . $key->invoice_number; ?>" onClick="return confirm('Are you sure you want to delete?')">Delete</a></li>
                                                        </ul>
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- ./Customer modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center> <h4 class="modal-title">Enter Payment<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>InvoiceController/edit_non_gst_invoice_payment" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Balance<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control"  name="id" id="id" value="" required="">
                                    <input type="hidden" class="form-control"  name="total" id="total" value="" required="">
                                    <input type="hidden" class="form-control"  name="invoice_numbers" id="invoice_number" value="">  
                                    <input type="hidden" class="form-control"  name="customer_id_fk" id="customer_id_fk"> 
                                    
                                    <input type="text" readonly="" class="form-control input-sm"  name="balance" id="balance" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Paid<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="number" min="0" class="form-control"  name="paid" id="paid" value="" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Method<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm "  name="payment_method" id="payment_method" required="">
                                        <option value="">Select Payment Method</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Cheque</option>
                                        <option value="3">NetBanking</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Note<span style="color: red;">*</span> </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" required="" name="note" id="note" rows="3"></textarea>

                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Date<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control backdate"   name="date" id="date" value="" required="" onkeydown="return false;">
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave"  class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
    
    <!-- Email modal -->
    <div id="emailModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center> <h4 class="modal-title">Send Invoice<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InvoiceController/send_non_gst_invoice_email" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To</label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="customer_id" id="customer_id" value="" required="">
                                    <input type="hidden" class="form-control" name="invoice_number" id="invoice_number" required="">
                                    <input type="email" class="form-control input-sm"  name="to_email" id="to_email" required="">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Subject </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="subject" id="subject" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Message </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="message" id="message" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Send a copy to</label>
                                <div class="col-sm-7">
                                    <input type="checkbox" name="copy_email" id="copy_email"> <?php echo $set_cc_email; ?> 
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave"  class="btn btn-success"><i class="fa fa-paper-plane" aria-hidden="true"></i>
Send</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
     <!-- ./ End Email modal -->