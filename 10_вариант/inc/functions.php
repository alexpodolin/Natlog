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
  $calendar_date = isset($_GET['date']) ? date('Y-m-d', $date) : date('Y-m-d');
  
  //Определим заданную дату
  for ($i = $date; $i <= $date; ($i+=86400))
    {
      $d_day = date('d', $i);                             //Получим диапазон дней      
      $d_month = mb_strtolower(date('m', $i), 'UTF-8');   //Получим диапазон месяцев, с маленькой буквы      
      $d_year = date('y', $i);                            //Получим диапазон год      
      $h_hour = $_GET["hour"];                            // Получим час уст. в select
      $m_from = $_GET["minute-from"];                     //минута от уст.в select
      $m_to = $_GET["minute-to"];                         //минута до уст.в select
      $now_hour = date('H');                              // Получим текущий час          
      $t_name = $d_year.$d_month.$d_day.$h_hour;          //Дата+время для имени таблицы
      $t_minute = $_GET["minute"];       
      //$t_name = "logdata_{$d_year}_{$d_month}_{$d_day}" . " ";  //Получим диапазон имен таблиц

      $min_d_year = date('Y', $i);  //год для min_datetime_log из таблицы
      //min_datetime_log из таблицы с разницей в три часа      
      $min_datetime_log = date('Y-m-d H:i:s', strtotime("{$min_d_year}-{$d_month}-{$d_day} {$h_hour}:{$t_minute}:00")-(3600*3));       
      //min_datetime_log из таблицы + 5 минут с разницей в три часа
      $t_minute_plus = $t_minute+5;
      $min_datetime_log_plus = date('Y-m-d H:i:s', strtotime("{$min_d_year}-{$d_month}-{$d_day} {$h_hour}:{$t_minute_plus}:00")-(3600*3)); 
           
      $now_time = date('ymdH', strtotime("now")); //вернем время и запишем в необходимой нам форме      
                
      //Проверка существования таблицы 
      $t_exist = $db_conn->query("SELECT count(*) FROM pg_catalog.pg_tables 
      WHERE tablename='logs_table_{$t_name}'")->fetch();       

      //Получим данные из таблицы если она существует и не запрашиваем данные за текущий час
      if(($t_exist['count']) and ($t_name < $now_time))
      {
        //Запрос к БД на извлечение данных        
        $LIMIT = isset($where) ? 5000000 : 10000;
        //$sql_get_data = $db_conn->query("SELECT * FROM logs_table_{$t_name} WHERE true {$where} AND ((min_datetime_log>='{$min_datetime_log}' AND min_datetime_log<='{$min_datetime_log_plus}') OR (datetime_log>='{$min_datetime_log}' AND datetime_log<='{$min_datetime_log_plus}')) ORDER BY min_datetime_log DESC LIMIT $LIMIT"); 

        echo "SELECT * FROM logs_table_{$t_name} WHERE true {$where} AND ((min_datetime_log>='{$min_datetime_log}' AND min_datetime_log<='{$min_datetime_log_plus}') OR (datetime_log>='{$min_datetime_log}' AND datetime_log<='{$min_datetime_log_plus}')) ORDER BY min_datetime_log DESC LIMIT $LIMIT";              

        //Вставка данных в таблицу
          while ($row = $sql_get_data->fetch()) 
          {

            //если min_datetime_log и min_datetime_log ещё пустые то используем datetime_log
            $row[min_datetime_log] = $row[min_datetime_log] ? $row[min_datetime_log] : $row[datetime_log];
            $row[max_datetime_log] = $row[max_datetime_log] ? $row[max_datetime_log] : $row[datetime_log];

            $row[inside_vrf_name] = $vrf_arr[trim($row['inside_vrf_name'])];
            $row[min_datetime_log] = date("H:i:s", strtotime($row[min_datetime_log])+(3600*3));
            $row[max_datetime_log] = date("H:i:s", strtotime($row[max_datetime_log])+(3600*3));

            $table[html_min_log] .= "<tr><td>{$row[min_datetime_log]} - {$row[max_datetime_log]}</td><td>{$protocols[$row[l4]]}</td><td>{$row[original_source_ip]}</td><td>{$row[inside_vrf_name]}</td><td>{$row[translated_source_ip]}</td><td>{$row[original_port]}</td><td>{$row[translated_first_source_port]}</td><td>{$row[destination_ip]}</td><td>{$row[destination_port]}</td></tr>";

            $table[arr][] = array($row[min_datetime_log].$row[max_datetime_log], $row[l4], $row[original_source_ip], $vrf_arr[$row[inside_vrf_name]], $row[translated_source_ip], $row[original_port], $row[translated_first_source_port], $row[destination_ip], $row[destination_port]);             
          }          
      }             
      else //Если таблица не существует то выведем ошибку
      { 
        $table['error'] = "Таблица отсутствует или не сформирована. Возможно введены не верные дата или время ";        
      }                           
    }    
  return $table;
} 
?>