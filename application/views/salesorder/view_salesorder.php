<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

//$session_data_head2 = $this->session->userdata('session_data_head2');
//$set_cc_email = $session_data_head2['cc_email'];
$set_cc_email = "pravin@xform.in";

defined('BASEPATH') or exit('No direct script access allowed');
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
                    Sales Order
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Sales Order</a></li>
                    <li class="active">Sales Order details</li>
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
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> Sales Orders List
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <form action="<?php echo base_url(); ?>SalesOrderController/get_monthyearwise_record" method="post" class="form-inline" style="margin: 0; display: inline-block;">
                                        <div class="input-group input-group-sm" style="width: 220px; display: table;">
                                            <span class="input-group-addon" style="height: 30px; padding: 5px 10px; border-radius: 4px 0 0 4px !important;"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control onlymonth input-sm" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off" required="" placeholder="Select Month/Year" style="height: 30px; border-radius: 0 !important;">
                                            <span class="input-group-btn" style="width: 1%;">
                                                <button class="btn btn-primary btn-sm btn-flat" name="submit" value="" type="submit" style="height: 30px; padding: 5px 15px; border-radius: 0 4px 4px 0 !important; font-weight: 600; border: none;">Filter</button>
                                            </span>
                                        </div>
                                    </form>
                                    <a href="<?php echo base_url(); ?>SalesOrderController/index?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #2e7d32 !important;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <a href="<?php echo base_url('SalesOrderController/export_all_salesorders'); ?>" class="btn btn-warning btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #f57c00 !important; color: white !important;">
                                        <i class="fa fa-file-excel-o"></i> Export SO
                                    </a>
                                    <a href="<?php echo base_url(); ?>SalesOrderController/create_gst_salesorder" class="btn btn-primary btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-plus"></i> Create Sales Order
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>



                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_salesorder_data_by_status' && $this->uri->segment(3) == '2') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>SalesOrderController/get_salesorder_data_by_status/2">Sent <span class="badge badge-light"> <?php echo $salesorder_sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_salesorder_data_by_status' && $this->uri->segment(3) == '1') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>SalesOrderController/get_salesorder_data_by_status/1">Draft <span class="badge badge-light"> <?php echo $salesorder_draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'index' || $this->uri->segment(2) == '' || $this->uri->segment(2) == 'get_monthyearwise_record') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>SalesOrderController/index?str=All">All Sales Orders <span class="badge badge-light"> <?php echo $salesorder_count; ?></span></a>
                                </li>
                            </ul>

                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="example3" class="table table-bordered table-striped" style="margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Customer Name</th>
                                            <th>Company Name</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th>Approved By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php $i = 1;

                                        foreach ($salesorders as $key) { ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                

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
                                                <?php }
                                                if ($key->status == 0) { ?>
                                                    <td></td><?php } ?>
                                                <td> <?php
                                                        // avoid passing null/empty to strtotime, which triggers a deprecation warning
                                                        if (!empty($key->date) && $key->date !== '0000-00-00') {
                                                            echo date('d-m-Y', strtotime($key->date));
                                                        } else {
                                                            echo '';
                                                        }
                                                        ?> </td>
                                                <td><a href="<?php echo base_url() . 'SalesOrderController/show_salesorder/' . $key->id ?>"><?php echo $key->number; ?> </a></td>
                                                <td> <?php echo $key->fullname; ?> </td>
                                                <td> <?php echo $key->customer_name; ?> </td>

                                                <td><?php if ($key->gst_type != 'I') { ?>
                                                        CGST/ SGST
                                                    <?php } else { ?>
                                                        IGST
                                                    <?php } ?>
                                                </td>
                                                <td> <?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->total), 0); ?> </td>
                                                <td><span class="label label-info" style="font-size: 11px; font-weight: normal;"><?php echo !empty($key->created_by_name) ? htmlspecialchars($key->created_by_name) : 'Admin'; ?></span></td>
                                                <td><span class="label label-success" style="font-size: 11px; font-weight: normal;"><?php echo !empty($key->approved_by_name) ? htmlspecialchars($key->approved_by_name) : ($key->status == 4 ? 'Admin' : '-'); ?></span></td>

                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                            <li><a class="view-modal-email-send" href="#" data-id="<?php echo $key->id; ?>" data-number="<?php echo $key->number; ?>"> Send  </a></li>
                                                            <li><a class="view-modal-so-whatsapp-send" href="#" data-number="<?php echo $key->number; ?>" data-pdf="<?php echo base_url() . 'Pdf/print_igst_salesorder/' . $key->id; ?>">WhatsApp </a></li>
                                                            <li role="presentation" class="divider"></li>

                                                            <?php if ($key->gst_type == 'S') { ?>
                                                                <li><a href="<?php echo base_url() . 'Pdf/print_igst_salesorder/' . $key->id ?>" name="btn_submit" id="download1" class="js-gear-download">Export As PDF</a></li>
                                                            <?php } else { ?>
                                                                <li><a href="<?php echo base_url() . 'Pdf/print_igst_salesorder/' . $key->id ?>" name="btn_submit" id="download1" class="js-gear-download">Export As PDF</a></li>
                                                            <?php } ?>
                                                            <li><a href="<?php echo base_url() . 'SalesOrderController/export_salesorder_excel/' . $key->id; ?>">Export As Excel</a></li>


                                                            <li><a href="<?php echo base_url() . 'SalesOrderController/show_salesorder/' . $key->id; ?>" class="js-gear-view">View</a></li>
                                                            <li><a href="<?php echo base_url() . 'SalesOrderController/edit_salesorder_details/' . $key->id ?>" class="js-gear-edit">Edit</a></li>
                                                            <!--                                                            <li><a href="<?php echo base_url() . 'SalesOrderController/print_salesorder/' . $key->id ?>">Print</a></li>-->
                                                            <li><a href="<?php echo base_url() . 'SalesOrderController/delete_salesorder_by_quote_number/' . $key->number; ?>" class="js-gear-delete">Delete</a></li>
                                                        </ul>
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php $i++;
                                        } ?>

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

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- WhatsApp Modal -->
    <div id="soWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center><h4 class="modal-title">Send WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="so_whatsapp_mobile">Mobile Number<span style="color:red;">*</span></label>
                        <input type="text" class="form-control input-sm" id="so_whatsapp_mobile" placeholder="Enter mobile number">
                    </div>
                    <div class="form-group">
                        <label for="so_whatsapp_message">Message<span style="color:red;">*</span></label>
                        <textarea class="form-control input-sm" id="so_whatsapp_message" rows="5"></textarea>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="so_whatsapp_send_btn"><i class="fa fa-whatsapp" aria-hidden="true"></i> Send WhatsApp</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ./Email modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Send Sales Order<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SalesOrderController/send_salesorder_email" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="customer_id" id="customer_id" value="" required="">
                                    <input type="hidden" class="form-control" name="number" id="number" value="" required="">
                                    <input type="text" class="form-control input-sm" name="to_email" id="to_email" required="">
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
                        <button type="submit" id="btnSave" class="btn btn-success"><i class="fa fa-paper-plane" aria-hidden="true"></i>
                            Send</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function buildSoWhatsAppUrl() {
            var mobile = $('#so_whatsapp_mobile').val().replace(/[^0-9]/g, '');
            var message = $('#so_whatsapp_message').val();
            if (mobile && message.trim()) {
                $('#so_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
                $('#so_whatsapp_url_info').show();
            } else {
                $('#so_whatsapp_url_info').hide();
            }
        }

        // Hide loading spinner immediately when page is ready
        $(function() { $('#loader').hide(); });

        // Pre-initialize SO table with custom dom before footer generic init
        $(function() {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#example3')) {
                $('#example3').DataTable({
                    "pageLength": 25,
                    "dom": "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                           "<'row'<'col-sm-12 table-responsive-container'tr>>" +
                           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "language": {
                        "search": "Search Sales Orders:",
                        "lengthMenu": "_MENU_ entries per page"
                    }
                });
            }
        });

        $(document).ready(function() {

            // Email modal handler

            $(document).on('click', '.view-modal-email-send', function(e) {
                e.preventDefault();
                var soNumber = $(this).data('number');
                var soId = $(this).data('id');
                $('#number').val(soNumber);
                $('#customer_id').val('');
                $('#subject').val('Sales Order ' + soNumber);
                $('#modal').attr('data-current-number', soNumber);

                $.ajax({
                    url: '<?php echo base_url(); ?>SalesOrderController/get_customer_email',
                    type: 'POST',
                    data: { number: soNumber },
                    dataType: 'json',
                    success: function(response) {
                        if ($('#modal').attr('data-current-number') !== String(soNumber)) return;
                        $('#to_email').val(response && response.email ? response.email : '');
                        $('#modal').modal('show');
                    },
                    error: function() {
                        if ($('#modal').attr('data-current-number') !== String(soNumber)) return;
                        $('#to_email').val('');
                        $('#modal').modal('show');
                    }
                });
            });

            // WhatsApp modal handler
            $(document).on('click', '.view-modal-so-whatsapp-send', function(e) {
                e.preventDefault();
                var soNumber = $(this).data('number');
                var pdfUrl = $(this).data('pdf');
                $('#soWhatsappModal').attr('data-current-number', soNumber);
                $('#so_whatsapp_mobile').val('');
                $('#so_whatsapp_message').val('');
                $('#so_whatsapp_url_info').hide();
                $('#soWhatsappModal').modal('show');

                $.ajax({
                    url: '<?php echo base_url(); ?>SalesOrderController/get_customer_email',
                    type: 'POST',
                    data: { number: soNumber },
                    dataType: 'json',
                    success: function(result) {
                        if ($('#soWhatsappModal').attr('data-current-number') !== String(soNumber)) return;
                        if (typeof result === 'string') { try { result = $.parseJSON(result); } catch(e) { result = null; } }
                        result = result || {};
                        var rawMobile = result.mobile || result.customer_mobile || result.mobile_number || result.phone || '';
                        var mobile = String(rawMobile).replace(/[^0-9]/g, '');
                        var message = 'Dear Sir/Madam,\n\nSales Order ' + soNumber + ' is shared with you.\n\nPlease check and confirm.\n\nPDF: ' + pdfUrl + '\n\nThanks.';

                        if (mobile) {
                            // Mobile fetched — open WhatsApp directly, close modal
                            $('#soWhatsappModal').modal('hide');
                            window.open('https://wa.me/' + mobile + '?text=' + encodeURIComponent(message), '_blank', 'noopener');
                        } else {
                            // Mobile missing — keep modal open for manual entry
                            $('#so_whatsapp_mobile').val('');
                            $('#so_whatsapp_message').val(message);
                            buildSoWhatsAppUrl();
                        }
                    },
                    error: function() {
                        if ($('#soWhatsappModal').attr('data-current-number') !== String(soNumber)) return;
                        var message = 'Dear Sir/Madam,\n\nSales Order ' + soNumber + ' is shared with you.\n\nPlease check and confirm.\n\nPDF: ' + pdfUrl + '\n\nThanks.';
                        $('#so_whatsapp_message').val(message);
                        buildSoWhatsAppUrl();
                    }
                });
            });

            $(document).on('input', '#so_whatsapp_mobile, #so_whatsapp_message', function() {
                buildSoWhatsAppUrl();
            });

            $(document).on('click', '#so_whatsapp_send_btn', function() {
                var mobile = $('#so_whatsapp_mobile').val().replace(/[^0-9]/g, '');
                var message = $('#so_whatsapp_message').val();
                if (!mobile || !message.trim()) {
                    alert('Please enter both mobile number and message');
                    return;
                }
                window.open('https://wa.me/' + mobile + '?text=' + encodeURIComponent(message), '_blank', 'noopener');
            });
        });
    </script>