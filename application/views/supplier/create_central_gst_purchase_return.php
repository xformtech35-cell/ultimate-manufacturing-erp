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
                    Debit Note
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SupplierController/view_purchase_return/' ?>">Debit Note</a></li>
                    <li class="active">Debit Note</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                
                   <div class="row" style="padding:2%">
                    <div class="pull-left">

<!--                        <div class="col-md-2">
                            <a href="<?php //echo base_url(); ?>InvoiceController/create_non_gst_invoice"  class="btn btn-primary" role="button">Non GST</a>
                        </div>-->
                        <div class="pull-right">
                            <div class="col-md-6">
                                <a href="<?php echo base_url(); ?>SupplierController/create_purchase_return" id="central_gst" name="central_gst" class="btn btn-primary" role="button">GST</a>
                            </div>
                            
                        </div>
                    </div>
                                                    <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                </div>
                
                
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Debit Note</h3>

                                     <!--<button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Customer</button>-->
                            </div>
                            <!-- /.box-header -->
                             <input type="hidden" class="form-control input-sm" name="gst_check" value="central_gst_check" id="gst_check">
                            <input type="hidden" name="quotation_igst_check" value="igst" id="quotation_igst_check">
                            <div class="box-body">

                             


                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>SupplierController/add_purchase_return" enctype="multipart/form-data">
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
                                                <input type="hidden" class="form-control input-sm"   name="gst_check" value="central_gst_check" id="gst_check">
                                                <input type="hidden" class="form-control input-sm" name="po_stock_check" value="po_stock_check" id="po_stock_check">
                                                <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="DN/<?php printf("%04d", $purcahse_return_id+ 1); ?>/<?php echo $financial_year; ?>">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>IGST Debit Note:<b> DN/<?php printf("%04d", $purcahse_return_id+ 1); ?>/<?php echo $financial_year; ?> </b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name"  name="supplier_id" id="supplier_id" required="">
                                                        <option value="">Select Company</option>
                                                        <?php foreach ($result as $key) { ?>    
                                                            <option value="<?php echo $key->supplier_id; ?>"><?php echo $key->company_name . " - " . $key->s_code; ?> - ( <?php echo $key->state_code; ?> )</option> 
                                                        <?php } ?>  
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
                                                <label for="inputEmail3" class="col-sm-4 control-label">Return Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="date" id="date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check currentDateWithSevendays" name="delivery_date" id="delivery_date" required="" onkeydown="return false;">
                                                </div>
                                            </div>
                                        </div>
                                        
                                         <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-6">
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
                                             <div class="form-group row ">
                                                <label for="inputEmail3" id="" class="col-sm-4 control-label">Reference No.</label>
                                                <div class="col-sm-6">
                                                    <input type="text"  class="form-control" name="ref_no" id="ref_no">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 hide">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo $settings['po_subheading']; ?>" class="form-control" name="subheading" id="subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="footer" id="footer" rows="3"><?php echo $settings['po_footer']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="memo" id="memo" rows="3"><?php echo $settings['po_memo']; ?></textarea>
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
                                            <th>IGST</th>
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

                                                    <textarea style="width: 150px"  class="form-control input-sm name_list description_auto hide" name="description[]" id="description1" rows="7">  </textarea>
                                                <td class="hide"> <span id="total_quantity1" name="total_quantity[]"></span></td>
                                                <td><input type="text" name="hsn[]" id="hsn1" required="" readonly class="form-control input-sm required_list name_list" /></td> 
                                                <td><input type="text"  name="quantity[]" id="quantity1" required="" class="form-control input-sm required_list name_list quantity_auto number-only-validation" value="1" /></td> 
                                                <td>
                                                <select style="width: 100px" class="form-control input-sm  item_search_unit"  name="unit[]" id="unit1"  required="" data-live-search="true">
                                                        <option></option> 
                                                    </select>
                                                </td> 
                                                <td><input type="text" readonly="" name="gst_per[]" id="gst_per1" class="form-control input-sm name_list" /></td> 
                                                <td class="hide"><input type="hidden" readonly="" name="sgst[]" id="sgst1" class="form-control input-sm sgst_list" /></td> 
                                                <td class="hide"><input type="hidden" readonly="" name="cgst[]" id="cgst1" class="form-control input-sm cgst_list" /></td> 
                                                <td><input type="text" name="igst[]" readonly="" id="igst1" class="form-control input-sm igst_list" /></td> 
                                                <td><input type="text" name="price[]" id="price1" required="" class="form-control input-sm required_list name_list price_auto" value="0.00" /></td>
                                                <td><input type="text" maxlength="5" name="discount[]" id="discount1" class="form-control input-sm name_list discount_auto number-only-validation" value="0" /></td>
                                                <td><input type="hidden" name="amount[]" id="amount1" class="form-control input-sm name_list amount_auto" value="0.00" />
                                                    <input type="hidden" name="amount_temp[]" id="amount_temp1" value="0.00" />
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
                                            <input type="hidden" name="total_item_qty" id="total_item_qty" value="0" />
                                            <span id="total_amount" name="total_amount[]">Total: ₹0.00</span><br>
