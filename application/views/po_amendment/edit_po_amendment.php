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
                <h1>Edit PO Amendment</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('PoamendmentController/index'); ?>">PO Amendments</a></li>
                    <li><a href="<?php echo base_url('PoamendmentController/view/' . $amendment['amendment_id']); ?>">View Amendment</a></li>
                    <li class="active">Edit Amendment</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Amendment: <?php echo $amendment['amendment_no']; ?></h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php echo form_open('PoamendmentController/edit/' . $amendment['amendment_id'], array('class' => 'form-horizontal form_overlay', 'id' => 'editAmendmentForm')); ?>

                                <div class="card-body ">

                                    <!-- Amendment Type -->
                                    <div class="form-group row">
                                        <label for="amendment_type" class="col-sm-3 control-label">Amendment Type *</label>
                                        <div class="col-sm-8">
                                            <select name="amendment_type" class="form-control" required>
                                                <option value="">Select Type</option>
                                                <option value="design_change" <?php echo $amendment['amendment_type'] == 'design_change' ? 'selected' : ''; ?>>Design Change</option>
                                                <option value="spec_change" <?php echo $amendment['amendment_type'] == 'spec_change' ? 'selected' : ''; ?>>Specification Change</option>
                                                <option value="drawing_change" <?php echo $amendment['amendment_type'] == 'drawing_change' ? 'selected' : ''; ?>>Drawing Change</option>
                                                <option value="price_change" <?php echo $amendment['amendment_type'] == 'price_change' ? 'selected' : ''; ?>>Price Change</option>
                                                <option value="quantity_change" <?php echo $amendment['amendment_type'] == 'quantity_change' ? 'selected' : ''; ?>>Quantity Change</option>
                                                <option value="delivery_change" <?php echo $amendment['amendment_type'] == 'delivery_change' ? 'selected' : ''; ?>>Delivery Change</option>
                                                <option value="other" <?php echo $amendment['amendment_type'] == 'other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group row">
                                        <label for="description" class="col-sm-3 control-label">Description *</label>
                                        <div class="col-sm-8">
                                            <textarea name="description" class="form-control" rows="3" required><?php echo $amendment['description']; ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Reason -->
                                    <div class="form-group row">
                                        <label for="reason" class="col-sm-3 control-label">Reason for Amendment *</label>
                                        <div class="col-sm-8">
                                            <textarea name="reason" class="form-control" rows="3" required><?php echo $amendment['reason']; ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Read-only Fields -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">Amendment No</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="<?php echo $amendment['amendment_no']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">PO Number</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="<?php echo $amendment['po_number']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">Status</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control"
                                                value="<?php echo ucfirst(str_replace('_', ' ', $amendment['status'])); ?>" readonly>
                                        </div>
                                    </div>

                                </div>

                                <div class="card-footer">
                                    <button type="button" id="back" class="btn btn-default"
                                        onclick="window.history.back();">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">
                                        <i class="fa fa-save"></i> Update Amendment
                                    </button>
                                </div>

                                <?php echo form_close(); ?>

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