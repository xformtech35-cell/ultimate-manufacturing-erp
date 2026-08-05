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
    .invoice-logo-left {
        text-align: left;
    }

    .invoice-logo-left img {
        display: inline-block;
        margin-top: 26px;
    }

    .invoice-grid-table {
        border-collapse: collapse;
        border: 2px solid #2f2f2f;
        background-color: #fff;
    }

    .invoice-grid-table > tbody > tr > th,
    .invoice-grid-table > tbody > tr > td,
    .invoice-grid-table > thead > tr > th,
    .invoice-grid-table > thead > tr > td {
        border: 1.5px solid #2f2f2f !important;
        color: #111;
        vertical-align: middle;
    }

    .invoice-grid-table > tbody > tr > th,
    .invoice-grid-table > thead > tr > th {
        background-color: #f3f3f3;
    }

    @media print {
        .row[style*="padding:2%"] {
            display: none !important;
        }
    }
</style>
<?php if ($this->input->get('duplicate_invoice') == '1') { ?>
<script type="text/javascript">
    alert("Same name invoice present, can't create!");
</script>
<?php } ?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'ProformaInvoiceController/index/' ?>"> GST Proforma Invoice</a></li>
                    <li class="active">GST Proforma Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <label for="inputEmail3" name="invoice_number" id="invoice_number" class="col-sm-12 control-label"><h2>GST Proforma Invoice:<?php echo $invoice_data_group['invoice_number']; ?></h2></label>

                <div class="row" style="padding:2%;">
                    <div class="col-sm-12" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">
                            <a href="<?php echo base_url(); ?>ProformaInvoiceController/edit_proforma_invoice_details/<?php echo $invoice_data_group['invoice_number']; ?>" class="btn btn-primary edit-stock-qty" role="button">Edit</a>
<div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    More <b class="caret"></b>
                                </button>
                                <input type="hidden" class="form-control input-sm" name="invoice_number" id="invoice_number" required="" value="<?php echo $invoice_data_group['invoice_number']; ?>">
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li class="hide"><a class="dropdown-item" href="<?php echo base_url(); ?>LoginController/get_settings/">Edit Business Information</a></li>
                                    <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/download_proforma_invoice/<?php echo $invoice_data_group['invoice_number']; ?>">Export As PDF</a></li>
                                </ul>
                            </div>
                            <form method="POST" action="<?php echo base_url(); ?>ProformaInvoiceController/sgst_to_igst" style="margin:0;">
                                    
                                    <input type="hidden" name="invoice_number" 
                                        value="<?php echo $invoice_data_group['invoice_number']; ?>">

                                    <?php 
                                    $gst_type = '';
                                    foreach ($show_invoice as $key) { 
                                        $gst_type = $key->gst_type; 
                                        break; 
                                    } 
                                    ?>

                                    <?php if ($gst_type != 'I') { ?>
                                        <button class="btn btn-primary" type="submit">
                                            SGST → IGST
                                        </button>
                                    <?php } else { ?>
                                        <button class="btn btn-primary" type="submit">
                                            IGST → SGST
                                        </button>
                                    <?php } ?>
</form>
                            <button class="btn btn-primary" onclick="window.print();" style="margin:0;">Print</button>
                            <form method="POST" action="<?php echo base_url(); ?>ProformaInvoiceController/convert_to_invoice/<?php echo $invoice_data_group['id']; ?>" style="margin:0;">
                                <input type="hidden" class="form-control input-sm" name="id" id="id" required="" value="<?php echo $invoice_data_group['id']; ?>">
                                <?php
                                $next_invoice_number = !empty($next_invoice_name)
                                    ? $next_invoice_name
                                    : ((date('m') <= 3)
                                        ? 'INV/' . sprintf("%04d", $inv_id + 1) . '/' . ((date('y') - 1) . '-' . date('y'))
                                        : 'INV/' . sprintf("%04d", $inv_id + 1) . '/' . (date('y') . '-' . (date('y') + 1)));
                                ?>
                                <input type="hidden" class="form-control input-sm" name="invoice_number" id="invoice_number" required="" value="<?php echo $next_invoice_number; ?>" style="display:inline-block; width:220px; margin-right:10px; text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
