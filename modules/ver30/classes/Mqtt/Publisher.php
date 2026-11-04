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
		$prefix = trim((string) $this->config->get('prefix', 'cvs'), '/');

		return ($prefix === '' ? '' : $prefix.'/').'gate/'.(int) $gate.'/'.$device;
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
			return FALSE;

		if (microtime(TRUE) < $this->blocked_until)
			return FALSE;

		if ( ! $this->connect())
			return FALSE;

		$payload['ts'] = date('Y-m-d H:i:s');

		$json = json_encode($payload, JSON_UNESCAPED_UNICODE);

		if ($json === FALSE)
		{
			Log::instance()->add(Log::NOTICE, 'mqtt: не удалось сериализовать сообщение для '.$topic);

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
			$this->connected   = FALSE;
			$this->client      = NULL;
			$this->blocked_until = microtime(TRUE) + (float) $this->config->get('down_ttl', 5);

			Log::instance()->add(Log::NOTICE, 'mqtt: ошибка публикации '.$topic.': '.$e->getMessage());

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

		$probe = @fsockopen($host, $port, $errno, $errstr, $timeout);

		if ($probe === FALSE)
		{
			$this->blocked_until = microtime(TRUE) + (float) $this->config->get('down_ttl', 5);
			Log::instance()->add(Log::NOTICE, 'mqtt: брокер '.$host.':'.$port.' недоступен'.($errstr ? ' ('.$errstr.')' : ''));

			return FALSE;
		}

		fclose($probe);

		$this->client = new phpMQTT($host, $port, $this->client_id());

		if ( ! @$this->client->connect(TRUE, NULL, $this->config->get('username'), $this->config->get('password')))
		{
			$this->client        = NULL;
			$this->blocked_until = microtime(TRUE) + (float) $this->config->get('down_ttl', 5);
			Log::instance()->add(Log::NOTICE, 'mqtt: не удалось подключиться к '.$host.':'.$port);

			return FALSE;
		}

		$this->connected = TRUE;

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
}
