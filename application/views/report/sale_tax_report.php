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
if(!isset($sale_tax_report) || empty($sale_tax_report)) {
    // Create a simple spreadsheet with error message
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No sales tax report data found for the selected date range');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Sales_Tax_Report_No_Data.xlsx';
    
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
$sheet->setTitle('Sales Tax Report');

// Main Header
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1', 'SALES TAX REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Date Range
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:C2');
    $sheet->setCellValue('A2', 'Period: ' . $from_date . ' to ' . $to_date);
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
    'B' . $startRow => 'Customer Name',
    'C' . $startRow => 'Sales Tax (₹)'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':C' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_tax = 0;
$total_sgst = 0;
$total_cgst = 0;
$total_igst = 0;

foreach ($sale_tax_report as $row) {
    // Calculate total tax (SGST*2 + IGST as per original code)
    $sgst = isset($row->sgst) ? floatval($row->sgst) : 0;
    $igst = isset($row->igst) ? floatval($row->igst) : 0;
    $total = ($sgst * 2) + $igst;
    
    $total_tax += $total;
    $total_sgst += $sgst;
    $total_cgst += $sgst; // CGST equals SGST
    $total_igst += $igst;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->company_name) ? $row->company_name : '');
    $sheet->setCellValue('C' . $rowCount, $total);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':C' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('C' . $rowCount)->applyFromArray($rightAlignStyle); // Tax right-aligned
    
    // Format tax column
    $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, 'TOTAL SALES TAX:');
    $sheet->setCellValue('C' . $totalRow, $total_tax);
    
    // Style for total row
    $sheet->getStyle('A' . $totalRow . ':C' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('C' . $totalRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':B' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Customers: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->setCellValue('C' . $summaryRow, 'Average Tax: ₹ ' . number_format(($rowCou > 1 ? $total_tax / ($rowCou - 1) : 0), 2));
$sheet->getStyle('C' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Tax Breakdown
$taxBreakdownRow = $summaryRow + 2;

$sheet->mergeCells('A' . $taxBreakdownRow . ':C' . $taxBreakdownRow);
$sheet->setCellValue('A' . $taxBreakdownRow, 'TAX BREAKDOWN');
$sheet->getStyle('A' . $taxBreakdownRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$taxBreakdownRow++;

$taxData = [
    ['Total SGST', $total_sgst],
    ['Total CGST', $total_cgst],
    ['Total IGST', $total_igst],
    ['Total Tax (SGST+CGST+IGST)', $total_sgst + $total_cgst + $total_igst]
];

foreach ($taxData as $index => $data) {
    $sheet->setCellValue('A' . ($taxBreakdownRow + $index), $data[0]);
    $sheet->setCellValue('C' . ($taxBreakdownRow + $index), $data[1]);
    
    // Apply yellow color to Total Tax row (last item)
    if($data[0] === 'Total Tax (SGST+CGST+IGST)') {
        $sheet->getStyle('A' . ($taxBreakdownRow + $index) . ':C' . ($taxBreakdownRow + $index))->applyFromArray($totalRowStyle);
    } else {
        $sheet->getStyle('A' . ($taxBreakdownRow + $index))->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('C' . ($taxBreakdownRow + $index))->applyFromArray($rightAlignStyle);
    }
    
    $sheet->getStyle('C' . ($taxBreakdownRow + $index))->getNumberFormat()->setFormatCode('#,##0.00');
}

// GST Summary
$gstSummaryRow = $taxBreakdownRow + 6;

$sheet->mergeCells('A' . $gstSummaryRow . ':C' . $gstSummaryRow);
$sheet->setCellValue('A' . $gstSummaryRow, 'GST SUMMARY');
$sheet->getStyle('A' . $gstSummaryRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$gstSummaryRow++;

$gstData = [
    ['Total SGST Paid', $total_sgst],
    ['Total CGST Paid', $total_cgst],
    ['Total IGST Paid', $total_igst],
    ['Total Tax Liability', $total_tax]
];

foreach ($gstData as $index => $data) {
    $sheet->setCellValue('A' . ($gstSummaryRow + $index), $data[0]);
    $sheet->setCellValue('C' . ($gstSummaryRow + $index), $data[1]);
    
    // Apply yellow color to Total Tax Liability row (last item)
    if($data[0] === 'Total Tax Liability') {
        $sheet->getStyle('A' . ($gstSummaryRow + $index) . ':C' . ($gstSummaryRow + $index))->applyFromArray($totalRowStyle);
    } else {
        $sheet->getStyle('A' . ($gstSummaryRow + $index))->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('C' . ($gstSummaryRow + $index))->applyFromArray($rightAlignStyle);
    }
    
    $sheet->getStyle('C' . ($gstSummaryRow + $index))->getNumberFormat()->setFormatCode('#,##0.00');
}

// Signature Section
$signatureRow = $gstSummaryRow + 6;

$sheet->mergeCells('A' . $signatureRow . ':B' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('C' . $signatureRow . ':C' . $signatureRow);
$sheet->setCellValue('C' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':B' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('C' . $signatureRow . ':C' . $signatureRow);
$sheet->setCellValue('C' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':C' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':C' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated sales tax report. For any discrepancies, please contact the accounts department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'C') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(18);

// Generate filename
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Sales_Tax_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>