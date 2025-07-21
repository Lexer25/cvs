<?php defined('SYSPATH') or die('No direct script access.');


/**
* @package    phpTablo
 * @category   Base
 * @author     Artonit
 * @copyright  (c) 2025 Artonit Team
 * @license    http://artonit/ru 
 
 */
class phpTablo extends phpMPTtcp{
	
	
	 public $isEnabled = false;            
	
	 public function __construct($address, $port)
    {
        
		if($address != '') 
		{
			$this->isEnabled= true;
			
		
		$this->address = $address;
        $this->port = $port;
		
		} else {
			
			$this->result='Device disabled';
			$this->edesc='Device disabled';
		}
		
		//Log::instance()->add(Log::NOTICE, '23 '. Debug::vars($this)); exit;
    }
	
	public function execute()// выполнение команды $this->command  
	{
		
		
		if($this->isEnabled)
		{
			$this->connect();
			$_command=$this->make_binary_command($this->command);
			$_answer=$this->sendCommand($_command);
		
			$this->checkAnswer($_answer);//заполняют свойства result и answer
			$this->close();
		}
		return;
		
	}
	
}
