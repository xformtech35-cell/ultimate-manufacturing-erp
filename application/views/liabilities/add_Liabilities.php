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
                    Add Liabilities
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Liabilities</a></li>
                    <li class="active">Add Liabilities Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Liabilities Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if ($this->session->flashdata('SUCCESSMSGLiabilities')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSGLiabilities') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSGLiabilities')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSGLiabilities') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>LiabilitiesController/add_Liabilities" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Liabilities<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text"  class="form-control input-sm  filterme" name="Liabilities" id="Liabilities" required="">
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
                                            <th>Liabilities</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($liabilities_result as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->liabilities; ?> </td>
                                            <td><a href="<?php echo base_url() . 'LiabilitiesController/delete_Liabilities_by_id/' . $key->liabilities_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>

                        </div>
                        <!-- /.box -->
                    </div>
                    
                    
                    
                    
                    
                    
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Sub Liabilities Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if ($this->session->flashdata('SUCCESSMSGsubLiabilities')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSGsubLiabilities') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSGsubLiabilities')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSGsubLiabilities') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>LiabilitiesController/add_subLiabilities" enctype="multipart/form-data">
                                    <div class="modal-body">

                                       <div class="card-body ">
                                            
                                            
                                             <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Liabilities<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <select class="form-control input-sm company_search_name"  name="Liabilities" id="Liabilities" required="">
                                                        <option value="">Select Liabilities</option>
                                                        <?php foreach ($liabilities_name as $key) { ?>
                                                            <option value="<?php echo $key->liabilities_id; ?>"><?php echo $key->liabilities; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                </div>
                                           

                                            
                                                <label for="inputEmail3" class="col-sm-1 control-label">SubLiabilities<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text"  class="form-control input-sm  filterme" name="subLiabilities" id="subLiabilities" required="">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
                                    
                                        <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                    </div>
                                </form>
                                
                            </div>
                            <!-- /.box-body -->
                            <table id="" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Liabilities Name</th>
                                            <th>Sub Liabilities</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($subliabilities_result as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->liabilities; ?> </td>
                                                <td> <?php echo $key->subliabilities_name; ?> </td>
                                            <td><a href="<?php echo base_url() . 'LiabilitiesController/delete_subLiabilities_by_id/' . $key->subliabilities_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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

  