<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div id="loader" class="center"></div>
<div class="wrapper">

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <h1>AI BOM & MRP Triage <small>Identify and resolve bottlenecks in your BOM workflow</small></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url() . 'AiController/index'; ?>">AI Insights</a></li>
            <li class="active">BOM Triage</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Top KPI Info Boxes -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Drafts</span>
                        <span class="info-box-number"><?= $total_drafts ?></span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">All pending draft BOMs</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Stale Drafts</span>
                        <span class="info-box-number"><?= $stale_count ?></span>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= $total_drafts > 0 ? ($stale_count / $total_drafts * 100) : 0 ?>%"></div>
                        </div>
                        <span class="progress-description">Sitting in draft > 7 days</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-folder-open-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Empty BOMs</span>
                        <span class="info-box-number"><?= $empty_count ?></span>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= $total_drafts > 0 ? ($empty_count / $total_drafts * 100) : 0 ?>%"></div>
                        </div>
                        <span class="progress-description">BOMs with 0 components</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-orange">
                    <span class="info-box-icon"><i class="fa fa-cogs"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Process Anomalies</span>
                        <span class="info-box-number"><?= $anomaly_count ?></span>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= $total_drafts > 0 ? ($anomaly_count / $total_drafts * 100) : 0 ?>%"></div>
                        </div>
                        <span class="progress-description">MRP run on unapproved drafts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Triage Summary Box -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-12">
                <div class="box box-solid bg-purple-active">
                    <div class="box-header">
                        <i class="fa fa-android"></i>
                        <h3 class="box-title">AI Executive Triage Summary</h3>
                    </div>
                    <div class="box-body" style="background: #fafafa; color: #333; border-top: 1px solid #d2d6de; padding: 15px;">
                        <p style="font-size: 14px; line-height: 1.6; margin: 0; font-weight: 500;">
                            <?= nl2br(htmlspecialchars($ai_highlights)) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Triage Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-magic text-primary"></i> Intelligent Bottleneck Analysis</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url() . 'BomController/index'; ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-list"></i> View All BOMs
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if (empty($triaged_boms)) { ?>
                            <div class="text-center" style="padding: 40px 0;">
                                <i class="fa fa-check-circle text-success" style="font-size: 50px;"></i>
                                <h4 style="margin-top: 15px; color: #888;">No Draft BOMs detected. Your workflow is completely clear!</h4>
                            </div>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table id="triageTable" class="table table-bordered table-striped table-hover">
                                    <thead style="background: #f4f4f4;">
                                        <tr>
                                            <th>#</th>
                                            <th>BOM Number</th>
                                            <th>Customer & System</th>
                                            <th>Age (Days)</th>
                                            <th>Stall Reason</th>
                                            <th>AI Recommendation</th>
                                            <th>AI Insight</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach ($triaged_boms as $bom): 
                                            $severity_class = 'info';
                                            if ($bom['triage_severity'] === 'danger') $severity_class = 'danger';
                                            elseif ($bom['triage_severity'] === 'warning') $severity_class = 'warning';
                                        ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td>
                                                <a href="<?= base_url() . 'BomController/show_bom/' . $bom['id'] ?>">
                                                    <strong><?= htmlspecialchars($bom['number_fk']) ?></strong>
                                                </a>
                                                <?php if ($bom['send_to_mrp'] == 2): ?>
                                                    <br><span class="label label-success">MRP Run</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars(!empty($bom['company_name']) ? $bom['company_name'] : ($bom['fullname'] ?? 'Unassigned Customer')) ?>
                                                <br><small class="text-muted">System: <?= htmlspecialchars($bom['system'] ?: 'Not Specified') ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?= $bom['days_stale'] > 7 ? 'bg-red' : 'bg-blue' ?>">
                                                    <?= $bom['days_stale'] ?> days
                                                </span>
                                            </td>
                                            <td>
                                                <div style="font-size: 13px;">
                                                    <?php foreach ($bom['triage_reasons'] as $reason): ?>
                                                        <div class="text-<?= $severity_class ?>" style="margin-bottom: 3px;">
                                                            <i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars($reason) ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 13px; font-weight: 500;">
                                                    <?php foreach ($bom['triage_actions'] as $action): ?>
                                                        <div class="text-success" style="margin-bottom: 3px;">
                                                            <i class="fa fa-arrow-circle-right"></i> <?= htmlspecialchars($action) ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div id="insight-container-<?= $bom['id'] ?>" style="min-width: 150px; font-size: 12px;">
                                                    <button type="button" class="btn btn-xs btn-info get-ai-insight-btn" data-id="<?= $bom['id'] ?>">
                                                        <i class="fa fa-android"></i> Ask AI
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                                        Action <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        <li><a href="<?= base_url() . 'BomController/submit_bom_for_approval/' . $bom['id'] ?>" onclick="return confirm('Submit this BOM for Sales Approval?')"><i class="fa fa-paper-plane text-info"></i> Submit for Approval</a></li>
                                                        <li><a href="<?= base_url() . 'BomController/edit_bom_details/' . $bom['id'] ?>"><i class="fa fa-edit text-warning"></i> Edit BOM</a></li>
                                                        <li><a href="<?= base_url() . 'BomController/show_bom/' . $bom['id'] ?>"><i class="fa fa-eye text-primary"></i> View Details</a></li>
                                                        <li class="divider"></li>
                                                        <li><a href="<?= base_url() . 'Pdf/print_bom/' . $bom['id'] ?>" target="_blank"><i class="fa fa-file-pdf-o"></i> Export PDF</a></li>
                                                    </ul>
                                                </div>
                                            </td>
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

<script>
$(document).ready(function() {
    $('#triageTable').DataTable({
        "order": [[ 3, "desc" ]], // Order by Age descending by default
        "pageLength": 25,
        "language": {
            "search": "Search BOM Triage:"
        }
    });

    // Handle AJAX request for single BOM AI Insight
    $(document).on('click', '.get-ai-insight-btn', function() {
        var $btn = $(this);
        var bomId = $btn.data('id');
        var $container = $('#insight-container-' + bomId);

        // Show spinner loader state
        $container.html('<span class="text-muted"><i class="fa fa-refresh fa-spin"></i> Analyzing...</span>');

        $.ajax({
            url: '<?= base_url() ?>AiController/ajax_get_bom_ai_insight',
            type: 'POST',
            dataType: 'json',
            data: { bom_id: bomId },
            success: function(response) {
                if (response.success) {
                    var html = '<div class="ai-insight-bubble" style="background: #f0f4f8; padding: 10px; border-left: 4px solid #00c0ef; border-radius: 4px; line-height: 1.4; color: #333; margin-bottom: 8px;">' + response.insight + '</div>';
                    
                    // Render action confirmation interface if BOM is fully valid for submission
                    if (response.can_submit) {
                        html += '<div style="margin-top: 5px;" class="action-btn-group">' +
                                '  <button class="btn btn-xs btn-success execute-action-btn" data-id="' + response.bom_id + '" data-action="submit_bom_for_approval" data-rec="' + response.insight + '"><i class="fa fa-check"></i> Approve & Submit</button>' +
                                '  <button class="btn btn-xs btn-default reject-action-btn" data-id="' + response.bom_id + '" data-rec="' + response.insight + '"><i class="fa fa-times text-danger"></i> Dismiss</button>' +
                                '</div>';
                    }
                    $container.html(html);
                } else {
                    $container.html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Error: ' + response.message + '</span>');
                }
            },
            error: function() {
                $container.html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Failed to connect.</span>');
            }
        });
    });

    // Execute recommended action (Submit BOM for Sales Approval workflow)
    $(document).on('click', '.execute-action-btn', function() {
        var $btn = $(this);
        var bomId = $btn.data('id');
        var action = $btn.data('action');
        var rec = $btn.data('rec');
        var $btnGroup = $btn.closest('.action-btn-group');

        if (!confirm("Are you sure you want to execute the AI recommendation and submit this BOM for Sales Approval?")) {
            return;
        }

        $btnGroup.html('<span class="text-muted"><i class="fa fa-refresh fa-spin"></i> Executing Action...</span>');

        $.ajax({
            url: '<?= base_url() ?>AiController/ajax_execute_agentic_action',
            type: 'POST',
            dataType: 'json',
            data: { bom_id: bomId, action: action, recommendation: rec },
            success: function(response) {
                if (response.success) {
                    $btnGroup.html('<span class="text-success" style="font-weight: 500;"><i class="fa fa-check-circle"></i> Action Executed & Logged!</span>');
                    // Reload window after 1.5 seconds to update table rows status
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $btnGroup.html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Error: ' + response.message + '</span>');
                }
            },
            error: function() {
                $btnGroup.html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Connection failed.</span>');
            }
        });
    });

    // Reject/Dismiss recommendation (logs decision to governance audit log)
    $(document).on('click', '.reject-action-btn', function() {
        var $btn = $(this);
        var bomId = $btn.data('id');
        var rec = $btn.data('rec');
        var $btnGroup = $btn.closest('.action-btn-group');

        $btnGroup.html('<span class="text-muted"><i class="fa fa-refresh fa-spin"></i> Dismissing...</span>');

        $.ajax({
            url: '<?= base_url() ?>AiController/ajax_reject_agentic_action',
            type: 'POST',
            dataType: 'json',
            data: { bom_id: bomId, recommendation: rec },
            success: function(response) {
                if (response.success) {
                    $btnGroup.html('<span class="text-danger" style="font-weight: 500;"><i class="fa fa-ban"></i> Recommendation Dismissed</span>');
                    setTimeout(function() {
                        $btnGroup.fadeOut();
                    }, 1200);
                } else {
                    $btnGroup.html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Error: ' + response.message + '</span>');
                }
            },
            error: function() {
                $btnGroup.html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Connection failed.</span>');
            }
        });
    });
});
</script>
