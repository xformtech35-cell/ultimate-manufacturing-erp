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
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN],
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
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$dataCellStyle = [
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$rightAlignStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
    ],
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$centerAlignStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN],
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
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Rename the sheet
$sheet->setTitle('All Transactions');

// Merge cells for main header
$sheet->mergeCells('A1:I1');

// Set main header with style
$sheet->setCellValue('A1', 'Bank Statement Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers
$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'Date',
    'C2' => 'REF NO.',
    'D2' => 'Name',
    'E2' => 'Type',
    'F2' => 'Total',
    'G2' => 'Received / Paid',
    'H2' => 'Sales / Purchase',
    'I2' => 'Balance'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A2:I2')->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = 3;
$rowCou = 1;

// Helper function to get status text
function getStatusText($status) {
    switch ($status) {
        case 1: return 'Draft';
        case 2: return 'Sent';
        case 3: return 'Viewed';
        case 4: return 'Approved';
        case 5: return 'Rejected';
        case 6: return 'Canceled';
        default: return '';
    }
}

// Process sales data
if (isset($result_sales)) {
    foreach ($result_sales as $row) {
        $gst_type = ($row->gst_type != 'I') ? 'SGST' : 'IGST';
        $receive = getStatusText($row->status);
        
        $sheet->setCellValue('A' . $rowCount, $rowCou);
        $sheet->setCellValue('B' . $rowCount, date("d-m-Y", strtotime($row->date)));
        $sheet->setCellValue('C' . $rowCount, $row->number_fk);
        $sheet->setCellValue('D' . $rowCount, $row->company_name);
        $sheet->setCellValue('E' . $rowCount, $gst_type);
        $sheet->setCellValue('F' . $rowCount, $row->total);
        $sheet->setCellValue('G' . $rowCount, $receive);
        $sheet->setCellValue('H' . $rowCount, 'Sales');
        $sheet->setCellValue('I' . $rowCount, $row->total);
        
        // Apply styles
        $sheet->getStyle('A' . $rowCount . ':I' . $rowCount)->applyFromArray($dataCellStyle);
        $sheet->getStyle('B' . $rowCount)->applyFromArray($centerAlignStyle); // Date centered
        $sheet->getStyle('E' . $rowCount)->applyFromArray($centerAlignStyle); // Type centered
        $sheet->getStyle('F' . $rowCount)->applyFromArray($rightAlignStyle); // Total right-aligned
        $sheet->getStyle('G' . $rowCount)->applyFromArray($centerAlignStyle); // Status centered
        $sheet->getStyle('H' . $rowCount)->applyFromArray($centerAlignStyle); // Transaction type centered
        $sheet->getStyle('I' . $rowCount)->applyFromArray($rightAlignStyle); // Balance right-aligned
        
        $rowCou++;
        $rowCount++;
    }
}

// Process quotation data
if (isset($result_quotation)) {
    foreach ($result_quotation as $row) {
        $gst_type = ($row->gst_type != 'I') ? 'SGST' : 'IGST';
        $receive = getStatusText($row->status);
        
        $sheet->setCellValue('A' . $rowCount, $rowCou);
        $sheet->setCellValue('B' . $rowCount, date("d-m-Y", strtotime($row->date)));
        $sheet->setCellValue('C' . $rowCount, $row->number_fk);
        $sheet->setCellValue('D' . $rowCount, $row->company_name);
        $sheet->setCellValue('E' . $rowCount, $gst_type);
        $sheet->setCellValue('F' . $rowCount, $row->total);
        $sheet->setCellValue('G' . $rowCount, $receive);
        $sheet->setCellValue('H' . $rowCount, 'Quotation');
        $sheet->setCellValue('I' . $rowCount, $row->total);
        
        // Apply styles
        $sheet->getStyle('A' . $rowCount . ':I' . $rowCount)->applyFromArray($dataCellStyle);
        $sheet->getStyle('B' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('E' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('F' . $rowCount)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('G' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('H' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('I' . $rowCount)->applyFromArray($rightAlignStyle);
        
        $rowCou++;
        $rowCount++;
    }
}

// Process purchase data
if (isset($result_purchase)) {
    foreach ($result_purchase as $row) {
        $gst_type = ($row->gst_type != 'I') ? 'SGST' : 'IGST';
        $receive = getStatusText($row->status);
        
        $sheet->setCellValue('A' . $rowCount, $rowCou);
        $sheet->setCellValue('B' . $rowCount, date("d-m-Y", strtotime($row->date)));
        $sheet->setCellValue('C' . $rowCount, $row->number_fk);
        $sheet->setCellValue('D' . $rowCount, $row->company_name);
        $sheet->setCellValue('E' . $rowCount, $gst_type);
        $sheet->setCellValue('F' . $rowCount, $row->total);
        $sheet->setCellValue('G' . $rowCount, $receive);
        $sheet->setCellValue('H' . $rowCount, 'Purchase');
        $sheet->setCellValue('I' . $rowCount, $row->total);
        
        // Apply styles
        $sheet->getStyle('A' . $rowCount . ':I' . $rowCount)->applyFromArray($dataCellStyle);
        $sheet->getStyle('B' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('E' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('F' . $rowCount)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('G' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('H' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('I' . $rowCount)->applyFromArray($rightAlignStyle);
        
        $rowCou++;
        $rowCount++;
    }
}

// Add total row
$totalRow = $rowCount;

// Set total row labels
$sheet->setCellValue('D' . $totalRow, 'Total:');
$sheet->setCellValue('E' . $totalRow, 'Total:');

// Apply total row style
$totalCells = ['D' . $totalRow, 'E' . $totalRow, 'F' . $totalRow, 'G' . $totalRow];
foreach ($totalCells as $cell) {
    $sheet->getStyle($cell)->applyFromArray($totalRowStyle);
}

// Add SUM formulas
$lastDataRow = $rowCount - 1;

$formulas = [
    'E' . $totalRow => "=SUM(E3:E" . $lastDataRow . ")",
    'F' . $totalRow => "=SUM(F3:F" . $lastDataRow . ")",
    'G' . $totalRow => "=SUM(G3:G" . $lastDataRow . ")"
];

foreach ($formulas as $cell => $formula) {
    $sheet->setCellValue($cell, $formula);
}

// Clear B column total cell (as in original)
$sheet->setCellValue('B' . $totalRow, '');

// Auto-size columns
foreach (range('A', 'I') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Format amount columns as numbers with 2 decimal places
$amountColumns = ['F', 'I'];
foreach ($amountColumns as $col) {
    $sheet->getStyle($col . '3:' . $col . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle($col . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Generate filename
$from_date_display = isset($from_date) ? $from_date : date('d-m-Y');
$to_date_display = isset($to_date) ? $to_date : date('d-m-Y');
$transaction_type_display = isset($transaction_type) ? $transaction_type : 'all';
$file_name = 'All_transaction_' . $transaction_type_display . '_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>