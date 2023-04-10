<?php defined('SYSPATH') or die('No direct script access.');
 
return array(
    'dir_log' => 'C:\Program Files (x86)\Cardsoft\DuoSE\Access\Log',
    'dir_compare' => 'C:\xampp\htdocs\city',
    'stat_day_befor' => 2,
    'name_device_fro_test' => 'л251 к45 калитка в л254',
	'city_name' => Arr::get(Arr::get(Arr::get(Kohana::$config->load('skud'),'skud_list'), Session::instance()->get('skud_number')), 'name'), //'Балчуг Вьюпоинт',
	'ver'=>'1.2.7',//добавлена аналитика и подсказки
	'developer'=>'www.artonit.ru',
		'main_windows'=>array(
				'windows1'=>true, // true окно №1  Информация по жильцам и картам
				'windows2'=>false, // true окно №2 Оборудование
				'windows3'=>false, // true окно №3 Очередь загрузок
				'windows4'=>false, // falseокно №4 События
				'windows5'=>false, // false окно №5 Изменения системы
				'windows6'=>false, // true окно №6 Статистика событий
				
				
		),
	'count_day_befor_end_time' =>30,
	'analit_ok'=>array(507, 509, 650, 651, 652, 653, 654, 655, 656, 480), //Список кодов аналитики, которые следует рассматривать как правильную работу системы 
	'analit_err'=>array(500, 501, 502, 503, 504, 505, 506, 508, 657, 658, 46, 481), // Список кодов аналитики, которые следует рассматривать как нарушение правильной работы системы.
	'analit_transit'=>array(5001, 5011, 5021, 5031, 5041, 5051, 5061), // 14.03.2020 Список кодов аналитики, которые следует рассматривать как переходные процессы: карта уже поставлена на удаление, но еще не удалена из контроллера.
	//30.01.2020 Определение условий доступа к пунктам верхнего меню. Значение false - без авторизации не показывать, значение true - показывать меню всегда
	'view_without_auth'=>array(
		'load'=>false,
		'load_order'=>false,
		'device_control'=>false,
		'events'=>true,
		'people'=>false,
		'door'=>false,
		'log'=>true,
		'check'=>false,
		),
	'curl_place'=>'C:\xampp\curl.exe -L',
	
);