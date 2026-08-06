<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <h1>Stock Management</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>">Stock Summary</a></li>
                    <li class="active">Stock Verification</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-check-square-o"></i> Stock Verification</h3>
                                <div class="box-tools pull-right">
                                    <a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>" class="btn btn-primary btn-sm">
                                        <i class="fa fa-arrow-left"></i> Back to Stock Summary
                                    </a>
                                </div>
                            </div>

                            <div class="box-body">
                                <!-- Flash messages -->
                                <?php
                                $success_msg = $this->session->flashdata('SUCCESSMSG');
                                if ($success_msg): ?>
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <i class="fa fa-check"></i> <?= $success_msg ?>
                                    </div>
                                <?php endif; ?>
                                <?php
                                $error_msg = $this->session->flashdata('ERRORMSG');
                                if ($error_msg): ?>
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <i class="fa fa-times"></i> <?= $error_msg ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Tabs -->
                                <ul class="nav nav-tabs" id="verificationTabs">
                                    <li class="active">
                                        <a href="#tab-new" data-toggle="tab"><i class="fa fa-plus-circle"></i> New Verification</a>
                                    </li>
                                    <li>
                                        <a href="#tab-history" data-toggle="tab"><i class="fa fa-history"></i> History
                                            <?php if (!empty($verification_history)): ?>
                                                <span class="badge bg-blue"><?= count($verification_history) ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content" style="padding-top:18px;">

                                    <!-- ===== TAB 1: NEW VERIFICATION ===== -->
                                    <div class="tab-pane active" id="tab-new">

                                        <?php if (validation_errors()): ?>
                                            <div class="alert alert-danger alert-dismissible">
                                                <button class="close" data-dismiss="alert">&times;</button>
                                                <strong>Error!</strong> <?= validation_errors() ?>
                                            </div>
                                        <?php endif; ?>

                                        <form method="post" action="<?php echo base_url('MaterialIssueController/stock_verification') ?>" id="verificationForm">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Verification Date *</label>
                                                        <input type="date" name="verification_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Remarks</label>
                                                        <textarea name="remarks" class="form-control" rows="1"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label><br>
                                                        <!-- Export current data as CSV -->
                                                        <button type="button" class="btn btn-success btn-sm" id="exportCurrentBtn" onclick="exportCurrentTable()">
                                                            <i class="fa fa-file-excel-o"></i> Export CSV
                                                        </button>
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-save"></i> Save Verification
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Search bar for item table -->
                                            <div class="row" style="margin-bottom:8px;">
                                                <div class="col-md-4 pull-right">
                                                    <input type="text" id="searchItems" class="form-control input-sm" placeholder="Search Stock Verification...">
                                                </div>
                                                <div class="col-md-3">
                                                    <select id="entriesPerPage" class="form-control input-sm" style="width:auto;display:inline-block;">
                                                        <option value="25">25 entries per page</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option value="999999">All</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="verificationTable">
                                                    <thead class="thead-dark" style="background:#337ab7;color:#fff;">
                                                        <tr>
                                                            <th width="25%">ITEM</th>
                                                            <th width="15%">SYSTEM STOCK</th>
                                                            <th width="15%">PHYSICAL STOCK *</th>
                                                            <th width="15%">VARIANCE</th>
                                                            <th width="15%">UNIT PRICE</th>
                                                            <th width="15%">VARIANCE VALUE</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="verificationBody">
                                                        <?php if (!empty($inventory_items)): ?>
                                                            <?php $i = 0; ?>
                                                            <?php foreach ($inventory_items as $item): ?>
                                                                <?php if ($item['stock'] > 0): ?>
                                                                    <tr class="item-row">
                                                                        <td>
                                                                            <strong><?= $item['code'] ?></strong> - <?= $item['item_name'] ?>
                                                                            <input type="hidden" name="inventory_id[]" value="<?= $item['inventory_id'] ?>">
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="system_stock[]" class="form-control system-stock" value="<?= $item['stock'] ?>" readonly>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="physical_stock[]" class="form-control physical-stock" data-index="<?= $i ?>" value="<?= $item['stock'] ?>" step="0.01" min="0" required>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="variance[]" class="form-control variance" data-index="<?= $i ?>" readonly>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="unit_price[]" class="form-control unit-price" value="<?= $item['sell_price'] ?>" readonly>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="variance_value[]" class="form-control variance-value" data-index="<?= $i ?>" readonly>
                                                                        </td>
                                                                    </tr>
                                                                    <?php $i++; ?>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr><td colspan="6" class="text-center text-muted">No items with stock found</td></tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr style="font-weight:bold;background:#f5f5f5;">
                                                            <th colspan="3" class="text-right">Total Variance:</th>
                                                            <th id="totalVariance">0.00</th>
                                                            <th></th>
                                                            <th id="totalVarianceValue">0.00</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <div class="alert alert-info" style="margin-top:10px;">
                                                <strong>Note:</strong>
                                                <ul style="margin-bottom:0;">
                                                    <li>Positive variance = physical stock &gt; system stock (stock gain)</li>
                                                    <li>Negative variance = physical stock &lt; system stock (stock loss)</li>
                                                    <li>Variance Value = Variance × Unit Price</li>
                                                </ul>
                                            </div>

                                            <div class="form-group" style="margin-top:12px;">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-save"></i> Save Verification
                                                </button>
                                                <button type="button" class="btn btn-default btn-sm" onclick="exportCurrentTable()">
                                                    <i class="fa fa-file-excel-o"></i> Export CSV
                                                </button>
                                                <a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>" class="btn btn-default">Cancel</a>
                                            </div>
                                        </form>
                                    </div><!-- /tab-new -->

                                    <!-- ===== TAB 2: HISTORY ===== -->
                                    <div class="tab-pane" id="tab-history">

                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col-md-6">
                                                <input type="text" id="searchHistory" class="form-control input-sm" placeholder="Search history...">
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <a href="<?= base_url('MaterialIssueController/export_stock_verification') ?>" class="btn btn-success btn-sm">
                                                    <i class="fa fa-file-excel-o"></i> Export All History
                                                </a>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover" id="historyTable">
                                                <thead style="background:#337ab7;color:#fff;">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Verification No</th>
                                                        <th>Date</th>
                                                        <th>Total Items</th>
                                                        <th>Total Variance Value</th>
                                                        <th>Status</th>
                                                        <th>Remarks</th>
                                                        <th>Verified By</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($verification_history)): ?>
                                                        <?php $no = 1; foreach ($verification_history as $h): ?>
                                                            <tr class="history-row">
                                                                <td><?= $no++ ?></td>
                                                                <td><strong><?= htmlspecialchars($h['verification_no'] ?? '') ?></strong></td>
                                                                <td><?= date('d-m-Y', strtotime($h['verification_date'])) ?></td>
                                                                <td class="text-center"><?= $h['total_items'] ?? 0 ?></td>
                                                                <td class="text-right">
                                                                    <?php $tv = floatval($h['total_variance'] ?? 0); ?>
                                                                    <span style="color:<?= $tv < 0 ? '#d9534f' : ($tv > 0 ? '#5cb85c' : '#888') ?>;font-weight:bold;">
                                                                        <?= number_format($tv, 2) ?>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php
                                                                    $status = strtolower($h['status'] ?? 'draft');
                                                                    $badge = $status === 'completed' ? 'success' : ($status === 'draft' ? 'warning' : 'default');
                                                                    ?>
                                                                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span>
                                                                </td>
                                                                <td><?= htmlspecialchars($h['remarks'] ?? '-') ?></td>
                                                                <td><?= htmlspecialchars($h['verified_by_name'] ?? '-') ?></td>
                                                                <td>
                                                                    <button class="btn btn-xs btn-info view-items-btn"
                                                                        data-id="<?= $h['verification_id'] ?>"
                                                                        data-no="<?= htmlspecialchars($h['verification_no'] ?? '') ?>">
                                                                        <i class="fa fa-eye"></i> Items
                                                                    </button>
                                                                    <a href="<?= base_url('MaterialIssueController/export_stock_verification/' . $h['verification_id']) ?>"
                                                                        class="btn btn-xs btn-success" title="Export this verification">
                                                                        <i class="fa fa-download"></i> Export
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <!-- Expandable item detail row -->
                                                            <tr class="items-detail-row" id="detail-<?= $h['verification_id'] ?>" style="display:none;">
                                                                <td colspan="9" style="background:#f9f9f9;padding:0;">
                                                                    <div style="padding:12px;">
                                                                        <strong>Items in <?= htmlspecialchars($h['verification_no'] ?? '') ?>:</strong>
                                                                        <div class="items-content-<?= $h['verification_id'] ?>">
                                                                            <i class="fa fa-spinner fa-spin"></i> Loading...
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr><td colspan="9" class="text-center text-muted" style="padding:30px;">
                                                            <i class="fa fa-inbox fa-2x"></i><br>No verification history found
                                                        </td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- /tab-history -->

                                </div><!-- /tab-content -->
                            </div><!-- /box-body -->
                        </div><!-- /box -->
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- Modal for item detail view -->
    <div class="modal fade" id="itemsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background:#337ab7;color:#fff;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-list"></i> <span id="modalVerifNo"></span> — Item Details</h4>
                </div>
                <div class="modal-body" id="modalItemsBody">
                    <div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <a href="#" id="modalExportBtn" class="btn btn-success"><i class="fa fa-download"></i> Export</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {

        // ===================== TAB 1: VARIANCE CALC =====================
        $(document).on('change keyup', '.physical-stock', function () {
            const systemStock   = parseFloat($(this).closest('tr').find('.system-stock').val()) || 0;
            const physicalStock = parseFloat($(this).val()) || 0;
            const unitPrice     = parseFloat($(this).closest('tr').find('.unit-price').val()) || 0;
            const variance      = physicalStock - systemStock;
            const varianceValue = variance * unitPrice;

            $(this).closest('tr').find('.variance').val(variance.toFixed(2));
            $(this).closest('tr').find('.variance-value').val(varianceValue.toFixed(2));

            $(this).closest('tr').removeClass('success danger warning');
            if (variance > 0)      $(this).closest('tr').addClass('success');
            else if (variance < 0) $(this).closest('tr').addClass('danger');
            else                   $(this).closest('tr').addClass('warning');

            calculateTotals();
        });

        $('.physical-stock').trigger('change');

        function calculateTotals() {
            let totalVariance = 0, totalVarianceValue = 0;
            $('.variance').each(function ()       { totalVariance      += parseFloat($(this).val()) || 0; });
            $('.variance-value').each(function () { totalVarianceValue += parseFloat($(this).val()) || 0; });
            $('#totalVariance').text(totalVariance.toFixed(2));
            $('#totalVarianceValue').text(totalVarianceValue.toFixed(2));
        }

        // ===================== FORM SUBMIT =====================
        $('#verificationForm').submit(function (e) {
            let hasVariance = false;
            $('.variance').each(function () {
                if (parseFloat($(this).val()) !== 0) { hasVariance = true; return false; }
            });
            if (!hasVariance) {
                if (!confirm('No variance detected. Are you sure you want to save?')) {
                    e.preventDefault(); return;
                }
            }
        });

        // ===================== SEARCH (New Verification table) =====================
        $('#searchItems').on('keyup', function () {
            const q = $(this).val().toLowerCase();
            $('#verificationBody tr.item-row').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
            });
        });

        // ===================== SEARCH (History table) =====================
        $('#searchHistory').on('keyup', function () {
            const q = $(this).val().toLowerCase();
            $('#historyTable tbody tr.history-row').each(function () {
                const show = $(this).text().toLowerCase().indexOf(q) > -1;
                $(this).toggle(show);
                // hide related detail row too
                const id = $(this).find('.view-items-btn').data('id');
                $('#detail-' + id).hide();
            });
        });

        // ===================== VIEW ITEMS (modal) =====================
        $(document).on('click', '.view-items-btn', function () {
            const vid = $(this).data('id');
            const vno = $(this).data('no');
            $('#modalVerifNo').text(vno);
            $('#modalExportBtn').attr('href', '<?= base_url('MaterialIssueController/export_stock_verification') ?>/' + vid);
            $('#modalItemsBody').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading...</div>');
            $('#itemsModal').modal('show');

            $.get('<?= base_url('MaterialIssueController/get_verification_items_ajax') ?>/' + vid, function (data) {
                $('#modalItemsBody').html(data);
            }).fail(function () {
                $('#modalItemsBody').html('<p class="text-danger text-center">Failed to load items.</p>');
            });
        });

        // ===================== EXPORT CURRENT TABLE as CSV =====================
        // (client-side CSV from visible table rows)
    });

    function exportCurrentTable() {
        const date  = document.querySelector('[name="verification_date"]').value || '<?= date('Y-m-d') ?>';
        let csv     = 'Stock Verification\nDate,' + date + '\n\n';
        csv        += 'Item Code,Item Name,System Stock,Physical Stock,Variance,Unit Price,Variance Value\n';

        document.querySelectorAll('#verificationBody tr.item-row').forEach(function (row) {
            const cells = row.querySelectorAll('input:not([type=hidden])');
            const itemText = row.querySelector('td:first-child').textContent.trim();
            const parts    = itemText.split(' - ');
            const code     = (parts[0] || '').trim();
            const name     = (parts.slice(1).join(' - ')).trim();
            csv += [
                '"' + code    + '"',
                '"' + name    + '"',
                cells[0].value,  // system stock
                cells[1].value,  // physical stock
                cells[2].value,  // variance
                cells[3].value,  // unit price
                cells[4].value   // variance value
            ].join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url  = window.URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = 'stock_verification_' + date.replace(/-/g, '') + '.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    }
    </script>

    <style>
        .success { background-color: #dff0d8 !important; }
        .danger  { background-color: #f2dede !important; }
        .warning { background-color: #fcf8e3 !important; }
        .nav-tabs { border-bottom: 2px solid #337ab7; }
        .nav-tabs > li.active > a,
        .nav-tabs > li.active > a:hover,
        .nav-tabs > li.active > a:focus {
            border-top: 3px solid #337ab7;
            font-weight: bold;
            color: #337ab7;
        }
        #historyTable thead th { vertical-align: middle; }
        .badge.bg-success { background: #5cb85c; }
        .badge.bg-warning { background: #f0ad4e; color:#333; }
        .badge.bg-default { background: #aaa; }
    </style>
</body>