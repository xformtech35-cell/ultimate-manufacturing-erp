<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="content-wrapper">
            <section class="content-header">
                <h1>GRN Approvals</h1>
                <ol class="breadcrumb">
                    <li><a href="<?= base_url('Home/index/') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">GRN Approvals</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#pending" data-toggle="tab"><i class="fa fa-clock-o"></i> Pending Approvals <span class="badge bg-red"><?= $total_pending ?? 0 ?></span></a></li>
                                <li><a href="#history" data-toggle="tab"><i class="fa fa-history"></i> Approval History <span class="badge bg-blue"><?= $total_history ?? 0 ?></span></a></li>
                            </ul>
                            <div class="tab-content">

                                <!-- Pending Approvals Tab -->
                                <div class="tab-pane active" id="pending">
                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Pending GRN Approvals</h3>
                                        </div>
                                        <div class="box-body">
                                            <?php if (empty($pending_approvals)): ?>
                                                <div class="alert alert-info text-center">
                                                    <i class="fa fa-info-circle"></i> No pending GRN approvals at the moment.
                                                </div>
                                            <?php else: ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>GRN Number</th>
                                                                <th>Supplier</th>
                                                                <th>PO Number</th>
                                                                <th>Date</th>
                                                                <th>Amount (₹)</th>
                                                                <th>Approval Level</th>
                                                                <th>Approver Role</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $i = 1;
                                                            foreach ($pending_approvals as $approval): ?>
                                                                <tr>
                                                                    <td><?= $i++ ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url('GrnController/view_grn_details/' . str_replace('/', '-', $approval['grn_number'])) ?>"
                                                                            class="text-primary" title="View GRN Details">
                                                                            <i class="fa fa-eye"></i> <?= html_escape($approval['grn_number'] ?? 'N/A') ?>
                                                                        </a>
                                                                    </td>
                                                                    <td><?= html_escape(!empty($approval['supplier_name']) ? $approval['supplier_name'] : 'N/A') ?></td>
                                                                    <td><?= html_escape(!empty($approval['po_number_fk']) ? $approval['po_number_fk'] : 'N/A') ?></td>
                                                                    <td>
                                                                        <?php
                                                                        if (!empty($approval['date']) && $approval['date'] !== '0000-00-00' && $approval['date'] !== null) {
                                                                            echo date('d-m-Y', strtotime($approval['date']));
                                                                        } else {
                                                                            echo 'N/A';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <?php
                                                                        $amount = $approval['total'] ?? 0;
                                                                        echo '₹' . number_format((float)$amount, 2);
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        $level = $approval['approval_level'] ?? '';
                                                                        $level_text = str_replace('_', ' ', $level);
                                                                        echo '<span class="label label-warning">' . ucfirst($level_text) . '</span>';
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?= html_escape($approval['approver_role'] ?? 'Approver') ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="btn-group">
                                                                             <a href="<?= base_url('GrnController/view_grn_details/' . str_replace('/', '-', $approval['grn_number'])) ?>"
                                                                                 class="btn btn-xs btn-info" title="View Details">
                                                                                 <i class="fa fa-eye"></i> View
                                                                             </a>
                                                                             <a href="<?= base_url('GrnController/conduct_inspection?grn=' . urlencode($approval['grn_number'])) ?>"
                                                                                 class="btn btn-xs btn-warning" title="Conduct Inspection">
                                                                                 <i class="fa fa-clipboard"></i> Inspect
                                                                             </a>
                                                                             <?php if (isset($approval['approval_id'])): ?>
                                                                                 <button class="btn btn-xs btn-success approve-btn"
                                                                                     data-id="<?= $approval['approval_id'] ?>"
                                                                                     data-grn="<?= html_escape($approval['grn_number']) ?>"
                                                                                     data-role="<?= html_escape($approval['approver_role'] ?? 'Approver') ?>">
                                                                                     <i class="fa fa-check"></i> Approve
                                                                                 </button>
                                                                                 <button class="btn btn-xs btn-danger reject-btn"
                                                                                     data-id="<?= $approval['approval_id'] ?>"
                                                                                     data-grn="<?= html_escape($approval['grn_number']) ?>"
                                                                                     data-role="<?= html_escape($approval['approver_role'] ?? 'Approver') ?>">
                                                                                     <i class="fa fa-times"></i> Reject
                                                                                 </button>
                                                                             <?php endif; ?>
                                                                         </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($pending_approvals)): ?>
                                            <div class="box-footer">
                                                <small class="text-muted">Showing <?= count($pending_approvals) ?> pending approval(s)</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Approval History Tab -->
                                <div class="tab-pane" id="history">
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">GRN Approval History</h3>
                                            <div class="box-tools pull-right">
                                                <a href="<?= base_url('GrnController/export_grn_approval_report') ?>" class="btn btn-sm btn-success">
                                                    <i class="fa fa-download"></i> Export Report
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <?php if (empty($approval_history)): ?>
                                                <div class="alert alert-info text-center">
                                                    <i class="fa fa-info-circle"></i> No GRN approval history found.
                                                </div>
                                            <?php else: ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>GRN Number</th>
                                                                <th>Supplier</th>
                                                                <th>PO Number</th>
                                                                <th>Amount (₹)</th>
                                                                <th>Approval Level</th>
                                                                <th>Status</th>
                                                                <th>Action Date</th>
                                                                <th>Remarks</th>
                                                                <th>Action By</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $i = 1;
                                                            foreach ($approval_history as $history): ?>
                                                                <tr>
                                                                    <td><?= $i++ ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url('GrnController/show_grn_approval_details/' . str_replace('/', '-', $history['grn_number'])) ?>"
                                                                            class="text-primary" title="View Details">
                                                                            <i class="fa fa-eye"></i> <?= html_escape($history['grn_number'] ?? 'N/A') ?>
                                                                        </a>
                                                                    </td>
                                                                    <td><?= html_escape(!empty($history['supplier_name']) ? $history['supplier_name'] : 'N/A') ?></td>
                                                                    <td><?= html_escape(!empty($history['po_number_fk']) ? $history['po_number_fk'] : 'N/A') ?></td>
                                                                    <td class="text-right">
                                                                        <?php
                                                                        $amount = $history['total'] ?? 0;
                                                                        echo '₹' . number_format((float)$amount, 2);
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        $level = $history['approval_level'] ?? '';
                                                                        $level_text = str_replace('_', ' ', $level);
                                                                        echo ucfirst($level_text);
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <span class="label label-<?= ($history['status'] ?? '') == 'approved' ? 'success' : 'danger' ?>">
                                                                            <?= ucfirst($history['status'] ?? 'pending') ?>
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        $action_date = $history['action_date'] ?? $history['created_at'] ?? '';
                                                                        if (!empty($action_date) && $action_date !== '0000-00-00 00:00:00' && $action_date !== null) {
                                                                            echo date('d-m-Y H:i', strtotime($action_date));
                                                                        } else {
                                                                            echo 'N/A';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <small><?= html_escape(!empty($history['remarks']) ? $history['remarks'] : 'No remarks') ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if (!empty($history['action_by_name'])) {
                                                                            echo html_escape($history['action_by_name']) . (!empty($history['action_by_role']) ? ' (' . html_escape($history['action_by_role']) . ')' : '');
                                                                        } else {
                                                                            echo html_escape(!empty($history['action_by']) ? $history['action_by'] : 'System');
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($approval_history)): ?>
                                            <div class="box-footer">
                                                <small class="text-muted">Showing <?= count($approval_history) ?> approval(s)</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Approval Modal -->
        <div class="modal fade" id="approvalModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="approvalForm" method="post" action="<?= base_url('GrnController/process_grn_approval') ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title" id="modalTitle">Process GRN Approval</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="approval_id" id="approval_id">
                            <input type="hidden" name="action" id="action">
                            <input type="hidden" name="user_email" value="<?= $this->session->userdata('session_data_head')['result']['user_email'] ?? '' ?>">

                            <div class="form-group">
                                <label>GRN Number</label>
                                <input type="text" class="form-control" id="grn_number" readonly>
                            </div>
                            <div class="form-group">
                                <label>Approver Role</label>
                                <input type="text" class="form-control" id="approver_role" readonly>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3"
                                    placeholder="Please provide remarks for your decision..." required></textarea>
                                <small class="text-muted">Remarks are required for audit trail.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Approve button click
                $('.approve-btn').click(function() {
                    $('#approval_id').val($(this).data('id'));
                    $('#action').val('approved');
                    $('#grn_number').val($(this).data('grn'));
                    $('#approver_role').val($(this).data('role'));
                    $('#modalTitle').html('<i class="fa fa-check"></i> Approve GRN: ' + $(this).data('grn'));
                    $('#approvalModal').modal('show');
                    $('#remarks').focus();
                });

                // Reject button click
                $('.reject-btn').click(function() {
                    $('#approval_id').val($(this).data('id'));
                    $('#action').val('rejected');
                    $('#grn_number').val($(this).data('grn'));
                    $('#approver_role').val($(this).data('role'));
                    $('#modalTitle').html('<i class="fa fa-times"></i> Reject GRN: ' + $(this).data('grn'));
                    $('#approvalModal').modal('show');
                    $('#remarks').focus();
                });

                // Form submission
                $('#approvalForm').submit(function(e) {
                    var remarks = $('#remarks').val().trim();
                    if (remarks === '') {
                        e.preventDefault();
                        alert('Please enter remarks before submitting.');
                        $('#remarks').focus();
                        return false;
                    }

                    // Show loading
                    $('#submitBtn').html('<i class="fa fa-spinner fa-spin"></i> Processing...')
                        .prop('disabled', true);
                });

                // Clear form when modal closes
                $('#approvalModal').on('hidden.bs.modal', function() {
                    $('#remarks').val('');
                    $('#submitBtn').html('Submit').prop('disabled', false);
                });

                // Tab handling - preserve active tab on page refresh
                $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                    var tabId = $(e.target).attr('href');
                    localStorage.setItem('grnApprovalTab', tabId);
                });

                // Load saved tab on page load
                var savedTab = localStorage.getItem('grnApprovalTab');
                if (savedTab && $(savedTab).length) {
                    $('a[href="' + savedTab + '"]').tab('show');
                }

                // Auto-refresh pending tab every 60 seconds
                setInterval(function() {
                    var currentTab = $('.nav-tabs .active a').attr('href');
                    if (currentTab === '#pending') {
                        // You can implement AJAX refresh here if needed
                        // location.reload();
                    }
                }, 60000);
            });
        </script>

<?php $this->load->view('admin/footer'); ?>