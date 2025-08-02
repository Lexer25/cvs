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
	echo Debug::vars('77', Setting::get('delay_cvs', 122));
		$gate=Model_cvss::getGateFromCam($id_cam);//это ворота, через которые пытается проехат ГРЗ
		
			
		
		
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
		//Log::instance()->add(Log::NOTICE, '85 data receive MPT '.Debug::vars($_POST));// exit;
		$input_data_0=$_POST;//извлекаю данных из полученного пакета
		//Log::instance()->add(Log::NOTICE, '103-10 debug :data', array(':data'=>number_format((microtime(1) - $t1), 3)));
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
			//$this->response->status(400);
			return;
		}
		
		
		
		//Log::instance()->add(Log::NOTICE, '103-20 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($post, 'key')));
		//Log::instance()->add(Log::NOTICE, '140 Валидация UHF выполнена успешно, продолжаю работу.');//вывод номера в лог-файл

		$id_gate=Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch'));
		
			
	
	Log::instance()->add(Log::NOTICE, '160 Получены данные ch = :ch, ip=:ip, key= ":key" (:keyDec), gate :gate',
					array( 
						':ip'=>Arr::get($post, 'ip'),
						':ch'=>Arr::get($post, 'ch'),
						':key'=>Arr::get($post, 'key'),
						':keyDec'=>hexdec(Arr::get($post, 'key')),
						':gate'=>$id_gate
						)); 
							
	//	Log::instance()->add(Log::NOTICE, '103-30 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($post, 'key')));
		$cvs=new phpCVS($id_gate);
		//Log::instance()->add(Log::NOTICE, '103-40 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Debug::vars($cvs)));
		$identifier=new Identifier(hexdec(Arr::get($post, 'key')));
		
