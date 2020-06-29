<?php
namespace Helper;

class SuQLJoin {
  public static function getTargetLink($scheme, $tableList, $table) {
    $tableLinks = array_keys($scheme[$table]);
    $possibleLinks = array_intersect($tableLinks, $tableList);
    return array_pop($possibleLinks);
  }
}
