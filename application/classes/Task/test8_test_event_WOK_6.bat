rem Тестирование события WOK код 6
rem въезд разрешен по категории доступа.
rem после выполенния скрипта ожидаю код события 6 , без гаража
rem

set grz014=D014DD71
set key014=3665363

set grz005=A005BB177
set key005=50500505

set keyT=11223344

set keyA=8696164


set keyT2=78787878
set keyT3=987654321

rem идентификатор 8696163 имеет категорию доступа 3.1, гаража нет.
set keyA2=8696163


C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100 --comment=test8
rem "очищаю" гараж

rem C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=delPep --id_pep=3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=delKeyFromInside --key=%keyA2%

C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyA2%	--id_gate=3
ping localhost -n 7
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyA2% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyT% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyA% --id_gate=3
REM ping localhost -n 3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyT2% --id_gate=3


C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101 --comment=test8





	