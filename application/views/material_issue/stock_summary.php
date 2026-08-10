<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');

// Calculate total valuation
$totalCostValue = 0;
$totalSellValue = 0;
if (!empty($stock_items)) {
    foreach ($stock_items as $item) {
        $costPrice = ($item['stock'] > 0) ? floatval($item['cost_price']) : 0;
        $sellPrice = ($item['stock'] > 0) ? floatval($item['sell_price']) : 0;
        $totalCostValue += ($item['stock'] * $costPrice);
        $totalSellValue += ($item['stock'] * $sellPrice);
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
        --info-teal: #17a2b8;
    }

    .enhanced-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 20px;
        background: #ffffff;
    }

    .enhanced-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .summary-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        margin-bottom: 20px;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.12);
    }

    .summary-card h3 {
        font-size: 28px;
        font-weight: 700;
        margin: 10px 0;
        color: #333;
    }

    .summary-card p {
        color: #666;
        font-size: 13px;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .summary-card i {
        font-size: 38px;
        margin-bottom: 10px;
        opacity: 0.85;
    }

    /* CRITICAL FIX: Table Container with Horizontal Scroll */
    .table-scroll-wrapper {
        position: relative;
        width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 0;
        border: 1px solid #e9ecef;
        border-radius: 4px;
    }

    .table-scroll-wrapper table {
        width: 100% !important;
        min-width: 1600px !important; /* Increased to accommodate all columns */
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }

    .table-enhance {
        border-collapse: separate;
        border-spacing: 0;
        width: 100% !important;
        min-width: 1600px !important;
    }

    .table-enhance thead {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark-blue) 100%) !important;
    }

    .table-enhance thead th {
        color: #ffffff !important;
        font-weight: 600;
        padding: 12px 10px;
        border: none !important;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.3px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 20;
        background: #2980b9 !important;
    }

    .table-enhance tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .table-enhance tbody tr:hover {
        background-color: #f8fafc;
    }

    .table-enhance tbody td {
        padding: 10px 10px;
        vertical-align: middle;
        font-size: 13px;
        color: #495057;
        white-space: nowrap;
        background: #ffffff;
    }

    .col-item-name {
        min-width: 180px;
        max-width: 280px;
        white-space: normal !important;
        word-wrap: break-word;
        word-break: break-word;
    }

    .col-nowrap {
        white-space: nowrap !important;
    }

    /* CRITICAL FIX: Action Column - Sticky Right */
    .table-enhance .sticky-action-col {
        position: sticky !important;
        right: 0 !important;
        z-index: 100 !important;
        background-color: #ffffff !important;
        box-shadow: -4px 0 8px rgba(0, 0, 0, 0.15) !important;
        min-width: 130px !important;
        max-width: 130px !important;
        width: 130px !important;
        border-left: 2px solid #dee2e6 !important;
    }

    .table-enhance thead th.sticky-action-col {
        z-index: 110 !important;
        background: #2980b9 !important;
        color: #ffffff !important;
        min-width: 130px !important;
        max-width: 130px !important;
        width: 130px !important;
        border-left: 2px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: -4px 0 8px rgba(0, 0, 0, 0.15) !important;
    }

    .table-enhance tbody tr:hover td.sticky-action-col {
        background-color: #f8fafc !important;
    }

    .table-enhance tfoot th.sticky-action-col,
    .table-enhance tfoot td.sticky-action-col {
        background-color: #f8fafc !important;
        z-index: 100 !important;
        border-left: 2px solid #dee2e6 !important;
    }

    /* Stock status colors */
    .stock-out {
        color: #dc3545;
        font-weight: bold;
    }

    .stock-low {
        color: #f39c12;
        font-weight: bold;
    }

    .stock-normal {
        color: #28a745;
        font-weight: bold;
    }

    /* Custom scrollbar styling */
    .table-scroll-wrapper::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Hide DataTables scroll wrapper if it creates extra elements */
    .dataTables_scroll {
        overflow: visible !important;
    }
    
    .dataTables_scrollHead,
    .dataTables_scrollBody {
        overflow: visible !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .summary-card h3 {
            font-size: 20px;
        }
        .summary-card i {
            font-size: 28px;
        }
        .table-enhance tbody td {
            font-size: 11px;
            padding: 6px 8px;
        }
        .table-enhance thead th {
            font-size: 10px;
            padding: 8px 6px;
        }
        .table-enhance .sticky-action-col {
            min-width: 100px !important;
            max-width: 100px !important;
            width: 100px !important;
        }
        .table-enhance thead th.sticky-action-col {
            min-width: 100px !important;
            max-width: 100px !important;
            width: 100px !important;
        }
        .btn-xs {
            font-size: 9px;
            padding: 1px 5px;
        }
        .col-item-name {
            min-width: 120px;
            max-width: 180px;
        }
        .table-scroll-wrapper table {
            min-width: 1400px !important;
        }
    }

    @media (max-width: 480px) {
        .table-scroll-wrapper table {
            min-width: 1200px !important;
        }
        .table-enhance tbody td {
            font-size: 10px;
            padding: 4px 6px;
        }
        .table-enhance .sticky-action-col {
            min-width: 80px !important;
            max-width: 80px !important;
            width: 80px !important;
        }
        .table-enhance thead th.sticky-action-col {
            min-width: 80px !important;
            max-width: 80px !important;
            width: 80px !important;
        }
        .btn-xs {
            font-size: 8px;
            padding: 1px 4px;
        }
        .col-item-name {
            min-width: 100px;
            max-width: 140px;
        }
    }
