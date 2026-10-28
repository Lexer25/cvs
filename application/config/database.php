<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'fb' => array(
		'type'			=> 'pdo',
		'connection'	=> array(
		//'dsn'		=> 'odbc:SDUO',
		//'dsn'		=> 'odbc:ParkResident',
		'dsn'		=> 'odbc:HL',
		'charset'   => 'windows-1251',
		)
	),
		
	
	'parkresident' => array(
		'type'			=> 'pdo',
		'connection'	=> array(
		'dsn'		=> 'odbc:PARKRESIDENT',
		'charset'   => 'windows-1251',
		)
	),
		
	
	'pr' => array(// сокращение от passofficeconfig
		'type'       => 'pdo',
		'connection' => array(
       		'dsn'        => 'sqlite:'.APPPATH .'\\Config\\parkresident.sqlite',
			'persistent' => FALSE,
		)),
		
		
	
);

