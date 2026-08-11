<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Order #<?php echo isset($joborder['number_fk']) ? $joborder['number_fk'] : ''; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .company-info h1 {
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .document-title {
            text-align: right;
        }
        .document-title h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .document-title .number {
            font-size: 16px;
            font-weight: bold;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-block {
            flex: 1;
            margin-right: 30px;
        }
        .info-block h3 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            color: #2c3e50;
            border-bottom: 1px solid #999;
            padding-bottom: 5px;
        }
        .info-block p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-block last-child {
            margin-right: 0;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        table thead {
            background-color: #f5f5f5;
            border-top: 1px solid #999;
            border-bottom: 2px solid #999;
        }
        table th {
            padding: 10px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: #333;
        }
        table td {
            padding: 8px 5px;
            border-bottom: 1px solid #ddd;
        }
        table tbody tr:last-child td {
            border-bottom: 2px solid #999;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            width: 50%;
            margin-left: auto;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .totals table {
            margin: 0;
        }
        .totals th {
            text-align: left;
            background-color: transparent;
            border: none;
            padding: 8px 10px;
        }
        .totals td {
            text-align: right;
            background-color: transparent;
            border: none;
            padding: 8px 10px;
        }
        .totals tr:last-child {
            background-color: #f5f5f5;
            font-weight: bold;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        .remarks {
            margin-bottom: 30px;
        }
        .remarks h3 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .remarks p {
            font-size: 11px;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #999;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .signature-block {
            text-align: center;
            width: 30%;
        }
        .signature-block .line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10px;
            font-weight: bold;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-button button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="print-button no-print">
        <button onclick="window.print()">Print Job Order</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1><?php echo isset($settings['company_name']) ? $settings['company_name'] : 'Company Name'; ?></h1>
                <p><?php echo isset($settings['company_address']) ? $settings['company_address'] : ''; ?></p>
            </div>
            <div class="document-title">
                <h2>JOB ORDER</h2>
                <p class="number">#<?php echo isset($joborder['number_fk']) ? $joborder['number_fk'] : 'N/A'; ?></p>
            </div>
        </div>

        <!-- Info Sections -->
        <div class="info-section">
            <div class="info-block">
                <h3>Bill From</h3>
                <p><strong><?php echo isset($settings['company_name']) ? $settings['company_name'] : ''; ?></strong></p>
                <p><?php echo isset($settings['company_address']) ? $settings['company_address'] : ''; ?></p>
                <p><?php echo isset($settings['company_phone']) ? 'Phone: ' . $settings['company_phone'] : ''; ?></p>
                <p><?php echo isset($settings['company_email']) ? 'Email: ' . $settings['company_email'] : ''; ?></p>
            </div>
            <div class="info-block">
                <h3>Bill To</h3>
                <p><strong><?php echo isset($joborder['company_name']) ? $joborder['company_name'] : ''; ?></strong></p>
                <p><?php echo isset($joborder['customer_address']) ? $joborder['customer_address'] : ''; ?></p>
                <p><?php echo isset($joborder['customer_mobile']) ? 'Phone: ' . $joborder['customer_mobile'] : ''; ?></p>
                <p><?php echo isset($joborder['customer_email']) ? 'Email: ' . $joborder['customer_email'] : ''; ?></p>
            </div>
            <div class="info-block">
                <h3>Job Order Details</h3>
                <p><strong>Date:</strong> <?php echo isset($joborder['date']) ? date('d-M-Y', strtotime($joborder['date'])) : ''; ?></p>
                <p><strong>Due Date:</strong> <?php echo isset($joborder['due_date']) ? date('d-M-Y', strtotime($joborder['due_date'])) : 'N/A'; ?></p>
                <p><strong>Status:</strong> <?php echo (isset($joborder['status']) && $joborder['status'] == 1) ? 'Draft' : 'Sent'; ?></p>
            </div>
        </div>

        <!-- Job Items Table -->
        <table>
            <thead>
                <tr>
                    <th width="5%">Sr.No.</th>
                    <th width="10%">Item Code</th>
                    <th width="20%">Equipment</th>
                    <th width="6%">Qty</th>
                    <th width="5%">Unit</th>
                    <th width="8%">Tag No.</th>
                    <th width="12%">Scope</th>
                    <th width="7%">Stores</th>
                    <th width="6%">Unit Price</th>
                    <th width="5%">Tax %</th>
                    <th width="8%">Amount</th>
                    <th width="8%">Remark</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                $sr_no = 1;
                if(isset($joborder_detail) && !empty($joborder_detail)) {
                    foreach($joborder_detail as $detail) {
                        $amount = isset($detail['amount']) ? $detail['amount'] : 0;
                        $grand_total += $amount;
                        ?>
                        <tr>
                            <td><?php echo $sr_no++; ?></td>
                            <td><?php echo isset($detail['item_code']) ? $detail['item_code'] : '-'; ?></td>
                            <td><?php echo isset($detail['equipment']) ? $detail['equipment'] : ''; ?></td>
                            <td class="text-right"><?php echo isset($detail['quantity']) ? number_format($detail['quantity'], 2) : '-'; ?></td>
                            <td><?php echo isset($detail['unit']) ? $detail['unit'] : '-'; ?></td>
                            <td><?php echo isset($detail['tag_no']) ? $detail['tag_no'] : '-'; ?></td>
                            <td><?php echo isset($detail['scope']) ? $detail['scope'] : '-'; ?></td>
                            <td><?php echo isset($detail['stores_remark']) ? $detail['stores_remark'] : '-'; ?></td>
                            <td class="text-right"><?php echo isset($detail['unit_price']) ? number_format($detail['unit_price'], 2) : '0.00'; ?></td>
                            <td class="text-right"><?php echo isset($detail['tax_rate']) ? number_format($detail['tax_rate'], 2) : '0.00'; ?>%</td>
                            <td class="text-right"><?php echo number_format($amount, 2); ?></td>
                            <td><?php echo isset($detail['remark']) ? $detail['remark'] : '-'; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="12" class="text-center">No Items</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals">
            <table>
                <tr>
                    <th>Sub Total:</th>
                    <td><?php echo number_format(isset($joborder['sub_total']) ? $joborder['sub_total'] : 0, 2); ?></td>
                </tr>
                <tr>
                    <th>Tax Amount:</th>
                    <td><?php echo number_format(isset($joborder['tax_amount']) ? $joborder['tax_amount'] : 0, 2); ?></td>
                </tr>
                <tr>
                    <th>Total:</th>
                    <td><?php echo number_format(isset($joborder['total']) ? $joborder['total'] : $grand_total, 2); ?></td>
                </tr>
            </table>
        </div>

        <!-- Remarks -->
        <?php if(isset($joborder['remarks']) && !empty($joborder['remarks'])) { ?>
            <div class="remarks">
                <h3>Remarks</h3>
                <p><?php echo $joborder['remarks']; ?></p>
            </div>
        <?php } ?>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-block">
                <div class="line">Authorized By</div>
            </div>
            <div class="signature-block">
                <div class="line">Customer Signature</div>
            </div>
            <div class="signature-block">
                <div class="line">Date</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer generated document. No signature is required.</p>
            <p>Generated on <?php echo date('d-m-Y'); ?></p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
