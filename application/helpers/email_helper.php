<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('send_amendment_notification')) {
    function send_amendment_notification($type, $data)
    {
        $CI = &get_instance();
        $CI->load->library('email');

        // Initialize email with configuration from email.php
        // The email configuration should be auto-loaded from config/email.php
        // If not, you need to load it manually:
        $CI->config->load('email', TRUE);
        $email_config = $CI->config->item('email');

        $CI->email->initialize($email_config);
        $CI->email->set_newline("\r\n");
        $CI->email->from($email_config['smtp_user'], 'XForm ERP System');

        switch ($type) {
            case 'approval_request':
                $CI->email->to($data['approver_email']);
                $CI->email->subject('PO Amendment Approval Required - ' . $data['amendment_no']);
                $message = "Dear Approver,<br><br>";
                $message .= "A PO amendment requires your approval.<br><br>";
                $message .= "<strong>Amendment No:</strong> " . $data['amendment_no'] . "<br>";
                $message .= "<strong>PO Number:</strong> " . $data['po_number'] . "<br>";
                $message .= "<strong>Type:</strong> " . ucfirst(str_replace('_', ' ', $data['amendment_type'])) . "<br>";
                $message .= "<strong>Description:</strong> " . $data['description'] . "<br>";
                $message .= "<strong>Initiated By:</strong> " . $data['initiated_by'] . "<br><br>";
                $message .= "Please review and approve at: " . base_url('poamendment/approvals') . "<br><br>";
                $message .= "Regards,<br>XForm ERP System";
                break;

            case 'vendor_ack_request':
                $CI->email->to($data['vendor_email']);
                $CI->email->subject('PO Amendment for Acknowledgment - ' . $data['amendment_no']);
                $message = "Dear " . $data['vendor_name'] . ",<br><br>";
                $message .= "A PO amendment has been approved and requires your acknowledgment.<br><br>";
                $message .= "<strong>Amendment No:</strong> " . $data['amendment_no'] . "<br>";
                $message .= "<strong>PO Number:</strong> " . $data['po_number'] . "<br>";
                $message .= "<strong>Type:</strong> " . ucfirst(str_replace('_', ' ', $data['amendment_type'])) . "<br>";
                $message .= "<strong>Description:</strong> " . $data['description'] . "<br><br>";
                $message .= "Please acknowledge the amendment at: " . base_url('vendor/acknowledge/' . $data['amendment_id']) . "<br><br>";
                $message .= "Regards,<br>XForm ERP System";
                break;

            case 'revised_po_issued':
                $CI->email->to($data['vendor_email']);
                $CI->email->subject('Revised PO Issued - ' . $data['revised_po_number']);
                $message = "Dear " . $data['vendor_name'] . ",<br><br>";
                $message .= "Revised Purchase Order has been issued.<br><br>";
                $message .= "<strong>Original PO:</strong> " . $data['original_po'] . "<br>";
                $message .= "<strong>Revised PO:</strong> " . $data['revised_po_number'] . "<br>";
                $message .= "<strong>Amendment Reference:</strong> " . $data['amendment_no'] . "<br><br>";
                $message .= "Please use the revised PO for all future references.<br><br>";
                $message .= "Regards,<br>XForm ERP System";
                break;
        }

        $CI->email->message($message);

        // Actually send emails
        if ($CI->email->send()) {
            log_message('info', 'Email sent: ' . $type . ' to ' . $CI->email->to);
            return true;
        } else {
            log_message('error', 'Email failed: ' . $type);
            log_message('error', $CI->email->print_debugger());
            return false;
        }
    }
}

