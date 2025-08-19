    <?php defined('SYSPATH') or die('No direct script access.');
			/**
		
		Удаляет указаанного id_pep из списка inside.
		Это необходимо для организации тестирования, чтобы изначально в гараже никого не было.
		C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=delPep --id_pep=3
		
		*/
 
    class Task_delPep extends Minion_Task 
	{
		
		    protected $_options = array(
        // param name => default value
        'id_pep'   => 0,
        );
	
        
        protected function _execute(array $params)
        {
       	
			$sql='delete from hl_inside hli where hli.id_pep='.Arr::get($params, 'id_pep').')';
			$query = DB::query(Database::DELETE, DB::expr($sql))
				->execute(Database::instance('fb'));
		
			Log::instance()->add(log::INFO, '43 Удалил id_pep :data из таблицы iside ', array(':data'=>Arr::get($params, 'id_pep')));	
			
		
        }
    }