<?php
namespace builder;

class SQLDriver {
  private static $drivers = [
    'mysql' => 'MySQLBuilder',
  ];

  public static function exists($driver) {
    return isset(self::$drivers[$driver]);
  }

  public static function get($driver) {
    return self::$drivers[$driver];
  }
}
