<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Панель эмулятора: матрица «ворота по горизонтали, показатели по вертикали».
 *
 * /mptemu              - матрица всех комплектов
 * /mptemu?live=0       - без автообновления
 * /mptemu/state        - состояние в JSON (можно ?device=127.0.0.2)
 * /mptemu/mode         - POST: режим имитации (device, delay_ms, result_code, ...)
 * /mptemu/clear        - POST: очистить табло комплекта (device)
 * /mptemu/reset        - POST: сбросить один комплект (device)
 * /mptemu/resetall     - POST: сбросить все комплекты
 *
 * @package    mptemu
 * @category   Controller
 */
class Controller_Mptemu extends Controller
{
	public function action_index()
	{
		$state = Emulator_State::instance()->read();
		$alive = $this->is_alive($state);

		$this->response
			->headers('Content-Type', 'text/html; charset=utf-8')
			->body($this->render($state, $alive));
	}

	public function action_state()
	{
		$state = Emulator_State::instance()->read();
		$state['alive'] = $this->is_alive($state);

		$device = (string) $this->request->query('device');

		if ($device !== '')
		{
			$state = array(
				'alive'  => $state['alive'],
				'device' => $device,
				'state'  => Emulator_State::instance()->device($state, $device),
			);
		}

		$this->response
			->headers('Content-Type', 'application/json; charset=utf-8')
			->body(json_encode($state, JSON_UNESCAPED_UNICODE));
	}

	public function action_mode()
	{
		if ( ! $this->is_post())
			return;

		$post   = $this->request->post();
		$device = (string) Arr::get($post, 'device', '');

		if ($device === '')
		{
			$this->response->status(400)->body('Не указан комплект (device)');
			return;
		}

		Emulator_State::instance()->setMode($device, array(
			'delay_ms'        => Arr::get($post, 'delay_ms', 0),
			'result_code'     => Arr::get($post, 'result_code', 0),
			'silent'          => Arr::get($post, 'silent', FALSE),
			'bad_bcc'         => Arr::get($post, 'bad_bcc', FALSE),
			'drop_connection' => Arr::get($post, 'drop_connection', FALSE),
		));

		$this->redirect_back();
	}

	public function action_clear()
	{
		if ( ! $this->is_post())
			return;

		$device = (string) $this->request->post('device');

		if ($device === '')
		{
			$this->response->status(400)->body('Не указан комплект (device)');
			return;
		}

		Emulator_State::instance()->clearDisplay($device);
		$this->redirect_back();
	}

	public function action_reset()
	{
		if ( ! $this->is_post())
			return;

		$device = (string) $this->request->post('device');

		if ($device === '')
		{
			$this->response->status(400)->body('Не указан комплект (device)');
			return;
		}

		Emulator_State::instance()->resetDevice($device);
		$this->redirect_back();
	}

	public function action_resetall()
	{
		if ( ! $this->is_post())
			return;

		Emulator_State::instance()->reset();
		$this->redirect_back();
	}

	/**
	 * POST или ответ 405.
	 *
	 * @return bool
	 */
	protected function is_post()
	{
		if ($this->request->method() === Request::POST)
			return TRUE;

		$this->response->status(405)->body('Только POST');

		return FALSE;
	}

	/**
	 * Возврат на панель после POST.
	 *
	 * @return void
	 */
	protected function redirect_back()
	{
		$this->response
			->status(303)
			->headers('Location', URL::site('mptemu'));
	}

	/**
	 * Запущен ли процесс эмулятора.
	 *
	 * @param  array $state
	 * @return bool|NULL
	 */
	protected function is_alive(array $state)
	{
		$pid = (int) Arr::get($state, 'pid');

		if ($pid <= 0 OR ! function_exists('exec'))
			return NULL;

		$out = array();
		@exec('tasklist /FI "PID eq '.$pid.'" /NH 2>NUL', $out);

		return (strpos(implode('', $out), (string) $pid) !== FALSE);
	}

