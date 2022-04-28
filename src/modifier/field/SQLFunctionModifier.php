<?php

namespace suql\modifier\field;

use sagittaracc\PlaceholderHelper;

/**
 * Модификатор Функция
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLFunctionModifier extends SQLBaseModifier
{
    /**
     * Основной обработчик функций
     * @param string $func название функции
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function func($func, $ofield, $params)
    {
        $params = array_map(function($param) {
            return (new PlaceholderHelper('?'))->bind($param);
        }, $params);

        array_unshift($params, $ofield->getField());
        $params = implode(', ', $params);

        $ofield->setField("$func($params)");
    }
    /**
     * Group concat
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_implode($ofield, $params)
    {
        $ofield->setField("group_concat(" . $ofield->getField() . " separator {$params[0]})");
    }
}
