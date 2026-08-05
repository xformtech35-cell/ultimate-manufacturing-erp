<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . '/third_party/amount_convert.php');
?>

<style>
    .quotation-logo-left {
        text-align: left;
    }

    .quotation-logo-left img {
        display: inline-block;
        margin-top: 26px;
    }

    .quotation-grid-table {
        border-collapse: collapse;
        border: 2px solid #2f2f2f;
        background-color: #fff;
    }

    .quotation-grid-table > tbody > tr > th,
    .quotation-grid-table > tbody > tr > td,
    .quotation-grid-table > thead > tr > th,
    .quotation-grid-table > thead > tr > td {
        border: 1.5px solid #2f2f2f !important;
        color: #111;
        vertical-align: middle;
    }

    .quotation-grid-table > tbody > tr > th,
    .quotation-grid-table > thead > tr > th {
        background-color: #f3f3f3;
    }

    .empty-row td {
        height: 40px;
    }

    .item-row td {
        border-top: none;
        border-bottom: none;
        border-left: 1px solid black;
        border-right: 1px solid black;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'EstimateController/index/' ?>">Quotation</a></li>
                    <li class="active">Quotation Details</li>
                </ol>
            </section>

            <section class="content">
                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                    <h2>Quotation:<?php echo isset($estimates_data_group['number_fk']) ? $estimates_data_group['number_fk'] : ''; ?></h2>
                </label>

                <div class="row" style="padding:2%; margin-left:0; margin-right:0;">
                    <div class="col-xs-6" style="padding-left:0;">
                        <div class="btn-group" role="group">
                            <a href="<?php echo base_url(); ?>EstimateController/edit_estimate_details/<?php echo $estimates_data_group['id']; ?>" class="btn btn-primary" role="button" style="margin-right:5px;">Edit</a>
                            
                            <form method="POST" action="<?php echo base_url(); ?>EstimateController/duplicate_quote" style="display:inline-block; margin-right:5px;">
                                <input type="hidden" name="id" value="<?php echo $estimates_data_group['id']; ?>">
                                <button class="btn btn-primary" type="submit">Duplicate</button>
                            </form>
                            
                            <form method="POST" action="<?php echo base_url(); ?>EstimateController/sgst_to_igst" style="display:inline-block; margin-right:5px;">
                                <input type="hidden" name="number_fk" value="<?php echo $estimates_data_group['number_fk']; ?>">
                                <?php 
                                $gst_type = '';
                                foreach ($show_quotation as $key) { 
                                    $gst_type = $key->gst_type; 
                                    break; 
                                } 
                                ?>
                                <?php if ($gst_type != 'I') { ?>
                                    <button class="btn btn-primary" type="submit">SGST → IGST</button>
                                <?php } else { ?>
                                    <button class="btn btn-primary" type="submit">IGST → SGST</button>
                                <?php } ?>
                            </form>
                            
                            <form method="POST" action="<?php echo base_url(); ?>EstimateController/convert_to_sales_order/<?php echo $estimates_data_group['id']; ?>" style="display:inline-block; margin-right:5px;">
                                <input type="hidden" name="number" value="<?php echo $estimates_data_group['id']; ?>">
                                <?php
                                if (date('m') <= 3) { 
                                    $financial_year = (date('y') - 1) . '-' . date('y');
                                } else { 
                                    $financial_year = date('y') . '-' . (date('y') + 1);
                                }
                                ?>
                                <input type="hidden" name="salesorder_number" value="SO/<?php printf("%04d", $salesorder_id); ?>/<?php echo $financial_year; ?>">
                                <button class="btn btn-primary" type="submit">Convert to Sales Order</button>
                            </form>
                            
                            <form method="POST" action="<?php echo base_url(); ?>EstimateController/convert_to_invoice/<?php echo $estimates_data_group['id']; ?>" style="display:inline-block; margin-right:5px;">
                                <input type="hidden" name="number" value="<?php echo $estimates_data_group['id']; ?>">
                                <input type="hidden" name="invoice_number" value="INV/<?php printf("%04d", $invoice_id + 1); ?>/<?php echo $financial_year; ?>">
                                <button class="btn btn-primary" type="submit">Convert to Invoice</button>
                            </form>
                            
                            <div class="dropdown" style="display:inline-block;">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    More <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <li><a href="<?php echo base_url(); ?>Pdf/print_igst_quote?quote_number_id=<?php echo $estimates_data_group['id']; ?>">Export As PDF</a></li>
                                    <li><a href="<?php echo base_url(); ?>Pdf/print_igst_quote?quote_number_id=<?php echo $estimates_data_group['id']; ?>&sez=sez">Export As PDF SEZ</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 text-right" style="padding-right:0;">
                        <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-close"></i> Close</a>
                    </div>
                </div>

                <div class="shadows1">
                    <div class="row">
                        <section class="contemporary-template__header">
                            <div class="col-xs-4">
                                <div class="quotation-logo-left">
                                    <img src="<?php echo base_url() . $settings['company_logo'] ?>" width="70%" height="35%">
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <!-- Shipping To - conditional -->
                                <?php if (isset($estimates_data_group['shipping_address']) && !empty($estimates_data_group['shipping_address'])) { ?>
                                <div class="contemporary-template__header__info">
                                    <div class="wv-heading--subtitle"><b>SHIP TO:</b></div>
                                    <span class="wv-text--strong"><?php echo $estimates_data_group['shipping_address']; ?></span>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-xs-4">
                                <div class="contemporary-template__header__info">
                                    <div class="wv-heading--title"><h3>QUOTATION</h3></div>
                                    <div class="wv-heading--subtitle"><?php echo $settings['quotation_subheading']; ?></div>
                                    <span class="wv-text--strong"><?php echo $settings['company_name']; ?></span><br>
                                    <span class="wv-text--strong"><b>GST No:</b> <?php echo $settings['company_gst']; ?></span><br>
                                    <span class="wv-text--strong"><b>PAN No:</b> <?php echo $settings['company_pan']; ?></span><br>
                                    <span class="wv-text--strong"><b>Mobile:</b> <?php echo $settings['mobile']; ?></span><br>
                                    <span class="wv-text--strong"><b>Email:</b> <?php echo $settings['email']; ?></span><br>
                                    <span class="wv-text--strong"><b>Address:</b> <?php echo $settings['address']; ?></span>
                                </div>
                            </div>
                        </section>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="contemporary-template__header__info">
                                <div class="wv-heading--subtitle"><b>Buyer:</b></div>
                                <span class="wv-text--strong"><b>Company:</b> <?php echo $estimates_data_group['company_name']; ?></span><br>
                                <span class="wv-text--strong"><b>Address:</b> <?php echo $estimates_data_group['address'] ?: 'None Provided'; ?></span><br>
                                <span class="wv-text--strong"><b>GST:</b> <?php echo $estimates_data_group['gst'] ?: 'None Provided'; ?></span><br>
                                <span class="wv-text--strong"><b>PAN:</b> <?php echo $estimates_data_group['pancard'] ?: 'None Provided'; ?></span><br>
                                <span class="wv-text--strong"><b>State Code:</b> <?php echo $estimates_data_group['state_code'] ?: 'None Provided'; ?></span><br>
                                <span class="wv-text--strong"><b>Customer:</b> <?php echo $estimates_data_group['fullname']; ?></span><br>
                                <span class="wv-text--strong"><b>Code:</b> <?php echo isset($estimates_data_group['c_code']) ? $estimates_data_group['c_code'] : ''; ?></span>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <!-- Empty or additional info if needed -->
                        </div>
                        <div class="col-xs-4">
                            <div class="contemporary-template__header__info">
                                <div class="wv-heading--subtitle"><b>Quotation Details:</b></div>
                                <span class="wv-text--strong"><b>No:</b> <?php echo $estimates_data_group['number_fk']; ?></span><br>
                                <span class="wv-text--strong"><b>Date:</b> <?php echo date('d-m-Y', strtotime($estimates_data_group['date'])); ?></span><br>
                                <span class="wv-text--strong"><b>Expires:</b> <?php echo date('d-m-Y', strtotime($estimates_data_group['exp_date'])); ?></span><br>
                                <span class="wv-text--strong"><b>Grand Total:</b> <?php echo indian_number_format($estimates_data_group['total'], 2); ?></span><br>
                                <span class="wv-text--strong"><b>Enquiry:</b> <?php 
                                    $enquiry_map = ['1'=>'By Mail', '2'=>'By Verbal', '3'=>'Just Dial', '4'=>'India Mart'];
                                    echo isset($estimates_data_group['enquiry']) ? ($enquiry_map[$estimates_data_group['enquiry']] ?? 'None') : 'None';
                                ?></span>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered quotation-grid-table">
                            <tr>
                                <th style="text-align: center;">Sr.No.</th>
                                <th>Description</th>
                                <th style="text-align: center;">HSN</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: center;">Unit</th>
                                <th style="text-align: center;">GST(%)</th>
                                <th class="gst" style="text-align: center;">SGST</th>
                                <th class="gst" style="text-align: center;">CGST</th>
                                <th class="igst" style="text-align: center;">IGST</th>
                                <th style="text-align: center;">Price</th>
                                <th style="text-align: center;">Disc(%)</th>
                                <th style="text-align: center;">Amount</th>
                            </tr>
                            <?php
                            $i = 1; $itemsToShow = 20;
                            $gst_type = ''; $total_qty = 0; $basic_total = 0; $cgst_total = 0; $sgst_total = 0; $igst_total = 0;
                            foreach ($show_quotation as $key) {
                                $gst_type = $key->gst_type;
                                $total_qty += $key->quantity;
                                $basic_total += $key->amount;
                                $cgst_total += $key->cgst;
                                $sgst_total += $key->sgst;
                                $igst_total += $key->igst;
                            ?>
                                <tr class="item-row">
                                    <td style="text-align: center;"><?php echo $i; ?></td>
                                    <td><?php echo $key->product_name . ' - ' . $key->item_name . '<br>' . $key->description; ?></td>
                                    <td style="text-align: center;"><?php echo $key->hsn_code; ?></td>
                                    <td style="text-align: center;"><?php echo $key->quantity; ?></td>
                                    <td style="text-align: center;"><?php echo $key->unit; ?></td>
                                    <td style="text-align: center;"><?php echo $key->gst; ?></td>
                                    <?php if ($gst_type != 'I') { ?>
                                        <td class="gst" style="text-align: center;"><?php echo indian_number_format($key->sgst, 2); ?></td>
                                        <td class="gst" style="text-align: center;"><?php echo indian_number_format($key->cgst, 2); ?></td>
                                        <td></td>
                                    <?php } else { ?>
                                        <td colspan="2"></td>
                                        <td class="igst" style="text-align: center;"><?php echo indian_number_format($key->igst, 2); ?></td>
                                    <?php } ?>
                                    <td style="text-align: center;"><?php echo indian_number_format($key->price, 2); ?></td>
                                    <td style="text-align: center;"><?php echo $key->discount; ?>%</td>
                                    <td style="text-align: right;"><?php echo indian_number_format($key->amount, 2); ?></td>
                                </tr>
                            <?php $i++; } 
                            while ($i <= $itemsToShow) { ?>
                                <tr class="empty-row">
                                    <td colspan="12"></td>
                                </tr>
                            <?php $i++; } ?>
                            
                            <tr>
                                <td colspan="9" class="text-right"><b>Total Qty:</b></td>
                                <td class="text-right" colspan="3"><b><?php echo indian_number_format($total_qty, 2); ?></b></td>
                            </tr>
                            <tr>
                                <td colspan="9" class="text-right">Total Before Tax:</td>
                                <td class="text-right" colspan="3">₹<?php echo indian_number_format($basic_total, 2); ?></td>
                            </tr>
                            <?php if ($gst_type != 'I') { ?>
                                <tr class="gst">
                                    <td colspan="9" class="text-right"><b>CGST Amount:</b></td>
                                    <td class="text-right" colspan="3">₹<?php echo indian_number_format($cgst_total, 2); ?></td>
                                </tr>
                                <tr class="gst">
                                    <td colspan="9" class="text-right"><b>SGST Amount:</b></td>
                                    <td class="text-right" colspan="3">₹<?php echo indian_number_format($sgst_total, 2); ?></td>
                                </tr>
                            <?php } else { ?>
                                <tr class="igst">
                                    <td colspan="9" class="text-right"><b>IGST Amount:</b></td>
                                    <td class="text-right" colspan="3">₹<?php echo indian_number_format($igst_total, 2); ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="9" class="text-right"><b>Grand Total:</b></td>
                                <td class="text-right" colspan="3">₹<?php echo indian_number_format($estimates_data_group['total'], 2); ?></td>
                            </tr>
                            <tr>
                                <td colspan="12" class="text-right"><b>Amount in Words: <?php echo number_to_word($estimates_data_group['total']); ?> Only</b></td>
                            </tr>
                        </table>
                    </div>

                    <?php if (!empty($estimates_data_group['quotation_memo'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Notes:</b></label>
                            <div><?php echo $estimates_data_group['quotation_memo']; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($estimates_data_group['terms_and_conditions'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Terms & Conditions:</b></label>
                            <div><?php echo $estimates_data_group['terms_and_conditions']; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($estimates_data_group['payment_terms'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Payment Terms:</b></label>
                            <div><?php echo $estimates_data_group['payment_terms']; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($estimates_data_group['process_schedule'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Process Schedule:</b></label>
                            <div><?php echo $estimates_data_group['process_schedule']; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($estimates_data_group['taxes'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Taxes:</b></label>
                            <div><?php echo $estimates_data_group['taxes']; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($estimates_data_group['exclusions'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Exclusions:</b></label>
                            <div><?php echo $estimates_data_group['exclusions']; ?></div>
                        </div>
                    <?php } ?>

                    <div class="form-group row">
                        <label class="col-sm-9 control-label"><b>Receiver's Signature:</b></label>
                        <label class="col-sm-3 control-label"><b>Authorized Signature:</b></label>
                    </div>
                    <center style="font-size: 12px"><?php echo $settings['quotation_footer']; ?></center>
                    <center style="font-size: 10px">This is Computer Generated Quotation</center>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
</body>

<script>
$(document).ready(function() {
    var igst = $('input[name="igst[]"]').length > 0;
    var gst = $('input[name="gst"]').length > 0;
    
    if (igst) {
        $('.gst').hide();
        $('.igst').show();
    } else if (gst) {
        $('.gst').show();
        $('.igst').hide();
    }
    
    $('#duplicateBtn').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var quoteId = form.find('input[name="id"]').val();
        $(this).html('<i class="fa fa-spinner fa-spin"></i> Duplicating...').prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('Quotation duplicated!');
                    window.location.href = '<?php echo base_url(); ?>EstimateController/edit_estimate_details/' + response.new_quote_id;
                } else {
                    alert('Error: ' + (response.message || 'Failed'));
                }
            },
            error: function() {
                alert('Duplication failed. Try again.');
            },
            complete: function() {
                $('#duplicateBtn').html('Duplicate').prop('disabled', false);
            }
        });
    });
});
</script>
