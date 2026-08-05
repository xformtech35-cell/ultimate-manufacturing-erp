<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8">
    <title>BOM</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .header-section { margin-bottom: 10px; }
        .info-row { margin-bottom: 5px; }
        .company-info { font-weight: bold; }
        .bom-title { font-size: 18px; font-weight: bold; text-align: center; margin: 10px 0; }
        .notes { margin-top: 20px; }
        .footer { font-size: 9px; text-align: center; margin-top: 20px; }
        .signature-section td { vertical-align: top; padding-top: 40px; }
        img { max-height: 80px; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <table>
        <!-- COMPANY HEADER -->
        <tr>
            <td colspan="9" style="text-align: center; padding: 10px;">
                <?php if (isset($settings['company_logo']) && !empty($settings['company_logo'])): ?>
                    <img src="<?php echo base_url() . $settings['company_logo']; ?>" alt="Logo">
                <?php endif; ?>
                <div style="font-size: 16px; font-weight: bold; margin-top: 5px;">
                    <?php echo isset($settings['company_name']) ? $settings['company_name'] : ''; ?>
                </div>
                <div class="company-info">
                    GST: <?php echo isset($settings['company_gst']) ? $settings['company_gst'] : ''; ?> | 
                    PAN: <?php echo isset($settings['company_pan']) ? $settings['company_pan'] : ''; ?><br>
                    Mobile: <?php echo isset($settings['mobile']) ? $settings['mobile'] : ''; ?> | 
                    <?php echo isset($settings['email']) ? $settings['email'] : ''; ?><br>
                    <?php echo isset($settings['address']) ? $settings['address'] : ''; ?>
                </div>
            </td>
        </tr>

        <!-- PROJECT & DETAILS HEADER (2-column layout like screen) -->
        <tr>
            <td colspan="5" style="vertical-align: top;">
                <div class="header-section">
                    <?php 
                    $session_data_head1 = $this->session->userdata('session_data_head');
                    $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
                    if ($_has_project_master): 
                    ?>
                    <div class="info-row"><strong>Project Code:</strong> <?php echo isset($bom_data_group['project_code']) ? $bom_data_group['project_code'] : 'N/A'; ?></div>
                    <?php endif; ?>
                    <div class="info-row"><strong>Customer Code:</strong> <?php echo isset($bom_data_group['customer_code']) ? $bom_data_group['customer_code'] : 'N/A'; ?></div>
                    <div class="info-row"><strong>System:</strong> <?php echo isset($bom_data_group['system']) ? $bom_data_group['system'] : 'N/A'; ?></div>
                    <div class="info-row"><strong>Location:</strong> <?php echo isset($bom_data_group['location']) ? $bom_data_group['location'] : 'N/A'; ?></div>
                    <div class="info-row"><strong>Capacity:</strong> <?php echo isset($bom_data_group['capacity']) ? $bom_data_group['capacity'] : 'N/A'; ?></div>
                    <div class="info-row"><strong>Project Quantity:</strong> <?php echo isset($bom_data_group['project_qty']) ? $bom_data_group['project_qty'] : 'N/A'; ?></div>
                    <div class="info-row"><strong>OC Number:</strong> <?php echo isset($bom_data_group['oc_number']) ? $bom_data_group['oc_number'] : 'N/A'; ?></div>
                </div>
            </td>
            <td colspan="4" style="vertical-align: top;">
                <div class="header-section">
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">BOM Details:</div>
                    <div class="info-row"><strong>BOM Number:</strong> <?php echo isset($bom_data_group['number']) ? $bom_data_group['number'] : ''; ?></div>
                    <div class="info-row"><strong>BOM Date:</strong> <?php echo isset($bom_data_group['date']) && $bom_data_group['date'] != '0000-00-00' ? date('d-m-Y', strtotime($bom_data_group['date'])) : 'N/A'; ?></div>
                    <div class="info-row"><strong>BOM Status:</strong> 
                        <?php 
                        $statusArr = array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled');
                        echo (isset($bom_data_group['status']) && isset($statusArr[$bom_data_group['status']])) ? $statusArr[$bom_data_group['status']] : 'Draft';
                        ?>
                    </div>
                    <div class="info-row"><strong>Company Name:</strong> <?php echo isset($bom_data_group['company_name']) ? $bom_data_group['company_name'] : 'N/A'; ?></div>
                    <div class="info-row"><strong>Prepared By:</strong> <?php echo isset($bom_data_group['prepare_by']) ? $bom_data_group['prepare_by'] : 'N/A'; ?></div>
                    <div class="info-row"><strong>Approved By:</strong> <?php echo isset($bom_data_group['approved_by']) ? $bom_data_group['approved_by'] : 'N/A'; ?></div>
                </div>
            </td>
        </tr>

        <!-- BOM ITEMS TABLE - EXACT MATCH TO SCREEN -->
        <tr>
            <td colspan="9" style="padding: 0;">
                <table>
                    <tr>
                        <th style="width: 5%;">Sr.No.</th>
                        <th style="width: 25%;">Product Name</th>
                        <th style="width: 6%;">QTY</th>
                        <th style="width: 6%;">Unit</th>
                        <th style="width: 10%;">Tag No.</th>
                        <th style="width: 12%;">Scope</th>
                        <th style="width: 12%;">Stores Remark (Y/N)</th>
                        <th style="width: 15%;">Remark</th>
                        <th style="width: 9%;">Status</th>
                    </tr>
                    <?php if(isset($show_bom) && !empty($show_bom)): 
                        $i = 1;
                        foreach($show_bom as $key): ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i++; ?></td>
                            <td><?php echo isset($key->equipment) ? htmlspecialchars($key->equipment) : ''; ?></td>
                            <td style="text-align: center;"><?php echo isset($key->quantity) ? $key->quantity : ''; ?></td>
                            <td style="text-align: center;"><?php echo isset($key->unit) ? htmlspecialchars($key->unit) : ''; ?></td>
                            <td><?php echo isset($key->tag_no) ? htmlspecialchars($key->tag_no) : ''; ?></td>
                            <td><?php echo isset($key->scope) ? htmlspecialchars($key->scope) : ''; ?></td>
                            <td style="text-align: center;">
                                <?php 
                                if(isset($key->stores_remark)):
                                    echo $key->stores_remark == 'Y' ? 'Yes' : ($key->stores_remark == 'N' ? 'No' : 'N/A');
                                endif;
                                ?>
                            </td>
                            <td><?php echo isset($key->remark) ? htmlspecialchars($key->remark) : ''; ?></td>
                            <td style="text-align: center;">
                                <?php 
                                $statusArr = array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled');
                                echo (isset($key->status_i) && isset($statusArr[$key->status_i])) ? $statusArr[$key->status_i] : '';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; 
                    else: ?>
                        <tr><td colspan="9" style="text-align: center;">No items found</td></tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>

        <!-- NOTES SECTION -->
        <?php if(isset($bom_data_group['note']) && !empty($bom_data_group['note'])): ?>
        <tr>
            <td colspan="9" class="notes">
                <strong>Notes:</strong><br><?php echo nl2br(htmlspecialchars($bom_data_group['note'])); ?>
            </td>
        </tr>
        <?php endif; ?>

        <!-- SIGNATURES & FOOTER -->
        <tr>
            <td colspan="4" class="signature-section">
                <strong>Prepared By:</strong><br>
                <?php echo isset($bom_data_group['prepare_by']) ? $bom_data_group['prepare_by'] : ''; ?>
            </td>
            <td colspan="5" class="signature-section">
                <strong>Approved By:</strong><br>
                <?php echo isset($bom_data_group['approved_by_name']) ? $bom_data_group['approved_by_name'] : (isset($bom_data_group['approved_by']) ? $bom_data_group['approved_by'] : 'N/A'); ?>
            </td>
        </tr>
        <tr>
            <td colspan="9" class="footer">
                <?php echo isset($settings['bom_footer']) ? $settings['bom_footer'] : ''; ?><br>
                <em>This is Computer Generated BOM</em>
            </td>
        </tr>
    </table>
</body>
</html>
