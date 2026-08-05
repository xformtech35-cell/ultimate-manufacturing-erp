<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."views/admin/modal.php");

?>
<style>

    .required label {
        font-weight: bold;
    }
    .required label:after {
        color: #e32;
        content: ' *';
        display:inline;
    }


</style>  

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Vendor Bank Entry
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <!-- <li><a href="<?php echo base_url() . 'Home/index/' ?>">Purchase</a></li> -->
                    <li class="active">Purchase Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                
                <div class="row" style="padding:2%">
                    <div class="pull-left">

                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Vendor Entry</h3>
                                <!-- <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a> -->

                            </div>
                            <!-- /.box-header -->
                            

                                
                                <div class="box-body">


                           <div class="row">

                    <?php // ensure the variable is always an array to avoid notices
                    if (!isset($result_by_id) || !is_array($result_by_id)) {
                        $result_by_id = [];
                    }
                    ?>
                    <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/save_payment">
                    <div class="modal-body">

                    <div class="card-body ">




                        
<!-- form start -->
<div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
    <div class="col-sm-7">

    <input type="hidden" name="payment_id" value="<?php echo $result_by_id['payment_id'] ?? ''; ?>"/>


    <select class="form-control input-sm company_search_name"  name="supplier_id" id="supplier_id" required="">
    <option value="">Select Company</option>
                            <?php foreach ($company_name as $key) { ?>
                                <option value="<?php echo $key['supplier_id']; ?>"  <?php if(isset($result_by_id['payment_supplier_id']) && $result_by_id['payment_supplier_id'] == $key['supplier_id']) echo 'selected="selected"'; ?>><?php echo $key['company_name'] . " - " . ($key['s_code'] ?? '') . ' ( ' . $key['payment'] . ' ) '; ?></option> 
                            <?php } ?>  
                        </select>
    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i> Add Company</span>

    </div>
</div>

<div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 control-label">Bank Voucher Type<span style="color: red;">*</span></label>
    <div class="col-sm-7">
        <select class="form-control input-sm "  name="bank_voucher_type" id="bank_voucher_type" required="">
            <option value="">Select Bank Voucher Type</option>
            <option value="Payment" <?php if(($result_by_id['bank_voucher_type'] ?? '') == "Payment") echo 'selected="selected"'; ?>>Payment</option>
            <option value="Receipt" <?php if(($result_by_id['bank_voucher_type'] ?? '') == "Receipt") echo 'selected="selected"'; ?>>Receipt</option>
        </select>
    </div>
</div>

<div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 control-label">Payment Type<span style="color: red;">*</span></label>
    <div class="col-sm-7">
        <select class="form-control input-sm "  name="payment_type" id="payment_type" required="">
            <option value="">Select Payment Type</option>
            <option value="Advance" <?php if(($result_by_id['payment_type'] ?? '') == "Advance") echo 'selected="selected"'; ?>>Advance</option>
            <option value="Partial" <?php if(($result_by_id['payment_type'] ?? '') == "Partial") echo 'selected="selected"'; ?>>Partial</option>
            <option value="Final" <?php if(($result_by_id['payment_type'] ?? '') == "Final") echo 'selected="selected"'; ?>>Final</option>
           
        </select>
    </div>
</div>




<div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 control-label">Paying Amount<span style="color: red;">*</span></label>
    <div class="col-sm-7">
        <input type="text"  class="form-control allownumericwithdecimal" name="payment" id="payment" value="<?php echo $result_by_id['payment'] ?? ''; ?>"  required="" min=0 oninput="validity.valid||(value='');">
    </div>
</div>

<div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 control-label">Payment Method<span style="color: red;">*</span></label>
    <div class="col-sm-7">
        <select class="form-control input-sm "  name="payment_method" id="payment_method" required="">
            <option value="">Select Payment Method</option>
            <option value="1" <?php if(($result_by_id['payment_method'] ?? '') == "1") echo 'selected="selected"'; ?>>Cash</option>
            <option value="2" <?php if(($result_by_id['payment_method'] ?? '') == "2") echo 'selected="selected"'; ?>>Cheque</option>
            <option value="3" <?php if(($result_by_id['payment_method'] ?? '') == "3") echo 'selected="selected"'; ?>>NetBanking</option>
            <option value="4"<?php if(($result_by_id['payment_method'] ?? '') == "4") echo 'selected="selected"'; ?>>Credit Card</option>
            <option value="5" <?php if(($result_by_id['payment_method'] ?? '') == "5") echo 'selected="selected"'; ?>>Other</option>
        </select>
    </div>
