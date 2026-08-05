<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title> Credit/Debit Note </title>
        <meta name="description" content="Invocie print page">
        <meta name="viewport" content="width=device-width">
<!--        <link href="<?php echo base_url(); ?>printme/bootstrap.min.css" rel="stylesheet" >-->
<!--        <link href="<?php echo base_url(); ?>printme/main.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>printme/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>printme/jquery-printme.js"></script>-->
        <style>
            table{
                width: 100%;
                margin: 2px;
                font-size:9px; 
                padding: 2px;
            }

            #dynamic_field {
                border-collapse: collapse ;
                border-width: 1px;
            }

            .text-right{
                text-align: right;
            }
        </style>
    <body>
        <?php
        foreach ($show_invoice as $key) {

            if ($key->gst_type == 'I') {
                $colspan = "8";
                $colspan1 = "4";
                $colspan2 = "4";
                $colspan3 = "7";
                $colspan4 = "1";

                $colspan5 = "3";
                $colspan6 = "2";
                $colspan7 = "3";
            } else {
                $colspan = "9";
                $colspan1 = "4";
                $colspan2 = "5";
                $colspan3 = "8";
                $colspan4 = "1";

                $colspan5 = "3";
                $colspan6 = "3";
                $colspan7 = "3";
            }
            break;
        }
        ?>

    <center><table border="1" cellpadding="2" id="dynamic_field">  
            <caption style="font-size: 1.3em;">
            <?php if ($invoice_data_group['acc_type'] == "Cr" ) {  ?>
                    Credit Note
                <?php } else { ?>
                    Debit Note
                    <?php }   ?>

                    
        </caption>
            <tr>
                <td colspan="<?php echo $colspan1; ?>"  valign="top">
            <center><img src="<?php echo base_url() . $settings['company_logo'] ?>"></center>
            </td>

            <td colspan="<?php echo $colspan2; ?>"  valign="top">

                <b style="font-size: 1.8em;"><?php echo $settings['company_name']; ?></b><br>
                <b>Address : </b><?php echo $settings['address']; ?><br>

                <b>State Code : </b><?php echo $settings['state_code'] . " "; ?><br>
                <b>GST Number : </b><?php echo strtoupper($settings['company_gst']) . " "; ?><br>
                <b>PAN Number : </b><?php echo strtoupper($settings['company_pan']) . " "; ?><br>
                <b>Mobile Number : </b><?php echo $settings['mobile'] . " "; ?><br>
                <b>Email ID : </b><?php echo $settings['email']; ?><br>
            </td>
            </tr>

            <tr>
                <td colspan="<?php echo $colspan5; ?>"  valign="top">
                    <div class="form-group row ">
                        <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>BILL TO:</b></label>
                    </div>

                    <div class="contemporary-template__header__info">
                        <div class="wv-heading--subtitle"></div>
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

                </td>

                <td colspan="<?php echo $colspan6; ?>"  valign="top">

                    <div class="form-group row ">
                        <div class="col-sm-12">
<?php if (isset($invoice_data_group['shipping_address'])) { ?><label class="control-label"><b>SHIPPING TO: </b></label> <br> <?php } ?>
                            <?php echo $invoice_data_group['shipping_address']; ?>
                        </div>
                    </div>
                </td>


                <td colspan="<?php echo $colspan7; ?>"  valign="top">
                    <div class="contemporary-template__header__info">
                        <label class="control-label"><b>INVOICE DETAILS:</b></label> 
                        <br>
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
                </td>

            </tr>

