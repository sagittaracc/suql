<?php
class SQLUserNameModifier
{
  public static function mod_ucname($ofield, $params)
  {
    $ofield->setField("CONCAT(UCASE(LEFT({$ofield->getField()}, 1)), SUBSTRING({$ofield->getField()}, 2))");
  }
}
