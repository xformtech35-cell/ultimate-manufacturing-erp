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
    $sheet->setCellValue('A1', 'No supplier data found for the selected date range');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Supplier_Report_No_Data.xlsx';
    
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
$sheet->setTitle('Supplier Report');

// Main Header
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A1', 'SUPPLIER REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Date Range (if available)
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:J2');
    $sheet->setCellValue('A2', 'Period: ' . $from_date . ' to ' . $to_date);
    $sheet->getStyle('A2')->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'font' => ['italic' => true, 'size' => 12],
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
    'B' . $startRow => 'Full Name',
    'C' . $startRow => 'Company Name',
    'D' . $startRow => 'Email',
    'E' . $startRow => 'Mobile',
    'F' . $startRow => 'Address',
    'G' . $startRow => 'State Code',
    'H' . $startRow => 'Pan Card',
    'I' . $startRow => 'GST No',
    'J' . $startRow => 'Payable (₹)'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':J' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_balance = 0;

foreach ($result as $row) {
    $balance = isset($row->balance) ? floatval($row->balance) : 0;
    $total_balance += $balance;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->fullname) ? $row->fullname : '');
    $sheet->setCellValue('C' . $rowCount, isset($row->company_name) ? $row->company_name : '');
    $sheet->setCellValue('D' . $rowCount, isset($row->email) ? $row->email : '');
    $sheet->setCellValue('E' . $rowCount, isset($row->mobile) ? $row->mobile : '');
    $sheet->setCellValue('F' . $rowCount, isset($row->address) ? $row->address : '');
    $sheet->setCellValue('G' . $rowCount, isset($row->state_code) ? $row->state_code : '');
    $sheet->setCellValue('H' . $rowCount, isset($row->pancard) ? $row->pancard : '');
    $sheet->setCellValue('I' . $rowCount, isset($row->gst) ? $row->gst : '');
    $sheet->setCellValue('J' . $rowCount, $balance);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':J' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('G' . $rowCount)->applyFromArray($centerAlignStyle); // State Code centered
    $sheet->getStyle('J' . $rowCount)->applyFromArray($rightAlignStyle); // Amount right-aligned
    
    // Format amount column
    $sheet->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, '');
    $sheet->setCellValue('C' . $totalRow, '');
    $sheet->setCellValue('D' . $totalRow, '');
    $sheet->setCellValue('E' . $totalRow, '');
    $sheet->setCellValue('F' . $totalRow, '');
    $sheet->setCellValue('G' . $totalRow, '');
    $sheet->setCellValue('H' . $totalRow, '');
    $sheet->setCellValue('I' . $totalRow, 'TOTAL PAYABLE:');
    $sheet->setCellValue('J' . $totalRow, $total_balance);
    
    // Style for total row
    $sheet->getStyle('A' . $totalRow . ':J' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('I' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('J' . $totalRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('J' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':E' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Suppliers: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('F' . $summaryRow . ':J' . $summaryRow);
$average_balance = ($rowCou > 1) ? $total_balance / ($rowCou - 1) : 0;
$sheet->setCellValue('F' . $summaryRow, 'Average Payable: ₹ ' . number_format($average_balance, 2));
$sheet->getStyle('F' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// GST Summary Section (if needed)
if(isset($sgst) || isset($cgst) || isset($igst)) {
    $gstRow = $summaryRow + 2;
    
    $sheet->mergeCells('A' . $gstRow . ':J' . $gstRow);
    $sheet->setCellValue('A' . $gstRow, 'GST SUMMARY');
    $sheet->getStyle('A' . $gstRow)->applyFromArray([
        'font' => ['bold' => true, 'size' => 12],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '3c8dbc'],
        ],
        'font' => ['color' => ['rgb' => 'FFFFFF']],
    ]);
    
    $gstRow++;
    
    $sgst_amount = isset($sgst[0]->sgst) ? floatval($sgst[0]->sgst) : 0;
    $cgst_amount = isset($cgst[0]->cgst) ? floatval($cgst[0]->cgst) : 0;
    $igst_amount = isset($igst[0]->igst) ? floatval($igst[0]->igst) : 0;
    
    $gstData = [
        ['SGST', $sgst_amount],
        ['CGST', $cgst_amount],
        ['IGST', $igst_amount],
        ['Total CGST & SGST', $sgst_amount + $cgst_amount],
        ['Total IGST', $igst_amount],
    ];
    
    foreach ($gstData as $index => $data) {
        $sheet->setCellValue('A' . ($gstRow + $index), $data[0]);
        $sheet->setCellValue('B' . ($gstRow + $index), $data[1]);
        $sheet->getStyle('A' . ($gstRow + $index))->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('B' . ($gstRow + $index))->applyFromArray($rightAlignStyle);
        $sheet->getStyle('B' . ($gstRow + $index))->getNumberFormat()->setFormatCode('#,##0.00');
    }
}

// Signature Section
$signatureRow = (isset($gstRow) ? $gstRow + 5 : $summaryRow + 5);

$sheet->mergeCells('A' . $signatureRow . ':D' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('G' . $signatureRow . ':J' . $signatureRow);
$sheet->setCellValue('G' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':D' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('G' . $signatureRow . ':J' . $signatureRow);
$sheet->setCellValue('G' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':J' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':J' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated supplier report. For any discrepancies, please contact the accounts department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'J') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(25);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(30);
$sheet->getColumnDimension('G')->setWidth(12);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(18);
$sheet->getColumnDimension('J')->setWidth(18);

// Generate filename
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Supplier_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>