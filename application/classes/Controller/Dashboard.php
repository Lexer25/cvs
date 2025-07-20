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
   public $permission = false;
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
			
	public $ip_is_test=false;//режим отладки. false -рабочий режим, true - режим ТЕСТ. В этом режиме IP берется из POST запроса
	
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
		
		//получил номер идентфикатора. Проверяю: надо ли с ним вообще работать?
		//Входные данные:
		$grz='1922384';
		$id_cam=3;//3.2 выезд
	//$id_cam=1;//3.2 въезд
		$gate=Model_cvss::getGateFromCam($id_cam);//это ворота, через которые пытается проехат ГРЗ
		
		echo __('77 ГРЗ :grz проезд в камеру :cam, ворота :gate', array(':grz'=>$grz, ':cam'=>$id_cam, ':gate'=>$gate));
		
		
		
		$content = View::factory('dashboard', array(
			//'garageLst'=>$garageLst,
				
		));
        //$this->template->content = $content;
		
	
		}
		
	

	
	public function action_sendMPT()
	{
		Log::instance()->add(Log::NOTICE, "\r\n 88 start UHF action_sendMPT");
		$t1=microtime(1);
		
		//фиксирую полученные данные из $_OST в лог-файл
		//Log::instance()->add(Log::NOTICE, '85 data receive MPT '.Debug::vars($_POST));// exit;
		$input_data_0=$_POST;//извлекаю данных из полученного пакета
		
		if(!Arr::get($input_data_0, 'test'))
		{
			//если в запросе нет поля test, то беру IP адрес из запроса
			$input_data_0['ip']=Request::$client_ip;
		}
		
		//	Log::instance()->add(Log::NOTICE, '130 data send MPT '.Debug::vars($input_data_0)); //exit;
		
		$input_data = $input_data_0;
		
		Log::instance()->add(Log::NOTICE, '114 MPT получил данные :data', array(':data'=>Debug::vars($input_data)));
		//Log::instance()->add(Log::NOTICE, '81 Получил данные UHF '. Debug::vars($input_data));
		$post=Validation::factory($input_data);
		$post->rule('ch', 'not_empty')//номер канала должен быть 
					->rule('ch', 'digit')
					->rule('ip', 'not_empty')//IP контроллера должен быть
					->rule('ip', 'ip')
					->rule('ip', 'Model_cvss::checkIpIsPresent') 
					->rule('key', 'not_empty')//значение ГРЗ
					->rule('key', 'regex', array(':value', '/^[ABCDEF\d]{3,8}+$/')) // https://regex101.com/ строк буквы АНГЛ алфавита
					
					;
				
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '95 Входные данные UHF не полные '. Debug::vars($post->errors()));//вывод номера в лог-файл
			Log::instance()->add(Log::NOTICE, '131 Обработку UHF прекращаю.');//вывод номера в лог-файл
			$this->response->status(400);
			return;
		}
		Log::instance()->add(Log::NOTICE, '140 Валидация UHF выполнена успешно, продолжаю работу.');//вывод номера в лог-файл

		$id_gate=Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch'));
		
		Log::instance()->add(Log::NOTICE, '160 Получены данные ch = :ch, ip=:ip, key= ":key" (:keyDec), gate :gate',
					array( 
						':ip'=>Arr::get($post, 'ip'),
						':ch'=>Arr::get($post, 'ch'),
						':key'=>Arr::get($post, 'key'),
						':keyDec'=>hexdec(Arr::get($post, 'key')),
						':gate'=>$id_gate
						)); 
							
		
		$cvs=new phpCVS($id_gate);
		$identifier=new Identifier(hexdec(Arr::get($post, 'key')));
		
		$result=$this->mainAnalysis($identifier,  $cvs);
		Log::instance()->add(Log::NOTICE, '152   результат работы mainAnalysis' . $result);
		
		
		Log::instance()->add(Log::NOTICE, "185 Stop UHF gate=".$id_gate.", UHF=".Arr::get($post, 'key').", permission =".$result.", total_time=".number_format((microtime(1) - $t1), 3)."\r\n");	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		
		$t2=microtime(1);
		
		$cvs=new phpCVS($id_gate);
		$cvs->grz=hexdec(Arr::get($post, 'key'));//передаю UHF в модель
		$cvs->code_validation=$result;//передаю в модель результат валидации
		
		if(Arr::get($input_data_0, 'test')) Log::instance()->add(Log::NOTICE, '169 режим ТЕСТ, команды на открытие ворот НЕ передаются'); 
			
		$this->gateControl($identifier, $cvs);
		Log::instance()->add(Log::NOTICE, "172 gateControl total_time=".number_format((microtime(1) - $t2), 3)."\r\n");		

	
		$this->response->status(200);
		
	Log::instance()->add(Log::NOTICE, "177 Stop UHF gate=".$cvs->id_gate.", eventcount=0, UHF=".$cvs->grz.", code_validation=".$cvs->code_validation.", total_time=".number_format((microtime(1) - $t1), 3)."\r\n");	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		return;
		
		
	}
	
	
	
	public function action_sendMPT_old()
	{	
		//25.05.2025 совмещаю обработку реальных запросов и тестовых. Это сделано для того, чтобы можно было тестировать систему
		//не используя свойство $this->ip_is_test
		//в тестовом запросе имеется поле test=>1.
		Log::instance()->add(Log::NOTICE, "\r\n 88 start UHF action_sendMPT");
		$t1=microtime(1);
		
		//фиксирую полученные данные из $_OST в лог-файл
		//Log::instance()->add(Log::NOTICE, '85 data receive MPT '.Debug::vars($_POST));// exit;
		$input_data_0=$_POST;//извлекаю данных из полученного пакета
		
		if(!Arr::get($input_data_0, 'test'))
		{
			//если в запросе нет поля test, то беру IP адрес из запроса
			$input_data_0['ip']=Request::$client_ip;
		}
		
		//	Log::instance()->add(Log::NOTICE, '130 data send MPT '.Debug::vars($input_data_0)); //exit;
		
		$input_data = $input_data_0;
		
		
		//Log::instance()->add(Log::NOTICE, '81 Получил данные UHF '. Debug::vars($input_data));
		$post=Validation::factory($input_data);
		$post->rule('ch', 'not_empty')//номер канала должен быть 
					->rule('ch', 'digit')
					->rule('ip', 'not_empty')//IP контроллера должен быть
					->rule('ip', 'ip')
					->rule('ip', 'Model_cvss::checkIpIsPresent') 
					->rule('key', 'not_empty')//значение ГРЗ
					->rule('key', 'regex', array(':value', '/^[ABCDEF\d]{3,8}+$/')) // https://regex101.com/ строк буквы АНГЛ алфавита
					
					;
				
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '95 Входные данные UHF не полные '. Debug::vars($post->errors()));//вывод номера в лог-файл
			Log::instance()->add(Log::NOTICE, '131 Обработку UHF прекращаю.');//вывод номера в лог-файл
			$this->response->status(400);
			return;
		}
		Log::instance()->add(Log::NOTICE, '140 Валидация UHF выполнена успешно, продолжаю работу.');//вывод номера в лог-файл
		
		
		//Определяю ворота
		$id_gate=Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch'));
		Log::instance()->add(Log::NOTICE, '160 Получены данные ch = :ch, ip=:ip, key= ":key" (:keyDec), gate :gate',
					array( 
						':ip'=>Arr::get($post, 'ip'),
						':ch'=>Arr::get($post, 'ch'),
						':key'=>Arr::get($post, 'key'),
						':keyDec'=>hexdec(Arr::get($post, 'key')),
						':gate'=>$id_gate
						)); 
		
	
	//Этап 2.
	//Создаю экземпляр класса. Это необходимо для проверки разрешения на въезд
	//Результатом этапа являются параметры $cvs->code_validation
	//В процессе валидации заполняются таблицы базы данных, автоматически фиксируя проезд.
		$cvs=new phpCVS($id_gate);
		$cvs->grz=hexdec(Arr::get($input_data, 'key'));//передаю UHF в модель
		
		Log::instance()->add(Log::NOTICE, "148 UHF befor check total_time=".number_format((microtime(1) - $t1), 3)."\r\n");	
		// ПРОВЕРКА: МОЖНО ЛИ ВЪЕЗЖАТЬ???
		$cvs->check(); 


		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		
		Log::instance()->add(Log::NOTICE, '190  cvs после check()  id_gate = :id_gate ip=:ip channel=:channel id_dev=:id_dev key=:key direct=:direct validate=:validate, total_time=:total_time ', 
					array(':id_gate'=>$cvs->id_gate, 
							':ip'=>$cvs->box_ip,
							':channel'=>$cvs->ch,
							':id_dev'=>$cvs->id_dev,
							':key'=>$cvs->grz,
							':direct'=>$direct,
							':validate'=>$cvs->code_validation,
							':total_time'=>number_format((microtime(1) - $t1), 3),
							));		
		
			$this->response->status(200);
		Log::instance()->add(Log::NOTICE, "169 after response total_time=".number_format((microtime(1) - $t1), 3)."\r\n");		
			$t2=microtime(1);
		//============================= Управление воротами!!!
		//если это не режим ТЕСТ, то можно передавать управление воротам.

		if(!Arr::get($input_data_0, 'test'))
		{
			
			$this->gateControl($identifier, $cvs);
		} else {
			
			Log::instance()->add(Log::NOTICE, '177 режим ТЕСТ, команды на открытие ворот НЕ передаются'); 
		}
		
		Log::instance()->add(Log::NOTICE, "183 gateControl total_time=".number_format((microtime(1) - $t2), 3)."\r\n");		

	
		$this->response->status(200);
		
	Log::instance()->add(Log::NOTICE, "185 Stop UHF gate=".$cvs->id_gate.", eventcount=0, UHF=".$cvs->grz.", code_validation=".$cvs->code_validation.", total_time=".number_format((microtime(1) - $t1), 3)."\r\n");	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		return;
	}

	
		
	/**20.07.2025
	*	Процедура управления воротами
	*
	*/
	public function gateControl(Identifier $identifier, phpCVS $cvs)
	{
		//===============================================================
		//Этап 3. Проверка на работу в режиме ТЕСТ	
		//если включен режим Тест (в конфигураторе, файл config), то надо возращать результат 145 (прохода в режиме Тест).
		//результатом работы этого этапа является:
		//коррекция сообщения для вывода на табло cvs->eventdMess 
	   //Log::instance()->add(Log::NOTICE, '223 start gateControl '.Debug::vars($cvs)); 
		$t1=microtime(1);	
	   $config = Kohana::$config->load('config');
		if(Arr::get($config, 'testMode'))
		{
			$cvs->code_validation=50;//разрешить проезд
			//$cvs->code_validation=145;//разрешить проезд
			$cvs->eventdMess='OPEN in TEST MODE:'.date('H:i:s', time());
			Log::instance()->add(Log::NOTICE, '196 включен режим testMode. Пропускаю всех вподряд. Validation '.$cvs->code_validation); 
				
				$direct='выезд';
				if($cvs->isEnter) $direct='въезд';
		
			Log::instance()->add(Log::NOTICE, '200 TEST MODE id_gate = :id_gate ip=:ip channel=:channel id_dev=:id_dev key=:key direct=:direct validate=:validate eventdMess=:eventdMess ', 
					array(':id_gate'=>$cvs->id_gate, 
							':ip'=>$cvs->box_ip,
							':channel'=>$cvs->ch,
							':id_dev'=>$cvs->id_dev,
							':key'=>$cvs->grz,
							':direct'=>$direct,
							':validate'=>$cvs->code_validation,
							));	
			
		}
		
		//===============================================================
		//Этап 4: опеределение режимов работы реле. Возможно, надо делать что-то более хитрое, чем просто щелкнуть реле.
		//результатом работы этого этапа является:
		//коррекция свойства ворот cvs->eventdMess 
		//теперь определяю каким реле щелкать.
	
		//получаю список ворот (из файла config), которые работают в реверсивном режиме (одни ворота и на въезд, и на выезд)
		//от этого лучше отказаться, и монтировать выходы реле параллельно.
		// но пока у нас получается режим "Одна дверь на два считывателя", реализованная программно.
		$reverseList=Arr::get($config, 'reverseGate');
				
		
		//Тут надо помнить, что ворота cvs рассматриваются как комплексное устройство, и у него нет понятия двери (канал, реле), а есть режим работы mode
		//при выполнеии команды opendoor реле работают в зависимости от режима mode
		//$mode ==0 открываю дверь 0
		//$mode ==1 открываю дверь 1
		//$mode ==2 открываю все двери поочередно
		//$mode ==3 открываю все двери сразу одной командой
		//этот режим и надо явно указать для правильного управления воротами.
		
		if(in_array($cvs->id_gate,$reverseList))
		{
			$cvs->mode = 2;//если режим работы Реверсивные ворота, то щелакаю обоими реле
			Log::instance()->add(Log::NOTICE, '215 реверсивный режим для ворот '.$cvs->id_gate.'. открываю оба реле'); 
		} else {
			$cvs->mode = $cvs->ch;//если режим НЕ реверсивный, то режим равен номеру канала
			Log::instance()->add(Log::NOTICE, '219 Не реверсивный режим. открываю реле '. $cvs->ch); 
		}
	
		Log::instance()->add(Log::NOTICE, '220-1'.Debug::vars($cvs)); 
	
		
		
		//===============================================================
		
		
		$events= new Events();
		$events->eventCode=$cvs->code_validation;
		$events->grz=$cvs->grz;
		$events->id_gate=$cvs->id_gate;
		$events->addEventRow();

		//Этап 5: управление внешими стройствами по результатам валидации
		
		$tablo=new phpTablo($cvs->tablo_ip, $cvs->tablo_port);//работа в режиме TCP 
		
			//сохраняю в семафор с номером табло номер обрабатываемого события
		//Model::factory('mpt')->setSemafor('lastevent'.$cvs->cam, $cvs_event_id);
		
		//обработка кодов валидации
		//Log::instance()->add(Log::NOTICE, '256-256  '.Debug::vars($cvs)); 
		
		
		
		 switch($cvs->code_validation){
		
		
			case events::WOK : //повторный проезд разрешен
			case 50 : //проезда разрешен
				$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);//создаю экземпляр контроллера МПТ
				//Log::instance()->add(Log::NOTICE, '307-307  '.Debug::vars($mpt));
				if(!Arr::get($config, 'debug')) $mpt->openGate($cvs->mode);// даю команду открыть ворота
			
				$i=0;
					while($mpt->result !='OK' AND $i<10)// делать до 10 попыток
					{
						Log::instance()->add(Log::DEBUG, '155 Команда открыть ворота '.$cvs->box_ip.':'.$cvs->box_port.' выполнена неудачно: '.$mpt->result.' desc '.$mpt->edesc.'. timestamp '.microtime(true).'. Команда Открыть ворота повторяется еще раз, попытка '.$i.' time_from_start='.number_format((microtime(1) - $t1), 3));
						if(!Arr::get($config, 'debug')) $mpt->openGate($cvs->mode);// открыть ворота
						$i++;
					}
					//Log::instance()->add(Log::NOTICE, '004_150 Событие 50. Результат выполнения команды openGate '.$cvs->box_ip.':'.$cvs->box_port.' result='.$mpt->result.', desc='.$mpt->edesc.'  после '. $i .' попыток time_from_start='.number_format((microtime(1) - $t1), 3));		
				if($mpt->result == 'Err') 
				{
					//Log::instance()->add(Log::NOTICE, '138 Событие 50. Не смог открыть ворота в течении 10 попыток. Видеокамера '.$cvs->cam.' ('.$direct.') ГРЗ '.$cvs->grz.' контролер IP='.$cvs->box_ip.':'.$cvs->box_port.' Режим шлюза '.$cvs->mode.' Ответ '.$mpt->result.' edesc '.$mpt->edesc);		
				} else 
				{
					//Log::instance()->add(Log::NOTICE, '004_64 Событие 50. Ответ контроллера после повторной команды '.$mpt->result.' edesc '.$mpt->edesc.'  после '. $i .' попыток.');	
						//Ворота открылись успешно, надо добавлять ГРЗ в таблицу inside в зависиммочти от направления движения
						
						$inside= new insideList;
						$inside->id_card=$identifier->id;
						$inside->id_pep=$identifier->id_pep;
						$inside->id_parking=$cvs->id_parking;;
						if($cvs->isEnter==1) {// въезд
							$inside->addToInside();
						} else {
							$inside->delFromInside();
						}
						 
						
				}
					//теперь занимаюсь выводом информации на табло
				
				$tablo->command='clearTablo';
				if(!Arr::get($config, 'debug')) $tablo->execute(); 		
				//Log::instance()->add(Log::NOTICE, '152 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
					
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x02";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '158 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x02\x01";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '164 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
				
				
			 break;
			 case 46 : //неизвестная карта
					//для неизвестной карты открывать ворота НЕ надо, поэтому экземпляр МПТ не создается.
					//работаю только с табло
				$tablo->command='clearTablo';
				if(!Arr::get($config, 'debug')) $tablo->execute(); 	
				//Log::instance()->add(Log::NOTICE, '173 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
							
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x03";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '180 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x03\x01";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '187 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				
			 break;
			 
			case 65 : //проезд запрещен (нет прав на проезд в этот гараж)
				//тут же можно сделать проверку: может, имеется разрешение на въезд в другой паркинг?
					
				$tablo->command='clearTablo';
				if(!Arr::get($config, 'debug')) $tablo->execute(); 		
				//Log::instance()->add(Log::NOTICE, '203 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x04";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '203 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x04\x01";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '216 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
			
			
				
			 break;
			 
			 case 81 : //проезд запрещен (нет мест в гаражах)
				//тут же можно сделать проверку свободных мест на другой парковке	
					
				$tablo->command='clearTablo';
				if(!Arr::get($config, 'debug')) $tablo->execute(); 	
				//Log::instance()->add(Log::NOTICE, '232 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
					
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x06";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '239 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x06\x01";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '246 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				
			 break;
			 
			 
			 default: //код валидации не обрабатывается.
			 
				Log::instance()->add(Log::NOTICE, "244 Неизвестный код валидации ". $cvs->code_validation);
			
			 
			 break;
		 } 
		
	}
	
	/**20.07.2025 Открытие ворот по http команде
	*
	*
	*/
	public function action_opengate()
	{
		
		
		//$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		Log::instance()->add(Log::NOTICE, '489 '. Debug::vars($_POST));
		//Log::instance()->add(Log::NOTICE, '490 '. Debug::vars($input_data_0));
		$id_gate=Arr::get($_POST, 'id');
		
		
		$cvs=new phpCVS($id_gate);// сделал экземпляр, чтобы получить IP, port, и номер канала ch
		
		$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);//создаю экземпляр контроллера МПТ
		$result=$mpt->openGate($cvs->ch);// даю команду открыть ворота. Результат может быть разный: от  подключения к контроллера до ошибки в протоколе.
		
		Log::instance()->add(Log::NOTICE, '499 открыл ворота id=:id ip=:ip port=:port ch=:ch', array(':id'=>$id_gate, ':ip'=>$cvs->box_ip, ':port'=>$cvs->box_port, ':ch'=>$cvs->ch));
		Log::instance()->add(Log::NOTICE, '414 результат выполнения команды открытия ворот :res', array(':res'=>Debug::vars($result)));
		//надо вернуть ответ
		$this->response
            ->headers('Content-Type', 'application/json')
            ->body(json_encode($result));
		
		//послать команду Открыть дверь.
		
	}
	
	
		
	public function action_opengate_2025_06_13()
	{
		
		
		$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		
		$id_gate=Arr::get($input_data_0, 'id');
		$cvs=new phpCVS($id_gate);// сделал экземпляр, чтобы получить IP, port, и номер канала ch
		
		$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);//создаю экземпляр контроллера МПТ
		$mpt->openGate($cvs->ch);// даю команду открыть ворота
		//Log::instance()->add(Log::NOTICE, '426 '. Debug::vars($mpt));
		Log::instance()->add(Log::NOTICE, '427 открыл ворота :id :ip :port :ch', array(':id'=>$id_gate, ':ip'=>$cvs->box_ip, ':port'=>$cvs->box_port, ':ch'=>$cvs->ch));
		
		//послать команду Открыть дверь.
		
	}
	
	
	public function action_exec()
	{
		$t1=microtime(1);
		Log::instance()->add(Log::NOTICE, "\r\n 425 start CVS action_exec");
		$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
			
		$input_data=Arr::get($input_data_0, 'plate');
		
		//Валидация данных: все ли правильно?
		$post=Validation::factory($input_data);
		$post->rule('id', 'not_empty')//номер события
					->rule('id', 'digit')
					->rule('id', 'Model_cvss::isEventUniq') //событие уникальное, не совпадает с ранее обработанным
					->rule('camera', 'not_empty')//номер видеокамеры
					->rule('camera', 'digit')
					->rule('camera', 'Model_cvss::checkCamIsPresent') 
					->rule('plate', 'not_empty')//значение ГРЗ
					->rule('plate', 'regex', array(':value', '/^[A-Za-z\d]{3,10}+$/')) // https://regex101.com/ строго буквы АНГЛ алфавита
					
					;
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '125 Входные данные cvs не полные '. Debug::vars($post->errors())); //exit;//вывод номера в лог-файл
			//echo Debug::vars('126 валидация прошла с ошибкой', $post->errors());exit;
			$this->response->status(200);
			exit;
		}
		Log::instance()->add(Log::NOTICE, '487 Начинаю анализ ГРЗ.');
		echo Debug::vars('488', $this->mainAnalysis());exit;
	}
	
	
	//29.04.2025 прием сообщений от системы распознавания CVS
	//и их последующая обработка
	//особенность: данные от cvs передаются в массиве POST в виде двумерного массива
	// "messageId" => <small>string</small><span>(36)</span> "dd03e3d2-ac2d-485a-8c5e-95c5d6ec869c"
    //"plate" => <small>array</small><span>(16)</span> <span>(
	public function action_exec_old()
	{	
		$t1=microtime(1);
		Log::instance()->add(Log::NOTICE, "\r\n 425 start CVS action_exec");
		
		
		
		//$input_data_0=$_POST;//извлекаю данных из полученного пакета
		$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
			
		//$input_data=json_decode(Arr::get($input_data_0, 'plate'), true);
		
		$input_data=Arr::get($input_data_0, 'plate');
		
		//Валидация данных: все ли правильно?
		$post=Validation::factory($input_data);
		
		
		$post->rule('id', 'not_empty')//номер события
					->rule('id', 'digit')
					->rule('id', 'Model_cvss::isEventUniq') //событие уникальное, не совпадает с ранее обработанным
					->rule('camera', 'not_empty')//номер видеокамеры
					->rule('camera', 'digit')
					->rule('camera', 'Model_cvss::checkCamIsPresent') 
					->rule('plate', 'not_empty')//значение ГРЗ
					->rule('plate', 'regex', array(':value', '/^[A-Za-z\d]{3,10}+$/')) // https://regex101.com/ строго буквы АНГЛ алфавита
					
					;
		//Log::instance()->add(Log::NOTICE, '460 контроль '. Debug::vars($input_data));  
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '125 Входные данные cvs не полные '. Debug::vars($post->errors())); //exit;//вывод номера в лог-файл
			//echo Debug::vars('126 валидация прошла с ошибкой', $post->errors());exit;
			$this->response->status(200);
			return;
		}
	
		
		
	//====================			
		$id_gate = Model::factory('mpt')->getIdGateFromCam(Arr::get($input_data, 'camera'));//получил номер ворот

		//Log::instance()->add(Log::NOTICE, '133 получил id_gate = :id_gate по номеру камеры=:cam.', array(':id_gate'=>$id_gate, ':cam'=>Arr::get($input_data, 'camera')));
	
				
		
		 //$cvs=new phpCVS(Arr::get($input_data, 'camera'));
		 $cvs=new phpCVS(Model_cvss::getGateFromCam(Arr::get($post, 'camera')));
		 //Log::instance()->add(Log::NOTICE, '117 '. Debug::vars($cvs));
		 //echo Debug::vars('168', $cvs);exit;
		
		$cvs->grz=Arr::get($input_data, 'plate');//передаю ГРЗ в модель
		$cvs->timeStamp=Arr::get($input_data, 'dateTime', -1);//передал в модель метку времени
		$cvs_event_id=Arr::get($input_data, 'id');//передал в модель номер события
		
		// вызов процесса валидации. Результат валидации сохраняется в $cvs как значения параметров
		//$cvs->check(); 
		$cvs->checkPHPin(); 
		//echo Debug::vars('176', $cvs);exit;
		
		//Log::instance()->add(Log::NOTICE, '117 '. Debug::vars($cvs));
		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		$dt=0;
		$dt=time()-strtotime($cvs->timeStamp);
		
		
		Log::instance()->add(Log::NOTICE, '593 cvs id_gate = :id_gate cam=:cam grz=:grz direct=:direct validate=:validate ', 
					array(':id_gate'=>$id_gate, 
							':cam'=>Arr::get($input_data, 'camera'),
							':cam_darect'=>Arr::get($input_data, 'direction'),
							':grz'=>Arr::get($input_data, 'plate'),
							':direct'=>$direct,
							':validate'=>$cvs->code_validation,
							));
		$this->response->status(200);
		//return;
		
		
	//============================= Управление воротами!!!
		//если это не режим ТЕСТ, то можно передавать управление воротам.

		if(!Arr::get($input_data, 'test'))
		{
			
			$this->gateControl($cvs);
		} else {
			
			Log::instance()->add(Log::NOTICE, '555 режим ТЕСТ, команды на открытие ворот НЕ передаются'); 
		}
	
	
	$t2=microtime(1);
	

		$this->response->status(200);
		
		//сохранение файла с фотографией машины
		
		
		Model::factory('mpt')->savePhoto( Arr::get($input_data, 'plate'), base64_decode(Arr::get($input_data, 'image'))); //debug
		
		//Log::instance()->add(Log::NOTICE, "273 Сохранение фото ". Arr::get($input_data, 'plate').', сохранено за '.(microtime(1) - $t2));
		
		
	Log::instance()->add(Log::NOTICE, "723 Stop cvs cam=".$cvs->cam.", cvs=". Arr::get($input_data, 'id').', grz='.$cvs->grz.', code_validation='.$cvs->code_validation.', total_time='.number_format((microtime(1) - $t1), 3)."\r\n");	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		return;
	}


	/**20.07/2025
	*
	*
	*/
	//
	
	public function mainAnalysis(Identifier $identifier, phpCVS $cvs)
	{
		// Log::instance()->add(Log::NOTICE, '729 :data', array(':data'=>Debug::vars($identifier)));
		// Log::instance()->add(Log::NOTICE, '730 :data', array(':data'=>Debug::vars($cvs)));
		Log::instance()->add(Log::NOTICE, '614 Начал работу анализатора для key='.$identifier->id);
		//начинаю анализ
		//начинаю проверку полученного идентификатора. grz
		//$identifier=new Identifier($grz);
		//Log::instance()->add(Log::NOTICE, '672 Identifier'. Debug::vars($identifier));
		if(!$identifier->status==Identifier::VALID){
			//зафиксировать в журнале отказ от дальнейшей обработки
			Log::instance()->add(Log::NOTICE, '675 карта :key не валидна status=:status. Завершаю обработку.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
			return Events::UNKNOWNCARD;
			//exit;
		} 
		Log::instance()->add(Log::NOTICE, '683 карта :key валидна status=:status. Продолжаю обработку.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
		//Теперь надо собрать данные для дальнейшего анализа
		
		//надо иметь информацию и о воротах, откуда пришел ГРЗ
			//Эта часть реализована в phpCVS
			//$cvs=new phpCVS($id_gate);
			//echo Debug::vars('106 информация о воротах', $cvs);
			
			//надо иметь информацию о гараже.
			$garage=new Garage($identifier->id_garage);//это - модель гаража, куда едет ГРЗ. Этот параметр беретс из $identifier->id_garage, но для отладки использую фиксировнное значение  $id_garage
			//echo Debug::vars('109 ГРЗ едет вот в этот гараж', $garage);
		
		
		//если у ГРЗ есть гараж, то надо делать только проверки свободных мест.
		//если же гаража нет, то надо проверять категорию доступа.		
		if(is_null($identifier->id_garage)) //гаража нет.
		{
			Log::instance()->add(Log::NOTICE, '706 карта :key гаража не имеет.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
			//тут надо вызвать метод анализа без гаража, только по категории доступа.
			//результат анализа - можно или нельзя!
			exit;

		} 
		Log::instance()->add(Log::NOTICE, '718 карта :key имеет гараж :garage.',
				array(
					':key'=>$identifier->id,
					':garage'=>$identifier->id_garage
					));
			//тут надо вызвать метод анализа при наличии гаража.
			//результат анализа:
			//-въезд разрешен (т.е. места есть).
			//въезд запрещен (т.е. мест нет)
			//Log::instance()->add(Log::NOTICE, '727 garage'. Debug::vars($garage));
			if(!$cvs->checkAccess($garage->id_parking)){ //тут $garage->id_parking - список парковок, на которых расположены машиноместа гаража
				//ворота, куда подъехал автомобиль, не содержит парковочных мест гаража, разрешенных этому ГРЗ.
				//въезд запрещен
				Log::instance()->add(Log::NOTICE, '731 карта :key подъехала к чужим воротам.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
				
				// $event->eventCode=events::ACCESSDENIED;
				// $event->grz=$grz;
				// $event->addEventRow();
				return Events::ACCESSDENIED;				
				//echo Debug::vars('134 ошибся воротами!');
				//exit;
			} 
				Log::instance()->add(Log::NOTICE, '744 карта :key подъехала к своим воротам.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
				
				//въезд разрешен, анализирую загрузку гаражей
				if($cvs->isEnter ) {//если въезд
				
				if(insideList::checkGrzInParking($identifier->id)) //он уже на парковке
				{
					return Events::WOK;	//повторный въезд
					
				}
				
				
					if($cvs->checkPHPin($garage)){//если в гараже есть места или именно этот ГРЗ уже стоит в гараже
						
						//въезд разрешен
						$permission = true;
						return Events::OK;	
					} else {
						//въезд запрещен (нет мест)
						$permission = false;
						return Events::CARLIMITEXCEEDED;	
					}
				} else {//если выезд
					if($cvs->checkPHPout($garage)){
						
						//выезд разрешен
						$permission = true;
						return Events::OK;
					} else {
						
						//выезд запрещен
						$permission = false;
						return Events::ACCESSDENIED;	
					}
				}
 	
			
		Log::instance()->add(Log::NOTICE, '808 Анализа завершен с результатом :data', array(':data'=>$permission));;

	}


}
