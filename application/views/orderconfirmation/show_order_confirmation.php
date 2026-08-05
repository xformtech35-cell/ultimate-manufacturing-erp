<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Order Confirmation Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url(); ?>OrderConfirmationController/index">Order Confirmation</a></li>
                    <li class="active">View OC</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">OC #<?php echo $oc['number_fk']; ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/print_order_confirmation/<?php echo $oc['number_fk']; ?>" class="btn btn-info btn-sm" target="_blank">
                                        <i class="fa fa-print"></i> Print
                                    </a>
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/edit_order_confirmation_details/<?php echo $oc['number_fk']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/delete_order_confirmation/<?php echo $oc['number_fk']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this Order Confirmation?');">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <!-- Flash Messages -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                        <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <!-- Status Update Buttons -->
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            <?php 
                                            $status = $oc['status'];
                                            $status_badge = '';
                                            $status_class = '';
                                            switch($status) {
                                                case 1:
                                                    $status_badge = 'Draft';
                                                    $status_class = 'label-warning';
                                                    break;
                                                case 2:
                                                    $status_badge = 'Sent/Confirmed';
                                                    $status_class = 'label-info';
                                                    break;
                                                case 3:
                                                    $status_badge = 'Accepted';
                                                    $status_class = 'label-success';
                                                    break;
                                                case 4:
                                                    $status_badge = 'Rejected';
                                                    $status_class = 'label-danger';
                                                    break;
                                                case 5:
                                                    $status_badge = 'Cancelled';
                                                    $status_class = 'label-default';
                                                    break;
                                            }
                                            ?>
                                            <span class="label <?php echo $status_class; ?>" style="font-size: 14px; padding: 8px 15px;">Current Status: <?php echo $status_badge; ?></span>
                                            
                                            <?php if($status == 1) { ?>
                                                <a href="<?php echo base_url(); ?>OrderConfirmationController/update_status/<?php echo $oc['number_fk']; ?>/2" class="btn btn-xs btn-info">Mark as Sent/Confirmed</a>
                                            <?php } ?>
                                            <?php if($status == 2) { ?>
                                                <a href="<?php echo base_url(); ?>OrderConfirmationController/update_status/<?php echo $oc['number_fk']; ?>/3" class="btn btn-xs btn-success">Mark as Accepted</a>
                                                <a href="<?php echo base_url(); ?>OrderConfirmationController/update_status/<?php echo $oc['number_fk']; ?>/4" class="btn btn-xs btn-danger">Mark as Rejected</a>
                                            <?php } ?>
                                            <?php if($status == 3 || $status == 4) { ?>
                                                <a href="<?php echo base_url(); ?>OrderConfirmationController/update_status/<?php echo $oc['number_fk']; ?>/5" class="btn btn-xs btn-default">Cancel</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Header Information -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">OC Number</th>
                                                <td><?php echo $oc['number_fk']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <td><?php echo date('d-M-Y', strtotime($oc['date'])); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Supplier</th>
                                                <td><?php echo isset($oc['company_name']) ? $oc['company_name'] : '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <th>PO Reference</th>
                                                <td><?php echo $oc['po_reference'] ? $oc['po_reference'] : '-'; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Expected Delivery Date</th>
                                                <td><?php echo $oc['delivery_date'] ? date('d-M-Y', strtotime($oc['delivery_date'])) : '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Payment Terms</th>
                                                <td><?php echo $oc['payment_terms'] ? $oc['payment_terms'] : '-'; ?></td>
                                            </tr>
                                             <?php if ($_has_project_master): ?>
                                             <tr>
                                                 <th>Project Code</th>
                                                 <td><?php echo $oc['project_code'] ? $oc['project_code'] : '-'; ?></td>
                                             </tr>
                                             <?php endif; ?>
                                            <tr>
                                                <th>Status</th>
                                                <td><span class="label <?php echo $status_class; ?>"><?php echo $status_badge; ?></span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <?php if($oc['remarks']) { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <p><?php echo $oc['remarks']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>

                                <hr>

                                <!-- Items Table -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>OC Items</h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Description</th>
                                                        <th>HSN Code</th>
                                                        <th>Quantity</th>
                                                        <th>Unit</th>
                                                        <th>Unit Price</th>
                                                        <th>Tax Rate</th>
                                                        <th>Tax Amount</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    if(isset($oc_detail) && !empty($oc_detail)) {
                                                        $i = 1;
                                                        foreach($oc_detail as $detail) {
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $i; ?></td>
                                                            <td><?php echo $detail['description']; ?></td>
                                                            <td><?php echo $detail['hsn_code'] ? $detail['hsn_code'] : '-'; ?></td>
                                                            <td><?php echo number_format($detail['quantity'], 2); ?></td>
                                                            <td><?php echo $detail['unit'] ? $detail['unit'] : '-'; ?></td>
                                                            <td><?php echo number_format($detail['unit_price'], 2); ?></td>
                                                            <td><?php echo number_format($detail['tax_rate'], 2); ?></td>
                                                            <td><?php echo number_format($detail['tax_amount'], 2); ?></td>
                                                            <td><?php echo number_format($detail['amount'], 2); ?></td>
                                                        </tr>
                                                    <?php 
                                                            $i++;
                                                        }
                                                    } else {
                                                    ?>
                                                        <tr>
                                                            <td colspan="9" class="text-center">No items found.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="8" class="text-right">Sub Total</th>
                                                        <th><?php echo number_format($oc['sub_total'], 2); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="8" class="text-right">Tax Amount</th>
                                                        <th><?php echo number_format($oc['tax_amount'], 2); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="8" class="text-right">Total Amount</th>
                                                        <th><?php echo number_format($oc['total'], 2); ?></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2024</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->
    
    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="<?php echo base_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>

