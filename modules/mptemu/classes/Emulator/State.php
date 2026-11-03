<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Состояние эмулятора: JSON-файл под flock.
 *
 * Один процесс эмулятора может обслуживать несколько «комплектов»
 * контроллер + табло. Комплект определяется IP-адресом назначения, на
 * который пришло подключение (см. Emulator_Server::accept()). Поэтому
 * состояние хранится как набор устройств:
 *
 *   devices => array(
 *       '127.0.0.2' => array(
 *           'host', 'first_seen', 'last_seen',
 *           'mode', 'doors', 'display', 'ports', 'commands', 'stats'
 *       ),
 *       ...
 *   )
 *
 * @package    mptemu
 * @category   State
 */
class Emulator_State
{
	const VERSION = 2;

	/**
	 * @var Emulator_State
	 */
	protected static $instance;

	protected $file;
	protected $log_file;
	protected $max_commands;

	/**
	 * @return Emulator_State
	 */
	public static function instance()
	{
		if (self::$instance === NULL)
		{
			$config = Kohana::$config->load('mptemu');

			self::$instance = new Emulator_State(
				$config->get('state_file'),
				$config->get('log_file'),
				(int) $config->get('max_commands', 200)
			);
		}

		return self::$instance;
	}

	public function __construct($file, $log_file = NULL, $max_commands = 200)
	{
		$this->file         = $file;
		$this->log_file     = $log_file;
		$this->max_commands = (int) $max_commands;

		$dir = dirname($this->file);

		if ( ! is_dir($dir))
		{
			@mkdir($dir, 0777, TRUE);
		}
	}

	/**
	 * Состояние верхнего уровня.
	 *
	 * @return array
	 */
	public function defaults()
	{
		return array(
			'version'    => self::VERSION,
			'started_at' => NULL,
			'pid'        => NULL,
			'devices'    => array(),
		);
	}

	/**
	 * Состояние одного «комплекта» контроллер + табло.
	 *
	 * @return array
	 */
	public function deviceDefaults()
	{
		return array(
			'host'       => NULL,
			'first_seen' => NULL,
			'last_seen'  => NULL,
			'mode'       => array(
				'delay_ms'        => 0,
				'result_code'     => 0,
				'silent'          => FALSE,
				'bad_bcc'         => FALSE,
				'drop_connection' => FALSE,
			),
			'doors' => array(
				'0' => array('count' => 0, 'opened_at' => NULL, 'open_until' => NULL),
				'1' => array('count' => 0, 'opened_at' => NULL, 'open_until' => NULL),
			),
			'display'  => array('last_text' => NULL, 'lines' => array()),
			'ports'    => array(0, 0),
			'commands' => array(),
			'stats'    => array('connections' => 0, 'commands' => 0, 'errors' => 0),
		);
	}

	/**
	 * Чтение состояния.
	 *
	 * @return array
	 */
	public function read()
	{
		$fp = @fopen($this->file, 'rb');

		if ($fp === FALSE)
			return $this->defaults();

		flock($fp, LOCK_SH);
		$raw = stream_get_contents($fp);
		flock($fp, LOCK_UN);
		fclose($fp);

		$data = json_decode($raw, TRUE);

		return $this->normalize(is_array($data) ? $data : array());
	}

