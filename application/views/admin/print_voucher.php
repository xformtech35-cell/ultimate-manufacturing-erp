<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title> Payment Voucher </title>
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
                height: 100px;
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
            <caption style="font-size: 1.3em;"> Payment Voucher</caption>
            <tr>
                <td colspan="2"  valign="top">
           
            <center>  <b style="font-size: 1.8em;"><?php echo $settings['company_name']; ?></b></center>
            </td>
            </tr>
            
             <tr>
                 <th>Particulars</th>
                <th>Amount</th>
                
            </tr>
            <tr>
                <td>
                     <?php echo $show_voucher_data['company_name'] ?>
                </td>
                <td> <?php echo $show_voucher_data['invocie_pay_amount'] ?></td>
            </tr>
            
            <tr>
                <td>
                    Ref. <?php echo $show_voucher_data['invoice_number_fk'] ?> &nbsp; &nbsp; &nbsp;<?php echo $show_voucher_data['invoice_pay_date'] ?> &nbsp; &nbsp; &nbsp;<?php echo $show_voucher_data['invocie_pay_amount'] ?>
                </td>
                <td> </td>
            </tr>
              <tr>
                <td>
                     Through -  <?php echo $show_voucher_data['bank_name'] ?>
                </td>
                <td> </td>
            </tr>
                <tr>
                <td>
                    
                     Amount in words -  <?php   // include amount_convert
                      require( APPPATH . '/third_party/amount_convert.php'); 
                          echo number_to_word($show_voucher_data['invocie_pay_amount']) .  ' Only';     
                              
                              ?>
                </td>
                <td> </td>
            </tr>
             <tr>
                 <td>Total </td>
                <td>
                       <?php echo $show_voucher_data['invocie_pay_amount'] ?>
                </td>
                
            </tr>
            <tr>
                <td colspan="2"><center style="font-size: 10px">This is Computer Generated Voucher</center></td>
            </tr>
        </table> 

        
        
        
        
        
    </body>
</html>