<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Acceptance Letter - <?php echo isset($oc['number_fk']) ? $oc['number_fk'] : 'OA'; ?></title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .outer-box {
            border: 4px double #000;
            padding: 30px 40px;
            margin: 0 auto;
            min-height: 1000px;
            box-sizing: border-box;
            position: relative;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-logo {
            width: 85px;
            vertical-align: middle;
        }
        .header-logo img {
            max-width: 80px;
            height: auto;
        }
        .company-title {
            font-size: 20pt;
            font-weight: bold;
            color: #0d2b5c;
            font-family: Calibri, Arial, sans-serif;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 11pt;
            color: #c00000;
            font-style: italic;
            font-weight: bold;
            font-family: Calibri, Arial, sans-serif;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0 25px 0;
        }
        .ref-date-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        .subject-line {
            font-size: 11pt;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .content-para {
            margin-bottom: 16px;
            text-align: justify;
            font-size: 11pt;
        }
        .terms-list {
            margin: 18px 0;
            padding-left: 0;
            list-style: none;
        }
        .terms-list li {
            margin-bottom: 12px;
            font-size: 11pt;
            line-height: 1.5;
        }
        .terms-label {
            font-weight: bold;
        }
        .signature-section {
            margin-top: 35px;
            font-size: 11pt;
        }
        .stamp-box {
            width: 120px;
            min-height: 75px;
            margin: 8px 0;
        }
        .stamp-box img {
            max-width: 120px;
            max-height: 75px;
        }
        .footer-section {
            position: absolute;
            bottom: 25px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 10pt;
            line-height: 1.4;
        }
        .footer-company {
            font-weight: bold;
            font-size: 13pt;
            color: #3b1660;
            margin-bottom: 4px;
        }
        .no-print {
            margin-bottom: 15px;
            text-align: right;
        }
        .btn-print {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-close {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }
        @media print {
            .no-print { display: none !important; }
            .outer-box { border: 4px double #000 !important; }
            @page { margin: 0; }
            body { padding: 10mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> Print / Download PDF</button>
        <button class="btn-close" onclick="window.close()">Close</button>
    </div>

    <div class="outer-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <?php 
                        $logo_path = !empty($settings['logo']) ? base_url() . $settings['logo'] : base_url() . 'uploads/xform-logo.jpg';
                    ?>
                    <img src="<?php echo $logo_path; ?>" alt="Company Logo" onerror="this.style.display='none';">
                </td>
                <td style="padding-left: 15px;">
                    <div class="company-title">
                        <?php echo !empty($settings['company_name']) ? strtoupper($settings['company_name']) : 'UWS ENVIRO-TECH PVT LTD'; ?>
                    </div>
                    <div class="company-subtitle">
                        <?php echo !empty($settings['tagline']) ? $settings['tagline'] : 'Ultimate Technologies for Fluid Automation'; ?>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Document Title -->
        <div class="doc-title">
            Order Acceptance Letter
        </div>

        <!-- Ref No and Date -->
        <table class="ref-date-table">
            <tr>
                <td align="left">
                    <strong>Ref. No.</strong> <span><?php echo !empty($oc['number_fk']) ? $oc['number_fk'] : 'UWSEPL/OA/25-26/011'; ?></span>
                </td>
                <td align="right">
                    <strong>Date:</strong> <span><?php echo !empty($oc['date']) ? date('d/m/Y', strtotime($oc['date'])) : date('d/m/Y'); ?></span>
                </td>
            </tr>
        </table>

        <!-- Subject -->
        <div class="subject-line">
            <strong>Subject:</strong> Order Acceptance Letter against <?php 
                if (!empty($oc['subject'])) {
                    echo $oc['subject'];
                } else if (!empty($oc_detail[0]['description'])) {
                    echo $oc_detail[0]['description'];
                } else {
                    echo 'DOSING SYSTEM for WTP Plant (Hindalco) (W-26004)';
                }
            ?>.
        </div>

        <!-- Body Paragraphs -->
        <div class="content-para">Dear Sir,</div>
        <div class="content-para">We thank you for valuable opportunity provided to us.</div>
        <div class="content-para">
            We acknowledge with thanks for the receipt of valuable PO No: <strong><?php echo !empty($oc['po_reference']) ? $oc['po_reference'] : '4520232398'; ?></strong> 
            DT: <strong><?php echo !empty($oc['po_date']) ? date('d.m.Y', strtotime($oc['po_date'])) : (!empty($oc['date']) ? date('d.m.Y', strtotime($oc['date'])) : '31.12.2025'); ?></strong> 
            for <?php echo !empty($oc_detail[0]['description']) ? $oc_detail[0]['description'] : 'Dosing System'; ?>.
        </div>
        <div class="content-para">
            We hereby acknowledge receipt of PO & accept with basic amount of <strong>Rs. <?php echo isset($oc['sub_total']) ? number_format($oc['sub_total'], 2) : '31,75,000 /-'; ?></strong> 
            <strong>+<?php 
                if (isset($oc['sub_total']) && $oc['sub_total'] > 0 && isset($oc['tax_amount'])) {
                    echo round(($oc['tax_amount'] / $oc['sub_total']) * 100);
                } else {
                    echo '18';
                }
            ?>% GST extra</strong> payable at actual on basic with following standard terms & conditions.
        </div>

        <!-- Terms and Conditions -->
        <ol class="terms-list">
            <li>
                <span class="terms-label">1) Price Basis:</span> 
                <?php echo !empty($oc['price_basis']) ? $oc['price_basis'] : 'Ex-works Talwade, Pune.'; ?>
            </li>
            <li>
                <span class="terms-label">2) Payment Terms:</span> 
                <?php echo !empty($oc['payment_terms']) ? $oc['payment_terms'] : '90% with full tax in 45 Days & 10% against submission of PBG valid for warranty period.'; ?>
            </li>
            <li>
                <span class="terms-label">3) Transportation Charges:</span> 
                <?php echo !empty($oc['transportation_charges']) ? $oc['transportation_charges'] : 'Extra to PRAJ scope.'; ?>
            </li>
            <li>
                <span class="terms-label">4) Dispatch Date:</span> 
                On or before <?php echo !empty($oc['delivery_date']) ? date('d.m.Y', strtotime($oc['delivery_date'])) : '10.03.2026'; ?>.
            </li>
            <li>
                <span class="terms-label">5) Service Charges:</span> 
                <?php echo !empty($oc['service_charges']) ? $oc['service_charges'] : 'Rs.5000/- will be charged extra per day per Engineer basis.'; ?>
            </li>
            <li>
                <span class="terms-label">6) Warranty:</span> 
                <?php echo !empty($oc['warranty']) ? $oc['warranty'] : '30 months from date of dispatch or 24 months from date of commissioning whichever is earlier against any manufacturing defect.'; ?>
            </li>
        </ol>

        <div class="content-para">We will start further proceedings on priority basis.</div>
        <div class="content-para">Thanking you.</div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div>For <strong><?php echo !empty($settings['company_name']) ? $settings['company_name'] : 'UWS Enviro-Tech Pvt Ltd'; ?></strong></div>
            <div class="stamp-box">
                <?php 
                    $stamp = !empty($settings['company_stamp']) ? $settings['company_stamp'] : (!empty($settings['stamp_signature']) ? $settings['stamp_signature'] : '');
                ?>
                <?php if (!empty($stamp)): ?>
                    <img src="<?php echo base_url() . $stamp; ?>" alt="Stamp & Signature">
                <?php else: ?>
                    <svg width="100" height="75" viewBox="0 0 100 75">
                        <circle cx="45" cy="38" r="30" stroke="#1d4ed8" stroke-width="1.5" fill="none" stroke-dasharray="3,1" />
                        <circle cx="45" cy="38" r="24" stroke="#1d4ed8" stroke-width="1" fill="none" />
                        <path id="stampArc" d="M 22,38 A 22,22 0 1,1 68,38" fill="none" />
                        <text font-size="4.5" font-family="sans-serif" font-weight="bold" fill="#1d4ed8">
                            <textPath href="#stampArc" startOffset="50%" text-anchor="middle"><?php echo !empty($settings['company_name']) ? strtoupper($settings['company_name']) : 'UWS ENVIRO-TECH PVT LTD'; ?></textPath>
                        </text>
                        <text x="45" y="41" font-size="7.5" font-family="cursive" font-weight="bold" fill="#1d4ed8" text-anchor="middle">Signed</text>
                        <text x="45" y="52" font-size="4.5" font-family="sans-serif" fill="#1d4ed8" text-anchor="middle">PUNE</text>
                    </svg>
                <?php endif; ?>
            </div>
            <div><strong>Authorized Signatory</strong></div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <div class="footer-company">
                <?php echo !empty($settings['company_name']) ? $settings['company_name'] : 'UWS Enviro-Tech Pvt. Ltd'; ?>
            </div>
            <div style="font-weight: bold; color: #000;">
                <?php 
                    $addr = !empty($settings['address']) ? $settings['address'] : (!empty($settings['company_address']) ? $settings['company_address'] : 'Plot No. 19/C, D-1 Block, Shop No. 342, 3<sup>rd</sup> Floor, HEUU Industrial Spaces, MIDC Chinchwad, Pune-411019.');
                    echo $addr;
                ?>
            </div>
            <div style="margin-top: 2px;">
                E-mail: <span style="color: #0000ff; text-decoration: underline;"><?php echo !empty($settings['email']) ? $settings['email'] : (!empty($settings['company_email']) ? $settings['company_email'] : 'projects@ultimatewater.in'); ?></span>
                &nbsp;
                Website: <span style="color: #0000ff; text-decoration: underline;"><?php echo !empty($settings['website']) ? $settings['website'] : 'www.ultimatewater.in'; ?></span>
            </div>
            <div style="font-weight: bold; margin-top: 2px;">
                Phone: <?php echo !empty($settings['mobile']) ? $settings['mobile'] : (!empty($settings['company_mobile']) ? $settings['company_mobile'] : '020 29528571'); ?>
            </div>
        </div>
    </div>

</body>
</html>
