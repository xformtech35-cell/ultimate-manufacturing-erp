<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
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
                                <button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Inventory</button>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

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
                                            <th>Description</th>
                                            <th>HSN/SAC</th>
                                            <th>GST</th>
                                            <th>Type</th>
                                            <th hidden>Stock</th>
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
                                                <td hidden="">

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
                                                <td> <a href="<?php echo base_url() . 'InventoryController/get_inventory_by_id/' . $key->inventory_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-pencil-square" aria-hidden="true"></i>
                                                    </a> 
                                                 <a href="<?php echo base_url() . 'InventoryController/delete_inventory_by_id/' . $key->inventory_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                 <a href="<?php echo base_url() . 'InventoryController/get_inventory_by_id_to_generate_bar_code/' . $key->inventory_id; ?> " class="btn btn-primary hide" role="button"><i class="fa fa-download" aria-hidden="true"></i>
                                                 </a> </td>
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

    <!-- ./Inventory modal -->

    <div id="myModal" class="modal fade" role="dialog" >
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center><h4 class="modal-title">Add Inventory<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>

                </div>

                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/add_inventory" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body ">
                            <!-- form start -->
                            <div class="form-group row hide">
                                <label for="inputEmail3" class="col-sm-2 control-label">Product Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-sm" name="item_name" id="item_name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 control-label">Item<span style="color: red;">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-sm prevent-special-char" name="code" id="code" required="">
                                    <!-- <input type="text" readonly="" class="form-control input-sm" name="code" id="code" value="GT<?php //printf("%04d", $inventory_id['COUNT(uid)'] + 1);   ?>">--> -->
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 control-label">Description<span style="color: red;">*</span></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control input-sm" name="prod_description" id="prod_description" rows="5" required="">  </textarea>
                                    <!--<input type="text" class="form-control input-sm" name="prod_description" id="prod_description">-->
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 control-label">HSN/SAC Code<span style="color: red;">*</span></label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" class="form-control input-sm number-only-validation" name="hsn" id="hsn" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 control-label">GST(%)<span style="color: red;">*</span></label>
                                <div class="col-sm-10">

                                    <select class="form-control input-sm "  name="gst_per" id="gst_per" required="">
                                        <option value="">Select GST</option>
                                        <?php foreach ($gst_class as $key) { ?>
                                            <option value="<?php echo $key['gst_class']; ?>"><?php echo $key['gst_class']; ?></option> 
                                        <?php } ?>  
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 control-label">Item Type<span style="color: red;">*</span></label>
                                <div class="col-sm-10">

                                    <select class="form-control input-sm "  name="item_type" id="item_type" required="">
                                        <option value="">Select item type</option>
                                        <option value="B">Boughtout</option>
                                        <option value="M">Manufacturing</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row hide">
                                <label for="inputEmail3" class="col-sm-2 control-label">Stock</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" class="form-control input-sm" name="stock" id="stock">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 control-label">Cost Price</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" class="form-control input-sm" name="cost_price" id="cost_price">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 control-label">Sell Price</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" class="form-control input-sm"  name="sell_price" id="sell_price">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave"  class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    
    
     <script>
 CKEDITOR.replace('prod_description');
 </script>