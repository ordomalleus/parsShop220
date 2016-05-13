<!DOCTYPE html>
<html lang="ru">
<head>
  <title>Парсер shop220.ru</title>
</head>
<body>

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

?>

<div style="min-height: 100px; padding: 10px; border: 1px solid #333;margin: 20px;">
  <pre>
    <?php
      print_r($result);
    ?>
  </pre>
</div>
<div style="min-height: 100px; padding: 10px; border: 1px solid #333;margin: 20px;">
  <pre>
    <?php
      print_r($parser->count);
    ?>
  </pre>
</div>

</body>
</html>