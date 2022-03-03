## Queries
### Query1
```php
class Query1 extends ActiveRecord
{
    public function table()
    {
        return 'table_1';
    }

    public function fields()
    {
        return [];
    }
}
```
### Query2
```php
class Query2 extends ActiveRecord
{
    public function table()
    {
        return 'table_2';
    }

    public function fields()
    {
        return [];
    }
}
```
### Query3
```php
class Query3 extends ActiveRecord
{
    public function table()
    {
        return 'table_3';
    }

    public function fields()
    {
        return [];
    }
}
```
## DB Schema
```php
class DBSchema extends Scheme
{
    function __construct()
    {
        $this->addTableList([
            'table_1' => 't1',
            'table_2' => 't2',
            'table_3' => 't3',
        ]);

        $this->rel('{{t1}}', '{{t2}}', '{{t1}}.id = {{t2}}.id');
        $this->rel('{{t2}}', '{{t3}}', '{{t2}}.id = {{t3}}.id');
    }
}
```
# Simple join
## Usage #1
```php
$query =
    Query1::all()
        ->select([
            'f1',
        ])
        ->join('table_2')
        ->join('table_3')
            ->select([
                'f1' => 'af1',
                'f2' => 'af2',
            ]);

$query->getRawSql();
```
## Usage #2
```php
$query =
    Query1::all()
        ->select([
            'f1',
        ])
        ->getQuery2()
        ->getQuery3()
            ->select([
                'f1' => 'af1',
                'f2' => 'af2',
            ]);

$query->getRawSql();
```
## Raw SQL
```sql
select
    table_1.f1,
    table_3.f1 as af1,
    table_3.f2 as af2
from table_1
inner join table_2 on table_1.id = table_2.id
inner join table_3 on table_2.id = table_3.id
```
# Smart join
## Usage #1
```php
$query =
    Query1::all()
        ->select([
            'f1',
        ])
        ->join('table_3', 'inner', 'smart')
            ->select([
                'f1' => 'af1',
                'f2' => 'af2',
            ]);

$query->getRawSql();
```
## Usage #2
```php
$query =
    Query1::all()
        ->select([
            'f1',
        ])
        ->getQuery3(['algorithm' => 'smart'])
            ->select([
                'f1' => 'af1',
                'f2' => 'af2',
            ]);

$query->getRawSql();
```
## Raw SQL
```sql
select
    table_1.f1,
    table_3.f1 as af1,
    table_3.f2 as af2
from table_1
inner join table_2 on table_1.id = table_2.id
inner join table_3 on table_2.id = table_3.id
```
# Join by named relation
## Relation 1
```php
class NamedRel1 extends NamedRel
{
    public function leftTable()
    {
        return 'table_1';
    }

    public function rightTable()
    {
        return 'table_2';
    }

    public function on()
    {
        return 'table_1.id = table_2.id';
    }
}
```
## Relation 2
```php
class NamedRel2 extends NamedRel
{
    public function leftTable()
    {
        return 'table_2';
    }

    public function rightTable()
    {
        return 'table_3';
    }

    public function on()
    {
        return 'table_2.id = table_3.id';
    }
}
```
## Usage
```php
    $query = Query1::all()->join(NamedRel1::class)->join(NamedRel2::class, 'left');

    $query->getRawSql();
```
## Raw SQL
```sql
select
    *
from table_1
inner join table_2 on table_1.id = table_2.id
left join table_3 on table_2.id = table_3.id
```