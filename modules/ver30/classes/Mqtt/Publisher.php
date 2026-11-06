<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Публикация команд ворот и табло в MQTT.
 *
 * Топики группируются по воротам:
 *   cvs/gate/<id_gate>/controller
 *   cvs/gate/<id_gate>/tablo
 *
 * Сообщение - JSON с полями gate, cmd и параметрами команды.
 * Соединение открывается лениво и переиспользуется в пределах одного
 * запроса; при недоступном брокере публикация отключается на down_ttl
 * секунд, чтобы не тормозить обработку проезда.
 *
 * @package    ver30
 * @category   Mqtt
 */
class Mqtt_Publisher
{
	/**
	 * @var Mqtt_Publisher
	 */
	protected static $instance;

	/**
	 * @var Config_Group
	 */
	protected $config;

	protected $client;
	protected $connected = FALSE;
	protected $blocked_until = 0;

	/**
	 * @var string  текст последней ошибки подключения или публикации
	 */
	protected $error = '';

	/**
	 * @return Mqtt_Publisher
	 */
	public static function instance()
	{
		if (self::$instance === NULL)
		{
			self::$instance = new Mqtt_Publisher();
		}

		return self::$instance;
	}

	public function __construct()
	{
		$this->config = Kohana::$config->load('mqtt');
	}

	public function __destruct()
	{
		$this->disconnect();
	}

	/**
	 * Команда ворот.
	 *
	 * @param  phpCVS $cvs
	 * @param  string $cmd
	 * @param  array  $data
	 * @return bool
	 */
	public function gate(phpCVS $cvs, $cmd, array $data = array())
	{
		return $this->publish(
			$this->topic($cvs->id_gate, 'controller'),
			array_merge(array(
				'gate'    => (int) $cvs->id_gate,
				'channel' => (int) $cvs->ch,
				'box'     => $cvs->box_ip,
				'cmd'     => $cmd,
			), $data)
		);
	}

	/**
	 * Команда табло.
	 *
	 * @param  phpCVS $cvs
	 * @param  string $cmd
	 * @param  string $coordinate бинарная координата
	 * @param  string $text
	 * @param  array  $data
	 * @return bool
	 */
	public function tablo(phpCVS $cvs, $cmd, $coordinate = NULL, $text = NULL, array $data = array())
	{
		$payload = array_merge(array(
			'gate'  => (int) $cvs->id_gate,
			'tablo' => $cvs->tablo_ip,
			'cmd'   => $cmd,
		), $data);

		if ($coordinate !== NULL)
		{
			$payload['coord'] = strtoupper(bin2hex($coordinate));
		}

		if ($text !== NULL)
		{
			$payload['text'] = $this->to_utf8($text);
		}

		return $this->publish($this->topic($cvs->id_gate, 'tablo'), $payload);
	}

	/**
	 * Топик команды конкретного устройства ворот.
	 *
	 * @param  int    $gate
	 * @param  string $device controller|tablo
	 * @return string
	 */
	public function topic($gate, $device)
	{
		return $this->prefixed('gate/'.(int) $gate.'/'.$device);
	}

	/**
	 * Топик с префиксом из настроек.
	 *
	 * @param  string $suffix
	 * @return string
	 */
	public function prefixed($suffix)
	{
		$prefix = trim((string) $this->config->get('prefix', 'cvs'), '/');

		return ($prefix === '' ? '' : $prefix.'/').$suffix;
	}

