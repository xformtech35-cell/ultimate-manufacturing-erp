<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
require_once(APPPATH . '/third_party/amount_convert.php');

$po_data_group = isset($po_data_group) && is_array($po_data_group) ? $po_data_group : [];
$show_po = isset($show_po) && is_array($show_po) ? $show_po : [];
$payment_history = isset($payment_history) && is_array($payment_history) ? $payment_history : [];
$amendments = isset($amendments) && is_array($amendments) ? $amendments : [];
$revisions = isset($revisions) && is_array($revisions) ? $revisions : [];
$settings = isset($settings) && is_array($settings) ? $settings : [];

$po_data_group = array_merge([
    'id' => '',
    'number' => '',
    'status' => 0,
    'company_name' => '',
    'fullname' => '',
    's_code' => 'N/A',
    'address' => '',
    'gst' => '',
    'pancard' => '',
    'mobile' => '',
    'email' => '',
    'purchase_date' => '',
    'delivery_date' => '',
    'total' => 0,
    'po_terms_and_conditions' => '',
    'po_payment_terms' => '',
    'po_process_schedule' => '',
    'po_taxes' => '',
    'po_exclusions' => '',
    'po_note' => '',
], $po_data_group);

$settings = array_merge([
    'company_logo' => '',
    'company_name' => '',
    'address' => '',
    'company_gst' => '',
    'company_pan' => '',
    'mobile' => '',
    'email' => '',
], $settings);

