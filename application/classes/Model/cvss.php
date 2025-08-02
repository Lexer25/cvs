<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
*4.05.2025 Вспомогательные функции для работы парковочной системы
*/
class Model_Cvss extends Model {
	
	/**Провка: находится ли номер видеокамеры в настройках?
	*@param $cam номер видеокамеры
	*/
	public static function checkCamIsPresent ($cam) // 
	{
	$sql='select hlp.id_cam from hl_param hlp
	where hlp.id_cam='.$cam;
	return( DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('ID_CAM') >0);
	}
	
	/**Провка: находится ли полученный IP в настройках?
	*@param $cam номер видеокамеры
	*/
	public static function checkIpIsPresent ($ip) // 
	{
	$sql='select distinct hlp.box_ip from hl_param hlp
where hlp.box_ip=\''.$ip.'\'';
	return( DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('BOX_IP') >0);
	}

		
	/**Получить id ворот по номеру видеокамеры
	*@param $cam номер видеокамеры
	*/
	public static function getGateFromCam ($cam) // 
	{
	$sql='select hlp.id from hl_param hlp
	where hlp.id_cam='.$cam;
	return( DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('ID'));
	}
	
	
	/**Получить id ворот по IP адресу контроллера
	*@param $cam номер видеокамеры
	*/
	public static function getGateFromBoxIp ($ip, $port) // 
	{
	$sql='select hlp.id from hl_param hlp
	where hlp.box_ip=\''.$ip.'\'
	and hlp.channel='.$port;
	//Log::instance()->add(Log::DEBUG, '54 '.$sql);
	return( DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('ID'));
	}
	
	
	
	
	
	/**Проверка: не повторное ли это событие?
	* 4.05.2025
	*cvs иногда присылала повторно событие
	*@param $id_event номер события
	*@return bool true - событие уникальное
	*/
	public static function isEventUniq ($id_event) // 
	{
		return true;
	}
	
