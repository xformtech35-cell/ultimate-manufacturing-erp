<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
    exit;
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
                <h1>Edit Approval Rule</h1>
                <ol class="breadcrumb">
                    <li><a href="<?= base_url('Home/index/') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?= base_url('ApprovalMatrixController') ?>">Approval Matrix</a></li>
                    <li class="active">Edit Approval Rule</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Approval Rule Details</h3>
                            </div>

                            <div class="box-body">

                                <!-- Flash Messages -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button">
                                            <span aria-hidden="true">x</span>
                                        </button>
                                        <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button">
                                            <span aria-hidden="true">x</span>
                                        </button>
                                        <strong>Info!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <!-- Edit Approval Rule Form -->
                                <form class="form-horizontal form_overlay" method="post" action="<?= base_url('ApprovalMatrixController/edit/' . $rule->id) ?>">
                                    <div class="modal-body">
                                        <div class="card-body">

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Document Type <span style="color:red;">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="document_type" class="form-control" required>
                                                        <option value="">Select Type</option>
                                                        <option value="SO" <?= isset($rule->document_type) && $rule->document_type == 'SO' ? 'selected' : '' ?>>SO (Sales Order)</option>
                                                        <option value="PR" <?= isset($rule->document_type) && $rule->document_type == 'PR' ? 'selected' : '' ?>>PR</option>
                                                        <option value="PO" <?= isset($rule->document_type) && $rule->document_type == 'PO' ? 'selected' : '' ?>>PO</option>
                                                        <option value="PA" <?= isset($rule->document_type) && $rule->document_type == 'PA' ? 'selected' : '' ?>>PO Amendment</option>
                                                        <option value="GRN" <?= isset($rule->document_type) && $rule->document_type == 'GRN' ? 'selected' : '' ?>>GRN</option>
                                                        <option value="BOM" <?= isset($rule->document_type) && $rule->document_type == 'BOM' ? 'selected' : '' ?>>BOM</option>
                                                        <option value="INV_UPDATE" <?= isset($rule->document_type) && $rule->document_type == 'INV_UPDATE' ? 'selected' : '' ?>>Inventory Item Update</option>
                                                        <option value="INV_DELETE" <?= isset($rule->document_type) && $rule->document_type == 'INV_DELETE' ? 'selected' : '' ?>>Inventory Item Delete</option>
                                                        <option value="INV" <?= isset($rule->document_type) && $rule->document_type == 'INV' ? 'selected' : '' ?>>Inventory (Update & Delete)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Level <span style="color:red;">*</span></label>
                                                <div class="col-sm-5">
                                                    <input type="number" name="level" class="form-control" min="1" value="<?= $rule->level ?>" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Approver Role <span style="color:red;">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="approver_role" class="form-control" required>
                                                        <option value="">Select Role</option>
                                                        <?php
                                                        if (!empty($role)) {
                                                            foreach ($role as $r) {
                                                        ?>
                                                                <option value="<?= $r->role_name ?>" <?= ($rule->approver_role == $r->role_name) ? 'selected' : '' ?>>
                                                                    <?= $r->role_name ?>
                                                                </option>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Department</label>
                                                <div class="col-sm-5">
                                                    <select name="department_id" class="form-control">
                                                        <option value="">Select Department (All)</option>
                                                        <?php
                                                        if (!empty($department_result)) {
                                                            foreach ($department_result as $dept) {
                                                        ?>
                                                                <option value="<?= $dept->department_id ?>" <?= (isset($rule->department_id) && $rule->department_id == $dept->department_id) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($dept->department_name) ?>
                                                                </option>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Min Amount</label>
                                                <div class="col-sm-5">
                                                    <input type="number" name="min_amount" class="form-control" step="0.01" value="<?= $rule->min_amount ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Max Amount</label>
                                                <div class="col-sm-5">
                                                    <input type="number" name="max_amount" class="form-control" step="0.01" value="<?= $rule->max_amount ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Custom Alert Message</label>
                                                <div class="col-sm-5">
                                                    <textarea name="notify_message" class="form-control" rows="2" placeholder="Custom message shown to user when request goes for approval (optional)"><?= html_escape($rule->notify_message ?? '') ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Status</label>
                                                <div class="col-sm-5">
                                                    <select name="status" class="form-control">
                                                        <option value="active" <?= $rule->status == 'active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= $rule->status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-5">
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                    <a href="<?= base_url('ApprovalMatrixController') ?>" class="btn btn-default">Cancel</a>
                                                </div>
                                            </div>

                                        </div>
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