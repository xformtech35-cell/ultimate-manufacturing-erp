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
                    Manage Locations
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Location Management</a></li>
                    <li class="active">Add/View Locations</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add New Location</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url('LocationController/add_location'); ?>" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <div class="card-body ">

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Location Name<span style="color: red;">*</span></label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="location_name" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Address</label>
                                                <div class="col-sm-5">
                                                    <textarea class="form-control" name="address" rows="2"></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">City</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="city">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">State</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="state">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Country</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="country">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Zip Code</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="zip_code">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Phone</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="phone">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-5">
                                                    <input type="email" class="form-control" name="email">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-5">
                                                    <button type="submit" class="btn btn-success">Add Location</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </form>

                            </div>
                            <!-- /.box-body -->
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Location Name</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($location_result)): ?>
                                        <?php $i = 1;
                                        foreach ($location_result as $loc) { ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $loc->location_name; ?></td>
                                                <td><?php echo $loc->address; ?></td>
                                                <td><?php echo $loc->city; ?></td>
                                                <td><?php echo $loc->phone; ?></td>
                                                <td><?php echo $loc->email; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'LocationController/edit_location?location_id=' . $loc->location_id; ?>" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo base_url() . 'LocationController/delete_location_by_id?location_id=' . $loc->location_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this location?')">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php $i++;
                                        } ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No locations found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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