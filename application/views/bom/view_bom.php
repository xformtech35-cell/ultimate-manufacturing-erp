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
                    BOM
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">BOM</a></li>
                    <li class="active">BOM details</li>
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
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> BOM Details
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <form action="<?php echo base_url(); ?>BomController/get_datewise_record" method="post" class="form-inline" style="margin: 0; display: inline-flex; align-items: center; gap: 8px;">
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
                                    <a href="<?php echo base_url(); ?>AiController/bom_triage" class="btn btn-info btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #0288d1 !important; color: white !important;">
                                        <i class="fa fa-magic"></i> AI Draft Triage
                                    </a>
                                    <a href="<?php echo base_url(); ?>BomController/index?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #2e7d32 !important;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <a href="<?php echo base_url('BomController/export_all_boms'); ?>" class="btn btn-warning btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #f57c00 !important; color: white !important;">
                                        <i class="fa fa-file-excel-o"></i> Export BOM
                                    </a>
                                    <a href="<?php echo base_url(); ?>BomController/create_gst_bom" class="btn btn-primary btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-plus"></i> Create BOM
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>

                            <ul class="nav nav-tabs">
                                <?php
                                $c_action = $this->uri->segment(2);
                                $c_status = $this->uri->segment(3);
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($c_action == 'get_bom_data_by_status' && $c_status == '1') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>BomController/get_bom_data_by_status/1">Draft <span class="badge badge-light"><?php echo isset($bom_draft_count) ? $bom_draft_count : 0; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($c_action == 'get_bom_data_by_status' && $c_status == '2') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>BomController/get_bom_data_by_status/2">Sent <span class="badge badge-light"><?php echo isset($bom_sent_count) ? $bom_sent_count : 0; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($c_action == 'get_bom_data_by_status' && $c_status == '7') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>BomController/get_bom_data_by_status/7">Under Review <span class="badge badge-light"><?php echo isset($bom_under_review_count) ? $bom_under_review_count : 0; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($c_action == 'get_bom_data_by_status' && $c_status == '4') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>BomController/get_bom_data_by_status/4">Approved <span class="badge badge-light"><?php echo isset($bom_approved_count) ? $bom_approved_count : 0; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($c_action == 'get_bom_data_by_status' && $c_status == '5') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>BomController/get_bom_data_by_status/5">Rejected <span class="badge badge-light"><?php echo isset($bom_rejected_count) ? $bom_rejected_count : 0; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($c_action == 'index' || empty($c_action) || $c_action == 'get_monthyearwise_record' || $c_action == 'get_datewise_record') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>BomController/index?str=All">All BOMs <span class="badge badge-light"><?php echo isset($bom_count) ? $bom_count : 0; ?></span></a>
                                </li>
                            </ul>

                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <table id="example0" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>BOM Number</th>
                                            <th>Company Name</th>
                                            <th>SO/OC Number</th>
                                            <th>Created By</th>
                                            <th>Approved By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        if (isset($boms) && !empty($boms)) {
                                            foreach ($boms as $key) {
                                                $bom_id = isset($key->bom_total_id) ? $key->bom_total_id : (isset($key->id) ? $key->id : 0);
                                        ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>

                                                <?php $status = isset($key->status) ? $key->status : 0; ?>
                                                <?php if ($status == 0) { ?>
                                                    <td><span class="label label-default">Pending</span></td>
                                                <?php } elseif ($status == 1) { ?>
                                                    <td><span class="label label-info">Draft</span></td>
                                                <?php } elseif ($status == 2) { ?>
                                                    <td><span class="label label-primary">Sent</span></td>
                                                <?php } elseif ($status == 3) { ?>
                                                    <td><span class="label label-default">Viewed</span></td>
                                                <?php } elseif ($status == 4) { ?>
                                                    <td><span class="label label-success">Approved</span></td>
                                                <?php } elseif ($status == 5) { ?>
                                                    <td><span class="label label-danger">Rejected</span></td>
                                                <?php } elseif ($status == 6) { ?>
                                                    <td><span class="label label-warning">Canceled</span></td>
                                                <?php } elseif ($status == 7) { ?>
                                                    <td><span class="label label-warning">Under Review</span></td>
                                                <?php } else { ?>
                                                    <td></td>
                                                <?php } ?>

                                                <td><?php
                                                    if (!empty($key->date) && $key->date !== '0000-00-00') {
                                                        echo date('d-m-Y', strtotime($key->date));
                                                    } else {
                                                        echo '';
                                                    }
                                                ?></td>
                                                 <td>
                                                     <a href="<?php echo base_url() . 'BomController/show_bom/' . $bom_id; ?>"><?php echo isset($key->number) ? $key->number : ''; ?></a>
                                                     <?php if (isset($key->send_to_mrp)) {
                                                         if ($key->send_to_mrp == 1) {
                                                             echo ' <span class="label label-warning" style="margin-left: 5px;">Sent to MRP</span>';
                                                         } elseif ($key->send_to_mrp == 2) {
                                                             echo ' <span class="label label-success" style="margin-left: 5px;">MRP Run</span>';
                                                         }
                                                     } ?>
                                                 </td>
                                                 <td><?php echo isset($key->company_name) ? $key->company_name : ''; ?></td>
                                                 <td><?php echo isset($key->oc_number) ? $key->oc_number : ''; ?></td>
                                                 <td><span class="label label-info" style="font-size: 11px; font-weight: normal;"><?php echo !empty($key->prepare_by) ? htmlspecialchars($key->prepare_by) : 'Admin'; ?></span></td>
                                                 <td>
                                                     <?php if ($status == 4) { ?>
                                                         <span class="label label-success" style="font-size: 11px; font-weight: normal;"><?php echo !empty($key->approved_by_name) ? htmlspecialchars($key->approved_by_name) : 'Admin'; ?></span>
                                                     <?php } else { ?>
                                                         -
                                                     <?php } ?>
                                                 </td>

                                                 <td>
                                                     <div class="dropdown">
                                                         <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown">
                                                             <span class="caret"></span></button>
                                                         <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="menu1">
                                                             <li><a class="view-modal-email-send" data-toggle="modal" data-id="<?php echo isset($key->number) ? $key->number : ''; ?>" data-target="#modal"> Send </a></li>
                                                             <li role="presentation" class="divider"></li>
                                                             <li><a href="<?php echo base_url() . 'Pdf/print_bom/' . $bom_id; ?>">Export As PDF</a></li>
                                                             <li><a href="<?php echo base_url() . 'BomController/export_bom_excel/' . $bom_id; ?>">Export As Excel</a></li>
                                                             <li role="presentation" class="divider"></li>
                                                             <li><a href="<?php echo base_url() . 'BomController/show_bom/' . $bom_id; ?>">View</a></li>
                                                              <?php
                                                              $bom_status = isset($key->status) ? (int)$key->status : 0;
                                                              $send_to_mrp_val = isset($key->send_to_mrp) ? (int)$key->send_to_mrp : 0;
                                                              
                                                              // Edit button visibility logic:
                                                              // Show Edit if:
                                                              // 1. BOM is Draft (0/1) or Rejected (5)
                                                              // 2. BOM is Approved (4) ONLY IF it has been Unsent from MRP (send_to_mrp == 0) or MRP has already executed (send_to_mrp == 2)
                                                              // Hide Edit if BOM is Under Review (7) or active in MRP (send_to_mrp == 1)
                                                              $show_edit = false;
                                                              if (in_array($bom_status, [0, 1, 5])) {
                                                                  $show_edit = true;
                                                              } elseif ($bom_status == 4) {
                                                                  if ($send_to_mrp_val == 0 || $send_to_mrp_val == 2) {
                                                                      $show_edit = true;
                                                                  }
                                                              }
                                                              if ($show_edit) { ?>
                                                                  <li><a href="<?php echo base_url() . 'BomController/edit_bom_details/' . $bom_id; ?>">Edit</a></li>
                                                              <?php } ?>
                                                              <li role="presentation" class="divider"></li>
                                                             <?php if (in_array($bom_status, [0, 1, 5])) { ?>
                                                                 <!-- Pending or Rejected: show Submit for Approval -->
                                                                 <li><a href="<?php echo base_url() . 'BomController/submit_bom_for_approval/' . $bom_id; ?>" onclick="return confirm('Submit this BOM for Sales Approval?')"><i class="fa fa-paper-plane text-info"></i> Submit for Approval</a></li>
                                                             <?php } elseif ($bom_status == 7) { ?>
                                                                 <!-- Under Review -->
                                                                 <li><a href="javascript:void(0);" style="color:#bbb;cursor:not-allowed;"><i class="fa fa-clock-o text-warning"></i> Under Review...</a></li>
                                                             <?php } elseif ($bom_status == 4) { ?>
                                                                 <!-- Approved: show Send to MRP options -->
                                                                 <?php if ($send_to_mrp_val == 1) { ?>
                                                                     <li><a href="<?php echo base_url() . 'BomController/unsend_from_mrp/' . $bom_id; ?>"><i class="fa fa-reply text-warning"></i> Unsend from MRP</a></li>
                                                                 <?php } elseif ($send_to_mrp_val == 2) { ?>
                                                                     <li><a href="javascript:void(0);" style="color:#bbb;cursor:not-allowed;text-decoration:line-through;"><i class="fa fa-share text-muted"></i> Send to MRP</a></li>
                                                                     <li><a href="javascript:void(0);" style="color:#bbb;cursor:not-allowed;text-decoration:line-through;"><i class="fa fa-reply text-muted"></i> Unsend from MRP</a></li>
                                                                 <?php } else { ?>
                                                                     <li><a href="<?php echo base_url() . 'BomController/send_to_mrp/' . $bom_id; ?>"><i class="fa fa-share text-success"></i> Send to MRP</a></li>
                                                                 <?php } ?>
                                                             <?php } else { ?>
                                                                 <!-- Draft/Sent/Viewed: cannot send to MRP yet -->
                                                                 <li><a href="javascript:void(0);" style="color:#bbb;cursor:not-allowed;" title="BOM must be Approved first"><i class="fa fa-lock text-muted"></i> Send to MRP (Locked)</a></li>
                                                             <?php } ?>
                                                             <?php if ($bom_status != 4) { ?>
                                                                 <li role="presentation" class="divider"></li>
                                                                 <li><a href="<?php echo base_url() . 'BomController/delete_bom_by_bom_number/' . (isset($key->number) ? $key->number : ''); ?>" onclick="return confirm('Are you sure you want to delete this BOM?')">Delete</a></li>
                                                             <?php } ?>
                                                         </ul>
                                                     </div>
                                                </td>
                                            </tr>
                                        <?php
                                            $i++;
                                            }
                                        } else { ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No BOM records found</td>
                                            </tr>
                                        <?php } ?>
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
                        <h4 class="modal-title">Send BOM<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>BomController/send_bom_email" enctype="multipart/form-data">
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

    <script>
    // Hide loading spinner immediately when page is ready
    $(function() { $('#loader').hide(); });

    // Pre-initialize BOM table with custom dom before footer generic init
    $(function() {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#example0')) {
            $('#example0').DataTable({
                "pageLength": 25,
                "dom": "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "language": {
                    "search": "Search BOMs:",
                    "lengthMenu": "_MENU_ entries per page"
                }
            });
        }
    });

    $(document).ready(function() {
        // Handle email modal data population

        $('.view-modal-email-send').click(function() {
            var bom_number = $(this).data('id');
            $('#number').val(bom_number);
            
            // Get customer email via AJAX
            $.ajax({
                url: '<?php echo base_url(); ?>BomController/get_customer_email',
                type: 'POST',
                data: {number: bom_number},
                dataType: 'json',
                success: function(data) {
                    if(data.success) {
                        $('#to_email').val(data.email);
                        $('#customer_id').val(data.customer_id);
                        $('#subject').val('BOM: ' + bom_number);
                        $('#message').val('Please find attached BOM for your reference.');
                    }
                }
            });
        });
    });
    </script>
</body>
</html>