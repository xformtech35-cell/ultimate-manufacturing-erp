<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
    exit;
}
defined('BASEPATH') or exit('No direct script access allowed');

$status_filter = $status_filter ?? 'pending';
$count_pending = $count_pending ?? 0;
$count_approved = $count_approved ?? 0;
$count_hold_canceled = $count_hold_canceled ?? 0;
$count_all = $count_all ?? 0;
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center" style="display:none;"></div>
    <div class="wrapper">

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <h1>
                    <i class="fa fa-check-square-o text-blue"></i> Sales Order Approval Dashboard
                    <small>Review, Approve, Hold & Manage Sales Orders</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('Home/index/'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('SalesOrderController/index'); ?>">Sales Order</a></li>
                    <li class="active">Approval Dashboard</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Flash Messages -->
                <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong><i class="fa fa-check-circle"></i> Success!</strong> <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('ERRORMSG')): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong><i class="fa fa-exclamation-triangle"></i> Error!</strong> <?php echo $this->session->flashdata('ERRORMSG'); ?>
                    </div>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-aqua" style="border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <span class="info-box-icon" style="border-radius: 6px 0 0 6px;"><i class="fa fa-clock-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-weight: 600;">Pending Approval</span>
                                <span class="info-box-number" style="font-size: 24px;"><?php echo $count_pending; ?></span>
                                <div class="progress" style="height: 3px; margin: 5px 0;">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">Awaiting Manager / Admin Review</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-green" style="border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <span class="info-box-icon" style="border-radius: 6px 0 0 6px;"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-weight: 600;">Approved</span>
                                <span class="info-box-number" style="font-size: 24px;"><?php echo $count_approved; ?></span>
                                <div class="progress" style="height: 3px; margin: 5px 0;">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">Approved Sales Orders</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-yellow" style="border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <span class="info-box-icon" style="border-radius: 6px 0 0 6px;"><i class="fa fa-pause"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-weight: 600;">Hold / Canceled</span>
                                <span class="info-box-number" style="font-size: 24px;"><?php echo $count_hold_canceled; ?></span>
                                <div class="progress" style="height: 3px; margin: 5px 0;">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">Held or Rejected Orders</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-purple" style="border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <span class="info-box-icon" style="border-radius: 6px 0 0 6px;"><i class="fa fa-folder-open"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="font-weight: 600;">Total Orders</span>
                                <span class="info-box-number" style="font-size: 24px;"><?php echo $count_all; ?></span>
                                <div class="progress" style="height: 3px; margin: 5px 0;">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">All Registered Sales Orders</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Matrix Rules Info -->
                <div class="box box-solid box-primary collapsed-box" style="border-radius: 6px;">
                    <div class="box-header with-border" style="cursor: pointer;" data-widget="collapse">
                        <h3 class="box-title"><i class="fa fa-sitemap"></i> Sales Order Approval Matrix Rules</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="box-body" style="display: none;">
                        <?php if (empty($so_matrix_rules)): ?>
                            <p class="text-muted"><i class="fa fa-info-circle text-blue"></i> No specific Sales Order rules configured in Approval Matrix. Orders can be approved by Admin or users with SO Approval permissions.</p>
                        <?php else: ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr style="background:#f8fafc;">
                                        <th>Level</th>
                                        <th>Approver Role</th>
                                        <th>Min Amount</th>
                                        <th>Max Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($so_matrix_rules as $rule): ?>
                                        <tr>
                                            <td><span class="badge bg-blue">Level <?php echo $rule->level; ?></span></td>
                                            <td><b><?php echo htmlspecialchars($rule->approver_role); ?></b></td>
                                            <td>₹<?php echo number_format($rule->min_amount, 2); ?></td>
                                            <td>₹<?php echo $rule->max_amount > 0 ? number_format($rule->max_amount, 2) : 'Unlimited'; ?></td>
                                            <td><span class="label label-success"><?php echo ucfirst($rule->status); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                        <a href="<?php echo base_url('ApprovalMatrixController'); ?>" class="btn btn-primary btn-xs" target="_blank"><i class="fa fa-cog"></i> Manage Approval Matrix Settings</a>
                    </div>
                </div>

                <!-- Main Data Box -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info" style="border-radius: 6px;">
                            
                            <div class="box-header with-border" style="padding: 12px 15px;">
                                <h3 class="box-title" style="font-weight: 600; font-size: 16px;">
                                    <i class="fa fa-list-alt text-blue"></i> Sales Orders Approvals Dashboard
                                </h3>
                                <div class="pull-right">
                                    <a href="<?php echo base_url('SalesOrderController/index'); ?>" class="btn btn-default btn-sm" style="font-weight: 600;">
                                        <i class="fa fa-arrow-left"></i> Back to Sales Orders List
                                    </a>
                                </div>
                            </div>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs" style="background: #f8fafc; padding: 5px 10px 0 10px;">
                                <li class="<?php echo ($status_filter === 'pending') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('SalesOrderController/so_approval_dashboard?status=pending'); ?>" style="font-weight: 600;">
                                        <i class="fa fa-clock-o text-yellow"></i> Pending Approvals <span class="badge bg-yellow"><?php echo $count_pending; ?></span>
                                    </a>
                                </li>
                                <li class="<?php echo ($status_filter === 'approved') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('SalesOrderController/so_approval_dashboard?status=approved'); ?>" style="font-weight: 600;">
                                        <i class="fa fa-check-circle text-green"></i> Approved <span class="badge bg-green"><?php echo $count_approved; ?></span>
                                    </a>
                                </li>
                                <li class="<?php echo ($status_filter === 'hold_canceled') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('SalesOrderController/so_approval_dashboard?status=hold_canceled'); ?>" style="font-weight: 600;">
                                        <i class="fa fa-pause-circle text-red"></i> On Hold / Canceled <span class="badge bg-red"><?php echo $count_hold_canceled; ?></span>
                                    </a>
                                </li>
                                <li class="<?php echo ($status_filter === 'all') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('SalesOrderController/so_approval_dashboard?status=all'); ?>" style="font-weight: 600;">
                                        <i class="fa fa-globe text-blue"></i> All Orders <span class="badge bg-blue"><?php echo $count_all; ?></span>
                                    </a>
                                </li>
                            </ul>

                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="so_approval_table" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
                                        <thead>
                                            <tr style="background:#f1f5f9; color: #334155;">
                                                <th style="width: 4%;">Sr.No.</th>
                                                <th style="width: 8%;">Status</th>
                                                <th style="width: 8%;">Date</th>
                                                <th style="width: 12%;">SO Number</th>
                                                <th style="width: 12%;">Customer Name</th>
                                                <th style="width: 12%;">Company Name</th>
                                                <th style="width: 6%;">Type</th>
                                                <th style="width: 9%;">Amount</th>
                                                <th style="width: 9%;">Created By</th>
                                                <th style="width: 10%;">Status / Approver</th>
                                                <th style="width: 10%;">Remarks</th>
                                                <th style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $i = 1;
                                            if (!empty($salesorders)) {
                                                foreach ($salesorders as $key) { 
                                                    $status_class = 'label-default';
                                                    $status_text = 'Draft';
                                                    switch ($key->status) {
                                                        case 1:
                                                            $status_class = 'label-default';
                                                            $status_text = 'Draft';
                                                            break;
                                                        case 2:
                                                            $status_class = 'label-info';
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
                                                            $status_class = 'label-warning';
                                                            $status_text = 'Hold';
                                                            break;
                                                        case 6:
                                                            $status_class = 'label-danger';
                                                            $status_text = 'Canceled';
                                                            break;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><span class="label <?php echo $status_class; ?>" style="font-size: 11px; font-weight: 500;"><?php echo $status_text; ?></span></td>
                                                        <td>
                                                            <?php
                                                            if (!empty($key->date) && $key->date !== '0000-00-00') {
                                                                echo date('d-m-Y', strtotime($key->date));
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href="<?php echo base_url() . 'SalesOrderController/show_salesorder/' . $key->id; ?>" style="font-weight: bold;">
                                                                <?php echo htmlspecialchars($key->number); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($key->fullname ?? '-'); ?></td>
                                                        <td><?php echo htmlspecialchars($key->customer_name ?? '-'); ?></td>
                                                        <td><?php echo ($key->gst_type != 'I') ? 'CGST/SGST' : 'IGST'; ?></td>
                                                        <td><b>₹<?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->total), 0); ?></b></td>
                                                        <td><span class="label label-default"><?php echo !empty($key->created_by_name) ? htmlspecialchars($key->created_by_name) : 'Admin'; ?></span></td>
                                                        <td>
                                                            <?php
                                                            $handler_name = !empty($key->approved_by_name) ? htmlspecialchars($key->approved_by_name) : (!empty($key->created_by_name) ? htmlspecialchars($key->created_by_name) : 'Admin');
                                                            switch ($key->status) {
                                                                case 4:
                                                                    echo '<span class="label label-success">Approved by: ' . $handler_name . '</span>';
                                                                    break;
                                                                case 5:
                                                                    echo '<span class="label label-warning">Held by: ' . $handler_name . '</span>';
                                                                    break;
                                                                case 6:
                                                                    echo '<span class="label label-danger">Canceled by: ' . $handler_name . '</span>';
                                                                    break;
                                                                case 2:
                                                                    echo '<span class="label label-info">Sent by: ' . $handler_name . '</span>';
                                                                    break;
                                                                default:
                                                                    echo '-';
                                                                    break;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><small><?php echo !empty($key->remarks) ? htmlspecialchars($key->remarks) : '-'; ?></small></td>
                                                        <td>
                                                            <div style="display: flex; gap: 4px; align-items: center;">
                                                                <!-- Action Buttons for Approver -->
                                                                <?php if (in_array($key->status, [0, 1, 2, 3])): ?>
                                                                    <button type="button" class="btn btn-success btn-xs btn-approve-so" data-id="<?php echo $key->id; ?>" data-number="<?php echo $key->number; ?>" title="Approve SO">
                                                                        <i class="fa fa-check"></i> Approve
                                                                    </button>
                                                                    <button type="button" class="btn btn-warning btn-xs btn-hold-so" data-id="<?php echo $key->id; ?>" data-number="<?php echo $key->number; ?>" title="Hold SO">
                                                                        <i class="fa fa-pause"></i> Hold
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-xs btn-cancel-so" data-id="<?php echo $key->id; ?>" data-number="<?php echo $key->number; ?>" title="Cancel SO">
                                                                        <i class="fa fa-times"></i> Reject
                                                                    </button>
                                                                <?php endif; ?>

                                                                <div class="dropdown" style="display: inline-block;">
                                                                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                                                        <i class="fa fa-ellipsis-v"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu pull-right" role="menu">
                                                                        <li><a href="<?php echo base_url() . 'SalesOrderController/show_salesorder/' . $key->id; ?>"><i class="fa fa-eye"></i> View Details</a></li>
                                                                        <li><a href="<?php echo base_url() . 'SalesOrderController/edit_salesorder_details/' . $key->id; ?>"><i class="fa fa-edit"></i> Edit</a></li>
                                                                        <li><a href="<?php echo base_url() . 'Pdf/print_igst_salesorder/' . $key->id; ?>" target="_blank"><i class="fa fa-file-pdf-o"></i> Export PDF</a></li>
                                                                        <li><a href="<?php echo base_url() . 'SalesOrderController/export_salesorder_excel/' . $key->id; ?>"><i class="fa fa-file-excel-o"></i> Export Excel</a></li>
                                                                        <li role="presentation" class="divider"></li>
                                                                        <li><a class="change-so-status-btn" href="#" data-id="<?php echo $key->id; ?>" data-number="<?php echo $key->number; ?>" data-status="<?php echo $key->status; ?>" data-remarks="<?php echo htmlspecialchars($key->remarks ?? ''); ?>"><i class="fa fa-refresh"></i> Change Status</a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php 
                                                    $i++;
                                                }
                                            } else { ?>
                                                <tr>
                                                    <td colspan="12" class="text-center" style="padding: 30px; color: #888;">
                                                        <i class="fa fa-info-circle" style="font-size: 24px;"></i><br>
                                                        No Sales Orders found for this view filter.
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
    </div>

    <!-- Single Action Modal for Approval / Status Change -->
    <div id="soApprovalActionModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header" id="action_modal_header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="action_modal_title">Process Sales Order</h4>
                </div>
                <form method="post" action="<?php echo base_url('SalesOrderController/update_salesorder_status'); ?>">
                    <div class="modal-body">
                        <input type="hidden" name="so_id" id="action_so_id">
                        <input type="hidden" name="so_number" id="action_so_number">
                        <input type="hidden" name="status" id="action_so_status">
                        <input type="hidden" name="redirect_to" value="so_approval_dashboard">

                        <p id="action_modal_msg" style="font-size: 13px; font-weight: 600;"></p>
                        
                        <div class="form-group">
                            <label>Remark / Notes</label>
                            <textarea name="remarks" id="action_so_remarks" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="action_modal_btn" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Submit</button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generic Change Status Modal -->
    <div id="soStatusModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header btn-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-refresh"></i> Change Status</h4>
                </div>
                <form method="post" action="<?php echo base_url('SalesOrderController/update_salesorder_status'); ?>">
                    <div class="modal-body">
                        <input type="hidden" name="so_id" id="so_status_id">
                        <input type="hidden" name="so_number" id="so_status_number">
                        <input type="hidden" name="redirect_to" value="so_approval_dashboard">
                        <div class="form-group">
                            <label>Select Status <span style="color:red;">*</span></label>
                            <select name="status" id="so_status_select" class="form-control" required>
                                <option value="1">Draft</option>
                                <option value="2">Sent (Pending Approval)</option>
                                <option value="3">Viewed</option>
                                <option value="4">Approved</option>
                                <option value="5">Hold</option>
                                <option value="6">Canceled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remark / Note</label>
                            <textarea name="remarks" id="so_status_remarks" class="form-control" rows="2" placeholder="Enter remark..."></textarea>
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
        $(document).ready(function() {
            // Quick Approve Button
            $(document).on('click', '.btn-approve-so', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var number = $(this).data('number');
                $('#action_so_id').val(id);
                $('#action_so_number').val(number);
                $('#action_so_status').val(4); // Approved
                $('#action_modal_header').attr('class', 'modal-header btn-success');
                $('#action_modal_title').html('<i class="fa fa-check"></i> Approve Sales Order');
                $('#action_modal_msg').html('Are you sure you want to <b>APPROVE</b> Sales Order <b>' + number + '</b>?');
                $('#action_modal_btn').attr('class', 'btn btn-success btn-sm').html('<i class="fa fa-check"></i> Approve SO');
                $('#soApprovalActionModal').modal('show');
            });

            // Quick Hold Button
            $(document).on('click', '.btn-hold-so', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var number = $(this).data('number');
                $('#action_so_id').val(id);
                $('#action_so_number').val(number);
                $('#action_so_status').val(5); // Hold
                $('#action_modal_header').attr('class', 'modal-header btn-warning');
                $('#action_modal_title').html('<i class="fa fa-pause"></i> Place Sales Order On Hold');
                $('#action_modal_msg').html('Are you sure you want to place Sales Order <b>' + number + '</b> ON HOLD?');
                $('#action_modal_btn').attr('class', 'btn btn-warning btn-sm').html('<i class="fa fa-pause"></i> Hold SO');
                $('#soApprovalActionModal').modal('show');
            });

            // Quick Cancel / Reject Button
            $(document).on('click', '.btn-cancel-so', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var number = $(this).data('number');
                $('#action_so_id').val(id);
                $('#action_so_number').val(number);
                $('#action_so_status').val(6); // Canceled
                $('#action_modal_header').attr('class', 'modal-header btn-danger');
                $('#action_modal_title').html('<i class="fa fa-times"></i> Reject / Cancel Sales Order');
                $('#action_modal_msg').html('Are you sure you want to <b>CANCEL / REJECT</b> Sales Order <b>' + number + '</b>?');
                $('#action_modal_btn').attr('class', 'btn btn-danger btn-sm').html('<i class="fa fa-times"></i> Reject SO');
                $('#soApprovalActionModal').modal('show');
            });

            // Change status button modal trigger
            $(document).on('click', '.change-so-status-btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var number = $(this).data('number');
                var status = $(this).data('status');
                var remarks = $(this).data('remarks');

                $('#so_status_id').val(id);
                $('#so_status_number').val(number);
                $('#so_status_select').val(status);
                $('#so_status_remarks').val(remarks);
                $('#soStatusModal').modal('show');
            });
        });
    </script>
</body>