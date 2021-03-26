<?php
namespace sagittaracc\helpers;

class StringHelper {
  public static function stripDoubleSpaces($s) {
    return str_replace('  ', ' ', $s);
  }
}