	/**
	 * Сортировка комплектов по IP, а не по алфавиту.
	 *
	 * @param  array $devices
	 * @return array
	 */
	protected function sort_devices(array $devices)
	{
		uksort($devices, function ($a, $b)
		{
			$ia = ip2long($a);
			$ib = ip2long($b);

			if ($ia === FALSE OR $ib === FALSE)
				return strcmp($a, $b);

			return ($ia < $ib) ? -1 : (($ia > $ib) ? 1 : 0);
		});

		return $devices;
	}

	/**
	 * Последняя запись из истории команд комплекта.
	 *
	 * @param  array $device
	 * @return array|NULL
	 */
	protected function last_command(array $device)
	{
		if (empty($device['commands']))
			return NULL;

		$commands = $device['commands'];

		return end($commands);
	}

	/**
	 * Человекочитаемый режим имитации.
	 *
	 * @param  array $mode
	 * @return string
	 */
	protected function mode_text(array $mode)
	{
		$parts = array();

		if ((int) $mode['delay_ms'] > 0)
			$parts[] = 'задержка '.$mode['delay_ms'].' мс';

		if ((int) $mode['result_code'] > 0)
			$parts[] = 'код '.$mode['result_code'];

		if ($mode['silent'])
			$parts[] = 'молчит';

		if ($mode['bad_bcc'])
			$parts[] = 'битый BCC';

		if ($mode['drop_connection'])
			$parts[] = 'обрыв';

		return empty($parts) ? 'норма' : implode(', ', $parts);
	}

	/**
	 * Две строки табло. Первый байт координаты: 0x00 - верхняя, 0x08 - нижняя.
	 *
	 * @param  array $device
	 * @return array 1 => строка|NULL, 2 => строка|NULL
	 */
	protected function display_lines(array $device)
	{
		$result = array(1 => NULL, 2 => NULL);

		foreach ($device['display']['lines'] as $line)
		{
			$coord = strtoupper(preg_replace('/[^0-9A-F]/i', '', (string) $line['coord']));

			if ($coord === '')
				continue;

			$first = hexdec(substr($coord, 0, 2));
			$row   = ($first === 0) ? 1 : 2;

			$result[$row] = $line;
		}

		return $result;
	}

	/**
	 * «5 с назад», «3 мин назад».
	 *
	 * @param  string $datetime
	 * @return string
	 */
	protected function age($datetime)
	{
		if ( ! $datetime)
			return '—';

		$ts = strtotime($datetime);

		if ($ts === FALSE)
			return HTML::chars((string) $datetime);

		$diff = time() - $ts;

		if ($diff < 0)
			return 'только что';

		if ($diff < 60)
			return $diff.' с назад';

		if ($diff < 3600)
			return floor($diff / 60).' мин назад';

		if ($diff < 86400)
			return floor($diff / 3600).' ч назад';

		return floor($diff / 86400).' дн назад';
	}

