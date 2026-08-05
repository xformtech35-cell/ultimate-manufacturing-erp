<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);

$totalRequired    = 0;
$totalIssued      = 0;
$totalPending     = 0;
$shortageCount    = 0;
$allocatableCount = 0;
$fulfilledCount   = 0;
$pendingCount     = 0;
$totalItemsCount  = count($mrp_items ?? []);

if (!empty($mrp_items)) {
    foreach ($mrp_items as $item) {
        $totalRequired += $item['total_required_qty'];
        $totalIssued   += $item['total_issued_qty'];
        $totalPending  += $item['pending_qty'];
        $st = $item['status'] ?? 'pending';
        if ($st === 'shortage' || ($item['shortage'] ?? 0) > 0) {
            $shortageCount++;
        }
        if (($item['available_stock'] ?? 0) > 0 && $st !== 'fulfilled') {
            $allocatableCount++;
        }
        if ($st === 'fulfilled') {
            $fulfilledCount++;
        }
        if ($st === 'pending') {
            $pendingCount++;
        }
    }
}
?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <i class="fa fa-cogs"></i> MRP Explosion Details
                <small>Order: <?php echo htmlspecialchars($so_info['number_fk']); ?></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo base_url() . 'MaterialIssueController/sales_order_mrp/'; ?>">BOM Order MRP</a></li>
                <li class="active">MRP Details</li>
            </ol>
        </section>

        <section class="content">

            <!-- Back & Export Buttons -->
            <div class="row" style="margin-bottom:15px;">
                <div class="col-xs-12">
                    <a href="<?php echo base_url(); ?>MaterialIssueController/sales_order_mrp" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back to list</a>
                    <button class="btn btn-success btn-sm pull-right" onclick="exportMRP()"><i class="fa fa-download"></i> Export CSV</button>
                    <button class="btn btn-default btn-sm pull-right" onclick="window.print()" style="margin-right:5px;"><i class="fa fa-print"></i> Print Report</button>
                </div>
            </div>

            <!-- Sales Order Summary Header -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid box-default" style="border-top: 3px solid #3c8dbc;">
                        <div class="box-body" style="background:#f9f9f9;">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>SO Number:</strong> <code style="font-size: 14px;"><?php echo htmlspecialchars($so_info['number_fk']); ?></code>
                                </div>
                                <div class="col-md-3">
                                    <strong>Date:</strong> <?php echo date('d-m-Y', strtotime($so_info['date'])); ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Customer:</strong> <?php echo htmlspecialchars($so_info['company_name'] ?: $so_info['fullname']); ?>
                                </div>
                                 <?php if ($_has_project_master): ?>
                                 <div class="col-md-3">
                                     <strong>Project Code:</strong> <span class="label label-info"><?php echo htmlspecialchars($so_info['project_code'] ?: 'N/A'); ?></span>
                                 </div>
                                 <?php endif; ?>
                            </div>
                            <hr style="margin: 10px 0;">
                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-12">
                                    <strong>Associated BOM Number(s):</strong>
                                    <?php if (!empty($associated_boms)): ?>
                                        <?php foreach ($associated_boms as $bom_no): ?>
                                            <span class="label label-success" style="font-size: 11px; margin-right: 5px; padding: 4px 8px; display: inline-block;">
                                                <i class="fa fa-file-text-o"></i> <?php echo htmlspecialchars($bom_no); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr style="margin: 10px 0;">
                            <div>
                                <strong>Items in Sales Order:</strong>
                                <div style="margin-top: 5px;">
                                    <?php foreach ($so_items as $so_item): ?>
                                        <?php if (isset($so_item['product_name']) && $so_item['product_name'] === '__HEADING__') continue; ?>
                                        <span class="badge bg-purple" style="margin-right: 5px; padding: 6px 10px; font-size: 11px;">
                                            <i class="fa fa-cube"></i> <?php echo htmlspecialchars($so_item['finished_good_name'] ?: $so_item['product_name']); ?> 
                                            (Qty: <strong><?php echo floatval($so_item['quantity']); ?></strong>)
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-tag"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Unique Components</span>
                            <span class="info-box-number"><?php echo count($mrp_items); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-blue"><i class="fa fa-cubes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Required Components</span>
                            <span class="info-box-number"><?php echo number_format($totalRequired, 2); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-orange"><i class="fa fa-hourglass-half"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Pending Issue</span>
                            <span class="info-box-number"><?php echo number_format($totalPending, 2); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Components Shortage</span>
                            <span class="info-box-number"><?php echo $shortageCount; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selection Action Bar -->
            <div class="selection-bar" id="selectionBar">
                <span id="selectionCount"><i class="fa fa-info-circle"></i> Select items below to generate a Purchase Requisition or allocate stock</span>
                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <button class="btn-generate-pr" id="btnGeneratePR" disabled onclick="generatePR()">
                        <i class="fa fa-file-text"></i> Generate PR for Selected
                    </button>
                    <button id="btnBulkAllocate" disabled onclick="bulkAllocate()" style="background:#16a34a; color:#fff; border:1px solid #22c55e; padding:8px 14px; border-radius:8px; cursor:not-allowed; font-size:13px; font-weight:600; opacity:0.5; transition: all 0.2s;">
                        <i class="fa fa-check"></i> Bulk Allocate Selected
                    </button>
                    <button class="btn-allocate" style="background:#dc2626; color:#fff; border:1px solid #ef4444; padding:8px 14px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" onclick="selectAllShortages()">
                        <i class="fa fa-check-square-o"></i> Select All PR Items (<?php echo $shortageCount; ?>)
                    </button>
                    <button class="btn-allocate" style="background:#16a34a; color:#fff; border:1px solid #22c55e; padding:8px 14px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" onclick="selectAllAllocatable()">
                        <i class="fa fa-check-circle-o"></i> Select All Allocates (<?php echo $allocatableCount; ?>)
                    </button>
                    <button style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.2); padding:8px 14px; border-radius:8px; cursor:pointer; font-size:13px;" onclick="clearSelection()">
                        <i class="fa fa-times"></i> Clear Selection
                    </button>
                </div>
            </div>

            <!-- Filters with live counts -->
            <div class="row" style="margin-bottom:12px;">
                <div class="col-sm-12">
                    <div class="btn-group" style="display:flex; gap:6px; flex-wrap:wrap;">
                        <button class="btn btn-default btn-sm active" id="filter-all">
                            <i class="fa fa-list"></i> All Items <span class="badge bg-black" style="margin-left:4px;"><?php echo $totalItemsCount; ?></span>
                        </button>
                        <button class="btn btn-danger btn-sm" id="filter-shortage">
                            <i class="fa fa-exclamation-triangle"></i> Shortages / PR Required <span class="badge bg-red" style="margin-left:4px;"><?php echo $shortageCount; ?></span>
                        </button>
                        <button class="btn btn-success btn-sm" id="filter-allocatable">
                            <i class="fa fa-check-square"></i> Stock Available / Allocate <span class="badge bg-green" style="margin-left:4px;"><?php echo $allocatableCount; ?></span>
                        </button>
                        <button class="btn btn-warning btn-sm" id="filter-pending">
                            <i class="fa fa-hourglass-half"></i> Pending <span class="badge bg-yellow" style="margin-left:4px;"><?php echo $pendingCount; ?></span>
                        </button>
                        <button class="btn btn-primary btn-sm" id="filter-fulfilled">
                            <i class="fa fa-check-circle"></i> Fulfilled / Issued <span class="badge bg-blue" style="margin-left:4px;"><?php echo $fulfilledCount; ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Exploded BOM Requirements Table -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-gears"></i> Exploded Material Requirements List</h3>
                        </div>
                        <div class="box-body">
                            <table id="mrpExplosionTable" class="table table-bordered table-hover" style="min-width: 1450px !important; width: 100% !important; margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px; text-align: center;"><input type="checkbox" id="chkAll" class="mrp-check" onchange="toggleAllShortage(this)"></th>
                                            <th style="width: 35px; text-align: center;">#</th>
                                            <th style="width: 100px;">Component Code</th>
                                            <th style="width: 170px;">Component Name</th>
                                            <th style="width: 50px;" class="text-center">Unit</th>
                                            <th class="text-right" style="width: 90px;">Gross Required</th>
                                            <th class="text-right" style="width: 85px;">Total Issued</th>
                                            <th class="text-right" style="width: 85px;">Net Pending</th>
                                            <th class="text-right" style="width: 95px;">Stock Available</th>
                                            <th class="text-right" style="width: 85px;">Shortage</th>
                                            <th class="text-center" style="width: 85px;">Status</th>
                                            <th style="width: 120px;">Source BOM</th>
                                            <th style="width: 120px;">FG Source</th>
                                            <th class="text-center" style="width: 100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($mrp_items)): ?>
                                            <?php $sr = 1; foreach ($mrp_items as $item): ?>
                                                <?php
                                                    $item_status = $item['status'] ?? 'pending';
                                                    if ($item_status === 'fulfilled') {
                                                        $row_status = 'fulfilled';
                                                        $status_label = '<span class="label label-success">Fulfilled</span>';
                                                    } elseif ($item_status === 'allocated') {
                                                        $row_status = 'allocated';
                                                        $status_label = '<span class="label label-primary" style="background-color: #3b82f6;">Allocated</span>';
                                                    } elseif ($item_status === 'shortage') {
                                                        $row_status = 'shortage';
                                                        $status_label = '<span class="label label-danger">Short Stock</span>';
                                                    } else {
                                                        $row_status = 'pending';
                                                        $status_label = '<span class="label label-warning">Pending</span>';
                                                    }
                                                ?>
                                                <tr class="mrp-row <?php echo $row_status === 'shortage' ? 'shortage-row' : ''; ?>"
                                                    data-status="<?php echo $row_status; ?>"
                                                    data-code="<?php echo htmlspecialchars($item['item_code']); ?>"
                                                    data-name="<?php echo htmlspecialchars($item['item_name']); ?>"
                                                    data-unit="<?php echo htmlspecialchars($item['unit']); ?>"
                                                    data-shortage="<?php echo $item['shortage']; ?>"
                                                    data-gross="<?php echo $item['total_required_qty']; ?>"
                                                    data-stock="<?php echo $item['available_stock']; ?>"
                                                    data-inventory-id="<?php echo intval($item['inventory_id']); ?>">
                                                    
                                                     <!-- Checkbox (for all items) -->
                                                     <td class="text-center" onclick="event.stopPropagation()">
                                                          <?php if ($row_status === 'shortage' && empty($item['has_pr'])): ?>
                                                              <input type="checkbox" class="mrp-check shortage-check"
                                                                     value="<?php echo htmlspecialchars($item['item_code']); ?>"
                                                                     onchange="updateSelection()">
                                                          <?php elseif (($row_status === 'pending' || $row_status === 'shortage') && $item['available_stock'] > 0): ?>
                                                              <input type="checkbox" class="mrp-check allocatable-check"
                                                                     data-inventory-id="<?php echo intval($item['inventory_id']); ?>"
                                                                     data-qty="<?php echo $item['total_required_qty']; ?>"
                                                                     value="<?php echo htmlspecialchars($item['item_code']); ?>"
                                                                     onchange="updateSelection()">
                                                          <?php else: ?>
                                                              <input type="checkbox" disabled class="mrp-check" style="opacity: 0.3; cursor: not-allowed;">
                                                          <?php endif; ?>
                                                      </td>
                                                    <td><?php echo $sr++; ?></td>
                                                    <td><code><?php echo htmlspecialchars($item['item_code']); ?></code></td>
                                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                                    <td class="text-right font-weight-bold"><?php echo number_format($item['total_required_qty'], 2); ?></td>
                                                    <td class="text-right text-success"><?php echo number_format($item['total_issued_qty'], 2); ?></td>
                                                    <td class="text-right <?php echo $item['pending_qty'] > 0 ? 'text-warning' : ''; ?>">
                                                        <?php echo number_format($item['pending_qty'], 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($item['available_stock'], 2); ?>
                                                        <?php if (isset($item['allocated_qty']) && $item['allocated_qty'] > 0): ?>
                                                            <br><span class="label label-info" style="font-size:9px; padding: 1px 4px; display:inline-block; margin-top:2px;">Alloc: <?php echo number_format($item['allocated_qty'], 2); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php if ($item['shortage'] > 0): ?>
                                                            <strong class="text-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo number_format($item['shortage'], 2); ?></strong>
                                                        <?php else: ?>
                                                            <span class="text-success"><i class="fa fa-check"></i> OK</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center"><?php echo $status_label; ?></td>
                                                    <td><small class="text-muted"><?php echo htmlspecialchars($item['bom_source']); ?></small></td>
                                                    <td><small class="text-muted"><?php echo htmlspecialchars($item['finished_good']); ?></small></td>
                                                    <td class="text-center" onclick="event.stopPropagation()">
                                                         <?php if ($row_status === 'shortage'): ?>
                                                             <?php if (!empty($item['has_pr'])): ?>
                                                                 <button class="btn-row-pr" disabled style="opacity: 0.65; cursor: not-allowed; background: #6c757d; border-color: #6c757d; color: #fff;" title="PR already generated (<?php echo htmlspecialchars($item['pr_info']['pr_no'] ?? ''); ?>)">
                                                                     <i class="fa fa-check-circle"></i> PR Generated
                                                                 </button>
                                                             <?php else: ?>
                                                                 <button class="btn-row-pr" onclick="generateSinglePR(
                                                                     '<?php echo htmlspecialchars($item['item_code']); ?>',
                                                                     '<?php echo htmlspecialchars(addslashes($item['item_name'])); ?>',
                                                                     '<?php echo $item['shortage']; ?>',
                                                                     '<?php echo htmlspecialchars($item['unit']); ?>',
                                                                     '<?php echo $item['total_required_qty']; ?>',
                                                                     '<?php echo $item['available_stock']; ?>'
                                                                 )">
                                                                      <i class="fa fa-file-text"></i> PR
                                                                 </button>
                                                             <?php endif; ?>
                                                             <?php if ($item['available_stock'] > 0): ?>
                                                                 <button class="btn-row-allocate" style="margin-top: 5px; display: inline-block;" onclick="allocateSoItem(
                                                                     '<?php echo htmlspecialchars($item['item_code']); ?>',
                                                                     '<?php echo intval($item['inventory_id']); ?>',
                                                                     '<?php echo $item['total_required_qty']; ?>'
                                                                 )">
                                                                     <i class="fa fa-check"></i> Allocate
                                                                 </button>
                                                             <?php endif; ?>
                                                             <?php if (isset($item['allocated_qty']) && $item['allocated_qty'] > 0): ?>
                                                                 <div style="margin-top: 5px; border-top: 1px dashed #ddd; padding-top: 5px;">
                                                                     <span style="color:#3b82f6; font-size:11px; font-weight:600; display:block; margin-bottom:4px;"><i class="fa fa-check-circle"></i> Allocated: <?php echo floatval($item['allocated_qty']); ?></span>
                                                                     <button class="btn-row-deallocate" onclick="deallocateSoItem(
                                                                         '<?php echo htmlspecialchars($item['item_code']); ?>',
                                                                         '<?php echo intval($item['inventory_id']); ?>',
                                                                         '<?php echo floatval($item['allocated_qty']); ?>'
                                                                     )">
                                                                         <i class="fa fa-undo"></i> Return
                                                                     </button>
                                                                 </div>
                                                             <?php endif; ?>
                                                         <?php elseif ($row_status === 'allocated'): ?>
                                                             <span style="color:#3b82f6; font-size:11px; font-weight:600; display:block; margin-bottom:4px;"><i class="fa fa-check-circle"></i> Allocated: <?php echo floatval($item['allocated_qty']); ?></span>
                                                             <button class="btn-row-deallocate" onclick="deallocateSoItem(
                                                                 '<?php echo htmlspecialchars($item['item_code']); ?>',
                                                                 '<?php echo intval($item['inventory_id']); ?>',
                                                                 '<?php echo floatval($item['allocated_qty']); ?>'
                                                             )">
                                                                 <i class="fa fa-undo"></i> Return
                                                               </button>
                                                         <?php elseif ($row_status === 'fulfilled'): ?>
                                                             <span style="color:#16a34a; font-size:11px; font-weight:600;"><i class="fa fa-check-circle"></i> Fulfilled</span>
                                                         <?php else: ?>
                                                             <button class="btn-row-allocate" onclick="allocateSoItem(
                                                                 '<?php echo htmlspecialchars($item['item_code']); ?>',
                                                                 '<?php echo intval($item['inventory_id']); ?>',
                                                                 '<?php echo $item['total_required_qty']; ?>'
                                                             )">
                                                                 <i class="fa fa-check"></i> Allocate
                                                             </button>
                                                         <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <?php if (!empty($mrp_items)): ?>
                                        <tfoot>
                                            <tr style="background:#ecf0f1; font-weight:bold;">
                                                <td colspan="5" class="text-right" style="padding-right:12px;">TOTALS:</td>
                                                <td class="text-right"><?php echo number_format($totalRequired, 2); ?></td>
                                                <td class="text-right text-success"><?php echo number_format($totalIssued, 2); ?></td>
                                                <td class="text-right text-warning"><?php echo number_format($totalPending, 2); ?></td>
                                                <td colspan="6"></td>
                                            </tr>
                                        </tfoot>
                                    <?php endif; ?>
                                </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
