    <?php defined('SYSPATH') or die('No direct script access.');
 
    class Task_AutoCheckCardidx extends Minion_Task {
		
		    protected $_options = array(
        // param name => default value
        'name'   => 'World',
        'delay'   => '30',
		);
	
        
        protected function _execute(array $params) //процедура автопроверки таблицы cardidx
        {
			
	
			
        $sql='select \'insert into CardIdx(ID_DB, ID_CARD, ID_DEV) values (1, \'\'\'||c.id_card ||\'\'\', \'||ac.id_dev||\');\' from ss_accessuser ssu
        join card c on ssu.id_pep=c.id_pep
        join access ac on ssu.id_accessname=ac.id_accessname
        left join cardidx cd on cd.id_card=c.id_card and cd.id_dev=ac.id_dev
        join device d on d.id_dev=ac.id_dev and d."ACTIVE"=1
        join device d2 on d2.id_ctrl=d.id_ctrl and d2.id_reader is null and d2."ACTIVE"=1
        where
        c."ACTIVE">0
        and (c.timeend>\'NOW\' or c.timeend is null)
        and c.id_cardtype in (1,2)
        and cd.id_card is null
		 and d2.id_devtype in (1)';
		
		//Kohana::$log->add(log::INFO, $sql);
		
		
		$query['AutoCheckCardidx'] = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
					
		$post=Validation::factory($query);
				$post->rule('AutoCheckCardidx', 'is_array')
						->rule('AutoCheckCardidx', 'not_empty')
						;
		if($post->check())
				{
					Kohana::$log->add(log::INFO, 'Автопроверка таблицы cardidx. Найдены пропуски в таблице cardidx. '. Debug::vars(Arr::get($post, 'AutoCheckCardidx')));
				} else 
				{
					Kohana::$log->add(log::INFO, 'Автопроверка таблицы cardidx. Ошибок нет.');
				}
				
		
		
        }
    }