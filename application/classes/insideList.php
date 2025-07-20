<?php defined('SYSPATH') or die('No direct script access.');


/**
* Класс для работы с таблицей hl_inside
 
 */
class insideList {
	
				public $entertime='null';
				public $id_card='null';
				public $id_pep='null';
				public $id_parking='null';
							
			
	
	public function addToInside()//добавление события в таблицу HL_EVENTS
	{
	
	$this->delFromInside();
	$_data=array(
				':ENTERTIME'=>'\'now\'',
				':ID_CARD'=>'\''.$this->id_card.'\'',
				':ID_PEP'=>$this->id_pep,
				':COUNTERID'=>$this->id_parking,
				
			);
			
	//если такой id_pep уже есть, то его надо удалить и добавить обновленные параметры.
	
	/* $sql=__('delete from hl_inside hli where hli.id_pep=:ID_PEP', $_data);
		Log::instance()->add(Log::NOTICE, '30  :data', array(':data'=>$sql));	exit;
		try
			{
				$query = DB::query(Database::DELETE, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 4 '. $e->getMessage());
							
			} */
	
	$sql=__('INSERT INTO HL_INSIDE (ENTERTIME,ID_CARD,ID_PEP,COUNTERID) VALUES (:ENTERTIME,:ID_CARD,:ID_PEP,:COUNTERID)', $_data);
		
	
		try
			{
				$query = DB::query(Database::INSERT, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 4 '. $e->getMessage());
							
			}
		
		
	}
	
	
	public function delFromInside()//добавление события в таблицу HL_EVENTS
	{
		$_data=array(
				':ENTERTIME'=>'\'now\'',
				':ID_CARD'=>'\''.$this->id_card.'\'',
				':ID_PEP'=>$this->id_pep,
				':COUNTERID'=>$this->id_parking,
				
			);
	
		$sql=__('delete from hl_inside hli where hli.id_pep=:ID_PEP', $_data);
		//Log::instance()->add(Log::NOTICE, '71  :data', array(':data'=>$sql));	exit;
		try
			{
				$query = DB::query(Database::DELETE, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 4 '. $e->getMessage());
							
			}
			
	}
	
		/**20.07.2025 проверка, что этот ГРЗ уже находится на этой парковке
		*
		*/
		public static function checkGrzInParking($id_card)
		{
			$_data=array(
				//':ENTERTIME'=>'\'now\'',
				':ID_CARD'=>'\''.$id_card.'\'',
				//':ID_PEP'=>$this->id_pep,
				//':COUNTERID'=>$this->id_parking,
				
			);
	
		$sql=__('select count(*) from hl_inside hli where hli.id_card=:ID_CARD', $_data);
		//Log::instance()->add(Log::NOTICE, '71  :data', array(':data'=>$sql));	exit;
		try
			{
				$query = DB::query(Database::SELECT, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'))
				->get('COUNT');
				if($query > 0) return true;
				return false;
				
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 4 '. $e->getMessage());
							
			}
			
		}
	
}
