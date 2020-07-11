<?php
namespace Helper;

class SuQLObjectReader {
  public static function getAllTheQueryList($osuql) {
    return array_keys($osuql['queries']);
  }

  public static function getQuery(&$osuql, $name) {
    return $osuql['queries'][$name];
  }
}
