    <?php defined('SYSPATH') or die('No direct script access.');
 
    class Task_sendGRZ extends Minion_Task {
		/**
		
		Тест для имитации обработки быстро пришедших идентификаторов.
		C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=A000AA00 --id_gate=3
		
		*/
		
		    protected $_options = array(
        // param name => default value
        'id_gate'   => 3,//номер ворот
        'grz'   => 'A005BB177',//номер ГРЗ
      
		);
	
        
        protected function _execute(array $params)
        {
        Log::instance()->add(log::INFO, '21-GRZ START Обработка GRZ :key ворота :id_gate', array(':key'=>Arr::get($params, 'grz'), ':id_gate'=>Arr::get($params, 'id_gate')));	
		
		Model::factory('cvss')->common(Arr::get($params, 'grz'), Arr::get($params, 'id_gate'));
		Log::instance()->add(log::INFO, '21-GRZ STOP Обработка GRZ :key ворота :id_gate', array(':key'=>Arr::get($params, 'grz'), ':id_gate'=>Arr::get($params, 'id_gate')));	
				
        }
    }