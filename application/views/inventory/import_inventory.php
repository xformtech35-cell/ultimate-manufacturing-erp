<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<style>
    .import-card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e0e0;
    }

    .import-steps {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .step-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 5px;
    }

    .step-number {
        width: 30px;
        height: 30px;
        background: white;
        color: #764ba2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-weight: bold;
    }

    .template-info {
        background: #e8f4fd;
        border-left: 4px solid #3498db;
        padding: 15px;
        border-radius: 5px;
        margin: 15px 0;
    }

    .import-section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        margin: 15px 0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .alert-custom {
        border-radius: 8px;
        border-left: 4px solid;
        margin: 15px 0;
    }

    .alert-success-custom {
        border-left-color: #27ae60;
        background: #e8f6ef;
    }

    .alert-info-custom {
        border-left-color: #3498db;
        background: #e8f4fd;
    }

    .alert-warning-custom {
        border-left-color: #f39c12;
        background: #fef9e7;
    }

    .btn-import {
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-import:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
    }

    .btn-template {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-template:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .upload-area {
        border: 2px dashed #3498db;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        background: #e8f4fd;
        border-color: #2980b9;
    }

    .upload-icon {
        font-size: 48px;
        color: #3498db;
        margin-bottom: 15px;
    }

    .requirements-list {
        list-style-type: none;
        padding-left: 0;
    }

    .requirements-list li {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .requirements-list li:before {
        content: "✓";
        color: #27ae60;
        font-weight: bold;
        margin-right: 10px;
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
                    <i class="fa fa-upload"></i> Import Inventory
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/index' ?>">Inventory</a></li>
                    <li class="active">Import Inventory</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Flash Messages -->
                <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
                    <div class="alert alert-success alert-custom alert-success-custom alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check-circle"></i> Success!</h4>
                        <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                        <?php if ($this->session->flashdata('IMPORT_ERRORS')): ?>
                            <br><br>
                            <a href="<?php echo base_url('InventoryController/import_inventory_summary'); ?>"
                                class="btn btn-warning btn-sm">
                                <i class="fa fa-exclamation-triangle"></i> View Import Errors
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('INFOMSG')): ?>
                    <div class="alert alert-info alert-custom alert-info-custom alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-info-circle"></i> Information</h4>
                        <?php echo $this->session->flashdata('INFOMSG'); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="box box-primary import-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-file-excel-o"></i> Import Inventory from Excel</h3>
                            </div>
                            <div class="box-body">
                                <!-- Import Steps -->
                                <div class="import-steps">
                                    <h4><i class="fa fa-list-ol"></i> Import Process</h4>
                                    <div class="step-item">
                                        <div class="step-number">1</div>
                                        <div>Download the Excel template</div>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-number">2</div>
                                        <div>Fill in your inventory data</div>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-number">3</div>
                                        <div>Upload the filled template</div>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-number">4</div>
                                        <div>Review and confirm import</div>
                                    </div>
                                </div>

                                <!-- Template Information -->
                                <div class="template-info">
                                    <h5><i class="fa fa-info-circle"></i> Template Information</h5>
                                    <p>Download our pre-formatted Excel template to ensure your data is imported correctly. The template includes:</p>
                                    <ul class="requirements-list">
                                        <li>All required fields marked with asterisks (*)</li>
                                        <li>Dropdown lists for GST %, Units, etc.</li>
                                        <li>Sample data for reference</li>
                                        <li>Data validation rules</li>
                                    </ul>

                                    <a href="<?php echo base_url('InventoryController/download_inventory_template'); ?>"
                                        class="btn btn-template">
                                        <i class="fa fa-download"></i> Download Template
                                    </a>
                                </div>

                                <!-- Upload Section -->
                                <div class="import-section">
                                    <h4><i class="fa fa-cloud-upload"></i> Upload Your File</h4>
                                    <p class="text-muted">Upload your completed Excel file (.xlsx, .xls, or .csv)</p>

                                    <?php echo form_open_multipart('InventoryController/process_inventory_import', ['id' => 'importForm']); ?>

                                    <div class="file-input-wrapper">
                                        <div class="upload-area" onclick="document.getElementById('inventory_file').click();">
                                            <div class="upload-icon">
                                                <i class="fa fa-cloud-upload"></i>
                                            </div>
                                            <h4>Click to select file or drag and drop</h4>
                                            <p class="text-muted">Supported formats: .xlsx, .xls, .csv (Max 5MB)</p>
                                            <div id="fileInfo" class="text-primary" style="margin-top: 15px; font-weight: 600;"></div>
                                        </div>
                                        <input type="file" name="inventory_file" id="inventory_file"
                                            accept=".xlsx,.xls,.csv" required>
                                    </div>

                                    <div class="form-group" style="margin-top: 20px;">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="update_existing" value="1" checked>
                                                <strong>Update existing items</strong> - If an item with the same code exists, update it
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="skip_errors" value="1">
                                                <strong>Skip rows with errors</strong> - Continue importing valid rows
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-import">
                                            <i class="fa fa-upload"></i> Start Import
                                        </button>
                                        <a href="<?php echo base_url('InventoryController/index'); ?>"
                                            class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to Inventory
                                        </a>
                                    </div>

                                    <?php echo form_close(); ?>
                                </div>

                                <!-- Requirements -->
                                <div class="import-section">
                                    <h4><i class="fa fa-exclamation-triangle"></i> Important Notes</h4>
                                    <div class="alert alert-warning alert-custom alert-warning-custom">
                                        <h5><i class="fa fa-warning"></i> Before Importing:</h5>
                                        <ul>
                                            <li>Ensure all required fields are filled (marked with *)</li>
                                            <li>Item Code must be unique</li>
                                            <li>HSN must be a valid number</li>
                                            <li>GST % should be a number (e.g., 18, 28, 5)</li>
                                            <li>Item Type must be 'B' (Boughtout) or 'M' (Manufacturing)</li>
                                            <li>For categories and groups, use the exact names from the dropdown or the format "Name (ID: X)"</li>
                                            <li>Do not modify the template structure</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Quick Stats -->
                        <div class="box box-success import-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-line-chart"></i> Import Statistics</h3>
                            </div>
                            <div class="box-body">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3><?php

                                            echo $this->inventory->get_total_inventory_count($session_data_head1['result']['user_id']); ?></h3>
                                        <p>Current Inventory Items</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-cubes"></i>
                                    </div>
                                </div>

                                <div class="callout callout-info">
                                    <h5><i class="fa fa-lightbulb-o"></i> Tips for Successful Import</h5>
                                    <ul style="padding-left: 20px; margin-bottom: 0;">
                                        <li>Start with a few test rows</li>
                                        <li>Verify category and group names</li>
                                        <li>Check HSN codes are valid numbers</li>
                                        <li>Review the import summary after completion</li>
                                    </ul>
                                </div>

                                <div class="callout callout-success">
                                    <h5><i class="fa fa-check-circle"></i> Supported Operations</h5>
                                    <ul style="padding-left: 20px; margin-bottom: 0;">
                                        <li>Add new inventory items</li>
                                        <li>Update existing items</li>
                                        <li>Bulk stock updates</li>
                                        <li>Price modifications</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Need Help -->
                        <div class="box box-warning import-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-question-circle"></i> Need Help?</h3>
                            </div>
                            <div class="box-body">
                                <p>Having trouble with the import?</p>
                                <ul style="padding-left: 20px;">
                                    <li>Download and review the template</li>
                                    <li>Check the sample data format</li>
                                    <li>Ensure all required fields are filled</li>
                                    <li>Verify file format is supported</li>
                                </ul>
                                <a href="<?php echo base_url('InventoryController/download_inventory_template'); ?>"
                                    class="btn btn-default btn-block">
                                    <i class="fa fa-download"></i> Get Template Again
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>

    <script>
        $(document).ready(function() {
            // File input change handler
            $('#inventory_file').change(function() {
                var fileInput = $(this)[0];
                if (fileInput.files.length > 0) {
                    var fileName = fileInput.files[0].name;
                    var fileSize = (fileInput.files[0].size / 1024 / 1024).toFixed(2); // MB
                    $('#fileInfo').html('<i class="fa fa-file-excel-o"></i> ' + fileName + ' (' + fileSize + ' MB)');
                } else {
                    $('#fileInfo').html('');
                }
            });

            // Form submission
            $('#importForm').submit(function(e) {
                var fileInput = $('#inventory_file')[0];
                if (!fileInput.files.length) {
                    e.preventDefault();
                    alert('Please select a file to upload.');
                    return false;
                }

                var fileName = fileInput.files[0].name.toLowerCase();
                var validExtensions = ['.xlsx', '.xls', '.csv'];
                var isValid = validExtensions.some(ext => fileName.endsWith(ext));

                if (!isValid) {
                    e.preventDefault();
                    alert('Please upload a valid Excel file (.xlsx, .xls, .csv).');
                    return false;
                }

                // Show loading
                $('button[type="submit"]').html('<i class="fa fa-spinner fa-spin"></i> Importing...').prop('disabled', true);
            });

            // Drag and drop functionality
            $('.upload-area').on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).css('background', '#e8f4fd');
                $(this).css('border-color', '#2980b9');
            });

            $('.upload-area').on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).css('background', '#f8fafc');
                $(this).css('border-color', '#3498db');
            });

            $('.upload-area').on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).css('background', '#f8fafc');
                $(this).css('border-color', '#3498db');

                var files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    $('#inventory_file')[0].files = files;
                    $('#inventory_file').trigger('change');
                }
            });
        });
    </script>
</body>