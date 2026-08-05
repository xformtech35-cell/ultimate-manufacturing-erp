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

// Define currency and number format patterns
$currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
$numberFormat = '#,#0.##;[Red]-#,#0.##';

// Rename the sheet
$sheet->setTitle('Excel report');

// Define style arrays - Standardized colors and fonts
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 22,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '31a3dd'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$periodHeadingStyle = [
    'font' => [
        'bold' => true,
        'italic' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '31a3dd'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

$columnHeaderStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '31a3dd'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$sectionHeaderStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '31a3dd'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$totalRowStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '1F4E78'],
        'size' => 14,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'e8f442'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

$bodyStyle = [
    'font' => [
        'color' => ['rgb' => '000000'],
        'size' => 11,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
    ],
    'borders' => [
        'right' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Apply standardized styles to header rows
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
$sheet->getStyle('A2:F2')->applyFromArray($periodHeadingStyle);
$sheet->getStyle('A3:F3')->applyFromArray($periodHeadingStyle);
$sheet->getStyle('A4:F4')->applyFromArray($periodHeadingStyle);
$sheet->getStyle('A5:F5')->applyFromArray($periodHeadingStyle);
$sheet->getStyle('A6:F6')->applyFromArray($columnHeaderStyle);
$sheet->getStyle('A7:F7')->applyFromArray($columnHeaderStyle);
$sheet->getStyle('A8:D8')->applyFromArray($sectionHeaderStyle);
$sheet->getStyle('A17:C17')->applyFromArray($sectionHeaderStyle);
$sheet->getStyle('A10:C10')->applyFromArray($sectionHeaderStyle);
$sheet->getStyle('D10:F10')->applyFromArray($columnHeaderStyle);
$sheet->getStyle('F6:F8')->applyFromArray($columnHeaderStyle);

// Set active sheet and merge cells for header
$spreadsheet->setActiveSheetIndex(0);
$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:F2');
$sheet->mergeCells('A3:F3');
$sheet->mergeCells('A4:F4');
$sheet->mergeCells('A5:F5');
$sheet->mergeCells('A6:A7');
$sheet->mergeCells('B6:B7');
$sheet->mergeCells('C6:C7');
$sheet->mergeCells('D6:D7');
$sheet->mergeCells('E6:E7');
$sheet->mergeCells('F6:F7');

// Set header values and formatting
$sheet->setCellValue('A1', 'Profit Loss Report');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:F1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 22,
        'name' => 'Arial',
    ]
]);

$sheet->setCellValue('A2', $settings['company_name']);
$sheet->getStyle('A2:D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A3', 'CIN:' . $settings['cin']);
$sheet->getStyle('A3:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A4', 'Tentative Profit & Loss A/c');
$sheet->getStyle('A4:D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A5', $from_date . " " . 'to' . " " . $to_date);
$sheet->getStyle('A5:D5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A6', 'Particulars');
$sheet->getStyle('A6:A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('C6', $from_date . " " . 'to' . " " . $to_date);
$sheet->getStyle('C6:C6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('D6', 'Particulars');
$sheet->getStyle('D6:D6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('F6', $from_date . " " . 'to' . " " . $to_date);
$sheet->getStyle('F6:F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Set section headers
$sheet->setCellValue('A8', 'Purchase Accounts');
$sheet->setCellValue('C8', $purchase_report['po_total']);
$sheet->setCellValue('D8', 'Sales Accounts');
$sheet->setCellValue('F8', $purchase_report['invoice_total']);

$sheet->setCellValue('A10', 'Direct Expense');
$sheet->setCellValue('C10', '');

$rowCount = 9;

// Process direct expenses
foreach ($purchase_report['expense_total_data'] as $row) {
    if (strpos($row->expense_category, 'Direct') !== false) {
        $sheet->setCellValue('A' . $rowCount, $row->expense_category);
        $sheet->setCellValue('B' . $rowCount, $row->expense_total);
    }
    $rowCount++;
}

// Set indirect expense section
$sheet->setCellValue('A17', 'Indirect Expense');
$sheet->setCellValue('B17', '');

// Process indirect expenses
foreach ($purchase_report['expense_total_data'] as $row) {
    if (strpos($row->expense_category, 'Direct') !== false) {
        // Skip direct expenses
    } else {
        $sheet->setCellValue('A' . $rowCount, $row->expense_category);
        $sheet->setCellValue('B' . $rowCount, $row->expense_total);
        $rowCount++;
    }
}

// Set closing stock and scrap values
$sheet->setCellValue('D10', 'Closing Stock');
$sheet->setCellValue('F10', $stock_in_hand);

$sheet->setCellValue('D11', 'Stock in Hand');
$sheet->setCellValue('E11', $stock_in_hand);

$sheet->setCellValue('D12', 'Scrap');
$sheet->setCellValue('E12', $scrap);




// Set formulas for totals with proper styling
$sheet->setCellValue('F13', '=SUM(F8:F10)');
$sheet->getStyle('F13')->applyFromArray($totalRowStyle);

$sheet->setCellValue('C10', '=SUM(B11:B14)');
$sheet->getStyle('C10')->applyFromArray($totalRowStyle);

$sheet->setCellValue('C17', '=SUM(B21:B28)');
$sheet->getStyle('C17')->applyFromArray($totalRowStyle);

// Auto-size columns
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);

// Generate filename
$file_name = ' Profit Loss Report ' . $from_date . '' . $to_date . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and save
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
