<?php

include_once("ajax_head.inc");

$sql_get_data = $db_conn->query("SELECT * FROM vrf_name WHERE short_vrf_name='{$_GET[vrf]}'");
$row = $sql_get_data->fetch();
$vrf_id = $row[argus_id_vrf_name];
$full_vrf_name = $row[full_vrf_name];

$company_details = get_company_details($_GET[ip], $vrf_id);
if ($company_details && count($company_details) > 0) {
	echo "
		<h2>Данные по услуге</h2>
		<p>IP-адрес: {$_GET[ip]}</p>
		<p>Организация: {$company_details[ORG_NAME]}</p>
		<p>ИНН: {$company_details[INN]}</p>
		<p>Адрес: {$company_details[ADDRESS]}</p>
	";
} else {
	echo "<h2>По запрашиваемоу IP-адресу данных нет</h2>";
	$e_body = "
		<h3>По запрашиваемоу IP-адресу не удалось найти данных</h3>
		<p>IP — {$_GET[ip]}</p>
		<p>VRF — {$full_vrf_name}</p>
	";
	sendmail('a.berezin@sats.spb.ru', 'Не удалось найти организацию по IP', $e_body);
}

?>