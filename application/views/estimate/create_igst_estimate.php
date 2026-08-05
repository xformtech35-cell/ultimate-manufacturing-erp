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
        content: '*';
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
                    IGST Quotation
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'EstimateController/index/' ?>"> Quotation</a></li>
                    <li class="active"> Quotation Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        <div class="col-md-2">
                            <!--<a href="<?php //echo base_url();   
                                            ?>EstimateController/create_estimate" id="non_gst" name="non_gst" class="btn btn-primary" role="button">Non GST</a>-->
                        </div>
                        <div class="pull-right">
                            <div class="col-md-6">
                                <a href="<?php echo base_url(); ?>EstimateController/create_gst_estimate" id="local_gst" name="local_gst" class="btn btn-primary" role="button">GST</a>
                            </div>

                            <!--                            <div class="col-md-2">
                                <a href="<?php echo base_url(); ?>EstimateController/create_igst_estimate" id="central_igst" name="central_igst" class="btn btn-primary" role="button">IGST</a>
                            </div>-->
                        </div>
                    </div>
                                                    <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                </div>



                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create IGST Quotation</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                             

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>EstimateController/add_estimate_quotation" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <?php
                                                if (date('m') <= 3) { //Upto March - previous FY
                                                    $financial_year = (date('y') - 1) . '-' . date('y');
                                                } else { //April onwards - current FY
                                                    $financial_year = date('y') . '-' . (date('y') + 1);
                                                }
                                                ?>
                                                <input type="hidden" name="quotation_igst_check" value="igst" id="quotation_igst_check">
                                                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="QUOTE/<?php printf("%04d", $quotation_id + 1); ?>/<?php echo $financial_year; ?>">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                                                    <h2>IGST Quotation:<b> QUOTE/<?php printf("%04d", $quotation_id + 1); ?>/<?php echo $financial_year; ?> </b></h2>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <?php if (in_array('Projects', $this->session->userdata('session_data_head')['permission'] ?? [])): ?>
                                             <div class="form-group row">
                                                  <label for="project_code" class="col-sm-5 control-label">Project Code</label>
                                                  <div class="col-sm-7">
                                                      <select class="form-control input-sm select2" name="project_code" id="project_code">
                                                          <option value="">Select Project Code</option>
                                                          <?php if(isset($project_code_result) && !empty($project_code_result)): ?>
                                                              <?php foreach($project_code_result as $pc): ?>
                                                                  <option value="<?php echo htmlspecialchars($pc->project_code); ?>"><?php echo htmlspecialchars($pc->project_code); ?></option>
                                                              <?php endforeach; ?>
                                                          <?php endif; ?>
                                                      </select>
                                                  </div>
                                             </div>
                                             <?php else: ?>
                                                 <input type="hidden" name="project_code" id="project_code" value="">
                                             <?php endif; ?>

                                            <div class="form-group row required">
                                                 <label for="inputEmail3" class="col-sm-5 control-label ">Company</label>
                                                 <div class="col-sm-7">
                                                     <select class="form-control input-sm company_search_name" name="customer_id" id="customer_id" required="">
                                                         <option value="">Select Company</option>
                                                         <?php foreach ($company_name as $key) { ?>
                                                             <option value="<?php echo $key->customer_id; ?>" data-fullname="<?php echo htmlspecialchars($key->fullname); ?>"><?php echo $key->company_name . " - " . $key->c_code; ?> - ( <?php echo $key->state_code; ?> )</option>
                                                         <?php } ?>
                                                     </select>
                                                     <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
 
                                                 </div>
                                             </div>

                                             <input type="hidden" name="customer_name" id="customer_name">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-5 control-label">Enquiry</label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm " name="enquiry" id="enquiry">
                                                        <option value="1">Mail</option>
                                                        <option value="2">Verbal</option>
                                                        <option value="3">Just Dial</option>
                                                        <option value="4">India Mart</option>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-5 control-label">SEZ</label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm" name="sez" id="sez">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row required">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="date" id="date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row required">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expires on</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control currentDateWithSevendays input-sm payment-due-date-check" name="expires_date" id="expires_date" required="" onkeydown="return false;">
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
                                            
                                        </div>

                                         <div class="col-md-6">
                                             <div class="form-group row">
                                                  <label for="system" class="col-sm-4 control-label">System<span style="color: red;">*</span></label>
                                                  <div class="col-sm-8">
                                                      <input type="text" class="form-control input-sm" name="system" id="system" required>
                                                  </div>
                                              </div>

                                             <div class="form-group row">
                                                 <label for="location" class="col-sm-4 control-label">Location</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" class="form-control input-sm" name="location" id="location">
                                                 </div>
                                             </div>

                                             <div class="form-group row">
                                                 <label for="capacity" class="col-sm-4 control-label">Capacity</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" class="form-control input-sm" name="capacity" id="capacity">
                                                 </div>
                                             </div>

                                             <div class="form-group row">
                                                 <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" value="<?php echo $settings['quotation_subheading']; ?>" class="form-control" name="quotation_subheading" id="quotation_subheading">
                                                 </div>
                                             </div>

                                             <div class="form-group row">
                                                 <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                 <div class="col-sm-8">
                                                     <textarea class="form-control" name="quotation_footer" id="quotation_footer" rows="3"><?php echo $settings['quotation_footer']; ?></textarea>
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
                                            <th>Action
                                                
                                            </th>
                                            <tr>
                                                <td>
                                                    <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list" name="product_name[]" id="item_name1" onchange="myFunction1(this.id)" required="" data-live-search="true" disabled>
                                                        <option></option>
   <option value="NEW">+ Add New Product</option>
                                                        <?php foreach ($item_name as $key) { ?>
                                                            <option value="<?php echo $key->code; ?>"><?php echo $key->code . " - " . $key->item_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <input type="hidden" class="form-control input-sm" name="quotation_id[]" id="quotation_id1" value="">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-info" onClick="descButton(this.id)" id="btnDescriptionId1">Description</button>
                                                    <textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description1" rows="4"></textarea>
                                                </td>
                                                <td><input type="text" id="hsn1" name="hsn[]" required="" readonly class="form-control input-sm name_list hsn_auto" /></td>
                                                <td><span class="hide" id="total_quantity1" name="total_quantity[]"></span><input type="hidden" name="total_quantity1[]" id="total_quantity" class="form-control input-sm name_list total_quantity_auto" /><input type="text" id="quantity1" name="quantity[]" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="1" /></td>
                                                <td><select style="width: 100px" class="form-control input-sm item_search_unit" name="unit[]" id="unit1" required="" data-live-search="true">
                                                        <option></option>
                                                    </select></td>
                                                <td><input type="text" readonly="" id="gst_per1" name="gst_per[]" class="form-control input-sm name_list gst_per_auto" /></td>
                                                <td class="hide"><input type="hidden" readonly="" name="sgst[]" id="sgst1" class="form-control input-sm sgst_list" /></td>
                                                <td class="hide"><input type="hidden" readonly="" name="cgst[]" id="cgst1" class="form-control input-sm cgst_list" /></td>
                                                <td><input type="text" readonly="" name="igst[]" id="igst1" class="form-control input-sm igst_list" /></td>
                                                <td><input type="text" id="price1" name="price[]" required="" class="form-control input-sm name_list price_auto" value="0.00" /></td>
                                                <td><input type="text" maxlength="5" name="discount[]" id="discount1" class="form-control input-sm name_list discount_auto number-only-validation" value="0" /></td>
                                                <td><input type="hidden" id="amount1" name="amount[]" class="form-control input-sm name_list amount_auto" value="0.00" /><input type="hidden" name="amount_temp[]" id="amount_temp1" class="amount_auto" value="0.00" /><input type="hidden" name="gst_amount[]" id="gst_amount1" class="form-control input-sm name_list gst_amount_auto" value="0.00" /><span id="span_amount1" name="span_amount[]" class="total_span_auto_amount">₹0.00</span></td>
                                                <td><button type="button" name="add_gst" id="add_gst" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
                                            </tr>
                                        </table>
                                        <div align="center">

                                            <button type="submit" name="submit" id="submit" class="btn btn-success">Save</button>
                                        </div>
                                        <div align="right" style="margin: 10px">

                                            <!--                                            Discount: <input type="text" name="discount"  id="discount" size="3" value="0" />%<br>-->
                                            <input type="hidden" name="temp_total" id="temp_total" class="form-control input-sm temp_total" value="0.00" /><br>
                                            <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span><br>
                                            <span id="total_amount" name="total_amount">Grand Total: ₹0.00</span><br>

                                            <input type="hidden" name="basic_total" id="basic_total" class="form-control input-sm basic_total" value="0.00" />
                                            <span id="igst_amount" class="hide_igst" name="igst_amount[]">Total IGST Amount: ₹0.00</span><br>
                                            <b> <span id="grand_total_amount1" name="grand_total_amount1[]">Grand Total: ₹0.00</span></b>
                                            <span class="hide" id="total_gst_amount" name="total_gst_amount[]">Total GST Amount: ₹0.00</span><br>

                                            <span id="grand_total_words" style="font-weight: bold; color: #555;">Grand Total in Words: Zero Rupees Only</span><br>
                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Terms & Conditions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="terms_and_conditions" id="terms_and_conditions" rows="3"><?php echo $settings['terms_and_conditions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Payment Terms</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="payment_terms" id="payment_terms" rows="3"><?php echo $settings['payment_terms']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Process Schedule</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="process_schedule" id="process_schedule" rows="3"><?php echo $settings['process_schedule']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-6">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Taxes</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="taxes" id="taxes" rows="3"><?php echo $settings['taxes']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Exclusions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="exclusions" id="exclusions" rows="3"><?php echo $settings['exclusions']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Note</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="quotation_memo" id="quotation_memo" rows="3"><?php echo $settings['quotation_memo']; ?></textarea>
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
                        <h4 class="modal-title">Add Company
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </h4>
                    </center>

                </div>
                <!--<form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>EstimateController/add_customer" enctype="multipart/form-data">-->
                <div class="modal-body">

                    <div class="card-body ">

                        <!-- form start -->
                        <div class="form-group row required">
                            <label for="inputEmail3" class="col-sm-4 control-label">Company Name</label>
                            <div class="col-sm-7">
                                <input type="hidden" name="gst_check_customer" value="gst_check_customer" id="gst_check_customer">
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
                                <input type="text" maxlength="15" class="form-control " name="gst" id="gst" style="text-transform: uppercase;" placeholder="e.g., 27AAPFU0205R1Z0">
                            </div>
                            <small class="text-muted col-sm-7 col-sm-offset-4">15-digit GST number</small>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="pancard" id="pancard" style="text-transform: uppercase;" maxlength="10" placeholder="e.g., AAPFU0205R">
                            </div>
                            <small class="text-muted col-sm-7 col-sm-offset-4">10-digit PAN (auto-filled from GST if available)</small>
                        </div>

                           <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                            <div class="col-sm-7">
                                <input type="number" class="form-control " name="state_code" id="state_code">
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
                            <label for="inputEmail3" class="col-sm-4 control-label"> Address</label>
                            <div class="col-sm-7">
                                <!--                                    <input type="text" class="form-control " name="address" id="address" >-->
                                <textarea class="form-control input-sm" name="address" id="address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-success performa_submit">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
                <!--</form>-->
            </div>
        </div>
    </div>
    <script>
        // Function to convert number to words (Indian numbering system)
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

// Get company_id from URL
function getUrlParameter(name) {
    name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

var companyIdFromUrl = getUrlParameter('company_id');
if (companyIdFromUrl) {
    var $customerSelect = $('#customer_id');
    $customerSelect.val(companyIdFromUrl).trigger('change');   // for plain select
    // If Select2 is used, also:
    if ($customerSelect.hasClass('select2-hidden-accessible')) {
        $customerSelect.trigger('change.select2');
    }
} 

            updateTotalQty();
            updateGrandTotalWords();
            $(document).on('input change', 'input[name="quantity[]"], input[name="price[]"], input[name="discount[]"], input[name="gst_per[]"], input[name="igst[]"]', function() {
                updateTotalQty();
                setTimeout(updateGrandTotalWords, 100);
            });
            setInterval(function() {
                updateTotalQty();
                updateGrandTotalWords();
            }, 500);

            CKEDITOR.replace('terms_and_conditions');
            CKEDITOR.replace('payment_terms');
            CKEDITOR.replace('process_schedule');
            CKEDITOR.replace('taxes');
            CKEDITOR.replace('exclusions');
            CKEDITOR.replace('quotation_memo');

            // Auto-fetch project details (System, Location, Capacity) when project_code is selected
            $(document).on('change select2:select', '#project_code', function() {
                var projectCode = $(this).val();
                if (!projectCode) {
                    return;
                }
                $.ajax({
                    url: '<?php echo base_url(); ?>SalesOrderController/ajax_get_project_details',
                    type: 'POST',
                    data: { project_code: projectCode },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success) {
                            if (response.customer) {
                                $('#customer_id').val(response.customer.customer_id).trigger('change');
                                $('#customer_name').val(response.customer.fullname || '');
                            } else {
                                $('#customer_name').val('');
                            }
                            if (response.project) {
                                $('#system').val(response.project.system || '');
                                $('#location').val(response.project.location || '');
                                $('#capacity').val(response.project.capacity || '');
                            }
                        }
                    }
                });
            });

            // GST to PAN and State Code conversion
            $(document).off('blur', '#gst').on('blur', '#gst', function() {
                var gstNo = $(this).val().trim().toUpperCase();
                
                if (gstNo.length === 0) {
                    return; // Allow empty field
                }
                
                // GST validation: must be 15 characters
                if (gstNo.length !== 15) {
                    alert('GST No must be 15 characters long. Example: 27AAPFU0205R1Z0');
                    $(this).val('');
                    $('#pancard').val('');
                    $('#state_code').val('');
                    $(this).focus();
                    return;
                }
                
                // Validate GST format: 2 digits + 10 char PAN + 1 digit + 1 char + 1 digit
                var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
                if (!gstRegex.test(gstNo)) {
                    alert('Invalid GST format. Expected: 2 digits + PAN + 1 digit + 1 letter + 1 digit\nExample: 27AAPFU0205R1Z0');
                    $(this).val('');
                    $('#pancard').val('');
                    $('#state_code').val('');
                    $(this).focus();
                    return;
                }
                
                // Extract State Code (first 2 digits) from GST No and auto-fill
                var stateCode = gstNo.substring(0, 2);
                $('#state_code').val(stateCode);
                
                // Extract PAN (characters 2-11) from GST No and auto-fill
                var panNo = gstNo.substring(2, 12);
                $('#pancard').val(panNo);
            });

            // PAN validation
            $(document).off('blur', '#pancard').on('blur', '#pancard', function() {
                var panNo = $(this).val().trim().toUpperCase();
                
                if (panNo.length === 0) {
                    return; // Allow empty field
                }
                
                // PAN validation: must be 10 characters
                if (panNo.length !== 10) {
                    alert('PAN No must be 10 characters long');
                    $(this).focus();
                    return;
                }
                
                // Validate PAN format: 5 letters, 4 numbers, 1 letter
                var panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
                if (!panRegex.test(panNo)) {
                    alert('Invalid PAN format.\nExpected: 5 letters + 4 numbers + 1 letter\nExample: AAPFU0205R');
                    $(this).focus();
                    return;
                }
            });

               // Auto-fetch customer details (PAN and GST) when company is selected
            $('#customer_id').on('change', function() {
                var customerId = $(this).val();

                // Enable/Disable item select based on company selection
                if (customerId) {
                    // Enable item select when company is selected
                    $('#item_name1').prop('disabled', false);
                } else {
                    // Disable item select when no company is selected
                    $('#item_name1').prop('disabled', true);
                }

                var fullname = $(this).find('option:selected').data('fullname') || '';
                $('#customer_name').val(fullname);

                  var selectedText = $(this).find('option:selected').text();
            var companyStateCode = (selectedText.match(/\(\s*(\d+)\s*\)/) || [])[1];
             setupCompanySelectValidation(
                customerId,
                companyStateCode,
                <?php echo json_encode($settings['state_code']); ?>,
                '<?php echo base_url(); ?>EstimateController/create_gst_estimate',
                '<?php echo base_url(); ?>EstimateController/create_igst_estimate',
                'igst'
            );
                
              
            });
        });
    </script>