/* 		if (Cache::instance()->get('id_gate_'.$id_gate))
		   {
			// Данные найдены в кеше, не надо обрабатывать данные от ворот.
				
			//$this->code_validation=-1;
			Log::instance()->add(Log::NOTICE, '99 ворота '.$id_gate.' открыты от предыдущего ГРЗ '.$identifier->id.'. Обработка данных прекращена.'); 
			exit;
			
		   }
		   else
		   {
			//Log::instance()->add(Log::NOTICE, '104 Данных от ворот '.$id_gate.' grz '.$identifier->id.'  не было давно, начанию обработку.'); 

			} */
		
		Log::instance()->add(Log::NOTICE, '103-50 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($post, 'key')));
		  			
			//проверка: а не был ли этот ГРЗ в предыдущей обработке за последние ХХ минут?
		  //для этого использую кеширование: сохраняю ГРЗ в кеш с указанным временем хранения.
		   
		  //echo Debug::vars('100',Cache::instance() );exit;
		 /*  if (Cache::instance()->get('grz_'.$identifier->id))
		   {
			// Данные найдены в кеше, не надо обрабатывать ГРЗ.
				
			$this->code_validation=8;
			Log::instance()->add(Log::NOTICE, '101 Повторный прием идентификатора grz '.$identifier->id.'. Обработка прекращена'); 
			//exit;
			
		   } */
		
	//$result=$this->mainAnalysis($identifier,  $cvs);
	$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
	
		//Log::instance()->add(Log::NOTICE, '103-50 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($post, 'key')));
		Log::instance()->add(Log::NOTICE, '187 результат работы mainAnalysis :result, grz :grz gate :gate', array(
				':result'=>$result,
				':grz'=>$identifier->id,
				':gate'=>$id_gate,
		));
		
		//делаю набор условий для последующей обработки. Если результат 50 (можно проезжать), то жду 30 секунд.
			switch($result){
				case 50:

					$this->setMutexIdentifier($id_gate, $identifier->id); //занял приоритет 
					Log::instance()->add(Log::NOTICE, '206 записал mutex значением :data', array(':data'=>$id_gate, ':data'=>$identifier->id));
								
					/* Log::instance()->add(Log::NOTICE, '163 фискирую в кеш что ворота :gate открыты, жду :delay секунд', array(
					':result'=>$result,
					':grz'=>$identifier->id,
					':gate'=>$id_gate,
					':delay'=>Setting::get('delay_cvs', 122),
					)); */
						Cache::instance()->set('id_gate_'.$id_gate, $id_gate, Setting::get('delay_cvs', 120)); // Проезд разрешен. Блокирую ворота на заданное время. В течении этого времени обработка других ГРЗ производится не будет.
						
				break;
				default:
					/* Log::instance()->add(Log::NOTICE, '163-0 фиксирую в кеш грз :grz не может ездить, жду его через не менее :delay секунд', array(
					':result'=>$result,
					':grz'=>$identifier->id,
					':gate'=>$id_gate,
					':delay'=>Setting::get('delay_cvs', 122),
					)); */
						Cache::instance()->set('grz_'.$identifier->id, $identifier->id, Setting::get('delay_cvs', 120)); // Этому ГРЗ проезд запрещен. Если он опять будет передан в это отрезок времени, то заблокируем его.
						
					break;
			}
			
			
		//Log::instance()->add(Log::NOTICE, '103-60 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($post, 'key')));
		
		//Log::instance()->add(Log::NOTICE, "220 Stop UHF gate=".$id_gate.", UHF=".Arr::get($post, 'key').", permission =".$result.", total_time=".number_format((microtime(1) - $t1), 3));	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		
		$t2=microtime(1);
		
		//$cvs=new phpCVS($id_gate);
		$cvs->grz=hexdec(Arr::get($post, 'key'));//передаю UHF в модель
		$cvs->code_validation=$result;//передаю в модель результат валидации
		$cvs->getMessForEvent($result);//формирую текстовое сообщение для табло
		
	Log::instance()->add(Log::NOTICE, '242 uhf mutex перед gateCOntrol имеет значение  :mutex.', array(':mutex'=>$this->getMutexIdentifier($id_gate)));	
						
	
	/* 	if($this->getMutexIdentifier($id_gate) != $cvs->grz) 
		{
				Log::instance()->add(Log::NOTICE, '248 mutex занят обработкой :mutex, прекращаю обработку.', array(':mutex'=>$this->getMutexIdentifier($id_gate)));	
				Log::instance()->add(Log::NOTICE, '249 debug :data', array(':data'=>Cache::instance()->get('mutex_3')));
				//$cvs->code_validation=8;//VALUES (8,'Повторное чтение идентификатора',65535);
				//exit;
						
		}
			 */		
		Log::instance()->add(Log::NOTICE, '251 mutex свободен.', array(':mutex'=>$this->getMutexIdentifier($id_gate)));
		
		//$this->gateControl($identifier, $cvs);
		Model::factory('cvss')->gateControl($identifier, $cvs);
		Log::instance()->add(Log::NOTICE, "236 gateControl total_time=".number_format((microtime(1) - $t2), 3));		

	
		//$this->response->status(200);
		//Log::instance()->add(Log::NOTICE, '103-70 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($post, 'key')));
		
	Log::instance()->add(Log::NOTICE, "242 Stop UHF gate=".$cvs->id_gate.", eventcount=0, UHF=".$cvs->grz.", code_validation=".$cvs->code_validation.", total_time=".number_format((microtime(1) - $t1), 3));	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
	//Log::instance()->add(Log::NOTICE, '103-80 end debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($post, 'key')));
		
		//$this->resetMutexIdentifier($id_gate); 
		
		Log::instance()->add(Log::NOTICE, '258 Освободил mutex');	
		return;
		
		
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
		//Log::instance()->add(Log::NOTICE, '665-05 debug :data', array(':data'=>(microtime(true) - $t1)));
		//Log::instance()->add(Log::NOTICE, '665-05 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($input_data, 'plate')));
		
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '125 Входные данные cvs не полные '. Debug::vars($post->errors())); //exit;//вывод номера в лог-файл
			//echo Debug::vars('126 валидация прошла с ошибкой', $post->errors());exit;
			//$this->response->status(200);
			exit;
		}
		//Log::instance()->add(Log::NOTICE, '665-10 Валидация ГРЗ :grz выполнена успешно, продолжаю работу debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($input_data, 'plate')));
		
		
		$id_gate = Model::factory('mpt')->getIdGateFromCam(Arr::get($input_data, 'camera'));//получил номер ворот
		
		
			
		Log::instance()->add(Log::NOTICE, '608 cvs id_gate = :id_gate cam=:cam grz=:grz', 
					array(':id_gate'=>$id_gate, 
							':cam'=>Arr::get($input_data, 'camera'),
							':cam_darect'=>Arr::get($input_data, 'direction'),
							':grz'=>Arr::get($input_data, 'plate')
							
							));
		//Log::instance()->add(Log::NOTICE, '665-20 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($input_data, 'plate')));
	$cvs=new phpCVS($id_gate);
	//Log::instance()->add(Log::NOTICE, '665-30 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($input_data, 'plate')));
		$identifier=new Identifier(Arr::get($input_data, 'plate'));//передаю ГРЗ в модель);
		
		
	if (Cache::instance()->get('id_gate_'.$id_gate))
		   {
			// Данные найдены в кеше, не надо обрабатывать данные от ворот.
				
			//$this->code_validation=-1;
			Log::instance()->add(Log::NOTICE, '99 ворота '.$id_gate.' открыты от предыдущего ГРЗ '.$identifier->id.'. Обработка данных прекращена.'); 
			exit;
			
		   }
		   else
		   {
			Log::instance()->add(Log::NOTICE, '104 Данных от ворот '.$id_gate.' grz '.$identifier->id.'  не было давно, начанию обработку.'); 

			}
		
		
		  			
			//проверка: а не был ли этот ГРЗ в предыдущей обработке за последние ХХ минут?
		  //для этого использую кеширование: сохраняю ГРЗ в кеш с указанным временем хранения.
		   
		  //echo Debug::vars('100',Cache::instance() );exit;
		  if (Cache::instance()->get('grz_'.$identifier->id))
		   {
			// Данные найдены в кеше, не надо обрабатывать ГРЗ.
				
			//$this->code_validation=-1;
			Log::instance()->add(Log::NOTICE, '101 Повторный прием идентификатора grz '.$identifier->id.'. Обработка прекращена'); 
			exit;
			
		   }	
		  // Log::instance()->add(Log::NOTICE, '665-40 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($input_data, 'plate')));
	//$result=$this->mainAnalysis($identifier,  $cvs);
	$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
		Log::instance()->add(Log::NOTICE, '393   результат работы mainAnalysis ' . $result);
		//Log::instance()->add(Log::NOTICE, "185 Stop GRZ gate=".$id_gate.", GRZ=".Arr::get($post, 'key').", permission =".$result.", total_time=".number_format((microtime(1) - $t1), 3));	
