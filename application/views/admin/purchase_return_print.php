<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Returnable Challan</title>
    <meta name="description" content="Purchase Return print page">
    <meta name="viewport" content="width=device-width">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            color: #000;
            font-size: 9px;
            line-height: 1.3;
            padding: 10px;
        }

        .purchase-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Company Header - Separate Table */
        .company-header {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #000;
            font-size: 9px;
            border-bottom: none;
        }

        .company-header td {
            border: 0.5px solid #000;
            padding: 6px 5px;
            vertical-align: top;
            border-bottom: none;
        }

        /* Vendor & Return Details Section */
        .details-section {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #000;
            border-top: none;
            font-size: 9px;
        }

        .details-section td {
            border: 0.5px solid #000;
            padding: 6px 5px;
            vertical-align: top;
        }

        /* Main Items Table */
        #dynamic_field {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #000;
            border-top: none;
            font-size: 9px;
            margin-top: -0.5px;
        }

        #dynamic_field th,
        #dynamic_field td {
            border: 0.5px solid #000;
            padding: 5px 3px;
            vertical-align: top;
        }

        #dynamic_field tr {
            border-top: none;
            border-bottom: none;
        }

        /* Items header styling */
        .items-header th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
            padding: 6px 3px;
        }

        /* Column width classes */
        .col-sr { width: 5%; text-align: center; }
        .col-desc { width: 30%; text-align: left; }
        .col-qty { width: 8%; text-align: center; }
        .col-hsn { width: 8%; text-align: center; }
        .col-gst { width: 6%; text-align: center; }
        .col-tax { width: 12%; text-align: center; }
        .col-cgst { width: 10%; text-align: center; }
        .col-sgst { width: 10%; text-align: center; }
        .col-price { width: 10%; text-align: right; }
        .col-discount { width: 8%; text-align: right; }
        .col-amount { width: 12%; text-align: right; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
        }

        .company-logo {
            max-width: 120px;
            max-height: 80px;
            width: auto;
            height: auto;
        }

        /* Label styling */
        .label {
            font-weight: bold;
            color: #444;
            min-width: 80px;
            display: inline-block;
        }

        .label-sm {
            font-weight: bold;
            color: #444;
            min-width: 60px;
            display: inline-block;
        }

        /* Section headings */
        .section-heading {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 4px;
            color: #003366;
            padding-bottom: 2px;
        }

        /* Item rows styling */
        .item-row td {
            border-top: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
            height: 33px;
        }

        /* First item row - keep top border */
        .item-row:first-child td {
            border-top: 0.5px solid #000;
        }

        /* Last item row - keep bottom border */
        .last-item-row td {
            border-bottom: 0.5px solid #000;
            height: 33px;
        }

        .empty-row td {
            border-top: none;
            border-bottom: none;
            height: 33px;
        }

        /* Total section */
        .total-section td {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .grand-total td {
            font-weight: bold;
            font-size: 10px;
            background: #f0f0f0;
        }

        /* Amount in words */
        .amount-words {
            font-weight: bold;
            padding: 6px 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Signature section */
        .signature-section {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #000;
            border-top: none;
            font-size: 9px;
        }

        .signature-section td {
            border: 0.5px solid #000;
            padding: 10px 5px;
            vertical-align: bottom;
            text-align: center;
            height: 70px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 8px;
            border: 0.5px solid #000;
            border-top: none;
            font-size: 8px;
        }

        caption {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            caption-side: top;
            color: #003366;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <div class="purchase-container">
        <?php
        $purchase_return_data_group = isset($purchase_return_data_group) && is_array($purchase_return_data_group) ? $purchase_return_data_group : [];
        $show_purchase_return = isset($show_purchase_return) && is_array($show_purchase_return) ? $show_purchase_return : [];
        $settings = isset($settings) && is_array($settings) ? $settings : [];

        $purchase_return_data_group = array_merge([
            'company_name' => '',
            's_code' => 'N/A',
            'address' => '',
            'mobile' => '',
            'email' => '',
            'state_code' => '',
            'gst' => '',
            'pancard' => '',
            'number' => '',
            'date' => '',
            'delivery_date' => '',
            'ref_no' => '',
            'total' => 0,
        ], $purchase_return_data_group);

        $settings = array_merge([
            'company_logo' => '',
            'company_name' => '',
            'company_gst' => '',
            'company_pan' => '',
            'mobile' => '',
            'email' => '',
            'address' => '',
        ], $settings);

        $return_display_date = !empty($purchase_return_data_group['date']) ? date('d-m-Y', strtotime($purchase_return_data_group['date'])) : '';

        // Initialize variables
        $gst_type_row = 'S';
        $colspan = '10';
        $colspan1 = '5';
        $colspan2 = '5';
        $colspan3 = '9';
        $colspan4 = '1';
        $total_items = !empty($show_purchase_return) ? count($show_purchase_return) : 0;
        
        // Get GST type from first record if available
        if (!empty($show_purchase_return)) {
            foreach ($show_purchase_return as $key) {
                if ($key->gst_type == 'I') {
                    $colspan = "9";
                    $colspan1 = "5";
                    $colspan2 = "4";
                    $colspan3 = "8";
                    $colspan4 = "1";
                    $gst_type_row = $key->gst_type;
                } else {
                    $colspan = "10";
                    $colspan1 = "5";
                    $colspan2 = "5";
                    $colspan3 = "9";
                    $colspan4 = "1";
                    $gst_type_row = $key->gst_type;
                }
                break;
            }
        }
        
        // Calculate blank rows needed
        $target_total_rows = 16;
        $items_count = !empty($show_purchase_return) ? count($show_purchase_return) : 0;
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        ?>

        <!-- Company Header - Separate Table -->
        <caption>PURCHASE RETURN</caption>
        <table class="company-header">
            <tr>
                <td width="25%" align="center" valign="middle">
                    <img class="company-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo" >
                </td>
                <td width="75%" valign="top">
                    <div class="company-name"><?php echo $settings['company_name']; ?></div>
                    <div><span class="label">GST Number :</span> <?php echo strtoupper($settings['company_gst']); ?></div>
                    <div><span class="label">PAN Number :</span> <?php echo strtoupper($settings['company_pan']); ?></div>
                    <div><span class="label">Mobile :</span> <?php echo $settings['mobile']; ?></div>
                    <div><span class="label">Email :</span> <?php echo $settings['email']; ?></div>
                    <div><span class="label">Address :</span> <?php echo $settings['address']; ?></div>
                </td>
            </tr>
        </table>

        <!-- Vendor & Return Details Section -->
        <table class="details-section">
            <tr>
                <!-- Vendor Details (Left) -->
                <td width="50%" valign="top">
                    <div class="section-heading">VENDOR DETAILS</div>
                    <div><span class="label-sm">Vendor Name :</span> <?php echo $purchase_return_data_group['company_name']; ?></div>
                    <div><span class="label-sm">Vendor Code :</span> <?php echo isset($purchase_return_data_group['s_code']) && $purchase_return_data_group['s_code'] ? $purchase_return_data_group['s_code'] : 'N/A'; ?></div>
                    <div><span class="label-sm">Address :</span> <?php echo $purchase_return_data_group['address']; ?></div>
                    <div><span class="label-sm">Mobile :</span> <?php echo isset($purchase_return_data_group['mobile']) && $purchase_return_data_group['mobile'] ? $purchase_return_data_group['mobile'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">Email :</span> <?php echo isset($purchase_return_data_group['email']) && $purchase_return_data_group['email'] ? $purchase_return_data_group['email'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">State Code :</span> <?php echo isset($purchase_return_data_group['state_code']) && $purchase_return_data_group['state_code'] ? $purchase_return_data_group['state_code'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">GST Number :</span> <?php echo isset($purchase_return_data_group['gst']) && $purchase_return_data_group['gst'] ? strtoupper($purchase_return_data_group['gst']) : 'None Provided'; ?></div>
                    <div><span class="label-sm">PAN Number :</span> <?php echo isset($purchase_return_data_group['pancard']) && $purchase_return_data_group['pancard'] ? $purchase_return_data_group['pancard'] : 'None Provided'; ?></div>
                </td>
                
                <!-- Return Details (Right) -->
                <td width="50%" valign="top">
                    <div class="section-heading">RETURN DETAILS</div>
                    <div><span class="label-sm">Return Number :</span> <?php echo $purchase_return_data_group['number']; ?></div>
                    <div><span class="label-sm">Return Date :</span> <?php echo $return_display_date; ?></div>
                    <div><span class="label-sm">Delivery Date :</span> <?php echo $purchase_return_data_group['delivery_date']; ?></div>
                    <div><span class="label-sm">Ref. No. :</span> <?php echo $purchase_return_data_group['ref_no']; ?></div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table id="dynamic_field">
            <!-- Header Row -->
            <?php if (!empty($show_purchase_return)) { ?>
            <tr class="items-header">
                <th class="col-sr">No</th>
                <th class="col-desc">Description</th>
                <th class="col-qty">Qty</th>
                <th class="col-hsn">HSN/SAC</th>
                <th class="col-gst">TAX %</th>
                <?php if ($gst_type_row != 'I') { ?>
                    <th class="col-sgst">SGST</th>
                    <th class="col-cgst">CGST</th>
                <?php } else { ?>
                    <th class="col-tax">IGST</th>
                <?php } ?>
                <th class="col-price">Price (₹)</th>
                <th class="col-discount">Discount(%)</th>
                <th class="col-amount">Amount (₹)</th>
            </tr>
            <?php } ?>

            <!-- Items Loop -->
            <?php
            $i = 1;
            $sgst_total_amt = 0;
            $cgst_total_amt = 0;
            $igst_total_amt = 0;
            $amt = 0;
            $total_qty = 0;
            $item_counter = 0;
            $formatted_total_qty = '0';
            
            if (!empty($show_purchase_return)) {
                foreach ($show_purchase_return as $key) {
                    $item_counter++;
                    $sgst_total_amt += $key->sgst;
                    $cgst_total_amt += $key->cgst;
                    $igst_total_amt += $key->igst;
                    $amt += $key->amount;
                    $total_qty += isset($key->quantity) ? (float) $key->quantity : 0;
                    
                    $row_class = ($item_counter == $items_count) ? 'last-item-row' : 'item-row';
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="col-sr"><?php echo $i; ?></td>
                    <td class="col-desc">
                        <b><?php echo $key->product_name; ?></b>
                        <?php if (!empty($key->description)) { ?>
                            <br><span style="font-size: 8px;"><?php echo $key->description; ?></span>
                        <?php } ?>
                    </td>
                    <td class="col-qty"><?php echo number_format($key->quantity, 2); ?></td>
                    <td class="col-hsn"><?php echo $key->hsn_code; ?></td>
                    <td class="col-gst"><?php echo $key->gst; ?>%</td>
                    
                    <?php if ($gst_type_row != 'I') { ?>
                        <td class="col-sgst"><?php echo number_format($key->sgst, 2); ?></td>
                        <td class="col-cgst"><?php echo number_format($key->cgst, 2); ?></td>
                    <?php } else { ?>
                        <td class="col-tax"><?php echo number_format($key->igst, 2); ?></td>
                    <?php } ?>
                    
                    <td class="col-price"><?php echo number_format($key->price, 2); ?></td>
                    <td class="col-discount"><?php echo number_format($key->discount, 2); ?></td>
                    <td class="col-amount"><?php echo number_format($key->amount, 2); ?></td>
                </tr>
            <?php
                    $i++;
                }
            }

            $formatted_total_qty = (fmod($total_qty, 1.0) == 0.0)
                ? number_format($total_qty, 0)
                : number_format($total_qty, 2);
            
            // Add blank rows
            if ($gst_type_row == 'I') {
                // IGST type
                for ($b = 0; $b < $blank_rows_needed; $b++) { ?>
                    <tr class="empty-row">
                        <td class="col-sr">&nbsp;</td>
                        <td class="col-desc">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-hsn">&nbsp;</td>
                        <td class="col-gst">&nbsp;</td>
                        <td class="col-tax">&nbsp;</td>
                        <td class="col-price">&nbsp;</td>
                        <td class="col-discount">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                    </tr>
                <?php }
            } else {
                // SGST/CGST type
                for ($b = 0; $b < $blank_rows_needed; $b++) { ?>
                    <tr class="empty-row">
                        <td class="col-sr">&nbsp;</td>
                        <td class="col-desc">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-hsn">&nbsp;</td>
                        <td class="col-gst">&nbsp;</td>
                        <td class="col-sgst">&nbsp;</td>
                        <td class="col-cgst">&nbsp;</td>
                        <td class="col-price">&nbsp;</td>
                        <td class="col-discount">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                    </tr>
                <?php }
            }
            ?>

            <!-- Total Calculation Section -->
            <?php if ($gst_type_row != 'I') { ?>
                <!-- CGST/SGST Case -->
                <tr class="total-section">
                    <td colspan="5" class="text-right"><b>Total Qty:</b> <?php echo $formatted_total_qty; ?></td>
                    <td colspan="4" class="text-right"><b>Total Before Tax:</b></td>
                    <td class="text-right"><b><?php echo number_format($amt, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>CGST Amount:</b></td>
                    <td class="text-right"><?php echo number_format($cgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>SGST Amount:</b></td>
                    <td class="text-right"><?php echo number_format($sgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>GST Amount:</b></td>
                    <td class="text-right"><b><?php echo number_format($sgst_total_amt + $cgst_total_amt, 2); ?></b></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="9" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td class="text-right"><b><?php echo number_format($purchase_return_data_group['total'], 2); ?></b></td>
                </tr>
            <?php } else { ?>
                <!-- IGST Case -->
                <tr class="total-section">
                    <td colspan="5" class="text-right"><b>Total Qty:</b> <?php echo $formatted_total_qty; ?></td>
                    <td colspan="3" class="text-right"><b>Total Before Tax:</b></td>
                    <td class="text-right"><b><?php echo number_format($amt, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="8" class="text-right"><b>IGST Amount:</b></td>
                    <td class="text-right"><?php echo number_format($igst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="8" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td class="text-right">
                        <b><?php echo number_format($purchase_return_data_group['total'], 2); ?></b>
                    </td>
                </tr>
            <?php } ?>
            
            <!-- Grand Total in Words -->
            <tr style="background: #f0f0f0;">
                <td colspan="<?php echo ($gst_type_row == 'I') ? 9 : 10; ?>" class="text-left amount-words">
                    <b>GRAND TOTAL IN WORDS: 
                    <?php
                    require_once(APPPATH . '/third_party/amount_convert.php');
                    echo strtoupper(number_to_word($purchase_return_data_group['total']));
                    ?> ONLY</b>
                </td>
            </tr>
            
            <!-- Signature Section -->
            <tr>
                <td colspan="4" style="height: 60px; vertical-align: bottom;">
                    <b>Prepared By:</b>
                </td>
                <td colspan="<?php echo ($gst_type_row == 'I') ? 4 : 5; ?>" style="height: 60px; vertical-align: bottom; text-align: center;">
                    Authorised Signatory
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo ($gst_type_row == 'I') ? 8 : 9; ?>" class="text-center" style="padding: 8px;">
                    It is electronic generated purchase return signatures may not appear
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            This is a Computer Generated Purchase Return
        </div>
    </div>
</body>
</html>