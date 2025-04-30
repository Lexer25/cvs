<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	//'fb' => Arr::get(Arr::get(Arr::get(Kohana::$config->load('skud'),'skud_list'), Session::instance()->get('skud_number')), 'fb_connection'),
	// в Калибре выбор базы данных фиксирован.
	'fb2' => array(
		'type'			=> 'pdo',
		'connection'	=> array(
			//'dsn'		=> 'firebird:dbname=26.98.93.81:C:\Program Files (x86)\CardSoft\DuoSE\Access\ShieldPro_rest.gdb',
			//'dsn'		=> 'firebird:dbname=192.168.5.10:C:\Program Files (x86)\CardSoft\DuoSE\Access\ShieldPro_rest.gdb',
			'dsn'		=> 'firebird:dbname=192.168.1.5:C:\Program Files (x86)\CardSoft\DuoSE\Access\ShieldPro_rest.gdb',
			'username'	=> 'SYSDBA',
			'password'	=> 'temp',
			'charset'   => 'windows-1251',
			)
		),
		'fb' => array(
				'type'			=> 'pdo',
				'connection'	=> array(
					'dsn'		=> 'odbc:SDUO',
					'charset'   => 'windows-1251',
					)
				),
	
);

