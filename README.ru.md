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

### SuQL ORM
SuQL расшифровывается как Sugar Query Language и это лучшая и простейшая ORM. Простая в использовании.
(MySQL, Sqlite, Oracle, PostgreSQL, SqlServer).

Имеет концептуально новый подход по работе с базами данных.
Расширяйте возможности SuQL ORM самостоятельно. Вы ограничены лишь Вашей фантазией.

### Примеры

Отнаследуйтесь от ```SuQL``` чтобы работать только с SQL ORM синтаксическим сахаром

```php

namespace app\model;

use \SuQL;

class User extends SuQL
{
  public function query()
  {
    return 'user';
  }

  public function table()
  {
    return 'users';
  }
}

```

```php
// 'select users.id, users.name from users'
echo User::find()->select(['id', 'name'])->getRawSql();
```

Отнаследуйтесь от ```PDOSuQL``` чтобы непосредственно работать с базой данных

```php

namespace app\model;

use \PDOSuQL;

class UserDb extends PDOSuQL
{
  protected $dbname = 'test';

  public function query()
  {
    return 'user';
  }

  public function table()
  {
    return 'users';
  }
}

```

```php
  print_r(UserDb::find()->fetch());
```

В папке ```examples``` смотрите примеры как Вы можете определять свои модели, кастомные модификаторы, и расширять SuQL ORM синтакс самостоятельно.
В файле ```tests/MySuQLExtTest.php``` смотрите как использовать сделанное Вами расширение SuQL ORM синтаксиса.
В файле ```tests/SuQLTest.php``` смотрите что на данный момент может SuQL ORM из коробки.

### Установка
- Через composer

```composer require sagittaracc/suql```

- Или установка вручную

```
git clone https://github.com/sagittaracc/suql.git
cd suql
composer install
```

## Итоги
SuQL работает через модификаторы. Они уже реализуют стандартные SQL секции как `WHERE`, `GROUP`, `JOIN`, `ORDER` и SQL функции и т.д.
Более того Вы можете писать свои собственные модификаторы.
