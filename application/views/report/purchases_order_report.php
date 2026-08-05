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
if(!isset($purchase_order_report) || empty($purchase_order_report)) {
    // Create a simple spreadsheet with error message
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No purchase order data found for the selected criteria');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Purchase_Order_Report_No_Data.xlsx';
    
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

// Function to get status text
function getPurchaseOrderStatus($status) {
    switch ($status) {
        case "1":
            return "Draft";
        case "2":
            return "Sent";
        case "3":
            return "Viewed";
        case "4":
            return "Approved";
        case "5":
            return "Rejected";
        case "6":
            return "Canceled";
        default:
            return $status;
    }
}

// Rename the sheet
$sheet->setTitle('Purchase Order Report');

// Main Header
$sheet->mergeCells('A1:K1');
$sheet->setCellValue('A1', 'PURCHASE ORDER REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Filter Information
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:K2');
    $filter_text = 'Period: ' . $from_date . ' to ' . $to_date;
    if(isset($fullname) && !empty($fullname)) {
        $filter_text .= ' | Supplier: ' . $fullname;
    }
    $sheet->setCellValue('A2', $filter_text);
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
    'C' . $startRow => 'Order No.',
    'D' . $startRow => 'Project Code',
    'E' . $startRow => 'Sales Order',
    'F' . $startRow => 'OC Number',
    'G' . $startRow => 'Supplier Name',
    'H' . $startRow => 'Due Date',
    'I' . $startRow => 'Status',
    'J' . $startRow => 'Type',
    'K' . $startRow => 'Total (₹)'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':K' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_amount = 0;

// Status-wise totals
$status_totals = [];
$supplier_totals = [];

foreach ($purchase_order_report as $row) {
    $amount = isset($row->total) ? floatval($row->total) : 0;
    $total_amount += $amount;
    
    $status = getPurchaseOrderStatus($row->status);
    $supplier = isset($row->fullname) ? $row->fullname : 'Unknown';
    
    // Track status totals
    if(!isset($status_totals[$status])) {
        $status_totals[$status] = 0;
    }
    $status_totals[$status] += $amount;
    
    // Track supplier totals
    if(!isset($supplier_totals[$supplier])) {
        $supplier_totals[$supplier] = 0;
    }
    $supplier_totals[$supplier] += $amount;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, isset($row->purchase_date) ? date("d-m-Y", strtotime($row->purchase_date)) : '');
    $sheet->setCellValue('C' . $rowCount, isset($row->number_fk) ? $row->number_fk : '');
    $sheet->setCellValue('D' . $rowCount, isset($row->project_code) ? $row->project_code : 'N/A');
    $sheet->setCellValue('E' . $rowCount, isset($row->so_no) ? $row->so_no : 'N/A');
    $sheet->setCellValue('F' . $rowCount, isset($row->oc_no) ? $row->oc_no : 'N/A');
    $sheet->setCellValue('G' . $rowCount, $supplier);
    $sheet->setCellValue('H' . $rowCount, isset($row->delivery_date) ? date("d-m-Y", strtotime($row->delivery_date)) : '');
    $sheet->setCellValue('I' . $rowCount, $status);
    $sheet->setCellValue('J' . $rowCount, 'Purchase Order');
    $sheet->setCellValue('K' . $rowCount, $amount);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':K' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('B' . $rowCount)->applyFromArray($centerAlignStyle); // Date centered
    $sheet->getStyle('H' . $rowCount)->applyFromArray($centerAlignStyle); // Due Date centered
    $sheet->getStyle('I' . $rowCount)->applyFromArray($centerAlignStyle); // Status centered
    $sheet->getStyle('J' . $rowCount)->applyFromArray($centerAlignStyle); // Type centered
    $sheet->getStyle('K' . $rowCount)->applyFromArray($rightAlignStyle); // Amount right-aligned
    
    // Format amount column
    $sheet->getStyle('K' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row (with yellow background as in original)
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, '');
    $sheet->setCellValue('C' . $totalRow, '');
    $sheet->setCellValue('D' . $totalRow, '');
    $sheet->setCellValue('E' . $totalRow, '');
    $sheet->setCellValue('F' . $totalRow, '');
    $sheet->setCellValue('G' . $totalRow, '');
    $sheet->setCellValue('H' . $totalRow, '');
    $sheet->setCellValue('I' . $totalRow, '');
    $sheet->setCellValue('J' . $totalRow, 'TOTAL:');
    $sheet->setCellValue('K' . $totalRow, $total_amount);
    
    // Style for total row - using yellow background with dark text
    $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('J' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('K' . $totalRow)->applyFromArray($totalRowStyle); // Ensure K column has yellow color
    $sheet->getStyle('K' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':F' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Purchase Orders: ' . ($rowCou - 1));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('G' . $summaryRow . ':K' . $summaryRow);
$sheet->setCellValue('G' . $summaryRow, 'Average Order Value: ₹ ' . number_format(($rowCou > 1 ? $total_amount / ($rowCou - 1) : 0), 2));
$sheet->getStyle('E' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Status-wise Summary
$statusRow = $summaryRow + 2;

$sheet->mergeCells('A' . $statusRow . ':K' . $statusRow);
$sheet->setCellValue('A' . $statusRow, 'STATUS-WISE PURCHASE ORDER SUMMARY');
$sheet->getStyle('A' . $statusRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$statusRow++;

$sheet->setCellValue('A' . $statusRow, 'Status');
$sheet->setCellValue('B' . $statusRow, 'Order Count');
$sheet->setCellValue('C' . $statusRow, 'Total Amount (₹)');
$sheet->setCellValue('D' . $statusRow, 'Percentage (%)');
$sheet->getStyle('A' . $statusRow . ':D' . $statusRow)->applyFromArray(['font' => ['bold' => true]]);
$statusRow++;

// Calculate status counts
$status_counts = [];
foreach ($purchase_order_report as $row) {
    $status = getPurchaseOrderStatus($row->status);
    if(!isset($status_counts[$status])) {
        $status_counts[$status] = 0;
    }
    $status_counts[$status]++;
}

foreach ($status_totals as $status => $amount) {
    $count = isset($status_counts[$status]) ? $status_counts[$status] : 0;
    $percentage = ($total_amount > 0) ? ($amount / $total_amount) * 100 : 0;
    
    $sheet->setCellValue('A' . $statusRow, $status);
    $sheet->setCellValue('B' . $statusRow, $count);
    $sheet->setCellValue('C' . $statusRow, $amount);
    $sheet->setCellValue('D' . $statusRow, number_format($percentage, 2) . '%');
    
    $sheet->getStyle('B' . $statusRow)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('C' . $statusRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $statusRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $statusRow++;
}

// Supplier-wise Summary
$supplierRow = $statusRow + 2;

$sheet->mergeCells('A' . $supplierRow . ':K' . $supplierRow);
$sheet->setCellValue('A' . $supplierRow, 'SUPPLIER-WISE PURCHASE ORDER SUMMARY');
$sheet->getStyle('A' . $supplierRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$supplierRow++;

$sheet->setCellValue('A' . $supplierRow, 'Supplier Name');
$sheet->setCellValue('B' . $supplierRow, 'Order Count');
$sheet->setCellValue('C' . $supplierRow, 'Total Amount (₹)');
$sheet->setCellValue('D' . $supplierRow, 'Percentage (%)');
$sheet->getStyle('A' . $supplierRow . ':D' . $supplierRow)->applyFromArray(['font' => ['bold' => true]]);
$supplierRow++;

// Calculate supplier counts
$supplier_counts = [];
foreach ($purchase_order_report as $row) {
    $supplier = isset($row->fullname) ? $row->fullname : 'Unknown';
    if(!isset($supplier_counts[$supplier])) {
        $supplier_counts[$supplier] = 0;
    }
    $supplier_counts[$supplier]++;
}

// Sort suppliers by total amount (descending)
arsort($supplier_totals);

foreach ($supplier_totals as $supplier => $amount) {
    $count = isset($supplier_counts[$supplier]) ? $supplier_counts[$supplier] : 0;
    $percentage = ($total_amount > 0) ? ($amount / $total_amount) * 100 : 0;
    
    $sheet->setCellValue('A' . $supplierRow, $supplier);
    $sheet->setCellValue('B' . $supplierRow, $count);
    $sheet->setCellValue('C' . $supplierRow, $amount);
    $sheet->setCellValue('D' . $supplierRow, number_format($percentage, 2) . '%');
    
    $sheet->getStyle('B' . $supplierRow)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('C' . $supplierRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $supplierRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $supplierRow++;
}

// Top Suppliers
$topSupplierRow = $supplierRow + 2;

$sheet->mergeCells('A' . $topSupplierRow . ':K' . $topSupplierRow);
$sheet->setCellValue('A' . $topSupplierRow, 'TOP 5 SUPPLIERS BY PURCHASE VALUE');
$sheet->getStyle('A' . $topSupplierRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$topSupplierRow++;

$sheet->setCellValue('A' . $topSupplierRow, 'Rank');
$sheet->setCellValue('B' . $topSupplierRow, 'Supplier Name');
$sheet->setCellValue('C' . $topSupplierRow, 'Total Amount (₹)');
$sheet->setCellValue('D' . $topSupplierRow, 'Percentage (%)');
$sheet->getStyle('A' . $topSupplierRow . ':D' . $topSupplierRow)->applyFromArray(['font' => ['bold' => true]]);
$topSupplierRow++;

$rank = 1;
$top_suppliers = array_slice($supplier_totals, 0, 5, true);

foreach ($top_suppliers as $supplier => $amount) {
    $percentage = ($total_amount > 0) ? ($amount / $total_amount) * 100 : 0;
    
    $sheet->setCellValue('A' . $topSupplierRow, $rank);
    $sheet->setCellValue('B' . $topSupplierRow, $supplier);
    $sheet->setCellValue('C' . $topSupplierRow, $amount);
    $sheet->setCellValue('D' . $topSupplierRow, number_format($percentage, 2) . '%');
    
    $sheet->getStyle('A' . $topSupplierRow)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('C' . $topSupplierRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $topSupplierRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rank++;
    $topSupplierRow++;
}

// Date Range Summary
$dateRangeRow = $topSupplierRow + 2;

$sheet->mergeCells('A' . $dateRangeRow . ':K' . $dateRangeRow);
$sheet->setCellValue('A' . $dateRangeRow, 'DATE RANGE SUMMARY');
$sheet->getStyle('A' . $dateRangeRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$dateRangeRow++;

$sheet->setCellValue('A' . $dateRangeRow, 'Period');
$sheet->setCellValue('B' . $dateRangeRow, 'Total Orders');
$sheet->setCellValue('C' . $dateRangeRow, 'Total Amount (₹)');
$sheet->setCellValue('D' . $dateRangeRow, 'Avg per Order (₹)');
$sheet->getStyle('A' . $dateRangeRow . ':D' . $dateRangeRow)->applyFromArray(['font' => ['bold' => true]]);
$dateRangeRow++;

$avg_per_order = ($rowCou > 1) ? $total_amount / ($rowCou - 1) : 0;

$sheet->setCellValue('A' . $dateRangeRow, $from_date . ' to ' . $to_date);
$sheet->setCellValue('B' . $dateRangeRow, ($rowCou - 1));
$sheet->setCellValue('C' . $dateRangeRow, $total_amount);
$sheet->setCellValue('D' . $dateRangeRow, $avg_per_order);

$sheet->getStyle('B' . $dateRangeRow)->applyFromArray($centerAlignStyle);
$sheet->getStyle('C' . $dateRangeRow . ':D' . $dateRangeRow)->applyFromArray($rightAlignStyle);
$sheet->getStyle('C' . $dateRangeRow)->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle('D' . $dateRangeRow)->getNumberFormat()->setFormatCode('#,##0.00');

// Signature Section
$signatureRow = $dateRangeRow + 3;

$sheet->mergeCells('A' . $signatureRow . ':E' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, '_________________________');
$sheet->mergeCells('F' . $signatureRow . ':K' . $signatureRow);
$sheet->setCellValue('E' . $signatureRow, '_________________________');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':E' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Prepared By');
$sheet->mergeCells('F' . $signatureRow . ':K' . $signatureRow);
$sheet->setCellValue('E' . $signatureRow, 'Authorized Signatory');
$signatureRow++;

$sheet->mergeCells('A' . $signatureRow . ':K' . $signatureRow);
$sheet->setCellValue('A' . $signatureRow, 'Generated on: ' . date('d-m-Y'));
$sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer Note
$footerRow = $signatureRow + 2;
$sheet->mergeCells('A' . $footerRow . ':K' . $footerRow);
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated purchase order report. For any discrepancies, please contact the purchase department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'K') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(12);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(15); // Project Code
$sheet->getColumnDimension('E')->setWidth(15); // Sales Order
$sheet->getColumnDimension('F')->setWidth(15); // OC Number
$sheet->getColumnDimension('G')->setWidth(30); // Supplier Name
$sheet->getColumnDimension('H')->setWidth(12); // Due Date
$sheet->getColumnDimension('I')->setWidth(12); // Status
$sheet->getColumnDimension('J')->setWidth(15); // Type
$sheet->getColumnDimension('K')->setWidth(15); // Total

// Generate filename
$supplier_display = isset($fullname) && !empty($fullname) ? preg_replace('/[^A-Za-z0-9\-]/', '_', $fullname) : 'All';
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Purchase_Order_Report_' . $supplier_display . '_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>