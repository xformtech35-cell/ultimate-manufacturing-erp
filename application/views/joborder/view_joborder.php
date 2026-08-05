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
                    Job Order
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Job Order</a></li>
                    <li class="active">Job Order details</li>
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
                                        <h3 class="box-title">Job Order details</h3>
                                    </div>
                                    <div class="col-md-4">
                                        <form action="<?php echo base_url(); ?>JobOrderController/get_monthyearwise_record" method="post">
                                            <div class="form-group row">
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control onlymonth input-sm pull-right" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off" required="">
                                                </div>
                                                <button class="btn btn-primary pull-right" name="submit" value="">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?php echo base_url(); ?>JobOrderController/index?str=All"><button class="btn btn-success btn-sm"> Show All</button></a>
                                        <a href="<?php echo base_url('JobOrderController/export_all_joborders'); ?>" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Export JO</a>
                                        <a href="<?php echo base_url(); ?>JobOrderController/create_joborder"><button class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Create</button></a>
                                    </div>
                                </div>
                            </div>

                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo base_url(); ?>JobOrderController/get_joborder_data_by_status/2">Sent <span class="badge badge-light"> <?php echo $joborder_sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo base_url(); ?>JobOrderController/get_joborder_data_by_status/1">Draft <span class="badge badge-light"> <?php echo $joborder_draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="<?php echo base_url(); ?>JobOrderController/index?str=All">All Job Orders <span class="badge badge-light"> <?php echo $joborder_count; ?></span></a>
                                </li>
                            </ul>

                            <!-- /.box-header -->
                            <div class="box-body">

                              

                                <table id="example0" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Job Order Number</th>
                                            <th>SO Number</th>
                                            <th>Company Name</th>
                                            <th>Created By</th>
                                            <th>Approved By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($joborders as $key) { ?>
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
                                                <?php } ?>
                                                <?php if ($key->status == 0) { ?>
                                                    <td></td>
                                                <?php } ?>

                                                <td><?php
                                                    if (!empty($key->date) && $key->date !== '0000-00-00') {
                                                        echo date('d-m-Y', strtotime($key->date));
                                                    } else {
                                                        echo '';
                                                    }
                                                ?></td>
                                                <td><a href="<?php echo base_url() . 'JobOrderController/show_joborder/' . $key->id ?>"><?php echo $key->number_fk; ?></a></td>
                                                <td>
                                                    <?php
                                                    if (!empty($key->so_reference)) {
                                                        echo $key->so_reference;
                                                    } elseif (!empty($key->oc_number)) {
                                                        echo $key->oc_number;
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo isset($key->company_name) ? $key->company_name : ''; ?></td>
                                                <td><span class="label label-info" style="font-size: 11px; font-weight: normal;"><?php echo !empty($key->prepare_by) ? htmlspecialchars($key->prepare_by) : 'Admin'; ?></span></td>
                                                <td><span class="label label-success" style="font-size: 11px; font-weight: normal;"><?php echo !empty($key->approved_by_name) ? htmlspecialchars($key->approved_by_name) : ($key->status == 4 ? 'Admin' : '-'); ?></span></td>

                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                            <li><a class="view-modal-email-send" href="#" data-id="<?php echo $key->number_fk; ?>"> Send </a></li>
                                                            <li><a class="view-modal-joborder-whatsapp-send" href="#" data-id="<?php echo $key->number_fk; ?>" data-pdf="<?php echo base_url() . 'Pdf/print_joborder/' . $key->id; ?>">WhatsApp</a></li>
                                                            <li role="presentation" class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'Pdf/print_joborder/' . $key->id ?>">Export As PDF</a></li>
                                                            <li><a href="<?php echo base_url() . 'JobOrderController/export_joborder_excel/' . $key->id ?>">Export As Excel</a></li>
                                                            <li role="presentation" class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'JobOrderController/show_joborder/' . $key->id; ?>">View</a></li>
                                                            <li><a href="<?php echo base_url() . 'JobOrderController/edit_joborder_details/' . $key->id ?>">Edit</a></li>
                                                            <li role="presentation" class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'JobOrderController/delete_joborder_by_joborder_number/' . $key->number_fk; ?>" onclick="return confirm('Are you sure you want to delete this Job Order?')">Delete</a></li>
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
                        <h4 class="modal-title">Send Job Order<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>JobOrderController/send_joborder_email" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="customer_id" id="customer_id" value="">
                                    <input type="hidden" class="form-control" name="number" id="number" value="">
                                    <input type="text" class="form-control input-sm" name="to_email" id="to_email" required="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Subject<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="subject" id="subject" rows="2" required=""></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Message</label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="message" id="message" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Send a copy to</label>
                                <div class="col-sm-7">
                                    <input type="checkbox" name="copy_email" id="copy_email"> <?php echo $set_cc_email; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ./WhatsApp modal -->
    <div id="joborderWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center>
                        <h4 class="modal-title">Send WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="hidden" class="form-control" id="jo_whatsapp_number" value="">
                                <input type="text" class="form-control input-sm" id="jo_whatsapp_mobile" placeholder="Enter mobile number" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Message<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <textarea class="form-control input-sm" id="jo_whatsapp_message" rows="4" required=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="jo_whatsapp_download_link" target="_blank" rel="noopener" class="btn btn-info" style="display:none;"><i class="fa fa-download" aria-hidden="true"></i> Download PDF</a>
                    <a href="#" id="jo_whatsapp_send_link" target="_blank" rel="noopener" class="btn btn-success"><i class="fa fa-whatsapp" aria-hidden="true"></i> Send WhatsApp</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // WhatsApp functionality
        function buildJobOrderWhatsAppUrl() {
            var mobile = ($('#jo_whatsapp_mobile').val() || '').replace(/[^0-9]/g, '');
            var message = $('#jo_whatsapp_message').val() || '';

            if (!mobile || !message.trim()) {
                $('#jo_whatsapp_send_link').attr('href', '#');
                return;
            }

            $('#jo_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
        }

        $(document).on('click', '.view-modal-joborder-whatsapp-send', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var number = $(this).data('id');
            var pdfUrl = $(this).data('pdf');
            var row = $(this).closest('tr');
            var joborderDate = $.trim(row.find('td:eq(2)').text());
            var joborderStatus = $.trim(row.find('td:eq(1)').text());

            $('.modal.in').modal('hide');
            $('#joborderWhatsappModal').modal('show');

            $('#jo_whatsapp_number').val(number);
            $('#jo_whatsapp_download_link').attr('href', pdfUrl);
            $('#jo_whatsapp_download_link').hide();
            $('#jo_whatsapp_message').val('Dear Sir/Madam,\nJob Order ' + number + ' is shared with you.\nDate: ' + joborderDate + '\nStatus: ' + joborderStatus + '\nClick to Download PDF:\n' + pdfUrl + '\nPlease check and confirm.\nThanks.');

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>JobOrderController/get_customer_mobile',
                data: { number: number },
                cache: false,
                success: function(data) {
                    var result = $.parseJSON(data);
                    $('#jo_whatsapp_mobile').val(result && result.mobile ? result.mobile : '');
                    buildJobOrderWhatsAppUrl();
                },
                error: function() {
                    $('#jo_whatsapp_mobile').val('');
                    buildJobOrderWhatsAppUrl();
                }
            });

            buildJobOrderWhatsAppUrl();
        });

        $('#jo_whatsapp_mobile, #jo_whatsapp_message').on('input', function() {
            buildJobOrderWhatsAppUrl();
        });

        $('#jo_whatsapp_send_link').on('click', function(event) {
            if ($(this).attr('href') === '#') {
                event.preventDefault();
                alert('Please enter valid mobile number and message.');
                return;
            }
            $('#jo_whatsapp_download_link').show();
        });

        // Handle email modal data population
        $('.view-modal-email-send').click(function(event) {
            event.preventDefault();
            var joborder_number = $(this).data('id');
            $('#number').val(joborder_number);
            $('#to_email').val('');
            $('#customer_id').val('');
            $('#subject').val('Job Order: ' + joborder_number);
            $('#message').val('Please find attached Job Order for your reference.');
            
            // Get customer email via AJAX
            $.ajax({
                url: '<?php echo base_url(); ?>JobOrderController/get_customer_email',
                type: 'POST',
                data: {number: joborder_number},
                dataType: 'json',
                success: function(data) {
                    if(data.success) {
                        $('#to_email').val(data.email);
                        $('#customer_id').val(data.customer_id);
                    }
                    $('#modal').modal('show');
                },
                error: function() {
                    $('#modal').modal('show');
                }
            });
        });
    });
    </script>
