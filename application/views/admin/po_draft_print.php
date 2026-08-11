<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>DRAFT - Purchase Order - <?php echo $po_data_group['number']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
            position: relative;
        }

        /* DRAFT watermark overlay */
        .draft-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            color: rgba(255, 0, 0, 0.1);
            font-size: 80px;
            font-weight: bold;
            white-space: nowrap;
            pointer-events: none;
            z-index: 999;
        }

        /* DRAFT header */
        .draft-header {
            background-color: #ffeb3b;
            color: #333;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            border: 2px dashed #f44336;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        th,
        td {
            padding: 6px 4px;
            border: 1px solid #ccc;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .header-table {
            border: 2px solid #f44336;
            border-style: dashed;
        }

        .company-logo {
            text-align: center;
            opacity: 0.8;
        }

        .company-logo img {
            max-height: 60px;
            max-width: 150px;
        }

        .po-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            color: #666;
        }

        .draft-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            color: #f44336;
            margin-bottom: 5px;
        }

        .section-title {
            background-color: #e0e0e0;
            font-weight: bold;
            padding: 5px;
            border-left: 3px solid #f44336;
        }

        .amount-table {
            width: 40%;
            float: right;
            margin-top: 10px;
            border: 1px dashed #999;
        }

        .amount-table td {
            padding: 4px;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .grand-total {
            background-color: #666;
            color: #fff;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .signature-box {
            width: 45%;
            display: inline-block;
            text-align: center;
        }

        .signature-line {
            border-top: 1px dashed #000;
            width: 80%;
            margin: 30px auto 5px;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .draft-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 8px;
            margin: 10px 0;
            border-radius: 3px;
            font-size: 9px;
        }

        .draft-note strong {
            color: #e74c3c;
        }

        .watermark-text {
            color: #f44336;
            font-weight: bold;
            text-align: center;
            padding: 3px;
            border-bottom: 1px dashed #f44336;
            margin-bottom: 5px;
        }

        .draft-border {
            border: 2px dashed #f44336 !important;
        }
    </style>
</head>

<body>

    <!-- DRAFT Watermark Overlay -->
    <div class="draft-overlay">DRAFT - FOR REVIEW ONLY</div>

    <!-- DRAFT Header -->
    <div class="draft-header">
        ⚠️ DRAFT PURCHASE ORDER - NOT FOR PRODUCTION ⚠️
    </div>

    <div class="watermark-text">
        THIS IS A DRAFT VERSION FOR REVIEW AND CONFIRMATION ONLY
    </div>

    <div class="company-logo">
        <img src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo">
    </div>

    <h2 class="draft-title">DRAFT PURCHASE ORDER</h2>
    <h3 class="po-title"><?php echo $po_data_group['number']; ?></h3>

    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="text-bold">FROM:</div>
                <div><?php echo $settings['company_name']; ?></div>
                <div><?php echo $settings['address']; ?></div>
                <div>GST: <?php echo $settings['company_gst']; ?></div>
                <div>PAN: <?php echo $settings['company_pan']; ?></div>
                <div>Tel: <?php echo $settings['mobile']; ?></div>
                <div>Email: <?php echo $settings['email']; ?></div>
            </td>
            <td width="50%">
                <div class="text-bold">TO:</div>
                <div><?php echo $po_data_group['company_name']; ?></div>
                <div>Vendor Code: <?php echo isset($po_data_group['s_code']) && $po_data_group['s_code'] ? $po_data_group['s_code'] : 'N/A'; ?></div>
                <div><?php echo $po_data_group['address']; ?></div>
                <div>GST: <?php echo $po_data_group['gst'] ?: 'N/A'; ?></div>
                <div>PAN: <?php echo $po_data_group['pancard'] ?: 'N/A'; ?></div>
                <div>Tel: <?php echo $po_data_group['mobile'] ?: 'N/A'; ?></div>
                <div>Email: <?php echo $po_data_group['email'] ?: 'N/A'; ?></div>
            </td>
        </tr>
    </table>

    <div class="draft-note">
        <strong>⚠️ IMPORTANT NOTE:</strong> This is a <strong>DRAFT</strong> purchase order for your review and confirmation only.
        Please review all details carefully and confirm acceptance or provide comments.
        A formal purchase order will be issued after internal approvals are complete.
    </div>

    <table>
        <tr>
            <td width="25%"><strong>Draft PO Number:</strong></td>
            <td width="25%"><?php echo $po_data_group['number']; ?></td>
            <td width="25%"><strong>Draft Date:</strong></td>
            <td width="25%"><?php echo date('d-m-Y', strtotime($po_data_group['purchase_date'])); ?></td>
        </tr>
        <tr>
            <td><strong>Proposed Delivery:</strong></td>
            <td><?php echo $po_data_group['delivery_date']; ?></td>
            <td><strong>Reference:</strong></td>
            <td><?php echo $po_data_group['subheading'] ?: 'N/A'; ?></td>
        </tr>
        <tr>
            <td><strong>Project Code:</strong></td>
            <td><?php 
                $session_data_head1 = $this->session->userdata('session_data_head');
                $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
                if ($_has_project_master) {
                    echo isset($po_data_group['project_code']) ? htmlspecialchars($po_data_group['project_code']) : 'N/A';
                }
            ?></td>
            <?php if (isset($po_data_group['so_no']) && isset($po_data_group['oc_no']) && !empty($po_data_group['so_no']) && $po_data_group['so_no'] === $po_data_group['oc_no']): ?>
            <td><strong>SO:</strong></td>
            <td><?php echo htmlspecialchars($po_data_group['so_no']); ?></td>
            <?php else: ?>
            <td><strong>Sales Order:</strong></td>
            <td><?php echo isset($po_data_group['so_no']) ? htmlspecialchars($po_data_group['so_no']) : 'N/A'; ?></td>
            <?php endif; ?>
        </tr>
        <?php if (!isset($po_data_group['so_no']) || !isset($po_data_group['oc_no']) || empty($po_data_group['so_no']) || $po_data_group['so_no'] !== $po_data_group['oc_no']): ?>
        <tr>
            <td><strong>OC Number:</strong></td>
            <td><?php echo isset($po_data_group['oc_no']) ? htmlspecialchars($po_data_group['oc_no']) : 'N/A'; ?></td>
            <td></td>
            <td></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="section-title">PROPOSED ORDER ITEMS (DRAFT)</div>

    <table>
        <thead>
            <tr>
                <th width="5%">Sr.</th>
                <th width="35%">Description</th>
                <th width="8%">HSN</th>
                <th width="10%">Proposed Qty</th>
                <th width="7%">GST%</th>
                <?php if ($show_po[0]->gst_type != 'I'): ?>
                    <th width="10%">SGST (₹)</th>
                    <th width="10%">CGST (₹)</th>
                <?php else: ?>
                    <th width="12%">IGST (₹)</th>
                <?php endif; ?>
                <th width="10%">Proposed Price (₹)</th>
                <th width="10%">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $sgst_total_amt = $igst_total_amt = $amt = 0;

            foreach ($show_po as $key):
                $sgst_total_amt += $key->cgst;
                $igst_total_amt += $key->igst;
                $amt += $key->amount;
            ?>
                <tr>
                    <td class="text-center"><?php echo $i++; ?></td>
                    <td>
                        <div class="text-bold"><?php echo $key->product_name; ?></div>
                        <?php if ($key->description): ?>
                            <div style="font-size: 9px; color: #666;"><?php echo $key->description; ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $key->hsn_code; ?></td>
                    <td class="text-center"><?php echo number_format($key->quantity) . ' ' . $key->unit; ?></td>
                    <td class="text-center"><?php echo $key->gst; ?></td>

                    <?php if ($key->gst_type != 'I'): ?>
                        <td class="text-right"><?php echo number_format($key->sgst, 2); ?></td>
                        <td class="text-right"><?php echo number_format($key->cgst, 2); ?></td>
                    <?php else: ?>
                        <td class="text-right"><?php echo number_format($key->igst, 2); ?></td>
                    <?php endif; ?>

                    <td class="text-right"><?php echo number_format($key->price, 2); ?></td>
                    <td class="text-right text-bold"><?php echo number_format($key->amount, 2); ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- Add empty rows if needed -->
            <?php while ($i <= 15): ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <?php if ($show_po[0]->gst_type != 'I'): ?>
                        <td>&nbsp;</td>
                    <?php endif; ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <?php $i++;
            endwhile; ?>
        </tbody>
    </table>

    <div style="float: right; width: 45%; margin-top: 10px;">
        <table class="amount-table draft-border">
            <tr class="total-row">
                <td>Total Before Tax:</td>
                <td class="text-right">₹ <?php echo number_format($amt, 2); ?></td>
            </tr>

            <?php if ($show_po[0]->gst_type != 'I'): ?>
                <tr>
                    <td>CGST Amount:</td>
                    <td class="text-right">₹ <?php echo number_format($sgst_total_amt, 2); ?></td>
                </tr>
                <tr>
                    <td>SGST Amount:</td>
                    <td class="text-right">₹ <?php echo number_format($sgst_total_amt, 2); ?></td>
                </tr>
                <tr>
                    <td>Total GST:</td>
                    <td class="text-right">₹ <?php echo number_format($sgst_total_amt * 2, 2); ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td>IGST Amount:</td>
                    <td class="text-right">₹ <?php echo number_format($igst_total_amt, 2); ?></td>
                </tr>
            <?php endif; ?>

            <tr class="grand-total">
                <td>PROPOSED TOTAL:</td>
                <td class="text-right">₹ <?php echo number_format($po_data_group['total'], 2); ?></td>
            </tr>
        </table>
    </div>

    <!-- Add clear:both after the amount section -->
    <div style="clear: both;"></div>

    <div style="clear: both; margin-top: 10px; padding: 8px; background: #f9f9f9; border: 1px dashed #ddd;">
        <?php require(APPPATH . '/third_party/amount_convert.php'); ?>
        <strong>Grand Total in Words:</strong> <?php echo number_to_word($po_data_group['total']); ?> Only
    </div>

    <!-- Vendor Response Section -->
    <div style="margin-top: 20px; padding: 10px; background: #e8f5e8; border: 1px solid #4caf50;">
        <div class="section-title">VENDOR RESPONSE REQUESTED</div>
        <p style="font-size: 9px; margin: 5px 0;">
            <strong>Please review this draft PO and respond within 3 working days:</strong>
        </p>
        <table style="width: 100%; border: none;">
            <tr>
                <td width="33%" style="border: none; text-align: center; padding: 10px;">
                    <div style="font-weight: bold; color: #4caf50;">✓ ACCEPT</div>
                    <div style="font-size: 8px;">I accept all terms and prices</div>
                </td>
                <td width="34%" style="border: none; text-align: center; padding: 10px;">
                    <div style="font-weight: bold; color: #ff9800;">✗ REJECT</div>
                    <div style="font-size: 8px;">I cannot accept as proposed</div>
                </td>
                <td width="33%" style="border: none; text-align: center; padding: 10px;">
                    <div style="font-weight: bold; color: #2196f3;">📝 COMMENT</div>
                    <div style="font-size: 8px;">I have comments/suggestions</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="signature-section">
        <table style="border: none; width: 100%;">
            <tr>
                <td width="45%" style="border: none; text-align: center;">
                    <div class="text-bold">Prepared By</div>
                    <div class="signature-line"></div>
                    <div>Name & Signature</div>
                    <div>Date: ______________</div>
                </td>

                <td width="10%" style="border: none;"></td>

                <td width="45%" style="border: none; text-align: center;">
                    <div class="text-bold">Vendor Acceptance</div>
                    <div class="signature-line"></div>
                    <div>Name & Signature</div>
                    <div>Date: ______________</div>
                </td>
            </tr>
        </table>
    </div>

    <?php if ($po_data_group['po_note'] || $po_data_group['po_terms_and_conditions']): ?>
        <div style="margin-top: 20px;">
            <div class="section-title">PROPOSED TERMS & NOTES</div>
            <?php if ($po_data_group['po_note']): ?>
                <div><strong>Note:</strong> <?php echo $po_data_group['po_note']; ?></div>
            <?php endif; ?>
            <?php if ($po_data_group['po_terms_and_conditions']): ?>
                <div style="margin-top: 5px;"><strong>Proposed Terms:</strong> <?php echo $po_data_group['po_terms_and_conditions']; ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        <strong>⚠️ THIS IS A DRAFT DOCUMENT FOR REVIEW ONLY ⚠️</strong><br>
        This draft purchase order is subject to change and internal approval.<br>
        A formal purchase order will be issued separately after all approvals.<br>
        <?php echo $settings['company_name']; ?> | <?php echo $settings['address']; ?> | Draft Generated: <?php echo date('d-m-Y'); ?>
    </div>

</body>

</html>