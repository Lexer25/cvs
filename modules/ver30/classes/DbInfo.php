<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Параметры подключения к базе данных.
 *
 * Класс собирает сведения о подключении: имя DSN, адрес сервера, путь к файлу
 * базы данных и состояние подключения. Основные данные берутся из конфигурации
 * Kohana (группа database), а для источников ODBC (dsn вида "odbc:ИМЯ") -
 * из системного источника данных Windows (реестр ODBC).
 *
 * @package    CVS
 * @category   Database
 * @author     Artonit
 * @copyright  (c) 2025 Artonit Team
 * @license    http://artonit/ru
 */
class DbInfo {

	// группа конфигурации базы данных по умолчанию
	const DEFAULT_GROUP = 'fb';

	// время ожидания подключения, секунд
	const TIMEOUT = 3;

	// таблица настроек, наличие которой проверяется в базе данных
	const SETTING_TABLE = 'HL_SETTING';

	// кэш разобранных параметров подключения
	protected static $_cache = array();

	/**
	 * Получить параметры подключения к базе данных.
	 *
	 * @param   string  $group   имя группы конфигурации database
	 * @param   array   $config  конфигурация подключения (если не из Kohana)
	 * @return  array   dsn, dsn_name, scheme, driver, host, ip, path, source, error
	 */
	public static function get($group = NULL, array $config = NULL)
	{
		if ($group === NULL)
		{
			$group = self::DEFAULT_GROUP;
		}

		$cache_key = $group.($config === NULL ? '' : ':'.md5(serialize($config)));

		if (isset(self::$_cache[$cache_key]))
		{
			return self::$_cache[$cache_key];
		}

		$info = array(
			'group'    => $group,
			'dsn'      => '',
			'dsn_name' => '',
			'scheme'   => '',
			'driver'   => '',
			'host'     => '',
			'ip'       => '',
			'path'     => '',
			'source'   => '',
			'error'    => '',
		);

		if ($config === NULL)
		{
			$config = self::_config($group);
		}

		if (empty($config))
		{
			$info['error'] = 'В конфигурации нет группы "'.$group.'"';
			return self::$_cache[$cache_key] = $info;
		}

		$connection = Arr::get($config, 'connection', array());
		$dsn = trim((string) Arr::get($connection, 'dsn', ''));
		$info['dsn'] = $dsn;

		if ($dsn === '')
		{
			$info['error'] = 'В конфигурации не задана строка DSN';
			return self::$_cache[$cache_key] = $info;
		}

		// DSN состоит из схемы и остальной части: "odbc:HL", "sqlite:C:\...\db.sqlite"
		$rest = $dsn;

		if (preg_match('/^([a-z0-9_]+):(.*)$/is', $dsn, $m))
		{
			$info['scheme'] = strtolower($m[1]);
			$rest = trim($m[2]);
		}

		switch ($info['scheme'])
		{
			case 'odbc':
				if (strpos($rest, '=') === FALSE AND strpos($rest, ';') === FALSE)
				{
					// "odbc:HL" - имя источника данных, параметры лежат в реестре
					$info['dsn_name'] = $rest;
					$registered = self::_read_odbc_dsn($rest);

					if (is_array($registered))
					{
						$info['source'] = 'registry';
						$info['driver'] = (string) self::_value($registered, array('Driver'));
						$info['host'] = (string) self::_value($registered, array('Server'));
						$info['path'] = (string) self::_value($registered, array('Database', 'Dbname'));
					}
					else
					{
						$info['error'] = 'Не удалось прочитать параметры DSN "'.$rest.'" из реестра ODBC';
					}
				}
				else
				{
					// DSN задан строкой: "odbc:Driver={...};Server=...;Database=..."
					$params = self::_parse_dsn_params($rest);

					$info['source'] = 'dsn';
					$info['dsn_name'] = (string) self::_value($params, array('DSN'));
					$info['driver'] = (string) self::_value($params, array('Driver'));
					$info['host'] = (string) self::_value($params, array('Server'));
					$info['path'] = (string) self::_value($params, array('Database', 'Dbname'));
				}
			break;

			case 'sqlite':
				$info['source'] = 'dsn';
				$info['path'] = $rest;
			break;

			case 'mysql':
			case 'pgsql':
			case 'sqlsrv':
				$params = self::_parse_dsn_params($rest);

				$info['source'] = 'dsn';
				$info['host'] = (string) self::_value($params, array('host'));
				$info['path'] = (string) self::_value($params, array('dbname', 'database'));
			break;

			default:
				$info['error'] = 'Неизвестный тип подключения: '.$info['scheme'];
		}

		self::_normalize($info);

		return self::$_cache[$cache_key] = $info;
	}

