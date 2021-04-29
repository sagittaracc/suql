<?php

namespace suql\modifier\field;

use suql\core\SuQLParam;
use suql\core\SuQLLikeParam;
use suql\core\SuQLBetweenParam;
use suql\core\SuQLCondition;
use suql\core\SuQLFieldName;
use suql\core\SuQLInParam;

/**
 * Where Clause
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SQLWhereModifier
{
    /**
     * Основной обработчик параметризованного where запроса
     * @param string $compare условие сравнения
     * @param suql\core\SuQLParam $suqlParam объект описания параметра запроса where
     * @param boolean $isFilter фильтровое сравнение или нет. При фильтровом не применяется при пустом $suqlParam
     * Пустота $suqlParam определяется для каждого типа параметра отдельно
     */
    private static function where($compare, $suqlParam, $isFilter)
    {
        $ofield = $suqlParam->getField();
        $placeholder = $suqlParam->getPlaceholder();
        $paramKey = $suqlParam->getParamKey();

        $fieldName = new SuQLFieldName($ofield->getTable(), [$ofield->getField() => $ofield->getAlias()]);
        $condition = new SuQLCondition($fieldName, "$ $compare $placeholder");

        if (!$ofield->getOSelect()->getOSuQL()->hasParam($paramKey))
        {
            $ofield->getOSelect()->getOSuQL()->setParam($paramKey, $suqlParam);
        }

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
                $ofield->getOSelect()->addWhere($condition);
            }
        }
    }
    /**
     * Модификатор '>'
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_greater($ofield, $params, $isFilter = false)
    {
        self::where('>', new SuQLParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор '>='
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_greaterOrEqual($ofield, $params, $isFilter = false)
    {
        self::where('>=', new SuQLParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор '<'
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_less($ofield, $params, $isFilter = false)
    {
        self::where('<', new SuQLParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор '<='
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_lessOrEqual($ofield, $params, $isFilter = false)
    {
        self::where('<=', new SuQLParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор '='
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_equal($ofield, $params, $isFilter = false)
    {
        self::where('=', new SuQLParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор '<>'
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_notEqual($ofield, $params, $isFilter = false)
    {
        self::where('<>', new SuQLParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор like
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_like($ofield, $params, $isFilter = false)
    {
        self::where('like', new SuQLLikeParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор between
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_between($ofield, $params, $isFilter = false)
    {
        self::where('between', new SuQLBetweenParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор in
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $param параметры модификатора
     * @param boolean $isFilter фильтровое условие или нет
     */
    public static function mod_in($ofield, $params, $isFilter = false)
    {
        self::where('in', new SuQLInParam($ofield, $params), $isFilter);
    }
    /**
     * Модификатор where
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_where($ofield, $params)
    {
        $fieldName = new SuQLFieldName($ofield->getTable(), [$ofield->getField() => $ofield->getAlias()]);
        $condition = new SuQLCondition($fieldName, $params[0]);

        if ($ofield->hasAlias()) {
            $ofield->getOSelect()->addHaving($condition->setFormat('%a'));
        } else {
            $ofield->getOSelect()->addWhere($condition);
        }
    }
    /**
     * Модификатор having
     * @param suql\core\SuQLField $ofield объект поля к которому применяется модификатор
     * @param array $params параметры модификатора
     */
    public static function mod_having($ofield, $params)
    {
        $fieldName = new SuQLFieldName($ofield->getTable(), [$ofield->getField() => $ofield->getAlias()]);
        $condition = new SuQLCondition($fieldName, $params[0], '%a');

        $ofield->getOSelect()->addHaving($condition);
    }
}
