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
SuQL (pronounced "suckle") is the ORM technology in a wider way. This lets you operate not only with tables, but also with tables from different DBMS, php arrays, external services, local files and put them all together like they are the real objects in just one database. After all you can do some post processing within the same technology.
You may have heard of LINQ (Language Integrated Query). This is a counterpart in PHP.
Basically the models don't have to be tables in database. It could be anything. The point is we work with different datasources using SQL like all these datasources are tables that be stored in just one database.
Also you can write database triggers in PHP like this:
```php
Consumption::trigger('insert',
    function ($row) {

        $lastOne = LastConsumption::find([
            'user' => $row['user']
        ]);
        $lastOne->value = $row['value'];
        $lastOne->time = $row['time'];
        $lastOne->save();

    }
);

$consumption = new Consumption();
$consumption->load($request);
$consumption->save();
```

### Installation
`composer require sagittaracc/suql`

### Documentation
You can read the documentation [here](https://github.com/sagittaracc/suql/blob/master/docs/index.md)

### Snippets for VS Code
Download the plugin [here](https://github.com/sagittaracc/suql-gen) and install it by running this command `code --install-extension suql-gen.vsix`

### Requirements
PHP >= 7.3

### Tests
To run tests:

`./vendor/bin/phpunit tests`

### Psalm
`./vendor/bin/psalm`
