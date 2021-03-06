<?php

namespace suql\syntax;

use suql\core\Condition;
use suql\core\FieldName;

/**
 * Сахар для suql\core\Expression
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Expression
{
    /**
     * Создает объект suql\core\Expression
     * @param string $expr
     * @param array $cond
     */
    public static function create($expr, $cond)
    {
        $conditionList = [];

        foreach ($cond as $options) {
            $class = $options[0];
            $fieldOptions = $options[1];
            $table = $fieldOptions[0];
            $field = $fieldOptions[1];
            $condition = $options[2];
            $params = $options[3];

            $conditionList[] = new Condition(new $class(new FieldName($table, $field), $params), $condition);
        }

        return new \suql\core\Expression($expr, $conditionList);
    }
}