</div>

<!-- Toast Container -->
<div class="mrp-toast" id="mrpToast"></div>

<!-- Scripts -->

<script>
var mrpTable;
var BASE_URL = '<?php echo base_url(); ?>';

$(document).ready(function() {
    mrpTable = $('#mrpExplosionTable').DataTable({
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>><"mrp-table-scroll-wrapper"t><"row"<"col-sm-5"i><"col-sm-7"p>>',
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "pageLength": 25,
        "columnDefs": [
            { "orderable": false, "targets": [0, 10, 11, 12, 13] }
        ],
        "language": {
            "search": "Search Components:",
            "emptyTable": '<div class="text-center" style="padding:30px; color:#999;"><i class="fa fa-info-circle fa-2x"></i><br><br>No exploded component requirements found. Make sure the Finished Goods in this Sales Order have associated BOMs.</div>',
            "zeroRecords": "No matching components found"
        }
    });

    // Custom Filters
    function applyFilter(status) {
        $.fn.dataTable.ext.search = [];
        if (status !== 'all') {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var node = mrpTable.row(dataIndex).node();
                if (status === 'allocatable') {
                    var stock = parseFloat($(node).data('stock')) || 0;
                    var st = $(node).data('status');
                    return stock > 0 && st !== 'fulfilled';
                }
                return $(node).data('status') === status;
            });
        }
        mrpTable.draw();
    }

    $('#filter-all').on('click', function() { applyFilter('all'); setBtnActive(this); });
    $('#filter-shortage').on('click', function() { applyFilter('shortage'); setBtnActive(this); });
    $('#filter-allocatable').on('click', function() { applyFilter('allocatable'); setBtnActive(this); });
    $('#filter-pending').on('click', function() { applyFilter('pending'); setBtnActive(this); });
    $('#filter-fulfilled').on('click', function() { applyFilter('fulfilled'); setBtnActive(this); });

    function setBtnActive(el) {
        $('.btn-group .btn').removeClass('active');
        $(el).addClass('active');
    }
    
    // Initial selection update
    updateSelection();
});

