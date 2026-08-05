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
                                                    <th width="20%">Item *</th>
                                                    <th width="8%">Available Stock</th>
                                                    <th width="8%">Required Qty</th>
                                                    <th width="8%">Out Qty</th>
                                                    <th width="8%">Pending</th>
                                                    <th width="8%">Price</th>
                                                    <th width="12%">Quantity *</th>
                                                    <th width="16%">Remarks</th>
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
                                                        <span id="required_qty_0" class="required-qty-display">-</span>
                                                        <input type="hidden" name="required_base[]" id="required_base_0"
                                                            value="0">
                                                    </td>
                                                    <td>
                                                        <span id="out_qty_0" class="out-qty-display">0</span>
                                                        <input type="hidden" name="out_base[]" id="out_base_0"
                                                            value="0">
                                                    </td>
                                                    <td>
                                                        <span id="pending_qty_0" class="pending-qty-display">-</span>
                                                        <input type="hidden" name="pending_base[]" id="pending_base_0"
                                                            value="0">
                                                    </td>
                                                    <td>
                                                        <span id="price_0" class="price-display">-</span>
                                                        <input type="hidden" name="item_price[]" id="item_price_0" value="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="quantity[]"
                                                            class="form-control quantity" data-row="0" step="0.01"
                                                            min="0.01" required>
                                                        <span id="qty_helper_0" class="help-block" style="font-size: 11px; margin-bottom: 0;"></span>
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

        // Add new row
        $('#addRow').click(function() {
            const newRow = `
                <tr id="row_${rowCount}">
                    <td>
                        <select name="inventory_id[]" class="form-control select2 item-select product_name_auto item_search_name name_list" 
                                data-row="${rowCount}" required>
                            <option value="">Select Item</option>
                            <option value="NEW">+ Add New Product</option>
                            <?php foreach ($inventory_items as $item): ?>
                                <option value="<?= $item['inventory_id'] ?>" 
                                        data-stock="<?= $item['stock'] ?>"
                                        data-price="<?= $item['sell_price'] ?>"
                                        data-code="<?= $item['code'] ?>">
                                    <?= $item['code'] ?> - <?= $item['item_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <span id="stock_${rowCount}" class="stock-display">-</span>
                        <input type="hidden" name="system_stock[]" id="system_stock_${rowCount}">
                    </td>
                    <td>
                        <span id="required_qty_${rowCount}" class="required-qty-display">-</span>
                        <input type="hidden" name="required_base[]" id="required_base_${rowCount}" value="0">
                    </td>
                    <td>
                        <span id="out_qty_${rowCount}" class="out-qty-display">0</span>
                        <input type="hidden" name="out_base[]" id="out_base_${rowCount}" value="0">
                    </td>
                    <td>
                        <span id="pending_qty_${rowCount}" class="pending-qty-display">-</span>
                        <input type="hidden" name="pending_base[]" id="pending_base_${rowCount}" value="0">
                    </td>
                    <td>
                        <span id="price_${rowCount}" class="price-display">-</span>
                        <input type="hidden" name="item_price[]" id="item_price_${rowCount}" value="0">
                    </td>
                    <td>
                        <input type="text" name="quantity[]" class="form-control quantity" 
                               data-row="${rowCount}" step="0.01" required>
                        <span id="qty_helper_${rowCount}" class="help-block" style="font-size: 11px; margin-bottom: 0;"></span>
                    </td>
                    <td>
                        <input type="text" name="item_remarks[]" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row" 
                                onclick="removeRow(${rowCount})">
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

        // Item selection change
        $(document).on('change', '.item-select', function() {
            const row = $(this).data('row');
            const inventoryId = $(this).val();

            if (!inventoryId) {
                // Clear all fields if no item selected
                $(`#stock_${row}`).text('-');
                $(`#system_stock_${row}`).val('');
                $(`#pending_base_${row}`).val(0);
                $(`#pending_qty_${row}`).text('-');
                $(`#out_base_${row}`).val(0);
                $(`#out_qty_${row}`).text(0);
                $(`#price_${row}`).text('-');
                $(`#item_price_${row}`).val('0');
                $(`#row_${row} .quantity`).attr('max', 0);
                return;
            }

            // Get stock and price from data attribute
            const selectedOption = $(this).find('option:selected');
            const stock = selectedOption.data('stock');
            const price = selectedOption.data('price');

            // Update stock display from attribute
            if (stock) {
                $(`#stock_${row}`).text(stock);
                $(`#system_stock_${row}`).val(stock);
                $(`#required_base_${row}`).val(stock);
                $(`#required_qty_${row}`).text(stock);
                $(`#pending_base_${row}`).val(stock);
                $(`#pending_qty_${row}`).text(stock);
                $(`#row_${row} .quantity`).attr('max', stock);
            } else {
                $(`#stock_${row}`).text('-');
                $(`#required_base_${row}`).val(0);
                $(`#required_qty_${row}`).text('-');
                $(`#pending_qty_${row}`).text('-');
            }

            // Update price display from attribute
            if (price) {
                $(`#price_${row}`).text(price);
                $(`#item_price_${row}`).val(price);
            } else {
                $(`#price_${row}`).text('-');
                $(`#item_price_${row}`).val('0');
            }

            // Set out/base for manual selection
            $(`#out_base_${row}`).val(0);
            $(`#out_qty_${row}`).text(0);
        });

        // Job order selection change -> load JO items
        $('#joborder_number').on('change', function() {
            const joNumber = $(this).val();

            if (!joNumber) {
                // Reset to single blank row
                $('#itemsBody').html('');
                rowCount = 1;
                $('#addRow').trigger('click');
                return;
            }

            $.ajax({
                url: '<?= base_url("MaterialIssueController/get_joborder_items") ?>',
                method: 'GET',
                dataType: 'json',
                data: {
                    number: joNumber
                },
                success: function(response) {
                    console.log('Job Order Items Response:', response);
                    if (response.status === 'success') {
                        const items = response.data;
                        $('#itemsBody').html('');
                        rowCount = 0;
                        items.forEach(function(item) {
                            const currentRow = rowCount;
                            const suggestedQty = Math.max(0, Math.min(parseFloat(item.pending_qty) || 0, parseFloat(item.stock) || 0));
                            const pendingVal = parseFloat(item.pending_qty) || 0;
                            const stockVal = parseFloat(item.stock) || 0;
                            
                            let helperHtml = '';
                            if (pendingVal > 0) {
                                if (stockVal <= 0) {
                                    helperHtml = `<span style="color: #dd4b39; font-weight: 500;"><i class="fa fa-exclamation-triangle"></i> Pending: ${pendingVal} (No Stock)</span>`;
                                } else if (stockVal < pendingVal) {
                                    helperHtml = `<span style="color: #f39c12; font-weight: 500;"><i class="fa fa-warning"></i> Pending: ${pendingVal} (Stock: ${stockVal})</span>`;
                                } else {
                                    helperHtml = `<span style="color: #00a65a; font-weight: 500;"><i class="fa fa-check-circle"></i> Pending: ${pendingVal}</span>`;
                                }
                            }

                            const itemRow = `
                                <tr id="row_${currentRow}">
                                    <td>
                                        <input type="hidden" name="inventory_id[]" value="${item.inventory_id}">
                                        <strong>${item.item_code} - ${item.item_name}</strong>
                                    </td>
                                    <td>
                                        <span id="stock_${currentRow}" class="stock-display">${item.stock}</span>
                                        <input type="hidden" name="system_stock[]" id="system_stock_${currentRow}" value="${item.stock}">
                                    </td>
                                    <td>
                                        <span id="required_qty_${currentRow}" class="required-qty-display">${item.required_qty}</span>
                                        <input type="hidden" name="required_base[]" id="required_base_${currentRow}" value="${item.required_qty}">
                                    </td>
                                    <td>
                                        <span id="out_qty_${currentRow}" class="out-qty-display">${item.out_qty}</span>
                                        <input type="hidden" name="out_base[]" id="out_base_${currentRow}" value="${item.out_qty}">
                                    </td>
                                    <td>
                                        <span id="pending_qty_${currentRow}" class="pending-qty-display">${item.pending_qty}</span>
                                        <input type="hidden" name="pending_base[]" id="pending_base_${currentRow}" value="${item.pending_qty}">
                                    </td>
                                    <td>
                                        <span id="price_${currentRow}" class="price-display">${item.price || '-'}</span>
                                        <input type="hidden" name="item_price[]" id="item_price_${currentRow}" value="${item.price || 0}">
                                    </td>
                                    <td>
                                        <input type="text" name="quantity[]" class="form-control quantity" data-row="${currentRow}" step="0.01" min="0" max="${item.stock}" value="${suggestedQty}" required>
                                        <span id="qty_helper_${currentRow}" class="help-block" style="font-size: 11px; margin-bottom: 0; line-height: 1.4;">${helperHtml}</span>
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
                            $(`#row_${currentRow} .quantity`).trigger('change');
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
                    console.error('AJAX Error Status:', status);
                    console.error('AJAX Error:', error);
                    console.error('AJAX Response:', xhr.responseText);
                    let errorMsg = 'Error fetching job order items';
                    if (xhr.responseText) {
                        try {
                            let resp = JSON.parse(xhr.responseText);
                            errorMsg = resp.message || errorMsg;
                        } catch (e) {
                            errorMsg = xhr.responseText;
                        }
                    }
                    alert(errorMsg + ' (Check browser console for details)');
                }
            });
        });

        // Quantity validation and pending/out update
        $(document).on('input change', '.quantity', function() {
            const row = $(this).data('row');
            let quantity = parseFloat($(this).val()) || 0;
            const stock = parseFloat($(`#stock_${row}`).text()) || 0;
            const basePending = parseFloat($(`#pending_base_${row}`).val()) || stock;
            const requiredQty = parseFloat($(`#required_base_${row}`).val()) || 0;
            const baseOut = parseFloat($(`#out_base_${row}`).val()) || 0;

            // Determine current allowed limit based on required, pending and stock
            const effectiveRequired = requiredQty > 0 ? requiredQty : stock;
            const allowedMax = Math.max(0, Math.min(stock, basePending + baseOut, effectiveRequired));

            if (quantity > allowedMax) {
                alert(`Quantity cannot exceed allowed limit (${allowedMax})`);
                quantity = allowedMax;
                $(this).val(allowedMax);
            }

            // Update pending: remaining required minus currently issued quantity
            const pending = Math.max(0, (requiredQty > 0 ? requiredQty : basePending + baseOut) - (
                baseOut + quantity));
            $(`#pending_qty_${row}`).text(pending);

            // Update out quantity: baseOut + Current Quantity
            const outQty = Math.max(0, baseOut + quantity);
            $(`#out_qty_${row}`).text(outQty);

            // Update helper block dynamically
            const helper = $(`#qty_helper_${row}`);
            if (helper.length > 0 && requiredQty > 0) {
                if (pending > 0) {
                    if (stock <= 0) {
                         helper.html(`<span style="color: #dd4b39; font-weight: 500;"><i class="fa fa-exclamation-triangle"></i> Pending: ${pending} (No Stock)</span>`);
                    } else {
                         helper.html(`<span style="color: #f39c12; font-weight: 500;"><i class="fa fa-warning"></i> Remaining: ${pending}</span>`);
                    }
                } else {
                    helper.html(`<span style="color: #00a65a; font-weight: 500;"><i class="fa fa-check-circle"></i> Fully Covered</span>`);
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