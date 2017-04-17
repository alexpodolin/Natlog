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

$res= "SELECT o.CLIENT_ID
            ,o.ORG_NAME
                        , o.INN
                        , ( SELECT LISTAGG ( ( SELECT b_name  FROM  argus_sys.NEW_BUILDING
                                                             WHERE B_ID = building_id), ', ')
                                                             WITHIN GROUP (ORDER BY IS_MAIN)
                                   FROM  argus_sys.INSTALLATION_L i
                                   WHERE i.SERVICE_ID = s.OBJECT_ID AND BITAND (OBJECT_STATUS_ID, 6) <> 0)
                                     AS address
FROM  argus_sys.SERVICE_L s, argus_sys.ORGANIZATION o
WHERE            s.OBJECT_ID = ( SELECT MAX (SERVICE_ID) KEEP (DENSE_RANK LAST ORDER BY ADDRESS_WIDTH) SERVICE_ID
                        FROM  ARGUS_SYS.SUBNET S, IP_.IP_SUBNET ISN, argus_sys.SERVICE_OBJECT so
                                   WHERE             ISN.IP_SUBNET_ID = S.SUBNET_ID
                                                           AND so.STATUS_ID = 258
                                                           AND so.OBJECT_ID = S.SUBNET_ID
                                                           AND ISN.IP_ADDRESS LIKE REGEXP_SUBSTR ('10.3.138.192', '[^.]+', 1) || '.%'
                                                           AND ISN.IP_VERSION = 4
                                                           AND BITAND (IP_ADDRESS_VALUE, argus_sys.iptonum (ISN.SUBNET_MASK)) =
                                                           BITAND (argus_sys.iptonum ('10.3.138.192'), argus_sys.iptonum (ISN.SUBNET_MASK))
                                                           AND NETWORK_SEGMENT_ID in (select NETWORK_SEGMENT_ID from NETWORK_SEGMENT 
                                                           connect  by  prior NETWORK_SEGMENT_ID = CONTAINER_SEGMENT_ID 
                                                            start with NETWORK_SEGMENT_ID = 210181646)   
                                                           AND SO.OBJECT_ENTITY_ID IN (194, 193, 27200002, 189, 30196, 30199, 30200, 30201, 30198))
                        AND o.CLIENT_ID = s.CLIENT_OWNER_ID and OBJECT_STATUS_ID in (386,258,260)";

$stid = oci_parse($GLOBALS[conn], $res);
oci_execute($stid);

while (($row = oci_fetch_array($stid, OCI_BOTH))) {
    // Используйте название полей в верхнем регистре для ассоциативных индексов
    echo $row['CLIENT_ID'] . "<br>\n";
    echo $row['ORG_NAME'] . "<br>\n";
    echo $row['INN'] . "<br>\n";
    echo $row['ADDRESS'] . "<br>\n";
}

oci_free_statement($stid);
oci_close($conn);

?>


