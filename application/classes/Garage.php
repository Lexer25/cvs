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
  //  public $placeCountUccuped;//количество занятых машиномест
    public $id_parking=array();//список парковочных площадей, на которых находятся места гаража
    public $not_count=false;//false - посдчет количества свободных мест ВЕДЕТСЯ, true - НЕ ведется.
    
	
	public $grzIn=array();//список грз, находящихся внутри
	
	
   
   
  
    public function __construct($id)// id_gate - номер гаража
    {
       $sql='select first 1 hlgn.name, hlgn.not_count, hlgn.div_code, count(hlg.id_place) as placeCount from hl_garagename hlgn
			join hl_garage hlg on hlg.id_garagename=hlgn.id
			where hlgn.id='.$id.'
			group by hlgn.name, hlgn.not_count, hlgn.div_code';
			//echo Debug::vars('40', $sql);exit;
					
	   $query = Arr::flatten(DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array()
			);
		//Log::instance()->add(Log::NOTICE, '37 garage'. Debug::vars($query));
					$this->id=$id; 
					$this->placeCount=Arr::get($query, 'PLACECOUNT'); 
					$this->name=Arr::get($query, 'NAME'); 
					$this->not_count=Arr::get($query, 'NOT_COUNT'); 
				//	$this->placeCountUccuped=2; 
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
		//Log::instance()->add(Log::NOTICE, '86 '. $sql);
		//Log::instance()->add(Log::NOTICE, '78 '.$sql);
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('COUNT');

		return $query;
    }
	
		
		//30.04.2026 получить список ГРЗ и UHF, находящихся в этом гараже
		public static function getListIdentifierOnGarage($gate, $garage)
		//public function getListIdentifierOnGarage()
		{
			 					
			$sql_details='select hli.id_card from hl_inside hli
                    join people p on p.id_pep=hli.id_pep
                    join hl_orgaccess hlo on hlo.id_org=p.id_org
                    join hl_param hlp on hli.counterid=hlp.id_parking
                    where hlp.id='.$gate.'
                    and hlo.id_garage='.$garage;
					
    Log::instance()->add(Log::NOTICE, "105 ". $sql_details);
			$query = DB::query(Database::SELECT, $sql_details)
				->execute(Database::instance('fb'))
				->as_array(); 
	
	 if (empty($query)) {
        return ''; // Или вернуть пустую строку
    }
    
    // Формируем строку с перечнем ID_CARD в кавычках
    $ids = array_column($query, 'ID_CARD');
    $result = '"' . implode('", "', $ids) . '"';
    
    return $result;
			
		}
		public function getPlaceCountUccuped($id_place)//подсчет количества занятых мест на указанной площадке для текущего гаража
{
    // Подсчет количества занятых мест
    $sql = 'select count(hli.id_pep) from hl_inside hli
            join people p on p.id_pep=hli.id_pep
            join hl_orgaccess hlo on hlo.id_org=p.id_org
            where hli.counterid='.$id_place.'
            and hlo.id_garage='.$this->id;	
    
    $query = DB::query(Database::SELECT, $sql)
        ->execute(Database::instance('fb'))
        ->get('COUNT');
    
    // Логирование детальной информации о занятых местах
    $sql_details = 'select hli.* from hl_inside hli
                    join people p on p.id_pep=hli.id_pep
                    join hl_orgaccess hlo on hlo.id_org=p.id_org
                    where hli.counterid='.$id_place.'
                    and hlo.id_garage='.$this->id;
    
    $details_query = DB::query(Database::SELECT, $sql_details)
        ->execute(Database::instance('fb'))
        ->as_array();
    
    // Запись в лог-файл
    Log::instance()->add(Log::NOTICE, '122 getPlaceCountUccuped для гаража ID='.$this->id.', площадка ID='.$id_place.
        ' | Количество занятых мест: '.$query.
        ' | Детализация: '.Debug::vars($details_query));
    
    return $query;
}
		
		public function getPlaceCountUccuped_0($id_place)//подсчет количества занятых мест на указанной площадке для текущего гаража
			{
				$sql='select count(hli.id_pep) from hl_inside hli
						join people p on p.id_pep=hli.id_pep
						join hl_orgaccess hlo on hlo.id_org=p.id_org
						where hli.counterid='.$id_place.'
						and hlo.id_garage='.$this->id;	
				//	echo Debug::vars('53', $sql);exit;
				Log::instance()->add(Log::NOTICE, '105 '. $sql);
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
