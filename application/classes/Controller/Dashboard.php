<?php defined('SYSPATH') or die('No direct script access.');

//для отладки http://localhost/cvs/index.php/dashboard/exec для ГРЗ
//для отладки http://localhost/cvs/index.php/dashboard/sendMPT для UHF
//public $is_test=true
class Controller_Dashboard extends Controller{

   public $template = 'template';
   //K631TX199
   //H497HB150
   
   //007E3AC2
   public $dataGRZ=array (
			'camera' => '1',
			'channel' => 3,
			'count' => 16,
			'dateTime' => '20250429T141918Z',
			'description' =>'---',
			'direction' => 0,
			'groupId' => -1,
			'id' => 624101,
			'image' =>  '/9j/4AAQSkZJRgABAQAAAQABAAD//gALQ1ZTIMDi8u4r/9sAQwAGBAUGBQQGBgUGBwcGCAoQCgoJCQoUDg8MEBcUGBgXFBYWGh0lHxobIxwWFiAsICMmJykqKRkfLTAtKDAlKCko/9sAQwEHBwcKCAoTCgoTKBoWGigoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgo/8QBogAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoLEAACAQMDAgQDBQUEBAAAAX0BAgMABBEFEiExQQYTUWEHInEUMoGRoQgjQrHBFVLR8CQzYnKCCQoWFxgZGiUmJygpKjQ1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4eLj5OXm5+jp6vHy8/T19vf4+foBAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKCxEAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/8AAEQgC0AUAAwEhAAIRAQMRAf/aAAwDAQACEQMRAD8A8zUs7okcckkkjrGiRoXZmY4CgDkkkgYFd5p/hfwv4X0CHUvic1xc6nfqj2mg2d0wlEciqcumI2DqRIGLNsABHLYoAx4/FwaJGk8HeAVcqMr/AGGDg46ZEnP1ro/BcHhDx3eyaVrmm22heIZnC2MuhQPawzRKpkI27nj3jbJnzBypGM9gDK+HOh2H/CY6v4V8&nbsp;&hellip;',
			'inList' => 0,
			'passed' => 1,
			'plate' => 'K631TX199',
			'quality' =>  '555555555000',
			'stayTimeMinutes' => 0,
			'type' => 0,
			'weight' => 0
			);
			
	public $dataUHF=array(
			'key'=>'007E3AC2',
			'ip'=>'192.168.0.100',
			'channel'=>'0'
			);
			
			public $is_test=true;//режим работы. false -рабочий режим, true - режим ТЕСТ
			
	
	public function before()
	{
			
			parent::before();
			//$session = Session::instance();
		
	}
	
	
	
