<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');
ini_set('max_input_vars', 9000);

include("../class/classDb.php");
include("../class/classProductManage.php");	

$product = new productMan();

$productsArray = json_decode($_POST['array']);
$brand = $_POST['brand'];

//echo "in excel file<br/>";

require_once dirname(__FILE__) . '/../class/Excel/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$cellArray = array("D","E","G","H","J","K","M","N","P","Q");

$objPHPExcel->getProperties()->setCreator("Robert Kocjan")
							 ->setLastModifiedBy("Robert Kocjan")
							 ->setTitle("Margin raport")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");
//FORMAT_PERCENTAGE_00
                             
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Product Name')
			->setCellValue('B1', 'Pack Size')
			->setCellValue('C1', 'Old Retail')
            ->setCellValue('D1', 'Supplier Cost')
            ->setCellValue('E1', 'Old Margin')
            ->setCellValue('F1', 'New retail')
            ->setCellValue('G1', 'New margin')
            ->setCellValue('H1', 'Barcode')
            ->setCellValue('I1', 'Tax');
    
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    $columnWidth = 10;
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth($columnWidth-5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth($columnWidth);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth($columnWidth);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth($columnWidth);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth($columnWidth-2);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth($columnWidth);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth($columnWidth+5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth($columnWidth-5);
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setWrapText(TRUE);

    $cellNo = 2;

    foreach($productsArray as $key => $value){
        //echo '<br/>'.$cellNo.') '.$key.'  -  '.$value->name.'  -  '.$value->salePrice;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$cellNo, $product->nameChange($value->name,'get'));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$cellNo, $value->pack);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$cellNo, $value->salePrice);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$cellNo, $value->supCost);
        $oldMargin = margin($value->salePrice,$value->supCost,$value->Tax);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$cellNo, $oldMargin);//old margin
            
            if ($oldMargin < 0.50){
                    $objPHPExcel->getActiveSheet()
                        ->getStyle('E'.$cellNo)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFd6d6d6');

                    $objPHPExcel->getActiveSheet()->getStyle('E'.$cellNo)->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
            }
            
        $objPHPExcel->getActiveSheet()->getStyle('E'.$cellNo)->getNumberFormat()->setFormatCode('0.00%');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$cellNo, '');//new retail
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$cellNo, marginFormula($cellNo));//new margin
        $objPHPExcel->getActiveSheet()->getStyle('G'.$cellNo)->getNumberFormat()->setFormatCode('0.00%');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$cellNo, $value->ean);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$cellNo, $value->Tax);
        $cellNo++;
    }
    
    /*Formating*/
    
    /***********/
 
 if(count($productsArray) > 0){   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
    $fileName = $brand.' - Margin raport '.date("Ymd");
    $fileName = str_replace(" ","_",$fileName).'.xlsx';
    
    
    
    
    $objWriter->save('../files/'.$fileName);
    
    
  
    $pathToFile = dirname(pathinfo(__FILE__)['dirname']).'\\files\\'.$fileName;
	
	
	$directory = explode("\\",dirname(dirname(__FILE__)));
		

	if (file_exists($pathToFile)){
		//echo "Click to download <a href = '/raport_v2/files/".$fileName."'>".$fileName."</a>";
			$show = "<br/><div class='row'>";
			$show .= "<div class='col-xs-12 col-12'>";
				$show .= "<a href = '/".$directory[count($directory)-1]."/files/".$fileName."'  class='btn btn-primary'><i class='fa fa-download' aria-hidden='true'></i>  Download <b>".$fileName."</b></a>";
				#$show .= "<a href = '".dirname(__DIR__).'\\files\\'.$fileName."'  class='btn btn-primary'><i class='fa fa-download' aria-hidden='true'></i>  Download <b>".$fileName."</b></a>";
			$show .= "</div>";
		$show .= "</div><br/>";
	}else{
		echo "Ups.. something went wrong and file wasn't created. Contact Robert.";    
	}
}else{
		$show = "<br/><div class='row'>";
			$show .= "<div class='col-xs-12 col-12'>";
				$show .= "No results found";
			$show .= "</div>";
		$show .= "</div><br/>";
	}
	echo $show;
    function margin($retail,$cost,$tax){
        if($tax == '0'){
            return ($retail-$cost)/$retail;
        }else{
            $taxVar = ($tax/100)+1;
            return (($retail/$taxVar)-$cost)/($retail/$taxVar);
        }
    }
    
    function marginFormula($cellNumber){
        $retail = 'F'.$cellNumber;
        $tax = '((I'.$cellNumber.'/100)+1)';
        $cost = 'D'.$cellNumber;

        return '=if('.$retail.' = "","",(('.$retail.'/'.$tax.')-'.$cost.')/('.$retail.'/'.$tax.'))';
    }
    
    function cellFormat($cell){
        $objPHPExcel->getActiveSheet()
            ->getStyle($cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFd6d6d6');

        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
    }
?>