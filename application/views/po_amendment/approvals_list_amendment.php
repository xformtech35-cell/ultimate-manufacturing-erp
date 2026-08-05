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
                <h1>PO Amendment Approvals</h1>
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
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Amendment No</th>
                                                <th>PO Number</th>
                                                <th>Vendor</th>
                                                <th>Amount</th>
                                                <th>Initiated Date</th>
                                                <th>Approval Level</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pending_approvals as $approval): ?>
                                                <tr>
                                                    <td><strong><?= $approval['amendment_no'] ?></strong></td>
                                                    <td>
                                                        <a href="<?= base_url('Purchaseorder/view_po/' . str_replace('/', '-', $approval['po_number'])) ?>"
                                                            target="_blank">
                                                            <?= $approval['po_number'] ?>
                                                        </a>
                                                    </td>
                                                    <td><?= $approval['vendor_name'] ?></td>
                                                    <td>₹<?= number_format($approval['amendment_value'] ?? 0, 2) ?></td>
                                                    <td><?= date('d-m-Y H:i', strtotime($approval['initiated_date'])) ?></td>
                                                    <td><?= ucfirst(str_replace('_', ' ', $approval['approval_level'])) ?></td>
                                                    <td>
                                                        <a href="<?= base_url('PoamendmentController/view/' . $approval['amendment_id']) ?>"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                        <button class="btn btn-sm btn-success approve-btn"
                                                            data-id="<?= $approval['approval_id'] ?>"
                                                            data-amendment="<?= $approval['amendment_no'] ?>">
                                                            <i class="fa fa-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-sm btn-danger reject-btn"
                                                            data-id="<?= $approval['approval_id'] ?>"
                                                            data-amendment="<?= $approval['amendment_no'] ?>">
                                                            <i class="fa fa-times"></i> Reject
                                                        </button>
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
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Amendment No</th>
                                                <th>PO Number</th>
                                                <th>Vendor</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($approval_history as $history): ?>
                                                <tr>
                                                    <td><?= $history['amendment_no'] ?></td>
                                                    <td><?= $history['po_number'] ?></td>
                                                    <td><?= $history['vendor_name'] ?></td>
                                                    <td>₹<?= number_format($history['amendment_value'], 2) ?></td>
                                                    <td>
                                                        <span class="label label-<?= $history['status'] == 'approved' ? 'success' : 'danger' ?>">
                                                            <?= ucfirst($history['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('d-m-Y H:i', strtotime($history['action_date'])) ?></td>
                                                    <td><?= $history['remarks'] ?></td>
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
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="approvalForm" method="post" action="<?= base_url('PoamendmentController/process_approval/') ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Process Approval</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="approval_id" id="approval_id">
                        <input type="hidden" name="action" id="action">
                        <div class="form-group">
                            <label>Amendment No</label>
                            <input type="text" class="form-control" id="amendment_no" readonly>
                        </div>
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea class="form-control" name="remarks" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.approve-btn').click(function() {
                $('#approval_id').val($(this).data('id'));
                $('#action').val('approved');
                $('#amendment_no').val($(this).data('amendment'));
                $('#approvalModal').modal('show');
            });

            $('.reject-btn').click(function() {
                $('#approval_id').val($(this).data('id'));
                $('#action').val('rejected');
                $('#amendment_no').val($(this).data('amendment'));
                $('#approvalModal').modal('show');
            });

            // Update form action URL with approval_id
            $('#approvalForm').submit(function(e) {
                var approval_id = $('#approval_id').val();
                $(this).attr('action', '<?= base_url("PoamendmentController/process_approval/") ?>' + approval_id);
            });
        });
    </script>
</body>