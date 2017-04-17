<?php

$db_host="SM-DB-02";
$db_name="SUREMTS";
$db_user="podolin_";
$db_pass="alex19850524";

$GLOBALS[conn] = oci_connect($db_user, $db_pass, "{$db_host}/{$db_name}", 'UTF8');
if (!$GLOBALS[conn]) {
	$e = oci_error();
	print_r($e);
	trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

?>