<button id="convertToInvoice" class="btn btn-primary" role="button" type="submit">Convert to Invoice</button>
                            </form>
                        </div>
                        

<div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">
                            <a href="javascript:void(0);" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='<?php echo base_url(); ?>ProformaInvoiceController/index'; }" class="btn btn-primary" role="button"><i class="fa fa-close"></i> Close</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->

             <div class="shadows1">
                    <div class="row">
                        <section>
                            <div class="col-xs-6">
                                <div class="invoice-logo-left">
                                    <img src="<?php echo base_url() . $settings['company_logo'] ?>" width="70%" height="35%" style="margin: 1px 5px 0px 0px;" alt="Company Logo">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="contemporary-template__header__info">
                                    <div class="wv-heading--title"><h3>Proforma Invoice</h3></div>
                                    <div class="wv-heading--subtitle"></div>
                                    <span class="wv-text--strong" style="font-size: 1.2em;"><?php echo $settings['company_name']; ?></span><br>
                                    <span class="wv-text--strong"><b>GST Number : </b> <?php echo $settings['company_gst']; ?></span><br>
                                    <span class="wv-text--strong"><b>PAN Number : </b> <?php echo $settings['company_pan']; ?></span><br>
                                    <span class="wv-text--strong"><b>Mobile Number : </b> <?php echo $settings['mobile']; ?></span><br>
                                    <span class="wv-text--strong"><b>Email ID : </b> <?php echo $settings['email']; ?></span><br>
                                    <span class="wv-text--strong"><b>State Code : </b> <?php echo $settings['state_code']; ?></span><br>
                                    <span class="wv-text--strong"><b>Address : </b> <?php echo $settings['address']; ?></span>
                                    
                                </div>
                            </div>
                        </section>

                    </div>
                    <hr>
                    <div class="row">

                        <div class="col-xs-4">

                            <div class="form-group row ">
                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>BILL TO:</b></label>
                            </div>
                            
                            
                            
                            
                            <div class="contemporary-template__header__info">
