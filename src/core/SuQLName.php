<?php
namespace core;

class SuQLName {
  public $name;
  public $alias;

  function __construct() {
    if (func_num_args() === 2) {

      $this->name = func_get_arg(0);
      $this->alias = func_get_arg(1);

    } else if (func_num_args() === 1) {

      $name = func_get_arg(0);

      if (is_array($name)) {
        foreach ($name as $name => $alias) break;
        $this->name = $name;
        $this->alias = $alias;
      } else if (is_string($name)) {
        $parts = explode('@', $name);
        $this->name = isset($parts[0]) ? $parts[0] : null;
        $this->alias = isset($parts[1]) ? $parts[1] : null;
      } else {
        $this->name = $this->alias = null;
      }

    } else
      return null;
  }

  public function format($s) {
    return $this->alias
            ? str_replace(['%n', '%a'], [$this->name, $this->alias], $s)
            : $this->name;
  }
}
