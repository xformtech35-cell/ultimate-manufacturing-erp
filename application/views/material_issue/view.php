<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Material Issue
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('material_issue') ?>">Material Issue</a></li>
                    <li class="active">View Issue Slip</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <?php $is_mrn = strpos($issue_slip['issue_no'], 'MRN-') === 0; ?>
                            <div class="box-header">
                                <h3 class="box-title">
                                    <?php if ($is_mrn): ?>
                                        Material Return Note (MRN): <?= $issue_slip['issue_no'] ?>
                                        <span class="pull-right" style="margin-left: 14px;">
                                            <span class="label" style="background-color:#ea580c; color:#fff;">Returned</span>
                                        </span>
                                    <?php else: ?>
                                        Material Issue Slip: <?= $issue_slip['issue_no'] ?>
                                        <span class="pull-right" style="margin-left: 14px;">
                                            <?php if ($issue_slip['status'] == 'draft'): ?>
                                            <span class="label label-warning">Draft</span>
                                            <?php elseif ($issue_slip['status'] == 'issued'): ?>
                                            <span class="label label-success">Issued</span>
                                            <?php elseif ($issue_slip['status'] == 'cancelled'): ?>
                                            <span class="label label-danger">Cancelled</span>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </h3>
                                <div class="pull-right">
                                    <?php if ($issue_slip['status'] == 'draft'): ?>
                                    <a href="<?= base_url('MaterialIssueController/print_slip/' . $issue_slip['issue_id']) ?>"
                                        target="_blank" class="btn btn-info btn-sm">
                                        <i class="fa fa-print"></i> Print
                                    </a>
                                    <a href="<?= base_url('Pdf/download_material_issue/' . $issue_slip['issue_id']) ?>"
                                        class="btn btn-danger btn-sm">
                                        <i class="fa fa-file-pdf-o"></i> Export PDF
                                    </a>
                                    <a href="<?= base_url('MaterialIssueController/approve/' . $issue_slip['issue_id']) ?>"
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Approve this issue slip? This will mark it as Issued.')">
                                        <i class="fa fa-check"></i> Approve / Issue
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#cancelModal">
                                        <i class="fa fa-times"></i> Cancel
                                    </button>
                                    <?php elseif ($is_mrn): ?>
                                    <a href="<?= base_url('MaterialIssueController/print_slip/' . $issue_slip['issue_id']) ?>"
                                        target="_blank" class="btn btn-info btn-sm">
                                        <i class="fa fa-print"></i> Print
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?php echo base_url('MaterialIssueController/index') ?>"
                                        class="btn btn-default btn-sm">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4><?= $is_mrn ? 'Return Details' : 'Issue Details' ?></h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="30%"><?= $is_mrn ? 'Return Date:' : 'Issue Date:' ?></th>
                                                <td>
                                                    <?php 
                                                    if ($issue_slip['status'] == 'draft' && isset($is_edit_mode) && $is_edit_mode): 
                                                    ?>
                                                    <input type="text" name="issue_date_edit" id="issue_date_edit"
                                                        class="form-control datepicker"
                                                        value="<?= date('d-m-Y', strtotime($issue_slip['issue_date'])) ?>"
                                                        style="display: inline-block; width: auto;">
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        id="updateDateBtn">
                                                        <i class="fa fa-save"></i> Update
                                                    </button>
                                                    <?php else: ?>
                                                    <?= date('d-m-Y', strtotime($issue_slip['issue_date'])) ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><?= $is_mrn ? 'Returned By:' : 'Issued To:' ?></th>
                                                <td><?= $issue_slip['issued_to'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Department:</th>
                                                <td><?= $issue_slip['department'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Job Order No:</th>
                                                <td><?= isset($issue_slip['joborder_number']) ? $issue_slip['joborder_number'] : 'N/A' ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><?= $is_mrn ? 'Remarks / Reason:' : 'Purpose:' ?></th>
                                                <td><?= $is_mrn ? $issue_slip['remarks'] : $issue_slip['purpose'] ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>System Information</h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="30%">Created By:</th>
                                                <td><?= isset($issue_slip['issued_by_name']) ? $issue_slip['issued_by_name'] : 'N/A' ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Created Date:</th>
                                                <td><?= date('d-m-Y H:i:s', strtotime($issue_slip['created_at'])) ?>
                                                </td>
                                            </tr>
                                            <?php if ($issue_slip['status'] == 'issued'): ?>
                                            <tr>
                                                <th>Approved By:</th>
                                                <td><?= isset($issue_slip['approved_by_name']) ? $issue_slip['approved_by_name'] : 'N/A' ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Approved Date:</th>
                                                <td><?= !empty($issue_slip['approved_date']) ? date('d-m-Y H:i:s', strtotime($issue_slip['approved_date'])) : 'N/A' ?>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if ($issue_slip['status'] == 'cancelled'): ?>
                                            <tr>
                                                <th>Remarks:</th>
                                                <td><?= $issue_slip['remarks'] ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        </table>
                                    </div>
                                </div>

                                <h4>Items Details</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                         <thead>
                                             <tr>
                                                 <th width="4%">Sr.No</th>
                                                 <th width="18%">Item</th>
                                                 <th width="5%">Unit</th>
                                                 <?php if (!$is_mrn): ?>
                                                 <th width="7%" class="text-right">BOM Qty</th>
                                                 <th width="7%" class="text-right">Prev. MI</th>
                                                 <?php endif; ?>
                                                 <th width="8%" class="text-right"><?= $is_mrn ? 'Returned Qty' : 'Current Issue' ?></th>
                                                 <?php if (!$is_mrn): ?>
                                                 <th width="8%" class="text-right">Total MI</th>
                                                 <?php endif; ?>
                                                 <th width="7%" class="text-right"><?= $is_mrn ? 'Status' : 'Overrun Qty' ?></th>
                                                 <th width="6%" class="text-right"><?= $is_mrn ? '' : 'Overrun %' ?></th>
                                                 <th width="8%" class="text-right"><?= $is_mrn ? '' : 'Rate' ?></th>
                                                 <th width="9%" class="text-right"><?= $is_mrn ? '' : 'Overrun Value' ?></th>
                                                 <th width="7%"><?= $is_mrn ? '' : 'Overrun Status' ?></th>
                                                 <th width="6%">Remarks</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             <?php if (!empty($issue_slip['items'])): ?>
                                             <?php $i = 1; ?>
                                             <?php foreach ($issue_slip['items'] as $item): ?>
                                             <tr>
                                                 <td class="text-center"><?= $i ?></td>
                                                 <td><?= $item['code'] ?> - <?= $item['item_name'] ?></td>
                                                 <td class="text-center"><?= $item['unit'] ?></td>
                                                 <?php if (!$is_mrn): ?>
                                                 <td class="text-right"><?= !empty($issue_slip['joborder_number']) ? number_format($item['bom_qty'] ?? $item['required_qty'], 2) : '-' ?></td>
                                                 <td class="text-right"><?= !empty($issue_slip['joborder_number']) ? number_format($item['previous_issued_qty'] ?? 0, 2) : '-' ?></td>
                                                 <?php endif; ?>
                                                 <td class="text-right"><?= number_format($is_mrn ? abs($item['quantity']) : $item['quantity'], 2) ?></td>
                                                 <?php if (!$is_mrn): ?>
                                                 <td class="text-right"><strong><?= number_format($item['total_mi_qty'] ?? (floatval($item['previous_issued_qty'] ?? 0) + floatval($item['quantity'])), 2) ?></strong></td>
                                                 <td class="text-right">
                                                     <?php
                                                     $overrun_qty = floatval($item['overrun_qty'] ?? 0);
                                                     if ($overrun_qty > 0) {
                                                         $color = ($item['overrun_status'] ?? 'none') === 'approval_required' ? '#dd4b39' : '#f39c12';
                                                         echo '<span style="color:'.$color.';font-weight:600;">'.number_format($overrun_qty, 2).'</span>';
                                                     } else { echo '<span style="color:#00a65a;">0</span>'; }
                                                     ?>
                                                 </td>
                                                 <td class="text-right">
                                                     <?php
                                                     $overrun_pct = floatval($item['overrun_pct_actual'] ?? 0);
                                                     if ($overrun_pct > 0) {
                                                         $color = ($item['overrun_status'] ?? 'none') === 'approval_required' ? '#dd4b39' : '#f39c12';
                                                         echo '<span style="color:'.$color.';font-weight:600;">'.number_format($overrun_pct, 2).'%</span>';
                                                     } else { echo '<span style="color:#00a65a;">0%</span>'; }
                                                     ?>
                                                 </td>
                                                 <td class="text-right"><?= !empty($item['unit_price']) ? '&#8377;'.number_format($item['unit_price'],2) : '-' ?></td>
                                                 <td class="text-right">
                                                     <?php
                                                     $ov = floatval($item['overrun_value'] ?? 0);
                                                     if ($ov > 0) {
                                                         $color = ($item['overrun_status'] ?? 'none') === 'approval_required' ? '#dd4b39' : '#f39c12';
                                                         echo '<span style="color:'.$color.';font-weight:600;">&#8377;'.number_format($ov,2).'</span>';
                                                     } else { echo '-'; }
                                                     ?>
                                                 </td>
                                                 <td>
                                                     <?php
                                                     $os = $item['overrun_status'] ?? 'none';
                                                     $badges = [
                                                         'none'              => ['label'=>'None',             'class'=>'label-default'],
                                                         'within_limit'      => ['label'=>'Within Limit',     'class'=>'label-warning'],
                                                         'approval_required' => ['label'=>'Approval Required','class'=>'label-danger'],
                                                         'approved'          => ['label'=>'Approved',         'class'=>'label-success'],
                                                         'rejected'          => ['label'=>'Rejected',         'class'=>'label-default'],
                                                     ];
                                                     $b = $badges[$os] ?? $badges['none'];
                                                     echo '<span class="label '.$b['class'].'">'.$b['label'].'</span>';
                                                     if (!empty($item['overrun_remarks'])) {
                                                         echo '<br><small class="text-muted">'.$item['overrun_remarks'].'</small>';
                                                     }
                                                     ?>
                                                 </td>
                                                 <?php else: ?>
                                                 <td><span class="label" style="background-color:#ea580c;color:#fff;">Returned</span></td>
                                                 <td></td><td></td><td></td><td></td>
                                                 <?php endif; ?>
                                                 <td><?= $item['remarks'] ?></td>
                                             </tr>
                                             <?php $i++; ?>
                                             <?php endforeach; ?>
                                             <?php else: ?>
                                             <tr>
                                                 <td colspan="<?= $is_mrn ? 6 : 8 ?>" class="text-center">No items found</td>
                                             </tr>
                                             <?php endif; ?>
                                         </tbody>
                                         <tfoot>
                                             <tr class="active">
                                                 <td colspan="<?= $is_mrn ? 3 : 5 ?>" class="text-right"><strong>Total Items:</strong></td>
                                                 <td colspan="3"><strong><?= $issue_slip['total_items'] ?></strong></td>
                                             </tr>
                                             <tr class="active">
                                                 <td colspan="<?= $is_mrn ? 3 : 5 ?>" class="text-right"><strong>Total Quantity:</strong></td>
                                                 <td class="text-right">
                                                     <strong><?= number_format($is_mrn ? abs($issue_slip['total_qty']) : $issue_slip['total_qty'], 2) ?></strong>
                                                 </td>
                                                 <td colspan="2"></td>
                                             </tr>
                                         </tfoot>
                                     </table>
                                </div>

                                <?php if (!empty($issue_slip['remarks'])): ?>
                                <div class="well">
                                    <strong>Remarks:</strong><br>
                                    <?= $issue_slip['remarks'] ?>
                                </div>
                                <?php endif; ?>

                                <?php if ($issue_slip['status'] === 'pending_overrun_approval'): ?>
                                <div class="alert alert-danger">
                                    <h4><i class="fa fa-exclamation-triangle"></i> Overrun Approval Required</h4>
                                    <p>One or more items in this Material Issue Slip exceed the allowed overrun tolerance.<br>
                                    A manager must <strong>Approve</strong> or <strong>Reject</strong> the overrun before stock is deducted.</p>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#approveOverrunModal">
                                        <i class="fa fa-check"></i> Approve Overrun
                                    </button>
                                    &nbsp;
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectOverrunModal">
                                        <i class="fa fa-times"></i> Reject Overrun
                                    </button>
                                </div>
                                <?php endif; ?>
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
    <!-- ./wrapper -->

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancel Issue Slip</h4>
                </div>
                <form method="post"
                    action="<?= base_url('MaterialIssueController/cancel/' . $issue_slip['issue_id']) ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Reason for Cancellation <span class="text-danger">*</span></label>
                            <textarea name="cancel_remarks" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Issue Slip</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Approve Overrun Modal -->
    <div class="modal fade" id="approveOverrunModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#00a65a;color:#fff;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-check-circle"></i> Approve Overrun</h4>
                </div>
                <form method="post"
                    action="<?= base_url('MaterialIssueController/approve_overrun/' . $issue_slip['issue_id']) ?>">
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <strong>Approving overrun</strong> will allow excess material issue quantities and trigger stock deduction.
                        </div>
                        <div class="form-group">
                            <label>Manager Remarks (Optional)</label>
                            <textarea name="overrun_remarks" class="form-control" rows="3"
                                      placeholder="e.g. Approved for production urgency"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check"></i> Confirm Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Overrun Modal -->
    <div class="modal fade" id="rejectOverrunModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#dd4b39;color:#fff;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-times-circle"></i> Reject Overrun</h4>
                </div>
                <form method="post"
                    action="<?= base_url('MaterialIssueController/reject_overrun/' . $issue_slip['issue_id']) ?>">
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <strong>Rejecting overrun</strong> will cancel this Material Issue Slip. No stock will be deducted.
                        </div>
                        <div class="form-group">
                            <label>Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="overrun_remarks" class="form-control" rows="3" required
                                      placeholder="e.g. Exceeds tolerance, revise quantities"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times"></i> Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Initialize datepicker if the date input exists
        if ($('.datepicker').length > 0) {
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true
            });
        }

        // Update date functionality (if in edit mode)
        $('#updateDateBtn').click(function() {
            const newDate = $('#issue_date_edit').val();
            const issueId = <?= $issue_slip['issue_id'] ?>;

            if (!newDate) {
                alert('Please select a date');
                return false;
            }

            $.ajax({
                url: '<?= base_url("MaterialIssueController/update_issue_date") ?>',
                method: 'POST',
                dataType: 'json',
                data: {
                    issue_id: issueId,
                    issue_date: newDate
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Issue date updated successfully');
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to update date');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Error updating date. Please try again.');
                }
            });
        });
    });
    </script>
</body>