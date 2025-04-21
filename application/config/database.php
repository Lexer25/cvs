<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	
	'fb' => array(
		'type'			=> 'pdo',
		'connection'	=> array(
			'dsn'		=> 'firebird:dbname=localhost:C:\\Temp3\\222.GDB',
			'username'	=> 'SYSDBA',
			'password'	=> 'temp',
			//'password'	=> 'masterkey',
			'charset'   => 'windows-1251',
			)
		),
	
);

