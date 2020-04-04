<?php
class SQLModifier extends SQLBaseModifier
{
  // You can define your own modifiers here
  public static function default_handler($modifier, &$queryObject, $field) {
    $fieldName = $queryObject['select'][$field]['field'];
    $aliasName = $queryObject['select'][$field]['alias'];

    $queryObject['select']["$modifier($fieldName)" . ($aliasName ? "@$aliasName" : '')] = [
      'field' => $fieldName,
      'alias' => $aliasName,
    ];

    unset($queryObject['select'][$field]);
  }

}
