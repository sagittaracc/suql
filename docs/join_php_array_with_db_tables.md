## Usage
```php
$tableData = [
    ['id' => 1, 'name' => 'mario'],
    ['id' => 2, 'name' => 'fayword'],
    ['id' => 3, 'name' => '1nterFucker'],
];

$query = TempTable::load($tableData)->join('table_name');
```
## Result
```php
[
    ['id' => '1', 'name' => 'mario', 'field' => '1', 'another_field' => '1'],
    ['id' => '2', 'name' => 'fayword', 'field' => '2', 'another_field' => '2'],
    ['id' => '3', 'name' => '1nterFucker', 'field' => '3', 'another_field' => '3'],
]
```
## Temp Table
```php
class TempTable extends ActiveRecord
{
    public function table()
    {
        return 'temp_table';
    }

    public function create()
    {
        return [
            'id' => 'integer',
            'name' => 'string',
        ];
    }

    public function fields()
    {
        return [];
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
```