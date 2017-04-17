<?php

$date = strtotime($_GET['date']); //convert to unix timestamp
$calendar_date = isset($_GET['date']) ? date('Y-m-d', $date) : date('Y-m-d');

//Действия при нажатии кнопки
if (isset($_GET["submit-btn"]) == "Найти")
{
	$table = get_gentable();	
}

?>

<div class="left-menu">
	<form action="#" autocomplete="on" method="GET" name="find-form">

	<p id="p-header">Полнофункциональный поиск:</p>
	<p>Приватный IP адрес:</p>
	<input id="tags" autocomplete="on" maxlength="15" name="where[original_source_ip]" type="text" value="<?=$_GET['where']['original_source_ip']?>">

	<p>IP адрес трансляции:</p>
		<input autocomplete="on" maxlength="15" name="where[translated_source_ip]" type="text" value="<?=$_GET['where']['translated_source_ip']?>">

	<p>IP адрес назначения:</p>
		<input autocomplete="on" maxlength="15" name="where[destination_ip]" type="text" value="<?=$_GET['where']['destination_ip']?>">

	<p>Оригинальный порт:</p>
		<input autocomplete="on" maxlength="5" name="where[original_port]" type="text" value="<?=$_GET['where']['original_port']?>">

	<p>Порт трансляции:</p>
		<input autocomplete="on" maxlength="5" name="where[translated_first_source_port]" type="text" value="<?=$_GET['where']['translated_first_source_port']?>">

	<p>Порт назначения:</p>
		<input autocomplete="on" maxlength="5" name="where[destination_port]" type="text" value="<?=$_GET['where']['destination_port']?>" >

	<p>Дата:</p>
		<input type="date" name="date" value="<?=$calendar_date;?>" />

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
			$selected = ($_GET['hour'] != $number) ? "" : "selected";							
			echo '<option '.$selected.' value='.$number.'>'.$number.' - '.$j.'</option>';
		}
		echo '</select>';
	?>

	<p>Интервал времени в мин.:</p>
	
	<?
	echo '<select name="minute-from">';
	foreach (range(0,59,1) as $number) 
		{
			$j = ($number != 59) ? $number + 1 : '59';
			$number = (strlen($number) <2 ) ? "0{$number}" : "$number";
			$j = (strlen($j) <2 ) ? "0{$j}" : "$j";
			$selected = ($_GET['minute-from'] != $number) ? "" : "selected";
			echo '<option '.$selected.' value='.$number.'>'.$number.'</option>';		    
		}
		echo '</select>';
	?>
	&nbsp; - &nbsp;
	<?
	echo '<select name="minute-to">';
	foreach (range(0,59,1) as $number) 
		{
			$j = ($number != 59) ? $number + 1 : '59';
			$number = (strlen($number) <2 ) ? "0{$number}" : "$number";
			$j = (strlen($j) <2 ) ? "0{$j}" : "$j";
			$selected = ($_GET['minute-to'] != $number) ? "" : "selected";
			echo '<option '.$selected.' value='.$number.'>'.$number.'</option>';		    
		}
		echo '</select>';
	?>					

	<p>Получить статистику:</p>
		<input name="submit-btn" type="submit" value="Найти">
	</form>						
</div>				

<div class="result">
	<!--Выведем ссылки после нажатия кнопки найти-->
	<?
	if (isset($_GET["submit-btn"]) == "Найти")
		{	
			$links = "<div class='icon'>
						<a href='' target='_blank' onclick='window.print()'><img src='/img/printer16x16.png' alt='Распечатать' title='Распечатать'>&nbsp;Распечатать</a>
						&nbsp;&nbsp;&nbsp;&nbsp;<a href='/save_xls.php?{$_SERVER[QUERY_STRING]}' target='_blank'><img src='/img/file_xls.png' alt='Сохранить в файл' title='Сохранить в файл'>&nbsp;Сохранить в файл</a>
					  </div>";
			echo "<div class='counter'>Общее кол-во сессий: {$table['sessions_count'] } </div>";
			echo $links;
			echo $table[html_pages] ? "<div class='pages-list'>СТР. &nbsp;&nbsp;{$table[html_pages]}{$table[html_select_pages]}</div>": "";
		}
	?>
	<table id="result-to-xlsx">							
		<tr class="table-header">												
			<td>Время сесси</td>
			<td>Протокол</td>
			<td>Приватный адрес</td>
			<!--<td>VRF-источник</td>-->
			<td>Оригинальный порт</td>
			<td>Адрес трансляции</td>
			<td>Порт трансляции</td>
			<td>Адрес назначения</td>
			<td>Порт назначения</td>						
		</tr>
		<?=$table['html_min_log'];?>
	</table><br>
	<?php
	echo $table[html_pages] ? "<div class='pages-list'>СТР. &nbsp;&nbsp;{$table[html_pages]}{$table[html_select_pages]}</div>": "";
	?>
	<h2><?=$table['error']?></h2>
</div>			
