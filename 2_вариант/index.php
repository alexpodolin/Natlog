<?
//Подключение к БД
include ("/sql/connect.php");

//вывод таблицы за сегодняшнее число
$d_day = date('d');								//Получим текущий день
$d_month = mb_strtolower(date('F'), 'UTF-8');	//Получим текущий месяц, с маленькой буквы
$d_year = date('Y');							//Получим текущий год
$t_name = "logdata_{$d_year}_{$d_month}_{$d_day}";	//Получим имя таблицы за сегодня

//Форма поиска по параметрам при нажатии кнопки "Найти"
//Если форма не заполнена, то по умолчанию выводит 100 последних записей за сегодня
if ($_GET["submit-btn"] == "Найти")
{
	if ($_GET["where"]) 
	{
		foreach ($_GET["where"] as $key => $value)
		{
			$value = trim($value);	//trim убрать пробелы по краям
			if ($value)
			{
				$where .= " AND {$key} = '{$value}'";
			}
		}		   
	}

/*	$new_date_from = strtotime($_GET['date-from']);		//convert to unix timestamp
	$new_date_to = strtotime($_GET['date-to']);			//convert to unix timestamp

	//поменяем даты местами если "дата с" больше "дата до"
	if ($new_date_from > $new_date_to) 
	{
		$a = $new_date_to;
		$new_date_to = $new_date_from;
		$new_date_from = $a;
	}

	//список всех таблиц между "дата с" "дата до" 
	if ($new_date_from != $new_date_to)
	{
		for ($i = $new_date_from; $i <= $new_date_to; ($i+=86400))
		{
			$d_day = date('d', $i);									//Получим диапазон дней
			$d_month = mb_strtolower(date('F', $i), 'UTF-8');		//Получим диапазон месяцев, с маленькой буквы
			$d_year = date('Y', $i);								//Получим диапазон год
//			$t_name = "logdata_{$d_year}_{$d_month}_{$d_day}" . " ";	//Получим диапазон имен таблиц		
			$sql_arr_get_data[] = "(SELECT * FROM logdata_{$d_year}_{$d_month}_{$d_day}  
									WHERE true {$where} ORDER BY time DESC)";	//Создадим массив из запросов
		}
	}
		$sql_get_data = implode(" UNION ", $sql_arr_get_data);
		echo $sql_get_data;*/

//Если форма не заполнена, то по умолчанию выводит 100 последних записей за сегодня
	$sql_get_data = $db_conn->query("SELECT * FROM logdata_{$d_year}_{$d_month}_{$d_day} 
									WHERE true {$where} ORDER BY time DESC");
 	while ($row = $sql_get_data->fetch()) 
 	{	
		$table .= "<tr><td>{$row[time]}</td><td>{$row[proto_l4]}</td><td>{$row[orig_src_ipv4]}</td>
		<td>{$row[inside_vrf_name]}</td><td>{$row[trnsl_src_ip]}</td><td>{$row[orig_port]}</td>
		<td>{$row[trnsl_frst_src_port]}</td><td>{$row[dst_ip]}</td><td>{$row[dst_port]}</td></tr>";
 	}
}

//Завершим подключение к БД
$db_conn = null;
?>


<!DOCTYPE HTML>
<html lang=en>
	<head>
		<meta charset="utf-8">
		<title>Просмотр статистики логов СПБ ГУП АТС СМОЛЬНОГО</title>
		<link href="/css/main.css" type="text/css" rel="stylesheet">			
		<link href="/images/favicon.ico" type="image/x-icon" rel="icon">
		<link charset="utf-8" href="css/font.css" type="text/css" rel="stylesheet">
	</head>

	<body>
		<div class="wrapper">	

			<div class="header">
				<div class=form>				
					<form>
						<input autocomplete="on" maxlength="15" placeholder="Поиск по любому параметру" type="search">
					</form>
				</div>

				<h2>Количество найденных записей&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</h2>
				<label><input type="radio" name="limit" value="10"></label>10
				<label><input type="radio" name="limit" value="100"></label>100
				<label><input type="radio" name="limit" value="1000"></label>1000
				<label><input type="radio" name="limit" value="Все" checked></label>Все	
			</div>

			<div class="main">
				<div class="left-menu">
						<form action="#" autocomplete="on" method="GET" name="find-form">
						<p id="p-header">Полнофункциональный поиск:</p>
						<p>Приватный IP адрес:</p>
							<input autocomplete="on" maxlength="15" name="where[orig_src_ipv4]" type="text">

						<p>IP адрес трансляции:</p>
							<input autocomplete="on" maxlength="15" name="where[trnsl_src_ip]" type="text">

						<p>IP адрес назначения:</p>
							<input autocomplete="on" maxlength="15" name="where[dst_ip]" type="text">

						<p>Оригинальный порт:</p>
							<input autocomplete="on" maxlength="5" name="where[orig_port]" type="text">

						<p>Порт трансляции:</p>
							<input autocomplete="on" maxlength="5" name="where[trnsl_frst_src_port]" type="text">

						<p>Порт назначения:</p>
							<input autocomplete="on" maxlength="5" name="where[dst_port]" type="text">

						<p>Дата:</p>
							<input type="date" name="date" min="2015-10-01" max="2099-01-01" 
							value="<?php echo date('Y-m-d'); ?>" />
						<p>Время:</p>
							<input type="text" id="time" name="time" placeholder="00:00:00">					

						<p>Получить статистику:</p>
							<input name="submit-btn" type="submit" value="Найти">

						<p>Сбросить введенные данные:</p>
							<input name="reset-btn" type="reset" value="Очистить" onclick="reset();">
						</form>
				</div>

				<div class="result">
					<table>							
						<tr class="table-header">												
							<td>Время</td>
							<td>Протокол</td>
							<td>Приватный адрес</td>
							<td>VRF-источник</td>
							<td>Адрес трансляции</td>
							<td>Оригинальный порт</td>							
							<td>Порт трансляции</td>
							<td>Адрес назначения</td>
							<td>Порт назначения</td>							
						</tr>
						<?=$table?>
					</table>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="/js/js.js"></script>
		
    </body> 
</html>