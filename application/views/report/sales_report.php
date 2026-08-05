<?php

// No direct script access
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '/third_party/amount_convert.php');

// Load PhpSpreadsheet via Composer autoload
require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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
        'size' => 20,
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
    'font' => [
        'size' => 11,
    ],
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
        'size' => 16,
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
$sheet->setTitle('Sales Report');

// Merge cells for main header
$sheet->mergeCells('A1:M1'); // Changed from L1 to M1

// Set main header with style
$sheet->setCellValue('A1', 'Sales Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Increase height of header row
$sheet->getRowDimension(1)->setRowHeight(35);

// Set column headers (ADDED Customer Code column)
$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'Customer Code',    // NEW - Customer Code column
    'C2' => 'Invoice No',
    'D2' => 'Invoice Date',
    'E2' => 'Company Name',
    'F2' => 'Type',
    'G2' => 'Total Before Tax',
    'H2' => 'SGST',
    'I2' => 'CGST',
    'J2' => 'IGST',
    'K2' => 'Total GST',
    'L2' => 'Grand Total',
    'M2' => 'Balance',
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style (Updated range to M2)
$sheet->getStyle('A2:M2')->applyFromArray($columnHeaderStyle);

// Increase height of column header row
$sheet->getRowDimension(2)->setRowHeight(30);

// Populate data
$rowCount = 3;
$rowCou = 1;
$t_before_tax = 0; $t_sgst = 0; $t_igst = 0; $t_gst = 0; $t_total = 0; $t_balance = 0;

foreach ($result as $row) {
    // Calculate GST values
    $gst_type = '';
    $sgst = 0;
    $igst = 0;
    
    if ($row->gst_type != 'I') {
        $gst_type = 'SGST';
        $sgst = $row->total_gst_amount / 2;
    } else {
        $gst_type = 'IGST';
        $igst = $row->total_gst_amount;
    }
    
    // Set data (ADDED Customer Code at column B)
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $row->c_code);  // NEW - Customer Code
    $sheet->setCellValue('C' . $rowCount, $row->invoice_number);
    $sheet->setCellValue('D' . $rowCount, date("d-m-Y", strtotime($row->invoice_date)));
    $sheet->setCellValue('E' . $rowCount, $row->company_name);
    $sheet->setCellValue('F' . $rowCount, $gst_type);
    $sheet->setCellValueExplicit('G' . $rowCount, indian_number_format((float)$row->total_before_tax, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('H' . $rowCount, indian_number_format((float)$sgst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('I' . $rowCount, indian_number_format((float)$sgst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('J' . $rowCount, indian_number_format((float)$igst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('K' . $rowCount, indian_number_format((float)$row->total_gst_amount, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('L' . $rowCount, indian_number_format((float)$row->total, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('M' . $rowCount, indian_number_format((float)$row->balance, 2), DataType::TYPE_STRING);
    
    // Calculate totals
    $t_before_tax += (float)$row->total_before_tax; 
    $t_sgst += (float)$sgst; 
    $t_igst += (float)$igst;
    $t_gst += (float)$row->total_gst_amount; 
    $t_total += (float)$row->total; 
    $t_balance += (float)$row->balance;
    
    // Apply border style to data row (Updated to M column)
    $sheet->getStyle('A' . $rowCount . ':M' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Right align amount columns (Updated column letters)
    $amountColumns = ['G', 'H', 'I', 'J', 'K', 'L', 'M'];
    foreach ($amountColumns as $col) {
        $sheet->getStyle($col . $rowCount)->applyFromArray($rightAlignStyle);
    }
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

// Set total row labels (Updated column references)
$sheet->setCellValue('F' . $totalRow, 'Total:');  // Changed from E to F due to new column
$sheet->setCellValue('G' . $totalRow, 'Total:'); // Changed from F to G

// Apply total row style to cells that will have totals (Updated column range)
$totalCells = ['F' . $totalRow, 'G' . $totalRow, 'H' . $totalRow, 'I' . $totalRow, 
               'J' . $totalRow, 'K' . $totalRow, 'L' . $totalRow, 'M' . $totalRow];
foreach ($totalCells as $cell) {
    $sheet->getStyle($cell)->applyFromArray($totalRowStyle);
}

// Add SUM values (Updated column references)
$lastDataRow = $rowCount - 1;

$formulas = [
    'G' . $totalRow => indian_number_format($t_before_tax, 2),   // Changed from F to G
    'H' . $totalRow => indian_number_format($t_sgst, 2),        // Changed from G to H
    'I' . $totalRow => indian_number_format($t_sgst, 2),        // Changed from H to I
    'J' . $totalRow => indian_number_format($t_igst, 2),        // Changed from I to J
    'K' . $totalRow => indian_number_format($t_gst, 2),         // Changed from J to K
    'L' . $totalRow => indian_number_format($t_total, 2),       // Changed from K to L
    'M' . $totalRow => indian_number_format($t_balance, 2)      // Changed from L to M
];

foreach ($formulas as $cell => $value) {
    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);
}

// Auto-size columns (Updated range to M)
foreach (range('A', 'M') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set column alignment for specific columns
$sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date column centered
$sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // Company Name left aligned
$sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Type column centered

// Format amount columns as numbers with 2 decimal places (Updated column letters)
$amountColumns = ['G', 'H', 'I', 'J', 'K', 'L', 'M'];
foreach ($amountColumns as $col) {
    $sheet->getStyle($col . '3:' . $col . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle($col . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Generate filename
$from_date_display = isset($from_date) ? $from_date : date('d-m-Y');
$to_date_display = isset($to_date) ? $to_date : date('d-m-Y');
$file_name = 'Sales_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>