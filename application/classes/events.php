<?php defined('SYSPATH') or die('No direct script access.');


/**
* Класс для работы с событиями парковочной системы
 
 */
class events {
	
				public $eventCode='null';
				public $event_time='null';
				public $is_enter='null';
				public $rubi_card='null';
				public $park_card='null';
				public $grz='null';
				public $comment='null';
				public $photo='null';
				public $id_pep='null';
				public $id_gate='null';
				public $created='null';
				
				
				const CARLIMITEXCEEDED=81;
				const UNKNOWNCARD=46;
				const DISABLEDCARD=65;
				const CARDEXPIRED=65;
				const DISABLEDUSER=65;
				const ACCESSDENIED=65;
				const OK=50;
	
	public function addEvent()//добавление события в таблицу HL_EVENTS
	{
		switch($this->eventCode){
			case self::UNKNOWNCARD:
			case self::DISABLEDCARD:
			case self::CARDEXPIRED:
			case self::DISABLEDUSER:
			$_data=array(
				'eventCode'=>$this->eventCode,
				'grz='=>$this->grz,
				'id_gate='=>$this->id_gate,
				);
			break;
			
			
			
		}
		
	}
	
	
	
	//19.07.2025
	public function addEventRow()//добавление события в таблицу HL_EVENTS
	{
		$_data=array(
				':EVENT_CODE'=>$this->eventCode,
				':EVENT_TIME'=>$this->event_time,
				':IS_ENTER'=>$this->is_enter,
				':RUBI_CARD'=>$this->rubi_card,
				':PARK_CARD'=>$this->park_card,
				':GRZ'=>'\''.$this->grz.'\'',
				':COMMENT'=>$this->comment,
				':PHOTO'=>$this->photo,
				':ID_PEP'=>$this->id_pep,
				':ID_GATE'=>$this->id_gate,
				':CREATED'=>$this->created
			);
		
		$sql=__('INSERT INTO HL_EVENTS (EVENT_CODE,IS_ENTER,RUBI_CARD,PARK_CARD,GRZ,COMMENT,PHOTO,ID_PEP,ID_GATE)
			VALUES (:EVENT_CODE,:IS_ENTER,:RUBI_CARD,:PARK_CARD,:GRZ,:COMMENT,:PHOTO,:ID_PEP,:ID_GATE)', $_data);
			
		//echo Debug::vars('78',$_data, $sql);exit;	
		Log::instance()->add(Log::NOTICE, '82  :data', array(':data'=>$sql));
		try
			{
				$query = DB::query(Database::INSERT, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 98 '. $e->getMessage());
							
			}
		
	}
	
	
}
