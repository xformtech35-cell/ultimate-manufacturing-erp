<?php
//print_r($status_result);die();
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."views/admin/modal.php");

$invoice_data_group = isset($invoice_data_group) && is_array($invoice_data_group) ? $invoice_data_group : array();
$invoice_data_group = array_merge(array(
    'invoice_number' => '',
    'company_name' => '',
    'customer_po' => '',
    'po_date' => '',
    'despatch_through' => '',
    'invoice_date' => '',
    'payment_due_date' => '',
    'delivery_date' => '',
    'note' => '',
    'delivery_note_no' => '',
    'invoice_footer' => '',
    'shipping_address' => '',
    'vehicle_no' => '',
    'sales_person' => '',
    'invoice_memo' => '',
    'invoice_terms_and_conditions' => '',
    'invoice_payment_terms' => '',
    'invoice_process_schedule' => '',
    'invoice_taxes' => '',
    'invoice_exclusions' => ''
), $invoice_data_group);

$show_invoice = isset($show_invoice) && is_array($show_invoice) ? $show_invoice : array();
$customer_result = isset($customer_result) && is_array($customer_result) ? $customer_result : array();
$item_name = isset($item_name) && is_array($item_name) ? $item_name : array();
$status_result = isset($status_result) && is_array($status_result) ? $status_result : array();

$invoice_status = 1;
if (isset($status_result[0]) && isset($status_result[0]->status)) {
    $invoice_status = (int) $status_result[0]->status;
}

$invoice_display_date = '';
if (!empty($invoice_data_group['invoice_date'])) {
    $invoice_timestamp = strtotime($invoice_data_group['invoice_date']);
    if ($invoice_timestamp !== false) {
        $invoice_display_date = date('d-m-Y', $invoice_timestamp);
    }
}

