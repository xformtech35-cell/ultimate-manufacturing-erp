<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Change Password
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Change Password</a></li>
                    <li class="active">Change Password</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Change Password Details</h3>
                                <button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#modal"><i class="glyphicon glyphicon-plus"></i>Change Password</button>
                            
                               

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

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

    </div>
    <!-- ./wrapper -->

    <!-- Change Password modal -->
    <div class="modal fade" id="modal" role="dialog">
        <div class="modal-dialog">

            <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>LoginController/change_password" enctype="multipart/form-data" onsubmit="return checkForm(this);">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Change Password</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body form">
                        <input type="hidden" value="" id="id" name="id"/> 
                        <div class="form-body">

                            <div class="form-group row">
                                <label class="col-sm-3 control-label">Old</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" name="company_name" id="company_name" required="">
                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 control-label">New</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" name="new_password" id="new_password" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-3 control-label">Confirm</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" name="confirm_password" id="confirm_password" required="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="btnSave"  class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </form>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- End Bootstrap modal -->

    <script>
        $(document).ready(function () {
            $("#div_load").fadeOut(5000);
        });

        function checkForm() {
            if (form.old_password.value == form.new_password.value)
            {
                alert("Entrer new password other than old password");
                return false;
            } else {
                if (form.new_password.value == form.confirm_password.value)
                {
                    return true;
                } else
                {
                    alert("Please Enter Correct passwod and it should be same as confirm password");
                    return false;
                }
                return false;
            }
        }
    </script>


