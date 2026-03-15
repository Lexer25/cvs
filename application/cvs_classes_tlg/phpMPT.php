<?php



/* phpMPT */

class phpMPT
{
    protected $socket;            /* holds the socket	*/
    public $address;            /* broker address */
    public $port;                /* broker port */
    private $connection;            /* stores connection */
    public $result;            /* результат выполнения команды */
    public $answer;            /* ответ на команду (если он должен быть) */
    public $command;            /* команда для выполнения */
    public $commandParam;            /* параметры команды */
    public $coordinate;            /* координаты вывода строки на табло */
    public $binCommand;            /* команда для выполнения */
    public $codeCommand;            /* код команды контроллера */
    public $udp_delay = 500000;            /* задержка при получении ответа UDP */
   

    

 
    public function __construct($address, $port)
    {
        $this->address = $address;
        $this->port = $port;
		$this->udp_delay=500000;
    }


   
    public function connect()
    {
     //открываю сокет UDP 
	
	 
	  $this->socket= @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	  socket_set_option($this->socket,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>1, "usec"=>0));
		if (false === $this->socket) { 
			//die("Couldn't create socket, error code is: " . iconv('windows-1251','UTF-8',socket_last_error()) . ",error message is: " . iconv('windows-1251','UTF-8', socket_strerror(socket_last_error())));
			//Log::instance()->add(Log::NOTICE, "44 Couldn't create socket, error code is: " . iconv('windows-1251','UTF-8',socket_last_error()) . ",error message is: " . iconv('windows-1251','UTF-8', socket_strerror(socket_last_error())));
			exit;
			} else {
				
				//Log::instance()->add(Log::NOTICE, "48 Socket создан успешно"); 	
			};
		
	
		// создаем соединение 
		$this->connection = @socket_connect($this->socket, $this->address, $this->port);
		
		if ($this->connection === false)      
		{
			Log::instance()->add(Log::NOTICE, "55 Cannot connect to server".$this->address.":". $this->port);
			exit;
		} else {
			//Log::instance()->add(Log::NOTICE, "60 OK connect to server".$this->address.":". $this->port);
			
		}
   

