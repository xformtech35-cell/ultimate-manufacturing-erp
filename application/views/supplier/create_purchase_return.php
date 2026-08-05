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
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SupplierController/view_purchase_return/' ?>">Debit Note</a></li>
                    <li class="active">Debit Note Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        <!--                        <div class="col-md-2">
                                                    <a href="<?php //echo base_url();  ?>InvoiceController/create_non_gst_invoice"  class="btn btn-primary" role="button">Non GST</a>
                                                </div>-->
                        <div class="pull-right">
                            <div class="col-md-6">
                                <a href="<?php echo base_url(); ?>SupplierController/create_central_gst_purchase_return" id="central_gst" name="central_gst" class="btn btn-primary" role="button">IGST</a>
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
                                                <input type="hidden" class="form-control input-sm"   name="gst_check" value="gst_check" id="gst_check">
                                                <input type="hidden" class="form-control input-sm" name="po_stock_check" value="po_stock_check" id="po_stock_check">
                                                <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="DN/<?php printf("%04d", $purcahse_return_id + 1); ?>/<?php echo $financial_year; ?>">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h3>GST Debit Note:<b> DN/<?php printf("%04d", $purcahse_return_id + 1); ?>/<?php echo $financial_year; ?> </b></h3></label>
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
                                                    <input type="text" class="form-control date input-sm payment-due-date-check currentDateWithSevendays" name="delivery_date" id="delivery_date" required="" onkeydown="return false;" autocomplete="off">
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
                                                <td><input type="text" name="hsn[]" id="hsn1" required="" readonly="" class="form-control input-sm required_list name_list"  /></td> 

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
                                            <span id="grand_total_amount"><b>Grand Total: </b>₹0.00</span><br>
                                            <span id="grand_total_words" style="font-weight: bold; color: #333;">Grand Total in Words: Zero Rupees Only</span><br>
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
        <!-- <?php $this->load->view('admin/footer'); ?> -->
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
                            <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="10" class="form-control input-sm pancard-valid" name="pancard" style="text-transform: uppercase;" id="pancard">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
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
                                <input type="text" class="form-control input-sm" name="state_code" id="state_code">
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
        function numberToWords(num) {
            const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

            if (num === 0) return 'Zero Rupees Only';

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

        function updateGrandTotalWords() {
            let grandTotal = parseFloat($('#total_quotation_amount').val()) || 0;
            let grandTotalAmount = Math.floor(grandTotal);
            let grandTotalWords = numberToWords(grandTotalAmount);
            $('#grand_total_words').text('Grand Total in Words: ' + grandTotalWords);
        }

        function updateTotalQty() {
            var totalQty = 0;
            $('input[name="quantity[]"]').each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });
            $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
        }
        $(document).ready(function () {
            updateTotalQty();
            updateGrandTotalWords();
            $(document).on('input change', 'input[name="quantity[]"], input[name="price[]"], input[name="discount[]"], input[name="gst_per[]"], input[name="sgst[]"], input[name="cgst[]"]', function() {
                updateTotalQty();
                setTimeout(updateGrandTotalWords, 100);
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
                'sgst'
            );
        });
            setInterval(function() {
                updateTotalQty();
                updateGrandTotalWords();
            }, 500);
            
          

            
        });
        // Auto-fill PAN and State Code from GST Number - Improved version
$(document).ready(function() {
    // Use event delegation on the modal container
    $('#myModal').on('blur change', '#gst', function() {
        var gstNo = $(this).val().trim().toUpperCase();
        console.log('GST blurred:', gstNo); // Debug log
        
        if (gstNo.length === 0) {
            $('#myModal #pancard').val('');
            $('#myModal #state_code').val('');
            return;
        }
        
        if (gstNo.length !== 15) {
            alert('GST No must be 15 characters long. Example: 27AAPFU0205R1Z0');
            $(this).val('');
            $('#myModal #pancard').val('');
            $('#myModal #state_code').val('');
            $(this).focus();
            return;
        }
        
        var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
        if (!gstRegex.test(gstNo)) {
            alert('Invalid GST format. Expected: 2 digits + PAN + 1 digit + 1 letter + 1 digit\nExample: 27AAPFU0205R1Z0');
            $(this).val('');
            $('#myModal #pancard').val('');
            $('#myModal #state_code').val('');
            $(this).focus();
            return;
        }
        
        // Extract PAN (characters 2-11)
        var panNo = gstNo.substring(2, 12);
        $('#myModal #pancard').val(panNo);
        
        // Extract State Code (first two digits)
        var stateCode = gstNo.substring(0, 2);
        $('#myModal #state_code').val(stateCode);
        
        console.log('PAN filled:', panNo, 'State Code filled:', stateCode);
    });
});

        
    </script>

 


