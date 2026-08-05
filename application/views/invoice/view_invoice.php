<?php
//print_r($invoices);die();

$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    .invoice-header-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 6px;
    }

    .invoice-header-actions .btn {
        margin-bottom: 4px;
    }

    @media (max-width: 991px) {
        .invoice-header-actions {
            justify-content: flex-start;
        }
    }
</style>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    GST Invoice 
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
                                    <div class="col-md-3">
                                        <h3 class="box-title">GST Invoice Details</h3> 
                                    </div>
                                    <div class="col-md-4">
                                        <form action="<?php echo base_url(); ?>InvoiceController/get_monthyearwise_record" method="post">
                                            <div class="form-group row">
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control onlymonth input-sm pull-right" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off" required="">
                                                </div>
                                                <button class="btn btn-primary pull-right" name="submit" value="">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-5 invoice-header-actions"> 
                                        <a href="<?php echo base_url(); ?>InvoiceController/index?str=All" class="btn btn-success btn-sm">Show All Invoice</a>
                                        <a href="<?php echo base_url(); ?>InvoiceController/create_invoice" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Create Invoice</a>
                                        <button type="button" class="btn btn-info btn-sm view-modal-invoice-waybill-create"><i class="fa fa-truck"></i> Create E-Way Bill</button>
                                    </div>
                                </div>
                             </div>
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_invoice_data_by_status' && $this->uri->segment(3) == '2') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>InvoiceController/get_invoice_data_by_status/2">Sent <span class="badge badge-light"> <?php echo $invoice_sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_invoice_data_by_status' && $this->uri->segment(3) == '1') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>InvoiceController/get_invoice_data_by_status/1">Draft <span class="badge badge-light"> <?php echo $invoice_draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'index' || $this->uri->segment(2) == '' || $this->uri->segment(2) == 'get_monthyearwise_record') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>InvoiceController/index?str=All">All Invoices <span class="badge badge-light"> <?php echo $invoice_count; ?></span></a>
                                </li>
                            </ul>
                            <!-- /.box-header -->
                            <div class="box-body">

                

                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Due</th>
                                            <th>Due Status</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Company Name</th>
                                            <!-- <th>Sales Person</th> -->
                                            <th>Type</th>
                                            <th>Total</th>
                                            <th>Balance</th>
                                            <th>Approve</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $unpaid = 0; ?>
                                        <?php
                                        $i = 1;
                                        
                                        foreach ($invoices as $key) {
                                            $invoice_number = !empty($key->invoice_number) ? $key->invoice_number : (!empty($key->number_fk) ? $key->number_fk : '');
                                            ?>
                                            <tr>
                                               
                                                <td>
                                                    <?php echo $i; ?>
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
                                                        <a href="<?php echo base_url() . 'InvoiceController/show_invoice/' . $key->id; ?>"><button type="submit" class="btn btn-danger">Over Due <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?></button></a>
                                                        <input type="hidden" class="form-control setunpaid" name="setunpaid" id="setunpaid" value="<?php $unpaid; ?>">
                                                        <?php
                                                        $unpaid++;
                                                    } else if (($date >= $currentdate) && ($balance_to_pay != 0.00)) {
                                                        ?>

                                                        <a href="<?php echo base_url() . 'InvoiceController/show_invoice/' . $key->id; ?>"><button type="submit" class="btn btn-info">Due on <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?></button></a>

                                                        <input type="hidden" class="form-control setunpaid" name="setunpaid" id="setunpaid" value="<?php $unpaid; ?>">
                                                        <?php
                                                        $unpaid++;
                                                    } else if ($balance_to_pay == 0.00) {
                                                        ?>
                                                        <a href="<?php echo base_url() . 'InvoiceController/show_invoice/' . $key->id; ?>"><button type="submit" class="btn btn-success">Paid <?php } ?></button></a>


                                                </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->invoice_date)); ?> </td>
                                                <td><input type="text" class="hide" id="get_invoice_no<?php echo $i; ?>" name="get_invoice_no" value="<?php echo $invoice_number; ?>">
                                                    <a href="<?php echo base_url() . 'InvoiceController/show_invoice/' . $key->id ?>"><?php echo $invoice_number; ?> </a>
                                                </td>

                                                <td> <?php echo $key->company_name; ?> </td>
                                                
                                                 <td><?php if ($key->gst_type != 'I') { ?>
                                                         CGST/ SGST
                                                     <?php } else { ?>
                                                         IGST
                                                     <?php } ?>
                                                         </td>
                                                
                                                <td> <?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->total), 0); ?> </td>
                                                <td> <?php
                                                    echo indian_number_format(round($balance_to_pay), 0);
                                                    ?> </td>
                                                <td>
                                                    <?php if ($key->status == 4) { ?>
                                                        <?php
                                                        echo "<html><i class='fa fa-check'></i></html>";
                                                    } else {
                                                        ?>
                                                        <input type="radio" class="approved-invoice" id="approved<?php echo $i; ?>" name="approved" value="4">
                                                        <i class='fa fa-check' style="display:none;" id="app<?php echo $i; ?>"></i>
                                                    <?php } ?>
                                                </td>

                                                <td>
                                                    <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                        <span class="caret"></span></button>
                                                    <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                        <li><a class="view-modal-invoice-email-send" href="#" data-id="<?php echo $invoice_number; ?>"> Send </a></li>
                                                        <li><a class="view-modal-invoice-reminder-send" href="#" data-id="<?php echo $invoice_number; ?>"> Reminder </a></li>
                                                        <li><a class="view-modal-invoice-whatsapp-send" href="#" data-id="<?php echo $invoice_number; ?>" data-pdf="<?php echo base_url() . 'Pdf/download_invoice/' . $invoice_number ?>/yes"> WhatsApp </a></li>
                                                        <li><a class="view-modal-invoice-waybill" href="#" data-id="<?php echo $invoice_number; ?>" data-pdf="<?php echo base_url() . 'Pdf/download_invoice/' . $invoice_number ?>/yes" data-company="<?php echo htmlspecialchars($key->company_name, ENT_QUOTES, 'UTF-8'); ?>" data-date="<?php echo date('d-m-Y', strtotime($key->invoice_date)); ?>" data-total="<?php echo round($key->total); ?>" data-vehicle="<?php echo isset($key->vehicle_no) ? htmlspecialchars($key->vehicle_no, ENT_QUOTES, 'UTF-8') : ''; ?>"> E-Way Bill </a></li>

                                                        <li role="presentation" class="divider"></li>
                                                        <li><a href="<?php echo base_url() . 'InvoiceController/edit_invoice_details/' . $invoice_number; ?>">Edit</a></li>
                                                        <li><a href="<?php echo base_url() . 'Pdf/download_invoice/' . $invoice_number ?>/yes" name="btn_submit" id="download1">Export As PDF</a></li>
                                                        <?php if ($key->status == 4 && $key->balance != 0) { ?>
                                                            <li><a class="view-modal-invoice-payment" data-toggle="modal" data-href="#"  data-id="<?php echo $key->id; ?>" data-target="#modal"> Enter Payment </a>  </li>                                                          </li>
                                                        <?php } ?>

                                                        <li style="display:none;" id="payment_enable<?php echo $i; ?>"><a class="view-modal-invoice-payment"  data-toggle="modal" data-href="#"  data-id="<?php echo $key->id; ?>" data-target="#modal"> Enter Payment </a>  </li>      
                                                        <?php if ($key->balance != 0) { ?>
                                                        <li><a class="view-modal-mark-paid" data-toggle="modal" data-href="#" data-id="<?php echo $key->id; ?>" data-target="#modal"><i class="fa fa-check-circle"></i> Mark as Paid</a></li>
                                                        <li><a href="<?php echo base_url() . 'InvoiceController/payment_in'; ?>"><i class="fa fa-money"></i> Payment In</a></li>
                                                        <?php } ?>
                                                        <li><a href="<?php echo base_url() . 'InvoiceController/show_invoice/' . $key->id; ?>">View</a></li>
                                                        <li><a href="<?php echo base_url() . 'InvoiceController/delete_invoice_by_invoice_number/' . $invoice_number; ?>" onClick="return confirm('Are you sure you want to delete?')">Delete</a></li>
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
                <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>InvoiceController/edit_invoice_payment">
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
                                    <input type="number"  class="form-control allownumericwithdecimal" name="paid" id="paid" value=""  required="" min=0 oninput="validity.valid||(value='');">
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
                                    <input type="text" class="form-control backdate"   name="date" id="date" value="" required="" onkeydown="return false;" autocomplete="off">
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
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InvoiceController/send_invoice_email" enctype="multipart/form-data">
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

    <!-- Reminder modal -->
    <div id="reminderModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-warning">
                    <center><h4 class="modal-title">Send Reminder<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InvoiceController/send_invoice_reminder_email" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="invoice_number" id="reminder_invoice_number" required="">
                                    <input type="email" class="form-control input-sm" name="to_email" id="reminder_to_email" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Subject<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="subject" id="reminder_subject" rows="2" required=""></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Message</label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="message" id="reminder_message" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Send a copy to</label>
                                <div class="col-sm-7">
                                    <input type="checkbox" name="copy_email" id="reminder_copy_email"> <?php echo $set_cc_email; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send Reminder</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="whatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center><h4 class="modal-title">Send WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="hidden" class="form-control" id="whatsapp_invoice_number" value="">
                                <input type="text" class="form-control input-sm" id="whatsapp_mobile" placeholder="Enter mobile number" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Message<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <textarea class="form-control input-sm" id="whatsapp_message" rows="4" required=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="whatsapp_send_link" target="_blank" rel="noopener" class="btn btn-success"><i class="fa fa-whatsapp" aria-hidden="true"></i> Send WhatsApp</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="waybillModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-info">
                    <center><h4 class="modal-title">E-Way Bill Details<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row" id="waybill_invoice_select_group">
                            <label class="col-sm-4 control-label">Select Invoice<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <select class="form-control input-sm" id="waybill_invoice_select">
                                    <option value="">Select Invoice</option>
                                    <?php foreach ($invoices as $waybill_invoice) {
                                        $waybill_invoice_number = !empty($waybill_invoice->invoice_number) ? $waybill_invoice->invoice_number : (!empty($waybill_invoice->number_fk) ? $waybill_invoice->number_fk : '');
                                        if ($waybill_invoice_number == '') {
                                            continue;
                                        }
                                        ?>
                                        <option value="<?php echo htmlspecialchars($waybill_invoice_number, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-id="<?php echo htmlspecialchars($waybill_invoice_number, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-pdf="<?php echo base_url() . 'Pdf/download_invoice/' . $waybill_invoice_number ?>/yes"
                                                data-company="<?php echo htmlspecialchars($waybill_invoice->company_name, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-date="<?php echo date('d-m-Y', strtotime($waybill_invoice->invoice_date)); ?>"
                                                data-total="<?php echo round($waybill_invoice->total); ?>"
                                                data-vehicle="<?php echo isset($waybill_invoice->vehicle_no) ? htmlspecialchars($waybill_invoice->vehicle_no, ENT_QUOTES, 'UTF-8') : ''; ?>">
                                            <?php echo htmlspecialchars($waybill_invoice_number . ' - ' . $waybill_invoice->company_name, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Invoice No</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm" id="waybill_invoice_number" readonly="">
                                <input type="hidden" id="waybill_pdf_url">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Party Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm" id="waybill_party_name" readonly="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Invoice Date</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm" id="waybill_invoice_date" readonly="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Invoice Value</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm" id="waybill_invoice_value" readonly="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">E-Way Bill No<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm waybill-field" id="waybill_no" maxlength="12" placeholder="12 digit e-way bill no">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Generated Date<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm waybill-field date" id="waybill_generated_date" onkeydown="return false;" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Valid Upto</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm waybill-field date" id="waybill_valid_upto" onkeydown="return false;" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Transport Mode<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <select class="form-control input-sm waybill-field" id="waybill_transport_mode">
                                    <option value="">Select Mode</option>
                                    <option value="Road">Road</option>
                                    <option value="Rail">Rail</option>
                                    <option value="Air">Air</option>
                                    <option value="Ship">Ship</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Transporter Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm waybill-field" id="waybill_transporter_name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Transporter GSTIN</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm waybill-field" id="waybill_transporter_gstin" maxlength="15" style="text-transform: uppercase;">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Vehicle No<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm waybill-field" id="waybill_vehicle_no" style="text-transform: uppercase;">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Distance (KM)</label>
                            <div class="col-sm-7">
                                <input type="number" class="form-control input-sm waybill-field" id="waybill_distance" min="0" step="1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Mobile</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm" id="waybill_mobile" placeholder="Enter mobile number">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Message</label>
                            <div class="col-sm-7">
                                <textarea class="form-control input-sm" id="waybill_message" rows="5" readonly=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="waybill_whatsapp_link" target="_blank" rel="noopener" class="btn btn-success"><i class="fa fa-whatsapp" aria-hidden="true"></i> Send WhatsApp</a>
                    <button type="button" id="waybill_print_btn" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function openSingleModal(modalSelector) {
                $('.modal.in').modal('hide');
                $(modalSelector).modal('show');
            }

            function buildWhatsAppUrl() {
                var mobile = ($('#whatsapp_mobile').val() || '').replace(/[^0-9]/g, '');
                var message = $('#whatsapp_message').val() || '';

                if (!mobile || !message.trim()) {
                    $('#whatsapp_send_link').attr('href', '#');
                    return;
                }

                $('#whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
            }

            function waybillStorageKey(invoiceNumber) {
                return 'invoice_waybill_' + invoiceNumber;
            }

            function getWaybillData() {
                return {
                    invoice_number: $('#waybill_invoice_number').val(),
                    party_name: $('#waybill_party_name').val(),
                    invoice_date: $('#waybill_invoice_date').val(),
                    invoice_value: $('#waybill_invoice_value').val(),
                    pdf_url: $('#waybill_pdf_url').val(),
                    waybill_no: $('#waybill_no').val(),
                    generated_date: $('#waybill_generated_date').val(),
                    valid_upto: $('#waybill_valid_upto').val(),
                    transport_mode: $('#waybill_transport_mode').val(),
                    transporter_name: $('#waybill_transporter_name').val(),
                    transporter_gstin: ($('#waybill_transporter_gstin').val() || '').toUpperCase(),
                    vehicle_no: ($('#waybill_vehicle_no').val() || '').toUpperCase(),
                    distance: $('#waybill_distance').val(),
                    mobile: $('#waybill_mobile').val()
                };
            }

            function setWaybillData(data) {
                $('#waybill_no').val(data.waybill_no || '');
                $('#waybill_generated_date').val(data.generated_date || '');
                $('#waybill_valid_upto').val(data.valid_upto || '');
                $('#waybill_transport_mode').val(data.transport_mode || '');
                $('#waybill_transporter_name').val(data.transporter_name || '');
                $('#waybill_transporter_gstin').val(data.transporter_gstin || '');
                $('#waybill_vehicle_no').val(data.vehicle_no || '');
                $('#waybill_distance').val(data.distance || '');
                $('#waybill_mobile').val(data.mobile || '');
            }

            function buildWaybillMessage() {
                var data = getWaybillData();
                var lines = [
                    'Dear Sir/Madam,',
                    '',
                    'E-Way Bill details for Invoice ' + data.invoice_number + ':',
                    'Party: ' + data.party_name,
                    'Invoice Date: ' + data.invoice_date,
                    'Invoice Value: ' + data.invoice_value,
                    'E-Way Bill No: ' + data.waybill_no,
                    'Generated Date: ' + data.generated_date,
                    'Valid Upto: ' + (data.valid_upto || '-'),
                    'Transport Mode: ' + data.transport_mode,
                    'Transporter Name: ' + (data.transporter_name || '-'),
                    'Transporter GSTIN: ' + (data.transporter_gstin || '-'),
                    'Vehicle No: ' + data.vehicle_no,
                    'Distance: ' + (data.distance || '-') + ' KM',
                    'Invoice PDF: ' + data.pdf_url,
                    '',
                    'Thanks.'
                ];

                $('#waybill_message').val(lines.join('\n'));
            }

            function buildWaybillWhatsAppUrl() {
                var mobile = ($('#waybill_mobile').val() || '').replace(/[^0-9]/g, '');
                var message = $('#waybill_message').val() || '';

                if (!mobile || !message.trim()) {
                    $('#waybill_whatsapp_link').attr('href', '#');
                    return;
                }

                $('#waybill_whatsapp_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
            }

            function validateWaybill() {
                var data = getWaybillData();
                if (!data.invoice_number) {
                    alert('Please select invoice.');
                    return false;
                }

                if (!data.waybill_no || !data.generated_date || !data.transport_mode || !data.vehicle_no) {
                    alert('Please fill E-Way Bill No, Generated Date, Transport Mode and Vehicle No.');
                    return false;
                }

                if (!/^[0-9]{12}$/.test(data.waybill_no)) {
                    alert('Please enter a valid 12 digit E-Way Bill No.');
                    return false;
                }

                return true;
            }

            function cacheWaybill() {
                var data = getWaybillData();
                localStorage.setItem(waybillStorageKey(data.invoice_number), JSON.stringify(data));
            }

            function saveWaybill() {
                var data = getWaybillData();
                cacheWaybill();

                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/save_invoice_waybill',
                    type: 'POST',
                    dataType: 'json',
                    data: data
                });
            }

            function escapeHtml(value) {
                return $('<div>').text(value || '').html();
            }

            function printWaybill() {
                if (!validateWaybill()) {
                    return;
                }

                saveWaybill();
                var data = getWaybillData();
                var printWindow = window.open('', '_blank');
                var html = '<html><head><title>E-Way Bill ' + escapeHtml(data.invoice_number) + '</title>';
                html += '<style>body{font-family:Arial,sans-serif;margin:30px;color:#222}h2{text-align:center;margin-bottom:20px}table{width:100%;border-collapse:collapse}td,th{border:1px solid #444;padding:8px;font-size:13px}th{background:#f2f2f2;text-align:left;width:35%}.footer{margin-top:25px;font-size:12px;color:#555}</style>';
                html += '</head><body><h2>E-Way Bill Details</h2><table>';
                html += '<tr><th>Invoice No</th><td>' + escapeHtml(data.invoice_number) + '</td></tr>';
                html += '<tr><th>Party Name</th><td>' + escapeHtml(data.party_name) + '</td></tr>';
                html += '<tr><th>Invoice Date</th><td>' + escapeHtml(data.invoice_date) + '</td></tr>';
                html += '<tr><th>Invoice Value</th><td>' + escapeHtml(data.invoice_value) + '</td></tr>';
                html += '<tr><th>E-Way Bill No</th><td>' + escapeHtml(data.waybill_no) + '</td></tr>';
                html += '<tr><th>Generated Date</th><td>' + escapeHtml(data.generated_date) + '</td></tr>';
                html += '<tr><th>Valid Upto</th><td>' + escapeHtml(data.valid_upto) + '</td></tr>';
                html += '<tr><th>Transport Mode</th><td>' + escapeHtml(data.transport_mode) + '</td></tr>';
                html += '<tr><th>Transporter Name</th><td>' + escapeHtml(data.transporter_name) + '</td></tr>';
                html += '<tr><th>Transporter GSTIN</th><td>' + escapeHtml(data.transporter_gstin) + '</td></tr>';
                html += '<tr><th>Vehicle No</th><td>' + escapeHtml(data.vehicle_no) + '</td></tr>';
                html += '<tr><th>Distance</th><td>' + escapeHtml(data.distance) + ' KM</td></tr>';
                html += '<tr><th>Invoice PDF</th><td>' + escapeHtml(data.pdf_url) + '</td></tr>';
                html += '</table><div class="footer">Printed on ' + escapeHtml(new Date().toLocaleString()) + '</div></body></html>';
                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            }

            $('.view-modal-invoice-email-send').click(function(event) {
                event.preventDefault();
                event.stopPropagation();
                openSingleModal('#emailModal');

                var invoiceNumber = $(this).data('id');
                var row = $(this).closest('tr');
                var dueStatus = $.trim(row.find('td:eq(4)').text());
                var invoiceDate = $.trim(row.find('td:eq(5)').text());

                $('#emailModal input[name="invoice_number"]').val(invoiceNumber);

                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/get_customer_email',
                    type: 'POST',
                    dataType: 'json',
                    data: { invoice_number: invoiceNumber },
                    success: function(response) {
                        $('#to_email').val(response && response.email ? response.email : '');
                    },
                    error: function() {
                        $('#to_email').val('');
                    }
                });

                $('#subject').val('Invoice ' + invoiceNumber + ' from <?php echo addslashes($session_data_head2['company_name']); ?>');
                $('#message').val('Dear Sir/Madam,\n\nPlease find Invoice ' + invoiceNumber + '.\nInvoice Date: ' + invoiceDate + '\nStatus: ' + dueStatus + '\n\nThanks.');
            });

            $('.view-modal-invoice-reminder-send').click(function(event) {
                event.preventDefault();
                event.stopPropagation();
                openSingleModal('#reminderModal');

                var invoiceNumber = $(this).data('id');
                var row = $(this).closest('tr');
                var dueInfo = $.trim(row.find('td:eq(3)').text());
                var dueStatus = $.trim(row.find('td:eq(4)').text());
                var invoiceDate = $.trim(row.find('td:eq(5)').text());

                $('#reminder_invoice_number').val(invoiceNumber);

                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/get_customer_email',
                    type: 'POST',
                    dataType: 'json',
                    data: { invoice_number: invoiceNumber },
                    success: function(response) {
                        $('#reminder_to_email').val(response && response.email ? response.email : '');
                    },
                    error: function() {
                        $('#reminder_to_email').val('');
                    }
                });

                $('#reminder_subject').val('Reminder: Invoice ' + invoiceNumber + ' payment follow-up');
                $('#reminder_message').val('Dear Sir/Madam,\n\nThis is a reminder for Invoice ' + invoiceNumber + '.\nInvoice Date: ' + invoiceDate + '\n' + dueInfo + '\n' + dueStatus + '\n\nPlease share payment update.\n\nThanks.');
            });

            $('.view-modal-invoice-whatsapp-send').click(function(event) {
                event.preventDefault();
                event.stopPropagation();
                openSingleModal('#whatsappModal');

                var invoiceNumber = $(this).data('id');
                var pdfUrl = $(this).data('pdf');
                var row = $(this).closest('tr');
                var dueInfo = $.trim(row.find('td:eq(3)').text());
                var dueStatus = $.trim(row.find('td:eq(4)').text());
                var invoiceDate = $.trim(row.find('td:eq(5)').text());

                $('#whatsapp_invoice_number').val(invoiceNumber);
                $('#whatsapp_download_link').attr('href', pdfUrl);
                $('#whatsapp_download_link').hide();

                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/get_customer_email',
                    type: 'POST',
                    dataType: 'json',
                    data: { invoice_number: invoiceNumber },
                    success: function(response) {
                        $('#whatsapp_mobile').val(response && response.mobile ? response.mobile : '');
                        buildWhatsAppUrl();
                    },
                    error: function() {
                        $('#whatsapp_mobile').val('');
                        buildWhatsAppUrl();
                    }
                });

                $('#whatsapp_message').val('Dear Sir/Madam,\nInvoice ' + invoiceNumber + ' is shared with you.\nInvoice Date: ' + invoiceDate + '\n' + dueInfo + '\n' + dueStatus + '\nClick to Download PDF:\n' + pdfUrl + '\nPlease check and confirm.\nThanks.');
                buildWhatsAppUrl();
            });

            function openWaybillForInvoice(source, row) {
                var invoiceNumber = source.data('id') || source.val();
                if (!invoiceNumber) {
                    $('#waybill_invoice_number').val('');
                    $('#waybill_pdf_url').val('');
                    $('#waybill_party_name').val('');
                    $('#waybill_invoice_date').val('');
                    $('#waybill_invoice_value').val('');
                    setWaybillData({});
                    buildWaybillMessage();
                    buildWaybillWhatsAppUrl();
                    return;
                }

                var storedData = localStorage.getItem(waybillStorageKey(invoiceNumber));
                var parsedData = {};

                if (storedData) {
                    try {
                        parsedData = JSON.parse(storedData) || {};
                    } catch (e) {
                        parsedData = {};
                    }
                }

                $('#waybill_invoice_number').val(invoiceNumber);
                $('#waybill_pdf_url').val(source.data('pdf') || '');
                $('#waybill_party_name').val(source.data('company') || (row ? $.trim(row.find('td:eq(6)').text()) : ''));
                $('#waybill_invoice_date').val(source.data('date') || (row ? $.trim(row.find('td:eq(4)').text()) : ''));
                $('#waybill_invoice_value').val(source.data('total') || (row ? $.trim(row.find('td:eq(8)').text()) : ''));

                setWaybillData({
                    waybill_no: parsedData.waybill_no || '',
                    generated_date: parsedData.generated_date || '',
                    valid_upto: parsedData.valid_upto || '',
                    transport_mode: parsedData.transport_mode || 'Road',
                    transporter_name: parsedData.transporter_name || '',
                    transporter_gstin: parsedData.transporter_gstin || '',
                    vehicle_no: parsedData.vehicle_no || (source.data('vehicle') || ''),
                    distance: parsedData.distance || '',
                    mobile: parsedData.mobile || ''
                });

                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/get_customer_email',
                    type: 'POST',
                    dataType: 'json',
                    data: { invoice_number: invoiceNumber },
                    success: function(response) {
                        if (!$('#waybill_mobile').val()) {
                            $('#waybill_mobile').val(response && response.mobile ? response.mobile : '');
                        }
                        buildWaybillMessage();
                        buildWaybillWhatsAppUrl();
                    },
                    error: function() {
                        buildWaybillMessage();
                        buildWaybillWhatsAppUrl();
                    }
                });

                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/get_invoice_waybill',
                    type: 'POST',
                    dataType: 'json',
                    data: { invoice_number: invoiceNumber },
                    success: function(response) {
                        if (response && response.waybill_no) {
                            setWaybillData({
                                waybill_no: response.waybill_no,
                                generated_date: response.generated_date,
                                valid_upto: response.valid_upto,
                                transport_mode: response.transport_mode,
                                transporter_name: response.transporter_name,
                                transporter_gstin: response.transporter_gstin,
                                vehicle_no: response.vehicle_no,
                                distance: response.distance,
                                mobile: response.mobile || $('#waybill_mobile').val()
                            });
                            buildWaybillMessage();
                            buildWaybillWhatsAppUrl();
                        }
                    }
                });

                buildWaybillMessage();
                buildWaybillWhatsAppUrl();
            }

            $('.view-modal-invoice-waybill').click(function(event) {
                event.preventDefault();
                event.stopPropagation();
                $('#waybill_invoice_select_group').hide();
                $('#waybill_invoice_select').val($(this).data('id'));
                openSingleModal('#waybillModal');
                openWaybillForInvoice($(this), $(this).closest('tr'));
            });

            $('.view-modal-invoice-waybill-create').click(function(event) {
                event.preventDefault();
                $('#waybill_invoice_select_group').show();
                $('#waybill_invoice_select').val('');
                openSingleModal('#waybillModal');
                openWaybillForInvoice($('#waybill_invoice_select option:selected'), null);
            });

            $('#waybill_invoice_select').on('change', function() {
                openWaybillForInvoice($(this).find('option:selected'), null);
            });

            $('#whatsapp_mobile, #whatsapp_message').on('input', function() {
                buildWhatsAppUrl();
            });

            $('.waybill-field, #waybill_mobile').on('input change', function() {
                if (this.id === 'waybill_transporter_gstin' || this.id === 'waybill_vehicle_no') {
                    this.value = this.value.toUpperCase();
                }

                buildWaybillMessage();
                buildWaybillWhatsAppUrl();

                if ($('#waybill_invoice_number').val()) {
                    cacheWaybill();
                }
            });

            $('#whatsapp_send_link').on('click', function(event) {
                if ($(this).attr('href') === '#') {
                    event.preventDefault();
                    alert('Please enter valid mobile number and message.');
                    return;
                }

                $('#whatsapp_download_link').show();
            });

            $('#waybill_whatsapp_link').on('click', function(event) {
                if (!validateWaybill()) {
                    event.preventDefault();
                    return;
                }

                saveWaybill();
                buildWaybillMessage();
                buildWaybillWhatsAppUrl();

                if ($(this).attr('href') === '#') {
                    event.preventDefault();
                    alert('Please enter valid mobile number.');
                }
            });

            $('#waybill_print_btn').on('click', function() {
                printWaybill();
            });
        });
    </script>
    
