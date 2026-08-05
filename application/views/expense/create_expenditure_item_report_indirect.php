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
$selected_employee = isset($selected_employee) ? $selected_employee : '';
$is_individual_category = isset($is_individual_category) ? (bool) $is_individual_category : false;
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Indirect Expenditure Report</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Indirect Expenditure Report</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/get_indirect_expense_report">
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
                                                        $cat_type = '';
                                                        
                                                        // Clean up display name
                                                        if (stripos($cat_value, 'Indirect - ') === 0) {
                                                            $cat_display = trim(substr($cat_value, strlen('Indirect - ')));
                                                        }
                                                        if (preg_match('/^(Individual|Corporate)\s*-\s*(.*)$/i', $cat_display, $matches)) {
                                                            $cat_type = strtolower(trim($matches[1]));
                                                        }
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($cat_value); ?>"
                                                        data-expense-type="<?php echo htmlspecialchars($cat_type); ?>" 
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
                                    <div class="form-group" id="employee_name_group" style="<?php echo $is_individual_category ? '' : 'display:none;'; ?>">
                                        <label for="employee_name" class="col-sm-3 control-label">Employee Name</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm" name="employee_name" id="employee_name">
                                                <option value="">All Employees</option>
                                                <?php if (isset($individuals) && !empty($individuals)) {
                                                    foreach ($individuals as $ind) { ?>
                                                        <option value="<?php echo htmlspecialchars($ind->employee_name); ?>" <?php echo ($selected_employee == $ind->employee_name) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($ind->employee_name); ?><?php echo !empty($ind->code) ? ' (' . htmlspecialchars($ind->code) . ')' : ''; ?>
                                                        </option>
                                                <?php }
                                                } ?>
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
            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <script>
    $(document).ready(function() {
        // Make date fields optional - remove required attribute
        $('#from_date, #to_date').removeAttr('required');
        
        // If "Show All" is checked, disable date fields
        $('input[name="show_all"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('#from_date, #to_date').prop('disabled', true);
                $('#from_date, #to_date').val('');
            } else {
                $('#from_date, #to_date').prop('disabled', false);
            }
        });
        
        // Trigger on page load if already checked
        if ($('input[name="show_all"]').is(':checked')) {
            $('#from_date, #to_date').prop('disabled', true);
        }

        function toggleEmployeeField() {
            var selectedType = $('#expense_category option:selected').data('expense-type') || '';
            if (selectedType === 'individual') {
                $('#employee_name_group').show();
            } else {
                $('#employee_name_group').hide();
                $('#employee_name').val('');
            }
        }

        $('#expense_category').on('change', toggleEmployeeField);
        toggleEmployeeField();
    });
    </script>
</body>
