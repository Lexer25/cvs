rem test2.bat - тут собраны базовые тесты для работы на Хедлайнер 2, где используются индуктивные петли.
set key=123456
set grz=D213DD71

set grz014=D014DD71
set key014=3665363

set grz005=A005BB177
set key005=50500505

set keyT=11223344

C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=100
rem "очищаю" гараж
rem тест 1-1: оба идентификатора принадлежа одному автомобилю. Перый идет UHF, затем ГРЗ. Ожидаю в таблице только один UHF (он был первым), занято только одно место.

REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3
REM ping localhost -n 1
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014% --id_gate=3


rem тест 1-2: оба идентификатора принадлежа одному автомобилю. Перый идет ГРЗ, затем UHF. ОЖидаю в таблице ГРЗ, т.к. он был первым, занято одно место.
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM ping localhost -n 10
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014% --id_gate=3
REM ping localhost -n 10
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3

rem тест 1-3: оба идентификатора принадлежат одному автомобилю. Перый идет ГРЗ, затем UHF. UHF уже на парковке. Ожидаю событие Повторный въезд, в таблице будет ГРЗ
rem сначала очищаю гараж и "ставлю" туда УХФ.
rem затем пауза 8 секунд, чтобы следующий въезд не был быстрым, повторным.
rem затем въезд ГРЗ. Ожидаю событие Повторный въезд под другим идентификатором.
rem а затем, через 1 секунду, въезд того же владельца по ХУФ. Ожидаю событие Повторный въезд, в гараже - ГРЗ, т.к. он был первым.

REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3
REM ping localhost -n 8
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014% --id_gate=3
REM ping localhost -n 1			
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey  --key=%key014%	--id_gate=3	
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101 -cooment='Событие повторный въезд'


rem тест 1-4: оба идентификатора принадлежа одному автомобилю. Перый идет UHF, затем ГРЗ. ГРЗ уже на парковке. 
rem Ожидаю Повторный въезд, в таблице будет UHF
			
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014% --id_gate=3
REM ping localhost -n 8
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3

rem ==================== идентификаторы действительный и недействительный
rem оба идентификатора чужие. Ожидаю: события о неизвестных идентификаторов.
rem test 2-0
REM set key=123456
REM set grz=A111AA99
REM echo test 2-0
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	
REM ping localhost -n 1
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%


REM echo test 2-0-0 Поток неизвестных идентификаторов. Сначало ожидаю событие Неизвестная карта, а затем отказ из-за повторов
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	
ping localhost -n 1
start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%
ping localhost -n 1




rem test 2-1 сперва неизвестный UHF,  затем известный ГРЗ. Ожидаю ГРЗ на парковке

REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014% --id_gate=3
REM ping localhost -n 8
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	--id_gate=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz014% --id_gate=3



rem тест 2-3. Два разных валидных UHF . Первый 014, затем 005. Ожидаю в списке 014.
REM echo test 2-3
rem очищаю гаража
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	
REM ping localhost -n 2
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005%
REM ping localhost -n 2
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%


rem тест 2-4. Два разных валидных UHF . Первый 005, затем 014. Ожидаю в списке 005.
REM echo test 2-4
rem очищаю гаража
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005%	
REM ping localhost -n 2
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%
REM ping localhost -n 2
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%grz%

rem ============ имитация последовательного проезда =====================
rem test 2-4 последовательный проезд одного и того же UHF на въезд и последующий выезд через ворота 3.1 Отказ в обработке повторного идентификатора
REM echo test 2-5
rem очищаю гаража
rem ch=1 - въезд
rem ch=0 - выезд
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005%	--id_gate=3
REM ping localhost -n 5 > null	
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005%	--ch=0


rem test 2-6 последовательный проезд "чужого" UHF и "своего" UHF на въезд и последующий выезд через ворота 3.1. На парковке должен остаться 005
REM echo test 2-6
rem очищаю гаража
rem ch=1 - въезд
rem ch=0 - выезд
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	--id_gate=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	--id_gate=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	--id_gate=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key%	--id_gate=3
REM ping localhost -n 5 > null	
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005%	--id_gate=3

rem ============================Счетчики тест серии 4
rem test 4-0 последовательно заежают машины . Ожидаю разрешение на въезд только первой, а остальные должны быть проигнорированы, т.к. очень часто ездят.
rem эта же ситуация будет происходит, когда в поле попадет сразу несколько UHF. Ожидаю на территории только первый UHF
REM echo test 4-0

REM ch=1 - въезд
REM ch=0 - выезд
rem C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
rem start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3
REM ping localhost -n 1 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005%	--id_gate=3
REM ping localhost -n 1 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyT%	--id_gate=3


rem ============================Счетчики тест серии 5 ==========================
rem test 5-1 выезд UHF, которого нет в гараже
REM echo test 4-0
rem ch=1 - въезд
rem ch=0 - выезд
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3
REM ping localhost -n 12 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005%	--ch=0
REM ping localhost -n 12 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%keyT%	--id_gate=3


rem имитация въезда , затем выезда одной машины, и затем попытка въезд ее же (вроде как номер пойма при выезде
REM C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=clearGarage --id_garage=3
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--ch=0
REM ping localhost -n 12 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014%	--id_gate=3
REM ping localhost -n 5 > null
REM start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005% --cam=6


rem start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key005% --ip=172.16.101.101	--id_gate=3
rem ping localhost -n 12 > null
rem start C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=sendKey --key=%key014% --ip=172.16.101.101	--ch=0
C:\xampp\php\php.exe c:\xampp\htdocs\cvs\modules\minion\minion --task=eventInsert --id=101





	