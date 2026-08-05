<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."views/admin/modal.php");

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
                <h1></h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#"></a></li>
                    <li class="active">Change Number</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row" style="padding:2%">
                    <div class="pull-left">
                        <div class="pull-right">
                            <div class="col-md-6"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Change <?php echo $flag; ?></h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>EstimateController/update_number">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Old <?php echo $flag; ?> Number</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control input-sm" name="number" id="number" value="<?php echo $number; ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">New <?php echo $flag; ?> Number</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control input-sm" name="new_number" id="new_number" required>
                                                    <input type="hidden" class="form-control input-sm" name="flag" id="flag" value="<?php echo $flag; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <!-- Back button -->
                                        <button type="button" class="btn btn-danger" onclick="goBack()"><i class="glyphicon glyphicon-remove"></i> Close</button>
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

    <script>
        // JavaScript function to navigate back
        function goBack() {
            window.history.back();
        }
    </script>
</body>
