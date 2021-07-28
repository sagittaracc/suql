<div align="center"><img src="https://i.postimg.cc/9XdgxsV9/suql-sugar-sql.png" alt="suql-sugar-sql" border="0"></div>
<h1 align="center">Sugar SQL</h1>
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
SuQL stands for Sugar SQL and this is an ORM library for PHP.

### Installation
Install the [SuQLBasic Template](https://github.com/sagittaracc/suql-app)

### How it looks
```php

use app\models\Users;
use suql\db\Container;

require 'vendor/autoload.php';

// Connect to the database
Container::create(require __DIR__ . '/app/config/db.php');

// Fetch data from the database
$data = Users::all()->getAdmins()->hidePassword()->fetchAll();

print_r($data);
```

### Requirements
The minimum requirement - PHP 5.6.

### Examples
See more examples in the ```tests``` directory

### Tests
This is tested with [PHPUnit](https://phpunit.de/). To run tests:

`./vendor/bin/phpunit`
