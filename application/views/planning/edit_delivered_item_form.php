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
                    Delivered Item
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Delivered Item</h3><br><br>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Sorry!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal" id="form_overlay" method="post" action="<?php echo base_url(); ?>PlanningController/add_delivered_item">
                                    <div class="modal-body">
                                        <div class="card-body ">
                                            <!-- form start -->
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Item Name</label>
                                                <div class="col-sm-7">
                                                    <input name="raw_item_delivery_id" value="<?php echo $delivered_item['raw_item_delivery_id']; ?>" type="hidden">
                                                    <!--<input type="text" class="form-control input-sm" name="raw_item_name" id="raw_item_name" maxlength="30" value="<?php echo $delivered_item['raw_item_name']; ?>" style="text-transform:uppercase" required=""/>-->
                                                    <select name="raw_item_name" class="form-control input-sm" required="">
                                                        <option value="">Select Product</option>
                                                        <?php foreach($raw_items as $key){ ?>
                                                        <option value="<?php echo $key->raw_item_master_name; ?>" <?php if($key->raw_item_master_name == $delivered_item['raw_item_name']){ echo 'selected'; } ?>><?php echo $key->raw_item_master_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Item Quantity</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm" name="raw_item_qty" id="raw_item_qty" maxlength="30" value="<?php echo $delivered_item['raw_item_qty']; ?>" style="text-transform:uppercase" required=""/>
                                                    <input type="hidden" class="form-control input-sm" name="raw_item_qty_hide" value="<?php echo $delivered_item['raw_item_qty']; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Item Unit</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm" name="raw_item_unit" id="raw_item_unit" maxlength="30" value="<?php echo $delivered_item['raw_item_unit']; ?>" style="text-transform:uppercase" required=""/>
                                                </div>
                                            </div>
                                          
                                    </div>
                                    <div class="modal-footer">
                                        <!--                        <button type="reset" class="btn btn-danger"> Reset</button>-->
                                        <button type="submit" id="btnSave"  class="btn btn-success">Submit</button>
                                    </div>
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


