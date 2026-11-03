<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Протокол обмена с контроллером MPT (Artonit) и табло.
 *
 * Формат кадра запроса:  [LEN][CMD][PARAM...][BCC]
 * Формат кадра ответа:   [LEN][CMD][CMD][RESULT][DATA...][BCC]
 *
 *  LEN    - полная длина кадра в байтах, включая LEN и BCC
 *  CMD    - код команды (0x4F opendoor, 0x46 text, ...)
 *  RESULT - 0 - успех, 1..4 - коды ошибок (см. RESULT_*)
 *  DATA   - полезная нагрузка (текст для табло, состояние портов и т.п.)
 *  BCC    - XOR всех байт кадра, кроме самого BCC
 *
 * Второй байт CMD в ответе (индекс 1) клиентским кодом не проверяется,
 * но присутствует в реальных ответах MPT, поэтому эмулируется для совместимости.
 *
 * @package    mptemu
 * @category   Protocol
 */
class Emulator_Protocol
{
	const CMD_GET_VERSION   = 0x56;
	const CMD_OPEN_DOOR     = 0x4F;
	const CMD_OPEN_DOOR_ALL = 0x10;
	const CMD_TEXT          = 0x46;
	const CMD_CLEAR_TABLO   = 0x44;
	const CMD_SCROLL_TEXT   = 0x4A;
	const CMD_PORT_STATE    = 0x53;

	const RESULT_OK           = 0;
	const RESULT_BCC_ERROR    = 1;
	const RESULT_LENGTH_ERROR = 2;
	const RESULT_UNKNOWN_CMD  = 3;
	const RESULT_DEVICE_ERROR = 4;

	/**
	 * Коды команд => имена, как они заданы в phpMPTtcp::make_binary_command().
	 *
	 * @return array
	 */
	public static function commands()
	{
		return array(
			self::CMD_GET_VERSION   => 'GetVersion',
			self::CMD_OPEN_DOOR     => 'opendoor',
			self::CMD_OPEN_DOOR_ALL => 'opendoor_all',
			self::CMD_TEXT          => 'text',
			self::CMD_CLEAR_TABLO   => 'clearTablo',
			self::CMD_SCROLL_TEXT   => 'scrolText',
			self::CMD_PORT_STATE    => 'getPortState',
		);
	}

	/**
	 * Человекочитаемое имя команды.
	 *
	 * @param  int $cmd
	 * @return string
	 */
	public static function name($cmd)
	{
		$commands = self::commands();

		if (isset($commands[$cmd]))
			return $commands[$cmd];

		return 'unknown(0x'.strtoupper(dechex($cmd)).')';
	}

	/**
	 * Текст ошибки по коду результата.
	 *
	 * @param  int $result
	 * @return string
	 */
	public static function resultText($result)
	{
		$texts = array(
			self::RESULT_OK           => 'OK',
			self::RESULT_BCC_ERROR    => 'контрольная сумма',
			self::RESULT_LENGTH_ERROR => 'длина пакета',
			self::RESULT_UNKNOWN_CMD  => 'неизвестная команда',
			self::RESULT_DEVICE_ERROR => 'ошибка оборудования',
		);

		if (isset($texts[$result]))
			return $texts[$result];

		return 'код '.$result;
	}

	/**
	 * Контрольная сумма кадра: XOR всех байт, кроме последнего (самого BCC).
	 *
	 * @param  string $frame кадр, у которого последний байт - BCC
	 * @return int
	 */
	public static function bcc($frame)
	{
		$bcc = 0;
		$length = strlen($frame) - 1;

		for ($i = 0; $i < $length; $i++)
		{
			$bcc ^= ord($frame[$i]);
		}

		return $bcc;
	}

	/**
	 * Сборка кадра запроса. Нужна для тестов и утилит, сам эмулятор её не использует.
	 *
	 * @param  int    $cmd
	 * @param  string $params
	 * @return string
	 */
	public static function request($cmd, $params = '')
	{
		$frame = chr($cmd).$params;
		$frame = chr(strlen($frame) + 2).$frame;

		return $frame.chr(self::bcc($frame));
	}

	/**
	 * Сборка кадра ответа.
	 *
	 * @param  int    $cmd
	 * @param  int    $result  код результата
	 * @param  string $payload полезная нагрузка
	 * @return string
	 */
	public static function response($cmd, $result = self::RESULT_OK, $payload = '')
	{
		// LEN + CMD + CMD + RESULT = 4 служебных байта, плюс сам LEN и BCC
		$max_payload = 255 - 5;

		if (strlen($payload) > $max_payload)
		{
			$payload = substr($payload, 0, $max_payload);
		}

		$body  = chr($cmd).chr($cmd).chr($result & 0xFF).$payload;
		$frame = chr(strlen($body) + 2).$body;

		return $frame.chr(self::bcc($frame));
	}

	/**
	 * Разбор кадра запроса.
	 *
	 * @param  string $frame
	 * @return array  ok, error, length, declared, cmd, params
	 */
	public static function parse($frame)
	{
		$actual = strlen($frame);

		$result = array(
			'ok'       => FALSE,
			'error'    => NULL,
			'length'   => $actual,
			'declared' => NULL,
			'cmd'      => NULL,
			'params'   => '',
		);

		if ($actual < 1)
		{
			$result['error'] = 'пустой кадр';
			return $result;
		}

		$declared = ord($frame[0]);
		$result['declared'] = $declared;

		if ($declared < 3)
		{
			$result['error'] = 'длина кадра '.$declared.' меньше минимальной (3)';
			return $result;
		}

		if ($declared !== $actual)
		{
			$result['error'] = 'длина в кадре '.$declared.' не совпадает с фактической '.$actual;
			$result['cmd']   = ($actual > 1) ? ord($frame[1]) : NULL;
			return $result;
		}

		$result['cmd']    = ord($frame[1]);
		$result['params'] = substr($frame, 2, $actual - 3);

		$received = ord($frame[$actual - 1]);
		$expected = self::bcc($frame);

		if ($received !== $expected)
		{
			$result['error'] = sprintf('BCC: получено 0x%02X, расчетное 0x%02X', $received, $expected);
			return $result;
		}

		$result['ok'] = TRUE;

		return $result;
	}

	/**
	 * Байты кадра в hex для логов и панели.
	 *
	 * @param  string $frame
	 * @return string
	 */
	public static function hex($frame)
	{
		if ($frame === NULL OR $frame === '')
			return '';

		return strtoupper(implode(' ', str_split(bin2hex($frame), 2)));
	}
}
