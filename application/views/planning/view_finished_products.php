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
                                <h3 class="box-title">Finished Products</h3>
                                <a href="<?php echo base_url(); ?>PlanningController/add_finished_product_form" class="btn btn-success btn-sm pull-right"  data-toggle="modal1" data-target="#myModal1"><i class="glyphicon glyphicon-plus"></i>Add Finished Product</a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Ooh!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <table id="product" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th> Batch</th>
                                            <th> Product Finished Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($finished_product as $key) {  ?>
                                            <tr>
                                                <td> 
                                                <a href="<?php echo base_url() . 'PlanningController/get_product_by_batch_wise/' . $key->batch_fk; ?> " role="button">Batch-<?php echo $key->batch_fk; ?></a> 
                                                </td>
                                                <td> <?php echo $key->product_finished_date; ?> </td>
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