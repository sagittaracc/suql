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

use app\models\Users;
use suql\db\Container;
use suql\syntax\Raw;

require '../vendor/autoload.php';

// Connect to the database
Container::create(require __DIR__ . '/config/db.php');

// Fetch data from the database
$data =
    Users::all()
        ->select([
            'login',
            'address',
        ])
        ->getCounter()
        ->getConsumption()
            ->order([
                'time' => 'desc',
                'counter_id',
            ])
        ->getResurs()
        ->leftJoin('tarif')
            ->select([
                Raw::expression("CONCAT(@name, ' (', FORMAT(@price, 0), ' р.)') AS tarif"),
            ])
    ->fetchAll();

echo "<pre>";
print_r($data);
echo "</pre>";
```

## Conclusion
SuQL is all about modifiers. They already replace standart SQL clauses such as `WHERE`, `GROUP`, `JOIN`, `ORDER` and SQL functions etc.
More than that, you can develop your own modifiers.
