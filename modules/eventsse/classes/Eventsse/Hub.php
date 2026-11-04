<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Мост «состояние mptemu -> поток Server-Sent Events».
 *
 * Эмулятор остаётся один - mptemu. Этот класс читает его файл состояния и
 * превращает изменения в события, которые браузер получает через EventSource.
 * Страница при этом ничего не опрашивает: нет ни перезагрузки по таймеру,
 * ни периодических XMLHttpRequest.
 *
 * События потока:
 *
 *   snapshot  полный снимок состояния (первое событие, а также после
 *             переподключения браузера)
 *   meta      изменились верхние поля: pid, started_at, alive, version
 *   device    изменился один комплект; payload.device === NULL - комплект
 *             удалён (сброс)
 *   command   эмулятор принял новую команду; payload.records - массив записей
 *   bye       сервер сам закрывает поток (max_seconds), браузер переподключится
 *   fatal     поток не может работать (например, не включён модуль mptemu)
 *
 * Каждое событие, кроме snapshot, получает id: (порядковый номер в рамках
 * соединения). Браузер помнит Last-Event-ID, но повтор событий сервер не
 * делает: после переподключения всегда приходит свежий snapshot.
 *
 * @package    eventsse
 * @category   SSE
 */
class Eventsse_Hub
{
	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * @var Emulator_State
	 */
	protected $store;

	/**
	 * @var float|NULL
	 */
	protected $alive_at;

	/**
	 * @var bool|NULL
	 */
	protected $alive_value;

	/**
	 * @param  array $options переопределение настроек (используется тестами)
	 * @return Eventsse_Hub
	 */
	public static function factory(array $options = array())
	{
		return new Eventsse_Hub($options);
	}

	/**
	 * @param array $options
	 */
	public function __construct(array $options = array())
	{
		$config = Kohana::$config->load('eventsse');

		$this->options = array(
			'state_file'    => $config->get('state_file'),
			'poll_ms'       => max(50, (int) $config->get('poll_ms', 250)),
			'heartbeat_s'   => max(1, (int) $config->get('heartbeat_s', 10)),
			'retry_ms'      => max(500, (int) $config->get('retry_ms', 2000)),
			'max_seconds'   => max(0, (int) $config->get('max_seconds', 300)),
			'replay'        => max(0, (int) $config->get('replay', 20)),
			'feed_limit'    => max(1, (int) $config->get('feed_limit', 100)),
			'alive_check'   => (bool) $config->get('alive_check', TRUE),
			'alive_every_s' => max(1, (int) $config->get('alive_every_s', 10)),
		);

		foreach ($options as $key => $value)
		{
			$this->options[$key] = $value;
		}

		if (empty($this->options['state_file']))
		{
			$mptemu = Kohana::$config->load('mptemu');
			$file   = $mptemu->get('state_file');

			$this->options['state_file'] = $file
				? $file
				: APPPATH.'cache/mptemu/state.json';
		}
	}

	/**
	 * Настройки, с которыми работает мост.
	 *
	 * @return array
	 */
	public function options()
	{
		return $this->options;
	}

	/**
	 * Файл состояния, который читает мост.
	 *
	 * @return string
	 */
	public function stateFile()
	{
		return $this->options['state_file'];
	}

	/**
	 * Доступен ли источник состояния - включён ли модуль mptemu.
	 *
	 * @return bool
	 */
	public function available()
	{
		return class_exists('Emulator_State');
	}

	/**
	 * Хранилище состояния mptemu.
	 *
	 * @return Emulator_State
	 */
	public function state()
	{
		return $this->store();
	}

	/**
	 * @return Emulator_State
	 * @throws RuntimeException если модуль mptemu не включён
	 */
	protected function store()
	{
		if ($this->store !== NULL)
			return $this->store;

		if ( ! $this->available())
		{
			throw new RuntimeException(
				'Не найден класс Emulator_State: включите модуль mptemu в Kohana::modules '
				.'(eventsse - только транспорт поверх него).'
			);
		}

		$mptemu = Kohana::$config->load('mptemu');

		if ($this->options['state_file'] === $mptemu->get('state_file'))
		{
			$this->store = Emulator_State::instance();
		}
		else
		{
			// Отладочный режим: читаем другой файл тем же кодом нормализации.
			$this->store = new Emulator_State($this->options['state_file']);
		}

		return $this->store;
	}

	/**
	 * Текущее состояние эмулятора (уже приведённое к схеме mptemu).
	 *
	 * @return array
	 */
	public function read()
	{
		return $this->store()->read();
	}

	/**
	 * Имя комплекта для человека.
	 *
	 * @param  string $host
	 * @return string
	 */
	public function alias($host)
	{
		return $this->store()->alias($host);
	}

