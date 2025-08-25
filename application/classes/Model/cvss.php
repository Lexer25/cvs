<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
*4.05.2025 Вспомогательные функции для работы парковочной системы
*/
class Model_Cvss extends Model {
	
	/**Провка: находится ли номер видеокамеры в настройках?
	*@param $cam номер видеокамеры
	*/
	
	//public $db='parkresident';
	public $db='pr';
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
		

		
		if(is_null($_id_pep))
		{
			Log::instance()->add(Log::DEBUG, 'Line 88 при выезде удалять некого. id_pep = :_id_pep. ', array(':_id_pep'=>Debug::vars($_id_pep)));
			return;

		}			
			
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
	
	
	/**20.07/2025 основной модуль анализа разрешения на въезд
	* события 
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
		Log::instance()->add(Log::NOTICE, '614 :key Начал работу mainAnalysis для key=:key ворота :id_gate isEnter :isEnter', array(':key'=>$identifier->id, ':id_gate'=>$cvs->id_gate, ':isEnter'=>$cvs->isEnter));
		//начинаю анализ
		//начинаю проверку полученного идентификатора. grz
		//$identifier=new Identifier($grz);
		//Log::instance()->add(Log::NOTICE, '776 Identifier'. Debug::vars($identifier));
		if(!$identifier->status==Identifier::VALID){
			//зафиксировать в журнале отказ от дальнейшей обработки
			Log::instance()->add(Log::NOTICE, '675 :key identifier :key не валидна status=:status. Завершаю обработку.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
			return Events::UNKNOWNCARD;
			//exit;
		} 
		Log::instance()->add(Log::NOTICE, '132 :key identifier :key валидна status=:status, ворота :id_gate. Продолжаю обработку.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status,
					':id_gate'=>$cvs->id_gate,
					));
		//Теперь надо собрать данные для дальнейшего анализа
		
		//надо иметь информацию и о воротах, откуда пришел ГРЗ
						
			
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
//ГАРАЖ ЕСТЬ!!!			
		//надо иметь информацию о гараже.
			$garage=new Garage($identifier->id_garage);//это - модель гаража, куда едет ГРЗ. Этот параметр беретс из $identifier->id_garage, но для отладки использую фиксировнное значение  $id_garage
				
		Log::instance()->add(Log::NOTICE, '190 :key identifier :key имеет гараж :garage.',
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
				return Events::ACCESSDENIED;				
			} 
				Log::instance()->add(Log::NOTICE, '216 :key identifier :key подъехала к своим воротам.',
				array(
					':key'=>$identifier->id,
					':status'=>$identifier->status
					));
				
//ВЪЕЗД!!!				//въезд разрешен, анализирую загрузку гаражей
				if($cvs->isEnter) {//если въезд
				
					if(insideList::checkGrzInParking($identifier)) //ГРЗ уже на парковке
					{
							Log::instance()->add(Log::NOTICE, '232 :key identifier :key id_pep :id_pep уже находится на парковке.',
				array(
					':key'=>$identifier->id,
					':id_pep'=>$identifier->id_pep,
					));
						return Events::G_WOK;	//w dubble OK повторный въезд
					} else {
					
						if(insideList::checkIdPepInParking($identifier)) //id_pep уже на парковке, но под другим идентификатором
							{
									Log::instance()->add(Log::NOTICE, '243 :key identifier :key НЕ на парковке, но его владелец id_pep :id_pep уже НА парковке под другим идентификтором.',
						array(
							':key'=>$identifier->id,
							':id_pep'=>$identifier->id_pep,
							));
								return Events::G_WOK_PEP;	//повторный въезд по владельцу
							}
					}
				
					if($cvs->checkPHPin($garage)){//если в гараже есть места
						
						//въезд разрешен
						
						return Events::G_OK_PLACE;	//есть места
					} else {
						//въезд запрещен (нет мест)
						//но этот id_pep может уже стоять в гараже, и тогда надо запустить... повторный въезд
						
						return Events::CARLIMITEXCEEDED;	
					}
//ВЫЕЗД!!!
				} else {//если выезд
				
					if(insideList::checkGrzInParking($identifier))//ГРЗ на парковке
					{ 
						return Events::G_OK;//просто штатный выезд			
					} else {
						return Events::G_OK_2;//выезд без въезда	
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
	   Log::instance()->add(Log::NOTICE, '223 start gateControl ', array(':data'=>Debug::vars($cvs))); 
	  // Log::instance()->add(Log::NOTICE, '223- tablo :mess ', array(':mess'=>iconv('windows-1251','UTF-8', $cvs->eventdMess))); 
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
			$cvs->mode = 2;//если режим работы Реверсивные ворота, то щелкаю обоими реле
			Log::instance()->add(Log::NOTICE, '215 :key реверсивный режим для ворот '.$cvs->id_gate.'. открываю оба реле', array(':key'=>$identifier->id)); 
			//блокировка противоположного въезда на некоторое время, чтобы не "поймать" выезжающий UHF или ГРЗ
			foreach($reverseList as $key=>$value)
			{
				
				//Log::instance()->add(Log::DEBUG, 'Line 335 блокировка ворот отладка :_dd. ', array(':_dd'=>Debug::vars($reverseList, $key, $value, $cvs->id_gate, ($cvs->id_gate != $value))));
				if ($cvs->id_gate != $value) 
				{
					$delayReversRepeat=Setting::get('delayReversRepeat', 120);//время блокировки смежный ворот при выезде
					
					Log::instance()->add(Log::DEBUG, 'Line 338 блокировка   ворот :id_gate gateBlock_:id_gate включена на время delayReversRepeat :delayReversRepeat секунд до :ct. ', array(':id_gate'=>$value, ':delayReversRepeat'=>$delayReversRepeat, ':ct'=>$this->calcTime($delayReversRepeat)));
				
					Cache::instance()->set('gateBlock_'.$value, array('name'=>'gateBlock_'.$value, 'id_gate_reverse'=>$value), $delayReversRepeat);//запрещаю проезд через другие ворота из списка реверсивных
				
				} else {//пока там две записи - будет работать нормально, будет запрещен въезд только в одни - противоположные - ворота
					Log::instance()->add(Log::DEBUG, 'Line 343 блокировка   ворот :id_gate НЕ включена. ', array(':id_gate'=>$value));
				}
				
			}
			
			
		} else {
			$cvs->mode = $cvs->ch;//если режим НЕ реверсивный, то режим равен номеру канала
			Log::instance()->add(Log::NOTICE, '219 Не реверсивный режим. открываю реле '. $cvs->ch); 
		}
	
		
		
		//===============================================================
		
		

		//Этап 5: управление внешими стройствами по результатам валидации
		
		$tablo=new phpTablo($cvs->tablo_ip, $cvs->tablo_port);//работа в режиме TCP 
		
			
		 switch($cvs->code_validation){
		
			//проез разрешен по категориям доступа
			case events::WOK : //повторный проезд разрешен, категория
			case events::OK : //проезда разрешен, категория
			
			//проезд разрешен по наличию гаражей
							
			case events::G_OK_PLACE : //15 въезд разрешен по наличию гаража, места есть
			case events::G_WOK_PEP : //16 повторный въезд разрешен.Этого ГРЗ на парковке нет, но есть этот пипел
			case events::G_WOK : //18 повторный въезд разрешен .Этот ГРЗ уже есть на парковке
			case events::G_OK : //17 выезд разрешен, имеется гараж, на парковке находится
			case events::G_OK_2 : //14 выезд разрешен без въезда, имеется гараж, на парковке не находится
			
			$mpt=new phpMPTtcp($cvs->box_ip, $cvs->box_port);//создаю экземпляр контроллера МПТ
				//Log::instance()->add(Log::NOTICE, '307-307  '.Debug::vars($mpt));
				if(!Arr::get($config, 'debug')) {
						$mpt->openGate($cvs->mode);// если режим отладки НЕ включен, то даю команду открыть ворота
				} else {
					Log::instance()->add(Log::NOTICE, '387 Режим TEST включен, команда на ворота подается всегда.');		
			
					$mpt->result ='OK';
				}			
				//============= даю в цикле команды на открытие ворот. Цикл сделан на случай потери связи, и был актуален для UDP
					$i=0;
					while($mpt->result !='OK' AND $i<2)// делать до 2 попыток
					{
						Log::instance()->add(Log::DEBUG, '155 Команда открыть ворота '.$cvs->box_ip.':'.$cvs->box_port.' выполнена неудачно: '.$mpt->result.' desc '.$mpt->edesc.'. timestamp '.microtime(true).'. Команда Открыть ворота повторяется еще раз, попытка '.$i.' time_from_start='.number_format((microtime(1) - $t1), 3));
						if(!Arr::get($config, 'debug')) $mpt->openGate($cvs->mode);// открыть ворота
						$i++;
					}
					//Log::instance()->add(Log::NOTICE, '004_150 Событие 50. Результат выполнения команды openGate '.$cvs->box_ip.':'.$cvs->box_port.' result='.$mpt->result.', desc='.$mpt->edesc.'  после '. $i .' попыток time_from_start='.number_format((microtime(1) - $t1), 3));		
				if($mpt->result == 'Err') 
				{
					Log::instance()->add(Log::NOTICE, '138 Событие 50. Не смог открыть ворота в течении 10 попыток. Видеокамера '.$cvs->cam.' ('.$direct.') ГРЗ '.$identifier->id.' контролер IP='.$cvs->box_ip.':'.$cvs->box_port.' Режим шлюза '.$cvs->mode.' Ответ '.$mpt->result.' edesc '.$mpt->edesc);		
				} else 
				{
					Log::instance()->add(Log::NOTICE, '004_64 Событие 50. Ответ контроллера после повторной команды '.$mpt->result.' edesc '.$mpt->edesc.'  после '. $i .' попыток.');	
					
						//Ворота открылись успешно, надо добавлять ГРЗ в таблицу inside в зависиммочти от направления движения
						
						$inside= new insideList;
						$inside->id_card=$identifier->id;
						$inside->id_pep=$identifier->id_pep;
						$inside->id_parking=$cvs->id_parking;
						
						if($cvs->isEnter==1) {// въезд
							$inside->addToInside();
							Log::instance()->add(Log::NOTICE, '378 добавляю в HL_inside :key id_pep :id_pep, id_parking :id_parking ', array(':key'=> $inside->id_card,':id_pep'=> $inside->id_pep,':id_parking'=> $inside->id_parking));
						} else { //это выезд
							//обработка для С гаражом и Без гаража - разная.
							//без гаража - надо только удалить этого id_pep из таблицы inside
							//а если с гаражом, то надо проверить присутсвие, и если пипел не на парковке, то удалять кого-нибудь.
							
					
							if(is_null($identifier->id_garage)) //если нет гаража
							{
									Log::instance()->add(Log::NOTICE, '397 :key это выезд без гаража', array(':key'=> $inside->id_card));	
									$inside->delFromInside();//если на парковке, то удаляю пипла по его id_pep
							} else {//если есть гараж
								if($identifier->checkInParking($cvs->id_parking))//внутри ли пипел?
								{
									Log::instance()->add(Log::NOTICE, '397 :key НА парковке', array(':key'=> $inside->id_card));
									$inside->delFromInside();//если на парковке, то удаляю пипла по его id_pep
								} else {
									Log::instance()->add(Log::NOTICE, '397 :key НЕ на парковке', array(':key'=> $inside->id_card));
										Log::instance()->add(Log::NOTICE, '423-0 Выехал Удаляю grz :grz id_pep=:id_pep id_garage=:id_garage id_parking = :id_parking, которого не было на стоянке. Удаляю кого нибудь из этого гаража на этой парковке.', array(
											':grz'=>$identifier->id,
											':id_pep'=>$identifier->id_pep,
											':id_garage'=>$identifier->id_garage,
											':id_parking'=>$cvs->id_parking,
											
											));
										$this->delAnyIdPepOnPlace($identifier->id_garage, $cvs->id_parking);//удаляю кого-нибудь

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
			 
				Log::instance()->add(Log::NOTICE, "244 Неизвестный код валидации ":code ". Ворота не открываются, на табло ничего не выводится ", array(':code'=>$cvs->code_validation));
			
			 
			 break;
		 } 
		
	}
	
	/**основной блок логики
		*/
		
