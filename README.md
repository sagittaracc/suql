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
[Select Query](https://github.com/sagittaracc/suql/wiki/Select-Query)

[Join Query](https://github.com/sagittaracc/suql/wiki/Join-Query)

[Use Views](https://github.com/sagittaracc/suql/wiki/Use-Views)

[Field Modifiers](https://github.com/sagittaracc/suql/wiki/Field-Modifiers)

[Query Modifiers](https://github.com/sagittaracc/suql/wiki/Query-Modifiers)

[Order Data](https://github.com/sagittaracc/suql/wiki/Order-Data)

[Functions](https://github.com/sagittaracc/suql/wiki/Functions)

[Extend SuQL](https://github.com/sagittaracc/suql/wiki/Extend-SuQL)

[Fetch data from databases](https://github.com/sagittaracc/suql/wiki/Fetch-data-from-databases)

More examples in the ```tests``` directory

In the ```examples``` directory see an example of how your app folder structure should look like.

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
