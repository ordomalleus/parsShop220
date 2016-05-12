<?php
require ('lib/phpQuery/phpQuery/phpQuery.php');

class Pars
{
  private $begin = 0; //с какой позиции парсим
  private $end = 0; //до какой позиции парсим
  private $arr = []; //массив данных
  private $site = 'http://shop220.ru';  //сайт

  public $count = 0; //сколько всего спарсили товаров за запуск скрипта
  public $lastUrl = 0;  //ID url последнего запроса

  public $test;  //для тестов

  function __construct( $begin = 0, $end = null )
  {
    $this->begin = $begin;
    $this->end = $end;
  }

  //проверка на существование временого файла
  /**
   * При старте скрипта создаеться временный файл (служит для флага на выполнение скрипта)
   * Если файл существует значит идет выполнение скрипта.
   * Когда скрипт отработает этот файл удаляеться
   * @return bool
   */
  public static function testParser()
  {
    if ( file_exists('run.temp') ){
      return false;
    } else {
      return true;
    }
  }

  /**
   * Читаем настройки из json
   */
  public static function getJson ()
  {
    $conf = file_get_contents("config.json");
    $json = json_decode($conf);
    return $json;
  }

  /**
   * Метод парсинга
   */
  public function goParser()
  {
    //Вначале создадим временный файл
    $temp = fopen("run.temp", "w");
    fwrite($temp, '');
    fclose($temp);

    $start = $this->begin;
    //если не указан конец то берем 1000
    if ( $this->end == null ){
      $this->end = $this->begin + 1000;
    }

    for ( $i = $start; $i < $this->end; $i++){
      $url = $this->site . '/product' . $i . '.htm';
      //если нет страницы то идем к следующей
      $this->lastUrl = $i;
      if ( !($html = @file_get_contents($url)) ){
        continue;
      }

      $document = phpQuery::newDocument($html);

      //получаем имя товара
      $name = $document->find('td>div>h1')->text();

      //Получаем рубрику родителя
      $parentRub = $document->find('#breadcrumbs>a');
      $breadcrumbs = [];
      foreach ( $parentRub as $el){
        $el = pq($el);
        $breadcrumbs[] = $el->text();
      }
      $breadcrumbsResult = implode(" >> ", $breadcrumbs);

      //получаем массивы данных
      $content = $document->find('span>meta');
      foreach ( $content as $el ){
        $el = pq($el);
        //получаем цену
        if ( $el->attr('itemprop') == 'Price'){
          $price = $el->attr('content');
        }
        //получаем валюту
        if ( $el->attr('itemprop') == 'priceCurrency'){
          $val = $el->attr('content');
        }
      }
      //Замена точки на запятую
      $price = str_replace('.',',',$price);
      //переиминовываем название
      $val = $val == 'RUB' ? 'руб.' : $val;
      //сохраняем сылку
      $urlArr = $url;

      //Добовляем в массив данных
      $this->arr[] = [
        'Наименование товара' => $name,
        'Цена минимальная' => $price,
        'Валюта' => $val,
        'Ссылка на страницу товара' => $urlArr,
        'Наименование род. Рубрики' => $breadcrumbsResult
      ];

      //Увеличим счетчик спарсеных товаров
      $this->count += 1;
    }

  }

  public function getArr()
  {
    return $this->arr;
  }

  //Запись в файлы
  public function getFiles ($arr)
  {
    $fp = fopen('parserTest.txt', 'a');

    foreach($arr as $ar){
      $str = "";
      foreach ($ar as $key => $value){
//        $str .= $key . "\t" . "\"" . $value . "\";" . "\t";
        $str .= "\"" . $value . "\";";
      }
      $str .= "\r\n";
      fwrite($fp, $str);
    }
    fclose($fp);

    //запись логов в другой файл
    $ft = fopen('parserLog.txt', 'a');
    fwrite($ft, 'Спарсено товаров ' . $this->count);
    fwrite($ft, "\r\n");
    fwrite($ft, 'ID последнего товара ' . $this->lastUrl);
    fwrite($ft, "\r\n");
    fclose($ft);

    //запишим в конфиг итерации, преобразованый json объект
    $conf = fopen('config.json', 'r+');
    $confJson = [
      'start' => $this->lastUrl,
      'end' => $this->lastUrl + 1001,
    ];
    fwrite($conf, json_encode($confJson));
    fclose($conf);

    //удалим временный файл (говорит об окончании парсинга)
    unlink('run.temp');
  }
}