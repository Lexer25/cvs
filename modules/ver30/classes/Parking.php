<?php

/**
* @package    ParkResident/Parking
 * @category   Base
 * @author     Artonit
 * @copyright  (c) 2025 Artonit Team
 * @license    http://artonit/ru 
 
 */

/* Parking 
Класс описывает парковочную площадь - территорию, где находятся парковочные места.


*/

class Parking
{
	public $name;
	public $id;
	public $is_active;
	public $created;
	public $parent;// родительская резиденция.
	public $modify;//дата последнего изменения
	public $mess;//сообщения всякие
	public $count;//количество машиномест
	public $is_apb=false;//контроль направления проезда
	public $is_test=false;//режим работы ТЕСТ (т.е. пропускает всех)
	
	
	private $is_apb_mask=0;//маска бита apb
	private $is_test_mask=1;//маска бита test

	
	
	
	public function __construct($id=null)
    {
       if(!is_null($id))//если указан id, то создаю экземпляр класса с данными из БД.
	   {
	   $this->id = $id;
		$sql='select hlr.id, hlr.name, hlr.enabled, hlr.created, hlr.modify, hlr.parent, hlr.maxcount, s.value_int, s2.value_int as is_test from hl_parking hlr
            left join hl_setting s on s.name=\'is_apb\' and s.value_int=hlr.id
            left join hl_setting s2 on s2.name=\'is_test\' and s2.value_int=hlr.id
			where hlr.id ='.$this->id;
			
		$sql='select hlr.id, hlr.name, hlr.enabled, hlr.created, hlr.modify, hlr.parent, hlr.maxcount, coalesce(hlr.mode, 0) as mode from hl_parking hlr
			where hlr.id ='.$this->id;
			
			
		//echo Debug::vars('30', $sql);exit;
		try
		{
			$query = Arr::flatten(DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array());
			$this->name=Arr::get($query, 'NAME');
			$this->is_active=Arr::get($query, 'ENABLED');
			$this->created=Arr::get($query, 'CREATED');
			$this->modify=Arr::get($query, 'MODIFY');
			$this->parent=Arr::get($query, 'PARENT');
			$this->count=Arr::get($query, 'MAXCOUNT');
			$this->is_apb=$this->isBitSet(Arr::get($query, 'MODE'), $this->is_apb_mask);
			$this->is_test=$this->isBitSet(Arr::get($query, 'MODE'), $this->is_test_mask);
			//$this->is_test=Arr::get($query, 'IS_TEST');
			
			
			//echo Debug::vars('69', $sql, $query, $this); exit;
			
		} catch (Exception $e) {
			echo Debug::vars('30', $sql, $e->getMessage()); exit;
			Log::instance()->add(Log::DEBUG, 'Line 40 '. $e->getMessage());
		
		}
	   } else { // если не указан id, то создаю пустой экземпляр класса
			
	   }
	}
	
	/*
	26.08.2023
	Сохранение данных
	*/
	
	public function add()
	{
		//echo Debug::vars('61', $this->name);exit;
		$sql='INSERT INTO HL_parking (NAME, PARENT, ENABLED, MAXCOUNT, MODE)
			values (\''.$this->name.'\', '.$this->parent.', '.$this->is_active.', '.$this->count.', 0)';
			
			
		//echo Debug::vars('783', $sql);exit;
		try
				{
				$query = DB::query(Database::INSERT, iconv('UTF-8','windows-1251',$sql))
				->execute(Database::instance('fb'));
				return true;
				} catch (Exception $e) {
					Log::instance()->add(Log::NOTICE, '794 '. $e->getMessage());
					return false;
				}
		return;
	}
	
	/*
	26.08.2023
	Изменение данных для указанного id
	*/
	public function update()
	{
		//echo Debug::vars('36', $this); exit;
		//Получаю текущее значение MODE
		$mode=0;
		$sql='select coalesce(hlr.mode, 0) as mode from hl_parking hlr
            where hlr.id =4';
		$mode = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->get('MODE')
				;	
				
		
		//Устанавливаю новые значения
		if(filter_var($this->is_apb, FILTER_VALIDATE_BOOLEAN))
		{
			$mode=$this->setBit($mode, $this->is_apb_mask);
		} else {
			$mode=$this->clearBit($mode, $this->is_apb_mask);
		}
		if(filter_var($this->is_test, FILTER_VALIDATE_BOOLEAN))
		{
			
			$mode=$this->setBit($mode, $this->is_test_mask);
		} else {
			$mode=$this->clearBit($mode, $this->is_test_mask);
		}
		//echo Debug::vars('137', $this, $mode);exit;
		
		$sql='UPDATE HL_PARKING
				SET NAME = \''.$this->name.'\',
				ENABLED = '.$this->is_active.',
				PARENT = '.$this->parent.',
				MAXCOUNT = '.$this->count.',
				MODE='.$mode.'
			WHERE (ID = '.$this->id.')';
		Log::instance()->add(Log::DEBUG, 'Line 101 '. $sql);
		//echo Debug::vars('147', $sql); exit;
		try
			{
			$query = DB::query(Database::UPDATE, iconv('UTF-8', 'CP1251',$sql))
			->execute(Database::instance('fb'));
			return true;
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 112 '. $e->getMessage());
				
				$this->mess=$e->getMessage();
				return 	false;
			}
	}
	
	
	/*
	26.08.2023
	Удаление указаннй площадки по ее id
	*/
	public function del()
	{
		//echo Debug::vars('36', $this->name, $this->standalone);
		
		$sql='delete from hl_parking hlr
			where hlr.id='.$this->id;
		//Log::instance()->add(Log::DEBUG, 'Line 72 '. $sql);
		//echo Debug::vars('65', $sql); exit;
		try
			{
			$query = DB::query(Database::DELETE, iconv('UTF-8', 'CP1251',$sql))
			->execute(Database::instance('fb'));
			
			return true;
			
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 139 '. $e->getMessage());
				$this->mess=$e->getMessage();	
				return false;				
			}
	}
	
	//30.06.2025 проверка, что бит установлен. Отсчет начинается с позиции 0.
	public function isBitSet($number, $position) {
		return ($number & (1 << $position)) !== 0;
	}
	
	//30.06.2025 Установка бита в заданной позиции. Позиция отсчитывается с 0.
	function setBit($number, $position) {
		return $number | (1 << $position);
	}
	
	//30.06.2025 Сброс бита в заданной позиции. Позиция отсчитывается с 0.
	function clearBit($number, $position) {
		return $number & ~(1 << $position);
	}
   
}
