<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    redirect('LoginController/logout');
}

$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = isset($session_data_head2['cc_email']) ? $session_data_head2['cc_email'] : '';

defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Opening Balance Management</title>
    <style>
        .text-danger { color: #dd4b39; }
        .text-success { color: #00a65a; }
        .balance-positive { color: green; text-align: right; }
        .balance-negative { color: red; text-align: right; }
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
                    Opening Balance Management
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('Home/index'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Opening Balance</a></li>
                    <li class="active">Opening Balance details</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
    <div class="row">
        <div class="col-md-6">
            <h3 class="box-title">Opening Balance Details</h3>
        </div>
        <div class="col-md-6 text-right">
            <div class="btn-group-header">
                <a href="<?php echo base_url('BalanceController/opening_balance_index'); ?>" class="btn btn-success btn-sm">
                    <i class="glyphicon glyphicon-list"></i> Show Opening Balance
                </a>
                <a href="<?php echo base_url('BalanceController/create_opening_balance'); ?>" class="btn btn-primary btn-sm">
                    <i class="glyphicon glyphicon-plus"></i> Create Opening Balance
                </a>
            </div>
        </div>
    </div>
</div>

                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
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
                                <!-- End Flash Message -->

                                <table class="table table-bordered table-striped table-hover" id="opening_balance_table">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.No</th>
                                            <th width="20%">Customer Name</th>
                                            <th width="20%">Account Name</th>
                                            <th width="15%">Opening Balance (₹)</th>
                                            <th width="15%">Balance Date</th>
                                            <th width="15%">Description</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1; foreach ($opening_balances as $balance) { ?>
                                            <tr>
                                                <td><?php echo $count++; ?></td>
                                                <td><?php echo isset($balance->company_name) ? htmlspecialchars($balance->company_name) : 'N/A'; ?></td>
                                                <td><?php echo isset($balance->account_name) ? htmlspecialchars($balance->account_name) : 'N/A'; ?></td>
                                                <td class="<?php echo ($balance->opening_balance_amount < 0) ? 'balance-negative' : 'balance-positive'; ?>">
                                                    <?php echo number_format(floatval($balance->opening_balance_amount), 2); ?>
                                                </td>
                                                <td><?php echo date('d-m-Y', strtotime($balance->balance_date)); ?></td>
                                                <td><?php echo htmlspecialchars(substr($balance->description, 0, 50)); ?></td>
                                                <td>
                                                    <a href="<?php echo base_url('BalanceController/edit_opening_balance/' . $balance->balance_id); ?>" 
                                                       class="btn btn-xs btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo base_url('BalanceController/delete_opening_balance/' . $balance->balance_id); ?>" 
                                                       class="btn btn-xs btn-danger" title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this opening balance?');">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" style="text-align:right">Total:</th>
                                            <th style="text-align:right">
                                                <?php 
                                                $total = 0;
                                                foreach ($opening_balances as $balance) {
                                                    $total += floatval($balance->opening_balance_amount);
                                                }
                                                echo number_format($total, 2);
                                                ?>
                                            </th>
                                            <th colspan="3"></th>
                                        </tr>
                                    </tfoot>
                                </table>
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
        // Fix for DataTables reinitialization error
        // Check if DataTable is already initialized
        if ($.fn.DataTable) {
            if ($.fn.dataTable.isDataTable('#opening_balance_table')) {
                // If already a DataTable, destroy it first
                $('#opening_balance_table').DataTable().destroy();
            }
            
            // Initialize DataTable with destroy option as safety measure
            $('#opening_balance_table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 25,
                "destroy": true, // This prevents reinitialization errors
                "language": {
                    "emptyTable": "No opening balances found",
                    "zeroRecords": "No matching records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "search": "Search Opening Balances:",
                    "lengthMenu": "Show _MENU_ entries",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        }
    });
    </script>
</body>
</html>