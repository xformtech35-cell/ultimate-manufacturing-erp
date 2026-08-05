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
                    Assets Sub Category
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#"> Assets Sub Category</a></li>
                    <li class="active"> Assets Sub Category</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Assets Sub Category</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>AssetController/add_asset_sub_category" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Assets<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">

                                                    
                                                    <select class="form-control input-sm asset"  name="asset" id="asset" required="" data-live-search="true">
                                                        <option value="">Moc</option>
                                                        <?php foreach ($asset_result as $key) { ?>
                                                            <option value="<?php echo $key->asset; ?>"><?php echo $key->asset; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                    
                                                    
                                                 </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Assets Sub Category<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text"  class="form-control input-sm  filterme" name="asset_sub_category" id="asset_sub_category" required="">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                    </div>
                                </form>
                                
                            </div>
                            <!-- /.box-body -->
                            <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Asset</th>
                                            <th>Asset Sub Category</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($asset_sub_category as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->asset; ?> </td>
                                                <td><?php echo $key->asset_sub_category; ?></td>
                                            <td><a href="<?php echo base_url() . 'AssetController/delete_asset_sub_category_by_id/' . $key->asset_sub_category_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                            </tr>
                                        <?php $i++; } ?>
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

  