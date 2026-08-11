<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CustomerController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->library('form_validation');
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
        
        if(($session_data_head['result']['user_id'] ?? NULL) === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function index() {
        $data['result'] = $this->customer->get_customer($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('customer/add_customer', $data);
    }

    public function add_customer() {
        //echo 'hii';die();
        $company_name = $this->input->post('company_name');
        
        $fullname = $this->input->post('fullname');
        
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $addresses = $this->input->post('address');

$address_json = json_encode($addresses);
        $state_code = $this->input->post('state_code');

        $c_code = $this->customer->get_last_customer_code($this->user_id);
        $c_code = $c_code + 3000;
        
        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'pancard' => $pancard,
            'gst' => $gst, 'email' => $email, 'mobile' => $mobile, 'address' => $address_json,
            'state_code' => $state_code, 'uid' => $this->user_id, 'c_code' => $c_code);
        $result = $this->customer->customer_check($company_name, $this->user_id);   

        if ($result == FALSE) {
            $this->customer->add_customer($data_customer);
            $this->session->set_flashdata('SUCCESSMSG', "Company added successfully!!");
            redirect('CustomerController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Company already exist!!");
            redirect('CustomerController/index');
        }
    }

    public function edit_customer() {
        $customer_id = $this->input->post('customer_id');
        $company_name = $this->input->post('company_name');
        $fullname = $this->input->post('fullname');
        $pancard = $this->input->post('pancard');
        $gst = $this->input->post('gst');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $address = $this->input->post('address');
        $state_code = $this->input->post('state_code');

        $data_customer = array('company_name' => $company_name, 'fullname' => $fullname, 'pancard' => $pancard,
            'gst' => $gst, 'email' => $email, 'mobile' => $mobile,
            'address' => $address, 'state_code' => $state_code);
        $result = $this->customer->edit_customer($data_customer, $customer_id,$this->user_id);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Customer updated successfully!!");
            redirect('CustomerController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Customer not updated successfully!!");
            redirect('CustomerController/index');
        }
    }

    public function get_customer_by_id() {
        $id = $this->uri->segment(3);
        $data['customer'] = $this->customer->get_customer_by_id($id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('customer/edit_customer', $data);
    }

    public function delete_customer_by_id() {
        $id = $this->uri->segment(3);
        $result = $this->customer->delete_customer_by_id($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Customer deleted successfully!!");
            redirect('CustomerController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Customer not deleted successfully!!");
            redirect('CustomerController/index');
        }
    }

    public function my_profile() {
        $session_data_head = $this->session->userdata('session_data_head');
        $mobile = $session_data_head['result']['user_id'];
        $data['result'] = $this->customers->get_customer_by_mobile($mobile);
        $this->load->view('header_side_bar', $session_data_head);
        $this->load->view('my_profile', $data);
    }

    public function get_customer() {
        $mobile = $this->input->post('mobile');
        $data['result'] = $this->customers->get_customer_by_mobile($mobile);
        $this->load->view('view_booked_services', $data);
    }

    /**
     * Export customers to Excel
     */
  public function export_customers() {
    // Create new Spreadsheet object
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator("System")
        ->setLastModifiedBy("System")
        ->setTitle("Customer List")
        ->setSubject("Customer Details")
        ->setDescription("Export of all customer details");

    // ========== ADD HEADING ==========
    $heading = "CUSTOMER LIST REPORT";
    $subheading = "Generated on: " . date('d-m-Y');

    // Insert heading at row 1
    $sheet->setCellValue('A1', $heading);
    $sheet->mergeCells('A1:J1'); // Merge across all columns (A to J)
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Insert subheading at row 2
    $sheet->setCellValue('A2', $subheading);
    $sheet->mergeCells('A2:J2');
    $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Now headers start from row 3
    $headers = [
        'Sr.No.',
        'Customer Code',
        'Company Name',
        'Contact Person',
        'PAN No',
        'GST No',
        'Email',
        'Mobile',
        'State Code',
        'Address'
    ];

    $column = 'A';
    $headerRow = 3; // Row number for headers
    foreach ($headers as $header) {
        $sheet->setCellValue($column . $headerRow, $header);
        $sheet->getStyle($column . $headerRow)->getFont()->setBold(true);
        $column++;
    }

    // Apply background color and borders to header row
    $headerStyleArray = [
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFEFEFEF']
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];
    $sheet->getStyle('A3:J3')->applyFromArray($headerStyleArray);

    // Get customer data
    $customers = $this->customer->get_customer($this->user_id);

    // Add data rows starting from row 4
    $row = 4;
    $sr_no = 1;

    foreach ($customers as $customer) {
        $sheet->setCellValue('A' . $row, $sr_no);
        $sheet->setCellValue('B' . $row, $customer->c_code ?? '');
        $sheet->setCellValue('C' . $row, $customer->company_name ?? '');
        $sheet->setCellValue('D' . $row, $customer->fullname ?? '');
        $sheet->setCellValue('E' . $row, $customer->pancard ?? '');
        $sheet->setCellValue('F' . $row, $customer->gst ?? '');
        $sheet->setCellValue('G' . $row, $customer->email ?? '');
        $sheet->setCellValue('H' . $row, $customer->mobile ?? '');
        $sheet->setCellValue('I' . $row, $customer->state_code ?? '');
        $sheet->setCellValue('J' . $row, $customer->address ?? '');

        $row++;
        $sr_no++;
    }

    // Auto size columns (based on the widest content in each column)
    foreach (range('A', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Set headers for download
    $filename = 'customers_' . date('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}
    /**
     * Export customers to PDF
     */
    public function export_customers_pdf() {
        // Boost memory and execution limits to handle large lists in mPDF
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');

        require_once APPPATH . '../vendor/autoload.php';

        // Get customer data
        $customers = $this->customer->get_customer($this->user_id);

        // Create HTML content
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; font-size: 10pt; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: left; }
                td { border: 1px solid #ddd; padding: 8px; }
                .header { text-align: center; margin-bottom: 20px; }
                .footer { margin-top: 30px; text-align: center; font-size: 9pt; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Customer List</h2>
              
                <p>Total Customers: ' . count($customers) . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Code</th>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>PAN No</th>
                        <th>TAX No</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>State Code</th>
                    </tr>
                </thead>
                <tbody>';

        $sr_no = 1;
        foreach ($customers as $customer) {
            $html .= '<tr>
                <td>' . $sr_no . '</td>
                <td>' . ($customer->c_code ?? '') . '</td>
                <td>' . ($customer->company_name ?? '') . '</td>
                <td>' . ($customer->fullname ?? '') . '</td>
                <td>' . ($customer->pancard ?? '') . '</td>
                <td>' . ($customer->gst ?? '') . '</td>
                <td>' . ($customer->email ?? '') . '</td>
                <td>' . ($customer->mobile ?? '') . '</td>
                <td>' . ($customer->state_code ?? '') . '</td>
            </tr>';
            $sr_no++;
        }

        $html .= '</tbody>
            </table>
            
            <div class="footer">
                <p>© ' . date('Y') . ' - Generated by ERP System</p>
            </div>
        </body>
        </html>';

        // Create PDF using mPDF
        if (class_exists('\Mpdf\Mpdf')) {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            // Memory optimizations for large customer lists
            $mpdf->simpleTables = true;
            $mpdf->packTableData = true;
            $mpdf->useSubstitutions = false;

            $mpdf->WriteHTML($html);

            $filename = 'customers_' . date('Ymd_His') . '.pdf';

            $mpdf->Output($filename, 'D'); // Download
            exit;
        } else {
            // Fallback to CI mPDF library
            $this->load->library('m_pdf');

            // Memory optimizations for large customer lists
            $this->m_pdf->pdf->simpleTables = true;
            $this->m_pdf->pdf->packTableData = true;
            $this->m_pdf->pdf->useSubstitutions = false;

            $this->m_pdf->pdf->WriteHTML($html);

            $filename = 'customers_' . date('Ymd_His') . '.pdf';
            $this->m_pdf->pdf->Output($filename, 'D');
            exit;
        }
    }

    /**
     * Show import customers view
     */
    public function import_customers_view() {
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('customer/import_customers');
    }

    /**
     * Download customer import template
     */
    public function download_customer_template() {
        require_once APPPATH . '../vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("System")
            ->setLastModifiedBy("System")
            ->setTitle("Customer Import Template")
            ->setSubject("Customer Import")
            ->setDescription("Template for importing customer details");

        // Add headers with instructions
        $headers = [
            'Company Name*',
            'Contact Person',
            'PAN No',
            'GST No',
            'Email',
            'Mobile*',
            'State Code',
            'Address'
        ];

        $instructions = [
            'Company Name*' => 'Required field',
            'Contact Person' => 'Optional',
            'PAN No' => '10 characters max',
            'GST No' => '15 characters max',
            'Email' => 'Valid email format',
            'Mobile*' => 'Required, 10 digits',
            'State Code' => 'Numeric state code',
            'Address' => 'Full address'
        ];

        $column = 'A';
        $row = 1;

        // Add headers
        foreach ($headers as $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $column++;
        }

        // Add instructions in row 2
        $column = 'A';
        $row = 2;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue($column . $row, $instruction);
            $sheet->getStyle($column . $row)->getFont()->setColor(
                new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED)
            );
            $column++;
        }

        // Add sample data in row 3
        $sampleData = [
            'ABC Corporation',
            'John Doe',
            'ABCDE1234F',
            '27ABCDE1234F1Z5',
            'customer@example.com',
            '9876543210',
            '27',
            '123 Street, City, State'
        ];

        $column = 'A';
        $row = 3;
        foreach ($sampleData as $data) {
            $sheet->setCellValue($column . $row, $data);
            $sheet->getStyle($column . $row)->getFont()->setItalic(true);
            $sheet->getStyle($column . $row)->getFont()->setColor(
                new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN)
            );
            $column++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set headers for download
        $filename = 'customer_import_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    /**
     * Process customer import from Excel
     */
    public function process_customer_import() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $config['upload_path'] = './uploads/imports/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = 5120; // 5MB
            $config['encrypt_name'] = TRUE;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('customer_file')) {
                $this->session->set_flashdata('INFOMSG', $this->upload->display_errors());
                redirect('CustomerController/import_customers_view');
            }

            $upload_data = $this->upload->data();
            $file_path = $config['upload_path'] . $upload_data['file_name'];

            // Load PhpSpreadsheet
            require_once APPPATH . '../vendor/autoload.php';

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                // Remove header row (row 1)
                array_shift($rows);
                // Remove instruction row (row 2)
                array_shift($rows);

                $imported = 0;
                $skipped = 0;
                $errors = [];

                foreach ($rows as $index => $row) {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $row_number = $index + 3; // +3 because we removed 2 rows and Excel is 1-indexed

                    // Validate required fields
                    if (empty($row[0]) || empty($row[5])) { // Company Name and Mobile
                        $errors[] = "Row {$row_number}: Missing required fields (Company Name or Mobile)";
                        $skipped++;
                        continue;
                    }

                    // Check if customer already exists
                    $existing = $this->db
                        ->where('company_name', $row[0])
                        ->where('uid', $this->user_id)
                        ->get('customer')
                        ->row();

                    if ($existing) {
                        $errors[] = "Row {$row_number}: Customer '{$row[0]}' already exists";
                        $skipped++;
                        continue;
                    }

                    // Get next customer code
                    $c_code = $this->customer->get_last_customer_code($this->user_id);
                    $c_code = $c_code + 3000;

                    // Prepare data
                    $customer_data = [
                        'company_name' => $row[0] ?? '',
                        'fullname' => $row[1] ?? '',
                        'pancard' => strtoupper($row[2] ?? ''),
                        'gst' => strtoupper($row[3] ?? ''),
                        'email' => $row[4] ?? '',
                        'mobile' => $row[5] ?? '',
                        'state_code' => $row[6] ?? '',
                        'address' => $row[7] ?? '',
                        'uid' => $this->user_id,
                        'c_code' => $c_code
                    ];

                    // Insert customer
                    if ($this->db->insert('customer', $customer_data)) {
                        $imported++;
                    } else {
                        $errors[] = "Row {$row_number}: Failed to insert customer";
                        $skipped++;
                    }
                }

                // Clean up uploaded file
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                // Prepare result message
                $message = "Import completed: {$imported} customers imported successfully.";
                if ($skipped > 0) {
                    $message .= " {$skipped} customers skipped.";
                }

                if (!empty($errors)) {
                    $this->session->set_flashdata('IMPORT_ERRORS', $errors);
                }

                $this->session->set_flashdata('SUCCESSMSG', $message);
            } catch (Exception $e) {
                $this->session->set_flashdata('INFOMSG', 'Error processing file: ' . $e->getMessage());
            }

            redirect('CustomerController/import_customers_view');
        }
    }
}