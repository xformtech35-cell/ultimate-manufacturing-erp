<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content-header">
                <h1>PO Amendment Details</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('PoamendmentController/index'); ?>">PO Amendments</a></li>
                    <li class="active">Amendment Details</li>
                </ol>
            </section>

            <section class="content">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i> Success!</h4>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Amendment Overview -->
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Amendment Overview</h3>
                                <div class="box-tools pull-right">
                                    <?php if ($amendment['status'] == 'draft'): ?>
                                        <a href="<?php echo base_url('PoamendmentController/edit/' . $amendment['amendment_id']); ?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a href="<?php echo base_url('PoamendmentController/delete/' . $amendment['amendment_id']); ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this amendment?');">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo base_url('PoamendmentController/index'); ?>" class="btn btn-default btn-sm">
                                        <i class="fa fa-list"></i> Back to List
                                    </a>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Amendment No</th>
                                                <td><strong><?php echo $amendment['amendment_no']; ?></strong></td>
                                            </tr>
                                            <tr>
                                                <th>PO Number</th>
                                                <td>
                                                    <a href="<?php echo base_url('SupplierController/show_po/' . $amendment['po_number']); ?>"
                                                        target="_blank" class="btn btn-xs btn-info">
                                                        <i class="fa fa-external-link"></i> <?php echo $amendment['po_number']; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Vendor</th>
                                                <td>
                                                    <?php if (!empty($amendment['po_details']['company_name'])): ?>
                                                        <?php echo $amendment['po_details']['company_name']; ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Amendment Type</th>
                                                <td>
                                                    <span class="label label-primary">
                                                        <?php echo ucfirst(str_replace('_', ' ', $amendment['amendment_type'])); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Initiated By</th>
                                                <td>
                                                    <?php echo !empty($amendment['initiated_by_name']) ? $amendment['initiated_by_name'] : 'User ID: ' . $amendment['initiated_by']; ?>
                                                    on <?php echo date('d-m-Y H:i', strtotime($amendment['initiated_date'])); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Status</th>
                                                <td>
                                                    <?php
                                                    $status_labels = array(
                                                        'draft' => 'label-default',
                                                        'pending_approval' => 'label-warning',
                                                        'approved' => 'label-info',
                                                        'vendor_acknowledged' => 'label-primary',
                                                        'revised_po_issued' => 'label-success',
                                                        'completed' => 'label-success',
                                                        'cancelled' => 'label-danger'
                                                    );
                                                    $label_class = isset($status_labels[$amendment['status']]) ? $status_labels[$amendment['status']] : 'label-default';
                                                    ?>
                                                    <span class="label <?php echo $label_class; ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $amendment['status'])); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Vendor Acknowledged</th>
                                                <td>
                                                    <?php if ($amendment['vendor_acknowledged'] == 1): ?>
                                                        <span class="label label-success">Yes</span>
                                                        by <?php echo $amendment['vendor_ack_by']; ?>
                                                        on <?php echo date('d-m-Y H:i', strtotime($amendment['vendor_ack_date'])); ?>
                                                    <?php else: ?>
                                                        <span class="label label-warning">No</span>
                                                        <?php if ($amendment['status'] == 'approved'): ?>
                                                            <a href="#vendorAckModal" data-toggle="modal" class="btn btn-xs btn-success">
                                                                <i class="fa fa-check"></i> Mark as Acknowledged
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Revised PO Issued</th>
                                                <td>
                                                    <?php if ($amendment['revised_po_issued'] == 1): ?>
                                                        <span class="label label-success">Yes</span>
                                                        <?php echo $amendment['revised_po_number']; ?>
                                                        on <?php echo date('d-m-Y H:i', strtotime($amendment['revised_po_date'])); ?>
                                                    <?php else: ?>
                                                        <span class="label label-warning">No</span>
                                                        <?php if ($amendment['status'] == 'vendor_acknowledged'): ?>
                                                            <a href="#revisedPOModal" data-toggle="modal" class="btn btn-xs btn-primary">
                                                                <i class="fa fa-file-text"></i> Issue Revised PO
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Amendment Value</th>
                                                <td>
                                                    <?php if (!empty($amendment['amendment_value']) && $amendment['amendment_value'] > 0): ?>
                                                        ₹<?php echo number_format($amendment['amendment_value'], 2); ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Attachment</th>
                                                <td>
                                                    <?php if (!empty($amendment['attachment'])): ?>
                                                        <a href="<?php echo base_url('uploads/amendments/' . $amendment['attachment']); ?>"
                                                            target="_blank" class="btn btn-xs btn-info">
                                                            <i class="fa fa-download"></i> Download Attachment
                                                        </a>
                                                    <?php else: ?>
                                                        No attachment
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Description and Reason -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Amendment Details</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <p class="well well-sm"><?php echo nl2br($amendment['description']); ?></p>
                                                </div>
                                                <div class="form-group">
                                                    <label>Reason for Amendment</label>
                                                    <p class="well well-sm"><?php echo nl2br($amendment['reason']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Changes Details -->
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Changes Details</h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($amendment['items'])): ?>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Field Type</th>
                                                <th>Field Name</th>
                                                <th>Old Value</th>
                                                <th>New Value</th>
                                                <th>Change Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1; ?>
                                            <?php foreach ($amendment['items'] as $item): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo ucfirst($item['field_type']); ?></td>
                                                    <td><?php echo $item['field_name']; ?></td>
                                                    <td><?php echo !empty($item['old_value']) ? $item['old_value'] : 'N/A'; ?></td>
                                                    <td><strong><?php echo $item['new_value']; ?></strong></td>
                                                    <td><?php echo $item['change_description']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-info">No change details recorded.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Status -->
                    <?php if (!empty($amendment['approvals'])): ?>
                        <div class="col-md-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Approval Status</h3>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Approval Level</th>
                                                <th>Approver Email</th>
                                                <th>Status</th>
                                                <th>Remarks</th>
                                                <th>Action Date</th>
                                                <th>Action By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($amendment['approvals'] as $approval): ?>
                                                <tr>
                                                    <td><?php echo ucfirst(str_replace('_', ' ', $approval['approval_level'])); ?></td>
                                                    <td><?php echo $approval['approver_email']; ?></td>
                                                    <td>
                                                        <?php if ($approval['status'] == 'approved'): ?>
                                                            <span class="label label-success">Approved</span>
                                                        <?php elseif ($approval['status'] == 'rejected'): ?>
                                                            <span class="label label-danger">Rejected</span>
                                                        <?php else: ?>
                                                            <span class="label label-warning">Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo !empty($approval['remarks']) ? $approval['remarks'] : 'N/A'; ?></td>
                                                    <td>
                                                        <?php if (!empty($approval['action_date'])): ?>
                                                            <?php echo date('d-m-Y H:i', strtotime($approval['action_date'])); ?>
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo !empty($approval['action_by']) ? $approval['action_by'] : 'N/A'; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <!-- Submit for Approval Button (for draft amendments) -->
                                    <?php if ($amendment['status'] == 'draft'): ?>
                                        <div class="text-center">
                                            <a href="#submitApprovalModal" data-toggle="modal" class="btn btn-success btn-lg">
                                                <i class="fa fa-paper-plane"></i> Submit for Approval
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons based on Status -->
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Actions</h3>
                            </div>
                            <div class="box-body">
                                <div class="text-center">
                                    <?php if ($amendment['status'] == 'draft'): ?>
                                        <a href="<?php echo base_url('PoamendmentController/edit/' . $amendment['amendment_id']); ?>"
                                            class="btn btn-warning">
                                            <i class="fa fa-edit"></i> Edit Amendment
                                        </a>
                                        <a href="#submitApprovalModal" data-toggle="modal" class="btn btn-success">
                                            <i class="fa fa-paper-plane"></i> Submit for Approval
                                        </a>
                                        <a href="<?php echo base_url('PoamendmentController/delete/' . $amendment['amendment_id']); ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this amendment?');">
                                            <i class="fa fa-trash"></i> Delete Amendment
                                        </a>
                                    <?php elseif ($amendment['status'] == 'approved' && $amendment['vendor_acknowledged'] == 0): ?>
                                        <a href="#vendorAckModal" data-toggle="modal" class="btn btn-success btn-lg">
                                            <i class="fa fa-check-circle"></i> Mark as Vendor Acknowledged
                                        </a>
                                    <?php elseif ($amendment['status'] == 'vendor_acknowledged' && $amendment['revised_po_issued'] == 0): ?>
                                        <a href="#revisedPOModal" data-toggle="modal" class="btn btn-primary btn-lg">
                                            <i class="fa fa-file-text"></i> Issue Revised PO
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?php echo base_url('PoamendmentController/index'); ?>" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- Submit for Approval Modal -->
    <div class="modal fade" id="submitApprovalModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="<?php echo base_url('PoamendmentController/submit_approval/' . $amendment['amendment_id']); ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Submit for Approval</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to submit this amendment for approval?</p>
                        <p><strong>Amendment No:</strong> <?php echo $amendment['amendment_no']; ?></p>
                        <div class="form-group">
                            <label for="amendment_value">Amendment Value (₹)</label>
                            <input type="number" name="amendment_value" class="form-control"
                                step="0.01" placeholder="Enter total value of changes" required>
                            <small class="text-muted">Required to determine approval workflow</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit for Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Vendor Acknowledgment Modal -->
    <div class="modal fade" id="vendorAckModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="<?php echo base_url('PoamendmentController/vendor_acknowledge/' . $amendment['amendment_id']); ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Vendor Acknowledgment</h4>
                    </div>
                    <div class="modal-body">
                        <p>Mark this amendment as vendor acknowledged.</p>
                        <p><strong>Amendment No:</strong> <?php echo $amendment['amendment_no']; ?></p>
                        <p><strong>Vendor:</strong>
                            <?php echo !empty($amendment['po_details']['company_name']) ? $amendment['po_details']['company_name'] : 'N/A'; ?>
                        </p>
                        <div class="form-group">
                            <label for="ack_by">Acknowledged By</label>
                            <input type="text" name="ack_by" class="form-control"
                                placeholder="Enter vendor representative name" required>
                        </div>
                        <div class="form-group">
                            <label for="ack_notes">Notes</label>
                            <textarea name="ack_notes" class="form-control" rows="3"
                                placeholder="Any additional notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark as Acknowledged</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Revised PO Modal -->
    <div class="modal fade" id="revisedPOModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="<?php echo base_url('PoamendmentController/update_revised_po/' . $amendment['amendment_id']); ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Issue Revised PO</h4>
                    </div>
                    <div class="modal-body">
                        <p>Issue a revised purchase order for this amendment.</p>
                        <p><strong>Original PO:</strong> <?php echo $amendment['po_number']; ?></p>
                        <div class="form-group">
                            <label for="revised_po_number">Revised PO Number</label>
                            <input type="text" name="revised_po_number" class="form-control"
                                placeholder="Enter revised PO number" required>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="create_new_po" id="create_new_po" value="1" class="form-check-input">
                            <label for="create_new_po" class="form-check-label">
                                Create new PO record in system
                            </label>
                            <small class="text-muted d-block">Check to create a new PO record based on this amendment</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Issue Revised PO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Auto-focus on first input in modals
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('input[type="text"]:first').focus();
            });

            // Show modal based on URL hash
            if (window.location.hash) {
                var hash = window.location.hash;
                if (hash === '#submitApprovalModal' || hash === '#vendorAckModal' || hash === '#revisedPOModal') {
                    $(hash).modal('show');
                }
            }
        });
    </script>
</body>