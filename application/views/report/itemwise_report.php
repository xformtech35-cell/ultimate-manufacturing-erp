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
if(!isset($result) || empty($result)) {
    // Create a simple spreadsheet with error message
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No itemwise report data found for the selected criteria');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Itemwise_Report_No_Data.xlsx';
    
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

// Rename the sheet
$sheet->setTitle('Itemwise Report');

// Main Header
$sheet->mergeCells('A1:M1');
$sheet->setCellValue('A1', 'ITEMWISE REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Filter Information
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:M2');
    $filter_text = 'Period: ' . $from_date . ' to ' . $to_date;
    if(isset($company_name) && !empty($company_name)) {
        $filter_text .= ' | Company: ' . $company_name;
    }
    if(isset($product_name) && !empty($product_name)) {
        $filter_text .= ' | Item: ' . $product_name;
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
    'B' . $startRow => 'Invoice No',
    'C' . $startRow => 'Invoice Date',
    'D' . $startRow => 'Invoice Amount (₹)',
    'E' . $startRow => 'Invoice Balance (₹)',
    'F' . $startRow => 'Payment Due Date',
    'G' . $startRow => 'Company Name',
    'H' . $startRow => 'Customer Name',
    'I' . $startRow => 'Pan Card',
    'J' . $startRow => 'Email',
    'K' . $startRow => 'Mobile',
    'L' . $startRow => 'Address',
    'M' . $startRow => 'Item Name'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':M' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_amount = 0;
$total_balance = 0;

foreach ($result as $row) {
    $amount = isset($row->total) ? floatval($row->total) : 0;
    $balance = isset($row->balance) ? floatval($row->balance) : 0;
    
    $total_amount += $amount;
    $total_balance += $balance;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->invoice_number) ? $row->invoice_number : '');
    $sheet->setCellValue('C' . $rowCount, isset($row->invoice_date) ? date("d-m-Y", strtotime($row->invoice_date)) : '');
    $sheet->setCellValue('D' . $rowCount, $amount);
    $sheet->setCellValue('E' . $rowCount, $balance);
    $sheet->setCellValue('F' . $rowCount, isset($row->payment_due_date) ? $row->payment_due_date : '');
    $sheet->setCellValue('G' . $rowCount, isset($row->company_name) ? $row->company_name : '');
    $sheet->setCellValue('H' . $rowCount, isset($row->fullname) ? $row->fullname : '');
    $sheet->setCellValue('I' . $rowCount, isset($row->pancard) ? $row->pancard : '');
    $sheet->setCellValue('J' . $rowCount, isset($row->email) ? $row->email : '');
    $sheet->setCellValue('K' . $rowCount, isset($row->mobile) ? $row->mobile : '');
    $sheet->setCellValue('L' . $rowCount, isset($row->address) ? $row->address : '');
    $sheet->setCellValue('M' . $rowCount, isset($row->product_name) ? $row->product_name : '');
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':M' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('C' . $rowCount)->applyFromArray($centerAlignStyle); // Date centered
    $sheet->getStyle('F' . $rowCount)->applyFromArray($centerAlignStyle); // Due Date centered
    $sheet->getStyle('D' . $rowCount . ':E' . $rowCount)->applyFromArray($rightAlignStyle); // Amounts right-aligned
    
    // Format amount columns
    $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, '');
    $sheet->setCellValue('C' . $totalRow, 'TOTALS:');
    $sheet->setCellValue('D' . $totalRow, $total_amount);
    $sheet->setCellValue('E' . $totalRow, $total_balance);
    
    // Style for total row
    $sheet->getStyle('A' . $totalRow . ':M' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('C' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('D' . $totalRow . ':E' . $totalRow)->applyFromArray($rightAlignStyle);
    
    // Format total row numbers
    $sheet->getStyle('D' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':F' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Invoices: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('G' . $summaryRow . ':M' . $summaryRow);
$sheet->setCellValue('G' . $summaryRow, 'Outstanding Amount: ₹ ' . number_format($total_balance, 2));
$sheet->getStyle('G' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Signature Section
$signatureRow = $summaryRow + 3;

$sheet->mergeCells('A' . $signatureRow . ':F' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('G' . $signatureRow . ':M' . $signatureRow);
$sheet->setCellValue('G' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':F' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('G' . $signatureRow . ':M' . $signatureRow);
$sheet->setCellValue('G' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':M' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':M' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated itemwise report. For any discrepancies, please contact the accounts department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'M') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(12);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(12);
$sheet->getColumnDimension('G')->setWidth(20);
$sheet->getColumnDimension('H')->setWidth(20);
$sheet->getColumnDimension('I')->setWidth(15);
$sheet->getColumnDimension('J')->setWidth(25);
$sheet->getColumnDimension('K')->setWidth(15);
$sheet->getColumnDimension('L')->setWidth(30);
$sheet->getColumnDimension('M')->setWidth(20);

// Generate filename
$company_display = isset($company_name) && !empty($company_name) ? preg_replace('/[^A-Za-z0-9\-]/', '_', $company_name) : 'All';
$product_display = isset($product_name) && !empty($product_name) ? preg_replace('/[^A-Za-z0-9\-]/', '_', $product_name) : 'All';
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Itemwise_Report_' . $company_display . '_' . $product_display . '_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>