<?php
class SuQLConfig
{
  public static function read() {
    return [
      'config' => [
        'var_declare' => SuQLSpecialSymbols::$prefix_declare_variable,
      ]
    ];
  }
}
