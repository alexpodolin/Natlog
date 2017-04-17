<?
function getSubnets()
{
  global $db_ora_conn;

  //Выберем подсети из таблицы
  $sql_get_sbnt = $db_ora_conn->query("SELECT * FROM SUBNET"); 
  foreach ($sql_get_sbnt as $key => $val) 
  { 
    echo "<pre>";
    $subnet = preg_grep("/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\/(\d{1,3})$/", array($val['SUBNET_NAME']));    
    print_r($subnet[0]);

    //print_r($val[SUBNET_NAME]);
    
    echo "</pre>";    
  }

$sql_get_sbnt->closeCursor();
$db_ora_conn = null;
}


?>