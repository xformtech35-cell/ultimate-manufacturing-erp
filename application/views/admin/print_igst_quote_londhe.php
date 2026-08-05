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
    table {
        width: 100%;
        margin: 2px;
        font-size: 9px;
        padding: 2px;
    }

    #dynamic_field {
        border-collapse: collapse;
        border-width: 1px;
    }

    .text-right {
        text-align: right;
    }
    </style>

<body>

    <?php
        foreach ($show_quotation as $key) {

           
               
                $colspan = "10";
                $colspan1 = "2";
                $colspan2 = "3";
                $colspan3 = "5";
                $colspan4 = "1";



                $colspan5 = "3";
                $colspan6 = "3";
                $colspan7 = "3";
                $colspan8 = "8";
                
       
            break;
        }
        
        ?>

    <center>

        <table border="1" id="dynamic_field">

            <tr>
                <td colspan="<?php echo $colspan; ?>">
                    <center>
                        <div class="wv-heading--title">
                            <h3 style="color:red">Quotation</h3>
                        </div>
                    </center>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan1; ?>">
                    <div class="wv-heading--subtitle"><b>Vendor Name & Address</div>
                </td>
                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>Reference No. 2102</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <div class="wv-heading--subtitle"><b>Dated: <?php
                    $originalDate = $estimates_data_group['date'];
                    $newDate = date("d/m/Y", strtotime($originalDate));
                     echo $newDate; ?></div>
                </td>
            </tr>

            <tr>
                <td colspan="<?php echo $colspan1; ?>">
                    <div class="wv-heading--subtitle"><b><?php echo $settings['company_name']; ?></div>
                </td>
                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>Quotation No.</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                <b> <?php echo $estimates_data_group['number_fk']; ?></b>
                </td>
            </tr>


            <tr>
                <td colspan="<?php echo $colspan1; ?>" rowspan="8">
                    <div class="wv-heading--subtitle"><b><?php echo $settings['address']; ?></div>
                </td>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>Email</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $settings['email']; ?>
                </td>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>PAN No.</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $settings['company_pan']; ?>
                </td>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>GST No.</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $settings['company_gst']; ?>
                </td>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>State Code</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $settings['state_code']; ?>
                </td>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>Contact No.</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $settings['mobile']; ?>
                </td>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>LUT/ARN</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $settings['cin']; ?>

                </td>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>MSME No.</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">

                </td>
            </tr>

            <tr>
                <td colspan="<?php echo $colspan1; ?>">
                    <div class="wv-heading--subtitle"><b>Buyer Name & Address</div>
                </td>
                <td colspan="<?php echo $colspan8; ?>">

                </td>
            </tr>





            <tr>
                <td colspan="<?php echo $colspan1; ?>">
                    <span class="wv-text--strong"><b> <?php echo $estimates_data_group['company_name']; ?></b></span>
                </td>
                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>State Code</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $estimates_data_group['state_code']; ?>
                </td>
            </tr>



            <tr>
                <td colspan="<?php echo $colspan1; ?>" rowspan="6">
                    <div class="wv-heading--subtitle"><b><?php echo $estimates_data_group['address']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>Place of Supply</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    Pune Maharashtra
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>GST No.</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $estimates_data_group['gst']; ?>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>PAN No.</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $estimates_data_group['pancard']; ?>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>Contact No.</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $estimates_data_group['mobile']; ?>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan2; ?>">
                    <div class="wv-heading--subtitle"><b>Email</div>
                </td>
                <td colspan="<?php echo $colspan3; ?>">
                    <?php echo $estimates_data_group['email']; ?>
                </td>
            </tr>



            <tr>
                <th rowspan="2">Sr No.</th>
                <th rowspan="2">Description of Good/Services</th>
                <th rowspan="2">Make</th>
                <th rowspan="2">HSN/SAC</th>
                <th rowspan="2">Qty</th>
                <th rowspan="2">Unit</th>
                <th colspan="2">Unit<br>Rate</th>
                <th rowspan="2">Total<br>Cost</th>
                <th rowspan="2">Total Amount</th>
            <tr>

                <th>Supply Rate</th>
                <th>Installation Rate</th>
            </tr>

            <?php
            $i = 1;
            foreach ($show_quotation as $key) {
                ?>
            <tr>
                <td>
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
                <td><span id="" class=""></span>
                    <?php echo number_format($key->amount, 2); ?>
                </td>
                <td><span id="" class=""></span>
                    <?php echo number_format($key->amount, 2); ?>
                </td>
                <td><span id="" class=""></span>
                    <?php echo number_format($key->amount, 2); ?>
                </td>
                <td><span id="" class=""></span>
                    <?php echo number_format($key->amount, 2); ?>
                </td>
                <td><span id="" class=""></span>
                    <?php echo number_format($key->amount, 2); ?>
                </td>
                <td><span id="" class=""></span>
                    <?php echo number_format($key->amount, 2); ?>
                </td>

            </tr>
            <?php
                $i++;
            }
            ?>


