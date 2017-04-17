<?php

include_once("ajax_head.inc");

$search_str_arr = explode(" ", $_GET[search_str]);
foreach ($search_str_arr as $key => $value) {
	$value = trim($value);
	if ($value) { $search_str_arr_new[] = "{$value}%"; }
}

$search_str = implode(" AND ", $search_str_arr_new);

if ($_GET[searchTypeAddress]) {
	$sql = "
	SELECT s.SERVICE_NAME, ( SELECT SERVICE_TYPE_NAME FROM  SERVICE_TYPE WHERE SERVICE_TYPE.SERVICE_TYPE_ID = s.SERVICE_TYPE_ID) SERVICE_TYPE_NAME, s.CLIENT_OWNER_ID as CLIENT_ID, ( SELECT ORG_NAME FROM  ORGANIZATION WHERE CLIENT_ID = s.CLIENT_OWNER_ID) AS CLIENT_OWNER_NAME, ( SELECT INN FROM  ORGANIZATION WHERE CLIENT_ID = s.CLIENT_OWNER_ID) AS INN, b_name address
	FROM  SERVICE_L s, INSTALLATION_L ins, NEW_BUILDING b
	WHERE   s.OBJECT_ID = SERVICE_ID AND ins.building_id = b.B_ID AND ins.OBJECT_STATUS_ID = 258 AND s.OBJECT_STATUS_ID = 258 AND CONTAINS (b_name, '{$search_str}') > 0 AND S.OBJECT_ID IN ( SELECT SO.SERVICE_ID FROM  SERVICE_OBJECT so WHERE  OBJECT_ID IN ( SELECT SUBNET_ID FROM  SUBNET s WHERE s.NETWORK_SEGMENT_ID IN (207320948, 207320966, 208190120, 207320982, 207320984, 207320988, 207320990)) AND SO.OBJECT_ENTITY_ID IN (194, 193, 27200002, 189, 30196, 30199, 30200, 30201, 30198))
	";
} elseif ($_GET[searchTypeName]) {
	$sql = "
	SELECT s.SERVICE_NAME, ( SELECT SERVICE_TYPE_NAME FROM  SERVICE_TYPE WHERE SERVICE_TYPE.SERVICE_TYPE_ID = s.SERVICE_TYPE_ID) SERVICE_TYPE_NAME, o.CLIENT_ID, o.ORG_NAME as CLIENT_OWNER_NAME, ( SELECT b_name FROM  new_building WHERE b_id = building_id) address, o.INN 
	FROM  SERVICE_L s, INSTALLATION_L ins, ORGANIZATION o 
	WHERE   s.OBJECT_ID = SERVICE_ID AND o.CLIENT_ID = s.CLIENT_OWNER_ID AND ins.OBJECT_STATUS_ID = 258 AND s.OBJECT_STATUS_ID = 258 AND CONTAINS (ORG_NAME, '%{$_GET[search_str]}%') > 0 AND S.OBJECT_ID IN (SELECT SO.SERVICE_ID FROM  SERVICE_OBJECT so WHERE OBJECT_ID IN ( SELECT SUBNET_ID FROM  SUBNET s WHERE s.NETWORK_SEGMENT_ID IN (207320948, 207320966, 208190120, 207320982, 207320984, 207320988, 207320990)) AND SO.OBJECT_ENTITY_ID IN (194, 193, 27200002, 189, 30196, 30199, 30200, 30201, 30198))
	";
} else {
	die("<h3 class='text-center'>По Вашему запросу ничего не найдено. Попробуйте изменить критерии поиска.</h3>");
}

$result = qwe($sql);
while ($mass = myarr($result)) {
	$tr .= "
		<div onclick='getAddressDetailById({$mass[CLIENT_ID]});'>
			<div>{$mass['CLIENT_OWNER_NAME']}</div>
			<div>{$mass['ADDRESS']}</div>
		</div>
	";
	$i++;
}

if ($tr) {
	foreach ($search_str_arr as $key => $value) {
		$tr = preg_replace("/({$value})/iu", "<b>$1</b>", $tr);
	}
	echo "<h3>Найдено записей: {$i}</h3>";
	echo "
	<div class='table table-hovered table-clickable'>
		<div class='head'>
			<div>Название организации</div>
			<div>Адрес услуги</div>
		</div>
		{$tr}
	</div>
	";
} else {
	echo "<h3 class='text-center'>По Вашему запросу ничего не найдено. Попробуйте изменить критерии поиска.</h3>";
}

?>