
<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Job Order - <?php echo isset($joborder_data_group['number']) ? $joborder_data_group['number'] : ''; ?></title>
    <meta name="description" content="Job Order print page">
    <meta name="viewport" content="width=device-width">
    <style>
        @page {
            margin: 12mm 10mm 12mm 10mm;
        }

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
            padding: 0;
        }

        table {
            width: 100%;
        }

        /* Improve rendering consistency in HTML->PDF (mPDF) */
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }


        .invoice-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Company Header - Separate Table */
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

        /* Project/Job Order Details - Separate Table */
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

        /* Main Items Table */
        #dynamic_field {
            width: 100%;
            table-layout: fixed;
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
            overflow: hidden;
            word-wrap: break-word;
        }
        
        #dynamic_field tr {
            border-top: none;
            border-bottom: none;
        }

        /* Items header styling */
        .items-header th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
            padding: 6px 3px;
        }

        /* Column width classes - Job Order (9 columns) */
        .col-sr { width: 5%; text-align: center; }
        .col-product { width: 10%; text-align: left; }
        .col-description { width: 25%; text-align: center; }
        .col-qty { width: 6%; text-align: center; }
        .col-unit { width: 5%; text-align: center; }
        .col-tag { width: 5%; text-align: center; }
        .col-scope { width: 20%; text-align: center; }
        .col-stores { width: 6%; text-align: center; }
        .col-price { width: 10%; text-align: right; }
        .col-remark { width: 17%; text-align: center; }


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
            min-width: 100px;
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

        .no-border-table {
            width: 100%;
            border-collapse: collapse;
        }

        .no-border-table td {
            border: none;
            padding: 1px;
        }
        
        .empty-row td {
            border-top: none;
            border-bottom: none;
            height: 33px;
        }

        .signature {
            height: 80px;
            vertical-align: bottom;
            text-align: center;
        }

        .stamp-img {
            max-width: 70px;
            max-height: 50px;
            margin-bottom: 5px;
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
        
        .status-draft { color: #999; }
        .status-sent { color: #0066cc; }
        .status-viewed { color: #ff9900; }
        .status-approved { color: #00cc00; }
        .status-rejected { color: #cc0000; }
        .status-canceled { color: #666; }
    </style>
</head>

<body>
    <div class="invoice-container">
        <?php
        // Status arrays
        $statusArr = array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled');
        $statusClass = array(1 => 'status-draft', 2 => 'status-sent', 3 => 'status-viewed', 4 => 'status-approved', 5 => 'status-rejected', 6 => 'status-canceled');
        
        // Target rows for consistent layout
        $target_total_rows = 15;
        $items_count = !empty($show_joborder) ? count($show_joborder) : 0;
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        ?>

        <caption>JOB ORDER</caption>
        
        <!-- Company Header Table -->
        <table class="company-header">
            <tr>
                <td width="25%" align="center" valign="middle">
                    <img class="company-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo">
                </td>
                <td width="75%" valign="top">
                    <div class="company-name"><?php echo $settings['company_name']; ?></div>
                    <div><span class="label">GST Number :</span> <?php echo strtoupper($settings['company_gst']); ?></div>
                    <div><span class="label">PAN Number :</span> <?php echo strtoupper($settings['company_pan']); ?></div>
                    <div><span class="label">Mobile :</span> <?php echo $settings['mobile']; ?></div>
                    <div><span class="label">Email :</span> <?php echo $settings['email']; ?></div>
                    <div><span class="label">Address :</span> <?php echo $settings['address']; ?></div>
                </td>
            </tr>
        </table>

        <!-- Project and Job Order Details Table -->
        <table class="details-section">
            <tr>
                <td width="50%" valign="top">
                    <div class="section-heading">PROJECT DETAILS</div>
                    <div><span class="label-sm">Customer Code:</span> <?php echo isset($joborder_data_group['customer_code']) ? $joborder_data_group['customer_code'] : 'N/A'; ?></div>
                    <div><span class="label-sm">Customer Name:</span> <?php echo isset($joborder_data_group['company_name']) ? $joborder_data_group['company_name'] : 'N/A'; ?></div>
                    <div><span class="label-sm">Contact Person:</span> <?php echo isset($joborder_data_group['fullname']) ? $joborder_data_group['fullname'] : 'N/A'; ?></div>
                    <div><span class="label-sm">Job Order Status:</span> 
                        <span class="<?php echo (isset($joborder_data_group['status']) && isset($statusClass[$joborder_data_group['status']])) ? $statusClass[$joborder_data_group['status']] : ''; ?>">
                            <?php echo (isset($joborder_data_group['status']) && isset($statusArr[$joborder_data_group['status']])) ? $statusArr[$joborder_data_group['status']] : 'Draft'; ?>
                        </span>
                    </div>
                </td>
                <td width="50%" valign="top">
                    <div class="section-heading">JOB ORDER DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">Job Order Number:</td><td><?php echo isset($joborder_data_group['number']) ? $joborder_data_group['number'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Job Order Date:</td><td><?php echo isset($joborder_data_group['date']) && $joborder_data_group['date'] != '0000-00-00' ? date('d-m-Y', strtotime($joborder_data_group['date'])) : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">System:</td><td><?php echo isset($joborder_data_group['system']) ? $joborder_data_group['system'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Location:</td><td><?php echo isset($joborder_data_group['location']) ? $joborder_data_group['location'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Capacity:</td><td><?php echo isset($joborder_data_group['capacity']) ? $joborder_data_group['capacity'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Project Qty:</td><td><?php echo isset($joborder_data_group['project_qty']) ? $joborder_data_group['project_qty'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">SO  Number:</td><td><?php echo isset($joborder_data_group['oc_number']) ? $joborder_data_group['oc_number'] : 'N/A'; ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table id="dynamic_field">
            <!-- Items Header - 9 columns exactly -->
            <tr class="items-header">
                <th class="col-sr">Sr.</th>
                <th class="col-product">Product Name</th>
                <th class="col-description">Description</th>

                <th class="col-qty">QTY</th>
                <th class="col-unit">Unit</th>
                <th class="col-tag">Tag No.</th>
                <th class="col-scope">Scope</th>
                <th class="col-stores">Stores Remark</th>
                <th class="col-price">Price</th>
                <th class="col-remark">Remark</th>
            </tr>

            <?php if(isset($show_joborder) && !empty($show_joborder)):
                $i = 1;
                $item_counter = 0;
                $total_items = count($show_joborder);
                $total_qty = 0;
                $formatted_total_qty = '0';
                
                foreach ($show_joborder as $key):
                    // ---- Section Heading Row ----
                    if (isset($key->product_name) && $key->product_name === '__HEADING__'):
                        $desc = trim($key->description ?? '');
                        $isMain = true;
                        if (isset($key->tag_no) && ($key->tag_no === 'MAIN' || $key->tag_no === 'SUB')) {
                            $isMain = ($key->tag_no === 'MAIN');
                        }
                        $bg = $isMain ? '#e6e0ed' : '#fdeada';
                        $fg = $isMain ? '#5a3d8a' : '#000000';
                        $displayDesc = $isMain ? strtoupper($desc) : $desc;
                ?>
                    <tr style="background-color: <?php echo $bg; ?> !important; page-break-inside: avoid;">
                        <td colspan="10" style="background-color: <?php echo $bg; ?> !important; color: <?php echo $fg; ?> !important; font-weight: bold; padding: 5px 8px; border: 0.5px solid #000; vertical-align: middle;">
                            <span style="font-family: 'DejaVu Sans', sans-serif;">🏷️</span>
                            <strong><?php echo htmlspecialchars($displayDesc); ?></strong>
                        </td>
                    </tr>
                <?php
                        continue;
                    endif;
                    // ---- End Heading Row ----

                    $item_counter++;
                    $total_qty += isset($key->quantity) ? (float) $key->quantity : 0;
                    $row_class = ($item_counter == $total_items) ? 'last-item-row' : 'item-row';
                    
                    $unit_name = '';
                    if(isset($key->unit) && isset($unit_result)):
                        foreach ($unit_result as $unit): 
                            if($unit->unit == $key->unit): 
                                $unit_name = $unit->unit;
                                break;
                            endif; 
                        endforeach; 
                    endif;
                    
                    $item_status = (isset($key->status_i) && isset($statusArr[$key->status_i])) ? $statusArr[$key->status_i] : '';
                    $item_status_class = (isset($key->status_i) && isset($statusClass[$key->status_i])) ? $statusClass[$key->status_i] : '';
                    
                    $product_display = isset($key->product_name) ? $key->product_name : '';
                    if(isset($key->item_name) && !empty($key->item_name)) {
                        $product_display .= " - " . $key->item_name;
                    }
                    
                    $stores_remark_display = '';
                    if(isset($key->stores_remark)) {
                        if($key->stores_remark == 'Y') {
                            $stores_remark_display = 'Yes';
                        } elseif($key->stores_remark == 'N') {
                            $stores_remark_display = 'No';
                        } else {
                            $stores_remark_display = htmlspecialchars($key->stores_remark);
                        }
                    }
                ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="col-sr"><?php echo $i; ?></td>
                    <td class="col-product"><?php echo htmlspecialchars($product_display); ?></td>
                    <td class="col-description text-left">

                        <?php
                        $description = '';
                        if (!empty($key->description)) {
                            $description = html_entity_decode($key->description, ENT_QUOTES, 'UTF-8');
                            // Convert paragraph tags to line breaks and keep limited formatting tags
                            $description = str_replace(['<p>', '</p>'], ['', '<br>'], $description);
                            $description = preg_replace('/<p[^>]*>/', '', $description);
                            $description = preg_replace('/<\/p>/', '<br>', $description);
                            $description = strip_tags($description, '<br><strong><b><em><i><u>');
                            $description = trim($description);
                        }
                        echo $description;
                        ?>
                       </td>
                    <td class="col-qty"><?php echo isset($key->quantity) ? number_format($key->quantity) : ''; ?></td>
                    <td class="col-unit"><?php echo $unit_name; ?></td>
                    <td class="col-tag"><?php echo isset($key->tag_no) ? htmlspecialchars($key->tag_no) : ''; ?></td>
                    <td class="col-scope">
                        <?php
                        $scope = '';
                        if (!empty($key->scope)) {
                            $scope = html_entity_decode($key->scope, ENT_QUOTES, 'UTF-8');
                            $scope = str_replace(['<p>', '</p>'], ['', '<br>'], $scope);
                            $scope = preg_replace('/<p[^>]*>/', '', $scope);
                            $scope = preg_replace('/<\/p>/', '<br>', $scope);
                            $scope = strip_tags($scope, '<br><strong><b><em><i><u>');
                            $scope = trim($scope);
                        }
                        echo $scope;
                        ?>
                    </td>
                    <td class="col-stores"><?php echo $stores_remark_display; ?></td>
                    <td class="col-price">
                        <?php echo isset($key->price) ? number_format((float)$key->price, 2) : ''; ?>
                    </td>
                    <td class="col-remark">
                        <?php
                        $remark = '';
                        if (!empty($key->remark)) {
                            $remark = html_entity_decode($key->remark, ENT_QUOTES, 'UTF-8');
                            $remark = str_replace(['<p>', '</p>'], ['', '<br>'], $remark);
                            $remark = preg_replace('/<p[^>]*>/', '', $remark);
                            $remark = preg_replace('/<\/p>/', '<br>', $remark);
                            $remark = strip_tags($remark, '<br><strong><b><em><i><u>');
                            $remark = trim($remark);
                        }
                        echo $remark;
                        ?>
                    </td>

                </tr>  
                <?php
                    $i++;
                endforeach;

                $formatted_total_qty = (fmod($total_qty, 1.0) == 0.0)
                    ? number_format($total_qty, 0)
                    : number_format($total_qty, 2);
                
                for ($b = 0; $b < $blank_rows_needed; $b++):
                ?>
                <tr class="empty-row">
                    <td class="col-sr">&nbsp;</td>
                    <td class="col-product">&nbsp;</td>
                    <td class="col-description">&nbsp;</td>
                    <td class="col-qty">&nbsp;</td>
                    <td class="col-unit">&nbsp;</td>
                    <td class="col-tag">&nbsp;</td>
                    <td class="col-scope">&nbsp;</td>
                    <td class="col-stores">&nbsp;</td>
                    
                    <td class="col-remark">&nbsp;</td>
                </tr>
                <?php
                endfor;
                
            else:
                ?>
                <tr class="item-row">
                    <td colspan="10" class="text-center" style="padding: 10px;">No items found</td>
                </tr>
                <?php
                for ($b = 1; $b < $target_total_rows; $b++):
                ?>
                <tr class="empty-row">
                    <td class="col-sr">&nbsp;</td>
                    <td class="col-product">&nbsp;</td>
                    <td class="col-description">&nbsp;</td>
                    <td class="col-qty">&nbsp;</td>
                    <td class="col-unit">&nbsp;</td>
                    <td class="col-tag">&nbsp;</td>
                    <td class="col-scope">&nbsp;</td>
                    <td class="col-stores">&nbsp;</td>
                    <td class="col-price">&nbsp;</td>
                    <td class="col-remark">&nbsp;</td>

                </tr>
                <?php
                endfor;
            endif; 
            ?>

            <tr class="total-section">
                <td colspan="4" class="text-right"><b>Total Qty:</b></td>
                <td colspan="1"><?php echo isset($formatted_total_qty) ? $formatted_total_qty : '0'; ?></td>
                <td colspan="6">&nbsp;</td>
            </tr>


            <!-- Notes Row - Same as BOM structure -->
            <tr class="items-header" style="background: #e0e0e0;">
                <th colspan="10" style="text-align: left;">Notes:</th>
            </tr>
            <tr>
                <td colspan="10" style="padding: 6px 5px;"><?php echo isset($joborder_data_group['note']) ? nl2br(htmlspecialchars($joborder_data_group['note'])) : ''; ?></td>
            </tr>

            <!-- Signature Row - Same as BOM with 6 and 4 colspan -->
            <tr>
                <td colspan="6" class="signature" style="vertical-align: bottom; text-align: center; height: 80px;">
                    <?php if (isset($stamp) && $stamp == "yes") { ?>
                        <div><img class="stamp-img" src="<?php echo base_url() . $settings['company_stamp']; ?>" alt="Stamp"></div>
                    <?php } ?>
                    <div>Prepared By</div>
                    <div style="margin-top: 20px;"><?php echo isset($joborder_data_group['prepare_by']) ? htmlspecialchars($joborder_data_group['prepare_by']) : '&nbsp;'; ?></div>
                </td>
                <td colspan="3" class="signature" style="vertical-align: bottom; text-align: center; height: 80px;">
                    <div>Approved By</div>
                    <div style="margin-top: 20px;">
                        <?php 
                        $approved_by = isset($joborder_data_group['approved_by_name']) ? $joborder_data_group['approved_by_name'] : (isset($joborder_data_group['approved_by']) ? $joborder_data_group['approved_by'] : '');
                        if($approved_by == '0' || empty($approved_by)) {
                            echo '&nbsp;';
                        } else {
                            echo htmlspecialchars($approved_by);
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            This is a Computer Generated Job Order<br>
            <?php echo $settings['company_name']; ?> | <?php echo $settings['address']; ?>
        </div>
    </div>
</body>
</html>
