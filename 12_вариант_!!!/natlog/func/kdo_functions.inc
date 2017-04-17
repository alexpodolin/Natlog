<?php

function qwe($SQL) {
	$stid = oci_parse($GLOBALS[conn],$SQL);
	oci_execute($stid);
	return $stid;
}

function qweOne($SQL) {
	$stid = oci_parse($GLOBALS[conn],$SQL);
	oci_execute($stid);
	return oci_fetch_row($stid);
}

function qweOneArr($SQL) {
	$stid = oci_parse($GLOBALS[conn],$SQL);
	oci_execute($stid);
	return oci_fetch_array($stid);
}

function myarr($stid) {
	return oci_fetch_array($stid);
}

function sendmail($to, $subject, $email_body) {
	mb_internal_encoding("UTF-8");
	$from_email = "no-reply@sats.spb.ru";
	$headers = "From: ". mb_encode_mimeheader("Служба сообщений NATLOG") ."<{$from_email}> \r\n";
	$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
	mail($to, $subject, $email_body, $headers, "-f {$from_email}");
}

function get_company_info($company_id) {
	$mass = qweOneArr("
	SELECT CLIENT_ID, ORG_NAME, SHORT_NAME, INN, (select b_name from new_building where b_id = BUILDING_ID ) building
	FROM  ORGANIZATION o, CLIENT c
	WHERE OBJECT_ID = CLIENT_ID AND CLIENT_ID = {$company_id}
	");
	return $mass;
}

function get_pagination($line_on_page, $current_page, $count_lines) {
	$return[limit] = "LIMIT {$line_on_page} OFFSET " . (!$current_page ? 0 : (($current_page - 1) * $line_on_page));
	$return[pages] = ceil($count_lines/$line_on_page);
	if ($return[pages] > 1) {
		$i_st = 1; $z = 9; $x = $z*2;
		if (($current_page+$z) > $return['pages']) { $z += (($current_page+$z)-$return['pages']-1); }
		if ($current_page > ($z+1)) { $i_st =  $current_page - $z; }
		for ($i=1; $i <= $return['pages']; $i++) {
			$query_string = $_SERVER[QUERY_STRING];
			$amp = $query_string ? "&" : "";
			$query_string = str_replace("{$amp}p={$current_page}","",$query_string);
			$url = "//{$_SERVER[HTTP_HOST]}/?{$query_string}{$amp}p={$i}";
			if (($i >= $i_st) && ($i <= $i_st+$x)) {
				$active_class = ($current_page == $i || (!$current_page && $i==1)) ? "active" : "";
				$html_pages .= "<a href='{$url}' class='pages {$active_class}' onclick='gotoAllBrPage(this); return false;'>{$i}</a>";
			}
			if ($return[pages] > $x) {
				$selected = $current_page != $i ? "" : "selected='selected'";
				$html_select_pages .= "<option value='{$url}' {$selected}>{$i}</option>";
			}
		}
		$return[html_pages] = $html_pages;
		$return[html_select_pages] = $html_select_pages ? "<select class='pages-list-select' onchange='gotoAllBrPageSelect(this);'>{$html_select_pages}</select>" : "";
	}
	return $return;
}

function get_company_services($company_id) {
	$result = qwe("
	SELECT s.service_name, ( SELECT SERVICE_TYPE_NAME FROM  SERVICE_TYPE WHERE SERVICE_TYPE.SERVICE_TYPE_ID = s.SERVICE_TYPE_ID) SERVICE_TYPE_NAME, ( SELECT IP_ADDRESS || '/' || ADDRESS_WIDTH FROM  IP_.IP_SUBNET ip WHERE ip.IP_SUBNET_ID = so.OBJECT_ID) IP_SUBNET, ( SELECT LISTAGG ( ( SELECT b_name FROM  new_building WHERE b_id = building_id), '; ') WITHIN GROUP (ORDER BY building_id) FROM  INSTALLATION_L i WHERE i.SERVICE_ID = s.OBJECT_ID AND i.OBJECT_STATUS_ID = 258) building
	FROM  SERVICE_L s, SERVICE_OBJECT so
	WHERE s.OBJECT_STATUS_ID = 258 + 0 * UID AND so.SERVICE_ID = s.OBJECT_ID AND SO.OBJECT_ENTITY_ID IN (194, 193, 27200002, 189, 30196, 30199, 30200, 30201, 30198) and  SO.OBJECT_ID IN ( SELECT SUBNET_ID FROM  SUBNET s WHERE s.NETWORK_SEGMENT_ID IN (207320948, 207320966, 208190120, 207320982, 207320984, 207320988, 207320990)) AND CLIENT_OWNER_ID = 0 * UID + {$company_id}
	");
	while ($mass = myarr($result)) {
		$i++;
		$return[$i][service_name] = $mass[SERVICE_NAME];
		$return[$i][service_type_name] = $mass[SERVICE_TYPE_NAME];
		$return[$i][ip_subnet] = $mass[IP_SUBNET];
		$return[$i][building] = $mass[BUILDING];
	}
	return $return;
}

function get_company_details($ip, $vrf_id) {
	$mass = qweOneArr("
	SELECT o.CLIENT_ID,o.ORG_NAME, o.INN, ( SELECT LISTAGG ( ( SELECT b_name  FROM  argus_sys.NEW_BUILDING
							 WHERE B_ID = building_id), ', ')
							 WITHIN GROUP (ORDER BY IS_MAIN)
					   FROM  argus_sys.INSTALLATION_L i
					   WHERE i.SERVICE_ID = s.OBJECT_ID AND BITAND (OBJECT_STATUS_ID, 6) <> 0)
						 AS address
	FROM  argus_sys.SERVICE_L s, argus_sys.ORGANIZATION o
	WHERE            s.OBJECT_ID = ( SELECT MAX (SERVICE_ID) KEEP (DENSE_RANK LAST ORDER BY ADDRESS_WIDTH) SERVICE_ID
		FROM  ARGUS_SYS.SUBNET S, IP_.IP_SUBNET ISN, argus_sys.SERVICE_OBJECT so
				   WHERE   ISN.IP_SUBNET_ID = S.SUBNET_ID
						   AND so.STATUS_ID = 258
						   AND so.OBJECT_ID = S.SUBNET_ID
						   AND ISN.IP_ADDRESS LIKE REGEXP_SUBSTR ('{$ip}', '[^.]+', 1) || '.%'
						   AND ISN.IP_VERSION = 4
						   AND BITAND (IP_ADDRESS_VALUE, argus_sys.iptonum (ISN.SUBNET_MASK)) =
						   BITAND (argus_sys.iptonum ('{$ip}'), argus_sys.iptonum (ISN.SUBNET_MASK))
						   AND NETWORK_SEGMENT_ID in (select NETWORK_SEGMENT_ID from NETWORK_SEGMENT 
						   connect  by  prior NETWORK_SEGMENT_ID = CONTAINER_SEGMENT_ID 
							start with NETWORK_SEGMENT_ID = {$vrf_id})
						   AND SO.OBJECT_ENTITY_ID IN (194, 193, 27200002, 189, 30196, 30199, 30200, 30201, 30198))
		AND o.CLIENT_ID = s.CLIENT_OWNER_ID and OBJECT_STATUS_ID in (386,258,260)
	");
	return $mass;
}

?>