// Selection actions across all DataTables pages
function updateSelection() {
    var nodes = mrpTable ? mrpTable.rows().nodes() : [];
    var prChecked = $(nodes).find('.shortage-check:checked').length;
    var allocChecked = $(nodes).find('.allocatable-check:checked').length;
    var totalChecked = prChecked + allocChecked;

    var btnPr = document.getElementById('btnGeneratePR');
    if (btnPr) {
        btnPr.disabled = (prChecked === 0);
        if (prChecked > 0) {
            btnPr.style.opacity = '1';
            btnPr.style.cursor = 'pointer';
        } else {
            btnPr.style.opacity = '0.5';
            btnPr.style.cursor = 'not-allowed';
        }
    }

    var btnAlloc = document.getElementById('btnBulkAllocate');
    if (btnAlloc) {
        btnAlloc.disabled = (allocChecked === 0);
        if (allocChecked > 0) {
            btnAlloc.style.opacity = '1';
            btnAlloc.style.cursor = 'pointer';
        } else {
            btnAlloc.style.opacity = '0.5';
            btnAlloc.style.cursor = 'not-allowed';
        }
    }

    if (totalChecked === 0) {
        document.getElementById('selectionCount').innerHTML =
            '<i class="fa fa-info-circle"></i> Select items below to generate a Purchase Requisition or allocate stock';
    } else {
        var parts = [];
        if (prChecked > 0) parts.push('<strong>' + prChecked + '</strong> for PR');
        if (allocChecked > 0) parts.push('<strong>' + allocChecked + '</strong> for Stock Allocation');
        document.getElementById('selectionCount').innerHTML =
            '<i class="fa fa-check-circle" style="color:#43e97b"></i> Selected ' + parts.join(' and ') + ' (across all pages)';
    }
}

