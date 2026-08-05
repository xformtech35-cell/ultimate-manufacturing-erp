<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>

.required label {
font-weight: bold;
}
.required label:after {
color: #e32;
content: '*';
display:inline;
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
                    Roles
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Roles Details</h3>
                               <button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#add_role"><i class="glyphicon glyphicon-plus"></i>Add Role</button>
                            </div>
                            <div class="box-body">
                               
                              


                                <!-- /.box-header -->

                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Role Name</th>
                                           <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($role as $key) { ?>
                                            <tr>
                                                <td><?php echo $key->role_name; ?> </td>
                                               <td> <a  class="btn btn-primary edit_role" data-toggle="modal" data-target="#edit_role" data-id="<?php echo $key->role_id; ?>"><i class="fa fa-edit"></i></a> 
                                                    <a href="<?php echo base_url() . 'RoleController/delete_role/' . $key->role_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"></i></a> 
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>        
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
    </div>


   
    <div class="modal fade" id="add_role" role="dialog">
        <div class="modal-dialog">
            <form class="form-horizontal" id="form_overlay" method="post" action="<?php echo base_url(); ?>RoleController/save_role" enctype="multipart/form-data" onsubmit="return checkForm(this);">
                <div class="modal-content">
                    <div class="modal-header btn-danger">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><center>Add Role</center></h4>
                    </div>
                    <div class="modal-body form">
                        <div class="form-body">
                            <br>
                            <div class="form-group row required">
                                <label class="col-sm-3 control-label">Role Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control form-control-sm" name="role_name"  required="">
                                    <span class="help-block"></span>
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
    </div>

   
    <div class="modal fade dept_window" id="edit_role">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><center>Edit Role</center></h4>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" id="form_overlay" action="<?php echo base_url(); ?>RoleController/edit_role" method="POST" enctype="multipart/form-data">  

                        <div class="modal-body form required">
                            <div class="form-body">
                                <div class="form-group row">
                                    <label class="col-sm-3 control-label">Role Name</label>
                                    <div class="col-sm-7">
                                        <input type="hidden" name="role_id" id="role_id">
                                        <input type="text" class="form-control form-control-sm" name="role_name" id="role_name" required="">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="btnSave"  class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>