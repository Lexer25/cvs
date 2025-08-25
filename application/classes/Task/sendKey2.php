    <?php defined('SYSPATH') or die('No direct script access.');
 
    class Task_sendKey2 extends Minion_Task {
		/**
		
		Тест для имитации обработки быстро пришедших идентификаторов.
		номер идентификатора надо передавать в десятичной форме.
		C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey2 --key=123456 --id_gate=3
		
		*/
		    protected $_options = array(
        // param name => default value
        'id_gate'   => 3,//номер ворот
        'key'   => '50500505',//номер ГРЗ
      
		);
	
        
        protected function _execute(array $params)
        {
        Log::instance()->add(log::INFO, '21-UHF START Обработка UHF :key ворота :id_gate', array(':key'=>Arr::get($params, 'key'), ':id_gate'=>Arr::get($params, 'id_gate')));	
		Model::factory('hed2')->common2(Arr::get($params, 'key'), Arr::get($params, 'id_gate'));
		Log::instance()->add(log::INFO, '21-UHF STOP Обработка UHF :key ворота :id_gate', array(':key'=>Arr::get($params, 'key'), ':id_gate'=>Arr::get($params, 'id_gate')));	
        }
    }