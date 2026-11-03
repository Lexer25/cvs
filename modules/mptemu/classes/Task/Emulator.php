<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Эмулятор внешнего контроллера ворот и табло.
 *
 * Запуск:
 *   C:\xampp\php\php.exe index.php --task=emulator
 *   C:\xampp\php\php.exe index.php --task=emulator --port=8001
 *   C:\xampp\php\php.exe index.php --task=emulator --reset=1
 *
 * Остановка: Ctrl+C.
 * Панель наблюдения: http://127.0.0.1:8080/cvs/mptemu
 *
 * @package    mptemu
 * @category   Task
 */
class Task_Emulator extends Minion_Task
{
	protected $_options = array(
		'host'  => '0.0.0.0',
		'port'  => '8000',
		'reset' => '0',
	);

	protected function _execute(array $params)
	{
		if (Arr::get($params, 'reset') === '1')
		{
			Emulator_State::instance()->reset();
			Minion_CLI::write('Состояние эмулятора сброшено');
		}

		$server = new Emulator_Server(
			Arr::get($params, 'host'),
			(int) Arr::get($params, 'port')
		);

		Minion_CLI::write('Эмулятор ворот и табло. Панель: '.URL::site('mptemu', NULL, FALSE));
		Minion_CLI::write('Остановка: Ctrl+C');

		try
		{
			$server->run();
		}
		catch (Exception $e)
		{
			Minion_CLI::write('Ошибка: '.$e->getMessage());
		}
	}
}
