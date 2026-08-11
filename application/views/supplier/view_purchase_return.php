<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}

$session_data_head2 = $this->session->userdata('session_data_head2');
// Guard against undefined session data
if (!isset($session_data_head2) || !is_array($session_data_head2)) {
    $session_data_head2 = [];
}
$set_cc_email = isset($session_data_head2['cc_email']) ? $session_data_head2['cc_email'] : '';

// Ensure purchase_return is an array
if (!isset($purchase_return) || !is_array($purchase_return)) {
    $purchase_return = [];
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    .dataTables_processing {
        display: none !important;
    }
</style>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center" style="display:none;"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Debit Note 
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Debit Note</a></li>
                    <li class="active">Debit Note</li>
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
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> Debit Note
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <form action="<?php echo base_url(); ?>SupplierController/get_purchase_return_datewise_record" method="post" class="form-inline" style="margin: 0; display: inline-flex; align-items: center; gap: 8px;">
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
                                    <a href="<?php echo base_url(); ?>SupplierController/view_purchase_return?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #2e7d32 !important;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <a href="<?php echo base_url(); ?>SupplierController/create_purchase_return" class="btn btn-primary btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-plus"></i> Create Debit Note
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                
                                <!-- Start Flash Message -->
                               

                              
                                <!-- End Flash Message -->

                                <table id="example7" class="  table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Supplier</th>
                                            <th>Vendor Code</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php $i= 1; foreach ($purchase_return as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->date)); ?> </td>
                                                <td><a href="<?php echo base_url() . 'SupplierController/show_purchase_return?number=' . $key->number . '&gst_type='. $key->gst_type;?>"><?php echo $key->number; ?> </a></td>
                                                <td> <?php echo $key->fullname; ?> </td>
                                                <td> <?php echo $key->s_code ?? 'N/A'; ?> </td>
                                                <td><?php if ($key->gst_type != 'I') { ?>
                                                         SGST
                                                     <?php } else { ?>
                                                         IGST
                                                     <?php } ?>
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
                                                         
                                                         
                                                <td> <?php echo number_format($key->total); ?> </td>
                                              
                                                
                                                <td>  <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                            <li><a class="view-modal-po-email-send" data-toggle="modal" data-href="#"  data-id="<?php echo $key->number; ?>" data-target="#modal"> Send Email </a></li>
                                                            <li><a class="view-modal-purchase-return-whatsapp-send" data-id="<?php echo $key->number; ?>" data-pdf="<?php echo base_url() . 'Pdf/download_purchase_return/' . $key->number; ?>"> WhatsApp </a></li>
                                                            <li role="presentation" class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'Pdf/download_purchase_return/' . $key->number ?>" class="js-gear-download">Export As PDF</a></li>
                                                            <li><a href="<?php echo base_url() . 'SupplierController/show_purchase_return?number=' . $key->number . '&gst_type=' . $key->gst_type; ?>" class="js-gear-view">View</a></li>
                                                            <li><a href="<?php echo base_url() . 'SupplierController/edit_purchase_return_details?number=' . $key->number . '&gst_type='. $key->gst_type; ?>" class="js-gear-edit">Edit</a></li>
                                                            <li><a href="<?php echo base_url() . 'SupplierController/delete_purchase_return_by_po_return_number/' . $key->number; ?>" onClick="return confirm('Are you sure you want to delete?')" class="js-gear-delete">Delete</a></li>
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
    
    <!-- ./WhatsApp modal -->
    <div id="purchaseReturnWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center><h4 class="modal-title">Send Debit Note WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="pr_whatsapp_mobile">Mobile Number<span style="color: red;">*</span></label>
                        <input type="text" class="form-control input-sm" id="pr_whatsapp_mobile" placeholder="Enter mobile number">
                    </div>
                    <div class="form-group">
                        <label for="pr_whatsapp_message">Message<span style="color: red;">*</span></label>
                        <textarea class="form-control input-sm" id="pr_whatsapp_message" rows="5" placeholder="Enter your message"></textarea>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="pr_whatsapp_send_btn"><i class="fa fa-whatsapp"></i> Send WhatsApp</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ./Email modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center><h4 class="modal-title">Send Debit Note<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/send_purchase_return_email" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body ">
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="number" id="number" value="" required="">
                                    <input type="email" class="form-control input-sm" name="to_email" id="to_email" required="">
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
                        <button type="submit" id="btnSave" class="btn btn-success"><i class="fa fa-paper-plane" aria-hidden="true"></i>
