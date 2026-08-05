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
                    Stock Management
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>">Stock Summary</a></li>
                    <li class="active">Stock Verification</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Stock Verification</h3>
                                <a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>" class="btn btn-primary pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to Stock Summary
                                </a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if (validation_errors()): ?>
                                    <div role="alert" class="alert alert-danger">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Error!</strong> <?php echo validation_errors(); ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="<?php echo base_url('MaterialIssueController/stock_verification') ?>" id="verificationForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Verification Date *</label>
                                                <input type="date" name="verification_date" class="form-control"
                                                    value="<?= date('Y-m-d') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea name="remarks" class="form-control" rows="1"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <h4>Verify Stock Items</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="verificationTable">
                                            <thead>
                                                <tr>
                                                    <th width="25%">Item</th>
                                                    <th width="15%">System Stock</th>
                                                    <th width="15%">Physical Stock *</th>
                                                    <th width="15%">Variance</th>
                                                    <th width="15%">Unit Price</th>
                                                    <th width="15%">Variance Value</th>
                                                </tr>
                                            </thead>
                                            <tbody id="verificationBody">
                                                <?php if (!empty($inventory_items)): ?>
                                                    <?php $i = 0; ?>
                                                    <?php foreach ($inventory_items as $item): ?>
                                                        <?php if ($item['stock'] > 0): ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?= $item['code'] ?></strong> - <?= $item['item_name'] ?>
                                                                    <input type="hidden" name="inventory_id[]" value="<?= $item['inventory_id'] ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="system_stock[]"
                                                                        class="form-control system-stock"
                                                                        value="<?= $item['stock'] ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="physical_stock[]"
                                                                        class="form-control physical-stock"
                                                                        data-index="<?= $i ?>"
                                                                        value="<?= $item['stock'] ?>"
                                                                        step="0.01" min="0" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="variance[]"
                                                                        class="form-control variance"
                                                                        data-index="<?= $i ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="unit_price[]"
                                                                        class="form-control unit-price"
                                                                        value="<?= $item['sell_price'] ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="variance_value[]"
                                                                        class="form-control variance-value"
                                                                        data-index="<?= $i ?>" readonly>
                                                                </td>
                                                            </tr>
                                                            <?php $i++; ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">No items with stock found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-right">Total Variance:</th>
                                                    <th id="totalVariance">0.00</th>
                                                    <th colspan="2" id="totalVarianceValue">0.00</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="alert alert-info">
                                        <strong>Note:</strong>
                                        <ul>
                                            <li>Positive variance means physical stock is more than system stock (stock gain)</li>
                                            <li>Negative variance means physical stock is less than system stock (stock loss)</li>
                                            <li>Variance value = Variance × Unit Price</li>
                                        </ul>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> Save Verification
                                        </button>
                                        <a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>" class="btn btn-default">
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
        $(document).ready(function() {
            // Calculate variance on physical stock change
            $(document).on('change keyup', '.physical-stock', function() {
                const index = $(this).data('index');
                const systemStock = parseFloat($(this).closest('tr').find('.system-stock').val());
                const physicalStock = parseFloat($(this).val()) || 0;
                const unitPrice = parseFloat($(this).closest('tr').find('.unit-price').val());

                // Calculate variance
                const variance = physicalStock - systemStock;
                const varianceValue = variance * unitPrice;

                // Update variance fields
                $(this).closest('tr').find('.variance').val(variance.toFixed(2));
                $(this).closest('tr').find('.variance-value').val(varianceValue.toFixed(2));

                // Update row color based on variance
                if (variance > 0) {
                    $(this).closest('tr').addClass('success').removeClass('danger warning');
                } else if (variance < 0) {
                    $(this).closest('tr').addClass('danger').removeClass('success warning');
                } else {
                    $(this).closest('tr').addClass('warning').removeClass('success danger');
                }

                // Calculate totals
                calculateTotals();
            });

            // Initialize variance calculations
            $('.physical-stock').trigger('change');

            function calculateTotals() {
                let totalVariance = 0;
                let totalVarianceValue = 0;

                $('.variance').each(function() {
                    totalVariance += parseFloat($(this).val()) || 0;
                });

                $('.variance-value').each(function() {
                    totalVarianceValue += parseFloat($(this).val()) || 0;
                });

                $('#totalVariance').text(totalVariance.toFixed(2));
                $('#totalVarianceValue').text(totalVarianceValue.toFixed(2));
            }

            // Form validation
            $('#verificationForm').submit(function(e) {
                let hasVariance = false;
                $('.variance').each(function() {
                    if (parseFloat($(this).val()) !== 0) {
                        hasVariance = true;
                        return false; // break loop
                    }
                });

                if (!hasVariance) {
                    if (!confirm('No variance detected. Are you sure you want to save?')) {
                        e.preventDefault();
                    }
                }

                return true;
            });
        });
    </script>

    <style>
        .success {
            background-color: #dff0d8 !important;
        }

        .danger {
            background-color: #f2dede !important;
        }

        .warning {
            background-color: #fcf8e3 !important;
        }
    </style>
</body>