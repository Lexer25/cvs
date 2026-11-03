<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * TCP-сервер эмулятора внешних контроллеров ворот и табло.
 *
 * Один процесс обслуживает произвольное число «комплектов». Комплект
 * определяется IP-адресом назначения: клиент подключается к 127.0.0.2:8000
 * или 127.0.0.3:8000, сервер видит адрес, на который пришло соединение
 * (stream_socket_get_name($conn, FALSE)), и ведёт отдельное состояние
 * для каждого такого адреса.
 *
 * Порты в hl_param не важны: phpMPTtcp::connect() принудительно ставит 8000,
 * поэтому «разные контроллеры» моделируются разными IP, а не портами.
 *
 * @package    mptemu
 * @category   Server
 */
class Emulator_Server
{
	protected $host;
	protected $port;
	protected $state;
	protected $server;
	protected $clients = array();
	protected $running = TRUE;
	protected $door_open_ms = 5000;
	protected $close_after = TRUE;

	public function __construct($host = NULL, $port = NULL)
	{
		$config = Kohana::$config->load('mptemu');

		$this->host         = ($host === NULL) ? $config->get('host', '0.0.0.0') : $host;
		$this->port         = ($port === NULL) ? (int) $config->get('port', 8000) : (int) $port;
		$this->door_open_ms = (int) $config->get('door_open_ms', 5000);
		$this->close_after  = (bool) $config->get('close_after_response', TRUE);
		$this->state        = Emulator_State::instance();
	}

	/**
	 * Остановка цикла (например, из обработчика сигнала).
	 *
	 * @return void
	 */
	public function stop()
	{
		$this->running = FALSE;
	}

	/**
	 * Основной цикл сервера.
	 *
	 * @return void
	 */
	public function run()
	{
		$errno  = 0;
		$errstr = '';

		$this->server = @stream_socket_server('tcp://'.$this->host.':'.$this->port, $errno, $errstr);

		if ($this->server === FALSE)
		{
			throw new RuntimeException('не удалось занять '.$this->host.':'.$this->port.' - '.$errstr.' ('.$errno.')');
		}

		stream_set_blocking($this->server, FALSE);

		$this->state->update(array(
			'started_at' => date('Y-m-d H:i:s'),
			'pid'        => getmypid(),
		));

		$this->say(NULL, 'слушаю tcp://'.$this->host.':'.$this->port);

		while ($this->running)
		{
			$read = array($this->server);

			foreach ($this->clients as $client)
			{
				$read[] = $client['socket'];
			}

			$write  = NULL;
			$except = NULL;

			$ready = @stream_select($read, $write, $except, 1);

			if ($ready === FALSE)
			{
				$this->say(NULL, 'ошибка stream_select');
				continue;
			}

			if ($ready === 0)
				continue;

			foreach ($read as $stream)
			{
				if ($stream === $this->server)
				{
					$this->accept();
				}
				else
				{
					$this->receive($stream);
				}
			}
		}
	}

	/**
	 * Приём нового подключения. Адрес назначения определяет комплект.
	 *
	 * @return void
	 */
	protected function accept()
	{
		$peer = '';
		$conn = @stream_socket_accept($this->server, 0, $peer);

		if ($conn === FALSE)
			return;

		stream_set_blocking($conn, FALSE);

		$local  = @stream_socket_get_name($conn, FALSE);
		$device = $this->deviceFromLocal($local);

		$this->clients[(int) $conn] = array(
			'socket' => $conn,
			'peer'   => $peer,
			'device' => $device,
			'buffer' => '',
		);

		$this->state->touch($device);

		$this->say($device, 'подключение от '.$peer);
	}

	/**
	 * IP комплекта из локального адреса соединения.
	 *
	 * @param  string $local например "127.0.0.2:8000"
	 * @return string
	 */
	protected function deviceFromLocal($local)
	{
		if ( ! $local)
			return '127.0.0.1';

		$position = strrpos($local, ':');

		if ($position === FALSE)
			return $local;

		return substr($local, 0, $position);
	}

	/**
	 * Чтение данных клиента и нарезка на кадры.
	 *
	 * @param  resource $stream
	 * @return void
	 */
	protected function receive($stream)
	{
		$id = (int) $stream;

		if ( ! isset($this->clients[$id]))
			return;

		$data = @fread($stream, 4096);

		if ($data === FALSE OR ($data === '' AND feof($stream)))
		{
			$this->disconnect($id, 'закрыто клиентом');
			return;
		}

		if ($data === '')
			return;

		$this->clients[$id]['buffer'] .= $data;

		while (isset($this->clients[$id]))
		{
			$buffer = $this->clients[$id]['buffer'];

			if (strlen($buffer) < 1)
				return;

			$length = ord($buffer[0]);

			if ($length < 3)
			{
				$this->record($this->clients[$id]['device'], array(
					'peer'  => $this->clients[$id]['peer'],
					'error' => 'длина кадра '.$length.' меньше минимальной (3)',
				));

				$this->disconnect($id, 'битый кадр');
				return;
			}

			if (strlen($buffer) < $length)
				return;

			$frame = substr($buffer, 0, $length);
			$this->clients[$id]['buffer'] = substr($buffer, $length);

			$this->handle($frame, $id);
		}
	}

