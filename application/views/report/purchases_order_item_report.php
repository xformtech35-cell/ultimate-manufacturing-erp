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
if(!isset($purchase_order_item_report1) || empty($purchase_order_item_report1)) {
    // Create a simple spreadsheet with error message
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'No purchase order item data found for the selected criteria');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
    
    $file_name = 'Purchase_Order_Item_Report_No_Data.xlsx';
    
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
$sheet->setTitle('Purchase Order Item');

// Main Header
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A1', 'PURCHASE ORDER ITEM REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Filter Information
if(isset($from_date) && isset($to_date)) {
    $sheet->mergeCells('A2:E2');
    $filter_text = 'Period: ' . $from_date . ' to ' . $to_date;
    if(isset($fullname) && !empty($fullname)) {
        $filter_text .= ' | Supplier: ' . $fullname;
    }
    $sheet->setCellValue('A2', $filter_text);
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
    'B' . $startRow => 'Item Name',
    'C' . $startRow => 'Quantity',
    'D' . $startRow => 'Price (₹)',
    'E' . $startRow => 'Amount (₹)'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style
$sheet->getStyle('A' . $startRow . ':E' . $startRow)->applyFromArray($columnHeaderStyle);

// Populate data
$rowCount = $startRow + 1;
$rowCou = 1;
$total_quantity = 0;
$total_price = 0;
$total_amount = 0;

// Item-wise tracking for summary
$item_totals = [];

foreach ($purchase_order_item_report1 as $row) {
    $quantity = isset($row->quantity) ? intval($row->quantity) : 0;
    $price = isset($row->price) ? floatval($row->price) : 0;
    $amount = isset($row->amount) ? floatval($row->amount) : 0;
    
    $total_quantity += $quantity;
    $total_price += $price;
    $total_amount += $amount;
    
    $item_name = isset($row->product_name) ? $row->product_name : 'Unknown';
    
    // Track item totals
    if(!isset($item_totals[$item_name])) {
        $item_totals[$item_name] = [
            'quantity' => 0,
            'amount' => 0,
            'avg_price' => 0
        ];
    }
    $item_totals[$item_name]['quantity'] += $quantity;
    $item_totals[$item_name]['amount'] += $amount;
    
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $item_name);
    $sheet->setCellValue('C' . $rowCount, $quantity);
    $sheet->setCellValue('D' . $rowCount, $price);
    $sheet->setCellValue('E' . $rowCount, $amount);
    
    // Apply border style to data row
    $sheet->getStyle('A' . $rowCount . ':E' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Apply alignment styles
    $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr.No. centered
    $sheet->getStyle('C' . $rowCount . ':E' . $rowCount)->applyFromArray($rightAlignStyle); // Numbers right-aligned
    
    // Format columns
    $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rowCou++;
    $rowCount++;
}

// Add total row (with yellow background as in original)
$totalRow = $rowCount;

if($rowCou > 1) {
    // Empty cells before total label
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, '');
    $sheet->setCellValue('C' . $totalRow, $total_quantity);
    $sheet->setCellValue('D' . $totalRow, $total_price);
    $sheet->setCellValue('E' . $totalRow, $total_amount);
    
    // Add "Total:" label in column C (as in original code)
    $sheet->setCellValue('C' . $totalRow, $total_quantity);
    
    // Style for total row - using yellow background
    $sheet->getStyle('A' . $totalRow . ':E' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('C' . $totalRow . ':E' . $totalRow)->applyFromArray($rightAlignStyle);
    
    // Format total row numbers
    $sheet->getStyle('C' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('D' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Summary Statistics
$summaryRow = $totalRow + 2;

$sheet->mergeCells('A' . $summaryRow . ':B' . $summaryRow);
$sheet->setCellValue('A' . $summaryRow, 'Total Items: ' . count($item_totals));
$sheet->getStyle('A' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

$sheet->mergeCells('C' . $summaryRow . ':E' . $summaryRow);
$avg_amount = ($rowCou > 1) ? $total_amount / ($rowCou - 1) : 0;
$sheet->setCellValue('C' . $summaryRow, 'Average per Item: ₹ ' . number_format($avg_amount, 2));
$sheet->getStyle('C' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'd9edf7'],
    ],
]);

// Item-wise Summary
$itemSummaryRow = $summaryRow + 2;

$sheet->mergeCells('A' . $itemSummaryRow . ':E' . $itemSummaryRow);
$sheet->setCellValue('A' . $itemSummaryRow, 'ITEM-WISE PURCHASE SUMMARY');
$sheet->getStyle('A' . $itemSummaryRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$itemSummaryRow++;

$sheet->setCellValue('A' . $itemSummaryRow, 'Item Name');
$sheet->setCellValue('B' . $itemSummaryRow, 'Quantity');
$sheet->setCellValue('C' . $itemSummaryRow, 'Total Amount (₹)');
$sheet->setCellValue('D' . $itemSummaryRow, 'Avg Price (₹)');
$sheet->getStyle('A' . $itemSummaryRow . ':D' . $itemSummaryRow)->applyFromArray(['font' => ['bold' => true]]);
$itemSummaryRow++;

// Calculate average price for each item
foreach ($item_totals as $item_name => $data) {
    $avg_price = ($data['quantity'] > 0) ? $data['amount'] / $data['quantity'] : 0;
    
    $sheet->setCellValue('A' . $itemSummaryRow, $item_name);
    $sheet->setCellValue('B' . $itemSummaryRow, $data['quantity']);
    $sheet->setCellValue('C' . $itemSummaryRow, $data['amount']);
    $sheet->setCellValue('D' . $itemSummaryRow, $avg_price);
    
    $sheet->getStyle('B' . $itemSummaryRow . ':D' . $itemSummaryRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('B' . $itemSummaryRow)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('C' . $itemSummaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('D' . $itemSummaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $itemSummaryRow++;
}

// Top Items by Purchase Value
$topItemsRow = $itemSummaryRow + 2;

$sheet->mergeCells('A' . $topItemsRow . ':E' . $topItemsRow);
$sheet->setCellValue('A' . $topItemsRow, 'TOP ITEMS BY PURCHASE VALUE');
$sheet->getStyle('A' . $topItemsRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$topItemsRow++;

$sheet->setCellValue('A' . $topItemsRow, 'Rank');
$sheet->setCellValue('B' . $topItemsRow, 'Item Name');
$sheet->setCellValue('C' . $topItemsRow, 'Quantity');
$sheet->setCellValue('D' . $topItemsRow, 'Total Amount (₹)');
$sheet->setCellValue('E' . $topItemsRow, 'Avg Price (₹)');
$sheet->getStyle('A' . $topItemsRow . ':E' . $topItemsRow)->applyFromArray(['font' => ['bold' => true]]);
$topItemsRow++;

// Sort items by amount (descending)
uasort($item_totals, function($a, $b) {
    return $b['amount'] <=> $a['amount'];
});

$rank = 1;
$top_items = array_slice($item_totals, 0, 5, true);

foreach ($top_items as $item_name => $data) {
    $avg_price = ($data['quantity'] > 0) ? $data['amount'] / $data['quantity'] : 0;
    
    $sheet->setCellValue('A' . $topItemsRow, $rank);
    $sheet->setCellValue('B' . $topItemsRow, $item_name);
    $sheet->setCellValue('C' . $topItemsRow, $data['quantity']);
    $sheet->setCellValue('D' . $topItemsRow, $data['amount']);
    $sheet->setCellValue('E' . $topItemsRow, $avg_price);
    
    $sheet->getStyle('A' . $topItemsRow)->applyFromArray($centerAlignStyle);
    $sheet->getStyle('C' . $topItemsRow . ':E' . $topItemsRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $topItemsRow)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('D' . $topItemsRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('E' . $topItemsRow)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $rank++;
    $topItemsRow++;
}

// Supplier Summary (if fullname is provided)
if(isset($fullname) && !empty($fullname)) {
    $supplierRow = $topItemsRow + 2;
    
    $sheet->mergeCells('A' . $supplierRow . ':E' . $supplierRow);
    $sheet->setCellValue('A' . $supplierRow, 'SUPPLIER SUMMARY');
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
    $sheet->setCellValue('B' . $supplierRow, $fullname);
    $sheet->setCellValue('C' . $supplierRow, 'Total Items');
    $sheet->setCellValue('D' . $supplierRow, $total_quantity);
    $sheet->setCellValue('E' . $supplierRow, $total_amount);
    
    $sheet->getStyle('A' . $supplierRow)->applyFromArray(['font' => ['bold' => true]]);
    $sheet->getStyle('C' . $supplierRow . ':E' . $supplierRow)->applyFromArray($rightAlignStyle);
    $sheet->getStyle('C' . $supplierRow)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('E' . $supplierRow)->getNumberFormat()->setFormatCode('#,##0.00');
}

// Date Range Summary
$dateRangeRow = (isset($supplierRow) ? $supplierRow : $topItemsRow) + 2;

$sheet->mergeCells('A' . $dateRangeRow . ':E' . $dateRangeRow);
$sheet->setCellValue('A' . $dateRangeRow, 'PERIOD SUMMARY');
$sheet->getStyle('A' . $dateRangeRow)->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '3c8dbc'],
    ],
    'font' => ['color' => ['rgb' => 'FFFFFF']],
]);
$dateRangeRow++;

$sheet->setCellValue('A' . $dateRangeRow, 'Description');
$sheet->setCellValue('B' . $dateRangeRow, 'Value');
$sheet->getStyle('A' . $dateRangeRow . ':B' . $dateRangeRow)->applyFromArray(['font' => ['bold' => true]]);
$dateRangeRow++;

$summary_data = [
    ['Total Quantity Purchased', $total_quantity],
    ['Total Purchase Value', $total_amount],
    ['Unique Items', count($item_totals)],
    ['Period', $from_date . ' to ' . $to_date]
];

foreach ($summary_data as $data) {
    $sheet->setCellValue('A' . $dateRangeRow, $data[0]);
    $sheet->setCellValue('B' . $dateRangeRow, $data[1]);
    
    if(is_numeric($data[1])) {
        $sheet->getStyle('B' . $dateRangeRow)->applyFromArray($rightAlignStyle);
        if(strpos($data[0], 'Value') !== false || strpos($data[0], 'Amount') !== false) {
            $sheet->getStyle('B' . $dateRangeRow)->getNumberFormat()->setFormatCode('#,##0.00');
        } else {
            $sheet->getStyle('B' . $dateRangeRow)->getNumberFormat()->setFormatCode('#,##0');
        }
    }
    
    $dateRangeRow++;
}

// Signature Section
$signatureRow = $dateRangeRow + 3;

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
$sheet->setCellValue('A' . $footerRow, 'This is a system-generated purchase order item report. For any discrepancies, please contact the purchase department.');
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
$sheet->getStyle('A' . $footerRow)->getFont()->getColor()->setARGB('FF808080'); // Gray color
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', 'E') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set minimum column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(15);

// Generate filename
$supplier_display = isset($fullname) && !empty($fullname) ? preg_replace('/[^A-Za-z0-9\-]/', '_', $fullname) : 'All';
$from_date_display = isset($from_date) ? str_replace('/', '-', $from_date) : date('d-m-Y');
$to_date_display = isset($to_date) ? str_replace('/', '-', $to_date) : date('d-m-Y');
$file_name = 'Purchase_Order_Item_Report_' . $supplier_display . '_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>