<!--                                    <div class="wv-heading--title"><h3>TAX INVOICE</h3></div>-->
                                    <div class="wv-heading--subtitle"></div>
                                    <span class="wv-text--strong"><b>Company Name: </b>  <?php echo $invoice_data_group['company_name']; ?></span><br>
                                    
                                   <span class="wv-text--strong"><b>Address: </b> <?php echo $invoice_data_group['address']; ?></span><br>
                                    <span class="wv-text--strong"><b>State Code : </b> <?php if ($invoice_data_group['state_code']) { ?>
                                        <?php echo $invoice_data_group['state_code']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    <span class="wv-text--strong"><b>GST Number : </b> <?php if ($invoice_data_group['gst']) { ?>
                                        <?php echo $invoice_data_group['gst']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    <span class="wv-text--strong"><b>PAN Number : </b> <?php if ($invoice_data_group['pancard']) { ?>
                                        <?php echo $invoice_data_group['pancard']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    <span class="wv-text--strong"><b>Customer Name : </b> <?php echo $invoice_data_group['fullname']; ?></span><br>
                                     <span class="wv-text--strong"><b>Customer Code : </b> <?php echo $invoice_data_group['c_code']; ?></span><br>
                                    <span class="wv-text--strong"><b>Customer PO : </b> <?php if ($invoice_data_group['customer_po']) { ?>
                                        <?php echo $invoice_data_group['customer_po']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    <span class="wv-text--strong"><b>PO Date : </b> <?php if ($invoice_data_group['po_date']) { ?>
                                        <?php echo $invoice_data_group['po_date']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    
                                      
                                    
                                </div>
                            

                        </div>

                        <div class="col-xs-4">

                            <div class="form-group row ">
                                <div class="col-sm-12">
                                   <?php if(isset($invoice_data_group['shipping_address'])) { ?><label class="control-label"><b>SHIPPING TO: </b></label> <br> <?php } ?>
                                         <?php echo $invoice_data_group['shipping_address']; ?>
                                </div>
                            </div>

                            
                        </div>
                        
                        
                         <div class="col-xs-4">    
                            
                            <div class="contemporary-template__header__info">
                                 <label class="control-label"><b>INVOICE DETAILS:</b></label> 
                                 <br>
<!--                                    <span class="wv-text--strong" style="font-size: 1.2em;"><b><?php echo $settings['company_name']; ?></b></span><br>-->
                                    <span class="wv-text--strong"><b>Invoice Number : </b> <?php echo $invoice_data_group['invoice_number']; ?></span><br>
                                 <span class="wv-text--strong"><b>Invoice Date : </b> <?php echo date('d-m-Y', strtotime($invoice_data_group['invoice_date'])); ?></span><br>
                                    <span class="wv-text--strong"><b>Delivery Note No : </b> <?php echo $invoice_data_group['delivery_note_no']; ?></span><br>
                                    <span class="wv-text--strong"><b>Delivery Date : </b> <?php echo $invoice_data_group['delivery_date']; ?></span><br>
                                    <span class="wv-text--strong"><b>Dispatch through : </b> <?php echo $invoice_data_group['despatch_through']; ?></span><br>
                                    <span class="wv-text--strong"><b>Vehicle No: </b> <?php echo $invoice_data_group['vehicle_no']; ?></span><br>
                                    <span class="wv-text--strong"><b>Payment mode: </b> <?php if ($invoice_data_group['payment_method'] == '1') { ?>
                                        <?php echo "By Cash"; ?>
                                    <?php } else if ($invoice_data_group['payment_method'] == '2') { ?>
                                        <?php
                                        echo "By Cheque";
                                    } else if ($invoice_data_group['payment_method'] == '3') {
                                        ?>
                                        <?php
                                        echo "By NetBanking";
                                    } else {
                                        ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span>
                                </div>
                            

                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">  

                        <table class="table table-bordered invoice-grid-table" id="dynamic_field">  
                            <tr >
                                <th class="text-center">Sr.No.</th>
                                <th>Description</th>
                                <th class="text-center">HSN Code</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">GST(%)</th>
                                <th class="gst text-center">SGST</th>
                                <th class="gst text-center">CGST</th>
                                <th class="igst text-center">IGST</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Discount(%)</th>
                                <th class="text-center">Amount</th>
                            </tr>
                            <?php
$i = 1;
                            $sgst_total_amt = 0;
                            $igst_total_amt = 0;
                            $amt = 0;
                            $total_qty = 0;
                            $total_discount = 0.0;
                            foreach ($show_invoice as $key) {
                                ?>
                                <?php $sgst_total_amt = $sgst_total_amt + $key->cgst; ?>
                                <?php $igst_total_amt = $igst_total_amt + $key->igst; ?>
<?php 
                                $total_qty += $key->quantity;
                                $discount_amount = ($key->price * ($key->discount / 100.0)) * $key->quantity;
                                $total_discount += $discount_amount;
                            ?>
                                <tr> 
                                    <td style="text-align: center;"><span id="" class=""></span>
                                        <?php echo $i; ?>
                                    </td>

                                    <td><span id="" class=""></span>
                                        <b><?php echo $key->product_name. " - " .  $key->item_name; ?></b>
                                        <?php echo $key->description; ?>
                                              <input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control input-sm name_list product_name_auto" />
                                    </td>

                                    <td style="text-align: center;"><span id="" class=""></span>
                                        <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                                    </td>

                                    <td style="text-align: center;"><span id="" class=""></span>
                                        <?php echo indian_number_format($key->quantity, 2); ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" value="1" />
                                    </td>
 
                                    <td style="text-align: center;" ><span id="" class=""></span>
                                        <?php echo $key->unit; ?><input type="hidden" name="unit[]" value="<?php echo $key->unit; ?>" id="unit<?php echo $i; ?>" class="form-control input-sm name_list" />
                                    </td>

                                    <td style="text-align: center;"><span id="" class=""></span>
                                        <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                    </td>


                                    <?php if ($key->gst_type != 'I') { ?>
                                        <td class="gst" style="text-align: center;"><span id="" class=""></span>
                                            <input type="hidden" name="gst" value="gst" id="gst">
                                            <?php echo indian_number_format($key->sgst, 2); ?><input type="hidden" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" />
                                        </td>

                                        <td class="gst" style="text-align: center;"><span id="" class=""></span>
                                            <?php echo indian_number_format($key->cgst, 2); ?><input type="hidden" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" />
                                        </td>
                                    <?php } else { ?>

                                        <td class="igst" style="text-align: center;">
                                            <span id="" class=""></span><?php echo indian_number_format($key->igst, 2); ?>
                                            <input type="hidden" name="igst" value="igst" id="igst">
                                            <input type="hidden" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" />
                                        </td>
                                    <?php } ?>



                                    <td style="text-align: center;"><span id="" class="" ></span>
                                        <?php echo indian_number_format($key->price, 2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control input-sm name_list"/>

                                    </td>


                                   <td style="text-align: center;"><span id="" class="" ></span>
                                        <?php echo indian_number_format($key->discount, 2); ?><input type="hidden" name="discount[]" id="discount<?php echo $i; ?>" class="form-control input-sm name_list" value="<?php echo $key->discount; ?>"/>

                                    </td>



                                    <td><span id="" style="text-align: right; display: block;"><?php echo indian_number_format($key->amount, 2); ?></span>
                                        <?php 
                                        $amt += $key->amount;
                                        $amt
                                        ?>
                                        <input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list text-right amount_auto" value="<?php echo $key->amount; ?>"/>

                                    </td>


                                </tr>  

                                <?php
                                $i++;
                            }
                            ?>
                            <?php
                            $i = 1;
                            foreach ($show_invoice as $key) {
                                ?>
                                <?php if ($key->gst_type != 'I') { ?>

    <tr>
                                        <td colspan="10" class="text-right">
                                            <b>Total Qty:</b>
                                        </td>
                                        <td class="text-right">
                                            <b><?php echo indian_number_format($total_qty, 2); ?></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="10" class="text-right">
                                            <b>Total Discount:</b>
                                        </td>
                                        <td class="text-right">
                                            <b>₹<?php echo indian_number_format($total_discount, 2); ?></b>
                                        </td>
                                    </tr>

                                           <tr class="">
                                            <td colspan="10"  class="text-right">
                                                Total Before Tax: 
                                            </td>
                                            <td colspan="1"  class="text-right">
                                                ₹ <?php echo $amt; ?>
                                            </td>
                                            
                             </tr> 
                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>CGST Amount : </b></label>
                                        </td>

                                        <td colspan="1"  class="text-right">
                                            <b><?php echo indian_number_format($sgst_total_amt, 2); ?></b><br>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>SGST Amount : </b></label>
                                        </td>
                                        <td colspan="1"  class="text-right">
                                            <b><?php echo indian_number_format($sgst_total_amt, 2); ?></b><br>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                                        </td>
                                        <td colspan="1"  class="text-right">
                                            <b>₹<?php echo indian_number_format($invoice_data_group['total'], 2); ?></b> <br>
                                        </td>
                                    </tr>
                                    
                                      <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>Balance (INR) : </b></label>
                                        </td>
                                        <td colspan="1"  class="text-right">
                                            <b>₹<?php echo indian_number_format($invoice_data_group['balance'], 2); ?></b> <br>
                                        </td>
                                    </tr>
                                    <tr></tr>

                                    <tr>
                                        <td colspan="11"  class="text-right">
                                            <?php
                                            // include amount_convert
                                            require_once( APPPATH . '/third_party/amount_convert.php');
                                            ?>
                                            <b>Grand Total in Words:<?php echo number_to_word($invoice_data_group['balance']); ?> Only.</b><br>
                                        </td>
                                    </tr>

                                <?php } else { ?>
                                    
                                     <tr>
                                        <td colspan="9" class="text-right">
                                            <b>Total Qty:</b>
                                        </td>
                                        <td class="text-right">
                                            <b><?php echo indian_number_format($total_qty, 2); ?></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="9" class="text-right">
                                            <b>Total Discount:</b>
                                        </td>
                                        <td class="text-right">
                                            <b>₹<?php echo indian_number_format($total_discount, 2); ?></b>
                                        </td>
                                    </tr>

                                     <tr class="">
                                            <td colspan="9"  class="text-right">
                                                Total Before Tax: 
                                            </td>
                                            <td colspan="1"  class="text-right">
                                                ₹ <?php echo $amt; ?>
                                            </td>
                                            
                             </tr> 

                                    <tr>
                                        <td colspan="9"  class="text-right">
                                            <label class="control-label"><b>IGST Amount : </b></label>
                                        </td>

                                        <td colspan="1"  class="text-right">
                                            <b> <?php echo indian_number_format($igst_total_amt, 2); ?></b><br>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="9"  class="text-right">
                                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                                        </td>

                                        <td colspan="1"  class="text-right">
                                            <b>₹<?php echo indian_number_format($invoice_data_group['total'], 2); ?></b> <br>
                                        </td>

                                    </tr>
                                    
                                                        <tr>
                                        <td colspan="9"  class="text-right">
                                            <label class="control-label"><b>Balance (INR) : </b></label>
                                        </td>

                                        <td colspan="1"  class="text-right">
                                            <b>₹<?php echo indian_number_format($invoice_data_group['balance'], 2); ?></b> <br>
                                        </td>

                                    </tr>
                                    
                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <?php
                                            // include amount_convert
                                            require_once( APPPATH . '/third_party/amount_convert.php');
                                            ?>
                                            <b>Grand Total in Words:<?php echo number_to_word($invoice_data_group['balance']); ?> Only.</b><br>
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

                     
                        
                        
                        
                        

                        <?php if (!empty($invoice_data_group['proforma_terms_and_conditions'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Terms &amp; Conditions:</b></label>
                            <div><?php echo $invoice_data_group['proforma_terms_and_conditions']; ?></div>
                        </div>
                        <?php } ?>

                        <?php if (!empty($invoice_data_group['proforma_payment_terms'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Payment Terms:</b></label>
                            <div><?php echo $invoice_data_group['proforma_payment_terms']; ?></div>
                        </div>
                        <?php } ?>

                        <?php if (!empty($invoice_data_group['proforma_process_schedule'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Process Schedule:</b></label>
                            <div><?php echo $invoice_data_group['proforma_process_schedule']; ?></div>
                        </div>
                        <?php } ?>

                        <?php if (!empty($invoice_data_group['proforma_taxes'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Taxes:</b></label>
                            <div><?php echo $invoice_data_group['proforma_taxes']; ?></div>
                        </div>
                        <?php } ?>

                        <?php if (!empty($invoice_data_group['proforma_exclusions'])) { ?>
                        <div class="col-sm-12" style="margin-top:10px;">
                            <label class="control-label"><b>Exclusions:</b></label>
                            <div><?php echo $invoice_data_group['proforma_exclusions']; ?></div>
                        </div>
                        <?php } ?>

                        <div class="form-group row ">
                            <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Signatory :</b></label>
                            <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Signatory :</b></label>
                        </div>

                        <div style="text-align: center;">This is Computer Generated Invoice</div><br>

                    </div>  
                </div>


            </section>
            <!-- /.content -->
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
    <script>

        $(document).ready(function () {

            var igst = $("#igst").val();
            //alert(igst);
            if (igst == "igst") {
                $(".gst").hide();
                $(".igst").show();

            } else {
                $(".gst").show();
//                 $(".gst"). css('padding','10% 0% 0% 0%')
                $(".igst").hide();
            }

        });

    </script>
