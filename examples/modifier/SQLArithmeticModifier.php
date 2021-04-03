<?php
class SQLArithmeticModifier
{
  public static function mod_div($ofield, $params)
  {
    $ofield->setField("{$ofield->getField()} / {$params[0]}");
  }
}