</style>

        <div class="content-wrapper">
            <section class="content-header">
                <h1>Stock Management</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Inventory</a></li>
                    <li class="active">Stock Summary</li>
                </ol>
            </section>

            <section class="content">
                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card" style="border-top: 4px solid #3498db;">
                            <i class="fa fa-cubes" style="color: #3498db;"></i>
                            <h3><?= count($stock_items) ?></h3>
                            <p>Total Items</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card" style="border-top: 4px solid #27ae60;">
                            <i class="fa fa-balance-scale" style="color: #27ae60;"></i>
                            <h3><?= count(array_filter($stock_items, function ($item) { return $item['stock'] > 0; })) ?></h3>
                            <p>In Stock Items</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card" style="border-top: 4px solid #f39c12;">
                            <i class="fa fa-exclamation-triangle" style="color: #f39c12;"></i>
                            <h3><?= count(array_filter($stock_items, function ($item) { return $item['stock'] <= 5 && $item['stock'] > 0; })) ?></h3>
                            <p>Low Stock Items</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card" style="border-top: 4px solid #e74c3c;">
                            <i class="fa fa-ban" style="color: #e74c3c;"></i>
                            <h3><?= count(array_filter($stock_items, function ($item) { return $item['stock'] <= 0; })) ?></h3>
                            <p>Out of Stock</p>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Card -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary enhanced-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-search"></i> Search & Filter</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body" style="padding: 15px 20px;">
                                <form method="get" action="<?php echo base_url('MaterialIssueController/stock_summary') ?>">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="category_id" class="form-control">
                                                    <option value="">All Categories</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['category_id'] ?>"
                                                            <?= (isset($filters['category_id']) && $filters['category_id'] == $category['category_id']) ? 'selected' : '' ?>>
                                                            <?= $category['category_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label>Group</label>
                                                <select name="group_id" class="form-control">
                                                    <option value="">All Groups</option>
                                                    <?php foreach ($groups as $group): ?>
                                                        <option value="<?= $group['group_id'] ?>"
                                                            <?= (isset($filters['group_id']) && $filters['group_id'] == $group['group_id']) ? 'selected' : '' ?>>
                                                            <?= $group['group_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label>Item Type</label>
                                                <select name="item_type" class="form-control">
                                                    <option value="">All Types</option>
                                                    <option value="B" <?= (isset($filters['item_type']) && $filters['item_type'] == 'B') ? 'selected' : '' ?>>Boughtout</option>
                                                    <option value="M" <?= (isset($filters['item_type']) && $filters['item_type'] == 'M') ? 'selected' : '' ?>>Manufacturing</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group" style="margin-top: 25px; display: flex; gap: 6px; flex-wrap: wrap;">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-filter"></i> Apply Filters
                                                </button>
                                                <a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>" class="btn btn-default btn-sm">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Summary Data Table Card -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info enhanced-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-list-alt"></i> Stock Summary Details</h3>
                                <div class="pull-right" style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <a href="<?= base_url('MaterialIssueController/export_stock_summary') . (!empty($filters) ? '?' . http_build_query($filters) : '') ?>" class="btn btn-success btn-sm">
                                        <i class="fa fa-file-excel-o"></i> Export Excel
                                    </a>
                                    <a href="<?= base_url('MaterialIssueController/stock_verification') ?>" class="btn btn-warning btn-sm">
                                        <i class="fa fa-check-square"></i> Stock Verification
                                    </a>
                                    <a href="<?= base_url('MaterialIssueController/low_stock') ?>" class="btn btn-danger btn-sm">
                                        <i class="fa fa-exclamation-triangle"></i> Low Stock
                                    </a>
                                    <a href="<?= base_url('MaterialIssueController/stock_valuation') ?>" class="btn btn-primary btn-sm">
                                        <i class="fa fa-calculator"></i> Stock Valuation
                                    </a>
                                </div>
                            </div>
                            <div class="box-body" style="padding: 15px 20px;">
                                <div class="table-scroll-wrapper">
                                    <table id="exampleMaterialIssue_stock_summary" class="table table-bordered table-striped table-enhance">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="min-width: 55px; width: 55px;">Sr.No.</th>
                                                <th style="min-width: 100px;">Item Code</th>
                                                <th class="col-item-name" style="min-width: 180px; max-width: 280px;">Item Name</th>
                                                <th style="min-width: 100px;">Category</th>
                                                <th style="min-width: 100px;">Group</th>
                                                <th class="text-center" style="min-width: 60px;">Unit</th>
                                                <th class="text-right" style="min-width: 100px;">Current Stock</th>
                                                <th class="text-right" style="min-width: 100px;">Cost Price</th>
                                                <th class="text-right" style="min-width: 100px;">Sell Price</th>
                                                <th class="text-right" style="min-width: 100px;">Cost Value</th>
                                                <th class="text-right" style="min-width: 100px;">Sell Value</th>
                                                <th class="text-center" style="min-width: 100px;">Status</th>
                                                <th class="text-center sticky-action-col" style="min-width: 130px !important; max-width: 130px !important; width: 130px !important;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($stock_items)): ?>
                                                <?php $i = 1; ?>
                                                <?php foreach ($stock_items as $item): ?>
                                                    <?php
                                                    $costPrice = ($item['stock'] > 0) ? floatval($item['cost_price']) : 0;
                                                    $sellPrice = ($item['stock'] > 0) ? floatval($item['sell_price']) : 0;
                                                    $costValue = floatval($item['stock']) * $costPrice;
                                                    $sellValue = floatval($item['stock']) * $sellPrice;
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= $i ?></td>
                                                        <td class="col-nowrap"><strong><?= $item['code'] ?></strong></td>
                                                        <td class="col-item-name"><?= $item['item_name'] ?></td>
                                                        <td><?= $item['category_name'] ?? 'N/A' ?></td>
                                                        <td><?= $item['group_name'] ?? 'N/A' ?></td>
                                                        <td class="text-center"><?= $item['unit'] ?></td>
                                                        <td class="text-right <?= get_stock_status_class($item['stock'], 5) ?>">
                                                            <?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format($item['stock'], 2); ?>
                                                        </td>
                                                        <td class="text-right text-primary">
                                                            <strong>₹<?= indian_number_format($costPrice, 2) ?></strong>
                                                        </td>
                                                        <td class="text-right text-success">
                                                            <strong>₹<?= indian_number_format($sellPrice, 2) ?></strong>
                                                        </td>
                                                        <td class="text-right">₹<?= indian_number_format($costValue, 2) ?></td>
                                                        <td class="text-right">₹<?= indian_number_format($sellValue, 2) ?></td>
                                                        <td class="text-center">
                                                            <?php if ($item['stock'] <= 0): ?>
                                                                <span class="label label-danger">Out of Stock</span>
                                                            <?php elseif ($item['stock'] <= 5): ?>
                                                                <span class="label label-warning">Low Stock</span>
                                                            <?php else: ?>
                                                                <span class="label label-success">In Stock</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center col-nowrap sticky-action-col">
                                                            <a href="<?= base_url('MaterialIssueController/stock_ledger/' . $item['inventory_id']) ?>"
                                                                class="btn btn-info btn-xs" title="View Ledger">
                                                                <i class="fa fa-history"></i> Ledger
                                                            </a>
                                                            <?php if ($item['stock'] <= 5 && $item['stock'] > 0): ?>
                                                                <button class="btn btn-warning btn-xs" title="Low Stock Alert">
                                                                    <i class="fa fa-exclamation"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="13" class="text-center">No stock items found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr style="background-color: #f8fafc; font-weight: bold;">
                                                <th colspan="9" class="text-right">Total Valuation:</th>
                                                <th class="text-right text-primary">₹<?= indian_number_format($totalCostValue, 2) ?></th>
                                                <th class="text-right text-success">₹<?= indian_number_format($totalSellValue, 2) ?></th>
                                                <th class="text-center"></th>
                                                <th class="sticky-action-col"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <?php
    function get_stock_status_class($current_stock, $reorder_level = 5)
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

    <script>
        $(document).ready(function() {
            // Destroy existing DataTable if any
            if ($.fn.DataTable.isDataTable('#exampleMaterialIssue_stock_summary')) {
                $('#exampleMaterialIssue_stock_summary').DataTable().destroy();
            }

            // Initialize DataTable WITHOUT scrollX
            var table = $('#exampleMaterialIssue_stock_summary').DataTable({
                "order": [
                    [6, "asc"]
                ],
                "pageLength": 25,
                "autoWidth": false,
                "language": {
                    "search": "Search Stock Summary:"
                },
                "columnDefs": [{
                    "targets": [12], // Action column index
                    "orderable": false,
                    "searchable": false
                }],
                "scrollX": false,
                "responsive": false,
                "paging": true,
                "lengthChange": true,
                "info": true
            });

            // Force table container to maintain scroll
            $('.table-scroll-wrapper').css({
                'width': '100%',
                'overflow-x': 'auto'
            });
            
            $('.table-scroll-wrapper table').css({
                'min-width': '1600px',
                'width': '100%'
            });
        });

        // Handle window resize
        $(window).on('resize', function() {
            var width = $(window).width();
            var minWidth = 1600;
            if (width < 768) {
                minWidth = 1400;
            } else if (width < 480) {
                minWidth = 1200;
            }
            $('.table-scroll-wrapper table').css('min-width', minWidth + 'px');
        });
    </script>
</body>