<?php
namespace core;

class SuQLField {
  private $osuql = null;

  private $table;
  private $field;
  private $alias;
  private $visible;
  private $modifier;

  function __construct($osuql, $table, $field, $alias, $visible, $modifier) {
    $this->osuql = $osuql;
    $this->table = $table;
    $this->field = $field;
    $this->alias = $alias;
    $this->visible = $visible;
    $this->modifier = $modifier;
  }

  public function addModifier($name, $arguments) {
    $this->modifier[$name] = $arguments;
  }
}
