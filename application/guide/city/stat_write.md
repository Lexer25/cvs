При обращении к методу **stat_device** модуля **task** происходит вызов метода **checkStatus**  модели **Device**.  
В ходе выполнения метода **checkStatus** происходит обращение к каждому контроллеру с целью получения следующих данных:
1. текущее состояние (reportstatus);  
2. чтение версии контроллера (getversion);  
3. чтение коилчества карт в двери 0 (getkeycount door=0);  
4. чтение количества карт в двери 1 (getkeycount door=1);  
Полученные данные заносятся в базу данных СКУД и доступны в меню Контроллеры в таблице **Количество карт в контроллере**.  
Сбор статистики позволяет оценить:
1. количество карт в контроллерах. Это количество не должно превышать максимальную емкость контроллера СКУД.
2. качество связи с контроллерами. Большое количество состояний вида **Online=No** говорит о том, что качество связи низкое.
