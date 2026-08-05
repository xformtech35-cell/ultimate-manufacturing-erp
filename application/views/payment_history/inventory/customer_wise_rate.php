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
                    Customer Wise Rate
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Customer Wise Rate</a></li>
                    <li class="active">Customer Wise Rate Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Customer Wise Rate Details</h3>
                                <button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Customer Rate</button>
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
                                            <th>Customer Name</th>
                                            <th>Item</th>
                                            <th>Price</th>
                                            <th>Added</th>
                                            <th>Modified</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $i = 1;
                                        foreach ($customer_wise_rate as $key) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->company_name; ?> </td>
                                                <td> <?php echo $key->code; ?> </td>
                                                <td> <?php echo number_format($key->customer_rate, 2); ?> </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->rate_added_date)); ?> </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->rate_modified_date)); ?> </td>
                                                <td> <a href="<?php echo base_url() . 'InventoryController/get_customer_rate_by_id/' . $key->customer_wise_rate_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-pencil-square" aria-hidden="true"></i>
                                                    </a> </td>
                                                <td> <a href="<?php echo base_url() . 'InventoryController/delete_customer_wise_rate_by_id/' . $key->customer_wise_rate_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center><h4 class="modal-title">Add Customer Wise Rate<button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>

                </div>

                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/add_customer_wise_rate" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body ">
                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Customer<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control"  name="customer_id_fk" id="customer_id_fk" required="">
                                        <option value="">Select Company</option>
                                        <?php foreach ($company_name as $key) { ?>
                                            <option value="<?php echo $key->customer_id; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                        <?php } ?>  
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Item<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control"  name="inventory_id_fk" id="inventory_id_fk"  required="" data-live-search="true">
                                        <option value="">Select Item</option>
                                        <?php foreach ($product_name as $key) { ?>
                                            <option value="<?php echo $key->inventory_id; ?>"><?php echo $key->code; ?></option> 
                                        <?php } ?>  
                                    </select>    
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Rate<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="customer_rate" id="customer_rate" required="">
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