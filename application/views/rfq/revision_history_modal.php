<?php if (empty($history)): ?>
    <div class="alert alert-warning text-center">No revision history found.</div>
<?php else: ?>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <?php foreach ($history as $index => $q): ?>
                <li class="<?= $index === 0 ? 'active' : '' ?>">
                    <a href="#rev_tab_<?= $q['quotation_id'] ?>" data-toggle="tab">
                        <strong>R<?= $q['revision_no'] ?></strong> 
                        <?= $index === 0 ? ' <span class="label label-success" style="font-size: 9px; padding: 2px 4px;">Latest</span>' : '' ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="tab-content" style="padding-top: 15px;">
            <?php foreach ($history as $index => $q): ?>
                <div class="tab-pane <?= $index === 0 ? 'active' : '' ?>" id="rev_tab_<?= $q['quotation_id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-condensed table-striped" style="font-size: 12px; margin-bottom: 10px;">
                                <tr>
                                    <th style="width: 40%;">Quote Date</th>
                                    <td><?= date('d-m-Y', strtotime($q['quote_date'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="label label-<?= $q['status'] === 'approved' ? 'success' : ($q['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($q['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Remarks</th>
                                    <td><?= htmlspecialchars($q['remarks'] ?? 'N/A') ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered table-condensed table-striped" style="font-size: 12px; margin-bottom: 10px;">
                                <tr>
                                    <th style="width: 40%;">Total Amount</th>
                                    <td class="text-right">₹<?= number_format($q['total_amount'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>GST Amount</th>
                                    <td class="text-right">₹<?= number_format($q['gst_amount'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Final Amount</th>
                                    <td class="text-right"><strong>₹<?= number_format($q['final_amount'], 2) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h4 style="margin-top: 15px; font-size: 14px; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px;"><i class="fa fa-list"></i> Quoted Items:</h4>
                    <table class="table table-bordered table-condensed no-datatable" style="font-size: 12px;">
                        <thead>
                            <tr style="background-color: #f9f9f9;">
                                <th>Item Code</th>
                                <th>Description</th>
                                <th class="text-right">Qty</th>
                                <th>Unit</th>
                                <th class="text-right">Quoted Unit Price</th>
                                <th class="text-right">GST %</th>
                                <th class="text-right">GST Amount</th>
                                <th class="text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($q['items'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['item_code']) ?></td>
                                    <td><?= htmlspecialchars($item['description']) ?></td>
                                    <td class="text-right"><?= number_format($item['quantity'], 2) ?></td>
                                    <td><?= htmlspecialchars($item['unit']) ?></td>
                                    <td class="text-right">₹<?= number_format($item['unit_price'], 2) ?></td>
                                    <td class="text-right"><?= number_format($item['gst_percentage'], 2) ?>%</td>
                                    <td class="text-right">₹<?= number_format($item['gst_amount'], 2) ?></td>
                                    <td class="text-right">₹<?= number_format($item['total_amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
