## Nested query
```php
class NestedQueryModel extends ActiveRecord
{
    public function query()
    {
        return 'nested_query_model';
    }

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
class QueryModel extends ActiveRecord
{
    public function query()
    {
        return 'query_model';
    }

    public function table()
    {
        return NestedQueryModel::all()->select(['field_1', 'field_2', 'field_3']);
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return $this->select([
            'field_1',
            'field_2',
        ]);
    }
}
```
## Usage
```php
    /**
        select
            nested_query_model.field_1,
            nested_query_model.field_2
        from (
            select
                table_1.field_1,
                table_1.field_2,
                table_1.field_3
            from table_1
        ) nested_query_model
    */

    QueryModel::all()->getRawSql();
```
