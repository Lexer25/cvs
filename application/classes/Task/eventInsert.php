    <?php defined('SYSPATH') or die('No direct script access.');
     
    /**
     * Вставка события о начале тестирования.
     *
     * C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100 --comment=56
     */
     
    class Task_eventInsert extends Minion_Task {
		
		    protected $_options = array(
        // param name => default value
        'id'   => 0,
        'grz'   => '',
        'id_gate'   => '',
        'comment'   => '',
        
		);
        
        protected function _execute(array $params)
        {
			switch(Arr::get($params, 'id')){
				case 100:
					$mess='START TEST';
				break;
				case 101:
					$mess='STOP TEST';
				break;
				default:
					$mess='Insert events '.Arr::get($params, 'id');
				break;
				
				
				
			}
			Log::instance()->add(Log::NOTICE, '22-22  :data', array(':data'=>$mess));
			$events= new Events();
			$events->eventCode=Arr::get($params, 'id');
			//$events->comment=iconv('windows-1251','UTF-8', Arr::get($params, 'comment'));
			$events->comment=Arr::get($params, 'comment');
			//$events->grz=$identifier->id;
			//$events->id_gate=$cvs->id_gate;
			$events->addEventRow();
			
		
			
        }
    }