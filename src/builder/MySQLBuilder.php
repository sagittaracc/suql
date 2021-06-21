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
        $createTableQuery = <<<SQL
CREATE $tableType TABLE `$table` (
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
    /**
     * Генерация запроса создания временной таблицы
     * @param suql\syntax\SuQL $model
     */
    public function createTemporaryTable($model)
    {
        return $this->createTable($model, false);
    }
}
