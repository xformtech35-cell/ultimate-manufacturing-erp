<?php
// No direct script access
defined('BASEPATH') OR exit('No direct script access allowed');

// Load PhpSpreadsheet via Composer autoload at the top
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
    $sheet->setCellValue('A1', 'No customer statement data found for the selected date range');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Customer_Statement_Report_No_Data.xlsx';
    
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
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Rename the sheet
$sheet->setTitle('Customer Statement');

// Main Header
$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'Customer Statement Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers
$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'Date',
    'C2' => 'Invoice No.',
    'D2' => 'Voucher No.',
    'E2' => 'Invoice Amount',
    'F2' => 'Receipt',
    'G2' => 'Balance (₹)',
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A2:G2')->applyFromArray($columnHeaderStyle);

// Style for invoice rows (white background, standard)
$invoiceRowStyle = [
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Style for payment rows (light background to distinguish)
$paymentRowStyle = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'f2f2f2'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Initialize variables
$grand_total  = 0;
$paid_amount  = 0;
$total_payable = 0;
$rowCount     = 3;
$srNo         = 1;
$company_name = '';

// Populate data - invoice row then its payment rows immediately after
foreach ($result as $row) {
    $row_type = isset($row['row_type']) ? $row['row_type'] : 'invoice';

    if(isset($row['company_name']) && !empty($row['company_name']) && empty($company_name)) {
        $company_name = $row['company_name'];
    }

    if ($row_type === 'invoice') {
        $invoice_amt = isset($row['total']) ? $row['total'] : 0;
        $grand_total += intval($invoice_amt);

        $sheet->setCellValue('A' . $rowCount, $srNo);
        $sheet->setCellValue('B' . $rowCount, isset($row['invoice_date'])   ? $row['invoice_date']   : '');
        $sheet->setCellValue('C' . $rowCount, isset($row['invoice_number']) ? $row['invoice_number'] : '');
        $sheet->setCellValue('D' . $rowCount, '');
        $sheet->setCellValue('E' . $rowCount, $invoice_amt);
        $sheet->setCellValue('F' . $rowCount, '');   // no receipt on invoice row
        $sheet->setCellValue('G' . $rowCount, isset($row['balance'])        ? $row['balance']        : '');

        $sheet->getStyle('A' . $rowCount . ':G' . $rowCount)->applyFromArray($invoiceRowStyle);
        $sheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');

        $srNo++;
    } else {
        // Payment row
        $pmt_amt = isset($row['invocie_pay_amount']) ? $row['invocie_pay_amount'] : 0;
        $paid_amount += intval($pmt_amt);

        $sheet->setCellValue('A' . $rowCount, '');  // no sr no on payment rows
        $sheet->setCellValue('B' . $rowCount, isset($row['invoice_date'])    ? $row['invoice_date']    : '');
        $sheet->setCellValue('C' . $rowCount, isset($row['invoice_number'])  ? $row['invoice_number']  : '');
        $sheet->setCellValue('D' . $rowCount, isset($row['voucher_number'])  ? $row['voucher_number']  : '');
        $sheet->setCellValue('E' . $rowCount, '');  // no invoice amount on payment row
        $sheet->setCellValue('F' . $rowCount, $pmt_amt);
        $sheet->setCellValue('G' . $rowCount, isset($row['balance'])         ? $row['balance']         : '');

        $sheet->getStyle('A' . $rowCount . ':G' . $rowCount)->applyFromArray($paymentRowStyle);
        $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');
    }

    // Common alignment
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('B' . $rowCount)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('C' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('D' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('E' . $rowCount)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('F' . $rowCount)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('G' . $rowCount)->applyFromArray($rightAlignStyle);

    $rowCount++;
}

// Calculate total payable
$total_payable = $grand_total - $paid_amount;

// Grand Total Row — columns: E = Invoice Amount, F = Receipt, G = Balance
$sheet->mergeCells('A' . $rowCount . ':D' . $rowCount);
$sheet->setCellValue('A' . $rowCount, 'Grand Total');
$sheet->getStyle('A' . $rowCount . ':D' . $rowCount)->applyFromArray($totalRowStyle);
$sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('E' . $rowCount, $grand_total);
$sheet->getStyle('E' . $rowCount)->applyFromArray($totalRowStyle);
$sheet->getStyle('E' . $rowCount)->applyFromArray($rightAlignStyle);
$sheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');

$sheet->setCellValue('F' . $rowCount, $paid_amount);
$sheet->getStyle('F' . $rowCount)->applyFromArray($totalRowStyle);
$sheet->getStyle('F' . $rowCount)->applyFromArray($rightAlignStyle);
$sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');

$sheet->setCellValue('G' . $rowCount, $total_payable);
$sheet->getStyle('G' . $rowCount)->applyFromArray($totalRowStyle);
$sheet->getStyle('G' . $rowCount)->applyFromArray($rightAlignStyle);
$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');

$rowCount += 2;

// Summary Statistics Row
$summaryRow = $rowCount;

$sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Transactions: ' . ($srNo - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('E' . $summaryRow . ':G' . $summaryRow);
$sheet->setCellValue('E' . $summaryRow, 'Customer: ' . $company_name);
$sheet->getStyle('E' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Signature Section
$signatureRow = $summaryRow + 3;

$sheet->mergeCells('A' . $signatureRow . ':B' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('F' . $signatureRow . ':G' . $signatureRow);
$sheet->setCellValue('F' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':B' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('F' . $signatureRow . ':G' . $signatureRow);
$sheet->setCellValue('F' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':G' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':G' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated customer statement. For any discrepancies, please contact the accounts department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080');
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'G') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(12);
$sheet->getColumnDimension('C')->setWidth(22);
$sheet->getColumnDimension('D')->setWidth(12);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);

// Generate filename
$company_name_display = !empty($company_name) ? preg_replace('/[^A-Za-z0-9\-]/', '_', $company_name) : 'Customer';
$file_name = 'Customer_Statement_Report_' . $company_name_display . '_' . date('Ymd_His') . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>