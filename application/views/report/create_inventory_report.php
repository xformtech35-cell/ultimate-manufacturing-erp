<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');

$selected_filters = isset($selected_filters) && is_array($selected_filters) ? $selected_filters : array();
$filter_options = isset($filter_options) && is_array($filter_options) ? $filter_options : array();
$selected_item_name = isset($selected_filters['item_name']) ? $selected_filters['item_name'] : '';
$selected_unit = isset($selected_filters['unit']) ? $selected_filters['unit'] : '';
$selected_item_type = isset($selected_filters['item_type']) ? $selected_filters['item_type'] : '';
$export_query = http_build_query(array_filter(array(
    'item_name' => $selected_item_name,
    'unit' => $selected_unit,
    'item_type' => $selected_item_type
), function ($value) {
    return $value !== '';
}));
$export_url = base_url() . 'ReportController/get_inventory_report' . ($export_query ? '?' . $export_query : '');
?>

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
                    <li><a href="#">Inventory</a></li>
                    <li class="active">Inventory Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Inventory Details</h3>
                                <a href="<?php echo $export_url; ?>"><button class="btn btn-success pull-right">Export to Excel</button></a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <form action="<?php echo base_url(); ?>ReportController/create_inventory_report" method="get" class="form-horizontal" style="margin-bottom: 15px;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Item Name</label>
                                                <div class="col-sm-8">
                                                    <select name="item_name" id="item_name" class="form-control input-sm">
                                                        <option value="">All Items</option>
                                                        <?php foreach ((array) ($filter_options['item_names'] ?? array()) as $item_name) { ?>
                                                            <option value="<?php echo htmlspecialchars($item_name, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($selected_item_name === $item_name) ? 'selected="selected"' : ''; ?>>
                                                                <?php echo htmlspecialchars($item_name, ENT_QUOTES, 'UTF-8'); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Unit</label>
                                                <div class="col-sm-8">
                                                    <select name="unit" id="unit" class="form-control input-sm">
                                                        <option value="">All Units</option>
                                                        <?php foreach ((array) ($filter_options['units'] ?? array()) as $unit) { ?>
                                                            <option value="<?php echo htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($selected_unit === $unit) ? 'selected="selected"' : ''; ?>>
                                                                <?php echo htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Type</label>
                                                <div class="col-sm-8">
                                                    <select name="item_type" id="item_type" class="form-control input-sm">
                                                        <option value="">All Types</option>
                                                        <option value="B" <?php echo ($selected_item_type === 'B') ? 'selected="selected"' : ''; ?>>Boughtout</option>
                                                        <option value="M" <?php echo ($selected_item_type === 'M') ? 'selected="selected"' : ''; ?>>Manufacturing</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                            <a href="<?php echo base_url(); ?>ReportController/create_inventory_report" class="btn btn-default btn-sm">Reset</a>
                                        </div>
                                    </div>
                                </form>

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
<!--                                            <th class="hide">Product</th>-->
                                            <th>Item</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>HSN/SAC</th>
                                            <th>GST</th>
                                            <th>Type</th>
                                            <th>Unit</th>
                                            <th >Stock</th>
                                            <th>Cost</th>
                                            <th>Sell</th>
<!--                                            <th>Added</th>
                                            <th>Modified</th>-->
<!--                                            <th>Action</th>-->
                                            <!--<th>Delete</th>-->
                                            <!--<th>QR</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $i = 1;
                                        foreach ((array) $result as $key) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
                                                <!--<td class="hide"> <?php //echo $key->item_name;   ?> </td>-->
                                                <td> <?php echo $key->code; ?> </td>
                                                <td> <?php echo $key->item_name; ?> </td>
                                                <td> <?php echo $key->prod_description; ?> </td>
                                                <td><?php echo $key->hsn; ?> </td>
                                                <td> <?php echo $key->gst_per; ?> </td>
                                                 <td> <?php
                                                   if ($key->item_type == 'B') {
                                                       echo 'Boughtout';
                                                       
                                                   } else{
                                                       echo 'Manufacturing';
                                                   }
                                                 
                                                 ?>
                                               
                                                 
                                                 </td>
                                                <td> <?php echo $key->unit; ?> </td>
                                                <td>

                                                    <?php if ($key->stock > '5') { ?>
                                                        <?php echo $key->stock; ?>
                                                    <?php } else { ?>
                                                        <span style="color: red;"><b><?php
                                                                echo $key->stock;
                                                            }
                                                            ?></b></span> 
                                                </td>

                                                <td> <?php echo number_format($key->cost_price, 2); ?> </td>
                                                <td> <?php echo number_format($key->sell_price, 2); ?> </td>
<!--                                                <td> <?php echo date('d-m-Y', strtotime($key->date_added)); ?> </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->date_modified)); ?> </td>-->
<!--                                                <td> <a href="<?php echo base_url() . 'InventoryController/get_inventory_by_id/' . $key->inventory_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-pencil-square" aria-hidden="true"></i>
                                                    </a> 
                                                 <a href="<?php echo base_url() . 'InventoryController/delete_inventory_by_id/' . $key->inventory_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                 <a href="<?php echo base_url() . 'InventoryController/get_inventory_by_id_to_generate_bar_code/' . $key->inventory_id; ?> " class="btn btn-primary hide" role="button"><i class="fa fa-download" aria-hidden="true"></i>
                                                 </a> </td>-->
                                            </tr>

                                            <?php
                                            $i++;
                                        }
                                        if (empty($result)) {
                                            ?>
                                            <tr>
                                                <td colspan="11" class="text-center">No inventory found for selected filters.</td>
                                            </tr>
                                            <?php
                                        }
                                        ?>

                                    </tbody>

                                </table>
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

    <!-- ./Inventory modal -->

   
    
    
     <script>
 CKEDITOR.replace('prod_description');
 </script>
