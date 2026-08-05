<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "views/admin/modal.php");

?>
<style>
.required label {
    font-weight: bold;
}

.required label:after {
    color: #e32;
    content: ' *';
    display: inline;
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
                    Invoice
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InvoiceController/index/' ?>">Invoice</a></li>
                    <li class="active">Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        <!--                        <div class="col-md-2">
                            <a href="<?php //echo base_url(); 
                                        ?>InvoiceController/create_non_gst_invoice"  class="btn btn-primary" role="button">Non GST</a>
                        </div>-->
                        <div class="pull-right">
                            <div class="col-md-6">
                                <a href="<?php echo base_url(); ?>InvoiceController/create_central_gst_invoice"
                                    id="central_gst" name="central_gst" class="btn btn-primary" role="button">IGST</a>
                            </div>

                        </div>
                    </div>
                    <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i
                            class="fa fa-close"></i> Close</a>

                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create GST Invoice</h3>

                            </div>
                            <!-- /.box-header -->









                            <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post"
                                action="<?php echo base_url(); ?>InvoiceController/add_invoice"
                                enctype="multipart/form-data">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group row ">
                                            <?php
                                                $next_invoice_number = !empty($next_invoice_name)
                                                    ? $next_invoice_name
                                                    : 'INV/' . sprintf("%04d", ((int) $invoice_id) + 1);
                                                ?>

                                            <input type="hidden" name="gst_discount_check" value="gst_discount_check"
                                                id="gst_discount_check">
                                            <input type="hidden" name="gst_check" value="gst" id="gst_check">
                                            <input type="hidden" name="save_button_hide" value="save_button_hide"
                                                id="save_button_hide">

                                            <div class="col-md-6" style="margin-bottom: 10px;">
                                                <label for="invoice_number" class="control-label">Invoice Name <span
                                                        style="color: red;">*</span></label>
                                                <input type="text" class="form-control input-sm" name="invoice_number"
                                                    id="invoice_number" required=""
                                                    value="<?php echo $next_invoice_number; ?>"
                                                    style="text-transform: uppercase;">
                                            </div>

                                            <label for="invoice_number" class="col-sm-12 control-label">
                                                <h2>GST Invoice: <b
                                                        id="invoice_number_preview"><?php echo $next_invoice_number; ?></b>
                                                </h2>
                                            </label>

                                            <input type="hidden" class="form-control input-sm" name="invoice_number_id"
                                                id="invoice_number_id" required=""
                                                value="<?php echo (int) $invoice_id; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Company<span
                                                    style="color: red;">*</span></label>
                                            <div class="col-sm-8">
                                                <select class="form-control input-sm company_search_name"
                                                    name="customer_id" id="customer_id" required="">
                                                    <option value="">Select Company</option>
                                                    <?php foreach ($company_name as $key) { ?>
                                                    <option value="<?php echo $key->customer_id; ?>">
                                                        <?php echo $key->company_name . " - " . $key->c_code; ?> - (
                                                        <?php echo $key->state_code; ?> )</option>
                                                    <?php } ?>
                                                </select>
                                                <span class="btn btn-success btn-sm" data-toggle="modal"
                                                    data-target="#myModal" style="margin-top: 10%"><i
                                                        class="glyphicon glyphicon-plus"></i>Add Company</span>

                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                            <div class="col-sm-8">
                                                <select class="form-control input-sm " name="status" id="status">
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
                                            <label for="inputEmail3" class="col-sm-4 control-label">Payment
                                                Method</label>
                                            <div class="col-sm-8">
                                                <select class="form-control input-sm " name="payment_method"
                                                    id="payment_method">
                                                    <option value="">Select Payment Method</option>
                                                    <option value="1">Cash</option>
                                                    <option value="2">Cheque</option>
                                                    <option value="3">NetBanking</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- <div class="form-group row ">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Customer PO</label>
                                            <div class="col-sm-8">
                                                <select class="form-control input-sm" name="invoice_customer_po"
                                                    id="invoice_customer_po" data-live-search="true">
                                                    <option value="">Select PO</option>
                                                </select>
                                            </div>
                                        </div> -->

                                                          <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Customer
                                                    PO</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm"
                                                        name="invoice_customer_po" id="invoice_customer_po">

                                                </div>
                                            </div>


                                        <div class="form-group row ">
                                            <label for="inputEmail3" class="col-sm-4 control-label">PO Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control date input-sm"
                                                    name="invoice_po_date" id="invoice_po_date"
                                                    onkeydown="return false;">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Dispatch
                                                through</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control input-sm" name="despatch_through"
                                                    id="despatch_through">
                                            </div>
                                        </div>



                                    </div>
                                    <div class="col-md-3">

                                        <div class="form-group row ">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Invoice Date<span
                                                    style="color: red;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control alldate input-sm created-date"
                                                    name="invoice_date" id="invoice_date" required=""
                                                    onkeydown="return false;">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span
                                                    style="color: red;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control date input-sm payment-due-date-check currentDateWithSevendays"
                                                    name="due_date" id="payment_due_date" required=""
                                                    onkeydown="return false;">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Delivery
                                                Date</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control currentDateWithSevendays  input-sm"
                                                    name="delivery_date" id="delivery_date" onkeydown="return false;">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" name="note" id="note"
                                                    rows="2"><?php echo $settings['invoice_notes']; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Delivery Note
                                                No</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control input-sm" name="delivery_note_no"
                                                    id="delivery_note_no">
                                            </div>
                                        </div>


                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group row hide">
                                            <label for="inputEmail3" id="subheading1"
                                                class="col-sm-4 control-label">Subheading</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    value="<?php echo $settings['invoice_subheading']; ?>"
                                                    class="form-control" name="invoice_subheading"
                                                    id="invoice_subheading">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" name="invoice_footer" id="invoice_footer"
                                                    rows="3"><?php echo $settings['invoice_footer']; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Shipping To</label>
                                            <div class="col-sm-8">
                                                <select class="form-control input-sm" id="shipping_address_dropdown"
                                                    name="shipping_address_dropdown">
                                                    <option value="">Select Address</option>
                                                </select>

                                                <textarea class="form-control" name="shipping_address"
                                                    id="shipping_address" rows="3" style="margin-top:6px"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Vehicle No</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control input-sm" name="vehicle_no"
                                                    id="vehicle_no">
                                            </div>
                                        </div>

                                      
                                   
                                        
                                        <!-- Hidden field to store final grand total for database -->
                                        <input type="hidden" name="final_grand_total" id="final_grand_total" value="0">
                                        <br>
                                        <div class="form-group row hide">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Sales Person
                                                Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control input-sm" name="sales_person"
                                                    id="sales_person">
                                            </div>
                                        </div>

                                        <div class="form-group row hide">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" name="invoice_memo" id="invoice_memo"
                                                    rows="3"><?php echo $settings['invoice_memo']; ?></textarea>
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
                                        <th>GST</th>
                                        <th>SGST</th>
                                        <th>CGST</th>
                                        <th>Price</th>
                                        <th>Discount(%)</th>
                                        <th>Amount</th>
                                        <th>Action

                                            <tr>
                                                <td>
                                                    <select style="width: 150px"
                                                        class="form-control input-sm product_name_auto item_search_name name_list"
                                                        name="product_name[]" id="item_name1"
                                                        onchange="myFunction1(this.id)" required=""
                                                        data-live-search="true">
                                                        <option value="">Select Product</option>
                                                        <option value="NEW">+ Add New Product</option>
                                                        <?php foreach ($item_name as $key) { ?>
                                                        <option value="<?php echo  $key->code; ?>">
                                                            <?php echo $key->code . " - " . $key->item_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <input type="hidden" name="product_code[]" id="product_code1"
                                                        class="product_code">
                                                </td>
                                                <td>

                                                    <button type="button" class="btn btn-info"
                                                        onClick="descButton(this.id)"
                                                        id="btnDescriptionId1">Description</button>

                                                    <textarea style="width: 150px; "
                                                        class="form-control input-sm name_list description_auto hide"
                                                        name="description[]" id="description1" rows="4"></textarea>

                                                </td>
                                                <td><input type="text" name="hsn[]" id="hsn1" required="" readonly
                                                        class="form-control input-sm required_list name_list" /></td>

                                                <td class="hide"> <span id="total_quantity1"
                                                        name="total_quantity[]"></span></td>
                                                <td><input type="text" name="quantity[]" id="quantity1" required=""
                                                        class="form-control input-sm required_list name_list quantity_auto number-only-validation"
                                                        value="1" autocomplete="off" /></td>
                                                <td>
                                                    <select style="width: 100px"
                                                        class="form-control input-sm  item_search_unit" name="unit[]"
                                                        id="unit1" required="" data-live-search="true">
                                                        <option></option>
                                                    </select>
                                                </td>
                                                <td><input type="text" readonly="" name="gst_per[]" id="gst_per1"
                                                        class="form-control input-sm name_list" /></td>
                                                <td><input type="text" readonly="" name="sgst[]" id="sgst1"
                                                        class="form-control input-sm sgst_list" /></td>
                                                <td><input type="text" readonly="" name="cgst[]" id="cgst1"
                                                        class="form-control input-sm cgst_list" /></td>
                                                <td class="hide"><input type="text" readonly="" name="igst[]" id="igst1"
                                                        class="form-control input-sm igst_list" /></td>
                                                <td><input type="text" name="price[]" id="price1" required=""
                                                        class="form-control input-sm required_list name_list price_auto"
                                                        value="0.00" autocomplete="off" /></td>
                                                <td><input type="text" maxlength="5" name="discount[]" id="discount1"
                                                        class="form-control input-sm name_list discount_auto number-only-validation"
                                                        value="0" autocomplete="off" /></td>
                                                <td><input type="hidden" name="amount[]" id="amount1"
                                                        class="form-control input-sm name_list amount_auto"
                                                        value="0.00" />
                                                    <input type="hidden" name="amount_temp[]" id="amount_temp1"
                                                        class="form-control input-sm name_list amount_auto"
                                                        value="0.00" />
                                                    <input type="hidden" name="gst_amount[]" id="gst_amount1"
                                                        class="form-control input-sm name_list gst_amount_auto"
                                                        value="0.00" />
                                                    <span id="span_amount1" name="span_amount[]">₹0.00</span>
                                                </td>
                                                <td>
                                                    <button type="button" name="add_gst" id="add_gst"
                                                        class="btn btn-xs btn-success"><i class="fa fa-plus-circle"
                                                            aria-hidden="true"></i></button>
                                        </th>
                                        </td>
                                        </tr>
                                    </table>
                                    <div align="center">

                                        <button type="submit" name="submit" id="submit"
                                            class="btn btn-success">Save</button>
                                    </div>
                                    <div align="right" style="margin: 10px">
                                        <input type="hidden" name="total_before_tax" id="total_before_tax"
                                            class="form-control input-sm name_list" value="0.00" />
                                        <input type="hidden" name="total_gst_amount" id="total_gst_amount"
                                            class="form-control input-sm name_list" value="0.00" />

                                        <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total
                                            Item Qty: 0</span><br>
                                        <span id="total_amount" name="total_amount[]">Total: ₹0.00</span><br>
                                        <span id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                        <span id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                <span id="igst_amount" class="hide_igst" name="igst_amount[]">Total IGST Amount: ₹0.00</span><br>
                                                                               <span id="total_gst_amount" name="total_gst_amount[]">Total GST Amount: ₹0.00</span><br>                                        <b> <span id="grand_total_amount2" name="grand_total_amount2[]"><b>Grand
                                                    Total:</b> ₹0.00</span></b><br>

                                        <b> Grand Total in Words:<span id="word2" name="word2"></span>Only.</b><br>

                                        <input type="hidden" name="total_quotation_amount" id="total_quotation_amount"
                                            class="form-control input-sm name_list" value="0.00" />

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-2 control-label">Terms &amp;
                                                Conditions</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="invoice_terms_and_conditions"
                                                    id="invoice_terms_and_conditions"
                                                    rows="3"><?php echo $settings['invoice_terms_and_conditions']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-2 control-label">Payment
                                                Terms</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="invoice_payment_terms"
                                                    id="invoice_payment_terms"
                                                    rows="3"><?php echo isset($invoice_data_group['invoice_payment_terms']) ? $invoice_data_group['invoice_payment_terms'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-2 control-label">Process
                                                Schedule</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="invoice_process_schedule"
                                                    id="invoice_process_schedule"
                                                    rows="3"><?php echo $settings['invoice_process_schedule']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-2 control-label">Taxes</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="invoice_taxes" id="invoice_taxes"
                                                    rows="3"><?php echo $settings['invoice_taxes']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-2 control-label">Exclusions</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="invoice_exclusions"
                                                    id="invoice_exclusions"
                                                    rows="3"><?php echo $settings['invoice_exclusions']; ?></textarea>
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

                    <center>
                        <h4 class="modal-title">Add Company <button type="button" class="close"
                                data-dismiss="modal">&times;</button></h4>
                    </center>

                </div>
                <!--<form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InvoiceController/add_customer" enctype="multipart/form-data">-->
                <div class="modal-body">

                    <div class="card-body ">

                        <!-- form start -->
                        <div class="form-group row required">
                            <label for="inputEmail3" class="col-sm-4 control-label">Company Name</label>
                            <div class="col-sm-7">
                                <input type="hidden" name="gst_check_customer" value="gst_check_customer"
                                    id="gst_check_customer">
                                <input type="text" class="form-control input-sm" name="company_name" id="company_name"
                                    required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm name-validation" name="fullname"
                                    id="fullname">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="15" class="form-control input-sm" name="gst" id="gst"
                                    style="text-transform: uppercase;">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="10" class="form-control input-sm " name="pancard"
                                    id="pancard" style="text-transform: uppercase;">
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
                                <input type="tel" class="form-control input-sm" name="mobile" id="mobile" maxlength="10"
                                    onkeyup="if (/\D/g.test(this.value))
                                                   this.value = this.value.replace(/\D/g, '')" />
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
                    <button type="submit" id="btnSave" class="btn btn-success performa_submit">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
                <!--</form>-->

            </div>

        </div>
    </div>


    <!--   <script>
//    if (!("autofocus" in document.createElement("input")))
//            document.getElementById("item_name1").focus();
        $('#item_name1').select2('open');
</script>-->

    <script type="text/javascript">
    document.getElementById('item_name1').open();
    </script>



    <script>
    function updateTotalQty() {
        var totalQty = 0;
        $('input[name="quantity[]"]').each(function() {
            totalQty += parseFloat($(this).val()) || 0;
        });
        $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
    }

    $(document).ready(function() {
        updateTotalQty();
        $(document).on('input change', 'input[name="quantity[]"]', function() {
            updateTotalQty();
        });
        $('#invoice_number').on('input', function() {
            var invoiceName = $(this).val().toUpperCase();
            $(this).val(invoiceName);
            $('#invoice_number_preview').text(invoiceName || '-');
        });
        setInterval(updateTotalQty, 500);

        CKEDITOR.replace('invoice_terms_and_conditions');
        CKEDITOR.replace('invoice_payment_terms');
        CKEDITOR.replace('invoice_process_schedule');
        CKEDITOR.replace('invoice_taxes');
        CKEDITOR.replace('invoice_exclusions');

        // Shipping address dropdown change handler - when user selects an address, fill textarea
        $(document).on('change', '#shipping_address_dropdown', function() {
            var selected = $(this).val();
            $('#shipping_address').val(selected);
        });

        // Auto-fetch shipping address and PO details when company is selected
        $('#customer_id').on('change', function() {
            var customerId = $(this).val();
            // Setup state code matching for GST/IGST routing
            // State code 27 = GST (our state), other state codes = IGST
            var selectedText = $(this).find('option:selected').text();
            var companyStateCode = (selectedText.match(/\(\s*(\d+)\s*\)/) || [])[1];

            setupCompanySelectValidation(
                customerId,
                companyStateCode,
                <?php echo json_encode($settings['state_code']); ?>,
                '<?php echo base_url(); ?>InvoiceController/create_invoice',
                '<?php echo base_url(); ?>InvoiceController/create_central_gst_invoice',
                'sgst'
            );

$('#invoice_customer_po').on('change', function () {

    var selectedText = $("#invoice_customer_po option:selected").text();

    var matches = selectedText.match(/\((.*?)\)/);

    if (matches && matches[1]) {

        var dateText = matches[1];

        var parts = dateText.split('-');

        var newDate = new Date(parts[2], parts[1] - 1, parts[0]);

        $('#invoice_po_date').datepicker('setDate', newDate);
    }
});

$('#invoice_po_date').datepicker({
    dateFormat: 'dd-mm-yy'
});


            // Fetch all PO details (PO Number and PO Date) for the selected company
            if (customerId) {
                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/get_sales_order_po_number',
                    type: 'POST',
                    data: {
                        customer_id: customerId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Clear existing options
                        $('#invoice_customer_po').empty().append(
                            '<option value="">Select PO</option>');
                        $('#invoice_po_date').val('');

                        if (response.success && response.data && response.data.length > 0) {

                        $.each(response.data, function(index, po) {

                            var poNumber = po.po_number || 'No PO';

                            var poDate = 'No date';

                            if (po.po_date) {
                                var date = new Date(po.po_date);

                                var day = String(date.getDate()).padStart(2, '0');
                                var month = String(date.getMonth() + 1).padStart(2, '0');
                                var year = date.getFullYear();

                                poDate = day + '-' + month + '-' + year;
                            }

                            var displayText = poNumber + ' - (' + poDate + ')';

                            //alert(displayText);

                            $('#invoice_customer_po').append(
                                $('<option>', {
                                    value: po.po_number,
                                    text: displayText
                                })
                            );
                        });
                    }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching PO details:', error);
                    }
                });
            }

   


            

            // Auto-populate PO Date when PO is selected from dropdown
            // $(document).off('change', '#invoice_customer_po').on('change', '#invoice_customer_po',
            //     function() {
            //         var selectedOption = $(this).find('option:selected');
            //         var poDate = selectedOption.data('po-date');

            //         if (poDate && poDate.trim() !== '') {
            //             // Parse date - handle both YYYY-MM-DD and DD-MM-YYYY formats
            //             var dateParts, formattedDate;

            //             if (poDate.indexOf('-') !== -1) {
            //                 dateParts = poDate.split('-');
            //                 if (dateParts[0].length === 4) {
            //                     // Format is YYYY-MM-DD, convert to dd-mm-yy
            //                     formattedDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[
            //                         0].slice(-2);
            //                 } else {
            //                     // Format is DD-MM-YYYY, convert to dd-mm-yy
            //                     formattedDate = dateParts[0] + '-' + dateParts[1] + '-' + dateParts[
            //                         2].slice(-2);
            //                 }
            //             } else if (poDate.indexOf('/') !== -1) {
            //                 dateParts = poDate.split('/');
            //                 if (dateParts[0].length === 4) {
            //                     // Format is YYYY/MM/DD
            //                     formattedDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[
            //                         0].slice(-2);
            //                 } else {
            //                     // Format is DD/MM/YYYY
            //                     formattedDate = dateParts[0] + '-' + dateParts[1] + '-' + dateParts[
            //                         2].slice(-2);
            //                 }
            //             } else {
            //                 formattedDate = poDate;
            //             }

            //             $('#invoice_po_date').val(formattedDate);
            //         } else {
            //             $('#invoice_po_date').val('');
            //         }
            //     });





            if (customerId) {
                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/get_customer_shipping_address',
                    type: 'POST',
                    data: {
                        customer_id: customerId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Clear existing options except "Select Address"
                            $('#shipping_address_dropdown').find('option:not(:first)')
                                .remove();

                            // Populate dropdown with all company addresses
                            if (response.addresses && response.addresses.length > 0) {
                                $.each(response.addresses, function(index, addr) {
                                    if (addr && addr.trim() !== '') {
                                        $('#shipping_address_dropdown').append(
                                            $('<option>', {
                                                value: addr,
                                                text: addr.length > 60 ?
                                                    addr.substring(0, 60) +
                                                    '...' : addr
                                            })
                                        );
                                    }
                                });
                                // Auto-select first address
                                $('#shipping_address_dropdown').find('option:eq(1)').prop(
                                    'selected', true);
                            }

                            // Set default address in textarea
                            $('#shipping_address').val(response.address);
                        } else {
                            $('#shipping_address').val('');
                            $('#shipping_address_dropdown').find('option:not(:first)')
                                .remove();
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

        // Auto-fetch items when PO is selected from dropdown
        $(document).on('change', '#invoice_customer_po', function() {
            var poNumber = $(this).val();
            
            console.log('PO Selected:', poNumber);
            
            // Clear existing item rows (but keep the first row)
            var rowCount = $('#dynamic_field tr').length;
            for (var i = rowCount - 1; i > 1; i--) {
                $('#dynamic_field tr:eq(' + i + ')').remove();
            }

            if (poNumber && poNumber.trim() !== '') {
                // Fetch items for the selected PO
                $.ajax({
                    url: '<?php echo base_url(); ?>InvoiceController/get_po_items',
                    type: 'POST',
                    data: {
                        po_number: poNumber
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('PO Items Response:', response);
                        if (response.success && response.data && response.data.length > 0) {
                            // Clear first row
                            clearFirstRow();
                            
                            var rowNumber = 1;
                            $.each(response.data, function(index, item) {
                                if (index === 0) {
                                    // Populate first row
                                    populateItemRow(rowNumber, item);
                                } else {
                                    // Add new rows for additional items
                                    addNewItemRow(rowNumber + 1, item);
                                    rowNumber++;
                                }
                            });
                            
                            // Trigger calculation updates
                            $('.price_auto, .quantity_auto, .discount_auto').trigger('change');
                            console.log('Items populated successfully');
                        } else {
                            console.log('No items found in response');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching PO items:', error);
                        console.error('XHR Response:', xhr.responseText);
                        alert('Error fetching items from PO: ' + error);
                    }
                });
            } else {
                console.log('No PO selected');
            }
        });

        // Helper function to clear the first item row
        function clearFirstRow() {
            $('#item_name1').val('').trigger('change');
            $('#product_code1').val('');
            $('#description1').val('');
            $('#hsn1').val('');
            $('#quantity1').val('1');
            $('#unit1').val('').trigger('change');
            $('#gst_per1').val('0');
            $('#sgst1').val('0');
            $('#cgst1').val('0');
            $('#igst1').val('0');
            $('#price1').val('0.00');
            $('#discount1').val('0');
            $('#amount1').val('0.00');
            $('#span_amount1').text('₹0.00');
        }

        // Helper function to populate item row
        function populateItemRow(rowNum, item) {
            $('#item_name' + rowNum).val(item.product_name).trigger('change');
            $('#product_code' + rowNum).val(item.product_name);
            $('#description' + rowNum).val(item.description);
            $('#hsn' + rowNum).val(item.hsn);
            $('#quantity' + rowNum).val(item.quantity);
            $('#unit' + rowNum).val(item.unit).trigger('change');
            $('#gst_per' + rowNum).val(item.gst_per);
            $('#sgst' + rowNum).val(item.sgst);
            $('#cgst' + rowNum).val(item.cgst);
            $('#igst' + rowNum).val(item.igst);
            $('#price' + rowNum).val(item.price);
            $('#discount' + rowNum).val(item.discount);
        }

        // Helper function to add new item rows by cloning the first row
        function addNewItemRow(rowNum, item) {
            // Clone the first row to get all its attributes, classes, and structure
            var firstRow = $('#dynamic_field tr:last');
            var newRow = firstRow.clone();
            
            // Update all IDs and names in the cloned row with new row number
            newRow.find('[id]').each(function() {
                var newId = $(this).attr('id').replace(/\d+$/, rowNum);
                $(this).attr('id', newId);
            });
            
            newRow.find('[name]').each(function() {
                // Keep the name attribute as is since it's an array (name[])
                // Just ensure the ID is updated
            });
            
            // Update the description button ID
            var descBtn = newRow.find('[id^="btnDescriptionId"]');
            if (descBtn.length) {
                descBtn.attr('id', 'btnDescriptionId' + rowNum);
            }
            
            // Update total_quantity span
            var totalQtySpan = newRow.find('[id^="total_quantity"]');
            if (totalQtySpan.length) {
                totalQtySpan.attr('id', 'total_quantity' + rowNum);
            }
            
            // Add the cloned row to the table
            $('#dynamic_field').append(newRow);
            
            // Now populate the new row with item data
            populateItemRow(rowNum, item);
        }

        // Trigger change on page load if a company is pre-selected
        if ($('#customer_id').val()) {
            $('#customer_id').trigger('change');
        }

        $('#add_name').on('submit', function() {
            // Update final grand total for database submission
            var grandTotalText = $('#grand_total_amount2').text();
            var match = grandTotalText.match(/₹([\d,]+\.?\d*)/);
            if (match) {
                var finalTotal = parseFloat(match[1].replace(/,/g, ''));
                $('#final_grand_total').val(finalTotal.toFixed(2));
            }
            
            // Update CKEditor instances before submission
            for (var instanceName in CKEDITOR.instances) {
                if (CKEDITOR.instances.hasOwnProperty(instanceName)) {
                    CKEDITOR.instances[instanceName].updateElement();
                }
            }
        });



    });

    // Auto-fill PAN and State Code from GST Number (for Add Company modal)
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

        var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
        if (!gstRegex.test(gstNo)) {
            alert(
                'Invalid GST format. Expected: 2 digits + PAN + 1 digit + 1 letter + 1 digit\nExample: 27AAPFU0205R1Z0'
                );
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

   

  

   


    // Recalculate TDS/TCS when grand total changes
    $(document).on('change', '.price_auto, .quantity_auto, .discount_auto', function() {
        setTimeout(function() {
            calculateTDS_TCSAmount();
        }, 500);
    });
    </script>