</div>



<div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 control-label">Payment Date<span style="color: red;">*</span></label>
    <div class="col-sm-7">
        <input type="text" class="form-control backdate"   name="payment_date" id="payment_date" value="<?php echo $result_by_id['payment_date'] ?? ''; ?>" required="" onkeydown="return false;" autocomplete="off">
    </div>
</div>


 <div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 control-label">Particular Name</label>

    <div class="col-sm-7">
        <select class="form-control input-sm" name="payment_bank" id="payment_bank">
    <option value="">Select Particular Name</option>

    <option value="GST_Purchase" <?php if(($result_by_id['payment_bank'] ?? '')=="GST_Purchase") echo 'selected="selected"'; ?>>GST Purchase</option>
    <option value="Opening_Balance" <?php if(($result_by_id['payment_bank'] ?? '')=="Opening_Balance") echo 'selected="selected"'; ?>>Opening Balance</option>
    <option value="ICICI" <?php if(($result_by_id['payment_bank'] ?? '')=="ICICI") echo 'selected="selected"'; ?>>ICICI Bank</option>
    <option value="HDFC" <?php if(($result_by_id['payment_bank'] ?? '')=="HDFC") echo 'selected="selected"'; ?>>HDFC Bank</option>
    <option value="SBI" <?php if(($result_by_id['payment_bank'] ?? '')=="SBI") echo 'selected="selected"'; ?>>SBI Bank</option>
    <option value="Saraswat" <?php if(($result_by_id['payment_bank'] ?? '')=="Saraswat") echo 'selected="selected"'; ?>>Saraswat Bank</option>
    <option value="IDBI" <?php if(($result_by_id['payment_bank'] ?? '')=="IDBI") echo 'selected="selected"'; ?>>IDBI Bank</option>
    <option value="AXIS" <?php if(($result_by_id['payment_bank'] ?? '')=="AXIS") echo 'selected="selected"'; ?>>AXIS Bank</option>
    <option value="RBL" <?php if(($result_by_id['payment_bank'] ?? '')=="RBL") echo 'selected="selected"'; ?>>RBL Bank</option>
    <option value="INDUS" <?php if(($result_by_id['payment_bank'] ?? '')=="INDUS") echo 'selected="selected"'; ?>>INDUS Bank</option>
    <option value="BOM" <?php if(($result_by_id['payment_bank'] ?? '')=="BOM") echo 'selected="selected"'; ?>>BOM Bank</option>
</select>
    </div>
</div>

<div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 control-label">Note<span style="color: red;">*</span></label>
    <div class="col-sm-7">
        <textarea class="form-control input-sm" required="" name="payment_note" id="payment_note" rows="3"><?php echo $result_by_id['payment_note'] ?? ''; ?></textarea>
    </div>
</div>


