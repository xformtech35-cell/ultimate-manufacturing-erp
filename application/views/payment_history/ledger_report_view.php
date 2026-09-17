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

        <div style="padding-left:75px; margin-bottom: 10px;">
            <b>From : </b><?php echo htmlspecialchars($from_date); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <b>To : </b><?php echo htmlspecialchars($to_date); ?>
        </div>

        <table> 
            <thead>
                <tr>
                    <th style="background-color: #444444; color: white; width: 5%;">Sr.No.</th>
                    <th style="background-color: #444444; color: white; width: 10%;">Date</th>
                    <th style="background-color: #444444; color: white; width: 25%;">Particular Name</th>
                    <th style="background-color: #444444; color: white; width: 12%;">Voucher Type</th>
                    <th style="background-color: #444444; color: white; width: 18%;">Voucher No</th>
                    <th style="background-color: #444444; color: white; width: 15%; text-align: right;">Debit</th>
                    <th style="background-color: #444444; color: white; width: 15%; text-align: right;">Credit</th>
                </tr>
            </thead>
            <tbody>

            <?php
            $i = 0;
            $grand_total = 0.0;   // Total Debit (Invoices + Dr Opening Balance)
            $credit_amount = 0.0; // Total Credit (Receipts / Customer Payments)
            
            foreach ((array) $ledger as $key) {
                if (empty($key)) {
                    continue;
                }
                $is_opening_balance = !empty($key['is_opening_balance']);
                $voucher_type = isset($key['type']) ? $key['type'] : '';
                $particulars = isset($key['particulars']) ? $key['particulars'] : '-';
                
                // Format voucher number
                $voucher_no = '-';
                if (!$is_opening_balance) {
                    if (!empty($key['invoice_number'])) {
                        $voucher_no = $key['invoice_number'];
                    } elseif (!empty($key['invoice_no'])) {
                        $voucher_no = $key['invoice_no'];
                    }
                }

                // Debit column: Sales invoices and Dr Opening Balance
                $debit_value = 0.0;
                if (!empty($key['total']) && (float)$key['total'] > 0) {
                    $debit_value = (float)$key['total'];
                    $grand_total += $debit_value;
                }

                // Credit column: Payments received & Receipts
                $credit_value = 0.0;
                if (!empty($key['invocie_pay_amount']) && (float)$key['invocie_pay_amount'] > 0) {
                    $credit_value = (float)$key['invocie_pay_amount'];
                    $credit_amount += $credit_value;
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
            
            <!-- Summary Row -->
            <tr style="background-color: #f9f9f9;">
                <td colspan="5" style="text-align: right;">
                    <b>Total</b>
                </td>
                <td>
                    <b><?php echo indian_number_format($grand_total, 2); ?></b>
                </td>
                <td>
                    <b><?php echo indian_number_format($credit_amount, 2); ?></b>
                </td>
            </tr>

            <!-- Closing Balance Row -->
            <tr>
                <?php
                $total_debit = (float)$grand_total;
                $total_credit = (float)$credit_amount;

                if ($total_debit > $total_credit) {
                    $closing_balance = $total_debit - $total_credit;
                    $closing_type = 'DR';
                    $balance_label = 'Closing Balance (Receivable / Dr)';
                    $closing_debit_display = '';
                    $closing_credit_display = indian_number_format($closing_balance, 2);
                } elseif ($total_credit > $total_debit) {
                    $closing_balance = $total_credit - $total_debit;
                    $closing_type = 'CR';
                    $balance_label = 'Closing Balance (Advance Received / Cr)';
                    $closing_debit_display = indian_number_format($closing_balance, 2);
                    $closing_credit_display = '';
                } else {
                    $closing_balance = 0.0;
                    $closing_type = '';
                    $balance_label = 'Closing Balance (Settled)';
                    $closing_debit_display = '0.00';
                    $closing_credit_display = '0.00';
                }
                ?>
                <td colspan="5" style="text-align: right;"><b><?php echo $balance_label; ?></b></td>
                <td><b><?php echo $closing_debit_display; ?></b></td>
                <td><b><?php echo $closing_credit_display; ?></b></td>
            </tr>

            <!-- Total Tally Row (Both Columns Equal) -->
            <tr style="background-color: #eef2f7;">
                <td colspan="5" style="text-align: right;">
                    <b>Total Balanced</b>
                </td>
                <td>
                    <b>
                        <?php 
                        $bigger_amount = max($total_debit, $total_credit);
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
        </table> 
    </body>
</html>
