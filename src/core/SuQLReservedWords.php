<?php
namespace core;

class SuQLReservedWords
{
  private static $list = [
    'now' => 'now()',
  ];

  public static function toSql($array) {
    foreach ($array as &$word) {
      if (array_key_exists($word, self::$list))
        $word = self::$list[$word];
    }
    unset($word);
    return $array;
  }
}
