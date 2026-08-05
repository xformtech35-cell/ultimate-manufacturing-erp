<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

$spreadsheet = new Spreadsheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(11);

// Style definitions
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 22],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '31a3dd']],
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN]
    ],
];

$subHeaderStyle = [
    'font' => ['bold' => true, 'size' => 12],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e8f442']],
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN]
    ],
];

$dataCellStyle = [
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN]
    ],
];

// Create first sheet for Job Order Details
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Job Order Details');

// Main Header
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A1', 'JOB ORDER DETAILED REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);
$sheet->getStyle('A1')->getFont()->setSize(22);

// Job Order Number
$sheet->mergeCells('A3:B3');
$sheet->setCellValue('A3', 'Job Order Number:');
$sheet->setCellValue('C3', $joborder->number_fk);
$sheet->getStyle('A3:B3')->applyFromArray($subHeaderStyle);

// Date
$sheet->mergeCells('A4:B4');
$sheet->setCellValue('A4', 'Date:');
$sheet->setCellValue('C4', !empty($joborder->date) ? date("d-m-Y", strtotime($joborder->date)) : '');
$sheet->getStyle('A4:B4')->applyFromArray($subHeaderStyle);

// Company Name
$sheet->mergeCells('A5:B5');
$sheet->setCellValue('A5', 'Company Name:');
$sheet->setCellValue('C5', isset($joborder->company_name) ? $joborder->company_name : '');
$sheet->getStyle('A5:B5')->applyFromArray($subHeaderStyle);

// Project Code
$sheet->mergeCells('A6:B6');
$sheet->setCellValue('A6', 'Project Code:');
$sheet->setCellValue('C6', isset($joborder->project_code) ? $joborder->project_code : '');
$sheet->getStyle('A6:B6')->applyFromArray($subHeaderStyle);

// Customer Code
$sheet->mergeCells('A7:B7');
$sheet->setCellValue('A7', 'Customer Code:');
$sheet->setCellValue('C7', isset($joborder->customer_code) ? $joborder->customer_code : '');
$sheet->getStyle('A7:B7')->applyFromArray($subHeaderStyle);

// Project Quantity
$sheet->mergeCells('A8:B8');
$sheet->setCellValue('A8', 'Project Quantity:');
$sheet->setCellValue('C8', isset($joborder->project_qty) ? $joborder->project_qty : '');
$sheet->getStyle('A8:B8')->applyFromArray($subHeaderStyle);

// System
$sheet->mergeCells('D3:E3');
$sheet->setCellValue('D3', 'System:');
$sheet->setCellValue('F3', isset($joborder->system) ? $joborder->system : '');
$sheet->getStyle('D3:E3')->applyFromArray($subHeaderStyle);

// Location
$sheet->mergeCells('D4:E4');
$sheet->setCellValue('D4', 'Location:');
$sheet->setCellValue('F4', isset($joborder->location) ? $joborder->location : '');
$sheet->getStyle('D4:E4')->applyFromArray($subHeaderStyle);

// Capacity
$sheet->mergeCells('D5:E5');
$sheet->setCellValue('D5', 'Capacity:');
$sheet->setCellValue('F5', isset($joborder->capacity) ? $joborder->capacity : '');
$sheet->getStyle('D5:E5')->applyFromArray($subHeaderStyle);

// OC Number
$sheet->mergeCells('D6:E6');
$sheet->setCellValue('D6', 'OC Number:');
$sheet->setCellValue('F6', isset($joborder->oc_number) ? $joborder->oc_number : '');
$sheet->getStyle('D6:E6')->applyFromArray($subHeaderStyle);

// Status
$sheet->mergeCells('D7:E7');
$sheet->setCellValue('D7', 'Status:');
$status_label = 'Unknown';
if ($joborder->status == 1) $status_label = 'Draft';
elseif ($joborder->status == 2) $status_label = 'Sent';
elseif ($joborder->status == 3) $status_label = 'Viewed';
elseif ($joborder->status == 4) $status_label = 'Approved';
elseif ($joborder->status == 5) $status_label = 'Rejected';
elseif ($joborder->status == 6) $status_label = 'Canceled';
$sheet->setCellValue('F7', $status_label);
$sheet->getStyle('D7:E7')->applyFromArray($subHeaderStyle);

// Note
if(!empty($joborder->note)) {
    $sheet->mergeCells('D8:E8');
    $sheet->setCellValue('D8', 'Note:');
    $sheet->setCellValue('F8', $joborder->note);
    $sheet->getStyle('D8:E8')->applyFromArray($subHeaderStyle);
}

// Customer Details Section
$rowStart = 10;
$sheet->mergeCells('A' . $rowStart . ':J' . $rowStart);
$sheet->setCellValue('A' . $rowStart, 'CUSTOMER DETAILS');
$sheet->getStyle('A' . $rowStart)->applyFromArray($subHeaderStyle);
$sheet->getStyle('A' . $rowStart)->getFont()->setBold(true);

