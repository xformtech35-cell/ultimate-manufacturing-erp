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
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Import Users
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'UserController/index' ?>">Users</a></li>
                    <li class="active">Import Users</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Import Users from File</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <!-- Start Flash Message -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Success!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('ERRORMSG')) { ?>
                                    <div role="alert" class="alert alert-danger">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Error!</strong> <?= $this->session->flashdata('ERRORMSG') ?>
                                    </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Import Users</h3>
                                            </div>
                                            <div class="panel-body">
                                                <form action="<?php echo base_url(); ?>UserController/import_csv" method="post" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label for="csv_file">Select File</label>
                                                        <input type="file" name="csv_file" id="csv_file" class="form-control" required accept=".xls,.xlsx,.csv">
                                                        <p class="help-block">Supported formats: Excel (.xls, .xlsx) or CSV (.csv)</p>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Import Mode</label>
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" name="import_mode" value="insert" checked>
                                                                Insert Only (Skip existing users)
                                                            </label>
                                                        </div>
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" name="import_mode" value="update">
                                                                Update Only (Update existing users)
                                                            </label>
                                                        </div>
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" name="import_mode" value="both">
                                                                Insert & Update
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fa fa-upload"></i> Import Users
                                                        </button>
                                                        <a href="<?php echo base_url(); ?>UserController/index" class="btn btn-default">
                                                            <i class="fa fa-arrow-left"></i> Back to Users
                                                        </a>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Download Resources</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="list-group">
                                                    <a href="<?php echo base_url(); ?>UserController/download_template" class="list-group-item">
                                                        <i class="fa fa-download"></i> Download Import Template (CSV)
                                                    </a>
                                                    <a href="<?php echo base_url(); ?>UserController/export_excel" class="list-group-item">
                                                        <i class="fa fa-download"></i> Download Current Users (Excel)
                                                    </a>
                                                    <a href="<?php echo base_url(); ?>UserController/export_csv" class="list-group-item">
                                                        <i class="fa fa-download"></i> Download Current Users (CSV)
                                                    </a>
                                                </div>

                                                <div class="alert alert-info">
                                                    <h4><i class="icon fa fa-info"></i> Import Instructions</h4>
                                                    <ol>
                                                        <li>Download the import template</li>
                                                        <li>Fill in the user data (columns A-G)</li>
                                                        <li>Save the file</li>
                                                        <li>Upload using the form on the left</li>
                                                    </ol>
                                                    <p><strong>Note:</strong> New users will get default password "password123"</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-warning">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">File Format Specifications</h3>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Column</th>
                                                            <th>Description</th>
                                                            <th>Required</th>
                                                            <th>Notes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>A</td>
                                                            <td>User ID</td>
                                                            <td>No</td>
                                                            <td>Leave empty for new users. Fill with existing ID to update</td>
                                                        </tr>
                                                        <tr>
                                                            <td>B</td>
                                                            <td>Username</td>
                                                            <td>Yes</td>
                                                            <td>Full name of the user</td>
                                                        </tr>
                                                        <tr>
                                                            <td>C</td>
                                                            <td>Email</td>
                                                            <td>Yes</td>
                                                            <td>Must be unique and valid email format</td>
                                                        </tr>
                                                        <tr>
                                                            <td>D</td>
                                                            <td>Role</td>
                                                            <td>Yes</td>
                                                            <td>Must match existing role names exactly</td>
                                                        </tr>
                                                        <tr>
                                                            <td>E</td>
                                                            <td>Department</td>
                                                            <td>No</td>
                                                            <td>Must match existing department names exactly</td>
                                                        </tr>
                                                        <tr>
                                                            <td>F</td>
                                                            <td>Location</td>
                                                            <td>No</td>
                                                            <td>Must match existing location names exactly (Bhatinda, Dahej, Mohali, Vapi, Pune, Mumbai)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>G</td>
                                                            <td>Created Date</td>
                                                            <td>No</td>
                                                            <td>Format: YYYY-MM-DD (Default: today's date)</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
</body>