<!--                                            <span id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>-->
                                            <span id="igst_amount" class="hide_igst" name="igst_amount[]">Total IGST Amount: ₹0.00</span><br>
                                            <!--<span id="total_gst_amount" name="total_gst_amount[]">Total GST Amount: ₹0.00</span><br>-->
                                            <b> <span id="grand_total_amount2" name="grand_total_amount2[]"><b>Grand Total:</b> ₹0.00</span></b><br>

                                            <b> Grand Total in Words:<span id="word2" name="word2"></span>Only.</b><br>
                                            <input type="hidden" value="create_igst_total_check" name="create_igst_total_check" id="create_igst_total_check"/>
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
                   <center> <h4 class="modal-title">Add Vendor<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
<!--                <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>SupplierController/add_supplier" enctype="multipart/form-data">-->
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Company  Name<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" name="gst_check_customer" value="t" id="gst_check_customer">
                                    <input type="hidden" class="form-control input-sm"  name="redirect_supplier" value="po_supplier" id="redirect_supplier">
                                    <input type="text" class="form-control input-sm"  name="company_name" id="company_name" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="fullname" id="fullname" onkeydown="return validate_name(event)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> PAN No.</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="10" class="form-control input-sm pancard-valid" name="pancard" style="text-transform: uppercase;" id="pancard">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> GST No.</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control input-sm gst-number-check" style="text-transform: uppercase;" name="gst" id="gst">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Email</label>
                                <div class="col-sm-7">
                                    <input type="email" class="form-control input-sm" name="email" id="email"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Mobile</label>
                                <div class="col-sm-7">                                            
                                    <input type="tel" class="form-control input-sm" name="mobile" id="mobile" maxlength="10"   
                                           onkeyup="if (/\D/g.test(this.value))
                                                       this.value = this.value.replace(/\D/g, '')"/>                                             
                                </div>
                            </div>
                            
                             <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control input-sm" name="state_code" id="state_code">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Address</label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="address" id="address"></textarea>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success performa_submit">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
