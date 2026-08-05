<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . '/third_party/amount_convert.php');

// helper for formatting dates in the show view, mirrors logic used in print template
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
<style>
    .salesorder-logo-left {
        text-align: left;
    }

    .salesorder-logo-left img {
        display: inline-block;
        margin-top: 26px;
    }

    .salesorder-grid-table {
        border-collapse: collapse;
        border: 2px solid #2f2f2f;
        background-color: #fff;
    }

    .salesorder-grid-table > tbody > tr > th,
    .salesorder-grid-table > tbody > tr > td,
    .salesorder-grid-table > thead > tr > th,
    .salesorder-grid-table > thead > tr > td {
        border: 1.5px solid #2f2f2f !important;
        color: #111;
        vertical-align: middle;
    }

    .salesorder-grid-table > tbody > tr > th,
    .salesorder-grid-table > thead > tr > th {
        background-color: #f3f3f3;
    }

    /* Responsive button group styles */
    .d-flex {
        display: flex;
    }
    .flex-wrap {
        flex-wrap: wrap;
    }
    .align-items-center {
        align-items: center;
    }
    .justify-content-between {
        justify-content: space-between;
    }
    .d-inline-block {
        display: inline-block;
    }
    .input-group {
        display: inline-flex;
        align-items: center;
    }
    .input-group-addon {
        padding: 6px 12px;
        font-size: 14px;
        font-weight: normal;
        line-height: 1;
        text-align: center;
        background-color: #eee;
        border: 1px solid #ccc;
        border-radius: 4px 0 0 4px;
        border-right: 0;
    }
    .input-group .btn {
        border-radius: 0 4px 4px 0;
    }
    @media (max-width: 767px) {
        .d-flex.flex-wrap {
            flex-direction: column;
            align-items: stretch !important;
        }
        .d-flex.flex-wrap > div,
        .d-flex.flex-wrap form,
        .d-flex.flex-wrap a,
        .d-flex.flex-wrap .dropdown {
            width: 100%;
            margin: 4px 0;
        }
        .input-group {
            width: 100%;
        }
    }

    @media print {
        .row[style*="padding:2%"] {
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
                                    Sales Order
                                </h1>-->
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SalesOrderController/index/' ?>">Sales Order</a></li>
                    <li class="active">Sales Order Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        
                        <!--<div class="box">-->
                        <!--<div class="box-header">-->
                          <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>Sales Order:<?php echo $salesorders_data_group['number_fk']; ?></h2></label>                        <!--</div>-->
                        <!-- /.box-header -->
                        <!--<div class="box-body">-->

                        <!-- BUTTON ROW - MODIFIED FOR BETTER LAYOUT -->
                        <div class="row" style="padding:2%; margin-left:0; margin-right:0;">
                            <div class="col-xs-12">
                                <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                                    <!-- Left side buttons -->
                                    <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                        <!-- Edit button -->
                                        <a href="<?php echo base_url(); ?>SalesOrderController/edit_salesorder_details/<?php echo $salesorders_data_group['id']; ?>" class="btn btn-primary">Edit</a>

                                        <!-- Convert to Invoice form -->
                                        <form method="POST" action="<?php echo base_url(); ?>SalesOrderController/convert_to_invoice/<?php echo $salesorders_data_group['id']; ?>" class="d-inline-block">
                                            <input type="hidden" name="number" value="<?php echo $salesorders_data_group['id']; ?>">
                                            <?php
                                            if (date('m') <= 3) {
                                                $financial_year = (date('y') - 1) . '-' . date('y');
                                            } else {
                                                $financial_year = date('y') . '-' . (date('y') + 1);
                                            }
                                            $next_inv_no = "INV/" . sprintf("%04d", $invoice_id + 1) . "/" . $financial_year;
                                            ?>
                                            <input type="hidden" name="invoice_number" value="<?php echo $next_inv_no; ?>">
                                            <button type="submit" class="btn btn-primary">Convert to Invoice</button>
                                        </form>

                                        <!-- SGST <-> IGST toggle form -->
                                        <form method="POST" action="<?php echo base_url(); ?>SalesOrderController/sgst_to_igst" class="d-inline-block">
                                            <input type="hidden" name="salesorder_id" value="<?php echo $salesorders_data_group['id']; ?>">
                                            <input type="hidden" name="number_fk" value="<?php echo $salesorders_data_group['number_fk']; ?>">
                                            <?php 
                                            $gst_type = '';
                                            foreach ($show_salesorder as $key) { 
                                                $gst_type = $key->gst_type; 
                                                break;
                                            } 
                                            ?>
                                            <button type="submit" class="btn btn-primary">
                                                <?php echo ($gst_type != 'I') ? 'SGST → IGST' : 'IGST → SGST'; ?>
                                            </button>
                                        </form>

                                        <!-- Print button -->
                                        <a href="<?php echo base_url(); ?>Pdf/print_igst_salesorder/<?php echo $salesorders_data_group['id']; ?>?mode=I" target="_blank" class="btn btn-primary" onclick="setTimeout(function(){window.print();}, 500); return false;">Print</a>

                                        <!-- More dropdown -->
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                                More <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a id="exportpdf" href="<?php echo base_url(); ?>Pdf/print_igst_salesorder/<?php echo $salesorders_data_group['id']; ?>">Export As PDF</a></li>
                                                <li><a href="<?php echo base_url() . 'SalesOrderController/export_salesorder_excel/' . $salesorders_data_group['id']; ?>">Export As Excel</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Right side Close button -->
                                    <div>
                                        <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-close"></i> Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END BUTTON ROW -->

                        <div class="shadows1" >
                            <div class="row">
                                <section class="contemporary-template__header">
                                    <div class="col-md-6">
                                        <div class="contemporary-template__header__logo salesorder-logo-left">
                                            <img class="contemporary-template__business-logo" src="<?php echo base_url() . $settings['company_logo'] ?>" width="70%" height="35%">

                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="contemporary-template__header__info">
                                            <div class="wv-heading--title"><h1>Sales Order</h1></div>
                                            <div class="wv-heading--subtitle"> <?php //echo $settings['salesorder_subheading']; ?></div>
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

                                <div class="col-md-6">

<!--                                    <div class="form-group row ">
                                        <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>Buyer:</b></label>
                                    </div>-->
                                    <div class="contemporary-template__header__info">
                                            
                                            <div class="wv-heading--subtitle"><b>Customer Details :</b></div>
                                            <span class="hide"><b>Project Code :</b> <?php echo $salesorders_data_group['project_code']; ?></span>
                                            <span class="wv-text--strong"><b>Company Name :</b> <?php echo $salesorders_data_group['company_name']; ?></span><br>
                                            <span class="wv-text--strong"><b>Customer Name :</b> <?php echo $salesorders_data_group['fullname']; ?></span><br>
                                            <span class="wv-text--strong"><b>Customer Code :</b> <?php if ($salesorders_data_group['c_code']) { ?>
                                                <?php echo $salesorders_data_group['c_code']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided"; ?>
                                            <?php } ?></span><br>
                                            <span class="wv-text--strong"><b>GST Number :</b> <?php if ($salesorders_data_group['gst']) { ?>
                                                <?php echo $salesorders_data_group['gst']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided"; ?>
                                            <?php } ?></span><br>
                                            <span class="wv-text--strong"><b>PAN Number :</b> <?php if ($salesorders_data_group['pancard']) { ?>
                                                <?php echo $salesorders_data_group['pancard']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided"; ?>
                                            <?php } ?></span><br>
                                            <span class="wv-text--strong"><b>State Code :</b> <?php if ($salesorders_data_group['state_code']) { ?>
                                                <?php echo $salesorders_data_group['state_code']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided"; ?>
                                            <?php } ?></span><br>
                                            <span class="wv-text--strong"><b>Address :</b>
                                            <?php if ($salesorders_data_group['address']) { ?>
                                                <?php echo $salesorders_data_group['address']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided"; ?>
                                            <?php } ?></span><br>
                                            <span class="wv-text--strong"><b>PO Number :</b> <?php echo $salesorders_data_group['po_number']; ?></span><br>
                                            <span class="wv-text--strong"><b>PO Date :</b> <?php echo !empty($salesorders_data_group['po_date']) ? date('d-m-Y', strtotime($salesorders_data_group['po_date'])) : 'N/A'; ?></span><br>
                                            <span class="wv-text--strong"><b>PO Status :</b> <?php echo ucfirst($salesorders_data_group['po_status'] ?? 'N/A'); ?></span><br>
                                            <span class="wv-text--strong"><b>Attachment :</b> 
                                               <?php if (!empty($salesorders_data_group['attachment'])) { 
                                                   $file_path = base_url() . 'uploads/' . $salesorders_data_group['attachment'];
                                                   echo '<a href="' . $file_path . '" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-download"></i> Download</a>';
                                               } else { 
                                                   echo 'None';
                                               } 
                                               ?></span><br>
                                            <span class="wv-text--strong"><b>Enquiry:</b> <?php 
                                               $enquiry = $salesorders_data_group['enquiry'];
                                               $enquiry_sources = array(1 => 'Mail', 2 => 'Verbal', 3 => 'Just Dial', 4 => 'India Mart');
                                               echo isset($enquiry_sources[$enquiry]) ? $enquiry_sources[$enquiry] : 'N/A';
                                            ?></span>
                                           
                                        </div>
                                </div>

                                <div class="col-md-6">
                                   
                                    <div class="contemporary-template__header__info">
                                        
                                        

                                            <div class="wv-heading--subtitle"><b>Sales Order Details :</b></div>
                                            <br>
                                            <span class="wv-text--strong"><b>Sales Order Number :</b> <?php echo !empty($salesorders_data_group['number']) ? $salesorders_data_group['number'] : $salesorders_data_group['number_fk']; ?></span><br>
                                             <span class="wv-text--strong"><b>Sales Order Date :</b> <?php echo format_show_date($salesorders_data_group['date']); ?></span><br>
                                             <span class="wv-text--strong"><b>Delivery Date :</b> <?php echo format_show_date($salesorders_data_group['exp_date']); ?></span><br>
                                             <?php if (!empty($salesorders_data_group['system'])) { ?>
                                                 <span class="wv-text--strong"><b>System :</b> <?php echo htmlspecialchars($salesorders_data_group['system']); ?></span><br>
                                             <?php } ?>
                                             <?php if (!empty($salesorders_data_group['location'])) { ?>
                                                 <span class="wv-text--strong"><b>Location :</b> <?php echo htmlspecialchars($salesorders_data_group['location']); ?></span><br>
                                             <?php } ?>
                                             <?php if (!empty($salesorders_data_group['capacity'])) { ?>
                                                 <span class="wv-text--strong"><b>Capacity :</b> <?php echo htmlspecialchars($salesorders_data_group['capacity']); ?></span><br>
                                             <?php } ?>
                                             <?php if (!empty($salesorders_data_group['project_qty'])) { ?>
                                                 <span class="wv-text--strong"><b>Project Quantity :</b> <?php echo htmlspecialchars($salesorders_data_group['project_qty']); ?></span><br>
                                             <?php } ?><br>
                                            <span class="non_gst_total hide"><label class="control-label non_gst_total"><b>Grand Total (INR): </b></label><?php echo indian_number_format($salesorders_data_group['basic_total'], 2); ?></span>
                                            <span class="gst_total hide"> <label class="control-label gst_total"><b>Grand Total (INR): </b></label><?php echo indian_number_format($salesorders_data_group['total'], 2); ?></span>
                                            <span class="igst_total hide"> <label class="control-label igst_total"><b>Grand Total (INR): </b></label><?php echo indian_number_format($salesorders_data_group['total'], 2); ?></span>
                                           <span class="wv-text--strong hide"><b>Enquiry : </b>
                            <?php if ($salesorders_data_group['enquiry'] == '1') { 
                             echo "By Mail";
                            } else if(($salesorders_data_group['enquiry'] == '2')) { 
                                echo "By Verbal";
                             } else if(($salesorders_data_group['enquiry'] == '3')) { 
                                echo "Just Dial";
                             } else if(($salesorders_data_group['enquiry'] == '4')) { 
                                echo "India Mart";
                            }
                            ?></span>
                            
                            <span class="wv-text--strong hide"><b>Project Code :</b> <?php echo $salesorders_data_group['project_code']; ?></span><br>
                           
                            <span class="wv-text--strong hide"><b>Customer Code :</b> <?php echo $salesorders_data_group['customer_code']; ?></span><br>

                                                
                                           
                                        </div>
                                    

                                </div>
                            </div>
                            <br>
                            <div class="table-responsive">  

                                <table class="table table-bordered salesorder-grid-table" id="dynamic_field">  
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
                                     <!-- <th>Discount(%)</th> -->
                                        <th>Amount</th>
                                    </tr>
<?php
                                     $total_qty = 0;
                                     $total_before_tax = 0;
                                     $total_sgst = 0;
                                     $total_cgst = 0;
                                     $total_igst = 0;
                                     $i = 1;
                                     foreach ($show_salesorder as $key) {
                                         // ---- Render Section Heading Row ----
                                         if (isset($key->product_name) && $key->product_name === '__HEADING__'):
                                             $desc = trim($key->description ?? '');
                                             $isMain = true;
                                             if (isset($key->tag_no) && ($key->tag_no === 'MAIN' || $key->tag_no === 'SUB')) {
                                                 $isMain = ($key->tag_no === 'MAIN');
                                             }
                                             $bg = $isMain ? '#e6e0ed' : '#fdeada';
                                             $fg = $isMain ? '#5a3d8a' : '#000000';
                                             $displayDesc = $isMain ? strtoupper($desc) : $desc;
                                             ?>
                                             <tr style="background-color: <?php echo $bg; ?> !important;">
                                                 <td colspan="11" style="background-color: <?php echo $bg; ?> !important; color: <?php echo $fg; ?> !important; font-weight: bold; padding: 8px 12px; border: 1px solid #ddd !important; vertical-align: middle;">
                                                     <i class="fa fa-tag" style="color: <?php echo $fg; ?>; margin-right: 8px; opacity: 0.7;"></i>
                                                     <strong><?php echo htmlspecialchars($displayDesc); ?></strong>
                                                 </td>
                                             </tr>
                                             <?php
                                             continue;
                                         endif;
                                         // ---- End Heading Row ----

                                         $total_qty += $key->quantity;
                                         $total_before_tax += $key->amount;
                                         $total_sgst += $key->sgst;
                                         $total_cgst += $key->cgst;
                                         $total_igst += $key->igst;
                                         ?>
                                        <tr> 
                                            <td><span id="" class=""></span>
                                                <?php echo $i; ?> 
                                            </td>
                                            <td><span id="" class=""></span>
                                                <b><?php echo $key->product_name. " - " .  $key->item_name; ?></b><br><input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control input-sm name_list product_name_auto" /><input type="hidden" class="form-control input-sm"   name="salesorder_id[]" id="salesorder_id<?php echo $i; ?>"  value="<?php echo $key->salesorder_id; ?>">
                                                <?php echo $key->description; ?><input type="hidden" name="description[]" value="<?php echo $key->description; ?>" id="description<?php echo $i; ?>" class="form-control input-sm name_list description_auto" />
                                            </td>
                                            <td><span id="" class=""></span>
                                                <?php echo $key->quantity; ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" value="1" />
                                            </td>

                                                <td><span id="" class=""></span>
                                                    <?php echo $key->unit ?? ''; ?><input type="hidden" name="unit[]" value="<?php echo $key->unit ?? ''; ?>" id="unit<?php echo $i; ?>" class="form-control input-sm name_list" />
                                                </td>
                                            <td><span id="" class=""></span>
                                                <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                                            </td>

                                            <?php if ($key->gst_type != 'I') { ?>

                                                <td class="gst"><span id="" class=""></span>
                                                    <input type="hidden" name="gst" value="gst" id="gst">
                                                    <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                                </td>

                                                <td class="gst"><span id="" class=""></span>
                                                    <?php echo indian_number_format($key->sgst, 2); ?><input type="hidden" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" />
                                                </td>

                                                <td class="gst"><span id="" class=""></span>
                                                    <?php echo indian_number_format($key->cgst, 2); ?><input type="hidden" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" />
                                                </td>

                                            <?php } else if ($key->gst_type != 'S') { ?>

                                                <td class="igst"><span id="" class=""></span>
                                                    <input type="hidden" name="igst" value="igst" id="igst">
                                                    <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                                </td>

                                                <td class="igst"><span id="" class=""></span>
                                                    <?php echo indian_number_format($key->igst, 2); ?><input type="hidden" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" />
                                                </td>

                                            <?php } else if (($key->gst_type != 'I') || ($key->gst_type != 'S')) { ?>
                                            <input type="hidden" name="non_gst" value="non_gst" id="non_gst">     
                                        <?php } ?>

                                        <td><span id="" class=""></span>
                                            <?php echo indian_number_format($key->price, 2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control input-sm name_list"/>
                                        </td>

                                       <!-- <td class=""><span id="" class=""></span>
                                            <?php echo $key->discount; ?><input type="hidden" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list"/> %
                                        </td> -->

                                        <td><span id="" class=""></span>
                                            <?php echo indian_number_format($key->amount, 2); ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>

                                        </td>
                                        </tr>  
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                        
                                         <tr class="">
                                            <td colspan="3" class="text-right"><b>Total Qty: <?php echo number_format($total_qty, 2); ?></b></td>
                                            <td colspan="8" class="text-right">
                                                Total Before Tax ₹ <?php echo indian_number_format($total_before_tax, 2); ?>
                                             </td>
                                         </tr>
                                        
                                        
                                        
                                        
                                        <tr class="gst">
                                            <td colspan="11"  class="text-right">
                                                <span id="sgst_amount" name="sgst_amount"><b>SGST Amount: </b>₹<?php echo indian_number_format($total_sgst, 2); ?></span><br>
                                             </td>
                                            
                                         </tr>
                                          <tr class="gst">
                                            <td colspan="11"  class="text-right">
                                                <span id="cgst_amount" name="cgst_amount"><b>CGST Amount:</b> ₹<?php echo indian_number_format($total_cgst, 2); ?></span><br>
                                             </td>
                                         </tr>
                                        
                                           <tr>
                                            <td colspan="11"  class="text-right">
                                                <span class="igst igst_edit_hide_show" id="igst_amount" name="igst_amount">IGST Amount: ₹<?php echo indian_number_format($total_igst, 2); ?></span>
                                             </td>
                                         </tr>
                                         
                                         <?php $calculated_grand_total = $total_before_tax + $total_sgst + $total_cgst + $total_igst; ?>
                                         <tr>
                                            <td colspan="11"  class="text-right"><b> <span>Grand Total: <?php echo indian_number_format($calculated_grand_total, 2); ?></span></b></td>
                                         </tr>

                                         <tr>
                                            <td colspan="11" class="text-right" style="font-weight:bold;">
                                                Grand Total in Words: <?php require_once(APPPATH . '/third_party/amount_convert.php'); echo number_to_word($calculated_grand_total); ?> Only
                                             </td>
                                         </tr>
                                </table>   

                                <div align="right" style="margin: 10px">

<!--                                    <b> <span>Grand Total: <?php echo number_format($salesorders_data_group['total'], 2); ?></span></b><br>-->
                                     <!--<span id="total_amount" name="total_amount">Total: ₹0.00</span><br>-->
                                    <span class="hide" id="sgst_amount" name="sgst_amount">SGST Amount: ₹0.00</span><br>
                                    <span class="hide" id="cgst_amount" name="cgst_amount">CGST Amount: ₹0.00</span><br>
                                    
                                </div>

                               

                                <?php if (!empty($salesorders_data_group['salesorder_memo'])) { ?>
                                <div class="col-sm-12" style="margin-top:10px;">
                                    <label class="control-label"><b>Notes:</b></label>
                                    <div><?php echo $salesorders_data_group['salesorder_memo']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($salesorders_data_group['terms_and_conditions'])) { ?>
                                <div class="col-sm-12" style="margin-top:10px;">
                                    <label class="control-label"><b>Terms &amp; Conditions:</b></label>
                                    <div><?php echo $salesorders_data_group['terms_and_conditions']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($salesorders_data_group['payment_terms'])) { ?>
                                <div class="col-sm-12" style="margin-top:10px;">
                                    <label class="control-label"><b>Payment Terms:</b></label>
                                    <div><?php echo $salesorders_data_group['payment_terms']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($salesorders_data_group['process_schedule'])) { ?>
                                <div class="col-sm-12" style="margin-top:10px;">
                                    <label class="control-label"><b>Process Schedule:</b></label>
                                    <div><?php echo $salesorders_data_group['process_schedule']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($salesorders_data_group['taxes'])) { ?>
                                <div class="col-sm-12" style="margin-top:10px;">
                                    <label class="control-label"><b>Taxes:</b></label>
                                    <div><?php echo $salesorders_data_group['taxes']; ?></div>
                                </div>
                                <?php } ?>

                                <?php if (!empty($salesorders_data_group['exclusions'])) { ?>
                                <div class="col-sm-12" style="margin-top:10px;">
                                    <label class="control-label"><b>Exclusions:</b></label>
                                    <div><?php echo $salesorders_data_group['exclusions']; ?></div>
                                </div>
                                <?php } ?>

                                <div class="form-group row " style="margin-top:20px;">
                                    <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Signatory :</b></label>
                                    <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Signatory :</b></label>
                                </div>

                                <?php if (!empty($salesorders_data_group['salesorder_footer'])) { ?>
                                <center style="font-size: 12px"><?php echo $salesorders_data_group['salesorder_footer']; ?></center>
                                <?php } ?>
                                <center style="font-size: 10px">This is Computer Generated Sales Order</center><br>

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

        $(document).ready(function () {

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

            }else
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
