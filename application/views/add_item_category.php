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
                                        <th>Sr.No.</th>
                                        <th>Category</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($category_result as $row) { ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= $row->category_name ?></td>
                                            <td>
                                                <a href="<?= base_url('ItemCategoryController/delete_category/' . $row->category_id) ?>"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Delete this category?')">
                                                    <i class="fa fa-trash"></i>
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

        <?php $this->load->view('admin/footer'); ?>

    </div>