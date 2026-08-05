<?php
    $session_data_head1 = $this->session->userdata('session_data_head');
    if (isset($session_data_head1)) {
    } else {
        header($this->config->item('header'));
    }
    defined('BASEPATH') or exit('No direct script access allowed');
    require_once(APPPATH . '/third_party/amount_convert.php');


    // echo "hiiiiiiiiiii";
    //         die();
    ?>

<style>
    .quotation-page {
        padding-bottom: 24px;
    }

    .quotation-title {
        word-break: break-word;
        margin: 0 0 10px;
    }

    .quotation-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: flex-start;
    }

    .quotation-actions form,
    .quotation-actions .dropdown {
        display: inline-block;
        margin: 0;
    }

    .quotation-actions .btn {
        white-space: normal;
    }

    .quotation-actions-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .quotation-close-col {
        display: flex;
        justify-content: flex-end;
    }

    .quotation-card {
        background: #fff;
        border: 1px solid #dfe4ea;
        border-radius: 10px;
        padding: 18px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }

    .quotation-company-block,
    .quotation-meta-block {
        word-break: break-word;
    }

    .quotation-logo-left {
        text-align: left;
    }

    .quotation-logo-left img {
        display: inline-block;
        margin-top: 26px;
        max-width: 100%;
        height: auto;
    }

    .quotation-grid-table {
        border-collapse: collapse;
        border: 2px solid #2f2f2f;
        background-color: #fff;
        min-width: 980px;
        margin-bottom: 0;
    }

    .quotation-grid-table > tbody > tr > th,
    .quotation-grid-table > tbody > tr > td,
    .quotation-grid-table > thead > tr > th,
    .quotation-grid-table > thead > tr > td {
        border: 1.5px solid #2f2f2f !important;
        color: #111;
        vertical-align: middle;
    }

    .quotation-grid-table > tbody > tr > th,
    .quotation-grid-table > thead > tr > th {
        background-color: #f3f3f3;
    }

    .empty-row td {
        height: 40px;
        /* Set a height for empty rows */
    }

    /* Hide top and bottom borders for item rows but keep vertical borders */
    .item-row td {
        border-top: none;
        /* Remove top border */
        border-bottom: none;
        /* Remove bottom border */
        border-left: 1px solid black;
        /* Keep vertical left border */
        border-right: 1px solid black;
        /* Keep vertical right border */
    }

    .quotation-section {
        margin-top: 12px;
    }

    .quotation-section .control-label,
    .quotation-section div {
        word-break: break-word;
    }

    .quotation-footer-note {
        font-size: 12px;
        text-align: center;
        word-break: break-word;
    }

    .quotation-footer-subnote {
        font-size: 10px;
        text-align: center;
    }

    @media (max-width: 991px) {
        .quotation-actions-row {
            flex-direction: column;
        }

        .quotation-close-col {
            width: 100%;
            justify-content: flex-start;
        }

        .quotation-logo-left,
        .quotation-meta-block {
            text-align: left;
        }

        .quotation-card {
            padding: 14px;
        }
    }

    @media (max-width: 767px) {
        .content-header .breadcrumb {
            float: none;
            position: static;
            margin-top: 10px;
        }

        .quotation-page .control-label {
            text-align: left;
        }

        .quotation-title {
            font-size: 24px;
            line-height: 1.3;
        }

        .quotation-grid-table {
            min-width: 760px;
        }

        .quotation-actions {
            width: 100%;
        }

        .quotation-actions form,
        .quotation-actions .dropdown,
        .quotation-actions .btn {
            width: 100%;
        }

        .quotation-actions .dropdown-menu {
            width: 100%;
        }

        .quotation-card {
            padding: 12px;
            border-radius: 8px;
        }
    }

    @media print {
        .quotation-actions-row {
            display: none !important;
        }
    }
</style>



