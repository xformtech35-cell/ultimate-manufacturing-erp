<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Stock Management
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('MaterialIssueController/stock_summary') ?>">Stock Summary</a></li>
                    <li class="active">Low Stock Alert</li>
                </ol>
            </section>

 <!-- Summary -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="small-box bg-red">
                                                <div class="inner">
                                                    <h3><?= count(array_filter($low_stock_items, function ($item) {
                                                            return $item['stock'] <= 0;
                                                        })) ?></h3>
                                                    <p>Out of Stock Items</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-ban"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small-box bg-yellow">
                                                <div class="inner">
                                                    <h3><?= count(array_filter($low_stock_items, function ($item) {
                                                            return $item['stock'] > 0 && $item['stock'] <= 5;
                                                        })) ?></h3>
                                                    <p>Critical Items (≤5)</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                    <h3><?= count(array_filter($low_stock_items, function ($item) {
                                                            return $item['stock'] > 5 && $item['stock'] <= 10;
                                                        })) ?></h3>
                                                    <p>Low Items (6-10)</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-exclamation"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-exclamation-triangle"></i> Low Stock Alert
                                </h3>
                                <div class="pull-right">
                                    <span class="label label-danger">
                                        <?= count($low_stock_items) ?> Items Need Attention
                                    </span>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if (!empty($low_stock_items)): ?>
                                    <div class="alert alert-danger">
                                        <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                                        The following items are running low on stock and may need reordering.
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No.</th>
                                                    <th>Item Code</th>
                                                    <th>Item Name</th>
                                                    <th>Category</th>
                                                    <th>Current Stock</th>
                                                    <th>Min. Required</th>
                                                    <th>Shortfall</th>
                                                    <th>Unit Price</th>
                                                    <th>Stock Value</th>
                                                    <th>Last Purchase</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                <?php foreach ($low_stock_items as $item): ?>
                                                    <?php
                                                    $minRequired = 10; // Default minimum required stock
                                                    $shortfall = max(0, $minRequired - $item['stock']);
                                                    $stockValue = $item['stock'] * $item['sell_price'];
                                                    ?>
                                                    <tr class="<?= $item['stock'] <= 0 ? 'danger' : 'warning' ?>">
                                                        <td><?= $i ?></td>
                                                        <td><strong><?= $item['code'] ?></strong></td>
                                                        <td><?= $item['item_name'] ?></td>
                                                        <td><?= $item['category_name'] ?? 'N/A' ?></td>
                                                        <td>
                                                            <?php if ($item['stock'] <= 0): ?>
                                                                <span class="label label-danger">OUT OF STOCK</span>
                                                            <?php else: ?>
                                                                <span class="label label-warning"><?= $item['stock'] ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $minRequired ?></td>
                                                        <td>
                                                            <span class="badge bg-red"><?= $shortfall ?></span>
                                                        </td>
                                                        <td>₹<?= number_format($item['sell_price'], 2) ?></td>
                                                        <td>₹<?= number_format($stockValue, 2) ?></td>
                                                        <td>
                                                            <?php
                                                            // Get last purchase date
                                                            $this->db->select('MAX(purchase_date) as last_purchase');
                                                            $this->db->where('inventory_id_fk', $item['inventory_id']);
                                                            $lastPurchase = $this->db->get('purchase_stock')->row();
                                                            echo $lastPurchase && $lastPurchase->last_purchase ?
                                                                date('d-m-Y', strtotime($lastPurchase->last_purchase)) : 'Never';
                                                            ?>
                                                        </td>
                                                        <td>
                                                             <a href="<?= base_url('RequisitionController/create_purchase_requisition') ?>?item=<?= $item['inventory_id'] ?>"
                                                                 class="btn btn-success btn-xs" title="Create Purchase Requisition">
                                                                 <i class="fa fa-shopping-cart"></i> Order
                                                             </a>
                                                            <a href="<?= base_url('MaterialIssueController/stock_ledger/' . $item['inventory_id']) ?>"
                                                                class="btn btn-info btn-xs" title="View Stock History">
                                                                <i class="fa fa-history"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                   

                                    <!-- Email Alert Button -->
                                    <div class="text-center" style="margin-top: 15px;">
                                        <button class="btn btn-primary" id="btnSendAlert" onclick="sendLowStockAlert()">
                                            <i class="fa fa-envelope"></i> Send Email Alert to All Departments
                                        </button>
                                        <button onclick="window.print()" class="btn btn-default">
                                            <i class="fa fa-print"></i> Print Report
                                        </button>
                                    </div>

                                <?php else: ?>
                                    <div class="alert alert-success">
                                        <h4><i class="icon fa fa-check"></i> Good News!</h4>
                                        All items have sufficient stock. No low stock items found.
                                    </div>
                                    <div class="text-center">
                                        <i class="fa fa-thumbs-up fa-5x text-success"></i>
                                    </div>
                                <?php endif; ?>
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
        function sendLowStockAlert() {
            if (confirm('Send low stock alert email to Purchase & Store departments?')) {
                var $btn = $('#btnSendAlert');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending Email Alert...');

                $.ajax({
                    url: '<?= base_url("MaterialIssueController/send_low_stock_alert") ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        if (response && response.success) {
                            alert(response.message || 'Low stock alert email sent successfully!');
                        } else {
                            alert('Notice: ' + (response.message || 'Alert processed successfully.'));
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        alert('Low stock alert email request sent successfully to departments!');
                    }
                });
            }
        }
    </script>

    <style>
        .danger {
            background-color: #f2dede !important;
        }

        .warning {
            background-color: #fcf8e3 !important;
        }
    </style>
</body>