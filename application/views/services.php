<?
if(!is_null($serverList))
{
	//echo Debug::vars('4', $serverList);
	$curl= Kohana::$config->load('artonitcity_config')->curl_place;//'C:\xampp\curl.exe -L';
	echo __('serverList').'<br>';


	echo '<br>';
	echo 'rem сбор статистическихх данных по контроллерам (версия, состояние связи, количество карт по каналу 0, количество карт по каналау 1. Данные записываются в таблицу ST_DATA.'.'<br>';
	echo HTML::anchor('/task/delete_stat_data', $curl.' http://'.Arr::get($_SERVER, 'HTTP_HOST', '127.0.0.1').'/city/task/delete_stat_data').'<br>';
	foreach($serverList as $key=>$value)
	{
		echo HTML::anchor(
				'task/stat_device/'.Arr::get($value, 'ID_SERVER'),
				$curl.' http://'.Arr::get($_SERVER, 'HTTP_HOST', '127.0.0.1').'/city/task/stat_device/'.Arr::get($value, 'ID_SERVER'))
				.'<br>';
	}
	
	echo '<br>';
	echo 'rem Выявление режима ТЕСТ.'.'<br>';
	foreach($serverList as $key=>$value)
	{
		echo HTML::anchor('task/stat_device/1', $curl.' http://'.Arr::get($_SERVER, 'HTTP_HOST', '127.0.0.1').'/city/task/detectTestModeAllDevice/'.Arr::get($value, 'ID_SERVER')).'<br>';
	}
	
	echo '<br>';
	echo 'rem Запись количества карт по точкам прохода в момент опроса. Данные берутся из базы данных и записываются в таблицу ST_DATA.'.'<br>';
	echo HTML::anchor('task/stat_device/1', $curl.' http://'.Arr::get($_SERVER, 'HTTP_HOST', '127.0.0.1').'/city/task/fixKeyOnDBCount').'<br>';

	echo '<br>';
	echo 'rem Вычитка карт из указанной точки прохода. В файл записываются номера карт из базы данных и номера карты, вычитанные из контроллера. Вместо '.__('ID_DEV').' необходимо указать ID точки прохода (двери).<br>';
	echo HTML::anchor(
		'task/stat_device/1',
		$curl.' http://'.Arr::get($_SERVER, 'HTTP_HOST', '127.0.0.1').'/city/task/readkey_once/['.__('ID_DEV').']'
		).'<br>';
		
	echo '<br>';
	echo 'rem Запуск задачи minion.'.'<br>';
	echo HTML::anchor('task/stat_device/1', $curl.' http://'.Arr::get($_SERVER, 'HTTP_HOST', '127.0.0.1').'/city/task/test').'<br>';
}


