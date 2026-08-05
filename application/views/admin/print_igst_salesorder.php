<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Sales Order - <?php echo isset($salesorders_data_group['number_fk']) ? $salesorders_data_group['number_fk'] : ''; ?></title>
    <meta name="description" content="Sales Order print page">
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

        /* Project/Sales Order Details - Separate Table */
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

        /* Column width classes - optimized for Sales Order */

        /* Column width classes - optimized with DISCOUNT column after Price */
        .col-sr { width: 3%; text-align: center; }
        .col-desc { width: 25%; }
        .col-hsn { width: 8%; text-align: center; }
        .col-qty { width: 4%; text-align: center; }
        .col-unit { width: 5%; text-align: center; }
        .col-gst { width: 4%; text-align: center; }
        .col-tax { width: 12%; text-align: center; } /* Shown when CGST/SGST are hidden */
        .col-cgst { width: 10%; text-align: center; }
        .col-sgst { width: 10%; text-align: center; }
        .col-rate { width: 8%; text-align: center; }
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
            min-width: 100px;
            display: inline-block;
        }

        /* Project/Sales Order details */
        .so-details {
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
            border-bottom: 1px solid #ccc;
        }
        
        /* Item rows styling */
        .item-row td {
            border-top: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
            height: auto;
            min-height: 40px;
            padding: 5px 3px;
        }

        /* First item row - keep top border */
        .item-row:first-child td {
            border-top: 0.5px solid #000;
        }

        /* Last item row - keep bottom border */
        .last-item-row td {
            border-bottom: 0.5px solid #000;
        }

        /* Nested table in details - no borders */
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
            height: 40px;
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
        
        /* Helper class to hide elements */
        .hide {
            display: none;
        }
        
        /* Extra styling for buyer details block */
        .buyer-detail-line {
            margin-bottom: 3px;
            word-break: break-word;
        }
        .project-details-block {
            line-height: 1.4;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <?php
        $gst_type_row = 'S';
        $total_items = !empty($show_salesorder) ? count($show_salesorder) : 0;
        
        // Get GST type from first record if available
        if (!empty($show_salesorder)) {
            foreach ($show_salesorder as $key) {
                if ($key->gst_type == 'I') {
                    $colspan = "9";
                    $colspan1 = "3";
                    $colspan2 = "6";
                    $colspan3 = "7";
                    $colspan4 = "22";
                    $colspan5 = "3";
                    $colspan6 = "3";
                    $colspan7 = "3";
                    $gst_type_row = $key->gst_type;
                } else {
                    $colspan = "10";
                    $colspan1 = "3";
                    $colspan2 = "7";
                    $colspan3 = "8";
                    $colspan4 = "2";
                    $colspan5 = "3";
                    $colspan6 = "3";
                    $colspan7 = "4";
                    $gst_type_row = $key->gst_type;
                }
                break;
            }
        }
        
        // Calculate how many blank rows to add
        $target_total_rows = 11;
        $items_count = !empty($show_salesorder) ? count($show_salesorder) : 0;
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        
        $non_heading_items_count = 0;
        if (!empty($show_salesorder)) {
            foreach ($show_salesorder as $check_key) {
                if (isset($check_key->product_name) && $check_key->product_name !== '__HEADING__') {
                    $non_heading_items_count++;
                }
            }
        }
        
        // Helper function to format dates
        function format_print_date($date) {
            if (empty($date) || $date === '0000-00-00' || $date === '1970-01-01') {
                return '';
            }
            $ts = strtotime($date);
            if ($ts === false) {
                return $date;
            }
            return date('d-m-Y', $ts);
        }
        
        // Helper function to safely output value with fallback
        function safe_value($value, $fallback = 'None Provided') {
            if (isset($value) && !empty(trim($value)) && trim($value) !== '') {
                return htmlspecialchars(trim($value));
            }
            return $fallback;
        }
        
        require_once(APPPATH . '/third_party/amount_convert.php');
        ?>

        <!-- Company Header - Separate Table -->
        <caption>SALES ORDER</caption>
        <table class="company-header">
            <tr>
                <td width="25%" align="center" valign="middle">
                    <img class="company-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo" style="max-width: 100%; max-height: 60px;">
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

        <!-- Project and Sales Order Details - Separate Table -->
        <table class="details-section">
            <tr>
                <!-- Project Details (Left) - NOW INCLUDES FULL BUYER/PROJECT DETAILS -->
               <!-- Customer Details (Left) - Matches show page exactly -->
<td width="50%" valign="top">
    <div class="section-heading">CUSTOMER DETAILS</div>
    <div class="project-details-block">
        <?php
        // Company Name
        echo '<div class="buyer-detail-line"><span class="label-sm">Company Name :</span> ' . safe_value(isset($salesorders_data_group['company_name']) ? $salesorders_data_group['company_name'] : '', 'N/A') . '</div>';
        
        // Customer Name
        echo '<div class="buyer-detail-line"><span class="label-sm">Customer Name :</span> ' . safe_value(isset($salesorders_data_group['fullname']) ? $salesorders_data_group['fullname'] : '', 'N/A') . '</div>';
        
        // Customer Code
        echo '<div class="buyer-detail-line"><span class="label-sm">Customer Code :</span> ' . safe_value(isset($salesorders_data_group['c_code']) ? $salesorders_data_group['c_code'] : '', 'None Provided') . '</div>';
        
        // GST Number
        $gst_val = isset($salesorders_data_group['gst']) ? trim($salesorders_data_group['gst']) : '';
        if (!empty($gst_val)) {
            echo '<div class="buyer-detail-line"><span class="label-sm">GST Number :</span> ' . htmlspecialchars($gst_val) . '</div>';
        } else {
            echo '<div class="buyer-detail-line"><span class="label-sm">GST Number :</span> None Provided</div>';
        }
        
        // PAN Number
        $pan_val = isset($salesorders_data_group['pancard']) ? trim($salesorders_data_group['pancard']) : '';
        if (!empty($pan_val)) {
            echo '<div class="buyer-detail-line"><span class="label-sm">PAN Number :</span> ' . htmlspecialchars($pan_val) . '</div>';
        } else {
            echo '<div class="buyer-detail-line"><span class="label-sm">PAN Number :</span> None Provided</div>';
        }
        
        // State Code
        $state_code_val = isset($salesorders_data_group['state_code']) ? trim($salesorders_data_group['state_code']) : '';
        if (!empty($state_code_val)) {
            echo '<div class="buyer-detail-line"><span class="label-sm">State Code :</span> ' . htmlspecialchars($state_code_val) . '</div>';
        } else {
            echo '<div class="buyer-detail-line"><span class="label-sm">State Code :</span> None Provided</div>';
        }
        
        // Address (multi-line safe)
        $address_val = isset($salesorders_data_group['address']) ? trim($salesorders_data_group['address']) : '';
        if (!empty($address_val)) {
            echo '<div class="buyer-detail-line"><span class="label-sm">Address :</span> ' . nl2br(htmlspecialchars($address_val)) . '</div>';
        } else {
            echo '<div class="buyer-detail-line"><span class="label-sm">Address :</span> None Provided</div>';
        }
        
        // PO Number
        echo '<div class="buyer-detail-line"><span class="label-sm">PO Number :</span> ' . safe_value(isset($salesorders_data_group['po_number']) ? $salesorders_data_group['po_number'] : '', 'N/A') . '</div>';
        
        // PO Date
        $po_date_val = isset($salesorders_data_group['po_date']) ? $salesorders_data_group['po_date'] : '';
        if (!empty($po_date_val) && $po_date_val !== '0000-00-00') {
            echo '<div class="buyer-detail-line"><span class="label-sm">PO Date :</span> ' . date('d-m-Y', strtotime($po_date_val)) . '</div>';
        } else {
            echo '<div class="buyer-detail-line"><span class="label-sm">PO Date :</span> N/A</div>';
        }
        
        // PO Status
        echo '<div class="buyer-detail-line"><span class="label-sm">PO Status :</span> ' . safe_value(isset($salesorders_data_group['po_status']) ? ucfirst($salesorders_data_group['po_status']) : '', 'N/A') . '</div>';
        
        // Attachment
        $attachment_val = isset($salesorders_data_group['attachment']) ? trim($salesorders_data_group['attachment']) : '';
        if (!empty($attachment_val)) {
            echo '<div class="buyer-detail-line"><span class="label-sm">Attachment :</span> <a href="' . base_url() . 'uploads/' . htmlspecialchars($attachment_val) . '" target="_blank">Download</a></div>';
        } else {
            echo '<div class="buyer-detail-line"><span class="label-sm">Attachment :</span> None</div>';
        }
        
        // Enquiry Source
        $enquiry = isset($salesorders_data_group['enquiry']) ? $salesorders_data_group['enquiry'] : '';
        $enquiry_sources = array(1 => 'Mail', 2 => 'Verbal', 3 => 'Just Dial', 4 => 'India Mart');
        $enquiry_text = isset($enquiry_sources[$enquiry]) ? $enquiry_sources[$enquiry] : 'N/A';
        echo '<div class="buyer-detail-line"><span class="label-sm">Enquiry :</span> ' . htmlspecialchars($enquiry_text) . '</div>';
        ?>
    </div>
</td>
                
                <!-- Sales Order Details (Right) -->
                <td width="50%" valign="top">
                    <div class="section-heading">SALES ORDER DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">SO Number:</td><td><?php echo isset($salesorders_data_group['number_fk']) ? $salesorders_data_group['number_fk'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">SO Date:</td><td><?php echo format_print_date($salesorders_data_group['date']); ?></td></tr>
                        <tr><td class="label-sm">Delivery On:</td><td><?php echo format_print_date($salesorders_data_group['exp_date']); ?></td></tr>
                        <tr><td class="label-sm">Enquiry:</td><td>
                            <?php
                            if (isset($salesorders_data_group['enquiry'])) {
                                if ($salesorders_data_group['enquiry'] == '1') echo "By Mail";
                                elseif ($salesorders_data_group['enquiry'] == '2') echo "By Verbal";
                                elseif ($salesorders_data_group['enquiry'] == '3') echo "Just Dial";
                                elseif ($salesorders_data_group['enquiry'] == '4') echo "India Mart";
                                else echo "None";
                            } else {
                                echo "None";
                            }
                            ?>
                        </td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table id="dynamic_field">
            
            <!-- Items Table Header -->
            <?php if (!empty($show_salesorder)) { ?>
            <tr class="items-header">
                <th class="col-sr">Sr.No.</th>
                <th class="col-desc">Description</th>
                <th class="col-hsn">HSN</th>
                <th class="col-qty">Qty</th>
                <th class="col-qty">Unit</th>
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

            <!-- Items Loop - FIXED with HTML decoding -->
            <?php
            $i = 1;
            $sgst_total_amt = 0;
            $cgst_total_amt = 0;
            $igst_total_amt = 0;
            $amt = 0;
            $total_qty = 0;
            $item_counter = 0;
            $formatted_total_qty = '0';
            
            if (!empty($show_salesorder)) {
                foreach ($show_salesorder as $key) {
                    // ---- Section Heading Row ----
                    if (isset($key->product_name) && $key->product_name === '__HEADING__'):
                        $desc = trim($key->description ?? '');
                        
                        // Spacer row
                        if ($desc === '') {
                        ?>
                        <tr style="background-color: #ffffff; border: none; height: 12px;">
                            <td colspan="<?php echo ($gst_type_row == 'I') ? 9 : 10; ?>" style="border: none; height: 12px; padding: 0;">&nbsp;</td>
                        </tr>
                        <?php
                            continue;
                        }

                        $isMain = true;
                        if (isset($key->tag_no) && ($key->tag_no === 'MAIN' || $key->tag_no === 'SUB')) {
                            $isMain = ($key->tag_no === 'MAIN');
                        }
                        $bg = $isMain ? '#e6e0ed' : '#fdeada';
                        $fg = $isMain ? '#5a3d8a' : '#000000';
                        $displayDesc = $isMain ? strtoupper($desc) : $desc;
                        $colspan = ($gst_type_row == 'I') ? 9 : 10;
                    ?>
                    <tr style="background-color: <?php echo $bg; ?> !important; page-break-inside: avoid;">
                        <td colspan="<?php echo $colspan; ?>" style="background-color: <?php echo $bg; ?> !important; color: <?php echo $fg; ?> !important; font-weight: bold; padding: 5px 8px; border: 0.5px solid #000; vertical-align: middle;">
                            <span style="font-family: 'DejaVu Sans', sans-serif;">🏷️</span>
                            <strong><?php echo htmlspecialchars($displayDesc); ?></strong>
                        </td>
                    </tr>
                    <?php
                        continue;
                    endif;
                    // ---- End Heading Row ----

                    $item_counter++;
                    $sgst_total_amt += isset($key->sgst) ? $key->sgst : 0;
                    $cgst_total_amt += isset($key->cgst) ? $key->cgst : 0;
                    $igst_total_amt += isset($key->igst) ? $key->igst : 0;
                    $amt += isset($key->amount) ? $key->amount : 0;
                    $total_qty += isset($key->quantity) ? (float) $key->quantity : 0;
                    
                    // Add class for last item row
                    $row_class = ($item_counter == $non_heading_items_count) ? 'last-item-row' : 'item-row';
                    
                    // FIX: Decode HTML entities in product name and description
                    $product_name = !empty($key->product_name) ? htmlspecialchars(html_entity_decode($key->product_name, ENT_QUOTES, 'UTF-8')) : '';
                    
                    $description = '';
                    if (!empty($key->description)) {
                        $description = html_entity_decode($key->description, ENT_QUOTES, 'UTF-8');
                        $description = str_replace(['<p>', '</p>'], ['', '<br>'], $description);
                        $description = preg_replace('/<p[^>]*>/', '', $description);
                        $description = preg_replace('/<\/p>/', '<br>', $description);
                        $description = strip_tags($description, '<br><strong><b><em><i><u>');
                        $description = trim($description);
                    }
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="col-sr"><?php echo $i; ?></td>
                    <td class="col-desc">
                        <b><?php echo $product_name. " - " .  $key->item_name; ?></b>
                        <?php if (!empty($description)) { ?>
                            <br><span style="font-size: 8px;"><?php echo $description; ?></span>
                        <?php } ?>
                    </td>
                    <td class="col-hsn"><?php echo isset($key->hsn_code) ? $key->hsn_code : '-'; ?></td>
                    <td class="col-qty"><?php echo isset($key->quantity) ? $key->quantity : '0'; ?></td>
                    <td class="col-qty"><?php echo isset($key->unit) ? $key->unit : '-'; ?></td>
                    <td class="col-gst"><?php echo isset($key->gst) ? $key->gst : '0%'; ?></td>
                    
                    <?php if ($gst_type_row != 'I') { ?>
                        <td class="col-sgst"><?php echo isset($key->sgst) ? indian_number_format($key->sgst, 2) : '0.00'; ?></td>
                        <td class="col-cgst"><?php echo isset($key->cgst) ? indian_number_format($key->cgst, 2) : '0.00'; ?></td>
                    <?php } else { ?>
                        <td class="col-tax"><?php echo isset($key->igst) ? indian_number_format($key->igst, 2) : '0.00'; ?></td>
                    <?php } ?>
                    
                    <td class="col-rate"><?php echo isset($key->price) ? indian_number_format($key->price, 2) : '0.00'; ?></td>
                    <td class="col-amount"><?php echo isset($key->amount) ? indian_number_format($key->amount, 2) : '0.00'; ?></td>
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
                // IGST type - 9 columns
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
                // SGST/CGST type - 10 columns
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

            <!-- Total Calculation Section -->
            <?php if ($gst_type_row != 'I') { ?>
                <!-- CGST/SGST Case -->
                <tr class="total-section">
                    <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                    <td><?php echo $formatted_total_qty; ?></td>
                    <td colspan="5" class="text-right"><b>Total Before Tax:</b></td>
                    <td class="col-amount text-right"><b><?php echo indian_number_format(isset($salesorders_data_group['basic_total']) ? $salesorders_data_group['basic_total'] : 0, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>CGST Amount:</b></td>
                    <td class="col-amount text-right"><?php echo indian_number_format($cgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>SGST Amount:</b></td>
                    <td class="col-amount text-right"><?php echo indian_number_format($sgst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="9" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td class="col-amount text-right"><b> <?php echo indian_number_format(isset($salesorders_data_group['total']) ? $salesorders_data_group['total'] : 0, 2); ?></b></td>
                </tr>
            <?php } else { ?>
                <!-- IGST Case -->
                <tr class="total-section">
                    <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                    <td><?php echo $formatted_total_qty; ?></td>
                    <td colspan="4" class="text-right"><b>Total Before Tax:</b></td>
                    <td class="col-amount text-right"><b><?php echo indian_number_format(isset($salesorders_data_group['basic_total']) ? $salesorders_data_group['basic_total'] : 0, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="8" class="text-right"><b>IGST Amount:</b></td>
                    <td class="col-amount text-right"><?php echo indian_number_format($igst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="8" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td class="col-amount text-right"><b> <?php echo indian_number_format(isset($salesorders_data_group['total']) ? $salesorders_data_group['total'] : 0, 2); ?></b></td>
                </tr>
            <?php } ?>
            
            <!-- Grand Total in Words -->
            <tr>
                <td colspan="<?php echo $colspan; ?>" class="text-right amount-words">
                    <b>Grand Total in Words: <?php echo number_to_word(isset($salesorders_data_group['total']) ? $salesorders_data_group['total'] : 0); ?> Only</b>
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
                            // Fallback if no bank details
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
            
            <!-- Additional Sales Order Sections -->
            <?php if (!empty($salesorders_data_group['terms_and_conditions']) || !empty($salesorders_data_group['payment_terms']) || 
                      !empty($salesorders_data_group['process_schedule']) || !empty($salesorders_data_group['taxes']) || 
                      !empty($salesorders_data_group['exclusions'])) { ?>
            
            <!-- Terms & Conditions -->
            <?php if (!empty($salesorders_data_group['terms_and_conditions'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Terms & Conditions:</span> <?php echo $salesorders_data_group['terms_and_conditions']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <!-- Payment Terms -->
            <?php if (!empty($salesorders_data_group['payment_terms'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Payment Terms:</span> <?php echo $salesorders_data_group['payment_terms']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <!-- Process Schedule -->
            <?php if (!empty($salesorders_data_group['process_schedule'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Process Schedule:</span> <?php echo $salesorders_data_group['process_schedule']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <!-- Taxes -->
            <?php if (!empty($salesorders_data_group['taxes'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Taxes:</span> <?php echo $salesorders_data_group['taxes']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <!-- Exclusions -->
            <?php if (!empty($salesorders_data_group['exclusions'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Exclusions:</span> <?php echo $salesorders_data_group['exclusions']; ?>
                </td>
            </tr>
            <?php } ?>
                        <!-- Note -->
            <?php if (!empty($salesorders_data_group['salesorder_memo'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Note:</span> <?php echo $salesorders_data_group['salesorder_memo']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <?php } ?>

           
        </table>

        <!-- Footer -->
        <div class="footer">
            <?php echo isset($salesorders_data_group['salesorder_footer']) ? $salesorders_data_group['salesorder_footer'] : ''; ?><br>
            This is a Computer Generated Sales Order<br>
            <?php echo $settings['company_name']; ?> | <?php echo $settings['address']; ?>
        </div>
    </div>

    <!-- Add jQuery for the script to work -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check if any element with id="igst" exists and has value "igst"
            if ($("#igst").length > 0 && $("#igst").val() == "igst") {
                $(".gst").hide();
                $(".igst").show();
            } else {
                $(".gst").show();
                $(".igst").hide();
            }
        });
    </script>
</body>

</html>
