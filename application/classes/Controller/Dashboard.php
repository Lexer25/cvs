<?php defined('SYSPATH') or die('No direct script access.');

//для отладки http://localhost/cvs/index.php/dashboard/exec для ГРЗ
//для отладки http://localhost/cvs/index.php/dashboard/sendMPT для UHF


/**
* @package    dashboard
 * @category   Base
 * @author     Artonit
 * @copyright  (c) 2025 Artonit Team
 * @license    http://artonit/ru 
 
 */
class Controller_Dashboard extends Controller{
//class Controller_Dashboard extends Controller_Template {

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
			
	public $is_test=false;//режим работы. false -рабочий режим, true - режим ТЕСТ
	
	public $ip_test='172.16.101.101';
			
	
	public function before()
	{
			
			parent::before();
			//$session = Session::instance();
		
	}
	
	
	
	public function action_index()
	{	
		$mess='Модуль интеграции системы распознавания государственных регистрационных знаков CVS и СКУД Артонит Сити.<br>';
		$mess.='www.artsec.ru<br>';
		$mess.='2022-'.date("Y").'<br>';
		$mess.=HTML::anchor('guide', 'guide');
		$this->response->body($mess);
		
		
		$content = View::factory('dashboard', array(
			//'garageLst'=>$garageLst,
				
		));
        //$this->template->content = $content;
		
	
	}

	
	public function action_sendMPT()
	{	
		Log::instance()->add(Log::NOTICE, "84 action_sendMPT");
		$t1=microtime(1);
		if($this->is_test) Log::instance()->add(Log::NOTICE, "85 TEST!!!!!!! IP беру из настроек, и не от клиента");
		Log::instance()->add(Log::NOTICE, '85 data send MPT '.Debug::vars($_POST));// exit;
		Log::instance()->add(Log::NOTICE, '86  send MPT from input'. Debug::vars(json_decode(file_get_contents('php://input'), true)));// exit;
		//извлекаю IP IP адрес из запроса
		
		//Log::instance()->add(Log::NOTICE,  Debug::vars('68', Request::$client_ip));
		
		if (!$this->request->method() === Request::POST)
		{
			
			Log::instance()->add(Log::NOTICE, '60 Получил данные от MPT, но это не POST');
		}
		
		
		//$input_data_0=$_POST;//извлекаю данных из полученного пакета
		$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		
		if ($this->is_test) 
		{
			
			
			if(Arr::get($input_data_0, 'ip') == '')
			{
				$input_data_0['ip']=$this->ip_test;
				
			} else {
				
				$input_data_0['ip']=Arr::get($input_data_0, 'ip');
			}
			
			//$input_data_0['key']='00F909E8';
		} else {
			
			$input_data_0['ip']=Request::$client_ip;
		}
			//echo Debug::vars('78', $input_data_0);
		
		Log::instance()->add(Log::NOTICE, $input_data_0);
		
		
		//$input_data_0['ip']=Request::$client_ip;
		$input_data = $input_data_0;
		Log::instance()->add(Log::NOTICE, '81 Получил данные UHF '. Debug::vars($input_data));
		$post=Validation::factory($input_data);
		$post->rule('ch', 'not_empty')//номер канала должен быть 
					->rule('ch', 'digit')
					->rule('ip', 'not_empty')//IP контроллера должен быть
					->rule('ip', 'ip')
					->rule('ip', 'Model_cvss::checkIpIsPresent') 
					->rule('key', 'not_empty')//значение ГРЗ
					->rule('key', 'regex', array(':value', '/^[ABCDEF\d]{3,8}+$/')) // https://regex101.com/ строк буквы АНГЛ алфавита
					
					;
		//$ip='192.168.0.101';
		//echo Debug::vars('91', filter_var($ip, FILTER_VALIDATE_IP));exit;			
			if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '95 Входные данные UHF не полные '. Debug::vars($post->errors()));//вывод номера в лог-файл
			Log::instance()->add(Log::NOTICE, '131 Обработку UHF прекращаю.');//вывод номера в лог-файл
			//echo Debug::vars('96 валидация UHF прошла с ошибкой', $post->errors());//exit;
			$this->response->status(400);
			return;
		}
		Log::instance()->add(Log::NOTICE, '140 Валидация UHF выполнена успешно, продолжаю работу.');//вывод номера в лог-файл
		
		Log::instance()->add(Log::NOTICE, '103 '. Debug::vars(Arr::get($post, 'ip'), Arr::get($post, 'ch'))); 
		Log::instance()->add(Log::NOTICE, '104 '. Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch'))); 
		
		
		$cvs=new phpCVS(Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch')));
		
		
		
		$cvs->grz=Arr::get($input_data, 'key');//передаю ГРЗ в модель
		$cvs->check(); 
		
		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		
		
		Log::instance()->add(Log::NOTICE, '231 id_gate = :id_gate ip=:ip channel=:channel id_dev=:id_dev key=:key direct=:direct validate=:validate ', 
					array(':id_gate'=>Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch')), 
							':ip'=>Arr::get($input_data, 'ip'),
							':channel'=>Arr::get($input_data, 'ch'),
							':id_dev'=>$cvs->id_dev,
							':key'=>Arr::get($input_data, 'key'),
							':direct'=>$direct,
							':validate'=>$cvs->code_validation,
							));		
		
	$this->response->status(200);
	
	
	//Log::instance()->add(Log::NOTICE, '171 Начинаю вывод на табло '. Debug::vars($cvs));
	
	//$tablo=new phpMPTtcp($cvs->tablo_ip, $cvs->tablo_port);//работа в режиме TCP 
	
	
	
	 //Проверка режима работы: если включен режим Тест, то надо возращать результат 145 (прохода в режиме Тест).
	   $config = Kohana::$config->load('config');
		Log::instance()->add(Log::NOTICE, '188 config '. Debug::vars($config)); 
		
		
		if(Arr::get($config, 'testMode'))
		{
			Log::instance()->add(Log::NOTICE, '182 включен режим testMode.'); 
			$cvs->code_validation=50;//разрешить проезд
			$cvs->eventdMess='OPEN in TEST MODE';
		}
		
		
		//теперь определяю каким реле щелкать.
		
		$reverseList=Arr::get($config, 'reverseGate');
		$_idGate=Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch'));
		// Log::instance()->add(Log::NOTICE, '202-1'.Debug::vars($reverseList)); //exit; 
		// Log::instance()->add(Log::NOTICE, '203-1'.Debug::vars($_idGate)); //exit; 
		// Log::instance()->add(Log::NOTICE, '203-1'.Debug::vars(in_array($_idGate,$reverseList))); exit; 
		
		
		Log::instance()->add(Log::NOTICE, '182-1'.Debug::vars($cvs)); 
		
		if(in_array($_idGate,$reverseList))
		{
			$cvs->mode = 2;//если режим работы Реверсивные ворота, то щелакаю обоими реле
		} else {
			
			$cvs->mode = Arr::get($input_data, 'ch');//если режим НЕ реверсивный, то режим равен номеру канала
		}
	
		Log::instance()->add(Log::NOTICE, '182-1'.Debug::vars($cvs)); 
	
	
		$tablo=new phpTablo($cvs->tablo_ip, $cvs->tablo_port);//работа в режиме TCP 
		
			//сохраняю в семафор с номером табло номер обрабатываемого события
		//Model::factory('mpt')->setSemafor('lastevent'.$cvs->cam, $cvs_event_id);
		
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

				
				
			 break;
			 
			 
			 default: //код валидации не обрабатывается.
			 
				Log::instance()->add(Log::NOTICE, "244 Неизвестный код валидации ". $cvs->code_validation);
			
			 
			 break;
		 } 
	
	$t2=microtime(1);
	
	/* Log::instance()->add(Log::NOTICE, "005 cam\tcode_validation\tgrz\ttimestamp\tdirect\tcvs_event_id\texecTime\tdesc",
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
		)); */
		
		$t2=microtime(1);

	
		$this->response->status(200);
		
		//сохранение файла с фотографией машины
		
		
//		Model::factory('mpt')->savePhoto( Arr::get($input_data, 'plate'), base64_decode(Arr::get($input_data, 'image'))); //debug
		
		//Log::instance()->add(Log::NOTICE, "273 Сохранение фото ". Arr::get($input_data, 'plate').', сохранено за '.(microtime(1) - $t2));
		
		
	Log::instance()->add(Log::NOTICE, "999 Stop cam=".$cvs->cam.", cvs=". Arr::get($input_data, 'id').', grz='.$cvs->grz.', code_validation='.$cvs->code_validation.', total_time='.number_format((microtime(1) - $t1), 3)."\r\n");	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		return;
	}

	
	
	
	//29.04.2025 прием сообщений от системы распознавания CVS
	//и их последующая обработка
	public function action_exec()
	{	
		//echo Debug::vars('89', Model::factory('mpt')->getSemafor('lastevent'));exit;
		//Model::factory('mpt')->setSemafor('123', '456');
		Log::instance()->add(Log::NOTICE, '74 '.Debug::vars($this->request));
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
		
		Log::instance()->add(Log::NOTICE, '158 Получил данные от CVS '. Debug::vars('158', $input_data_0));
		Log::instance()->add(Log::NOTICE, '159 Получил данные от CVS '. Debug::vars('158', $input_data));
		
		
		//echo Debug::vars('110', $input_data);//exit;
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
					//->rule('plate', 'regex', array(':value', '/^[A-Za-z\d]{3,10}+$/')) // https://regex101.com/ строк буквы АНГЛ алфавита
					
					;
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '125 Входные данные не полные '. Debug::vars($post->errors()));//вывод номера в лог-файл
			//echo Debug::vars('126 валидация прошла с ошибкой', $post->errors());//exit;
			$this->response->status(200);
			return;
		}
		
		//валидация данных прошла успешно, продолжаю обработку
		//echo Debug::vars('129 Валидация данных ГРЗ данных прошла успешно');//exit;
		$last_event=Model::factory('mpt')->getSemafor('lastevent');//номер последнего обработанного события. Номер взят из файла временного.
	
		Log::instance()->add(Log::NOTICE, '221 last_event '. $last_event);//вывод номера в лог-файл
		
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
		//echo Debug::vars('176', $cvs);exit;
		
		//Log::instance()->add(Log::NOTICE, '117 '. Debug::vars($cvs));
		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		$dt=0;
		$dt=time()-strtotime($cvs->timeStamp);
		
		//фиксирую результат валидации
		/* Log::instance()->add(Log::NOTICE, "004 cam\tcode_validation\tgrz\ttimestamp\tdirect\tcvs_event_id\ttexec\tdesc",
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
		)); */
		//Log::instance()->add(Log::NOTICE,Debug::vars($cvs));
		Log::instance()->add(Log::NOTICE, '231 id_gate = :id_gate cam=:cam grz=:grz direct=:direct validate=:validate ', 
					array(':id_gate'=>$id_gate, 
							':cam'=>Arr::get($input_data, 'camera'),
							':grz'=>Arr::get($input_data, 'plate'),
							':direct'=>$direct,
							':validate'=>$cvs->code_validation,
							));
		$this->response->status(200);
		//return;
		
	//начинаю обработку результата
		$tablo=new phpTablo($cvs->tablo_ip, $cvs->tablo_port);//работа в режиме TCP 
		
			//сохраняю в семафор с номером табло номер обрабатываемого события
		//Model::factory('mpt')->setSemafor('lastevent'.$cvs->cam, $cvs_event_id);
		
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
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		return;
	}


	public function after()
	{
		
	}
}
