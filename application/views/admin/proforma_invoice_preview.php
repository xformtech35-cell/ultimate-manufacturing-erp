<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title> Invoice </title>
        <meta name="description" content="Invoice print page">
        <meta name="viewport" content="width=device-width">
        <link href="<?php echo base_url(); ?>printme/bootstrap.min.css" rel="stylesheet" >
        <link href="<?php echo base_url(); ?>printme/main.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>printme/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>printme/jquery-printme.js"></script>

        <script type="text/javascript">

            $(document).ready(function () {
                $("#print_btn").click(function () {
                    var print_btn = document.getElementById("print_btn");
                    var download_btn = document.getElementById("download_btn");
                    var hide_url = document.getElementById("hide_url");

                    print_btn.style.visibility = 'hidden';
                    download_btn.style.visibility = 'hidden';
                    hide_url.style.visibility = 'hidden';

                    window.print();
                    location.reload();
                    print_btn.style.visibility = 'visible';
                    download_btn.style.visibility = 'download_btn';
                });
            });
        </script>

    </head>
    <body>
        <div class="container main print shadows1" id="print">

            <div class="row">

                <div class="row" id="dataexample3">
                    <div class="col-md-9">

                    </div>
                    <div class="col-md-1">
                        <div class="panel-body hide">
                            <p>
                                <button id="print_btn" class="btn btn-primary btn-sm">Print</button>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel-body">
                            <p id="hide_url">
                                <?php $url = base_url() . 'Pdf/download_invoice/' . $invoice_data_group['invoice_number']; ?>
