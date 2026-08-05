<?php
$session_data_head1 = $this->session->userdata('session_data_head');
require_once(APPPATH . '/third_party/amount_convert.php');
?>
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
                }

                #make_pdf, #hide_certificate, #print_hide,#back_hide, #title_text{
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
        <div style="margin-bottom: 15px;">
            <a href="javascript:print();" class="btn btn-success printbtn" id="print_hide">Download</a>
            <button type="button" style="margin-top: 10px;margin-right:10px;" id="back_hide" class="btn btn-danger pull-right" onclick="history.back();">
                <i class="fa fa-arrow-left"></i> Back Button
            </button>
        </div>

        <center> 
         <h2><b><?php echo isset($session_data_head1['settings']['company_name']) ? $session_data_head1['settings']['company_name'] : ''; ?></b></h2>
         <h5>Address : <?php echo isset($session_data_head1['settings']['address']) ? $session_data_head1['settings']['address'] : ''; ?></h5>
         <h5>GSTIN : <?php echo isset($session_data_head1['settings']['company_gst']) ? $session_data_head1['settings']['company_gst'] : ''; ?></h5>
        </center>

        <br>
        <center> 
         <h1><b>Purchase Ledger</b></h1>
        </center>

        <br>
        <center> 
         <h4><b><?php echo isset($company_name) ? $company_name : ''; ?></b></h4>
         <h5>Address : <?php echo isset($address) ? $address : ''; ?></h5>
        </center>

        <div style="padding-left:75px;">
            <b>From : </b><?php print_r($from_date) ?>
            <b>To : </b><?php print_r($to_date) ?>
        </div>

        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th style="background-color: #444444; color: white">Sr.No.</th>
                    <th style="background-color: #444444; color: white; width:10%" >Date</th>
                      <th style="background-color: #444444; color: white; width:15%">Particular Name</th>
                    <th style="background-color: #444444; color: white; width:10%">Voucher Type</th>
                        
                    <th style="background-color: #444444; color: white; width:15%"> VoucherNo</th>
              
                    <th style="background-color: #444444; color: white">Debit</th>
                    <th style="background-color: #444444; color: white">Credit</th>
                </tr>
            </thead>
            <tbody>

            <?php
            $i = 0;
            $grand_total = 0;  // Total purchases (credit)
            $paid_amount = 0;   // Total payments (debit)
            $receipt_amount = 0; // Total receipts (credit)
            
            foreach ((array) $ledger as $key) {
                // Skip empty entries
                if (empty($key)) {
                    continue;
                }
                $voucher_type = isset($key['type']) ? strtolower(trim($key['type'])) : '';
                $particulars = isset($key['particulars']) ? strtolower(trim($key['particulars'])) : '';
                $normalized_particulars = str_replace(' ', '_', $particulars);
                $is_receipt_voucher = in_array($voucher_type, array('receipt', 'rcpt', 'rept'));
                $is_payment_voucher = in_array($voucher_type, array('payment', 'pymt'));
                $is_opening_balance = !empty($key['is_opening_balance']) || $voucher_type == 'opening balance' || strpos($normalized_particulars, 'opening_balance') !== false;
                ?>

                <tr>
                    <td><?php echo $i + 1; ?></td>

                    <td>
                        <?php 
                        if (isset($key['invoice_date']) && !empty($key['invoice_date'])) {
                            $originalDate = $key['invoice_date'];
                            // Check if it's already in d-m-Y format or needs conversion
                            if (strpos($originalDate, '-') !== false) {
                                $parts = explode('-', $originalDate);
                                // If it's Y-m-d format (like 2026-03-14)
                                if (strlen($parts[0]) == 4) {
                                    echo $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                                } else {
                                    // Already in d-m-Y format
                                    echo $originalDate;
                                }
                            } else {
                                echo $originalDate;
                            }
                        }
                        ?>
                    </td>

                      <td>
                       <?php
                        // Check for particulars in different possible keys



                        if (isset($key['particulars']) && !empty($key['particulars'])) {
                            if($key['particulars']  == "Dr Opening_Balance"){
                                echo "Dr Opening Balance";

                            }else if($key['particulars']  == "Dr GST_Purchase"){
                                echo "Dr GST Purchase";
                            }
                            
                            else{
                            echo $key['particulars'];  
                            }





                        } elseif (isset($key['description'])) {
                            echo $key['description'];
                        } elseif (isset($key['type']) && $key['type'] == 'Prch') {
                            echo "Purchase Bill";
                        } elseif ($is_opening_balance) {
                            echo "Opening Balance";
                        } elseif ($is_receipt_voucher) {
                            echo "Receipt";
                        } elseif ($is_payment_voucher) {
                            echo "Payment Made";
                        } elseif (!empty($key['invocie_pay_amount']) && empty($key['total'])) {
                            echo "Payment";
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        // Determine voucher type based on available data

                                                 if(isset($key['particulars']) && $key['particulars'] == "Dr Opening_Balance"){
                                                 }else{
                        if (isset($key['type']) && !empty($key['type'])) {
                            echo $key['type'];
                        } elseif (isset($key['invoice_type'])) {
                            echo $key['invoice_type'];
                        } else {
                            // Determine type based on available data
                            if (!empty($key['total']) && $key['total'] > 0 && empty($key['invocie_pay_amount'])) {
                                echo "Purchase";
                            } elseif (empty($key['total']) && !empty($key['invocie_pay_amount'])) {
                                echo "Payment";
                            } else {
                                echo '-';
                            }
                        }
                                                 }
                        ?>
                    </td>
                    


                    <td>
                        <?php
                        // Check for invoice number in different possible keys
                           if(isset($key['particulars']) && $key['particulars'] == "Dr Opening_Balance"){
                                                 }else{
                        if (isset($key['invoice_number']) && !empty($key['invoice_number'])) {
                            echo $key['invoice_number'];
                        } elseif (isset($key['invoice_no']) && !empty($key['invoice_no'])) {
                            echo $key['invoice_no'];
                        } else {
                            echo '-';
                        }
                                                 }
                        ?>
                    </td>

                  
                    <td>
                        <?php
                        // Debit column - shows payments made (money going out)
                        $debit_value = 0;
                        
                        // For payment entries
                        if ($is_payment_voucher && !$is_opening_balance && !empty($key['invocie_pay_amount'])) {
                            $debit_value += (double)$key['invocie_pay_amount'];
                            $paid_amount += (double)$key['invocie_pay_amount'];
                        }
                        
                        // For payment entries without type specified
                        if (!isset($key['type']) && !$is_opening_balance && isset($key['invocie_pay_amount']) && !empty($key['invocie_pay_amount'])) {
                            $debit_value += (double)$key['invocie_pay_amount'];
                            $paid_amount += (double)$key['invocie_pay_amount'];
                        }

                        echo indian_number_format((float)$debit_value, 2);
                        ?>
                    </td>

                    <td>
                        <?php
                        // Credit column - shows purchase bills (liability increase)
                        $credit_value = 0;
                        
                        // For purchase entries (Prch type or total amount)
                        if (isset($key['type']) && $key['type'] === 'Prch' && isset($key['total']) && !empty($key['total'])) {
                            $credit_value += (double)$key['total'];
                            $grand_total += (double)$key['total'];
                        }
                        
                        // For purchase entries without type specified
                        if (!isset($key['type']) && isset($key['total']) && !empty($key['total']) && $key['total'] > 0) {
                            $credit_value += (double)$key['total'];
                            $grand_total += (double)$key['total'];
                        }

                        // For receipt entries
                        if (($is_receipt_voucher || $is_opening_balance) && !empty($key['invocie_pay_amount'])) {
                            $credit_value += (double)$key['invocie_pay_amount'];
                            $receipt_amount += (double)$key['invocie_pay_amount'];
                        }

                        echo indian_number_format((float)$credit_value, 2);
                        ?>
                    </td>

                </tr>  

                <?php
                $i++;
            }
            ?>
            
            <!-- Total Row -->
            <tr>
                <td colspan="5" style="text-align: right;">
                    <b>Total</b>
                </td>
                <td>
                    <b><?php echo indian_number_format((float)$paid_amount, 2); ?></b>
                </td>
                <td>
                    <b><?php echo indian_number_format((float)($grand_total + $receipt_amount), 2); ?></b>
                </td>
            </tr>
            
           <tr>
    <td colspan="5" style="text-align: right;">
        <b>Closing balance</b>
    </td>

    <?php
    $total_credit = (float)$grand_total + $receipt_amount;
    $difference = abs($paid_amount - $total_credit);

    if ($paid_amount > $total_credit) {
        // Debit is greater → show difference in Credit column
        ?>
        <td></td>
        <td><b><?php echo indian_number_format($difference, 2); ?></b></td>
        <?php
    } elseif ($total_credit > $paid_amount) {
        // Credit is greater → show difference in Debit column
        ?>
        <td><b><?php echo indian_number_format($difference, 2); ?></b></td>
        <td></td>
        <?php
    } else {
        // Equal case
        ?>
        <td><b>0.00</b></td>
        <td><b>0.00</b></td>
        <?php
    }
    ?>
</tr>
            
            <!-- Final Adjustment Row - Makes both columns equal with the bigger amount -->
            <tr>
                <td colspan="5" style="text-align: right;">
                    <?php
                    $bigger_amount = max($total_credit, $paid_amount);
                    if ($total_credit > $paid_amount) {
                        echo "<b>Amount Payable</b>";
                    } elseif ($paid_amount > $total_credit) {
                        echo "<b>Advance Paid</b>";
                    } else {
                        echo "<b>Settled Amount</b>";
                    }
                    ?>
                </td>
                <td colspan="1">
                    <b><?php echo indian_number_format((float)$bigger_amount, 2); ?></b>
                </td>
                <td colspan="1">
                    <b><?php echo indian_number_format((float)$bigger_amount, 2); ?></b>
                </td>
            </tr>
            
            </tbody>
        </table> 
    </body>
</html>
