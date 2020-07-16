<?php
namespace core;

class SuQLField {
  private $oselect = null;

  private $table;
  private $field;
  private $alias;
  private $visible;
  private $modifier;

  function __construct($oselect, $table, $field, $alias, $visible, $modifier) {
    $this->oselect = $oselect;
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
