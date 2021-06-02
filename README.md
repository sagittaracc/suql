<h1 align="center">Sugar SQL</h1>

### SuQL
SuQL stands for Sugar SQL and this is just a query builder... 

### Requirements
The minimum requirement - PHP 5.6. 

### Examples
More examples in the ```tests``` directory

### Tests
`./vendor/bin/phpunit tests`

### Example with Database
```php

use app\models\User;
use suql\db\Container;

require 'vendor/autoload.php';

Container::create(require __DIR__ . '/app/config/db.php');

$users = User::all()->select(['login' => 'uname']);
$groups = $users->getGroup()->fetchAll();

print_r($groups);
```

In the ```ActiveRecord``` class
```php
...
public function getDb()
{
    return Container::get('db');
}
...
```

## Conclusion
SuQL is all about modifiers. They already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` and SQL functions etc.
More than that, you can develop your own modifiers.
