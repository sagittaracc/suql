<?php
namespace builder;

class SQLAdapter {
  private static $adapters = [
    'mysql' => 'TestBuilder',
  ];

  public static function exists($adapter) {
    return isset(self::$adapters[$adapter]);
  }

  public static function get($adapter) {
    return self::$adapters[$adapter];
  }
}