function selectAllShortages() {
    if (!mrpTable) return;
    clearSelection();
    var nodes = mrpTable.rows({ search: 'applied' }).nodes();
    $(nodes).each(function() {
        var st = $(this).data('status');
        var shortage = parseFloat($(this).data('shortage')) || 0;
        if (st === 'shortage' || shortage > 0) {
            $(this).find('.shortage-check').prop('checked', true);
        }
    });
    updateSelection();
}

function selectAllAllocatable() {
    if (!mrpTable) return;
    clearSelection();
    var nodes = mrpTable.rows({ search: 'applied' }).nodes();
    $(nodes).each(function() {
        var stock = parseFloat($(this).data('stock')) || 0;
        var st = $(this).data('status');
        if (stock > 0 && st !== 'fulfilled') {
            $(this).find('.allocatable-check').prop('checked', true);
        }
    });
    updateSelection();
}

function clearSelection() {
    if (mrpTable) {
        var nodes = mrpTable.rows().nodes();
        $(nodes).find('.shortage-check, .allocatable-check').prop('checked', false);
    }
    $('#chkAll').prop('checked', false);
    updateSelection();
}

function toggleAllShortage(master) {
    if (master.checked) {
        selectAllShortages();
    } else {
        clearSelection();
    }
}

function bulkAllocate() {
    if (!mrpTable) return;
    var nodes = mrpTable.rows().nodes();
    var items = [];
    
    $(nodes).find('.allocatable-check:checked').each(function () {
        items.push({
            item_code: $(this).val(),
            inventory_id: parseInt($(this).data('inventory-id')),
            quantity: parseFloat($(this).data('qty'))
        });
    });

    if (items.length === 0) {
        showToast('⚠️ No items selected for allocation.', 'error');
        return;
    }

    if (!confirm('Are you sure you want to bulk allocate stock for the ' + items.length + ' selected item(s)?')) {
        return;
    }

    var btn = $('#btnBulkAllocate');
    var originalHTML = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Allocating...');

    $.ajax({
        url: BASE_URL + 'MaterialIssueController/ajax_bulk_allocate_so_mrp_items',
        type: 'POST',
        dataType: 'json',
        data: {
            so_number: '<?php echo htmlspecialchars($so_info['number_fk']); ?>',
            items: JSON.stringify(items)
        },
        success: function (res) {
            if (res.success) {
                showToast('✅ ' + res.message, 'success');
                setTimeout(function () { window.location.reload(); }, 1500);
            } else {
                showToast('❌ ' + (res.message || 'Failed to bulk allocate stock'), 'error');
                btn.prop('disabled', false).html(originalHTML);
                updateSelection();
            }
        },
        error: function () {
            showToast('❌ Server error. Please try again.', 'error');
            btn.prop('disabled', false).html(originalHTML);
            updateSelection();
        }
    });
}

