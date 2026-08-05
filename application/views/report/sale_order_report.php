<?php
// No direct script access
defined('BASEPATH') OR exit('No direct script access allowed');

// Load PhpSpreadsheet via Composer autoload
require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

// Check if data exists
if(!isset($sale_order_report) || empty($sale_order_report)) {
    // Create a simple spreadsheet with error message
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No sale order data found for the selected criteria');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Sale_Order_Report_No_Data.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $file_name . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set default font
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(11);

// Define style arrays
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 22,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '31a3dd'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$columnHeaderStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '31a3dd'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$dataCellStyle = [
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$rightAlignStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$centerAlignStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

$totalRowStyle = [
    'font' => [
        'bold' => true,
        'size' => 14,
        'color' => ['rgb' => '1F4E78'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'e8f442'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Function to get status text (you can customize this based on your status codes)
function getOrderStatus($status) {
    switch ($status) {
        case "1":
            return "Draft";
        case "2":
            return "Sent";
        case "3":
            return "Viewed";
        case "4":
            return "Approved";
        case "5":
            return "Rejected";
        case "6":
            return "Canceled";
        default:
            return $status;
    }
}

// Rename the sheet
$sheet->setTitle('Sale Order Report');

// Main Header
$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A1', 'SALE ORDER REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Filter Information
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:H2');
    $filter_text = 'Period: ' . $from_date . ' to ' . $to_date;
    if(isset($fullname) && !empty($fullname)) {
        $filter_text .= ' | Customer: ' . $fullname;
    }
    $sheet->setCellValue('A2', $filter_text);
    $sheet->getStyle('A2')->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'font' => [
            'italic' => true,
            'bold' => true,
            'size' => 12,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '31a3dd'],
        ],
    ]);
    
    // Empty row
    $sheet->setCellValue('A3', '');
    $startRow = 4;
} else {
    $startRow = 2;
}

// Set column headers
$headers = [
    'A' . $startRow => 'Sr.No.',
    'B' . $startRow => 'Date',
    'C' . $startRow => 'Order No.',
    'D' . $startRow => 'Customer Name',
    'E' . $startRow => 'Due Date',
    'F' . $startRow => 'Status',
    'G' . $startRow => 'Type',
    'H' . $startRow => 'Total (₹)'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':H' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_amount = 0;

// Status-wise totals
$status_totals = [];

foreach ($sale_order_report as $row) {
    $amount = isset($row->total) ? floatval($row->total) : 0;
    $total_amount += $amount;
    
    $status = getOrderStatus($row->status);
    
    // Track status totals
    if(!isset($status_totals[$status])) {
        $status_totals[$status] = 0;
    }
    $status_totals[$status] += $amount;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->date) ? date("d-m-Y", strtotime($row->date)) : '');
    $sheet->setCellValue('C' . $rowCount, isset($row->number_fk) ? $row->number_fk : '');
    $sheet->setCellValue('D' . $rowCount, isset($row->fullname) ? $row->fullname : '');
    $sheet->setCellValue('E' . $rowCount, isset($row->exp_date) ? date("d-m-Y", strtotime($row->exp_date)) : '');
    $sheet->setCellValue('F' . $rowCount, $status);
    $sheet->setCellValue('G' . $rowCount, 'Sale Order');
    $sheet->setCellValue('H' . $rowCount, $amount);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':H' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('B' . $rowCount)->applyFromArray($centerAlignStyle); // Date centered
    $sheet->getStyle('E' . $rowCount)->applyFromArray($centerAlignStyle); // Due Date centered
    $sheet->getStyle('F' . $rowCount)->applyFromArray($centerAlignStyle); // Status centered
    $sheet->getStyle('G' . $rowCount)->applyFromArray($centerAlignStyle); // Type centered
    $sheet->getStyle('H' . $rowCount)->applyFromArray($rightAlignStyle); // Amount right-aligned
    
    // Format amount column
    $sheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row (matching original style with yellow background)
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, '');
    $sheet->setCellValue('C' . $totalRow, '');
    $sheet->setCellValue('D' . $totalRow, '');
    $sheet->setCellValue('E' . $totalRow, '');
    $sheet->setCellValue('F' . $totalRow, '');
    $sheet->setCellValue('G' . $totalRow, 'TOTAL:');
    $sheet->setCellValue('H' . $totalRow, $total_amount);
    
    // Style for total row - using yellow background with dark text
    $sheet->getStyle('A' . $totalRow . ':H' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('G' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('H' . $totalRow)->applyFromArray($totalRowStyle); // Ensure H column has yellow color
    $sheet->getStyle('H' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Orders: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('E' . $summaryRow . ':H' . $summaryRow);
$sheet->setCellValue('E' . $summaryRow, 'Average Order Value: ₹ ' . number_format(($rowCou > 1 ? $total_amount / ($rowCou - 1) : 0), 2));
$sheet->getStyle('E' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Status-wise Summary
$statusRow = $summaryRow + 2;

$sheet->mergeCells('A' . $statusRow . ':H' . $statusRow);
$sheet->setCellValue('A' . $statusRow, 'STATUS-WISE ORDER SUMMARY');
$sheet->getStyle('A' . $statusRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$statusRow++;

$sheet->setCellValue('A' . $statusRow, 'Status');
$sheet->setCellValue('B' . $statusRow, 'Order Count');
$sheet->setCellValue('C' . $statusRow, 'Total Amount (₹)');
$sheet->setCellValue('D' . $statusRow, 'Percentage (%)');
$sheet->getStyle('A' . $statusRow . ':D' . $statusRow)->applyFromArray(['font' => ['bold' => true]]);
$statusRow++;

// Calculate status counts
$status_counts = [];
foreach ($sale_order_report as $row) {
    $status = getOrderStatus($row->status);
    if(!isset($status_counts[$status])) {
        $status_counts[$status] = 0;
    }
    $status_counts[$status]++;
}

foreach ($status_totals as $status => $amount) {
    $count = isset($status_counts[$status]) ? $status_counts[$status] : 0;
    $percentage = ($total_amount > 0) ? ($amount / $total_amount) * 100 : 0;
    
    $sheet->setCellValue('A' . $statusRow, $status);
    $sheet->setCellValue('B' . $statusRow, $count);
    $sheet->setCellValue('C' . $statusRow, $amount);
    $sheet->setCellValue('D' . $statusRow, number_format($percentage, 2) . '%');
    
    $sheet->getStyle('B' . $statusRow)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('C' . $statusRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $statusRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $statusRow++;
}

// Customer Summary (if multiple customers)
$customer_totals = [];
foreach ($sale_order_report as $row) {
    $customer = isset($row->fullname) ? $row->fullname : 'Unknown';
    $amount = isset($row->total) ? floatval($row->total) : 0;
    
    if(!isset($customer_totals[$customer])) {
        $customer_totals[$customer] = 0;
    }
    $customer_totals[$customer] += $amount;
}

if(count($customer_totals) > 1) {
    $customerRow = $statusRow + 2;
    
    $sheet->mergeCells('A' . $customerRow . ':H' . $customerRow);
    $sheet->setCellValue('A' . $customerRow, 'CUSTOMER-WISE ORDER SUMMARY');
    $sheet->getStyle('A' . $customerRow)->applyFromArray([
        'font' => ['bold' => true, 'size' => 12],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '3c8dbc'],
        ],
        'font' => ['color' => ['rgb' => 'FFFFFF']],
    ]);
    $customerRow++;
    
    $sheet->setCellValue('A' . $customerRow, 'Customer Name');
    $sheet->setCellValue('B' . $customerRow, 'Order Count');
    $sheet->setCellValue('C' . $customerRow, 'Total Amount (₹)');
    $sheet->setCellValue('D' . $customerRow, 'Percentage (%)');
    $sheet->getStyle('A' . $customerRow . ':D' . $customerRow)->applyFromArray(['font' => ['bold' => true]]);
    $customerRow++;
    
    foreach ($customer_totals as $customer => $amount) {
        $percentage = ($total_amount > 0) ? ($amount / $total_amount) * 100 : 0;
        
        $sheet->setCellValue('A' . $customerRow, $customer);
        $sheet->setCellValue('B' . $customerRow, 1); // You may want to count actual orders per customer
        $sheet->setCellValue('C' . $customerRow, $amount);
        $sheet->setCellValue('D' . $customerRow, number_format($percentage, 2) . '%');
        
        $sheet->getStyle('B' . $customerRow)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('C' . $customerRow)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('C' . $customerRow)->getNumberFormat()->setFormatCode('#,##0.00');
        
        $customerRow++;
    }
}

// Signature Section
$signatureRow = (isset($customerRow) ? $customerRow : $statusRow) + 3;

$sheet->mergeCells('A' . $signatureRow . ':D' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('E' . $signatureRow . ':H' . $signatureRow);
$sheet->setCellValue('E' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':D' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('E' . $signatureRow . ':H' . $signatureRow);
$sheet->setCellValue('E' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':H' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':H' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated sale order report. For any discrepancies, please contact the sales department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'H') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(12);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(20);
$sheet->getColumnDimension('E')->setWidth(12);
$sheet->getColumnDimension('F')->setWidth(12);
$sheet->getColumnDimension('G')->setWidth(12);
$sheet->getColumnDimension('H')->setWidth(15);

// Generate filename
$customer_display = isset($fullname) && !empty($fullname) ? preg_replace('/[^A-Za-z0-9\-]/', '_', $fullname) : 'All';
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Sale_Order_Report_' . $customer_display . '_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>