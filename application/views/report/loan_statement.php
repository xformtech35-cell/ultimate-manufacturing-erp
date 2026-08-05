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
if(!isset($loan_report) || empty($loan_report)) {
    // Create a simple spreadsheet with error message
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No loan data found for the selected date range');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Loan_Report_No_Data.xlsx';
    
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
$sheet->setTitle('Loan Report');

// Main Header
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A1', 'LOAN REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Date Range
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:E2');
    $sheet->setCellValue('A2', 'Period: ' . $from_date . ' to ' . $to_date);
    $sheet->getStyle('A2')->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'font' => ['italic' => true, 'size' => 12, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
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
    'C' . $startRow => 'Interest Rate (%)',
    'D' . $startRow => 'Amount (₹)',
    'E' . $startRow => 'Account No.'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':E' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_amount = 0;
$total_interest = 0;
$interest_count = 0;

// Account-wise tracking
$account_totals = [];

foreach ($loan_report as $row) {
    $amount = isset($row->current_balance) ? floatval($row->current_balance) : 0;
    $interest_rate = isset($row->interest_rate) ? floatval($row->interest_rate) : 0;
    $account_no = isset($row->acc_number) ? $row->acc_number : 'N/A';
    
    $total_amount += $amount;
    if($interest_rate > 0) {
        $total_interest += $interest_rate;
        $interest_count++;
    }
    
    // Track account totals
    if(!isset($account_totals[$account_no])) {
        $account_totals[$account_no] = [
            'amount' => 0,
            'count' => 0,
            'interest' => 0
        ];
    }
    $account_totals[$account_no]['amount'] += $amount;
    $account_totals[$account_no]['count']++;
    $account_totals[$account_no]['interest'] += $interest_rate;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->loan_date) ? date("d-m-Y", strtotime($row->loan_date)) : '');
    $sheet->setCellValue('C' . $rowCount, $interest_rate);
    $sheet->setCellValue('D' . $rowCount, $amount);
    $sheet->setCellValue('E' . $rowCount, $account_no);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':E' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('B' . $rowCount)->applyFromArray($centerAlignStyle); // Date centered
    $sheet->getStyle('C' . $rowCount)->applyFromArray($rightAlignStyle); // Interest rate right-aligned
    $sheet->getStyle('D' . $rowCount)->applyFromArray($rightAlignStyle); // Amount right-aligned
    $sheet->getStyle('E' . $rowCount)->applyFromArray($centerAlignStyle); // Account No. centered
    
    // Format columns
    $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('0.00"%');
    $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

if($rowCou > 1) {
    $avg_interest = ($interest_count > 0) ? $total_interest / $interest_count : 0;
    
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, '');
    $sheet->setCellValue('C' . $totalRow, number_format($avg_interest, 2) . '%');
    $sheet->setCellValue('D' . $totalRow, $total_amount);
    $sheet->setCellValue('E' . $totalRow, 'TOTAL:');
    
    // Style for total row
    $sheet->getStyle('A' . $totalRow . ':E' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('C' . $totalRow . ':D' . $totalRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    
    // Format total row numbers
    $sheet->getStyle('C' . $totalRow)->getNumberFormat()->setFormatCode('0.00"%');
    $sheet->getStyle('D' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':B' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Loans: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('C' . $summaryRow . ':E' . $summaryRow);
$sheet->setCellValue('C' . $summaryRow, 'Total Loan Amount: ₹ ' . number_format($total_amount, 2));
$sheet->getStyle('C' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Account-wise Summary
$accountRow = $summaryRow + 2;

$sheet->mergeCells('A' . $accountRow . ':E' . $accountRow);
$sheet->setCellValue('A' . $accountRow, 'ACCOUNT-WISE LOAN SUMMARY');
$sheet->getStyle('A' . $accountRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$accountRow++;

$sheet->setCellValue('A' . $accountRow, 'Account No.');
$sheet->setCellValue('B' . $accountRow, 'No. of Loans');
$sheet->setCellValue('C' . $accountRow, 'Total Amount (₹)');
$sheet->setCellValue('D' . $accountRow, 'Avg Interest (%)');
$sheet->setCellValue('E' . $accountRow, 'Percentage (%)');
$sheet->getStyle('A' . $accountRow . ':E' . $accountRow)->applyFromArray(['font' => ['bold' => true]]);
$accountRow++;

foreach ($account_totals as $account_no => $data) {
    $avg_interest = ($data['count'] > 0) ? $data['interest'] / $data['count'] : 0;
    $percentage = ($total_amount > 0) ? ($data['amount'] / $total_amount) * 100 : 0;
    
    $sheet->setCellValue('A' . $accountRow, $account_no);
    $sheet->setCellValue('B' . $accountRow, $data['count']);
    $sheet->setCellValue('C' . $accountRow, $data['amount']);
    $sheet->setCellValue('D' . $accountRow, $avg_interest);
    $sheet->setCellValue('E' . $accountRow, number_format($percentage, 2) . '%');
    
    $sheet->getStyle('B' . $accountRow . ':D' . $accountRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('B' . $accountRow)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('C' . $accountRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('D' . $accountRow)->getNumberFormat()->setFormatCode('0.00"%');
    
    $accountRow++;
}

// Interest Rate Analysis
$interestRow = $accountRow + 2;

$sheet->mergeCells('A' . $interestRow . ':E' . $interestRow);
$sheet->setCellValue('A' . $interestRow, 'INTEREST RATE ANALYSIS');
$sheet->getStyle('A' . $interestRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$interestRow++;

$sheet->setCellValue('A' . $interestRow, 'Interest Rate Range');
$sheet->setCellValue('B' . $interestRow, 'No. of Loans');
$sheet->setCellValue('C' . $interestRow, 'Total Amount (₹)');
$sheet->setCellValue('D' . $interestRow, 'Percentage (%)');
$sheet->getStyle('A' . $interestRow . ':D' . $interestRow)->applyFromArray(['font' => ['bold' => true]]);
$interestRow++;

// Categorize loans by interest rate
$interest_ranges = [
    '0-5%' => ['min' => 0, 'max' => 5, 'count' => 0, 'amount' => 0],
    '5-10%' => ['min' => 5, 'max' => 10, 'count' => 0, 'amount' => 0],
    '10-15%' => ['min' => 10, 'max' => 15, 'count' => 0, 'amount' => 0],
    '15%+' => ['min' => 15, 'max' => PHP_FLOAT_MAX, 'count' => 0, 'amount' => 0]
];

foreach ($loan_report as $row) {
    $interest = isset($row->interest_rate) ? floatval($row->interest_rate) : 0;
    $amount = isset($row->current_balance) ? floatval($row->current_balance) : 0;
    
    if ($interest <= 5) {
        $interest_ranges['0-5%']['count']++;
        $interest_ranges['0-5%']['amount'] += $amount;
    } elseif ($interest <= 10) {
        $interest_ranges['5-10%']['count']++;
        $interest_ranges['5-10%']['amount'] += $amount;
    } elseif ($interest <= 15) {
        $interest_ranges['10-15%']['count']++;
        $interest_ranges['10-15%']['amount'] += $amount;
    } else {
        $interest_ranges['15%+']['count']++;
        $interest_ranges['15%+']['amount'] += $amount;
    }
}

foreach ($interest_ranges as $range => $data) {
    if ($data['count'] > 0) {
        $percentage = ($total_amount > 0) ? ($data['amount'] / $total_amount) * 100 : 0;
        
        $sheet->setCellValue('A' . $interestRow, $range);
        $sheet->setCellValue('B' . $interestRow, $data['count']);
        $sheet->setCellValue('C' . $interestRow, $data['amount']);
        $sheet->setCellValue('D' . $interestRow, number_format($percentage, 2) . '%');
        
        $sheet->getStyle('B' . $interestRow . ':C' . $interestRow)->applyFromArray($rightAlignStyle);
        $sheet->getStyle('B' . $interestRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C' . $interestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        
        $interestRow++;
    }
}

// Top Loans by Amount
$topLoansRow = $interestRow + 2;

$sheet->mergeCells('A' . $topLoansRow . ':E' . $topLoansRow);
$sheet->setCellValue('A' . $topLoansRow, 'TOP 5 LOANS BY AMOUNT');
$sheet->getStyle('A' . $topLoansRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$topLoansRow++;

$sheet->setCellValue('A' . $topLoansRow, 'Rank');
$sheet->setCellValue('B' . $topLoansRow, 'Date');
$sheet->setCellValue('C' . $topLoansRow, 'Interest Rate');
$sheet->setCellValue('D' . $topLoansRow, 'Amount (₹)');
$sheet->setCellValue('E' . $topLoansRow, 'Account No.');
$sheet->getStyle('A' . $topLoansRow . ':E' . $topLoansRow)->applyFromArray(['font' => ['bold' => true]]);
$topLoansRow++;

// Sort loans by amount
$sorted_loans = $loan_report;
usort($sorted_loans, function($a, $b) {
    return floatval($b->current_balance) <=> floatval($a->current_balance);
});

$rank = 1;
$top_loans = array_slice($sorted_loans, 0, 5);

foreach ($top_loans as $row) {
    $sheet->setCellValue('A' . $topLoansRow, $rank);
    $sheet->setCellValue('B' . $topLoansRow, isset($row->loan_date) ? date("d-m-Y", strtotime($row->loan_date)) : '');
    $sheet->setCellValue('C' . $topLoansRow, isset($row->interest_rate) ? $row->interest_rate : 0);
    $sheet->setCellValue('D' . $topLoansRow, isset($row->current_balance) ? $row->current_balance : 0);
    $sheet->setCellValue('E' . $topLoansRow, isset($row->acc_number) ? $row->acc_number : 'N/A');
    
    $sheet->getStyle('A' . $topLoansRow)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('B' . $topLoansRow)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('C' . $topLoansRow . ':D' . $topLoansRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $topLoansRow)->getNumberFormat()->setFormatCode('0.00"%');
    $sheet->getStyle('D' . $topLoansRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rank++;
    $topLoansRow++;
}

// Signature Section
$signatureRow = $topLoansRow + 3;

$sheet->mergeCells('A' . $signatureRow . ':B' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('C' . $signatureRow . ':E' . $signatureRow);
$sheet->setCellValue('C' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':B' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('C' . $signatureRow . ':E' . $signatureRow);
$sheet->setCellValue('C' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':E' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':E' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated loan report. For any discrepancies, please contact the finance department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'E') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(12);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(18);
$sheet->getColumnDimension('E')->setWidth(18);

// Generate filename
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Loan_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>