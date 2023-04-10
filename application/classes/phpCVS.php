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
	public $id_gate;		/* номер ворот*/
	public $id_dev;		/* id_dev, обслуживающий эти ворота*/
	public $mode;		/* режим работы ворот*/
	public $id_parking;	/* id паркинга */
	public $eventdMess;	/* сообщения для вывода на табло */
	public $messParam;	/* параметры сообщения для вывода на табло */

  
    public function __construct($cam)
    {
       $sql='select hlp.tablo_ip, 
					hlp.tablo_port,
					hlp.box_ip,
					hlp.box_port,
					hlp.id_gate,
					hlp.id_dev,
					hlp.mode,
					hlp.id_parking,
					hlp.is_enter from hl_param hlp where hlp.id_cam='.$cam;
					
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
       
		$sql='select event_type, id_pep from VALIDATEPASS_HL_PARKING('.$this->id_dev.', \''.$this->grz.'\', NULL)';
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
		//echo Debug::vars('73', $sql, $query);
		$query=Arr::get($query, 0);
		$this->getMessForEvent(Arr::get($query, 'EVENT_TYPE'));
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



   
}
