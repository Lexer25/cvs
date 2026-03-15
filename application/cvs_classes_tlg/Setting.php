<?php

/**

* @package    ParkResident/Garage
 * @category   Base
 * @author     Artonit
 * @copyright  (c) 2025 Artonit Team
 * @license    http://artonit/ru 
 
 */

/* Setting 
Класс для работы с таблицей HL_Setting

*/

class Setting
{
   private $ver='1.0';
	
	
	
	 public function __construct()
    {
      
	}
	
	
	/*
	*30.08.2023
	*Получить массив имен параметров hl_setting
	**/
	public function getNameList()
	{
		$sql='select * from hl_setting';
		echo Debug::vars('72');exit;
		$res=array();
		try
		{
			$res = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
			
			
		} catch (Exception $e) {
			////echo Debug::vars('30', $sql, $e->getMessage()); exit;
			Log::instance()->add(Log::DEBUG, 'Line 52 '. $e->getMessage());
		
		}
		echo Debug::vars('86', $res);exit;
		return $res;
	}
	
	/*
	*30.08.2023
	*получить данные из таблицы hl_setting
	*@input $name - имя переменной, которое надо получить.
	*@input $default - значение по умолчанию, передается в ответ если значение для $name не найдено
	*/
	public static function get($name, $default=null)
	{
		$sql='select coalesce(hls.value_str, hls.value_int) from hl_setting hls
			where hls.name=\''.$name.'\'';
		//echo Debug::vars('99', $sql);//exit;
		$res=array();
		try
		{
			$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('COALESCE');
			if(is_null($query)){
				$res=$default;
			} else {
				$res=$query;
			}
						
		} catch (Exception $e) {
			////echo Debug::vars('30', $sql, $e->getMessage()); exit;
			Log::instance()->add(Log::DEBUG, 'Line 52 '. $e->getMessage());
			$res=$default;
		}
		//echo Debug::vars('80', $res);exit;
		return $res;
	}
	
	
	
	/*
	17.07.2025
	Добавление данных
	*/
	
	public function add()
	{
		//echo Debug::vars('36', $this->name, $this->standalone);
		 $sql=__('INSERT INTO TABLE (NAME, standalone)
			VALUES (\':NAME\', :standalone)', 
		array(
			':NAME'=>$this->name,
			':standalone'=>$this->standalone,
			));
	//echo Debug::vars('45', $sql); exit;
		try
			{
				$query = DB::query(Database::INSERT, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				$this->result=$this->result_ok;
				$this->edesc='OK';
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 83 '. $e->getMessage());
				$this->result=$this->result_err;
				$this->edesc=$e->getMessage();				
			}
	}
	
	/**
	17.07.2025
		Добавлени или обновление данных в таблице БД
		$input $name - название (имя) ключа
		$input $value - значение ключа
		$input $type - тип значения ключа: 'int' - integer, 'str' - string
	*/
	public function update($name, $value, $type)
	{
		//проверка: есть ли такой ключ в таблице?
		//если ключа нет, то добавляю его.
		//если ключ есть, то обновляю его.
		
		$sql='select count(*) from hl_setting hls
			where hls.name=\''.$name.'\'';

		if(DB::query(Database::INSERT, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb')))
				{//ключ есть в базе данных. надо просто обновить.
			//	echo Debug::vars('135');exit;
					switch($type){
						case 'int':
						$sql=__('update hl_setting hls
									set hls.value_int=:value 
									where hls.name=\':NAME\'', array(
									':NAME'=>$name,
									':value'=>$value
								)); 
						break;
						case 'str':
						$sql=__('update hl_setting hls
									set hls.value_str=:value 
									where hls.name=\':NAME\'', array(
									':NAME'=>$name,
									':value'=>$value
								)); 
						break;
					}
					//echo Debug::vars('179', $sql);exit;
						try
							{
								$query = DB::query(Database::UPDATE, iconv('UTF-8', 'CP1251',$sql))
								->execute(Database::instance('fb'));
								Session::instance()->set('ok_mess', array('desc'=>'Данные обновлены успешно'));
							} catch (Exception $e) {
								Log::instance()->add(Log::DEBUG, 'Line 83 '. $e->getMessage());
								Session::instance()->set('e_mess', array('desc'=>$e->getMessage()));
							}
							
						
					
				} else {
					//если ключа нет, то добавляю его.
			//		echo Debug::vars('165');exit;
					switch($type){
						case 'int':
							 $sql=__('INSERT INTO hl_setting (NAME, VALUE_INT)
								VALUES (\':NAME\', :value)', array(
									':NAME'=>$name,
									':value'=>$value
								)); 
						break;
						case 'str':
						 $sql=__('INSERT INTO hl_setting (NAME, VALUE_str)
								VALUES (\':NAME\', \':value\')', array(
									':NAME'=>$name,
									':value'=>$value
								)); 
						break;
					}
					//echo Debug::vars('208', $sql);exit;
						try
							{
								$query = DB::query(Database::INSERT, iconv('UTF-8', 'CP1251',$sql))
								->execute(Database::instance('fb'));
							} catch (Exception $e) {
								Log::instance()->add(Log::DEBUG, 'Line 83 '. $e->getMessage());
							}
				}
	}
	
	
	/*
	26.08.2023
	Удаление данных для указанного id
	*/
	public function delete()
	{
		//echo Debug::vars('36', $this->name, $this->standalone);
		
		$sql='delete from ...';
		Log::instance()->add(Log::DEBUG, 'Line 72 '. $sql);
		//echo Debug::vars('65', $sql); exit;
		try
			{
			$query = DB::query(Database::DELETE, iconv('UTF-8', 'CP1251',$sql))
			->execute(Database::instance('fb'));
			
			$this->result=$this->result_ok;
			$this->edesc=$this->id;
			
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 139 '. $e->getMessage());
				$this->result=$this->result_err;
				$this->edesc=$e->getMessage();				
			}
	}
	
	
   
}
