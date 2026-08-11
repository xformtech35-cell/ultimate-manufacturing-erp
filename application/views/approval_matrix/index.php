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
                <h1>Approval Matrix</h1>
                <ol class="breadcrumb">
                    <li><a href="<?= base_url('Home/index/') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Approval Matrix</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Approval Rule</h3>
                            </div>

                            <!-- /.box-header -->
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

                                 <div class="alert alert-info" style="margin-top: 10px; margin-bottom: 15px;">
                                     <i class="fa fa-info-circle"></i> <strong>Approval Permission Rule:</strong> Approvals at each level can be performed by users holding the <b>Selected Approver Role</b> OR users with the <b>Admin</b> role.
                                 </div>

                                 <!-- Add Approval Rule Form -->
                                <form class="form-horizontal form_overlay" method="post" action="<?= base_url('ApprovalMatrix/add') ?>">
                                    <div class="modal-body">
                                        <div class="card-body">

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Document Type <span style="color:red;">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="document_type" class="form-control" required>
                                                        <option value="">Select Type</option>
                                                        <option value="SO">SO (Sales Order)</option>
                                                        <option value="PR">PR</option>
                                                        <option value="PO">PO</option>
                                                        <option value="PA">PO Amendment</option>
                                                        <option value="GRN">GRN</option>
                                                        <option value="BOM">BOM</option>
                                                        <option value="INV_UPDATE">Inventory Item Update</option>
                                                        <option value="INV_DELETE">Inventory Item Delete</option>
                                                        <option value="INV">Inventory (Update & Delete)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Level <span style="color:red;">*</span></label>
                                                <div class="col-sm-5">
                                                    <input type="number" name="level" class="form-control" min="1" required>
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
                                                                <option value="<?= $r->role_name ?>"><?= $r->role_name ?></option>
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
                                                                <option value="<?= $dept->department_id ?>"><?= htmlspecialchars($dept->department_name) ?></option>
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
                                                    <input type="number" name="min_amount" class="form-control" step="0.01">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Max Amount</label>
                                                <div class="col-sm-5">
                                                    <input type="number" name="max_amount" class="form-control" step="0.01">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Status</label>
                                                <div class="col-sm-5">
                                                    <select name="status" class="form-control">
                                                        <option value="active" selected>Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-5">
                                                    <button type="submit" class="btn btn-success">Submit</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </form>

                            </div>
                            <!-- /.box-body -->

                            <!-- Table of Approval Rules -->
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Document Type</th>
                                        <th>Level</th>
                                        <th>Department</th>
                                        <th>Approver Role</th>
                                        <th>Min Amount</th>
                                        <th>Max Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($approvals as $row) { ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= $row->document_type ?></td>
                                            <td><?= $row->level ?></td>
                                            <td><?= !empty($row->department_name) ? html_escape($row->department_name) : '<span class="label label-default">All</span>' ?></td>
                                            <td><strong><?= html_escape($row->approver_role) ?></strong> <span class="label label-info" style="font-size: 10px; margin-left: 3px;" title="Admin can also approve">+ Admin</span></td>
                                            <td><?= $row->min_amount ?></td>
                                            <td><?= $row->max_amount ?></td>
                                            <td><?= $row->status ?></td>
                                            <td>
                                                <a href="<?= base_url('ApprovalMatrixController/edit/' . $row->id) ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="<?= base_url('ApprovalMatrixController/delete/' . $row->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php $i++;
                                    } ?>
                                </tbody>
                            </table>

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