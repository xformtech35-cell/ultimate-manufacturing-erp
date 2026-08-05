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
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Create PO Amendment
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('PoamendmentController/index'); ?>">PO Amendments</a></li>
                    <li class="active">Create Amendment</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create PO Amendment</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <?php echo form_open_multipart('PoamendmentController/create', array('class' => 'form-horizontal form_overlay', 'id' => 'amendmentForm')); ?>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row ">
                                            <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                                                <?php if (isset($po) && $po): ?>
                                                    <h2>Amendment for PO: <b><?php echo $po['number_fk']; ?></b></h2>
                                                <?php else: ?>
                                                    <h2>Select PO to Create Amendment</h2>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- PO Selection -->
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label for="po_id" class="col-sm-4 control-label">Select PO *</label>
                                            <div class="col-sm-8">
                                                <?php if (isset($po) && $po): ?>
                                                    <input type="hidden" name="po_id" value="<?php echo $po['id']; ?>">
                                                    <input type="hidden" name="po_number" value="<?php echo $po['number_fk']; ?>">
                                                    <div class="well well-sm">
                                                        <strong>PO Number:</strong> <?php echo $po['number_fk']; ?><br>
                                                        <strong>Vendor:</strong> <?php echo $po['company_name']; ?><br>
                                                        <strong>Total Amount:</strong> ₹<?php echo number_format($po['total'], 2); ?><br>
                                                        <strong>PO Date:</strong> <?php echo date('d-M-Y', strtotime($po['date'])); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <select name="po_id" id="po_id" class="form-control input-sm select2" required onchange="loadPOItems()">
                                                        <option value="">Select PO</option>
                                                        <?php
                                                        // Load approved POs from database
                                                        $this->db->select('pt.id, pt.number_fk, pt.date, pt.total, s.company_name, s.supplier_id');
                                                        $this->db->from('po_total pt');
                                                        $this->db->join('supplier s', 's.supplier_id = pt.supplier_id_fk');
                                                        $this->db->where('pt.approval_status', 'approved');
                                                        $this->db->where('pt.status', '4');
                                                        $this->db->order_by('pt.date', 'desc');
                                                        $pos = $this->db->get()->result_array();

                                                        foreach ($pos as $po_item):
                                                        ?>
                                                            <option value="<?php echo $po_item['id']; ?>"
                                                                data-number="<?php echo $po_item['number_fk']; ?>"
                                                                data-date="<?php echo $po_item['date']; ?>"
                                                                data-total="<?php echo $po_item['total']; ?>"
                                                                data-company="<?php echo $po_item['company_name']; ?>">
                                                                <?php echo $po_item['number_fk']; ?> - <?php echo $po_item['company_name']; ?>
                                                                (₹<?php echo number_format($po_item['total'], 2); ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <input type="hidden" name="po_number" id="po_number">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Amendment Type -->
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label for="amendment_type" class="col-sm-4 control-label">Amendment Type *</label>
                                            <div class="col-sm-8">
                                                <select name="amendment_type" class="form-control input-sm" required>
                                                    <option value="">Select Type</option>
                                                    <option value="design_change">Design Change</option>
                                                    <option value="spec_change">Specification Change</option>
                                                    <option value="drawing_change">Drawing Change</option>
                                                    <option value="price_change">Price Change</option>
                                                    <option value="quantity_change">Quantity Change</option>
                                                    <option value="delivery_change">Delivery Change</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Amendment Value -->
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label for="amendment_value" class="col-sm-4 control-label">Amendment Value</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="amendment_value" id="amendment_value" class="form-control input-sm" value="0.00" readonly>
                                                <small class="text-muted">Calculated automatically</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description and Reason -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="description" class="col-sm-2 control-label">Description *</label>
                                            <div class="col-sm-10">
                                                <textarea name="description" class="form-control" rows="3" required placeholder="Brief description of the amendment"><?php echo set_value('description'); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="reason" class="col-sm-2 control-label">Reason *</label>
                                            <div class="col-sm-10">
                                                <textarea name="reason" class="form-control" rows="3" required placeholder="Detailed reason for the amendment"><?php echo set_value('reason'); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Attachment -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="attachment" class="col-sm-2 control-label">Attachment</label>
                                            <div class="col-sm-10">
                                                <input type="file" name="attachment" class="form-control input-sm">
                                                <small class="text-muted">PDF, DOC, JPG, PNG (Max 5MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div align="center">
                                            <button type="submit" name="save_draft" class="btn btn-info">
                                                <i class="fa fa-save"></i> Save as Draft
                                            </button>
                                            <button type="submit" name="submit_for_approval" class="btn btn-success">
                                                <i class="fa fa-paper-plane"></i> Submit for Approval
                                            </button>
                                            <button type="button" onclick="window.history.back()" class="btn btn-default">
                                                <i class="fa fa-times"></i> Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <?php echo form_close(); ?>

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
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Track amendment changes
            let amendmentChanges = [];
            let totalAmendmentValue = 0;

            // Load PO items via AJAX
            function loadPOItems() {
                const poId = $('#po_id').val();
                if (!poId) return;

                $('#loader').show();

                $.ajax({
                    url: '<?php echo base_url("PoamendmentController/get_po_items_ajax"); ?>',
                    type: 'POST',
                    data: {
                        po_id: poId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Update page with PO items
                            updatePOItemsDisplay(response);
                            $('#amendment_changes_section').show();
                        } else {
                            alert(response.message);
                            $('#amendment_changes_section').hide();
                        }
                        $('#loader').hide();
                    },
                    error: function() {
                        alert('Error loading PO items');
                        $('#loader').hide();
                    }
                });
            }

            // Amend item button click
            $(document).on('click', '.amend-item-btn', function() {
                const rowNum = $(this).data('row');
                const row = $('#po_item_row_' + rowNum);

                // Show edit fields
                row.find('.new-value').removeClass('hide');
                row.find('.original-value').addClass('hide');
                row.find('.amend-item-btn').addClass('hide');
                row.find('.save-amendment-btn').removeClass('hide');
                row.find('.cancel-amendment-btn').removeClass('hide');
            });

            // Cancel amendment
            $(document).on('click', '.cancel-amendment-btn', function() {
                const rowNum = $(this).data('row');
                const row = $('#po_item_row_' + rowNum);

                // Hide edit fields
                row.find('.new-value').addClass('hide').val('');
                row.find('.original-value').removeClass('hide');
                row.find('.amend-item-btn').removeClass('hide');
                row.find('.save-amendment-btn').addClass('hide');
                row.find('.cancel-amendment-btn').addClass('hide');
                row.find('#item_amended_' + rowNum).val('0');
            });

            // Save amendment
            $(document).on('click', '.save-amendment-btn', function() {
                const rowNum = $(this).data('row');
                const row = $('#po_item_row_' + rowNum);

                const itemCode = row.find('select[name="item_code[]"]').val();
                const itemName = row.find('select[name="item_code[]"] option:selected').text();
                const originalQty = parseFloat(row.find('input[name="original_quantity[]"]').val()) || 0;
                const newQty = parseFloat(row.find('input[name="new_quantity[]"]').val()) || originalQty;
                const originalPrice = parseFloat(row.find('input[name="original_price[]"]').val()) || 0;
                const newPrice = parseFloat(row.find('input[name="new_price[]"]').val()) || originalPrice;
                const originalAmount = originalQty * originalPrice;
                const newAmount = newQty * newPrice;
                const changeAmount = newAmount - originalAmount;

                // Validate changes
                if (newQty === originalQty && newPrice === originalPrice) {
                    alert('No changes made. Please modify quantity or price.');
                    return;
                }

                // Determine change type
                let changeType = '';
                if (newQty !== originalQty && newPrice !== originalPrice) {
                    changeType = 'Quantity & Price';
                } else if (newQty !== originalQty) {
                    changeType = 'Quantity';
                } else {
                    changeType = 'Price';
                }

                // Add to amendment changes
                const changeId = 'change_' + Date.now();
                const changeRow = `
                    <tr id="${changeId}">
                        <td>${itemName}</td>
                        <td>${changeType}</td>
                        <td>Qty: ${originalQty} @ ₹${originalPrice.toFixed(2)} = ₹${originalAmount.toFixed(2)}</td>
                        <td>Qty: ${newQty} @ ₹${newPrice.toFixed(2)} = ₹${newAmount.toFixed(2)}</td>
                        <td class="${changeAmount >= 0 ? 'text-success' : 'text-danger'}">${changeAmount >= 0 ? '+' : ''}₹${changeAmount.toFixed(2)}</td>
                        <td>
                            <input type="text" class="form-control input-sm" name="change_description_${changeId}" placeholder="Change description" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-xs btn-danger remove-change" data-change-id="${changeId}" data-row="${rowNum}">
                                <i class="fa fa-trash"></i>
                            </button>
                            <input type="hidden" name="change_item_code_${changeId}" value="${itemCode}">
                            <input type="hidden" name="change_original_qty_${changeId}" value="${originalQty}">
                            <input type="hidden" name="change_new_qty_${changeId}" value="${newQty}">
                            <input type="hidden" name="change_original_price_${changeId}" value="${originalPrice}">
                            <input type="hidden" name="change_new_price_${changeId}" value="${newPrice}">
                        </td>
                    </tr>`;

                $('#amendment_changes_body').append(changeRow);

                // Update totals
                totalAmendmentValue += changeAmount;
                updateTotals();

                // Mark item as amended
                row.find('#item_amended_' + rowNum).val('1');
                row.find('.new-amount').text('₹' + newAmount.toFixed(2)).removeClass('hide');
                row.find('.original-amount').addClass('hide');

                // Reset edit mode
                row.find('.amend-item-btn').removeClass('hide').html('<i class="fa fa-edit"></i> Edit');
                row.find('.save-amendment-btn').addClass('hide');
                row.find('.cancel-amendment-btn').addClass('hide');
            });

            // Remove change
            $(document).on('click', '.remove-change', function() {
                const changeId = $(this).data('change-id');
                const rowNum = $(this).data('row');
                const row = $('#po_item_row_' + rowNum);

                // Get change amount from the row being removed
                const changeAmountText = $('#' + changeId).find('td:nth-child(5)').text();
                const changeAmount = parseFloat(changeAmountText.replace(/[^0-9.-]+/g, "")) || 0;

                // Update totals
                totalAmendmentValue -= changeAmount;
                updateTotals();

                // Reset item
                row.find('#item_amended_' + rowNum).val('0');
                row.find('.new-amount').addClass('hide');
                row.find('.original-amount').removeClass('hide');
                row.find('.new-value').addClass('hide').val('');
                row.find('.original-value').removeClass('hide');

                // Remove change row
                $('#' + changeId).remove();
            });

            function updateTotals() {
                const originalTotal = parseFloat($('#original_total').text().replace(/[^0-9.-]+/g, "")) || 0;
                const revisedTotal = originalTotal + totalAmendmentValue;

                $('#amendment_value').val(totalAmendmentValue.toFixed(2));
                $('#amendment_total').text('₹' + totalAmendmentValue.toFixed(2));
                $('#revised_total').text('₹' + revisedTotal.toFixed(2));
            }

            function updatePOItemsDisplay(response) {
                // This function would update the PO items table with AJAX data
                console.log('PO Items loaded:', response);
                // Implement your AJAX update logic here
            }

            // Form validation
            $('#amendmentForm').submit(function(e) {
                // Basic validation
                const poId = $('#po_id').val();
                if (!poId) {
                    e.preventDefault();
                    alert('Please select a PO');
                    return false;
                }

                const amendmentType = $('select[name="amendment_type"]').val();
                if (!amendmentType) {
                    e.preventDefault();
                    alert('Please select amendment type');
                    return false;
                }

                // Check if any changes made
                // if ($('#amendment_changes_body tr').length === 0) {
                //     e.preventDefault();
                //     alert('Please make at least one amendment to items');
                //     return false;
                // }

                return true;
            });
        });
    </script>


    <script>
        // Function to load PO items when PO is selected
        function loadPOItems(poId) {
            if (!poId) {
                // Hide sections if no PO selected
                $('#po_items_section').hide();
                $('#amendment_changes_section').hide();
                return;
            }

            // Show loading indicator
            $('#po_items_section').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br>Loading PO items...</div>');
            $('#po_items_section').show();

            // Make AJAX call to get PO items
            $.ajax({
                url: '<?php echo base_url("PoamendmentController/get_po_items/"); ?>' + poId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        renderPOItems(response.data, response.po);
                        $('#amendment_changes_section').show();
                    } else {
                        $('#po_items_section').html('<div class="alert alert-danger">' + response.message + '</div>');
                        $('#amendment_changes_section').hide();
                    }
                },
                error: function(xhr, status, error) {
                    $('#po_items_section').html('<div class="alert alert-danger">Error loading PO items. Please try again.</div>');
                    $('#amendment_changes_section').hide();
                    console.error('AJAX Error:', error);
                }
            });
        }

        // Function to render PO items table
        function renderPOItems(items, poDetails) {
            if (items.length === 0) {
                $('#po_items_section').html('<div class="alert alert-warning">No items found for this PO.</div>');
                return;
            }

            let html = `
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-cubes"></i> PO Items - ${poDetails.po_number}</h4>
            </div>
            <div class="panel-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Supplier:</strong> ${poDetails.supplier_name}
                    </div>
                    <div class="col-md-3">
                        <strong>PO Date:</strong> ${poDetails.po_date}
                    </div>
                    <div class="col-md-3">
                        <strong>Total Amount:</strong> ${formatCurrency(poDetails.total_amount)}
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong> ${poDetails.status}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="po_items_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;

            let totalAmount = 0;

            items.forEach(function(item, index) {
                const itemAmount = parseFloat(item.quantity) * parseFloat(item.price);
                totalAmount += itemAmount;

                html += `
            <tr id="po_item_${item.po_id}" data-item-id="${item.po_id}">
                <td>${index + 1}</td>
                <td>
                    <strong>${escapeHtml(item.product_name)}</strong>
                </td>
                <td>
                    <small class="text-muted">${escapeHtml(item.description.substring(0, 100))}...</small>
                </td>
                <td class="text-right">${parseFloat(item.quantity).toFixed(2)}</td>
                <td>${item.unit}</td>
                <td class="text-right">${formatCurrency(item.price)}</td>
                <td class="text-right">${formatCurrency(itemAmount)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-info add-to-amendment" 
                            data-item-id="${item.po_id}"
                            data-product-name="${escapeHtml(item.product_name)}"
                            data-quantity="${item.quantity}"
                            data-unit="${item.unit}"
                            data-price="${item.price}"
                            data-description="${escapeHtml(item.description.substring(0, 200))}">
                        <i class="fa fa-plus"></i> Add Change
                    </button>
                </td>
            </tr>`;
            });

            html += `
                            <tr class="active">
                                <td colspan="6" class="text-right"><strong>Total:</strong></td>
                                <td class="text-right"><strong>${formatCurrency(totalAmount)}</strong></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>`;

            $('#po_items_section').html(html);

            // Reset amendment section
            $('#amendment_changes_body').empty();
            $('#amendment_value').val('0');
            totalAmendmentValue = 0;

            // Reinitialize event handlers for new buttons
            initializeEventHandlers();
        }

        // Helper function to format currency
        function formatCurrency(amount) {
            return parseFloat(amount).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        // Global variables for amendment handling
        let changeCounter = 0;
        let totalAmendmentValue = 0;

        // Initialize all event handlers
        function initializeEventHandlers() {
            // Add change button click handler
            $(document).off('click', '.add-to-amendment').on('click', '.add-to-amendment', function() {
                changeCounter++;
                const itemId = $(this).data('item-id');
                const productName = $(this).data('product-name');
                const quantity = $(this).data('quantity');
                const unit = $(this).data('unit');
                const price = $(this).data('price');
                const description = $(this).data('description');

                // Create new change row
                const newRow = `
            <tr id="change_row_${changeCounter}">
                <td>
                    ${productName}
                    <input type="hidden" name="po_item_id[]" value="${itemId}">
                </td>
                <td>
                    <select name="change_type[]" class="form-control change-type" required>
                        <option value="">Select Type</option>
                        <option value="quantity_change">Quantity Change</option>
                        <option value="price_change">Price Change</option>
                        <option value="spec_change">Specification Change</option>
                        <option value="description_change">Description Change</option>
                        <option value="delivery_change">Delivery Change</option>
                        <option value="other">Other</option>
                    </select>
                </td>
                <td>
                    <select name="change_field[]" class="form-control change-field" required>
                        <option value="">Select Field</option>
                        <option value="quantity" data-original="${quantity}">Quantity</option>
                        <option value="unit_price" data-original="${price}">Unit Price</option>
                        <option value="description" data-original="${escapeHtml(description)}">Description</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="old_value[]" class="form-control old-value" readonly>
                </td>
                <td>
                    <input type="text" name="new_value[]" class="form-control new-value" required>
                </td>
                <td>
                    <input type="number" name="change_amount[]" class="form-control change-amount" 
                           value="0" step="0.01" required>
                </td>
                <td>
                    <input type="text" name="change_description[]" class="form-control" 
                           placeholder="Enter change description">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-change" 
                            data-row="${changeCounter}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

                $('#amendment_changes_body').append(newRow);

                // Disable add button for this item
                $(this).prop('disabled', true).html('<i class="fa fa-check"></i> Added');
            });

            // Remove change row
            $(document).off('click', '.remove-change').on('click', '.remove-change', function() {
                const rowId = $(this).data('row');
                const row = $(`#change_row_${rowId}`);
                const itemId = row.find('input[name="po_item_id[]"]').val();

                // Re-enable add button for this item
                $(`#po_item_${itemId} .add-to-amendment`).prop('disabled', false).html('<i class="fa fa-plus"></i> Add Change');

                // Remove from total
                const changeAmount = parseFloat(row.find('.change-amount').val()) || 0;
                totalAmendmentValue -= changeAmount;
                $('#amendment_value').val(totalAmendmentValue.toFixed(2));

                row.remove();
            });

            // Handle change type and field selection
            $(document).off('change', '.change-type, .change-field').on('change', '.change-type, .change-field', function() {
                const row = $(this).closest('tr');
                const changeField = row.find('.change-field option:selected');
                const originalValue = changeField.data('original');

                if (originalValue !== undefined) {
                    row.find('.old-value').val(originalValue);

                    // Set placeholder based on field
                    if (changeField.val() === 'quantity') {
                        row.find('.new-value').attr('placeholder', 'Enter new quantity');
                    } else if (changeField.val() === 'unit_price') {
                        row.find('.new-value').attr('placeholder', 'Enter new unit price');
                    } else if (changeField.val() === 'description') {
                        row.find('.new-value').attr('placeholder', 'Enter new description');
                    }
                }
            });

            // Calculate change amount when new value changes
            $(document).off('change', '.new-value').on('change', '.new-value', function() {
                calculateChangeAmount($(this));
            });
        }

        // Function to calculate change amount
        function calculateChangeAmount(inputElement) {
            const row = inputElement.closest('tr');
            const changeType = row.find('.change-type').val();
            const changeField = row.find('.change-field').val();
            const oldValue = parseFloat(row.find('.old-value').val()) || row.find('.old-value').val();
            const newValue = parseFloat(inputElement.val()) || inputElement.val();

            let changeAmount = 0;

            if (changeField === 'quantity') {
                const unitPrice = parseFloat(row.find('.change-field option[value="unit_price"]').data('original')) || 0;
                const oldQty = parseFloat(oldValue) || 0;
                const newQty = parseFloat(newValue) || 0;
                changeAmount = (newQty - oldQty) * unitPrice;
            } else if (changeField === 'unit_price') {
                const quantity = parseFloat(row.find('.change-field option[value="quantity"]').data('original')) || 0;
                const oldPrice = parseFloat(oldValue) || 0;
                const newPrice = parseFloat(newValue) || 0;
                changeAmount = (newPrice - oldPrice) * quantity;
            } else {
                // For description or other changes, default to 0
                changeAmount = 0;
            }

            row.find('.change-amount').val(changeAmount.toFixed(2));

            // Update total amendment value
            const previousAmount = parseFloat(row.data('change-amount')) || 0;
            totalAmendmentValue = totalAmendmentValue - previousAmount + changeAmount;
            $('#amendment_value').val(totalAmendmentValue.toFixed(2));

            // Store current change amount in row data
            row.data('change-amount', changeAmount);
        }

        // Initialize when document is ready
        $(document).ready(function() {
            // Check if PO is already selected from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const poId = urlParams.get('po_id');

            if (poId) {
                // Load PO items immediately if PO ID is in URL
                loadPOItems(poId);

                // Set the dropdown value
                $('#po_id').val(poId).trigger('change');
            }

            // Initialize event handlers
            initializeEventHandlers();

            // Form validation before submit
            // $('form').on('submit', function(e) {
            //     const changeRows = $('#amendment_changes_body tr').length;
            //     if (changeRows === 0) {
            //         e.preventDefault();
            //         alert('Please add at least one amendment change.');
            //         return false;
            //     }
            // });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Bind change event for PO selection
            $('#po_id').on('change', function() {
                loadPOItems(this.value);
            });
        });
    </script>
</body>