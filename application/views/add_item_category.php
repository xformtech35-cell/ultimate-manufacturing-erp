<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <div class="content-wrapper">
            <section class="content-header">
                <h1>Item Category</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Item Category</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">

                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Category</h3>
                            </div>

                            <div class="box-body">


                                <form class="form-horizontal" method="post"
                                    action="<?= base_url('ItemCategoryController/add_item_category') ?>">

                                    <div class="form-group row">
                                        <label class="col-sm-4 control-label">Category Name <span style="color:red">*</span></label>
                                        <div class="col-sm-3">
                                            <input type="text" name="category_name" class="form-control" required="">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success pull-right">Submit</button>

                                </form>

                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">Sr.No.</th>
                                        <th>Category Name</th>
                                        <th style="width: 150px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($category_result as $row) { ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= htmlspecialchars($row->category_name) ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-primary btn-sm edit-cat-btn"
                                                    data-id="<?= $row->category_id ?>"
                                                    data-name="<?= htmlspecialchars($row->category_name) ?>">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </button>
                                                <a href="<?= base_url('ItemCategoryController/delete_category/' . $row->category_id) ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Delete this category?')">
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

        <!-- Edit Category Modal -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="<?= base_url('ItemCategoryController/update_category') ?>">
                        <div class="modal-header bg-primary text-white" style="background-color: #3c8dbc; color: #fff;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="editCategoryModalLabel"><i class="fa fa-edit"></i> Edit Category</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="category_id" id="edit_category_id">
                            <div class="form-group">
                                <label for="edit_category_name">Category Name <span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="category_name" id="edit_category_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php $this->load->view('admin/footer'); ?>

        <script>
            $(document).ready(function() {
                $(document).on('click', '.edit-cat-btn', function() {
                    var id = $(this).data('id');
                    var name = $(this).data('name');
                    $('#edit_category_id').val(id);
                    $('#edit_category_name').val(name);
                    $('#editCategoryModal').modal('show');
                });
            });
        </script>

    </div>