	/**
	 * Приведение прочитанных данных к актуальной схеме.
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function normalize(array $data)
	{
		$defaults = $this->defaults();
		$result   = $defaults;

		foreach ($data as $key => $value)
		{
			$result[$key] = $value;
		}

		// Миграция с версии 1: плоское состояние считалось одним комплектом
		if (empty($result['devices']) AND isset($data['doors']))
		{
			$legacy = array('host' => '127.0.0.1');

			foreach (array('mode', 'doors', 'display', 'ports', 'commands', 'stats') as $key)
			{
				if (isset($data[$key]))
				{
					$legacy[$key] = $data[$key];
				}
			}

			$result['devices'] = array('127.0.0.1' => $legacy);
		}

		$devices = array();

		foreach ((array) $result['devices'] as $host => $device)
		{
			$devices[$host] = $this->normalizeDevice(is_array($device) ? $device : array());
			$devices[$host]['host'] = $host;
		}

		ksort($devices);

		$result['devices'] = $devices;
		$result['version'] = self::VERSION;

		return $result;
	}

	/**
	 * Приведение состояния одного комплекта к актуальной схеме.
	 *
	 * @param  array $device
	 * @return array
	 */
	protected function normalizeDevice(array $device)
	{
		$defaults = $this->deviceDefaults();
		$result   = $defaults;

		foreach ($device as $key => $value)
		{
			$result[$key] = $value;
		}

		$mode    = Arr::get($device, 'mode');
		$stats   = Arr::get($device, 'stats');
		$display = Arr::get($device, 'display');
		$doors   = Arr::get($device, 'doors');
		$ports   = Arr::get($device, 'ports');
		$log     = Arr::get($device, 'commands');

		$result['mode']    = array_merge($defaults['mode'], is_array($mode) ? $mode : array());
		$result['stats']   = array_merge($defaults['stats'], is_array($stats) ? $stats : array());
		$result['display'] = array_merge($defaults['display'], is_array($display) ? $display : array());

		// array_merge переиндексировал бы числовые ключи дверей (0,1), поэтому объединение
		$result['doors'] = is_array($doors) ? ($doors + $defaults['doors']) : $defaults['doors'];

		$result['ports']    = is_array($ports) ? $ports : $defaults['ports'];
		$result['commands'] = is_array($log) ? $log : array();

		return $result;
	}

	/**
	 * Состояние одного комплекта из общего состояния.
	 *
	 * @param  array  $state
	 * @param  string $host
	 * @return array
	 */
	public function device(array $state, $host)
	{
		if (isset($state['devices'][$host]))
			return $state['devices'][$host];

		$device = $this->deviceDefaults();
		$device['host'] = $host;

		return $device;
	}

	/**
	 * Псевдонимы комплектов из конфига: IP => человекочитаемое имя.
	 *
	 * @return array
	 */
	public function aliases()
	{
		$aliases = Kohana::$config->load('mptemu')->get('aliases', array());

		return is_array($aliases) ? $aliases : array();
	}

	/**
	 * Имя комплекта для панели и консоли.
	 *
	 * @param  string $host
	 * @return string
	 */
	public function alias($host)
	{
		$aliases = $this->aliases();

		if (isset($aliases[$host]) AND $aliases[$host] !== '')
			return $aliases[$host];

		return $host;
	}

	/**
	 * Изменение состояния под эксклюзивной блокировкой.
	 *
	 * @param  callable $mutator получает массив состояния, возвращает новый
	 * @return array|NULL
	 */
	public function modify($mutator)
	{
		$fp = @fopen($this->file, 'c+b');

		if ($fp === FALSE)
			return NULL;

		flock($fp, LOCK_EX);

		$raw  = stream_get_contents($fp);
		$data = json_decode($raw, TRUE);
		$data = $this->normalize(is_array($data) ? $data : array());

		$data = call_user_func($mutator, $data);

		if ( ! is_array($data))
		{
			$data = $this->defaults();
		}

		rewind($fp);
		ftruncate($fp, 0);
		fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
		fflush($fp);
		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}

	/**
	 * Изменение состояния одного комплекта.
	 *
	 * @param  string   $host
	 * @param  callable $mutator получает состояние комплекта, возвращает новое
	 * @return array|NULL
	 */
	public function modifyDevice($host, $mutator)
	{
		return $this->modify(function (array $state) use ($host, $mutator)
		{
			$device = isset($state['devices'][$host])
				? $state['devices'][$host]
				: $this->deviceDefaults();

			$device = call_user_func($mutator, $device);

			if ( ! is_array($device))
			{
				$device = $this->deviceDefaults();
			}

			$device['host'] = $host;
			$state['devices'][$host] = $device;

			ksort($state['devices']);

			return $state;
		});
	}

