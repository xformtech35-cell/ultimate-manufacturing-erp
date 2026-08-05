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
            <section class="content-header">
                <h1>PO Amendment Dashboard</h1>
            </section>

            <section class="content">
                <!-- Stats Row -->
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-gray"><i class="fa fa-file-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Draft</span>
                                <span class="info-box-number"><?= $counts['draft'] ?? 0 ?></span>
                                <a href="<?= base_url('PoamendmentController/index?status=draft') ?>" class="small-box-footer">
                                    View <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pending Approval</span>
                                <span class="info-box-number"><?= $counts['pending_approval'] ?? 0 ?></span>
                                <a href="<?= base_url('PoamendmentController/index?status=pending_approval') ?>" class="small-box-footer">
                                    View <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Awaiting Vendor</span>
                                <span class="info-box-number"><?= $counts['awaiting_vendor'] ?? 0 ?></span>
                                <a href="<?= base_url('PoamendmentController/index?status=approved') ?>" class="small-box-footer">
                                    View <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-building-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Awaiting Revised PO</span>
                                <span class="info-box-number"><?= $counts['awaiting_revised_po'] ?? 0 ?></span>
                                <a href="<?= base_url('PoamendmentController/index?status=vendor_acknowledged') ?>" class="small-box-footer">
                                    View <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Quick Actions</h3>
                            </div>
                            <div class="box-body text-center">
                                <a href="<?= base_url('PoamendmentController/create') ?>" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus"></i> Create New Amendment
                                </a>
                                <a href="<?= base_url('PoamendmentController/approvals') ?>" class="btn btn-warning btn-lg">
                                    <i class="fa fa-check-circle"></i> View My Approvals
                                </a>
                                <a href="<?= base_url('PoamendmentController/index') ?>" class="btn btn-info btn-lg">
                                    <i class="fa fa-list"></i> View All Amendments
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Amendments -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Recent Amendments</h3>
                            </div>
                            <div class="box-body">
                                <?php if (empty($recent_amendments)): ?>
                                    <div class="alert alert-info">No recent amendments</div>
                                <?php else: ?>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Amendment No</th>
                                                <th>PO Number</th>
                                                <th>Vendor</th>
                                                <th>Type</th>
                                                <th>Initiated Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_amendments as $amendment): ?>
                                                <tr>
                                                    <td><strong><?= $amendment['amendment_no'] ?></strong></td>
                                                    <td><?= $amendment['po_number'] ?></td>
                                                    <td><?= $amendment['vendor_name'] ?></td>
                                                    <td><?= ucfirst(str_replace('_', ' ', $amendment['amendment_type'])) ?></td>
                                                    <td><?= date('d-m-Y H:i', strtotime($amendment['initiated_date'])) ?></td>
                                                    <td>
                                                        <?php
                                                        $status_labels = array(
                                                            'draft' => 'label-default',
                                                            'pending_approval' => 'label-warning',
                                                            'approved' => 'label-info',
                                                            'vendor_acknowledged' => 'label-primary',
                                                            'revised_po_issued' => 'label-success',
                                                            'completed' => 'label-success',
                                                            'cancelled' => 'label-danger'
                                                        );
                                                        $label_class = isset($status_labels[$amendment['status']]) ? $status_labels[$amendment['status']] : 'label-default';
                                                        ?>
                                                        <span class="label <?= $label_class ?>">
                                                            <?= ucfirst(str_replace('_', ' ', $amendment['status'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('PoamendmentController/view/' . $amendment['amendment_id']) ?>"
                                                            class="btn btn-xs btn-info">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                            <div class="box-footer text-center">
                                <a href="<?= base_url('PoamendmentController/index') ?>" class="btn btn-default">
                                    View All Amendments <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
</body>