	//24.07.2025 удаляю кого-нибудь из гаража, кто находится на стоянке
	public function delAnyIdPepOnPlace($id_garage,$id_parking)
	{
		$sql='select first 1 hli.id_pep from hl_inside hli
			left join people p on hli.id_pep=p.id_pep
			left join hl_orgaccess hlo on hlo.id_org=p.id_org
			where hlo.id_garage='.$id_garage.'
			and hli.counterid='.$id_parking;
		
		$_id_pep = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('ID_PEP');
			
		$sql='delete from hl_inside hli
			where hli.id_pep='.$_id_pep;
		try
			{
				$query = DB::query(Database::DELETE, iconv('UTF-8', 'CP1251',$sql))
				->execute(Database::instance('fb'));
				Log::instance()->add(Log::DEBUG, 'Line 78 people :id_pep из паркинга :id_parking удалени успешно. ', array(':id_pep'=>$this->id_pep, ':id_parking'=>$this->id_parking));
				
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, 'Line 78 '. $e->getMessage());
							
			}
		
	}
	
	
	/**20.07/2025
	*
	*
	*/
	//
	
	public function mainAnalysis(Identifier $identifier, phpCVS $cvs)
	{
		// Log::instance()->add(Log::NOTICE, '729 identifier :data', array(':data'=>Debug::vars($identifier)));
		// Log::instance()->add(Log::NOTICE, '730 cvs :data', array(':data'=>Debug::vars($cvs)));
		//формирую информацию о паркинге (нужные режимы его работы
		//sleep(5); //имитация задержки в период проверки при работе с рабочей базой данных
		$parking = new Parking($cvs->id_parking);
		//Log::instance()->add(Log::NOTICE, '924 parking :data', array(':data'=>Debug::vars($parking)));
		Log::instance()->add(Log::NOTICE, '614 Начал работу анализатора для key=:key ворота :id_gate', array(':key'=>$identifier->id, ':id_gate'=>$cvs->id_gate));
		//начинаю анализ
		//начинаю проверку полученного идентификатора. grz
		//$identifier=new Identifier($grz);
		//Log::instance()->add(Log::NOTICE, '776 Identifier'. Debug::vars($identifier));
		if(!$identifier->status==Identifier::VALID){
			//зафиксировать в журнале отказ от дальнейшей обработки
			Log::instance()->add(Log::NOTICE, '675 identifier :key не валидна status=:status. Завершаю обработку.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
			return Events::UNKNOWNCARD;
			//exit;
		} 
		Log::instance()->add(Log::NOTICE, '132 identifier :key валидна status=:status, ворота :id_gate. Продолжаю обработку.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status,
					':id_gate'=>$cvs->id_gate,
					));
		//Теперь надо собрать данные для дальнейшего анализа
		
		//надо иметь информацию и о воротах, откуда пришел ГРЗ
			//Эта часть реализована в phpCVS
			//$cvs=new phpCVS($id_gate);
			//echo Debug::vars('106 информация о воротах', $cvs);
			
			
		//если у ГРЗ есть гараж, то надо делать только проверки свободных мест.
		//если же гаража нет, то надо проверять категорию доступа.	

//======== нет гаража ========================		
		if(is_null($identifier->id_garage)) //гаража нет.
		{
			Log::instance()->add(Log::NOTICE, '706 identifier :key гаража не имеет. Проверяю категории доступа',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
			//тут надо вызвать метод анализа без гаража, только по категории доступа.
			//результат анализа - можно или нельзя!
			
//ГАРАЖА НЕТ!!!			
			if($cvs->isEnter) {//если въезд 
			//checkInPlace - проверка нахождения на территории
			//Log::instance()->add(Log::NOTICE, '781 identifier :key ', array(':key'=>Debug::vars($identifier)));
			
				if ($cvs->checkAccessForNonGarage($identifier->id_pep))//есть ли разрешение по категориям доступа?
				{
					if($identifier->checkInPlace()) return Events::WOK;//повторный въезд разрешен
					return Events::OK;//въезд разрещен
				} else {
					return Events::ACCESSDENIED;//въезд запрещен, нет разрешения категориии доступа
				}
			} else { //если это выезд,
				if ($cvs->checkAccessForNonGarage($identifier->id_pep)) return Events::OK;//есть ли разрешение по категориям доступа?
				return Events::ACCESSDENIED;
			}

		} 
//========== есть гараж ===========================		
//ГАРАЖА ЕСТЬ!!!			
		//надо иметь информацию о гараже.
			$garage=new Garage($identifier->id_garage);//это - модель гаража, куда едет ГРЗ. Этот параметр беретс из $identifier->id_garage, но для отладки использую фиксировнное значение  $id_garage
			//echo Debug::vars('109 ГРЗ едет вот в этот гараж', $garage);
		
	/* 	Log::instance()->add(Log::NOTICE, '803  гараж :garage.',
				array(
					':key'=>$identifier->id,
					':garage'=>Debug::vars($garage)
					));		 */
		
		Log::instance()->add(Log::NOTICE, '190 identifier :key имеет гараж :garage.',
				array(
					':key'=>$identifier->id,
					':garage'=>$identifier->id_garage
					));
			//тут надо вызвать метод анализа при наличии гаража.
			//результат анализа:
			//-въезд разрешен (т.е. места есть).
			//въезд запрещен (т.е. мест нет)
			//Log::instance()->add(Log::NOTICE, '727 garage'. Debug::vars($garage));
			if(!$cvs->checkAccess($garage->id_parking)){ //тут $garage->id_parking - список парковок, на которых расположены машиноместа гаража
				//ворота, куда подъехал автомобиль, не содержит парковочных мест гаража, разрешенных этому ГРЗ.
				//въезд запрещен
				Log::instance()->add(Log::NOTICE, '203 identifier :key подъехала к чужим воротам.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
				
				// $event->eventCode=events::ACCESSDENIED;
				// $event->grz=$grz;
				// $event->addEventRow();
				return Events::ACCESSDENIED;				
				//echo Debug::vars('134 ошибся воротами!');
				//exit;
			} 
				Log::instance()->add(Log::NOTICE, '216 identifier :key подъехала к своим воротам.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
				
//ВЪЕЗД!!!				//въезд разрешен, анализирую загрузку гаражей
				if($cvs->isEnter) {//если въезд
				
					if(insideList::checkGrzInParking($identifier->id)) //он уже на парковке
					{
						return Events::WOK;	//повторный въезд
						
					}
					
				
					if($cvs->checkPHPin($garage)){//если в гараже есть места или именно этот ГРЗ уже стоит в гараже
						
						//въезд разрешен
						
						return Events::OK;	
					} else {
						//въезд запрещен (нет мест)
						//но этот id_pep может уже стоять в гараже, и тогда надо запустить... повторный въезд
						
						return Events::CARLIMITEXCEEDED;	
					}
//ВЫЕЗД!!!
				} else {//если выезд
				
					//if($cvs->checkPHPin($garage)){
					if($cvs->checkPHPout($garage)){
						//выезд разрешен
						return Events::OK;
					} else {
						//выезд запрещен
						return Events::ACCESSDENIED;	
					}
				}
 	
			
		Log::instance()->add(Log::NOTICE, '808 Анализа :key для ворот :id_gate завершен с результатом :data', array(':key'=>$identifier->id, ':id_gate'=>$cvs->id_gate, ':data'=>$permission));;
		return Events::UNKNOWNRESULT;
	}


/**20.07.2025
	*	Процедура управления воротами
	*
	*/
	public function gateControl(Identifier $identifier, phpCVS $cvs)
	{
		//===============================================================
		//Этап 3. Проверка на работу в режиме ТЕСТ	
		//если включен режим Тест (в конфигураторе, файл config), то надо возращать результат 145 (прохода в режиме Тест).
		//результатом работы этого этапа является:
		//коррекция сообщения для вывода на табло cvs->eventdMess 
	   //Log::instance()->add(Log::NOTICE, '223 start gateControl '.Debug::vars($cvs)); 
		$t1=microtime(1);	
	   $config = Kohana::$config->load('config');
		if(Arr::get($config, 'testMode'))
		{
			$cvs->code_validation=50;//разрешить проезд
			//$cvs->code_validation=145;//разрешить проезд
			$cvs->eventdMess='OPEN in TEST MODE:'.date('H:i:s', time());
			Log::instance()->add(Log::NOTICE, '196 включен режим testMode. Пропускаю всех вподряд. Validation '.$cvs->code_validation); 
				
				$direct='выезд';
				if($cvs->isEnter) $direct='въезд';
		
			Log::instance()->add(Log::NOTICE, '200 TEST MODE id_gate = :id_gate ip=:ip channel=:channel id_dev=:id_dev key=:key direct=:direct validate=:validate eventdMess=:eventdMess ', 
					array(':id_gate'=>$cvs->id_gate, 
							':ip'=>$cvs->box_ip,
							':channel'=>$cvs->ch,
							':id_dev'=>$cvs->id_dev,
							':key'=>$identifier->id,
							':direct'=>$direct,
							':validate'=>$cvs->code_validation,
							));	
			
		}
		
		//===============================================================
		//Этап 4: опеределение режимов работы реле. Возможно, надо делать что-то более хитрое, чем просто щелкнуть реле.
		//результатом работы этого этапа является:
		//коррекция свойства ворот cvs->eventdMess 
		//теперь определяю каким реле щелкать.
	
		//получаю список ворот (из файла config), которые работают в реверсивном режиме (одни ворота и на въезд, и на выезд)
		//от этого лучше отказаться, и монтировать выходы реле параллельно.
		// но пока у нас получается режим "Одна дверь на два считывателя", реализованная программно.
		$reverseList=Arr::get($config, 'reverseGate');
				
		
		//Тут надо помнить, что ворота cvs рассматриваются как комплексное устройство, и у него нет понятия двери (канал, реле), а есть режим работы mode
		//при выполнеии команды opendoor реле работают в зависимости от режима mode
		//$mode ==0 открываю дверь 0
		//$mode ==1 открываю дверь 1
		//$mode ==2 открываю все двери поочередно
		//$mode ==3 открываю все двери сразу одной командой
		//этот режим и надо явно указать для правильного управления воротами.
		
		if(in_array($cvs->id_gate,$reverseList))
		{
			$cvs->mode = 2;//если режим работы Реверсивные ворота, то щелакаю обоими реле
			Log::instance()->add(Log::NOTICE, '215 реверсивный режим для ворот '.$cvs->id_gate.'. открываю оба реле'); 
		} else {
			$cvs->mode = $cvs->ch;//если режим НЕ реверсивный, то режим равен номеру канала
			Log::instance()->add(Log::NOTICE, '219 Не реверсивный режим. открываю реле '. $cvs->ch); 
		}
	
		//Log::instance()->add(Log::NOTICE, '220-1'.Debug::vars($cvs)); 
	
		Log::instance()->add(Log::NOTICE, '452-0 debug :data', array(':data'=>(microtime(true) - $t1)));
		
		//===============================================================
		
		//записываю событие в журнал
		$events= new Events();
		$events->eventCode=$cvs->code_validation;
		$events->grz=$identifier->id;
		$events->id_gate=$cvs->id_gate;
		$events->addEventRow();

		//Этап 5: управление внешими стройствами по результатам валидации
		
		$tablo=new phpTablo($cvs->tablo_ip, $cvs->tablo_port);//работа в режиме TCP 
		
			//сохраняю в семафор с номером табло номер обрабатываемого события
		//Model::factory('mpt')->setSemafor('lastevent'.$cvs->cam, $cvs_event_id);
		
		//обработка кодов валидации
		//Log::instance()->add(Log::NOTICE, '256-256  '.Debug::vars($cvs)); 
		
		//Log::instance()->add(Log::NOTICE, '220-2'.Debug::vars($cvs)); 
		Log::instance()->add(Log::NOTICE, '452-10 debug :data', array(':data'=>(microtime(true) - $t1)));
		
		 switch($cvs->code_validation){
		
		
			case events::WOK : //повторный проезд разрешен
			case 50 : //проезда разрешен
				$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);//создаю экземпляр контроллера МПТ
				//Log::instance()->add(Log::NOTICE, '307-307  '.Debug::vars($mpt));
				if(!Arr::get($config, 'debug')) {
						$mpt->openGate($cvs->mode);// даю команду открыть ворота
				} else {
					$mpt->result ='OK';
				}			
				$i=0;
					while($mpt->result !='OK' AND $i<10)// делать до 10 попыток
					{
						Log::instance()->add(Log::DEBUG, '155 Команда открыть ворота '.$cvs->box_ip.':'.$cvs->box_port.' выполнена неудачно: '.$mpt->result.' desc '.$mpt->edesc.'. timestamp '.microtime(true).'. Команда Открыть ворота повторяется еще раз, попытка '.$i.' time_from_start='.number_format((microtime(1) - $t1), 3));
						if(!Arr::get($config, 'debug')) $mpt->openGate($cvs->mode);// открыть ворота
						$i++;
					}
					//Log::instance()->add(Log::NOTICE, '004_150 Событие 50. Результат выполнения команды openGate '.$cvs->box_ip.':'.$cvs->box_port.' result='.$mpt->result.', desc='.$mpt->edesc.'  после '. $i .' попыток time_from_start='.number_format((microtime(1) - $t1), 3));		
				if($mpt->result == 'Err') 
				{
					//Log::instance()->add(Log::NOTICE, '138 Событие 50. Не смог открыть ворота в течении 10 попыток. Видеокамера '.$cvs->cam.' ('.$direct.') ГРЗ '.$identifier->id.' контролер IP='.$cvs->box_ip.':'.$cvs->box_port.' Режим шлюза '.$cvs->mode.' Ответ '.$mpt->result.' edesc '.$mpt->edesc);		
				} else 
				{
					//Log::instance()->add(Log::NOTICE, '004_64 Событие 50. Ответ контроллера после повторной команды '.$mpt->result.' edesc '.$mpt->edesc.'  после '. $i .' попыток.');	
						//Ворота открылись успешно, надо добавлять ГРЗ в таблицу inside в зависиммочти от направления движения
						
						$inside= new insideList;
						$inside->id_card=$identifier->id;
						$inside->id_pep=$identifier->id_pep;
						$inside->id_parking=$cvs->id_parking;
						
						if($cvs->isEnter==1) {// въезд
							$inside->addToInside();
						} else { //это выезд
							//обработка для С гаражом и Без гаража - разная.
							//без гаража - надо только удалить этого id_pep из таблицы inside
							//а если с гаражом, то надо проверить присутсвие, и если пипел не на парковке, то удалять кого-нибудь.
							
							if(is_null($identifier->id_garage)) //если нет гаража
							{
									$inside->delFromInside();//если на парковке, то удаляю пипла по его id_pep
							} else {//если есть гараж
								if(!$identifier->checkInParking($cvs->id_parking))
								{
									$inside->delFromInside();//если на парковке, то удаляю пипла по его id_pep
								} else {
										Log::instance()->add(Log::NOTICE, '423-0 Выехал Удаляю grz :grz id_pep=:id_pep id_garage=:id_garage id_parking = :id_parking, которого не было на стоянке. Удаляю кого нибудь из этого гаража на этой парковке.', array(
											':grz'=>$identifier->id,
											':id_pep'=>$identifier->id_pep,
											':id_garage'=>$identifier->id_garage,
											':id_parking'=>$cvs->id_parking,
											
											));
										Model::factory('cvss')->delAnyIdPepOnPlace($identifier->id_garage, $cvs->id_parking);
										//$inside->delFromInside();//если на парковке, то удаляю пипла по его id_pep
									}
							}
						}
						 
						
				}
					//теперь занимаюсь выводом информации на табло
				
				$tablo->command='clearTablo';
				if(!Arr::get($config, 'debug')) $tablo->execute(); 		
				//Log::instance()->add(Log::NOTICE, '152 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
					
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$identifier->id;
				$tablo->coordinate="\x00\x00\x02";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '158 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x02\x01";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '164 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));
				
				
			 break;
			 case 46 : //неизвестная карта
					//для неизвестной карты открывать ворота НЕ надо, поэтому экземпляр МПТ не создается.
					//работаю только с табло
				$tablo->command='clearTablo';
				if(!Arr::get($config, 'debug')) $tablo->execute(); 	
				//Log::instance()->add(Log::NOTICE, '173 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
							
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$identifier->id;
				$tablo->coordinate="\x00\x00\x03";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '180 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x03\x01";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '187 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				
			 break;
			 
			case 65 : //проезд запрещен (нет прав на проезд в этот гараж)
				//тут же можно сделать проверку: может, имеется разрешение на въезд в другой паркинг?
					
				$tablo->command='clearTablo';
				if(!Arr::get($config, 'debug')) $tablo->execute(); 		
				//Log::instance()->add(Log::NOTICE, '203 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$identifier->id;
				$tablo->coordinate="\x00\x00\x04";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '203 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x04\x01";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '216 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
			
			
				
			 break;
			 
			 case 81 : //проезд запрещен (нет мест в гаражах)
				//тут же можно сделать проверку свободных мест на другой парковке	
					
				$tablo->command='clearTablo';
				if(!Arr::get($config, 'debug')) $tablo->execute(); 	
				//Log::instance()->add(Log::NOTICE, '232 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		
					
				
				$tablo->command='text';// вывод ГРЗ на табло
				$tablo->commandParam=$identifier->id;
				$tablo->coordinate="\x00\x00\x06";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '239 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

				
				$tablo->command='scrolText';// вывод сообщений на табло
				$tablo->commandParam=$cvs->eventdMess;
				$tablo->coordinate="\x08\x00\x06\x01";
				if(!Arr::get($config, 'debug')) $tablo->execute();
				//Log::instance()->add(Log::NOTICE, '246 Ответ от табло '.$cvs->tablo_ip.':'.$cvs->tablo_port.' result: '.$tablo->result.' desc '.$tablo->edesc.' time_from_start='.number_format((microtime(1) - $t1), 3));		

			 break;
			 
			 
			 default: //код валидации не обрабатывается.
			 
				Log::instance()->add(Log::NOTICE, "244 Неизвестный код валидации. Ворота не открываются, на табло ничего не выводится ". $cvs->code_validation);
			
			 
			 break;
		 } 
		
	}
	
	public function common(Identifier $identifier, phpCVS $cvs)
	{
		
	}
	
}
