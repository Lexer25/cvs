<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Панель и поток событий эмулятора mptemu через Server-Sent Events.
 *
 * /eventsse              - живая панель (обновляется потоком, без опроса)
 * /eventsse/stream       - поток text/event-stream (EventSource)
 * /eventsse/state        - снимок состояния в JSON (?feed=1 - с историей)
 * /eventsse/mode         - POST: режим имитации (device, delay_ms, ...)
 * /eventsse/clear        - POST: очистить табло комплекта (device)
 * /eventsse/reset        - POST: сбросить один комплект (device)
 * /eventsse/resetall     - POST: сбросить все комплекты
 *
 * Эмулятора здесь нет: устройства эмулирует mptemu, а модуль читает его
 * состояние и раздаёт изменения потоком. Поэтому в bootstrap.php должны быть
 * включены оба модуля, mptemu - раньше eventsse.
 *
 * @package    eventsse
 * @category   Controller
 */
class Controller_Eventsse extends Controller
{
	/**
	 * Живая панель.
	 *
	 * @return void
	 */
	public function action_index()
	{
		$hub       = Eventsse_Hub::factory();
		$available = $hub->available();

		$snapshot = array(
			'meta'    => array('ts' => date('Y-m-d H:i:s'), 'devices' => 0),
			'devices' => array(),
			'feed'    => array(),
		);

		if ($available)
		{
			try
			{
				$snapshot = $hub->snapshot(TRUE);
			}
			catch (Exception $e)
			{
				// Панель откроется с сообщением ниже; поток отдаст fatal.
			}
		}

		$config = Kohana::$config->load('eventsse');

		$view = View::factory('eventsse/panel', array(
			'title'       => $config->get('title', 'Живая панель событий'),
			'available'   => $available,
			'snapshot'    => $snapshot,
			'feed_limit'  => (int) $config->get('feed_limit', 100),
			'urls'        => array(
				'stream'   => URL::site('eventsse/stream'),
				'state'    => URL::site('eventsse/state'),
				'mode'     => URL::site('eventsse/mode'),
				'clear'    => URL::site('eventsse/clear'),
				'reset'    => URL::site('eventsse/reset'),
				'resetall' => URL::site('eventsse/resetall'),
			),
		));

		$this->response
			->headers('Content-Type', 'text/html; charset=utf-8')
			->body($view->render());
	}

	/**
	 * Поток SSE. Управление уходит в Eventsse_Hub::stream(), который сам
	 * отдаёт заголовки и работает до отключения браузера или max_seconds.
	 *
	 * @return void
	 */
	public function action_stream()
	{
		Eventsse_Hub::factory()->stream();

		exit;
	}

	/**
	 * Снимок состояния в JSON - для отладки и внешних клиентов.
	 *
	 * @return void
	 */
	public function action_state()
	{
		$hub = Eventsse_Hub::factory();

		if ( ! $hub->available())
		{
			$this->json(array('ok' => FALSE, 'error' => $this->missing()), 503);
			return;
		}

		try
		{
			$snapshot = $hub->snapshot((bool) $this->request->query('feed'));
		}
		catch (Exception $e)
		{
			$this->json(array('ok' => FALSE, 'error' => $e->getMessage()), 500);
			return;
		}

		$this->json($snapshot);
	}

	/**
	 * Режим имитации комплекта.
	 *
	 * @return void
	 */
	public function action_mode()
	{
		if ( ! $this->post_only())
			return;

		$payload = $this->payload();
		$device  = trim((string) Arr::get($payload, 'device', ''));

		if ($device === '')
		{
			$this->json(array('ok' => FALSE, 'error' => 'Не указан комплект (device)'), 400);
			return;
		}

		$store = $this->store();

		if ($store === NULL)
			return;

		$store->setMode($device, array(
			'delay_ms'        => Arr::get($payload, 'delay_ms', 0),
			'result_code'     => Arr::get($payload, 'result_code', 0),
			'silent'          => Arr::get($payload, 'silent', FALSE),
			'bad_bcc'         => Arr::get($payload, 'bad_bcc', FALSE),
			'drop_connection' => Arr::get($payload, 'drop_connection', FALSE),
		));

		$this->device_response($store, $device);
	}

