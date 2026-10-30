rem тест имитирут въезд ГРЗ H027BB799 чеерз ворота 
rem 11 - въезд
re, 10 - выезда


set key=123456
set grz027=P025PB799




C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100


 C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz027%	--id_gate=11







C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101





	