Send</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Hide loading spinner immediately when page is ready
        $(function() {
            $('#loader').hide();
        });

        // Pre-initialize Debit Note table with custom dom before footer generic init
        $(function() {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#example7')) {
                $('#example7').DataTable({
                    "pageLength": 25,
                    "dom": "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                           "<'row'<'col-sm-12'tr>>" +
                           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "language": {
                        "search": "Search Purchase Returns:",
                        "lengthMenu": "_MENU_ entries per page"
                    }
                });
            }
        });

        // Build WhatsApp URL when mobile or message changes
        function buildPurchaseReturnWhatsAppUrl() {
            var mobile = $('#pr_whatsapp_mobile').val().replace(/[^0-9]/g, '');
            var message = $('#pr_whatsapp_message').val();
            if (mobile && message.trim()) {
                $('#pr_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
                $('#pr_whatsapp_url_info').show();
            } else {
                $('#pr_whatsapp_url_info').hide();
            }
        }

        $(document).ready(function() {
            // WhatsApp Send Button Click
            $(document).on('click', '#pr_whatsapp_send_btn', function() {
                var mobile = $('#pr_whatsapp_mobile').val().replace(/[^0-9]/g, '');
                var message = $('#pr_whatsapp_message').val();
                if (!mobile || !message.trim()) {
                    alert('Please enter both mobile number and message');
                    return;
                }
                window.open('https://wa.me/' + mobile + '?text=' + encodeURIComponent(message), '_blank', 'noopener');
            });

            // Real-time URL builder
            $(document).on('input', '#pr_whatsapp_mobile, #pr_whatsapp_message', function() {
                buildPurchaseReturnWhatsAppUrl();
            });

            // WhatsApp Menu Click
            $(document).on('click', '.view-modal-purchase-return-whatsapp-send', function(event) {
                event.preventDefault();
                var returnNumber = $(this).data('id');
                var pdfUrl = $(this).data('pdf');
                
                $('#purchaseReturnWhatsappModal').modal('show');
                
                // Reset form
                $('#pr_whatsapp_mobile').val('');
                $('#pr_whatsapp_message').val('');
                $('#pr_whatsapp_url_info').hide();
                
                // Fetch supplier mobile
                $.ajax({
                    url: '<?php echo base_url(); ?>SupplierController/get_purchase_return_supplier_email',
                    type: 'POST',
                    data: { number: returnNumber },
                    dataType: 'json',
                    success: function(result) {
                        if (typeof result === 'string') {
                            try { result = $.parseJSON(result); } catch (e) { result = null; }
                        }
                        var rawMobile = result.mobile || result.supplier_mobile || result.mobile_number || result.phone || '';
                        var mobile = String(rawMobile).replace(/[^0-9]/g, '');
                        $('#pr_whatsapp_mobile').val(mobile);
                        
                        var message = 'Dear Sir/Madam,\n\nReturnable Challan ' + returnNumber + ' is shared with you.\n\nPlease check and confirm.\n\nPDF: ' + pdfUrl + '\n\nThanks.';
                        $('#pr_whatsapp_message').val(message);
                        buildPurchaseReturnWhatsAppUrl();
                    },
                    error: function() {
                        $('#pr_whatsapp_mobile').val('');
                        var message = 'Dear Sir/Madam,\n\nReturnable Challan ' + returnNumber + ' is shared with you.\n\nPlease check and confirm.\n\nPDF: ' + pdfUrl + '\n\nThanks.';
                        $('#pr_whatsapp_message').val(message);
                    }
                });
            });

            $('.view-modal-po-email-send').click(function() {
                var number = $(this).data('id');
                $('#number').val(number);
                $('#subject').val('Debit Note ' + number);

                $.ajax({
                    url: '<?php echo base_url(); ?>SupplierController/get_purchase_return_supplier_email',
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
        });
    </script>





