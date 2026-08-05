<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation - <?php echo $oc['number_fk']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
        }
        .invoice-title {
            font-size: 18px;
            margin-top: 10px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .details-table th, .details-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .details-table th {
            background-color: #f5f5f5;
            text-align: left;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
        }
        .items-table .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-draft { background-color: #f0ad4e; color: white; }
        .status-sent { background-color: #5bc0de; color: white; }
        .status-accepted { background-color: #5cb85c; color: white; }
        .status-rejected { background-color: #d9534f; color: white; }
        .status-cancelled { background-color: #777; color: white; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Close</button>
    </div>

    <div class="header">
        <div class="company-name"><?php echo isset($settings['company_name']) ? $settings['company_name'] : 'Company Name'; ?></div>
        <div><?php echo isset($settings['company_address']) ? $settings['company_address'] : ''; ?></div>
        <div><?php echo isset($settings['company_mobile']) ? 'Phone: ' . $settings['company_mobile'] : ''; ?></div>
        <div class="invoice-title"><strong>ORDER CONFIRMATION (OC)</strong></div>
    </div>

    <table class="details-table">
        <tr>
            <th width="50%">OC Number</th>
            <td width="50%"><strong><?php echo $oc['number_fk']; ?></strong></td>
        </tr>
        <tr>
            <th>Date</th>
            <td><?php echo date('d-M-Y', strtotime($oc['date'])); ?></td>
        </tr>
        <tr>
            <th>Supplier</th>
            <td><?php echo isset($oc['company_name']) ? $oc['company_name'] : '-'; ?></td>
        </tr>
        <tr>
            <th>Supplier Address</th>
            <td><?php echo isset($oc['address']) ? $oc['address'] : '-'; ?></td>
        </tr>
        <tr>
            <th>PO Reference</th>
            <td><?php echo $oc['po_reference'] ? $oc['po_reference'] : '-'; ?></td>
        </tr>
        <tr>
            <th>Expected Delivery Date</th>
            <td><?php echo $oc['delivery_date'] ? date('d-M-Y', strtotime($oc['delivery_date'])) : '-'; ?></td>
        </tr>
        <tr>
            <th>Payment Terms</th>
            <td><?php echo $oc['payment_terms'] ? $oc['payment_terms'] : '-'; ?></td>
        </tr>
        <?php 
        $session_data_head1 = $this->session->userdata('session_data_head');
        $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
        if ($_has_project_master): 
        ?>
        <tr>
            <th>Project Code</th>
            <td><?php echo $oc['project_code'] ? $oc['project_code'] : '-'; ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Status</th>
            <td>
                <?php 
                $status = $oc['status'];
                $status_class = '';
                $status_text = '';
                switch($status) {
                    case 1:
                        $status_class = 'status-draft';
                        $status_text = 'Draft';
                        break;
                    case 2:
                        $status_class = 'status-sent';
                        $status_text = 'Sent/Confirmed';
                        break;
                    case 3:
                        $status_class = 'status-accepted';
                        $status_text = 'Accepted';
                        break;
                    case 4:
                        $status_class = 'status-rejected';
                        $status_text = 'Rejected';
                        break;
                    case 5:
                        $status_class = 'status-cancelled';
                        $status_text = 'Cancelled';
                        break;
                }
                ?>
                <span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
            </td>
        </tr>
    </table>

    <?php if($oc['remarks']) { ?>
    <table class="details-table">
        <tr>
            <th>Remarks</th>
            <td><?php echo $oc['remarks']; ?></td>
        </tr>
    </table>
    <?php } ?>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="30%">Description</th>
                <th width="10%">HSN Code</th>
                <th width="10%" class="text-right">Quantity</th>
                <th width="8%">Unit</th>
                <th width="12%" class="text-right">Unit Price</th>
                <th width="8%" class="text-right">Tax Rate</th>
                <th width="12%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(isset($oc_detail) && !empty($oc_detail)) {
                $i = 1;
                foreach($oc_detail as $detail) {
            ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $detail['description']; ?></td>
                    <td><?php echo $detail['hsn_code'] ? $detail['hsn_code'] : '-'; ?></td>
                    <td class="text-right"><?php echo number_format($detail['quantity'], 2); ?></td>
                    <td><?php echo $detail['unit'] ? $detail['unit'] : '-'; ?></td>
                    <td class="text-right"><?php echo number_format($detail['unit_price'], 2); ?></td>
                    <td class="text-right"><?php echo number_format($detail['tax_rate'], 2); ?></td>
                    <td class="text-right"><?php echo number_format($detail['amount'], 2); ?></td>
                </tr>
            <?php 
                    $i++;
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right">Sub Total</td>
                <td class="text-right"><?php echo number_format($oc['sub_total'], 2); ?></td>
            </tr>
            <tr>
                <td colspan="7" class="text-right">Tax Amount</td>
                <td class="text-right"><?php echo number_format($oc['tax_amount'], 2); ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="7" class="text-right">Total Amount</td>
                <td class="text-right"><?php echo number_format($oc['total'], 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>

