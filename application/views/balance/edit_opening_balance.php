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
    <title>Edit Opening Balance</title>
    <style>
        .required label {
            font-weight: bold;
        }
        .required label:after {
            color: #e32;
            content: '*';
            display: inline;
        }
        
        .text-danger {
            color: #dd4b39;
        }
        .help-block {
            display: block;
            margin-top: 5px;
            color: #737373;
        }
        
        /* Modal styles */
        .modal-header.bg-info {
            background-color: #5bc0de;
            color: white;
        }
        .modal-header.bg-success {
            background-color: #5cb85c;
            color: white;
        }
        .modal-header.bg-warning {
            background-color: #f0ad4e;
            color: white;
        }
        .modal-header.bg-primary {
            background-color: #337ab7;
            color: white;
        }
        .modal-header.bg-danger {
            background-color: #d9534f;
            color: white;
        }
        
        /* CKEditor modal styling */
        .cke_chrome {
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
        }
        
        /* Table styling */
        .table-responsive {
            overflow-x: auto;
        }
        
        #dynamic_field td {
            vertical-align: middle;
        }
        
        .select2-container {
            width: 100% !important;
        }
        
        .btn-xs {
            margin: 2px;
        }
        
        /* Action header button styling */
        .action-header-btn {
            margin-left: 5px;
        }
        
        .hide {
            display: none;
        }
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
                    Edit Opening Balance
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('Home/index'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('BalanceController/opening_balance_index'); ?>">Opening Balance</a></li>
                    <li class="active">Edit Opening Balance Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Opening Balance</h3>
                                <a href="<?php echo base_url('BalanceController/opening_balance_index'); ?>" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>
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

                                <?php echo validation_errors('<div class="alert alert-danger alert-dismissible">', '</div>'); ?>

                                <form method="post" action="<?php echo base_url('BalanceController/update_opening_balance/' . $opening_balance->balance_id); ?>" role="form">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_id">Account / Customer / Vendor <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="customer_id" name="customer_id" required>
                                                        <option value="">-- Select Account / Customer / Vendor --</option>
                                                        <?php foreach ($accounts as $account): ?>
                                                            <?php
                                                            $is_selected = isset($opening_balance->account_name) && strpos($opening_balance->account_name, $account->company_name) !== false;
                                                            $account_label = $account->company_name;
                                                            if (!empty($account->account_code)) {
                                                                $account_label .= ' - ' . $account->account_code;
                                                            }
                                                            ?>
                                                            <option value="<?php echo $account->account_id; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($account_label . ' (' . $account->account_type . ')'); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="account_name">Account Name</label>
                                                    <input type="text" class="form-control" id="account_name" name="account_name" 
                                                           value="<?php echo isset($opening_balance->account_name) ? htmlspecialchars($opening_balance->account_name) : ''; ?>"
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
                                                               name="opening_balance_amount" value="<?php echo isset($opening_balance->opening_balance_amount) ? $opening_balance->opening_balance_amount : ''; ?>" required>
                                                    </div>
                                                    <small class="help-block">This amount will show in Credit in customer and vendor ledger reports.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="balance_date">Balance Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="balance_date" name="balance_date" 
                                                           value="<?php echo isset($opening_balance->balance_date) ? $opening_balance->balance_date : date('Y-m-d'); ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description">Description / Notes</label>
                                                    <textarea class="form-control" id="description" name="description" 
                                                              rows="3" placeholder="Enter any additional notes or description"><?php echo isset($opening_balance->description) ? htmlspecialchars($opening_balance->description) : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Update Opening Balance
                                        </button>
                                        <a href="<?php echo base_url('BalanceController/opening_balance_index'); ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> Cancel
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

    <script>
    $(document).ready(function() {
        // Initialize select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                placeholder: "-- Select Account / Customer / Vendor --",
                allowClear: true
            });
        }
        
        // Optional: Auto-fill account name when account is selected
        $('#customer_id').on('change', function() {
            var accountName = $(this).find('option:selected').text();
            if ($('#account_name').val() === '' || $('#account_name').val() === $('#account_name').attr('data-original')) {
                var nameOnly = accountName.split(' - ')[0].replace(/\s+\((Customer|Vendor)\)\s*$/, '');
                $('#account_name').val(nameOnly);
            }
        });
        
        // Store original account name
        $('#account_name').attr('data-original', $('#account_name').val());
        
        // Add confirmation before reset
        $('button[type="reset"]').on('click', function(e) {
            if (!confirm('Are you sure you want to reset all changes?')) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>
