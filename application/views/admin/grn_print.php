<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>GRN - <?php echo isset($grn_data_group['grn_number']) ? $grn_data_group['grn_number'] : ''; ?></title>
    <meta name="description" content="GRN - Goods Received Note print page">
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

        .company-logo {
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Supplier/GRN Details - Separate Table */
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

        /* Column width classes - optimized for GRN */
        .col-sr { width: 5%; text-align: center; }
        .col-item { width: 18%; text-align: left; }
        .col-desc { width: 15%; text-align: left; }
        .col-qty { width: 5%; text-align: center; }
        .col-unit { width: 5%; text-align: center; }
        .col-hsn { width: 6%; text-align: center; }
        .col-gst { width: 5%; text-align: center; }
        .col-sgst { width: 6%; text-align: center; }
        .col-cgst { width: 6%; text-align: center; }
        .col-received { width: 6%; text-align: center; }
        .col-pending { width: 6%; text-align: center; }
        .col-price { width: 8%; text-align: right; }

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
            min-width: 80px;
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
            padding: 5px 3px;
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
            background: #fef9e6;
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
        
        /* Note/message styling */
        .message-box {
            padding: 6px 5px;
            font-style: italic;
            color: #333;
        }
        
        /* Notes section */
        .notes-section {
            padding: 5px;
            border: 0.5px solid #000;
            border-top: none;
            margin-top: -0.5px;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <?php
        $colspan = "10"; // Total columns in table
        $colspan1 = "6"; // Left column for bank details
        $colspan2 = "6"; // Right column for signature
        
        // Calculate how many blank rows to add
        $target_total_rows = 15;
        $items_count = !empty($show_grn) ? count($show_grn) : 0;
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        
        require_once(APPPATH . '/third_party/amount_convert.php');
        ?>

        <!-- Company Header - Separate Table -->
        <caption>GOODS RECEIVED NOTE (GRN)</caption>
        <table class="company-header">
            <tr>
                <td width="25%" align="center" valign="middle">
                    <?php if (!empty($settings['company_logo'])): ?>
                        <img class="company-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo">
                    <?php else: ?>
                        <div style="height: 60px;"></div>
                    <?php endif; ?>
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

        <!-- Supplier and GRN Details - Separate Table -->
        <table class="details-section">
            <tr>
                <!-- Supplier Details (Left) -->
                <td width="50%" valign="top">
                    <div class="section-heading">SUPPLIER DETAILS</div>
                    <div><span class="label-sm">Name</span> <?php echo isset($grn_data_group['company_name']) ? htmlspecialchars($grn_data_group['company_name']) : 'N/A'; ?></div>
                    <div><span class="label-sm">Address</span> <?php echo isset($grn_data_group['address']) ? htmlspecialchars($grn_data_group['address']) : 'N/A'; ?></div>
                    <div><span class="label-sm">GST Number:</span> <?php echo isset($grn_data_group['gst']) ? strtoupper(htmlspecialchars($grn_data_group['gst'])) : 'N/A'; ?></div>
                    <div><span class="label-sm">PAN Number:</span> <?php echo isset($grn_data_group['pancard']) ? htmlspecialchars($grn_data_group['pancard']) : 'N/A'; ?></div>
                    <div><span class="label-sm">Contact Person:</span> <?php echo isset($grn_data_group['fullname']) ? htmlspecialchars($grn_data_group['fullname']) : 'N/A'; ?></div>
                </td>
                
                <!-- GRN Details (Right) -->
                <td width="50%" valign="top">
                    <div class="section-heading">GRN DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">GRN Number:</td><td><?php echo isset($grn_data_group['grn_number']) ? htmlspecialchars($grn_data_group['grn_number']) : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">GRN Date:</td><td>
                            <?php 
                            if (isset($grn_data_group['date']) && $grn_data_group['date'] != '0000-00-00') {
                                echo date('d-m-Y', strtotime($grn_data_group['date']));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td></tr>
                        <tr><td class="label-sm">PO Number:</td><td><?php echo isset($grn_data_group['po_number_fk']) ? htmlspecialchars($grn_data_group['po_number_fk']) : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Total Amount:</td><td>&#8377; <?php echo isset($grn_data_group['total']) ? number_format(floatval($grn_data_group['total']), 2) : '0.00'; ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>
        
  <!-- </tr>
        </table> -->
        <!-- Main Items Table -->
        <table id="dynamic_field">
            
            <!-- Items Table Header -->
            <tr class="items-header">
                <th class="col-sr">Sr.No.</th>
                <th class="col-item">Item</th>
                <th class="col-desc">Description</th>
                <th class="col-qty">Qty</th>
                <th class="col-unit">Unit</th>
                <th class="col-hsn">HSN</th>
                <th class="col-gst">TAX%</th>
                <th class="col-sgst">SGST</th>
                <th class="col-cgst">CGST</th>
                <th class="col-received">Received</th>
                <th class="col-pending">Pending</th>
                <th class="col-price">Price</th>
            </tr>

            <!-- Items Loop - FIXED with HTML decoding for both Item and Description -->
            <?php
            $i = 1;
            $grand_total = 0;
            $item_counter = 0;
            $total_qty = 0;
            $total_sgst = 0;
            $total_cgst = 0;
            $total_igst = 0;
            
            if (!empty($show_grn) && is_array($show_grn)):
                foreach ($show_grn as $item):
                    $item_counter++;
                    $row_class = ($item_counter == $items_count) ? 'last-item-row' : 'item-row';
                    
                    // Calculate line total
                    $price = !empty($item->price) ? floatval($item->price) : 0;
                    $received_qty = !empty($item->received_quantity) ? floatval($item->received_quantity) : 1;
                    $line_total = $price * $received_qty;
                    $grand_total += $line_total;
                    $total_qty += !empty($item->quantity) ? floatval($item->quantity) : 0;
                    
                    // Calculate SGST and CGST totals
                    $sgst_amt = !empty($item->sgst) ? floatval($item->sgst) : 0;
                    $cgst_amt = !empty($item->cgst) ? floatval($item->cgst) : 0;
                    $igst_amt = !empty($item->igst) ? floatval($item->igst) : 0;
                    $total_sgst += $sgst_amt;
                    $total_cgst += $cgst_amt;
                    $total_igst += $igst_amt;
                    
                    // FIX: Decode HTML entities in product name and remove tags
                    $product_name = '';
                    if (!empty($item->product_name)) {
                        $product_name = html_entity_decode($item->product_name, ENT_QUOTES, 'UTF-8');
                        $product_name = str_replace(['<p>', '</p>'], ['', ' '], $product_name);
                        $product_name = preg_replace('/<p[^>]*>/', '', $product_name);
                        $product_name = preg_replace('/<\/p>/', ' ', $product_name);
                        $product_name = strip_tags($product_name);
                        $product_name = trim($product_name);
                        $product_name = htmlspecialchars($product_name);
                    } else {
                        $product_name = '-';
                    }
                    
                    // FIX: Decode HTML entities in description and remove <p> tags
                    $description = '';
                    if (!empty($item->description)) {
                        $description = html_entity_decode($item->description, ENT_QUOTES, 'UTF-8');
                        $description = str_replace(['<p>', '</p>'], ['', '<br>'], $description);
                        $description = preg_replace('/<p[^>]*>/', '', $description);
                        $description = preg_replace('/<\/p>/', '<br>', $description);
                        $description = strip_tags($description, '<br><strong><b><em><i><u>');
                        $description = trim($description);
                    }
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="col-sr"><?php echo $i; ?></td>
                    <td class="col-item"><?php echo $product_name . " - " .  $item->item_name;?></td>
                    <td class="col-desc">
                        <?php if (!empty($description)): ?>
                            <span style="font-size: 8px;"><?php echo $description; ?></span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="col-qty"><?php echo !empty($item->quantity) ? $item->quantity : '0'; ?></td>
                    <td class="col-unit"><?php echo !empty($item->unit) ? htmlspecialchars($item->unit) : '-'; ?></td>
                    <td class="col-hsn"><?php echo !empty($item->hsn_code) ? htmlspecialchars($item->hsn_code) : '-'; ?></td>
                    <td class="col-gst"><?php echo !empty($item->gst) ? $item->gst : '0'; ?></td>
                    <td class="col-sgst"><?php echo !empty($item->sgst) ? number_format($item->sgst, 2) : '0.00'; ?></td>
                    <td class="col-cgst"><?php echo !empty($item->cgst) ? number_format($item->cgst, 2) : '0.00'; ?></td>
                    <td class="col-received"><?php echo !empty($item->received_quantity) ? $item->received_quantity : '0'; ?></td>
                    <td class="col-pending"><?php echo !empty($item->pending_quantity) ? $item->pending_quantity : '0'; ?></td>
                    <td class="col-price">&#8377; <?php echo number_format($price, 2); ?></td>
                </tr>
            <?php
                    $i++;
                endforeach;
            else:
                // If no items, show message but continue for empty rows
                ?>
                <tr>
                    <td colspan="11" class="text-center" style="padding: 10px;">No items found in this GRN</td>
                </tr>
            <?php
            endif;

            $display_total = !empty($grn_data_group['total']) ? floatval($grn_data_group['total']) : $grand_total;
            $total_tax = $total_sgst + $total_cgst + $total_igst;
            $total_before_tax = $display_total - $total_tax;
            
            // Add blank rows to reach target count
            for ($b = 0; $b < $blank_rows_needed; $b++):
            ?>
            <tr class="empty-row">
                <td class="col-sr">&nbsp;</td>
                <td class="col-item">&nbsp;</td>
                <td class="col-desc">&nbsp;</td>
                <td class="col-qty">&nbsp;</td>
                <td class="col-unit">&nbsp;</td>
                <td class="col-hsn">&nbsp;</td>
                <td class="col-gst">&nbsp;</td>
                <td class="col-sgst">&nbsp;</td>
                <td class="col-cgst">&nbsp;</td>
                <td class="col-received">&nbsp;</td>
                <td class="col-pending">&nbsp;</td>
                <td class="col-price">&nbsp;</td>
            </tr>
            <?php endfor; ?>

            <tr class="total-section">
                <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                <td><?php echo indian_number_format($total_qty, 2); ?></td>
                <td colspan="7" class="text-right"><b>Total Before Tax:</b></td>
                <td class="col-price text-right"><strong><?php echo indian_number_format($total_before_tax, 2); ?></strong></td>
            </tr>
            <tr class="total-section">
                <td colspan="11" class="text-right" style="padding-right: 10px;">
                    <strong>Tax Amount &#8377;:</strong>
                </td>
                <td class="col-price text-right">
                    <strong><?php echo indian_number_format($total_tax, 2); ?></strong>
                </td>
            </tr>
            <tr class="total-section">
                <td colspan="11" class="text-right" style="padding-right: 10px;">
                    <strong>SGST Amount &#8377;:</strong>
                </td>
                <td class="col-price text-right">
                    <strong><?php echo indian_number_format($total_sgst, 2); ?></strong>
                </td>
            </tr>
            <tr class="total-section">
                <td colspan="11" class="text-right" style="padding-right: 10px;">
                    <strong>CGST Amount &#8377;:</strong>
                </td>
                <td class="col-price text-right">
                    <strong><?php echo indian_number_format($total_cgst, 2); ?></strong>
                </td>
            </tr>
            <tr class="total-section">
                <td colspan="11" class="text-right" style="padding-right: 10px;">
                    <strong>IGST Amount &#8377;:</strong>
                </td>
                <td class="col-price text-right">
                    <strong><?php echo indian_number_format($total_igst, 2); ?></strong>
                </td>
            </tr>
            <tr class="grand-total">
                <td colspan="11" class="text-right" style="padding-right: 10px;">
                    <strong>Grand Total &#8377;:</strong>
                </td>
                <td class="col-price text-right">
                    <strong><?php echo indian_number_format($display_total, 2); ?></strong>
                </td>
            </tr>
            
            <!-- Grand Total in Words -->
            <tr>
                <td colspan="12" class="text-right amount-words">
                    <b>Grand Total in Words: <?php echo number_to_word($display_total); ?> Only</b>
                </td>
            </tr>
        </table>

        <!-- Notes Section -->
        <?php if (!empty($grn_data_group['note'])): ?>
        <div class="notes-section">
            <span class="label">Notes:</span> <?php echo nl2br(htmlspecialchars($grn_data_group['note'])); ?>
        </div>
        <?php endif; ?>

        <!-- Bank Details & Signature Section - Combined Row -->
        <table style="width:100%; border-collapse: collapse; border: 0.5px solid #000; border-top: none; font-size:9px; margin-top:-0.5px;">
            <tr>
                <!-- Bank Details - Vertical Layout with Parsing -->
                <td width="50%" class="bank-details" style="padding: 8px 5px; border-right: 0.5px solid #000;">
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
                
                <!-- Authorised Signatory Only (Right side) with Stamp -->
                <td width="50%" class="signature" style="vertical-align: bottom; text-align: center; padding: 8px 5px;">
                    <?php if (isset($stamp) && $stamp == "yes") { ?>
                        <div><img class="stamp-img" src="<?php echo base_url() . $settings['company_stamp']; ?>"></div>
                    <?php } ?>
                    <div>Authorised Signatory</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            *This is an electronically generated document and does not require a physical signature.<br>
            <?php echo $settings['company_name']; ?> | <?php echo $settings['address']; ?>
        </div>
    </div>
</body>
</html>
