<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
     <div id="loader" class="center"></div> 
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Indirect Entry Ledger</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/add_expense_data_indirect' ?>">Indirect Entry Ledger</a></li>
                    <li class="active">Indirect Entry Ledger Details</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Indirect Entry Ledger Details</h3>
                                <div class="pull-right" style="margin-right: 10px;">
                                    <a href="<?php echo base_url(); ?>InventoryController/add_expense_data_indirect?str=All" class="btn btn-success" style="margin-right: 5px;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addExpenseModal">
                                        <i class="fa fa-plus"></i> Add Expense
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">

                               

                           
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <h3 class="box-title" style="padding-left: 15px;">Expenditure Details</h3> 
                                </div>
                            </div>
                            
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Expenditure Category</th>
                                        <th>Bank Voucher Type</th>
                                        <th>Employee Name</th>
                                        <th>Paid Date</th>
                                        <th>Month</th>
                                        <th>TAX (%)</th>
                                        <th>Basic Amount</th>
                                        <th>Total Amount</th>
                                        <th>Expenditure Doc</th>
                                        <th>Remark</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $total_basic = 0;
                                    $total_amount = 0;
                                    if (isset($expense_result) && !empty($expense_result)) {
                                        foreach ($expense_result as $key) {
                                            $total_basic += (float)(isset($key->basic_amount) ? $key->basic_amount : 0);
                                            $total_amount += (float)$key->expense_amount;
                                            // Clean up category display name
                                            $display_category = $key->expense_category;
                                            if (stripos($display_category, 'Indirect - ') === 0) {
                                                $display_category = trim(substr($display_category, strlen('Indirect - ')));
                                            }
                                            if (preg_match('/^(Individual|Corporate)\s*-\s*(.*)$/i', $display_category, $m)) {
                                                $display_category = trim($m[2]);
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo htmlspecialchars($display_category); ?></td>
                                                <td><?php echo htmlspecialchars(isset($key->bank_voucher_type) ? $key->bank_voucher_type : ''); ?></td>
                                                <td><?php echo htmlspecialchars($key->employee_name); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($key->date)); ?></td>
                                                <td><?php echo htmlspecialchars(isset($key->expense_month) ? $key->expense_month : ''); ?></td>
                                                <td><?php echo htmlspecialchars($key->gst_class); ?></td>
                                                <td><?php echo number_format((float)(isset($key->basic_amount) ? $key->basic_amount : 0), 2); ?></td>
                                                <td><?php echo number_format((float)$key->expense_amount, 2); ?></td>
                                                <td>
                                                    <?php if ($key->expense_upload && $key->expense_upload != './uploads/') { ?>
                                                        <a href="<?php echo base_url() . $key->expense_upload ?>" download="Download">Download</a>
                                                    <?php } else { echo "-"; } ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($key->expense_note); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a href="<?php echo base_url() . 'InventoryController/get_expense_by_id/' . $key->expense_id . '?expense_mode=indirect'; ?>"><i class="fa fa-pencil"></i> Edit</a></li>
                                                            <li><a href="<?php echo base_url() . 'InventoryController/delete_expense_by_id/' . $key->expense_id . '?expense_mode=indirect'; ?>" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"></i> Delete</a></li>
                                                            <li role="separator" class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'InventoryController/export_expense_excel?expense_id=' . $key->expense_id . '&expense_mode=indirect'; ?>"><i class="fa fa-file-excel-o" style="color:#1a7a4a;"></i> Export Excel</a></li>
                                                            <li><a href="<?php echo base_url() . 'InventoryController/export_expense_pdf?expense_id=' . $key->expense_id . '&expense_mode=indirect'; ?>"><i class="fa fa-file-pdf-o" style="color:#c0392b;"></i> Export PDF</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="11" style="text-align: center;">No records found</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php if (isset($expense_result) && !empty($expense_result) && count($expense_result) > 0) { ?>
                                    <tr style="background: #f5f5f5; font-weight: bold;">
                                        <td colspan="6" style="text-align: right;">Total:</td>
                                        <td><?php echo number_format($total_basic, 2); ?></td>
                                        <td><?php echo number_format($total_amount, 2); ?></td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Add Expense Modal -->
        <div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background: #3c8dbc; color: white;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="addExpenseModalLabel"><i class="fa fa-plus"></i> Add Indirect Expense</h4>
                    </div>
                    <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>InventoryController/add_expense" enctype="multipart/form-data" id="expenseForm">
                        <input type="hidden" name="expense_mode" value="indirect">
                        <input type="hidden" name="status" value="1">
                        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    
                                    <!-- 1. Expenditure Category -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Expenditure Category <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" name="expense_category" id="modal_expense_category" required onchange="toggleIndividualField()">
                                                <option value="">Select Expenditure Category</option>
                                                <?php 
                                                if (isset($expense_catgory) && !empty($expense_catgory)) {
                                                    foreach ($expense_catgory as $key) { 
                                                        $cat_value = $key->exp_cat;
                                                        $cat_display = $key->exp_cat;
                                                        $exp_type_from_cat = '';
                                                        
                                                        // Parse the stored category name
                                                        if (stripos($cat_value, 'Indirect - ') === 0) {
                                                            $temp = trim(substr($cat_value, strlen('Indirect - ')));
                                                            if (preg_match('/^(Individual|Corporate)\s*-\s*(.*)$/i', $temp, $m)) {
                                                                $exp_type_from_cat = strtolower($m[1]);
                                                                $cat_display = trim($m[2]);
                                                            } else {
                                                                $cat_display = $temp;
                                                            }
                                                        }
                                                    ?>
                                                        <option value="<?php echo htmlspecialchars($cat_value); ?>" 
                                                                data-expense-type="<?php echo $exp_type_from_cat; ?>">
                                                            <?php echo htmlspecialchars($cat_display); ?>
                                                        </option>
                                                    <?php 
                                                    }
                                                } 
                                                ?>
                                            </select>


                                        </div>
                                    </div>
                                    

<!-- 2. Individual Dropdown (only for Individual type - hidden by default) -->
                                    <div class="form-group" id="individual_dropdown_field" style="display: none;">
                                        <label class="col-sm-4 control-label">Select Individual <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" name="employee_name" id="modal_individual_select">
                                                <option value="">Select Individual</option>
                                                <?php if (isset($individuals) && !empty($individuals)) {
                                                    foreach ($individuals as $ind) { ?>
                                                        <option value="<?php echo htmlspecialchars($ind->employee_name); ?>"><?php echo htmlspecialchars($ind->employee_name); ?> (<?php echo htmlspecialchars($ind->code); ?>)</option>
                                                <?php } 
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 2. Bank Voucher Type -->
                                    <div class="form-group row">
                                        <label for="bank_voucher_type" class="col-sm-4 control-label">Bank Voucher Type<span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" name="bank_voucher_type" id="bank_voucher_type" required="">
                                                <option value="">Select Bank Voucher Type</option>
                                                <option value="Payment" <?php echo (($result_by_id['bank_voucher_type'] ?? '') == "Payment") ? 'selected="selected"' : ''; ?>>Payment</option>
                                                <option value="Receipt" <?php echo (($result_by_id['bank_voucher_type'] ?? '') == "Receipt") ? 'selected="selected"' : ''; ?>>Receipt</option>
                                            </select>
                                        </div>
                                    </div>

     



                                    <!-- Hidden input for Corporate type (no field shown) -->
                                    <input type="hidden" name="employee_name" id="hidden_employee_name" value="Corporate Expense">

                                    <!-- 3. Paid Date -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Paid Date <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-sm alldate" name="date" id="modal_date" required onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>

                                    <!-- 4. Month -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Month <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-sm onlymonth" name="expense_month" id="modal_expense_month" required onkeydown="return false;" autocomplete="off" placeholder="Select Month">
                                        </div>
                                    </div>

                                    <!-- 5. Basic Amount -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Basic Amount <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="number" min="0" step="0.01" class="form-control input-sm" name="basic_amount" id="modal_basic_amount" required onchange="modalCalcTotal()" onkeyup="modalCalcTotal()">
                                        </div>
                                    </div>

                                    <!-- 6. GST (%) -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">GST (%)</label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" name="gst_class" id="modal_gst_class" onchange="modalCalcTotal()">
                                                <option value="">Select GST</option>
                                                <?php if (isset($gst_class_result) && !empty($gst_class_result)) {
                                                    foreach ($gst_class_result as $key) { ?>
                                                        <option value="<?php echo $key->gst_class; ?>"><?php echo $key->gst_class; ?></option>
                                                <?php } 
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 7. Total Amount (Auto calculated) -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Total Amount</label>
                                        <div class="col-sm-7">
                                            <input type="number" min="0" step="0.01" class="form-control input-sm" name="expense_amount" id="modal_expense_amount" readonly style="background:#f5f5f5;">
                                        </div>
                                    </div>

                                    <!-- 8. Expenditure Doc -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Expenditure Doc</label>
                                        <div class="col-sm-7">
                                            <input type="file" class="form-control input-sm" name="expense_upload" id="modal_expense_upload">
                                        </div>
                                    </div>

                                    <!-- 9. Remark -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Remark</label>
                                        <div class="col-sm-7">
                                            <textarea class="form-control" name="expense_note" id="modal_expense_note" rows="3"></textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <script>
    function modalCalcTotal() {
        var basic = parseFloat(document.getElementById('modal_basic_amount').value) || 0;
        var gstEl = document.getElementById('modal_gst_class');
        var gst = parseFloat(gstEl.options[gstEl.selectedIndex].value) || 0;
        var total = basic + (basic * gst / 100);
        document.getElementById('modal_expense_amount').value = total.toFixed(2);
    }

    function toggleIndividualField() {
        var categorySelect = document.getElementById('modal_expense_category');
        if (!categorySelect || categorySelect.selectedIndex === -1 || !categorySelect.options[categorySelect.selectedIndex]) {
            return;
        }
        
        var selectedOption = categorySelect.options[categorySelect.selectedIndex];
        var expenseType = selectedOption.getAttribute('data-expense-type');
        
        var individualField = document.getElementById('individual_dropdown_field');
        var individualSelect = document.getElementById('modal_individual_select');
        var hiddenEmployeeName = document.getElementById('hidden_employee_name');
        
        if (expenseType === 'individual') {
            // Show individual dropdown, hide hidden field
            individualField.style.display = 'block';
            if (individualSelect) individualSelect.setAttribute('required', 'required');
            if (hiddenEmployeeName) hiddenEmployeeName.disabled = true;
        } else {
            // Hide individual dropdown, use hidden field for corporate
            individualField.style.display = 'none';
            if (individualSelect) individualSelect.removeAttribute('required');
            if (individualSelect) individualSelect.value = '';
            if (hiddenEmployeeName) hiddenEmployeeName.disabled = false;
        }
    }

    // Before form submit, ensure correct value is sent
    $('#expenseForm').on('submit', function() {
        var categorySelect = document.getElementById('modal_expense_category');
        var selectedOption = categorySelect.options[categorySelect.selectedIndex];
        var expenseType = selectedOption.getAttribute('data-expense-type');
        var hiddenEmployeeName = document.getElementById('hidden_employee_name');
        var individualSelect = document.getElementById('modal_individual_select');
        
        if (expenseType === 'individual') {
            // Disable hidden field and use individual select value
            hiddenEmployeeName.disabled = true;
            hiddenEmployeeName.value = '';
        } else {
            // Enable hidden field for corporate
            hiddenEmployeeName.disabled = false;
            hiddenEmployeeName.value = 'Corporate Expense';
        }
    });

    // Reset modal form when closed
    $('#addExpenseModal').on('hidden.bs.modal', function () {
        document.getElementById('expenseForm').reset();
        document.getElementById('modal_expense_amount').value = '';
        document.getElementById('individual_dropdown_field').style.display = 'none';
        var individualSelect = document.getElementById('modal_individual_select');
        var hiddenEmployeeName = document.getElementById('hidden_employee_name');
        if (individualSelect) individualSelect.removeAttribute('required');
        if (individualSelect) individualSelect.value = '';
        if (hiddenEmployeeName) {
            hiddenEmployeeName.disabled = false;
            hiddenEmployeeName.value = 'Corporate Expense';
        }
    });

    // Initialize on page load
    $(document).ready(function() {
        $('#modal_expense_category').on('change', toggleIndividualField);
    });
    </script>
</body>