// Generate PR (bulk across all pages)
function generatePR() {
    var selected = [];
    if (!mrpTable) return;

    var nodes = mrpTable.rows({ search: 'applied' }).nodes();
    $(nodes).find('.shortage-check:checked').each(function () {
        var row = $(this).closest('tr');
        var shortage = parseFloat(row.data('shortage'));
        var gross = parseFloat(row.data('gross'));
        // If shortage is 0 or less, fallback to the gross required quantity
        var qty = shortage > 0 ? shortage : gross;

        selected.push({
            code:     row.data('code'),
            name:     row.data('name'),
            unit:     row.data('unit'),
            shortage: qty,
            gross:    gross,
            stock:    parseFloat(row.data('stock'))
        });
    });

    if (selected.length === 0) {
        showToast('Please select at least one item.', 'error');
        return;
    }

    if (!confirm('Generate Purchase Requisition for ' + selected.length + ' item(s)?\n\nThis will create a new PR in the system for all selected items across all pages.')) {
        return;
    }

    sendPRRequest(selected);
}

function generateSinglePR(code, name, shortage, unit, gross, stock) {
    var s_qty = parseFloat(shortage);
    var g_qty = parseFloat(gross);
    var qty = s_qty > 0 ? s_qty : g_qty;
    if (!confirm('Generate PR for: ' + name + '\nOrder Qty: ' + qty.toFixed(2) + ' ' + unit)) return;
    sendPRRequest([{ code: code, name: name, unit: unit, shortage: qty, gross: g_qty, stock: parseFloat(stock) }]);
}

