<?php

use sagittaracc\PlaceholderHelper;

class SQLCaseModifier
{
  private static function mod_case($case, $ofield, $params) {
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

  public static function mod_role($ofield, $params) {
    self::mod_case([
      '$ = 1'   => 'admin',
      '$ = 2'   => 'user',
      '$ = 3'   => 'guest',
      'default' => '',
    ], $ofield, $params);
  }

  public static function mod_even($ofield, $params) {
    self::mod_case([
      '$ = 1' => 'no',
      '$ = 0' => 'yes',
    ], $ofield, $params);
  }
}
