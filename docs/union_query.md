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
## Usage
```php
    /**
        (select table_1.field_1, table_1.field_2, table_1.field_3 from table_1)
            union
        (select table_1.field_4, table_1.field_5, table_1.field_6 from table_1)
    */

    $query1 = Query::all()->select(['field_1', 'field_2', 'field_3'])->as('query1');
    $query2 = Query::all()->select(['field_4', 'field_5', 'field_6'])->as('query2');

    $query = $query1->and([$query2]);
    $query->getRawSql();
```