	/**
	 * Проверка настроек MQTT: доступность брокера, подключение и пробная публикация.
	 *
	 * Этапы проверяются по очереди, при ошибке на любом из них возвращается
	 * описание проблемы - это позволяет отличить "брокер не запущен" от
	 * "брокер не принял логин/пароль" и от "публикация отключена в настройках".
	 *
	 * @param  string $topic  топик для тестового сообщения
	 * @param  array  $payload
	 * @return array  ok, enabled, host, port, topic, payload, probe, connected, published, error, message
	 */
	public function test($topic = NULL, array $payload = array())
	{
		$host = (string) $this->config->get('host', '127.0.0.1');
		$port = (int) $this->config->get('port', 1883);

		if ($topic === NULL)
		{
			$topic = $this->prefixed('dashboard/test');
		}

		$payload = array_merge(array(
			'gate'   => 0,
			'cmd'    => 'test',
			'source' => 'dashboard',
		), $payload);
		$payload['ts'] = date('Y-m-d H:i:s');

		$result = array(
			'ok'        => FALSE,
			'enabled'   => (bool) $this->config->get('enabled', TRUE),
			'host'      => $host,
			'port'      => $port,
			'qos'       => (int) $this->config->get('qos', 0),
			'retain'    => (bool) $this->config->get('retain', FALSE),
			'topic'     => $topic,
			'payload'   => $payload,
			'probe'     => FALSE,
			'connected' => FALSE,
			'published' => FALSE,
			'error'     => '',
			'message'   => '',
		);

		// 1. TCP-доступность брокера
		$probe = $this->probe();

		if ($probe !== TRUE)
		{
			$reason = is_string($probe) ? $probe : $host.':'.$port.' недоступен';

			$result['error']   = 'брокер '.$reason;
			$result['message'] = 'Брокер '.$reason;

			Log::instance()->add(Log::NOTICE, 'mqtt test: '.$result['message']);

			return $result;
		}

		$result['probe'] = TRUE;

		// 2. подключение (CONNECT с логином и паролем)
		try
		{
			$connected = $this->connect();
		}
		catch (Exception $e)
		{
			$connected = FALSE;
			$this->error = 'ошибка протокола MQTT: '.$e->getMessage();
		}

		if ( ! $connected)
		{
			$result['error']   = ($this->error !== '') ? $this->error : 'не удалось подключиться к брокеру';
			$result['message'] = 'Подключение не установлено: '.$result['error'];

			Log::instance()->add(Log::NOTICE, 'mqtt test: '.$result['message']);

			return $result;
		}

		$result['connected'] = TRUE;

		// 3. публикация тестового сообщения (только если публикация включена)
		if ( ! $result['enabled'])
		{
			$result['ok']      = TRUE;
			$result['message'] = 'Брокер доступен, подключение установлено. Публикация отключена (enabled = false), тестовое сообщение не отправлено.';
		}
		else
		{
			$json = json_encode($payload, JSON_UNESCAPED_UNICODE);

			if ($json === FALSE)
			{
				$result['error']   = 'не удалось сериализовать тестовое сообщение';
				$result['message'] = $result['error'];
			}
			else
			{
				try
				{
					$this->client->publish(
						$topic,
						$json,
						$result['qos'],
						$result['retain']
					);

					$result['published'] = TRUE;
					$result['ok']        = TRUE;
					$result['message']   = 'Тестовое сообщение отправлено в топик '.$topic
						.' (QoS '.$result['qos'].', retain '.($result['retain'] ? 'true' : 'false').').';
				}
				catch (Exception $e)
				{
					$this->disconnect();

					$result['error']   = 'ошибка публикации: '.$e->getMessage();
					$result['message'] = 'Публикация не выполнена: '.$result['error'];
				}
			}
		}

		if ($result['message'] === '')
		{
			$result['message'] = $result['error'];
		}

		Log::instance()->add(Log::NOTICE, 'mqtt test: '.$result['message']);

		return $result;
	}

