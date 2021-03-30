<?php
class SQLFunctionModifier
{
  public static function default_function_handler($modifier, $ofield, $params) {
    array_unshift($params, $ofield->getField());
    $params = implode(', ', $params);
    $ofield->setField("$modifier($params)");
    $ofield->delModifier($modifier);
  }

  public static function mod_abs($ofield, $params) {
    self::default_function_handler('abs', $ofield, $params);
  }

  public static function mod_count($ofield, $params) {
    self::default_function_handler('count', $ofield, $params);
  }

  public static function mod_min($ofield, $params) {
    self::default_function_handler('min', $ofield, $params);
  }

  public static function mod_max($ofield, $params) {
    self::default_function_handler('max', $ofield, $params);
  }

  public static function mod_sum($ofield, $params) {
    self::default_function_handler('sum', $ofield, $params);
  }

  public static function mod_round($ofield, $params) {
    self::default_function_handler('round', $ofield, $params);
  }

  public static function mod_implode($ofield, $params) {
    $ofield->setField("group_concat(".$ofield->getField()." separator {$params[0]})");
    $ofield->delModifier('implode');
  }

  public static function mod_datediffnow($ofield, $params) {
    $params[] = 'now()';
    self::default_function_handler('datediff', $ofield, $params);
  }

  public static function mod_mod($ofield, $params) {
    self::default_function_handler('mod', $ofield, $params);
  }

  public static function mod_date_format($ofield, $params) {
    self::default_function_handler('date_format', $ofield, $params);
  }

  public static function mod_sign($ofield, $params) {
    self::default_function_handler('sign', $ofield, $params);
  }

  public static function mod_concat($ofield, $params) {
    // TODO: global processing the params
    $params = array_map(fn($param) => str_replace('@', "{$ofield->getTable()}.", $param), $params);
    self::default_function_handler('concat', $ofield, $params);
  }
}