//Log::instance()->add(Log::NOTICE, '665-50 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($input_data, 'plate')));
		
		$t2=microtime(1);
		
		//$cvs=new phpCVS($id_gate);
		$cvs->grz=Arr::get($input_data, 'plate');//передаю UHF в модель
		$cvs->code_validation=$result;//передаю в модель результат валидации
		$cvs->getMessForEvent($result);//формирую текстовое сообщение для табло
		
		
		if(Arr::get($input_data_0, 'test')) Log::instance()->add(Log::NOTICE, '169 режим ТЕСТ, команды на открытие ворот НЕ передаются'); 
			
		

	
		//$this->response->status(200);
		//Log::instance()->add(Log::NOTICE, '665-60 debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($input_data, 'plate')));
		//делаю набор условий для последующей обработки. Если результат 50 (можно проезжать), то жду 30 секунд.
			switch($result){
				case 50:
					$this->setMutexIdentifier($id_gate, $identifier->id); 
					Log::instance()->add(Log::NOTICE, '206 зянял mutex значением :data', array(':data'=>$id_gate, ':data'=>$identifier->id));

					/* Log::instance()->add(Log::NOTICE, '163 фискирую в кеш что ворота :gate открыты, жду :delay секунд', array(
					':result'=>$result,
					':grz'=>$identifier->id,
					':gate'=>$id_gate,
					':delay'=>Setting::get('delay_cvs', 122),
					)); */
						Cache::instance()->set('id_gate_'.$id_gate, $id_gate, Setting::get('delay_cvs', 120)); // Проезд разрешен. Блокирую ворота на заданное время. В течении этого времени обработка других ГРЗ производится не будет.
						
				break;
				default:
				/* 	Log::instance()->add(Log::NOTICE, '163-0 фиксирую в кеш грз :grz не может ездить, жду его через не менее :delay секунд', array(
					':result'=>$result,
					':grz'=>$identifier->id,
					':gate'=>$id_gate,
					':delay'=>Setting::get('delay_cvs', 120),
					)); */
						Cache::instance()->set('grz_'.$identifier->id, $identifier->id, Setting::get('delay_cvs', 120)); // Этому ГРЗ проезд запрещен. Если он опять будет передан в это отрезок времени, то заблокируем его.
						
					break;
			}
				
				
				Log::instance()->add(Log::NOTICE, '694 grz mutex перед gateCOntrol имеет значение  :mutex.', array(':mutex'=>$this->getMutexIdentifier($id_gate)));	
				
				if($this->getMutexIdentifier($id_gate) != $cvs->grz) 
					{
						Log::instance()->add(Log::NOTICE, '145 mutex занят обработкой :mutex, прекращаю обработку.', array(':mutex'=>$this->getMutexIdentifier($id_gate)));	
						Log::instance()->add(Log::NOTICE, '149 debug :data', array(':data'=>Cache::instance()->get('mutex_3')));
						exit;
						
					}
		Log::instance()->add(Log::NOTICE, '700 grz mutex свободен.', array(':mutex'=>$this->getMutexIdentifier($id_gate)));
		
		//$this->gateControl($identifier, $cvs);
		Model::factory('cvss')->gateControl($identifier, $cvs);
		Log::instance()->add(Log::NOTICE, "172 gateControl total_time=".number_format((microtime(1) - $t2), 3));		
	Log::instance()->add(Log::NOTICE, "788 Stop UHF gate=".$cvs->id_gate.", eventcount=0, UHF=".$cvs->grz.", code_validation=".$cvs->code_validation.", total_time=".number_format((microtime(1) - $t1), 3));	
	//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
	//Log::instance()->add(Log::NOTICE, '665-70 end debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>Arr::get($input_data, 'plate')));
	

	//$this->resetMutexIdentifier($id_gate); 
	Log::instance()->add(Log::NOTICE, "818 mutex освободил.");	
	return;
		
		
	}
	
		
		
		/**Установка (захват) мьютекса
		*
		*
		*/
		public function setMutexIdentifier($id_gate, $identifier)
		{
			Log::instance()->add(Log::NOTICE, '1110-10 debug :data', array(':data'=>Cache::instance()->get('mutex_'.$id_gate)));
			if (Cache::instance()->get('mutex_'. $id_gate))//если true, значит он уже обрабатывается. Обработка полученного идентификатора надо прекращать.
			{
				return true;
			} else {
				Cache::instance()->delete('mutex_'. $id_gate);
				Cache::instance()->set('mutex_'. $id_gate, $identifier, Setting::get('delay_cvs', 20));
				return false;
				
			}
			
		}
		
		
		
		/**чтение значения  мьютекса
		*
		*
		*/
		public function getMutexIdentifier($id_gate)
		{
			// Log::instance()->add(Log::NOTICE, '1127 mutex_'. $id_gate);	
			// Log::instance()->add(Log::NOTICE, '1128 mutex_'. $id_gate.' '. Cache::instance()->get('mutex_'. $id_gate));	
			return Cache::instance()->get('mutex_'. $id_gate);
			
		}
		
		
		
		/** освобождение мьютекса
		*
		*/
		
		public function resetMutexIdentifier($id_gate)
		{
			Cache::instance()->delete('mutex_'. $id_gate);//если этот кеш есть, значит он уже обрабатывается. Его надо удалить и заменить на вновь полученный идентификатор
			return true;
			
		}
		
		/* public function checkMutexIdentifier($id_gate)
		{
			if (Cache::instance()->get('id_gate_'. $id_gate))//если этот кеш есть, значит он уже обрабатывается. Его надо удалить и заменить на вновь полученный идентификатор
			{
				
				Log::instance()->add(Log::NOTICE, "1094 выполняется обработка ранее полученного идентификатора, процесс завершаю.");
			}
			
		}
		 */
		
		
}
