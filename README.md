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

### What is this?
SuQL is syntactic sugar for SQL.

### Why do you need this?
1. Write queries once, use for every DBMS.
2. Write queries that are easy to read and write.
3. Expand SuQL syntax on your own. SQL isn't the limit. There's no limit really.

### Examples
There are couple examples in the ```tests``` directory and an example of extending SuQL syntax in the ```syntax``` directory.

## Conclusion
SuQL is all about modifiers and commands. Modifiers already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` and SQL functions etc. Meanwhile commands can do everything that SuQL or SQL can\'t.
More than that, you can develop your own modifiers and commands.
