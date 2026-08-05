<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    PO Revision History
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SupplierController/view_purchase_order/' ?>">Purchase Orders</a></li>
                    <li><a href="<?php echo base_url('SupplierController/show_po/' . str_replace('/', '-', $po_data['number'])); ?>">PO Details</a></li>
                    <li class="active">Revision History</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Revision History for: <?php echo $po_data['number']; ?></h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Back to PO</a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Revision</th>
                                                <th>Amendment No</th>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th>Value Change</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Revised PO</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($revisions)): ?>
                                                <tr>
                                                    <td colspan="9" class="text-center">No revisions found for this PO</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($revisions as $revision): ?>
                                                    <tr>
                                                        <td>R<?php echo $revision['revision_number']; ?></td>
                                                        <td><?php echo $revision['amendment_no']; ?></td>
                                                        <td><?php echo ucfirst(str_replace('_', ' ', $revision['amendment_type'])); ?></td>
                                                        <td><?php echo $revision['description']; ?></td>
                                                        <td>₹<?php echo number_format($revision['amendment_value'], 2); ?></td>
                                                        <td>
                                                            <span class="label label-<?php
                                                                                        switch ($revision['status']) {
                                                                                            case 'approved':
                                                                                                echo 'success';
                                                                                                break;
                                                                                            case 'pending_approval':
                                                                                                echo 'warning';
                                                                                                break;
                                                                                            case 'rejected':
                                                                                                echo 'danger';
                                                                                                break;
                                                                                            case 'vendor_acknowledged':
                                                                                                echo 'info';
                                                                                                break;
                                                                                            case 'revised_po_issued':
                                                                                                echo 'primary';
                                                                                                break;
                                                                                            default:
                                                                                                echo 'default';
                                                                                        }
                                                                                        ?>">
                                                                <?php echo ucfirst(str_replace('_', ' ', $revision['status'])); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('d-M-Y H:i', strtotime($revision['initiated_date'])); ?></td>
                                                        <td>
                                                            <?php if ($revision['new_revised_po_number']): ?>
                                                                <a href="<?php echo base_url('SupplierController/show_po/' . str_replace('/', '-', $revision['new_revised_po_number'])); ?>">
                                                                    <?php echo $revision['new_revised_po_number']; ?>
                                                                </a>
                                                            <?php else: ?>
                                                                Not Created
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="<?php echo base_url('PoamendmentController/view/' . $revision['amendment_id']); ?>"
                                                                class="btn btn-xs btn-info" title="View Details">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <?php if ($revision['status'] == 'approved' && !$revision['is_revision_created']): ?>
                                                                <a href="<?php echo base_url('PoamendmentController/create_revision/' . $revision['amendment_id']); ?>"
                                                                    class="btn btn-xs btn-success" title="Create Revision">
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Create New Amendment Button -->
                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-12 text-center">
                                        <a href="<?php echo base_url('PoamendmentController/create?po_id=' . $po_data['id']); ?>"
                                            class="btn btn-lg btn-warning">
                                            <i class="fa fa-edit"></i> Create New Amendment
                                        </a>
                                    </div>
                                </div>
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
</body>