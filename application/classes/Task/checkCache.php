    <?php defined('SYSPATH') or die('No direct script access.');
     
    /**
     * Test class
     *
     * C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=checkCache
     */
     
    class Task_checkCache extends Minion_Task {
        
        protected function _execute(array $params)
        {
            
				
			Model_cvss::checkTimeout('Model_cvss::checkTimeoutModel_cvss::checkTimeoutModel_cvss::checkTimeout');
        }
    }