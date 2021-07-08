<?php

namespace suql\builder;

use sagittaracc\ArrayHelper;
use sagittaracc\PlaceholderHelper;
use sagittaracc\SimpleList;

/**
 * MySQL сборщик
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
final class MySQLBuilder extends SQLBuilder
{
    /**
     * Генерация запроса создания таблицы
     * @param suql\syntax\SuQL $model
     * @param boolean $temp
     */
    public function createTable($model, $temp = false)
    {
        $table = $model->table();
        $tableType = $temp ? 'TEMPORARY' : '';
        $createTableQuery = "CREATE $tableType TABLE `$table` (#fieldList{`#key` #value}) COLLATE='utf8mb4_general_ci' ENGINE=InnoDB";
        $fieldList = ArrayHelper::join($model->create(), [
            'integer' => "INT(10) NOT NULL",
            'string' => "VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci'",
        ]);

        return (new PlaceholderHelper($createTableQuery))->bindObject(SimpleList::create([
            'name' => 'fieldList',
            'separator' => ",\n",
            'list' => $fieldList,
        ]));
    }
    /**
     * Генерация запроса создания временной таблицы
     * @param suql\syntax\SuQL $model
     */
    public function createTemporaryTable($model)
    {
        return $this->createTable($model, true);
    }
    /**
     * Загрузить массив в таблицу
     * @param string $table
     * @param array $data
     */
    public function insertIntoTable($table, $data)
    {
        $insertIntoTableQuery = "INSERT INTO $table ({columns}) VALUES {rows}";
        $array = ArrayHelper::setArray($data);

        $header = $array->getHeader();
        $body = $array->getBody();

        $columns = implode(',', $header);
        $rows = call_user_func_array([
            (new PlaceholderHelper)->stringOfChar(count($body), '?')->setParenthesis('(', ')'),
            'bind'
        ], $body);

        return str_replace(
            ['{columns}', '{rows}'],
            [$columns, $rows],
            $insertIntoTableQuery
        );
    }
}
