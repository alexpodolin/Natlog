<?php
if (isset($_GET['print']))
{
	header("Content-Type: application/force-download");
	header("Content-Type: charset=UTF-8");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=logs.xls"); 
	header("Content-Transfer-Encoding: binary");
}

//Подключение к БД postgresql
include ("sql/connect.php");

//Функция Работа с postgresql
include ("inc/functions.php");

//Подключение к БД oracle (suremts)
include ("sql/ora_connect.php");

//вывод таблицы за сегодняшнее число
//$d_day = date('d');									//Получим текущий день
//$d_month = mb_strtolower(date('F'), 'UTF-8');			//Получим текущий месяц, с маленькой буквы
//$d_year = date('Y');									//Получим текущий год
//$t_name = "logdata_{$d_year}_{$d_month}_{$d_day}";	//Получим имя таблицы за сегодня

//Сохранение в файл
if (isset($_GET['print']))
{

	$var_fields = array("Время", "Протокол", "Приватный адрес", "VRF-источник", "Адрес трансляции", "Оригинальный порт", "Порт трансляции", "Адрес назначения", "Порт назначения");

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
	               foreach ($xls_data_arr as $key2 => $xls_data) {
	                               $value_to_xls = iconv('utf-8','cp1251',$xls_data);
	                               xlsWriteLabel($j,$i,$value_to_xls);
	                               $i++;
	               	}
	}
	//заканчиваем собирать
	xlsEOF();

	die();
}

//Действия при нажатии кнопки
if ($_GET["submit-btn"] == "Найти")
{	
	$table = get_gentable();	
}

foreach ($_GET as $key => $value) {
	if (is_array($value))
	{
		foreach ($value as $key1 => $value1) 
		{
			$href_arr[] = "{$key1}={$value1}";
		}
	}
	else 
	$href_arr[] = "{$key}={$value}";
}

if ($href_arr)
{
	$href = "?".implode("&", $href_arr);
}

//Завершим подключение к БД
$db_conn = null;
?>

<!DOCTYPE HTML>
<html lang=en>
	<head>
		<meta charset="utf-8">
		<title>Просмотр статистики логов СПБ ГУП АТС СМОЛЬНОГО</title>
		<link rel="shortcut icon" href="biological-safety-16-57073.png" type="image/png">
		<link href="/css/main.css" media="screen" type="text/css" rel="stylesheet">
		<link href="/css/print.css" media="print" type="text/css" rel="stylesheet">
		<link href="/css/jquery-ui.css" type="text/css" rel="stylesheet">		
		<link charset="utf-8" href="css/font.css" type="text/css" rel="stylesheet">
		<script type="text/javascript" src="/jquery/jquery-2.2.0.min.js"></script>
		<script type="text/javascript" src="/jquery/jquery.maskedinput.js"></script>
		<script type="text/javascript" src="/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="/js/js.js"></script>
	</head>

	<body>
		<div class="wrapper">			

			<div class="header">
			</div>

			<div class="main">
				<div class="left-menu">
					<form action="#" autocomplete="on" method="GET" name="find-form">

					<!--<h2>Количество найденных записей&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</h2>
					<label><input type="radio" name="limit" value="100" checked>100</label>
					<label><input type="radio" name="limit" value="500">500</label>
					<label><input type="radio" name="limit" value="1000">1000</label>
					<label><input type="radio" name="limit" value="9223372036854775807">Все	</label>-->

					<p id="p-header">Полнофункциональный поиск:</p>
					<p>Приватный IP адрес:</p>
						<input id="tags" autocomplete="on" maxlength="15" name="where[original_source_ip]" type="text" value="<?=$_GET[where][original_source_ip]?>">

					<p>IP адрес трансляции:</p>
						<input autocomplete="on" maxlength="15" name="where[translated_source_ip]" type="text" value="<?=$_GET[where][translated_source_ip]?>">

					<p>IP адрес назначения:</p>
						<input autocomplete="on" maxlength="15" name="where[destination_ip]" type="text" value="<?=$_GET[where][destination_ip]?>">

					<p>Оригинальный порт:</p>
						<input autocomplete="on" maxlength="5" name="where[original_port]" type="text" value="<?=$_GET[where][original_port]?>">

					<p>Порт трансляции:</p>
						<input autocomplete="on" maxlength="5" name="where[translated_first_source_port]" type="text" value="<?=$_GET[where][translated_first_source_port]?>">

					<p>Порт назначения:</p>
						<input autocomplete="on" maxlength="5" name="where[destination_port]" type="text"value="<?=$_GET[where][destination_port]?>" >

					<p>Дата:</p>
						<input type="date" name="date" min="2015-10-01" max="2099-01-01" 
						value="<?php echo date('Y-m-d'); ?>" />

					<!--<p>Время:</p>
						<input type="text" id="time" name="where[time]" placeholder="00:00:00">-->
					<p>Интервал времени в часах:</p>
					<?
						echo '<select name="hour">';
						foreach (range(0,23) as $number) 
						{
							$j = ($number != 23) ? $number + 1 : '00';
							$number = (strlen($number) <2 ) ? "0{$number}" : "$number";
							$j = (strlen($j) <2 ) ? "0{$j}" : "$j";
						    echo '<option value='.$number.'>'.$number.' - '.$j.'</option>';
						}
						echo '</select>';
					?>

					<p>Интервал времени в минутах:</p>
					<?
						echo '<select name="minute">';
						//echo '<option value="">00 - 59</option>';
						foreach (range(0,55,5) as $number) 
						{
							$j = ($number != 55) ? $number + 5 : '59';
							$number = (strlen($number) <2 ) ? "0{$number}" : "$number";
							$j = (strlen($j) <2 ) ? "0{$j}" : "$j";
						    echo '<option value='.$number.'>'.$number.' - '.$j.'</option>';
						}
						echo '</select>';
					?>

					<p>Получить статистику:</p>
						<input name="submit-btn" type="submit" value="Найти">

					<!--<p>Сбросить введенные данные:</p>
						<input name="reset-btn" type="reset" value="Очистить" onclick="resetForm('find-form');">-->
					</form>						
				</div>				

				<div class="result">					
					<table id="result-to-xlsx">							
						<tr class="table-header">												
							<td>Время сесси</td>
							<td>Протокол</td>
							<td>Приватный адрес</td>
							<td>VRF-источник</td>
							<td>Адрес трансляции</td>
							<td>Оригинальный порт</td>							
							<td>Порт трансляции</td>
							<td>Адрес назначения</td>
							<td>Порт назначения</td>							
						</tr>
						<?=$table[html]?>
					</table>
					<h2><?=$table[error]?></h2>
				</div>

				<!--Выведем ссылки после нажатия кнопки найти-->
				<?
				$links = "<div class='icon'>
							<a href='' target='_blank' onclick='window.print()'><img src='/img/printer16x16.png' alt='Версия для печати' title='Версия для печати'>&nbsp;Версия для печати</a>
							<br>
							<!--<a href='{$href}&print'><img src='/img/file_xls.png' alt='Сохранить в файл' title='Сохранить в файл'>&nbsp;Сохранить в файл</a>-->
						  </div>";				
				?>	
				<?
				if ($_GET["submit-btn"] == "Найти")
					{	
						echo $links;							
					}
				?>			
			</div>	
		</div>		
    </body> 
</html>