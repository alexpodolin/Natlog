<?php

include ("sql/kdo_ora_conn.php");
include ("func/kdo_functions.inc");

$result = qwe('SELECT * FROM ORGANIZATION');
while ($mass = myarr($result)) {
	echo "{$mass[0]}";
}


?>