<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <!--                <h1>
                                    Quotation
                                </h1>-->
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'EstimateController/index/' ?>">Quotation</a></li>
                    <li class="active">Quotation Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content quotation-page">
                <div class="row">
                    <div class="col-xs-12">
                        <!--<div class="box">-->
                        <!--<div class="box-header">-->
                        <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                            <h2 class="quotation-title">Quotation:<?php echo isset($estimates_data_group['number_fk']) ? $estimates_data_group['number_fk'] : ''; ?></h2>
                        </label>
                        <!--</div>-->
                        <!-- /.box-header -->
                        <!--<div class="box-body">-->
                        
                        <div class="row quotation-actions-row" style="padding:2%; margin-left:0; margin-right:0;">
                            <div class="col-xs-12 col-md-9 quotation-actions" style="padding-left:0;">
                                <a href="<?php echo base_url(); ?>EstimateController/edit_estimate_details/<?php echo $estimates_data_group['id']; ?>" class="btn btn-primary" role="button" style="margin-right:5px;">Edit</a>
                                <form method="POST" action="<?php echo base_url(); ?>EstimateController/sgst_to_igst" style="display:inline-block; margin-right:5px;">
                                    <input type="hidden" name="number_fk" value="<?php echo $estimates_data_group['number_fk']; ?>">
                                    <?php 
                                    $gst_type = '';
                                    foreach ($show_quotation as $key) { 
                                        $gst_type = $key->gst_type; 
                                        break; 
                                    } 
                                    ?>
                                    <?php if ($gst_type != 'I') { ?>
                                        <button class="btn btn-primary" type="submit">SGST → IGST</button>
                                    <?php } else { ?>
                                        <button class="btn btn-primary" type="submit">IGST → SGST</button>
                                    <?php } ?>
                                </form>
                                <form method="POST" action="<?php echo base_url(); ?>EstimateController/convert_to_sales_order/<?php echo $estimates_data_group['id']; ?>" style="display:inline-block; margin-right:5px;">
                                    <input type="hidden" name="number" value="<?php echo $estimates_data_group['id']; ?>">
                                    <?php
                                    if (date('m') <= 3) {
                                        $financial_year = (date('y') - 1) . '-' . date('y');
                                    } else {
                                        $financial_year = date('y') . '-' . (date('y') + 1);
                                    }
                                    ?>
                                    <input type="hidden" name="salesorder_number" value="SO/<?php printf("%04d", $salesorder_id); ?>/<?php echo $financial_year; ?>">
                                    <button class="btn btn-primary" type="submit">Convert to Sales Order</button>
                                </form>
                                <form method="POST" action="<?php echo base_url(); ?>EstimateController/convert_to_invoice/<?php echo $estimates_data_group['id']; ?>" style="display:inline-block; margin-right:5px;">
                                    <input type="hidden" name="number" value="<?php echo $estimates_data_group['id']; ?>">
                                    <?php
                                    $next_invoice_number = !empty($next_invoice_name)
                                        ? $next_invoice_name
                                        : 'INV/' . sprintf("%04d", ((int) $invoice_id) + 1) . '/' . ((date('m') <= 3) ? ((date('y') - 1) . '-' . date('y')) : (date('y') . '-' . (date('y') + 1)));
                                    ?>
                                    <input type="hidden" name="invoice_number" value="<?php echo $next_invoice_number; ?>" class="form-control input-sm" style="display:inline-block; width:220px; margin-right:5px; text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                    <button class="btn btn-primary" type="submit">Convert to Invoice</button>
                                </form>
                                <form method="POST" action="<?php echo base_url(); ?>EstimateController/duplicate_quote" style="display:inline-block; margin-right:5px;">
                                    <input type="hidden" name="id" value="<?php echo $estimates_data_group['id']; ?>">
                                    <button class="btn btn-primary" type="submit">Duplicate</button>
                                </form>
                                <a href="<?php echo base_url(); ?>Pdf/print_igst_quote?quote_number_id=<?php echo $estimates_data_group['id']; ?>" target="_blank" class="btn btn-primary" style="margin-right:5px;" onclick="setTimeout(function(){window.print();}, 500); return false;">Print</a>
                                <div class="dropdown" style="display:inline-block;">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        More <b class="caret"></b>
                                    </button>
                                    <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo isset($estimates_data_group['number_fk']) ? $estimates_data_group['number_fk'] : ''; ?>">
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <li class="hide"><a class="dropdown-item" href="<?php echo base_url(); ?>/LoginController/get_settings">Edit Business Information</a></li>
                                        <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/print_igst_quote?quote_number_id=<?php echo $estimates_data_group['id']; ?>">Export As PDF</a></li>
                                        <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/print_igst_quote?quote_number_id=<?php echo $estimates_data_group['id']; ?>&sez=sez">Export As PDF SEZ</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3 text-right quotation-close-col" style="padding-right:0;">
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-close"></i> Close</a>
                            </div>
                        </div>
                        <div class="shadows1 quotation-card">
                            <div class="row">
                                <section class="contemporary-template__header">
                                    <div class="col-xs-12 col-md-6 quotation-company-block">
                                        <div class="contemporary-template__header__logo quotation-logo-left">
                                            <img class="contemporary-template__business-logo" src="<?php echo base_url() . $settings['company_logo'] ?>" width="70%" height="35%">

                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="contemporary-template__header__info quotation-meta-block">

                                            <div class="wv-heading--title">
                                                <h1>Quotation</h1>
                                            </div>
                                            <div class="wv-heading--subtitle"> <?php echo $settings['quotation_subheading']; ?></div>
                                            <span class="wv-text--strong"><b> <?php echo $settings['company_name']; ?></b></span><br>
                                            <span class="wv-text--strong"><b>GST No :</b> <?php echo $settings['company_gst']; ?></span><br>
                                            <span class="wv-text--strong"><b>PAN No :</b> <?php echo $settings['company_pan']; ?></span><br>
                                            <span class="wv-text--strong"><b>Mobile Number :</b> <?php echo $settings['mobile']; ?></span><br>
                                            <span class="wv-text--strong"><b>Email ID :</b> <?php echo $settings['email']; ?></span><br>
                                            <span class="wv-text--strong"><b>Address :</b> <?php echo $settings['address']; ?></span>


                                        </div>
                                    </div>
                                </section>

                            </div>
                            <hr>
                            <div class="row">

                                <div class="col-xs-12 col-md-6">

                                    <!--                                    <div class="form-group row ">
                                        <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>Buyer:</b></label>
                                    </div>-->
                                    <div class="contemporary-template__header__info">

                                        <div class="wv-heading--subtitle"><b>Buyer :</b></div>
                                        <br>
                                        <span class="wv-text--strong"><b>Company Name :</b> <?php echo $estimates_data_group['company_name']; ?></span><br>
                                        <span class="wv-text--strong"><b>Address :</b>
                                            <?php if ($estimates_data_group['address']) { ?>
                                                <?php echo $estimates_data_group['address']; ?>
                                            <?php } else { ?>
                                            <?php
                                                echo "None Provided";
                                            }
                                            ?>
                                        </span><br>
                                        <span class="wv-text--strong"><b>GST Number :</b> <?php if ($estimates_data_group['gst']) { ?>
                                                <?php echo $estimates_data_group['gst']; ?>
                                            <?php } else { ?>
                                            <?php
                                                                                                echo "None Provided";
                                                                                            }
                                            ?></span><br>
                                        <span class="wv-text--strong"><b>PAN Number :</b> <?php if ($estimates_data_group['pancard']) { ?>
                                                <?php echo $estimates_data_group['pancard']; ?>
                                            <?php } else { ?>
                                            <?php
                                                                                                echo "None Provided";
                                                                                            }
                                            ?></span><br>
                                        <span class="wv-text--strong"><b>State Code :</b> <?php if ($estimates_data_group['state_code']) { ?>
                                                <?php echo $estimates_data_group['state_code']; ?>
                                            <?php } else { ?>
                                            <?php
                                                                                                echo "None Provided";
                                                                                            }
                                            ?></span><br>
                                        <span class="wv-text--strong"><b>Customer Name :</b> <?php echo $estimates_data_group['fullname']; ?></span><br>
                                        <span class="wv-text--strong"><b>Customer Code :</b> <?php echo isset($estimates_data_group['c_code']) ? $estimates_data_group['c_code'] : ''; ?></span>
                                        


                                    </div>
                                </div>

                                <div class="col-xs-12 col-md-6">

                                    <div class="contemporary-template__header__info">

                                        <div class="wv-heading--subtitle"><b>Quotation Details :</b></div>
                                        <br>
                                        <span class="wv-text--strong"><b>Quotation Number :</b> <?php echo isset($estimates_data_group['number_fk']) ? $estimates_data_group['number_fk'] : ''; ?></span><br>
                                        <span class="wv-text--strong"><b>Quotation Date :</b> <?php echo date('d-m-Y', strtotime($estimates_data_group['date'])); ?></span><br>
                                        <span class="wv-text--strong"><b>Expires on :</b> <?php echo date('d-m-Y', strtotime($estimates_data_group['exp_date'])); ?></span><br>                                        <span class="non_gst_total hide"><label class="control-label non_gst_total"><b>Grand Total (INR): </b></label><?php echo indian_number_format($estimates_data_group['basic_total'], 2); ?></span>
                                        <span class="gst_total hide"> <label class="control-label gst_total"><b>Grand Total (INR): </b></label><?php echo indian_number_format($estimates_data_group['total'], 2); ?></span>
                                        <span class="igst_total hide"> <label class="control-label igst_total"><b>Grand Total (INR): </b></label><?php echo indian_number_format($estimates_data_group['total'], 2); ?></span>
                                        <span class="wv-text--strong "><b>Enquiry : </b>
                                            <?php if ($estimates_data_group['enquiry'] == '1') {
                                                echo "By Mail";
                                            } else if (($estimates_data_group['enquiry'] == '2')) {
                                                echo "By Verbal";
                                            } else if (($estimates_data_group['enquiry'] == '3')) {
                                                echo "Just Dial";
                                            } else if (($estimates_data_group['enquiry'] == '4')) {
                                                echo "India Mart";
                                            }
                                            ?></span><br>
                                        <?php 
                                        $session_data_head1 = $this->session->userdata('session_data_head');
                                        $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
                                        if ($_has_project_master && !empty($estimates_data_group['project_code'])): 
                                        ?>
                                            <span class="wv-text--strong"><b>Project Code :</b> <?php echo htmlspecialchars($estimates_data_group['project_code']); ?></span><br>
                                        <?php endif; ?>
                                    </div>


                                </div>
                            </div>
                            <br>
                            <div class="table-responsive">

                                <table class="table table-bordered quotation-grid-table" id="dynamic_field">
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Description</th>
                                        <th>QTY</th>
                                        <th>Unit</th>     
                                        <th>HSN Code</th>
                                        <th class="gst_per">TAX(%)</th>
                                        <th class="gst">SGST</th>
                                        <th class="gst">CGST</th>
                                        <th class="igst">IGST</th>
                                        <th>Price</th>
                                        <th>Discount(%)</th>
                                        <th>Amount</th>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    $colspan = 0;
