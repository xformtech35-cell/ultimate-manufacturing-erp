<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');

// Get filter values from POST or set defaults
$search_item = isset($_POST['search_item']) ? $_POST['search_item'] : (isset($_GET['search_item']) ? $_GET['search_item'] : '');
$item_type = isset($_POST['item_type']) ? $_POST['item_type'] : (isset($_GET['item_type']) ? $_GET['item_type'] : '');
$stock_status = isset($_POST['stock_status']) ? $_POST['stock_status'] : (isset($_GET['stock_status']) ? $_GET['stock_status'] : '');
$sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : (isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_added');
?>

<style>
    /* Enhanced UI Styles - Same as before */
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
        margin-top: 20px;
    }

    .enhanced-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    /* DataTables Controls Styling (Length Menu & Search Box) */
    .dataTables_wrapper .dataTables_length {
        float: left;
        margin-bottom: 12px;
    }
    .dataTables_wrapper .dataTables_length label {
        font-weight: 500;
        color: #495057;
        font-size: 13px;
        margin-bottom: 0;
    }
    .dataTables_wrapper .dataTables_length select {
        height: 32px;
        padding: 4px 8px;
        font-size: 13px;
        border-radius: 4px;
        border: 1px solid #d2d6de;
        background-color: #fff;
        color: #555;
        margin: 0 6px;
        outline: none;
        display: inline-block;
        width: auto;
    }
    .dataTables_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 12px;
        text-align: right;
    }
    .dataTables_wrapper .dataTables_filter label {
        font-weight: 500;
        color: #495057;
        font-size: 13px;
        margin-bottom: 0;
    }
    .dataTables_wrapper .dataTables_filter input {
        height: 32px;
        padding: 4px 10px;
        font-size: 13px;
        border-radius: 4px;
        border: 1px solid #d2d6de;
        background-color: #fff;
        color: #555;
        margin-left: 6px;
        outline: none;
        display: inline-block;
        width: auto;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 2px rgba(60, 141, 188, 0.2);
    }

    /* ===== INVENTORY HISTORY: Table Scroll Container ===== */
    .inv-table-wrap {
        width: 100% !important;
        overflow-x: auto !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch;
        border-radius: 8px;
        border: 1px solid #d0dbe8;
    }

    .table-enhance {
        width: 100% !important;
        min-width: 1480px !important;
        margin: 0 !important;
    }

    /* ===== STICKY ACTIONS COLUMN ===== */
    .table-enhance td.sticky-action-col,
    .table-enhance th.sticky-action-col {
        position: sticky !important;
        right: 0 !important;
        z-index: 20 !important;
        min-width: 205px !important;
        width: 205px !important;
        text-align: center !important;
    }
    .table-enhance td.sticky-action-col {
        background-color: #ffffff !important;
        box-shadow: -3px 0 8px rgba(0,0,0,0.08) !important;
    }
    .table-enhance th.sticky-action-col {
        background: linear-gradient(135deg, #1e6fa8 0%, #3c8dbc 100%) !important;
        box-shadow: -3px 0 8px rgba(0,0,0,0.12) !important;
        z-index: 30 !important;
    }
    /* Sticky col inherits correct stripe background */
    .table-enhance tbody tr:nth-of-type(odd) td.sticky-action-col {
        background-color: #f4f8fb !important;
    }
    .table-enhance tbody tr:hover td.sticky-action-col {
        background-color: #dbeeff !important;
    }

    .table-enhance tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .table-enhance tbody tr:hover {
        background-color: #f8fafc;
    }

    .table-enhance tbody td {
        padding: 14px 12px;
        vertical-align: middle;
        border: none;
        color: #495057;
        font-size: 14px;
    }

    /* Status Indicators */
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 6px;
    }

    .status-low {
        background-color: var(--danger-red);
        animation: pulse 2s infinite;
    }

    .status-ok {
        background-color: var(--success-green);
    }

    .stock-low {
        color: var(--danger-red);
        font-weight: 600;
    }

    .stock-ok {
        color: var(--success-green);
        font-weight: 600;
    }

    /* Badge Styles */
    .badge-type {
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-boughtout {
        background-color: #e3f2fd;
        color: #1565c0;
        border: 1px solid #bbdefb;
    }

    .badge-manufacturing {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    /* Action Buttons */
    .btn-action-group {
        display: flex;
        gap: 6px;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-action-edit:hover {
        background-color: var(--primary-blue);
        color: white;
        border-color: var(--primary-blue);
    }

    .btn-action-delete:hover {
        background-color: var(--danger-red);
        color: white;
        border-color: var(--danger-red);
    }

    .btn-action-download:hover {
        background-color: var(--info-teal);
        color: white;
        border-color: var(--info-teal);
    }

    .btn-action-issue:hover {
        background-color: var(--warning-orange);
        color: white;
        border-color: var(--warning-orange);
    }

    /* Filter Card */
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .filter-card h4 {
        color: white;
        margin-top: 0;
    }

    /* Summary Cards */
    .summary-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .summary-card h3 {
        font-size: 28px;
        font-weight: 700;
        margin: 10px 0;
        color: #333;
    }

    .summary-card p {
        color: #666;
        font-size: 14px;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .summary-card i {
        font-size: 40px;
        margin-bottom: 15px;
        opacity: 0.8;
    }

    /* Animations */
    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .btn-action-group {
            flex-direction: column;
            gap: 4px;
        }

        .btn-action {
            width: 100%;
        }

        .summary-card {
            margin-bottom: 15px;
        }
    }
    /* Issued column eye popover */
    .issued-cell {
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .issued-badge {
        display: inline-block;
        background: #27ae60;
        color: #fff;
        border-radius: 4px;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .issued-eye-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #eafaf1;
        border: 1px solid #a9dfbf;
        color: #1e8449;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .issued-eye-btn:hover {
        background: #27ae60;
        color: #fff;
        border-color: #27ae60;
        box-shadow: 0 2px 6px rgba(39,174,96,0.4);
    }
    /* Modal Item Links Styling */
    .issued-modal-link-jo {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        margin-bottom: 5px;
        background: #f0faf4;
        border: 1px solid #a9dfbf;
        border-radius: 6px;
        color: #1e8449;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .issued-modal-link-jo:hover {
        background: #27ae60;
        color: #fff;
        border-color: #27ae60;
        text-decoration: none;
    }
    .issued-modal-link-jo:hover .issued-modal-icon-jo {
        background: #fff;
        color: #27ae60;
    }
    .issued-modal-icon-jo {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #27ae60;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .issued-modal-link-slip {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        margin-bottom: 5px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        color: #495057;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .issued-modal-link-slip:hover {
        background: #6c757d;
        color: #fff;
        border-color: #6c757d;
        text-decoration: none;
    }
    .issued-modal-link-slip:hover .issued-modal-icon-slip {
        background: #fff;
        color: #6c757d;
    }
    .issued-modal-icon-slip {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #6c757d;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
</style>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <i class="fa fa-history"></i> Inventory History
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/index' ?>">Products</a></li>
                    <li class="active">Inventory History</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Flash Messages -->
               

                <!-- Summary Cards -->
                <?php
                // Calculate summary data
                $total_items = count($result ?? []);
                $low_stock_items = 0;
                $total_stock_value = 0;
                $boughtout_count = 0;
                $manufacturing_count = 0;

                if (!empty($result)) {
                    foreach ($result as $item) {
                        if ($item->stock <= 5) {
                            $low_stock_items++;
                        }
                        $total_stock_value += ($item->stock * $item->cost_price);
                        if ($item->item_type == 'B') {
                            $boughtout_count++;
                        } else {
                            $manufacturing_count++;
                        }
                    }
                }
                ?>

                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card" style="border-top: 4px solid #3498db;">
                            <i class="fa fa-cubes" style="color: #3498db;"></i>
                            <h3><?php echo $total_items; ?></h3>
                            <p>Total Items</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card" style="border-top: 4px solid #e74c3c;">
                            <i class="fa fa-exclamation-triangle" style="color: #e74c3c;"></i>
                            <h3><?php echo $low_stock_items; ?></h3>
                            <p>Low Stock Items</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card" style="border-top: 4px solid #27ae60;">
                            <i class="fa fa-balance-scale" style="color: #27ae60;"></i>
                            <h3>₹<?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($total_stock_value), 0); ?></h3>
                            <p>Total Stock Value</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card" style="border-top: 4px solid #f39c12;">
                            <i class="fa fa-tags" style="color: #f39c12;"></i>
                            <h3><?php echo $boughtout_count . '/' . $manufacturing_count; ?></h3>
                            <p>Boughtout / Manufacturing</p>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Section -->
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
                                <!-- Changed method to GET and removed action so it submits to current page -->
                                <form class="form-horizontal" method="get" action="">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Search Item</label>
                                                <input type="text" class="form-control" name="search_item" id="search_item"
                                                    placeholder="Enter Item Code or Name"
                                                    value="<?php echo htmlspecialchars($search_item); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Item Type</label>
                                                <select name="item_type" class="form-control">
                                                    <option value="">All Types</option>
                                                    <option value="B" <?php echo $item_type == 'B' ? 'selected' : ''; ?>>Boughtout</option>
                                                    <option value="M" <?php echo $item_type == 'M' ? 'selected' : ''; ?>>Manufacturing</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Stock Status</label>
                                                <select name="stock_status" class="form-control">
                                                    <option value="">All</option>
                                                    <option value="low" <?php echo $stock_status == 'low' ? 'selected' : ''; ?>>Low Stock (≤5)</option>
                                                    <option value="ok" <?php echo $stock_status == 'ok' ? 'selected' : ''; ?>>In Stock (>5)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Sort By</label>
                                                <select name="sort_by" class="form-control">
                                                    <option value="date_added" <?php echo $sort_by == 'date_added' ? 'selected' : ''; ?>>Date Modified</option>
                                                    <option value="stock" <?php echo $sort_by == 'stock' ? 'selected' : ''; ?>>Stock Level</option>
                                                    <option value="cost_price" <?php echo $sort_by == 'cost_price' ? 'selected' : ''; ?>>Cost Price</option>
                                                    <option value="sell_price" <?php echo $sort_by == 'sell_price' ? 'selected' : ''; ?>>Sell Price</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top: 25px; display: flex; flex-wrap: wrap; gap: 6px;">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-filter"></i> Apply Filters
                                                </button>
                                                <a href="<?php echo base_url('InventoryController/inventory_history'); ?>" class="btn btn-default btn-sm">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </a>
                                                <button type="button" class="btn btn-success btn-sm" onclick="exportTable()">
                                                    <i class="fa fa-download"></i> Export
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Table -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info enhanced-card">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-list-alt"></i> Inventory History Details
                                    <small class="text-muted">Total <?php echo $total_items; ?> items found</small>
                                </h3>
                                
                                <div class="box-tools pull-right" style="display: flex; align-items: center; gap: 8px;">
                                    <!-- <a href="<?php echo base_url('MaterialIssueController/create'); ?>" class="btn btn-warning btn-xs" style="color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px;">
                                        <i class="fa fa-plus"></i> Add Issue for Job Order
                                    </a> -->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-box-tool" data-widget="maximize">
                                            <i class="fa fa-expand"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="box-body" style="padding: 15px;">
                                <?php if (!empty($result)): ?>
                                    <table id="exampleInventoryHistory" class="table table-bordered table-enhance">                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Description</th>
                                                <th>HSN/SAC</th>
                                                <th>GST%</th>
                                                <th>Type</th>
                                                <th>Stock</th>
                                                <th>Allocated</th>
                                                <th>Issued</th>
                                                <th>Available</th>
                                                <th>Unit</th>
                                                <th>Cost</th>
                                                <th>Sell</th>
                                                <th>Modified</th>
                                                <th class="text-center sticky-action-col">Actions</th>
                                            </tr>
                                        </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($result as $key):
                                                     $stock_class = $key->stock <= 5 ? 'stock-low' : 'stock-ok';
                                                     $status_class = $key->stock <= 5 ? 'status-low' : 'status-ok';
                                                    $type_class = $key->item_type == 'B' ? 'badge-boughtout' : 'badge-manufacturing';
                                                    $type_text = $key->item_type == 'B' ? 'Boughtout' : 'Manufacturing';
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i; ?>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo $key->code; ?></strong>
                                                        </td>
                                                        <td>
                                                            <?php echo !empty($key->item_name) ? $key->item_name : $key->code; ?>
                                                        </td>
                                                        <td>
                                                            <div style="max-width: 250px; word-wrap: break-word;">
                                                                <?php echo $key->prod_description; ?>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $key->hsn; ?></td>
                                                        <td>
                                                            <span class="label label-default"><?php echo $key->gst_per; ?>%</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge-type <?php echo $type_class; ?>">
                                                                <?php echo $type_text; ?>
                                                            </span>
                                                        </td>
                                                        <td class="<?php echo $stock_class; ?>">
                                                            <span class="status-indicator <?php echo $status_class; ?>"></span>
                                                            <?php echo $key->stock; ?>
                                                        </td>
                                                         <td>
                                                             <span class="label label-warning"><?php echo number_format(floatval($key->allocated_stock ?? 0), 2); ?></span>
                                                             <?php 
                                                             $inv_id = $key->inventory_id;
                                                             if (!empty($allocations[$inv_id])): 
                                                             ?>
                                                                 <div style="font-size: 10px; margin-top: 4px; line-height: 1.3; color: #7f8c8d; max-width: 200px; text-align: left;">
                                                                     <?php foreach ($allocations[$inv_id] as $alloc): 
                                                                         $qty = floatval($alloc['allocated_quantity'] - $alloc['issued_quantity']);
                                                                         if ($qty <= 0) continue;
                                                                         
                                                                         $dest = '';
                                                                         if (!empty($alloc['joborder_number'])) {
                                                                             $dest = 'JO: ' . $alloc['joborder_number'];
                                                                             if (!empty($alloc['salesorder_number'])) {
                                                                                 $dest .= ' (SO: ' . $alloc['salesorder_number'] . ')';
                                                                             }
                                                                         } else if (!empty($alloc['notes']) && preg_match('/Sales Order Allocation:\s*(\S+)/', $alloc['notes'], $matches)) {
                                                                             $dest = 'SO: ' . $matches[1];
                                                                         } else {
                                                                             $dest = $alloc['notes'];
                                                                         }
                                                                     ?>
                                                                         <div style="border-bottom: 1px dashed #eee; padding: 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($dest); ?>"><?php echo htmlspecialchars(number_format($qty, 2) . ' — ' . $dest); ?></div>
                                                                     <?php endforeach; ?>
                                                                 </div>
                                                             <?php endif; ?>
                                                         </td>
                                                        <?php
                                                        // --- ISSUED COLUMN ---
                                                        $inv_id_issued = $key->inventory_id;
                                                        $has_issued    = !empty($issued_data[$inv_id_issued]);
                                                        $issued_row    = $has_issued ? $issued_data[$inv_id_issued] : null;
                                                        $modal_id      = 'issuedModal_' . $inv_id_issued;
                                                        ?>
                                                        <td style="vertical-align: middle;">
                                                            <?php if ($has_issued && floatval($issued_row['total_issued_qty']) > 0): ?>
                                                                <div class="issued-cell">
                                                                    <span class="issued-badge"><?php echo number_format(floatval($issued_row['total_issued_qty']), 2); ?></span>
                                                                    <span class="issued-eye-btn"
                                                                          data-toggle="modal"
                                                                          data-target="#<?php echo $modal_id; ?>"
                                                                          title="View JO & Slip Details">
                                                                        <i class="fa fa-eye"></i>
                                                                    </span>
                                                                </div>
                                                            <?php else: ?>
                                                                <span class="text-muted" style="font-size:11px;">&#8212;</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="label label-info"><?php echo number_format(floatval($key->available_stock ?? 0), 2); ?></span>
                                                        </td>
                                                        <td><?php echo $key->unit; ?></td>
                                                        <td class="text-primary">
                                                            <strong>₹<?php echo indian_number_format(round($key->cost_price), 0); ?></strong>
                                                        </td>
                                                        <td class="text-success">
                                                            <strong>₹<?php echo indian_number_format(round($key->sell_price), 0); ?></strong>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <i class="fa fa-calendar"></i>
                                                                <?php echo date('d-m-Y', strtotime($key->date_added)); ?>
                                                            </small>
                                                        </td>
                                                        <td class="text-center sticky-action-col">
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 12px; font-weight: 600; font-size: 12px; border-radius: 4px; border: 1px solid #ccc; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                                                                    Action <span class="caret" style="margin-left: 3px;"></span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-right text-left" style="min-width: 175px; box-shadow: 0 6px 18px rgba(0,0,0,0.15); border-radius: 6px; padding: 6px 0; margin-top: 4px;">
                                                                    <li>
                                                                        <a href="<?php echo base_url('InventoryController/get_inventory_by_id/' . $key->inventory_id); ?>" style="padding: 7px 15px; font-size: 13px; display: block;">
                                                                            <i class="fa fa-pencil-square text-primary" style="margin-right: 8px; width: 16px;"></i> Edit Item
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?php echo base_url('MaterialIssueController/stock_ledger/' . $key->inventory_id); ?>" style="padding: 7px 15px; font-size: 13px; display: block;">
                                                                            <i class="fa fa-history text-info" style="margin-right: 8px; width: 16px;"></i> Stock History
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?php echo base_url('MaterialIssueController/create'); ?>" style="padding: 7px 15px; font-size: 13px; display: block;">
                                                                            <i class="fa fa-share-square-o text-warning" style="margin-right: 8px; width: 16px;"></i> Issue Material
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?php echo base_url('InventoryController/get_inventory_by_id_to_generate_bar_code/' . $key->inventory_id); ?>" style="padding: 7px 15px; font-size: 13px; display: block;">
                                                                            <i class="fa fa-barcode text-success" style="margin-right: 8px; width: 16px;"></i> Generate Barcode
                                                                        </a>
                                                                    </li>
                                                                    <li role="separator" class="divider" style="margin: 4px 0;"></li>
                                                                    <li>
                                                                        <a href="<?php echo base_url('InventoryController/delete_inventory_by_id/' . $key->inventory_id); ?>"
                                                                           onclick="return confirm('Are you sure you want to delete this inventory item?')"
                                                                           style="padding: 7px 15px; font-size: 13px; display: block; color: #d9534f;">
                                                                            <i class="fa fa-trash text-danger" style="margin-right: 8px; width: 16px;"></i> Delete Item
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    $i++;
                                                endforeach;
                                                ?>
                                            </tbody>
                                        </table>
                                <?php else: ?>
                                    <div class="text-center" style="padding: 40px;">
                                        <i class="fa fa-cube fa-4x text-muted"></i>
                                        <h3>No Inventory Items Found</h3>
                                        <p class="text-muted">
                                            <?php if ($search_item || $item_type || $stock_status): ?>
                                                No items match your filter criteria. Try adjusting your filters.
                                            <?php else: ?>
                                                No inventory history records available.
                                            <?php endif; ?>
                                        </p>
                                        <a href="<?php echo base_url('InventoryController/index'); ?>"
                                            class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Add New Item
                                        </a>
                                        <?php if ($search_item || $item_type || $stock_status): ?>
                                            <a href="<?php echo base_url('InventoryController/inventory_history'); ?>"
                                                class="btn btn-default">
                                                <i class="fa fa-times"></i> Clear Filters
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo base_url('Home/index'); ?>"
                                                class="btn btn-default">
                                                <i class="fa fa-dashboard"></i> Go to Dashboard
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($result)): ?>
                                <div class="box-footer clearfix">
                                    <div class="pull-left">
                                        <p class="text-muted">
                                            Showing <strong><?php echo $total_items; ?></strong> inventory items
                                            <?php if ($low_stock_items > 0): ?>
                                                | <span class="text-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo $low_stock_items; ?> items low on stock</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo base_url('InventoryController/index'); ?>"
                                            class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to Products
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Modals for Issued Details -->
                <?php 
                if (!empty($result)):
                    foreach ($result as $key): 
                        $inv_id_issued = $key->inventory_id;
                        $has_issued    = !empty($issued_data[$inv_id_issued]);
                        $issued_row    = $has_issued ? $issued_data[$inv_id_issued] : null;
                        $modal_id      = 'issuedModal_' . $inv_id_issued;
                        if ($has_issued && floatval($issued_row['total_issued_qty']) > 0):
                ?>
                            <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $modal_id; ?>Label">
                                <div class="modal-dialog modal-md" role="document">
                                    <div class="modal-content" style="border-radius:10px; overflow:hidden; border:none;">
                                        <div class="modal-header" style="background:linear-gradient(135deg,#27ae60,#1e8449); color:#000; border:none; padding:15px 20px;">
    <button type="button" class="close" data-dismiss="modal" style="color:#000; opacity:1; font-size:20px;">&times;</button>

    <h4 class="modal-title" id="<?php echo $modal_id; ?>Label" style="font-weight:700; font-size:15px; color:#000;">
        <i class="fa fa-list-alt"></i>
        Issued Details &mdash;
        <strong><?php echo htmlspecialchars(!empty($key->item_name) ? $key->item_name : $key->code); ?></strong>

        <span style="background:rgba(255,255,255,0.6); border-radius:4px; padding:2px 8px; margin-left:8px; font-size:13px; color:#000;">
            <?php echo number_format(floatval($issued_row['total_issued_qty']), 2); ?> units
        </span>
    </h4>
</div>
                                        <div class="modal-body" style="padding:20px; max-height:400px; overflow-y:auto;">

                                            <?php if (!empty($issued_row['jo_entries'])): ?>
                                            <div style="margin-bottom:16px;">
                                                <div style="font-weight:700; color:#1e8449; font-size:13px; margin-bottom:8px; padding-bottom:6px; border-bottom:2px solid #eafaf1;">
                                                    <i class="fa fa-wrench"></i> Job Orders
                                                    <span class="badge" style="background:#27ae60; color:#fff; margin-left:6px;"><?php echo count($issued_row['jo_entries']); ?></span>
                                                </div>
                                                <?php foreach ($issued_row['jo_entries'] as $jo_entry): ?>
                                                    <?php
                                                    $jo_href = !empty($jo_entry['jo_id'])
                                                        ? base_url('JobOrderController/show_joborder/' . $jo_entry['jo_id'])
                                                        : base_url('JobOrderController/index');
                                                    ?>
                                                    <a href="<?php echo $jo_href; ?>" target="_blank" class="issued-modal-link-jo">
                                                        <span class="issued-modal-icon-jo">
                                                            <i class="fa fa-external-link"></i>
                                                        </span>
                                                        <?php echo htmlspecialchars($jo_entry['jo_number']); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($issued_row['slip_entries'])): ?>
                                            <div>
                                                <div style="font-weight:700; color:#555; font-size:13px; margin-bottom:8px; padding-bottom:6px; border-bottom:2px solid #f5f5f5;">
                                                    <i class="fa fa-file-text-o"></i> Material Issue Slips
                                                    <span class="badge" style="background:#6c757d; color:#fff; margin-left:6px;"><?php echo count($issued_row['slip_entries']); ?></span>
                                                </div>
                                                <?php foreach ($issued_row['slip_entries'] as $slip_entry): ?>
                                                    <?php
                                                    $slip_href = !empty($slip_entry['slip_id'])
                                                        ? base_url('MaterialIssueController/view/' . $slip_entry['slip_id'])
                                                        : base_url('MaterialIssueController/index');
                                                    ?>
                                                    <a href="<?php echo $slip_href; ?>" target="_blank" class="issued-modal-link-slip">
                                                        <span class="issued-modal-icon-slip">
                                                            <i class="fa fa-file-text"></i>
                                                        </span>
                                                        <?php echo htmlspecialchars($slip_entry['slip_no']); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>

                                        </div>
                                        <div class="modal-footer" style="border:none; padding:10px 20px;">
                                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                                                <i class="fa fa-times"></i> Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <?php 
                        endif;
                    endforeach; 
                endif;
                ?>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <script>
        $(function() {
            // Initialize autocomplete if barcode data exists
            <?php if (isset($barcode) && !empty($barcode)): ?>
                var availableBarcodes = [
                    <?php foreach ($barcode as $key): ?> "<?php echo addslashes($key->barcode); ?>",
                    <?php endforeach; ?>
                ];

                $("#search_item").autocomplete({
                    source: availableBarcodes,
                    minLength: 2
                });
            <?php endif; ?>

            // Initialize DataTable with custom configuration
            if ($('#exampleInventoryHistory').length) {
                var $invTable = $('#exampleInventoryHistory');
                if ($.fn.DataTable.isDataTable($invTable)) {
                    $invTable.DataTable().destroy();
                }
                var table = $invTable.DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": false,
                    "pageLength": 25,
                    "order": [],
                    "language": {
                        "search": "Search Inventory History:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ items",
                        "infoEmpty": "No items to show",
                        "paginate": {
                            "previous": "<i class='fa fa-chevron-left'></i>",
                            "next": "<i class='fa fa-chevron-right'></i>"
                        }
                    },
                    "dom": "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                           "<'inv-table-wrap'tr>" +
                           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "drawCallback": function(settings) {
                        initIssuedTooltips();
                    }
                });
            }

            // Add row hover effects
            $('table tbody tr').hover(function() {
                $(this).addClass('bg-light');
            }, function() {
                $(this).removeClass('bg-light');
            });

            // Show/hide filter section based on URL parameters
            function checkFilters() {
                var hasFilters = window.location.search.includes('search_item') ||
                    window.location.search.includes('item_type') ||
                    window.location.search.includes('stock_status') ||
                    window.location.search.includes('sort_by');

                if (hasFilters) {
                    $('.box-primary .box-tools .btn-box-tool').click();
                }
            }

            checkFilters();
        });

        function exportTable() {
            // Build export URL with current filters
            var baseUrl = '<?php echo base_url("InventoryController/export_inventory"); ?>';

            // Get all filter parameters
            var searchItem = $('#search_item').val();
            var itemType = $('[name="item_type"]').val();
            var stockStatus = $('[name="stock_status"]').val();
            var sortBy = $('[name="sort_by"]').val();

            // Build query string
            var params = [];
            if (searchItem) params.push('search_item=' + encodeURIComponent(searchItem));
            if (itemType) params.push('item_type=' + encodeURIComponent(itemType));
            if (stockStatus) params.push('stock_status=' + encodeURIComponent(stockStatus));
            if (sortBy) params.push('sort_by=' + encodeURIComponent(sortBy));

            var queryString = params.length > 0 ? '?' + params.join('&') : '';

            // Redirect to export URL
            window.location.href = baseUrl + queryString;
        }
        // Initialize eye tooltips (also re-init on DataTable redraw)
        function initIssuedTooltips() {
            $('.issued-eye-btn').tooltip('destroy').tooltip({
                container: 'body',
                trigger: 'hover'
            });
        }
        initIssuedTooltips();
    </script>
</body>
