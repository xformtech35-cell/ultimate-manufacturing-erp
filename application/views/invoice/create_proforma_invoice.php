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
        content: '*';
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
                    Proforma Invoice
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'ProformaInvoiceController/index/' ?>"> Invoice</a></li>
                    <li class="active">Proforma Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        <!--                        <div class="col-md-2">
                                                    <a href="<?php //echo base_url();     ?>InvoiceController/create_non_gst_invoice"  class="btn btn-primary" role="button">Non GST</a>
                                                </div>-->
                        <div class="pull-right">
                            <div class="col-md-6">
                                <a href="<?php echo base_url(); ?>ProformaInvoiceController/create_central_gst_proforma_invoice" id="central_gst" name="central_gst" class="btn btn-primary" role="button">IGST</a>
                            </div>

                        </div>
                    </div>
                                                    <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create GST Proforma Invoice</h3>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                            

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>ProformaInvoiceController/add_proforma_invoice" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <?php
                                                if (date('m') <= 3) {//Upto March - previous FY
                                                    $financial_year = (date('y') - 1) . '-' . date('y');
                                                } else {//April onwards - current FY
                                                    $financial_year = date('y') . '-' . (date('y') + 1);
                                                }
                                                ?>
                                                <input type="hidden" name="gst_discount_check" value="gst_discount_check" id="gst_discount_check">
                                                <input type="hidden" name="gst_check" value="gst" id="gst_check">
                                                <input type="hidden" name="save_button_hide" value="save_button_hide" id="save_button_hide">
                                                <input type="hidden" class="form-control input-sm"   name="invoice_number" id="invoice_number" required="" value="PI/<?php printf("%04d", $invoice_id + 1); ?>/<?php echo $financial_year; ?>">
                                                <label for="inputEmail3" name="invoice_number" id="invoice_number" class="col-sm-12 control-label"> <h2> GST Proforma Invoice:<b>PI/<?php printf("%04d", $invoice_id + 1); ?>/<?php echo $financial_year; ?></b> </h2></label>

                                                <input type="hidden" class="form-control input-sm"   name="invoice_number_id" id="invoice_number_id" required="" value="<?php echo $invoice_id + 1; ?>">
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <!--                                            <div class="form-group row">
                                                                                            <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                                                            <div class="col-sm-8">
                                                                                                <select class="form-control input-sm company_search_name"  name="customer_id" id="customer_id" required="">
                                                                                                    <option value="">Select Company</option>
                                            <?php foreach ($company_name as $key) { ?>
                                                                                                                    <option value="<?php echo $key->customer_id; ?>"><?php echo $key->company_name; ?></option> 
                                            <?php } ?>  
                                                                                                </select>
                                                                                                <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
 
                                                                                            </div>
                                                                                        </div>-->

                                            <div class="form-group row required">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name"  name="customer_id" id="customer_id" required="">
                                                        <option value="">Select Company</option>
                                                        <?php foreach ($company_name as $key) { ?>
                                                            <option value="<?php echo $key->customer_id; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?> - ( <?php echo $key->state_code; ?> )</option> 
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
                                            
                                            
                                            
                                            

                                            <div class="form-group row">
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

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">PO Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm" name="invoice_po_date" id="invoice_po_date" onkeydown="return false;" autocomplete="off">
                                                </div>
                                            </div>
                                            
                                           

                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row required">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Invoice Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate  input-sm created-date" name="invoice_date" id="invoice_date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row required">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control  currentDateWithSevendays  input-sm payment-due-date-check" name="due_date" id="payment_due_date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Delivery Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control currentDateWithSevendays input-sm" name="delivery_date" id="delivery_date" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Note</label>
                                                <div class="col-sm-8">
                                                    <textarea  class="form-control" name="note" id="note" rows="2"><?php echo $settings['proforma_note']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Delivery Note No</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="delivery_note_no" id="delivery_note_no">
                                                </div>
                                            </div>
                                            
                                              


                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group row hide">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo $settings['proforma_subheading']; ?>" class="form-control" name="invoice_subheading" id="invoice_subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="invoice_footer" id="invoice_footer" rows="3"><?php echo $settings['proforma_footer']; ?></textarea>
                                                </div>
                                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Shipping To</label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm" id="shipping_address_dropdown" name="shipping_address_dropdown">
                                        <option value="">Select Address</option>
                                    </select>

                                    <textarea class="form-control" name="shipping_address" id="shipping_address" rows="3" style="margin-top:6px"></textarea>
                                </div>
                            </div>


                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Dispatch through</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="despatch_through" id="despatch_through">
                                                </div>
                                            </div>
                                             <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Vehicle No</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="vehicle_no" id="vehicle_no">
                                                </div>
                                            </div>

                                            <br>
<!--                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Sales Person Name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="sales_person" id="sales_person">
                                                </div>
                                            </div>-->

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="invoice_memo" id="invoice_memo" rows="3"><?php echo $settings['proforma_memo']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">  

                                    <table class="table table-bordered" id="dynamic_field">  
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>HSN Code</th>
                                            <th>QTY</th>
                                            <th>UNIT</th>
                                            <th>TAX</th>
                                            <th>SGST</th>
                                            <th>CGST</th>
                                            <th>Price</th>
                                            <th>Discount(%)</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                            <tr>  
                                                <td>
                                                    <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list"  name="product_name[]" id="item_name1" onchange="myFunction1(this.id)" required="" data-live-search="true">
                                                        <option></option>
   <option value="NEW">+ Add New Product</option>
                                                        <?php foreach ($item_name as $key) { ?>
                                                            <option value="<?php echo $key->code; ?>"><?php echo $key->code . " - " . $key->item_name; ?></option>
                                                        <?php } ?>  
                                                    </select>

                                                </td> 
                                                <td>

                                                <button type="button" class="btn btn-info" onClick="descButton(this.id)" id="btnDescriptionId1">Description</button>

                                                    <textarea style="width: 150px; "  class="form-control input-sm name_list description_auto hide" name="description[]" id="description1" rows="4"></textarea>
                                                    
                                                </td> 
                                                <td><input type="text" name="hsn[]" id="hsn1" required="" readonly class="form-control input-sm required_list name_list"  /></td> 

                                                <td class="hide"> <span id="total_quantity1" name="total_quantity[]"></span></td>
                                                <td><input type="text" name="quantity[]" id="quantity1" required="" class="form-control input-sm required_list name_list quantity_auto number-only-validation" value="1"  autocomplete="off"/></td> 
                                                <td>
                                                <select style="width: 100px" class="form-control input-sm  item_search_unit"  name="unit[]" id="unit1"  required="" data-live-search="true">
                                                        <option></option> 
                                                    </select>
                                                </td> 
                                                <td><input type="text" readonly="" name="gst_per[]" id="gst_per1" class="form-control input-sm name_list" /></td> 
                                                <td><input type="text" readonly="" name="sgst[]" id="sgst1" class="form-control input-sm sgst_list" /></td> 
                                                <td><input type="text" readonly="" name="cgst[]" id="cgst1" class="form-control input-sm cgst_list" /></td> 
                                                <td class="hide"><input type="text" readonly="" name="igst[]" id="igst1" class="form-control input-sm igst_list" /></td> 
                                                <td><input type="text" name="price[]" id="price1" required="" class="form-control input-sm required_list name_list price_auto" value="0.00" autocomplete="off"/></td>
                                                <td><input type="text" maxlength="5" name="discount[]" id="discount1" class="form-control input-sm name_list discount_auto number-only-validation" value="0" autocomplete="off"/></td>
                                                <td><input type="hidden" name="amount[]" id="amount1" class="form-control input-sm name_list amount_auto" value="0.00" />
                                                    <input type="hidden" name="amount_temp[]" id="amount_temp1" class="form-control input-sm name_list amount_auto"  value="0.00" />
                                                    <input type="hidden" name="gst_amount[]" id="gst_amount1" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                    <span id="span_amount1" name="span_amount[]">₹0.00</span>
                                                </td>
                                                <td><button type="button" name="add_gst" id="add_gst" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>  
                                            </tr>  
                                        </table>   
                                        <div align="center">

                                            <button type="submit" name="submit" id="submit"  class="btn btn-success">Save</button>
                                        </div>
                                        <div align="right" style="margin: 10px">
                                            <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span><br>
                                            <span id="total_amount" name="total_amount[]">Total: ₹0.00</span><br>
                                            <span id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                            <!--<span id="igst_amount" class="hide_igst" name="igst_amount[]">Total IGST Amount: ₹0.00</span><br>-->
<!--                                            <span id="total_gst_amount" name="total_gst_amount[]">Total GST Amount: ₹0.00</span><br>-->
                                            <b> <span id="grand_total_amount2" name="grand_total_amount2[]"><b>Grand Total:</b> ₹0.00</span></b><br>

                                            <b> Grand Total in Words:<span id="word2" name="word2"></span>Only.</b><br>

                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
                                           
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Terms &amp; Conditions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="proforma_terms_and_conditions" id="proforma_terms_and_conditions" rows="3"><?php echo $settings['proforma_terms_and_conditions']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Payment Terms</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="proforma_payment_terms" id="proforma_payment_terms" rows="3"><?php echo $settings['proforma_payment_terms']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Process Schedule</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="proforma_process_schedule" id="proforma_process_schedule" rows="3"><?php echo $settings['proforma_process_schedule']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Taxes</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="proforma_taxes" id="proforma_taxes" rows="3"><?php echo $settings['proforma_taxes']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Exclusions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="proforma_exclusions" id="proforma_exclusions" rows="3"><?php echo $settings['proforma_exclusions']; ?></textarea>
                                                </div>
                                            </div>
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

                    <center><h4 class="modal-title">Add Company <button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>

                </div>
              <!--                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ProformaInvoiceController/add_customer" enctype="multipart/form-data">-->
                <div class="modal-body">

                    <div class="card-body ">

                        <!-- form start -->
                        <div class="form-group row required">
                            <label for="inputEmail3" class="col-sm-4 control-label">Company  Name</label>
                            <div class="col-sm-7">
                                <input type="hidden" name="gst_check_customer" value="gst_check_customer" id="gst_check_customer">
                                <input type="text" class="form-control input-sm"  name="company_name" id="company_name" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm name-validation"  name="fullname" id="fullname" >
                            </div>
                        </div>

                         <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="15" class="form-control input-sm" name="gst" id="gst" style="text-transform: uppercase;" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="10" class="form-control input-sm " name="pancard" id="pancard" style="text-transform: uppercase;" >
                            </div>
                        </div>

                           <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                            <div class="col-sm-7">
                                <input type="number" class="form-control input-sm" name="state_code" id="state_code">
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
                                <input type="text" class="form-control input-sm" name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value))
                                                   this.value = this.value.replace(/\D/g, '')"/>                                             
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
                    <button type="submit" id="btnSave"  class="btn btn-success performa_submit" >Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
                <!--                </form>-->

            </div>

        </div>
    </div>

    <script>
    function updateTotalQty() {
        var totalQty = 0;
        $('input[name="quantity[]"]').each(function() {
            totalQty += parseFloat($(this).val()) || 0;
        });
        $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
    }

    $(document).ready(function () {
        updateTotalQty();
        $(document).on('input change', 'input[name="quantity[]"]', function() {
            updateTotalQty();
        });
        setInterval(updateTotalQty, 500);

        CKEDITOR.replace('proforma_terms_and_conditions');
        CKEDITOR.replace('proforma_payment_terms');
        CKEDITOR.replace('proforma_process_schedule');
        CKEDITOR.replace('proforma_taxes');
        CKEDITOR.replace('proforma_exclusions');

        // Shipping address dropdown change handler - when user selects an address, fill textarea
        $(document).on('change', '#shipping_address_dropdown', function () {
            var selected = $(this).val();
            $('#shipping_address').val(selected);
        });

        // Auto-fetch shipping address when company is selected
        $('#customer_id').on('change', function() {
            var customerId = $(this).val();

             var selectedText = $(this).find('option:selected').text();
            var companyStateCode = (selectedText.match(/\(\s*(\d+)\s*\)/) || [])[1];
             setupCompanySelectValidation(
                customerId,
                companyStateCode,
                <?php echo json_encode($settings['state_code']); ?>,
                '<?php echo base_url(); ?>ProformaInvoiceController/create_proforma_invoice',
            '<?php echo base_url(); ?>ProformaInvoiceController/create_central_gst_proforma_invoice',
                'sgst'
            );
            
            if (customerId) {
                $.ajax({
                    url: '<?php echo base_url(); ?>ProformaInvoiceController/get_customer_shipping_address',
                    type: 'POST',
                    data: { customer_id: customerId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Clear existing options except "Select Address"
                            $('#shipping_address_dropdown').find('option:not(:first)').remove();
                            
                            // Populate dropdown with all company addresses
                            if (response.addresses && response.addresses.length > 0) {
                                $.each(response.addresses, function(index, addr) {
                                    if (addr && addr.trim() !== '') {
                                        $('#shipping_address_dropdown').append(
                                            $('<option>', {
                                                value: addr,
                                                text: addr.length > 60 ? addr.substring(0, 60) + '...' : addr
                                            })
                                        );
                                    }
                                });
                                // Auto-select first address
                                $('#shipping_address_dropdown').find('option:eq(1)').prop('selected', true);
                            }
                            
                            // Set default address in textarea
                            $('#shipping_address').val(response.address);
                        } else {
                            $('#shipping_address').val('');
                            $('#shipping_address_dropdown').find('option:not(:first)').remove();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching customer address:', error);
                    }
                });
            } else {
                $('#shipping_address').val('');
                $('#shipping_address_dropdown').find('option:not(:first)').remove();
            }
        });

        // Trigger change on page load if a company is pre-selected
if ($('#customer_id').val()) {
    $('#customer_id').trigger('change');
}

        // If address contains multiple lines, keep it as-is
        $(document).trigger('change');

        // Auto-fill on page load (first option)
        var initialSelected = $('#shipping_address_dropdown').val();
        if (initialSelected) {
            $('#shipping_address').val(initialSelected);
        }
        
       


              
    });

    // ========== NEW CODE: Auto-fill PAN and State Code from GST Number ==========
    $(document).off('blur', '#gst').on('blur', '#gst', function() {
        var gstNo = $(this).val().trim().toUpperCase();
        
        if (gstNo.length === 0) {
            $('#pancard').val('');
            $('#state_code').val('');
            return;
        }
        
        if (gstNo.length !== 15) {
            alert('GST No must be 15 characters long. Example: 27AAPFU0205R1Z0');
            $(this).val('');
            $('#pancard').val('');
            $('#state_code').val('');
            $(this).focus();
            return;
        }
        
               // var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
                var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;

        if (!gstRegex.test(gstNo)) {
            alert('Invalid GST format. Expected: 2 digits + PAN + 1 digit + 1 letter + 1 digit\nExample: 27AAPFU0205R1Z0');
            $(this).val('');
            $('#pancard').val('');
            $('#state_code').val('');
            $(this).focus();
            return;
        }
        
        // Extract PAN (characters 2-11) and fill PAN field
        var panNo = gstNo.substring(2, 12);
        $('#pancard').val(panNo);
        
        // Extract State Code (first two digits) and fill State Code field
        var stateCode = gstNo.substring(0, 2);
        $('#state_code').val(stateCode);
    });

    </script>
</body>