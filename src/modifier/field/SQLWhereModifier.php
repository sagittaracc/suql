<?php

namespace suql\modifier\field;

use suql\core\SuQLParam;
use suql\core\SuQLLikeParam;
use suql\core\SuQLBetweenParam;
use suql\core\SuQLInParam;

class SQLWhereModifier
{
  public static function default_where_handler($compare, $suqlParam, $isFilter) {
    $ofield = $suqlParam->getField();
    $placeholder = $suqlParam->getPlaceholder();
    $paramKey = $suqlParam->getParamKey();

    if (!$ofield->getOSelect()->getOSuQL()->hasParam($paramKey))
    {
      $ofield->getOSelect()->getOSuQL()->setParam($paramKey, $suqlParam);
    }

    if ($ofield->hasAlias())
      $ofield->getOSelect()->addHaving("{$ofield->getAlias()} $compare $placeholder");
    else
    {
      if ($isFilter)
      {
        $ofield->getOSelect()->addFilterWhere($paramKey, "{$ofield->getField()} $compare $placeholder");
      }
      else
      {
        $ofield->getOSelect()->addWhere("{$ofield->getField()} $compare $placeholder");
      }
    }
  }

  public static function mod_greater($ofield, $params, $isFilter = false) {
    self::default_where_handler('>', new SuQLParam($ofield, $params), $isFilter);
  }

  public static function mod_greaterOrEqual($ofield, $params, $isFilter = false) {
    self::default_where_handler('>=', new SuQLParam($ofield, $params), $isFilter);
  }

  public static function mod_less($ofield, $params, $isFilter = false) {
    self::default_where_handler('<', new SuQLParam($ofield, $params), $isFilter);
  }

  public static function mod_lessOrEqual($ofield, $params, $isFilter = false) {
    self::default_where_handler('<=', new SuQLParam($ofield, $params), $isFilter);
  }

  public static function mod_equal($ofield, $params, $isFilter = false) {
    self::default_where_handler('=', new SuQLParam($ofield, $params), $isFilter);
  }

  public static function mod_notEqual($ofield, $params, $isFilter = false) {
    self::default_where_handler('<>', new SuQLParam($ofield, $params), $isFilter);
  }

  public static function mod_like($ofield, $params, $isFilter = false) {
    self::default_where_handler('like', new SuQLLikeParam($ofield, $params), $isFilter);
  }

  public static function mod_between($ofield, $params, $isFilter = false) {
    self::default_where_handler('between', new SuQLBetweenParam($ofield, $params), $isFilter);
  }

  public static function mod_in($ofield, $params, $isFilter = false) {
    self::default_where_handler('in', new SuQLInParam($ofield, $params), $isFilter);
  }

  public static function mod_where($ofield, $params) {
    if ($ofield->hasAlias())
      $ofield->getOSelect()->addHaving(str_replace('$', $ofield->getAlias(), $params[0]));
    else
      $ofield->getOSelect()->addWhere(str_replace('$', $ofield->getField(), $params[0]));
  }

  public static function mod_having($ofield, $params) {
    $ofield->getOSelect()->addHaving(str_replace('$', $ofield->getJustField(), $params[0]));
  }
}
