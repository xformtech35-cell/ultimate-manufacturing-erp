<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Item Deletion Requests
            <small>Admin Approval Panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Item Deletion Requests</li>
        </ol>
    </section>

    <section class="content">

        <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fa fa-check"></i> <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('ERRORMSG')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fa fa-ban"></i> <?php echo $this->session->flashdata('ERRORMSG'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('INFOMSG')): ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fa fa-info"></i> <?php echo $this->session->flashdata('INFOMSG'); ?>
            </div>
        <?php endif; ?>

        <!-- Pending Requests Box -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-clock-o text-warning"></i> Pending Requests
                    <span class="label label-warning" style="margin-left: 8px;"><?php echo count($pending); ?> Pending</span>
                </h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped <?php echo empty($pending) ? 'no-datatable' : ''; ?>">
                    <thead>
                        <tr style="background:#f4f4f4;">
                            <th style="width:40px;">#</th>
                            <th>Item Code / Name</th>
                            <th>Module</th>
                            <th>Requested By</th>
                            <th>Reason</th>
                            <th>Requested At</th>
                            <th style="width:160px;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pending)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted" style="padding:20px;">
                                    <i class="fa fa-check-circle text-success" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                                    No pending deletion requests.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pending as $idx => $row): ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['item_code']); ?></strong>
                                        <?php if (!empty($row['item_name']) && $row['item_name'] !== $row['item_code']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($row['item_name']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="label label-info">
                                            <?php echo $row['module'] === 'inventory' ? 'Inventory Management' : 'Item Code Master'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['requested_by_name']); ?></td>
                                    <td><?php echo !empty($row['reason']) ? htmlspecialchars($row['reason']) : '<span class="text-muted">—</span>'; ?></td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($is_admin)): ?>
                                            <a href="<?php echo base_url('DeleteApprovalController/approve/' . $row['id']); ?>"
                                               class="btn btn-xs btn-success"
                                               onclick="return confirm('Approve deletion of [<?php echo htmlspecialchars($row['item_code']); ?>]? This CANNOT be undone.');">
                                                <i class="fa fa-check"></i> Approve
                                            </a>
                                            <a href="<?php echo base_url('DeleteApprovalController/reject/' . $row['id']); ?>"
                                               class="btn btn-xs btn-danger"
                                               onclick="return confirm('Reject deletion of [<?php echo htmlspecialchars($row['item_code']); ?>]?');">
                                                <i class="fa fa-times"></i> Reject
                                            </a>
                                        <?php else: ?>
                                            <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending Admin Approval</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- History Box -->
        <div class="box box-default collapsed-box">
            <div class="box-header with-border" style="cursor:pointer;" data-widget="collapse">
                <h3 class="box-title">
                    <i class="fa fa-history text-muted"></i> Request History
                    <small class="text-muted"><?php echo count($history); ?> Records</small>
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped <?php echo empty($history) ? 'no-datatable' : ''; ?>">
                    <thead>
                        <tr style="background:#f4f4f4;">
                            <th style="width:40px;">#</th>
                            <th>Item Code / Name</th>
                            <th>Module</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Reviewed At</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted" style="padding:20px;">No history records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($history as $idx => $row): ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['item_code']); ?></strong></td>
                                    <td><?php echo $row['module'] === 'inventory' ? 'Inventory' : 'Item Code Master'; ?></td>
                                    <td><?php echo htmlspecialchars($row['requested_by_name']); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'approved'): ?>
                                            <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                                        <?php else: ?>
                                            <span class="label label-danger"><i class="fa fa-times"></i> Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo !empty($row['updated_at']) ? date('d M Y, h:i A', strtotime($row['updated_at'])) : '—'; ?></td>
                                    <td><?php echo !empty($row['review_remarks']) ? htmlspecialchars($row['review_remarks']) : '—'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</div>
