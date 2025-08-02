<?php

/**20.07.2025 Класс, предоставляющий технические параметры ворот (gate): адреса и порты контроллера ворот, табло, связанные с воротами надписи и т.п.
* это - строка из таблицы hl_param, где собраны все параметры ворот.
* выполняется валидацию данных в БД СКУД при работе с cvs
*/
class phpCVS
{
   // public $cam = false;        /* номер видеокамеры */
   //  public $grz;            /* грз автомобиля*/
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
					//$this->cam=Arr::get($query, 'ID_CAM');
					$this->ch=Arr::get($query, 'CH');

			$this->getMessForIdle();
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
	*@param garage - id гаража
	*@param is_enter - true - въезд, false - выезд
	
	*@return void
	*/

	public function checkPHPin(Garage $garage)
    {
	//Проверяю направление проезда.

	if($this->isEnter ){
	//проверяю наличие свободных мест
	//т.к. гараж может иметь на разых площадках, то и количество свободных мест надо считать для каждой площадки.
			//echo Debug::vars('142',$garage->getPlaceCount($cvs->id_parking));
			//echo Debug::vars('143',$garage->getPlaceCountUccuped($cvs->id_parking));
		Log::instance()->add(Log::NOTICE, '144 всего мест :count, занято :occuped.',
			array(
				':count'=>$garage->getPlaceCount($this->id_parking),
				':occuped'=>$garage->getPlaceCountUccuped($this->id_parking)
				));
 		 if($garage->getPlaceCount($this->id_parking) - $garage->getPlaceCountUccuped($this->id_parking) >0){
			//свободные места есть, можно запускать.
			 Log::instance()->add(Log::NOTICE, '238 мест хватает, въезд разрешен.', array(':data'=>'data'));
			 return true;
		 } else {
			 //мест нет, не запускать.
		//*добавить событие  в hl_events
		//*дать команду на ворота: что вывести табло.
			
				Log::instance()->add(Log::NOTICE, '245 мест нет :data', array(':data'=>1));
			 return false;
		 }
	
	} else { //выезд. При выезде надо проверять: разрешен ли выезд, если ГРЗ нет на паркове.
	// алгоритм зависит от параметра учета парковки.
			// $event->eventCode=events::OK;
		// $event->grz=$grz;
		// $event->addEventRow();
		// echo Debug::vars('165 выезд!!!');
		// есть ли ГРЗ и id_pep в таблице hl_inside?
		// если есть, то удалить оттуда.
		// а если нет, то удалить кого-нибудь другого...
		//	if($identifier->checkInParking($this->id_parking))
		//	{
	// * удалить идентификатор из таблицы hl_inside
	// *	сделать запись в журнале событий
	// * открыть ворота
		//		echo Debug::vars('174 Был на территории, можно выезжать!');
		//		return true;
		//	} else {
				// * надо хоть кого-то удалить
				// * сделать запись в журнале событий о нарушении порядка
				// * сделать запись в журнале событий о выезде
				// *открыть ворота
		//		echo Debug::vars('180 на территории не был, пытается выехать. что делать?');
		//		Log::instance()->add(Log::NOTICE, ' на территории не был, пытается выехать. что делать?', array(':data'=>1));

		}
	}
	


	/**20.07.2025 проверка разрешения на выезд
	*
	*
	*/
	
	public function checkPHPout()
	{
		return true;
	}


	/**
	*@input $garage->id_parking - список парковок, на которых расположены машиноместа гаража
	*Функция позволяет определить "правльность" ворот: вдруг к чужим подъехал?
	*/
	
   public function checkAccess($listParking)
	{

		// Log::instance()->add(Log::NOTICE, '287 checkAccess $this->id_parking.'. Debug::vars($this));
		// Log::instance()->add(Log::NOTICE, '288 checkAccess $listParking.'. Debug::vars($listParking));
		// Log::instance()->add(Log::NOTICE, '289 checkAccess $listParking.'. Debug::vars(in_array ($this->id_parking, $listParking)));

		if(in_array ($this->id_parking, $listParking)) return true;
		return false;
	}

	/**21.07.2025 проверка категория доступа СКУД. Если есть разрешение на проезд, то ответ true
	*@input $garage->id_parking - список парковок, на которых расположены машиноместа гаража
	*Функция позволяет определить "правльность" ворот: вдруг к чужим подъехал?
	*/
	
   public function checkAccessForNonGarage($id_pep)
	{

		$sql='select count(a.id_dev) from ss_accessuser ssa
			join access a on a.id_accessname=ssa.id_accessname
			where ssa.id_pep='.$id_pep.'
			and a.id_dev='.$this->id_dev;
		Log::instance()->add(Log::NOTICE, '287 :sql', array(':sql'=> Debug::vars($sql)));	
			
	$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('COUNT');
			
		if($query>0) return true;
	return false;

		
	}


}


