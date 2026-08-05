<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>BOM - <?php echo isset($bom_data_group['number']) ? $bom_data_group['number'] : ''; ?></title>
    <meta name="description" content="BOM print page">
    <meta name="viewport" content="width=device-width">
    <style>
        /* Section Heading Row Styling */
        .bom-heading-row td {
            background-color: #e8e0f0 !important;
            color: #5a3d8a !important;
            font-weight: bold !important;
            font-size: 10px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 10px !important;
            border: 0.5px solid #000 !important;
            vertical-align: middle !important;
            text-align: left !important;
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
            padding: 10px;
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

        /* Project/BOM Details - Separate Table */
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
        
        /* Remove top and bottom borders from tr elements only */
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

        /* Column width classes - optimized for BOM */
        .col-sr { width: 3%; text-align: center; }
        .col-code { width: 8%; text-align: center; }
        .col-product { width: 12%; text-align: center; }
        .col-description { width: 22%; text-align: center; }
        .col-qty { width: 3%; text-align: center; }
        .col-unit { width: 4%; text-align: center; }
        .col-tag { width: 4%; text-align: center; }
        .col-scope { width: 13%; text-align: center; }
        .col-stores { width: 7%; text-align: center; }
        .col-remark { width: 17%; text-align: center; }
        .col-status { width: 7%; text-align: center; }

        /* Text alignments */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
        }

        /* Label styling */
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

        /* Section headings */
        .section-heading {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 4px;
            color: #003366;
            padding-bottom: 2px;
        }
        
        /* Item rows styling */
        .item-row td {
            border-top: 0.5px solid #000;
            border-bottom: 0.5px solid #000;
            height: 33px;
        }

        /* First item row - keep top border */
        .item-row:first-child td {
            border-top: 0.5px solid #000;
        }

        /* Last item row - keep bottom border */
        .last-item-row td {
            border-bottom: 0.5px solid #000;
            height: 33px;
        }

        /* Nested table in details - no borders */
        .no-border-table {
            width: 100%;
            border-collapse: collapse;
        }

        .no-border-table td {
            border: none;
            padding: 1px;
        }
        
        /* Empty row styling - no borders */
        .empty-row td {
            border-top: none;
            border-bottom: none;
            height: 33px;
        }

        /* Bank details - vertical layout */
        .bank-details {
            vertical-align: top;
        }
        
        .bank-details-content {
            line-height: 1.8;
        }
        
        .bank-details-content .label-sm {
            min-width: 180px;
            display: inline-block;
            font-weight: bold;
            color: #444;
        }

        .bank-details-content div {
            margin-bottom: 2px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* For lines without labels */
        .bank-details-content div:not(:has(.label-sm)) {
            margin-left: 0;
            font-weight: normal;
        }

        /* For the cheque line */
        .bank-details-content div:last-child {
            margin-top: 5px;
            padding-top: 2px;
        }

        /* Signature section */
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

        /* Footer */
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
        
        /* Note/message styling */
        .message-box {
            padding: 6px 5px;
            font-style: italic;
            color: #333;
        }
        
        /* Status colors */
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
        $colspan = "9";
        $colspan1 = "4";
        $colspan2 = "5";
        
        // Calculate how many blank rows to add
        $target_total_rows = 18;
        $items_count = !empty($show_bom) ? count($show_bom) : 0;
        $blank_rows_needed = max(0, $target_total_rows - $items_count);
        
        // Status array
        $statusArr = array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled');
        $statusClass = array(1 => 'status-draft', 2 => 'status-sent', 3 => 'status-viewed', 4 => 'status-approved', 5 => 'status-rejected', 6 => 'status-canceled');
        ?>

        <!-- Company Header - Separate Table -->
        <caption>BILL OF MATERIALS (BOM)</caption>
        <table class="company-header">
            <tr>
                <td colspan="2" align="center" valign="middle" style="border-bottom: 0.5px solid #000; padding: 8px; text-align: center;">
                    <img class="company-logo" src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo" style="max-height: 85px; width: auto; display: block; margin: 0 auto;">
                </td>
            </tr>
            <tr>
                <td width="50%" valign="top" style="border-right: 0.5px solid #000;">
                    <div class="company-name" style="font-weight: bold; font-size: 10px; margin-bottom: 3px;"><?php echo $settings['company_name']; ?></div>
                    <div><span class="label">GST Number :</span> <?php echo strtoupper($settings['company_gst']); ?></div>
                    <div><span class="label">PAN Number :</span> <?php echo strtoupper($settings['company_pan']); ?></div>
                </td>
                <td width="50%" valign="top">
                    <div><span class="label">Mobile :</span> <?php echo $settings['mobile']; ?></div>
                    <div><span class="label">Email :</span> <?php echo $settings['email']; ?></div>
                    <div><span class="label">Address :</span> <?php echo $settings['address']; ?></div>
                </td>
            </tr>
        </table>

        <!-- Project and BOM Details - Separate Table -->
        <table class="details-section">
            <tr>
                <!-- Project Details (Left) -->
                <td width="50%" valign="top">
                    <div class="section-heading">PROJECT DETAILS</div>
                    <?php 
                    $session_data_head1 = $this->session->userdata('session_data_head');
                    $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
                    if ($_has_project_master): 
                    ?>
                    <div><span class="label-sm">Project Code:</span> <?php echo isset($bom_data_group['project_code']) ? $bom_data_group['project_code'] : 'N/A'; ?></div>
                    <?php endif; ?>
                    <div><span class="label-sm">Customer Code:</span> <?php echo isset($bom_data_group['customer_code']) ? $bom_data_group['customer_code'] : 'N/A'; ?></div>
                    <div><span class="label-sm">Customer Name:</span> <?php echo isset($bom_data_group['company_name']) ? $bom_data_group['company_name'] : 'N/A'; ?></div>
                    <div><span class="label-sm">Contact Person:</span> <?php echo isset($bom_data_group['fullname']) ? $bom_data_group['fullname'] : 'N/A'; ?></div>
                    <div><span class="label-sm">BOM Status:</span> 
                        <span class="<?php echo (isset($bom_data_group['status']) && isset($statusClass[$bom_data_group['status']])) ? $statusClass[$bom_data_group['status']] : ''; ?>">
                            <?php echo (isset($bom_data_group['status']) && isset($statusArr[$bom_data_group['status']])) ? $statusArr[$bom_data_group['status']] : 'Draft'; ?>
                        </span>
                    </div>
                </td>
                
                <!-- BOM Details (Right) -->
                <td width="50%" valign="top">
                    <div class="section-heading">BOM DETAILS</div>
                    <table class="no-border-table" style="font-size:8.5px;">
                        <tr><td class="label-sm">BOM Number:</td><td><?php echo isset($bom_data_group['number']) ? $bom_data_group['number'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">BOM Date:</td><td><?php echo isset($bom_data_group['date']) && $bom_data_group['date'] != '0000-00-00' ? date('d-m-Y', strtotime($bom_data_group['date'])) : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">System:</td><td><?php echo isset($bom_data_group['system']) ? $bom_data_group['system'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Location:</td><td><?php echo isset($bom_data_group['location']) ? $bom_data_group['location'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Capacity:</td><td><?php echo isset($bom_data_group['capacity']) ? $bom_data_group['capacity'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Project Qty:</td><td><?php echo isset($bom_data_group['project_qty']) ? $bom_data_group['project_qty'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">OC Number:</td><td><?php echo isset($bom_data_group['oc_number']) ? $bom_data_group['oc_number'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Created By:</td><td><?php echo isset($bom_data_group['prepare_by']) ? $bom_data_group['prepare_by'] : 'N/A'; ?></td></tr>
                        <tr><td class="label-sm">Approved By:</td><td><?php echo isset($bom_data_group['approved_by_name']) ? $bom_data_group['approved_by_name'] : 'N/A'; ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table id="dynamic_field">
            
            <!-- Items Table Header -->
            <tr class="items-header">
                <th class="col-sr">Sr.</th>
                <th class="col-code">Item Code</th>
                <th class="col-product">Product Name</th>
                <th class="col-description">Description</th>
                <th class="col-qty">QTY</th>
                <th class="col-unit">Unit</th>
                <th class="col-tag">Tag No.</th>
                <th class="col-scope">Scope</th>
                <th class="col-stores">Stores Remark</th>
                <th class="col-remark">Remark</th>
                <th class="col-status">Status</th>
            </tr>

            <!-- Items Loop -->
            <?php
            if(isset($show_bom) && !empty($show_bom)):
                $i = 1;
                $item_counter = 0;
                $current_section_is_peach_pdf = false;
                
                // Check if database records already contain headings
                $hasHeadingsInDb = false;
                foreach ($show_bom as $check_key) {
                    if (isset($check_key->product_name) && $check_key->product_name === '__HEADING__') {
                        $hasHeadingsInDb = true;
                        break;
                    }
                }
                
                // Auto-generate headings if no heading rows exist in DB
                if (!$hasHeadingsInDb):
                ?>
                <tr style="background-color: #c3d69b;">
                    <td colspan="11" style="background-color: #c3d69b; color: #000000; font-family: Calibri, sans-serif; font-size: 11px; font-weight: bold; text-align: left; padding: 6px 10px; border: 0.5px solid #000;">
                        <strong>EQUIPMENTS</strong>
                    </td>
                </tr>
                <?php
                    $subheading = !empty($bom_data_group['bom_subheading']) ? $bom_data_group['bom_subheading'] : (isset($bom_data_group['system']) ? $bom_data_group['system'] : '');
                    if (!empty($subheading)):
                ?>
                <tr style="background-color: #e6e0ed;">
                    <td colspan="11" style="background-color: #e6e0ed; color: #ff0000; font-family: Cambria, serif; font-size: 10.5px; font-weight: bold; text-align: left; padding: 6px 10px; border: 0.5px solid #000;">
                        <strong><?php echo htmlspecialchars(strtoupper($subheading)); ?></strong>
                    </td>
                </tr>
                <?php
                    endif;
                endif;
                
                // Display actual BOM items
                foreach ($show_bom as $key):
                    $item_counter++;
                    $row_class = ($item_counter == $items_count) ? 'last-item-row' : 'item-row';
                    
                    if (isset($key->product_name) && $key->product_name === '__HEADING__'):
                        $desc = trim($key->description ?? '');
                        
                        // Spacer row
                        if ($desc === '') {
                        ?>
                        <tr style="background-color: #ffffff; border: none; height: 12px;">
                            <td colspan="11" style="border: none; height: 12px; padding: 0;">&nbsp;</td>
                        </tr>
                        <?php
                            continue;
                        }

                        // Determine colors and apply text-transform via PHP
                        $bg = '#dbeff4'; // Default Blue
                        $fg = '#000000';
                        $font = 'Calibri, sans-serif';
                        $fontSize = '10px';
                        $displayDesc = $desc; // Default: keep as-is

                        $isMain = null;
                        if (isset($key->tag_no) && ($key->tag_no === 'MAIN' || $key->tag_no === 'SUB')) {
                            $isMain = ($key->tag_no === 'MAIN');
                        }

                        if ($isMain === true || ($isMain === null && preg_match('/SYSTEM|SPARES|COMMISSIONING|TANK FOR|EQUIPMENTS/i', $desc))) {
                            if (preg_match('/EQUIPMENTS/i', $desc)) {
                                $bg = '#c3d69b'; // Green
                                $fg = '#000000';
                                $font = 'Calibri, sans-serif';
                                $fontSize = '11px';
                            } else {
                                $bg = '#e6e0ed'; // Lavender
                                $fg = '#ff0000'; // Red
                                $font = 'Cambria, serif';
                                $fontSize = '10.5px';
                            }
                            $displayDesc = strtoupper($desc); // Apply uppercase via PHP
                            $current_section_is_peach_pdf = false;
                        } elseif ($isMain === false || ($isMain === null && preg_match('/PIPING|FITTINGS|VALVES|FLANGE|ELBOW|TEE|PIPE|CPVC|UPVC/i', $desc))) {
                            $bg = '#fdeada'; // Peach
                            $fg = '#000000';
                            $font = 'Calibri, sans-serif';
                            $fontSize = '10px';
                            $displayDesc = $desc;
                            $current_section_is_peach_pdf = true;
                        } else {
                            $bg = '#dbeff4'; // Blue
                            $fg = '#000000';
                            $font = 'Calibri, sans-serif';
                            $fontSize = '10px';
                            $displayDesc = $desc;
                            $current_section_is_peach_pdf = false;
                        }
                    ?>
                    <tr style="background-color: <?php echo $bg; ?>;">
                        <td colspan="11" style="background-color: <?php echo $bg; ?>; color: <?php echo $fg; ?>; font-family: <?php echo $font; ?>; font-size: <?php echo $fontSize; ?>; font-weight: bold; text-align: left; padding: 6px 10px; border: 0.5px solid #000;">
                            <strong><?php echo htmlspecialchars($displayDesc); ?></strong>
                        </td>
                    </tr>
                    <?php
                        continue;
                    endif;


                    // Get unit name
                    $unit_name = '';
                    if(isset($key->unit) && isset($unit_result)):
                        foreach ($unit_result as $unit): 
                            if($unit->unit == $key->unit): 
                                $unit_name = $unit->unit;
                                break;
                            endif; 
                        endforeach; 
                    endif;
                    
                    // Get status
                    $item_status = (isset($key->status_i) && isset($statusArr[$key->status_i])) ? $statusArr[$key->status_i] : '';
                    $item_status_class = (isset($key->status_i) && isset($statusClass[$key->status_i])) ? $statusClass[$key->status_i] : '';
                ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="col-sr"><?php echo $i; ?></td>
                    <td class="col-code"><?php echo isset($key->product_name) ? htmlspecialchars($key->product_name) : ''; ?></td>
                    <td class="col-product"><?php echo isset($key->item_name) ? htmlspecialchars($key->item_name) : ''; ?></td>
                       <td class="col-description" class="text-left">
                        <?php
                        $description = '';
                        if (!empty($key->description)) {
                            $description = html_entity_decode($key->description, ENT_QUOTES, 'UTF-8');
                            // Convert paragraph tags to line breaks and keep limited formatting tags
                            $description = str_replace(['<p>', '</p>'], ['', '<br>'], $description);
                            $description = preg_replace('/<p[^>]*>/', '', $description);
                            $description = preg_replace('/<\/p>/', '<br>', $description);
                            $description = nl2br($description);
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
                            // Convert paragraph tags to line breaks and keep limited formatting tags
                            $scope = str_replace(['<p>', '</p>'], ['', '<br>'], $scope);
                            $scope = preg_replace('/<p[^>]*>/', '', $scope);
                            $scope = preg_replace('/<\/p>/', '<br>', $scope);
                            $scope = nl2br($scope);
                            $scope = strip_tags($scope, '<br><strong><b><em><i><u>');
                            $scope = trim($scope);
                        }
                        echo $scope;
                        ?>
                    </td>
                    <td class="col-stores">
                        <?php 
                        if(isset($key->stores_remark)):
                            if($key->stores_remark == 'Y'):
                                echo 'Yes';
                            elseif($key->stores_remark == 'N'):
                                echo 'No';
                            else:
                                echo htmlspecialchars($key->stores_remark);
                            endif;
                        endif;
                        ?>
                    </td>
                    <td class="col-remark">
                        <?php
                        $remark = '';
                        if (!empty($key->remark)) {
                            $remark = html_entity_decode($key->remark, ENT_QUOTES, 'UTF-8');
                            // Convert paragraph tags to line breaks and keep limited formatting tags
                            $remark = str_replace(['<p>', '</p>'], ['', '<br>'], $remark);
                            $remark = preg_replace('/<p[^>]*>/', '', $remark);
                            $remark = preg_replace('/<\/p>/', '<br>', $remark);
                            $remark = nl2br($remark);
                            $remark = strip_tags($remark, '<br><strong><b><em><i><u>');
                            $remark = trim($remark);
                        }
                        echo $remark;
                        ?>
                    </td>
                    <td class="col-status <?php echo $item_status_class; ?>"><?php echo $item_status; ?></td>
                </tr>  
                <?php
                    $i++;
                endforeach;
                
                // Add empty rows to reach target count
                for ($b = 0; $b < $blank_rows_needed; $b++):
                ?>
                <tr class="empty-row">
                    <td class="col-sr">&nbsp;</td>
                    <td class="col-code">&nbsp;</td>
                    <td class="col-product">&nbsp;</td>
                    <td class="col-description">&nbsp;</td>
                    <td class="col-qty">&nbsp;</td>
                    <td class="col-unit">&nbsp;</td>
                    <td class="col-tag">&nbsp;</td>
                    <td class="col-scope">&nbsp;</td>
                    <td class="col-stores">&nbsp;</td>
                    <td class="col-remark">&nbsp;</td>
                    <td class="col-status">&nbsp;</td>
                </tr>
                <?php
                endfor;
                
            else:
                // If no items found, show message and empty rows
                ?>
                <tr>
                    <td colspan="11" class="text-center" style="padding: 10px;">No items found</td>
                </tr>
                <?php
                // Add empty rows for consistent layout
                for ($b = 1; $b < $target_total_rows; $b++):
                ?>
                <tr class="empty-row">
                    <td class="col-sr">&nbsp;</td>
                    <td class="col-code">&nbsp;</td>
                    <td class="col-product">&nbsp;</td>
                    <td class="col-description">&nbsp;</td>
                    <td class="col-qty">&nbsp;</td>
                    <td class="col-unit">&nbsp;</td>
                    <td class="col-tag">&nbsp;</td>
                    <td class="col-scope">&nbsp;</td>
                    <td class="col-stores">&nbsp;</td>
                    <td class="col-remark">&nbsp;</td>
                    <td class="col-status">&nbsp;</td>
                </tr>
                <?php
                endfor;
            endif; ?>
            


            <!-- Prepared By and Approved By - Signature Row -->
            <tr>
                <td colspan="6" class="signature" style="vertical-align: bottom; text-align: center; height: 60px;">
                    <?php if (isset($stamp) && $stamp == "yes") { ?>
                        <div><img class="stamp-img" src="<?php echo base_url() . $settings['company_stamp']; ?>"></div>
                    <?php } ?>
                    <div>Prepared By</div>
                    <div style="margin-top: 20px;"><?php echo isset($bom_data_group['prepare_by']) ? htmlspecialchars($bom_data_group['prepare_by']) : '&nbsp;'; ?></div>
                </td>
                <td colspan="5" class="signature" style="vertical-align: bottom; text-align: center; height: 60px;">
                    <div>Approved By</div>
                    <div style="margin-top: 20px;">
                        <?php 
                        $approved_by = isset($bom_data_group['approved_by_name']) ? $bom_data_group['approved_by_name'] : (isset($bom_data_group['approved_by']) ? $bom_data_group['approved_by'] : '');
                        if($approved_by == '0' || empty($approved_by)) {
                            echo '&nbsp;'; // Shows a blank space
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
            This is a Computer Generated BOM<br>
            <?php echo $settings['company_name']; ?> | <?php echo $settings['address']; ?>
        </div>
    </div>
</body>
</html>