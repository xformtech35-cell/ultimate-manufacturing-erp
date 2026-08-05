<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');

$is_edit_mode = !empty($edit_record);
$form_action  = $is_edit_mode
    ? base_url() . 'InventoryController/edit_direct_individual'
    : base_url() . 'InventoryController/add_direct_individual';
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Direct Individual Master</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Direct</a></li>
                    <li class="active">Individual Master</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Individual Master Details</h3>
                            </div>
                            <div class="box-body">

                               
                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo $form_action; ?>">
                                    <?php if ($is_edit_mode) { ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_record['id']; ?>">
                                    <?php } ?>
                                    <div class="modal-body">
                                        <div class="card-body">

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Code<span style="color:red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control input-sm" name="code"
                                                        value="<?php echo $is_edit_mode ? htmlspecialchars($edit_record['code']) : ''; ?>"
                                                        required maxlength="50">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Employee Name<span style="color:red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control input-sm" name="employee_name"
                                                        value="<?php echo $is_edit_mode ? htmlspecialchars($edit_record['employee_name']) : ''; ?>"
                                                        required maxlength="150">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Type<span style="color:red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <select class="form-control input-sm" name="type" required>
                                                        <option value="">Select Type</option>
                                                        <option value="Permanent" <?php echo ($is_edit_mode && $edit_record['type'] == 'Permanent') ? 'selected' : ''; ?>>Permanent</option>
                                                        <option value="Contract" <?php echo ($is_edit_mode && $edit_record['type'] == 'Contract') ? 'selected' : ''; ?>>Contract</option>
                                                        <option value="Temporary" <?php echo ($is_edit_mode && $edit_record['type'] == 'Temporary') ? 'selected' : ''; ?>>Temporary</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        <?php if ($is_edit_mode) { ?>
                                            <a href="<?php echo base_url() . 'InventoryController/direct_individual_master'; ?>" class="btn btn-default">Cancel</a>
                                            <button type="submit" class="btn btn-success pull-right downloadButton">Update</button>
                                        <?php } else { ?>
                                            <button type="button" id="back" class="btn btn-default">Back</button>
                                            <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                        <?php } ?>
                                    </div>
                                </form>

                            </div>
                            <!-- /.box-body -->

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Code</th>
                                        <th>Employee Name</th>
                                        <th>Type</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($records as $row) { ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo htmlspecialchars($row->code); ?></td>
                                            <td><?php echo htmlspecialchars($row->employee_name); ?></td>
                                            <td><?php echo htmlspecialchars($row->type); ?></td>
                                            <td>
                                                <a href="<?php echo base_url() . 'InventoryController/direct_individual_master?edit_id=' . $row->id; ?>"
                                                   class="btn btn-primary" role="button">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo base_url() . 'InventoryController/delete_direct_individual/' . $row->id; ?>"
                                                   class="btn btn-danger" role="button"
                                                   onclick="return confirm('Are you sure you want to delete?')">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php $i++; } ?>
                                    <?php if (empty($records)) { ?>
                                        <tr><td colspan="6" class="text-center">No records found.</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
</body>
