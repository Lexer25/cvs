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
    public $binCommand;            /* команда для выполнения */
    public $codeCommand;            /* код команды контроллера */
   

    

 
    public function __construct($address, $port)
    {
        $this->address = $address;
        $this->port = $port;
    }


   
    public function connect()
    {
     //открываю сокет UDP 
	  $this->socket= @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if (false == $this->socket) { 
			die("Couldn't create socket, error code is: " . iconv('windows-1251','UTF-8',socket_last_error()) .
			",error message is: " . iconv('windows-1251','UTF-8', socket_strerror(socket_last_error())));
			};
		
	
		// создаем соединение 
		$this->connection = @socket_connect($this->socket, $this->address, $this->port);
		if ($this->connection === false)      die("Cannot connect to server".$server.":". $port);
   

        return true;
    }

   

    /**
     * Sends a proper disconnect, then closes the socket
     */
    public function close()// при завершении работы возможно придется что-то куда-то выводить, поэтому перед вызовом disconnect можно сделать еще что-нибудь
    {
        $this->disconnect();
        //stream_socket_shutdown($this->socket, STREAM_SHUT_WR);
    }

	/**
     * отправка пакета данных в контроллера
     * @param command $loop
     *
     * @return bool | string
     */
	
	
    public function sendCommand($command)
    {
        //echo Debug::vars('69',unpack('C*',$this->binCommand),  unpack('C*', $command));
		if($this->connection === true)
		{
			socket_write($this->socket, $command, strlen($command));
			$reply = socket_read($this->socket,4096);
			
		} else {
				$reply ='No connection';
				
			}
			
			return $reply;
    }
	
	public function disconnect()
    {
        socket_close($this->socket);
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
	
	*/
	public function make_binary_command($command)
	{
		echo Debug::vars('115', $command);
		$known_commands = array(
        'GetVersion'=>array(0x56), 
		'opendoor door=0'=>array(0x4F, 0x00),
		'opendoor door=1'=>array(0x4F, 0x01),
		);
		$this->codeCommand=Arr::get(Arr::get($known_commands, $this->command), 0);
		$commandLen = count(Arr::get($known_commands, $this->command))+2; // длина команды
		$result=pack('c', $commandLen);// сформировал первый байт команды (длина)
		foreach (Arr::get($known_commands, $this->command) as $key)
		{
			
			$result=$result.pack('c', $key);// собираю команду как набор бинарных данных
		}
		
		$_bcc=$this->bcc($result, 0, $commandLen); // получение bcc по всей команде
			
		return $result.$_bcc;;
	}
	
	
	public function execute()// выполнение команды $this->command
	{
		$this->connect();
		$_answer=$this->sendCommand($this->make_binary_command($this->command));
		$this->checkAnswer($_answer);//заполняют свойства result и answer
		$this->close();
		
		return;
		
	}
	
	public function checkAnswer($data)
	{
		echo Debug::vars('140', unpack("C*",$data)); 
		$_lenData=strlen($data);
		$_lenDEC=Arr::get(unpack('c*', $data), 1);
		$_commandRepeatDEC=Arr::get(unpack('c*', $data), 3);
		$_resultDEC=Arr::get(unpack('c*', $data), 2);
		$_bccDEC=Arr::get(unpack('c*', $data), $_lenData);
		echo Debug::vars('155', $_commandRepeatDEC, $this->command);
		
		for($i=3; $i<$_lenData; $i++)
		{
			$_data[]=$data[$i];
		}
		
		if($_lenData>255)// проверка длины пакета
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
						$this->edesc='Команда выполнена с ошибкой '.$_resultDEC.' ('.Arr::get($err_messm, $_resultDEC).')';
						
						
					} 
					elseif ($_commandRepeatDEC<>$this->codeCommand)
						{
							$this->result='Err';
							$this->edesc='Получен ответ '.$_commandRepeatDEC.' на команду'. $this->codeCommand;
							
							
						} else 
						{
							$this->result='OK';
							$this->edesc='';
							$_res='';
							for($i=4; $i<$_lenDEC-1; $i++)
							{
								//echo Debug::vars('97', $i, unpack("C",$bcc), unpack("C",$data[$i]));
								$_res=$_res.$data[$i];
							}
							$this->answer=$_res;
						}

		return;
		
	}
	
	public function sendtext($mess, $param)//вывод сообщения на табло
	{
		
		return;
	}
   
   public function openGate($mode)// открытие ворот с учетом режима работы
	{
		if($mode ==0)
		{
			$this->command='opendoor door=0';
			$this->execute();
			
			$this->command='opendoor door=1';
			$this->execute();
		}
		
		if($mode ==1)
		{
			$this->command='opendoor door=0';
			$this->execute();
			
			$this->command='opendoor door=1';
			$this->execute();
		}
		
		
		if($mode ==2)
		{
			$this->command='opendoor door=0';
			$this->execute();
			
			$this->command='opendoor door=1';
			$this->execute();
		}
		
		
		if($mode ==3)
		{
			$this->command='opendoor door=0';
			$this->execute();
			
			$this->command='opendoor door=1';
			$this->execute();
		}
		
		
		
		
		
		return;
	}
   
   
   
}
