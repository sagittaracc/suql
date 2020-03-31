<?php
class SQLModifier
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
  }

  public static function mod_count(&$queryObject, $field) {
    $fieldName = $queryObject['select'][$field]['field'];
    $aliasName = $queryObject['select'][$field]['alias'];

    $queryObject['select']["count($fieldName)" . ($aliasName ? " as $aliasName" : '')] = [
			'field' => "count($fieldName)",
			'alias' => $aliasName,
		];

    unset($queryObject['select'][$field]);
  }
}
