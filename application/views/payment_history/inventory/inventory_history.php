<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style>
.ui-autocomplete {
max-height: 300px;
overflow-y: auto; /* prevent horizontal scrollbar */
overflow-x: hidden; /* add padding to account for vertical scrollbar */
z-index:1000 !important;
}
</style>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Inventory History
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Inventory History</a></li>
                    <li class="active">Inventory History Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Inventory History Details</h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body">

                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-danger">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <div class="form-group row">
                                    <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/inventory_history" enctype="multipart/form-data">
<!--                                        <label for="inputEmail3" class="col-sm-1 control-label">Search</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control input-sm ui-autocomplete" name="barcode_no" id="barcode_no" placeholder="Enter Barcode to Search">
                                        </div>

                                    
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>-->
                                    </form>
                                </div>

<!--                                <table id="" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Description</th>
                                            <th>Customer Name</th>
                                            <th>Status</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $key) { ?>

                                        <td><?php echo $key->item; ?></td>
                                            <td> <?php echo $key->description; ?></td>
                                        <td> <?php echo $key->fullname; ?></td>
                                        <td> <?php if ($key->status == 1) { ?>
                                                <a href="<?php echo base_url() . 'InvoiceController/show_invoice/' . $key->id ?>"><span style="color: red;"><?php echo "Sold" ?></span> </a>   

                                            <?php } else { ?> 

                                                <span style="color: green;"> <?php echo "Available"; ?>  </span>
                                            <?php } ?>
                                        </td>

                                    <?php } ?>

                                    </tbody>

                                </table>-->
                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
<!--                                            <th class="hide">Product</th>-->
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>HSN/SAC</th>
                                            <th>GST</th>
                                            <th>Type</th>
                                            <th>Stock</th>
                                            <th>Cost</th>
                                            <th>Sell</th>
<!--                                            <th>Added</th>
                                            <th>Modified</th>-->
                                            <th>Action</th>
                                            <!--<th>Delete</th>-->
                                            <!--<th>QR</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $i = 1;
                                        foreach ($result as $key) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
                                                <!--<td class="hide"> <?php //echo $key->item_name;   ?> </td>-->
                                                <td> <?php echo $key->code; ?> </td>
                                                <td> <?php echo $key->prod_description; ?> </td>
                                                <td><?php echo $key->hsn; ?> </td>
                                                <td> <?php echo $key->gst_per; ?> </td>
                                                 <td> <?php
                                                   if ($key->item_type == 'B') { echo 'Boughtout';} else{
                                                       echo 'Manufacturing';
                                                   }
                                                 
                                                 ?>
                                               
                                                 
                                                 </td>
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
                                                <td>

                                                    <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                                <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="<?php echo base_url() . 'InventoryController/get_inventory_by_id/' . $key->inventory_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?php echo base_url() . 'InventoryController/delete_inventory_by_id/' . $key->inventory_id; ?>" 
                                                                       role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
                                                                </li>
                                                                <li>
                                                                   <a href="<?php echo base_url() . 'InventoryController/get_inventory_by_id_to_generate_bar_code/' . $key->inventory_id; ?>" class="btn btn-primary hide" role="button"><i class="fa fa-download" aria-hidden="true"></i></a>

                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>

                                            <?php
                                            $i++;
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

    <script>
        $(function () {
            var availableBarcodes=
                    [
                <?php foreach ($barcode as $key) { ?>
                    "<?php echo $key->barcode; ?>",
                       <?php  } ?>
                    ];

            $("#barcode_no").autocomplete({
                source: availableBarcodes
            });
        });
    </script>