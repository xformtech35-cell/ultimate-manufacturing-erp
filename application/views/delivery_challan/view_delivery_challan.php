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
                    Delivery challan
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

                            <div class="box-header with-border" style="padding: 12px 15px; display: block !important; clear: both !important;">
                                <div style="float: left !important; display: inline-block;">
                                    <h3 class="box-title" style="float: left !important; font-weight: 600; margin: 0; font-size: 18px; color: #1e293b; display: inline-flex; align-items: center; gap: 8px; line-height: 30px;">
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> Delivery Challan Details
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <form action="<?php echo base_url(); ?>DeliveryChallanController/get_datewise_record" method="post" class="form-inline" style="margin: 0; display: inline-flex; align-items: center; gap: 8px;">
                                        <div class="input-group input-group-sm" style="width: 170px;">
                                            <span class="input-group-addon" style="height: 30px; padding: 5px 8px;"><i class="fa fa-calendar"></i> From</span>
                                            <input type="text" class="form-control backdate input-sm" name="from_date" id="from_date" value="<?php echo isset($from_date) ? htmlspecialchars($from_date) : ''; ?>" onkeydown="return false;" autocomplete="off" required placeholder="From Date" style="height: 30px;">
                                        </div>
                                        <div class="input-group input-group-sm" style="width: 170px;">
                                            <span class="input-group-addon" style="height: 30px; padding: 5px 8px;"><i class="fa fa-calendar"></i> To</span>
                                            <input type="text" class="form-control backdate input-sm" name="to_date" id="to_date" value="<?php echo isset($to_date) ? htmlspecialchars($to_date) : ''; ?>" onkeydown="return false;" autocomplete="off" required placeholder="To Date" style="height: 30px;">
                                        </div>
                                        <button class="btn btn-primary btn-sm" name="submit" type="submit" style="height: 30px; padding: 5px 12px; font-weight: 600; border: none; border-radius: 4px;">
                                            <i class="fa fa-filter"></i> Filter
                                        </button>
                                    </form>
                                    <a href="<?php echo base_url(); ?>DeliveryChallanController/index?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #2e7d32 !important;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <button class="btn btn-info btn-sm" id="convertToInvoiceBtn" data-toggle="modal" data-target="#bulkConvertModal" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #0288d1 !important; color: white !important;">
                                        <i class="fa fa-file-invoice"></i> Convert to Invoice
                                    </button>
                                    <a href="<?php echo base_url(); ?>DeliveryChallanController/create_delivery_challan" class="btn btn-primary btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-plus"></i> Create Delivery Challan
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>


                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo base_url(); ?>DeliveryChallanController/get_delivery_challan_data_by_status/2">Sent <span class="badge badge-light"> <?php echo $invoice_sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo base_url(); ?>DeliveryChallanController/get_delivery_challan_data_by_status/1">Draft <span class="badge badge-light"> <?php echo $invoice_draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="<?php echo base_url(); ?>DeliveryChallanController/index?str=All">All DC <span class="badge badge-light"> <?php echo $invoice_count; ?></span></a>
                                </li>
                            </ul>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                               

                              

                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAllCheckbox" class="select-all-checkbox" title="Select All"></th>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Due</th>
                                            <th>Due Status</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Customer Name</th>
                                            <th>Company Name</th>
                                            <th>Total</th>
                                            <th>Type</th>
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
                                            ?>
                                            <tr>
                                                <td>

                                                <?php echo $key->id; ?>
                                                    <input type="checkbox" class="dc-checkbox" name="selected_dcs" value="<?php echo $key->id; ?>" data-dc-number="<?php echo $key->invoice_number; ?>">
                                                </td>
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
                                                <td> <?php echo $key->company_name; ?> </td>
                                                
                                                <td> <?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->total), 0); ?> </td>
                                                <td><?php if ($key->gst_type != 'I') { ?>
                                                         SGST
                                                     <?php } else { ?>
                                                         IGST
                                                     <?php } ?>
                                                         </td>
                                                <td> <?php
                                                    echo indian_number_format(round($balance_to_pay), 0);
                                                    ?> </td>
                                                <td>
                                                    <?php if ($key->status == 4) { ?>
                                                        <?php
                                                        echo "<i class='fa fa-check'></i>";
                                                    } else {
                                                        ?>
                                                        <input type="radio" class="approved-delivery-challan1" id="approved<?php echo $i; ?>" name="approved" value="4">
                                                        <i class='fa fa-check' style="display:none;" id="app<?php echo $i; ?>"></i>
                                                    <?php } ?>
                                                </td>

