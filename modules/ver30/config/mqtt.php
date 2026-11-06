<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Настройки дублирования команд в MQTT.
 *
 * Команды ворот и табло публикуются в топики, сгруппированные по воротам:
 *
 *   <prefix>/gate/<id_gate>/controller   - команды открытия (opendoor)
 *   <prefix>/gate/<id_gate>/tablo        - команды табло (clearTablo, text, scrolText)
 *
 * @package    ver30
 */
return array(
	'enabled'         => false,
	'host'            => '127.0.0.1',   // localhost:1883 (IPv4, чтобы не уйти на ::1)
	'port'            => 1883,
	'username'        => NULL,
	'password'        => NULL,
	'client_id'       => 'cvs-ver30',
	'prefix'          => 'cvs',
	'qos'             => 0,
	'retain'          => FALSE,
	'connect_timeout' => 1,             // сек, быстрый предварительный тест брокера
	'down_ttl'        => 5,             // сек, пауза после неудачного подключения
);
