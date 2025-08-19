rem Тестирование события G_OK_PLACE код 15
rem въезд разрешен. Ни идентификатора, ни владельца на парковке нет, машиноместа имеются.
rem ожидаю код события 15 
rem

set grz014=D014DD71
set key014=3665363

set grz005=A005BB177
set key005=50500505

set keyT=11223344

set keyA=8696164


set keyT2=78787878
set keyT3=987654321


C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100
rem "очищаю" гараж
rem тест 5-1: оба идентификатора принадлежа одному автомобилю. Перый идет UHF, затем ГРЗ. Ожидаю в таблице только один UHF (он был первым), занято только одно место.

C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3
rem ping localhost -n 7
rem C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyT% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyA% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyT2% --id_gate=3


C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101





	