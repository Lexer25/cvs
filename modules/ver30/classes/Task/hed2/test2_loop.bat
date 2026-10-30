rem test2-1_order.bat - имитация работы ворот хед 2 проезд по индуктивной петле
rem Парковка 3.1
rem въезд id_gate=3
rem выезд id_gate=4

rem Парковка 3.2
rem въезд id_gate=2
rem выезд id_gate=7

rem Парковка 3.3
rem въезд id_gate=5
rem выезд id_gate=6


rem этих ГРЗ и УХФ нет в базе данных
set key=123456
set grz=D213DD71

rem ниже - Гараж Артсек 3 (id_garage=3)
set grz014=D014DD71
set key014=3665363

set grz005=A005BB177
set key005=50500505

set keyT=11223344

rem Иванов гараж (id_garage=1)

rem Показники
set key641176=641176
set key8097531=8097531

rem Отдел продаж
CONCATENATION
set key3518308=3518308
set key3554897=3554897
set key6795553=6795553
set key1922384=1922384
set key6904336=6904336
set key7008204=7008204
set key3178167=3178167
set key6844750=6844750
set key6844759=6844759
set key8387033=8387033
set key8387016=8387016
set key5012772=5012772
set key5018878=5018878

rem Гараж в 3.1 (id_garage=2)

CONCATENATION
set key11388024=11388024
set key6810902=6810902
set key6863465=6863465


rem УК (id_garage=5)
CONCATENATION
set keyM567OM799=M567OM799
set keyH763CT790=H763CT790
set key22332233=22332233
set keyP938KX777=P938KX777
set key3415507=3415507
set key3417555=3417555
set key3410387=3410387
set key7THT3321=7THT3321


rem тест 2-1.1 ворота открыты, подъезжает автомобиль. СИстема читает и UHF, и ГРЗ
rem в настройках должно быть указано, что ворота открыты.
rem используются ворота 3.2 2-въезд, 7 - выезд
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100
rem "очищаю" гаражи
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=2
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=5
REM тест 1-1: по одному идентификатору от каждого гаража

REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey2 --key=%key014%	--id_gate=2
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey2 --key=%grz014%	--id_gate=2
rem Теперь надо посмотреть Кто на парковке. Ожидаю ГРЗ.
rem а теперь выезжаю.
REM ping localhost -n 10
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key641176%	--id_gate=7
REM ping localhost -n 1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key11388024%	--id_gate=7
REM ping localhost -n 1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=7
REM ping localhost -n 1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014%	--id_gate=7
REM ping localhost -n 1

Rem тест 2 въезд при закрытых воротах 
rem тест 2-1.2 ворота закрыты, подъезжает автомобиль. СИстема читает и UHF, и ГРЗ
rem в настройках должно быть указано, что ворота закрыты.
rem используются ворота 3.2 2-въезд, 7 - выезд

C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100 --comment=loop

C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendLoop2 --key=%key641176%	--id_gate=2


C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101





	