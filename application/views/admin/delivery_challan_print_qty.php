<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Delivery Challan</title>
    <meta name="description" content="Invoice print page">
    <meta name="viewport" content="width=device-width">
    <style>
        table {
            width: 100%;
            margin: 2px;
            font-size: 9px;
            padding: 2px;
        }

        #dynamic_field {
            border-collapse: collapse;
            border-width: 1px;
        }

        .text-right {
            text-align: right;
        }

        table,
        td,
        th {
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
        }

       .empty-row td {
            height: 40px;
            /* Set a height for empty rows */
        }

        /* Hide top and bottom borders for item rows but keep vertical borders */
        .item-row td {
            border-top: none;
            /* Remove top border */
            border-bottom: none;
            /* Remove bottom border */
            border-left: 1px solid black;
            /* Keep vertical left border */
            border-right: 1px solid black;
            /* Keep vertical right border */
        } 
    </style>
</head>

<body>
    <?php
    foreach ($show_invoice as $key) {
        $colspan = "4";
        $colspan1 = "2";
        $colspan2 = "2";
        $colspan3 = "2";
        $colspan4 = "1";
        $colspan5 = "1";
        break;
    }
    ?>

    <center>
        <table border="1" cellpadding="2" id="dynamic_field">
            <caption style="font-size: 1.3em;">Delivery Challan</caption>
            <tr>
                <td style="width:20%" colspan="<?php echo $colspan1; ?>" valign="top">
                    <center><img src="<?php echo base_url() . $settings['company_logo'] ?>" width="30%" height="15%">
                    </center>
                </td>

                <td colspan="<?php echo $colspan2; ?>" valign="top">
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
                <td colspan="<?php echo $colspan3; ?>" valign="top">
                    <div class="form-group row ">
                        <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>BILL TO:</b></label>
                    </div>
                    <div class="contemporary-template__header__info">
                        <span class="wv-text--strong"><b>Company Name: </b>
                            <?php echo $invoice_data_group['company_name']; ?></span><br>
                        <span class="wv-text--strong"> <?php echo $invoice_data_group['address']; ?></span><br>
                        <span class="wv-text--strong"><b>State Code : </b>
                            <?php echo $invoice_data_group['state_code'] ?: "None Provided"; ?></span><br>
                        <span class="wv-text--strong"><b>GST Number : </b>
                            <?php echo $invoice_data_group['gst'] ?: "None Provided"; ?></span><br>
                        <span class="wv-text--strong"><b>PAN Number : </b>
                            <?php echo $invoice_data_group['pancard'] ?: "None Provided"; ?></span><br>
                        <span class="wv-text--strong"><b>KIND ATTN : </b>
                            <?php echo $invoice_data_group['fullname']; ?></span><br>
                        <span class="wv-text--strong"><b>Customer PO : </b>
                            <?php echo $invoice_data_group['customer_po'] ?: "None Provided"; ?></span><br>
                        <span class="wv-text--strong"><b>PO Date : </b>
                            <?php echo $invoice_data_group['po_date'] ?: "None Provided"; ?></span><br>
                        <span class="wv-text--strong"><b>Supplier Code : </b>
                            <?php echo $invoice_data_group['supplier_code'] ?: "None Provided"; ?></span>
                    </div>
                </td>

                <td colspan="<?php echo $colspan4; ?>" valign="top">
                    <div class="form-group row ">
                        <div class="col-sm-12">
                            <?php if (isset($invoice_data_group['shipping_address'])) { ?>
                                <label class="control-label"><b>SHIPPING TO: </b></label><br>
                            <?php } ?>
                            <?php echo $invoice_data_group['shipping_address']; ?>
                        </div>
                    </div>
                </td>

                <td colspan="<?php echo $colspan5; ?>" valign="top">
                    <div class="contemporary-template__header__info">
                        <label class="control-label"><b>DC DETAILS:</b></label>
                        <br>
                        <span class="wv-text--strong"><b>DC : </b>
                            <?php echo $invoice_data_group['invoice_number']; ?></span><br>
                        <span class="wv-text--strong"><b>DC Date : </b>
                            <?php echo date('d-m-Y', strtotime($invoice_data_group['invoice_date'])); ?></span><br>
                        <span class="wv-text--strong"><b>Delivery Note No : </b>
                            <?php echo $invoice_data_group['delivery_note_no']; ?></span><br>
                        <span class="wv-text--strong"><b>Delivery Date : </b>
                            <?php echo $invoice_data_group['delivery_date']; ?></span><br>
                        <span class="wv-text--strong"><b>Dispatch through : </b>
                            <?php echo $invoice_data_group['despatch_through']; ?></span><br>
                        <span class="wv-text--strong"><b>Vehicle No: </b>
                            <?php echo $invoice_data_group['vehicle_no']; ?></span><br>
                        <span class="wv-text--strong"><b>Payment mode: </b>
                            <?php echo $invoice_data_group['payment_method'] == '1' ? "By Cash" : ($invoice_data_group['payment_method'] == '2' ? "By Cheque" : ($invoice_data_group['payment_method'] == '3' ? "By NetBanking" : "None Provided")); ?></span>
                    </div>
                </td>
            </tr>

            <!-- Item Rows directly inside the main table -->
            <tr>
                <th style="width:5%">Sr.No.</th>
                <th style="width:50%">Description</th>
                <th style="width:10%">Qty</th>
                <th style="width:10%">Unit</th>
            </tr>

            <?php
            $i = 1;
            $itemsToShow = 14; // Set to show 18 items
            foreach ($show_invoice as $key) {
                ?>
                <tr class="item-row">
                    <td>
                        <center><?php echo $i; ?></center>
                    </td>
                    <td><b><?php echo $key->product_name; ?></b><br><?php echo $key->description; ?></td>
                    <td>
                        <center><?php echo number_format($key->quantity) ?></center>
                    </td>
                    <td>
                        <center><?php echo $key->unit; ?></center>
                    </td>
                </tr>
                <?php
                $i++;
            }

            // Add empty rows to make up to the desired number if necessary
            while ($i <= $itemsToShow) {
                ?>
                <tr class="empty-row item-row">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php
                $i++;
            }
            ?>
            <tr>
                <td colspan="<?php echo $colspan1 ?>" align="center" height="110" style="vertical-align:bottom;">
                    <label for="inputEmail3" class="col-sm-4 control-label"><b>Receivers Signatory: </b></label>
                </td>

                <td colspan="<?php echo $colspan2 ?>" align="center" height="110" style="vertical-align:bottom;">
                    <center><img src="<?php echo base_url() . $settings['company_stamp']; ?>" width="15%" height="15%">
                    </center>
                    <label for="inputEmail3" class="col-sm-4 control-label"><b>Authorised Signatory: </b></label>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>">
                    <center style="font-size: 10px">This is Computer Generated Invoice</center>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>