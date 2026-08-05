<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?php
    $purchase_bill_defaults = array(
        'number' => '',
        'company_name' => '',
        's_code' => '',
        'address' => '',
        'mobile' => '',
        'email' => '',
        'state_code' => '',
        'gst' => '',
        'pancard' => '',
        'date' => '',
        'delivery_date' => '',
        'invoice_no' => '',
        'total' => 0,
        'pv_title' => '',
        'pv_subheading' => '',
        'subheading' => '',
        'pv_memo' => '',
        'memo' => '',
        'pv_footer' => '',
        'footer' => '',
        'pv_note' => '',
        'pv_terms_and_conditions' => '',
        'pv_payment_terms' => '',
        'pv_process_schedule' => '',
        'pv_taxes' => '',
        'pv_exclusions' => ''
    );
    $purchase_bill_data_group = isset($purchase_bill_data_group) && is_array($purchase_bill_data_group)
        ? array_merge($purchase_bill_defaults, $purchase_bill_data_group)
        : $purchase_bill_defaults;
    $show_purchase_bill = isset($show_purchase_bill) && is_array($show_purchase_bill) ? $show_purchase_bill : array();
    $settings = isset($settings) && is_array($settings) ? $settings : array();
    $settings = array_merge(array(
        'company_logo' => '',
        'company_name' => '',
        'company_gst' => '',
        'company_pan' => '',
        'mobile' => '',
        'email' => '',
        'address' => '',
        'pv_title' => '',
        'pv_subheading' => '',
        'pv_memo' => '',
        'pv_footer' => '',
        'pv_note' => '',
        'invoice_notes' => '',
        'company_stamp' => ''
    ), $settings);
    $voucher_date = 'N/A';
    if (!empty($purchase_bill_data_group['date'])) {
        $voucher_timestamp = strtotime($purchase_bill_data_group['date']);
        if ($voucher_timestamp !== false) {
            $voucher_date = date('d-m-Y', $voucher_timestamp);
        }
    }
    ?>
    <title>Purchase Voucher - <?php echo isset($purchase_bill_data_group['number']) ? $purchase_bill_data_group['number'] : ''; ?></title>
    <meta name="description" content="Purchase Voucher print page">
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
            table-layout: fixed;
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
            overflow: hidden;
            word-wrap: break-word;
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

        /* Column width classes - optimized for Purchase Bill with Unit column */
        .col-sr { width: 4%; text-align: center; }
        .col-desc { width: 35%; }
        .col-hsn { width: 7%; text-align: center; }
        .col-qty { width: 6%; text-align: center; }
        .col-unit { width: 7%; text-align: center; }
        .col-gst { width: 7%; text-align: center; }
        .col-tax { width: 7%; text-align: center; }
        .col-cgst { width: 8%; text-align: center; }
        .col-sgst { width: 8%; text-align: center; }
        .col-rate { width: 8%; text-align: center; }
        .col-amount { width: 10%; text-align: right; }

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

        /* Nested table in bill details - no borders */
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

        /* Bank details - vertical layout */
        .bank-details {
            vertical-align: top;
        }
        
        .tax-summary {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-top: 6px;
        }

        .tax-summary td, .tax-summary th {
            border: 0.5px solid #000;
            padding: 4px 3px;
        }

        .tax-summary th {
            font-weight: bold;
            text-align: center;
        }

        .bank-details-content {
            line-height: 1.8;
        }
        
        .bank-details-content .label-sm {
            min-width: 180px;
            display: inline-block;
            font-weight: bold;
            color: #444;
        }

        .bank-details-content div {
            margin-bottom: 2px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* For lines without labels */
        .bank-details-content div:not(:has(.label-sm)) {
            margin-left: 0;
            font-weight: normal;
        }

        /* For the cheque line */
        .bank-details-content div:last-child {
            margin-top: 5px;
            padding-top: 2px;
        }

        /* Signature section */
        .signature {
            height: 60px;
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
        
        /* Note/message styling */
        .message-box {
            padding: 6px 5px;
            font-style: italic;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <?php
        $gst_type_row = 'S';
        $colspan = "10";
        $colspan1 = "3";
        $colspan2 = "7";
        $colspan3 = "9";
        $colspan4 = "1";
        $total_items = !empty($show_purchase_bill) ? count($show_purchase_bill) : 0;
        
        // Get GST type from first record if available
        if (!empty($show_purchase_bill)) {
            foreach ($show_purchase_bill as $key) {
                if ($key->gst_type == 'I') {
                    $colspan = "9";  // Total columns in table (with Unit)
                    $colspan1 = "3"; // Left column for bank details
                    $colspan2 = "6"; // Right column for signature
                    $colspan3 = "8"; // For total rows (colspan before amount)
                    $colspan4 = "1"; // For amount column
                    $gst_type_row = $key->gst_type;
                } else {
                    $colspan = "10"; // Total columns in table (with Unit)
                    $colspan1 = "3"; // Left column for bank details
                    $colspan2 = "7"; // Right column for signature
                    $colspan3 = "9"; // For total rows (colspan before amount)
                    $colspan4 = "1"; // For amount column
                    $gst_type_row = $key->gst_type;
                }
                break;
            }
        }
        
        // Calculate how many blank rows to add
        $target_total_rows = 14;
        $items_count = !empty($show_purchase_bill) ? count($show_purchase_bill) : 0;
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        
        require_once(APPPATH . '/third_party/amount_convert.php');

        $pv_title = !empty($purchase_bill_data_group['pv_title'])
            ? $purchase_bill_data_group['pv_title']
            : (!empty($settings['pv_title']) ? $settings['pv_title'] : 'PURCHASE VOUCHER');
        $pv_subheading = !empty($purchase_bill_data_group['pv_subheading'])
            ? $purchase_bill_data_group['pv_subheading']
            : (!empty($settings['pv_subheading']) ? $settings['pv_subheading'] : (!empty($purchase_bill_data_group['subheading']) ? $purchase_bill_data_group['subheading'] : ''));
        $pv_memo = !empty($purchase_bill_data_group['pv_memo'])
            ? $purchase_bill_data_group['pv_memo']
            : (!empty($purchase_bill_data_group['memo']) ? $purchase_bill_data_group['memo'] : (!empty($settings['pv_memo']) ? $settings['pv_memo'] : ''));
        $pv_footer = !empty($purchase_bill_data_group['pv_footer'])
            ? $purchase_bill_data_group['pv_footer']
            : (!empty($purchase_bill_data_group['footer']) ? $purchase_bill_data_group['footer'] : (!empty($settings['pv_footer']) ? $settings['pv_footer'] : ''));
        $pv_note = !empty($purchase_bill_data_group['pv_note'])
            ? $purchase_bill_data_group['pv_note']
            : (!empty($settings['pv_note']) ? $settings['pv_note'] : '');
        $pv_terms_and_conditions = !empty($purchase_bill_data_group['pv_terms_and_conditions']) ? $purchase_bill_data_group['pv_terms_and_conditions'] : '';
        $pv_payment_terms = !empty($purchase_bill_data_group['pv_payment_terms']) ? $purchase_bill_data_group['pv_payment_terms'] : '';
        $pv_process_schedule = !empty($purchase_bill_data_group['pv_process_schedule']) ? $purchase_bill_data_group['pv_process_schedule'] : '';
        $pv_taxes = !empty($purchase_bill_data_group['pv_taxes']) ? $purchase_bill_data_group['pv_taxes'] : '';
        $pv_exclusions = !empty($purchase_bill_data_group['pv_exclusions']) ? $purchase_bill_data_group['pv_exclusions'] : '';
        ?>

        <!-- Company Header - Separate Table -->
        <caption><?php echo $pv_title; ?></caption>
        <?php if (!empty($pv_subheading)) { ?>
            <div style="text-align:center; font-size:11px; font-weight:bold; margin-bottom:6px;"><?php echo $pv_subheading; ?></div>
        <?php } ?>
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

        <!-- Vendor and Bill Details - Separate Table -->
        <table class="details-section">
            <tr>
                <!-- Vendor Details (Left) -->
                <td width="50%" valign="top">
                    <div class="section-heading">VENDOR</div>
                    <div><span class="label-sm">Name</span> <?php echo $purchase_bill_data_group['company_name']; ?></div>
                    <div><span class="label-sm">Address</span> <?php echo isset($purchase_bill_data_group['address']) && $purchase_bill_data_group['address'] ? $purchase_bill_data_group['address'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">Mobile:</span> <?php echo isset($purchase_bill_data_group['mobile']) && $purchase_bill_data_group['mobile'] ? $purchase_bill_data_group['mobile'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">Email:</span> <?php echo isset($purchase_bill_data_group['email']) && $purchase_bill_data_group['email'] ? $purchase_bill_data_group['email'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">State Code:</span> <?php echo isset($purchase_bill_data_group['state_code']) && $purchase_bill_data_group['state_code'] ? $purchase_bill_data_group['state_code'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">GST Number:</span> <?php echo isset($purchase_bill_data_group['gst']) && $purchase_bill_data_group['gst'] ? strtoupper($purchase_bill_data_group['gst']) : 'None Provided'; ?></div>
                                        <div><span class="label-sm">Vendor Code:</span> <?php echo isset($purchase_bill_data_group['s_code']) && $purchase_bill_data_group['s_code'] ? $purchase_bill_data_group['s_code'] : 'N/A'; ?></div>

                    <div><span class="label-sm">PAN Number:</span> <?php echo isset($purchase_bill_data_group['pancard']) && $purchase_bill_data_group['pancard'] ? $purchase_bill_data_group['pancard'] : 'None Provided'; ?></div>
                </td>
                
                <!-- Bill Details (Right) -->
                <td width="50%" valign="top">
                    <div class="section-heading">Voucher DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">Voucher No:</td><td><?php echo $purchase_bill_data_group['number']; ?></td></tr>
                        <tr><td class="label-sm">Voucher Date:</td><td><?php echo $voucher_date; ?></td></tr>
                        <tr><td class="label-sm">Delivery Date:</td><td><?php echo $purchase_bill_data_group['delivery_date'] ?: 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Invoice No:</td><td><?php echo $purchase_bill_data_group['invoice_no'] ?: 'N/A'; ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Message Box -->
        <table style="width:100%; border-collapse: collapse; border: 0.5px solid #000; border-top: none; font-size:9px; margin-top:-0.5px;">
            <tr>
                <td class="message-box">
                    <?php if (!empty($pv_memo)) { ?>
                        <?php echo $pv_memo; ?>
                    <?php } else { ?>
                        <b>Dear Sir/Madam,</b><br>
                        With reference to your quotation and discussion held with you, kindly start following works as per rates and Terms &amp; Conditions mention here under. Kindly send us order confirmation.
                    <?php } ?>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table id="dynamic_field">
            
            <!-- Items Table Header -->
            <?php if (!empty($show_purchase_bill)) { ?>
            <tr class="items-header">
                <th class="col-sr">Sr.</th>
                <th class="col-desc">Description</th>
                <th class="col-hsn">HSN/SAC</th>
                <th class="col-qty">Qty</th>
                <th class="col-unit">Unit</th>
                <th class="col-gst">TAX</th>
                <?php if ($gst_type_row != 'I') { ?>
                    <th class="col-sgst">SGST</th>
                    <th class="col-cgst">CGST</th>
                <?php } else { ?>
                    <th class="col-tax">IGST</th>
                <?php } ?>
                <th class="col-rate">Price</th>
                <th class="col-amount">Amount</th>
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
            
            if (!empty($show_purchase_bill)) {
                foreach ($show_purchase_bill as $key) {
                    $item_counter++;
                    
                    // Calculate line amount
                    $quantity = floatval($key->quantity);
                    $price = floatval($key->price);
                    $line_amount = $quantity * $price;
                    $amt += $line_amount;
                    $total_qty += $quantity;

                    // Track GST amounts
                    if ($key->gst_type != 'I') {
                        $sgst_total_amt += $key->sgst;
                        $cgst_total_amt += $key->cgst;
                    } else {
                        $igst_total_amt += $key->igst;
                    }
                    
                    // Add class for last item row
                    $row_class = ($item_counter == $items_count) ? 'last-item-row' : 'item-row';
                    
                    // Decode HTML entities in description
                    $description = html_entity_decode($key->description, ENT_QUOTES, 'UTF-8');
                    $description = str_replace(['<p>', '</p>'], ['', '<br>'], $description);
                    $description = strip_tags($description, '<br><strong><b><em><i><u>');
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="col-sr"><?php echo $i; ?></td>
                    <td class="col-desc">
                        <b><?php echo htmlspecialchars($key->product_name) . " - " .  $key->item_name; ?></b>
                        <?php if (!empty($description)) { ?>
                            <br><span style="font-size: 8px;"><?php echo $description; ?></span>
                        <?php } ?>
                    </td>
                    <td class="col-hsn"><?php echo htmlspecialchars($key->hsn_code); ?></td>
                    <td class="col-qty"><?php echo indian_number_format($quantity, 0); ?></td>
                    <td class="col-unit"><?php echo htmlspecialchars($key->unit); ?></td>
                    <td class="col-gst"><?php echo $key->gst; ?></td>
                    
                    <?php if ($gst_type_row != 'I') { ?>
                        <td class="col-sgst"><?php echo indian_number_format($key->sgst, 2); ?></td>
                        <td class="col-cgst"><?php echo indian_number_format($key->cgst, 2); ?></td>
                    <?php } else { ?>
                        <td class="col-tax"><?php echo indian_number_format($key->igst, 2); ?></td>
                    <?php } ?>
                    
                    <td class="col-rate"><?php echo indian_number_format($price, 2); ?></td>
                    <td class="col-amount text-right"><?php echo indian_number_format($line_amount, 2); ?></td>
                </tr>
            <?php
                    $i++;
                }
            }

            $formatted_total_qty = (fmod($total_qty, 1.0) == 0.0)
                ? number_format($total_qty, 0)
                : number_format($total_qty, 2);
            
            // Add blank rows with Unit column
            if ($gst_type_row == 'I') {
                // IGST type - 9 columns (including Unit)
                for ($b = 0; $b < $blank_rows_needed; $b++) { ?>
                    <tr class="empty-row">
                        <td class="col-sr">&nbsp;</td>
                        <td class="col-desc">&nbsp;</td>
                        <td class="col-hsn">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-unit">&nbsp;</td>
                        <td class="col-gst">&nbsp;</td>
                        <td class="col-tax">&nbsp;</td>
                        <td class="col-rate">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                    </tr>
                <?php }
            } else {
                // SGST/CGST type - 10 columns (including Unit)
                for ($b = 0; $b < $blank_rows_needed; $b++) { ?>
                    <tr class="empty-row">
                        <td class="col-sr">&nbsp;</td>
                        <td class="col-desc">&nbsp;</td>
                        <td class="col-hsn">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-unit">&nbsp;</td>
                        <td class="col-gst">&nbsp;</td>
                        <td class="col-sgst">&nbsp;</td>
                        <td class="col-cgst">&nbsp;</td>
                        <td class="col-rate">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                    </tr>
                <?php }
            }
            ?>

            <!-- Calculate Tax Summary -->
            <?php
            $tax_summary = array();
            $total_taxable_value = 0;
            $total_igst_amount = 0;
            $total_sgst_amount = 0;
            $total_cgst_amount = 0;

            foreach ($show_purchase_bill as $key) {
                $hsn = $key->hsn_code;
                $gst_rate = $key->gst;
                $quantity = floatval($key->quantity);
                $price = floatval($key->price);
                $taxable_value = $quantity * $price;
                $gst_percentage = floatval(str_replace('%', '', $gst_rate));
                $gst_amount = ($taxable_value * $gst_percentage) / 100;

                if (!isset($tax_summary[$hsn . '|' . $gst_rate])) {
                    $tax_summary[$hsn . '|' . $gst_rate] = array(
                        'hsn' => $hsn,
                        'gst_rate' => $gst_rate,
                        'taxable_value' => 0,
                        'igst' => 0,
                        'sgst' => 0,
                        'cgst' => 0
                    );
                }

                $tax_summary[$hsn . '|' . $gst_rate]['taxable_value'] += $taxable_value;

                if ($key->gst_type == 'I') {
                    $tax_summary[$hsn . '|' . $gst_rate]['igst'] += $gst_amount;
                    $total_igst_amount += $gst_amount;
                } else {
                    $sgst = $gst_amount / 2;
                    $cgst = $gst_amount / 2;
                    $tax_summary[$hsn . '|' . $gst_rate]['sgst'] += $sgst;
                    $tax_summary[$hsn . '|' . $gst_rate]['cgst'] += $cgst;
                    $total_sgst_amount += $sgst;
                    $total_cgst_amount += $cgst;
                }

                $total_taxable_value += $taxable_value;
            }

            $total_tax_amount = $total_igst_amount + $total_sgst_amount + $total_cgst_amount;
            ?>

            <!-- Total Calculation Section - Updated with Unit column -->
            <?php if ($gst_type_row != 'I') { ?>
                <!-- CGST/SGST Case - 10 columns total (with Unit), so colspan before amount is 9 -->
                <tr class="total-section">
                    <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                    <td><?php echo $formatted_total_qty; ?></td>
                    <td colspan="5" class="text-right"><b>Total Before Tax:</b></td>
                    <td class="col-amount text-right"><b><?php echo indian_number_format($amt, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>CGST Amount:</b></td>
                    <td class="col-amount text-right"><?php echo indian_number_format($cgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>SGST Amount:</b></td>
                    <td class="col-amount text-right"><?php echo indian_number_format($sgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>Total GST:</b></td>
                    <td class="col-amount text-right"><b><?php echo indian_number_format($sgst_total_amt + $cgst_total_amt, 2); ?></b></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="9" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td class="col-amount text-right"><b> <?php echo indian_number_format($purchase_bill_data_group['total'], 2); ?></b></td>
                </tr>
            <?php } else { ?>
                <!-- IGST Case - 9 columns total (with Unit), so colspan before amount is 8 -->
                <tr class="total-section">
                    <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                    <td><?php echo $formatted_total_qty; ?></td>
                    <td colspan="4" class="text-right"><b>Total Before Tax:</b></td>
                    <td class="col-amount text-right"><b><?php echo indian_number_format($amt, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="8" class="text-right"><b>IGST Amount:</b></td>
                    <td class="col-amount text-right"><?php echo indian_number_format($igst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="8" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td class="col-amount text-right"><b> <?php echo indian_number_format($purchase_bill_data_group['total'], 2); ?></b></td>
                </tr>
            <?php } ?>
            
            <!-- Grand Total in Words -->
            <tr>
                <td colspan="<?php echo $colspan; ?>" class="text-right amount-words">
                    <b>Grand Total in Words: <?php echo number_to_word($purchase_bill_data_group['total']); ?> Only</b>
                </td>
            </tr>

            <!-- Tax Summary Table -->
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 5px;">
                    <table class="tax-summary">
                        <thead>
                            <tr>
                                <th>HSN/SAC</th>
                                <th>Taxable Value</th>
                                <?php if ($gst_type_row == 'I') { ?>
                                    <th>IGST Rate</th>
                                    <th>IGST Amount</th>
                                <?php } else { ?>
                                    <th>CGST Rate</th>
                                    <th>CGST Amount</th>
                                    <th>SGST Rate</th>
                                    <th>SGST Amount</th>
                                <?php } ?>
                                <th>Total Tax</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tax_summary as $item) { ?>
                                <tr>
                                    <td class="text-center"><?php echo $item['hsn']; ?></td>
                                    <td class="text-right"><?php echo indian_number_format($item['taxable_value'], 2); ?></td>
                                    <?php if ($gst_type_row == 'I') { ?>
                                        <td class="text-center"><?php echo $item['gst_rate']; ?></td>
                                        <td class="text-right"><?php echo indian_number_format($item['igst'], 2); ?></td>
                                        <td class="text-right"><?php echo indian_number_format($item['igst'], 2); ?></td>
                                    <?php } else {
                                        $rate = floatval(str_replace('%', '', $item['gst_rate'])) / 2;
                                    ?>
                                        <td class="text-center"><?php echo $rate . '%'; ?></td>
                                        <td class="text-right"><?php echo indian_number_format($item['cgst'], 2); ?></td>
                                        <td class="text-center"><?php echo $rate . '%'; ?></td>
                                        <td class="text-right"><?php echo indian_number_format($item['sgst'], 2); ?></td>
                                        <td class="text-right"><?php echo indian_number_format($item['cgst'] + $item['sgst'], 2); ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                            <tr style="font-weight: bold;">
                                <td class="text-center">Total</td>
                                <td class="text-right"><?php echo indian_number_format($total_taxable_value, 2); ?></td>
                                <?php if ($gst_type_row == 'I') { ?>
                                    <td></td>
                                    <td class="text-right"><?php echo indian_number_format($total_igst_amount, 2); ?></td>
                                    <td class="text-right"><?php echo indian_number_format($total_igst_amount, 2); ?></td>
                                <?php } else { ?>
                                    <td></td>
                                    <td class="text-right"><?php echo indian_number_format($total_cgst_amount, 2); ?></td>
                                    <td></td>
                                    <td class="text-right"><?php echo indian_number_format($total_sgst_amount, 2); ?></td>
                                    <td class="text-right"><?php echo indian_number_format($total_tax_amount, 2); ?></td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-right amount-words">
                        Tax Amount (in words): INR <?php echo number_to_word($total_tax_amount); ?> Only
                    </div>
                </td>
            </tr>

            <!-- Bank Details & Signature Section - Combined Row -->
            <tr>
                <!-- Bank Details - Vertical Layout with Parsing -->
                <td colspan="<?php echo $colspan1; ?>" class="bank-details" style="padding: 8px 5px;">
                    <div class="section-heading">Bank Details:</div>
                    <div class="bank-details-content">
                        <?php 
                        $bank_text = isset($settings['invoice_notes']) ? $settings['invoice_notes'] : '';
                        
                        if (!empty($bank_text)) {
                            // Define field patterns to look for
                            $patterns = [
                                'payment_by' => '/Payment by:\s*([^:]+?)(?=Company Bank Account Details|$)/i',
                                'company_details' => '/Company Bank Account Details for Online Transfer/i',
                                'name' => '/Name:\s*([^:]+?)(?=Account No:|$)/i',
                                'account_no' => '/Account No:\s*([^:]+?)(?=IFSC Code:|$)/i',
                                'ifsc' => '/IFSC Code:\s*([^:]+?)(?=Account Type:|$)/i',
                                'account_type' => '/Account Type:\s*([^:]+?)(?=Branch:|$)/i',
                                'branch' => '/Branch:\s*([^:]+?)(?=Cheque will be|$)/i',
                                'cheque' => '/Cheque will be[^"]*"([^"]+)"[^.]*\./i'
                            ];
                            
                            // Extract and display Payment by
                            if (preg_match($patterns['payment_by'], $bank_text, $matches)) {
                                echo '<div><span class="label-sm">Payment by:</span> ' . trim($matches[1]) . '</div>';
                            }
                            
                            // Display Company Bank Account Details line
                            echo '<div>Company Bank Account Details for Online Transfer</div>';
                            
                            // Extract and display Name
                            if (preg_match($patterns['name'], $bank_text, $matches)) {
                                echo '<div><span class="label-sm">Name:</span> ' . trim($matches[1]) . '</div>';
                            }
                            
                            // Extract and display Account No
                            if (preg_match($patterns['account_no'], $bank_text, $matches)) {
                                echo '<div><span class="label-sm">Account No:</span> ' . trim($matches[1]) . '</div>';
                            }
                            
                            // Extract and display IFSC Code
                            if (preg_match($patterns['ifsc'], $bank_text, $matches)) {
                                echo '<div><span class="label-sm">IFSC Code:</span> ' . trim($matches[1]) . '</div>';
                            }
                            
                            // Extract and display Account Type
                            if (preg_match($patterns['account_type'], $bank_text, $matches)) {
                                echo '<div><span class="label-sm">Account Type:</span> ' . trim($matches[1]) . '</div>';
                            }
                            
                            // Extract and display Branch
                            if (preg_match($patterns['branch'], $bank_text, $matches)) {
                                echo '<div><span class="label-sm">Branch:</span> ' . trim($matches[1]) . '</div>';
                            }
                            
                            // Extract and display Cheque line
                            if (preg_match($patterns['cheque'], $bank_text, $matches)) {
                                echo '<div style="margin-top: 3px;">Cheque will be drawn "' . trim($matches[1]) . '" and sent to our company address</div>';
                            }
                        } else {
                            echo '<div>No bank details available</div>';
                        }
                        ?>
                    </div>
                </td>
                
                <!-- Authorised Signatory Only (Right side) -->
                <td colspan="<?php echo $colspan2; ?>" class="signature" style="vertical-align: bottom; text-align: center;">
                    <?php if (isset($stamp) && $stamp == "yes") { ?>
                        <div><img class="stamp-img" src="<?php echo base_url() . $settings['company_stamp']; ?>"></div>
                    <?php } ?>
                    <div>Authorised Signatory</div>
                </td>
            </tr>

            <?php if (!empty($pv_note)) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 8px 5px;">
                    <div class="section-heading">Note</div>
                    <div><?php echo $pv_note; ?></div>
                </td>
            </tr>
            <?php } ?>

            <?php if (!empty($pv_terms_and_conditions)) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 8px 5px;">
                    <div class="section-heading">Terms &amp; Conditions</div>
                    <div><?php echo $pv_terms_and_conditions; ?></div>
                </td>
            </tr>
            <?php } ?>

            <?php if (!empty($pv_payment_terms)) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 8px 5px;">
                    <div class="section-heading">Payment Terms</div>
                    <div><?php echo $pv_payment_terms; ?></div>
                </td>
            </tr>
            <?php } ?>

            <?php if (!empty($pv_process_schedule)) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 8px 5px;">
                    <div class="section-heading">Process Schedule</div>
                    <div><?php echo $pv_process_schedule; ?></div>
                </td>
            </tr>
            <?php } ?>

            <?php if (!empty($pv_taxes)) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 8px 5px;">
                    <div class="section-heading">Taxes</div>
                    <div><?php echo $pv_taxes; ?></div>
                </td>
            </tr>
            <?php } ?>

            <?php if (!empty($pv_exclusions)) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 8px 5px;">
                    <div class="section-heading">Exclusions</div>
                    <div><?php echo $pv_exclusions; ?></div>
                </td>
            </tr>
            <?php } ?>

        </table>

        <!-- Footer -->
        <div class="footer">
            <?php if (!empty($pv_footer)) { ?>
                <div style="margin-bottom:4px;"><?php echo $pv_footer; ?></div>
            <?php } ?>
            It is electronic generated purchase Voucher signatures may not appear<br>
            <?php echo $settings['company_name']; ?> | <?php echo $settings['address']; ?>
        </div>
    </div>
</body>
</html>