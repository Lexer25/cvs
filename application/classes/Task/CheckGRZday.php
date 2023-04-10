    <?php defined('SYSPATH') or die('No direct script access.');
 
    class Task_CheckGRZday extends Minion_Task {
		//отчет за текущие сутки
		
		    protected $_options = array(
        // param name => default value
        'name'   => 'World',
        'delay'   => '30',
		);
	
        
        protected function _execute(array $params)
        {
            //Minion_CLI::write('Hello World!');
		//$dateFrom=Date::formatted_time(\''.Arr::get($params, 'delay', 30).' minutes ago');
		
		//$dateFrom=Date::formatted_time('1 day ago', 'Y-m-d');
		$dateFrom=Date::formatted_time('now', 'Y-m-d');
		$dateTo=Date::formatted_time('now', 'Y-m-d H:i:s');
		//$dateTo=date("Y-m-d H:i:s");		
			
	
					
		$sql='SELECT count(grz) as countGRZ,count(id) as countID
  FROM [KalibrParking].[dbo].[Events]
  where EventTime>\''.$dateFrom.'\'
  and EventTime<\''.$dateTo.'\'
  and EventCode in (513, 514, 517)';

					
		if($query = DB::query(Database::SELECT, DB::expr($sql))
			->execute(Database::instance('parking1'))
			->as_array())
			{
				$countGRZ=$query[0]['countGRZ'];
				$countID= $query[0]['countID'];
				$log = Log::instance();	
				$log->add(log::INFO, 'Получено из базы данных отчет за период '.$dateFrom.'-'.$dateTo.', всего проездов за сутки '.$countID.', получено ГРЗ за сутки '.$countGRZ );
				$answer='Калибр контроль ГРЗ. Всего проездов  '.$countID.', получено ГРЗ  '.$countGRZ.'. K='.$countGRZ/$countID;	
					
				$countErrors =	count($query);
			} else {
				$answer=__('No errors');
				$countErrors =	0;
			}
		//echo Debug::vars('28',$sql); exit;	
		$mailer = Email::factory();

		//echo Debug::vars('38', $mailer); //exit;
		$mailer
		  ->to('it@artsec.ru', 'IT')
		  ->to('b71@mail.ru', 'b71')
		  ->from('support@artonit.ru', 'Калибр')
		  ->subject('Калибр ГРЗ за сутки. Проездов  '.$countID.', ГРЗ  '.$countGRZ. ' за '.$dateFrom.'-'.$dateTo)
		  ->html('<i>'.$answer.'</i>')
		  ->send();
		
        }
    }