<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
*4.05.2025 Вспомогательные функции для работы парковочной системы
*/
class Model_Cvss extends Model {
	
	/**Провка: находится ли номер видеокамеры в настройках?
	*@param $cam номер видеокамеры
	*/
	public static function checkCamIsPresent ($cam) // 
	{
	$sql='select hlp.id_cam from hl_param hlp
	where hlp.id_cam='.$cam;
	return( DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('ID_CAM') >0);
	}
	
	/**Провка: находится ли полученный IP в настройках?
	*@param $cam номер видеокамеры
	*/
	public static function checkIpIsPresent ($ip) // 
	{
	$sql='select distinct hlp.box_ip from hl_param hlp
where hlp.box_ip=\''.$ip.'\'';
	return( DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('BOX_IP') >0);
	}
	
	
	
	/**Получить id ворот по номеру видеокамеры
	*@param $cam номер видеокамеры
	*/
	public static function getGateFromCam ($cam) // 
	{
	$sql='select hlp.id from hl_param hlp
	where hlp.id_cam='.$cam;
	return( DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('ID'));
	}
	
	
	/**Получить id ворот по IP адресу контроллера
	*@param $cam номер видеокамеры
	*/
	public static function getGateFromBoxIp ($ip) // 
	{
	$sql='select hlp.id from hl_param hlp
	where hlp.box_ip=\''.$ip.'\'';
	return( DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('ID'));
	}
	
	
	
	
	
	/**Проверка: не повторное ли это событие?
	* 4.05.2025
	*cvs иногда присылала повторно событие
	*@param $id_event номер события
	*@return bool true - событие уникальное
	*/
	public static function isEventUniq ($id_event) // 
	{
		return true;
	}
	
	
	
}