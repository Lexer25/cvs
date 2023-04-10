    <?php defined('SYSPATH') or die('No direct script access.');
 
    class Task_ReloadRestoreConnect extends Minion_Task {
		
		    protected $_options = array(
        // param name => default value
        'name'   => 'World',
        'delay'   => '30',
        'attempts'   => '0',
        'maxAttempts'   => '2',
		);
	
        
        protected function _execute(array $params)
        {
            //Minion_CLI::write('Hello World!');
		//$dateFrom=Date::formatted_time(\''.Arr::get($params, 'delay', 30).' minutes ago');
		$log = Log::instance();
		
		//Эти строки сделаны для тестирования процесса обращения к ТС2.
		/* $TS2client = new TS2client('172.19.1.17', 1967);
		$TS2client->settings(10, true);
		$TS2client->startServer();
		
		$TS2client->sendMessage('r51 login name="3", password="35"');// exit;
		$TS2client->readMessage();// exit;
		 $TS2client->sendMessage('t45 ver');// exit;
		 $log->add(log::INFO,'99 '. $TS2client->readMessage());
		 $TS2client->sendMessage('t49 enumdevices');// exit;
		$log->add(log::INFO,'100 '. iconv('windows-1251','UTF-8',$TS2client->readMessage()));
		$TS2client->stopClient();
		 $log->add(log::INFO,'102 '. 'Завершение опроса. Клиент TS2 остановлен.'); */
		
		
		//обновление количества попыток для контроллеров, которые появились на связи.			
		$sql='update cardindev cd2
			set cd2.attempts='. Arr::get($params, 'attempts', 0).'
			where cd2.id_dev in (
			select distinct cid.id_dev from cardindev cid
			join device d on d.id_dev=cid.id_dev
			join device d2 on d2.id_ctrl=d.id_ctrl and d2.id_reader is null
			join st_data std on std.id_dev=d2.id_dev and std.id_param=1
			where cid.attempts>'.Arr::get($params, 'maxAttempts', 0).')';
  
  
					
		$query = DB::query(Database::UPDATE, $sql)
				->execute(Database::instance('fb'));
			
		
		
		
		
		$log->add(log::INFO, 'Произведена загрузка карт в  контроллеры, с которыми восстановленая связь. Обновляются карты, у которых количество попыток больше '.Arr::get($params, 'maxAttempts', 0).', им присваивается значение количества попыток '.Arr::get($params, 'attempts', 0).'.');	
		
		
		
		if(	1==1)
			{
				$log->add(log::INFO, 'E-mail is send.' );
				$answer='Произведена загрузка карт в  контроллеры, с которыми восстановленая связь.';	
				

		$mailer = Email::factory();

		
		/* $mailer
		  ->to('it@artsec.ru', 'IT')
		  ->to('b71@mail.ru', 'b71')
		  ->from('support@artonit.ru', 'Калибр')
		  ->subject($answer)
		  ->html('<i>'.$answer.'</i>')
		  ->send();	 */			
				
			} else {
				
				$log->add(log::INFO, 'E-mail is not send.' );
				
			}
		
		
        }
    }