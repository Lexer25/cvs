<?php 

/** 02.08.2025 автоматизированное тестироание парковочной системы
*
* c:\xampp\php\phpunit.bat C:\xampp\htdocs\cvs\application\tests\cvsTest_1_UHF.php
* выполняется проверка разрешения на проезд при изменении категорий доступа.
* в ходе выполнения теста в БД СКУД добавляются различные категории доступа, после чего выполняется 
* проверка для указанного идентификатора UHF
* ожидается, что въезд и выезд разрешены только в те ворота, которые входят в разрешенную категорию доступа.
*/
  
class cvsTest_1_UHF extends UnitTest_TestCase 
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
		
		$id=new Identifier('8696164');
		//$id->addIdentifierInSSaccessUser(77);
		$this->assertEquals(0, $id->delIdentifierInSSaccessUser($a)); 
	}
	
	//проверяю, что никуда не пускает
	
	
	function providerBBB ()
	{
		//массивы проверяемы значений
		// ожидаемый результат номер ворот номер карты.
		//тестирование въездов. все должно быть ОК
		return array (
			array (65, 3, '8696164'),
			array (65, 2, '8696164'),
			array (65, 5, '8696164'),
			array (65, 4, '8696164'),
			array (65, 7, '8696164'),
			array (65, 6, '8696164'),
			
		); 
	}
	
	/**
     * @dataProvider providerBBB
     */
	
	public function testBBB($a, $b, $c) 
    {
		$cvs=new phpCVS($b);//Ворота номер 3
		$identifier=new Identifier($c);
		$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
		$this->assertEquals($a, $result); 
		
    } 

//===================== 3.1 ======================================
	
	/**2.08.2025 Добавляю разрешение на 3.1
	*
	*/
	function testAddAccess ($accessname=77)
	{
		$id=new Identifier('8696164');
		$this->assertEquals(1, $id->addIdentifierInSSaccessUser($accessname)); 
	}
	
	function provider3_1 ()
	{
		
		return array (
			array (50, 3, '8696164'),
			array (65, 2, '8696164'),
			array (65, 5, '8696164'),
			array (50, 4, '8696164'),
			array (65, 7, '8696164'),
			array (65, 6, '8696164'),
			
		); 
	}
	
	/**
     * @dataProvider provider3_1
     */
	public function test3_1($a, $b, $c) 
    {
		$cvs=new phpCVS($b);//Ворота номер 3
		$identifier=new Identifier($c);
		$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
		$this->assertEquals($a, $result); 
		
    } 

//==================== 3.2 =======================================
	 
	 
	
	/**2.08.2025 удаление категории доступа 3.2
	*
	*/
	function testDelAccess31 ($accessname=77)
	{
		
		$id=new Identifier('8696164');
		//$id->addIdentifierInSSaccessUser(77);
		$this->assertEquals(0, $id->delIdentifierInSSaccessUser($accessname)); 
	}
	/**2.08.2025 Добавляю разрешение на 3.2
	*
	*/
	function testAddAccess32 ($accessname=78)
	{
		$id=new Identifier('8696164');
		$this->assertEquals(1, $id->addIdentifierInSSaccessUser($accessname)); 
	}
	
	function provider3_2 ()
	{
		
		return array (
			array (65, 3, '8696164'),
			array (50, 2, '8696164'),
			array (65, 5, '8696164'),
			array (65, 4, '8696164'),
			array (50, 7, '8696164'),
			array (65, 6, '8696164'),
			
		); 
	}
	
	/**
     * @dataProvider provider3_2
     */
	public function test3_2($a, $b, $c) 
    {
		$cvs=new phpCVS($b);//Ворота номер 3
		$identifier=new Identifier($c);
		$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
		$this->assertEquals($a, $result); 
		
    } 
//==================== 3.3 =======================================
	 
	 
	
	/**2.08.2025 удаление категории доступа 3.3
	*
	*/
	function testDelAccess33 ($accessname=78)
	{
		
		$id=new Identifier('8696164');
		//$id->addIdentifierInSSaccessUser(77);
		$this->assertEquals(0, $id->delIdentifierInSSaccessUser($accessname)); 
	}
	/**2.08.2025 Добавляю разрешение на 3.3
	*
	*/
	function testAddAccess33 ($accessname=79)
	{
		$id=new Identifier('8696164');
		$this->assertEquals(1, $id->addIdentifierInSSaccessUser($accessname)); 
	}
	
	function provider3_3 ()
	{
		
		return array (
			array (65, 3, '8696164'),
			array (65, 2, '8696164'),
			array (50, 5, '8696164'),
			array (65, 4, '8696164'),
			array (65, 7, '8696164'),
			array (50, 6, '8696164'),
			
		); 
	}
	
	/**
     * @dataProvider provider3_3
     */
	public function test3_3($a, $b, $c) 
    {
		$cvs=new phpCVS($b);//Ворота номер 3
		$identifier=new Identifier($c);
		$result=Model::factory('cvss')->mainAnalysis($identifier,  $cvs);
		$this->assertEquals($a, $result); 
		
    } 
	
}