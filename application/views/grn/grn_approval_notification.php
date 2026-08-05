<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>GRN Approval Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #007bff;
        }

        .content {
            padding: 30px;
            background: #fff;
            border: 1px solid #ddd;
        }

        .footer {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .grn-details {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>GRN Approval Required</h1>
        </div>

        <div class="content">
            <p>Dear Approver,</p>

            <p>A new Goods Received Note (GRN) requires your approval:</p>

            <div class="grn-details">
                <h3>GRN Details:</h3>
                <p><strong>GRN Number:</strong> <?= $grn_number ?></p>
                <p><strong>Approval Level:</strong> <?= $approval_level ?></p>
                <p><strong>Total Amount:</strong> ₹<?= number_format($amount, 2) ?></p>
                <?php if (!empty($grn_data['supplier_name'])): ?>
                    <p><strong>Supplier:</strong> <?= $grn_data['supplier_name'] ?></p>
                <?php endif; ?>
                <?php if (!empty($grn_data['po_number_fk'])): ?>
                    <p><strong>PO Number:</strong> <?= $grn_data['po_number_fk'] ?></p>
                <?php endif; ?>
            </div>

            <p>Please review and take appropriate action on this GRN.</p>

            <p style="text-align: center; margin: 30px 0;">
                <a href="<?= $approval_link ?>" class="btn">Review GRN for Approval</a>
            </p>

            <p>This is an automated notification. Please do not reply to this email.</p>
        </div>

        <div class="footer">
            <p>© <?= date('Y') ?> GRN Approval System. All rights reserved.</p>
            <p>This email was sent by the automated GRN approval system.</p>
        </div>
    </div>
</body>

</html>