	/**
	 * HTML панели.
	 *
	 * @param  array     $state
	 * @param  bool|NULL $alive
	 * @return string
	 */
	protected function render(array $state, $alive)
	{
		$config = Kohana::$config->load('mptemu');
		$live   = (int) $config->get('live_refresh', 0);

		if ($this->request->query('live') === '0')
		{
			$live = 0;
		}

		if ($alive === NULL)
		{
			$alive_text = '<span class="muted">неизвестно</span>';
		}
		else
		{
			$alive_text = $alive ? '<b class="ok">работает</b>' : '<b class="err">не запущен</b>';
		}

		$html  = '<!doctype html><html lang="ru"><head><meta charset="utf-8">';
		$html .= '<title>Эмулятор ворот и табло</title>';
		$html .= '<style>'
			.'body{font:13px/1.35 Segoe UI,Arial,sans-serif;margin:10px 14px;color:#222}'
			.'h1{font-size:17px;margin:0 0 6px}'
			.'table{border-collapse:collapse;table-layout:fixed;width:100%}'
			.'.matrix{margin-top:8px}'
			.'.matrix th,.matrix td{border:1px solid #c9c9c9;padding:3px 5px;vertical-align:middle;'
			.'white-space:nowrap;overflow:hidden;text-overflow:ellipsis}'
			.'.matrix thead th{background:#e8eef7;text-align:center;font-weight:600;font-size:12px}'
			.'.matrix thead th.metric{background:#f2f2f2}'
			.'.matrix tbody th{background:#f7f7f7;text-align:left;font-weight:400;color:#555;width:150px}'
			.'.matrix td{text-align:center;font-size:12px}'
			.'.matrix td.text{text-align:left}'
			.'.matrix td.tablo{padding:2px 5px}'
			.'.matrix td.tablo div{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}'
			.'.matrix td.tablo div + div{border-top:1px dotted #ddd;color:#555}'
			.'.ip{color:#888;font-weight:400;font-size:11px}'
			.'.muted{color:#888}'
			.'.ok{color:#070;font-weight:600}'
			.'.err{color:#b00;font-weight:600}'
			.'.dim{color:#9a9a9a;background:#fbfbfb}'
			.'code{background:#f2f2f2;padding:0 3px;font-size:11px}'
			.'.ctl{margin:6px 0 0}'
			.'.ctl-item{display:flex;flex-wrap:wrap;align-items:center;gap:6px;'
			.'padding:3px 0;border-bottom:1px dotted #ddd}'
			.'.ctl-item b{min-width:190px}'
			.'.ctl-item input[type=number]{width:60px}'
			.'details{margin:8px 0}'
			.'summary{cursor:pointer;color:#036;font-weight:600}'
			.'.log td,.log th{border:1px solid #ddd;padding:2px 5px;font-size:11px;white-space:nowrap}'
			.'.log th{background:#f3f3f3}'
			.'.log{margin:4px 0 10px}'
			.'</style></head><body>';

		$html .= '<h1>Эмулятор ворот и табло</h1>';

		$html .= '<div class="muted">Слушает <code>tcp://'.HTML::chars((string) $config->get('host')).':'
			.HTML::chars((string) $config->get('port')).'</code>'
			.', запущен '.HTML::chars((string) Arr::get($state, 'started_at', '-'))
			.', PID '.HTML::chars((string) Arr::get($state, 'pid', '-'))
			.', состояние: '.$alive_text
			.', комплектов: <b>'.count($state['devices']).'</b>'
			.($live > 0
				? ' · обновление '.$live.' с (<a href="'.URL::site('mptemu').'?live=0">выключить</a>)'
				: ' · обновление выключено (<a href="'.URL::site('mptemu').'">включить</a>)')
			.'</div>';

		$html .= $this->render_matrix($state);
		$html .= $this->render_controls($state);
		$html .= $this->render_history($state);

		if ($live > 0)
		{
			// не перезагружаем страницу, пока открыт блок управления или история
			$html .= '<script>setTimeout(function(){'
				.'if(!document.querySelector("details[open]")){location.reload();}'
				.'},'.($live * 1000).');</script>';
		}

		$html .= '</body></html>';

		return $html;
	}

