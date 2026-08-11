<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Material Issue Slip - <?php echo isset($issue_slip['issue_no']) ? htmlspecialchars($issue_slip['issue_no'], ENT_QUOTES, 'UTF-8') : ''; ?></title>
    <meta name="description" content="Material Issue Slip print page">
    <meta name="viewport" content="width=device-width">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            color: #000;
            font-size: 9px;
            line-height: 1.3;
            padding: 10px;
        }

        .invoice-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .company-header {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #000;
            font-size: 9px;
            border-bottom: none;
        }

        .company-header td {
            border: 0.5px solid #000;
            padding: 6px 5px;
            vertical-align: top;
            border-bottom: none;
        }

        .details-section {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #000;
            border-top: none;
            font-size: 9px;
        }

        .details-section td {
            border: 0.5px solid #000;
            padding: 6px 5px;
            vertical-align: top;
        }

        #dynamic_field {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #000;
            border-top: none;
            font-size: 9px;
            margin-top: -0.5px;
        }

        #dynamic_field th,
        #dynamic_field td {
            border: 0.5px solid #000;
            padding: 3px;
            vertical-align: top;
            margin-bottom: -0.5px;
        }

        #dynamic_field tr {
            border-top: none;
            border-bottom: none;
        }

        .items-header th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
            padding: 6px 3px;
        }

        .col-sr { width: 4%; text-align: center; }
        .col-desc { width: 38%; }
        .col-unit { width: 8%; text-align: center; }
        .col-stock { width: 10%; text-align: right; }
        .col-qty { width: 10%; text-align: right; }
        .col-pending { width: 10%; text-align: right; }
        .col-remark { width: 20%; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
        }

        .label {
            font-weight: bold;
            color: #444;
            min-width: 80px;
            display: inline-block;
        }

        .label-sm {
            font-weight: bold;
            color: #444;
            min-width: 90px;
            display: inline-block;
        }

        .section-heading {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 4px;
            color: #003366;
            padding-bottom: 2px;
        }

        .item-row td {
            border-top: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
            height: 33px;
        }

        .item-row:first-child td {
            border-top: 0.5px solid #000;
        }

        .last-item-row td {
            border-bottom: 0.5px solid #000;
            height: 33px;
        }

        .empty-row td {
            border-top: none;
            border-bottom: none;
            height: 33px;
        }

        .total-section td {
            font-weight: bold;
        }

        .grand-total td {
            font-weight: bold;
            font-size: 10px;
            background: #f0f0f0;
        }

        .note-section {
            padding: 4px 5px;
            border-top: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
        }

        .signature {
            height: 80px;
            vertical-align: bottom;
            text-align: center;
        }

        .signature-line {
            border-top: 0.5px solid #000;
            width: 75%;
            margin: 45px auto 5px;
        }

        .footer {
            text-align: center;
            padding: 8px;
            border: 0.5px solid #000;
            border-top: none;
            font-size: 8px;
        }

        caption {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            caption-side: top;
            color: #003366;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <?php
        $settings = isset($settings) && is_array($settings) ? $settings : array();
        $issue_slip = isset($issue_slip) && is_array($issue_slip) ? $issue_slip : array();
        $items = !empty($issue_slip['items']) && is_array($issue_slip['items']) ? $issue_slip['items'] : array();

        $esc = function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };

        $field = function ($array, $key, $default = '') {
            return isset($array[$key]) && $array[$key] !== '' ? $array[$key] : $default;
        };

        $dateFormat = function ($date) {
            if (empty($date) || $date === '0000-00-00') {
                return 'N/A';
            }

            $timestamp = strtotime($date);
            return $timestamp ? date('d-m-Y', $timestamp) : $date;
        };

        $formatQty = function ($number) {
            $number = (float) $number;
            return fmod($number, 1.0) == 0.0 ? number_format($number, 0) : number_format($number, 2);
        };

        $assetUrl = function ($path) {
            if (empty($path)) {
                return '';
            }

            return preg_match('/^https?:\/\//i', $path) ? $path : base_url() . ltrim($path, '/');
        };

        $is_mrn = strpos($field($issue_slip, 'issue_no'), 'MRN-') === 0;

        $total_qty = 0;
        foreach ($items as $item) {
            $qty = isset($item['quantity']) ? (float) $item['quantity'] : 0;
            $total_qty += $is_mrn ? abs($qty) : $qty;
        }

        if (isset($issue_slip['total_qty']) && (float) $issue_slip['total_qty'] !== 0.0) {
            $total_qty = $is_mrn ? abs((float) $issue_slip['total_qty']) : (float) $issue_slip['total_qty'];
        }

        $target_total_rows = 15;
        $items_count = count($items);
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        $logo = $assetUrl($field($settings, 'company_logo'));
        ?>

        <caption><?php echo $is_mrn ? 'MATERIAL RETURN NOTE (MRN)' : 'MATERIAL ISSUE SLIP'; ?></caption>

        <table class="company-header">
            <tr>
                <td width="25%" align="center" valign="middle">
                    <?php if ($logo !== '') { ?>
                        <img class="company-logo" src="<?php echo $esc($logo); ?>" alt="Logo">
                    <?php } ?>
                </td>
                <td width="75%" valign="top">
                    <div class="company-name"><?php echo $esc($field($settings, 'company_name', 'Company Name')); ?></div>
                    <div><span class="label">GST Number :</span> <?php echo $esc(strtoupper($field($settings, 'company_gst'))); ?></div>
                    <div><span class="label">PAN Number :</span> <?php echo $esc(strtoupper($field($settings, 'company_pan'))); ?></div>
                    <div><span class="label">Mobile :</span> <?php echo $esc($field($settings, 'mobile')); ?></div>
                    <div><span class="label">Email :</span> <?php echo $esc($field($settings, 'email')); ?></div>
                    <div><span class="label">Address :</span> <?php echo $esc($field($settings, 'address')); ?></div>
                </td>
            </tr>
        </table>

        <table class="details-section">
            <tr>
                <td width="50%" valign="top">
                    <div class="section-heading"><?php echo $is_mrn ? 'RETURN DETAILS' : 'ISSUE DETAILS'; ?></div>
                    <div><span class="label-sm"><?php echo $is_mrn ? 'Return No:' : 'Issue No:'; ?></span> <?php echo $esc($field($issue_slip, 'issue_no')); ?></div>
                    <div><span class="label-sm"><?php echo $is_mrn ? 'Return Date:' : 'Issue Date:'; ?></span> <?php echo $esc($dateFormat($field($issue_slip, 'issue_date'))); ?></div>
                    <div><span class="label-sm">Status:</span> <?php echo $esc($is_mrn ? 'Returned' : ucfirst($field($issue_slip, 'status'))); ?></div>
                    <div><span class="label-sm">Total Items:</span> <?php echo (int) $field($issue_slip, 'total_items', $items_count); ?></div>
                    <div><span class="label-sm">Total Qty:</span> <?php echo $formatQty($total_qty); ?></div>
                </td>

                <td width="50%" valign="top">
                    <div class="section-heading">REFERENCE DETAILS</div>
                    <div><span class="label-sm"><?php echo $is_mrn ? 'Returned By:' : 'Issued To:'; ?></span> <?php echo $esc($field($issue_slip, 'issued_to')); ?></div>
                    <div><span class="label-sm">Department:</span> <?php echo $esc($field($issue_slip, 'department')); ?></div>
                    <?php 
                    $session_data_head1 = $this->session->userdata('session_data_head');
                    $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
                    if ($_has_project_master): 
                    ?>
                    <div><span class="label-sm">Project Code:</span> <?php echo $esc($field($issue_slip, 'project_code', 'N/A')); ?></div>
                    <?php endif; ?>
                    <div><span class="label-sm">Job Order No:</span> <?php echo $esc($field($issue_slip, 'joborder_number', 'N/A')); ?></div>
                    <div><span class="label-sm">Generated On:</span> <?php echo date('d-m-Y'); ?></div>
                </td>
            </tr>
            <?php if (!$is_mrn && $field($issue_slip, 'purpose') !== '') { ?>
                <tr>
                    <td colspan="2">
                        <span class="label">Purpose:</span> <?php echo $esc($field($issue_slip, 'purpose')); ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <table id="dynamic_field">
            <tr class="items-header">
                <th class="col-sr">Sr.No.</th>
                <th class="col-desc">Description</th>
                <th class="col-unit">Unit</th>
                <th class="col-stock">Available Stock</th>
                <?php if (!$is_mrn): ?>
                <th class="col-stock">Required Qty</th>
                <th class="col-stock">Total Issued</th>
                <?php endif; ?>
                <th class="col-qty"><?php echo $is_mrn ? 'Return Qty' : 'Issue Qty'; ?></th>
                <th class="col-pending"><?php echo $is_mrn ? 'Status' : 'Pending Qty'; ?></th>
                <th class="col-remark">Remark</th>
            </tr>

            <?php
            $i = 1;
            $item_counter = 0;
            foreach ($items as $item) {
                $item_counter++;
                $row_class = ($item_counter == $items_count) ? 'last-item-row' : 'item-row';
                $code = $field($item, 'code');
                $item_name = $field($item, 'item_name');
                $description = trim($code . ($item_name !== '' ? ' - ' . $item_name : ''));
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="col-sr"><?php echo $i; ?></td>
                    <td class="col-desc"><b><?php echo $esc($description); ?></b></td>
                    <td class="col-unit"><?php echo $esc($field($item, 'unit')); ?></td>
                    <td class="col-stock"><?php echo isset($item['current_stock']) ? $formatQty($item['current_stock']) : '-'; ?></td>
                    <?php if (!$is_mrn): ?>
                    <td class="col-stock"><?php echo !empty($issue_slip['joborder_number']) ? $formatQty($item['required_qty']) : '-'; ?></td>
                    <td class="col-stock"><?php echo !empty($issue_slip['joborder_number']) ? $formatQty($item['fulfilled_qty']) : '-'; ?></td>
                    <?php endif; ?>
                    <td class="col-qty"><?php echo $formatQty($is_mrn ? abs($field($item, 'quantity', 0)) : $field($item, 'quantity', 0)); ?></td>
                    <td class="col-pending">
                        <?php
                        if ($is_mrn) {
                            echo 'Returned';
                        } else {
                            $pending = isset($item['pending_qty']) ? floatval($item['pending_qty']) : 0;
                            $stock = isset($item['current_stock']) ? floatval($item['current_stock']) : 0;
                            if (!empty($issue_slip['joborder_number'])) {
                                if ($pending > 0) {
                                    if ($stock <= 0) {
                                        echo '<span style="color: #dd4b39; font-weight: bold;">Pending: ' . $formatQty($pending) . ' (No Stock)</span>';
                                    } else {
                                        echo '<span style="color: #f39c12; font-weight: bold;">Remaining: ' . $formatQty($pending) . '</span>';
                                    }
                                } else {
                                    echo '<span style="color: #00a65a; font-weight: bold;">Fully Covered</span>';
                                }
                            } else {
                                echo $formatQty($pending);
                            }
                        }
                        ?>
                    </td>
                    <td class="col-remark"><?php echo $esc($field($item, 'remarks')); ?></td>
                </tr>
            <?php
                $i++;
            }
 
            for ($b = 0; $b < $blank_rows_needed; $b++) { ?>
                <tr class="empty-row">
                    <td class="col-sr">&nbsp;</td>
                    <td class="col-desc">&nbsp;</td>
                    <td class="col-unit">&nbsp;</td>
                    <td class="col-stock">&nbsp;</td>
                    <?php if (!$is_mrn): ?>
                    <td class="col-stock">&nbsp;</td>
                    <td class="col-stock">&nbsp;</td>
                    <?php endif; ?>
                    <td class="col-qty">&nbsp;</td>
                    <td class="col-pending">&nbsp;</td>
                    <td class="col-remark">&nbsp;</td>
                </tr>
            <?php } ?>

            <tr class="total-section">
                <td colspan="<?php echo $is_mrn ? 4 : 6; ?>" class="text-right"><b>Total Quantity:</b></td>
                <td class="col-qty"><b><?php echo $formatQty($total_qty); ?></b></td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <?php if ($field($issue_slip, 'remarks') !== '') { ?>
                <tr>
                    <td colspan="<?php echo $is_mrn ? 7 : 9; ?>" class="note-section">
                        <span class="label">Remarks:</span> <?php echo nl2br($esc($field($issue_slip, 'remarks'))); ?>
                    </td>
                </tr>
            <?php } ?>

            <tr>
                <td colspan="2" class="signature">
                    <div class="signature-line"></div>
                    Prepared By
                </td>
                <td colspan="2" class="signature">
                    <div class="signature-line"></div>
                    <?php echo $is_mrn ? 'Approved By' : 'Issued By'; ?>
                </td>
                <td colspan="3" class="signature">
                    <div class="signature-line"></div>
                    <?php echo $is_mrn ? 'Returned By' : 'Received By'; ?>
                </td>
            </tr>
        </table>

        <div class="footer">
            This is a computer generated <?php echo $is_mrn ? 'material return note' : 'material issue slip'; ?>. No physical signature required.<br>
            <?php echo $esc($field($settings, 'company_name')); ?> | <?php echo $esc($field($settings, 'address')); ?>
        </div>
    </div>
</body>

</html>
