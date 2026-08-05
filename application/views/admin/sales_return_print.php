<?php require_once(APPPATH . 'third_party/amount_convert.php'); ?>
<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Credit Note</title>
    <meta name="description" content="Credit Note print page">
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

        .invoice-container {
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

        /* Billing/Shipping Details - Separate Table */
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
            padding: 3px;
            vertical-align: top;
            margin-bottom: -0.5px;
        }
        
        /* Remove top and bottom borders from tr elements only */
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
        .col-sr { width: 3%; text-align: center; }
        .col-desc { width: 25%; }
        .col-hsn { width: 8%; text-align: center; }
        .col-qty { width: 4%; text-align: center; }
        .col-unit { width: 5%; text-align: center; }
        .col-gst { width: 5%; text-align: center; }
        .col-tax { width: 12%; text-align: center; }
        .col-cgst { width: 10%; text-align: center; }
        .col-sgst { width: 10%; text-align: center; }
        .col-rate { width: 8%; text-align: center; }
        .col-discount { width: 7%; text-align: center; }
        .col-amount { width: 13%; text-align: right; }

        /* Text alignments */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
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

        /* Credit Note details */
        .sales-return-details {
            font-weight: bold;
            color: #003366;
            font-size: 9px;
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

        /* Nested table in sales return details - no borders */
        .no-border-table {
            width: 100%;
            border-collapse: collapse;
        }

        .no-border-table td {
            border: none;
            padding: 1px;
        }
        
        /* Empty row styling - no borders */
        .empty-row td {
            border-top: none;
            border-bottom: none;
            height: 33px;
        }

        /* Total section */
        .total-section td {
            font-weight: bold;
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
        .signature {
            height: 80px;
            vertical-align: bottom;
            text-align: center;
        }

        .stamp-img {
            max-width: 70px;
            max-height: 50px;
            margin-bottom: 5px;
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
    <div class="invoice-container">
        <?php
        // Initialize variables with default values
        $gst_type_row = 'S';
        $total_items = !empty($show_sales_return) ? count($show_sales_return) : 0;
        
        // Get GST type from first record if available
        if (!empty($show_sales_return)) {
            foreach ($show_sales_return as $key) {
                if ($key->gst_type == 'I') {
                    $colspan = "9";
                    $colspan1 = "4";
                    $colspan2 = "4";
                    $colspan3 = "8";
                    $colspan4 = "1";
                    $gst_type_row = $key->gst_type;
                } else {
                    $colspan = "10";
                    $colspan1 = "5";
                    $colspan2 = "4";
                    $colspan3 = "9";
                    $colspan4 = "1";
                    $gst_type_row = $key->gst_type;
                }
                break;
            }
        }

        $column_count = ($gst_type_row == 'I') ? 9 : 10;
        $summary_label_colspan = $column_count - 1;
        
        // Calculate how many blank rows to add
        $target_total_rows = 15;
        $items_count = !empty($show_sales_return) ? count($show_sales_return) : 0;
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        ?>

        <!-- Company Header - Separate Table -->
        <caption>SALES RETURN</caption>
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

        <!-- Buyer and Credit Note Details - Separate Table -->
        <table class="details-section">
            <tr>
                <!-- Vendor Details (Left) -->
                <td width="50%" valign="top">
                    <div class="section-heading">VENDOR</div>
                    <div><span class="label-sm">Name</span> <?php echo isset($sales_return_data_group['company_name']) ? $sales_return_data_group['company_name'] : ''; ?></div>
                    <div><span class="label-sm">Address</span> <?php echo isset($sales_return_data_group['address']) && $sales_return_data_group['address'] ? $sales_return_data_group['address'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">GST Number:</span> <?php echo isset($sales_return_data_group['gst']) && $sales_return_data_group['gst'] ? strtoupper($sales_return_data_group['gst']) : 'None Provided'; ?></div>
                    <div><span class="label-sm">PAN Number:</span> <?php echo isset($sales_return_data_group['pancard']) && $sales_return_data_group['pancard'] ? $sales_return_data_group['pancard'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">Mobile:</span> <?php echo isset($sales_return_data_group['mobile']) && $sales_return_data_group['mobile'] ? $sales_return_data_group['mobile'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">State Code:</span> <?php echo isset($sales_return_data_group['state_code']) && $sales_return_data_group['state_code'] ? $sales_return_data_group['state_code'] : 'None Provided'; ?></div>
                </td>
                
                <!-- Credit Note Details (Right) -->
                <td width="50%" valign="top">
                    <div class="section-heading">SALES RETURN DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">Return No:</td><td><?php echo isset($sales_return_data_group['number']) ? $sales_return_data_group['number'] : ''; ?></td></tr>
                        <tr><td class="label-sm">Return Date:</td><td><?php echo isset($sales_return_data_group['date']) ? date('d-m-Y', strtotime($sales_return_data_group['date'])) : ''; ?></td></tr>
                        <tr><td class="label-sm">Delivery Date:</td><td><?php echo isset($sales_return_data_group['delivery_date']) ? $sales_return_data_group['delivery_date'] : ''; ?></td></tr>
                        <tr><td class="label-sm">Ref. No:</td><td><?php echo isset($sales_return_data_group['ref_no']) ? $sales_return_data_group['ref_no'] : ''; ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table id="dynamic_field">
            
            <!-- Items Table Header -->
            <?php if (!empty($show_sales_return)) { ?>
            <tr class="items-header">
                <th class="col-sr">No</th>
                <th class="col-desc">Description</th>
                <th class="col-qty">Qty</th>
                <th class="col-unit">Unit</th>
                <th class="col-hsn">HSN/SAC</th>
                <th class="col-gst">TAX %</th>
                <?php if ($gst_type_row != 'I') { ?>
                    <th class="col-sgst">SGST</th>
                    <th class="col-cgst">CGST</th>
                <?php } else { ?>
                    <th class="col-tax">IGST</th>
                <?php } ?>
                <th class="col-rate">Price</th>
                <th class="col-discount">Discount(%)</th>
                <th class="col-amount">&nbsp;&nbsp;Amount&nbsp;&nbsp;</th>
            </tr>
            <?php } ?>

            <!-- Items Loop -->
            <?php
            $i = 1;
            $cgst_total_amt = 0;
            $sgst_total_amt = 0;
            $igst_total_amt = 0;
            $amt = 0;
            $total_qty = 0;
            $item_counter = 0;
            
            if (!empty($show_sales_return)) {
                foreach ($show_sales_return as $key) {
                    $item_counter++;
                    $cgst_total_amt += $key->cgst;
                    $sgst_total_amt += $key->sgst;
                    $igst_total_amt += $key->igst;
                    $amt += $key->amount;
                    $total_qty += (float) $key->quantity;
                    
                    // Add class for last item row
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
                    <td class="col-qty"><?php echo number_format($key->quantity); ?></td>
                    <td class="col-unit"><?php echo $key->unit; ?></td>
                    <td class="col-hsn"><?php echo $key->hsn_code; ?></td>
                    <td class="col-gst"><?php echo $key->gst; ?></td>
                    
                    <?php if ($gst_type_row != 'I') { ?>
                        <td class="col-sgst"><?php echo number_format($key->sgst, 2); ?></td>
                        <td class="col-cgst"><?php echo number_format($key->cgst, 2); ?></td>
                    <?php } else { ?>
                        <td class="col-tax"><?php echo number_format($key->igst, 2); ?></td>
                    <?php } ?>
                    
                    <td class="col-rate"><?php echo number_format($key->price, 2); ?></td>
                    <td class="col-discount"><?php echo number_format($key->discount, 2); ?></td>
                    <td class="col-amount"><?php echo number_format($key->amount, 2); ?></td>
                </tr>
            <?php
                    $i++;
                }
            }
            
            // Add blank rows
            if ($gst_type_row == 'I') {
                // IGST type - 10 columns
                for ($b = 0; $b < $blank_rows_needed; $b++) { ?>
                    <tr class="empty-row">
                        <td class="col-sr">&nbsp;</td>
                        <td class="col-desc">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-unit">&nbsp;</td>
                        <td class="col-hsn">&nbsp;</td>
                        <td class="col-gst">&nbsp;</td>
                        <td class="col-tax">&nbsp;</td>
                        <td class="col-rate">&nbsp;</td>
                        <td class="col-discount">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                    </tr>
                <?php }
            } else {
                // SGST/CGST type - 11 columns
                for ($b = 0; $b < $blank_rows_needed; $b++) { ?>
                    <tr class="empty-row">
                        <td class="col-sr">&nbsp;</td>
                        <td class="col-desc">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-unit">&nbsp;</td>
                        <td class="col-hsn">&nbsp;</td>
                        <td class="col-gst">&nbsp;</td>
                        <td class="col-sgst">&nbsp;</td>
                        <td class="col-cgst">&nbsp;</td>
                        <td class="col-rate">&nbsp;</td>
                        <td class="col-discount">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                    </tr>
                <?php }
            }
            ?>

            <!-- Total Calculation Section -->
            <?php 
            $formatted_total_qty = (fmod($total_qty, 1.0) == 0.0)
                ? number_format($total_qty, 0)
                : number_format($total_qty, 2);
            ?>

            <?php if ($gst_type_row != 'I') { ?>
                <!-- CGST/SGST Case -->
                <tr class="total-section">
                    <td colspan="5" class="text-right"><b>Total Qty:</b> <?php echo $formatted_total_qty; ?></td>
                    <td colspan="4" class="text-right"><b>Total Before Tax:</b></td>
                    <td colspan="2" class="text-right"><b><?php echo number_format($amt, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>CGST Amount:</b></td>
                    <td colspan="2" class="text-right"><?php echo number_format($cgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>SGST Amount:</b></td>
                    <td colspan="2" class="text-right"><?php echo number_format($sgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>GST Amount:</b></td>
                    <td colspan="2" class="text-right"><b><?php echo number_format($cgst_total_amt + $sgst_total_amt, 2); ?></b></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="9" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td colspan="2" class="text-right"><b><?php echo number_format(isset($sales_return_data_group['total']) ? $sales_return_data_group['total'] : 0, 2); ?></b></td>
                </tr>
            <?php } else { ?>
                <!-- IGST Case -->
                <tr class="total-section">
                    <td colspan="5" class="text-right"><b>Total Qty:</b> <?php echo $formatted_total_qty; ?></td>
                    <td colspan="3" class="text-right"><b>Total Before Tax:</b></td>
                    <td colspan="2" class="text-right"><b><?php echo number_format($amt, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="8" class="text-right"><b>IGST Amount:</b></td>
                    <td colspan="2" class="text-right"><?php echo number_format($igst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="8" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td colspan="2" class="text-right">
                        <b><?php echo number_format(isset($sales_return_data_group['total']) ? $sales_return_data_group['total'] : 0, 2); ?></b>
                    </td>
                </tr>
            <?php } ?>

            <!-- Grand Total in Words -->
            <tr style="background: #f0f0f0;">
                <td colspan="<?php echo ($gst_type_row == 'I') ? 10 : 11; ?>" class="text-left amount-words">
                    <b>GRAND TOTAL IN WORDS: 
                    <?php
                    require_once(APPPATH . '/third_party/amount_convert.php');
                    echo strtoupper(number_to_word(isset($sales_return_data_group['total']) ? $sales_return_data_group['total'] : 0));
                    ?> ONLY</b>
                </td>
            </tr>

            <!-- Signature Section -->
            <tr>
                <td colspan="5" style="height: 60px; vertical-align: bottom;">
                    <b>Prepared By:</b>
                </td>
                <td colspan="<?php echo ($gst_type_row == 'I') ? 4 : 5; ?>" class="signature" style="vertical-align: bottom; text-align: center;">
                    Authorised Signatory
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo ($gst_type_row == 'I') ? 9 : 10; ?>" class="text-center" style="padding: 8px;">
                    It is electronic generated sales return signatures may not appear
                </td>
            </tr>
        </table>
        <div class="footer">
            This is a Computer Generated Credit Note
        </div>
    </div>
</body>

</html>