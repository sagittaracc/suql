<?php
use core\SuQLReservedWords;
use Helper\CPlaceholder;

class SQLBaseModifier
{
  public static function default_handler($modifier, $ofield, $params) {
    array_unshift($params, $ofield->getField());
    $params = implode(', ', $params);
    $ofield->setField("$modifier($params)");
    $ofield->delModifier($modifier);
  }

  public static function mod_case($case, $ofield, $params) {
    $fieldName = $ofield->getField();
    $caseList = [];

    foreach ($case as $when => $then) {
      if ($when === 'default') {
        $caseList[] = (new CPlaceholder("else ?"))->bind($then);
      } else {
        $caseList[] = "when " . str_replace('$', $fieldName, $when) . (new CPlaceholder(" then ?"))->bind($then);
      }
    }

    $ofield->setField('case ' . implode(' ', $caseList) . ' end');
  }

  public static function mod_asc($ofield, $params) {
    $field = $ofield->hasAlias() ? $ofield->getAlias() : $ofield->getField();
    $ofield->getOSelect()->addOrder($field, 'asc');
  }

  public static function mod_desc($ofield, $params) {
    $field = $ofield->hasAlias() ? $ofield->getAlias() : $ofield->getField();
    $ofield->getOSelect()->addOrder($field, 'desc');
  }

  public static function mod_group($ofield, $params) {
    $ofield->getOSelect()->addGroup($ofield->getOriginalField());
    if (!empty($params)) {
      $having = $ofield->getAlias() . ' = ' . $params[0];
      $ofield->getOSelect()->addHaving($having);
    }
  }

  public static function mod_count($ofield, $params) {
    self::default_handler('count', $ofield, $params);
  }

  public static function mod_min($ofield, $params) {
    self::default_handler('min', $ofield, $params);
  }

  public static function mod_max($ofield, $params) {
    self::default_handler('max', $ofield, $params);
  }

  public static function mod_sum($ofield, $params) {
    self::default_handler('sum', $ofield, $params);
  }

  public static function mod_round($ofield, $params) {
    self::default_handler('round', $ofield, $params);
  }

  public static function mod_greater($ofield, $params) {
    if ($ofield->hasAlias())
      $ofield->getOSelect()->addHaving($ofield->getAlias() . ' > ' . $params[0]);
    else
      $ofield->getOSelect()->addWhere($ofield->getField() . ' > ' . $params[0]);
  }

  public static function mod_implode($ofield, $params) {
    $ofield->setField("group_concat(".$ofield->getField()." separator {$params[0]})");
    $ofield->delModifier('implode');
  }

  public static function mod_andNotEqual($ofield, $params) {
    // Default handler for adding where clause
    if ($ofield->hasAlias())
      $ofield->getOSelect()->addHaving($ofield->getAlias() . ' <> ' . $params[0]);
    else
      $ofield->getOSelect()->addWhere($ofield->getField() . ' <> ' . $params[0]);
  }
}
