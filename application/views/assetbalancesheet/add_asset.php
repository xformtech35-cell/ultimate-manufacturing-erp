<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
     <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Add Asset
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Asset</a></li>
                    <li class="active">Add Asset Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Asset Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if ($this->session->flashdata('SUCCESSMSGAsset')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSGAsset') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSGAsset')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSGAsset') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>AssetbalancesheetController/add_asset" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Asset<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text"  class="form-control input-sm  filterme" name="asset" id="asset" required="">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
<!--                                        <button type="button" id="back" class="btn btn-default">Back</button>-->
                                        <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                    </div>
                                </form>
                                
                            </div>
                            <!-- /.box-body -->
                            <table id="" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Asset</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($asset_result as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->asset; ?> </td>
                                            <td><a href="<?php echo base_url() . 'AssetbalancesheetController/delete_asset_by_id/' . $key->asset_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>

                        </div>
                        
                        
                        
                        
                        
                         <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add SubAsset Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if ($this->session->flashdata('SUCCESSMSGSubasset')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSGSubasset') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSGSubasset')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSGSubasset') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>AssetbalancesheetController/add_subasset" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            
                                            
                                             <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Asset<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <select class="form-control input-sm company_search_name"  name="asset" id="asset" required="">
                                                        <option value="">Select Asset</option>
                                                        <?php foreach ($asset_name as $key) { ?>
                                                            <option value="<?php echo $key->asset_id; ?>"><?php echo $key->asset; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                </div>
                                           

                                            
                                                <label for="inputEmail3" class="col-sm-1 control-label">SubAsset<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text"  class="form-control input-sm  filterme" name="subasset" id="subasset" required="">
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                              
                                    </div>
                                    <div class="card-footer small text-muted">
<!--                                        <button type="button" id="back" class="btn btn-default">Back</button>-->
                                        <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                    </div>
                                </form>
                                
                            </div>
                            <!-- /.box-body -->
                            <table id="" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Asset Name </th>
                                             <th>Sub Assets</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($subasset_result as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->asset; ?> </td>
                                                 <td> <?php echo $key->subasset_name; ?> </td>
                                            <td><a href="<?php echo base_url() . 'AssetbalancesheetController/delete_subasset_by_id/' . $key->subasset_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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

  