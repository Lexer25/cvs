<?php

/*
выполняется валидацию данных в БД СКУД при работе с cvs
*/
class phpCVS
{
    public $cam = false;        /* номер видеокамеры */
    public $id_pep;                /* id пользователя владельца ГРЗ */
    public $grz;            /* грз автомобиля*/
    public $code_validation;            /*результат валидации*/
	public $tablo_ip; 	/* IP адрес табло */
	public $tablo_port;	/* порт табло*/
	public $box_ip;		/* IP адрес шкафа управления*/
	public $box_port;	/* порт шкафа управления*/
	public $ch;	/* номер канала*/
	public $id_gate;		/* номер ворот*/
	public $id_dev;		/* id_dev, обслуживающий эти ворота*/
	public $mode;		/* режим работы ворот*/
	public $id_parking;	/* id паркинга */
	public $eventdMess;	/* сообщения для вывода на табло */
	public $messParam;	/* параметры сообщения для вывода на табло */
	public $isEnter;	/* 0 для выезда, 1 для въезда */
	public $timeStamp;	/* метка времени от CVS */
	public $top_string;	/* верхняя строка заставки */
	public $down_string;	/* нижняя строка заставки */

	
	
	//конструктор "работает" но номеру ворот.
    public function __construct($id)
    {
       $sql='select hlp.tablo_ip, 
					hlp.tablo_port,
					hlp.box_ip,
					hlp.box_port,
					hlp.id as id_gate,
					hlp.id_dev,
					hlp.mode,
					hlp.id_parking,
					hlp.is_enter,
					hlp.is_enter,
					hlp.id_cam,
                    hlp.channel as ch					
					from hl_param hlp where hlp.id='.$id;
					//echo Debug::vars('40', $sql);exit;
		//Log::instance()->add(Log::NOTICE, '46'. $sql);			
	   $query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
			
			$query=Arr::get($query, 0);
					$this->tablo_ip=Arr::get($query, 'TABLO_IP'); 
					$this->tablo_port=Arr::get($query, 'TABLO_PORT');
					$this->box_ip=Arr::get($query, 'BOX_IP');
					$this->box_port=Arr::get($query, 'BOX_PORT');
					$this->id_gate=Arr::get($query, 'ID_GATE');
					$this->id_dev=Arr::get($query, 'ID_DEV');
					$this->mode=Arr::get($query, 'MODE');
					$this->id_parking=Arr::get($query, 'ID_PARKING');
					$this->isEnter=Arr::get($query, 'IS_ENTER');
					$this->cam=Arr::get($query, 'ID_CAM');
					$this->ch=Arr::get($query, 'CH');
					
			$this->getMessForIdle();
			return;
			
    }


   public function getIDPEP($id_pep1)
    {
       
		$sql='select count(*) from people';
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('COUNT');
	   $this->id_pep = $query;
		return;
    }

/** 30.04.2025
*Процесс валидации ГРЗ в указанной точке проезда
*@param id_dev - точка проезда
*@param GRZ - номерной знак
*@return void
*/

 public function check()
    {
      
				 //проверка: а не был ли этот ГРЗ в предыдущей обработке за последние ХХ минут?
		  //для этого использую кеширование: сохраняю ГРЗ в кеш с указанным временем хранения.
		   
		  //echo Debug::vars('100',Cache::instance() );exit;
		  if (Cache::instance()->get('grz'))
		   {
			// Данные найдены в кеше, не надо обрабатывать ГРЗ.
				
			//$this->code_validation=-1;
			Log::instance()->add(Log::NOTICE, '101 Повторный прием идентификатора не обрабатывается.'); 
			
		   }
		   else
		   {
			// Данных нет в кеше, нужно сгенерировать заново
			$sql='select rc as event_type, id_pep from REGISTERPASS_HL_2('.$this->id_dev.', \''.$this->grz.'\', NULL)';
			//echo Debug::vars('92', $sql);exit;
			
			//Log::instance()->add(Log::NOTICE, '193 '. $sql); 
			$query = DB::query(Database::SELECT, $sql)
			 ->execute(Database::instance('fb'))
			 ->as_array();
			
			$query=Arr::get($query, 0);
			$this->getMessForEvent(Arr::get($query, 'EVENT_TYPE'));
			//$this->getMessForIdle();
			$this->code_validation = Arr::get($query, 'EVENT_TYPE');
			//echo Debug::vars('119',  Setting::get('delay_cvs', 30));exit;
			Log::instance()->add(Log::NOTICE, '120 '. Setting::get('delay_cvs', 30)); 
			Cache::instance()->set('grz', $this->grz,  Setting::get('delay_cvs', 30)); // Время задержки берется из настроект setting. Если настройка отсутвует, то по по умолчанию берется 30 секунд. 
			}
	}
	
