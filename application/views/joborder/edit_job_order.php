<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Job Order</title>
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/admin-lte/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/admin-lte/dist/css/skins/skin-blue.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/select2/dist/css/select2.min.css">
    <style>
        .required-field::after {
            content: " *";
            color: red;
        }
        .job-item td {
            vertical-align: middle;
            padding: 8px 4px;
        }
        .job-item input {
            min-width: 80px;
        }
        .table-header {
            background-color: #3c8dbc;
            color: white;
        }
        .table-header th {
            font-weight: 600;
            font-size: 13px;
            vertical-align: middle;
            text-align: center;
        }
        .totals-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .totals-section .form-group {
            margin-bottom: 10px;
        }
        .totals-section input[readonly] {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        #loader {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        #loader.center {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3c8dbc;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        .alert {
            border-radius: 3px;
            margin-bottom: 20px;
        }
        .box {
            border-top: 3px solid #3c8dbc;
        }
        .form-control:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 5px rgba(60, 141, 188, 0.3);
        }
        .btn-group-action {
            white-space: nowrap;
        }
        .btn-action {
            margin-right: 5px;
        }
        @media (max-width: 768px) {
            .job-item td {
                display: block;
                width: 100%;
            }
            .job-item td:before {
                content: attr(data-label);
                font-weight: bold;
                display: inline-block;
                width: 120px;
            }
            .table-responsive {
                border: none;
            }
        }
        .summary-card {
            background: #f9f9f9;
            border-left: 4px solid #3c8dbc;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .summary-item.total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .panel-heading {
            background-color: #3c8dbc !important;
            color: white !important;
        }
        .select2-container--default .select2-selection--single {
            height: 34px;
            border-radius: 3px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
        }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <h1>
                    Edit Job Order
                    <small>Update Job Order</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url(); ?>JobOrderController/index">Job Orders</a></li>
                    <li class="active">Edit Job Order</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Alert Messages -->
                        <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-check"></i> Success!</h4>
                                <?= $this->session->flashdata('SUCCESSMSG') ?>
                            </div>
                        <?php } ?>

                        <?php if ($this->session->flashdata('INFOMSG')) { ?>
                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-info"></i> Information!</h4>
                                <?= $this->session->flashdata('INFOMSG') ?>
                            </div>
                        <?php } ?>

                        <?php if ($this->session->flashdata('ERRMSG')) { ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                <?= $this->session->flashdata('ERRMSG') ?>
                            </div>
                        <?php } ?>

                        <!-- Main Box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-edit"></i> Edit Job Order #<?php echo isset($joborder['number_fk']) ? $joborder['number_fk'] : 'N/A'; ?></h3>
                                <div class="box-tools pull-right">
                                    <span class="label label-info">Status: <?php 
                                        $status = isset($joborder['status']) ? $joborder['status'] : '';
                                        $status_badge = '';
                                        switch($status) {
                                            case 1: $status_badge = 'Draft'; break;
                                            case 2: $status_badge = 'Sent'; break;
                                            case 3: $status_badge = 'Accepted'; break;
                                            case 4: $status_badge = 'Rejected'; break;
                                            case 5: $status_badge = 'Cancelled'; break;
                                            default: $status_badge = 'Unknown';
                                        }
                                        echo $status_badge;
                                    ?></span>
                                </div>
                            </div>
                            
                            <!-- Form Start -->
                            <form role="form" action="<?php echo base_url(); ?>JobOrderController/update_job_order" method="POST" enctype="multipart/form-data" id="jobOrderForm">
                                <div class="box-body">
                                    <!-- Job Order Information -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Job Order Number</label>
                                                <input type="text" class="form-control" name="number" value="<?php echo isset($joborder['number_fk']) ? $joborder['number_fk'] : ''; ?>" readonly>
                                                <p class="help-block">Auto-generated job order number</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="required-field">Date</label>
                                                <input type="date" class="form-control" name="date" value="<?php echo isset($joborder['date']) ? date('Y-m-d', strtotime($joborder['date'])) : date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="required-field">Customer</label>
                                                <select class="form-control select2" name="customer_id" style="width: 100%;" required>
                                                    <option value="">-- Select Customer --</option>
                                                    <?php if(isset($result) && !empty($result)) {
                                                        foreach($result as $customer) {
                                                            $selected = (isset($joborder['customer_id']) && $joborder['customer_id'] == $customer->customer_id) ? 'selected' : '';
                                                            echo '<option value="'.$customer->customer_id.'" '.$selected.'>'.htmlspecialchars($customer->company_name).'</option>';
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Due Date</label>
                                                <input type="date" class="form-control" name="due_date" value="<?php echo isset($joborder['due_date']) && !empty($joborder['due_date']) ? date('Y-m-d', strtotime($joborder['due_date'])) : ''; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Payment Terms</label>
                                                <select class="form-control" name="payment_terms">
                                                    <option value="">-- Select Payment Terms --</option>
                                                    <option value="Net 15" <?php echo (isset($joborder['payment_terms']) && $joborder['payment_terms'] == 'Net 15') ? 'selected' : ''; ?>>Net 15</option>
                                                    <option value="Net 30" <?php echo (isset($joborder['payment_terms']) && $joborder['payment_terms'] == 'Net 30') ? 'selected' : ''; ?>>Net 30</option>
                                                    <option value="Net 45" <?php echo (isset($joborder['payment_terms']) && $joborder['payment_terms'] == 'Net 45') ? 'selected' : ''; ?>>Net 45</option>
                                                    <option value="Net 60" <?php echo (isset($joborder['payment_terms']) && $joborder['payment_terms'] == 'Net 60') ? 'selected' : ''; ?>>Net 60</option>
                                                    <option value="Due on Receipt" <?php echo (isset($joborder['payment_terms']) && $joborder['payment_terms'] == 'Due on Receipt') ? 'selected' : ''; ?>>Due on Receipt</option>
                                                </select>
                                            </div>
                                        </div>
                                         <?php if ($_has_project_master): ?>
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label>Project Code</label>
                                                 <input type="text" class="form-control" name="project_code" value="<?php echo isset($joborder['project_code']) ? $joborder['project_code'] : ''; ?>" placeholder="Enter project code">
                                             </div>
                                         </div>
                                         <?php endif; ?>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea class="form-control" name="remarks" rows="3" placeholder="Enter any remarks or special instructions..."><?php echo isset($joborder['remarks']) ? $joborder['remarks'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select class="form-control" name="status" required>
                                                    <option value="1" <?php echo (isset($joborder['status']) && $joborder['status'] == 1) ? 'selected' : ''; ?>>Draft</option>
                                                    <option value="2" <?php echo (isset($joborder['status']) && $joborder['status'] == 2) ? 'selected' : ''; ?>>Sent</option>
                                                    <option value="3" <?php echo (isset($joborder['status']) && $joborder['status'] == 3) ? 'selected' : ''; ?>>Accepted</option>
                                                    <option value="4" <?php echo (isset($joborder['status']) && $joborder['status'] == 4) ? 'selected' : ''; ?>>Rejected</option>
                                                    <option value="5" <?php echo (isset($joborder['status']) && $joborder['status'] == 5) ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Job Details Section -->
                                    <div class="panel panel-default" style="margin-top: 30px;">
                                        <div class="panel-heading">
                                            <h3 class="panel-title"><i class="fa fa-list"></i> Job Order Line Items</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="jobOrderTable">
                                                    <thead>
                                                        <tr class="table-header">
                                                            <th width="5%">#</th>
                                                            <th width="12%">Item Code</th>
                                                            <th width="18%">Equipment</th>
                                                            <th width="8%">Qty</th>
                                                            <th width="6%">Unit</th>
                                                            <th width="10%">Tag No.</th>
                                                            <th width="13%">Scope</th>
                                                            <th width="13%">Stores Remark</th>
                                                            <th width="12%">Remark</th>
                                                            <th width="3%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        if(isset($joborder_detail) && !empty($joborder_detail)) {
                                                            $sr_no = 1;
                                                            foreach($joborder_detail as $detail) {
                                                        ?>
                                                        <tr class="job-item">
                                                            <td class="sr-no" data-label="#"><?php echo $sr_no; ?></td>
                                                            <td><input type="text" class="form-control input-sm item_code" name="item_code[]" placeholder="Item Code" value="<?php echo isset($detail['item_code']) ? htmlspecialchars($detail['item_code']) : ''; ?>"></td>
                                                            <td><input type="text" class="form-control input-sm equipment" name="equipment[]" placeholder="Equipment" value="<?php echo isset($detail['equipment']) ? htmlspecialchars($detail['equipment']) : ''; ?>"></td>
                                                            <td><input type="number" class="form-control input-sm quantity" name="quantity[]" placeholder="Qty" step="0.01" value="<?php echo isset($detail['quantity']) ? $detail['quantity'] : '1'; ?>"></td>
                                                            <td><input type="text" class="form-control input-sm unit" name="unit[]" placeholder="Unit" value="<?php echo isset($detail['unit']) ? htmlspecialchars($detail['unit']) : 'pcs'; ?>"></td>
                                                            <td><input type="text" class="form-control input-sm tag_no" name="tag_no[]" placeholder="Tag No." value="<?php echo isset($detail['tag_no']) ? htmlspecialchars($detail['tag_no']) : ''; ?>"></td>
                                                            <td><input type="text" class="form-control input-sm scope" name="scope[]" placeholder="Scope" value="<?php echo isset($detail['scope']) ? htmlspecialchars($detail['scope']) : ''; ?>"></td>
                                                            <td>
                                                                <select class="form-control input-sm stores_remark" name="stores_remark[]">
                                                                    <option value="">Select</option>
                                                                    <option value="Y" <?php echo (isset($detail['stores_remark']) && $detail['stores_remark'] == 'Y') ? 'selected' : ''; ?>>Yes (In Stock)</option>
                                                                    <option value="N" <?php echo (isset($detail['stores_remark']) && $detail['stores_remark'] == 'N') ? 'selected' : ''; ?>>No (Not in Stock)</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control input-sm remark" name="remark[]" placeholder="Remark" value="<?php echo isset($detail['remark']) ? htmlspecialchars($detail['remark']) : ''; ?>"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-row" title="Delete Row"><i class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                        <?php
                                                            $sr_no++;
                                                            }
                                                        } else {
                                                        ?>
                                                        <tr class="job-item">
                                                            <td class="sr-no" data-label="#">1</td>
                                                            <td><input type="text" class="form-control input-sm item_code" name="item_code[]" placeholder="Item Code"></td>
                                                            <td><input type="text" class="form-control input-sm equipment" name="equipment[]" placeholder="Equipment"></td>
                                                            <td><input type="number" class="form-control input-sm quantity" name="quantity[]" placeholder="Qty" step="0.01" value="1"></td>
                                                            <td><input type="text" class="form-control input-sm unit" name="unit[]" placeholder="Unit" value="pcs"></td>
                                                            <td><input type="text" class="form-control input-sm tag_no" name="tag_no[]" placeholder="Tag No."></td>
                                                            <td><input type="text" class="form-control input-sm scope" name="scope[]" placeholder="Scope"></td>
                                                            <td>
                                                                <select class="form-control input-sm stores_remark" name="stores_remark[]">
                                                                    <option value="">Select</option>
                                                                    <option value="Y">Yes (In Stock)</option>
                                                                    <option value="N">No (Not in Stock)</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control input-sm remark" name="remark[]" placeholder="Remark"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-row" title="Delete Row"><i class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                        <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="9"></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-success btn-sm" id="addRowBtn" title="Add New Row">
                                                                    <i class="fa fa-plus-circle"></i> Add
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            
                                            <div class="row" style="margin-top: 15px;">
                                                <div class="col-md-12 text-right">
                                                    <span class="text-muted">Total Items: <span id="totalItems">1</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.box-body -->

                                <div class="box-footer">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fa fa-save"></i> Update Job Order
                                        </button>
                                        <a href="<?php echo base_url(); ?>JobOrderController/show_job_order/<?php echo isset($joborder['number_fk']) ? $joborder['number_fk'] : ''; ?>" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Cancel
                                        </a>
                                    </div>
                                    <button type="reset" class="btn btn-warning pull-right" id="resetBtn">
                                        <i class="fa fa-undo"></i> Reset Form
                                    </button>
                                </div>
                            </form>
                            <!-- Form End -->
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

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2024</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo base_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url(); ?>bower_components/select2/dist/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url(); ?>bower_components/admin-lte/dist/js/adminlte.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: 'Select Customer',
                allowClear: true
            });

            // Show loader on form submit
            $('#jobOrderForm').on('submit', function() {
                $('#loader').show();
                $('#submitBtn').prop('disabled', true);
                return true;
            });

            // Save and New functionality
            $('#saveAndNewBtn').click(function() {
                $('#jobOrderForm').attr('action', '<?php echo base_url(); ?>JobOrderController/save_job_order_and_new');
                $('#jobOrderForm').submit();
            });

            // Add new row
            $('#addRowBtn').click(function() {
                var rowCount = $('.job-item').length + 1;
                var newRow = '<tr class="job-item">' +
                    '<td class="sr-no" data-label="#">' + rowCount + '</td>' +
                    '<td><input type="text" class="form-control input-sm item_code" name="item_code[]" placeholder="Item Code"></td>' +
                    '<td><input type="text" class="form-control input-sm equipment" name="equipment[]" placeholder="Equipment"></td>' +
                    '<td><input type="number" class="form-control input-sm quantity" name="quantity[]" placeholder="Qty" step="0.01" value="1"></td>' +
                    '<td><input type="text" class="form-control input-sm unit" name="unit[]" placeholder="Unit" value="pcs"></td>' +
                    '<td><input type="text" class="form-control input-sm tag_no" name="tag_no[]" placeholder="Tag No."></td>' +
                    '<td><input type="text" class="form-control input-sm scope" name="scope[]" placeholder="Scope"></td>' +
                    '<td>' +
                        '<select class="form-control input-sm stores_remark" name="stores_remark[]">' +
                            '<option value="">Select</option>' +
                            '<option value="Y">Yes (In Stock)</option>' +
                            '<option value="N">No (Not in Stock)</option>' +
                        '</select>' +
                    '</td>' +
                    '<td><input type="text" class="form-control input-sm remark" name="remark[]" placeholder="Remark"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-row" title="Delete Row"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
                $('#jobOrderTable tbody').append(newRow);
                updateSerialNumbers();
                updateTotalItems();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                if (confirm('Are you sure you want to remove this item?')) {
                    $(this).closest('tr').remove();
                    updateSerialNumbers();
                    updateTotalItems();
                }
            });

            // Update serial numbers
            function updateSerialNumbers() {
                $('.job-item').each(function(index) {
                    $(this).find('.sr-no').text(index + 1);
                });
            }

            // Update total items count
            function updateTotalItems() {
                var count = $('.job-item').length;
                $('#totalItems').text(count);
            }

            // Reset form
            $('#resetBtn').click(function(e) {
                e.preventDefault();
                if (confirm('Reset all changes? This will clear all form fields.')) {
                    $('#jobOrderForm')[0].reset();
                    // Keep only one row in the table
                    var tbody = $('#jobOrderTable tbody');
                    tbody.empty();
                    tbody.append('<tr class="job-item">' +
                        '<td class="sr-no" data-label="#">1</td>' +
                        '<td><input type="text" class="form-control input-sm item_code" name="item_code[]" placeholder="Item Code"></td>' +
                        '<td><input type="text" class="form-control input-sm equipment" name="equipment[]" placeholder="Equipment"></td>' +
                        '<td><input type="number" class="form-control input-sm quantity" name="quantity[]" placeholder="Qty" step="0.01" value="1"></td>' +
                        '<td><input type="text" class="form-control input-sm unit" name="unit[]" placeholder="Unit" value="pcs"></td>' +
                        '<td><input type="text" class="form-control input-sm tag_no" name="tag_no[]" placeholder="Tag No."></td>' +
                        '<td><input type="text" class="form-control input-sm scope" name="scope[]" placeholder="Scope"></td>' +
                        '<td>' +
                            '<select class="form-control input-sm stores_remark" name="stores_remark[]">' +
                                '<option value="">Select</option>' +
                                '<option value="Y">Yes (In Stock)</option>' +
                                '<option value="N">No (Not in Stock)</option>' +
                            '</select>' +
                        '</td>' +
                        '<td><input type="text" class="form-control input-sm remark" name="remark[]" placeholder="Remark"></td>' +
                        '<td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-row" title="Delete Row"><i class="fa fa-trash"></i></button></td>' +
                        '</tr>');
                    updateTotalItems();
                }
            });



            // Keyboard shortcuts
            $(document).keydown(function(e) {
                // Ctrl+S for save
                if (e.ctrlKey && e.keyCode === 83) {
                    e.preventDefault();
                    $('#jobOrderForm').submit();
                }
                // Ctrl+Shift+R for reset
                if (e.ctrlKey && e.shiftKey && e.keyCode === 82) {
                    e.preventDefault();
                    $('#resetBtn').click();
                }
                // Ctrl+Shift+N for save and new
                if (e.ctrlKey && e.shiftKey && e.keyCode === 78) {
                    e.preventDefault();
                    $('#saveAndNewBtn').click();
                }
            });

            // Tooltips
            $('[title]').tooltip();

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Set default date to today if empty
            if (!$('input[name="date"]').val()) {
                var today = new Date().toISOString().split('T')[0];
                $('input[name="date"]').val(today);
            }

            // Update total items on load
            updateTotalItems();
        });
    </script>
</body>
</html>