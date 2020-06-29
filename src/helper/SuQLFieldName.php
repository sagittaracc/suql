<?php
namespace Helper;

class SuQLFieldName extends SuQLName {
  public $table;

  function __construct($table, $name) {
    $this->table = $table;
    parent::__construct($name);
  }

  public function format($s) {
    return str_replace(['%t', '%n'], [$this->table, $this->name], $s);
  }
}
