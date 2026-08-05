<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
    exit();
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . '/third_party/amount_convert.php');

// Check if data exists before displaying
if (empty($grn_data_group)) {
    echo '<div class="alert alert-danger">GRN data not found. Please go back to GRN list.</div>';
    echo '<a href="' . base_url() . 'GrnController/grn_index" class="btn btn-primary">Back to GRN List</a>';
    exit();
}

// Check if settings exist
if (empty($settings)) {
    $settings = [
        'company_logo' => '',
        'company_name' => 'Company Name',
        'company_gst' => '',
        'company_pan' => '',
        'mobile' => '',
        'email' => '',
        'address' => ''
    ];
}
?>
<style>
    /* Prevent top content/breadcrumbs from hiding under the fixed header */
    .content-wrapper {
        padding-top: 20px !important;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'GrnController/grn_index' ?>">GRN</a></li>
                    <li class="active">GRN Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <!-- LINE 35 FIX: Add null check -->
                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                                    <h2> GRN: <?php echo isset($grn_data_group['grn_number']) ? $grn_data_group['grn_number'] : 'N/A'; ?></h2>
                                </label>

                                <div class="pull-right">
                                    <div class="col-md-5">
                                        <!-- LINE 43: Add null check -->
                                        <a class="btn btn-primary" id="exportpdf" href="<?php echo base_url(); ?>Pdf/grn_pdf/<?php echo isset($grn_data_group['grn_number']) ? $grn_data_group['grn_number'] : ''; ?>">Export As PDF</a>
                                    </div>
                                    <div class="col-md-1 "></div>
                                    <div class="col-md-4">
                                        <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-danger pull-right"><i class="fa fa-close"></i> Close</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="shadows1">
                                    <div class="row">
                                        <section class="contemporary-template__header">
                                            <div class="col-md-8">
                                                <div class="contemporary-template__header__logo">
                                                    <?php if (!empty($settings['company_logo'])): ?>
                                                        <img class="contemporary-template__business-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" width="70%" height="35%">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="contemporary-template__header__info">
                                                    <div class="wv-heading--title">
                                                        <h1>GRN</h1>
                                                    </div>
                                                    <span class="wv-text--strong"><b><?php echo isset($settings['company_name']) ? $settings['company_name'] : ''; ?></b></span><br>
                                                    <span class="wv-text--strong"><b>GST Number:</b><?php echo isset($settings['company_gst']) ? $settings['company_gst'] : ''; ?></span><br>
                                                    <span class="wv-text--strong"><b>PAN Number:</b><?php echo isset($settings['company_pan']) ? $settings['company_pan'] : ''; ?></span><br>
                                                    <span class="wv-text--strong"><b>Mobile Number:</b><?php echo isset($settings['mobile']) ? $settings['mobile'] : ''; ?></span><br>
                                                    <span class="wv-text--strong"><b>Email ID:</b><?php echo isset($settings['email']) ? $settings['email'] : ''; ?></span><br>
                                                    <span class="wv-text--strong"><b>Address:</b></span>
                                                    <div class="contemporary-template__header__info__address">
                                                        <?php
                                                        if (!empty($settings['address'])) {
                                                            $pieces = explode(',', $settings['address']);
                                                            foreach ($pieces as $part) {
                                                                echo trim($part) . "<br>";
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group row">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>BILL TO:</b></label>
                                            </div>

                                            <!-- LINE 101 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-8">
                                                    <?php echo isset($grn_data_group['company_name']) ? $grn_data_group['company_name'] : 'N/A'; ?>
                                                </div>
                                            </div>

                                            <!-- LINE 107 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-8">
                                                    <?php echo isset($grn_data_group['address']) ? $grn_data_group['address'] : 'N/A'; ?>
                                                </div>
                                            </div>

                                            <!-- LINE 113 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-8">
                                                    <b>GST Number:</b>
                                                    <?php if (!empty($grn_data_group['gst'])) { ?>
                                                        <?php echo $grn_data_group['gst']; ?>
                                                    <?php } else { ?>
                                                        None Provided
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <!-- LINE 126 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-8">
                                                    <b>PAN Number:</b>
                                                    <?php if (!empty($grn_data_group['pancard'])) { ?>
                                                        <?php echo $grn_data_group['pancard']; ?>
                                                    <?php } else { ?>
                                                        None Provided
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <!-- LINE 139 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-8">
                                                    <b>Customer Name: <?php echo isset($grn_data_group['fullname']) ? $grn_data_group['fullname'] : 'N/A'; ?></b><br>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-4">
                                            <!-- LINE 156 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <b>GRN Number:</b><?php echo isset($grn_data_group['grn_number']) ? $grn_data_group['grn_number'] : 'N/A'; ?>
                                                </div>
                                            </div>

                                            <!-- LINE 162 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <b>GRN Date:</b><?php echo isset($grn_data_group['date']) ? $grn_data_group['date'] : 'N/A'; ?>
                                                </div>
                                            </div>

                                            <!-- LINE 168 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <b>Grand Total (INR): </b>
                                                    <?php
                                                    $total = isset($grn_data_group['total']) ? $grn_data_group['total'] : 0;
                                                    echo number_format($total, 2);
                                                    ?>
                                                </div>
                                            </div>

                                            <!-- LINE 174 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <b>Invoice No: </b><?php echo isset($grn_data_group['invoice_number']) ? $grn_data_group['invoice_number'] : 'N/A'; ?>
                                                </div>
                                            </div>

                                            <!-- LINE 180 FIX: Add null check -->
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <b>Invoice Date: </b><?php echo isset($grn_data_group['invoice_date']) ? $grn_data_group['invoice_date'] : 'N/A'; ?>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <b>PO Number: </b><?php echo isset($grn_data_group['po_number_fk']) ? $grn_data_group['po_number_fk'] : 'N/A'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dynamic_field">
                                            <tr>
                                                <th>Sr.No.</th>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>Qty</th>
                                                <th>Unit</th>
                                                <th>HSN Code</th>
                                                <th>GST</th>
                                                <th>SGST</th>
                                                <th>CGST</th>
                                                <th>Received</th>
                                                <th>Pending</th>
                                                <th>Price</th>
                                            </tr>
                                            <?php
                                            $i = 1;
                                            $total_qty = 0;
                                            $total_sgst = 0;
                                            $total_cgst = 0;
                                            $total_igst = 0;
                                            if (!empty($show_grn)) {
                                                foreach ($show_grn as $key) {
                                            $total_qty += isset($key->quantity) ? (float)$key->quantity : 0;
                                            $total_sgst += isset($key->sgst) ? (float)$key->sgst : 0;
                                            $total_cgst += isset($key->cgst) ? (float)$key->cgst : 0;
                                            $total_igst += isset($key->igst) ? (float)$key->igst : 0;
                                            ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo isset($key->product_name) ? $key->product_name . " - " . $key->item_name : ''; ?></td>
                                                        <td><?php echo isset($key->description) ? $key->description : ''; ?></td>
                                                        <td><?php echo isset($key->quantity) ? $key->quantity : 0; ?></td>
                                                        <td><?php echo isset($key->unit) ? $key->unit : ''; ?></td>
                                                        <td><?php echo isset($key->hsn_code) ? $key->hsn_code : ''; ?></td>
                                                        <td><?php echo isset($key->gst) ? $key->gst : ''; ?></td>
                                                        <td class="gst"><?php echo isset($key->sgst) ? number_format($key->sgst, 2) : '0.00'; ?></td>
                                                        <td class="gst"><?php echo isset($key->cgst) ? number_format($key->cgst, 2) : '0.00'; ?></td>
                                                        <td><?php echo isset($key->received_quantity) ? $key->received_quantity : 0; ?></td>
                                                        <td><?php echo isset($key->pending_quantity) ? $key->pending_quantity : 0; ?></td>
                                                        <td><?php echo isset($key->price) ? number_format($key->price, 2) : '0.00'; ?></td>
                                                    </tr>
                                            <?php
                                                    $i++;
                                                }
                                                $grand_total = isset($grn_data_group['total']) ? (float)$grn_data_group['total'] : 0;
                                                $total_tax = $total_sgst + $total_cgst + $total_igst;
                                                $total_before_tax = $grand_total - $total_tax;
                                            ?>
                                                    <tr class="">
                                                        <td colspan="3" class="text-right"><b>Total Qty: <?php echo number_format($total_qty, 2); ?></b></td>
                                                        <td colspan="9" class="text-right">
                                                            Total Before Tax ₹ <?php echo indian_number_format($total_before_tax, 2); ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="12" class="text-right">
                                                            <span id="tax_amount" name="tax_amount"><b>Tax Amount:</b> ₹<?php echo indian_number_format($total_tax, 2); ?></span>
                                                        </td>
                                                    </tr>
                                                    <tr class="gst">
                                                        <td colspan="12" class="text-right">
                                                            <span id="sgst_amount" name="sgst_amount"><b>SGST Amount: </b>₹<?php echo indian_number_format($total_sgst, 2); ?></span><br>
                                                        </td>
                                                    </tr>
                                                    <tr class="gst">
                                                        <td colspan="12" class="text-right">
                                                            <span id="cgst_amount" name="cgst_amount"><b>CGST Amount:</b> ₹<?php echo indian_number_format($total_cgst, 2); ?></span><br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="12" class="text-right">
                                                            <span class="igst igst_edit_hide_show" id="igst_amount" name="igst_amount"><b>IGST Amount:</b> ₹<?php echo indian_number_format($total_igst, 2); ?></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="12" class="text-right"><b><span>Grand Total: <?php echo indian_number_format($grand_total, 2); ?></span></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="12" class="text-right" style="font-weight:bold;">
                                                            Grand Total in Words: <?php echo number_to_word($grand_total); ?> Only
                                                        </td>
                                                    </tr>
                                            <?php
                                            } else {
                                                echo '<tr><td colspan="12" class="text-center">No items found</td></tr>';
                                            }
                                            ?>
                                        </table>

                                        <label class="control-label pull-left"><b>Notes</b></label><br>
                                        <div class="col-sm-12">
                                            <!-- LINE 266 FIX: Add null check -->
                                            <textarea style="overflow: auto; border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo isset($grn_data_group['note']) ? $grn_data_group['note'] : ''; ?></textarea><br>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Sign :</b></label>
                                            <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Sign :</b></label>
                                        </div>

                                        <label class="control-label pull-left"><b>*(It is electronic generated grn signatures may not appear).</b></label><br>
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
