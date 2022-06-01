<div align="center"><img src="https://i.postimg.cc/bvpF0Xhd/suql.png" alt="suql-sugar-sql" border="0"></div>
<h1 align="center">Sugar Query Language</h1>
<div align="center">
  <img src="https://img.shields.io/github/v/release/sagittaracc/suql" alt="GitHub release (latest by date)"/>
  <a href="https://github.com/sagittaracc/suql/blob/master/LICENSE">
    <img src="https://img.shields.io/github/license/sagittaracc/suql" alt="GitHub license"/>
  </a>
  <a href="https://github.com/sagittaracc/suql/issues">
    <img src="https://img.shields.io/github/issues/sagittaracc/suql" alt="GitHub issues"/>
  </a>
</div>

### SuQL
Example
```
test\suql\models\Query1
    f1 af1
    f2
        order asc
        as af2
```

### Установка
`composer require sagittaracc/suql`

### Документация
Прочитать можно [здесь](https://github.com/sagittaracc/suql/blob/master/docs/index.md)

### Сниппеты для VS Code
Скачайте плагин [здесь](https://github.com/sagittaracc/suql-gen) и установите его с помощью команды `code --install-extension suql-gen.vsix`

### Системные требования
PHP версии не ниже 7.3

### Тестирование
Покрыто тестами [PHPUnit](https://phpunit.de/). Для запуска тестов выполните:

`./vendor/bin/phpunit tests`

### Psalm
Данный код проанализирован с помощью Psalm. Для запуска статического анализа выполните:

`./vendor/bin/psalm`