	/**
	 * Проверить состояние подключения к базе данных.
	 *
	 * @param   string  $group   имя группы конфигурации database
	 * @param   array   $config  конфигурация подключения (если не из Kohana)
	 * @return  array   ok, ms, error, warning, table
	 */
	public static function check($group = NULL, array $config = NULL)
	{
		if ($group === NULL)
		{
			$group = self::DEFAULT_GROUP;
		}

		$result = array(
			'ok'      => FALSE,
			'ms'      => 0,
			'error'   => '',
			'warning' => '',
			'table'   => array(
				'name'  => self::SETTING_TABLE,
				'ok'    => FALSE,
				'error' => '',
			),
		);

		if ($config === NULL)
		{
			$config = self::_config($group);
		}

		if (empty($config))
		{
			$result['error'] = 'В конфигурации нет группы "'.$group.'"';
			return $result;
		}

		$connection = Arr::get($config, 'connection', array());
		$dsn = trim((string) Arr::get($connection, 'dsn', ''));

		if ($dsn === '')
		{
			$result['error'] = 'В конфигурации не задана строка DSN';
			return $result;
		}

		$options = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		);

		// для всех драйверов, кроме SQLite, ограничиваем время ожидания подключения
		if (stripos($dsn, 'sqlite:') !== 0)
		{
			$options[PDO::ATTR_TIMEOUT] = (int) self::TIMEOUT;
		}

		$start = microtime(TRUE);

		try
		{
			$pdo = new PDO(
				$dsn,
				Arr::get($connection, 'username', NULL),
				Arr::get($connection, 'password', NULL),
				$options
			);

			$result['ms'] = (int) round((microtime(TRUE) - $start) * 1000);

			// подключение установлено, отдельно проверяем выполнение запроса
			try
			{
				$pdo->query(self::_test_sql($dsn))->fetchAll();
			}
			catch (Exception $e)
			{
				$result['warning'] = self::_utf8($e->getMessage());
			}

			// проверяем наличие таблицы настроек HL_SETTING
			try
			{
				$pdo->query('SELECT COUNT(*) FROM '.self::SETTING_TABLE)->fetchAll();
				$result['table']['ok'] = TRUE;
			}
			catch (Exception $e)
			{
				$result['table']['error'] = self::_utf8($e->getMessage());
			}

			$result['ok'] = TRUE;
		}
		catch (Exception $e)
		{
			$result['ms'] = (int) round((microtime(TRUE) - $start) * 1000);
			$result['error'] = self::_utf8($e->getMessage());
			$result['table']['error'] = 'нет подключения к базе данных';
		}

