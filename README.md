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
```php
$orders =
    Users:find(['active' => false])
         ->getOrders()->where(['paid' => true]);

try {
    $transaction = Transaction::begin($orders);

    foreach($orders->fetchAll() as $order) {
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
