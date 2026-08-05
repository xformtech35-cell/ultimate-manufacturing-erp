<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

if (!function_exists('expenditure_report_clean_category')) {
    function expenditure_report_clean_category($stored_category)
    {
        $stored_category = trim((string) $stored_category);
        if (stripos($stored_category, 'Direct - ') === 0) {
            return trim(substr($stored_category, strlen('Direct - ')));
        }
        if (stripos($stored_category, 'Indirect - ') === 0) {
            return trim(substr($stored_category, strlen('Indirect - ')));
        }
        return $stored_category;
    }
}

if (!function_exists('expenditure_report_status_label')) {
    function expenditure_report_status_label($status)
    {
        if ((string) $status === '1') {
            return 'Done';
        }
        if ((string) $status === '2') {
            return 'Pending on Date';
        }
        if ((string) $status === '3') {
            return 'Advance';
        }
        return 'Pending Amount';
    }
}

$rows = isset($result) && is_array($result) ? $result : array();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Expenditure Report');
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(9);

$headerStyle = array(
    'font' => array('bold' => true, 'size' => 14),
    'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER),
    'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => 'E8F4FF')),
    'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN)),
);

$columnHeaderStyle = array(
    'font' => array('bold' => true),
    'alignment' => array(
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ),
    'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => 'F4F7FB')),
    'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN)),
);

$dataCellStyle = array(
    'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN)),
);

$totalRowStyle = array(
    'font' => array('bold' => true),
    'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => 'EAF4FB')),
    'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN)),
);

$sheet->mergeCells('A1:K1');
$sheet->setCellValue('A1', 'EXPENDITURE REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

$periodText = 'Period: ' . (isset($from_date) ? $from_date : '') . ' to ' . (isset($to_date) ? $to_date : '');
$sheet->mergeCells('A2:K2');
$sheet->setCellValue('A2', $periodText);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

$startRow = 4;
$headers = array(
    'A' . $startRow => 'Sr.No.',
    'B' . $startRow => 'Expenditure Category',
    'C' . $startRow => 'Employee Name',
    'D' . $startRow => 'Paid Date',
    'E' . $startRow => 'Month',
    'F' . $startRow => 'GST (%)',
    'G' . $startRow => 'Basic Amount (Rs.)',
    'H' . $startRow => 'Total Amount (Rs.)',
    'I' . $startRow => 'Remark',
    'J' . $startRow => 'Expenditure Doc',
    'K' . $startRow => 'Payment Status',
);

foreach ($headers as $cell => $label) {
    $sheet->setCellValue($cell, $label);
}
$sheet->getStyle('A' . $startRow . ':K' . $startRow)->applyFromArray($columnHeaderStyle);

$rowIndex = $startRow + 1;
$sr = 1;
$basicTotal = 0.0;
$grandTotal = 0.0;

foreach ($rows as $row) {
    $basic = (float) (isset($row->basic_amount) ? $row->basic_amount : 0);
    $total = (float) (isset($row->expense_amount) ? $row->expense_amount : 0);

    $sheet->setCellValue('A' . $rowIndex, $sr);
    $sheet->setCellValue('B' . $rowIndex, expenditure_report_clean_category(isset($row->expense_category) ? $row->expense_category : ''));
    $sheet->setCellValue('C' . $rowIndex, isset($row->employee_name) ? $row->employee_name : '');
    $sheet->setCellValue('D' . $rowIndex, !empty($row->date) ? date('d-m-Y', strtotime($row->date)) : '');
    $sheet->setCellValue('E' . $rowIndex, isset($row->expense_month) ? $row->expense_month : '');
    $sheet->setCellValue('F' . $rowIndex, isset($row->gst_class) ? $row->gst_class : '');
    $sheet->setCellValue('G' . $rowIndex, $basic);
    $sheet->setCellValue('H' . $rowIndex, $total);
    $sheet->setCellValue('I' . $rowIndex, isset($row->expense_note) ? $row->expense_note : '');
    $sheet->setCellValue('J' . $rowIndex, !empty($row->expense_upload) ? 'View' : '-');
    $sheet->setCellValue('K' . $rowIndex, expenditure_report_status_label(isset($row->status) ? $row->status : ''));

    $sheet->getStyle('A' . $rowIndex . ':K' . $rowIndex)->applyFromArray($dataCellStyle);
    $sheet->getStyle('A' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('D' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('G' . $rowIndex . ':H' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('K' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('G' . $rowIndex)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('H' . $rowIndex)->getNumberFormat()->setFormatCode('#,##0.00');

    $basicTotal += $basic;
    $grandTotal += $total;
    $sr++;
    $rowIndex++;
}

if (count($rows) > 0) {
    $sheet->mergeCells('A' . $rowIndex . ':F' . $rowIndex);
    $sheet->setCellValue('A' . $rowIndex, 'Grand Total');
    $sheet->setCellValue('G' . $rowIndex, $basicTotal);
    $sheet->setCellValue('H' . $rowIndex, $grandTotal);
    $sheet->setCellValue('I' . $rowIndex, '');
    $sheet->setCellValue('J' . $rowIndex, '');
    $sheet->setCellValue('K' . $rowIndex, '');

    $sheet->getStyle('A' . $rowIndex . ':K' . $rowIndex)->applyFromArray($totalRowStyle);
    $sheet->getStyle('A' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('G' . $rowIndex . ':H' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('G' . $rowIndex)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('H' . $rowIndex)->getNumberFormat()->setFormatCode('#,##0.00');
}

foreach (range('A', 'K') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$file_name = 'Expenditure_Report_' . date('d-m-Y_H-i-s') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
