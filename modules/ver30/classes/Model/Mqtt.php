<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_mqtt extends Model {
	
	
	public function send_message($topik, $mess)
	{
		//echo Debug::vars('8', $mess); exit;
		$server = '194.87.237.67';     // change if necessary
		$port = 1883;                     // change if necessary
		$username = '1';                   // set your username
		$password = '1';                   // set your password
		$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

		$mqtt = new phpMQTT($server, $port, $client_id);
		if ($mqtt->connect(true, NULL, $username, $password)) {
			$mqtt->publish($topik, $mess, 0, false);
			//Log::instance()->add(Log::NOTICE, '19 Mqtt subscribe '. $topik);
			//Log::instance()->add(Log::NOTICE, '19 Mqtt subscribe '. Debug::vars($mqtt->subscribe($topik, 0)));
			//Log::instance()->add(Log::NOTICE, '19 Mqtt subscribe '. Debug::vars($mqtt->subscribeAndWaitForMessage($topik, 0)));
			
			$mqtt->close();
			$res=' Connect OK, send = OK.';
		} else {
			$res = "Error. Time out!\n";
			
		}
		return $res;
	}
	
	
	
	
	
	
	
}
