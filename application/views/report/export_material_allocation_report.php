<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if (!isset($show_project_cols)) {
    $CI =& get_instance();
    $sess = $CI->session->userdata('session_data_head');
    $user_role_id = $sess['result']['role'] ?? null;
    $role_name = strtolower($sess['result']['role_name'] ?? '');
    if ($role_name === 'admin' || $user_role_id == 1) {
        $show_project_cols = true;
    } else if ($user_role_id) {
        $perm_row = $CI->db->select('grp_perm')->from('permission')->where('role_id_fk', $user_role_id)->group_start()->where('grp_perm', 'Projects')->or_where('grp_perm', 'projects')->group_end()->get()->row_array();
        if ($perm_row) {
            $show_project_cols = true;
        } else {
            $count = $CI->db->where('role_id_fk', $user_role_id)->count_all_results('permission');
            $show_project_cols = ($count > 0) ? false : (in_array('Projects', $sess['permission'] ?? []) || in_array('projects', $sess['permission'] ?? []));
        }
    } else {
        $show_project_cols = in_array('Projects', $sess['permission'] ?? []) || in_array('projects', $sess['permission'] ?? []);
    }
}

$columnDefs = [
    ['title' => 'Sr.No.', 'key' => 'sr', 'type' => 'text'],
    ['title' => 'Allocation Date', 'key' => 'allocated_date', 'type' => 'date'],
];

if ($show_project_cols) {
    $columnDefs[] = ['title' => 'Project Code', 'key' => 'project_code', 'type' => 'text'];
    $columnDefs[] = ['title' => 'Project Name', 'key' => 'project_name', 'type' => 'text'];
}

array_push($columnDefs,
    ['title' => 'SO Reference', 'key' => 'salesorder_number', 'type' => 'text'],
    ['title' => 'BOM Number(s)', 'key' => 'bom_numbers', 'type' => 'text'],
    ['title' => 'Job Order No.', 'key' => 'joborder_number', 'type' => 'text'],
    ['title' => 'Item Code', 'key' => 'item_code', 'type' => 'text'],
    ['title' => 'Item Name', 'key' => 'item_name', 'type' => 'text'],
    ['title' => 'Allocated Qty', 'key' => 'alloc_qty', 'type' => 'number'],
    ['title' => 'Cost Price', 'key' => 'costPrice', 'type' => 'number'],
    ['title' => 'Total Cost', 'key' => 'lineTotal', 'type' => 'number'],
    ['title' => 'Pending Qty', 'key' => 'pendingQty', 'type' => 'number'],
    ['title' => 'Status', 'key' => 'status', 'type' => 'text'],
    ['title' => 'User Name', 'key' => 'username', 'type' => 'text']
);

$totalCols = count($columnDefs);
$maxCol = Coordinate::stringFromColumnIndex($totalCols);

$spreadsheet = new Spreadsheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(11);

// Style definitions
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '31a3dd']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$titleStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 18],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1c6c94']],
];

$subHeaderStyle = [
    'font' => ['bold' => true, 'size' => 11],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e8f442']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$dataCellStyle = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Material Allocation Report');

// Main Header
$sheet->mergeCells("A1:{$maxCol}1");
$sheet->setCellValue('A1', 'MATERIAL ALLOCATION REPORT');
$sheet->getStyle("A1:{$maxCol}1")->applyFromArray($titleStyle);
$sheet->getRowDimension(1)->setRowHeight(40);

// Filter Text (Period)
$filterText = 'Period: ' . ($from_date ? $from_date : '-') . ' to ' . ($to_date ? $to_date : '-');
$sheet->mergeCells("A2:{$maxCol}2");
$sheet->setCellValue('A2', $filterText);
$sheet->getStyle("A2:{$maxCol}2")->applyFromArray($subHeaderStyle);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(2)->setRowHeight(25);

// Table Headers
foreach ($columnDefs as $idx => $def) {
    $colLetter = Coordinate::stringFromColumnIndex($idx + 1);
    $sheet->setCellValue($colLetter . '4', $def['title']);
}
$sheet->getStyle("A4:{$maxCol}4")->applyFromArray($headerStyle);
$sheet->getRowDimension(4)->setRowHeight(25);

