<?php
// Bank Entry landing page
defined('BASEPATH') or exit('No direct script access allowed');

$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <div class="content-wrapper">
            <section class="content-header">
                <h1>Bank Entry</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Bank Entry</a></li>
                    <li class="active">Choose Action</li>
                </ol>
            </section>

            <section class="content">
                <div class="row" style="padding:2%">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Select Entry Type</h3>
                            </div>

                            <div class="box-body">
                                <div class="row" style="margin-top:10px;">
                                    <div class="col-sm-3">
                                        <a class="btn btn-success btn-block btn-lg" href="<?php echo base_url('InvoiceController/payment_in'); ?>">
                                            <i class="fa fa-arrow-circle-right"></i> Customer Bank Entry
                                        </a>
                                    </div>

                                    <div class="col-sm-3">
                                        <a class="btn btn-primary btn-block btn-lg" href="<?php echo base_url('SupplierController/payment_out'); ?>">
                                            <i class="fa fa-file-text-o"></i> Vendor Bank Entry 
                                        </a>
                                    </div>

                                    <div class="col-sm-3">
                                        <a class="btn btn-warning btn-block btn-lg" href="<?php echo base_url('InventoryController/add_expense_data_direct'); ?>">
                                            <i class="fa fa-money"></i> Direct Bank Entry
                                        </a>
                                    </div>

                                    <div class="col-sm-3">
                                        <a class="btn btn-warning btn-block btn-lg" href="<?php echo base_url('InventoryController/add_expense_data_indirect'); ?>">
                                            <i class="fa fa-money"></i> Indirect Bank Entry
                                        </a>
                                    </div>
                                    
                                </div>

                                

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
</body>

