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
                    PO Amendments
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">PO Amendments</a></li>
                    <li class="active">List Amendments</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">All PO Amendments</h3>
                                <div class="box-tools pull-right">
                                    <a href="<?php echo base_url('PoamendmentController/create'); ?>" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create Amendment
                                    </a>
                                    <a href="<?php echo base_url('PoamendmentController/dashboard'); ?>" class="btn btn-info btn-sm">
                                        <i class="fa fa-dashboard"></i> Dashboard
                                    </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <!-- Filters -->
                                <form method="get" class="form-horizontal" style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #eee;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Amendment No</label>
                                                <input type="text" name="amendment_no" class="form-control input-sm"
                                                    value="<?php echo $this->input->get('amendment_no'); ?>"
                                                    placeholder="Amendment No">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>PO Number</label>
                                                <input type="text" name="po_number" class="form-control input-sm"
                                                    value="<?php echo $this->input->get('po_number'); ?>"
                                                    placeholder="PO Number">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control input-sm">
                                                    <option value="">All Status</option>
                                                    <option value="draft" <?php echo $this->input->get('status') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                    <option value="pending_approval" <?php echo $this->input->get('status') == 'pending_approval' ? 'selected' : ''; ?>>Pending Approval</option>
                                                    <option value="approved" <?php echo $this->input->get('status') == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="vendor_acknowledged" <?php echo $this->input->get('status') == 'vendor_acknowledged' ? 'selected' : ''; ?>>Vendor Acknowledged</option>
                                                    <option value="revised_po_issued" <?php echo $this->input->get('status') == 'revised_po_issued' ? 'selected' : ''; ?>>Revised PO Issued</option>
                                                    <option value="completed" <?php echo $this->input->get('status') == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select name="amendment_type" class="form-control input-sm">
                                                    <option value="">All Types</option>
                                                    <option value="design_change" <?php echo $this->input->get('amendment_type') == 'design_change' ? 'selected' : ''; ?>>Design Change</option>
                                                    <option value="spec_change" <?php echo $this->input->get('amendment_type') == 'spec_change' ? 'selected' : ''; ?>>Spec Change</option>
                                                    <option value="drawing_change" <?php echo $this->input->get('amendment_type') == 'drawing_change' ? 'selected' : ''; ?>>Drawing Change</option>
                                                    <option value="price_change" <?php echo $this->input->get('amendment_type') == 'price_change' ? 'selected' : ''; ?>>Price Change</option>
                                                    <option value="quantity_change" <?php echo $this->input->get('amendment_type') == 'quantity_change' ? 'selected' : ''; ?>>Quantity Change</option>
                                                    <option value="delivery_change" <?php echo $this->input->get('amendment_type') == 'delivery_change' ? 'selected' : ''; ?>>Delivery Change</option>
                                                    <option value="other" <?php echo $this->input->get('amendment_type') == 'other' ? 'selected' : ''; ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group" style="margin-top: 25px;">
                                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                                <a href="<?php echo base_url('PoamendmentController/index'); ?>" class="btn btn-default btn-sm">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Amendments Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Amendment No</th>
                                                <th>PO Number</th>
                                                <th>Type</th>
                                                <th>Initiated Date</th>
                                                <th>Description</th>
                                                <th>Vendor Ack</th>
                                                <th>Revised PO</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($amendments)): ?>
                                                <?php $i = 1; ?>
                                                <?php foreach ($amendments as $amendment): ?>
                                                    <tr>
                                                        <td><?php echo $i++; ?></td>
                                                        <td>
                                                            <strong><?php echo $amendment['amendment_no']; ?></strong>
                                                        </td>
                                                        <td>
                                                            <a href="<?php echo base_url('SupplierController/show_po/' . $amendment['po_number']); ?>" target="_blank">
                                                                <?php echo $amendment['po_number']; ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo ucfirst(str_replace('_', ' ', $amendment['amendment_type'])); ?></td>
                                                        <td><?php echo date('d-m-Y H:i', strtotime($amendment['initiated_date'])); ?></td>
                                                        <td>
                                                            <?php
                                                            // Fixed: Using substr instead of character_limiter
                                                            $description = $amendment['description'];
                                                            if (strlen($description) > 30) {
                                                                echo substr($description, 0, 30) . '...';
                                                            } else {
                                                                echo $description;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($amendment['vendor_acknowledged'] == 1): ?>
                                                                <span class="label label-success">Yes</span><br>
                                                                <small><?php echo date('d-m-Y', strtotime($amendment['vendor_ack_date'])); ?></small>
                                                            <?php else: ?>
                                                                <span class="label label-warning">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($amendment['revised_po_issued'] == 1): ?>
                                                                <span class="label label-success">Yes</span><br>
                                                                <small><?php echo $amendment['revised_po_number']; ?></small>
                                                            <?php else: ?>
                                                                <span class="label label-warning">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
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
                                                            <span class="label <?php echo $label_class; ?>">
                                                                <?php echo ucfirst(str_replace('_', ' ', $amendment['status'])); ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <a href="<?php echo base_url('PoamendmentController/view/' . $amendment['amendment_id']); ?>"
                                                                    class="btn btn-xs btn-info" title="View">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                                <?php if ($amendment['status'] == 'draft'): ?>
                                                                    <a href="<?php echo base_url('PoamendmentController/edit/' . $amendment['amendment_id']); ?>"
                                                                        class="btn btn-xs btn-warning" title="Edit">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <a href="<?php echo base_url('PoamendmentController/delete/' . $amendment['amendment_id']); ?>"
                                                                        class="btn btn-xs btn-danger"
                                                                        onclick="return confirm('Are you sure you want to delete this amendment?');"
                                                                        title="Delete">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="10" class="text-center">No amendments found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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
    <!-- ./wrapper -->
</body>