1) Установить в файле config.json, первичную настройку старта парсинга. По умолчанию стоит {"start":0,"end":1001} . Эта настройка указывает с какого товара парсить по какой
Если парсинг идет с нуля, лучьше так и оставить.
2)Запустить start.php Он начинает парсинг сайта, по 1000 товаров. И пишет в файл parser.txt. Чтоб каждый раз не запускать, можно сделать в CRON(unix) задание или Планировщик задачь(win)
Когда отпарсятся 1000 товаров, автоматом меняеться настройка файла config.json. К примеру было {"start":0,"end":1001} после парсинга станет {"start":1000,"end":2001}
3)Когда отпарсите столько сколько нужно товаров, нужно запусить файл convertExcel.php. Он конвертирует содержимое файла parser.txt в excel формат в файл shop220.xlsx
4)Сейчас все отпрасенные товары находятся в файлах parserFull.txt и shop220Full.xlsx