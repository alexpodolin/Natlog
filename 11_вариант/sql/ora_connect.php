<?
//Переменные
$db_host="SM-DB-02";
$db_port="1521";
$db_name="SUREMTS";
$db_user="kdo_";
$db_pass="1234";

//Создадим подключение
$tns = "
(DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = $db_host)(PORT = $db_port))
    )
    (CONNECT_DATA =
      (SERVICE_NAME = $db_name)
    )
  )
       ";
try
  {
    $db_ora_conn = new PDO("oci:dbname=".$tns,$db_user,$db_pass);
//    $db_ora_conn = new PDO("oci:dbname=$db_host/$db_name;charset=CL8MSWIN1251",$db_user,$db_pass);
    // Устанвоим PDO error mode to exception
  	$db_ora_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    echo "Connected to oracle successfully"; 

    // Исп. драйвер
    // $name = $db_ora_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    // echo "$name";
	}

catch(PDOException $e)
  {
    echo "Connection to oracle failed: " . $e->getMessage() . '</br>';
    //  Список доступных драйверов
    // foreach(PDO::getAvailableDrivers() as $driver)
    // echo $driver, '<br>';
  }

$res = $db_ora_conn->query("SELECT CLIENT_ID, ORG_NAME, INN FROM  ORGANIZATION WHERE CLIENT_ID = 197268700;", PDO::FETCH_ASSOC);


?>