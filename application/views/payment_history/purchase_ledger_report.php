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

        <div style="padding-left:75px; margin-bottom: 10px;">
            <b>From : </b><?php echo htmlspecialchars($from_date); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <b>To : </b><?php echo htmlspecialchars($to_date); ?>
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
            $grand_total = 0.0;  // Total purchases & Cr Opening balance (credit)
            $paid_amount = 0.0;   // Total payments made (debit)
            
            foreach ((array) $ledger as $key) {
                if (empty($key)) {
                    continue;
                }
                $is_opening_balance = !empty($key['is_opening_balance']);
                $voucher_type = isset($key['type']) ? $key['type'] : '';
                $particulars = isset($key['particulars']) ? $key['particulars'] : '-';
                
                $voucher_no = '-';
                if (!$is_opening_balance) {
                    if (!empty($key['invoice_number'])) {
                        $voucher_no = $key['invoice_number'];
                    } elseif (!empty($key['invoice_no'])) {
                        $voucher_no = $key['invoice_no'];
                    }
                }

                // Debit column: Payments made to supplier
                $debit_value = 0.0;
                if (!empty($key['invocie_pay_amount']) && (float)$key['invocie_pay_amount'] > 0) {
                    $debit_value = (float)$key['invocie_pay_amount'];
                    $paid_amount += $debit_value;
                }

                // Credit column: Purchase bills & Cr Opening Balance
                $credit_value = 0.0;
                if (!empty($key['total']) && (float)$key['total'] > 0) {
                    $credit_value = (float)$key['total'];
                    $grand_total += $credit_value;
                }
                ?>

                <tr>
                    <td><?php echo $i + 1; ?></td>

                    <td>
                        <?php 
                        if (isset($key['display_date'])) {
                            echo $key['display_date'];
                        } elseif (isset($key['invoice_date']) && !empty($key['invoice_date'])) {
                            echo date('d-m-Y', strtotime($key['invoice_date']));
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($particulars); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($voucher_type); ?>
                    </td>
                    
                    <td>
                        <?php echo htmlspecialchars($voucher_no); ?>
                    </td>

                    <td>
                        <?php echo ($debit_value > 0) ? indian_number_format($debit_value, 2) : '0.00'; ?>
                    </td>

                    <td>
                        <?php echo ($credit_value > 0) ? indian_number_format($credit_value, 2) : '0.00'; ?>
                    </td>
                </tr>  

                <?php
                $i++;
            }
            ?>
            
            <!-- Total Row -->
            <tr style="background-color: #f9f9f9;">
                <td colspan="5" style="text-align: right;">
                    <b>Total</b>
                </td>
                <td>
                    <b><?php echo indian_number_format($paid_amount, 2); ?></b>
                </td>
                <td>
                    <b><?php echo indian_number_format($grand_total, 2); ?></b>
                </td>
            </tr>
            
            <!-- Closing Balance Row -->
            <tr>
                <?php
                $total_credit = (float)$grand_total; // Bills + Opening Balance
                $total_debit = (float)$paid_amount;   // Payments Made

                if ($total_credit > $total_debit) {
                    $closing_balance = $total_credit - $total_debit;
                    $closing_type = 'CR';
                    $balance_label = 'Closing Balance (Amount Payable)';
                    $closing_debit_display = indian_number_format($closing_balance, 2);
                    $closing_credit_display = '';
                } elseif ($total_debit > $total_credit) {
                    $closing_balance = $total_debit - $total_credit;
                    $closing_type = 'DR';
                    $balance_label = 'Closing Balance (Advance Paid)';
                    $closing_debit_display = '';
                    $closing_credit_display = indian_number_format($closing_balance, 2);
                } else {
                    $closing_balance = 0.0;
                    $closing_type = '';
                    $balance_label = 'Closing Balance (Settled)';
                    $closing_debit_display = '0.00';
                    $closing_credit_display = '0.00';
                }
                ?>
                <td colspan="5" style="text-align: right;">
                    <b><?php echo $balance_label; ?></b>
                </td>
                <td><b><?php echo $closing_debit_display; ?></b></td>
                <td><b><?php echo $closing_credit_display; ?></b></td>
            </tr>
            
            <!-- Final Tally Row - Makes both columns equal with the bigger amount -->
            <tr style="background-color: #eef2f7;">
                <td colspan="5" style="text-align: right;">
                    <b>Total Balanced</b>
                </td>
                <td>
                    <b>
                        <?php 
                        $bigger_amount = max($total_credit, $total_debit);
                        echo indian_number_format($bigger_amount, 2); 
                        ?>
                    </b>
                </td>
                <td>
                    <b>
                        <?php 
                        echo indian_number_format($bigger_amount, 2); 
                        ?>
                    </b>
                </td>
            </tr>
            
            </tbody>
        </table> 
    </body>
</html>
