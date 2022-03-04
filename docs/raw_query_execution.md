## config/db.php
```php
return [
    'connection' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => '',
        'user' => 'root',
        'pass' => '',
    ],
];
```
## config/db-test.php
```php
return [
    'connection' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'db_test',
        'user' => 'root',
        'pass' => '',
    ],
];
```
## Create a database
```php
Container::create(require('config/db.php'));
Query::create('create database db_test')->setConnection('connection')->exec();
```
## Create a table
```php
Container::add(require('config/db-test.php'));
Query::create('create table table_name(field int, another_field int)')->setConnection('db_test')->exec();
```
## Insert data
```php
Query::create('insert into table_name (field, another_field) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
Query::create('insert into table_name (field, another_field) values (?, ?), (?, ?), (?, ?)')->setConnection('db_test')->exec([4, 4, 5, 5, 6, 6]);
```
## Delete data
```php
$count = Query::create('delete from table_name')->setConnection('db_test')->exec();
// $count = rows count that have been deleted
```
## Nested query
```php
$query = Query::create('select * from ?')->bind([Query1::all()])->getQuery();
```
```sql
select * from (
    select * from table_1
)
```