	 public function check_old()
    {
      
		$t1=microtime(1);
		$sql='select rc as event_type, id_pep from REGISTERPASS_HL_2('.$this->id_dev.', \''.$this->grz.'\', NULL)';
		//echo Debug::vars('92', $sql);exit;
		
		Log::instance()->add(Log::NOTICE, '193 '. $sql); 
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
		
		$query=Arr::get($query, 0);
		$this->getMessForEvent(Arr::get($query, 'EVENT_TYPE'));
		//$this->getMessForIdle();
		$this->code_validation = Arr::get($query, 'EVENT_TYPE');
		
		Log::instance()->add(Log::NOTICE, "106 check total_time=".number_format((microtime(1) - $t1), 3)."\r\n");	
		
		return;
    }
	
	
	
	
	
 public function getMessForEvent($id_event)
 {
	 $sql='select * from hl_messages hlm
		where hlm.eventcode='.$id_event;
	
	$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
		//echo Debug::vars('87', $sql, $query); exit;
		$query=Arr::get($query, 0);
	   $this->eventdMess = Arr::get($query, 'TEXT');
	   $this->messParam = Arr::get($query, 'PARAM');
	   return;
	 
 }
 
 /*
 
 получить надписи на табло на то время, пока нет ГРЗ.
 */
 
public function getMessForIdle()
 {
	 $sql='select hlm.text from hl_messages hlm
        where hlm.smalname=\'text1\'';
       
	
	$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('TEXT');
		
	//Log::instance()->add(Log::NOTICE, '124 служебные надписи '. Debug::vars($sql, $query));	
	 $this->top_string = $query;
	 $this->down_string = $query;
	//Log::instance()->add(Log::NOTICE, '126 служебные надписи '. iconv('windows-1251','UTF-8', $this->top_string). ' '. iconv('windows-1251','UTF-8',$this->down_string));	


	$sql='select hlm.text from hl_messages hlm
        where hlm.smalname=\'text2\'';
       
	
	$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('TEXT');
		
	//Log::instance()->add(Log::NOTICE, '135 служебные надписи '. Debug::vars($sql, $query));	
	
	 $this->down_string = $query;
	
	
	 return;
 }
 
/** 19.07.2025
	*Процесс валидации ГРЗ и UHF в указанной точке проезда средствами php (не процедура в БД СКУД).
	*@param id_parking - id парковочной площадки
	*@param is_enter - true - въезд, false - выезд
	*@param GRZ - номерной знак
	*@return void
	*/

