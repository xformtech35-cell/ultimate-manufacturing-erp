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
                <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('ERRORMSG')): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Error!</strong> <?php echo $this->session->flashdata('ERRORMSG'); ?>
                    </div>
                <?php endif; ?>
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
                                    <a href="<?php echo base_url('SalesOrderController/so_approval_dashboard'); ?>" class="btn btn-info btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none; background-color: #0288d1 !important; color: white !important;">
                                        <i class="fa fa-check-square-o"></i> SO Approval Dashboard
                                    </a>
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
                                            <th style="width: 4%;">Sr.No.</th>
                                            <th style="width: 6%;">Status</th>
                                            <th style="width: 8%;">Date</th>
                                            <th style="width: 12%;">Number</th>
                                            <th style="width: 12%;">Customer Name</th>
                                            <th style="width: 12%;">Company Name</th>
                                            <th style="width: 6%;">Type</th>
                                            <th style="width: 8%;">Amount</th>
                                            <th style="width: 10%;">Created By</th>
                                            <th style="width: 10%;">Status Changed By</th>
                                            <th style="width: 10%;">Remarks</th>
                                            <th style="width: 4%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php $i = 1;

                                        foreach ($salesorders as $key) { ?>
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
                                                         $status_text = 'Under Process';
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
                                                         $status_text = 'Hold';
                                                         break;
                                                     case 6:
                                                         $status_class = 'label-danger';
                                                         $status_text = 'Canceled';
                                                         break;
                                                     default:
                                                         $status_class = 'label-default';
                                                         $status_text = 'Draft';
                                                         break;
                                                 }
                                                 ?>
                                                 <td><span class="label <?php echo $status_class; ?>" style="font-size: 11px; font-weight: normal;"><?php echo $status_text; ?></span></td>
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
                                                 <td style="min-width: 130px;"><span class="label label-info" style="font-size: 11px; font-weight: normal; white-space: normal; display: inline-block; text-align: left; word-break: break-word;"><?php echo !empty($key->created_by_name) ? htmlspecialchars($key->created_by_name) : 'Admin'; ?></span></td>
                                                  <td style="min-width: 130px;">
                                                      <?php
                                                      if ($key->status == 0 || $key->status == 1) {
                                                          echo '-';
                                                      } else {
                                                          $handler_name = !empty($key->approved_by_name) ? htmlspecialchars($key->approved_by_name) : (!empty($key->created_by_name) ? htmlspecialchars($key->created_by_name) : 'Admin');
                                                          if (!empty($handler_name)) {
                                                              switch ($key->status) {
                                                                  case 4: // Approved
                                                                      echo '<span class="label label-success" style="font-size: 11px; font-weight: normal; white-space: normal; display: inline-block; text-align: left; word-break: break-word;">Approved by: ' . $handler_name . '</span>';
                                                                      break;
                                                                  case 5: // Hold
                                                                      echo '<span class="label label-danger" style="font-size: 11px; font-weight: normal; white-space: normal; display: inline-block; text-align: left; word-break: break-word;">Held by: ' . $handler_name . '</span>';
                                                                      break;
                                                                  case 6: // Canceled
                                                                      echo '<span class="label label-warning" style="font-size: 11px; font-weight: normal; white-space: normal; display: inline-block; text-align: left; word-break: break-word;">Canceled by: ' . $handler_name . '</span>';
                                                                      break;
                                                                  case 2: // Sent
                                                                      echo '<span class="label label-warning" style="font-size: 11px; font-weight: normal; white-space: normal; display: inline-block; text-align: left; word-break: break-word;">Under Process (Sent by: ' . $handler_name . ')</span>';
                                                                      break;
                                                                  case 3: // Viewed
                                                                      echo '<span class="label label-primary" style="font-size: 11px; font-weight: normal; white-space: normal; display: inline-block; text-align: left; word-break: break-word;">Viewed by: ' . $handler_name . '</span>';
                                                                      break;
                                                                  default:
                                                                      echo '<span class="label label-default" style="font-size: 11px; font-weight: normal; white-space: normal; display: inline-block; text-align: left; word-break: break-word;">Changed by: ' . $handler_name . '</span>';
                                                                      break;
                                                              }
                                                          } else {
                                                              echo '-';
                                                          }
                                                      }
                                                      ?>
                                                  </td>
                                                 <td style="max-width: 150px; font-size: 11px; word-break: break-word;"><?php echo !empty($key->remarks) ? htmlspecialchars($key->remarks) : '-'; ?></td>
 
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
                                                            <?php
                                                             $session_data_head = $this->session->userdata('session_data_head');
                                                             $permissions = $session_data_head['permission'] ?? array();
                                                             $role_name_session = $session_data_head['result']['role_name'] ?? '';
                                                             $is_admin_user = (strtolower($role_name_session) === 'admin' || ($session_data_head['result']['role'] ?? null) == 1);
                                                             if ($is_admin_user || in_array('SO_Approval', $permissions)):
                                                             ?>
                                                                 <li><a class="change-so-status-btn" href="#" data-id="<?php echo $key->id; ?>" data-number="<?php echo $key->number; ?>" data-status="<?php echo $key->status; ?>" data-remarks="<?php echo htmlspecialchars($key->remarks ?? ''); ?>"><i class="fa fa-refresh"></i> Change Status</a></li>
                                                             <?php endif; ?>
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
                                <label for="inputEmail3" class="col-sm-3 control-label">Copy to (CC)</label>
                                <div class="col-sm-8">
                                    <div style="border: 1px solid #d2d6de; border-radius: 4px; padding: 10px; background: #fafafa;">
                                        <div style="max-height: 140px; overflow-y: auto; padding-right: 5px; margin-bottom: 6px;">
                                            <?php if (!empty($set_cc_email)): ?>
                                                <div style="margin-bottom: 6px;">
                                                    <label style="font-weight: 600; cursor: pointer; margin-bottom: 0;">
                                                        <input type="checkbox" name="cc_emails[]" value="<?php echo htmlspecialchars($set_cc_email); ?>" checked>
                                                        <i class="fa fa-envelope-o text-primary"></i> <?php echo htmlspecialchars($set_cc_email); ?>
                                                        <small class="text-muted">(Default CC)</small>
                                                    </label>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($team_users)): ?>
                                                <?php foreach ($team_users as $t_user): ?>
                                                    <?php if (!empty($t_user['user_email']) && $t_user['user_email'] !== $set_cc_email): ?>
                                                        <div style="margin-bottom: 6px;">
                                                            <label style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                                <input type="checkbox" name="cc_emails[]" value="<?php echo htmlspecialchars($t_user['user_email']); ?>">
                                                                <?php echo htmlspecialchars($t_user['username']); ?> <small class="text-muted">(<?php echo htmlspecialchars($t_user['user_email']); ?>)</small>
                                                            </label>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                            <div id="so_custom_cc_container"></div>
                                        </div>

                                        <div style="padding-top: 8px; border-top: 1px dashed #ccc; display: flex; gap: 6px; align-items: center;">
                                            <input type="email" id="so_new_custom_cc_email" class="form-control input-sm" placeholder="Add custom CC email (e.g. name@company.com)" onkeydown="if(event.key==='Enter'){event.preventDefault();addSoCustomCcEmail();}">
                                            <button type="button" class="btn btn-sm btn-info" onclick="addSoCustomCcEmail()" style="white-space: nowrap;">
                                                <i class="fa fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                    <small class="help-block" style="margin-top: 3px; font-size: 11px; margin-bottom: 0;">Select team emails or add custom email addresses to CC on this Sales Order.</small>
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

    <!-- Change Status Modal -->
    <div id="soStatusModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header btn-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-refresh"></i> Change Status</h4>
                </div>
                <form method="post" action="<?php echo base_url(); ?>SalesOrderController/update_salesorder_status">
                    <div class="modal-body">
                        <input type="hidden" name="so_id" id="so_status_id">
                        <input type="hidden" name="so_number" id="so_status_number">
                        <div class="form-group">
                            <label>Status<span style="color:red;">*</span></label>
                            <select name="status" id="so_status_select" class="form-control" required>
                                <option value="1">Draft</option>
                                <option value="2">Submit for Approval (Under Process)</option>
                                <option value="3">Viewed</option>
                                <option value="5">Hold</option>
                                <option value="6">Canceled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remark / Note</label>
                            <textarea name="remarks" id="so_status_remarks" class="form-control" rows="2" placeholder="Enter status change remark..."></textarea>
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

            $(document).on('click', '.change-so-status-btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var number = $(this).data('number');
                var status = $(this).data('status');
                var remarks = $(this).data('remarks') || '';
                $('#so_status_id').val(id);
                $('#so_status_number').val(number);
                $('#so_status_select').val(status);
                $('#so_status_remarks').val(remarks);
                $('#soStatusModal').modal('show');
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

        function addSoCustomCcEmail() {
            var emailInput = $('#so_new_custom_cc_email');
            var emailVal = $.trim(emailInput.val());
            if (!emailVal) {
                alert('Please enter an email address.');
                return;
            }
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailVal)) {
                alert('Please enter a valid email address.');
                return;
            }
            var exists = false;
            $('input[name="cc_emails[]"]').each(function() {
                if ($(this).val().toLowerCase() === emailVal.toLowerCase()) {
                    exists = true;
                    $(this).prop('checked', true);
                }
            });
            if (exists) {
                emailInput.val('');
                return;
            }
            var html = '<div style="margin-bottom: 6px;">' +
                       '<label style="font-weight: normal; cursor: pointer; margin-bottom: 0;">' +
                       '<input type="checkbox" name="cc_emails[]" value="' + emailVal + '" checked> ' +
                       emailVal + ' <small class="text-info">(Custom)</small>' +
                       '</label>' +
                       '</div>';
            $('#so_custom_cc_container').append(html);
            emailInput.val('');
        }
    </script>