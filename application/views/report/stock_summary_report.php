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
    $sheet->setCellValue('A1', 'No stock summary data found for the selected date range');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Stock_Summary_Report_No_Data.xlsx';
    
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
$sheet->setTitle('Stock Summary');

// Main Header
$sheet->mergeCells('A1:F1');
$sheet->setCellValue('A1', 'STOCK SUMMARY REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Date Range
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:F2');
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
    'B' . $startRow => 'Item Name',
    'C' . $startRow => 'Stock Quantity',
    'D' . $startRow => 'Purchase Price (₹)',
    'E' . $startRow => 'Sell Price (₹)',
    'F' . $startRow => 'Stock Value (₹)'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':F' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_purchase_value = 0;
$total_sell_value = 0;
$total_stock_value = 0;

foreach ($result as $row) {
    $stock = isset($row->stock) ? intval($row->stock) : 0;
    $cost_price = isset($row->cost_price) ? floatval($row->cost_price) : 0;
    $sell_price = isset($row->sell_price) ? floatval($row->sell_price) : 0;
    $stock_value = $cost_price * $stock;
    
    $total_purchase_value += $cost_price;
    $total_sell_value += $sell_price;
    $total_stock_value += $stock_value;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->code) ? $row->code : '');
    $sheet->setCellValue('C' . $rowCount, $stock);
    $sheet->setCellValue('D' . $rowCount, $cost_price);
    $sheet->setCellValue('E' . $rowCount, $sell_price);
    $sheet->setCellValue('F' . $rowCount, $stock_value);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':F' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('C' . $rowCount . ':F' . $rowCount)->applyFromArray($rightAlignStyle); // Numbers right-aligned
    
    // Format columns
    $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('D' . $rowCount . ':F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, 'TOTALS:');
    $sheet->setCellValue('C' . $totalRow, '');
    $sheet->setCellValue('D' . $totalRow, $total_purchase_value);
    $sheet->setCellValue('E' . $totalRow, $total_sell_value);
    $sheet->setCellValue('F' . $totalRow, $total_stock_value);
    
    // Style for total row
    $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('D' . $totalRow . ':F' . $totalRow)->applyFromArray($rightAlignStyle);
    
    // Format total row numbers
    $sheet->getStyle('D' . $totalRow . ':F' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':C' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Items: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('D' . $summaryRow . ':F' . $summaryRow);
$sheet->setCellValue('D' . $summaryRow, 'Total Stock Value: ₹ ' . number_format($total_stock_value, 2));
$sheet->getStyle('D' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Stock Valuation Summary
$valuationRow = $summaryRow + 2;

$sheet->mergeCells('A' . $valuationRow . ':F' . $valuationRow);
$sheet->setCellValue('A' . $valuationRow, 'STOCK VALUATION SUMMARY');
$sheet->getStyle('A' . $valuationRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$valuationRow++;

$valuationData = [
    ['Total Purchase Price', $total_purchase_value],
    ['Total Selling Price', $total_sell_value],
    ['Total Stock Value (Cost)', $total_stock_value],
    ['Potential Profit', $total_sell_value - $total_purchase_value]
];

$valuationCount = count($valuationData);
foreach ($valuationData as $index => $data) {
    $sheet->setCellValue('A' . ($valuationRow + $index), $data[0]);
    $sheet->setCellValue('F' . ($valuationRow + $index), $data[1]);
    
    // Apply yellow color to Potential Profit row (last item)
    if($index === $valuationCount - 1) {
        $sheet->getStyle('A' . ($valuationRow + $index) . ':F' . ($valuationRow + $index))->applyFromArray($totalRowStyle);
        $sheet->getStyle('F' . ($valuationRow + $index))->getNumberFormat()->setFormatCode('#,##0.00');
    } else {
        $sheet->getStyle('A' . ($valuationRow + $index))->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('F' . ($valuationRow + $index))->applyFromArray($rightAlignStyle);
        $sheet->getStyle('F' . ($valuationRow + $index))->getNumberFormat()->setFormatCode('#,##0.00');
    }
}

// Signature Section
$signatureRow = $valuationRow + 5;

$sheet->mergeCells('A' . $signatureRow . ':C' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('D' . $signatureRow . ':F' . $signatureRow);
$sheet->setCellValue('D' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':C' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('D' . $signatureRow . ':F' . $signatureRow);
$sheet->setCellValue('D' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':F' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':F' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated stock summary report. Values are based on current stock quantities and prices.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'F') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(18);
$sheet->getColumnDimension('E')->setWidth(18);
$sheet->getColumnDimension('F')->setWidth(18);

// Generate filename
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Stock_Summary_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>