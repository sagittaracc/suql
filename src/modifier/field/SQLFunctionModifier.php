<?php

namespace suql\modifier\field;

/**
 * Модификатор Функция
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLFunctionModifier
{
    /**
     * Основной обработчик функций
     * @param string $func название функции
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    private static function func($func, $ofield, $params)
    {
        array_unshift($params, $ofield->getField());
        $params = implode(', ', $params);
        $ofield->setField("$func($params)");
    }
    /**
     * Модуль
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_abs($ofield, $params)
    {
        self::func('abs', $ofield, $params);
    }
    /**
     * Количество записей
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_count($ofield, $params)
    {
        self::func('count', $ofield, $params);
    }
    /**
     * Минимальный
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_min($ofield, $params)
    {
        self::func('min', $ofield, $params);
    }
    /**
     * Максимальный
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_max($ofield, $params)
    {
        self::func('max', $ofield, $params);
    }
    /**
     * Сумма
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_sum($ofield, $params)
    {
        self::func('sum', $ofield, $params);
    }
    /**
     * Округление чисел
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_round($ofield, $params)
    {
        self::func('round', $ofield, $params);
    }
    /**
     * Group concat
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_implode($ofield, $params)
    {
        $ofield->setField("group_concat(" . $ofield->getField() . " separator {$params[0]})");
    }
    /**
     * Промежуток времени до текущего момента
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_datediffnow($ofield, $params)
    {
        $params[] = 'now()';
        self::func('datediff', $ofield, $params);
    }
    /**
     * Остаток от деления
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_mod($ofield, $params)
    {
        self::func('mod', $ofield, $params);
    }
    /**
     * Форматирование даты
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_date_format($ofield, $params)
    {
        self::func('date_format', $ofield, $params);
    }
    /**
     * Знак
     * @param suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_sign($ofield, $params)
    {
        self::func('sign', $ofield, $params);
    }
}
