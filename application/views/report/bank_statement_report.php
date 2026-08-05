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

// Check if data exists
if(!isset($result) || empty($result)) {
    // Create a simple spreadsheet with error message
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No bank statement data found for the selected date range');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Bank_Statement_Report_No_Data.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $file_name . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

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
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
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
        'size' => 14,
        'color' => ['rgb' => '1F4E78'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'e8f442'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Rename the sheet
$sheet->setTitle('Bank Statement');

// Main Header
$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A1', 'BANK STATEMENT REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Date Range
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:H2');
    $sheet->setCellValue('A2', 'Period: ' . $from_date . ' to ' . $to_date);
    $sheet->getStyle('A2')->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'font' => [
            'italic' => true,
            'bold' => true,
            'size' => 12,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '31a3dd'],
        ],
    ]);
    
    // Empty row
    $sheet->setCellValue('A3', '');
    $startRow = 4;
} else {
    $startRow = 2;
}

// Set column headers
$headers = [
    'A' . $startRow => 'Sr.No.',
    'B' . $startRow => 'Date',
    'C' . $startRow => 'Bank Name',
    'D' . $startRow => 'Transaction Details',
    'E' . $startRow => 'Withdrawal (₹)',
    'F' . $startRow => 'Deposit (₹)',
    'G' . $startRow => 'Balance (₹)',
    'H' . $startRow => 'Description'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':H' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_withdrawal = 0;
$total_deposit = 0;
$total_balance = 0;

foreach ($result as $row) {
    $withdrawal = isset($row->withdrawal_amount) ? floatval($row->withdrawal_amount) : 0;
    $deposit = isset($row->deposite_amount) ? floatval($row->deposite_amount) : 0;
    $balance = isset($row->balance_amount) ? floatval($row->balance_amount) : 0;
    
    $total_withdrawal += $withdrawal;
    $total_deposit += $deposit;
    $total_balance = $balance; // Last balance
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->transaction_date) ? date("d-m-Y", strtotime($row->transaction_date)) : '');
    $sheet->setCellValue('C' . $rowCount, isset($row->bank_transaction_name) ? $row->bank_transaction_name : '');
    $sheet->setCellValue('D' . $rowCount, isset($row->transaction_detail) ? $row->transaction_detail : '');
    $sheet->setCellValue('E' . $rowCount, $withdrawal);
    $sheet->setCellValue('F' . $rowCount, $deposit);
    $sheet->setCellValue('G' . $rowCount, $balance);
    $sheet->setCellValue('H' . $rowCount, isset($row->description) ? $row->description : '');
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':H' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('B' . $rowCount)->applyFromArray($centerAlignStyle); // Date centered
    $sheet->getStyle('E' . $rowCount . ':G' . $rowCount)->applyFromArray($rightAlignStyle); // Amounts right-aligned
    
    // Format amount columns
    $sheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, '');
    $sheet->setCellValue('C' . $totalRow, '');
    $sheet->setCellValue('D' . $totalRow, 'TOTALS:');
    $sheet->setCellValue('E' . $totalRow, $total_withdrawal);
    $sheet->setCellValue('F' . $totalRow, $total_deposit);
    $sheet->setCellValue('G' . $totalRow, $total_balance);
    $sheet->setCellValue('H' . $totalRow, '');
    
    // Style for total row
    $sheet->getStyle('A' . $totalRow . ':H' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('D' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('E' . $totalRow . ':G' . $totalRow)->applyFromArray($rightAlignStyle);
    
    // Format total row numbers
    $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('F' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('G' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Transactions: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('E' . $summaryRow . ':H' . $summaryRow);
$sheet->setCellValue('E' . $summaryRow, 'Net Flow: ₹ ' . number_format($total_deposit - $total_withdrawal, 2));
$sheet->getStyle('E' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Bank Summary
$bankSummaryRow = $summaryRow + 2;

$sheet->mergeCells('A' . $bankSummaryRow . ':H' . $bankSummaryRow);
$sheet->setCellValue('A' . $bankSummaryRow, 'BANK SUMMARY');
$sheet->getStyle('A' . $bankSummaryRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$bankSummaryRow++;

$bankSummaryData = [
    ['Total Withdrawals', $total_withdrawal],
    ['Total Deposits', $total_deposit],
    ['Closing Balance', $total_balance],
    ['Total Debit Transactions', $total_withdrawal > 0 ? 'Yes' : 'No'],
    ['Total Credit Transactions', $total_deposit > 0 ? 'Yes' : 'No']
];

foreach ($bankSummaryData as $index => $data) {
    $sheet->setCellValue('A' . ($bankSummaryRow + $index), $data[0]);
    $sheet->setCellValue('G' . ($bankSummaryRow + $index), $data[1]);
    $sheet->getStyle('A' . ($bankSummaryRow + $index))->applyFromArray(['font' => ['bold' => true]]);
    $sheet->getStyle('G' . ($bankSummaryRow + $index))->applyFromArray($rightAlignStyle);
    if(is_numeric($data[1])) {
        $sheet->getStyle('G' . ($bankSummaryRow + $index))->getNumberFormat()->setFormatCode('#,##0.00');
    }
}

// Signature Section
$signatureRow = $bankSummaryRow + 7;

$sheet->mergeCells('A' . $signatureRow . ':D' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('E' . $signatureRow . ':H' . $signatureRow);
$sheet->setCellValue('E' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':D' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('E' . $signatureRow . ':H' . $signatureRow);
$sheet->setCellValue('E' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':H' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':H' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated bank statement. For any discrepancies, please contact the finance department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'H') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(12);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(25);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(30);

// Generate filename
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Bank_Statement_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>