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

// Rename the sheet
$sheet->setTitle('Job Order Items');

// Merge cells for main header
$sheet->mergeCells('A1:O1');

// Set main header with style
$sheet->setCellValue('A1', 'Job Order Items');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers
$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'Job Order No',
    'C2' => 'Product Name',
    'D2' => 'Description',
    'E2' => 'QTY',
    'F2' => 'UNIT',
    'G2' => 'TAG NO.',
    'H2' => 'SCOPE',
    'I2' => 'STORES REMARK',
    'J2' => 'REMARK',
    'K2' => 'Company Name',
    'L2' => 'Date',
    'M2' => 'Status',
    'N2' => 'Price',
    'O2' => 'Amount',
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

$sheet->getStyle('A2:O2')->applyFromArray($columnHeaderStyle);

$rowCount = 3;
$rowCou = 1;
$grandTotal = 0;

foreach ($result as $row) {
    $status_label = 'Unknown';
    if ($row->status == 1) {
        $status_label = 'Draft';
    } elseif ($row->status == 2) {
        $status_label = 'Sent';
    } elseif ($row->status == 3) {
        $status_label = 'Viewed';
    } elseif ($row->status == 4) {
        $status_label = 'Approved';
    } elseif ($row->status == 5) {
        $status_label = 'Rejected';
    } elseif ($row->status == 6) {
        $status_label = 'Canceled';
    }

    $costPrice = isset($row->cost_price) ? (float)$row->cost_price : 0;
    $qty = isset($row->quantity) ? (float)$row->quantity : 0;
    $amount = $qty * $costPrice;
    $grandTotal += $amount;

    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->number) ? $row->number : '');
    $sheet->setCellValue('C' . $rowCount, !empty($row->product_description) ? $row->product_description : (!empty($row->product_name) ? $row->product_name : ''));
    $sheet->setCellValue('D' . $rowCount, isset($row->description) ? strip_tags($row->description) : '');
    $sheet->setCellValue('E' . $rowCount, isset($row->quantity) ? $row->quantity : '');
    $sheet->setCellValue('F' . $rowCount, isset($row->unit) ? $row->unit : '');
    $sheet->setCellValue('G' . $rowCount, isset($row->tag_no) ? $row->tag_no : '');
    $sheet->setCellValue('H' . $rowCount, isset($row->scope) ? strip_tags($row->scope) : '');
    $sheet->setCellValue('I' . $rowCount, isset($row->stores_remark) ? $row->stores_remark : '');
    $sheet->setCellValue('J' . $rowCount, isset($row->remark) ? strip_tags($row->remark) : '');
    $sheet->setCellValue('K' . $rowCount, isset($row->company_name) ? $row->company_name : '');
    $sheet->setCellValue('L' . $rowCount, !empty($row->date) && $row->date != '0000-00-00' ? date("d-m-Y", strtotime($row->date)) : '');
    $sheet->setCellValue('M' . $rowCount, $status_label);
    $sheet->setCellValueExplicit('N' . $rowCount, indian_number_format($Price, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('O' . $rowCount, indian_number_format($amount, 2), DataType::TYPE_STRING);

    $sheet->getStyle('A' . $rowCount . ':O' . $rowCount)->applyFromArray($dataCellStyle);

    $sheet->getStyle('E' . $rowCount . ':E' . $rowCount)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('N' . $rowCount . ':O' . $rowCount)->applyFromArray($rightAlignStyle);

    $rowCou++;
    $rowCount++;
}

// Total row after items
$sheet->setCellValue('N' . $rowCount, 'Grand Total');
$sheet->setCellValueExplicit('O' . $rowCount, indian_number_format($grandTotal, 2), DataType::TYPE_STRING);
$sheet->getStyle('N' . $rowCount . ':O' . $rowCount)->applyFromArray($columnHeaderStyle);

foreach (range('A', 'O') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('L')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$file_name = 'JobOrder_Items.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;