	public function checkPHPin()
    {
		
		$event=new Events();
		$event->eventCode=13;
		$event->grz=$grz;
		$event->addEventRow();
		
		//собираю информацию по полученному идентификатору
		$identifier=new Identifier($grz);
		switch($identifier->status){
			case 'UNKNOWNCARD':
				//запись в БД что карта неизвестна, завершение работы
				echo Debug::vars('85 UNKNOWNCARD');
				$event->eventCode=events::UNKNOWNCARD;
				$event->grz=$grz;
				$event->addEventRow();
				exit;
			break;
			case	 'DISABLEDCARD':
				//запись в БД что карта DISABLEDCARD
				exit;
			break;
			case	 'DISABLEDUSER':
				//запись в БД что карта DISABLEDUSER
				exit;
			break;
			case	 'CARDEXPIRED':
				//запись в БД что карта DISABLEDUSER
				exit;
			break;
			default:
				//идентификатор валидный, продолжаем работу
			break;
		}
			echo Debug::vars('94 информация о ГРЗ', $identifier);//exit;
			
			//надо иметь информацию и о воротах, откуда пришел ГРЗ
			//Эта часть реализована в phpCVS
			$cvs=new phpCVS(Model_cvss::getGateFromCam($id_cam));
			echo Debug::vars('106 информация о воротах', $cvs);
			
			//надо иметь информацию о гараже.
			$garage=new Garage($identifier->id_garage);//это - модель гаража, куда едет ГРЗ. Этот параметр беретс из $identifier->id_garage, но для отладки использую фиксировнное значение  $id_garage
			echo Debug::vars('109 ГРЗ едет вот в этот гараж', $garage);
		
		

		//если у ГРЗ есть гараж, то надо делать только проверки свободных мест.
		//если же гаража нет, то надо проверять категорию доступа.		
		if(is_null($identifier->id_garage)) //гаража нет.
		{
			
			echo Debug::vars('103 гаража нет. проезд только при наличии категории доступа.');
			exit;

		}	
			//если гараж есть, то:
			
	//может ли ГРЗ ездить через эти ворота?
		//для этого необходимы условия:
		//1. гараж и ворота находятся на одной площадке. для этого надо знать
		//общего у них -  парковочная площадка id_parking
		//echo Debug::vars('132', in_array ($cvs->id_parking, $garage->id_parking));exit; 
		if(!in_array ($cvs->id_parking, $garage->id_parking)){
			//ворота, куда подъехал автомобиль, не содержит парковочных мест гаража, разрешенных этому ГРЗ.
			//ворота не открывать!. можно вывести подсказку: ошиблись воротами!
			$event->eventCode=events::ACCESSDENIED;
			$event->grz=$grz;
			$event->addEventRow();
			
			echo Debug::vars('134 ошибся воротами!');
			exit;
		}
		echo Debug::vars('137 в эти ворота можно проезжать!');
		 //это въезд или выезд?
	
	
	//Проверяю направление проезда.
	if($cvs->isEnter ){
	//проверяю наличие свободных мест	 
	//т.к. гараж может иметь на разых площадках, то и количество свободных мест надо считать для каждой площадки.
			//echo Debug::vars('142',$garage->getPlaceCount($cvs->id_parking));
			//echo Debug::vars('143',$garage->getPlaceCountUccuped($cvs->id_parking));
			
 		 if($garage->getPlaceCount($cvs->id_parking) - $garage->getPlaceCountUccuped($cvs->id_parking) >0){
			//свободные мест есть, можно запускать.
		//*добавить событие  в hl_events
		
		$event->eventCode=events::OK;
		$event->grz=$grz;
		$event->addEventRow();
		//*добавить в hl_inside номер id_pep и дату. Номер ГРЗ идет как вспомогательный параметр, чтобы можно было понять по какому ГРЗ был въезд. 
		//*давать команду на управление воротами: открыть, вывести надпись и т.п.
			 echo Debug::vars('116 Можно заезжать!');
		 } else {
			 //мест нет, не запускать.
		//*добавить событие  в hl_events
		//*дать команду на ворота: что вывести табло.
			 echo Debug::vars('121 Мест нет');
		 } 
		//проверяю наличие свободных мест.
		//теперь по номеру видекамеры определяю параметры гаража, куда пытается заехать автомобиль.	
		
	} else { //выезд
	$event->eventCode=events::OK;
		$event->grz=$grz;
		$event->addEventRow();
		echo Debug::vars('165 выезд!!!');
		// есть ли ГРЗ и id_pep в таблице hl_inside?
		//если есть, то удалить оттуда.
		//а если нет, то удалить кого-нибудь другого...
		if($identifier->checkInParking($cvs->id_parking))
		{
	//* удалить идентификатор из таблицы hl_inside
	//*	сделать запись в журнале событий
	//* открыть ворота
	echo Debug::vars('174 Был на территории, можно выезжать!');
		} else {
			//* надо хоть кого-то удалить
			//* сделать запись в журнале событий о нарушении порядка
			//* сделать запись в журнале событий о выезде
			//*открыть ворота
			echo Debug::vars('180 на территории не был, пытается выехать. что делать?');
		}
		
		
	}
		
    }
	
	
	
   
}
