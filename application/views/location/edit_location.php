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
                    Edit Location
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'LocationController/index' ?>">Locations</a></li>
                    <li class="active">Edit Location</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Location Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url('LocationController/update_location'); ?>" enctype="multipart/form-data">
                                    <input type="hidden" name="location_id" value="<?php echo $location->location_id; ?>">

                                    <div class="modal-body">
                                        <div class="card-body ">

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Location Name<span style="color: red;">*</span></label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="location_name" value="<?php echo $location->location_name; ?>" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Address</label>
                                                <div class="col-sm-5">
                                                    <textarea class="form-control" name="address" rows="2"><?php echo $location->address; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">City</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="city" value="<?php echo $location->city; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">State</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="state" value="<?php echo $location->state; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Country</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="country" value="<?php echo $location->country; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Zip Code</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="zip_code" value="<?php echo $location->zip_code; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Phone</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="phone" value="<?php echo $location->phone; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-5">
                                                    <input type="email" class="form-control" name="email" value="<?php echo $location->email; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-5">
                                                    <button type="submit" class="btn btn-success">Update Location</button>
                                                    <a href="<?php echo base_url() . 'LocationController/index' ?>" class="btn btn-default">Cancel</a>
                                                </div>
                                            </div>

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
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->