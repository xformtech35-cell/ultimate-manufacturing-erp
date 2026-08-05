<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        /* BOX STYLE SECTIONS */
        .box {
            border: 1px solid #ccc;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .section-title {
            background: #f0f4ff;
            border-left: 4px solid #1a73e8;
            padding: 4px 6px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th {
            background: #e3e9f5;
            border: 1px solid #aaa;
            padding: 6px;
            text-align: center;
            font-weight: bold;
        }

        td {
            border: 1px solid #bbb;
            padding: 6px;
            vertical-align: top;
        }

        /* Zebra rows */
        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .notes-box {
            min-height: 55px;
            padding: 6px;
            border: 1px solid #bbb;
            border-radius: 4px;
            background: #fafafa;
        }

        /* Signature style */
        .sign-table td {
            height: 80px;
            text-align: center;
            border: none;
            font-weight: bold;
        }
    </style>

</head>

<body>

    <!-- Company Header Details (below the blue bar) -->
    <div class="box">
        <table style="border:none;">
            <tr>
                <td style="width:40%; border:none;">
                    <img src="<?php echo base_url($settings['company_logo']); ?>"
                        style="max-height:70px;">
                </td>

                <td style="width:60%; text-align:right; border:none;">
                    <b style="font-size:14px;"><?php echo $settings['company_name']; ?></b><br>
                    GST: <?php echo $settings['company_gst']; ?><br>
                    PAN: <?php echo $settings['company_pan']; ?><br>
                    Mobile: <?php echo $settings['mobile']; ?><br>
                    Email: <?php echo $settings['email']; ?><br>
                    Address: <?php echo $settings['address']; ?>
                </td>
            </tr>
        </table>
    </div>


    <!-- REQUISITION DETAILS -->
    <div class="box">
        <div class="section-title">Requisition Information</div>

        <table>
            <tr>
                <td style="width:50%;">
                    <b>PR Number:</b> <?php echo $requisition->pr_no; ?><br>
                    <b>Requisition Date:</b> <?php echo date('d-m-Y', strtotime($requisition->pr_date)); ?><br>
                    <b>Required Date:</b>
                    <?php echo !empty($requisition->required_date) ? date('d-m-Y', strtotime($requisition->required_date)) : 'N/A'; ?><br>
                    <b>Requested By:</b>
                    <?php echo $requisition->requested_by_name ?? $requisition->requested_by; ?><br>
                    <?php 
                    $session_data_head1 = $this->session->userdata('session_data_head');
                    $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
                    if ($_has_project_master): 
                    ?>
                    <b>Project Code:</b> <?php echo $requisition->project_code ?? 'N/A'; ?><br>
                    <?php endif; ?>
                    <?php if (isset($requisition->so_no) && isset($requisition->oc_no) && !empty($requisition->so_no) && $requisition->so_no === $requisition->oc_no): ?>
                    <b>SO:</b> <?php echo htmlspecialchars($requisition->so_no); ?><br>
                    <?php else: ?>
                    <b>Sales Order:</b> <?php echo $requisition->so_no ?? 'N/A'; ?><br>
                    <b>OC Number:</b> <?php echo $requisition->oc_no ?? 'N/A'; ?><br>
                    <?php endif; ?>
                </td>

                <td style="width:50%;">
                    <b>Department:</b>
                    <?php
                    if (!empty($department_result)) {
                        foreach ($department_result as $dept) {
                            if ($dept->department_id == $requisition->department_id_fk) {
                                echo $dept->department_name;
                                break;
                            }
                        }
                    }
                    ?><br>

                    <b>Urgency Level:</b> <?php echo $requisition->urgency_level ?? 'N/A'; ?><br>

                    <b>Remarks:</b><br>
                    <div class="notes-box">
                        <?php echo nl2br($requisition->remarks ?? 'N/A'); ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>


    <!-- ITEMS TABLE -->
    <div class="box">
        <div class="section-title">Item Details</div>

        <table>
            <thead>
                <tr>
                    <th>Sr.No</th>
                    <th>Item</th>
                    <!-- <th>Description</th> -->
                    <th>HSN</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Est. Cost</th>
                    <th>Specification</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if (!empty($requisition_items)) {
                    $i = 1;
                    foreach ($requisition_items as $item) { ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $item->item_code ?? ''; ?></td>
                            <!-- <td><?php echo nl2br(trim($item->description ?? '')); ?></td> -->
                            <td><?php echo $item->hsn ?? ''; ?></td>
                            <td><?php echo $item->quantity ?? ''; ?></td>
                            <td><?php echo $item->unit ?? ''; ?></td>
                            <td><?php echo number_format($item->estimated_cost ?? 0, 2); ?></td>
                            <td><?php echo nl2br($item->specification ?? ''); ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">No items found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


    <!-- NOTES -->
    <div class="box">
        <div class="section-title">Notes</div>
        <div class="notes-box">
            <?php echo $settings['purchase_requisition_notes']; ?>
        </div>
    </div>

    <!-- SIGN -->
    <table class="sign-table" style="width:100%; margin-top:20px;">
        <tr>
            <td style="width:50%;">
                <img src="<?php echo base_url($settings['company_stamp']); ?>" width="90"><br>

                Receiver's Signature
            </td>

            <td style="width:50%;">
                <img src="<?php echo base_url($settings['company_stamp']); ?>" width="90"><br>
                Authorized Signatory
            </td>
        </tr>
    </table>

</body>

</html>