<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<style>
    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s;
        padding: 8px 12px;
        height: auto;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .form-control[readonly] {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
    }

    .control-label {
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
    }

    .required-star {
        color: red;
        font-weight: bold;
    }

    .badge-type {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-boughtout {
        background-color: #e3f2fd;
        color: #1565c0;
        border: 1px solid #bbdefb;
    }

    .badge-manufacturing {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }
</style>

<!-- Include CKEditor from CDN -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <i class="fa fa-edit"></i> Edit Inventory Item
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/index/' ?>"><i class="fa fa-cubes"></i> Inventory</a></li>
                    <li class="active"><i class="fa fa-edit"></i> Edit Inventory Item</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info enhanced-card">
                            <div class="box-header with-border" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                                <h4 class="box-title" style="margin: 0; color: #007bff; font-weight: 600;">
                                    <i class="fa fa-pencil-square-o"></i> Edit Inventory Item</h4>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-default pull-right" style="padding: 6px 16px;">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <?php if (isset($inventory) && !empty($inventory)): ?>
                                    <?php
                                    $type_class = $inventory['item_type'] == 'B' ? 'badge-boughtout' : 'badge-manufacturing';
                                    $type_text = $inventory['item_type'] == 'B' ? 'Boughtout' : 'Manufacturing';
                                    ?>

                                    <div class="alert alert-info" style="border-left: 4px solid var(--info-teal); margin-bottom: 20px;">
                                        <h4><i class="fa fa-info-circle"></i> Editing Item</h4>
                                        <p>
                                            <strong>Item Code:</strong> <?php echo $inventory['code']; ?>
                                            <span class="badge-type <?php echo $type_class; ?> pull-right">
                                                <?php echo $type_text; ?>
                                            </span>
                                        </p>
                                    </div>

                                    <form method="post" action="<?php echo base_url(); ?>InventoryController/edit_inventory" enctype="multipart/form-data">
                                        <div style="padding: 20px;">
                                            <!-- Hidden field -->
                                            <input type="hidden" name="inventory_id" value="<?php echo $inventory['inventory_id']; ?>">

                                            <!-- Row 1: Code, Name, HSN -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Item Code <span class="required-star">*</span></label>
<input type="text" class="form-control" name="code" id="code" value="<?php echo $inventory['code']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Item Name <span class="required-star">*</span></label>
                                                        <input type="text" class="form-control" name="item_name" id="item_name" value="<?php echo htmlspecialchars($inventory['item_name']); ?>" required placeholder="Enter item name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">HSN/SAC Code <span class="required-star">*</span></label>
                                                        <input type="number" min="0" class="form-control" name="hsn" id="hsn" value="<?php echo $inventory['hsn']; ?>" required placeholder="Enter HSN/SAC code">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <div class="form-group" style="margin-bottom: 25px;">
                                                <label class="control-label" style="margin-bottom: 10px;">Description</label>
                                                <textarea class="form-control" name="prod_description" id="prod_description" rows="4" placeholder="Enter item description" style="resize: vertical;"><?php echo $inventory['prod_description']; ?></textarea>
                                            </div>



                                            <!-- Row 2: Category, Group, Unit -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Category</label>
                                                        <select class="form-control" name="category_id" id="category_id">
                                                            <option value="">Select Category</option>
                                                            <?php foreach ($category_result as $c): ?>
                                                                <option value="<?= $c->category_id; ?>" <?= ($inventory['category_id'] == $c->category_id) ? 'selected' : ''; ?>>
                                                                    <?= $c->category_name; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Group</label>
                                                        <select class="form-control" name="group_id" id="group_id">
                                                            <option value="">Select Group</option>
                                                            <?php foreach ($group_result as $g): ?>
                                                                <option value="<?= $g->group_id; ?>" <?= ($inventory['group_id'] == $g->group_id) ? 'selected' : ''; ?>>
                                                                    <?= $g->group_name; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Unit <span class="required-star">*</span></label>
                                                        <select class="form-control" name="unit" id="unit" required>
                                                            <option value="">Select Unit</option>
                                                            <?php foreach ($unit_result as $row): ?>
                                                                <option value="<?php echo $row->unit; ?>" <?php if ($inventory['unit'] == $row->unit) echo 'selected="selected"'; ?>>
                                                                    <?php echo $row->unit; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 3: GST, Item Type, Cost Price, Sell Price -->
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">GST(%) <span class="required-star">*</span></label>
                                                        <select class="form-control" name="gst_per" id="gst_per" required>
                                                            <option value="">Select GST</option>
                                                            <?php foreach ($gst_class as $row): ?>
                                                                <option value="<?php echo $row['gst_class']; ?>" <?php if ($inventory['gst_per'] == $row['gst_class']) echo 'selected="selected"'; ?>>
                                                                    <?php echo $row['gst_class']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Item Type</label>
                                                        <select class="form-control" name="item_type" id="item_type">
                                                            <option value="">Select type</option>
                                                            <option value="B" <?php if ($inventory['item_type'] == 'B') echo 'selected="selected"'; ?>>Boughtout</option>
                                                            <option value="M" <?php if ($inventory['item_type'] == 'M') echo 'selected="selected"'; ?>>Manufacturing</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Cost Price (&#8377;) <span class="required-star">*</span></label>
                                                        <input type="number" min="0" step="0.01" class="form-control" name="cost_price" id="cost_price" value="<?php echo $inventory['cost_price']; ?>" required placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Sell Price (&#8377;)</label>
                                                        <input type="number" min="0" step="0.01" class="form-control" name="sell_price" id="sell_price" value="<?php echo $inventory['sell_price']; ?>" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 4: Stock & Packing -->
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Stock</label>
                                                        <input type="number" min="0" step="0.01" class="form-control" name="stock" id="stock" value="<?php echo $inventory['stock']; ?>" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" style="margin-bottom: 20px;">
                                                        <label class="control-label" style="margin-bottom: 8px;">Packing</label>
                                                        <input type="text" class="form-control" name="packing" id="packing" value="<?php echo isset($inventory['packing']) ? $inventory['packing'] : ''; ?>" placeholder="e.g. 10 kg / 1 pack">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div style="padding: 15px 20px; border-top: 1px solid #dee2e6; background-color: #f8f9fa;">
                                            <button type="button" id="back" class="btn btn-default" style="padding: 8px 20px; margin-right: 10px;">
                                                <i class="fa fa-times"></i> Back
                                            </button>
                                            <button type="submit" class="btn btn-success pull-right" style="padding: 8px 20px;">
                                                <i class="fa fa-check"></i> Update Item
                                            </button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <h4><i class="fa fa-warning"></i> Error!</h4>
                                        Inventory item not found. Please go back and try again.
                                    </div>
                                    <div class="text-center">
                                        <a href="<?php echo base_url() . 'InventoryController/index/'; ?>" class="btn btn-primary">
                                            <i class="fa fa-arrow-left"></i> Back to Inventory List
                                        </a>
                                    </div>
                                <?php endif; ?>
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
        $(document).ready(function() {
            // Initialize CKEditor on the description textarea
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('prod_description', {
                    height: 75,
                    toolbar: [
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
                        { name: 'links', items: ['Link', 'Unlink'] },
                        { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                        { name: 'colors', items: ['TextColor', 'BGColor'] },
                        { name: 'tools', items: ['Maximize'] }
                    ]
                });
            }

            // Back button functionality
            $('#back').click(function() {
                window.history.back();
            });
        });
    </script>
</body>