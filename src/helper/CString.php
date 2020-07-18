<?php
namespace Helper;

class CString {
  public static function stripDoubleSpaces($s) {
    return str_replace('  ', ' ', $s);
  }
}
