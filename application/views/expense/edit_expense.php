<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');

$expense_mode = isset($expense_mode) ? strtolower($expense_mode) : '';
$expense_mode_label = '';
if ($expense_mode == 'direct') {
    $expense_mode_label = 'Direct ';
} elseif ($expense_mode == 'indirect') {
    $expense_mode_label = 'Indirect ';
}
?>
<body class="hold-transition skin-blue sidebar-mini">
     <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Edit <?php echo $expense_mode_label; ?>Entry
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/add_expense_data?expense_mode=' . $expense_mode ?>">Edit <?php echo $expense_mode_label; ?>Entry</a></li>
                    <li class="active">Edit Expense Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Expense Details</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/add_expense" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">

                                            <input type="hidden" name="expense_id" id="expense_id" value="<?php echo $exp_result_by_id['expense_id']; ?>">
                                            <input type="hidden" name="expense_mode" value="<?php echo $expense_mode; ?>">

                                            <!-- 1. Expenditure Category -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Expenditure Category <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm" name="expense_category" id="expense_category" required="">
                                                        <option value="">Select Expenditure Category</option>
                                                        <?php foreach ($expense_catgory as $key) { ?>
                                                            <option value="<?php echo $key->exp_cat; ?>"<?=$exp_result_by_id['expense_category'] == $key->exp_cat ? ' selected="selected"' : '';?>><?php echo $key->exp_cat; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 2. Bank Voucher Type (Direct only) -->
                                            <?php if ($expense_mode == 'direct') { ?>
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Bank Voucher Type <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm" name="bank_voucher_type" id="bank_voucher_type" required="">
                                                        <option value="">Select Bank Voucher Type</option>
                                                        <option value="Payment"<?= (isset($exp_result_by_id['bank_voucher_type']) && $exp_result_by_id['bank_voucher_type'] == 'Payment') ? ' selected="selected"' : ''; ?>>Payment</option>
                                                        <option value="Receipt"<?= (isset($exp_result_by_id['bank_voucher_type']) && $exp_result_by_id['bank_voucher_type'] == 'Receipt') ? ' selected="selected"' : ''; ?>>Receipt</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <input type="hidden" name="bank_voucher_type" value="">
                                            <?php } ?>

                                            <!-- 3. Expenditure Type (Indirect only) -->
                                            <?php if ($expense_mode == 'indirect') { ?>
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Expenditure Type <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm" name="expense_type" id="expense_type" required="">
                                                        <option value="">Select Expenditure Type</option>
                                                        <option value="Individual"<?= (isset($exp_result_by_id['expense_type']) && $exp_result_by_id['expense_type'] == 'Individual') ? ' selected="selected"' : ''; ?>>Individual</option>
                                                        <option value="Corporate"<?= (isset($exp_result_by_id['expense_type']) && $exp_result_by_id['expense_type'] == 'Corporate') ? ' selected="selected"' : ''; ?>>Corporate</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <input type="hidden" name="expense_type" value="">
                                            <?php } ?>

                                            <!-- 3. Employee Name -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Employee Name <span style="color: red;"></span></label>
                                                <div class="col-sm-7">
                                                    <?php if ($expense_mode == 'direct') { ?>
                                                        <select class="form-control input-sm" name="employee_name" id="employee_name">
                                                            <option value="">Select Employee Name</option>
                                                            <?php if (isset($direct_individuals) && !empty($direct_individuals)) { ?>
                                                                <?php foreach ($direct_individuals as $emp) { ?>
                                                                    <option value="<?php echo htmlspecialchars($emp->employee_name); ?>" <?php echo (isset($exp_result_by_id['employee_name']) && $exp_result_by_id['employee_name'] == $emp->employee_name) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($emp->employee_name); ?> (<?php echo htmlspecialchars($emp->code); ?>)</option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    <?php } else { ?>
                                                        <select class="form-control input-sm" name="employee_name" id="employee_name" required="">
                                                            <option value="">Select Employee Name</option>
                                                            <?php if (isset($individuals) && !empty($individuals)) { ?>
                                                                <?php foreach ($individuals as $emp) { ?>
                                                                    <option value="<?php echo htmlspecialchars($emp->employee_name); ?>" <?php echo (isset($exp_result_by_id['employee_name']) && $exp_result_by_id['employee_name'] == $emp->employee_name) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($emp->employee_name); ?> (<?php echo htmlspecialchars($emp->code); ?>)</option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <!-- 4. Paid Date -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Paid Date <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <?php $newformat = date('d-m-Y', strtotime($exp_result_by_id['date'])); ?>
                                                    <input type="text" class="form-control input-sm alldate1" name="date" value="<?php echo $newformat; ?>" required="" onkeydown="return false;" autocomplete="off">
                                                </div>
                                            </div>

                                            <!-- 5. Month -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Month <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm onlymonth" name="expense_month" id="expense_month" value="<?php echo htmlspecialchars(isset($exp_result_by_id['expense_month']) ? $exp_result_by_id['expense_month'] : ''); ?>" required="" onkeydown="return false;" autocomplete="off" placeholder="Select Month">
                                                </div>
                                            </div>

                                            <!-- 6. GST -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">GST (%)</label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm" name="gst_class" id="gst_class" onchange="calcTotal()">
                                                        <option value="">Select GST</option>
                                                        <?php foreach ($gst_class_result as $key) { ?>
                                                            <option value="<?php echo $key->gst_class; ?>"<?=$exp_result_by_id['gst_class'] == $key->gst_class ? ' selected="selected"' : '';?>><?php echo $key->gst_class; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 7. Basic Amount -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Basic Amount <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <input type="number" min="0" step="0.01" class="form-control input-sm" name="basic_amount" id="basic_amount" value="<?php echo htmlspecialchars(isset($exp_result_by_id['basic_amount']) ? $exp_result_by_id['basic_amount'] : ''); ?>" required="" onchange="calcTotal()" onkeyup="calcTotal()">
                                                </div>
                                            </div>

                                            <!-- 8. Total Amount (GST calculated) -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Total Amount</label>
                                                <div class="col-sm-7">
                                                    <input type="number" min="0" step="0.01" class="form-control input-sm" name="expense_amount" id="expense_amount" value="<?php echo htmlspecialchars($exp_result_by_id['expense_amount']); ?>" readonly="" style="background:#f5f5f5;">
                                                </div>
                                            </div>

                                            <!-- 8. Remark -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Remark</label>
                                                <div class="col-sm-7">
                                                    <textarea class="form-control" name="expense_note" id="expense_note" rows="2"><?php echo htmlspecialchars($exp_result_by_id['expense_note']); ?></textarea>
                                                </div>
                                            </div>

                                            <!-- 9. Expenditure Doc -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Expenditure Doc</label>
                                                <div class="col-sm-7">
                                                    <input type="file" class="form-control input-sm" name="expense_upload" id="expense_upload">
                                                    <?php if (!empty($exp_result_by_id['expense_upload']) && $exp_result_by_id['expense_upload'] != './uploads/') { ?>
                                                        <a href="<?php echo base_url() . $exp_result_by_id['expense_upload'] ?>" download="Download">View</a>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <!-- Payment Status -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Payment Status <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm" name="status" id="status" required="">
                                                        <option value="">Select Payment Status</option>
                                                        <option value="1"<?=$exp_result_by_id['status'] == '1' ? ' selected="selected"' : '';?>>Done</option>
                                                        <option value="2"<?=$exp_result_by_id['status'] == '2' ? ' selected="selected"' : '';?>>Pending on Date</option>
                                                        <option value="3"<?=$exp_result_by_id['status'] == '3' ? ' selected="selected"' : '';?>>Advance</option>
                                                        <option value="4"<?=$exp_result_by_id['status'] == '4' ? ' selected="selected"' : '';?>>Pending Amount</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
                                </form>
<script>
function calcTotal() {
    var basic = parseFloat(document.getElementById('basic_amount').value) || 0;
    var gstEl = document.getElementById('gst_class');
    var gst   = parseFloat(gstEl.options[gstEl.selectedIndex].value) || 0;
    var total = basic + (basic * gst / 100);
    document.getElementById('expense_amount').value = total.toFixed(2);
}
calcTotal();
</script>

                            </div>
                            <!-- /.box-body -->
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Expenditure Category</th>
                                        <?php if ($expense_mode == 'direct') { ?><th>Bank Voucher Type</th><?php } ?>
                                        <?php if ($expense_mode == 'indirect') { ?><th>Expenditure Type</th><?php } ?>
                                        <th>Employee Name</th>
                                        <th>Paid Date</th>
                                        <th>Month</th>
                                        <th>TAX (%)</th>
                                        <th>Basic Amount</th>
                                        <th>Total Amount</th>
                                        <th>Remark</th>
                                        <th>Expenditure Doc</th>
                                        <th>Payment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($expense_result as $key) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo htmlspecialchars($key->expense_category); ?></td>
                                            <?php if ($expense_mode == 'direct') { ?>
                                            <td><?php echo htmlspecialchars(isset($key->bank_voucher_type) ? $key->bank_voucher_type : ''); ?></td>
                                            <?php } ?>
                                            <?php if ($expense_mode == 'indirect') { ?>
                                            <td><?php echo htmlspecialchars(isset($key->expense_type) ? $key->expense_type : ''); ?></td>
                                            <?php } ?>
                                            <td><?php echo htmlspecialchars($key->employee_name); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($key->date)); ?></td>
                                            <td><?php echo htmlspecialchars(isset($key->expense_month) ? $key->expense_month : ''); ?></td>
                                            <td><?php echo htmlspecialchars($key->gst_class); ?></td>
                                            <td><?php echo number_format((float)(isset($key->basic_amount) ? $key->basic_amount : 0), 2); ?></td>
                                            <td><?php echo number_format((float)$key->expense_amount, 2); ?></td>
                                            <td><?php echo htmlspecialchars($key->expense_note); ?></td>
                                            <td>
                                                <?php if ($key->expense_upload && $key->expense_upload != './uploads/') { ?>
                                                    <a href="<?php echo base_url() . $key->expense_upload ?>" download="Download">Download</a>
                                                <?php } ?>
                                            </td>
                                            <td><?php
                                                switch ($key->status) {
                                                    case "1": echo "Done"; break;
                                                    case "2": echo "Pending on Date"; break;
                                                    case "3": echo "Advance"; break;
                                                    default:  echo "Pending Amount";
                                                }
                                            ?></td>
                                            <td>
                                                
                                                <div class="dropdown">
<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
<span class="caret"></span></button>
<ul class="dropdown-menu">
<li><a href="<?php echo base_url() . 'InventoryController/get_expense_by_id/' . $key->expense_id . '?expense_mode=' . $expense_mode; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
<li><a href="<?php echo base_url() . 'InventoryController/delete_expense_by_id/' . $key->expense_id . '?expense_mode=' . $expense_mode; ?>" 
role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>
</ul>
</div>
                                            </td> </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>

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
<?php /* end */ ?>


