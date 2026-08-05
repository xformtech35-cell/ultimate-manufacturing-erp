<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Delivery Challan </title>
        <meta name="description" content="Invocie print page">
        <meta name="viewport" content="width=device-width">
<!--        <link href="<?php echo base_url(); ?>printme/bootstrap.min.css" rel="stylesheet" >-->
<!--        <link href="<?php echo base_url(); ?>printme/main.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>printme/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>printme/jquery-printme.js"></script>-->
     <style>
      table{
                            width: 100%;
                            margin: 2px;
                            font-size:9px;
                            padding: 2px;
                        }
                       
    #dynamic_field {
  border-collapse: collapse ;
   border-width: 1px;
}

.text-right{
    text-align: right;
}

table, td, th {
  border: 1px solid black;
}

table {
  border-collapse: collapse;
}
    </style>
    <body>
        
        
            <?php


        foreach ($show_invoice as $key) {

                $colspan = "4";
                $colspan1 = "2";
                $colspan2 = "2";
                $colspan3 = "1";
                $colspan4 = "1";
                $colspan5 = "2";
           
            break;
        }
        ?>

        <center><table border="1" cellpadding="2" id="dynamic_field">  
            <caption style="font-size: 1.3em;">Delivery Challan</caption>
            <tr>
                   <td colspan="<?php echo $colspan1; ?>"  valign="top">
            <center><img src="<?php echo base_url() . $settings['company_logo'] ?>" width="30%" height="15%"></center>
                </td>
                
                   <td colspan="<?php echo $colspan2; ?>"  valign="top">

                    <b style="font-size: 1.8em;"><?php echo $settings['company_name']; ?></b><br>
                    <b>Address : </b><?php echo $settings['address']; ?><br>
                    
                    <b>State Code : </b><?php echo $settings['state_code'] . " "; ?><br>
                    <b>GST Number : </b><?php echo strtoupper($settings['company_gst']) . " "; ?><br>
                    <b>PAN Number : </b><?php echo strtoupper($settings['company_pan']) . " "; ?><br>
                    <b>Mobile Number : </b><?php echo $settings['mobile'] . " "; ?><br>
                    <b>Email ID : </b><?php echo $settings['email']; ?><br>
                </td>
            </tr>
     
            <tr>
                <td colspan="<?php echo $colspan3; ?>"  valign="top">
                      <div class="form-group row ">
                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>BILL TO:</b></label>
                            </div>
                            
                            <div class="contemporary-template__header__info">
                                    <div class="wv-heading--subtitle"></div>
                                    <span class="wv-text--strong"><b>Company Name: </b>  <?php echo $invoice_data_group['company_name']; ?></span>
                                   <span class="wv-text--strong"> <?php echo $invoice_data_group['address']; ?></span><br>
                                    <span class="wv-text--strong"><b>State Code : </b> <?php if ($invoice_data_group['state_code']) { ?>
                                        <?php echo $invoice_data_group['state_code']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    <span class="wv-text--strong"><b>GST Number : </b> <?php if ($invoice_data_group['gst']) { ?>
                                        <?php echo $invoice_data_group['gst']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    <span class="wv-text--strong"><b>PAN Number : </b> <?php if ($invoice_data_group['pancard']) { ?>
                                        <?php echo $invoice_data_group['pancard']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    <span class="wv-text--strong"><b>KIND ATTN : </b> <?php echo $invoice_data_group['fullname']; ?></span><br>
                                    <span class="wv-text--strong"><b>Customer PO : </b> <?php if ($invoice_data_group['customer_po']) { ?>
                                        <?php echo $invoice_data_group['customer_po']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    <span class="wv-text--strong"><b>PO Date : </b> <?php if ($invoice_data_group['po_date']) { ?>
                                        <?php echo $invoice_data_group['po_date']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span><br>
                                    
                                     <span class="wv-text--strong"><b>Supplier Code : </b> <?php if ($invoice_data_group['supplier_code']) { ?>
                                        <?php echo $invoice_data_group['supplier_code']; ?>
                                    <?php } else { ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span>
                                </div>
                   
                </td>
                
                <td colspan="<?php echo $colspan4; ?>"  valign="top">
                    
                    <div class="form-group row ">
                                <div class="col-sm-12">
                                   <?php if(isset($invoice_data_group['shipping_address'])) { ?><label class="control-label"><b>SHIPPING TO: </b></label> <br> <?php } ?>
                                         <?php echo $invoice_data_group['shipping_address']; ?>
                                </div>
                            </div>
                </td>
                
                
                <td colspan="<?php echo $colspan5; ?>"  valign="top">
                  <div class="contemporary-template__header__info">
                                 <label class="control-label"><b>DC DETAILS:</b></label> 
                                 <br>
                                  <span class="wv-text--strong"><b>DC : </b> <?php echo $invoice_data_group['invoice_number']; ?></span><br>
                                    <span class="wv-text--strong"><b>DC Date : </b> <?php echo date('d-m-Y', strtotime($invoice_data_group['invoice_date'])); ?></span><br>
                                    <span class="wv-text--strong"><b>Delivery Note No : </b> <?php echo $invoice_data_group['delivery_note_no']; ?></span><br>
                                    <span class="wv-text--strong"><b>Delivery Date : </b> <?php echo $invoice_data_group['delivery_date']; ?></span><br>
                                    <span class="wv-text--strong"><b>Dispatch through : </b> <?php echo $invoice_data_group['despatch_through']; ?></span><br>
                                    <span class="wv-text--strong"><b>Vehicle No: </b> <?php echo $invoice_data_group['vehicle_no']; ?></span><br>
                                    <span class="wv-text--strong"><b>Payment mode: </b> <?php if ($invoice_data_group['payment_method'] == '1') { ?>
                                        <?php echo "By Cash"; ?>
                                    <?php } else if ($invoice_data_group['payment_method'] == '2') { ?>
                                        <?php
                                        echo "By Cheque";
                                    } else if ($invoice_data_group['payment_method'] == '3') {
                                        ?>
                                        <?php
                                        echo "By NetBanking";
                                    } else {
                                        ?>
                                        <?php
                                        echo "None Provided";
                                    }
                                    ?></span>
                                </div>
                </td>

            </tr>
            <tr>
           <td colspan="4">

           <table>
            <?php
            $i = 1;
            foreach ($show_invoice as $key) {
                ?>
               
                <tr>
                    <th style="width:5%">Sr.No.</th>
                    <th style="width:50%">Description</th>
                    <th style="width:10%">Qty</th>
                    <th style="width:10%">Unit</th>
                </tr>

                <?php
                $i++;
                break;
            }
            ?>

            <?php
            $i = 1;
            $sgst_total_amt = 0;
            $igst_total_amt = 0;
            $amt = 0;
            $total_qty = 0;
            $formatted_total_qty = '0';
            foreach ($show_invoice as $key) {
                ?>
                <?php $sgst_total_amt = $sgst_total_amt + $key->cgst; ?>
                <?php $igst_total_amt = $igst_total_amt + $key->igst; ?>
                <?php $total_qty += floatval($key->quantity); ?>

                <tr> 
                    <td><span id="" class=""></span>
                        <?php echo $i; ?>
                    </td>

                    <td width="30%"><span id="" class=""></span>
                        <b><?php echo $key->product_name; ?></b><br>
                        <?php echo $key->description; ?>
                    </td>

                    <td><span id="" class=""></span>
                        <?php echo number_format($key->quantity); ?>
                    </td>

                    <td><span id="" class=""></span>
                        <?php echo $key->unit; ?>
                    </td>

                 
                </tr>  
                <?php
                $i++;
            }

            $formatted_total_qty = (fmod($total_qty, 1.0) == 0.0)
                ? number_format($total_qty, 0)
                : number_format($total_qty, 2);
            ?>

            <tr>
                <td colspan="2" class="text-right"><b>Total Qty:</b></td>
                <td colspan="2"><b><?php echo $formatted_total_qty; ?></b></td>
            </tr>
            </table> 


        </td>
        </tr>
                    
            
             <tr>
                <td colspan="<?php echo $colspan; ?>"><center style="font-size: 12px"><?php echo $settings['address']; ?></center></td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>"><center style="font-size: 10px">This is Computer Generated Invoice</center></td>
            </tr>
        </table> 
      
    </body>
</html>