<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<script src="https://cdn.ckeditor.com/ckeditor5/47.5.0/ckeditor5.umd.js"></script>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Inventory
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Edit Inventory</a></li>
                    <li class="active">Edit Inventory Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Inventory Details</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/edit_inventory" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <!-- form start -->
                                        <div class="form-group row hide">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Product Name</label>
                                            <div class="col-sm-9">
                                                <input type="hidden" class="form-control input-sm" name="inventory_id" id="inventory_id" value="<?php
                                                                                                                                                if (isset($inventory) && !empty($inventory)) {
                                                                                                                                                    echo $inventory['inventory_id'];
                                                                                                                                                }
                                                                                                                                                ?>" required="">

                                                <input type="text" class="form-control input-sm" name="item_name" id="item_name" value="<?php
                                                                                                                                        if (isset($inventory) && !empty($inventory)) {
                                                                                                                                            echo $inventory['item_name'];
                                                                                                                                        }
                                                                                                                                        ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Item<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" readonly="" class="form-control input-sm" name="code" id="code" value="<?php
                                                                                                                                            if (isset($inventory) && !empty($inventory)) {
                                                                                                                                                echo $inventory['code'];
                                                                                                                                            }
                                                                                                                                            ?>" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Description<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control input-sm" name="prod_description" id="prod_description" rows="5"><?php echo $inventory['prod_description']; ?></textarea>
                                                <!--                                                <input type="text"  class="form-control input-sm"  name="prod_description" id="prod_description"  value="<?php
                                                                                                                                                                                                                if (isset($inventory) && !empty($inventory)) {
                                                                                                                                                                                                                    echo $inventory['prod_description'];
                                                                                                                                                                                                                }
                                                                                                                                                                                                                ?>" required="">-->



                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">HSN/SAC Code<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="number" min="0" class="form-control input-sm number-only-validation" name="hsn" id="hsn" value="<?php
                                                                                                                                                                if (isset($inventory) && !empty($inventory)) {
                                                                                                                                                                    echo $inventory['hsn'];
                                                                                                                                                                }
                                                                                                                                                                ?>" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">GST(%)<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm " name="gst_per" id="gst_per" required="">
                                                    <option value="">Select GST</option>
                                                    <?php
                                                    $gst = $inventory['gst_per'];

                                                    foreach ($gst_class as $row) {
                                                    ?>
                                                        <option value="<?php echo $row['gst_class'] ?>"
                                                            <?php
                                                            if ($gst == $row['gst_class']) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>><?php echo $row['gst_class']; ?></option>
                                                    <?php }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Item Type<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">

                                                <select class="form-control input-sm " name="item_type" id="item_type" required="">
                                                    <option value="">Select item type</option>
                                                    <option value="B" <?php if ($inventory['item_type'] == 'B') {
                                                                            echo 'selected="selected"';
                                                                        } ?>>Boughtout</option>
                                                    <option value="M" <?php if ($inventory['item_type'] == 'M') {
                                                                            echo 'selected="selected"';
                                                                        } ?>>Manufacturing</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row hide">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Stock</label>
                                            <div class="col-sm-9">
                                                <input type="number" min="0" class="form-control input-sm" name="stock" id="stock" value="<?php
                                                                                                                                            if (isset($inventory) && !empty($inventory)) {
                                                                                                                                                echo $inventory['stock'];
                                                                                                                                            }
                                                                                                                                            ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Cost Price</label>
                                            <div class="col-sm-9">
                                                <input type="text" min="0" class="form-control input-sm" name="cost_price" id="cost_price" value="<?php
                                                                                                                                                    if (isset($inventory) && !empty($inventory)) {
                                                                                                                                                        echo $inventory['cost_price'];
                                                                                                                                                    }
                                                                                                                                                    ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Sell Price</label>
                                            <div class="col-sm-9">
                                                <input type="text" min="0" class="form-control input-sm" name="sell_price" id="sell_price" value="<?php
                                                                                                                                                    if (isset($inventory) && !empty($inventory)) {
                                                                                                                                                        echo $inventory['sell_price'];
                                                                                                                                                    }
                                                                                                                                                    ?>">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer small text-muted">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
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

    <script>
        CKEDITOR.replace('prod_description');
    </script>