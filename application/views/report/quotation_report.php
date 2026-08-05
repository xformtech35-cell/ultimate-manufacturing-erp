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

// Rename the sheet
$sheet->setTitle('Quotation Report');

// Merge cells for main header
$sheet->mergeCells('A1:L1');

// Set main header with style
$sheet->setCellValue('A1', 'Quotation Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers
$sheet->setCellValue('A2', 'Sr.No.');
$sheet->setCellValue('B2', 'Status');
$sheet->setCellValue('C2', 'Number');
$sheet->setCellValue('D2', 'Date');
$sheet->setCellValue('E2', 'Total');
$sheet->setCellValue('F2', 'Company Name');
$sheet->setCellValue('G2', 'Customer Name');
$sheet->setCellValue('H2', 'GST Number');
$sheet->setCellValue('I2', 'GST Type');
$sheet->setCellValue('J2', 'Email');
$sheet->setCellValue('K2', 'Mobile');
$sheet->setCellValue('L2', 'Address');

// Apply column header style
$sheet->getStyle('A2:L2')->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = 3;
$rowCou = 1;

foreach ($result as $row) {
    
    // Determine status
    $status = '';
    switch ($row->status) {
        case 1:
            $status = 'Draft';
            break;
        case 2:
            $status = 'Sent';
            break;
        case 3:
            $status = 'Viewed';
            break;
        case 4:
            $status = 'Approved';
            break;
        case 5:
            $status = 'Rejected';
            break;
        case 6:
            $status = 'Canceled';
            break;
        default:
            $status = '';
    }
    
    // Determine GST type
    $type = ($row->gst_type != 'I') ? 'SGST' : 'IGST';
    
    // Set data
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $status);
    $sheet->setCellValue('C' . $rowCount, $row->number);
    $sheet->setCellValue('D' . $rowCount, date("d-m-Y", strtotime($row->date)));
    $sheet->setCellValue('E' . $rowCount, $row->total);
    $sheet->setCellValue('F' . $rowCount, $row->company_name);
    $sheet->setCellValue('G' . $rowCount, $row->fullname);
    $sheet->setCellValue('H' . $rowCount, $row->customer_gst);
    $sheet->setCellValue('I' . $rowCount, $type);
    $sheet->setCellValue('J' . $rowCount, $row->email);
    $sheet->setCellValue('K' . $rowCount, $row->mobile);
    $sheet->setCellValue('L' . $rowCount, $row->address);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':L' . $rowCount)->applyFromArray($dataCellStyle);
    
    $rowCou++;
    $rowCount++;
}

// Auto-size columns
foreach (range('A', 'L') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set column alignment for specific columns
$sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Total column

// Generate filename
$from_date_display = isset($from_date) ? $from_date : date('d-m-Y');
$to_date_display = isset($to_date) ? $to_date : date('d-m-Y');
$file_name = 'Quotation_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>