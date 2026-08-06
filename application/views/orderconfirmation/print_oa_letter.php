<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Acceptance Letter - <?php echo isset($oc['number_fk']) ? $oc['number_fk'] : 'OA'; ?></title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .outer-box {
            border: 3px double #000;
            padding: 25px 30px;
            margin: 0 auto;
            min-height: 950px;
            box-sizing: border-box;
            position: relative;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .header-logo {
            width: 80px;
            vertical-align: top;
        }
        .header-logo img {
            max-width: 75px;
            height: auto;
        }
        .company-title {
            font-size: 22px;
            font-weight: bold;
            color: #0d2b5c;
            font-family: Arial, sans-serif;
            text-transform: uppercase;
        }
        .company-subtitle {
            font-size: 13px;
            color: #b30000;
            font-style: italic;
            font-weight: bold;
            font-family: Arial, sans-serif;
        }
        .doc-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 20px 0;
            text-transform: capitalize;
        }
        .ref-date-table {
            width: 100%;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .subject-line {
            font-size: 13.5px;
            margin-bottom: 15px;
        }
        .content-para {
            margin-bottom: 12px;
            text-align: justify;
        }
        .terms-list {
            margin: 15px 0;
            padding-left: 0;
            list-style: none;
        }
        .terms-list li {
            margin-bottom: 8px;
        }
        .terms-label {
            font-weight: bold;
        }
        .signature-section {
            margin-top: 30px;
        }
        .stamp-box {
            width: 120px;
            height: 70px;
            margin: 10px 0;
            display: inline-block;
        }
        .stamp-box img {
            max-width: 120px;
            max-height: 70px;
        }
        .footer-section {
            margin-top: 40px;
            text-align: center;
            font-size: 11.5px;
            line-height: 1.4;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .footer-company {
            font-weight: bold;
            font-size: 13px;
            color: #0d2b5c;
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
            .outer-box { border: 3px double #000 !important; }
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
                <td style="padding-left: 10px;">
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
                    Ref. No. <span><?php echo !empty($oc['number_fk']) ? $oc['number_fk'] : 'UWSEPL/OA/25-26/011'; ?></span>
                </td>
                <td align="right">
                    Date: <span><?php echo !empty($oc['date']) ? date('d/m/Y', strtotime($oc['date'])) : date('d/m/Y'); ?></span>
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
                <?php if (!empty($settings['stamp_signature'])): ?>
                    <img src="<?php echo base_url() . $settings['stamp_signature']; ?>" alt="Stamp & Signature">
                <?php endif; ?>
            </div>
            <div><strong>Authorized Signatory</strong></div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <div class="footer-company"><?php echo !empty($settings['company_name']) ? $settings['company_name'] : 'UWS Enviro-Tech Pvt. Ltd'; ?></div>
            <div>
                <?php echo !empty($settings['company_address']) ? $settings['company_address'] : 'Plot No. 19/C, D-1 Block, Shop No. 342, 3rd Floor, HEUU Industrial Spaces, MIDC Chinchwad, Pune-411019.'; ?>
            </div>
            <div>
                E-mail: <?php echo !empty($settings['company_email']) ? $settings['company_email'] : 'projects@ultimatewater.in'; ?> 
                &nbsp;|&nbsp; 
                Website: <?php echo !empty($settings['website']) ? $settings['website'] : 'www.ultimatewater.in'; ?>
                <?php if (!empty($settings['company_mobile'])): ?>
                    &nbsp;|&nbsp; Phone: <?php echo $settings['company_mobile']; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
