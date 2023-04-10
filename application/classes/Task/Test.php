    <?php defined('SYSPATH') or die('No direct script access.');
     
    /**
     * Test class
     *
     * @author novisasha
     */
     
    class Task_Test extends Minion_Task {
        
        protected function _execute(array $params)
        {
            Minion_CLI::write('Hello World!');
        }
    }