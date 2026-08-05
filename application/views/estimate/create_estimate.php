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
                  Non GST Quotation
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Non GST Quotation</a></li>
                    <li class="active">Non GST Quotation Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Non GST Quotation</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                
                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                
                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>EstimateController/add_estimate_quotation" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <input type="hidden" name="quotation_non_gst_check" value="non_gst" id="quotation_non_gst_check">   
                                                <?php
                                                if (date('m') <= 3) { //Upto March - previous FY
                                                    $ng_financial_year = (date('y') - 1) . '-' . date('y');
                                                } else { //April onwards - current FY
                                                    $ng_financial_year = date('y') . '-' . (date('y') + 1);
                                                }
                                                ?>
                                                <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="NGQUOTE/<?php printf("%04d", $non_gst_quotation_id['COUNT(uid)'] + 1); ?>/<?php echo $ng_financial_year; ?>">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>Non GST Quotation:<b> NGQUOTE/<?php printf("%04d", $non_gst_quotation_id['COUNT(uid)'] + 1); ?>/<?php echo $ng_financial_year; ?> </b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-5 control-label">Company<span style="color: red;">*</span></label>
                                                
                                                <div class="col-sm-7" id="append_to_dropdown">
                                                  
                                                    <select id="customer_id"  name="customer_id" class="form-control company_search_name"  required="">
                                                   <!--id="company_search_name"-->
                                                         <option value="">Select Company</option>
                                                      <?php foreach ($company_name as $key) { ?>
                                                            <option value="<?php echo $key->customer_id; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                                        <?php } ?>  
                                                  <!--<option value="" id='add_company1'>ABC</option>--> 
                                                </select>
                                         
                                                    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
 
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-5 control-label">Enquiry</label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm "  name="enquiry" id="enquiry">
                                                        <option value="1">Mail</option>
                                                        <option value="2">Verbal</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm created-date" name="date" id="date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expires on<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check" name="expires_date" id="expires_date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm "  name="status" id="status">
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group row ">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo $settings['quotation_subheading']; ?>" class="form-control" name="quotation_subheading" id="quotation_subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="quotation_footer" id="quotation_footer" rows="3"><?php echo $settings['quotation_footer']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="quotation_memo" id="quotation_memo" rows="3"><?php echo $settings['quotation_memo']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">  

                                        <table class="table table-bordered" id="dynamic_field">  
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>MOQ</th>
                                            <th>HSN Code</th>
                                            <th>Price/Unit</th>
                                            <th>Discount(%)</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                            <tr>  
                                                <td>
                                                    <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list"  name="Product_name[]" id="item_name1" onchange="myFunction(this.id)" required="" data-live-search="true">
                                                        <option></option>
                                                        <?php foreach ($product_name as $key) { ?>
                                                            <option value="<?php echo $key->barcode; ?>"><?php echo $key->barcode; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                   
                                                </td> 
                                                <td><input type="text" name="description[]" id="description1" class="form-control input-sm name_list description_auto" /></td> 
                                                <td class="hide"> <span id="total_quantity1" name="total_quantity[]"></span></td>
                                                <td><input type="number" min="1" name="quantity[]" id="quantity1" required="" class="form-control input-sm required_list name_list quantity_auto number-only-validation" value="1" /></td> 
                                                <td><input type="text" readonly="" name="hsn[]" id="hsn1" required="" class="form-control input-sm required_list name_list" /></td> 
                                                <td class="hide"><input type="text" name="gst_per[]" id="gst_per1" class="form-control input-sm name_list" /></td> 
                                                <td><input type="text" readonly="" name="price[]" id="price1" required="" class="form-control input-sm required_list name_list price_auto" value="" /></td>
                                                <td><input type="number" min="0" maxlength="2" name="discount[]" id="discount1" class="form-control input-sm name_list discount_auto number-only-validation" value="" /></td>
                                                <td><input type="hidden" name="amount[]" id="amount1" class="form-control input-sm name_list amount_auto" value="0.00" />
                                                    <input type="hidden" name="amount_temp[]" id="amount_temp1" value="0.00" />
                                                    <input type="hidden" name="gst_amount[]" id="gst_amount1" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                    <span id="span_amount1" name="span_amount[]">₹0.00</span>
                                                </td>
                                                <td><button type="button" name="edit_non_gst" id="edit_non_gst" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>  
                                            </tr>  
                                        </table>  
                                        <div align="center">

                                            <button type="submit" name="submit" id="submit"  class="btn btn-success">Save</button>
                                        </div>
                                        <div align="right" style="margin: 10px">
                                            <input type="hidden" name="temp_total" id="temp_total" class="form-control input-sm temp_total" value="0.00" /><br>
                                            <span id="total_amount" name="total_amount"><b>Grand Total:</b> ₹0.00</span><br>
                                            <span class="text" id="grand_total_amount"><b>Grand Total:</b> ₹0.00</span>
                                            <input type="hidden" name="basic_total" id="basic_total" class="form-control input-sm basic_total" value="0.00" />
                                            <span class="hide" id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span class="hide" id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                            <span class="hidden" id="total_gst_amount" name="total_gst_amount[]">Total GST Amount: ₹0.00</span><br>
                                            
                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
                                        </div>
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

    <!-- ./Customer modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center><h4 class="modal-title">Add Company
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </h4></center>
                </div>
                <!--<form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>EstimateController/add_customer" enctype="multipart/form-data">-->
                    <div class="modal-body">

                        <div class="card-body ">
 <!-- form start -->
                            <div class="form-group row required">
                                <label for="inputEmail3" class="col-sm-4 control-label">Company  Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="company_name" id="company_name" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control " name="fullname" id="fullname">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="pancard" id="pancard" style="text-transform: uppercase;" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control " name="gst" id="gst" style="text-transform: uppercase;" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Email</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control " name="email" id="email" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Mobile</label>
                                <div class="col-sm-7">                                            
                                    <input type="text" class="form-control " name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value))
                                                               this.value = this.value.replace(/\D/g, '')"  />                                             
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control " name="state_code" id="state_code" >
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Address</label>
                                <div class="col-sm-7">
<!--                                    <input type="text" class="form-control " name="address" id="address" >-->
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






