<?php defined('SYSPATH') or die('No direct script access.');

//для отладки http://localhost/cvs/index.php/dashboard/exec для ГРЗ
//для отладки http://localhost/cvs/index.php/dashboard/sendMPT для UHF


/**
* @package    dashboard
 * @category   Base
 * @author     Artonit
 * @copyright  (c) 2025 Artonit Team
 * @license    http://artonit/ru 
 
 */
class Controller_Dashboard extends Controller{
//class Controller_Dashboard extends Controller_Template {

   public $template = 'template';
   public $permission = false;
   //K631TX199
   //H497HB150
   
   //007E3AC2
   public $dataGRZ=array (
			'camera' => '1',
			'channel' => 3,
			'count' => 16,
			'dateTime' => '20250429T141918Z',
			'description' =>'---',
			'direction' => 0,
			'groupId' => -1,
			'id' => 624101,
			'image' =>  '/9j/4AAQSkZJRgABAQAAAQABAAD//gALQ1ZTIMDi8u4r/9sAQwAGBAUGBQQGBgUGBwcGCAoQCgoJCQoUDg8MEBcUGBgXFBYWGh0lHxobIxwWFiAsICMmJykqKRkfLTAtKDAlKCko/9sAQwEHBwcKCAoTCgoTKBoWGigoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgo/8QBogAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoLEAACAQMDAgQDBQUEBAAAAX0BAgMABBEFEiExQQYTUWEHInEUMoGRoQgjQrHBFVLR8CQzYnKCCQoWFxgZGiUmJygpKjQ1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4eLj5OXm5+jp6vHy8/T19vf4+foBAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKCxEAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/8AAEQgC0AUAAwEhAAIRAQMRAf/aAAwDAQACEQMRAD8A8zUs7okcckkkjrGiRoXZmY4CgDkkkgYFd5p/hfwv4X0CHUvic1xc6nfqj2mg2d0wlEciqcumI2DqRIGLNsABHLYoAx4/FwaJGk8HeAVcqMr/AGGDg46ZEnP1ro/BcHhDx3eyaVrmm22heIZnC2MuhQPawzRKpkI27nj3jbJnzBypGM9gDK+HOh2H/CY6v4V8&nbsp;&hellip;',
			'inList' => 0,
			'passed' => 1,
			'plate' => 'K631TX199',
			'quality' =>  '555555555000',
			'stayTimeMinutes' => 0,
			'type' => 0,
			'weight' => 0
			);
			
	public $dataUHF=array(
			'key'=>'007E3AC2',
			'ip'=>'192.168.0.100',
			'channel'=>'0'
			);
			
	public $ip_is_test=false;//режим отладки. false -рабочий режим, true - режим ТЕСТ. В этом режиме IP берется из POST запроса
	
