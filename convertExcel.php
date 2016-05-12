<?php
set_time_limit(300);
require_once 'lib/PHPExcel/PHPExcel.php'; // Подключаем библиотеку PHPExcel
echo 'началась конвертация в EXCEL';

$phpexcel = new PHPExcel(); // Создаём объект PHPExcel
$page = $phpexcel->setActiveSheetIndex(0); // Делаем активной первую страницу и получаем её

//Открываем фал для чтения
$fp = file('parser.txt');

$cel = 1; //номер строки
foreach ($fp as $val){
  $str = explode(";", $val);
  $str[0] = isset($str[0]) ? substr($str[0],1,-1) : ' ';
  $page->setCellValue("A" . $cel, $str[0]);
  $str[1] = isset($str[1]) ? substr($str[1],1,-1) : ' ';
  $page->setCellValue("B" . $cel, $str[1]);
  $str[2] = isset($str[2]) ? substr($str[2],1,-1) : ' ';
  $page->setCellValue("C" . $cel, $str[2]);
  $str[3] = isset($str[3]) ? substr($str[3],1,-1) : ' ';
  $page->setCellValue("D" . $cel, $str[3]);
  $str[4] = isset($str[4]) ? substr($str[4],1,-1) : ' ';
  $page->setCellValue("E" . $cel, $str[4]);
  $cel++;
}
//Установим формат ячеек
$page->getColumnDimension('A')->setWidth(70);
$page->getColumnDimension('B')->setWidth(20);
$page->getColumnDimension('C')->setWidth(8);
$page->getColumnDimension('D')->setWidth(40);
$page->getColumnDimension('E')->setWidth(90);

$page->setTitle("shop220"); // Заголовок делаем "shop220"

//Пишем в файл
$objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
$objWriter->save("shop220.xlsx");

echo ' Закончилась конвертация в EXCEL';