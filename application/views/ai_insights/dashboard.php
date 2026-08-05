<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div id="loader" class="center"></div>
<div class="wrapper">

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <h1>AI Insights Dashboard <small>Cross-module business analytics and anomaly detection</small></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">AI Insights</li>
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
                        <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
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
                        <span class="progress-description">Drafts stuck > 7 days</span>
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
                        <span class="progress-description">MRP runs on unapproved drafts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Executive Summary Box -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid bg-purple-active">
                    <div class="box-header">
                        <i class="fa fa-android"></i>
                        <h3 class="box-title">AI Executive Summary Insights</h3>
                    </div>
                    <div class="box-body" style="background: #fafafa; color: #333; border-top: 1px solid #d2d6de; padding: 15px;">
                        <p style="font-size: 14px; line-height: 1.6; margin: 0; font-weight: 500;">
                            <?= nl2br(htmlspecialchars($ai_executive_summary)) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Chart -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Draft BOM Age Distribution</h3>
                    </div>
                    <div class="box-body">
                        <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                            <canvas id="ageDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Module Link Grid -->
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-th"></i> AI Triage Modules</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6" style="margin-bottom: 15px;">
                                <a href="<?php echo base_url(); ?>AiController/bom_triage" class="btn btn-block btn-lg btn-default" style="padding: 20px; border-left: 5px solid #00a65a; text-align: left;">
                                    <h4 style="margin: 0 0 5px 0; font-weight: 600;"><i class="fa fa-magic text-success"></i> BOM & MRP</h4>
                                    <small class="text-muted">Draft triage & anomaly checks</small>
                                </a>
                            </div>
                            <div class="col-md-6" style="margin-bottom: 15px;">
                                <a href="<?php echo base_url(); ?>AiController/chat" class="btn btn-block btn-lg btn-default" style="padding: 20px; border-left: 5px solid #00c0ef; text-align: left;">
                                    <h4 style="margin: 0 0 5px 0; font-weight: 600;"><i class="fa fa-comments text-info"></i> Ask AI Bot</h4>
                                    <small class="text-muted">Natural language queries</small>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="margin-bottom: 15px;">
                                <a href="javascript:void(0);" class="btn btn-block btn-lg btn-default disabled" style="padding: 20px; border-left: 5px solid #dd4b39; text-align: left; opacity: 0.6; cursor: not-allowed;" title="Coming soon">
                                    <h4 style="margin: 0 0 5px 0; font-weight: 600;"><i class="fa fa-shopping-cart text-danger"></i> Purchase AI <span class="label label-warning" style="font-size: 10px; vertical-align: middle;">SOON</span></h4>
                                    <small class="text-muted">Vendor delays & PO insights</small>
                                </a>
                            </div>
                            <div class="col-md-6" style="margin-bottom: 15px;">
                                <a href="<?php echo base_url(); ?>AiController/settings" class="btn btn-block btn-lg btn-default" style="padding: 20px; border-left: 5px solid #605ca8; text-align: left;">
                                    <h4 style="margin: 0 0 5px 0; font-weight: 600;"><i class="fa fa-gears text-purple"></i> Settings</h4>
                                    <small class="text-muted">API key & cache config</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('admin/footer'); ?>
<div class="control-sidebar-bg"></div>
</div><!-- /.wrapper -->

<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    var ctx = document.getElementById('ageDistributionChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'BOM Count',
                data: <?= json_encode($chart_data) ?>,
                backgroundColor: [
                    'rgba(0, 192, 239, 0.7)',
                    'rgba(0, 166, 90, 0.7)',
                    'rgba(243, 156, 18, 0.7)',
                    'rgba(245, 105, 84, 0.7)',
                    'rgba(144, 48, 144, 0.7)'
                ],
                borderColor: [
                    'rgba(0, 192, 239, 1)',
                    'rgba(0, 166, 90, 1)',
                    'rgba(243, 156, 18, 1)',
                    'rgba(245, 105, 84, 1)',
                    'rgba(144, 48, 144, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
