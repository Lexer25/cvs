##Настройка Артонит CVS.  
###Настройка связи с базой данных СКУД.  
Настройка пути к базе данных хранится в файле \cvs\application\config\database.php раздел fb:  
~~~
'fb' => array(
		'type'			=> 'pdo',
		'connection'	=> array(
		'dsn'		=> 'odbc:SDUO',
		'charset'   => 'windows-1251',
		)
	),
~~~
Менять настройки без понимания смысла не рекомендую.  

###Настройка режимов работы.  
Параметры режимов работы хранятся в файле \cvs\application\config\config.php:  
~~~
	'testMode'=>true,//в этом режиме проезд разрешен по любому идентификатору 
	'reverseGate'=>array(3,4),//если gate находится в этом списке, то щелкнут оба реле
	
~~~
###Настройка оборудования
Настройка оборудования производится в панели [ParkResident/КПП](parkresident/index.php/gate/list) 
 