$rowStart++;
$sheet->mergeCells('A' . $rowStart . ':B' . $rowStart);
$sheet->setCellValue('A' . $rowStart, 'Full Name:');
$sheet->setCellValue('C' . $rowStart, isset($joborder->fullname) ? $joborder->fullname : '');
$sheet->mergeCells('D' . $rowStart . ':E' . $rowStart);
$sheet->setCellValue('D' . $rowStart, 'Email:');
$sheet->setCellValue('F' . $rowStart, isset($joborder->email) ? $joborder->email : '');

$rowStart++;
$sheet->mergeCells('A' . $rowStart . ':B' . $rowStart);
$sheet->setCellValue('A' . $rowStart, 'Mobile:');
$sheet->setCellValue('C' . $rowStart, isset($joborder->mobile) ? $joborder->mobile : '');
$sheet->mergeCells('D' . $rowStart . ':E' . $rowStart);
$sheet->setCellValue('D' . $rowStart, 'GST No:');
$sheet->setCellValue('F' . $rowStart, isset($joborder->gst) ? $joborder->gst : '');

$rowStart++;
$sheet->mergeCells('A' . $rowStart . ':B' . $rowStart);
$sheet->setCellValue('A' . $rowStart, 'PAN Card:');
$sheet->setCellValue('C' . $rowStart, isset($joborder->pancard) ? $joborder->pancard : '');
$sheet->mergeCells('D' . $rowStart . ':E' . $rowStart);
$sheet->setCellValue('D' . $rowStart, 'State Code:');
$sheet->setCellValue('F' . $rowStart, isset($joborder->state_code) ? $joborder->state_code : '');

$rowStart++;
$sheet->mergeCells('A' . $rowStart . ':B' . $rowStart);
$sheet->setCellValue('A' . $rowStart, 'Address:');
$sheet->mergeCells('C' . $rowStart . ':J' . $rowStart);
$sheet->setCellValue('C' . $rowStart, isset($joborder->address) ? $joborder->address : '');

// Items Section
$rowStart = $rowStart + 2;
$sheet->mergeCells('A' . $rowStart . ':J' . $rowStart);
$sheet->setCellValue('A' . $rowStart, 'JOB ORDER ITEMS');
$sheet->getStyle('A' . $rowStart)->applyFromArray($subHeaderStyle);
$sheet->getStyle('A' . $rowStart)->getFont()->setBold(true);

$rowStart++;
$headers = [
    'A' . $rowStart => 'Sr.No.',
    'B' . $rowStart => 'Product Code',
    'C' . $rowStart => 'Product Name',
    'D' . $rowStart => 'Description',
    'E' . $rowStart => 'Quantity',
    'F' . $rowStart => 'Unit',
    'G' . $rowStart => 'Tag No.',
    'H' . $rowStart => 'Scope',
    'I' . $rowStart => 'Stores Remark',
    'J' . $rowStart => 'Remark'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}
$sheet->getStyle('A' . $rowStart . ':J' . $rowStart)->applyFromArray($headerStyle);

$rowStart++;
$i = 1;
foreach ($joborder_details as $detail) {
    $sheet->setCellValue('A' . $rowStart, $i);
    $sheet->setCellValue('B' . $rowStart, isset($detail->product_code) ? $detail->product_code : '');
    $sheet->setCellValue('C' . $rowStart, isset($detail->item_name) ? $detail->item_name : '');
    $sheet->setCellValue('D' . $rowStart, isset($detail->description) ? $detail->description : '');
    $sheet->setCellValue('E' . $rowStart, isset($detail->quantity) ? $detail->quantity : '');
    $sheet->setCellValue('F' . $rowStart, isset($detail->unit) ? $detail->unit : '');
    $sheet->setCellValue('G' . $rowStart, isset($detail->tag_no) ? $detail->tag_no : '');
    $sheet->setCellValue('H' . $rowStart, isset($detail->scope) ? $detail->scope : '');
    $sheet->setCellValue('I' . $rowStart, isset($detail->stores_remark) ? $detail->stores_remark : '');
    $sheet->setCellValue('J' . $rowStart, isset($detail->remark) ? $detail->remark : '');
    
    $sheet->getStyle('A' . $rowStart . ':J' . $rowStart)->applyFromArray($dataCellStyle);
    $rowStart++;
    $i++;
}

// Auto-size columns
foreach (range('A', 'J') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set alignment for description and scope columns
$sheet->getStyle('D')->getAlignment()->setWrapText(true);
$sheet->getStyle('H')->getAlignment()->setWrapText(true);
$sheet->getStyle('J')->getAlignment()->setWrapText(true);

// File name
$file_name = 'JobOrder_Report_' . $joborder->number_fk . '_' . date('d-m-Y') . '.xlsx';

// Output headers
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>