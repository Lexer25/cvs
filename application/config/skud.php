<?php defined('SYSPATH') or die('No direct script access.');
 
return array(
	'skud_list'=>array(
	'1' =>	array(
			'name'=>'Калибр',
			'fb_connection' => array(
				'type'			=> 'pdo',
				'connection'	=> array(
					'dsn'		=> 'firebird:dbname=localhost:C:\\Program Files (x86)\\Cardsoft\\DuoSE\\Access\\SHIELDPRO_REST.GDB',
					'username'	=> 'SYSDBA',
					'password'	=> 'temp',
					'charset'   => 'windows-1251',
					)
				),
			),
		)
	
);