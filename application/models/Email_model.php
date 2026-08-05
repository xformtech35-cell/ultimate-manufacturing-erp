<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Send approval notification
    // Send approval notification
    public function send_approval_notification($po_number, $approver_email, $amount, $approval_level, $po_data, $supplier_data)
    {
        // Get company data from session
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'] ?? 'XForm Technologies';
        $set_company_logo = base_url() . '/' . ($session_data_head2['company_logo'] ?? 'assets/images/logo.png');

        $set_from_email = $session_data_head2['from_email'] ?? 'procurement@xform.in';

        $subject = "Purchase Order Approval Required: " . $po_number;
        $approval_link = base_url('SupplierController/po_approvals');

        // Ensure data exists
        $supplier_name = isset($supplier_data['company_name']) ? $supplier_data['company_name'] : 'Unknown Vendor';
        $fullname = isset($supplier_data['fullname']) ? $supplier_data['fullname'] : 'Unknown Contact';
        $pr_id = isset($po_data['pr_id']) ? $po_data['pr_id'] : 'N/A';
        $po_date = isset($po_data['date']) ? date('d-m-Y', strtotime($po_data['date'])) : date('d-m-Y');
        $payment_due_date = isset($po_data['payment_due_date']) ? $po_data['payment_due_date'] : date('d-m-Y', strtotime('+15 days'));

        // Get approver title
        $approver_title = $this->get_approver_title($approval_level);

        // Format amount
        $formatted_amount = "Rs. " . number_format($amount, 2);

        $htmlContent = '
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>PO Approval Required</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <head>
        <title>Purchase Order Approval Required - ' . $set_company_name . '</title>
        <style> 
            @media (min-width: 1281px) {
                .boxs {
                    padding: 2% 10% 2% 10%; 
                    margin: 0% 20% 0% 20%;
                }
            }

            @media (min-width: 1025px) and (max-width: 1280px) {
                .boxs {
                    padding: 2% 10% 2% 10%; 
                    margin: 0% 10% 0% 10%;
                }
            }

            @media (min-width: 768px) and (max-width: 1024px) {
                .boxs {
                    padding: 2% 10% 2% 10%; 
                    margin: 0% 10% 0% 10%;
                }
            }

            @media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
                .boxs {
                    padding: 2% 10% 2% 10%; 
                    margin: 0% 20% 0% 20%;
                }
            }

            @media (min-width: 481px) and (max-width: 767px) {
                .boxs {
                    padding: 2% 10% 2% 10%; 
                    margin: 0% 0% 0% 0%;
                    text-align: center;
                }
            }

            @media (min-width: 320px) and (max-width: 480px) {
                .boxs {
                    padding: 2% 10% 2% 10%; 
                    margin: 0% 0% 0% 0%;
                    text-align: center;
                }
            }
            
            .shadows1 {    
                padding: 2% 4% 2% 4%;
                border-radius: 2px;
                line-height: 2;
                text-align: center;
                border: 1px solid #ddd;
                -webkit-box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.16);
                -moz-box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.16);
                box-shadow: 0px 0px 19px 0px rgba(0,0,0,0.16);
                background: #fff;
            }
            
            .po-details-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                text-align: left;
            }
            
            .po-details-table th {
                background-color: #f5f5f5;
                padding: 12px;
                border: 1px solid #ddd;
                font-weight: bold;
                width: 40%;
            }
            
            .po-details-table td {
                padding: 12px;
                border: 1px solid #ddd;
                width: 60%;
            }
            
            .highlight-amount {
                color: #d9534f;
                font-weight: bold;
                font-size: 18px;
            }
            
            .approval-badge {
                background-color: #f0ad4e;
                color: #fff;
                padding: 6px 12px;
                border-radius: 4px;
                font-weight: bold;
                display: inline-block;
            }
            
            .action-button {
                background-color: #5cb85c;
                border-radius: 4px;
                color: #ffffff;
                display: inline-block;
                font-family: sans-serif;
                font-size: 16px;
                font-weight: bold;
                line-height: 40px;
                text-align: center;
                text-decoration: none;
                width: 250px;
                margin: 20px 0;
            }
            
            .note-box {
                background-color: #fcf8e3;
                border: 1px solid #faebcc;
                border-radius: 4px;
                padding: 15px;
                margin: 20px 0;
                text-align: center;
                color: #8a6d3b;
            }
            
            hr {
                border: none;
                border-top: 1px solid #eee;
                margin: 20px 0;
            }
        </style>
    </head>
    <body style="background: #f8f8f8; font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
        <div class="boxs">
            <div class="shadows1">  
                <center> 
                    <img alt="' . $set_company_name . '" src="' . $set_company_logo . '" width="30%" style="max-width: 200px;">
                </center>
                
                <span style="text-decoration:none;color:#2f2f36;font-weight:bold;font-size:28px;">
                    <center>Purchase Order Approval Required</center>
                </span>
                <br>
                
                <p style="font-size: 16px;">Dear <strong>' . $approver_title . '</strong>,</p>
                
                <p style="font-size: 16px;">A new Purchase Order requires your approval. Please review the details below:</p>
                
                <h3 style="color: #2c3e50; margin-top: 20px;">PO Details:</h3>
                
                <table class="po-details-table">
                    <tr>
                        <th>PO Number:</th>
                        <td><strong style="font-size: 18px;">' . $po_number . '</strong></td>
                    </tr>
                    <tr>
                        <th>Vendor Name:</th>
                        <td>' . $supplier_name . '</td>
                    </tr>
                    <tr>
                        <th>Contact Person:</th>
                        <td>' . $fullname . '</td>
                    </tr>
                    <tr>
                        <th>Total Amount:</th>
                        <td><span class="highlight-amount">' . $formatted_amount . '</span></td>
                    </tr>
                    <tr>
                        <th>PR Reference:</th>
                        <td>' . $pr_id . '</td>
                    </tr>
                    <tr>
                        <th>PO Date:</th>
                        <td>' . $po_date . '</td>
                    </tr>
                    <tr>
                        <th>Delivery Date:</th>
                        <td>' . $payment_due_date . '</td>
                    </tr>
                    <tr>
                        <th>Approval Level:</th>
                        <td><span class="approval-badge">' . ucfirst(str_replace('_', ' ', $approval_level)) . '</span></td>
                    </tr>
                </table>
                
                <div class="note-box">
                    <strong> Important:</strong> Please review and take action on this PO within 24 hours.
                </div>
                
                <center> 
                    <a href="' . $approval_link . '" class="action-button" target="_blank">
                         Review & Approve PO
                    </a>
                </center>
                
                <p style="font-size: 14px; color: #666;">
                    If the button above doesn\'t work, please copy and paste this link into your browser:<br>
                    <span style="background: #f5f5f5; padding: 8px; border-radius: 4px; display: inline-block; margin-top: 5px; font-size: 12px; word-break: break-all;">
                        ' . $approval_link . '
                    </span>
                </p>
                
                <hr>
                
                <p style="font-size: 16px; margin-top: 20px;">
                    <strong>Message:</strong><br>
                    Please review this purchase order and take appropriate action.
                </p>
                
                <hr>
                
                <p style="font-size: 14px; color: #666;">
                    Best Regards,<br>
                    <strong>Procurement Department</strong><br>
                    ' . $set_company_name . '
                </p>
                
                <hr>
                
                <p style="text-decoration:none;color:#2f2f36; font-size: 14px;">
                    "This is an automated notification from ' . $set_company_name . ' Procurement System. If this email was sent in error, please contact" 
                    <a href="mailto:contact@xform.in" style="text-decoration:none;color:#008f9b;font-weight:bold" target="_blank">contact@xform.in</a>
                </p>
            </div>
            
            <center>
                <span style="text-decoration:none;color:#2f2f36; font-size: 12px; margin-top: 20px; display: block;">
                    Powered by 
                    <img alt="XForm Technologies" src=" ' .   $set_company_logo . '" width="8%" height="8%" style="margin-top: 1%; vertical-align: middle;">
                   ' .  $set_company_name . '
                </span>
            </center>
        </div>
    </body>
