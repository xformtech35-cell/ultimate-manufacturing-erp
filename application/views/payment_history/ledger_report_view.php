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

            .ledger-heading {
                text-align: center;
                width: 100%;
            }

            .ledger-heading h1,
            .ledger-heading h2,
            .ledger-heading h4,
            .ledger-heading h5 {
                text-align: center;
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
        <?php if (empty($is_pdf)) { ?>
        <div style="margin-bottom: 15px;">
            <form method="post" action="<?php echo base_url(); ?>PaymentController/get_gst_ledger" style="display:inline-block;">
                <input type="hidden" name="from_date" value="<?php echo isset($from_date) ? htmlspecialchars($from_date, ENT_QUOTES, 'UTF-8') : ''; ?>">
                <input type="hidden" name="to_date" value="<?php echo isset($to_date) ? htmlspecialchars($to_date, ENT_QUOTES, 'UTF-8') : ''; ?>">
                <input type="hidden" name="company_name" value="<?php echo isset($company_id) ? htmlspecialchars($company_id, ENT_QUOTES, 'UTF-8') : ''; ?>">
                <button type="submit" name="download_pdf" value="1" class="btn btn-success printbtn" id="print_hide">Download PDF</button>
            </form>
            <button type="button" style="margin-top: 10px;margin-right:10px;" id="back_hide" class="btn btn-danger pull-right" onclick="history.back();">
                <i class="fa fa-arrow-left"></i> Back Button
            </button>
        </div>
        <?php } ?>

        <div class="ledger-heading"> 
         <h2><b><?php echo isset($session_data_head1['settings']['company_name']) ? $session_data_head1['settings']['company_name'] : ''; ?></b></h2>
         <h5>Address : <?php echo isset($session_data_head1['settings']['address']) ? $session_data_head1['settings']['address'] : ''; ?></h5>
         <h5>GSTIN : <?php echo isset($session_data_head1['settings']['company_gst']) ? $session_data_head1['settings']['company_gst'] : ''; ?></h5>
        </div>

          <br>
        <div class="ledger-heading"> 
         <h1><b>Sales Ledger</b></h1>
        </div>

        <br>
        <div class="ledger-heading"> 
         <h4><b><?php echo isset($company_name) ? $company_name : ''; ?></b></h4>
         <h5>Address : <?php echo isset($address) ? $address : ''; ?></h5>
        </div>

        <div style="padding-left:75px;">
            <b>From : </b><?php print_r($from_date) ?>
            <b>To : </b><?php print_r($to_date) ?>
        </div>

        <table> 
            <tr>
                <th style="background-color: #444444; color: white">Sr.No.</th>
                <th style="background-color: #444444; color: white; width:10%" >Date</th>
                <th style="background-color: #444444; color: white; width:15%">Particular Name</th>
                <th style="background-color: #444444; color: white; width:10%">Voucher Type</th>
                <th style="background-color: #444444; color: white; width:15%"> Voucher No</th>
                
                <th style="background-color: #444444; color: white">Debit</th>
                <th style="background-color: #444444; color: white">Credit</th>
            </tr>

            <?php
            $i = 0;
            $grand_total = 0;
            $paid_amount = 0;
            $balance_amount = 0.0;
            $credit_amount = 0.0;
            
            foreach ((array) $ledger as $key) {
                // Skip empty entries
                if (empty($key)) {
                    continue;
                }
                $voucher_type = isset($key['type']) ? strtolower(trim($key['type'])) : '';
                $is_receipt_voucher = in_array($voucher_type, array('receipt', 'rcpt', 'rept'));
                $is_opening_balance = !empty($key['is_opening_balance']) || $voucher_type == 'opening balance';
                ?>

                <tr> 
                    <td><?php echo $i + 1; ?></td>

                    <td>
                        <?php 
                        if (isset($key['invoice_date']) && !empty($key['invoice_date'])) {
                            // Handle different date formats
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

                            }else if($key['particulars']  == "Dr GST_Sales"){
                                echo "Dr GST Sales";
                            }
                            
                            else{
                            echo $key['particulars'];  
                            }
                        } elseif (isset($key['description'])) {
                            echo $key['description'];
                        } elseif ($is_opening_balance) {
                            echo "Opening Balance";
                        } elseif ($is_receipt_voucher) {
                            echo "Receipt";
                        } elseif (!empty($key['invocie_pay_amount']) && empty($key['total'])) {
                            echo "Payment ";
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        // Check for 'type' key or alternative keys
                         if(isset($key['particulars']) && $key['particulars'] == "Dr Opening_Balance"){

                        }else{
                        if (isset($key['type']) && !empty($key['type'])) {
                            echo $key['type'];
                        } elseif (isset($key['invoice_type'])) {
                            echo $key['invoice_type'];
                        } else {
                            // Determine type based on available data
                            if (!empty($key['total']) && $key['total'] > 0 && empty($key['invocie_pay_amount'])) {
                                echo "Sales";
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
                        // Debit column - show sales invoices and payments (excluding receipts)
                        $debit_value = 0;
                        if (isset($key['total']) && !empty($key['total']) && $key['total'] > 0) {
                            $debit_value += (double)$key['total'];
                            $grand_total += (double)$key['total'];
                        }

                        if (isset($key['invocie_pay_amount']) && !empty($key['invocie_pay_amount']) &&
                            !$is_receipt_voucher && !$is_opening_balance) {
                            $debit_value += (double)$key['invocie_pay_amount'];
                            $paid_amount += (double)$key['invocie_pay_amount'];
                        }

                        echo indian_number_format((float)$debit_value, 2) . "";
                        ?>
                    </td>

                    <td>
                        <?php
                        // Credit column - show receipts only (Rcpt/Rept), excluding opening balance
                        $credit_value = 0;
                        if (($is_receipt_voucher) && !empty($key['invocie_pay_amount'])) {
                            $credit_value += (double)$key['invocie_pay_amount'];
                            $credit_amount += $credit_value;
                        }

                        echo indian_number_format((float)$credit_value, 2) . "";
                        ?>
                    </td>


                </tr>  

                <?php
                $i++;
            }
            ?>
            
            <!-- Summary Rows -->
            <tr>
                <td colspan="5" style="text-align: right;">
                    <b>Total</b>
                </td>
                <td>
                    <b><?php echo indian_number_format((float)($grand_total + $paid_amount), 2); ?></b>
                </td>
                <td>
                    <b><?php echo indian_number_format((float)$credit_amount, 2); ?></b>
                </td>
            </tr>
<!-- Balance Row -->
<tr>
    <?php
    $total_debit = (float)$grand_total + $paid_amount;
    $balance = $total_debit - $credit_amount;
    if ($balance > 0) {
        // Positive amount - show in 7th column
        ?>
        <td colspan="5" style="text-align: right;"><b>Closing Balance</b></td>
        <td></td>  <!-- Empty 6th column -->
        <td><b><?php echo indian_number_format($balance, 2); ?></b></td>
        <?php
    } else {
        // Negative amount - show in 6th column
        ?>
        <td colspan="5" style="text-align: right;"><b>Closing Balance</b></td>
        <td><b><?php echo indian_number_format($balance, 2); ?></b></td>
        <td></td>  <!-- Empty 7th column -->
        <?php
    }
    ?>
</tr>

            <tr>
                <td colspan="5" style="text-align: right;">
                    
                </td>
<td colspan="1">
    <?php 
    $bigger_amount = max($total_debit, $credit_amount);
    echo indian_number_format((float)$bigger_amount, 2); 
    ?>
</td>
<td colspan="1">
    <?php 
    $bigger_amount = max($total_debit, $credit_amount);
    echo indian_number_format((float)$bigger_amount, 2); 
    ?>
</td>
            </tr>
        </table> 
    </body>
</html>
