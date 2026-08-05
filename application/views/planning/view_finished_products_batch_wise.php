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
                    Finished Products
                </h1>

            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Finished Products Batch Wise</h3>
                                
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Ooh!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <table id="example7" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th> Product Name</th>
                                            <th> Product Quantity</th>
                                            <th> Product Unit</th>
                                            <th> Product Finished Date</th>
                                            <th> Batch</th>
                                            <th> Actions</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($finished_product as $key) {  ?>
                                            <tr>
                                                <td> <?php echo $key->product_name; ?> </td>
                                                <td> <?php echo $key->product_qty; ?> </td>
                                                <td> <?php echo $key->product_unit; ?> </td>
                                                <td> <?php echo $key->product_finished_date; ?> </td>
                                                <td> Batch-<?php echo $key->batch_fk; ?> </td>
                                                <td> 
                                                    <a href="<?php echo base_url() . 'PlanningController/get_product_by_id/' . $key->product_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-edit"></i></a> 
                                                     <a href="<?php echo base_url() . 'PlanningController/delete_product_by_id/' . $key->product_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"></i>
                                                    </a> 
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
        <!-- Add the sidebar's background. This div must be placed
             immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->




