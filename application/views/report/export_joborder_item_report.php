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
$sheet->setTitle('Job Order Item Report');

// Merge cells for main header
$sheet->mergeCells('A1:F1');

// Set main header with style
$sheet->setCellValue('A1', 'Job Order Item Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers
$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'Job Order No',
    'C2' => 'Date',
    'D2' => 'Company Name',
    'E2' => 'Total Cost',
    'F2' => 'Status',
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

$sheet->getStyle('A2:F2')->applyFromArray($columnHeaderStyle);

$rowCount = 3;
$rowCou = 1;

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

    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $row->number);
    $sheet->setCellValue('C' . $rowCount, !empty($row->date) && $row->date != '0000-00-00' ? date("d-m-Y", strtotime($row->date)) : '');
    $sheet->setCellValue('D' . $rowCount, isset($row->company_name) ? $row->company_name : '');
    $sheet->setCellValue('E' . $rowCount, isset($row->item_cost) && $row->item_cost !== null ? $row->item_cost : 0);
    $sheet->setCellValue('F' . $rowCount, $status_label);

    $sheet->getStyle('A' . $rowCount . ':F' . $rowCount)->applyFromArray($dataCellStyle);

    $sheet->getStyle('E' . $rowCount)->applyFromArray($rightAlignStyle);

    $rowCou++;
    $rowCount++;
}

$totalRow = $rowCount;
$sheet->setCellValue('D' . $totalRow, 'Total:');

$sheet->setCellValue('E' . $totalRow, "=SUM(E3:E" . ($rowCount - 1) . ")");
$sheet->getStyle('E' . $totalRow)->applyFromArray($totalRowStyle);

foreach (range('A', 'F') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$sheet->getStyle('C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->getStyle('E3:E' . ($rowCount - 1))->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');

$from_date_display = isset($from_date) ? $from_date : date('d-m-Y');
$to_date_display = isset($to_date) ? $to_date : date('d-m-Y');
$file_name = 'JobOrder_Item_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;