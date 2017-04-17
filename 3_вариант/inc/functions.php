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
      $d_month = mb_strtolower(date('F', $i), 'UTF-8');   //Получим диапазон месяцев, с маленькой буквы
      $d_year = date('Y', $i);                //Получим диапазон год
//      $t_name = "logdata_{$d_year}_{$d_month}_{$d_day}" . " ";  //Получим диапазон имен таблиц

      $radio_limit = $_GET["limit"];  //опр. кол. строк кот. выводим в зав. от radio button

      //Запрос на извлечение данных
      $sql_get_data = $db_conn->query("SELECT * FROM logdata_{$d_year}_{$d_month}_{$d_day} 
                  WHERE true {$where} ORDER BY time DESC LIMIT '$radio_limit'");
      //Вставка данных в таблицу
      while ($row = $sql_get_data->fetch()) 
      { 
        $table[html] .= "<tr><td>{$row[time]}</td><td>{$row[proto_l4]}</td><td>{$row[orig_src_ipv4]}</td>
        <td>{$row[inside_vrf_name]}</td><td>{$row[trnsl_src_ip]}</td><td>{$row[orig_port]}</td>
        <td>{$row[trnsl_frst_src_port]}</td><td>{$row[dst_ip]}</td><td>{$row[dst_port]}</td></tr>";

        $table[arr][] = array($row[time], $row[proto_l4], $row[orig_src_ipv4], $row[inside_vrf_name],
                        $row[trnsl_src_ip], $row[orig_port], $row[trnsl_frst_src_port],
                        $row[dst_ip], $row[dst_port]); 
      }    
    }    
  return $table;
} 
?>