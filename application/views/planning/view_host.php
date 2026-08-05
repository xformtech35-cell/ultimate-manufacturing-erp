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
                    Host
                </h1>
<!--                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Location</a></li>
                    <li class="active">Add Location Details</li>
                </ol>-->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Host</h3><br><br>
                                <span id="error" style="color:red;display:none">Plese Enter Only Alphabets...</span>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Sorry!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal" id="form_overlay" method="post" action="<?php echo base_url(); ?>DepartmentController/add_host">
                                    <div class="modal-body">
                                        <div class="card-body ">
                                            <!-- form start -->
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Employee Name</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm" name="employee_name" id="employee_name" maxlength="30" style="text-transform:uppercase" required=""/>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label"> Employee Id</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm"  name="employee_identity" maxlength="10" style="text-transform:uppercase" id="employee_identity">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label"> Employee Mobile</label>
                                                <div class="col-sm-7">                                               
                                                    <input type="tel" pattern="[789][0-9]{9}" class="form-control input-sm" name="employee_mobile" id="employee_mobile" minlength="10" maxlength="10" required=""  
                                                           onkeyup="if (/\D/g.test(this.value))
                                                                       this.value = this.value.replace(/\D/g, '')"/>                                             
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Employee     Email</label>
                                                <div class="col-sm-7">
                                                    <input type="email" class="form-control input-sm"  name="employee_mail" id="employee_mail" style="text-transform:uppercase"
                                                           pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" required="">
                                                </div>

                                            </div>
                                          
                                    </div>
                                    <div class="modal-footer">
                                        <!--                        <button type="reset" class="btn btn-danger"> Reset</button>-->
                                        <button type="submit" id="btnSave"  class="btn btn-success">Submit</button>
                                    </div>
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
                
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="example" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Employee Id</th>
                                            <th>Employee Mobile</th>
                                            <th>Employee Mail</th>
                                            
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($employee as $key) {   ?>
                                            <tr>
                                                <td> <?php echo $key->employee_name; ?> </td>
                                                 <td> <?php echo $key->employee_identity; ?> </td>
                                                 <td> <?php echo $key->employee_mobile; ?> </td>
                                                 <td> <?php echo $key->employee_mail; ?> </td>
                                                <td> 
                                                    <a href="<?php echo base_url() . 'DepartmentController/get_host_by_id/' . $key->employee_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-edit"></i></a> 
                                                 <a href="<?php echo base_url() . 'DepartmentController/delete_host_by_id/' . $key->employee_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"></i></a> </td>
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
            </section>
            <!-- /.content -->
        </div>
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->


