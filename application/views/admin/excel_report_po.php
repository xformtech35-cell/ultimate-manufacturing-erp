<?php

// include PHPExcel
require( APPPATH . '/third_party/Classes/PHPExcel.php');

// create new PHPExcel object
$objPHPExcel = new PHPExcel;
// set default font
$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// set default font size
$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
// create the writer
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

/**
 * Define currency and number format.
 */
// currency format, € with < 0 being in red color
$currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
// number format, with thousands separator and two decimal points.
$numberFormat = '#,#0.##;[Red]-#,#0.##';

// writer already created the first sheet for us, let's get it
$objSheet = $objPHPExcel->getActiveSheet();
// rename the sheet
$objSheet->setTitle('Excel report');

// let's bold and size the header font and write the header
// as you can see, we can specify a range of cells, like here: cells from A1 to A4
$objSheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(16);
$objPHPExcel->getActiveSheet()
        ->getStyle('A1:F1')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("e8f442");

$styleArray = array(
    'font' => array(
        'bold' => true,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E4')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F4')->applyFromArray($styleArray);

$objPHPExcel->setActiveSheetIndex(0);
// write header
$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');

$objPHPExcel->getActiveSheet()->getCell('A1')->setValue('GSTR3B Report')->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Year.');
$objPHPExcel->getActiveSheet()->SetCellValue('B2', $month);


$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Month.');
$objPHPExcel->getActiveSheet()->SetCellValue('B3', $year);

$objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Nature of Supplies.');
$objPHPExcel->getActiveSheet()->SetCellValue('B4', 'Total Taxable Value');
$objPHPExcel->getActiveSheet()->SetCellValue('C4', 'Integrated Tax');
$objPHPExcel->getActiveSheet()->SetCellValue('D4', 'Central Tax');
$objPHPExcel->getActiveSheet()->SetCellValue('E4', 'State/UT Tax');
$objPHPExcel->getActiveSheet()->SetCellValue('F4', 'Cess');


$rowCount = 5;
$rowCou = 1;

$objPHPExcel->getActiveSheet()->SetCellValue('A5', '(a) Outward taxable supplies (other than zero rated, nil rated and exempted');
$objPHPExcel->getActiveSheet()->SetCellValue('A6', '(b) Outward taxable supplies (zero rated)');
$objPHPExcel->getActiveSheet()->SetCellValue('A7', '(c) Other outward supplies (nil rated, exempted)');
$objPHPExcel->getActiveSheet()->SetCellValue('A8', '(d) Inward supplies (liable to reverse charge)');
$objPHPExcel->getActiveSheet()->SetCellValue('A9', '(e) Non-GST outward supplies');


foreach ($result as $row) {
    
    $fr  = $row->taxable;


    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $row->taxable);
     

    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $row->integrated_tax);
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $row->central_tax);
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $row->state_tax);

    //$objPHPExcel->getActiveSheet()->SetCellValue('W' . $rowCount, str_replace(".xls", "", $str));
   
$rowCou++;
    $rowCount++;
}
$objPHPExcel->getActiveSheet()->SetCellValue('B6', $fr);
 
// autosize the columns
$objSheet->getColumnDimension('A')->setAutoSize(true);
$objSheet->getColumnDimension('B')->setAutoSize(true);
$objSheet->getColumnDimension('C')->setAutoSize(true);
$objSheet->getColumnDimension('D')->setAutoSize(true);
$objSheet->getColumnDimension('E')->setAutoSize(true);
$objSheet->getColumnDimension('F')->setAutoSize(true);


// write the file to excel
// Redirect output to a client’s web browser (Excel2007)

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Purchase Order Report.xlsx"');
$objWriter->save('php://output');
exit;
