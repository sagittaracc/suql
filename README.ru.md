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
В папке ```examples``` Вы можете найти примеры моделей, а в папке ```tests``` Вы можете найти примеры их использования, а также примеры расширения функциональности синтаксиса SuQL ORM в директории ```syntax```.

### Установка
Через composer
```composer require sagittaracc/suql```

## Итоги
SuQL работает через модификаторы. Они уже реализуют стандартные SQL секции как `WHERE`, `GROUP`, `JOIN`, `ORDER` и SQL функции и т.д.
Более того Вы можете писать свои собственные модификаторы.
