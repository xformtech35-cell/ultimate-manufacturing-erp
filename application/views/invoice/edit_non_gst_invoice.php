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
                    Edit Non GST Invoice
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Non GST Invoice</a></li>
                    <li class="active">Non GST Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">

                                <h3 class="box-title">Edit Non GST Invoice</h3>
                                     <!--<button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Customer</button>-->
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

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>InvoiceController/edit_ng_invoice" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                
                                                <input type="hidden" name="edit_invoice_stock_check" value="edit_invoice_stock_check" id="edit_invoice_stock_check">
                                                
                                                <input type="hidden" class="form-control input-sm" name="invoice_number" id="invoice_number" required="" value="<?php echo $ng_invoice_data_group['invoice_number']; ?>">
                                                <label for="inputEmail3" name="invoice_number" id="invoice_number" class="col-sm-12 control-label"><h2> Invoice:<b> <?php echo $ng_invoice_data_group['invoice_number']; ?></b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="col-md-12 company_search_name" name="customer_id" id="customer_id" required id="">
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $company_name = $ng_invoice_data_group['company_name'];

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

                                                </div>

                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm "  name="status" id="status">
                                                       <?php
                                                            if ($status_result[0]->status == 1) { ?>
                                                        <option value="1" selected="">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 2) { ?>
                                                        <option value="1" >Draft</option>
                                                        <option value="2" selected="">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 3) { ?>
                                                        <option value="1" >Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3" selected="">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 4) { ?>
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4" selected="">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 5) { ?>
                                                        <option value="1" selected="">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5" selected="">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 6) { ?>
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
                                                <label for="inputEmail3" class="col-sm-4 control-label">Invoice Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm created-date" name="date" id="date" required="" value="<?php echo $ng_invoice_data_group['date']; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check" name="payment_due_date" id="payment_due_date" required="" value="<?php echo $ng_invoice_data_group['payment_due_date']; ?>" onkeydown="return false;">
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Note</label>
                                                <div class="col-sm-7">
                                                    <textarea  class="form-control" name="note" id="note" rows="2"><?php echo $ng_invoice_data_group['note']; ?></textarea>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="col-md-6">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo $ng_invoice_data_group['invoice_subheading']; ?>" class="form-control" name="invoice_subheading" id="invoice_subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="invoice_footer" id="invoice_footer" rows="3"><?php echo $ng_invoice_data_group['invoice_footer']; ?></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Shipping To</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="shipping_address" id="shipping_address" rows="3"><?php echo $ng_invoice_data_group['shipping_address']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="invoice_memo" id="invoice_memo" rows="3"><?php echo $ng_invoice_data_group['invoice_memo']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">  

                                        <table class="table table-bordered" id="dynamic_field">  
                                            <tr>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>Qty(Nos/Kg)</th>
                                                <th>HSN Code</th>
                                                <th>Price</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>

                                            <?php
                                            $i = 1;
                                            foreach ($show_ng_invoice as $key) {
                                                ?>
                                                <tr> 

                                                    <td>
                                                        <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list"  name="term[]" id="item_name<?php echo $i; ?>" onchange="myFunction(this.id)" required="" data-live-search="true">
                                                            <option></option>

                                                            <?php
                                                            foreach ($product_code_list as $row) {
                                                                ?>
                                                                <option value="<?php echo $row->barcode ?>"  
                                                                <?php
                                                                if ($key->product_name == $row->barcode) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?> ><?php echo $row->barcode; ?></option>
                                                                    <?php }
                                                                    ?>

                                                        </select>

                                                    <!--<input type="text" name="term[]" value="<?php //echo $key->product_name;  ?>" id="item_name<?php //echo $i;  ?>" required="" class="form-control input-sm name_list product_name_auto" />-->
                                                    <input type="hidden" class="form-control input-sm"   name="invoice_id[]" id="invoice_id<?php echo $i; ?>"  value="<?php echo $key->invoice_id; ?>"></td>
                                                    <td><input type="text" name="description[]" id="description1<?php echo $i; ?>" value="<?php echo $key->description; ?>" class="form-control input-sm name_list description_auto" /></td> 
                                                    <td class="hide"> <span id="total_quantity<?php echo $i; ?>" name="total_quantity[]"></span></td>
                                                    <td><input type="number" min="1" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="1" /></td> 
                                                    <td><input type="text" readonly="" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" required="" class="form-control input-sm name_list" /></td> 
                                                    <td class="hide"><input type="text" readonly="" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" /></td> 

                                                    <td><input type="text" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" required="" class="form-control input-sm price_auto"/></td>
                                                    <td><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>
                                                        <input type="hidden" name="gst_amount[]" id="gst_amount<?php echo $i; ?>" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                        <span id="span_amount<?php echo $i; ?>" name="span_amount[]">₹<?php echo $key->amount; ?></span>
                                                    </td>
                                                    <td>
    <?php if ($i == 1) { ?>
                                                            <input type="hidden" name="gst" value="gst" id="gst">
                                                            <button type="button" accesskey="n" name="edit_non_gst_invoive" id="edit_non_gst_invoive" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 
    <?php } else { ?>
                                                            <button type="button" name="remove" id="remove<?php echo $i; ?>" class="btn btn-danger btn_remove_invoice_ng">X</button>  
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

        <div class="control-sidebar-bg"></div>
<?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
    <script>

        $(document).ready(function () {

            var igst_hide = $("#igst_hide").val();
            //alert(igst_hide);
            if (igst_hide == "igst_hide") {
                $(".gst").hide();
                $(".igst_hide").show();
            } else {
                $(".gst").show();
                $(".igst").hide();
            }

        });
    </script>