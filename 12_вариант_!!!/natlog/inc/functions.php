<?

function get_gentable()
{

global $db_conn;

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
$result = $db_conn->query("SELECT * FROM vrf_name");
while ($row = $result->fetch()) {
	$vrf_arr[$row[short_vrf_name]] = $row[full_vrf_name];
	$vrf_argus_id_arr[$row[short_vrf_name]] = $row[argus_id_vrf_name];
}

//Форма поиска по параметрам при нажатии кнопки "Найти"
//Если форма не заполнена, то по умолчанию выводит последние записи за сегодня
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
      $now_hour = date('H');                              // Получим текущий час          
      $t_name = $d_year.$d_month.$d_day.$h_hour;          //Дата+время для имени таблицы
      $t_minute = $_GET["minute"];       
      //$t_name = "logdata_{$d_year}_{$d_month}_{$d_day}" . " ";  //Получим диапазон имен таблиц

      $min_d_year = date('Y', $i);  //год для min_datetime_log из таблицы
      //min_datetime_log из таблицы с разницей в три часа      
      $min_datetime_log = date('Y-m-d H:i:s', strtotime("{$min_d_year}-{$d_month}-{$d_day} {$h_hour}:{$t_minute}:00")-(3600*3));       
      
           
      $now_time = date('ymdH', strtotime("now")); //вернем время и запишем в необходимой нам форме
      
      $minute_from = date('Y-m-d', $i).' '.$_GET["hour"].":".$_GET["minute-from"].":"."00";
      $minute_to = date('Y-m-d', $i).' '.$_GET["hour"].":".$_GET["minute-to"].":"."59";
                
      //Проверка существования таблицы 
      $t_exist = $db_conn->query("SELECT count(*) FROM pg_catalog.pg_tables WHERE tablename='logs_table_{$t_name}'")->fetch();       

      //Получим данные из таблицы если она существует 
      if(($t_exist['count']) and ($t_name < $now_time))
      {	 
  
		$where_sql = "datetime_log BETWEEN '{$minute_from}'::timestamp without time zone - '3 hour'::interval AND '{$minute_to}'::timestamp without time zone - '3 hour'::interval {$where}";
		//Пагинация
		$t_count = $db_conn->query("SELECT count(*) as t_count from logs_table_{$t_name} WHERE {$where_sql}")->fetch();
		$paginat_arr = get_pagination(5000, $_GET[p], $t_count[t_count]);
		
		$table['pages'] = $paginat_arr[pages];
		$table['sessions_count'] = $t_count[t_count] > $n ? "{$t_count[t_count]} ({$table['pages']} стр.)" : $t_count[t_count];
		$table['html_select_pages'] = $paginat_arr[html_select_pages];
		$table['html_pages'] = $paginat_arr[html_pages];

        $sql_get_data = $db_conn->query("SELECT * FROM logs_table_{$t_name} WHERE {$where_sql} ORDER BY datetime_log {$paginat_arr[limit]}");

        //Вставка данных в таблицу
          while ($row = $sql_get_data->fetch()) 
          {
            //если min_datetime_log и min_datetime_log ещё пустые то используем datetime_log
            $row['min_datetime_log'] = $row['min_datetime_log'] ? $row['min_datetime_log'] : $row['datetime_log'];
            $row['max_datetime_log'] = $row['max_datetime_log'] ? $row['max_datetime_log'] : $row['datetime_log'];

            $row['inside_vrf_name_full'] = $vrf_arr[trim($row['inside_vrf_name'])];
            $row['min_datetime_log'] = date("H:i:s", strtotime($row['min_datetime_log'])+(3600*3));
            $row['max_datetime_log'] = date("H:i:s", strtotime($row['max_datetime_log'])+(3600*3));

            $table['html_min_log'] .= "
			<tr>
				<td>{$row['min_datetime_log']} - {$row['max_datetime_log']}</td>
				<td>{$protocols[$row['l4']]}</td>
				<td><a href='/company_details' onclick='getAddressDetails(this); return false;' data-ip='{$row['original_source_ip']}' data-vrf='{$row['inside_vrf_name']}'>{$row['original_source_ip']}</a></td>
				<!--<td>{$row['inside_vrf_name_full']}</td>-->
				<td>{$row['original_port']}</td>
				<td>{$row['translated_source_ip']}</td>
				<td>{$row['translated_first_source_port']}</td>
				<td>{$row['destination_ip']}</td><td>{$row['destination_port']}</td>
			</tr>";

            $table['arr'][] = array(
				'datetime_log' => $row['min_datetime_log'].' - '.$row['max_datetime_log'], 
				'l4' => $row['l4'], 
				'original_source_ip' => $row['original_source_ip'], 
				'inside_vrf_name_full' => $vrf_arr[$row['inside_vrf_name_full']], 
				'translated_source_ip' => $row['translated_source_ip'], 
				'original_port' => $row['original_port'], 
				'translated_first_source_port' => $row['translated_first_source_port'], 
				'destination_ip' => $row['destination_ip'], 
				'destination_port' => $row['destination_port']
			);             
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