        return true;
    }

   

    /**
     * Sends a proper disconnect, then closes the socket
     */
    public function close()// при завершении работы возможно придется что-то куда-то выводить, поэтому перед вызовом disconnect можно сделать еще что-нибудь
    {
        $this->disconnect();
        
    }

	/**
     * отправка пакета данных в контроллера
    
     */
	
	
    public function sendCommand($command)
    {
        //echo Debug::vars('69',unpack('C*',$this->binCommand),  unpack('C*', $command));
		if($this->connection === true)
		{
			try
			{
				$byte_send=socket_write($this->socket, $command, strlen($command));
				Log::instance()->add(Log::NOTICE, '82-phpMPT-UDP-sendCommand '.$this->address.':'.$this->port.' timestamp '.microtime(true).' отправлен UDP пакет '.implode (",", unpack("C*",$command)).', ответ '.$byte_send);
				// ждать 500 миллисекунды
				usleep($this->udp_delay);
				$reply = socket_read($this->socket,4096);
				Log::instance()->add(Log::NOTICE, '86-phpMPT-UDP-recievCommand '.$this->address.':'.$this->port.' timestamp '.microtime(true).' получен ответ  '.implode (",", unpack("C*",$reply)));
			
				} catch  (Exception $e) {
					//Log::instance()->add(Log::NOTICE, '91-MPT-checkAnswer '.Debug::vars('180', $e).' '.Debug::vars('92', iconv('windows-1251','UTF-8', $e->getMessage())));
					Log::instance()->add(Log::NOTICE, '91-MPT-checkAnswer-exception timestamp '.microtime(true).Debug::vars('92', iconv('windows-1251','UTF-8', $e->getMessage())));
					$this->result='Err';
					$this->edesc=$e->getMessage();
					
					return;	
				}
			
		} else {
				$reply ='No connection';
				
			}
			
			return $reply;
    }
	
	public function disconnect()
    {
        socket_close($this->socket);
		//Log::instance()->add(Log::NOTICE, "121 Socket закрыт");
    }
	
	/*
	расчет контрольной суммы по строке/массиву
	
	
	*/
	
	
	public function bcc($data, $from, $to)
	{
		
		$bcc=pack('C', 0);
		//echo Debug::vars('98 start bcc',  unpack("C*",$data), $from, $to, unpack("C*",$bcc));
		for($i=$from; $i<$to-1; $i++)
		{
			//echo Debug::vars('97', $i, unpack("C",$bcc), unpack("C",$data[$i]));
			$bcc=$bcc^$data[$i];
		}
		//echo Debug::vars('105', unpack("C",$bcc));
		return $bcc;
	}

	/*
			функция из текстовых команд формирует бинарный набор (включая в себя длину команды и BCC)
		
		$command - текст команды	
		$this->commandParam - дополнительные параметры команды 
		$this->coordinate - дополнительные параметры команды	
	
	*/
	public function make_binary_command($command)
	{
		
		$known_commands = array(
        'GetVersion'=>"\x56", 
		'opendoor'=>"\x4F",
		'opendoor_all'=>"\x10",
		'text'=>"\x46",
		'clearTablo'=>"\x44",
		'scrolText'=>"\x4A",
		);
		
		//echo Debug::vars('115 команда в контроллер', $command, $this->commandParam, $this->coordinate, Arr::get(unpack('c*', (Arr::get($known_commands, $command))), 1));
		$this->codeCommand=Arr::get(unpack('c*', (Arr::get($known_commands, $command))), 1);//запоминаю команду для последующего сравнения
		
		$ttr=Arr::get($known_commands, $this->command).$this->coordinate.$this->commandParam;// сборка команды с параметрами, без длины и BCC
		$lenCommad=pack('c', strlen($ttr)+2);// длина команды в формате binary
		$_bcc=$this->bcc($lenCommad.$ttr, 0, strlen($ttr)+2); // получение bcc по всей команде
	
		return $lenCommad.$ttr.$_bcc;
	}
	
	/*
	Отправка подготовленного UDP пакета
	*/
	
	public function execute()// выполнение команды $this->command  
	{
		$this->connect();
		$_command=$this->make_binary_command($this->command);
		$_answer=$this->sendCommand($_command);
		//Log::instance()->add(Log::NOTICE, '161-phpMPT-execute timestamp '.microtime(true).' отправлен UDP пакет '.implode (",", unpack("C*",$_command)));
		//Log::instance()->add(Log::NOTICE, '163-phpMPT-execute '.microtime(true).' получен ответ  '.implode (",", unpack("C*",$_answer)));
		$this->checkAnswer($_answer);//заполняют свойства result и answer
		$this->close();
		
		return;
		
	}
	
	
	
	public function checkAnswer($data)
	{
		//echo Debug::vars('173-MPT ответ на команду', unpack("C*",$data)); 
		$_lenData=strlen($data);
		$_lenDEC=Arr::get(unpack('c*', $data), 1);
		$_commandRepeatDEC=Arr::get(unpack('c*', $data), 3);
		$_resultDEC=Arr::get(unpack('c*', $data), 4);
		$_bccDEC=Arr::get(unpack('c*', $data), $_lenData);
		 //echo Debug::vars('155 десятичный номер команды в ответе', $_commandRepeatDEC, $this->command, 'Длина ответа '. $_lenData);
		//Log::instance()->add(Log::NOTICE, '180-MPT-checkAnswer '.Debug::vars('180',unpack("C*",$data), implode (",", unpack("C*",$data)), $_lenData, $_lenDEC, $_commandRepeatDEC, $_resultDEC, $_bccDEC ));
		
		for($i=3; $i<$_lenData; $i++)
		{
			$_data[]=$data[$i];
		}
		
		if($_lenData==0)// нет ответа из сокета
		{
			$this->result='Err';
			$this->edesc='Ответ не получен или длина пакета 0';
		}
		elseif ($_lenDEC<3) // ответ не может быть меньше 3 байт.
			{
				$this->result='Err';
				$this->edesc='Длина пакета меньше 3 байт.';
				
			}
		
		elseif($_lenData>255)// проверка длины пакета
		{
			$this->result='Err';
			$this->edesc='Packet UDP more then 255 byte';
		} elseif ($_lenDEC<>$_lenData) // проверка совпадения заявленной и реальной длины пакета.
			{
				$this->result='Err';
				$this->edesc='Длина пакета '.$_lenData.' не совпадает с указанной длиной '.$_lenDEC;
				
			} elseif (Arr::get(unpack('C',$this->bcc($data, 0, $_lenDEC)), 1) <>$_bccDEC)// проверка контрольной суммы пакета.
				{
					$this->result='Err';
					$this->edesc='Неправильная контрольная сумма ответа. В ответе BCC='.$_bccDEC.', рассчитанная BCC='.Arr::get(unpack('C',$this->bcc($data, 0, $_lenDEC)), 1);
					
				} 
					elseif ($_resultDEC<>0)// проверка успешно ли выполнена команда
					{
						$err_mess=array 
						('1'=>'Ошибка контрольной суммы, команда не выполнена',
						'2'=>'Ошибка длины, команда не выполнена',
						'3'=>'Ошибка, неизвестная команда',
						'4'=>'Ошибка подключенного оборудования'
						);

						
						$this->result='Err';
						$this->edesc='Команда выполнена с ошибкой '.$_resultDEC.' ('.Arr::get($err_mess, $_resultDEC).')';
						
						
					} 
					elseif ($_commandRepeatDEC<>$this->codeCommand)
						{
							$this->result='Err';
							$this->edesc='Получен ответ '.$_commandRepeatDEC.' на команду'. $this->codeCommand;
							
							
						} else 
						{
							$this->result='OK';
							$this->edesc='OK';
							$_res='';
							for($i=4; $i<$_lenDEC-1; $i++)
							{
								//echo Debug::vars('97', $i, unpack("C",$bcc), unpack("C",$data[$i]));
								$_res=$_res.$data[$i];
							}
							$this->answer=$_res;
						}
		//echo Debug::vars('216', $this->result, $this->edesc,$this->answer ); 
		//Log::instance()->add(Log::NOTICE, '246-phpMPT-checkAnswer result:'. $this->result.', edesc:'.$this->edesc.', answer:'.implode(",",unpack("C",$data)));
		return;
		
	}
	
	public function sendtext($mess, $param)//вывод сообщения на табло
	{
		$this->command='opendoor door=0';
		$this->execute();
		return;
	}
   
   public function openGate($mode)// открытие ворот с учетом режима работы
	{
		//echo Debug::vars('241', $mode ); exit;
		if($mode ==0)
		{
			$this->command='opendoor';
			$this->commandParam="\x00";
			$this->execute();
			
			/* $this->command='opendoor';
			$this->commandParam="\x01";
			$this->execute(); */
		}
		
		if($mode ==1)
		{
			/* $this->command='opendoor';
			$this->commandParam="\x00";
			$this->execute(); */
			
			$this->command='opendoor';
			$this->commandParam="\x01";
			$this->execute();
		}
		
		
		if($mode ==2)
		{
			$this->command='opendoor';
			$this->commandParam="\x00";
			$this->execute();
			
			$this->command='opendoor';
			$this->commandParam="\x01";
			$this->execute();
		}
		
		
		if($mode ==3)
		{
			$this->command='opendoor_all';
			$this->commandParam="\x00";
			$this->execute();
			
			$this->command='opendoor';
			$this->commandParam="\x01";
			$this->execute();
		}
		
		
		
		
		
		return;
	}
   
   
   
}