	/**
	 * Очистка табло комплекта.
	 *
	 * @return void
	 */
	public function action_clear()
	{
		if ( ! $this->post_only())
			return;

		$device = trim((string) Arr::get($this->payload(), 'device', ''));

		if ($device === '')
		{
			$this->json(array('ok' => FALSE, 'error' => 'Не указан комплект (device)'), 400);
			return;
		}

		$store = $this->store();

		if ($store === NULL)
			return;

		$store->clearDisplay($device);

		$this->device_response($store, $device);
	}

	/**
	 * Сброс одного комплекта.
	 *
	 * @return void
	 */
	public function action_reset()
	{
		if ( ! $this->post_only())
			return;

		$device = trim((string) Arr::get($this->payload(), 'device', ''));

		if ($device === '')
		{
			$this->json(array('ok' => FALSE, 'error' => 'Не указан комплект (device)'), 400);
			return;
		}

		$store = $this->store();

		if ($store === NULL)
			return;

		$store->resetDevice($device);

		$this->json(array('ok' => TRUE, 'removed' => $device));
	}

	/**
	 * Сброс всех комплектов.
	 *
	 * @return void
	 */
	public function action_resetall()
	{
		if ( ! $this->post_only())
			return;

		$store = $this->store();

		if ($store === NULL)
			return;

		$store->reset();

		$this->json(array('ok' => TRUE, 'removed' => '*'));
	}

	/**
	 * Текст ошибки «нет mptemu».
	 *
	 * @return string
	 */
	protected function missing()
	{
		return 'Модуль mptemu не включён: eventsse работает только поверх него. '
			.'Добавьте в application/bootstrap.php строку '
			."'mptemu' => MODPATH.'mptemu',";
	}

	/**
	 * Хранилище состояния или NULL (тогда ответ уже отправлен).
	 *
	 * @return Emulator_State|NULL
	 */
	protected function store()
	{
		$hub = Eventsse_Hub::factory();

		if ( ! $hub->available())
		{
			$this->json(array('ok' => FALSE, 'error' => $this->missing()), 503);
			return NULL;
		}

		return $hub->state();
	}

	/**
	 * Ответ с обновлённым комплектом: панель применяет его сразу, не ожидая
	 * следующего события потока.
	 *
	 * @param  Emulator_State $store
	 * @param  string         $host
	 * @return void
	 */
	protected function device_response($store, $host)
	{
		$state = $store->read();
		$device = $store->device($state, $host);

		$device['alias'] = $store->alias($host);

		$this->json(array('ok' => TRUE, 'device' => $device));
	}

	/**
	 * Только POST или готовый ответ 405.
	 *
	 * @return bool
	 */
	protected function post_only()
	{
		if ($this->request->method() === Request::POST)
			return TRUE;

		$this->json(array('ok' => FALSE, 'error' => 'Только POST'), 405);

		return FALSE;
	}

	/**
	 * Параметры запроса: JSON-тело или обычная форма.
	 *
	 * @return array
	 */
	protected function payload()
	{
		$body = $this->request->body();
		$data = json_decode((string) $body, TRUE);

		if (is_array($data))
			return $data;

		$post = $this->request->post();

		return is_array($post) ? $post : array();
	}

	/**
	 * JSON-ответ.
	 *
	 * @param  array $data
	 * @param  int   $status
	 * @return void
	 */
	protected function json(array $data, $status = 200)
	{
		$this->response
			->status($status)
			->headers('Content-Type', 'application/json; charset=utf-8')
			->headers('Cache-Control', 'no-store')
			->body(json_encode($data, JSON_UNESCAPED_UNICODE));
	}
}
