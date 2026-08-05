<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . '/third_party/amount_convert.php');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Stock Management
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>">Stock Summary</a></li>
                    <li class="active">Stock Valuation Report</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Stock Valuation Report</h3>
                                <div class="pull-right">
                                    <button onclick="window.print()" class="btn btn-default btn-sm">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                    <button onclick="exportToExcel()" class="btn btn-success btn-sm">
                                        <i class="fa fa-file-excel-o"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="info-box bg-aqua">
                                            <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Items</span>
                                                <span class="info-box-number"><?= count($valuation_report) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="info-box bg-green">
                                            <span class="info-box-icon"><i class="fa fa-money"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Cost Value</span>
                                                <span class="info-box-number">₹<?= indian_number_format($total_cost_value, 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="info-box bg-yellow">
                                            <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Selling Value</span>
                                                <span class="info-box-number">₹<?= indian_number_format($total_selling_value, 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="info-box bg-red">
                                            <span class="info-box-icon"><i class="fa fa-percent"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Profit Margin</span>
                                                <span class="info-box-number">
                                                    <?= ($total_cost_value > 0) ? number_format((($total_selling_value - $total_cost_value) / $total_cost_value * 100), 2) : '0.00' ?>%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                // Inline function at top of view file
                                function get_stock_status_class($current_stock, $reorder_level = 10)
                                {
                                    if ($current_stock <= 0) {
                                        return 'stock-out';
                                    } elseif ($current_stock <= $reorder_level) {
                                        return 'stock-low';
                                    } else {
                                        return 'stock-normal';
                                    }
                                }
                                ?>

                                <div class="table-responsive">
                                    <table id="valuationTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Unit</th>
                                                <th>Current Stock</th>
                                                <th>Cost Price</th>
                                                <th>Sell Price</th>
                                                <th>Cost Value</th>
                                                <th>Sell Value</th>
                                                <th>Profit</th>
                                                <th>Margin %</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($valuation_report)): ?>
                                                <?php $i = 1; ?>
                                                <?php foreach ($valuation_report as $item): ?>
                                                    <?php
                                                    $profit = $item['total_selling_value'] - $item['total_cost_value'];
                                                    $margin = ($item['total_cost_value'] > 0) ? ($profit / $item['total_cost_value'] * 100) : 0;
                                                    ?>
                                                    <tr>
                                                        <td><?= $i ?></td>
                                                        <td><strong><?= $item['code'] ?></strong></td>
                                                        <td><?= $item['item_name'] ?></td>
                                                        <td><?= $item['unit'] ?></td>
                                                        <td class="<?= get_stock_status_class($item['stock'], 10) ?>">
                                                            <?= indian_number_format($item['stock'], 2) ?>
                                                        </td>
                                                        <td>₹<?= indian_number_format($item['cost_price'], 2) ?></td>
                                                        <td>₹<?= indian_number_format($item['sell_price'], 2) ?></td>
                                                        <td>₹<?= indian_number_format($item['total_cost_value'], 2) ?></td>
                                                        <td>₹<?= indian_number_format($item['total_selling_value'], 2) ?></td>
                                                        <td class="<?= $profit >= 0 ? 'text-success' : 'text-danger' ?>">
                                                            <strong>₹<?= indian_number_format($profit, 2) ?></strong>
                                                        </td>
                                                        <td class="<?= $margin >= 0 ? 'text-success' : 'text-danger' ?>">
                                                            <strong><?= number_format($margin, 2) ?>%</strong>
                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="11" class="text-center">No valuation data available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="7" class="text-right">Grand Total:</th>
                                                <th>₹<?= indian_number_format($total_cost_value, 2) ?></th>
                                                <th>₹<?= indian_number_format($total_selling_value, 2) ?></th>
                                                <th class="<?= ($total_selling_value - $total_cost_value) >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    ₹<?= indian_number_format(($total_selling_value - $total_cost_value), 2) ?>
                                                </th>
                                                <th class="<?= (($total_selling_value - $total_cost_value) / $total_cost_value * 100) >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    <?= ($total_cost_value > 0) ? number_format((($total_selling_value - $total_cost_value) / $total_cost_value * 100), 2) : '0.00' ?>%
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Chart Section -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="box box-default">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Top 10 Items by Cost Value</h3>
                                            </div>
                                            <div class="box-body">
                                                <canvas id="costValueChart" style="height: 250px;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="box box-default">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Top 10 Items by Sell Value</h3>
                                            </div>
                                            <div class="box-body">
                                                <canvas id="sellValueChart" style="height: 250px;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#valuationTable')) {
                $('#valuationTable').DataTable().destroy();
            }
            $('#valuationTable').DataTable({
                "order": [
                    [7, "desc"]
                ],
                "pageLength": 25,
                "language": {
                    "search": "Search Stock Valuation:"
                }
            });

            // Prepare data for charts
            var topCostItems = <?= json_encode(array_slice($valuation_report, 0, 10)) ?>;

            var costLabels = topCostItems.map(item => item.code);
            var costValues = topCostItems.map(item => item.total_cost_value);
            var sellValues = topCostItems.map(item => item.total_selling_value);

            // Cost Value Chart
            var costCtx = document.getElementById('costValueChart').getContext('2d');
            var costChart = new Chart(costCtx, {
                type: 'bar',
                data: {
                    labels: costLabels,
                    datasets: [{
                        label: 'Cost Value',
                        data: costValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Sell Value Chart
            var sellCtx = document.getElementById('sellValueChart').getContext('2d');
            var sellChart = new Chart(sellCtx, {
                type: 'bar',
                data: {
                    labels: costLabels,
                    datasets: [{
                        label: 'Sell Value',
                        data: sellValues,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });

        function exportToExcel() {
            var table = document.getElementById("valuationTable");
            var html = table.outerHTML;

            // Create a Blob with the HTML
            var blob = new Blob([html], {
                type: "application/vnd.ms-excel"
            });

            // Create a link element
            var link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "Stock_Valuation_Report_" + new Date().toISOString().split('T')[0] + ".xls";

            // Trigger download
            link.click();
        }
    </script>

    <style>
        @media print {

            .box-header .pull-right,
            .info-box,
            .row .col-md-6 {
                display: none !important;
            }

            .box {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</body>