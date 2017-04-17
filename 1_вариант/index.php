<?
//Подключение к БД
include ("/sql/connect.php");

/*
//Вывод списка всех таблиц
$get_t_list = $db_conn->query("SELECT tablename FROM pg_tables WHERE tableowner='logging' ORDER BY tablename;");
while ($row = $get_t_list->fetch())
{
    echo $row['tablename'] . " ";
}
*/

$_POST['date-from'];	//Получим дату с
$_POST['date-to'];		///Получим дата до

/*//вывод таблицы за сегодняшнее число
$d_day = date('d');								//Получим текущий день
$d_month = mb_strtolower(date('F'), 'UTF-8');	//Получим текущий месяц, с маленькой буквы
$d_year = date('Y');							//Получим текущий год
$t_name = "logdata_{$d_year}_{$d_month}_{$d_day}";	//Получим имя таблицы за сегодня
//Извлечем данные из таблицы за сегодняшнее число
$get_t_data = $db_conn->query("SELECT * FROM logdata_{$d_year}_{$d_month}_{$d_day} ORDER BY time DESC LIMIT 1000");
while ($row = $get_t_data->fetch())
//Запишем все в ячейки таблицы
{
	$table .= "<tr><td>{$row[time]}</td><td>{$row[proto_l4]}</td><td>{$row[orig_src_ipv4]}</td>
	<td>{$row[inside_vrf_name]}</td><td>{$row[trnsl_src_ip]}</td><td>{$row[orig_port]}</td>
	<td>{$row[trnsl_frst_src_port]}</td><td>{$row[dst_ip]}</td><td>{$row[dst_port]}</td>";
}*/

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
					<form action="#" autocomplete="on" method="POST" name="find-form">
					<p id="p-header">Полнофункциональный поиск:</p>
					<p>Приватный IP адрес:</p>
						<input autocomplete="on" maxlength="15" name="prvt-ip" type="text">

					<p>IP адрес трансляции:</p>
						<input autocomplete="on" maxlength="15" name="tr-ip" type="text">

					<p>IP адрес назначения:</p>
						<input autocomplete="on" maxlength="15" name="dest-ip" type="text">

					<p>Оригинальный порт:</p>
						<input autocomplete="on" maxlength="5" name="orig-port" type="text">

					<p>Порт трансляции:</p>
						<input autocomplete="on" maxlength="5" name="tr-port" type="text">

					<p>Порт назначения:</p>
						<input autocomplete="on" maxlength="5" name="dest-port" type="text">

					<p>Дата c:</p>
						<input type="date" name="date-from" min="2015-10-01" max="2099-01-01" 
						value="<?php echo date('Y-m-d'); ?>" />
					<p>Время с:</p>
						<input type="text" id="time-from" name="time-from" placeholder="00:00:00">					

					<p>Дата до:</p>
						<input type="date" name="date-to" min="2015-10-01" max="2099-01-01"
						value="<?php echo date('Y-m-d'); ?>" />
					<p>Время до:</p>
						<input type="text" id="time-to" name="time-to" placeholder="23:59:59">
										

					<p>Получить статистику:</p>
						<input name="submit-btn" type="submit" value="Найти">

					<p>Сбросить введенные данные:</p>
						<input name="reset-btn" type="reset" value="Очистить">
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