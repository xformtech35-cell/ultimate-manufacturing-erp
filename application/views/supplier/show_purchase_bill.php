<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . '/third_party/amount_convert.php');
?>
<style>
    .purchasebill-logo-left {
        text-align: left;
    }

    .purchasebill-logo-left img {
        display: inline-block;
    }

    .purchasebill-grid-table {
        border-collapse: collapse;
        border: 2px solid #2f2f2f;
        background-color: #fff;
    }

    .purchasebill-grid-table > tbody > tr > th,
    .purchasebill-grid-table > tbody > tr > td,
    .purchasebill-grid-table > thead > tr > th,
    .purchasebill-grid-table > thead > tr > td {
        border: 1.5px solid #2f2f2f !important;
        color: #111;
        vertical-align: middle;
    }

    .purchasebill-grid-table > tbody > tr > th,
    .purchasebill-grid-table > thead > tr > th {
        background-color: #f3f3f3;
    }

    @media print {
        .row[style*="padding:2%"] {
            display: none !important;
        }
    }
</style>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <!--                <h1>
                                    Purchase Bill
                                </h1>-->
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">PURCHASE VOUCHER</a></li>
                    <li class="active">Purchase Voucher Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2> Purchase Voucher:<?php echo $purchase_bill_data_group['number']; ?></h2></label>

                        <div class="row" style="padding:2%; margin-left:0; margin-right:0;">
                            <div class="col-xs-6" style="padding-left:0;">
                                <a href="<?php echo base_url(); ?>SupplierController/edit_purchase_bill_details/<?php echo $purchase_bill_data_group['number']; ?>" class="btn btn-primary" role="button" style="margin-right:5px;">Edit</a>
                                <form method="POST" action="<?php echo base_url(); ?>SupplierController/sgst_to_igst" style="display:inline-block; margin-right:5px;">
                                    <input type="hidden" name="number" value="<?php echo $purchase_bill_data_group['number']; ?>">
                                    <?php 
                                    $gst_type = '';
                                    foreach ($show_purchase_bill as $key) { 
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
                                <button class="btn btn-primary" onclick="window.print();" style="margin-right:5px;">Print</button>
                                <div class="dropdown" style="display:inline-block;">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        More <b class="caret"></b>
                                    </button>
                                    <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo $purchase_bill_data_group['number']; ?>">
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <li class="hide"><a class="dropdown-item" href="<?php echo base_url(); ?>/LoginController/get_settings">Edit Business Information</a></li>
                                        <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/download_purchase_bill/<?php echo $purchase_bill_data_group['number']; ?>">Export As PDF</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xs-6 text-right" style="padding-right:0;">
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-close"></i> Close</a>
                            </div>
                        </div>

                        <!-- /.box-header -->
                        <div class="shadows1">
                            <div class="row">
                                <section>
                                    <div class="col-md-6">
                                        <div class="purchasebill-logo-left">
                                            <img src="<?php echo base_url() . $settings['company_logo'] ?>" width="50%" height="25%">
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="contemporary-template__header__info">
                                            <div class="wv-heading--title"><h1>Purchase Voucher</h1></div>
                                            <div class="wv-heading--subtitle"></div>
                                            <span class="wv-text--strong"><b> <?php echo $settings['company_name']; ?></b></span><br>
                                            <span class="wv-text--strong"><b>GST Number :</b> <?php echo $settings['company_gst']; ?></span><br>
                                           <span class="wv-text--strong"><b>PAN Number :</b> <?php echo $settings['company_pan']; ?></span><br>
                                            <span class="wv-text--strong"><b>Mobile Number :</b> <?php echo $settings['mobile']; ?></span><br>
                                            <span class="wv-text--strong"><b>Email ID :</b> <?php echo $settings['email']; ?></span><br>
                                            <span class="wv-text--strong"><b>Address :</b> <?php echo $settings['address']; ?></span>
                                            
                                        </div>
                                    </div>

                                </section>

                            </div>
                            <hr>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="contemporary-template__header__info">
                                        <span class="wv-text--strong"><b>Vendor Name :</b> <?php echo $purchase_bill_data_group['company_name']; ?></b></span><br>
                                        <span class="wv-text--strong"><b>Vendor Code :</b> <?php echo $purchase_bill_data_group['s_code'] ?? 'N/A'; ?></span><br>
                                            <span class="wv-text--strong"><b>Address :</b> <?php echo $purchase_bill_data_group['address']; ?></span><br>
                                            
                                           <span class="wv-text--strong"><b>GST Number :</b> <?php if ($purchase_bill_data_group['gst']) { ?>
                                                <?php echo $purchase_bill_data_group['gst']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?></span><br>
                                            <span class="wv-text--strong"><b>PAN Number :</b> <?php if ($purchase_bill_data_group['pancard']) { ?>
                                                <?php echo $purchase_bill_data_group['pancard']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?></span><br>
                                            <span class="wv-text--strong"><b> Customer Name :</b> <?php echo $purchase_bill_data_group['fullname']; ?></span><br>
                                            
                                        </div>
                                    </div>

                                <div class="col-md-6">
                                    <span class="wv-text--strong"><b>Voucher Details</b></b></span><br>
                                    
                                    
                                    <div class="contemporary-template__header__info">
                                        
                                        <span class="wv-text--strong"><b>Voucher Number :</b> <?php echo $purchase_bill_data_group['number']; ?></span><br>
                                        <span class="wv-text--strong"><b>Voucher Date :</b> <?php echo date('d-m-Y', strtotime($purchase_bill_data_group['date'])); ?></span><br>
                                        <span class="wv-text--strong"><b>Delivery Date :</b> <?php echo $purchase_bill_data_group['delivery_date']; ?></span><br>
                                        <span class="wv-text--strong"><b>Invoice No :</b> <?php echo $purchase_bill_data_group['invoice_no']; ?></span><br>
                                        <?php if (!empty($purchase_bill_data_group['po_upload'])): ?>
                                            <span class="wv-text--strong"><b>Bill File :</b> 
                                                <a href="<?php echo base_url('Download/download_invoice_file/' . urlencode($purchase_bill_data_group['po_upload'])); ?>" 
                                                   class="btn btn-sm btn-warning" title="Download Bill File">
                                                    <i class="fa fa-download"></i> Download Bill
                                                </a>
                                            </span><br>
                                        <?php endif; ?>
                                        <?php if (!empty($purchase_bill_data_group['invoice_file'])): ?>
                                            <span class="wv-text--strong"><b>Invoice File :</b> 
                                                <a href="<?php echo base_url('Download/download_invoice_file/' . urlencode($purchase_bill_data_group['invoice_file'])); ?>" 
                                                   class="btn btn-sm btn-info" title="Download Invoice File">
                                                    <i class="fa fa-download"></i> Download Invoice
                                                </a>
                                            </span><br>
                                        <?php endif; ?>
                                    </div>
                                    

                                </div>
                            </div>
                            <br>
                             <div class="table-responsive">  

                        <table class="table table-bordered purchasebill-grid-table" id="dynamic_field">  
                            <tr>
                                <th>Sr.No.</th>
                                <th>Description</th>
                                <th>HSN Code</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>TAX(%)</th>
                                <th class="gst">SGST</th>
                                <th class="gst">CGST</th>
                                <th class="igst">IGST</th>
                                 
                                <th>Price</th>
                                 <th>Discount(%)</th>
    <!--                                <th>Discount(%)</th>-->
                                <th>Amount</th>
                            </tr>
                            <?php
                            $i = 1;
                            $sgst_total_amt = 0;
                            $igst_total_amt = 0;
                            $amt = 0;
                            $total_qty = 0;
                            foreach ($show_purchase_bill as $key) {
                                ?>
                                <?php $sgst_total_amt = $sgst_total_amt + $key->cgst; ?>
                                <?php $igst_total_amt = $igst_total_amt + $key->igst; ?>
                                <?php $total_qty += $key->quantity; ?>
                                <tr> 
                                    <td><span id="" class=""></span>
                                        <?php echo $i; ?>
                                    </td>

                                    <td><span id="" class=""></span>
                                        <b><?php echo $key->product_name . " - " .  $key->item_name; ?></b>
                                        <?php echo $key->description; ?>
                                              <input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control input-sm name_list product_name_auto" />
                                    </td>

                                    <td><span id="" class=""></span>
                                        <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                                    </td>

                                    <td><span id="" class=""></span>
                                        <?php echo indian_number_format($key->quantity, 2); ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" value="1" />
                                    </td>
                                    
                                     <td><span id="" class=""></span>
                                        <?php echo $key->unit; ?><input type="hidden" name="unit[]" value="<?php echo $key->unit; ?>" id="unit<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                                    </td>
                                    
                                    <td><span id="" class=""></span>
                                        <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                    </td>

                                    <?php if ($key->gst_type != 'I') { ?>
                                        <td class="gst"><span id="" class=""></span>
                                            <input type="hidden" name="gst" value="gst" id="gst">
                                            <?php echo indian_number_format($key->sgst, 2); ?><input type="hidden" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" />
                                        </td>

                                        <td class="gst"><span id="" class=""></span>
                                            <?php echo indian_number_format($key->cgst, 2); ?><input type="hidden" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" />
                                        </td>
                                    <?php } else { ?>

                                        <td class="igst">
                                            <span id="" class=""></span><?php echo indian_number_format($key->igst, 2); ?>
                                            <input type="hidden" name="igst" value="igst" id="igst">
                                            <input type="hidden" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" />
                                        </td>
                                    <?php } ?>



                                    <td><span id="" class=""></span>
                                        <?php echo indian_number_format($key->price, 2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control input-sm name_list"/>
                                    </td>
                                    <td><span id="" class=""></span>
                                        <?php echo indian_number_format($key->discount, 2); ?><input type="hidden" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list"/>
                                    </td>
                                    <td><span id="" class=""></span>
                                        <?php echo indian_number_format($key->amount, 2); 
                                        $amt += $key->amount;
                                        ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>
                                    </td>


                                </tr>  

                                <?php
                                $i++;
                            }
                            ?>
                            <?php
                            $i = 1;
                            foreach ($show_purchase_bill as $key) {
                                ?>
                                <?php if ($key->gst_type != 'I') { ?>

                                    <tr>
                                        <td colspan="10" class="text-right">
                                            <b>Total Qty:</b>
                                        </td>
                                        <td class="text-left">
                                            <b><?php echo indian_number_format($total_qty, 2); ?></b>
                                        </td>
                                    </tr>

                                    <tr class="">
                                            <td colspan="10"  class="text-right">
                                                Total Before Tax 
                                            </td>
                                            <td colspan="1"  class="text-left">
                                                ₹ <?php echo $amt; ?>
                                            </td>
                                            
                             </tr> 
                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>CGST Amount : </b></label>
                                        </td>

                                        <td colspan="1"  class="text-left">
                                            <b><?php echo indian_number_format($sgst_total_amt, 2); ?></b><br>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>SGST Amount : </b></label>
                                        </td>
                                        <td colspan="1"  class="text-left">
                                            <b><?php echo indian_number_format($sgst_total_amt, 2); ?></b><br>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>GST Amount : </b></label>
                                        </td>
                                        <td colspan="1"  class="text-left">
                                            <b><?php echo indian_number_format($sgst_total_amt * 2, 2); ?></b><br>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                                        </td>
                                        <td colspan="1"  class="text-left">
                                            <b>₹<?php echo indian_number_format($purchase_bill_data_group['total'], 2); ?></b> <br>
                                        </td>
                                    </tr>
                                    <tr></tr>

                                    <tr>
                                        <td colspan="11"  class="text-right">
                                            <?php
                                            // include amount_convert
                                            require_once( APPPATH . '/third_party/amount_convert.php');
                                            ?>
                                            <b> Grand Total in Words:<?php echo number_to_word($purchase_bill_data_group['total']); ?> Only.</b><br>
                                        </td>
                                    </tr>

                                <?php } else { ?>
                                    
                                     <tr>
                                        <td colspan="9" class="text-right">
                                            <b>Total Qty:</b>
                                        </td>
                                        <td class="text-left">
                                            <b><?php echo indian_number_format($total_qty, 2); ?></b>
                                        </td>
                                    </tr>

                                     <tr class="">
                                            <td colspan="9"  class="text-right">
                                                Total Before Tax: 
                                            </td>
                                            <td colspan="1"  class="text-left">
                                                ₹ <?php echo $amt; ?>
                                            </td>
                                            
                             </tr> 

                                    <tr>
                                        <td colspan="9"  class="text-right">
                                            <label class="control-label"><b>IGST Amount : </b></label>
                                        </td>

                                        <td colspan="1"  class="text-left">
                                            <b> <?php echo indian_number_format($igst_total_amt, 2); ?></b><br>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="9"  class="text-right">
                                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                                        </td>

                                        <td colspan="1"  class="text-left">
                                            <b>₹<?php echo indian_number_format($purchase_bill_data_group['total'], 2); ?></b> <br>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <?php
                                            // include amount_convert
                                            require_once( APPPATH . '/third_party/amount_convert.php');
                                            ?>
                                            <b> Grand Total in Words:<?php echo number_to_word($purchase_bill_data_group['total']); ?> Only.</b><br>
                                        </td>
                                    </tr>

                                    <tr>

                                    </tr>
                                <?php } ?>

                                <?php
                                $i++;
                                break;
                            }
                            ?>
                        </table>   

                       

                        <div class="col-sm-12">
                            <textarea style="overflow: auto;
                                                          border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $purchase_bill_data_group['pv_note'] ?? ($settings['pv_note'] ?? ''); ?></textarea>
                        </div>

                                            <?php if (!empty($purchase_bill_data_group['pv_terms_and_conditions'])) { ?>
                                            <div class="col-sm-12" style="margin-top:10px;">
                                                <label class="control-label"><b>Terms &amp; Conditions:</b></label>
                                                <div><?php echo $purchase_bill_data_group['pv_terms_and_conditions']; ?></div>
                                            </div>
                                            <?php } ?>

                                            <?php if (!empty($purchase_bill_data_group['pv_payment_terms'])) { ?>
                                            <div class="col-sm-12" style="margin-top:10px;">
                                                <label class="control-label"><b>Payment Terms:</b></label>
                                                <div><?php echo $purchase_bill_data_group['pv_payment_terms']; ?></div>
                                            </div>
                                            <?php } ?>

                                            <?php if (!empty($purchase_bill_data_group['pv_process_schedule'])) { ?>
                                            <div class="col-sm-12" style="margin-top:10px;">
                                                <label class="control-label"><b>Process Schedule:</b></label>
                                                <div><?php echo $purchase_bill_data_group['pv_process_schedule']; ?></div>
                                            </div>
                                            <?php } ?>

                                            <?php if (!empty($purchase_bill_data_group['pv_taxes'])) { ?>
                                            <div class="col-sm-12" style="margin-top:10px;">
                                                <label class="control-label"><b>Taxes:</b></label>
                                                <div><?php echo $purchase_bill_data_group['pv_taxes']; ?></div>
                                            </div>
                                            <?php } ?>

                                            <?php if (!empty($purchase_bill_data_group['pv_exclusions'])) { ?>
                                            <div class="col-sm-12" style="margin-top:10px;">
                                                <label class="control-label"><b>Exclusions:</b></label>
                                                <div><?php echo $purchase_bill_data_group['pv_exclusions']; ?></div>
                                            </div>
                                            <?php } ?>

                        <div class="form-group row ">
                            <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Signatory :</b></label>
                            <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Signatory :</b></label>
                        </div>

                        <div style="text-align: center;">This is Computer Generated Invoice</div><br>

                    </div>  
                        </div>
                        <!-- /.box-body -->
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
            </section>
        </div>
        <!-- /.row -->
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <div id="purchaseBillWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-success">
                    <center>
                        <h4 class="modal-title">Send Purchase Voucher WhatsApp<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <input type="hidden" class="form-control" id="pb_whatsapp_number" value="">
                                <input type="text" class="form-control input-sm" id="pb_whatsapp_mobile" placeholder="Enter mobile number" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Message<span style="color: red;">*</span></label>
                            <div class="col-sm-7">
                                <textarea class="form-control input-sm" id="pb_whatsapp_message" rows="4" required=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="pb_whatsapp_send_link" target="_blank" rel="noopener" class="btn btn-success"><i class="fa fa-whatsapp" aria-hidden="true"></i> Send WhatsApp</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
    
    
        <script>

        $(document).ready(function () {

            var igst = $("#igst").val();
         //   alert(igst);
            if (igst == "igst") {
                $(".gst").hide();
                $(".igst").show();

            } else {
                $(".gst").show();
//                 $(".gst"). css('padding','10% 0% 0% 0%')
                $(".igst").hide();
            }

            function buildPurchaseBillWhatsAppUrl() {
                var mobile = ($('#pb_whatsapp_mobile').val() || '').replace(/[^0-9]/g, '');
                var message = $('#pb_whatsapp_message').val() || '';

                if (!mobile || !message.trim()) {
                    $('#pb_whatsapp_send_link').attr('href', '#');
                    return;
                }

                $('#pb_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
            }

            $(document).on('click', '.view-modal-pb-whatsapp-send', function(event) {
                event.preventDefault();
                event.stopPropagation();

                var number = $(this).data('id');
                var pdfUrl = $(this).data('pdf');
                var status = 'N/A';
                var billDate = '<?php echo date('d-m-Y', strtotime($purchase_bill_data_group['date'])); ?>';
                var dueInfo = 'Invoice No: <?php echo isset($purchase_bill_data_group['invoice_no']) ? addslashes($purchase_bill_data_group['invoice_no']) : ''; ?>';

                $('#pb_whatsapp_number').val(number);
                $('#pb_whatsapp_mobile').val('');
                $('#pb_whatsapp_message').val('Dear Sir/Madam,\nPurchase Voucher ' + number + ' is shared with you.\nDate: ' + billDate + '\nStatus: ' + status + '\n' + dueInfo + '\nPDF: ' + pdfUrl + '\nPlease check and confirm.\nThanks.');

                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>SupplierController/get_purchase_bill_supplier_email',
                    data: { number: number },
                    dataType: 'json',
                    cache: false,
                    success: function(result) {
                        if (typeof result === 'string') {
                            try {
                                result = $.parseJSON(result);
                            } catch (e) {
                                result = null;
                            }
                        }

                        var data = result;
                        if ($.isArray(result) && result.length > 0) {
                            data = result[0];
                        }

                        var rawMobile = '';
                        if (data && typeof data === 'object') {
                            rawMobile = data.mobile || data.supplier_mobile || data.mobile_number || data.phone || '';
                        }

                        var mobile = String(rawMobile).replace(/[^0-9]/g, '');
                        var message = $('#pb_whatsapp_message').val() || '';

                        if (mobile && message.trim()) {
                            window.open('https://wa.me/' + mobile + '?text=' + encodeURIComponent(message), '_blank', 'noopener');
                            return;
                        }

                        $('.modal.in').modal('hide');
                        $('#purchaseBillWhatsappModal').modal('show');
                        $('#pb_whatsapp_mobile').val(mobile);
                        buildPurchaseBillWhatsAppUrl();
                    },
                    error: function() {
                        $('.modal.in').modal('hide');
                        $('#purchaseBillWhatsappModal').modal('show');
                        $('#pb_whatsapp_mobile').val('');
                        buildPurchaseBillWhatsAppUrl();
                    }
                });
            });

            $('#pb_whatsapp_mobile, #pb_whatsapp_message').on('input', function() {
                buildPurchaseBillWhatsAppUrl();
            });

            $('#pb_whatsapp_send_link').on('click', function(event) {
                if ($(this).attr('href') === '#') {
                    event.preventDefault();
                    alert('Please enter valid mobile number and message.');
                }
            });

        });
    </script>





