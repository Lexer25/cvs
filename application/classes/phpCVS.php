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
		return;
    }
	
	
	/** 05.06.2025
	*Процесс валидации ГРЗ в указанной точке проезда
	*@param id_dev - точка проезда
	*@param GRZ - номерной знак
	*@return void
	*в отличии от check() не делает записи в базе данных СКУД.
	*/

	 public function validation()
    {
      
		
		$sql='select EVENT_TYPE as event_type, id_pep from VALIDATEPASS_HL_PARKING_2('.$this->id_dev.', \''.$this->grz.'\', NULL)';
		//echo Debug::vars('92', $sql);exit;
		
		//Log::instance()->add(Log::NOTICE, '193 '. $sql); 
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
		
		$query=Arr::get($query, 0);
		$this->getMessForEvent(Arr::get($query, 'EVENT_TYPE'));
		//$this->getMessForIdle();
		$this->code_validation = Arr::get($query, 'EVENT_TYPE');
		return;
    }
	
	
	
/** 3.05.2025
*Процесс валидации ГРЗ и UHF в указанной точке проезда средствами php (не процедура в БД СКУД).
*@param id_parking - id парковочной площадки
*@param is_enter - true - въезд, false - выезд
*@param GRZ - номерной знак
*@return void
*/

 public function checkPHPin()
    {
		
		//валидация card. если неуспешно, то сразу отказ в проезде (RC=46)
		//если выезд, то праверить только разрешение на выезд и:
			//разрешить выезд
			//удалить card из таблицы inside
		//если въезд, то проверить наличие свободных мест:
			//подсчет количества машиномест в гараже для этого card
			//подсчет уже занятых мест в этом гараже.
			//сравнение.
			//если места есть, то:
				//разрешить въезд
				//отметить card в таблице inside
		
		$sql='select rc as event_type, id_pep from REGISTERPASS_HL_2('.$this->id_dev.', \''.$this->grz.'\', NULL)';
		//$sql='select event_type, id_pep from VALIDATEPASS_HL_PARKING('.$this->id_dev.', \''.$this->grz.'\', NULL)';
		echo Debug::vars('83', $sql);exit;
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
		
		$query=Arr::get($query, 0);
		$this->getMessForEvent(Arr::get($query, 'EVENT_TYPE'));
		//$this->getMessForIdle();
		$this->code_validation = Arr::get($query, 'EVENT_TYPE');
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
 
 /*
 установка семафора для синхронизации работы систем.
 $semaforName - имя семаформа
$data - содержимое семафора
 
 */
/* public function setSemafor($semaforName, $data)
 {
		$fp = fopen($semaforName, "w"); // Открываем файл в режиме записи	
		$test = fwrite($fp, $data); // Запись в файл
		fclose($fp); //Закрытие файла
		
	 return;
 }
  */
 

 /*
 прочитать семафора для синхронизации работы систем.
 $semaforName - имя семаформа
$data - содержимое семафора
 
 */
/* public function getSemafor($semaforName)
 {
		$handle = fopen($semaforName, "r");
		$contents = fread($handle, filesize($semaforName));
		fclose($handle);
		
	 return $contents;
 }
  */
 



   
}
