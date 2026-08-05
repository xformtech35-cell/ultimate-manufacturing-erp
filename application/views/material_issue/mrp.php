<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');

$summary    = isset($summary)    ? $summary    : array('total_jo'=>0,'total_materials'=>0,'shortage_count'=>0,'ok_count'=>0,'total_gross_req'=>0,'total_shortage'=>0);
$mrp_items  = isset($mrp_items)  ? $mrp_items  : array();
$jo_list    = isset($jo_list)    ? $jo_list    : array();
?>

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
#mrpTable {
    width: 100% !important;
    min-width: 1450px !important;
    max-width: none !important;
    border-collapse: collapse !important;
}

#mrpTable th, #mrpTable td {
    white-space: nowrap !important;
    vertical-align: middle !important;
    padding: 8px 12px !important;
}

/* ===================== MRP PAGE STYLES ===================== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.mrp-wrapper { font-family: 'Inter', sans-serif; }

/* ── Flow Diagram ── */
.mrp-flow-strip {
    display: flex;
    align-items: center;
    gap: 0;
    background: linear-gradient(135deg, #1a1f36 0%, #0d1b2a 100%);
    border-radius: 14px;
    padding: 18px 28px;
    margin-bottom: 24px;
    overflow-x: auto;
    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}
.flow-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 110px;
    text-align: center;
}
.flow-icon {
    width: 52px; height: 52px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    margin-bottom: 8px;
    position: relative;
}
.flow-icon.so   { background: linear-gradient(135deg,#667eea,#764ba2); color:#fff; }
.flow-icon.bom  { background: linear-gradient(135deg,#f093fb,#f5576c); color:#fff; }
.flow-icon.inv  { background: linear-gradient(135deg,#4facfe,#00f2fe); color:#fff; }
.flow-icon.calc { background: linear-gradient(135deg,#43e97b,#38f9d7); color:#fff; }
.flow-icon.pr   { background: linear-gradient(135deg,#fa709a,#fee140); color:#fff; }
.flow-icon.alloc{ background: linear-gradient(135deg,#a8edea,#fed6e3); color:#1a1f36; }
.flow-label { color: #c9d1d9; font-size: 11px; font-weight: 600; line-height: 1.3; }
.flow-arrow {
    color: #4facfe;
    font-size: 22px;
    margin: 0 8px;
    padding-bottom: 20px;
    flex-shrink: 0;
}
.flow-fork {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin: 0 10px;
    padding-bottom: 20px;
}
.flow-fork-item {
    background: rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.flow-fork-item.shortage { color: #ff6b6b; border: 1px solid rgba(255,107,107,0.3); }
.flow-fork-item.allocate { color: #6bcb77; border: 1px solid rgba(107,203,119,0.3); }

/* ── Summary Cards ── */
.mrp-stat-cards { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
.mrp-stat-card {
    flex: 1; min-width: 160px;
    border-radius: 12px;
    padding: 18px 20px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}
.mrp-stat-card.blue   { background: linear-gradient(135deg,#4facfe,#00f2fe); }
.mrp-stat-card.purple { background: linear-gradient(135deg,#667eea,#764ba2); }
.mrp-stat-card.red    { background: linear-gradient(135deg,#fa709a,#f5576c); }
.mrp-stat-card.green  { background: linear-gradient(135deg,#43e97b,#38f9d7); color:#1a1f36; }
.mrp-stat-card.orange { background: linear-gradient(135deg,#f7971e,#ffd200); color:#1a1f36; }
.mrp-stat-card.dark   { background: linear-gradient(135deg,#2c3e50,#3498db); }
.stat-icon { font-size: 28px; opacity: 0.85; }
.stat-val  { font-size: 26px; font-weight: 700; line-height: 1; }
.stat-lbl  { font-size: 11px; opacity: 0.85; font-weight: 500; margin-top: 2px; }

/* ── Main Table ── */
.mrp-table-box {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    overflow: hidden;
    margin-bottom: 24px;
}
.mrp-table-header {
    background: linear-gradient(90deg, #1a1f36, #2c3e50);
    color: #fff;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.mrp-table-header h3 { margin: 0; font-size: 16px; font-weight: 600; }
.filter-btns { display: flex; gap: 8px; flex-wrap: wrap; }
.filter-btn {
    padding: 5px 14px;
    border-radius: 20px;
    border: 1.5px solid rgba(255,255,255,0.25);
    color: rgba(255,255,255,0.7);
    background: transparent;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s;
}
.filter-btn.active, .filter-btn:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border-color: rgba(255,255,255,0.5);
}
.filter-btn.red.active   { background:#f5576c; border-color:#f5576c; }
.filter-btn.green.active { background:#43e97b; border-color:#43e97b; color:#1a1f36; }

#mrpTable thead tr { background: #f0f4f8; }
#mrpTable thead th {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    padding: 10px 12px;
    white-space: nowrap;
    border-bottom: 2px solid #e2e8f0;
}
#mrpTable tbody td { vertical-align: middle; font-size: 13px; padding: 10px 12px; }
.mrp-row { cursor: pointer; transition: background 0.15s; }
.mrp-row:hover td { background: #f8fafd !important; }
.mrp-row.shortage-row td { background: #fff8f8; }
.mrp-row.ok-row       td { background: #f8fff8; }

/* Status badges */
.badge-shortage { background: #fee2e2; color: #dc2626; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-ok       { background: #dcfce7; color: #16a34a; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-none     { background: #f1f5f9; color: #94a3b8; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }

/* Expand row */
.child-panel {
    background: #f0f4f8;
    padding: 12px 24px 16px;
    border-left: 4px solid #4facfe;
}
.child-panel h5 { font-size: 13px; font-weight: 600; color: #1a1f36; margin: 0 0 10px; }
.child-panel table td { font-size: 12px; background: #fff; }

/* Action buttons */
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
    background: linear-gradient(135deg,#43e97b,#38f9d7);
    color: #1a1f36;
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
.btn-allocate:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67,233,123,0.4); }
.btn-row-pr {
    background:#fff0f3; color:#dc2626;
    border:1px solid #fca5a5;
    padding:2px 10px; border-radius:6px;
    font-size:11px; font-weight:600;
    cursor:pointer; transition:all .15s;
}
.btn-row-pr:hover { background:#dc2626; color:#fff; }
.btn-row-pr:disabled { background:#6c757d; color:#fff; border-color:#6c757d; opacity:0.65; cursor:not-allowed; }
.btn-row-allocate {
    background:#dcfce7; color:#16a34a;
    border:1px solid #86efac;
    padding:2px 10px; border-radius:6px;
    font-size:11px; font-weight:600;
    cursor:pointer; transition:all .15s;
}
.btn-row-allocate:hover { background:#16a34a; color:#fff; }

/* Checkbox */
.mrp-check { width:16px; height:16px; cursor:pointer; accent-color:#f5576c; }

/* Alert toast */
.mrp-toast {
    position:fixed; top:80px; right:20px; z-index:9999;
    padding:14px 20px;
    border-radius:10px;
    font-size:13px;
    font-weight:600;
    box-shadow:0 4px 20px rgba(0,0,0,0.15);
    display:none;
    max-width:360px;
    animation: slideIn 0.3s ease;
}
.mrp-toast.success { background:#dcfce7; color:#16a34a; border-left:4px solid #16a34a; }
.mrp-toast.error   { background:#fee2e2; color:#dc2626; border-left:4px solid #dc2626; }
.mrp-toast.info    { background:#dbeafe; color:#1d4ed8; border-left:4px solid #1d4ed8; }
@keyframes slideIn { from { transform:translateX(40px); opacity:0; } to { transform:translateX(0); opacity:1; } }

/* Formula highlight */
.formula-box {
    background: linear-gradient(135deg,#f0f4ff,#e8f5e9);
    border-radius: 10px;
    padding: 14px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 13px;
    font-weight: 600;
    color: #2c3e50;
    border: 1px solid #c7d2fe;
}
.formula-box .formula-seg { display:flex; align-items:center; gap:6px; }
.formula-box .fop { color:#6366f1; font-size:18px; font-weight:700; }
.formula-box .fresult { background:#6366f1; color:#fff; padding:2px 10px; border-radius:6px; }
.formula-box .fi   { padding:2px 10px; background:#fff; border:1px solid #c7d2fe; border-radius:6px; color:#1e40af; }

/* Selection bar */
.selection-bar {
    background: linear-gradient(135deg,#1a1f36,#2c3e50);
    color:#fff;
    border-radius:10px;
    padding:12px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:16px;
    gap:12px;
    flex-wrap:wrap;
}
.selection-bar span { font-size:13px; font-weight:500; }

.btn-row-deallocate {
    background:#fff7ed; color:#ea580c;
    border:1px solid #ffedd5;
    padding:2px 10px; border-radius:6px;
    font-size:11px; font-weight:600;
    cursor:pointer; transition:all .15s;
}
.btn-row-deallocate:hover { background:#ea580c; color:#fff; }

@media print {
    .main-header,.main-sidebar,.content-header ol,.mrp-flow-strip,.selection-bar,
    .filter-btns, #mrpTable_filter, #mrpTable_length,
    #mrpTable_paginate, #mrpTable_info, .btn-generate-pr, .btn-allocate,
    .btn-row-pr, .btn-row-allocate, .btn-row-deallocate, .mrp-check { display:none !important; }
    .content-wrapper { margin-left:0 !important; }
    .mrp-stat-card { box-shadow:none !important; }
}
</style>

    <div class="content-wrapper">
        <section class="content-header">
            <h1><i class="fa fa-cogs"></i> Material Requirements Planning (MRP) — Job Order Run</h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>MRP</li>
                <li class="active">Job Order MRP</li>
            </ol>
        </section>

        <section class="content">

            <!-- ══════════ MRP FLOW DIAGRAM ══════════ -->
            <div class="mrp-flow-strip">
                <div class="flow-step">
                    <div class="flow-icon so"><i class="fa fa-shopping-cart"></i></div>
                    <div class="flow-label">Job Order<br>(SO / Customer)</div>
                </div>
                <div class="flow-arrow"><i class="fa fa-arrow-right"></i></div>
                <div class="flow-step">
                    <div class="flow-icon bom"><i class="fa fa-sitemap"></i></div>
                    <div class="flow-label">BOM Explosion<br>(Components)</div>
                </div>
                <div class="flow-arrow"><i class="fa fa-arrow-right"></i></div>
                <div class="flow-step">
                    <div class="flow-icon inv"><i class="fa fa-cubes"></i></div>
                    <div class="flow-label">Inventory<br>(Current Stock)</div>
                </div>
                <div class="flow-arrow"><i class="fa fa-arrow-right"></i></div>
                <div class="flow-step">
                    <div class="flow-icon calc"><i class="fa fa-calculator"></i></div>
                    <div class="flow-label">Net Requirement<br>Calculation</div>
                </div>
                <div class="flow-arrow"><i class="fa fa-arrow-right"></i></div>
                <div class="flow-fork">
                    <div class="flow-fork-item shortage">
                        <i class="fa fa-exclamation-triangle"></i> Shortage → Generate PR
                    </div>
                    <div class="flow-fork-item allocate">
                        <i class="fa fa-check-circle"></i> Stock OK → Allocate to JO
                    </div>
                </div>
            </div>

            <!-- ══════════ FORMULA BAR ══════════ -->
            <div class="formula-box">
                <div class="formula-seg"><span class="fi">Gross Req</span><span style="color:#888">=</span><span class="fi">JO Qty</span><span class="fop">×</span><span class="fi">BOM Qty/Unit</span></div>
                <div class="formula-seg" style="margin-left:12px;"><span class="fi">Net Req</span><span style="color:#888">=</span><span class="fi">Gross Req</span><span class="fop">−</span><span class="fi">Available Stock</span></div>
                <div class="formula-seg" style="margin-left:12px;"><span>Shortage → </span><span class="fresult">Generate PR</span></div>
                <div class="formula-seg"><span>Stock OK → </span><span style="background:#16a34a;color:#fff;padding:2px 10px;border-radius:6px;">Allocate</span></div>
            </div>

            <!-- ══════════ SUMMARY CARDS ══════════ -->
            <div class="mrp-stat-cards">
                <div class="mrp-stat-card dark">
                    <div class="stat-icon"><i class="fa fa-briefcase"></i></div>
                    <div>
                        <div class="stat-val"><?php echo $summary['total_jo']; ?></div>
                        <div class="stat-lbl">Active Job Orders</div>
                    </div>
                </div>
                <div class="mrp-stat-card blue">
                    <div class="stat-icon"><i class="fa fa-list-ol"></i></div>
                    <div>
                        <div class="stat-val"><?php echo $summary['total_materials']; ?></div>
                        <div class="stat-lbl">Unique Raw Materials</div>
                    </div>
                </div>
                <div class="mrp-stat-card orange">
                    <div class="stat-icon"><i class="fa fa-cubes"></i></div>
                    <div>
                        <div class="stat-val"><?php echo number_format($summary['total_gross_req'], 0); ?></div>
                        <div class="stat-lbl">Total Gross Requirement</div>
                    </div>
                </div>
                <div class="mrp-stat-card red">
                    <div class="stat-icon"><i class="fa fa-exclamation-triangle"></i></div>
                    <div>
                        <div class="stat-val"><?php echo $summary['shortage_count']; ?></div>
                        <div class="stat-lbl">Items with Shortage</div>
                    </div>
                </div>
                <div class="mrp-stat-card green">
                    <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                    <div>
                        <div class="stat-val"><?php echo $summary['ok_count']; ?></div>
                        <div class="stat-lbl">Items — Stock OK</div>
                    </div>
                </div>
                <div class="mrp-stat-card purple">
                    <div class="stat-icon"><i class="fa fa-minus-circle"></i></div>
                    <div>
                        <div class="stat-val"><?php echo number_format($summary['total_shortage'], 0); ?></div>
                        <div class="stat-lbl">Total Shortage Qty</div>
                    </div>
                </div>
            </div>

            <!-- ══════════ FLASH MESSAGES ══════════ -->
            <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
                <div class="alert alert-success alert-dismissible fade in">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('ERRORMSG')): ?>
                <div class="alert alert-danger alert-dismissible fade in">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('ERRORMSG'); ?>
                </div>
            <?php endif; ?>

            <!-- ══════════ SELECTION ACTION BAR ══════════ -->
            <div class="selection-bar" id="selectionBar">
                <span id="selectionCount"><i class="fa fa-info-circle"></i> Select shortage items below to generate a Purchase Requisition</span>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button class="btn-generate-pr" id="btnGeneratePR" disabled onclick="generatePR()">
                        <i class="fa fa-file-text"></i> Generate PR for Selected
                    </button>
                    <button class="btn-allocate" onclick="selectAllShortages()">
                        <i class="fa fa-check-square-o"></i> Select All Shortages
                    </button>
                    <button style="background:rgba(255,255,255,0.1);color:#fff;border:1px solid rgba(255,255,255,0.2);padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px;" onclick="clearSelection()">
                        <i class="fa fa-times"></i> Clear
                    </button>
                    <button style="background:rgba(255,255,255,0.1);color:#fff;border:1px solid rgba(255,255,255,0.2);padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px;" onclick="exportMRP()">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                    <button style="background:rgba(255,255,255,0.1);color:#fff;border:1px solid rgba(255,255,255,0.2);padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px;" onclick="window.print()">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
            </div>

            <!-- ══════════ MAIN MRP TABLE ══════════ -->
            <div class="mrp-table-box">
                <div class="mrp-table-header">
                    <h3><i class="fa fa-table"></i> MRP Raw Material Requirements
                        <small style="font-weight:400;opacity:0.7"> — Click row to expand Job Order breakdown</small>
                    </h3>
                    <div class="filter-btns">
                        <button class="filter-btn active" id="filter-all"     onclick="applyFilter('all',this)">All</button>
                        <button class="filter-btn red"    id="filter-shortage" onclick="applyFilter('shortage',this)">🔴 Shortage</button>
                        <button class="filter-btn green"  id="filter-ok"       onclick="applyFilter('ok',this)">🟢 Stock OK</button>
                    </div>
                </div>

                <div style="padding:16px 20px;">
                    <div class="table-responsive">
                    <table id="mrpTable" class="table table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:3%"><input type="checkbox" id="chkAll" class="mrp-check" onchange="toggleAllShortage(this)"></th>
                                <th style="width:3%">#</th>
                                <th style="width:9%">Item Code</th>
                                <th style="width:20%">Raw Material</th>
                                <th style="width:4%">Unit</th>
                                <th class="text-right" style="width:9%">Gross Req<br><small style="font-weight:normal;color:#94a3b8">(JO×BOM)</small></th>
                                <th class="text-right" style="width:9%">Stock<br><small style="font-weight:normal;color:#94a3b8">(Available)</small></th>
                                <th class="text-right" style="width:10%">Net Req<br><small style="font-weight:normal;color:#94a3b8">(Shortage)</small></th>
                                <th class="text-center" style="width:8%">Status</th>
                                <th class="text-center" style="width:6%">JOs</th>
                                <th class="text-center" style="width:6%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($mrp_items)): ?>
                            <?php $sr = 1; foreach ($mrp_items as $idx => $item): ?>
                            <?php
                                $status      = $item['status'];
                                $gross       = floatval($item['gross_requirement']);
                                $stock       = floatval($item['current_stock']);
                                $net         = floatval($item['net_requirement']);
                                $shortage    = floatval($item['shortage']);
                                $is_shortage = ($status === 'shortage');
                                $row_class   = $is_shortage ? 'shortage-row' : ($status === 'ok' ? 'ok-row' : '');
                                $jo_json     = htmlspecialchars(json_encode($item['jo_details']), ENT_QUOTES, 'UTF-8');
                                $has_bom     = $item['has_bom'] ? 'BOM' : 'Direct';
                            ?>
                            <tr class="mrp-row <?php echo $row_class; ?>"
                                data-status="<?php echo $status; ?>"
                                data-shortage="<?php echo $shortage; ?>"
                                data-code="<?php echo htmlspecialchars($item['raw_material_code']); ?>"
                                data-name="<?php echo htmlspecialchars($item['raw_material_name']); ?>"
                                data-unit="<?php echo htmlspecialchars($item['unit']); ?>"
                                data-inventory-id="<?php echo intval($item['inventory_id']); ?>"
                                data-gross="<?php echo $gross; ?>"
                                data-stock="<?php echo $stock; ?>"
                                data-jo='<?php echo $jo_json; ?>'>

                                <!-- Checkbox (only for shortage) -->
                                <td class="text-center" onclick="event.stopPropagation()">
                                    <?php if ($is_shortage): ?>
                                    <input type="checkbox" class="mrp-check shortage-check"
                                           value="<?php echo htmlspecialchars($item['raw_material_code']); ?>"
                                           onchange="updateSelection()">
                                    <?php endif; ?>
                                </td>
                                <td><span style="color:#94a3b8;font-size:12px;"><?php echo $sr++; ?></span></td>
                                <td>
                                    <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:11px;">
                                        <?php echo htmlspecialchars($item['raw_material_code']); ?>
                                    </code>
                                    <br><span style="font-size:10px;color:#94a3b8;"><?php echo $has_bom; ?></span>
                                </td>
                                <td style="font-weight:500;"><?php echo htmlspecialchars($item['raw_material_name']); ?></td>
                                <td style="color:#64748b;"><?php echo htmlspecialchars($item['unit']); ?></td>
                                <td class="text-right"><strong><?php echo number_format($gross, 2); ?></strong></td>
                                <td class="text-right" style="color:<?php echo $stock > 0 ? '#16a34a' : '#94a3b8'; ?>">
                                     <?php echo number_format($stock, 2); ?>
                                     <?php if (isset($item['allocated_qty']) && $item['allocated_qty'] > 0): ?>
                                         <br><span class="label label-info" style="font-size:9px; padding: 1px 4px; display:inline-block; margin-top:2px;">Alloc: <?php echo number_format($item['allocated_qty'], 2); ?></span>
                                     <?php endif; ?>
                                 </td>
                                <td class="text-right">
                                    <?php if ($shortage > 0): ?>
                                        <strong style="color:#dc2626;">
                                            <i class="fa fa-exclamation-triangle"></i>
                                            <?php echo number_format($shortage, 2); ?>
                                        </strong>
                                    <?php else: ?>
                                        <span style="color:#16a34a;"><i class="fa fa-check"></i> 0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($status === 'shortage'): ?>
                                        <span class="badge-shortage"><i class="fa fa-exclamation-triangle"></i> Shortage</span>
                                    <?php elseif ($status === 'ok'): ?>
                                        <span class="badge-ok"><i class="fa fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="badge-none">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-blue"><?php echo count($item['jo_details']); ?></span>
                                </td>
                                <td class="text-center" onclick="event.stopPropagation()">
                                    <?php if ($is_shortage): ?>
                                        <?php if (!empty($item['has_pr'])): ?>
                                            <button class="btn-row-pr" disabled style="opacity: 0.65; cursor: not-allowed; background: #6c757d; border-color: #6c757d; color: #fff;" title="PR already generated (<?php echo htmlspecialchars($item['pr_info']['pr_no'] ?? ''); ?>)">
                                                <i class="fa fa-check-circle"></i> PR Generated
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-row-pr" onclick="generateSinglePR(
                                                '<?php echo htmlspecialchars($item['raw_material_code']); ?>',
                                                '<?php echo htmlspecialchars(addslashes($item['raw_material_name'])); ?>',
                                                '<?php echo $shortage; ?>',
                                                '<?php echo htmlspecialchars($item['unit']); ?>',
                                                '<?php echo $gross; ?>',
                                                '<?php echo $stock; ?>'
                                            )">
                                                <i class="fa fa-file-text"></i> PR
                                            </button>
                                        <?php endif; ?>
                                    <?php elseif ($status === 'ok'): ?>
                                    <button class="btn-row-allocate" onclick="allocateMaterial(this)">
                                        <i class="fa fa-check"></i> Allocate
                                    </button>
                                    <?php else: ?>
                                    <span style="color:#3b82f6;font-size:11px;font-weight:600;display:block;margin-bottom:4px;"><i class="fa fa-check-circle"></i> Allocated</span>
                                    <button class="btn-row-deallocate" onclick="deallocateMaterial(this)">
                                        <i class="fa fa-undo"></i> Return
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- ══════════ JO LIST SUMMARY ══════════ -->
            <?php if (!empty($jo_list)): ?>
            <div class="mrp-table-box" style="margin-bottom:40px;">
                <div class="mrp-table-header">
                    <h3><i class="fa fa-briefcase"></i> Active Job Orders in This MRP Run</h3>
                </div>
                <div style="padding:16px 20px;">
                    <div class="table-responsive">
                    <table class="table table-condensed table-bordered" style="font-size:12px;">
                        <thead>
                            <tr style="background:#f0f4f8;">
                                <th>#</th>
                                <th>JO Number</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th class="text-right">JO Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $s=1; foreach($jo_list as $jo): ?>
                            <tr>
                                <td><?php echo $s++; ?></td>
                                <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;"><?php echo htmlspecialchars($jo['jo_number']); ?></code></td>
                                <td><?php echo htmlspecialchars($jo['jo_date']); ?></td>
                                <td><?php echo htmlspecialchars($jo['company_name']); ?> <span style="color:#94a3b8;"><?php echo htmlspecialchars($jo['customer_code']); ?></span></td>
                                <td><?php echo htmlspecialchars($jo['product_name']); ?></td>
                                <td class="text-right"><strong><?php echo number_format(floatval($jo['jo_qty']), 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </section>
    </div>
</div>

<!-- ══════════ TOAST ══════════ -->
<div class="mrp-toast" id="mrpToast"></div>

<!-- ══════════ SCRIPTS ══════════ -->

<script>
var mrpTable;
var BASE_URL = '<?php echo base_url(); ?>';

// ── Build child row HTML ──────────────────────────────────────────────────
function buildChildRow(joData, code, name) {
    if (!joData || joData.length === 0) {
        return '<div class="child-panel"><em class="text-muted">No Job Order details found.</em></div>';
    }
    var html = '<div class="child-panel">';
    html += '<h5><i class="fa fa-list-ul text-primary"></i> Job Order Breakdown — <strong>' + code + '</strong>: ' + name + '</h5>';
    html += '<table class="table table-condensed table-bordered">';
    html += '<thead><tr style="background:#dce8f5;">';
    html += '<th>JO Number</th><th>Date</th><th>Customer</th><th>Product</th>';
    html += '<th class="text-right">JO Qty</th>';
    html += '<th class="text-right">BOM Qty/Unit</th>';
    html += '<th class="text-right">Gross Req<br><small>(JO×BOM)</small></th>';
    html += '</tr></thead><tbody>';

    for (var i = 0; i < joData.length; i++) {
        var jo   = joData[i];
        var joQty   = parseFloat(jo.jo_qty)           || 0;
        var bomQty  = parseFloat(jo.bom_qty_per_unit) || 1;
        var grossR  = parseFloat(jo.gross_req)         || (joQty * bomQty);
        html += '<tr>';
        html += '<td><strong>' + jo.jo_number + '</strong></td>';
        html += '<td>' + (jo.jo_date || '—') + '</td>';
        html += '<td>' + (jo.company_name || '—') + ' <small style="color:#94a3b8;">' + (jo.customer_code || '') + '</small></td>';
        html += '<td>' + (jo.product_name || jo.product_code || '—') + '</td>';
        html += '<td class="text-right">' + joQty.toFixed(2) + '</td>';
        html += '<td class="text-right">' + bomQty.toFixed(3) + '</td>';
        html += '<td class="text-right"><strong>' + grossR.toFixed(2) + '</strong></td>';
        html += '</tr>';
    }

    html += '</tbody></table></div>';
    return html;
}

// ── DataTable init ────────────────────────────────────────────────────────
$(document).ready(function () {

    mrpTable = $('#mrpTable').DataTable({
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>><"mrp-table-scroll-wrapper"t><"row"<"col-sm-5"i><"col-sm-7"p>>',
        paging:    true,
        searching: true,
        ordering:  true,
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: [0, 10] }
        ],
        language: {
            search:      'Search MRP:',
            lengthMenu:  'Show _MENU_ items',
            info:        'Showing _START_ to _END_ of _TOTAL_ items',
            zeroRecords: 'No items after filtering',
            emptyTable:  '<div class="text-center" style="padding:30px;color:#94a3b8;"><div style="font-size:36px;margin-bottom:8px;">📦</div><strong>No active Job Orders found.</strong><br><small>Add Job Orders and link BOM to see MRP calculations.</small></div>'
        }
    });

    // Expand/collapse child row on row click
    $('#mrpTable tbody').on('click', 'tr.mrp-row', function () {
        var tr   = $(this);
        var row  = mrpTable.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(buildChildRow(
                tr.data('jo'),
                tr.data('code'),
                tr.data('name')
            )).show();
            tr.addClass('shown');
        }
    });
});

// ── Filter ────────────────────────────────────────────────────────────────
function applyFilter(status, btn) {
    $('.filter-btn').removeClass('active');
    $(btn).addClass('active');
    $.fn.dataTable.ext.search = [];
    if (status !== 'all') {
        $.fn.dataTable.ext.search.push(function (s, d, idx) {
            var node = mrpTable.row(idx).node();
            return $(node).data('status') === status;
        });
    }
    mrpTable.draw();
}

// ── Selection ─────────────────────────────────────────────────────────────
function updateSelection() {
    var checked = $('.shortage-check:checked').length;
    var total   = $('.shortage-check').length;
    var btn = document.getElementById('btnGeneratePR');
    btn.disabled = (checked === 0);

// Selection actions across all DataTables pages
function updateSelection() {
    var nodes = mrpTable ? mrpTable.rows().nodes() : [];
    var checked = $(nodes).find('.shortage-check:checked').length;
    var btn = document.getElementById('btnGeneratePR');
    if (btn) btn.disabled = (checked === 0);

    if (checked === 0) {
        document.getElementById('selectionCount').innerHTML =
            '<i class="fa fa-info-circle"></i> Select shortage items to generate a PR';
    } else {
        document.getElementById('selectionCount').innerHTML =
            '<i class="fa fa-check-circle" style="color:#43e97b"></i> <strong>' + checked + '</strong> item(s) selected for PR (across all pages)';
    }
}

function selectAllShortages() {
    if (!mrpTable) return;
    var nodes = mrpTable.rows({ search: 'applied' }).nodes();
    $(nodes).find('.shortage-check').not(':disabled').prop('checked', true);
    updateSelection();
}

function clearSelection() {
    if (mrpTable) {
        var nodes = mrpTable.rows().nodes();
        $(nodes).find('.shortage-check').prop('checked', false);
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

// ── Generate PR (bulk across all pages) ───────────────────────────────────
function generatePR() {
    var selected = [];
    if (!mrpTable) return;

    var nodes = mrpTable.rows({ search: 'applied' }).nodes();
    $(nodes).find('.shortage-check:checked').each(function () {
        var row = $(this).closest('tr');
        selected.push({
            code:     row.data('code'),
            name:     row.data('name'),
            unit:     row.data('unit'),
            shortage: parseFloat(row.data('shortage')),
            gross:    parseFloat(row.data('gross')),
            stock:    parseFloat(row.data('stock'))
        });
    });

    if (selected.length === 0) {
        showToast('Please select at least one shortage item.', 'error');
        return;
    }

    if (!confirm('Generate Purchase Requisition for ' + selected.length + ' item(s)?\n\nThis will create a new PR in the system for all selected items across all pages.')) {
        return;
    }

    var so_no = '';
    var project_code = '';
    if (firstRow) {
        var joData = firstRow.data('jo');
        if (joData && joData.length > 0) {
            so_no = joData[0].salesorder_number || '';
            project_code = joData[0].project_code || '';
        }
    }

    sendPRRequest(selected, so_no, project_code);
}

function generateSinglePR(code, name, shortage, unit, gross, stock) {
    if (!confirm('Generate PR for: ' + name + '\nShortage Qty: ' + parseFloat(shortage).toFixed(2) + ' ' + unit)) return;
    
    var row = $('tr.mrp-row').filter(function() {
        return $(this).data('code') == code;
    }).first();
    
    var so_no = '';
    var project_code = '';
    if (row.length > 0) {
        var joData = row.data('jo');
        if (joData && joData.length > 0) {
            so_no = joData[0].salesorder_number || '';
            project_code = joData[0].project_code || '';
        }
    }

    sendPRRequest([{ code: code, name: name, unit: unit, shortage: parseFloat(shortage), gross: parseFloat(gross), stock: parseFloat(stock) }], so_no, project_code);
}

function sendPRRequest(items, so_no, project_code) {
    var btn = document.getElementById('btnGeneratePR');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating...';

    $.ajax({
        url:  BASE_URL + 'MaterialIssueController/ajax_generate_pr_from_mrp',
        type: 'POST',
        data: { 
            items: JSON.stringify(items),
            so_no: so_no || '',
            project_code: project_code || ''
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
            }
        },
        error: function () {
            showToast('❌ Server error. Please try again.', 'error');
        },
        complete: function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-file-text"></i> Generate PR for Selected';
        }
    });
}

// ── Toast ─────────────────────────────────────────────────────────────────
// Allocate Stock OK material to all JOs in the row by creating Material Issue Slips.
function allocateMaterial(button) {
    var btn = $(button);
    var row = btn.closest('tr.mrp-row');
    var inventoryId = parseInt(row.attr('data-inventory-id'), 10) || 0;
    var joData = row.data('jo') || [];
    var itemCode = row.data('code');
    var itemName = row.data('name');
    var unit = row.data('unit');
    var gross = parseFloat(row.data('gross')) || 0;
    var stock = parseFloat(row.data('stock')) || 0;

    if (!inventoryId) {
        showToast('Inventory item is missing, so stock cannot be allocated.', 'error');
        return;
    }

    if (!joData.length) {
        showToast('No Job Order breakdown found for allocation.', 'error');
        return;
    }

    if (stock < gross) {
        showToast('Available stock is lower than the gross requirement. Generate PR first.', 'error');
        return;
    }

    if (!confirm('Reserve ' + gross.toFixed(2) + ' ' + unit + ' of ' + itemName + ' for ' + joData.length + ' Job Order(s)?\n\nThis will allocate/reserve stock without reducing physical inventory.')) {
        return;
    }

    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Allocating...');

    var index = 0;
    var allocated = 0;
    var failed = [];

    function allocateNext() {
        if (index >= joData.length) {
            if (failed.length) {
                showToast('Allocated ' + allocated + ' JO(s). Failed: ' + failed.join(', '), 'error');
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> Allocate');
            } else {
                showToast('Stock allocated to ' + allocated + ' JO(s). Refreshing MRP...', 'success');
                setTimeout(function () { window.location.reload(); }, 900);
            }
            return;
        }

        var jo = joData[index++];
        var qty = parseFloat(jo.gross_req) || 0;

        if (!jo.jo_number || qty <= 0) {
            failed.push(jo.jo_number || ('Row ' + index));
            allocateNext();
            return;
        }

        $.ajax({
            url: BASE_URL + 'MaterialIssueController/ajax_allocate_stock',
            type: 'POST',
            dataType: 'json',
            data: {
                inventory_id: inventoryId,
                jo_number: jo.jo_number,
                quantity: qty,
                item_code: itemCode,
                item_name: itemName,
                unit: unit
            },
            success: function (res) {
                if (res.success) {
                    allocated++;
                } else {
                    failed.push(jo.jo_number + ' (' + (res.message || 'failed') + ')');
                }
            },
            error: function () {
                failed.push(jo.jo_number + ' (server error)');
            },
            complete: allocateNext
        });
    }

    allocateNext();
}

// Deallocate allocated component from all JOs in the row
function deallocateMaterial(button) {
    var btn = $(button);
    var row = btn.closest('tr.mrp-row');
    var inventoryId = parseInt(row.attr('data-inventory-id'), 10) || 0;
    var joData = row.data('jo') || [];
    var itemCode = row.data('code');
    var itemName = row.data('name');

    if (!inventoryId) {
        showToast('Inventory item is missing, so stock cannot be deallocated.', 'error');
        return;
    }

    if (!joData.length) {
        showToast('No Job Order breakdown found for deallocation.', 'error');
        return;
    }

    if (!confirm('Return allocated stock for ' + itemName + ' from ' + joData.length + ' Job Order(s)?\n\nThis will cancel the reservation and return the stock to available inventory.')) {
        return;
    }

    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Returning...');

    var index = 0;
    var deallocated = 0;
    var failed = [];

    function deallocateNext() {
        if (index >= joData.length) {
            if (failed.length) {
                showToast('Deallocated ' + deallocated + ' JO(s). Failed: ' + failed.join(', '), 'error');
                btn.prop('disabled', false).html('<i class="fa fa-undo"></i> Return');
            } else {
                showToast('Allocated stock returned to inventory for ' + deallocated + ' JO(s). Refreshing MRP...', 'success');
                setTimeout(function () { window.location.reload(); }, 900);
            }
            return;
        }

        var jo = joData[index++];

        if (!jo.jo_number) {
            deallocateNext();
            return;
        }

        $.ajax({
            url: BASE_URL + 'MaterialIssueController/ajax_deallocate_stock',
            type: 'POST',
            dataType: 'json',
            data: {
                inventory_id: inventoryId,
                item_code: itemCode,
                jo_number: jo.jo_number
            },
            success: function (res) {
                if (res.success) {
                    deallocated++;
                } else {
                    failed.push(jo.jo_number + ' (' + (res.message || 'failed') + ')');
                }
            },
            error: function () {
                failed.push(jo.jo_number + ' (server error)');
            },
            complete: deallocateNext
        });
    }

    deallocateNext();
}


function showToast(msg, type) {
    var t = document.getElementById('mrpToast');
    t.textContent = msg;
    t.className = 'mrp-toast ' + type;
    t.style.display = 'block';
    setTimeout(function () { t.style.display = 'none'; }, 4000);
}

// ── CSV Export ────────────────────────────────────────────────────────────
function exportMRP() {
    var rows = [['#','Item Code','Raw Material','Unit','Gross Req','Stock','Net Req / Shortage','Status','JO Count'].join(',')];

    mrpTable.rows({ search: 'applied' }).every(function () {
        var cells = [];
        $(this.node()).find('td').each(function (i) {
            if (i === 0) return; // skip checkbox
            cells.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
        });
        rows.push(cells.join(','));
    });

    var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href = url;
    a.download = 'MRP_Report_<?php echo date('Ymd_His'); ?>.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Initial selection update
updateSelection();
</script>
</body>
</html>