	/**
	 * Матрица: столбцы - комплекты, строки - показатели.
	 *
	 * @param  array $state
	 * @return string
	 */
	protected function render_matrix(array $state)
	{
		$devices = $this->sort_devices($state['devices']);

		if (empty($devices))
		{
			return '<p class="muted">Комплектов пока нет: эмулятор ещё не принимал подключений. '
				.'Пропишите в hl_param <code>box_ip</code>/<code>tablo_ip</code> на адрес этого хоста '
				.'(например <code>127.0.0.2</code>) и отправьте событие.</p>';
		}

		$html = '<table class="matrix"><thead><tr><th class="metric">Показатель</th>';

		foreach ($devices as $host => $device)
		{
			$age_ts = strtotime((string) Arr::get($device, 'last_seen', ''));
			$stale  = ($age_ts !== FALSE AND (time() - $age_ts) > 600) ? ' dim' : '';

			$html .= '<th class="'.$stale.'">'.HTML::chars(Emulator_State::instance()->alias($host))
				.'<br><span class="ip">'.HTML::chars($host).'</span></th>';
		}

		$html .= '</tr></thead><tbody>';

		$html .= '<tr><th>Последняя команда</th>';

		foreach ($devices as $host => $device)
		{
			$command = $this->last_command($device);

			if ($command === NULL)
			{
				$html .= '<td class="muted">—</td>';
				continue;
			}

			$params = trim((string) $command['params']);
			$title  = $command['name'].' '.$command['cmd'].($params !== '' ? ' [' . $params . ']' : '');

			$html .= '<td title="'.HTML::chars($title).'"><code>'
				.HTML::chars($command['name']).'</code>'
				.($params !== '' ? ' <span class="ip">'.HTML::chars($params).'</span>' : '')
				.'</td>';
		}

		$html .= '</tr>';

		$html .= '<tr><th>Результат</th>';

		foreach ($devices as $host => $device)
		{
			$command = $this->last_command($device);

			if ($command === NULL OR $command['result'] === NULL)
			{
				$html .= '<td class="muted">—</td>';
				continue;
			}

			$code = (int) $command['result'];
			$note = trim((string) $command['note'].' '.(string) $command['error']);

			$html .= '<td'.($note !== '' ? ' title="'.HTML::chars($note).'"' : '').'>'
				.'<span class="'.($code === 0 ? 'ok' : 'err').'">'.$code.' '
				.HTML::chars(Emulator_Protocol::resultText($code)).'</span></td>';
		}

		$html .= '</tr>';

		$html .= '<tr><th>Время ответа</th>';

		foreach ($devices as $host => $device)
		{
			$command = $this->last_command($device);

			if ($command === NULL)
			{
				$html .= '<td class="muted">—</td>';
				continue;
			}

			$html .= '<td>'.HTML::chars((string) $command['ts'])
				.($command['ms'] !== NULL ? ' · '.HTML::chars((string) $command['ms']).' мс' : '')
				.'</td>';
		}

		$html .= '</tr>';

		foreach (array('0', '1') as $door)
		{
			$html .= '<tr><th>Реле '.$door.' (открытий)</th>';

			foreach ($devices as $host => $device)
			{
				$state_door = isset($device['doors'][$door])
					? $device['doors'][$door]
					: array('count' => 0, 'opened_at' => NULL, 'open_until' => NULL);

				$title = $state_door['open_until']
					? 'открыто до '.$state_door['open_until']
					: 'ещё не открывалось';

				$html .= '<td title="'.HTML::chars($title).'">'.(int) $state_door['count']
					.' <span class="ip">'.HTML::chars((string) $state_door['opened_at']).'</span></td>';
			}

			$html .= '</tr>';
		}

		$html .= '<tr><th>Табло: верх / низ</th>';

		foreach ($devices as $host => $device)
		{
			$lines = $this->display_lines($device);

			$html .= '<td class="text tablo">';

			foreach (array(1, 2) as $row)
			{
				$text = ($lines[$row] === NULL) ? '' : trim((string) $lines[$row]['text']);

				$html .= '<div'.($text !== '' ? ' title="'.HTML::chars($text).'"' : '').'>'
					.($text !== '' ? HTML::chars($text) : '<span class="muted">—</span>')
					.'</div>';
			}

			$html .= '</td>';
		}

		$html .= '</tr>';

		$html .= '<tr><th>Режим</th>';

		foreach ($devices as $host => $device)
		{
			$mode = $this->mode_text($device['mode']);
			$html .= '<td class="text">'.($mode === 'норма'
				? '<span class="ok">норма</span>'
				: '<span class="err">'.HTML::chars($mode).'</span>')
				.'</td>';
		}

		$html .= '</tr>';

		$html .= '<tr><th>Всего команд / ошибок</th>';

		foreach ($devices as $host => $device)
		{
			$html .= '<td>'.(int) $device['stats']['commands'].' / '
				.(int) $device['stats']['errors'].'</td>';
		}

		$html .= '</tr>';

		$html .= '<tr><th>Подключение</th>';

		foreach ($devices as $host => $device)
		{
			$html .= '<td>'.HTML::chars($this->age((string) Arr::get($device, 'last_seen', '')))
				.'</td>';
		}

		$html .= '</tr>';

		$html .= '</tbody></table>';

		return $html;
	}

