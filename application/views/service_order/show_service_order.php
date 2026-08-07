<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1><?php echo $config['title']; ?> Details</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'ServiceOrderController/' . $config['url_prefix']; ?>"><?php echo $config['title']; ?></a></li>
                    <li class="active"><?php echo $invoice_data_group['number_fk']; ?></li>
                </ol>
            </section>

            <section class="content">
                <div class="row" style="padding: 10px 15px;">
                    <a href="<?php echo base_url() . 'ServiceOrderController/' . $config['url_prefix']; ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to List</a>
                    <a href="<?php echo base_url() . 'ServiceOrderController/print_service_order/' . $invoice_data_group['number_fk']; ?>" target="_blank" class="btn btn-primary pull-right" style="margin-left: 10px;"><i class="fa fa-print"></i> Print / PDF</a>
                    <a href="<?php echo base_url() . 'ServiceOrderController/edit_service_order_details/' . $invoice_data_group['number_fk']; ?>" class="btn btn-warning pull-right"><i class="fa fa-edit"></i> Edit</a>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info" style="padding: 20px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3><?php echo $settings['company_name'] ?? 'UWS private limited'; ?></h3>
                                    <p><?php echo nl2br($settings['address'] ?? ''); ?></p>
                                    <p>GSTIN: <?php echo $settings['company_gst'] ?? ''; ?></p>
                                </div>
                                <div class="col-md-6 text-right">
                                    <h2><?php echo strtoupper($config['title']); ?></h2>
                                    <h4>Number: <b><?php echo $invoice_data_group['number_fk']; ?></b></h4>
                                    <p>Date: <?php echo date('d-m-Y', strtotime($invoice_data_group['date'])); ?></p>
                                    <?php if (!empty($invoice_data_group['po_number'])) { ?>
                                        <p>PO Number: <?php echo htmlspecialchars($invoice_data_group['po_number']); ?></p>
                                    <?php } ?>
                                </div>
                            </div>
                            
                            <hr>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-6">
                                    <h4><b>Customer Details:</b></h4>
                                    <p><b>Company Name:</b> <?php echo $invoice_data_group['company_name']; ?></p>
                                    <p><b>Recipient Name:</b> <?php echo $invoice_data_group['fullname']; ?></p>
                                    <p><b>GSTIN:</b> <?php echo $invoice_data_group['customer_gst_no']; ?></p>
                                    <p><b>PAN:</b> <?php echo $invoice_data_group['customer_pancard_no']; ?></p>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 30px;">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr style="background:#f4f4f4;">
                                                <th style="width: 5%;">Sr.No.</th>
                                                <th style="width: 30%;">Service Name</th>
                                                <th style="width: 30%;">Description</th>
                                                <th style="width: 8%;">SAC</th>
                                                <th style="width: 5%;">Qty</th>
                                                <th style="width: 5%;">Unit</th>
                                                <th style="width: 10%;">Price</th>
                                                <th style="width: 7%;">GST %</th>
                                                <th style="width: 10%;">Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $i = 1;
                                            $total_cgst = 0;
                                            $total_sgst = 0;
                                            $total_igst = 0;
                                            $is_igst = false;
                                            foreach ($show_invoice as $item) { 
                                                if ($item->gst_type == 'I') {
                                                    $is_igst = true;
                                                    $total_igst += $item->igst;
                                                } else {
                                                    $total_cgst += $item->cgst;
                                                    $total_sgst += $item->sgst;
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><b><?php echo htmlspecialchars($item->service_name); ?></b></td>
                                                    <td><?php echo nl2br(htmlspecialchars($item->description)); ?></td>
                                                    <td><?php echo htmlspecialchars($item->sac_code); ?></td>
                                                    <td><?php echo $item->quantity; ?></td>
                                                    <td><?php echo htmlspecialchars($item->unit); ?></td>
                                                    <td><?php echo number_format($item->price, 2); ?></td>
                                                    <td><?php echo $item->gst; ?>%</td>
                                                    <td><?php echo number_format($item->amount, 2); ?></td>
                                                </tr>
                                                <?php 
                                                $i++;
                                            } 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-6">
                                    <?php if (!empty($invoice_data_group['service_order_memo'])) { ?>
                                        <p><b>Memo:</b> <?php echo nl2br(htmlspecialchars($invoice_data_group['service_order_memo'])); ?></p>
                                    <?php } ?>
                                </div>
                                <div class="col-md-6 text-right">
                                    <p>Basic Total: <b><?php echo number_format($invoice_data_group['basic_total'], 2); ?></b></p>
                                    <?php if ($is_igst) { ?>
                                        <p>IGST Total: <b><?php echo number_format($total_igst, 2); ?></b></p>
                                    <?php } else { ?>
                                        <p>CGST Total: <b><?php echo number_format($total_cgst, 2); ?></b></p>
                                        <p>SGST Total: <b><?php echo number_format($total_sgst, 2); ?></b></p>
                                    <?php } ?>
                                    <hr>
                                    <h3>Grand Total: <b><?php echo number_format($invoice_data_group['total'], 2); ?></b></h3>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px;">
                                <div class="col-md-12">
                                    <h3>Terms & Conditions Details:</h3>
                                    
                                    <div class="col-md-6" style="padding-left:0;">
                                        <?php if (!empty($invoice_data_group['terms_and_conditions'])) { ?>
                                            <p><b>Terms & Conditions:</b><br><?php echo nl2br(htmlspecialchars($invoice_data_group['terms_and_conditions'])); ?></p>
                                        <?php } ?>
                                        <?php if (!empty($invoice_data_group['payment_terms'])) { ?>
                                            <p><b>Payment Terms:</b><br><?php echo nl2br(htmlspecialchars($invoice_data_group['payment_terms'])); ?></p>
                                        <?php } ?>
                                        <?php if (!empty($invoice_data_group['transportation'])) { ?>
                                            <p><b>Transportation:</b><br><?php echo nl2br(htmlspecialchars($invoice_data_group['transportation'])); ?></p>
                                        <?php } ?>
                                    </div>

                                    <div class="col-md-6">
                                        <?php if (!empty($invoice_data_group['installation'])) { ?>
                                            <p><b>Installation:</b><br><?php echo nl2br(htmlspecialchars($invoice_data_group['installation'])); ?></p>
                                        <?php } ?>
                                        <?php if (!empty($invoice_data_group['process_schedule'])) { ?>
                                            <p><b>Process Schedule:</b><br><?php echo nl2br(htmlspecialchars($invoice_data_group['process_schedule'])); ?></p>
                                        <?php } ?>
                                        <?php if (!empty($invoice_data_group['taxes'])) { ?>
                                            <p><b>Taxes:</b><br><?php echo nl2br(htmlspecialchars($invoice_data_group['taxes'])); ?></p>
                                        <?php } ?>
                                        <?php if (!empty($invoice_data_group['exclusions'])) { ?>
                                            <p><b>Exclusions:</b><br><?php echo nl2br(htmlspecialchars($invoice_data_group['exclusions'])); ?></p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
</body>
