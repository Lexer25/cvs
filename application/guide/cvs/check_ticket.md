Алгоритм проверки ГРЗ (обработка в методе exec):  
-- из запроса POST извлекаются номер UHF  номер видеокамеры.  
-- Выполняется валидация данных.  
-- Определяется номер ворот, от которых получены данные.  
-- Создается экземпляр phpCvs.  
-- Выполняется проверка: разрешен ли проезд?  
-- в зависимости от результат проверка на табло выводится соответсвующая надпись и выдается сигнал на открытие ворот.


##Проверка (phpCvs->check()). REGISTERPASS_HL_2  
Выполняется обращение к процедуре [REGISTERPASS_HL_2](#REGISTERPASS_HL_2)(id_dev, grz, NULL) базы данных СКУД.  
Результат проверки сохраняется в свойстве code_validation.  


##REGISTERPASS_HL_2 
Последовательно выполняет операции:  
-- [validatepass_hl_parking_2](#validatepass_hl_parking_2) :id_dev, :id_card, :grz - валидация ГРЗ. 
-- проверка: если ГРЗ на территории, то формируется событие о повторном въезде.  
-- вставляется событие в таблицу HL_EVENTS (локальнй журнал событий парковочной системы).
-- вставляется событие в таблицу EVENTS (единый журнал событий СКУД).

##validatepass_hl_parking_2
-- проверяет наличие свободных мест  
-- проверяет параметры ГРЗ: имеется ли такой в БД? срок действия не истек ли?
-- если ГРЗ "приписан" к гаражу, то выполяется проверка наличия свободных мест. Если место есть, то будет разрешен въезд (независимо от категории доступа!).  
-- если ГРЗ не "приписан" к гаражу, то выполняется проверка категорий доступа: если точка проезда входит в разрешенные, то въезд будет разрешен.  
Процедура возвращает код события:  
RC_OK = 50; /* проверка успешна, проход разрешен */  
RC_UNKNOWNCARD = 46; /* неизвестная карта */  
RC_DISABLEDCARD = 65; /* карта неактивна */  
RC_DISABLEDUSER = 65; /* юзер неактивен */  
RC_CARDEXPIRED = 47; /* "сейчас" вне срока действия карты */  
RC_ACCESSDENIED = 65; /* нет права доступа */  
RC_CARLIMITEXCEEDED = 81; /* превышен лимит количества авто на территории */  


