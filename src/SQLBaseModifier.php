<?php
class SQLBaseModifier
{
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
}
