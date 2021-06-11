<h1 align="center">Sugar SQL</h1>

### SuQL
SuQL stands for Sugar SQL and this is an ORM library for MySQL.

### How to use it
```
// Create a new project
> php suql --create-project --name app --db test

// Create a model that refers to the user table in the db
> php suql --create-model --name app\user --type model

// Create an entry point
> php suql --create-entry-point --name index --model app\user

// Run the entry point
> php index.php
```

### How it looks
```php

use app\models\Users;
use suql\db\Container;

require '../vendor/autoload.php';

// Connect to the database
Container::create(require __DIR__ . '/config/db.php');

// Fetch data from the database
$data =
    Users::all()
        ->getCounter()
        ->getConsumption()
            ->order([
                'time' => 'desc',
                'counter_id',
            ])
        ->getResurs()
        ->getTarif([
            'join' => 'left',
        ])
    ->fetchAll();

echo "<pre>";
print_r($data);
echo "</pre>";
```

### Requirements
The minimum requirement - PHP 5.6. 

### Examples
More examples in the ```tests``` directory

### Tests
`./vendor/bin/phpunit tests`

## Conclusion
SuQL is all about modifiers. They already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` and SQL functions etc.
More than that, you can develop your own modifiers.
