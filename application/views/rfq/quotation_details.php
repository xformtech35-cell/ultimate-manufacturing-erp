<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">
                    <i class="fas fa-file-invoice-dollar"></i> Quotation Details
                </h4>
            </div>
            <div class="card-body">
                <!-- Quotation Header -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">RFQ Number:</th>
                                <td><?= $quotation['rfq_no'] ?></td>
                            </tr>
                            <tr>
                                <th>Vendor:</th>
                                <td><strong><?= $quotation['supplier_name'] ?></strong></td>
                            </tr>
                            <tr>
                                <th>Quote Date:</th>
                                <td><?= date('d-m-Y', strtotime($quotation['quote_date'])) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Quotation ID:</th>
                                <td>#<?= $quotation['quotation_id'] ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-<?=
                                                            $quotation['status'] == 'approved' ? 'success' : ($quotation['status'] == 'rejected' ? 'danger' : 'warning')
                                                            ?>">
                                        <?= ucfirst($quotation['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Created On:</th>
                                <td><?= date('d-m-Y H:i', strtotime($quotation['created_at'])) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if (!empty($quotation['remarks'])): ?>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-sticky-note"></i> Remarks:</strong><br>
                                <?= $quotation['remarks'] ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Financial Summary -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-title">Sub Total</h6>
                                <h4 class="text-primary">₹<?= number_format($quotation['total_amount'], 2) ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-title">GST Amount</h6>
                                <h4 class="text-warning">₹<?= number_format($quotation['gst_amount'], 2) ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Final Amount</h6>
                                <h4>₹<?= number_format($quotation['final_amount'], 2) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <h5 class="mb-3"><i class="fas fa-list"></i> Quotation Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">GST %</th>
                                <th class="text-center">GST Amount</th>
                                <th class="text-center">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($quotation_items)): ?>
                                <?php foreach ($quotation_items as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= $item['item_code'] ?></strong></td>
                                        <td><?= $item['description'] ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-center"><?= $item['unit'] ?></td>
                                        <td class="text-end">₹<?= number_format($item['unit_price'], 2) ?></td>
                                        <td class="text-center"><?= $item['gst_percentage'] ?></td>
                                        <td class="text-end">₹<?= number_format($item['gst_amount'], 2) ?></td>
                                        <td class="text-end"><strong>₹<?= number_format($item['total_amount'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No items found in this quotation.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-active">
                            <tr>
                                <td colspan="8" class="text-end"><strong>Sub Total:</strong></td>
                                <td class="text-end"><strong>₹<?= number_format($quotation['total_amount'], 2) ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-end"><strong>GST Total:</strong></td>
                                <td class="text-end"><strong>₹<?= number_format($quotation['gst_amount'], 2) ?></strong></td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="8" class="text-end"><strong>Grand Total:</strong></td>
                                <td class="text-end"><strong>₹<?= number_format($quotation['final_amount'], 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <a href="javascript:window.print()" class="btn btn-primary me-2">
                            <i class="fas fa-print"></i> Print Quotation
                        </a>
                        <a href="<?= base_url('RFQController/show_rfq/' . $quotation['rfq_id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to RFQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>