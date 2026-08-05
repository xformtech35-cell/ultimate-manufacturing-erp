<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."views/admin/modal.php");
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);

?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Edit Purchase Order
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SupplierController/show_po/' ?>">Purchase Order</a></li>
                    <li class="active">Purchase Order Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Purchase Order</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>SupplierController/edit_purchase_order" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <?php $current_gst_type = !empty($show_po) ? $show_po[0]->gst_type : 'S'; ?>
                                                <input type="hidden" class="form-control input-sm" name="gst_check" value="<?php echo $current_gst_type === 'I' ? '' : 'gst_check'; ?>" id="gst_check">
                                                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo $po_data_group['number']; ?>">
                                                <input type="hidden" class="form-control input-sm" name="po_stock_check" value="po_stock_check" id="po_stock_check">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>IGST Purchase Order:<b> <?php echo $po_data_group['number']; ?></b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name" name="supplier_id" id="supplier_id" required id="">
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $company_name = $po_data_group['company_name'];

                                                        foreach ($result as $row) {
                                                            ?>
                                                            <option value="<?php echo $row->supplier_id ?>"  
                                                            <?php
                                                            if ($company_name == $row->company_name) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?> ><?php echo $row->company_name . " - " . $row->s_code; ?></option>

                                                        <?php }
                                                        ?>
                                                    </select>
                                                    <span class="btn btn-success btn-sm add-vendor-btn" data-toggle="modal" data-target="#addVendorModal" style="margin-top: 6px;"><i class="glyphicon glyphicon-plus"></i> Add Vendor</span>
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

                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Order Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate  input-sm created-date" name="purchase_date" id="date" required="" value="<?php echo date('d-m-Y', strtotime($po_data_group['purchase_date'])); ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm payment-due-date-check currentDateWithSevendays" name="delivery_date" id="delivery_date" required="" value="<?php echo $po_data_group['delivery_date']; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">P.O./S.O.</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" readonly=""  placeholder="P.O./S.O." name="po" id="po"value="<?php echo $po_data_group['number']; ?>" >
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">PO Upload</label>
                                                <div class="col-sm-8">
                                                    <input type="file" class="form-control input-sm" name="po_upload" id="po_upload" value="<?php echo $po_data_group['po_upload']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-6">


                                                    <select class="form-control input-sm "  name="status" id="status">

                                                        <?php 
                                                        $po_data_group['status'] = 1;
                                                        
                                                        if ($po_data_group['status'] == 1) { ?>
                                                            <option value="1" selected="">Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($po_data_group['status'] == 2) { ?>
                                                            <option value="1" >Draft</option>
                                                            <option value="2" selected="">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($po_data_group['status'] == 3) { ?>
                                                            <option value="1" >Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3" selected="">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($po_data_group['status'] == 4) { ?>
                                                            <option value="1">Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4" selected="">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($po_data_group['status'] == 5) { ?>
                                                            <option value="1" selected="">Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5" selected="">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($po_data_group['status'] == 6) { ?>
                                                            <option value="1">Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6" selected="">Canceled</option> 
                                                        <?php } ?>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 hide">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo $po_data_group['subheading']; ?>" class="form-control" name="subheading" id="subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="footer" id="footer" rows="3"><?php echo $po_data_group['footer']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="memo" id="memo" rows="3"><?php echo $po_data_group['memo']; ?></textarea>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                                        <?php if ($_has_project_master): ?>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Project Code</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm select2" name="project_code" id="project_code">
                                                        <option value="">Select Project Code</option>
                                                        <?php foreach ($projects as $proj) { ?>
                                                            <option value="<?php echo htmlspecialchars($proj['project_code']); ?>" <?= (isset($po_data_group['project_code']) && $po_data_group['project_code'] == $proj['project_code']) ? 'selected' : '' ?>><?php echo htmlspecialchars($proj['project_code']); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Sales Order No</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm select2" name="so_no" id="so_no">
                                                        <option value="">Select Sales Order</option>
                                                        <?php foreach ($sales_orders as $so) { ?>
                                                            <option value="<?php echo htmlspecialchars($so['number_fk']); ?>" data-oc="<?php echo htmlspecialchars($so['oc_number']); ?>" data-project="<?php echo htmlspecialchars($so['project_code']); ?>" <?= (isset($po_data_group['so_no']) && $po_data_group['so_no'] == $so['number_fk']) ? 'selected' : '' ?>><?php echo htmlspecialchars($so['number_fk']); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">OC Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="oc_no" id="oc_no" readonly placeholder="Auto-populated" value="<?php echo isset($po_data_group['oc_no']) ? htmlspecialchars($po_data_group['oc_no']) : ''; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                       <div class="table-responsive"> 
                                        

                                       <table class="table table-bordered" id="dynamic_field">  
                                            <tr>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>HSN/SAC</th>

                                                <th>QTY</th>
                                                <th>UNIT</th>
                                                <th class="gst_per">TAX(%)</th>
                                                <th class="gst">SGST</th>
                                                <th class="gst">CGST</th>
                                                <th class="igst_edit_hide_show">IGST</th>
                                                <th>Price</th>
                                                <th>Discount(%)</th>
                                                <th>Amount</th>
                                                <th>Action
                                                <!-- <button type="button" name="edit_gst" id="edit_gst" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></th>                                                </th> -->
                                            </tr>

                                            <?php
                                            $i = 1;
                                            foreach ($show_po as $key) {
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
                                                   
                                                        <input type="hidden" class="form-control input-sm" name="po_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->po_id; ?>">
                                                    </td> 
                                                    <td>
                                                    <button type="button" class="btn btn-info" onClick="descButton(this.id)" id="btnDescriptionId<?php echo $i; ?>">Description</button>

                                                        <textarea style="width: 150px"  class="form-control input-sm name_list description_auto hide" name="description[]" id="description<?php echo $i; ?>" rows="7"><?php echo $key->description; ?></textarea>
                                                    </td> 
                                                                                                        <td><input type="text" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" required="" class="form-control input-sm name_list" readonly="" /></td> 

                                                    <td class="hide"> <span id="total_quantity<?php echo $i; ?>" name="total_quantity[]"></span></td>
                                                    <td><input type="text" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="1" /></td> 
                                                    <td>
                                                <select style="width: 100px" class="form-control input-sm  item_search_unit"  name="unit[]" id="unit1"  required="" data-live-search="true">
                                                <option value="<?php echo $key->unit ?>" ><?php echo $key->unit; ?></option>
                                                    </select>
                                                </td> 
                                                    <td class="gst_per"><input type="text" readonly="" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" /></td> 
                                                    <td class="gst"><input type="text" readonly="" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" /></td> 
                                                    <td class="gst"><input type="text" readonly="" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" /></td> 
                                                    <td class="igst_edit_hide_show"><input type="text" readonly="" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" /></td> 
                                                    <td><input type="text" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" required="" class="form-control input-sm price_auto"  /></td>
                                                    <td><input type="text" maxlength="5" name="discount[]" value="<?php echo isset($key->discount) && $key->discount !== '' ? $key->discount : 0; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list discount_auto number-only-validation"/></td>
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
                                                                <button type="button" name="edit_gst" id="edit_gst" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 

                                                            <?php } else if ($key->gst_type != 'I') { ?>
                                                                <input type="hidden" name="gst" value="gst" id="gst">
                                                                <input type="hidden" name="edit_sgst_cgst_check" value="edit_sgst_cgst_check" id="edit_sgst_cgst_check">
                                                                <input type="hidden" name="gst_discount_check" value="gst" id="gst_discount_check">
                                                                <button type="button" name="edit_gst" id="edit_gst" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 

                                                            <?php } else if (($key->gst_type != 'S') || ($key->gst_type != 'I')) { ?>
                                                                <input type="hidden" name="non_gst" value="non_gst" id="non_gst">
                                                                <button type="button" name="edit_non_gst" id="edit_non_gst" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 
                                                            <?php } ?>


                                                        <?php } else { ?>
                                                            <button type="button" name="remove" title="edit_po" id="remove<?php echo $i; ?>" class="btn btn-xs btn-danger btn_remove">X</button>  
                                                        <?php } ?>
                                                    </td> 
                                                    
                                                </tr>  
                                                <?php
                                                $i++;
                                            }
                                            ?>

                                        </table>  
                                        <div class="col-md-12">
                                            <div align="center" style="margin-top: 20px;">
                                                <button type="submit" name="submit" id="submit" class="btn btn-success">Save</button>
                                            </div>
                                        </div>
                                        <div align="right" style="margin: 10px">
                                            <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span><br>
                                            <span id="total_amount" name="total_amount[]" class="total_span_auto_amount">Total: ₹0.00</span><br>
                                            <span class="gst" id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span class="gst" id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                            <span class="igst_hide" id="igst_amount" name="igst_amount[]">IGST Amount: ₹0.00</span><br>
                                            <span id="grand_total_amount"><b>Grand Total:</b> ₹0.00</span><br>
                                            <b> Grand Total in Words:<span id="word2" name="word2"></span>Only.</b><br>
                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
                                        </div>
                                    </div>  
                                  
                                    
                                    
                                     <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Terms & Conditions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_terms_and_conditions" id="po_terms_and_conditions" rows="3"><?php echo $po_data_group['po_terms_and_conditions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Payment Terms</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_payment_terms" id="po_payment_terms" rows="3"><?php echo $po_data_group['po_payment_terms']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Process Schedule</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_process_schedule" id="po_process_schedule" rows="3"><?php echo $po_data_group['po_process_schedule']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>  

                                        <div class="col-xs-6">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Taxes</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_taxes" id="po_taxes" rows="3"><?php echo $po_data_group['po_taxes']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Exclusions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_exclusions" id="po_exclusions" rows="3"><?php echo $po_data_group['po_exclusions']; ?></textarea>
                                                </div>
                                            </div>  
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Note</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_note" id="po_note" rows="3"><?php echo $po_data_group['po_note']; ?></textarea>
                                                </div>
                                            </div>    
                                        </div>
                                    </div>

                                    <div class="row">
                                        
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
    

    <!-- ./Supplier modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header bg-primary">

                    <center>
                        <h4 class="modal-title">Add Company
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </h4>
                    </center>

                </div>
                <div class="modal-body">

                    <div class="card-body ">

                        <!-- form start -->
                        <div class="form-group row required">
                            <label for="inputEmail3" class="col-sm-4 control-label">Company Name</label>
                            <div class="col-sm-7">
                                <input type="hidden" name="gst_check_customer" value="po_supplier_check" id="gst_check_customer">
                                <input type="text" class="form-control" name="company_name" id="company_name" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label">Contact Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control " name="fullname" id="fullname">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="pancard" id="pancard" style="text-transform: uppercase;">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="15" class="form-control " name="gst" id="gst" style="text-transform: uppercase;">
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
                                                this.value = this.value.replace(/\D/g, '')" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control " name="state_code" id="state_code">
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
                    <button type="button" id="btnSave" class="btn btn-success performa_submit">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
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
            $(document).on('input change', 'input[name="quantity[]"]', function() { updateTotalQty(); });
            setInterval(updateTotalQty, 500);

            var igstEditMode = $('#igst_edit_hide_show').val();
            if (igstEditMode === 'igst_edit_hide_show') {
                $('.gst').hide();
                $('.igst_edit_hide_show').show();
                $('.igst_hide').show();
            } else {
                $('.gst').show();
                $('.igst_edit_hide_show').hide();
                $('.igst_hide').hide();
            }

            CKEDITOR.replace('po_note');
            CKEDITOR.replace('po_terms_and_conditions');
            CKEDITOR.replace('po_payment_terms');
            CKEDITOR.replace('po_process_schedule');
            CKEDITOR.replace('po_taxes');
            CKEDITOR.replace('po_exclusions');
            
            $('form').on('submit', function() {
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }
            $('#so_no').change(function() {
                var selected = $(this).find('option:selected');
                var oc = selected.data('oc');
                var project = selected.data('project');
                
                $('#oc_no').val(oc ? oc : '');
                if (project) {
                    $('#project_code').val(project).trigger('change');
                }
            });
        });
    </script>

   
