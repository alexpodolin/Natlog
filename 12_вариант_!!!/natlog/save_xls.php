<?php

header("Content-Type: application/force-download");
header("Content-Type: charset=UTF-8");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=logs.xls"); 
header("Content-Transfer-Encoding: binary");

include ("sql/connect.php");
include ("inc/functions.php");

//Сохранение в файл
$var_fields = array('datetime_log' => "Время", 'l4' => "Протокол", 'original_source_ip' => "Приватный адрес", 'translated_source_ip' => "Адрес трансляции", 'original_port' => "Оригинальный порт", 'translated_first_source_port' => "Порт трансляции", 'destination_ip' => "Адрес назначения", 'destination_port' => "Порт назначения");

//Начало заголовка файлаs
function xlsBOF() {
			   echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); 
}
//Начало конца файла
function xlsEOF() {
			   echo pack("ss", 0x0A, 0x00);
}
//Запись номера в ячейки и колонки файла
function xlsWriteNumber($row, $col, $value) {
			   echo pack("sssss", 0x203, 14, $row, $col, 0x0);
			   echo pack("d", $value);
}
//Запись текста в ячейки и колонки файла
function xlsWriteLabel($row, $col, $value ) {
			   $L = strlen($value);
			   echo pack("ssssss", 0x204, 8 + $L, $row, $col, 0x0, $L);
			   echo $value;
}

$xls_line_tmp = get_gentable();
$xls_line = $xls_line_tmp[arr];

//начинаем собирать файл
//столбец
xlsBOF();
$i = 0;
foreach ($var_fields as $key => $value) {
			   $value_to_xls = iconv('utf-8','cp1251',$value);	               
			   xlsWriteLabel(0,$i,$value_to_xls);
			   $i++;	               
}
//строка
$j = 0;
foreach ($xls_line as $key => $xls_data_arr) {
	$i = 0; $j++;
	foreach ($var_fields as $index => $xls_data) {
		$value_to_xls = iconv('utf-8','cp1251',$xls_data_arr[$index]);
		xlsWriteLabel($j,$i,$value_to_xls);
		$i++;
	}
}
//заканчиваем собирать
xlsEOF();

?>