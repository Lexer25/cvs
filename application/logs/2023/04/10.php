<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2023-04-10 10:39:54 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: mtp ~ APPPATH\classes\Controller\Dashboard.php [ 28 ] in C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php:28
2023-04-10 10:39:54 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(28): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 28, Array)
#1 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#6 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#7 {main} in C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php:28
2023-04-10 10:50:33 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: mpt ~ APPPATH\classes\Controller\Dashboard.php [ 19 ] in C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php:19
2023-04-10 10:50:33 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(19): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 19, Array)
#1 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#6 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#7 {main} in C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php:19
2023-04-10 10:50:43 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _answer ~ APPPATH\classes\Controller\Dashboard.php [ 25 ] in C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php:25
2023-04-10 10:50:43 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 25, Array)
#1 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#6 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#7 {main} in C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php:25
2023-04-10 10:51:00 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _answer ~ APPPATH\classes\phpMPT.php [ 126 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:126
2023-04-10 10:51:00 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(126): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 126, Array)
#1 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#2 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#3 [internal function]: Kohana_Controller->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#7 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#8 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:126
2023-04-10 11:05:36 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: content ~ APPPATH\views\template.php [ 62 ] in C:\xampp\htdocs\cvs\application\views\template.php:62
2023-04-10 11:05:36 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\views\template.php(62): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 62, Array)
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
2023-04-10 11:07:17 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _len ~ APPPATH\classes\phpMPT.php [ 141 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:141
2023-04-10 11:07:17 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(141): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 141, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(131): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(172): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(81): phpMPT->openGate('1')
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_exec()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:141
2023-04-10 11:07:24 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _len ~ APPPATH\classes\phpMPT.php [ 141 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:141
2023-04-10 11:07:24 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(141): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 141, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(131): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:141
2023-04-10 11:12:21 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected '}', expecting ']' ~ APPPATH\classes\phpMPT.php [ 148 ] in file:line
2023-04-10 11:12:21 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 11:19:42 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected ')' ~ APPPATH\classes\phpMPT.php [ 150 ] in file:line
2023-04-10 11:19:42 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 11:20:07 --- CRITICAL: ErrorException [ 8 ]: Use of undefined constant bcc - assumed 'bcc' ~ APPPATH\classes\phpMPT.php [ 159 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:159
2023-04-10 11:20:07 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(159): Kohana_Core::error_handler(8, 'Use of undefine...', 'C:\\xampp\\htdocs...', 159, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(131): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:159
2023-04-10 11:23:08 --- CRITICAL: ErrorException [ 2 ]: Missing argument 2 for phpMPT::bcc(), called in C:\xampp\htdocs\cvs\application\classes\phpMPT.php on line 123 and defined ~ APPPATH\classes\phpMPT.php [ 95 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:95
2023-04-10 11:23:08 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(95): Kohana_Core::error_handler(2, 'Missing argumen...', 'C:\\xampp\\htdocs...', 95, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(123): phpMPT->bcc(Array)
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(132): phpMPT->make_binary_command()
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:95
2023-04-10 11:24:42 --- CRITICAL: ErrorException [ 2 ]: Missing argument 2 for phpMPT::bcc(), called in C:\xampp\htdocs\cvs\application\classes\phpMPT.php on line 123 and defined ~ APPPATH\classes\phpMPT.php [ 95 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:95
2023-04-10 11:24:42 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(95): Kohana_Core::error_handler(2, 'Missing argumen...', 'C:\\xampp\\htdocs...', 95, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(123): phpMPT->bcc(Array)
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(132): phpMPT->make_binary_command()
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:95
2023-04-10 11:24:43 --- CRITICAL: ErrorException [ 2 ]: Missing argument 2 for phpMPT::bcc(), called in C:\xampp\htdocs\cvs\application\classes\phpMPT.php on line 123 and defined ~ APPPATH\classes\phpMPT.php [ 95 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:95
2023-04-10 11:24:43 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(95): Kohana_Core::error_handler(2, 'Missing argumen...', 'C:\\xampp\\htdocs...', 95, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(123): phpMPT->bcc(Array)
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(132): phpMPT->make_binary_command()
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:95
2023-04-10 11:25:40 --- CRITICAL: ErrorException [ 8 ]: Undefined offset: 2 ~ APPPATH\classes\phpMPT.php [ 101 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:101
2023-04-10 11:25:40 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(101): Kohana_Core::error_handler(8, 'Undefined offse...', 'C:\\xampp\\htdocs...', 101, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(123): phpMPT->bcc(Array, 1, 3)
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(132): phpMPT->make_binary_command()
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:101
2023-04-10 11:28:28 --- CRITICAL: ErrorException [ 8 ]: Array to string conversion ~ APPPATH\classes\phpMPT.php [ 123 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:123
2023-04-10 11:28:28 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(123): Kohana_Core::error_handler(8, 'Array to string...', 'C:\\xampp\\htdocs...', 123, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(132): phpMPT->make_binary_command()
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:123
2023-04-10 11:29:52 --- CRITICAL: ErrorException [ 1 ]: Call to undefined function bcc() ~ APPPATH\classes\phpMPT.php [ 122 ] in file:line
2023-04-10 11:29:52 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 11:30:11 --- CRITICAL: ErrorException [ 8 ]: Array to string conversion ~ APPPATH\classes\phpMPT.php [ 123 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:123
2023-04-10 11:30:11 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(123): Kohana_Core::error_handler(8, 'Array to string...', 'C:\\xampp\\htdocs...', 123, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(132): phpMPT->make_binary_command()
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:123
2023-04-10 11:30:38 --- CRITICAL: ErrorException [ 8 ]: Use of undefined constant bcc - assumed 'bcc' ~ APPPATH\classes\phpMPT.php [ 165 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:165
2023-04-10 11:30:38 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(165): Kohana_Core::error_handler(8, 'Use of undefine...', 'C:\\xampp\\htdocs...', 165, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(137): phpMPT->checkAnswer('\x05\x00O\x02H')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:165
2023-04-10 11:30:56 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _bcc ~ APPPATH\classes\phpMPT.php [ 165 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:165
2023-04-10 11:30:56 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(165): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 165, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(137): phpMPT->checkAnswer('\x05\x00O\x02H')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:165
2023-04-10 11:31:03 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _bcc ~ APPPATH\classes\phpMPT.php [ 165 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:165
2023-04-10 11:31:03 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(165): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 165, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(137): phpMPT->checkAnswer('\x05\x00O\x02H')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:165
2023-04-10 11:31:04 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _bcc ~ APPPATH\classes\phpMPT.php [ 165 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:165
2023-04-10 11:31:04 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(165): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 165, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(137): phpMPT->checkAnswer('\x05\x00O\x02H')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:165
2023-04-10 11:32:18 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected ';' ~ APPPATH\classes\phpMPT.php [ 124 ] in file:line
2023-04-10 11:32:18 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 11:32:29 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _bcc ~ APPPATH\classes\phpMPT.php [ 166 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:166
2023-04-10 11:32:29 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(166): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 166, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(138): phpMPT->checkAnswer('\x05\x00O\x02H')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:166
2023-04-10 11:43:43 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected ';' ~ APPPATH\classes\phpMPT.php [ 97 ] in file:line
2023-04-10 11:43:43 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 11:43:54 --- CRITICAL: ErrorException [ 2 ]: unpack() expects exactly 2 parameters, 4 given ~ APPPATH\classes\phpMPT.php [ 97 ] in file:line
2023-04-10 11:43:54 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unpack() expect...', 'C:\\xampp\\htdocs...', 97, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(97): unpack('C*', '\x04O\x00', 1, 3)
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(123): phpMPT->bcc('\x04O\x00', 1, 3)
#3 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(134): phpMPT->make_binary_command()
#4 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#6 [internal function]: Kohana_Controller->execute()
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#9 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#10 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#11 {main} in file:line
2023-04-10 11:55:00 --- CRITICAL: ErrorException [ 8 ]: Uninitialized string offset: 3 ~ APPPATH\classes\phpMPT.php [ 102 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:102
2023-04-10 11:55:00 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(102): Kohana_Core::error_handler(8, 'Uninitialized s...', 'C:\\xampp\\htdocs...', 102, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(124): phpMPT->bcc('\x04O\x00', 1, 3)
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(135): phpMPT->make_binary_command()
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:102
2023-04-10 11:58:54 --- CRITICAL: ErrorException [ 8 ]: Uninitialized string offset: 3 ~ APPPATH\classes\phpMPT.php [ 102 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:102
2023-04-10 11:58:54 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(102): Kohana_Core::error_handler(8, 'Uninitialized s...', 'C:\\xampp\\htdocs...', 102, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(124): phpMPT->bcc('\x04O\x00', 0, 4)
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(135): phpMPT->make_binary_command()
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:102
2023-04-10 12:01:02 --- CRITICAL: ErrorException [ 8 ]: Uninitialized string offset: 3 ~ APPPATH\classes\phpMPT.php [ 102 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:102
2023-04-10 12:01:02 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(102): Kohana_Core::error_handler(8, 'Uninitialized s...', 'C:\\xampp\\htdocs...', 102, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(124): phpMPT->bcc('\x04O\x00', 0, 4)
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(135): phpMPT->make_binary_command()
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:102
2023-04-10 12:02:01 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected ';' ~ APPPATH\classes\phpMPT.php [ 105 ] in file:line
2023-04-10 12:02:01 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 12:16:55 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected ';' ~ APPPATH\classes\phpMPT.php [ 69 ] in file:line
2023-04-10 12:16:55 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 14:06:15 --- CRITICAL: ErrorException [ 8 ]: Undefined property: phpMPT::$Command ~ APPPATH\classes\phpMPT.php [ 141 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:141
2023-04-10 14:06:15 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(141): Kohana_Core::error_handler(8, 'Undefined prope...', 'C:\\xampp\\htdocs...', 141, Array)
#1 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#2 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#3 [internal function]: Kohana_Controller->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#7 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#8 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:141
2023-04-10 14:12:27 --- CRITICAL: ErrorException [ 1 ]: Call to undefined function make_binary_command() ~ APPPATH\classes\phpMPT.php [ 139 ] in file:line
2023-04-10 14:12:27 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 14:14:49 --- CRITICAL: ErrorException [ 8 ]: Uninitialized string offset: 6 ~ APPPATH\classes\phpMPT.php [ 157 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:157
2023-04-10 14:14:49 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(157): Kohana_Core::error_handler(8, 'Uninitialized s...', 'C:\\xampp\\htdocs...', 157, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:157
2023-04-10 14:15:34 --- CRITICAL: ErrorException [ 8 ]: Uninitialized string offset: 6 ~ APPPATH\classes\phpMPT.php [ 157 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:157
2023-04-10 14:15:34 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(157): Kohana_Core::error_handler(8, 'Uninitialized s...', 'C:\\xampp\\htdocs...', 157, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:157
2023-04-10 14:22:21 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected 'Err' (T_STRING) ~ APPPATH\classes\phpMPT.php [ 176 ] in file:line
2023-04-10 14:22:21 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 14:22:54 --- CRITICAL: ErrorException [ 2 ]: unpack(): Type C: not enough input, need 1, have 0 ~ APPPATH\classes\phpMPT.php [ 171 ] in file:line
2023-04-10 14:22:54 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unpack(): Type ...', 'C:\\xampp\\htdocs...', 171, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(171): unpack('C', '')
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in file:line
2023-04-10 14:23:53 --- CRITICAL: ErrorException [ 2 ]: unpack(): Type C: not enough input, need 1, have 0 ~ APPPATH\classes\phpMPT.php [ 171 ] in file:line
2023-04-10 14:23:53 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unpack(): Type ...', 'C:\\xampp\\htdocs...', 171, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(171): unpack('C', '')
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in file:line
2023-04-10 14:24:40 --- CRITICAL: ErrorException [ 2 ]: unpack(): Type C: not enough input, need 1, have 0 ~ APPPATH\classes\phpMPT.php [ 171 ] in file:line
2023-04-10 14:24:40 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unpack(): Type ...', 'C:\\xampp\\htdocs...', 171, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(171): unpack('C', '')
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in file:line
2023-04-10 14:26:23 --- CRITICAL: ErrorException [ 2 ]: unpack(): Type C: not enough input, need 1, have 0 ~ APPPATH\classes\phpMPT.php [ 171 ] in file:line
2023-04-10 14:26:23 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unpack(): Type ...', 'C:\\xampp\\htdocs...', 171, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(171): unpack('C', '')
#2 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#3 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#9 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#10 {main} in file:line
2023-04-10 14:31:56 --- CRITICAL: ErrorException [ 8 ]: Array to string conversion ~ APPPATH\classes\phpMPT.php [ 172 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:172
2023-04-10 14:31:56 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(172): Kohana_Core::error_handler(8, 'Array to string...', 'C:\\xampp\\htdocs...', 172, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:172
2023-04-10 14:33:13 --- CRITICAL: ErrorException [ 8 ]: Array to string conversion ~ APPPATH\classes\phpMPT.php [ 173 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:173
2023-04-10 14:33:13 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(173): Kohana_Core::error_handler(8, 'Array to string...', 'C:\\xampp\\htdocs...', 173, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:173
2023-04-10 14:33:40 --- CRITICAL: ErrorException [ 8 ]: Array to string conversion ~ APPPATH\classes\phpMPT.php [ 174 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:174
2023-04-10 14:33:40 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(174): Kohana_Core::error_handler(8, 'Array to string...', 'C:\\xampp\\htdocs...', 174, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:174
2023-04-10 14:34:24 --- CRITICAL: ErrorException [ 8 ]: Array to string conversion ~ APPPATH\classes\phpMPT.php [ 173 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:173
2023-04-10 14:34:24 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(173): Kohana_Core::error_handler(8, 'Array to string...', 'C:\\xampp\\htdocs...', 173, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:173
2023-04-10 14:37:04 --- CRITICAL: ErrorException [ 8 ]: Array to string conversion ~ APPPATH\classes\phpMPT.php [ 173 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:173
2023-04-10 14:37:04 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(173): Kohana_Core::error_handler(8, 'Array to string...', 'C:\\xampp\\htdocs...', 173, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:173
2023-04-10 14:43:37 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _commandDEC ~ APPPATH\classes\phpMPT.php [ 182 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:182
2023-04-10 14:43:37 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(182): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 182, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:182
2023-04-10 14:44:16 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _commandDEC ~ APPPATH\classes\phpMPT.php [ 182 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:182
2023-04-10 14:44:16 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(182): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 182, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:182
2023-04-10 14:44:57 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _commandDEC ~ APPPATH\classes\phpMPT.php [ 182 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:182
2023-04-10 14:44:57 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(182): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 182, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:182
2023-04-10 14:45:14 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _commandDEC ~ APPPATH\classes\phpMPT.php [ 182 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:182
2023-04-10 14:45:14 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(182): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 182, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(140): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:182
2023-04-10 15:01:55 --- CRITICAL: ErrorException [ 8 ]: Undefined property: phpMPT::$edesc ~ APPPATH\classes\Controller\Dashboard.php [ 28 ] in C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php:28
2023-04-10 15:01:55 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(28): Kohana_Core::error_handler(8, 'Undefined prope...', 'C:\\xampp\\htdocs...', 28, Array)
#1 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#4 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#6 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#7 {main} in C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php:28
2023-04-10 15:06:02 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected 'echo' (T_ECHO) ~ APPPATH\classes\phpMPT.php [ 151 ] in file:line
2023-04-10 15:06:02 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2023-04-10 15:24:51 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: content ~ APPPATH\views\template.php [ 62 ] in C:\xampp\htdocs\cvs\application\views\template.php:62
2023-04-10 15:24:51 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\views\template.php(62): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 62, Array)
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
2023-04-10 15:36:13 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: _res ~ APPPATH\classes\phpMPT.php [ 206 ] in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:206
2023-04-10 15:36:13 --- DEBUG: #0 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(206): Kohana_Core::error_handler(8, 'Undefined varia...', 'C:\\xampp\\htdocs...', 206, Array)
#1 C:\xampp\htdocs\cvs\application\classes\phpMPT.php(141): phpMPT->checkAnswer('\x06\x00O\x00\x00I')
#2 C:\xampp\htdocs\cvs\application\classes\Controller\Dashboard.php(25): phpMPT->execute()
#3 C:\xampp\htdocs\cvs\system\classes\Kohana\Controller.php(84): Controller_Dashboard->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Dashboard))
#6 C:\xampp\htdocs\cvs\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\cvs\system\classes\Kohana\Request.php(997): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\cvs\index.php(118): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\cvs\application\classes\phpMPT.php:206