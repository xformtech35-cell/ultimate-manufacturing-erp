<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '/third_party/amount_convert.php');
require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

// Title style
$titleStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 16],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '31a3dd']],
];

// Header style
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '31a3dd']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

// Data cell style
$dataStyle = [
    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$rightAlign = [
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
];

// Total row style
$totalRowStyle = [
    'font' => ['bold' => true, 'size' => 11],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e8f442']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

// ========== TITLE & DATE RANGE ==========
$sheet->setTitle('Sales HSN Report');

// Main title (merged A1 to G1)
$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'Sales Report (HSN Wise)');
$sheet->getStyle('A1')->applyFromArray($titleStyle);
$sheet->getRowDimension(1)->setRowHeight(30);

// Date range subtitle (merged A2 to G2)
$sheet->mergeCells('A2:G2');
$sheet->setCellValue('A2', 'Date Range: ' . date('d-m-Y', strtotime($from_date)) . ' to ' . date('d-m-Y', strtotime($to_date)));
$sheet->getStyle('A2')->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);
$sheet->getRowDimension(2)->setRowHeight(20);

// ========== HEADERS (ROW 3) ==========
$headers = [
    'A3' => 'Sr.No.',
    'B3' => 'HSN Code',
    'C3' => 'Total Value (incl. tax)',
    'D3' => 'Taxable Value (excl. tax)',
    'E3' => 'IGST',
    'F3' => 'CGST',
    'G3' => 'SGST',
];
foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}
$sheet->getStyle('A3:G3')->applyFromArray($headerStyle);
$sheet->getRowDimension(3)->setRowHeight(25);

// ========== DATA ROWS (START FROM ROW 4) ==========
$rowCount = 4;
$serial = 1;
$total_value = 0;
$total_taxable = 0;
$total_igst = 0;
$total_cgst = 0;
$total_sgst = 0;

foreach ($result as $row) {
    $sheet->setCellValue('A' . $rowCount, $serial);
    $sheet->setCellValue('B' . $rowCount, $row->hsn_code);
    $sheet->setCellValueExplicit('C' . $rowCount, indian_number_format((float)$row->total_value, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('D' . $rowCount, indian_number_format((float)$row->taxable_value, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('E' . $rowCount, indian_number_format((float)$row->igst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('F' . $rowCount, indian_number_format((float)$row->cgst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('G' . $rowCount, indian_number_format((float)$row->sgst, 2), DataType::TYPE_STRING);
    
    $sheet->getStyle('A' . $rowCount . ':G' . $rowCount)->applyFromArray($dataStyle);
    foreach (['C', 'D', 'E', 'F', 'G'] as $col) {
        $sheet->getStyle($col . $rowCount)->applyFromArray($rightAlign);
    }
    
    $total_value += (float)$row->total_value;
    $total_taxable += (float)$row->taxable_value;
    $total_igst += (float)$row->igst;
    $total_cgst += (float)$row->cgst;
    $total_sgst += (float)$row->sgst;
    
    $serial++;
    $rowCount++;
}

// ========== TOTAL ROW ==========
$totalRow = $rowCount;
$sheet->setCellValue('A' . $totalRow, 'Total');
$sheet->mergeCells('A' . $totalRow . ':B' . $totalRow);
$sheet->setCellValueExplicit('C' . $totalRow, indian_number_format($total_value, 2), DataType::TYPE_STRING);
$sheet->setCellValueExplicit('D' . $totalRow, indian_number_format($total_taxable, 2), DataType::TYPE_STRING);
$sheet->setCellValueExplicit('E' . $totalRow, indian_number_format($total_igst, 2), DataType::TYPE_STRING);
$sheet->setCellValueExplicit('F' . $totalRow, indian_number_format($total_cgst, 2), DataType::TYPE_STRING);
$sheet->setCellValueExplicit('G' . $totalRow, indian_number_format($total_sgst, 2), DataType::TYPE_STRING);
$sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->applyFromArray($totalRowStyle);
foreach (['C', 'D', 'E', 'F', 'G'] as $col) {
    $sheet->getStyle($col . $totalRow)->applyFromArray($rightAlign);
}

// ========== AUTO-SIZE COLUMNS ==========
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// ========== FILE NAME & OUTPUT ==========
$from_date_display = isset($from_date) ? date('d-m-Y', strtotime($from_date)) : date('d-m-Y');
$to_date_display = isset($to_date) ? date('d-m-Y', strtotime($to_date)) : date('d-m-Y');
$file_name = 'Sales_HSN_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>