$itemsToShow = 2; // Set to show 18 items
    $total_qty = 0;

                                    foreach ($show_quotation as $key) {
                                    ?>
                                        <tr>
                                            <td><span id="" class=""></span>
                                                <?php echo $i; ?>
                                                <?php $total_qty += $key->quantity; ?>
                                            </td>
                                            <td><span id="" class=""></span>
                                                <b><?php echo $key->product_name  . " - " .  $key->item_name; ?></b>
                                                <?php echo $key->description; ?>
                                                <input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control input-sm name_list product_name_auto" />
                                            </td>
                                            <td><span id="" class=""></span>
<?php echo $key->quantity; ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list" />
                                            </td>
                                            <td><span id="" class=""></span>
                                                <?php echo $key->unit; ?><input type="hidden" name="unit[]" value="<?php echo $key->unit; ?>" id="unit<?php echo $i; ?>" class="form-control input-sm name_list" />
                                            </td>


                                            <td><span id="" class=""></span>
                                                <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" />
                                            </td>

                                            <?php if ($key->gst_type != 'I') {
                                                $colspan = 9; ?>

                                                <td class="gst"><span id="" class=""></span>
                                                    <input type="hidden" name="gst" value="gst" id="gst">
                                                    <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                                </td>

                                                <td class="gst"><span id="" class=""></span>
                                                    <?php echo indian_number_format($key->sgst, 2); ?><input type="hidden" name="sgst[]" value="<?php echo $key->sgst; ?>" id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" />
                                                </td>

                                                <td class="gst"><span id="" class=""></span>
                                                    <?php echo indian_number_format($key->cgst, 2); ?><input type="hidden" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" />
                                                </td>

                                            <?php } else if ($key->gst_type != 'S') {
                                                $colspan = 10 ?>

                                                <td class="igst"><span id="" class=""></span>
                                                    <input type="hidden" name="igst" value="igst" id="igst">
                                                    <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                                </td>

                                                <td class="igst"><span id="" class=""></span>
                                                    <?php echo indian_number_format($key->igst, 2); ?><input type="hidden" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" />
                                                </td>

                                            <?php } ?>

                                            <td><span id="" class=""></span>
                                                <?php echo indian_number_format($key->price, 2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control input-sm name_list" />
                                            </td>

                                            <td><span id="" class=""></span>
                                                <?php echo $key->discount; ?><input type="hidden" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                                            </td>

                                            <td><span id="" class=""></span>
                                                <?php echo indian_number_format($key->amount, 2); ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>" />

                                            </td>
                                        </tr>
                                    <?php
                                        $i++;
                                    }
                                    while ($i <= $itemsToShow) {
                                        // GST (S) rows have 11 cells; IGST (I) rows have 10 cells
                                        $empty_cols = ($colspan == 10) ? 10 : 11;
                                    ?>
                                        <tr class="empty-row item-row">
                                            <?php for ($c = 0; $c < $empty_cols; $c++): ?>
                                            <td>&nbsp;</td>
                                            <?php endfor; ?>
                                        </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>

                                    <tr>
                                        <td colspan="11" class="text-right"><b>Total Qty:</b>
                                       <b><?php echo indian_number_format($total_qty, 0); ?></b></td>
                                    </tr>

                                    <tr class="">

                                        <td colspan="11" class="text-right">
                                            Total Before Tax ₹ <?php echo indian_number_format($estimates_data_group['basic_total'], 2); ?>
                                        </td>
                                    </tr>

                                    <tr class="gst">
                                        <td colspan="11" class="text-right">
                                            <span id="cgst_amount" name="cgst_amount"><b>CGST Amount:</b> ₹0.00</span>
                                        </td>
                                    </tr>
                                    <tr class="gst">
                                        <td colspan="11" class="text-right">
                                            <span id="sgst_amount" name="sgst_amount"><b>SGST Amount: </b>₹0.00</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="11" class="text-right">
                                            <span class="igst igst_edit_hide_show" id="igst_amount" name="igst_amount">IGST Amount: ₹0.00</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="11" class="text-right"><b><span>Grand Total: <?php echo indian_number_format($estimates_data_group['total'], 2); ?></span></b></td>
                                    </tr>

                                    <!-- Grand Total in Words -->
                                    <tr>
                                        <td colspan="11" class="text-right">
                                            <b>Grand Total in Words: 
                                            <?php
                                            require_once(APPPATH . '/third_party/amount_convert.php');
                                            echo number_to_word($estimates_data_group['total']);
                                            ?> Only</b>
                                        </td>
                                    </tr>
                                </table>

                                

                                <div align="right" style="margin: 10px">

                                    <!--                                    <b> <span>Grand Total: <?php echo number_format($estimates_data_group['total'], 2); ?></span></b><br>-->
                                    <!--<span id="total_amount" name="total_amount">Total: ₹0.00</span><br>-->
                                    <span class="hide" id="sgst_amount" name="sgst_amount">SGST Amount: ₹0.00</span><br>
                                    <span class="hide" id="cgst_amount" name="cgst_amount">CGST Amount: ₹0.00</span><br>

                                </div>
 
                                <label class="control-label pull-left"><b>Notes</b></label><br>

                                <!--                                <div class="col-sm-12">
                                    <textarea style="overflow: auto;
                                              border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $settings['notes']; ?></textarea><br>
                                </div>

                                <div class="form-group row ">
                                    <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Sign :</b></label>
                                    <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Sign :</b></label>
                                </div>-->
                                <?php if (!empty($estimates_data_group['quotation_memo'])) { ?>
                                <div class="col-sm-12 quotation-section" style="margin-top:10px;">
                                    <label class="control-label"><b>Notes:</b></label>
                                    <div><?php echo $estimates_data_group['quotation_memo']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($estimates_data_group['terms_and_conditions'])) { ?>
                                <div class="col-sm-12 quotation-section" style="margin-top:10px;">
                                    <label class="control-label"><b>Terms &amp; Conditions:</b></label>
                                    <div><?php echo $estimates_data_group['terms_and_conditions']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($estimates_data_group['payment_terms'])) { ?>
                                <div class="col-sm-12 quotation-section" style="margin-top:10px;">
                                    <label class="control-label"><b>Payment Terms:</b></label>
                                    <div><?php echo $estimates_data_group['payment_terms']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($estimates_data_group['process_schedule'])) { ?>
                                <div class="col-sm-12 quotation-section" style="margin-top:10px;">
                                    <label class="control-label"><b>Process Schedule:</b></label>
                                    <div><?php echo $estimates_data_group['process_schedule']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($estimates_data_group['taxes'])) { ?>
                                <div class="col-sm-12 quotation-section" style="margin-top:10px;">
                                    <label class="control-label"><b>Taxes:</b></label>
                                    <div><?php echo $estimates_data_group['taxes']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($estimates_data_group['exclusions'])) { ?>
                                <div class="col-sm-12 quotation-section" style="margin-top:10px;">
                                    <label class="control-label"><b>Exclusions:</b></label>
                                    <div><?php echo $estimates_data_group['exclusions']; ?></div>
                                </div>
                                <?php } ?>

                                <div class="quotation-footer-note"><?php echo $settings['quotation_footer']; ?></div>
                                <div class="quotation-footer-subnote">This is Computer Generated Invoice</div><br>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
    <script>
        $(document).ready(function() {

            var igst = $("#igst").val();
            var gst = $("#gst").val();
            //  alert("show "+igst);
            //  alert("show "+gst);
            if (igst == "igst") {
                $(".gst").hide();
                $(".igst").show();
                $(".gst_per").show();
                $(".total").show();

                $(".non_gst_total").hide();
                $(".gst_total").hide();
                $(".igst_total").show();
                $(".hide_gst").hide();

            } else
            if (gst == "gst") {
                $(".gst").show();
                $(".igst").hide();
                $(".gst_per").show();
                $(".total").show();

                $(".non_gst_total").hide();
                $(".gst_total").show();
                $(".igst_total").hide();
                $(".hide_igst").hide();
            }

        });
    </script>
