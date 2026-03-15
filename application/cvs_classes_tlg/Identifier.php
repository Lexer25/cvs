<?php

/*
класс Identifier описывает свойств и методы полученного идентификатора:
- если в базе?
- Активен ли?
- срок действия?
- есть ли разрешение по категории доступа в указанной точке проезда?
*/
class Identifier
{
    public $id;        //номер идентификатора
    public $status = null;        //номер идентификатора
    public $id_pep = null;        //ФИО владельца
    public $id_garage = null;        //номер гаража, куда входит идентификатор
    public $id_cardtype = null;        //номер гаража, куда входит идентификатор
	
   
		const UNKNOWNCARD=1;
		const DISABLEDCARD=2;
		const CARDEXPIRED=3;
		const DISABLEDUSER=4;
		const ACCESSDENIED=5;
		const VALID=0;

  
    public function __construct($id)// id - номер идентификатора
    {
       $sql='select c.id_pep, c.id_cardtype, hlo.id_garage from card c
				join people p on p.id_pep=c.id_pep
				left join hl_orgaccess hlo on hlo.id_org=p.id_org
				where c.id_card=\''.$id.'\'';

		//echo Debug::vars('31', $sql);exit;	
//Log::instance()->add(Log::NOTICE, '35 '.$sql); exit;		
	   $query = Arr::flatten(DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array()
			);
		$this->id=$id; 
		if(is_numeric(Arr::get($query, 'ID_PEP')))	{
					
					$this->status=self::VALID; 
					$this->id_garage=Arr::get($query, 'ID_GARAGE');
					$this->id_pep=Arr::get($query, 'ID_PEP');
					$this->id_cardtype=Arr::get($query, 'ID_CARDTYPE');
		} else {
			$this->status=self::UNKNOWNCARD;

		}			
			return;
			
    }


			public function checkInParking($id_parking)//проверка: находится ли этот идентификатор на id_parkung?
			{
			   
						$sql='select count(*) from hl_inside hli
					where hli.id_pep='.$this->id_pep.'
					and hli.counterid='.$id_parking;
					//echo Debug::vars('52', $sql);exit;
				$query = DB::query(Database::SELECT, $sql)
					->execute(Database::instance('fb'))
					->get('COUNT');
				if($query==0)
				{
					return false;
				} else {
					return true;
				}
			}
			
			
			public function checkInPlace()//проверка: находится ли этот идентификатор на любой парковке?
			{
			   
						$sql='select count(*) from hl_inside hli
					where hli.id_pep='.$this->id_pep;
					//echo Debug::vars('52', $sql);exit;
				$query = DB::query(Database::SELECT, $sql)
					->execute(Database::instance('fb'))
					->get('COUNT');
				if($query==0)
				{
					return false;
				} else {
					return true;
				}
			}
			
			
			
			
			
			public function checkIdentifier()
			{
		$identifier=new Identifier($grz);
		switch($identifier->status){
			case 'UNKNOWNCARD':
				//запись в БД что карта неизвестна, завершение работы
				echo Debug::vars('85 UNKNOWNCARD');
				$event->eventCode=events::UNKNOWNCARD;
				$event->grz=$grz;
				$event->addEventRow();
				exit;
			break;
			case	 'DISABLEDCARD':
				//запись в БД что карта DISABLEDCARD
				exit;
			break;
			case	 'DISABLEDUSER':
				//запись в БД что карта DISABLEDUSER
				exit;
			break;
			case	 'CARDEXPIRED':
				//запись в БД что карта DISABLEDUSER
				exit;
			break;
			default:
				//идентификатор валидный, продолжаем работу
			break;
		}
		
		}
		
		/**2.08.2025 добавление категории доступа для владельца текущего идентификатора		
		*@input accessname - id категории доступа, которую надо добавить.
		*/
		 public function  addIdentifierInSSaccessUser($accessname)
		 {
			 $_data=array(
				':ID_DB'=>1,
				':ID_PEP'=>$this->id_pep,
				':ID_ACCESSNAME'=>$accessname,
				':USERNAME'=>'\'phptest\''
			);
			 $sql=__('INSERT INTO SS_ACCESSUSER (ID_DB,ID_PEP,ID_ACCESSNAME,USERNAME) VALUES (:ID_DB,:ID_PEP,:ID_ACCESSNAME,:USERNAME)', $_data);
			 try
				{
					$query = DB::query(Database::INSERT, iconv('UTF-8', 'CP1251',$sql))
					->execute(Database::instance('fb'));
					return 0;
				} catch (Exception $e) {
					Log::instance()->add(Log::DEBUG, 'Line 145 '. $e->getMessage());
					return 1;
				}
		 }
		 
		 /**2.08.2025 удаление категории доступа для владельца текущего идентификатора		
		*@input accessname - id категории доступа, которую надо добавить.
		*/
		 public function delIdentifierInSSaccessUser($accessname)
		 {
			 $_data=array(
				':ID_DB'=>1,
				':ID_PEP'=>$this->id_pep,
				':ID_ACCESSNAME'=>$accessname,
				':USERNAME'=>'\'phptest\''
			);
			 $sql=__('delete from ss_accessuser ssa where ssa.id_pep=:ID_PEP and ssa.id_accessname=:ID_ACCESSNAME', $_data);
			 try
				{
					$query = DB::query(Database::DELETE, iconv('UTF-8', 'CP1251',$sql))
					->execute(Database::instance('fb'));
					return 0;
				} catch (Exception $e) {
					Log::instance()->add(Log::DEBUG, 'Line 168 '. $e->getMessage());
					return 1;
				}
		 }
		 
		 
		 
}
