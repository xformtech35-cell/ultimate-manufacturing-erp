<?php
// No direct script access
defined('BASEPATH') OR exit('No direct script access allowed');

// Load PhpSpreadsheet via Composer autoload
require_once APPPATH . '/third_party/amount_convert.php';
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
$spreadsheet->getDefaultStyle()->getFont()->setSize(9);

// Define style arrays (matching sales_report.php)
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
        'size' => 11,
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
        'size' => 10,
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

// Rename sheet
$sheet->setTitle('GRN Report');

// Merge for main header
$sheet->mergeCells('A1:N1');
$sheet->setCellValue('A1', 'GRN Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(35);

// Company info (row 2-3)
$sheet->mergeCells('A2:N2');
$sheet->mergeCells('A3:N3');
$sheet->setCellValue('A2', isset($settings['company_name']) ? $settings['company_name'] : 'Your Company');
$sheet->setCellValue('A3', isset($settings['company_gst']) ? 'GST: ' . $settings['company_gst'] : '');
$sheet->getStyle('A2:A3')->applyFromArray($headerStyle);
$sheet->getRowDimension(2)->setRowHeight(20);
$sheet->getRowDimension(3)->setRowHeight(20);

// Date range
$sheet->mergeCells('A4:N4');
$date_text = 'Period: ' . ($from_date ?? '01-' . date('m-Y')) . ' to ' . ($to_date ?? date('d-m-Y'));
$sheet->setCellValue('A4', $date_text);
$sheet->getStyle('A4')->applyFromArray($headerStyle);
$sheet->getRowDimension(4)->setRowHeight(25);

// Column headers (row 6)
$headers = [
    'A5' => 'Sr.No.', 'B5' => 'GRN No.', 'C5' => 'Date', 'D5' => 'PO No.',
    'E5' => 'Product Name', 'F5' => 'Qty', 'G5' => 'Rec. Qty', 'H5' => 'Pend. Qty',
    'I5' => 'Price', 'J5' => 'Amount', 'K5' => 'HSN', 'L5' => 'GST%', 
    'M5' => 'Supplier', 'N5' => 'Total'
];
foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}
$sheet->getStyle('A5:N5')->applyFromArray($columnHeaderStyle);
$sheet->getRowDimension(5)->setRowHeight(30);

// Data population
$rowCount = 6;
$i = 1;
$grand_total = 0;
foreach ($result as $row) {
    $row_total = floatval($row->total ?? 0);
    $grand_total += $row_total;
    
    $sheet->setCellValue('A' . $rowCount, $i++);
    $sheet->setCellValue('B' . $rowCount, $row->grn_number ?? '');
    $sheet->setCellValue('C' . $rowCount, isset($row->date) ? date('d-m-Y', strtotime($row->date)) : 'N/A');
    $sheet->setCellValue('D' . $rowCount, $row->po_number_fk ?? 'N/A');
    $sheet->setCellValue('E' . $rowCount, $row->product_name ?? 'N/A');
    $sheet->setCellValue('F' . $rowCount, number_format($row->quantity ?? 0));
    $sheet->setCellValue('G' . $rowCount, number_format($row->received_quantity ?? 0));
    $sheet->setCellValue('H' . $rowCount, number_format($row->pending_quantity ?? 0));
    $sheet->setCellValue('I' . $rowCount, number_format($row->price ?? 0, 2));
    $sheet->setCellValueExplicit('J' . $rowCount, indian_number_format((float)($row->amount ?? 0), 2), DataType::TYPE_STRING);
    $sheet->setCellValue('K' . $rowCount, $row->hsn_code ?? '');
    $sheet->setCellValue('L' . $rowCount, ($row->gst ?? 0) . '%');
    $sheet->setCellValue('M' . $rowCount, $row->company_name ?? '');
    $sheet->setCellValueExplicit('N' . $rowCount, indian_number_format($row_total, 2), DataType::TYPE_STRING);
    
    // Styles
    $sheet->getStyle('A' . $rowCount . ':N' . $rowCount)->applyFromArray($dataCellStyle);
foreach (['I', 'J', 'N'] as $col) {
        $sheet->getStyle($col . $rowCount)->applyFromArray($rightAlignStyle);
    }
    $sheet->getStyle($col . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    $rowCount++;
}

// Grand Total Row
$totalRow = $rowCount;
    $sheet->mergeCells('A' . $totalRow . ':I' . $totalRow);
    $sheet->setCellValue('A' . $totalRow, 'GRAND TOTAL');
    $sheet->setCellValue('N' . $totalRow, indian_number_format($grand_total, 2));
$sheet->getStyle('A' . $totalRow . ':N' . $totalRow)->applyFromArray($totalRowStyle);
$sheet->getStyle('A' . $totalRow . ':N' . $totalRow)->applyFromArray($totalRowStyle);

// Auto-size columns
foreach (range('A', 'N') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Number formats
$amountCols = ['I', 'J', 'N'];
foreach ($amountCols as $col) {
    $sheet->getStyle($col . '6:' . $col . ($rowCount-1))->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle($col . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Filename
$from = $from_date ?? date('d-m-Y');
$to = $to_date ?? date('d-m-Y');
$file_name = 'GRN_Report_' . str_replace('/', '_', $from) . '_to_' . str_replace('/', '_', $to) . '.xlsx';

// Headers
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
