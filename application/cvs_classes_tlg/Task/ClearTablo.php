    <?php defined('SYSPATH') or die('No direct script access.');
     
    /**
     * Test class
     *
     * @author novisasha
     */
     
    class Task_ClearTablo extends Minion_Task {
        
        protected function _execute(array $params)
        {
            //Minion_CLI::write('Hello World!');
			$tt=time();
			Log::instance()->add(Log::NOTICE, '#15 Старт очистки табло');
			
			$camList=array(2,4);
			//время демонстрации надписи
			$timeVisible=60;
			foreach($camList as $key)
			{
				//Log::instance()->add(Log::NOTICE, '#Файл lastevent'.$key.' был изменен '.($tt - filemtime('c:\xampp\htdocs\cvs\lastevent'.$key)).' сек назада.');
				if(($tt - filemtime('c:\xampp\htdocs\cvs\lastevent'.$key)>$timeVisible))
				{
					$cvs=new phpCvs($key);
					//$tablo=new phpMPT($cvs->tablo_ip, $cvs->tablo_port);
					
					$tablo=new phpMPTtcp($cvs->tablo_ip, $cvs->tablo_port);//если камера №2, то используем TCP протокол
					
					//$tablo=new phpMPT($cvs->tablo_ip, $cvs->tablo_port);
					
					
			
				//очищаю табло 
					$tablo->command='clearTablo';
					$tablo->execute();
					
					$tablo->command='text';// вывод ГРЗ на табло
					$tablo->commandParam='';
					$tablo->execute();
					
					Log::instance()->add(Log::WARNING, '#41 Выполнена очистка экрана табло камеры '.$key);
					//Log::instance()->add(Log::WARNING, '#43 '.Debug::vars($key, $cvs, $tablo));
					
					
				//вывод рекламных сообщений на табло
				
					$tablo->command='text';// вывод строки 1
					$tablo->commandParam=$cvs->top_string;
					$tablo->coordinate="\x00\x00\x02";
					$tablo->execute();
					
					$tablo->command='text';// вывод строки 1
					$tablo->command='scrolText';// вывод строки 1
					$tablo->commandParam=$cvs->down_string;
					$tablo->coordinate="\x08\x00\x02\x01";
					$tablo->execute();
					
					/* $tablo->command='scrolText';
					$tablo->execute(); 	
					Log::instance()->add(Log::WARNING, '#58 Вывод на табло '.$key.' сообщений  верхняя строка "'.iconv('windows-1251','UTF-8',$cvs->top_string).'", нижняя строка "'.iconv('windows-1251','UTF-8',$cvs->down_string).'"');
				 */
				}
				
				
			}
			return;
        }
    }