	/**
	 * Управление комплектами (свёрнуто, чтобы не занимать экран).
	 *
	 * @param  array $state
	 * @return string
	 */
	protected function render_controls(array $state)
	{
		$devices = $this->sort_devices($state['devices']);

		if (empty($devices))
			return '';

		$html = '<details><summary>Управление комплектами</summary><div class="ctl">';

		foreach ($devices as $host => $device)
		{
			$mode = $device['mode'];

			$html .= '<div class="ctl-item">';

			$html .= '<form method="post" action="'.URL::site('mptemu/mode').'">'
				.'<input type="hidden" name="device" value="'.HTML::chars($host).'">'
				.'<b>'.HTML::chars(Emulator_State::instance()->alias($host)).'</b> '
				.'мс <input name="delay_ms" type="number" min="0" max="60000" value="'.(int) $mode['delay_ms'].'"> '
				.'код <select name="result_code">';

			for ($code = 0; $code <= 4; $code++)
			{
				$selected = ((int) $mode['result_code'] === $code) ? ' selected' : '';
				$html .= '<option value="'.$code.'"'.$selected.'>'.$code.'</option>';
			}

			$html .= '</select> '
				.'<label><input type="checkbox" name="silent" value="1"'.($mode['silent'] ? ' checked' : '').'> молчать</label> '
				.'<label><input type="checkbox" name="bad_bcc" value="1"'.($mode['bad_bcc'] ? ' checked' : '').'> битый BCC</label> '
				.'<label><input type="checkbox" name="drop_connection" value="1"'.($mode['drop_connection'] ? ' checked' : '').'> обрыв</label> '
				.'<button type="submit">Применить</button>'
				.'</form>';

			$html .= ' <form method="post" action="'.URL::site('mptemu/clear').'">'
				.'<input type="hidden" name="device" value="'.HTML::chars($host).'">'
				.'<button type="submit">Очистить табло</button></form>';

			$html .= ' <form method="post" action="'.URL::site('mptemu/reset').'"'
				.' onsubmit="return confirm(\'Сбросить этот комплект?\');">'
				.'<input type="hidden" name="device" value="'.HTML::chars($host).'">'
				.'<button type="submit">Сбросить</button></form>';

			$html .= '</div>';
		}

		$html .= '</div>';

		$html .= '<form method="post" action="'.URL::site('mptemu/resetall').'"'
			.' onsubmit="return confirm(\'Сбросить все комплекты?\');">'
			.'<button type="submit">Сбросить все комплекты</button></form>';

		$html .= '</details>';

		return $html;
	}

	/**
	 * История команд по каждому комплекту (свёрнута).
	 *
	 * @param  array $state
	 * @return string
	 */
	protected function render_history(array $state)
	{
		$devices = $this->sort_devices($state['devices']);

		if (empty($devices))
			return '';

		$html = '<details><summary>История команд</summary>';

		foreach ($devices as $host => $device)
		{
			if (empty($device['commands']))
				continue;

			$html .= '<div><b>'.HTML::chars(Emulator_State::instance()->alias($host)).'</b> '
				.'<span class="ip">'.HTML::chars($host).'</span></div>';

			$html .= '<table class="log"><tr><th>время</th><th>команда</th><th>параметры</th>'
				.'<th>код</th><th>мс</th><th>примечание</th></tr>';

			$commands = array_slice(array_reverse($device['commands']), 0, 10);

			foreach ($commands as $command)
			{
				$code = (int) $command['result'];
				$note = trim((string) $command['note'].' '.(string) $command['error']);

				$html .= '<tr><td>'.HTML::chars((string) $command['ts']).'</td>'
					.'<td>'.HTML::chars($command['name'].' '.$command['cmd']).'</td>'
					.'<td><code>'.HTML::chars((string) $command['params']).'</code></td>'
					.'<td class="'.($code === 0 ? 'ok' : 'err').'">'.HTML::chars((string) $command['result']).'</td>'
					.'<td>'.HTML::chars((string) $command['ms']).'</td>'
					.'<td>'.HTML::chars($note).'</td></tr>';
			}

			$html .= '</table>';
		}

		$html .= '</details>';

		return $html;
	}
}
