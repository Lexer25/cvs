<?php

/*
класс Gate описывает свойств и методы работы ворот, используемые в парковочной системе
Ворота - это шлагбаум, привод и т.п., чем надо каким-то образом управлять.
*/
class Gate
{
    public $id;        //id ворот
   	public $box_ip;		/* IP адрес шкафа (контроллера) управления*/
	public $box_port;	/* порт шкафа управления*/
	public $id_gate;		/* номер ворот*/
	public $id_dev;		/* id_dev, обслуживающий эти ворота*/
	public $mode;		/* режим работы ворот*/
	public $id_parking;	/* id паркинга */
	public $isEnter;	/* 0 для выезда, 1 для въезда */
	

  
    public function __construct($id_gate)// id_gate - номер ворот
    {
       $sql='select hlp.tablo_ip, 
					hlp.tablo_port,
					hlp.box_ip,
					hlp.box_port,
					hlp.id_gate,
					hlp.id_dev,
					hlp.mode,
					hlp.id_parking,
					hlp.is_enter,
					hlp.is_enter from hl_param hlp where hlp.id_cam='.$cam;
					echo Debug::vars('40', $sql);exit;
					
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
					$this->cam=$cam;
					
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


 public function check()
    {
       
		$sql='select rc as event_type, id_pep from REGISTERPASS_HL_2('.$this->id_dev.', \''.$this->grz.'\', NULL)';
		//$sql='select event_type, id_pep from VALIDATEPASS_HL_PARKING('.$this->id_dev.', \''.$this->grz.'\', NULL)';
		echo Debug::vars('73', $sql);exit;
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
