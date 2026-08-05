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
                   Add Liabilities Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Liabilities Details</a></li>
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

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>BankdetailController/add_loan" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            
                                            
                                            <div class="col-md-6">
                                                   <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="acc_name" id="acc_name" >
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="bank" id="bank" >
                                                </div>
                                            </div>
                                                
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Number</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="acc_number" id="acc_number" >
                                                </div>
                                            </div>
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label"> Date</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control input-sm alldate" name="loan_date" id="loan_date" required="" onkeydown="return false;">

                                                </div>
                                            </div>
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Description</label>
                                                <div class="col-sm-6">
                                                    
                                                    <textarea class="form-control" name="loan_description" id="loan_description" ></textarea>

                                                </div>
                                            </div>
                                              <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Current Balance</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="current_balance" id="current_balance">
                                                </div>
                                            </div>  
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Loan Recevied In</label>
                                                <div class="col-sm-6">
                                                    
                                                    <select class="form-control input-sm" name="loan_recevied" id="loan_recevied">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1">Current account</option> 
                                                            <option value="2">OD account</option> 
                                                            <option value="3">CC account</option> 
                                                    </select>
                                                </div>
                                            </div>
                                                
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Main Liabilities</label>
                                                <div class="col-sm-6">
                                                    
                                                    <select  class="form-control input-sm" name="liabilities" id="liabilities">
                                                        <option value="">Select Liabilities</option>
                                                            <option value="Capital Account">Capital Account</option> 
                                                            <option value="Loans">Loans</option> 
                                                            <option value="Current Liabilities">Current Liabilities</option> 
                                                    </select>
                                                </div>
                                            </div>
                                                
                                                <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Sub-Categories Liability</label>
                                                <div class="col-sm-6">
                                                    
                                                    <select  class="form-control input-sm" name="sub_liabilities" id="sub_liabilities">
                                                        <option value="">Select Sub-Liabilities</option>
                                                            <option value="Reserves & Surplus">Reserves & Surplus</option> 
                                                            <option value="Share Capital">Share Capital</option> 
                                                            <option value="Bank OD A/C">Bank OD A/C</option> 
                                                            <option value="Unsecured Loans">Unsecured Loans</option>
                                                            <option value="Duties & Taxes">Duties & Taxes</option>
                                                            <option value="Provisions">Provisions</option>
                                                            <option value="Sundry Creditors">Sundry Creditors</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Interest Rate(%)</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="interest_rate" id="interest_rate">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Term Duration In Month</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="duration" id="duration">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Processing Fee</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="processing_fee" id="processing_fee">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Processing Fee Paid From</label>
                                                <div class="col-sm-6">
                                                    <select type="text" class="form-control input-sm" name="processing_fee_paid_from" id="processing_fee_paid_from">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1">Savings account</option> 
                                                            <option value="2">Cash account</option> 
                                                            <option value="3">Current account</option>
                                                            <option value="4">OD account</option>
                                                            <option value="5">CC account</option>
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
                            <table id="" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Account Name</th>
                                            <th>Date</th>
                                            <th>Principal</th>
                                            <th>Interest</th>
                                            <th>Total Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($loan_result as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->acc_name; ?> </td>
                                                <td> <?php echo $key->loan_date; ?> </td>
                                                <td> <?php echo $key->current_balance; ?> </td>
                                                <td> <?php echo $key->interest_rate; ?> </td>
                                                <td> <?php echo $key->current_balance; ?> </td>
                                                <td>

                                                    <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                                <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="<?php echo base_url() . 'BankdetailController/get_loan_id/' . $key->loan_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                                                                <li><a href="<?php echo base_url() . 'BankdetailController/delete_loan_by_id/' . $key->loan_id; ?>" 
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

<!--   <script>
    //Reference: https://jsfiddle.net/fwv18zo1/
var $select1 = $( '#liabilities' ),
		$select2 = $( '#sub_liabilities' ),
    $options = $select2.find( 'option' );
    
$select1.on( 'change', function() {
	$select2.html( $options.filter( '[value="' + this.value + '"]' ) );
} ).trigger( 'change' );
    </script>-->