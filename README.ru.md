<p align="center">
  <img src="/assets/images/logo.png" alt="logo"/>
</p>

<p align="center">
  <a href="README.md">
    <img src="/assets/images/en.png" alt="Read SuQL documentation in English"/>
  </a>
  <a href="README.ru.md">
    <img src="/assets/images/ru.png" alt="Читать SuQL документация на русском"/>
  </a>
</p>

# Sugar SQL

<p align="left">
  <img src="https://img.shields.io/github/v/release/sagittaracc/suql" alt="GitHub release (latest by date)"/>
  <a href="https://github.com/sagittaracc/suql/blob/master/LICENSE">
    <img src="https://img.shields.io/github/license/sagittaracc/suql" alt="GitHub license"/>
  </a>
  <a href="https://github.com/sagittaracc/suql/issues">
    <img src="https://img.shields.io/github/issues/sagittaracc/suql" alt="GitHub issues"/>
  </a>
</p>

### Что это?
SuQL - это синтаксический сахар для SQL.

### Зачем это нужно?
1. Увеличение скорости backend-разработки.
2. Создание запросов, простых для чтения и написания.
3. Создание web-сервисов на чистом SuQL, без необходимости постобработки на каком-либо языке программирования.
3. Возможность расширить синтаксис самостоятельно, как угодно, выходя за рамки даже SQL. Границ просто нет.

### Как это использовать?
1. Установите через composer ```composer require sagittaracc/suql```
2. Создайте файл ```test.php```
```php
<?php
require 'vendor/autoload.php';
$suql = (new SuQL)->setAdapter('mysql');
$sql = $suql->query('
  SELECT FROM users
    id,
    name
  ;
')->getSQL();
echo $sql;
```
3. Запустите ```test.php```
```
> php test.php
select users.id, users.name from users
```

### Демо
Все примеры можно посмотреть в папке ```tests``` а затем попробовать их [здесь](https://repl.it/@sagittaracc/suql-example)
