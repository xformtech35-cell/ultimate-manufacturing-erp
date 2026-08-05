<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

if (!isset($result) || empty($result)) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No supplier statement data found for the selected date range');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);

    $file_name = 'Supplier_Statement_Report_No_Data.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $file_name . '"');
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(11);

$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 22],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '31a3dd']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$columnHeaderStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '31a3dd']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$rightAlignStyle = [
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$centerAlignStyle = [
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
];

$invoiceRowStyle = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$paymentRowStyle = [
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f2f2f2']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$totalRowStyle = [
    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F4E78']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e8f442']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$sheet->setTitle('Supplier Statement');

// Main Header (A–G)
$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'Supplier Statement Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Column headers
$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'Date',
    'C2' => 'Bill No.',
    'D2' => 'Voucher No.',
    'E2' => 'Bill Amount',
    'F2' => 'Payment Out',
    'G2' => 'Balance (₹)',
];
foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}
$sheet->getStyle('A2:G2')->applyFromArray($columnHeaderStyle);

// Data
$grand_total   = 0;
$paid_amount   = 0;
$total_payable = 0;
$rowCount      = 3;
$srNo          = 1;
$supplier_name = '';

foreach ($result as $row) {
    $row_type = isset($row['row_type']) ? $row['row_type'] : 'bill';

    if (!empty($row['company_name']) && empty($supplier_name)) {
        $supplier_name = $row['company_name'];
    }

    if ($row_type === 'bill') {
        $bill_amt = isset($row['total']) ? $row['total'] : 0;
        $grand_total += intval($bill_amt);

        $sheet->setCellValue('A' . $rowCount, $srNo);
        $sheet->setCellValue('B' . $rowCount, isset($row['bill_date'])      ? $row['bill_date']      : '');
        $sheet->setCellValue('C' . $rowCount, isset($row['bill_number'])    ? $row['bill_number']    : '');
        $sheet->setCellValue('D' . $rowCount, '');
        $sheet->setCellValue('E' . $rowCount, $bill_amt);
        $sheet->setCellValue('F' . $rowCount, '');
        $sheet->setCellValue('G' . $rowCount, isset($row['balance']) ? $row['balance'] : '');

        $sheet->getStyle('A' . $rowCount . ':G' . $rowCount)->applyFromArray($invoiceRowStyle);
        $sheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');

        $srNo++;
    } else {
        $pmt_amt = isset($row['pay_amount']) ? $row['pay_amount'] : 0;
        $paid_amount += intval($pmt_amt);

        $sheet->setCellValue('A' . $rowCount, '');
        $sheet->setCellValue('B' . $rowCount, isset($row['bill_date'])      ? $row['bill_date']      : '');
        $sheet->setCellValue('C' . $rowCount, isset($row['bill_number'])    ? $row['bill_number']    : '');
        $sheet->setCellValue('D' . $rowCount, isset($row['voucher_number']) ? $row['voucher_number'] : '');
        $sheet->setCellValue('E' . $rowCount, '');
        $sheet->setCellValue('F' . $rowCount, $pmt_amt);
        $sheet->setCellValue('G' . $rowCount, isset($row['balance']) ? $row['balance'] : '');

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

$total_payable = $grand_total - $paid_amount;

// Grand Total Row
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

// Summary row
$summaryRow = $rowCount;
$sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Transactions: ' . ($srNo - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd9edf7']],
]);

$sheet->mergeCells('E' . $summaryRow . ':G' . $summaryRow);
$sheet->setCellValue('E' . $summaryRow, 'Supplier: ' . $supplier_name);
$sheet->getStyle('E' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd9edf7']],
]);

// Signature section
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

$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':G' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated supplier statement. For any discrepancies, please contact the accounts department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080');
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Column widths
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(12);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(12);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);

// Row heights
$sheet->getRowDimension(1)->setRowHeight(35);
$sheet->getRowDimension(2)->setRowHeight(20);

// Generate file
$supplier_display = !empty($supplier_name) ? preg_replace('/[^A-Za-z0-9\-]/', '_', $supplier_name) : 'Supplier';
$file_name = 'Supplier_Statement_Report_' . $supplier_display . '_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
