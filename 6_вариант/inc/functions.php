<?
function get_gentable()

{
//Сопоставление протокола его номеру
$proto_file = file_get_contents("files/protocols"); //Читает содержимое файла в строку
$proto_arr = explode ("\n", $proto_file); //Разбивает строку с помощью разделителя  
foreach ($proto_arr as $key_null) //Разобъем эл-ты массива построчно 
{         
  $key = preg_replace("/^\#.*?$/", " ", $key_null); //Убирем строки начинающиеся с #
  $key_2  = explode("\t", trim($key));
  $protocols[$key_2[1]] = $key_2[2];           
}       

//Вывод полного имени vrf 
$vrf_arr = array("gup-lan-lit"=>"gup-lan-lite", "gsm-inet-li"=>"gsm-inet-lite", "cross-net-l"=>"cross-net-lite", "gup-inet-na"=>"gup-inet-nat-lite", "mfc-inet-na"=>"mfc-inet-nat-lite");

//Форма поиска по параметрам при нажатии кнопки "Найти"
//Если форма не заполнена, то по умолчанию выводит последние записи за сегодня
global $db_conn;
  if ($_GET["where"]) 
  {
    foreach ($_GET["where"] as $key => $value)
    {
      $value = trim($value);  //trim убрать пробелы по краям
      if ($value)
      {
        $where .= " AND {$key} = '{$value}'";
      }
    }      
  } 

  $date = strtotime($_GET['date']); //convert to unix timestamp


  //Определим заданную дату
  for ($i = $date; $i <= $date; ($i+=86400))
    {
      $d_day = date('d', $i);                             //Получим диапазон дней      
      $d_month = mb_strtolower(date('m', $i), 'UTF-8');   //Получим диапазон месяцев, с маленькой буквы      
      $d_year = date('y', $i);                            //Получим диапазон год      
      $h_hour = $_GET["hour"];                            // Получим час уст. в select
      $now_hour = date('H');                              // Получим текущий час          
      $t_name = $d_year.$d_month.$d_day.$h_hour;          //Дата+время для имени таблицы      
      //$t_name = "logdata_{$d_year}_{$d_month}_{$d_day}" . " ";  //Получим диапазон имен таблиц

      //$radio_limit = $_GET["limit"];  //опр. кол. строк кот. выводим в зав. от radio button

            
      //Проверка существования таблицы 
      $t_exist = $db_conn->query("SELECT count(*) FROM pg_catalog.pg_tables 
      WHERE tablename='logs_table_{$t_name}'")->fetch(); //Запрос
      
      if($t_exist[count])
      {
      //Получим данные из таблицы если она существует
        $sql_get_data = $db_conn->query("SELECT * FROM logs_table_{$t_name}
                                 WHERE true {$where} ORDER BY datetime_log DESC");     
        //Вставка данных в таблицу
          while ($row = $sql_get_data->fetch()) 
          { 
            $row[inside_vrf_name] = $vrf_arr[trim($row[inside_vrf_name])];
            $table[html] .= "<tr><td>{$row[datetime_log]}</td><td>{$protocols[$row[l4]]}</td><td>{$row[original_source_ip]}</td>
            <td>{$row[inside_vrf_name]}</td><td>{$row[translated_source_ip]}</td><td>{$row[original_port]}</td>
            <td>{$row[translated_first_source_port]}</td><td>{$row[destination_ip]}</td><td>{$row[destination_port]}</td></tr>";

            $table[arr][] = array($row[datetime_log], $row[l4], $row[original_source_ip], $vrf_arr[$row[inside_vrf_name]], $row[translated_source_ip], $row[original_port], $row[translated_first_source_port], $row[destination_ip], $row[destination_port]); 
          }
      }
      else //Если таблица не существует то выведем ошибку
      { 
        $table[error] = "Таблица отсутствует. Возможно введены не верные дата или время ";        
      }                           
    }    
  return $table;
} 
?>