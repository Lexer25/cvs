<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_mpt extends Model {
	
	
	public function send_message($mess = null)
	{
		//echo Debug::vars('8', $mess); exit;
		$ip = '192.168.8.120';     // change if necessary
		$port = 1985;                     // change if necessary
		$username = '1';                   // set your username
		$password = '1';                   // set your password
		$client_id = 'phpmpt-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

		$mpt = new phpmpt($ip, $port);
		if ($mpt->connect()) {
			//$mpt->message("\x03\x56\x55");
			//$mpt->message(0x03, 0x53, 0x50);
			//$answ=$mpt->message("\x04\x4F\x00\x4B");
			$command= "\x03\x56\x55";
			//$command= "\x03\x53\x50"; //получить версию устройства
			$command= "\x04\x4F\x00\x4B"; //открыть дверь 0
			
			$answ=$mpt->message($command);
			//$mpt->message('mama');
			$mpt->close();
			echo Debug::vars('--',strlen($answ), substr($answ, 0, 20), hexdec(substr($answ, 0, 2)));
			echo Debug::vars('22',Arr::get($answ, 0),  hexdec(Arr::get($answ, 0)));
			echo Debug::vars('23',Arr::get($answ, 1), hexdec(Arr::get($answ, 1)));
			echo Debug::vars('24', Arr::get($answ, 2),hexdec(Arr::get($answ, 2)));
			echo Debug::vars('25', Arr::get($answ, 3),hexdec(Arr::get($answ, 3)));
			echo Debug::vars('26', Arr::get($answ, 4),hexdec(Arr::get($answ, 4)));
			echo Debug::vars('27', Arr::get($answ, 5),hexdec(Arr::get($answ, 5)));
			$res=' Connect OK, send = '. $answ;
		} else {
			$res = "Error. Time out!";
			
		}
		return $res;
	}
	
	
	
	
	
	
	
}