	/**
	 * Полный снимок для первого события потока.
	 *
	 * @param  bool $with_feed включать историю команд
	 * @return array meta, devices, [feed]
	 */
	public function snapshot($with_feed = TRUE)
	{
		$state   = $this->read();
		$devices = $this->devices($state);

		$snapshot = array(
			'meta'    => $this->meta($state),
			'devices' => $devices,
		);

		if ($with_feed)
		{
			$snapshot['feed'] = $this->feed($devices);
		}

		return $snapshot;
	}

	/**
	 * Комплекты из состояния с добавленным псевдонимом.
	 *
	 * @param  array $state
	 * @return array
	 */
	public function devices(array $state)
	{
		$devices = array();

		foreach ((array) Arr::get($state, 'devices', array()) as $host => $device)
		{
			$device['alias'] = $this->alias($host);
			$devices[$host] = $device;
		}

		return $devices;
	}

	/**
	 * Верхние поля состояния.
	 *
	 * @param  array $state
	 * @return array
	 */
	public function meta(array $state)
	{
		return array(
			'ts'          => date('Y-m-d H:i:s'),
			'source'      => str_replace('\\', '/', $this->options['state_file']),
			'version'     => Arr::get($state, 'version'),
			'started_at'  => Arr::get($state, 'started_at'),
			'pid'         => Arr::get($state, 'pid'),
			'alive'       => $this->alive(Arr::get($state, 'pid')),
			'devices'     => count((array) Arr::get($state, 'devices', array())),
			'poll_ms'     => $this->options['poll_ms'],
			'heartbeat_s' => $this->options['heartbeat_s'],
			'feed_limit'  => $this->options['feed_limit'],
		);
	}

	/**
	 * Жив ли процесс эмулятора. Результат кешируется на alive_every_s секунд:
	 * tasklist стоит десятки миллисекунд, а вызывается на каждой итерации.
	 *
	 * @param  int $pid
	 * @return bool|NULL NULL - проверить нельзя
	 */
	public function alive($pid)
	{
		if ( ! $this->options['alive_check'] OR ! $pid)
			return NULL;

		$now = microtime(TRUE);

		if ($this->alive_at !== NULL AND ($now - $this->alive_at) < $this->options['alive_every_s'])
			return $this->alive_value;

		$this->alive_at    = $now;
		$this->alive_value = $this->processAlive((int) $pid);

		return $this->alive_value;
	}

	/**
	 * @param  int $pid
	 * @return bool|NULL
	 */
	protected function processAlive($pid)
	{
		if ($pid <= 0 OR ! function_exists('exec'))
			return NULL;

		$out = array();
		@exec('tasklist /FI "PID eq '.$pid.'" /NH 2>NUL', $out);

		return (strpos(implode('', $out), (string) $pid) !== FALSE);
	}

	/**
	 * История команд для начального снимка: последние replay записей каждого
	 * комплекта. Время в записях - только H:i:s, поэтому порядок между
	 * комплектами приблизительный; живые события приходят в точном порядке.
	 *
	 * @param  array $devices
	 * @return array
	 */
	public function feed(array $devices)
	{
		$records = array();

		foreach ($devices as $host => $device)
		{
			$commands = (array) Arr::get($device, 'commands', array());

			foreach (array_slice($commands, -$this->options['replay']) as $command)
			{
				$command['host']  = $host;
				$command['alias'] = $device['alias'];
				$records[] = $command;
			}
		}

		usort($records, function ($a, $b)
		{
			$ta = (string) Arr::get($a, 'ts', '');
			$tb = (string) Arr::get($b, 'ts', '');

			if ($ta === $tb)
				return 0;

			// Новые записи - сверху ленты.
			return ($ta < $tb) ? 1 : -1;
		});

		return array_slice($records, 0, $this->options['feed_limit']);
	}

