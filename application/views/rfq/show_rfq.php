<?php
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
                    RFQ Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?= base_url('RFQController/index') ?>">RFQ</a></li>
                    <li class="active">RFQ Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">RFQ Details - <?= isset($rfq['rfq_no']) ? $rfq['rfq_no'] : 'N/A' ?></h3>

                                <div class="pull-right">
                                    <?php if (!empty($quotations) && count($quotations) > 1): ?>
                                        <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#comparisonModal">
                                            <i class="fa fa-chart-bar"></i> Compare All Quotations
                                        </button>
                                    <?php endif; ?>
                                    <a href="<?= base_url('RFQController/index') ?>" class="btn btn-success">
                                        <i class="fa fa-arrow-left"></i> Back to RFQ List
                                    </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- RFQ INFO -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <strong><i class="fa fa-calendar"></i> Date:</strong>
                                        <?= date('d-m-Y', strtotime($rfq['rfq_date'])) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fa fa-info-circle"></i> Status:</strong>
                                        <span class="label label-<?= $rfq['status'] == 'Pending' ? 'warning' : ($rfq['status'] == 'Sent' ? 'info' : 'success') ?>">
                                            <?= $rfq['status'] ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fa fa-user"></i> Created By:</strong>
                                        <?= isset($rfq['created_by_name']) ? $rfq['created_by_name'] : $rfq['created_by'] ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fa info-circle"></i> PR No:</strong>
                                        <?= $rfq['pr_no'] ?>
                                    </div>
                                </div>

                                <hr>

                                <!-- ITEMS -->
                                <!-- <h4><i class="fa fa-list"></i> RFQ Items</h4>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Item Code</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($items)): ?>
                                                <?php foreach ($items as $index => $item): ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td><strong><?= $item['item_code'] ?></strong></td>
                                                        <td><?= strip_tags($item['description']) ?></td>
                                                        <td><?= $item['quantity'] ?></td>
                                                        <td><?= $item['unit'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="fa fa-exclamation-circle fa-2x mb-2"></i><br>
                                                        No items found.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div> -->

                                <hr>

                                <!-- ADD VENDOR QUOTATION FORM -->
                                <h4><i class="fa fa-plus-circle"></i> Add Vendor Quotation</h4>

                                <form action="<?= base_url('RFQController/saveQuotation') ?>" method="POST" id="quotationForm">
                                    <input type="hidden" name="rfq_id" value="<?= $rfq['rfq_id'] ?>">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"><i class="fa fa-truck"></i> Vendor Name *</label>
                                                <select name="supplier_id" class="form-control" required>
                                                    <option value="">Select Vendor</option>
                                                    <?php foreach ($suppliers as $s): ?>
                                                        <option value="<?= $s['supplier_id'] ?>"><?= $s['supplier_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"><i class="fa fa-calendar-alt"></i> Quote Date *</label>
                                                <input type="text" class="form-control datepicker" name="quote_date" required>

                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"><i class="fa fa-sticky-note"></i> Remarks</label>
                                                <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
                                            </div>
                                        </div>
                                    </div>

                                    <br>

                                    <!-- ITEM-WISE PRICING TABLE WITH GST -->
                                    <h5><i class="fa fa-calculator"></i> Item-wise Quotation</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Item Code</th>
                                                    <th>Description</th>
                                                    <th>Quantity</th>
                                                    <th>Unit</th>
                                                    <th>Unit Price *</th>
                                                    <th>GST %</th>
                                                    <th>Total Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($items)): ?>
                                                    <?php foreach ($items as $item): ?>
                                                        <tr>
                                                            <td><strong><?= $item['item_code'] ?></strong></td>
                                                            <td><?= strip_tags($item['description']) ?></td>
                                                            <td><?= $item['quantity'] ?></td>
                                                            <td><?= $item['unit'] ?></td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="item_prices[<?= $item['rfq_item_id'] ?>]"
                                                                    class="form-control unit-price"
                                                                    data-quantity="<?= $item['quantity'] ?>"
                                                                    data-itemid="<?= $item['rfq_item_id'] ?>"
                                                                    required
                                                                    placeholder="0.00"
                                                                    min="0">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="item_gst[<?= $item['rfq_item_id'] ?>]"
                                                                    class="form-control gst-percentage"
                                                                    value="18.00"
                                                                    placeholder="GST %"
                                                                    min="0" max="100">
                                                            </td>
                                                            <td class="item-total text-right" id="total_<?= $item['rfq_item_id'] ?>">
                                                                <strong>0.00</strong>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-4">
                                                            No items available for quotation.
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="6" class="text-right"><strong>Sub Total:</strong></td>
                                                    <td class="text-right"><strong id="subTotal">0.00</strong></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="text-right"><strong>GST Total:</strong></td>
                                                    <td class="text-right"><strong id="gstTotal">0.00</strong></td>
                                                </tr>
                                                <tr class="success">
                                                    <td colspan="6" class="text-right"><strong>Grand Total:</strong></td>
                                                    <td class="text-right"><strong id="grandTotal">0.00</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <br>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-paper-plane"></i> Submit Quotation
                                        </button>
                                        <button type="reset" class="btn btn-default btn-lg">
                                            <i class="fa fa-refresh"></i> Reset
                                        </button>
                                    </div>
                                </form>

                                <hr>

                                <!-- QUOTATIONS LIST -->
                                <h4><i class="fa fa-file-invoice-dollar"></i> Vendor Quotations</h4>

                                <?php if (empty($quotations)): ?>
                                    <div class="alert alert-info text-center">
                                        <i class="fa fa-info-circle fa-2x mb-3"></i><br>
                                        <h5>No quotations received yet.</h5>
                                        <p class="mb-0">Submit the first quotation using the form above.</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Compare Selected Section -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="alert" id="compareSection" style="display: none; background-color: #e8f4fd; border-color: #bee5eb; color: #0c5460;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong><i class="fa fa-chart-line"></i> Compare Selected Quotations</strong>
                                                        <span id="selectedCount" class="badge badge-info ml-2">0 selected</span>
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <button type="button" class="btn btn-info btn-sm" id="compareSelected">
                                                            <i class="fa fa-balance-scale"></i> Compare Selected (Max 3)
                                                        </button>
                                                        <button type="button" class="btn btn-default btn-sm" id="clearSelection">
                                                            <i class="fa fa-times"></i> Clear
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Vendor</th>
                                                    <th>Total Amount</th>
                                                    <th>GST Amount</th>
                                                    <th>Final Amount</th>
                                                    <th>Quote Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                    <th>Compare</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $rank = ['L1', 'L2', 'L3', 'L4', 'L5', 'L6', 'L7', 'L8', 'L9', 'L10'];
                                                $i = 0;
                                                ?>
                                                <?php foreach ($quotations as $q): ?>
                                                    <tr class="<?=
                                                                $i == 0 ? 'success' : ($i == 1 ? 'warning' : ($i == 2 ? 'info' : ''))
                                                                ?>">
                                                        <td>
                                                            <span class="label <?=
                                                                                $i == 0 ? 'label-success' : ($i == 1 ? 'label-warning' : ($i == 2 ? 'label-info' : 'label-default'))
                                                                                ?>">
                                                                <?= $rank[$i] ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong><?= $q['supplier_name'] ?></strong>
                                                            <span class="label label-primary" style="font-size: 10px; margin-left: 5px;">R<?= $q['revision_no'] ?? 0 ?></span>
                                                            <?php if (!empty($q['contact_person'])): ?>
                                                                <br><small class="text-muted">Contact: <?= $q['contact_person'] ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-right">₹<?= number_format($q['total_amount'], 2) ?></td>
                                                        <td class="text-right">₹<?= number_format($q['gst_amount'], 2) ?></td>
                                                        <td class="text-right"><strong>₹<?= number_format($q['final_amount'], 2) ?></strong></td>
                                                        <td><?= date('d-m-Y', strtotime($q['quote_date'])) ?></td>
                                                        <td>
                                                            <span class="label label-<?=
                                                                                        $q['status'] == 'approved' ? 'success' : ($q['status'] == 'rejected' ? 'danger' : 'warning')
                                                                                        ?>">
                                                                <?= ucfirst($q['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-sm view-quotation"
                                                                data-toggle="modal" data-target="#quotationModal"
                                                                data-vendor="<?= $q['supplier_name'] ?>"
                                                                data-date="<?= date('d-m-Y', strtotime($q['quote_date'])) ?>"
                                                                data-total="<?= number_format($q['final_amount'], 2) ?>"
                                                                data-remarks="<?= $q['remarks'] ?>"
                                                                data-items='<?= json_encode($q['items'] ?? []) ?>'>
                                                                <i class="fa fa-eye"></i> Details
                                                            </button>

                                                            <?php if (isset($q['revision_no']) && $q['revision_no'] > 0): ?>
                                                                <button type="button" class="btn btn-default btn-sm view-revision-history"
                                                                    data-rfq-id="<?= $rfq['rfq_id'] ?>"
                                                                    data-supplier-id="<?= $q['supplier_id'] ?>"
                                                                    data-vendor-name="<?= $q['supplier_name'] ?>">
                                                                    <i class="fa fa-history"></i> History
                                                                </button>
                                                            <?php endif; ?>

                                                            <!-- Convert to PO Form -->
                                                            <form action="<?= base_url('SupplierController/convert_rfq_to_po') ?>" method="POST" style="display: inline;"
                                                                onsubmit="return confirm('Convert RFQ to Purchase Order for <?= $q['supplier_name'] ?>?\\nTotal Amount: ₹<?= number_format($q['final_amount'], 2) ?>\\n\\nThis will create a PO that requires approval.');">

                                                                <input type="hidden" name="rfq_id" value="<?= $rfq['rfq_id'] ?>">
                                                                <input type="hidden" name="quotation_id" value="<?= $q['quotation_id'] ?>">
                                                                <input type="hidden" name="vendor_id" value="<?= $q['supplier_id'] ?>">
                                                                <button type="submit" class="btn btn-success btn-sm">
                                                                    <i class="fa fa-file-invoice"></i> Convert to PO
                                                                </button>
                                                            </form>
                                                            <a href="<?= base_url('RFQController/deleteQuotation/' . $q['quotation_id'] . '/' . $rfq['rfq_id']) ?>"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to delete this quotation?')">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="compare-checkbox"
                                                                value="<?= $q['quotation_id'] ?>"
                                                                data-vendor="<?= $q['supplier_name'] ?>"
                                                                data-total="<?= $q['final_amount'] ?>"
                                                                style="transform: scale(1.5);">
                                                        </td>
                                                    </tr>
                                                <?php $i++;
                                                endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>

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

    <!-- Quotation Details Modal -->
    <div class="modal fade" id="quotationModal" tabindex="-1" aria-labelledby="quotationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="quotationModalLabel">
                        <i class="fa fa-file-invoice-dollar"></i> Quotation Details
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Vendor:</strong> <span id="modalVendor" class="font-weight-bold"></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Quote Date:</strong> <span id="modalDate"></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Amount:</strong> <span id="modalTotal" class="font-weight-bold text-success"></span>
                        </div>
                    </div>
                    <div class="row mb-3" id="modalRemarksRow" style="display: none;">
                        <div class="col-md-12">
                            <strong>Remarks:</strong> <span id="modalRemarks" class="font-italic"></span>
                        </div>
                    </div>
                    <hr>
                    <h5>Item-wise Pricing</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="modalItemsTable">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>GST %</th>
                                    <th>GST Amount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="modalItemsBody">
                                <!-- Items will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Modal -->
    <div class="modal fade" id="comparisonModal" tabindex="-1" aria-labelledby="comparisonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 95%; width: 95%;">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="comparisonModalLabel">
                        <i class="fa fa-balance-scale"></i> Quotation Comparison
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="comparisonContent">
                        <!-- Comparison content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="printComparison">
                        <i class="fa fa-print"></i> Print Comparison
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Revision History Modal -->
    <div class="modal fade" id="revisionHistoryModal" tabindex="-1" aria-labelledby="revisionHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy text-white" style="background-color: #001f3f; color: #fff;">
                    <h5 class="modal-title" id="revisionHistoryModalLabel" style="display: inline-block;">
                        <i class="fa fa-history"></i> Revision History - <span id="historyVendorName"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="revisionHistoryContent">
                    <!-- Revision History will be loaded dynamically via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Datepicker JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <style>
        .lowest-price {
            background-color: #d4edda !important;
        }

        .highest-price {
            background-color: #f8d7da !important;
        }

        .comparison-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .unit-price:valid {
            border-color: #28a745;
        }

        .unit-price:invalid {
            border-color: #dc3545;
        }

        .datepicker {
            z-index: 1151 !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Date picker initialization
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true
            });

            // Calculate item total and grand total
            $('.unit-price, .gst-percentage').on('input', function() {
                calculateTotals();
            });

            // Reset form handler
            $('button[type="reset"]').on('click', function() {
                setTimeout(function() {
                    $('.item-total strong').text('0.00');
                    $('#subTotal').text('0.00');
                    $('#gstTotal').text('0.00');
                    $('#grandTotal').text('0.00');
                }, 100);
            });

            // Form submission validation
            $('#quotationForm').on('submit', function() {
                var grandTotal = parseFloat($('#grandTotal').text());
                if (grandTotal <= 0) {
                    alert('Please enter valid unit prices for all items.');
                    return false;
                }
                return true;
            });

            // Quotation modal handler
            $('.view-quotation').on('click', function() {
                var vendor = $(this).data('vendor');
                var date = $(this).data('date');
                var total = $(this).data('total');
                var remarks = $(this).data('remarks');
                var items = $(this).data('items');

                $('#modalVendor').text(vendor);
                $('#modalDate').text(date);
                $('#modalTotal').text('₹' + total);

                // Show/hide remarks
                if (remarks && remarks.trim() !== '') {
                    $('#modalRemarks').text(remarks);
                    $('#modalRemarksRow').show();
                } else {
                    $('#modalRemarksRow').hide();
                }

                // Populate items table
                var itemsBody = $('#modalItemsBody');
                itemsBody.empty();

                if (items && items.length > 0) {
                    items.forEach(function(item) {
                        var row = '<tr>' +
                            '<td>' + (item.item_code || '') + '</td>' +
                            '<td>' + (item.description || '') + '</td>' +
                            '<td class="text-right">' + (item.quantity || '') + '</td>' +
                            '<td class="text-right">₹' + (item.unit_price ? parseFloat(item.unit_price).toFixed(2) : '0.00') + '</td>' +
                            '<td class="text-right">' + (item.gst_percentage ? parseFloat(item.gst_percentage).toFixed(2) + '%' : '0%') + '</td>' +
                            '<td class="text-right">₹' + (item.gst_amount ? parseFloat(item.gst_amount).toFixed(2) : '0.00') + '</td>' +
                            '<td class="text-right font-weight-bold">₹' + (item.total_amount ? parseFloat(item.total_amount).toFixed(2) : '0.00') + '</td>' +
                            '</tr>';
                        itemsBody.append(row);
                    });
                } else {
                    itemsBody.append('<tr><td colspan="7" class="text-center text-muted">No items found</td></tr>');
                }
            });

            // Comparison functionality
            var selectedQuotations = [];

            $('.compare-checkbox').on('change', function() {
                var quotationId = $(this).val();
                var vendorName = $(this).data('vendor');
                var totalAmount = $(this).data('total');

                if ($(this).is(':checked')) {
                    if (selectedQuotations.length >= 3) {
                        $(this).prop('checked', false);
                        alert('You can compare maximum 3 quotations at a time.');
                        return;
                    }
                    selectedQuotations.push({
                        id: quotationId,
                        vendor: vendorName,
                        total: totalAmount
                    });
                } else {
                    selectedQuotations = selectedQuotations.filter(function(q) {
                        return q.id != quotationId;
                    });
                }

                updateCompareSection();
            });

            // Clear selection
            $('#clearSelection').on('click', function() {
                $('.compare-checkbox').prop('checked', false);
                selectedQuotations = [];
                updateCompareSection();
            });

            function updateCompareSection() {
                var count = selectedQuotations.length;
                if (count > 0) {
                    $('#compareSection').show();
                    $('#selectedCount').text(count + ' quotation(s) selected');
                    $('#compareSelected').text('Compare Selected (' + count + ')');
                } else {
                    $('#compareSection').hide();
                }
            }

            // Compare selected quotations
            $('#compareSelected').on('click', function() {
                if (selectedQuotations.length < 2) {
                    alert('Please select at least 2 quotations to compare.');
                    return;
                }

                // Load comparison data via AJAX
                loadComparisonData(selectedQuotations);
            });

            // Load all quotations comparison
            $('#comparisonModal').on('show.bs.modal', function() {
                loadAllComparison();
            });

            function loadComparisonData(quotations) {
                $('#comparisonModal').modal('show');

                // Show loading
                $('#comparisonContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i><br><p>Loading comparison data...</p></div>');

                // AJAX call to get detailed comparison data
                $.ajax({
                    url: '<?= base_url('RFQController/getQuotationComparison') ?>',
                    type: 'POST',
                    data: {
                        rfq_id: '<?= $rfq['rfq_id'] ?>',
                        quotation_ids: quotations.map(function(q) {
                            return q.id;
                        })
                    },
                    success: function(response) {
                        $('#comparisonContent').html(response);
                    },
                    error: function() {
                        $('#comparisonContent').html('<div class="alert alert-danger text-center">Error loading comparison data.</div>');
                    }
                });
            }

            function loadAllComparison() {
                $('#comparisonContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i><br><p>Loading all quotations comparison...</p></div>');

                // AJAX call to get all quotations comparison
                $.ajax({
                    url: '<?= base_url('RFQController/getAllQuotationComparison') ?>',
                    type: 'POST',
                    data: {
                        rfq_id: '<?= $rfq['rfq_id'] ?>'
                    },
                    success: function(response) {
                        $('#comparisonContent').html(response);
                    },
                    error: function() {
                        $('#comparisonContent').html('<div class="alert alert-danger text-center">Error loading comparison data.</div>');
                    }
                });
            }

            // Print comparison
            $('#printComparison').on('click', function() {
                var printContent = $('#comparisonContent').html();
                var originalContent = document.body.innerHTML;

                document.body.innerHTML =
                    '<html><head><title>Quotation Comparison - RFQ <?= $rfq['rfq_no'] ?></title>' +
                    '<link rel="stylesheet" href="<?= base_url() ?>bower_components/bootstrap/dist/css/bootstrap.min.css">' +
                    '<style>@media print { .no-print { display: none; } body { padding: 20px; } }</style></head><body>' +
                    '<div class="container">' + printContent + '</div></body></html>';

                window.print();
                document.body.innerHTML = originalContent;
                location.reload();
            });

            function calculateTotals() {
                var subTotal = 0;
                var gstTotal = 0;
                var grandTotal = 0;

                $('.unit-price').each(function() {
                    var unitPrice = parseFloat($(this).val()) || 0;
                    var quantity = parseFloat($(this).data('quantity')) || 0;
                    var itemId = $(this).data('itemid');
                    var gstPercentage = parseFloat($(this).closest('tr').find('.gst-percentage').val()) || 0;

                    var itemSubTotal = unitPrice * quantity;
                    var itemGst = (itemSubTotal * gstPercentage) / 100;
                    var itemTotal = itemSubTotal + itemGst;

                    // Update item total
                    $('#total_' + itemId + ' strong').text(itemTotal.toFixed(2));

                    subTotal += itemSubTotal;
                    gstTotal += itemGst;
                    grandTotal += itemTotal;
                });

                // Update totals
                $('#subTotal').text(subTotal.toFixed(2));
                $('#gstTotal').text(gstTotal.toFixed(2));
                $('#grandTotal').text(grandTotal.toFixed(2));
            }

            // Initialize calculations on page load
            calculateTotals();
        });

        // Add this JavaScript to your RFQ view
        $(document).ready(function() {
            // View Revision History button click
            $(document).on('click', '.view-revision-history', function() {
                var rfqId = $(this).data('rfq-id');
                var supplierId = $(this).data('supplier-id');
                var vendorName = $(this).data('vendor-name');

                $('#historyVendorName').text(vendorName);
                $('#revisionHistoryContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i><br><p>Loading revision history...</p></div>');
                $('#revisionHistoryModal').modal('show');

                $.ajax({
                    url: '<?= base_url("RFQController/getRevisionHistory") ?>',
                    type: 'POST',
                    data: {
                        rfq_id: rfqId,
                        supplier_id: supplierId,
                        <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
                    },
                    success: function(response) {
                        $('#revisionHistoryContent').html(response);
                    },
                    error: function() {
                        $('#revisionHistoryContent').html('<div class="alert alert-danger text-center">Error loading revision history.</div>');
                    }
                });
            });

            // Convert to PO button click
            $(document).on('click', '.convert-to-po-btn', function() {
                var rfqId = $(this).data('rfq-id');
                var quotationId = $(this).data('quotation-id');
                var vendorId = $(this).data('vendor-id');
                var vendorName = $(this).data('vendor-name');
                var totalAmount = $(this).data('total-amount');

                // Show confirmation modal
                if (confirm('Convert RFQ to Purchase Order for ' + vendorName + '?\nTotal Amount: ₹' + parseFloat(totalAmount).toLocaleString('en-IN', {
                        minimumFractionDigits: 2
                    }) + '\n\nThis will create a PO that requires approval.')) {
                    // Submit conversion request
                    $.ajax({
                        url: '<?= base_url("SupplierController/convert_rfq_to_po") ?>',
                        type: 'POST',
                        data: {
                            rfq_id: rfqId,
                            quotation_id: quotationId,
                            vendor_id: vendorId,
                            <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
                        },
                        success: function(response) {

                            alert(JSON.stringify(response));
                            window.location.href = '<?= base_url("SupplierController/view_purchase_order") ?>';
                        },
                        error: function() {
                            alert('Error converting RFQ to PO. Please try again.');
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>