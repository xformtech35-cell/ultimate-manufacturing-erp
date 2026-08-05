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
                   Edit Cheque Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'ChequeController/cheque_index'?>">Edit Cheque Details</a></li>
                    <li class="active">Edit Cheque Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Cheque Details</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>ChequeController/edit_cheque_detail" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            <input type="hidden" name="cheque_id" id="cheque_id" value="<?php echo $cheque_detail_by_id['cheque_id']; ?>">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Cheque Number</label>
                                                <div class="col-sm-4">
                                                    <input type="text"  class="form-control input-sm  filterme" name="cheque_no" id="cheque_no" value="<?php echo $cheque_detail_by_id['cheque_no']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Creation Date</label>
                                                <div class="col-sm-4">
                                                    
                                                        <?php
                                                    $time = strtotime($cheque_detail_by_id['creation_date']);
                                                    $newformat = date('d-m-Y',$time);
                                                     ?>
                                                    <input type="text" class="form-control input-sm alldate1" name="creation_date"  value="<?php echo $newformat; ?>" required="" onkeydown="return false;" autocomplete="off">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Account Name</label>
                                                <div class="col-sm-4">
                                                    <input type="text"  class="form-control input-sm  filterme" name="bank_account_name" id="bank_account_name" value="<?php echo $cheque_detail_by_id['bank_account_name']; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                 <label for="inputEmail3" class="col-sm-4 control-label">No. of Cheque</label>
                                                    <div class="col-sm-4">
                                                        <input type="text"  class="form-control input-sm  filterme" name="no_of_cheque" id="no_of_cheque" value="<?php echo $cheque_detail_by_id['no_of_cheque']; ?>">
                                                    </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-4">
<!--                                                    <input type="text"  class="form-control input-sm  filterme" name="status" id="status">-->
                                                    <select type="text" class="form-control input-sm" name="status" id="status">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1"<?=$cheque_detail_by_id['status'] == '1' ? ' selected="selected"' : '';?>>Validate</option> 
                                                            <option value="2"<?=$cheque_detail_by_id['status'] == '2' ? ' selected="selected"' : '';?>>Not Validate</option> 
                                                    </select>
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

  