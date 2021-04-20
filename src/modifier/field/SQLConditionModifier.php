<?php
class SQLConditionModifier
{
  private static function default_condition_handler($function, $condition, $ofield, $params)
  {
    $ofield->setField(str_replace('$', $ofield->getField(), $condition));
    SQLFunctionModifier::default_function_handler($function, $ofield, $params);
  }

  public static function mod_ifNull($ofield, $params)
  {
    self::default_condition_handler('if', '$ is null', $ofield, $params);
  }

  public static function mod_ifZero($ofield, $params)
  {
    self::default_condition_handler('if', '$ = 0', $ofield, $params);
  }

  public static function mod_ifNegative($ofield, $params)
  {
    self::default_condition_handler('if', '$ < 0', $ofield, $params);
  }
}
