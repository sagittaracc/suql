<?php
class SQLGroupModifier
{
  public static function mod_group($ofield, $params) {
    $ofield->getOSelect()->addGroup($ofield->getOriginalField());
    if (!empty($params)) {
      $having = $ofield->getAlias() . ' = ' . $params[0];
      $ofield->getOSelect()->addHaving($having);
    }
  }
}
