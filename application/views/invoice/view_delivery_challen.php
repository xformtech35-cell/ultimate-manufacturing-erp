<?php
echo "pravinccccccccccccccccccc sir";die();
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
                    Delivery Challan
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">GST Invoice</a></li>
                    <li class="active">GST Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">

                            <div class="box-header">
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <h3 class="box-title">Delivery Challan Details</h3> 
                                    </div>
                                    <div class="col-md-3">
                                        <form action="<?php echo base_url(); ?>DeliveryChallanController/get_monthyearwise_record" method="post">
                                               <div class="form-group row">
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control onlymonth input-sm" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off" required="">
                                                </div>
                                                <button class="btn btn-primary pull-right" name="submit" value="">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-5">
                                        <a href="<?php echo base_url(); ?>DeliveryChallanController/index?str=All"><button class="btn btn-success btn-sm"> Show All Delivery Challan</button></a>
                                        <a href="<?php echo base_url(); ?>DeliveryChallanController/create_delivery_challan"><button class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i>Create Delivery Challan</button></a>
                                    </div>
                                </div>
                            </div>


                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_delivery_challan_data_by_status' && $this->uri->segment(3) == '2') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>DeliveryChallanController/get_delivery_challan_data_by_status/2">Sent <span class="badge badge-light"> <?php echo $invoice_sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_delivery_challan_data_by_status' && $this->uri->segment(3) == '1') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>DeliveryChallanController/get_delivery_challan_data_by_status/1">Draft <span class="badge badge-light"> <?php echo $invoice_draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'index' || $this->uri->segment(2) == '' || $this->uri->segment(2) == 'get_monthyearwise_record') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>DeliveryChallanController/index?str=All">All Invoices <span class="badge badge-light"> <?php echo $invoice_count; ?></span></a>
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

                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Due</th>
                                            <th>Due Status</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Customer</th>
<!--                                            <th>Sales Person</th>-->
                                            <th>Total</th>
                                            <th>Type</th>
                                            <th>Balance</th>
                                            <th>Approve</th>-->
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $unpaid = 0; ?>
                                        <?php
                                        $i = 1;
                                        foreach ($invoices as $key) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $i;  echo $key->status; ?>
                                                </td>

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

                                                <td>
                                                    <?php
                                                    $today1 = date('d-m-Y');
                                                    $payment_due_date = date('Y-m-d', strtotime($key->payment_due_date));

                                                    $start = new DateTime($payment_due_date);
                                                    $end = new DateTime(); //Current date time
                                                    $diff = $start->diff($end);
                                                    $due_string = '';
                                                    if ($diff->invert > 0 && $diff->d >= 0) {
                                                        $due_after_days = $diff->d + 1;
                                                        $due_string = "Due after " . $due_after_days . " day(s)";
                                                        if ($key->balance != 0.00) {
                                                            ?>
                                                            <span style="color: gray;"><b><?php echo $due_string; ?></b></span>
                                                            <?php
                                                        }
                                                    } else if ($diff->invert == 0 && $diff->d >= 0) {
                                                        $due_string = "Due " . $diff->d . " day(s) ago";
                                                        if ($key->balance != 0.00) {
                                                            ?>
                                                            <span style="color: red;"><b><?php echo $due_string; ?></b></span>
                                                            <?php
                                                        }
                                                    }
                                                    ?>

                                                </td>
                                                <td> <?php
                                                    $balance_to_pay = $key->balance;
                                                    $today = date('Y-m-d');
                                                    $date = new DateTime($key->payment_due_date);
                                                    $currentdate = new DateTime($today);
                                                    if (($date < $currentdate) && ($balance_to_pay != 0.00)) {
                                                        ?>
                                                        <a href="<?php echo base_url() . 'DeliveryChallanController/show_delivery_challan/' . $key->id; ?>"><button type="submit" class="btn btn-danger">Over Due <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?></button></a>
                                                        <input type="hidden" class="form-control setunpaid" name="setunpaid" id="setunpaid" value="<?php $unpaid; ?>">
                                                        <?php
                                                        $unpaid++;
                                                    } else if (($date >= $currentdate) && ($balance_to_pay != 0.00)) {
                                                        ?>

                                                        <a href="<?php echo base_url() . 'DeliveryChallanController/show_delivery_challan/' . $key->id; ?>"><button type="submit" class="btn btn-info">Due on <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?></button></a>

                                                        <input type="hidden" class="form-control setunpaid" name="setunpaid" id="setunpaid" value="<?php $unpaid; ?>">
                                                        <?php
                                                        $unpaid++;
                                                    } else if ($balance_to_pay == 0.00) {
                                                        ?>
                                                        <a href="<?php echo base_url() . 'DeliveryChallanController/show_delivery_challan/' . $key->id; ?>"><button type="submit" class="btn btn-success">Paid <?php } ?></button></a>


                                                </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->invoice_date)); ?> </td>
                                                <td><input type="text" class="hide" id="get_invoice_no<?php echo $i; ?>" name="get_invoice_no" value="<?php echo $key->invoice_number; ?>">
                                                    <a href="<?php echo base_url() . 'DeliveryChallanController/show_delivery_challan/' . $key->id ?>"><?php echo $key->invoice_number; ?> </a>
                                                </td>

                                                <td> <?php echo $key->fullname; ?> </td>
                                                
