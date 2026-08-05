<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div id="loader" class="center"></div>
<div class="wrapper">

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <h1>BOM Approval Dashboard <small>Pending approvals for your role</small></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url() . 'BomController/index'; ?>">BOM</a></li>
            <li class="active">Approvals</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-check-circle text-success"></i> BOMs Awaiting Your Approval</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url() . 'BomController/index'; ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-list"></i> View All BOMs
                            </a>
                        </div>
                    </div>

                    <div class="box-body">

                        <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong><i class="fa fa-check"></i></strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                            </div>
                        <?php } ?>
                        <?php if ($this->session->flashdata('ERRORMSG')) { ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong><i class="fa fa-warning"></i></strong> <?= $this->session->flashdata('ERRORMSG') ?>
                            </div>
                        <?php } ?>

                        <?php if (empty($pending_approvals)) { ?>
                            <div class="text-center" style="padding: 40px 0;">
                                <i class="fa fa-check-circle text-success" style="font-size: 50px;"></i>
                                <h4 style="margin-top: 15px; color: #888;">No pending BOM approvals for your role.</h4>
                            </div>
                        <?php } else { ?>
                            <table class="table table-bordered table-striped table-hover">
                                <thead style="background: #f4f4f4;">
                                    <tr>
                                        <th>#</th>
                                        <th>BOM Number</th>
                                        <th>Customer</th>
                                        <th>OC / Project</th>
                                        <th>System</th>
                                        <th>Approval Level</th>
                                        <th>Submitted</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($pending_approvals as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <a href="<?= base_url() . 'BomController/show_bom/' . $row['bom_id_fk'] ?>">
                                                <strong><?= htmlspecialchars($row['bom_number']) ?></strong>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars(!empty($row['company_name']) ? $row['company_name'] : ($row['fullname'] ?? '')) ?></td>
                                        <td>
                                            <?= htmlspecialchars($row['oc_number'] ?? '') ?>
                                            <?php if (!empty($row['project_code'])): ?>
                                                <br><small class="text-muted">Project: <?= htmlspecialchars($row['project_code']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['bom_system'] ?? '') ?></td>
                                        <td>
                                            <span class="label label-warning"><?= htmlspecialchars($row['approval_level']) ?></span>
                                            <br><small class="text-muted"><?= htmlspecialchars($row['approver_role']) ?></small>
                                        </td>
                                        <td><?= !empty($row['bom_date']) ? date('d-m-Y', strtotime($row['bom_date'])) : '' ?></td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-xs"
                                                    onclick="openApprovalModal(<?= $row['approval_id'] ?>, '<?= htmlspecialchars($row['bom_number']) ?>', 'approved')">
                                                <i class="fa fa-check"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs"
                                                    onclick="openApprovalModal(<?= $row['approval_id'] ?>, '<?= htmlspecialchars($row['bom_number']) ?>', 'rejected')">
                                                <i class="fa fa-times"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php } ?>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->

        <!-- BOM Approval History -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-history text-muted"></i> BOM Approval History</h3>
                    </div>
                    <div class="box-body">
                        <?php if (empty($approval_history)) { ?>
                            <div class="text-center" style="padding: 20px 0;">
                                <p class="text-muted">No approval history records found.</p>
                            </div>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead style="background: #f4f4f4;">
                                        <tr>
                                            <th>#</th>
                                            <th>BOM Number</th>
                                            <th>Customer</th>
                                            <th>OC / Project</th>
                                            <th>System</th>
                                            <th>Level</th>
                                            <th>Role</th>
                                            <th>Decision</th>
                                            <th>Processed By</th>
                                            <th>Date</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach ($approval_history as $row): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td>
                                                <a href="<?= base_url() . 'BomController/show_bom/' . $row['bom_id_fk'] ?>">
                                                    <strong><?= htmlspecialchars($row['bom_number']) ?></strong>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars(!empty($row['company_name']) ? $row['company_name'] : ($row['fullname'] ?? '')) ?></td>
                                            <td>
                                                <?= htmlspecialchars($row['oc_number'] ?? '') ?>
                                                <?php if (!empty($row['project_code'])): ?>
                                                    <br><small class="text-muted">Project: <?= htmlspecialchars($row['project_code']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['bom_system'] ?? '') ?></td>
                                            <td><span class="label label-default"><?= htmlspecialchars($row['approval_level']) ?></span></td>
                                            <td><small><?= htmlspecialchars($row['approver_role']) ?></small></td>
                                            <td>
                                                <?php if ($row['status'] === 'approved') { ?>
                                                    <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                                                <?php } else { ?>
                                                    <span class="label label-danger"><i class="fa fa-times"></i> Rejected</span>
                                                <?php } ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['action_by'] ?? '') ?></td>
                                            <td><?= !empty($row['action_date']) ? date('d-m-Y H:i', strtotime($row['action_date'])) : '' ?></td>
                                            <td><small class="text-muted"><?= htmlspecialchars($row['remarks'] ?? '') ?></small></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('admin/footer'); ?>
<div class="control-sidebar-bg"></div>
</div><!-- /.wrapper -->

<!-- Approve/Reject Modal -->
<div id="approvalModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="approvalModalTitle">Approve BOM</h4>
            </div>
            <form method="post" action="<?= base_url() . 'BomController/process_bom_approval' ?>">
                <div class="modal-body">
                    <input type="hidden" name="approval_id" id="modal_approval_id">
                    <input type="hidden" name="action" id="modal_action">
                    <div class="form-group">
                        <label>BOM Number</label>
                        <input type="text" class="form-control input-sm" id="modal_bom_number" readonly>
                    </div>
                    <div class="form-group">
                        <label>Remarks <small class="text-muted">(optional)</small></label>
                        <textarea name="remarks" class="form-control input-sm" rows="3" placeholder="Enter your remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="approvalSubmitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openApprovalModal(approvalId, bomNumber, action) {
    document.getElementById('modal_approval_id').value = approvalId;
    document.getElementById('modal_action').value = action;
    document.getElementById('modal_bom_number').value = bomNumber;

    var isApprove = action === 'approved';
    document.getElementById('approvalModalTitle').textContent = isApprove ? 'Approve BOM' : 'Reject BOM';
    document.getElementById('approvalSubmitBtn').className = isApprove ? 'btn btn-success' : 'btn btn-danger';
    document.getElementById('approvalSubmitBtn').textContent = isApprove ? 'Approve' : 'Reject';

    $('#approvalModal').modal('show');
}
</script>
