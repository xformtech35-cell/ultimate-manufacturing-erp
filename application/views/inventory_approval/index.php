<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    .table-responsive {
        overflow-x: auto !important;
    }
    .diff-table th {
        background-color: #f8fafc;
        font-weight: 600;
    }
    .diff-changed {
        background-color: #ecfdf5;
        font-weight: bold;
        color: #047857;
    }
    .diff-old {
        color: #64748b;
        text-decoration: line-through;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-check-square-o" style="color: #3b82f6;"></i> Inventory Approvals
            <small>Manage inventory update and deletion requests</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('Home/index') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= base_url('InventoryController/index') ?>"><i class="fa fa-archive"></i> Inventory</a></li>
            <li class="active">Inventory Approvals</li>
        </ol>
    </section>

    <section class="content">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fa fa-check"></i> <?= $this->session->flashdata('SUCCESSMSG') ?>
            </div>
        <?php } ?>

        <?php if ($this->session->flashdata('ERRORMSG')) { ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fa fa-ban"></i> <?= $this->session->flashdata('ERRORMSG') ?>
            </div>
        <?php } ?>

        <?php if ($this->session->flashdata('INFOMSG')) { ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fa fa-info"></i> <?= $this->session->flashdata('INFOMSG') ?>
            </div>
        <?php } ?>

        <!-- Per-User Approval Status Notifications -->
        <?php
        $user_id_curr = (int)($session_data_head1['result']['user_id'] ?? 0);
        if ($user_id_curr > 0 && $this->db->table_exists('user_notifications')) {
            $user_notifs = $this->db
                ->where('user_id', $user_id_curr)
                ->where('is_read', 0)
                ->order_by('created_at', 'DESC')
                ->limit(5)
                ->get('user_notifications')
                ->result_array();

            foreach ($user_notifs as $unotif) {
                $alert_class = ($unotif['type'] === 'success') ? 'alert-success' : (($unotif['type'] === 'error') ? 'alert-danger' : 'alert-info');
                $alert_icon  = ($unotif['type'] === 'success') ? 'fa-check-circle' : (($unotif['type'] === 'error') ? 'fa-times-circle' : 'fa-info-circle');
                ?>
                <div class="alert <?= $alert_class; ?> alert-dismissible" style="box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" onclick="jQuery.get('<?= base_url('InventoryApprovalController/mark_notification_read/' . $unotif['id']); ?>');">×</button>
                    <h4><i class="icon fa <?= $alert_icon; ?>"></i> <?= htmlspecialchars($unotif['title']); ?></h4>
                    <?= htmlspecialchars($unotif['message']); ?>
                    <span class="pull-right" style="font-size: 11px; opacity: 0.8;"><?= date('d M Y, h:i A', strtotime($unotif['created_at'])); ?></span>
                </div>
                <?php
            }
        }
        ?>

        <div class="row">
            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab_pending" data-toggle="tab">
                                <i class="fa fa-clock-o text-warning"></i> Pending Requests
                                <span class="label label-warning" style="margin-left: 5px;"><?= count($pending_requests); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="#tab_approved" data-toggle="tab">
                                <i class="fa fa-check-circle text-success"></i> Approved History
                                <span class="label label-success" style="margin-left: 5px;"><?= count($approved_requests); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="#tab_rejected" data-toggle="tab">
                                <i class="fa fa-times-circle text-danger"></i> Rejected History
                                <span class="label label-default" style="margin-left: 5px;"><?= count($rejected_requests); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="#tab_all_history" data-toggle="tab">
                                <i class="fa fa-history text-primary"></i> All History
                                <span class="label label-info" style="margin-left: 5px;"><?= count($all_history_requests); ?></span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" style="padding: 15px;">
                        <!-- TAB 1: PENDING REQUESTS -->
                        <div class="tab-pane active" id="tab_pending">
                            <?php if (empty($pending_requests)) { ?>
                                <div class="text-center" style="padding: 40px; color: #94a3b8;">
                                    <i class="fa fa-check-circle-o fa-3x" style="color: #cbd5e1; margin-bottom: 10px;"></i>
                                    <p style="font-size: 14px; font-weight: 500;">No pending inventory approval requests!</p>
                                </div>
                            <?php } else { ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover datatable-approval">
                                        <thead>
                                            <tr style="background-color: #f8fafc;">
                                                <th width="5%">#</th>
                                                <th width="12%">Request Type</th>
                                                <th width="15%">Item Code</th>
                                                <th width="20%">Item Name</th>
                                                <th width="15%">Requested By</th>
                                                <th width="13%">Date</th>
                                                <th width="20%">Actions / Comparison</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1; foreach ($pending_requests as $req) { ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td>
                                                        <?php if ($req['request_type'] === 'update') { ?>
                                                            <span class="label label-info"><i class="fa fa-edit"></i> Edit / Update</span>
                                                        <?php } else { ?>
                                                            <span class="label label-danger"><i class="fa fa-trash"></i> Delete</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td><strong><?= htmlspecialchars($req['item_code'] ?: 'N/A'); ?></strong></td>
                                                    <td><?= htmlspecialchars($req['item_name'] ?: 'N/A'); ?></td>
                                                    <td>
                                                        <i class="fa fa-user text-muted"></i> <?= htmlspecialchars($req['requested_by_name'] ?: 'User #' . $req['requested_by']); ?>
                                                    </td>
                                                    <td><?= date('d-m-Y H:i', strtotime($req['created_at'])); ?></td>
                                                    <td>
                                                        <?php if ($req['request_type'] === 'update') { ?>
                                                            <button type="button" class="btn btn-xs btn-default" data-toggle="modal" data-target="#modal-diff-<?= $req['id']; ?>">
                                                                <i class="fa fa-eye text-primary"></i> View Changes
                                                            </button>
                                                        <?php } else { ?>
                                                            <span class="text-danger" style="font-size: 11px;">
                                                                <i class="fa fa-info-circle"></i> <?= htmlspecialchars($req['reason'] ?: 'Item Deletion Request'); ?>
                                                            </span>
                                                        <?php } ?>

                                                        <?php if (!empty($is_admin)) { ?>
                                                            <a href="<?= base_url('InventoryApprovalController/approve/' . $req['id']); ?>" 
                                                               class="btn btn-xs btn-success" style="margin-left: 5px;"
                                                               onclick="return confirm('Are you sure you want to APPROVE this inventory request?');">
                                                                <i class="fa fa-check"></i> Approve
                                                            </a>

                                                            <button type="button" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-reject-<?= $req['id']; ?>">
                                                                <i class="fa fa-times"></i> Reject
                                                            </button>
                                                        <?php } else { ?>
                                                            <span class="label label-warning" style="margin-left: 5px;"><i class="fa fa-clock-o"></i> Pending Admin Approval</span>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Modals placed cleanly outside table DOM hierarchy -->
                                <?php foreach ($pending_requests as $req) { ?>
                                    <!-- Modal: View Field Diff -->
                                    <?php if ($req['request_type'] === 'update') { 
                                        $old_data = json_decode($req['old_data'], true) ?: [];
                                        $new_data = json_decode($req['new_data'], true) ?: [];
                                    ?>
                                        <div class="modal fade" id="modal-diff-<?= $req['id']; ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #3b82f6; color: white;">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title"><i class="fa fa-edit"></i> Requested Field Changes: <?= htmlspecialchars($req['item_name']); ?> (<?= htmlspecialchars($req['item_code']); ?>)</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row" style="margin-bottom: 10px;">
                                                            <div class="col-sm-6">
                                                                <strong>Requested By:</strong> <?= htmlspecialchars($req['requested_by_name']); ?>
                                                            </div>
                                                            <div class="col-sm-6 text-right">
                                                                <strong>Date:</strong> <?= date('d-m-Y H:i:s', strtotime($req['created_at'])); ?>
                                                            </div>
                                                        </div>

                                                        <table class="table table-bordered diff-table">
                                                            <thead>
                                                                <tr>
                                                                    <th width="30%">Field Name</th>
                                                                    <th width="35%">Old Value</th>
                                                                    <th width="35%">New Requested Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                $fields = ['item_name' => 'Item Name', 'code' => 'Item Code', 'prod_description' => 'Description', 'hsn' => 'HSN/SAC', 'unit' => 'Unit', 'item_type' => 'Type', 'gst_per' => 'GST %', 'stock' => 'Stock Quantity', 'cost_price' => 'Cost Price (₹)', 'sell_price' => 'Sell Price (₹)'];
                                                                foreach ($fields as $key => $label) {
                                                                    $old_val = $old_data[$key] ?? '';
                                                                    $new_val = $new_data[$key] ?? '';
                                                                    $is_changed = (string)$old_val !== (string)$new_val;
                                                                ?>
                                                                    <tr class="<?= $is_changed ? 'diff-changed' : ''; ?>">
                                                                        <td><strong><?= $label; ?></strong></td>
                                                                        <td class="<?= $is_changed ? 'diff-old' : ''; ?>"><?= htmlspecialchars((string)$old_val !== '' ? $old_val : 'N/A'); ?></td>
                                                                        <td>
                                                                            <?= htmlspecialchars((string)$new_val !== '' ? $new_val : 'N/A'); ?>
                                                                            <?php if ($is_changed) { ?>
                                                                                <span class="label label-success pull-right"><i class="fa fa-pencil"></i> Modified</span>
                                                                            <?php } ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="<?= base_url('InventoryApprovalController/approve/' . $req['id']); ?>" class="btn btn-success" onclick="return confirm('Approve these inventory changes?');"><i class="fa fa-check"></i> Approve Changes</a>
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <!-- Modal: Reject Remarks -->
                                    <div class="modal fade" id="modal-reject-<?= $req['id']; ?>" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <form method="post" action="<?= base_url('InventoryApprovalController/reject/' . $req['id']); ?>">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #ef4444; color: white;">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title"><i class="fa fa-times-circle"></i> Reject Inventory Request</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reject the <strong><?= $req['request_type']; ?></strong> request for item <strong><?= htmlspecialchars($req['item_name']); ?></strong>?</p>
                                                        <div class="form-group">
                                                            <label>Rejection Reason / Remarks:</label>
                                                            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> Reject Request</button>
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php } ?>

                            <?php } ?>
                        </div>

                        <!-- TAB 2: APPROVED HISTORY -->
                        <div class="tab-pane" id="tab_approved">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable-approval">
                                    <thead>
                                        <tr style="background-color: #f8fafc;">
                                            <th width="5%">#</th>
                                            <th width="12%">Type</th>
                                            <th width="15%">Item Code</th>
                                            <th width="20%">Item Name</th>
                                            <th width="15%">Requested By</th>
                                            <th width="15%">Approved By</th>
                                            <th width="18%">Approved Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach ($approved_requests as $req) { ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td>
                                                    <span class="label label-success"><i class="fa fa-check"></i> <?= strtoupper($req['request_type']); ?></span>
                                                </td>
                                                <td><strong><?= htmlspecialchars($req['item_code'] ?: 'N/A'); ?></strong></td>
                                                <td><?= htmlspecialchars($req['item_name'] ?: 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($req['requested_by_name'] ?: 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($req['reviewed_by_name'] ?: 'Admin'); ?></td>
                                                <td><?= date('d-m-Y H:i', strtotime($req['updated_at'] ?: $req['created_at'])); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 3: REJECTED HISTORY -->
                        <div class="tab-pane" id="tab_rejected">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable-approval">
                                    <thead>
                                        <tr style="background-color: #f8fafc;">
                                            <th width="5%">#</th>
                                            <th width="12%">Type</th>
                                            <th width="15%">Item Code</th>
                                            <th width="20%">Item Name</th>
                                            <th width="15%">Requested By</th>
                                            <th width="15%">Rejected By</th>
                                            <th width="18%">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach ($rejected_requests as $req) { ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td>
                                                    <span class="label label-danger"><i class="fa fa-times"></i> <?= strtoupper($req['request_type']); ?></span>
                                                </td>
                                                <td><strong><?= htmlspecialchars($req['item_code'] ?: 'N/A'); ?></strong></td>
                                                <td><?= htmlspecialchars($req['item_name'] ?: 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($req['requested_by_name'] ?: 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($req['reviewed_by_name'] ?: 'Admin'); ?></td>
                                                <td><?= htmlspecialchars($req['review_remarks'] ?: 'No remarks'); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <!-- TAB 4: ALL HISTORY -->
                        <div class="tab-pane" id="tab_all_history">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable-approval">
                                    <thead>
                                        <tr style="background-color: #f8fafc;">
                                            <th width="5%">#</th>
                                            <th width="12%">Request Type</th>
                                            <th width="15%">Item Code</th>
                                            <th width="20%">Item Name</th>
                                            <th width="15%">Requested By</th>
                                            <th width="12%">Status</th>
                                            <th width="13%">Reviewed By</th>
                                            <th width="18%">Remarks / Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach ($all_history_requests as $req) { ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td>
                                                    <?php if ($req['request_type'] === 'update') { ?>
                                                        <span class="label label-info"><i class="fa fa-edit"></i> EDIT</span>
                                                    <?php } else { ?>
                                                        <span class="label label-danger"><i class="fa fa-trash"></i> DELETE</span>
                                                    <?php } ?>
                                                </td>
                                                <td><strong><?= htmlspecialchars($req['item_code'] ?: 'N/A'); ?></strong></td>
                                                <td><?= htmlspecialchars($req['item_name'] ?: 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($req['requested_by_name'] ?: 'N/A'); ?></td>
                                                <td>
                                                    <?php if ($req['status'] === 'approved') { ?>
                                                        <span class="label label-success"><i class="fa fa-check"></i> APPROVED</span>
                                                    <?php } else { ?>
                                                        <span class="label label-danger"><i class="fa fa-times"></i> REJECTED</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?= htmlspecialchars($req['reviewed_by_name'] ?: 'Admin'); ?></td>
                                                <td>
                                                    <?= htmlspecialchars($req['review_remarks'] ?: $req['reason'] ?: 'No remarks'); ?>
                                                    <br><small class="text-muted"><?= date('d-m-Y H:i', strtotime($req['updated_at'] ?: $req['created_at'])); ?></small>
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
        </div>
    </section>
</div>
