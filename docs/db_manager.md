## Usage #1
```php
$db = new suql\db\Manager();
$query = $db->entity('table_1')->order(['f1']);
$query->getRawSql();
```
```sql
select
    *
from table_1
order by table_1.f1 asc
```
## Usage #2
```php
$db = new suql\db\Manager(null, DbSchema::class);

$query =
    $db->entity('table_1')
        ->select(['f1'])
    ->with('table_2')
    ->with('table_3')
        ->select(['f1']);

$query->getRawSql();
```
```sql
select
    table_1.f1,
    table_3.f1
from table_1
inner join table_2 on table_1.id = table_2.id
inner join table_3 on table_2.id = table_3.id
```
## Usage #3
```php
$db = new suql\db\Manager(null, DbSchema::class);

$query =
    $db->entity('table_1')
        ->select(['f1'])
    ->with('table_3', 'inner', 'smart')
        ->select(['f1']);

$query->getRawSql();
```
```sql
select
    table_1.f1,
    table_3.f1
from table_1
inner join table_2 on table_1.id = table_2.id
inner join table_3 on table_2.id = table_3.id
```
## Db Schema
```php
class DbSchema extends Scheme
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