	public $ip_test='172.16.101.101';
			
	
	public function before()
	{
			
			parent::before();
			//$session = Session::instance();
		
	}
	
public function action_index()
{
    $mess  = 'Модуль интеграции системы распознавания государственных регистрационных знаков CVS и СКУД Артонит Сити.<br>';
    $mess .= 'www.artsec.ru<br>';
    $mess .= '2022-' . date("Y") . '<br>';
    $mess .= 'Ver:' . (defined('CVS_VERSION') ? CVS_VERSION : 'unknown') . '<br>';
    $mess .= HTML::anchor('guide', 'guide') . '<br><br>';
    $mess .= '<b>Тестирование</b><br>
        запусть файл C:\xampp\htdocs\cvs\modules\ver30\classes\Task\test_garage_057_3491248_in.bat<br>
        На рабочем месте охраны https://localhost/parkresident/index.php/rmo наблюдать появление события.<br><br>
        <b>Внимание!</b><br>Если в колонке comment указано debug_ON, то изменить настройку debug в файле C:\xampp\htdocs\cvs\modules\ver30\config\config.php.<br><br>';

    // --- Блок "Текущие настройки" ---
    // Вариант 1: если config.php зарегистрирован как Kohana-конфиг (например, 'config')
    $cfg = Kohana::$config->load('config');
    $testMode = (bool) $cfg->get('testMode', false);
    $debug    = (bool) $cfg->get('debug', false);
    $reverseGate = $cfg->get('reverseGate', array());

    // Вариант 2 (если конфиг не зарегистрирован в Kohana, а лежит файлом):
    // $cfg = include MODPATH.'ver30/config/config.php';
    // $testMode = (bool) Arr::get($cfg, 'testMode', false);
    // $debug    = (bool) Arr::get($cfg, 'debug', false);
    // $reverseGate = Arr::get($cfg, 'reverseGate', array());

    // reverseGate может быть массивом или строкой — приводим к строке для вывода
    if (is_array($reverseGate)) {
        $reverseGateStr = empty($reverseGate) ? '—' : implode(', ', $reverseGate);
    } else {
        $reverseGateStr = (string) $reverseGate;
    }

    $mess .= '<b>Текущие настройки</b><br>';
    $mess .= '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;margin-bottom:15px;">';
    $mess .= '<tr><th align="left">Параметр</th><th align="left">Значение</th><th align="left">Описание</th></tr>';
    $mess .= '<tr><td colspan="3" align="left" style="background:#f2f2f2;"><b>Общие настройки</b> ('.HTML::chars($this->_config_path('config')).')</td></tr>';
    $mess .= '<tr><td>testMode</td><td>' . ($testMode ? 'true' : 'false') . '</td><td>true - проезд разрешен по любому идентификатору<br>false - рабочий режим.</td></tr>';
    $mess .= '<tr><td>debug</td><td>' . ($debug ? 'true' : 'false') . '</td><td>true - команды на контроллеры и табло не подаются<br>false - рабочий режим.</td></tr>';
    $mess .= '<tr><td>reverseGate</td><td>' . HTML::chars($reverseGateStr) . '</td><td>id ворот, работающих в ревресинов режиме (и на въезд, и на выезд).<br>Если gate находится в этом списке, то щелкнут оба реле</td></tr>';
    $mess .= '<tr><td colspan="3" align="left" style="background:#f2f2f2;"><b>MQTT</b> ('.HTML::chars($this->_config_path('mqtt')).')</td></tr>';
    $mess .= $this->_mqtt_settings();
    $mess .= '</table><br>';

    // --- Блок "Подключение к базе данных" ---
    $mess .= $this->_db_connection();

    // Кнопка «Тест» — отправляет POST с $dataGRZ на dashboard/exec
    $mess .= '<button id="btnTestCvs" type="button">Тест CVS</button>';
    // Кнопка «Тест MQTT» — проверяет брокера через dashboard/testMqtt
    $mess .= ' <button id="btnTestMqtt" type="button">Тест MQTT</button>';
    $mess .= '<pre id="testResult" style="margin-top:10px;white-space:pre-wrap;"></pre>';
    $mess .= '<pre id="testMqttResult" style="margin-top:10px;white-space:pre-wrap;"></pre>';

    // JSON с тестовыми данными из $this->dataGRZ
    $testPayload = json_encode(array('plate' => $this->dataGRZ));

    $mess .= '<script>
    (function () {
        var url = ' . json_encode(URL::site('dashboard/exec', true)) . ';
        var payload = ' . $testPayload . ';

        document.getElementById("btnTestCvs").addEventListener("click", function () {
            var out = document.getElementById("testResult");
            out.textContent = "Отправка POST на " + url + " ...";

            fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            })
            .then(function (r) {
                return r.text().then(function (t) {
                    out.textContent = "HTTP " + r.status + " " + r.statusText + "\n\n" + t;
                });
            })
            .catch(function (e) {
                out.textContent = "Ошибка: " + e;
            });
        });
    })();
    </script>';

    // --- Кнопка «Тест MQTT» ---
    $mess .= '<script>
    (function () {
        var url = ' . json_encode(URL::site('dashboard/testMqtt', true)) . ';
        var btn = document.getElementById("btnTestMqtt");
        var out = document.getElementById("testMqttResult");

        function yes(flag) {
            return flag ? "OK" : "нет";
        }

        btn.addEventListener("click", function () {
            btn.disabled = true;
            out.style.color = "";
            out.textContent = "Проверка MQTT: " + url + " ...";

            fetch(url, { method: "POST" })
            .then(function (r) {
                if ( ! r.ok) {
                    throw new Error("HTTP " + r.status + " " + r.statusText);
                }
                return r.json();
            })
            .then(function (d) {
                var text = "";
                text += "Брокер: " + d.host + ":" + d.port + "\n";
                text += "TCP-доступность: " + yes(d.probe) + "\n";
                text += "Подключение CONNECT: " + yes(d.connected) + "\n";
                text += "Публикация: " + (d.published ? "OK" : (d.enabled ? "нет" : "отключена (enabled = false)")) + "\n";
                text += "Топик: " + d.topic + "\n";

                if (d.published) {
                    text += "Сообщение: " + JSON.stringify(d.payload) + "\n";
                }

                text += "\n" + d.message + "\n";
                text += "Итог: " + (d.ok ? "тест пройден" : "ОШИБКА") + " (" + d.ms + " мс)";

                out.textContent = text;
                out.style.color = d.ok ? "#3c763d" : "#a94442";
                btn.disabled = false;
            })
            .catch(function (e) {
                out.textContent = "Ошибка: " + e;
                out.style.color = "#a94442";
                btn.disabled = false;
            });
        });
    })();
    </script>';

    $this->response->body($mess);
}	

	/**
	 * Тест настроек MQTT — вызывается кнопкой "Тест MQTT" на панели.
	 *
	 * Проверяет TCP-доступность брокера, подключение (CONNECT) и публикует
	 * пробное сообщение в топик <prefix>/dashboard/test. Результат отдается
	 * в JSON, чтобы страница показала, на каком этапе проверка не прошла.
	 *
	 * URL: dashboard/testMqtt
	 *
	 * @return  void
	 */
	public function action_testMqtt()
	{
		$t1 = microtime(TRUE);

		$result = Mqtt_Publisher::instance()->test();

		$result['ms'] = (int) round((microtime(TRUE) - $t1) * 1000);

		$json = json_encode($result);

		if ($json === FALSE)
		{
			// например, в тексте ошибки драйвера/брокера попались не-UTF-8 байты
			$json = json_encode(array(
				'ok'      => FALSE,
				'enabled' => Arr::get($result, 'enabled', FALSE),
				'host'    => Arr::get($result, 'host', ''),
				'port'    => Arr::get($result, 'port', 0),
				'probe'   => Arr::get($result, 'probe', FALSE),
				'connected' => Arr::get($result, 'connected', FALSE),
				'published' => FALSE,
				'topic'   => Arr::get($result, 'topic', ''),
				'payload' => array(),
				'ms'      => $result['ms'],
				'error'   => 'не удалось сериализовать результат теста в JSON',
				'message' => 'не удалось сериализовать результат теста в JSON',
			));
		}

		$this->response
			->headers('Content-Type', 'application/json')
			->body($json);
	}

	/**
	 * Путь к файлу настроек группы конфигурации Kohana.
	 *
	 * Для группы 'config' (как и 'i18n', 'messages') Kohana::find_file()
	 * всегда возвращает массив найденных файлов - они объединяются при
	 * загрузке конфигурации. Для вывода берем последний файл: так как пути
	 * перебираются от system к application, последним оказывается файл
	 * с наибольшим приоритетом.
	 *
	 * @param   string  $group  имя группы ('config', 'mqtt' ...)
	 * @return  string
	 */
	protected function _config_path($group)
	{
		$found = Kohana::find_file('config', $group, 'php');

		if (is_array($found))
		{
			$found = empty($found) ? FALSE : end($found);
		}

		return $found ? $found : 'файл настроек не найден';
	}

	/**
	 * Строки таблицы "Текущие настройки" с параметрами MQTT.
	 *
	 * Значения читаются из modules/ver30/config/mqtt.php через штатный
	 * механизм конфигурации Kohana (группа 'mqtt') - те же данные, что
	 * использует Mqtt_Publisher.
	 *
	 * @return  string  HTML
	 */
	protected function _mqtt_settings()
	{
		$config = Kohana::$config->load('mqtt');

		// ключ настройки => описание для таблицы
		$params = array(
			'enabled'         => 'true - команды ворот и табло дублируются в MQTT<br>false - публикация в MQTT отключена',
			'host'            => 'адрес брокера MQTT',
			'port'            => 'TCP-порт брокера',
			'username'        => 'логин для подключения к брокеру (— если авторизация не нужна)',
			'password'        => 'пароль для подключения к брокеру (на странице не выводится)',
			'client_id'       => 'базовый client id; к нему добавляется случайный суффикс, чтобы брокер закрывал предыдущую сессию',
			'prefix'          => 'префикс топиков: &lt;prefix&gt;/gate/&lt;id_gate&gt;/controller и &lt;prefix&gt;/gate/&lt;id_gate&gt;/tablo',
			'qos'             => 'уровень качества обслуживания MQTT (0..2)',
			'retain'          => 'true - брокер хранит последнее сообщение в топике<br>false - сообщения не сохраняются',
			'connect_timeout' => 'таймаут предварительной проверки брокера, сек',
			'down_ttl'        => 'пауза после неудачного подключения к брокеру, сек',
		);

		$html = '';

		foreach ($params as $key => $note)
		{
			$value = $config->get($key);

			if ($key === 'password')
			{
				// пароль в открытом виде на странице не показываем
				$value = ($value === NULL OR $value === '') ? '—' : '******';
			}
			else
			{
				$value = HTML::chars($this->_config_value($value));
			}

			$html .= '<tr><td>'.HTML::chars($key).'</td><td>'.$value.'</td><td>'.$note.'</td></tr>';
		}

		return $html;
	}

	/**
	 * Привести значение настройки к строке для вывода в таблице.
	 *
	 * @param   mixed   $value
	 * @return  string
	 */
	protected function _config_value($value)
	{
		if ($value === NULL OR $value === '')
			return '—';

		if (is_bool($value))
			return $value ? 'true' : 'false';

		if (is_array($value))
			return empty($value) ? '—' : implode(', ', $value);

		return (string) $value;
	}

	/**
	 * Блок с параметрами подключения к базе данных:
	 * имя DSN, IP-адрес, путь к базе данных, состояние подключения.
	 *
	 * @return  string  HTML
	 */
	protected function _db_connection()
	{
		$info  = DbInfo::get();
		$check = DbInfo::check();

		$dsn_name = ($info['dsn_name'] !== '') ? $info['dsn_name'] : '—';
		$ip       = ($info['ip'] !== '') ? $info['ip'] : '—';
		$path     = ($info['path'] !== '') ? $info['path'] : '—';

		// описание источника данных
		$dsn_note = 'имя источника данных ODBC (строка подключения '.HTML::chars($info['dsn']).')';

		if ($info['driver'] !== '')
		{
			$dsn_note .= ', драйвер '.HTML::chars(basename($info['driver']));
		}

		if ($info['error'] !== '')
		{
			$dsn_note .= '<br><span style="color:#a94442;">'.HTML::chars($info['error']).'</span>';
		}

		// состояние подключения
		if ($check['ok'])
		{
			$state = '<span style="color:#3c763d;font-weight:bold;">подключено</span>';

			if ($check['ms'] > 0)
			{
				$state .= ' ('.$check['ms'].' мс)';
			}

			if ($check['warning'] !== '')
			{
				$state .= '<br><small>проверочный запрос: '.HTML::chars($this->_short($check['warning'])).'</small>';
			}
		}
		else
		{
			$state = '<span style="color:#a94442;font-weight:bold;">нет подключения</span>';

			if ($check['error'] !== '')
			{
				$state .= '<br><small style="color:#a94442;">'.HTML::chars($this->_short($check['error'])).'</small>';
			}
		}

		// наличие таблицы настроек HL_SETTING
		$table      = Arr::get($check, 'table', array());
		$table_name = Arr::get($table, 'name', DbInfo::SETTING_TABLE);

		if (Arr::get($table, 'ok'))
		{
			$table_state = '<span style="color:#3c763d;font-weight:bold;">всё ОК</span>';
			$table_note  = 'таблица '.HTML::chars($table_name).' есть в базе данных';
		}
		else
		{
			$table_state = '<span style="color:#a94442;font-weight:bold;">не ОК</span>';

			if ($check['ok'])
			{
				$table_error = (string) Arr::get($table, 'error');

				// драйвер сообщает об отсутствии таблицы разными словами
				if (preg_match('/Table unknown|no such table|Base table or view not found/i', $table_error))
				{
					$table_note = 'таблица '.HTML::chars($table_name).' не найдена в базе данных';
				}
				else
				{
					$table_note = 'таблица '.HTML::chars($table_name).' недоступна';

					if ($table_error !== '')
					{
						$table_note .= ': '.HTML::chars($this->_short($table_error));
					}
				}
			}
			else
			{
				$table_note = 'проверка не выполнена: нет подключения к базе данных';
			}
		}

		$html  = '<b>Подключение к базе данных</b><br>';
		$html .= '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;margin-bottom:15px;">';
		$html .= '<tr><th align="left">Параметр</th><th align="left">Значение</th><th align="left">Описание</th></tr>';
		$html .= '<tr><td>Имя DSN</td><td>'.HTML::chars($dsn_name).'</td><td>'.$dsn_note.'</td></tr>';
		$html .= '<tr><td>IP-адрес</td><td>'.HTML::chars($ip).'</td><td>адрес сервера базы данных (параметр Server источника данных)</td></tr>';
		$html .= '<tr><td>Путь к базе данных</td><td>'.HTML::chars($path).'</td><td>файл базы данных на сервере (параметр Database источника данных)</td></tr>';
		$html .= '<tr><td>Состояние подключения</td><td>'.$state.'</td><td>результат проверки подключения при загрузке страницы</td></tr>';
		$html .= '<tr><td>Таблица '.HTML::chars($table_name).'</td><td>'.$table_state.'</td><td>'.$table_note.'</td></tr>';
		$html .= '</table><br>';

		return $html;
	}

	/**
	 * Сократить текст (например, длинное сообщение драйвера) до заданной длины.
	 *
	 * @param   string   $text
	 * @param   integer  $length
	 * @return  string
	 */
	protected function _short($text, $length = 400)
	{
		$text = trim(preg_replace('/\s+/u', ' ', (string) $text));

		if (function_exists('mb_strlen') AND mb_strlen($text, 'UTF-8') > $length)
		{
			$text = mb_substr($text, 0, $length, 'UTF-8').'…';
		}

		return $text;
	}
	
	public function action_sendMPT()
	{
		Log::instance()->add(Log::NOTICE, "/r/n88 start UHF action_sendMPT");
		$t1=microtime(1);
		
		//фиксирую полученные данные из $_OST в лог-файл
			$input_data_0=$_POST;//извлекаю данных из полученного пакета
		if(!Arr::get($input_data_0, 'test'))
		{
			//если в запросе нет поля test, то беру IP адрес из запроса
			$input_data_0['ip']=Request::$client_ip;
		}
		
		
		$input_data = $input_data_0;
		
		Log::instance()->add(Log::NOTICE, '114 MPT получил данные :data', array(':data'=>Debug::vars($input_data)));

		$post=Validation::factory($input_data);
		
		//case 'input'://это сигнал от индуктивной петли. Надо зафиксироват событие
		if(Arr::get($post, 'type') == 'input'){
			
				
				//вызов обработчика сигнала индуктивной петли.
				switch(Arr::get($post, 'pin')){
						case 'INPUT2':
							$ch=0;
						break;
						case 'INPUT3':
							$ch=1;
						break;
					
					
				}
				
				//Log::instance()->add(Log::NOTICE, '109 changeLoop pin :pin ip :ip ch :ch  state :state', array(':ip'=> Arr::get($post, 'ip'), ':ch'=> Arr::get($post, 'pin'), ':state'=> Arr::get($post, 'state'), ':pin'=>$ch));
				
				$id_gate=Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), $ch);
				Model::factory('cvss')->loophandler($id_gate);
					switch(Arr::get($post, 'state')){
						case 0:
							$eventCode=Events::LOOP0;
						break;
						case 1:
							$eventCode=Events::LOOP1;
						break;
						
					}
					$events= new Events();
					//$events->grz=null;
					$events->id_gate=$id_gate;
					//$events->is_enter=null;
					$events->eventCode=$eventCode;
			//$events->addEventRow(); 
			//Log::instance()->add(Log::NOTICE, '138 save events loop eventCode :eventCode', array(':eventCode'=> $eventCode));
			//break;
			
			//завершаю обработку 
			//Log::instance()->add(Log::NOTICE, '999-142 stop UHF total_time  :data', array(':data'=>(microtime(true) - $t1)));
			exit;
			
		}
		$post->rule('ch', 'not_empty')//номер канала должен быть 
					->rule('ch', 'digit')
					->rule('ip', 'not_empty')//IP контроллера должен быть
					->rule('ip', 'ip')
					->rule('ip', 'Model_cvss::checkIpIsPresent') 
					->rule('key', 'not_empty')//значение ГРЗ
					->rule('key', 'regex', array(':value', '/^[ABCDEF\d]{3,8}+$/')) // https://regex101.com/ строк буквы АНГЛ алфавита
					
					;
				
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '95 Входные данные UHF не полные '. Debug::vars($post->errors()));//вывод номера в лог-файл
			Log::instance()->add(Log::NOTICE, '131 Обработку UHF прекращаю.');//вывод номера в лог-файл
			//$this->response->status(400);
			return;
		}
		
		//29.08.2025 check loop
		
			$mpt2=new phpMPTtcp(Arr::get($post, 'ip'),8000);
			$mpt2->command='getPortState';
			$mpt2->execute();
			Log::instance()->add(Log::NOTICE, '133 poststate key :key ip :ip ch :ch  state :data', array(':data'=> bin2hex($mpt2->answer), ':ip'=> Arr::get($post, 'ip'), ':key'=> Arr::get($post, 'key'), ':ch'=> Arr::get($post, 'ch')));
		
		
		//формирую основые переменные
		$id_gate=Model_cvss::getGateFromBoxIp(Arr::get($post, 'ip'), Arr::get($post, 'ch'));
		
		$identifier=hexdec(Arr::get($post, 'key'));
		
		Log::instance()->add(Log::NOTICE, '160 :key Получены данные 
				ch = :ch, 
				ip=:ip, 
				key= ":key" (:keyDec), 
				gate :gate, 
				isEnter=:isEnter
				',
					array( 
						':ip'=>Arr::get($post, 'ip'),
						':ch'=>Arr::get($post, 'ch'),
						':key'=>Arr::get($post, 'key'),
						':keyDec'=>hexdec(Arr::get($post, 'key')),
						':gate'=>$id_gate,
						
						)); 
	
						
						
		
		//переход к основной обработке
		
		Model::factory('cvss')->common( $identifier,  $id_gate);
		
		
		
	}
	
	
	
	public function action_exec()
	{
		$t1=microtime(1);
		Log::instance()->add(Log::NOTICE, '/r/n668 start CVS action_exec');
		//Log::instance()->add(Log::NOTICE, '665 debug :data', array(':data'=>(microtime(true) - $t1)));
		$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
			
		$input_data=Arr::get($input_data_0, 'plate');
		//Log::instance()->add(Log::NOTICE, '676 debug :data', array(':data'=>Debug::vars($input_data)));
		
		//Валидация данных: все ли правильно?
		//$post=Validation::factory($input_data);
		
		if (is_array($input_data)) {
				$post = Validation::factory($input_data);
		} else {
			
			Log::instance()->add(Log::NOTICE, '222 Входные данные cvs не являются массивом '. Debug::vars($input_data)); //exit;//вывод номера в лог-файл
			exit;
			
		}
		

		
		$post->rule('id', 'not_empty')//номер события
					->rule('id', 'digit')
					->rule('id', 'Model_cvss::isEventUniq') //событие уникальное, не совпадает с ранее обработанным
					->rule('camera', 'not_empty')//номер видеокамеры
					->rule('camera', 'digit')
					->rule('camera', 'Model_cvss::checkCamIsPresent') 
					->rule('plate', 'not_empty')//значение ГРЗ
					->rule('plate', 'regex', array(':value', '/^[A-Za-z\d]{3,10}+$/')) // https://regex101.com/ строго буквы АНГЛ алфавита
					;
		
		if(!$post->check())
		{
			
			Log::instance()->add(Log::NOTICE, '125 Входные данные cvs не полные '. Debug::vars($post->errors())); //exit;//вывод номера в лог-файл
			exit;
		}
		//формирую основные переменные
		$id_gate = Model::factory('mpt')->getIdGateFromCam(Arr::get($input_data, 'camera'));//получил номер ворот

		$identifier=Arr::get($input_data, 'plate');
		Log::instance()->add(Log::NOTICE, '999-240 validate CVS :plate total_time  :data', array(':data'=>(microtime(true) - $t1), ':plate'=>$identifier));
		
		
		$_data=array(':id_gate'=>$id_gate, 
							':cam'=>Arr::get($input_data, 'camera'),
							':cam_darect'=>Arr::get($input_data, 'direction'),
							':grz'=>Arr::get($input_data, 'plate')
							);
		Log::instance()->add(Log::NOTICE, '608 cvs exec получил данные '. Debug::vars($_data)); 
					
		
		//собрал нужные данные и передаю их в модуль управления логикой
		//$this->common( $identifier,  $cvs);
		//Model::factory('cvss')->common( $identifier,  $cvs);
		Model::factory('cvss')->common( $identifier,  $id_gate);
	
		Log::instance()->add(Log::NOTICE, '999-256 stop CVS :plate total_time  :data', array(':data'=>(microtime(true) - $t1), ':plate'=>$identifier));
		
	}
	
	/**20.07.2025 Открытие ворот по http команде
	*
	*
	*/
	public function action_opengate()
	{
		
		
		//$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		Log::instance()->add(Log::NOTICE, '489 '. Debug::vars($_POST));
		//Log::instance()->add(Log::NOTICE, '490 '. Debug::vars($input_data_0));
		$id_gate=Arr::get($_POST, 'id');
		
				
		//$id_gate=Arr::get($_POST, 'id');
		//$id_gate=Arr::get($input_data_0, 'id');
		
		
		$cvs=new phpCVS($id_gate);// сделал экземпляр, чтобы получить IP, port, и номер канала ch
		
		$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);//создаю экземпляр контроллера МПТ
		$result=$mpt->openGate($cvs->ch);// даю команду открыть ворота. Результат может быть разный: от  подключения к контроллера до ошибки в протоколе.
		
		Mqtt_Publisher::instance()->gate($cvs, 'opendoor', array(
			'mode'   => (int) $cvs->ch,
			'doors'  => array((int) $cvs->ch),
			'source' => 'operator',
			'debug'  => FALSE,
		));

		Log::instance()->add(Log::NOTICE, '499 открыл ворота id=:id ip=:ip port=:port ch=:ch', array(':id'=>$id_gate, ':ip'=>$cvs->box_ip, ':port'=>$cvs->box_port, ':ch'=>$cvs->ch));
		Log::instance()->add(Log::NOTICE, '414 результат выполнения команды открытия ворот :res', array(':res'=>Debug::vars($result)));
		//надо вернуть ответ
		$this->response
            ->headers('Content-Type', 'application/json')
            ->body(json_encode($result));
		
		//послать команду Открыть дверь.
		
	}
		
		
	/**13.10.2025 Открытие ворот по команде от бота
	*
	*
	*/
	public function action_opengateBot()
	{
		
		
		/* //$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		Log::instance()->add(Log::NOTICE, '489 '. Debug::vars($_POST));
		//Log::instance()->add(Log::NOTICE, '490 '. Debug::vars($input_data_0));
		$id_gate=Arr::get($_POST, 'id'); */
		
		$input_data_0=json_decode(file_get_contents('php://input'), true);//извлекаю данных из полученного пакета
		Log::instance()->add(Log::NOTICE, '225 action_opengate получил '. Debug::vars($_POST));
		//Log::instance()->add(Log::NOTICE, '226 action_opengate получил '. Debug::vars(file_get_contents('php://input'), true));
		Log::instance()->add(Log::NOTICE, '490 -0'. Debug::vars($input_data_0));
		
		//$id_gate=Arr::get($_POST, 'id');
		$id_gate=Arr::get($input_data_0, 'id');
		
		
		
			//$eventCode=Events::LOOP1; $events->eventCode=$result;
			$events= new Events();
			$events->id_gate=$id_gate;
			$events->eventCode=Events::evOpenDoorOperator;
			$events->addEventRow();
					
					
		$cvs=new phpCVS($id_gate);// сделал экземпляр, чтобы получить IP, port, и номер канала ch
		
		$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);//создаю экземпляр контроллера МПТ
		$result=$mpt->openGate($cvs->ch);// даю команду открыть ворота. Результат может быть разный: от  подключения к контроллера до ошибки в протоколе.
		
		Mqtt_Publisher::instance()->gate($cvs, 'opendoor', array(
			'mode'   => (int) $cvs->ch,
			'doors'  => array((int) $cvs->ch),
			'source' => 'bot',
			'debug'  => FALSE,
		));

		Log::instance()->add(Log::NOTICE, '333 команда от бота открыть ворота id=:id ip=:ip port=:port ch=:ch', array(':id'=>$id_gate, ':ip'=>$cvs->box_ip, ':port'=>$cvs->box_port, ':ch'=>$cvs->ch));
		//Log::instance()->add(Log::NOTICE, '334 результат выполнения команды открытия ворот :res', array(':res'=>Debug::vars($result)));
		Log::instance()->add(Log::NOTICE, '335 результат выполнения команды открытия ворот :res', array(':res'=>$result->result));
		if($result->result === 'OK') {
			$events->eventCode=Events::evOpenDoorOperatorOk;
		} else {
			$events->eventCode=Events::evOpenDoorOperatorErr;
		}
		$events->addEventRow();
		
		//надо вернуть ответ
		$this->response
            ->headers('Content-Type', 'application/json')
            ->body(json_encode($result));
		
		//послать команду Открыть дверь.
	}
		
		
		/**20.10.2025 ping pong
	*
	*
	*/
	public function action_checkBot(){
		
		Log::instance()->add(Log::NOTICE, "342 action_checkBot");
		
	}
		
	/**20.10.2025 Получение id и name всех ворот
	*
	*
	*/
	public function action_getGatesList()
	{
		Log::instance()->add(Log::NOTICE, "340 action_getGatesList");
		
		
		$result=Model::factory('cvss')->getGatesList();
		//echo Debug::vars('358', $result);//exit;
		//echo Debug::vars('358', json_encode($result));exit;
		
		$this->response
            ->body(json_encode($result));

	}					
		
	public function after()
    {
      // Cache::instance()->garbage_collect();
    }
		
}