	/**
	 * Замена значений верхнего уровня.
	 *
	 * @param  array $changes
	 * @return array|NULL
	 */
	public function update(array $changes)
	{
		return $this->modify(function (array $data) use ($changes)
		{
			foreach ($changes as $key => $value)
			{
				$data[$key] = $value;
			}

			return $data;
		});
	}

	/**
	 * Отметка подключения к комплекту.
	 *
	 * @param  string $host
	 * @return void
	 */
	public function touch($host)
	{
		$this->modifyDevice($host, function (array $device)
		{
			$now = date('Y-m-d H:i:s');

			if (empty($device['first_seen']))
			{
				$device['first_seen'] = $now;
			}

			$device['last_seen'] = $now;
			$device['stats']['connections']++;

			return $device;
		});
	}

	/**
	 * Добавление записи о полученной команде.
	 *
	 * @param  string $host
	 * @param  array  $record
	 * @return void
	 */
	public function appendCommand($host, array $record)
	{
		$record += array(
			'ts'     => date('H:i:s'),
			'peer'   => NULL,
			'cmd'    => NULL,
			'name'   => NULL,
			'params' => '',
			'frame'  => '',
			'result' => NULL,
			'ms'     => NULL,
			'note'   => NULL,
			'error'  => NULL,
		);

		$this->modifyDevice($host, function (array $device) use ($record)
		{
			$device['commands'][] = $record;

			if (count($device['commands']) > $this->max_commands)
			{
				$device['commands'] = array_slice($device['commands'], -$this->max_commands);
			}

			$device['stats']['commands']++;

			if ($record['error'] !== NULL)
			{
				$device['stats']['errors']++;
			}

			return $device;
		});
	}

	/**
	 * Сохранение режима имитации одного комплекта.
	 *
	 * @param  string $host
	 * @param  array  $mode
	 * @return array|NULL
	 */
	public function setMode($host, array $mode)
	{
		return $this->modifyDevice($host, function (array $device) use ($mode)
		{
			$device['mode'] = array(
				'delay_ms'        => max(0, (int) Arr::get($mode, 'delay_ms', 0)),
				'result_code'     => max(0, min(4, (int) Arr::get($mode, 'result_code', 0))),
				'silent'          => (bool) Arr::get($mode, 'silent', FALSE),
				'bad_bcc'         => (bool) Arr::get($mode, 'bad_bcc', FALSE),
				'drop_connection' => (bool) Arr::get($mode, 'drop_connection', FALSE),
			);

			return $device;
		});
	}

	/**
	 * Очистка экрана табло комплекта.
	 *
	 * @param  string $host
	 * @return array|NULL
	 */
	public function clearDisplay($host)
	{
		return $this->modifyDevice($host, function (array $device)
		{
			$device['display'] = array('last_text' => NULL, 'lines' => array());

			return $device;
		});
	}

	/**
	 * Сброс одного комплекта.
	 *
	 * @param  string $host
	 * @return array|NULL
	 */
	public function resetDevice($host)
	{
		return $this->modify(function (array $state) use ($host)
		{
			unset($state['devices'][$host]);

			return $state;
		});
	}

	/**
	 * Полный сброс всех комплектов, с сохранением отметки о запуске процесса.
	 *
	 * @return array|NULL
	 */
	public function reset()
	{
		return $this->modify(function (array $state)
		{
			$state['devices'] = array();

			return $state;
		});
	}

	/**
	 * Текстовая строка в лог-файл (с ротацией после 5 МБ).
	 *
	 * @param  string $text
	 * @return void
	 */
	public function logLine($text)
	{
		if ( ! $this->log_file)
			return;

		$dir = dirname($this->log_file);

		if ( ! is_dir($dir))
		{
			@mkdir($dir, 0777, TRUE);
		}

		if (is_file($this->log_file) AND filesize($this->log_file) > 5242880)
		{
			@rename($this->log_file, $this->log_file.'.1');
		}

		@file_put_contents(
			$this->log_file,
			date('Y-m-d H:i:s').' '.$text."\n",
			FILE_APPEND | LOCK_EX
		);
	}
}