		return $result;
	}

	/**
	 * Конфигурация подключения из файлов конфигурации Kohana.
	 *
	 * @param   string  $group
	 * @return  array
	 */
	protected static function _config($group)
	{
		$config = Kohana::$config->load('database')->get($group, array());

		return is_array($config) ? $config : array();
	}

	/**
	 * Параметры DSN, заданного строкой "Server=...;Database=...".
	 *
	 * @param   string  $dsn
	 * @return  array
	 */
	protected static function _parse_dsn_params($dsn)
	{
		$params = array();

		foreach (explode(';', $dsn) as $part)
		{
			if (strpos($part, '=') === FALSE)
			{
				continue;
			}

			list($key, $value) = explode('=', $part, 2);

			$key = trim($key);
			$value = trim($value, " \t\n\r\0\x0B{}");

			if ($key !== '')
			{
				$params[$key] = $value;
			}
		}

		return $params;
	}

	/**
	 * Прочитать параметры системного источника данных ODBC из реестра Windows.
	 *
	 * @param   string  $name  имя DSN
	 * @return  array|NULL
	 */
	protected static function _read_odbc_dsn($name)
	{
		static $cache = array();

		if (array_key_exists($name, $cache))
		{
			return $cache[$name];
		}

		$data = NULL;

		// имя DSN не должно содержать ничего, кроме допустимых символов
		if (preg_match('/^[A-Za-z0-9_.\- ]+$/', $name) AND function_exists('exec'))
		{
			$keys = array(
				'HKLM\\SOFTWARE\\ODBC\\ODBC.INI\\'.$name,
				'HKCU\\SOFTWARE\\ODBC\\ODBC.INI\\'.$name,
			);

			// 32- и 64-битные представления реестра
			foreach (array('', ' /reg:64', ' /reg:32') as $view)
			{
				foreach ($keys as $key)
				{
					$output = array();
					$code = 1;

					@exec('reg query "'.$key.'"'.$view.' 2>NUL', $output, $code);

					if ($code === 0)
					{
						$data = self::_parse_reg_output($output);

						if (is_array($data))
						{
							break 2;
						}
					}
				}
			}
		}

		return $cache[$name] = $data;
	}

	/**
	 * Разобрать вывод команды "reg query".
	 *
	 * @param   array  $lines
	 * @return  array|NULL
	 */
	protected static function _parse_reg_output(array $lines)
	{
		$data = array();

		foreach ($lines as $line)
		{
			// "    Server    REG_SZ    127.0.0.1"
			if (preg_match('/^\s+(\S.*?)\s{4}(REG_[A-Z_]+)\s{4}(.*)$/', $line, $m))
			{
				$data[$m[1]] = trim($m[3]);
			}
		}

		return empty($data) ? NULL : $data;
	}

	/**
	 * Выделить адрес сервера и путь к базе данных, определить IP-адрес.
	 *
	 * @param   array  $info
	 * @return  void
	 */
	protected static function _normalize(array & $info)
	{
		$host = trim($info['host']);
		$path = trim($info['path']);

		// в параметрах DSN значения иногда записаны в кавычках
		$host = trim($host, "\"'");
		$path = trim($path, "\"'");

		// Firebird/InterBase: "сервер[:порт]:путь" или "сервер/порт:путь"
		if ($host === '' AND $path !== '' AND ! preg_match('/^[a-z]:[\\\\\/]/i', $path))
		{
			if (preg_match('#^([^:\\\\/]+)(?:(?::|/)(\d+))?:(.+)$#s', $path, $m) AND strlen($m[1]) > 1)
			{
				$host = $m[1];
				$path = $m[3];
			}
		}

		$info['host'] = $host;
		$info['path'] = $path;
		$info['ip'] = self::_resolve_ip($host);
	}

	/**
	 * Привести адрес сервера к IP-адресу (если задано имя хоста).
	 *
	 * @param   string  $host
	 * @return  string
	 */
	protected static function _resolve_ip($host)
	{
		$host = trim($host);

		if ($host === '')
		{
			return '';
		}

		if (filter_var($host, FILTER_VALIDATE_IP))
		{
			return $host;
		}

		$ip = @gethostbyname($host);

		if ($ip !== $host AND filter_var($ip, FILTER_VALIDATE_IP))
		{
			return $host.' ('.$ip.')';
		}

		return $host;
	}

	/**
	 * Значение из массива по имени ключа без учёта регистра.
	 *
	 * @param   array   $data
	 * @param   array   $names   варианты имени ключа
	 * @param   string  $default
	 * @return  string
	 */
	protected static function _value(array $data, array $names, $default = '')
	{
		foreach ($data as $key => $value)
		{
			foreach ($names as $name)
			{
				if (strcasecmp($key, $name) === 0)
				{
					return (string) $value;
				}
			}
		}

		return $default;
	}

	/**
	 * Привести текст к UTF-8: сообщения ODBC-драйверов Windows приходят
	 * в кодировке ANSI (windows-1251).
	 *
	 * @param   string  $text
	 * @return  string
	 */
	protected static function _utf8($text)
	{
		$text = (string) $text;

		if ($text === '' OR preg_match('//u', $text))
		{
			return $text;
		}

		$converted = @iconv('CP1251', 'UTF-8//IGNORE', $text);

		if ($converted === FALSE OR $converted === '')
		{
			$converted = @mb_convert_encoding($text, 'UTF-8', 'Windows-1251');
		}

		return ($converted === FALSE OR $converted === '') ? $text : $converted;
	}

	/**
	 * Простейший запрос для проверки работоспособности подключения.
	 *
	 * @param   string  $dsn
	 * @return  string
	 */
	protected static function _test_sql($dsn)
	{
		if (stripos($dsn, 'odbc:') === 0)
		{
			// Firebird/InterBase
			return 'SELECT 1 FROM RDB$DATABASE';
		}

		return 'SELECT 1';
	}

} // End DbInfo
