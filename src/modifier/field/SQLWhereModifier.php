<?php
class SQLWhereModifier
{
  public static function default_where_handler($ofield, $params, $compare) {
    if ($ofield->hasAlias())
      $ofield->getOSelect()->addHaving("{$ofield->getAlias()} $compare {$params[0]}");
    else
      $ofield->getOSelect()->addWhere("{$ofield->getField()} $compare {$params[0]}");
  }

  public static function mod_greater($ofield, $params) {
    self::default_where_handler($ofield, $params, '>');
  }

  public static function mod_greaterOrEqual($ofield, $params) {
    self::default_where_handler($ofield, $params, '>=');
  }

  public static function mod_less($ofield, $params) {
    self::default_where_handler($ofield, $params, '<');
  }

  public static function mod_lessOrEqual($ofield, $params) {
    self::default_where_handler($ofield, $params, '<=');
  }

  public static function mod_equal($ofield, $params) {
    self::default_where_handler($ofield, $params, '=');
  }

  public static function mod_notEqual($ofield, $params) {
    self::default_where_handler($ofield, $params, '<>');
  }

  public static function mod_like($ofield, $params) {
    $params[0] = trim($params[0], "'");
    $params[0] = "'%{$params[0]}%'";
    self::default_where_handler($ofield, $params, 'like');
  }

  public static function mod_startsWith($ofield, $params) {
    $params[0] = trim($params[0], "'");
    $params[0] = "'{$params[0]}%'";
    self::default_where_handler($ofield, $params, 'like');
  }

  public static function mod_endsWith($ofield, $params) {
    $params[0] = trim($params[0], "'");
    $params[0] = "'%{$params[0]}'";
    self::default_where_handler($ofield, $params, 'like');
  }

  public static function mod_where($ofield, $params) {
    if ($ofield->hasAlias())
      $ofield->getOSelect()->addHaving(str_replace('$', $ofield->getAlias(), trim($params[0], "'")));
    else
      $ofield->getOSelect()->addWhere(str_replace('$', $ofield->getField(), trim($params[0], "'")));
  }
}
