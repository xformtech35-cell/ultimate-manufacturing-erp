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
                    Advance
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Edit Advance</a></li>
                    <li class="active">Edit Advance Details</li>
                </ol>
            </section>
            
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Advance Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>AdvanceController/edit_advance" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <!-- form start -->
                                        
                                        <div class="form-group row">
                                               <label for="inputEmail3" class="col-sm-3 control-label">Customer Name</label>
                                                <div class="col-sm-8">
                                                      <input type="hidden" class="form-control input-sm" name="advance_id" id="advance_id" value="<?php
                                                if (isset($advance) && !empty($advance)) {
                                                    echo $advance['advance_id'];
                                                }
                                                ?>" required="">
                                                    <select class="col-md-12 company_search_name" name="customer_id" id="customer_id" required="">
                                                        <option value="">Select Customer</option>
                                                        <?php
                                                        $company_name = $advance['customer_id_fk'];
                                                       
                                                        foreach ($customer_result as $row) {
                                                            ?>
                                                            <option value="<?php echo $row->customer_id ?>"  
                                                            <?php
                                                            if ($company_name == $row->customer_id) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?> ><?php echo $row->company_name . " - " . $row->c_code; ?></option>
                                                                <?php }
                                                                ?>
                                                    </select>  

                                                </div>

                                            </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Advance</label>
                                            <div class="col-sm-8">
                                                <input type="text" min="0" class="form-control input-sm"  name="advance_pay" id="advance_pay" value="<?php
                                                if (isset($advance) && !empty($advance)) {
                                                    echo $advance['advance_pay'];
                                                }
                                                ?>">
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
