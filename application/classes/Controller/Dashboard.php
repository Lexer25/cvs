<?php defined('SYSPATH') or die('No direct script access.');

//class Controller_Dashboard extends Controller_Template {
class Controller_Dashboard extends Controller{

   public $template = 'template';
   public $dataGRZ=array (
			'camera' => 4,
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
			'plate' => 'C023CA797',
			'quality' =>  '555555555000',
			'stayTimeMinutes' => 0,
			'type' => 0,
			'weight' => 0
			);
			
	public $dataUHF=array(
			'key'=>'123BSD',
			);
			
			public $is_test=false;//режим работы. 0 -рабочий режим, 1 - режим ТЕСТ
	
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
			
			Log::instance()->add(Log::NOTICE, '56 Получил данные от MPT, но это не POST');
		}
			$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
			$input_data=$input_data_0;
			Log::instance()->add(Log::NOTICE, '56 Получил данные от MPT '. Debug::vars($input_data));
			Log::instance()->add(Log::NOTICE, '57 Получил данные от MPT '. Arr::get($input_data, 'ip'));
			
			
			//тут я получаю id_gate id гейта
			$id_gate = Model::factory('mpt')->getIdGateFromMPT(Arr::get($input_data, 'ip'), Arr::get($input_data, 'channel'));
			Log::instance()->add(Log::NOTICE, '65 получил id_gate по IP адресу контроллера'. $id_gate);
			
			
			//и далее могу проводить валидацию, имея номер UHF и номер ворот.
			
			
			
				
		
	$this->response->body($mess);
	}

	
	
	
	//29.04.2025 прием сообщений от системы распознавания CVS
	//и их последующая обработка
	public function action_exec()
	{	
		Model::factory('mpt')->setSemafor('123', '456');
		//Log::instance()->add(Log::NOTICE, '74 '.Debug::vars($this->request));
		$t1=microtime(1);
		//Log::instance()->add(Log::NOTICE, "000\r\n");//запись в лог о начале приема-начале обработки
	
	//опеределяю режим работы: тест или реальный?
	if($this->request->param('id') == 'test')
	{
		Log::instance()->add(Log::NOTICE, '73 Работаю в режиме ТЕСТ');
		$input_data=$this->data;
		$this->is_test=true;
		echo Debug::vars('78 работаю в режиме тест');
		
	} else {

		$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		//$input_data=Arr::get($input_data_0, 'plate');
		$input_data=$input_data_0;
	}
	 // по формату данных определяю тип источника
		//Log::instance()->add(Log::NOTICE, '109 Получил данные от CVS '. Debug::vars($input_data));
	//Log::instance()->add(Log::NOTICE, '67 Получил данные от CVS '. Arr::get($input_data, 'image'));
		
		
		//Log::instance()->add(Log::NOTICE, '250 check');	
			$last_event=Model::factory('mpt')->getSemafor('lastevent');
		Log::instance()->add(Log::NOTICE, '255 check');	

		Log::instance()->add(Log::NOTICE, '114 last_event '. $last_event);
		
		if($last_event - Arr::get($input_data, 'id') > 1) Log::instance()->add(Log::NOTICE, '78 Потеряны соыбтия с old по new.', array('old'=>$last_event, 'new'=>Arr::get($input_data, 'id') ));
		Log::instance()->add(Log::NOTICE, "001 Start cvs id_event=".Arr::get($input_data, 'id').', grz '.Arr::get($input_data, 'plate').'. timestamp '.microtime(true).' time_from_start='.number_format((microtime(1) - $t1), 3));
		if(($last_event == Arr::get($input_data, 'id')) AND (!$this->is_test))
		{
			
			Log::instance()->add(Log::NOTICE, '78 Повторный пакет от CVS. Delta time='. (time()-strtotime(Arr::get($input_data, 'dateTime'))). ' '.file_get_contents('php://input'));
			Log::instance()->add(Log::NOTICE, '80 повторный прием события. id нового события new равен номеру ранее обработанного события old. Дальнейша обработка события прекращается.', array('old'=>$last_event, 'new'=>Arr::get($input_data, 'id') ));
			$this->response->status(200);
			return;
			
		}
		
		//сохраняю номер полученного пакета от CVS
				
		Model::factory('mpt')->setSemafor('lastevent', Arr::get($input_data, 'id'));
		
		$id_gate = Model::factory('mpt')->getIdGateFromCam(Arr::get($input_data, 'camera'));
		
		Log::instance()->add(Log::NOTICE, '133 получил id_gate по номеру камеры'. $id_gate);
	
		//и далее могу проводить валидацию, имея номер ГРЗ и номер ворот.
	
	
		//todo валидация на наличие номера камеры в настройках.
		
		 $cvs=new phpCVS(Arr::get($input_data, 'camera'));
		 
		 if(!$cvs->id_gate) //для указанной точки проезда нет настроек ворот.
		 {
			Log::instance()->add(Log::NOTICE, '117 Для видеокамеры :id_cam настройки вопрот и табло не указаны. Обработку запрос прекращаю.', array(':id_cam'=> Arr::get($input_data, 'camera')));
			 
			 
		 }
		 
		 $cvs->grz=Arr::get($input_data, 'plate');//передаю ГРЗ в модель
		

		$cvs->timeStamp=Arr::get($input_data, 'dateTime', -1);
		$cvs_event_id=Arr::get($input_data, 'id');
		
		// вызов процесса валидации. Результат валидации сохраняется в $cvs как значения параметров
		$cvs->check(); 
		
		
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
		
		if(($cvs->cam ==1) or ($cvs->cam ==3))
		{
			
			Log::instance()->add(Log::NOTICE, "998 Stop cam=".$cvs->cam.", cvs=". Arr::get($input_data, 'id').', grz='.$cvs->grz.', code_validation='.$cvs->code_validation.', total_time='.number_format((microtime(1) - $t1), 3).' не обслуживается');
			return;
		}
		
		
		//$tablo=new phpMPT($cvs->tablo_ip, $cvs->tablo_port); //работа в режиме UDP
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