?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Edit GST Invoice
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">GST Invoice</a></li>
                    <li class="active">GST Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">

                                <h3 class="box-title">Edit GST Invoice</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                                     <!--<button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Customer</button>-->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                            
                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>InvoiceController/edit_invoice" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <input type="hidden" name="edit_invoice_stock_check" value="edit_invoice_stock_check" id="edit_invoice_stock_check">

                                                <input type="hidden" class="form-control input-sm" name="invoice_number" id="invoice_number" required="" value="<?php echo $invoice_data_group['invoice_number'] ?? ''; ?>">
                                                <label for="inputEmail3" name="invoice_number" id="invoice_number" class="col-sm-12 control-label"><h2> Invoice:<b> <?php echo $invoice_data_group['invoice_number'] ?? ''; ?></b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8" id="append_to_dropdown">
                                                    <select class="col-md-12 company_search_name" name="customer_id" id="customer_id" required id="">
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $company_name = $invoice_data_group['company_name'] ?? '';

                                                        foreach ($customer_result as $row) {
                                                            ?>
                                                            <option value="<?php echo $row->customer_id ?>"  
                                                            <?php
                                                            if ($company_name == $row->company_name) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?> ><?php echo $row->company_name . " - " . $row->c_code; ?></option>
                                                                <?php }
                                                                ?>
                                                    </select>
                                                    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>

                                                </div>

                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm "  name="status" id="status">
                                                        <?php foreach (array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled') as $status_value => $status_label) { ?>
                                                            <option value="<?php echo $status_value; ?>" <?php echo ($invoice_status === $status_value) ? 'selected="selected"' : ''; ?>><?php echo $status_label; ?></option>
                                                        <?php } ?>
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
                                                    <input type="text" style="text-transform: uppercase;" class="form-control input-sm" name="invoice_customer_po" id="invoice_customer_po" value="<?php echo $invoice_data_group['customer_po'] ?? ''; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">PO Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm" name="invoice_po_date" id="invoice_po_date" value="<?php echo $invoice_data_group['po_date'] ?? ''; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Dispatch through</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="despatch_through" id="despatch_through" value="<?php echo $invoice_data_group['despatch_through'] ?? ''; ?>">
                                                </div>
                                            </div>

                                          

                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Invoice Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control holedate input-sm created-date" name="invoice_date" id="invoice_date" required="" value="<?php echo $invoice_display_date; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control holedate input-sm payment-due-date-check " name="payment_due_date" id="payment_due_date" autocomplete="off" required="" value="<?php echo $invoice_data_group['payment_due_date'] ?? ''; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Delivery Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control holedate input-sm" name="delivery_date" id="delivery_date" value="<?php echo $invoice_data_group['delivery_date'] ?? ''; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                                                <div class="col-sm-9">
                                                    <textarea  class="form-control" name="note" id="note" rows="2"><?php echo $invoice_data_group['note'] ?? ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Delivery Note No</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control input-sm" name="delivery_note_no" id="delivery_note_no" value="<?php echo $invoice_data_group['delivery_note_no'] ?? ''; ?>">
                                                </div>
                                            </div>

                                        </div>

                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Footer</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="invoice_footer" id="invoice_footer" rows="3"><?php echo $invoice_data_group['invoice_footer']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Shipping To</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="shipping_address" id="shipping_address" rows="3"><?php echo $invoice_data_group['shipping_address']; ?></textarea>
                                                </div>
                                            </div>

                                              <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Vehicle No</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="vehicle_no" id="vehicle_no" value="<?php echo $invoice_data_group['vehicle_no']; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row hide">
                                             <label for="inputEmail3" class="col-sm-3 control-label">Sales Person Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control input-sm" name="sales_person" id="sales_person" value="<?php echo $invoice_data_group['sales_person']; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Memo</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="invoice_memo" id="invoice_memo" rows="3"><?php echo $invoice_data_group['invoice_memo']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="table-responsive"> 
                                        

                                    <table class="table table-bordered" id="dynamic_field">  
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>HSN Code</th>
                                                    <th>QTY</th>
                                                    <th>UNIT</th>
                                                    <th class="gst_per">TAX(%)</th>
                                                    <th class="gst">SGST</th>
                                                    <th class="gst">CGST</th>
                                                    <th class="igst_edit_hide_show">IGST</th>
                                                    <th>Price</th>
                                                    <th>Discount(%)</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($show_invoice as $key) {
                                                    ?>
                                                    <tr> 
                                                        <td>
                                                            <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list"  name="product_name[]" id="item_name<?php echo $i; ?>" onchange="myFunction1(this.id)" required="" data-live-search="true">
                                                                <option></option>

                                                                <?php
                                                                foreach ($item_name as $row) {
                                                                    ?>
                                                                    <option value="<?php echo $row->code ?>"  
                                                                    <?php
                                                                    if ($key->product_name == $row->code) {
                                                                        echo 'selected="selected"';
                                                                    }
                                                                    ?> ><?php echo $row->code . " - " . $row->item_name; ?></option>
                                                                        <?php }
                                                                        ?>

                                                            </select>
                                                       
                                                            <input type="hidden" class="form-control input-sm" name="invoice_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->invoice_id; ?>">
                                                        </td> 
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-xs description-modal-btn" onClick="descButton(this.id)" id="btnDescriptionId<?php echo $i; ?>">Description</button>

                                                            <textarea style="width: 150px; display: none;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description<?php echo $i; ?>" rows="7"><?php echo $key->description; ?></textarea>
                                                        </td> 
                                                        <td><input type="text" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" required="" class="form-control input-sm name_list" readonly="" /></td> 

                                                        <td class="hide"> <span id="total_quantity<?php echo $i; ?>" name="total_quantity[]"></span></td>
                                                        <td><input type="text" min="1" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="1" /></td> 
                                                        <td>
                                                    <select style="width: 100px" class="form-control input-sm  item_search_unit"  name="unit[]" id="unit1"  required="" data-live-search="true">
                                                    <option value="<?php echo $key->unit ?>" ><?php echo $key->unit; ?></option>
                                                        </select>
                                                    </td> 
                                                        <td class="gst_per"><input type="text" readonly="" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" /></td> 
                                                        <td class="gst"><input type="text" readonly="" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" /></td> 
                                                        <td class="gst"><input type="text" readonly="" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" /></td> 
                                                        <td class="igst_edit_hide_show"><input type="text" readonly="" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" /></td> 
                                                        <td><input type="number" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" required="" class="form-control input-sm price_auto"  /></td>
                                                        <td><input type="text" maxlength="5" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list discount_auto number-only-validation" value="" /></td>
                                                        <td><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>
                                                            <input type="hidden" name="amount_temp[]" id="amount_temp<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->price * $key->quantity; ?>"/>
                                                            <input type="hidden" name="gst_amount[]" id="gst_amount<?php echo $i; ?>" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                            <span id="span_amount<?php echo $i; ?>" name="span_amount[]">₹<?php echo $key->amount; ?></span>
                                                        </td>
                                                        <td>
                                                            <?php if ($i == 1) { ?>

                                                                <?php if ($key->gst_type != 'S') { ?>
                                                                    <input type="hidden" name="quotation_igst_check" value="igst" id="quotation_igst_check">

                                                                    <input type="hidden" name="igst_edit_hide_show" value="igst_edit_hide_show" id="igst_edit_hide_show">
                                                                    <button type="button" name="edit_gst" id="edit_gst" class="btn btn-success btn-xs action-header-btn" title="Add Row"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 

                                                                <?php } else if ($key->gst_type != 'I') { ?>
                                                                    <input type="hidden" name="gst" value="gst" id="gst">
                                                                    <input type="hidden" name="edit_sgst_cgst_check" value="edit_sgst_cgst_check" id="edit_sgst_cgst_check">
                                                                    <input type="hidden" name="gst_discount_check" value="gst" id="gst_discount_check">
                                                                    <button type="button" name="edit_gst" id="edit_gst" class="btn btn-success btn-xs action-header-btn" title="Add Row"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 

                                                                <?php } else if (($key->gst_type != 'S') || ($key->gst_type != 'I')) { ?>
                                                                    <input type="hidden" name="non_gst" value="non_gst" id="non_gst">
                                                                    <button type="button" name="edit_non_gst" id="edit_non_gst" class="btn btn-success btn-xs action-header-btn" title="Add Row"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 
                                                                <?php } ?>

                                                            <?php } else { ?>
                                                                <button type="button" name="remove" title="edit_invoice" id="remove<?php echo $i; ?>" class="btn btn-danger btn-xs btn_remove"><i class="fa fa-times"></i></button>  
                                                            <?php } ?>
                                                        </td> 
                                                    </tr>  
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>  
                                        <div align="center">
                                            <button type="submit" name="submit" id="submit"  class="btn btn-success">Save</button>
                                        </div>
                                        
                                        
                                        <div align="right" style="margin: 10px">
                                            
                                       <input type="hidden" name="total_before_tax" id="total_before_tax" class="form-control input-sm name_list" value="0.00" />
                                       <input type="text" name="total_gst_amount" id="total_gst_amount" class="form-control input-sm name_list" value="0.00" />

                                           
                                            <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span><br>
                                            <span id="total_amount" name="total_amount[]" class="total_span_auto_amount">Total before Tax: ₹0.00</span><br>
                                            <span class="gst" id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span class="gst" id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                            <span class="igst_hide" id="igst_amount" name="igst_amount[]">IGST Amount: ₹0.00</span><br>
                                            <span id="grand_total_amount"><b>Grand Total:</b> ₹0.00</span><br>
                                            <b> Grand Total in Words:<span id="word2" name="word2"></span>Only.</b><br>

                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Terms & Conditions</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="invoice_terms_and_conditions" id="invoice_terms_and_conditions" rows="3"><?php echo isset($invoice_data_group['invoice_terms_and_conditions']) ? $invoice_data_group['invoice_terms_and_conditions'] : ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Payment Terms</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="invoice_payment_terms" id="invoice_payment_terms" rows="3"><?php echo isset($invoice_data_group['invoice_payment_terms']) ? $invoice_data_group['invoice_payment_terms'] : ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Process Schedule</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="invoice_process_schedule" id="invoice_process_schedule" rows="3"><?php echo isset($invoice_data_group['invoice_process_schedule']) ? $invoice_data_group['invoice_process_schedule'] : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Taxes</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="invoice_taxes" id="invoice_taxes" rows="3"><?php echo isset($invoice_data_group['invoice_taxes']) ? $invoice_data_group['invoice_taxes'] : ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Exclusions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="invoice_exclusions" id="invoice_exclusions" rows="3"><?php echo isset($invoice_data_group['invoice_exclusions']) ? $invoice_data_group['invoice_exclusions'] : ''; ?></textarea>
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

        <!-- ./Customer modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Company</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card-body ">
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
                                <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control " name="gst" id="gst" style="text-transform: uppercase;" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="pancard" id="pancard" style="text-transform: uppercase;" >
                                </div>
                            </div>

                           

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Email</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control " name="email" id="email" pattern="^([0-9a-zA-Z]([-.\.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Mobile</label>
                                <div class="col-sm-7">                                            
                                    <input type="text" class="form-control " name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')"  />
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
                                    <textarea class="form-control input-sm" name="address" id="address"></textarea>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="gst_check_customer" value="gst_check_customer">
                        <button type="button" id="btnSave"  class="btn btn-success performa_submit">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="control-sidebar-bg"></div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
    
    <style>
        .description-modal-btn {
            width: 100%;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Modal styles */
        .modal-header.bg-info {
            background-color: #5bc0de;
            color: white;
        }
        .modal-header.bg-success {
            background-color: #5cb85c;
            color: white;
        }
        .modal-header.bg-warning {
            background-color: #f0ad4e;
            color: white;
        }
        .modal-header.bg-primary {
            background-color: #337ab7;
            color: white;
        }
        .modal-header.bg-danger {
            background-color: #d9534f;
            color: white;
        }

        /* CKEditor modal styling */
        .cke_chrome {
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
        }

        /* Table styling */
        .table-responsive {
            overflow-x: auto;
        }

        #dynamic_field td {
            vertical-align: middle;
            padding: 8px;
        }

        #dynamic_field th {
            padding: 10px 8px;
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .select2-container {
            width: 100% !important;
        }

        .btn-xs {
            margin: 2px;
            padding: 3px 8px;
            font-size: 12px;
        }

        /* Action header button styling */
        .action-header-btn {
            margin-left: 5px;
        }

        .hide {
            display: none;
        }

        /* Responsive form adjustment */
        @media (max-width: 768px) {
            .col-sm-3.control-label {
                text-align: left;
            }
            .form-group {
                margin-bottom: 12px;
            }
        }
    </style>

    <script>
        // Function to convert number to words (Indian numbering system)
        function numberToWords(num) {
            const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            
            if (num === 0) return 'Zero';
            
            function convertLessThanThousand(n) {
                let result = '';
                
                if (n >= 100) {
                    result += ones[Math.floor(n / 100)] + ' Hundred ';
                    n %= 100;
                }
                
                if (n >= 20) {
                    result += tens[Math.floor(n / 10)] + ' ';
                    n %= 10;
                } else if (n >= 10) {
                    result += teens[n - 10] + ' ';
                    n = 0;
                }
                
                if (n > 0) {
                    result += ones[n] + ' ';
                }
                
                return result;
            }
            
            if (num < 0) num = -num;
            
            let crore = Math.floor(num / 10000000);
            num %= 10000000;
            
            let lakh = Math.floor(num / 100000);
            num %= 100000;
            
            let thousand = Math.floor(num / 1000);
            num %= 1000;
            
            let result = '';
            
            if (crore > 0) {
                result += convertLessThanThousand(crore) + 'Crore ';
            }
            
            if (lakh > 0) {
                result += convertLessThanThousand(lakh) + 'Lakh ';
            }
            
            if (thousand > 0) {
                result += convertLessThanThousand(thousand) + 'Thousand ';
            }
            
            if (num > 0) {
                result += convertLessThanThousand(num);
            }
            
            return result.trim() + ' Rupees Only';
        }

        // Calculate and update all totals
        function calculateAllTotals() {
            let basicTotal = 0;
            let sgstTotal = 0;
            let cgstTotal = 0;
            let igstTotal = 0;
            let totalQty = 0;
            $('input[name="quantity[]"]').each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });
            $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
            
            // Sum all amounts
            $('input[name="amount[]"]').each(function() {
                basicTotal += parseFloat($(this).val()) || 0;
            });
            
            let isIgst = $('#quotation_igst_check').length > 0;
            
            if (isIgst) {
                // Sum IGST values
                $('input[name="igst[]"]').each(function() {
                    igstTotal += parseFloat($(this).val()) || 0;
                });
            } else {
                // Sum SGST values
                $('input[name="sgst[]"]').each(function() {
                    sgstTotal += parseFloat($(this).val()) || 0;
                });
                
                // Sum CGST values
                $('input[name="cgst[]"]').each(function() {
                    cgstTotal += parseFloat($(this).val()) || 0;
                });
            }
            
            // Calculate grand total
            let grandTotal = basicTotal + sgstTotal + cgstTotal + igstTotal;
            
            // Update display elements
            $('#total_amount').text('Total: ₹' + basicTotal.toFixed(2));
            $('#total_before_tax').val(basicTotal.toFixed(2));
            
            $('#sgst_amount').text('SGST Amount: ₹' + sgstTotal.toFixed(2));
            $('#cgst_amount').text('CGST Amount: ₹' + cgstTotal.toFixed(2));
            $('#igst_amount').text('IGST Amount: ₹' + igstTotal.toFixed(2));
            
            $('#grand_total_amount').text('Grand Total: ₹' + grandTotal.toFixed(2));
            $('#total_quotation_amount').val(grandTotal.toFixed(2));
            
            // Convert to words
            let grandTotalAmount = Math.floor(grandTotal);
            let grandTotalWords = numberToWords(grandTotalAmount);
            $('#word2').text(grandTotalWords.replace(' Rupees Only', ''));
        }

        $(document).ready(function () {
            // Initial calculation
            calculateAllTotals();
            
            // Monitor for changes
            $(document).on('change input', 'input[name="price[]"], input[name="quantity[]"], input[name="sgst[]"], input[name="cgst[]"], input[name="igst[]"]', function() {
                calculateAllTotals();
            });
            
            // Periodic check
            setInterval(calculateAllTotals, 1000);
        });
    </script>
    
    <script>
        // Global flag to prevent multiple modal openings
        var isModalOpen = false;

        function descButton(buttonId) {
            // Prevent if modal is already open
            if (isModalOpen) {
                return;
            }
            
            isModalOpen = true;
            var rowNum = buttonId.replace('btnDescriptionId', '');
            var textarea = document.getElementById('description' + rowNum);
            var textareaContent = textarea ? textarea.value : '';
            
            // Remove existing modal if any
            $('#descriptionModal').remove();
            
            // Clean up any existing CKEditor instances
            if (CKEDITOR.instances['modalDescription' + rowNum]) {
                CKEDITOR.instances['modalDescription' + rowNum].destroy(true);
            }
            
            var modalHtml = '<div id="descriptionModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">' +
                '<div class="modal-dialog modal-lg">' +
                '<div class="modal-content">' +
                '<div class="modal-header bg-info">' +
                '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                '<h4 class="modal-title">Item Description - Row ' + rowNum + '</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                '<textarea id="modalDescription' + rowNum + '" class="form-control" rows="10">' + escapeHtml(textareaContent) + '</textarea>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-success" onclick="saveDescription(' + rowNum + ')">Save</button>' +
                '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            
            $('body').append(modalHtml);
            
            // Handle modal hidden event to reset flag
            $('#descriptionModal').on('hidden.bs.modal', function () {
                isModalOpen = false;
                // Clean up CKEditor when modal is hidden
                if (CKEDITOR.instances['modalDescription' + rowNum]) {
                    CKEDITOR.instances['modalDescription' + rowNum].destroy(true);
                }
                $(this).remove();
            });
            
            // Initialize CKEditor after modal is shown
            $('#descriptionModal').on('shown.bs.modal', function () {
                setTimeout(function() {
                    if (!CKEDITOR.instances['modalDescription' + rowNum]) {
                        CKEDITOR.replace('modalDescription' + rowNum, {
                            height: '300px'
                        });
                    }
                }, 100);
            });
            
            $('#descriptionModal').modal('show');
        }

        function saveDescription(rowNum) {
            if (CKEDITOR.instances['modalDescription' + rowNum]) {
                var editorContent = CKEDITOR.instances['modalDescription' + rowNum].getData();
                $('#description' + rowNum).val(editorContent);
            }
            
            $('#descriptionModal').modal('hide');
            isModalOpen = false;
        }

        function escapeHtml(text) {
            if (!text) return '';
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>

    <script>
        CKEDITOR.replace('invoice_memo');
        CKEDITOR.replace('invoice_terms_and_conditions');
        CKEDITOR.replace('invoice_payment_terms');
        CKEDITOR.replace('invoice_process_schedule');
        CKEDITOR.replace('invoice_taxes');
        CKEDITOR.replace('invoice_exclusions');

        $('#add_name').on('submit', function () {
            for (var instanceName in CKEDITOR.instances) {
                if (CKEDITOR.instances.hasOwnProperty(instanceName)) {
                    CKEDITOR.instances[instanceName].updateElement();
                }
            }
        });
    </script>

</body>
