<?php
namespace core;

class SuQLConfig
{
  private static $instance = null;
  private $config;
  private $configFile = 'config/main.php';

  protected function __construct()
  {
    $this->config = require($this->configFile);
  }

  protected function __clone() {}

  public static function getInstance()
  {
    if (is_null(self::$instance)) {
      self::$instance = new static();
    }

    return self::$instance;
  }

  public function getModifierConfig()
  {
    return $this->config['modifier'];
  }

  public function getModifierClassList()
  {
    return $this->getModifierConfig()['handler'];
  }
}
