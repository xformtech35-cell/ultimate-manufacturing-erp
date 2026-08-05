
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title id="title_text"> Ledger </title>
        <meta name="description" content="Quotation print page">
        <meta name="viewport" content="width=device-width">
        <link href="<?php echo base_url(); ?>printme/bootstrap.min.css" rel="stylesheet" >
        <link href="<?php echo base_url(); ?>printme/main.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>printme/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>printme/jquery-printme.js"></script>

        <style>
            table {
                font-family: arial, sans-serif;

                width: 90%;
                margin-right: 5%;
                margin-left: 5%;  


            }

            td, th {
                border: 1px solid #000000;
                text-align: left;
                padding: 5px;
            }

            @media print {

                @page {
                    size: auto;   /* auto is the initial value */
                    margin: 0;  /* this affects the margin in the printer settings */
                    /*                       margin-top: 3%;  */

                }


                #make_pdf, #hide_certificate, #print_hide, #title_text{
                    display: none;
                }
                .navbar{
                    display:none;
                }
                #footer{
                    display:none;
                } 
                .printbtn{
                    display:none; 
                }
                #social_share{
                    display:none;
                }
                #page_break2{

                    page-break-after: always;

                }
            }

        </style>
    </head>
    <body>
        <a href="javascript:print();" class="btn btn-success printbtn" id="print_hide">Download</a>

    <center> <h3><b>Vtech Solutions</b></h3>
        <h4><b>Ledger Printing</b></h4><br></center>

    <div style="padding-left:75px;">
        <b>From : </b><?php print_r($from_date) ?>
        <b>To : </b><?php print_r($to_date) ?>

    </div>

    <table> 

        <tr>
            <th style="background-color: #444444; color: white">Sr.No.</th>
            <th style="background-color: #444444; color: white">Date</th>
            <th style="background-color: #444444; color: white">Invoice No</th>
            <th style="background-color: #444444; color: white">Debit</th>
            <th style="background-color: #444444; color: white">Credit</th>

        </tr>


        <?php
        $i = 0;
        $grand_total = 0;
        $paid_amount = 0;
        foreach ((object) $ledger as $key) {
            ?>

            <tr> 
                <td><span id="" class=""></span>
                    <?php echo $i + 1; ?>
                </td>

                <td><span id="" class=""></span>
                    <?php echo $key['invoice_date']; ?>
                </td>
                <td><span id="" class=""></span>
                    <?php
                    if ($key['invoice_number']) {
                        echo $key['invoice_number'];
                    } else {

                        echo '';
                    }
                    ?>
                </td>

                <td><span id="" class=""></span>
                    <?php
                    $grand_total = $grand_total + $key['total'];
                    echo $key['total'];
                    ?>
                </td>

                <td><span id="" class=""></span>
                    <?php
                    $paid_amount = $paid_amount + $key['invocie_pay_amount'];
                    echo $key['invocie_pay_amount'];
                    ?>

                </td>

            </tr>  


            <?php
            $i++;
        }
        ?>
        <tr>
            <td colspan="3"   class="text-right">
                <b>Grand Total : </b>
            </td>
            <td colspan="1">
                <?php echo $grand_total; ?>
            </td>
            <td>
                <?php echo $paid_amount; ?>
            </td>
        </tr>

        <tr>
            <td colspan="4"   class="text-right">
                <b>Total Payable : </b>
            </td>
            <td colspan="1">
                <?php echo $grand_total - $paid_amount; ?>
            </td>

        </tr>

    </table> 
</body>
</html>