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
                   Edit Loan Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'BankdetailController/loan_account_index'?>">Edit Loan Details</a></li>
                    <li class="active">Edit Loan Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Loan Details</h3>
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

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>BankdetailController/edit_loan" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            
                                            
                                            <div class="col-md-6">
                                                <input type="hidden" name="loan_id" id="loan_id" value="<?php echo $bank_loan_by_id['loan_id']; ?>">
                                                   <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="acc_name" id="acc_name" value="<?php echo $bank_loan_by_id['acc_name']; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="bank" id="bank" value="<?php echo $bank_loan_by_id['bank']; ?>">
                                                </div>
                                            </div>
                                                
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Number</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="acc_number" id="acc_number" value="<?php echo $bank_loan_by_id['acc_number']; ?>">
                                                </div>
                                            </div>
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label"> Date</label>
                                                <div class="col-sm-6">
                                                    <?php
                                                    $time = strtotime($bank_loan_by_id['loan_date']);
                                                    $newformat = date('d-m-Y',$time);
                                                     ?>
                                                    <input type="text" class="form-control input-sm alldate1" name="loan_date"  value="<?php echo $newformat; ?>" required="" onkeydown="return false;" autocomplete="off">
                                                </div>
                                            </div>
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Description</label>
                                                <div class="col-sm-6">
                                                    
                                                    <textarea class="form-control" name="loan_description" id="loan_description" ><?php echo $bank_loan_by_id['loan_description']; ?></textarea>

                                                </div>
                                            </div>
                                              <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Current Balance</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="current_balance" id="current_balance" value="<?php echo $bank_loan_by_id['current_balance']; ?>">
                                                </div>
                                            </div>  
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Loan Recevied In</label>
                                                <div class="col-sm-6">
                                                    
                                                    <select type="text" class="form-control input-sm" name="loan_recevied" id="loan_recevied">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1"<?=$bank_loan_by_id['loan_recevied'] == '1' ? ' selected="selected"' : '';?>>Current account</option> 
                                                            <option value="2"<?=$bank_loan_by_id['loan_recevied'] == '2' ? ' selected="selected"' : '';?>>OD account</option>
                                                            <option value="3"<?=$bank_loan_by_id['loan_recevied'] == '3' ? ' selected="selected"' : '';?>>CC account</option>
                                                    </select>
                                                </div>
                                            </div>
                                                
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Main Liabilities</label>
                                                <div class="col-sm-6">
                                                    
                                                    <select type="text" class="form-control input-sm" name="liabilities" id="liabilities">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1"<?=$bank_loan_by_id['liabilities'] == '1' ? ' selected="selected"' : '';?>>Capital Account</option> 
                                                            <option value="2"<?=$bank_loan_by_id['liabilities'] == '2' ? ' selected="selected"' : '';?>>Loans</option>
                                                            <option value="3"<?=$bank_loan_by_id['liabilities'] == '3' ? ' selected="selected"' : '';?>>Current Liabilities</option>
                                                    </select>
                                                </div>
                                            </div>
                                                
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Sub-Categories Liability</label>
                                                <div class="col-sm-6">
                                                    
                                                    <select type="text" class="form-control input-sm" name="sub_liabilities" id="sub_liabilities">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1"<?=$bank_loan_by_id['sub_liabilities'] == '1' ? ' selected="selected"' : '';?>>Reserves & Surplus</option> 
                                                            <option value="1"<?=$bank_loan_by_id['sub_liabilities'] == '1' ? ' selected="selected"' : '';?>>Share Capital</option>
                                                            <option value="2"<?=$bank_loan_by_id['sub_liabilities'] == '2' ? ' selected="selected"' : '';?>>Bank OD A/C</option>
                                                            <option value="2"<?=$bank_loan_by_id['sub_liabilities'] == '2' ? ' selected="selected"' : '';?>>Unsecured Loans</option>
                                                            <option value="3"<?=$bank_loan_by_id['sub_liabilities'] == '3' ? ' selected="selected"' : '';?>>Duties & Taxes</option>
                                                            <option value="3"<?=$bank_loan_by_id['sub_liabilities'] == '3' ? ' selected="selected"' : '';?>>Provisions</option>
                                                            <option value="3"<?=$bank_loan_by_id['sub_liabilities'] == '3' ? ' selected="selected"' : '';?>>Sundry Creditors</option>
                                                            
                                                            
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Interest Rate(%)</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="interest_rate" id="interest_rate" value="<?php echo $bank_loan_by_id['interest_rate']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Term Duration In Month</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="duration" id="duration" value="<?php echo $bank_loan_by_id['duration']; ?>">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Processing Fee</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="processing_fee" id="processing_fee" value="<?php echo $bank_loan_by_id['processing_fee']; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Processing Fee Paid From</label>
                                                <div class="col-sm-6">
                                                    <select type="text" class="form-control input-sm" name="processing_fee_paid_from" id="processing_fee_paid_from">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1"<?=$bank_loan_by_id['processing_fee_paid_from'] == '1' ? ' selected="selected"' : '';?>>Savings account</option> 
                                                            <option value="2"<?=$bank_loan_by_id['processing_fee_paid_from'] == '2' ? ' selected="selected"' : '';?>>Cash account</option> 
                                                            <option value="3"<?=$bank_loan_by_id['processing_fee_paid_from'] == '3' ? ' selected="selected"' : '';?>>Current account</option>
                                                            <option value="4"<?=$bank_loan_by_id['processing_fee_paid_from'] == '4' ? ' selected="selected"' : '';?>>OD account</option>
                                                            <option value="5"<?=$bank_loan_by_id['processing_fee_paid_from'] == '5' ? ' selected="selected"' : '';?>>CC account</option>
                                                    </select>
                                                </div>
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
    <script>
    //Reference: https://jsfiddle.net/fwv18zo1/
var $select1 = $( '#liabilities' ),
		$select2 = $( '#sub_liabilities' ),
    $options = $select2.find( 'option' );
    
$select1.on( 'change', function() {
	$select2.html( $options.filter( '[value="' + this.value + '"]' ) );
} ).trigger( 'change' );
    </script>
  