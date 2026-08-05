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
$sheet->setTitle('Customer Report');

// Company Header
$sheet->mergeCells('A1:K1');
$sheet->setCellValue('A1', 'CUSTOMER REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Date Range
$sheet->mergeCells('A2:K2');
$sheet->setCellValue('A2', 'Period: ' . (isset($from_date) ? $from_date : '') . ' to ' . (isset($to_date) ? $to_date : ''));
$sheet->getStyle('A2')->applyFromArray([
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'font' => ['italic' => true, 'size' => 12],
]);

// Empty row
$sheet->setCellValue('A3', '');

// Set column headers (starting from row 4)
$headers = [
    'A4' => 'Sr.No.',
    'B4' => 'Full Name',
    'C4' => 'Company Name',
    'D4' => 'Email',
    'E4' => 'Mobile',
    'F4' => 'Address',
    'G4' => 'State Code',
    'H4' => 'Pan Card',
    'I4' => 'GST No',
    'J4' => 'Customer Code',
    'K4' => 'Receivable (₹)'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A4:K4')->applyFromArray($columnHeaderStyle);

// Populate data (starting from row 5)
$rowCount = 5;
$rowCou = 1;
$total_balance = 0;

if(isset($result) && !empty($result)) {
    foreach ($result as $row) {
        $total_balance += isset($row->balance) ? $row->balance : 0;
        
        $sheet->setCellValue('A' . $rowCount, $rowCou);
        $sheet->setCellValue('B' . $rowCount, $row->fullname);
        $sheet->setCellValue('C' . $rowCount, $row->company_name);
        $sheet->setCellValue('D' . $rowCount, $row->email);
        $sheet->setCellValue('E' . $rowCount, $row->mobile);
        $sheet->setCellValue('F' . $rowCount, $row->address);
        $sheet->setCellValue('G' . $rowCount, $row->state_code);
        $sheet->setCellValue('H' . $rowCount, $row->pancard);
        $sheet->setCellValue('I' . $rowCount, $row->gst);
        $sheet->setCellValue('J' . $rowCount, $row->c_code);
        $sheet->setCellValue('K' . $rowCount, $row->balance);
        
        // Apply border style to data row
        $sheet->getStyle('A' . $rowCount . ':K' . $rowCount)->applyFromArray($dataCellStyle);
        
        // Apply alignment styles
        $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
        $sheet->getStyle('G' . $rowCount)->applyFromArray($centerAlignStyle); // State Code centered
        $sheet->getStyle('K' . $rowCount)->applyFromArray($rightAlignStyle); // Amount right-aligned
        
        // Format amount column
        $sheet->getStyle('K' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
        
        $rowCou++;
        $rowCount++;
    }
}

// Add total row
$totalRow = $rowCount;
$lastDataRow = $rowCount - 1;

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
    $sheet->setCellValue('I' . $totalRow, '');
    $sheet->setCellValue('J' . $totalRow, 'TOTAL RECEIVABLE:');
    $sheet->setCellValue('K' . $totalRow, $total_balance);
    
    // Style for total row
    $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('J' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('K' . $totalRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('K' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':F' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Customers: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('G' . $summaryRow . ':K' . $summaryRow);
$average_balance = ($rowCou > 1) ? $total_balance / ($rowCou - 1) : 0;
$sheet->setCellValue('G' . $summaryRow, 'Average Receivable: ₹ ' . number_format($average_balance, 2));
$sheet->getStyle('G' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// GST Summary Section (from the commented section in your original view)
if(isset($sgst) || isset($cgst) || isset($igst)) {
    $gstRow = $summaryRow + 2;
    
    $sheet->mergeCells('A' . $gstRow . ':K' . $gstRow);
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
    
    $sgst_amount = isset($sgst[0]->sgst) ? $sgst[0]->sgst : 0;
    $cgst_amount = isset($cgst[0]->cgst) ? $cgst[0]->cgst : 0;
    $igst_amount = isset($igst[0]->igst) ? $igst[0]->igst : 0;
    
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
$signatureRow = ($gstRow ?? $summaryRow) + 5;

$sheet->mergeCells('A' . $signatureRow . ':D' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('H' . $signatureRow . ':K' . $signatureRow);
$sheet->setCellValue('H' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':D' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('H' . $signatureRow . ':K' . $signatureRow);
$sheet->setCellValue('H' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':K' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':K' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated customer report. For any discrepancies, please contact the accounts department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->setColor(new Color('808080'));
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'K') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);   // Sr.No.
$sheet->getColumnDimension('B')->setWidth(20);  // Full Name
$sheet->getColumnDimension('C')->setWidth(20);  // Company Name
$sheet->getColumnDimension('D')->setWidth(25);  // Email
$sheet->getColumnDimension('E')->setWidth(15);  // Mobile
$sheet->getColumnDimension('F')->setWidth(30);  // Address
$sheet->getColumnDimension('G')->setWidth(12);  // State Code
$sheet->getColumnDimension('H')->setWidth(15);  // Pan Card
$sheet->getColumnDimension('I')->setWidth(18);  // GST No
$sheet->getColumnDimension('J')->setWidth(15);  // Customer Code
$sheet->getColumnDimension('K')->setWidth(18);  // Receivable

// Generate filename
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Customer_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>