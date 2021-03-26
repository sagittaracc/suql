<?php
namespace core;

class SuQLConfig
{
  private static $instance = null;
  private $config;
  private $configFile = __DIR__ . '/../../config/main.php';

  protected function __construct()
  {
    $this->config = require($this->configFile);
  }

  protected function __clone() {}

  public static function load()
  {
    if (is_null(self::$instance)) {
      self::$instance = new static();
    }

    return self::$instance;
  }

  public function get($path)
  {
    $cfg = $this->config;

    $pathKey = explode('.', $path);
    foreach ($pathKey as $key) {
      if (!isset($cfg[$key]))
        return null;

      $cfg = $cfg[$key];
    }

    return $cfg;
  }
}
