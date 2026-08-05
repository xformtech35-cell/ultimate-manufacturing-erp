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
use PhpOffice\PhpSpreadsheet\Cell\DataType;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set default font
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

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

$sheet->setTitle('Purchase Bill HSN Report');
$sheet->mergeCells('A1:M1');
$sheet->setCellValue('A1', 'Purchase Voucher Report (HSN Wise)');
$sheet->getStyle('A1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(35);

$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'Voucher No',
    'C2' => 'Voucher Date',
    'D2' => 'Supplier Name',
    'E2' => 'HSN Code',
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

$sheet->getStyle('A2:M2')->applyFromArray($columnHeaderStyle);
$sheet->getRowDimension(2)->setRowHeight(30);

$rowCount = 3;
$rowCou = 1;
$t_before_tax = 0;
$t_sgst = 0;
$t_cgst = 0;
$t_igst = 0;
$t_gst = 0;
$t_total = 0;
$t_balance = 0;

foreach ($result as $row) {
    $gst_type = '';
    $sgst = (float) $row->sgst;
    $cgst = (float) $row->cgst;
    $igst = (float) $row->igst;

    if ($row->gst_type != 'I') {
        $gst_type = 'SGST';
    } else {
        $gst_type = 'IGST';
        $sgst = 0;
        $cgst = 0;
    }

    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $row->number);
    $sheet->setCellValue('C' . $rowCount, date('d-m-Y', strtotime($row->date)));
    $sheet->setCellValue('D' . $rowCount, $row->supplier_name);
    $sheet->setCellValue('E' . $rowCount, !empty($row->hsn_code) ? $row->hsn_code : '');
    $sheet->setCellValue('F' . $rowCount, $gst_type);
    $sheet->setCellValueExplicit('G' . $rowCount, indian_number_format((float)$row->total_before_tax, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('H' . $rowCount, indian_number_format((float)$sgst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('I' . $rowCount, indian_number_format((float)$cgst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('J' . $rowCount, indian_number_format((float)$igst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('K' . $rowCount, indian_number_format((float)$row->total_gst_amount, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('L' . $rowCount, indian_number_format((float)$row->total, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('M' . $rowCount, indian_number_format((float)$row->balance, 2), DataType::TYPE_STRING);

    $t_before_tax += (float)$row->total_before_tax;
    $t_sgst += $sgst;
    $t_cgst += $cgst;
    $t_igst += $igst;
    $t_gst += (float)$row->total_gst_amount;
    $t_total += (float)$row->total;
    $t_balance += (float)$row->balance;

    $sheet->getStyle('A' . $rowCount . ':M' . $rowCount)->applyFromArray($dataCellStyle);

    $amountColumns = ['G', 'H', 'I', 'J', 'K', 'L', 'M'];
    foreach ($amountColumns as $col) {
        $sheet->getStyle($col . $rowCount)->applyFromArray($rightAlignStyle);
    }

    $rowCou++;
    $rowCount++;
}

$totalRow = $rowCount;
$sheet->setCellValue('F' . $totalRow, 'Total:');

$totalCells = ['F' . $totalRow, 'G' . $totalRow, 'H' . $totalRow, 'I' . $totalRow, 'J' . $totalRow, 'K' . $totalRow, 'L' . $totalRow, 'M' . $totalRow];
foreach ($totalCells as $cell) {
    $sheet->getStyle($cell)->applyFromArray($totalRowStyle);
}

$formulas = [
    'G' . $totalRow => indian_number_format($t_before_tax, 2),
    'H' . $totalRow => indian_number_format($t_sgst, 2),
    'I' . $totalRow => indian_number_format($t_cgst, 2),
    'J' . $totalRow => indian_number_format($t_igst, 2),
    'K' . $totalRow => indian_number_format($t_gst, 2),
    'L' . $totalRow => indian_number_format($t_total, 2),
    'M' . $totalRow => indian_number_format($t_balance, 2),
];

foreach ($formulas as $cell => $value) {
    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);
}

$lastDataRow = $rowCount - 1;
foreach (range('A', 'M') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$amountColumns = ['G', 'H', 'I', 'J', 'K', 'L', 'M'];
foreach ($amountColumns as $col) {
    $sheet->getStyle($col . '3:' . $col . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle($col . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

$from_date_display = isset($from_date) ? $from_date : date('d-m-Y');
$to_date_display = isset($to_date) ? $to_date : date('d-m-Y');
$file_name = 'Purchase_Bill_HSN_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>