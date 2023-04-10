    <?php defined('SYSPATH') or die('No direct script access.');
 
    class Task_SendErrors extends Minion_Task {
		
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
			
		$sql='SELECT ev.[EventCode]
      ,ev.[EventTime]
      ,ev.[EventUser]
      ,ev.[CardID]
      ,ev.[GateID]
      ,ev.[ErrorLevel]
	  ,ev.[Comment]
      ,ev.[GRZ]
	  ,ec.EventName
					FROM [KalibrParking].[dbo].[Events] ev
					join EventCodes ec on ec.EventCode=ev.EventCode
					where EventTime>\''.$dateFrom.'\'
					and ev.EventCode in (516, 772, 773, 1281, 1284, 1285, 1286, 1287, 1288, 1289, 1290)';
					
		if($query = DB::query(Database::SELECT, DB::expr($sql))
			->execute(Database::instance('parking1')))
			{
				
				$answer=View::Factory('ErrorsListForMail', array(
					'dataForSend' => $query,
					'dateFrom' => $dateFrom,
					'dateTo' => $dateTo,
					//'dataForSend' => '',
					));
				$countErrors =	count($query);
			} else {
				$answer=__('No errors');
				$countErrors =	0;
			}
		//echo Debug::vars('28',$answer); exit;	
		$mailer = Email::factory();

		//echo Debug::vars('38', $mailer); //exit;
		$mailer
		  ->to('b71@mail.ru', 'Получатель')
		  ->from('support@artonit.ru', 'Калибр')
		  ->subject('Калибр Количество ошибок '.$countErrors.', '.$dateFrom.'-'.$dateTo)
		  ->html('<i>'.$answer.'</i>')
		  ->send();
		
        }
    }