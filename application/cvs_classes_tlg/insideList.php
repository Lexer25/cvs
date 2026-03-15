<?php defined('SYSPATH') or die('No direct script access.');


/**
* Класс для работы с таблицей hl_inside
 
 */
class insideList {
	
				public $entertime='null';
				public $id_card='null';
				public $id_pep='null';
				public $id_parking='null';
							
			
	
	/**очистка таблицы hl_inside
	*
	*/
	public static function clearInside()
	{
		$sql='delete from hl_inside';
		try
			{
				$query = DB::query(Database::DELETE, $sql)
				->execute(Database::instance('fb'));
				return 0;
				
			} catch (Exception $e) {
				//Log::instance()->add(Log::DEBUG, 'Line 30 '. $e->getMessage());
				return 1;
							
			}
		
	}
	
	public function addToInside()//добавление события в таблицу HL_INSIDE
	{
	
	$this->delFromInside();// удаляю запись для этого id_pep. В результате после добавления новой записи будет обновлен номер идентификатора
	$_data=array(
				':ENTERTIME'=>'\'now\'',
				':ID_CARD'=>'\''.$this->id_card.'\'',
				':ID_PEP'=>$this->id_pep,
				':COUNTERID'=>$this->id_parking,
				
			);
			
	
	$sql=__('INSERT INTO HL_INSIDE (ENTERTIME,ID_CARD,ID_PEP,COUNTERID) VALUES (:ENTERTIME,:ID_CARD,:ID_PEP,:COUNTERID)', $_data);
		try
			{
				$query = DB::query(Database::INSERT, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				
			} catch (Exception $e) {
				//Log::instance()->add(Log::DEBUG, 'Line 59 '. $e->getMessage());
							
			}
		
		
	}
	
	
	public function delFromInside()
	{
		$_data=array(
				':ENTERTIME'=>'\'now\'',
				':ID_CARD'=>'\''.$this->id_card.'\'',
				':ID_PEP'=>$this->id_pep,
				':COUNTERID'=>$this->id_parking,
				
			);
	
		$sql=__('delete from hl_inside hli where hli.id_pep=:ID_PEP', $_data);
		//Log::instance()->add(Log::NOTICE, '71  :data', array(':data'=>$sql));	//exit;
		try
			{
				$query = DB::query(Database::DELETE, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				//Log::instance()->add(Log::DEBUG, 'Line 78 people :id_pep из паркинга :id_parking удалени успешно. ', array(':id_pep'=>$this->id_pep, ':id_parking'=>$this->id_parking));
				
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 78 '. $e->getMessage());
							
			}
			
	}
	
		/**20.07.2025 проверка, что этот ГРЗ уже находится на этой парковке
		*
		*/
		public static function checkGrzInParking($identifier)
		{
			$_data=array(
				//':ENTERTIME'=>'\'now\'',
				':ID_CARD'=>'\''.$identifier->id.'\'',
				':ID_PEP'=>$identifier->id_pep,
				//':COUNTERID'=>$this->id_parking,
				
			);
	
		//$sql=__('select count(*) from hl_inside hli where hli.id_card=:ID_CARD or hli.id_pep=:ID_PEP', $_data);
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
				Log::instance()->add(Log::DEBUG, 'Line 96 '. $e->getMessage());
							
			}
			
		}
		
		/**20.07.2025 проверка, что этот id_pep уже находится на этой парковке
		*
		*/
		public static function checkIdPepInParking($identifier)
		{
			$_data=array(
				//':ENTERTIME'=>'\'now\'',
				':ID_CARD'=>'\''.$identifier->id.'\'',
				':ID_PEP'=>$identifier->id_pep,
				//':COUNTERID'=>$this->id_parking,
				
			);
	
		$sql=__('select count(*) from hl_inside hli where hli.id_pep=:ID_PEP', $_data);
			//Log::instance()->add(Log::NOTICE, '71  :data', array(':data'=>$sql));	exit;
		try
			{
				$query = DB::query(Database::SELECT, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'))
				->get('COUNT');
				if($query > 0) return true;
				return false;
				
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 96 '. $e->getMessage());
							
			}
			
		}
	
}
