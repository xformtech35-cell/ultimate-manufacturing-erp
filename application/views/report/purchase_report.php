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

$totalRowStyle = [
    'font' => [
        'bold' => true,
        'size' => 14,
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
$sheet->setTitle('Purchase Report');

// Merge cells for main header
$sheet->mergeCells('A1:L1');

// Set main header with style
$sheet->setCellValue('A1', 'Purchase Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers
$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'PO No',
    'C2' => 'PO Date',
    'D2' => 'Company Name',
    'E2' => 'Type',
    'F2' => 'Total Before Tax',
    'G2' => 'SGST',
    'H2' => 'CGST',
    'I2' => 'IGST',
    'J2' => 'Total GST',
    'K2' => 'Grand Total',
    'L2' => 'Balance',

];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A2:L2')->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = 3;
$rowCou = 1;

foreach ($result as $row) {
    // Determine GST type
    $gst_type = ($row->gst_type != 'I') ? 'SGST' : 'IGST';
    
    // Set data
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $row->number);
    $sheet->setCellValue('C' . $rowCount, date("d-m-Y", strtotime($row->purchase_date)));
    $sheet->setCellValue('D' . $rowCount, $row->company_name);
    $sheet->setCellValue('E' . $rowCount, $gst_type);
    $sheet->setCellValue('F' . $rowCount, $row->total_before_tax);
    $sheet->setCellValue('G' . $rowCount, $row->sgst);
    $sheet->setCellValue('H' . $rowCount, $row->cgst);
    $sheet->setCellValue('I' . $rowCount, $row->igst);
    $sheet->setCellValue('J' . $rowCount, $row->total_gst_amount);
    $sheet->setCellValue('K' . $rowCount, $row->total);
    $sheet->setCellValue('L' . $rowCount, $row->balance);

    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':L' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Right align amount columns
    $amountColumns = ['F', 'G', 'H', 'I', 'J', 'K', 'L'];
    foreach ($amountColumns as $col) {
        $sheet->getStyle($col . $rowCount)->applyFromArray($rightAlignStyle);
    }
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

// Set total row labels
$sheet->setCellValue('E' . $totalRow, 'Total:');
$sheet->setCellValue('F' . $totalRow, 'Total:');

// Apply total row style to cells that will have totals
$totalCells = ['E' . $totalRow, 'F' . $totalRow, 'G' . $totalRow, 'H' . $totalRow, 
               'I' . $totalRow, 'J' . $totalRow, 'K' . $totalRow, 'L' . $totalRow];
foreach ($totalCells as $cell) {
    $sheet->getStyle($cell)->applyFromArray($totalRowStyle);
}

// Add SUM formulas
$lastDataRow = $rowCount - 1;

$formulas = [
    'F' . $totalRow => "=SUM(F3:F" . $lastDataRow . ")",
    'G' . $totalRow => "=SUM(G3:G" . $lastDataRow . ")",
    'H' . $totalRow => "=SUM(H3:H" . $lastDataRow . ")",
    'I' . $totalRow => "=SUM(I3:I" . $lastDataRow . ")",
    'J' . $totalRow => "=SUM(J3:J" . $lastDataRow . ")",
    'K' . $totalRow => "=SUM(K3:K" . $lastDataRow . ")",
    'L' . $totalRow => "=SUM(L3:L" . $lastDataRow . ")"
];

foreach ($formulas as $cell => $formula) {
    $sheet->setCellValue($cell, $formula);
}

// Auto-size columns
foreach (range('A', 'L') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set column alignment for specific columns
$sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date column centered
$sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Type column centered
$sheet->getStyle('M')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Payment Due Date centered
$sheet->getStyle('N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // PO Date centered

// Format amount columns as numbers with 2 decimal places
$amountColumns = ['F', 'G', 'H', 'I', 'J', 'K', 'L'];
foreach ($amountColumns as $col) {
    $sheet->getStyle($col . '3:' . $col . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle($col . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Generate filename
$from_date_display = isset($from_date) ? $from_date : date('d-m-Y');
$to_date_display = isset($to_date) ? $to_date : date('d-m-Y');
$file_name = 'Purchase_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>