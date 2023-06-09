Артонит Сити - СКУД для жилого комплекса.  
###Технологическая основа.  
Панель управления Артонит Тикет написана на основе фреймворка Kohana версии 3.3.3.  
Информация выбирается из базы данных СКУД Артонит.  
Параметры работы панели управления (связь с базой данных, название объекта и т.п.) хранятся в [конфигурационных файлах](config_city).  
###Запуск панели управления.  
Панель управления находится по адресу http://<IP адрес севрера СКУД>:<порт web-сервера>/city.  
Если порт web-сервера 80, то его можно не указывать.  
Примеры:  
http://192.168.0.1:8080/city  
http://192.168.0.1/city  
192.168.0.1/city  
[Перейти к панели управления](http://localhost:8080/city/)
###Авторизованный доступ.  
Доступ к некоторым функциям панели управления доступен после авторизации.  
###Структуру панели управления.  
Панель управления предствляет собой набор закладок. На каждой из закладок выводится соответсвующая информация:  
Закладка  |Авторизация  |Краткое описание  |  
----------|-------------|------------------|  
Артонит Сити|Не требуется|Просмотр текущего состояния системы|
Загрузка контроллеров|Требуется для управления.<br>Не требуется для просмотра.|Просмотр состояния контроллеров. Управление контроллерами.|  
Очередь загрузки|Требуется для управления.<br>Не требуется для просмотра.|Просмотр и управление очередью загрузки номеров карт в контроллеры|
Результат|Не требуется|Окно с результатами выполнения команд|  
События|Не требуется|Статистические данные по событиям за последние 24 часа|
Жильцы|Не требуется|Поиск жильцов и вывод связанной с ними информации|
Точки прохода|Не требуется|Поиск точек прохода и вывод связанной с ними информации|
Журналы|Не требуется|Просмот списка журналов логирования, скачивание журналов|
Тест|Требуется|Набор инструментов для тестирования оборудования|
Справка|Не требуется|Просмотр справочной информации|
