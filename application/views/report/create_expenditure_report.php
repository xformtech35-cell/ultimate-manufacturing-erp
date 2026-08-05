<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');

$from_date = isset($from_date) ? $from_date : '';
$to_date = isset($to_date) ? $to_date : '';
$expense_category_str = isset($expense_category_str) ? $expense_category_str : '';
$employee_name_str = isset($employee_name_str) ? $employee_name_str : '';
$expense_month_str = isset($expense_month_str) ? $expense_month_str : '';
$gst_class_str = isset($gst_class_str) ? $gst_class_str : '';
$status_str = isset($status_str) ? $status_str : '';
$expense_categories = isset($expense_categories) && is_array($expense_categories) ? $expense_categories : array();
$gst_class_result = isset($gst_class_result) && is_array($gst_class_result) ? $gst_class_result : array();
$result = isset($result) && is_array($result) ? $result : array();

if (!function_exists('report_clean_expense_category')) {
    function report_clean_expense_category($stored_category)
    {
        $stored_category = trim((string) $stored_category);
        if (stripos($stored_category, 'Direct - ') === 0) {
            return trim(substr($stored_category, strlen('Direct - ')));
        }
        if (stripos($stored_category, 'Indirect - ') === 0) {
            return trim(substr($stored_category, strlen('Indirect - ')));
        }
        return $stored_category;
    }
}
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Expenditure Report</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Report</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Expenditure Report</h3>
                            </div>

                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_expenditure_report">
                                <div class="box-body">
                                    <div class="form-group row">
                                        <label for="expense_category" class="col-sm-3 control-label">Expenditure Category</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm" name="expense_category" id="expense_category">
                                                <option value="">All Categories</option>
                                                <?php foreach ($expense_categories as $cat) {
                                                    $cat_value = isset($cat->exp_cat) ? (string) $cat->exp_cat : '';
                                                    if ($cat_value === '') {
                                                        continue;
                                                    }
                                                    ?>
                                                    <option value="<?php echo htmlspecialchars($cat_value); ?>" <?php echo ($expense_category_str === $cat_value) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars(report_clean_expense_category($cat_value)); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="employee_name" class="col-sm-3 control-label">Employee Name</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control input-sm" name="employee_name" id="employee_name" value="<?php echo htmlspecialchars($employee_name_str); ?>" placeholder="Employee Name">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="from_date" class="col-sm-3 control-label">Paid Date From<span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" required="" onkeydown="return false;">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="to_date" class="col-sm-3 control-label">Paid Date To<span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control payment-due-date-check" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" required="" onkeydown="return false;">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="expense_month" class="col-sm-3 control-label">Month</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control onlymonth" name="expense_month" id="expense_month" value="<?php echo htmlspecialchars($expense_month_str); ?>" autocomplete="off" placeholder="MM-YYYY">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="gst_class" class="col-sm-3 control-label">GST (%)</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm" name="gst_class" id="gst_class">
                                                <option value="">All GST</option>
                                                <?php foreach ($gst_class_result as $gst_row) {
                                                    $gst_label = isset($gst_row['gst_class']) ? (string) $gst_row['gst_class'] : '';
                                                    $gst_value = rtrim($gst_label, '%');
                                                    ?>
                                                    <option value="<?php echo htmlspecialchars($gst_value); ?>" <?php echo ((string) $gst_class_str === (string) $gst_value) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($gst_label); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="status" class="col-sm-3 control-label">Payment Status</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm" name="status" id="status">
                                                <option value="">All Status</option>
                                                <option value="1" <?php echo ($status_str === '1') ? 'selected="selected"' : ''; ?>>Done</option>
                                                <option value="2" <?php echo ($status_str === '2') ? 'selected="selected"' : ''; ?>>Pending on Date</option>
                                                <option value="3" <?php echo ($status_str === '3') ? 'selected="selected"' : ''; ?>>Advance</option>
                                                <option value="4" <?php echo ($status_str === '4') ? 'selected="selected"' : ''; ?>>Pending Amount</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <center>
                                    <button type="reset" class="btn btn-default">Clear</button>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </center>
                            </form>

                            <a href="<?php echo base_url(); ?>ReportController/get_expenditure_report_by_date"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>

                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Expenditure Category</th>
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
                                    $basic_total = 0;
                                    $grand_total = 0;
                                    foreach ($result as $row) {
                                        $row_basic = (float) (isset($row->basic_amount) ? $row->basic_amount : 0);
                                        $row_total = (float) (isset($row->expense_amount) ? $row->expense_amount : 0);
                                        $basic_total += $row_basic;
                                        $grand_total += $row_total;

                                        $status_label = 'Pending Amount';
                                        if ((string) $row->status === '1') {
                                            $status_label = 'Done';
                                        } elseif ((string) $row->status === '2') {
                                            $status_label = 'Pending on Date';
                                        } elseif ((string) $row->status === '3') {
                                            $status_label = 'Advance';
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo htmlspecialchars(report_clean_expense_category(isset($row->expense_category) ? $row->expense_category : '')); ?></td>
                                            <td><?php echo htmlspecialchars(isset($row->employee_name) ? $row->employee_name : ''); ?></td>
                                            <td><?php echo !empty($row->date) ? date('d-m-Y', strtotime($row->date)) : ''; ?></td>
                                            <td><?php echo htmlspecialchars(isset($row->expense_month) ? $row->expense_month : ''); ?></td>
                                            <td><?php echo htmlspecialchars(isset($row->gst_class) ? $row->gst_class : ''); ?></td>
                                            <td style="text-align:right;"><?php echo number_format($row_basic, 2); ?></td>
                                            <td style="text-align:right;"><?php echo number_format($row_total, 2); ?></td>
                                            <td><?php echo htmlspecialchars(isset($row->expense_note) ? $row->expense_note : ''); ?></td>
                                            <td>
                                                <?php if (!empty($row->expense_upload)) { ?>
                                                    <a href="<?php echo base_url() . $row->expense_upload; ?>" target="_blank">View</a>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $status_label; ?></td>
                                            <td>-</td>
                                        </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>
                                    <?php if (count($result) > 0) { ?>
                                        <tr style="font-weight:bold;background:#eaf4fb;">
                                            <td colspan="6" style="text-align:right;">Grand Total:</td>
                                            <td style="text-align:right;"><?php echo number_format($basic_total, 2); ?></td>
                                            <td style="text-align:right;"><?php echo number_format($grand_total, 2); ?></td>
                                            <td colspan="4"></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
</body>
