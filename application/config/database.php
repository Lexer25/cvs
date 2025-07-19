<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'fb' => array(
		'type'			=> 'pdo',
		'connection'	=> array(
		'dsn'		=> 'odbc:SDUO',
		//'dsn'		=> 'odbc:SDUO_2',
		'charset'   => 'windows-1251',
		)
	),
	
);

