<?php

use sagittaracc\PlaceholderHelper;

class SQLCaseModifier
{
  public static function mod_case($case, $ofield, $params) {
    $fieldName = $ofield->getField();
    $caseList = [];

    foreach ($case as $when => $then) {
      if ($when === 'default') {
        $caseList[] = (new PlaceholderHelper("else ?"))->bind($then);
      } else {
        $caseList[] = "when " . str_replace('$', $fieldName, $when) . (new PlaceholderHelper(" then ?"))->bind($then);
      }
    }

    $ofield->setField('case ' . implode(' ', $caseList) . ' end');
  }
}
