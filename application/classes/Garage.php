<?php

/*
класс Garage описывает свойств и методы гаража: количество мест, количество занятых мест.
Метод:
- добавить грз
- удалить ГРЗ
*/
class Garage
{
    public $id;        //id гаража
    public $name;        //название гаража
    public $placeCount;//количество машиномест в гараже
    public $placeCountUccuped;//количество занятых машиномест
    public $id_parking=array();//список парковочных площадей, на которых находятся места гаража
    
	
	public $grzIn=array();//список грз, находящихся внутри
	
	
   
   
  
    public function __construct($id)// id_gate - номер гаража
    {
      /*  $sql='select hlp.tablo_ip, 
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
			->as_array(); */
			
					$this->id=$id; 
					$this->placeCount=3; 
					$this->placeCountUccuped=2; 
					$this->id_parking=$this->id_parking();

			return;
			
    }

	
	/**19.07.2025 подготовка списка парковочных площадей, на которых расположениы машиноместа гаража
	*
	*
	*/
	public function id_parking()
	{
		$sql='select distinct hlp.id_parking from hl_garage hlg
			join hl_place hlp on hlp.id=hlg.id_place
			where hlg.id_garagename='.$this->id;
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
			
			$res=array();
			foreach ($query as $key=>$value)
			{
				$res[]=Arr::get($value, 'ID_PARKING');
				
			}
			return $res;
		
	}
	
	/**19.07.2025 подсчет общего количества мест на указанной площадке для текущего гаража
	*это не общее количество машиномест в гараже, а именно на указанной площадке.
	*
	*
	*/
	public function getPlaceCount($id_place)
    {
       
		$sql='select count(hlp.id) from hl_place hlp
			join hl_garage hlg on hlg.id_place=hlp.id
			where hlg.id_garagename='.$this->id.'
			and hlp.id_parking='.$id_place;
		//	echo Debug::vars('53', $sql);exit;
		//Log::instance()->add(Log::NOTICE, '78 '.$sql);
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('COUNT');

		return $query;
    }
	
	
	
		public function getPlaceCountUccuped($id_place)//подсчет количества занятых мест на указанной площадке для текущего гаража
			{
				$sql='select count(hli.id_pep) from hl_inside hli
						join people p on p.id_pep=hli.id_pep
						join hl_orgaccess hlo on hlo.id_org=p.id_org
						where hli.counterid='.$id_place.'
						and hlo.id_garage='.$this->id;	
				//	echo Debug::vars('53', $sql);exit;
				$query = DB::query(Database::SELECT, $sql)
					->execute(Database::instance('fb'))
					->get('COUNT');

				return $query;
			}
	
		/** 19.08.2025 удаление ГРЗ из гаража на указанной площадке
		*/
		public function clearOnPlace($id_place)
		{
			
			
		}


		 public function check()
			{
			   
				$sql='select rc as event_type, id_pep from REGISTERPASS_HL_2('.$this->id_dev.', \''.$this->grz.'\', NULL)';
				
				echo Debug::vars('94', $sql);exit;
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
		 

   
}