	public function action_index()
	{	
		$mess='Модуль интеграции системы распознавания государственных регистрационных знаков CVS и СКУД Артонит Сити.<br>';
		$mess.='www.artsec.ru<br>';
		$mess.='2022-'.date("Y");
		
	$this->response->body($mess);
	}

	
	public function action_sendMPT()
	{	
		Log::instance()->add(Log::NOTICE, "action_sendMPT");
		$mess='www.artsec.ru<br>';
		$mess.='2022-'.date("Y");
		if (!$this->request->method() === Request::POST)
		{
			
			Log::instance()->add(Log::NOTICE, '60 Получил данные от MPT, но это не POST');
		}
			
			if ($this->is_test) 
		{
			$input_data_0=$this->dataUHF;
		} else {
			$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		}
			echo Debug::vars('78', $input_data_0);
		
		$input_data = $input_data_0;
		$post=Validation::factory($input_data);
		$post->rule('channel', 'not_empty')//номер канала должен быть 
					->rule('channel', 'digit')
					->rule('ip', 'not_empty')//IP контроллера должен быть
					->rule('ip', 'ip')
					->rule('ip', 'Model_cvss::checkIpIsPresent') 
					->rule('key', 'not_empty')//значение ГРЗ
					->rule('key', 'regex', array(':value', '/^[ABCDEF\d]{8}+$/')) // https://regex101.com/ строк буквы АНГЛ алфавита
					
					;
		//$ip='192.168.0.101';
		//echo Debug::vars('91', filter_var($ip, FILTER_VALIDATE_IP));exit;			
			if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '95 Входные данные UHF не полные '. Debug::vars($post->errors()));//вывод номера в лог-файл
			echo Debug::vars('96 валидация UHF прошла с ошибкой', $post->errors());//exit;
			$this->response->status(200);
			return;
		}
		//echo Debug::vars('101', Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip')));exit;
		$cvs=new phpCVS(Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip')));
		echo Debug::vars('103', $cvs);//exit;	
		$cvs->grz=Arr::get($input_data, 'key');//передаю ГРЗ в модель
		//$cvs->timeStamp=Arr::get($input_data, 'dateTime', -1);//передал в модель метку времени
		//$cvs_event_id=Arr::get($input_data, 'id');//передал в модель номер события
		$cvs->check(); 
		echo Debug::vars('105', $cvs);exit;
				
		
	$this->response->body($mess);
	}

	
	
	
	//29.04.2025 прием сообщений от системы распознавания CVS
	//и их последующая обработка
	public function action_exec()
	{	
		//echo Debug::vars('89', Model::factory('mpt')->getSemafor('lastevent'));exit;
		//Model::factory('mpt')->setSemafor('123', '456');
		//Log::instance()->add(Log::NOTICE, '74 '.Debug::vars($this->request));
		$t1=microtime(1);
		Log::instance()->add(Log::NOTICE, "000\r\n");//запись в лог о начале приема-начале обработки

		if ($this->is_test) 
		{
			$input_data_0=$this->dataGRZ;
		} else {
			$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		}
		
		//Log::instance()->add(Log::NOTICE, '96 Получил данные от CVS '. Debug::vars($input_data_0));
		//Log::instance()->add(Log::NOTICE, '97 Получил данные от CVS '. Arr::get($input_data, 'ip'));
			
		$input_data=$input_data_0;
		
		echo Debug::vars('110', $input_data);//exit;
		// тут находится фильтр от повторно отправленых сообщений от CVS.
		// у повторно отправленных сообщений один и тот же номер события.
		$post=Validation::factory($input_data);
		$post->rule('id', 'not_empty')//номер события
					->rule('id', 'digit')
					->rule('id', 'Model_cvss::isEventUniq') //событие уникальное, не совпадает с ранее обработанным
					->rule('camera', 'not_empty')//номер видеокамеры
					->rule('camera', 'digit')
					->rule('camera', 'Model_cvss::checkCamIsPresent') 
					->rule('plate', 'not_empty')//значение ГРЗ
					//->rule('plate', 'max_length', array(':value', 10))
					->rule('plate', 'regex', array(':value', '/^[A-Za-z\d]{3,10}+$/')) // https://regex101.com/ строк буквы АНГЛ алфавита
					
					;
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '125 Входные данные не полные '. Debug::vars($post->errors()));//вывод номера в лог-файл
			echo Debug::vars('126 валидация прошла с ошибкой', $post->errors());//exit;
			$this->response->status(200);
			return;
		}
		//валидация данных прошла успешно, продолжаю обработку
		echo Debug::vars('129 Валидация данных ГРЗ данных прошла успешно');//exit;
		$last_event=Model::factory('mpt')->getSemafor('lastevent');//номер последнего обработанного события. Номер взят из файла временного.
	
		Log::instance()->add(Log::NOTICE, '114 last_event '. $last_event);//вывод номера в лог-файл
		
		//if($last_event - Arr::get($input_data, 'id') > 1) Log::instance()->add(Log::NOTICE, '78 Потеряны соыбтия с old по new.', array('old'=>$last_event, 'new'=>Arr::get($input_data, 'id') ));
		Log::instance()->add(Log::NOTICE, "001 Start cvs id_event=".Arr::get($input_data, 'id').',
					grz '.Arr::get($input_data, 'plate').',
					camera '.Arr::get($input_data, 'camera').',
					timestamp '.microtime(true).',
					time_from_start='.number_format((microtime(1) - $t1), 3));
		
			
				
		//сохраняю номер полученного пакета от CVS
		
		//echo Debug::vars('150');exit;
		
		Model::factory('mpt')->setSemafor('lastevent', Arr::get($input_data, 'id'));//сохранил последний номер обрабатываемого события
		
		$id_gate = Model::factory('mpt')->getIdGateFromCam(Arr::get($input_data, 'camera'));//получил номер ворот
		
		Log::instance()->add(Log::NOTICE, '133 получил id_gate = :id_gate по номеру камеры=:cam.', array(':id_gate'=>$id_gate, ':cam'=>Arr::get($input_data, 'camera')));
	
		//и далее могу проводить валидацию, имея номер ГРЗ и номер ворот.
	
	
		//todo валидация на наличие номера камеры в настройках.
		
		//echo Debug::vars('163');exit;
		
		
		 //$cvs=new phpCVS(Arr::get($input_data, 'camera'));
		 $cvs=new phpCVS(Model_cvss::getGateFromCam(Arr::get($post, 'camera')));
		 //Log::instance()->add(Log::NOTICE, '117 '. Debug::vars($cvs));
		 //echo Debug::vars('168', $cvs);exit;
		
		$cvs->grz=Arr::get($input_data, 'plate');//передаю ГРЗ в модель
		$cvs->timeStamp=Arr::get($input_data, 'dateTime', -1);//передал в модель метку времени
		$cvs_event_id=Arr::get($input_data, 'id');//передал в модель номер события
		
		// вызов процесса валидации. Результат валидации сохраняется в $cvs как значения параметров
		$cvs->check(); 
		echo Debug::vars('176', $cvs);exit;
		Log::instance()->add(Log::NOTICE, '117 '. Debug::vars($cvs));exit;
		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		$dt=0;
		$dt=time()-strtotime($cvs->timeStamp);
		//фиксирую результат валидации
		Log::instance()->add(Log::NOTICE, "004 cam\tcode_validation\tgrz\ttimestamp\tdirect\tcvs_event_id\ttexec\tdesc",
		array(
			'dt'=>$dt,
			'cvs_event_id'=>$cvs_event_id,
			'timestamp'=>date("Y-m-d H:i:s", strtotime($cvs->timeStamp)),
			'cam'=>$cvs->cam,
			'direct'=>$direct,
			'tablo_port'=>$cvs->tablo_ip,
			'tablo_port'=>$cvs->tablo_port,
			'grz'=>$cvs->grz,
			'code_validation'=>$cvs->code_validation,
			'eventdMess'=>iconv('windows-1251','UTF-8',$cvs->eventdMess),
			'texec'=>number_format((microtime(1) - $t1), 3),
			'desc'=>' результат проверки данных',
		));
		
		/* if(($cvs->cam ==1) or ($cvs->cam ==3))
		{
			
			Log::instance()->add(Log::NOTICE, "998 Stop cam=".$cvs->cam.", cvs=". Arr::get($input_data, 'id').', grz='.$cvs->grz.', code_validation='.$cvs->code_validation.', total_time='.number_format((microtime(1) - $t1), 3).' не обслуживается');
			return;
		}
		 */
		
		//$tablo=new phpMPT($cvs->tablo_ip, $cvs->tablo_port); //работа в режиме UDP
	//начинаю обработку результата
		$tablo=new phpMPTtcp($cvs->tablo_ip, $cvs->tablo_port);//работа в режиме TCP 
		
			//сохраняю в семафор с номером табло номер обрабатываемого события
		Model::factory('mpt')->setSemafor('lastevent'.$cvs->cam, $cvs_event_id);
		
		//обработка кодов валидации
		 switch($cvs->code_validation){
		
		
			case 50 : //проезда разрешен
				$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);//создаю экземпляр контроллера МПТ
				$mpt->openGate($cvs->mode);// даю команду открыть ворота
			
				$i=0;
					while($mpt->result !='OK' AND $i<10)// делать до 10 попыток
					{
						Log::instance()->add(Log::DEBUG, '155 Команда открыть ворота '.$cvs->box_ip.':'.$cvs->box_port.' выполнена неудачно: '.$mpt->result.' desc '.$mpt->edesc.'. timestamp '.microtime(true).'. Команда Открыть ворота повторяется еще раз, попытка '.$i.' time_from_start='.number_format((microtime(1) - $t1), 3));
						$mpt->openGate($cvs->mode);// открыть ворота
						$i++;
					}
					Log::instance()->add(Log::NOTICE, '004_150 Событие 50. Результат выполнения команды openGate '.$cvs->box_ip.':'.$cvs->box_port.' result='.$mpt->result.', desc='.$mpt->edesc.'  после '. $i .' попыток time_from_start='.number_format((microtime(1) - $t1), 3));		
				if($mpt->result == 'Err') 
				{
					Log::instance()->add(Log::NOTICE, '138 Событие 50. Не смог открыть ворота в течении 10 попыток. Видеокамера '.$cvs->cam.' ('.$direct.') ГРЗ '.$cvs->grz.' контролер IP='.$cvs->box_ip.':'.$cvs->box_port.' Режим шлюза '.$cvs->mode.' Ответ '.$mpt->result.' edesc '.$mpt->edesc);		
				} else 
				{
					Log::instance()->add(Log::NOTICE, '004_64 Событие 50. Ответ контроллера после повторной команды '.$mpt->result.' edesc '.$mpt->edesc.'  после '. $i .' попыток.');		
				}
					//теперь занимаюсь выводом информации на табло
				
				$tablo->command='clearTablo';
				$tablo->execute(); 		
				Log::instance()->add(Log::NOTICE, '152 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
					
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x02";
				$tablo->execute();
				Log::instance()->add(Log::NOTICE, '158 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x02\x01";
				$tablo->execute();
				Log::instance()->add(Log::NOTICE, '164 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
				/* 
				$tablo->command='scrolText';
				$tablo->execute(); 	
				Log::instance()->add(Log::NOTICE, '169 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
				 */	
			 
			 break;
			 case 46 : //неизвестная карта
					//для неизвестной карты открывать ворота НЕ надо, поэтому экземпляр МПТ не создается.
					//работаю только с табло
				$tablo->command='clearTablo';
				$tablo->execute(); 	
				Log::instance()->add(Log::NOTICE, '173 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
							
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x03";
				$tablo->execute();
				Log::instance()->add(Log::NOTICE, '180 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x03\x01";
				$tablo->execute();
				Log::instance()->add(Log::NOTICE, '187 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				/* 
				$tablo->command='scrolText';
				$tablo->execute(); 	
				Log::instance()->add(Log::NOTICE, '192 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
				 */
					 
				
			 break;
			 
			case 65 : //проезд запрещен (нет прав на проезд в этот гараж)
				//тут же можно сделать проверку: может, имеется разрешение на въезд в другой паркинг?
					
				$tablo->command='clearTablo';
				$tablo->execute(); 		
				Log::instance()->add(Log::NOTICE, '203 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x04";
				$tablo->execute();
				Log::instance()->add(Log::NOTICE, '203 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x04\x01";
				$tablo->execute();
				Log::instance()->add(Log::NOTICE, '216 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				/* $tablo->command='scrolText';
				$tablo->execute(); 		
				Log::instance()->add(Log::NOTICE, '221 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
 */
			
				
			 break;
			 
			 case 81 : //проезд запрещен (нет мест в гаражах)
				//тут же можно сделать проверку свободных мест на другой парковке	
					
				$tablo->command='clearTablo';
				$tablo->execute(); 	
				Log::instance()->add(Log::NOTICE, '232 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
					
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x06";
				$tablo->execute();
				Log::instance()->add(Log::NOTICE, '239 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x06\x01";
				$tablo->execute();
				Log::instance()->add(Log::NOTICE, '246 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				/* 
				$tablo->command='scrolText';
				$tablo->execute(); 		
				Log::instance()->add(Log::NOTICE, '251 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
 */
				
			 break;
			 
			 
			 default: //код валидации не обрабатывается.
			 
				Log::instance()->add(Log::NOTICE, "244 Неизвестный код валидации ". $cvs->code_validation);
			
			 
			 break;
		 } 
	
	$t2=microtime(1);
	
	Log::instance()->add(Log::NOTICE, "005 cam\tcode_validation\tgrz\ttimestamp\tdirect\tcvs_event_id\texecTime\tdesc",
		array(
			'cvs_event_id'=>$cvs_event_id,
			'timestamp'=>date("Y-m-d H:i:s", strtotime($cvs->timeStamp)),
			'cam'=>$cvs->cam,
			'direct'=>$direct,
			'tablo_port'=>$cvs->tablo_ip,
			'tablo_port'=>$cvs->tablo_port,
			'grz'=>$cvs->grz,
			'code_validation'=>$cvs->code_validation,
			'eventdMess'=>iconv('windows-1251','UTF-8',$cvs->eventdMess),
			'execTime'=>number_format(($t2 - $t1), 3),
			'desc'=>' обработка данных завершена',
		));
		
		$t2=microtime(1);

	
		$this->response->status(200);
		
		//сохранение файла с фотографией машины
		
		
//		Model::factory('mpt')->savePhoto( Arr::get($input_data, 'plate'), base64_decode(Arr::get($input_data, 'image'))); //debug
		
		//Log::instance()->add(Log::NOTICE, "273 Сохранение фото ". Arr::get($input_data, 'plate').', сохранено за '.(microtime(1) - $t2));
		
		
	Log::instance()->add(Log::NOTICE, "999 Stop cam=".$cvs->cam.", cvs=". Arr::get($input_data, 'id').', grz='.$cvs->grz.', code_validation='.$cvs->code_validation.', total_time='.number_format((microtime(1) - $t1), 3)."\r\n");	
		return;
	}


	public function after()
	{
		
	}
}
