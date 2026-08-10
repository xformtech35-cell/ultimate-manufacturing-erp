<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "views/admin/modal.php");
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
                    Purchase Order
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Purchase Order</a></li>
                    <li class="active">Purchase Order Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        <div class="pull-right">
                            <div class="col-md-6">
                                <a href="<?php echo base_url(); ?>SupplierController/create_central_gst_purchase" id="central_gst" name="central_gst" class="btn btn-primary" role="button">IGST</a>
                            </div>

                        </div>
                    </div>
                     <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                </div>


                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Purchase Order</h3>

                                <!--<button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Customer</button>-->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                               


                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>SupplierController/add_po" enctype="multipart/form-data">
                                    <input type="hidden" name="pr_id" id="pr_id" value="<?php echo htmlspecialchars($pr_id ?? ''); ?>">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <?php
                                                if (date('m') <= 3) {
                                                    $financial_year = (date('y') - 1) . '-' . date('y');
                                                    $so_fy = (date('y') - 1) . date('y');
                                                } else {
                                                    $financial_year = date('y') . '-' . (date('y') + 1);
                                                    $so_fy = date('y') . (date('y') + 1);
                                                }
                                                $po_seq_formatted = sprintf("%04d", $po_id + 1);
                                                $so_ref = isset($so_no) ? $so_no : (isset($pr_info['so_no']) ? $pr_info['so_no'] : (isset($pr_no) ? $pr_no : ''));
                                                $so_suffix = "";
                                                if (!empty($so_ref)) {
                                                    if (preg_match('/(?:([0-9]{4})|([0-9]{2}-[0-9]{2})).*?([0-9]+)$/', trim($so_ref), $so_m)) {
                                                        $s_fy = !empty($so_m[1]) ? $so_m[1] : str_replace('-', '', $so_m[2]);
                                                        $so_suffix = "/(" . $s_fy . "/" . $so_m[3] . ")";
                                                    } elseif (preg_match('/([0-9]+)$/', trim($so_ref), $so_m)) {
                                                        $so_suffix = "/(" . $so_fy . "/" . $so_m[1] . ")";
                                                    }
                                                }
                                                $po_number_generated = 'PO/' . $financial_year . '/' . $po_seq_formatted . $so_suffix;
                                                ?>
                                                <input type="hidden" name="gst_discount_check" value="gst_discount_check" id="gst_discount_check">
                                                <input type="hidden" class="form-control input-sm" name="gst_check" value="gst_check" id="gst_check">
                                                <input type="hidden" class="form-control input-sm" name="po_stock_check" value="po_stock_check" id="po_stock_check">
                                                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo htmlspecialchars($po_number_generated); ?>">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                                                    <h2>GST Purchase Order: <b><?php echo htmlspecialchars($po_number_generated); ?></b></h2>
                                                </label>
                                                <?php if (!empty($pr_no)): ?>
                                                    <div class="col-sm-12">
                                                        <div class="alert alert-info alert-dismissible" style="padding: 10px 15px; margin-bottom: 10px; border-left: 4px solid #00c0ef;">
                                                            <i class="fa fa-info-circle"></i> Converted from Requisition: <strong><?php echo htmlspecialchars($pr_no); ?></strong>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name" name="supplier_id" id="supplier_id" required="">
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
                                                    <select class="form-control input-sm " name="payment_method" id="payment_method">
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
                                                    <input type="text" class="form-control alldate input-sm created-date" name="purchase_date" id="purchase_date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check currentDateWithSevendays" name="delivery_date" id="delivery_date" required="" onkeydown="return false;" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">P.O./S.O.</label>
                                                <div class="col-sm-8">

                                                <?php echo $po_id; ?>
                                                    <input type="text" class="form-control input-sm" readonly="" placeholder="P.O./S.O." name="po" id="po" value="<?php printf("%04d", $po_id + 1); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">PO Upload</label>
                                                <div class="col-sm-8">
                                                    <input type="file" class="form-control input-sm" name="po_upload" id="po_upload">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control input-sm " name="status" id="status">
                                                        <option value="2" selected>Pending</option>
                                                        <option value="1">Draft</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option>
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                    </select>
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
                                    <div class="row" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                                         <?php if ($_has_project_master): ?>
                                         <div class="col-md-4">
                                             <div class="form-group row">
                                                 <label class="col-sm-4 control-label">Project Code</label>
                                                 <div class="col-sm-8">
                                                     <select class="form-control input-sm select2" name="project_code" id="project_code">
                                                         <option value="">Select Project Code</option>
                                                         <?php foreach ($projects as $proj) { ?>
                                                             <option value="<?php echo htmlspecialchars($proj['project_code']); ?>"><?php echo htmlspecialchars($proj['project_code']); ?></option>
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
                                                        <?php foreach ($sales_orders as $so) { 
                                                            $selected = (!empty($so_ref) && (trim($so_ref) === trim($so['number_fk']) || strpos($so['number_fk'], $so_ref) !== false)) ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo htmlspecialchars($so['number_fk']); ?>" data-oc="<?php echo htmlspecialchars($so['oc_number']); ?>" data-project="<?php echo htmlspecialchars($so['project_code']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($so['number_fk']); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <script>
                                                        $(document).ready(function() {
                                                            if($('#so_no').val() !== '') {
                                                                $('#so_no').trigger('change');
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">OC Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="oc_no" id="oc_no" readonly placeholder="Auto-populated">
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
                                            <th>TAX(%)</th>
                                            <th>SGST</th>
                                            <th>CGST</th>
                                            <th>Price</th>
                                            <th>Discount(%)</th>
                                            <th>Amount</th>
                                            <th>Action
                                            </th>
                                            <tr>
                                                <td>
                                                    <select style="width: 150px" class="form-control input-xs product_name_auto item_search_name name_list" name="product_name[]" id="item_name1" onchange="myFunction1(this.id)" required="" data-live-search="true">
                                                        <option></option>
   <option value="NEW">+ Add New Product</option>
                                                        <?php foreach ($item_name as $key) { ?>
                                                            <option value="<?php echo $key->code; ?>"><?php echo $key->code . " - " . $key->item_name; ?></option>
                                                        <?php } ?>
                                                    </select>

                                                </td>
                                                <td>

                                                    <button type="button" class="btn btn-info" onClick="descButton(this.id)" id="btnDescriptionId1">Description</button>

                                                    <textarea style="width: 150px; " class="form-control input-sm name_list description_auto hide" name="description[]" id="description1" rows="4"></textarea>

                                                </td>
                                                <td><input type="text" name="hsn[]" id="hsn1" required="" readonly class="form-control input-sm required_list name_list" /></td>

                                                <td class="hide"> <span id="total_quantity1" name="total_quantity[]"></span></td>
                                                <td><input type="text" name="quantity[]" id="quantity1" required="" class="form-control input-sm required_list name_list quantity_auto number-only-validation" value="1" autocomplete="off" /></td>
                                                <td>
                                                    <select style="width: 100px" class="form-control input-sm  item_search_unit" name="unit[]" id="unit1" required="" data-live-search="true">
                                                        <option></option>
                                                    </select>
                                                </td>
                                                <td><input type="text" readonly="" name="gst_per[]" id="gst_per1" class="form-control input-sm name_list" /></td>
                                                <td><input type="text" readonly="" name="sgst[]" id="sgst1" class="form-control input-sm sgst_list" /></td>
                                                <td><input type="text" readonly="" name="cgst[]" id="cgst1" class="form-control input-sm cgst_list" /></td>
                                                <td class="hide"><input type="text" readonly="" name="igst[]" id="igst1" class="form-control input-sm igst_list" /></td>
                                                <td><input type="text" name="price[]" id="price1" required="" class="form-control input-sm required_list name_list price_auto" value="0.00" autocomplete="off" /></td>
                                                <td><input type="text" maxlength="5" name="discount[]" id="discount1" class="form-control input-sm name_list discount_auto number-only-validation" value="0" autocomplete="off" /></td>
                                                <td><input type="hidden" name="amount[]" id="amount1" class="form-control input-sm name_list amount_auto" value="0.00" />
                                                    <input type="hidden" name="amount_temp[]" id="amount_temp1" class="form-control input-sm name_list amount_auto" value="0.00" />
                                                    <input type="hidden" name="gst_amount[]" id="gst_amount1" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                    <span id="span_amount1" name="span_amount[]">₹0.00</span>
                                                </td>
                                                <td>                                                <button type="button" name="add_gst" id="add_gst" class="btn btn-success btn-xs action-header-btn"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                                    </td>
                                            </tr>
                                        </table>
                                        <div class="col-md-12">
                                            <div align="center" style="margin-top: 20px;">
                                                <button type="submit" name="submit" id="submit" class="btn btn-success">Save</button>
                                            </div>
                                        </div>

                                        <div align="right" style="margin: 10px">
                                            <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span><br>
                                            <span id="total_amount" name="total_amount[]">Total: ₹0.00</span><br>
                                            <span id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                            <span id="grand_total_amount2"><b>Grand Total: </b>₹0.00</span><br>
                                            <span id="word2" style="font-weight: bold; color: #333;">Grand Total in Words: Zero Rupees Only</span>
                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
                                            

                                        </div>
                                        

                                        
                                    </div>
                                    


                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Terms & Conditions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_terms_and_conditions" id="po_terms_and_conditions" rows="3"><?php echo $settings['po_terms_and_conditions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Payment Terms</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_payment_terms" id="po_payment_terms" rows="3"><?php echo $settings['po_payment_terms']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Process Schedule</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_process_schedule" id="po_process_schedule" rows="3"><?php echo $settings['po_process_schedule']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-6">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Taxes</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_taxes" id="po_taxes" rows="3"><?php echo $settings['po_taxes']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Exclusions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_exclusions" id="po_exclusions" rows="3"><?php echo $settings['po_exclusions']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Note</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="po_note" id="po_note" rows="3"><?php echo $settings['po_note']; ?></textarea>
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
                        <h4 class="modal-title">Add Vendor<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <!--                <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>SupplierController/add_supplier" enctype="multipart/form-data">-->
                <div class="modal-body">

                    <div class="card-body ">

                        <!-- form start -->
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label">Company Name<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="hidden" name="gst_check_customer" value="t" id="gst_check_customer">
                                <input type="hidden" class="form-control input-sm" name="redirect_supplier" value="po_supplier" id="redirect_supplier">
                                <input type="text" class="form-control input-sm" name="company_name" id="company_name" required="">
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
                                <input type="email" class="form-control input-sm" name="email" id="email" />
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
        var BASE_URL = '<?php echo base_url(); ?>';
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
            $(document).on('change keyup', '#total_quotation_amount', function() {
                var amt = $(this).val();
                $('#word2').text('Grand Total in Words: ' + convertNumberToWords(amt) + ' Only');
            });
            setInterval(function() {
                updateTotalQty();
                $('#word2').text('Grand Total in Words: ' + convertNumberToWords($('#total_quotation_amount').val()) + ' Only');
            }, 500);
            CKEDITOR.replace('po_terms_and_conditions');
            CKEDITOR.replace('po_payment_terms');
            CKEDITOR.replace('po_process_schedule');
            CKEDITOR.replace('po_taxes');
            CKEDITOR.replace('po_exclusions');
            CKEDITOR.replace('po_note');
            
            $('form').on('submit', function() {
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }
            });
            
            $('#supplier_id').on('change', function() {
                var customerId = $(this).val();
            // Setup state code matching for GST/IGST routing
            // State code 27 = GST (our state), other state codes = IGST
            var selectedText = $(this).find('option:selected').text();
            var companyStateCode = (selectedText.match(/\(\s*(\d+)\s*\)/) || [])[1];


            setupCompanySelectValidation(
                customerId,
                companyStateCode,
                <?php echo json_encode($settings['state_code']); ?>,
                '<?php echo base_url(); ?>SupplierController/create_purchase_order',
                '<?php echo base_url(); ?>SupplierController/create_central_gst_purchase',
                'sgst'
            );
        });

        // PR items auto-loader
        var pr_id = $('#pr_id').val();
        if (pr_id) {
            loadPRItems(pr_id);
        }

        function populateFirstPORow(itemCode, description, quantity, unit, price) {
            $('#item_name1').val(itemCode).trigger('change');
            setTimeout(function() {
                $('#quantity1').val(quantity).trigger('input');
                $('#description1').val(description);
                if (unit) {
                    $('#unit1').val(unit).trigger('change');
                }
                $('#price1').val(price).trigger('input');
            }, 500);
        }

        function addNewPORowWithValue(itemCode, description, quantity, unit, price) {
            var rowCount = $("#dynamic_field tr").length;
            var product_options = $("#item_name1").html();
            var unit_options = window.unitOptions || $("#unit1").html();
            
            var igst = "";
            var sgst = "";
            var cgst = "";
            var gst_check = $("#gst_check").val();
            
            if (gst_check !== "gst_check") { // IGST
                sgst = '<td class="gst hide"><input type="text" readonly="" name="sgst[]" id="sgst' + rowCount + '" class="form-control input-sm sgst_list" value="0.00" /></td>';
                cgst = '<td class="gst hide"><input type="text" readonly="" name="cgst[]" id="cgst' + rowCount + '" class="form-control input-sm cgst_list" value="0.00" /></td>';
                igst = '<td class="igst_edit_hide_show"><input type="text" readonly="" name="igst[]" id="igst' + rowCount + '" class="form-control input-sm igst_list" /></td>';
            } else { // SGST/CGST
                sgst = '<td class="gst"><input type="text" readonly="" name="sgst[]" id="sgst' + rowCount + '" class="form-control input-sm sgst_list" /></td>';
                cgst = '<td class="gst"><input type="text" readonly="" name="cgst[]" id="cgst' + rowCount + '" class="form-control input-sm cgst_list" /></td>';
                igst = '<td class="igst_edit_hide_show hide"><input type="text" readonly="" name="igst[]" id="igst' + rowCount + '" class="form-control input-sm igst_list" value="0.00" /></td>';
            }
            
            var newRowHtml = '<tr id="row' + rowCount + '">' +
                '<td><select style="width: 150px" class="form-control input-sm item_search_name add_new_product product_name_auto" name="product_name[]" id="item_name' + rowCount + '" onchange="myFunction1(this.id)" required="" data-live-search="true">' + product_options + '</select></td>' +
                '<td><button type="button" class="btn btn-info " onClick="descButton(this.id)" id="btnDescriptionId' + rowCount + '">description</button>' +
                '<textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description' + rowCount + '" rows="4"></textarea></td>' +
                '<td><input type="text" id="hsn' + rowCount + '" name="hsn[]" required="" class="form-control input-sm name_list hsn_auto" readonly/></td>' +
                '<td><span class="hide" id="total_quantity' + rowCount + '" name="total_quantity[]"></span>' +
                '<input type="hidden" name="total_quantity1[]" id="total_quantity" class="form-control input-sm name_list total_quantity_auto" />' +
                '<input type="text" id="quantity' + rowCount + '" name="quantity[]" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="' + quantity + '" /></td>' +
                '<td><select style="width: 100px" class="form-control input-sm item_search_unit" name="unit[]" id="unit' + rowCount + '" required="" data-live-search="true">' + unit_options + '</select></td>' +
                '<td><input type="text" readonly="" id="gst_per' + rowCount + '" name="gst_per[]" class="form-control input-sm name_list gst_per_auto" /></td>' +
                sgst + cgst + igst +
                '<td><input type="text" id="price' + rowCount + '" name="price[]" required="" class="form-control input-sm name_list price_auto" value="' + price + '" /></td>' +
                '<td><input type="text" maxlength="5" name="discount[]" id="discount' + rowCount + '" class="form-control input-sm name_list discount_auto number-only-validation" value="0" /></td>' +
                '<td><input type="hidden" id="amount' + rowCount + '" name="amount[]" class="form-control input-sm name_list amount_auto" value="0.00" />' +
                '<input type="hidden" name="amount_temp[]" id="amount_temp' + rowCount + '" class="amount_auto" value="0.00" />' +
                '<input type="hidden" name="gst_amount[]" id="gst_amount' + rowCount + '" class="form-control input-sm name_list gst_amount_auto" value="0.00" />' +
                '<span id="span_amount' + rowCount + '" name="span_amount[]" class="total_span_auto_amount">₹0.00</span></td>' +
                '<td><button type="button" name="remove" id="remove' + rowCount + '" class="btn btn-danger btn-xs btn_remove">X</button></td>' +
                '</tr>';
                
            $("#dynamic_field").append(newRowHtml);
            
            $("#item_name" + rowCount).select2({ placeholder: "Select Item" });
            $("#unit" + rowCount).select2({ placeholder: "Select Unit" });
            
            $("#item_name" + rowCount).val(itemCode).trigger('change');
            
            setTimeout(function() {
                $("#quantity" + rowCount).val(quantity).trigger('input');
                $("#description" + rowCount).val(description);
                if (unit) {
                    $("#unit" + rowCount).val(unit).trigger('change');
                }
                $("#price" + rowCount).val(price).trigger('input');
            }, 500);
        }

        function loadPRItems(pr_id) {
            $.ajax({
                url: BASE_URL + 'SupplierController/get_pr_items_for_po',
                type: 'GET',
                data: { pr_id: pr_id },
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.items.length > 0) {
                        var items = res.items;
                        var first = items[0];
                        populateFirstPORow(first.item_code, first.description, parseFloat(first.quantity), first.unit, parseFloat(first.estimated_cost) / parseFloat(first.quantity) || 0);
                        
                        for (var i = 1; i < items.length; i++) {
                            (function(item, idx) {
                                setTimeout(function() {
                                    addNewPORowWithValue(item.item_code, item.description, parseFloat(item.quantity), item.unit, parseFloat(item.estimated_cost) / parseFloat(item.quantity) || 0);
                                }, idx * 600);
                            })(items[i], i);
                        }
                    }
                },
                error: function() {
                    alert('Error loading Requisition items.');
                }
            });
        }

        $('#so_no').change(function() {
            var selected = $(this).find('option:selected');
            var oc = selected.data('oc');
            var project = selected.data('project');
            var soVal = $(this).val();
            
            $('#oc_no').val(oc ? oc : '');
            if (project) {
                $('#project_code').val(project).trigger('change');
            }

            var basePo = 'PO/<?php echo $financial_year; ?>/<?php echo $po_seq_formatted; ?>';
            if (soVal) {
                var soFy = '<?php echo $so_fy; ?>';
                var soSeq = '';
                var match = soVal.match(/(?:([0-9]{4})|([0-9]{2}-[0-9]{2})).*?([0-9]+)$/);
                if (match) {
                    soFy = match[1] ? match[1] : match[2].replace('-', '');
                    soSeq = match[3];
                } else {
                    var m2 = soVal.match(/([0-9]+)$/);
                    if (m2) soSeq = m2[1];
                }
                if (soSeq) {
                    basePo += '/(' + soFy + '/' + soSeq + ')';
                }
            }
            $('#number').val(basePo);
            $('label[name="number"] h2 b').text(basePo);
        });
        
        // Trigger change on load if a Sales Order is pre-selected
        if ($('#so_no').val()) {
            $('#so_no').trigger('change');
        }
        });
    </script>
