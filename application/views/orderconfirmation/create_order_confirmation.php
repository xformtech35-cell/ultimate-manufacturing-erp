<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
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
                    Create Order Confirmation
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url(); ?>OrderConfirmationController/index">Order Confirmation</a></li>
                    <li class="active">Create OC</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Order Confirmation Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="<?php echo base_url(); ?>OrderConfirmationController/save_order_confirmation" method="POST" enctype="multipart/form-data">
                                <div class="box-body">
                                    <!-- Flash Messages -->
                                    <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                        <div role="alert" class="alert alert-success">
                                            <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                            <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                        </div>
                                    <?php } ?>

                                    <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                        <div role="alert" class="alert alert-info">
                                            <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                            <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                        </div>
                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="number">OC Number</label>
                                                <?php 
                                                $oc_number = '';
                                                if(isset($oc_id) && $oc_id > 0) {
                                                    $oc_number = 'OC-' . str_pad($oc_id + 1, 3, '0', STR_PAD_LEFT) . '/' . date('y') . '-' . (date('y') + 1);
                                                } else {
                                                    $oc_number = 'OC-001/' . date('y') . '-' . (date('y') + 1);
                                                }
                                                ?>
                                                <input type="text" class="form-control" id="number" name="number" value="<?php echo $oc_number; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Date <span style="color: red;">*</span></label>
                                                <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="row">
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="customer_id">Customer <span style="color: red;">*</span></label>
                                                 <select class="form-control" id="customer_id" name="customer_id" required>
                                                     <option value="">-- Select Customer --</option>
                                                     <?php if(isset($customer_result) && !empty($customer_result)) {
                                                         foreach($customer_result as $cust) {
                                                             $selected = (isset($so_header['customer_id_fk']) && $so_header['customer_id_fk'] == $cust->customer_id) ? 'selected' : '';
                                                             $addr = isset($cust->address) ? htmlspecialchars($cust->address, ENT_QUOTES) : '';
                                                             $gstin = isset($cust->gstin) ? htmlspecialchars($cust->gstin, ENT_QUOTES) : '';
                                                             $mobile = isset($cust->mobile_number) ? htmlspecialchars($cust->mobile_number, ENT_QUOTES) : '';
                                                             echo '<option value="'.$cust->customer_id.'" '.$selected.' data-address="'.$addr.'" data-gstin="'.$gstin.'" data-mobile="'.$mobile.'">'.$cust->company_name.'</option>';
                                                         }
                                                     } ?>
                                                 </select>
                                                 <div id="customer_info_box" style="display:none; margin-top: 6px; padding: 8px 12px; background: #f8f9fa; border-left: 3px solid #007bff; border-radius: 3px; font-size: 12px;"></div>
                                                 <div id="oa_action_box" style="display:none; margin-top: 8px;">
                                                     <button type="button" class="btn btn-sm btn-info btn-flat" data-toggle="modal" data-target="#modal_oa_preview" onclick="updateOALetterPreview()">
                                                         <i class="fa fa-file-text-o"></i> Preview & Download Order Acceptance Letter
                                                     </button>
                                                 </div>
                                             </div>
                                         </div>
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="supplier_id">Supplier / Issued By (Company Settings)</label>
                                                 <?php $setting_comp_name = !empty($settings['company_name']) ? $settings['company_name'] : 'Xformtech'; ?>
                                                 <select class="form-control" id="supplier_id" name="supplier_id">
                                                     <option value="0" selected><?= htmlspecialchars($setting_comp_name); ?></option>
                                                 </select>
                                                 <small class="help-block" style="margin-bottom:0; color: #007bff;"><i class="fa fa-building"></i> Company Name from Settings: <strong><?= htmlspecialchars($setting_comp_name); ?></strong></small>
                                             </div>
                                         </div>
                                     </div>

                                     <div class="row">
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="po_reference">Customer PO Reference No.</label>
                                                 <input type="text" class="form-control" id="po_reference" name="po_reference" placeholder="e.g., 4520232398" value="<?php echo isset($so_header['customer_po_no']) ? $so_header['customer_po_no'] : ''; ?>">
                                             </div>
                                         </div>
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="po_date">Customer PO Date</label>
                                                 <input type="date" class="form-control" id="po_date" name="po_date" value="<?php echo isset($so_header['po_date']) ? $so_header['po_date'] : ''; ?>">
                                             </div>
                                         </div>
                                     </div>

                                     <div class="row">
                                         <div class="col-md-12">
                                             <div class="form-group">
                                                 <label for="subject">Subject Line for OA Letter</label>
                                                 <input type="text" class="form-control" id="subject" name="subject" placeholder="e.g., Order Acceptance Letter against DOSING SYSTEM for WTP Plant (Hindalco) (W-26004)" value="<?php echo isset($so_header['subject']) ? $so_header['subject'] : ''; ?>">
                                             </div>
                                         </div>
                                     </div>

                                     <div class="row">
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="delivery_date">Expected Dispatch / Delivery Date</label>
                                                 <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="<?php echo isset($so_header['delivery_date']) ? $so_header['delivery_date'] : ''; ?>">
                                             </div>
                                         </div>
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="payment_terms">Payment Terms</label>
                                                 <input type="text" class="form-control" id="payment_terms" name="payment_terms" placeholder="e.g., 90% with full tax in 45 Days & 10% against submission of PBG..." value="<?php echo isset($so_header['payment_terms']) ? $so_header['payment_terms'] : '90% with full tax in 45 Days & 10% against submission of PBG valid for warranty period.'; ?>">
                                             </div>
                                         </div>
                                     </div>

                                     <div class="row">
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="price_basis">Price Basis</label>
                                                 <input type="text" class="form-control" id="price_basis" name="price_basis" placeholder="e.g., Ex-works Talwade, Pune." value="Ex-works Talwade, Pune.">
                                             </div>
                                         </div>
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="transportation_charges">Transportation Charges</label>
                                                 <input type="text" class="form-control" id="transportation_charges" name="transportation_charges" placeholder="e.g., Extra to PRAJ scope." value="Extra to PRAJ scope.">
                                             </div>
                                         </div>
                                     </div>

                                     <div class="row">
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="service_charges">Service Charges</label>
                                                 <input type="text" class="form-control" id="service_charges" name="service_charges" placeholder="e.g., Rs.5000/- will be charged extra per day per Engineer basis." value="Rs.5000/- will be charged extra per day per Engineer basis.">
                                             </div>
                                         </div>
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="warranty">Warranty</label>
                                                 <input type="text" class="form-control" id="warranty" name="warranty" placeholder="e.g., 30 months from date of dispatch..." value="30 months from date of dispatch or 24 months from date of commissioning whichever is earlier against any manufacturing defect.">
                                             </div>
                                         </div>
                                     </div>

                                      <?php if ($_has_project_master): ?>
                                      <div class="row">
                                          <div class="col-md-6">
                                              <div class="form-group">
                                                  <label for="project_code">Project Code</label>
                                                  <input type="text" class="form-control" id="project_code" name="project_code" placeholder="Enter project code" value="<?php echo isset($so_header['project_code']) ? $so_header['project_code'] : ''; ?>">
                                              </div>
                                          </div>
                                      </div>
                                      <?php endif; ?>

                                     <div class="row">
                                         <div class="col-md-12">
                                             <div class="form-group">
                                                 <label for="remarks">Remarks</label>
                                                 <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Enter any remarks..."></textarea>
                                             </div>
                                         </div>
                                     </div>

                                    <hr>
                                    <h4>OC Items</h4>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered" id="ocTable">
                                                <thead>
                                                    <tr>
                                                        <th width="25%">Description</th>
                                                        <th width="10%">HSN Code</th>
                                                        <th width="10%">Quantity</th>
                                                        <th width="8%">Unit</th>
                                                        <th width="12%">Unit Price</th>
                                                        <th width="8%">Tax Rate %</th>
                                                        <th width="12%">Amount</th>
                                                        <th width="10%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="oc-item">
                                                        <td><input type="text" class="form-control description" name="description[]" placeholder="Item description"></td>
                                                        <td><input type="text" class="form-control hsn_code" name="hsn_code[]" placeholder="HSN"></td>
                                                        <td><input type="number" class="form-control quantity" name="quantity[]" placeholder="Qty" step="0.01"></td>
                                                        <td><input type="text" class="form-control unit" name="unit[]" placeholder="Unit"></td>
                                                        <td><input type="number" class="form-control unit_price" name="unit_price[]" placeholder="Price" step="0.01"></td>
                                                        <td><input type="number" class="form-control tax_rate" name="tax_rate[]" placeholder="%" step="0.01" value="0"></td>
                                                        <td><input type="number" class="form-control amount" name="amount[]" placeholder="Amount" readonly></td>
                                                        <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="7"><button type="button" class="btn btn-success btn-sm" id="addRowBtn"><i class="fa fa-plus"></i> Add Row</button></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Sub Total</label>
                                                <input type="text" class="form-control" id="sub_total" name="sub_total" placeholder="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tax Amount</label>
                                                <input type="text" class="form-control" id="tax_amount" name="tax_amount" placeholder="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Total Amount</label>
                                                <input type="text" class="form-control" id="total_amount" name="total_amount" placeholder="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Order Confirmation</button>
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/index" class="btn btn-default"><i class="fa fa-ban"></i> Cancel</a>
                                </div>
                            </form>
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2024</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->
    
    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="<?php echo base_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Add new row
            $('#addRowBtn').click(function() {
                var newRow = '<tr class="oc-item">' +
                    '<td><input type="text" class="form-control description" name="description[]" placeholder="Item description"></td>' +
                    '<td><input type="text" class="form-control hsn_code" name="hsn_code[]" placeholder="HSN"></td>' +
                    '<td><input type="number" class="form-control quantity" name="quantity[]" placeholder="Qty" step="0.01"></td>' +
                    '<td><input type="text" class="form-control unit" name="unit[]" placeholder="Unit"></td>' +
                    '<td><input type="number" class="form-control unit_price" name="unit_price[]" placeholder="Price" step="0.01"></td>' +
                    '<td><input type="number" class="form-control tax_rate" name="tax_rate[]" placeholder="%" step="0.01" value="0"></td>' +
                    '<td><input type="number" class="form-control amount" name="amount[]" placeholder="Amount" readonly></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
                $('#ocTable tbody').append(newRow);
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotal();
            });

            // Calculate amount when quantity, price or tax changes
            $(document).on('change', '.quantity, .unit_price, .tax_rate', function() {
                var row = $(this).closest('tr');
                var quantity = parseFloat(row.find('.quantity').val()) || 0;
                var unit_price = parseFloat(row.find('.unit_price').val()) || 0;
                var tax_rate = parseFloat(row.find('.tax_rate').val()) || 0;
                
                var subtotal = quantity * unit_price;
                var tax_amount = subtotal * (tax_rate / 100);
                var amount = subtotal + tax_amount;
                
                row.find('.amount').val(amount.toFixed(2));
                calculateTotal();
            });

            // Calculate total
            function calculateTotal() {
                var subtotal = 0;
                var total_tax = 0;
                var grand_total = 0;
                
                $('.oc-item').each(function() {
                    var row = $(this);
                    var quantity = parseFloat(row.find('.quantity').val()) || 0;
                    var unit_price = parseFloat(row.find('.unit_price').val()) || 0;
                    var tax_rate = parseFloat(row.find('.tax_rate').val()) || 0;
                    
                    var row_subtotal = quantity * unit_price;
                    var row_tax = row_subtotal * (tax_rate / 100);
                    var row_amount = row_subtotal + row_tax;
                    
                    subtotal += row_subtotal;
                    total_tax += row_tax;
                    grand_total += row_amount;
                });
                
                $('#sub_total').val(subtotal.toFixed(2));
                $('#tax_amount').val(total_tax.toFixed(2));
                $('#total_amount').val(grand_total.toFixed(2));
            }
            // Customer Selection Info Display & OA Letter Trigger
            $('#customer_id').on('change', function() {
                var selected = $(this).find(':selected');
                var address = selected.data('address');
                var gstin = selected.data('gstin');
                var mobile = selected.data('mobile');
                var val = $(this).val();

                if (address || gstin || mobile) {
                    var html = '';
                    if (address) html += '<div style="margin-bottom:2px;"><i class="fa fa-map-marker" style="color:#007bff;"></i> <strong>Address:</strong> ' + address + '</div>';
                    if (gstin) html += '<div style="margin-bottom:2px;"><i class="fa fa-credit-card" style="color:#28a745;"></i> <strong>GSTIN:</strong> ' + gstin + '</div>';
                    if (mobile) html += '<div><i class="fa fa-phone" style="color:#17a2b8;"></i> <strong>Contact:</strong> ' + mobile + '</div>';
                    $('#customer_info_box').html(html).slideDown();
                } else {
                    $('#customer_info_box').slideUp();
                }

                if (val) {
                    $('#oa_action_box').slideDown();
                    updateOALetterPreview();
                    $('#modal_oa_preview').modal('show');
                } else {
                    $('#oa_action_box').slideUp();
                }
            }).trigger('change');
        });

        function updateOALetterPreview() {
            var companyName = "<?php echo !empty($settings['company_name']) ? addslashes($settings['company_name']) : 'UWS ENVIRO-TECH PVT LTD'; ?>";
            var companyTagline = "<?php echo !empty($settings['tagline']) ? addslashes($settings['tagline']) : 'Ultimate Technologies for Fluid Automation'; ?>";
            var logoUrl = "<?php echo !empty($settings['company_logo']) ? base_url() . $settings['company_logo'] : (!empty($settings['logo']) ? base_url() . $settings['logo'] : base_url() . 'uploads/xform-logo.jpg'); ?>";
            var stampUrl = "<?php echo !empty($settings['company_stamp']) ? base_url() . $settings['company_stamp'] : (!empty($settings['stamp_signature']) ? base_url() . $settings['stamp_signature'] : ''); ?>";
            var address = "<?php echo !empty($settings['address']) ? addslashes($settings['address']) : (!empty($settings['company_address']) ? addslashes($settings['company_address']) : 'Plot No. 19/C, D-1 Block, Shop No. 342, 3rd Floor, HEUU Industrial Spaces, MIDC Chinchwad, Pune-411019.'); ?>";
            var email = "<?php echo !empty($settings['email']) ? addslashes($settings['email']) : (!empty($settings['company_email']) ? addslashes($settings['company_email']) : 'projects@ultimatewater.in'); ?>";
            var website = "<?php echo !empty($settings['website']) ? addslashes($settings['website']) : 'www.ultimatewater.in'; ?>";
            var phone = "<?php echo !empty($settings['mobile']) ? addslashes($settings['mobile']) : (!empty($settings['company_mobile']) ? addslashes($settings['company_mobile']) : '020 29528571'); ?>";

            var number = $('#number').val() || 'OC-001/26-27';
            var dateInput = $('#date').val();
            var dateVal = dateInput ? new Date(dateInput).toLocaleDateString('en-GB') : new Date().toLocaleDateString('en-GB');
            var poRef = $('#po_reference').val() || '4520232398';
            var poDateInput = $('#po_date').val();
            var poDateVal = poDateInput ? new Date(poDateInput).toLocaleDateString('en-GB') : '31.12.2025';
            var subject = $('#subject').val() || 'DOSING SYSTEM for WTP Plant (Hindalco) (W-26004)';
            var delDateInput = $('#delivery_date').val();
            var deliveryDateVal = delDateInput ? new Date(delDateInput).toLocaleDateString('en-GB') : '10.03.2026';
            var paymentTerms = $('#payment_terms').val() || '90% with full tax in 45 Days & 10% against submission of PBG valid for warranty period.';
            var priceBasis = $('#price_basis').val() || 'Ex-works Talwade, Pune.';
            var transportCharges = $('#transportation_charges').val() || 'Extra to PRAJ scope.';
            var serviceCharges = $('#service_charges').val() || 'Rs.5000/- will be charged extra per day per Engineer basis.';
            var warranty = $('#warranty').val() || '30 months from date of dispatch or 24 months from date of commissioning whichever is earlier against any manufacturing defect.';
            
            var subTotalVal = $('#sub_total').val() || '31,75,000';
            var taxAmountVal = parseFloat($('#tax_amount').val()) || 0;
            var subTotalNum = parseFloat(subTotalVal.replace(/,/g, '')) || 3175000;
            var gstPer = (subTotalNum > 0 && taxAmountVal > 0) ? Math.round((taxAmountVal / subTotalNum) * 100) : 18;

            var selectedCustText = $('#customer_id option:selected').text();
            if (!selectedCustText || selectedCustText.indexOf('-- Select') !== -1) {
                selectedCustText = 'Valued Customer';
            }

            var html = `
                <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                    <tr>
                        <td style="width:85px; vertical-align:middle;">
                            <img src="${logoUrl}" style="max-width:80px; height:auto;" onerror="this.style.display='none';">
                        </td>
                        <td style="padding-left:15px;">
                            <div style="font-size:20pt; font-weight:bold; color:#0d2b5c; font-family:Calibri, Arial, sans-serif;">${companyName.toUpperCase()}</div>
                            <div style="font-size:11pt; color:#c00000; font-style:italic; font-weight:bold; font-family:Calibri, Arial, sans-serif;">${companyTagline}</div>
                        </td>
                    </tr>
                </table>

                <div style="text-align:center; font-size:14pt; font-weight:bold; text-decoration:underline; margin:20px 0 25px 0;">
                    Order Acceptance Letter
                </div>

                <table style="width:100%; margin-bottom:20px; font-size:11pt;">
                    <tr>
                        <td align="left"><strong>Ref. No.</strong> ${number}</td>
                        <td align="right"><strong>Date:</strong> ${dateVal}</td>
                    </tr>
                </table>

                <div style="font-size:11pt; margin-bottom:20px; line-height:1.5;">
                    <strong>Subject:</strong> Order Acceptance Letter against ${subject}.
                </div>

                <div style="margin-bottom:12px; font-size:11pt;">Dear Sir,</div>
                <div style="margin-bottom:12px; font-size:11pt;">We thank you for valuable opportunity provided to us.</div>
                <div style="margin-bottom:12px; font-size:11pt;">
                    We acknowledge with thanks for the receipt of valuable PO No: <strong>${poRef}</strong> DT: <strong>${poDateVal}</strong>.
                </div>
                <div style="margin-bottom:16px; font-size:11pt; text-align:justify;">
                    We hereby acknowledge receipt of PO & accept with basic amount of <strong>Rs. ${subTotalVal} /-</strong> <strong>+${gstPer}% GST extra</strong> payable at actual on basic with following standard terms & conditions.
                </div>

                <ol style="margin:18px 0; padding-left:20px; font-size:11pt; line-height:1.6; list-style-type:none;">
                    <li style="margin-bottom:10px;"><strong>1) Price Basis:</strong> ${priceBasis}</li>
                    <li style="margin-bottom:10px;"><strong>2) Payment Terms:</strong> ${paymentTerms}</li>
                    <li style="margin-bottom:10px;"><strong>3) Transportation Charges:</strong> ${transportCharges}</li>
                    <li style="margin-bottom:10px;"><strong>4) Dispatch Date:</strong> On or before ${deliveryDateVal}.</li>
                    <li style="margin-bottom:10px;"><strong>5) Service Charges:</strong> ${serviceCharges}</li>
                    <li style="margin-bottom:10px;"><strong>6) Warranty:</strong> ${warranty}</li>
                </ol>

                <div style="margin-bottom:12px; font-size:11pt;">We will start further proceedings on priority basis.</div>
                <div style="margin-bottom:25px; font-size:11pt;">Thanking you.</div>

                <div style="margin-top:30px; font-size:11pt;">
                    <div>For <strong>${companyName}</strong></div>
                    <div style="width:120px; min-height:65px; margin:8px 0;">
                        ${stampUrl ? `<img src="${stampUrl}" style="max-width:120px; max-height:65px;">` : `<div style="border:1px dashed #007bff; color:#007bff; padding:8px; font-size:10px; text-align:center;">[Stamp & Signature]</div>`}
                    </div>
                    <div><strong>Authorized Signatory</strong></div>
                </div>

                <div style="margin-top:40px; border-top:1px solid #ddd; padding-top:15px; text-align:center; font-size:10pt; line-height:1.4;">
                    <div style="font-weight:bold; font-size:12pt; color:#3b1660;">${companyName}</div>
                    <div style="font-weight:bold; color:#000;">${address}</div>
                    <div style="margin-top:2px;">E-mail: <span style="color:#0000ff; text-decoration:underline;">${email}</span> &nbsp; Website: <span style="color:#0000ff; text-decoration:underline;">${website}</span></div>
                    <div style="font-weight:bold; margin-top:2px;">Phone: ${phone}</div>
                </div>
            `;

            $('#oa_letter_preview_container').html(html);
        }

        function printOAPreviewModal() {
            var printContents = document.getElementById('oa_letter_preview_container').innerHTML;
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Order Acceptance Letter</title>');
            printWindow.document.write('<style>body{font-family:Calibri, Arial, sans-serif; padding:20px;} @page{margin:10mm;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(function() {
                printWindow.print();
            }, 500);
        }
    </script>

    <!-- Modal: Order Acceptance Live Preview & Download -->
    <div class="modal fade" id="modal_oa_preview" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 900px;">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0d2b5c; color: white;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="fa fa-file-text-o"></i> Order Acceptance Letter Preview</h4>
                </div>
                <div class="modal-body" style="background-color: #f4f6f9; padding: 20px;">
                    <div id="oa_letter_preview_container" style="background: white; border: 4px double #000; padding: 30px 40px; min-height: 750px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-family: Calibri, 'Segoe UI', Arial, sans-serif;">
                        <!-- Dynamically filled by updateOALetterPreview() -->
                    </div>
                </div>
                <div class="modal-footer" style="background: #e9ecef;">
                    <button type="button" class="btn btn-primary" onclick="printOAPreviewModal()"><i class="fa fa-print"></i> Print / Download PDF</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

