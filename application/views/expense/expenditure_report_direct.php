<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');

$from_date = isset($from_date) ? $from_date : '';
$to_date = isset($to_date) ? $to_date : '';
$selected_category = isset($selected_category) ? $selected_category : '';
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Direct Expenditure Report</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Direct Expenditure Report</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Filter Report</h3>
                            </div>
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/get_direct_expense_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="from_date" class="col-sm-3 control-label">From Date</label>
                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" value="<?php echo $from_date; ?>" name="from_date" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="to_date" class="col-sm-3 control-label">To Date</label>
                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control payment-due-date-check" value="<?php echo $to_date; ?>" name="to_date" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="expense_category" class="col-sm-3 control-label">Expenditure Category</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm" name="expense_category" id="expense_category">
                                                <option value="">All Categories</option>
                                                <?php 
                                                if (isset($expense_catgory) && !empty($expense_catgory)) {
                                                    foreach ($expense_catgory as $key) { 
                                                        $cat_value = $key->exp_cat;
                                                        $cat_display = $key->exp_cat;
                                                        
                                                        if (stripos($cat_value, 'Direct - ') === 0) {
                                                            $cat_display = trim(substr($cat_value, strlen('Direct - ')));
                                                        }
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($cat_value); ?>" 
                                                        <?php echo ($selected_category == $cat_value) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cat_display); ?>
                                                    </option>
                                                <?php 
                                                    }
                                                } 
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-4">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="show_all" value="1" <?php echo (isset($_POST['show_all']) && $_POST['show_all'] == 1) ? 'checked' : ''; ?>>
                                                    Show All Records (Ignore Date Range)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <center>
                                    <button type="button" class="btn btn-default" onclick="history.back()">Cancel</button>
                                    <button type="submit" class="btn btn-success">Generate Report</button>
                                </center>
                            </form>
                        </div>
                    </div>
                </div>

                <?php if (isset($result) && !empty($result)) { ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-success">
                            <div class="box-header">
                                <h3 class="box-title">Report Results</h3>
                                <div class="pull-right">
                                    <a href="<?php echo base_url(); ?>ReportController/export_direct_expense_excel?from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>&category=<?php echo urlencode($selected_category); ?>&show_all=<?php echo isset($_POST['show_all']) ? $_POST['show_all'] : ''; ?>" class="btn btn-success btn-sm">
                                        <i class="fa fa-file-excel-o"></i> Export to Excel
                                    </a>
                                </div>
                            </div>
                            <div class="box-body">
                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Expenditure Category</th>
                                            <th>Paid Date</th>
                                            <th>Month</th>
                                            <th>TAX (%)</th>
                                            <th>Basic Amount</th>
                                            <th>Total Amount</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $total_basic = 0;
                                        $total_amount = 0;
                                        foreach ($result as $key) {
                                            $total_basic += (float)(isset($key->basic_amount) ? $key->basic_amount : 0);
                                            $total_amount += (float)$key->expense_amount;
                                            ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo htmlspecialchars(str_replace('Direct - ', '', $key->expense_category)); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($key->date)); ?></td>
                                                <td><?php echo htmlspecialchars(isset($key->expense_month) ? $key->expense_month : ''); ?></td>
                                                <td><?php echo htmlspecialchars($key->gst_class); ?></td>
                                                <td style="text-align:right;"><?php echo number_format((float)(isset($key->basic_amount) ? $key->basic_amount : 0), 2); ?></td>
                                                <td style="text-align:right;"><?php echo number_format((float)$key->expense_amount, 2); ?></td>
                                                <td><?php echo htmlspecialchars($key->expense_note); ?></td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background: #f5f5f5; font-weight: bold;">
                                            <td colspan="5" style="text-align: right;">Total:</td>
                                            <td style="text-align:right;"><?php echo number_format($total_basic, 2); ?></td>
                                            <td style="text-align:right;"><?php echo number_format($total_amount, 2); ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } elseif (isset($result) && empty($result)) { ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-warning">
                            <div class="box-body">
                                <div class="alert alert-info text-center">
                                    <strong>No records found</strong> for the selected filters.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <script>
    $(document).ready(function() {
        $('#from_date, #to_date').datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });

        $('input[name="show_all"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('#from_date, #to_date').prop('disabled', true).val('');
            } else {
                $('#from_date, #to_date').prop('disabled', false);
            }
        });
        
        if ($('input[name="show_all"]').is(':checked')) {
            $('#from_date, #to_date').prop('disabled', true);
        }
    });
    </script>
</body>