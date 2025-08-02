<?php 

/** 02.08.2025 автоматизированное тестироание модуля gateControl
*
* c:\xampp\php\phpunit.bat C:\xampp\htdocs\cvs\application\tests\cvsTest_2_gateControl.php
* выполняется проверка по результатам валидации.
*/
  
class cvsTest_2_gateControl extends UnitTest_TestCase 
{

	function providerAccessName ()
	{
		//список категорий доступа
		return array (
			array(77),
			array(78),
			array(79)); 
	}
	
	

	//очищаю таблицу inside
	function testClearInside ()
	{
		$this->assertEquals(0, insideList::clearInside()); 
		
	}
	
	
	/**
     * @dataProvider providerAccessName
     */
	 
	 
	/**2.08.2025 забираю все категории доступа
	*
	*/
	function testDelAccess ($a)
	{
		
		$id=new Identifier('3665363');
		//echo Debug::vars('43', $id);exit;
		$this->assertEquals(0, $id->delIdentifierInSSaccessUser($a)); 
	}
	
	//проверяю, что никуда не пускает
	
	
	function providerBBB ()
	{
		//массивы проверяемы значений
		// ожидаемый результат номер ворот номер карты.
		//тестирование въездов. все должно быть ОК
		return array (
			array (50, 3, '3665363'),
			array (6, 3, '3665363'),
			array (65, 2, '3665363'),
			array (65, 5, '3665363'),
			array (50, 4, '3665363'),
			array (50, 4, '3665363'),
			array (50, 4, '3665363'),
			array (65, 7, '3665363'),
			array (65, 6, '3665363'),
			array (50, 3, '3665363'),
			
		); 	
		
		/* return array (
			array (50, 3, 'D014DD71'),
			array (6, 3, 'D014DD71'),
			array (65, 2, 'D014DD71'),
			array (65, 5, 'D014DD71'),
			array (50, 4, 'D014DD71'),
			array (50, 4, 'D014DD71'),
			array (50, 4, 'D014DD71'),
			array (65, 7, 'D014DD71'),
			array (65, 6, 'D014DD71'),
			array (50, 3, 'D014DD71'),
		);  */
	}
	
	/**
     * @dataProvider providerBBB
     */
	
	public function testBBB($a, $b, $c) 
    {
		$cvs=new phpCVS($b);//Ворота номер 3
		$identifier=new Identifier($c);
		
		$test=Model::factory('cvss');
		$cvs->code_validation=$test->mainAnalysis($identifier,  $cvs);//передаю в модель результат валидации
		$test->gateControl($identifier,  $cvs);
		//echo Debug::vars('78', $cvs->code_validation);exit;
		$this->assertEquals($a, $cvs->code_validation); 
		
    } 


	
}