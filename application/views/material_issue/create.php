<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Material Issue
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('material_issue') ?>">Material Issue</a></li>
                    <li class="active">Create Issue Slip</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Material Issue Slip</h3>
                                <a href="<?php echo base_url('MaterialIssueController/index') ?>"
                                    class="btn btn-primary pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if (validation_errors()): ?>
                                <div role="alert" class="alert alert-danger">
                                    <button data-dismiss="alert" class="close" type="button"><span
                                            aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                    <strong>Error!</strong> <?php echo validation_errors(); ?>
                                </div>
                                <?php endif; ?>

                                <form method="post" action="<?php echo base_url('MaterialIssueController/create') ?>"
                                    id="issueForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Issue Date <span class="text-danger">*</span></label>
                                                <input type="text" name="issue_date" class="form-control datepicker"
                                                    value="<?= date('d-m-Y') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Issued To (Site/Department/Person) <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="issued_to" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <select name="department" class="form-control select2">
                                                    <option value="">Select Department</option>
                                                    <option value="Construction">Construction</option>
                                                    <option value="Production">Production</option>
                                                    <option value="Maintenance">Maintenance</option>
                                                    <option value="Store">Store</option>
                                                    <option value="Office">Office</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 hide">
                                            <div class="form-group">
                                                <label>Project Code</label>
                                                <select name="project_code" class="form-control select2" >
                                                    <option value="">Select Project</option>
                                                    <?php foreach ($projects as $project): ?>
                                                    <option value="<?= $project->project_code ?>">
                                                        <?= $project->project_code ?> - <?= $project->project_name ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Purpose</label>
                                        <textarea name="purpose" class="form-control" rows="2"></textarea>
                                    </div>

                                   <div class="form-group">
    <label>Job Order</label>
    <select name="joborder_number" id="joborder_number"
        class="form-control select2 joborder-search" style="width: 100%;">
        <option value="">Select Job Order</option>
        <?php foreach ($joborders as $jo): ?>
            <option value="<?= htmlspecialchars($jo->number_fk) ?>">
                <?= htmlspecialchars($jo->number_fk) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

                                    <h4>Items Details</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="itemsTable">
                                            <thead>
                                                <tr>
                                                    <th width="16%">Item *</th>
                                                    <th width="6%">Avail. Stock</th>
                                                    <th width="6%">BOM Qty</th>
                                                    <th width="6%">Prev. MI</th>
                                                    <th width="5%">Allwd %</th>
                                                    <th width="6%">Allwd Qty</th>
                                                    <th width="6%">Max Qty</th>
                                                    <th width="8%">Current MI *</th>
                                                    <th width="7%">Total MI</th>
                                                    <th width="6%">Overrun Qty</th>
                                                    <th width="5%">Overrun %</th>
                                                    <th width="9%">Rate / Overrun Value</th>
                                                    <th width="9%">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsBody">
                                                <tr id="row_0">
                                                    <td>
                                                        <select name="inventory_id[]"
                                                            class="form-control select2 item-select product_name_auto item_search_name name_list"
                                                            data-row="0" required>
                                                            <option value="">Select Item</option>
                                                            <option value="NEW">+ Add New Product</option>
                                                            <?php foreach ($inventory_items as $item): ?>
                                                            <option value="<?= $item['inventory_id'] ?>"
                                                                data-stock="<?= $item['stock'] ?>"
                                                                data-price="<?= $item['sell_price'] ?>"
                                                                data-overrun-pct="<?= $item['allowed_overrun_pct'] ?? 2 ?>"
                                                                data-code="<?= $item['code'] ?>">
                                                                <?= $item['code'] ?> - <?= $item['item_name'] ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <span id="stock_0" class="stock-display">-</span>
                                                        <input type="hidden" name="system_stock[]" id="system_stock_0">
                                                    </td>
                                                    <td>
                                                        <span id="bom_qty_0" class="bom-qty-display">-</span>
                                                        <input type="hidden" name="bom_qty[]" id="bom_qty_hidden_0" value="0">
                                                        <input type="hidden" name="required_base[]" id="required_base_0" value="0">
                                                    </td>
                                                    <td>
                                                        <span id="out_qty_0" class="out-qty-display">0</span>
                                                        <input type="hidden" name="out_base[]" id="out_base_0" value="0">
                                                    </td>
                                                    <td>
                                                        <span id="allowed_pct_0" class="text-info">-</span>
                                                        <input type="hidden" name="allowed_overrun_pct_item[]" id="allowed_overrun_pct_hidden_0" value="0">
                                                    </td>
                                                    <td>
                                                        <span id="allowed_qty_0" class="text-info">-</span>
                                                        <input type="hidden" name="allowed_overrun_qty_item[]" id="allowed_overrun_qty_hidden_0" value="0">
                                                    </td>
                                                    <td>
                                                        <span id="max_qty_0" class="text-info">-</span>
                                                        <input type="hidden" name="max_allowed_qty_item[]" id="max_allowed_qty_hidden_0" value="0">
                                                        <input type="hidden" name="pending_base[]" id="pending_base_0" value="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="quantity[]"
                                                            class="form-control quantity" id="qty_input_0" data-row="0" step="0.01"
                                                            min="0.01" required>
                                                        <span id="qty_helper_0" class="help-block" style="font-size: 11px; margin-bottom: 0;"></span>
                                                    </td>
                                                    <td>
                                                        <span id="total_mi_qty_0" style="font-weight:600;">0</span>
                                                    </td>
                                                    <td>
                                                        <span id="overrun_qty_0" style="font-weight:600;">0</span>
                                                    </td>
                                                    <td>
                                                        <span id="overrun_pct_0" style="font-weight:600;">0%</span>
                                                    </td>
                                                    <td>
                                                        <span id="price_0" class="price-display">-</span><br>
                                                        <small id="overrun_val_0" class="text-danger" style="font-weight:600;"></small>
                                                        <input type="hidden" name="item_price[]" id="item_price_0" value="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item_remarks[]" class="form-control">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

    

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> Create Issue Slip
                                        </button>
                                        <a href="<?php echo base_url('MaterialIssueController/index') ?>"
                                            class="btn btn-default">
                                            Cancel
                                        </a>
                                    </div>
                                </form>
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

    <script>
    let rowCount = 1;

    $(document).ready(function() {
        // Initialize datepicker
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Initialize Select2 on page load
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                placeholder: 'Select an option',
                allowClear: true,
                width: '100%'
            });
        }

        // Add new row (manual item selection — no JO)
        $('#addRow').click(function() {
            const currentRow = rowCount;
            const newRow = `
                <tr id="row_${currentRow}">
                    <td>
                        <select name="inventory_id[]" class="form-control select2 item-select product_name_auto item_search_name name_list" 
                                data-row="${currentRow}" required>
                            <option value="">Select Item</option>
                            <option value="NEW">+ Add New Product</option>
                            <?php foreach ($inventory_items as $item): ?>
                                <option value="<?= $item['inventory_id'] ?>" 
                                        data-stock="<?= $item['stock'] ?>"
                                        data-price="<?= $item['sell_price'] ?>"
                                        data-overrun-pct="<?= $item['allowed_overrun_pct'] ?? 2 ?>"
                                        data-code="<?= $item['code'] ?>">
                                    <?= $item['code'] ?> - <?= $item['item_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <span id="stock_${currentRow}" class="stock-display">-</span>
                        <input type="hidden" name="system_stock[]" id="system_stock_${currentRow}">
                    </td>
                    <td>
                        <span id="bom_qty_${currentRow}" class="bom-qty-display">-</span>
                        <input type="hidden" name="bom_qty[]" id="bom_qty_hidden_${currentRow}" value="0">
                        <input type="hidden" name="required_base[]" id="required_base_${currentRow}" value="0">
                    </td>
                    <td>
                        <span id="out_qty_${currentRow}" class="out-qty-display">0</span>
                        <input type="hidden" name="out_base[]" id="out_base_${currentRow}" value="0">
                    </td>
                    <td>
                        <span id="allowed_pct_${currentRow}" class="text-info">-</span>
                        <input type="hidden" name="allowed_overrun_pct_item[]" id="allowed_overrun_pct_hidden_${currentRow}" value="0">
                    </td>
                    <td>
                        <span id="allowed_qty_${currentRow}" class="text-info">-</span>
                        <input type="hidden" name="allowed_overrun_qty_item[]" id="allowed_overrun_qty_hidden_${currentRow}" value="0">
                    </td>
                    <td>
                        <span id="max_qty_${currentRow}" class="text-info">-</span>
                        <input type="hidden" name="max_allowed_qty_item[]" id="max_allowed_qty_hidden_${currentRow}" value="0">
                        <input type="hidden" name="pending_base[]" id="pending_base_${currentRow}" value="0">
                    </td>
                    <td>
                        <input type="text" name="quantity[]" class="form-control quantity" 
                               id="qty_input_${currentRow}" data-row="${currentRow}" step="0.01" min="0.01" required>
                        <span id="qty_helper_${currentRow}" class="help-block" style="font-size: 11px; margin-bottom: 0; line-height: 1.4;"></span>
                    </td>
                    <td>
                        <span id="overrun_qty_${currentRow}" style="font-weight:600;">0</span>
                    </td>
                    <td>
                        <span id="overrun_pct_${currentRow}" style="font-weight:600;">0%</span>
                    </td>
                    <td>
                        <span id="price_${currentRow}" class="price-display">-</span><br>
                        <small id="overrun_val_${currentRow}" class="text-danger" style="font-weight:600;"></small>
                        <input type="hidden" name="item_price[]" id="item_price_${currentRow}" value="0">
                    </td>
                    <td>
                        <input type="text" name="item_remarks[]" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row" 
                                onclick="removeRow(${currentRow})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
             `;

            $('#itemsBody').append(newRow);

            // Initialize Select2 for the newly added row
            if (typeof $.fn.select2 !== 'undefined') {
                $(`#row_${rowCount} .select2`).select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    width: '100%'
                });
            }

            rowCount++;

            // Enable remove button for first row if there are multiple rows
            if ($('#itemsBody tr').length > 1) {
                $('#row_0 .remove-row').prop('disabled', false);
            }
        });

        // Item selection change (manual mode, no JO)
        $(document).on('change', '.item-select', function() {
            const row = $(this).data('row');
            const selectedOption = $(this).find('option:selected');
            const stock       = parseFloat(selectedOption.data('stock')) || 0;
            const price       = parseFloat(selectedOption.data('price')) || 0;
            const overrunPct  = parseFloat(selectedOption.data('overrun-pct')) || 2;

            if (!$(this).val()) {
                $(`#stock_${row}`).text('-');
                $(`#system_stock_${row}`).val('');
                $(`#bom_qty_${row}`).text('-'); $(`#bom_qty_hidden_${row}`).val(0);
                $(`#required_base_${row}`).val(0);
                $(`#out_base_${row}`).val(0); $(`#out_qty_${row}`).text(0);
                $(`#allowed_pct_${row}`).text('-'); $(`#allowed_overrun_pct_hidden_${row}`).val(0);
                $(`#allowed_qty_${row}`).text('-'); $(`#allowed_overrun_qty_hidden_${row}`).val(0);
                $(`#max_qty_${row}`).text('-'); $(`#max_allowed_qty_hidden_${row}`).val(0);
                $(`#pending_base_${row}`).val(0);
                $(`#price_${row}`).text('-'); $(`#item_price_${row}`).val(0);
                $(`#overrun_qty_${row}`).text(0);
                $(`#overrun_pct_${row}`).text('0%');
                $(`#overrun_val_${row}`).text('');
                return;
            }

            const bomQty         = stock; // When no JO, BOM qty = available stock
            const allowedOvQty   = parseFloat((bomQty * overrunPct / 100).toFixed(4));
            const maxAllowedQty  = parseFloat((bomQty + allowedOvQty).toFixed(4));

            $(`#stock_${row}`).text(stock);
            $(`#system_stock_${row}`).val(stock);
            $(`#bom_qty_${row}`).text(bomQty); $(`#bom_qty_hidden_${row}`).val(bomQty);
            $(`#required_base_${row}`).val(bomQty);
            $(`#pending_base_${row}`).val(bomQty);
            $(`#allowed_pct_${row}`).text(overrunPct + '%'); $(`#allowed_overrun_pct_hidden_${row}`).val(overrunPct);
            $(`#allowed_qty_${row}`).text(allowedOvQty); $(`#allowed_overrun_qty_hidden_${row}`).val(allowedOvQty);
            $(`#max_qty_${row}`).text(maxAllowedQty); $(`#max_allowed_qty_hidden_${row}`).val(maxAllowedQty);
            $(`#price_${row}`).text(price > 0 ? price : '-'); $(`#item_price_${row}`).val(price);
            $(`#out_base_${row}`).val(0); $(`#out_qty_${row}`).text(0);
            $(`#overrun_qty_${row}`).text(0); $(`#overrun_pct_${row}`).text('0%'); $(`#overrun_val_${row}`).text('');
        });

        // Job order selection change -> load JO items with overrun fields
        $('#joborder_number').on('change', function() {
            const joNumber = $(this).val();

            if (!joNumber) {
                $('#itemsBody').html('');
                rowCount = 1;
                $('#addRow').trigger('click');
                return;
            }

            $.ajax({
                url: '<?= base_url("MaterialIssueController/get_joborder_items") ?>',
                method: 'GET',
                dataType: 'json',
                data: { number: joNumber },
                success: function(response) {
                    console.log('Job Order Items Response:', response);
                    if (response.status === 'success') {
                        const items = response.data;
                        $('#itemsBody').html('');
                        rowCount = 0;
                        items.forEach(function(item) {
                            const currentRow = rowCount;
                            const bomQty        = parseFloat(item.required_qty) || 0;
                            const allowedPct    = parseFloat(item.allowed_overrun_pct) || 2;
                            const allowedQty    = parseFloat(item.allowed_overrun_qty) || parseFloat((bomQty * allowedPct / 100).toFixed(4));
                            const maxAllowedQty = parseFloat(item.max_allowed_qty)  || parseFloat((bomQty + allowedQty).toFixed(4));
                            const stockVal      = parseFloat(item.stock) || 0;
                            const outQty        = parseFloat(item.out_qty) || 0;
                            const pendingVal    = parseFloat(item.pending_qty) || 0;
                            const price         = parseFloat(item.price) || 0;
                            const suggestedQty  = Math.max(0, Math.min(pendingVal, stockVal));

                            let helperHtml = '';
                            if (pendingVal > 0) {
                                if (stockVal <= 0) {
                                    helperHtml = `<span style="color:#dd4b39;font-weight:500;"><i class="fa fa-exclamation-triangle"></i> Pending: ${pendingVal} (No Stock)</span>`;
                                } else if (stockVal < pendingVal) {
                                    helperHtml = `<span style="color:#f39c12;font-weight:500;"><i class="fa fa-warning"></i> Pending: ${pendingVal} (Stock: ${stockVal})</span>`;
                                } else {
                                    helperHtml = `<span style="color:#00a65a;font-weight:500;"><i class="fa fa-check-circle"></i> Pending: ${pendingVal}</span>`;
                                }
                            }

                            const itemRow = `
                                <tr id="row_${currentRow}">
                                    <td>
                                        <input type="hidden" name="inventory_id[]" value="${item.inventory_id}">
                                        <strong>${item.item_code} - ${item.item_name}</strong>
                                    </td>
                                    <td>
                                        <span id="stock_${currentRow}" class="stock-display">${stockVal}</span>
                                        <input type="hidden" name="system_stock[]" id="system_stock_${currentRow}" value="${stockVal}">
                                    </td>
                                    <td>
                                        <span id="bom_qty_${currentRow}" class="bom-qty-display text-primary" style="font-weight:600;">${bomQty}</span>
                                        <input type="hidden" name="bom_qty[]" id="bom_qty_hidden_${currentRow}" value="${bomQty}">
                                        <input type="hidden" name="required_base[]" id="required_base_${currentRow}" value="${bomQty}">
                                    </td>
                                    <td>
                                        <span id="out_qty_${currentRow}" class="out-qty-display">${outQty}</span>
                                        <input type="hidden" name="out_base[]" id="out_base_${currentRow}" value="${outQty}">
                                    </td>
                                    <td>
                                        <span id="allowed_pct_${currentRow}" class="text-info">${allowedPct}%</span>
                                        <input type="hidden" name="allowed_overrun_pct_item[]" id="allowed_overrun_pct_hidden_${currentRow}" value="${allowedPct}">
                                    </td>
                                    <td>
                                        <span id="allowed_qty_${currentRow}" class="text-info">${allowedQty}</span>
                                        <input type="hidden" name="allowed_overrun_qty_item[]" id="allowed_overrun_qty_hidden_${currentRow}" value="${allowedQty}">
                                    </td>
                                    <td>
                                        <span id="max_qty_${currentRow}" class="text-info" style="font-weight:600;">${maxAllowedQty}</span>
                                        <input type="hidden" name="max_allowed_qty_item[]" id="max_allowed_qty_hidden_${currentRow}" value="${maxAllowedQty}">
                                        <input type="hidden" name="pending_base[]" id="pending_base_${currentRow}" value="${pendingVal}">
                                    </td>
                                    <td>
                                        <input type="text" name="quantity[]" class="form-control quantity" id="qty_input_${currentRow}" data-row="${currentRow}" step="0.01" min="0" value="${suggestedQty}" required>
                                        <span id="qty_helper_${currentRow}" class="help-block" style="font-size: 11px; margin-bottom: 0; line-height: 1.4;">${helperHtml}</span>
                                    </td>
                                    <td>
                                        <span id="total_mi_qty_${currentRow}" style="font-weight:600;">0</span>
                                    </td>
                                    <td>
                                        <span id="overrun_qty_${currentRow}" style="font-weight:600;">0</span>
                                    </td>
                                    <td>
                                        <span id="overrun_pct_${currentRow}" style="font-weight:600;">0%</span>
                                    </td>
                                    <td>
                                        <span id="price_${currentRow}" class="price-display">${price > 0 ? price.toFixed(2) : '-'}</span><br>
                                        <small id="overrun_val_${currentRow}" class="text-danger" style="font-weight:600;"></small>
                                        <input type="hidden" name="item_price[]" id="item_price_${currentRow}" value="${price}">
                                    </td>
                                    <td>
                                        <input type="text" name="item_remarks[]" class="form-control">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row" 
                                                onclick="removeRow(${currentRow})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;

                            $('#itemsBody').append(itemRow);
                            // Trigger overrun calculation for pre-filled quantity
                            $(`#qty_input_${currentRow}`).trigger('input');
                            rowCount++;
                        });

                        if (rowCount > 1) {
                            $('#row_0 .remove-row').prop('disabled', false);
                        }
                    } else {
                        alert(response.message || 'Failed to load job order items');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    let errorMsg = 'Error fetching job order items';
                    try { errorMsg = JSON.parse(xhr.responseText).message || errorMsg; } catch(e) {}
                    alert(errorMsg + ' (Check browser console for details)');
                }
            });
        });

        // ── OVERRUN REAL-TIME CALCULATION (CUMULATIVE) ──
        // Triggered on every keystroke/change of the MI Quantity input
        $(document).on('input change', '.quantity', function() {
            const row           = $(this).data('row');
            const miQty         = parseFloat($(this).val()) || 0;
            const bomQty        = parseFloat($(`#bom_qty_hidden_${row}`).val()) || 0;
            const allowedPct    = parseFloat($(`#allowed_overrun_pct_hidden_${row}`).val()) || 0;
            const allowedQty    = parseFloat($(`#allowed_overrun_qty_hidden_${row}`).val()) || 0;
            const maxAllowedQty = parseFloat($(`#max_allowed_qty_hidden_${row}`).val()) || 0;
            const stockVal      = parseFloat($(`#stock_${row}`).text()) || 0;
            const prevIssuedQty = parseFloat($(`#out_base_${row}`).val()) || 0;
            const rate          = parseFloat($(`#item_price_${row}`).val()) || 0;

            // Cumulative Total MI Quantity = Prev Issued Qty + Current MI Qty
            const totalMiQty    = parseFloat((prevIssuedQty + miQty).toFixed(4));
            $(`#total_mi_qty_${row}`).text(totalMiQty);

            // 1. Overrun Qty: totalMiQty - bomQty (only when positive)
            const overrunQty  = Math.max(0, parseFloat((totalMiQty - bomQty).toFixed(4)));
            // 2. Overrun %: (totalMiQty - bomQty) / bomQty * 100
            const overrunPct  = (bomQty > 0 && totalMiQty > bomQty) ? parseFloat(((totalMiQty - bomQty) / bomQty * 100).toFixed(2)) : 0;
            // 3. Overrun Value: overrunQty * rate
            const overrunVal  = parseFloat((overrunQty * rate).toFixed(2));

            // Update overrun display cells
            const $overrunQtySpan  = $(`#overrun_qty_${row}`);
            const $overrunPctSpan  = $(`#overrun_pct_${row}`);
            const $overrunValSmall = $(`#overrun_val_${row}`);
            const $helper          = $(`#qty_helper_${row}`);

            $overrunQtySpan.text(overrunQty > 0 ? overrunQty : 0);
            $overrunPctSpan.text(overrunPct > 0 ? overrunPct + '%' : '0%');

            // 4. Color-coded status feedback based on CUMULATIVE Total MI
            if (bomQty > 0 && totalMiQty > bomQty) {
                if (totalMiQty <= maxAllowedQty) {
                    // Within allowed tolerance — orange warning
                    $overrunQtySpan.css('color', '#f39c12');
                    $overrunPctSpan.css('color', '#f39c12');
                    $overrunValSmall.text(rate > 0 ? '₹' + overrunVal.toFixed(2) + ' overrun' : '').css('color', '#f39c12');
                    $helper.html(`<span style="color:#f39c12;font-weight:500;"><i class="fa fa-warning"></i> Within Limit (Total: ${totalMiQty} / Max: ${maxAllowedQty})</span>`);
                } else {
                    // Exceeds tolerance — red, approval required
                    $overrunQtySpan.css('color', '#dd4b39');
                    $overrunPctSpan.css('color', '#dd4b39');
                    $overrunValSmall.text(rate > 0 ? '₹' + overrunVal.toFixed(2) + ' overrun' : '').css('color', '#dd4b39');
                    $helper.html(`<span style="color:#dd4b39;font-weight:500;"><i class="fa fa-exclamation-triangle"></i> <strong>Approval Required</strong> (Total: ${totalMiQty} / Max: ${maxAllowedQty})</span>`);
                }
            } else {
                // No overrun
                $overrunQtySpan.css('color', '#00a65a').text('0');
                $overrunPctSpan.css('color', '#00a65a').text('0%');
                $overrunValSmall.text('');
                if (bomQty > 0) {
                    const remaining = Math.max(0, bomQty - totalMiQty);
                    if (remaining > 0) {
                        $helper.html(`<span style="color:#f39c12;font-weight:500;"><i class="fa fa-warning"></i> Remaining: ${remaining}</span>`);
                    } else {
                        $helper.html(`<span style="color:#00a65a;font-weight:500;"><i class="fa fa-check-circle"></i> Fully Covered</span>`);
                    }
                }
            }
        });
    });

    function removeRow(rowId) {
        $(`#row_${rowId}`).remove();

        // If only one row left, disable remove button
        if ($('#itemsBody tr').length === 1) {
            $('#row_0 .remove-row').prop('disabled', true);
        }
    }

    // Form validation before submit
    $('#issueForm').submit(function(e) {
        // Check if at least one item is added with quantity
        const itemCount = $('#itemsBody tr').filter(function() {
            const quantity = parseFloat($(`#${$(this).attr('id')} .quantity`).val());
            return !isNaN(quantity) && quantity > 0;
        }).length;

        if (itemCount === 0) {
            e.preventDefault();
            alert('Please add at least one item to the issue slip.');
            return false;
        }

        // Validate each item quantity against stock, required, out, and pending
        let isValid = true;
        $('#itemsBody tr').each(function() {
            const rowId = $(this).attr('id').replace('row_', '');
            const quantity = parseFloat($(`#row_${rowId} .quantity`).val()) || 0;
            const displayedStock = parseFloat($(`#stock_${rowId}`).text()) || 0;
            const requiredQty = parseFloat($(`#required_base_${rowId}`).val()) || 0;
            const baseOut = parseFloat($(`#out_base_${rowId}`).val()) || 0;
            const pendingQty = parseFloat($(`#pending_qty_${rowId}`).text()) || 0;
            const outQty = baseOut + quantity;
            const itemName = $(`#row_${rowId} td:first-child`).text().trim();

            if (quantity === 0) {
                return true; // Skip validation for 0 quantity items (they won't be issued)
            }

            if (quantity > displayedStock) {
                alert(`Quantity for ${itemName} exceeds available stock (${displayedStock})`);
                isValid = false;
                return false;
            }

            if (requiredQty > 0 && quantity > requiredQty) {
                alert(`Quantity for ${itemName} cannot exceed required quantity (${requiredQty})`);
                isValid = false;
                return false;
            }

            if (requiredQty > 0 && outQty > requiredQty) {
                alert(
                    `Out quantity for ${itemName} cannot exceed required quantity (${requiredQty}), current out would be ${outQty}`
                );
                isValid = false;
                return false;
            }

            if (pendingQty < 0) {
                alert(`Pending quantity for ${itemName} cannot be negative`);
                isValid = false;
                return false;
            }

            // If requiredQty set, enforce pending based on required - out
            if (requiredQty > 0) {
                const computedPending = Math.max(0, requiredQty - outQty);
                if (Math.abs(computedPending - pendingQty) > 0.0001) {
                    alert(
                        `Pending quantity for ${itemName} does not match computed required-out (${computedPending.toFixed(2)}).`
                    );
                    isValid = false;
                    return false;
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
        }

        return isValid;
    });
    </script>
</body>