<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="wrapper">

        <div class="content-wrapper">

            <section class="content-header">
                <h1>Request For Quotation (RFQ)</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('Home/index') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">RFQs</li>
                </ol>
            </section>

            <section class="content">

                <div class="row">
                    <div class="col-xs-12">

                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">All RFQs</h3>
                                <div class="box-tools pull-right">
                                    <a href="<?php echo base_url('RequisitionController/view_requisition_order?str=All'); ?>" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create RFQ
                                    </a>
                                </div>
                            </div>

                            <div class="box-body">

                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div class="alert alert-success">
                                        <strong>Success!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <table class="table table-bordered table-striped" id="example3">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>RFQ No</th>
                                            <th>PR Number</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php $i = 1;
                                        foreach ($rfqs as $row): ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $row->rfq_no ?></td>
                                                <td><?= $row->pr_no ?></td>
                                                <td><?= date('d-m-Y', strtotime($row->rfq_date)) ?></td>
                                                <td><?= $row->status ?></td>
                                                <td><?= $row->created_by_name ?></td>

                                                <td>
                                                    <a href="<?php echo base_url('RFQController/show_rfq/' . $row->rfq_id); ?>"
                                                        class="btn btn-primary btn-sm">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>
                </div>

            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
    </div>
</body>