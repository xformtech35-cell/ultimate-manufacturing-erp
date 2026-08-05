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
                    Credit Note
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Credit Note</a></li>
                    <li class="active">Credit Note</li>
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
                                        <h3 class="box-title">Credit Note</h3>
                                    </div>
                                    <div class="col-md-4">
                                        <form
                                            action="<?php echo base_url(); ?>SalesReturnController/get_sales_return_purmonthyearwise_record"
                                            method="post">
                                            <div class="form-group row">
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control onlymonth input-sm"
                                                        name="month_year" id="month_year" onkeydown="return false;"
                                                        autocomplete="off">
                                                </div>
                                                <button class="btn btn-primary" name="submit" value="">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <a
                                            href="<?php echo base_url(); ?>SalesReturnController/view_sales_return?str=All"><button
                                                class="btn btn-success btn-sm"> Show All Credit Note</button></a>
                                        <a href="<?php echo base_url(); ?>SalesReturnController/create_sales_return"><button
                                                class="btn btn-success btn-sm"><i
                                                    class="glyphicon glyphicon-plus"></i>Create Credit Note</button></a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">



                                <table id="example7" class=" table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Customer Name</th>
                                            <th>Company Name</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php $i= 1; foreach ($sales_return as $key) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $i; ?>
                                            </td>
                                            <td> <?php echo date('d-m-Y', strtotime($key->date)); ?> </td>
                                            <td><a
                                                    href="<?php echo base_url() . 'SalesReturnController/show_sales_return?number=' . $key->number . '&gst_type='. $key->gst_type;?>"><?php echo $key->number; ?>
                                                </a></td>
                                            <td> <?php echo $key->fullname; ?> </td>
                                            <td> <?php echo $key->company_name; ?> </td>
                                            <td><?php if ($key->gst_type != 'I') { ?>
                                                CGST/ SGST
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


                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" id="menu1"
                                                        type="button" data-toggle="dropdown">
                                                        <span class="caret"></span></button>
                                                    <ul class="dropdown-menu pull-right" role="menu"
                                                        aria-labelledby="menu1">
                                                        <li><a class="view-modal-po-email-send" href="#"
                                                                data-id="<?php echo $key->number; ?>"> Send </a></li>
                                                        <li><a class="view-modal-sales-return-whatsapp-send" href="#"
                                                                data-id="<?php echo $key->number; ?>"
                                                                data-pdf="<?php echo base_url() . 'Pdf/download_sales_return/' . $key->number; ?>">
                                                                WhatsApp </a></li>
                                                        <li role="presentation" class="divider"></li>
                                                        <li><a href="<?php echo base_url() . 'Pdf/download_sales_return/' . $key->number ?>"
                                                                class="js-gear-download">Export As PDF</a></li>
                                                        <li><a href="<?php echo base_url() . 'SalesReturnController/show_sales_return?number=' . $key->number . '&gst_type=' . $key->gst_type; ?>"
                                                                class="js-gear-view">View</a></li>
                                                        <li><a href="<?php echo base_url() . 'SalesReturnController/edit_sales_return_details?number=' . $key->number . '&gst_type='. $key->gst_type; ?>"
                                                                class="js-gear-edit">Edit</a></li>
                                                        <li><a href="<?php echo base_url() . 'SalesReturnController/delete_sales_return_by_po_return_number/' . $key->number; ?>"
                                                                onClick="return confirm('Are you sure you want to delete?')"
                                                                class="js-gear-delete">Delete</a></li>
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

    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Send Credit Note<button type="button" class="close"
                                data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post"
                    action="<?php echo base_url(); ?>SalesReturnController/send_sales_return_email"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="to_email" class="col-sm-4 control-label">To<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="number" id="number" value=""
                                        required="">
                                    <input type="email" class="form-control input-sm" name="to_email" id="to_email"
                                        required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="subject" class="col-sm-4 control-label">Subject<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="subject" id="subject" rows="2"
                                        required=""></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="message" class="col-sm-4 control-label">Message</label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="message" id="message"
                                        rows="3"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="copy_email" class="col-sm-4 control-label">Send a copy to</label>
                                <div class="col-sm-7">
                                    <input type="checkbox" name="copy_email" id="copy_email">
                                    <?php echo $set_cc_email; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success"><i class="fa fa-paper-plane"
                                aria-hidden="true"></i> Send</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="salesReturnWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center>
                        <h4 class="modal-title">Send Credit Note WhatsApp<button type="button" class="close"
                                data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sr_whatsapp_mobile">Mobile Number<span style="color: red;">*</span></label>
                        <input type="text" class="form-control input-sm" id="sr_whatsapp_mobile"
                            placeholder="Enter mobile number">
                    </div>
                    <div class="form-group">
                        <label for="sr_whatsapp_message">Message<span style="color: red;">*</span></label>
                        <textarea class="form-control input-sm" id="sr_whatsapp_message" rows="5"
                            placeholder="Enter your message"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="sr_whatsapp_send_btn">
                        <i class="fab fa-whatsapp"></i> Send WhatsApp
                    </button> <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function buildSalesReturnWhatsAppUrl() {
        var mobile = ($('#sr_whatsapp_mobile').val() || '').replace(/[^0-9]/g, '');
        var message = $('#sr_whatsapp_message').val() || '';

        if (mobile && message.trim()) {
            $('#sr_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(
            message));
            $('#sr_whatsapp_url_info').show();
        } else {
            $('#sr_whatsapp_send_link').attr('href', '#');
            $('#sr_whatsapp_url_info').hide();
        }
    }

    $(document).ready(function() {
        $(document).on('click', '.view-modal-po-email-send', function(event) {
            event.preventDefault();
            var returnNumber = $(this).data('id');
            var row = $(this).closest('tr');
            var returnDate = $.trim(row.find('td:eq(1)').text());
            var status = $.trim(row.find('td:eq(6)').text());

            $('#number').val(returnNumber);
            $('#to_email').val('');
            $('#subject').val('Credit Note ' + returnNumber +
                ' from <?php echo addslashes($session_data_head2['company_name']); ?>');
            $('#message').val('Dear Sir/Madam,\n\nPlease find Credit Note ' + returnNumber +
                '.\nDate: ' + returnDate + '\nStatus: ' + status + '\n\nThanks.');

            $.ajax({
                url: '<?php echo base_url(); ?>SalesReturnController/get_sales_return_customer_contact',
                type: 'POST',
                data: {
                    number: returnNumber
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

        $(document).on('click', '.view-modal-sales-return-whatsapp-send', function(event) {
            event.preventDefault();
            var returnNumber = $(this).data('id');
            var pdfUrl = $(this).data('pdf');
            var row = $(this).closest('tr');
            var returnDate = $.trim(row.find('td:eq(1)').text());
            var status = $.trim(row.find('td:eq(6)').text());

            $('#salesReturnWhatsappModal').modal('show');
            $('#sr_whatsapp_mobile').val('');
            $('#sr_whatsapp_url_info').hide();

            var message = 'Dear Sir/Madam,\n\nSales Return ' + returnNumber +
                ' is shared with you.\nDate: ' + returnDate + '\nStatus: ' + status + '\n\nPDF: ' +
                pdfUrl + '\n\nPlease check and confirm.\n\nThanks.';
            $('#sr_whatsapp_message').val(message);

            $.ajax({
                url: '<?php echo base_url(); ?>SalesReturnController/get_sales_return_customer_contact',
                type: 'POST',
                data: {
                    number: returnNumber
                },
                dataType: 'json',
                success: function(result) {
                    if (typeof result === 'string') {
                        try {
                            result = $.parseJSON(result);
                        } catch (e) {
                            result = null;
                        }
                    }
                    var rawMobile = result && result.mobile ? result.mobile : '';
                    $('#sr_whatsapp_mobile').val(String(rawMobile).replace(/[^0-9]/g, ''));
                    buildSalesReturnWhatsAppUrl();
                },
                error: function() {
                    $('#sr_whatsapp_mobile').val('');
                    buildSalesReturnWhatsAppUrl();
                }
            });
        });

        $(document).on('input', '#sr_whatsapp_mobile, #sr_whatsapp_message', function() {
            buildSalesReturnWhatsAppUrl();
        });

        $(document).on('click', '#sr_whatsapp_send_btn', function() {
            var mobile = ($('#sr_whatsapp_mobile').val() || '').replace(/[^0-9]/g, '');
            var message = $('#sr_whatsapp_message').val() || '';

            if (!mobile || !message.trim()) {
                alert('Please enter both mobile number and message');
                return;
            }

            window.open('https://wa.me/' + mobile + '?text=' + encodeURIComponent(message), '_blank',
                'noopener');
        });
    });
    </script>