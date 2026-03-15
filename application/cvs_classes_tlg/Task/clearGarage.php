    <?php defined('SYSPATH') or die('No direct script access.');
			/**
		
		Удаляет все грз для указанного гаража.
		Это необходимо для организации тестирования, чтобы изначально в гараже никого не было.
		C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
		
		*/
 
    class Task_clearGarage extends Minion_Task 
	{
		
		    protected $_options = array(
        // param name => default value
        'id_garage'   => 3,
        'delay'   => '30',
		);
	
        
        protected function _execute(array $params)
        {
        $res=array();
		$sql='select p.id_pep from hl_garagename  hlgn
			join hl_orgaccess hlo on hlo.id_garage=hlgn.id
			join people p on p.id_org=hlo.id_org
			where hlgn.id='.Arr::get($params, 'id_garage');
  

					
		$query = DB::query(Database::SELECT, DB::expr($sql))
			->execute(Database::instance('fb'))
			->as_array();
			foreach($query as $key=>$value)
			{
				$res[]=Arr::get($value, 'ID_PEP');
			}
		
		
		$sql='delete from hl_inside hli where hli.id_pep in ('.implode(",",$res).')';
		$query = DB::query(Database::DELETE, DB::expr($sql))
			->execute(Database::instance('fb'));
		
	
		Log::instance()->add(log::INFO, '43 Очистил гаража :data', array(':data'=>Arr::get($params, 'id_garage')));	
			
		
        }
    }