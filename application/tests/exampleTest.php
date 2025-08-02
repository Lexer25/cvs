<?php 
	
	class ExampleTest extends UnitTest_TestCase 
{
    public function testInsert()
    {
        // Ваш тестовый код здесь
        // Например:
		//$tstype = Model::factory('Tserver')->getTSListForServer();
		$tstype = Model::factory('Door')->test();
        $this->assertNotEmpty($tstype);
    }
}