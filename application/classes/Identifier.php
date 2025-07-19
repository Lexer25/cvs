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
	
   
	

  
    public function __construct($id)// id - номер идентификатора
    {
       $sql='select c.id_pep from card c
			where c.id_card=\''.$id.'\'';

					
	   $query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('ID_PEP');
		if(is_numeric($query))	{
					$this->status='VALID'; 
					$this->id_garage=1; 
					$this->id_pep=$query;
		} else {
			$this->status='UNKNOWNCARD';

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
		  
}
