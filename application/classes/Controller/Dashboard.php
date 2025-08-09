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
		
		$identifier=hexdec(Arr::get($post, 'key'));
		
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
						
						)); 
	
						
						
		
		//переход к основной обработке
		
		Model::factory('cvss')->common( $identifier,  $id_gate);
		
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
		//$cvs=new phpCVS($id_gate);
		//$identifier=new Identifier(Arr::get($input_data, 'plate'));//передаю ГРЗ в модель);
		$identifier=Arr::get($input_data, 'plate');//передаю ГРЗ в модель);
		
		
		$_data=array(':id_gate'=>$id_gate, 
							':cam'=>Arr::get($input_data, 'camera'),
							':cam_darect'=>Arr::get($input_data, 'direction'),
							':grz'=>Arr::get($input_data, 'plate')
							);
		Log::instance()->add(Log::NOTICE, '608 cvs exec получил данные '. Debug::vars($_data)); 
					
		
		//собрал нужные данные и передаю их в модуль управления логикой
		//$this->common( $identifier,  $cvs);
		//Model::factory('cvss')->common( $identifier,  $cvs);
		Model::factory('cvss')->common( $identifier,  $id_gate);
	
		
		
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
		
		
		
						
		
		    public function after()
    {
      // Cache::instance()->garbage_collect();
    }
		
}
