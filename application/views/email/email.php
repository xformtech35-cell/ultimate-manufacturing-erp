<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
     <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Email Settings
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Email Setting</a></li>
                    <li class="active">Email Setting Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">

                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>EmailController/add_email_settings" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <div class="box-header">
                                            <h3 class="box-title">Email settings</h3>
                                        </div>

                                        <div class="form-group row"> 
                                            <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                                <div role="alert" class="alert alert-success">
                                                    <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                                    <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <!-- form start -->
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Company Logo  <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="hidden" value="<?php echo $email_set['email_setting_id']; ?>" class="form-control input-sm" name="email_setting_id" id="email_setting_id">
                                                <input type="file" class="form-control input-sm" name="company_logo" id="company_logo" value="<?php echo $email_set['company_logo']; ?>" >
                                                <img src="<?php echo base_url() . $email_set['company_logo']; ?>" width="20%" height="20%"/>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Company Name  <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $email_set['company_name']; ?>" class="form-control input-sm" name="company_name" id="company_name" required="">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Company Website  <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $email_set['company_website']; ?>" class="form-control input-sm" name="company_website" id="company_website" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">From  Email  <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="email" value="<?php echo $email_set['from_email']; ?>" class="form-control input-sm" name="from_email" id="from_email" required="" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$"/>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Gmail Password<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="password" value="<?php echo $email_set['password_email']; ?>" class="form-control input-sm" name="password_email" id="password_email" required="" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Copy Email  <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="email" value="<?php echo $email_set['cc_email']; ?>" class="form-control input-sm" name="cc_email" id="cc_email" required="" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$"/>
                                            </div>
                                        </div>
                                        
                                        
                                        <br>
                                        <center> <label><h4><b><span style="color: red">*</span>  (Gmail settings are necessary to send Email).</b></h4></label></center><br>
                                        
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

        <div class="control-sidebar-bg"></div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
    