	/**
	 * Публикация JSON-сообщения.
	 *
	 * @param  string $topic
	 * @param  array  $payload
	 * @return bool
	 */
	public function publish($topic, array $payload)
	{
		if ( ! $this->config->get('enabled', TRUE))
		{
			$this->error = 'публикация в MQTT отключена (enabled = false)';

			return FALSE;
		}

		if (microtime(TRUE) < $this->blocked_until)
			return FALSE;

		if ( ! $this->connect())
			return FALSE;

		$payload['ts'] = date('Y-m-d H:i:s');

		$json = json_encode($payload, JSON_UNESCAPED_UNICODE);

		if ($json === FALSE)
		{
			$this->error = 'не удалось сериализовать сообщение для '.$topic;
			Log::instance()->add(Log::NOTICE, 'mqtt: '.$this->error);

			return FALSE;
		}

		try
		{
			$this->client->publish(
				$topic,
				$json,
				(int) $this->config->get('qos', 0),
				(bool) $this->config->get('retain', FALSE)
			);
		}
		catch (Exception $e)
		{
			$this->error         = 'ошибка публикации '.$topic.': '.$e->getMessage();
			$this->connected     = FALSE;
			$this->client        = NULL;
			$this->blocked_until = microtime(TRUE) + (float) $this->config->get('down_ttl', 5);

			Log::instance()->add(Log::NOTICE, 'mqtt: '.$this->error);

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Ленивое подключение к брокеру с быстрым предварительным тестом.
	 *
	 * @return bool
	 */
	protected function connect()
	{
		if ($this->connected)
			return TRUE;

		$host    = (string) $this->config->get('host', '127.0.0.1');
		$port    = (int) $this->config->get('port', 1883);
		$timeout = (float) $this->config->get('connect_timeout', 1);

		$probe = $this->probe($host, $port, $timeout);

		if ($probe !== TRUE)
		{
			$this->error         = 'брокер '.$probe;
			$this->blocked_until = microtime(TRUE) + (float) $this->config->get('down_ttl', 5);
			Log::instance()->add(Log::NOTICE, 'mqtt: '.$this->error);

			return FALSE;
		}

		$this->client = new phpMQTT($host, $port, $this->client_id());

		if ( ! @$this->client->connect(TRUE, NULL, $this->config->get('username'), $this->config->get('password')))
		{
			$this->error         = 'не удалось подключиться к '.$host.':'.$port;
			$this->client        = NULL;
			$this->blocked_until = microtime(TRUE) + (float) $this->config->get('down_ttl', 5);
			Log::instance()->add(Log::NOTICE, 'mqtt: '.$this->error);

			return FALSE;
		}

		$this->connected     = TRUE;
		$this->blocked_until = 0;
		$this->error         = '';

		return TRUE;
	}

	/**
	 * Предварительная проверка TCP-доступности брокера.
	 *
	 * @param  string $host
	 * @param  int    $port
	 * @param  float  $timeout
	 * @return TRUE|string  TRUE при успехе, иначе текст ошибки
	 */
	protected function probe($host = NULL, $port = NULL, $timeout = NULL)
	{
		if ($host === NULL)
		{
			$host = (string) $this->config->get('host', '127.0.0.1');
		}

		if ($port === NULL)
		{
			$port = (int) $this->config->get('port', 1883);
		}

		if ($timeout === NULL)
		{
			$timeout = (float) $this->config->get('connect_timeout', 1);
		}

		$socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

		if ($socket === FALSE)
		{
			$reason = $host.':'.$port.' недоступен';

			if ($errstr)
			{
				$reason .= ' ('.$this->clean_error($errstr).')';
			}

			return $reason;
		}

		fclose($socket);

		return TRUE;
	}

	/**
	 * Закрытие соединения.
	 *
	 * @return void
	 */
	public function disconnect()
	{
		if ( ! $this->connected OR ! $this->client)
			return;

		try
		{
			$this->client->close();
		}
		catch (Exception $e)
		{
			// соединение уже закрыто - молча игнорируем
		}

		$this->client    = NULL;
		$this->connected = FALSE;
	}

	/**
	 * Уникальный client id: брокер закрывает предыдущую сессию с тем же идентом.
	 *
	 * @return string
	 */
	protected function client_id()
	{
		$base = (string) $this->config->get('client_id', 'cvs-ver30');

		return $base.'-'.substr(md5(uniqid('', TRUE)), 0, 8);
	}

	/**
	 * Текст табло приходит из БД СКУД в windows-1251.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function to_utf8($string)
	{
		$string = (string) $string;

		if ($string === '' OR preg_match('//u', $string))
			return $string;

		$converted = @iconv('windows-1251', 'UTF-8//IGNORE', $string);

		return ($converted === FALSE) ? $string : $converted;
	}

	/**
	 * Текст ошибки для показа на странице: UTF-8, без переводов строк, не длиннее $length.
	 *
	 * Сообщения системных вызовов Windows (например, от fsockopen) приходят
	 * в кодировке ANSI, поэтому перед выводом их нужно привести к UTF-8 -
	 * иначе json_encode не сможет сериализовать ответ.
	 *
	 * @param  string  $text
	 * @param  int     $length
	 * @return string
	 */
	protected function clean_error($text, $length = 200)
	{
		$text = trim(preg_replace('/\s+/u', ' ', $this->to_utf8((string) $text)));

		if (function_exists('mb_strlen') AND mb_strlen($text, 'UTF-8') > $length)
		{
			$text = mb_substr($text, 0, $length, 'UTF-8').'…';
		}

		return $text;
	}
}
