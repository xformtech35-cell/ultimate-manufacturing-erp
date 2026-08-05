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
                   Edit Bank Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Edit Bank Details</a></li>
                    <li class="active">Edit Bank Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Bank Details</h3>
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

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>BankdetailController/edit_bank_detail" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            
                                            <div class="col-md-6">
                                                
                                            
                                            
                                                  <input type="hidden" name="bank_id" id="bank_id" value="<?php echo $bank_detail_by_id['bank_id']; ?>">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="bank_name" id="bank_name" value="<?php echo $bank_detail_by_id['bank_name']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Type</label>
                                                <div class="col-sm-6">
                                                    <select type="text" class="form-control input-sm" name="account_type" id="account_type">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1"<?=$bank_detail_by_id['account_type'] == '1' ? ' selected="selected"' : '';?>>Savings account</option> 
                                                            <option value="2"<?=$bank_detail_by_id['account_type'] == '2' ? ' selected="selected"' : '';?>>Current or credit card account</option> 
                                                            <option value="3"<?=$bank_detail_by_id['account_type'] == '3' ? ' selected="selected"' : '';?>>Cash account</option> 
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">State</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="state" id="state" value="<?php echo $bank_detail_by_id['state']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-6">
<!--                                                    <input type="text"  class="form-control input-sm  filterme" name="status" id="status">-->
                                                    <select type="text" class="form-control input-sm" name="status" id="status">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1"<?=$bank_detail_by_id['status'] == '1' ? ' selected="selected"' : '';?>>Open</option> 
                                                            <option value="2"<?=$bank_detail_by_id['status'] == '2' ? ' selected="selected"' : '';?>>Close</option> 
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Comment/ Note</label>
                                                <div class="col-sm-6">
                                                    
                                                    <textarea class="form-control" name="comment" id="comment"><?php echo $bank_detail_by_id['comment']; ?> </textarea>

                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Initial Balance</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="initial_balance" id="initial_balance" value="<?php echo $bank_detail_by_id['initial_balance']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Minimum allowed balance</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="minimum_allowed_balance" id="minimum_allowed_balance" value="<?php echo $bank_detail_by_id['minimum_allowed_balance']; ?>">
                                                </div>
                                            </div>
                                            </div>
                                            
                                             <div class="col-md-6">
                                                 <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Minimum desired Balance</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="minimum_desired_balance" id="minimum_desired_balance" value="<?php echo $bank_detail_by_id['minimum_desired_balance']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Number</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="account_number" id="account_number" value="<?php echo $bank_detail_by_id['account_number']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">IFSC Code</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="ifsc_code" id="ifsc_code" value="<?php echo $bank_detail_by_id['ifsc_code']; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Address</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="bank_address" id="bank_address" value="<?php echo $bank_detail_by_id['bank_address']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Owner Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="account_owner_name" id="account_owner_name" value="<?php echo $bank_detail_by_id['account_owner_name']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Owner Address</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="account_owner_address" id="account_owner_address" value="<?php echo $bank_detail_by_id['account_owner_address']; ?>">
                                                </div>
                                            </div>
                                            </div>
                                         </div>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        
                                        <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                    </div>
                                </form>
                                
                            </div>