		public function common($identifier, $id_gate)
		{
			$t1=microtime(true);	
			$cvs=new phpCVS($id_gate);
			$identifier=new Identifier($identifier);
			
			//Log::instance()->add(Log::NOTICE, '551-0 :key common identifier :data', array(':key'=>$identifier->id,':data'=>Debug::vars($identifier)));
			//Log::instance()->add(Log::NOTICE, '552-1 :key common cvs :data', array(':key'=>$identifier->id,':data'=>Debug::vars($cvs)));
				
			//готовлю класс для записи события.
			//единые для всех событий данные заполняю заранее.
			$events= new Events();
			$events->grz=$identifier->id;
			$events->id_gate=$cvs->id_gate;
			$events->is_enter=$cvs->isEnter;
			if(!is_null($identifier->id_garage)) $events->id_garage=$identifier->id_garage;//номер гаража
					
	//Фильтр от повтора
			  
			   if (Cache::instance()->get('grz_'.$identifier->id))
			   {
					// Данные найдены в кеше, не надо обрабатывать ГРЗ.
					$result=9;
					Log::instance()->add(Log::NOTICE, '240 Повторный прием идентификатора grz :grz в течении менее чем :delay_cvs. Обработка прекращена с кодом :result', 
						array(':grz'=>$identifier->id, ':result'=>$result, ':delay_cvs'=>Setting::get('delay_cvs', 120))); 
					// $events= new Events();
					 $events->eventCode=$result;
					// $events->grz=$identifier->id;
					// $events->id_gate=$cvs->id_gate;
					// $events->is_enter=$cvs->isEnter;
					$events->addEventRow();
					
					exit;
				
			   } else {
				   
				 //  Log::instance()->add(Log::NOTICE, '260 :key Первая отмета. Зафиксирую в мьютексе :name..', array(':key'=>$identifier->id, ':name'=>'grz_'.$identifier->id)); 
					
				   Cache::instance()->set('grz_'.$identifier->id, array('set_grz'=>1, 'key'=>$identifier->id), Setting::get('delay_cvs', 120));
				   Log::instance()->add(Log::NOTICE, '267 :key входной фильтр от повтора номера. нет мьютекс :name. Значит эту отметка не получали давно. Фиксирую в мьютексе и продолжаю обработку.', array(':key'=>$identifier->id, ':name'=>'grz_'.$identifier->id)); 
			  
			   }
			    
	// Фильтр проверка: не находится ли ворота во временной блокировке? Это актуально для реверсивных ворот
			   //в это время любые данные UHF и ГРЗ игнорируются.
			   if (Cache::instance()->get('gateBlock_'.$cvs->id_gate))
			   {
					$result=10;
					// Данные найдены в кеше, не надо обрабатывать ГРЗ.
					Log::instance()->add(Log::NOTICE, '600 :key найден мьютекс :name. Это значит, что ворота находятся во временной блокировке', array(':key'=>$identifier->id, ':name'=>'gateBlock_'.$cvs->id_gate)); 
					Log::instance()->add(Log::NOTICE, '601 Прием идентификатора :key от ворот :id_gate в момент встречного движена в реверсивных воротах. Обработка идентификатора прекращена с кодом :result', array(':key'=>$identifier->id, ':id_gate'=>$cvs->id_gate, ':result'=>$result)); 
					
					//$events= new Events();
					$events->eventCode=$result;
					//$events->grz=$identifier->id;
					//$events->id_gate=$cvs->id_gate;
					$events->addEventRow();
				
					exit;
				
			   }
			   
			   Log::instance()->add(Log::NOTICE, '246 :key не найден мьютекс :name. Значит ворота не в режиме блокировки.', array(':key'=>$identifier->id, ':name'=>'gateBlock_'.$cvs->id_gate)); 
			   //проверка: а не идут ли тут друг за другом идентификаторы, которые сразу попали в поле антенны?
			  
			  $_data=$this->getMutexIdentifier('gate_'.$cvs->id_gate); //получил номер предыдущего обработанного UHF, на КОТОРЫЙ УЖЕ ИМЕЕТСЯ РАЗРЕШЕНИЕ
			  /* формат мьютекса $_data=array(
							'key'=>$identifier->id,
							'id_pep'=>$identifier->id_pep,
							'id_garage'=>$identifier->id_garage,
							'id_gate'=>$cvs->id_gate,
							'isEnter'=>$cvs->isEnter,
							'isEnter'=>$cvs->isEnter,
							); */
							
			if(Arr::get($_data, 'id_gate') == $cvs->id_gate)
			{
				 
				//если попали сюда - значит, это "паровозик": попытка проезд в то время, пока ворота открыты от предыдущего проезда.
				//из "паровозика" можно выйти в случаях:
				// - если номер принадлежит ранее проехавшему id_pep (т.е. прочитан второй идентификатор проезждающей машины
				// - если гараж принадлежит ранее проехавшему автомобилю
				// - если номер не валиден либо не имеет право на проезд 12.08.2025 г.
				
				 Log::instance()->add(Log::NOTICE, '634-0 :key паровозик identifier :data', array(':key'=>$identifier->id,':data'=>Debug::vars($identifier)));
				 Log::instance()->add(Log::NOTICE, '634-1 :key паровозик cvs :data', array(':key'=>$identifier->id,':data'=>Debug::vars($cvs)));
		
				 $result=Events::ANALYSERBUSY;//код 7 - процесс занят
				 
				  Log::instance()->add(Log::NOTICE, '285 :key найден мьютекс :name :data', array(':key'=>$identifier->id, ':name'=>'gate_'.$cvs->id_gate, ':data'=>Debug::vars($_data)));
								
					if(Arr::get($_data, 'key') == $identifier->id)
					{
						//получен повторно идентификатор. Этого быть не должно, т.к. повтор фильтруется на входе.
						
					}
					
					if((Arr::get($_data, 'key') != $identifier->id) and (Arr::get($_data, 'id_pep') == $identifier->id_pep))
					{
						//номер другой, но владелец тот же самый. вот тут надо прекращать обработку.
						$result=11;
					//	$events= new Events();
						$events->eventCode=$result;
					//	$events->grz=$identifier->id;
					//	$events->id_gate=$cvs->id_gate;
						$events->addEventRow();
						 Log::instance()->add(Log::NOTICE, '651 :key номер другой, но владелец :id_pep тот же. Завершаю обработку exit;', array(':key'=>$identifier->id, ':id_pep'=>$identifier->id_pep));
						exit;
					}
					
					if((Arr::get($_data, 'id_garage') == $identifier->id_garage))
					{
						
						//Едет еще кто-то из того же гаража. Не обрабатывать!!!
						$result=8;
				//		$events= new Events();
						$events->eventCode=$result;
				//		$events->grz=$identifier->id;
				//		$events->id_gate=$cvs->id_gate;
						$events->addEventRow();
					 Log::instance()->add(Log::NOTICE, '664 :key номер другой, но гараж :id_garage тот же. Завершаю обработку exit;', array(':key'=>$identifier->id,':id_garage'=>$identifier->id_garage));

						exit;
					}
					
					
			}		
			//} else {
				Log::instance()->add(Log::NOTICE, '297 :key НЕ найден мьютекс :name. Значит на этих воротах идентификатор не обрабатывается в параллельном потоке.', array(':key'=>$identifier->id, ':name'=>'gate_'.$cvs->id_gate, ':data'=>Debug::vars($_data)));
				
					Log::instance()->add(Log::NOTICE, '274 :key start mainAnalysis.', array(':key'=>$identifier->id));
					//============== Главное! анализ!!! ==============================		
						$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
					//===================================================================
						Log::instance()->add(Log::NOTICE, '292 :key gate :gate garage :garage stop mainAnalysis с результатом :result.', array(':key'=>$identifier->id, ':result'=> $result, ':gate'=>$cvs->id_gate, ':garage'=>$identifier->id_garage));
				   
			 //  }
			   

			
			$cvs->code_validation=$result;//передаю в модель результат валидации
			$cvs->getMessForEvent($result);//формирую текстовое сообщение для табло
			
			//сохраняю идентификатора в кеше для защиты от повторной обработки
					
					
			//делаю набор условий для последующей обработки. Если результат 50 (можно проезжать), то жду 30 секунд.
				switch($result){
					case 81:
					case 50:
						$_data=array(
							'key'=>$identifier->id,
							'id_pep'=>$identifier->id_pep,
							'id_gate'=>$cvs->id_gate,
							'isEnter'=>$cvs->isEnter,
							'id_garage'=>$identifier->id_garage,
							);
						$this->setMutexIdentifier('gate_'.$cvs->id_gate, $_data ); //если проезд разрешен, то фиксирую этот ГРЗ в мьютексе.
						//Log::instance()->add(Log::NOTICE, '206 :key валидация прошла успешно, записал в mutex :name значение :data', array(':key'=>$identifier->id,':name'=>'gate_'.$cvs->id_gate, ':data'=>Debug::vars($_data)));
													
					break;
					default:
								
						break;
				}
					
//============================== ???		 			
			Log::instance()->add(Log::NOTICE, '318 :key mutex перед gateControl имеет значение  :mutex.', 
				array(
					':key'=>$identifier->id, 
					':mutex'=>Debug::vars($this->getMutexIdentifier('gate_'.$cvs->id_gate))));	
			 
	
					
			//мьтекс введен с целью недопустить фиксацию двух машиномест при получении сразу двух идентификаторов от одного автомобиля на въезде		
			//в этот момент в мьютексе должен находится последний (по времени) валидный идентификатор
			// если его значение совпадает с текущим обрабатываемым, значит, надо продолжать работу.
			// если не совпадает - значит, пока шла обработка этого идентификатора, по другому каналу пришел и был обработан другой идентификатор.			
			
			//Log::instance()->add(Log::NOTICE, '351 :key grz mutex свободен.', array(':key'=>$identifier->id));
//=========== запись события в журнал

				
		//$events= new Events();
		$events->eventCode=$cvs->code_validation;
		//$events->grz=$identifier->id;
		//$events->id_gate=$cvs->id_gate;
		$events->addEventRow();
		Log::instance()->add(Log::NOTICE, '342 :key save events code=:code , grz=:grz, gate=:gate', array(':key'=>$identifier->id, ':code'=>$events->eventCode, ':grz'=>$events->grz, ':gate'=>$events->id_gate));
		
//============== управление воротами ========================================			
			//перехожу к управлению воротами
		
		Model::factory('cvss')->gateControl($identifier, $cvs);
		Log::instance()->add(Log::NOTICE, "335 :key Stop gate=:gate, code_validation=:code, total_time=:tt", array(
			':key'=>$identifier->id, 
			':gate'=>$cvs->id_gate, 
			':code'=>$cvs->code_validation,
			':tt'=>number_format((microtime(1) - $t1), 3)
			));	
		//echo Debug::vars('419 обработку ГРЗ завершил.');exit;
		Log::instance()->add(Log::NOTICE, '337 end debug :id :data', array(':data'=>number_format((microtime(1) - $t1), 3), ':id'=>$identifier->id));
		
		return;
			
		}
		
		
		
