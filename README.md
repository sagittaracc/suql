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

### Requirements
The minimum requirement - PHP 5.6

### Examples
More examples in the ```tests``` directory

### Install
- Via composer

```composer require sagittaracc/suql```

- Or by cloning the repository

```
git clone https://github.com/sagittaracc/suql.git
cd suql
composer install
```
## Tests
`./vendor/bin/phpunit tests`

## Conclusion
SuQL is all about modifiers. They already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` and SQL functions etc.
More than that, you can develop your own modifiers.
