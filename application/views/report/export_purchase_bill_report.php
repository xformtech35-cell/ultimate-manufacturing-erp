<?php

// No direct script access
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '/third_party/amount_convert.php');

// Load PhpSpreadsheet via Composer autoload
require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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

$totalRowStyle = [
    'font' => [
        'bold' => true,
        'size' => 16,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'e8f442'],
    ],
    'borders' => [
        'top' => ['borderStyle' => Border::BORDER_THIN],
        'bottom' => ['borderStyle' => Border::BORDER_THIN],
        'left' => ['borderStyle' => Border::BORDER_THIN],
        'right' => ['borderStyle' => Border::BORDER_THIN],
    ],
];

// Rename the sheet
$sheet->setTitle('Purchase Voucher Report');

// Merge cells for main header (updated to M column since we added Supplier Code)
$sheet->mergeCells('A1:M1');

// Set main header with style
$sheet->setCellValue('A1', 'Purchase Voucher Report');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers - ADDED Supplier Code column
$headers = [
    'A2' => 'Sr.No.',
    'B2' => 'Voucher No',
    'C2' => 'Voucher Date',
    'D2' => 'Supplier Code',      // NEW: Supplier Code column
    'E2' => 'Supplier Name',
    'F2' => 'Type',
    'G2' => 'Total Before Tax',
    'H2' => 'SGST',
    'I2' => 'CGST',
    'J2' => 'IGST',
    'K2' => 'Total GST',
    'L2' => 'Grand Total',
    'M2' => 'Balance',
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Apply column header style (updated range to M2)
$sheet->getStyle('A2:M2')->applyFromArray($columnHeaderStyle);

// Populate data - group by Voucher number to show summary per Voucher
$rowCount = 3;
$rowCou = 1;
$t_before_tax = 0; $t_sgst = 0; $t_igst = 0; $t_gst = 0; $t_total = 0; $t_balance = 0;

// Group data by Voucher number
$grouped_data = [];
foreach ($result as $row) {
    $bill_no = $row->number;
    if (!isset($grouped_data[$bill_no])) {
        $grouped_data[$bill_no] = [
            'number' => $row->number,
            'date' => $row->date,
            'supplier_code' => isset($row->s_code) ? $row->s_code : (isset($row->supplier_code) ? $row->supplier_code : ''), // FETCH SUPPLIER CODE
            'supplier_name' => !empty($row->company_name) ? $row->company_name : (!empty($row->supplier_fullname) ? $row->supplier_fullname : 'Supplier ID: ' . $row->supplier_id_fk),
            'total_before_tax' => 0,
            'total_sgst' => 0,
            'total_cgst' => 0,
            'total_igst' => 0,
            'total_gst' => 0,
            'grand_total' => 0,
            'balance' => $row->balance,
            'gst_type' => $row->gst_type
        ];
    }
    
    $amount = floatval($row->amount);
    $sgst = floatval($row->sgst);
    $cgst = floatval($row->cgst);
    $igst = floatval($row->igst);
    $total_gst = $sgst + $cgst + $igst;
    $grand_total = $amount + $total_gst - floatval($row->discount);
    
    $grouped_data[$bill_no]['total_before_tax'] += $amount;
    $grouped_data[$bill_no]['total_sgst'] += $sgst;
    $grouped_data[$bill_no]['total_cgst'] += $cgst;
    $grouped_data[$bill_no]['total_igst'] += $igst;
    $grouped_data[$bill_no]['total_gst'] += $total_gst;
    $grouped_data[$bill_no]['grand_total'] += $grand_total;
}

foreach ($grouped_data as $bill) {
    // Determine GST type
    $gst_type = '';
    $sgst = 0;
    $igst = 0;
    
    if ($bill['gst_type'] != 'I') {
        $gst_type = 'SGST';
        $sgst = $bill['total_gst'] / 2;
    } else {
        $gst_type = 'IGST';
        $igst = $bill['total_gst'];
    }
    
    // Set data with Supplier Code in column D
    $sheet->setCellValue('A' . $rowCount, $rowCou);
    $sheet->setCellValue('B' . $rowCount, $bill['number']);
    $sheet->setCellValue('C' . $rowCount, date("d-m-Y", strtotime($bill['date'])));
    $sheet->setCellValue('D' . $rowCount, $bill['supplier_code']); // SUPPLIER CODE HERE
    $sheet->setCellValue('E' . $rowCount, $bill['supplier_name']);
    $sheet->setCellValue('F' . $rowCount, $gst_type);
    $sheet->setCellValueExplicit('G' . $rowCount, indian_number_format((float)$bill['total_before_tax'], 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('H' . $rowCount, indian_number_format((float)$sgst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('I' . $rowCount, indian_number_format((float)$sgst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('J' . $rowCount, indian_number_format((float)$igst, 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('K' . $rowCount, indian_number_format((float)$bill['total_gst'], 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('L' . $rowCount, indian_number_format((float)$bill['grand_total'], 2), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('M' . $rowCount, indian_number_format((float)$bill['balance'], 2), DataType::TYPE_STRING);
    
    $t_before_tax += (float)$bill['total_before_tax']; 
    $t_sgst += (float)$sgst; 
    $t_igst += (float)$igst;
    $t_gst += (float)$bill['total_gst']; 
    $t_total += (float)$bill['grand_total']; 
    $t_balance += (float)$bill['balance'];
    
    // Apply border style to data row (updated to M column)
    $sheet->getStyle('A' . $rowCount . ':M' . $rowCount)->applyFromArray($dataCellStyle);
    
    // Right align amount columns
    $amountColumns = ['G', 'H', 'I', 'J', 'K', 'L', 'M'];
    foreach ($amountColumns as $col) {
        $sheet->getStyle($col . $rowCount)->applyFromArray($rightAlignStyle);
    }
    
    $rowCou++;
    $rowCount++;
}

// Add total row
$totalRow = $rowCount;

// Set total row labels (updated columns)
$sheet->setCellValue('F' . $totalRow, 'Total:');
$sheet->setCellValue('G' . $totalRow, 'Total:');

// Apply total row style to cells that will have totals (updated to M column)
$totalCells = ['F' . $totalRow, 'G' . $totalRow, 'H' . $totalRow, 'I' . $totalRow, 
               'J' . $totalRow, 'K' . $totalRow, 'L' . $totalRow, 'M' . $totalRow];
foreach ($totalCells as $cell) {
    $sheet->getStyle($cell)->applyFromArray($totalRowStyle);
}

// Add SUM formulas or values
$formulas = [
    'G' . $totalRow => indian_number_format($t_before_tax, 2),
    'H' . $totalRow => indian_number_format($t_sgst, 2),
    'I' . $totalRow => indian_number_format($t_sgst, 2),
    'J' . $totalRow => indian_number_format($t_igst, 2),
    'K' . $totalRow => indian_number_format($t_gst, 2),
    'L' . $totalRow => indian_number_format($t_total, 2),
    'M' . $totalRow => indian_number_format($t_balance, 2)
];

foreach ($formulas as $cell => $value) {
    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);
}

// Auto-size columns (updated to M)
foreach (range('A', 'M') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set column alignment for specific columns
$sheet->getStyle('C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date column centered
$sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Type column centered

// Format amount columns as numbers with 2 decimal places (updated columns)
$amountColumns = ['G', 'H', 'I', 'J', 'K', 'L', 'M'];
$lastDataRow = $rowCount - 1;
foreach ($amountColumns as $col) {
    $sheet->getStyle($col . '3:' . $col . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle($col . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
}

// Generate filename
$from_date_display = isset($from_date) ? date('d-m-Y', strtotime($from_date)) : date('d-m-Y');
$to_date_display = isset($to_date) ? date('d-m-Y', strtotime($to_date)) : date('d-m-Y');
$file_name = 'Purchase_Voucher_Report_' . $from_date_display . '_to_' . $to_date_display . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $file_name . '"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>