		/**чтение значения  мьютекса
		* возвращает значение указанного мьютекса
		*
		*/
		public function getMutexIdentifier($name)
		{
			// Log::instance()->add(Log::NOTICE, '1127 mutex_'. $id_gate);	
			// Log::instance()->add(Log::NOTICE, '1128 mutex_'. $id_gate.' '. Cache::instance()->get('mutex_'. $id_gate));	
			return unserialize (Cache::instance()->get($name));
			
		}
		
		/**чтение мьютекса: свободен или занят
		* TRUE - значит, свободен.
		* FALSE  занят
		*
		*/
		public function isMutexFree($name)
		{
			if(Cache::instance()->get($name)) return false; //ресурс свободен, можно его использовать.
			return true;
			
		}
		
		
		
		/**Установка (захват) мьютекса
		*
		*
		*/
		//public function setMutexIdentifier($id_gate, $identifier)
		public function setMutexIdentifier($name, $data)
		{
			//Log::instance()->add(Log::NOTICE, '389 записал в mutex :name значения :data', array(':mame'=>$name, ':data'=>Debug::vars($data)));
			if (Cache::instance()->get($name))//если true, значит он уже обрабатывается. Обработка полученного идентификатора надо прекращать.
			{
				return true;
			} else {
				Cache::instance()->delete($name);
				Cache::instance()->set($name, serialize($data), Setting::get('delay_cvs', 20));
				return false;
				
			}
			
		}
		
