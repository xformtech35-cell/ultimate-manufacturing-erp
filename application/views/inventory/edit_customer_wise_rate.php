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
                    <li><a href="#">Edit Customer Wise Rate</a></li>
                    <li class="active">Edit Customer Wise Rate Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Customer Wise Rate Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/edit_customer_wise_rate" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <!-- form start -->
                                        <div class="form-group row">
                                            
                                            <div class="col-sm-9">
                                                <input type="hidden" class="form-control input-sm" name="customer_wise_rate_id" id="customer_wise_rate_id" value="<?php
                                                if (isset($customer_wise_rate) && !empty($customer_wise_rate)) {
                                                    echo $customer_wise_rate['customer_wise_rate_id'];
                                                }
                                                ?>" required="">

                                            </div>
                                        </div>

                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Customer</label>
                                                    <div class="col-sm-9">
                                                        <select class="col-md-12 company_search_name" name="customer_id_fk" id="customer_id_fk" required id="">
                                                            <option>Select Company</option>
                                                            <?php
                                                            $customer_id_fk = $customer_wise_rate['customer_id_fk'];
                                                            foreach ($company_name as $row) {
                                                                ?>
                                                                <option value="<?php echo $row->customer_id ?>"  
                                                                <?php
                                                                if ($customer_id_fk == $row->customer_id) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?> ><?php echo $row->company_name . " - " . $row->c_code; ?></option>
                                                                    <?php }?>
                                                        </select>  
                                                    </div>
                                                </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Item</label>
                                                    <div class="col-sm-9">
                                                        <select class="col-md-12 company_search_name" name="inventory_id_fk" id="inventory_id_fk" required id="">
                                                            <option>Select Company</option>
                                                            <?php
                                                            $inventory_id_fk = $customer_wise_rate['inventory_id_fk'];

                                                            foreach ($product_name as $row) {
                                                                ?>
                                                                <option value="<?php echo $row->inventory_id ?>"  
                                                                <?php
                                                                if ($inventory_id_fk == $row->inventory_id) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?> ><?php echo $row->code . " - " . $row->item_name; ?></option>
                                                                    <?php }
                                                                    ?>
                                                        </select>  
                                                    </div>
                                                </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Item<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm"  name="customer_rate" id="customer_rate" value="<?php
                                                if (isset($customer_wise_rate) && !empty($customer_wise_rate)) {
                                                    echo $customer_wise_rate['customer_rate'];
                                                }
                                                ?>" required="">
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
