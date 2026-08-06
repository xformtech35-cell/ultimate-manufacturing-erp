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
        'startColor' => ['rgb' => '31a3dd'], // Changed to match your UI color
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
        'startColor' => ['rgb' => '31a3dd'], // Changed to match your UI color
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

// Helper function to clean HTML description
function cleanDescription($description) {
    // Decode HTML entities first
    $description = html_entity_decode($description);
    
    // Replace <br> and <br/> tags with space
    $description = preg_replace('/<br\s*\/?>/i', ' ', $description);
    
    // Replace </p> with space and <p> with nothing
    $description = preg_replace('/<\/p>/i', ' ', $description);
    $description = preg_replace('/<p[^>]*>/i', '', $description);
    
    // Remove any other HTML tags
    $description = strip_tags($description);
    
    // Remove extra whitespace
    $description = preg_replace('/\s+/', ' ', $description);
    
    // Trim the string
    $description = trim($description);
    
    return $description;
}

// Clear any previous output
if (ob_get_length()) {
    ob_clean();
}

// Rename the sheet
$sheet->setTitle('Inventory Report');

// Merge cells for main header
$sheet->mergeCells('A1:K1');

// Set main header with style
$sheet->setCellValue('A1', 'INVENTORY REPORT');
$sheet->getStyle('A1')->applyFromArray($headerStyle);

// Set column headers
$sheet->setCellValue('A2', 'Sr. No.');
$sheet->setCellValue('B2', 'Item Code');
$sheet->setCellValue('C2', 'Item Name');
$sheet->setCellValue('D2', 'Description');
$sheet->setCellValue('E2', 'HSN/SAC');
$sheet->setCellValue('F2', 'GST (%)');
$sheet->setCellValue('G2', 'Type');
$sheet->setCellValue('H2', 'Stock QTY');
$sheet->setCellValue('I2', 'Unit Price (₹)');
$sheet->setCellValue('J2', 'Total (₹)');

$sheet->setCellValue('H2', 'Unit');
$sheet->setCellValue('I2', 'Stock QTY');
$sheet->setCellValue('J2', 'Unit Price');
$sheet->setCellValue('K2', 'Total');

// Apply column header style
$sheet->getStyle('A2:K2')->applyFromArray($columnHeaderStyle);

// Set column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(40);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(10);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(12);
$sheet->getColumnDimension('I')->setWidth(15);
$sheet->getColumnDimension('J')->setWidth(15);
$sheet->getColumnDimension('K')->setWidth(15);

// Enable text wrapping for description column
//$sheet->getStyle('C')->getAlignment()->setWrapText(true);

// Populate data
$rowCount = 3;
$rowCou = 1;
$total_value = 0;

if (!empty($result)) {
    foreach ($result as $row) {
        
        // Determine item type
        $item_type = ($row->item_type == 'B') ? 'Boughtout' : 'Manufacturing';
        
        // Clean the description from HTML tags
        $clean_description = cleanDescription($row->prod_description);
        
        // Calculate total value for this item (QTY * Unit Price)
        $item_total = $row->stock * $row->cost_price;
        
        // Calculate overall total
        $total_value += $item_total;
        
        // Set data
        $sheet->setCellValue('A' . $rowCount, $rowCou);
        $sheet->setCellValue('B' . $rowCount, $row->code);
        $sheet->setCellValue('C' . $rowCount, $row->item_name);
        $sheet->setCellValue('D' . $rowCount, $clean_description);
        $sheet->setCellValue('E' . $rowCount, $row->hsn);
        $sheet->setCellValue('F' . $rowCount, $row->gst_per);
        $sheet->setCellValue('G' . $rowCount, $item_type);
        $sheet->setCellValue('H' . $rowCount, $row->unit);
        $sheet->setCellValue('I' . $rowCount, $row->stock);
        $sheet->setCellValue('J' . $rowCount, $row->cost_price);
        $sheet->setCellValue('K' . $rowCount, $item_total);
        
        // Apply border style to data row
        $sheet->getStyle('A' . $rowCount . ':K' . $rowCount)->applyFromArray($dataCellStyle);
        
        // Apply alignment
        $sheet->getStyle('A' . $rowCount)->applyFromArray($centerAlignStyle); // Sr No centered
        $sheet->getStyle('F' . $rowCount)->applyFromArray($centerAlignStyle); // GST centered
        $sheet->getStyle('H' . $rowCount)->applyFromArray($centerAlignStyle); // Unit centered
        $sheet->getStyle('I' . $rowCount)->applyFromArray($centerAlignStyle); // Stock QTY centered
        $sheet->getStyle('J' . $rowCount)->applyFromArray($rightAlignStyle); // Unit Price right-aligned
        $sheet->getStyle('K' . $rowCount)->applyFromArray($rightAlignStyle); // Total right-aligned
        
        // Highlight low stock in red
        if ($row->stock <= 5) {
            $sheet->getStyle('I' . $rowCount)->getFont()->getColor()->setRGB('FF0000');
            $sheet->getStyle('I' . $rowCount)->getFont()->setBold(true);
        }
        
        // Set vertical alignment to top for description column to handle multi-line text
        $sheet->getStyle('D' . $rowCount)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        
        $rowCou++;
        $rowCount++;
    }
    
    // Add total row
    $totalRow = $rowCount;
    $sheet->setCellValue('A' . $totalRow, '');
    $sheet->setCellValue('B' . $totalRow, '');
    $sheet->setCellValue('C' . $totalRow, '');
    $sheet->setCellValue('D' . $totalRow, '');
    $sheet->setCellValue('E' . $totalRow, '');
    $sheet->setCellValue('F' . $totalRow, '');
    $sheet->setCellValue('G' . $totalRow, '');
    $sheet->setCellValue('H' . $totalRow, '');
    $sheet->setCellValue('I' . $totalRow, '');
    $sheet->setCellValue('J' . $totalRow, 'TOTAL VALUE:');
    $sheet->setCellValue('K' . $totalRow, $total_value);
    
    // Style total row
    $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->applyFromArray($totalRowStyle);
    $sheet->getStyle('K' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('J' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
} else {
    $sheet->setCellValue('A3', 'No data available');
    $sheet->mergeCells('A3:K3');
    $sheet->getStyle('A3')->getFont()->setItalic(true);
}

// Format amount columns as currency/numbers
$sheet->getStyle('J3:J' . ($rowCount-1))->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle('K3:K' . ($rowCount-1))->getNumberFormat()->setFormatCode('#,##0.00');

// Add border to entire data range
$sheet->getStyle('A2:K' . ($rowCount))->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

// Freeze header row
$sheet->freezePane('A3');

// Generate filename
$file_name = 'Inventory_Report_' . date('Y-m-d') . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

// Create writer and output to browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Important: Add exit to prevent any additional output
exit;
?>
