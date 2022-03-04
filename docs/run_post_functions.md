## Usage
```php
$data = TableName::all()->fieldToInt()->fetchAll();
```
## Result
```php
[
    ['field' => 1, 'another_field' => '1'],
    ['field' => 2, 'another_field' => '2'],
    ['field' => 3, 'another_field' => '3'],
    ['field' => 4, 'another_field' => '4'],
    ['field' => 5, 'another_field' => '5'],
    ['field' => 6, 'another_field' => '6'],
]
```
## If we don't use that post function
```php
$data = TableName::all()->fetchAll();
```
## Result
```php
[
    ['field' => '1', 'another_field' => '1'],
    ['field' => '2', 'another_field' => '2'],
    ['field' => '3', 'another_field' => '3'],
    ['field' => '4', 'another_field' => '4'],
    ['field' => '5', 'another_field' => '5'],
    ['field' => '6', 'another_field' => '6'],
]
```
## TableName model
```php
class TableName extends ActiveRecord
{
    public function table()
    {
        return 'table_name';
    }

    public function fields()
    {
        return [];
    }

    public function commandFieldToInt($data)
    {
        return array_map(function($row) {
            $row['field'] = intval($row['field']);
            return $row;
        }, $data);
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
```