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
                   Add Transaction Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Transaction Details</a></li>
                    <li class="active">Add Transaction Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Transaction Details</h3>
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

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>BankdetailController/add_banktransaction_detail" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Name</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control input-sm" name="bank_transaction_name" id="bank_transaction_name" required="">
                                                        <option value="" disabled="">Select Bank Name</option>
                                                      <?php foreach ($bankdetail_result as $key) { ?>
                                                            <option value="<?php echo $key->bank_name; ?>"><?php echo $key->bank_name; ?></option> 
                                                        <?php } ?>  
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Transaction Date</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control input-sm alldate" name="transaction_date" id="transaction_date" required="" onkeydown="return false;">

                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Transaction Details</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="transaction_detail" id="transaction_detail" >
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Withdrawal Amount</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="withdrawal_amount" id="withdrawal_amount" >
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Deposite Amount</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="deposite_amount" id="deposite_amount" > 
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Balance Amount</label>
                                                <div class="col-sm-6">
                                                     <input type="text"  class="form-control input-sm  filterme" name="balance_amount" id="balance_amount">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Description</label>
                                                <div class="col-sm-6">
                                                    <textarea class="form-control" name="description" id="description" > </textarea>
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
                                            <th>Bank Name</th>
                                            <th>Date</th>
                                            <th>Transaction Details</th>
                                            <th>Withdrawal Amount</th>
                                            <th>Deposite Amount</th>
                                            <th>Balance Amount</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($banktransaction_result as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->bank_transaction_name; ?> </td>
                                                <td> <?php echo $key->transaction_date; ?> </td>
                                                <td><?php echo $key->transaction_detail; ?></td>
                                                    <td> <?php echo $key->withdrawal_amount; ?> </td>
                                                    <td><?php echo $key->deposite_amount; ?></td>
                                                    <td><?php echo $key->balance_amount; ?></td>
                                                    <td><?php echo $key->description; ?></td>
                                                <td>

                                                    <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                                <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="<?php echo base_url() . 'BankdetailController/get_bank_transaction_id/' . $key->bank_transaction_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                                                                <li><a href="<?php echo base_url() . 'BankdetailController/delete_bank_transaction_by_id/' . $key->bank_transaction_id; ?>" 
                                                                       role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
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

  