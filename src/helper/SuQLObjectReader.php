<?php
namespace Helper;

class SuQLObjectReader {
  public static function getAllTheQueryList($osuql) {
    return array_keys($osuql['queries']);
  }
}
