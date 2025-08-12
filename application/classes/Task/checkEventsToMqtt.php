    <?php defined('SYSPATH') or die('No direct script access.');
     
    /**
     *9.12.2023
     *
     * @author Бухаров
	 отправка на mqtt номера последнего события.
     */
     
    class Task_checkEventsToMqtt extends Minion_Task {
		
        
        protected function _execute(array $params)
        {
			
			//$server = '127.0.0.1';     // change if necessary
			$server = '194.87.237.67';     // change if necessary
			$port = 1883;                     // change if necessary
			$username = '1';                   // set your username
			$password = '1';                   // set your password
			$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()
			$topik='citycrm';
			$prev_id_event=0;
			$sql='select gen_id(gen_event_id,0)
			from RDB$DATABASE';

			$mqtt = new phpMQTT($server, $port, $client_id);
			while(true){
				$mess='test '.time();
			
			
			$id_event = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->get('GEN_ID');
			//if( $prev_id_event != $id_event){
				$prev_id_event = $id_event;
				if ($mqtt->connect(true, NULL, $username, $password)) {
					$mqtt->publish($topik, $id_event, 0, false);
					//$mqtt->close();
					Log::instance()->add(Log::DEBUG, '29 mqtt topik='. $topik.', mess='.$id_event.'.');
					$res=' Connect OK, send = OK.';
					} else {
					$res = "Error. Time out!\n";
					}
					
			//	}
				sleep(2);
			}
		
		
		
		}
    }