if (!function_exists('send_rfq_to_vendors')) {
    function send_rfq_to_vendors($rfq_id, $vendor_ids = array())
    {
        $CI = &get_instance();
        $CI->load->library('email');

        // Load necessary models
        $CI->load->model('RFQ_model', 'rfq');
        $CI->load->model('Quotation_model');

        // Get RFQ details
        $rfq = $CI->Quotation_model->get_rfq_details($rfq_id);
        $items = $CI->Quotation_model->get_rfq_items($rfq_id);

        // Get vendor details
        $vendors = array();
        if (!empty($vendor_ids)) {
            $CI->db->where_in('supplier_id', $vendor_ids);
            $vendors = $CI->db->get('supplier')->result_array();
        }

        // Fetch custom emails from rfq_suppliers
        $custom_emails = [];
        if (!empty($vendor_ids)) {
            $CI->db->where('rfq_id', $rfq_id);
            $CI->db->where_in('supplier_id', $vendor_ids);
            $rfq_suppliers = $CI->db->get('rfq_suppliers')->result_array();
            foreach ($rfq_suppliers as $rs) {
                $custom_emails[$rs['supplier_id']] = [
                    'emails_to' => $rs['emails_to'] ?? null,
                    'emails_cc' => $rs['emails_cc'] ?? null
                ];
            }
        }

        $session_data_head2 = $CI->session->userdata('session_data_head');
        $additional_cc = $rfq['additional_cc'] ?? '';
        $sent_count = 0;

        foreach ($vendors as $vendor) {
            $sid = $vendor['supplier_id'];
            $emails_to = $custom_emails[$sid]['emails_to'] ?? null;
            $emails_cc = $custom_emails[$sid]['emails_cc'] ?? null;

            // Fallback to default supplier email if no custom to/cc is saved
            if (empty($emails_to) && empty($emails_cc)) {
                $emails_to = $vendor['email'];
            }

            if (!empty($emails_to)) {
                // Get email configuration from session or use default

                $CI->email->set_newline("\r\n");
                $CI->email->from(
                    !empty($session_data_head2['from_email']) ? $session_data_head2['from_email'] : 'noreply@xform.in',
                    'XForm ERP System'
                );

                $CI->email->to($emails_to);
                
                // Merge vendor CC and additional CC
                $final_cc_arr = [];
                if (!empty($emails_cc)) {
                    $final_cc_arr[] = $emails_cc;
                }
                if (!empty($additional_cc)) {
                    $final_cc_arr[] = $additional_cc;
                }

                if (!empty($final_cc_arr)) {
                    $CI->email->cc(implode(',', $final_cc_arr));
                }

                // FIX HERE: Change $rfq->rfq_no to $rfq['rfq_no']
                $CI->email->subject('Request for Quotation (RFQ) - ' . $rfq['rfq_no']);

                // Build email message
                $message = "<!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
                        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
                        .content { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        th { background: #f2f2f2; padding: 10px; text-align: left; }
                        td { padding: 10px; border-bottom: 1px solid #ddd; }
                        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
                        .btn { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Request for Quotation (RFQ)</h2>
                        </div>
                        
                        <div class='content'>
                            <p>Dear " . htmlspecialchars($vendor['supplier_name'] ?? $vendor['company_name']) . ",</p>
                            
                            <p>We are pleased to invite you to submit your quotation for the following items as per the details below:</p>
                            
                            <h3>RFQ Details:</h3>
                            <p><strong>RFQ Number:</strong> " . $rfq['rfq_no'] . "</p>
                            <p><strong>RFQ Date:</strong> " . date('d-m-Y', strtotime($rfq['rfq_date'])) . "</p>
                            <p><strong>PR Number:</strong> " . ($rfq['pr_no'] ?? 'N/A') . "</p>
                            
                            <h3>Items List:</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>";

                foreach ($items as $item) {
                    // FIX HERE: Also check if $item is array or object
                    $item_code = is_array($item) ? $item['item_code'] : $item->item_code;
                    $description = is_array($item) ? $item['description'] : $item->description;
                    $quantity = is_array($item) ? $item['quantity'] : $item->quantity;
                    $unit = is_array($item) ? $item['unit'] : $item->unit;

                    $message .= "<tr>
                                    <td>" . $item_code . "</td>
                                    <td>" . htmlspecialchars($description) . "</td>
                                    <td>" . $quantity . "</td>
                                    <td>" . $unit . "</td>
                                </tr>";
                }

                $message .= "</tbody>
                            </table>
                            
                            <h3 style='display:none'>Submission Instructions:</h3>
                            <ol style='display:none'>
                                <li>Please quote your best price for each item</li>
                                <li>Include GST percentage separately</li>
                                <li>Specify delivery terms and timeline</li>
                                <li>Mention payment terms</li>
                                <li>Quote validity period</li>
                            </ol>
                            
                            <p style='display:none'>Please submit your quotation through our portal:</p>
                            <a style='display:none' href='" . base_url('vendor/quotation/submit/' . $rfq_id . '/' . $vendor['supplier_id']) . "' class='btn'>
                                Submit Quotation
                            </a>
                            
                            <p> you can reply to this email with your quotation.</p>
                            
                            <p><strong>Submission Deadline:</strong> " . date('d-m-Y', strtotime('+7 days')) . "</p>
                            
                            <p>For any queries, please contact our procurement department.</p>
                        </div>
                        
                        <div class='footer'>
                            <p>This is an auto-generated email. Please do not reply directly to this email.</p>
                            <p>© " . date('Y') . " XForm ERP System. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>";

                $CI->email->message($message);

                // Send email
                if ($CI->email->send()) {
                    $sent_count++;

                    // Log email sent
                    // FIX HERE: Use array syntax
                    log_message('info', 'RFQ email sent to vendor: ' . $vendor['email'] . ' for RFQ: ' . $rfq['rfq_no']);

                    // Update database to mark email sent
                    $CI->db->where('rfq_id', $rfq_id);
                    $CI->db->where('supplier_id', $vendor['supplier_id']);
                    $CI->db->update('rfq_suppliers', array(
                        'email_sent' => 1,
                        'email_sent_date' => date('Y-m-d H:i:s')
                    ));
                } else {
                    log_message('error', 'Failed to send email to vendor: ' . $vendor['email']);
                    log_message('error', $CI->email->print_debugger());
                }

                $CI->email->clear();
            }
        }

        return array(
            'total_vendors' => count($vendors),
            'sent_count' => $sent_count,
            'failed_count' => count($vendors) - $sent_count
        );
    }
}
