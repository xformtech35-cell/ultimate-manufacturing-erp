<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <?php echo $page_title; ?>
            <small>Take action on amendment</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Amendment: <?php echo $amendment['amendment_no']; ?></h3>
                    </div>

                    <div class="box-body">
                        <div class="alert alert-info">
                            <h4><i class="icon fa fa-info"></i> Approval Required</h4>
                            You are required to approve or reject this PO Amendment.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h4>Amendment Details</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Amendment No:</th>
                                        <td><?php echo $amendment['amendment_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>PO Number:</th>
                                        <td><?php echo $amendment['po_number']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amendment Value:</th>
                                        <td>₹<?php echo number_format($amendment['amendment_value'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amendment Type:</th>
                                        <td><?php echo $amendment['amendment_type']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Description:</th>
                                        <td><?php echo $amendment['description']; ?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h4>Your Approval Level</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Approval Level:</th>
                                        <td><?php echo $approval_record['approval_level']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Your Role:</th>
                                        <td><?php echo $approval_record['approver_role']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Action Required:</th>
                                        <td><span class="label label-warning">Pending</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <?php echo form_open('PoamendmentController/process_amendment_approval/' . $amendment_id, array('id' => 'approvalForm')); ?>

                        <div class="form-group">
                            <label>Action *</label><br>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="action" value="approved" checked>
                                    <span class="text-success"><i class="fa fa-check"></i> Approve</span>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="action" value="rejected">
                                    <span class="text-danger"><i class="fa fa-times"></i> Reject</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"
                                placeholder="Enter remarks for your action (required for rejection)"></textarea>
                            <small class="text-muted">Remarks are mandatory when rejecting.</small>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Submit Action
                            </button>
                            <a href="<?php echo site_url('PoamendmentController/view/' . $amendment_id); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Amendment
                            </a>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Show/hide remarks validation based on action
        $('input[name="action"]').change(function() {
            if ($(this).val() === 'rejected') {
                $('#remarks').attr('required', 'required');
            } else {
                $('#remarks').removeAttr('required');
            }
        });

        // Form validation
        $('#approvalForm').submit(function(e) {
            var action = $('input[name="action"]:checked').val();
            var remarks = $('#remarks').val().trim();

            if (action === 'rejected' && remarks === '') {
                e.preventDefault();
                alert('Remarks are required when rejecting.');
                $('#remarks').focus();
            }
        });
    });
</script>