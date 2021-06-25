<div align="center"><img src="https://i.ibb.co/NNq7DLQ/suql-sugar-sql.png" alt="suql-sugar-sql" border="0"></div>
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
SuQL stands for Sugar SQL and this is an ORM library for MySQL.

### Why?
I use it at my work place. This is how:
```php

use app\models\Users;
use suql\db\Container;

require 'vendor/autoload.php';

Container::create(require __DIR__ . '/app/config/db.php');

$data =
    Users::all()
    	->where(['id' => 11])
	->getCounters()
        ->getResurs()
        ->getManual()
            ->order(['time' => 'desc'])
        ->getTarif(['join' => 'left'])
        ->fetchAll();

return $data;
```

### How to use it
1. Install via composer
```
composer require sagittaracc/suql
```
2. Create a new project
```
> ./suql --create-project --name app --db test
```
3. Create a model that refers to a table in the db
```
> ./suql --create-model --name app\users --type model
```
4. Create a table from the model in database
```
> ./suql --create-table --model app\models\Users
```
5. Create an entry point
```
> ./suql --create-entry-point --name index --model app\users
```
6. Run the entry point
```
> php index.php
```

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

Model ```Users```
```php

namespace app\models;

use app\records\ActiveRecord;

class Users extends ActiveRecord
{
    public function table()
    {
        return 'users';
    }

    public function create()
    {
    	return [
	    'id' => 'integer',
	    'login' => 'string',
	    'password' => 'string',
	];
    }

    public function fields()
    {
        return [];
    }

    public function getAdmins()
    {
        return
	    $this->getGroups(['algorithm' => 'smart'])
	         ->where('name', 'like', '%admin%');
    }

    public function postHidePassword($data)
    {
        return array_map(function($row) {
            $row['password'] = '***';
            return $row;
        }, $data);
    }
}
```

Model ```Group```
```php

namespace app\models;

use app\records\ActiveRecord;

class Groups extends ActiveRecord
{
    public function table()
    {
        return 'groups';
    }

    public function create()
    {
        return [
            'id' => 'integer',
            'name' => 'string',
        ];
    }

    public function fields()
    {
        return [];
    }
}
```

### Requirements
The minimum requirement - PHP 5.6.

### Examples
More examples in the ```tests``` directory

### Tests
`./vendor/bin/phpunit tests`
