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
		$mess.='Ver:'.Kohana::$config->load('config')->ver.'<br>';
		$mess.=HTML::anchor('guide', 'guide');
		$this->response->body($mess);
		
			
		
		
		$content = View::factory('dashboard', array(
			//'garageLst'=>$garageLst,
				
		));
        //$this->template->content = $content;
		
	
		}
		
	

	
	public function action_sendMPT()
	{
		Log::instance()->add(Log::NOTICE, "88 start UHF action_sendMPT");
		$t1=microtime(1);
		
		//фиксирую полученные данные из $_OST в лог-файл
			$input_data_0=$_POST;//извлекаю данных из полученного пакета
		if(!Arr::get($input_data_0, 'test'))
		{
			//если в запросе нет поля test, то беру IP адрес из запроса
			$input_data_0['ip']=Request::$client_ip;
		}
		
		
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
			//$this->response->status(400);
			return;
		}
		//формирую основые переменные
		$id_gate=Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch'));
		$cvs=new phpCVS($id_gate);
		$identifier=new Identifier(hexdec(Arr::get($post, 'key')));
		
		Log::instance()->add(Log::NOTICE, '160 :key Получены данные 
				ch = :ch, 
				ip=:ip, 
				key= ":key" (:keyDec), 
				gate :gate, 
				isEnter=:isEnter
				',
					array( 
						':ip'=>Arr::get($post, 'ip'),
						':ch'=>Arr::get($post, 'ch'),
						':key'=>Arr::get($post, 'key'),
						':keyDec'=>hexdec(Arr::get($post, 'key')),
						':gate'=>$id_gate,
						':isEnter'=>$cvs->isEnter,
						)); 
	
						
						
		
		//переход к основной обработке
		$this->common( $identifier,  $cvs);
	}
	
	
	
	public function action_exec()
	{
		$t1=microtime(1);
		Log::instance()->add(Log::NOTICE, '668 start CVS action_exec');
		//Log::instance()->add(Log::NOTICE, '665 debug :data', array(':data'=>(microtime(true) - $t1)));
		$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
			
		$input_data=Arr::get($input_data_0, 'plate');
		//Log::instance()->add(Log::NOTICE, '676 debug :data', array(':data'=>Debug::vars($input_data)));
		
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
			exit;
		}
		//формирую основные переменные
		$id_gate = Model::factory('mpt')->getIdGateFromCam(Arr::get($input_data, 'camera'));//получил номер ворот
		$cvs=new phpCVS($id_gate);
		$identifier=new Identifier(Arr::get($input_data, 'plate'));//передаю ГРЗ в модель);
		
		
		$_data=array(':id_gate'=>$id_gate, 
							':cam'=>Arr::get($input_data, 'camera'),
							':cam_darect'=>Arr::get($input_data, 'direction'),
							':grz'=>Arr::get($input_data, 'plate')
							);
		Log::instance()->add(Log::NOTICE, '608 cvs exec получил данные '. Debug::vars($_data)); 
					
		
		//собрал нужные данные и передаю их в модуль управления логикой
		$this->common( $identifier,  $cvs);
	
		
		
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
		
		
		
		/**основной блок логики
		*/
		
		public function common(Identifier $identifier, phpCVS $cvs)
		{
			$t1=microtime(true);	
				
				//проверка: а не был ли этот ГРЗ в предыдущей обработке за последние ХХ минут?
			  //для этого использую кеширование: сохраняю ГРЗ в кеш с указанным временем хранения.
			  
			   if (Cache::instance()->get('grz_'.$identifier->id))
			   {
					// Данные найдены в кеше, не надо обрабатывать ГРЗ.
					 Log::instance()->add(Log::NOTICE, '250 :key найдент мьютекс :name. Значит этот идентификатор уже обрабатывается.', array(':key'=>$identifier->id, ':name'=>'grz_'.$identifier->id)); 
					Log::instance()->add(Log::NOTICE, '240 Повторный прием идентификатора grz '.$identifier->id.'. Обработка прекращена'); 
					$events= new Events();
					$events->eventCode=9;
					$events->grz=$identifier->id;
					$events->id_gate=$cvs->id_gate;
					$events->addEventRow();
					
					exit;
				
			   }
			   Log::instance()->add(Log::NOTICE, '260 :key нет мьютекс :name. Значит этот идентификатор НЕ обрабатывается в параллельном потоке. Продолжаю обработку.', array(':key'=>$identifier->id, ':name'=>'grz_'.$identifier->id)); 
			   
			   //проверка: не находится ли ворота во временной блокировке?
			   if (Cache::instance()->get('gateBlock_'.$cvs->id_gate))
			   {
					// Данные найдены в кеше, не надо обрабатывать ГРЗ.
					Log::instance()->add(Log::NOTICE, '246 :key найден мьютекс :name. Это значит, что ворота находятся во временной блокировке', array(':key'=>$identifier->id, ':name'=>'gateBlock_'.$cvs->id_gate)); 
					Log::instance()->add(Log::NOTICE, '255 Прием идентификатора :key от ворот :id_gate в момент встречного движена в реверсивных воротах. Обработка идентификатора прекращена.', array(':key'=>$identifier->id, ':id_gate'=>$cvs->id_gate)); 
					$events= new Events();
					$events->eventCode=10;
					$events->grz=$identifier->id;
					$events->id_gate=$cvs->id_gate;
					$events->addEventRow();
					
					exit;
				
			   }
			   Log::instance()->add(Log::NOTICE, '246 :key не найден мьютекс :name. Значит ворота не в режиме блокировки.', array(':key'=>$identifier->id, ':name'=>'gateBlock_'.$cvs->id_gate)); 
			   //проверка: а не идут ли тут друг за другом идентификаторы, которые сразу попали в поле антенны?
			  
			  $_data=$this->getMutexIdentifier('gate_'.$cvs->id_gate); //получил номер предыдущего обработанного UHF, на КОТОРЫЙ УЖЕ ИМЕЕТСЯ РАЗРЕШЕНИЕ
			//  Log::instance()->add(Log::NOTICE, '282 :key читаю мьютекс :name :data', array(':key'=>$identifier->id, ':name'=>'gate_'.$cvs->id_gate, ':data'=>Debug::vars($_data))); 
			   if(Arr::get($_data, 'id_gate') == $cvs->id_gate)
			   {
				   Log::instance()->add(Log::NOTICE, '285 :key найден мьютекс :name :data', array(':key'=>$identifier->id, ':name'=>'gate_'.$cvs->id_gate, ':data'=>Debug::vars($_data)));
				  Log::instance()->add(Log::NOTICE, '263 :key уже выполняется обработка идентификатора :_key mutex :mutex на этих воротах :id_gate. Завершаю работу с кодом :code.  ', 
					array(
					':key'=>$identifier->id, 
					':_key'=>Arr::get($_data, 'key'), 
					':id_gate'=>Arr::get($_data, 'id_gate'),
					':code'=>9,
					':mutex'=>'gate_'.$cvs->id_gate,
					)
					); 
					$result=9;
			   } else {
				     Log::instance()->add(Log::NOTICE, '297 :key НЕ найден мьютекс :name :data. Значит на этих воротах идентификатор не обрабатывается в параллельном потоке.', array(':key'=>$identifier->id, ':name'=>'gate_'.$cvs->id_gate, ':data'=>Debug::vars($_data)));
				
				//	Log::instance()->add(Log::NOTICE, '274 :key начинаю mainAnalysis.', array(':key'=>$identifier->id));
					//============== Главное! анализ!!! ==============================		
						$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
					//===================================================================
						Log::instance()->add(Log::NOTICE, '292 :key результат работы mainAnalysis :result.', array(':key'=>$identifier->id, ':result'=> $result));
				   
			   }
			   

			
			$cvs->code_validation=$result;//передаю в модель результат валидации
			$cvs->getMessForEvent($result);//формирую текстовое сообщение для табло
			
			//сохраняю идентификатора в кеше для защиты от повторной обработки
			Cache::instance()->set('grz_'.$identifier->id, array('set_grz'=>1, 'key'=>$identifier->id), Setting::get('delay_cvs', 120)); // Этому ГРЗ проезд запрещен. Если он опять будет передан в это отрезок времени, то заблокируем его.
			//Cache::instance()->set('door_'.$cvs->id_gate, $cvs->id_gate, Setting::get('delay_cvs', 120)); // Номер ворот, на которых начал обработку.
					
					
			//делаю набор условий для последующей обработки. Если результат 50 (можно проезжать), то жду 30 секунд.
				switch($result){
					case 81:
					case 50:
						$_data=array('key'=>$identifier->id, 'id_gate'=>$cvs->id_gate, 'isEnter'=>$cvs->isEnter);
						$this->setMutexIdentifier('gate_'.$cvs->id_gate, $_data ); //если проезд разрешен, то фиксирую этот ГРЗ в мьютексе.
						Log::instance()->add(Log::NOTICE, '206 :key валидация прошла успешно, записал в mutex :name значение :data', array(':key'=>$identifier->id,':name'=>'gate_'.$cvs->id_gate, ':data'=>Debug::vars($_data)));
													
					break;
					default:
								
						break;
				}
					
		 			
			Log::instance()->add(Log::NOTICE, '318 :key mutex перед gateControl имеет значение  :mutex.', 
				array(
					':key'=>$identifier->id, 
					':mutex'=>Debug::vars($this->getMutexIdentifier('gate_'.$cvs->id_gate))));	
			 
			if($this->isMutexFree('gate_'.$cvs->id_gate))
			{
				//ресурс свободен
				//Log::instance()->add(Log::NOTICE, '322-0 mutex true свободен, продолжаю обработку.');	
				Log::instance()->add(Log::NOTICE, '322-0 :key mutex true свободен, продолжаю обработку', array(':key'=>$identifier->id));
				
			} else {
				$_data=$this->getMutexIdentifier('gate_'.$cvs->id_gate);//получил данные из кеша
				Log::instance()->add(Log::NOTICE, '322-1 :key mutex false. занят, записано значение :data', array(':key'=>$identifier->id, ':data'=>Debug::vars($_data)));
				if(Arr::get($_data, 'key') != $identifier->id) 
				{
					Log::instance()->add(Log::NOTICE, '324 :key mutex занят обработкой :mutex, прекращаю обработку.', array(':key'=>$identifier->id,  ':mutex'=>Debug::vars($this->getMutexIdentifier('gate_'.$cvs->id_gate))));	
						
					$events= new Events();
					$events->eventCode=7;
					$events->grz=$identifier->id;
					$events->id_gate=$cvs->id_gate;
					$events->addEventRow();
					exit;
				} else {
					//номера обрабатываемых идентификаторов совпадают.
					Log::instance()->add(Log::NOTICE, '343 :key значение mutex :mutex совпадает с номером обрабатываемого идентификатора, продолжаю обработку.', array(':key'=>$identifier->id,  ':mutex'=>Arr::get($this->getMutexIdentifier('gate_'.$cvs->id_gate), 'key')));	
					//тут бы еще и ворота проверить. если ворота те же самые, то продолжаю обработку.
					//если же ворота другие - прекращать обработку.
					//if(Arr::get($_data, 'id_gate') != $cvs->id_gate) 
					
				}
				
				
			}
					
			//мьтекс введен с целью недопустить фиксацию двух машиномест при получении сразу двух идентификаторов от одного автомобиля на въезде		
			//в этот момент в мьютексе должен находится последний (по времени) валидный идентификатор
			// если его значение совпадает с текущим обрабатываемым, значит, надо продолжать работу.
			// если не совпадает - значит, пока шла обработка этого идентификатора, по другому каналу пришел и был обработан другой идентификатор.			
			
			//Log::instance()->add(Log::NOTICE, '351 :key grz mutex свободен.', array(':key'=>$identifier->id));
//=========== запись события в журнал

				
		$events= new Events();
		$events->eventCode=$cvs->code_validation;
		$events->grz=$identifier->id;
		$events->id_gate=$cvs->id_gate;
		$events->addEventRow();
		Log::instance()->add(Log::NOTICE, '342 :key save events code=:code , grz=:grz, gate=:gate', array(':key'=>$identifier->id, ':code'=>$events->eventCode, ':grz'=>$events->grz, ':gate'=>$events->id_gate));
		
//============== управление воротами ========================================			
			//перехожу к управлению воротами
		
		Model::factory('cvss')->gateControl($identifier, $cvs);
		Log::instance()->add(Log::NOTICE, "335 :key Stop gate=:gate, code_validation=:code, total_time=:tt", array(
			':key'=>$identifier->id, 
			':gate'=>$cvs->id_gate, 
			':code'=>$cvs->code_validation,
			':tt'=>number_format((microtime(1) - $t1), 3)
			));	
		//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		Log::instance()->add(Log::NOTICE, '337 end debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>$identifier->id));
		
		return;
			
		}
		
		
		/**чтение значения  мьютекса
		* возвращает значение указанного мьютекса
		*
		*/
		public function getMutexIdentifier($name)
		{
			// Log::instance()->add(Log::NOTICE, '1127 mutex_'. $id_gate);	
			// Log::instance()->add(Log::NOTICE, '1128 mutex_'. $id_gate.' '. Cache::instance()->get('mutex_'. $id_gate));	
			return unserialize (Cache::instance()->get($name));
			
		}
		
		/**чтение мьютекса: свободен или занят
		* TRUE - значит, свободен.
		* FALSE  занят
		*
		*/
		public function isMutexFree($name)
		{
			if(Cache::instance()->get($name)) return false; //ресурс свободен, можно его использовать.
			return true;
			
		}
		
		
		
		/**Установка (захват) мьютекса
		*
		*
		*/
		//public function setMutexIdentifier($id_gate, $identifier)
		public function setMutexIdentifier($name, $data)
		{
			//Log::instance()->add(Log::NOTICE, '389 записал в mutex :name значения :data', array(':mame'=>$name, ':data'=>Debug::vars($data)));
			if (Cache::instance()->get($name))//если true, значит он уже обрабатывается. Обработка полученного идентификатора надо прекращать.
			{
				return true;
			} else {
				Cache::instance()->delete($name);
				Cache::instance()->set($name, serialize($data), Setting::get('delay_cvs', 20));
				return false;
				
			}
			
		}
		
		/** освобождение мьютекса
		*
		*/
		
		public function resetMutexIdentifier($name)
		{
		/* 
			Cache::instance()->delete($name);
			return true; */
			//Удаление не требуется, т.к. кеш имеет срок жизни
			
		}
		
		/* public function checkMutexIdentifier($id_gate)
		{
			if (Cache::instance()->get('id_gate_'. $id_gate))//если этот кеш есть, значит он уже обрабатывается. Его надо удалить и заменить на вновь полученный идентификатор
			{
				
				Log::instance()->add(Log::NOTICE, "1094 выполняется обработка ранее полученного идентификатора, процесс завершаю.");
			}
			
		}
		 */
		
		
		    public function after()
    {
      // Cache::instance()->garbage_collect();
    }
		
}
