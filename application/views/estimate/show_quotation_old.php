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
                <!--                <h1>
                                    Quotation
                                </h1>-->
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Quotation</a></li>
                    <li class="active">Quotation Details@@@@@@@@@@@@@@@@@@@@@@@@2</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <!--<div class="box">-->
                        <!--<div class="box-header">-->
                        <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>Quotation:<?php echo $estimates_data_group['number_fk']; ?></h2></label>
                        <!--</div>-->
                        <!-- /.box-header -->
                        <!--<div class="box-body">-->

                        <div class="row" style="padding:2%">
                            <div class="pull-left">

                                <div class="col-md-1">
                                    <a href="<?php echo base_url(); ?>EstimateController/edit_estimate_details/<?php echo $estimates_data_group['id']; ?>" class="btn btn-primary" role="button">Edit</a>
                                </div>
                                <div class="col-md-3">
                                    <form method="POST" action="<?php echo base_url(); ?>EstimateController/convert_to_sales_order/<?php echo $estimates_data_group['id']; ?>">
                                        <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="<?php echo $estimates_data_group['id']; ?>">  
                                        
                                        
                                         <?php
                                                if (date('m') <= 3) {//Upto June 2014-2015
                                                    $financial_year = strtoupper(date('M')) . "/" . (date('y') - 1) . '-' . date('y');
                                                } else {//After June 2015-2016
                                                    $financial_year = strtoupper(date('M')) . "/" . date('y') . '-' . (date('y') + 1);
                                                }
                                        ?>
                                        
                                        <input type="hidden" class="form-control input-sm"   name="salesorder_number" id="salesorder_number" required="" value="SO/<?php printf("%04d", $salesorder_id['COUNT(uid)'] + 1); ?>/<?php echo $financial_year; ?>">
                                        <button id="convertToSalesorder" class="btn btn-primary" role="button" type="submit">Convert to Sales Order</button>
                                    </form>
                                </div>
                                <div class="col-md-2">
                                    <form method="POST" action="<?php echo base_url(); ?>EstimateController/convert_to_invoice/<?php echo $estimates_data_group['id']; ?>">
                                        <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="<?php echo $estimates_data_group['id']; ?>">  
                                        
                                        
                                         <?php
                                                if (date('m') <= 3) {//Upto June 2014-2015
                                                    $financial_year = strtoupper(date('M')) . "/" . (date('y') - 1) . '-' . date('y');
                                                } else {//After June 2015-2016
                                                    $financial_year = strtoupper(date('M')) . "/" . date('y') . '-' . (date('y') + 1);
                                                }
                                        ?>
                                        
                                        <input type="hidden" class="form-control input-sm"   name="invoice_number" id="invoice_number" required="" value="INV/<?php printf("%04d", $invoice_id['COUNT(uid)'] + 1); ?>/<?php echo $financial_year; ?>">
                                        <button id="convertToInvoice" class="btn btn-primary" role="button" type="submit">Convert to Invoice</button>
                                    </form>
                                </div>
                                
                                 <div class="col-md-1">
                                 </div>
                                 <div class="col-md-1">
                                    <form method="POST" action="<?php echo base_url(); ?>EstimateController/duplicate_quote">
                                        <input type="hidden" class="form-control input-sm"   name="id" id="id" required="" value="<?php echo $estimates_data_group['id']; ?>">  
                                         <button id="" class="btn btn-primary" role="button" type="submit">Duplicate</button>
                                    </form>
                                </div>
                                
                                <div class="pull-right">
                                    
                                    
                
                                       
                                  

                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                More <b class="caret"></b>
                                            </button>
                                            <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="<?php echo $estimates_data_group['number_fk']; ?>">  
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li class="hide"><a class="dropdown-item" href="<?php echo base_url(); ?>/LoginController/get_settings">Edit Business Information</a></li>
                                                <li><a class="dropdown-item hide_gst" id="exportpdf" href="<?php echo base_url(); ?>Pdf/print_igst_quote/<?php echo $estimates_data_group['id']; ?>">Export As PDF</a></li>
                                                 <li><a class="dropdown-item hide_igst" id="exportpdf" href="<?php echo base_url(); ?>Pdf/print_igst_quote/<?php echo $estimates_data_group['id']; ?>">Export As PDF</a></li>
                                              
                                            </ul>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="shadows1" >
                            <div class="row">
                                <section class="contemporary-template__header">
                                    <div class="col-md-6">
                                        <div class="contemporary-template__header__logo">
                                            <center><img class="contemporary-template__business-logo" src="<?php echo base_url() . $settings['company_logo'] ?>" width="30%" height="15%" style="margin-top:26px;"></center>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="contemporary-template__header__info">
                                            <div class="wv-heading--title"><h1>QUOTATION</h1></div>
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

                                <div class="col-md-6">

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
                                            <span class="wv-text--strong"><b>Customer Name :</b> <?php echo $estimates_data_group['fullname']; ?></span>
                                                
                                           
                                        </div>
                                </div>

                                <div class="col-md-6">
                                   
                                    <div class="contemporary-template__header__info">

                                            <div class="wv-heading--subtitle"><b>Quotation Details :</b></div>
                                            <br>
                                            <span class="wv-text--strong"><b>Quotation Number :</b> <?php echo $estimates_data_group['number_fk']; ?></span><br>
                                            <span class="wv-text--strong"><b>Quotation Date :</b> <?php echo date('d-m-Y', strtotime($estimates_data_group['date'])); ?></span><br>
                                            <span class="wv-text--strong"><b>Expires on :</b> <?php echo $estimates_data_group['exp_date']; ?></span><br>
                                            <span class="non_gst_total hide"><label class="control-label non_gst_total"><b>Grand Total (INR): </b></label><?php echo number_format($estimates_data_group['basic_total'], 2); ?></span>
                                            <span class="gst_total hide"> <label class="control-label gst_total"><b>Grand Total (INR): </b></label><?php echo number_format($estimates_data_group['total'], 2); ?></span>
                                            <span class="igst_total hide"> <label class="control-label igst_total"><b>Grand Total (INR): </b></label><?php echo number_format($estimates_data_group['total'], 2); ?></span>
                                           <span class="wv-text--strong "><b>Enquiry : </b>
                            <?php if ($estimates_data_group['enquiry'] == '1') { 
                             echo "By Mail";
                            } else if(($estimates_data_group['enquiry'] == '2')) { 
                                echo "By Verbal";
                             } else if(($estimates_data_group['enquiry'] == '3')) { 
                                echo "Just Dial";
                             } else if(($estimates_data_group['enquiry'] == '4')) { 
                                echo "India Mart";
                            }
                            ?></span><br>

                                                
                                           
                                        </div>
                                    

                                </div>
                            </div>
                            <br>
                            <div class="table-responsive">  

                                <table class="table table-bordered" id="dynamic_field">  
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Description</th>
                                        <th>QTY</th>
                                        <th>HSN Code</th>
                                        <th class="gst_per">TAX(%)</th>
                                        <th class="gst">SGST</th>
                                        <th class="gst">CGST</th>
                                        <th class="igst">IGST</th>
                                        <th>Price/Unit</th>
                                        <th>Discount(%)</th>
                                        <th>Amount</th>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    $colspan = 0;
                                    
                                    foreach ($show_quotation as $key) {
                                        ?>
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
                                                <?php echo $key->quantity; ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" value="1" />
                                            </td>
                                            <td><span id="" class=""></span>
                                                <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                                            </td>

                                            <?php if ($key->gst_type != 'I') { $colspan = 10 ; ?>

                                                <td class="gst"><span id="" class=""></span>
                                                    <input type="hidden" name="gst" value="gst" id="gst">
                                                    <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                                </td>

                                                <td class="gst"><span id="" class=""></span>
                                                    <?php echo number_format($key->sgst, 2); ?><input type="hidden" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" />
                                                </td>

                                                <td class="gst"><span id="" class=""></span>
                                                    <?php echo number_format($key->cgst, 2); ?><input type="hidden" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" />
                                                </td>

                                            <?php } else if ($key->gst_type != 'S') { echo "<br>iiiii" .  $colspan = 10; ?>

                                                <td class="igst"><span id="" class=""></span>
                                                    <input type="hidden" name="igst" value="igst" id="igst">
                                                    <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
                                                </td>

                                                <td class="igst"><span id="" class=""></span>
                                                    <?php echo number_format($key->igst, 2); ?><input type="hidden" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" />
                                                </td>

                                            <?php } ?>

                                        <td><span id="" class=""></span>
                                            <?php echo number_format($key->price, 2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control input-sm name_list"/>
                                        </td>

                                        <td><span id="" class=""></span>
                                            <?php echo $key->discount; ?><input type="hidden" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list"/> %
                                        </td>

                                        <td><span id="" class=""></span>
                                            <?php echo number_format($key->amount, 2); ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>

                                        </td>
                                        </tr>  
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                        
                                         <tr class="">
                                            <td colspan="<?php echo $colspan; ?>"  class="text-right">
                                                Total Before Tax: ₹ <?php echo number_format($estimates_data_group['basic_total'], 2); ?>
                                            </td>
                                            
                                        </tr>
                                        
                                        
                                        
                                        
                                        <tr class="gst">
                                            <td colspan="<?php echo $colspan; ?>"  class="text-right">
                                                <span id="sgst_amount" name="sgst_amount"><b>SGST Amount: </b>₹0.00</span><br>
                                            </td>
                                            
                                        </tr>
                                          <tr class="gst">
                                            <td colspan="<?php echo $colspan; ?>"  class="text-right">
                                                <span id="cgst_amount" name="cgst_amount"><b>CGST Amount:</b> ₹0.00</span><br>
                                            </td>
                                        </tr>
                                        
                                          <tr>
                                            <td colspan="<?php echo $colspan; ?>"  class="text-right">
                                                <span class="igst igst_edit_hide_show" id="igst_amount" name="igst_amount">1IGST Amount: ₹0.00</span>
                                            </td>
                                        </tr>
                                         
                                        <tr>
                                            <td colspan="<?php echo $colspan; ?>"  class="text-right"><b> <span>Grand Total: <?php echo number_format($estimates_data_group['total'], 2); ?></span></b></td>
                                        </tr>
                                </table>   

                                <div align="right" style="margin: 10px">

<!--                                    <b> <span>Grand Total: <?php echo number_format($estimates_data_group['total'], 2); ?></span></b><br>-->
                                     <!--<span id="total_amount" name="total_amount">Total: ₹0.00</span><br>-->
                                    <span class="hide" id="sgst_amount" name="sgst_amount">SGST Amount: ₹0.00</span><br>
                                    <span class="hide" id="cgst_amount" name="cgst_amount">CGST Amount: ₹0.00</span><br>
                                    
                                </div>

                                <label class="control-label pull-left"><b>Notes</b></label><br>

                                <div class="col-sm-12">
                                    <textarea style="overflow: auto;
                                              border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $settings['notes']; ?></textarea><br>
                                </div>

                                <div class="form-group row ">
                                    <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Sign :</b></label>
                                    <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Sign :</b></label>
                                </div>

                                <center style="font-size: 12px"><?php echo $settings['quotation_footer']; ?></center>
                                <center style="font-size: 10px">This is Computer Generated Invoice</center><br>

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

