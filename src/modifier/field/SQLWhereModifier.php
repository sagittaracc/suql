<?php

namespace suql\modifier\field;

use suql\core\LikeParam;
use suql\core\BetweenParam;
use suql\core\Condition;
use suql\core\FieldName;
use suql\core\InParam;
use suql\core\SimpleParam;

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
                $ofield->getOSelect()->addWhere(new Condition(new SimpleParam($fieldName, $params), "$ $compare $placeholder"));
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
        self::where('>', $ofield, new SimpleParam($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '>='
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_greaterOrEqual($ofield, $params, $isFilter = false)
    {
        self::where('>=', $ofield, new SimpleParam($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '<'
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_less($ofield, $params, $isFilter = false)
    {
        self::where('<', $ofield, new SimpleParam($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '<='
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_lessOrEqual($ofield, $params, $isFilter = false)
    {
        self::where('<=', $ofield, new SimpleParam($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '='
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_equal($ofield, $params, $isFilter = false)
    {
        self::where('=', $ofield, new SimpleParam($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор '<>'
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_notEqual($ofield, $params, $isFilter = false)
    {
        self::where('<>', $ofield, new SimpleParam($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор like
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_like($ofield, $params, $isFilter = false)
    {
        self::where('like', $ofield, new LikeParam($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор between
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_between($ofield, $params, $isFilter = false)
    {
        self::where('between', $ofield, new BetweenParam($ofield->getFieldNameObject(), $params), $isFilter);
    }
    /**
     * Модификатор in
     * @param \suql\core\Field $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_in($ofield, $params, $isFilter = false)
    {
        self::where('in', $ofield, new InParam($ofield->getFieldNameObject(), $params), $isFilter);
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
