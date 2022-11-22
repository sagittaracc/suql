<?php

namespace suql\modifier\field;

use suql\core\Condition;
use suql\core\param\Between;
use suql\core\param\In;
use suql\core\param\Like;
use suql\core\param\Simple;

/**
 * Where Clause
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLWhereModifier extends SQLBaseModifier
{
    /**
     * Основной обработчик параметризованного where запроса
     * @param string $compare условие сравнения
     * @param \suql\core\Param $suqlParam объект описания параметра запроса where
     * @param boolean $isFilter фильтровое сравнение или нет. При фильтровом не применяется при пустом $suqlParam
     * Пустота $suqlParam определяется для каждого типа параметра отдельно
     */
    private static function where($compare, $ofield, $suqlParam, $isFilter)
    {
        $placeholder = $suqlParam->getPlaceholder();
        $paramKey = $suqlParam->getParamKey();
        $fieldName = $suqlParam->getField();
        $params = $suqlParam->getParams();
        $condition = new Condition($fieldName, "$ $compare $placeholder");

        if ($ofield->hasAlias())
        {
            $ofield->getOSelect()->addHaving($condition->setFormat('%a'));
        }
        else
        {
            if ($isFilter)
            {
                $ofield->getOSelect()->addFilterWhere($paramKey, $condition);
            }
            else
            {
                $ofield->getOSelect()->addWhere(new Condition(new Simple($fieldName, $params), "$ $compare $placeholder"));
            }
        }
    }
    /**
     * Модификатор '>'
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_greater($ofield, $params, $isFilter = false)
    {
        self::where('>', $ofield, new Simple($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '>='
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_greaterOrEqual($ofield, $params, $isFilter = false)
    {
        self::where('>=', $ofield, new Simple($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '<'
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_less($ofield, $params, $isFilter = false)
    {
        self::where('<', $ofield, new Simple($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '<='
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_lessOrEqual($ofield, $params, $isFilter = false)
    {
        self::where('<=', $ofield, new Simple($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '='
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_equal($ofield, $params, $isFilter = false)
    {
        self::where('=', $ofield, new Simple($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '<>'
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_notEqual($ofield, $params, $isFilter = false)
    {
        self::where('<>', $ofield, new Simple($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор like
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_like($ofield, $params, $isFilter = false)
    {
        self::where('like', $ofield, new Like($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор between
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_between($ofield, $params, $isFilter = false)
    {
        self::where('between', $ofield, new Between($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор in
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_in($ofield, $params, $isFilter = false)
    {
        self::where('in', $ofield, new In($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор where
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_where($ofield, $params)
    {
        $ofield->getOSelect()->addWhere($params);
    }
    /**
     * Модификатор having
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_having($ofield, $params)
    {
        $ofield->getOSelect()->addHaving($params);
    }
}
