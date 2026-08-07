<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print <?php echo $config['title']; ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header-table, .details-table, .items-table, .terms-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #111;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .details-table td {
            width: 50%;
            vertical-align: top;
            padding: 5px;
            border: 1px solid #ddd;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right !important;
        }
        .totals-table {
            width: 350px;
            float: right;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        .terms-section {
            margin-top: 40px;
            clear: both;
        }
        .terms-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        .terms-block {
            flex: 1 1 45%;
            border: 1px solid #eee;
            padding: 8px;
            background: #fafafa;
            border-radius: 4px;
        }
        .terms-title {
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            margin-bottom: 5px;
            padding-bottom: 2px;
            text-transform: uppercase;
            font-size: 10px;
            color: #555;
        }
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .sig-box {
            text-align: center;
            width: 200px;
        }
        .sig-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print();" style="padding: 6px 12px; font-weight: bold; background: #008d4c; color: #fff; border: none; cursor: pointer; border-radius: 3px;">Print Document</button>
    </div>

    <!-- Letterhead / Sender Info -->
    <table class="header-table">
        <tr>
            <td>
                <div style="font-size: 16px; font-weight: bold; color: #003366;"><?php echo $settings['company_name'] ?? 'UWS private limited'; ?></div>
                <div style="margin-top: 5px; line-height: 1.4; color: #555;">
                    <?php echo nl2br($settings['address'] ?? ''); ?><br>
                    GSTIN: <?php echo $settings['company_gst'] ?? ''; ?><br>
                    PAN: <?php echo $settings['company_pan'] ?? ''; ?>
                </div>
            </td>
            <td class="text-right">
                <div class="title"><?php echo $config['title']; ?></div>
                <div style="font-size: 12px; font-weight: bold;">Number: <?php echo $invoice_data_group['number_fk']; ?></div>
                <div style="margin-top: 5px; color: #555;">
                    Date: <?php echo date('d-m-Y', strtotime($invoice_data_group['date'])); ?><br>
                    <?php if (!empty($invoice_data_group['po_number'])) { ?>
                        PO Number: <?php echo htmlspecialchars($invoice_data_group['po_number']); ?><br>
                    <?php } ?>
                    <?php if (!empty($invoice_data_group['customer_code'])) { ?>
                        Client Code: <?php echo htmlspecialchars($invoice_data_group['customer_code']); ?><br>
                    <?php } ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Billing Details -->
    <table class="details-table">
        <tr>
            <td>
                <strong>Billing To:</strong><br>
                <div style="margin-top: 5px; font-size: 12px; font-weight: bold; color: #222;"><?php echo $invoice_data_group['company_name']; ?></div>
                <div style="margin-top: 3px; line-height: 1.4; color: #555;">
                    <?php echo nl2br($invoice_data_group['customer_address'] ?? ''); ?><br>
                    Recipient: <?php echo $invoice_data_group['fullname']; ?><br>
                    GSTIN: <?php echo $invoice_data_group['customer_gst_no']; ?><br>
                    PAN: <?php echo $invoice_data_group['customer_pancard_no']; ?>
                </div>
            </td>
            <td>
                <strong>Shipping Address:</strong><br>
                <div style="margin-top: 5px; line-height: 1.4; color: #555;">
                    <?php echo nl2br($invoice_data_group['customer_address'] ?? ''); ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">Sr.</th>
                <th style="width: 30%;">Service Name</th>
                <th style="width: 30%;">Description</th>
                <th style="width: 8%;">SAC Code</th>
                <th style="width: 5%;" class="text-right">Qty</th>
                <th style="width: 5%;">Unit</th>
                <th style="width: 10%;" class="text-right">Price</th>
                <th style="width: 7%;" class="text-right">GST %</th>
                <th style="width: 10%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            $total_cgst = 0;
            $total_sgst = 0;
            $total_igst = 0;
            $is_igst = false;
            foreach ($show_invoice as $item) { 
                if ($item->gst_type == 'I') {
                    $is_igst = true;
                    $total_igst += $item->igst;
                } else {
                    $total_cgst += $item->cgst;
                    $total_sgst += $item->sgst;
                }
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><strong><?php echo htmlspecialchars($item->service_name); ?></strong></td>
                    <td><?php echo nl2br(htmlspecialchars($item->description)); ?></td>
                    <td><?php echo htmlspecialchars($item->sac_code); ?></td>
                    <td class="text-right"><?php echo $item->quantity; ?></td>
                    <td><?php echo htmlspecialchars($item->unit); ?></td>
                    <td class="text-right"><?php echo number_format($item->price, 2); ?></td>
                    <td class="text-right"><?php echo $item->gst; ?>%</td>
                    <td class="text-right"><strong><?php echo number_format($item->amount, 2); ?></strong></td>
                </tr>
                <?php 
                $i++;
            } 
            ?>
        </tbody>
    </table>

    <!-- Totals Area -->
    <div style="overflow: hidden; margin-top: 10px;">
        <table class="totals-table">
            <tr>
                <td>Basic Total:</td>
                <td class="text-right"><strong><?php echo number_format($invoice_data_group['basic_total'], 2); ?></strong></td>
            </tr>
            <?php if ($is_igst) { ?>
                <tr>
                    <td>IGST Total:</td>
                    <td class="text-right"><?php echo number_format($total_igst, 2); ?></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td>CGST Total:</td>
                    <td class="text-right"><?php echo number_format($total_cgst, 2); ?></td>
                </tr>
                <tr>
                    <td>SGST Total:</td>
                    <td class="text-right"><?php echo number_format($total_sgst, 2); ?></td>
                </tr>
            <?php } ?>
            <tr style="background-color: #fafafa; font-size: 12px; font-weight: bold; border-top: 2px solid #ddd;">
                <td>Grand Total:</td>
                <td class="text-right" style="color: #003366;"><strong><?php echo number_format($invoice_data_group['total'], 2); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Terms & Conditions Section -->
    <div class="terms-section">
        <div style="font-size: 12px; font-weight: bold; color: #111; border-bottom: 2px solid #003366; padding-bottom: 4px; margin-bottom: 10px;">TERMS & CONDITIONS DETAILS</div>
        <div class="terms-grid">
            <?php if (!empty($invoice_data_group['terms_and_conditions'])) { ?>
                <div class="terms-block">
                    <div class="terms-title">Terms & Conditions</div>
                    <div><?php echo nl2br(htmlspecialchars($invoice_data_group['terms_and_conditions'])); ?></div>
                </div>
            <?php } ?>
            <?php if (!empty($invoice_data_group['payment_terms'])) { ?>
                <div class="terms-block">
                    <div class="terms-title">Payment Terms</div>
                    <div><?php echo nl2br(htmlspecialchars($invoice_data_group['payment_terms'])); ?></div>
                </div>
            <?php } ?>
            <?php if (!empty($invoice_data_group['transportation'])) { ?>
                <div class="terms-block">
                    <div class="terms-title">Transportation</div>
                    <div><?php echo nl2br(htmlspecialchars($invoice_data_group['transportation'])); ?></div>
                </div>
            <?php } ?>
            <?php if (!empty($invoice_data_group['installation'])) { ?>
                <div class="terms-block">
                    <div class="terms-title">Installation</div>
                    <div><?php echo nl2br(htmlspecialchars($invoice_data_group['installation'])); ?></div>
                </div>
            <?php } ?>
            <?php if (!empty($invoice_data_group['process_schedule'])) { ?>
                <div class="terms-block">
                    <div class="terms-title">Process Schedule</div>
                    <div><?php echo nl2br(htmlspecialchars($invoice_data_group['process_schedule'])); ?></div>
                </div>
            <?php } ?>
            <?php if (!empty($invoice_data_group['taxes'])) { ?>
                <div class="terms-block">
                    <div class="terms-title">Taxes</div>
                    <div><?php echo nl2br(htmlspecialchars($invoice_data_group['taxes'])); ?></div>
                </div>
            <?php } ?>
            <?php if (!empty($invoice_data_group['exclusions'])) { ?>
                <div class="terms-block">
                    <div class="terms-title">Exclusions</div>
                    <div><?php echo nl2br(htmlspecialchars($invoice_data_group['exclusions'])); ?></div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="sig-box">
            <div class="sig-line">Prepared By</div>
        </div>
        <div class="sig-box">
            <div class="sig-line">Customer Acceptance</div>
        </div>
        <div class="sig-box">
            <div style="font-weight: bold; font-size: 9px; margin-bottom: 5px;">For <?php echo $settings['company_name'] ?? 'UWS private limited'; ?></div>
            <div class="sig-line">Authorised Signatory</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
