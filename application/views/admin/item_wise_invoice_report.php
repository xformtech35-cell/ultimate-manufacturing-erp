<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title> Invoice </title>
        <meta name="description" content="Invoice print page">
        <meta name="viewport" content="width=device-width">
        <link href="<?php echo base_url(); ?>printme/bootstrap.min.css" rel="stylesheet" >
        <link href="<?php echo base_url(); ?>printme/main.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>printme/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>printme/jquery-printme.js"></script>

        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #000000;
                text-align: left;
                padding: 5px;
            }

        </style>
    </head>
    <body>

        <table  border="2">
            <caption style="font-size: 1.3em;">Vtech Solution</caption>
        </table>
            <b style="font-size: 1.2em;">ITEMWISE INVOICE REGISTER FROM <?php echo $from_date ?> TO <?php echo $to_date ?></b>
        <table> 
            
                <tr>
                    <th style="background-color: #444444; color: white">Part Description</th>
                    <th style="background-color: #444444; color: white">Quantity</th>
                    <th style="background-color: #444444; color: white">Amount</th>
                </tr>

            <?php
            $grand_total = 0;
            $i = 1;
           
            foreach ($item_wise_report as $key) {
                ?>
                
                <tr> 

                    <td>
                        <b><?php echo $key->company_name; ?></b><br>
                        <?php echo $key->product_name; ?>
                    </td>

                    <td>
                        <?php echo number_format($key->quantity); ?>
                    </td>
                    
                    <td  class="text-right">
                        <?php if($key->gst_type !="I"){ ?>
                        <?php $grand_total = $key->total + $key->sgst + $key->cgst;  echo number_format($grand_total, 2); ?>
                    <?php }else{ ?>
                        <?php $grand_total = $key->total + $key->igst;  echo number_format($grand_total, 2); ?>
                    <?php }?>
                    </td>
                    
                </tr>  
                <?php
                $i++;
            }
            ?>

                 <?php
            $i = 1;
            foreach ($item_wise_report as $key) {
                ?>

                    <tr>
                        <td class="text-right">
                            <label class="control-label"><b>Grand Total (INR) : </b></label>
                        </td>
                         <td>
                            <?php echo number_format($key->quantity); ?>
                        </td>
                        <td  class="text-right">
                            <?php echo number_format($grand_total, 2); ?>
                        </td>

                    </tr>
                

                <?php
                $i++;
                break;
            }
            ?>
                
                
        </table> 

    </body>
</html>