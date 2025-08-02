<?php defined('SYSPATH') or die('No direct access allowed!'); 
  
class SampleTest extends Kohana_UnitTest_TestCase 
{ 
    
	
	public function test_add() 
    {
        $this->assertEquals(2, 1+1); 
    } 
	
	
	
	public function test_add2() 
    {
        $this->assertEquals(5, 1+4); 
    } 
	
/*	
	function providerStrLen()
    {
        return array(
            array('One set of testcase data', 24),
            array('This is a different one', 23),
        );
    }
 */
    /**
     * @dataProvider providerStrLen
     */
/*   
   function testStrLen($string, $length)
    {
        $this->assertSame(
            $length,
            strlen($string)
        );
    }
	*/
}