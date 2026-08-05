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
                    Ledger Report 
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Ledger Report</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Sales Ledger</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>PaymentController/get_gst_ledger">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label ">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="from_date" class="form-control backdate" name="from_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label ">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="to_date" class="form-control" name="to_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Company</label>
                                        <div class="col-sm-9">
                                            <select style="width:350px" class="form-control input-sm company_search_name" name="company_name" id="company_name">
                                                <option value="">Select Company</option>
                                                <?php foreach ($company_name as $key) { ?>
                                                    <option value="<?php echo $key->customer_id; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                                <?php } ?>  
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                    <!-- right column -->

                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
                
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Purchase Ledger</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>PaymentController/get_purchse_ledger">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label ">Froms Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="from_date2" class="form-control backdate" name="from_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label ">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-9">
                                            <input type="text" id="to_date2" class="form-control" name="to_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Company</label>
                                        <div class="col-sm-9">
                                            <select style="width:350px" class="form-control input-sm product_name_auto item_search_vendor name_list" name="supplier_name" id="supplier_name" >
                                                <option value="">Select Company</option>
                                                        <?php foreach ($result as $key) { ?>
                                                            <option value="<?php echo $key->supplier_id; ?>"><?php echo $key->company_name . " - " . $key->s_code; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row hide">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Item</label>
                                        <div class="col-sm-9">
                                    
                                            <select name="code" id="code" class="form-control item_search_name">
                                                <option value="">Select Item</option>
                                                   <option value="NEW">+ Add New Product</option>

                                                <?php
                                                foreach ($item_name as $key){?>
                                                            <option value="<?php echo $key->code; ?>"><?php echo $key->code . " - " . $key->item_name; ?></option>
                                            <?php }
                                                ?>
                                            </select>
                                     </div>
                                    </div>
                                    
                                    
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->
                   
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->

        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->


