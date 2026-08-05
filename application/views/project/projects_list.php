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
                    Projects Master
                </h1>
                <!-- <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Projects</a></li>
                    <li class="active">Projects List</li>
                </ol> -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Projects</h3>
                                <a href="<?php echo base_url(); ?>ProjectController/index" class="btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i> Add Project</a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <table id="gpexample-col2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Project Code</th>
                                            <th>Project Name</th>
                                            <th>System</th>
                                            <th>Opportunity Name</th>
                                            <th>Status</th>
                                            <th>Start Date</th>
                                            <th>Organization</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($projects)) { ?>
                                            <?php foreach ($projects as $project) { ?>
                                                <tr>
                                                    <td><?php echo $project->id; ?></td>
                                                    <td><?php echo $project->project_code; ?></td>
                                                    <td><?php echo $project->project_name; ?></td>
                                                    <td><?php echo !empty($project->system) ? $project->system : 'N/A'; ?></td>
                                                    <td><?php echo $project->opportunity_name; ?></td>
                                                    <td>
                                                        <?php 
                                                        $status_class = '';
                                                        switch($project->project_status) {
                                                            case 'Planning': $status_class = 'label label-primary'; break;
                                                            case 'In Progress': $status_class = 'label label-success'; break;
                                                            case 'Completed': $status_class = 'label label-info'; break;
                                                            case 'On Hold': $status_class = 'label label-warning'; break;
                                                            case 'Cancelled': $status_class = 'label label-danger'; break;
                                                            default: $status_class = 'label label-default';
                                                        }
                                                        ?>
                                                        <span class="<?php echo $status_class; ?>"><?php echo $project->project_status; ?></span>
                                                    </td>
                                                    <td><?php echo date('d-m-Y', strtotime($project->project_start_date)); ?></td>
                                                    <td><?php echo $project->organisation_name; ?></td>
                                                    <td> 
                                                        <a href="<?php echo base_url() . 'ProjectController/edit_project/' . $project->id; ?>" class="btn btn-primary btn-xs" role="button"><i class="fa fa-edit"></i> Edit</a> 
                                                        <a href="<?php echo base_url() . 'ProjectController/delete_project/' . $project->id; ?>" class="btn btn-danger btn-xs" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"></i> Delete</a> 
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="9" class="text-center">No projects found</td>
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
        <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->