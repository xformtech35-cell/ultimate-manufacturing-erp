<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('GSTR1 Report');

$headerStyle = [
    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 16],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '31A3DD']],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$colHeaderStyle = [
    'font'      => ['bold' => true, 'color' => ['rgb' => '000000']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$dataStyle = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

// Row 1: Title
$sheet->mergeCells('A1:L1');
$sheet->setCellValue('A1', 'GSTR 1 Report');
$sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

// Row 2: Column headers
$headers = ['Sr.No.', 'Invoice No', 'Invoice Date', 'Company Name', 'GST No', 'Type', 'Total Before Tax', 'SGST', 'CGST', 'IGST', 'Total GST', 'Grand Total'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '2', $header);
    $col++;
}
$sheet->getStyle('A2:L2')->applyFromArray($colHeaderStyle);

// Data rows
$rowCount = 3;
$rowCou   = 1;
$total_before_tax = 0;
$total_sgst       = 0;
$total_cgst       = 0;
$total_igst       = 0;
$total_gst        = 0;
$total_grand      = 0;
foreach ($gstr1_report as $row) {
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $row->invoice_number);
    $sheet->setCellValue('C' . $rowCount, date('d-m-Y', strtotime($row->invoice_date)));
    $sheet->setCellValue('D' . $rowCount, $row->company_name);
    $sheet->setCellValue('E' . $rowCount, $row->customer_gst_no);
    $sheet->setCellValue('F' . $rowCount, 'Sale');
    $sheet->setCellValue('G' . $rowCount, $row->total_before_tax);
    $sheet->setCellValue('H' . $rowCount, $row->sgst);
    $sheet->setCellValue('I' . $rowCount, $row->cgst);
    $sheet->setCellValue('J' . $rowCount, $row->igst);
    $sheet->setCellValue('K' . $rowCount, $row->total_gst_amount);
    $sheet->setCellValue('L' . $rowCount, $row->grand_total);
    $sheet->getStyle('A' . $rowCount . ':L' . $rowCount)->applyFromArray($dataStyle);
    $total_before_tax += floatval($row->total_before_tax);
    $total_sgst       += floatval($row->sgst);
    $total_cgst       += floatval($row->cgst);
    $total_igst       += floatval($row->igst);
    $total_gst        += floatval($row->total_gst_amount);
    $total_grand      += floatval($row->grand_total);
    $rowCou++;
    $rowCount++;
}

// Grand Total Row
$totalRowStyle = [
    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F4E78']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e8f442']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$sheet->mergeCells('A' . $rowCount . ':F' . $rowCount);
$sheet->setCellValue('A' . $rowCount, 'Grand Total');
$sheet->setCellValue('G' . $rowCount, $total_before_tax);
$sheet->setCellValue('H' . $rowCount, $total_sgst);
$sheet->setCellValue('I' . $rowCount, $total_cgst);
$sheet->setCellValue('J' . $rowCount, $total_igst);
$sheet->setCellValue('K' . $rowCount, $total_gst);
$sheet->setCellValue('L' . $rowCount, $total_grand);
$sheet->getStyle('A' . $rowCount . ':L' . $rowCount)->applyFromArray($totalRowStyle);
foreach (['G', 'H', 'I', 'J', 'K', 'L'] as $numCol) {
    $sheet->getStyle($numCol . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle($numCol . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Autosize columns
foreach (range('A', 'L') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$file_name = 'GSTR1_Report_' . $from_date . '_' . $to_date . '.xlsx';
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
