<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Mpt extends Model {
	
	
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
	
		/*
		 установка семафора для синхронизации работы систем.
		 $semaforName - имя семаформа
		$data - содержимое семафора
		 
		 */
		 public function setSemafor($semaforName, $data)
		 {
				
				$fp = fopen($semaforName, "w"); // Открываем файл в режиме записи	
				$test = fwrite($fp, $data); // Запись в файл
				fclose($fp); //Закрытие файла
				
			 return;
		 }
		  
			/*
		сохранине фотографии на диск
		 $semaforName - имя семаформа
		$data - содержимое семафора
		 
		 */
		 public function savePhoto($namePhoto, $data)
		 {
			
			$path="C:\\xampp\\htdocs\\\parkresident\\grzPhoto\\";
				if (!file_exists($path)) {
					mkdir($path, 0777, true);
				}
				$fp = fopen($path.$namePhoto.'.jpg', "w"); // Открываем файл в режиме записи	
				$test = fwrite($fp, $data); // Запись в файл
				fclose($fp); //Закрытие файла
				
			 return;
		 }
		  
		 

		 /*
		 прочитать семафора для синхронизации работы систем.
		 в семафоре хранится номер последнего принятого сообщения.
		 $semaforName - имя семаформа
		$data - содержимое семафора
		 
		 */
		 public function getSemafor($semaforName)
		 {
				//Log::instance()->add(Log::NOTICE, '256 check');	exit;
				//Log::instance()->add(Log::NOTICE, '85 читаю файл '. $semaforName);
				$contents=0;
				if($handle = fopen($semaforName, "r"))
				{
					Log::instance()->add(Log::NOTICE, '89 файл существует, его размер  '. filesize($semaforName));
					if(filesize($semaforName)>0) $contents = fread($handle, filesize($semaforName));
					fclose($handle);
				}
				
			//Log::instance()->add(Log::NOTICE, '94 содержимое файла '. $contents);
			 return $contents;
		 }
		  
		 
		 /*
		 Получить параметры подключения по указанному gate
		 
		 */
		 public function getParamForGate($gate)
		 {
			$sql='select * from hl_param hlp where hlp.id='.$gate;
				

			$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array();
			return Arr::flatten($query);	
		 }
		  
		 
			
			/*
		 Получить ID ворот указанному IP адресу контроллера
		 
		 */
		 public function getIdGateFromMPT($ip, $channel)
		 {
			$sql='select hlp.id from hl_param hlp
			where hlp.box_ip=\''.$ip.'\'
			and hlp.channel='.$channel;
				

			$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->get('ID');
			return $query;	
		 }
		  
		 
			
			/*
		 Получить ID ворот по номерку видеокамеры 
		 
		 */
		 public function getIdGateFromCam($id_cam)
		 {
			$sql='select hlp.id from hl_param hlp
			where hlp.id_cam='.$id_cam;
				

			$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->get('ID');
			return $query;	
		 }
		  
		 
			
			
	
	
	
}
