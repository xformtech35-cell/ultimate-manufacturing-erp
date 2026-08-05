<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');

$expense_mode = isset($expense_mode) ? strtolower($expense_mode) : '';
$expense_mode_label = '';
if ($expense_mode == 'direct') {
    $expense_mode_label = 'Direct ';
} elseif ($expense_mode == 'indirect') {
    $expense_mode_label = 'Indirect ';
}

$is_edit_mode = isset($edit_expense_cat) && !empty($edit_expense_cat);
$form_action = $is_edit_mode ? base_url() . 'InventoryController/edit_expense_cateogry' : base_url() . 'InventoryController/add_expense_cateogry';
$exp_cat_value = $is_edit_mode && isset($edit_expense_name) ? $edit_expense_name : '';
$expense_type_value = $is_edit_mode && isset($edit_expense_type) ? $edit_expense_type : '';
$cancel_url = ($expense_mode == 'direct') ? base_url() . 'InventoryController/direct_expense_master' : base_url() . 'InventoryController/indirect_expense_master';
?>

<body class="hold-transition skin-blue sidebar-mini">
     <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php echo $expense_mode_label; ?>Master
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#"><?php echo $expense_mode_label; ?>Master</a></li>
                    <li class="active"><?php echo $expense_mode_label; ?>Master Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title"><?php echo $expense_mode_label; ?>Master Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                             

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo $form_action; ?>" enctype="multipart/form-data">
                                    <input type="hidden" name="expense_mode" value="<?php echo $expense_mode; ?>">
                                    <?php if ($is_edit_mode) { ?>
                                        <input type="hidden" name="exp_cat_id" value="<?php echo $edit_expense_cat['exp_cat_id']; ?>">
                                    <?php } ?>
                                    <div class="modal-body">

                                        <div class="card-body ">

                                            <?php if ($expense_mode == 'indirect') { ?>
                                            <div class="form-group row">
                                                <label for="expense_type" class="col-sm-4 control-label">Expenditure Type<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <select class="form-control input-sm" name="expense_type" id="expense_type" required="">
                                                        <option value="">Select Type</option>
                                                        <option value="individual" <?php echo ($expense_type_value == 'individual') ? 'selected' : ''; ?>>Individual</option>
                                                        <option value="corporate" <?php echo ($expense_type_value == 'corporate') ? 'selected' : ''; ?>>Corporate</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php } ?>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expenditure Category<span style="color: red;">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control input-sm" name="exp_cat" id="exp_cat" value="<?php echo $exp_cat_value; ?>" required="">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        <?php if ($is_edit_mode) { ?>
                                            <a href="<?php echo $cancel_url; ?>" class="btn btn-default">Cancel</a>
                                            <button type="submit" class="btn btn-success pull-right downloadButton">Update</button>
                                        <?php } else { ?>
                                            <button type="button" id="back" class="btn btn-default">Back</button>
                                            <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                        <?php } ?>
                                    </div>
                                </form>
                                
                            </div>
                            <!-- /.box-body -->
                            <table id="" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <?php if ($expense_mode == 'indirect') { ?>
                                                <th>Expenditure Type</th>
                                            <?php } ?>
                                            <th>Expenditure Category</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($expense_catgory as $key) { ?>
                                            <?php
                                            $row_exp_type = '';
                                            $row_exp_name = $key->exp_cat;
                                            if ($expense_mode == 'direct') {
                                                if (stripos($row_exp_name, 'Direct - ') === 0) {
                                                    $row_exp_name = trim(substr($row_exp_name, strlen('Direct - ')));
                                                }
                                            }
                                            if ($expense_mode == 'indirect') {
                                                if (stripos($row_exp_name, 'Indirect - ') === 0) {
                                                    $row_exp_name = trim(substr($row_exp_name, strlen('Indirect - ')));
                                                }
                                                if (preg_match('/^(Individual|Corporate)\s*-\s*(.*)$/i', $row_exp_name, $m)) {
                                                    $row_exp_type = ucfirst(strtolower($m[1]));
                                                    $row_exp_name = trim($m[2]);
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <?php if ($expense_mode == 'indirect') { ?>
                                                    <td><?php echo $row_exp_type; ?></td>
                                                <?php } ?>
                                                <td> <?php echo $row_exp_name; ?> </td>
                                            <td><a href="<?php echo $cancel_url . '?edit_id=' . $key->exp_cat_id; ?>" class="btn btn-primary" role="button"><i class="fa fa-pencil" aria-hidden="true"></i></a></td>
                                            <td><a href="<?php echo base_url() . 'InventoryController/delete_exp_cat_by_id/' . $key->exp_cat_id . '?expense_mode=' . $expense_mode; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                            </tr>
                                        <?php $i++; } ?>
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

  