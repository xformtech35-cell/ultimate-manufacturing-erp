<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
    .import-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .upload-box {
        border: 2px dashed #ccc;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        background-color: #f9f9f9;
        margin-bottom: 20px;
    }

    .upload-box:hover {
        border-color: #3c8dbc;
        background-color: #f0f8ff;
    }

    .steps {
        margin: 30px 0;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .step {
        margin-bottom: 15px;
        padding: 10px;
        border-left: 4px solid #3c8dbc;
        background: white;
    }

    .error-list {
        margin-top: 20px;
        padding: 15px;
        background: #ffe6e6;
        border: 1px solid #ff9999;
        border-radius: 5px;
    }

    .template-info {
        background: #e7f3fe;
        border: 1px solid #b3d9ff;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Import Vendors</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SupplierController/index' ?>">Vendors</a></li>
                    <li class="active">Import Vendors</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Import Vendor Details from Excel</h3>
                                <a href="<?php echo base_url() . 'SupplierController/index' ?>" class="btn btn-default btn-sm pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to Vendors
                                </a>
                            </div>

                            <div class="box-body import-container">
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                        <strong>Success!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-danger">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                        <strong>Error!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('IMPORT_ERRORS')) {
                                    $errors = $this->session->flashdata('IMPORT_ERRORS'); ?>
                                    <div class="error-list">
                                        <h4>Import Errors:</h4>
                                        <ul>
                                            <?php foreach ($errors as $error): ?>
                                                <li><?php echo $error; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php } ?>

                                <div class="template-info">
                                    <h4><i class="fa fa-info-circle"></i> Download Template First</h4>
                                    <p>Please download the template file and fill in your vendor data. Then upload the completed file.</p>
                                    <a href="<?php echo base_url() . 'SupplierController/download_vendor_template' ?>" class="btn btn-success">
                                        <i class="fa fa-download"></i> Download Template
                                    </a>
                                </div>

                                <div class="upload-box">
                                    <form action="<?php echo base_url() . 'SupplierController/process_vendor_import' ?>" method="post" enctype="multipart/form-data" id="importForm">
                                        <i class="fa fa-file-excel-o fa-4x" style="color: #217346;"></i>
                                        <h3>Upload Excel File</h3>
                                        <p>Supported formats: .xls, .xlsx, .csv (Max 5MB)</p>

                                        <div class="form-group" style="margin-top: 20px;">
                                            <input type="file" name="vendor_file" id="vendor_file" class="form-control" accept=".xls,.xlsx,.csv" required>
                                        </div>

                                        <div class="form-group" style="margin-top: 20px;">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fa fa-upload"></i> Upload & Import
                                            </button>
                                            <button type="button" class="btn btn-default btn-lg" onclick="window.location.href='<?php echo base_url() . 'SupplierController/index' ?>'">
                                                <i class="fa fa-times"></i> Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="steps">
                                    <h4><i class="fa fa-list-ol"></i> Import Steps:</h4>
                                    <div class="step">
                                        <strong>Step 1:</strong> Download the template file
                                    </div>
                                    <div class="step">
                                        <strong>Step 2:</strong> Fill in vendor data (red fields are required)
                                    </div>
                                    <div class="step">
                                        <strong>Step 3:</strong> Save the file as Excel (.xlsx) format
                                    </div>
                                    <div class="step">
                                        <strong>Step 4:</strong> Upload the file using the form above
                                    </div>
                                    <div class="step">
                                        <strong>Step 5:</strong> Review import results and fix any errors
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h4><i class="fa fa-lightbulb-o"></i> Tips:</h4>
                                    <ul>
                                        <li>Keep the column order as in the template</li>
                                        <li><strong>Company Name</strong> and <strong>Mobile</strong> are required fields</li>
                                        <li>PAN numbers should be exactly 10 characters</li>
                                        <li>GST numbers should be exactly 15 characters</li>
                                        <li>Mobile numbers should be 10 digits</li>
                                        <li>Email should be in valid format (e.g., name@example.com)</li>
                                        <li>State Code should be numeric (e.g., 27 for Maharashtra, 29 for Karnataka)</li>
                                        <li>Duplicate company names will be skipped</li>
                                        <li>Empty rows will be ignored</li>
                                        <li>Vendor Code will be auto-generated (starts from 5000)</li>
                                    </ul>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <h4><i class="fa fa-exclamation-triangle"></i> Column Order in Template:</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Column</th>
                                                <th>Field</th>
                                                <th>Required</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>A</td>
                                                <td>Company Name</td>
                                                <td><span class="label label-danger">Required</span></td>
                                            </tr>
                                            <tr>
                                                <td>B</td>
                                                <td>Contact Person</td>
                                                <td><span class="label label-default">Optional</span></td>
                                            </tr>
                                            <tr>
                                                <td>C</td>
                                                <td>PAN No</td>
                                                <td><span class="label label-default">Optional</span></td>
                                            </tr>
                                            <tr>
                                                <td>D</td>
                                                <td>GST No</td>
                                                <td><span class="label label-default">Optional</span></td>
                                            </tr>
                                            <tr>
                                                <td>E</td>
                                                <td>Email</td>
                                                <td><span class="label label-default">Optional</span></td>
                                            </tr>
                                            <tr>
                                                <td>F</td>
                                                <td>Mobile</td>
                                                <td><span class="label label-danger">Required</span></td>
                                            </tr>
                                            <tr>
                                                <td>G</td>
                                                <td>State Code</td>
                                                <td><span class="label label-default">Optional</span></td>
                                            </tr>
                                            <tr>
                                                <td>H</td>
                                                <td>Address</td>
                                                <td><span class="label label-default">Optional</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // File upload validation
            $('#importForm').submit(function(e) {
                var fileInput = $('#vendor_file')[0];
                var filePath = fileInput.value;
                var allowedExtensions = /(\.xls|\.xlsx|\.csv)$/i;

                if (!allowedExtensions.exec(filePath)) {
                    alert('Please upload file having extensions .xls, .xlsx or .csv only.');
                    return false;
                }

                if (fileInput.files[0].size > 5242880) { // 5MB
                    alert('File size must be less than 5MB');
                    return false;
                }

                return true;
            });

            // Show file name when selected
            $('#vendor_file').change(function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });
        });
    </script>
</body>