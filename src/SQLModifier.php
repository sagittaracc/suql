<?php
class SQLModifier extends SQLBaseModifier
{
  // You can define your own modifiers here
  public static function mod_datediff(&$queryObject, $field) {
    parent::default_handler('datediff', $queryObject, $field);
  }
}
