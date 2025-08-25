CREATE TABLE keys (
"id" integer primary key autoincrement,
"key" text,
"type" integer,
"id_pep" integer,
"result" integer,
"timestamp" text , 
"created" text  DEFAULT (datetime('now', 'localtime')), 
"gate" integer  default "Номер ворот");



