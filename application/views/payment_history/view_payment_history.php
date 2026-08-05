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
                    Payment History
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">View Payment History</a></li>
                    <li class="active">View Payment History Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">View GST Payment History Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                            </div>
                            <!-- /.box-body -->
                            <table id="" class="payment5 table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Invoice Number</th>
                                        <th>Paid Amount</th>
                                        <th>Payment Type</th>
                                        <th>Paid Date</th>
                                        <th>Remark</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($gst_invoice_payment_history as $key) {
                                        ?>
                                    <tr>
                                            <td>
                                                <?php echo $i; ?>
                                            </td>
                                            <td> <?php echo $key->invoice_number_fk; ?> </td>
                                            <td> <?php echo number_format($key->invocie_pay_amount,2); ?> </td>
                                            <td> <?php echo $key->payment_type; ?> </td>
                                            <td> <?php echo $key->invoice_pay_date; ?> </td>
                                            <td> <?php echo $key->invoice_pay_remark; ?> </td>

                                            <td> <a href="<?php echo base_url() . 'PaymentController/get_payment_by_id/' . $key->invocie_pay_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-pencil-square" aria-hidden="true"></i>
                                                </a> 
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->

                </div>
                <div class="row hide">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">View Non GST Payment History Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                            </div>
                            <!-- /.box-body -->
                            <table id="" class="payment5 table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Invoice Number</th>
                                        <th>Paid Amount</th>
                                        <th>Paid Date</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($non_gst_invoice_payment_history as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $i; ?>
                                            </td>
                                            <td> <?php echo $key->ng_invoice_number_fk; ?> </td>
                                            <td> <?php echo $key->ng_invocie_pay_amount; ?> </td>
                                            <td> <?php echo $key->ng_invoice_pay_date; ?> </td>
                                            <td> <?php echo $key->ng_invoice_pay_remark; ?> </td>

                <!--<td><a href="<?php //echo base_url() . 'GstController/delete_gst_class_by_id/' . $key->id;   ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>-->
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
</div>
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

