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
                    <li><a href="<?php echo base_url() . 'BankdetailController/bank_transcation_index'?>">Edit Bank Details</a></li>
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

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>BankdetailController/edit_bank_transaction" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            <input type="hidden" name="bank_transaction_id" id="bank_transaction_id" value="<?php echo $bank_transaction_by_id['bank_transaction_id']; ?>">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Name</label>
                                                <div class="col-sm-6">
                                                    <select  class="form-control input-sm" name="bank_transaction_name" id="bank_transaction_name" required="">
                                                        <option value="" disabled="">Select Bank Name</option>
                                                        <?php foreach ($bankdetail_result as $key) { ?>
                                                            <option value="<?php echo $key->bank_name; ?>"<?= $bank_transaction_by_id['bank_transaction_name'] == $key->bank_name ? ' selected="selected"' : ''; ?>><?php echo $key->bank_name; ?></option> 

                                                        <?php } ?>  

                                                    </select>                                                
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Transaction Date</label>
                                                <div class="col-sm-6">
                                                    <?php
                                                    $time = strtotime($bank_transaction_by_id['transaction_date']);
                                                    $newformat = date('d-m-Y',$time);
                                                     ?>
                                                    <input type="text" class="form-control input-sm alldate1" name="transaction_date"  value="<?php echo $newformat; ?>" required="" onkeydown="return false;" autocomplete="off">
                                                </div>
                                            </div>
                                                                  
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Transaction Details</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="transaction_detail" id="transaction_detail" value="<?php echo $bank_transaction_by_id['transaction_detail']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Withdrawal Amount</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="withdrawal_amount" id="withdrawal_amount" value="<?php echo $bank_transaction_by_id['withdrawal_amount']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Deposite Amount</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="deposite_amount" id="deposite_amount" value="<?php echo $bank_transaction_by_id['deposite_amount']; ?>"> 
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Balance Amount</label>
                                                <div class="col-sm-6">
                                                     <input type="text"  class="form-control input-sm  filterme" name="balance_amount" id="balance_amount" value="<?php echo $bank_transaction_by_id['balance_amount']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Description</label>
                                                <div class="col-sm-6">
                                                    <textarea class="form-control" name="description" id="description" ><?php echo $bank_transaction_by_id['description']; ?> </textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        
                                        <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                    </div>
                                </form>
                                
                            </div>