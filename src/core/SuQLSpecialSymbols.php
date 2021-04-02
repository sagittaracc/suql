<?php
namespace core;

class SuQLSpecialSymbols {
  public static $prefix_declare_variable = '@';
  public static $prefix_declare_field_alias = ':';

  public static function nestedQueryPlaceholder($query) {
    return self::$prefix_declare_variable . $query;
  }
}
