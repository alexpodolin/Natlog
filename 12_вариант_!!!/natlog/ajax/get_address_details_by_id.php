<?php

include_once("ajax_head.inc");

$company_info = get_company_info($_GET[id]);
$company_services = get_company_services($_GET[id]);

echo "
	<h2>Подробная информация об организации</h2>
	<p>Полное название: {$company_info[ORG_NAME]}</p>
	<p>Краткое название: {$company_info[SHORT_NAME]}</p>
	<p>ИНН: {$company_info[INN]}</p>
";

if (count($company_services) > 0) {
	echo "<h3>Предоставляемые услуги</h3>";
	echo "<div class='table'>";
	echo "<div class='head'>
		<div>Название услуги</div>
		<div>Тип услуги</div>
		<div>Подсеть</div>
		<div>Адрес предоставления услуги</div>
	</div>";
	foreach ($company_services as $key => $service_arr) {
		echo "<div>
			<div>{$service_arr[service_name]}</div>
			<div>{$service_arr[service_type_name]}</div>
			<div>{$service_arr[ip_subnet]}</div>
			<div>{$service_arr[building]}</div>
		</div>";
	}
	echo "</div><br><br>";
}

?>