```php
class TableModel extends ActiveRecord
{
    public function query()
    {
        return 'tbl_1';
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
```php
class NestedQueryModel extends ActiveRecord
{
    public function query()
    {
        return 'nested_query_model';
    }

    public function table()
    {
        return TableModel::all()->select(['field_1', 'field_2', 'field_3']);
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
```php
    /**
        select
            tbl_1.field_1,
            tbl_1.field_2
        from (
            select
                table_1.field_1,
                table_1.field_2,
                table_1.field_3
            from table_1
        ) tbl_1
    */

    NestedQueryModel::all()->getRawSql();
```