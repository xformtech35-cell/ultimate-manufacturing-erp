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
                     Category
                </h1>
<!--                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Ticket Category</a></li>
                    <li class="active">Add Ticket Category Details</li>
                </ol>-->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Add Category</h3><br><br>
                                <span id="error" style="color:red;display:none">Plese Enter Only Alphabets...</span>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Ooh!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>


                                <form class="form-horizontal" id="form_overlay" method="post" action="<?php echo base_url(); ?>MasterController/add_category">

                                    <div class="modal-body">

                                        <div class="card-body ">
                                            <!-- form start -->
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label"> Category</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm"  name="category_name" maxlength="40" style="text-transform:uppercase" required="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <!--                        <button type="reset" class="btn btn-danger"> Reset</button>-->
                                        <button type="submit" id="btnSave"  class="btn btn-success pull-left">Submit</button>

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
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->


