<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."views/admin/modal.php");

?>
<style>
    .required label {
        font-weight: bold;
    }
    .required label:after {
        color: #e32;
        content: ' *';
        display:inline;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Purchase
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Payment Against Purchase</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                
                <div class="row" style="padding:2%">
                    <div class="pull-left">

                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Payment Against Purchase</h3>
                            </div>
                            <!-- /.box-header -->
                            

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                
                                <div class="box-body">

                                    <p class="text-muted">This page is under construction. You can reuse the <code>payment_out</code> form and add additional fields for linking to purchase invoices.</p>

                                    <!-- TODO: Add form to select supplier and associated purchases -->

                                </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
