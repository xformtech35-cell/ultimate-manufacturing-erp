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
                    User
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">User</a></li>
                    <li class="active">User Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add User Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>UserController/add_user">
                                    <div class="card-body ">
                                        <!-- form start -->
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">User Name<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm" name="username" id="username" required="" onkeydown="return validate_name(event)">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">User Password<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control input-sm" name="password" id="password" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 control-label">User Role<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="role" id="role" class="form-control input-sm" required>
                                                    <option value="">Select Role</option>
                                                    <?php if (!empty($role)) {
                                                        foreach ($role as $r) { ?>
                                                            <option value="<?= $r->role_id; ?>"><?= $r->role_name; ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 control-label">Department<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm company_search_name" name="department_id_fk" id="department_id_fk" required="">
                                                    <option value="">Select Department</option>
                                                    <?php foreach ($department_result as $key) { ?>
                                                        <option value="<?php echo $key->department_id; ?>"><?php echo $key->department_name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 control-label">Location<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="location_id" id="location_id" required="">
                                                    <option value="">Select Location</option>
                                                    <?php foreach ($location_result as $location) { ?>
                                                        <option value="<?php echo $location->location_id; ?>"><?php echo $location->location_name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">User Email<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control input-sm" name="user_email" id="user_email" required="" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer small text-muted">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
                                </form>

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

    <!-- ./Customer modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Add User <button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>UserController/add_user" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">User Name<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="username" id="username" required="" onkeydown="return validate_name(event)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">User Password<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control input-sm" name="password" id="password" required="">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">User Password<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select name="role" id="role" class="form-control input-sm" required>
                                        <option value="">Select Role</option>
                                        <?php if (!empty($role)) {
                                            foreach ($role as $r) { ?>
                                                <option value="<?= $r->role_id; ?>">
                                                    <?= $r->role_name; ?>
                                                </option>
                                        <?php }
                                        } ?>
                                    </select>

                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">User Email<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="email" class="form-control input-sm" name="user_email" id="user_email" required="" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>

            </div>

        </div>
    </div>