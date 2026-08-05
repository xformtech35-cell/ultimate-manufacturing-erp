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
                   Add Bank Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Bank Details</a></li>
                    <li class="active">Add Bank Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Bank Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>BankdetailController/add_bank_detail" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            
                                            
                                            <div class="col-md-6">
                                                   <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="bank_name" id="bank_name" >
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Type</label>
                                                <div class="col-sm-6">
                                                    
                                                    <select type="text" class="form-control input-sm" name="account_type" id="account_type">
                                                        <option value="">Select Account Type</option>
                                                            <option value="1">Savings account</option> 
                                                            <option value="2">Current or credit card account</option> 
                                                            <option value="3">Cash account</option> 
                                                            
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">State</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="state" id="state" >
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-6">
<!--                                                    <input type="text"  class="form-control input-sm  filterme" name="status" id="status">-->
                                                    <select type="text" class="form-control input-sm" name="status" id="status">
                                                        <option value="">Select Status</option>
                                                            <option value="1">Open</option> 
                                                            <option value="2">Closed</option> 
                                                            
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Comment/ Note</label>
                                                <div class="col-sm-6">
                                                    
                                                    <textarea class="form-control" name="comment" id="comment" > </textarea>

                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Initial Balance</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="initial_balance" id="initial_balance">
                                                </div>
                                            </div>
                                               <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Minimum allowed balance</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="minimum_allowed_balance" id="minimum_allowed_balance">
                                                </div>
                                            </div> 
                                            </div>
                                            
                                            <div class="col-md-6">
                                                
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Minimum desired Balance</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="minimum_desired_balance" id="minimum_desired_balance">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Number</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="account_number" id="account_number">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">IFSC Code</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="ifsc_code" id="ifsc_code">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Address</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="bank_address" id="bank_address">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Owner Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="account_owner_name" id="account_owner_name">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Account Owner Address</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control input-sm  filterme" name="account_owner_address" id="account_owner_address">
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
                                            <th>Bank Name</th>
                                            <th>Account Type</th>
                                            <th>Account Number</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($bankdetail_result as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->bank_name; ?> </td>
                                                
                                                <td> <?php
                                                        switch ($key->account_type) {
                                                            case "1":
                                                                echo "Savings account";
                                                                break;
                                                            case "2":
                                                                echo "Current or credit card account";
                                                                break;
                                                            case "3":
                                                                echo "Cash account";
                                                                break;
                                                            default:
                                                                echo "";
                                                        }
                                                        ?>
                                                    </td>
                                                <td> <?php echo $key->account_number; ?> </td>
                                                <td> <?php
                                                        switch ($key->status) {
                                                            case "1":
                                                                echo "Open";
                                                                break;
                                                            case "2":
                                                                echo "Closed";
                                                                break;
                                                            default:
                                                                echo "";
                                                        }
                                                        ?>
                                                    </td>
                                               
                                                <td>

                                                    <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                                <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="<?php echo base_url() . 'BankdetailController/get_bank_detail_id/' . $key->bank_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                                                                <li><a href="<?php echo base_url() . 'BankdetailController/delete_bank_detail_by_id/' . $key->bank_id; ?>" 
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

  