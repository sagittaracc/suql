<?php
namespace Helper;

class SuQLObjectReader {
  public static function getAllTheQueryList($osuql) {
    return array_keys($osuql['queries']);
  }

  public static function &getQuery(&$osuql, $name) {
    if (isset($osuql['queries'][$name]))
      $queryObject = &$osuql['queries'][$name];
    else
      $queryObject = null;

    return $queryObject;
  }

  public static function &getQueryType(&$osuql, $name) {
    $queryObject = &self::getQuery($osuql, $name);
    $type = &$queryObject['type'];
    return $type;
  }

  public static function &getQuerySuqlString(&$osuql, $name) {
    $queryObject = &self::getQuery($osuql, $name);
    $suqlString = &$queryObject['suql'];
    return $suqlString;
  }
}
