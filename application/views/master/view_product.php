<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');

$role_name = strtolower($session_data_head1['result']['role_name'] ?? '');
$role_id   = (int)($session_data_head1['result']['role_id'] ?? $session_data_head1['result']['user_role_id'] ?? 0);
$user_id   = (int)($session_data_head1['result']['user_id'] ?? 0);
$is_admin  = ($role_name === 'admin' || $role_id === 1 || $user_id === 1);
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">



        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Products Master
                </h1>
<!--                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Guest House Problem</a></li>
                    <li class="active">Guest House Problem Details</li>
                </ol>-->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Products</h3>
                                <a href="<?php echo base_url(); ?>MasterController/add_product_form" class="btn btn-success btn-sm pull-right"  data-toggle="modal1" data-target="#myModal1"><i class="glyphicon glyphicon-plus"></i>Add Product</a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Ooh!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <table id="gpexample-col2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th> Category</th>
                                            <th> Products</th>
                                            <th> Actions</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $key) {  ?>
                                            <tr>
                                                <td> <?php echo $key->category_name; ?> </td>
                                                <td> <?php echo $key->product_master_name; ?> </td>
                                                <td> 
                                                    <a href="<?php echo base_url() . 'MasterController/get_product_by_id/' . $key->product_master_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-edit"></i></a> 
                                                     <?php if ($is_admin): ?>
                                                         <a href="<?php echo base_url() . 'MasterController/delete_product_by_id/' . $key->product_master_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"></i></a>
                                                     <?php else: ?>
                                                         <a href="javascript:void(0);" onclick="openDeleteRequestModal('<?= $key->product_master_id; ?>', '<?= htmlspecialchars($key->product_master_name, ENT_QUOTES); ?>', 'item_code_master')" class="btn btn-danger" role="button"><i class="fa fa-trash"></i></a>
                                                     <?php endif; ?> 
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
