rem test3_0_order_no_count.bat - проверка работы свойства no_count
rem если true - то количество машин для въезда ограничено количеством свободных машиномест
rem если false - то количество машин для въезда не ограничено

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
set key2101=A2101AA97
set key2102=A2102AA97
set key2103=A2103AA97
set key2104=A2104AA97
set key2105=A2105AA97
set key2106=A2106AA97
set key2107=A2107AA97
set key2108=A2108AA97
set key2109=A2109AA97
set key2110=A2110AA97


rem Гараж в 3.1 (id_garage=2)

тест 3.1-0 Паровозик. Все должны заехать.
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100
"очищаю" гаражи
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=1
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=2
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=5
тест 1-1: по одному идентификатору от каждого гаража

C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2101%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2102%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2103%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2104%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2105%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2106%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2107%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2108%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2109%	--id_gate=3
ping localhost -n 3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key2110%	--id_gate=3
ping localhost -n 3


C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101





	