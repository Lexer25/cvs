<?php 
  
class qq22Test extends UnitTest_TestCase 
{ 
	function providerBBB ()
	{
		return array (
			array (6, 2),
		); 
	}
	
	/**
     * @dataProvider providerBBB
     */
	
	public function testBBB($a, $b) 
    {
		session_start();
		$_SESSION['skud_number']=1;
		
	      
		$my= Model::factory('Check')->GetIdServer($b);
		$this->assertEquals($a, $my); 
		
    } 
	
	
	
	
}