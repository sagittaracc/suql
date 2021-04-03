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
SuQL stands for Sugar Query Language and it's the best and simplest ORM. Easy to use.
(MySQL, Sqlite, Oracle, PostgreSQL, SqlServer).

It has a conceptually new way of approaching databases.
Expand SuQL syntax on your own. There's no limit really.

### Examples
In the ```examples``` directory see the examples of how you should define models, custom modifiers and extend SuQL ORM syntax on your own.
In the ```tests/MySuQLExtTest.php``` see how you should use what you define in those examples.
In the ```tests/SuQLTest.php``` see the build-in capabilities of SuQL ORM syntax.

### Install
- Via composer

```composer require sagittaracc/suql```

- Or by cloning the repository

```
git clone https://github.com/sagittaracc/suql.git
cd suql
composer install
```

## Conclusion
SuQL is all about modifiers. They already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` and SQL functions etc.
More than that, you can develop your own modifiers.
