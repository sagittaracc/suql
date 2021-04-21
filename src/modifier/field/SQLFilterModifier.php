<?php

class SQLFilterModifier
{
  public static function mod_filter($ofield, $params)
  {
    $filterFunction = $params[0];
    $filterValue = $params[1];

    if (!is_null($filterValue))
    {
      $whereModifier = "mod_$filterFunction";
      if (method_exists('SQLWhereModifier', $whereModifier))
      {
        SQLWhereModifier::$whereModifier($ofield, [$filterValue], true);
      }
    }
  }
}
