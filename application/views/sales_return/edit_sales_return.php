<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."views/admin/modal.php");

?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Edit Sales Return
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SalesReturnController/show_sales_return/' ?>">Sales Return</a></li>
                    <li class="active">Sales Return</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Sales Return</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>SalesReturnController/edit_sales_return" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">

                                                <input type="hidden" class="form-control input-sm"   name="gst_check" value="<?php echo $gst_check; ?>" id="gst_check">
                                                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo $sales_return_data_group['number']; ?>">
                                                <input type="hidden" class="form-control input-sm" name="po_stock_check" value="po_stock_check" id="po_stock_check">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2> PR:<b> <?php echo $sales_return_data_group['number']; ?></b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name" name="customer_id" id="customer_id" required>
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $company_name = $sales_return_data_group['company_name'];

                                                        foreach ($result as $row) {
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
                                                <label for="inputEmail3" class="col-sm-4 control-label">Return Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control   input-sm holedate" autocomplete="off" name="date" id="date" required="" value="<?php echo $sales_return_data_group['po_date']; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm payment-due-date-check holedate" autocomplete="off" name="delivery_date" id="delivery_date" required="" value="<?php echo $sales_return_data_group['delivery_date']; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                        </div>
                                        
                                         <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-6">

                                                    <select class="form-control input-sm "  name="status" id="status">

                                                        <?php if ($sales_return_data_group['status'] == 1) { ?>
                                                            <option value="1" selected="">Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($sales_return_data_group['status'] == 2) { ?>
                                                            <option value="1" >Draft</option>
                                                            <option value="2" selected="">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($sales_return_data_group['status'] == 3) { ?>
                                                            <option value="1" >Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3" selected="">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($sales_return_data_group['status'] == 4) { ?>
                                                            <option value="1">Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4" selected="">Approved</option> 
                                                            <option value="5">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($sales_return_data_group['status'] == 5) { ?>
                                                            <option value="1" selected="">Draft</option>
                                                            <option value="2">Sent</option>
                                                            <option value="3">Viewed</option>
                                                            <option value="4">Approved</option> 
                                                            <option value="5" selected="">Rejected</option>
                                                            <option value="6">Canceled</option> 
                                                        <?php } ?>
                                                        <?php if ($sales_return_data_group['status'] == 6) { ?>
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
                                            <div class="form-group row ">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Ref. No.</label>
                                                <div class="col-sm-6">
                                                    <input type="text" value="<?php echo $sales_return_data_group['ref_no']; ?>" class="form-control" name="ref_no" id="ref_no">
                                                </div>
                                            </div> 
                                        </div>
                                       
                                        <div class="col-md-6 hide">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo $sales_return_data_group['subheading']; ?>" class="form-control" name="subheading" id="subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="footer" id="footer" rows="3"><?php echo $sales_return_data_group['footer']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="memo" id="memo" rows="3"><?php echo $sales_return_data_group['memo']; ?></textarea>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                       <div class="table-responsive"> 



                                       <table class="table table-bordered" id="dynamic_field">  
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
                                                <th>Price/Unit</th>
                                                <th>Discount(%)</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>

                                            <?php
                                            $i = 1;
                                            foreach ($show_sales_return as $key) {
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
                                                   
                                                        <input type="hidden" class="form-control input-sm" name="sr_return_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->sr_return_id; ?>">
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
                                                    <td><input type="text" maxlength="5" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list discount_auto number-only-validation" value="" /></td>
                                                    <td><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>
                                                        <input type="hidden" name="amount_temp[]" id="amount_temp<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->price * $key->quantity; ?>"/>
                                                        <input type="hidden" name="gst_amount[]" id="gst_amount<?php echo $i; ?>" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                        <span id="span_amount<?php echo $i; ?>" name="span_amount[]">₹<?php echo $key->amount; ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($i == 1) { ?>


                                                            <?php if ($key->gst_type != 'S') { ?>
                                                                <input type="hidden" name="quotation_igst_check" value="igst_edit_hide_show" id="quotation_igst_check">

                                                                <input type="hidden" name="igst_edit_hide_show" value="igst_edit_hide_show" id="igst_edit_hide_show">
                                                                <button type="button" name="edit_igst" id="edit_igst" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 

                                                            <?php } else if ($key->gst_type != 'I') { ?>
                                                                <input type="hidden" name="gst" value="gst" id="gst">
                                                                <input type="hidden" name="edit_sgst_cgst_check" value="edit_sgst_cgst_check" id="edit_sgst_cgst_check">
                                                                <input type="hidden" name="gst_discount_check" value="gst" id="gst_discount_check">
                                                                <button type="button" name="edit_gst" id="edit_gst" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 

                                                            <?php } else if (($key->gst_type != 'S') || ($key->gst_type != 'I')) { ?>
                                                                <input type="hidden" name="non_gst" value="non_gst" id="non_gst">
                                                                <button type="button" name="edit_non_gst" id="edit_non_gst" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 
                                                            <?php } ?>


                                                        <?php } else { ?>
                                                            <button type="button" name="remove" title="edit_sr_return" id="remove<?php echo $i; ?>" class="btn btn-danger btn-xs btn_remove">X</button>  
                                                        <?php } ?>
                                                    </td> 
                                                </tr>  
                                                <?php
                                                $i++;
                                            }
                                            ?>

                                        </table> 


                                        <div align="center">
                                            <button type="submit" name="submit" id="submit"  class="btn btn-success">Save</button>
                                        </div>
                                        <div align="right" style="margin: 10px">
                                            <span id="total_amount" name="total_amount[]" class="total_span_auto_amount">Total: ₹0.00</span><br>
                                            <span class="gst" id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span class="gst" id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                            <span class="igst_hide" id="igst_amount" name="igst_amount[]">IGST Amount: ₹0.00</span><br>
                                            <span id="grand_total_amount"><b>Grand Total:</b> ₹0.00</span><br>
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
    
  
    
    
    
   