		/** освобождение мьютекса
		*
		*/
		
		public function resetMutexIdentifier($name)
		{
		
			//Удаление не требуется, т.к. кеш имеет срок жизни
			
		}
		
		/**расчет времени. К текущему времени прибавляется указанное количество секунд и возвращается значение в текстововм формате.
		*/
		
		public function calcTime($delay)
		{
			return date('H:i:s', (time() + $delay));
			
		}
		
		
		
		/**фукнция saveKeyFromGate сохраняет данные, полученные от UHF или ГРЗ, с указанием номера ворот.
		* данные - это массив: что получили - то и выводим.
		* если id_pep для добавлямего идентификатора уже существует, то вставка не производится.
		*/
		public function saveKeyFromGate($id_pep, $identifier, $id_gate, $result)
		{
			
        
			// Проверяем существует ли запись
			$exists = DB::select('id_pep')
				->from('keys')
				->where('id_pep', '=', $id_pep)
				->execute($this->db)
				->count() > 0;
			
			if ($exists) {
				// Обновляем существующую запись
			  
				Log::instance()->add(Log::NOTICE, '856 :key повторное чтение для уже того же id_pep :id_pep в id_gate :id_gate. Запись не добавляется во временный буфер.', array(':key'=>$identifier,':id_pep'=>$id_pep, ':id_gate'=>$id_gate));
				return 0;
				
			} else {
				// Вставляем новую запись
			   /*  DB::insert($table, array_keys($data))
					->values(array_values($data))
					->execute(); */
				$type='--';
				$timestamp= (int) microtime(true);
				//Log::instance()->add(Log::NOTICE, '851 :key id_gate :data', array(':key'=>$identifier,':data'=>Debug::vars($gate, $key, $type, $timestamp)));
				
				$query = DB::insert('keys', array('id_pep', 'gate', 'key', 'type', 'timestamp', 'result'))
						->values(array($id_pep, $id_gate, $identifier, $type, $timestamp, $result))
						;
			return $query->execute($this->db);
			}
  
		}
			

	
		/**фукнция getKeyFromGate извлекает данные о последних полученных UHF или ГРЗ
		* @input $id_gate - номер ворот, которые надо обрабатывать.
		* данные - это массив: что получили - то и выводим.
		*/
		public function getKeyFromGate($id_gate)
		{
			
			$evCode=array(events::WOK,
				events::OK,
				events::G_OK_PLACE,
				events::G_WOK_PEP,
				events::G_WOK,
				events::G_OK,
				events::G_OK_2);//коды событий, по которым разрешен проезд, когда надо открывать ворота
			
			$deltaTbefor=20;//в секундах - за какое время до текущего момента искать идентификаторы
			  $keys = DB::select('key', 'id_pep', 'result')
				->from('keys')
				->where('gate', '=', $id_gate)
				->where('result', 'IN', $evCode)
				->and_where('timestamp', '>', (int)(microtime(true) - $deltaTbefor))
				->distinct(TRUE)
				->limit(1)
				->order_by('timestamp', 'asc')
				->execute($this->db);
		
			//Log::instance()->add(Log::NOTICE, '877 :data', array(':data'=>Debug::vars($id_gate, $keys)));
			
			
			return $keys;
			
		}
		
		
		/**фукнция delKeyFromGate удаляет данные для указанного gate
		* @input $id_gate - номер ворот, которые надо обрабатывать.
		* данные - это массив: что получили - то и выводим.
		*/
		public function delKeyFromGate($id_gate)
		{
			
			$count = DB::delete('keys')
			->where('gate', '=', $id_gate)
			->execute($this->db);
				
			Log::instance()->add(Log::NOTICE, '895 :data', array(':data'=>Debug::vars($count)));
			
			
			return $count;
			
		}
		
		
		/**фукнция delIdPepFromGate удаляет данные для указанного id_pep
		* @input $id_gate - номер ворот, которые надо обрабатывать.
		* данные - это массив: что получили - то и выводим.
		*/
		public function delIdPepFromGate($id_pep)
		{
			
			$count = DB::delete('keys')
			->where('id_pep', '=', $id_pep)
			->execute($this->db);
				
			Log::instance()->add(Log::NOTICE, '938 :data', array(':data'=>Debug::vars($count)));
			
			
			return $count;
			
		}
		
		
		
		
		/**фукнция delOldKeyFromGate удаляет записи старше указанного периода.
		* @input $delay период в секундах, свыше которого записи надо удалить.
		* данные - это массив: что получили - то и выводим.
		*/
		public function delOldKeyFromGate($delay)
		{
			
			$count = DB::delete('keys')
			->where('timestamp', '<', (int)(microtime(true) - $delay))
			->execute($this->db);
				
			Log::instance()->add(Log::NOTICE, '895 :data', array(':data'=>Debug::vars($count)));
			
			
			return $count;
			
		}
		
		
		/** 22.08.2025 При получении идентификатора выполняется проверка: как давно орабатывался этот же идентификатор?
		* если интервал между получениями больше чем Т1, то возвращается true.
		* если интервал между получениями меньше чем Т1, то возвращается false.
		*/
		
		public function repeatFilter($identifier, $cacheName)
		{
			  $delay=Setting::get('delay_cvs', 120);
			  $name=$cacheName.'_'.$identifier;
			  if (Cache::instance()->get($name))
			   {
					// Данные найдены в кеше, не надо обрабатывать ГРЗ.
					$result=9;
					return false;
				
			   } else {
				   
				 //  Log::instance()->add(Log::NOTICE, '260 :key Первая отмета. Зафиксирую в мьютексе :name..', array(':key'=>$identifier->id, ':name'=>'grz_'.$identifier->id)); 
					
				   Cache::instance()->set($name, array('set_key'=>1, 'key'=>$identifier), $delay);
				   //Log::instance()->add(Log::NOTICE, '889 :key входной фильтр от повтора номера. Эту отметка не получали давно. Фиксирую в мьютексе и продолжаю обработку.', array(':key'=>$identifier, ':name'=>$cacheName.'_'.$identifier)); 
				return true;
			   }
		
		}
		
		
		
		
		
}