<tr>
    <td rowspan=4 colspan=3>
    <?php
                            // include amount_convert
                            require( APPPATH . '/third_party/amount_convert.php');
                            ?>
                    <label class="control-label"><b>Amount (in Words) :
                            <?php echo number_to_word($estimates_data_group['basic_total']); ?> Only</label>
      </td>
      <td class="text-right" colspan=6>
        Total
      </td>
      <td>
      <?php echo number_format($estimates_data_group['basic_total'], 2); ?>
      </td>
     
    </tr>
    <tr>
      <td class="text-right" colspan=6>
        IGST(0%)
      </td>
      <td>
        
      </td>
    </tr>
    <tr>
      <td class="text-right" colspan=6>
       Round Off
      </td>
      <td>
     <b> <?php echo number_format($estimates_data_group['basic_total'], 2); ?>  </b>
      </td>
    </tr>
    <tr>
      <td class="text-right" colspan=6>
        Grand Total Amount
      </td>
      <td>
      <b>  <h3><?php echo number_format($estimates_data_group['basic_total'], 2); ?> </h3></b>
      </td>
    </tr>

    <tr>
      <td class="text-left" colspan=10>
       Under SEZ: Supply to SEZ units or SEZ developer for authorized operation under bond or latter of undertaking without payment of IGST
      </td>
    </tr>

    <tr>
      <td class="text-right" colspan=10>

      </td>
    </tr>


    <tr>
                <th colspan="<?php echo $colspan; ?>" style="text-align: left"> Bank Details:</th>
            </tr>
            <tr>

                <td colspan="<?php echo $colspan; ?>">
                    <?php echo $settings['invoice_notes']; ?>
                </td>
            </tr>

            <?php if($estimates_data_group['quotation_memo']){?>
            <tr>
                <th colspan="<?php echo $colspan; ?>" style="text-align: left"> Note:</th>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>">
                    <?php echo $estimates_data_group['quotation_memo']; ?>
                </td>
            </tr>
            <?php } ?>
            <?php if($estimates_data_group['terms_and_conditions']){?>
            <tr>
                <th colspan="<?php echo $colspan; ?>" style="text-align: left"> Terms & Conditions:</th>
            </tr>

            <tr>
                <td colspan="<?php echo $colspan; ?>"><?php echo $estimates_data_group['terms_and_conditions']; ?></td>
            </tr>
            <?php } ?>
            <?php if($estimates_data_group['payment_terms']){?>
            <tr>
                <th colspan="<?php echo $colspan; ?>" style="text-align: left">Payment Terms:</th>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>"><?php echo $estimates_data_group['payment_terms']; ?></td>
            </tr>
            <?php } ?>
            <?php if($estimates_data_group['process_schedule']){?>
            <tr>
                <th colspan="<?php echo $colspan; ?>" style="text-align: left">Process Schedule:</th>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>"><?php echo $estimates_data_group['process_schedule']; ?></td>
            </tr>
            <?php } ?>
            <?php if($estimates_data_group['taxes']){?>
            <tr>
                <th colspan="<?php echo $colspan; ?>" style="text-align: left">Taxes:</th>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>"><?php echo $estimates_data_group['taxes']; ?></td>
            </tr>
            <?php } ?>
            <?php if($estimates_data_group['exclusions']){?>
            <tr>
                <th colspan="<?php echo $colspan; ?>" style="text-align: left">Exclusions:</th>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>"><?php echo $estimates_data_group['exclusions']; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="<?php echo $colspan1; ?>">
                <center><br> <br> <br> <br> <br> <br> <br> <br> <label for="inputEmail3" class="col-sm-3 control-label"><b>Receivers Signatory :</b></label></center>
                </td>

                <td colspan="<?php echo $colspan; ?>">
                <center> <img src="<?php echo base_url() . $settings['company_stamp'] ?>" width="100px" height="100px"></center>
                <label for="inputEmail3" class="col-sm-9 control-label"><b> Authorized Signatory :</b></label>
                </td>

            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>">
                    <center style="font-size: 12px"><?php echo $estimates_data_group['quotation_footer']; ?></center>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>">
                    <center style="font-size: 9px">This is Computer Generated Quotation</center>
                </td>
            </tr>


        </table>
    </center>
</body>



</html>