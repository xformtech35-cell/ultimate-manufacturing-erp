<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Order Confirmation Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url(); ?>OrderConfirmationController/index">Order Confirmation</a></li>
                    <li class="active">View OC</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">OC #<?php echo $oc['number_fk']; ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/print_oa_letter/<?php echo $oc['number_fk']; ?>" class="btn btn-primary btn-sm" target="_blank">
                                        <i class="fa fa-file-pdf-o"></i> Print OA Letter
                                    </a>
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/print_order_confirmation/<?php echo $oc['number_fk']; ?>" class="btn btn-info btn-sm" target="_blank">
                                        <i class="fa fa-print"></i> Print Register Format
                                    </a>
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/edit_order_confirmation_details/<?php echo $oc['number_fk']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/delete_order_confirmation/<?php echo $oc['number_fk']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this Order Confirmation?');">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <!-- Flash Messages -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                        <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <!-- Status Update Buttons -->
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            <?php 
                                            $status = isset($oc['status']) ? $oc['status'] : 1;
                                            $status_badge = '';
                                            $status_class = '';
                                            switch($status) {
                                                case 1:
                                                    $status_badge = 'Draft';
                                                    $status_class = 'label-warning';
                                                    break;
                                                case 2:
                                                    $status_badge = 'Sent to Customer';
                                                    $status_class = 'label-info';
                                                    break;
                                                case 3:
                                                    $status_badge = 'Customer Accepted (Confirmed)';
                                                    $status_class = 'label-success';
                                                    break;
                                                case 4:
                                                    $status_badge = 'Follow up with Customer (Rejected)';
                                                    $status_class = 'label-danger';
                                                    break;
                                                case 5:
                                                    $status_badge = 'Cancelled';
                                                    $status_class = 'label-default';
                                                    break;
                                            }
                                            ?>
                                            <span class="label <?php echo $status_class; ?>" style="font-size: 14px; padding: 8px 15px;">Current Status: <?php echo $status_badge; ?></span>
                                            
                                            <?php if($status == 1) { ?>
                                                <a href="<?php echo base_url(); ?>OrderConfirmationController/update_status/<?php echo $oc['number_fk']; ?>/2" class="btn btn-sm btn-info" style="margin-left: 10px;"><i class="fa fa-send"></i> Issue OA to Customer</a>
                                            <?php } ?>
                                            <?php if($status == 2 || $status == 4) { ?>
                                                <a href="<?php echo base_url(); ?>OrderConfirmationController/update_status/<?php echo $oc['number_fk']; ?>/3" class="btn btn-sm btn-success" style="margin-left: 10px;"><i class="fa fa-check-circle"></i> Customer Accepts (Yes)</a>
                                                <a href="<?php echo base_url(); ?>OrderConfirmationController/update_status/<?php echo $oc['number_fk']; ?>/4" class="btn btn-sm btn-danger" style="margin-left: 5px;"><i class="fa fa-phone"></i> Follow up with Customer (No)</a>
                                            <?php } ?>
                                            <?php if($status == 3) { ?>
                                                <a href="<?php echo base_url(); ?>OrderConfirmationController/update_status/<?php echo $oc['number_fk']; ?>/5" class="btn btn-sm btn-default" style="margin-left: 10px;"><i class="fa fa-ban"></i> Cancel Order</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Formatted Order Acceptance Letter Document Preview -->
                                <?php
                                    $comp_name = !empty($settings['company_name']) ? $settings['company_name'] : 'UWS ENVIRO-TECH PVT LTD';
                                    $comp_tagline = !empty($settings['tagline']) ? $settings['tagline'] : 'Ultimate Technologies for Fluid Automation';
                                    $logo_path = !empty($settings['company_logo']) ? base_url() . $settings['company_logo'] : (!empty($settings['logo']) ? base_url() . $settings['logo'] : base_url() . 'uploads/xform-logo.jpg');
                                    $stamp_path = !empty($settings['company_stamp']) ? base_url() . $settings['company_stamp'] : (!empty($settings['stamp_signature']) ? base_url() . $settings['stamp_signature'] : '');
                                    $address = !empty($settings['address']) ? $settings['address'] : (!empty($settings['company_address']) ? $settings['company_address'] : 'Plot No. 19/C, D-1 Block, Shop No. 342, 3rd Floor, HEUU Industrial Spaces, MIDC Chinchwad, Pune-411019.');
                                    $email = !empty($settings['email']) ? $settings['email'] : (!empty($settings['company_email']) ? $settings['company_email'] : 'projects@ultimatewater.in');
                                    $website = !empty($settings['website']) ? $settings['website'] : 'www.ultimatewater.in';
                                    $phone = !empty($settings['mobile']) ? $settings['mobile'] : (!empty($settings['company_mobile']) ? $settings['company_mobile'] : '020 29528571');

                                    $sub_total_num = isset($oc['sub_total']) ? (float)$oc['sub_total'] : 0;
                                    $tax_num = isset($oc['tax_amount']) ? (float)$oc['tax_amount'] : 0;
                                    $gst_per = ($sub_total_num > 0 && $tax_num > 0) ? round(($tax_num / $sub_total_num) * 100) : 18;
                                ?>

                                <div class="row" style="margin-bottom: 25px;">
                                    <div class="col-md-12">
                                        <div style="background: #fff; border: 4px double #000; padding: 35px 45px; max-width: 900px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-family: Calibri, 'Segoe UI', Arial, sans-serif;">
                                            
                                            <!-- Letterhead Header -->
                                            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                                                <tr>
                                                    <td style="width:85px; vertical-align:middle;">
                                                        <img src="<?php echo $logo_path; ?>" style="max-width:80px; height:auto;" onerror="this.style.display='none';">
                                                    </td>
                                                    <td style="padding-left:15px;">
                                                        <div style="font-size:20pt; font-weight:bold; color:#0d2b5c; font-family:Calibri, Arial, sans-serif;"><?php echo strtoupper($comp_name); ?></div>
                                                        <div style="font-size:11pt; color:#c00000; font-style:italic; font-weight:bold; font-family:Calibri, Arial, sans-serif;"><?php echo $comp_tagline; ?></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="text-align:center; font-size:14pt; font-weight:bold; text-decoration:underline; margin:20px 0 25px 0;">
                                                Order Acceptance Letter
                                            </div>

                                            <table style="width:100%; margin-bottom:20px; font-size:11pt;">
                                                <tr>
                                                    <td align="left"><strong>Ref. No.</strong> <?php echo isset($oc['number_fk']) ? $oc['number_fk'] : ''; ?></td>
                                                    <td align="right"><strong>Date:</strong> <?php echo isset($oc['date']) ? date('d.m.Y', strtotime($oc['date'])) : date('d.m.Y'); ?></td>
                                                </tr>
                                            </table>

                                            <div style="font-size:11pt; margin-bottom:20px; line-height:1.5;">
                                                <strong>Subject:</strong> Order Acceptance Letter against <?php echo !empty($oc['subject']) ? $oc['subject'] : 'DOSING SYSTEM'; ?>.
                                            </div>

                                            <div style="margin-bottom:12px; font-size:11pt;">Dear Sir,</div>
                                            <div style="margin-bottom:12px; font-size:11pt;">We thank you for valuable opportunity provided to us.</div>
                                            <div style="margin-bottom:12px; font-size:11pt;">
                                                We acknowledge with thanks for the receipt of valuable PO No: <strong><?php echo !empty($oc['po_reference']) ? $oc['po_reference'] : '-'; ?></strong> DT: <strong><?php echo !empty($oc['po_date']) ? date('d.m.Y', strtotime($oc['po_date'])) : '-'; ?></strong>.
                                            </div>
                                            <div style="margin-bottom:16px; font-size:11pt; text-align:justify;">
                                                We hereby acknowledge receipt of PO & accept with basic amount of <strong>Rs. <?php echo number_format($sub_total_num, 2); ?> /-</strong> <strong>+<?php echo $gst_per; ?>% GST extra</strong> payable at actual on basic with following standard terms & conditions.
                                            </div>

                                            <ol style="margin:18px 0; padding-left:20px; font-size:11pt; line-height:1.6; list-style-type:none;">
                                                <li style="margin-bottom:10px;"><strong>1) Price Basis:</strong> <?php echo !empty($oc['price_basis']) ? $oc['price_basis'] : 'Ex-works Talwade, Pune.'; ?></li>
                                                <li style="margin-bottom:10px;"><strong>2) Payment Terms:</strong> <?php echo !empty($oc['payment_terms']) ? $oc['payment_terms'] : 'Standard terms.'; ?></li>
                                                <li style="margin-bottom:10px;"><strong>3) Transportation Charges:</strong> <?php echo !empty($oc['transportation_charges']) ? $oc['transportation_charges'] : 'Extra at actuals.'; ?></li>
                                                <li style="margin-bottom:10px;"><strong>4) Dispatch Date:</strong> On or before <?php echo !empty($oc['delivery_date']) ? date('d.m.Y', strtotime($oc['delivery_date'])) : '-'; ?>.</li>
                                                <li style="margin-bottom:10px;"><strong>5) Service Charges:</strong> <?php echo !empty($oc['service_charges']) ? $oc['service_charges'] : 'Extra as applicable.'; ?></li>
                                                <li style="margin-bottom:10px;"><strong>6) Warranty:</strong> <?php echo !empty($oc['warranty']) ? $oc['warranty'] : '12 months against manufacturing defects.'; ?></li>
                                            </ol>

                                            <div style="margin-bottom:12px; font-size:11pt;">We will start further proceedings on priority basis.</div>
                                            <div style="margin-bottom:25px; font-size:11pt;">Thanking you.</div>

                                            <div style="margin-top:30px; font-size:11pt;">
                                                <div>For <strong><?php echo $comp_name; ?></strong></div>
                                                <div style="width:120px; min-height:65px; margin:8px 0;">
                                                    <?php if(!empty($stamp_path)): ?>
                                                        <img src="<?php echo $stamp_path; ?>" style="max-width:120px; max-height:65px;">
                                                    <?php else: ?>
                                                        <div style="border:1px dashed #007bff; color:#007bff; padding:8px; font-size:10px; text-align:center;">[Stamp & Signature]</div>
                                                    <?php endif; ?>
                                                </div>
                                                <div><strong>Authorized Signatory</strong></div>
                                            </div>

                                            <div style="margin-top:40px; border-top:1px solid #ddd; padding-top:15px; text-align:center; font-size:10pt; line-height:1.4;">
                                                <div style="font-weight:bold; font-size:12pt; color:#3b1660;"><?php echo $comp_name; ?></div>
                                                <div style="font-weight:bold; color:#000;"><?php echo $address; ?></div>
                                                <div style="margin-top:2px;">E-mail: <span style="color:#0000ff; text-decoration:underline;"><?php echo $email; ?></span> &nbsp; Website: <span style="color:#0000ff; text-decoration:underline;"><?php echo $website; ?></span></div>
                                                <div style="font-weight:bold; margin-top:2px;">Phone: <?php echo $phone; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Items Table -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>OC Items</h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Description</th>
                                                        <th>HSN Code</th>
                                                        <th>Quantity</th>
                                                        <th>Unit</th>
                                                        <th>Unit Price</th>
                                                        <th>Tax Rate</th>
                                                        <th>Tax Amount</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    if(isset($oc_detail) && !empty($oc_detail)) {
                                                        $i = 1;
                                                        foreach($oc_detail as $detail) {
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $i; ?></td>
                                                            <td><?php echo $detail['description']; ?></td>
                                                            <td><?php echo $detail['hsn_code'] ? $detail['hsn_code'] : '-'; ?></td>
                                                            <td><?php echo number_format($detail['quantity'], 2); ?></td>
                                                            <td><?php echo $detail['unit'] ? $detail['unit'] : '-'; ?></td>
                                                            <td><?php echo number_format($detail['unit_price'], 2); ?></td>
                                                            <td><?php echo number_format($detail['tax_rate'], 2); ?></td>
                                                            <td><?php echo number_format($detail['tax_amount'], 2); ?></td>
                                                            <td><?php echo number_format($detail['amount'], 2); ?></td>
                                                        </tr>
                                                    <?php 
                                                            $i++;
                                                        }
                                                    } else {
                                                    ?>
                                                        <tr>
                                                            <td colspan="9" class="text-center">No items found.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="8" class="text-right">Sub Total</th>
                                                        <th><?php echo number_format($oc['sub_total'], 2); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="8" class="text-right">Tax Amount</th>
                                                        <th><?php echo number_format($oc['tax_amount'], 2); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="8" class="text-right">Total Amount</th>
                                                        <th><?php echo number_format($oc['total'], 2); ?></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
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

