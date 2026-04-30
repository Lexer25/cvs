rem тест имитирут въезд ГРЗ H027BB799 чеерз ворота 
rem 11 - въезд
re, 10 - выезда


set key=123456
set grz027=H027HB977




C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100


 C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz027%	--id_gate=10







C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101





	