function sendPRRequest(items) {
    var btn = document.getElementById('btnGeneratePR');
    btn.disabled = true;
    var originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating...';

    $.ajax({
        url:  BASE_URL + 'MaterialIssueController/ajax_generate_pr_from_mrp',
        type: 'POST',
        data: { 
            items: JSON.stringify(items),
            so_no: '<?php echo htmlspecialchars($so_info['number_fk']); ?>',
            project_code: '<?php echo htmlspecialchars($so_info['project_code'] ?: ''); ?>'
        },
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                showToast('✅ ' + res.message, 'success');
                items.forEach(function(it) {
                    var $row = $('tr.mrp-row[data-code="' + it.code + '"]');
                    if ($row.length) {
                        $row.find('.shortage-check').prop('checked', false).prop('disabled', true).css({ opacity: 0.3, cursor: 'not-allowed' });
                        $row.find('.btn-row-pr').prop('disabled', true)
                            .css({ opacity: 0.65, cursor: 'not-allowed', background: '#6c757d', borderColor: '#6c757d', color: '#fff' })
                            .attr('title', 'PR Generated')
                            .html('<i class="fa fa-check-circle"></i> PR Generated')
                            .removeAttr('onclick');
                    }
                });
                setTimeout(function () {
                    if (confirm(res.message + '\n\nView the generated PR now?')) {
                        window.location.href = res.pr_url;
                    }
                }, 500);
                clearSelection();
            } else {
                showToast('❌ ' + (res.message || 'Failed to generate PR'), 'error');
                alert('❌ Error: ' + (res.message || 'Failed to generate PR'));
            }
        },
        error: function () {
            showToast('❌ Server error. Please try again.', 'error');
        },
        complete: function () {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    });
}