<td>
    <div class="dropdown" style="position:absolute;">
        <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
            <span class="caret"></span>
        </button>

        <ul class="dropdown-menu" role="menu" aria-labelledby="menu1"
            style="top:0; right:100%; left:auto;">

            <li>
                <a class="view-modal-delivery-challan-email-send"
                   data-toggle="modal"
                   data-id="<?php echo $key->invoice_number; ?>"
                   data-target="#emailModal">
                   Send
                </a>
            </li>

            <li>
                <a class="view-modal-delivery-challan-whatsapp-send"
                   href="#"
                   data-id="<?php echo $key->invoice_number; ?>"
                   data-pdf="<?php echo base_url().'Pdf/download_delivery_challan?invoice_number='.$key->invoice_number; ?>">
                   WhatsApp
                </a>
            </li>

            <li role="presentation" class="divider"></li>

            <li>
                <a href="<?php echo base_url().'Pdf/download_delivery_challan?invoice_number='.$key->invoice_number; ?>">
                    Export As PDF
                </a>
            </li>

            <?php if ($key->status == 4 && $key->balance != 0) { ?>
            <li>
                <a class="view-modal-delivery_challan-payment"
                   data-toggle="modal"
                   data-id="<?php echo $key->id; ?>"
                   data-target="#modal">
                   Enter Payment
                </a>
            </li>
            <?php } ?>

            <li>
                <a href="<?php echo base_url().'DeliveryChallanController/edit_delivery_challan_details/'.$key->id; ?>" class="js-gear-edit">
                    Edit
                </a>
            </li>

            <li>
                <a href="<?php echo base_url().'DeliveryChallanController/show_delivery_challan/'.$key->id; ?>">
                    View
                </a>
            </li>

            <li>
                <a href="<?php echo base_url().'DeliveryChallanController/delete_delivery_challan_by_invoice_number/'.$key->invoice_number; ?>" 
                   onclick="return confirm('Are you sure you want to delete?')">
                   Delete
                </a>
            </li>

        </ul>
    </div>
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
                <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>DeliveryChallanController/edit_delivery_challan">
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
                    <center> <h4 class="modal-title"> Send Delivery Challan
<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>DeliveryChallanController/send_delivery_challan_email" enctype="multipart/form-data">
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

    <div id="deliveryChallanWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center>
                        <h4 class="modal-title">Send Delivery Challan WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="hidden" class="form-control" id="dc_whatsapp_invoice_number" value="">
                                <input type="text" class="form-control input-sm" id="dc_whatsapp_mobile" placeholder="Enter mobile number" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Message<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <textarea class="form-control input-sm" id="dc_whatsapp_message" rows="4" required=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="dc_whatsapp_send_link" target="_blank" rel="noopener" class="btn btn-success"><i class="fa fa-whatsapp" aria-hidden="true"></i> Send WhatsApp</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>


