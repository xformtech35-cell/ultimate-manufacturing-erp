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
$objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(16);
$objPHPExcel->getActiveSheet()
        ->getStyle('A1:D1')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("31a3dd");
$objSheet->getStyle('A2:D2')->getFont()->setBold(true)->setSize(14);
$objPHPExcel->getActiveSheet()
        ->getStyle('A2:D2')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("FFFFFF");

$objSheet->getStyle('A3:D3')->getFont()->setBold(true)->setSize(10);
$objPHPExcel->getActiveSheet()
        ->getStyle('A3:D3')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("FFFFFF");

$objSheet->getStyle('A4:D4')->getFont()->setBold(true)->setSize(10);
$objPHPExcel->getActiveSheet()
        ->getStyle('A4:D4')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("FFFFFF");

$objSheet->getStyle('A5:D5')->getFont()->setBold(true)->setSize(10);
$objPHPExcel->getActiveSheet()
        ->getStyle('A5:D5')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("FFFFFF");


$styleArray = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => '000000'),
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
$styleArray1 = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => 'FFFFFF'),
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

$styleArray2 = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => '000000'),
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

$styleArray3 = array(
    'font' => array(
        'bold' => FALSE,
        'color' => array('rgb' => '000000'),
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    ),
    'borders' => array(
        
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleArray3);
$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($styleArray3);
$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($styleArray3);
$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($styleArray3);
$objPHPExcel->getActiveSheet()->getStyle('A6:F6')->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getStyle('A7:F7')->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->applyFromArray($styleArray2);
//$objPHPExcel->getActiveSheet()->getStyle('A15:C15')->applyFromArray($styleArray2);
//$objPHPExcel->getActiveSheet()->getStyle('A10:C10')->applyFromArray($styleArray2);
//$objPHPExcel->getActiveSheet()->getStyle('D10:F10')->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getStyle('F6:F8')->applyFromArray($styleArray2);
//$objPHPExcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleArray2);
//$objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);


$objPHPExcel->setActiveSheetIndex(0);
// write header
$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
$objPHPExcel->getActiveSheet()->mergeCells('A4:F4');
$objPHPExcel->getActiveSheet()->mergeCells('A5:F5');
$objPHPExcel->getActiveSheet()->mergeCells('A6:A7');
$objPHPExcel->getActiveSheet()->mergeCells('B6:B7');
$objPHPExcel->getActiveSheet()->mergeCells('C6:C7');
$objPHPExcel->getActiveSheet()->mergeCells('D6:D7');
$objPHPExcel->getActiveSheet()->mergeCells('E6:E7');
$objPHPExcel->getActiveSheet()->mergeCells('F6:F7');
$objPHPExcel->getActiveSheet()->getCell('A1')->setValue('Balance Sheet Report')->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A2')->setValue($settings['company_name'])->getStyle('A2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A3')->setValue('CIN:' .$settings['cin'])->getStyle('A3:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A4')->setValue('Tentative Balance Sheet')->getStyle('A4:D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A5')->setValue($from_date . " " . 'to' . " " . $to_date)->getStyle('A5:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A6')->setValue('Liabilities')->getStyle('A6:A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('C6')->setValue($from_date . " "  . 'to' . " "  . $to_date)->getStyle('C6:C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('D6')->setValue('Assets')->getStyle('D6:D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('F6')->setValue( $from_date . " " . 'to' . " "  . $to_date)->getStyle('F6:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$objPHPExcel->getActiveSheet()->SetCellValue('A8', 'Current Liabilities');
$objPHPExcel->getActiveSheet()->SetCellValue('B9', 'Sundry Creditors');
//$objPHPExcel->getActiveSheet()->SetCellValue('C8');
//$objPHPExcel->getActiveSheet()->SetCellValue('D8', 'Fixed Assets');
//$objPHPExcel->getActiveSheet()->SetCellValue('F8', "2321");

$rowCount = 9;

foreach ($balance_report as $row) {
    
   
        $objPHPExcel->getActiveSheet()->SetCellValue('B'. $rowCount, $row->liabilities);
       

  $rowCount++;
}




$rowCount = 13;

foreach ($subliabilities_name_excel as $row) {
    
   
        $objPHPExcel->getActiveSheet()->SetCellValue('A'. $rowCount, $row->liabilities);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'. $rowCount, $row->subliabilities_name);
        

  $rowCount++;
}

$objPHPExcel->getActiveSheet()->SetCellValue('A15', 'Loans(Liability)');
//$rowCount = $rowCount + 1;
foreach ($balance_report['expense_liabilities_data'] as $row) {
    
    if (strpos($row->liabilities, 'Loans') !== false) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A'. $rowCount, $row->sub_liabilities);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'. $rowCount, $row->sub_liabilities_total);
}

  $rowCount++;
}

//$objPHPExcel->getActiveSheet()->SetCellValue('A13', 'Loans(Liability)');
//$rowCount = $rowCount + 1;
foreach ($balance_report['expense_liabilities_data'] as $row) {
    
    if (strpos($row->liabilities, 'Current') !== false) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A'. $rowCount, $row->sub_liabilities);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'. $rowCount, $row->sub_liabilities_total);
}

  $rowCount++;
}



$objSheet->getStyle('C8')->getFont()->setBold(true)->setSize(8);

$objPHPExcel->getActiveSheet()
    ->setCellValue(
        'C8',
        '=SUM(B12:B13)'
    );




// autosize the columns
$objSheet->getColumnDimension('A')->setAutoSize(true);
$objSheet->getColumnDimension('B')->setAutoSize(true);
$objSheet->getColumnDimension('C')->setAutoSize(true);
$objSheet->getColumnDimension('D')->setAutoSize(true);
$objSheet->getColumnDimension('E')->setAutoSize(true);
$objSheet->getColumnDimension('F')->setAutoSize(true);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$file_name = ' Balance Sheet Report ' . $from_date . '' . $to_date. '.xlsx';
header('Content-Disposition: attachment;filename="'. $file_name .'"');
$objWriter->save('php://output');
exit;