function showToast(msg, type) {
    var t = document.getElementById('mrpToast');
    t.textContent = msg;
    t.className = 'mrp-toast ' + type;
    t.style.display = 'block';
    setTimeout(function () { t.style.display = 'none'; }, 4000);
}

function exportMRP() {
    var rows = [['#', 'Component Code', 'Component Name', 'Unit', 'Required Qty', 'Issued Qty', 'Pending Qty', 'Stock Available', 'Shortage', 'Status', 'Source BOM', 'FG Source'].join(',')];

    mrpTable.rows({ search: 'applied' }).every(function() {
        var cells = [];
        $(this.node()).find('td').each(function(i) {
            if (i === 0) return; // Skip checkbox column in export
            cells.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
        });
        rows.push(cells.join(','));
    });

    var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href = url;
    a.download = 'MRP_Sales_Order_<?php echo htmlspecialchars($so_info['number_fk']); ?>_<?php echo date('Ymd_His'); ?>.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function allocateSoItem(itemCode, inventoryId, qty) {
    if (!confirm('Allocate component ' + itemCode + ' to Job Order(s)?\n\nThis will reserve the required quantity from available stock without physically issuing it.')) {
        return;
    }

    var btn = $(event.target).closest('button');
    var originalHTML = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Allocating...');

    $.ajax({
        url: BASE_URL + 'MaterialIssueController/ajax_allocate_so_mrp_item',
        type: 'POST',
        dataType: 'json',
        data: {
            so_number: '<?php echo htmlspecialchars($so_info['number_fk']); ?>',
            item_code: itemCode,
            inventory_id: inventoryId,
            quantity: qty
        },
        success: function (res) {
            if (res.success) {
                showToast('✅ ' + res.message, 'success');
                setTimeout(function () { window.location.reload(); }, 1000);
            } else {
                showToast('❌ ' + (res.message || 'Failed to allocate stock'), 'error');
                btn.prop('disabled', false).html(originalHTML);
            }
        },
        error: function () {
            showToast('❌ Server error. Please try again.', 'error');
            btn.prop('disabled', false).html(originalHTML);
        }
    });
}

function deallocateSoItem(itemCode, inventoryId, allocatedQty) {
    allocatedQty = parseFloat(allocatedQty) || 0;
    var returnQtyInput = prompt('Enter quantity to return (allocated: ' + allocatedQty + '):', allocatedQty);
    if (returnQtyInput === null) {
        return;
    }
    
    var returnQty = parseFloat(returnQtyInput);
    if (isNaN(returnQty) || returnQty <= 0 || returnQty > allocatedQty) {
        alert('Please enter a valid quantity between 0 and ' + allocatedQty);
        return;
    }

    if (!confirm('Return ' + returnQty.toFixed(2) + ' of component ' + itemCode + ' to available inventory stock?')) {
        return;
    }

    var btn = $(event.target).closest('button');
    var originalHTML = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Returning...');

    $.ajax({
        url: BASE_URL + 'MaterialIssueController/ajax_deallocate_stock',
        type: 'POST',
        dataType: 'json',
        data: {
            so_number: '<?php echo htmlspecialchars($so_info['number_fk']); ?>',
            item_code: itemCode,
            inventory_id: inventoryId,
            quantity: returnQty
        },
        success: function (res) {
            if (res.success) {
                showToast('✅ ' + res.message, 'success');
                setTimeout(function () { window.location.reload(); }, 1000);
            } else {
                showToast('❌ ' + (res.message || 'Failed to cancel allocation'), 'error');
                btn.prop('disabled', false).html(originalHTML);
            }
        },
        error: function () {
            showToast('❌ Server error. Please try again.', 'error');
            btn.prop('disabled', false).html(originalHTML);
        }
    });
}
</script>

