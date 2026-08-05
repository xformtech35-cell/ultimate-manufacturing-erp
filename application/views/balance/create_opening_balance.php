<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    redirect('LoginController/logout');
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Opening Balance</title>
    <style>
        .text-danger { color: #dd4b39; }
        .help-block { display: block; margin-top: 5px; color: #737373; }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Create Opening Balance
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('Home/index'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('BalanceController/opening_balance_index'); ?>">Opening Balances</a></li>
                    <li class="active">Create Opening Balance</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Opening Balance Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success alert-dismissible">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                        <strong>Well done!!</strong> <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('ERRORMSG')) { ?>
                                    <div role="alert" class="alert alert-danger alert-dismissible">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                        <strong>Error!!</strong> <?php echo $this->session->flashdata('ERRORMSG'); ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info alert-dismissible">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                        <strong>Info!!</strong> <?php echo $this->session->flashdata('INFOMSG'); ?>
                                    </div>
                                <?php } ?>

                                <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

                                <form method="post" action="<?php echo base_url('BalanceController/store_opening_balance'); ?>" role="form">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_id">Account / Customer / Vendor <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="customer_id" name="customer_id" required>
                                                        <option value="">-- Select Account / Customer / Vendor --</option>
                                                        <?php if (!empty($accounts)): ?>
                                                            <?php foreach ($accounts as $account): ?>
                                                                <option value="<?php echo $account->account_id; ?>">
                                                                    <?php
                                                                    $account_label = $account->company_name;
                                                                    if (!empty($account->account_code)) {
                                                                        $account_label .= ' - ' . $account->account_code;
                                                                    }
                                                                    echo htmlspecialchars($account_label . ' (' . $account->account_type . ')');
                                                                    ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="account_name">Account Name</label>
                                                    <input type="text" class="form-control" id="account_name" name="account_name" 
                                                           placeholder="Enter account name (optional)">
                                                    <small class="help-block">If left empty, selected account name will be used</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="opening_balance_amount">Opening Balance Amount <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">₹</span>
                                                        <input type="number" step="0.01" class="form-control" id="opening_balance_amount" 
                                                               name="opening_balance_amount" required>
                                                    </div>
                                                    <small class="help-block">This amount will show in Credit in customer and vendor ledger reports.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="balance_date">Balance Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="balance_date" name="balance_date" 
                                                           value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <textarea class="form-control" id="description" name="description" 
                                                              rows="3" placeholder="Optional notes..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> Create Opening Balance
                                        </button>
                                        <a href="<?php echo base_url('BalanceController/opening_balance_index'); ?>" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to List
                                        </a>
                                        <button type="reset" class="btn btn-warning">
                                            <i class="fa fa-refresh"></i> Reset
                                        </button>
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
            // Initialize select2 if available
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2();
            }
            
            // Optional: Auto-fill account name when account is selected
            $('#customer_id').on('change', function() {
                var accountName = $(this).find('option:selected').text();
                if ($('#account_name').val() === '') {
                    var nameOnly = accountName.split(' - ')[0].replace(/\s+\((Customer|Vendor)\)\s*$/, '');
                    $('#account_name').val(nameOnly);
                }
            });
        });
    </script>
</body>
</html>