$purchase_display_date = !empty($po_data_group['purchase_date']) ? date('d-m-Y', strtotime($po_data_group['purchase_date'])) : '';
$delivery_display_date = !empty($po_data_group['delivery_date']) ? date('d-m-Y', strtotime($po_data_group['delivery_date'])) : '';
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="row">
                    <div class="col-md-8">
                        <h1>
                            <i class="fa fa-file-text-o"></i> Purchase Order Details
                            <small>PO: <?php echo $po_data_group['number']; ?></small>
                        </h1>
                    </div>
                    <div class="col-md-4">
                        <ol class="breadcrumb pull-right">
                            <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                            <li><a href="<?php echo base_url() . 'SupplierController/view_purchase_order' ?>"><i class="fa fa-shopping-cart"></i> Purchase Orders</a></li>
                            <li class="active"><i class="fa fa-eye"></i> View PO</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-body">
                                <div class="btn-group" style="display: flex; flex-direction: row; gap:10px">
                                    <a href="<?php echo base_url() . 'SupplierController/view_purchase_order'; ?>" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                    <a href="<?php echo base_url(); ?>SupplierController/edit_po_details/<?php echo $po_data_group['number']; ?>" class="btn btn-primary" style="margin-left: 10px;">
                                        <i class="fa fa-edit"></i> Edit PO
                                    </a>
                                    <a href="<?php echo base_url() . 'Pdf/download_po/' . $po_data_group['number']; ?>" target="_blank" class="btn btn-primary" style="margin-left: 10px;">
                                        <i class="fa fa-file-pdf-o "></i> Export PDF
                                    </a>
                                    <div class="btn-group hide">
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-send"></i> Send <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="send-email-po" data-po-number="<?php echo $po_data_group['number']; ?>">
                                                    <i class="fa fa-envelope"></i> Send via Email
                                                </a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="https://www.gmail.com" target="_blank">
                                                    <i class="fa fa-google"></i> Send via Gmail
                                                </a></li>
                                            <li><a href="https://outlook.live.com" target="_blank">
                                                    <i class="fa fa-envelope"></i> Send via Outlook
                                                </a></li>
                                        </ul>
                                    </div>
                                    <div class="btn-group hide">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cog"></i> Actions <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo base_url(); ?>Pdf/download_po/<?php echo $po_data_group['number']; ?>" target="_blank">
                                                    <i class="fa fa-file-pdf-o text-danger"></i> Export As PDF
                                                </a></li>
                                            <li><a href="<?php echo base_url(); ?>Pdf/download_po/<?php echo $po_data_group['number']; ?>?print=1" target="_blank">
                                                    <i class="fa fa-print text-primary"></i> Print
                                                </a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="<?php echo base_url(); ?>LoginController/get_settings">
                                                    <i class="fa fa-building text-info"></i> Edit Business Info
                                                </a></li>
                                        </ul>
                                    </div>
                                    <form method="POST" style="margin-left: 20 px;" action="<?php echo base_url(); ?>SupplierController/convert_to_purchase_bill/<?php echo $po_data_group['id']; ?>" style="display: inline-block;">
                                        <?php
                                        if (date('m') <= 3) {
                                            $financial_year =  (date('y') - 1) . '-' . date('y');
                                        } else {
                                            $financial_year =  date('y') . '-' . (date('y') + 1);
                                        }
                                        ?>
                                        <input type="hidden" name="number" value="<?php echo $po_data_group['id']; ?>">
                                        <input type="hidden" name="po_number" value="<?php echo $po_data_group['number']; ?>">
                                        <input type="hidden" name="purchases_bill_number" value="VCH/<?php printf("%04d", $purcahse_bill_id + 1); ?>/<?php echo $financial_year; ?>">
                                        <button type="submit" class="btn btn-warning "> Convert to Purchase Voucher
                                            <i class="fa fa-exchange"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo base_url(); ?>SupplierController/convert_to_delivery_challan/<?php echo $po_data_group['id']; ?>" style="display: inline-block;">
                                        <?php
                                        if (date('m') <= 3) {
                                            $financial_year =  (date('y') - 1) . '-' . date('y');
                                        } else {
                                            $financial_year =  date('y') . '-' . (date('y') + 1);
                                        }
                                        ?>
                                        <input type="hidden" name="number" value="<?php echo $po_data_group['id']; ?>">
                                        <input type="hidden" name="invoice_number" value="DC/<?php printf("%04d", $invoice_id + 1); ?>/<?php echo $financial_year; ?>">
                                        <button type="submit" class="btn btn-primary hide">
                                            <i class="fa fa-truck"></i> Convert to Delivery Challan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PO Details Card -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-file-text-o"></i> Purchase Order Details
                                    <span class="label label-primary"><?php echo $po_data_group['number']; ?></span>
                                    <?php
                                    // Status badge
                                    $status_class = '';
                                    $status_text = '';
                                    $status = $po_data_group['status'] ?? 0;
                                    switch ($status) {
                                        case 1:
                                            $status_class = 'default';
                                            $status_text = 'Draft';
                                            break;
                                        case 2:
                                            $status_class = 'info';
                                            $status_text = 'Sent';
                                            break;
                                        case 3:
                                            $status_class = 'primary';
                                            $status_text = 'Viewed';
                                            break;
                                        case 4:
                                            $status_class = 'success';
                                            $status_text = 'Approved';
                                            break;
                                        case 5:
                                            $status_class = 'danger';
                                            $status_text = 'Rejected';
                                            break;
                                        case 6:
                                            $status_class = 'warning';
                                            $status_text = 'Cancelled';
                                            break;
                                        default:
                                            $status_class = 'default';
                                            $status_text = 'Pending';
                                    }
                                    ?>
                                    <span class="label label-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </h3>
                            </div>
                            <div class="box-body">
                                <!-- Header Section -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title"><i class="fa fa-building"></i> Company Details</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <img src="<?php echo base_url() . $settings['company_logo'] ?>" class="img-responsive" style="max-height: 140px;">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h4><strong><?php echo $settings['company_name']; ?></strong></h4>
                                                        <p>
                                                            <i class="fa fa-map-marker"></i> <?php echo $settings['address']; ?><br>
                                                            <i class="fa fa-id-card"></i> GST: <?php echo $settings['company_gst']; ?><br>
                                                            <i class="fa fa-credit-card"></i> PAN: <?php echo $settings['company_pan']; ?><br>
                                                            <i class="fa fa-phone"></i> <?php echo $settings['mobile']; ?><br>
                                                            <i class="fa fa-envelope"></i> <?php echo $settings['email']; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title"><i class="fa fa-user"></i> Vendor Details</h4>
                                            </div>
                                            <div class="panel-body">
                                                <h4><strong><?php echo $po_data_group['company_name']; ?></strong></h4>
                                                <p>
                                                    <i class="fa fa-hashtag"></i> Vendor Code: <?php echo $po_data_group['s_code'] ?? 'N/A'; ?><br>
                                                    <i class="fa fa-map-marker"></i> <?php echo $po_data_group['address']; ?><br>
                                                    <i class="fa fa-id-card"></i> GST: <?php echo $po_data_group['gst'] ?: 'None Provided'; ?><br>
                                                    <i class="fa fa-credit-card"></i> PAN: <?php echo $po_data_group['pancard'] ?: 'None Provided'; ?><br>
                                                    <i class="fa fa-user"></i> Contact: <?php echo $po_data_group['fullname']; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PO Info Section -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title"><i class="fa fa-info-circle"></i> PO Information</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="info-box bg-light-blue">
                                                            <span class="info-box-icon"><i class="fa fa-hashtag"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">PO Number</span>
                                                                <span class="info-box-number"><?php echo $po_data_group['number']; ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="info-box bg-green">
                                                            <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Order Date</span>
                                                                <span class="info-box-number"><?php echo $purchase_display_date; ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="info-box bg-yellow">
                                                            <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Delivery Date</span>
                                                                <span class="info-box-number"><?php echo $delivery_display_date; ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="info-box bg-red">
                                                            <span class="info-box-icon"><i class="fa fa-money"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Total Amount</span>
                                                                <span class="info-box-number">₹<?php echo indian_number_format($po_data_group['total'], 2); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                 <!-- Project & Sales Linkage Section -->
                                 <div class="row">
                                     <div class="col-md-12">
                                         <div class="panel panel-default">
                                             <div class="panel-heading">
                                                 <h4 class="panel-title"><i class="fa fa-link"></i> Project & Sales Linkage</h4>
                                             </div>
                                             <div class="panel-body">
                                                 <div class="row">
                                                     <?php if ($_has_project_master): ?>
                                                      <div class="col-md-4">
                                                          <strong>Project Code:</strong> <span class="label label-default" style="font-size: 13px;"><?php echo !empty($po_data_group['project_code']) ? htmlspecialchars($po_data_group['project_code']) : 'N/A'; ?></span>
                                                      </div>
                                                      <?php endif; ?>
                                                     <?php if (!empty($po_data_group['so_no']) && !empty($po_data_group['oc_no']) && $po_data_group['so_no'] === $po_data_group['oc_no']): ?>
                                                      <div class="col-md-8">
                                                          <strong>SO:</strong> <span style="font-size: 13px;"><?php echo htmlspecialchars($po_data_group['so_no']); ?></span>
                                                      </div>
                                                      <?php else: ?>
                                                      <div class="col-md-4">
                                                          <strong>Sales Order No:</strong> <span style="font-size: 13px;"><?php echo !empty($po_data_group['so_no']) ? htmlspecialchars($po_data_group['so_no']) : 'N/A'; ?></span>
                                                      </div>
                                                      <div class="col-md-4">
                                                          <strong>OC Number:</strong> <span style="font-size: 13px;"><?php echo !empty($po_data_group['oc_no']) ? htmlspecialchars($po_data_group['oc_no']) : 'N/A'; ?></span>
                                                      </div>
                                                      <?php endif; ?>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                <!-- Items Table -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box box-solid">
                                            <div class="box-header with-border">
                                                <h3 class="box-title"><i class="fa fa-list"></i> Items</h3>
                                            </div>
                                            <div class="box-body table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr class="bg-light-blue">
                                                            <th width="5%">Sr.No.</th>
                                                            <th width="25%">Description</th>
                                                            <th width="10%">HSN Code</th>
                                                            <th width="8%">Qty</th>
                                                            <th width="8%">Unit</th>
                                                            <th width="8%">TAX(%)</th>
                                                          
                                                            <th width="10%" class="gst">SGST</th>
                                                            <th width="10%" class="gst">CGST</th>
                                                            <th width="10%" class="igst">IGST</th>
                                                         
                                                            <th width="8%">Price</th>
                                                                 <th width="8%">Discount(%)</th>
                                                            <th width="8%">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
                                                        $sgst_total_amt = 0;
                                                        $cgst_total_amt = 0;
                                                        $igst_total_amt = 0;
                                                        $amt = 0;
                                                        $subtotal_before_tax = 0;
                                                        $discount_total = 0;
                                                        $total_qty = 0;
                                                        $has_igst = false;

                                                        foreach ($show_po as $key) {
                                                            $quantity = floatval($key->quantity);
                                                            $price = floatval($key->price);
                                                            $discount_pct = floatval($key->discount);
                                                            $line_amount = floatval($key->amount);

                                                            if ($line_amount <= 0) {
                                                                // fallback when amount wasn’t set or is zero
                                                                $line_amount = $quantity * $price * (1 - ($discount_pct / 100));
                                                            }

                                                            $gst_rate = floatval(rtrim($key->gst, '%')); // removes % sign
                                                            $gst_amount = ($line_amount * $gst_rate) / 100;

                                                            $discount_amount = ($quantity * $price) * ($discount_pct / 100);
                                                            $discount_total += $discount_amount;

                                                            if ($key->gst_type != 'I') {
                                                                // SGST & CGST
                                                                $sgst_amount = $gst_amount / 2;
                                                                $cgst_amount = $gst_amount / 2;
                                                                $sgst_total_amt += $sgst_amount;
                                                                $cgst_total_amt += $cgst_amount;
                                                            } else {
                                                                // IGST
                                                                $igst_amount = $gst_amount;
                                                                $igst_total_amt += $igst_amount;
                                                                $has_igst = true;
                                                            }

                                                            $amt += $line_amount;
                                                            $subtotal_before_tax += $line_amount;
                                                            $total_qty += $quantity;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><?php echo $i; ?></td>
                                                                <td>
                                                                    <strong><?php echo $key->product_name. " - " .  $key->item_name; ?></strong><br>
                                                                    <small class="text-muted"><?php echo $key->description; ?></small>
                                                                </td>
                                                                <td class="text-center"><?php echo $key->hsn_code; ?></td>
                                                                <td class="text-right"><?php echo indian_number_format($key->quantity, 2); ?></td>
                                                                <td class="text-center"><?php echo $key->unit; ?></td>
                                                                <td class="text-center"><?php echo $key->gst; ?></td>
                                                              
                                                                <?php if ($key->gst_type != 'I') { ?>
                                                                    <td class="gst text-right">₹<?php echo indian_number_format($sgst_amount, 2); ?></td>
                                                                    <td class="gst text-right">₹<?php echo indian_number_format($cgst_amount, 2); ?></td>
                                                                    <td class="igst text-right" style="display: none;"></td>
                                                                <?php } else { ?>
                                                                    <td class="gst text-right" style="display: none;"></td>
                                                                    <td class="gst text-right" style="display: none;"></td>
                                                                    <td class="igst text-right">₹<?php echo indian_number_format($igst_amount, 2); ?></td>
                                                                <?php } ?>

                                                                <td class="text-right">₹<?php echo indian_number_format($key->price, 2); ?></td>
                                                                                                                                      <td class="text-center"><?php echo indian_number_format($discount_pct, 2); ?></td>
                                                                <td class="text-right"><strong>₹<?php echo indian_number_format($line_amount, 2); ?></strong></td>
                                                            </tr>
                                                        <?php $i++;
                                                        } ?>
                                                    </tbody>
                                                    
                                                        <?php if (!$has_igst) { ?>
                                                            <!-- SGST/CGST Summary -->
                                                            <tr>
                                                                   <td colspan="8" class="text-left"></td>
                                                                <td colspan="2" class="text-right"><strong>Total Qty:</strong></td>
                                                             
                                                                <td colspan="1" class="text-right"><strong><?php echo indian_number_format($total_qty, 2); ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                   <td colspan="8" class="text-left"></td>
                                                                <td colspan="2" class="text-right"><strong>Subtotal (before tax):</strong></td>
                                                             
                                                                <td colspan="1" class="text-right"><strong>₹<?php echo indian_number_format($subtotal_before_tax, 2); ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                    <td colspan="8" class="text-left"></td>
                                                                <td colspan="2" class="text-right"><strong>Total Discount:</strong></td>
                                                            
                                                                <td colspan="1" class="text-right"><strong>₹<?php echo indian_number_format($discount_total, 2); ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                     <td colspan="8" class="text-left"></td>
                                                                <td colspan="2" class="text-right"><strong>CGST Amount:</strong></td>
                                                           
                                                                <td colspan="1" class="text-right"><strong>₹<?php echo indian_number_format($cgst_total_amt, 2); ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                   <td colspan="8" class="text-left"></td>
                                                                <td colspan="2" class="text-right"><strong>SGST Amount:</strong></td>
                                                             
                                                                <td colspan="1" class="text-right"><strong>₹<?php echo indian_number_format($sgst_total_amt, 2); ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                    <td colspan="8" class="text-left"></td>
                                                                <td colspan="2" class="text-right"><strong>Total GST Amount:</strong></td>
                                                            
                                                                <td colspan="1" class="text-right"><strong>₹<?php echo indian_number_format($sgst_total_amt + $cgst_total_amt, 2); ?></strong></td>
                                                            </tr>
                                                        <?php } else { ?>
                                                            <!-- IGST Summary -->
                                                            <tr>
                                                                <td colspan="9" class="text-right"><strong>Total Qty:</strong></td>
                                                                <td colspan="2" class="text-right"><strong><?php echo indian_number_format($total_qty, 2); ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="9" class="text-right"><strong>Subtotal (before tax):</strong></td>
                                                                <td colspan="2" class="text-right"><strong>₹<?php echo indian_number_format($subtotal_before_tax, 2); ?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="9" class="text-right"><strong>IGST Amount:</strong></td>
                                                                <td colspan="2" class="text-right"><strong>₹<?php echo indian_number_format($igst_total_amt, 2); ?></strong></td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr class="bg-gray">
                                                            <td colspan="<?php echo $has_igst ? '9' : '9'; ?>" class="text-right">
                                                                <h4><strong>Grand Total (INR):</strong></h4>
                                                            </td>
                                                            <td colspan="3" class="text-right">
                                                                <h4><strong>₹<?php echo indian_number_format($po_data_group['total'], 2); ?></strong></h4>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="11" class="text-right">
                                                                <strong>
                                                                    <?php
                                                                    require_once(APPPATH . '/third_party/amount_convert.php');
                                                                    ?>
                                                                    Grand Total in Words: <?php echo number_to_word($po_data_group['total']); ?> Only.
                                                                </strong>
                                                            </td>
                                                        </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment History -->
                                <?php if (!empty($payment_history)): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-success">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title"><i class="fa fa-history"></i> Payment History</h3>
                                                </div>
                                                <div class="box-body table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr class="bg-green">
                                                                <th>#</th>
                                                                <th>Date</th>
                                                                <th>Payment Type</th>
                                                                <th>Note</th>
                                                                <th>Paid Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $j = 1; ?>
                                                            <?php foreach ($payment_history as $key): ?>
                                                                <tr>
                                                                    <td class="text-center"><?php echo $j; ?></td>
                                                                    <td><?php echo date('d-m-Y', strtotime($key->purchase_pay_date)); ?></td>
                                                                    <td><span class="label label-info"><?php echo $key->payment_type; ?></span></td>
                                                                    <td><?php echo $key->purchase_pay_remark; ?></td>
                                                                    <td class="text-right"><strong class="text-success">₹<?php echo indian_number_format($key->purchase_pay_amount, 2); ?></strong></td>
                                                                </tr>
                                                                <?php $j++; ?>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Amendment History -->
                                <?php if (!empty($amendments) || !empty($revisions)): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-warning">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title"><i class="fa fa-edit"></i> Amendment History</h3>
                                                </div>
                                                <div class="box-body">
                                                    <?php if ($is_revised_po): ?>
                                                        <div class="alert alert-info">
                                                            <i class="fa fa-info-circle"></i>
                                                            <strong>This is a Revised Purchase Order</strong><br>
                                                            <strong>Original PO:</strong>
                                                            <a href="<?php echo base_url('SupplierController/show_po/' . $original_po_number); ?>" class="alert-link">
                                                                <?php echo $original_po_number; ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($revisions)): ?>
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h4 class="panel-title"><i class="fa fa-history"></i> Revision Timeline</h4>
                                                            </div>
                                                            <div class="panel-body">
                                                                <div class="timeline">
                                                                    <?php foreach ($revisions as $revision): ?>
                                                                        <div class="timeline-item">
                                                                            <div class="timeline-point timeline-point-<?php
                                                                                                                        echo $revision['status'] == 'approved' ? 'success' : ($revision['status'] == 'pending_approval' ? 'warning' : 'danger'); ?>">
                                                                                <i class="fa fa-file-text-o"></i>
                                                                            </div>
                                                                            <div class="timeline-event">
                                                                                <div class="panel panel-default">
                                                                                    <div class="panel-heading">
                                                                                        <h4 class="panel-title">
                                                                                            Revision R<?php echo $revision['revision_number']; ?> -
                                                                                            <?php echo ucwords(str_replace('_', ' ', $revision['amendment_type'])); ?>
                                                                                            <span class="pull-right label label-<?php
                                                                                                                                switch ($revision['status']) {
                                                                                                                                    case 'approved':
                                                                                                                                        echo 'success';
                                                                                                                                        break;
                                                                                                                                    case 'pending_approval':
                                                                                                                                        echo 'warning';
                                                                                                                                        break;
                                                                                                                                    case 'rejected':
                                                                                                                                        echo 'danger';
                                                                                                                                        break;
                                                                                                                                    default:
                                                                                                                                        echo 'default';
                                                                                                                                }
                                                                                                                                ?>">
                                                                                                <?php echo ucfirst($revision['status']); ?>
                                                                                            </span>
                                                                                        </h4>
                                                                                    </div>
                                                                                    <div class="panel-body">
                                                                                        <div class="row">
                                                                                            <div class="col-md-6">
                                                                                                <p><strong>Amendment:</strong> <?php echo $revision['amendment_no']; ?></p>
                                                                                                <p><strong>Description:</strong> <?php echo $revision['description']; ?></p>
                                                                                                <p><strong>Reason:</strong> <?php echo $revision['reason']; ?></p>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <?php if ($revision['amendment_value'] > 0): ?>
                                                                                                    <p><strong>Value Change:</strong> ₹<?php echo indian_number_format($revision['amendment_value'], 2); ?></p>
                                                                                                <?php endif; ?>
                                                                                                <?php if ($revision['new_revised_po_number']): ?>
                                                                                                    <p><strong>Revised PO:</strong>
                                                                                                        <a href="<?php echo base_url('SupplierController/show_po/' . $revision['new_revised_po_number']); ?>">
                                                                                                            <?php echo $revision['new_revised_po_number']; ?>
                                                                                                        </a>
                                                                                                    </p>
                                                                                                <?php endif; ?>
                                                                                                <p><strong>Date:</strong> <?php echo date('d-M-Y H:i', strtotime($revision['initiated_date'])); ?></p>
                                                                                                <?php if ($revision['attachment']): ?>
                                                                                                    <p><strong>Attachment:</strong>
                                                                                                        <a href="<?php echo base_url('uploads/amendments/' . $revision['attachment']); ?>" target="_blank" class="btn btn-xs btn-default">
                                                                                                            <i class="fa fa-download"></i> Download
                                                                                                        </a>
                                                                                                    </p>
                                                                                                <?php endif; ?>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr class="bg-yellow">
                                                                    <th>Amendment No</th>
                                                                    <th>Type</th>
                                                                    <th>Description</th>
                                                                    <th>Value Change</th>
                                                                    <th>Status</th>
                                                                    <th>Date</th>
                                                                    <th>Revised PO</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($amendments as $amendment): ?>
                                                                    <tr>
                                                                        <td><?php echo $amendment['amendment_no']; ?></td>
                                                                        <td><?php echo ucwords(str_replace('_', ' ', $amendment['amendment_type'])); ?></td>
                                                                        <td><?php echo $amendment['description']; ?></td>
                                                                        <td class="text-right">₹<?php echo indian_number_format($amendment['amendment_value'], 2); ?></td>
                                                                        <td>
                                                                            <span class="label label-<?php
                                                                                                        switch ($amendment['status']) {
                                                                                                            case 'approved':
                                                                                                                echo 'success';
                                                                                                                break;
                                                                                                            case 'pending_approval':
                                                                                                                echo 'warning';
                                                                                                                break;
                                                                                                            case 'rejected':
                                                                                                                echo 'danger';
                                                                                                                break;
                                                                                                            case 'vendor_acknowledged':
                                                                                                                echo 'info';
                                                                                                                break;
                                                                                                            case 'revised_po_issued':
                                                                                                                echo 'primary';
                                                                                                                break;
                                                                                                            default:
                                                                                                                echo 'default';
                                                                                                        }
                                                                                                        ?>">
                                                                                <?php echo ucfirst(str_replace('_', ' ', $amendment['status'])); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td><?php echo date('d-M-Y', strtotime($amendment['initiated_date'])); ?></td>
                                                                        <td>
                                                                            <?php if ($amendment['new_revised_po_number']): ?>
                                                                                <a href="<?php echo base_url('SupplierController/show_po/' . $amendment['new_revised_po_number']); ?>" class="label label-primary">
                                                                                    <?php echo $amendment['new_revised_po_number']; ?>
                                                                                </a>
                                                                            <?php else: ?>
                                                                                <span class="label label-default">Not Created</span>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                        <td>
                                                                            <a href="<?php echo base_url('PoamendmentController/view/' . $amendment['amendment_id']); ?>" class="btn btn-xs btn-info" title="View Details">
                                                                                <i class="fa fa-eye"></i>
                                                                            </a>
                                                                            <?php if ($amendment['status'] == 'approved' && !$amendment['is_revision_created']): ?>
                                                                                <a href="<?php echo base_url('PoamendmentController/create_revision/' . $amendment['amendment_id']); ?>" class="btn btn-xs btn-success" title="Create Revision">
                                                                                    <i class="fa fa-plus"></i> Revise
                                                                                </a>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="box-footer">
                                                    <div class="pull-right">
                                                        <?php if ($po_data_group['status'] == 4): ?>
                                                            <a href="<?php echo base_url('PoamendmentController/create?po_id=' . $po_data_group['id']); ?>" class="btn btn-warning">
                                                                <i class="fa fa-edit"></i> Create Amendment
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if (!empty($amendments)): ?>
                                                            <a href="<?php echo base_url('PoamendmentController/revision_history/' . $po_data_group['number']); ?>" class="btn btn-info">
                                                                <i class="fa fa-history"></i> View All Amendments
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Terms and Conditions -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box box-default">
                                            <div class="box-header with-border">
                                                <h3 class="box-title"><i class="fa fa-file-text"></i> Terms & Conditions</h3>
                                            </div>
                                            <div class="box-body">
                                                <?php if (!empty($po_data_group['po_terms_and_conditions'])) { ?>
                                                <div class="col-sm-12" style="margin-top:10px;">
                                                    <label class="control-label"><b>Terms &amp; Conditions:</b></label>
                                                    <div><?php echo $po_data_group['po_terms_and_conditions']; ?></div>
                                                </div>
                                                <?php } ?>

                                                <?php if (!empty($po_data_group['po_payment_terms'])) { ?>
                                                <div class="col-sm-12" style="margin-top:10px;">
                                                    <label class="control-label"><b>Payment Terms:</b></label>
                                                    <div><?php echo $po_data_group['po_payment_terms']; ?></div>
                                                </div>
                                                <?php } ?>

                                                <?php if (!empty($po_data_group['po_process_schedule'])) { ?>
                                                <div class="col-sm-12" style="margin-top:10px;">
                                                    <label class="control-label"><b>Process Schedule:</b></label>
                                                    <div><?php echo $po_data_group['po_process_schedule']; ?></div>
                                                </div>
                                                <?php } ?>

                                                <?php if (!empty($po_data_group['po_taxes'])) { ?>
                                                <div class="col-sm-12" style="margin-top:10px;">
                                                    <label class="control-label"><b>Taxes:</b></label>
                                                    <div><?php echo $po_data_group['po_taxes']; ?></div>
                                                </div>
                                                <?php } ?>

                                                <?php if (!empty($po_data_group['po_exclusions'])) { ?>
                                                <div class="col-sm-12" style="margin-top:10px;">
                                                    <label class="control-label"><b>Exclusions:</b></label>
                                                    <div><?php echo $po_data_group['po_exclusions']; ?></div>
                                                </div>
                                                <?php } ?>

                                                <?php if (!empty($po_data_group['po_note'])) { ?>
                                                <div class="col-sm-12" style="margin-top:10px;">
                                                    <label class="control-label"><b>Notes:</b></label>
                                                    <div><?php echo $po_data_group['po_note']; ?></div>
                                                </div>
                                                <?php } ?>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="text-center">
                                                            <h4><strong>Receiver's Signatory</strong></h4>
                                                            <hr style="border-top: 1px solid #000; width: 200px;">
                                                            <p>Name, Designation & Signature</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-center">
                                                            <h4><strong>Authorized Signatory</strong></h4>
                                                            <hr style="border-top: 1px solid #000; width: 200px;">
                                                            <p>For <?php echo $settings['company_name']; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center text-muted" style="margin-top: 20px;">
                                                    <i class="fa fa-info-circle"></i> This is Computer Generated Document
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->

    <!-- Send Email Modal -->
    <div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog" aria-labelledby="sendEmailModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="sendEmailModalLabel"><i class="fa fa-envelope"></i> Send Purchase Order</h4>
                </div>
                <form id="sendEmailForm" method="post" action="<?php echo base_url(); ?>SupplierController/send_po_email" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>To Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="to_email" required placeholder="recipient@example.com">
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" name="subject" placeholder="Purchase Order Subject">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-control" name="message" rows="4" placeholder="Add your message here..."></textarea>
                        </div>
                        <input type="hidden" name="number" id="modalPoNumber" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle IGST display
            var hasIgst = <?php echo $has_igst ? 'true' : 'false'; ?>;
            if (hasIgst) {
                $('.gst').hide();
                $('.igst').show();
            } else {
                $('.gst').show();
                $('.igst').hide();
            }

            // Send email modal
            $('.send-email-po').click(function(e) {
                e.preventDefault();
                var poNumber = $(this).data('po-number');
                $('#modalPoNumber').val(poNumber);
                $('#sendEmailModal').modal('show');
            });

            // Print functionality
            $('.print-po').click(function() {
                window.print();
            });
        });
    </script>

    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 20px;
        }

        .timeline-point {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .timeline-point-success {
            background-color: #00a65a;
            color: white;
        }

        .timeline-point-warning {
            background-color: #f39c12;
            color: white;
        }

        .timeline-point-danger {
            background-color: #dd4b39;
            color: white;
        }

        .timeline-event {
            flex-grow: 1;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #3c8dbc;
        }

        .info-box {
            min-height: 90px;
            margin-bottom: 15px;
        }

        .info-box-icon {
            border-radius: 4px 0 0 4px;
        }

        .table>thead>tr>th {
            background-color: #3c8dbc;
            color: white;
        }

        .well {
            background-color: #f5f5f5;
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            padding: 20px;
        }

        .send-icon {
            width: 20px;
            height: 20px;
            margin-right: 5px;
            vertical-align: middle;
        }

        .btn-group {
            margin-bottom: 10px;
        }
    </style>
</body>