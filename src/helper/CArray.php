<?php
namespace Helper;

class CArray {
  public static function slice_by_keys($array, $keys) {
    if (!is_array($array) || !is_array($keys))
      return null;

    if (empty($keys))
      return [];

    return array_intersect_key($array, array_fill_keys($keys, 0));
  }
}
