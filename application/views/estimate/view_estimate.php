<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];

defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
    #ui-datepicker-div.month-only-picker .ui-datepicker-calendar {
        display: none !important;
    }
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
                    Quotation
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Quotation</a></li>
                    <li class="active">Quotation details</li>
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
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> Quotation Details
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <form action="<?php echo base_url(); ?>EstimateController/get_datewise_record" method="post" class="form-inline" style="margin: 0; display: inline-flex; align-items: center; gap: 8px;">
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
                                    <a href="<?php echo base_url(); ?>EstimateController/index?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #2e7d32 !important;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <a href="<?php echo base_url('EstimateController/export_all_quotations'); ?>" class="btn btn-warning btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #f57c00 !important; color: white !important;">
                                        <i class="fa fa-file-excel-o"></i> Export Quotation
                                    </a>
                                    <a href="<?php echo base_url(); ?>EstimateController/create_gst_estimate" class="btn btn-primary btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-plus"></i> Create Quotation
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_quotation_data_by_status' && $this->uri->segment(3) == '2') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>EstimateController/get_quotation_data_by_status/2">Sent <span class="badge badge-light"> <?php echo $quo_sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_quotation_data_by_status' && $this->uri->segment(3) == '1') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>EstimateController/get_quotation_data_by_status/1">Draft <span class="badge badge-light"> <?php echo $quo_draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'index' || $this->uri->segment(2) == '' || $this->uri->segment(2) == 'get_monthyearwise_record') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>EstimateController/index?str=All">All Quotations <span class="badge badge-light"> <?php echo $quo_count; ?></span></a>
                                </li>
                            </ul>

                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Company Name</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php $i = 1;

                                        foreach ($estimates as $key) { ?>
                                            <tr>
                                                <td><span id="" class=""></span>
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
                                                <?php }
                                                if ($key->status == 0) { ?>
                                                    <td></td><?php } ?>
                                                <td> <?php echo date('d-m-Y', strtotime($key->date)); ?> </td>
                                                <td><a href="<?php echo base_url() . 'EstimateController/show_quotation/' . $key->id ?>"><?php echo $key->number; ?> </a></td>
                                                <td> <?php echo $key->company_name; ?> </td>
                                                <td><?php if ($key->gst_type != 'I') { ?>
                                                        CGST/ SGST
                                                    <?php } else { ?>
                                                        IGST
                                                    <?php } ?>
                                                </td>
                                                <td> <?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->total), 0); ?> </td>

                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                            <li><a class="view-modal-email-send" data-href="#" data-id="<?php echo $key->number; ?>" >Send</a></li>
                                                            <li><a class="view-modal-estimate-whatsapp-send" href="#" data-id="<?php echo $key->number; ?>" data-pdf="<?php echo base_url() . 'Pdf/print_igst_quote?quote_number_id=' . $key->id; ?>">WhatsApp</a></li>
                                                            <li role="presentation" class="divider"></li>

                                                            <li><a href="<?php echo base_url() . 'Pdf/print_igst_quote?quote_number_id=' . $key->id ?>" name="btn_submit" id="download1" class="js-gear-download">Export As PDF</a></li>

                                                            <li><a href="<?php echo base_url() . 'EstimateController/show_quotation/' . $key->id; ?>" class="js-gear-view">View</a></li>
                                                            <li><a href="<?php echo base_url() . 'EstimateController/edit_estimate_details/' . $key->id ?>" class="js-gear-edit">Edit</a></li>
                                                            <li><a href="<?php echo base_url() . 'EstimateController/delete_quotation_by_quote_number/' . $key->number; ?>" class="js-gear-delete">Delete</a></li>
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

    <!-- ./Email modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Send Quotation<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>EstimateController/send_quotation_email" enctype="multipart/form-data">
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

    <div id="estimateWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center>
                        <h4 class="modal-title">Send Quotation WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="hidden" class="form-control" id="estimate_whatsapp_number" value="">
                                <input type="text" class="form-control input-sm" id="estimate_whatsapp_mobile" placeholder="Enter mobile number" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Message<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <textarea class="form-control input-sm" id="estimate_whatsapp_message" rows="4" required=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="estimate_whatsapp_send_link" target="_blank" rel="noopener" class="btn btn-success"><i class="fa fa-whatsapp" aria-hidden="true"></i> Send WhatsApp</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Hide loading spinner immediately when page is ready
        $(function() { $('#loader').hide(); });

        // Pre-initialize Quotation table with custom dom before footer generic init
        $(function() {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#example3')) {
                $('#example3').DataTable({
                    "pageLength": 25,
                    "dom": "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                           "<'row'<'col-sm-12'tr>>" +
                           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "language": {
                        "search": "Search Quotations:",
                        "lengthMenu": "_MENU_ entries per page"
                    }
                });
            }
        });

        $(document).ready(function() {
            function buildEstimateWhatsAppUrl() {
                var mobile = ($('#estimate_whatsapp_mobile').val() || '').replace(/[^0-9]/g, '');
                var message = $('#estimate_whatsapp_message').val() || '';


                if (!mobile || !message.trim()) {
                    $('#estimate_whatsapp_send_link').attr('href', '#');
                    return;
                }

                $('#estimate_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
            }

            $(document).on('click', '.view-modal-estimate-whatsapp-send', function(event) {
                event.preventDefault();
                event.stopPropagation();

                var number = $(this).data('id');
                var pdfUrl = $(this).data('pdf');
                var row = $(this).closest('tr');
                var quotationDate = $.trim(row.find('td:eq(2)').text());
                var quotationStatus = $.trim(row.find('td:eq(1)').text());

                $('.modal.in').modal('hide');
                $('#estimateWhatsappModal').modal('show');

                $('#estimate_whatsapp_number').val(number);
                $('#estimate_whatsapp_download_link').attr('href', pdfUrl);
                $('#estimate_whatsapp_download_link').hide();
                $('#estimate_whatsapp_message').val('Dear Sir/Madam,\nQuotation ' + number + ' is shared with you.\nDate: ' + quotationDate + '\nStatus: ' + quotationStatus + '\nClick to Download PDF:\n' + pdfUrl + '\nPlease check and confirm.\nThanks.');

                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>EstimateController/get_customer_mobile',
                    data: { number: number },
                    cache: false,
                    success: function(data) {
                        var result = $.parseJSON(data);
                        $('#estimate_whatsapp_mobile').val(result && result.mobile ? result.mobile : '');
                        buildEstimateWhatsAppUrl();
                    },
                    error: function() {
                        $('#estimate_whatsapp_mobile').val('');
                        buildEstimateWhatsAppUrl();
                    }
                });

                buildEstimateWhatsAppUrl();
            });

            $('#estimate_whatsapp_mobile, #estimate_whatsapp_message').on('input', function() {
                buildEstimateWhatsAppUrl();
            });

            $('#estimate_whatsapp_send_link').on('click', function(event) {
                if ($(this).attr('href') === '#') {
                    event.preventDefault();
                    alert('Please enter valid mobile number and message.');
                    return;
                }

                $('#estimate_whatsapp_download_link').show();
            });
        });
    </script>