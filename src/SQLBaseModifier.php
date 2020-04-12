<?php
class SQLBaseModifier
{
  public static function default_handler($modifier, &$queryObject, $field) {
    $fieldName = $queryObject['select'][$field]['field'];
    $aliasName = $queryObject['select'][$field]['alias'];
    $params    = SuQLReservedWords::toSql($queryObject['select'][$field]['modifier'][$modifier]);
    $strParams = (count($params) > 0 ? ', ' . implode(', ', $params) : '');

    $queryObject['select']["$modifier($fieldName" . "$strParams)" . ($aliasName ? "@$aliasName" : '')] = [
      'field' => $fieldName,
      'alias' => $aliasName,
    ];

    unset($queryObject['select'][$field]);
  }

  public static function mod_asc(&$queryObject, $field) {
    $queryObject['order'][] = [
      'field' => $queryObject['select'][$field]['field'],
      'direction' => 'asc',
    ];
  }

  public static function mod_desc(&$queryObject, $field) {
    $queryObject['order'][] = [
      'field' => $queryObject['select'][$field]['field'],
      'direction' => 'desc',
    ];
  }

  public static function mod_group(&$queryObject, $field) {
    $queryObject['group'][] = $queryObject['select'][$field]['field'];
    if (!empty($queryObject['select'][$field]['modifier']['group']))
    {
      $group = $queryObject['select'][$field]['alias'];
      $name = $queryObject['select'][$field]['modifier']['group'][0];
      $queryObject['having'][] = "$group = $name";
    }
  }

  public static function mod_count(&$queryObject, $field) {
    self::default_handler('count', $queryObject, $field);
  }

  public static function mod_join(&$queryObject, $field) {
    $fieldOptions = $queryObject['select'][$field];
    $table = $fieldOptions['table'];
    $queryObject['join'][$table]['type'] = 'inner';
    $queryObject['join'][$table]['on'] = $fieldOptions['field'] . ' = ' . $fieldOptions['modifier']['join'][0];
  }
}
