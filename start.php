<?php
set_time_limit(3600);
include_once ('pars.php');

//проверка на выполнения скрипта
if ( Pars::testParser() ){
  echo 'Начался парсинг сайта';

  //возьмем настройки из json
  $config = Pars::getJson();

  //Вызываем парсинг с нужными параметрами конфига
  $parser = new Pars($config->start,$config->end);
  $parser->goParser();
  $result = $parser->getArr();
  $parser->getFiles($result);
  echo 'Закончился парсинг сайта';
} else {
  echo 'Предыдущий парсинг ещо не завершился';
  die();
}