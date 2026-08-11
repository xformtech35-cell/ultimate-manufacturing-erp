<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}

$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = isset($session_data_head2['cc_email']) ? $session_data_head2['cc_email'] : '';
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
                    Purchase Voucher   
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Purchase Voucher</a></li>
                    <li class="active">Purchase Voucher</li>
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
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> Purchase Vouchers List
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <form action="<?php echo base_url(); ?>SupplierController/get_purchase_bill_datewise_record" method="post" class="form-inline" style="margin: 0; display: inline-flex; align-items: center; gap: 8px;">
                                        <div class="input-group input-group-sm" style="width: 170px;">
                                            <span class="input-group-addon" style="height: 30px; padding: 5px 8px;"><i class="fa fa-calendar"></i> From</span>
                                            <input type="text" class="form-control backdate input-sm" name="from_date" id="from_date" value="<?php echo isset($from_date) ? htmlspecialchars($from_date) : ''; ?>" onkeydown="return false;" autocomplete="off" required placeholder="From Date" style="height: 30px;">
                                        </div>
                                        <div class="input-group input-group-sm" style="width: 170px;">
                                            <span class="input-group-addon" style="height: 30px; padding: 5px 8px;"><i class="fa fa-calendar"></i> To</span>
                                            <input type="text" class="form-control backdate input-sm" name="to_date" id="to_date" value="<?php echo isset($to_date) ? htmlspecialchars($to_date) : ''; ?>" onkeydown="return false;" autocomplete="off" required placeholder="To Date" style="height: 30px;">
                                        </div>
                                        <button class="btn btn-primary btn-sm" name="submit" value="" type="submit" style="height: 30px; padding: 5px 12px; font-weight: 600; border: none; border-radius: 4px;"><i class="fa fa-filter"></i> Filter</button>
                                    </form>
                                    <a href="<?php echo base_url(); ?>SupplierController/view_purchase_bill?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #2e7d32 !important;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <a href="<?php echo base_url(); ?>SupplierController/create_purchase_bill" class="btn btn-primary btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-plus"></i> Create Purchase Voucher
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                
                                <!-- Start Flash Message -->
                               

                                
                                <!-- End Flash Message -->
 
                                  <table id="example3" class="table table-bordered table-striped" style="margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Due Date</th>
                                            <th>Number</th>
                                            <th>Company Name</th>
                                            <th>Vendor Code</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Balance</th>
                                            <th>Expenditure Type</th>
                                            <th>Approve</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         <?php $unpaid = 0; ?>
                                        <?php $i= 1; foreach ($purchase_bill as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td>
                                                 <?php if ($key->status == 1) { ?>
                                                    Draft
                                                <?php } ?>
                                                <?php if ($key->status == 2) { ?>
                                                    Sent
                                                <?php } ?>
                                                <?php if ($key->status == 3) { ?>
                                                    Viewed
                                                <?php } ?>
                                                <?php if ($key->status == 4) { ?>
                                                    Approved
                                                <?php } ?>
                                                <?php if ($key->status == 5) { ?>
                                                    Rejected
                                                <?php } ?>
                                                <?php if ($key->status == 6) { ?>
                                                    Canceled
                                                <?php } if ($key->status == 0) { ?>
                                                   <?php } ?> 
                                                </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->date)); ?> </td>
                                                
                                                <td> <?php
                                                    $balance_to_pay = $key->balance;
                                                    $today = date('Y-m-d');
                                                    $date = new DateTime($key->payment_due_date);
                                                    $currentdate = new DateTime($today);
                                                    if (($date < $currentdate) && ($balance_to_pay != 0.00)) {
                                                        ?>
                                                        <a href="<?php echo base_url() . 'SupplierController/show_purchase_bill/' . $key->number; ?>"><button type="submit" class="btn btn-danger">Over Due <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?></button></a>
                                                        <input type="hidden" class="form-control setunpaid" name="setunpaid" id="setunpaid" value="<?php $unpaid; ?>">
                                                        <?php
                                                        $unpaid++;
                                                    } else if (($date >= $currentdate) && ($balance_to_pay != 0.00)) {
                                                        ?>

                                                        <a href="<?php echo base_url() . 'SupplierController/show_purchase_bill/' . $key->number; ?>"><button type="submit" class="btn btn-info">Due on <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?></button></a>

                                                        <input type="hidden" class="form-control setunpaid" name="setunpaid" id="setunpaid" value="<?php $unpaid; ?>">
                                                        <?php
                                                        $unpaid++;
                                                    } else if ($balance_to_pay == 0.00) {
                                                        ?>
                                                        <a href="<?php echo base_url() . 'SupplierController/show_purchase_bill/' . $key->number; ?>"><button type="submit" class="btn btn-success">Paid <?php } ?></button></a>


                                                </td>
                                                <!-- <td hidden=""> <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?> </td> -->
                                                <td><a href="<?php echo base_url() . 'SupplierController/show_purchase_bill/' . $key->number ?>"><?php echo $key->number; ?> </a></td>
                                                <td> <?php echo $key->company_name; ?> </td>
                                                <td> <?php echo $key->s_code ?? 'N/A'; ?> </td>
                                                <td><?php if ($key->gst_type != 'I') { ?>
                                                         CGST/ SGST
                                                     <?php } else { ?>
                                                         IGST
                                                     <?php } ?>
                                                         </td>
                                                         
                                                <td> <?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->total), 0); ?> </td>
                                               <td> <?php
                                                    echo indian_number_format(round($balance_to_pay), 0);
                                                    ?> 
                                                    <input type="hidden" class="hide" id="get_purchase_no<?php echo $i; ?>" name="get_purchase_no" value="<?php echo $key->number; ?>">
                                                </td>
                                                <td> 
                                                   <?php echo $key->expenditure_type; ?>
                                                
                                                </td>
                                               
                                                    <td>
                                                    <?php if ($key->status == 4) { ?>
                                                        <?php
                                                        echo "<html><i class='fa fa-check'></i></html>";
                                                    } else {
                                                        ?>
                                                        <input type="radio" class="approved-purchase-bill" id="approved<?php echo $i; ?>" name="approved" value="4">
                                                        <i class='fa fa-check' style="display:none;" id="app<?php echo $i; ?>"></i>
                                                    <?php } ?>
                                                </td>
                                                <td>  <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                            <li><a class="view-modal-po-email-send" data-toggle="modal" data-href="#"  data-id="<?php echo $key->number; ?>" data-target="#modal"> Send </a></li> 
                                                            <li><a class="view-modal-po-whatsapp-send" href="#" data-id="<?php echo $key->number; ?>" data-pdf="<?php echo base_url() . 'Pdf/download_purchase_bill/' . $key->number; ?>"> WhatsApp </a></li>
                                                            <li role="presentation" class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'Pdf/download_purchase_bill/' . $key->number; ?>" class="js-gear-download">Export As PDF</a></li>
                                                            <li><a href="<?php echo base_url() . 'SupplierController/show_purchase_bill/' . $key->number; ?>" class="js-gear-view">View</a></li>
                                                            <?php if ($key->status == 4 && $key->balance != 0) { ?>
                                                            <li><a class="modal-purchase-bill-payment" data-toggle="modal" data-href="#"  data-id="<?php echo $key->id; ?>" data-target="#modal1"> Enter Payment </a>  </li>                                                          </li>
                                                        <?php } ?>

                                                        <li style="display:none" id="payment_enable<?php echo $i; ?>"><a class="modal-purchase-bill-payment"  data-toggle="modal" data-href="#"  data-id="<?php echo $key->id; ?>" data-target="#modal1"> Enter Payment </a>  </li>      
                                                        <?php if ($key->balance != 0) { ?>
                                                        <li><a class="modal-purchase-bill-mark-paid" data-toggle="modal" data-href="#" data-id="<?php echo $key->id; ?>" data-target="#modal1"><i class="fa fa-check-circle"></i> Mark as Paid</a></li>
                                                        <li><a href="<?php echo base_url() . 'SupplierController/payment_out'; ?>"><i class="fa fa-money"></i> Payment Out</a></li>
                                                        <?php } ?>
                                                            <li><a href="<?php echo base_url() . 'SupplierController/edit_purchase_bill_details/' . $key->number; ?>" class="js-gear-edit">Edit</a></li>
                                                            
                                                            <li><a href="<?php echo base_url() . 'SupplierController/delete_purchase_bill_by_po_bill_number/' . $key->number;  ?>" onClick="return confirm('Are you sure you want to delete?')" class="js-gear-delete">Delete</a></li>
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
        <!-- /.content-wrapper -->
       
        <!-- Control Sidebar -->
        
            <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    
    <!-- ./Email modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center> <h4 class="modal-title">Send Purchase Voucher<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/send_purchase_bill_email" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="supplier_id" id="supplier_id" value="" required="">
                                    <input type="hidden" class="form-control" name="number" id="number" value="" required="">
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
                                    <input type="checkbox" name="copy_email" id="copy_email">  <?php echo $set_cc_email; ?> 
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
    
    <div id="modal1" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center> <h4 class="modal-title">Enter Payment<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/edit_purchase_bill_payment">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Balance<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control"   name="id" id="id" value="" >
                                    <input type="hidden" class="form-control"   name="total" id="total" value="" >
                                    <input type="hidden" class="form-control"   name="number" id="number_fk" value="" > 
                                    <input type="hidden" class="form-control"   name="supplier_id_fk" id="supplier_id_fk"> 
                                    <input type="text" readonly="" class="form-control input-sm"  name="balance" id="balance" >
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
                                    <input type="text" class="form-control backdate"   name="date" id="date" value="" required="" onkeydown="return false;" autocomplete="off">
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

    <div id="purchaseBillWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center>
                        <h4 class="modal-title">Send Purchase Voucher WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="hidden" class="form-control" id="pb_whatsapp_number" value="">
                                <input type="text" class="form-control input-sm" id="pb_whatsapp_mobile" placeholder="Enter mobile number" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Message<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <textarea class="form-control input-sm" id="pb_whatsapp_message" rows="4" required=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="pb_whatsapp_send_btn" class="btn btn-success"><i class="fa fa-whatsapp" aria-hidden="true"></i> Send WhatsApp</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>





