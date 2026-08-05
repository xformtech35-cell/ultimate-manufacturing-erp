<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="comparison-table">
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="text-center mb-3">
                <i class="fas fa-balance-scale"></i>
                Quotation Comparison - RFQ: <?= $rfq['rfq_no'] ?>
            </h4>
            <div class="text-center text-muted mb-4">
                <strong>Date:</strong> <?= date('d-m-Y', strtotime($rfq['rfq_date'])) ?> |
                <strong>Total Quotations:</strong> <?= count($quotations) ?>
            </div>
        </div>
    </div>

    <?php if (empty($quotations)): ?>
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle"></i> No quotations available for comparison.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2" class="align-middle">Item Code</th>
                        <th rowspan="2" class="align-middle">Description</th>
                        <th rowspan="2" class="align-middle text-center">Quantity</th>
                        <th rowspan="2" class="align-middle text-center">Unit</th>
                        <?php foreach ($quotations as $index => $quotation): ?>
                            <th class="text-center <?= $index == 0 ? 'bg-success' : ($index == 1 ? 'bg-warning' : ($index == 2 ? 'bg-info' : '')) ?>">
                                <?= $quotation['supplier_name'] ?><br>
                                <small class="text-light">₹<?= number_format($quotation['final_amount'], 2) ?></small><br>
                                <span class="badge bg-light text-dark">
                                    <?= $ranks[$index] ?? 'L' . ($index + 1) ?>
                                </span>
                            </th>
                        <?php endforeach; ?>
                        <th rowspan="2" class="align-middle text-center bg-success text-white">Best Price</th>
                    </tr>
                    <tr>
                        <?php foreach ($quotations as $index => $quotation): ?>
                            <th class="text-center small <?= $index == 0 ? 'bg-success' : ($index == 1 ? 'bg-warning' : ($index == 2 ? 'bg-info' : '')) ?>">
                                Date: <?= date('d-m-Y', strtotime($quotation['quote_date'])) ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><strong><?= $item['item_code'] ?></strong></td>
                            <td><?= strip_tags($item['description']) ?></td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-center"><?= $item['unit'] ?></td>

                            <?php
                            $prices = [];
                            foreach ($quotations as $quotation):
                                $item_price = null;
                                $item_gst = null;
                                $item_total = null;

                                // Find the item in quotation items
                                foreach ($quotation['items'] as $q_item) {
                                    if ($q_item['rfq_item_id'] == $item['rfq_item_id']) {
                                        $item_price = $q_item['unit_price'];
                                        $item_gst = $q_item['gst_percentage'];
                                        $item_total = $q_item['total_amount'];
                                        break;
                                    }
                                }
                                $prices[] = $item_price;
                            ?>
                                <td class="text-center <?=
                                                        $item_price && $item_price == min(array_filter($prices, function ($p) {
                                                            return $p !== null;
                                                        })) ? 'lowest-price' : ($item_price && $item_price == max(array_filter($prices, function ($p) {
                                                                return $p !== null;
                                                            })) ? 'highest-price' : '')
                                                        ?>">
                                    <?php if ($item_price): ?>
                                        <div><strong>₹<?= number_format($item_price, 2) ?></strong></div>
                                        <small class="text-muted">GST: <?= $item_gst ?>%</small><br>
                                        <small class="text-success">Total: ₹<?= number_format($item_total, 2) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>

                            <td class="text-center bg-success text-white">
                                <?php
                                $valid_prices = array_filter($prices, function ($p) {
                                    return $p !== null;
                                });
                                echo !empty($valid_prices) ? '<strong>₹' . number_format(min($valid_prices), 2) . '</strong>' : 'N/A';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Total Row -->
                    <tr class="table-info">
                        <td colspan="4" class="text-end"><strong>Sub Total:</strong></td>
                        <?php foreach ($quotations as $index => $quotation): ?>
                            <td class="text-center <?= $index == 0 ? 'lowest-price' : '' ?>">
                                <strong>₹<?= number_format($quotation['total_amount'], 2) ?></strong>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center bg-success text-white">
                            <strong>₹<?= number_format(min(array_column($quotations, 'total_amount')), 2) ?></strong>
                        </td>
                    </tr>

                    <!-- GST Row -->
                    <tr class="table-warning">
                        <td colspan="4" class="text-end"><strong>GST Amount:</strong></td>
                        <?php foreach ($quotations as $quotation): ?>
                            <td class="text-center">
                                <strong>₹<?= number_format($quotation['gst_amount'], 2) ?></strong>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center bg-success text-white">
                            <strong>₹<?= number_format(min(array_column($quotations, 'gst_amount')), 2) ?></strong>
                        </td>
                    </tr>

                    <!-- Final Amount Row -->
                    <tr class="table-success">
                        <td colspan="4" class="text-end"><strong>Final Amount:</strong></td>
                        <?php foreach ($quotations as $index => $quotation): ?>
                            <td class="text-center <?= $index == 0 ? 'lowest-price' : '' ?>">
                                <strong>₹<?= number_format($quotation['final_amount'], 2) ?></strong>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center bg-success text-white">
                            <strong>₹<?= number_format(min(array_column($quotations, 'final_amount')), 2) ?></strong>
                        </td>
                    </tr>

                    <!-- Quote Date Row -->
                    <tr>
                        <td colspan="4" class="text-end"><strong>Quote Date:</strong></td>
                        <?php foreach ($quotations as $quotation): ?>
                            <td class="text-center">
                                <?= date('d-m-Y', strtotime($quotation['quote_date'])) ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center">-</td>
                    </tr>

                    <!-- Status Row -->
                    <tr>
                        <td colspan="4" class="text-end"><strong>Status:</strong></td>
                        <?php foreach ($quotations as $quotation): ?>
                            <td class="text-center">
                                <span class="badge bg-<?=
                                                        $quotation['status'] == 'approved' ? 'success' : ($quotation['status'] == 'rejected' ? 'danger' : 'warning')
                                                        ?>">
                                    <?= ucfirst($quotation['status']) ?>
                                </span>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center">-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Comparison Summary -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie"></i> Comparison Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Ranking Summary:</strong></h6>
                                <ul class="list-group">
                                    <?php foreach ($quotations as $index => $quotation): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <span class="badge bg-<?=
                                                                        $index == 0 ? 'success' : ($index == 1 ? 'warning' : ($index == 2 ? 'info' : 'secondary'))
                                                                        ?> me-2">
                                                    <?= $ranks[$index] ?? 'L' . ($index + 1) ?>
                                                </span>
                                                <?= $quotation['supplier_name'] ?>
                                            </span>
                                            <span class="fw-bold">₹<?= number_format($quotation['final_amount'], 2) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Price Analysis:</strong></h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Metric</th>
                                                <th>Amount</th>
                                                <th>Vendor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-success">
                                                <td>Lowest Quote</td>
                                                <td><strong>₹<?= number_format(min(array_column($quotations, 'final_amount')), 2) ?></strong></td>
                                                <td><?= $quotations[0]['supplier_name'] ?></td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td>Highest Quote</td>
                                                <td><strong>₹<?= number_format(max(array_column($quotations, 'final_amount')), 2) ?></strong></td>
                                                <td><?= $quotations[count($quotations) - 1]['supplier_name'] ?></td>
                                            </tr>
                                            <tr class="table-info">
                                                <td>Average Quote</td>
                                                <td><strong>₹<?= number_format(array_sum(array_column($quotations, 'final_amount')) / count($quotations), 2) ?></strong></td>
                                                <td>-</td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <td>Price Difference</td>
                                                <td><strong>₹<?= number_format(max(array_column($quotations, 'final_amount')) - min(array_column($quotations, 'final_amount')), 2) ?></strong></td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php if (count($quotations) > 1): ?>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <h6><strong><i class="fas fa-lightbulb"></i> Recommendation:</strong></h6>
                                        <p class="mb-1">
                                            <strong><?= $quotations[0]['supplier_name'] ?></strong> offers the most competitive pricing at
                                            <strong>₹<?= number_format($quotations[0]['final_amount'], 2) ?></strong>, which is
                                            <strong>₹<?= number_format($quotations[1]['final_amount'] - $quotations[0]['final_amount'], 2) ?></strong>
                                            lower than the next best offer from <?= $quotations[1]['supplier_name'] ?>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .comparison-table .lowest-price {
        background-color: #d4edda !important;
        font-weight: bold;
    }

    .comparison-table .highest-price {
        background-color: #f8d7da !important;
    }

    .comparison-table th.bg-success,
    .comparison-table th.bg-warning,
    .comparison-table th.bg-info {
        color: white !important;
    }

    .comparison-table .table-dark th {
        border-color: #454d55;
    }
</style>