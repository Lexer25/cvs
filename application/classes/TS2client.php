<?php
/**
 * Класс WebSocket сервера
 https://tokmakov.msk.ru/blog/item/202
 */
class TS2client {

    /**
     * Функция вызывается, когда получено сообщение от клиента
     */
    public $handler;

    /**
     * IP адрес сервера
     */
    private $ip;
    /**
     * Порт сервера
     */
    private $port;
    /**
     * Сокет для принятия новых соединений, прослушивает указанный порт
     */
    private $connection;
	
	
	/**
     * Сокет подключен и готов передавать команды 3.11.2021 Бухаров
     */
    private $connReady;
	
	
	
    /**
     * Для хранения всех подключений, принятых слушающим сокетом
     */
    private $connects;

    /**
     * Ограничение по времени работы клиента
     */
    private $timeLimit = 0;
    /**
     * Время начала работы клиента
     */
    private $startTime;
    /**
     * Выводить сообщения в консоль?
     */
    private $verbose = false;
    /**
     * Записывать сообщения в log-файл?
     */
    private $logging = false;
    /**
     * Имя log-файла
     */
    private $logFile = 'ws-log.txt';
    /**
     * Ресурс log-файла
     */
    private $resource;
	
	

    public function __construct($ip = '127.0.0.1', $port = 1967) {
        $this->ip = $ip;
        $this->port = $port;

    }

    public function __destruct() {
        if (is_resource($this->connection)) {
            $this->stopClient();
        }
        if ($this->logging) {
            fclose($this->resource);
        }
    }

    /**
     * Дополнительные настройки для отладки
     */
    public function settings($timeLimit = 0, $verbose = false, $logging = false, $logFile = 'ws-log.txt') {
        $this->timeLimit = $timeLimit;
        $this->verbose = $verbose;
        $this->logging = $logging;
        $this->logFile = $logFile;
        if ($this->logging) {
            $this->resource = fopen($this->logFile, 'a');
        }
    }

    /**
     * Выводит сообщение в консоль и/или записывает в лог-файл
     */
    private function debug($message) {
        $message = '[' . date('r') . '] ' . $message . PHP_EOL;
        if ($this->verbose) {
            echo $message;
        }
        if ($this->logging) {
            fwrite($this->resource, $message);
        }
    }

 
	/*
	*отправка сообщения
	*/
	public function sendMessage($command)
	{
		if ($this->logging) Log::instance()->add(Log::DEBUG,'Стр. 121. Вызов функции sendMessage '. $this->connection.','. $this->connReady);
			
		 if (true === $this->connReady) {
			 //$login_mes = 'r51 login name="3", password="35"';
				$reply=socket_write($this->connection, $command."\r\n", strlen($command."\r\n"));
				//получаем ответ
				           
        } else {
			$reply='No connection TS2.';
		}
		
		 return $reply;
	}
	
	/*
	*чтение данных из сокета для получения ответа на команду
	*/	
	public function readMessage()
	{
		//Log::instance()->add(Log::DEBUG,'Стр. 121. Вызов функции readMessage '. $this->connection.','. $this->connReady);
			
		 if (true === $this->connReady) {
			 //надо знать, что тут программа будет стоять до тех пор, пока что-то не получит из сокета
			 $reply=socket_read($this->connection,4096);
			 
           
        } else {
			$reply='No connection TS2.';
		}
		
		 return $reply;
	}
	
	
	

    /**
     * Запускает клиента в работу
     */
    public function startServer() {

        
		$this->debug('Try start server...');
		if ($this->logging) Log::instance()->add(Log::DEBUG, 'Try start server...');
					

        $this->connection = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (false === $this->connection) {
            $this->debug('Error socket_create(): ' . socket_strerror(socket_last_error()));
			if ($this->logging) Log::instance()->add(Log::DEBUG, "Line 147.Couldn't create socket, error code is: " . socket_last_error().", error message is: " . socket_strerror(socket_last_error()));
					
            return;
        }

        // подключаюсь к ТС2
        $this->connReady = @socket_connect($this->connection, $this->ip, $this->port); // слушаем сокет
        
		if (false === $this->connReady) {
            $this->debug('Error socket_listen(): ' . socket_strerror(socket_last_error()));
			if ($this->logging) Log::instance()->add(Log::DEBUG, 'Стр. 170. Не могу выполнить socket_connect.');
					
            return;
        }

       
        $this->connects = array($this->connection);// а вот где-то тут можно указать время ожидания ответов и т.п.
        $this->startTime = time();
		socket_read($this->connection,4096);
        while (true) {

           

            // если истекло ограничение по времени, останавливаем сервер
            if ($this->timeLimit && time() - $this->startTime > $this->timeLimit) {
                $this->debug('Time limit. Stopping server.');
				if ($this->logging) Log::instance()->add(Log::DEBUG, 'Стр. 188. Time limit. Stopping server!' .$this->timeLimit);
       
                $this->stopClient();
                return;
            }
			
			// if (time() - $this->startTime > 20) {
                // Log::instance()->add(Log::DEBUG, 'Стр. 195. Кручусь в бесконечном цикле.');
                return;
            // }

        }

    }

    /**
     * Останавливает работу клиента
     */
    public function stopClient() {
              
        socket_close($this->connection);
        if (!empty($this->connects)) { // отправляем все клиентам сообщение о разрыве соединения
            foreach ($this->connects as $connect) {
                if (is_resource($connect)) {
                    socket_write($connect, self::encode('  Closed on server demand', 'close'));
                    socket_shutdown($connect);
                    socket_close($connect);
                }
            }
        }
    }


   

}