<!--                                <a href="<?php echo $url; ?>"><button id="download_btn" class="btn btn-primary btn-sm">Export As PDF</button></a>-->
                            </p>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-xs-6">
                        <img src="<?php echo base_url() . $settings['company_logo'] ?>" width="20%" height="20%">
                    </div>
                    <div class="col-xs-6">
                        <div class="contemporary-template__header__info">
                            <div class="wv-heading--title"><h3>PROFORMA INVOICE</h3></div>
                            <div class="wv-heading--subtitle"></div>
                            <span class="wv-text--strong" style="font-size: 1.2em;"><b><?php echo $settings['company_name']; ?></b></span><br>
                            <span class="wv-text--strong"><b>GST Number : </b><?php echo $settings['company_gst']; ?></span><br>
                            <span class="wv-text--strong"><b>PAN Number : </b><?php echo $settings['company_pan']; ?></span><br>
                            <span class="wv-text--strong"><b>Mobile Number : </b><?php echo $settings['mobile']; ?></span><br>
                            <span class="wv-text--strong"><b>Email ID : </b><?php echo $settings['email']; ?></span><br>
                            <span class="wv-text--strong"><b>State Code : </b><?php echo $settings['state_code']; ?></span><br>
                            <span class="wv-text--strong"><b>Address : </b><?php echo $settings['address']; ?></span>

                          

                        </div>
                    </div>
                </div>
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
                                    <?php
                                        // Shipping/Billing address fields may store JSON array. Normalize to a plain string.
                                        $invoice_shipping = $invoice_data_group['shipping_address'] ?? '';
                                        if (is_string($invoice_shipping)) {
                                            $t = trim($invoice_shipping);
                                            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                                                $decoded = json_decode($t, true);
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    if (is_array($decoded)) {
                                                        $invoice_shipping = $decoded[0] ?? '';
                                                    }
                                                }
                                            }
                                        }
                                    ?>

                                    <span class="wv-text--strong"><b>Company Name: </b>  <?php echo $invoice_data_group['company_name']; ?></span>
                                    
                                   <span class="wv-text--strong"> <?php echo $invoice_data_group['address']; ?></span><br>
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
                                    ?></span>
                                    
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

                <table class="table table-bordered" id="dynamic_field">  
                    <tr>
                        <th>Sr.No.</th>
                        <th>Description</th>
                        <th>HSN Code</th>
                        <th>Qty</th>
                        <th>TAX(%)</th>
                        <th class="gst">SGST</th>
                        <th class="gst">CGST</th>
                        <th class="igst">IGST</th>
                        <th>Price</th>
                        <th>Amount</th>
                    </tr>
                    <?php
                    $i = 1;
                    $sgst_total_amt = 0;
                    $igst_total_amt = 0;
                    foreach ($show_invoice as $key) {
                        ?>
                        <?php $sgst_total_amt = $sgst_total_amt + $key->cgst; ?>
                        <?php $igst_total_amt = $igst_total_amt + $key->igst; ?>
                        <tr> 
                            <td><span id="" class=""></span>
                                <?php echo $i; ?>
                            </td>
                            <td><span id="" class=""></span>
                                <b><?php echo $key->product_name; ?></b><input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control input-sm name_list product_name_auto" /><input type="hidden" class="form-control input-sm"   name="quotation_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->invoice_id; ?>"><br>
                                <?php echo $key->description; ?><input type="hidden" name="description[]" value="<?php echo $key->description; ?>" id="description<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" />
                            </td>

                            <td><span id="" class=""></span>
                                <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                            </td>

                            <td><span id="" class=""></span>
                                <?php echo number_format($key->quantity); ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" value="1" />
                            </td>

                            <td><span id="" class=""></span>
                                <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                            </td>

                            <?php if ($key->gst_type != 'I') { ?>
                                <td  class="gst"><span id="" class=""></span>
                                    <input type="hidden" name="gst" value="gst" id="gst">
                                    <?php echo $key->sgst; ?><input type="hidden" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" />

                                </td>
                                <td  class="gst"><span id="" class=""></span>
                                    <?php echo $key->cgst; ?><input type="hidden" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" />
                                </td>
                            <?php } else { ?>
                                <td  class="igst">
                                    <span id="" class="">
                                        <input type="hidden" name="igst" value="igst" id="igst">
                                        <?php echo $key->igst; ?><input type="hidden" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" />
                                    </span>
                                </td>
                            <?php } ?>
                            <td><span id="" class=""></span>
                                <?php echo number_format($key->price, 2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control input-sm name_list"/>

                            </td>
                            <td><span id="" class=""></span>
                                <?php echo number_format($key->amount, 2); ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>

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

                            </tr>
                            <tr>
                                <td colspan="8"  class="text-right">
                                    <label class="control-label"><b>CGST Amount : </b></label>
                                </td>

                                <td colspan="9"  class="text-left">
                                    <b><?php echo number_format($sgst_total_amt, 2); ?></b><br>
                                </td>

                            </tr>

                            <tr>
                                <td colspan="8"  class="text-right">
                                    <label class="control-label"><b>SGST Amount : </b></label>
                                </td>
                                <td colspan="9"  class="text-left">
                                    <b><?php echo number_format($sgst_total_amt, 2); ?></b><br>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="8"  class="text-right">
                                    <label class="control-label"><b>Grand Total (INR) : </b></label>
                                </td>
                                <td colspan="9"  class="text-left">
                                    <b>₹<?php echo number_format($invoice_data_group['total'], 2); ?></b> <br>
                                </td>
                            </tr>
                            <tr></tr>

                            <tr>
                                <td colspan="9"  class="text-right">
                                    <?php
                                    // include amount_convert
                                    require( APPPATH . '/third_party/amount_convert.php');
                                    ?>
                                    <b> Grand Total in Words:<?php echo number_to_word($invoice_data_group['total']); ?> Only.</b><br>
                                </td>
                            </tr>

                        <?php } else { ?>

                            <tr>
                                <td colspan="7"  class="text-right">
                                    <label class="control-label"><b>IGST Amount : </b></label>
                                </td>

                                <td colspan="8"  class="text-left">
                                    <b> <?php echo number_format($igst_total_amt, 2); ?></b><br>
                                </td>

                            </tr>
                            <tr>
                                <td colspan="7"  class="text-right">
                                    <label class="control-label"><b>Grand Total (INR) : </b></label>
                                </td>

                                <td colspan="8"  class="text-left">
                                    <b>₹<?php echo number_format($invoice_data_group['total'], 2); ?></b> <br>
                                </td>

                            </tr>
                            <tr>
                                <td colspan="8"  class="text-right">
                                    <?php
                                    // include amount_convert
                                    require( APPPATH . '/third_party/amount_convert.php');
                                    ?>
                                    <b> Grand Total in Words:<?php echo number_to_word($invoice_data_group['total']); ?> Only.</b><br>
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
            </div>  

            <div class="row hide">
                <div class="col-md-10">

                </div>
                <div class="col-md-2">
                    <label class="control-label"><b>Payment mode: </b><?php if ($invoice_data_group['payment_method'] == '1') { ?>
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
                        ?>
                    </label>
                </div>
            </div>

            <div class="row" id="dataexample4">
                <div class="col-sm-12">
                    <textarea style="overflow: auto; border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $settings['invoice_notes']; ?></textarea>
                </div>
            </div>
            <table class="table table-striped">
                <tr>
                    <td class="text-left">
                        <label for="inputEmail3" class="col-sm-3 control-label"><b>Receivers Signatory :</b></label>
                    </td>

                    <td colspan="8"  class="text-right">
                        <label for="inputEmail3" class="col-sm-9 control-label"><b> Authorized Signatory :</b></label>
                    </td>
                </tr>
            </table> 

            <div style="text-align: center;">This is Computer Generated Invoice</div><br>

        </div>
    </body>
    <script>

        $(document).ready(function () {

            var igst = $("#igst").val();
            //alert(igst);
            if (igst == "igst") {
                $(".gst").hide();
                $(".igst").show();
            } else {
                $(".gst").show();
                $(".igst").hide();
            }

        });
    </script>
</html>