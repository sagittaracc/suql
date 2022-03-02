## Nested query
```php
class NestedQuery extends ActiveRecord
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
## Inner query
```php
class Query extends ActiveRecord
{
    public function table()
    {
        return NestedQuery::all()->select(['field_1', 'field_2', 'field_3']);
    }

    public function fields()
    {
        return [
            'field_1',
            'field_2',
        ];
    }
}
```
## Usage
```php
    /**
        select
            NestedQuery.field_1,
            NestedQuery.field_2
        from (
            select
                table_1.field_1,
                table_1.field_2,
                table_1.field_3
            from table_1
        ) NestedQuery
    */

    Query::all()->getRawSql();
```
