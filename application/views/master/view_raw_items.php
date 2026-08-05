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
 
                     Raw Items
                </h1>
 
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
 
                                <h3 class="box-title"> Raw Items</h3>
                                <a href="<?php echo base_url(); ?>MasterController/add_raw_itms_form" class="btn btn-success btn-sm pull-right"  data-toggle="modal1" data-target="#myModal1"><i class="glyphicon glyphicon-plus"></i>Add Raw Item</a>
 
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

 
                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Ooh!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

 
                                <table id="example2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th> Raw Item Name</th>
                                            <th> Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
 
                                        <?php foreach ($raw_items as $key) {  ?>
                                        <tr>
                                            <td> <?php echo $key->raw_item_master_name; ?> </td>
                                            <td> 
                                                <a href="<?php echo base_url() . 'MasterController/get_raw_item_by_id/' . $key->raw_item_master_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-edit"></i></a> 
                                            <a href="<?php echo base_url() . 'MasterController/delete_raw_item_by_id/' . $key->raw_item_master_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"></i></a> 
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
 
           
        <?php $this->load->view('admin/footer'); ?>
 
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->


 