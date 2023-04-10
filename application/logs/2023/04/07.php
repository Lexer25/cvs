<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2023-04-07 14:54:32 --- CRITICAL: Database_Exception [ -902 ]: SQLSTATE[08001] SQLConnect: -902 [Gemini InterBase ODBC Driver][INTERBASE]Unable to complete network request to host "26.98.93.81". Failed to establish a connection. unknown Win32 error 10060.  ~ MODPATH\database\classes\Kohana\Database\PDO.php [ 59 ] in C:\xampp\htdocs\cvs\modules\database\classes\Kohana\Database\PDO.php:136
2023-04-07 14:54:32 --- DEBUG: #0 C:\xampp\htdocs\cvs\modules\database\classes\Kohana\Database\PDO.php(136): Kohana_Database_PDO->connect()
#1 C:\xampp\htdocs\cvs\modules\database\classes\Kohana\Database\Query.php(251): Kohana_Database_PDO->query(1, 'select hlp.tabl...', false, Array)
#2 C:\xampp\htdocs\cvs\application\classes\phpCVS.php(37): Kohana_Database_Query->execute(Object(Database_PDO))
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(49): phpCVS->__construct('3')
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_exec()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\modules\database\classes\Kohana\Database\PDO.php:136
2023-04-07 14:55:25 --- CRITICAL: Database_Exception [ -902 ]: SQLSTATE[08001] SQLConnect: -902 [Gemini InterBase ODBC Driver][INTERBASE]Unable to complete network request to host "26.98.93.81". Failed to establish a connection. unknown Win32 error 10060.  ~ MODPATH\database\classes\Kohana\Database\PDO.php [ 59 ] in C:\xampp\htdocs\cvs\modules\database\classes\Kohana\Database\PDO.php:136
2023-04-07 14:55:25 --- DEBUG: #0 C:\xampp\htdocs\cvs\modules\database\classes\Kohana\Database\PDO.php(136): Kohana_Database_PDO->connect()
#1 C:\xampp\htdocs\cvs\modules\database\classes\Kohana\Database\Query.php(251): Kohana_Database_PDO->query(1, 'select hlp.tabl...', false, Array)
#2 C:\xampp\htdocs\cvs\application\classes\phpCVS.php(37): Kohana_Database_Query->execute(Object(Database_PDO))
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(49): phpCVS->__construct('3')
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_exec()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\modules\database\classes\Kohana\Database\PDO.php:136
2023-04-07 14:56:05 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: content ~ APPPATH\views\template.php [ 62 ] in C:\xampp\htdocs\cvs\application\views\template.php:62
2023-04-07 14:56:05 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\views\template.php(62): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 62, Array)
#1 C:\xampp\htdocs\cvs\system\classes\Kohana\View.php(62): include('C:\\xampp\\htdocs...')
#2 C:\xampp\htdocs\cvs\system\classes\Kohana\View.php(359): Kohana_View::capture('C:\\xampp\\htdocs...', Array)
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller\Template.php(44): Kohana_View->render()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(87): Kohana_Controller_Template->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\views\template.php:62