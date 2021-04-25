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
SuQL stands for Sugar Query Language. This is a flexible and extendable ORM framework. Basically you can do the fuck you want by using it. It can solve any problem. All you need to do is to learn its concept.

It has shitloads of killer features such as programmatical views, self-generated placeholders, extremally flexible approach to generate SQL queries, you can do this on your own literally. 

### Real live example
See the live example step by step [here](https://github.com/sagittaracc/suql/wiki/Live-example)

### Requirements
The minimum requirement - PHP 5.6

### Documentation
[Select Query](https://github.com/sagittaracc/suql/wiki/Select-Query)

[Join Query](https://github.com/sagittaracc/suql/wiki/Join-Query)

[Use Views](https://github.com/sagittaracc/suql/wiki/Use-Views)

[Field Modifiers](https://github.com/sagittaracc/suql/wiki/Field-Modifiers)

[Filter View](https://github.com/sagittaracc/suql/wiki/Filter-View)

[Query Modifiers](https://github.com/sagittaracc/suql/wiki/Query-Modifiers)

[Order Data](https://github.com/sagittaracc/suql/wiki/Order-Data)

[Functions](https://github.com/sagittaracc/suql/wiki/Functions)

[Extend SuQL](https://github.com/sagittaracc/suql/wiki/Extend-SuQL)

[Fetch data from databases](https://github.com/sagittaracc/suql/wiki/Fetch-data-from-databases)

[Insert Query](https://github.com/sagittaracc/suql/wiki/Insert-Query)

[Execute Query](https://github.com/sagittaracc/suql/wiki/Execute-queries)

[Compatibility with other frameworks](https://github.com/sagittaracc/suql/wiki/Compatibility-with-other-frameworks)

[Complex WHERE Conditions](https://github.com/sagittaracc/suql/wiki/Complex-WHERE-Conditions)

[Use your own placeholders](https://github.com/sagittaracc/suql/wiki/Use-your-own-placeholders)

More examples in the ```tests``` directory

### Code generator
This mothefucking framework can generate models and modifiers so you can waste no time.

To generate a model run `php gen/model.php` and follow the instructions.

To generate a modifier run `php gen/modifier.php` and follow the instructions.

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
