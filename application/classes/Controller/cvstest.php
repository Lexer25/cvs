<?php defined('SYSPATH') or die('No direct script access.');

//class Controller_Dashboard extends Controller_Template {
class Controller_cvstest extends Controller{

   public $template = 'template';
	
	public function before()
	{
			
			parent::before();
			//$session = Session::instance();
		
	}
	
	
	
	public function action_index()
	{	
		$t1=microtime(1);
		
		//Model::factory('mpt')->setSemafor('123', '456');
		Log::instance()->add(Log::WARNING, '23 Test semafor '.Model::factory('mpt')->getSemafor('123'));	
		
		
		$mpt=new phpMPT('192.168.1.57', 1985);
		//$mpt=new phpMPT('192.168.1.56', 1985);
		$tablo=new phpMPT('192.168.8.128', 1985);
		
		$mpt->command='opendoor';
		$mpt->commandParam="\x00";
		//$mpt->execute();/*
		$mpt->command='GetVersion';
		//$mpt->execute();
		
		$tablo->command='clearTablo';
		////$tablo->execute(); */
		$tablo->command='text';
		//$tablo->commandParam='Добро пожаловать';
		$tablo->commandParam='Дорогая передача!';
		$tablo->coordinate="\x00\x00\x01";
		//$tablo->execute();
		 
		 $tablo->command='GetVersion';
		 //$tablo->execute();
		
		
	
		
		//echo Debug::vars('44', $mpt->result, $mpt->answer, $mpt->edesc);
		//echo Debug::vars('45', $tablo->result, $tablo->answer, $tablo->edesc);
		
	
		return;
	}

	/*
	25.04.2023 г.
	функция сделана для тестирования въезда/выезда процедур валидации.
	Шлагбаумы и ворота открываются, надписи на табло выводятся реально.
	контроль номера события не производится.
	Результат выполнения выводится на странице grz.
	$control_door 0 -не выдавать сигнал на ворота, 1 - выдавать сигнал на ворота 
	$control_tablo 0 -не выводить надписи на табло, 1 - выводить надписи на табло
	
	
	*/
	
