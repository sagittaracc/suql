<?php

namespace suql\modifier\field;

/**
 * Фильтры
 * Расширение возможностей Where Clause
 * Не применяются если параметры фильтра пустые
 * Пустота параметров определяется в классе параметра
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLFilterModifier extends SQLWhereModifier
{
    /**
     * Модификатор filter
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_filter($ofield, $params)
    {
        $filterFunction = $params[0];
        $filterValue = $params[1];

        if (!is_null($filterValue))
        {
            $whereModifier = "mod_$filterFunction";
            if (method_exists(static::class, $whereModifier))
            {
                static::$whereModifier($ofield, [$filterValue], true);
            }
        }
    }
}