<script>
    $(document).ready(function() {
        function buildPurchaseBillWhatsAppUrl() {
            var mobile = ($('#pb_whatsapp_mobile').val() || '').replace(/[^0-9]/g, '');
            var message = $('#pb_whatsapp_message').val() || '';

            if (!mobile || !message.trim()) {
                return '#';
            }

            return 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message);
        }

        $('.view-modal-po-email-send').click(function() {
            var number = $(this).data('id');
            $('#number').val(number);
            $('#subject').val('Purchase Voucher ' + number);

            $.ajax({
                url: '<?php echo base_url(); ?>SupplierController/get_purchase_bill_supplier_email',
                type: 'POST',
                data: {
                    number: number
                },
                dataType: 'json',
                success: function(response) {
                    $('#to_email').val(response && response.email ? response.email : '');
                    $('#modal').modal('show');
                },
                error: function() {
                    $('#to_email').val('');
                    $('#modal').modal('show');
                }
            });
        });

        $(document).on('click', '.view-modal-po-whatsapp-send', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var number = $(this).data('id');
            var pdfUrl = $(this).data('pdf');
            var row = $(this).closest('tr');
            var status = $.trim(row.find('td:eq(1)').text());
            var billDate = $.trim(row.find('td:eq(2)').text());
            var dueInfo = $.trim(row.find('td:eq(3)').text());

            $('#purchaseBillWhatsappModal').attr('data-current-number', number);
            $('#pb_whatsapp_number').val(number);
            $('#pb_whatsapp_mobile').val('');
            $('#pb_whatsapp_message').val('Dear Sir/Madam,\n\nPurchase Voucher ' + number + ' is shared with you.\nDate: ' + billDate + '\nStatus: ' + status + '\n' + dueInfo + '\n\nPDF: ' + pdfUrl + '\n\nPlease check and confirm.\n\nThanks.');
            $('#purchaseBillWhatsappModal').modal('show');

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>SupplierController/get_purchase_bill_supplier_email',
                data: { number: number },
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
                        rawMobile = data.mobile || data.supplier_mobile || data.mobile_number || data.phone || '';
                    }

                    if ($('#purchaseBillWhatsappModal').attr('data-current-number') !== String(number)) {
                        return;
                    }

                    $('#pb_whatsapp_mobile').val(String(rawMobile).replace(/[^0-9]/g, ''));
                },
                error: function() {
                    if ($('#purchaseBillWhatsappModal').attr('data-current-number') !== String(number)) {
                        return;
                    }

                    $('#pb_whatsapp_mobile').val('');
                }
            });
        });

        $('#pb_whatsapp_mobile, #pb_whatsapp_message').on('input', function() {
            buildPurchaseBillWhatsAppUrl();
        });

        $('#pb_whatsapp_send_btn').on('click', function() {
            var whatsappUrl = buildPurchaseBillWhatsAppUrl();

            if (whatsappUrl === '#') {
                alert('Please enter valid mobile number and message.');
                return;
            }

            window.open(whatsappUrl, '_blank', 'noopener');
        });
    });
</script>