	public function action_exec()
	{	
		$control_door=0;
		$control_tablo=0;
		
		$t1=microtime(1);
		Log::instance()->add(Log::WARNING, "\r\n 61-test Start - cvstest");
	
	
	$input_data_0=json_decode(file_get_contents('php://input'), true);
	
	Log::instance()->add(Log::WARNING, '65 '.$input_data_0);
	$input_data=Arr::get($input_data_0, 'plate');
	
	
		 $cvs=new phpCVS(Arr::get($input_data, 'camera'));
		
		 
		 $cvs->grz=Arr::get($input_data, 'plate');
		

		$cvs->timeStamp=Arr::get($input_data, 'dateTime', -1);
		$cvs_event_id=Arr::get($input_data, 'id');
		
		// вызов процесса валидации
		$cvs->check(); 
		//отправка результатов валидации в ответ на запрос

		
		Log::instance()->add(Log::WARNING, '98 Результат валидации в тестовой программе'. Debug::vars($cvs));	
		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		$dt=0;
		$dt=time()-strtotime($cvs->timeStamp);
		//фиксирую результат валидации
		//тип записи (001) - номер камеры - ID_event - время - ГРЗ - направление - код валидации - текст на табло - время выполнения
		//Log::instance()->add(Log::WARNING, "001 dt\tcvs_event_id\ttimestamp\tcam\tgrz\tdirect\tcode_validation\teventdMess\ttexec",
		
		$_gate_control=($control_door)? 'Сигнал на открите подается':'Сигнал не подается';
		$_tablo_control=($control_tablo)? 'Данные на табло выводятся':'Данные на табло не выводятся';
		//echo Debug::vars('109', $_gate_control, $_tablo_control); exit;
		$result_mess=__('0022 номер камеры: id_cam<br>номер события cvs: cvs_event_id<br> метка времени: timestamp<br><b>ГРЗ: </b>grz<br> направление direct<br><b>код валидации: </b>code_validation<br> сообщение на табло: eventdMess<br> время валидации texec<br> Режим тестирования<br><b>управление воротами</b> gate_control<br><b>вывод на табло</b> tablo_control',
		array(
			'dt'=>$dt,
			'cvs_event_id'=>$cvs_event_id,
			'timestamp'=>date("Y-m-d H:i:s", strtotime($cvs->timeStamp)),
			'id_cam'=>$cvs->cam,
			'direct'=>$direct,
			'tablo_port'=>$cvs->tablo_ip,
			'tablo_port'=>$cvs->tablo_port,
			'grz'=>$cvs->grz,
			'code_validation'=>$cvs->code_validation,
			'eventdMess'=>iconv('windows-1251','UTF-8',$cvs->eventdMess),
			'texec'=>microtime(1)-$t1,
			'gate_control'=>$_gate_control,
			'tablo_control'=>$_tablo_control,
			)); 
		$this->response->body($result_mess);
		Log::instance()->add(Log::WARNING, $result_mess);
		
	
		/* if(($cvs->cam ==1) or ($cvs->cam ==3))
		{
			
			Log::instance()->add(Log::WARNING, "132 камера '.$cvs->cam.' не обрабатывается. ");
			
			return;
		} */
		
		
		$tablo=new phpMPT($cvs->tablo_ip, $cvs->tablo_port);
				
		//сохраняю в семафор с номером табло номер обрабатываемого события
		//Model::factory('mpt')->setSemafor('lastevent'.$cvs->cam, $cvs_event_id);
		
		
		 switch($cvs->code_validation){
			 
			 case 50 : //проезда разрешен
				$mpt=new phpMPT($cvs->box_ip, $cvs->box_port);
				
				if($control_door) $mpt->openGate($cvs->mode);// открыть ворота
		
		//Log::instance()->add(Log::WARNING, '138 Событие 50. Для видеокамеры '.$cvs->cam.' ('.$direct.') команда на открытие ворот подана на контролер IP='.$cvs->box_ip.':'.$cvs->box_port.'.');	
		Log::instance()->add(Log::WARNING, '139 Событие 50. Ответ контроллера '.$mpt->result.'.');				
	
		$tablo->command='clearTablo';
		if($control_tablo) if($control_tablo) $tablo->execute(); 		
		
		$tablo->command='text';// вывод ГРЗ на табло
		$tablo->commandParam=$cvs->grz;
		$tablo->coordinate="\x00\x00\x02";
		$tablo->execute();
		
		$tablo->command='text';// вывод сообщений на табло
		$tablo->commandParam=$cvs->eventdMess;
		$tablo->coordinate="\x08\x00\x02";
		$tablo->execute();
		
		//Log::instance()->add(Log::WARNING, '154 Событие 50. Для видеокамеры '.$cvs->cam.' ('.$direct.')  на табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' вывод надписи "'.$cvs->grz.' '.iconv('windows-1251','UTF-8',$cvs->eventdMess).'" завершен');
		$tablo->command='scrolText';
		if($control_tablo) $tablo->execute(); 		
			
			 
			 break;
			 case 46 : //неизвестная карта
			
		$tablo->command='clearTablo';
		if($control_tablo) $tablo->execute(); 		
		
		$tablo->command='text';// вывод ГРЗ на табло
		$tablo->commandParam=$cvs->grz;
		$tablo->coordinate="\x00\x00\x03";
		$tablo->execute();
		
		$tablo->command='text';// вывод сообщений на табло
		$tablo->commandParam=$cvs->eventdMess;
		$tablo->coordinate="\x08\x00\x03";
		$tablo->execute();
		
		$tablo->command='scrolText';
		if($control_tablo) $tablo->execute(); 			 
			 
				
			 break;
			 
			case 65 : //проезд запрещен (нет прав на проезд в этот гараж)
		$tablo->command='clearTablo';
		if($control_tablo) $tablo->execute(); 		
		
		$tablo->command='text';// вывод ГРЗ на табло
		$tablo->commandParam=$cvs->grz;
		$tablo->coordinate="\x00\x00\x04";
		$tablo->execute();
		
		$tablo->command='text';// вывод сообщений на табло
		$tablo->commandParam=$cvs->eventdMess;
		$tablo->coordinate="\x08\x00\x04";
		$tablo->execute();
		
		$tablo->command='scrolText';
		if($control_tablo) $tablo->execute(); 		
			
				
			 break;
			 
			 case 81 : //проезд запрещен (нет мест в гаражах)
			
			
			$tablo->command='clearTablo';
		if($control_tablo) $tablo->execute(); 		
		
		$tablo->command='text';// вывод ГРЗ на табло
		$tablo->commandParam=$cvs->grz;
		$tablo->coordinate="\x00\x00\x06";
		$tablo->execute();
		
		$tablo->command='text';// вывод сообщений на табло
		$tablo->commandParam=$cvs->eventdMess;
		$tablo->coordinate="\x08\x00\x06";
		$tablo->execute();
		
		$tablo->command='scrolText';
		if($control_tablo) $tablo->execute(); 		

				
			 break;
			 
			 
			 default: //код валидации не обрабатывается.
			 
		
			
			 
			 break;
		 } 

	Model::factory('mqtt')->send_message('root/test/phpCVS/', $cvs_event_id.'|'. $cvs->timeStamp .'|'.$cvs->cam.'|'. $cvs->grz.'|'. $direct.'|'. $cvs->code_validation.'|'. (microtime(1) - $t1));
		
	//Log::instance()->add(Log::WARNING, '152 Контрольное сообщение на MQTT отправлено');
	//Log::instance()->add(Log::WARNING, '153 Обработка завершена. Время выполнения '. (microtime(1) - $t1));
	$t2=microtime(1);
	Log::instance()->add(Log::WARNING, "003\tcvs_event_id\ttimestamp\tcam\tgrz\tdirect\tcode_validation\teventdMess\texecTime",
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
		sleep(0);
		
		// проверяю, что номер события в "моем" табло не изменился.
		if (Model::factory('mpt')->getSemafor('lastevent'.$cvs->cam) == $cvs_event_id)
		{
			$cvs->getMessForIdle();
			Log::instance()->add(Log::WARNING, '255 Очищаю табло cam, т.к. старый номер события old совпадает с новым номером события new. Вывожу служебные надписи text1 и text2',
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
		Log::instance()->add(Log::WARNING, 'Выполнена очистка экрана');
		Log::instance()->add(Log::WARNING, "\r\n");
		
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
		//if($control_tablo) $tablo->execute(); 	
		
		}
		else 
		{
			Log::instance()->add(Log::WARNING, '279 Табло cam не очищаю, т.к. старый номер события old не совпадает с новым номером события new', array('old'=>Model::factory('mpt')->getSemafor('lastevent'.$cvs->cam), 'new'=>$cvs_event_id, 'cam'=>$cvs->cam), null);
		}
		
		
		return;
	}

/*
безусловный въезд
или просто открыть ворота.

*/
	public function action_forcecomein()
	{
	
		
	}
	
		public function after()
	{
		
	}
}