<?php
$i = 1;
foreach ($show_invoice as $key) {
    ?>
                <tr>
                    <th nowrap>Sr.No.</th>
                    <th>Description</th>
                    <th>HSN Code</th>
                    <th nowrap width="10%">Qty Nos/Kg</th>
                    <th nowrap width="10%">GST %</th>
    <?php if ($key->gst_type != 'I') { ?>
                        <th>SGST</th>
                        <th>CGST</th>
    <?php } else { ?>
                        <th>IGST</th>
                    <?php } ?>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>

    <?php
    $i++;
    break;
}
?>

            <?php
            $i = 1;
            $sgst_total_amt = 0;
            $igst_total_amt = 0;
            $amt = 0;
            foreach ($show_invoice as $key) {
                ?>
                <?php $sgst_total_amt = $sgst_total_amt + $key->cgst; ?>
                <?php $igst_total_amt = $igst_total_amt + $key->igst; ?>

                <tr> 
                    <td><span id="" class=""></span>
    <?php echo $i; ?>
                    </td>

                    <td width="30%"><span id="" class=""></span>
                        <b><?php echo $key->product_name; ?></b><br>
    <?php echo $key->description; ?>
                    </td>

                    <td><span id="" class=""></span>
    <?php echo $key->hsn_code; ?>
                    </td>

                    <td><span id="" class=""></span>
    <?php echo number_format($key->quantity); ?>
                    </td>

                    <td><span id="" class=""></span>
    <?php echo $key->gst; ?>
                    </td>

    <?php if ($key->gst_type != 'I') { ?>
                        <td><span id="" class=""></span>

        <?php echo number_format($key->sgst, 2); ?>
                        </td>
                        <td><span id="" class=""></span>
        <?php echo number_format($key->cgst, 2); ?>
                        </td>
                        <?php } else { ?>
                        <td><span id="" class=""></span>

        <?php echo number_format($key->igst, 2); ?>
                        </td>
                        <?php } ?>

                    <td><span id="" class=""></span>
    <?php echo number_format($key->price, 2); ?>
                    </td>
                    <td  class="text-right"><span id="" class=""></span>
    <?php
    echo number_format($key->amount, 2);
    $amt += $key->amount;
    ?>
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

                    <tr class="">
                        <td colspan="<?php echo $colspan3 ?>"  class="text-right">
                            <b>Total Before Tax: </b>
                        </td>
                        <td colspan="<?php echo $colspan4 ?>"  class="text-left">
                            <?php echo number_format($amt, 2); ?>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="<?php echo $colspan3 ?>"  class="text-right">
                            <label class="control-label"><b>CGST Amount : </b></label>
                        </td>

                        <td colspan="<?php echo $colspan4 ?>"  class="text-left">
                            <?php echo number_format($sgst_total_amt, 2); ?><br>
                        </td>

                    </tr>

                    <tr>
                        <td colspan="<?php echo $colspan3 ?>"  class="text-right">
                            <label class="control-label"><b>SGST Amount : </b></label>
                        </td>
                        <td colspan="<?php echo $colspan4 ?>"  class="text-left">
                            <?php echo number_format($sgst_total_amt, 2); ?><br>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="<?php echo $colspan3 ?>"  class="text-right">
                            <label class="control-label"><b>GST Amount : </b></label>
                        </td>
                        <td colspan="<?php echo $colspan4 ?>"  class="text-left">
                            <b><?php echo number_format($sgst_total_amt * 2, 2); ?></b><br>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="<?php echo $colspan3 ?>"  class="text-right">
                            <label class="control-label"><b>Grand Total (₹) : </b></label>
                        </td>
                        <td colspan="<?php echo $colspan4 ?>"  class="text-left">
                            <?php echo number_format($invoice_data_group['total'], 2); ?><br>
                        </td>
                    </tr>
                    <tr></tr>

                    <tr>
                        <td colspan="<?php echo $colspan ?>"  class="text-right">
                            <?php
                            // include amount_convert
                            require( APPPATH . '/third_party/amount_convert.php');
                            ?>
                            <label class="control-label"><b>Grand Total in Words : <?php echo number_to_word($invoice_data_group['total']); ?> Only</label>
                        </td>
                    </tr>

                <?php } else { ?>


                    <tr class="">
                        <td colspan="<?php echo $colspan3 ?>"  class="text-right">
                            <b>Total Before Tax: </b>
                        </td>
                        <td colspan="<?php echo $colspan4 ?>"  class="text-left">
                            <?php echo number_format($amt, 2); ?>
                        </td>

                    </tr>

                    <tr>
                        <td colspan="<?php echo $colspan3 ?>"  class="text-right">
                            <label class="control-label"><b>IGST Amount : </b></label>
                        </td>

                        <td colspan="<?php echo $colspan4 ?>"  class="text-left">
                            <?php echo number_format($igst_total_amt, 2); ?><br>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="<?php echo $colspan3 ?>"  class="text-right">
                            <label class="control-label"><b>Grand Total (₹) : </b></label>
                        </td>

                        <td colspan="<?php echo $colspan4 ?>"  class="text-left">
                            <b>Rs. <?php echo number_format($invoice_data_group['total'], 2); ?></b> <br>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="<?php echo $colspan ?>"  class="text-right">
                            <?php
                            // include amount_convert
                            require( APPPATH . '/third_party/amount_convert.php');
                            ?>
                            <label class="control-label"><b>Grand Total in Words : <?php echo number_to_word($invoice_data_group['total']); ?> Only</label>

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

            <tr>
                <th colspan="<?php echo $colspan ?>" style="text-align: left">Bank Details:</th>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan ?>">                   
                    <?php echo $settings['invoice_notes']; ?>
                </td>
            </tr>

            <tr>
                <th colspan="<?php echo $colspan ?>" style="text-align: left">Note:</th>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan ?>">                   
                    <?php echo $settings['invoice_memo']; ?>
                </td>
            </tr>     

            <tr>
                <td colspan="<?php echo $colspan1 ?>" align="center" height="110" style="vertical-align:bottom;">
                    <label for="inputEmail3" class="col-sm-4 control-label"><b>Receivers Signatory : </b></label>
                </td>

                <td colspan="<?php echo $colspan2 ?>"  align="center" height="110" style="vertical-align:bottom;">
            <center><img src="<?php echo base_url() . $settings['company_stamp']; ?>" width="15%" height="15%"></center>
            <label for="inputEmail3" class="col-sm-4 control-label"><b> Authorised Signatory : </b></label>
            </td>
            </tr>

            <tr>
                <td colspan="<?php echo $colspan ?>"><center style="font-size: 12px"><?php echo $settings['address']; ?></center></td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan ?>"><center style="font-size: 10px">This is Computer Generated Invoice</center></td>
            </tr>
        </table> 

    </body>
</html>

