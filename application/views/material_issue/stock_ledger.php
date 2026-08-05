<?php
defined('BASEPATH') or exit('No direct script access allowed');

$item_details_arr = (isset($item_details) && is_array($item_details)) ? $item_details : array();
$item_stock = isset($item_details_arr['stock']) ? (float)$item_details_arr['stock'] : 0;
$item_unit = isset($item_details_arr['unit']) ? $item_details_arr['unit'] : '';
$item_cost = isset($item_details_arr['cost_price']) ? (float)$item_details_arr['cost_price'] : 0;
$item_sell = isset($item_details_arr['sell_price']) ? (float)$item_details_arr['sell_price'] : 0;

// Get item name for selected item
$selectedItemName = '';
if (isset($selected_item) && $selected_item) {
    if (!empty($item_details_arr['item_name'])) {
        $selectedItemName = $item_details_arr['item_name'] . ' (' . ($item_details_arr['code'] ?? '') . ')';
    } else {
        $this->db->select('item_name, code');
        $this->db->where('inventory_id', $selected_item);
        $item_row = $this->db->get('inventory')->row_array();
        if ($item_row) {
            $selectedItemName = $item_row['item_name'] . ' (' . $item_row['code'] . ')';
        }
    }
}
?>

<body class="hold-transition skin-blue sidebar-mini">
<div id="loader" class="center"></div>
<div class="wrapper">
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Stock Management
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>">Stock Summary</a></li>
            <li class="active">Stock Ledger</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">Stock Ledger</h3>
                        <a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>" class="btn btn-primary pull-right">
                            <i class="fa fa-arrow-left"></i> Back to Stock Summary
                        </a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <!-- Filter Form -->
                        <form method="post" action="<?php echo base_url('MaterialIssueController/stock_ledger') ?>" class="form-inline">
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-4">
                                    <label>Select Item *</label>
                                    <select name="inventory_id" style="width: 100%;" class="form-control item_search_name" required>
                                        <option value="">Select Item</option>
                                        <?php foreach ($inventory_items as $item): ?>
                                            <option value="<?= $item['inventory_id'] ?>"
                                                <?= (isset($selected_item) && $selected_item == $item['inventory_id']) ? 'selected' : '' ?>>
                                                <?= $item['code'] ?> - <?= $item['item_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>From Date</label>
                                    <input type="date" name="date_from" class="form-control" style="width:100%;"
                                        value="<?= isset($date_from) ? $date_from : '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <label>To Date</label>
                                    <input type="date" name="date_to" class="form-control" style="width:100%;"
                                        value="<?= isset($date_to) ? $date_to : '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 25px;">View Ledger</button>
                                </div>
                            </div>
                        </form>

                        <?php if (isset($selected_item) && $selected_item): ?>
                            <!-- Item Details Price & Stock Banner -->
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-3">
                                    <div class="info-box bg-aqua">
                                        <span class="info-box-icon"><i class="fa fa-cube"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Current Stock</span>
                                            <span class="info-box-number"><?= number_format($item_stock, 2) ?> <?= htmlspecialchars($item_unit) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-green">
                                        <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Purchase Price (Cost)</span>
                                            <span class="info-box-number">₹<?= number_format($item_cost, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-yellow">
                                        <span class="info-box-icon"><i class="fa fa-tag"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Selling Price</span>
                                            <span class="info-box-number">₹<?= number_format($item_sell, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-purple">
                                        <span class="info-box-icon"><i class="fa fa-calculator"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Stock Value</span>
                                            <span class="info-box-number">₹<?= number_format($item_stock * $item_cost, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($ledger_entries)): ?>
                                <div class="table-responsive-container">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr style="background-color: #2980b9; color: #ffffff;">
                                                <th>Date</th>
                                                <th>Transaction Type</th>
                                                <th>Reference No</th>
                                                <th>Purchase Price (₹)</th>
                                                <th>Selling Price (₹)</th>
                                                <th>In Qty</th>
                                                <th>Out Qty</th>
                                                <th>Balance Qty</th>
                                                <th>Total Value (₹)</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ledger_entries as $entry): ?>
                                                <?php
                                                    $isPurchase = in_array(strtoupper($entry['transaction_type']), ['GRN', 'PURCHASE', 'PURCHASE_STOCK']);
                                                    $unitPrice = $isPurchase ? $entry['purchase_price'] : $entry['selling_price'];
                                                    $entryTotalValue = abs($entry['quantity']) * $unitPrice;
                                                ?>
                                                <tr>
                                                    <td><?= date('d-m-Y H:i', strtotime($entry['transaction_date'])) ?></td>
                                                    <td>
                                                        <?php if ($isPurchase): ?>
                                                            <span class="label label-success"><i class="fa fa-shopping-cart"></i> Purchase (GRN)</span>
                                                        <?php elseif ($entry['transaction_type'] == 'ISSUE'): ?>
                                                            <span class="label label-primary"><i class="fa fa-paper-plane"></i> Material Issue (MIS)</span>
                                                        <?php elseif ($entry['transaction_type'] == 'ADJUSTMENT'): ?>
                                                            <span class="label label-warning"><i class="fa fa-sliders"></i> Adjustment</span>
                                                        <?php elseif ($entry['transaction_type'] == 'RETURN'): ?>
                                                            <span class="label label-info"><i class="fa fa-undo"></i> Return</span>
                                                        <?php elseif ($entry['transaction_type'] == 'TRANSFER'): ?>
                                                            <span class="label label-default"><i class="fa fa-exchange"></i> Transfer</span>
                                                        <?php elseif ($entry['transaction_type'] == 'SALES'): ?>
                                                            <span class="label label-danger"><i class="fa fa-line-chart"></i> Sales</span>
                                                        <?php else: ?>
                                                            <span class="label label-default"><?= htmlspecialchars($entry['transaction_type']) ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($isPurchase): ?>
                                                            <a href="<?= base_url('GrnController/show_grn/' . urlencode($entry['reference_no'])) ?>" target="_blank" class="label label-success" style="font-size: 11px;">
                                                                <i class="fa fa-external-link"></i> GRN: <?= htmlspecialchars($entry['reference_no']) ?>
                                                            </a>
                                                        <?php elseif (in_array(strtoupper($entry['transaction_type']), ['ISSUE', 'MIS'])): ?>
                                                            <a href="<?= base_url('MaterialIssueController/index') ?>?search=<?= urlencode($entry['reference_no']) ?>" target="_blank" class="label label-primary" style="font-size: 11px;">
                                                                <i class="fa fa-external-link"></i> MIS: <?= htmlspecialchars($entry['reference_no']) ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <strong><?= htmlspecialchars($entry['reference_no']) ?></strong>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right font-weight-bold" style="color: #27ae60;">
                                                        ₹<?= number_format($entry['purchase_price'], 2) ?>
                                                    </td>
                                                    <td class="text-right font-weight-bold" style="color: #e67e22;">
                                                        ₹<?= number_format($entry['selling_price'], 2) ?>
                                                    </td>
                                                    <td class="text-success text-right">
                                                        <?php if ($entry['quantity'] > 0): ?>
                                                            <strong>+<?= number_format($entry['quantity'], 2) ?></strong>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-danger text-right">
                                                        <?php if ($entry['quantity'] < 0): ?>
                                                            <strong><?= number_format(abs($entry['quantity']), 2) ?></strong>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong><?= number_format($entry['balance_quantity'], 2) ?></strong>
                                                    </td>
                                                    <td class="text-right" style="font-weight: 700; color: #2c3e50;">
                                                        ₹<?= number_format($entryTotalValue, 2) ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($entry['remarks']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Summary Footer -->
                                <div class="well" style="margin-top: 15px; background-color: #f8fafc; border-left: 5px solid #2980b9;">
                                    <h4 style="margin-top:0; color: #2c3e50; font-weight:700;"><i class="fa fa-bar-chart"></i> Ledger Summary</h4>
                                    <?php
                                    $totalIn = 0;
                                    $totalOut = 0;
                                    $openingBalance = 0;
                                    $closingBalance = 0;

                                    if (!empty($ledger_entries)) {
                                        $openingBalance = $ledger_entries[0]['balance_quantity'] - $ledger_entries[0]['quantity'];
                                        $closingBalance = end($ledger_entries)['balance_quantity'];

                                        foreach ($ledger_entries as $entry) {
                                            if ($entry['quantity'] > 0) {
                                                $totalIn += $entry['quantity'];
                                            } else {
                                                $totalOut += abs($entry['quantity']);
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <strong>Opening Balance:</strong> <?= number_format($openingBalance, 2) ?>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <strong>Total In (GRN):</strong> <span class="text-success">+<?= number_format($totalIn, 2) ?></span>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <strong>Total Out (MIS):</strong> <span class="text-danger">-<?= number_format($totalOut, 2) ?></span>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <strong>Closing Balance:</strong> <span class="text-primary"><?= number_format($closingBalance, 2) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Print Button -->
                                <div class="text-center" style="margin-top: 15px;">
                                    <button onclick="window.print()" class="btn btn-default">
                                        <i class="fa fa-print"></i> Print Ledger
                                    </button>
                                </div>

                            <?php else: ?>
                                <div class="alert alert-warning" style="margin-top: 15px;">
                                    <i class="fa fa-info-circle"></i> No ledger entries found for the selected period.
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info" style="margin-top: 15px;">
                                <i class="fa fa-arrow-up"></i> Please select an item to view its stock ledger.
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
</div>
<!-- /.wrapper -->
<?php $this->load->view('admin/footer'); ?>