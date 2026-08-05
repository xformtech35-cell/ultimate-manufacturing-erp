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
$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

// Define style arrays
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 16,
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
$sheet->setTitle('Sales Purchase Report');

// Merge cells for main header
$sheet->mergeCells('A1:K1');

// Set main header with style
$sheet->setCellValue('A1', 'Sales Purchase Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers
$sheet->setCellValue('A2', 'Sr.No.');
$sheet->setCellValue('B2', 'Type');
$sheet->setCellValue('C2', 'INV/PO No.');
$sheet->setCellValue('D2', 'Ref. No.');
$sheet->setCellValue('E2', 'Date');
$sheet->setCellValue('F2', 'GST Type');
$sheet->setCellValue('G2', 'Total Before Tax');
$sheet->setCellValue('H2', 'Total GST');
$sheet->setCellValue('I2', 'Grand Total');
$sheet->setCellValue('J2', 'Company Name');
$sheet->setCellValue('K2', 'GST Number');

// Apply column header style
$sheet->getStyle('A2:K2')->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = 3;
$rowCou = 1;

// Process sales data
foreach ($result_sales as $row) {
    $gst_type = ($row->gst_type == "S") ? "SGST" : "IGST";
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, 'Sales');
    $sheet->setCellValue('C' . $rowCount, $row->invoice_number);
    $sheet->setCellValue('D' . $rowCount, $row->customer_po);
    $sheet->setCellValue('E' . $rowCount, date("d-m-Y", strtotime($row->invoice_date)));
    $sheet->setCellValue('F' . $rowCount, $gst_type);
    $sheet->setCellValue('G' . $rowCount, $row->total_before_tax);
    $sheet->setCellValue('H' . $rowCount, $row->total_gst_amount);
    $sheet->setCellValue('I' . $rowCount, $row->total);
    $sheet->setCellValue('J' . $rowCount, $row->company_name);
    $sheet->setCellValue('K' . $rowCount, $row->customer_gst);
    
    // Apply border style and right alignment to amount columns
    $sheet->getStyle('A' . $rowCount . ':K' . $rowCount)->applyFromArray($dataCellStyle);
    $sheet->getStyle('G' . $rowCount . ':I' . $rowCount)->applyFromArray($rightAlignStyle);
    
    $rowCou++;
    $rowCount++;
}

// Process purchase data
foreach ($result_purchase_bill as $row) {
    $gst_type = ($row->gst_type == "S") ? "SGST" : "IGST";
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, 'Purchase');
    $sheet->setCellValue('C' . $rowCount, $row->number);
    $sheet->setCellValue('D' . $rowCount, $row->invoice_no);
    $sheet->setCellValue('E' . $rowCount, date("d-m-Y", strtotime($row->date)));
    $sheet->setCellValue('F' . $rowCount, $gst_type);
    $sheet->setCellValue('G' . $rowCount, $row->total_before_tax);
    $sheet->setCellValue('H' . $rowCount, $row->total_gst_amount);
    $sheet->setCellValue('I' . $rowCount, $row->total);
    $sheet->setCellValue('J' . $rowCount, $row->company_name);
    $sheet->setCellValue('K' . $rowCount, $row->customer_gst);
    
    // Apply border style and right alignment to amount columns
    $sheet->getStyle('A' . $rowCount . ':K' . $rowCount)->applyFromArray($dataCellStyle);
    $sheet->getStyle('G' . $rowCount . ':I' . $rowCount)->applyFromArray($rightAlignStyle);
    
    $rowCou++;
    $rowCount++;
}

// Auto-size columns
foreach (range('A', 'K') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set column alignment for specific columns
$sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date column centered
$sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // GST Type centered
$sheet->getStyle('G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Total Before Tax right-aligned
$sheet->getStyle('H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Total GST right-aligned
$sheet->getStyle('I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Grand Total right-aligned

// Format amount columns as currency/numbers
$sheet->getStyle('G3:G' . ($rowCount-1))->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle('H3:H' . ($rowCount-1))->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle('I3:I' . ($rowCount-1))->getNumberFormat()->setFormatCode('#,##0.00');

// Generate filename
$from_date_display = isset($from_date) ? $from_date : date('d-m-Y');
$to_date_display = isset($to_date) ? $to_date : date('d-m-Y');
$file_name = 'Sales_Purchase_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>