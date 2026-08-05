<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Delivery Challan</title>
    <meta name="description" content="Delivery Challan print page">
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

        /* Column width classes - optimized for Delivery Challan */
        .col-sr { width: 4%; text-align: center; }
        .col-desc { width: 26%; }
        .col-hsn { width: 8%; text-align: center; }
        .col-qty { width: 4%; text-align: center; }
        .col-unit { width: 4%; text-align: center; }
        .col-gst { width: 4%; text-align: center; }
        .col-tax { width: 7%; text-align: center; }
        .col-cgst { width: 9%; text-align: center; }
        .col-sgst { width: 9%; text-align: center; }
        .col-rate { width: 9%; text-align: center; }
        .col-Discount { width: 8%; text-align: center; }
        .col-amount { width: 12%; text-align: right; }

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

        /* PO details */
        .po-details {
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

        /* Nested table in invoice details - no borders */
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

        /* Note section - single line */
        .note-section {
            padding: 4px 5px;
            border-top: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
        }
        
        .note-content {
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        $gst_type_row = '';
        $total_items = count($show_invoice);
        
        foreach ($show_invoice as $key) {
            if ($key->gst_type == 'I') {
                $colspan = "10";
                $colspan1 = "4";
                $colspan2 = "6";
                $colspan3 = "8";
                $colspan4 = "1";
                $colspan5 = "2";
                $colspan6 = "3";
                $colspan7 = "5";
                $gst_type_row = $key->gst_type;
            } else {
                $colspan = "11";
                $colspan1 = "5";
                $colspan2 = "6";
                $colspan3 = "9";
                $colspan4 = "1";
                $colspan5 = "2";
                $colspan6 = "4";
                $colspan7 = "4";
                $gst_type_row = $key->gst_type;
            }
            break;
        }
        
        // Calculate tax totals
        $total_before_tax = 0;
        $total_cgst = 0;
        $total_sgst = 0;
        $total_igst = 0;
        
        foreach ($show_invoice as $key) {
            $total_before_tax += $key->amount;
            $total_cgst += $key->cgst;
            $total_sgst += $key->sgst;
            $total_igst += $key->igst;
        }
        
        $total_tax = $total_cgst + $total_sgst + $total_igst;
        $grand_total = $total_before_tax;
        
        // Calculate how many blank rows to add
        $target_total_rows = 12;
        $items_count = count($show_invoice);
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        
        require_once(APPPATH . '/third_party/amount_convert.php');
        ?>

        <!-- Company Header - Separate Table -->
        <caption>DELIVERY CHALLAN</caption>
        <table class="company-header">
            <tr>
                <td width="25%" align="center" valign="middle">
                    <img class="company-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo" >
                </td>
                <td width="75%" valign="top">
                    <div class="company-name"><?php echo $settings['company_name']; ?></div>
                    <div><span class="label">Address :</span> <?php echo $settings['address']; ?></div>
                    <div><span class="label">State Code :</span> <?php echo $settings['state_code']; ?></div>
                    <div><span class="label">GST Number :</span> <?php echo strtoupper($settings['company_gst']); ?></div>
                    <div><span class="label">PAN Number :</span> <?php echo strtoupper($settings['company_pan']); ?></div>
                    <div><span class="label">Mobile :</span> <?php echo $settings['mobile']; ?></div>
                    <div><span class="label">Email :</span> <?php echo $settings['email']; ?></div>
                </td>
            </tr>
        </table>

        <!-- Billing, Shipping and Challan Details - Separate Table -->
        <table class="details-section">
            <tr>
                <!-- Billing To (Left) -->
                <td width="33%" valign="top">
                    <div class="section-heading">BILL TO</div>
                    <div><span class="label-sm"></span> <?php echo $invoice_data_group['company_name']; ?></div>
                    <div><span class="label-sm"></span> <?php echo $invoice_data_group['address']; ?></div>
                    <div><span class="label-sm">State Code:</span> <?php echo $invoice_data_group['state_code'] ?: 'None Provided'; ?></div>
                    <div><span class="label-sm">GST Number:</span> <?php echo $invoice_data_group['gst'] ?: 'None Provided'; ?></div>
                    <div><span class="label-sm">PAN Number:</span> <?php echo $invoice_data_group['pancard'] ?: 'None Provided'; ?></div>
                    <div><span class="label-sm">Customer Name:</span> <?php echo $invoice_data_group['fullname']; ?></div>
                </td>
                
                <!-- Shipping To (Middle) -->
                <td width="33%" valign="top">
                    <div class="section-heading">SHIP TO</div>
                    <?php if (isset($invoice_data_group['shipping_address']) && !empty($invoice_data_group['shipping_address'])) { ?>
                        <div><?php echo nl2br($invoice_data_group['shipping_address']); ?></div>
                    <?php } else { ?>
                        <div>Same as Billing Address</div>
                    <?php } ?>
                </td>
                
                <!-- Challan Details with Supplier Code (Right) -->
                <td width="34%" valign="top">
                    <div class="section-heading">CHALLAN DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">Challan No:</td><td><?php echo $invoice_data_group['invoice_number']; ?></td></tr>
                        <tr><td class="label-sm">Challan Date:</td><td><?php echo date('d-m-Y', strtotime($invoice_data_group['invoice_date'])); ?></td></tr>
                        <tr><td class="label-sm">Delivery Note:</td><td><?php echo $invoice_data_group['delivery_note_no'] ?: 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Delivery Date:</td><td><?php echo $invoice_data_group['delivery_date'] ?: 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Dispatch:</td><td><?php echo $invoice_data_group['despatch_through'] ?: 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Vehicle No:</td><td><?php echo $invoice_data_group['vehicle_no'] ?: 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Payment Mode:</td><td>
                            <?php 
                            if ($invoice_data_group['payment_method'] == '1') echo "Cash";
                            elseif ($invoice_data_group['payment_method'] == '2') echo "Cheque";
                            elseif ($invoice_data_group['payment_method'] == '3') echo "Net Banking";
                            else echo "None";
                            ?>
                        </td></tr>
                    </table>
                    
                    <!-- Supplier Code - Moved to right side -->
                    <div style="margin-top:5px; padding-top:3px;">
                        <div class="po-details">
                            <span class="label-sm">Customer PO:</span> 
                            <?php echo $invoice_data_group['customer_po'] ?: 'None Provided'; ?>
                        </div>
                        <div class="po-details">
                            <span class="label-sm">PO Date:</span> 
                            <?php echo $invoice_data_group['po_date'] ?: 'None Provided'; ?>
                        </div>
                        <!-- <div class="po-details">
                            <span class="label-sm">Supplier Code:</span> 
                            <?php echo $invoice_data_group['supplier_code'] ?: 'None Provided'; ?>
                        </div> -->
                    </div>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table id="dynamic_field">
            
            <!-- Items Table Header -->
            <?php foreach ($show_invoice as $key) { ?>
            <tr class="items-header">
                <th class="col-sr">No</th>
                <th class="col-desc">Description</th>
                <th class="col-hsn">HSN/SAC</th>
                <th class="col-qty">Qty</th>
                <th class="col-unit">Unit</th>
                <th class="col-gst">TAX</th>
                <?php if ($key->gst_type != 'I') { ?>
                    <th class="col-sgst">SGST</th>
                    <th class="col-cgst">CGST</th>
                <?php } else { ?>
                    <th class="col-tax">IGST</th>
                <?php } ?>
             
                <th class="col-rate">Price</th>
                   <th class="col-Discount">Dis(%)</th>
                <th class="col-amount">&nbsp;&nbsp;Amount&nbsp;&nbsp;</th>
            </tr>
            <?php break; } ?>

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
            
            foreach ($show_invoice as $key) {
                $item_counter++;
                $sgst_total_amt += $key->sgst;
                $cgst_total_amt += $key->cgst;
                $igst_total_amt += $key->igst;
                $amt += $key->amount;
                $total_qty += isset($key->quantity) ? (float) $key->quantity : 0;
                
                // Add class for last item row to add bottom border
                $row_class = ($item_counter == $items_count) ? 'last-item-row' : 'item-row';
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="col-sr"><?php echo $i; ?></td>
                    <td class="col-desc">
                        <b><?php echo $key->product_name. " - " .  $key->item_name; ?></b>
                        <?php if (!empty($key->description)) { ?>
                            <br><span style="font-size: 8px;"><?php echo $key->description; ?></span>
                        <?php } ?>
                    </td>
                    <td class="col-hsn"><?php echo $key->hsn_code; ?></td>
                    <td class="col-qty"><?php echo indian_number_format($key->quantity, ); ?></td>
                    <td class="col-unit"><?php echo $key->unit; ?></td>
                    <td class="col-gst"><?php echo $key->gst; ?></td>
                    
                    <?php if ($key->gst_type != 'I') { ?>
                        <td class="col-sgst"><?php echo indian_number_format($key->sgst, 2); ?></td>
                        <td class="col-cgst"><?php echo indian_number_format($key->cgst, 2); ?></td>
                    <?php } else { ?>
                        <td class="col-tax"><?php echo indian_number_format($key->igst, 2); ?></td>
                    <?php } ?>
                    
                    <td class="col-rate"><?php echo indian_number_format($key->price, 2); ?></td>
                    <td class="col-Discount"><?php echo indian_number_format($key->discount, 2); ?></td>
                    <td class="col-amount"><?php echo indian_number_format($key->amount, 2); ?></td>
                </tr>
            <?php
                $i++;
            }

            // Round all totals to 2 decimal places
            $amt = round($amt, 2);
            $sgst_total_amt = round($sgst_total_amt, 2);
            $cgst_total_amt = round($cgst_total_amt, 2);
            $igst_total_amt = round($igst_total_amt, 2);
            
            $formatted_total_qty = (fmod($total_qty, 1.0) == 0.0)
                ? number_format($total_qty, 0)
                : number_format($total_qty, 2);
            
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
                        <td class="col-rate">&nbsp;</td>
                        <td class="col-Discount">&nbsp;</td>
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
                        <td class="col-rate">&nbsp;</td>
                        <td class="col-Discount">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                    </tr>
                <?php }
            }
            ?>

            <!-- Total Calculation Section -->
            <?php 
                $grand_total_gst  = round($amt + $sgst_total_amt + $cgst_total_amt, 0);
                $grand_total_igst = round($amt + $igst_total_amt, 0);
            ?>
            <?php if ($gst_type_row == 'I') { ?>
                <!-- IGST Case -->
                <tr class="total-section">
                    <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                    <td><?php echo $formatted_total_qty; ?></td>
                    <td colspan="5" class="text-right"><b>Total Before Tax:</b></td>
                    <td class="col-amount"><b><?php echo indian_number_format($amt, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="9" class="text-right"><b>IGST Amount:</b></td>
                    <td class="col-amount"><?php echo indian_number_format($igst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="9" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td class="col-amount"><b><?php echo indian_number_format($grand_total_igst, 0); ?></b></td>
                </tr>
            <?php } else { ?>
                <!-- CGST/SGST Case -->
                <tr class="total-section">
                    <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                    <td><?php echo $formatted_total_qty; ?></td>
                    <td colspan="6" class="text-right"><b>Total Before Tax:</b></td>
                    <td class="col-amount"><b><?php echo indian_number_format($amt, 2); ?></b></td>
                </tr>
                <tr class="total-section">
                    <td colspan="10" class="text-right"><b>CGST Amount:</b></td>
                    <td class="col-amount"><?php echo indian_number_format($cgst_total_amt, 2); ?></td>
                </tr>
                <tr class="total-section">
                    <td colspan="10" class="text-right"><b>SGST Amount:</b></td>
                    <td class="col-amount"><?php echo indian_number_format($sgst_total_amt, 2); ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="10" class="text-right"><b>Grand Total (&#8377;):</b></td>
                    <td class="col-amount"><b><?php echo indian_number_format($grand_total_gst, 0); ?></b></td>
                </tr>
            <?php } ?>

            <!-- Grand Total in Words -->
            <?php 
                $words_colspan  = ($gst_type_row == 'I') ? 10 : 11;
                $grand_total_words = ($gst_type_row == 'I') ? $grand_total_igst : $grand_total_gst;
            ?>
            <tr>
                <td colspan="<?php echo $words_colspan; ?>" class="text-right amount-words">
                    <b>Grand Total in Words: <?php echo number_to_word($grand_total_words); ?> Only</b>
                </td>
            </tr>

            <!-- Note Section - Single Line -->
            <tr>
                <td colspan="<?php echo $words_colspan; ?>" class="note-section">
                    <span class="label">Note:</span> 
                    <span class="note-content"><?php echo $settings['invoice_memo']; ?></span>
                </td>
            </tr>

            <!-- Bank Details & Signature Section - Combined Row (No Receiver's Signatory) -->
            <tr>
                <!-- Bank Details - Vertical Layout with Parsing -->
                <td colspan="<?php echo $colspan1; ?>" class="bank-details" style="padding: 8px 5px;">
                    <div class="section-heading">Bank Details:</div>
                    <div class="bank-details-content">
                        <?php 
                        $bank_text = $settings['invoice_notes'];
                        
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
                
                <!-- Authorised Signatory Only (Right side) - No Receiver's Signatory -->
                <td colspan="<?php echo $colspan2; ?>" class="signature" style="vertical-align: bottom; text-align: center;">
                    <?php if (isset($stamp) && $stamp == "yes") { ?>
                        <div><img class="stamp-img" src="<?php echo base_url() . $settings['company_stamp']; ?>"></div>
                    <?php } ?>
                    <div>Authorised Signatory</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <?php echo $settings['address']; ?><br>
            This is a Computer Generated Challannnnnnn
        </div>
    </div>
</body>
</html>