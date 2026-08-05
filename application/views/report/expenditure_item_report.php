<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

$is_individual_category = isset($is_individual_category) ? (bool) $is_individual_category : false;
$employee_name = isset($employee_name) ? trim((string) $employee_name) : '';

if (!function_exists('report_export_clean_expense_category')) {
    function report_export_clean_expense_category($stored_category)
    {
        $stored_category = trim((string) $stored_category);

        if (stripos($stored_category, 'Direct - ') === 0) {
            $stored_category = trim(substr($stored_category, strlen('Direct - ')));
        } elseif (stripos($stored_category, 'Indirect - ') === 0) {
            $stored_category = trim(substr($stored_category, strlen('Indirect - ')));
        }

        return preg_replace('/^(Individual|Corporate)\s*-\s*/i', '', $stored_category);
    }
}

if (!function_exists('getExpenditureStatus')) {
    function getExpenditureStatus($status)
    {
        switch ((string) $status) {
            case '1':
                return 'Done';
            case '2':
                return 'Pending on Date';
            case '3':
                return 'Advance';
            default:
                return 'Pending Amount';
        }
    }
}

if (!isset($result) || empty($result)) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No expenditure item data found for the selected criteria');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Expenditure_Item_Report_No_Data.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'],
        'size' => 16,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'e8f442'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$columnHeaderStyle = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$dataCellStyle = [
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$rightAlignStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$centerAlignStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

$totalRowStyle = [
    'font' => [
        'bold' => true,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'e8f442'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$lastColumn = $is_individual_category ? 'I' : 'H';
$sheet->setTitle('Expenditure Item');
$sheet->mergeCells('A1:' . $lastColumn . '1');
$sheet->setCellValue('A1', 'EXPENDITURE ITEM REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

if (isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:' . $lastColumn . '2');
    $filter_text = 'Period: ' . $from_date . ' to ' . $to_date;

    if (isset($expense_category) && $expense_category !== '') {
        $filter_text .= ' | Category: ' . report_export_clean_expense_category($expense_category);
    }
    if ($is_individual_category && $employee_name !== '') {
        $filter_text .= ' | Employee: ' . $employee_name;
    }

    $sheet->setCellValue('A2', $filter_text);
    $sheet->getStyle('A2')->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'font' => ['italic' => true, 'size' => 11],
    ]);
    $sheet->setCellValue('A3', '');
    $startRow = 4;
} else {
    $startRow = 2;
}

$headers = [
    'A' . $startRow => 'Sr.No.',
    'B' . $startRow => 'Category Name',
];

if ($is_individual_category) {
    $headers['C' . $startRow] = 'Employee Name';
    $headers['D' . $startRow] = 'Date';
    $headers['E' . $startRow] = 'Month';
    $headers['F' . $startRow] = 'Amount';
    $headers['G' . $startRow] = 'GST %';
    $headers['H' . $startRow] = 'GST Amount';
    $headers['I' . $startRow] = 'Total Amount';
} else {
    $headers['C' . $startRow] = 'Date';
    $headers['D' . $startRow] = 'Month';
    $headers['E' . $startRow] = 'Amount';
    $headers['F' . $startRow] = 'GST %';
    $headers['G' . $startRow] = 'GST Amount';
    $headers['H' . $startRow] = 'Total Amount';
}

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

$sheet->getStyle('A' . $startRow . ':' . $lastColumn . $startRow)->applyFromArray($columnHeaderStyle);

$rowCount = $startRow + 1;
$rowCou = 1;
$total_amount = 0;

foreach ($result as $row) {
    $amount = isset($row->expense_amount) ? (float) $row->expense_amount : 0;
    $gst_amount = isset($row->gst_amount) ? (float) $row->gst_amount : 0;
    $total_amount_row = $amount + $gst_amount;
    $total_amount += $total_amount_row;

    $category = isset($row->expense_category) ? $row->expense_category : 'Uncategorized';
    $display_category = report_export_clean_expense_category($category);
    $date = isset($row->date) ? $row->date : '';
    $formatted_date = !empty($date) ? date('d-m-Y', strtotime($date)) : '';
    $month = isset($row->expense_month) ? trim((string) $row->expense_month) : '';
    if ($month === '' && !empty($date)) {
        $month = date('F', strtotime($date));
    }

    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $display_category);

    if ($is_individual_category) {
        $sheet->setCellValue('C' . $rowCount, isset($row->employee_name) ? $row->employee_name : '');
        $sheet->setCellValue('D' . $rowCount, $formatted_date);
        $sheet->setCellValue('E' . $rowCount, $month);
        $sheet->setCellValue('F' . $rowCount, $amount);
        $sheet->setCellValue('G' . $rowCount, isset($row->gst_percentage) ? $row->gst_percentage : 0);
        $sheet->setCellValue('H' . $rowCount, $gst_amount);
        $sheet->setCellValue('I' . $rowCount, $total_amount_row);
    } else {
        $sheet->setCellValue('C' . $rowCount, $formatted_date);
        $sheet->setCellValue('D' . $rowCount, $month);
        $sheet->setCellValue('E' . $rowCount, $amount);
        $sheet->setCellValue('F' . $rowCount, isset($row->gst_percentage) ? $row->gst_percentage : 0);
        $sheet->setCellValue('G' . $rowCount, $gst_amount);
        $sheet->setCellValue('H' . $rowCount, $total_amount_row);
    }

    $sheet->getStyle('A' . $rowCount . ':' . $lastColumn . $rowCount)->applyFromArray($dataCellStyle);
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle);

    if ($is_individual_category) {
        $sheet->getStyle('D' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('E' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('F' . $rowCount)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('G' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('H' . $rowCount)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('I' . $rowCount)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('I' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    } else {
        $sheet->getStyle('C' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('D' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('E' . $rowCount)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('F' . $rowCount)->applyFromArray($centerAlignStyle);
        $sheet->getStyle('G' . $rowCount)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('H' . $rowCount)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    $rowCou++;
    $rowCount++;
}

$totalRow = $rowCount;

if ($rowCou > 1) {
    $sheet->getStyle('A' . $totalRow . ':' . $lastColumn . $totalRow)->applyFromArray($totalRowStyle);

    if ($is_individual_category) {
        $sheet->setCellValue('E' . $totalRow, 'TOTAL EXPENDITURE');
        $sheet->setCellValue('I' . $totalRow, $total_amount);
        $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I' . $totalRow)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('I' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
    } else {
        $sheet->setCellValue('D' . $totalRow, 'TOTAL EXPENDITURE');
        $sheet->setCellValue('H' . $totalRow, $total_amount);
        $sheet->getStyle('D' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H' . $totalRow)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('H' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
    }
}

$summaryRow = $totalRow + 2;
$sheet->mergeCells('A' . $summaryRow . ':B' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Transactions: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$signatureRow = $summaryRow + 3;
$sheet->mergeCells('A' . $signatureRow . ':' . $lastColumn . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':' . $lastColumn . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated expenditure item report. For any discrepancies, please contact the finance department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080');
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

foreach (range('A', $lastColumn) as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(24);

if ($is_individual_category) {
    $sheet->getColumnDimension('C')->setWidth(24);
    $sheet->getColumnDimension('D')->setWidth(12);
    $sheet->getColumnDimension('E')->setWidth(12);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(8);
    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->getColumnDimension('I')->setWidth(15);
} else {
    $sheet->getColumnDimension('C')->setWidth(12);
    $sheet->getColumnDimension('D')->setWidth(12);
    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->getColumnDimension('F')->setWidth(8);
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->getColumnDimension('H')->setWidth(15);
}

$expense_category_display = isset($expense_category) && !empty($expense_category)
    ? preg_replace('/[^A-Za-z0-9\-]/', '_', report_export_clean_expense_category($expense_category))
    : 'All';
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Expenditure_Item_Report_' . $expense_category_display . '_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
