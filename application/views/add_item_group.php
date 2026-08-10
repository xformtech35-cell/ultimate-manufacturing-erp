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

            <!-- Page Header -->
            <section class="content-header">
                <h1>Add Item Group</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('Home/index/'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Item Group</a></li>
                    <li class="active">Add Group</li>
                </ol>
            </section>

            <!-- Main Content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">

                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Item Group</h3>
                            </div>

                            <div class="box-body">

                          
                            

                                <form class="form-horizontal" method="post" action="<?php echo base_url('ItemGroupController/add_item_group'); ?>">

                                    <div class="modal-body">
                                        <div class="card-body">

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Group Name
                                                    <span style="color:red">*</span>
                                                </label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control input-sm" name="group_name" id="group_name" required>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
                                </form>

                            </div>

                            <!-- List Table -->
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">Sr.No.</th>
                                        <th>Group Name</th>
                                        <th style="width: 150px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($group_result as $grp) { ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= htmlspecialchars($grp->group_name) ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-primary btn-sm edit-grp-btn"
                                                    data-id="<?= $grp->group_id ?>"
                                                    data-name="<?= htmlspecialchars($grp->group_name) ?>">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </button>
                                                <a href="<?php echo base_url('ItemGroupController/delete_group/' . $grp->group_id); ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this group?')">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php $i++;
                                    } ?>
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </section>

        </div>

        <!-- Edit Group Modal -->
        <div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-labelledby="editGroupModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="<?= base_url('ItemGroupController/update_group') ?>">
                        <div class="modal-header bg-primary text-white" style="background-color: #3c8dbc; color: #fff;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="editGroupModalLabel"><i class="fa fa-edit"></i> Edit Item Group</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="group_id" id="edit_group_id">
                            <div class="form-group">
                                <label for="edit_group_name">Group Name <span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="group_name" id="edit_group_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php $this->load->view('admin/footer'); ?>

        <script>
            $(document).ready(function() {
                $(document).on('click', '.edit-grp-btn', function() {
                    var id = $(this).data('id');
                    var name = $(this).data('name');
                    $('#edit_group_id').val(id);
                    $('#edit_group_name').val(name);
                    $('#editGroupModal').modal('show');
                });
            });
        </script>

        <div class="control-sidebar-bg"></div>

    </div>