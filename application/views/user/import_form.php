<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <div class="row">
                    <div class="col-md-6">
                        <h1>
                            <i class="fa fa-upload"></i> Import Users
                        </h1>
                    </div>
                    <div class="col-md-6">
                        <ol class="breadcrumb pull-right">
                            <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                            <li><a href="<?php echo base_url() . 'UserController/index' ?>">Users</a></li>
                            <li class="active">Import Users</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-file-excel-o"></i> Import Users from File</h3>
                                <div class="box-tools pull-right">
                                    <a href="<?php echo base_url(); ?>UserController/index" class="btn btn-default btn-sm">
                                        <i class="fa fa-arrow-left"></i> Back to Users
                                    </a>
                                </div>
                            </div>

                            <div class="box-body">
                                <!-- Flash Messages -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h4><i class="icon fa fa-check"></i> Success!</h4>
                                        <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('ERRORMSG')) { ?>
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                        <?= $this->session->flashdata('ERRORMSG') ?>
                                    </div>
                                <?php } ?>

                                <!-- Import Form -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-upload"></i> Upload File</h4>
                                    </div>
                                    <div class="panel-body">
                                        <form action="<?php echo base_url(); ?>UserController/import_csv" method="post" enctype="multipart/form-data" id="importForm">
                                            <div class="form-group">
                                                <label for="csv_file"><i class="fa fa-file"></i> Select File</label>
                                                <div class="input-group">
                                                    <input type="file" name="csv_file" id="csv_file" class="form-control" required accept=".csv,.xls,.xlsx">
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-info" onclick="document.getElementById('csv_file').click()">
                                                            <i class="fa fa-folder-open"></i> Browse
                                                        </button>
                                                    </span>
                                                </div>
                                                <p class="help-block">
                                                    <i class="fa fa-info-circle"></i>
                                                    Supported formats: CSV, Excel (.xls, .xlsx). Max file size: 5MB
                                                </p>
                                            </div>

                                            <div class="form-group">
                                                <label><i class="fa fa-cog"></i> Import Options</label>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" name="import_mode" value="insert" checked>
                                                                <strong>Insert Only</strong><br>
                                                                <small class="text-muted">Skip existing users</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" name="import_mode" value="update">
                                                                <strong>Update Only</strong><br>
                                                                <small class="text-muted">Update existing users</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" name="import_mode" value="both">
                                                                <strong>Insert & Update</strong><br>
                                                                <small class="text-muted">Both insert and update</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success btn-lg" id="importBtn">
                                                    <i class="fa fa-upload"></i> Start Import
                                                </button>
                                                <a href="<?php echo base_url(); ?>UserController/index" class="btn btn-default">
                                                    <i class="fa fa-times"></i> Cancel
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Resources Section -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-download"></i> Download Resources</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="well">
                                                    <h5><i class="fa fa-file-excel-o text-success"></i> Download Template</h5>
                                                    <p>Download the template file with correct format</p>
                                                    <a href="<?php echo base_url(); ?>UserController/download_template" class="btn btn-success btn-block">
                                                        <i class="fa fa-download"></i> Download Template (CSV)
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="well">
                                                    <h5><i class="fa fa-database text-primary"></i> Export Current Data</h5>
                                                    <p>Export existing users for reference</p>
                                                    <div class="btn-group btn-block">
                                                        <button type="button" class="btn btn-primary dropdown-toggle btn-block" data-toggle="dropdown">
                                                            <i class="fa fa-download"></i> Export Current Users <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu">
                                                            <li><a href="<?php echo base_url(); ?>UserController/export_excel"><i class="fa fa-file-excel-o"></i> Excel Format</a></li>
                                                            <li><a href="<?php echo base_url(); ?>UserController/export_csv"><i class="fa fa-file-text-o"></i> CSV Format</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Format Instructions -->
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-info-circle"></i> File Format Instructions</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr class="bg-warning">
                                                        <th width="10%">Column</th>
                                                        <th width="25%">Description</th>
                                                        <th width="10%">Required</th>
                                                        <th width="55%">Notes & Examples</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong>A</strong></td>
                                                        <td>User ID</td>
                                                        <td><span class="label label-default">Optional</span></td>
                                                        <td>Leave empty for new users. Fill with existing ID to update</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>B</strong></td>
                                                        <td>Username</td>
                                                        <td><span class="label label-danger">Required</span></td>
                                                        <td>Full name of the user. Example: <code>John Doe</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>C</strong></td>
                                                        <td>Email</td>
                                                        <td><span class="label label-danger">Required</span></td>
                                                        <td>Must be unique and valid email. Example: <code>john@example.com</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>D</strong></td>
                                                        <td>Role</td>
                                                        <td><span class="label label-danger">Required</span></td>
                                                        <td>Must match existing role names exactly. Example: <code>Admin</code>, <code>User</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>E</strong></td>
                                                        <td>Department</td>
                                                        <td><span class="label label-default">Optional</span></td>
                                                        <td>Must match existing department names. Example: <code>IT Department</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>F</strong></td>
                                                        <td>Location</td>
                                                        <td><span class="label label-default">Optional</span></td>
                                                        <td>Must match existing location names exactly. Available locations: <code>Bhatinda</code>, <code>Dahej</code>, <code>Mohali</code>, <code>Vapi</code>, <code>Pune</code>, <code>Mumbai</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>G</strong></td>
                                                        <td>Created Date</td>
                                                        <td><span class="label label-default">Optional</span></td>
                                                        <td>Format: <code>YYYY-MM-DD</code>. Leave empty for current date</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="alert alert-info">
                                            <h5><i class="fa fa-lightbulb-o"></i> Tips for Successful Import:</h5>
                                            <ol>
                                                <li>Use the template file to ensure correct format</li>
                                                <li>New users will get default password: <code>password123</code></li>
                                                <li>Check your file for duplicates before importing</li>
                                                <li>Make sure role, department, and location names match existing records exactly</li>
                                                <li>Backup your data before performing bulk imports</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
    </div>

    <style>
        .panel {
            margin-bottom: 20px;
        }

        .panel-heading {
            padding: 10px 15px;
        }

        .well {
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }

        .label {
            padding: 3px 8px;
            font-size: 11px;
        }

        .bg-warning {
            background-color: #fcf8e3 !important;
        }

        code {
            background-color: #f9f2f4;
            color: #c7254e;
            padding: 2px 4px;
            border-radius: 4px;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Form submission handler
            $('#importForm').submit(function(e) {
                var fileInput = $('#csv_file');
                var file = fileInput[0].files[0];

                if (!file) {
                    alert('Please select a file to upload.');
                    return false;
                }

                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit.');
                    return false;
                }

                // Check file extension
                var validExtensions = ['.csv', '.xls', '.xlsx'];
                var fileName = file.name.toLowerCase();
                var isValid = validExtensions.some(function(ext) {
                    return fileName.endsWith(ext);
                });

                if (!isValid) {
                    alert('Invalid file type. Please upload CSV or Excel files only.');
                    return false;
                }

                // Show loading
                $('#importBtn').html('<i class="fa fa-spinner fa-spin"></i> Importing...').prop('disabled', true);
                return true;
            });

            // File input change handler
            $('#csv_file').change(function() {
                var fileName = $(this).val().split('\\').pop();
                if (fileName) {
                    $(this).next('.input-group-btn').find('.btn').html('<i class="fa fa-file"></i> ' + fileName);
                }
            });
        });
    </script>
</body>