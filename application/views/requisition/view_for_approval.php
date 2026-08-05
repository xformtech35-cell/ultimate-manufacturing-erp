<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');

// Get user ID from session
$session_data_head = $this->session->userdata('session_data_head');
$user_id = $session_data_head['result']['user_id'] ?? null;

// Check if user can approve this PR
$can_approve = false;
$role_name = $session_data_head['result']['role_name'] ?? '';
$is_admin = (strtolower($role_name) === 'admin');

if ($requisition->current_approver_role) {
    if ($is_admin) {
        $can_approve = true;
    } else {
        // Get user's roles
        $user_roles = $this->requisition->get_user_roles($user_id);
        $can_approve = in_array($requisition->current_approver_role, $user_roles);
    }
}
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Review Purchase Requisition
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>">Approval Dashboard</a></li>
                    <li class="active">Review PR</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Flash Messages -->
                <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i> Success!</h4>
                        <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('ERRORMSG')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        <?php echo $this->session->flashdata('ERRORMSG'); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <!-- PR Details Box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">PR Details - <?php echo $requisition->pr_no ?? 'N/A'; ?></h3>
                                <div class="box-tools pull-right">
                                    <span class="label label-<?php
                                                                echo ($requisition->approval_status == 'Approved') ? 'success' : (($requisition->approval_status == 'Pending') ? 'warning' : 'danger');
                                                                ?>">
                                        <?php echo $requisition->approval_status; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">PR Number:</th>
                                                <td><?php echo $requisition->pr_no ?? 'N/A'; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Department:</th>
                                                <td><?php echo $requisition->department_name ?? 'N/A'; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Requested By:</th>
                                                <td><?php echo $requisition->requester_name ?? 'N/A'; ?></td>
                                            </tr>
                                            <tr>
                                                <th>PR Date:</th>
                                                <td><?php echo date('d-m-Y', strtotime($requisition->pr_date)); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Created By:</th>
                                                <td><?php echo $requisition->requester_name ?? 'N/A'; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Required Date:</th>
                                                <td><?php echo date('d-m-Y', strtotime($requisition->required_date)); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Urgency Level:</th>
                                                <td>
                                                    <span class="label label-<?php
                                                                                echo ($requisition->urgency_level == 'Critical') ? 'danger' : (($requisition->urgency_level == 'Urgent') ? 'warning' : 'info');
                                                                                ?>">
                                                        <?php echo $requisition->urgency_level; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Total Value:</th>
                                                <td><strong>₹<?php echo number_format($requisition->total_value, 2); ?></strong></td>
                                            </tr>
                                            <tr>
                                                <th>Current Approver:</th>
                                                <td>
                                                    <?php if ($requisition->current_approver_role): ?>
                                                        <span class="label label-primary"><?php echo $requisition->current_approver_role; ?></span>
                                                        <?php if ($can_approve): ?>
                                                            <span class="label label-success"><i class="fa fa-check"></i> You can approve</span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not assigned</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Remarks:</th>
                                                <td><?php echo $requisition->remarks ?: 'No remarks'; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Approval Progress -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Approval Progress</h4>
                                        <?php if (!empty($approval_progress['levels'])): ?>
                                            <div class="progress" style="height: 30px;">
                                                <?php
                                                $total_applicable = 0;
                                                foreach ($approval_progress['levels'] as $level) {
                                                    if ($level['applicable']) {
                                                        $total_applicable++;
                                                    }
                                                }

                                                $width_per_level = $total_applicable > 0 ? (100 / $total_applicable) : 0;

                                                foreach ($approval_progress['levels'] as $level):
                                                    if ($level['applicable']):
                                                ?>
                                                        <div class="progress-bar progress-bar-<?php
                                                                                                echo ($level['status'] == 'approved') ? 'success' : (($level['status'] == 'current') ? 'warning' : (($level['status'] == 'pending') ? 'info' : 'default'));
                                                                                                ?>"
                                                            style="width: <?php echo $width_per_level; ?>%"
                                                            title="Level <?php echo $level['level']; ?> - <?php echo $level['role']; ?>">
                                                            Level <?php echo $level['level']; ?><br>
                                                            <small><?php echo $level['role']; ?></small>
                                                        </div>
                                                <?php
                                                    endif;
                                                endforeach;
                                                ?>
                                            </div>
                                            <p class="text-center">
                                                <strong><?php echo round($approval_progress['percentage'], 2); ?>% Complete</strong>
                                                (<?php echo $approval_progress['completed_levels']; ?> of <?php echo $total_applicable; ?> levels completed)
                                            </p>

                                            <!-- Detailed Approval Steps -->
                                            <div class="table-responsive">
                                                 <table class="table table-bordered no-datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Level</th>
                                                            <th>Approver Role</th>
                                                            <th>Amount Range</th>
                                                            <th>Status</th>
                                                            <th>Approved By</th>
                                                            <th>Approved Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($approval_progress['levels'] as $level): ?>
                                                            <?php if ($level['applicable']): ?>
                                                                <tr>
                                                                    <td>Level <?php echo $level['level']; ?></td>
                                                                    <td><?php echo $level['role']; ?></td>
                                                                    <td>
                                                                        ₹<?php echo number_format($level['min_amount'], 2); ?>
                                                                        to
                                                                        <?php echo $level['max_amount'] == 0 ? 'Unlimited' : '₹' . number_format($level['max_amount'], 2); ?>
                                                                    </td>
                                                                    <td>
                                                                        <span class="label label-<?php
                                                                                                    echo ($level['status'] == 'approved') ? 'success' : (($level['status'] == 'current') ? 'warning' : (($level['status'] == 'pending') ? 'info' : 'default'));
                                                                                                    ?>">
                                                                            <?php echo ucfirst($level['status']); ?>
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($level['approved_by']):
                                                                            $approver_name = $this->requisition->get_user_name($level['approved_by']);
                                                                            $approver_role_name = $this->requisition->get_user_role($level['approved_by']);
                                                                        ?>
                                                                            <?php echo html_escape($approver_name) . ($approver_role_name ? ' (' . html_escape($approver_role_name) . ')' : ''); ?>
                                                                        <?php else: ?>
                                                                            <span class="text-muted">-</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($level['approved_date']): ?>
                                                                            <?php echo date('d-m-Y H:i', strtotime($level['approved_date'])); ?>
                                                                        <?php else: ?>
                                                                            <span class="text-muted">-</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> No approval levels defined for this PR amount.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Items List</h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($requisition_items)): ?>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Item Code</th>
                                                <th>Description</th>
                                                <th>HSN</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Estimated Cost</th>
                                                <th>Total</th>
                                                <th>Specification</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $counter = 1;
                                            $total = 0; ?>
                                            <?php foreach ($requisition_items as $item): ?>
                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td><?php echo $item->item_code; ?></td>
                                                    <td><?php echo strip_tags($item->description); ?></td>
                                                    <td><?php echo $item->hsn; ?></td>
                                                    <td><?php echo $item->quantity; ?></td>
                                                    <td><?php echo $item->unit; ?></td>
                                                    <td>₹<?php echo number_format($item->estimated_cost, 2); ?></td>
                                                    <td>₹<?php
                                                            $item_total = $item->estimated_cost * $item->quantity;
                                                            $total += $item_total;
                                                            echo number_format($item_total, 2);
                                                            ?></td>
                                                    <td><?php echo $item->specification; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="7" class="text-right">Total:</th>
                                                <th colspan="2">₹<?php echo number_format($total, 2); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-warning"></i> No items found for this requisition.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Approval Action Form -->
                        <?php if ($can_approve && $requisition->approval_status == 'Pending'): ?>
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Take Action (You are: <?php echo $requisition->current_approver_role; ?>)</h3>
                                </div>
                                <div class="box-body">
                                    <form action="<?php echo base_url('RequisitionController/process_approval'); ?>" method="post" id="approvalForm">
                                        <input type="hidden" name="pr_id" value="<?php echo $requisition->pr_id; ?>">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Comments (Optional):</label>
                                                    <textarea class="form-control" name="comments" rows="3"
                                                        placeholder="Enter your comments here... (e.g., Approved with notes, Reason for rejection, etc.)"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <button type="submit" name="action" value="Approved" class="btn btn-success btn-lg">
                                                    <i class="fa fa-check"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-lg" onclick="showRejectReason()">
                                                    <i class="fa fa-times"></i> Reject
                                                </button>
                                                <button type="button" class="btn btn-warning btn-lg" onclick="showReturnReason()">
                                                    <i class="fa fa-reply"></i> Return for Revision
                                                </button>
                                                <a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>"
                                                    class="btn btn-default btn-lg">
                                                    <i class="fa fa-arrow-left"></i> Back
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Hidden forms for reject and return with specific comments -->
                                        <div id="rejectForm" style="display: none; margin-top: 20px;">
                                            <div class="alert alert-danger">
                                                <h4><i class="fa fa-exclamation-triangle"></i> Rejection Reason</h4>
                                                <p>Please provide a reason for rejecting this PR:</p>
                                                <div class="form-group">
                                                    <textarea class="form-control" name="reject_comments" rows="3"  placeholder="Required: Enter reason for rejection..."></textarea>
                                                </div>
                                                <button type="submit" name="action" value="Rejected" class="btn btn-danger">
                                                    <i class="fa fa-times"></i> Confirm Rejection
                                                </button>
                                                <button type="button" class="btn btn-default" onclick="hideRejectForm()">Cancel</button>
                                            </div>
                                        </div>

                                        <div id="returnForm" style="display: none; margin-top: 20px;">
                                            <div class="alert alert-warning">
                                                <h4><i class="fa fa-info-circle"></i> Return Reason</h4>
                                                <p>Please provide feedback for returning this PR for revision:</p>
                                                <div class="form-group">
                                                    <textarea class="form-control" name="return_comments" rows="3"  placeholder="Required: Enter feedback for revision..."></textarea>
                                                </div>
                                                <button type="submit" name="action" value="Returned" class="btn btn-warning">
                                                    <i class="fa fa-reply"></i> Confirm Return
                                                </button>
                                                <button type="button" class="btn btn-default" onclick="hideReturnForm()">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php elseif (!$can_approve && $requisition->approval_status == 'Pending'): ?>
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Approval Status</h3>
                                </div>
                                <div class="box-body">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        This PR is currently pending approval from <strong><?php echo $requisition->current_approver_role; ?></strong>.
                                        <br>You cannot take action on this PR as you are not assigned as the current approver.
                                    </div>
                                    <a href="<?php echo base_url('RequisitionController/approval_dashboard'); ?>"
                                        class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Approval History -->
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Approval History & Timeline</h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($approval_history)): ?>
                                    <!-- Detailed History Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped no-datatable">
                                            <thead>
                                                <tr>
                                                    <th>Date & Time</th>
                                                    <th>Level</th>
                                                    <th>Approver Role</th>
                                                    <th>Approver Name</th>
                                                    <th>Action</th>
                                                    <th>Comments</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($approval_history as $history): ?>
                                                    <tr>
                                                        <td><?php echo date('d-m-Y H:i:s', strtotime($history->action_date)); ?></td>
                                                        <td>Level <?php echo $history->approval_level; ?></td>
                                                        <td><?php echo $history->approver_role; ?></td>
                                                        <td><?php echo $history->approver_name; ?></td>
                                                        <td>
                                                            <span class="label label-<?php
                                                                                        echo ($history->action == 'Approved') ? 'success' : (($history->action == 'Rejected') ? 'danger' : (($history->action == 'Returned') ? 'warning' : 'info'));
                                                                                        ?>">
                                                                <?php echo $history->action; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $history->comments ?: '<span class="text-muted">No comments</span>'; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Timeline View -->
                                    <h4>Timeline View</h4>
                                    <ul class="timeline">
                                        <?php foreach ($timeline as $event): ?>
                                            <li class="time-label">
                                                <span class="bg-<?php
                                                                echo ($event['event'] == 'Approved') ? 'green' : (($event['event'] == 'Rejected') ? 'red' : (($event['event'] == 'Returned') ? 'yellow' : 'blue'));
                                                                ?>">
                                                    <?php echo date('d M Y', strtotime($event['date'])); ?>
                                                </span>
                                            </li>
                                            <li>
                                                <i class="fa fa-<?php
                                                                echo ($event['event'] == 'Approved') ? 'check' : (($event['event'] == 'Rejected') ? 'times' : (($event['event'] == 'Returned') ? 'reply' : (($event['event'] == 'Submitted') ? 'paper-plane' : 'file')));
                                                                ?> bg-<?php
                                                                        echo ($event['event'] == 'Approved') ? 'green' : (($event['event'] == 'Rejected') ? 'red' : (($event['event'] == 'Returned') ? 'yellow' : 'blue'));
                                                                        ?>"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fa fa-clock-o"></i> <?php echo date('H:i', strtotime($event['date'])); ?></span>
                                                    <h3 class="timeline-header">
                                                        <strong><?php echo $event['event']; ?></strong>
                                                        <?php if ($event['user'] != 'System' && $event['user'] != 'Unknown User'): ?>
                                                            by <?php echo $event['user']; ?>
                                                        <?php endif; ?>
                                                    </h3>
                                                    <div class="timeline-body">
                                                        <?php if (!empty($event['description'])): ?>
                                                            <?php echo $event['description']; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">No details provided</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                        <li>
                                            <i class="fa fa-clock-o bg-gray"></i>
                                        </li>
                                    </ul>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No approval history available for this PR.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>

    <script>
        function showRejectReason() {
            document.getElementById('rejectForm').style.display = 'block';
            document.getElementById('returnForm').style.display = 'none';
            // Copy comments from main form to reject form
            var mainComments = document.querySelector('textarea[name="comments"]').value;
            document.querySelector('textarea[name="reject_comments"]').value = mainComments;
        }

        function hideRejectForm() {
            document.getElementById('rejectForm').style.display = 'none';
        }

        function showReturnReason() {
            document.getElementById('returnForm').style.display = 'block';
            document.getElementById('rejectForm').style.display = 'none';
            // Copy comments from main form to return form
            var mainComments = document.querySelector('textarea[name="comments"]').value;
            document.querySelector('textarea[name="return_comments"]').value = mainComments;
        }

        function hideReturnForm() {
            document.getElementById('returnForm').style.display = 'none';
        }

        // Handle form submission
        document.getElementById('approvalForm').addEventListener('submit', function(e) {
            var action = e.submitter ? e.submitter.value : '';
            var commentsField = '';

            if (action === 'Rejected') {
                commentsField = document.querySelector('textarea[name="reject_comments"]');
            } else if (action === 'Returned') {
                commentsField = document.querySelector('textarea[name="return_comments"]');
            } else {
                commentsField = document.querySelector('textarea[name="comments"]');
            }

            // If it's reject or return and no comments in the specific field, show alert
            if ((action === 'Rejected' || action === 'Returned') && commentsField.value.trim() === '') {
                alert('Please provide a reason for ' + action.toLowerCase() + 'ing this PR.');
                e.preventDefault();
                return false;
            }

            // Confirm action
            var confirmMessage = '';
            switch (action) {
                case 'Approved':
                    confirmMessage = 'Are you sure you want to APPROVE this Purchase Requisition?';
                    break;
                case 'Rejected':
                    confirmMessage = 'Are you sure you want to REJECT this Purchase Requisition?\n\nThis action cannot be undone.';
                    break;
                case 'Returned':
                    confirmMessage = 'Are you sure you want to RETURN this Purchase Requisition for revision?';
                    break;
            }

            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }

            return true;
        });
    </script>

    <style>
        .progress-bar {
            text-align: center;
            line-height: 15px;
            padding-top: 2px;
            font-weight: bold;
        }

        .progress-bar small {
            font-size: 10px;
            line-height: 10px;
            display: block;
        }

        .timeline {
            list-style: none;
            padding: 20px 0 20px;
            position: relative;
        }

        .timeline:before {
            top: 0;
            bottom: 0;
            position: absolute;
            content: " ";
            width: 3px;
            background-color: #eeeeee;
            left: 25px;
            margin-right: -1.5px;
        }

        .timeline>li {
            margin-bottom: 20px;
            position: relative;
        }

        .timeline>li:before,
        .timeline>li:after {
            content: " ";
            display: table;
        }

        .timeline>li:after {
            clear: both;
        }

        .timeline>li>.timeline-item {
            margin-left: 60px;
            margin-right: 15px;
            margin-top: 0;
            padding: 10px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .timeline>li>.fa {
            width: 50px;
            height: 50px;
            font-size: 15px;
            line-height: 50px;
            position: absolute;
            color: #fff;
            background: #d2d6de;
            border-radius: 50%;
            text-align: center;
            left: 0;
            top: 0;
        }

        .time-label>span {
            padding: 5px 10px;
            color: #fff;
            border-radius: 3px;
        }

        .timeline-header {
            border-bottom: 1px solid #f4f4f4;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .timeline-body {
            padding-top: 5px;
        }

        .timeline-footer {
            border-top: 1px solid #f4f4f4;
            padding-top: 5px;
            margin-top: 5px;
        }
    </style>
</body>