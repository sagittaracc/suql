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
    public function createTable($model)
    {
        $table = $model->table();
        $createTableQuery = <<<SQL
CREATE TABLE `$table` (
#fieldList{`#key` #value}
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
SQL;
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
}
