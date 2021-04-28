<?php

namespace suql\modifier\field;

class SQLGroupModifier
{
  public static function mod_group($ofield, $params) {
    $ofield->getOSelect()->addGroup($ofield->getOriginalField());
  }
}
