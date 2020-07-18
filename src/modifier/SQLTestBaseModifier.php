<?php
use core\SuQLReservedWords;

class SQLTestBaseModifier
{
  public static function default_handler($modifier, $ofield, $params) {
    $fieldName = $ofield->getField();
    $aliasName = $ofield->getAlias();
    $strParams = (count($params) > 0 ? ', ' . implode(', ', $params) : '');

    $ofield->setField("$modifier($fieldName" . "$strParams)");
    $ofield->delModifier($modifier);
  }

  public static function mod_case($case, $ofield, $params) {
    $fieldName = $ofield->getField();
    $caseList = [];

    foreach ($case as $when => $then) {
      if ($when === 'default') {
        $caseList[] = "else $then";
      } else {
        $caseList[] = "when " . str_replace('$', $fieldName, $when) . " then $then";
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
    $ofield->getOSelect()->addGroup($ofield->getField());
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
}
