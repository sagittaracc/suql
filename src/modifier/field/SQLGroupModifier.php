<?php
class SQLGroupModifier
{
  public static function mod_group($ofield, $params) {
    $ofield->getOSelect()->addGroup($ofield->getOriginalField());
  }
}