$rowNo = 5;
$sr = 1;
$totalAlloc = 0;
$totalPending = 0;
$totalCost = 0;

foreach ((array) $result as $row) {
    $alloc_qty = isset($row->allocated_quantity) ? (float) $row->allocated_quantity : 0;
    $pendingQty = isset($row->pending_quantity) ? (float) $row->pending_quantity : 0;
    $costPrice = isset($row->cost_price) ? (float) $row->cost_price : 0;
    $lineTotal = isset($row->total_cost) ? (float) $row->total_cost : ($alloc_qty * $costPrice);
    
    $totalAlloc += $alloc_qty;
    $totalPending += $pendingQty;
    $totalCost += $lineTotal;

    $rowValues = [
        'sr' => $sr,
        'allocated_date' => !empty($row->allocated_date) ? date('d-m-Y', strtotime($row->allocated_date)) : '',
        'project_code' => isset($row->project_code) ? $row->project_code : '',
        'project_name' => isset($row->project_name) ? $row->project_name : '',
        'salesorder_number' => isset($row->salesorder_number) ? $row->salesorder_number : '',
        'bom_numbers' => isset($row->bom_numbers) ? $row->bom_numbers : '',
        'joborder_number' => isset($row->joborder_number) ? $row->joborder_number : '',
        'item_code' => isset($row->item_code) ? $row->item_code : '',
        'item_name' => isset($row->item_name) ? $row->item_name : '',
        'alloc_qty' => $alloc_qty,
        'costPrice' => $costPrice,
        'lineTotal' => $lineTotal,
        'pendingQty' => $pendingQty,
        'status' => isset($row->status) ? ucfirst($row->status) : '',
        'username' => isset($row->username) ? $row->username : '',
    ];

    foreach ($columnDefs as $idx => $def) {
        $colLetter = Coordinate::stringFromColumnIndex($idx + 1);
        $val = isset($rowValues[$def['key']]) ? $rowValues[$def['key']] : '';
        $sheet->setCellValue($colLetter . $rowNo, $val);
        if ($def['type'] === 'number') {
            $sheet->getStyle($colLetter . $rowNo)->getNumberFormat()->setFormatCode('#,##0.00');
        }
    }

    $sheet->getStyle("A{$rowNo}:{$maxCol}{$rowNo}")->applyFromArray($dataCellStyle);

    $sr++;
    $rowNo++;
}

if (!empty($result)) {
    foreach ($columnDefs as $idx => $def) {
        $colLetter = Coordinate::stringFromColumnIndex($idx + 1);
        if ($def['key'] === 'item_name') {
            $sheet->setCellValue($colLetter . $rowNo, 'Total');
        } else if ($def['key'] === 'alloc_qty') {
            $sheet->setCellValue($colLetter . $rowNo, $totalAlloc);
            $sheet->getStyle($colLetter . $rowNo)->getNumberFormat()->setFormatCode('#,##0.00');
        } else if ($def['key'] === 'lineTotal') {
            $sheet->setCellValue($colLetter . $rowNo, $totalCost);
            $sheet->getStyle($colLetter . $rowNo)->getNumberFormat()->setFormatCode('#,##0.00');
        } else if ($def['key'] === 'pendingQty') {
            $sheet->setCellValue($colLetter . $rowNo, $totalPending);
            $sheet->getStyle($colLetter . $rowNo)->getNumberFormat()->setFormatCode('#,##0.00');
        } else {
            $sheet->setCellValue($colLetter . $rowNo, '');
        }
    }
    
    $sheet->getStyle("A{$rowNo}:{$maxCol}{$rowNo}")->applyFromArray($subHeaderStyle);
}

// Auto-size columns
for ($i = 1; $i <= $totalCols; $i++) {
    $colLetter = Coordinate::stringFromColumnIndex($i);
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
}

// Set wrap text on item name
foreach ($columnDefs as $idx => $def) {
    if ($def['key'] === 'item_name') {
        $colLetter = Coordinate::stringFromColumnIndex($idx + 1);
        $sheet->getStyle($colLetter)->getAlignment()->setWrapText(true);
    }
}

$fileName = 'Material_Allocation_Report_' . date('d-m-Y_H-i-s') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
