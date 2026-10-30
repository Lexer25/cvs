<?php defined('SYSPATH') or die('No direct script access.');

/**
* @package    Tools
 * @category   Base
 * @author     Artonit
 * @copyright  (c) 2025 Artonit Team
 * @license    http://artonit/ru 
 
 */
 

class Controller_Tools extends Controller{

   public $template = 'template';
	
	public function before()
	{
			
			parent::before();
			//$session = Session::instance();
		
	}
	
	
	
	public function action_index()
	{	
		
		
		//echo Debug::vars('44', $mpt->result, $mpt->answer, $mpt->edesc);
		echo Debug::vars('45'); exit;
		
	
		
	}

	public function action_opendoor()
	{	
		$t1=microtime(1);
		Log::instance()->add(Log::NOTICE, "\r\n 60 Start");
	
	

	$input_data_0=json_decode(file_get_contents('php://input'), true);
	
	$input_data=Arr::get($input_data_0, 'plate');
	
		$last_event=Model::factory('mpt')->getSemafor('lastevent');
		if($last_event - Arr::get($input_data, 'id') > 1) Log::instance()->add(Log::NOTICE, '78 Потеряны соыбтия с old по new.', array('old'=>$last_event, 'new'=>Arr::get($input_data, 'id') ));
		if(Arr::get($input_data, 'id') - $last_event == 1) Log::instance()->add(Log::NOTICE, '79 Порядок событий правильный. Был old, стал new.', array('old'=>$last_event, 'new'=>Arr::get($input_data, 'id') ));
		if($last_event == Arr::get($input_data, 'id') )
		{
			Log::instance()->add(Log::NOTICE, '78 Повторный пакет от CVS. Delta time='. (time()-strtotime(Arr::get($input_data, 'dateTime'))). ' '.file_get_contents('php://input'));
			Log::instance()->add(Log::NOTICE, '80 повторный прием события. id нового события new равен номеру ранее обработанного события old. Дальнейша обработка события прекращается.', array('old'=>$last_event, 'new'=>Arr::get($input_data, 'id') ));
			exit;
		}
		
		
		Log::instance()->add(Log::NOTICE, '72 Начало работы. Получены данные от CVS. Delta time='. (time()-strtotime(Arr::get($input_data, 'dateTime'))). ' '.file_get_contents('php://input'));	
		Model::factory('mpt')->setSemafor('lastevent', Arr::get($input_data, 'id'));
	
	
		//todo валидация на наличие номера камеры в настройках.
		
		 $cvs=new phpCVS(Arr::get($input_data, 'camera'));
		 
		 
		 $cvs->grz=Arr::get($input_data, 'plate');
		

		$cvs->timeStamp=Arr::get($input_data, 'dateTime', -1);
		$cvs_event_id=Arr::get($input_data, 'id');
		
		// вызов процесса валидации
		$cvs->check(); 
		
		
		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		$dt=0;
		$dt=time()-strtotime($cvs->timeStamp);
		//фиксирую результат валидации
		Log::instance()->add(Log::NOTICE, "001 dt\tcvs_event_id\ttimestamp\tcam\tgrz\tdirect\tcode_validation\teventdMess\ttexec",
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
			'texec'=>microtime(1)-$t1,
		));
		
	
		if(($cvs->cam ==1) or ($cvs->cam ==3))
		{
			
			Log::instance()->add(Log::NOTICE, "132 камера '.$cvs->cam.' не обрабатывается. ");
			exit; 
		}
		
		
		$tablo=new phpMPT($cvs->tablo_ip, $cvs->tablo_port);
				
		//сохраняю в семафор с номером табло номер обрабатываемого события
		Model::factory('mpt')->setSemafor('lastevent'.$cvs->cam, $cvs_event_id);
		
		
		 switch($cvs->code_validation){
			 
			 case 50 : //проезда разрешен
				$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);
				
				$mpt->openGate($cvs->mode);// открыть ворота
		
		//Log::instance()->add(Log::NOTICE, '138 Событие 50. Для видеокамеры '.$cvs->cam.' ('.$direct.') команда на открытие ворот подана на контролер IP='.$cvs->box_ip.':'.$cvs->box_port.'.');	
		Log::instance()->add(Log::NOTICE, '139 Событие 50. Ответ контроллера '.$mpt->result.'.');				
	
		$tablo->command='clearTablo';
		$tablo->execute(); 		
		
		$tablo->command='text';// вывод ГРЗ на табло
		$tablo->commandParam=$cvs->grz;
		$tablo->coordinate="\x00\x00\x02";
		$tablo->execute();
		
		$tablo->command='text';// вывод сообщений на табло
		$tablo->commandParam=$cvs->eventdMess;
		$tablo->coordinate="\x08\x00\x02";
		$tablo->execute();
		
		//Log::instance()->add(Log::NOTICE, '154 Событие 50. Для видеокамеры '.$cvs->cam.' ('.$direct.')  на табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' вывод надписи "'.$cvs->grz.' '.iconv('windows-1251','UTF-8',$cvs->eventdMess).'" завершен');
		$tablo->command='scrolText';
		$tablo->execute(); 		
			
			 
			 break;
			 case 46 : //неизвестная карта
			
		$tablo->command='clearTablo';
		$tablo->execute(); 		
		
		$tablo->command='text';// вывод ГРЗ на табло
		$tablo->commandParam=$cvs->grz;
		$tablo->coordinate="\x00\x00\x03";
		$tablo->execute();
		
