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
		
		
		$content = View::factory('dashboard', array(
			//'garageLst'=>$garageLst,
				
		));
        //$this->template->content = $content;
		
	
	}
	
	/**прием данных от МПТ. Тут могут быть данные о полученном RFID и о измении состояния индуктивной петли loop.
	*
	*
	*/
	
	public function action_sendMPT()
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
		
		$post=Validation::factory($input_data);
		$post->rule('ip', 'not_empty')//IP контроллера должен быть
					->rule('ip', 'ip')
					->rule('ip', 'Model_cvss::checkIpIsPresent') 
					;
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '116 получены данные от незарегистрированного IP адреса. '. Debug::vars($post->errors()));//вывод номера в лог-файл
			Log::instance()->add(Log::NOTICE, '131 Обработку данных прекращаю.');//вывод номера в лог-файл
			$this->response->status(400);
			return;
		}
		//определение источника: rfid или loop
		if(array_key_exists('key', $_POST)) 
		{
			//вставляю результат в базу данных S2, чтобы зафиксировать результат валидации (с меткой времени).
			$cvs = $this->rfidHandler($input_data);//источник данных - rfid
			// вставка в S2!!!
			//вызов обработчика S2. возможно, что машина уже стоит на индуктивной петле, и можно сразу открывать ворота.
			$this->gateControlS2('rfid');
		}
		if(array_key_exists('pin', $_POST)) //источник данных - loop
		{
			$this->loopHandler();//обработка сигналов от индукционной петли. Например, включить или выключить индикацию
			//надо включить индикацию на табло, чтобы показать, что "видим" автомобиль.
			//вызов обработчика S2: можно ли проезжать? или отказ?
			$this->gateControlS2('loop');
			
		}
		
		Log::instance()->add(Log::NOTICE, '136 sendMPT завершил свою работу.');
			
	}
	
	
	/**5.06.2025 Обработка результата с учетом состояния индуктивной петли и ранее полученных данных
	* необходимо обработать набор данных	uhf | loop | plate с возможными временными интервалами.
	*
	*/
	function gateControlS2($mode)
	{
		switch ($mode){
			case 'rfid':
				//проверить: если машина на петле, и код валидации 50, то открывать ворота.
				//если код не 50, то выводить причину отказа
			
			break;
			case 'loop':
				//есть ли данные от rfid или uhf за допустимый отрезок времени?
				
			
			break;
			case 'plate':
				//проверить: если машина на петле, и код валидации 50, то открывать ворота.
				//если код не 50, то выводить причину отказа
			
			break;
			default:
				//вывод сообщения об ошибке
			
			break;
			
		}
		
	}
	
	
	/**обработка сигнала от идукционной петли
	*
	*/
	public function loopHandler()
	{
		
		
	}
	
	
	
	/** 5.06.2025 Обработка полученного RFID
	*@input массив данных, содержащий key и номер канала
	*@output запись результата в промежуточную таблицу s2.sqlite
	*/
	
	public function rfidHandler($input_data)
	{
		//Log::instance()->add(Log::NOTICE, '81 Получил данные UHF '. Debug::vars($input_data));
		$t1=microtime(true);
		$post=Validation::factory($input_data);
		$post->rule('ch', 'not_empty')//номер канала должен быть 
					->rule('ch', 'digit')
					->rule('key', 'not_empty')//значение ГРЗ
					->rule('key', 'regex', array(':value', '/^[ABCDEF\d]{3,8}+$/')) // https://regex101.com/ строк буквы АНГЛ алфавита
					
					;
				
		if(!$post->check())
		{
			Log::instance()->add(Log::NOTICE, '146 Входные данные UHF не полные '. Debug::vars($post->errors()));//вывод ошибки валидации в лог-файл
			Log::instance()->add(Log::NOTICE, '147 Обработку UHF прекращаю.');//вывод номера в лог-файл
			$this->response->status(400);
			return;
		}
		Log::instance()->add(Log::NOTICE, '140 Валидация RFID выполнена успешно, продолжаю работу.');//вывод номера в лог-файл
		
		
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
	//Создаю экземпляр класса ворот. Это необходимо для проверки разрешения на въезд
	//Результатом этапа являются параметры $cvs->code_validation
	//5.06.2025 НЕТ!!! В процессе валидации заполняются таблицы базы данных, автоматически фиксируя проезд.-- как раз НЕ фиксируется в БД ничего!!! Только проверка: можно ли выехать? обработка результат осуществляется в других процедурах
		$cvs=new phpCVS($id_gate);
		//$cvs->grz=hexdec(Arr::get($input_data, 'key'));//передаю UHF в модель
		$cvs->grz=(Arr::get($input_data, 'key'));//передаю UHF в модель
		
		
		// ПРОВЕРКА: МОЖНО ЛИ ВЪЕЗЖАТЬ???
		//$cvs->check(); 
		$cvs->validation(); 

		Log::instance()->add(Log::NOTICE, '154 '. Debug::vars($cvs));
		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		
		Log::instance()->add(Log::NOTICE, '190  cvs после check()  id_gate = :id_gate ip=:ip channel=:channel id_dev=:id_dev key=:key direct=:direct validate=:validate ', 
					array(':id_gate'=>$cvs->id_gate, 
							':ip'=>$cvs->box_ip,
							':channel'=>$cvs->ch,
							':id_dev'=>$cvs->id_dev,
							':key'=>$cvs->grz,
							':direct'=>$direct,
							':validate'=>$cvs->code_validation,
							));		
		
			$this->response->status(200);
	
		//============================= Управление воротами!!!
		//если это не режим ТЕСТ, то можно передавать управление воротам.

		if(!Arr::get($input_data, 'test'))
		{
			
			$this->gateControl($cvs);
		} else {
			
			Log::instance()->add(Log::NOTICE, '179 режим ТЕСТ, команды на открытие ворот НЕ передаются'); 
		}
		
		$t2=microtime(1);

	
		$this->response->status(200);
		
	Log::instance()->add(Log::NOTICE, "387 Stop UHF gate=".$cvs->id_gate.", UHF=".$cvs->grz.", code_validation=".$cvs->code_validation.", total_time=".number_format((microtime(1) - $t1), 3)."\r\n");	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		return;
	}

	
	
	public function gateControl(phpCVS $cvs)
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
	
		//Log::instance()->add(Log::NOTICE, '220-1'.Debug::vars($cvs)); 
	
		
		
		//===============================================================
		//Этап 5: управление внешими стройствами по результатам валидации
		
		$tablo=new phpTablo($cvs->tablo_ip, $cvs->tablo_port);//работа в режиме TCP 
		
			//сохраняю в семафор с номером табло номер обрабатываемого события
		//Model::factory('mpt')->setSemafor('lastevent'.$cvs->cam, $cvs_event_id);
		
		//обработка кодов валидации
		//Log::instance()->add(Log::NOTICE, '256-256  '.Debug::vars($cvs)); 
		
		
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
					//Log::instance()->add(Log::NOTICE, '004_150 Событие 50. Результат выполнения команды openGate '.$cvs->box_ip.':'.$cvs->box_port.' result='.$mpt->result.', desc='.$mpt->edesc.'  после '. $i .' попыток time_from_start='.number_format((microtime(1) - $t1), 3));		
				if($mpt->result == 'Err') 
				{
					//Log::instance()->add(Log::NOTICE, '138 Событие 50. Не смог открыть ворота в течении 10 попыток. Видеокамера '.$cvs->cam.' ('.$direct.') ГРЗ '.$cvs->grz.' контролер IP='.$cvs->box_ip.':'.$cvs->box_port.' Режим шлюза '.$cvs->mode.' Ответ '.$mpt->result.' edesc '.$mpt->edesc);		
				} else 
				{
					//Log::instance()->add(Log::NOTICE, '004_64 Событие 50. Ответ контроллера после повторной команды '.$mpt->result.' edesc '.$mpt->edesc.'  после '. $i .' попыток.');		
				}
					//теперь занимаюсь выводом информации на табло
				
				$tablo->command='clearTablo';
				$tablo->execute(); 		
				//Log::instance()->add(Log::NOTICE, '152 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
					
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x02";
				$tablo->execute();
				//Log::instance()->add(Log::NOTICE, '158 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x02\x01";
				$tablo->execute();
				//Log::instance()->add(Log::NOTICE, '164 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
				
			 break;
			 case 46 : //неизвестная карта
					//для неизвестной карты открывать ворота НЕ надо, поэтому экземпляр МПТ не создается.
					//работаю только с табло
				$tablo->command='clearTablo';
				$tablo->execute(); 	
				//Log::instance()->add(Log::NOTICE, '173 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
							
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x03";
				$tablo->execute();
				//Log::instance()->add(Log::NOTICE, '180 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x03\x01";
				$tablo->execute();
				//Log::instance()->add(Log::NOTICE, '187 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				
			 break;
			 
			case 65 : //проезд запрещен (нет прав на проезд в этот гараж)
				//тут же можно сделать проверку: может, имеется разрешение на въезд в другой паркинг?
					
				$tablo->command='clearTablo';
				$tablo->execute(); 		
				//Log::instance()->add(Log::NOTICE, '203 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x04";
				$tablo->execute();
				//Log::instance()->add(Log::NOTICE, '203 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x04\x01";
				$tablo->execute();
				//Log::instance()->add(Log::NOTICE, '216 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
			
			
				
			 break;
			 
			 case 81 : //проезд запрещен (нет мест в гаражах)
				//тут же можно сделать проверку свободных мест на другой парковке	
					
				$tablo->command='clearTablo';
				$tablo->execute(); 	
				//Log::instance()->add(Log::NOTICE, '232 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
					
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$cvs->grz;
				$tablo->coordinate="\x00\x00\x06";
				$tablo->execute();
				//Log::instance()->add(Log::NOTICE, '239 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x06\x01";
				$tablo->execute();
				//Log::instance()->add(Log::NOTICE, '246 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				
			 break;
			 
			 
			 default: //код валидации не обрабатывается.
			 
				Log::instance()->add(Log::NOTICE, "244 Неизвестный код валидации ". $cvs->code_validation);
			
			 
			 break;
		 } 
		
	}
	
		
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
		Log::instance()->add(Log::NOTICE, '500 результат выполнения команды открытия ворот :res', array(':res'=>Debug::vars($result)));
		//надо вернуть ответ
		$this->response
            ->headers('Content-Type', 'application/json')
            ->body(json_encode($result));
		
		//послать команду Открыть дверь.
		
	}
	
	
	//29.04.2025 прием сообщений от системы распознавания CVS
	//и их последующая обработка
	//особенность: данные от cvs передаются в массиве POST в виде двумерного массива
	// "messageId" => <small>string</small><span>(36)</span> "dd03e3d2-ac2d-485a-8c5e-95c5d6ec869c"
    //"plate" => <small>array</small><span>(16)</span> <span>(
	public function action_exec()
	{	
		$t1=microtime(1);
		Log::instance()->add(Log::NOTICE, "\r\n 425 start CVS action_exec");
		
		
		if(!$_POST){
			
			Log::instance()->add(Log::NOTICE, '430 нет данных в массиве POST, завершаю работу '. Debug::vars($_POST));
			return;
		}

		$input_data_0=$_POST;//извлекаю данных из полученного пакета
		
			
		$input_data=json_decode(Arr::get($input_data_0, 'plate'), true);
		
		
		
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
		//	Log::instance()->add(Log::NOTICE, '460 контроль '. Debug::vars($post->check())); exit; 
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '125 Входные данные cvs не полные '. Debug::vars($post->errors())); //exit;//вывод номера в лог-файл
			//echo Debug::vars('126 валидация прошла с ошибкой', $post->errors());exit;
			$this->response->status(200);
			return;
		}
		
		//Этап проверка номера события: не повторяется ли? ================================================
		//"Разборки с повтором номер событий от cvs. было так, что cvs два раза присылал один и тот же номер.
		
		//валидация данных прошла успешно, продолжаю обработку
		echo Debug::vars('129 Валидация данных ГРЗ данных прошла успешно'); //exit;
		//$last_event=Model::factory('mpt')->getSemafor('lastevent');//номер последнего обработанного события. Номер взят из файла временного.
	
		//Log::instance()->add(Log::NOTICE, '221 last_event '. $last_event);//вывод номера в лог-файл
		
		//if($last_event - Arr::get($input_data, 'id') > 1) Log::instance()->add(Log::NOTICE, '78 Потеряны соыбтия с old по new.', array('old'=>$last_event, 'new'=>Arr::get($input_data, 'id') ));
	/* 	Log::instance()->add(Log::NOTICE, "001 Start cvs id_event=".Arr::get($input_data, 'id').',
					grz '.Arr::get($input_data, 'plate').',
					camera '.Arr::get($input_data, 'camera').',
					timestamp '.microtime(true).',
					time_from_start='.number_format((microtime(1) - $t1), 3)); */
		
			
				
		//сохраняю номер полученного пакета от CVS
		
		//echo Debug::vars('150');exit;
		
		Model::factory('mpt')->setSemafor('lastevent', Arr::get($input_data, 'id'));//сохранил последний номер обрабатываемого события
		
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
		$cvs->validation(); 
		//echo Debug::vars('176', $cvs);exit;
		
		Log::instance()->add(Log::NOTICE, '510 '. Debug::vars($cvs));
		
		$direct='выезд';
		if($cvs->isEnter) $direct='въезд';
		$dt=0;
		$dt=time()-strtotime($cvs->timeStamp);
		
		
		Log::instance()->add(Log::NOTICE, '593 cvs id_gate = :id_gate cam=:cam grz=:grz direct=:direct validate=:validate ', 
					array(':id_gate'=>$id_gate, 
							':cam'=>Arr::get($input_data, 'camera'),
							':grz'=>Arr::get($input_data, 'plate'),
							':direct'=>$direct,
							':validate'=>$cvs->code_validation,
							));
		$this->response->status(200);
		//обработка ГРЗ завершена, результат валидации известен и хранится в переменной $cvs
		//============================= Управление воротами!!!
		//если это не режим ТЕСТ, то можно передавать управление воротам.

		if(!Arr::get($input_data, 'test'))
		{
			
			$this->gateControl($cvs);
		} else {
			
			Log::instance()->add(Log::NOTICE, '179 режим ТЕСТ, команды на открытие ворот НЕ передаются'); 
		}
	
	
		$t2=microtime(1);

	
		$this->response->status(200);
		
		//сохранение файла с фотографией машины
		
		
//		Model::factory('mpt')->savePhoto( Arr::get($input_data, 'plate'), base64_decode(Arr::get($input_data, 'image'))); //debug
		
		//Log::instance()->add(Log::NOTICE, "273 Сохранение фото ". Arr::get($input_data, 'plate').', сохранено за '.(microtime(1) - $t2));
		
		
	Log::instance()->add(Log::NOTICE, "723 Stop cvs cam=".$cvs->cam.", cvs=". Arr::get($input_data, 'id').', grz='.$cvs->grz.', code_validation='.$cvs->code_validation.', total_time='.number_format((microtime(1) - $t1), 3)."\r\n");	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		return;
	}


	public function after()
	{
		
	}
}
