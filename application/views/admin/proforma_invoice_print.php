<?php
$invoice_data_group = isset($invoice_data_group) && is_array($invoice_data_group) ? $invoice_data_group : array();
$invoice_data_group = array_merge(array(
    'company_name' => '',
    'address' => '',
    'state_code' => '',
    'gst' => '',
    'pancard' => '',
    'fullname' => '',
    'c_code' => '',
    'shipping_address' => '',
    'invoice_number' => '',
    'invoice_date' => '',
    'date' => '',
    'delivery_note_no' => '',
    'delivery_date' => '',
    'despatch_through' => '',
    'vehicle_no' => '',
    'payment_method' => '',
    'customer_po' => '',
    'po_date' => '',
    'proforma_terms_and_conditions' => '',
    'proforma_payment_terms' => '',
    'proforma_process_schedule' => '',
    'proforma_taxes' => '',
    'proforma_exclusions' => '',
    'total' => 0,
    'balance' => 0
), $invoice_data_group);

$show_invoice = isset($show_invoice) && is_array($show_invoice) ? $show_invoice : array();
$settings = isset($settings) && is_array($settings) ? $settings : array();
$settings = array_merge(array(
    'company_logo' => '',
    'company_name' => '',
    'address' => '',
    'state_code' => '',
    'company_gst' => '',
    'company_pan' => '',
    'mobile' => '',
    'email' => ''
), $settings);

$invoiceDateRaw = !empty($invoice_data_group['invoice_date']) ? $invoice_data_group['invoice_date'] : $invoice_data_group['date'];
$invoiceDateDisplay = 'N/A';
if (!empty($invoiceDateRaw)) {
    $invoiceTimestamp = strtotime($invoiceDateRaw);
    if ($invoiceTimestamp !== false) {
        $invoiceDateDisplay = date('d-m-Y', $invoiceTimestamp);
    }
}

$proforma_text_sections = array(
    'Terms & Conditions' => $invoice_data_group['proforma_terms_and_conditions'],
    'Payment Terms' => $invoice_data_group['proforma_payment_terms'],
    'Process Schedule' => $invoice_data_group['proforma_process_schedule'],
    'Taxes' => $invoice_data_group['proforma_taxes'],
    'Exclusions' => $invoice_data_group['proforma_exclusions']
);

