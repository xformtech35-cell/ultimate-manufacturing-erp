<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title> Quotation </title>
        <meta name="description" content="Quotation print page">
        <meta name="viewport" content="width=device-width">
<!--        <link href="<?php echo base_url(); ?>printme/bootstrap.min.css" rel="stylesheet" >-->
<!--        <link href="<?php echo base_url(); ?>printme/main.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>printme/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>printme/jquery-printme.js"></script>-->
<style>
      table{
                            width: 100%;
                            margin: 2px;
                            font-size:10px; 
                            padding: 2px;
                        }
                       
    #dynamic_field {
  border-collapse: collapse ;
   border-width: 1px;
}

.text-right{
    text-align: right;
}
    </style>

    <body>
    <center><table border="1" cellpadding="2" id="dynamic_field">  
            <tr>
                <td colspan="5">
            <center> <img src="<?php echo base_url() . $settings['company_logo'] ?>" width="30%" height="15%"></center>
            </td>
            <td colspan="5">
                <div class="wv-heading--title"><h3>QUOTATION</h3></div>
                <div class="wv-heading--subtitle"> <?php echo $settings['quotation_subheading']; ?></div>
                <span class="wv-text--strong" style="font-size: 1.2em;"><b><?php echo $settings['company_name']; ?></b></span><br>
                <span class="wv-text--strong"><b>GST Number : </b><?php echo $settings['company_gst']; ?></span><br>
                <span class="wv-text--strong"><b>PAN Number : </b><?php echo $settings['company_pan']; ?></span><br>
                <span class="wv-text--strong"><b>Mobile Number : </b><?php echo $settings['mobile']; ?></span><br>
                <span class="wv-text--strong"><b>Email ID : </b><?php echo $settings['email']; ?></span><br>
                <span class="wv-text--strong"><b>Address : </b><?php echo $settings['address']; ?></span>
            </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="contemporary-template__header__info">

                        <div class="wv-heading--subtitle"><b>Buyer</b></div>
                        <span class="wv-text--strong" style="font-size: 1.1em;"><b><?php echo isset($estimates_data_group['company_name']) && !empty($estimates_data_group['company_name']) ? $estimates_data_group['company_name'] : (isset($estimates_data_group['fullname']) ? $estimates_data_group['fullname'] : ''); ?></b></span><br>
                        <?php if (!empty($estimates_data_group['address'])): ?>
                        <span class="wv-text--strong"><b>Address : </b><?php echo $estimates_data_group['address']; ?></span><br>
                        <?php endif; ?>
                        <?php if (!empty($estimates_data_group['gst'])): ?>
                        <span class="wv-text--strong"><b>GST Number : </b><?php echo $estimates_data_group['gst']; ?></span><br>
                        <?php endif; ?>
                        <?php if (!empty($estimates_data_group['pancard'])): ?>
                        <span class="wv-text--strong"><b>PAN Number : </b><?php echo $estimates_data_group['pancard']; ?></span><br>
                        <?php endif; ?>
                        <?php if (!empty($estimates_data_group['mobile'])): ?>
                        <span class="wv-text--strong"><b>Mobile Number : </b><?php echo $estimates_data_group['mobile']; ?></span><br>
                        <?php endif; ?>
                        <?php if (!empty($estimates_data_group['email'])): ?>
                        <span class="wv-text--strong"><b>Email ID : </b><?php echo $estimates_data_group['email']; ?></span><br>
                        <?php endif; ?>
                        
                        <?php 
                        $session_data_head1 = $this->session->userdata('session_data_head');
                        $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
                        if ($_has_project_master): 
                        ?>
                        <span class="wv-text--strong"><b>Project Code :</b> <?php echo isset($estimates_data_group['project_code']) ? $estimates_data_group['project_code'] : ''; ?></span><br>
                        <?php endif; ?>
                        <span class="wv-text--strong"><b>PO Number :</b> <?php echo isset($estimates_data_group['po_number']) ? $estimates_data_group['po_number'] : ''; ?></span><br>
                    </div>
                </td>
                <td colspan="5">
                    <div class="contemporary-template__header__info">
                        <div class="wv-heading--subtitle"><b>Quotation Details</b></div>
                        <span class="wv-text--strong"><b>Quotation Number :</b> <?php echo $estimates_data_group['number_fk']; ?></span><br>
                        <span class="wv-text--strong"><b>Quotation Date :</b> <?php echo $estimates_data_group['date']; ?></span><br>
                        <span class="wv-text--strong "><b>Expires on : </b><?php echo $estimates_data_group['exp_date']; ?></span><br>
                        <span class="wv-text--strong "><b>Enquiry : </b>
                            <?php
                            if ($estimates_data_group['enquiry'] == '1') {
                                echo "By Mail";
                            } else if (($estimates_data_group['enquiry'] == '2')) {
                                echo "By Verbal";
                            } else if (($estimates_data_group['enquiry'] == '3')) {
                                echo "Just Dial";
                            } else if (($estimates_data_group['enquiry'] == '4')) {
                                echo "India Mart";
                            }
                            ?></span><br>
                    </div>
                </td>
            </tr>

            <tr>
                <th nowrap>No</th>
                <th>Description</th>
                <th>HSN Code</th>
                <th nowrap>Qty No/Kg</th>
                <th>Unit</th>
                <th class="gst_per">GST %</th>
                <th class="gst">SGST</th>
                <th class="gst">CGST</th>
                 <th>Discount</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>

            <?php
            $i = 1;
            $sgst_total_amt = 0;
            $igst_total_amt = 0;
            foreach ($show_quotation as $key) {
                ?>
                <?php $sgst_total_amt = $sgst_total_amt + $key->cgst; ?>
                <?php $igst_total_amt = $igst_total_amt + $key->igst; ?>

                <tr> 
                    <td><span id="" class=""></span>
                        <?php echo $i; ?>
                    </td>

                    <td width="30%"> <b><?php echo $key->product_name; ?></b><br>
                                <?php echo $key->description; ?>                            
                            </td>

                            <td><span id="" class=""></span>
                                <?php echo $key->hsn_code; ?>
                            </td>
                            
                            <td><span id="" class=""></span>
                                <?php echo $key->quantity; ?>                            
                            </td>

                            <td><?php echo $key->unit ?? ''; ?></td>

                            <?php if ($key->gst_type != 'I') { ?>
                                <td  class="gst"><span id="" class=""></span>

                                    <?php echo $key->gst; ?>
                                </td>
                                <td  class="gst"><span id="" class=""></span>
                                    <?php echo number_format($key->sgst, 2); ?>
                                </td>
                                <td class="gst"><span id="" class=""></span>
                                    <?php echo number_format($key->cgst, 2); ?>
                                </td>
                            <?php } elseif ($key->gst_type != 'S') { ?>

                                <td class="igst"><span id=""></span>

                                    <?php echo $key->gst; ?>
                                </td>
                                <td class="igst"><span id=""></span>
                                    <?php echo number_format($key->igst, 2); ?>
                                </td>

                            <?php } ?>

                             <td> <?php echo $key->discount; ?> %
                                </td>  
                            <td><span id=""></span>
                                <?php echo number_format($key->price, 2); ?>
                            </td>
                            <td><span id="" class=""></span>
                                <?php echo number_format($key->amount, 2); ?>
                            </td>
                </tr>  
                
                <?php
                $i++;
            }
            ?>
                
                    <tr class="">
                                            <td colspan="10"  class="text-right">
                                                <b>Total Before Tax: </b>
                                            </td>
                                            
                                             <td colspan="1"  class="text-left">
                            <?php echo number_format($estimates_data_group['basic_total'], 2); ?>
                        </td>                 
                   </tr>

            <?php
            $i = 1;
            foreach ($show_quotation as $key) {
                ?>


                <?php if ($key->gst_type != 'I') { ?>

                    <tr>
                        <td colspan="10"  class="text-right">
                            <label class="control-label"><b>CGST Amount : </b></label>
                        </td>

                        <td colspan="1"  class="text-left">
                            <?php echo number_format($sgst_total_amt, 2); ?><br>
                        </td>

                    </tr>

                    <tr>
                        <td colspan="10"  class="text-right">
                            <label class="control-label"><b>SGST Amount : </b></label>
                        </td>
                        <td colspan="1"  class="text-left">
                            <?php echo number_format($sgst_total_amt, 2); ?><br>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="10"  class="text-right">
                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                        </td>

                        <td colspan="1"  class="text-left">
                            <?php echo number_format($estimates_data_group['total'], 2); ?><br>
                        </td>
                    </tr>

                <?php } else if ($key->gst_type != 'S') { ?>

                    <tr>
                        <td colspan="10"  class="text-right">
                            <label class="control-label"><b>IGST Amount : </b></label>
                        </td>

                        <td colspan="1"  class="text-left">
                            <?php echo number_format($igst_total_amt, 2); ?><br>
                        </td>

                    </tr>
                    <tr> 

                        <td colspan="10"  class="text-right">
                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                        </td>

                        <td colspan="1"  class="text-left">
                            <?php echo number_format($estimates_data_group['total'], 2); ?><br>
                        </td>

                    </tr>
                <?php } else if (($key->gst_type != 'I') || ($key->gst_type != 'S')) { ?>
                    <tr>
                        <td colspan="10"  class="text-right">
                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                        </td>

                        <td colspan="1"  class="text-left">
                            <?php echo number_format($estimates_data_group['basic_total'], 2); ?><br>
                        </td>

                    </tr>
                <?php } ?>

                <?php
                $i++;
                break;
            }
            ?>  
                    
             <tr>
                        <td colspan="11"  class="text-right">
                            <?php
                            // include amount_convert
                            require( APPPATH . '/third_party/amount_convert.php');
                            ?>
                            <label class="control-label"><b>Grand Total in Words : <?php echo number_to_word($estimates_data_group['total']); ?> Only</label>
                        </td>
                    </tr>       
                    
                    
                    
                    
            <tr>
                <th colspan="11" style="text-align: left">Bank Details:</th>
            </tr>
            <tr>

                <td colspan="11">                   
                    <?php echo $settings['invoice_notes']; ?>
                </td>
            </tr>
            <tr>
                <th colspan="11" style="text-align: left">Note:</th>
            </tr>
            <tr>
                <td colspan="11">                   
                    <?php echo $settings['quotation_memo']; ?>
                </td>
            </tr>

            <tr>
                <th colspan="11" style="text-align: left">Terms & Conditions:</th>
            </tr>

            <tr>
                <td colspan="11"><?php echo $estimates_data_group['terms_and_conditions']; ?></td>
            </tr>
            <tr>
                <th colspan="11" style="text-align: left">Payment Terms:</th>
            </tr>
            <tr>
                <td colspan="11"><?php echo $estimates_data_group['payment_terms']; ?></td>
            </tr>
            <tr>
                <th colspan="11" style="text-align: left"> Process Schedule:</th>
            </tr>
            <tr>
                <td colspan="11" style="text-align: left"><?php echo $estimates_data_group['process_schedule']; ?></td>
            </tr>
            <tr>
                <th colspan="11" style="text-align: left"> Taxes:</th>
            </tr>
            <tr>
                <td colspan="11"><?php echo $estimates_data_group['taxes']; ?></td>
            </tr>
            <tr>
                <th colspan="11" style="text-align: left"> Exclusions:</th>
            </tr>
            <tr>
                <td colspan="11"><?php echo $estimates_data_group['exclusions']; ?></td>
            </tr>
            <tr>
                <td colspan="5">
                    <label for="inputEmail3" class="col-sm-3 control-label"><b>Prepared By :</b> <br> <?php echo $estimates_data_group['prepare_by']; ?></label>
                </td>

                <td colspan="5">
                    <label for="inputEmail3" class="col-sm-9 control-label"><b> Approved By :</b> <br> <?php echo isset($estimates_data_group['approved_by']) ? $estimates_data_group['approved_by'] : ''; ?></label>
                </td>

            </tr>
            <tr>
                <td colspan="11"><center style="font-size: 12px"><?php echo $settings['quotation_footer']; ?></center></td>
            </tr>
            <tr>
                <td colspan="11"><center style="font-size: 10px">This is Computer Generated Quotation</center></td>
            </tr>
        </table>
    </center>
    <br>

</body>
<script>

    $(document).ready(function () {

        var igst = $("#igst").val();
        alert(igst);
        if (igst == "igst") {
            $(".gst").hide();
            $(".igst").show();
        } else {
            $(".gst").show();
            $(".igst").hide();
        }

    });
</script>
</html>