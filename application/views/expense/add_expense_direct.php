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
                <h1>Direct Entry Ledger</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/add_expense_data_direct' ?>">Direct Entry Ledger</a></li>
                    <li class="active">Direct Entry Ledger Details</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Direct Entry Ledger Details</h3>
                                <div class="pull-right" style="margin-right: 10px;">
                                    <a href="<?php echo base_url(); ?>InventoryController/add_expense_data_direct?str=All" class="btn btn-success" style="margin-right: 5px;">
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

                                <div class="col-md-5" style="padding-top:5px; display:flex; justify-content:right;">

                                </div>
                            </div>
                            
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Expenditure Category</th>
                                        <th>Bank Voucher Type</th>
                                        <th>Paid Date</th>
                                        <th>Month</th>
<th>GST (%)</th>
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
                                    foreach ($expense_result as $key) {
                                        $total_basic += (float)(isset($key->basic_amount) ? $key->basic_amount : 0);
                                        $total_amount += (float)$key->expense_amount;
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
<td><?php echo htmlspecialchars(isset($key->expense_category) ? $key->expense_category : ''); ?></td>
<td><?php echo htmlspecialchars(isset($key->bank_voucher_type) ? $key->bank_voucher_type : ''); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($key->date)); ?></td>
                                            <td><?php echo htmlspecialchars(isset($key->expense_month) ? $key->expense_month : ''); ?></td>
<td><?php echo htmlspecialchars(isset($key->gst_class) ? (string)$key->gst_class : ''); ?></td>
                                            <td><?php echo number_format((float)(isset($key->basic_amount) ? $key->basic_amount : 0), 2); ?></td>
                                            <td><?php echo number_format((float)$key->expense_amount, 2); ?></td>
                                            <td>
                                                <?php if ($key->expense_upload && $key->expense_upload != './uploads/') { ?>
                                                    <a href="<?php echo base_url() . $key->expense_upload ?>" download="Download">Download</a>
                                                <?php } else { echo "-"; } ?>
                                            </td>
<td><?php echo htmlspecialchars(isset($key->expense_note) ? (string)$key->expense_note : ''); ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="<?php echo base_url() . 'InventoryController/get_expense_by_id/' . $key->expense_id . '?expense_mode=direct'; ?>"><i class="fa fa-pencil"></i> Edit</a></li>
                                                        <li><a href="<?php echo base_url() . 'InventoryController/delete_expense_by_id/' . $key->expense_id . '?expense_mode=direct'; ?>" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"></i> Delete</a></li>
                                                        <li role="separator" class="divider"></li>
                                                        <li><a href="<?php echo base_url() . 'InventoryController/export_expense_excel?expense_id=' . $key->expense_id . '&expense_mode=direct'; ?>"><i class="fa fa-file-excel-o" style="color:#1a7a4a;"></i> Export Excel</a></li>
                                                        <li><a href="<?php echo base_url() . 'InventoryController/export_expense_pdf?expense_id=' . $key->expense_id . '&expense_mode=direct'; ?>"><i class="fa fa-file-pdf-o" style="color:#c0392b;"></i> Export PDF</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                    <?php if (count($expense_result) > 0) { ?>
                                    <tr style="background: #f5f5f5; font-weight: bold;">
                                        <td colspan="5" style="text-align: right;">Total:</td>
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="addExpenseModalLabel"><i class="fa fa-plus"></i> Add Direct Expense</h4>
                    </div>
                    <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>InventoryController/add_expense" enctype="multipart/form-data" id="expenseForm">
                        <input type="hidden" name="expense_mode" value="direct">
                        <input type="hidden" name="expense_type" value="">
                        <input type="hidden" name="status" value="1">
                        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    
                                    <!-- 0. Employee Name -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Employee Name <span style="color: red;"></span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" name="employee_name" id="modal_employee_name">
                                                <option value="">Select Employee Name</option>
                                                <?php if (isset($direct_individuals) && !empty($direct_individuals)) { ?>
                                                    <?php foreach ($direct_individuals as $emp) { ?>
                                                        <option value="<?php echo htmlspecialchars($emp->employee_name); ?>"><?php echo htmlspecialchars($emp->employee_name); ?> (<?php echo htmlspecialchars($emp->code); ?>)</option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 1. Expenditure Category -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Expenditure Category <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" name="expense_category" id="modal_expense_category" required="">
                                                <option value="">Select Expenditure Category</option>
                                                <?php foreach ($expense_catgory as $key) { ?>
                                                    <option value="<?php echo $key->exp_cat; ?>"><?php echo $key->exp_cat; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>





                                    <!-- 2. Bank Voucher Type -->
                                    <div class="form-group row">
                                        <label for="bank_voucher_type" class="col-sm-4 control-label">Bank Voucher Type<span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" name="bank_voucher_type" id="bank_voucher_type" required="">
                                                <option value="">Select Bank Voucher Type</option>
                                                <option value="Payment" <?php if(($result_by_id['bank_voucher_type'] ?? '') == "Payment") echo 'selected="selected"'; ?>>Payment</option>
                                                <option value="Receipt" <?php if(($result_by_id['bank_voucher_type'] ?? '') == "Receipt") echo 'selected="selected"'; ?>>Receipt</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 3. Paid Date -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Paid Date <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-sm alldate" name="date" id="modal_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>

                                    <!-- 3. Month -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Month <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-sm onlymonth" name="expense_month" id="modal_expense_month" required="" onkeydown="return false;" autocomplete="off" placeholder="Select Month">
                                        </div>
                                    </div>

                                    <!-- 4. Basic Amount -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Basic Amount <span style="color: red;">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="number" min="0" step="0.01" class="form-control input-sm" name="basic_amount" id="modal_basic_amount" required="" onchange="modalCalcTotal()" onkeyup="modalCalcTotal()">
                                        </div>
                                    </div>

                                    <!-- 5. GST (%) -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">GST (%)</label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" name="gst_class" id="modal_gst_class" onchange="modalCalcTotal()">
                                                <option value="">Select GST</option>
                                                <?php foreach ($gst_class_result as $key) { ?>
                                                    <option value="<?php echo $key->gst_class; ?>"><?php echo $key->gst_class; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 6. Total Amount (Auto calculated) -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Total Amount</label>
                                        <div class="col-sm-7">
                                            <input type="number" min="0" step="0.01" class="form-control input-sm" name="expense_amount" id="modal_expense_amount" readonly="" style="background:#f5f5f5;">
                                        </div>
                                    </div>

                                    <!-- 7. Expenditure Doc -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Expenditure Doc</label>
                                        <div class="col-sm-7">
                                            <input type="file" class="form-control input-sm" name="expense_upload" id="modal_expense_upload">
                                        </div>
                                    </div>

                                    <!-- 8. Remark -->
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

    // Reset modal form when closed
    $('#addExpenseModal').on('hidden.bs.modal', function () {
        document.getElementById('expenseForm').reset();
        document.getElementById('modal_expense_amount').value = '';
    });
    </script>
</body>