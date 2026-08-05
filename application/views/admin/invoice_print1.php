<!DOCTYPE html>
<html>
<head>
    <title>Invoice Print</title>
</head>
<body>
    <h1>Invoice #<?php echo $invoice_data['invoice_number']; ?></h1>
    <p>Customer: <?php echo $invoice_data['customer_name']; ?></p>
    <p>Amount: <?php echo $invoice_data['amount']; ?></p>

    <?php if ($with_stamp) { ?>
        <p><strong>Stamp:</strong> Approved</p>
    <?php } ?>
</body>
</html>
