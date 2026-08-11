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
                            <div class="box-header with-border" style="padding: 12px 15px; display: block !important; clear: both !important;">
                                <div style="float: left !important; display: inline-block;">
                                    <h3 class="box-title" style="float: left !important; font-weight: 600; margin: 0; font-size: 18px; color: #1e293b; display: inline-flex; align-items: center; gap: 8px; line-height: 30px;">
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> Job Order Details
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <form action="<?php echo base_url(); ?>JobOrderController/get_datewise_record" method="post" class="form-inline" style="margin: 0; display: inline-flex; align-items: center; gap: 8px;">
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
                                    <a href="<?php echo base_url(); ?>JobOrderController/index?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #2e7d32 !important;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <a href="<?php echo base_url('JobOrderController/export_all_joborders'); ?>" class="btn btn-warning btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #f57c00 !important; color: white !important;">
                                        <i class="fa fa-file-excel-o"></i> Export JO
                                    </a>
                                    <a href="<?php echo base_url(); ?>JobOrderController/create_joborder" class="btn btn-primary btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-plus"></i> Create Job Order
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>

                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_joborder_data_by_status' && $this->uri->segment(3) == '6') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>JobOrderController/get_joborder_data_by_status/6">Closed <span class="badge badge-light"> <?php echo $joborder_closed_count ?? 0; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_joborder_data_by_status' && $this->uri->segment(3) == '4') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>JobOrderController/get_joborder_data_by_status/4">Approved <span class="badge badge-light"> <?php echo $joborder_approved_count ?? 0; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_joborder_data_by_status' && $this->uri->segment(3) == '2') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>JobOrderController/get_joborder_data_by_status/2">Sent <span class="badge badge-light"> <?php echo $joborder_sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_joborder_data_by_status' && $this->uri->segment(3) == '1') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>JobOrderController/get_joborder_data_by_status/1">Draft <span class="badge badge-light"> <?php echo $joborder_draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'index' || $this->uri->segment(2) == '' || $this->uri->segment(2) == 'get_monthyearwise_record') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>JobOrderController/index?str=All">All Job Orders <span class="badge badge-light"> <?php echo $joborder_count; ?></span></a>
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

                                                <?php 
                                                $status_class = 'label-default';
                                                $status_text = 'Draft';
                                                switch ($key->status) {
                                                    case 1:
                                                        $status_class = 'label-default';
                                                        $status_text = 'Draft';
                                                        break;
                                                    case 2:
                                                        $status_class = 'label-warning';
                                                        $status_text = 'Sent';
                                                        break;
                                                    case 3:
                                                        $status_class = 'label-primary';
                                                        $status_text = 'Viewed';
                                                        break;
                                                    case 4:
                                                        $status_class = 'label-success';
                                                        $status_text = 'Approved';
                                                        break;
                                                    case 5:
                                                        $status_class = 'label-danger';
                                                        $status_text = 'Rejected';
                                                        break;
                                                    case 6:
                                                        $status_class = 'label-danger';
                                                        $status_text = 'Closed';
                                                        break;
                                                    default:
                                                        $status_class = 'label-default';
                                                        $status_text = 'Draft';
                                                        break;
                                                }
                                                ?>
                                                <td><span class="label <?php echo $status_class; ?>" style="font-size: 11px; font-weight: normal;"><?php echo $status_text; ?></span></td>

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
                                                <td><span class="label label-success" style="font-size: 11px; font-weight: normal;"><?php echo !empty($key->approved_by_name) ? htmlspecialchars($key->approved_by_name) : ($key->status == 4 || $key->status == 6 ? 'Admin' : '-'); ?></span></td>

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
                                                            <li><a class="change-jo-status-btn" href="#" data-id="<?php echo $key->id; ?>" data-number="<?php echo $key->number_fk; ?>" data-status="<?php echo $key->status; ?>" data-note="<?php echo htmlspecialchars($key->note ?? ''); ?>"><i class="fa fa-refresh"></i> Change Status</a></li>
                                                            <?php if ($key->status != 6): ?>
                                                                <li><a href="<?php echo base_url('JobOrderController/force_close_joborder/' . $key->id); ?>" onclick="return confirm('Are you sure you want to FORCEFULLY CLOSE this Job Order?')" style="color:#d9534f;"><i class="fa fa-times-circle"></i> Force Close</a></li>
                                                            <?php endif; ?>
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

        // Change Job Order Status modal handler
        $(document).on('click', '.change-jo-status-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var number = $(this).data('number');
            var status = $(this).data('status');
            var note = $(this).data('note') || '';
            $('#jo_status_id').val(id);
            $('#jo_status_number').val(number);
            $('#jo_status_select').val(status);
            $('#jo_status_remarks').val(note);
            $('#joStatusModal').modal('show');
        });
    });
    </script>

    <!-- Change Job Order Status Modal -->
    <div class="modal fade" id="joStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background:#3c8dbc; color:#fff;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-refresh"></i> Change Status</h4>
                </div>
                <form method="post" action="<?php echo base_url(); ?>JobOrderController/update_joborder_status">
                    <div class="modal-body">
                        <input type="hidden" name="jo_id" id="jo_status_id">
                        <input type="hidden" name="jo_number" id="jo_status_number">
                        <div class="form-group">
                            <label>Status<span style="color:red;">*</span></label>
                            <select name="status" id="jo_status_select" class="form-control input-sm" required>
                                <option value="1">Draft</option>
                                <option value="2">Sent</option>
                                <option value="4">Approved</option>
                                <option value="6">Closed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remark / Note</label>
                            <textarea name="remarks" id="jo_status_remarks" class="form-control input-sm" rows="2" placeholder="Enter status change remark..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Update</button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
