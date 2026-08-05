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
    $sheet->setCellValue('A1', 'No expenditure category data found for the selected date range');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Expenditure_Category_Report_No_Data.xlsx';
    
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
$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

// Define style arrays
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 16,
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
        'startColor' => ['rgb' => '31a3dd'],
    ],
    'font' => [
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Rename the sheet
$sheet->setTitle('Expenditure Category');

// Main Header
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1', 'EXPENDITURE CATEGORY REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Date Range
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:C2');
    $sheet->setCellValue('A2', 'Period: ' . $from_date . ' to ' . $to_date);
    $sheet->getStyle('A2')->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'font' => ['italic' => true, 'size' => 11],
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
    'B' . $startRow => 'Expense Category',
    'C' . $startRow => 'Expense Amount (₹)'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':C' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_amount = 0;

// Store category totals for percentage calculation
$category_totals = [];

foreach ($result as $row) {
    $amount = isset($row->expense_amount) ? floatval($row->expense_amount) : 0;
    $total_amount += $amount;
    
    $category = isset($row->expense_category) ? $row->expense_category : 'Uncategorized';
    $category_totals[$category] = $amount;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $row->expense_category);
    $sheet->setCellValue('C' . $rowCount, $amount);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':C' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('C' . $rowCount)->applyFromArray($rightAlignStyle); // Amount right-aligned
    
    // Format amount column
    $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, 'TOTAL:');
    $sheet->setCellValue('C' . $totalRow, $total_amount);
    
    // Style for total row
    $sheet->getStyle('A' . $totalRow . ':C' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('C' . $totalRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':B' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Categories: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->setCellValue('C' . $summaryRow, 'Total Expenditure: ₹ ' . number_format($total_amount, 2));
$sheet->getStyle('C' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Category Analysis
$analysisRow = $summaryRow + 2;

$sheet->mergeCells('A' . $analysisRow . ':C' . $analysisRow);
$sheet->setCellValue('A' . $analysisRow, 'CATEGORY ANALYSIS');
$sheet->getStyle('A' . $analysisRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$analysisRow++;

$sheet->setCellValue('A' . $analysisRow, 'Category');
$sheet->setCellValue('B' . $analysisRow, 'Amount (₹)');
$sheet->setCellValue('C' . $analysisRow, 'Percentage (%)');
$sheet->getStyle('A' . $analysisRow . ':C' . $analysisRow)->applyFromArray(['font' => ['bold' => true]]);
$analysisRow++;

// Sort categories by amount (descending)
arsort($category_totals);

foreach ($category_totals as $category => $amount) {
    $percentage = ($total_amount > 0) ? ($amount / $total_amount) * 100 : 0;
    
    $sheet->setCellValue('A' . $analysisRow, $category);
    $sheet->setCellValue('B' . $analysisRow, $amount);
    $sheet->setCellValue('C' . $analysisRow, number_format($percentage, 2) . '%');
    
    $sheet->getStyle('B' . $analysisRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('B' . $analysisRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $analysisRow++;
}

// Find highest and lowest categories
$highest_category = '';
$highest_amount = 0;
$lowest_category = '';
$lowest_amount = PHP_FLOAT_MAX;

foreach ($category_totals as $category => $amount) {
    if($amount > $highest_amount) {
        $highest_amount = $amount;
        $highest_category = $category;
    }
    if($amount < $lowest_amount) {
        $lowest_amount = $amount;
        $lowest_category = $category;
    }
}

// Category Statistics
$statsRow = $analysisRow + 2;

$sheet->mergeCells('A' . $statsRow . ':C' . $statsRow);
$sheet->setCellValue('A' . $statsRow, 'CATEGORY STATISTICS');
$sheet->getStyle('A' . $statsRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$statsRow++;

$statisticsData = [
    ['Highest Spending Category', $highest_category, $highest_amount],
    ['Lowest Spending Category', $lowest_category, $lowest_amount],
    ['Average per Category', '', ($rowCou > 1 ? $total_amount / ($rowCou - 1) : 0)]
];

foreach ($statisticsData as $index => $data) {
    $sheet->setCellValue('A' . ($statsRow + $index), $data[0]);
    $sheet->setCellValue('B' . ($statsRow + $index), $data[1]);
    $sheet->setCellValue('C' . ($statsRow + $index), $data[2]);
    $sheet->getStyle('A' . ($statsRow + $index))->applyFromArray(['font' => ['bold' => true]]);
    $sheet->getStyle('C' . ($statsRow + $index))->applyFromArray($rightAlignStyle);
    if(is_numeric($data[2])) {
        $sheet->getStyle('C' . ($statsRow + $index))->getNumberFormat()->setFormatCode('#,##0.00');
    }
}

// Top Categories
$topCategoriesRow = $statsRow + 5;

$sheet->mergeCells('A' . $topCategoriesRow . ':C' . $topCategoriesRow);
$sheet->setCellValue('A' . $topCategoriesRow, 'TOP 3 EXPENDITURE CATEGORIES');
$sheet->getStyle('A' . $topCategoriesRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$topCategoriesRow++;

$sheet->setCellValue('A' . $topCategoriesRow, 'Rank');
$sheet->setCellValue('B' . $topCategoriesRow, 'Category');
$sheet->setCellValue('C' . $topCategoriesRow, 'Amount (₹)');
$sheet->getStyle('A' . $topCategoriesRow . ':C' . $topCategoriesRow)->applyFromArray(['font' => ['bold' => true]]);
$topCategoriesRow++;

$rank = 1;
$top_categories = array_slice($category_totals, 0, 3, true);

foreach ($top_categories as $category => $amount) {
    $sheet->setCellValue('A' . $topCategoriesRow, $rank);
    $sheet->setCellValue('B' . $topCategoriesRow, $category);
    $sheet->setCellValue('C' . $topCategoriesRow, $amount);
    
    $sheet->getStyle('A' . $topCategoriesRow)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('C' . $topCategoriesRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $topCategoriesRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rank++;
    $topCategoriesRow++;
}

// Signature Section
$signatureRow = $topCategoriesRow + 3;

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
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated expenditure category report. For any discrepancies, please contact the finance department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'C') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(18);

// Generate filename
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Expenditure_Category_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>