</div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave"  class="btn btn-success">Submit</button>
                        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button> -->
                        <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-danger pull-right"><i class="fa fa-close"></i> Close</a>

                    </div>
                </form>

                      
                <div class="row">
                            <center> <b><h4>Payment History</h4></b></center>
                            <div class="col-sm-12">
                                <table class="table table-bordered table-striped" id="example7">  
                                <thead>
                                    <tr>
                                        
                                    <th>Sr.No.</th>
                                        <th>V_ID</th>
                                        <th>S_ID</th>
                                        <th>Company Name</th>
                                        <th>Paid Amount</th>
                                        <th>Balance</th>
                                        <th>Last Used</th>
                                        <th>Payment Type</th>
                                        <th>Bank Voucher Type</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Particular Name</th>
                                        <th>Note</th>
                                        <th>Download</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $j = 1;
                                    foreach ($result as $key) {
                                        ?>
                                        <tr> 
                                        <td>
                                                <?php echo $j; ?>
                                            </td>
                                            <td><?php echo  $key->payment_id; ?></td>
                                            <td><?php echo  $key->supplier_id; ?></td>
                                            <td><?php echo $key->company_name; ?></td>
                                            <td><?php echo $key->payment; ?></td>
                                            <td><?php echo $key->pay_balance; ?></td>
                                            <td><?php echo $key->pay_paid; ?></td>
                                            <td>
                                                <?php echo $key->payment_type; ?>
                                            </td>
                                                <td>
                                                    <?php echo $key->bank_voucher_type; ?>
                                            
                                            
                                            <td>
                                                <?php 
                                                $arr = array(1 => 'Cash', 2 => 'Cheque', 3 => 'NetBanking',
                                                4 => 'Credit Card', 5 => 'Other',);
                                                echo $arr[$key->payment_method];
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $key->status; ?>
                                            </td>
                                            <td>
                                                <?php echo date('d-m-Y', strtotime($key->payment_date)); ?>
                                            </td>

                                             <td>
                                                <?php echo $key->payment_bank; ?>
                                            </td>

                                            <td>
                                                <?php echo $key->payment_note; ?>
                                            </td>
                                           
                                            <td>
                                               
                                        <?php
                                        if($key->pay_balance > 1){ ?>
                                        <span class="btn btn-success btn-sm btnEdit" data-toggle="modal"   data-target="#myModalLinkPayment" style="margin-top: 10%"><i class="glyphicon glyphicon-send"></i> Link Payment</span>

                                      <?php  }
                                        ?>
                                     
                                       
       <a href="<?php echo base_url() . 'Pdf/print_voucher?invocie_pay_id='. $key->payment_id; ?>&flag=out">Voucher</a>&nbsp;&nbsp;&nbsp;&nbsp;
       <?php if($key->status == "not used"){ ?>  <a href="<?php echo base_url() . 'SupplierController/getPaymentById?id='. $key->payment_id; ?>&flag=out">Edit</a> <?php  }
                                        ?>

                                            </td>
                                        </tr>
                                        <?php
                                        $j++;
                                    }
                                    ?>
                                     </tbody>
                                </table>
                            </div>

                        </div>
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
    <!-- ./supplier modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">

                    <center><h4 class="modal-title">Add Company <button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>

                </div>
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row required">
                                <label for="inputEmail3" class="col-sm-4 control-label">Company  Name</label>
                                <div class="col-sm-7">
                                    <input type="hidden" name="gst_check_supplier" value="gst_check_supplier" id="gst_check_supplier">
                                    <input type="text" class="form-control input-sm"  name="company_name" id="company_name" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">supplier Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm name-validation"  name="fullname" id="fullname">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="10" class="form-control input-sm " name="pancard" id="pancard" style="text-transform: uppercase;">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control input-sm" name="gst" id="gst" style="text-transform: uppercase;">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Email</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="email" id="email" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Mobile</label>
                                <div class="col-sm-7">                                            
                                    <input type="tel" class="form-control input-sm" name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value))
                                                   this.value = this.value.replace(/\D/g, '')"/>                                             
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="state_code" id="state_code">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Address</label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="address" id="address"></textarea>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave"  class="btn btn-success performa_submit">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                <!--</form>-->

            </div>

        </div>
    </div>

     <!-- ./supplier modal -->
     <div id="myModalLinkPayment" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">

                    <center><h4 class="modal-title">Link Payment <button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>

                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/edit_purchase_bill_payment">
                    <div class="modal-body">

                        <div class="card-body ">

                        <p style="display:none">
                        <select class="form-control input-sm company_search_name"  name="supplier_id_fk" id="supplier_id_fk" required="">
                                <?php foreach ($company_name as $key) { ?>
                                                            <option value="<?php echo $key['supplier_id']; ?>"><?php echo $key['company_name'] . " - " . $key['s_code'] . ' ( ' . $key['payment'] . ' ) '; ?></option> 
                                                        <?php } ?>  
                                                    </select>

                                                    </p>

                                <input type="hidden" name="number" id="invoice_number"/>
                                <input type="hidden" name="paid" id="paid"/>
                                <input type="hidden" name="id" id="ida"/>
                                <input type="hidden" name="balance" id="balance"/>
                                <input type="hidden" name="date" id="date" value="<?php echo date('d-m-Y')?>"/>


                                <input type="hidden" name="comp_name" id="comp_name"/>
                                <b><span id="company_name_span"></span></b> &nbsp; &nbsp; &nbsp; &nbsp;
                               Not Used Amount :  <input type="text" name="pay_amt" id="pay_amt" readonly/>
                                <input type="hidden" name="pay_id" id="pay_id"/>

                        <table class="table table-bordered table-striped" id="linkPaymentTable">  
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Date</th>
                                        <th>ID</th>
                                        <th>Bill No.</th>
                                        <th>Total</th>
                                        <th>Balance</th>
                                        <th>Paid</th>
                                        <th>Payment</th>
                                       
                                    </tr>
                                  
                                     
                                  </table>       


                           
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id=""  class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
    