<!--                </form>-->
            </div>
        </div>
    </div>

    <script>
        function getPurchaseReturnRowIndex(element) {
            var match = ($(element).attr('id') || '').match(/\d+$/);
            return match ? match[0] : '';
        }

        function parsePurchaseReturnNumber(value) {
            return parseFloat(String(value || '').replace(/,/g, '')) || 0;
        }

        function purchaseReturnNumberToWords(num) {
            if (num === 0) {
                return 'Zero ';
            }

            var ones = ['', 'One ', 'Two ', 'Three ', 'Four ', 'Five ', 'Six ', 'Seven ', 'Eight ', 'Nine ', 'Ten ', 'Eleven ', 'Twelve ', 'Thirteen ', 'Fourteen ', 'Fifteen ', 'Sixteen ', 'Seventeen ', 'Eighteen ', 'Nineteen '];
            var tens = ['', '', 'Twenty ', 'Thirty ', 'Forty ', 'Fifty ', 'Sixty ', 'Seventy ', 'Eighty ', 'Ninety '];

            function words(n) {
                if (n < 20) return ones[n];
                if (n < 100) return tens[Math.floor(n / 10)] + ones[n % 10];
                if (n < 1000) return ones[Math.floor(n / 100)] + 'Hundred ' + words(n % 100);
                if (n < 100000) return words(Math.floor(n / 1000)) + 'Thousand ' + words(n % 1000);
                if (n < 10000000) return words(Math.floor(n / 100000)) + 'Lakh ' + words(n % 100000);
                return words(Math.floor(n / 10000000)) + 'Crore ' + words(n % 10000000);
            }

            return words(Math.floor(num));
        }

        function updatePurchaseReturnTotals() {
            var totalAmount = 0;
            var totalIgst = 0;

            $('.amount_auto').each(function() {
                totalAmount += parsePurchaseReturnNumber($(this).val());
            });

            $('.igst_list').each(function() {
                totalIgst += parsePurchaseReturnNumber($(this).val());
            });

            var grandTotal = totalAmount + totalIgst;
            $('#total_amount').text('Total: ₹' + totalAmount.toFixed(2));
            $('#igst_amount').text('Total IGST Amount: ₹' + totalIgst.toFixed(2));
            $('#grand_total_amount2').text('Grand Total: ₹' + grandTotal.toFixed(2));
            $('#total_quotation_amount').val(grandTotal.toFixed(2));
            $('#word2').text(purchaseReturnNumberToWords(grandTotal));
        }

        function calculatePurchaseReturnRow(index) {
            if (!index) {
                return;
            }

            var quantity = parsePurchaseReturnNumber($('#quantity' + index).val());
            var price = parsePurchaseReturnNumber($('#price' + index).val());
            var discount = parsePurchaseReturnNumber($('#discount' + index).val());
            var gstPercent = parsePurchaseReturnNumber($('#gst_per' + index).val());

            if (discount > 100) {
                discount = 0;
                $('#discount' + index).val('0');
                alert('Oops!! Discount is not more than 100%');
            }

            var baseAmount = quantity * price;
            var amountAfterDiscount = baseAmount - ((baseAmount * discount) / 100);
            var igstAmount = (amountAfterDiscount * gstPercent) / 100;

            $('#amount' + index).val(amountAfterDiscount.toFixed(2));
            $('#amount_temp' + index).val(amountAfterDiscount.toFixed(2));
            $('#gst_amount' + index).val(igstAmount.toFixed(2));
            $('#igst' + index).val(igstAmount.toFixed(2));
            $('#sgst' + index).val('0.00');
            $('#cgst' + index).val('0.00');
            $('#span_amount' + index).text('₹' + amountAfterDiscount.toFixed(2));
        }

        function calculatePurchaseReturnAll() {
            $('.quantity_auto').each(function() {
                calculatePurchaseReturnRow(getPurchaseReturnRowIndex(this));
            });
            updateTotalQty();
            updatePurchaseReturnTotals();
        }

        function updateTotalQty() {
            var totalQty = 0;
            $('input[name="quantity[]"]').each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });
            var formattedQty = Number.isInteger(totalQty) ? totalQty : totalQty.toFixed(2);
            $('#total_item_qty_display').text('Total Item Qty: ' + formattedQty);
            $('#total_item_qty').val(totalQty);
        }
        $(document).ready(function () {
            calculatePurchaseReturnAll();
            $(document).on('input change keyup', '.quantity_auto, .price_auto, .price_auto_purchase_return, .discount_auto, input[name="gst_per[]"]', function() {
                calculatePurchaseReturnRow(getPurchaseReturnRowIndex(this));
                updateTotalQty();
                updatePurchaseReturnTotals();
            });
            $(document).on('click', '#add_gst, .remove, .btn-danger', function () {
                setTimeout(calculatePurchaseReturnAll, 100);
            });
            
            $('#supplier_id').on('change', function() {


    //   alert("Company selection changed"); // Debugging alert
                var customerId = $(this).val();
        
            var selectedText = $(this).find('option:selected').text();
            var companyStateCode = (selectedText.match(/\(\s*(\d+)\s*\)/) || [])[1];

            // alert("Selected Company State Code: " + companyStateCode); // Debugging alert
            setupCompanySelectValidation(
                customerId,
                companyStateCode,
                <?php echo json_encode($settings['state_code']); ?>,
                 '<?php echo base_url(); ?>SupplierController/create_purchase_return',
                '<?php echo base_url(); ?>SupplierController/create_central_gst_purchase_return',
                'igst'
            );
        });
        });
    </script>
    
  
    
 