<!--                                                 <td> <?php echo $key->sales_person; ?> </td>-->
                                                
                                                <td> <?php echo number_format($key->total, 2); ?> </td>
                                                <td><?php if ($key->gst_type != 'I') { ?>
                                                         SGST
                                                     <?php } else { ?>
                                                         IGST
                                                     <?php } ?>
                                                         </td>
                                                <td> <?php
                                                    echo number_format($balance_to_pay, 2);
                                                    ?> </td>
                                                <td>
                                                    <?php if ($key->status == 4) { ?>
                                                        <?php
                                                        echo "<html><i class='fa fa-check'></i></html>";
                                                    } else {
                                                        ?>
                                                        <input type="radio" class="approved-delivery-challan1" id="approved<?php echo $i; ?>" name="approved" value="4">
                                                        <i class='fa fa-check' style="display:none;" id="app<?php echo $i; ?>"></i>
                                                    <?php } ?>
                                                </td>
<!--                                                
                                                <td>
                                                    <?php if ($key->status == 4) { ?>
                                                        <?php
                                                        echo "<html><i class='fa fa-check'></i></html>";
                                                    } else {
                                                        ?>
                                                        <input type="radio" class="approved-invoice1" id="approved<?php echo $i; ?>" name="approved" value="4">
                                                        <i class='fa fa-check' style="display:none;" id="app<?php echo $i; ?>"></i>
                                                    <?php } ?>
                                                </td>-->
                                                

                                                <td>
                                                    <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                        <span class="caret"></span></button>
                                                    <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                        <li><a class="view-modal-delivery-challan-email-send" data-toggle="modal" data-href="#"  id="<?php echo $key->invoice_number; ?>" data-target="#emailModal"> Send </a></li> 
                                                        <li role="presentation" class="divider"></li>
                                                        <li><a href="<?php echo base_url() . 'Pdf/download_proforma_invoice/' . $key->invoice_number ?>" name="btn_submit" id="download1">Export As PDF</a></li>
                                                        
                                                        <?php if ($key->status == 4 && $key->balance != 0) { ?>
                                                            <li><a class="view-modal-delivery_challan-payment" data-toggle="modal" data-href="#"  data-id="<?php echo $key->id; ?>" data-target="#modal"> Enter Payment </a>  </li>      
                                                        <?php } ?> 

                                                        <li style="display:none;" id="payment_enable<?php echo $i; ?>"><a class="view-modal-delivery-challan-payment"  data-toggle="modal" data-href="#"  data-id="<?php echo $key->id; ?>" data-target="#modal"> Enter Payment </a>  </li>     
                                                        <li><a href="<?php echo base_url() . 'DeliveryChallanController/show_delivery_challan/' . $key->id; ?>">View</a></li>
                                                        <li><a href="<?php echo base_url() . 'DeliveryChallanController/delete_delivery_challan_by_invoice_number/' . $key->invoice_number; ?>" onClick="return confirm('Are you sure you want to delete?')">Delete</a></li>
                                                    </ul>


                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
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
                <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>ProformaInvoiceController/edit_proforma_payment">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Balance<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control"   name="id" id="id" value="" >
                                    <input type="hidden" class="form-control"   name="total" id="total" value="" >
                                    <input type="hidden" class="form-control"   name="invoice_number" id="invoice_number" value="" > 
                                    <input type="hidden" class="form-control"   name="customer_id_fk" id="customer_id_fk"> 

                                    <input type="text" readonly="" class="form-control input-sm"  name="balance" id="balance" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Type<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm "  name="payment_type" id="payment_type" required="">
                                        <option value="">Select Payment Type</option>
                                        <option value="Advance">Advance</option>
                                        <option value="Partial">Partial</option>
                                        <option value="Final">Final</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Paying Amount<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text"  class="form-control allownumericwithdecimal" name="paid" id="paid" value="" required="">
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
                                        <option value="4">Credit Card</option>
                                        <option value="5">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Note<span style="color: red;">*</span></label>
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
                            
                            
                            
                            
                              
                             <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Name</label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm "  name="bank_name" id="bank_name">
                                        <option value="">Select Payment Method</option>
                                        <option value="HDFC">HDFC Bank</option>
                                        <option value="SBI">SBI Bank</option>
                                        <option value="Saraswat">Saraswat Bank</option>
                                        <option value="IDBI">IDBI Bank</option>
                                        <option value="ICICI">ICICI Bank</option>
                                        <option value="AXIS">AXIS Bank</option>
                                        <option value="RBL">RBL Bank</option>
                                        <option value="INDUS">INDUS Bank</option>
                                        <option value="BOM">BOM Bank</option>
                                       
                                    </select>
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
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ProformaInvoiceController/send_invoice_email" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="customer_id" id="customer_id" value="" required="">
                                    <input type="hidden" class="form-control" name="invoice_number" id="invoice_number" required="">
                                    <input type="email" class="form-control input-sm"  name="to_email" id="to_email" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Subject<span style="color: red;">*</span> </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="subject" id="subject" rows="2" required=""></textarea>
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