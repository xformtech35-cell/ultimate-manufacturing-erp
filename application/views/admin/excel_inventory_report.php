<?php


// include PHPExcel
require( APPPATH . '/third_party/Classes/PHPExcel.php');

// create new PHPExcel object
$objPHPExcel = new PHPExcel;
// set default font
$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// set default font size
$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
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
$objSheet->getStyle('A1:J1')->getFont()->setBold(true)->setSize(22);
$objPHPExcel->getActiveSheet()
        ->getStyle('A1:J1')
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

$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('J2')->applyFromArray($styleArray);


$objPHPExcel->setActiveSheetIndex(0);
// write header
$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');

$objPHPExcel->getActiveSheet()->getCell('A1')->setValue('Inventory Report')->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Sr.No.');
$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Description');
$objPHPExcel->getActiveSheet()->SetCellValue('C2', 'Item');
$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'HSN Code');
$objPHPExcel->getActiveSheet()->SetCellValue('E2', 'GST Percentage');
$objPHPExcel->getActiveSheet()->SetCellValue('F2', 'Stock');
$objPHPExcel->getActiveSheet()->SetCellValue('G2', 'Cost Price');
$objPHPExcel->getActiveSheet()->SetCellValue('H2', 'Sell Price');
$objPHPExcel->getActiveSheet()->SetCellValue('I2', 'Added Date');
$objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Modified Date');

$rowCount = 3;
$rowCou = 1;
foreach ($result as $row) {
//    $str = str_replace("./uploads/", "", $row->qo_file_path);
//    if($row->net_amount==0 && $row->expense>0){
//        $margin = $row->total - $row->expense;
//    }else{
//        $margin = $row->net_amount - $row->expense;
//    }


    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $rowCou);
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $row->item_name);
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $row->code);
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $row->hsn);
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $row->gst_per);
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $row->stock);
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $row->cost_price);
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $row->sell_price);
    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, date("d-m-Y",strtotime($row->date_added)));
    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $row->date_modified);
 
    
    
$rowCou++;
    $rowCount++;
}


// autosize the columns
$objSheet->getColumnDimension('A')->setAutoSize(true);
$objSheet->getColumnDimension('B')->setAutoSize(true);
$objSheet->getColumnDimension('C')->setAutoSize(true);
$objSheet->getColumnDimension('D')->setAutoSize(true);
$objSheet->getColumnDimension('E')->setAutoSize(true);
$objSheet->getColumnDimension('F')->setAutoSize(true);
$objSheet->getColumnDimension('G')->setAutoSize(true);
$objSheet->getColumnDimension('H')->setAutoSize(true);
$objSheet->getColumnDimension('I')->setAutoSize(true);
$objSheet->getColumnDimension('J')->setAutoSize(true);

// write the file to excel
// Redirect output to a client’s web browser (Excel2007)

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Inventory Report.xlsx"');
$objWriter->save('php://output');
exit;
