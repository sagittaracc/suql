<?php
namespace sagittaracc\helpers;

class ArrayHelper {
  public static function slice_by_keys($array, $keys) {
    if (!is_array($array) || !is_array($keys))
      return null;

    if (empty($keys))
      return [];

    return array_intersect_key($array, array_fill_keys($keys, 0));
  }

  public static function isSequential($arr) {
    return array_keys($arr) === range(0, count($arr) - 1);
  }
}
