<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<style>
    .summary-card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e0e0;
    }

    .error-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .error-item {
        padding: 10px 15px;
        margin: 5px 0;
        background: #ffe6e6;
        border-left: 4px solid #e74c3c;
        border-radius: 4px;
    }

    .row-highlight {
        background: #fff3cd !important;
        font-weight: bold;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <i class="fa fa-exclamation-triangle"></i> Import Summary
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/index' ?>">Inventory</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/import_inventory_view' ?>">Import</a></li>
                    <li class="active">Import Summary</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-warning summary-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-file-excel-o"></i> Import Errors Summary</h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger">
                                        <h4><i class="icon fa fa-warning"></i> Import Completed with Errors</h4>
                                        <p>The import completed but encountered the following errors:</p>
                                    </div>

                                    <div class="error-list">
                                        <?php foreach ($errors as $index => $error): ?>
                                            <div class="error-item">
                                                <strong>Error <?php echo $index + 1; ?>:</strong> <?php echo $error; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="callout callout-warning" style="margin-top: 20px;">
                                        <h4><i class="fa fa-lightbulb-o"></i> How to fix these errors:</h4>
                                        <ul>
                                            <li>Download the template again</li>
                                            <li>Correct the errors in the mentioned rows</li>
                                            <li>Re-upload the corrected file</li>
                                            <li>The import will update existing items and add new ones</li>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <h4><i class="icon fa fa-info-circle"></i> No Errors Found</h4>
                                        <p>The import completed successfully without any errors.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="box-footer">
                                <a href="<?php echo base_url('InventoryController/import_inventory_view'); ?>"
                                    class="btn btn-warning">
                                    <i class="fa fa-upload"></i> Import Again
                                </a>
                                <a href="<?php echo base_url('InventoryController/index'); ?>"
                                    class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Inventory
                                </a>
                                <a href="<?php echo base_url('InventoryController/download_inventory_template'); ?>"
                                    class="btn btn-primary pull-right">
                                    <i class="fa fa-download"></i> Download Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
</body>