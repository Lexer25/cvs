    <?php defined('SYSPATH') or die('No direct script access.');
			/**
		
		Удаляет указаанного key из списка inside.
		Это необходимо для организации тестирования, чтобы изначально в гараже никого не было.
		C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=delKeyFromInside --key=3
		
		*/
 
    class Task_delKeyFromInside extends Minion_Task 
	{
		
		    protected $_options = array(
        // param name => default value
        'key'   => 0,
        );
	
        
        protected function _execute(array $params)
        {
       	
			$sql='delete from hl_inside hli where hli.id_card=\''.Arr::get($params, 'key').'\'';
			$query = DB::query(Database::DELETE, DB::expr($sql))
				->execute(Database::instance('fb'));
		
			Log::instance()->add(log::INFO, '43 Удалил key :data из таблицы iside ', array(':data'=>Arr::get($params, 'key')));	
			
		
        }
    }