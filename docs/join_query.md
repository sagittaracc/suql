# Simple join

...

# Smart join

...

# Join by named relation

## Query
```php
class Query extends ActiveRecord
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
    $query = Query::all()->join(NamedRel1::class)->join(NamedRel2::class, 'left');

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
