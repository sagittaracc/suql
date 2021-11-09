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

### Example
Find all the paid orders of the users that are not active anymore and delete them.

```php
$orders =
    Users:find(['active' => false])
        ->select([
            '*',
            Fun::expression('{{initials fullname}} as users_initials')
        ])
        ->getOrders()
        ->where(['paid' => true])
        ->register([
            'initials' => function ($fullname) {
                // Getting the initials of the current user
            }
        ]);

try {
    $transaction = Transaction::begin($orders);

    foreach($orders->fetchAll() as $order) {
        echo $order->users_initials;
        $order->delete();
    }

    $transaction->commit();
} catch (Exception $e) {
    $transaction->rollback();
}

```

### Requirements
The minimum requirement - PHP 5.6.

### Examples
See more examples in the ```tests``` directory

### Tests
This is tested with [PHPUnit](https://phpunit.de/). To run tests:

`./vendor/bin/phpunit`
