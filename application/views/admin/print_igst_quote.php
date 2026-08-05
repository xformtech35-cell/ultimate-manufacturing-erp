<?php require_once(APPPATH . 'third_party/amount_convert.php'); ?>
<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Quotation</title>
    <meta name="description" content="Quotation print page">
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

        /* Column width classes - optimized with DISCOUNT column after Price */
        .col-sr { width: 3%; text-align: center; }
        .col-desc { width: 25%; }
        .col-hsn { width: 8%; text-align: center; }
        .col-qty { width: 4%; text-align: center; }
        .col-unit { width: 5%; text-align: center; }
        .col-gst { width: 5%; text-align: center; }
        .col-tax { width: 12%; text-align: center; } /* Shown when CGST/SGST are hidden */
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

        /* Quotation details */
        .quotation-details {
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

        /* Nested table in quotation details - no borders */
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
        $total_items = !empty($show_quotation) ? count($show_quotation) : 0;
        
        // Get GST type from first record if available
        if (!empty($show_quotation)) {
            foreach ($show_quotation as $key) {
                if ($key->gst_type == 'I') {
                    $colspan = "10";
                    $colspan1 = "5";
                    $colspan2 = "5";
                    $colspan3 = "9";
                    $colspan4 = "1";
                    $colspan5 = "3";
                    $colspan6 = "3";
                    $colspan7 = "4";
                    $gst_type_row = $key->gst_type;
                } else {
                    $colspan = "11";
                    $colspan1 = "6";
                    $colspan2 = "5";
                    $colspan3 = "10";
                    $colspan4 = "1";
                    $colspan5 = "4";
                    $colspan6 = "4";
                    $colspan7 = "3";
                    $gst_type_row = $key->gst_type;
                }
                break;
            }
        }

        $column_count = ($gst_type_row == 'I') ? 10 : 11;
        $summary_label_colspan = $column_count - 1;
        
        // Calculate how many blank rows to add
        $target_total_rows = 16;
        $items_count = !empty($show_quotation) ? count($show_quotation) : 0;
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        
        // Check if sez key exists in estimates_data_group
        $sez_value = isset($estimates_data_group['sez']) ? $estimates_data_group['sez'] : 0;
        ?>

        <!-- Company Header - Separate Table -->
        <caption>QUOTATION</caption>
        <table class="company-header">
            <tr>
                <td width="40%" align="center" valign="middle">
                    <img class="company-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo" >
                </td>
                <td width="50%" valign="top">
                    <div class="company-name"><?php echo $settings['company_name']; ?></div>
                    <div><span class="label-sm">GST Number :</span> <?php echo strtoupper($settings['company_gst']); ?></div>
                    <div><span class="label-sm">PAN Number :</span> <?php echo strtoupper($settings['company_pan']); ?></div>
                    <div><span class="label-sm">Mobile :</span> <?php echo $settings['mobile']; ?></div>
                    <div><span class="label-sm">Email :</span> <?php echo $settings['email']; ?></div>
                    <div><span class="label">Address :</span> <?php echo $settings['address']; ?></div>
                    <?php if (!empty($settings['quotation_subheading'])) { ?>
                        <div><span class="label">Subheading :</span> <?php echo $settings['quotation_subheading']; ?></div>
                    <?php } ?>
                </td>
            </tr>
        </table>

        <!-- Buyer and Quotation Details - Separate Table -->
        <table class="details-section">
            <tr>
                <!-- Buyer Details (Left) -->
                <td width="50%" valign="top">
                    <div class="section-heading">BUYER</div>
                    <div><span class="label-sm">Name</span> <?php echo isset($estimates_data_group['company_name']) ? $estimates_data_group['company_name'] : ''; ?></div>
                    <div><span class="label-sm">Address</span> <?php 
                    if (isset($estimates_data_group['address']) && $estimates_data_group['address']) {
                        $address = $estimates_data_group['address'];
                        if (is_array($address)) {
                            echo implode(', ', array_filter($address));
                        } else {
                            echo $address;
                        }
                    } else {
                        echo 'None Provided';
                    }
                    ?></div>
                    <div><span class="label-sm">GST Number:</span> <?php echo isset($estimates_data_group['gst']) && $estimates_data_group['gst'] ? strtoupper($estimates_data_group['gst']) : 'None Provided'; ?></div>
                    <div><span class="label-sm">PAN Number:</span> <?php echo isset($estimates_data_group['pancard']) && $estimates_data_group['pancard'] ? $estimates_data_group['pancard'] : 'None Provided'; ?></div>
                    <div><span class="label-sm">Customer Name:</span> <?php echo isset($estimates_data_group['fullname']) ? $estimates_data_group['fullname'] : ''; ?></div>
                    <div><span class="label-sm">Customer Code:</span> <?php echo isset($estimates_data_group['c_code']) ? $estimates_data_group['c_code'] : ''; ?></div>
                </td>
                
                <!-- Quotation Details (Right) -->
                <td width="50%" valign="top">
                    <div class="section-heading">QUOTATION DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">Quotation No:</td><td><?php echo isset($estimates_data_group['number_fk']) ? $estimates_data_group['number_fk'] : ''; ?></td></tr>
                        <tr><td class="label-sm">Quotation Date:</td><td><?php echo isset($estimates_data_group['date']) ? date('d-m-Y', strtotime($estimates_data_group['date'])) : ''; ?></td></tr>
                        <tr><td class="label-sm">Expires on:</td><td><?php echo isset($estimates_data_group['exp_date']) ? date('d-m-Y', strtotime($estimates_data_group['exp_date'])) : ''; ?></td></tr>
                        <tr><td class="label-sm">Enquiry:</td><td>
                            <?php
                            if (isset($estimates_data_group['enquiry'])) {
                                if ($estimates_data_group['enquiry'] == '1') echo "By Mail";
                                elseif ($estimates_data_group['enquiry'] == '2') echo "By Verbal";
                                elseif ($estimates_data_group['enquiry'] == '3') echo "Just Dial";
                                elseif ($estimates_data_group['enquiry'] == '4') echo "India Mart";
                                else echo "None";
                            } else {
                                echo "None";
                            }
                            ?>
                        </td></tr>
                        <?php if (!empty($estimates_data_group['quotation_subheading'])) { ?>
                        <tr><td class="label-sm">Project:</td><td><?php echo $estimates_data_group['quotation_subheading']; ?></td></tr>
                        <?php } ?>
                        <?php 
                        $session_data_head1 = $this->session->userdata('session_data_head');
                        $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
                        if ($_has_project_master && !empty($estimates_data_group['project_code'])) { 
                        ?>
                        <tr><td class="label-sm">Project Code:</td><td><?php echo htmlspecialchars($estimates_data_group['project_code']); ?></td></tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table id="dynamic_field">
            
            <!-- Items Table Header -->
            <?php if (!empty($show_quotation)) { ?>
            <tr class="items-header">
                <th class="col-sr">No</th>
                <th class="col-desc">Description</th>
                <th class="col-hsn">HSN/SAC</th>
                <th class="col-qty">Qty</th>
                <th class="col-unit">Unit</th>
                <th class="col-gst">TAX </th>
                <?php if ($gst_type_row != 'I') { ?>
                    <th class="col-sgst">SGST</th>
                    <th class="col-cgst">CGST</th>
                <?php } else { ?>
                    <th class="col-tax">IGST</th>
                <?php } ?>
                <th class="col-discount">Dis(%)</th>
                <th class="col-rate">Price</th>
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
            $gst_type = !empty($show_quotation) ? $show_quotation[0]->gst_type : 'S';
            
            if (!empty($show_quotation)) {
                foreach ($show_quotation as $key) {
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
                        <b><?php echo $key->product_name . " - " .  $key->item_name; ?></b>
                        <?php if (!empty($key->description)) { ?>
                            <br><span style="font-size: 8px;"><?php echo $key->description; ?></span>
                        <?php } ?>
                    </td>
                    <td class="col-hsn"><?php echo $key->hsn_code; ?></td>
                    <td class="col-qty"><?php echo $key->quantity; ?></td>
                    <td class="col-unit"><?php echo $key->unit; ?></td>
                    <td class="col-gst"><?php echo $key->gst; ?></td>
                    
                    <?php if ($gst_type_row != 'I') { ?>
                        <td class="col-sgst"><?php echo indian_number_format($key->sgst, 2); ?></td>
                        <td class="col-cgst"><?php echo indian_number_format($key->cgst, 2); ?></td>
                    <?php } else { ?>
                        <td class="col-tax"><?php echo indian_number_format($key->igst, 2); ?></td>
                    <?php } ?>
                    
                    <td class="col-discount"><?php echo $key->discount; ?>%</td>
                    <td class="col-rate"><?php echo indian_number_format($key->price, 2); ?></td>
                    <td class="col-amount"><?php echo indian_number_format($key->amount, 2); ?></td>
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
                        <td class="col-hsn">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-unit">&nbsp;</td>
                        <td class="col-gst">&nbsp;</td>
                        <td class="col-tax">&nbsp;</td>
                        <td class="col-discount">&nbsp;</td>
                        <td class="col-rate">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                    </tr>
                <?php }
            } else {
                // SGST/CGST type - 11 columns
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
                        <td class="col-discount">&nbsp;</td>
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
                    <td colspan="3" class="text-right"><b>Total Qty:</td><td><?php echo (fmod($total_qty, 1.0) == 0.0) ? number_format($total_qty, 0) : number_format($total_qty, 2); ?></td> <td colspan="6" class="text-right" >Total Before Tax:</b></td>
                    <td colspan="1" class="text-right"><b><?php echo number_format(isset($estimates_data_group['basic_total']) ? $estimates_data_group['basic_total'] : 0, 2); ?></b></td>
                </tr>

                <tr class="total-section">
                    <td colspan="<?php echo $summary_label_colspan; ?>" class="text-right"><b>CGST Amount:</b></td>
                    <td colspan="1" class="text-right"><?php echo indian_number_format($cgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="<?php echo $summary_label_colspan; ?>" class="text-right"><b>SGST Amount:</b></td>
                    <td colspan="1" class="text-right"><?php echo indian_number_format($sgst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="<?php echo $summary_label_colspan; ?>" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td colspan="1" class="text-right"><b> <?php echo indian_number_format(isset($estimates_data_group['total']) ? $estimates_data_group['total'] : 0, 2); ?></b></td>
                </tr>
            <?php } else { ?>
                <!-- IGST Case -->
                <tr class="total-section">
                    <td colspan="<?php echo $summary_label_colspan; ?>" class="text-right"><b>Total Qty: <?php echo (fmod($total_qty, 0) == 0.0) ? number_format($total_qty, 0) : number_format($total_qty, 2); ?> | Total Before Tax:</b></td>
                    <td colspan="1" class="text-right"><b><?php echo indian_number_format($estimates_data_group['basic_total'], 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="<?php echo $summary_label_colspan; ?>" class="text-right"><b>IGST Amount:</b></td>
                    <td colspan="1" class="text-right"><?php echo indian_number_format($igst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="<?php echo $summary_label_colspan; ?>" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td colspan="1" class="text-right">
                        <b> <?php
                        if ($sez_value == 1) {
                        echo indian_number_format((isset($estimates_data_group['total']) ? $estimates_data_group['total'] : 0) - $igst_total_amt, 2);
                        } else {
                            echo indian_number_format(isset($estimates_data_group['total']) ? $estimates_data_group['total'] : 0, 2);
                        }
                        ?></b>
                    </td>
                </tr>
            <?php } ?>
            
            <!-- Grand Total in Words -->
            <tr>
                <td colspan="<?php echo $colspan; ?>" class="text-right amount-words">
                    <b>Grand Total in Words: 
                    <?php
                    // include amount_convert
                    require_once(APPPATH . '/third_party/amount_convert.php');
                    
                    if ($sez_value == 1 && $gst_type_row == 'I') {
                        echo number_to_word((isset($estimates_data_group['total']) ? $estimates_data_group['total'] : 0) - $igst_total_amt);
                    } else {
                        echo number_to_word(isset($estimates_data_group['total']) ? $estimates_data_group['total'] : 0);
                    }
                    ?> Only</b>
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
            
            <!-- Additional Quotation Sections -->
            <?php if (!empty($estimates_data_group['terms_and_conditions']) || !empty($estimates_data_group['payment_terms']) || 
                      !empty($estimates_data_group['process_schedule']) || !empty($estimates_data_group['taxes']) || 
                      !empty($estimates_data_group['exclusions'])) { ?>
            
            <!-- Terms & Conditions -->
            <?php if (!empty($estimates_data_group['terms_and_conditions'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Terms & Conditions:</span> <?php echo $estimates_data_group['terms_and_conditions']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <!-- Payment Terms -->
            <?php if (!empty($estimates_data_group['payment_terms'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Payment Terms:</span> <?php echo $estimates_data_group['payment_terms']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <!-- Process Schedule -->
            <?php if (!empty($estimates_data_group['process_schedule'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Process Schedule:</span> <?php echo $estimates_data_group['process_schedule']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <!-- Taxes -->
            <?php if (!empty($estimates_data_group['taxes'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Taxes:</span> <?php echo $estimates_data_group['taxes']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <!-- Exclusions -->
            <?php if (!empty($estimates_data_group['exclusions'])) { ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="padding: 3px 5px;">
                    <span class="label">Exclusions:</span> <?php echo $estimates_data_group['exclusions']; ?>
                </td>
            </tr>
            <?php } ?>
            
            <?php } ?>
        </table>

        <!-- Footer -->
        <div class="footer">
            <?php echo isset($estimates_data_group['quotation_footer']) ? $estimates_data_group['quotation_footer'] : $settings['address']; ?><br>
            This is a Computer Generated Quotation
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