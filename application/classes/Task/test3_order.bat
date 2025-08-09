rem test3_order.bat - имитация паровозика: автомобили двужутся друг за другом плотно
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


rem тест 3.1-0 Паровозик. Все должны заехать.
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100
rem "очищаю" гаражи
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=2
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=5
rem тест 1-1: по одному идентификатору от каждого гаража

REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3
REM ping localhost -n 1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key641176%	--id_gate=3
REM ping localhost -n 1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key11388024%	--id_gate=3
REM ping localhost -n 1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key22332233%	--id_gate=3



rem тест 3.1-1 Чтение номера вдогонку с одним автомобилем. 
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100
rem "очищаю" гаражи
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=2
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=5

REM ping localhost -n 1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3
rem через некоторое время вижу этот номер на выезде. Ожидаю событие "Чтение вдогонку, не обрабатывается" Не обрабатывается: встречное реверсивное движение, что на парковке этот номер останется.
REM ping localhost -n 15
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=4




rem тест 3.1-2 Чтение номера вдогонку при проезде двух автомобилей автомобилем. 
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100
rem "очищаю" гаражи
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=1
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=2
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=5

ping localhost -n 1
rem въезжает первый автомобиль. Ожидаю событие Проезд разрешен
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3
rem ворота открываются, автомобиль начинает движение, и тут читается номер второго автомобиля. Это та же парковка, но другой гараж.  Ожидаю событие Проезд разрешен
ping localhost -n 5
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key11388024%	--id_gate=3
rem первый автомобиль читается уже на выезде, с другой стороны ворот. Событие второй проезд через ревесивные ворота.
ping localhost -n 15
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=4
rem а затем и вротой проезжает считыватель с другой стороны... Событие второй проезд через ревесивные ворота.
ping localhost -n 5
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key11388024%	--id_gate=4







REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz014% --id_gate=3

rem тест 1-2: оба идентификатора принадлежа одному автомобилю. Перый идет ГРЗ, затем UHF. ОЖидаю в таблице ГРЗ, т.к. он был первым, занято одно место.
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz014% --id_gate=3
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3

rem тест 1-3: оба идентификатора принадлежат одному автомобилю. Перый идет ГРЗ, затем UHF. UHF уже на парковке. Ожидаю событие Повторный въезд, в таблице будет ГРЗ
rem сначала очищаю гараж и "ставлю" туда УХФ.
rem затем пауза 8 секунд, чтобы следующий въезд не был быстрым, повторным.
rem затем въезд ГРЗ. Ожидаю событие Повторный въезд под другим идентификатором.
rem а затем, через 1 секунду, въезд того же владельца по ХУФ. Ожидаю событие Повторный въезд.

REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3
REM ping localhost -n 8
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz014% --id_gate=3
REM ping localhost -n 1			
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF  --key=%key014%	--id_gate=3	


rem тест 1-4: оба идентификатора принадлежа одному автомобилю. Перый идет UHF, затем ГРЗ. ГРЗ уже на парковке. Ожидаю Повторный въезд, в таблице будет UHF
			
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz014% --id_gate=3
REM ping localhost -n 8
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3

rem ==================== идентификаторы действительный и недействительный
rem оба идентификатора чужие. Ожидаю: события о неизвестных идентификаторов.
rem test 2-0
REM set key=123456
REM set grz=A111AA99
REM echo test 2-0
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%


REM echo test 2-0-0 Поток неизвестных идентификаторов. Сначало ожидаю событие Неизвестная карта, а затем отказ из-за повторов
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%
REM ping localhost -n 1




rem test 2-1 сперва неизвестный UHF,  затем известный ГРЗ. Ожидаю ГРЗ на парковке

REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz014% --id_gate=3
REM ping localhost -n 8
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	--id_gate=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz014% --id_gate=3



rem тест 2-3. Два разных валидных UHF . Первый 014, затем 005. Ожидаю в списке 014.
REM echo test 2-3
rem очищаю гаража
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	
REM ping localhost -n 2
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key005%
REM ping localhost -n 2
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%


rem тест 2-4. Два разных валидных UHF . Первый 005, затем 014. Ожидаю в списке 005.
REM echo test 2-4
rem очищаю гаража
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key005%	
REM ping localhost -n 2
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%
REM ping localhost -n 2
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --grz=%grz%

rem ============ имитация последовательного проезда =====================
rem test 2-4 последовательный проезд одного и того же UHF на въезд и последующий выезд через ворота 3.1 Отказ в обработке повторного идентификатора
REM echo test 2-5
rem очищаю гаража
rem ch=1 - въезд
rem ch=0 - выезд
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key005%	--id_gate=3
REM ping localhost -n 5 > null	
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key005%	--ch=0


rem test 2-6 последовательный проезд "чужого" UHF и "своего" UHF на въезд и последующий выезд через ворота 3.1. На парковке должен остаться 005
REM echo test 2-6
rem очищаю гаража
rem ch=1 - въезд
rem ch=0 - выезд
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	--id_gate=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	--id_gate=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	--id_gate=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key%	--id_gate=3
REM ping localhost -n 5 > null	
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key005%	--id_gate=3

rem ============================Счетчики тест серии 4
rem test 4-0 последовательно заежают машины . Ожидаю разрешение на въезд только первой, а остальные должны быть проигнорированы, т.к. очень часто ездят.
rem эта же ситуация будет происходит, когда в поле попадет сразу несколько UHF. Ожидаю на территории только первый UHF
REM echo test 4-0

REM ch=1 - въезд
REM ch=0 - выезд
rem C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
rem start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3
REM ping localhost -n 1 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key005%	--id_gate=3
REM ping localhost -n 1 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%keyT%	--id_gate=3


rem ============================Счетчики тест серии 5 ==========================
rem test 5-1 выезд UHF, которого нет в гараже
REM echo test 4-0
rem ch=1 - въезд
rem ch=0 - выезд
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3
REM ping localhost -n 12 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key005%	--ch=0
REM ping localhost -n 12 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%keyT%	--id_gate=3


rem имитация въезда , затем выезда одной машины, и затем попытка въезд ее же (вроде как номер пойма при выезде
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--ch=0
REM ping localhost -n 12 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014%	--id_gate=3
REM ping localhost -n 5 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendGRZ --key=%key005% --cam=6


rem start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key005% --ip=172.16.101.101	--id_gate=3
rem ping localhost -n 12 > null
rem start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendUHF --key=%key014% --ip=172.16.101.101	--ch=0
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101





	