<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . '/third_party/amount_convert.php');

$total_cost_value    = isset($total_cost_value) ? (float)$total_cost_value : 0;
$total_selling_value = isset($total_selling_value) ? (float)$total_selling_value : 0;
$total_profit        = isset($total_profit) ? (float)$total_profit : ($total_selling_value - $total_cost_value);
$profit_margin       = isset($profit_margin) ? (float)$profit_margin : (($total_cost_value > 0) ? (($total_profit / $total_cost_value) * 100) : 0);
$valuation_report    = isset($valuation_report) && is_array($valuation_report) ? $valuation_report : array();
$categories          = isset($categories) && is_array($categories) ? $categories : array();
$groups              = isset($groups) && is_array($groups) ? $groups : array();
$filters             = isset($filters) && is_array($filters) ? $filters : array();

function get_stock_badge_class($current_stock, $reorder_level = 5)
{
    if ($current_stock <= 0) {
        return 'label label-danger';
    } elseif ($current_stock <= $reorder_level) {
        return 'label label-warning';
    } else {
        return 'label label-success';
    }
}
?>

<style>
    :root {
        --primary-blue: #3498db;
        --primary-dark-blue: #2980b9;
        --success-green: #27ae60;
        --warning-orange: #f39c12;
        --danger-red: #e74c3c;
    }

    .valuation-metric-card {
        background: #ffffff;
        border-radius: 8px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #e9ecef;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .valuation-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }
    .valuation-metric-icon {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 16px;
        flex-shrink: 0;
    }
    .valuation-metric-info h3 {
        margin: 0 0 4px 0;
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
    }
    .valuation-metric-info p {
        margin: 0;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
        color: #64748b;
        letter-spacing: 0.5px;
    }
    .valuation-metric-info small {
        font-size: 11px;
        color: #94a3b8;
    }

    /* Filter Box */
    .filter-box {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 15px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    /* Table Enhancements */
    .table-valuation-wrapper {
        position: relative;
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #ffffff;
    }
    #valuationTable {
        width: 100% !important;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    #valuationTable thead th {
        background: #2c3b4a !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 12px 10px;
        white-space: nowrap;
        border: none !important;
        vertical-align: middle;
    }
    #valuationTable tbody td {
        padding: 10px 10px;
        font-size: 12.5px;
        vertical-align: middle;
        border-top: 1px solid #edf2f7;
        color: #334155;
    }
    #valuationTable tbody tr:hover {
        background-color: #f8fafc;
    }
    #valuationTable tfoot th {
        background: #f1f5f9 !important;
        color: #1e293b !important;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 10px;
        border-top: 2px solid #cbd5e1 !important;
    }
    .num-col {
        text-align: right !important;
        font-variant-numeric: tabular-nums;
    }

    .badge-profit-pos {
        background-color: #ecfdf5;
        color: #059669;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .badge-profit-neg {
        background-color: #fef2f2;
        color: #dc2626;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-block;
    }

    @media print {
        .content-header,
        .filter-box,
        .box-header .pull-right,
        .row-charts,
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }
        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        .box {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-line-chart text-primary"></i> Stock Valuation Report
            <small>Comprehensive Inventory Financial Value & Profit Margins</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('Home/index/') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= base_url('MaterialIssueController/stock_summary') ?>">Stock Summary</a></li>
            <li class="active">Stock Valuation</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Top Summary Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="valuation-metric-card" style="border-top: 4px solid #3b82f6;">
                    <div class="valuation-metric-icon" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                        <i class="fa fa-cubes"></i>
                    </div>
                    <div class="valuation-metric-info">
                        <h3><?= number_format(count($valuation_report)) ?></h3>
                        <p>Total In-Stock Items</p>
                        <small>Active stocked items</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="valuation-metric-card" style="border-top: 4px solid #06b6d4;">
                    <div class="valuation-metric-icon" style="background: rgba(6, 182, 212, 0.1); color: #0891b2;">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="valuation-metric-info">
                        <h3>₹<?= indian_number_format($total_cost_value, 2) ?></h3>
                        <p>Total Cost Value</p>
                        <small>Based on purchase cost</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="valuation-metric-card" style="border-top: 4px solid #10b981;">
                    <div class="valuation-metric-icon" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <i class="fa fa-tag"></i>
                    </div>
                    <div class="valuation-metric-info">
                        <h3>₹<?= indian_number_format($total_selling_value, 2) ?></h3>
                        <p>Total Selling Value</p>
                        <small>Expected sales revenue</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="valuation-metric-card" style="border-top: 4px solid #f59e0b;">
                    <div class="valuation-metric-icon" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                        <i class="fa fa-percent"></i>
                    </div>
                    <div class="valuation-metric-info">
                        <h3><?= number_format($profit_margin, 2) ?>%</h3>
                        <p>Gross Profit Margin</p>
                        <small class="<?= $total_profit >= 0 ? 'text-success' : 'text-danger' ?>">
                            Est. Profit: ₹<?= indian_number_format($total_profit, 2) ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Box -->
        <div class="filter-box">
            <form method="get" action="<?= base_url('MaterialIssueController/stock_valuation') ?>" class="form-inline" style="display:flex; flex-wrap:wrap; align-items:center; gap:12px;">
                <div class="form-group" style="margin:0;">
                    <label style="margin-right: 6px; font-weight: 600; color: #475569;"><i class="fa fa-folder-open-o"></i> Category:</label>
                    <select name="category_id" class="form-control input-sm" style="min-width: 170px;">
                        <option value="">-- All Categories --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= (!empty($filters['category_id']) && $filters['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin:0;">
                    <label style="margin-right: 6px; font-weight: 600; color: #475569;"><i class="fa fa-tags"></i> Group:</label>
                    <select name="group_id" class="form-control input-sm" style="min-width: 170px;">
                        <option value="">-- All Groups --</option>
                        <?php foreach ($groups as $grp): ?>
                            <option value="<?= $grp['group_id'] ?>" <?= (!empty($filters['group_id']) && $filters['group_id'] == $grp['group_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grp['group_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 600;">
                    <i class="fa fa-filter"></i> Apply Filter
                </button>
                <?php if (!empty($filters['category_id']) || !empty($filters['group_id'])): ?>
                    <a href="<?= base_url('MaterialIssueController/stock_valuation') ?>" class="btn btn-default btn-sm" style="font-weight: 600;">
                        <i class="fa fa-times"></i> Clear Filters
                    </a>
                <?php endif; ?>

                <div class="pull-right" style="margin-left:auto; display:flex; gap:8px;">
                    <button type="button" onclick="window.print()" class="btn btn-default btn-sm" style="font-weight: 600;">
                        <i class="fa fa-print"></i> Print
                    </button>
                    <button type="button" onclick="exportToExcel()" class="btn btn-success btn-sm" style="font-weight: 600;">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                    </button>
                </div>
            </form>
        </div>

        <!-- Charts Section -->
        <div class="row row-charts">
            <div class="col-md-6">
                <div class="box box-primary" style="border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);">
                    <div class="box-header with-border" style="padding: 12px 15px;">
                        <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #1e293b;">
                            <i class="fa fa-bar-chart text-primary"></i> Top 10 Items by Cost Value
                        </h3>
                    </div>
                    <div class="box-body" style="padding: 15px;">
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="costValueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-success" style="border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);">
                    <div class="box-header with-border" style="padding: 12px 15px;">
                        <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #1e293b;">
                            <i class="fa fa-bar-chart text-success"></i> Top 10 Items by Selling Value
                        </h3>
                    </div>
                    <div class="box-body" style="padding: 15px;">
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="sellValueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valuation Table Card -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-info" style="border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-top: 3px solid #00c0ef;">
                    <div class="box-header with-border" style="padding: 14px 18px;">
                        <h3 class="box-title" style="font-size: 16px; font-weight: 700; color: #0f172a;">
                            <i class="fa fa-table"></i> Item-wise Valuation Details
                        </h3>
                        <span class="text-muted" style="font-size: 12px; margin-left: 10px;">
                            (Showing <?= count($valuation_report) ?> items currently in stock)
                        </span>
                    </div>

                    <div class="box-body" style="padding: 15px;">
                        <div class="table-valuation-wrapper">
                            <table id="valuationTable" class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 45px; text-align: center;">#</th>
                                        <th style="width: 100px;">Item Code</th>
                                        <th>Item Name</th>
                                        <th style="width: 60px; text-align: center;">Unit</th>
                                        <th class="num-col" style="width: 95px;">Current Stock</th>
                                        <th class="num-col" style="width: 105px;">Cost Price</th>
                                        <th class="num-col" style="width: 105px;">Sell Price</th>
                                        <th class="num-col" style="width: 125px;">Cost Value</th>
                                        <th class="num-col" style="width: 125px;">Sell Value</th>
                                        <th class="num-col" style="width: 115px;">Profit</th>
                                        <th class="num-col" style="width: 90px;">Margin %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($valuation_report)): ?>
                                        <?php $i = 1; ?>
                                        <?php foreach ($valuation_report as $item): ?>
                                            <?php
                                            $stock       = floatval($item['stock'] ?? 0);
                                            $cost_price  = floatval($item['cost_price'] ?? 0);
                                            $sell_price  = floatval($item['sell_price'] ?? 0);
                                            $cost_val    = floatval($item['total_cost_value'] ?? ($stock * $cost_price));
                                            $sell_val    = floatval($item['total_selling_value'] ?? ($stock * $sell_price));
                                            $profit      = isset($item['profit']) ? floatval($item['profit']) : ($sell_val - $cost_val);
                                            $margin_pct  = isset($item['margin_pct']) ? floatval($item['margin_pct']) : (($cost_val > 0) ? (($profit / $cost_val) * 100) : 0);
                                            $badge_class = get_stock_badge_class($stock, 5);
                                            ?>
                                            <tr>
                                                <td style="text-align: center; color: #64748b;"><?= $i ?></td>
                                                <td><span style="font-weight: 700; color: #0284c7;"><?= htmlspecialchars($item['code']) ?></span></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($item['item_name']) ?></strong>
                                                    <?php if (!empty($item['category_name'])): ?>
                                                        <br><small class="text-muted"><i class="fa fa-folder-o"></i> <?= htmlspecialchars($item['category_name']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: center;"><span class="badge" style="background:#e2e8f0; color:#334155; font-size:11px;"><?= htmlspecialchars($item['unit'] ?: 'Nos') ?></span></td>
                                                <td class="num-col">
                                                    <span class="<?= $badge_class ?>" style="font-size: 12px; font-weight: 600;">
                                                        <?= number_format($stock, 2) ?>
                                                    </span>
                                                </td>
                                                <td class="num-col" style="color: #475569;">₹<?= indian_number_format($cost_price, 2) ?></td>
                                                <td class="num-col" style="color: #475569;">₹<?= indian_number_format($sell_price, 2) ?></td>
                                                <td class="num-col" style="font-weight: 700; color: #0369a1;">₹<?= indian_number_format($cost_val, 2) ?></td>
                                                <td class="num-col" style="font-weight: 700; color: #047857;">₹<?= indian_number_format($sell_val, 2) ?></td>
                                                <td class="num-col">
                                                    <span class="<?= $profit >= 0 ? 'badge-profit-pos' : 'badge-profit-neg' ?>">
                                                        ₹<?= indian_number_format($profit, 2) ?>
                                                    </span>
                                                </td>
                                                <td class="num-col">
                                                    <span class="<?= $margin_pct >= 0 ? 'badge-profit-pos' : 'badge-profit-neg' ?>">
                                                        <?= number_format($margin_pct, 2) ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="text-center" style="padding: 30px; color: #94a3b8;">
                                                <i class="fa fa-info-circle fa-2x"></i><br>
                                                No stock valuation data available matching your criteria.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" style="text-align: right; font-size: 14px;">Grand Total:</th>
                                        <th class="num-col" style="color: #0369a1; font-size: 14px;">₹<?= indian_number_format($total_cost_value, 2) ?></th>
                                        <th class="num-col" style="color: #047857; font-size: 14px;">₹<?= indian_number_format($total_selling_value, 2) ?></th>
                                        <th class="num-col" style="font-size: 14px;">
                                            <span class="<?= $total_profit >= 0 ? 'badge-profit-pos' : 'badge-profit-neg' ?>">
                                                ₹<?= indian_number_format($total_profit, 2) ?>
                                            </span>
                                        </th>
                                        <th class="num-col" style="font-size: 14px;">
                                            <span class="<?= $profit_margin >= 0 ? 'badge-profit-pos' : 'badge-profit-neg' ?>">
                                                <?= number_format($profit_margin, 2) ?>%
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Chart.js (Deferred/Safe loading) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    $(document).ready(function() {
        // Destroy existing DataTable instance if already initialized
        if ($.fn.DataTable.isDataTable('#valuationTable')) {
            $('#valuationTable').DataTable().destroy();
        }

        // Initialize DataTable
        var table = $('#valuationTable').DataTable({
            "order": [
                [7, "desc"] // Sort by Cost Value descending
            ],
            "pageLength": 25,
            "lengthMenu": [
                [10, 25, 50, 100, 250, -1],
                [10, 25, 50, 100, 250, "All"]
            ],
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search valuation report...",
                "lengthMenu": "Show _MENU_ items",
                "info": "Showing _START_ to _END_ of _TOTAL_ items",
                "infoEmpty": "No items found",
                "paginate": {
                    "first": "«",
                    "previous": "‹",
                    "next": "›",
                    "last": "»"
                }
            },
            "autoWidth": false,
            "responsive": false
        });

        // Initialize Charts if Chart.js is loaded
        try {
            if (typeof Chart !== 'undefined') {
                var rawItems = <?= json_encode(array_slice($valuation_report, 0, 10)) ?>;

                if (rawItems && rawItems.length > 0) {
                    var labels     = rawItems.map(function(it) { return it.code + ' - ' + (it.item_name.length > 18 ? it.item_name.substring(0, 18) + '...' : it.item_name); });
                    var costValues = rawItems.map(function(it) { return parseFloat(it.total_cost_value || 0); });
                    var sellValues = rawItems.map(function(it) { return parseFloat(it.total_selling_value || 0); });

                    // Cost Value Chart
                    var costCanvas = document.getElementById('costValueChart');
                    if (costCanvas) {
                        new Chart(costCanvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Cost Value (₹)',
                                    data: costValues,
                                    backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                    borderColor: '#2563eb',
                                    borderWidth: 1,
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function(ctx) {
                                                return ' Cost: ₹' + Number(ctx.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(val) {
                                                return '₹' + Number(val).toLocaleString('en-IN');
                                            }
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 25,
                                            font: { size: 10 }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Sell Value Chart
                    var sellCanvas = document.getElementById('sellValueChart');
                    if (sellCanvas) {
                        new Chart(sellCanvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Selling Value (₹)',
                                    data: sellValues,
                                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                    borderColor: '#059669',
                                    borderWidth: 1,
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function(ctx) {
                                                return ' Selling: ₹' + Number(ctx.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(val) {
                                                return '₹' + Number(val).toLocaleString('en-IN');
                                            }
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 25,
                                            font: { size: 10 }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            }
        } catch (e) {
            console.warn('Charts could not be initialized:', e);
        }
    });

    // Clean Excel Export Function
    function exportToExcel() {
        var table = document.getElementById("valuationTable");
        if (!table) return;

        // Clone table to avoid modifying displayed DOM
        var clone = table.cloneNode(true);

        var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                   '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
                   '<x:Name>Stock Valuation</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>' +
                   '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>' +
                   clone.outerHTML +
                   '</body></html>';

        var blob = new Blob([html], { type: "application/vnd.ms-excel;charset=utf-8" });
        var link = document.createElement("a");
        var today = new Date().toISOString().split('T')[0];
        link.href = URL.createObjectURL(blob);
        link.download = "Stock_Valuation_Report_" + today + ".xls";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<?php $this->load->view('admin/footer'); ?>
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->