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
                    Purchase Stock
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Purchase Stock</a></li>
                    <li class="active">Purchase Stock Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Purchase Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/add_stock" enctype="multipart/form-data">

                                    <div class="modal-body">

                                        <div class="card-body ">
                                            <!-- form start -->

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Item<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <select class="form-control input-sm product_name_auto item_search_name name_list"  name="term" id="item_name" onchange="myFunction(this.id)" required="" data-live-search="true">
                                                        <option value=""></option>
                                                        <?php foreach ($product_code as $key) { ?>
                                                            <option value="<?php echo $key->raw_item_master_id; ?>"><?php echo $key->raw_item_master_name; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">In Stock<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <input type="number" min="0" class="form-control input-sm number-only-validation instock" name="instock" id="instock" required="">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Unit<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">

                                                    <select class="form-control input-sm name_list"  name="purchase_unit" id="purchase_unit"  required="" data-live-search="true">
                                                            <option value="">Select Unit</option>
                                                            <option value="KG">KG</option> 
                                                            <option value="PKG">PKG</option> 
                                                            <option value="LIT">LIT</option> 
                                                            <option value="TEEN">TEEN</option> 
                                                            <option value="OTHER">OTHER</option> 
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Vendor Name<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">

                                                    <select class="form-control input-sm product_name_auto item_search_vendor name_list"  name="supplier_name" id="supplier_name" onchange="myFunction(this.id)" required="" data-live-search="true">
                                                        <option value=""></option>
                                                        <?php foreach ($result as $key) { ?>
                                                            <option value="<?php echo $key->fullname; ?>"><?php echo $key->fullname; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Purchase Date</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm alldate" name="purchase_date" id="purchase_date">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Rate on item</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm rate_on_item" name="rate_on_item" id="rate_on_item">
                                                </div>
                                            </div>

                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Amount</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm" name="paid_amount" id="paid_amount">
                                                </div>
                                            </div>
                                            
                                            
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" id="btnSave"  class="btn btn-success">Save</button>
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
        <?php $this->load->view('admin/footer'); ?>

        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
    