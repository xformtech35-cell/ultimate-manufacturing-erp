<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . '/third_party/amount_convert.php');
// Ensure variables used as arrays are initialized to avoid "access array offset on null" warnings
if (!isset($purchase_return_data_group) || !is_array($purchase_return_data_group)) {
    $purchase_return_data_group = [];
}
if (!isset($settings) || !is_array($settings)) {
    $settings = [];
}
if (!isset($gst_type)) {
    $gst_type = '';
}
?>
<style>
    .purchasereturn-logo-left {
        text-align: left;
    }

    .purchasereturn-logo-left img {
        display: inline-block;
    }

    .purchasereturn-grid-table {
        border-collapse: collapse;
        border: 2px solid #2f2f2f;
        background-color: #fff;
    }

    .purchasereturn-grid-table > tbody > tr > th,
    .purchasereturn-grid-table > tbody > tr > td,
    .purchasereturn-grid-table > thead > tr > th,
    .purchasereturn-grid-table > thead > tr > td {
        border: 1.5px solid #2f2f2f !important;
        color: #111;
        vertical-align: middle;
    }

    .purchasereturn-grid-table > tbody > tr > th,
    .purchasereturn-grid-table > thead > tr > th {
        background-color: #f3f3f3;
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
                                    Purchase Return
                                </h1>-->
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Purchase Return</a></li>
                    <li class="active">Purchase Return</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2> Debit Note:<?php echo $purchase_return_data_group['number'] ?? ''; ?></h2></label>

                        <div class="row" style="padding:2%; margin-left:0; margin-right:0;">
    <div class="col-xs-6" style="padding-left:0;">
        <div class="btn-group" role="group">
            <a href="<?php echo base_url(); ?>SupplierController/edit_purchase_return_details?number=<?php echo ($purchase_return_data_group['number'] ?? '') . '&gst_type=' . $gst_type; ?>" class="btn btn-primary" role="button" style="margin-right:5px;">Edit</a>
            <div class="dropdown" style="display:inline-block;">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More <b class="caret"></b>
                </button>
                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo $purchase_return_data_group['number'] ?? ''; ?>">
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <li class="hide"><a class="dropdown-item" href="<?php echo base_url(); ?>/LoginController/get_settings">Edit Business Information</a></li>
                    <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/download_purchase_return/<?php echo $purchase_return_data_group['number'] ?? ''; ?>">Export As PDF</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-xs-6 text-right" style="padding-right:0;">
        <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-close"></i> Close</a>
    </div>
</div>

                        <!-- /.box-header -->
                        <div class="shadows1">
                            <div class="row">
                                <section>
                                    <div class="col-md-6">
                                        <div class="purchasereturn-logo-left">
                                            <img src="<?php echo base_url() . ($settings['company_logo'] ?? '') ?>" width="70%" height="35%">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="contemporary-template__header__info">
                                            <div class="wv-heading--title"><h1>Debit Note</h1></div>
                                            <div class="wv-heading--subtitle"></div>
                                            <span class="wv-text--strong"><b> <?php echo $settings['company_name'] ?? ''; ?></b></span><br>
                                            <span class="wv-text--strong"><b>GST Number :</b> <?php echo $settings['company_gst'] ?? ''; ?></span><br>
                                           <span class="wv-text--strong"><b>PAN Number :</b> <?php echo $settings['company_pan'] ?? ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Mobile Number :</b> <?php echo $settings['mobile'] ?? ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Email ID :</b> <?php echo $settings['email'] ?? ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Address :</b> <?php echo $settings['address'] ?? ''; ?></span>
                                            
                                        </div>
                                    </div>
                                </section>

                            </div>
                            <hr>
                            <div class="row">

                                <div class="col-md-6">

                                        
                                         <span class="wv-text--strong"><b>Vendor Name :</b> <?php echo $purchase_return_data_group['company_name'] ?? ''; ?></b></span><br>
                                         <span class="wv-text--strong"><b>Vendor Code :</b> <?php echo $purchase_return_data_group['s_code'] ?? 'N/A'; ?></span><br>
                                    <div class="contemporary-template__header__info">
                                            
                                         
                                            
                                            <span class="wv-text--strong"><b>Address :</b> <?php echo $purchase_return_data_group['address'] ?? ''; ?></span><br>
                                           <span class="wv-text--strong"><b>GST Number :</b> <?php if (isset($purchase_return_data_group['gst']) && $purchase_return_data_group['gst']) { ?>
                                                <?php echo $purchase_return_data_group['gst']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?></span><br>
                                            <span class="wv-text--strong"><b>PAN Number :</b> <?php if (isset($purchase_return_data_group['pancard']) && $purchase_return_data_group['pancard']) { ?>
                                                <?php echo $purchase_return_data_group['pancard']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?></span><br>
                                            <span class="wv-text--strong"><b> Customer Name :</b> <?php echo $purchase_return_data_group['fullname'] ?? ''; ?></span><br>
                                            
                                        </div>
                                    
                                    

                                </div>

                                <div class="col-md-6">
                                    <span class="wv-text--strong"><b>Return Details</b></b></span><br>
                                    
                                    
                                    <div class="contemporary-template__header__info">
                                        
                                        <span class="wv-text--strong"><b>Return Number :</b> <?php echo $purchase_return_data_group['number'] ?? ''; ?></span><br>
                                        <span class="wv-text--strong"><b>Return Date :</b> <?php echo (isset($purchase_return_data_group['date']) && $purchase_return_data_group['date']) ? date('d-m-Y', strtotime($purchase_return_data_group['date'])) : ''; ?></span><br>
                                        <span class="wv-text--strong"><b>Delivery Date :</b> <?php echo $purchase_return_data_group['delivery_date'] ?? ''; ?></span><br>
                                        <span class="wv-text--strong"><b>Ref. No.:</b> <?php echo $purchase_return_data_group['ref_no'] ?? ''; ?></span><br>

                                        </div>
                                    

                                </div>
                            </div>
                            <br>
                             <div class="table-responsive">  

                        <table class="table table-bordered purchasereturn-grid-table" id="dynamic_field">  
                            <tr>
                                <th>Sr.No.</th>
                                <th>Description</th>
                                <th>HSN Code</th>
                               <th>Qty</th>
                               <th>Unit</th>
           

                                <th>TAX(%)</th>
                                <?php if (!empty($show_purchase_return) && $show_purchase_return[0]->gst_type != 'I') { ?>
                                    <th class="gst">SGST</th>
                                    <th class="gst">CGST</th>
                                <?php } else { ?>
                                    <th class="igst">IGST</th>
                                <?php } ?>
                                <th>Price</th>
                                    <th>Discount(%)</th>
                                <th>Amount</th>
                            </tr>
                            <?php
                            $i = 1;
                            $sgst_total_amt = 0;
                            $igst_total_amt = 0;
                            $amt = 0;
                            $total_qty = 0;
                            foreach ($show_purchase_return as $key) {
                                ?>
                                <?php $sgst_total_amt = $sgst_total_amt + $key->cgst; ?>
                                <?php $igst_total_amt = $igst_total_amt + $key->igst; ?>
                                <?php $total_qty += $key->quantity; ?>
                                <tr> 
                                    <td><span id="" class=""></span>
                                        <?php echo $i; ?>
                                    </td>

                                    <td><span id="" class=""></span>
                                        <b><?php echo $key->product_name; ?></b>
                                        <?php echo $key->description; ?>
                                              <input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control input-sm name_list product_name_auto" />
                                    </td>

                                    <td><span id="" class=""></span>
                                        <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                                    </td>

                                    <td><span id="" class=""></span>
                                        <?php echo indian_number_format($key->quantity, 2); ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" value="1" />
                                    </td>

                                    <td><span id="" class=""></span>
                                        <?php echo $key->unit; ?><input type="hidden" name="unit[]" value="<?php echo $key->unit; ?>" id="unit<?php echo $i; ?>" class="form-control input-sm name_list" />
                                    </td>

                                    <td><span id="" class=""></span>
                                        <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                    </td>

                                    <?php if ($key->gst_type != 'I') { ?>
                                        <td class="gst"><span id="" class=""></span>
                                            <input type="hidden" name="gst" value="gst" id="gst">
                                            <?php echo indian_number_format($key->sgst, 2); ?><input type="hidden" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" />
                                        </td>

                                        <td class="gst"><span id="" class=""></span>
                                            <?php echo indian_number_format($key->cgst, 2); ?><input type="hidden" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" />
                                        </td>
                                    <?php } else { ?>

                                        <td class="igst">
                                            <span id="" class=""></span><?php echo indian_number_format($key->igst, 2); ?>
                                            <input type="hidden" name="igst" value="igst" id="igst">
                                            <input type="hidden" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" />
                                        </td>
                                    <?php } ?>



                                    <td><span id="" class=""></span>
                                        <?php echo indian_number_format($key->price, 2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control input-sm name_list"/>

                                    </td>
                                        <td><span id="" class=""></span>
                                            <?php echo indian_number_format($key->discount, 2); ?><input type="hidden" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list"/>
                                        </td>

                                    <td><span id="" class=""></span>
                                        <?php echo indian_number_format($key->amount, 2); 
                                        $amt += $key->amount;
                                        ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>

                                    </td>


                                </tr>  

                                <?php
                                $i++;
                            }
                            ?>
                            <?php
                            $i = 1;
                            foreach ($show_purchase_return as $key) {
                                ?>
                                <?php if ($key->gst_type != 'I') { ?>

                                    <tr>
                                        <td colspan="10" class="text-right">
                                            <b>Total Qty:</b>
                                        </td>
                                        <td class="text-left">
                                            <b><?php echo indian_number_format($total_qty, 2); ?></b>
                                        </td>
                                    </tr>

                                    <tr class="">
                                            <td colspan="10"  class="text-right">
                                                Total Before Tax: 
                                            </td>
                                            <td class="text-left">
                                                ₹ <?php echo indian_number_format($amt, 2); ?>
                                            </td>
                                            
                             </tr> 
                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>CGST Amount : </b></label>
                                        </td>

                                        <td class="text-left">
                                            <b><?php echo indian_number_format($sgst_total_amt, 2); ?></b><br>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>SGST Amount : </b></label>
                                        </td>
                                        <td class="text-left">
                                            <b><?php echo indian_number_format($sgst_total_amt, 2); ?></b><br>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>GST Amount : </b></label>
                                        </td>
                                        <td class="text-left">
                                            <b><?php echo indian_number_format($sgst_total_amt * 2, 2); ?></b><br>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="10"  class="text-right">
                                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                                        </td>
                                        <td class="text-left">
                                            <b>₹<?php echo indian_number_format($purchase_return_data_group['total'] ?? 0, 2); ?></b> <br>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td colspan="11"  class="text-right">
                                            <?php
                                            // include amount_convert
                                            require_once( APPPATH . '/third_party/amount_convert.php');
                                            ?>
                                            <b> Grand Total in Words:<?php echo number_to_word($purchase_return_data_group['total'] ?? 0); ?> Only.</b><br>
                                        </td>
                                    </tr>

                                <?php } else { ?>
                                    
                                     <tr>
                                        <td colspan="9" class="text-right">
                                            <b>Total Qty:</b>
                                        </td>
                                        <td class="text-left">
                                            <b><?php echo indian_number_format($total_qty, 2); ?></b>
                                        </td>
                                    </tr>

                                     <tr class="">
                                            <td colspan="9"  class="text-right">
                                                Total Before Tax: 
                                            </td>
                                            <td class="text-left">
                                                ₹ <?php echo indian_number_format($amt, 2); ?>
                                            </td>
                                            
                             </tr> 

                                    <tr>
                                        <td colspan="9"  class="text-right">
                                            <label class="control-label"><b>IGST Amount : </b></label>
                                        </td>

                                        <td class="text-left">
                                            <b> <?php echo indian_number_format($igst_total_amt, 2); ?></b><br>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="9"  class="text-right">
                                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                                        </td>

                                        <td class="text-left">
                                            <b>₹<?php echo indian_number_format($purchase_return_data_group['total'], 2); ?></b> <br>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="9"  class="text-right">
                                            <?php
                                            // include amount_convert
                                            require_once( APPPATH . '/third_party/amount_convert.php');
                                            ?>
                                            <b> Grand Total in Words:<?php echo number_to_word($purchase_return_data_group['total']); ?> Only.</b><br>
                                        </td>
                                    </tr>

                                <?php } ?>

                                <?php
                                $i++;
                                break;
                            }
                            ?>
                        </table>   

                       

                        <div class="col-sm-12">
                            <textarea style="overflow: auto;
                                      border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $settings['invoice_notes']; ?></textarea>
                        </div>
                        <div class="form-group row ">
                            <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Signatory :</b></label>
                            <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Signatory :</b></label>
                        </div>

                        <div style="text-align: center;">This is Computer Generated Invoice</div><br>

                    </div>  
                        </div>
                        <!-- /.box-body -->
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
            </section>
        </div>
        <!-- /.row -->
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    
    
        <script>

        $(document).ready(function () {

            var igst = $("#igst").val();
         //   alert(igst);
            if (igst == "igst") {
                $(".gst").hide();
                $(".igst").show();

            } else {
                $(".gst").show();
//                 $(".gst"). css('padding','10% 0% 0% 0%')
                $(".igst").hide();
            }

        });
    </script>





