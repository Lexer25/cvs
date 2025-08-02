    <?php defined('SYSPATH') or die('No direct script access.');
 
    class Task_CheckGRZ extends Minion_Task {
		
		    protected $_options = array(
        // param name => default value
        'name'   => 'World',
        'delay'   => '30',
		);
	
        
        protected function _execute(array $params)
        {
            //Minion_CLI::write('Hello World!');
		//$dateFrom=Date::formatted_time(\''.Arr::get($params, 'delay', 30).' minutes ago');
		
		$dateFrom=Date::formatted_time(Arr::get($params, 'delay', 30).' minutes ago');
		$dateTo=Date::formatted_time();
		//$dateTo=date("Y-m-d H:i:s");		
			
	
					
		$sql='SELECT count(grz) as countGRZ,count(id) as countID
  FROM [KalibrParking].[dbo].[Events]
  where EventTime>\''.$dateFrom.'\'
  and EventTime<\''.$dateTo.'\'
  and EventCode in (513, 514, 517)';
  
  //$sql='SELECT count(grz) as countGRZ,count(id) as countID   FROM [KalibrParking].[dbo].[Events]   where EventTime>\'2020-10-23 13:30:00\'   and  EventTime<\'2020-10-23 14:00:00\'   and EventCode in (513, 514, 517)';

					
		$query = DB::query(Database::SELECT, DB::expr($sql))
			->execute(Database::instance('parking1'))
			->as_array();
			
			$countGRZ=$query[0]['countGRZ'];
			$countID= $query[0]['countID'];
			
		//$countID = 20;
		//$countGRZ = 0;
		
		$log = Log::instance();
		$log->add(log::INFO, $sql);		
		$log->add(log::INFO, 'Получено из базы данных: всего проездов  '.$countID.', получено ГРЗ  '.$countGRZ );	
		
		
		
		if(	$countID>3 and $countGRZ == 0)
			{
				$log->add(log::INFO, 'E-mail is send.' );
				$answer='Авария Калибр ГРЗ. Всего проездов '.$countID.', ГРЗ  '.$countGRZ. ' за '.$dateFrom.' - '.$dateTo;	
				
//echo Debug::vars('28',$answer); exit;	
		$mailer = Email::factory();

		//echo Debug::vars('38', $mailer); //exit;
		$mailer
		  ->to('it@artsec.ru', 'IT')
		  ->to('b71@mail.ru', 'b71')
		  ->from('support@artonit.ru', 'Калибр')
		  ->subject($answer)
		  ->html('<i>'.$answer.'</i>')
		  ->send();				
				
			} else {
				
				$log->add(log::INFO, 'E-mail is not send.' );
				
			}
		
		
        }
    }