	/**
	 * Обработка одного кадра.
	 *
	 * @param  string $frame
	 * @param  int    $id
	 * @return void
	 */
	protected function handle($frame, $id)
	{
		$started = microtime(TRUE);
		$peer    = $this->clients[$id]['peer'];
		$device  = $this->clients[$id]['device'];

		$state  = $this->state->read();
		$config = $this->state->device($state, $device);
		$mode   = $config['mode'];

		$parsed = Emulator_Protocol::parse($frame);

		$record = array(
			'peer'   => $peer,
			'cmd'    => ($parsed['cmd'] === NULL) ? '-' : sprintf('0x%02X', $parsed['cmd']),
			'name'   => ($parsed['cmd'] === NULL) ? '-' : Emulator_Protocol::name($parsed['cmd']),
			'params' => Emulator_Protocol::hex($parsed['params']),
			'frame'  => Emulator_Protocol::hex($frame),
			'error'  => $parsed['error'],
		);

		if ($mode['drop_connection'])
		{
			$record['note'] = 'режим drop_connection';
			$this->record($device, $record);
			$this->disconnect($id, 'drop_connection');
			return;
		}

		if ($mode['silent'])
		{
			$record['note'] = 'режим silent: ответ не отправлен';
			$this->record($device, $record);
			return;
		}

		if ($mode['delay_ms'] > 0)
		{
			usleep((int) $mode['delay_ms'] * 1000);
		}

		$forced = (int) $mode['result_code'];

		if ( ! $parsed['ok'])
		{
			$result = Emulator_Protocol::RESULT_BCC_ERROR;
			$record['note'] = 'битый кадр';
		}
		elseif ($forced !== Emulator_Protocol::RESULT_OK)
		{
			$result = $forced;
			$record['note'] = 'принудительный код '.$forced;
		}
		elseif ( ! array_key_exists($parsed['cmd'], Emulator_Protocol::commands()))
		{
			$result = Emulator_Protocol::RESULT_UNKNOWN_CMD;
			$record['note'] = 'команда неизвестна';
		}
		else
		{
			$result = Emulator_Protocol::RESULT_OK;
		}

		$payload = ($result === Emulator_Protocol::RESULT_OK)
			? $this->applyCommand($device, $parsed['cmd'], $parsed['params'])
			: '';

		$cmd      = ($parsed['cmd'] === NULL) ? 0 : $parsed['cmd'];
		$response = Emulator_Protocol::response($cmd, $result, $payload);

		if ($mode['bad_bcc'])
		{
			$response = substr($response, 0, -1).chr(ord(substr($response, -1)) ^ 0xFF);
			$record['note'] = trim(Arr::get($record, 'note').' bad_bcc');
		}

		$written = @fwrite($this->clients[$id]['socket'], $response);

		$record['result'] = $result;
		$record['ms']     = round((microtime(TRUE) - $started) * 1000, 1);

		if ($written === FALSE OR $written < strlen($response))
		{
			$record['error'] = 'ответ записан не полностью';
		}

		$this->record($device, $record);

		// Клиент (phpMPTtcp::sendCommand) читает ответ в цикле до EOF. Если не закрыть
		// соединение, он будет ждать SO_RCVTIMEO (3 с) на каждую команду.
		if ($this->close_after)
		{
			$this->disconnect($id, 'закрыто эмулятором');
		}
	}

	/**
	 * Применение команды к состоянию комплекта. Возвращает нагрузку ответа.
	 *
	 * @param  string $device
	 * @param  int    $cmd
	 * @param  string $params
	 * @return string
	 */
	protected function applyCommand($device, $cmd, $params)
	{
		switch ($cmd)
		{
			case Emulator_Protocol::CMD_OPEN_DOOR:
				$this->openDoor($device, strlen($params) > 0 ? ord($params[0]) : 0);
				return '';

			case Emulator_Protocol::CMD_OPEN_DOOR_ALL:
				$this->openDoor($device, 0);
				$this->openDoor($device, 1);
				return '';

			case Emulator_Protocol::CMD_CLEAR_TABLO:
				$this->state->clearDisplay($device);
				return '';

			case Emulator_Protocol::CMD_TEXT:
				$this->storeLine($device, $params, 3, FALSE);
				return '';

			case Emulator_Protocol::CMD_SCROLL_TEXT:
				$this->storeLine($device, $params, 4, TRUE);
				return '';

			case Emulator_Protocol::CMD_PORT_STATE:
				$state  = $this->state->read();
				$config = $this->state->device($state, $device);
				$ports  = $config['ports'];

				return chr((int) Arr::get($ports, 0, 0)).chr((int) Arr::get($ports, 1, 0));

			case Emulator_Protocol::CMD_GET_VERSION:
				return 'MPT-EMU 1.0';
		}

		return '';
	}

