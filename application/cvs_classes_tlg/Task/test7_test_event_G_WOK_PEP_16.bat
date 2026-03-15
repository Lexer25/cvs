rem Тестирование события G_WOK_PEP код 16 повторный въез владельца, при этом владелец уже находится на парковке, но въезжает под другим номером.
rem ожидаю код события 16
rem что делать? варианты:
rem 1. добавлять еще одну машину, занимать второе место,
rem 2. удалять уже стоявший номер, заменять его на вновь прибывший.
rem сейчас реализован вариант 2.

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
ping localhost -n 7
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3
ping localhost -n 7
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyT% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyA% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyT2% --id_gate=3


C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101





	