<?
//Переменные
$db_host="SM-DB-02";
$db_port="1521";
$db_name="SUREMTS";
$db_user="podolin_";
$db_pass="alex19850524";

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
// Устанвоим PDO error mode to exception
	$db_ora_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    echo "Connected to oracle successfully"; 
	}
catch(PDOException $e)
    {
    echo "Connection to oracle failed: " . $e->getMessage() . '</br>'; 
    // foreach(PDO::getAvailableDrivers() as $driver)
    // echo $driver, '<br>';

    }


/*$tns = "
(DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = $db_host)(PORT = $db_port))
    )
    (CONNECT_DATA =
      (SERVICE_NAME = $db_name)
    )
  )
       ";

try{
    $conn = new PDO("oci:dbname=".$tns,$db_user,$db_pass);
//echo "Connected to oracle successfully";
}catch(PDOException $e){
    echo ($e->getMessage());
}

?>*/