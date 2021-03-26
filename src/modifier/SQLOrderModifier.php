<?php
class SQLOrderModifier extends SQLBaseModifier
{
  public static function default_order_handler($ofield, $params, $direction) {
    $field = $ofield->hasAlias() ? $ofield->getAlias() : $ofield->getField();
    $ofield->getOSelect()->addOrder($field, $direction);
  }

  public static function mod_asc($ofield, $params) {
    self::default_order_handler($ofield, $params, 'asc');
  }

  public static function mod_desc($ofield, $params) {
    self::default_order_handler($ofield, $params, 'desc');
  }
}
