<?
//Переменные
$db_host="EN-LOG-01";
$db_port="5432";
$db_name="natlog";
$db_user="nlog";
$db_pass="Vo6uVONl";

//Создадим подключение
try
    {
	$db_conn = new PDO("pgsql:host=$db_host; dbname=$db_name", $db_user, $db_pass);
// Устанвоим PDO error mode to exception
	$db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    echo "Connected successfully"; 
	}
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>
