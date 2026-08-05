<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
$session_data_head2 = $this->session->userdata('session_data_head2');
if (!isset($session_data_head2) || !is_array($session_data_head2)) {
    $session_data_head2 = [];
}
$set_cc_email = isset($session_data_head2['cc_email']) ? $session_data_head2['cc_email'] : '';
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    GRN
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">GRN</a></li>
                    <li class="active">GRN details</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">GRN details</h3>
                                <a href="<?php echo base_url(); ?>GrnController/create_grn"><button class="btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i>Create GRN</button></a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <!-- Start Flash Message -->
                               

                              
                                <!-- End Flash Message -->

                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Date</th>
                                            <th>GRN Number</th>
                                            <th>PO Number</th>
                                            <th>Company</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php $i = 1;
                                        foreach ($grn as $key) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->date; ?> </td>
                                                <td><a href="<?php echo base_url() . 'GrnController/show_grn/' . $key->grn_number ?>"><?php echo $key->grn_number; ?> </a></td>
                                                <td> <?php echo $key->po_number_fk; ?> </td>
                                                <td> <?php echo $key->company_name; ?> </td>
                                                <td> <?php echo $key->fullname; ?> </td>
                                                <td> <?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->total), 0); ?> </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span></button>
                                                        <!-- In the action dropdown menu (around line where you have Export As PDF and Delete) -->
                                                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                            <li><a class="view-modal-grn-email-send" href="#" data-id="<?php echo $key->grn_number; ?>"> Send Email </a></li>
                                                            <li><a class="view-modal-grn-whatsapp-send" href="#" data-id="<?php echo $key->grn_number; ?>" data-pdf="<?php echo base_url() . 'Pdf/grn_pdf/' . $key->grn_number; ?>">WhatsApp </a></li>
                                                            <li role="presentation" class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'Pdf/grn_pdf/' . $key->grn_number ?>" name="btn_submit" id="download1" class="js-gear-download">Export As PDF</a></li>
                                                            <!-- Add this line for inspection report -->
                                                            <li><a href="<?php echo base_url() . 'GrnController/conduct_inspection/' . $key->grn_number; ?>">Conduct Inspection</a></li>

                                                            <li><a href="<?php echo base_url() . 'GrnController/inspection_report/' . $key->grn_number; ?>">View Inspection Report</a></li>
                                                            <li><a href="<?php echo base_url() . 'GrnController/delete_grn_by_grn_number/' . $key->grn_number; ?>" class="js-gear-delete">Delete</a></li>
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

    <!-- ./WhatsApp modal -->
    <div id="grnWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center><h4 class="modal-title">Send GRN WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="grn_whatsapp_mobile">Mobile Number<span style="color: red;">*</span></label>
                        <input type="text" class="form-control input-sm" id="grn_whatsapp_mobile" placeholder="Enter mobile number">
                    </div>
                    <div class="form-group">
                        <label for="grn_whatsapp_message">Message<span style="color: red;">*</span></label>
                        <textarea class="form-control input-sm" id="grn_whatsapp_message" rows="5" placeholder="Enter your message"></textarea>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="grn_whatsapp_send_btn">WhatsApp</button>
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
                        <h4 class="modal-title">Send GRN<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>GrnController/send_grn_email" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
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
                                    <input type="checkbox" class="" name="copy_email" id="copy_email"> <?php echo $set_cc_email; ?>
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
        function buildGrnWhatsAppUrl() {
            var mobile = $('#grn_whatsapp_mobile').val().replace(/[^0-9]/g, '');
            var message = $('#grn_whatsapp_message').val();
            if (mobile && message.trim()) {
                $('#grn_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
                $('#grn_whatsapp_url_info').show();
            } else {
                $('#grn_whatsapp_url_info').hide();
            }
        }

        $(document).ready(function() {
            $(document).on('click', '.view-modal-grn-email-send', function(event) {
                event.preventDefault();
                var number = $(this).data('id');
                $('#number').val(number);
                $('#subject').val('GRN ' + number);
                $('#modal').attr('data-current-number', number);

                $.ajax({
                    url: '<?php echo base_url(); ?>GrnController/get_grn_supplier_contact',
                    type: 'POST',
                    data: { number: number },
                    dataType: 'json',
                    success: function(response) {
                        if ($('#modal').attr('data-current-number') !== String(number)) {
                            return;
                        }
                        $('#to_email').val(response && response.email ? response.email : '');
                        $('#modal').modal('show');
                    },
                    error: function() {
                        if ($('#modal').attr('data-current-number') !== String(number)) {
                            return;
                        }
                        $('#to_email').val('');
                        $('#modal').modal('show');
                    }
                });
            });

            $(document).on('click', '.view-modal-grn-whatsapp-send', function(event) {
                event.preventDefault();
                var grnNumber = $(this).data('id');
                var pdfUrl = $(this).data('pdf');
                $('#grnWhatsappModal').attr('data-current-number', grnNumber);

                $('#grnWhatsappModal').modal('show');
                $('#grn_whatsapp_mobile').val('');
                $('#grn_whatsapp_message').val('');
                $('#grn_whatsapp_url_info').hide();

                $.ajax({
                    url: '<?php echo base_url(); ?>GrnController/get_grn_supplier_contact',
                    type: 'POST',
                    data: { number: grnNumber },
                    dataType: 'json',
                    success: function(result) {
                        if ($('#grnWhatsappModal').attr('data-current-number') !== String(grnNumber)) {
                            return;
                        }
                        if (typeof result === 'string') {
                            try { result = $.parseJSON(result); } catch (e) { result = null; }
                        }
                        result = result || {};
                        var rawMobile = result.mobile || result.supplier_mobile || result.mobile_number || result.phone || '';
                        var mobile = String(rawMobile).replace(/[^0-9]/g, '');
                        $('#grn_whatsapp_mobile').val(mobile);

                        var message = 'Dear Sir/Madam,\n\nGRN ' + grnNumber + ' is shared with you.\n\nPlease check and confirm.\n\nPDF: ' + pdfUrl + '\n\nThanks.';
                        $('#grn_whatsapp_message').val(message);
                        buildGrnWhatsAppUrl();
                    },
                    error: function() {
                        if ($('#grnWhatsappModal').attr('data-current-number') !== String(grnNumber)) {
                            return;
                        }
                        var message = 'Dear Sir/Madam,\n\nGRN ' + grnNumber + ' is shared with you.\n\nPlease check and confirm.\n\nPDF: ' + pdfUrl + '\n\nThanks.';
                        $('#grn_whatsapp_message').val(message);
                        buildGrnWhatsAppUrl();
                    }
                });
            });

            $(document).on('input', '#grn_whatsapp_mobile, #grn_whatsapp_message', function() {
                buildGrnWhatsAppUrl();
            });

            $(document).on('click', '#grn_whatsapp_send_btn', function() {
                var mobile = $('#grn_whatsapp_mobile').val().replace(/[^0-9]/g, '');
                var message = $('#grn_whatsapp_message').val();
                if (!mobile || !message.trim()) {
                    alert('Please enter both mobile number and message');
                    return;
                }
                window.open('https://wa.me/' + mobile + '?text=' + encodeURIComponent(message), '_blank', 'noopener');
            });
        });
    </script>