	/**
	 * Отметка открытия реле.
	 *
	 * @param  string $device
	 * @param  int    $door
	 * @return void
	 */
	protected function openDoor($device, $door)
	{
		$door = ((int) $door === 1) ? '1' : '0';
		$now  = time();
		$till = $now + (int) ($this->door_open_ms / 1000);

		$this->state->modifyDevice($device, function (array $config) use ($door, $now, $till)
		{
			if ( ! isset($config['doors'][$door]))
			{
				$config['doors'][$door] = array('count' => 0, 'opened_at' => NULL, 'open_until' => NULL);
			}

			$config['doors'][$door]['count']++;
			$config['doors'][$door]['opened_at']  = date('H:i:s', $now);
			$config['doors'][$door]['open_until'] = date('H:i:s', $till);

			return $config;
		});

		$this->say($device, 'OPEN DOOR '.$door);
	}

	/**
	 * Запоминание строки, выведенной на табло.
	 *
	 * @param  string $device
	 * @param  string $params       координата + текст
	 * @param  int    $coord_length длина координаты в байтах
	 * @param  bool   $scrolling    это бегущая строка
	 * @return void
	 */
	protected function storeLine($device, $params, $coord_length, $scrolling)
	{
		$coordinate = substr($params, 0, $coord_length);
		$text       = substr($params, $coord_length);

		// Табло подключено к той же системе, что и БД СКУД: текст приходит в windows-1251
		$decoded = @iconv('windows-1251', 'UTF-8//IGNORE', $text);

		if ($decoded !== FALSE)
		{
			$text = $decoded;
		}

		$key = strtoupper(bin2hex($coordinate));
		$now = date('H:i:s');

		$this->state->modifyDevice($device, function (array $config) use ($key, $text, $coordinate, $scrolling, $now)
		{
			$config['display']['lines'][$key] = array(
				'coord'     => strtoupper(bin2hex($coordinate)),
				'text'      => $text,
				'scrolling' => $scrolling,
				'ts'        => $now,
			);

			$config['display']['last_text'] = $text;

			return $config;
		});

		$this->say($device, 'TABLO '.$key.' «'.$text.'»');
	}

	/**
	 * Сохранение записи о кадре в состояние и текстовый лог.
	 *
	 * @param  string $device
	 * @param  array  $record
	 * @return void
	 */
	protected function record($device, array $record)
	{
		$this->state->appendCommand($device, $record);

		$line = '['.$this->state->alias($device).'] '
			.Arr::get($record, 'peer').' '
			.Arr::get($record, 'name').' '
			.Arr::get($record, 'cmd')
			.' params='.Arr::get($record, 'params')
			.' result='.Arr::get($record, 'result')
			.' ms='.Arr::get($record, 'ms');

		if (Arr::get($record, 'error'))
		{
			$line .= ' ошибка: '.Arr::get($record, 'error');
		}

		if (Arr::get($record, 'note'))
		{
			$line .= ' примечание: '.Arr::get($record, 'note');
		}

		$this->state->logLine($line);
	}

	/**
	 * Закрытие соединения.
	 *
	 * @param  int    $id
	 * @param  string $reason
	 * @return void
	 */
	protected function disconnect($id, $reason = NULL)
	{
		if ( ! isset($this->clients[$id]))
			return;

		$peer   = $this->clients[$id]['peer'];
		$device = $this->clients[$id]['device'];

		@fclose($this->clients[$id]['socket']);
		unset($this->clients[$id]);

		$this->say($device, 'отключение '.$peer.($reason ? ' ('.$reason.')' : ''));
	}

	/**
	 * Вывод в консоль и в лог-файл. $device = NULL - служебное сообщение.
	 *
	 * @param  string|NULL $device
	 * @param  string      $text
	 * @return void
	 */
	protected function say($device, $text)
	{
		if ($device !== NULL)
		{
			$text = '['.$this->state->alias($device).'] '.$text;
		}

		$line = date('H:i:s').' '.$text.PHP_EOL;

		if (defined('STDOUT'))
		{
			// пишем напрямую: echo при перенаправлении вывода буферизуется
			fwrite(STDOUT, $line);
			fflush(STDOUT);
		}
		else
		{
			echo $line;
		}

		$this->state->logLine($text);
	}
}
