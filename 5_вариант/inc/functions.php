<?
function get_gentable()

{
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
      $d_day = date('d', $i);                 //Получим диапазон дней      
      $d_month = mb_strtolower(date('m', $i), 'UTF-8');   //Получим диапазон месяцев, с маленькой буквы      
      $d_year = date('y', $i);                //Получим диапазон год      
      $h_hour = $_GET["hour"];                    // Получим час уст. в select
      $now_hour = date('H');             // Получим текущий час          
      $t_name = $d_year.$d_month.$d_day.$h_hour; //Дата+время для имени таблицы      
//      $t_name = "logdata_{$d_year}_{$d_month}_{$d_day}" . " ";  //Получим диапазон имен таблиц

      $radio_limit = $_GET["limit"];  //опр. кол. строк кот. выводим в зав. от radio button

      //Запрос на извлечение данных
      // $sql_get_data = $db_conn->query("SELECT * FROM logdata_{$d_year}_{$d_month}_{$d_day} 
      //             WHERE true {$where} ORDER BY time DESC LIMIT '$radio_limit'");
      $sql_get_data = $db_conn->query("SELECT * FROM logs_table_{$t_name}
                                     WHERE true {$where} ORDER BY datetime_log DESC LIMIT '$radio_limit'");
      //Вставка данных в таблицу
      while ($row = $sql_get_data->fetch()) 
      { 
        $table[html] .= "<tr><td>{$row[datetime_log]}</td><td>{$row[l4]}</td><td>{$row[original_source_ip]}</td>
        <td>{$row[inside_vrf_name]}</td><td>{$row[translated_source_ip]}</td><td>{$row[original_port]}</td>
        <td>{$row[translated_first_source_port]}</td><td>{$row[destination_ip]}</td><td>{$row[destination_port]}</td></tr>";

        $table[arr][] = array($row[datetime_log], $row[l4], $row[original_source_ip], $row[inside_vrf_name],
                        $row[translated_source_ip], $row[original_port], $row[translated_first_source_port],
                        $row[destination_ip], $row[destination_port]); 
      }    
    }    
  return $table;
} 
?>