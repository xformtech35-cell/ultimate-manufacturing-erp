<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Add Finish Goods
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#"> Finish Goods</a></li>
                    <li class="active"> Finish Goods Details</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Finish Goods Details</h3>
                                <button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Finish Goods</button>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <table id="example2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Item Code</th>
                                            <th>Roll Weight In KG</th>
                                            <th>Roll Color</th>
                                            <th>GSM</th>
                                            <th>Roll Size</th>
                                            <th>Bag Created Qty</th>
                                            <th>Bag Type</th>
                                            <th>Bag Size</th>
                                            <th>Created</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $i = 1;
                                        foreach ($finish_stock as $key) {
                                            ?>
                                            <tr>
                                                <td>
                                            <?php echo $i; ?>
                                                </td>
                                                <td><?php echo $key->code; ?> </td>
                                                <td> <?php echo $key->roll_weight; ?> </td>
                                                <td><?php echo $key->roll_color; ?> </td>
                                                <td><?php echo $key->gsm; ?> </td>
                                                <td> <?php echo $key->roll_size; ?> </td>
                                                <td> <?php echo $key->bags_created; ?> </td>
                                                <td> <?php echo $key->bag_type; ?> </td>
                                                <td> <?php echo $key->bag_size; ?> </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->created_date)); ?> </td>
                                                    <td> <a href="<?php echo base_url() . 'RollController/get_finish_goods_by_id/' . $key->id; ?> " class="btn btn-primary" role="button"><i class="fa fa-pencil-square" aria-hidden="true"></i>
                                                    </a> </td>
                                                <td> <a href="<?php echo base_url() . 'RollController/delete_finish_stock_by_id/' . $key->id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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
    <!-- Finish Goods modal -->

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center><h4 class="modal-title">Add Finish Goods<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>

                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>RollController/add_finish_goods" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body ">
                            <!-- form start -->

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Item<span style="color: red;">*</span></label>
                                <div class="col-sm-7" id="append_to_dropdown">
                                    <select id="code"  name="code" class="form-control"  required="">
                                        <option value="">Select Item</option>
                                           <option value="NEW">+ Add New Product</option>

                                        <?php foreach ($code_name as $key) { ?>
                                                            <option value="<?php echo $key->code; ?>"><?php echo $key->code . " - " . $key->item_name; ?></option>
                                        <?php } ?>  
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Roll Weight In KG</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="roll_weight" id="roll_weight">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Roll Color</label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="roll_color" id="roll_color">
                                        <option value="Red">Red</option>
                                        <option value="RGreen">R Green</option>
                                        <option value="LYellow">L Yellow</option>
                                        <option value="GYellow">G Yellow</option> 
                                        <option value="PGreen">P Green</option>
                                        <option value="White">White</option>
                                        <option value="Ivory">Ivory</option>
                                        <option value="Beige">Beige</option>
                                        <option value="Orange">Orange</option> 
                                        <option value="MedicalBlue">Medical Blue</option>
                                        <option value="Violet">Violet</option>
                                        <option value="Blue">Blue</option>
                                        <option value="Black">Black</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">GSM</label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="gsm" id="gsm">
                                        <option value="60">60</option>
                                        <option value="80">80</option>
                                        <option value="100">100</option>
                                        <option value="120">120</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Roll Size (Inch)</label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="roll_size" id="roll_size">
                                        <option value='25"'>25"</option>
                                        <option value='32"'>32"</option>
                                        <option value='37"'>37"</option>
                                        <option value='43"'>43"</option> 
                                        <option value='47"'>47"</option>
                                        <option value='63"'>63"</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Bags Created Qty (Numbers)</label>
                                <div class="col-sm-7">
                                    <input type="number" min="0" class="form-control input-sm" name="bags_created" id="bags_created" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Bag Type</label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="bag_type" id="bag_type">
                                        <option value="Standard-D-Cut">Standard D-Cut</option>
                                        <option value="Deluxe-D-Cut">Deluxe D-Cut</option>
                                        <option value="Rice-Bags">Rice Bags</option>
                                        <option value="Printing-Bags">Printing Bags</option>
                                        <option value="Shopping Bags">Shopping Bags</option>
                                        <option value="W-Cut Bags">W-Cut Bags</option>
                                        <option value="U-Cut Bags">U-Cut Bags</option>
                                        <option value="Wedding Bags">Wedding Bags</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Bag Size</label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="bag_size" id="bag_size">
                                        <option value="8x10">8x10</option>
                                        <option value="10x14">10x14</option>
                                        <option value="12x16">12x16</option>
                                        <option value="14x19">14x19</option> 
                                        <option value="16x21">16x21</option>
                                    </select>
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
<!-- ./ Finish Goods modal -->