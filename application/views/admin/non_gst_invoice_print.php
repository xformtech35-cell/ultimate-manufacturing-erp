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
                text-align: left;
            }
            .elc-font{
                font-size: 10px;
            }
            
            @page :right {
	margin-top: 0.5cm;
	margin-bottom: 0.5cm;
}


        </style>
    </head>
    <body>
        <table class="table table-striped">
            <tr>
                <td style="width:40%">
                    <img src="<?php echo base_url() . $settings['company_logo'] ?>" width="17%" height="17%">
                </td>

                <td class="text-right" colspan="2">
                    <h5>TAX INVOICE</h5>

                    <b><?php echo $settings['company_name']; ?></b><br>
                    <b>GST Number:</b><?php echo strtoupper($settings['company_gst']); ?><br>
                    <b>PAN Number:</b><?php echo $settings['company_pan']; ?><br>
                    <b>Mobile Number:</b><?php echo $settings['mobile']; ?><br>
                    <b>Email ID:</b><?php echo $settings['email']; ?><br>
                    <b>Address:</b>
                    <?php
                    $pieces = explode(',', $settings['address']);
                    foreach ($pieces as $part) {
                        echo $part . " ";
                    }
                    ?>
                </td>
            </tr>
        </table>
        <hr>
        <table class="table table-striped">
            <tr>
                <td>
                    <span class="wv-text--strong"><b>BILL TO:</b></span><br>
                    <?php echo $ng_invoice_data_group['company_name']; ?>
                    <br>
                    <?php echo $ng_invoice_data_group['address']; ?>
                    <br>
                    <span class="wv-text--strong"><b>GST No:</b><?php if ($ng_invoice_data_group['gst']) { ?>
                            <?php echo $ng_invoice_data_group['gst']; ?>
                        <?php } else { ?>
                            <?php
                            echo "None Provided";
                        }
                        ?>
                    </span>
                    <br>
                    <span class="wv-text--strong"><b>PAN No:</b><?php if ($ng_invoice_data_group['pancard']) { ?>
                            <?php echo $ng_invoice_data_group['pancard']; ?>
                        <?php } else { ?>
                            <?php
                            echo "None Provided";
                        }
                        ?>
                    </span>
                    <br>
                    <b> Customer Name:  <?php echo $ng_invoice_data_group['fullname']; ?></b>

                </td>

                <td class="text-right">
                    <label class="control-label"><b>Invoice Number:</b></label><?php echo $ng_invoice_data_group['invoice_number']; ?> <br>
                    <label class="control-label"><b>Invoice Date:</b></label><?php echo date('d-m-Y', strtotime($ng_invoice_data_group['invoice_date'])); ?> <br>
                    <label class="control-label"><b>Grand Total (INR): </b></label><?php echo number_format($ng_invoice_data_group['total'], 2); ?> <br>
                </td>
            </tr>
        </table>               

        <table class="table table-striped table-bordered" > 
            <tr>
                <th style="background-color: #444444; color: white">No</th>
                <th style="background-color: #444444; color: white; width: 30%">Item</th>
                <th style="background-color: #444444; color: white">Description</th>
                <th style="background-color: #444444; color: white">Quantity (Nos/Kg)</th>
                <th style="background-color: #444444; color: white">HSN Code</th>
                <th style="background-color: #444444; color: white">Price</th>
                <th style="background-color: #444444; color: white" class="text-right">Amount</th>
            </tr>

            <?php
            $i = 1;
            foreach ($show_ng_invoice as $key) {
                ?>
                <tr> 
                    <td><span id="" class=""></span>
                        <?php echo $i; ?>
                    </td>

                    <td><span id="" class=""></span>
                        <?php echo $key->product_name; ?>
                    </td>

                    <td><span id="" class=""></span>
                        <?php echo $key->description; ?>
                    </td>

                    <td><span id="" class=""></span>
                        <?php echo $key->quantity; ?>
                    </td>
                    <td><span id="" class=""></span>
                        <?php echo $key->hsn_code; ?>
                    </td>

                    <td><span id="" class=""></span>
                        <?php echo number_format($key->price, 2); ?>
                    </td>
                    <td  class="text-right"><span id="" class=""></span>
                        <?php echo number_format($key->amount, 2); ?>
                    </td>
                </tr>  
                <?php
                $i++;
            }
            ?>

            <tr>
                <td colspan="7"  class="text-right">
                    <label class="control-label"><b>Grand Total (INR): </b></label><?php echo number_format($ng_invoice_data_group['total'], 2); ?><br>
                </td>

            </tr>

            <tr>
                <td colspan="7"  class="text-right">
                    <?php
                    // include amount_convert
                    require( APPPATH . '/third_party/amount_convert.php');
                    ?>
                    <label class="control-label"><b>Grand Total in Words: <?php echo number_to_word($ng_invoice_data_group['total']); ?> Only</label>
                </td>
            </tr>

            <tr>

                <td colspan="10"  class="text-right">
                    <label class="control-label"><b>Payment mode: </b></label>
                    <?php if ($ng_invoice_data_group['payment_method'] == '1') { ?>
                        <?php echo "By Cash"; ?>
                    <?php } else if ($ng_invoice_data_group['payment_method'] == '2') { ?>
                        <?php
                        echo "By Cheque";
                    } else if ($ng_invoice_data_group['payment_method'] == '3') {
                        ?>
                        <?php
                        echo "By NetBanking";
                    } else {
                        ?><?php
                        echo "None Provided";
                    }
                    ?>

                </td>
            </tr>
        </table> 
        <label class="control-label"><b>Notes</b></label>
        <textarea style="overflow: auto; border: none;" class="elc-font form-control" readonly="" name="notes" id="quotation_memo" rows="4"><?php echo $settings['notes']; ?></textarea>
        <table class="table table-striped">
            <tr>
                <td class="text-left">
                    <label for="inputEmail3" class="col-sm-4 control-label"><b>Receivers Sign :</b></label>
                </td>

                <td colspan="5"  class="text-right">
                    <label for="inputEmail3" class="col-sm-4 control-label"><b> Authorized Sign :</b></label>
                </td>
            </tr>
        </table> 

        <p class="pull-left elc-font" >*(It is electronic generated invoice signatures may not appear).</p>
    </body>
</html>