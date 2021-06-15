<?php

namespace suql\builder;

/**
 * MySQL сборщик
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
final class MySQLBuilder extends SQLBuilder
{
    // FIX: This is a mess
    public function createTable($model)
    {
        $typeList = [
            'integer' => "INT(10) NOT NULL",
            'string' => "VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci'",
        ];

        $table = $model->table();
        $fieldList = $model->create();

        $s = [];
        foreach ($fieldList as $field => $type) {
            $s[] = "\t`$field` {$typeList[$type]}";
        }
        $s = implode(",\n", $s);

        return <<<SQL
CREATE TABLE `$table` (
$s
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
SQL;
    }
}