		$tablo->command='text';// вывод сообщений на табло
		$tablo->commandParam=$cvs->eventdMess;
		$tablo->coordinate="\x08\x00\x03";
		$tablo->execute();
		
		$tablo->command='scrolText';
		$tablo->execute(); 			 
			 
				
			 break;
			 
			case 65 : //проезд запрещен (нет прав на проезд в этот гараж)
		$tablo->command='clearTablo';
		$tablo->execute(); 		
		
		$tablo->command='text';// вывод ГРЗ на табло
		$tablo->commandParam=$cvs->grz;
		$tablo->coordinate="\x00\x00\x04";
		$tablo->execute();
		
		$tablo->command='text';// вывод сообщений на табло
		$tablo->commandParam=$cvs->eventdMess;
		$tablo->coordinate="\x08\x00\x04";
		$tablo->execute();
		
		$tablo->command='scrolText';
		$tablo->execute(); 		
			
				
			 break;
			 
			 case 81 : //проезд запрещен (нет мест в гаражах)
			
			
			$tablo->command='clearTablo';
		$tablo->execute(); 		
		
		$tablo->command='text';// вывод ГРЗ на табло
		$tablo->commandParam=$cvs->grz;
		$tablo->coordinate="\x00\x00\x06";
		$tablo->execute();
		
		$tablo->command='text';// вывод сообщений на табло
		$tablo->commandParam=$cvs->eventdMess;
		$tablo->coordinate="\x08\x00\x06";
		$tablo->execute();
		
		$tablo->command='scrolText';
		$tablo->execute(); 		

				
			 break;
			 
			 
			 default: //код валидации не обрабатывается.
			 
		
			
			 
			 break;
		 } 

	Model::factory('mqtt')->send_message('root/test/phpCVS/', $cvs_event_id.'|'. $cvs->timeStamp .'|'.$cvs->cam.'|'. $cvs->grz.'|'. $direct.'|'. $cvs->code_validation.'|'. (microtime(1) - $t1));
		
	//Log::instance()->add(Log::NOTICE, '152 Контрольное сообщение на MQTT отправлено');
	//Log::instance()->add(Log::NOTICE, '153 Обработка завершена. Время выполнения '. (microtime(1) - $t1));
	$t2=microtime(1);
	Log::instance()->add(Log::NOTICE, "003\tcvs_event_id\ttimestamp\tcam\tgrz\tdirect\tcode_validation\teventdMess\texecTime",
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
			'execTime'=>($t2 - $t1),
		));
		$t2=microtime(1);
		//sleep(10);
		
		// проверяю, что номер события в "моем" табло не изменился.
		if (Model::factory('mpt')->getSemafor('lastevent'.$cvs->cam) == $cvs_event_id)
		{
			$cvs->getMessForIdle();
			Log::instance()->add(Log::NOTICE, '255 Очищаю табло cam, т.к. старый номер события old совпадает с новым номером события new. Вывожу служебные надписи text1 и text2',
				array('old'=>Model::factory('mpt')->getSemafor('lastevent'.$cvs->cam),
				'new'=>$cvs_event_id,
				'cam'=>$cvs->cam,
				'text1'=>iconv('windows-1251','UTF-8',$cvs->top_string),
				'text2'=>iconv('windows-1251','UTF-8',$cvs->down_string),
				), null);
		//очищаю табло через
		$tablo->command='clearTablo';
		//$tablo->execute();
		$tablo->command='text';// вывод ГРЗ на табло
		
		$tablo->commandParam='';
		//$tablo->execute();
		Log::instance()->add(Log::NOTICE, 'Выполнена очистка экрана');
		Log::instance()->add(Log::NOTICE, "\r\n");
		
		//вывод рекламных сообщений на табло
	
		$tablo->command='text';// вывод строки 1
		$tablo->commandParam=$cvs->top_string;
		$tablo->coordinate="\x00\x00\x02";
		//$tablo->execute();
		
		$tablo->command='text';// вывод строки 1
		$tablo->commandParam=$cvs->down_string;
		$tablo->coordinate="\x08\x00\x02";
		//$tablo->execute();
		
		$tablo->command='scrolText';
		//$tablo->execute(); 	
		
		}
		else 
		{
			Log::instance()->add(Log::NOTICE, '279 Табло cam не очищаю, т.к. старый номер события old не совпадает с новым номером события new', array('old'=>Model::factory('mpt')->getSemafor('lastevent'.$cvs->cam), 'new'=>$cvs_event_id, 'cam'=>$cvs->cam), null);
		}
		

		return;
	}

	/*
	безусловный въезд
	или просто открыть ворота.
	В методе POST надо передать
	ip адрес контроллера
	port - порт контроллера
	gateMode - режим работы ворот
	
	*/
	public function action_forcecomein()
	{
		Log::instance()->add(Log::NOTICE, '284 forcecome дверь открыл оператор для ip='.$this->request->post('ip').', port='.$this->request->post('port').', режим ворот gateMode='.$this->request->post('gateMode'). ' от компьютера '.Request::$client_ip);
	
		//извлекаю параметры из POST запроса
		$ip=$this->request->post('ip');
		$port=$this->request->post('port');
		$gateMode=$this->request->post('gateMode');
	
		$mpt=new phpMPTtcp($ip, $port);
		
		//выдать команду открыть дверь
		$mpt->openGate($gateMode);// открыть ворота

		Log::instance()->add(Log::NOTICE, '322 forcecome завершил работу. Ответ mpt: '. $mpt->result);
		$this->response->body($mpt->result);
}
	
	
}
