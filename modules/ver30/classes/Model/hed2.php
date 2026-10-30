<?php defined('SYSPATH') OR die('No direct access allowed.');

//23.08.2025 этот файл создан для отладки алгоритма контроллеров.
//и последующего переноса алгоритма в action

class Model_hed2 extends Model {
	
	
	
	/** основная процедура обработки входящих идентификаторв при использовании индуктивной петли
	*/
	public function common2($key, $id_gate)
	{
		$cvs=new phpCVS($id_gate);
		$identifier=new Identifier($key);
			
		
		$cvss=Model::factory('cvss');
		$cvss->delOldKeyFromGate(20);//удалил старые записи
		$events= new Events();
			$events->grz=$identifier->id;
			$events->id_gate=$cvs->id_gate;
			$events->is_enter=$cvs->isEnter;
			if(!is_null($identifier->id_garage)) $events->id_garage=$identifier->id_garage;//номер гаража
			
			
		if(!$cvss->repeatFilter($key, $id_gate))
		{
		  // повтор номера. выхожу из обработки
		  Log::instance()->add(Log::NOTICE, '82 :key Повторный прием идентификатора key :key. Обработка прекращается.', 
						array(':key'=>$identifier->id)); 
			$result=9;
			$events->eventCode=$result;
			$events->addEventRow();
			
			
		} else {
			//продолжаю обработку полученного идентификатора
			Log::instance()->add(Log::NOTICE, '89 :key входной фильтр от повтора номера. Прием идентификатора key :key. Продолжаю обработку.', array(':key'=>$identifier->id)); 
			
			Log::instance()->add(Log::NOTICE, '93 :key start mainAnalysis.', array(':key'=>$identifier->id));
	//============== Главное! анализ!!! ==============================		
			
			$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
	//===================================================================
			Log::instance()->add(Log::NOTICE, '292 :key gate :gate garage :garage stop mainAnalysis с результатом :result.', array(':key'=>$identifier->id, ':result'=> $result, ':gate'=>$cvs->id_gate, ':garage'=>$identifier->id_garage));
		}	
		$events->eventCode=$result;
			$events->addEventRow();
		$cvs->code_validation=$result;		
		 //Log::instance()->add(log::NOTICE, '35-0 :key common identifier :data', array(':key'=>$identifier->id,':data'=>debug::vars($identifier)));
		 //log::instance()->add(Log::NOTICE, '35-1 :key common cvs :data', array(':key'=>$identifier->id,':data'=>Debug::vars($cvs)));
		// Log::instance()->add(Log::NOTICE, '35-2 :key id_gate :data', array(':key'=>$identifier->id,':data'=>$id_gate));
				  
		
		
		//$gateIsOpen=true;//отладка для случая Ворота открыты, и этот же режим для работы без петли
		$gateIsOpen=false;//отладка для случая Ворота закрыты
		
		//если ворота открыты, то сразу же перехожу к разрешению проезда.
		if($gateIsOpen==true) 
		{
			Log::instance()->add(Log::NOTICE, '59 :key ворота :gate открыты.', array(':key'=>$identifier->id, ':result'=> $result, ':gate'=>$cvs->id_gate, ':garage'=>$identifier->id_garage));
	
			Model::factory('cvss')->gateControl($identifier, $cvs);
		} else {
			//ворота закрыты, сохраняю результат в БД локальную
			Log::instance()->add(Log::NOTICE, '59 :key ворота :gate закрты, сохраняю данные в локальную БД.', array(':key'=>$identifier->id, ':result'=> $result, ':gate'=>$cvs->id_gate, ':garage'=>$identifier->id_garage));
	
			$cvss->saveKeyFromGate($identifier->id_pep, $identifier->id, $id_gate, $result);
			
		}
	
		
	}
	
	//обработка сигнала от петли
	public function loop($id_gate)
	{
		//получаю данных из локальной базы: какие идентификаторы получал?
		$cvss=Model::factory('cvss');
		//$cvss->delOldKeyFromGate(20);//удалил старые записи
		$data=$cvss->getKeyFromGate($id_gate);
		Log::instance()->add(Log::NOTICE, '79 ворота :gate данные из локальной БД :data.', array(':gate'=>$id_gate, ':data'=>Debug::vars($data,$data->count())));
		
		Log::instance()->add(Log::NOTICE, '86 :data.', array(':gate'=>$id_gate, ':data'=>Debug::vars($data->current())));
		Log::instance()->add(Log::NOTICE, '87 :data.', array(':gate'=>$id_gate, ':data'=>Arr::get($data->current(), 'id_pep')));
		$cvs=new phpCVS($id_gate);
		
		if($data->count()==0)
		{
			//автомобиль не распознан
			Log::instance()->add(Log::NOTICE, '83 ворота :gate автомобиль НЕ распознан, ворота не открывать :data.', array(':gate'=>$id_gate, ':data'=>Debug::vars($data->count())));
			//надо вывести на табло надпись "Не распознан".
			
 			
		} else {
			//есть распознанные автомобили. Варианты:
			//1. идентификаторы разные, но одинаковый id_pep - это у автомобиля прочитались и ГРЗ, и UHF под лобовым стеклом. Добавлять или удалять из inside
			//2. идентификаторы разные, id_pep тоже разные, гаражи разные - это стоят две (и более автомашины) перед воротами. 
			//Они проедут оба, надо добавлять в inside при въезд и удалять из inside при выезде
			//3. идентификаторы разные, id_pep тоже разные, но гаражи одинаковые - едут друг за другом два владельца гаража.
			Log::instance()->add(Log::NOTICE, '101 ворота :gate автомобиль РАСПОЗНАН, ворота открывать :data.', array(':gate'=>$id_gate, ':data'=>Debug::vars($data->count())));
			Log::instance()->add(Log::NOTICE, '105 удаляю из буфера id_pep :data.', array(':data'=>Arr::get($data->current(), 'id_pep')));
			$_count=$cvss->delIdPepFromGate(Arr::get($data->current(), 'id_pep'));//удаляю из очереди, как уже проехавшего
			Log::instance()->add(Log::NOTICE, '90 ворота :gate автомобиль РАСПОЗНАН, ворота открывать, удалено :_count записей', array(':gate'=>$id_gate, ':_count'=>$_count));
			$identifier=new Identifier(Arr::get($data->current(), 'id_pep'));
			
			$cvss->gateControl($identifier, $cvs);
		
		}
		
		
		
		return;
		
	}
	
	
	
	
}