$has_proforma_text_sections = false;
foreach ($proforma_text_sections as $section_content) {
    $clean_content = trim(str_replace('&nbsp;', '', strip_tags((string) $section_content)));
    if ($clean_content !== '') {
        $has_proforma_text_sections = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Proforma Invoice</title>
    <meta name="description" content="Proforma Invoice print page">
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
        .col-sr { width: 4%; text-align: center; }
        .col-desc { width: 25%; }
        .col-hsn { width: 6%; text-align: center; }
        .col-qty { width: 4%; text-align: center; }
        .col-unit { width: 5%; text-align: center; }
        .col-gst { width: 5%; text-align: center; }
        .col-tax { width: 12%; text-align: center; } /* Shown when CGST/SGST are hidden */
        .col-cgst { width: 9%; text-align: center; }
        .col-sgst { width: 9%; text-align: center; }
        .col-rate { width: 8%; text-align: center; }
        .col-discount { width: 6%; text-align: center; }
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

        /* PO details - normal styling, not blue bold */
        .po-details {
            font-weight: normal;
            color: #000;
            font-size: 9px;
            margin-top: 3px;
        }
        
        .po-details .label-sm {
            font-weight: bold;
            color: #444;
            min-width: 75px;
            display: inline-block;
        }

        /* Tax summary table */
        .tax-summary {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        .tax-summary td, .tax-summary th {
            border: 0.5px solid #000;
            padding: 4px 3px;
        }

        .tax-summary th {
            font-weight: bold;
            text-align: center;
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
            height: auto;
            min-height: 33px;
        }

        /* First item row - keep top border */
        .item-row:first-child td {
            border-top: 0.5px solid #000;
        }

        /* Last item row - keep bottom border */
        .last-item-row td {
            border-bottom: 0.5px solid #000;
            height: auto;
            min-height: 33px;
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
        
        /* Discount styling */
        .discount-cell {
            text-align: center;
        }
        
        /* PO details container */
        .po-container {
            margin-top: 5px;
            padding-top: 3px;
            border-top: 0.5px dashed #ccc;
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
                $colspan = "10";      // For tax summary (HSN, Taxable, IGST Rate, IGST Amt, Total Tax)
                $colspan1 = "4";      // For bank details left column span
                $colspan2 = "6";      // For signature right column span
                $colspan3 = "9";      // For total rows colspan (before tax, etc)
                $colspan4 = "1";
                $colspan5 = "3";
                $colspan6 = "2";
                $colspan7 = "4";
                $gst_type_row = $key->gst_type;
            } else {
                $colspan = "11";      // For tax summary (HSN, Taxable, CGST Rate, CGST Amt, SGST Rate, SGST Amt, Total Tax)
                $colspan1 = "4";
                $colspan2 = "7";
                $colspan3 = "10";     // For total rows colspan (with discount column)
                $colspan4 = "1";
                $colspan5 = "3";
                $colspan6 = "3";
                $colspan7 = "4";
                $gst_type_row = $key->gst_type;
            }
            break;
        }
        
        // Calculate how many blank rows to add
        $target_total_rows = 10;
        $items_count = count($show_invoice);
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        
        // Calculate Tax Summary with discount consideration
        $tax_summary = array();
        $total_taxable_value = 0;
        $total_igst_amount = 0;
        $total_sgst_amount = 0;
        $total_cgst_amount = 0;
        $total_discount_value = 0; // Track total discount amount

        foreach ($show_invoice as $key) {
            $hsn = $key->hsn_code;
            $gst_rate = $key->gst;
            $taxable_value = $key->amount; // Amount after discount already applied from backend
            $key_index = $hsn . '|' . $gst_rate;
            
            // Track discount amount if available
            if(isset($key->discount) && $key->discount > 0) {
                $total_discount_value += $key->discount;
            }
            
            if (!isset($tax_summary[$key_index])) {
                $tax_summary[$key_index] = array(
                    'hsn' => $hsn,
                    'gst_rate' => $gst_rate,
                    'taxable_value' => 0,
                    'igst' => 0,
                    'sgst' => 0,
                    'cgst' => 0
                );
            }
            
            $tax_summary[$key_index]['taxable_value'] += $taxable_value;
            $tax_summary[$key_index]['igst'] += $key->igst;
            $tax_summary[$key_index]['sgst'] += $key->sgst;
            $tax_summary[$key_index]['cgst'] += $key->cgst;
            
            $total_taxable_value += $taxable_value;
            $total_igst_amount += $key->igst;
            $total_sgst_amount += $key->sgst;
            $total_cgst_amount += $key->cgst;
        }
        $total_tax_amount = $total_igst_amount + $total_sgst_amount + $total_cgst_amount;
        require(APPPATH . '/third_party/amount_convert.php');
        ?>

        <!-- Company Header - Separate Table -->
        <caption>PROFORMA INVOICE</caption>
        <table class="company-header">
              <tr>
                <td width="25%" align="center" valign="middle">
                    <img class="company-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo" >
                 </td>
                <td width="75%" valign="top">
                    <div class="company-name"> Name: <?php echo $settings['company_name']; ?></div>
                    <div><span class="label">Address :</span> <?php echo $settings['address']; ?></div>
                    <div><span class="label">State Code :</span> <?php echo $settings['state_code']; ?></div>
                    <div><span class="label">GST Number :</span> <?php echo strtoupper($settings['company_gst']); ?></div>
                    <div><span class="label">PAN Number :</span> <?php echo strtoupper($settings['company_pan']); ?></div>
                    <div><span class="label">Mobile :</span> <?php echo $settings['mobile']; ?></div>
                    <div><span class="label">Email :</span> <?php echo $settings['email']; ?></div>
                 </td>
              </tr>
         </table>

        <!-- Billing, Shipping and Invoice Details - Separate Table -->
        <table class="details-section">
            <tr>
                <!-- Billing To (Left) -->
                <td width="33%" valign="top">
                    <div class="section-heading">BILL TO</div>
                    <div><span class="label-sm">Name:</span> <?php echo $invoice_data_group['company_name']; ?></div>
                    <div><span class="label-sm">Address:</span> <?php echo $invoice_data_group['address']; ?></div>
                    <div><span class="label-sm">State Code:</span> <?php echo $invoice_data_group['state_code'] ?: 'None Provided'; ?></div>
                    <div><span class="label-sm">GST Number:</span> <?php echo $invoice_data_group['gst'] ?: 'None Provided'; ?></div>
                    <div><span class="label-sm">PAN Number:</span> <?php echo $invoice_data_group['pancard'] ?: 'None Provided'; ?></div>
                    <div><span class="label-sm">Customer Name:</span> <?php echo $invoice_data_group['fullname']; ?></div>
                    <div><span class="label-sm">Customer Code:</span> <?php echo $invoice_data_group['c_code'] ?: 'None Provided'; ?></div>
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
                
                <!-- Invoice Details (Right) -->
                <td width="34%" valign="top">
                    <div class="section-heading">INVOICE DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">Invoice No:</td><td><?php echo $invoice_data_group['invoice_number']; ?></td></tr>
                        <tr><td class="label-sm">Invoice Date:</td><td><?php echo $invoiceDateDisplay; ?></td></tr>
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
                         <tr><td class="label-sm">Customer PO:</td><td><?php echo $invoice_data_group['customer_po'] ?: 'N/A'; ?></td></tr>
                         <tr><td class="label-sm">PO Date:</td><td><?php echo $invoice_data_group['po_date'] ?: 'N/A'; ?></td></tr>
                    </table>
                    
                    
                 </td>
            </tr>
         </table>

        <!-- Main Items Table -->
        <table id="dynamic_field">
            
            <!-- Items Table Header - Discount column after Price -->
            <?php foreach ($show_invoice as $key) { ?>
            <tr class="items-header">
                <th class="col-sr">No</th>
                <th class="col-desc">Description</th>
                <th class="col-hsn">HSN/SAC</th>
                <th class="col-qty">Qty</th>
                <th class="col-unit">Unit</th>
                <th class="col-gst">TAX</th>
                <?php if ($key->gst_type != 'I') { ?>
                    <th class="col-cgst">CGST</th>
                    <th class="col-sgst">SGST</th>
                <?php } else { ?>
                    <th class="col-tax">IGST</th>
                <?php } ?>
                <th class="col-rate">Price</th>
                <th class="col-discount">Dis(%)</th>
                <th class="col-amount">Amount</th>
             </tr>
            <?php break; } ?>

            <!-- Items Loop -->
            <?php
            $i = 1;
            $sgst_total_amt = 0;
            $igst_total_amt = 0;
            $amt = 0;
            $total_qty = 0;
            $item_counter = 0;
            $formatted_total_qty = '0';
            
            foreach ($show_invoice as $key) {
                $item_counter++;
                $sgst_total_amt += $key->cgst;
                $igst_total_amt += $key->igst;
                $total_qty += isset($key->quantity) ? (float) $key->quantity : 0;
                $discount_val = isset($key->discount) ? $key->discount : 0;
                
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
                    <td class="col-qty"><?php echo indian_number_format($key->quantity); ?></td>
                    <td class="col-unit"><?php echo $key->unit; ?></td>
                    <td class="col-gst"><?php echo $key->gst; ?></td>
                    
                    <?php if ($key->gst_type != 'I') { ?>
                        <td class="col-cgst"><?php echo indian_number_format($key->cgst, 2); ?></td>
                        <td class="col-sgst"><?php echo indian_number_format($key->sgst, 2); ?></td>
                    <?php } else { ?>
                        <td class="col-tax"><?php echo indian_number_format($key->igst, 2); ?></td>
                    <?php } ?>
                    
                    <td class="col-rate"><?php echo indian_number_format($key->price, 2); ?></td>
                    <td class="col-discount"><?php echo indian_number_format($discount_val, 2); ?></td>
                    <td class="col-amount"><?php echo indian_number_format($key->amount, 2); $amt += $key->amount; ?></td>
                 </tr>
            <?php
                $i++;
            }

            $formatted_total_qty = (fmod($total_qty, 1.0) == 0.0)
                ? number_format($total_qty, 0)
                : number_format($total_qty, 2);
            
            // Add blank rows - dynamically adjust columns based on GST type
            if ($gst_type_row == 'I') {
                // IGST type - columns: Sr, Desc, HSN, Qty, Unit, GST, IGST, Price, Discount, Amount = 10 columns
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
                        <td class="col-discount">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                     </tr>
                <?php }
            } else {
                // SGST/CGST type - columns: Sr, Desc, HSN, Qty, Unit, GST, CGST, SGST, Price, Discount, Amount = 11 columns
                for ($b = 0; $b < $blank_rows_needed; $b++) { ?>
                    <tr class="empty-row">
                        <td class="col-sr">&nbsp;</td>
                        <td class="col-desc">&nbsp;</td>
                        <td class="col-hsn">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-unit">&nbsp;</td>
                        <td class="col-gst">&nbsp;</td>
                        <td class="col-cgst">&nbsp;</td>
                        <td class="col-sgst">&nbsp;</td>
                        <td class="col-rate">&nbsp;</td>
                        <td class="col-discount">&nbsp;</td>
                        <td class="col-amount">&nbsp;</td>
                     </tr>
                <?php }
            }
            ?>

            <!-- Tax Calculation Section - Dynamically adjust colspan -->
            <?php foreach ($show_invoice as $key) { ?>
                <?php if ($key->gst_type != 'I') { ?>
                    <!-- CGST/SGST Case -->
                    <tr class="total-section">
                        <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                        <td><?php echo $formatted_total_qty; ?></td>
                        <td colspan="6" class="text-right"><b>Total Before Tax:</b></td>
                        <td colspan="1" class="text-right"><b><?php echo indian_number_format($amt, 2); ?></b></td>
                     </tr>
                    <tr class="total-section">
                        <td colspan="10" class="text-right"><b>CGST Amount:</b></td>
                        <td colspan="1" class="text-right"><?php echo indian_number_format($sgst_total_amt, 2); ?></td>
                     </tr>
                    <tr class="total-section">
                        <td colspan="10" class="text-right"><b>SGST Amount:</b></td>
                        <td colspan="1" class="text-right"><?php echo indian_number_format($sgst_total_amt, 2); ?></td>
                     </tr>
                    <tr class="grand-total">
                        <td colspan="10" class="text-right"><b>Grand Total (&#8377;):</b></td>
                        <td colspan="1" class="text-right"><b> <?php echo indian_number_format($invoice_data_group['total'], 2); ?></b></td>
                     </tr>
                    <tr class="grand-total">
                        <td colspan="10" class="text-right"><b>Balance (&#8377;):</b></td>
                        <td colspan="1" class="text-right"><b> <?php echo indian_number_format($invoice_data_group['balance'], 2); ?></b></td>
                     </tr>
                    <tr>
                        <td colspan="11" class="text-right amount-words">
                            <b>Grand Total in Words: 
                            <?php 
                            echo number_to_word($invoice_data_group['balance']); ?> Only</b>
                         </td>
                     </tr>
                <?php } else { ?>
                    <!-- IGST Case -->
                    <tr class="total-section">
                        <td colspan="3" class="text-right"><b>Total Qty:</b></td>
                        <td><?php echo $formatted_total_qty; ?></td>
                        <td colspan="5" class="text-right"><b>Total Before Tax:</b></td>
                        <td colspan="1" class="text-right"><b><?php echo indian_number_format($amt, 2); ?></b></td>
                     </tr>
                    <tr class="total-section">
                        <td colspan="9" class="text-right"><b>IGST Amount:</b></td>
                        <td colspan="1" class="text-right"><?php echo indian_number_format($igst_total_amt, 2); ?></td>
                     </tr>
                    <tr class="grand-total">
                        <td colspan="9" class="text-right"><b>Grand Total (&#8377;):</b></td>
                        <td colspan="1" class="text-right"><b> <?php echo indian_number_format($invoice_data_group['total'], 2); ?></b></td>
                     </tr>
                    <tr class="grand-total">
                        <td colspan="9" class="text-right"><b>Balance (&#8377;):</b></td>
                        <td colspan="1" class="text-right"><b>&#8377; <?php echo indian_number_format($invoice_data_group['balance'], 2); ?></b></td>
                     </tr>
                    <tr>
                        <td colspan="10" class="text-right amount-words">
                            <b>Grand Total in Words: 
                            <?php 
                            echo number_to_word($invoice_data_group['balance']); ?> Only</b>
                         </td>
                     </tr>
                <?php } ?>
                <?php break; ?>
            <?php } ?>

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

            <?php if ($has_proforma_text_sections) { ?>
                <tr>
                    <td colspan="<?php echo $colspan; ?>" style="padding: 8px;">
                        <div class="section-heading">Additional PI Details</div>
                        <?php foreach ($proforma_text_sections as $section_label => $section_content) { ?>
                            <?php $section_plain = trim(str_replace('&nbsp;', '', strip_tags((string) $section_content))); ?>
                            <?php if ($section_plain !== '') { ?>
                                <div style="margin-top: 4px;"><b><?php echo $section_label; ?>:</b></div>
                                <div><?php echo $section_content; ?></div>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>

            <!-- Bank Details & Signature Section - Combined Row -->
            <tr>
                <!-- Bank Details - Vertical Layout with Improved Parsing -->
                <td colspan="<?php echo $colspan1; ?>" class="bank-details" style="padding: 8px 5px;">
                    <div class="section-heading">Bank Details:</div>
                    <div class="bank-details-content">
                        <?php 
                        $bank_text = $settings['invoice_notes'];
                        
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
                        ?>
                    </div>
                 </td>
                
                <!-- Authorised Signatory Only (Right side) -->
                <td colspan="<?php echo $colspan2; ?>" class="signature" style="vertical-align: bottom; text-align: center;">
                    <?php if ($stamp == "yes") { ?>
                        <div><img class="stamp-img" src="<?php echo base_url() . $settings['company_stamp']; ?>"></div>
                    <?php } ?>
                    <div>Authorised Signatory</div>
                 </td>
             </tr>
         </table>

        <!-- Footer -->
        <div class="footer">
            <?php echo $settings['address']; ?><br>
            This is a Computer Generated Proforma Invoice
        </div>
    </div>
</body>
</html>