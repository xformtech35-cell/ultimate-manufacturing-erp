<div class="container">
    <div id="loader" class="center"></div>

    <h2>Test Approval Process</h2>

    <?php foreach ($pending_approvals as $approval): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>PR: <?php echo $approval->pr_no ?? $approval->pr_id; ?></strong>
                - <?php echo $approval->department_name; ?>
            </div>
            <div class="panel-body">
                <p>Requested by: <?php echo $approval->requester_name; ?></p>
                <p>Date: <?php echo date('d-m-Y', strtotime($approval->pr_date)); ?></p>

                <form action="<?php echo base_url('RequisitionController/process_approval'); ?>" method="post">
                    <input type="hidden" name="pr_id" value="<?php echo $approval->pr_id; ?>">

                    <div class="form-group">
                        <textarea name="comments" class="form-control" placeholder="Comments..."></textarea>
                    </div>

                    <button type="submit" name="action" value="Approved" class="btn btn-success">Approve</button>
                    <button type="submit" name="action" value="Rejected" class="btn btn-danger">Reject</button>
                    <button type="submit" name="action" value="Returned" class="btn btn-warning">Return</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($pending_approvals)): ?>
        <div class="alert alert-info">No pending approvals to test.</div>
    <?php endif; ?>
</div>