	/**
	 * Сравнение двух снимков. Пустой массив - ничего не изменилось.
	 *
	 * @param  array $prev
	 * @param  array $curr
	 * @return array список array('event' => ..., 'data' => ...)
	 */
	public function diff(array $prev, array $curr)
	{
		$events = array();

		$meta_prev = (array) Arr::get($prev, 'meta', array());
		$meta_curr = (array) Arr::get($curr, 'meta', array());

		foreach (array('alive', 'started_at', 'pid', 'version') as $key)
		{
			if (Arr::get($meta_prev, $key) !== Arr::get($meta_curr, $key))
			{
				$events[] = array('event' => 'meta', 'data' => $meta_curr);
				break;
			}
		}

		$before = (array) Arr::get($prev, 'devices', array());
		$after  = (array) Arr::get($curr, 'devices', array());

		foreach ($after as $host => $device)
		{
			if ( ! isset($before[$host]))
			{
				$events[] = array('event' => 'device', 'data' => array(
					'host'    => $host,
					'alias'   => $device['alias'],
					'device'  => $device,
					'changes' => array('add'),
				));

				continue;
			}

			$changes = array();

			foreach (array('mode', 'doors', 'display', 'ports', 'stats', 'last_seen', 'first_seen') as $key)
			{
				if (json_encode(Arr::get($before[$host], $key)) !== json_encode(Arr::get($device, $key)))
				{
					$changes[] = $key;
				}
			}

			if ( ! empty($changes))
			{
				$events[] = array('event' => 'device', 'data' => array(
					'host'    => $host,
					'alias'   => $device['alias'],
					'device'  => $device,
					'changes' => $changes,
				));
			}

			// stats.commands растёт монотонно на каждую принятую команду,
			// поэтому по разнице счётчиков видно, сколько записей новых -
			// даже когда история уже обрезана max_commands.
			$total_before = (int) Arr::get((array) Arr::get($before[$host], 'stats', array()), 'commands', 0);
			$total_after  = (int) Arr::get((array) Arr::get($device, 'stats', array()), 'commands', 0);
			$new          = $total_after - $total_before;

			if ($new > 0)
			{
				$records = array_slice((array) Arr::get($device, 'commands', array()), -$new);

				$events[] = array('event' => 'command', 'data' => array(
					'host'    => $host,
					'alias'   => $device['alias'],
					'records' => $records,
				));
			}
		}

		foreach ($before as $host => $device)
		{
			if ( ! isset($after[$host]))
			{
				$events[] = array('event' => 'device', 'data' => array(
					'host'    => $host,
					'alias'   => Arr::get($device, 'alias'),
					'device'  => NULL,
					'changes' => array('remove'),
				));
			}
		}

		return $events;
	}

	/**
	 * Основной цикл: отправляет snapshot и затем только изменения.
	 *
	 * @return void
	 */
	public function stream()
	{
		$this->prepareOutput();

		$retry     = (int) $this->options['retry_ms'];
		$heartbeat = (int) $this->options['heartbeat_s'];
		$poll_us   = ((int) $this->options['poll_ms']) * 1000;
		$limit     = (int) $this->options['max_seconds'];

		echo 'retry: '.$retry."\n\n";
		@flush();

		try
		{
			$snapshot = $this->snapshot(TRUE);
		}
		catch (Exception $e)
		{
			$this->send('fatal', array('message' => $e->getMessage()));
			return;
		}

		$this->send('snapshot', $snapshot);

		$prev      = $snapshot;
		$id        = 0;
		$started   = microtime(TRUE);
		$last_beat = microtime(TRUE);

		while ( ! connection_aborted())
		{
			if ($limit > 0 AND (microtime(TRUE) - $started) >= $limit)
			{
				// Не обрываем соединение молча: браузер сам переподключится
				// через retry и получит свежий снимок.
				$this->send('bye', array(
					'reason'   => 'max_seconds',
					'retry_ms' => $retry,
				));

				return;
			}

			try
			{
				$curr = $this->snapshot(FALSE);
			}
			catch (Exception $e)
			{
				$this->send('fatal', array('message' => $e->getMessage()));

				return;
			}

			$events = $this->diff($prev, $curr);

			foreach ($events as $event)
			{
				$id++;

				$this->send($event['event'], $event['data'], $id);
			}

			if ( ! empty($events))
			{
				$prev = $curr;
			}

			$now = microtime(TRUE);

			if (($now - $last_beat) >= $heartbeat)
			{
				// Комментарий SSE: браузер его игнорирует, прокси видят трафик.
				echo ': hb '.time()."\n\n";
				@flush();

				$last_beat = $now;
			}

			usleep($poll_us);
		}
	}

	/**
	 * Заголовки и отключение буферизации. Kohana стартует свой ob_start()
	 * в Kohana::init(), поэтому его нужно закрыть до первого echo.
	 *
	 * @return void
	 */
	protected function prepareOutput()
	{
		while (ob_get_level() > 0)
		{
			@ob_end_flush();
		}

		@set_time_limit(0);
		@ini_set('zlib.output_compression', '0');
		@ini_set('output_buffering', '0');
		@ini_set('implicit_flush', '1');

		header('Content-Type: text/event-stream; charset=utf-8');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');
		header('X-Accel-Buffering: no');
		header('Connection: keep-alive');
	}

	/**
	 * Кадр SSE.
	 *
	 * @param  string   $event
	 * @param  mixed    $data
	 * @param  int|NULL $id
	 * @return void
	 */
	protected function send($event, $data, $id = NULL)
	{
		if ($id !== NULL)
		{
			echo 'id: '.$id."\n";
		}

		echo 'event: '.$event."\n";
		echo 'data: '.$this->json($data)."\n\n";

		@flush();
	}

	/**
	 * json_encode с запасным вариантом для битой кодировки в тексте табло.
	 *
	 * @param  mixed $data
	 * @return string
	 */
	public function json($data)
	{
		$json = json_encode($data, JSON_UNESCAPED_UNICODE);

		if ($json === FALSE)
		{
			$json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
		}

		return ($json === FALSE) ? '{}' : $json;
	}
}
