<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div id="loader" class="center"></div>
<div class="wrapper">

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <h1>AI Model Settings <small>Configure Groq API and Caching parameters</small></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url() . 'AiController/index'; ?>">AI Insights</a></li>
            <li class="active">Settings</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-gears text-primary"></i> Groq Configuration</h3>
                    </div>

                    <form id="settingsForm" class="form-horizontal">
                        <div class="box-body" style="padding: 20px 30px;">
                            
                            <div id="settingsAlert" class="alert" style="display: none;"></div>

                            <div class="form-group">
                                <label for="groq_api_key" class="col-sm-3 control-label">Groq API Key</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-placeholder form-control" id="groq_api_key" name="groq_api_key" 
                                           value="<?= htmlspecialchars($ai_settings['groq_api_key'] ?? '') ?>" 
                                           placeholder="gsk_...">
                                    <small class="text-muted">Enter your Groq API secret key starting with <code>gsk_</code>.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="groq_model" class="col-sm-3 control-label">LLM Model</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="groq_model" name="groq_model">
                                        <option value="llama-3.1-8b-instant" <?= ($ai_settings['groq_model'] ?? '') === 'llama-3.1-8b-instant' ? 'selected' : '' ?>>llama-3.1-8b-instant (Fast & Token Efficient)</option>
                                        <option value="llama-3.3-70b-versatile" <?= ($ai_settings['groq_model'] ?? '') === 'llama-3.3-70b-versatile' ? 'selected' : '' ?>>llama-3.3-70b-versatile (Highly Accurate & Creative)</option>
                                    </select>
                                    <small class="text-muted">Select the model tier to use for insights generation.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cache_expiry" class="col-sm-3 control-label">Cache Duration (Hours)</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="cache_expiry" name="cache_expiry" 
                                           value="<?= htmlspecialchars($ai_settings['cache_expiry'] ?? '24') ?>" min="1" max="168">
                                    <small class="text-muted">Avoid repeat API calls by caching responses (e.g., 24 hours). Insights will regenerate after this threshold.</small>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer" style="padding: 15px 30px;">
                            <a href="<?= base_url() ?>AiController/index" class="btn btn-default">Back to Dashboard</a>
                            <button type="submit" id="saveBtn" class="btn btn-primary pull-right">
                                <i class="fa fa-save"></i> Save Settings
                            </button>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
            </div>
        </div>
        <!-- AI Learned Memories & Training Card -->
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-graduation-cap text-primary"></i> 🤖 AI Learned Memories & Dynamic Rules (Auto-Training)</h3>
                    </div>
                    <div class="box-body">
                        <p class="text-muted" style="margin-bottom: 15px;">
                            This table contains rules, user preferences, and facts the AI has automatically learned from your chats (e.g. commands starting with <i>"remember..."</i>) and your work actions. These facts are dynamically injected into prompt contexts to train the co-pilot.
                        </p>
                        <?php if (empty($learned_memories)) { ?>
                            <div class="text-center text-muted" style="padding: 20px 0;">
                                <i class="fa fa-brain" style="font-size: 30px; color: #d2d6de;"></i>
                                <p style="margin-top: 10px;">The AI has not saved any training memories yet. Try telling the chatbot: <i>"Remember that BOM 123 is urgent"</i></p>
                            </div>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr style="background: #f4f4f4;">
                                            <th>Type</th>
                                            <th>Key Context</th>
                                            <th>Learned Fact / Behavior</th>
                                            <th>Date Captured</th>
                                            <th style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($learned_memories as $m): 
                                            $type_badge = 'label-default';
                                            if ($m['memory_type'] === 'user_preference') $type_badge = 'label-info';
                                            elseif ($m['memory_type'] === 'user_action') $type_badge = 'label-success';
                                        ?>
                                        <tr id="memory-row-<?= $m['id'] ?>">
                                            <td><span class="label <?= $type_badge ?>"><?= htmlspecialchars(strtoupper(str_replace('_', ' ', $m['memory_type']))) ?></span></td>
                                            <td><code><?= htmlspecialchars($m['context_key']) ?></code></td>
                                            <td><strong><?= htmlspecialchars($m['learned_fact']) ?></strong></td>
                                            <td><?= date('d-m-Y h:i A', strtotime($m['created_at'])) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-xs btn-danger delete-memory-btn" data-id="<?= $m['id'] ?>">
                                                    <i class="fa fa-trash"></i> Forget
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Governance & Audit Logs Table -->
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-university text-info"></i> AI Governance & Audit Trail</h3>
                    </div>
                    <div class="box-body">
                        <?php if (empty($governance_logs)) { ?>
                            <div class="text-center text-muted" style="padding: 20px 0;">
                                <i class="fa fa-info-circle" style="font-size: 30px;"></i>
                                <p style="margin-top: 10px;">No AI agent actions have been logged yet.</p>
                            </div>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr style="background: #f4f4f4;">
                                            <th>ID</th>
                                            <th>Action Recommended</th>
                                            <th>Target Record</th>
                                            <th>Recommendation Summary</th>
                                            <th>Human Decision</th>
                                            <th>Execution Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($governance_logs as $log): 
                                            $decision_badge = 'label-default';
                                            if ($log['human_decision'] === 'approved') $decision_badge = 'label-success';
                                            elseif ($log['human_decision'] === 'rejected') $decision_badge = 'label-danger';
                                        ?>
                                        <tr>
                                            <td><?= $log['id'] ?></td>
                                            <td><strong><?= htmlspecialchars($log['action_type']) ?></strong></td>
                                            <td>
                                                <span class="label label-primary"><?= htmlspecialchars(strtoupper($log['module'])) ?></span> 
                                                <?= htmlspecialchars($log['bom_number'] ?? 'ID: ' . $log['record_id']) ?>
                                            </td>
                                            <td><small class="text-muted"><?= htmlspecialchars($log['recommendation_text']) ?></small></td>
                                            <td><span class="label <?= $decision_badge ?>"><?= htmlspecialchars(strtoupper($log['human_decision'])) ?></span></td>
                                            <td><?= !empty($log['executed_at']) ? date('d-m-Y h:i A', strtotime($log['executed_at'])) : '-' ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('admin/footer'); ?>
<div class="control-sidebar-bg"></div>
</div><!-- /.wrapper -->

<script>
$(document).ready(function() {
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#saveBtn');
        var $alert = $('#settingsAlert');
        
        $btn.prop('disabled', true).html('<i class="fa fa-refresh fa-spin"></i> Saving...');
        $alert.hide().removeClass('alert-success alert-danger');

        $.ajax({
            url: '<?= base_url() ?>AiController/ajax_save_settings',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Settings');
                if (response.success) {
                    $alert.addClass('alert-success').html('<i class="fa fa-check"></i> ' + response.message).fadeIn();
                } else {
                    $alert.addClass('alert-danger').html('<i class="fa fa-exclamation-circle"></i> ' + response.message).fadeIn();
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Settings');
                $alert.addClass('alert-danger').html('<i class="fa fa-exclamation-circle"></i> Failed to communicate with server.').fadeIn();
            }
        });
    });

    // Delete memory action
    $(document).on('click', '.delete-memory-btn', function() {
        var id = $(this).data('id');
        var $row = $('#memory-row-' + id);
        
        if (!confirm("Are you sure you want the AI to forget this learned fact?")) {
            return;
        }

        $.ajax({
            url: '<?= base_url() ?>AiController/ajax_delete_memory',
            type: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(500, function() { $(this).remove(); });
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Failed to connect to the server.');
            }
        });
    });
});
</script>
