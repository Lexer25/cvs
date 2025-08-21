<?php defined('SYSPATH') or die('No direct script access.');


/**
* Класс для работы с событиями парковочной системы
 
 */
class events {
	
				public $eventCode='null';
				public $event_time='null';
				public $is_enter='null';
				public $id_garage='null';
				public $rubi_card='null';
				public $grz;
				public $comment='';
				public $photo='null';
				public $id_pep='null';
				public $id_gate='null';
				public $created='null';
				
				
				const CARLIMITEXCEEDED=81;//нет мест
				const UNKNOWNCARD=46;//неизвестная карта
				const DISABLEDCARD=65;//карта неактивна
				const CARDEXPIRED=65;//срок действия карты истек
				const DISABLEDUSER=65;//пользователь неактивен
				const ACCESSDENIED=65;//доступ запрещен
				
				//события по идентификаторам без гаража
				const WOK=6;//повторный въезд по категории доступа
				const OK=50;//въезд или выезд разрешены по категории доступа. Тут полное совпадение с событиями СКУД
				
				//события с идентификатором с гаражом
				const G_OK_PLACE=15;//въезд разрешен по наличию гаража, места есть
				const G_WOK_PEP=16;//повторный въезд разрешен.Этого ГРЗ на парковке нет, но есть этот пипел
				const G_WOK=18;//повторный въезд разрешен .Этот ГРЗ уже есть на парковке
				
				const G_OK=17;//выезд разрешен, имеется гараж, на парковке находится
				const G_OK_2=14;//выезд разрешен без въезда, имеется гараж, на парковке не находится
				const UNKNOWNRESULT=-1;
				const ANALYSERBUSY=7;
				
				
				
				
				
	
	public function addEvent()//добавление события в таблицу HL_EVENTS
	{
		switch($this->eventCode){
			case self::UNKNOWNCARD:
			case self::DISABLEDCARD:
			case self::CARDEXPIRED:
			case self::DISABLEDUSER:
			case self::WOK:
			case 8:
			
			$_data=array(
				'eventCode'=>$this->eventCode,
				'grz='=>$this->grz,
				'id_gate='=>$this->id_gate,
				);
			break;
			
			default:
			
				Log::instance()->add(Log::NOTICE, '49 попытка записать событий с неизвестным кодом. Данные события :data', array(':data'=>Debug::vars($this)));
			break;
			
			
			
		}
		
	}
	
	
	
	//19.07.2025
	public function addEventRow()//добавление события в таблицу HL_EVENTS
	{
		$config = Kohana::$config->load('config');
		$mess=$this->comment;
		if(Arr::get($config, 'debug')) $mess = $mess. ' debug_ON' ;
		if(Arr::get($config, 'testMode')) $mess = $mess. ' testMode_ON' ;
		//if(is_null($this->id_garage)) $this->id_garage = '\'NULL\'' ;
		//Log::instance()->add(Log::NOTICE, '80  :data', array(':data'=>Debug::vars($this)));
		
		$_data=array(
				':EVENT_CODE'=>$this->eventCode,
				':EVENT_TIME'=>$this->event_time,
				':IS_ENTER'=>$this->is_enter,
				':RUBI_CARD'=>$this->rubi_card,
				':ID_GARAGE'=>$this->id_garage,
				':GRZ'=>'\''.$this->grz.'\'',
				':COMMENT'=>'\''.$mess.'\'',
				':PHOTO'=>$this->photo,
				':ID_PEP'=>$this->id_pep,
				':ID_GATE'=>$this->id_gate,
				':CREATED'=>$this->created,
				':EVENT_TIME'=>'\''.date('d.m.Y H:i:s').'\''
			);
			
		//INSERT INTO HL_EVENTS (ID,EVENT_CODE,EVENT_TIME,IS_ENTER,RUBI_CARD,PARK_CARD,GRZ,COMMENT,PHOTO,ID_PEP,ID_GATE,CREATED,ID_GARAGE) 
		//VALUES (25076,101,'19-AUG-2025 20:21:43',NULL,NULL,NULL,'',' debug_ON',NULL,NULL,NULL,'19-AUG-2025 20:21:43',NULL);
		$sql=__('INSERT INTO HL_EVENTS (EVENT_CODE,EVENT_TIME,IS_ENTER,RUBI_CARD,ID_GARAGE,GRZ,COMMENT,PHOTO,ID_PEP,ID_GATE)
			VALUES (:EVENT_CODE,:EVENT_TIME,:IS_ENTER,:RUBI_CARD,:ID_GARAGE,:GRZ,:COMMENT,:PHOTO,:ID_PEP,:ID_GATE)', $_data);
			
		//echo Debug::vars('78',$_data, $sql);
		Log::instance()->add(Log::NOTICE, '82  :data', array(':data'=>$sql));
		try
			{
				$query = DB::query(Database::INSERT, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				
			} catch (Exception $e) {
				//Log::instance()->add(Log::DEBUG, 'Line 98 '. $e->getMessage());
							
			}
		
	}
	
	
}
