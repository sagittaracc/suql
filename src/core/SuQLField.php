<?php
namespace suql\core;

class SuQLField {
  private $oselect = null;

  private $table;
  private $field;
  private $justField;
  private $originalField;
  private $alias;
  private $visible;
  private $modifier = [];

  function __construct($oselect, $table, $field, $alias, $visible) {
    $this->oselect = $oselect;
    $this->table = $table;
    $this->field = $table ? $field->format('%t.%n') : $field->format('%n');
    $this->justField = $field->format('%n');
    // TODO: Проверить originalField - возможно бесполезно
    $this->originalField = $this->field;
    $this->alias = $alias;
    $this->visible = $visible;
  }

  public function getOSelect() {
    return $this->oselect;
  }

  public function getTable() {
    return $this->table;
  }

  public function addModifier($name, $params = []) {
    $this->modifier[$name] = $params;
  }

  public function addCallbackModifier($callback) {
    $this->modifier["callback"] = $callback;
  }

  public function delModifier($name) {
    unset($this->modifier[$name]);
  }

  public function hasModifier() {
    return !empty($this->modifier);
  }

  public function getModifierList() {
    return $this->modifier;
  }

  public function getField() {
    return $this->field;
  }

  public function setField($field) {
    $this->field = $field;
  }

  public function getJustField()
  {
    return $this->justField;
  }

  public function getOriginalField() {
    return $this->originalField;
  }

  public function hasAlias() {
    return !empty($this->alias);
  }

  public function getAlias() {
    return $this->alias;
  }

  public function visible() {
    return $this->visible === true;
  }
}