<style>
.mrp-table-scroll-wrapper {
    width: 100% !important;
    overflow-x: auto !important;
    display: block !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    margin-top: 10px !important;
    margin-bottom: 15px !important;
    -webkit-overflow-scrolling: touch !important;
}
.mrp-table-scroll-wrapper::-webkit-scrollbar {
    height: 10px !important;
}
.mrp-table-scroll-wrapper::-webkit-scrollbar-track {
    background: #f1f5f9 !important;
    border-radius: 5px !important;
}
.mrp-table-scroll-wrapper::-webkit-scrollbar-thumb {
    background: #3b82f6 !important;
    border-radius: 5px !important;
}
.mrp-table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
    background: #1d4ed8 !important;
}

.mrp-table-scroll-wrapper table,
#mrpExplosionTable {
    width: 100% !important;
    min-width: 1450px !important;
    max-width: none !important;
    border-collapse: collapse !important;
}

#mrpExplosionTable th, #mrpExplosionTable td {
    white-space: nowrap !important;
    vertical-align: middle !important;
    padding: 8px 12px !important;
}

.mrp-row:hover td { background:rgba(60,141,188,.06) !important; }
.shortage-row td { background: #fff8f8 !important; }
.info-box-number { font-size:22px; }

/* Premium Selection bar */
.selection-bar {
    background: linear-gradient(135deg,#1a1f36,#2c3e50);
    color:#fff;
    border-radius:10px;
    padding:16px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:16px;
    gap:12px;
    flex-wrap:wrap;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.selection-bar span { font-size:13px; font-weight:500; }

.btn-generate-pr {
    background: linear-gradient(135deg,#fa709a,#f5576c);
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-generate-pr:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(245,87,108,0.4); }
.btn-generate-pr:disabled { opacity:0.5; cursor:not-allowed; transform:none; }

.btn-allocate {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.25);
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-allocate:hover { background: rgba(255,255,255,0.25); }

.btn-row-pr {
    background:#fff0f3; color:#dc2626;
    border:1px solid #fca5a5;
    padding:3px 10px; border-radius:6px;
    font-size:11px; font-weight:600;
    cursor:pointer; transition:all .15s;
}
.btn-row-pr:hover { background:#dc2626; color:#fff; }
.btn-row-pr:disabled { background:#6c757d; color:#fff; border-color:#6c757d; opacity:0.65; cursor:not-allowed; }

.mrp-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    padding: 14px 24px;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
    z-index: 9999;
    display: none;
    animation: toastSlide 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.mrp-toast.success { background: linear-gradient(135deg,#10b981,#059669); }
.mrp-toast.error   { background: linear-gradient(135deg,#ef4444,#dc2626); }

@keyframes toastSlide {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@media print {
    .main-header, .main-sidebar, .content-header ol, .btn-group, .selection-bar,
    #mrpExplosionTable_filter, #mrpExplosionTable_length,
    #mrpExplosionTable_paginate, #mrpExplosionTable_info, .btn, .btn-row-pr, .btn-row-allocate, .btn-row-deallocate, .mrp-check { display:none !important; }
    .content-wrapper { margin-left:0 !important; }
}
.btn-row-allocate {
    background:#e8fdf0; color:#16a34a;
    border:1px solid #a7f3d0;
    padding:3px 10px; border-radius:6px;
    font-size:11px; font-weight:600;
    cursor:pointer; transition:all .15s;
}
.btn-row-allocate:hover { background:#16a34a; color:#fff; }
.btn-row-deallocate {
    background:#fff7ed; color:#ea580c;
    border:1px solid #ffedd5;
    padding:3px 10px; border-radius:6px;
    font-size:11px; font-weight:600;
    cursor:pointer; transition:all .15s;
}
.btn-row-deallocate:hover { background:#ea580c; color:#fff; }

.table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    width: 100% !important;
    margin-bottom: 15px;
}
#mrpExplosionTable {
    width: 100% !important;
    min-width: 1250px !important;
    border-collapse: collapse !important;
}
#mrpExplosionTable thead th {
    background: #2b7bba !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
    border: 1px solid #23689b !important;
    padding: 9px 10px !important;
}
#mrpExplosionTable tbody td {
    vertical-align: middle !important;
    white-space: nowrap !important;
    padding: 6px 10px !important;
    font-size: 12px !important;
}
</style>
</body>
</html>
