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
                    Delivered Items
                </h1>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Delivered Items</h3>
                                <a href="<?php echo base_url(); ?>PlanningController/add_delivered_item_form" class="btn btn-success btn-sm pull-right"  data-toggle="modal1" data-target="#myModal1"><i class="glyphicon glyphicon-plus"></i>Add Delivered Item</a>
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
                                            <th> Batch Description</th>
                                            <th> Item Deliver Date</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($delivered_items as $key) {  ?>
                                            <tr>
                                                <td> 
                                                    <a href="<?php echo base_url() . 'PlanningController/get_row_item_batchwise/' . $key->batch; ?> "  role="button">Batch-<?php echo $key->batch; ?></a> 
                                                </td>
                                                <td> <?php echo $key->batch_description; ?> </td>
                                                <td> <?php echo $key->raw_item_deliver_date; ?> </td>
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