<!-- Bulk Convert Modal -->
<div class="modal fade" id="bulkConvertModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo base_url('DeliveryChallanController/bulk_convert_delivery_challan_new'); ?>" id="bulkConvertForm">
                <div class="modal-header">
                    <h4 class="modal-title">Convert Delivery Challans to Invoice</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="noDCSelectedAlert" class="alert alert-warning" style="display:none;">No delivery challans selected.</div>
                    <div id="selectedDCAlert" class="alert alert-info" style="display:none;">
                        <strong>Selected DC(s):</strong> <span id="selectedDCList"></span>
                    </div>
                    <div id="bulkConversionInputs"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnBulkConvert">Convert</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        function buildDeliveryChallanWhatsAppUrl() {
            var mobile = ($('#dc_whatsapp_mobile').val() || '').replace(/[^0-9]/g, '');
            var message = $('#dc_whatsapp_message').val() || '';

            if (!mobile || !message.trim()) {
                $('#dc_whatsapp_send_link').attr('href', '#');
                return;
            }

            $('#dc_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
        }

        // Modal data passing for payment
        $('.view-modal-delivery_challan-payment').click(function() {
            var id = $(this).data('id');
            $('#modal #id').val(id);
        });

        // Modal data passing for email
        $('.view-modal-delivery-challan-email-send').click(function() {
            var invoice_no = $(this).data('id');
            $('#emailModal #invoice_number').val(invoice_no);
            $('#emailModal #subject').val('Delivery Challan: ' + invoice_no);
        });

        // Quotation-style WhatsApp flow: open modal, fetch mobile, prefill message.
        $(document).on('click', '.view-modal-delivery-challan-whatsapp-send', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var invoiceNumber = $(this).data('id');
            var pdfUrl = $(this).data('pdf');
            var row = $(this).closest('tr');
            var status = $.trim(row.find('td:eq(1)').text());
            var dueInfo = $.trim(row.find('td:eq(2)').text());
            var dueStatus = $.trim(row.find('td:eq(3)').text());
            var challanDate = $.trim(row.find('td:eq(4)').text());

            $('.modal.in').modal('hide');
            $('#deliveryChallanWhatsappModal').modal('show');

            $('#dc_whatsapp_invoice_number').val(invoiceNumber);
            $('#dc_whatsapp_mobile').val('');
            $('#dc_whatsapp_message').val('Dear Sir/Madam,\nDelivery Challan ' + invoiceNumber + ' is shared with you.\nDate: ' + challanDate + '\nStatus: ' + status + '\n' + dueInfo + '\n' + dueStatus + '\nPDF: ' + pdfUrl + '\nPlease check and confirm.\nThanks.');

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>DeliveryChallanController/get_customer_email',
                data: { invoice_number: invoiceNumber },
                dataType: 'json',
                cache: false,
                success: function(result) {
                    if (typeof result === 'string') {
                        try {
                            result = $.parseJSON(result);
                        } catch (e) {
                            result = null;
                        }
                    }

                    var data = result;
                    if ($.isArray(result) && result.length > 0) {
                        data = result[0];
                    }

                    var rawMobile = '';
                    if (data && typeof data === 'object') {
                        rawMobile = data.mobile || data.customer_mobile || data.mobile_number || data.phone || '';
                    }

                    $('#dc_whatsapp_mobile').val(String(rawMobile).replace(/[^0-9]/g, ''));
                    buildDeliveryChallanWhatsAppUrl();
                },
                error: function() {
                    $('#dc_whatsapp_mobile').val('');
                    buildDeliveryChallanWhatsAppUrl();
                }
            });

            buildDeliveryChallanWhatsAppUrl();
        });

        $('#dc_whatsapp_mobile, #dc_whatsapp_message').on('input', function() {
            buildDeliveryChallanWhatsAppUrl();
        });

        $('#dc_whatsapp_send_link').on('click', function(event) {
            if ($(this).attr('href') === '#') {
                event.preventDefault();
                alert('Please enter valid mobile number and message.');
            }
        });

        // Checkbox functionality for bulk convert to invoice
        $(document).on('change', '#selectAllCheckbox', function() {
            var isChecked = $(this).is(':checked');
            $('.dc-checkbox').prop('checked', isChecked);
            updateConvertButtonState();
        });

        $(document).on('change', '.dc-checkbox', function() {
            var totalCheckboxes = $('.dc-checkbox').length;
            var checkedCheckboxes = $('.dc-checkbox:checked').length;
            
            if (totalCheckboxes === checkedCheckboxes) {
                $('#selectAllCheckbox').prop('checked', true);
            } else {
                $('#selectAllCheckbox').prop('checked', false);
            }
            
            updateConvertButtonState();
        });

        function updateConvertButtonState() {
            var checkedCount = $('.dc-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#convertToInvoiceBtn').prop('disabled', false);
                $('#btnBulkConvert').prop('disabled', false);
            } else {
                $('#convertToInvoiceBtn').prop('disabled', true);
                $('#btnBulkConvert').prop('disabled', true);
            }
        }

        // Handle Convert to Invoice button click
        $(document).on('click', '#convertToInvoiceBtn', function(e) {
            e.preventDefault();
            var checkedCount = $('.dc-checkbox:checked').length;
            
            if (checkedCount === 0) {
                alert('Please select at least one delivery challan to convert.');
                return;
            }

            // Build the list of selected DCs and input fields
            var selectedDCs = [];
            var inputsHTML = '';
            var counter = 1;
            
            $('.dc-checkbox:checked').each(function() {
                var dcId = $(this).val();
                var dcNumber = $(this).data('dc-number');
                selectedDCs.push({ id: dcId, number: dcNumber });
                
                inputsHTML += '<div class="form-group row" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">' +
                    '<label class="col-sm-4 control-label">Delivery Challan: ' + dcNumber + '<span style="color: red;">*</span></label>' +
                    '<div class="col-sm-7">' +
                    '<input type="text" class="form-control input-sm" name="dc_ids[]" value="' + dcId + '">' +
                    '<input type="text" class="form-control input-sm" name="dc_numbers[]" value="' + dcNumber + '">' +
                    '</div>' +
                    '</div>';
                counter++;
            });

            // Update modal
            $('#selectedDCCount').text(checkedCount);
            $('#selectedDCList').html(selectedDCs.map(function(dc) {
                return '<span class="badge badge-primary" style="margin-right: 5px;">' + dc.number + '</span>';
            }).join(''));
            $('#selectedDCAlert').show();
            $('#noDCSelectedAlert').hide();
            $('#bulkConversionInputs').html(inputsHTML);
            
            // Show modal
            $('#bulkConvertModal').modal('show');
        });

        // Handle bulk convert form submission
        $('#bulkConvertForm').on('submit', function(e) {
            e.preventDefault();
            
            var invoiceNumbers = [];
            var isDuplicate = false;
            var isEmpty = false;
            
            $('.invoice-number-input').each(function() {
                var val = $(this).val().trim();
                if (val === '') {
                    isEmpty = true;
                    $(this).css('border-color', 'red');
                    return false;
                } else {
                    $(this).css('border-color', '');
                }
                
                if (invoiceNumbers.indexOf(val) !== -1) {
                    isDuplicate = true;
                    return false;
                }
                invoiceNumbers.push(val);
            });
            
            if (isEmpty) {
                alert('Please fill in all invoice numbers.');
                return false;
            }
            
            if (isDuplicate) {
                alert('Invoice numbers must be unique. Please enter different invoice numbers for each delivery challan.');
                return false;
            }
            
            this.submit();
        });

        // Initialize button state
        updateConvertButtonState();
    });
</script>

</body>
</html>