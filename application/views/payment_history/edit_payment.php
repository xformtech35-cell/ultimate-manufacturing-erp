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
                    Payment
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Edit Payment</a></li>
                    <li class="active">Edit Payment Details</li>
                </ol>
            </section>
            
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Payment Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>PaymentController/edit_payment_history" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <!-- form start -->
                                      
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Invoice Number<span style="color: red;">*</span></label>
                                            <div class="col-sm-8">
                                                
                                                <input type="text" class="form-control hide input-sm" name="invocie_pay_id" id="invocie_pay_id" value="<?php
                                                if (isset($payment) && !empty($payment)) {
                                                    echo $payment['invocie_pay_id'];
                                                }
                                                ?>">
                                                
                                                <input type="text" class="form-control input-sm check_current_peending_balance" readonly="" name="invoice_number_fk" id="invoice_number_fk" value="<?php
                                                if (isset($payment) && !empty($payment)) {
                                                    echo $payment['invoice_number_fk'];
                                                }
                                                ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Paid Amount<span style="color: red;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" min="0" class="form-control input-sm"  name="invocie_pay_amount" id="invocie_pay_amount" value="<?php
                                                if (isset($payment) && !empty($payment)) {
                                                    echo $payment['invocie_pay_amount'];
                                                }
                                                ?>" required="">
                                                
                                                <input type="text" class="form-control input-sm hide"  name="invocie_pay_amount_hide" id="invocie_pay_amount_hide" value="<?php
                                                if (isset($payment) && !empty($payment)) {
                                                    echo $payment['invocie_pay_amount'];
                                                }
                                                ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Payment Type<span style="color: red;">*</span></label>
                                            <div class="col-sm-8">
                                                <select class="form-control input-sm " required="" name="payment_type" id="payment_type">
                                                         <?php
                                                            if ($payment['payment_type'] == 'Advance') { ?>
                                                        <option value="Advance" selected="">Advance</option>
                                                        <option value="Partial">Partial</option>
                                                        <option value="Final">Final</option>
                                                            <?php } ?>
                                                         <?php
                                                            if ($payment['payment_type'] == 'Partial') { ?>
                                                        <option value="Advance">Advance</option>
                                                        <option value="Partial" selected="">Partial</option>
                                                        <option value="Final">Final</option>
                                                            <?php } ?>
                                                         <?php
                                                            if ($payment['payment_type'] == 'Final') { ?>
                                                        <option value="Advance">Advance</option>
                                                        <option value="Partial">Partial</option>
                                                        <option value="Final" selected="">Final</option>
                                                            <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Note<span style="color: red;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control input-sm"  name="invoice_pay_remark" id="invoice_pay_remark" value="<?php
                                                if (isset($payment) && !empty($payment)) {
                                                    echo $payment['invoice_pay_remark'];
                                                }
                                                ?>" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Payment Date<span style="color: red;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control backdate input-sm"  name="invoice_pay_date" id="invoice_pay_date" value="<?php
                                                if (isset($payment) && !empty($payment)) {
                                                    echo $payment['invoice_pay_date'];
                                                }
                                                ?>" required="" onkeydown="return false;">
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
