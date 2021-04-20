<?php
class SQLWhereModifier
{
  public static function default_where_handler($ofield, $params, $compare, $isFilter) {
    $placeholder = substr($params[0], 0, 1) === ':'
                      ? $params[0]
                      : $ofield->getPlaceholderName();

    if (!$ofield->getOSelect()->getOSuQL()->hasParam($placeholder))
    {
      $ofield->getOSelect()->getOSuQL()->setParam($placeholder, $params[0]);
    }

    if ($ofield->hasAlias())
      $ofield->getOSelect()->addHaving("{$ofield->getAlias()} $compare $placeholder");
    else
    {
      if ($isFilter)
      {
        $ofield->getOSelect()->addFilterWhere("$placeholder", "{$ofield->getField()} $compare $placeholder");
      }
      else
      {
        $ofield->getOSelect()->addWhere("{$ofield->getField()} $compare $placeholder");
      }
    }
  }

  public static function mod_greater($ofield, $params, $isFilter = false) {
    self::default_where_handler($ofield, $params, '>', $isFilter);
  }

  public static function mod_greaterOrEqual($ofield, $params, $isFilter = false) {
    self::default_where_handler($ofield, $params, '>=', $isFilter);
  }

  public static function mod_less($ofield, $params, $isFilter = false) {
    self::default_where_handler($ofield, $params, '<', $isFilter);
  }

  public static function mod_lessOrEqual($ofield, $params, $isFilter = false) {
    self::default_where_handler($ofield, $params, '<=', $isFilter);
  }

  public static function mod_equal($ofield, $params, $isFilter = false) {
    self::default_where_handler($ofield, $params, '=', $isFilter);
  }

  public static function mod_notEqual($ofield, $params, $isFilter = false) {
    self::default_where_handler($ofield, $params, '<>', $isFilter);
  }

  public static function mod_like($ofield, $params, $isFilter = false) {
    $params[0] = "%{$params[0]}%";
    self::default_where_handler($ofield, $params, 'like', $isFilter);
  }

  public static function mod_startsWith($ofield, $params, $isFilter = false) {
    $params[0] = "{$params[0]}%";
    self::default_where_handler($ofield, $params, 'like', $isFilter);
  }

  public static function mod_endsWith($ofield, $params, $isFilter = false) {
    $params[0] = "%{$params[0]}";
    self::default_where_handler($ofield, $params, 'like', $isFilter);
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