</html>';

        // Email sending
        $this->load->library('email');
        $this->email->set_mailtype("html");
        if (strpos(base_url(), 'localhost') !== false) {

    // Local server
    $this->email->from($set_from_email, $set_company_name);

} else {

    // Live server
    $this->email->from("noreply@uwsenvirotech.com", $set_company_name);

}
        $this->email->to($approver_email);
        $this->email->subject($subject);

        // Optional: Add CC if needed
        // $this->email->cc('procurement@xform.in');

        $this->email->message($htmlContent);

        if ($this->email->send()) {
            log_message('info', 'PO approval email sent successfully to ' . $approver_email . ' for PO ' . $po_number);
            return true;
        } else {
            log_message('error', 'Failed to send PO approval email to ' . $approver_email . ' for PO ' . $po_number);
            log_message('error', $this->email->print_debugger());
            return false;
        }
    }

    // Helper function to get approver title
    private function get_approver_title($approval_level)
    {
        $titles = [
            'manager' => 'Manager',
            'head_procurement' => 'Head of Procurement',
            'director' => 'Director',
            'finance' => 'Finance Manager'
        ];

        return $titles[$approval_level] ?? 'Approver';
    }

    /**
     * Send GRN approval notification email to the designated approver.
     * Parameters mirror send_approval_notification but tailored for GRN.
     */
    public function send_grn_approval_notification($grn_number, $approver_email, $amount, $approval_level, $grn_data)
    {
        // reuse company/session details as in PO notification
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'] ?? 'XForm Technologies';
        $set_company_logo = base_url() . '/' . ($session_data_head2['company_logo'] ?? 'assets/images/logo.png');
        $set_from_email = $session_data_head2['from_email'] ?? 'procurement@xform.in';

        $subject = "GRN Approval Required: " . $grn_number;
        $approval_link = base_url('GrnController/grn_approvals');

        // Ensure data exists
        $supplier_name = isset($grn_data['company_name']) ? $grn_data['company_name'] : 'Unknown Vendor';
        $fullname = isset($grn_data['fullname']) ? $grn_data['fullname'] : 'Unknown Contact';
        $grn_date = isset($grn_data['date']) ? date('d-m-Y', strtotime($grn_data['date'])) : date('d-m-Y');

        // Get approver title
        $approver_title = $this->get_approver_title($approval_level);

        $formatted_amount = "Rs. " . number_format($amount, 2);

        // The HTML body can be a simplified version of PO body with appropriate headings
        $htmlContent = '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>GRN Approval Required</title></head>
<body style="font-family: Arial, sans-serif; background:#f8f8f8;">
    <div style="max-width:800px;margin:0 auto;padding:20px;background:#fff;border:1px solid #ddd;">
        <center><img src="' . $set_company_logo . '" width="150" alt="' . $set_company_name . '" /></center>
        <h2 style="text-align:center;">Goods Receipt Note Approval Required</h2>
        <p>Dear <strong>' . $approver_title . '</strong>,</p>
        <p>A new GRN requires your approval. Details below:</p>
        <table style="width:100%;border-collapse:collapse;">
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">GRN Number</th><td style="border:1px solid #ddd;padding:8px;">' . $grn_number . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Vendor</th><td style="border:1px solid #ddd;padding:8px;">' . $supplier_name . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Contact</th><td style="border:1px solid #ddd;padding:8px;">' . $fullname . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">GRN Date</th><td style="border:1px solid #ddd;padding:8px;">' . $grn_date . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Amount</th><td style="border:1px solid #ddd;padding:8px;">' . $formatted_amount . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Approval Level</th><td style="border:1px solid #ddd;padding:8px;">' . ucfirst(str_replace('_',' ',$approval_level)) . '</td></tr>
        </table>
        <p style="text-align:center;margin:20px 0;"><a href="' . $approval_link . '" style="background:#5cb85c;color:#fff;padding:10px 20px;text-decoration:none;">Review & Approve GRN</a></p>
        <p>If the button does not work, copy this link into your browser:<br>' . $approval_link . '</p>
        <p>Regards,<br><strong>Procurement Department</strong><br>' . $set_company_name . '</p>
    </div>
</body>
</html>';

        // send email
        $this->load->library('email');
        $this->email->set_mailtype("html");
        if (strpos(base_url(), 'localhost') !== false) {

    // Local server
    $this->email->from($set_from_email, $set_company_name);

} else {

    // Live server
    $this->email->from("noreply@uwsenvirotech.com", $set_company_name);

}
        $this->email->to($approver_email);
        $this->email->subject($subject);
        $this->email->message($htmlContent);

        if ($this->email->send()) {
            log_message('info', 'GRN approval email sent to ' . $approver_email . ' for GRN ' . $grn_number);
            return true;
        } else {
            log_message('error', 'Failed to send GRN approval email to ' . $approver_email . ' for GRN ' . $grn_number);
            log_message('error', $this->email->print_debugger());
            return false;
        }
    }

    public function send_grn_fully_approved_notification($grn_number, $creator_email, $amount, $grn_data)
    {
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'] ?? 'XForm Technologies';
        $set_company_logo = base_url() . '/' . ($session_data_head2['company_logo'] ?? 'assets/images/logo.png');
        $set_from_email = $session_data_head2['from_email'] ?? 'procurement@xform.in';

        $subject = "GRN Fully Approved: " . $grn_number;

        $supplier_name = isset($grn_data['company_name']) ? $grn_data['company_name'] : 'Unknown Vendor';
        $grn_date = isset($grn_data['date']) ? date('d-m-Y', strtotime($grn_data['date'])) : date('d-m-Y');
        $formatted_amount = "Rs. " . number_format($amount, 2);

        $htmlContent = '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>GRN Fully Approved</title></head>
<body style="font-family: Arial, sans-serif; background:#f8f8f8;">
    <div style="max-width:800px;margin:0 auto;padding:20px;background:#fff;border:1px solid #ddd;">
        <center><img src="' . $set_company_logo . '" width="150" alt="' . $set_company_name . '" /></center>
        <h2 style="text-align:center;color:#5cb85c;">Goods Receipt Note Fully Approved</h2>
        <p>Dear User,</p>
        <p>The Goods Receipt Note (GRN) listed below has been fully approved and stock has been updated in the inventory.</p>
        <table style="width:100%;border-collapse:collapse;">
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">GRN Number</th><td style="border:1px solid #ddd;padding:8px;">' . $grn_number . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Vendor</th><td style="border:1px solid #ddd;padding:8px;">' . $supplier_name . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">GRN Date</th><td style="border:1px solid #ddd;padding:8px;">' . $grn_date . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Amount</th><td style="border:1px solid #ddd;padding:8px;">' . $formatted_amount . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Status</th><td style="border:1px solid #ddd;padding:8px;color:#5cb85c;font-weight:bold;">Fully Approved</td></tr>
        </table>
        <p>Regards,<br><strong>Procurement Department</strong><br>' . $set_company_name . '</p>
    </div>
</body>
</html>';

        $this->load->library('email');
        $this->email->set_mailtype("html");
        if (strpos(base_url(), 'localhost') !== false) {
            $this->email->from($set_from_email, $set_company_name);
        } else {
            $this->email->from("noreply@uwsenvirotech.com", $set_company_name);
        }
        $this->email->to($creator_email);
        $this->email->subject($subject);
        $this->email->message($htmlContent);

        if ($this->email->send()) {
            log_message('info', 'GRN fully approved email sent to ' . $creator_email . ' for GRN ' . $grn_number);
            return true;
        } else {
            log_message('error', 'Failed to send GRN fully approved email to ' . $creator_email . ' for GRN ' . $grn_number);
            return false;
        }
    }

    public function send_accounts_notification($grn_number, $accounts_email, $amount, $grn_data)
    {
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'] ?? 'XForm Technologies';
        $set_company_logo = base_url() . '/' . ($session_data_head2['company_logo'] ?? 'assets/images/logo.png');
        $set_from_email = $session_data_head2['from_email'] ?? 'procurement@xform.in';

        $subject = "Accounts Alert - GRN Fully Approved: " . $grn_number;

        $supplier_name = isset($grn_data['company_name']) ? $grn_data['company_name'] : 'Unknown Vendor';
        $grn_date = isset($grn_data['date']) ? date('d-m-Y', strtotime($grn_data['date'])) : date('d-m-Y');
        $formatted_amount = "Rs. " . number_format($amount, 2);

        $htmlContent = '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Accounts Alert: GRN Fully Approved</title></head>
<body style="font-family: Arial, sans-serif; background:#f8f8f8;">
    <div style="max-width:800px;margin:0 auto;padding:20px;background:#fff;border:1px solid #ddd;">
        <center><img src="' . $set_company_logo . '" width="150" alt="' . $set_company_name . '" /></center>
        <h2 style="text-align:center;color:#007bff;">Accounts Notification - GRN Approved</h2>
        <p>Dear Accounts Team,</p>
        <p>Please note that the following Goods Receipt Note (GRN) has been fully approved. You may proceed with the billing/payment processes.</p>
        <table style="width:100%;border-collapse:collapse;">
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">GRN Number</th><td style="border:1px solid #ddd;padding:8px;">' . $grn_number . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Vendor</th><td style="border:1px solid #ddd;padding:8px;">' . $supplier_name . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">GRN Date</th><td style="border:1px solid #ddd;padding:8px;">' . $grn_date . '</td></tr>
            <tr><th style="text-align:left;border:1px solid #ddd;padding:8px;">Amount</th><td style="border:1px solid #ddd;padding:8px;">' . $formatted_amount . '</td></tr>
        </table>
        <p>Regards,<br><strong>Procurement Department</strong><br>' . $set_company_name . '</p>
    </div>
</body>
</html>';

        $this->load->library('email');
        $this->email->set_mailtype("html");
        if (strpos(base_url(), 'localhost') !== false) {
            $this->email->from($set_from_email, $set_company_name);
        } else {
            $this->email->from("noreply@uwsenvirotech.com", $set_company_name);
        }
        $this->email->to($accounts_email);
        $this->email->subject($subject);
        $this->email->message($htmlContent);

        if ($this->email->send()) {
            log_message('info', 'GRN accounts email sent to ' . $accounts_email . ' for GRN ' . $grn_number);
            return true;
        } else {
            log_message('error', 'Failed to send GRN accounts email to ' . $accounts_email . ' for GRN ' . $grn_number);
            return false;
        }
    }

    // Send PO to vendor
    public function send_po_to_vendor($po_number, $vendor_email, $vendor_data, $po_data, $po_items)
    {
        $subject = "Purchase Order: " . $po_number . " - XForm Technologies";

        // Ensure data exists
        $company_name = isset($vendor_data['company_name']) ? $vendor_data['company_name'] : 'Vendor';
        $fullname = isset($vendor_data['fullname']) ? $vendor_data['fullname'] : 'Contact Person';
        $address = isset($vendor_data['address']) ? $vendor_data['address'] : 'Address not specified';
        $mobile = isset($vendor_data['mobile']) ? $vendor_data['mobile'] : 'N/A';
        $po_date = isset($po_data['date']) ? date('d-m-Y', strtotime($po_data['date'])) : date('d-m-Y');
        $payment_due_date = isset($po_data['payment_due_date']) ? $po_data['payment_due_date'] : date('d-m-Y', strtotime('+15 days'));
        $po_payment_terms = isset($po_data['po_payment_terms']) ? $po_data['po_payment_terms'] : 'Net 30 Days';

        // Generate items table HTML
        $items_html = "";
        $total_amount = 0;
        foreach ($po_items as $item) {
            $product_name = isset($item['product_name']) ? htmlspecialchars($item['product_name']) : 'Item';
            $description = isset($item['description']) ? htmlspecialchars($item['description']) : '';
            $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
            $unit = isset($item['unit']) ? $item['unit'] : 'PCS';
            $price = isset($item['price']) ? number_format($item['price'], 2) : '0.00';
            $amount = isset($item['amount']) ? number_format($item['amount'], 2) : '0.00';

            $items_html .= "
            <tr>
                <td>{$product_name}</td>
                <td>{$description}</td>
                <td align='center'>{$quantity} {$unit}</td>
                <td align='right'>₹{$price}</td>
                <td align='right'>₹{$amount}</td>
            </tr>";
            $total_amount += isset($item['amount']) ? floatval($item['amount']) : 0;
        }

        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 800px; margin: 0 auto; padding: 20px; }
                .header { background: #2c3e50; color: white; padding: 30px; text-align: center; }
                .content { background: white; padding: 30px; border: 1px solid #ddd; }
                .company-info { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #2c3e50; color: white; padding: 12px; text-align: left; }
                td { padding: 10px; border: 1px solid #ddd; }
                .total-row { background: #f8f9fa; font-weight: bold; }
                .btn { display: inline-block; padding: 12px 25px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
                .terms { background: #f9f9f9; padding: 15px; border-left: 4px solid #3498db; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>PURCHASE ORDER</h1>
                    <h2>PO No: {$po_number}</h2>
                    <p>Date: {$po_date}</p>
                </div>
                
                <div class='content'>
                    <div class='company-info'>
                        <h3>XForm Technologies</h3>
                        <p>Procurement Department</p>
                        <p>Email: procurement@xform.in</p>
                    </div>
                    
                    <h3>Vendor Details:</h3>
                    <p>
                        <strong>{$company_name}</strong><br>
                        {$fullname}<br>
                        {$address}<br>
                        Email: {$vendor_email}<br>
                        Mobile: {$mobile}
                    </p>
                    
                    <h3>Delivery Details:</h3>
                    <p>
                        <strong>Delivery Date:</strong> {$payment_due_date}
                    </p>
                    
                    <h3>Items Ordered:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Specification</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$items_html}
                            <tr class='total-row'>
                                <td colspan='4' align='right'><strong>Total Amount:</strong></td>
                                <td align='right'><strong>₹" . number_format($total_amount, 2) . "</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class='terms'>
                        <h4>Terms & Conditions:</h4>
                        <p><strong>Payment Terms:</strong> {$po_payment_terms}</p>
                        <p><strong>GST:</strong> Applicable as per GST Act</p>
                        <p><strong>Delivery:</strong> As per agreed schedule</p>
                    </div>
                    
                    <h3>Action Required:</h3>
                    <p>Please:</p>
                    <ol>
                        <li>Acknowledge receipt of this Purchase Order</li>
                        <li>Confirm delivery schedule</li>
                        <li>Submit invoice with PO reference</li>
                    </ol>
                    
                    <p>For any queries, please contact: procurement@xform.in</p>
                    
                    <p>Regards,<br>
                    <strong>Procurement Team</strong><br>
                    XForm Technologies</p>
                </div>
                
                <div class='footer'>
                    <p><strong>This is an official Purchase Order from XForm Technologies.</strong></p>
                </div>
            </div>
        </body>
        </html>";

        // Send to vendor
        $result1 = $this->send_email($vendor_email, $subject, $message);

        // CC to procurement team
        $result2 = $this->send_email('procurement@xform.in', $subject . ' [CC]', $message);

        return $result1 && $result2;
    }

    // Send rejection notification
    public function send_po_rejection_notification($po_number, $remarks, $creator_email, $po_data, $supplier_data)
    {
        $subject = "Purchase Order Rejected: " . $po_number;

        // Ensure data exists
        $supplier_name = isset($supplier_data['company_name']) ? $supplier_data['company_name'] : 'Unknown Vendor';
        $total_amount = isset($po_data['total']) ? number_format($po_data['total'], 2) : '0.00';
        $remarks_safe = htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8');

        $message = "
        <html>
        <body>
            <h3>Purchase Order Rejected</h3>
            <p>Dear User,</p>
            
            <p>Your Purchase Order has been <strong>rejected</strong> by the approver.</p>
            
            <div style='background: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107;'>
                <h4 style='color: #856404; margin-top: 0;'><i class='fa fa-exclamation-triangle'></i> Rejection Details</h4>
                <p><strong>Reason:</strong> " . nl2br($remarks_safe) . "</p>
            </div>
            
            <h3>PO Details:</h3>
            <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                <tr style='background: #f8f9fa;'>
                    <th style='text-align: left; padding: 10px;'>Field</th>
                    <th style='text-align: left; padding: 10px;'>Details</th>
                </tr>
                <tr>
                    <td style='padding: 10px;'><strong>PO Number</strong></td>
                    <td style='padding: 10px;'>{$po_number}</td>
                </tr>
                <tr>
                    <td style='padding: 10px;'><strong>Vendor</strong></td>
                    <td style='padding: 10px;'>{$supplier_name}</td>
                </tr>
                <tr>
                    <td style='padding: 10px;'><strong>Amount</strong></td>
                    <td style='padding: 10px;'>₹{$total_amount}</td>
                </tr>
                <tr>
                    <td style='padding: 10px;'><strong>Rejected On</strong></td>
                    <td style='padding: 10px;'>" . date('d-m-Y H:i:s') . "</td>
                </tr>
            </table>
            
            <p style='margin-top: 20px;'>Please review the remarks and take appropriate action.</p>
            
            <p>Regards,<br>
            Procurement System - XForm Technologies</p>
        </body>
        </html>";

        return $this->send_email($creator_email, $subject, $message);
    }

    // General email sending function
    public function send_email($to, $subject, $message)
    {
        $this->load->library('email');

        // Try to load email configuration from config file first
        $config = array();

        // Check if email config exists in config file
        if (file_exists(APPPATH . 'config/email.php')) {
            $this->config->load('email', true);
            $config = $this->config->item('email');
        }





        // Initialize email
        $this->email->initialize($config);
        $this->email->clear(true); // Clear previous email
        $this->email->from($config['smtp_user'] ?? 'procurement@xform.in', 'XForm Technologies - Procurement');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);

        // Send email and handle errors
        try {
            $result = $this->email->send();
            if (!$result) {
                log_message('error', 'Email sending failed: ' . $this->email->print_debugger());
            }
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Email exception: ' . $e->getMessage());
            return false;
        }
    }



    // Test email function
    public function test_email($to = 'test@example.com')
    {
        $subject = "Test Email from Procurement System";
        $message = "
        <html>
        <body>
            <h2>Test Email</h2>
            <p>This is a test email from XForm Technologies Procurement System.</p>
            <p>Time: " . date('Y-m-d H:i:s') . "</p>
        </body>
        </html>";

        return $this->send_email($to, $subject, $message);
    }


    // Add method to Email_model.php
    public function send_draft_po_to_vendor($vendor_email, $draft_po_number, $pdf_file, $draft, $subject = '', $message = '')
    {
        $this->load->library('email');

        // Generate unique token for vendor response
        $response_token = md5($draft['draft_id'] . time());

        // Store token in database
        $this->db->where('draft_id', $draft['draft_id'])
            ->update('po_drafts', ['draft_token' => $response_token]);

        // Prepare email content
        $company_name = $this->settings['company_name'];
        $company_logo = base_url() . $this->settings['company_logo'];

        $response_url = base_url() . 'SupplierController/vendor_draft_response/' . $response_token;

        $html_content = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #f8f9fa; padding: 20px; text-align: center; }
            .content { padding: 20px; background: white; }
            .footer { background: #f8f9fa; padding: 10px; text-align: center; font-size: 12px; color: #666; }
            .response-buttons { margin: 20px 0; text-align: center; }
            .btn { padding: 10px 20px; margin: 5px; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
            .btn-accept { background: #28a745; color: white; }
            .btn-reject { background: #dc3545; color: white; }
            .btn-review { background: #007bff; color: white; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="' . $company_logo . '" alt="' . $company_name . '" width="150">
                <h2>Draft Purchase Order for Review</h2>
            </div>
            
            <div class="content">
                <h3>Draft PO #: ' . $draft_po_number . '</h3>
                <p><strong>From:</strong> ' . $company_name . '</p>
                <p><strong>Date:</strong> ' . date('d-m-Y') . '</p>
                
                ' . ($message ? '<p><strong>Message:</strong> ' . nl2br($message) . '</p>' : '') . '
                
                <p>This is a <strong>DRAFT</strong> purchase order for your review and confirmation. 
                Please review the attached PDF and provide your response.</p>
                
                <div class="response-buttons">
                    <p><strong>Quick Response:</strong></p>
                    <a href="' . $response_url . '?response=accepted" class="btn btn-accept">✓ Accept Draft</a>
                    <a href="' . $response_url . '?response=rejected" class="btn btn-reject">✗ Reject Draft</a>
                    <a href="' . $response_url . '" class="btn btn-review">📝 Review & Comment</a>
                </div>
                
                <p><em>Note: This draft PO is subject to our internal approval process. 
                A formal PO will be issued after internal approvals are complete.</em></p>
            </div>
            
            <div class="footer">
                <p>This is a computer generated draft purchase order.</p>
                <p>' . $company_name . '</p>
            </div>
        </div>
    </body>
    </html>';

        $subject = $subject ?: 'Draft Purchase Order #' . $draft_po_number . ' for Review';

        $this->email->from($this->settings['from_email'], $company_name);
        $this->email->to($vendor_email);
        $this->email->subject($subject);
        $this->email->message($html_content);
        $this->email->attach($pdf_file);

        return $this->email->send();
    }

    public function send_vendor_response_notification($draft_id, $vendor_response, $vendor_comments)
    {
        // Get draft creator email
        $this->db->select('u.email, d.draft_number, d.vendor_email, s.company_name as vendor_name');
        $this->db->from('po_drafts d');
        $this->db->join('user u', 'd.created_by = u.user_id');
        $this->db->join('supplier s', 'd.vendor_email = s.email', 'left');
        $this->db->where('d.draft_id', $draft_id);
        $data = $this->db->get()->row_array();

        if (!$data) return false;

        $subject = 'Vendor Response for Draft PO #' . $data['draft_number'];
        $vendor_name = $data['vendor_name'] ?: $data['vendor_email'];

        $message = '
    <h3>Vendor Response Received</h3>
    <p><strong>Draft PO:</strong> ' . $data['draft_number'] . '</p>
    <p><strong>Vendor:</strong> ' . $vendor_name . '</p>
    <p><strong>Response:</strong> ' . strtoupper($vendor_response) . '</p>
    ' . ($vendor_comments ? '<p><strong>Comments:</strong> ' . nl2br($vendor_comments) . '</p>' : '') . '
    <p><a href="' . base_url() . 'SupplierController/view_po_drafts">View Drafts</a></p>';

        $this->email->from($this->settings['from_email'], $this->settings['company_name']);
        $this->email->to($data['email']);
        $this->email->subject($subject);
        $this->email->message($message);

        return $this->email->send();
    }
}
