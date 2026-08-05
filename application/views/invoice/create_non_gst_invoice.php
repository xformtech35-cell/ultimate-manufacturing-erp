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
                    Invoice
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Invoice</a></li>
                    <li class="active">Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                
<!--                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        <div class="col-md-2">
                            <a href="<?php //echo base_url(); ?>InvoiceController/create_invoice" id="local_gst" name="local_gst" class="btn btn-primary" role="button">GST</a>
                        </div>
                        <div class="pull-right">
                            <div class="col-md-6">
                                <a href="<?php //echo base_url(); ?>InvoiceController/create_central_gst_invoice" id="central_gst" name="central_gst" class="btn btn-primary" role="button">IGST</a>
                            </div>
                           
                        </div>
                    </div>
                </div>-->
                
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Non GST Invoice</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>


                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>InvoiceController/add_non_gst_invoice" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <input type="hidden" name="gst_check" value="gst" id="gst_check">
                                                <input type="hidden" name="save_button_hide" value="save_button_hide" id="save_button_hide">
                                                <?php
                                                if (date('m') <= 3) {//Upto March - previous FY
                                                    $ng_inv_fy = (date('y') - 1) . '-' . date('y');
                                                } else {//April onwards - current FY
                                                    $ng_inv_fy = date('y') . '-' . (date('y') + 1);
                                                }
                                                ?>
                                                <input type="hidden" class="form-control input-sm"   name="invoice_number" id="invoice_number" required="" value="INV/<?php printf("%04d", $invoice_id['COUNT(uid)'] + 1); ?>/<?php echo $ng_inv_fy; ?>">
                                                <label for="inputEmail3" name="invoice_number" id="invoice_number" class="col-sm-12 control-label"> <h2>Non GST Invoice:<b>INV/<?php printf("%04d", $invoice_id['COUNT(uid)'] + 1); ?>/<?php echo $ng_inv_fy; ?></b> </h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name"  name="customer_id" id="customer_id" required="">
                                                        <option value="">Select Company</option>
                                                        <?php foreach ($company_name as $key) { ?>
                                                            <option value="<?php echo $key->customer_id; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
 
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
                                            
                                             <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Method</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm "  name="payment_method" id="payment_method">
                                                        <option value="">Select Payment Method</option>
                                                        <option value="1">Cash</option>
                                                        <option value="2">Cheque</option>
                                                        <option value="3">NetBanking</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Customer PO</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="invoice_customer_po" id="invoice_customer_po">
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Invoice Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm created-date alldate" name="invoice_date" id="invoice_date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check alldate" name="due_date" id="payment_due_date"  required="" onkeydown="return false;">
                                                </div>
                                            </div>
                                           
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Note</label>
                                                <div class="col-sm-8">
                                                    <textarea  class="form-control" name="note" id="note" rows="2"><?php echo $settings['invoice_notes']; ?></textarea>
                                                </div>
                                            </div>
                                            
                                             <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">PO Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm" name="invoice_po_date" id="invoice_po_date" onkeydown="return false;">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group row hide">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo $settings['invoice_subheading']; ?>" class="form-control" name="invoice_subheading" id="invoice_subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="invoice_footer" id="invoice_footer" rows="3"><?php echo $settings['invoice_footer']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Shipping To</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="shipping_address" id="shipping_address" rows="3"></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="invoice_memo" id="invoice_memo" rows="3"><?php echo $settings['invoice_memo']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">  

                                        <table class="table table-bordered" id="dynamic_field">  
                                           <th>Item</th>
                                           <th>Description</th>
                                            <th>Quantity (Nos/Kg)</th>
                                            <th>HSN Code</th>
                                            <th>Price</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                            <tr>  
                                                <td><select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list"  name="term[]" id="item_name1" onchange="myFunction(this.id)" required="" data-live-search="true">
                                                        <option></option>
                                                           <option value="NEW">+ Add New Product</option>

                                                        <?php foreach ($product_name as $key) { ?>
                                                            <option value="<?php echo $key->code; ?>"><?php echo $key->code . " - " . $key->item_name; ?></option>
                                                        <?php } ?>  
                                                    </select>
                                                    
                                                    <!--<input type="text" name="term[]" id="item_name1" required="" class="form-control input-sm name_list product_name_auto" />-->
                                                </td> 
                                                <td><input type="text" name="description[]" id="description1" class="form-control input-sm name_list description_auto" /></td> 
                                                <td class="hide"> <span id="total_quantity1" name="total_quantity[]"></span>
                                                    <input type="hidden" name="total_quantity1[]" id="total_quantity" class="form-control input-sm name_list total_quantity_auto"  />                        
                                                </td>
                                                <td><input type="number" min="1" name="quantity[]" id="quantity1" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="" /></td> 
                                                <td><input type="text" readonly="" name="hsn[]" id="hsn1" required="" class="form-control input-sm name_list" /></td> 
                                                <td class="hide"><input type="text" readonly="" name="gst_per[]" id="gst_per1" class="form-control input-sm name_list" /></td> 
                                                <td><input type="text"  name="price[]" id="price1" required="" class="form-control input-sm price_auto" value="0"/></td>
                                                <td><input type="hidden" name="amount[]" id="amount1" class="form-control input-sm name_list amount_auto" value="0.00" />
                                                    <input type="hidden" name="gst_amount[]" id="gst_amount1" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                    <span id="span_amount1" name="span_amount[]">₹0.00</span>
                                                </td>
                                                <td><button type="button" accesskey="n" name="edit_non_gst_invoive" id="edit_non_gst_invoive" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>  
                                            </tr>  
                                        </table>  
                                        <div align="center">

                                            <button type="submit" name="submit" id="submit"  class="btn btn-success">Save</button>
                                        </div>
                                        <div align="right" style="margin: 10px">
                                            <span id="total_amount" name="total_amount[]">Total: ₹0.00</span><br>
                                            <!--<span id="igst_amount" class="hide_igst" name="igst_amount[]">Total IGST Amount: ₹0.00</span><br>-->
<!--                                            <span id="total_gst_amount" name="total_gst_amount[]">Total GST Amount: ₹0.00</span><br>-->
                                            <b> <span id="grand_total_amount2" name="grand_total_amount2[]"><b>Grand Total:</b> ₹0.00</span></b><br>
                                            
                                             <b> Grand Total in Words:<span id="word2" name="word2"></span>Only.</b><br>
                                            
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
                  <center>  <h4 class="modal-title">Add Company <button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InvoiceController/add_customer" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Company  Name<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" name="non_gst_check_customer" value="non_gst_check_customer" id="non_gst_check_customer">
                                    <input type="text" class="form-control input-sm"  name="company_name" id="company_name" required="" onkeydown="return validate_name(event)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Customer Name<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm name-validation"  name="fullname" id="fullname" required="" onkeydown="return validate_name(event)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> PAN No<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="10" class="form-control input-sm pancard-valid" name="pancard" id="pancard" style="text-transform: uppercase;" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> GST No<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control input-sm gst-number-check" name="gst" id="gst" style="text-transform: uppercase;" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Email<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="email" class="form-control input-sm" name="email" id="email" required="" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Mobile<span style="color: red;">*</span></label>
                                <div class="col-sm-7">                                            
                                    <input type="text" pattern="[789][0-9]{9}" class="form-control input-sm" name="mobile" id="mobile" maxlength="10" required=""  
                                           onkeyup="if (/\D/g.test(this.value))
                                                       this.value = this.value.replace(/\D/g, '')"/>                                             
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Address<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="address" id="address" required="">
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave"  class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>

            </div>

        </div>
    </div>






