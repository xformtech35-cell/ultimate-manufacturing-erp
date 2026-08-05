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
                                        <th>Sr.No.</th>
                                        <th>Group Name</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($group_result as $grp) { ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= $grp->group_name ?></td>
                                            <td>
                                                <a href="<?php echo base_url('ItemGroupController/delete_group/' . $grp->group_id); ?>"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this group?')">
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
        <div class="control-sidebar-bg"></div>

    </div>