<?php defined('SYSPATH') OR die('No direct script access.'); ?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title><?php echo HTML::chars($title); ?></title>
<style>
body{font:13px/1.35 Segoe UI,Arial,sans-serif;margin:10px 14px;color:#222}
h1{font-size:17px;margin:0 0 6px}
h2{font-size:13px;margin:14px 0 4px}
.muted{color:#888}
.ok{color:#070;font-weight:600}
.err{color:#b00;font-weight:600}
.warn{background:#fff4d6;border:1px solid #e6c200;padding:8px 10px;border-radius:4px}
.ip{color:#888;font-weight:400;font-size:11px}
code{background:#f2f2f2;padding:0 3px;font-size:11px}
.status-line{margin:3px 0 8px;color:#444}
.conn{display:inline-block;padding:1px 8px;border-radius:9px;font-size:11px;font-weight:600;color:#fff;background:#888}
.conn.on{background:#0a0}
.conn.wait{background:#e6a100}
.conn.off{background:#911}
.matrix{border-collapse:collapse;table-layout:fixed;width:100%;margin-top:6px}
.matrix th,.matrix td{border:1px solid #c9c9c9;padding:3px 5px;vertical-align:middle;
	white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.matrix thead th{background:#e8eef7;text-align:center;font-weight:600;font-size:12px}
.matrix thead th.metric{background:#f2f2f2}
.matrix tbody th{background:#f7f7f7;text-align:left;font-weight:400;color:#555;width:185px}
.matrix td{text-align:center;font-size:12px}
.matrix td.text{text-align:left}
.matrix td.tablo{padding:2px 5px}
.matrix td.tablo div{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.matrix td.tablo div + div{border-top:1px dotted #ddd;color:#555}
.feed{border-collapse:collapse;width:100%;margin-top:4px}
.feed th,.feed td{border:1px solid #ddd;padding:2px 5px;font-size:11px;white-space:nowrap}
.feed th{background:#f3f3f3;text-align:left}
.feed td.note{white-space:normal;color:#555}
.feed tr.fresh td{background:#fff8c9}
.feed tr.fresh{transition:background .8s}
.ctl-item{display:flex;flex-wrap:wrap;align-items:center;gap:6px;padding:3px 0;border-bottom:1px dotted #ddd}
.ctl-item form{display:flex;flex-wrap:wrap;align-items:center;gap:4px;margin:0}
.ctl-item b{min-width:165px}
.ctl-item input[type=number]{width:62px}
details{margin:10px 0}
summary{cursor:pointer;color:#036;font-weight:600}
#toast{position:fixed;right:14px;bottom:14px;background:#222;color:#fff;padding:6px 10px;border-radius:4px;
	font-size:12px;opacity:0;transition:opacity .3s;pointer-events:none}
#toast.show{opacity:.92}
#toast.bad{background:#911}
</style>
</head>
<body>

<h1><?php echo HTML::chars($title); ?></h1>

<?php if ( ! $available): ?>

<p class="warn">
	Модуль <b>mptemu</b> не включён: eventsse работает только поверх него.
	Добавьте в <code>application/bootstrap.php</code> строку
	<code>'mptemu' =&gt; MODPATH.'mptemu',</code> раньше строки <code>'eventsse'</code>.
</p>

<?php else: ?>

<div class="status-line">
	<span id="conn" class="conn wait">подключение…</span>
	<span id="status-text" class="muted"></span>
</div>

<div id="meta" class="status-line muted"></div>

<div id="devices"></div>

<h2>Лента событий <span id="feed-count" class="muted"></span></h2>

<table class="feed">
	<thead>
		<tr>
			<th>время</th>
			<th>комплект</th>
			<th>команда</th>
			<th>параметры</th>
			<th>код</th>
			<th>мс</th>
			<th>примечание</th>
		</tr>
	</thead>
	<tbody id="feed-body"></tbody>
</table>

<details id="controls">
	<summary>Управление комплектами</summary>
	<div id="controls-body"></div>
</details>

<div id="toast"></div>

<noscript>
	<p class="warn">Для живой панели нужен JavaScript и поддержка Server-Sent Events.
	Состояние в JSON доступно по адресу <code><?php echo HTML::chars($urls['state']); ?></code>.</p>
</noscript>

<script>
window.EVENTSSE_CONFIG = <?php echo json_encode(array(
	'stream'     => $urls['stream'],
	'urls'       => array(
		'mode'     => $urls['mode'],
		'clear'    => $urls['clear'],
		'reset'    => $urls['reset'],
		'resetall' => $urls['resetall'],
	),
	'feed_limit' => (int) $feed_limit,
), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

window.EVENTSSE_INITIAL = <?php echo json_encode($snapshot,
	JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

(function () {
	'use strict';

	var CFG = window.EVENTSSE_CONFIG || {};
	var INITIAL = window.EVENTSSE_INITIAL || {};

	var state = {
		meta: INITIAL.meta || {},
		devices: INITIAL.devices || {},
		feed: (INITIAL.feed || []).slice(0, CFG.feed_limit || 100)
	};

	var dom = {
		conn: document.getElementById('conn'),
		status: document.getElementById('status-text'),
		meta: document.getElementById('meta'),
		devices: document.getElementById('devices'),
		feedBody: document.getElementById('feed-body'),
		feedCount: document.getElementById('feed-count'),
		controls: document.getElementById('controls-body'),
		toast: document.getElementById('toast')
	};

	var view = {
		hostsKey: null,
		controlsKey: null,
		events: 0,
		lastEventAt: null
	};

	var source = null;
	var toastTimer = null;

	var RESULT_TEXT = {
		0: 'OK',
		1: 'контрольная сумма',
		2: 'длина пакета',
		3: 'неизвестная команда',
		4: 'ошибка оборудования'
	};

	// ------------------------------------------------------------------
	// Вспомогательные функции
	// ------------------------------------------------------------------

	function el(tag, cls, text) {
		var node = document.createElement(tag);

		if (cls) {
			node.className = cls;
		}

		if (text !== undefined && text !== null) {
			node.textContent = String(text);
		}

		return node;
	}

	function ipValue(ip) {
		var parts = String(ip).split('.');
		var value = 0;

		for (var i = 0; i < 4; i++) {
			value = value * 256 + (parseInt(parts[i], 10) || 0);
		}

		return value;
	}

	function hostsList() {
		return Object.keys(state.devices).sort(function (a, b) {
			var va = ipValue(a);
			var vb = ipValue(b);

			if (va !== vb) {
				return va - vb;
			}

			return a < b ? -1 : (a > b ? 1 : 0);
		});
	}

	function aliasOf(host) {
		var device = state.devices[host];

		return (device && device.alias) ? device.alias : host;
	}

	function cellId(host, field) {
		return 'c-' + host + '-' + field;
	}

	function cell(host, field) {
		return document.getElementById(cellId(host, field));
	}

	function parseDate(value) {
		var match = /^(\d{4})-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)$/.exec(String(value || ''));

		if (!match) {
			return null;
		}

		return new Date(+match[1], +match[2] - 1, +match[3], +match[4], +match[5], +match[6]);
	}

	function ageText(value) {
		var date = parseDate(value);

		if (!date) {
			return '—';
		}

		var diff = Math.round((Date.now() - date.getTime()) / 1000);

		if (diff < 0) {
			return 'только что';
		}

		if (diff < 60) {
			return diff + ' с назад';
		}

		if (diff < 3600) {
			return Math.floor(diff / 60) + ' мин назад';
		}

		if (diff < 86400) {
			return Math.floor(diff / 3600) + ' ч назад';
		}

		return Math.floor(diff / 86400) + ' дн назад';
	}

	function resultText(code) {
		return RESULT_TEXT[code] || ('код ' + code);
	}

	function modeText(mode) {
		mode = mode || {};

		var parts = [];

		if ((parseInt(mode.delay_ms, 10) || 0) > 0) {
			parts.push('задержка ' + mode.delay_ms + ' мс');
		}

		if ((parseInt(mode.result_code, 10) || 0) > 0) {
			parts.push('код ' + mode.result_code);
		}

		if (mode.silent) {
			parts.push('молчит');
		}

		if (mode.bad_bcc) {
			parts.push('битый BCC');
		}

		if (mode.drop_connection) {
			parts.push('обрыв');
		}

		return parts.length ? parts.join(', ') : 'норма';
	}

	function lastCommand(device) {
		var commands = device.commands || [];

		return commands.length ? commands[commands.length - 1] : null;
	}

	function noteOf(record) {
		var parts = [];

		if (record.note) {
			parts.push(record.note);
		}

		if (record.error) {
			parts.push(record.error);
		}

		return parts.join(' ').replace(/\s+/g, ' ').trim();
	}

	function displayLines(device) {
		var result = { 1: null, 2: null };
		var lines = (device.display && device.display.lines) || {};

		Object.keys(lines).forEach(function (key) {
			var line = lines[key];
			var coord = String(line.coord || '').toUpperCase().replace(/[^0-9A-F]/g, '');

			if (!coord) {
				return;
			}

			var first = parseInt(coord.substring(0, 2), 16);

			result[first === 0 ? 1 : 2] = line;
		});

		return result;
	}

	function toast(text, bad) {
		dom.toast.textContent = text;
		dom.toast.className = 'show' + (bad ? ' bad' : '');

		clearTimeout(toastTimer);

		toastTimer = setTimeout(function () {
			dom.toast.className = '';
		}, 2500);
	}

	// ------------------------------------------------------------------
	// Заголовок и состояние соединения
	// ------------------------------------------------------------------

	function setConn(cls, text) {
		dom.conn.className = 'conn ' + cls;
		dom.conn.textContent = text;
	}

	function renderStatusText() {
		var text = 'событий: ' + view.events;

		if (view.lastEventAt) {
			var diff = Math.round((Date.now() - view.lastEventAt) / 1000);
			text += ' · последнее: ' + (diff < 1 ? 'только что' : diff + ' с назад');
		}

		dom.status.textContent = text;
	}

	function renderMeta() {
		var meta = state.meta || {};

		dom.meta.innerHTML = '';

		dom.meta.appendChild(document.createTextNode('Источник: '));
		dom.meta.appendChild(el('code', null, meta.source || '—'));

		dom.meta.appendChild(document.createTextNode(' · запущен: '));
		dom.meta.appendChild(el('span', null, meta.started_at || '—'));

		dom.meta.appendChild(document.createTextNode(' · PID: '));
		dom.meta.appendChild(el('span', null, meta.pid || '—'));

		dom.meta.appendChild(document.createTextNode(' · эмулятор: '));

		if (meta.alive === true) {
			dom.meta.appendChild(el('b', 'ok', 'работает'));
		} else if (meta.alive === false) {
			dom.meta.appendChild(el('b', 'err', 'не запущен'));
		} else {
			dom.meta.appendChild(el('span', 'muted', 'неизвестно'));
		}

		dom.meta.appendChild(document.createTextNode(' · комплектов: '));
		dom.meta.appendChild(el('b', null, hostsList().length));

		dom.meta.appendChild(document.createTextNode(' · снимок: '));
		dom.meta.appendChild(el('span', null, meta.ts || '—'));
	}

	// ------------------------------------------------------------------
	// Матрица «комплекты по горизонтали, показатели по вертикали»
	// ------------------------------------------------------------------

	function buildTable() {
		var hosts = hostsList();

		dom.devices.innerHTML = '';

		if (!hosts.length) {
			dom.devices.appendChild(el('p', 'muted',
				'Комплектов пока нет: эмулятор ещё не принимал подключений. '
				+ 'Пропишите в hl_param box_ip/tablo_ip на адрес этого хоста '
				+ '(например 127.0.0.2) и отправьте событие.'));

			view.hostsKey = '';

			return;
		}

		var table = el('table', 'matrix');
		var thead = el('thead');
		var headRow = el('tr');

		headRow.appendChild(el('th', 'metric', 'Показатель'));

		hosts.forEach(function (host) {
			var th = el('th');

			th.appendChild(document.createTextNode(aliasOf(host)));
			th.appendChild(el('br'));
			th.appendChild(el('span', 'ip', host));

			headRow.appendChild(th);
		});

		thead.appendChild(headRow);
		table.appendChild(thead);

		var tbody = el('tbody');

		var rows = [
			['cmd', 'Последняя команда'],
			['res', 'Результат'],
			['ms', 'Время ответа'],
			['door0', 'Реле 0 (открытий)'],
			['door1', 'Реле 1 (открытий)'],
			['tablo', 'Табло: верх / низ'],
			['mode', 'Режим'],
			['stats', 'Всего команд / ошибок'],
			['seen', 'Подключение']
		];

		rows.forEach(function (row) {
			var tr = el('tr');

			tr.appendChild(el('th', null, row[1]));

			hosts.forEach(function (host) {
				var cls = '';

				if (row[0] === 'cmd' || row[0] === 'mode') {
					cls = 'text';
				} else if (row[0] === 'tablo') {
					cls = 'text tablo';
				}

				var td = el('td', cls);
				td.id = cellId(host, row[0]);

				tr.appendChild(td);
			});

			tbody.appendChild(tr);
		});

		table.appendChild(tbody);
		dom.devices.appendChild(table);

		view.hostsKey = hosts.join(',');
	}

	function ensureTable() {
		if (hostsList().join(',') === view.hostsKey) {
			return;
		}

		buildTable();
	}

	function fill(host) {
		var device = state.devices[host];

		if (!device) {
			return;
		}

		var last = lastCommand(device);
		var node;

		// Последняя команда
		node = cell(host, 'cmd');

		if (node) {
			node.innerHTML = '';

			if (!last) {
				node.title = '';
				node.appendChild(el('span', 'muted', '—'));
			} else {
				node.appendChild(el('code', null, (last.name || '-') + ' ' + (last.cmd || '')));

				if (last.params) {
					node.appendChild(document.createTextNode(' '));
					node.appendChild(el('span', 'ip', last.params));
				}

				node.title = (last.name || '') + ' ' + (last.cmd || '')
					+ (last.params ? ' [' + last.params + ']' : '');
			}
		}

		// Результат
		node = cell(host, 'res');

		if (node) {
			node.innerHTML = '';
			node.title = '';

			if (!last || last.result === null || last.result === undefined) {
				node.appendChild(el('span', 'muted', '—'));
			} else {
				var code = parseInt(last.result, 10);

				node.appendChild(el('span', code === 0 ? 'ok' : 'err', code + ' ' + resultText(code)));

				var note = noteOf(last);

				if (note) {
					node.title = note;
				}
			}
		}

		// Время ответа
		node = cell(host, 'ms');

		if (node) {
			node.innerHTML = '';

			if (!last) {
				node.appendChild(el('span', 'muted', '—'));
			} else {
				node.appendChild(document.createTextNode(String(last.ts || '')));

				if (last.ms !== null && last.ms !== undefined) {
					node.appendChild(document.createTextNode(' · ' + last.ms + ' мс'));
				}
			}
		}

		// Реле 0 / 1
		[0, 1].forEach(function (door) {
			var doorCell = cell(host, 'door' + door);

			if (!doorCell) {
				return;
			}

			doorCell.innerHTML = '';

			var data = (device.doors && device.doors[door])
				? device.doors[door]
				: { count: 0, opened_at: null, open_until: null };

			doorCell.appendChild(document.createTextNode(String(data.count || 0)));
			doorCell.appendChild(document.createTextNode(' '));
			doorCell.appendChild(el('span', 'ip', data.opened_at || ''));
			doorCell.title = data.open_until
				? 'открыто до ' + data.open_until
				: 'ещё не открывалось';
		});

		// Табло: верхняя строка (координата 00…) и нижняя (08…)
		node = cell(host, 'tablo');

		if (node) {
			node.innerHTML = '';

			var lines = displayLines(device);

			[1, 2].forEach(function (row) {
				var text = lines[row] ? String(lines[row].text || '').trim() : '';
				var div = el('div', text ? null : 'muted', text || '—');

				if (text) {
					div.title = text;
				}

				node.appendChild(div);
			});
		}

		// Режим имитации
		node = cell(host, 'mode');

		if (node) {
			var mode = modeText(device.mode);

			node.innerHTML = '';
			node.appendChild(el('span', mode === 'норма' ? 'ok' : 'err', mode));
		}

		// Счётчики
		node = cell(host, 'stats');

		if (node) {
			var stats = device.stats || {};

			node.textContent = (stats.commands || 0) + ' / ' + (stats.errors || 0);
		}

		// Подключение
		node = cell(host, 'seen');

		if (node) {
			node.textContent = ageText(device.last_seen);
			node.title = device.last_seen || '';
		}
	}

	// ------------------------------------------------------------------
	// Лента событий
	// ------------------------------------------------------------------

	function commandRow(record) {
		var tr = el('tr');
		var code = (record.result === null || record.result === undefined)
			? null
			: parseInt(record.result, 10);

		tr.appendChild(el('td', null, record.ts || '—'));
		tr.appendChild(el('td', null, record.alias || record.host || '—'));
		tr.appendChild(el('td', null, ((record.name || '—') + ' ' + (record.cmd || '')).trim()));

		var params = el('td');
		params.appendChild(el('code', null, record.params || ''));
		tr.appendChild(params);

		tr.appendChild(el('td', code === null ? 'muted' : (code === 0 ? 'ok' : 'err'),
			code === null ? '—' : String(code)));

		tr.appendChild(el('td', null,
			(record.ms === null || record.ms === undefined) ? '—' : String(record.ms)));

		tr.appendChild(el('td', 'note', noteOf(record) || ''));

		return tr;
	}

	function updateFeedCount() {
		dom.feedCount.textContent = '· ' + dom.feedBody.children.length;
	}

	function renderFeed() {
		dom.feedBody.innerHTML = '';

		state.feed.forEach(function (record) {
			dom.feedBody.appendChild(commandRow(record));
		});

		updateFeedCount();
	}

	function prependFeed(records) {
		var limit = CFG.feed_limit || 100;

		records.forEach(function (record) {
			var tr = commandRow(record);

			tr.className = 'fresh';
			dom.feedBody.insertBefore(tr, dom.feedBody.firstChild);

			setTimeout(function () {
				tr.className = '';
			}, 1500);
		});

		while (dom.feedBody.children.length > limit) {
			dom.feedBody.removeChild(dom.feedBody.lastChild);
		}

		updateFeedCount();
	}

	// ------------------------------------------------------------------
	// Управление комплектами (POST, без опроса)
	// ------------------------------------------------------------------

	function checkBox(name, label, checked) {
		var wrap = el('label');
		var input = el('input');

		input.type = 'checkbox';
		input.name = name;
		input.checked = !!checked;

		wrap.appendChild(input);
		wrap.appendChild(document.createTextNode(' ' + label));

		return wrap;
	}

	function buttonForm(host, action, label, confirmText) {
		var form = el('form');

		if (host) {
			form.dataset.host = host;
		}

		form.dataset.action = action;

		if (confirmText) {
			form.dataset.confirm = confirmText;
		}

		var button = el('button', null, label);
		button.type = 'submit';
		form.appendChild(button);

		return form;
	}

	function ensureControls() {
		var key = hostsList().join(',');

		if (key === view.controlsKey) {
			return;
		}

		view.controlsKey = key;
		dom.controls.innerHTML = '';

		var hosts = hostsList();

		if (!hosts.length) {
			dom.controls.appendChild(el('p', 'muted', 'Управлять пока нечем.'));
			return;
		}

		hosts.forEach(function (host) {
			var device = state.devices[host] || {};
			var mode = device.mode || {};

			var item = el('div', 'ctl-item');

			item.appendChild(el('b', null, aliasOf(host)));

			var form = el('form');
			form.dataset.host = host;
			form.dataset.action = 'mode';

			var delay = el('input');
			delay.type = 'number';
			delay.name = 'delay_ms';
			delay.min = '0';
			delay.max = '60000';
			delay.value = mode.delay_ms || 0;
			delay.title = 'задержка ответа, мс';

			form.appendChild(document.createTextNode(' мс '));
			form.appendChild(delay);

			form.appendChild(document.createTextNode(' код '));

			var select = el('select');
			select.name = 'result_code';

			for (var code = 0; code <= 4; code++) {
				var option = el('option', null, code);

				option.value = String(code);

				if ((parseInt(mode.result_code, 10) || 0) === code) {
					option.selected = true;
				}

				select.appendChild(option);
			}

			form.appendChild(select);
			form.appendChild(checkBox('silent', 'молчать', mode.silent));
			form.appendChild(checkBox('bad_bcc', 'битый BCC', mode.bad_bcc));
			form.appendChild(checkBox('drop_connection', 'обрыв', mode.drop_connection));

			var submit = el('button', null, 'Применить');
			submit.type = 'submit';
			form.appendChild(submit);

			item.appendChild(form);
			item.appendChild(buttonForm(host, 'clear', 'Очистить табло'));
			item.appendChild(buttonForm(host, 'reset', 'Сбросить', 'Сбросить этот комплект?'));

			dom.controls.appendChild(item);
		});

		dom.controls.appendChild(
			buttonForm('', 'resetall', 'Сбросить все комплекты', 'Сбросить все комплекты?')
		);
	}

	function post(url, payload) {
		if (!url) {
			return;
		}

		var xhr = new XMLHttpRequest();

		xhr.open('POST', url, true);
		xhr.setRequestHeader('Content-Type', 'application/json');
		xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

		xhr.onreadystatechange = function () {
			if (xhr.readyState !== 4) {
				return;
			}

			var data = null;

			try {
				data = JSON.parse(xhr.responseText);
			} catch (e) {
				data = null;
			}

			if (xhr.status >= 200 && xhr.status < 300 && data && data.ok) {
				if (data.device) {
					state.devices[data.device.host || payload.device] = data.device;
					ensureTable();
					ensureControls();
					fill(data.device.host || payload.device);
					renderMeta();
				}

				// Сброс: убираем комплект (или все) сразу, не дожидаясь потока.
				if (data.removed === '*') {
					state.devices = {};
					state.feed = [];
					ensureTable();
					ensureControls();
					renderFeed();
					renderMeta();
				} else if (data.removed) {
					delete state.devices[data.removed];
					state.feed = state.feed.filter(function (record) {
						return record.host !== data.removed;
					});
					ensureTable();
					ensureControls();
					renderFeed();
					renderMeta();
				}

				toast('применено');
				return;
			}

			toast((data && data.error) || ('ошибка ' + xhr.status), true);
		};

		xhr.send(JSON.stringify(payload));
	}

	document.addEventListener('submit', function (event) {
		var form = event.target;

		if (!form.dataset || !form.dataset.action) {
			return;
		}

		event.preventDefault();

		if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
			return;
		}

		var action = form.dataset.action;
		var payload = { device: form.dataset.host || '' };

		if (action === 'mode') {
			payload.delay_ms = parseInt(form.elements.delay_ms.value, 10) || 0;
			payload.result_code = parseInt(form.elements.result_code.value, 10) || 0;
			payload.silent = !!form.elements.silent.checked;
			payload.bad_bcc = !!form.elements.bad_bcc.checked;
			payload.drop_connection = !!form.elements.drop_connection.checked;
		}

		post(CFG.urls[action], payload);
	});

	// ------------------------------------------------------------------
	// Поток SSE
	// ------------------------------------------------------------------

	function touch() {
		view.events++;
		view.lastEventAt = Date.now();
		renderStatusText();
	}

	function applySnapshot(data) {
		state.meta = data.meta || {};
		state.devices = data.devices || {};
		state.feed = (data.feed || []).slice(0, CFG.feed_limit || 100);

		ensureTable();
		hostsList().forEach(fill);
		ensureControls();
		renderMeta();
		renderFeed();
	}

	function connect() {
		if (!window.EventSource) {
			setConn('off', 'нет поддержки SSE');
			toast('Браузер не поддерживает Server-Sent Events', true);

			return;
		}

		source = new EventSource(CFG.stream);

		source.onopen = function () {
			setConn('on', 'поток подключён');
		};

		source.onerror = function () {
			setConn('wait', 'переподключение…');
		};

		source.addEventListener('snapshot', function (event) {
			applySnapshot(JSON.parse(event.data));
			touch();
		});

		source.addEventListener('meta', function (event) {
			state.meta = JSON.parse(event.data) || {};

			renderMeta();
			touch();
		});

		source.addEventListener('device', function (event) {
			var data = JSON.parse(event.data) || {};
			var host = data.host;

			if (data.device === null || data.device === undefined) {
				delete state.devices[host];

				ensureTable();
				ensureControls();
				renderMeta();
				touch();

				return;
			}

			state.devices[host] = data.device;

			ensureTable();
			ensureControls();
			fill(host);
			renderMeta();
			touch();
		});

		source.addEventListener('command', function (event) {
			var data = JSON.parse(event.data) || {};
			var records = data.records || [];

			records.forEach(function (record) {
				record.host = data.host;
				record.alias = data.alias || data.host;
			});

			state.feed = records.concat(state.feed).slice(0, CFG.feed_limit || 100);

			prependFeed(records);
			touch();
		});

		source.addEventListener('bye', function () {
			// Сервер сам закрыл поток по max_seconds; EventSource переподключится.
			setConn('wait', 'переоткрытие потока…');
		});

		source.addEventListener('fatal', function (event) {
			var data = {};

			try {
				data = JSON.parse(event.data);
			} catch (e) {
				data = {};
			}

			setConn('off', 'поток недоступен');
			toast(data.message || 'Поток недоступен', true);

			// Ошибка конфигурации: переподключаться бессмысленно.
			source.close();
		});
	}

	// ------------------------------------------------------------------
	// Старт
	// ------------------------------------------------------------------

	function tick() {
		renderStatusText();

		hostsList().forEach(function (host) {
			var node = cell(host, 'seen');

			if (node && state.devices[host]) {
				node.textContent = ageText(state.devices[host].last_seen);
			}
		});
	}

	function start() {
		renderMeta();
		ensureTable();
		hostsList().forEach(fill);
		ensureControls();
		renderFeed();
		renderStatusText();
		connect();

		setInterval(tick, 1000);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
</script>

<?php endif; ?>

</body>
</html>
