<?php
class SQLWhereModifier extends SQLBaseModifier
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

  public static function mod_notEqual($ofield, $params) {
    self::default_where_handler($ofield, $params, '<>');
  }
}
