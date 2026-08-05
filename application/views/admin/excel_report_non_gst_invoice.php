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
$objSheet->getStyle('A1:L1')->getFont()->setBold(true)->setSize(16);
$objPHPExcel->getActiveSheet()
        ->getStyle('A1:L1')
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

$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($styleArray);
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
$objPHPExcel->getActiveSheet()->getStyle('K2')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('L2')->applyFromArray($styleArray);

$objPHPExcel->setActiveSheetIndex(0);
// write header
$objPHPExcel->getActiveSheet()->mergeCells('A1:L1');

$objPHPExcel->getActiveSheet()->getCell('A1')->setValue('Non GST Invoice Report')->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Sr.No.');
$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Invoice No');
$objPHPExcel->getActiveSheet()->SetCellValue('C2', 'Invoice Date');
$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'Invoice Amount');
$objPHPExcel->getActiveSheet()->SetCellValue('E2', 'Invoice Balance');
$objPHPExcel->getActiveSheet()->SetCellValue('F2', 'Payment Due Date');
$objPHPExcel->getActiveSheet()->SetCellValue('G2', 'Company Name');
$objPHPExcel->getActiveSheet()->SetCellValue('H2', 'Customer Name');
$objPHPExcel->getActiveSheet()->SetCellValue('I2', 'Pan Card');
$objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Email');
$objPHPExcel->getActiveSheet()->SetCellValue('K2', 'Mobile');
$objPHPExcel->getActiveSheet()->SetCellValue('L2', 'Address');

$rowCount = 3;
$rowCou = 1;
foreach ($result as $row) {
    
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $rowCou);
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $row->invoice_number);
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, date("d-m-Y",strtotime($row->invoice_date)));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $row->total);
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $row->balance);
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $row->payment_due_date);
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $row->company_name);
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $row->fullname);
    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $row->pancard);
    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $row->email);
    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $row->mobile);
    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $row->address);

    
$rowCou++;
    $rowCount++;
}

//for total non gst invoice amount
$lTotalVar = 'C' . $rowCount;

$kTotalVar = 'D' . $rowCount;

$eTotalVar = 'E' . $rowCount;

$makeBOLDVar = $lTotalVar;

$objSheet->getStyle($makeBOLDVar)->getFont()->setBold(true)->setSize(14);

$objPHPExcel->getActiveSheet()
        ->getStyle($makeBOLDVar)
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("e8f442");

$objSheet->getStyle($kTotalVar)->getFont()->setBold(true)->setSize(14);

$objPHPExcel->getActiveSheet()
        ->getStyle($kTotalVar)
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("e8f442");


$objSheet->getStyle($eTotalVar)->getFont()->setBold(true)->setSize(14);

$objPHPExcel->getActiveSheet()
        ->getStyle($eTotalVar)
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB("e8f442");
 
 $objPHPExcel->getActiveSheet()->setCellValue($lTotalVar, 'Total:');
 $objPHPExcel->getActiveSheet()->setCellValue($kTotalVar, 'Total:');
 
 $rowc =  $rowCount-1;
 $iTotalPOamt = "=SUM(D2:D". $rowc . ")";
 $iTotalVar = 'D' . $rowCount;
 $objPHPExcel->getActiveSheet()->setCellValue($iTotalVar, $iTotalPOamt);
 
 
  $iTotalbalamt = "=SUM(E2:E". $rowc . ")";
 $iTotalBalVar = 'E' . $rowCount;
 $objPHPExcel->getActiveSheet()->setCellValue($iTotalBalVar, $iTotalbalamt);
 
 
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
$objSheet->getColumnDimension('K')->setAutoSize(true);
$objSheet->getColumnDimension('L')->setAutoSize(true);


// write the file to excel
// Redirect output to a client’s web browser (Excel2007)

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Non GST Invoice Report.xlsx"');
$objWriter->save('php://output');
exit;
