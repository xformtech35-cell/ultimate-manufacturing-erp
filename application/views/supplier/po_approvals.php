<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content-header">
                <h1>PO Approvals</h1>
            </section>

            <section class="content">
                <div class="row">
                    <!-- Pending Approvals -->
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Pending Approvals</h3>
                                <span class="badge bg-red"><?= count($pending_approvals) ?></span>
                            </div>
                            <div class="box-body">
                                <?php if (empty($pending_approvals)): ?>
                                    <div class="alert alert-info">No pending approvals</div>
                                <?php else: ?>
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>PO Number</th>
                                                <th>Vendor</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th width="10%">SO Number</th>
                                                <th>Approval Level</th>
                                                <th>Approver Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pending_approvals as $approval): ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?= base_url('SupplierController/show_po_details/' . str_replace('/', '-', $approval['po_number'])) ?>">
                                                            <?= html_escape($approval['po_number'] ?? 'N/A') ?>
                                                        </a>
                                                    </td>
                                                    <td><?= html_escape(!empty($approval['supplier_name']) ? $approval['supplier_name'] : 'N/A') ?></td>
                                                    <td>
                                                        <?php
                                                        $amount = $approval['total'] ?? 0;
                                                        echo '₹' . number_format((float)$amount, 2);
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (!empty($approval['date']) && $approval['date'] !== '0000-00-00' && $approval['date'] !== null) {
                                                            echo date('d-m-Y', strtotime($approval['date']));
                                                        } else {
                                                            echo date('d-m-Y');
                                                        }
                                                        ?>
                                                    </td>
                                                   <td>
    <span>
    <?php
    if (!empty($approval['so_no'])) {
        echo html_escape($approval['so_no']);
    } elseif (!empty($approval['oc_no'])) {
        echo html_escape($approval['oc_no']);
    } else {
        echo 'N/A';
    }
    ?>
    </span>
</td>
                                                    <td>
                                                        <?php
                                                        $level = $approval['approval_level'] ?? '';
                                                        $level_text = str_replace('_', ' ', $level);
                                                        echo '<span class="label label-warning">' . ucfirst($level_text) . '</span>';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $role = 'Approver';
                                                        if (!empty($approval['approver_role'])) {
                                                            $role = $approval['approver_role'];
                                                        } elseif (!empty($approval['approval_level'])) {
                                                            $level = strtolower($approval['approval_level']);
                                                            if (strpos($level, 'buyer') !== false) {
                                                                $role = 'Buyer';
                                                            } elseif (strpos($level, 'purchase_manager') !== false || strpos($level, 'purchase') !== false) {
                                                                $role = 'Purchase Manager';
                                                            } elseif (strpos($level, 'director') !== false) {
                                                                $role = 'Director';
                                                            } elseif (strpos($level, 'manager') !== false) {
                                                                $role = 'Manager';
                                                            }
                                                        }
                                                        echo html_escape($role);
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="<?= base_url('SupplierController/show_po_details/' . str_replace('/', '-', $approval['po_number'])) ?>"
                                                                class="btn btn-sm btn-info" title="View Details">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-success approve-btn"
                                                                data-id="<?= $approval['approval_id'] ?? '' ?>"
                                                                data-po="<?= html_escape($approval['po_number'] ?? '') ?>"
                                                                data-role="<?= html_escape($role) ?>">
                                                                <i class="fa fa-check"></i> Approve
                                                            </button>
                                                            <button class="btn btn-sm btn-danger reject-btn"
                                                                data-id="<?= $approval['approval_id'] ?? '' ?>"
                                                                data-po="<?= html_escape($approval['po_number'] ?? '') ?>"
                                                                data-role="<?= html_escape($role) ?>">
                                                                <i class="fa fa-times"></i> Reject
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Approval History -->
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Approval History</h3>
                            </div>
                            <div class="box-body">
                                <?php if (empty($approval_history)): ?>
                                    <div class="alert alert-info">No approval history</div>
                                <?php else: ?>
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>PO Number(Pending / Approval / Rejected)</th>
                                                <th>Vendor</th>
                                                <th>SO Number</th>
                                                <th>Amount</th>
                                                <th>Level</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Action Date</th>
                                                <th>Remarks</th>
                                                <th>Action By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($approval_history as $history): ?>
                                                <tr>
                                                    <td>

                                                        <a href="<?= base_url('SupplierController/show_po_details/' . str_replace('/', '-', $history['po_number'])) ?>">
                                                            <?= html_escape($history['po_number'] ?? 'N/A') ?>
                                                        </a>

                                                    </td>
                                                    <td><?= html_escape(!empty($history['supplier_name']) ? $history['supplier_name'] : 'N/A') ?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($history['so_no'])) {
                                                            echo html_escape($history['so_no']);
                                                        } elseif (!empty($history['oc_no'])) {
                                                            echo html_escape($history['oc_no']);
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
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
                                                        <?php
                                                        $role = 'Approver';
                                                        if (!empty($history['approver_role'])) {
                                                            $role = $history['approver_role'];
                                                        } elseif (!empty($history['approval_level'])) {
                                                            $level = strtolower($history['approval_level']);
                                                            if (strpos($level, 'buyer') !== false) {
                                                                $role = 'Buyer';
                                                            } elseif (strpos($level, 'purchase_manager') !== false || strpos($level, 'purchase') !== false) {
                                                                $role = 'Purchase Manager';
                                                            } elseif (strpos($level, 'director') !== false) {
                                                                $role = 'Director';
                                                            } elseif (strpos($level, 'manager') !== false) {
                                                                $role = 'Manager';
                                                            }
                                                        }
                                                        echo html_escape($role);
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <span class="label label-<?= ($history['status'] ?? '') == 'approved' ? 'success' : 'danger' ?>">
                                                            <?= ucfirst($history['status'] ?? 'pending') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $action_date = $history['action_date'] ?? '';
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
                                <?php endif; ?>
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
                    <form id="approvalForm" method="post" action="<?= base_url('SupplierController/process_po_approval') ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title" id="modalTitle">Process Approval</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="approval_id" id="approval_id">
                            <input type="hidden" name="action" id="action">
                            <input type="hidden" name="user_email" value="<?= $this->session->userdata('session_data_head')['result']['user_email'] ?? '' ?>">

                            <div class="form-group">
                                <label>PO Number</label>
                                <input type="text" class="form-control" id="po_number" readonly>
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
                    $('#po_number').val($(this).data('po'));
                    $('#approver_role').val($(this).data('role'));
                    $('#modalTitle').text('Approve PO: ' + $(this).data('po'));
                    $('#approvalModal').modal('show');
                    $('#remarks').focus();
                });

                // Reject button click
                $('.reject-btn').click(function() {
                    $('#approval_id').val($(this).data('id'));
                    $('#action').val('rejected');
                    $('#po_number').val($(this).data('po'));
                    $('#approver_role').val($(this).data('role'));
                    $('#modalTitle').text('Reject PO: ' + $(this).data('po'));
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
            });
        </script>
    </div>
</body>