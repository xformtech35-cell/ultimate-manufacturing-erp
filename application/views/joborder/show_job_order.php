<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);

// helper for formatting dates in the show view
function format_show_date($date) {
    if (empty($date) || $date === '0000-00-00' || $date === '1970-01-01') {
        return '';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return $date;
    }
    return date('d-m-Y', $ts);
}
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'JobOrderController/index/' ?>">Job Order</a></li>
                    <li class="active">Job Order Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>Job Order: <?php echo isset($joborder['number_fk']) ? $joborder['number_fk'] : ''; ?></h2></label>

                        <div class="row" style="padding:2%">
                            <div class="pull-left">
                                <div class="col-md-2">
                                    <a href="<?php echo base_url(); ?>JobOrderController/edit_job_order_details/<?php echo isset($joborder['number_fk']) ? $joborder['number_fk'] : ''; ?>" class="btn btn-primary" role="button"><i class="fa fa-edit"></i> Edit</a>
                                </div>
                                <div class="pull-right">
                                    <div class="col-md-2">
                                        <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="shadows1">
                            <div class="row">
                                <section class="contemporary-template__header">
                                    <div class="col-md-6">
                                        <div class="contemporary-template__header__logo">
                                            <center><img class="contemporary-template__business-logo" src="<?php echo base_url() . (isset($settings['company_logo']) ? $settings['company_logo'] : 'assets/img/logo.png'); ?>" width="30%" height="15%" style="margin-top:26px;"></center>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="contemporary-template__header__info">
                                            <div class="wv-heading--title"><h1>JOB ORDER</h1></div>
                                            <div class="wv-heading--subtitle"></div>
                                            <span class="wv-text--strong"><b><?php echo isset($settings['company_name']) ? $settings['company_name'] : ''; ?></b></span><br>
                                            <span class="wv-text--strong"><b>GST No :</b> <?php echo isset($settings['company_gst']) ? $settings['company_gst'] : ''; ?></span><br>
                                            <span class="wv-text--strong"><b>PAN No :</b> <?php echo isset($settings['company_pan']) ? $settings['company_pan'] : ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Mobile Number :</b> <?php echo isset($settings['mobile']) ? $settings['mobile'] : ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Email ID :</b> <?php echo isset($settings['email']) ? $settings['email'] : ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Address :</b> <?php echo isset($settings['address']) ? $settings['address'] : ''; ?></span>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="contemporary-template__header__info">
                                        <div class="wv-heading--subtitle"><b>Project Details :</b></div>
                                        <br>
                                        <?php if ($_has_project_master): ?>
                                         <span class="wv-text--strong"><b>Project Code :</b> <?php echo isset($joborder['project_code']) && !empty($joborder['project_code']) ? $joborder['project_code'] : 'N/A'; ?></span><br>
                                         <?php endif; ?>
                                        <span class="wv-text--strong"><b>Customer Name :</b> <?php echo isset($joborder['company_name']) ? $joborder['company_name'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Address :</b> <?php echo isset($joborder['customer_address']) && !empty($joborder['customer_address']) ? $joborder['customer_address'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Email :</b> <?php echo isset($joborder['customer_email']) && !empty($joborder['customer_email']) ? $joborder['customer_email'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Mobile :</b> <?php echo isset($joborder['customer_mobile']) && !empty($joborder['customer_mobile']) ? $joborder['customer_mobile'] : 'N/A'; ?></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="contemporary-template__header__info">
                                        <div class="wv-heading--subtitle"><b>Job Order Details :</b></div>
                                        <br>
                                        <span class="wv-text--strong"><b>Job Order Number :</b> <?php echo isset($joborder['number_fk']) ? $joborder['number_fk'] : ''; ?></span><br>
                                        <span class="wv-text--strong"><b>Job Order Date :</b> <?php echo format_show_date(isset($joborder['date']) ? $joborder['date'] : ''); ?></span><br>
                                        <span class="wv-text--strong"><b>Due Date :</b> <?php echo format_show_date(isset($joborder['due_date']) ? $joborder['due_date'] : ''); ?></span><br>
                                        <span class="wv-text--strong"><b>Payment Terms :</b> <?php echo isset($joborder['payment_terms']) && !empty($joborder['payment_terms']) ? $joborder['payment_terms'] : 'N/A'; ?></span><br>
                                        <?php 
                                        $status = isset($joborder['status']) ? $joborder['status'] : '';
                                        $status_badge = '';
                                        $status_class = '';
                                        switch($status) {
                                            case 1:
                                                $status_badge = 'Draft';
                                                $status_class = 'label-warning';
                                                break;
                                            case 2:
                                                $status_badge = 'Sent';
                                                $status_class = 'label-info';
                                                break;
                                            default:
                                                $status_badge = 'Active';
                                                $status_class = 'label-success';
                                        }
                                        ?>
                                        <span class="wv-text--strong"><b>Status :</b> <span class="label <?php echo $status_class; ?>"><?php echo $status_badge; ?></span></span>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="table-responsive">
                                <h4><strong>Job Order Line Items</strong></h4>
                                <table class="table table-bordered" id="dynamic_field">
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Item Code</th>
                                        <th>Equipment</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Tag No.</th>
                                        <th>Scope</th>
                                        <th>Stores Remark (Y/N)</th>
                                        <th>Remark</th>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    if(isset($joborder_detail) && !empty($joborder_detail)) {
                                        foreach($joborder_detail as $detail) {
                                            ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo isset($detail['item_code']) && !empty($detail['item_code']) ? $detail['item_code'] : '-'; ?></td>
                                                <td><?php echo isset($detail['equipment']) ? $detail['equipment'] : ''; ?></td>
                                                <td><?php echo isset($detail['quantity']) ? number_format($detail['quantity'], 2) : '-'; ?></td>
                                                <td><?php echo isset($detail['unit']) && !empty($detail['unit']) ? $detail['unit'] : '-'; ?></td>
                                                <td><?php echo isset($detail['tag_no']) && !empty($detail['tag_no']) ? $detail['tag_no'] : '-'; ?></td>
                                                <td><?php echo isset($detail['scope']) && !empty($detail['scope']) ? $detail['scope'] : '-'; ?></td>
                                                <td><?php echo isset($detail['stores_remark']) && !empty($detail['stores_remark']) ? $detail['stores_remark'] : '-'; ?></td>
                                                <td><?php echo isset($detail['remark']) && !empty($detail['remark']) ? $detail['remark'] : '-'; ?></td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No Items</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div>

                            <?php if(isset($joborder['remarks']) && !empty($joborder['remarks'])) { ?>
                            <div style="margin: 10px">
                                <label class="control-label"><b>Remarks</b></label><br>
                                <div class="col-sm-12">
                                    <textarea style="overflow: auto; border: none;" class="form-control" readonly="" name="remarks" id="joborder_remarks" rows="4"><?php echo $joborder['remarks']; ?></textarea><br>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Sign :</b></label>
                                <label for="inputEmail3" class="col-sm-3 control-label"><b>Authorized Sign :</b></label>
                            </div>

                            <center style="font-size: 10px">This is Computer Generated Job Order</center><br>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2024</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="<?php echo base_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>

